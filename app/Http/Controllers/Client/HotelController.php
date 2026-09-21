<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\City;
use App\Models\Room;

class HotelController extends Controller
{
    // ────────────────────────────────────────────────────────────────────────────
    // Hotels Listing  (Phase 2)
    // ────────────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search'    => 'nullable|string|max:100',
            'city_id'   => 'nullable|integer|exists:cities,id',
            'stars'     => 'nullable|integer|between:1,5',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'sort'      => 'nullable|in:price_asc,price_desc,stars_desc,name_asc',
            'check_in'  => 'nullable|date|after_or_equal:today',
            'check_out' => 'nullable|date|after:check_in',
            'guests'    => 'nullable|integer|min:1|max:20',
        ]);

        $checkIn = $validated['check_in'] ?? null;
        $checkOut = $validated['check_out'] ?? null;

        $query = Hotel::query()
            ->with(['city', 'reviews'])
            ->withCount('reviews')
            ->where('status', 'active')
            ->addSelect([
                'starting_price' => Room::selectRaw('MIN(price_per_night)')
                    ->whereColumn('hotel_id', 'hotels.id')
                    ->where('status', 'available')
                    ->limit(1),
            ])
            ->select('hotels.*');

        if (!empty($validated['search'])) {
            $term = $validated['search'];
            $query->where(function ($q) use ($term) {
                $q->where('hotels.name', 'like', "%{$term}%")
                  ->orWhere('hotels.description', 'like', "%{$term}%")
                  ->orWhere('hotels.address', 'like', "%{$term}%");
            });
        }

        if (!empty($validated['city_id'])) {
            $query->where('hotels.city_id', $validated['city_id']);
        }

        if (!empty($validated['stars'])) {
            $query->where('hotels.stars', $validated['stars']);
        }

        if (!empty($validated['price_min'])) {
            $query->where('hotels.price_per_night', '>=', $validated['price_min']);
        }
        if (!empty($validated['price_max'])) {
            $query->where('hotels.price_per_night', '<=', $validated['price_max']);
        }

        if ($checkIn && $checkOut) {
            $query->whereHas('rooms', function ($q) use ($checkIn, $checkOut, $validated) {
                $q->where('status', 'available')
                  ->when(!empty($validated['guests']), fn ($rq) => $rq->where('capacity', '>=', $validated['guests']))
                  ->whereDoesntHave('reservations', function ($rq) use ($checkIn, $checkOut) {
                      $rq->whereNotIn('status', ['cancelled'])
                         ->where('check_in', '<', $checkOut)
                         ->where('check_out', '>', $checkIn);
                  });
            });
        } elseif (!empty($validated['guests'])) {
            // Even without dates, filter hotels that have at least one room fitting the guest count
            $query->whereHas('rooms', function ($q) use ($validated) {
                $q->where('status', 'available')
                  ->where('capacity', '>=', $validated['guests']);
            });
        }

        switch ($validated['sort'] ?? 'name_asc') {
            case 'price_asc':  $query->orderBy('hotels.price_per_night', 'asc');  break;
            case 'price_desc': $query->orderBy('hotels.price_per_night', 'desc'); break;
            case 'stars_desc': $query->orderBy('hotels.stars', 'desc');            break;
            default:           $query->orderBy('hotels.name', 'asc');             break;
        }

        $hotels = $query->paginate(12)->withQueryString();

        foreach ($hotels as $hotel) {
            $hotel->avg_rating = $hotel->reviews->count() > 0
                ? round($hotel->reviews->avg('rating'), 1)
                : null;
        }

        $cities = City::withCount(['hotels' => fn ($q) => $q->where('status', 'active')])
            ->having('hotels_count', '>', 0)
            ->orderBy('name')
            ->get();

        $priceRange = Hotel::where('status', 'active')
            ->selectRaw('MIN(price_per_night) as min_price, MAX(price_per_night) as max_price')
            ->first();

        return view('client.hotels.index', compact('hotels', 'cities', 'priceRange', 'validated'));
    }

    // ────────────────────────────────────────────────────────────────────────────
    // Hotel Detail  (Phase 3)
    // ────────────────────────────────────────────────────────────────────────────
    public function show(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'check_in'  => 'nullable|date|after_or_equal:today',
            'check_out' => 'nullable|date|after:check_in',
            'guests'    => 'nullable|integer|min:1|max:20',
        ]);

        $checkIn = $validated['check_in'] ?? null;
        $checkOut = $validated['check_out'] ?? null;

        // Only active hotels are visible publicly
        if ($hotel->status !== 'active') {
            abort(404);
        }

        // Eager-load all relations used on the detail page — zero N+1
        $hotel->load(['city', 'reviews.user']);

        // ── Rooms ────────────────────────────────────────────────────────────
        // Order: available first, then by price ascending
        $rooms = $hotel->rooms()
            ->orderByRaw("FIELD(status, 'available', 'reserved', 'occupied', 'maintenance', 'inactive')")
            ->orderBy('price_per_night', 'asc')
            ->get();

        // Annotate each room with a simple is_bookable flag
        $requestedGuests = !empty($validated['guests']) ? (int) $validated['guests'] : null;

        foreach ($rooms as $room) {
            $isAvailable = ($room->status === 'available');

            // Capacity gate: room cannot serve the requested guest count
            if ($isAvailable && $requestedGuests && $room->capacity < $requestedGuests) {
                $isAvailable = false;
            }

            // Date-overlap gate: room has an active reservation in the requested window
            if ($isAvailable && $checkIn && $checkOut) {
                $conflict = \App\Models\Reservation::where('room_id', $room->id)
                    ->whereNotIn('status', ['cancelled'])
                    ->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn)
                    ->exists();

                if ($conflict) {
                    $isAvailable = false;
                }
            }

            $room->is_bookable = $isAvailable;
        }

        // ── Reviews ──────────────────────────────────────────────────────────
        $reviews     = $hotel->reviews()->with('user')->latest()->take(6)->get();
        $reviewCount = $hotel->reviews()->count();
        $avgRating   = $reviewCount > 0
            ? round($hotel->reviews()->avg('rating'), 1)
            : null;

        // Star distribution for the rating breakdown bar
        $ratingBreakdown = [];
        if ($reviewCount > 0) {
            for ($s = 5; $s >= 1; $s--) {
                $cnt = $hotel->reviews()->where('rating', $s)->count();
                $ratingBreakdown[$s] = [
                    'count'   => $cnt,
                    'percent' => round(($cnt / $reviewCount) * 100),
                ];
            }
        }

        // ── Related hotels in same city (max 3) ──────────────────────────────
        $relatedHotels = Hotel::where('city_id', $hotel->city_id)
            ->where('id', '!=', $hotel->id)
            ->where('status', 'active')
            ->with('city')
            ->withCount('reviews')
            ->take(3)
            ->get();

        foreach ($relatedHotels as $h) {
            $h->avg_rating = $h->reviews_count > 0
                ? round($h->reviews()->avg('rating'), 1)
                : null;
            $h->starting_price = $h->rooms()
                ->where('status', 'available')
                ->min('price_per_night');
        }

        return view('client.hotels.show', compact(
            'hotel',
            'rooms',
            'reviews',
            'reviewCount',
            'avgRating',
            'ratingBreakdown',
            'relatedHotels',
            'validated'
        ));
    }
}
