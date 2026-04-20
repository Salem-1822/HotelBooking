@extends('super_admin.layouts.app')

@section('title', 'Create New Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 1.25rem;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Create Admin Account</h4>
                            <p class="text-muted small mb-0">Fill in the details to register a new administrator and assign them to a city.</p>
                        </div>
                        <a href="{{ route('super_admin.admins.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('super_admin.admins.store') }}" method="POST" autocomplete="off">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Basic Information Section -->
                            <div class="col-12">
                                <h6 class="text-uppercase fw-bold text-primary small mb-3 ls-wide">Basic Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label small fw-bold">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                                            <input type="text" name="name" id="name" class="form-control bg-light border-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Ahmed Benjelloun" required autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label small fw-bold">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                                            <input type="email" name="email" id="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="example@hotelia.com" required autocomplete="off">
                                        </div>
                                        @error('email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Assignment Section -->
                            <div class="col-12">
                                <h6 class="text-uppercase fw-bold text-primary small mb-3 ls-wide">Regional Assignment</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="city_id" class="form-label small fw-bold">City to Manage</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="bi bi-geo-alt text-muted"></i></span>
                                            <select name="city_id" id="city_id" class="form-select bg-light border-0 @error('city_id') is-invalid @enderror" required>
                                                <option value="" selected disabled>Select a city...</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-text small opacity-75 mt-1">
                                            <i class="bi bi-info-circle me-1"></i> Admin will automatically manage all hotels in this city.
                                        </div>
                                        @error('city_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="form-label small fw-bold">Temporary Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="bi bi-shield-lock text-muted"></i></span>
                                            <input type="password" name="password" id="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" required autocomplete="new-password">
                                        </div>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submission Section -->
                            <div class="col-12 pt-3">
                                <div class="p-3 bg-light rounded-3 mb-4">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="bi bi-shield-check fs-4 text-success"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 small text-dark fw-bold">Security Notice</p>
                                            <p class="mb-0 small text-muted">A verification email will be optional. These credentials will grant access to all city-level data including bookings and hotel management.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm border-0 fw-bold">
                                        <i class="bi bi-person-check-fill me-2"></i> Confirm and Create
                                    </button>
                                    <button type="reset" class="btn btn-light px-4 py-2 rounded-3 fw-bold">Reset Form</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-wide {
        letter-spacing: 0.05rem;
    }
    .form-control:focus, .form-select:focus {
        background-color: #f8fafc !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem !important;
    }
    .form-control, .form-select {
        border-radius: 0 0.5rem 0.5rem 0 !important;
        padding: 0.75rem 1rem;
    }
</style>
@endsection
