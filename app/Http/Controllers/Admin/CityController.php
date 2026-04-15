<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::withCount('hotels')->latest()->get();
        return view('admin.cities.index', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            'image' => 'nullable|string'
        ]);

        City::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $request->image
        ]);

        return redirect()->route('admin.cities.index')->with('success', 'City created successfully.');
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            'image' => 'nullable|string'
        ]);

        $city->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $request->image
        ]);

        return redirect()->route('admin.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->route('admin.cities.index')->with('success', 'City deleted successfully.');
    }

    public function exportPDF()
    {
        $cities = City::all();
        $headers = ['ID', 'Name', 'Slug', 'Hotels Count', 'Created At'];
        $data = $cities->map(fn($c) => [$c->id, $c->name, $c->slug, $c->hotels()->count(), $c->created_at]);
        $title = 'Cities';

        $pdf = Pdf::loadView('admin.exports.pdf', compact('headers', 'data', 'title'));
        return $pdf->download('cities_report.pdf');
    }
}
