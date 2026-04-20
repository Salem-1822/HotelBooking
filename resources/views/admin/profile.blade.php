@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 fade-in">
    <div>
        <h5 class="fw-bold mb-0">Super Admin Profile</h5>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge bg-danger rounded-pill shadow-sm">Super Admin Only</span>
            <p class="text-muted small mb-0 d-none d-sm-block">Manage your personal account details.</p>
        </div>
    </div>
</div>

<div class="row g-4 fade-in">
    <!-- Profile Card -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm transition hover-lift" style="border-radius: 1rem;">
            <div class="card-body p-4 text-center">
                <div class="mb-4 position-relative d-inline-block image-container">
                    @if($admin->profile_image && \Storage::disk('public')->exists('profiles/' . $admin->profile_image))
                        <img src="{{ asset('storage/profiles/' . $admin->profile_image) }}" id="profilePreview" alt="Profile" class="profile-img rounded-circle shadow-sm border border-3 border-white" style="width: 140px; height: 140px; object-fit: cover;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=0F172A&color=fff&size=200" id="profilePreview" alt="Profile" class="profile-img rounded-circle shadow-sm border border-3 border-white" style="width: 140px; height: 140px; object-fit: cover;">
                    @endif
                    <div class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle shadow" style="width:36px; height:36px; line-height:24px; cursor: pointer;" onclick="document.getElementById('profile_image').click();" title="Change Photo">
                        <i class="bi bi-camera"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">{{ $admin->name }}</h5>
                <p class="text-muted small mb-3">{{ $admin->email }}</p>
                <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm text-capitalize border" style="font-size: 0.75rem;">
                    {{ str_replace('_', ' ', $admin->role) }}
                </span>

                @if($admin->profile_image)
                <div class="mt-4 pt-3 border-top">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        <button type="submit" name="remove_image" value="1" class="btn btn-sm btn-outline-danger fw-bold px-3 shadow-sm" style="border-radius: 0.5rem;" onclick="return confirm('Are you sure you want to remove your profile photo?');">
                            <i class="bi bi-trash-fill me-1"></i> Remove Photo
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i> Edit Profile</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/*" onchange="previewImage(this)">

                    <div class="row g-3">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="form-control bg-light border-0 py-2 input-focus" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="form-control bg-light border-0 py-2 input-focus" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 mb-4">
                            <label class="form-label small fw-bold text-muted">Password <span class="fw-normal text-muted" style="font-size:0.7rem;">(Leave blank to keep current)</span></label>
                            <input type="password" name="password" class="form-control bg-light border-0 py-2 input-focus" placeholder="••••••••">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 text-end mt-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" style="border-radius: 0.5rem;">
                                <i class="bi bi-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in {
        animation: smoothFade 0.6s ease-out forwards;
        opacity: 0;
    }

    @keyframes smoothFade {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.08) !important;
    }

    .image-container .profile-img {
        transition: transform 0.4s ease;
    }

    .image-container:hover .profile-img {
        transform: scale(1.05);
    }

    .input-focus {
        transition: all 0.2s ease;
    }

    .input-focus:focus {
        background-color: #fff !important;
        border-color: var(--brand-primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.15) !important;
        transform: translateY(-1px);
    }
</style>

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
