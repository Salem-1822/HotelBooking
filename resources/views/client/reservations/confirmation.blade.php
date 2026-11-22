@extends('client.layouts.app')

@section('title', __('booking.confirmed_title', ['id' => $reservation->id]))

@push('styles')
<style>
.confirm-hero {
    background: linear-gradient(135deg, #064E3B 0%, #065F46 100%);
    padding: 3.5rem 0 5rem;
    color: #fff;
    text-align: center;
}
.confirm-hero .icon-wrap {
    width: 80px; height: 80px;
    background: rgba(255,255,255,.12);
    border: 2px solid rgba(255,255,255,.25);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.25rem;
    margin: 0 auto 1.25rem;
    animation: popIn .5s cubic-bezier(.2,.8,.2,1) forwards;
}
@keyframes popIn {
    0%   { opacity:0; transform:scale(.6); }
    100% { opacity:1; transform:scale(1); }
}
.confirm-hero h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 2.2rem; font-weight: 800;
    letter-spacing: -.02em;
}
.confirm-hero .ref-badge {
    display: inline-block;
    background: rgba(212,175,55,.18);
    border: 1px solid rgba(212,175,55,.4);
    color: var(--brand-accent);
    font-size: .8rem; font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase;
    padding: .4rem 1.2rem; border-radius: 50px;
    margin-top: .75rem;
}

/* ── Content ── */
.confirm-section {
    margin-top: -2.5rem;
    padding-bottom: 4rem;
}
.confirm-card {
    background: #fff;
    border-radius: 1.25rem;
    border: 1px solid var(--border-color);
    box-shadow: 0 20px 60px rgba(15,23,42,.07), 0 4px 16px rgba(15,23,42,.04);
    overflow: hidden;
    max-width: 680px;
    margin: 0 auto;
}
.confirm-card-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
    padding: 1.25rem 2rem;
    color: #fff;
}
.confirm-card-header h5 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700; margin: 0; font-size: 1rem;
}
.confirm-card-body { padding: 2rem; }
.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .75rem;
    padding: .75rem 0;
    border-bottom: 1px solid var(--border-color);
    font-size: .9rem;
}
.detail-row:last-child { border-bottom: none; }
.detail-row .d-label { color: var(--text-muted); font-weight: 500; flex-shrink: 0; }
.detail-row .d-value { font-weight: 700; color: var(--text-primary); text-align: right; }
@if(app()->getLocale() === 'ar')
.detail-row .d-value { text-align: left; }
@endif
.status-pill {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .35rem 1rem; border-radius: 50px;
    font-size: .78rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
}
.status-pending {
    background: rgba(245,158,11,.12);
    border: 1px solid rgba(245,158,11,.3);
    color: #92400E;
}
.total-row {
    background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
    border-radius: .875rem;
    padding: 1.25rem 1.5rem;
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 1.5rem;
}
.total-row .label { font-weight: 700; color: var(--text-primary); font-size: .95rem; }
.total-row .amount {
    font-family: 'Poppins', sans-serif;
    font-size: 1.75rem; font-weight: 800;
    color: var(--brand-primary);
}
.btn-goto-dashboard {
    background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
    color: #fff; border: none;
    padding: .8rem 2rem; border-radius: .75rem;
    font-weight: 700; font-size: .95rem;
    display: inline-flex; align-items: center; gap: .5rem;
    text-decoration: none;
    transition: all .2s ease;
    box-shadow: 0 4px 16px rgba(15,23,42,.2);
}
.btn-goto-dashboard:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15,23,42,.28);
    color: #fff;
}
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<div class="confirm-hero">
    <div class="container">
        <div class="icon-wrap">
            <i class="bi bi-check-lg"></i>
        </div>
        <h1>{{ __('booking.reservation_submitted') }}</h1>
        <p style="color:rgba(255,255,255,.7); font-size:1rem; margin-top:.5rem;">
            {{ __('booking.reservation_submitted_subtitle') }}
        </p>
        <div class="ref-badge">
            <i class="bi bi-hash"></i> MOR-RSV-{{ $reservation->id }}
        </div>
    </div>
