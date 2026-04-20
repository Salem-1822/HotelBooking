@extends('super_admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-building text-primary fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Total Hotels</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['hotels'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-calendar-check text-success fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Reservations</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['reservations'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-geo-alt text-warning fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Cities</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['cities'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-cash-stack text-info fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Revenue</h6>
                    <h4 class="fw-bold mb-0">{{ number_format($stats['revenue'], 0) }} <small class="fw-normal text-muted" style="font-size: 0.75rem;">MAD</small></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 h-100">
            <h5 class="fw-bold mb-4">Reservations Overview</h5>
            <div class="bg-light rounded text-center py-5 border" style="border-style: dashed !important;">
                <p class="text-muted mb-0">Live Analytics Chart Placeholder</p>
                <i class="bi bi-graph-up fs-1 text-primary opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4 h-100">
            <h5 class="fw-bold mb-4">Recently Added Hotels</h5>
            <div class="list-group list-group-flush">
                @forelse($recent_hotels as $hotel)
                <div class="list-group-item px-0 border-0 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3 p-2 bg-light rounded text-primary">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-bold">{{ $hotel->name }}</p>
                            <small class="text-muted">{{ $hotel->city->name }}</small>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted py-3">No hotels found.</p>
                @endforelse
            </div>
            <a href="{{ route('super_admin.hotels.index') }}" class="btn btn-light border w-100 mt-auto">View All Hotels</a>
        </div>
    </div>
</div>
@endsection
