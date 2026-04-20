<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') | HOTELIA</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --brand-primary: #f97316;
            --brand-primary-dark: #ea580c;
            --brand-primary-light: #ffedd5;

            --brand-success: #10b981;
            --brand-danger: #ef4444;
            --brand-warning: #f59e0b;
            --brand-info: #0ea5e9;

            --sidebar-bg: #0f172a;
            --topbar-height: 70px;
            --sidebar-width: 260px;

            --bg-body: #f8fafc;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: #334155;
            overflow-x: hidden;
        }

        /* FIX RESPONSIVE SIDEBAR & CONTENT */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1060;
            transition: var(--transition);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            transition: var(--transition);
        }

        .topbar {
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Mobile Adjustments */
        @media (max-width: 991.98px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1050;
                display: none;
            }

            .sidebar.show~.sidebar-overlay {
                display: block;
            }
        }

        /* UPDATE EXPORT PDF BUTTON STYLE */
        .btn-export {
            background-color: var(--brand-primary);
            color: #fff !important;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
        }

        .btn-export:hover {
            background-color: var(--brand-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.25);
        }

        /* General UI Consistency Overlaying Bootstrap */
        .card {
            border: none;
            border-radius: 1.25rem;
            box-shadow: var(--card-shadow);
        }

        .btn-primary {
            background-color: var(--brand-primary);
            border: none;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--brand-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
        }

        .sidebar-logo {
            padding: 2.5rem 1.5rem;
            text-align: center;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 0.5rem;
            margin: 0.25rem 1rem;
            transition: var(--transition);
        }

        .sidebar-link i {
            font-size: 1.25rem;
            margin-right: 0.85rem;
        }

        .sidebar-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-link.active {
            background: var(--brand-primary);
            color: #fff;
            box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.25);
        }

        /* Animated Brand Element */
        .brand-title {
            font-family: 'Inter', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: 0.08em;
            background: linear-gradient(120deg, #ffffff 20%, var(--brand-primary) 50%, #ffffff 80%);
            background-size: 200% auto;
            color: #fff;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeIn 1.2s ease-out forwards, shimmer 5s linear infinite, softPulse 4s ease-in-out infinite;
            display: inline-block;
            margin-bottom: 0;
            text-shadow: 0 0 20px rgba(249, 115, 22, 0.1);
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        @keyframes softPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); text-shadow: 0 0 15px rgba(249, 115, 22, 0.3); }
        }

        /* Page Transition Loader */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        #page-loader.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        .loader-content {
            text-align: center;
        }

        .loader-content h1 {
            font-family: 'Inter', sans-serif;
            font-weight: 800;
            font-size: 3.5rem;
            letter-spacing: 0.15em;
            margin: 0;
            color: #fff;
            background: linear-gradient(120deg, #ffffff 20%, var(--brand-primary) 50%, #ffffff 80%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: 
                loaderScale 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards,
                shimmer 3s linear infinite;
            text-shadow: 0 0 30px rgba(249, 115, 22, 0.2);
        }

        @keyframes loaderScale {
            0% { transform: scale(0.85); opacity: 0; filter: blur(4px); }
            100% { transform: scale(1); opacity: 1; filter: blur(0); }
        }
    </style>
</head>

<body>
    <!-- Full Page Transition Loader -->
    <div id="page-loader">
        <div class="loader-content">
            <h1>HOTELIA</h1>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- UPDATE BRAND COLORS ONLY -->
        <div class="sidebar-logo">
            <div class="brand-title">HOTELIA</div>
            <div class="small text-uppercase mt-2 fw-bold"
                style="font-size: 0.65rem; letter-spacing: 0.2em; color: var(--brand-primary); opacity: 0.6;">
                Premier Management
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.hotels.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.hotels.*') ? 'active' : '' }}">
                <i class="bi bi-building-fill"></i>
                <span>Hotels</span>
            </a>
            <a href="{{ route('admin.cities.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Cities</span>
            </a>
            <a href="{{ route('admin.admins.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Admins</span>
            </a>
            <a href="{{ route('admin.reservations.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Reservations</span>
            </a>



            <div class="mt-4 mb-2 small text-uppercase text-muted px-3 fw-bold"
                style="font-size: 0.7rem; letter-spacing: 0.1em;">Settings</div>

            <a href="{{ route('admin.settings') }}"
                class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <span>System Config</span>
            </a>
        </nav>
    </aside>

    <div class="sidebar-overlay" onclick="document.getElementById('sidebar').classList.remove('show')"></div>

    <!-- Main Content -->
    <main class="main-content">
        <header class="topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none"
                    onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-bold">@yield('title', 'Admin Panel')</h5>
                </div>
            </div>

            <div class="d-flex align-items-center gap-4">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                        data-bs-toggle="dropdown">
                        @php $layoutAdmin = Auth::guard('admin')->user(); @endphp
                        @if($layoutAdmin->profile_image && \Storage::disk('public')->exists('profiles/' . $layoutAdmin->profile_image))
                            <img src="{{ asset('storage/profiles/' . $layoutAdmin->profile_image) }}"
                                width="40" height="40" class="rounded-circle border border-2 border-white shadow-sm me-2" style="object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($layoutAdmin->name) }}&background=0F172A&color=fff"
                                width="40" height="40" class="rounded-circle border border-2 border-white shadow-sm me-2">
                        @endif
                        <div class="d-none d-sm-block text-start">
                            <div class="fw-bold lh-1" style="font-size: 0.9rem;">
                                {{ $layoutAdmin->name }}</div>
                            <small class="text-muted text-capitalize"
                                style="font-size: 0.75rem;">{{ str_replace('_', ' ', $layoutAdmin->role) }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2"
                        style="border-radius: 12px; min-width: 200px;">
                        <li><a class="dropdown-item rounded-3 py-2" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i> My
                                Profile</a></li>
                        <li>
                            <hr class="dropdown-divider mx-2">
                        </li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-3 py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="p-4 p-md-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
                    style="border-radius: 0.75rem;">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
                    style="border-radius: 0.75rem;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = event.target.closest('.btn-light.d-lg-none');
            if (window.innerWidth < 992 && sidebar.classList.contains('show') && !sidebar.contains(event.target) && !toggle) {
                sidebar.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
    
    <script>
        // Page Transition Animation Logic
        document.addEventListener("DOMContentLoaded", function() {
            const loader = document.getElementById('page-loader');
            
            // Hide loader on initial page load
            setTimeout(() => {
                if(loader) loader.classList.add('fade-out');
            }, 300); // slight delay for smooth entry

            // Intercept internal link clicks
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    const target = this.getAttribute('target');
                    
                    // Proceed with animation if it's an internal link, valid, and not opening in new tab
                    if (
                        href && 
                        href.startsWith(window.location.origin) && 
                        target !== '_blank' && 
                        !e.ctrlKey && 
                        !e.metaKey
                    ) {
                        e.preventDefault();
                        
                        // Show loader
                        if(loader) loader.classList.remove('fade-out');
                        
                        // Redirect after animation finishes
                        setTimeout(() => {
                            window.location.href = href;
                        }, 500);
                    }
                });
            });

            // Fallback: If user uses browsers back/forward buttons, hide loader
            window.addEventListener('pageshow', function(event) {
                if (event.persisted && loader) { // if loaded from cache
                    loader.classList.add('fade-out');
                }
            });
        });
    </script>
</body>

</html>