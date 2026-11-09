<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\City;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected Hotel $hotel;
    protected Admin $superAdmin;
    protected Admin $regularAdmin;
    protected User $client;

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

        $this->superAdmin = Admin::create([
            'name'     => 'Salem Zaafrou',
            'email'    => 'salemzaafrou@gmail.com',
            'password' => Hash::make('password123'),
            'role'     => 'super_admin',
            'status'   => 'active',
        ]);

        $this->regularAdmin = Admin::create([
            'name'     => 'Regular Hotel Admin',
            'email'    => 'admin@hotelbooking.com',
            'password' => Hash::make('AdminPassword123!'),
            'role'     => 'admin',
            'city_id'  => $this->city->id,
            'hotel_id' => $this->hotel->id,
            'status'   => 'active',
        ]);

        $this->client = User::create([
            'name'     => 'Regular Client',
            'email'    => 'client@example.com',
            'password' => Hash::make('ClientPassword123!'),
            'role'     => 'client',
            'status'   => 'active',
        ]);
    }

    /**
     * Requirement: Super Admin account exists with correct credentials and role.
     */
    public function test_super_admin_account_exists_with_correct_data_and_hashed_password(): void
    {
        $admin = Admin::where('email', 'salemzaafrou@gmail.com')->first();

        $this->assertNotNull($admin);
        $this->assertSame('Salem Zaafrou', $admin->name);
        $this->assertSame('super_admin', $admin->role);
        $this->assertSame('active', $admin->status);
        $this->assertTrue(Hash::check('password123', $admin->password));
    }

    /**
     * Requirement: Super Admin logs in and is redirected directly to Super Admin dashboard.
     */
    public function test_super_admin_logs_in_successfully_and_redirects_to_super_admin_dashboard(): void
    {
        $response = $this->post('/super-admin/login', [
            'email'    => 'salemzaafrou@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('super_admin.dashboard'));
        $this->assertAuthenticatedAs($this->superAdmin, 'admin');

        // Follow redirect
        $dashboardResponse = $this->get(route('super_admin.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Salem Zaafrou');
    }

    /**
     * Requirement: Super Admin is NEVER redirected to client or regular admin dashboard.
     */
    public function test_super_admin_is_never_redirected_to_client_or_admin_dashboard(): void
    {
        // Even if session has an intended client dashboard URL
        session(['url.intended' => route('client.dashboard')]);

        $response = $this->post('/super-admin/login', [
            'email'    => 'salemzaafrou@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('super_admin.dashboard'));
        $this->assertFalse($response->isRedirect(route('client.dashboard')));
        $this->assertFalse($response->isRedirect(route('admin.dashboard')));
    }

    /**
     * Requirement: Super Admin can access Hotels management.
     */
    public function test_super_admin_can_access_hotels_management(): void
    {
        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.hotels.index'));

        $response->assertStatus(200);
        $response->assertSee('Atlas Luxury Resort');
    }

    /**
     * Requirement: Super Admin can access Admin CRUD.
     */
    public function test_super_admin_can_access_admin_crud(): void
    {
        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('super_admin.admins.index'));

        $response->assertStatus(200);
        $response->assertSee('Regular Hotel Admin');
    }

    /**
     * Requirement: Super Admin creates a new regular Admin through existing CRUD.
     */
    public function test_super_admin_creates_regular_admin_that_can_login_to_admin_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin, 'admin')
            ->post(route('super_admin.admins.store'), [
                'name'     => 'New Hotel Manager',
                'email'    => 'newmanager@hotelbooking.com',
                'password' => 'ManagerSecret123!',
                'city_id'  => $this->city->id,
                'hotel_id' => $this->hotel->id,
            ]);

        $response->assertRedirect(route('super_admin.admins.index'));

        $newAdmin = Admin::where('email', 'newmanager@hotelbooking.com')->first();
        $this->assertNotNull($newAdmin);
        $this->assertSame('admin', $newAdmin->role);

        // Sign out super admin
        $this->post(route('super_admin.logout'));

        // New admin logs in through /admin/login
        $loginResponse = $this->post('/admin/login', [
            'email'    => 'newmanager@hotelbooking.com',
            'password' => 'ManagerSecret123!',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Requirement: Regular Admin cannot access Super Admin dashboard.
     */
    public function test_regular_admin_cannot_access_super_admin_dashboard(): void
    {
        $response = $this->actingAs($this->regularAdmin, 'admin')
            ->get(route('super_admin.dashboard'));

        $response->assertForbidden();
    }

    /**
     * Requirement: Client cannot access Super Admin dashboard.
     */
    public function test_client_cannot_access_super_admin_dashboard(): void
    {
        $response = $this->actingAs($this->client, 'web')
            ->get(route('super_admin.dashboard'));

        $response->assertForbidden();
    }

    /**
     * Requirement: Regular Admin logs in and reaches Admin dashboard.
     */
    public function test_regular_admin_logs_in_and_reaches_admin_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email'    => 'admin@hotelbooking.com',
            'password' => 'AdminPassword123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->regularAdmin, 'admin');
    }

    /**
     * Requirement: Client registration still works normally.
     */
    public function test_client_registration_works_normally(): void
    {
        $response = $this->post('/client/register', [
            'name'                  => 'Sara Client',
            'email'                 => 'sara@example.com',
            'password'              => 'SaraPass123!',
            'password_confirmation' => 'SaraPass123!',
        ]);

        $response->assertRedirect(route('login'));

        $user = User::where('email', 'sara@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('client', $user->role);
        $this->assertTrue(Hash::check('SaraPass123!', $user->password));
    }

    /**
     * Requirement: Client logs in and reaches Client dashboard.
     */
    public function test_client_logs_in_and_reaches_client_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => 'client@example.com',
            'password' => 'ClientPassword123!',
        ]);

        $response->assertRedirect(route('client.dashboard'));
        $this->assertAuthenticatedAs($this->client, 'web');
    }

    /**
     * Requirement: Invalid Super Admin credentials are rejected.
     */
    public function test_invalid_super_admin_credentials_are_rejected(): void
    {
        $response = $this->post('/super-admin/login', [
            'email'    => 'salemzaafrou@gmail.com',
            'password' => 'WrongPassword!',
        ]);

        $this->assertGuest('admin');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Requirement: Regular Admin cannot log in through Super Admin login page.
     */
    public function test_regular_admin_cannot_login_through_super_admin_login(): void
    {
        $response = $this->post('/super-admin/login', [
            'email'    => 'admin@hotelbooking.com',
            'password' => 'AdminPassword123!',
        ]);

        $this->assertGuest('admin');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Requirement: Client cannot log in through Super Admin login page.
     */
    public function test_client_cannot_login_through_super_admin_login(): void
    {
        $response = $this->post('/super-admin/login', [
            'email'    => 'client@example.com',
            'password' => 'ClientPassword123!',
        ]);

        $this->assertGuest('admin');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Requirement: Super Admin cannot log in through regular Admin login page.
     */
    public function test_super_admin_cannot_login_through_regular_admin_login(): void
    {
        $response = $this->post('/admin/login', [
            'email'    => 'salemzaafrou@gmail.com',
            'password' => 'password123',
        ]);

        $this->assertGuest('admin');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Requirement: Super Admin logout works correctly.
     */
    public function test_super_admin_logout_clears_session_and_redirects(): void
    {
        $response = $this->actingAs($this->superAdmin, 'admin')
            ->post('/super-admin/logout');

        $response->assertRedirect(route('super_admin.login'));
        $this->assertGuest('admin');
    }

    /**
     * Requirement: Unauthenticated guest accessing Super Admin dashboard redirects to Super Admin login.
     */
    public function test_guest_accessing_super_admin_dashboard_redirects_to_super_admin_login(): void
    {
        $response = $this->get(route('super_admin.dashboard'));

        $response->assertRedirect(route('super_admin.login'));
    }

    /**
     * Requirement: Unauthenticated guest accessing Admin dashboard redirects to Admin login.
     */
    public function test_guest_accessing_admin_dashboard_redirects_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }
}

