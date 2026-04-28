<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::with('city')->latest()->paginate(10);
        $cities = City::all();
        return view('super_admin.hotels.index', compact('hotels', 'cities'));
    }

    public function show(Hotel $hotel)
    {
        return view('super_admin.hotels.show', compact('hotel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string',
            'status' => 'required|in:active,inactive',
            // Added validation for price_per_night to fix the 1364 default value error
            'price_per_night' => 'required|numeric|min:0',
        ]);

        Hotel::create($request->all());

        return redirect()->route('super_admin.hotels.index')->with('success', 'Hotel created successfully.');
    }

    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string',
            'status' => 'required|in:active,inactive',
            // Added validation for price_per_night
            'price_per_night' => 'required|numeric|min:0',
        ]);

        $hotel->update($request->all());

        return redirect()->route('super_admin.hotels.index')->with('success', 'Hotel updated successfully.');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('super_admin.hotels.index')->with('success', 'Hotel deleted successfully.');
    }

    public function exportPDF()
    {
        $hotels = Hotel::with('city')->get();
        $headers = ['ID', 'Hotel Name', 'City', 'Address', 'Status'];
        $data = $hotels->map(fn($h) => [$h->id, $h->name, $h->city->name, $h->address, $h->status]);
        $title = 'Hotels';

        $pdf = Pdf::loadView('super_admin.exports.pdf', compact('headers', 'data', 'title'));
        return $pdf->download('hotels_report.pdf');
    }
}
