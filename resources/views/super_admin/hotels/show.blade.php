@extends('super_admin.layouts.app')

@section('title', __('super_admin.hotel_details'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">{{ __('super_admin.hotel_details') }}</h5>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('super_admin.hotels.index') }}" class="text-decoration-none">{{ __('super_admin.hotel_management') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('super_admin.hotels.index') }}" class="btn btn-light border shadow-sm rounded-pill px-4 fw-bold">
        <i class="bi bi-arrow-left me-2"></i> {{ __('super_admin.back') }}
    </a>
</div>

<div class="row g-4">
    <!-- Main Info -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-md-center gap-4 mb-4">
                    <div class="bg-primary bg-opacity-10 rounded text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 80px; height: 80px;">
                        <i class="bi bi-building fs-1"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1">{{ $hotel->name }}</h3>
                        <p class="text-muted mb-0"><i class="bi bi-geo-alt me-1 text-primary"></i> {{ $hotel->address }}, {{ $hotel->city->name }}</p>
                    </div>
                    <div class="ms-md-auto text-md-end mt-3 mt-md-0">
                        <span class="badge {{ $hotel->status == 'active' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 rounded-pill text-capitalize mb-2 shadow-sm" style="font-size: 0.8rem;">
                            {{ $hotel->status == 'active' ? __('super_admin.active') : __('super_admin.inactive') }}
                        </span>
                        <p class="text-muted small fw-bold mb-0">HOTEL ID: #HTL-{{ $hotel->id }}</p>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i> {{ __('super_admin.pricing_information') }}</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">{{ __('super_admin.price_per_night') }}</span>
                            <span class="fs-4 fw-bold text-dark">{{ number_format($hotel->price_per_night, 2) }} <small class="text-muted fs-6">{{ __('super_admin.mad') }}</small></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">{{ __('super_admin.location_base') }}</span>
                            <span class="fs-5 fw-bold text-dark">{{ $hotel->city->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
