@extends('client.layouts.app')

@section('title', 'Luxury Hotels & Resorts')

@push('head')
<link rel="preload" as="image" href="{{ asset('images/hero-bg.webp') }}" fetchpriority="high" media="(min-width: 769px)" type="image/webp">
<link rel="preload" as="image" href="{{ asset('images/hero-bg-mobile.webp') }}" fetchpriority="high" media="(max-width: 768px)" type="image/webp">
@endpush

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        position: relative;
        overflow: hidden;
        background-color: var(--brand-primary);
        padding: 8rem 0 6rem;
        color: #fff;
    }

    .hero-bg-picture {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }

    .hero-bg-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 58, 138, 0.85) 100%);
        z-index: 1;
        pointer-events: none;
    }

    .hero-section .container {
        position: relative;
        z-index: 2;
    }
    
    /* Search Widget */
    .search-widget {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        margin-top: -3rem;
        position: relative;
        z-index: 10;
    }
    
    .search-input-group {
        border-inline-end: 1px solid #E2E8F0;
    }
    
    @media (max-width: 991px) {
        .search-input-group {
            border-inline-end: none;
            border-bottom: 1px solid #E2E8F0;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
    }

    .search-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--brand-secondary);
        margin-bottom: 0.25rem;
        display: block;
    }

    .search-input {
        border: none;
        padding: 0;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1rem;
        box-shadow: none !important;
        background: transparent;
    }

    .search-input:focus {
        background: transparent;
    }
    
    .search-input::placeholder {
        font-weight: 400;
        color: #94A3B8;
    }

    /* Section Titles */
    .section-title {
        font-weight: 800;
        font-size: 2.25rem;
        color: var(--brand-primary);
        margin-bottom: 1rem;
    }
    
    .section-subtitle {
        color: var(--text-muted);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto 3rem;
    }

    /* Destination Cards */
    .destination-card {
        border-radius: 1rem;
        overflow: hidden;
        position: relative;
        height: 320px;
        transition: all 0.3s ease;
        display: block;
    }
    
    .destination-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0) 60%);
        z-index: 1;
        transition: opacity 0.3s ease;
    }

    .destination-card:hover::before {
        background: linear-gradient(to top, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.2) 100%);
    }

    .destination-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .destination-card:hover img {
        transform: scale(1.05);
    }

    .destination-content {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 1.5rem;
        z-index: 2;
        color: #fff;
    }

    .destination-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .destination-count {
        font-size: 0.9rem;
        color: var(--brand-accent);
        font-weight: 500;
    }

    .fallback-img-bg {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
    
    .fallback-img-bg i {
        font-size: 4rem;
        color: rgba(255,255,255,0.1);
    }

    /* Hotel Cards */
    .hotel-card {
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        overflow: hidden;
        background: #fff;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .hotel-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-5px);
    }

    .hotel-img-wrapper {
        position: relative;
        height: 220px;
        overflow: hidden;
    }

    .hotel-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .hotel-card:hover .hotel-img-wrapper img {
        transform: scale(1.05);
    }
    
    .hotel-badge,
    .hotel-stars-badge,
    .category-badge,
    .hotel-card .badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background-color: #0F172A !important;
        color: #FFFFFF !important;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 700 !important;
        z-index: 2;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .hotel-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .hotel-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 0.5rem;
        text-decoration: none;
    }
    
    .hotel-title:hover {
        color: var(--brand-accent);
    }

    .hotel-location {
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }
    
    .hotel-footer {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .hotel-price {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--brand-primary);
    }
    
    .hotel-price span {
        font-size: 0.8rem;
        font-weight: 400;
        color: var(--text-muted);
    }

    /* Stats Section */
    .stats-section {
        background-color: var(--brand-primary);
        color: #fff;
        padding: 5rem 0;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 800;
        color: var(--brand-accent);
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 1rem;
        font-weight: 500;
        color: rgba(255,255,255,0.95);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Features Section */
    .feature-icon {
        width: 64px;
        height: 64px;
        background: rgba(212, 175, 55, 0.1);
        color: var(--brand-accent);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin: 0 auto 1.5rem;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        background: var(--brand-accent);
        color: #fff;
        transform: translateY(-5px);
    }

    /* Review Card */
    .review-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        padding: 2rem;
        height: 100%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        min-width: 0;
        max-width: 100%;
    }
    
    .review-quote {
        color: var(--brand-accent);
        font-size: 2rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }

    .review-comment {
        font-style: italic;
        color: #475569;
        white-space: normal;
        word-break: normal;
        overflow-wrap: anywhere;
        min-width: 0;
        max-width: 100%;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        padding: 5rem 0;
        color: #fff;
        text-align: center;
        border-radius: 1.5rem;
        margin: 5rem 0;
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <picture class="hero-bg-picture">
            <source media="(max-width: 768px)" srcset="{{ asset('images/hero-bg-mobile.webp') }}" type="image/webp">
            <source srcset="{{ asset('images/hero-bg.webp') }}" type="image/webp">
            <img src="{{ asset('images/hero-bg.webp') }}" alt="Luxury Hotel Resort" class="hero-bg-img" width="1600" height="900" fetchpriority="high" decoding="sync">
        </picture>
        <div class="hero-overlay"></div>
        <div class="container">
            <h1 class="display-4 fw-bold mb-3 reveal" style="font-family: 'Poppins', sans-serif;">{{ __('home.hero_title') }}</h1>
            <p class="lead mb-5 mx-auto reveal delay-1" style="max-width: 600px; color: rgba(255,255,255,0.95);">{{ __('home.hero_subtitle') }}</p>
        </div>
    </section>

    <!-- Search Widget -->
    <div class="container">
        <div class="search-widget reveal delay-2">
            <form action="{{ route('hotels.index') }}" method="GET">
                <div class="row g-0 align-items-center">
                    <div class="col-lg-3 col-md-6 px-3 search-input-group">
                        <label for="search_city_id" class="search-label"><i class="bi bi-geo-alt-fill me-1 text-accent" aria-hidden="true"></i> {{ __('home.search.destination') }}</label>
                        <select class="form-select search-input" id="search_city_id" name="city_id" aria-label="{{ __('home.search.destination') }}">
                            <option value="">{{ __('home.search.where_going') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 px-3 search-input-group">
                        <label for="search_check_in" class="search-label"><i class="bi bi-calendar-check me-1 text-accent" aria-hidden="true"></i> {{ __('home.search.check_in') }}</label>
                        <input type="date" class="form-control search-input" id="search_check_in" name="check_in" min="{{ date('Y-m-d') }}" aria-label="{{ __('home.search.check_in') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 px-3 search-input-group">
                        <label for="search_check_out" class="search-label"><i class="bi bi-calendar-check-fill me-1 text-accent" aria-hidden="true"></i> {{ __('home.search.check_out') }}</label>
                        <input type="date" class="form-control search-input" id="search_check_out" name="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}" aria-label="{{ __('home.search.check_out') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 px-3 search-input-group border-0">
                        <label for="search_guests" class="search-label"><i class="bi bi-person-fill me-1 text-accent" aria-hidden="true"></i> {{ __('home.search.guests') }}</label>
                        <select class="form-select search-input" id="search_guests" name="guests" aria-label="{{ __('home.search.guests') }}">
                            <option value="1">{{ trans_choice('hotel.guest_count', 1) }}</option>
                            <option value="2" selected>{{ trans_choice('hotel.guest_count', 2) }}</option>
                            <option value="3">{{ trans_choice('hotel.guest_count', 3) }}</option>
                            <option value="4">4+ {{ __('hotel.guests') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-1 px-3 mt-3 mt-lg-0 text-center text-lg-end">
                        <button type="submit" class="btn btn-accent w-100 py-3" style="border-radius: 0.875rem;" title="{{ __('home.search.search_hotels') }}" aria-label="{{ __('home.search.search_hotels') }}">
                            <i class="bi bi-search fs-5" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Destinations Section -->
    <section id="destinations" class="py-5 mt-5">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <h2 class="section-title">{{ __('home.destinations.title') }}</h2>
                <p class="section-subtitle">{{ __('home.destinations.subtitle') }}</p>
            </div>
            
            @if($cities->count() > 0)
                <div class="row g-4">
                    @foreach($cities as $index => $city)
                        @php
                            $cityImgUrl = $city->image_url;
                            if (str_contains($cityImgUrl, 'images.unsplash.com')) {
                                $cityImgUrl = preg_match('/w=\d+/', $cityImgUrl)
                                    ? preg_replace('/w=\d+/', 'w=500&q=75&fm=webp', $cityImgUrl)
                                    : $cityImgUrl . (str_contains($cityImgUrl, '?') ? '&' : '?') . 'w=500&q=75&fm=webp';
                            }
                        @endphp
                        <div class="{{ $index % 4 == 0 || $index % 4 == 3 ? 'col-lg-7' : 'col-lg-5' }} col-md-6 reveal" style="transition-delay: {{ ($index % 4) * 0.1 }}s;">
                            <a href="{{ route('hotels.index', ['city_id' => $city->id]) }}" class="destination-card" aria-label="{{ $city->name }} - {{ __('home.destinations.hotels_count', ['count' => $city->hotels_count]) }}">
                                <img src="{{ $cityImgUrl }}" width="500" height="300" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?w=500&q=75&fm=webp';" alt="{{ $city->name }}">
                                <div class="destination-content">
                                    <h3 class="destination-title">{{ $city->name }}</h3>
                                    <div class="destination-count">{{ __('home.destinations.hotels_count', ['count' => $city->hotels_count]) }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 bg-white" style="border-radius: 1rem; border: 1px dashed var(--border-color);">
                    <i class="bi bi-map text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">{{ __('home.destinations.subtitle') }}</h5>
                </div>
            @endif
        </div>
    </section>

    <!-- Featured Hotels -->
    <section id="hotels" class="py-5 bg-white">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between mb-5 reveal">
                <div>
                    <h2 class="section-title mb-2">{{ __('home.featured.title') }}</h2>
                    <p class="text-muted mb-0">{{ __('home.featured.subtitle') }}</p>
                </div>
                <a href="{{ route('hotels.index') }}" class="btn btn-outline-dark d-none d-md-inline-block" style="border-radius: 2rem; padding: 0.5rem 1.5rem; font-weight: 500;" aria-label="{{ __('home.featured.title') }} - {{ __('common.all') }}">{{ __('common.all') }} <i class="bi bi-arrow-right ms-1"></i></a>
            </div>

            @if($featuredHotels->count() > 0)
                <div class="row g-4">
                    @foreach($featuredHotels as $index => $hotel)
                        <div class="col-lg-4 col-md-6 reveal" style="transition-delay: {{ $index * 0.1 }}s;">
                            <div class="hotel-card">
                                <div class="hotel-img-wrapper">
                                    @if($hotel->stars)
                                        <div class="hotel-badge" style="background-color: #0F172A !important; color: #FFFFFF !important; font-weight: 700 !important;"><i class="bi bi-star-fill me-1" style="color:#F59E0B !important;" aria-hidden="true"></i> {{ $hotel->stars }} {{ __('common.stars') }}</div>
                                    @endif
                                    @php
                                        $hotelImgUrl = $hotel->image_url;
                                        if (str_contains($hotelImgUrl, 'images.unsplash.com')) {
                                            $hotelImgUrl = preg_match('/w=\d+/', $hotelImgUrl)
                                                ? preg_replace('/w=\d+/', 'w=500&q=75&fm=webp', $hotelImgUrl)
                                                : $hotelImgUrl . (str_contains($hotelImgUrl, '?') ? '&' : '?') . 'w=500&q=75&fm=webp';
                                        }
                                    @endphp
                                    <img src="{{ $hotelImgUrl }}" width="400" height="220" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1542314831-c6a4d27a6582?w=500&q=75&fm=webp';" alt="{{ $hotel->name }}">
                                </div>
                                <div class="hotel-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h3 class="h5 mb-0"><a href="{{ route('hotels.show', $hotel) }}" class="hotel-title">{{ $hotel->name }}</a></h3>
                                        @if($hotel->avg_rating)
                                            <div class="d-flex align-items-center bg-light px-2 py-1 rounded" style="font-size: 0.85rem; font-weight: 600;">
                                                <i class="bi bi-star-fill text-warning me-1"></i> {{ number_format($hotel->avg_rating, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="hotel-location"><i class="bi bi-geo-alt me-1"></i> {{ $hotel->city->name ?? '' }}</div>
                                    
                                    <div class="hotel-footer">
                                        <div class="hotel-price">
                                            {{ number_format($hotel->starting_price ?? $hotel->price_per_night, 2) }} {{ __('common.currency') }} <span>{{ __('common.per_night') }}</span>
                                        </div>
                                        <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-sm btn-accent" style="border-radius: 0.5rem;" aria-label="{{ __('home.featured.view_details') }}: {{ $hotel->name }}">{{ __('home.featured.view_details') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4 d-md-none">
                    <a href="{{ route('hotels.index') }}" class="btn btn-outline-dark" style="border-radius: 2rem; padding: 0.5rem 1.5rem;" aria-label="{{ __('home.featured.title') }} - {{ __('common.all') }}">{{ __('common.all') }}</a>
                </div>
            @else
                <div class="text-center py-5 bg-light" style="border-radius: 1rem; border: 1px dashed var(--border-color);">
                    <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">{{ __('home.featured.no_hotels') }}</h5>
                </div>
            @endif
        </div>
    </section>

    <!-- Statistics -->
    <section class="stats-section" id="stats-section">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6 reveal delay-1">
                    <div class="stat-item">
                        <div class="stat-number animated-counter" data-target="{{ $stats['total_hotels'] ?? 0 }}">0</div>
                        <div class="stat-label">{{ __('home.stats.total_hotels') }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 reveal delay-2">
                    <div class="stat-item">
                        <div class="stat-number animated-counter" data-target="{{ $stats['total_cities'] ?? 0 }}">0</div>
                        <div class="stat-label">{{ __('home.stats.destinations') }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 reveal delay-3">
                    <div class="stat-item">
                        <div class="stat-number animated-counter" data-target="{{ $stats['total_rooms'] ?? 0 }}">0</div>
                        <div class="stat-label">{{ __('home.stats.luxury_rooms') }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 reveal delay-4">
                    <div class="stat-item">
                        <div class="stat-number animated-counter" data-target="{{ $stats['total_guests'] ?? 0 }}">0</div>
                        <div class="stat-label">{{ __('home.stats.happy_guests') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-5 my-5">
        <div class="container text-center">
            <h2 class="section-title reveal">{{ __('home.features.title') }}</h2>
            <p class="section-subtitle reveal delay-1">{{ __('home.features.subtitle') }}</p>
            
            <div class="row g-4 mt-2">
                <div class="col-lg-4 col-md-6 reveal delay-1">
                    <div class="feature-card px-3">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-bold mb-3" style="font-size: 1.25rem;">{{ __('home.features.secure_title') }}</h3>
                        <p class="text-muted">{{ __('home.features.secure_desc') }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 reveal delay-2">
                    <div class="feature-card px-3">
                        <div class="feature-icon">
                            <i class="bi bi-tags" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-bold mb-3" style="font-size: 1.25rem;">{{ __('home.features.price_title') }}</h3>
                        <p class="text-muted">{{ __('home.features.price_desc') }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 offset-md-3 offset-lg-0 reveal delay-3">
                    <div class="feature-card px-3">
                        <div class="feature-icon">
                            <i class="bi bi-headset" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-bold mb-3" style="font-size: 1.25rem;">{{ __('home.features.support_title') }}</h3>
                        <p class="text-muted">{{ __('home.features.support_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="section-title text-center reveal">{{ __('home.reviews.title') }}</h2>
            <p class="section-subtitle text-center reveal delay-1">{{ __('home.reviews.subtitle') }}</p>
            
            <div class="row g-4 mt-2">
                @forelse($guestReviews as $index => $review)
                    <div class="col-lg-4 col-md-6 reveal" style="transition-delay: {{ $index * 0.1 }}s;">
                        <div class="review-card">
                            <i class="bi bi-quote review-quote"></i>
                            <div class="mb-3 text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <p class="review-comment mb-4">"{{ \Illuminate\Support\Str::limit($review->comment, 150, '...') }}"</p>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? 'Guest') }}&background=0F172A&color=fff" class="rounded-circle me-3" width="48" height="48" alt="{{ $review->user->name ?? 'Guest' }}" loading="lazy" decoding="async">
                                <div>
                                    <div class="mb-0 fw-bold">{{ $review->user->name ?? 'Guest' }}</div>
                                    <small class="text-muted">{{ __('home.reviews.stayed_at', ['hotel' => $review->hotel->name ?? 'Hotel']) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 reveal delay-1">
                        <div class="text-center py-5" style="border-radius: 1rem; border: 1px dashed var(--border-color); background: #fff;">
                            <i class="bi bi-chat-square-quote" style="font-size: 3rem; color: var(--brand-accent); opacity: 0.5;"></i>
                            <h5 class="mt-3 mb-2" style="color: var(--brand-primary); font-weight: 700;">{{ __('home.reviews.no_reviews_title') }}</h5>
                            <p class="text-muted mb-0" style="max-width: 380px; margin: 0 auto;">{{ __('home.reviews.no_reviews_desc') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <div class="container">
        <section class="cta-section reveal">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">{{ __('home.cta.title') }}</h2>
                    <p class="lead mb-5 mx-auto" style="color: rgba(255,255,255,0.8); max-width: 500px;">{{ __('home.cta.subtitle') }}</p>
                    <a href="{{ route('hotels.index') }}" class="btn btn-accent btn-lg px-5 py-3" style="border-radius: 0.875rem;">{{ __('home.cta.button') }}</a>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statsSection = document.getElementById('stats-section');
            if (!statsSection) return;

            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            const counters = statsSection.querySelectorAll('.animated-counter');
            let hasAnimated = false;

            // Immediately show final values without animation
            const showFinalValues = () => {
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'), 10) || 0;
                    const suffix = counter.getAttribute('data-suffix') || '';
                    counter.textContent = target + suffix;
                });
            };

            // Smooth count-up using requestAnimationFrame + easeOutExpo
            const animateCounters = () => {
                if (hasAnimated) return;
                hasAnimated = true;

                const DURATION = 1500; // ms

                const easeOutExpo = (t) => t === 1 ? 1 : 1 - Math.pow(2, -10 * t);

                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'), 10) || 0;
                    const suffix = counter.getAttribute('data-suffix') || '';

                    if (target === 0) {
                        counter.textContent = '0' + suffix;
                        return;
                    }

                    const startTime = performance.now();

                    const tick = (now) => {
                        const elapsed = now - startTime;
                        const progress = Math.min(elapsed / DURATION, 1);
                        const easedProgress = easeOutExpo(progress);
                        const current = Math.round(target * easedProgress);

                        counter.textContent = current + suffix;

                        if (progress < 1) {
                            requestAnimationFrame(tick);
                        } else {
                            counter.textContent = target + suffix;
                        }
                    };

                    requestAnimationFrame(tick);
                });
            };

            if (prefersReducedMotion) {
                // Skip animation — show final values immediately
                showFinalValues();
                return;
            }

            // Observe the stats section entering the viewport.
            // Delay start by ~1.2s to let the reveal fade-in (delay-4 = 0.4s + 0.8s transition)
            // finish before counting begins, so numbers appear after elements are visible.
            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !hasAnimated) {
                        // The last stat column has delay-4 (0.4s) + 0.8s transition = 1.2s
                        setTimeout(animateCounters, 1200);
                        sectionObserver.unobserve(statsSection);
                    }
                });
            }, { threshold: 0.2 });

            sectionObserver.observe(statsSection);
        });

        // Date sync on Home search widget
        const homeCheckIn = document.querySelector('.search-widget input[name="check_in"]');
        const homeCheckOut = document.querySelector('.search-widget input[name="check_out"]');
        if (homeCheckIn && homeCheckOut) {
            homeCheckIn.addEventListener('change', function () {
                if (this.value) {
                    homeCheckOut.min = this.value;
                    if (homeCheckOut.value && homeCheckOut.value <= this.value) {
                        homeCheckOut.value = '';
                    }
                }
            });
        }
    </script>
    @endpush
@endsection
