@extends('client.layouts.app')

@section('title', 'My Dashboard')

@push('styles')
<style>
    /* ── Dashboard hero banner ── */
    .dashboard-hero {
        background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
        padding: 4rem 0 5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .dashboard-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: rgba(212, 175, 55, 0.06);
        pointer-events: none;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: -40px;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.03);
        pointer-events: none;
    }

    .welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(212, 175, 55, 0.15);
        border: 1px solid rgba(212, 175, 55, 0.35);
        color: var(--brand-accent);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 0.35rem 0.9rem;
        border-radius: 50px;
        margin-bottom: 1.25rem;
    }

    .welcome-heading {
        font-family: 'Poppins', sans-serif;
        font-size: 2.4rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .welcome-heading .client-name {
        background: linear-gradient(135deg, #fff 30%, var(--brand-accent) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .welcome-sub {
        color: rgba(255, 255, 255, 0.65);
        font-size: 1rem;
        margin-top: 0.25rem;
    }

    /* ── Overlap card section ── */
    .dashboard-content {
        margin-top: -2.5rem;
        padding-bottom: 4rem;
    }

    .info-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        padding: 1.75rem;
        height: 100%;
    }

    .info-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 1rem;
    }

    .info-card-title {
        font-family: 'Poppins', sans-serif;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .info-card-value {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* ── Coming soon panel ── */
    .coming-soon-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        padding: 2.5rem;
        text-align: center;
    }

    .coming-soon-icon {
        width: 72px;
        height: 72px;
        border-radius: 1rem;
        background: linear-gradient(135deg, var(--brand-primary) 0%, #1E3A8A 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #fff;
        margin: 0 auto 1.25rem;
    }
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<section class="dashboard-hero">
    <div class="container position-relative" style="z-index: 1;">
        <div class="welcome-badge">
            <i class="bi bi-patch-check-fill"></i> Client Portal
        </div>
        <h1 class="welcome-heading">
            Welcome back, <span class="client-name">{{ Auth::guard('web')->user()->name }}</span>
        </h1>
        <p class="welcome-sub">You're successfully signed in to your HotelBooking account.</p>
    </div>
</section>

{{-- ── Dashboard Content ── --}}
<section class="dashboard-content">
    <div class="container">

        {{-- Quick-Info Row --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon" style="background: rgba(212, 175, 55, 0.12); color: var(--brand-accent);">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="info-card-title">Account</div>
                    <div class="info-card-value" style="font-size: 1rem; word-break: break-all;">
                        {{ Auth::guard('web')->user()->email }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon" style="background: rgba(34, 197, 94, 0.12); color: #16A34A;">
                        <i class="bi bi-shield-check-fill"></i>
                    </div>
                    <div class="info-card-title">Status</div>
                    <div class="info-card-value" style="font-size: 1rem; color: #16A34A;">
                        Active
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon" style="background: rgba(99, 102, 241, 0.12); color: #6366F1;">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <div class="info-card-title">Member Since</div>
                    <div class="info-card-value" style="font-size: 1rem;">
                        {{ Auth::guard('web')->user()->created_at->format('M Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- My Reservations --}}
        <div class="mb-4">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
                <h5 style="font-family:'Poppins',sans-serif; font-weight:700; margin:0;">
                    <i class="bi bi-calendar-check me-2" style="color:var(--brand-accent);"></i>My Reservations
                </h5>
                <div>
                    <a href="{{ route('client.reservations.index') }}" class="btn btn-outline-secondary btn-sm px-3 me-2" style="font-weight:600; border-radius:.625rem;">
                        View All
                    </a>
                    <a href="{{ route('hotels.index') }}" class="btn btn-accent btn-sm px-3" style="font-weight:600; border-radius:.625rem;">
                        <i class="bi bi-plus-lg me-1"></i>New Booking
                    </a>
                </div>
            </div>

            @if($reservations->isEmpty())
                <div style="background:#fff; border:1px solid var(--border-color); border-radius:1rem; box-shadow:var(--card-shadow); padding:3rem; text-align:center;">
                    <i class="bi bi-calendar2-x" style="font-size:3rem; color:var(--text-muted); opacity:.4; display:block; margin-bottom:1rem;"></i>
                    <div style="font-weight:600; color:var(--text-primary); margin-bottom:.5rem;">No reservations yet</div>
                    <p style="color:var(--text-muted); font-size:.875rem; max-width:320px; margin:0 auto 1.5rem;">
                        Browse our hotels and book a room to see your reservations here.
                    </p>
                    <a href="{{ route('hotels.index') }}" class="btn btn-accent px-4">
                        <i class="bi bi-building me-2"></i>Browse Hotels
                    </a>
                </div>
            @else
                <div style="background:#fff; border:1px solid var(--border-color); border-radius:1rem; box-shadow:var(--card-shadow); overflow:hidden;">
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; font-size:.875rem;">
                            <thead>
                                <tr style="background:#F8FAFC; border-bottom:2px solid var(--border-color);">
                                    <th style="padding:.875rem 1.25rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em; white-space:nowrap;">Reference</th>
                                    <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Hotel / Room</th>
                                    <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em; white-space:nowrap;">Dates</th>
                                    <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Guests</th>
                                    <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Total</th>
                                    <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservations as $res)
                                @php
                                    [$statusBg, $statusColor, $statusBorder, $statusLabel] = match($res->status) {
                                        'pending'     => ['rgba(245,158,11,.1)',  '#92400E', 'rgba(245,158,11,.3)', 'Pending'],
                                        'confirmed'   => ['rgba(34,197,94,.1)',   '#166534', 'rgba(34,197,94,.3)',  'Confirmed'],
                                        'cancelled'   => ['rgba(239,68,68,.1)',   '#991B1B', 'rgba(239,68,68,.3)',  'Cancelled'],
                                        'checked_in'  => ['rgba(59,130,246,.1)', '#1E3A8A', 'rgba(59,130,246,.3)', 'Checked In'],
                                        'checked_out' => ['rgba(100,116,139,.1)','#374151', 'rgba(100,116,139,.3)','Checked Out'],
                                        default       => ['rgba(156,163,175,.1)', '#6B7280', 'rgba(156,163,175,.3)', ucfirst($res->status)],
                                    };
                                    $nights = \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out));
                                @endphp
                                <tr style="border-bottom:1px solid var(--border-color);">
                                    <td style="padding:.875rem 1.25rem; white-space:nowrap;">
                                        <span style="font-weight:700; color:var(--brand-primary); font-size:.82rem;">#MOR-RSV-{{ $res->id }}</span>
                                        <div style="font-size:.72rem; color:var(--text-muted); margin-top:.15rem;">
                                            {{ $res->created_at->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td style="padding:.875rem 1rem;">
                                        <div style="font-weight:600; color:var(--text-primary);">
                                            {{ $res->hotel->name ?? '—' }}
                                        </div>
                                        <div style="font-size:.78rem; color:var(--text-muted);">
                                            @if($res->room)
                                                Room {{ $res->room->room_number }}
                                                @if($res->room->type) · {{ $res->room->type }} @endif
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding:.875rem 1rem; white-space:nowrap;">
                                        <div style="font-weight:600; color:var(--text-primary);">
                                            {{ \Carbon\Carbon::parse($res->check_in)->format('d M Y') }}
                                        </div>
                                        <div style="font-size:.78rem; color:var(--text-muted);">
                                            → {{ \Carbon\Carbon::parse($res->check_out)->format('d M Y') }}
                                            <span style="margin-left:.35rem; font-weight:600;">{{ $nights }}n</span>
                                        </div>
                                    </td>
                                    <td style="padding:.875rem 1rem; text-align:center;">
                                        <span style="font-weight:600;">{{ $res->guests_count }}</span>
                                    </td>
                                    <td style="padding:.875rem 1rem; white-space:nowrap;">
                                        <span style="font-weight:700; color:var(--brand-primary);">{{ number_format($res->total_price, 2) }} MAD</span>
                                    </td>
                                    <td style="padding:.875rem 1rem;">
                                        <span style="display:inline-flex; align-items:center; gap:.3rem;
                                                     background:{{ $statusBg }}; color:{{ $statusColor }};
                                                     border:1px solid {{ $statusBorder }};
                                                     padding:.25rem .75rem; border-radius:50px;
                                                     font-size:.72rem; font-weight:700; letter-spacing:.04em;
                                                     white-space:nowrap;">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <p style="font-size:.75rem; color:var(--text-muted); margin-top:.75rem; text-align:right;">
                    <i class="bi bi-info-circle me-1"></i>
                    Showing your {{ $reservations->count() }} most recent reservation{{ $reservations->count() !== 1 ? 's' : '' }}. Status updates in real time as the hotel confirms your request.
                </p>
            @endif
        </div>

    </div>
</section>

@endsection
