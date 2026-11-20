<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('common.app_name')) | HotelBooking</title>

    <!-- Fonts: Poppins + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --brand-primary: #0F172A;
            --brand-secondary: #334155;
            --brand-primary-light: #E2E8F0;
            --brand-accent: #D4AF37;
            --brand-accent-dark: #B8860B;
            --bg-body: #F8FAFC;
            --text-primary: #1E293B;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --card-shadow-hover: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand {
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar */
        .navbar-custom {
            background-color: var(--brand-primary);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #ffffff 0%, var(--brand-accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            font-size: 0.95rem;
            margin: 0 0.5rem;
            transition: color 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--brand-accent) !important;
        }

        .btn-accent {
            background-color: var(--brand-accent);
            border-color: var(--brand-accent);
            color: #fff;
            border-radius: 0.625rem;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .btn-accent:hover {
            background-color: var(--brand-accent-dark);
            border-color: var(--brand-accent-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-outline-light-custom {
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: #fff;
            border-radius: 0.625rem;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s ease;
        }

        .btn-outline-light-custom:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* Language Dropdown */
        .lang-dropdown-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            border-radius: 0.625rem;
            padding: 0.5rem 0.85rem;
            transition: all 0.2s ease;
        }

        .lang-dropdown-btn:hover,
        .lang-dropdown-btn:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        /* Footer */
        .footer {
            background-color: var(--brand-primary);
            color: rgba(255, 255, 255, 0.7);
            padding: 4rem 0 2rem;
            margin-top: auto;
        }

        .footer-heading {
            color: #fff;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.2s;
            display: block;
            margin-bottom: 0.75rem;
        }

        .footer-link:hover {
            color: var(--brand-accent);
        }

        /* Forms */
        .form-control,
        .form-select {
            border-radius: 0.625rem;
            border-color: var(--border-color);
            padding: 0.75rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }

        /* Utility */
        .text-accent {
            color: var(--brand-accent);
        }

        /* Scroll Reveal Animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.2, 0.8, 0.2, 1), transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
            will-change: opacity, transform;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .delay-1 {
            transition-delay: 0.1s;
        }

        .delay-2 {
            transition-delay: 0.2s;
        }

        .delay-3 {
            transition-delay: 0.3s;
        }

        .delay-4 {
            transition-delay: 0.4s;
        }

        .delay-5 {
            transition-delay: 0.5s;
        }

        .delay-6 {
            transition-delay: 0.6s;
        }

        @media (prefers-reduced-motion: reduce) {

            .reveal,
            .delay-1,
            .delay-2,
            .delay-3,
            .delay-4,
            .delay-5,
            .delay-6 {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }

        /* Password toggle button & input wrapping rules */
        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 1rem;
            pointer-events: none;
            z-index: 2;
        }

        .input-icon-wrap .form-control {
            padding-left: 3.25rem !important;
            padding-right: 1.25rem !important;
        }

        .input-icon-wrap .form-control.has-toggle {
            padding-left: 3.25rem !important;
            padding-right: 3.25rem !important;
        }

        .password-toggle-btn {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #9CA3AF;
            padding: 0.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
            font-size: 1.1rem;
            line-height: 1;
            transition: color 0.15s ease;
        }
        .password-toggle-btn:hover {
            color: #374151;
        }
        .password-toggle-btn:focus {
            outline: none;
            color: var(--brand-primary, #0F172A);
        }

        /* RTL Overrides for input icons, inputs & toggles */
        [dir="rtl"] .password-toggle-btn,
        html[dir="rtl"] .password-toggle-btn {
            right: auto !important;
            left: 0.85rem !important;
        }

        [dir="rtl"] .input-icon-wrap .input-icon,
        html[dir="rtl"] .input-icon-wrap .input-icon {
            right: 1rem !important;
            left: auto !important;
        }

        [dir="rtl"] .input-icon-wrap .form-control,
        html[dir="rtl"] .input-icon-wrap .form-control {
            padding-right: 3.25rem !important;
            padding-left: 1.25rem !important;
            text-align: right !important;
        }

        [dir="rtl"] .input-icon-wrap .form-control.has-toggle,
        html[dir="rtl"] .input-icon-wrap .form-control.has-toggle {
            padding-right: 3.25rem !important;
            padding-left: 3.25rem !important;
            text-align: right !important;
        }

        [dir="rtl"] .input-icon-wrap input[type="email"],
        [dir="rtl"] .input-icon-wrap .auth-email-input,
        html[dir="rtl"] .input-icon-wrap input[type="email"],
        html[dir="rtl"] .input-icon-wrap .auth-email-input {
            direction: ltr !important;
            text-align: right !important;
            padding-right: 3.25rem !important;
            padding-left: 1.25rem !important;
        }

        [dir="rtl"] .input-icon-wrap input[type="email"]::placeholder,
        [dir="rtl"] .input-icon-wrap .auth-email-input::placeholder,
        html[dir="rtl"] .input-icon-wrap input[type="email"]::placeholder,
        html[dir="rtl"] .input-icon-wrap .auth-email-input::placeholder {
            text-align: right !important;
            direction: rtl !important;
        }

        [dir="rtl"] .alert-success-custom,
        [dir="rtl"] .alert-error-custom,
        [dir="rtl"] .alert-status-custom,
        html[dir="rtl"] .alert-success-custom,
        html[dir="rtl"] .alert-error-custom,
        html[dir="rtl"] .alert-status-custom {
            border-left: none !important;
            border-right: 4px solid !important;
        }
    </style>
    @stack('styles')
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">HotelBooking</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation"
                style="border-color: rgba(255,255,255,0.5);">
                <i class="bi bi-list text-white fs-2"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">{{ __('navigation.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hotels.index') || request()->routeIs('hotels.show') ? 'active' : '' }}"
                            href="{{ route('hotels.index') }}">{{ __('navigation.hotels') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                            href="{{ route('about') }}">{{ __('navigation.about') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                            href="{{ route('contact') }}">{{ __('navigation.contact') }}</a>
                    </li>
                    @auth('web')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}"
                                href="{{ route('client.dashboard') }}">{{ __('navigation.dashboard') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.reservations.*') ? 'active' : '' }}"
                                href="{{ route('client.reservations.index') }}">{{ __('navigation.my_reservations') }}</a>
                        </li>
                    @endauth
                </ul>
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <!-- Language Dropdown -->
                    <div class="dropdown">
                        <button class="btn lang-dropdown-btn d-flex align-items-center gap-2" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-globe" style="font-size: 1.1rem; color: rgba(255,255,255,0.7);"></i>
                            <span style="font-size: 0.85rem; font-weight: 500;">
                                @if(app()->getLocale() === 'fr') Français
                                @elseif(app()->getLocale() === 'ar') العربية
                                @else English
                                @endif
                            </span>
                            <i class="bi bi-chevron-expand" style="font-size: 0.8rem; color: rgba(255,255,255,0.5);"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-sm"
                            style="border-radius: 0.625rem; border: 1px solid rgba(255,255,255,0.1); background: #1E293B; padding: 0.5rem; min-width: 150px;">
                            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', 'en') }}"
                                    style="border-radius: 0.375rem; font-size: 0.85rem; transition: background 0.2s ease;"><span
                                        class="fs-6">🇺🇸</span> English</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{ route('locale.switch', 'fr') }}"
                                    style="border-radius: 0.375rem; font-size: 0.85rem; transition: background 0.2s ease;"><span
                                        class="fs-6">🇫🇷</span> Français</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ route('locale.switch', 'ar') }}"
                                    style="border-radius: 0.375rem; font-size: 0.85rem; transition: background 0.2s ease;"><span
                                        class="fs-6">🇲🇦</span> العربية</a></li>
                        </ul>
                    </div>

                    @auth('web')
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-white"
                                data-bs-toggle="dropdown">
                                <div class="me-2 text-end d-none d-lg-block">
                                    <div style="font-size: 0.875rem; font-weight: 600;">
                                        {{ Auth::guard('web')->user()->name }}
                                    </div>
                                </div>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('web')->user()->name) }}&background=D4AF37&color=fff"
                                    class="rounded-circle" width="38" height="38" alt="">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                                style="border-radius: 0.875rem; border: 1px solid var(--border-color);">
                                <li><a class="dropdown-item py-2" href="{{ route('client.profile.edit') }}"><i
                                            class="bi bi-person-fill me-2 text-muted"></i> {{ __('navigation.my_profile') }}</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger"><i
                                                class="bi bi-box-arrow-right me-2"></i> {{ __('navigation.sign_out') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light-custom">{{ __('navigation.login') }}</a>
                        <a href="{{ route('client.register') }}" class="btn btn-accent">{{ __('navigation.register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="margin-top: 76px;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="navbar-brand mb-3" style="font-size: 1.8rem;">HotelBooking</div>
                    <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem;">{{ __('navigation.footer_tagline') }}</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">{{ __('navigation.quick_links') }}</h5>
                    <a href="{{ route('home') }}" class="footer-link">{{ __('navigation.home') }}</a>
                    <a href="{{ route('about') }}" class="footer-link">{{ __('navigation.about') }}</a>
                    <a href="{{ route('hotels.index') }}" class="footer-link">{{ __('navigation.hotels') }}</a>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">{{ __('navigation.contact') }}</h5>
                    <a href="{{ route('contact') }}" class="footer-link">{{ __('navigation.contact') }}</a>
                    <a href="#" class="footer-link">{{ __('navigation.privacy_policy') }}</a>
                    <a href="#" class="footer-link">{{ __('navigation.terms_of_service') }}</a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading">{{ __('navigation.newsletter') }}</h5>
                    <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem;">{{ __('navigation.newsletter_desc') }}</p>
                    <div class="input-group mt-3">
                        <input type="email" class="form-control bg-transparent text-white" placeholder="{{ __('navigation.email_placeholder') }}"
                            style="border-color: rgba(255,255,255,0.2);">
                        <button class="btn btn-accent" type="button">{{ __('navigation.subscribe') }}</button>
                    </div>
                </div>
            </div>
            <div class="border-top mt-5 pt-4 text-center" style="border-color: rgba(255,255,255,0.1) !important;">
                <p class="mb-0" style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">{!! __('navigation.all_rights_reserved', ['year' => date('Y')]) !!}</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function () {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.style.padding = '0.5rem 0';
                navbar.style.backgroundColor = 'rgba(15, 23, 42, 0.98)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.style.padding = '1rem 0';
                navbar.style.backgroundColor = 'var(--brand-primary)';
                navbar.style.backdropFilter = 'none';
            }
        });

        // Shared scroll reveal observer
        document.addEventListener('DOMContentLoaded', function () {
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (!prefersReducedMotion) {
                const observerOptions = {
                    root: null,
                    rootMargin: '0px',
                    threshold: 0.15
                };

                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, observerOptions);

                const revealElements = document.querySelectorAll('.reveal');
                revealElements.forEach(el => observer.observe(el));

                // Immediately reveal items already in viewport
                setTimeout(() => {
                    revealElements.forEach(el => {
                        const rect = el.getBoundingClientRect();
                        if (rect.top < window.innerHeight && rect.bottom > 0) {
                            el.classList.add('is-visible');
                            observer.unobserve(el);
                        }
                    });
                }, 50);
            }
        });

        // Reusable password visibility toggle
        function initPasswordToggles() {
            document.querySelectorAll('.password-toggle-btn').forEach(function (btn) {
                if (btn.dataset.toggleInitialized) return;
                btn.dataset.toggleInitialized = 'true';
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (!input) return;
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        if (icon) {
                            icon.classList.remove('bi-eye');
                            icon.classList.add('bi-eye-slash');
                        }
                        this.setAttribute('aria-pressed', 'true');
                    } else {
                        input.type = 'password';
                        if (icon) {
                            icon.classList.remove('bi-eye-slash');
                            icon.classList.add('bi-eye');
                        }
                        this.setAttribute('aria-pressed', 'false');
                    }
                });
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPasswordToggles);
        } else {
            initPasswordToggles();
        }
    </script>
    @stack('scripts')
</body>

</html>