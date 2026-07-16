@extends('super_admin.layouts.app')

@section('title', 'System Configuration')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
    <div>
        <h5 class="fw-bold mb-1" style="color:#0F172A;font-size:1.25rem;">System Configuration</h5>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge fw-semibold px-3 py-2" style="background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;border-radius:0.625rem;font-size:0.72rem;">
                <i class="bi bi-shield-lock-fill me-1"></i>Super Admin Only
            </span>
            <p class="text-muted small mb-0 d-none d-sm-block" style="font-size:0.8rem;">Manage global application settings and preferences.</p>
        </div>
    </div>
</div>

<form action="{{ route('super_admin.settings.update') }}" method="POST">
    @csrf

    <div class="row g-4">
        {{-- General Settings --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100" style="border-radius:1rem;">
                <div class="card-body p-0">
                    <div class="p-4 pb-3" style="border-bottom:1px solid #F3F4F6;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border-radius:0.625rem;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-sliders" style="color:#1E3A8A;font-size:0.9rem;"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="font-size:0.95rem;color:#0F172A;">General Settings</h6>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="form-label">Site Name</label>
                            <input type="text" name="settings[site_name]"
                                value="{{ $settings['site_name'] ?? '' }}"
                                class="form-control" placeholder="e.g. HotelBooking">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Default Currency</label>
                            <input type="text" name="settings[currency]"
                                value="{{ $settings['currency'] ?? 'MAD' }}"
                                class="form-control" placeholder="e.g. MAD, USD, EUR">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact Info --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100" style="border-radius:1rem;">
                <div class="card-body p-0">
                    <div class="p-4 pb-3" style="border-bottom:1px solid #F3F4F6;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;background:linear-gradient(135deg,#ECFDF5,#D1FAE5);border-radius:0.625rem;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-envelope-paper" style="color:#059669;font-size:0.9rem;"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="font-size:0.95rem;color:#0F172A;">Contact Information</h6>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="form-label">Admin Email</label>
                            <input type="email" name="settings[admin_email]"
                                value="{{ $settings['admin_email'] ?? '' }}"
                                class="form-control" placeholder="admin@hotelbooking.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Support Phone</label>
                            <input type="text" name="settings[phone]"
                                value="{{ $settings['phone'] ?? '' }}"
                                class="form-control" placeholder="+212 600-000000">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Settings --}}
        <div class="col-12">
            <div class="card" style="border-radius:1rem;">
                <div class="card-body p-0">
                    <div class="p-4 pb-3" style="border-bottom:1px solid #F3F4F6;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;background:linear-gradient(135deg,#FFF7ED,#FED7AA);border-radius:0.625rem;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-building-gear" style="color:#EA580C;font-size:0.9rem;"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="font-size:0.95rem;color:#0F172A;">System Settings & Address</h6>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label">Headquarters Address</label>
                            <textarea name="settings[address]" class="form-control" rows="3"
                                placeholder="Full street address">{{ $settings['address'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit Area --}}
    <div class="d-flex justify-content-end mt-4 pt-2">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="font-size:0.875rem;">
            <i class="bi bi-save me-2"></i> Save Settings
        </button>
    </div>
</form>
@endsection
