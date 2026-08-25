<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    // Statuses that indicate a customer has an active/current booking
    private const ACTIVE_STATUSES = ['pending', 'confirmed', 'checked_in'];

    public function index(Request $request)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        if (!$hotelId) {
            return redirect()->route('login')->with('error', 'No hotel assigned.');
        }

        // Load all customers for this hotel.
        // withCount('reservations')                → total bookings per customer
        // withCount('reservations as active_...')  → bookings in active statuses
        // with(['reservations' => ...])            → latest 1 reservation for "Last Reservation" date
        //   Scoped with limit(1) per customer via a specific subrelation
        $customers = Customer::where('hotel_id', $hotelId)
            ->withCount('reservations')
            ->withCount(['reservations as active_reservations_count' => function ($q) {
                $q->whereIn('status', self::ACTIVE_STATUSES);
            }])
            ->with(['reservations' => function ($q) {
                $q->orderByDesc('check_in')->with('room');
            }])
            ->get();

        // Attach last_reservation_date as a flat property expected by the view
        foreach ($customers as $customer) {
            $customer->last_reservation_date = $customer->reservations->first()?->check_in;
        }

        // --- Dashboard-style summary cards ---
        $totalCustomers     = $customers->count();
        $newCustomers       = $customers->filter(fn ($c) => $c->reservations_count === 1)->count();
        $returningCustomers = $customers->filter(fn ($c) => $c->reservations_count >= 2)->count();
        $activeCustomers    = $customers->filter(fn ($c) => $c->active_reservations_count > 0)->count();

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'newCustomers',
            'returningCustomers',
            'activeCustomers'
        ));
    }

    /**
     * Show full customer detail (used by the View modal — returns data via redirect to index).
     * Since we use inline modals, the show route just loads the index with the target customer.
     * The actual detail is rendered via Blade modals embedded in index.blade.php.
     */
    public function show(Customer $customer)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        // Scope check: prevent viewing customers from other hotels
        if ($customer->hotel_id !== $hotelId) {
            abort(403, 'Unauthorized.');
        }

        // Load all reservations for detail view, ordered by check_in desc
        $customer->load(['reservations' => function ($q) {
            $q->orderByDesc('check_in')->with('room');
        }]);
        $customer->loadCount('reservations');

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Update the customer's name and/or phone.
     * Phone changes re-resolve customer identity (hotel_id + normalized phone).
     * Prevents creating a duplicate customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        if ($customer->hotel_id !== $hotelId) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $normalizedPhone = Customer::normalizePhone($data['phone']);

        // If phone changed, verify no other customer at this hotel already has this normalized phone
        if ($normalizedPhone !== $customer->phone) {
            $conflict = Customer::where('hotel_id', $hotelId)
                ->where('phone', $normalizedPhone)
                ->where('id', '!=', $customer->id)
                ->exists();

            if ($conflict) {
                return back()
                    ->withErrors(['phone' => 'A customer with this phone number already exists at this hotel.'])
                    ->withInput();
            }
        }

        $customer->update([
            'name'  => $data['name'],
            'phone' => $normalizedPhone,
            'email' => $data['email'] ?? null,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer \"{$customer->name}\" updated successfully.");
    }

    /**
     * Safe customer deletion.
     *
     * Strategy: If the customer has ANY reservations, we NULL out their customer_id
     * (preserving reservation history) before deleting the customer.
     * The customer_id FK is already configured as nullOnDelete in the migration,
     * so in practice the DB handles this automatically. But we also flash a
     * warning to inform the admin that reservation records remain.
     */
    public function destroy(Customer $customer)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        if ($customer->hotel_id !== $hotelId) {
            abort(403, 'Unauthorized.');
        }

        $reservationCount = $customer->reservations()->count();
        
        if ($reservationCount > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', "Cannot delete customer \"{$customer->name}\" because they have {$reservationCount} reservation(s).");
        }

        $customerName = $customer->name;
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer \"{$customerName}\" has been deleted.");
    }
}
