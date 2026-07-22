<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') | HotelBooking</title>

    <!-- Fonts: Poppins + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ================================================================
           HOTELBOOKING DESIGN SYSTEM — HOTEL ADMIN
           Color Palette:
             Primary:    #0F172A (navy blue)
             Secondary:  #334155
             Accent:     #D4AF37 (gold)
             Background: #F8FAFC
             Cards:      #FFFFFF
             Text:       #1E293B
             Muted:      #64748B
             Border:     #E2E8F0
             Success:    #22C55E
             Warning:    #F59E0B
             Danger:     #EF4444
             Info:       #3B82F6
        ================================================================ */

        :root {
            --brand-primary:       #0F172A;
            --brand-secondary:     #334155;
            --brand-primary-light: #E2E8F0;
            --brand-accent:        #D4AF37;
            --brand-accent-dark:   #B8860B;

            --brand-success: #22C55E;
            --brand-danger:  #EF4444;
            --brand-warning: #F59E0B;
            --brand-info:    #3B82F6;

            --sidebar-bg:      #0F172A;
            --topbar-height:   70px;
            --sidebar-width:   265px;

            --bg-body:         #F8FAFC;
            --text-primary:    #1E293B;
            --text-muted:      #64748B;
            --border-color:    #E2E8F0;

            /* Scoped transitions */
            --t-sidebar: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --t-layout:  margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --t-color:   background-color 0.2s ease, color 0.2s ease;
            --t-btn:     background-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
            --t-link:    background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;

            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --card-shadow-hover: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
        }

        /* ── Global ────────────────────────────────── */
        body {
            background-color: var(--bg-body);
            font-family: 'Poppins', 'Inter', sans-serif;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* ── Sidebar ───────────────────────────────── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1060;
            transition: var(--t-sidebar);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            transition: var(--t-layout);
        }

        /* ── Topbar ────────────────────────────────── */
        .topbar {
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        /* ── Mobile Adjustments ────────────────────── */
        @media (max-width: 991.98px) {
            .sidebar { left: calc(-1 * var(--sidebar-width)); }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; width: 100%; }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1050;
                display: none;
                backdrop-filter: blur(2px);
            }
            .sidebar.show ~ .sidebar-overlay { display: block; }
        }

        /* ── Sidebar Logo Area ─────────────────────── */
        .sidebar-logo {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .brand-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #ffffff 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: brandFadeIn 0.6s ease-out forwards;
            display: inline-block;
            margin-bottom: 0;
        }

        .brand-subtitle {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: rgba(255, 255, 255, 0.35);
            font-weight: 600;
            display: block;
            margin-top: 0.25rem;
        }

        @keyframes brandFadeIn {
            0%   { opacity: 0; transform: translateY(6px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ── Sidebar Nav ───────────────────────────── */
        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(255, 255, 255, 0.25);
            padding: 0.75rem 1.5rem 0.4rem;
            margin-top: 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: rgba(255, 255, 255, 0.55);
            text-decoration: none;
            border-radius: 0.625rem;
            margin: 0.15rem 0.85rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--t-link);
            position: relative;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.07);
        }

        .sidebar-link.active {
            background: var(--brand-secondary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.35);
            font-weight: 600;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--brand-accent);
            border-radius: 0 3px 3px 0;
        }

        /* ── Sidebar Footer ────────────────────────── */
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* ── Bootstrap Override: Cards ─────────────── */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
        }

        /* ── Bootstrap Override: btn-primary ───────── */
        .btn-primary {
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
            border-radius: 0.625rem;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
            transition: var(--t-btn);
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--brand-secondary);
            border-color: var(--brand-secondary);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
        }
        
        .btn-accent {
            background-color: var(--brand-accent);
            border-color: var(--brand-accent);
            color: #fff;
            border-radius: 0.625rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: var(--t-btn);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        .btn-accent:hover {
            background-color: var(--brand-accent-dark);
            border-color: var(--brand-accent-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        /* ── Topbar User dropdown ──────────────────── */
        .topbar-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .topbar-username {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .topbar-role {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: capitalize;
        }

        .dropdown-menu.premium-dropdown {
            border: 1px solid var(--border-color);
            border-radius: 0.875rem;
            box-shadow: 0 20px 40px -8px rgba(0, 0, 0, 0.12);
            padding: 0.5rem;
            min-width: 210px;
        }

        .dropdown-menu.premium-dropdown .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            transition: var(--t-color);
        }

        .dropdown-menu.premium-dropdown .dropdown-item:hover {
            background: #F1F5F9;
            color: var(--brand-primary);
        }

        .dropdown-menu.premium-dropdown .dropdown-item.text-danger:hover {
            background: #FEF2F2;
            color: var(--brand-danger);
        }

        /* ── Page content animation ────────────────── */
        .page-content-wrapper {
            animation: pageEnter 0.22s ease-out both;
        }

        @keyframes pageEnter {
            0%   { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ── Full Page Loader ──────────────────────── */
        #page-loader {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
            transition: opacity 0.45s ease;
        }
        #page-loader.fade-out { opacity: 0; pointer-events: none; }

        .loader-content { text-align: center; }

        .loader-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 2.8rem;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #ffffff 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: loaderScale 0.75s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .loader-subtitle {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: rgba(255,255,255,0.3);
            font-weight: 600;
            margin-top: 0.5rem;
            display: block;
        }

        .loader-bar {
            width: 160px;
            height: 3px;
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
            margin: 1.25rem auto 0;
            overflow: hidden;
        }
        .loader-bar-inner {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--brand-primary), var(--brand-accent));
            border-radius: 3px;
            animation: loadProgress 0.8s ease-out 0.1s forwards;
        }

        @keyframes loaderScale {
            0%   { transform: scale(0.88); opacity: 0; filter: blur(4px); }
            100% { transform: scale(1);    opacity: 1; filter: blur(0); }
        }
        @keyframes loadProgress {
            0%   { width: 0; }
            100% { width: 100%; }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Full Page Transition Loader -->
    <div id="page-loader">
        <div class="loader-content">
            <div class="loader-brand">HotelBooking</div>
            <span class="loader-subtitle">Hotel Administrator Portal</span>
            <div class="loader-bar">
                <div class="loader-bar-inner"></div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="brand-title">HotelBooking</div>
            <span class="brand-subtitle">Administrator</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.rooms.index') }}" class="sidebar-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                <i class="bi bi-door-open-fill"></i>
                <span>Manage Rooms</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Reservations</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-people-fill"></i>
                <span>Customers</span>
            </a>
            
            <div class="nav-section-label">Management</div>
            
            <a href="#" class="sidebar-link">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Reports</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-building-fill"></i>
                <span>Hotel Profile</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            @php $footerAdmin = Auth::guard('admin')->user(); @endphp
            <div class="d-flex align-items-center gap-2">
                @if($footerAdmin->profile_image && \Storage::disk('public')->exists('profiles/' . $footerAdmin->profile_image))
                    <img src="{{ asset('storage/profiles/' . $footerAdmin->profile_image) }}"
                        style="width:36px;height:36px;object-fit:cover;border-radius:50%;border:2px solid rgba(255,255,255,0.15);" alt="">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($footerAdmin->name) }}&background=0F172A&color=fff&size=80"
                        style="width:36px;height:36px;object-fit:cover;border-radius:50%;" alt="">
                @endif
                <div class="overflow-hidden">
                    <div style="font-size:0.8rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $footerAdmin->name }}
                    </div>
                    <div style="font-size:0.65rem;color:rgba(255,255,255,0.4);text-transform:capitalize;">
                        {{ str_replace('_', ' ', $footerAdmin->role) }}
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <div class="sidebar-overlay" onclick="document.getElementById('sidebar').classList.remove('show')"></div>

    <!-- Main Content -->
    <main class="main-content">
        <header class="topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border d-lg-none"
                    onclick="document.getElementById('sidebar').classList.toggle('show')"
                    style="border-radius:0.625rem;width:38px;height:38px;padding:0;display:flex!important;align-items:center;justify-content:center;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <h6 class="mb-0 fw-bold" style="color:var(--text-primary);font-size:1rem;">
                        @yield('title', 'Admin Panel')
                    </h6>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- User Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                        data-bs-toggle="dropdown" style="gap:0.625rem;">
                        @php $layoutAdmin = Auth::guard('admin')->user(); @endphp
                        @if($layoutAdmin->profile_image && \Storage::disk('public')->exists('profiles/' . $layoutAdmin->profile_image))
                            <img src="{{ asset('storage/profiles/' . $layoutAdmin->profile_image) }}"
                                class="topbar-user-avatar" style="object-fit:cover;" alt="">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($layoutAdmin->name) }}&background=0F172A&color=fff&size=80"
                                class="topbar-user-avatar" alt="">
                        @endif
                        <div class="d-none d-sm-block text-start">
                            <div class="topbar-username">{{ $layoutAdmin->name }}</div>
                            <div class="topbar-role">{{ str_replace('_', ' ', $layoutAdmin->role) }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu premium-dropdown dropdown-menu-end mt-2">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person-circle me-2"></i> My Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider mx-2 my-1"></li>
                        <li>
                            <form action="{{ route('super_admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="p-4 p-md-5 page-content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 0.875rem; border: none; border-left: 4px solid var(--brand-success);">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 0.875rem; border: none; border-left: 4px solid var(--brand-danger);">
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
            const toggle = event.target.closest('.btn.d-lg-none');
            if (window.innerWidth < 992 && sidebar.classList.contains('show') && !sidebar.contains(event.target) && !toggle) {
                sidebar.classList.remove('show');
            }
        });
        
        document.addEventListener('DOMContentLoaded', function () {
            const loader = document.getElementById('page-loader');
            if (loader) {
                setTimeout(() => loader.classList.add('fade-out'), 220);
            }
            
            const sidebarLinks = document.querySelectorAll('.sidebar-link[href]');
            sidebarLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (!href || href === '#' || href.startsWith('javascript') || this.getAttribute('target') === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
                    if (href === window.location.href || href === window.location.pathname) return;
                    e.preventDefault();
                    if (loader) loader.classList.remove('fade-out');
                    setTimeout(function () {
                        window.location.href = href;
                    }, 380);
                });
            });

            // Step 4: Global Modal Stacking Context Fix
            const modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                document.body.appendChild(modal);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
