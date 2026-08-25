<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the Admin's profile page.
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        // Eager-load the assigned hotel and its city for the reference card.
        $admin->load('hotel.city');

        return view('admin.profile.index', compact('admin'));
    }

    /**
     * Update the Admin's personal information (name, email, phone, profile photo).
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'phone'         => 'nullable|string|max:50',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $admin->name  = $request->name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;

        if ($request->hasFile('profile_image')) {
            // Delete the old image if it exists.
            if ($admin->profile_image && Storage::disk('public')->exists('profiles/' . $admin->profile_image)) {
                Storage::disk('public')->delete('profiles/' . $admin->profile_image);
            }
            // Store the new image in storage/profiles/.
            $filename = time() . '_' . $request->file('profile_image')->getClientOriginalName();
            $request->file('profile_image')->storeAs('profiles', $filename, 'public');
            $admin->profile_image = $filename;
        }

        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the Admin's password.
     * Requires the current password for verification before allowing a change.
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        // Verify the current password before allowing a change.
        if (! Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors([
                'current_password' => 'The current password you entered is incorrect.',
            ])->withInput()->with('password_section', true);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully.');
    }

    /**
     * Remove the Admin's profile photo.
     */
    public function removePhoto(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin->profile_image && Storage::disk('public')->exists('profiles/' . $admin->profile_image)) {
            Storage::disk('public')->delete('profiles/' . $admin->profile_image);
        }

        $admin->profile_image = null;
        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profile photo removed.');
    }
}
