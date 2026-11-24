@extends('client.layouts.app')

@section('title', __('booking.book_room_title', ['number' => $room->room_number]))

@push('styles')
<style>
/* ── Page wrapper ── */
.reserve-hero {
    background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
    padding: 3rem 0 4.5rem;
    color: #fff;
}
.reserve-hero h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.02em;
}
.reserve-hero .breadcrumb-item a { color: rgba(255,255,255,.6); text-decoration: none; }
.reserve-hero .breadcrumb-item a:hover { color: var(--brand-accent); }
.reserve-hero .breadcrumb-item.active { color: rgba(255,255,255,.9); }
.reserve-hero .breadcrumb-separator { color: rgba(255,255,255,.35); }

/* ── Main layout ── */
.reserve-section {
    margin-top: -2.5rem;
    padding-bottom: 4rem;
}

/* ── Form Card ── */
.form-card {
    background: #fff;
    border-radius: 1.25rem;
    border: 1px solid var(--border-color);
    box-shadow: 0 20px 60px rgba(15,23,42,.07), 0 4px 16px rgba(15,23,42,.04);
    overflow: hidden;
}
.form-card-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, #1e3a8a 100%);
    padding: 1.5rem 2rem;
    color: #fff;
}
.form-card-header h5 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    margin: 0;
}
.form-card-body { padding: 2rem; }

/* ── Labels / inputs ── */
.field-label {
    font-weight: 600;
    font-size: 0.8rem;
    color: #374151;
    letter-spacing: .01em;
    margin-bottom: .35rem;
}
.input-icon-wrap { position: relative; }
.input-icon-wrap .field-icon {
    position: absolute;
    inset-inline-start: 1rem;
    inset-inline-end: auto;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF; font-size: 1rem;
    pointer-events: none; z-index: 2;
}
.input-icon-wrap .form-control {
    padding-inline-start: 2.75rem;
    padding-inline-end: 1rem;
    border-radius: .75rem;
    border: 1.5px solid var(--border-color);
    background: #F9FAFB;
    font-size: .875rem;
    transition: all .2s ease;
}
.input-icon-wrap .form-control:focus {
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 4px rgba(15,23,42,.08);
    background: #fff;
}
.input-icon-wrap .form-control.is-invalid { border-color: #EF4444; background: #FFF5F5; }
.invalid-feedback { font-size: .78rem; color: #DC2626; margin-top: .3rem; }

/* ── Submit ── */
.btn-reserve {
    background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
    color: #fff; border: none;
    padding: .85rem; border-radius: .75rem;
    font-weight: 700; font-size: 1rem;
    width: 100%; transition: all .2s ease;
    box-shadow: 0 4px 16px rgba(15,23,42,.2);
    font-family: 'Poppins', sans-serif;
}
.btn-reserve:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15,23,42,.28);
    filter: brightness(1.07); color: #fff;
}

