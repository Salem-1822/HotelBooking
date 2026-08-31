<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Welcome') | HotelBooking</title>

    <!-- Fonts: Poppins + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar */
        .navbar-custom {
            background-color: var(--brand-primary);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        .nav-link:hover, .nav-link.active {
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
            border: 1px solid rgba(255,255,255,0.5);
            color: #fff;
            border-radius: 0.625rem;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s ease;
        }

        .btn-outline-light-custom:hover {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
        }

        /* Footer */
        .footer {
            background-color: var(--brand-primary);
            color: rgba(255,255,255,0.7);
            padding: 4rem 0 2rem;
            margin-top: auto;
        }
        
        .footer-heading {
            color: #fff;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.2s;
            display: block;
            margin-bottom: 0.75rem;
        }

        .footer-link:hover {
            color: var(--brand-accent);
        }

        /* Forms */
        .form-control, .form-select {
            border-radius: 0.625rem;
            border-color: var(--border-color);
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }

        /* Utility */
        .text-accent {
            color: var(--brand-accent);
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">HotelBooking</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="border-color: rgba(255,255,255,0.5);">
                <i class="bi bi-list text-white fs-2"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hotels.index') ? 'active' : '' }}" href="{{ route('hotels.index') }}">Hotels</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <div class="d-flex gap-3 mt-3 mt-lg-0">
                    @auth('web')
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-white" data-bs-toggle="dropdown">
                                <div class="me-2 text-end d-none d-lg-block">
                                    <div style="font-size: 0.875rem; font-weight: 600;">{{ Auth::guard('web')->user()->name }}</div>
                                </div>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('web')->user()->name) }}&background=D4AF37&color=fff" class="rounded-circle" width="38" height="38" alt="">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 0.875rem; border: 1px solid var(--border-color);">
                                <li><a class="dropdown-item py-2" href="#"><i class="bi bi-grid-fill me-2 text-muted"></i> Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person-fill me-2 text-muted"></i> Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light-custom">Login</a>
                        <a href="#" class="btn btn-accent">Register</a>
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
                    <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem;">Experience luxury and comfort at our carefully curated selection of hotels worldwide. Your perfect stay is just a click away.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">Quick Links</h5>
                    <a href="#" class="footer-link">Home</a>
                    <a href="#" class="footer-link">All Hotels</a>
                    <a href="#" class="footer-link">Destinations</a>
                    <a href="#" class="footer-link">Offers & Promos</a>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">Support</h5>
                    <a href="#" class="footer-link">Help Center</a>
                    <a href="#" class="footer-link">Contact Us</a>
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading">Newsletter</h5>
                    <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem;">Subscribe to get special offers and updates.</p>
                    <div class="input-group mt-3">
                        <input type="email" class="form-control bg-transparent text-white" placeholder="Email address" style="border-color: rgba(255,255,255,0.2);">
                        <button class="btn btn-accent" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="border-top mt-5 pt-4 text-center" style="border-color: rgba(255,255,255,0.1) !important;">
                <p class="mb-0" style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">&copy; {{ date('Y') }} HotelBooking. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
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
    </script>
    @stack('scripts')
</body>
</html>
