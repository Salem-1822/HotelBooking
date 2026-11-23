@extends('client.layouts.app')

@section('title', __('about.title') . ' | HotelBooking')

@push('styles')
<style>
    /* Hero Section */
    .about-hero {
        background-color: var(--bg-body);
        padding: 12rem 0 8rem;
        position: relative;
        text-align: center;
        overflow: hidden;
    }

    .hero-label {
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--brand-accent);
        margin-bottom: 1.5rem;
        display: inline-block;
        position: relative;
    }

    .hero-label::after {
        content: '';
        display: block;
        width: 40px;
        height: 2px;
        background-color: var(--brand-accent);
        margin: 0.75rem auto 0;
    }

    .hero-title {
        font-weight: 800;
        font-size: 3.5rem;
        color: var(--brand-primary);
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .hero-text {
        font-size: 1.15rem;
        color: var(--text-muted);
        max-width: 700px;
        margin: 0 auto 2.5rem;
        line-height: 1.7;
    }

    /* General Sections */
    .section-padding {
        padding: 7rem 0;
    }

    .section-title {
        font-weight: 800;
        font-size: 2.5rem;
        color: var(--brand-primary);
        margin-bottom: 1.5rem;
    }

    .section-subtitle {
        color: var(--text-muted);
        font-size: 1.1rem;
        max-width: 600px;
        margin-bottom: 4rem;
        line-height: 1.7;
    }

    .bg-light-neutral {
        background-color: var(--bg-body);
    }
    
    .bg-white-section {
        background-color: #ffffff;
    }

    /* How It Works Section */
    .step-card {
        background: #ffffff;
        border-radius: 1rem;
        padding: 2.5rem;
        height: 100%;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }

    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    }

    .step-number {
        font-size: 1rem;
        font-weight: 800;
        color: var(--brand-accent);
        margin-bottom: 1.25rem;
        display: block;
        letter-spacing: 0.1em;
    }

    .step-icon {
        font-size: 2.5rem;
        color: var(--brand-primary);
        margin-bottom: 1.5rem;
    }

    .step-title {
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--brand-primary);
    }

    .step-desc {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* What We Offer / Features */
    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 2rem;
    }

    .feature-icon-wrapper {
        background: rgba(212, 175, 55, 0.1);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-inline-end: 1.25rem;
    }

    .feature-icon-wrapper i {
        color: var(--brand-accent);
        font-size: 1.25rem;
    }

    .feature-content h5 {
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 0.5rem;
    }

    .feature-content p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Brand Visual Blocks (Replacing Images) */
    .brand-visual-block {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        border-radius: 1rem;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
        height: 100%;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .brand-visual-block::before {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: radial-gradient(circle at top right, rgba(212, 175, 55, 0.1) 0%, transparent 60%);
    }

    .brand-visual-content {
        text-align: center;
        position: relative;
        z-index: 2;
    }
    
    .brand-visual-content .hb-logo {
        font-size: 5rem;
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -2px;
        line-height: 1;
        margin-bottom: 0;
    }
    
    .brand-line {
        width: 60px;
        height: 3px;
        background-color: var(--brand-accent);
        margin: 1.5rem auto;
    }

    .brand-visual-alt {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .brand-visual-alt .hb-text {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--brand-primary);
        letter-spacing: -1px;
        line-height: 1.1;
    }

    /* CTA Section */
    .cta-section {
        background: var(--brand-primary);
        color: white;
        padding: 6rem 0;
        text-align: center;
    }

    .cta-title {
        font-weight: 800;
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
    }



    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .hero-title { font-size: 2.5rem; }
        .section-title { font-size: 2rem; }
        .section-padding { padding: 4rem 0; }
        .brand-visual-block { min-height: 300px; margin-top: 3rem; }
    }
</style>
@endpush

@section('content')

<!-- 1. HERO SECTION -->
<section class="about-hero">
    <div class="container">
        <span class="hero-label reveal">{{ __('about.hero_label') }}</span>
        <h1 class="hero-title reveal delay-1">{{ __('about.hero_title') }}</h1>
        <p class="hero-text reveal delay-2">
            {{ __('about.hero_text') }}
        </p>
        <div class="d-flex gap-3 justify-content-center mt-5 reveal delay-3">
            <a href="{{ route('hotels.index') }}" class="btn btn-primary px-5 py-3 fw-bold text-uppercase shadow-sm" style="letter-spacing: 0.05em; border-radius: 0.5rem; background-color: var(--brand-primary); border: none;">
                {{ __('about.explore_hotels') }}
            </a>
        </div>
    </div>
</section>

