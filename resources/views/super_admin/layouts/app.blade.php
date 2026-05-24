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

            /* Scoped transition values — never use 'all' globally */
            --t-sidebar:  left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --t-layout:   margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --t-color:    background-color 0.2s ease, color 0.2s ease;
            --t-btn:      background-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
            --t-link:     background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;

            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: #334155;
            overflow-x: hidden;
        }

        /* SIDEBAR & CONTENT — only transition the properties that actually change */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1060;
            /* Only 'left' changes on mobile toggle — never animate 'all' */
            transition: var(--t-sidebar);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            /* Only 'margin-left' changes on mobile — never animate 'all' */
            transition: var(--t-layout);
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

        /* EXPORT BUTTON — scoped transition, no 'all' */
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
            transition: var(--t-btn);
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
            transition: var(--t-btn);
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
            /* Scoped: only color and background animate on hover/active */
            transition: var(--t-link);
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

        /*
         * Brand title — ONE-SHOT fade-in only.
         * softPulse and shimmer removed: they were infinite loops
         * running on every page, causing constant layout repaints.
         */
        .brand-title {
            font-family: 'Inter', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: 0.08em;
            background: linear-gradient(120deg, #ffffff 30%, var(--brand-primary) 60%, #ffffff 90%);
            background-size: 200% auto;
            color: #fff;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            /* Only fadeIn — runs once, then stops. No infinite loops. */
            animation: brandFadeIn 0.6s ease-out forwards;
            display: inline-block;
            margin-bottom: 0;
        }

        @keyframes brandFadeIn {
            0%   { opacity: 0; transform: translateY(8px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /*
         * Page content area — lightweight one-shot entry animation.
         * Replaces the heavy full-screen loader for non-sidebar navigations.
         * Runs once per page load, then the element is static.
         */
        .page-content-wrapper {
            animation: pageEnter 0.25s ease-out both;
        }

        @keyframes pageEnter {
            0%   { opacity: 0; transform: translateY(12px); }
            100% { opacity: 1; transform: translateY(0); }
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
            0% {
                transform: scale(0.85);
                opacity: 0;
                filter: blur(4px);
            }

            100% {
                transform: scale(1);
                opacity: 1;
                filter: blur(0);
            }
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
            <a href="{{ route('super_admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('super_admin.hotels.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.hotels.*') ? 'active' : '' }}">
                <i class="bi bi-building-fill"></i>
                <span>Hotels</span>
            </a>
            <a href="{{ route('super_admin.cities.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.cities.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Cities</span>
            </a>
            <a href="{{ route('super_admin.admins.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.admins.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Admins</span>
            </a>
            <a href="{{ route('super_admin.reservations.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.reservations.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Reservations</span>
            </a>



            <div class="mt-4 mb-2 small text-uppercase text-muted px-3 fw-bold"
                style="font-size: 0.7rem; letter-spacing: 0.1em;">Settings</div>

            <a href="{{ route('super_admin.settings') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.settings') ? 'active' : '' }}">
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
                            <img src="{{ asset('storage/profiles/' . $layoutAdmin->profile_image) }}" width="40" height="40"
                                class="rounded-circle border border-2 border-white shadow-sm me-2"
                                style="object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($layoutAdmin->name) }}&background=0F172A&color=fff"
                                width="40" height="40" class="rounded-circle border border-2 border-white shadow-sm me-2">
                        @endif
                        <div class="d-none d-sm-block text-start">
                            <div class="fw-bold lh-1" style="font-size: 0.9rem;">
                                {{ $layoutAdmin->name }}
                            </div>
                            <small class="text-muted text-capitalize"
                                style="font-size: 0.75rem;">{{ str_replace('_', ' ', $layoutAdmin->role) }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2"
                        style="border-radius: 12px; min-width: 200px;">
                        <li><a class="dropdown-item rounded-3 py-2" href="{{ route('super_admin.profile') }}"><i
                                    class="bi bi-person me-2"></i> My
                                Profile</a></li>
                        <li>
                            <hr class="dropdown-divider mx-2">
                        </li>
                        <li>
                            <form action="{{ route('super_admin.logout') }}" method="POST">
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

        {{-- page-content-wrapper applies the lightweight one-shot pageEnter fade --}}
        <div class="p-4 p-md-5 page-content-wrapper">
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
        /**
         * PAGE TRANSITION SYSTEM — HOTELIA Super Admin
         *
         * Rules:
         *  ✅ Full-screen loader fires ONLY on sidebar navigation clicks (.sidebar-link)
         *  ✅ Lightweight pageEnter CSS animation handles all other page arrivals
         *  ❌ Loader does NOT fire on: table actions, export buttons, modal triggers,
         *     pagination links, dropdown items, form submissions, or any other anchor
         */
        document.addEventListener('DOMContentLoaded', function () {
            const loader = document.getElementById('page-loader');

            // ── Step 1: Fade out the loader on every page arrival ──────────────
            // Fast fade-out so the page content becomes visible immediately.
            if (loader) {
                setTimeout(() => loader.classList.add('fade-out'), 200);
            }

            // ── Step 2: Attach loader ONLY to sidebar navigation links ─────────
            // We target '.sidebar-link' exclusively — not all anchors.
            // This prevents the loader from firing on:
            //   • Export PDF buttons
            //   • "View Details" / "Edit" / "Delete" table actions
            //   • Pagination links
            //   • Profile dropdown items
            //   • Any link inside modals or cards
            const sidebarLinks = document.querySelectorAll('.sidebar-link[href]');

            sidebarLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');

                    // Skip: empty, hash, javascript:, external, or modifier-key clicks
                    if (
                        !href ||
                        href === '#' ||
                        href.startsWith('javascript') ||
                        this.getAttribute('target') === '_blank' ||
                        e.ctrlKey || e.metaKey || e.shiftKey
                    ) return;

                    // Skip: already on this page (same URL) — no need to reload
                    if (href === window.location.href || href === window.location.pathname) return;

                    // ✅ Show the full-screen loader for sidebar page navigation
                    e.preventDefault();
                    if (loader) loader.classList.remove('fade-out');

                    // Navigate after the loader fade-in completes
                    setTimeout(function () {
                        window.location.href = href;
                    }, 400);
                });
            });

            // ── Step 3: Handle browser back/forward cache (bfcache) ────────────
            // When the user navigates back, the page may be served from cache
            // with the loader still visible — ensure it fades out.
            window.addEventListener('pageshow', function (event) {
                if (event.persisted && loader) {
                    loader.classList.add('fade-out');
                }
            });
        });
    </script>
</body>

</html>