@extends('admin.layouts.app')

@section('title', 'Manage Reservations')

@push('styles')
<style>
    .page-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
        flex-wrap: wrap;
    }
    .page-title-bar .title-group h4 {
        font-size: 1.55rem;
        letter-spacing: -0.02em;
    }
    .summary-card {
        border-radius: 1rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
    }
    .summary-card .card-body {
        min-height: 120px;
    }
    .reservation-table tbody tr:hover {
        background: rgba(15, 23, 42, 0.02);
    }
    .reservation-table td,
    .reservation-table th {
        vertical-align: middle;
    }


    .modal {
        z-index: 9999 !important;
    }
    .modal-backdrop {
        z-index: 9998 !important;
    }
    .modal-content {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
</style>
@endpush

@section('content')
<div class="page-title-bar mb-4">
    <div class="title-group">
        <h4 class="fw-bold mb-1">Manage Reservations</h4>
        <p class="mb-0 text-muted">Manage guest bookings, status changes, and reservation details.</p>
    </div>
    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addReservationModal">
        <i class="bi bi-plus-lg me-2"></i> Add Reservation
    </button>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Total Reservations</p>
                <h3 class="mb-1 fw-bold counter" data-count="{{ $totalReservations }}">0</h3>
                <span class="badge bg-light text-dark">All status</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Pending</p>
                <h3 class="mb-1 fw-bold counter text-warning" data-count="{{ $pendingCount }}">0</h3>
                <span class="badge bg-warning bg-opacity-10 text-warning">Action Required</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Confirmed</p>
                <h3 class="mb-1 fw-bold counter text-success" data-count="{{ $confirmedCount }}">0</h3>
                <span class="badge bg-success bg-opacity-10 text-success">Approved</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Cancelled</p>
                <h3 class="mb-1 fw-bold counter text-danger" data-count="{{ $cancelledCount }}">0</h3>
                <span class="badge bg-danger bg-opacity-10 text-danger">Revoked</span>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reservations.index') }}" method="GET" id="filterForm">
            <div class="row gx-3 gy-3 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" 
                               placeholder="Search guest name, phone, room number..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <select name="status" class="form-select bg-light border-0">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <input type="date" name="date" class="form-control bg-light border-0" value="{{ request('date') }}" title="Filter by Check-in Date">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-light border"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- Data Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 reservation-table">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4">Reservation Ref</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Booking Dates</th>
                    <th>Guests</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $res)
                <tr>
                    <td class="ps-4">
                        <span class="fw-bold text-dark">#MOR-RSV-{{ $res->id }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $res->guest_name }}</div>
                        <small class="text-muted"><i class="bi bi-telephone text-primary me-1"></i> {{ $res->guest_phone }}</small>
                    </td>
                    <td>
                        @if($res->room)
                            <div class="fw-semibold text-dark">Room {{ $res->room->room_number }}</div>
                            <small class="text-muted">{{ $res->room->type }} ({{ number_format($res->room->price_per_night, 0) }} MAD/night)</small>
                        @else
                            <span class="text-danger small">No Room Assigned</span>
                        @endif
                    </td>
                    <td>
                        <div class="small fw-semibold text-dark">
                            {{ $res->check_in ? \Carbon\Carbon::parse($res->check_in)->format('d M Y') : 'N/A' }} 
                            <i class="bi bi-arrow-right mx-1 text-muted"></i> 
                            {{ $res->check_out ? \Carbon\Carbon::parse($res->check_out)->format('d M Y') : 'N/A' }}
                        </div>
                        <small class="text-muted">
                            {{ $res->check_in && $res->check_out ? \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out)) . ' Nights' : 'N/A' }}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $res->guests_count }}</span>
                    </td>
                    <td>
                        <span class="fw-bold text-primary">{{ number_format($res->total_price, 0) }} MAD</span>
                    </td>
                    <td>
                        @php
                            $badgeClass = match($res->status) {
                                'confirmed'   => 'bg-success bg-opacity-10 text-success',
                                'checked_in'  => 'bg-primary bg-opacity-10 text-primary',
                                'checked_out' => 'bg-secondary bg-opacity-10 text-secondary',
                                'pending'     => 'bg-warning bg-opacity-10 text-warning',
                                'cancelled'   => 'bg-danger bg-opacity-10 text-danger',
                                default       => 'bg-light text-dark',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2.5 py-1.5 fw-bold">
                            {{ $statusOptions[$res->status] ?? ucfirst($res->status) }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-dark px-3 mb-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#viewModal{{ $res->id }}">
                            <i class="bi bi-eye-fill me-1"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-primary px-3 mb-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal{{ $res->id }}">
                            <i class="bi bi-pencil-fill me-1"></i> Edit
                        </button>
                        <form action="{{ route('admin.reservations.destroy', $res) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this reservation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-3 mb-1">
                                <i class="bi bi-trash-fill me-1"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- View Details Modal --}}
                <div class="modal fade text-start" id="viewModal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0 bg-light p-4">
                                <h5 class="modal-title fw-bold text-dark">Reservation Details #MOR-RSV-{{ $res->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small mb-1 text-uppercase ls-1" style="font-size:0.7rem;">Status</p>
                                        <span class="badge {{ $badgeClass }} px-2.5 py-1.5 fw-bold">
                                            {{ $statusOptions[$res->status] ?? ucfirst($res->status) }}
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted small mb-1 text-uppercase ls-1" style="font-size:0.7rem;">Total Cost</p>
                                        <h4 class="fw-bold text-primary mb-0">{{ number_format($res->total_price, 0) }} MAD</h4>
                                    </div>
                                </div>
                                <hr class="my-3 opacity-50">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Guest Name</label>
                                        <span class="text-dark fw-semibold">{{ $res->guest_name }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Guest Phone</label>
                                        <span class="text-dark fw-semibold">{{ $res->guest_phone }}</span>
                                    </div>
                                    @if($res->customer && $res->customer->email)
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Guest Email</label>
                                        <span class="text-dark fw-semibold">{{ $res->customer->email }}</span>
                                    </div>
                                    @endif
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Room Number</label>
                                        <span class="text-dark fw-semibold">Room {{ $res->room?->room_number ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Room Type</label>
                                        <span class="text-dark fw-semibold">{{ $res->room?->type ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Check-in Date</label>
                                        <span class="text-dark fw-semibold">{{ $res->check_in }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Check-out Date</label>
                                        <span class="text-dark fw-semibold">{{ $res->check_out }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Nights Count</label>
                                        <span class="text-dark fw-semibold">{{ $res->check_in && $res->check_out ? \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out)) : 'N/A' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Guests Count</label>
                                        <span class="text-dark fw-semibold">{{ $res->guests_count }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit Reservation Modal --}}
                <div class="modal fade text-start" id="editModal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('admin.reservations.update', $res) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0 bg-light p-4">
                                    <h5 class="modal-title fw-bold text-dark">Edit Reservation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-bold">Select Customer</label>
                                        <select name="customer_id" class="form-select customer-select">
                                            <option value="">-- New / Custom Guest Details --</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" 
                                                    {{ $res->customer_id == $customer->id ? 'selected' : '' }}
                                                    data-name="{{ $customer->name }}" 
                                                    data-phone="{{ $customer->phone }}" 
                                                    data-email="{{ $customer->email ?? '—' }}">
                                                    {{ $customer->name }} ({{ $customer->phone }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="customer-preview card bg-light border-0 mb-3 {{ $res->customer_id ? '' : 'd-none' }}">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-2">Customer Details</h6>
                                            <p class="mb-1"><i class="bi bi-person text-primary me-2"></i><span class="preview-name">{{ $res->customer?->name }}</span></p>
                                            <p class="mb-1"><i class="bi bi-telephone text-primary me-2"></i><span class="preview-phone">{{ $res->customer?->phone }}</span></p>
                                            <p class="mb-0"><i class="bi bi-envelope text-primary me-2"></i><span class="preview-email">{{ $res->customer?->email ?? '—' }}</span></p>
                                        </div>
                                    </div>
                                    <div class="new-customer-fields {{ $res->customer_id ? 'd-none' : '' }}">
                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-bold">Guest Name</label>
                                            <input type="text" name="guest_name" class="form-control guest-name-input" value="{{ $res->guest_name }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-bold">Guest Phone</label>
                                            <input type="text" name="guest_phone" class="form-control guest-phone-input" value="{{ $res->guest_phone }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-bold">Guest Email (Optional)</label>
                                            <input type="email" name="guest_email" class="form-control guest-email-input" value="">
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <label class="form-label text-dark fw-bold">Room</label>
                                            <select name="room_id" class="form-select" required>
                                                @foreach($rooms as $room)
                                                    <option value="{{ $room->id }}" {{ $res->room_id == $room->id ? 'selected' : '' }}>
                                                        Room {{ $room->room_number }} ({{ $room->type }} - {{ number_format($room->price_per_night, 0) }} MAD)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-dark fw-bold">Guests Count</label>
                                            <input type="number" name="guests_count" class="form-control" value="{{ $res->guests_count }}" min="1" required>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <label class="form-label text-dark fw-bold">Check-in</label>
                                            <input type="date" name="check_in" class="form-control" value="{{ $res->check_in }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-dark fw-bold">Check-out</label>
                                            <input type="date" name="check_out" class="form-control" value="{{ $res->check_out }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-bold">Status</label>
                                        <select name="status" class="form-select" required>
                                            @foreach($statusOptions as $val => $label)
                                                <option value="{{ $val }}" {{ $res->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 p-4 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar2-x fs-1 d-block mb-3 opacity-50"></i>
                        No reservations found matching the criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $reservations->links() }}
</div>

{{-- Add Reservation Modal --}}
<div class="modal fade" id="addReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.reservations.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="modal-title fw-bold text-dark">Add Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Select Customer</label>
                        <select name="customer_id" class="form-select customer-select">
                            <option value="">-- Create New Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    data-name="{{ $customer->name }}" 
                                    data-phone="{{ $customer->phone }}" 
                                    data-email="{{ $customer->email ?? '—' }}">
                                    {{ $customer->name }} ({{ $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="customer-preview card bg-light border-0 mb-3 d-none">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2">Customer Details</h6>
                            <p class="mb-1"><i class="bi bi-person text-primary me-2"></i><span class="preview-name"></span></p>
                            <p class="mb-1"><i class="bi bi-telephone text-primary me-2"></i><span class="preview-phone"></span></p>
                            <p class="mb-0"><i class="bi bi-envelope text-primary me-2"></i><span class="preview-email"></span></p>
                        </div>
                    </div>
                    <div class="new-customer-fields">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Guest Name</label>
                            <input type="text" name="guest_name" class="form-control guest-name-input" placeholder="John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Guest Phone</label>
                            <input type="text" name="guest_phone" class="form-control guest-phone-input" placeholder="+212 600-000000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Guest Email (Optional)</label>
                            <input type="email" name="guest_email" class="form-control guest-email-input" placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-dark fw-bold">Room</label>
                            <select name="room_id" class="form-select" required>
                                <option value="" disabled selected>Select Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">
                                        Room {{ $room->room_number }} ({{ $room->type }} - {{ number_format($room->price_per_night, 0) }} MAD)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dark fw-bold">Guests Count</label>
                            <input type="number" name="guests_count" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-dark fw-bold">Check-in</label>
                            <input type="date" name="check_in" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dark fw-bold">Check-out</label>
                            <input type="date" name="check_out" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach($createStatusOptions as $val => $label)
                                <option value="{{ $val }}" {{ $val === 'pending' ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Reservation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle Customer Select changes
        document.querySelectorAll('.customer-select').forEach(select => {
            select.addEventListener('change', function() {
                const modal = this.closest('.modal-body');
                const preview = modal.querySelector('.customer-preview');
                const newFields = modal.querySelector('.new-customer-fields');
                
                if (this.value) {
                    // Existing customer selected
                    const option = this.options[this.selectedIndex];
                    preview.querySelector('.preview-name').textContent = option.dataset.name;
                    preview.querySelector('.preview-phone').textContent = option.dataset.phone;
                    preview.querySelector('.preview-email').textContent = option.dataset.email;
                    
                    preview.classList.remove('d-none');
                    newFields.classList.add('d-none');
                    
                    // Disable required on inputs so form can submit
                    modal.querySelectorAll('.new-customer-fields input').forEach(input => input.removeAttribute('required'));
                } else {
                    // New customer
                    preview.classList.add('d-none');
                    newFields.classList.remove('d-none');
                    
                    // Enable required on name and phone
                    modal.querySelector('.guest-name-input').setAttribute('required', 'required');
                    modal.querySelector('.guest-phone-input').setAttribute('required', 'required');
                }
            });
            // Trigger change event to set initial state correctly
            select.dispatchEvent(new Event('change'));
        });

        // Animate Counters
        document.querySelectorAll('.counter').forEach(el => {
            const target = Number(el.dataset.count || 0);
            let current = 0;
            const step = Math.max(1, Math.round(target / 30));
            const interval = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(interval);
                }
                el.textContent = current;
            }, 16);
        });
    });
</script>
@endpush
