<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // Forgot Password — Request Form
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show the Forgot Password form.
     * Redirect to dashboard if the client is already authenticated.
     */
    public function showForgotForm(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }

        return view('client.auth.forgot-password');
    }

    /**
     * Handle submission of the Forgot Password form.
     *
     * Always returns a generic response to prevent account enumeration —
     * the user cannot tell whether the email is registered or not.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        // Attempt to send the reset link via the 'users' broker.
        // We intentionally ignore the status to prevent account enumeration.
        Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        // Generic message regardless of whether the email is registered.
        return back()->with(
            'status',
            'If that email address is registered, you will receive a password reset link shortly.'
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Reset Password — Reset Form
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show the Reset Password form.
     * Passes the token and email to the view.
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('client.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Handle the Reset Password form submission.
     *
     * Validates the token, updates the password, and redirects to the
     * login page. The user is NOT automatically authenticated after reset.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'token.required'     => 'The reset token is missing.',
            'email.required'     => 'Please enter your email address.',
            'email.email'        => 'Please enter a valid email address.',
            'password.required'  => 'Please enter a new password.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        // Attempt to reset the password via the 'users' broker.
        // The broker validates the token, checks expiry, and updates the password.
        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // The 'password' cast on User ensures hashing automatically.
                $user->forceFill(['password' => $password])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Do NOT auto-login. Redirect to the shared login page.
            return redirect()
                ->route('login')
                ->with('success', 'Your password has been reset successfully. Please sign in with your new password.');
        }

        // Token is invalid or expired — return an error on the email field.
        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $this->resolveResetError($status),
            ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Resolve a human-readable error message from a Password broker status string.
     * Avoids exposing internal broker constants directly to the view layer.
     */
    private function resolveResetError(string $status): string
    {
        return match ($status) {
            Password::INVALID_TOKEN  => 'This password reset link is invalid or has already been used.',
            Password::INVALID_USER   => 'We could not find a user with that email address.',
            Password::RESET_THROTTLED => 'Please wait before requesting another password reset.',
            default                  => 'Something went wrong. Please try again.',
        };
    }
}
