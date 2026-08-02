<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HotelProfileController extends Controller
{
    /**
     * Amenity options available for selection.
     * Keys are stored in the database; values are display labels.
     */
    private const AMENITIES = [
        'wifi'             => ['label' => 'Free WiFi',         'icon' => 'bi-wifi'],
        'parking'          => ['label' => 'Free Parking',      'icon' => 'bi-p-square-fill'],
        'swimming_pool'    => ['label' => 'Swimming Pool',     'icon' => 'bi-water'],
        'gym'              => ['label' => 'Fitness Center',    'icon' => 'bi-bicycle'],
        'restaurant'       => ['label' => 'Restaurant',        'icon' => 'bi-cup-hot-fill'],
        'spa'              => ['label' => 'Spa & Wellness',    'icon' => 'bi-flower1'],
        'air_conditioning' => ['label' => 'Air Conditioning',  'icon' => 'bi-thermometer-snow'],
        'breakfast'        => ['label' => 'Breakfast',         'icon' => 'bi-egg-fried'],
        'airport_shuttle'  => ['label' => 'Airport Shuttle',   'icon' => 'bi-bus-front-fill'],
        'bar'              => ['label' => 'Bar & Lounge',      'icon' => 'bi-cup-straw'],
        'room_service'     => ['label' => 'Room Service',      'icon' => 'bi-bell-fill'],
        'laundry'          => ['label' => 'Laundry Service',   'icon' => 'bi-bag-fill'],
        'conference_room'  => ['label' => 'Conference Room',   'icon' => 'bi-briefcase-fill'],
        'concierge'        => ['label' => 'Concierge',         'icon' => 'bi-person-badge-fill'],
        'pet_friendly'     => ['label' => 'Pet Friendly',      'icon' => 'bi-heart-fill'],
        'ev_charging'      => ['label' => 'EV Charging',       'icon' => 'bi-lightning-charge-fill'],
    ];

    private const POLICIES = [
        'children' => [
            'Allowed',
            'Not Allowed',
            'Allowed (under 12 free)',
            'Allowed (extra charge)',
        ],
        'pets' => [
            'Allowed',
            'Not Allowed',
            'Allowed (extra charge)',
            'Small pets only',
        ],
        'smoking' => [
            'Non-Smoking Property',
            'Smoking Allowed',
            'Designated Areas Only',
            'Smoking Rooms Available',
        ],
    ];

    // ─────────────────────────────────────────────────────────────── show ──

    public function show()
    {
        $admin   = Auth::guard('admin')->user();
        $hotelId = $admin->hotel_id;

        if (!$hotelId) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No hotel has been assigned to your account. Please contact your Super Admin.');
        }

        $hotel = Hotel::with('city')->findOrFail($hotelId);

        // ── Room statistics ───────────────────────────────────────────────
        $rooms            = Room::where('hotel_id', $hotelId)->get();
        $totalRooms       = $rooms->count();
        $availableRooms   = $rooms->where('status', 'available')->count();
        $occupiedRooms    = $rooms->where('status', 'occupied')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();
        $occupancyRate    = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // ── Reservation statistics ────────────────────────────────────────
        $reservationsQuery = Reservation::where('hotel_id', $hotelId);
        $totalReservations = (clone $reservationsQuery)->count();

        $totalCustomers = (clone $reservationsQuery)
            ->selectRaw('COUNT(DISTINCT CONCAT(COALESCE(guest_name,""),"|",COALESCE(guest_phone,""))) as aggregate')
            ->value('aggregate');

        // ── Review statistics ─────────────────────────────────────────────
        $totalReviews = Review::where('hotel_id', $hotelId)->count();

        $avgRating = Review::where('hotel_id', $hotelId)->avg('rating');
        $avgRating = $avgRating ? round($avgRating, 1) : null;

        // ── Monthly revenue (confirmed / checked_in / checked_out) ────────
        $monthlyRevenue = (clone $reservationsQuery)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum('total_price');

        // ── All-time revenue ──────────────────────────────────────────────
        $totalRevenue = (clone $reservationsQuery)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum('total_price');

        return view('admin.hotel_profile.index', [
            'hotel'             => $hotel,
            'amenitiesOptions'  => self::AMENITIES,
            'policies'          => self::POLICIES,
            // Stats
            'totalRooms'        => $totalRooms,
            'availableRooms'    => $availableRooms,
            'occupiedRooms'     => $occupiedRooms,
            'maintenanceRooms'  => $maintenanceRooms,
            'occupancyRate'     => $occupancyRate,
            'totalReservations' => $totalReservations,
            'totalCustomers'    => $totalCustomers,
            'totalReviews'      => $totalReviews,
            'avgRating'         => $avgRating,
            'monthlyRevenue'    => $monthlyRevenue,
            'totalRevenue'      => $totalRevenue,
        ]);
    }

    // ────────────────────────────────────────────────────────────── update ──

    public function update(Request $request)
    {
        $admin   = Auth::guard('admin')->user();
        $hotelId = $admin->hotel_id;

        if (!$hotelId) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No hotel has been assigned to your account.');
        }

        $hotel = Hotel::findOrFail($hotelId);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string|max:3000',
            'stars'               => 'nullable|integer|min:1|max:5',
            'address'             => 'required|string|max:500',
            'phone'               => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:255',
            'check_in_time'       => 'nullable|date_format:H:i',
            'check_out_time'      => 'nullable|date_format:H:i',
            'cancellation_policy' => 'nullable|string|max:1000',
            'children_policy'     => 'nullable|string|max:100',
            'pets_policy'         => 'nullable|string|max:100',
            'smoking_policy'      => 'nullable|string|max:100',
            'amenities'           => 'nullable|array',
            'amenities.*'         => 'string|max:50',
            // Images
            'image'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gallery_images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_gallery'      => 'nullable|array',
            'remove_gallery.*'    => 'integer',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
        ], [
            'name.required'          => 'Hotel name is required.',
            'address.required'       => 'Address is required.',
            'email.email'            => 'Please enter a valid email address.',
            'stars.min'              => 'Star rating must be between 1 and 5.',
            'stars.max'              => 'Star rating must be between 1 and 5.',
            'image.image'            => 'Cover image must be a valid image file.',
            'image.max'              => 'Cover image must not exceed 5 MB.',
            'image.mimes'            => 'Cover image must be JPG, PNG or WebP.',
            'check_in_time.date_format'  => 'Check-in time must be a valid time (e.g. 14:00).',
            'check_out_time.date_format' => 'Check-out time must be a valid time (e.g. 11:00).',
        ]);

        // ── Cover Image (existing 'image' column) ─────────────────────────
        if ($request->hasFile('image')) {
            if ($hotel->image) {
                Storage::disk('public')->delete($hotel->image);
            }
            $validated['image'] = $request->file('image')->store('hotels/covers', 'public');
        } else {
            // Do not overwrite image with null if no new file was uploaded
            unset($validated['image']);
        }

        // ── Gallery: remove marked images first ───────────────────────────
        $gallery = $hotel->gallery_images ?? [];

        if ($request->filled('remove_gallery')) {
            foreach ($request->input('remove_gallery') as $idx) {
                if (isset($gallery[$idx])) {
                    Storage::disk('public')->delete($gallery[$idx]);
                    unset($gallery[$idx]);
                }
            }
            $gallery = array_values($gallery);
        }

        // ── Gallery: add new uploads ──────────────────────────────────────
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $gallery[] = $file->store('hotels/gallery', 'public');
            }
        }

        $validated['gallery_images'] = !empty($gallery) ? $gallery : null;

        // ── Amenities ─────────────────────────────────────────────────────
        $validated['amenities'] = $request->input('amenities') ?: null;

        $hotel->update($validated);

        return redirect()->route('admin.hotel-profile')
            ->with('success', 'Hotel profile has been updated successfully.');
    }
}
