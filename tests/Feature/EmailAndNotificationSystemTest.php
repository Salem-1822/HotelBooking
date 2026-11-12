<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\City;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Notifications\AdminPasswordResetNotification;
use App\Notifications\ClientPasswordResetNotification;
use App\Notifications\ReservationStatusChangedNotification;
use App\Notifications\SuperAdminPasswordResetNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;

class EmailAndNotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Room $room;
    protected User $client;
    protected User $otherClient;
    protected Admin $admin;
    protected Admin $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);

        $this->hotel = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Royal Atlas Palace',
            'address' => 'Hivernage, Marrakech',
            'stars' => 5,
            'price_per_night' => 1500,
            'status' => 'active',
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => 'Suite-101',
            'type' => 'Deluxe Suite',
            'capacity' => 2,
            'price_per_night' => 1500,
            'status' => 'available',
        ]);

        $this->client = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'phone' => '0612345678',
            'password' => Hash::make('Password123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->otherClient = User::create([
            'name' => 'John Connor',
            'email' => 'john@example.com',
            'phone' => '0687654321',
            'password' => Hash::make('Password123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->admin = Admin::create([
            'hotel_id' => $this->hotel->id,
            'city_id' => $this->city->id,
            'name' => 'Hotel Manager',
            'email' => 'manager@royalatlas.com',
            'password' => Hash::make('AdminSecret123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->superAdmin = Admin::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@hotelbooking.com',
            'password' => Hash::make('SuperSecret123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function client_password_reset_sends_notification_only_to_existing_client(): void
    {
        $response = $this->post(route('client.password.email'), [
            'email' => 'sarah@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($this->client, ClientPasswordResetNotification::class);
        Notification::assertNotSentTo($this->otherClient, ClientPasswordResetNotification::class);

        // Anti-enumeration: unregistered email receives same status response, zero notifications sent
        $unregResponse = $this->post(route('client.password.email'), [
            'email' => 'nonexistent@example.com',
        ]);
        $unregResponse->assertSessionHas('status');
    }

    #[Test]
    public function admin_password_reset_sends_admin_notification_strictly_to_admin(): void
    {
        $response = $this->post(route('admin.password.email'), [
            'email' => 'manager@royalatlas.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($this->admin, AdminPasswordResetNotification::class);
        Notification::assertNotSentTo($this->superAdmin, AdminPasswordResetNotification::class);
        Notification::assertNotSentTo($this->client, AdminPasswordResetNotification::class);
    }

    #[Test]
    public function super_admin_password_reset_sends_super_admin_notification_strictly_to_super_admin(): void
    {
        $response = $this->post(route('super_admin.password.email'), [
            'email' => 'superadmin@hotelbooking.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($this->superAdmin, SuperAdminPasswordResetNotification::class);
        Notification::assertNotSentTo($this->admin, SuperAdminPasswordResetNotification::class);
        Notification::assertNotSentTo($this->client, SuperAdminPasswordResetNotification::class);
    }

    #[Test]
    public function reservation_status_transition_sends_email_to_correct_recipient(): void
    {
        $res = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Sarah Connor',
            'guest_phone' => '0612345678',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 2,
            'total_price' => 3000,
            'status' => 'pending',
        ]);

        // Initial creation as pending does NOT send an email
        Notification::assertNothingSent();

        // 1. Transition to confirmed
        $res->update(['status' => 'confirmed']);
        Notification::assertSentTo(
            $this->client,
            ReservationStatusChangedNotification::class,
            fn ($notification) => $notification->reservation->status === 'confirmed'
        );
        Notification::assertNotSentTo($this->otherClient, ReservationStatusChangedNotification::class);

        // 2. Transition to checked_in
        $res->update(['status' => 'checked_in']);
        Notification::assertSentTo(
            $this->client,
            ReservationStatusChangedNotification::class,
            fn ($notification) => $notification->reservation->status === 'checked_in'
        );

        // 3. Transition to checked_out
        $res->update(['status' => 'checked_out']);
        Notification::assertSentTo(
            $this->client,
            ReservationStatusChangedNotification::class,
            fn ($notification) => $notification->reservation->status === 'checked_out'
        );

        // 4. Transition to cancelled
        $res->update(['status' => 'cancelled']);
        Notification::assertSentTo(
            $this->client,
            ReservationStatusChangedNotification::class,
            fn ($notification) => $notification->reservation->status === 'cancelled'
        );
    }

    #[Test]
    public function updating_reservation_without_status_change_does_not_send_duplicate_emails(): void
    {
        $res = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Sarah Connor',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 2,
            'total_price' => 3000,
            'status' => 'confirmed',
        ]);

        Notification::fake(); // Reset fake to count subsequent calls

        // Update another field without modifying status
        $res->update([
            'guest_name' => 'Sarah Connor Updated',
        ]);

        Notification::assertNothingSent();
    }

    #[Test]
    public function customer_without_user_account_receives_status_email_via_customer_model(): void
    {
        $customer = Customer::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Walk-in Guest',
            'email' => 'walkin@external.com',
            'phone' => '+212611223344',
        ]);

        $res = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'customer_id' => $customer->id,
            'guest_name' => 'Walk-in Guest',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 1,
            'total_price' => 3000,
            'status' => 'pending',
        ]);

        Notification::fake();

        $res->update(['status' => 'confirmed']);

        Notification::assertSentTo($customer, ReservationStatusChangedNotification::class);
        Notification::assertNotSentTo($this->client, ReservationStatusChangedNotification::class);
    }
}
