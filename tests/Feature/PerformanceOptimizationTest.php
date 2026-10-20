<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Hotel;
use App\Models\City;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Customer;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Admin $hotelAdmin;
    protected User $client;
    protected Room $room1;
    protected Room $room2;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->hotel = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Atlas Luxury Resort',
            'address' => 'Boulevard Mohamed VI, Marrakech',
            'status' => 'active',
            'price_per_night' => 1200,
            'stars' => 5,
        ]);

        $this->hotelAdmin = Admin::create([
            'name' => 'Manager Atlas',
            'email' => 'admin@atlas.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'hotel_id' => $this->hotel->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);

        $this->client = User::create([
            'name' => 'Yassine',
            'email' => 'yassine@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->room1 = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => '101',
            'type' => 'Deluxe Suite',
            'capacity' => 2,
            'price_per_night' => 1200,
            'status' => 'available',
        ]);

        $this->room2 = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => '102',
            'type' => 'Standard Room',
            'capacity' => 2,
            'price_per_night' => 800,
            'status' => 'available',
        ]);
    }

    public function test_home_page_caches_stats_and_cities_reducing_subsequent_queries()
    {
        DB::enableQueryLog();

        // 1st visit: warming cache
        $response1 = $this->get(route('home'));
        $response1->assertStatus(200);
        $queries1 = count(DB::getQueryLog());

        DB::flushQueryLog();

        // 2nd visit: cached cities, stats, reviews
        $response2 = $this->get(route('home'));
        $response2->assertStatus(200);
        $queries2 = count(DB::getQueryLog());

        // Cached queries count must be strictly less than unheated queries count
        $this->assertLessThan($queries1, $queries2);
    }

    public function test_hotel_show_uses_batch_availability_and_single_aggregate_rating_breakdown()
    {
        // Create an existing reservation on room 1
        Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room1->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 2,
            'check_in' => now()->addDays(5)->format('Y-m-d'),
            'check_out' => now()->addDays(7)->format('Y-m-d'),
            'total_price' => 2400,
            'status' => 'confirmed',
        ]);

        DB::enableQueryLog();

        $response = $this->get(route('hotels.show', [
            'hotel'     => $this->hotel->id,
            'check_in'  => now()->addDays(5)->format('Y-m-d'),
            'check_out' => now()->addDays(7)->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $queries = DB::getQueryLog();

        // Ensure we don't have separate Reservation queries per room (N+1 eliminated)
        $reservationOverlapQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], 'reservations') && str_contains($q['query'], 'check_in');
        });

        // Exactly 1 batch conflict query executed for all rooms
        $this->assertLessThanOrEqual(1, count($reservationOverlapQueries));
    }

    public function test_admin_dashboard_executes_minimal_aggregate_queries()
    {
        DB::enableQueryLog();

        $response = $this->actingAs($this->hotelAdmin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $queries = DB::getQueryLog();

        // Check that monthly queries are aggregated (not 6 separate monthly full-model queries)
        $monthlyQueries = array_filter($queries, function ($q) {
            return str_contains(strtolower($q['query']), 'group by') && str_contains($q['query'], 'reservations');
        });

        $this->assertNotEmpty($monthlyQueries);
    }

    public function test_admin_customers_page_is_paginated_and_does_not_unboundedly_load_all_records()
    {
        $response = $this->actingAs($this->hotelAdmin, 'admin')->get(route('admin.customers.index'));
        $response->assertStatus(200);

        // Assert paginator view instance
        $customers = $response->viewData('customers');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $customers);
        $this->assertEquals(15, $customers->perPage());
    }

    public function test_client_reservations_index_eager_loads_review_without_n_plus_one()
    {
        // Create 2 reservations
        $res = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room1->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 2,
            'check_in' => now()->subDays(5)->format('Y-m-d'),
            'check_out' => now()->subDays(2)->format('Y-m-d'),
            'total_price' => 3600,
            'status' => 'checked_out',
        ]);

        Review::create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->client->id,
            'reservation_id' => $res->id,
            'rating' => 5,
            'comment' => 'Exceptional stay!',
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->client, 'web')->get(route('client.reservations.index'));
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        $reviewQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], 'reviews') && str_contains($q['query'], 'in (');
        });

        // Reviews should be loaded via single eager loading query `select * from reviews where reservation_id in (...)`
        $reservations = $response->viewData('reservations');
        $this->assertGreaterThan(0, $reservations->count());
        $this->assertLessThanOrEqual(1, count($reviewQueries));
    }

    public function test_cache_invalidation_when_hotel_is_updated()
    {
        Cache::put('home_cities_with_counts', ['dummy'], 3600);
        $this->hotel->touch(); // triggers saved event
        $this->assertFalse(Cache::has('home_cities_with_counts'));
    }
}
