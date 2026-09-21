<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Show the reservation form for a specific room.
     * GET /client/reserve/{hotel}/{room}
     */
    public function create(Request $request, Hotel $hotel, Room $room)
    {
        // Only active hotels
        if ($hotel->status !== 'active') {
            abort(404);
        }

        // Room must belong to this hotel
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        // Room must be available (status-based; date availability checked on store)
        if ($room->status !== 'available') {
            return redirect()->route('hotels.show', $hotel)
                ->with('error', 'This room is no longer available.');
        }

        // Accept pre-filled dates and guests from the search funnel
        $prefill = $request->only(['check_in', 'check_out', 'guests']);

        return view('client.reservations.create', compact('hotel', 'room', 'prefill'));
    }

    /**
     * Store a new client reservation.
     * POST /client/reserve/{hotel}/{room}
     */
    public function store(Request $request, Hotel $hotel, Room $room)
    {
        // Only active hotels
        if ($hotel->status !== 'active') {
            abort(404);
        }

        // Room must belong to this hotel (server-side, non-spoofable)
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        // Room must be in 'available' status
        if ($room->status !== 'available') {
            return back()->withErrors(['room' => 'This room is no longer available.']);
        }

        // ── Validate input ────────────────────────────────────────────────────
        $data = $request->validate([
            'check_in'     => ['required', 'date', 'after_or_equal:today'],
            'check_out'    => ['required', 'date', 'after:check_in'],
            'guests_count' => ['required', 'integer', 'min:1', 'max:' . $room->capacity],
            'phone'        => ['nullable', 'string', 'max:50'],
        ], [
            'check_in.required'          => 'Please select a check-in date.',
            'check_in.after_or_equal'    => 'Check-in date cannot be in the past.',
            'check_out.required'         => 'Please select a check-out date.',
            'check_out.after'            => 'Check-out must be after check-in.',
            'guests_count.required'      => 'Please enter the number of guests.',
            'guests_count.min'           => 'At least 1 guest is required.',
            'guests_count.max'           => "This room can accommodate a maximum of {$room->capacity} guest(s).",
        ]);

        // ── Calculate total price ─────────────────────────────────────────────
        $checkIn  = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $nights   = max(1, $checkIn->diffInDays($checkOut));
        $totalPrice = $room->price_per_night * $nights;

        $client = Auth::guard('web')->user();

        try {
            // ── Atomic booking transaction ────────────────────────────────────
            $reservation = DB::transaction(function () use ($data, $room, $hotel, $totalPrice, $client) {
                // Pessimistic lock on the room to prevent concurrent overlap checks
                $lockedRoom = Room::where('id', $room->id)->lockForUpdate()->first();

                if (!$lockedRoom || $lockedRoom->status !== 'available') {
                    return 'room_unavailable';
                }

                // ── Date-overlap availability check ───────────────────────────
                // Convention: check-in day is inclusive, check-out day is exclusive.
                // Overlap exists when: existing.check_in < new.check_out AND existing.check_out > new.check_in
                $conflict = Reservation::where('room_id', $room->id)
                    ->whereNotIn('status', ['cancelled'])
                    ->where('check_in', '<', $data['check_out'])
                    ->where('check_out', '>', $data['check_in'])
                    ->exists();

                if ($conflict) {
                    return 'conflict';
                }

                // ── Create reservation ────────────────────────────────────────
                // Phone: use form input first, then fall back to account phone
                $phone = !empty($data['phone']) ? $data['phone'] : ($client->phone ?? null);

                // Resolve or create Customer record for Admin Customers page
                $customerId = null;
                if (!empty($phone)) {
                    $normalizedPhone = \App\Models\Customer::normalizePhone($phone);
                    if (!empty($normalizedPhone)) {
                        $customer = \App\Models\Customer::firstOrCreate(
                            ['hotel_id' => $hotel->id, 'phone' => $normalizedPhone],
                            ['name' => $client->name, 'email' => $client->email]
                        );
                        $customerId = $customer->id;
                    }
                }

                return Reservation::create([
                    'hotel_id'     => $hotel->id,
                    'room_id'      => $room->id,
                    'user_id'      => $client->id,
                    'customer_id'  => $customerId,
                    'guest_name'   => $client->name,
                    'guest_phone'  => $phone,
                    'guests_count' => $data['guests_count'],
                    'check_in'     => $data['check_in'],
                    'check_out'    => $data['check_out'],
                    'total_price'  => $totalPrice,
                    'status'       => 'pending',
                ]);
            });

            // ── Handle failure cases safely ───────────────────────────────────
            if ($reservation === 'room_unavailable') {
                return back()->withErrors(['room' => 'This room is no longer available.']);
            }
            
            if ($reservation === 'conflict') {
                return back()
                    ->withInput()
                    ->withErrors(['check_in' => 'This room is already reserved for the selected dates. Please choose different dates.']);
            }

            return redirect()->route('client.reservations.confirmation', $reservation)
                ->with('success', 'Your reservation has been submitted successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'We encountered an issue processing your booking due to high traffic. Please try again.');
        }
    }

    /**
     * Show the reservation confirmation page.
     * GET /client/reservations/{reservation}/confirmation
     */
    public function confirmation(Reservation $reservation)
    {
        // Only the owner may view their confirmation
        if ($reservation->user_id !== Auth::guard('web')->user()->id) {
            abort(403);
        }

        $reservation->load(['hotel.city', 'room']);

        return view('client.reservations.confirmation', compact('reservation'));
    }
    /**
     * Display a listing of the client's reservations.
     */
    public function index()
    {
        $client = Auth::guard('web')->user();
        
        $reservations = Reservation::where('user_id', $client->id)
            ->with(['hotel', 'room'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('client.reservations.index', compact('reservations'));
    }

    /**
     * Display the specified reservation details.
     */
    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::guard('web')->user()->id) {
            abort(403);
        }

        $reservation->load(['hotel', 'room']);

        return view('client.reservations.show', compact('reservation'));
    }

    /**
     * Cancel the reservation if eligible.
     */
    public function cancel(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::guard('web')->user()->id) {
            abort(403);
        }

        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This reservation cannot be cancelled because it is already ' . str_replace('_', ' ', $reservation->status) . '.');
        }

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservation has been cancelled successfully.');
    }
}
