@extends('admin.layouts.app')

@section('title', 'System Configuration')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h5 class="fw-bold mb-0">System Configuration</h5>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge bg-danger rounded-pill shadow-sm">Super Admin Only</span>
            <p class="text-muted small mb-0 d-none d-sm-block">Manage global application settings and preferences.</p>
        </div>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- General Settings -->
        <div class="col-12 col-xl-6">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-sliders me-2 text-primary"></i> General Settings</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Site Name</label>
                        <input type="text" name="settings[site_name]" value="{{ $settings['site_name'] ?? '' }}" class="form-control bg-light border-0 py-2" placeholder="e.g. HOTELIA">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Default Currency</label>
                        <input type="text" name="settings[currency]" value="{{ $settings['currency'] ?? 'MAD' }}" class="form-control bg-light border-0 py-2" placeholder="e.g. MAD, USD, EUR">
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="col-12 col-xl-6">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-envelope-paper me-2 text-primary"></i> Contact Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Admin Email</label>
                        <input type="email" name="settings[admin_email]" value="{{ $settings['admin_email'] ?? '' }}" class="form-control bg-light border-0 py-2" placeholder="admin@hotelia.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Support Phone</label>
                        <input type="text" name="settings[phone]" value="{{ $settings['phone'] ?? '' }}" class="form-control bg-light border-0 py-2" placeholder="+212 600-000000">
                    </div>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-building-gear me-2 text-primary"></i> System Settings & Address</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Headquarters Address</label>
                        <textarea name="settings[address]" class="form-control bg-light border-0" rows="3" placeholder="Full street address">{{ $settings['address'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Area -->
    <div class="d-flex justify-content-end mt-4 px-2">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" style="border-radius: 0.5rem;">
            <i class="bi bi-save me-2"></i> Save Settings
        </button>
    </div>
</form>

<style>
    .form-control:focus {
        background-color: #fff !important;
        border-color: #f97316 !important;
        box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.25);
    }
</style>
@endsection
