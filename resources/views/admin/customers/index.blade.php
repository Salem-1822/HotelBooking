@extends('admin.layouts.app')

@section('title', 'Customers')

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
    .customer-table tbody tr:hover {
        background: rgba(15, 23, 42, 0.02);
    }
    .customer-table td,
    .customer-table th {
        vertical-align: middle;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f3f4f6;
        color: #4b5563;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="page-title-bar mb-4">
    <div class="title-group">
        <h4 class="fw-bold mb-1">Customer Management</h4>
        <p class="mb-0 text-muted">View customer details, reservation history, and activity stats.</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Total Customers</p>
                    <h3 class="mb-1 fw-bold text-dark">{{ $totalCustomers }}</h3>
                </div>
                <span class="badge bg-light text-dark align-self-start">All registered</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">New Customers</p>
                    <h3 class="mb-1 fw-bold text-info">{{ $newCustomers }}</h3>
                </div>
                <span class="badge bg-info bg-opacity-10 text-info align-self-start">1 reservation</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Returning Customers</p>
                    <h3 class="mb-1 fw-bold text-success">{{ $returningCustomers }}</h3>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success align-self-start">2+ reservations</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Active Customers</p>
                    <h3 class="mb-1 fw-bold text-primary">{{ $activeCustomers }}</h3>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary align-self-start">Active stay/booking</span>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 customer-table">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4" style="width: 80px;">Avatar</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th class="text-center">Total Bookings</th>
                    <th>Last Reservation</th>
                    <th>Tier Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="ps-4">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </div>
                    </td>
                    <td>
                        <span class="fw-bold text-dark">{{ $customer->name }}</span>
                    </td>
                    <td>
                        <span class="text-muted">{{ $customer->email ?? '—' }}</span>
                    </td>
                    <td>
                        <span>{{ $customer->phone ?? 'N/A' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark px-3 py-2 fw-semibold">{{ $customer->reservations_count }}</span>
                    </td>
                    <td>
                        <span>{{ $customer->last_reservation_date ? \Carbon\Carbon::parse($customer->last_reservation_date)->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td>
                        @if($customer->reservations_count >= 5)
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2.5 py-1.5 fw-bold">VIP</span>
                        @elseif($customer->reservations_count >= 2)
                            <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5 fw-bold">Returning</span>
                        @else
                            <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 fw-bold">New</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <button type="button" class="btn btn-sm btn-outline-dark px-3 mb-1" data-bs-toggle="modal" data-bs-target="#viewCustomerModal{{ $customer->id }}">
                            <i class="bi bi-eye-fill me-1"></i> View
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary px-3 mb-1" data-bs-toggle="modal" data-bs-target="#editCustomerModal{{ $customer->id }}">
                            <i class="bi bi-pencil-fill me-1"></i> Edit
                        </button>
                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer? Customers with reservation history cannot be deleted.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-3 mb-1">
                                <i class="bi bi-trash-fill me-1"></i> Delete
                            </button>
                        </form>

                        <!-- View Customer Modal -->
                        <div class="modal fade" id="viewCustomerModal{{ $customer->id }}" tabindex="-1" aria-labelledby="viewCustomerModalLabel{{ $customer->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content bg-light">
                                    <div class="modal-header border-0 pb-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 p-md-5 pt-0">
                                        <div class="page-title-bar mb-4">
                                            <div class="title-group">
                                                <h4 class="fw-bold mb-1">Customer Detail: {{ $customer->name }}</h4>
                                                <p class="mb-0 text-muted">Detailed view and reservation history</p>
                                            </div>
                                        </div>

                                        <div class="row g-4 mb-4">
                                            <!-- Customer Information -->
                                            <div class="col-md-6">
                                                <div class="card info-card h-100 border-0 shadow-sm p-4" style="border-radius: 1rem;">
                                                    <h6 class="fw-bold text-uppercase mb-4" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                                                        <i class="bi bi-person-fill text-primary me-2"></i> Customer Information
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Full Name</div>
                                                            <div class="fw-medium text-dark">{{ $customer->name }}</div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Phone</div>
                                                            <div class="fw-medium text-dark">{{ $customer->phone }}</div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Email</div>
                                                            <div class="fw-medium text-dark">{{ $customer->email ?? '—' }}</div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Created</div>
                                                            <div class="fw-medium text-dark">{{ $customer->created_at->format('M d, Y') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reservation Summary -->
                                            <div class="col-md-6">
                                                <div class="card info-card h-100 border-0 shadow-sm p-4" style="border-radius: 1rem;">
                                                    <h6 class="fw-bold text-uppercase mb-4" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                                                        <i class="bi bi-pie-chart-fill text-success me-2"></i> Reservation Summary
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Total Reservations</div>
                                                            <div><span class="badge bg-light text-dark border">{{ $customer->reservations_count }}</span></div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Status</div>
                                                            <div>
                                                                @if($customer->reservations_count >= 5)
                                                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">VIP</span>
                                                                @elseif($customer->reservations_count >= 2)
                                                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Returning</span>
                                                                @else
                                                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">New</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">First Reservation</div>
                                                            <div class="fw-medium text-dark">{{ $customer->reservations->last()?->check_in ? \Carbon\Carbon::parse($customer->reservations->last()->check_in)->format('M d, Y') : '—' }}</div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Last Reservation</div>
                                                            <div class="fw-medium text-dark">{{ $customer->reservations->first()?->check_in ? \Carbon\Carbon::parse($customer->reservations->first()->check_in)->format('M d, Y') : '—' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reservation History -->
                                        <div class="card border-0 shadow-sm" style="border-radius: 1rem; overflow: hidden;">
                                            <div class="card-header bg-white border-bottom py-3">
                                                <h6 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                                                    <i class="bi bi-clock-history text-muted me-2"></i> Reservation History
                                                </h6>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light text-uppercase small text-muted">
                                                        <tr>
                                                            <th class="ps-4">ID</th>
                                                            <th>Date Created</th>
                                                            <th>Check-in</th>
                                                            <th>Check-out</th>
                                                            <th>Room</th>
                                                            <th>Status</th>
                                                            <th class="text-end pe-4">Total Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($customer->reservations as $res)
                                                        <tr>
                                                            <td class="ps-4"><span class="fw-bold text-dark">#{{ str_pad($res->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                                                            <td>{{ $res->created_at->format('M d, Y') }}</td>
                                                            <td><span class="text-success fw-medium">{{ \Carbon\Carbon::parse($res->check_in)->format('M d, Y') }}</span></td>
                                                            <td><span class="text-danger fw-medium">{{ \Carbon\Carbon::parse($res->check_out)->format('M d, Y') }}</span></td>
                                                            <td>
                                                                <span class="badge bg-light text-dark border">
                                                                    <i class="bi bi-door-closed me-1"></i>{{ $res->room ? $res->room->room_number : 'N/A' }}
                                                                </span>
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
                                                                    $badgeLabel = match($res->status) {
                                                                        'confirmed'   => 'Confirmed',
                                                                        'checked_in'  => 'Checked In',
                                                                        'checked_out' => 'Checked Out',
                                                                        'pending'     => 'Pending',
                                                                        'cancelled'   => 'Cancelled',
                                                                        default       => ucfirst($res->status),
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }} px-2.5 py-1.5 fw-bold">{{ $badgeLabel }}</span>
                                                            </td>
                                                            <td class="text-end pe-4 fw-bold" style="color: #0F172A;">{{ number_format($res->total_price, 2) }} MAD</td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center py-4 text-muted">No reservations found.</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Customer Modal -->
                        <div class="modal fade" id="editCustomerModal{{ $customer->id }}" tabindex="-1" aria-labelledby="editCustomerModalLabel{{ $customer->id }}" aria-hidden="true">
                            <div class="modal-dialog text-start">
                                <div class="modal-content">
                                    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editCustomerModalLabel{{ $customer->id }}">Edit Customer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="name{{ $customer->id }}" class="form-label">Full Name</label>
                                                <input type="text" class="form-control" id="name{{ $customer->id }}" name="name" value="{{ $customer->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone{{ $customer->id }}" class="form-label">Phone</label>
                                                <input type="text" class="form-control" id="phone{{ $customer->id }}" name="phone" value="{{ $customer->phone }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email{{ $customer->id }}" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email{{ $customer->id }}" name="email" value="{{ $customer->email }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-3 text-muted mb-0">No customers found for this hotel.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
