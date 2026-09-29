@extends('client.layouts.app')

@section('title', 'Reservation #MOR-RSV-' . $reservation->id)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
/* ── Page Header ─────────────────────────────── */
.page-header {
    background: var(--brand-primary);
    padding: 2.5rem 0 2rem;
}
.page-header .breadcrumb-item,
.page-header .breadcrumb-item a {
    color: rgba(255,255,255,0.55);
    font-size: 0.82rem;
    text-decoration: none;
}
.page-header .breadcrumb-item.active { color: rgba(255,255,255,0.9); }
.page-header .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.3); }
.page-header h1 { color: #fff; font-size: 1.5rem; font-weight: 700; margin: 0; }

/* ── Section heading ─────────────────────────── */
.sec-heading {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-color);
}

/* ── Data rows ───────────────────────────────── */
.data-row {
    display: flex;
    padding: 0.625rem 0;
    border-bottom: 1px solid #F1F5F9;
    font-size: 0.875rem;
    gap: 0.75rem;
}
.data-row:last-child { border-bottom: none; }
.data-label {
    width: 140px;
    flex-shrink: 0;
    color: var(--text-muted);
    font-size: 0.82rem;
    font-weight: 500;
    padding-top: 0.05rem;
}
.data-value { color: var(--text-primary); font-weight: 600; flex: 1; }

/* ── Status badge ────────────────────────────── */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.4rem 1rem;
    letter-spacing: 0.03em;
}

/* ── Hotel image banner ──────────────────────── */
.hotel-banner {
    height: 180px;
    background: linear-gradient(135deg, var(--brand-primary) 0%, #1e3a5f 100%);
    border-radius: 0.75rem 0.75rem 0 0;
    overflow: hidden;
    position: relative;
}
.hotel-banner img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.hotel-banner-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(15,23,42,0.7) 0%, transparent 60%);
}
.hotel-banner-label {
    position: absolute; bottom: 1rem; left: 1.25rem;
    color: #fff;
}
.hotel-banner-label .hotel-name { font-weight: 700; font-size: 1.05rem; }
.hotel-banner-label .hotel-city { font-size: 0.78rem; color: rgba(255,255,255,0.75); }

/* ── Summary card ────────────────────────────── */
.summary-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 0.875rem;
    overflow: hidden;
    box-shadow: var(--card-shadow);
}
.summary-card-header {
    background: var(--brand-primary);
    color: #fff;
    padding: 1.25rem 1.5rem;
}
.summary-card-header .ref {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.55);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.2rem;
}
.summary-card-header .ref-val {
    font-size: 1rem;
    font-weight: 700;
    color: var(--brand-accent);
}
.summary-card-body { padding: 1.25rem 1.5rem; }

