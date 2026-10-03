<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    /**
     * Store a new review for a reservation.
     */
    public function store(Request $request, Reservation $reservation)
    {
        // 1. Authentication
        $userId = Auth::guard('web')->id();
        if (!$userId) {
            abort(401);
        }

        // 2. Reservation ownership
        Gate::authorize('view', $reservation);

        // 3. Completed stay check
        if ($reservation->status !== 'checked_out') {
            return back()->with('error', 'Reviews can only be submitted after your stay is completed.');
        }

        // 4. Existing review check
        if ($reservation->review()->exists()) {
            return back()->with('error', 'You have already reviewed this reservation.');
        }

        // 6. Validation
        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        // 5. Hotel relation safely retrieved from reservation
        $hotelId = $reservation->hotel_id;

        // Prevent XSS by stripping tags before storing
        $cleanComment = strip_tags($validated['comment']);

        // Create the review
        $reservation->review()->create([
            'hotel_id' => $hotelId,
            'user_id'  => $userId,
            'rating'   => $validated['rating'],
            'comment'  => $cleanComment,
        ]);

        return back()->with('success', 'Thank you! Your review has been published.');
    }
}
