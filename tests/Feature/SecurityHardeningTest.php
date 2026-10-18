<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotelA;
    protected Hotel $hotelB;
    protected Admin $superAdmin;
    protected Admin $hotelAdminA;
    protected Admin $hotelAdminB;
    protected User $clientA;
    protected User $clientB;
    protected Room $roomA;
    protected Room $roomB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'name' => 'Casablanca',
            'slug' => 'casablanca',
        ]);

        $this->hotelA = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Grand Hotel Casablanca',
            'address' => 'Boulevard d\'Anfa',
            'price_per_night' => 500,
            'status' => 'active',
        ]);

        $this->hotelB = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Royal Atlas Hotel',
            'address' => 'Corniche',
            'price_per_night' => 800,
            'status' => 'active',
        ]);

        $this->superAdmin = Admin::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('SuperSecretPassword123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->hotelAdminA = Admin::create([
            'name' => 'Admin Hotel A',
            'email' => 'admin_a@example.com',
            'password' => Hash::make('AdminSecretPassword123!'),
            'role' => 'admin',
            'hotel_id' => $this->hotelA->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);

        $this->hotelAdminB = Admin::create([
            'name' => 'Admin Hotel B',
            'email' => 'admin_b@example.com',
            'password' => Hash::make('AdminSecretPassword123!'),
            'role' => 'admin',
            'hotel_id' => $this->hotelB->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);

        $this->clientA = User::create([
            'name' => 'Client Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('ClientPassword123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->clientB = User::create([
            'name' => 'Client Bob',
            'email' => 'bob@example.com',
            'password' => Hash::make('ClientPassword123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->roomA = Room::create([
            'hotel_id' => $this->hotelA->id,
            'room_number' => '101',
            'type' => 'Deluxe',
            'capacity' => 2,
            'price_per_night' => 500,
            'status' => 'available',
        ]);

        $this->roomB = Room::create([
            'hotel_id' => $this->hotelB->id,
            'room_number' => '201',
            'type' => 'Suite',
            'capacity' => 4,
            'price_per_night' => 800,
            'status' => 'available',
        ]);
    }

    /**
     * Requirement 1: Super Admin can access authorized Super Admin routes.
     */
    public function test_super_admin_can_access_authorized_super_admin_routes(): void
    {
        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get('/super-admin/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get('/super-admin/hotels');
        $response->assertStatus(200);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get('/super-admin/cities');
        $response->assertStatus(200);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get('/super-admin/reservations');
        $response->assertStatus(200);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get('/super-admin/admins');
        $response->assertStatus(200);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get('/super-admin/settings');
        $response->assertStatus(200);
    }

    /**
     * Requirement 2: Hotel Admin is denied access to Super Admin routes.
     */
    public function test_hotel_admin_is_denied_access_to_super_admin_routes(): void
    {
        // Must return 403 Forbidden, NOT 200 or 302 redirect
        $this->actingAs($this->hotelAdminA, 'admin')
            ->get('/super-admin/dashboard')
            ->assertForbidden();

        $this->actingAs($this->hotelAdminA, 'admin')
            ->get('/super-admin/hotels')
            ->assertForbidden();

        $this->actingAs($this->hotelAdminA, 'admin')
            ->get('/super-admin/cities')
            ->assertForbidden();

        $this->actingAs($this->hotelAdminA, 'admin')
            ->get('/super-admin/reservations')
            ->assertForbidden();

        $this->actingAs($this->hotelAdminA, 'admin')
            ->get('/super-admin/admins')
            ->assertForbidden();

        $this->actingAs($this->hotelAdminA, 'admin')
            ->get('/super-admin/settings')
            ->assertForbidden();

        // Direct state-changing request
        $this->actingAs($this->hotelAdminA, 'admin')
            ->post('/super-admin/cities', [
                'name' => 'Unauthorized City',
            ])
            ->assertForbidden();
    }

    /**
     * Requirement 3: Client is denied access to admin and super admin routes.
     */
    public function test_client_is_denied_access_to_admin_routes(): void
    {
        $this->actingAs($this->clientA, 'web')
            ->get('/admin/dashboard')
            ->assertForbidden();

        $this->actingAs($this->clientA, 'web')
            ->get('/admin/rooms')
            ->assertForbidden();

        $this->actingAs($this->clientA, 'web')
            ->get('/admin/reservations')
            ->assertForbidden();

        $this->actingAs($this->clientA, 'web')
            ->get('/super-admin/dashboard')
            ->assertForbidden();
    }

    /**
     * Requirement 4: Hotel Admin cannot access or modify another hotel's data by changing an ID.
     */
    public function test_hotel_admin_cannot_access_another_hotels_data_by_changing_id(): void
    {
        // Hotel Admin A tries to modify Room of Hotel B
        $response = $this->actingAs($this->hotelAdminA, 'admin')
            ->put("/admin/rooms/{$this->roomB->id}", [
                'room_number' => '201',
                'type' => 'Suite',
                'capacity' => 4,
                'price_per_night' => 10,
                'status' => 'available',
            ]);
        $response->assertForbidden();

        // Hotel Admin A tries to delete Room of Hotel B
        $this->actingAs($this->hotelAdminA, 'admin')
            ->delete("/admin/rooms/{$this->roomB->id}")
            ->assertForbidden();

        // Hotel Admin A tries to modify a Reservation belonging to Hotel B
        $resB = Reservation::create([
            'hotel_id' => $this->hotelB->id,
            'room_id' => $this->roomB->id,
            'user_id' => $this->clientB->id,
            'guest_name' => 'Bob',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 1,
            'total_price' => 1600,
            'status' => 'pending',
        ]);

        $this->actingAs($this->hotelAdminA, 'admin')
            ->put("/admin/reservations/{$resB->id}", [
                'room_id' => $this->roomA->id,
                'guest_name' => 'Tampered',
                'check_in' => Carbon::tomorrow()->toDateString(),
                'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
                'guests_count' => 1,
                'status' => 'confirmed',
            ])
            ->assertForbidden();

        $this->actingAs($this->hotelAdminA, 'admin')
            ->delete("/admin/reservations/{$resB->id}")
            ->assertForbidden();

        // Customer of Hotel B
        $customerB = Customer::create([
            'hotel_id' => $this->hotelB->id,
            'name' => 'Customer B',
            'phone' => '0600000000',
        ]);

        $this->actingAs($this->hotelAdminA, 'admin')
            ->get("/admin/customers/{$customerB->id}")
            ->assertForbidden();

        $this->actingAs($this->hotelAdminA, 'admin')
            ->patch("/admin/customers/{$customerB->id}", [
                'name' => 'Hacked Name',
                'phone' => '0699999999',
            ])
            ->assertForbidden();

        $this->actingAs($this->hotelAdminA, 'admin')
            ->delete("/admin/customers/{$customerB->id}")
            ->assertForbidden();
    }

    /**
     * Requirement 5: Client A cannot read or change Client B's reservation.
     */
    public function test_client_a_cannot_read_or_change_client_b_reservation(): void
    {
        $resB = Reservation::create([
            'hotel_id' => $this->hotelB->id,
            'room_id' => $this->roomB->id,
            'user_id' => $this->clientB->id,
            'guest_name' => $this->clientB->name,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 2,
            'total_price' => 1600,
            'status' => 'pending',
        ]);

        // Client A tries to view confirmation
        $this->actingAs($this->clientA, 'web')
            ->get("/client/reservations/{$resB->id}/confirmation")
            ->assertForbidden();

        // Client A tries to view details
        $this->actingAs($this->clientA, 'web')
            ->get("/client/reservations/{$resB->id}")
            ->assertForbidden();

        // Client A tries to cancel Client B's reservation
        $this->actingAs($this->clientA, 'web')
            ->patch("/client/reservations/{$resB->id}/cancel")
            ->assertForbidden();

        $this->assertEquals('pending', $resB->fresh()->status);
    }

    /**
     * Requirement 6: Unauthorized users cannot confirm, cancel, or alter reservations.
     */
    public function test_unauthorized_users_cannot_confirm_cancel_or_alter_reservations(): void
    {
        $res = Reservation::create([
            'hotel_id' => $this->hotelA->id,
            'room_id' => $this->roomA->id,
            'user_id' => $this->clientA->id,
            'guest_name' => $this->clientA->name,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 1,
            'total_price' => 1000,
            'status' => 'pending',
        ]);

        // Unauthenticated guest attempts
        $this->get("/client/reservations/{$res->id}")
            ->assertRedirect('/login');

        $this->patch("/client/reservations/{$res->id}/cancel")
            ->assertRedirect('/login');

        // Client A tries to cancel already cancelled reservation
        $res->update(['status' => 'cancelled']);
        $this->actingAs($this->clientA, 'web')
            ->patch("/client/reservations/{$res->id}/cancel")
            ->assertSessionHas('error');
    }

    /**
     * Requirement 7: Clients cannot forge server-calculated prices or payment states.
     */
    public function test_clients_cannot_forge_server_calculated_prices_or_payment_states(): void
    {
        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(3)->toDateString(); // 3 nights @ 500 = 1500

        $response = $this->actingAs($this->clientA, 'web')
            ->post("/client/reserve/{$this->hotelA->id}/{$this->roomA->id}", [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests_count' => 2,
                'total_price' => 1.00, // Attacker attempts to forge price
                'status' => 'confirmed', // Attacker attempts to forge confirmed status
                'phone' => '0612345678',
            ]);

        $created = Reservation::where('user_id', $this->clientA->id)->latest()->first();
        $this->assertNotNull($created);
        // Must be calculated on server: 3 nights * 500 = 1500
        $this->assertEquals(1500.00, (float) $created->total_price);
        // Must be forced to 'pending', ignoring user request
        $this->assertEquals('pending', $created->status);
    }

    /**
     * Requirement 8: Invalid booking dates and unavailable rooms are rejected.
     */
    public function test_invalid_booking_dates_and_unavailable_rooms_are_rejected(): void
    {
        // 1. Date in the past
        $response = $this->actingAs($this->clientA, 'web')
            ->post("/client/reserve/{$this->hotelA->id}/{$this->roomA->id}", [
                'check_in' => Carbon::yesterday()->toDateString(),
                'check_out' => Carbon::tomorrow()->toDateString(),
                'guests_count' => 1,
            ]);
        $response->assertSessionHasErrors('check_in');

        // 2. Check-out before check-in
        $response = $this->actingAs($this->clientA, 'web')
            ->post("/client/reserve/{$this->hotelA->id}/{$this->roomA->id}", [
                'check_in' => Carbon::tomorrow()->addDays(2)->toDateString(),
                'check_out' => Carbon::tomorrow()->toDateString(),
                'guests_count' => 1,
            ]);
        $response->assertSessionHasErrors('check_out');

        // 3. Exceeds room capacity (Room capacity is 2, requested 5)
        $response = $this->actingAs($this->clientA, 'web')
            ->post("/client/reserve/{$this->hotelA->id}/{$this->roomA->id}", [
                'check_in' => Carbon::tomorrow()->toDateString(),
                'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
                'guests_count' => 5,
            ]);
        $response->assertSessionHasErrors('guests_count');

        // 4. Unavailable room (maintenance)
        $this->roomA->update(['status' => 'maintenance']);
        $response = $this->actingAs($this->clientA, 'web')
            ->post("/client/reserve/{$this->hotelA->id}/{$this->roomA->id}", [
                'check_in' => Carbon::tomorrow()->toDateString(),
                'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
                'guests_count' => 2,
            ]);
        $response->assertSessionHasErrors('room');
    }

    /**
     * Requirement 9: Concurrent booking behavior and date overlap prevention.
     */
    public function test_overlapping_booking_behavior_is_rejected(): void
    {
        $start = Carbon::tomorrow();
        $end = Carbon::tomorrow()->addDays(4);

        // Pre-existing booking from Day 1 to Day 5
        Reservation::create([
            'hotel_id' => $this->hotelA->id,
            'room_id' => $this->roomA->id,
            'user_id' => $this->clientB->id,
            'guest_name' => $this->clientB->name,
            'check_in' => $start->toDateString(),
            'check_out' => $end->toDateString(),
            'guests_count' => 2,
            'total_price' => 2000,
            'status' => 'confirmed',
        ]);

        // Attempt overlapping reservation (Day 2 to Day 6)
        $overlapResponse = $this->actingAs($this->clientA, 'web')
            ->post("/client/reserve/{$this->hotelA->id}/{$this->roomA->id}", [
                'check_in' => $start->copy()->addDay()->toDateString(),
                'check_out' => $end->copy()->addDay()->toDateString(),
                'guests_count' => 2,
            ]);
        $overlapResponse->assertSessionHasErrors('check_in');

        // Non-overlapping reservation (Day 5 to Day 8) succeeds
        $nonOverlapResponse = $this->actingAs($this->clientA, 'web')
            ->post("/client/reserve/{$this->hotelA->id}/{$this->roomA->id}", [
                'check_in' => $end->toDateString(),
                'check_out' => $end->copy()->addDays(3)->toDateString(),
                'guests_count' => 2,
            ]);
        $nonOverlapResponse->assertSessionHasNoErrors();
        $nonOverlapResponse->assertRedirect();
    }

    /**
     * Additional Security Test: Rate limiting brute-force protection on Login.
     */
    public function test_login_brute_force_is_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'victim@example.com',
                'password' => 'WrongPassword!',
            ]);
        }

        // 6th attempt should be throttled
        $response = $this->post('/login', [
            'email' => 'victim@example.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $msg = strtolower($errors[0]);
        $this->assertTrue(str_contains($msg, 'second') || str_contains($msg, 'trop') || str_contains($msg, 'too many') || str_contains($msg, 'tentative'));
    }

    /**
     * Additional Security Test: Inactive account cannot log in.
     */
    public function test_inactive_accounts_cannot_authenticate(): void
    {
        $inactiveAdmin = Admin::create([
            'name' => 'Inactive Admin',
            'email' => 'inactive@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'status' => 'inactive',
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'Password123!',
        ]);

        $this->assertFalse(\Illuminate\Support\Facades\Auth::guard('admin')->check());
        $response->assertSessionHasErrors('email');
    }
}
