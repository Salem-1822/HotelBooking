<?php

namespace App\Http\Controllers\Admin;

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
     * Show the dedicated Regular Admin login form.
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            if ($admin->role === 'admin' && ($admin->is_blocked || $admin->status === 'blocked')) {
                $adminId = $admin->id;
                Auth::guard('admin')->logout();
                session()->invalidate();
                session()->regenerateToken();
                session(['blocked_admin_id' => $adminId]);
                return redirect()->route('admin.blocked');
            }
            if ($admin->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('super_admin.dashboard');
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle a login request to the Regular Admin portal.
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

        // Check if regular administrator account exists and is blocked
        $adminAccount = Admin::where('email', $credentials['email'])
            ->where('role', 'admin')
            ->first();

        if ($adminAccount && ($adminAccount->is_blocked || $adminAccount->status === 'blocked')) {
            if (Hash::check($credentials['password'], $adminAccount->password)) {
                $request->session()->put('blocked_admin_id', $adminAccount->id);
                return redirect()->route('admin.blocked');
            }
        }

        // Attempt authentication via the admin guard for active, unblocked accounts only
        $adminCredentials = array_merge($credentials, [
            'status' => 'active',
            'is_blocked' => false,
        ]);
        if (Auth::guard('admin')->attempt($adminCredentials, $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();

            // Strict role verification: only regular admin role allowed
            if ($user->role !== 'admin') {
                Auth::guard('admin')->logout();
                RateLimiter::hit($throttleKey, 60);

                return back()->withErrors([
                    'email' => 'Access denied. Super Administrators must log in through the Super Admin portal.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        // Specific failure messages
        $inactiveAccount = Admin::where('email', $credentials['email'])
            ->where('role', 'admin')
            ->where('status', '!=', 'active')
            ->first();

        if ($inactiveAccount && Hash::check($credentials['password'], $inactiveAccount->password)) {
            return back()->withErrors([
                'email' => 'Your Administrator account is currently inactive. Please contact your Super Administrator.',
            ])->onlyInput('email');
        }

        $superAdmin = Admin::where('email', $credentials['email'])
            ->where('role', 'super_admin')
            ->first();

        if ($superAdmin && Hash::check($credentials['password'], $superAdmin->password)) {
            return back()->withErrors([
                'email' => 'Access denied. Super Administrators must log in through the Super Admin portal.',
            ])->onlyInput('email');
        }

        $clientUser = User::where('email', $credentials['email'])->first();
        if ($clientUser && Hash::check($credentials['password'], $clientUser->password)) {
            return back()->withErrors([
                'email' => 'Access denied. Client accounts cannot access the Admin Portal.',
            ])->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the Admin out of the application.
     */
    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Display the dedicated blocked-account information page.
     */
    public function showBlocked(Request $request)
    {
        $adminId = $request->session()->get('blocked_admin_id') ?? $request->query('admin_id');

        if (! $adminId) {
            return redirect()->route('admin.login');
        }

        $admin = Admin::find($adminId);

        if (! $admin || (! $admin->is_blocked && $admin->status !== 'blocked')) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.blocked', [
            'admin' => $admin,
            'adminName' => $admin->name,
            'blockedReason' => $admin->blocked_reason ?? 'Aucun motif spécifié par le Super Administrateur.',
            'blockedAt' => $admin->blocked_at,
        ]);
    }
}

