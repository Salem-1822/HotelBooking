<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        if (!$hotelId) {
            return redirect()->route('login')->with('error', 'No hotel assigned.');
        }

        // Base query for customers of this hotel
        // A customer is a User who has at least one reservation in the admin's hotel.
        $customersQuery = User::whereHas('reservations', function ($query) use ($hotelId) {
            $query->where('hotel_id', $hotelId);
        })->withCount(['reservations' => function ($query) use ($hotelId) {
            $query->where('hotel_id', $hotelId);
        }]);

        // Fetch all customers for Phase 1 (no pagination/filters yet)
        $customers = $customersQuery->get();

        // Calculate dashboard cards stats scoped to this hotel
        $totalCustomers = $customers->count();

        // New customers: exactly 1 reservation at this hotel
        $newCustomers = $customers->filter(function ($customer) {
            return $customer->reservations_count === 1;
        })->count();

        // Returning customers: 2 or more reservations at this hotel
        $returningCustomers = $customers->filter(function ($customer) {
            return $customer->reservations_count >= 2;
        })->count();

        // Active customers: has a pending, confirmed, or checked_in reservation at this hotel
        $activeCustomers = User::whereHas('reservations', function ($query) use ($hotelId) {
            $query->where('hotel_id', $hotelId)
                  ->whereIn('status', ['pending', 'confirmed', 'checked_in']);
        })->count();

        // Retrieve last reservation details for each customer to show in the table
        // To avoid N+1, load latest reservation for the admin's hotel
        foreach ($customers as $customer) {
            $lastReservation = Reservation::where('user_id', $customer->id)
                ->where('hotel_id', $hotelId)
                ->latest()
                ->first();
            $customer->last_reservation_date = $lastReservation ? $lastReservation->check_in : null;
        }

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'newCustomers',
            'returningCustomers',
            'activeCustomers'
        ));
    }

    public function show(User $user)
    {
        // Placeholders for Phase 1 & 2
        return redirect()->route('admin.customers.index');
    }
}
