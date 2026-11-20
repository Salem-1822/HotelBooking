<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class HomePageReviewTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $activeHotel;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->activeHotel = Hotel::create([
            'city_id'         => $this->city->id,
            'name'            => 'Atlas Luxury Resort',
            'description'     => 'A wonderful resort in Marrakech.',
            'address'         => 'Avenue Mohammed VI',
            'price_per_night' => 1200,
            'status'          => 'active',
            'stars'           => 5,
        ]);

        $this->client = User::create([
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('password123'),
            'status'   => 'active',
        ]);
    }

    public function test_home_page_loads_successfully()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('What Our Guests Say');
        $response->assertSee('Real experiences from real travelers');
    }

    public function test_empty_state_shown_when_no_reviews_exist_and_no_fake_reviews()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('No guest reviews yet.');
        // Verify fake/static fallback testimonials are NOT present
        $response->assertDontSee('Sarah J.');
        $response->assertDontSee('Michael T.');
        $response->assertDontSee('Emma W.');
    }

    public function test_real_client_review_appears_on_home_page_even_with_three_stars()
    {
        $review = Review::create([
            'hotel_id' => $this->activeHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 3,
            'comment'  => 'Pleasant stay, good service and friendly staff.',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Pleasant stay, good service and friendly staff.');
        $response->assertSee('John Doe');
        $response->assertSee('Stayed at Atlas Luxury Resort');
        $response->assertDontSee('No guest reviews yet.');
    }

    public function test_hotel_details_reviews_still_display_properly()
    {
        $review = Review::create([
            'hotel_id' => $this->activeHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 3,
            'comment'  => 'Great location and rooms.',
        ]);

        $response = $this->get(route('hotels.show', $this->activeHotel));

        $response->assertStatus(200);
        $response->assertSee('Great location and rooms.');
        $response->assertSee('John Doe');
    }

    public function test_reviews_from_inactive_hotels_are_not_displayed_on_home_page()
    {
        $inactiveHotel = Hotel::create([
            'city_id'         => $this->city->id,
            'name'            => 'Closed Hotel',
            'description'     => 'Currently closed.',
            'address'         => 'Some street',
            'price_per_night' => 500,
            'status'          => 'inactive',
            'stars'           => 3,
        ]);

        Review::create([
            'hotel_id' => $inactiveHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 5,
            'comment'  => 'Old review of a closed hotel.',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('Old review of a closed hotel.');
        $response->assertSee('No guest reviews yet.');
    }

    public function test_cache_is_invalidated_when_a_review_is_created()
    {
        // First visit warms cache with empty collection
        $response1 = $this->get(route('home'));
        $response1->assertSee('No guest reviews yet.');

        // Now create a review
        Review::create([
            'hotel_id' => $this->activeHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 4,
            'comment'  => 'Newly submitted review.',
        ]);

        // Next visit should immediately reflect the new review because cache was flushed
        $response2 = $this->get(route('home'));
        $response2->assertSee('Newly submitted review.');
    }

    public function test_reviews_render_with_review_comment_class_and_safe_wrapping()
    {
        // 1. Short review
        Review::create([
            'hotel_id' => $this->activeHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 5,
            'comment'  => 'Great!',
        ]);

        // 2. Normal review
        Review::create([
            'hotel_id' => $this->activeHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 4,
            'comment'  => 'The staff was courteous, the rooms were clean, and our stay was pleasant overall.',
        ]);

        // 3. Very long review containing many words and an uninterrupted long string / URL
        $longComment = 'This was an absolutely incredible experience with exceptional service throughout our entire vacation. ' .
            'https://hotelbooking.wasmer.app/hotels/atlas-luxury-resort/extra-long-unbroken-parameter-slug-testing-overflow-handling ' .
            'We will definitely be coming back next season!';
        Review::create([
            'hotel_id' => $this->activeHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 5,
            'comment'  => $longComment,
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('review-comment');
        $response->assertSee('overflow-wrap: anywhere', false);
        $response->assertSee('word-break: normal', false);
        $response->assertSee('white-space: normal', false);
    }
}
