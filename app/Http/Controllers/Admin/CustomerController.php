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

        // --- Dashboard-style summary cards via database aggregates ---
        $baseCustomers = Customer::where('hotel_id', $hotelId);
        $totalCustomers = (clone $baseCustomers)->count();

        // Count new (exactly 1 reservation) and returning (2+ reservations)
        $reservationCounts = Customer::where('customers.hotel_id', $hotelId)
            ->join('reservations', 'reservations.customer_id', '=', 'customers.id')
            ->selectRaw('customers.id, COUNT(reservations.id) as res_count')
            ->groupBy('customers.id')
            ->pluck('res_count', 'id');

        $newCustomers       = $reservationCounts->filter(fn ($cnt) => $cnt === 1)->count();
        $returningCustomers = $reservationCounts->filter(fn ($cnt) => $cnt >= 2)->count();

        // Active customers: having at least one active reservation
        $activeCustomers = Customer::where('hotel_id', $hotelId)
            ->whereHas('reservations', fn ($q) => $q->whereIn('status', self::ACTIVE_STATUSES))
            ->count();

        // Load paginated customers for this hotel (15 per page)
        // Attach subquery for latest reservation check_in date to avoid loading all reservations for list view
        $customers = Customer::where('hotel_id', $hotelId)
            ->withCount('reservations')
            ->withCount(['reservations as active_reservations_count' => function ($q) {
                $q->whereIn('status', self::ACTIVE_STATUSES);
            }])
            ->addSelect([
                'last_reservation_date' => \App\Models\Reservation::select('check_in')
                    ->whereColumn('customer_id', 'customers.id')
                    ->orderByDesc('check_in')
                    ->limit(1),
            ])
            ->with(['reservations' => function ($q) {
                $q->orderByDesc('check_in')->with('room');
            }])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

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
        if (!$hotelId || $customer->hotel_id !== $hotelId) {
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

        if (!$hotelId || $customer->hotel_id !== $hotelId) {
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

        if (!$hotelId || $customer->hotel_id !== $hotelId) {
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