/* ── Price summary ───────────────────────────── */
.price-total {
    background: rgba(212,175,55,0.06);
    border: 1px solid rgba(212,175,55,0.2);
    border-radius: 0.625rem;
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.price-total .label { font-size: 0.82rem; color: var(--text-muted); font-weight: 500; }
.price-total .amount { font-size: 1.35rem; font-weight: 800; color: var(--brand-primary); }

/* ── Main card ───────────────────────────────── */
.main-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 0.875rem;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.main-card-body { padding: 1.5rem; }

/* ── Review section ──────────────────────────── */
.review-cta {
    background: rgba(212,175,55,0.05);
    border: 1px dashed rgba(212,175,55,0.4);
    border-radius: 0.75rem;
    padding: 1.25rem;
    text-align: center;
    margin-top: 0.5rem;
}
.review-cta .title { font-weight: 700; color: var(--brand-primary); font-size: 0.95rem; margin-bottom: 0.35rem; }
.review-cta .sub   { font-size: 0.78rem; color: var(--text-muted); margin-bottom: 1rem; }
.reviewed-badge {
    background: rgba(34,197,94,0.08);
    border: 1px solid rgba(34,197,94,0.25);
    border-radius: 0.625rem;
    padding: 0.875rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 0.5rem;
}
.reviewed-badge i  { font-size: 1.25rem; color: #22C55E; flex-shrink: 0; }
.reviewed-badge .txt  { font-weight: 700; font-size: 0.875rem; color: #15803D; }
.reviewed-badge .sub  { font-size: 0.75rem; color: var(--text-muted); }

/* ── Star rating ─────────────────────────────── */
.star-rating label { transition: color 0.15s; cursor: pointer; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #F59E0B !important; }

/* ── Map ─────────────────────────────────────── */
#hotelMap { height: 240px; border-radius: 0.75rem; border: 1px solid var(--border-color); }

/* ── Alert ───────────────────────────────────── */
.alert { border-radius: 0.75rem; border: none; font-size: 0.875rem; }
.alert-success { background: rgba(34,197,94,0.1); color: #15803D; }
.alert-danger  { background: rgba(239,68,68,0.1); color: #991B1B; }

/* ── Responsive ──────────────────────────────── */
@media (max-width: 767px) {
    .data-label { width: 110px; }
}
</style>
@endpush

@section('content')

{{-- PHP vars --}}
@php
    $statusMap = [
        'pending'     => ['Pending',     'rgba(245,158,11,.1)', '#92400E', 'rgba(245,158,11,.3)', 'bi-hourglass-split'],
        'confirmed'   => ['Confirmed',   'rgba(34,197,94,.1)', '#166534', 'rgba(34,197,94,.3)',   'bi-check-circle'],
        'cancelled'   => ['Cancelled',   'rgba(239,68,68,.1)', '#991B1B', 'rgba(239,68,68,.3)',   'bi-x-circle'],
        'checked_in'  => ['Checked In',  'rgba(59,130,246,.1)','#1E3A8A', 'rgba(59,130,246,.3)', 'bi-door-open'],
        'checked_out' => ['Checked Out', 'rgba(100,116,139,.1)','#374151','rgba(100,116,139,.3)','bi-house-check'],
    ];
    [$statusLabel, $statusBg, $statusColor, $statusBorder, $statusIcon] = $statusMap[$reservation->status] ?? [ucfirst($reservation->status), 'rgba(156,163,175,.1)', '#6B7280', 'rgba(156,163,175,.3)', 'bi-circle'];
    $nights = \Carbon\Carbon::parse($reservation->check_in)->diffInDays(\Carbon\Carbon::parse($reservation->check_out));
@endphp

{{-- Page Header --}}
<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.reservations.index') }}">My Reservations</a></li>
                <li class="breadcrumb-item active">#MOR-RSV-{{ $reservation->id }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h1><i class="bi bi-receipt me-2" style="color:var(--brand-accent); font-size:1.3rem;"></i>Reservation Details</h1>
            <a href="{{ route('client.reservations.index') }}" class="btn btn-outline-light-custom" style="font-size:0.82rem; padding:0.45rem 1.2rem;">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-4 align-items-start">

        {{-- ── LEFT COLUMN ─────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Hotel image + info --}}
            <div class="main-card">
                {{-- Hotel banner --}}
                <div class="hotel-banner">
                    @if($reservation->hotel?->image)
                        <img src="{{ asset('storage/' . $reservation->hotel->image) }}"
                             alt="{{ $reservation->hotel->name }}"
                             onerror="this.parentElement.classList.add('no-img'); this.remove();">
                    @endif
                    <div class="hotel-banner-overlay"></div>
                    <div class="hotel-banner-label">
                        <div class="hotel-name">{{ $reservation->hotel->name ?? 'Hotel' }}</div>
                        @if($reservation->hotel?->city)
                            <div class="hotel-city"><i class="bi bi-geo-alt-fill me-1"></i>{{ $reservation->hotel->city->name }}</div>
                        @elseif($reservation->hotel?->address)
                            <div class="hotel-city"><i class="bi bi-geo-alt-fill me-1"></i>{{ $reservation->hotel->address }}</div>
                        @endif
                    </div>
                </div>

                <div class="main-card-body">
                    {{-- Room info row --}}
                    @if($reservation->room)
                    <div class="d-flex align-items-center gap-3 p-3 mb-4"
                         style="background:#F8FAFC; border-radius:0.625rem; border:1px solid var(--border-color);">
                        <div style="width:38px; height:38px; background:rgba(212,175,55,0.12); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="bi bi-door-open" style="color:var(--brand-accent); font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <div style="font-size:0.68rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); font-weight:700;">Room</div>
                            <div style="font-weight:700; color:var(--brand-primary);">
                                {{ $reservation->room->room_number }}
                                @if($reservation->room->type) · <span style="font-weight:500; color:var(--text-muted);">{{ ucfirst($reservation->room->type) }}</span>@endif
                            </div>
                        </div>
                        @if($reservation->room->capacity)
                        <div class="ms-auto" style="font-size:0.78rem; color:var(--text-muted);">
                            <i class="bi bi-people me-1"></i>Up to {{ $reservation->room->capacity }} guest{{ $reservation->room->capacity !== 1 ? 's' : '' }}
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Booking details --}}
                    <div class="sec-heading">Booking Details</div>

                    <div class="data-row">
                        <span class="data-label">Reference</span>
                        <span class="data-value" style="font-family:'Poppins',sans-serif; color:var(--brand-primary);">#MOR-RSV-{{ $reservation->id }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Booked on</span>
                        <span class="data-value" style="font-weight:500; color:var(--text-muted);">{{ $reservation->created_at->format('d M Y \a\t H:i') }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Check-in</span>
                        <span class="data-value">{{ \Carbon\Carbon::parse($reservation->check_in)->format('l, d M Y') }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Check-out</span>
                        <span class="data-value">{{ \Carbon\Carbon::parse($reservation->check_out)->format('l, d M Y') }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Duration</span>
                        <span class="data-value">
                            {{ $nights }} night{{ $nights !== 1 ? 's' : '' }}
                        </span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Guests</span>
                        <span class="data-value"><i class="bi bi-people me-1 text-muted"></i>{{ $reservation->guests_count }} guest{{ $reservation->guests_count !== 1 ? 's' : '' }}</span>
                    </div>
                    @if($reservation->hotel?->check_in_time || $reservation->hotel?->check_out_time)
                    <div class="data-row">
                        <span class="data-label">Hotel times</span>
                        <span class="data-value" style="color:var(--text-muted); font-weight:500; font-size:0.82rem;">
                            @if($reservation->hotel->check_in_time)Check-in from {{ $reservation->hotel->check_in_time }}@endif
                            @if($reservation->hotel->check_in_time && $reservation->hotel->check_out_time) &nbsp;·&nbsp; @endif
                            @if($reservation->hotel->check_out_time)Check-out by {{ $reservation->hotel->check_out_time }}@endif
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Map card --}}
            @if($reservation->hotel)
            <div class="main-card">
                <div class="main-card-body">
                    <div class="sec-heading">Hotel Location</div>

                    @if($reservation->hotel->latitude && $reservation->hotel->longitude)
                        @if($reservation->hotel->address)
                        <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:1rem;">
                            <i class="bi bi-geo-alt-fill me-1" style="color:var(--brand-accent);"></i>
                            {{ $reservation->hotel->address }}
                        </p>
                        @endif
                        <div id="hotelMap"></div>
                        <div class="mt-3 text-end">
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $reservation->hotel->latitude }},{{ $reservation->hotel->longitude }}"
                               target="_blank" rel="noopener noreferrer"
                               class="btn btn-sm btn-outline-secondary"
                               style="border-radius:0.5rem; font-size:0.8rem; font-weight:600;">
                                <i class="bi bi-map me-1"></i> Open in Google Maps
                            </a>
                        </div>
                    @elseif($reservation->hotel->address)
                        <div style="padding:1.5rem; background:#F8FAFC; border-radius:0.625rem; border:1px solid var(--border-color);">
                            <i class="bi bi-geo-alt-fill me-2" style="color:var(--brand-accent);"></i>
                            {{ $reservation->hotel->address }}
                        </div>
                    @else
                        <div style="padding:1.5rem; background:#F8FAFC; border-radius:0.625rem; border:1px solid var(--border-color); font-size:0.875rem; color:var(--text-muted);">
                            <i class="bi bi-info-circle me-2"></i> Location information is not available.
                        </div>
                    @endif
                </div>
            </div>
            @endif

        </div>{{-- /col-lg-8 --}}

        {{-- ── RIGHT COLUMN ────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Summary card --}}
            <div class="summary-card mb-4">
                <div class="summary-card-header">
                    <div class="ref">Reservation Reference</div>
                    <div class="ref-val">#MOR-RSV-{{ $reservation->id }}</div>
                </div>
                <div class="summary-card-body">

                    {{-- Status --}}
                    <div class="mb-4">
                        <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); margin-bottom:0.625rem;">Current Status</div>
                        <span class="status-pill" style="background:{{ $statusBg }}; color:{{ $statusColor }}; border:1px solid {{ $statusBorder }};">
                            <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- Quick facts --}}
                    <div class="mb-4">
                        <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); margin-bottom:0.75rem;">Stay Summary</div>
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <div style="display:flex; justify-content:space-between; font-size:0.82rem;">
                                <span style="color:var(--text-muted);">Check-in</span>
                                <span style="font-weight:600;">{{ \Carbon\Carbon::parse($reservation->check_in)->format('d M Y') }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:0.82rem;">
                                <span style="color:var(--text-muted);">Check-out</span>
                                <span style="font-weight:600;">{{ \Carbon\Carbon::parse($reservation->check_out)->format('d M Y') }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:0.82rem;">
                                <span style="color:var(--text-muted);">Duration</span>
                                <span style="font-weight:600;">{{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:0.82rem;">
                                <span style="color:var(--text-muted);">Guests</span>
                                <span style="font-weight:600;">{{ $reservation->guests_count }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Total price --}}
                    <div class="price-total mb-4">
                        <div>
                            <div class="label">Total Paid</div>
                            <div style="font-size:0.72rem; color:var(--text-muted);">{{ $nights }} night{{ $nights !== 1 ? 's' : '' }} · {{ $reservation->guests_count }} guest{{ $reservation->guests_count !== 1 ? 's' : '' }}</div>
                        </div>
                        <div class="amount">{{ number_format($reservation->total_price, 0) }}<small style="font-size:0.7rem; font-weight:500; color:var(--text-muted);"> MAD</small></div>
                    </div>

                    {{-- Cancel action --}}
                    @if(in_array($reservation->status, ['pending', 'confirmed']))
                        <div style="border-top:1px solid var(--border-color); padding-top:1.25rem;">
                            <div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:0.75rem;">
                                <i class="bi bi-info-circle me-1"></i>You can cancel before check-in. This action cannot be undone.
                            </div>
                            <form action="{{ route('client.reservations.cancel', $reservation) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100" style="border-radius:0.625rem; font-size:0.875rem; font-weight:600;">
                                    <i class="bi bi-x-circle me-1"></i> Cancel Booking
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Review action --}}
                    @if($reservation->status === 'checked_out')
                        <div style="border-top:1px solid var(--border-color); padding-top:1.25rem; margin-top:{{ in_array($reservation->status, ['pending','confirmed']) ? '0' : '0' }};">
                            @if(!$reservation->review)
                                <div class="review-cta">
                                    <div class="title"><i class="bi bi-star-fill me-1" style="color:var(--brand-accent);"></i> Rate Your Stay</div>
                                    <div class="sub">Your feedback helps other travellers make better choices.</div>
                                    <button type="button" class="btn btn-accent w-100"
                                            data-bs-toggle="modal" data-bs-target="#reviewModal"
                                            style="font-size:0.875rem; font-weight:600; border-radius:0.625rem;">
                                        Leave a Review
                                    </button>
                                </div>
                            @else
                                <div class="reviewed-badge">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div>
                                        <div class="txt">Review Submitted</div>
                                        <div class="sub">Thank you for your feedback!</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

            {{-- Hotel contact quick-card --}}
            @if($reservation->hotel?->phone || $reservation->hotel?->email)
            <div class="summary-card">
                <div class="summary-card-body">
                    <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); margin-bottom:0.75rem;">Hotel Contact</div>
                    @if($reservation->hotel->phone)
                    <a href="tel:{{ $reservation->hotel->phone }}"
                       style="display:flex; align-items:center; gap:0.625rem; font-size:0.82rem; color:var(--brand-primary); text-decoration:none; margin-bottom:0.5rem; font-weight:600;">
                        <i class="bi bi-telephone" style="color:var(--brand-accent);"></i>
                        {{ $reservation->hotel->phone }}
                    </a>
                    @endif
                    @if($reservation->hotel->email)
                    <a href="mailto:{{ $reservation->hotel->email }}"
                       style="display:flex; align-items:center; gap:0.625rem; font-size:0.82rem; color:var(--brand-primary); text-decoration:none; font-weight:600;">
                        <i class="bi bi-envelope" style="color:var(--brand-accent);"></i>
                        {{ $reservation->hotel->email }}
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>{{-- /col-lg-4 --}}
    </div>
