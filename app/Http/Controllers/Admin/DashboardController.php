<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Debugging as requested
        // dd(Auth::guard('admin')->user());
        
        $stats = [
            'hotels' => Hotel::count(),
            'cities' => City::count(),
            'reservations' => Reservation::count(),
            'admins' => Admin::count(),
            'revenue' => Reservation::where('status', 'completed')->sum('total_price'),
        ];
        
        $recent_hotels = Hotel::with('city')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recent_hotels'));
    }


    public function settings()
    {
        return view('admin.settings');
    }
}
