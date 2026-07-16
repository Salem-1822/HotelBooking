@extends('super_admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
{{-- Welcome Banner --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
    <div>
        <h4 class="fw-bold mb-1" style="color:#0F172A;font-size:1.5rem;">
            Welcome back, {{ Auth::guard('admin')->user()->name }}! 👋
        </h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">
            Here's what's happening at HotelBooking today.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge fw-semibold px-3 py-2" style="background:#EFF6FF;color:#1E3A8A;border:1px solid #BFDBFE;border-radius:0.625rem;font-size:0.78rem;">
            <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;color:#10B981;"></i>
            System Online
        </span>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row g-4 mb-5">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-wrap" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);">
                    <i class="bi bi-building-fill" style="color:#1E3A8A;"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 fw-semibold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.06em;">Total Hotels</p>
                    <h3 class="fw-bold mb-0" style="font-size:1.75rem;color:#0F172A;line-height:1;">{{ $stats['hotels'] }}</h3>
                </div>
            </div>
            <div class="mt-3 pt-3" style="border-top:1px solid #F3F4F6;">
                <a href="{{ route('super_admin.hotels.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.78rem;color:#1E3A8A;">
                    Manage Hotels <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-wrap" style="background:linear-gradient(135deg,#ECFDF5,#D1FAE5);">
                    <i class="bi bi-calendar-check-fill" style="color:#059669;"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 fw-semibold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.06em;">Reservations</p>
                    <h3 class="fw-bold mb-0" style="font-size:1.75rem;color:#0F172A;line-height:1;">{{ $stats['reservations'] }}</h3>
                </div>
            </div>
            <div class="mt-3 pt-3" style="border-top:1px solid #F3F4F6;">
                <a href="{{ route('super_admin.reservations.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.78rem;color:#059669;">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-wrap" style="background:linear-gradient(135deg,#FFF7ED,#FED7AA);">
                    <i class="bi bi-geo-alt-fill" style="color:#EA580C;"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 fw-semibold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.06em;">Cities</p>
                    <h3 class="fw-bold mb-0" style="font-size:1.75rem;color:#0F172A;line-height:1;">{{ $stats['cities'] }}</h3>
                </div>
            </div>
            <div class="mt-3 pt-3" style="border-top:1px solid #F3F4F6;">
                <a href="{{ route('super_admin.cities.index') }}" class="text-decoration-none fw-semibold" style="font-size:0.78rem;color:#EA580C;">
                    Manage Cities <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-wrap" style="background:linear-gradient(135deg,#FDFAE7,#FEF3C7);">
                    <i class="bi bi-cash-stack" style="color:#D97706;"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 fw-semibold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.06em;">Revenue</p>
                    <h3 class="fw-bold mb-0" style="font-size:1.5rem;color:#0F172A;line-height:1;">
                        {{ number_format($stats['revenue'], 0) }}
                        <small class="fw-normal text-muted" style="font-size:0.65rem;">MAD</small>
                    </h3>
                </div>
            </div>
            <div class="mt-3 pt-3" style="border-top:1px solid #F3F4F6;">
                <span class="fw-semibold" style="font-size:0.78rem;color:#D97706;">
                    <i class="bi bi-graph-up me-1"></i>Total Earnings
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Lower Row --}}
<div class="row g-4">
    {{-- Chart Placeholder --}}
    <div class="col-lg-8">
        <div class="card h-100" style="border-radius:1rem;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h6 class="fw-bold mb-1" style="font-size:1rem;color:#0F172A;">Reservations Overview</h6>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">Booking trends across all properties</p>
                    </div>
                    <span class="badge fw-semibold px-3 py-2" style="background:#EFF6FF;color:#1E3A8A;border:1px solid #BFDBFE;border-radius:0.5rem;font-size:0.72rem;">
                        Live Data
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="height:200px;background:linear-gradient(135deg,#F8FAFC,#EFF6FF);border-radius:0.875rem;border:2px dashed #DBEAFE;">
                    <div class="text-center">
                        <i class="bi bi-graph-up-arrow" style="font-size:2.5rem;color:#BFDBFE;"></i>
                        <p class="text-muted mt-2 mb-0 fw-medium" style="font-size:0.85rem;">Analytics Chart — Live Data Placeholder</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Hotels --}}
    <div class="col-lg-4">
        <div class="card h-100" style="border-radius:1rem;">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="fw-bold mb-0" style="font-size:1rem;color:#0F172A;">Recently Added Hotels</h6>
                    <span class="badge" style="background:#EFF6FF;color:#1E3A8A;border-radius:0.5rem;font-size:0.72rem;padding:0.35rem 0.65rem;">
                        Latest
                    </span>
                </div>
                <div class="flex-grow-1">
                    @forelse($recent_hotels as $hotel)
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom:1px solid #F3F4F6;">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3"
                            style="width:42px;height:42px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);">
                            <i class="bi bi-building-fill" style="color:#1E3A8A;font-size:1rem;"></i>
                        </div>
                        <div class="overflow-hidden">
                            <p class="mb-0 fw-semibold text-truncate" style="font-size:0.875rem;color:#0F172A;">{{ $hotel->name }}</p>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1" style="color:#D4AF37;"></i>{{ $hotel->city->name }}
                            </small>
                        </div>
                        <span class="ms-auto badge {{ $hotel->status == 'active' ? '' : '' }} px-2 py-1 flex-shrink-0"
                            style="font-size:0.65rem;font-weight:600;
                            background:{{ $hotel->status == 'active' ? '#ECFDF5' : '#FEF9C3' }};
                            color:{{ $hotel->status == 'active' ? '#059669' : '#A16207' }};
                            border-radius:0.375rem;">
                            {{ ucfirst($hotel->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="bi bi-building" style="font-size:2rem;color:#E5E7EB;"></i>
                        <p class="text-muted mt-2 mb-0" style="font-size:0.82rem;">No hotels found</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ route('super_admin.hotels.index') }}" class="btn btn-light border fw-semibold w-100 mt-3"
                    style="border-radius:0.625rem;font-size:0.875rem;color:#1E3A8A;border-color:#DBEAFE!important;background:#F0F7FF;">
                    <i class="bi bi-grid me-2"></i>View All Hotels
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
