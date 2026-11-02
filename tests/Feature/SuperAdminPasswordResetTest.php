<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Notifications\AdminPasswordResetNotification;
use App\Notifications\ClientPasswordResetNotification;
use App\Notifications\SuperAdminPasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Feature tests for the Super Admin Forgot Password / Password Reset flow.
 */
class SuperAdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function makeSuperAdmin(array $overrides = []): Admin
    {
        return Admin::create(array_merge([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@example.com',
            'password' => Hash::make('OldSuperPass123!'),
            'role'     => 'super_admin',
            'status'   => 'active',
        ], $overrides));
    }

    private function makeAdmin(array $overrides = []): Admin
    {
        return Admin::create(array_merge([
            'name'     => 'Regular Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('AdminPass123!'),
            'role'     => 'admin',
            'status'   => 'active',
        ], $overrides));
    }

    private function makeClient(array $overrides = []): User
    {
        return User::create(array_merge([
            'name'     => 'Regular Client',
            'email'    => 'client@example.com',
            'password' => Hash::make('ClientPass123!'),
            'role'     => 'client',
            'status'   => 'active',
        ], $overrides));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. Super Admin Forgot Password Page Loads
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function super_admin_forgot_password_page_is_accessible_to_guests(): void
    {
        $response = $this->get(route('super_admin.password.request'));

        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
        $response->assertSee('Super Admin Portal');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. Valid Super Admin Email Returns Generic Status
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function submitting_a_registered_super_admin_email_returns_generic_status(): void
    {
        Notification::fake();

        $superAdmin = $this->makeSuperAdmin();

        $response = $this->post(route('super_admin.password.email'), [
            'email' => $superAdmin->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertStringContainsString(
            'If that email address is registered',
            session('status')
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Unregistered Email Returns the Same Generic Status (Anti-Enumeration)
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function submitting_an_unregistered_email_returns_the_same_generic_status(): void
    {
        Notification::fake();

        $response = $this->post(route('super_admin.password.email'), [
            'email' => 'ghost-superadmin@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertStringContainsString(
            'If that email address is registered',
            session('status')
        );

        Notification::assertNothingSent();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. Invalid Email Format Is Rejected
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function invalid_email_format_is_rejected_on_super_admin_forgot_form(): void
    {
        $response = $this->post(route('super_admin.password.email'), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. Reset Notification Is Sent with Correct Super Admin URL
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function super_admin_password_reset_notification_is_sent_with_correct_super_admin_url(): void
    {
        Notification::fake();

        $superAdmin = $this->makeSuperAdmin();

        $this->post(route('super_admin.password.email'), [
            'email' => $superAdmin->email,
        ]);

        Notification::assertSentTo(
            $superAdmin,
            SuperAdminPasswordResetNotification::class,
            function (SuperAdminPasswordResetNotification $notification) use ($superAdmin) {
                $mailMessage = $notification->toMail($superAdmin);

                $viewData = $mailMessage->viewData;
                $resetUrl = $viewData['resetUrl'] ?? '';

                return str_contains($resetUrl, '/super-admin/reset-password/')
                    && str_contains($resetUrl, 'email=' . urlencode($superAdmin->email));
            }
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. Reset Form Renders with a Valid Token
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function super_admin_reset_password_form_renders_with_a_valid_token(): void
    {
        $superAdmin = $this->makeSuperAdmin();
        $token = Password::broker('super_admins')->createToken($superAdmin);

        $response = $this->get(route('super_admin.password.reset', [
            'token' => $token,
            'email' => $superAdmin->email,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Reset Your Password');
        $response->assertSee('Super Admin Portal');
        $response->assertSee($superAdmin->email);
        $response->assertSee($token);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 7. Password Confirmation Mismatch Is Rejected
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function super_admin_password_confirmation_mismatch_is_rejected(): void
    {
        $superAdmin = $this->makeSuperAdmin();
        $token = Password::broker('super_admins')->createToken($superAdmin);

        $response = $this->post(route('super_admin.password.update'), [
            'token'                 => $token,
            'email'                 => $superAdmin->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword456!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. Invalid Token Shows an Error
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function super_admin_invalid_token_shows_an_error(): void
    {
        $superAdmin = $this->makeSuperAdmin();

        $response = $this->post(route('super_admin.password.update'), [
            'token'                 => 'invalid-fake-token-string',
            'email'                 => $superAdmin->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'invalid',
            strtolower(session('errors')->get('email')[0])
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 9. Expired Token Is Rejected
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function super_admin_expired_token_shows_an_error(): void
    {
        $superAdmin = $this->makeSuperAdmin();
        $token = Password::broker('super_admins')->createToken($superAdmin);

        // Back-date the token to simulate expiry (broker expires at 60 min).
        DB::table('super_admin_password_reset_tokens')
            ->where('email', $superAdmin->email)
            ->update(['created_at' => now()->subMinutes(61)]);

        $response = $this->post(route('super_admin.password.update'), [
            'token'                 => $token,
            'email'                 => $superAdmin->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 10. Successful Reset — Updates Password, Invalidates Token, Redirects
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function successful_super_admin_reset_updates_hashed_password_and_redirects_to_login(): void
    {
        $superAdmin = $this->makeSuperAdmin(['password' => Hash::make('OldSuperPass123!')]);
        $token = Password::broker('super_admins')->createToken($superAdmin);

        $response = $this->post(route('super_admin.password.update'), [
            'token'                 => $token,
            'email'                 => $superAdmin->email,
            'password'              => 'BrandNewSuperPass999!',
            'password_confirmation' => 'BrandNewSuperPass999!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $superAdmin->refresh();
        $this->assertTrue(Hash::check('BrandNewSuperPass999!', $superAdmin->password));
        $this->assertFalse(Hash::check('OldSuperPass123!', $superAdmin->password));

        // Super Admin is NOT auto-authenticated after reset
        $this->assertGuest('admin');

        // Token is invalidated — reusing the token fails
        $retryResponse = $this->post(route('super_admin.password.update'), [
            'token'                 => $token,
            'email'                 => $superAdmin->email,
            'password'              => 'AnotherPassword111!',
            'password_confirmation' => 'AnotherPassword111!',
        ]);
        $retryResponse->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 11. Regular Admin Cannot Use the Super Admin Reset Flow
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function regular_admin_cannot_reset_password_via_super_admin_flow(): void
    {
        $admin = $this->makeAdmin();

        $token = Password::broker('super_admins')->createToken($admin);

        $originalPassword = $admin->password;

        $this->post(route('super_admin.password.update'), [
            'token'                 => $token,
            'email'                 => $admin->email,
            'password'              => 'HackedSuperPassword123!',
            'password_confirmation' => 'HackedSuperPassword123!',
        ]);

        // Regular admin's password must NOT have changed
        $admin->refresh();
        $this->assertSame($originalPassword, $admin->password);
        $this->assertFalse(Hash::check('HackedSuperPassword123!', $admin->password));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 12. Client Cannot Use the Super Admin Reset Flow
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function client_cannot_use_super_admin_reset_flow(): void
    {
        $client = $this->makeClient();

        $response = $this->post(route('super_admin.password.update'), [
            'token'                 => 'some-arbitrary-token',
            'email'                 => $client->email,
            'password'              => 'ClientAttempt123!',
            'password_confirmation' => 'ClientAttempt123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 13. Regular Admin and Client Flows Are Unaffected
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_and_client_forgot_password_features_are_unaffected(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin();
        $this->post(route('admin.password.email'), ['email' => $admin->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($admin, AdminPasswordResetNotification::class);

        $client = $this->makeClient();
        $this->post(route('client.password.email'), ['email' => $client->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($client, ClientPasswordResetNotification::class);
    }
}
