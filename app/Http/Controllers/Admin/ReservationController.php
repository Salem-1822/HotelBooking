<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Initialize query with eager loading
        $query = Reservation::with(['hotel.city']);

        // 2. Apply Dynamic Filters (Search removed)
        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by City (via Hotel bridge)
        if ($request->filled('city_id')) {
            $query->whereHas('hotel', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        // Filter by Hotel
        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // 3. Finalize with sorting and pagination
        $reservations = $query->latest()->paginate(12)->withQueryString();
        
        $cities = \App\Models\City::orderBy('name')->get();
        $hotels = \App\Models\Hotel::orderBy('name')->get();

        return view('admin.reservations.index', compact('reservations', 'cities', 'hotels'));
    }

    public function exportPDF()
    {
        $reservations = Reservation::with('hotel')->get();
        $headers = ['ID', 'Guest', 'Hotel', 'Total Amount', 'Status', 'Booking Dates'];
        $data = $reservations->map(fn($r) => [
            '#MOR-' . $r->id, 
            $r->guest_name, 
            $r->hotel->name, 
            number_format($r->total_price, 0) . ' MAD', 
            ucfirst($r->status), 
            $r->check_in . ' to ' . $r->check_out
        ]);
        $title = 'Morocco Reservations';

        $pdf = Pdf::loadView('admin.exports.pdf', compact('headers', 'data', 'title'));
        return $pdf->download('moroccan_reservations_report.pdf');
    }
}
