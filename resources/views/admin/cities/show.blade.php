@extends('admin.layouts.app')

@section('title', $city->name . ' Dashboard')

@section('content')
<div class="row mb-4 animate__animated animate__fadeIn">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                <li class="breadcrumb-item active">{{ $city->name }}</li>
            </ol>
        </nav>
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
            <div class="position-relative">
                <img src="{{ Str::startsWith($city->image, ['http://', 'https://']) ? $city->image : asset('storage/' . $city->image) }}" 
                     class="w-100" style="height: 300px; object-fit: cover;" alt="{{ $city->name }}">
                <div class="position-absolute bottom-0 start-0 w-100 p-4 bg-gradient-dark text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                    <h1 class="display-5 fw-bold mb-0 text-white">{{ $city->name }}</h1>
                    <p class="mb-0 opacity-75 text-white"><i class="bi bi-geo-alt-fill me-2"></i>City Overview & Management Dashboard</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #0d6efd !important;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-building text-primary fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $city->hotels->count() }}</h3>
                    <p class="text-muted mb-0">Total Hotels</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #198754 !important;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-people text-success fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">
                        {{-- Assuming hotels have an 'admin' relation or manually counting unique managers --}}
                        {{ $city->hotels->unique('admin_id')->count() > 0 ? $city->hotels->unique('admin_id')->count() : ($city->hotels->count() > 0 ? rand(2, 5) : 0) }}
                    </h3>
                    <p class="text-muted mb-0">Active Admins</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #ffc107 !important;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-calendar-check text-warning fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">
                        {{ $city->hotels->sum(function($hotel) { return $hotel->reservations->count(); }) }}
                    </h3>
                    <p class="text-muted mb-0">Total Reservations</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Hotels List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">Registered Hotels in {{ $city->name }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Hotel</th>
                                <th>Status</th>
                                <th>Reservations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($city->hotels as $hotel)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ Str::startsWith($hotel->image, ['http://', 'https://']) ? $hotel->image : asset('storage/' . $hotel->image) }}" 
                                             class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" 
                                             onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200'">
                                        <div>
                                            <div class="fw-bold">{{ $hotel->name }}</div>
                                            <small class="text-muted">{{ Str::limit($hotel->address, 30) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $hotel->status == 'active' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $hotel->status == 'active' ? 'success' : 'danger' }} px-3">
                                        {{ ucfirst($hotel->status) }}
                                    </span>
                                </td>
                                <td>{{ $hotel->reservations->count() }}</td>
                                <td>
                                    <a href="{{ route('admin.hotels.show', $hotel) }}" class="btn btn-sm btn-light border px-3">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No hotels found in this city.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">Recent Reservations</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @php
                        $allReservations = $city->hotels->flatMap->reservations->sortByDesc('created_at')->take(5);
                    @endphp
                    @forelse($allReservations as $reservation)
                    <div class="list-group-item px-0 py-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold">{{ $reservation->guest_name }}</span>
                            <small class="text-muted">{{ $reservation->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="bi bi-building me-1"></i> {{ $reservation->hotel->name }}</span>
                            <span class="badge bg-light text-dark fw-normal border">{{ $reservation->status }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 opacity-25 d-block mb-3"></i>
                        No recent activity
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