<!-- 2. ABOUT & MISSION SECTION -->
<section id="mission" class="section-padding bg-white-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 pe-lg-5 reveal">
                <span class="text-uppercase fw-bold text-muted" style="letter-spacing: 0.1em; font-size: 0.85rem;">{{ __('about.mission.label') }}</span>
                <h2 class="section-title mt-2">{{ __('about.mission.title') }}</h2>
                <p class="text-muted mb-4" style="line-height: 1.8; font-size: 1.05rem;">
                    {{ __('about.mission.p1') }}
                </p>
                <p class="text-muted mb-4" style="line-height: 1.8; font-size: 1.05rem;">
                    {{ __('about.mission.p2') }}
                </p>
                <div class="d-flex align-items-center gap-4 mt-5">
                    <div>
                        <h3 class="fw-bold text-dark mb-1 fs-5">{{ __('about.mission.simple') }}</h3>
                        <span class="text-muted small">{{ __('about.mission.simple_sub') }}</span>
                    </div>
                    <div style="width: 1px; height: 40px; background: #E2E8F0;"></div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1 fs-5">{{ __('about.mission.secure') }}</h3>
                        <span class="text-muted small">{{ __('about.mission.secure_sub') }}</span>
                    </div>
                    <div style="width: 1px; height: 40px; background: #E2E8F0;"></div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1 fs-5">{{ __('about.mission.clear') }}</h3>
                        <span class="text-muted small">{{ __('about.mission.clear_sub') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0 reveal delay-2">
                <div class="brand-visual-block">
                    <div class="brand-visual-content">
                        <div class="hb-logo">HB</div>
                        <div class="brand-line"></div>
                        <div class="text-white-50 text-uppercase fw-semibold" style="letter-spacing: 3px; font-size: 0.9rem;">HotelBooking</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. HOW IT WORKS -->
<section class="section-padding bg-light-neutral">
    <div class="container text-center">
        <h2 class="section-title reveal">{{ __('about.how_it_works.title') }}</h2>
        <p class="section-subtitle mx-auto reveal delay-1">{{ __('about.how_it_works.subtitle') }}</p>
        
        <div class="row g-4 mt-2">
            <div class="col-md-6 col-lg-3 reveal delay-1">
                <div class="step-card text-start">
                    <span class="step-number">01</span>
                    <i class="bi bi-search step-icon"></i>
                    <h3 class="step-title">{{ __('about.how_it_works.step1_title') }}</h3>
                    <p class="step-desc">{{ __('about.how_it_works.step1_desc') }}</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 reveal delay-2">
                <div class="step-card text-start">
                    <span class="step-number">02</span>
                    <i class="bi bi-door-open step-icon"></i>
                    <h3 class="step-title">{{ __('about.how_it_works.step2_title') }}</h3>
                    <p class="step-desc">{{ __('about.how_it_works.step2_desc') }}</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 reveal delay-3">
                <div class="step-card text-start">
                    <span class="step-number">03</span>
                    <i class="bi bi-calendar2-check step-icon"></i>
                    <h3 class="step-title">{{ __('about.how_it_works.step3_title') }}</h3>
                    <p class="step-desc">{{ __('about.how_it_works.step3_desc') }}</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 reveal delay-4">
                <div class="step-card text-start">
                    <span class="step-number">04</span>
                    <i class="bi bi-luggage step-icon"></i>
                    <h3 class="step-title">{{ __('about.how_it_works.step4_title') }}</h3>
                    <p class="step-desc">{{ __('about.how_it_works.step4_desc') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. WHAT WE OFFER & WHY US -->
<section class="section-padding bg-white-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0 reveal">
                <h2 class="section-title">{{ __('about.offer.title') }}</h2>
                <p class="text-muted mb-4" style="line-height: 1.8;">
                    {{ __('about.offer.desc') }}
                </p>
                
                <ul class="feature-list mt-5">
                    <li class="feature-item">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="fw-bold fs-6 mb-1 text-dark">{{ __('about.offer.item1_title') }}</h3>
                            <p>{{ __('about.offer.item1_desc') }}</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="fw-bold fs-6 mb-1 text-dark">{{ __('about.offer.item2_title') }}</h3>
                            <p>{{ __('about.offer.item2_desc') }}</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="fw-bold fs-6 mb-1 text-dark">{{ __('about.offer.item3_title') }}</h3>
                            <p>{{ __('about.offer.item3_desc') }}</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-star"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="fw-bold fs-6 mb-1 text-dark">{{ __('about.offer.item4_title') }}</h3>
                            <p>{{ __('about.offer.item4_desc') }}</p>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-6 offset-lg-1 reveal delay-2">
                <div class="brand-visual-block brand-visual-alt shadow-sm mb-4">
                    <div class="brand-visual-content">
                        <div class="hb-text">HOTEL<br>BOOKING</div>
                        <div class="brand-line mt-3 mb-0" style="width: 40px;"></div>
                    </div>
                </div>
                <div class="bg-light-neutral p-4 rounded-4 border-0">
                    <h3 class="fw-bold text-dark mb-3 fs-5">{{ __('about.offer.designed_title') }}</h3>
                    <p class="text-muted mb-0" style="line-height: 1.7;">
                        {{ __('about.offer.designed_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. FINAL CTA -->
<section class="cta-section reveal">
    <div class="container">
        <h2 class="cta-title">{{ __('about.cta.title') }}</h2>
        <p class="text-white-50 mb-5 fs-5">{{ __('about.cta.subtitle') }}</p>
        <a href="{{ route('hotels.index') }}" class="btn btn-accent btn-lg px-5 py-3 fw-bold text-uppercase shadow" style="letter-spacing: 1px; border-radius: 0.5rem;">
            {{ __('about.cta.button') }} <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>
</section>

@endsection


