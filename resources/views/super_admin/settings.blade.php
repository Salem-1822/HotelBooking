@extends('super_admin.layouts.app')

@section('title', __('super_admin.system_configuration'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
    <div>
        <h5 class="fw-bold mb-1" style="color:#0F172A;font-size:1.25rem;">{{ __('super_admin.system_configuration') }}</h5>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge fw-semibold px-3 py-2" style="background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;border-radius:0.625rem;font-size:0.72rem;">
                <i class="bi bi-shield-lock-fill me-1"></i>{{ __('super_admin.super_admin_only') }}
            </span>
            <p class="text-muted small mb-0 d-none d-sm-block" style="font-size:0.8rem;">{{ __('super_admin.manage_global_settings') }}</p>
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
                            <h6 class="fw-bold mb-0" style="font-size:0.95rem;color:#0F172A;">{{ __('super_admin.general_settings') }}</h6>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="form-label">{{ __('super_admin.site_name') }}</label>
                            <input type="text" name="settings[site_name]"
                                value="{{ $settings['site_name'] ?? '' }}"
                                class="form-control" placeholder="e.g. HotelBooking">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('super_admin.default_currency') }}</label>
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
                            <h6 class="fw-bold mb-0" style="font-size:0.95rem;color:#0F172A;">{{ __('super_admin.contact_information') }}</h6>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="form-label">{{ __('super_admin.admin_email') }}</label>
                            <input type="email" name="settings[admin_email]"
                                value="{{ $settings['admin_email'] ?? '' }}"
                                class="form-control" placeholder="admin@hotelbooking.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('super_admin.support_phone') }}</label>
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
                            <h6 class="fw-bold mb-0" style="font-size:0.95rem;color:#0F172A;">{{ __('super_admin.system_settings_address') }}</h6>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label">{{ __('super_admin.headquarters_address') }}</label>
                            <textarea name="settings[address]" class="form-control" rows="3"
                                placeholder="{{ __('super_admin.full_street_address') }}">{{ $settings['address'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit Area --}}
    <div class="d-flex justify-content-end mt-4 pt-2">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="font-size:0.875rem;">
            <i class="bi bi-save me-2"></i> {{ __('super_admin.save_settings') }}
        </button>
    </div>
</form>
@endsection
