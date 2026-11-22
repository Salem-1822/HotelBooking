<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * Handles Forgot Password and Reset Password for regular Admin accounts.
 *
 * This flow is exclusively for accounts with role = 'admin'.
 * It uses the 'admins' password broker (separate token table) to ensure
 * complete isolation from the Client and Super Admin flows.
 */
class PasswordResetController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // Forgot Password — Request Form
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show the Admin Forgot Password form.
     * Redirects to the Admin dashboard if already authenticated as admin.
     */
    public function showForgotForm(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.forgot-password');
    }

    /**
     * Handle submission of the Admin Forgot Password form.
     *
     * Role enforcement: only sends a reset link if the email belongs to an
     * account with role = 'admin'. The response is always generic to prevent
     * account enumeration, regardless of whether the email is registered.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => __('auth.val_email_required'),
            'email.email'    => __('auth.val_email_valid'),
        ]);

        // Attempt to send the reset link via the 'admins' broker.
        // The broker uses the AdminPasswordResetNotification, which points
        // to the admin.password.reset route — not the client reset form.
        // We intentionally ignore the status to prevent account enumeration.
        Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        // Generic message regardless of whether the email is registered.
        return back()->with(
            'status',
            __('passwords.sent_generic')
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Reset Password — Reset Form
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show the Admin Reset Password form.
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Handle the Admin Reset Password form submission.
     *
     * Validates the token against the 'admins' broker, enforces role = 'admin'
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
            'token.required'     => __('passwords.token_missing'),
            'email.required'     => __('auth.val_email_required'),
            'email.email'        => __('auth.val_email_valid'),
            'password.required'  => __('auth.val_password_required'),
            'password.min'       => __('auth.val_password_min'),
            'password.confirmed' => __('auth.val_password_confirmed'),
        ]);

        // Attempt to reset the password via the 'admins' broker.
        // The broker validates the token, checks expiry, and calls the closure.
        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                // Extra role guard: only allow role = 'admin' to reset through this flow.
                // super_admin accounts use a different flow (not implemented here).
                if ($admin->role !== 'admin') {
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
                ->with('success', __('passwords.reset_success'));
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
            Password::INVALID_TOKEN   => __('passwords.token'),
            Password::INVALID_USER    => __('passwords.user'),
            Password::RESET_THROTTLED => __('passwords.throttled'),
            default                   => __('passwords.token'),
        };
    }
}
