<?php

namespace App\Http\Controllers\SuperAdmin;

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
        return view('super_admin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($request->has('remove_image')) {
            $admin->deleteProfileImageFile();
            $admin->profile_image = null;
            $admin->save();
            return redirect()->back()->with('success', 'Profile photo removed successfully.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            $newPath = $request->file('profile_image')->store('profiles', 'public');
            $admin->deleteProfileImageFile();
            $admin->profile_image = $newPath;
        }

        $admin->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
