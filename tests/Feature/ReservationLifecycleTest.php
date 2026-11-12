<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use App\Models\Admin;
use App\Models\Reservation;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class ReservationLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Room $room;
    protected User $client;
    protected User $otherClient;
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->hotel = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Atlas Luxury Palace',
            'description' => 'A luxury stay in the heart of Marrakech.',
            'address' => 'Boulevard Mohamed VI',
            'stars' => 5,
            'price_per_night' => 1000,
            'status' => 'active',
        ]);

        $this->admin = Admin::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Hotel Manager',
            'email' => 'manager@palace.com',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => 'Suite-301',
            'type' => 'Executive Suite',
            'capacity' => 3,
            'price_per_night' => 1200,
            'status' => 'available',
        ]);

        $this->client = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'phone' => '0612345678',
            'password' => Hash::make('Secret123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->otherClient = User::create([
            'name' => 'Kyle Reese',
            'email' => 'kyle@example.com',
            'phone' => '0698765432',
            'password' => Hash::make('Secret123!'),
            'role' => 'client',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function full_reservation_lifecycle_from_client_booking_to_admin_check_in_out_and_review(): void
    {
        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(2)->toDateString(); // 2 nights

        // 1. Client views reservation form
        $formResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reserve.create', [$this->hotel, $this->room]));
        $formResponse->assertStatus(200);
        $formResponse->assertSee('Atlas Luxury Palace');
        $formResponse->assertSee('Suite-301');

        // 2. Client submits reservation
        $storeResponse = $this->actingAs($this->client, 'web')
            ->post(route('client.reserve.store', [$this->hotel, $this->room]), [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests_count' => 2,
                'phone' => '0612345678',
            ]);

        $storeResponse->assertSessionHasNoErrors();
        $reservation = Reservation::where('user_id', $this->client->id)->first();
        $this->assertNotNull($reservation);

        // Verify correct server-side calculations & initial state
        $this->assertEquals(2400.00, (float) $reservation->total_price); // 2 nights * 1200
        $this->assertEquals('pending', $reservation->status);
        $this->assertEquals(2, $reservation->guests_count);
        $this->assertEquals($this->hotel->id, $reservation->hotel_id);
        $this->assertEquals($this->room->id, $reservation->room_id);

        $storeResponse->assertRedirect(route('client.reservations.confirmation', $reservation));

        // 3. Client can view confirmation voucher
        $confirmResponse = $this->actingAs($this->client, 'web')
            ->get(route('client.reservations.confirmation', $reservation));
        $confirmResponse->assertStatus(200);
        $confirmResponse->assertSee('Atlas Luxury Palace');
        $confirmResponse->assertSee('2,400');

        // 4. Admin confirms the reservation
        $updateResponse = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.reservations.update', $reservation), [
                'room_id' => $this->room->id,
                'guest_name' => $reservation->guest_name,
                'guest_phone' => $reservation->guest_phone,
                'guests_count' => 2,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => 'confirmed',
            ]);
        $updateResponse->assertSessionHasNoErrors();
        $this->assertEquals('confirmed', $reservation->fresh()->status);

        // 5. Admin checks in the guest
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.reservations.update', $reservation), [
                'room_id' => $this->room->id,
                'guest_name' => $reservation->guest_name,
                'guest_phone' => $reservation->guest_phone,
                'guests_count' => 2,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => 'checked_in',
            ]);
        $this->assertEquals('checked_in', $reservation->fresh()->status);

        // Client cannot review yet while checked in
        $prematureReviewResponse = $this->actingAs($this->client, 'web')
            ->post(route('client.reservations.reviews.store', $reservation), [
                'rating' => 5,
                'comment' => 'Too early!',
            ]);
        $this->assertEquals(0, Review::where('reservation_id', $reservation->id)->count());

        // 6. Admin checks out the guest
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.reservations.update', $reservation), [
                'room_id' => $this->room->id,
                'guest_name' => $reservation->guest_name,
                'guest_phone' => $reservation->guest_phone,
                'guests_count' => 2,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => 'checked_out',
            ]);
        $this->assertEquals('checked_out', $reservation->fresh()->status);

        // 7. Client submits a review now that stay is checked out
        $reviewResponse = $this->actingAs($this->client, 'web')
            ->post(route('client.reservations.reviews.store', $reservation), [
                'rating' => 5,
                'comment' => 'Exceptional experience and hospitable staff.',
            ]);
        $reviewResponse->assertSessionHasNoErrors();
        $this->assertEquals(1, Review::where('reservation_id', $reservation->id)->count());

        // 8. Client cannot submit a duplicate review for the same reservation
        $duplicateReview = $this->actingAs($this->client, 'web')
            ->post(route('client.reservations.reviews.store', $reservation), [
                'rating' => 4,
                'comment' => 'Trying to review again.',
            ]);
        $this->assertEquals(1, Review::where('reservation_id', $reservation->id)->count());
    }

    #[Test]
    public function client_cannot_view_or_cancel_another_clients_reservation(): void
    {
        $res = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Sarah Connor',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 1,
            'total_price' => 2400,
            'status' => 'pending',
        ]);

        // Kyle tries to view Sarah's reservation
        $this->actingAs($this->otherClient, 'web')
            ->get(route('client.reservations.show', $res))
            ->assertForbidden();

        // Kyle tries to view Sarah's confirmation voucher
        $this->actingAs($this->otherClient, 'web')
            ->get(route('client.reservations.confirmation', $res))
            ->assertForbidden();

        // Kyle tries to cancel Sarah's reservation
        $this->actingAs($this->otherClient, 'web')
            ->patch(route('client.reservations.cancel', $res))
            ->assertForbidden();

        $this->assertEquals('pending', $res->fresh()->status);
    }

    #[Test]
    public function client_can_cancel_own_pending_or_confirmed_reservation(): void
    {
        $res = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Sarah Connor',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 1,
            'total_price' => 2400,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->client, 'web')
            ->patch(route('client.reservations.cancel', $res));

        $response->assertSessionHasNoErrors();
        $this->assertEquals('cancelled', $res->fresh()->status);
    }

    #[Test]
    public function client_cannot_cancel_checked_in_or_checked_out_reservation(): void
    {
        $res = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Sarah Connor',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 1,
            'total_price' => 2400,
            'status' => 'checked_in',
        ]);

        $response = $this->actingAs($this->client, 'web')
            ->patch(route('client.reservations.cancel', $res));

        $this->assertEquals('checked_in', $res->fresh()->status);
    }

    #[Test]
    public function room_availability_validates_against_overlapping_dates(): void
    {
        $start = Carbon::tomorrow();
        $end = Carbon::tomorrow()->addDays(3);

        // Existing reservation from Day 1 to Day 4
        Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Sarah Connor',
            'check_in' => $start->toDateString(),
            'check_out' => $end->toDateString(),
            'guests_count' => 1,
            'total_price' => 3600,
            'status' => 'confirmed',
        ]);

        // Attempt exact overlap
        $this->actingAs($this->otherClient, 'web')
            ->post(route('client.reserve.store', [$this->hotel, $this->room]), [
                'check_in' => $start->toDateString(),
                'check_out' => $end->toDateString(),
                'guests_count' => 1,
            ])
            ->assertSessionHasErrors('check_in');

        // Attempt partial overlap (inside range)
        $this->actingAs($this->otherClient, 'web')
            ->post(route('client.reserve.store', [$this->hotel, $this->room]), [
                'check_in' => $start->copy()->addDay()->toDateString(),
                'check_out' => $end->copy()->subDay()->toDateString(),
                'guests_count' => 1,
            ])
            ->assertSessionHasErrors('check_in');

        // Back-to-back reservation (starts on previous check_out day) is allowed
        $validResponse = $this->actingAs($this->otherClient, 'web')
            ->post(route('client.reserve.store', [$this->hotel, $this->room]), [
                'check_in' => $end->toDateString(),
                'check_out' => $end->copy()->addDays(2)->toDateString(),
                'guests_count' => 1,
            ]);

        $validResponse->assertSessionHasNoErrors();
        $this->assertEquals(2, Reservation::where('room_id', $this->room->id)->count());
    }

    #[Test]
    public function cancelled_reservations_do_not_block_room_availability(): void
    {
        $start = Carbon::tomorrow();
        $end = Carbon::tomorrow()->addDays(3);

        // Existing cancelled reservation
        Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Sarah Connor',
            'check_in' => $start->toDateString(),
            'check_out' => $end->toDateString(),
            'guests_count' => 1,
            'total_price' => 3600,
            'status' => 'cancelled',
        ]);

        // Same dates should now be available
        $response = $this->actingAs($this->otherClient, 'web')
            ->post(route('client.reserve.store', [$this->hotel, $this->room]), [
                'check_in' => $start->toDateString(),
                'check_out' => $end->toDateString(),
                'guests_count' => 1,
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('pending', Reservation::where('user_id', $this->otherClient->id)->first()->status);
    }
}

