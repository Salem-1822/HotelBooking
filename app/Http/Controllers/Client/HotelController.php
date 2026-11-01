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
            'city_id'   => 'nullable|string|max:100',
            'city'      => 'nullable|string|max:100',
            'ville'     => 'nullable|string|max:100',
            'stars'     => 'nullable|integer|between:1,5',
            'check_in'  => 'nullable|date',
            'check_out' => 'nullable|date',
            'guests'    => 'nullable|integer|min:1|max:20',
        ]);

        // ── Date parsing and normalization ──────────────────────────────────
        $checkIn = null;
        $checkOut = null;
        if (!empty($validated['check_in'])) {
            try {
                $checkIn = \Carbon\Carbon::parse($validated['check_in'])->format('Y-m-d');
            } catch (\Exception $e) {
                $checkIn = null;
            }
        }
        if (!empty($validated['check_out'])) {
            try {
                $checkOut = \Carbon\Carbon::parse($validated['check_out'])->format('Y-m-d');
            } catch (\Exception $e) {
                $checkOut = null;
            }
        }

        if ($checkIn && !$checkOut) {
            $checkOut = \Carbon\Carbon::parse($checkIn)->addDay()->format('Y-m-d');
        } elseif (!$checkIn && $checkOut) {
            $checkIn = max(now()->format('Y-m-d'), \Carbon\Carbon::parse($checkOut)->subDay()->format('Y-m-d'));
        }

        if ($checkIn && $checkOut && $checkOut <= $checkIn) {
            $checkOut = \Carbon\Carbon::parse($checkIn)->addDay()->format('Y-m-d');
        }

        $validated['check_in'] = $checkIn;
        $validated['check_out'] = $checkOut;

        $query = Hotel::query()
            ->select('hotels.*')
            ->addSelect([
                'starting_price' => Room::selectRaw('MIN(price_per_night)')
                    ->whereColumn('hotel_id', 'hotels.id')
                    ->where('status', 'available')
                    ->limit(1),
            ])
            ->with(['city', 'reviews'])
            ->withCount('reviews')
            ->where('status', 'active');

        // ── Text Search (hotel name, description, address, city name & slug) ─
        if (!empty($validated['search'])) {
            $term = trim($validated['search']);
            $lowerTerm = strtolower($term);
            $query->where(function ($q) use ($lowerTerm) {
                $q->whereRaw('LOWER(hotels.name) LIKE ?', ["%{$lowerTerm}%"])
                  ->orWhereRaw('LOWER(hotels.description) LIKE ?', ["%{$lowerTerm}%"])
                  ->orWhereRaw('LOWER(hotels.address) LIKE ?', ["%{$lowerTerm}%"])
                  ->orWhereHas('city', function ($cq) use ($lowerTerm) {
                      $cq->whereRaw('LOWER(name) LIKE ?', ["%{$lowerTerm}%"])
                        ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$lowerTerm}%"]);
                  });
            });
        }

        // ── City Filter (handles ID, slug, exact name, or partial LIKE match) ─
        $rawCity = $validated['city_id'] ?? $validated['city'] ?? $validated['ville'] ?? null;
        if (!empty($rawCity)) {
            $cleanCity = trim($rawCity);
            $cityModel = null;

            if (is_numeric($cleanCity)) {
                $cityModel = City::find((int) $cleanCity);
            }

            if (!$cityModel) {
                $cityModel = City::where('slug', \Illuminate\Support\Str::slug($cleanCity))
                    ->orWhereRaw('LOWER(slug) = ?', [strtolower($cleanCity)])
                    ->first();
            }

            if (!$cityModel) {
                $cityModel = City::whereRaw('LOWER(name) = ?', [strtolower($cleanCity)])->first();
            }

            if (!$cityModel) {
                $cityModel = City::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($cleanCity) . '%'])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ['%' . strtolower($cleanCity) . '%'])
                    ->first();
            }

            if ($cityModel) {
                $query->where('hotels.city_id', $cityModel->id);
                $validated['city_id'] = $cityModel->id;
            } else {
                $query->whereRaw('1 = 0');
                $validated['city_id'] = $cleanCity;
            }
        }

        // ── Star Rating Filter ───────────────────────────────────────────────
        if (!empty($validated['stars'])) {
            $query->where('hotels.stars', (int) $validated['stars']);
        }

        // ── Date Availability / Fully Booked Filter ──────────────────────────
        if ($checkIn && $checkOut) {
            $query->where(function ($hq) use ($checkIn, $checkOut, $validated) {
                $hq->whereHas('rooms', function ($rq) use ($checkIn, $checkOut, $validated) {
                    $rq->where('status', 'available')
                       ->when(!empty($validated['guests']), fn ($cq) => $cq->where('capacity', '>=', $validated['guests']))
                       ->whereDoesntHave('reservations', function ($resQ) use ($checkIn, $checkOut) {
                           $resQ->whereNotIn('status', ['cancelled'])
                                ->where('check_in', '<', $checkOut)
                                ->where('check_out', '>', $checkIn);
                       });
                })
                ->orWhere(function ($fallbackQ) use ($checkIn, $checkOut) {
                    $fallbackQ->doesntHave('rooms')
                        ->whereDoesntHave('reservations', function ($resQ) use ($checkIn, $checkOut) {
                            $resQ->whereNotIn('status', ['cancelled'])
                                 ->where('check_in', '<', $checkOut)
                                 ->where('check_out', '>', $checkIn);
                        });
                });
            });
        } elseif (!empty($validated['guests'])) {
            $query->where(function ($hq) use ($validated) {
                $hq->whereHas('rooms', function ($rq) use ($validated) {
                    $rq->where('status', 'available')
                       ->where('capacity', '>=', $validated['guests']);
                })->orWhereDoesntHave('rooms');
            });
        }

        $query->orderBy('hotels.id', 'desc');

        $hotels = $query->paginate(12)->withQueryString();

        foreach ($hotels as $hotel) {
            $hotel->avg_rating = $hotel->reviews->count() > 0
                ? round($hotel->reviews->avg('rating'), 1)
                : null;
        }

        $cities = \Illuminate\Support\Facades\Cache::remember('active_cities_with_hotel_count', 3600, function () {
            return City::withCount(['hotels' => fn ($q) => $q->where('status', 'active')])
                ->orderBy('name')
                ->get();
        });

        return view('client.hotels.index', compact('hotels', 'cities', 'validated'));
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
        // Order: available first, then by price ascending (ANSI SQL compatible across MySQL and SQLite)
        $rooms = $hotel->rooms()
            ->orderByRaw("CASE status WHEN 'available' THEN 1 WHEN 'reserved' THEN 2 WHEN 'occupied' THEN 3 WHEN 'maintenance' THEN 4 ELSE 5 END")
            ->orderBy('price_per_night', 'asc')
            ->get();

        // Annotate each room with a simple is_bookable flag
        $requestedGuests = !empty($validated['guests']) ? (int) $validated['guests'] : null;

        // Efficient batch check: if dates are requested, find all conflicting room_ids in a single query
        $conflictedRoomIds = [];
        if ($checkIn && $checkOut) {
            $conflictedRoomIds = \App\Models\Reservation::whereIn('room_id', $rooms->pluck('id'))
                ->whereNotIn('status', ['cancelled'])
                ->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn)
                ->pluck('room_id')
                ->flip()
                ->toArray();
        }

        foreach ($rooms as $room) {
            $isAvailable = ($room->status === 'available');

            // Capacity gate: room cannot serve the requested guest count
            if ($isAvailable && $requestedGuests && $room->capacity < $requestedGuests) {
                $isAvailable = false;
            }

            // Date-overlap gate: check batch conflict hash
            if ($isAvailable && isset($conflictedRoomIds[$room->id])) {
                $isAvailable = false;
            }

            $room->is_bookable = $isAvailable;
        }

        // ── Reviews ──────────────────────────────────────────────────────────
        $reviews     = $hotel->reviews()->with('user')->latest()->take(6)->get();
        $reviewCount = $hotel->reviews()->count();
        $avgRating   = $reviewCount > 0
            ? round($hotel->reviews()->avg('rating'), 1)
            : null;

        // Star distribution for the rating breakdown bar (single GROUP BY query instead of 5 separate queries)
        $ratingBreakdown = [];
        if ($reviewCount > 0) {
            $ratingCounts = $hotel->reviews()
                ->selectRaw('rating, COUNT(*) as cnt')
                ->groupBy('rating')
                ->pluck('cnt', 'rating')
                ->toArray();

            for ($s = 5; $s >= 1; $s--) {
                $cnt = $ratingCounts[$s] ?? 0;
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
            ->withAvg('reviews', 'rating')
            ->withMin(['rooms' => function ($query) {
                $query->where('status', 'available');
            }], 'price_per_night')
            ->take(3)
            ->get();

        foreach ($relatedHotels as $h) {
            $h->avg_rating = $h->reviews_avg_rating ? round((float)$h->reviews_avg_rating, 1) : null;
            $h->starting_price = $h->rooms_min_price_per_night ?? $h->price_per_night;
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
