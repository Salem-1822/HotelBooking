@extends('admin.layouts.app')

@section('title', __('admin.customer_management'))

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
        <h4 class="fw-bold mb-1">{{ __('admin.customer_management') }}</h4>
        <p class="mb-0 text-muted">{{ __('admin.customer_management_desc') }}</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.total_customers') }}</p>
                    <h3 class="mb-1 fw-bold text-dark">{{ $totalCustomers }}</h3>
                </div>
                <span class="badge bg-light text-dark align-self-start">{{ __('admin.all_registered') }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.new_customers') }}</p>
                    <h3 class="mb-1 fw-bold text-info">{{ $newCustomers }}</h3>
                </div>
                <span class="badge bg-info bg-opacity-10 text-info align-self-start">{{ __('admin.one_reservation') }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.returning_customers') }}</p>
                    <h3 class="mb-1 fw-bold text-success">{{ $returningCustomers }}</h3>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success align-self-start">{{ __('admin.two_plus_reservations') }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.active_customers') }}</p>
                    <h3 class="mb-1 fw-bold text-primary">{{ $activeCustomers }}</h3>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary align-self-start">{{ __('admin.active_stay') }}</span>
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
                    <th class="ps-4" style="width: 80px;">{{ __('admin.th_avatar') }}</th>
                    <th>{{ __('admin.th_full_name') }}</th>
                    <th>{{ __('admin.th_email') }}</th>
                    <th>{{ __('admin.th_phone') }}</th>
                    <th class="text-center">{{ __('admin.th_total_bookings') }}</th>
                    <th>{{ __('admin.th_last_reservation') }}</th>
                    <th>{{ __('admin.th_tier_status') }}</th>
                    <th class="text-center">{{ __('admin.th_actions') }}</th>
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
                        <span><span dir="ltr">{{ $customer->phone ?? 'N/A' }}</span></span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark px-3 py-2 fw-semibold">{{ $customer->reservations_count }}</span>
                    </td>
                    <td>
                        <span>{{ $customer->last_reservation_date ? \Carbon\Carbon::parse($customer->last_reservation_date)->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td>
                        @if($customer->reservations_count >= 5)
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2.5 py-1.5 fw-bold">{{ __('admin.vip') }}</span>
                        @elseif($customer->reservations_count >= 2)
                            <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5 fw-bold">{{ __('admin.returning') }}</span>
                        @else
                            <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 fw-bold">{{ __('admin.new') }}</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <button type="button" class="btn btn-sm btn-outline-dark px-3 mb-1" data-bs-toggle="modal" data-bs-target="#viewCustomerModal{{ $customer->id }}">
                            <i class="bi bi-eye-fill me-1"></i> {{ __('admin.view') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary px-3 mb-1" data-bs-toggle="modal" data-bs-target="#editCustomerModal{{ $customer->id }}">
                            <i class="bi bi-pencil-fill me-1"></i> {{ __('admin.edit') }}
                        </button>
                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.delete_customer_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-3 mb-1">
                                <i class="bi bi-trash-fill me-1"></i> {{ __('admin.delete') }}
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
                                                <h4 class="fw-bold mb-1">{{ __('admin.customer_detail_title', ['name' => $customer->name]) }}</h4>
                                                <p class="mb-0 text-muted">{{ __('admin.detailed_view_history') }}</p>
                                            </div>
                                        </div>

                                        <div class="row g-4 mb-4">
                                            <!-- Customer Information -->
                                            <div class="col-md-6">
                                                <div class="card info-card h-100 border-0 shadow-sm p-4" style="border-radius: 1rem;">
                                                    <h6 class="fw-bold text-uppercase mb-4" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                                                        <i class="bi bi-person-fill text-primary me-2"></i> {{ __('admin.customer_information') }}
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">{{ __('admin.th_full_name') }}</div>
                                                            <div class="fw-medium text-dark">{{ $customer->name }}</div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">{{ __('admin.th_phone') }}</div>
                                                            <div class="fw-medium text-dark"><span dir="ltr">{{ $customer->phone }}</span></div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">{{ __('admin.th_email') }}</div>
                                                            <div class="fw-medium text-dark">{{ $customer->email ?? '—' }}</div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">{{ __('admin.created_at') }}</div>
                                                            <div class="fw-medium text-dark">{{ $customer->created_at->format('M d, Y') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reservation Summary -->
                                            <div class="col-md-6">
                                                <div class="card info-card h-100 border-0 shadow-sm p-4" style="border-radius: 1rem;">
                                                    <h6 class="fw-bold text-uppercase mb-4" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                                                        <i class="bi bi-pie-chart-fill text-success me-2"></i> {{ __('admin.reservation_summary') }}
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">{{ __('admin.total_reservations') }}</div>
                                                            <div><span class="badge bg-light text-dark border">{{ $customer->reservations_count }}</span></div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">{{ __('admin.th_status') }}</div>
                                                            <div>
                                                                @if($customer->reservations_count >= 5)
                                                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">{{ __('admin.vip') }}</span>
                                                                @elseif($customer->reservations_count >= 2)
                                                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">{{ __('admin.returning') }}</span>
                                                                @else
                                                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ __('admin.new') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">{{ __('admin.first_reservation') }}</div>
                                                            <div class="fw-medium text-dark">{{ $customer->reservations->last()?->check_in ? \Carbon\Carbon::parse($customer->reservations->last()->check_in)->format('M d, Y') : '—' }}</div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">{{ __('admin.last_reservation') }}</div>
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
                                                    <i class="bi bi-clock-history text-muted me-2"></i> {{ __('admin.reservation_history') }}
                                                </h6>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light text-uppercase small text-muted">
                                                        <tr>
                                                            <th class="ps-4">{{ __('admin.th_id') }}</th>
                                                            <th>{{ __('admin.created_at') }}</th>
                                                            <th>{{ __('admin.check_in_date') }}</th>
                                                            <th>{{ __('admin.check_out_date') }}</th>
                                                            <th>{{ __('admin.th_room') }}</th>
                                                            <th>{{ __('admin.th_status') }}</th>
                                                            <th class="text-end pe-4">{{ __('admin.th_total_price') }}</th>
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
                                                                        'confirmed'   => __('admin.confirmed'),
                                                                        'checked_in'  => __('admin.checked_in'),
                                                                        'checked_out' => __('admin.checked_out'),
                                                                        'pending'     => __('admin.pending'),
                                                                        'cancelled'   => __('admin.cancelled'),
                                                                        default       => ucfirst($res->status),
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }} px-2.5 py-1.5 fw-bold">{{ $badgeLabel }}</span>
                                                            </td>
                                                            <td class="text-end pe-4 fw-bold" style="color: #0F172A;">{{ number_format($res->total_price, 2) }} {{ __('common.currency') }}</td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center py-4 text-muted">{{ __('admin.no_reservations_found') }}</td>
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
                                            <h5 class="modal-title" id="editCustomerModalLabel{{ $customer->id }}">{{ __('admin.edit_customer_title') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="name{{ $customer->id }}" class="form-label">{{ __('admin.th_full_name') }}</label>
                                                <input type="text" class="form-control" id="name{{ $customer->id }}" name="name" value="{{ $customer->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone{{ $customer->id }}" class="form-label">{{ __('admin.th_phone') }}</label>
                                                <input type="text" class="form-control" id="phone{{ $customer->id }}" name="phone" value="{{ $customer->phone }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email{{ $customer->id }}" class="form-label">{{ __('admin.th_email') }}</label>
                                                <input type="email" class="form-control" id="email{{ $customer->id }}" name="email" value="{{ $customer->email }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                                            <button type="submit" class="btn btn-primary">{{ __('admin.save_changes') }}</button>
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
                        <p class="mt-3 text-muted mb-0">{{ __('admin.no_customers_found') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-center">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>

@endsection
