<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // All statuses — used in edit form and status filter
    protected array $statusOptions = [
        'pending'     => 'Pending',
        'confirmed'   => 'Confirmed',
        'checked_in'  => 'Checked In',
        'checked_out' => 'Checked Out',
        'cancelled'   => 'Cancelled',
    ];

    // Statuses allowed only at creation time
    protected array $createStatusOptions = [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
    ];

    public function index(Request $request)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        // Base query for this admin's hotel
        $query = Reservation::where('hotel_id', $hotelId)->with('room');

        // Stats calculation (before filters are applied)
        $statsQuery = Reservation::where('hotel_id', $hotelId);
        $totalReservations = $statsQuery->count();
        $pendingCount = $statsQuery->clone()->where('status', 'pending')->count();
        $confirmedCount = $statsQuery->clone()->where('status', 'confirmed')->count();
        $cancelledCount = $statsQuery->clone()->where('status', 'cancelled')->count();

        // Search by guest name or phone or room number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhereHas('room', function($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Check-in Date
        if ($request->filled('date')) {
            $query->whereDate('check_in', $request->date);
        }

        $reservations = $query->latest()->paginate(10)->withQueryString();
        
        $rooms = Room::where('hotel_id', $hotelId)->get();

        return view('admin.reservations.index', [
            'reservations'       => $reservations,
            'totalReservations'  => $totalReservations,
            'pendingCount'       => $pendingCount,
            'confirmedCount'     => $confirmedCount,
            'cancelledCount'     => $cancelledCount,
            'statusOptions'      => $this->statusOptions,
            'createStatusOptions' => $this->createStatusOptions,
            'rooms'              => $rooms,
        ]);
    }

    public function store(Request $request)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        
        $rules = [
            'room_id'      => ['required', 'exists:rooms,id,hotel_id,' . $hotelId],
            'guest_name'   => 'required|string|max:255',
            'guest_phone'  => 'required|string|max:50',
            'guests_count' => 'required|integer|min:1',
            'check_in'     => 'required|date',
            'check_out'    => 'required|date|after:check_in',
            // Only pending/confirmed are valid at creation time
            'status'       => ['required', Rule::in(array_keys($this->createStatusOptions))],
        ];

        $data = $request->validate($rules);
        $data['hotel_id'] = $hotelId;

        // Calculate total price based on room price and number of nights
        $room = Room::findOrFail($data['room_id']);
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $nights = $checkIn->diffInDays($checkOut);
        $nights = $nights > 0 ? $nights : 1;
        $data['total_price'] = $room->price_per_night * $nights;

        Reservation::create($data);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation created successfully.');
    }

    public function update(Request $request, Reservation $reservation)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        if ($reservation->hotel_id !== $hotelId) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'room_id' => ['required', 'exists:rooms,id,hotel_id,' . $hotelId],
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:50',
            'guests_count' => 'required|integer|min:1',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'status' => ['required', Rule::in(array_keys($this->statusOptions))],
        ];

        $data = $request->validate($rules);

        // Calculate total price based on room price and number of nights
        $room = Room::findOrFail($data['room_id']);
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $nights = $checkIn->diffInDays($checkOut);
        $nights = $nights > 0 ? $nights : 1;
        $data['total_price'] = $room->price_per_night * $nights;

        $reservation->update($data);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        if ($reservation->hotel_id !== $hotelId) {
            abort(403, 'Unauthorized action.');
        }

        $reservation->delete();

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
