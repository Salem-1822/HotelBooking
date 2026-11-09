<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the client registration form.
     * Redirect to client dashboard if already authenticated.
     */
    public function showRegisterForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }

        if (Auth::guard('admin')->check()) {
            $role = Auth::guard('admin')->user()->role;
            return $role === 'super_admin'
                ? redirect()->route('super_admin.dashboard')
                : redirect()->route('admin.dashboard');
        }

        return view('client.auth.register');
    }

    /**
     * Handle the client registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'         => 'Please enter your full name.',
            'name.max'              => 'Name may not exceed 255 characters.',
            'email.required'        => 'Please enter your email address.',
            'email.email'           => 'Please enter a valid email address.',
            'email.unique'          => 'An account with this email already exists.',
            'password.required'     => 'Please enter a password.',
            'password.min'          => 'Password must be at least 8 characters.',
            'password.confirmed'    => 'Passwords do not match.',
        ]);

        // Create the client account in the users table
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'client',
            'status'   => 'active',
        ]);

        // Redirect to the login page with a success message
        return redirect()->route('login')
            ->with('success', 'Account created successfully! Please log in to continue.');
    }

    /**
     * Show the dedicated Client login form.
     */
    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }

        if (Auth::guard('admin')->check()) {
            $role = Auth::guard('admin')->user()->role;
            return $role === 'super_admin'
                ? redirect()->route('super_admin.dashboard')
                : redirect()->route('admin.dashboard');
        }

        return view('client.auth.login');
    }

    /**
     * Handle a client login request.
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

        // Attempt authentication via the web guard for active clients only
        $clientCredentials = array_merge($credentials, ['status' => 'active']);
        if (Auth::guard('web')->attempt($clientCredentials, $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();

            // Strict role verification: only client role allowed
            if ($user->role !== 'client') {
                Auth::guard('web')->logout();
                RateLimiter::hit($throttleKey, 60);

                return back()->withErrors([
                    'email' => 'Access denied. Administrators must log in through their dedicated portal.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->route('client.dashboard');
        }

        // Inactive account check
        $inactiveClient = User::where('email', $credentials['email'])
            ->where('status', '!=', 'active')
            ->first();

        if ($inactiveClient && Hash::check($credentials['password'], $inactiveClient->password)) {
            return back()->withErrors([
                'email' => 'Your account is currently inactive. Please contact customer support.',
            ])->onlyInput('email');
        }

        // Check if an admin attempted to login through the client portal
        $adminAccount = Admin::where('email', $credentials['email'])->first();
        if ($adminAccount && Hash::check($credentials['password'], $adminAccount->password)) {
            return back()->withErrors([
                'email' => 'Access denied. Administrators must log in through their dedicated portal.',
            ])->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the client out of the application.
     */
    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
