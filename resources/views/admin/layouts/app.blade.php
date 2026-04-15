<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin | Salem Hotel</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-bg: #111827; --sidebar-width: 260px; }
        body { background-color: #f3f4f6; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .sidebar { 
            width: var(--sidebar-width); 
            background: var(--sidebar-bg); 
            min-height: 100vh; 
            position: fixed; 
            top: 0; left: 0; 
            z-index: 100;
            transition: all 0.3s;
        }
        .sidebar-link { 
            padding: 0.8rem 1.5rem; 
            display: flex; 
            align-items: center; 
            color: rgba(255,255,255,0.7); 
            text-decoration: none; 
            border-left: 4px solid transparent;
            transition: 0.2s;
        }
        .sidebar-link:hover, .sidebar-link.active { 
            background: rgba(255,255,255,0.05); 
            color: #fff; 
            border-left-color: #3b82f6; 
        }
        .sidebar-link i { margin-right: 12px; font-size: 1.1rem; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; transition: all 0.3s; }
        .navbar { 
            height: 70px; 
            background: #fff; 
            border-bottom: 1px solid #e5e7eb; 
            padding: 0 1.5rem; 
        }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 0.75rem; }
        .card-title { font-weight: 700; color: #111827; }
        @media (max-width: 991.98px) { 
            .sidebar { left: calc(-1 * var(--sidebar-width)); }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="p-4 text-center">
            <h4 class="text-white fw-bold mb-0">SALEM<span class="text-primary">HOTEL</span></h4>
        </div>
        <nav class="mt-2">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.hotels.index') }}" class="sidebar-link {{ request()->routeIs('admin.hotels.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Hotels
            </a>
            <a href="{{ route('admin.cities.index') }}" class="sidebar-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Cities
            </a>
            <a href="{{ route('admin.admins.index') }}" class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Admins
            </a>
            <a href="{{ route('admin.reservations.index') }}" class="sidebar-link {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Reservations
            </a>
            <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Reports
            </a>
            <a href="{{ route('admin.exports') }}" class="sidebar-link {{ request()->routeIs('admin.exports') ? 'active' : '' }}">
                <i class="bi bi-download"></i> Exports
            </a>
            <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Settings
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="navbar d-flex align-items-center justify-content-between sticky-top">
            <button class="btn d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0 fw-bold">@yield('title', 'Admin Panel')</h5>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=Super+Admin&background=0D6EFD&color=fff" width="35" height="35" class="rounded-circle me-2">
                    <span class="d-none d-sm-inline fw-semibold">Super Admin</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                </ul>
            </div>
        </header>

        <div class="p-4">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
