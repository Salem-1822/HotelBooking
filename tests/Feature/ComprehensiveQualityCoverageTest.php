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
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class ComprehensiveQualityCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Admin $superAdmin;
    protected Admin $admin;
    protected User $client;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'name' => 'Tangier',
            'slug' => 'tangier',
        ]);

        $this->hotel = Hotel::create([
            'city_id' => $this->city->id,
            'name' => 'El Minzah Hotel',
            'address' => '85 Rue de la Liberte',
            'stars' => 5,
            'price_per_night' => 1100,
            'status' => 'active',
        ]);

        $this->superAdmin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@hotelbooking.com',
            'password' => Hash::make('SuperPass123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->admin = Admin::create([
            'hotel_id' => $this->hotel->id,
            'city_id' => $this->city->id,
            'name' => 'Hotel Manager',
            'email' => 'manager@elminzah.com',
            'password' => Hash::make('AdminPass123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->client = User::create([
            'name' => 'Amine',
            'email' => 'amine@example.com',
            'password' => Hash::make('ClientPass123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => '101',
            'type' => 'Deluxe',
            'capacity' => 2,
            'price_per_night' => 1100,
            'status' => 'available',
        ]);
    }

    #[Test]
    public function client_profile_update_and_password_change_validations(): void
    {
        // 1. Update personal details
        $profileResponse = $this->actingAs($this->client, 'web')
            ->put(route('client.profile.update'), [
                'name' => 'Amine Updated',
                'email' => 'amine.new@example.com',
                'phone' => '0612345678',
            ]);

        $profileResponse->assertSessionHasNoErrors();
        $this->assertEquals('Amine Updated', $this->client->fresh()->name);
        $this->assertEquals('amine.new@example.com', $this->client->fresh()->email);

        // 2. Change password with correct current password
        $passwordResponse = $this->actingAs($this->client, 'web')
            ->put(route('client.profile.password.update'), [
                'current_password' => 'ClientPass123!',
                'password' => 'NewStrongSecret789!',
                'password_confirmation' => 'NewStrongSecret789!',
            ]);

        $passwordResponse->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewStrongSecret789!', $this->client->fresh()->password));

        // 3. Reject password change with incorrect current password
        $wrongPassResponse = $this->actingAs($this->client, 'web')
            ->put(route('client.profile.password.update'), [
                'current_password' => 'WrongCurrent!',
                'password' => 'AnotherPass123!',
                'password_confirmation' => 'AnotherPass123!',
            ]);

        $wrongPassResponse->assertSessionHasErrors('current_password');
    }

    #[Test]
    public function super_admin_pdf_export_routes_generate_valid_pdf_downloads(): void
    {
        Reservation::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->client->id,
            'guest_name' => 'Amine',
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'guests_count' => 2,
            'total_price' => 2200,
            'status' => 'confirmed',
        ]);

        // Super Admin Hotels PDF Export
        $hotelsPdfResponse = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.hotels.export'));
        $hotelsPdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $hotelsPdfResponse->headers->get('content-type'));

        // Super Admin Reservations PDF Export
        $resPdfResponse = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.reservations.export'));
        $resPdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $resPdfResponse->headers->get('content-type'));
    }

    #[Test]
    public function unauthenticated_or_unauthorized_users_cannot_access_pdf_exports(): void
    {
        // Unauthenticated guest redirected
        $this->get(route('super_admin.hotels.export'))
            ->assertRedirect(route('super_admin.login'));

        // Client cannot access super admin export
        $this->actingAs($this->client, 'web')
            ->get(route('super_admin.hotels.export'))
            ->assertForbidden();

        // Regular admin cannot access super admin export
        $this->actingAs($this->admin, 'admin')
            ->get(route('super_admin.hotels.export'))
            ->assertForbidden();
    }
}

