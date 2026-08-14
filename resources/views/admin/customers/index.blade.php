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
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-dark px-3 mb-1">
                            <i class="bi bi-eye-fill me-1"></i> View
                        </a>
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
