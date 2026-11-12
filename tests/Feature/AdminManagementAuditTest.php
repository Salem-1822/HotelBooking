<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class AdminManagementAuditTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotelA;
    protected Hotel $hotelB;
    protected Admin $superAdmin;
    protected Admin $adminA;
    protected Admin $adminB;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->city = City::create([
            'name' => 'Casablanca',
            'slug' => 'casablanca',
        ]);

        $this->hotelA = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Grand Hotel Casablanca',
            'description' => 'A luxury stay in Casablanca.',
            'address' => 'Boulevard d Anfa',
            'stars' => 5,
            'price_per_night' => 1200,
            'status' => 'active',
        ]);

        $this->hotelB = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'Atlas Marina',
            'description' => 'Marina beachfront hotel.',
            'address' => 'Corniche Ain Diab',
            'stars' => 4,
            'price_per_night' => 900,
            'status' => 'active',
        ]);

        $this->superAdmin = Admin::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@hotelbooking.com',
            'password' => Hash::make('SuperSecret123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->adminA = Admin::create([
            'hotel_id' => $this->hotelA->id,
            'city_id' => $this->city->id,
            'name' => 'Admin Alpha',
            'email' => 'admin.a@hotelbooking.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->adminB = Admin::create([
            'hotel_id' => $this->hotelB->id,
            'city_id' => $this->city->id,
            'name' => 'Admin Beta',
            'email' => 'admin.b@hotelbooking.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->client = User::create([
            'name' => 'Guest Client',
            'email' => 'client@guest.com',
            'password' => Hash::make('ClientPass123!'),
            'role' => 'client',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function super_admin_dashboard_accurately_aggregates_database_records(): void
    {
        $room = Room::create([
            'hotel_id' => $this->hotelA->id,
            'room_number' => '101',
            'type' => 'Deluxe',
            'capacity' => 2,
            'price_per_night' => 1200,
            'status' => 'available',
        ]);

        Reservation::create([
            'hotel_id' => $this->hotelA->id,
            'room_id' => $room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Guest Client',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 2,
            'total_price' => 2400.00,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Grand Hotel Casablanca');
        $response->assertSee('2,400'); // Revenue formatted or present
    }

    #[Test]
    public function super_admin_can_perform_hotel_crud_operations(): void
    {
        // 1. Create Hotel
        $createResponse = $this->actingAs($this->superAdmin, 'admin')
            ->post(route('super_admin.hotels.store'), [
                'city_id' => $this->city->id,
                'name' => 'Kenzi Tower Hotel',
                'description' => 'Iconic twin towers hotel in Maarif.',
                'address' => 'Twin Center, Bd Zerktouni',
                'stars' => 5,
                'price_per_night' => 1500,
                'phone' => '0522000000',
                'email' => 'contact@kenzitower.com',
                'status' => 'active',
            ]);

        $createResponse->assertRedirect(route('super_admin.hotels.index'));
        $hotel = Hotel::where('name', 'Kenzi Tower Hotel')->first();
        $this->assertNotNull($hotel);

        // 2. Update Hotel
        $updateResponse = $this->actingAs($this->superAdmin, 'admin')
            ->put(route('super_admin.hotels.update', $hotel), [
                'city_id' => $this->city->id,
                'name' => 'Kenzi Tower Hotel Luxury',
                'description' => 'Updated luxury description.',
                'address' => 'Twin Center, Bd Zerktouni',
                'stars' => 5,
                'price_per_night' => 1700,
                'phone' => '0522000000',
                'email' => 'contact@kenzitower.com',
                'status' => 'active',
            ]);

        $updateResponse->assertRedirect(route('super_admin.hotels.index'));
        $this->assertEquals('Kenzi Tower Hotel Luxury', $hotel->fresh()->name);
        $this->assertEquals(1700, $hotel->fresh()->price_per_night);

        // 3. Delete Hotel
        $deleteResponse = $this->actingAs($this->superAdmin, 'admin')
            ->delete(route('super_admin.hotels.destroy', $hotel));

        $deleteResponse->assertRedirect(route('super_admin.hotels.index'));
        $this->assertNull(Hotel::find($hotel->id));
    }

    #[Test]
    public function hotel_admin_can_manage_rooms_only_for_their_assigned_hotel(): void
    {
        // Admin A creates a room in Hotel A
        $createResponse = $this->actingAs($this->adminA, 'admin')
            ->post(route('admin.rooms.store'), [
                'room_number' => 'A-201',
                'type' => 'Suite',
                'capacity' => 2,
                'price_per_night' => 850,
                'status' => 'available',
            ]);

        $createResponse->assertRedirect(route('admin.rooms.index'));
        $roomA = Room::where('room_number', 'A-201')->first();
        $this->assertNotNull($roomA);
        $this->assertEquals($this->hotelA->id, $roomA->hotel_id);

        // Admin B creates a room in Hotel B
        $roomB = Room::create([
            'hotel_id' => $this->hotelB->id,
            'room_number' => 'B-301',
            'type' => 'Ocean View Suite',
            'capacity' => 3,
            'price_per_night' => 1100,
            'status' => 'available',
        ]);

        // IDOR Attack: Admin A attempts to modify Admin B's room
        $tamperResponse = $this->actingAs($this->adminA, 'admin')
            ->put(route('admin.rooms.update', $roomB), [
                'room_number' => 'B-301',
                'type' => 'Hacked Suite',
                'capacity' => 4,
                'price_per_night' => 10,
                'status' => 'available',
            ]);
        $tamperResponse->assertForbidden();
        $this->assertEquals('Ocean View Suite', $roomB->fresh()->type);

        // IDOR Attack: Admin A attempts to delete Admin B's room
        $deleteTamper = $this->actingAs($this->adminA, 'admin')
            ->delete(route('admin.rooms.destroy', $roomB));
        $deleteTamper->assertForbidden();
        $this->assertNotNull(Room::find($roomB->id));
    }

    #[Test]
    public function hotel_admin_profile_and_password_update_validations(): void
    {
        // Admin A updates name and email
        $updateProfileResponse = $this->actingAs($this->adminA, 'admin')
            ->put(route('admin.profile.update'), [
                'name' => 'Admin Alpha Updated',
                'email' => 'admin.alpha.new@hotelbooking.com',
                'phone' => '0600112233',
            ]);

        $updateProfileResponse->assertSessionHasNoErrors();
        $this->assertEquals('Admin Alpha Updated', $this->adminA->fresh()->name);
        $this->assertEquals('admin.alpha.new@hotelbooking.com', $this->adminA->fresh()->email);

        // Admin A updates password with valid current password
        $passwordResponse = $this->actingAs($this->adminA, 'admin')
            ->put(route('admin.profile.password.update'), [
                'current_password' => 'AdminPass123!',
                'password' => 'NewStrongPass456!',
                'password_confirmation' => 'NewStrongPass456!',
            ]);

        $passwordResponse->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewStrongPass456!', $this->adminA->fresh()->password));

        // Wrong current password must be rejected
        $wrongPassResponse = $this->actingAs($this->adminA, 'admin')
            ->put(route('admin.profile.password.update'), [
                'current_password' => 'IncorrectPass!',
                'password' => 'AnotherNewPass789!',
                'password_confirmation' => 'AnotherNewPass789!',
            ]);

        $wrongPassResponse->assertSessionHasErrors('current_password');
    }

    #[Test]
    public function regular_admin_and_client_cannot_access_super_admin_operations(): void
    {
        // Client attempts to reach Super Admin dashboard
        $this->actingAs($this->client, 'web')
            ->get(route('super_admin.dashboard'))
            ->assertForbidden();

        // Regular Admin attempts to reach Super Admin dashboard
        $this->actingAs($this->adminA, 'admin')
            ->get(route('super_admin.dashboard'))
            ->assertForbidden();

        // Regular Admin attempts to list all Admins
        $this->actingAs($this->adminA, 'admin')
            ->get(route('super_admin.admins.index'))
            ->assertForbidden();

        // Regular Admin attempts to modify Super Admin settings
        $this->actingAs($this->adminA, 'admin')
            ->get(route('super_admin.settings'))
            ->assertForbidden();
    }
}
