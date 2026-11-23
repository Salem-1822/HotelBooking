@extends('client.layouts.app')

@section('title', __('auth.forgot_password_title'))

@push('styles')
<style>
    /* ── Page Layout ── */
    .auth-section {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: linear-gradient(135deg, #F8FAFC 0%, #EEF2FF 100%);
    }

    /* ── Card ── */
    :root {
        --brand-primary: #0F172A;
        --brand-accent:  #D4AF37;
        --border-color:  #E5E7EB;
        --text-muted:    #6B7280;
    }

    .auth-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 1.25rem;
        box-shadow: 0 8px 40px rgba(15, 23, 42, 0.10);
        overflow: hidden;
    }

    .auth-card-header {
        background: var(--brand-primary);
        color: #fff;
        padding: 2rem 2.5rem 1.5rem;
        text-align: center;
    }

    .auth-brand {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-size: 1.4rem;
        letter-spacing: -0.02em;
        color: var(--brand-accent);
        margin-bottom: 0.5rem;
    }

    .auth-card-header h5 {
        margin: 0 0 0.35rem;
        font-weight: 700;
        font-size: 1.15rem;
    }

    .auth-card-header p {
        margin: 0;
        font-size: 0.83rem;
        opacity: 0.75;
    }

    .auth-card-body {
        padding: 2rem 2.5rem 2.5rem;
    }

    /* ── Form Controls ── */
    .auth-label {
        font-weight: 600;
        font-size: 0.8rem;
        color: #374151;
        margin-bottom: 0.35rem;
        letter-spacing: 0.01em;
    }

    .input-icon-wrap {
        position: relative;
    }

    .input-icon-wrap .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 1rem;
        pointer-events: none;
        z-index: 2;
    }

    .input-icon-wrap .form-control {
        padding-left: 3.25rem !important;
        padding-right: 1.25rem !important;
        border-radius: 0.75rem;
        border: 1.5px solid var(--border-color);
        background: #F9FAFB;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    [dir="rtl"] .input-icon-wrap .input-icon,
    html[dir="rtl"] .input-icon-wrap .input-icon {
        left: auto !important;
        right: 1rem !important;
    }

    [dir="rtl"] .input-icon-wrap .form-control,
    html[dir="rtl"] .input-icon-wrap .form-control {
        padding-right: 3.25rem !important;
        padding-left: 1.25rem !important;
        text-align: right !important;
    }

    [dir="rtl"] .input-icon-wrap input[type="email"],
    [dir="rtl"] .input-icon-wrap .auth-email-input,
    html[dir="rtl"] .input-icon-wrap input[type="email"],
    html[dir="rtl"] .input-icon-wrap .auth-email-input {
        direction: ltr !important;
        text-align: right !important;
        padding-right: 3.25rem !important;
        padding-left: 1.25rem !important;
    }

    [dir="rtl"] .input-icon-wrap input[type="email"]::placeholder,
    [dir="rtl"] .input-icon-wrap .auth-email-input::placeholder,
    html[dir="rtl"] .input-icon-wrap input[type="email"]::placeholder,
    html[dir="rtl"] .input-icon-wrap .auth-email-input::placeholder {
        text-align: right !important;
        direction: rtl !important;
    }

    .input-icon-wrap .form-control:focus {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.08);
        background: #fff;
    }

    .input-icon-wrap .form-control.is-invalid {
        border-color: #EF4444;
        background: #FFF5F5;
    }

    .invalid-feedback {
        font-size: 0.78rem;
        color: #DC2626;
        margin-top: 0.3rem;
    }

    /* ── Submit Button ── */
    .btn-auth {
        background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
        color: #fff;
        border: none;
        padding: 0.8rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.95rem;
        width: 100%;
        transition: all 0.2s ease;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.2);
        letter-spacing: 0.01em;
        font-family: 'Poppins', sans-serif;
    }

    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.28);
        filter: brightness(1.07);
    }

    .btn-auth:active { transform: translateY(0); }

    /* ── Footer Link ── */
    .auth-footer-text {
        text-align: center;
        font-size: 0.855rem;
        color: var(--text-muted);
        margin-top: 1.5rem;
    }

    .auth-footer-text a {
        color: var(--brand-primary);
        font-weight: 600;
        text-decoration: none;
    }

    .auth-footer-text a:hover {
        color: #1E3A8A;
        text-decoration: underline;
    }

    /* ── Status Alert ── */
    .alert-status-custom {
        border-radius: 0.75rem;
        font-size: 0.85rem;
        border: none;
        background: #F0FDF4;
        color: #166534;
        border-inline-start: 4px solid #22C55E;
        padding: 0.9rem 1rem;
    }

    /* ── Divider ── */
    .auth-divider {
        border-color: var(--border-color);
        margin: 1.5rem 0;
    }

    /* ── Info note ── */
    .auth-info-note {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-align: center;
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<section class="auth-section">
    <div class="auth-card reveal">

        {{-- Card Header --}}
        <div class="auth-card-header">
            <div class="auth-brand reveal delay-1">HotelBooking</div>
            <h1 class="h5 reveal delay-1">{{ __('auth.forgot_password_title') }}</h1>
            <p class="reveal delay-2">{{ __('auth.forgot_password_subtitle') }}</p>
        </div>

        {{-- Card Body --}}
        <div class="auth-card-body reveal delay-3">

            {{-- Generic status message (anti-enumeration) --}}
            @if(session('status'))
                <div class="alert-status-custom mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                </div>
            @endif

            @unless(session('status'))
                <p class="auth-info-note">
                    {{ __('auth.reset_link_info') }}<br>
                    {{ __('auth.link_expires_in', ['minutes' => config('auth.passwords.users.expire', 60)]) }}
                </p>

                <form method="POST" action="{{ route('client.password.email') }}" novalidate>
                    @csrf

                    {{-- Email Address --}}
                    <div class="mb-4">
                        <label for="email" class="auth-label">{{ __('auth.email') }}</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope input-icon"></i>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control auth-email-input @error('email') is-invalid @enderror"
                                placeholder="{{ __('auth.email_placeholder') }}"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                autofocus
                            >
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-auth">
                        <i class="bi bi-send me-2"></i>{{ __('auth.send_reset_link') }}
                    </button>
                </form>
            @endunless

            <hr class="auth-divider">

            <p class="auth-footer-text reveal delay-4 mb-2">
                {{ __('auth.remember_password') }}
                <a href="{{ route('login') }}">{{ __('auth.back_to_sign_in') }}</a>
            </p>

            <p class="auth-footer-text reveal delay-4 mt-0" style="font-size: 0.78rem;">
                {{ __('auth.hotel_administrator') }}
                <a href="{{ route('admin.password.request') }}">{{ __('auth.admin_reset_portal') }}</a>
            </p>

        </div>{{-- /.auth-card-body --}}
    </div>{{-- /.auth-card --}}
</section>
@endsection
