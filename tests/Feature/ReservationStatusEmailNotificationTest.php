<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Notifications\ReservationStatusChangedNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReservationStatusEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Hotel $hotel;
    protected Room $room;
    protected User $client;
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $city = City::create(['name' => 'Marrakech', 'slug' => 'marrakech']);

        $this->hotel = Hotel::create([
            'name' => 'Atlas Luxury Resort',
            'city_id' => $city->id,
            'address' => 'Hivernage, Marrakech',
            'status' => 'active',
            'price_per_night' => 1200,
            'stars' => 5,
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => '101',
            'type' => 'Deluxe Suite',
            'capacity' => 2,
            'price_per_night' => 1200,
            'status' => 'available',
        ]);

        $this->client = User::create([
            'name' => 'Karim Alami',
            'email' => 'karim.alami@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->admin = Admin::create([
            'name' => 'Hotel Manager',
            'email' => 'manager@hotelatlas.com',
            'password' => Hash::make('AdminSecret123!'),
            'role' => 'admin',
            'hotel_id' => $this->hotel->id,
            'status' => 'active',
        ]);
    }

    /**
     * Test notification is sent when reservation status is changed to confirmed.
     */
    public function test_email_sent_when_status_changes_to_confirmed(): void
    {
        Notification::fake();

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 2,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'total_price' => 3600,
            'status' => 'pending',
        ]);

        Notification::assertNothingSent();

        // Admin updates status to confirmed
        $this->actingAs($this->admin, 'admin')
            ->put("/admin/reservations/{$reservation->id}", [
                'room_id' => $this->room->id,
                'guest_name' => $this->client->name,
                'guest_phone' => '0612345678',
                'guests_count' => 2,
                'check_in' => Carbon::tomorrow()->toDateString(),
                'check_out' => Carbon::tomorrow()->addDays(3)->toDateString(),
                'status' => 'confirmed',
            ]);

        Notification::assertSentTo(
            $this->client,
            ReservationStatusChangedNotification::class,
            function ($notification) use ($reservation) {
                $mailData = $notification->toMail($this->client);
                return $mailData->subject === 'Votre réservation est confirmée – HotelBooking'
                    && str_contains($mailData->render(), '#MOR-RSV-' . $reservation->id)
                    && str_contains($mailData->render(), 'Atlas Luxury Resort')
                    && str_contains($mailData->render(), route('client.reservations.show', $reservation));
            }
        );
    }

    /**
     * Test notification is sent when reservation status is changed to checked_in.
     */
    public function test_email_sent_when_status_changes_to_checked_in(): void
    {
        Notification::fake();

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 2,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'total_price' => 3600,
            'status' => 'confirmed',
        ]);

        $reservation->update(['status' => 'checked_in']);

        Notification::assertSentTo(
            $this->client,
            ReservationStatusChangedNotification::class,
            function ($notification) {
                $mailData = $notification->toMail($this->client);
                return $mailData->subject === 'Confirmation de votre arrivée – HotelBooking'
                    && str_contains($mailData->render(), 'Arrivée effectuée');
            }
        );
    }

    /**
     * Test notification is sent when reservation status is changed to checked_out and contains review link.
     */
    public function test_email_sent_when_status_changes_to_checked_out_with_review_link(): void
    {
        Notification::fake();

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 2,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'total_price' => 3600,
            'status' => 'checked_in',
        ]);

        $reservation->update(['status' => 'checked_out']);

        $expectedReviewUrl = route('client.reservations.show', $reservation) . '#review-section';

        Notification::assertSentTo(
            $this->client,
            ReservationStatusChangedNotification::class,
            function ($notification) use ($expectedReviewUrl) {
                $mailData = $notification->toMail($this->client);
                $rendered = $mailData->render();

                return $mailData->subject === 'Merci pour votre séjour – Partagez votre avis'
                    && str_contains($rendered, 'Donner mon avis')
                    && str_contains($rendered, $expectedReviewUrl);
            }
        );
    }

    /**
     * Test notification is sent when client cancels reservation.
     */
    public function test_email_sent_when_status_changes_to_cancelled(): void
    {
        Notification::fake();

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 2,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'total_price' => 3600,
            'status' => 'confirmed',
        ]);

        $this->actingAs($this->client, 'web')
            ->patch("/client/reservations/{$reservation->id}/cancel");

        Notification::assertSentTo(
            $this->client,
            ReservationStatusChangedNotification::class,
            function ($notification) {
                $mailData = $notification->toMail($this->client);
                return $mailData->subject === 'Mise à jour de votre réservation – Annulation'
                    && str_contains($mailData->render(), 'Annulée');
            }
        );
    }

    /**
     * Test saving the same status again does NOT send another email.
     */
    public function test_saving_same_status_does_not_trigger_email(): void
    {
        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 2,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'total_price' => 3600,
            'status' => 'confirmed',
        ]);

        Notification::fake();

        // Update another attribute without changing status
        $reservation->update(['guests_count' => 1]);

        Notification::assertNothingSent();

        // Update status to same value
        $reservation->update(['status' => 'confirmed']);

        Notification::assertNothingSent();
    }

    /**
     * Test guest reservation associated with a Customer receives the email.
     */
    public function test_customer_receives_email_when_reservation_has_no_user(): void
    {
        $customer = Customer::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Fatima Zahra',
            'phone' => '0698765432',
            'email' => 'fatima@example.com',
        ]);

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'customer_id' => $customer->id,
            'guest_name' => $customer->name,
            'guest_phone' => $customer->phone,
            'guests_count' => 1,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2400,
            'status' => 'pending',
        ]);

        Notification::fake();

        $reservation->update(['status' => 'confirmed']);

        Notification::assertSentTo(
            $customer,
            ReservationStatusChangedNotification::class,
            function ($notification) {
                return $notification->reservation->status === 'confirmed';
            }
        );
    }

    /**
     * Test duplicate status notification is strictly prevented.
     */
    public function test_duplicate_status_notification_prevented_by_deduplication(): void
    {
        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 1,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2400,
            'status' => 'pending',
        ]);

        Notification::fake();

        // First transition to checked_in
        $reservation->update(['status' => 'checked_in']);
        Notification::assertSentTimes(ReservationStatusChangedNotification::class, 1);

        // Attempt second notification for the same transition
        $reservation->sendStatusChangeNotification();
        Notification::assertSentTimes(ReservationStatusChangedNotification::class, 1);
    }

    /**
     * Test that when a database transaction rolls back, no email notification is dispatched.
     */
    public function test_no_email_sent_when_database_transaction_rolls_back(): void
    {
        Reservation::$forceAfterCommitTesting = true;

        try {
            Notification::fake();

            $reservation = Reservation::create([
                'hotel_id' => $this->hotel->id,
                'room_id' => $this->room->id,
                'user_id' => $this->client->id,
                'guest_name' => $this->client->name,
                'guests_count' => 1,
                'check_in' => Carbon::tomorrow()->toDateString(),
                'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
                'total_price' => 2400,
                'status' => 'pending',
            ]);

            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($reservation) {
                    $reservation->update(['status' => 'confirmed']);
                    throw new \RuntimeException('Simulated payment/booking transaction rollback');
                });
            } catch (\RuntimeException $e) {
                // Caught as expected
            }

            Notification::assertNothingSent();
        } finally {
            Reservation::$forceAfterCommitTesting = false;
        }
    }

    /**
     * Test unauthenticated client clicking review link is redirected to login,
     * then returned directly to the intended reservation review page upon authentication.
     */
    public function test_unauthenticated_client_clicking_review_link_redirects_to_login_then_intended_review_page(): void
    {
        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 1,
            'check_in' => Carbon::yesterday()->subDays(2)->toDateString(),
            'check_out' => Carbon::yesterday()->toDateString(),
            'total_price' => 2400,
            'status' => 'checked_out',
        ]);

        $reviewUrl = route('client.reservations.show', ['reservation' => $reservation, 'action' => 'review']);

        // 1. Guest visits the review link
        $response = $this->get($reviewUrl);
        $response->assertRedirect(route('login'));

        // 2. Client logs in
        $loginResponse = $this->post(route('login'), [
            'email' => $this->client->email,
            'password' => 'Secret123!',
        ]);

        // 3. Client is redirected back to their intended review page
        $loginResponse->assertRedirect($reviewUrl);

        // 4. Following redirect shows the reservation and review interface
        $pageResponse = $this->actingAs($this->client, 'web')->get($reviewUrl);
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Leave a Review');
        $pageResponse->assertSee($this->hotel->name);
        $pageResponse->assertSee('#MOR-RSV-' . $reservation->id);
    }

    /**
     * Test IDOR protection: a client cannot view or review another client's reservation.
     */
    public function test_client_cannot_view_or_review_another_clients_reservation(): void
    {
        $otherClient = User::create([
            'name' => 'Sara Mansouri',
            'email' => 'sara.mansouri@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 1,
            'check_in' => Carbon::yesterday()->subDays(2)->toDateString(),
            'check_out' => Carbon::yesterday()->toDateString(),
            'total_price' => 2400,
            'status' => 'checked_out',
        ]);

        // Other client tries to access reservation page -> 403 Forbidden
        $response = $this->actingAs($otherClient, 'web')
            ->get(route('client.reservations.show', $reservation));
        $response->assertStatus(403);

        // Other client tries to submit a review for this reservation -> 403 Forbidden
        $reviewResponse = $this->actingAs($otherClient, 'web')
            ->post(route('client.reservations.reviews.store', $reservation), [
                'rating' => 5,
                'comment' => 'This is an unauthorized review attempt.',
            ]);
        $reviewResponse->assertStatus(403);

        $this->assertDatabaseMissing('reviews', [
            'reservation_id' => $reservation->id,
            'user_id' => $otherClient->id,
        ]);
    }

    /**
     * Test reviews cannot be submitted for uncompleted (e.g. pending/confirmed) reservations.
     */
    public function test_review_cannot_be_submitted_for_uncompleted_stay(): void
    {
        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 1,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2400,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->client, 'web')
            ->post(route('client.reservations.reviews.store', $reservation), [
                'rating' => 5,
                'comment' => 'Reviewing before check-out is forbidden.',
            ]);

        $response->assertSessionHas('error', 'Reviews can only be submitted after your stay is completed.');
        $this->assertDatabaseMissing('reviews', [
            'reservation_id' => $reservation->id,
        ]);
    }

    /**
     * Test duplicate review submission for the same reservation is rejected.
     */
    public function test_duplicate_review_submission_is_rejected(): void
    {
        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => $this->client->name,
            'guests_count' => 1,
            'check_in' => Carbon::yesterday()->subDays(2)->toDateString(),
            'check_out' => Carbon::yesterday()->toDateString(),
            'total_price' => 2400,
            'status' => 'checked_out',
        ]);

        // Submit first review successfully
        $firstResponse = $this->actingAs($this->client, 'web')
            ->post(route('client.reservations.reviews.store', $reservation), [
                'rating' => 5,
                'comment' => 'Wonderful resort with excellent service!',
            ]);

        $firstResponse->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'reservation_id' => $reservation->id,
            'rating' => 5,
        ]);

        // Submit duplicate review -> rejected
        $duplicateResponse = $this->actingAs($this->client, 'web')
            ->post(route('client.reservations.reviews.store', $reservation), [
                'rating' => 4,
                'comment' => 'Trying to submit another review.',
            ]);

        $duplicateResponse->assertSessionHas('error', 'You have already reviewed this reservation.');
        $this->assertEquals(1, $reservation->review()->count());
    }

    /**
     * Test post-login redirect prevents open redirect vulnerabilities.
     */
    public function test_post_login_redirect_prevents_open_redirect_attacks(): void
    {
        // Attacker injects external URL into intended session
        $response = $this->withSession(['url.intended' => 'https://evil-attacker-site.com/steal-credentials'])
            ->post(route('login'), [
                'email' => $this->client->email,
                'password' => 'Secret123!',
            ]);

        // Must redirect safely to internal client dashboard, NOT evil-attacker-site.com
        $response->assertRedirect(route('client.dashboard'));
        $this->assertNotEquals('https://evil-attacker-site.com/steal-credentials', $response->headers->get('Location'));
    }
}
