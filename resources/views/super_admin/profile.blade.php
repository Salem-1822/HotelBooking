@extends('super_admin.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
    <div>
        <h5 class="fw-bold mb-1" style="color:#0F172A;font-size:1.25rem;">My Profile</h5>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge fw-semibold px-3 py-2" style="background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;border-radius:0.625rem;font-size:0.72rem;">
                <i class="bi bi-shield-lock-fill me-1"></i>Super Admin Only
            </span>
            <p class="text-muted small mb-0 d-none d-sm-block" style="font-size:0.8rem;">Manage your personal account details.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-12 col-xl-4">
        <div class="card" style="border-radius:1rem;">
            <div class="card-body p-4 text-center">
                {{-- Avatar Section --}}
                <div class="mb-4 position-relative d-inline-block">
                    @if($admin->profile_image && \Storage::disk('public')->exists('profiles/' . $admin->profile_image))
                        <img src="{{ asset('storage/profiles/' . $admin->profile_image) }}" id="profilePreview"
                            alt="Profile"
                            style="width:120px;height:120px;object-fit:cover;border-radius:50%;border:4px solid #EFF6FF;box-shadow:0 8px 24px rgba(30,58,138,0.15);">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=1E3A8A&color=fff&size=200"
                            id="profilePreview" alt="Profile"
                            style="width:120px;height:120px;object-fit:cover;border-radius:50%;border:4px solid #EFF6FF;box-shadow:0 8px 24px rgba(30,58,138,0.15);">
                    @endif
                    <button type="button" onclick="document.getElementById('profile_image').click();"
                        class="position-absolute bottom-0 end-0 btn d-flex align-items-center justify-content-center"
                        style="width:34px;height:34px;border-radius:50%;background:var(--brand-primary,#1E3A8A);color:#fff;border:2px solid #fff;padding:0;box-shadow:0 4px 10px rgba(30,58,138,0.3);" title="Change Photo">
                        <i class="bi bi-camera" style="font-size:0.8rem;"></i>
                    </button>
                </div>

                <h5 class="fw-bold mb-1" style="color:#0F172A;">{{ $admin->name }}</h5>
                <p class="text-muted mb-3" style="font-size:0.82rem;">{{ $admin->email }}</p>
                <span class="badge fw-semibold px-3 py-2 text-capitalize"
                    style="background:#EFF6FF;color:#1E3A8A;border:1px solid #BFDBFE;border-radius:0.625rem;font-size:0.75rem;">
                    <i class="bi bi-shield-check me-1"></i>{{ str_replace('_', ' ', $admin->role) }}
                </span>

                @if($admin->profile_image)
                <div class="mt-4 pt-3" style="border-top:1px solid #F3F4F6;">
                    <form action="{{ route('super_admin.profile.update') }}" method="POST">
                        @csrf
                        <button type="submit" name="remove_image" value="1"
                            class="btn btn-sm fw-semibold px-3"
                            style="border-radius:0.625rem;background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;"
                            onclick="return confirm('Remove your profile photo?');">
                            <i class="bi bi-trash me-1"></i> Remove Photo
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="col-12 col-xl-8">
        <div class="card" style="border-radius:1rem;">
            <div class="card-body p-0">
                <div class="p-4 pb-3" style="border-bottom:1px solid #F3F4F6;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:34px;height:34px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border-radius:0.625rem;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-person-lines-fill" style="color:#1E3A8A;font-size:0.9rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-0" style="font-size:0.95rem;color:#0F172A;">Edit Profile Information</h6>
                    </div>
                </div>
                <div class="p-4">
                    <form action="{{ route('super_admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/*"
                            onchange="previewImage(this)">

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name"
                                    value="{{ old('name', $admin->name) }}"
                                    class="form-control" required>
                                @error('name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email"
                                    value="{{ old('email', $admin->email) }}"
                                    class="form-control" required>
                                @error('email') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    New Password
                                    <span class="fw-normal text-muted ms-1" style="font-size:0.72rem;">(Leave blank to keep current)</span>
                                </label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••">
                                @error('password') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12 mt-2">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" style="font-size:0.875rem;">
                                        <i class="bi bi-save me-2"></i> Save Changes
                                    </button>
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
    #profilePreview {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    #profilePreview:hover {
        transform: scale(1.04);
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
