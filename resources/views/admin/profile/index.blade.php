@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">My Profile</h4>
            <p class="text-muted mb-0 fs-6">Manage your personal information and security settings</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Profile Card & Hotel Info -->
        <div class="col-xl-4 col-lg-5">
            <!-- Profile Overview Card -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 1rem;">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-4 mt-2">
                        @if($admin->profile_image && \Storage::disk('public')->exists('profiles/' . $admin->profile_image))
                            <img src="{{ asset('storage/profiles/' . $admin->profile_image) }}" alt="{{ $admin->name }}" 
                                class="rounded-circle img-thumbnail shadow-sm" style="width: 130px; height: 130px; object-fit: cover; border-width: 3px;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=0F172A&color=fff&size=150" alt="{{ $admin->name }}" 
                                class="rounded-circle img-thumbnail shadow-sm" style="width: 130px; height: 130px; object-fit: cover; border-width: 3px;">
                        @endif

                        @if($admin->profile_image)
                            <form action="{{ route('admin.profile.photo.destroy') }}" method="POST" class="position-absolute" style="bottom: 0; right: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-circle shadow" title="Remove Photo" onclick="return confirm('Are you sure you want to remove your profile photo?')" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-1">{{ $admin->name }}</h5>
                    <p class="text-muted mb-3 text-capitalize">{{ str_replace('_', ' ', $admin->role) }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Active
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top p-4">
                    <h6 class="fw-bold mb-3 fs-6"><i class="bi bi-building me-2 text-muted"></i> Assigned Hotel</h6>
                    @if($admin->hotel)
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-light rounded p-2 text-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-shop text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $admin->hotel->name }}</h6>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt-fill me-1"></i>
                                    {{ $admin->hotel->city ? $admin->hotel->city->name : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0 small fst-italic">No hotel assigned.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Forms -->
        <div class="col-xl-8 col-lg-7">
            
            <!-- Personal Information Form -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 1rem;">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="mb-0 fw-bold">Personal Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $admin->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="profile_image" class="form-label fw-semibold">Profile Photo</label>
                                <input type="file" class="form-control @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <div class="form-text small text-muted">Max file size: 5MB. Formats: JPG, PNG, WEBP.</div>
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Form -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 1rem;" id="password-section">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="mb-0 fw-bold">Change Password</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                <div class="form-text small text-muted">Must be at least 8 characters long.</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-shield-lock me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('password_section'))
            document.getElementById('password-section').scrollIntoView({ behavior: 'smooth' });
        @endif
    });
</script>
@endpush
