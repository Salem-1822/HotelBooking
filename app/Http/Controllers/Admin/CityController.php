<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::withCount('hotels')->latest()->get();
        return view('super_admin.cities.index', compact('cities'));
    }

    public function show($id)
    {
        $city = City::with(['hotels.reservations'])->findOrFail($id);
        return view('super_admin.cities.show', compact('city'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('cities', 'public');
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        City::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath
        ]);

        return redirect()->route('super_admin.cities.index')->with('success', 'City created successfully.');
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $imagePath = $city->image;

        if ($request->hasFile('image_file')) {
            // Delete old file if it's local
            if ($city->image && !Str::startsWith($city->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($city->image);
            }
            $imagePath = $request->file('image_file')->store('cities', 'public');
        } elseif ($request->filled('image_url')) {
            // Delete old file if switching to URL
            if ($city->image && !Str::startsWith($city->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($city->image);
            }
            $imagePath = $request->image_url;
        }

        $city->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath
        ]);

        return redirect()->route('super_admin.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->route('super_admin.cities.index')->with('success', 'City deleted successfully.');
    }

    public function exportPDF()
    {
        $cities = City::all();
        $headers = ['ID', 'Name', 'Slug', 'Hotels Count', 'Created At'];
        $data = $cities->map(fn($c) => [$c->id, $c->name, $c->slug, $c->hotels()->count(), $c->created_at]);
        $title = 'Cities';

        $pdf = Pdf::loadView('super_admin.exports.pdf', compact('headers', 'data', 'title'));
        return $pdf->download('cities_report.pdf');
    }
}
