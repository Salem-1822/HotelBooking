<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\City;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedirectLoopTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $this->city = City::create([
            'name' => 'Marrakech',
            'slug' => 'marrakech',
        ]);
    }

    public function test_admin_without_assigned_hotel_does_not_loop_and_renders_no_hotel_view(): void
    {
        $admin = Admin::create([
            'name'     => 'Unassigned Admin',
            'email'    => 'unassigned@hotelbooking.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'city_id'  => $this->city->id,
            'hotel_id' => null,
            'status'   => 'active',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('No Hotel Assigned');
        $response->assertSee('Please contact your Super Administrator');
    }

    public function test_customer_and_report_routes_redirect_to_dashboard_not_login_when_no_hotel(): void
    {
        $admin = Admin::create([
            'name'     => 'Unassigned Admin',
            'email'    => 'unassigned2@hotelbooking.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'city_id'  => $this->city->id,
            'hotel_id' => null,
            'status'   => 'active',
        ]);

        $customerResponse = $this->actingAs($admin, 'admin')->get(route('admin.customers.index'));
        $customerResponse->assertRedirect(route('admin.dashboard'));

        $reportResponse = $this->actingAs($admin, 'admin')->get(route('admin.reports.index'));
        $reportResponse->assertRedirect(route('admin.dashboard'));

        $hotelProfileResponse = $this->actingAs($admin, 'admin')->get(route('admin.hotel-profile'));
        $hotelProfileResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_admin_visiting_login_redirects_to_dashboard_without_loop(): void
    {
        $admin = Admin::create([
            'name'     => 'Unassigned Admin',
            'email'    => 'unassigned3@hotelbooking.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'city_id'  => $this->city->id,
            'hotel_id' => null,
            'status'   => 'active',
        ]);

        // When visiting login while authenticated
        $loginResponse = $this->actingAs($admin, 'admin')->get(route('login'));
        $loginResponse->assertRedirect(route('admin.dashboard'));

        // Follow the redirect
        $dashboardResponse = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
    }

    public function test_admin_with_assigned_hotel_loads_full_dashboard(): void
    {
        $hotel = Hotel::create([
            'city_id'         => $this->city->id,
            'name'            => 'Atlas Resort',
            'address'         => 'Avenue Mohammed VI',
            'price_per_night' => 1200,
            'status'          => 'active',
        ]);

        $admin = Admin::create([
            'name'     => 'Assigned Admin',
            'email'    => 'assigned@hotelbooking.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'city_id'  => $this->city->id,
            'hotel_id' => $hotel->id,
            'status'   => 'active',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Atlas Resort');
        $response->assertDontSee('No Hotel Assigned');
    }
}
