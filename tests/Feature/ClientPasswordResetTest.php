<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ClientPasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Feature tests for the Client Forgot Password / Password Reset flow.
 *
 * All 16 required scenarios are covered:
 *   1.  Forgot password page is accessible
 *   2.  Forgot password page redirects authenticated clients
 *   3.  Valid email submission returns a generic status message
 *   4.  Non-existent email submission returns the same generic status (anti-enumeration)
 *   5.  Invalid email format is rejected by validation
 *   6.  Notification is sent to the correct user
 *   7.  No notification is sent for an unregistered email
 *   8.  Reset link rate limiting (throttle) prevents rapid re-submissions
 *   9.  Reset password page renders with a valid token
 *  10.  Reset password page with an invalid token shows an error
 *  11.  Reset password page with an expired token shows an error
 *  12.  Successful password reset redirects to login
 *  13.  Successful password reset shows a success flash message
 *  14.  Password is actually updated after reset
 *  15.  Client is NOT automatically authenticated after reset
 *  16.  Password confirmation mismatch is rejected
 */
class ClientPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────────────────
    // 1. Forgot Password Page — Accessibility
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function forgot_password_page_is_accessible_to_guests(): void
    {
        $response = $this->get(route('client.password.request'));

        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. Authenticated Client — Redirect
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function authenticated_client_is_redirected_from_forgot_password_page(): void
    {
        $user = User::factory()->create(['role' => 'client', 'status' => 'active']);

        $response = $this->actingAs($user, 'web')
            ->get(route('client.password.request'));

        $response->assertRedirect(route('client.dashboard'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Valid Email — Generic Status Returned
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function submitting_a_registered_email_returns_generic_status(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'client', 'status' => 'active']);

        $response = $this->post(route('client.password.email'), [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // The message must be generic — it must not confirm the email is registered.
        $this->assertStringContainsString(
            'If that email address is registered',
            session('status')
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. Non-Existent Email — Same Generic Status (Anti-Enumeration)
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function submitting_an_unregistered_email_returns_the_same_generic_status(): void
    {
        Notification::fake();

        $response = $this->post(route('client.password.email'), [
            'email' => 'no-such-user@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertStringContainsString(
            'If that email address is registered',
            session('status')
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. Invalid Email Format — Validation Error
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function submitting_an_invalid_email_format_returns_a_validation_error(): void
    {
        $response = $this->post(route('client.password.email'), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. Notification Sent to Registered User
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function password_reset_notification_is_sent_to_a_registered_user(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'client', 'status' => 'active']);

        $this->post(route('client.password.email'), [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            ClientPasswordResetNotification::class,
            function (ClientPasswordResetNotification $notification) use ($user) {
                $mail = $notification->toMail($user);
                $rendered = $mail->render();

                return $mail->subject === 'Reset Your HotelBooking Password'
                    && str_contains($rendered, 'Reset Your Password')
                    && str_contains($rendered, 'HotelBooking')
                    && str_contains($rendered, 'The HotelBooking Team')
                    && str_contains($rendered, (string) config('auth.passwords.users.expire', 60))
                    && str_contains($rendered, $notification->token);
            }
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 7. No Notification Sent for Unregistered Email
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function no_notification_is_sent_for_an_unregistered_email(): void
    {
        Notification::fake();

        $this->post(route('client.password.email'), [
            'email' => 'ghost@example.com',
        ]);

        Notification::assertNothingSent();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. Reset Link Rate Limiting
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function reset_link_requests_are_throttled_per_user(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'client', 'status' => 'active']);

        // First request — should succeed and send a notification.
        $firstResponse = $this->post(route('client.password.email'), [
            'email' => $user->email,
        ]);
        $firstResponse->assertRedirect();
        $firstResponse->assertSessionHas('status');

        // Second immediate request — broker throttles it (RESET_THROTTLED).
        // The response is still a redirect with a generic status (same message),
        // but no second notification is dispatched.
        $secondResponse = $this->post(route('client.password.email'), [
            'email' => $user->email,
        ]);
        $secondResponse->assertRedirect();

        // Only one notification should have been sent despite two submissions.
        Notification::assertSentToTimes($user, ClientPasswordResetNotification::class, 1);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 9. Reset Password Page — Valid Token
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function reset_password_page_renders_with_a_valid_token(): void
    {
        $user  = User::factory()->create(['role' => 'client', 'status' => 'active']);
        $token = Password::broker('users')->createToken($user);

        $response = $this->get(route('client.password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Reset Your Password');
        $response->assertSee($user->email);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 10. Reset Password Page — Invalid Token Shows Error
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function submitting_an_invalid_token_shows_an_error(): void
    {
        $user = User::factory()->create(['role' => 'client', 'status' => 'active']);

        $response = $this->post(route('client.password.update'), [
            'token'                 => 'totally-invalid-token',
            'email'                 => $user->email,
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
    // 11. Expired Token Shows an Error
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function submitting_an_expired_token_shows_an_error(): void
    {
        $user  = User::factory()->create(['role' => 'client', 'status' => 'active']);
        $token = Password::broker('users')->createToken($user);

        // Manually expire the token by back-dating its created_at.
        \DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->update(['created_at' => now()->subMinutes(61)]);

        $response = $this->post(route('client.password.update'), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 12. Successful Reset — Redirects to Login
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function successful_password_reset_redirects_to_login_page(): void
    {
        $user  = User::factory()->create(['role' => 'client', 'status' => 'active']);
        $token = Password::broker('users')->createToken($user);

        $response = $this->post(route('client.password.update'), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect(route('login'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 13. Successful Reset — Success Flash Message
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function successful_password_reset_flashes_a_success_message(): void
    {
        $user  = User::factory()->create(['role' => 'client', 'status' => 'active']);
        $token = Password::broker('users')->createToken($user);

        $response = $this->post(route('client.password.update'), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHas('success');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 14. Password Is Actually Updated
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function password_is_actually_updated_after_a_successful_reset(): void
    {
        $user  = User::factory()->create([
            'role'     => 'client',
            'status'   => 'active',
            'password' => Hash::make('OldPassword123!'),
        ]);
        $token = Password::broker('users')->createToken($user);

        $this->post(route('client.password.update'), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'BrandNewPassword999!',
            'password_confirmation' => 'BrandNewPassword999!',
        ]);

        $user->refresh();

        $this->assertTrue(Hash::check('BrandNewPassword999!', $user->password));
        $this->assertFalse(Hash::check('OldPassword123!', $user->password));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 15. Client Is NOT Auto-Authenticated After Reset
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function client_is_not_automatically_authenticated_after_password_reset(): void
    {
        $user  = User::factory()->create(['role' => 'client', 'status' => 'active']);
        $token = Password::broker('users')->createToken($user);

        $this->post(route('client.password.update'), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $this->assertGuest('web');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 16. Password Confirmation Mismatch
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function password_confirmation_mismatch_is_rejected(): void
    {
        $user  = User::factory()->create(['role' => 'client', 'status' => 'active']);
        $token = Password::broker('users')->createToken($user);

        $response = $this->post(route('client.password.update'), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'DoesNotMatch999!',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