</div>

{{-- ── Review Modal ────────────────────────────── --}}
@if($reservation->status === 'checked_out' && !$reservation->review)
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
    <div class="modal-content" style="border-radius:1rem; border:none; box-shadow:0 20px 60px rgba(15,23,42,0.15);">

      <div class="modal-header border-0 px-4 pt-4 pb-0">
        <div>
          <h5 class="modal-title mb-0" id="reviewModalLabel"
              style="font-family:'Poppins',sans-serif; font-weight:700; color:var(--brand-primary);">
            Leave a Review
          </h5>
          <p class="text-muted mb-0 mt-1" style="font-size:0.8rem;">
            {{ $reservation->hotel->name ?? 'Your stay' }}
          </p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('client.reservations.reviews.store', $reservation) }}" method="POST">
        @csrf
        <div class="modal-body px-4 py-3">

            {{-- Star rating --}}
            <div class="mb-4">
                <label class="form-label" style="font-size:0.82rem; font-weight:700; color:var(--text-primary); margin-bottom:0.75rem;">
                    Overall Rating <span class="text-danger">*</span>
                </label>
                <div class="star-rating d-flex flex-row-reverse justify-content-end gap-1"
                     style="font-size:2rem; color:#E2E8F0;">
                    <input type="radio" id="star5" name="rating" value="5" class="d-none" required />
                    <label for="star5" class="bi bi-star-fill mb-0" title="5 stars — Excellent" style="cursor:pointer;"></label>
                    <input type="radio" id="star4" name="rating" value="4" class="d-none" />
                    <label for="star4" class="bi bi-star-fill mb-0" title="4 stars — Very Good" style="cursor:pointer;"></label>
                    <input type="radio" id="star3" name="rating" value="3" class="d-none" />
                    <label for="star3" class="bi bi-star-fill mb-0" title="3 stars — Good" style="cursor:pointer;"></label>
                    <input type="radio" id="star2" name="rating" value="2" class="d-none" />
                    <label for="star2" class="bi bi-star-fill mb-0" title="2 stars — Fair" style="cursor:pointer;"></label>
                    <input type="radio" id="star1" name="rating" value="1" class="d-none" />
                    <label for="star1" class="bi bi-star-fill mb-0" title="1 star — Poor" style="cursor:pointer;"></label>
                </div>
                @error('rating')
                    <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Comment --}}
            <div class="mb-2">
                <label for="comment" class="form-label" style="font-size:0.82rem; font-weight:700; color:var(--text-primary);">
                    Your Review <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" id="comment" name="comment" rows="4"
                          placeholder="Tell us what you loved, what could be improved, and any tips for future guests..."
                          required maxlength="1000">{{ old('comment') }}</textarea>
                @error('comment')
                    <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                @enderror
                <div class="text-end mt-1" style="font-size:0.72rem; color:var(--text-muted);">Max 1000 characters</div>
            </div>

        </div>
        <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                  style="border-radius:0.625rem; font-size:0.875rem; font-weight:600;">Cancel</button>
          <button type="submit" class="btn btn-accent"
                  style="border-radius:0.625rem; font-size:0.875rem; font-weight:600; padding:0.55rem 1.5rem;">
              <i class="bi bi-send me-1"></i> Submit Review
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapEl = document.getElementById('hotelMap');
    if (mapEl) {
        const lat = {{ $reservation->hotel->latitude ?? 'null' }};
        const lng = {{ $reservation->hotel->longitude ?? 'null' }};
        if (lat !== null && lng !== null) {
            const map = L.map('hotelMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map)
              .bindPopup("<b>{{ addslashes($reservation->hotel->name ?? '') }}</b>")
              .openPopup();
        }
    }
});
</script>
@endpush
