<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // If already authenticated via admin guard (Super Admin or Admin), redirect accordingly
        if (Auth::guard('admin')->check()) {
            $role = Auth::guard('admin')->user()->role;
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('super_admin.dashboard');
        }

        // If already authenticated via web guard (Client), redirect to client dashboard
        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }

        return view('super_admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
            ]);
            if ($message === 'auth.throttle') {
                $message = "Too many login attempts. Please try again in {$seconds} seconds.";
            }
            return back()->withErrors([
                'email' => $message,
            ])->onlyInput('email');
        }

        // --- Attempt 1: Admin guard (Super Admin + Admin) with active status check ---
        $adminCredentials = array_merge($credentials, ['status' => 'active']);
        if (Auth::guard('admin')->attempt($adminCredentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $role = Auth::guard('admin')->user()->role;
            if ($role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('super_admin.dashboard'));
        }

        // --- Attempt 2: Web guard (Client users in the `users` table) with active status check ---
        $clientCredentials = array_merge($credentials, ['status' => 'active']);
        if (Auth::guard('web')->attempt($clientCredentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('client.dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Logout from whichever guard is currently active
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
