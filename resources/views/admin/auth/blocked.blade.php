@extends('client.layouts.app')

@section('title', 'Administrator Account Suspended')

@push('styles')
    <style>
        /* Auth Page Container */
        .blocked-section {
            min-height: calc(100vh - 76px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            background: linear-gradient(160deg, var(--bg-body) 0%, #FEE2E2 100%);
        }

        /* Auth Card */
        .blocked-card {
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08), 0 4px 16px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            width: 100%;
            max-width: 540px;
            border: 1px solid var(--border-color);
        }

        /* Card Header */
        .blocked-card-header {
            background: linear-gradient(135deg, #991B1B 0%, #0F172A 100%);
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .blocked-card-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.15);
            pointer-events: none;
        }

        .blocked-brand {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #FCA5A5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
        }

        .blocked-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(239, 68, 68, 0.25);
            color: #FEE2E2;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.3rem 0.85rem;
            border-radius: 9999px;
            margin-bottom: 0.5rem;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .blocked-icon-wrapper {
            width: 72px;
            height: 72px;
            margin: 0 auto 1.25rem;
            background: rgba(239, 68, 68, 0.12);
            border: 2px solid rgba(239, 68, 68, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #DC2626;
            font-size: 2.25rem;
        }

        .reason-box {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-left: 4px solid #DC2626;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin: 1.5rem 0;
            text-align: left;
        }

        .reason-box .reason-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #991B1B;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .reason-box .reason-text {
            color: #1F2937;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 0;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: pre-line;
        }

        .btn-return-login {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #fff;
            font-weight: 600;
            padding: 0.85rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            border: none;
        }

        .btn-return-login:hover {
            background: linear-gradient(135deg, #1E293B 0%, #334155 100%);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
        }
    </style>
@endpush

@section('content')
<div class="blocked-section">
    <div class="blocked-card">
        <!-- Header -->
        <div class="blocked-card-header">
            <div class="blocked-brand">HotelBooking</div>
            <span class="blocked-badge">
                <i class="bi bi-shield-x"></i> Admin Portal
            </span>
            <h5 class="text-white mb-0 fw-semibold">Administrator Account Suspended</h5>
        </div>

        <!-- Body -->
        <div class="p-4 p-md-5 text-center">
            <div class="blocked-icon-wrapper">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>

            <!-- Greeting -->
            <h4 class="fw-bold text-dark mb-2">Hello {{ $adminName }},</h4>

            <!-- Main Message -->
            <p class="text-muted mb-3" style="font-size: 1rem; line-height: 1.6;">
                Your HotelBooking administrator account has been blocked by the Super Administrator.
            </p>

            <!-- Status Indicator -->
            <div class="mb-3">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-slash-circle me-1"></i> Account Blocked
                </span>
            </div>

            <!-- Blocking Reason Box -->
            <div class="reason-box">
                <div class="reason-title">
                    <i class="bi bi-info-circle-fill"></i> Block Reason:
                </div>
                <p class="reason-text">{{ $blockedReason }}</p>
                @if(!empty($blockedAt))
                    <div class="text-muted small mt-2 pt-2 border-top" style="font-size: 0.78rem;">
                        <i class="bi bi-clock-history me-1"></i> Effective Date: {{ \Carbon\Carbon::parse($blockedAt)->format('M d, Y - H:i') }}
                    </div>
                @endif
            </div>

            <!-- Additional Message -->
            <p class="text-muted small mb-4" style="line-height: 1.6;">
                For more information regarding this decision, please contact the Super Administrator.
            </p>

            <!-- Return Action -->
            <a href="{{ route('admin.login') }}" class="btn-return-login">
                <i class="bi bi-arrow-left"></i> Return to Login Page
            </a>
        </div>
    </div>
</div>
@endsection
