<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        if (!$hotelId) {
            return redirect()->route('login')->with('error', 'No hotel assigned.');
        }

        // Hotel with Rooms count
        $hotel = Hotel::withCount('rooms')->findOrFail($hotelId);

        // Rooms Stats (Single aggregate query)
        $totalRooms = $hotel->rooms_count;
        $roomCounts = Room::where('hotel_id', $hotelId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $availableRooms   = $roomCounts['available'] ?? 0;
        $occupiedRooms    = $roomCounts['occupied'] ?? 0;
        $maintenanceRooms = $roomCounts['maintenance'] ?? 0;

        // Calculate occupancy rate
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // Reservations Stats (Single aggregate query for all statuses)
        $reservationCounts = Reservation::where('hotel_id', $hotelId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $pendingReservations    = $reservationCounts['pending'] ?? 0;
        $confirmedReservations  = $reservationCounts['confirmed'] ?? 0;
        $cancelledReservations  = $reservationCounts['cancelled'] ?? 0;
        $checkedInReservations  = $reservationCounts['checked_in'] ?? 0;
        $checkedOutReservations = $reservationCounts['checked_out'] ?? 0;

        // Check-ins/Check-outs: count by reservation STATUS (operational state), not by date column
        $todayCheckIns  = $checkedInReservations;
        $todayCheckOuts = $checkedOutReservations;

        // Customers
        $totalCustomers = Customer::where('hotel_id', $hotelId)->count();

        $reservationsQuery = Reservation::where('hotel_id', $hotelId);

        // Recent Reservations (Latest 5)
        $latestReservations = (clone $reservationsQuery)
            ->with(['room'])
            ->latest()
            ->take(5)
            ->get();


        // Top Customers
        $latestCustomers = Customer::where('hotel_id', $hotelId)
            ->withCount('reservations')
            ->orderByDesc('reservations_count')
            ->take(5)
            ->get();


        // Chart Data: Reservations Per Month (Last 6 months) — single grouped query instead of 6 separate queries loading full models
        $sixMonthsAgo = Carbon::today()->startOfMonth()->subMonths(5);
        $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
        $yearMonthSql = $isSqlite ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyAggregates = Reservation::where('hotel_id', $hotelId)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("{$yearMonthSql} as ym, COUNT(*) as total_count, SUM(CASE WHEN status IN ('confirmed', 'checked_in', 'checked_out') THEN total_price ELSE 0 END) as revenue")
            ->groupByRaw($yearMonthSql)
            ->get()
            ->keyBy('ym');

        $monthlyReservations = [];
        $monthlyRevenue = [];
        $chartLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonths($i);
            $chartLabels[] = $month->format('M');
            $ymKey = $month->format('Y-m');

            $row = $monthlyAggregates->get($ymKey);
            $monthlyReservations[] = $row ? (int)$row->total_count : 0;
            $monthlyRevenue[]      = $row ? (float)$row->revenue : 0;
        }

        // Activity Timeline (Just latest reservations for now, since we don't have an activity log table)
        $activities = (clone $reservationsQuery)->latest()->take(6)->get();


        return view('admin.dashboard', compact(
            'hotel',
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'maintenanceRooms',
            'occupancyRate',
            'pendingReservations',
            'confirmedReservations',
            'cancelledReservations',
            'checkedInReservations',
            'checkedOutReservations',
            'todayCheckIns',
            'todayCheckOuts',
            'totalCustomers',
            'latestReservations',
            'latestCustomers',
            'chartLabels',
            'monthlyReservations',
            'monthlyRevenue',
            'activities'
        ));
    }
}
