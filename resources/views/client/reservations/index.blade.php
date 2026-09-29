@extends('client.layouts.app')

@section('title', 'My Reservations')

@push('styles')
<style>
/* ── Page Header ─────────────────────────────── */
.page-header {
    background: var(--brand-primary);
    padding: 2.5rem 0 2rem;
    margin-bottom: 0;
}
.page-header h1 {
    color: #fff;
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
}
.page-header .breadcrumb-item,
.page-header .breadcrumb-item a {
    color: rgba(255,255,255,0.55);
    font-size: 0.82rem;
    text-decoration: none;
}
.page-header .breadcrumb-item.active { color: rgba(255,255,255,0.9); }
.page-header .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.3); }

/* ── Reservation Card ────────────────────────── */
.res-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 0.875rem;
    overflow: hidden;
    display: flex;
    transition: box-shadow 0.2s ease, transform 0.2s ease;
    margin-bottom: 1rem;
}
.res-card:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-2px);
}
.res-card-accent {
    width: 5px;
    flex-shrink: 0;
}
.res-card-accent.pending    { background: #F59E0B; }
.res-card-accent.confirmed  { background: #22C55E; }
.res-card-accent.checked_in  { background: #3B82F6; }
.res-card-accent.checked_out { background: #94A3B8; }
.res-card-accent.cancelled  { background: #EF4444; }

.res-card-body {
    padding: 1.25rem 1.5rem;
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
}
.res-info-block { min-width: 0; }
.res-ref {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 0.2rem;
}
.res-hotel-name {
    font-size: 1rem;
    font-weight: 700;
    color: var(--brand-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 220px;
}
.res-room {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-top: 0.1rem;
}
.res-dates {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}
.res-dates .date-label {
    font-size: 0.68rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.res-dates .date-val {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
}
.res-nights-badge {
    display: inline-block;
    background: rgba(212,175,55,0.1);
    color: #92630A;
    border: 1px solid rgba(212,175,55,0.3);
    border-radius: 50px;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.15rem 0.6rem;
    margin-top: 0.3rem;
    white-space: nowrap;
}
.res-price {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--brand-primary);
    white-space: nowrap;
}
.res-price small {
    font-size: 0.7rem;
    font-weight: 400;
    color: var(--text-muted);
    display: block;
}
.res-status-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.3rem 0.875rem;
    letter-spacing: 0.04em;
    white-space: nowrap;
}
.res-card-actions {
    display: flex;
    align-items: center;
    padding: 1.25rem 1.5rem 1.25rem 0;
    flex-shrink: 0;
}

/* ── Empty State ─────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 0.875rem;
}
.empty-state i { font-size: 3rem; color: #CBD5E1; margin-bottom: 1.25rem; display: block; }
.empty-state h5 { color: var(--text-primary); font-weight: 600; margin-bottom: 0.5rem; }
.empty-state p  { color: var(--text-muted); font-size: 0.9rem; }

/* ── Flash Messages ──────────────────────────── */
.alert { border-radius: 0.75rem; border: none; }
.alert-success { background: rgba(34,197,94,0.1); color: #15803D; }
.alert-danger  { background: rgba(239,68,68,0.1);  color: #991B1B; }

/* ── Responsive ──────────────────────────────── */
@media (max-width: 640px) {
    .res-card { flex-direction: column; }
    .res-card-accent { width: 100%; height: 4px; }
    .res-card-body { padding: 1rem; }
    .res-card-actions { padding: 0 1rem 1rem; justify-content: flex-end; }
    .res-hotel-name { max-width: 100%; }
}
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">My Reservations</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h1><i class="bi bi-calendar-check me-2" style="color:var(--brand-accent); font-size:1.4rem;"></i>My Reservations</h1>
            <a href="{{ route('hotels.index') }}" class="btn btn-accent" style="font-size:0.875rem; padding:0.55rem 1.4rem;">
                <i class="bi bi-plus-lg me-1"></i> Book New Room
            </a>
        </div>
    </div>
</div>

<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}
        </div>
    @endif

    @if($reservations->isEmpty())
        <div class="empty-state">
            <i class="bi bi-calendar-x"></i>
            <h5>No reservations yet</h5>
            <p>You haven't made any reservations. Explore our hotels and book your perfect stay.</p>
            <a href="{{ route('hotels.index') }}" class="btn btn-accent mt-3">Browse Hotels</a>
        </div>
    @else

        {{-- Summary bar --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <p class="mb-0 text-muted" style="font-size:0.875rem;">
                Showing <strong>{{ $reservations->count() }}</strong> reservation{{ $reservations->count() !== 1 ? 's' : '' }}
                @if($reservations->total() > $reservations->count())
                    of <strong>{{ $reservations->total() }}</strong> total
                @endif
            </p>
        </div>

        @foreach($reservations as $res)
        @php
            $statusMap = [
                'pending'     => ['Pending',     'rgba(245,158,11,.1)', '#92400E', 'rgba(245,158,11,.3)'],
                'confirmed'   => ['Confirmed',   'rgba(34,197,94,.1)',  '#166534', 'rgba(34,197,94,.3)'],
                'cancelled'   => ['Cancelled',   'rgba(239,68,68,.1)', '#991B1B', 'rgba(239,68,68,.3)'],
                'checked_in'  => ['Checked In',  'rgba(59,130,246,.1)','#1E3A8A', 'rgba(59,130,246,.3)'],
                'checked_out' => ['Checked Out', 'rgba(100,116,139,.1)','#374151','rgba(100,116,139,.3)'],
            ];
            [$statusLabel, $statusBg, $statusColor, $statusBorder] = $statusMap[$res->status] ?? [ucfirst($res->status), 'rgba(156,163,175,.1)', '#6B7280', 'rgba(156,163,175,.3)'];
            $nights = \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out));
        @endphp

        <div class="res-card">
            <div class="res-card-accent {{ $res->status }}"></div>
            <div class="res-card-body">

                {{-- Hotel & Reference --}}
                <div class="res-info-block" style="flex: 2; min-width: 160px;">
                    <div class="res-ref">#MOR-RSV-{{ $res->id }} &nbsp;·&nbsp; {{ $res->created_at->format('d M Y') }}</div>
                    <div class="res-hotel-name">{{ $res->hotel->name ?? '—' }}</div>
                    <div class="res-room">
                        @if($res->room)
                            <i class="bi bi-door-open me-1" style="color:var(--brand-accent);"></i>Room {{ $res->room->room_number }}
                            @if($res->room->type) · <span>{{ ucfirst($res->room->type) }}</span>@endif
                        @endif
                    </div>
                </div>

                {{-- Dates --}}
                <div class="res-info-block res-dates" style="min-width: 130px;">
                    <div>
                        <div class="date-label">Check-in</div>
                        <div class="date-val">{{ \Carbon\Carbon::parse($res->check_in)->format('d M Y') }}</div>
                    </div>
                    <div style="margin-top:0.5rem;">
                        <div class="date-label">Check-out</div>
                        <div class="date-val">{{ \Carbon\Carbon::parse($res->check_out)->format('d M Y') }}</div>
                    </div>
                    <span class="res-nights-badge">{{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</span>
                </div>

                {{-- Guests --}}
                <div class="res-info-block d-none d-md-block" style="min-width: 80px;">
                    <div class="res-ref">Guests</div>
                    <div style="font-weight:600; font-size:0.9rem; color:var(--text-primary); margin-top:0.2rem;">
                        <i class="bi bi-people me-1" style="color:var(--brand-accent);"></i>{{ $res->guests_count }}
                    </div>
                </div>

                {{-- Price --}}
                <div class="res-info-block" style="min-width: 100px;">
                    <div class="res-ref">Total</div>
                    <div class="res-price">{{ number_format($res->total_price, 0) }} MAD</div>
                </div>

                {{-- Status --}}
                <div class="res-info-block" style="min-width: 110px;">
                    <span class="res-status-badge" style="background:{{ $statusBg }}; color:{{ $statusColor }}; border:1px solid {{ $statusBorder }};">
                        {{ $statusLabel }}
                    </span>
                    @if($res->status === 'checked_out' && !$res->review)
                        <div style="margin-top:0.4rem;">
                            <span style="font-size:0.68rem; color:var(--brand-accent); font-weight:600;">
                                <i class="bi bi-star me-1"></i>Awaiting review
                            </span>
                        </div>
                    @elseif($res->status === 'checked_out' && $res->review)
                        <div style="margin-top:0.4rem;">
                            <span style="font-size:0.68rem; color:#166534; font-weight:600;">
                                <i class="bi bi-check-circle me-1"></i>Reviewed
                            </span>
                        </div>
                    @endif
                </div>

            </div>
            {{-- Action --}}
            <div class="res-card-actions">
                <a href="{{ route('client.reservations.show', $res) }}"
                   class="btn btn-sm"
                   style="border:1.5px solid var(--border-color); color:var(--brand-primary); border-radius:0.625rem; font-weight:600; font-size:0.8rem; padding:0.45rem 1.1rem; white-space:nowrap; transition:all 0.2s ease;"
                   onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.background='var(--brand-primary)'; this.style.color='#fff';"
                   onmouseout="this.style.borderColor='var(--border-color)'; this.style.background='transparent'; this.style.color='var(--brand-primary)';">
                    View Details
                </a>
            </div>
        </div>
        @endforeach

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $reservations->links('pagination::bootstrap-5') }}
        </div>

    @endif
</div>
@endsection
