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
        $query = Reservation::with('hotel')->latest();
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $reservations = $query->paginate(10);
        return view('admin.reservations.index', compact('reservations'));
    }

    public function exportPDF()
    {
        $reservations = Reservation::with('hotel')->get();
        $headers = ['ID', 'Guest', 'Hotel', 'Price', 'Status', 'Date'];
        $data = $reservations->map(fn($r) => [
            $r->id, 
            $r->guest_name, 
            $r->hotel->name, 
            $r->total_price, 
            $r->status, 
            $r->check_in . ' / ' . $r->check_out
        ]);
        $title = 'Reservations';

        $pdf = Pdf::loadView('admin.exports.pdf', compact('headers', 'data', 'title'));
        return $pdf->download('reservations_report.pdf');
    }
}
