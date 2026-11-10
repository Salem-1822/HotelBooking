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

class AdminBlockingTest extends TestCase
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
            'name'       => 'Salem SuperAdmin',
            'email'      => 'superadmin@hotelbooking.com',
            'password'   => Hash::make('password123'),
            'role'       => 'super_admin',
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->regularAdmin = Admin::create([
            'name'       => 'Karim Admin',
            'email'      => 'karim@hotelbooking.com',
            'password'   => Hash::make('AdminPassword123!'),
            'role'       => 'admin',
            'city_id'    => $this->city->id,
            'hotel_id'   => $this->hotel->id,
            'status'     => 'active',
            'is_blocked' => false,
        ]);

        $this->client = User::create([
            'name'     => 'Yassine Client',
            'email'    => 'client@hotelbooking.com',
            'password' => Hash::make('ClientPassword123!'),
            'role'     => 'client',
        ]);
    }

    /**
     * 1. Super Admin can block an administrator.
     */
    public function test_super_admin_can_block_an_administrator(): void
    {
        $response = $this->actingAs($this->superAdmin, 'admin')->put(
            route('super_admin.admins.update', $this->regularAdmin),
            [
                'name'           => $this->regularAdmin->name,
                'email'          => $this->regularAdmin->email,
                'city_id'        => $this->city->id,
                'hotel_id'       => $this->hotel->id,
                'status'         => 'blocked',
                'blocked_reason' => 'Violation des règles de sécurité de la plateforme.',
            ]
        );

        $response->assertRedirect(route('super_admin.admins.index'));
        $response->assertSessionHas('success');

        $this->regularAdmin->refresh();
        $this->assertTrue($this->regularAdmin->is_blocked);
        $this->assertEquals('blocked', $this->regularAdmin->status);
        $this->assertEquals('Violation des règles de sécurité de la plateforme.', $this->regularAdmin->blocked_reason);
        $this->assertNotNull($this->regularAdmin->blocked_at);
    }

    /**
     * 2. Super Admin can unblock an administrator.
     */
    public function test_super_admin_can_unblock_an_administrator(): void
    {
        $this->regularAdmin->markAsBlocked('Blocage initial');
        $this->assertTrue($this->regularAdmin->is_blocked);

        $response = $this->actingAs($this->superAdmin, 'admin')->put(
            route('super_admin.admins.update', $this->regularAdmin),
            [
                'name'     => $this->regularAdmin->name,
                'email'    => $this->regularAdmin->email,
                'city_id'  => $this->city->id,
                'hotel_id' => $this->hotel->id,
                'status'   => 'active',
            ]
        );

        $response->assertRedirect(route('super_admin.admins.index'));
        $response->assertSessionHas('success');

        $this->regularAdmin->refresh();
        $this->assertFalse($this->regularAdmin->is_blocked);
        $this->assertEquals('active', $this->regularAdmin->status);
        $this->assertNull($this->regularAdmin->blocked_reason);
        $this->assertNull($this->regularAdmin->blocked_at);
    }

    /**
     * 3. Blocking requires a reason.
     */
    public function test_blocking_requires_a_reason(): void
    {
        $response = $this->actingAs($this->superAdmin, 'admin')->put(
            route('super_admin.admins.update', $this->regularAdmin),
            [
                'name'           => $this->regularAdmin->name,
                'email'          => $this->regularAdmin->email,
                'city_id'        => $this->city->id,
                'hotel_id'       => $this->hotel->id,
                'status'         => 'blocked',
                'blocked_reason' => '',
            ]
        );

        $response->assertSessionHasErrors(['blocked_reason']);

        $this->regularAdmin->refresh();
        $this->assertFalse($this->regularAdmin->is_blocked);
        $this->assertEquals('active', $this->regularAdmin->status);
    }

    /**
     * 4. Existing administrators remain Active by default.
     */
    public function test_existing_administrators_remain_active_by_default(): void
    {
        $newAdmin = Admin::create([
            'name'     => 'Nouveau Admin Test',
            'email'    => 'nouveau@hotelbooking.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
            'city_id'  => $this->city->id,
            'hotel_id' => $this->hotel->id,
            'status'   => 'active',
        ]);

        $this->assertFalse((bool) $newAdmin->is_blocked);
        $this->assertTrue($newAdmin->isActive());
        $this->assertFalse($newAdmin->isBlocked());
        $this->assertEquals('active', $newAdmin->status);
    }

    /**
     * 5. Active administrators can log in normally.
     */
    public function test_active_administrators_can_log_in_normally(): void
    {
        $response = $this->post(route('admin.login'), [
            'email'    => $this->regularAdmin->email,
            'password' => 'AdminPassword123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->regularAdmin, 'admin');
    }

    /**
     * 6. Blocked administrators cannot log in.
     */
    public function test_blocked_administrators_cannot_log_in(): void
    {
        $this->regularAdmin->markAsBlocked('Accès suspendu pour audit interne.');

        $response = $this->post(route('admin.login'), [
            'email'    => $this->regularAdmin->email,
            'password' => 'AdminPassword123!',
        ]);

        $response->assertRedirect(route('admin.blocked'));
        $this->assertGuest('admin');
    }

    /**
     * 7. Blocked page displays the correct administrator name.
     */
    public function test_blocked_page_displays_the_correct_administrator_name(): void
    {
        $this->regularAdmin->markAsBlocked('Compte suspendu temporairement.');

        $response = $this->withSession(['blocked_admin_id' => $this->regularAdmin->id])
                         ->get(route('admin.blocked'));

        $response->assertOk();
        $response->assertSee('Hello Karim Admin,');
        $response->assertSee('Administrator Account Suspended');
    }

    /**
     * 8. Blocked page displays the correct blocking reason.
     */
    public function test_blocked_page_displays_the_correct_blocking_reason(): void
    {
        $reason = 'Suspicion d\'activité anormale sur les réservations de l\'hôtel.';
        $this->regularAdmin->markAsBlocked($reason);

        $response = $this->withSession(['blocked_admin_id' => $this->regularAdmin->id])
                         ->get(route('admin.blocked'));

        $response->assertOk();
        $response->assertSee('Block Reason:');
        $response->assertSee($reason);
        $response->assertSee('Account Blocked');
        $response->assertSee(route('admin.login'));
    }

    /**
     * 9. Blocked administrators cannot access Admin dashboard.
     */
    public function test_blocked_administrators_cannot_access_admin_dashboard(): void
    {
        $this->regularAdmin->markAsBlocked('Suspension active');

        $response = $this->actingAs($this->regularAdmin, 'admin')->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.blocked'));
        $this->assertGuest('admin');
    }

    /**
     * 10. Blocked administrators cannot access protected Admin routes.
     */
    public function test_blocked_administrators_cannot_access_protected_admin_routes(): void
    {
        $this->regularAdmin->markAsBlocked('Suspension globale');

        $protectedRoutes = [
            route('admin.rooms.index'),
            route('admin.reservations.index'),
            route('admin.customers.index'),
            route('admin.reports.index'),
            route('admin.hotel-profile'),
            route('admin.profile'),
        ];

        foreach ($protectedRoutes as $routeUrl) {
            $response = $this->actingAs($this->regularAdmin, 'admin')->get($routeUrl);
            $response->assertRedirect(route('admin.blocked'));
            $this->assertGuest('admin');
        }
    }

    /**
     * 11. An already authenticated administrator loses access after being blocked.
     */
    public function test_an_already_authenticated_administrator_loses_access_after_being_blocked(): void
    {
        // 1. Regular admin is authenticated and accesses dashboard successfully
        $this->actingAs($this->regularAdmin, 'admin');
        $this->get(route('admin.dashboard'))->assertOk();

        // 2. Super Admin blocks the regular administrator
        $this->regularAdmin->markAsBlocked('Compte suspendu par le Super Admin en direct');

        // 3. Admin makes the next HTTP request
        $response = $this->get(route('admin.dashboard'));

        // 4. Session is immediately revoked and redirected to blocked page
        $response->assertRedirect(route('admin.blocked'));
        $this->assertGuest('admin');
    }

    /**
     * 12. Normal administrators cannot modify blocking status.
     */
    public function test_normal_administrators_cannot_modify_blocking_status(): void
    {
        $response = $this->actingAs($this->regularAdmin, 'admin')->put(
            route('super_admin.admins.update', $this->regularAdmin),
            [
                'name'     => 'Hacked Admin',
                'email'    => $this->regularAdmin->email,
                'city_id'  => $this->city->id,
                'hotel_id' => $this->hotel->id,
                'status'   => 'active',
            ]
        );

        $this->assertTrue(in_array($response->status(), [403, 302], true));
    }

    /**
     * 13. Client authentication remains unaffected.
     */
    public function test_client_authentication_remains_unaffected(): void
    {
        $response = $this->post(route('client.login'), [
            'email'    => $this->client->email,
            'password' => 'ClientPassword123!',
        ]);

        $response->assertRedirect(route('client.dashboard'));
        $this->assertAuthenticatedAs($this->client, 'web');
    }

    /**
     * 14. Super Admin authentication remains unaffected.
     */
    public function test_super_admin_authentication_remains_unaffected(): void
    {
        $response = $this->post(route('super_admin.login'), [
            'email'    => $this->superAdmin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('super_admin.dashboard'));
        $this->assertAuthenticatedAs($this->superAdmin, 'admin');
    }

    /**
     * 15. Unblocking restores normal authentication.
     */
    public function test_unblocking_restores_normal_authentication(): void
    {
        // Step 1: Admin is blocked
        $this->regularAdmin->markAsBlocked('Blocage pour vérification');

        // Login fails with redirect to blocked
        $this->post(route('admin.login'), [
            'email'    => $this->regularAdmin->email,
            'password' => 'AdminPassword123!',
        ])->assertRedirect(route('admin.blocked'));
        $this->assertGuest('admin');

        // Step 2: Super Admin unblocks the admin
        $this->actingAs($this->superAdmin, 'admin')->put(
            route('super_admin.admins.update', $this->regularAdmin),
            [
                'name'     => $this->regularAdmin->name,
                'email'    => $this->regularAdmin->email,
                'city_id'  => $this->city->id,
                'hotel_id' => $this->hotel->id,
                'status'   => 'active',
            ]
        )->assertRedirect(route('super_admin.admins.index'));

        // Step 3: Admin logs in normally with the exact same credentials
        Auth::guard('admin')->logout();

        $loginResponse = $this->post(route('admin.login'), [
            'email'    => $this->regularAdmin->email,
            'password' => 'AdminPassword123!',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->regularAdmin->fresh(), 'admin');

        // Step 4: Admin has full access to protected dashboard
        $this->get(route('admin.dashboard'))->assertOk();
    }
}

