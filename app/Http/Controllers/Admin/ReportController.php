<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;

class ReportController extends Controller
{
    public function index()
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        if (!$hotelId) {
            return redirect()->route('login')->with('error', 'No hotel assigned.');
        }

        // 1. Total Reservations
        $totalReservations = Reservation::where('hotel_id', $hotelId)->count();

        // 2. Completed/Stayed Reservations (checked_out)
        $completedReservations = Reservation::where('hotel_id', $hotelId)
            ->where('status', 'checked_out')
            ->count();

        // 3. Pending Reservations
        $pendingReservations = Reservation::where('hotel_id', $hotelId)
            ->where('status', 'pending')
            ->count();

        // 4. Cancelled Reservations
        $cancelledReservations = Reservation::where('hotel_id', $hotelId)
            ->where('status', 'cancelled')
            ->count();

        // 5. Total Revenue (Only Confirmed & Completed statuses: confirmed, checked_in, checked_out)
        $totalRevenue = Reservation::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum('total_price');

        // 6. Total Unique Customers (Isolated logic)
        $totalCustomers = $this->getUniqueCustomersCount($hotelId);

        return view('admin.reports.index', compact(
            'totalReservations',
            'completedReservations',
            'pendingReservations',
            'cancelledReservations',
            'totalRevenue',
            'totalCustomers'
        ));
    }

    public function exportPDF()
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        if (!$hotelId) {
            abort(403, 'No hotel assigned.');
        }

        $hotel = \App\Models\Hotel::findOrFail($hotelId);

        // 1. Total Reservations
        $totalReservations = Reservation::where('hotel_id', $hotelId)->count();

        // 2. Completed/Stayed Reservations (checked_out)
        $completedReservations = Reservation::where('hotel_id', $hotelId)
            ->where('status', 'checked_out')
            ->count();

        // 3. Pending Reservations
        $pendingReservations = Reservation::where('hotel_id', $hotelId)
            ->where('status', 'pending')
            ->count();

        // 4. Cancelled Reservations
        $cancelledReservations = Reservation::where('hotel_id', $hotelId)
            ->where('status', 'cancelled')
            ->count();

        // 5. Total Revenue (Confirmed & Completed statuses: confirmed, checked_in, checked_out)
        $totalRevenue = Reservation::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum('total_price');

        // 6. Total Unique Customers
        $totalCustomers = $this->getUniqueCustomersCount($hotelId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf', compact(
            'hotel',
            'totalReservations',
            'completedReservations',
            'pendingReservations',
            'cancelledReservations',
            'totalRevenue',
            'totalCustomers'
        ));

        return $pdf->download('report-' . str_replace(' ', '-', strtolower($hotel->name)) . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Isolated helper to calculate unique customers.
     * Easily replaceable when migrating to standard user-link models.
     */
    protected function getUniqueCustomersCount(int $hotelId): int
    {
        // First check if user_id is populated in the database table
        $hasUserRelation = Reservation::where('hotel_id', $hotelId)
            ->whereNotNull('user_id')
            ->exists();

        if ($hasUserRelation) {
            return Reservation::where('hotel_id', $hotelId)
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');
        }

        // Fallback workaround logic using guest text fields
        return Reservation::where('hotel_id', $hotelId)
            ->selectRaw('COUNT(DISTINCT CONCAT(COALESCE(guest_name, ""), "|", COALESCE(guest_phone, ""))) as aggregate')
            ->value('aggregate') ?? 0;
    }
}
