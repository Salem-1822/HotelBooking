<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminUserController extends Controller
{
    public function index()
    {
        // Use the scope to show only "normal" admins consistently
        $admins = Admin::with('city.hotels')
            ->visibleAdmins()
            ->latest()
            ->paginate(10);
            
        return view('super_admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $cities = City::orderBy('name')->get();
        return view('super_admin.admins.create', compact('cities'));
    }

    public function show($id)
    {
        $admin = Admin::with(['city.hotels.reservations'])->findOrFail($id);
        
        // Calculate basic stats
        $hotels = $admin->city ? $admin->city->hotels : collect();
        
        $stats = [
            'total_hotels' => $hotels->count(),
            'total_reservations' => $hotels->sum(fn($h) => $h->reservations->count()),
            'confirmed_res' => $hotels->sum(fn($h) => $h->reservations->where('status', 'confirmed')->count()),
            'cancelled_res' => $hotels->sum(fn($h) => $h->reservations->where('status', 'cancelled')->count()),
        ];

        return view('super_admin.admins.show', compact('admin', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'city_id' => 'required|exists:cities,id'
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'city_id' => $request->city_id,
            'role' => 'admin',
            'status' => 'active'
        ]);

        return redirect()->route('super_admin.admins.index')->with('success', 'Admin created successfully and assigned to city.');
    }

    public function update(Request $request, Admin $admin)
    {
        $admin->update($request->only('name', 'email', 'status'));
        if ($request->password) {
            $admin->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'Admin updated successfully.');
    }

    public function destroy(Admin $admin)
    {
        // 1. Prevent deleting self
        if (Auth::guard('admin')->id() === $admin->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // 2. Prevent deleting the last super admin
        if ($admin->role === 'super_admin') {
            $superAdminCount = Admin::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->back()->with('error', 'The system must have at least one Super Admin.');
            }
        }

        $admin->delete();
        return redirect()->route('super_admin.admins.index')->with('success', 'Admin deleted successfully.');
    }

    public function exportPDF()
    {
        $users = Admin::visibleAdmins()->get();
        $headers = ['ID', 'Name', 'Email', 'Joined At'];
        $data = $users->map(fn($u) => [$u->id, $u->name, $u->email, $u->created_at]);
        $title = 'System Admins';

        $pdf = Pdf::loadView('admin.exports.pdf', compact('headers', 'data', 'title'));
        return $pdf->download('admins_report.pdf');
    }
}
