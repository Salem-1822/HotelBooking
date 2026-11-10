<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the administrators.
     */
    public function index(Request $request)
    {
        $query = Admin::with(['city', 'hotel']);

        // Implementation of search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $admins = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $totalAdmins = Admin::count();
        $adminsWithCity = Admin::whereNotNull('city_id')->count();
        $cities = City::with('hotels')->orderBy('name', 'asc')->get();

        return view('super_admin.admins.index', compact('admins', 'totalAdmins', 'adminsWithCity', 'cities'));
    }

    /**
     * Store a newly created administrator in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8',
            'city_id' => 'required|exists:cities,id',
            'hotel_id' => [
                'required',
                Rule::exists('hotels', 'id')->where(function ($query) use ($request) {
                    return $query->where('city_id', $request->city_id);
                }),
            ],
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'image_file.image' => 'The uploaded file must be an image.',
            'image_file.mimes' => 'The image must be a file of type: jpg, jpeg, png, webp.'
        ]);

        $profileImage = null;
        if ($request->hasFile('image_file')) {
            $profileImage = $request->file('image_file')->store('profiles', 'public');
        }

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_image' => $profileImage,
            'city_id' => $request->city_id,
            'hotel_id' => $request->hotel_id,
            'role' => 'admin', // default role
            'status' => 'active'
        ]);

        return redirect()->route('super_admin.admins.index')->with('success', 'Administrator created successfully.');
    }

    /**
     * Update the specified administrator in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        // Prevent Super Admin from blocking their own account
        if (Auth::guard('admin')->id() === $admin->id && $request->input('status') === 'blocked') {
            return redirect()->route('super_admin.admins.index')->withErrors(['status' => 'You cannot block your own Super Admin account.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($admin->id)],
            'password' => 'nullable|string|min:8',
            'city_id' => 'required|exists:cities,id',
            'hotel_id' => [
                'required',
                Rule::exists('hotels', 'id')->where(function ($query) use ($request) {
                    return $query->where('city_id', $request->city_id);
                }),
            ],
            'status' => ['nullable', Rule::in(['active', 'blocked'])],
            'blocked_reason' => ['nullable', 'string', 'required_if:status,blocked', 'max:1000'],
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'blocked_reason.required_if' => 'Le motif du blocage est obligatoire lorsque le compte est bloqué.',
            'image_file.image' => 'The uploaded file must be an image.',
            'image_file.mimes' => 'The image must be a file of type: jpg, jpeg, png, webp.'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'city_id' => $request->city_id,
            'hotel_id' => $request->hotel_id,
        ];

        // Handle Account Status (blocking / unblocking)
        if ($request->filled('status')) {
            if ($request->status === 'blocked') {
                $data['is_blocked'] = true;
                $data['status'] = 'blocked';
                $data['blocked_reason'] = $request->blocked_reason;
                if (! $admin->is_blocked) {
                    $data['blocked_at'] = now();
                }

                // Immediately revoke active sessions for this administrator
                if (Schema::hasTable('sessions')) {
                    DB::table('sessions')->where('user_id', $admin->id)->delete();
                }
            } else {
                // Unblocking process
                $data['is_blocked'] = false;
                $data['status'] = 'active';
                $data['blocked_reason'] = null;
                $data['blocked_at'] = null;
            }
        }

        // Only update password if a new one is provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle profile image update
        if ($request->hasFile('image_file')) {
            $newImage = $request->file('image_file')->store('profiles', 'public');
            $admin->deleteProfileImageFile();
            $data['profile_image'] = $newImage;
        }

        $admin->update($data);

        $message = ($request->status === 'blocked') 
            ? 'Administrator account has been blocked.' 
            : 'Administrator updated successfully.';

        return redirect()->route('super_admin.admins.index')->with('success', $message);
    }

    /**
     * Remove the specified administrator from storage.
     */
    public function destroy(Admin $admin)
    {
        // Prevent deletion of the currently logged-in super admin
        if (Auth::guard('admin')->id() === $admin->id) {
            return redirect()->route('super_admin.admins.index')->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $admin->deleteProfileImageFile();

        $admin->delete();

        return redirect()->route('super_admin.admins.index')->with('success', 'Administrator deleted successfully.');
    }
}
