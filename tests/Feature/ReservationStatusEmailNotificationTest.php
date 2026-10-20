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
}
