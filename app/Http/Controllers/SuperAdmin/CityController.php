<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::withCount('hotels');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $cities = $query->latest()->paginate(10)->withQueryString();
        
        $totalCities = City::count();
        $totalHotels = \App\Models\Hotel::count();

        return view('super_admin.cities.index', compact('cities', 'totalCities', 'totalHotels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'name.required' => 'The city name is required.',
            'name.unique' => 'This city already exists.',
            'image_file.image' => 'The uploaded file must be an image.',
            'image_file.mimes' => 'The image must be a file of type: jpg, jpeg, png, webp.'
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('cities', 'public');
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
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'image_file.image' => 'The uploaded file must be an image.',
            'image_file.mimes' => 'The image must be a file of type: jpg, jpeg, png, webp.'
        ]);

        $imagePath = $city->image;

        if ($request->hasFile('image_file')) {
            if ($city->image && !Str::startsWith($city->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($city->image);
            }
            $imagePath = $request->file('image_file')->store('cities', 'public');
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
        if ($city->image && !Str::startsWith($city->image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($city->image);
        }
        
        $city->delete();
        return redirect()->route('super_admin.cities.index')->with('success', 'City deleted successfully.');
    }
}
