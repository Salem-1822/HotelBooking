<?php

use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Review;

$room = Room::first();
if (!$room) { echo "No rooms\n"; exit; }

$hotel = Hotel::find($room->hotel_id);
$user = User::first();

// 1. Create checked_out reservation
$reservation = Reservation::create([
    'hotel_id' => $hotel->id,
    'room_id' => $room->id,
    'user_id' => $user->id,
    'guest_name' => 'Test Guest',
    'guest_phone' => '123456789',
    'guests_count' => 1,
    'check_in' => '2026-01-01',
    'check_out' => '2026-01-05',
    'total_price' => 500,
    'status' => 'checked_out',
]);

echo "Reservation created (ID: {$reservation->id})\n";

// 2. Create Review (simulating controller store)
$review = $reservation->review()->create([
    'hotel_id' => $reservation->hotel_id,
    'user_id' => $reservation->user_id,
    'rating' => 4,
    'comment' => 'This is an awesome test review!',
]);

echo "Review created (ID: {$review->id})\n";

// 3. Verify on Hotel
$hotelData = Hotel::with('reviews.user')->withCount('reviews')->find($hotel->id);

echo "Hotel Reviews Count: {$hotelData->reviews_count}\n";
$avg = $hotelData->reviews()->avg('rating');
echo "Hotel Avg Rating: {$avg}\n";

$latestReview = $hotelData->reviews()->latest()->first();
if ($latestReview) {
    echo "Latest Review: {$latestReview->comment} - Rating: {$latestReview->rating}\n";
}
