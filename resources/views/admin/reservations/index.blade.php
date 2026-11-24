@extends('admin.layouts.app')

@section('title', __('admin.manage_reservations'))

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
        <h4 class="fw-bold mb-1">{{ __('admin.manage_reservations') }}</h4>
        <p class="mb-0 text-muted">{{ __('admin.manage_reservations_desc') }}</p>
    </div>
    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addReservationModal">
        <i class="bi bi-plus-lg me-2"></i> {{ __('admin.add_reservation') }}
    </button>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.total_reservations') }}</p>
                <h3 class="mb-1 fw-bold counter" data-count="{{ $totalReservations }}">0</h3>
                <span class="badge bg-light text-dark">{{ __('admin.all_status') }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.pending') }}</p>
                <h3 class="mb-1 fw-bold counter text-warning" data-count="{{ $pendingCount }}">0</h3>
                <span class="badge bg-warning bg-opacity-10 text-warning">{{ __('admin.action_required') }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.confirmed') }}</p>
                <h3 class="mb-1 fw-bold counter text-success" data-count="{{ $confirmedCount }}">0</h3>
                <span class="badge bg-success bg-opacity-10 text-success">{{ __('admin.approved') }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.cancelled') }}</p>
                <h3 class="mb-1 fw-bold counter text-danger" data-count="{{ $cancelledCount }}">0</h3>
                <span class="badge bg-danger bg-opacity-10 text-danger">{{ __('admin.revoked') }}</span>
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
                               placeholder="{{ __('admin.search_reservations_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <select name="status" class="form-select bg-light border-0">
                        <option value="">{{ __('admin.filter_all_statuses') }}</option>
                        @foreach($statusOptions as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <input type="date" name="date" class="form-control bg-light border-0" value="{{ request('date') }}" title="{{ __('admin.filter_checkin_title') }}">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100">{{ __('admin.filter') }}</button>
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
                    <th class="ps-4">{{ __('admin.th_ref') }}</th>
                    <th>{{ __('admin.th_guest') }}</th>
                    <th>{{ __('admin.th_room') }}</th>
                    <th>{{ __('admin.th_booking_dates') }}</th>
                    <th>{{ __('admin.guests') }}</th>
                    <th>{{ __('admin.th_total_price') }}</th>
                    <th>{{ __('admin.th_status') }}</th>
                    <th class="text-center">{{ __('admin.th_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $res)
                <tr>
                    <td class="ps-4">
                        <span class="fw-bold text-dark"><span dir="ltr">#MOR-RSV-{{ $res->id }}</span></span>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $res->guest_name }}</div>
                        <small class="text-muted"><i class="bi bi-telephone text-primary me-1"></i> <span dir="ltr">{{ $res->guest_phone }}</span></small>
                    </td>
                    <td>
                        @if($res->room)
                            <div class="fw-semibold text-dark">{{ __('admin.room') }} {{ $res->room->room_number }}</div>
                            <small class="text-muted">{{ $res->room->type }} ({{ number_format($res->room->price_per_night, 0) }} {{ __('common.currency') }}/{{ __('admin.night_short') }})</small>
                        @else
                            <span class="text-danger small">{{ __('admin.no_room_assigned') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="small fw-semibold text-dark">
                            {{ $res->check_in ? \Carbon\Carbon::parse($res->check_in)->format('d M Y') : 'N/A' }} 
                            <i class="bi bi-arrow-right mx-1 text-muted"></i> 
                            {{ $res->check_out ? \Carbon\Carbon::parse($res->check_out)->format('d M Y') : 'N/A' }}
                        </div>
                        <small class="text-muted">
                            {{ $res->check_in && $res->check_out ? __('admin.nights_count', ['count' => \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out))]) : 'N/A' }}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $res->guests_count }}</span>
                    </td>
                    <td>
                        <span class="fw-bold text-primary">{{ number_format($res->total_price, 0) }} {{ __('common.currency') }}</span>
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
                            {{ __('admin.' . $res->status) ?? ($statusOptions[$res->status] ?? ucfirst($res->status)) }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-dark px-3 mb-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#viewModal{{ $res->id }}">
                            <i class="bi bi-eye-fill me-1"></i> {{ __('admin.view') }}
                        </button>
                        <button class="btn btn-sm btn-outline-primary px-3 mb-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal{{ $res->id }}">
                            <i class="bi bi-pencil-fill me-1"></i> {{ __('admin.edit') }}
                        </button>
                        <form action="{{ route('admin.reservations.destroy', $res) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.delete_reservation_confirm') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-3 mb-1">
                                <i class="bi bi-trash-fill me-1"></i> {{ __('admin.delete') }}
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- View Details Modal --}}
                <div class="modal fade text-start" id="viewModal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0 bg-light p-4">
                                <h5 class="modal-title fw-bold text-dark">{{ __('admin.reservation_details_title', ['ref' => '#MOR-RSV-' . $res->id]) }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small mb-1 text-uppercase ls-1" style="font-size:0.7rem;">{{ __('admin.th_status') }}</p>
                                        <span class="badge {{ $badgeClass }} px-2.5 py-1.5 fw-bold">
                                            {{ __('admin.' . $res->status) ?? ($statusOptions[$res->status] ?? ucfirst($res->status)) }}
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted small mb-1 text-uppercase ls-1" style="font-size:0.7rem;">{{ __('admin.total_cost') }}</p>
                                        <h4 class="fw-bold text-primary mb-0">{{ number_format($res->total_price, 0) }} {{ __('common.currency') }}</h4>
                                    </div>
                                </div>
                                <hr class="my-3 opacity-50">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.guest_name') }}</label>
                                        <span class="text-dark fw-semibold">{{ $res->guest_name }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.guest_phone') }}</label>
                                        <span class="text-dark fw-semibold" dir="ltr">{{ $res->guest_phone ?? '—' }}</span>
                                    </div>
                                    @if($res->user)
                                    <div class="col-12">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.booking_source') }}</label>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1" style="font-size:.72rem;">
                                            <i class="bi bi-person-check-fill me-1"></i>{{ __('admin.client_portal') }}
                                        </span>
                                        <div class="text-muted small mt-1" dir="ltr">{{ $res->user->email }}</div>
                                    </div>
                                    @endif
                                    @if($res->customer && $res->customer->email)
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.th_email') }}</label>
                                        <span class="text-dark fw-semibold" dir="ltr">{{ $res->customer->email }}</span>
                                    </div>
                                    @endif
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.room_number_label') }}</label>
                                        <span class="text-dark fw-semibold">{{ __('admin.room') }} {{ $res->room?->room_number ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.room_type_label') }}</label>
                                        <span class="text-dark fw-semibold">{{ $res->room?->type ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.check_in_date') }}</label>
                                        <span class="text-dark fw-semibold">{{ $res->check_in }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.check_out_date') }}</label>
                                        <span class="text-dark fw-semibold">{{ $res->check_out }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('client.nights') }}</label>
                                        <span class="text-dark fw-semibold">{{ $res->check_in && $res->check_out ? \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out)) : 'N/A' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('admin.guests_count') }}</label>
                                        <span class="text-dark fw-semibold">{{ $res->guests_count }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('admin.close') }}</button>
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
                                    <h5 class="modal-title fw-bold text-dark">
                                        {{ __('admin.edit_reservation_title', ['ref' => '#MOR-RSV-' . $res->id]) }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">

                                    @if($res->user)
                                        {{-- ── CLIENT PORTAL RESERVATION ── --}}
                                        <div class="alert alert-info border-0 rounded-3 mb-3 py-2 px-3" style="font-size:.82rem; background:#EFF6FF; color:#1E40AF;">
                                            <i class="bi bi-person-check-fill me-1"></i>
                                            <strong>{{ __('admin.client_portal_booking_alert') }}</strong>
                                        </div>
                                        {{-- Read-only client info --}}
                                        <div class="card border-0 bg-light mb-3">
                                            <div class="card-body py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($res->user->name) }}&background=0F172A&color=fff&size=40"
                                                         class="rounded-circle" width="40" height="40" alt="">
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $res->user->name }}</div>
                                                        <div class="text-muted small">{{ $res->user->email }}</div>
                                                        @if($res->guest_phone)
                                                            <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $res->guest_phone }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Hidden fields to satisfy server validation for client reservations --}}
                                        <input type="hidden" name="guest_name" value="{{ $res->guest_name }}">
                                        <input type="hidden" name="guest_phone" value="{{ $res->guest_phone ?? '' }}">
                                    @else
                                        {{-- ── ADMIN-MANAGED RESERVATION ── --}}
                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-bold">{{ __('admin.select_customer') }}</label>
                                            <select name="customer_id" class="form-select customer-select">
                                                <option value="">{{ __('admin.new_custom_guest') }}</option>
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
                                                <h6 class="fw-bold mb-2">{{ __('admin.customer_details') }}</h6>
                                                <p class="mb-1"><i class="bi bi-person text-primary me-2"></i><span class="preview-name">{{ $res->customer?->name }}</span></p>
                                                <p class="mb-1"><i class="bi bi-telephone text-primary me-2"></i><span class="preview-phone">{{ $res->customer?->phone }}</span></p>
                                                <p class="mb-0"><i class="bi bi-envelope text-primary me-2"></i><span class="preview-email">{{ $res->customer?->email ?? '—' }}</span></p>
                                            </div>
                                        </div>
                                        <div class="new-customer-fields {{ $res->customer_id ? 'd-none' : '' }}">
                                            <div class="mb-3">
                                                <label class="form-label text-dark fw-bold">{{ __('admin.guest_name') }}</label>
                                                <input type="text" name="guest_name" class="form-control guest-name-input" value="{{ $res->guest_name }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-dark fw-bold">{{ __('admin.guest_phone') }}</label>
                                                <input type="text" name="guest_phone" class="form-control guest-phone-input" value="{{ $res->guest_phone }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-dark fw-bold">{{ __('admin.guest_email_optional') }}</label>
                                                <input type="email" name="guest_email" class="form-control guest-email-input" value="">
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <label class="form-label text-dark fw-bold">{{ __('admin.th_room') }}</label>
                                            <select name="room_id" class="form-select" required>
                                                @foreach($rooms as $room)
                                                    <option value="{{ $room->id }}" {{ $res->room_id == $room->id ? 'selected' : '' }}>
                                                        {{ __('admin.room') }} {{ $room->room_number }} ({{ $room->type }} - {{ number_format($room->price_per_night, 0) }} {{ __('common.currency') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-dark fw-bold">{{ __('admin.guests_count') }}</label>
                                            <input type="number" name="guests_count" class="form-control" value="{{ $res->guests_count }}" min="1" required>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <label class="form-label text-dark fw-bold">{{ __('admin.check_in_date') }}</label>
                                            <input type="date" name="check_in" class="form-control" value="{{ $res->check_in }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-dark fw-bold">{{ __('admin.check_out_date') }}</label>
                                            <input type="date" name="check_out" class="form-control" value="{{ $res->check_out }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-bold">{{ __('admin.th_status') }}</label>
                                        <select name="status" class="form-select" required>
                                            @foreach($statusOptions as $val => $label)
                                                <option value="{{ $val }}" {{ $res->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 p-4 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                                    <button type="submit" class="btn btn-primary px-4">{{ __('admin.save_changes') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar2-x fs-1 d-block mb-3 opacity-50"></i>
                        {{ __('admin.no_reservations_found') }}
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
                    <h5 class="modal-title fw-bold text-dark">{{ __('admin.add_reservation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">{{ __('admin.select_customer') }}</label>
                        <select name="customer_id" class="form-select customer-select">
                            <option value="">{{ __('admin.create_new_customer') }}</option>
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
                            <h6 class="fw-bold mb-2">{{ __('admin.customer_details') }}</h6>
                            <p class="mb-1"><i class="bi bi-person text-primary me-2"></i><span class="preview-name"></span></p>
                            <p class="mb-1"><i class="bi bi-telephone text-primary me-2"></i><span class="preview-phone"></span></p>
                            <p class="mb-0"><i class="bi bi-envelope text-primary me-2"></i><span class="preview-email"></span></p>
                        </div>
                    </div>
                    <div class="new-customer-fields">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">{{ __('admin.guest_name') }}</label>
                            <input type="text" name="guest_name" class="form-control guest-name-input" placeholder="{{ __('admin.guest_name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">{{ __('admin.guest_phone') }}</label>
                            <input type="text" name="guest_phone" class="form-control guest-phone-input" placeholder="+212 600-000000" dir="ltr" style="text-align:start;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">{{ __('admin.guest_email_optional') }}</label>
                            <input type="email" name="guest_email" class="form-control guest-email-input" placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-dark fw-bold">{{ __('admin.th_room') }}</label>
                            <select name="room_id" class="form-select" required>
                                <option value="" disabled selected>{{ __('admin.select_room') }}</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">
                                        {{ __('admin.room') }} {{ $room->room_number }} ({{ $room->type }} - {{ number_format($room->price_per_night, 0) }} {{ __('common.currency') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dark fw-bold">{{ __('admin.guests_count') }}</label>
                            <input type="number" name="guests_count" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-dark fw-bold">{{ __('admin.check_in_date') }}</label>
                            <input type="date" name="check_in" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dark fw-bold">{{ __('admin.check_out_date') }}</label>
                            <input type="date" name="check_out" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">{{ __('admin.th_status') }}</label>
                        <select name="status" class="form-select" required>
                            @foreach($createStatusOptions as $val => $label)
                                <option value="{{ $val }}" {{ $val === 'pending' ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">{{ __('admin.add_reservation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
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
