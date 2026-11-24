@extends('client.layouts.app')

@section('title', $hotel->name)

@push('head')
<link rel="preload" as="image" href="{{ $hotel->image_url }}" fetchpriority="high">
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
/* ══════════════════════════════════════════
   Hero / Gallery
══════════════════════════════════════════ */
.hotel-hero {
    background: var(--brand-primary);
    position: relative;
    overflow: hidden;
}
.hero-gallery-wrap {
    position: relative;
    height: 480px;
}
@media (max-width: 768px) { .hero-gallery-wrap { height: 280px; } }

.hero-main-img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.hero-placeholder {
    width: 100%; height: 100%;
    background: linear-gradient(135deg, #0F172A 0%, #1e3a5f 100%);
    display: flex; align-items: center; justify-content: center;
}
.hero-placeholder i { font-size: 6rem; color: rgba(255,255,255,0.08); }
.hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(15,23,42,0.88) 0%, rgba(15,23,42,0.2) 60%, transparent 100%);
}
.hero-content {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 2rem;
    color: #fff;
    z-index: 2;
}

/* ══════════════════════════════════════════
   Breadcrumb + stars badge
══════════════════════════════════════════ */
.breadcrumb-bar {
    background: var(--brand-primary);
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.breadcrumb-bar .breadcrumb { margin: 0; }
.breadcrumb-bar .breadcrumb-item a { color: rgba(255,255,255,0.55); text-decoration: none; }
.breadcrumb-bar .breadcrumb-item a:hover { color: var(--brand-accent); }
.breadcrumb-bar .breadcrumb-item.active { color: rgba(255,255,255,0.85); }
.breadcrumb-bar .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.3); }

/* ══════════════════════════════════════════
   Hotel Summary Panel
══════════════════════════════════════════ */
.hotel-summary-panel {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 1.75rem;
    box-shadow: var(--card-shadow);
    position: sticky;
    top: 90px;
}
.price-from-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }
.price-hero { font-size: 2.25rem; font-weight: 800; color: var(--brand-primary); line-height: 1; }
.price-hero sup { font-size: 1rem; font-weight: 600; vertical-align: super; }
.price-hero small { font-size: 0.8rem; font-weight: 400; color: var(--text-muted); }
.hotel-quick-fact {
    display: flex; align-items: center; gap: 0.625rem;
    font-size: 0.875rem; color: var(--text-muted);
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
}
.hotel-quick-fact:last-of-type { border-bottom: none; }
.hotel-quick-fact i { color: var(--brand-accent); width: 18px; }
.hotel-quick-fact strong { color: var(--text-primary); }

/* ══════════════════════════════════════════
   Section headings
══════════════════════════════════════════ */
.section-heading {
    font-size: 1.35rem; font-weight: 700;
    color: var(--brand-primary);
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--border-color);
}
.section-heading::after {
    content: '';
    display: block;
    width: 40px; height: 3px;
    background: var(--brand-accent);
    border-radius: 2px;
    margin-top: 0.5rem;
}

/* ══════════════════════════════════════════
   Amenity Chips
══════════════════════════════════════════ */
.amenity-chip {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: rgba(212,175,55,0.08);
    border: 1px solid rgba(212,175,55,0.25);
    color: var(--brand-primary);
    border-radius: 2rem;
    padding: 0.35rem 0.875rem;
    font-size: 0.82rem; font-weight: 500;
    white-space: nowrap;
}
.amenity-chip i { color: var(--brand-accent); font-size: 0.85rem; }

