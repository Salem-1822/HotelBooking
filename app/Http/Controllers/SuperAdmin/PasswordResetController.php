<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * Handles Forgot Password and Reset Password for Super Admin accounts.
 *
 * This flow is exclusively for accounts with role = 'super_admin'.
 * It uses the 'super_admins' password broker (dedicated token table) to ensure
 * complete isolation from regular Admin and Client flows.
 */
class PasswordResetController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // Forgot Password — Request Form
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show the Super Admin Forgot Password form.
     * Redirects to the Super Admin dashboard if already authenticated as super_admin.
     */
    public function showForgotForm(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role === 'super_admin') {
            return redirect()->route('super_admin.dashboard');
        }

        return view('super_admin.auth.forgot-password');
    }

    /**
     * Handle submission of the Super Admin Forgot Password form.
     *
     * Role enforcement: only sends a reset link if the email belongs to an
     * account with role = 'super_admin'. The response is always generic to prevent
     * account enumeration, regardless of whether the email is registered.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        $credentials = [
            'email' => trim((string) $request->input('email')),
            'role'  => 'super_admin',
        ];

        // Attempt to send the reset link via the 'super_admins' broker.
        // We intentionally ignore the status to prevent account enumeration.
        Password::broker('super_admins')->sendResetLink($credentials);

        // Generic message regardless of whether the email is registered.
        return back()->with(
            'status',
            'If that email address is registered to a super admin account, you will receive a password reset link shortly.'
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Reset Password — Reset Form
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show the Super Admin Reset Password form.
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('super_admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Handle the Super Admin Reset Password form submission.
     *
     * Validates the token against the 'super_admins' broker, enforces role = 'super_admin'
     * restriction, updates and hashes the password, invalidates the token,
     * and redirects to the shared login page.
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

        $credentials = [
            'token'                 => $request->input('token'),
            'email'                 => trim((string) $request->input('email')),
            'password'              => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
        ];

        // Attempt to reset the password via the 'super_admins' broker.
        // The broker validates the token, checks expiry, and calls the closure.
        $status = Password::broker('super_admins')->reset(
            $credentials,
            function ($admin, $password) {
                // Extra role guard: only allow role = 'super_admin' to reset through this flow.
                if ($admin->role !== 'super_admin') {
                    return;
                }

                // Hash the password manually (Admin model has no automatic hashing cast).
                $admin->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Do NOT auto-login. Redirect to the shared login page with a success message.
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
     */
    private function resolveResetError(string $status): string
    {
        return match ($status) {
            Password::INVALID_TOKEN   => 'This password reset link is invalid or has already been used.',
            Password::INVALID_USER    => 'We could not find a super admin account with that email address.',
            Password::RESET_THROTTLED => 'Please wait before requesting another password reset.',
            default                   => 'Something went wrong. Please try again.',
        };
    }
}
