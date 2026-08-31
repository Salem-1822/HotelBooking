@extends('client.layouts.app')

@section('title', 'Luxury Hotels & Resorts')

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 58, 138, 0.8) 100%), 
                    url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
        padding: 8rem 0 6rem;
        color: #fff;
        position: relative;
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
        border-right: 1px solid #E2E8F0;
    }
    
    @media (max-width: 991px) {
        .search-input-group {
            border-right: none;
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
    
    .hotel-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: var(--brand-accent);
        color: #fff;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
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
        color: rgba(255,255,255,0.8);
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
    }
    
    .review-quote {
        color: var(--brand-accent);
        font-size: 2rem;
        opacity: 0.3;
        margin-bottom: 1rem;
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
        <div class="container">
            <h1 class="display-4 fw-bold mb-3" style="font-family: 'Poppins', sans-serif;">Discover Your Perfect Stay</h1>
            <p class="lead mb-5 mx-auto" style="max-width: 600px; color: rgba(255,255,255,0.8);">Experience world-class luxury and comfort at our carefully selected hotels across top destinations.</p>
        </div>
    </section>

    <!-- Search Widget -->
    <div class="container">
        <div class="search-widget">
            <form action="{{ route('home') }}" method="GET">
                <div class="row g-0 align-items-center">
                    <div class="col-lg-3 col-md-6 px-3 search-input-group">
                        <label class="search-label"><i class="bi bi-geo-alt-fill me-1 text-accent"></i> Destination</label>
                        <select class="form-select search-input" name="city_id">
                            <option value="">Where are you going?</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 px-3 search-input-group">
                        <label class="search-label"><i class="bi bi-calendar-check me-1 text-accent"></i> Check-in</label>
                        <input type="date" class="form-control search-input" name="check_in" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 px-3 search-input-group">
                        <label class="search-label"><i class="bi bi-calendar-check-fill me-1 text-accent"></i> Check-out</label>
                        <input type="date" class="form-control search-input" name="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                    <div class="col-lg-2 col-md-6 px-3 search-input-group border-0">
                        <label class="search-label"><i class="bi bi-person-fill me-1 text-accent"></i> Guests</label>
                        <select class="form-select search-input" name="guests">
                            <option value="1">1 Guest</option>
                            <option value="2" selected>2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4+ Guests</option>
                        </select>
                    </div>
                    <div class="col-lg-1 px-3 mt-3 mt-lg-0 text-center text-lg-end">
                        <button type="button" class="btn btn-accent w-100 py-3" style="border-radius: 0.875rem;">
                            <i class="bi bi-search fs-5"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Destinations Section -->
    <section id="destinations" class="py-5 mt-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Popular Destinations</h2>
                <p class="section-subtitle">Explore our top locations and find the perfect setting for your next unforgettable getaway.</p>
            </div>
            
            @if($cities->count() > 0)
                <div class="row g-4">
                    @foreach($cities as $index => $city)
                        <div class="{{ $index == 0 || $index == 3 ? 'col-lg-7' : 'col-lg-5' }} col-md-6">
                            <a href="#" class="destination-card">
                                @if($city->image)
                                    <!-- Fallback to a placeholder if image doesn't exist in storage or is just a name -->
                                    <img src="{{ asset('storage/' . $city->image) }}" onerror="this.src='https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';" alt="{{ $city->name }}">
                                @else
                                    <div class="fallback-img-bg">
                                        <i class="bi bi-building"></i>
                                    </div>
                                @endif
                                <div class="destination-content">
                                    <h3 class="destination-title">{{ $city->name }}</h3>
                                    <div class="destination-count">{{ $city->hotels_count }} Hotel{{ $city->hotels_count > 1 ? 's' : '' }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 bg-white" style="border-radius: 1rem; border: 1px dashed var(--border-color);">
                    <i class="bi bi-map text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Destinations coming soon</h5>
                </div>
            @endif
        </div>
    </section>

    <!-- Featured Hotels -->
    <section id="hotels" class="py-5 bg-white">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between mb-5">
                <div>
                    <h2 class="section-title mb-2">Featured Hotels</h2>
                    <p class="text-muted mb-0">Hand-picked luxury accommodations for your perfect stay.</p>
                </div>
                <a href="#" class="btn btn-outline-dark d-none d-md-inline-block" style="border-radius: 2rem; padding: 0.5rem 1.5rem; font-weight: 500;">View All <i class="bi bi-arrow-right ms-1"></i></a>
            </div>

            @if($featuredHotels->count() > 0)
                <div class="row g-4">
                    @foreach($featuredHotels as $hotel)
                        <div class="col-lg-4 col-md-6">
                            <div class="hotel-card">
                                <div class="hotel-img-wrapper">
                                    @if($hotel->stars)
                                        <div class="hotel-badge"><i class="bi bi-star-fill text-warning me-1"></i> {{ $hotel->stars }} Stars</div>
                                    @endif
                                    
                                    @if($hotel->image)
                                        <img src="{{ asset('storage/' . $hotel->image) }}" onerror="this.src='https://images.unsplash.com/photo-1542314831-c6a4d27a6582?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';" alt="{{ $hotel->name }}">
                                    @else
                                        <div class="fallback-img-bg">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="hotel-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <a href="#" class="hotel-title">{{ $hotel->name }}</a>
                                        @if($hotel->avg_rating)
                                            <div class="d-flex align-items-center bg-light px-2 py-1 rounded" style="font-size: 0.85rem; font-weight: 600;">
                                                <i class="bi bi-star-fill text-warning me-1"></i> {{ number_format($hotel->avg_rating, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="hotel-location"><i class="bi bi-geo-alt me-1"></i> {{ $hotel->city->name ?? 'Location unavailable' }}</div>
                                    
                                    <div class="hotel-footer">
                                        <div class="hotel-price">
                                            ${{ number_format($hotel->starting_price ?? $hotel->price_per_night, 2) }} <span>/ night</span>
                                        </div>
                                        <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-sm btn-accent" style="border-radius: 0.5rem;">View Hotel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4 d-md-none">
                    <a href="#" class="btn btn-outline-dark" style="border-radius: 2rem; padding: 0.5rem 1.5rem;">View All Hotels</a>
                </div>
            @else
                <div class="text-center py-5 bg-light" style="border-radius: 1rem; border: 1px dashed var(--border-color);">
                    <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No hotels available at the moment</h5>
                </div>
            @endif
        </div>
    </section>

    <!-- Statistics -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['total_hotels'] > 0 ? $stats['total_hotels'] : '50+' }}</div>
                        <div class="stat-label">Luxury Hotels</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['total_cities'] > 0 ? $stats['total_cities'] : '20+' }}</div>
                        <div class="stat-label">Destinations</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['total_rooms'] > 0 ? $stats['total_rooms'] : '1000+' }}</div>
                        <div class="stat-label">Premium Rooms</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['total_reviews'] > 0 ? $stats['total_reviews'] : '10k+' }}</div>
                        <div class="stat-label">Happy Guests</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-5 my-5">
        <div class="container text-center">
            <h2 class="section-title">Why Choose HotelBooking</h2>
            <p class="section-subtitle">We provide a premium experience from the moment you start searching until you check out.</p>
            
            <div class="row g-4 mt-2">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card px-3">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="font-size: 1.25rem;">Secure Booking</h4>
                        <p class="text-muted">Your payment and personal information are protected by industry-leading security protocols.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card px-3">
                        <div class="feature-icon">
                            <i class="bi bi-tags"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="font-size: 1.25rem;">Best Price Guarantee</h4>
                        <p class="text-muted">Find a lower price on another website? We'll match it and give you an extra discount.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 offset-md-3 offset-lg-0">
                    <div class="feature-card px-3">
                        <div class="feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="font-size: 1.25rem;">24/7 Support</h4>
                        <p class="text-muted">Our dedicated customer service team is available around the clock to assist you.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="section-title text-center">What Our Guests Say</h2>
            <p class="section-subtitle text-center">Real experiences from real travelers.</p>
            
            <div class="row g-4 mt-2">
                @forelse($guestReviews as $review)
                    <div class="col-lg-4 col-md-6">
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
                            <p class="mb-4" style="font-style: italic; color: #475569;">"{{ \Illuminate\Support\Str::limit($review->comment, 150, '...') }}"</p>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? 'Guest') }}&background=0F172A&color=fff" class="rounded-circle me-3" width="48" height="48" alt="">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $review->user->name ?? 'Guest User' }}</h6>
                                    <small class="text-muted">Stayed at {{ $review->hotel->name ?? 'Hotel' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback reviews if DB is empty to maintain layout -->
                    <div class="col-lg-4 col-md-6">
                        <div class="review-card">
                            <i class="bi bi-quote review-quote"></i>
                            <div class="mb-3 text-warning">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            </div>
                            <p class="mb-4" style="font-style: italic; color: #475569;">"Absolutely exceptional service and beautiful rooms. The booking process was seamless and the stay was unforgettable."</p>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Sarah+J&background=0F172A&color=fff" class="rounded-circle me-3" width="48" height="48" alt="">
                                <div>
                                    <h6 class="mb-0 fw-bold">Sarah J.</h6>
                                    <small class="text-muted">Frequent Traveler</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="review-card">
                            <i class="bi bi-quote review-quote"></i>
                            <div class="mb-3 text-warning">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            </div>
                            <p class="mb-4" style="font-style: italic; color: #475569;">"I've used many booking platforms, but HotelBooking offers the best selection of luxury properties by far."</p>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Michael+T&background=0F172A&color=fff" class="rounded-circle me-3" width="48" height="48" alt="">
                                <div>
                                    <h6 class="mb-0 fw-bold">Michael T.</h6>
                                    <small class="text-muted">Business Executive</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 offset-md-3 offset-lg-0">
                        <div class="review-card">
                            <i class="bi bi-quote review-quote"></i>
                            <div class="mb-3 text-warning">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i>
                            </div>
                            <p class="mb-4" style="font-style: italic; color: #475569;">"Great experience overall. The hotel matched the photos perfectly and the check-in process was very fast."</p>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Emma+W&background=0F172A&color=fff" class="rounded-circle me-3" width="48" height="48" alt="">
                                <div>
                                    <h6 class="mb-0 fw-bold">Emma W.</h6>
                                    <small class="text-muted">Family Vacation</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <div class="container">
        <section class="cta-section">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">Ready to book your next trip?</h2>
                    <p class="lead mb-5 mx-auto" style="color: rgba(255,255,255,0.8); max-width: 500px;">Join thousands of satisfied travelers who have found their perfect stay with HotelBooking.</p>
                    <a href="#hotels" class="btn btn-accent btn-lg px-5 py-3" style="border-radius: 0.875rem;">Explore All Hotels</a>
                </div>
            </div>
        </section>
    </div>
@endsection
