<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminReviewsTest extends TestCase
{
    use RefreshDatabase;

    protected City  $city;
    protected Hotel $hotel;
    protected Hotel $otherHotel;
    protected Admin $admin;
    protected Admin $otherAdmin;
    protected Admin $superAdmin;
    protected User  $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->hotel = Hotel::create([
            'city_id'         => $this->city->id,
            'name'            => 'Atlas Luxury Resort',
            'address'         => 'Hivernage, Marrakech',
            'price_per_night' => 1200,
            'status'          => 'active',
        ]);

        $this->otherHotel = Hotel::create([
            'city_id'         => $this->city->id,
            'name'            => 'Sahara Boutique Hotel',
            'address'         => 'Gueliz, Marrakech',
            'price_per_night' => 800,
            'status'          => 'active',
        ]);

        $this->admin = Admin::create([
            'name'       => 'Karim Admin',
            'email'      => 'karim@hotelbooking.com',
            'password'   => Hash::make('AdminPassword123!'),
            'role'       => 'admin',
            'city_id'    => $this->city->id,
            'hotel_id'   => $this->hotel->id,
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->otherAdmin = Admin::create([
            'name'       => 'Youssef Admin',
            'email'      => 'youssef@hotelbooking.com',
            'password'   => Hash::make('AdminPassword123!'),
            'role'       => 'admin',
            'city_id'    => $this->city->id,
            'hotel_id'   => $this->otherHotel->id,
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->superAdmin = Admin::create([
            'name'       => 'Salem SuperAdmin',
            'email'      => 'superadmin@hotelbooking.com',
            'password'   => Hash::make('SuperPassword123!'),
            'role'       => 'super_admin',
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->client = User::create([
            'name'     => 'Fatima Client',
            'email'    => 'fatima@example.com',
            'password' => Hash::make('ClientPassword123!'),
            'role'     => 'client',
        ]);
    }

    // ─────────────────────────────────────────────
    // 1. Access Control
    // ─────────────────────────────────────────────

    /** Admin can access their reviews page (HTTP 200). */
    public function test_admin_can_access_reviews_index(): void
    {
        Review::create([
            'hotel_id' => $this->hotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 4,
            'comment'  => 'Great stay!',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/reviews')
            ->assertOk()
            ->assertSee('Hotel Reviews');
    }

    /** Unauthenticated user is redirected to admin login. */
    public function test_unauthenticated_user_cannot_access_reviews(): void
    {
        $this->get('/admin/reviews')
            ->assertRedirect();
    }

    /** Client user cannot access the admin reviews page (redirect or 403). */
    public function test_client_user_cannot_access_admin_reviews(): void
    {
        $response = $this->actingAs($this->client)
            ->get('/admin/reviews');

        // The admin guard middleware either redirects (302) or forbids (403) a web User.
        $this->assertTrue(
            in_array($response->getStatusCode(), [301, 302, 303, 307, 308, 403]),
            "Expected redirect or 403, got {$response->getStatusCode()}"
        );
    }

    // ─────────────────────────────────────────────
    // 2. IDOR / Scoping
    // ─────────────────────────────────────────────

    /** Admin only sees reviews for their own hotel. */
    public function test_admin_only_sees_own_hotel_reviews(): void
    {
        Review::create([
            'hotel_id' => $this->hotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 5,
            'comment'  => 'Excellent!',
        ]);

        $otherReview = Review::create([
            'hotel_id' => $this->otherHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 2,
            'comment'  => 'Not great.',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/reviews')
            ->assertOk()
            ->assertSee('Excellent!')
            ->assertDontSee('Not great.');
    }

    /** Admin cannot see another hotel's review. */
    public function test_admin_cannot_see_other_hotel_reviews(): void
    {
        Review::create([
            'hotel_id' => $this->otherHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 1,
            'comment'  => 'Very bad experience.',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get('/admin/reviews')
            ->assertOk()
            ->assertDontSee('Very bad experience.');
    }

    // ─────────────────────────────────────────────
    // 3. Deletion
    // ─────────────────────────────────────────────

    /** Admin can delete a review belonging to their hotel. */
    public function test_admin_can_delete_own_hotel_review(): void
    {
        $review = Review::create([
            'hotel_id' => $this->hotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 3,
            'comment'  => 'Average.',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->delete("/admin/reviews/{$review->id}")
            ->assertRedirect('/admin/reviews')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    /** Admin cannot delete a review belonging to another hotel (403). */
    public function test_admin_cannot_delete_other_hotel_review(): void
    {
        $otherReview = Review::create([
            'hotel_id' => $this->otherHotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 5,
            'comment'  => 'Loved it!',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->delete("/admin/reviews/{$otherReview->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('reviews', ['id' => $otherReview->id]);
    }

    /** Deleting a review does not cascade to user, hotel, or reservation. */
    public function test_deleting_review_does_not_cascade(): void
    {
        $review = Review::create([
            'hotel_id' => $this->hotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 4,
            'comment'  => 'Good.',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->delete("/admin/reviews/{$review->id}")
            ->assertRedirect('/admin/reviews');

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        $this->assertDatabaseHas('users',  ['id' => $this->client->id]);
        $this->assertDatabaseHas('hotels', ['id' => $this->hotel->id]);
    }

    // ─────────────────────────────────────────────
    // 4. Pagination
    // ─────────────────────────────────────────────

    /** Reviews are paginated (15 per page). */
    public function test_reviews_are_paginated(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Review::create([
                'hotel_id' => $this->hotel->id,
                'user_id'  => $this->client->id,
                'rating'   => rand(1, 5),
                'comment'  => "Review number {$i}",
            ]);
        }

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/reviews');

        $response->assertOk();
        // With 15-per-page pagination and 20 records, pagination links should appear.
        $response->assertSee('page=2', false);
    }

    // ─────────────────────────────────────────────
    // 5. Super Admin unaffected
    // ─────────────────────────────────────────────

    /** Super Admin dashboard routes remain accessible (no regression). */
    public function test_super_admin_dashboard_still_accessible(): void
    {
        $this->actingAs($this->superAdmin, 'admin')
            ->get('/super-admin/dashboard')
            ->assertOk();
    }

    // ─────────────────────────────────────────────
    // 6. Admin without hotel_id gets 403
    // ─────────────────────────────────────────────

    /** Admin without an assigned hotel gets a 403 when accessing reviews. */
    public function test_admin_without_hotel_gets_forbidden(): void
    {
        $noHotelAdmin = Admin::create([
            'name'       => 'Orphan Admin',
            'email'      => 'orphan@hotelbooking.com',
            'password'   => Hash::make('Password123!'),
            'role'       => 'admin',
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->actingAs($noHotelAdmin, 'admin')
            ->get('/admin/reviews')
            ->assertForbidden();
    }

    // ─────────────────────────────────────────────
    // 7. Unauthenticated delete attempt
    // ─────────────────────────────────────────────

    /** Unauthenticated delete attempt is redirected. */
    public function test_unauthenticated_delete_redirects(): void
    {
        $review = Review::create([
            'hotel_id' => $this->hotel->id,
            'user_id'  => $this->client->id,
            'rating'   => 3,
            'comment'  => 'Decent.',
        ]);

        $this->delete("/admin/reviews/{$review->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }
}
