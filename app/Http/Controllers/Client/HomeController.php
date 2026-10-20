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
        // 1. Fetch Cities with hotel counts (Cached for 1 hour, auto-invalidated on hotel/city mutations)
        $cities = \Illuminate\Support\Facades\Cache::remember('home_cities_with_counts', 3600, function () {
            return City::has('hotels')
                ->withCount('hotels')
                ->orderBy('hotels_count', 'desc')
                ->get();
        });

        // 2. Fetch Featured Hotels (fresh random sample of active hotels)
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

        // 3. Platform Statistics (Cached for 30 minutes)
        $stats = \Illuminate\Support\Facades\Cache::remember('home_platform_stats', 1800, function () {
            return [
                'total_hotels' => Hotel::where('status', 'active')->count(),
                'total_cities' => City::whereHas('hotels', function($q) {
                    $q->where('status', 'active');
                })->count(),
                'total_rooms'  => Room::whereHas('hotel', function($q) {
                    $q->where('status', 'active');
                })->where('status', '!=', 'inactive')->count(),
                'total_guests' => (int) \App\Models\Reservation::where('status', 'checked_out')->sum('guests_count'),
            ];
        });

        // 4. Guest Reviews (Cached for 15 minutes)
        $guestReviews = \Illuminate\Support\Facades\Cache::remember('home_guest_reviews', 900, function () {
            return Review::with(['user', 'hotel'])
                ->where('rating', '>=', 4)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        });

        return view('client.home', compact('cities', 'featuredHotels', 'stats', 'guestReviews'));
    }
}
