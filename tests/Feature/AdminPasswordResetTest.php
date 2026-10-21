<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Notifications\AdminPasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Feature tests for the Admin Forgot Password / Password Reset flow.
 *
 * All 11 required scenarios are covered:
 *   1.  Admin Forgot Password page loads (guest accessible)
 *   2.  A valid admin email submission triggers the reset process (generic status)
 *   3.  The reset notification contains the correct admin reset URL
 *   4.  The reset form renders correctly
 *   5.  Mismatched passwords are rejected
 *   6.  Invalid token is rejected
 *   7.  Expired token is rejected
 *   8.  Valid matching passwords succeed — password is securely hashed
 *   9.  Super Admin cannot use the regular Admin reset flow
 *  10.  Client cannot use the Admin reset flow
 *  11.  Existing Client Forgot Password feature still works
 */
class AdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Create a regular Admin (role = 'admin') for use in tests.
     */
    private function makeAdmin(array $overrides = []): Admin
    {
        return Admin::create(array_merge([
            'name'     => 'Test Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('OldPassword123!'),
            'role'     => 'admin',
            'status'   => 'active',
        ], $overrides));
    }

    /**
     * Create a Super Admin (role = 'super_admin') for use in tests.
     */
    private function makeSuperAdmin(array $overrides = []): Admin
    {
        return Admin::create(array_merge([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@example.com',
            'password' => Hash::make('SuperPass123!'),
            'role'     => 'super_admin',
            'status'   => 'active',
        ], $overrides));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. Admin Forgot Password Page Loads
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_forgot_password_page_is_accessible_to_guests(): void
    {
        $response = $this->get(route('admin.password.request'));

        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
        $response->assertSee('Admin Portal');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. Valid Admin Email Returns Generic Status
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function submitting_a_registered_admin_email_returns_generic_status(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin();

        $response = $this->post(route('admin.password.email'), [
            'email' => $admin->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Generic message — must not confirm whether the email is registered.
        $this->assertStringContainsString(
            'If that email address is registered',
            session('status')
        );
    }

    /** @test */
    public function submitting_an_unregistered_email_returns_the_same_generic_status(): void
    {
        Notification::fake();

        $response = $this->post(route('admin.password.email'), [
            'email' => 'nobody@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertStringContainsString(
            'If that email address is registered',
            session('status')
        );
    }

    /** @test */
    public function invalid_email_format_is_rejected_on_admin_forgot_form(): void
    {
        $response = $this->post(route('admin.password.email'), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Reset Notification Contains Correct Admin Reset URL
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_password_reset_notification_is_sent_with_correct_admin_url(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin();

        $this->post(route('admin.password.email'), [
            'email' => $admin->email,
        ]);

        Notification::assertSentTo(
            $admin,
            AdminPasswordResetNotification::class,
            function (AdminPasswordResetNotification $notification) use ($admin) {
                $mail     = $notification->toMail($admin);
                $rendered = $mail->render();

                // Subject is correct.
                $this->assertSame('Reset Your HotelBooking Admin Password', $mail->subject);

                // Reset URL must point to the ADMIN reset route, not the client route.
                $expectedUrlPrefix = route('admin.password.reset', ['token' => $notification->token]);
                $this->assertStringContainsString($expectedUrlPrefix, $rendered);

                // Must NOT contain the client reset URL pattern.
                $this->assertStringNotContainsString('/client/reset-password', $rendered);

                // Email content sanity checks.
                $this->assertStringContainsString('HotelBooking', $rendered);
                $this->assertStringContainsString($notification->token, $rendered);

                return true;
            }
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. Reset Form Renders Correctly
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_reset_password_form_renders_with_a_valid_token(): void
    {
        $admin = $this->makeAdmin();
        $token = Password::broker('admins')->createToken($admin);

        $response = $this->get(route('admin.password.reset', [
            'token' => $token,
            'email' => $admin->email,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Reset Your Password');
        $response->assertSee('Admin Portal');
        $response->assertSee($admin->email);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. Mismatched Passwords Are Rejected
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_password_confirmation_mismatch_is_rejected(): void
    {
        $admin = $this->makeAdmin();
        $token = Password::broker('admins')->createToken($admin);

        $response = $this->post(route('admin.password.update'), [
            'token'                 => $token,
            'email'                 => $admin->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'DoesNotMatch999!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. Invalid Token Is Rejected
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_invalid_token_shows_an_error(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->post(route('admin.password.update'), [
            'token'                 => 'totally-invalid-token',
            'email'                 => $admin->email,
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
    // 7. Expired Token Is Rejected
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_expired_token_shows_an_error(): void
    {
        $admin = $this->makeAdmin();
        $token = Password::broker('admins')->createToken($admin);

        // Back-date the token to simulate expiry (broker expires at 60 min).
        \DB::table('admin_password_reset_tokens')
            ->where('email', $admin->email)
            ->update(['created_at' => now()->subMinutes(61)]);

        $response = $this->post(route('admin.password.update'), [
            'token'                 => $token,
            'email'                 => $admin->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. Successful Reset — Password Hashed, Token Invalidated, Redirect to Login
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function successful_admin_reset_updates_hashed_password_and_redirects_to_login(): void
    {
        $admin = $this->makeAdmin(['password' => Hash::make('OldPassword123!')]);
        $token = Password::broker('admins')->createToken($admin);

        $response = $this->post(route('admin.password.update'), [
            'token'                 => $token,
            'email'                 => $admin->email,
            'password'              => 'BrandNewPassword999!',
            'password_confirmation' => 'BrandNewPassword999!',
        ]);

        // Redirects to the shared login page.
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Password is actually updated and hashed correctly.
        $admin->refresh();
        $this->assertTrue(Hash::check('BrandNewPassword999!', $admin->password));
        $this->assertFalse(Hash::check('OldPassword123!', $admin->password));

        // Admin is NOT auto-authenticated after reset.
        $this->assertGuest('admin');

        // Token is invalidated — a second attempt with the same token should fail.
        $retryResponse = $this->post(route('admin.password.update'), [
            'token'                 => $token,
            'email'                 => $admin->email,
            'password'              => 'AnotherPassword111!',
            'password_confirmation' => 'AnotherPassword111!',
        ]);
        $retryResponse->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 9. Super Admin Cannot Use the Admin Reset Flow
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function super_admin_cannot_reset_password_via_admin_flow(): void
    {
        $superAdmin = $this->makeSuperAdmin();

        // The 'admins' broker looks up users in the Admin model/admins table.
        // A super_admin email IS in the admins table, but after the token is
        // validated the reset closure checks role === 'admin' and skips the update.
        $token = Password::broker('admins')->createToken($superAdmin);

        $originalPassword = $superAdmin->password;

        $this->post(route('admin.password.update'), [
            'token'                 => $token,
            'email'                 => $superAdmin->email,
            'password'              => 'HackedPassword123!',
            'password_confirmation' => 'HackedPassword123!',
        ]);

        // The super_admin's password must NOT have changed.
        $superAdmin->refresh();
        $this->assertSame($originalPassword, $superAdmin->password);
        $this->assertFalse(Hash::check('HackedPassword123!', $superAdmin->password));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 10. Client Cannot Use the Admin Reset Flow
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function client_cannot_use_admin_reset_flow(): void
    {
        // Create a Client (User model, 'users' table).
        $client = User::factory()->create([
            'role'   => 'client',
            'status' => 'active',
        ]);

        // Try to create a token via the 'admins' broker — this will fail because
        // the client is NOT in the admins table, so no token is created.
        $response = $this->post(route('admin.password.email'), [
            'email' => $client->email,
        ]);

        // Should return the same generic status (no error exposure).
        $response->assertSessionHas('status');

        // No token should be stored in the admin_password_reset_tokens table.
        $this->assertDatabaseMissing('admin_password_reset_tokens', [
            'email' => $client->email,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 11. Existing Client Forgot Password Feature Still Works
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function client_forgot_password_feature_is_unaffected(): void
    {
        Notification::fake();

        $client = User::factory()->create([
            'role'   => 'client',
            'status' => 'active',
        ]);

        // Client forgot password page is still accessible.
        $pageResponse = $this->get(route('client.password.request'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Forgot Password');

        // Client email submission still sends a generic status.
        $postResponse = $this->post(route('client.password.email'), [
            'email' => $client->email,
        ]);
        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('status');

        // The client notification goes to the USERS table, not the admins table.
        $this->assertDatabaseMissing('admin_password_reset_tokens', [
            'email' => $client->email,
        ]);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $client->email,
        ]);
    }
}
