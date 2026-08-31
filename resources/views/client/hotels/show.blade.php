@extends('client.layouts.app')

@section('title', $hotel->name)

@push('styles')
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
/* Gallery strip */
.gallery-strip {
    display: flex;
    gap: 0.5rem;
    padding: 0.5rem;
    background: var(--brand-primary);
    overflow-x: auto;
    scrollbar-width: none;
}
.gallery-strip::-webkit-scrollbar { display: none; }
.gallery-thumb {
    flex-shrink: 0;
    width: 100px; height: 70px;
    border-radius: 0.375rem;
    overflow: hidden;
    cursor: pointer;
    opacity: 0.65;
    transition: opacity 0.2s, transform 0.2s;
    border: 2px solid transparent;
}
.gallery-thumb.active, .gallery-thumb:hover {
    opacity: 1;
    border-color: var(--brand-accent);
    transform: scale(1.04);
}
.gallery-thumb img { width: 100%; height: 100%; object-fit: cover; }
.gallery-thumb-placeholder {
    width: 100%; height: 100%;
    background: rgba(255,255,255,0.07);
    display: flex; align-items: center; justify-content: center;
}
.gallery-thumb-placeholder i { color: rgba(255,255,255,0.25); }

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
.room-status-badge.available  { background: #10B981; color: #fff; }
.room-status-badge.occupied,
.room-status-badge.reserved   { background: #F59E0B; color: #fff; }
.room-status-badge.maintenance,
.room-status-badge.inactive   { background: #94A3B8; color: #fff; }

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
.room-name { font-size: 1.1rem; font-weight: 700; color: var(--brand-primary); margin-bottom: 0.4rem; }
.room-meta {
    display: flex; flex-wrap: wrap; gap: 1rem;
    margin-bottom: 0.875rem;
}
.room-meta-item { font-size: 0.83rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem; }
.room-meta-item i { color: var(--brand-accent); }
.room-description { font-size: 0.87rem; color: #475569; flex: 1; margin-bottom: 0.875rem; }
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
    background: var(--brand-accent);
    border: none;
    color: #fff;
    border-radius: 0.625rem;
    font-size: 0.9rem; font-weight: 700;
    padding: 0.625rem 1.5rem;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(212,175,55,0.3);
    white-space: nowrap;
}
.btn-book-now:hover { background: var(--brand-accent-dark); color: #fff; transform: translateY(-1px); }
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
.review-text { font-style: italic; color: #475569; font-size: 0.92rem; margin-bottom: 1rem; }

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
.policy-value { color: var(--text-primary); }

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
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Hotels</a></li>
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
        @php
            $allImages = [];
            if ($hotel->image) $allImages[] = $hotel->image;
            if (!empty($hotel->gallery_images)) {
                foreach ($hotel->gallery_images as $gi) { if ($gi && $gi !== $hotel->image) $allImages[] = $gi; }
            }
        @endphp

        {{-- Main image --}}
        @if(count($allImages) > 0)
            <img id="heroMainImg"
                 src="{{ asset('storage/' . $allImages[0]) }}"
                 class="hero-main-img"
                 alt="{{ $hotel->name }}"
                 onerror="this.parentElement.innerHTML='<div class=\'hero-placeholder\'><i class=\'bi bi-building\'></i></div>'">
        @else
            <div class="hero-placeholder">
                <i class="bi bi-building"></i>
            </div>
        @endif
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
                <span style="color:rgba(255,255,255,0.6); font-size:0.85rem;">({{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }})</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Thumbnail strip for multiple images --}}
    @if(count($allImages) > 1)
    <div class="gallery-strip" id="galleryStrip">
        @foreach($allImages as $idx => $img)
            <div class="gallery-thumb {{ $idx === 0 ? 'active' : '' }}"
                 data-img="{{ asset('storage/' . $img) }}"
                 data-idx="{{ $idx }}"
                 onclick="switchHeroImage(this)"
                 role="button" aria-label="Image {{ $idx + 1 }}">
                <img src="{{ asset('storage/' . $img) }}" alt="Gallery image {{ $idx + 1 }}"
                     onerror="this.parentElement.classList.add('d-none')">
            </div>
        @endforeach
    </div>
    @endif
</div>

<!-- ══════════════════════════════════════════
     Main Content
════════════════════════════════════════════ -->
<div class="container py-4 py-lg-5">
    <div class="row g-4 g-lg-5">

        <!-- Left column: hotel info + rooms + reviews -->
        <div class="col-lg-8">

            <!-- Description -->
            @if($hotel->description)
            <section class="mb-5" aria-label="Hotel description">
                <h2 class="section-heading">About this Hotel</h2>
                <p class="lh-lg" style="color:#475569; font-size:0.97rem;">{{ $hotel->description }}</p>
            </section>
            @endif

            <!-- Amenities -->
            @if(!empty($hotel->amenities))
            <section class="mb-5" aria-label="Amenities">
                <h2 class="section-heading">Amenities &amp; Services</h2>
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
            <section class="mb-5" aria-label="Hotel information">
                <h2 class="section-heading">Hotel Information</h2>
                <div class="row g-4">
                    @if($hotel->check_in_time || $hotel->check_out_time)
                    <div class="col-md-6">
                        @if($hotel->check_in_time)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-door-open me-2 text-accent"></i>Check-in</span>
                            <span class="policy-value">{{ $hotel->check_in_time }}</span>
                        </div>
                        @endif
                        @if($hotel->check_out_time)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-door-closed me-2 text-accent"></i>Check-out</span>
                            <span class="policy-value">{{ $hotel->check_out_time }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($hotel->cancellation_policy || $hotel->children_policy || $hotel->pets_policy || $hotel->smoking_policy)
                    <div class="col-md-6">
                        @if($hotel->cancellation_policy)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-x-circle me-2 text-accent"></i>Cancellation</span>
                            <span class="policy-value">{{ $hotel->cancellation_policy }}</span>
                        </div>
                        @endif
                        @if($hotel->children_policy)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-person-arms-up me-2 text-accent"></i>Children</span>
                            <span class="policy-value">{{ $hotel->children_policy }}</span>
                        </div>
                        @endif
                        @if($hotel->pets_policy)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-heart me-2 text-accent"></i>Pets</span>
                            <span class="policy-value">{{ $hotel->pets_policy }}</span>
                        </div>
                        @endif
                        @if($hotel->smoking_policy)
                        <div class="policy-row">
                            <span class="policy-label"><i class="bi bi-slash-circle me-2 text-accent"></i>Smoking</span>
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
                        <span>{{ $hotel->phone }}</span>
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

            <!-- ── ROOMS ────────────────────────────────────── -->
            <section class="mb-5" aria-label="Available rooms" id="rooms">
                <h2 class="section-heading">Available Rooms</h2>

                @if($rooms->isEmpty())
                    <div class="empty-box">
                        <i class="bi bi-door-open"></i>
                        <p>No rooms are currently listed for this hotel.</p>
                        <a href="{{ route('hotels.index') }}" class="btn btn-sm btn-outline-secondary mt-3" style="border-radius:0.5rem;">
                            Browse Other Hotels
                        </a>
                    </div>
                @else
                    @php $availableCount = $rooms->where('is_bookable', true)->count(); @endphp
                    @if($availableCount > 0)
                    <div class="alert d-flex align-items-center gap-2 mb-3"
                         style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.25); border-radius:0.75rem; font-size:0.875rem;">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span><strong>{{ $availableCount }}</strong> room{{ $availableCount !== 1 ? 's' : '' }} available right now</span>
                    </div>
                    @endif

                    @foreach($rooms as $room)
                    <article class="room-card" aria-label="{{ $room->name ?? 'Room '.$room->room_number }}">
                        {{-- Room image --}}
                        <div class="room-card-img">
                            <div class="room-status-badge {{ $room->status }}">
                                {{ ucfirst($room->status) }}
                            </div>
                            @if($room->main_image)
                                <img src="{{ asset('storage/' . $room->main_image) }}"
                                     alt="{{ $room->name ?? 'Room image' }}"
                                     loading="lazy"
                                     onerror="this.parentElement.innerHTML='<div class=\'room-card-img-placeholder\'><i class=\'bi bi-image\'></i></div>'">
                            @else
                                <div class="room-card-img-placeholder">
                                    <i class="bi bi-door-open"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Room body --}}
                        <div class="room-card-body">
                            @if($room->type)
                                <span class="room-type-badge">{{ $room->type }}</span>
                            @endif

                            <div class="room-name">
                                {{ $room->name ?? 'Room ' . $room->room_number }}
                            </div>

                            <div class="room-meta">
                                @if($room->capacity)
                                <div class="room-meta-item">
                                    <i class="bi bi-person-fill"></i>
                                    Up to {{ $room->capacity }} guest{{ $room->capacity > 1 ? 's' : '' }}
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
                                    Floor {{ $room->floor }}
                                </div>
                                @endif
                            </div>

                            @if($room->description)
                            <p class="room-description">{{ \Illuminate\Support\Str::limit($room->description, 140) }}</p>
                            @else
                            <div style="flex:1;"></div>
                            @endif

                            <div class="room-footer">
                                <div>
                                    <div class="room-price">
                                        <sup>$</sup>{{ number_format($room->price_per_night, 2) }}
                                        <small>/ night</small>
                                    </div>
                                </div>
                                @if($room->is_bookable)
                                    {{-- Phase 4 will add the booking route; for now we anchor to the booking section --}}
                                    <a href="#booking-cta" class="btn-book-now" role="button"
                                       data-room-id="{{ $room->id }}"
                                       data-room-name="{{ $room->name ?? 'Room '.$room->room_number }}"
                                       data-room-price="{{ $room->price_per_night }}"
                                       onclick="selectRoom(this)">
                                        <i class="bi bi-calendar-check me-1"></i> Book Now
                                    </a>
                                @else
                                    <span class="btn-book-now unavailable" aria-disabled="true">
                                        <i class="bi bi-x-circle me-1"></i> Unavailable
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                    @endforeach
                @endif
            </section>

            <!-- ── REVIEWS ──────────────────────────────────── -->
            <section class="mb-5" aria-label="Guest reviews" id="reviews">
                <h2 class="section-heading">Guest Reviews</h2>

                @if($reviewCount === 0)
                    <div class="empty-box">
                        <i class="bi bi-chat-left-quote"></i>
                        <p>No reviews yet for this hotel. Be the first to share your experience after your stay!</p>
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
                            <div class="text-muted mt-1" style="font-size:0.8rem;">{{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }}</div>
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
            <section class="mb-5" aria-label="Similar hotels">
                <h2 class="section-heading">More Hotels in {{ $hotel->city->name ?? 'This City' }}</h2>
                <div class="row g-3">
                    @foreach($relatedHotels as $rh)
                    <div class="col-md-4">
                        <a href="{{ route('hotels.show', $rh) }}" class="related-card">
                            <div class="related-card-img">
                                @if($rh->image)
                                    <img src="{{ asset('storage/' . $rh->image) }}" alt="{{ $rh->name }}"
                                         onerror="this.parentElement.innerHTML='<div class=\'related-card-img-placeholder\'><i class=\'bi bi-building\'></i></div>'">
                                @else
                                    <div class="related-card-img-placeholder">
                                        <i class="bi bi-building"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="related-card-body">
                                <div style="font-weight:700; font-size:0.95rem; color:var(--brand-primary); margin-bottom:0.3rem;">
                                    {{ $rh->name }}
                                </div>
                                @if($rh->avg_rating)
                                <div style="font-size:0.8rem; color:var(--text-muted);">
                                    <i class="bi bi-star-fill text-warning" style="font-size:0.72rem;"></i>
                                    {{ $rh->avg_rating }} &nbsp;·&nbsp; {{ $rh->reviews_count }} reviews
                                </div>
                                @endif
                                @if($rh->starting_price)
                                <div style="font-size:0.9rem; font-weight:700; color:var(--brand-primary); margin-top:0.5rem;">
                                    From ${{ number_format($rh->starting_price, 0) }}<small style="font-weight:400; font-size:0.75rem; color:var(--text-muted);">/night</small>
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
                    <div class="price-from-label">Rooms starting from</div>
                    <div class="price-hero">
                        <sup>$</sup>{{ number_format($minRoomPrice, 2) }}
                        <small>/ night</small>
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
                    <strong>{{ $hotel->stars }}-Star</strong>&nbsp;Hotel
                </div>
                @endif
                @if($avgRating)
                <div class="hotel-quick-fact">
                    <i class="bi bi-chat-left-quote-fill"></i>
                    <strong>{{ $avgRating }}</strong>&nbsp;/ 5 rating
                    <span class="text-muted">({{ $reviewCount }})</span>
                </div>
                @endif
                @if($rooms->isNotEmpty())
                <div class="hotel-quick-fact">
                    <i class="bi bi-door-open-fill"></i>
                    <strong>{{ $rooms->count() }}</strong>&nbsp;room type{{ $rooms->count() !== 1 ? 's' : '' }} available
                </div>
                @endif
                @if($hotel->check_in_time)
                <div class="hotel-quick-fact">
                    <i class="bi bi-clock-fill"></i>
                    Check-in: <strong>{{ $hotel->check_in_time }}</strong>
                </div>
                @endif
                @if($hotel->check_out_time)
                <div class="hotel-quick-fact">
                    <i class="bi bi-clock"></i>
                    Check-out: <strong>{{ $hotel->check_out_time }}</strong>
                </div>
                @endif

                <!-- Selected room feedback -->
                <div id="selectedRoomInfo" class="mt-3 p-2" style="background:rgba(212,175,55,0.07); border:1px solid rgba(212,175,55,0.25); border-radius:0.625rem; display:none; font-size:0.875rem;">
                    <div style="font-weight:600; color:var(--brand-primary); margin-bottom:0.25rem;">Selected Room</div>
                    <div id="selectedRoomName" style="color:var(--text-muted);"></div>
                    <div id="selectedRoomPrice" style="font-weight:700; color:var(--brand-primary);"></div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    @if($rooms->where('is_bookable', true)->count() > 0)
                        <a href="#rooms" class="btn btn-accent py-3" style="border-radius:0.75rem; font-weight:700;">
                            <i class="bi bi-calendar-check me-2"></i> Select a Room
                        </a>
                    @else
                        <button class="btn py-3" style="border-radius:0.75rem; background:#94A3B8; color:#fff; cursor:not-allowed;" disabled>
                            No rooms available
                        </button>
                    @endif
                    <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary py-2"
                       style="border-radius:0.75rem;">
                        ← Back to Hotels
                    </a>
                </div>

                @if($hotel->phone)
                <div class="text-center mt-4" style="font-size:0.82rem; color:var(--text-muted);">
                    Questions? Call us:<br>
                    <a href="tel:{{ $hotel->phone }}" style="color:var(--brand-primary); font-weight:600;">{{ $hotel->phone }}</a>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════
     Mobile Sticky CTA
════════════════════════════════════════════ -->
<div class="sticky-cta-mobile" aria-label="Book room">
    <div>
        @if($minRoomPrice ?? null)
        <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em;">From</div>
        <div style="font-size:1.25rem; font-weight:800; color:var(--brand-primary);">
            ${{ number_format($minRoomPrice, 2) }}<small style="font-size:0.72rem; font-weight:400; color:var(--text-muted);">/night</small>
        </div>
        @endif
    </div>
    @if($rooms->where('is_bookable', true)->count() > 0)
        <a href="#rooms" class="btn btn-accent px-4 py-2" style="border-radius:0.625rem; font-weight:700;">
            <i class="bi bi-calendar-check me-1"></i> Book Now
        </a>
    @else
        <span class="btn px-4 py-2" style="border-radius:0.625rem; background:#94A3B8; color:#fff;">Unavailable</span>
    @endif
</div>

@endsection

@push('scripts')
<script>
/* ── Gallery switcher ──────────────────────────────────────── */
function switchHeroImage(thumbEl) {
    const mainImg = document.getElementById('heroMainImg');
    if (!mainImg) return;
    mainImg.src = thumbEl.dataset.img;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    thumbEl.classList.add('active');
}

/* ── Room selection feedback ───────────────────────────────── */
function selectRoom(btn) {
    const name  = btn.dataset.roomName;
    const price = parseFloat(btn.dataset.roomPrice);
    const panel = document.getElementById('selectedRoomInfo');
    if (!panel) return;
    document.getElementById('selectedRoomName').textContent  = name;
    document.getElementById('selectedRoomPrice').textContent = '$' + price.toFixed(2) + ' / night';
    panel.style.display = 'block';
    // Highlight all book-now buttons; dim others
    document.querySelectorAll('.btn-book-now:not(.unavailable)').forEach(b => {
        b.style.opacity = b === btn ? '1' : '0.5';
    });
}
</script>
@endpush
