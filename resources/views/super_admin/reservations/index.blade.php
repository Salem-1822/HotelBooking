@extends('super_admin.layouts.app')

@section('title', __('super_admin.morocco_reservations'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h5 class="fw-bold mb-0">{{ __('super_admin.global_reservations') }}</h5>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge bg-primary rounded-pill shadow-sm">{{ __('super_admin.records_found', ['count' => $reservations->total()]) }}</span>
            <p class="text-muted small mb-0 d-none d-sm-block">{{ __('super_admin.managing_bookings') }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('super_admin.reservations.export') }}" class="btn btn-light px-4 py-2 rounded-3 shadow-sm border text-danger">
            <i class="bi bi-file-pdf me-2"></i> {{ __('super_admin.export_pdf') }}
        </a>
    </div>
</div>

{{-- Dynamic Filters Bar --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
    <div class="card-body p-4 bg-white">
        <form action="{{ route('super_admin.reservations.index') }}" method="GET" id="filterForm">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted">{{ __('super_admin.city') }}</label>
                    <select name="city_id" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">{{ __('super_admin.all_cities') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted">{{ __('super_admin.status') }}</label>
                    <select name="status" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">{{ __('super_admin.all_status') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('super_admin.status_pending') }}</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('super_admin.status_confirmed') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('super_admin.status_cancelled') }}</option>
                        <option value="checked_in"  {{ request('status') == 'checked_in'  ? 'selected' : '' }}>{{ __('super_admin.status_checked_in') }}</option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>{{ __('super_admin.status_checked_out') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-8 col-lg-4">
                    <label class="form-label small fw-bold text-muted">{{ __('super_admin.hotel_establishment') }}</label>
                    <select name="hotel_id" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">{{ __('super_admin.all_hotels') }}</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ request('hotel_id') == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-2 d-flex flex-column justify-content-end">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 fw-bold border-0 shadow-sm">
                            {{ __('super_admin.apply') }}
                        </button>
                        <a href="{{ route('super_admin.reservations.index') }}" class="btn btn-light border-0" title="{{ __('super_admin.reset') }}">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Reservations Grid --}}
<div class="row g-4 mb-4">
    @forelse($reservations as $res)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm transition hover-lift" style="border-radius: 1rem;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="text-truncate me-2">
                        <span class="badge bg-light text-muted border mb-2 fs-8">#MOR-RSV-{{ $res->id }}</span>
                        @if($res->user)
                            <span class="badge mb-2 ms-1" style="font-size:.65rem; background:#EFF6FF; color:#1E40AF; border:1px solid #BFDBFE;">
                                <i class="bi bi-person-check-fill"></i> {{ __('super_admin.client_portal') }}
                            </span>
                        @endif
                        <h6 class="fw-bold mb-0 text-dark text-truncate" title="{{ $res->guest_name }}">{{ $res->guest_name }}</h6>
                        @if($res->user)
                            <small class="text-muted"><i class="bi bi-envelope text-primary me-1"></i> {{ $res->user->email }}</small>
                        @else
                            <small class="text-muted"><i class="bi bi-telephone text-primary me-1"></i> {{ $res->guest_phone ?? '—' }}</small>
                        @endif
                    </div>
                    @php
                        $statusClass = match($res->status) {
                            'confirmed'   => 'status-confirmed',
                            'pending'     => 'status-pending',
                            'cancelled'   => 'status-cancelled',
                            'completed'   => 'status-completed',
                            'checked_in'  => 'status-confirmed',
                            'checked_out' => 'status-completed',
                            default       => 'bg-secondary text-white'
                        };
                        $statusLabel = match($res->status) {
                            'pending'     => __('super_admin.status_pending'),
                            'confirmed'   => __('super_admin.status_confirmed'),
                            'cancelled'   => __('super_admin.status_cancelled'),
                            'completed'   => __('super_admin.status_checked_out'),
                            'checked_in'  => __('super_admin.status_checked_in'),
                            'checked_out' => __('super_admin.status_checked_out'),
                            default       => ucfirst($res->status)
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} border px-3 py-2 rounded-pill text-capitalize shadow-xs" style="font-size: 0.7rem;">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-white p-2 rounded-circle shadow-sm me-3 flex-shrink-0">
                            <i class="bi bi-building-fill text-primary"></i>
                        </div>
                        <div class="text-truncate">
                            <p class="mb-0 fw-bold small text-dark text-truncate">{{ optional($res->hotel)->name ?? 'Unknown Hotel' }}</p>
                            <small class="text-muted opacity-75"><i class="bi bi-geo-alt me-1"></i> {{ optional(optional($res->hotel)->city)->name ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3 gap-2">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-0 fw-bold text-uppercase ls-1">{{ __('super_admin.in') }}</p>
                            <p class="mb-0 fw-bold text-dark small">{{ $res->check_in ? \Carbon\Carbon::parse($res->check_in)->format('d M') : 'N/A' }}</p>
                        </div>
                        <div class="text-center px-1">
                            <i class="bi bi-arrow-right text-muted opacity-50"></i>
                        </div>
                        <div class="flex-grow-1 text-end">
                            <p class="text-muted small mb-0 fw-bold text-uppercase ls-1">{{ __('super_admin.out') }}</p>
                            <p class="mb-0 fw-bold text-dark small">{{ $res->check_out ? \Carbon\Carbon::parse($res->check_out)->format('d M') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <p class="text-muted mb-0 small" style="font-size: 0.75rem;"><i class="bi bi-people me-1"></i> {{ __('super_admin.guests_count', ['count' => $res->guests_count ?? 1]) }}</p>
                        <p class="text-muted mb-0 small" style="font-size: 0.75rem;"><i class="bi bi-tag me-1"></i> {{ __('super_admin.per_night', ['price' => number_format(optional($res->hotel)->price_per_night ?? 0, 0)]) }}</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted small mb-0 text-uppercase fw-bold d-block ls-1" style="font-size: 0.6rem;">{{ __('super_admin.total') }}</small>
                        <h5 class="fw-bold mb-0 text-primary">{{ number_format($res->total_price ?? 0, 0) }} <small class="fw-normal text-muted" style="font-size: 0.7rem;">{{ __('super_admin.mad') }}</small></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 py-5 text-center bg-white rounded-4 shadow-sm">
        <div class="mb-3">
            <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                <i class="bi bi-search fs-1 text-muted opacity-50"></i>
            </div>
        </div>
        <h5 class="text-dark fw-bold">{{ __('super_admin.no_reservations_found') }}</h5>
        <p class="text-muted small mx-auto" style="max-width: 300px;">{{ __('super_admin.no_reservations_desc') }}</p>
        <a href="{{ route('super_admin.reservations.index') }}" class="btn btn-primary px-4 py-2 rounded-pill mt-3 fw-bold">
            <i class="bi bi-arrow-left me-2"></i> {{ __('super_admin.show_all_reservations') }}
        </a>
    </div>
    @endforelse
</div>

<div class="pagination-container py-4">
    {{ $reservations->links() }}
</div>

<style>
    .ls-1 { letter-spacing: 0.05em; }
    .fs-8 { font-size: 0.7rem; }
    .hover-lift {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
    
    /* Advanced Badge Styling */
    .status-confirmed { background-color: #dcfce7; color: #15803d; border-color: #bbf7d0 !important; }
    .status-pending { background-color: #fef9c3; color: #a16207; border-color: #fef08a !important; }
    .status-cancelled { background-color: #fee2e2; color: #b91c1c; border-color: #fecaca !important; }
    .status-completed { background-color: #dcf0ff; color: #1d4ed8; border-color: #bee3ff !important; }

    /* Custom Mobile Styling */
    @media (max-width: 767.98px) {
        .card-body.p-4 { padding: 1.5rem !important; }
    }
</style>
@endsection
