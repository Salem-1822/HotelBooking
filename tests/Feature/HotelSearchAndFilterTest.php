<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HotelSearchAndFilterTest extends TestCase
{
    use RefreshDatabase;

    protected City $cityMarrakech;
    protected City $cityCasablanca;
    protected Hotel $hotelMarrakech1;
    protected Hotel $hotelMarrakech2;
    protected Hotel $hotelCasablanca;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->cityMarrakech = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->cityCasablanca = City::create([
            'name' => 'Casablanca',
            'slug' => 'casablanca',
        ]);

        $this->hotelMarrakech1 = Hotel::create([
            'city_id' => $this->cityMarrakech->id,
            'name' => 'Atlas Luxury Resort',
            'description' => 'Un oasis de calme',
            'address' => 'Avenue Mohammed VI',
            'price_per_night' => 1200,
            'stars' => 5,
            'status' => 'active',
        ]);

        $this->hotelMarrakech2 = Hotel::create([
            'city_id' => $this->cityMarrakech->id,
            'name' => 'Riad Medina Gold',
            'description' => 'Authentic riad experience',
            'address' => 'Derb Dabachi',
            'price_per_night' => 600,
            'stars' => 3,
            'status' => 'active',
        ]);

        $this->hotelCasablanca = Hotel::create([
            'city_id' => $this->cityCasablanca->id,
            'name' => 'Corniche Ocean Palace',
            'description' => 'Vue sur mer magnifique',
            'address' => 'Boulevard de la Corniche',
            'price_per_night' => 900,
            'stars' => 4,
            'status' => 'active',
        ]);
    }

    /**
     * 1. Simplify Filters:
     * Keep ONLY City (Ville) and Star Rating (Nombre d'étoiles).
     * Remove price range slider, amenities, property types.
     */
    public function test_only_city_and_stars_sidebar_filters_are_rendered_without_price_filter(): void
    {
        $response = $this->get(route('hotels.index'));
        $response->assertStatus(200);

        // Required facet filters are present
        $response->assertSee('Filter by City (Ville)', false);
        $response->assertSee("Filter by Star Rating (Nombre d'étoiles)", false);

        // Removed filters must NOT be present
        $response->assertDontSee('price_min');
        $response->assertDontSee('price_max');
        $response->assertDontSee('Price per Night');
        $response->assertDontSee('Apply Price');
    }

    /**
     * 1b. Sort dropdown:
     * COMPLETELY REMOVE the "Sort by" dropdown menu from the UI and backend logic.
     */
    public function test_sort_dropdown_is_completely_removed(): void
    {
        $response = $this->get(route('hotels.index'));
        $response->assertStatus(200);

        // Entire sort select and label completely removed
        $response->assertDontSee('Sort by:');
        $response->assertDontSee('id="sortSelect"', false);
        $response->assertDontSee('name="sort"', false);
        $response->assertDontSee('applySortChange', false);
        $response->assertDontSee('Price: Low to High');
        $response->assertDontSee('Price: High to Low');
        $response->assertDontSee('Stars: Highest First');
        $response->assertDontSee('Name A–Z');
    }

    /**
     * 2. Fix "Ville Not Found" Bug:
     * Resolve issue where filtering by an existing city returns empty results.
     * Check slug matching, case sensitivity, exact match vs LIKE, and foreign key relations.
     */
    public function test_city_filtering_handles_id_slug_case_insensitivity_and_like(): void
    {
        // 2a. By city ID
        $resId = $this->get(route('hotels.index', ['city_id' => $this->cityCasablanca->id]));
        $resId->assertStatus(200);
        $hotelsId = $resId->viewData('hotels');
        $this->assertCount(1, $hotelsId);
        $this->assertEquals($this->hotelCasablanca->id, $hotelsId->first()->id);

        // 2b. By city slug passed in city_id or city parameter
        $resSlug = $this->get(route('hotels.index', ['city' => 'casablanca']));
        $resSlug->assertStatus(200);
        $hotelsSlug = $resSlug->viewData('hotels');
        $this->assertCount(1, $hotelsSlug);
        $this->assertEquals($this->hotelCasablanca->id, $hotelsSlug->first()->id);

        // 2c. By city slug passed in city_id
        $resCityIdSlug = $this->get(route('hotels.index', ['city_id' => 'marrakech']));
        $resCityIdSlug->assertStatus(200);
        $hotelsCityIdSlug = $resCityIdSlug->viewData('hotels');
        $this->assertCount(2, $hotelsCityIdSlug);

        // 2d. Case-insensitivity: UPPERCASE
        $resUpper = $this->get(route('hotels.index', ['city' => 'MARRAKECH']));
        $resUpper->assertStatus(200);
        $hotelsUpper = $resUpper->viewData('hotels');
        $this->assertCount(2, $hotelsUpper);

        // 2e. Text search input for city name
        $resSearch = $this->get(route('hotels.index', ['search' => 'Casablanca']));
        $resSearch->assertStatus(200);
        $hotelsSearch = $resSearch->viewData('hotels');
        $this->assertCount(1, $hotelsSearch);
        $this->assertEquals($this->hotelCasablanca->id, $hotelsSearch->first()->id);

        // 2f. Unknown city cleanly returns 0 results
        $resUnknown = $this->get(route('hotels.index', ['city' => 'NonExistentCity999']));
        $resUnknown->assertStatus(200);
        $hotelsUnknown = $resUnknown->viewData('hotels');
        $this->assertCount(0, $hotelsUnknown);
    }

    /**
     * 2b. Star rating filtering.
     */
    public function test_star_rating_filter_works_correctly(): void
    {
        $response = $this->get(route('hotels.index', ['stars' => 5]));
        $response->assertStatus(200);
        $hotels = $response->viewData('hotels');
        $this->assertCount(1, $hotels);
        $this->assertEquals($this->hotelMarrakech1->id, $hotels->first()->id);
    }

    /**
     * 2c. Date Availability:
     * Ensure that selecting Check-in / Check-out dates accurately filters out
     * hotels that are fully booked during that date range based on existing reservations.
     */
    public function test_date_availability_filters_out_fully_booked_hotels(): void
    {
        // Add 2 rooms to hotelMarrakech1
        $room1 = Room::create([
            'hotel_id' => $this->hotelMarrakech1->id,
            'room_number' => '101',
            'type' => 'Suite',
            'capacity' => 2,
            'price_per_night' => 1200,
            'status' => 'available',
        ]);

        $room2 = Room::create([
            'hotel_id' => $this->hotelMarrakech1->id,
            'room_number' => '102',
            'type' => 'Standard',
            'capacity' => 2,
            'price_per_night' => 1000,
            'status' => 'available',
        ]);

        $stayIn = Carbon::tomorrow()->format('Y-m-d');
        $stayOut = Carbon::tomorrow()->addDays(3)->format('Y-m-d');

        // Book Room 1 for this date range
        Reservation::create([
            'hotel_id' => $this->hotelMarrakech1->id,
            'room_id' => $room1->id,
            'guest_name' => 'Guest 1',
            'guests_count' => 2,
            'check_in' => $stayIn,
            'check_out' => $stayOut,
            'total_price' => 3600,
            'status' => 'confirmed',
        ]);

        // Query with dates: Room 2 is still free, so hotelMarrakech1 must still be returned!
        $res1 = $this->get(route('hotels.index', [
            'check_in' => $stayIn,
            'check_out' => $stayOut,
        ]));
        $res1->assertStatus(200);
        $ids1 = $res1->viewData('hotels')->pluck('id')->toArray();
        $this->assertContains($this->hotelMarrakech1->id, $ids1);

        // Now book Room 2 as well for the same dates -> hotelMarrakech1 is now FULLY BOOKED
        $resB = Reservation::create([
            'hotel_id' => $this->hotelMarrakech1->id,
            'room_id' => $room2->id,
            'guest_name' => 'Guest 2',
            'guests_count' => 2,
            'check_in' => $stayIn,
            'check_out' => $stayOut,
            'total_price' => 3000,
            'status' => 'confirmed',
        ]);

        $res2 = $this->get(route('hotels.index', [
            'check_in' => $stayIn,
            'check_out' => $stayOut,
        ]));
        $res2->assertStatus(200);
        $ids2 = $res2->viewData('hotels')->pluck('id')->toArray();
        $this->assertNotContains($this->hotelMarrakech1->id, $ids2);

        // Cancel Reservation B -> hotelMarrakech1 should be available again
        $resB->update(['status' => 'cancelled']);

        $res3 = $this->get(route('hotels.index', [
            'check_in' => $stayIn,
            'check_out' => $stayOut,
        ]));
        $res3->assertStatus(200);
        $ids3 = $res3->viewData('hotels')->pluck('id')->toArray();
        $this->assertContains($this->hotelMarrakech1->id, $ids3);
    }

    /**
     * 2d. Center Search Bar works seamlessly across both pages (`/` and `/hotels`).
     */
    public function test_search_bar_seamless_across_home_and_hotels_pages(): void
    {
        // 1. Home page renders the search form with destination, check-in, check-out, guests
        $home = $this->get(route('home'));
        $home->assertStatus(200);
        $home->assertSee('name="city_id"', false);
        $home->assertSee('name="check_in"', false);
        $home->assertSee('name="check_out"', false);
        $home->assertSee('name="guests"', false);

        // 2. Submit form from Home to /hotels
        $searchFromHome = $this->get(route('hotels.index', [
            'city_id' => $this->cityMarrakech->id,
            'check_in' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests' => 2,
        ]));
        $searchFromHome->assertStatus(200);
        $hotels = $searchFromHome->viewData('hotels');
        $this->assertCount(2, $hotels);
        foreach ($hotels as $hotel) {
            $this->assertEquals($this->cityMarrakech->id, $hotel->city_id);
        }

        // 3. /hotels index renders search bar with current values populated
        $searchFromHome->assertSee('value="' . $this->cityMarrakech->id . '" selected', false);
    }

    /**
     * 3. Hotel detail page:
     * Description renders cleanly formatted in structured paragraphs respecting line breaks.
     */
    public function test_hotel_detail_renders_description_with_formatted_paragraphs(): void
    {
        $longUnbrokenString = str_repeat('supercalifragilisticexpialidocious', 5);
        $this->hotelMarrakech1->update([
            'description' => "First paragraph line 1.\nFirst paragraph line 2.\r\n\r\nSecond paragraph line 1 with long word: {$longUnbrokenString}",
        ]);

        $response = $this->get(route('hotels.show', $this->hotelMarrakech1));
        $response->assertStatus(200);

        // Contains container and structured paragraphs
        $response->assertSee('hotel-description-content', false);
        $response->assertSee('break-words', false);
        $response->assertSee('overflow-wrap: anywhere', false);
        $response->assertSee('<br />', false);
        $response->assertSee('First paragraph line 1.', false);
        $response->assertSee('Second paragraph line 1', false);
        $response->assertSee($longUnbrokenString, false);
    }
}
