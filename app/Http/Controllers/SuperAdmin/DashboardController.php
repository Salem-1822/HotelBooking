<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI Statistics
        $stats = [
            'hotels'       => Hotel::count(),
            'cities'       => City::count(),
            'reservations' => Reservation::count(),
            'admins'       => Admin::count(),
            'customers'    => Customer::count(),
            // Fix: 'completed' was removed from the status ENUM in migration 2026_07_27.
            // Current valid statuses: pending | confirmed | cancelled | checked_in | checked_out
            'revenue'      => Reservation::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                                          ->sum('total_price'),
        ];

        // Chart data: reservations grouped by month for the last 6 months
        $chartData = Reservation::selectRaw(
                "DATE_FORMAT(created_at, '%b %Y') as label,
                 DATE_FORMAT(created_at, '%Y%m') as sort_key,
                 COUNT(*) as total"
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupByRaw("DATE_FORMAT(created_at, '%b %Y'), DATE_FORMAT(created_at, '%Y%m')")
            ->orderByRaw("MIN(created_at)")
            ->get();

        // Reservation breakdown by status (for chart legend)
        $reservationsByStatus = Reservation::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Recently added hotels list
        $recent_hotels = Hotel::with('city')->latest()->take(5)->get();

        return view('super_admin.dashboard', compact(
            'stats',
            'recent_hotels',
            'chartData',
            'reservationsByStatus'
        ));
    }

    public function settings()
    {
        return view('super_admin.settings');
    }
}
