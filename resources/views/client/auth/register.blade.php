@extends('client.layouts.app')

@section('title', __('auth.register_title'))

@push('styles')
<style>
    /* ── Auth Page Container ── */
    .auth-section {
        min-height: calc(100vh - 76px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        background: linear-gradient(
            160deg,
            var(--bg-body) 0%,
            #EEF2FF 100%
        );
    }

    /* ── Auth Card ── */
    .auth-card {
        background: #fff;
        border-radius: 1.25rem;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08), 0 4px 16px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        width: 100%;
        max-width: 480px;
        border: 1px solid var(--border-color);
    }

    /* ── Card Header ── */
    .auth-card-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
        padding: 2.25rem 2.5rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .auth-card-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(212, 175, 55, 0.07);
        pointer-events: none;
    }

    .auth-brand {
        font-family: 'Poppins', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        background: linear-gradient(135deg, #fff 0%, var(--brand-accent) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
    }

    .auth-card-header h1,
    .auth-card-header h5 {
        color: rgba(255, 255, 255, 0.95);
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
    }

    .auth-card-header p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.83rem;
        margin: 0.35rem 0 0;
    }

    /* ── Card Body ── */
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

    .input-icon-wrap .form-control.has-toggle {
        padding-left: 3.25rem !important;
        padding-right: 3.25rem !important;
    }

    .password-toggle-btn {
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #9CA3AF;
        padding: 0.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 5;
        font-size: 1.1rem;
        line-height: 1;
        transition: color 0.15s ease;
    }

    .password-toggle-btn:hover {
        color: #374151;
    }

    .password-toggle-btn:focus {
        outline: none;
        color: var(--brand-primary);
    }

    [dir="rtl"] .password-toggle-btn,
    html[dir="rtl"] .password-toggle-btn {
        right: auto !important;
        left: 0.85rem !important;
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

    [dir="rtl"] .input-icon-wrap .form-control.has-toggle,
    html[dir="rtl"] .input-icon-wrap .form-control.has-toggle {
        padding-right: 3.25rem !important;
        padding-left: 3.25rem !important;
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

    /* ── Success Alert ── */
    .alert-success-custom {
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
</style>
@endpush

@section('content')
<section class="auth-section">
    <div class="auth-card reveal">

        {{-- Card Header --}}
        <div class="auth-card-header">
            <div class="auth-brand reveal delay-1">HotelBooking</div>
            <h1 class="h5 reveal delay-1">{{ __('auth.register_title') }}</h1>
            <p class="reveal delay-2">{{ __('auth.register_subtitle') }}</p>
        </div>

        {{-- Card Body --}}
        <div class="auth-card-body reveal delay-3">

            {{-- Success flash (after redirect from registration) --}}
            @if(session('success'))
                <div class="alert-success-custom mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('client.register.submit') }}" novalidate>
                @csrf

                {{-- Full Name --}}
                <div class="mb-3">
                    <label for="name" class="auth-label">{{ __('auth.full_name') }}</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="{{ __('auth.full_name_placeholder') }}"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required
                            autofocus
                        >
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email Address --}}
                <div class="mb-3">
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
                        >
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="auth-label">{{ __('auth.password_label') }}</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control has-toggle @error('password') is-invalid @enderror"
                            placeholder="{{ __('auth.password_placeholder') }}"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="password-toggle-btn" data-target="password" aria-label="{{ __('auth.toggle_password') }}">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="auth-label">{{ __('auth.confirm_password') }}</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control has-toggle @error('password_confirmation') is-invalid @enderror"
                            placeholder="{{ __('auth.confirm_password_placeholder') }}"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="password-toggle-btn" data-target="password_confirmation" aria-label="{{ __('auth.toggle_password') }}">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-auth">
                    <i class="bi bi-person-plus me-2"></i>{{ __('auth.create_account') }}
                </button>
            </form>

            <hr class="auth-divider">

            <p class="auth-footer-text reveal delay-4">
                {{ __('auth.already_have_account') }}
                <a href="{{ route('login') }}">{{ __('auth.sign_in') }}</a>
            </p>

        </div>{{-- /.auth-card-body --}}
    </div>{{-- /.auth-card --}}
</section>
@endsection
