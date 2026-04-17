<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::withCount(['hotels', 'city'])->latest()->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    public function show($id)
    {
        $admin = User::with(['hotels.reservations', 'city'])->findOrFail($id);
        
        // Calculate basic stats
        $stats = [
            'total_hotels' => $admin->hotels->count(),
            'total_reservations' => $admin->hotels->sum(fn($h) => $h->reservations->count()),
            'confirmed_res' => $admin->hotels->sum(fn($h) => $h->reservations->where('status', 'confirmed')->count()),
            'cancelled_res' => $admin->hotels->sum(fn($h) => $h->reservations->where('status', 'cancelled')->count()),
        ];

        return view('admin.admins.show', compact('admin', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully.');
    }

    public function update(Request $request, User $admin)
    {
        $admin->update($request->only('name', 'email', 'status'));
        if ($request->password) {
            $admin->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'Admin updated successfully.');
    }

    public function destroy(User $admin)
    {
        $admin->delete();
        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully.');
    }

    public function exportPDF()
    {
        $users = User::all();
        $headers = ['ID', 'Name', 'Email', 'Joined At'];
        $data = $users->map(fn($u) => [$u->id, $u->name, $u->email, $u->created_at]);
        $title = 'System Admins';

        $pdf = Pdf::loadView('admin.exports.pdf', compact('headers', 'data', 'title'));
        return $pdf->download('admins_report.pdf');
    }
}