</div>

{{-- ── Details ── --}}
<section class="confirm-section">
    <div class="container">

        @if(session('success'))
            <div class="alert border-0 rounded-3 mb-4 text-center" style="max-width:680px; margin-left:auto; margin-right:auto; background:#F0FDF4; color:#166534; border-left:4px solid #22C55E!important;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="confirm-card">
            <div class="confirm-card-header">
                <h5><i class="bi bi-file-text me-2"></i>{{ __('booking.reservation_details') }}</h5>
            </div>
            <div class="confirm-card-body">

                {{-- Reference --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('booking.reference') }}</span>
                    <span class="d-value">#MOR-RSV-{{ $reservation->id }}</span>
                </div>

                {{-- Status --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('client.status') }}</span>
                    <span class="d-value">
                        <span class="status-pill status-pending">
                            <i class="bi bi-clock-fill"></i>
                            {{ __('booking.status_pending_confirmation') }}
                        </span>
                    </span>
                </div>

                <hr class="my-2" style="border-color:var(--border-color);">

                {{-- Hotel --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('booking.hotel') }}</span>
                    <span class="d-value">
                        {{ $reservation->hotel->name }}
                        @if($reservation->hotel->city)
                            <div style="font-weight:400; color:var(--text-muted); font-size:.82rem;">
                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $reservation->hotel->city->name ?? '' }}
                            </div>
                        @endif
                    </span>
                </div>

                {{-- Room --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('booking.room') }}</span>
                    <span class="d-value">
                        {{ __('booking.room_number', ['number' => $reservation->room->room_number]) }}
                        @if($reservation->room->type)
                            <div style="font-weight:400; color:var(--text-muted); font-size:.82rem;">
                                {{ $reservation->room->type }}
                            </div>
                        @endif
                    </span>
                </div>

                {{-- Check-in --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('booking.check_in') }}</span>
                    <span class="d-value">{{ \Carbon\Carbon::parse($reservation->check_in)->format('l, d M Y') }}</span>
                </div>

                {{-- Check-out --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('booking.check_out') }}</span>
                    <span class="d-value">{{ \Carbon\Carbon::parse($reservation->check_out)->format('l, d M Y') }}</span>
                </div>

                {{-- Duration --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('booking.duration') }}</span>
                    <span class="d-value">
                        @php
                            $nights = \Carbon\Carbon::parse($reservation->check_in)->diffInDays(\Carbon\Carbon::parse($reservation->check_out));
                        @endphp
                        {{ trans_choice('client.nights', $nights) }}
                    </span>
                </div>

                {{-- Guests --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('booking.guests') }}</span>
                    <span class="d-value">{{ trans_choice('hotel.guest_count', $reservation->guests_count) }}</span>
                </div>

                {{-- Guest name (from auth) --}}
                <div class="detail-row">
                    <span class="d-label">{{ __('booking.guest_name') }}</span>
                    <span class="d-value">{{ $reservation->guest_name }}</span>
                </div>

                {{-- Total --}}
                <div class="total-row">
                    <span class="label">
                        <i class="bi bi-wallet2 me-2"></i>{{ __('booking.estimated_total') }}
                    </span>
                    <span class="amount">{{ number_format($reservation->total_price, 2) }} MAD</span>
                </div>

                <p class="text-center text-muted mt-3" style="font-size:.78rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    {{ __('booking.no_payment_required_notice') }}
                </p>

            </div>
        </div>

        <div class="text-center mt-4 d-flex flex-wrap gap-3 justify-content-center">
            <a href="{{ route('client.dashboard') }}" class="btn-goto-dashboard">
                <i class="bi bi-grid-fill"></i>{{ __('booking.go_to_dashboard') }}
            </a>
            <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius:.75rem; font-weight:600;">
                <i class="bi bi-building me-2"></i>{{ __('booking.browse_more_hotels') }}
            </a>
        </div>

    </div>
</section>

@endsection
