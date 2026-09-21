<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Customer;

class ProfileController extends Controller
{
    /**
     * Show the client profile edit form.
     */
    public function edit()
    {
        $user = Auth::guard('web')->user();
        return view('client.profile.edit', compact('user'));
    }

    /**
     * Update the client's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        // We normalize the phone here to ensure it's stored in a clean format,
        // which helps when later creating Customer records during booking.
        if (!empty($validated['phone'])) {
            $validated['phone'] = Customer::normalizePhone($validated['phone']);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('client.profile.edit')->with('success', 'Profile information updated successfully.');
    }

    /**
     * Update the client's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('web')->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('client.profile.edit')->with('success', 'Password updated successfully.');
    }
}
