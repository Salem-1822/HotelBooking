<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'hotels' => Hotel::count(),
            'cities' => City::count(),
            'reservations' => Reservation::count(),
            'admins' => User::count(),
            'revenue' => Reservation::where('status', 'completed')->sum('total_price'),
        ];
        
        $recent_hotels = Hotel::with('city')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recent_hotels'));
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function exports()
    {
        return view('admin.exports');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
