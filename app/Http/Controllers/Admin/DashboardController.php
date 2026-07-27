<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\User;
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

        // Rooms Stats
        $totalRooms = $hotel->rooms_count;
        $rooms = Room::where('hotel_id', $hotelId)->get();
        $availableRooms = $rooms->where('status', 'available')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();

        // Calculate occupancy rate
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // Reservations Stats
        $reservationsQuery = Reservation::where('hotel_id', $hotelId);

        $pendingReservations    = (clone $reservationsQuery)->where('status', 'pending')->count();
        $confirmedReservations  = (clone $reservationsQuery)->where('status', 'confirmed')->count();
        $cancelledReservations  = (clone $reservationsQuery)->where('status', 'cancelled')->count();
        $checkedInReservations  = (clone $reservationsQuery)->where('status', 'checked_in')->count();
        $checkedOutReservations = (clone $reservationsQuery)->where('status', 'checked_out')->count();

        // Check-ins/Check-outs: count by reservation STATUS (operational state), not by date column
        $todayCheckIns  = $checkedInReservations;
        $todayCheckOuts = $checkedOutReservations;

        // Customers
        // Temporary: reservations table currently does not contain a user_id column.
        // To keep the dashboard stable until reservations are linked to authenticated users,
        // we compute customer count based on unique guests (guest_name + guest_phone).
        $totalCustomers = (clone $reservationsQuery)
            ->selectRaw('COUNT(DISTINCT CONCAT(COALESCE(guest_name, ""), "|", COALESCE(guest_phone, ""))) as aggregate')
            ->value('aggregate');


        // Recent Reservations (Latest 5)
        $latestReservations = (clone $reservationsQuery)
            ->with(['room'])
            ->latest()
            ->take(5)
            ->get();


        // Recent Customers
        // Temporary: reservations table is not currently linked to users via a user_id column.
        // Avoid querying users with whereHas('reservations') until the schema/relationships are updated.
        $latestCustomers = collect();


        // Chart Data: Reservations Per Month (Last 6 months)
        $monthlyReservations = [];
        $monthlyRevenue = [];
        $chartLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonths($i);
            $chartLabels[] = $month->format('M');

            $monthRes = (clone $reservationsQuery)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->get();

            $monthlyReservations[] = $monthRes->count();
            // Revenue from confirmed, checked_in, and checked_out reservations
            $monthlyRevenue[] = $monthRes->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])->sum('total_price');
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
