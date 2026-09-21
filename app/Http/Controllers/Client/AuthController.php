<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        // Redirect to the shared login page with a success message
        return redirect()->route('login')
            ->with('success', 'Account created successfully! Please log in to continue.');
    }
}