/* ── Summary card (right column) ── */
.summary-card {
    background: #fff;
    border-radius: 1.25rem;
    border: 1px solid var(--border-color);
    box-shadow: 0 8px 30px rgba(15,23,42,.06);
    position: sticky;
    top: 96px;
}
.summary-card-header {
    background: linear-gradient(135deg, var(--brand-accent) 0%, #B8860B 100%);
    padding: 1.25rem 1.5rem;
    border-radius: 1.25rem 1.25rem 0 0;
    color: #fff;
}
.summary-card-header h6 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    margin: 0;
    font-size: .95rem;
}
.summary-card-body { padding: 1.5rem; }
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .5rem;
    margin-bottom: .85rem;
    font-size: .875rem;
}
.summary-row .label { color: var(--text-muted); flex-shrink: 0; }
.summary-row .value { font-weight: 600; color: var(--text-primary); text-align: right; }
@if(app()->getLocale() === 'ar')
.summary-row .value { text-align: left; }
@endif
.summary-total {
    border-top: 2px solid var(--border-color);
    padding-top: 1rem;
    margin-top: .5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.summary-total .label { font-weight: 700; font-size: .9rem; }
.summary-total .value { font-family: 'Poppins', sans-serif; font-size: 1.4rem; font-weight: 800; color: var(--brand-primary); }
.nights-badge {
    display: inline-block;
    background: rgba(212,175,55,.12);
    border: 1px solid rgba(212,175,55,.3);
    color: #92700E;
    font-size: .72rem; font-weight: 700;
    padding: .2rem .7rem;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-top: .35rem;
}
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<div class="reserve-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb" style="background:none; padding:0; margin:0; font-size:.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('navigation.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">{{ __('navigation.hotels') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->name }}</a></li>
                <li class="breadcrumb-item active">{{ __('booking.confirm_reservation') }}</li>
            </ol>
        </nav>
        <h1><i class="bi bi-calendar-check me-2" style="color:var(--brand-accent);"></i>{{ __('booking.complete_your_reservation') }}</h1>
        <p style="color:rgba(255,255,255,.65); margin-top:.5rem; font-size:.95rem;">
            {!! __('booking.one_step_away', ['hotel' => '<strong style="color:var(--brand-accent);">' . e($hotel->name) . '</strong>']) !!}
        </p>
    </div>
</div>

{{-- ── Main section ── --}}
<section class="reserve-section">
    <div class="container">
        <div class="row g-4 align-items-start">

            {{-- ── Left: Form ── --}}
            <div class="col-lg-7">
                <div class="form-card">
                    <div class="form-card-header">
                        <h5><i class="bi bi-pencil-square me-2"></i>{{ __('booking.reservation_details') }}</h5>
                    </div>
                    <div class="form-card-body">

                        @if($errors->any())
                            <div class="alert alert-danger border-0 rounded-3 mb-4" style="font-size:.875rem; border-inline-start:4px solid #EF4444!important; background:#FEF2F2; color:#991B1B;">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('client.reserve.store', [$hotel, $room]) }}" id="reservationForm">
                            @csrf

                            {{-- Guest info (read-only, from auth) --}}
                            <div class="mb-4 p-3" style="background:#F8FAFC; border-radius:.875rem; border:1px solid var(--border-color);">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('web')->user()->name) }}&background=0F172A&color=fff&size=48"
                                         class="rounded-circle" width="48" height="48" alt="{{ Auth::guard('web')->user()->name }}" loading="lazy" decoding="async">
                                    <div>
                                        <div style="font-weight:700; color:var(--text-primary);">{{ Auth::guard('web')->user()->name }}</div>
                                        <div style="font-size:.82rem; color:var(--text-muted);">{{ Auth::guard('web')->user()->email }}</div>
                                        <div style="font-size:.75rem; margin-top:.2rem;">
                                            <span style="background:rgba(34,197,94,.1); color:#16A34A; padding:.15rem .6rem; border-radius:50px; font-weight:600; font-size:.7rem;">
                                                <i class="bi bi-shield-check-fill me-1"></i>{{ __('booking.verified_guest') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Check-in --}}
                            <div class="mb-3">
                                <label for="check_in" class="field-label">{{ __('booking.check_in_date') }}</label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-calendar-event field-icon"></i>
                                    <input type="date" name="check_in" id="check_in"
                                           class="form-control @error('check_in') is-invalid @enderror"
                                           value="{{ old('check_in', $prefill['check_in'] ?? '') }}"
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                </div>
                                @error('check_in')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Check-out --}}
                            <div class="mb-3">
                                <label for="check_out" class="field-label">{{ __('booking.check_out_date') }}</label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-calendar-x field-icon"></i>
                                    <input type="date" name="check_out" id="check_out"
                                           class="form-control @error('check_out') is-invalid @enderror"
                                           value="{{ old('check_out', $prefill['check_out'] ?? '') }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           required>
                                </div>
                                @error('check_out')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Guests --}}
                            <div class="mb-3">
                                <label for="guests_count" class="field-label">
                                    {{ __('booking.number_of_guests') }}
                                    <span style="font-weight:400; color:var(--text-muted);">{{ __('booking.max_capacity', ['capacity' => $room->capacity]) }}</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-people-fill field-icon"></i>
                                    <input type="number" name="guests_count" id="guests_count"
                                           class="form-control @error('guests_count') is-invalid @enderror"
                                           value="{{ old('guests_count', min($prefill['guests'] ?? 1, $room->capacity)) }}"
                                           min="1" max="{{ $room->capacity }}"
                                           required>
                                </div>
                                @error('guests_count')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone (optional, prefilled from account) --}}
                            <div class="mb-4">
                                <label for="phone" class="field-label">
                                    {{ __('booking.contact_phone') }}
                                    <span style="font-weight:400; color:var(--text-muted);">{{ __('booking.optional') }}</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-telephone field-icon"></i>
                                    <input type="tel" name="phone" id="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', Auth::guard('web')->user()->phone ?? '') }}"
                                           dir="ltr" style="text-align:start;"
                                           placeholder="{{ __('booking.phone_placeholder') }}">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div style="font-size:.75rem; color:var(--text-muted); margin-top:.3rem;">
                                    <i class="bi bi-info-circle me-1"></i>{{ __('booking.hotel_contact_notice') }}
                                </div>
                            </div>

                            <button type="submit" class="btn-reserve">
                                <i class="bi bi-calendar-check me-2"></i>{{ __('booking.confirm_reservation') }}
                            </button>

                            <p class="text-center text-muted mt-3" style="font-size:.78rem;">
                                <i class="bi bi-shield-lock me-1"></i>
                                {{ __('booking.secure_data_note') }}
                            </p>
                        </form>

                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('hotels.show', $hotel) }}" style="font-size:.85rem; color:var(--text-muted); text-decoration:none;">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('booking.back_to_hotel', ['hotel' => $hotel->name]) }}
                    </a>
                </div>
            </div>

            {{-- ── Right: Summary ── --}}
            <div class="col-lg-5">
                <div class="summary-card">
                    <div class="summary-card-header">
                        <h6><i class="bi bi-receipt me-2"></i>{{ __('booking.booking_summary') }}</h6>
                    </div>
                    <div class="summary-card-body">

                        {{-- Hotel --}}
                        <div class="summary-row">
                            <span class="label">{{ __('booking.hotel') }}</span>
                            <span class="value">{{ $hotel->name }}</span>
                        </div>

                        {{-- Location --}}
                        @if($hotel->city)
                        <div class="summary-row">
                            <span class="label">{{ __('booking.location') }}</span>
                            <span class="value">{{ $hotel->city->name }}</span>
                        </div>
                        @endif

                        <hr style="margin:.5rem 0 1rem; border-color:var(--border-color);">

                        {{-- Room --}}
                        <div class="summary-row">
                            <span class="label">{{ __('booking.room') }}</span>
                            <span class="value">{{ __('booking.room_number', ['number' => $room->room_number]) }}</span>
                        </div>

                        {{-- Type --}}
                        @if($room->type)
                        <div class="summary-row">
                            <span class="label">{{ __('booking.type') }}</span>
                            <span class="value">{{ $room->type }}</span>
                        </div>
                        @endif

                        {{-- Capacity --}}
                        <div class="summary-row">
                            <span class="label">{{ __('booking.capacity') }}</span>
                            <span class="value">{{ trans_choice('hotel.up_to_guests', $room->capacity, ['count' => $room->capacity]) }}</span>
                        </div>

                        {{-- Bed --}}
                        @if($room->bed_type)
                        <div class="summary-row">
                            <span class="label">{{ __('booking.bed_type') }}</span>
                            <span class="value">{{ $room->bed_type }}</span>
                        </div>
                        @endif

                        <hr style="margin:.5rem 0 1rem; border-color:var(--border-color);">

                        {{-- Price per night --}}
                        <div class="summary-row">
                            <span class="label">{{ __('booking.rate') }}</span>
                            <span class="value">{{ number_format($room->price_per_night, 2) }} {{ __('common.currency') }} {{ __('hotel.per_night') }}</span>
                        </div>

                        {{-- Dynamic total (JS) --}}
                        <div class="summary-total">
                            <span class="label">{{ __('booking.estimated_total') }}</span>
                            <div class="text-end">
                                <div class="value" id="totalDisplay">—</div>
                                <div id="nightsBadge" class="nights-badge d-none">— {{ trans_choice('client.nights', 2) }}</div>
                            </div>
                        </div>

                        <p class="text-muted mt-3 mb-0" style="font-size:.75rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            {{ __('booking.final_total_notice') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
