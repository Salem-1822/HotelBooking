<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Fetch Cities with hotel counts
        $cities = City::withCount('hotels')
            ->having('hotels_count', '>', 0)
            ->orderBy('hotels_count', 'desc')
            ->take(4)
            ->get();

        // 2. Fetch Featured Hotels
        $featuredHotels = Hotel::with(['city'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withMin(['rooms' => function ($query) {
                $query->where('status', 'available');
            }], 'price_per_night')
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(6)
            ->get();
            
        // Assign expected properties for the view
        foreach ($featuredHotels as $hotel) {
            $hotel->avg_rating = $hotel->reviews_avg_rating ? (float)$hotel->reviews_avg_rating : null;
            $hotel->starting_price = $hotel->rooms_min_price_per_night ?? $hotel->price_per_night;
        }

        // 3. Platform Statistics
        $stats = [
            'total_hotels' => Hotel::where('status', 'active')->count(),
            'total_cities' => City::whereHas('hotels', function($q) {
                $q->where('status', 'active');
            })->count(),
            'total_rooms'  => Room::whereHas('hotel', function($q) {
                $q->where('status', 'active');
            })->where('status', '!=', 'inactive')->count(),
            'total_guests' => (int) \App\Models\Reservation::where('status', 'checked_out')->sum('guests_count'),
        ];

        // 4. Guest Reviews
        $guestReviews = Review::with(['user', 'hotel'])
            ->where('rating', '>=', 4)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('client.home', compact('cities', 'featuredHotels', 'stats', 'guestReviews'));
    }
}
