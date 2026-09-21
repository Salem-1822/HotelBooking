<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $client = Auth::guard('web')->user();

        // Load this client's reservations with hotel and room info, most recent first
        $reservations = Reservation::where('user_id', $client->id)
            ->with(['hotel', 'room'])
            ->latest()
            ->take(10)
            ->get();

        return view('client.dashboard', compact('reservations'));
    }
}