(function () {
    const pricePerNight = {{ (float) $room->price_per_night }};
    const checkIn  = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    const totalEl  = document.getElementById('totalDisplay');
    const badgeEl  = document.getElementById('nightsBadge');

    function updateTotal() {
        const ci = checkIn.value  ? new Date(checkIn.value)  : null;
        const co = checkOut.value ? new Date(checkOut.value) : null;
        if (!ci || !co || co <= ci) {
            totalEl.textContent = '—';
            badgeEl.classList.add('d-none');
            return;
        }
        const nights = Math.round((co - ci) / 86400000);
        const total  = nights * pricePerNight;
        totalEl.textContent = total.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ' + '{{ __('common.currency') }}';
        badgeEl.textContent = nights + ' ' + (nights === 1 ? '{{ app()->getLocale() === 'fr' ? 'nuit' : (app()->getLocale() === 'ar' ? 'ليلة' : 'night') }}' : '{{ app()->getLocale() === 'fr' ? 'nuits' : (app()->getLocale() === 'ar' ? 'ليالٍ' : 'nights') }}');
        badgeEl.classList.remove('d-none');

        // keep check_out min = check_in + 1
        const minOut = new Date(ci);
        minOut.setDate(minOut.getDate() + 1);
        checkOut.min = minOut.toISOString().split('T')[0];
    }

    checkIn.addEventListener('change', updateTotal);
    checkOut.addEventListener('change', updateTotal);

    // Trigger on page load if old input is present
    updateTotal();
})();
</script>
@endpush
