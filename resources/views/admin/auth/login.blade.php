@extends('client.layouts.app')

@section('title', 'Admin Login')

@push('styles')
    <style>
        /* ✨ Auth Page Container ✨ */
        .auth-section {
            min-height: calc(100vh - 76px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            background: linear-gradient(160deg,
                    var(--bg-body) 0%,
                    #EEF2FF 100%);
        }

        /* ✨ Auth Card ✨ */
        .auth-card {
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08), 0 4px 16px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
            border: 1px solid var(--border-color);
        }

        /* ✨ Card Header ✨ */
        .auth-card-header {
            background: linear-gradient(135deg, #1E3A8A 0%, #0F172A 100%);
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
            background: rgba(212, 175, 55, 0.1);
            pointer-events: none;
        }

        .auth-brand {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
        }

        .auth-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            margin-bottom: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .auth-card-header h5 {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0;
        }

        .auth-card-header p {
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.83rem;
            margin: 0.35rem 0 0;
        }

        /* ✨ Card Body ✨ */
        .auth-card-body {
            padding: 2rem 2.5rem 2.5rem;
        }

        /* ✨ Form Controls ✨ */
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
            padding-left: 2.75rem;
            border-radius: 0.75rem;
            border: 1.5px solid var(--border-color);
            background: #F9FAFB;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .input-icon-wrap .form-control:focus {
            border-color: #1E3A8A;
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
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

        /* ✨ Submit Button ✨ */
        .btn-auth {
            background: linear-gradient(135deg, #1E3A8A 0%, #0F172A 100%);
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
            filter: brightness(1.08);
            color: #fff;
        }

        .btn-auth:active {
            transform: translateY(0);
        }

        /* ✨ Alerts ✨ */
        .alert-success-custom {
            border-radius: 0.75rem;
            font-size: 0.85rem;
            border: none;
            background: #F0FDF4;
            color: #166534;
            border-left: 4px solid #22C55E;
            padding: 0.9rem 1rem;
        }

        .alert-error-custom {
            border-radius: 0.75rem;
            font-size: 0.85rem;
            border: none;
            background: #FEF2F2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
            padding: 0.9rem 1rem;
        }

        .auth-divider {
            border-color: var(--border-color);
            margin: 1.5rem 0;
        }

        .forgot-link {
            color: #1E3A8A;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .forgot-link:hover {
            color: #0F172A;
            text-decoration: underline;
        }

        .form-check-input:checked {
            background-color: #1E3A8A;
            border-color: #1E3A8A;
        }

        .security-note {
            text-align: center;
            font-size: 0.74rem;
            color: #6B7280;
            margin-top: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }

        .portal-notice {
            background: #F8FAFC;
            border-radius: 0.65rem;
            padding: 0.75rem 1rem;
            font-size: 0.78rem;
            color: #64748B;
            text-align: center;
            border: 1px dashed #CBD5E1;
            margin-top: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <section class="auth-section">
        <div class="auth-card">

            {{-- Card Header --}}
            <div class="auth-card-header">
                <div class="auth-badge"><i class="bi bi-building me-1"></i>Hotel Administration</div>
                <div class="auth-brand">HotelBooking</div>
                <h5>Admin Portal</h5>
                <p>Sign in to manage hotel operations, rooms, and guests.</p>
            </div>

            {{-- Card Body --}}
            <div class="auth-card-body">

                @if(session('success'))
                    <div class="alert-success-custom mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-error-custom mb-4">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" novalidate>
                    @csrf

                    {{-- Email Address --}}
                    <div class="mb-3">
                        <label for="email" class="auth-label">Administrator Email</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="admin@hotelbooking.com"
                                value="{{ old('email') }}" autocomplete="email" required autofocus>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="auth-label mb-0">Password</label>
                            <a href="{{ route('admin.password.request') }}" class="forgot-link">Forgot password?</a>
                        </div>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="••••••••"
                                autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small text-muted" for="remember" style="font-size:0.82rem;">
                                Keep me signed in
                            </label>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-auth">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In as Admin
                    </button>

                    <div class="security-note">
                        <i class="bi bi-shield-check text-primary"></i>
                        Hotel management portal &mdash; Admin sessions are encrypted
                    </div>

                    <div class="portal-notice">
                        Super Administrator? <a href="{{ route('super_admin.login') }}" class="text-decoration-none fw-semibold">Super Admin Login</a>
                        &bull; Client? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Client Login</a>
                    </div>
                </form>

            </div>{{-- /.auth-card-body --}}
        </div>{{-- /.auth-card --}}
    </section>
@endsection

