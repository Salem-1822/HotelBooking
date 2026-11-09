<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the dedicated Super Admin login form.
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            $role = Auth::guard('admin')->user()->role;
            if ($role === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            }
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }

        return view('super_admin.auth.login');
    }

    /**
     * Handle a login request to the Super Admin portal.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
            ]);
            if ($message === 'auth.throttle') {
                $message = "Too many login attempts. Please try again in {$seconds} seconds.";
            }
            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        // Attempt authentication via the admin guard for active accounts only
        $adminCredentials = array_merge($credentials, ['status' => 'active']);
        if (Auth::guard('admin')->attempt($adminCredentials, $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();

            // Strict role verification: only super_admin role allowed
            if ($user->role !== 'super_admin') {
                Auth::guard('admin')->logout();
                RateLimiter::hit($throttleKey, 60);

                return back()->withErrors([
                    'email' => 'Access denied. Only Super Administrators can log in through this portal.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Super Admin must ALWAYS be redirected directly to Super Admin dashboard
            return redirect()->route('super_admin.dashboard');
        }

        // Specific failure messages for better feedback
        $inactiveAccount = Admin::where('email', $credentials['email'])
            ->where('role', 'super_admin')
            ->where('status', '!=', 'active')
            ->first();

        if ($inactiveAccount && Hash::check($credentials['password'], $inactiveAccount->password)) {
            return back()->withErrors([
                'email' => 'Your Super Administrator account is currently inactive. Please contact system support.',
            ])->onlyInput('email');
        }

        // Check if a regular admin or client tried to authenticate here
        $regularAdmin = Admin::where('email', $credentials['email'])
            ->where('role', 'admin')
            ->first();

        if ($regularAdmin && Hash::check($credentials['password'], $regularAdmin->password)) {
            return back()->withErrors([
                'email' => 'Access denied. Regular Administrators must log in through the Admin Portal.',
            ])->onlyInput('email');
        }

        $clientUser = User::where('email', $credentials['email'])->first();
        if ($clientUser && Hash::check($credentials['password'], $clientUser->password)) {
            return back()->withErrors([
                'email' => 'Access denied. Client accounts cannot access the Super Admin Portal.',
            ])->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the Super Admin out of the application.
     */
    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super_admin.login');
    }
}
