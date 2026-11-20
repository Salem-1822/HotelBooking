@extends('client.layouts.app')

@section('title', __('admin.reset_password_title'))

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
        max-width: 440px;
        background: #fff;
        border-radius: 1.25rem;
        box-shadow: 0 8px 40px rgba(15, 23, 42, 0.10);
        overflow: hidden;
    }

    .auth-card-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
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

    /* ── Error Alert ── */
    .alert-error-custom {
        border-radius: 0.75rem;
        font-size: 0.85rem;
        border: none;
        background: #FEF2F2;
        color: #991B1B;
        border-left: 4px solid #EF4444;
        padding: 0.9rem 1rem;
    }

    /* ── Divider ── */
    .auth-divider {
        border-color: var(--border-color);
        margin: 1.5rem 0;
    }

    /* ── Password hint ── */
    .pw-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.3rem;
    }

    /* ── Admin badge ── */
    .admin-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(212, 175, 55, 0.15);
        color: var(--brand-accent);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
<section class="auth-section">
    <div class="auth-card reveal">

        {{-- Card Header --}}
        <div class="auth-card-header">
            <div class="auth-brand reveal delay-1">HotelBooking</div>
            <div class="admin-badge reveal delay-1">
                <i class="bi bi-shield-fill"></i> {{ __('admin.admin_portal') }}
            </div>
            <h5 class="reveal delay-1">{{ __('admin.reset_password_title') }}</h5>
            <p class="reveal delay-2">{{ __('admin.reset_password_subtitle') }}</p>
        </div>

        {{-- Card Body --}}
        <div class="auth-card-body reveal delay-3">

            {{-- Validation errors (invalid / expired token) --}}
            @if($errors->any())
                <div class="alert-error-custom mb-4">
                    <ul class="mb-0 list-unstyled">
                        @foreach($errors->all() as $error)
                            <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}" novalidate>
                @csrf

                {{-- Hidden token --}}
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email Address (pre-filled from the reset link) --}}
                <div class="mb-3">
                    <label for="email" class="auth-label">{{ __('admin.email_address') }}</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope input-icon"></i>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control auth-email-input @error('email') is-invalid @enderror"
                            placeholder="admin@example.com"
                            value="{{ old('email', $email) }}"
                            autocomplete="email"
                            required
                        >
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- New Password --}}
                <div class="mb-3">
                    <label for="password" class="auth-label">{{ __('admin.new_password') }}</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control has-toggle @error('password') is-invalid @enderror"
                            placeholder="{{ __('admin.password_min_chars') }}"
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
                    <p class="pw-hint">{{ __('admin.password_min_chars') }}</p>
                </div>

                {{-- Confirm New Password --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="auth-label">{{ __('admin.confirm_new_password') }}</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control has-toggle @error('password_confirmation') is-invalid @enderror"
                            placeholder="{{ __('admin.repeat_new_password') }}"
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
                    <i class="bi bi-shield-check me-2"></i>{{ __('admin.reset_password_title') }}
                </button>
            </form>

            <hr class="auth-divider">

            <p class="auth-footer-text reveal delay-4">
                {{ __('admin.remember_password') }}
                <a href="{{ route('admin.login') }}">{{ __('admin.back_to_sign_in') }}</a>
            </p>

        </div>{{-- /.auth-card-body --}}
    </div>{{-- /.auth-card --}}
</section>
@endsection