/* ══════════════════════════════════════════
   Room Cards
══════════════════════════════════════════ */
.room-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    overflow: hidden;
    display: flex;
    flex-direction: row;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
    margin-bottom: 1.25rem;
}
.room-card:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-3px);
}
.room-card-img {
    width: 220px; flex-shrink: 0;
    position: relative; overflow: hidden;
}
.room-card-img img { width: 100%; height: 100%; object-fit: cover; }
.room-card-img-placeholder {
    width: 100%; height: 100%; min-height: 160px;
    background: linear-gradient(135deg, #0F172A 0%, #1e3a5f 100%);
    display: flex; align-items: center; justify-content: center;
}
.room-card-img-placeholder i { font-size: 2.5rem; color: rgba(255,255,255,0.1); }
.room-status-badge {
    position: absolute; top: 0.75rem; left: 0.75rem;
    border-radius: 2rem;
    font-size: 0.72rem; font-weight: 700;
    padding: 0.2rem 0.625rem;
    letter-spacing: 0.02em;
}
@if(app()->getLocale() === 'ar')
.room-status-badge { left: auto; right: 0.75rem; }
@endif
.room-status-badge.available  { background: #059669; color: #fff; font-weight: 600; }
.room-status-badge.occupied,
.room-status-badge.reserved   { background: #F59E0B; color: #0F172A; font-weight: 700; }
.room-status-badge.maintenance,
.room-status-badge.inactive   { background: #64748B; color: #fff; }

.room-card-body {
    padding: 1.25rem 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.room-type-badge {
    display: inline-block;
    background: rgba(15,23,42,0.06);
    color: var(--brand-secondary);
    border-radius: 0.375rem;
    padding: 0.2rem 0.625rem;
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    margin-bottom: 0.5rem;
}
.room-title { font-size: 1.1rem; font-weight: 700; color: var(--brand-primary); margin-bottom: 0.4rem; word-break: break-word; overflow-wrap: anywhere; max-width: 100%; }
.room-meta {
    display: flex; flex-wrap: wrap; gap: 1rem;
    margin-bottom: 0.875rem;
}
.room-meta-item { font-size: 0.83rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem; }
.room-meta-item i { color: var(--brand-accent); }
.room-description { font-size: 0.87rem; color: #475569; flex: 1; margin-bottom: 0.875rem; word-break: break-word; overflow-wrap: anywhere; white-space: pre-line; max-width: 100%; }
.room-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: 0.875rem;
    border-top: 1px solid var(--border-color);
    gap: 1rem;
    flex-wrap: wrap;
}
.room-price { font-size: 1.35rem; font-weight: 800; color: var(--brand-primary); }
.room-price sup { font-size: 0.8rem; font-weight: 600; vertical-align: super; }
.room-price small { font-size: 0.75rem; font-weight: 400; color: var(--text-muted); }
.btn-book-now {
    background: var(--brand-accent) !important;
    border: none;
    color: #0F172A !important;
    border-radius: 0.625rem;
    font-size: 0.9rem; font-weight: 700 !important;
    padding: 0.625rem 1.5rem;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(212,175,55,0.3);
    white-space: nowrap;
}
.btn-book-now:hover { background: var(--brand-accent-dark) !important; color: #0F172A !important; transform: translateY(-1px); }
.btn-book-now:disabled, .btn-book-now.unavailable {
    background: #94A3B8; box-shadow: none; cursor: not-allowed; opacity: 0.7;
}
@media (max-width: 768px) {
    .room-card { flex-direction: column; }
    .room-card-img { width: 100%; height: 180px; }
}

/* ══════════════════════════════════════════
   Rating Summary
══════════════════════════════════════════ */
.rating-big { font-size: 4rem; font-weight: 800; color: var(--brand-primary); line-height: 1; }
.rating-bar-row { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.4rem; }
.rating-bar-label { font-size: 0.78rem; color: var(--text-muted); width: 12px; }
.rating-bar-track { flex: 1; height: 6px; background: var(--border-color); border-radius: 3px; overflow: hidden; }
.rating-bar-fill { height: 100%; background: var(--brand-accent); border-radius: 3px; transition: width 0.8s ease; }
.rating-bar-count { font-size: 0.75rem; color: var(--text-muted); width: 24px; text-align: right; }

/* ══════════════════════════════════════════
   Review Cards
══════════════════════════════════════════ */
.review-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
}
.review-quote-icon { color: var(--brand-accent); font-size: 1.75rem; opacity: 0.3; margin-bottom: 0.75rem; }
.review-text { font-style: italic; color: #475569; font-size: 0.92rem; margin-bottom: 1rem; word-break: break-word; overflow-wrap: anywhere; white-space: pre-line; max-width: 100%; }

/* ══════════════════════════════════════════
   Related Hotels
══════════════════════════════════════════ */
.related-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 0.875rem;
    overflow: hidden;
    display: flex; flex-direction: column;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
    text-decoration: none;
    color: inherit;
}
.related-card:hover { box-shadow: var(--card-shadow-hover); transform: translateY(-3px); color: inherit; }
.related-card-img { height: 140px; overflow: hidden; }
.related-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
.related-card:hover .related-card-img img { transform: scale(1.04); }
.related-card-img-placeholder {
    height: 100%;
    background: linear-gradient(135deg, var(--brand-primary), #1e3a5f);
    display: flex; align-items: center; justify-content: center;
}
.related-card-img-placeholder i { font-size: 2rem; color: rgba(255,255,255,0.1); }
.related-card-body { padding: 1rem; flex: 1; }

/* ══════════════════════════════════════════
   Policies table
══════════════════════════════════════════ */
.policy-row { display: flex; gap: 1rem; padding: 0.625rem 0; border-bottom: 1px solid var(--border-color); font-size: 0.875rem; }
.policy-row:last-child { border-bottom: none; }
.policy-label { width: 160px; flex-shrink: 0; color: var(--text-muted); font-weight: 500; }
.policy-value { color: var(--text-primary); word-break: break-word; overflow-wrap: anywhere; min-width: 0; }

/* ══════════════════════════════════════════
   Mobile sticky CTA
══════════════════════════════════════════ */
.sticky-cta-mobile {
    display: none;
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #fff;
    border-top: 1px solid var(--border-color);
    padding: 1rem 1.5rem;
    z-index: 900;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
    align-items: center; justify-content: space-between; gap: 1rem;
}
@media (max-width: 991px) { .sticky-cta-mobile { display: flex; } }
@media (max-width: 991px) { body { padding-bottom: 90px; } }

/* ══════════════════════════════════════════
   Empty states
══════════════════════════════════════════ */
.empty-box {
    text-align: center; padding: 3rem 2rem;
    background: #f8fafc; border: 1px dashed var(--border-color);
    border-radius: 1rem;
}
.empty-box i { font-size: 2.5rem; color: #CBD5E1; margin-bottom: 1rem; display: block; }
.empty-box p { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
</style>
@endpush

@section('content')

<!-- ══════════════════════════════════════════
     Breadcrumb bar
════════════════════════════════════════════ -->
<div class="breadcrumb-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('hotel.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">{{ __('hotel.hotels') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($hotel->name, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- ══════════════════════════════════════════
     Hero Gallery
════════════════════════════════════════════ -->
<div class="hotel-hero">
    <div class="hero-gallery-wrap" id="heroGallery">

        {{-- Main hotel photo --}}
        @php
            $heroHotelImg = $hotel->image_url;
            if (str_contains($heroHotelImg, 'images.unsplash.com')) {
                $heroHotelImg = preg_match('/w=\d+/', $heroHotelImg)
                    ? preg_replace('/w=\d+/', 'w=1200&q=75&fm=webp', $heroHotelImg)
                    : $heroHotelImg . (str_contains($heroHotelImg, '?') ? '&' : '?') . 'w=1200&q=75&fm=webp';
            }
        @endphp
        <img id="heroMainImg"
             src="{{ $heroHotelImg }}"
             class="hero-main-img"
             alt="{{ $hotel->name }}"
             width="1200"
             height="480"
             fetchpriority="high"
             loading="eager"
             decoding="async"
             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&h=800&fit=crop&fm=webp&q=75'">
        <div class="hero-overlay"></div>

        {{-- Hero content overlay --}}
        <div class="hero-content">
            @if($hotel->stars)
                <div class="mb-2">
                    @for($i = 1; $i <= $hotel->stars; $i++)
                        <i class="bi bi-star-fill text-warning" style="font-size:0.9rem;"></i>
                    @endfor
                </div>
            @endif
            <h1 style="font-size:2rem; font-weight:800; margin-bottom:0.4rem;">{{ $hotel->name }}</h1>
            <div style="color:rgba(255,255,255,0.8); font-size:0.95rem;">
                <i class="bi bi-geo-alt-fill me-1 text-warning"></i>
                {{ $hotel->city->name ?? '' }}
                @if($hotel->address)&nbsp;·&nbsp; {{ $hotel->address }}@endif
            </div>
            @if($avgRating)
            <div class="d-flex align-items-center gap-2 mt-2">
                <div class="d-flex align-items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgRating) ? '-fill' : '' }}"
                           style="color:#F59E0B; font-size:0.82rem;"></i>
                    @endfor
                </div>
                <span style="font-weight:700; color:#fff;">{{ $avgRating }}</span>
                <span style="color:rgba(255,255,255,0.6); font-size:0.85rem;">({{ trans_choice('hotel.reviews_count', $reviewCount) }})</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     Main Content
════════════════════════════════════════════ -->
<div class="container py-4 py-lg-5">
    <div class="row g-4 g-lg-5">

        <!-- Left column: hotel info + rooms + reviews -->
        <div class="col-lg-8 min-w-0" style="min-width: 0;">

            <!-- Description -->
            @if($hotel->description)
            <section class="mb-5 overflow-hidden" style="max-width: 100%;">
                <h2 class="section-heading">{{ __('hotel.about_this_hotel') }}</h2>
                <div class="hotel-description-content break-words overflow-hidden max-w-full"
                     style="color:#475569; font-size:0.975rem; line-height:1.8; word-break: break-word; overflow-wrap: anywhere; word-wrap: break-word; max-width: 100%;">
                    @php
                        $paragraphs = array_filter(preg_split('/\r\n\r\n|\n\n|\r\r/', $hotel->description), fn($p) => trim($p) !== '');
                    @endphp
                    @foreach($paragraphs as $paragraph)
                        <p class="{{ $loop->last ? 'mb-0' : 'mb-3' }}" style="color:#475569; line-height:1.8; word-break: break-word; overflow-wrap: anywhere; word-wrap: break-word;">{!! nl2br(e(trim($paragraph))) !!}</p>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Amenities -->
            @if(!empty($hotel->amenities))
            <section class="mb-5">
                <h2 class="section-heading">{{ __('hotel.amenities_services') }}</h2>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $amenityIcons = [
                            'wifi' => 'bi-wifi', 'pool' => 'bi-droplet-fill', 'spa' => 'bi-stars',
                            'gym' => 'bi-bicycle', 'parking' => 'bi-p-square', 'restaurant' => 'bi-cup-hot-fill',
                            'bar' => 'bi-cup-straw', 'breakfast' => 'bi-egg-fried', 'ac' => 'bi-thermometer-snow',
                            'laundry' => 'bi-washing-machine', 'concierge' => 'bi-person-badge',
                            'airport shuttle' => 'bi-airplane', 'pet friendly' => 'bi-heart',
                            'room service' => 'bi-bell', 'conference room' => 'bi-building',
                        ];
                        $amenities = is_array($hotel->amenities) ? $hotel->amenities : [];
                    @endphp
                    @foreach($amenities as $amenity)
                        @php
                            $lc   = strtolower(trim($amenity));
                            $icon = $amenityIcons[$lc] ?? 'bi-check-circle';
                        @endphp
                        <span class="amenity-chip">
                            <i class="bi {{ $icon }}"></i>
                            {{ ucfirst($amenity) }}
                        </span>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Hotel Info / Policies -->
            @php
                $hasPolicies = $hotel->check_in_time || $hotel->check_out_time
                    || $hotel->cancellation_policy || $hotel->children_policy
                    || $hotel->pets_policy || $hotel->smoking_policy;
            @endphp
            @if($hasPolicies || $hotel->phone || $hotel->email)
            <section class="mb-5">
                <h2 class="section-heading">{{ __('hotel.hotel_information') }}</h2>
                <div class="row g-4">
                    @if($hotel->check_in_time || $hotel->check_out_time)
                    <div class="col-md-6">
                        @if($hotel->check_in_time)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-door-open me-2 text-accent"></i>{{ __('hotel.check_in') }}</span>
                            <span class="policy-value">{{ $hotel->check_in_time }}</span>
                        </div>
                        @endif
                        @if($hotel->check_out_time)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-door-closed me-2 text-accent"></i>{{ __('hotel.check_out') }}</span>
                            <span class="policy-value">{{ $hotel->check_out_time }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($hotel->cancellation_policy || $hotel->children_policy || $hotel->pets_policy || $hotel->smoking_policy)
                    <div class="col-md-6">
                        @if($hotel->cancellation_policy)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-x-circle me-2 text-accent"></i>{{ __('hotel.cancellation') }}</span>
                            <span class="policy-value">{{ $hotel->cancellation_policy }}</span>
                        </div>
                        @endif
                        @if($hotel->children_policy)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-person-arms-up me-2 text-accent"></i>{{ __('hotel.children') }}</span>
                            <span class="policy-value">{{ $hotel->children_policy }}</span>
                        </div>
                        @endif
                        @if($hotel->pets_policy)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-heart me-2 text-accent"></i>{{ __('hotel.pets') }}</span>
                            <span class="policy-value">{{ $hotel->pets_policy }}</span>
                        </div>
                        @endif
                        @if($hotel->smoking_policy)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-slash-circle me-2 text-accent"></i>{{ __('hotel.smoking') }}</span>
                            <span class="policy-value">{{ $hotel->smoking_policy }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @if($hotel->phone || $hotel->email)
                <div class="d-flex flex-wrap gap-4 mt-3">
                    @if($hotel->phone)
                    <div class="hotel-quick-fact">
                        <i class="bi bi-telephone-fill"></i>
                        <span dir="ltr">{{ $hotel->phone }}</span>
                    </div>
                    @endif
                    @if($hotel->email)
                    <div class="hotel-quick-fact">
                        <i class="bi bi-envelope-fill"></i>
                        <a href="mailto:{{ $hotel->email }}" style="color:var(--text-muted); text-decoration:none;">{{ $hotel->email }}</a>
                    </div>
                    @endif
                </div>
                @endif
            </section>
            @endif

            <!-- Location Section -->
            <section class="mb-5" id="location">
                <h2 class="section-heading">{{ __('hotel.location') }}</h2>
                @if($hotel->latitude && $hotel->longitude)
                    <div class="mb-3" style="font-size: 0.95rem; color: #475569;">
                        <i class="bi bi-geo-alt-fill text-accent me-1"></i>
                        {{ $hotel->address ?? 'Location coordinates provided' }}
                    </div>
                    <div id="hotelMap" style="height: 350px; border-radius: 1rem; border: 1px solid var(--border-color); z-index: 1;"></div>
                    <div class="mt-3 text-end">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $hotel->latitude }},{{ $hotel->longitude }}" 
                           target="_blank" rel="noopener noreferrer" 
                           class="btn btn-outline-secondary btn-sm" 
                           style="border-radius:0.5rem; font-weight: 500;">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('hotel.view_on_google_maps') }}
                        </a>
                    </div>
                @elseif($hotel->address)
                    <div class="mb-3" style="font-size: 0.95rem; color: #475569;">
                        <i class="bi bi-geo-alt-fill text-accent me-1"></i>
                        {{ $hotel->address }}
                    </div>
                    <div class="alert" style="background:rgba(100,116,139,0.08); border:1px solid rgba(100,116,139,0.25); border-radius:0.75rem; font-size:0.875rem;">
                        <i class="bi bi-info-circle-fill me-2 text-secondary"></i> {{ __('hotel.location_map_not_available') }}
                    </div>
                @else
                    <div class="alert" style="background:rgba(100,116,139,0.08); border:1px solid rgba(100,116,139,0.25); border-radius:0.75rem; font-size:0.875rem;">
                        <i class="bi bi-info-circle-fill me-2 text-secondary"></i> {{ __('hotel.location_info_not_available') }}
                    </div>
                @endif
            </section>

            <section class="mb-5" id="rooms">
                <h2 class="section-heading">{{ __('hotel.available_rooms') }}</h2>

                {{-- Show date context if dates were provided --}}
                @if(!empty($validated['check_in']) && !empty($validated['check_out']))
                    @php
                        $ci = \Carbon\Carbon::parse($validated['check_in']);
                        $co = \Carbon\Carbon::parse($validated['check_out']);
                        $nights = $ci->diffInDays($co);
                    @endphp
                    <div class="alert d-flex align-items-center gap-2 mb-3"
                         style="background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.25); border-radius:0.75rem; font-size:0.875rem;">
                        <i class="bi bi-calendar-range-fill" style="color:#3B82F6;"></i>
                        <span>
                            {{ __('hotel.showing_availability_for') }} <strong>{{ $ci->format('d M Y') }}</strong> <i class="bi bi-arrow-right mx-1 rtl-flip text-muted"></i> <strong>{{ $co->format('d M Y') }}</strong>
                            ({{ trans_choice('client.nights', $nights) }})
                            @if(!empty($validated['guests']))
                                · <strong>{{ trans_choice('hotel.guest_count', (int)$validated['guests']) }}</strong>
                            @endif
                        </span>
                        <a href="{{ route('hotels.show', $hotel) }}" class="ms-auto text-muted" style="font-size:0.78rem; text-decoration:none; white-space:nowrap;">
                            <i class="bi bi-x"></i> {{ __('hotel.clear_dates') }}
                        </a>
                    </div>
                @endif

                @if($rooms->isEmpty())
                    <div class="empty-box">
                        <i class="bi bi-door-open"></i>
                        <p>{{ __('hotel.no_rooms_listed_for_hotel') }}</p>
                        <a href="{{ route('hotels.index') }}" class="btn btn-sm btn-outline-secondary mt-3" style="border-radius:0.5rem;">
                            {{ __('hotel.browse_other_hotels') }}
                        </a>
                    </div>
                @else
                    @php $availableCount = $rooms->where('is_bookable', true)->count(); @endphp
                    @if($availableCount > 0)
                    <div class="alert d-flex align-items-center gap-2 mb-3"
                         style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.25); border-radius:0.75rem; font-size:0.875rem;">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>
                            {!! trans_choice('hotel.rooms_available_count', $availableCount, ['count' => $availableCount]) !!}
                            @if(!empty($validated['check_in']) && !empty($validated['check_out']))
                                {{ __('hotel.for_your_dates') }}
                            @else
                                {{ __('hotel.right_now') }}
                            @endif
                        </span>
                    </div>
                    @elseif(!empty($validated['check_in']) && !empty($validated['check_out']))
                    <div class="alert d-flex align-items-center gap-2 mb-3"
                         style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.25); border-radius:0.75rem; font-size:0.875rem;">
                        <i class="bi bi-x-circle-fill text-danger"></i>
                        <span>{{ __('hotel.no_rooms_for_dates') }}</span>
                    </div>
                    @endif

                    @foreach($rooms as $room)
                    <article class="room-card">
                        {{-- Room image --}}
                        <div class="room-card-img">
                            @php
                                $displayStatus = $room->is_bookable ? 'available' : ($room->status === 'available' ? 'reserved' : $room->status);
                                $displayLabel  = $room->is_bookable ? __('hotel.room_available_badge') : (!empty($validated['check_in']) && !empty($validated['check_out']) ? __('hotel.room_reserved_badge') : ucfirst($room->status));
                            @endphp
                            <div class="room-status-badge {{ $displayStatus }}">
                                {{ $displayLabel }}
                            </div>
                            @php
                                $roomCardImg = $room->main_image_url;
                                if (str_contains($roomCardImg, 'images.unsplash.com')) {
                                    $roomCardImg = preg_match('/w=\d+/', $roomCardImg)
                                        ? preg_replace('/w=\d+/', 'w=220&q=75&fm=webp', $roomCardImg)
                                        : $roomCardImg . (str_contains($roomCardImg, '?') ? '&' : '?') . 'w=220&q=75&fm=webp';
                                }
                            @endphp
                            <img src="{{ $roomCardImg }}"
                                 alt="Room {{ $room->room_number }}"
                                 width="220"
                                 height="160"
                                 loading="lazy"
                                 decoding="async"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1590490360182-c33d57733427?w=220&h=160&fit=crop&fm=webp&q=75'">
                        </div>

                        {{-- Room body --}}
                        <div class="room-card-body">
                            @if($room->type)
                                <span class="room-type-badge">{{ $room->type }}</span>
                            @endif

                            <div class="room-title">
                                {{ __('client.room', ['number' => $room->room_number]) }}
                            </div>

                            <div class="room-meta">
                                @if($room->capacity)
                                <div class="room-meta-item">
                                    <i class="bi bi-person-fill"></i>
                                    {{ trans_choice('hotel.up_to_guests', $room->capacity, ['count' => $room->capacity]) }}
                                </div>
                                @endif
                                @if($room->bed_type)
                                <div class="room-meta-item">
                                    <i class="bi bi-moon-stars-fill"></i>
                                    {{ $room->bed_type }}
                                </div>
                                @endif
                                @if($room->size)
                                <div class="room-meta-item">
                                    <i class="bi bi-arrows-fullscreen"></i>
                                    {{ number_format($room->size) }} m²
                                </div>
                                @endif
                                @if($room->floor)
                                <div class="room-meta-item">
                                    <i class="bi bi-layers-fill"></i>
                                    {{ __('hotel.floor', ['number' => $room->floor]) }}
                                </div>
                                @endif
                            </div>

                            @if($room->description)
                            <p class="room-description break-words overflow-wrap-break-word whitespace-pre-line max-w-full" style="word-break: break-word; overflow-wrap: anywhere; white-space: pre-line; max-width: 100%;">{{ \Illuminate\Support\Str::limit($room->description, 140) }}</p>
                            @else
                            <div style="flex:1;"></div>
                            @endif

                            <div class="room-footer">
                                <div>
                                    <div class="room-price">
                                        {{ number_format($room->price_per_night, 2) }} {{ __('common.currency') }}
                                        <small>{{ __('hotel.per_night') }}</small>
                                    </div>
                                </div>
                                @if($room->is_bookable)
                                    @php
                                        $bookParams = array_filter([
                                            'check_in'  => $validated['check_in'] ?? null,
                                            'check_out' => $validated['check_out'] ?? null,
                                            'guests'    => $validated['guests'] ?? null,
                                        ]);
                                        $queryStr = count($bookParams) > 0 ? '?' . http_build_query($bookParams) : '';
                                    @endphp
                                    @if(Auth::guard('admin')->check())
                                        <button type="button"
                                                class="btn-book-now disabled"
                                                disabled
                                                title="{{ __('hotel.clients_only_title') }}"
                                                style="opacity: 0.65; cursor: not-allowed; background: #64748B; border-color: #64748B;">
                                            <i class="bi bi-shield-lock me-1"></i> {{ __('hotel.clients_only') }}
                                        </button>
                                    @elseif(Auth::guard('web')->check() && Auth::guard('web')->user()->role === 'client')
                                        <a href="{{ route('client.reserve.create', [$hotel, $room]) }}{{ $queryStr }}"
                                           class="btn-book-now"
                                           data-room-id="{{ $room->id }}"
                                           data-room-title="{{ __('client.room', ['number' => $room->room_number]) }}"
                                           data-room-price="{{ $room->price_per_night }}"
                                           title="{{ __('hotel.book_room') }}">
                                            <i class="bi bi-calendar-check me-1"></i> {{ __('hotel.book_room') }}
                                        </a>
                                    @else
                                        <a href="{{ route('client.reserve.create', [$hotel, $room]) }}{{ $queryStr }}"
                                           class="btn-book-now"
                                           title="{{ __('hotel.sign_in_to_book') }}">
                                            <i class="bi bi-calendar-check me-1"></i> {{ __('hotel.book_room') }}
                                        </a>
                                    @endif
                                @else
                                    <span class="btn-book-now unavailable" aria-disabled="true">
                                        <i class="bi bi-x-circle me-1"></i> {{ __('hotel.unavailable') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                    @endforeach
                @endif
            </section>

            <!-- ── REVIEWS ──────────────────────────────────── -->
            <section class="mb-5" id="reviews">
                <h2 class="section-heading">{{ __('hotel.guest_reviews') }}</h2>

                @if($reviewCount === 0)
                    <div class="empty-box">
                        <i class="bi bi-chat-left-quote"></i>
                        <p>{{ __('hotel.no_reviews_yet_desc') }}</p>
                    </div>
                @else
                    {{-- Rating summary --}}
                    <div class="row align-items-center mb-4 g-4">
                        <div class="col-auto text-center">
                            <div class="rating-big">{{ $avgRating }}</div>
                            <div class="d-flex justify-content-center gap-1 mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= round($avgRating) ? '-fill' : '' }}"
                                       style="color:#F59E0B; font-size:0.9rem;"></i>
                                @endfor
                            </div>
                            <div class="text-muted mt-1" style="font-size:0.8rem;">{{ trans_choice('hotel.reviews_count', $reviewCount) }}</div>
                        </div>
                        <div class="col">
                            @for($s = 5; $s >= 1; $s--)
                            <div class="rating-bar-row">
                                <span class="rating-bar-label">{{ $s }}</span>
                                <div class="rating-bar-track">
                                    <div class="rating-bar-fill"
                                         style="width:{{ $ratingBreakdown[$s]['percent'] ?? 0 }}%"></div>
                                </div>
                                <span class="rating-bar-count">{{ $ratingBreakdown[$s]['count'] ?? 0 }}</span>
                            </div>
                            @endfor
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach($reviews as $review)
                        <div class="col-md-6">
                            <div class="review-card">
                                <i class="bi bi-quote review-quote-icon"></i>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"
                                           style="color:#F59E0B; font-size:0.8rem;"></i>
                                    @endfor
                                </div>
                                <p class="review-text">
                                    "{{ \Illuminate\Support\Str::limit($review->comment, 160) }}"
                                </p>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? 'Guest') }}&background=0F172A&color=fff&size=40"
                                         class="rounded-circle" width="40" height="40"
                                         alt="{{ $review->user->name ?? 'Guest' }}">
                                    <div>
                                        <div style="font-weight:600; font-size:0.9rem;">{{ $review->user->name ?? 'Guest' }}</div>
                                        <div style="font-size:0.78rem; color:var(--text-muted);">
                                            {{ $review->created_at->format('M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- ── RELATED HOTELS ───────────────────────────── -->
            @if($relatedHotels->isNotEmpty())
            <section class="mb-5">
                <h2 class="section-heading">{{ __('hotel.more_hotels_in', ['city' => $hotel->city->name ?? __('hotel.this_city')]) }}</h2>
                <div class="row g-3">
                    @foreach($relatedHotels as $rh)
                    <div class="col-md-4">
                        @php
                            $rhImg = $rh->image_url;
                            if (str_contains($rhImg, 'images.unsplash.com')) {
                                $rhImg = preg_match('/w=\d+/', $rhImg)
                                    ? preg_replace('/w=\d+/', 'w=300&q=75&fm=webp', $rhImg)
                                    : $rhImg . (str_contains($rhImg, '?') ? '&' : '?') . 'w=300&q=75&fm=webp';
                            }
                        @endphp
                        <a href="{{ route('hotels.show', $rh) }}" class="related-card">
                            <div class="related-card-img">
                                <img src="{{ $rhImg }}" alt="{{ $rh->name }}"
                                     width="300"
                                     height="140"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=300&h=140&fit=crop&fm=webp&q=75'">
                            </div>
                            <div class="related-card-body">
                                <div style="font-weight:700; font-size:0.95rem; color:var(--brand-primary); margin-bottom:0.3rem;">
                                    {{ $rh->name }}
                                </div>
                                @if($rh->avg_rating)
                                <div style="font-size:0.8rem; color:var(--text-muted);">
                                    <i class="bi bi-star-fill text-warning" style="font-size:0.72rem;"></i>
                                    {{ $rh->avg_rating }} &nbsp;·&nbsp; {{ trans_choice('hotel.reviews_count', $rh->reviews_count) }}
                                </div>
                                @endif
                                @if($rh->starting_price)
                                <div style="font-size:0.9rem; font-weight:700; color:var(--brand-primary); margin-top:0.5rem;">
                                    {{ __('hotel.from_price', ['price' => number_format($rh->starting_price, 0)]) }}<small style="font-weight:400; font-size:0.75rem; color:var(--text-muted);">{{ __('hotel.per_night') }}</small>
                                </div>
                                @endif
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

        </div><!-- /col-lg-8 -->

        <!-- Right column: summary + CTA panel -->
        <div class="col-lg-4">
            <div class="hotel-summary-panel" id="booking-cta">
                <div class="mb-3">
                    @php
                        $minRoomPrice = $rooms->where('is_bookable', true)->min('price_per_night')
                            ?? $rooms->min('price_per_night')
                            ?? $hotel->price_per_night;
                    @endphp
                    @if($minRoomPrice)
                    <div class="price-from-label">{{ __('hotel.rooms_starting_from') }}</div>
                    <div class="price-hero">
                        {{ number_format($minRoomPrice, 2) }} {{ __('common.currency') }}
                        <small>{{ __('hotel.per_night') }}</small>
                    </div>
                    @endif
                </div>

                {{-- Quick facts --}}
                @if($hotel->city)
                <div class="hotel-quick-fact">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>{{ $hotel->city->name }}</span>
                </div>
                @endif
                @if($hotel->stars)
                <div class="hotel-quick-fact">
                    <i class="bi bi-star-fill"></i>
                    <strong>{{ __('hotel.star_hotel', ['stars' => $hotel->stars]) }}</strong>
                </div>
                @endif
                @if($avgRating)
                <div class="hotel-quick-fact">
                    <i class="bi bi-chat-left-quote-fill"></i>
                    <strong>{{ __('hotel.rating_out_of_5', ['rating' => $avgRating]) }}</strong>
                    <span class="text-muted">({{ $reviewCount }})</span>
                </div>
                @endif
                @if($rooms->isNotEmpty())
                <div class="hotel-quick-fact">
                    <i class="bi bi-door-open-fill"></i>
                    <strong>{!! trans_choice('hotel.room_types_available', $rooms->count(), ['count' => $rooms->count()]) !!}</strong>
                </div>
                @endif
                @if($hotel->check_in_time)
                <div class="hotel-quick-fact">
                    <i class="bi bi-clock-fill"></i>
                    {{ __('hotel.check_in') }}: <strong>{{ $hotel->check_in_time }}</strong>
                </div>
                @endif
                @if($hotel->check_out_time)
                <div class="hotel-quick-fact">
                    <i class="bi bi-clock"></i>
                    {{ __('hotel.check_out') }}: <strong>{{ $hotel->check_out_time }}</strong>
                </div>
                @endif

                <!-- Selected room feedback -->
                <div id="selectedRoomInfo" class="mt-3 p-2" style="background:rgba(212,175,55,0.07); border:1px solid rgba(212,175,55,0.25); border-radius:0.625rem; display:none; font-size:0.875rem;">
                    <div style="font-weight:600; color:var(--brand-primary); margin-bottom:0.25rem;">{{ __('hotel.selected_room') }}</div>
                    <div id="selectedRoomTitle" style="color:var(--text-muted);"></div>
                    <div id="selectedRoomPrice" style="font-weight:700; color:var(--brand-primary);"></div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    @if($rooms->where('is_bookable', true)->count() > 0)
                        <a href="#rooms" class="btn btn-accent py-3" style="border-radius:0.75rem; font-weight:700;">
                            <i class="bi bi-calendar-check me-2"></i> {{ __('hotel.select_a_room') }}
                        </a>
                    @else
                        <button class="btn py-3" style="border-radius:0.75rem; background:#94A3B8; color:#fff; cursor:not-allowed;" disabled>
                            {{ __('hotel.no_rooms_available') }}
                        </button>
                    @endif
                    <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary py-2"
                       style="border-radius:0.75rem;">
                        <i class="bi bi-arrow-left me-1 rtl-flip"></i>{{ __('hotel.back_to_hotels') }}
                    </a>
                </div>

                @if($hotel->phone)
                <div class="text-center mt-4" style="font-size:0.82rem; color:var(--text-muted);">
                    {{ __('hotel.questions_call_us') }}<br>
                    <a href="tel:{{ $hotel->phone }}" dir="ltr" style="color:var(--brand-primary); font-weight:600;">{{ $hotel->phone }}</a>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════
     Mobile Sticky CTA
════════════════════════════════════════════ -->
<div class="sticky-cta-mobile">
    <div>
        @if($minRoomPrice ?? null)
        <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em;">{{ __('hotel.from') }}</div>
        <div style="font-size:1.25rem; font-weight:800; color:var(--brand-primary);">
            {{ number_format($minRoomPrice, 2) }} {{ __('common.currency') }}<small style="font-size:0.72rem; font-weight:400; color:var(--text-muted);">{{ __('hotel.per_night') }}</small>
        </div>
        @endif
    </div>
    @if($rooms->where('is_bookable', true)->count() > 0)
        <a href="#rooms" class="btn btn-accent px-4 py-2" style="border-radius:0.625rem; font-weight:700;">
            <i class="bi bi-calendar-check me-1"></i> {{ __('hotel.book_now') }}
        </a>
    @else
        <span class="btn px-4 py-2" style="border-radius:0.625rem; background:#94A3B8; color:#fff;">{{ __('hotel.unavailable') }}</span>
    @endif
</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}" src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="" defer></script>
<script nonce="{{ $cspNonce }}">
/* ── Room selection feedback ───────────────────────────────── */
function selectRoom(btn) {
    const title = btn.dataset.roomTitle;
    const price = parseFloat(btn.dataset.roomPrice);
    const panel = document.getElementById('selectedRoomInfo');
    if (!panel) return;
    document.getElementById('selectedRoomTitle').textContent = title;
    document.getElementById('selectedRoomPrice').textContent = price.toFixed(2) + ' ' + '{{ __('common.currency') }} {{ __('hotel.per_night') }}';
    panel.style.display = 'block';
    // Highlight all book-now buttons; dim others
    document.querySelectorAll('.btn-book-now:not(.unavailable)').forEach(b => {
        b.style.opacity = b === btn ? '1' : '0.5';
    });
}

/* ── Location Map ─────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
    const mapEl = document.getElementById('hotelMap');
    if (mapEl) {
        const lat = {{ $hotel->latitude ?? 'null' }};
        const lng = {{ $hotel->longitude ?? 'null' }};
        
        if (lat !== null && lng !== null) {
            const map = L.map('hotelMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            
            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>{{ addslashes($hotel->name) }}</b>")
                .openPopup();
        }
    }
});
</script>
@endpush
