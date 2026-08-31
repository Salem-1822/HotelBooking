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
        $featuredHotels = Hotel::with(['city', 'reviews'])
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(6)
            ->get();
            
        // Calculate average rating for hotels manually
        foreach ($featuredHotels as $hotel) {
            $hotel->avg_rating = $hotel->reviews->count() > 0 ? $hotel->reviews->avg('rating') : null;
            $hotel->reviews_count = $hotel->reviews->count();
            // Get a starting price (min price of its rooms)
            $minPriceRoom = $hotel->rooms()->where('status', 'available')->orderBy('price_per_night', 'asc')->first();
            $hotel->starting_price = $minPriceRoom ? $minPriceRoom->price_per_night : $hotel->price_per_night;
        }

        // 3. Platform Statistics
        $stats = [
            'total_hotels' => Hotel::where('status', 'active')->count(),
            'total_cities' => City::has('hotels')->count(),
            'total_rooms'  => Room::count(),
            'total_reviews' => Review::count(),
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
