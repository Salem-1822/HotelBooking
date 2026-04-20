<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($request->has('remove_image')) {
            if ($admin->profile_image && Storage::disk('public')->exists('profiles/' . $admin->profile_image)) {
                Storage::disk('public')->delete('profiles/' . $admin->profile_image);
            }
            $admin->profile_image = null;
            $admin->save();
            return redirect()->back()->with('success', 'Profile photo removed successfully.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|min:6',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($admin->profile_image && Storage::disk('public')->exists('profiles/' . $admin->profile_image)) {
                Storage::disk('public')->delete('profiles/' . $admin->profile_image);
            }
            // Store new image
            $filename = time() . '_' . $request->file('profile_image')->getClientOriginalName();
            $request->file('profile_image')->storeAs('profiles', $filename, 'public');
            $admin->profile_image = $filename;
        }

        $admin->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
