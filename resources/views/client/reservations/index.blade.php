@extends('client.layouts.app')

@section('title', 'My Reservations')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-family: 'Poppins', sans-serif; font-weight: 700;">My Reservations</h2>
        <a href="{{ route('hotels.index') }}" class="btn btn-accent px-4 py-2" style="border-radius: 0.625rem;">Book New Room</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div style="background:#fff; border:1px solid var(--border-color); border-radius:1rem; box-shadow:var(--card-shadow); overflow:hidden;">
        @if($reservations->isEmpty())
            <div class="p-5 text-center">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">You don't have any reservations yet.</h5>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:.875rem;">
                    <thead>
                        <tr style="background:#F8FAFC; border-bottom:2px solid var(--border-color);">
                            <th style="padding:.875rem 1.25rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Reference</th>
                            <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Hotel / Room</th>
                            <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Dates</th>
                            <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Total</th>
                            <th style="padding:.875rem 1rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em;">Status</th>
                            <th style="padding:.875rem 1.25rem; font-weight:700; color:var(--text-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em; text-align:right;">Actions</th>
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
                        @endphp
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:.875rem 1.25rem;">
                                <span style="font-weight:700; color:var(--brand-primary); font-size:.82rem;">#MOR-RSV-{{ $res->id }}</span>
                                <div style="font-size:.72rem; color:var(--text-muted); margin-top:.15rem;">
                                    {{ $res->created_at->format('d M Y') }}
                                </div>
                            </td>
                            <td style="padding:.875rem 1rem;">
                                <div style="font-weight:600; color:var(--text-primary);">{{ $res->hotel->name ?? '—' }}</div>
                                <div style="font-size:.78rem; color:var(--text-muted);">
                                    @if($res->room) Room {{ $res->room->room_number }} @endif
                                </div>
                            </td>
                            <td style="padding:.875rem 1rem; white-space:nowrap;">
                                <div style="font-weight:600; color:var(--text-primary);">{{ \Carbon\Carbon::parse($res->check_in)->format('d M Y') }}</div>
                                <div style="font-size:.78rem; color:var(--text-muted);">→ {{ \Carbon\Carbon::parse($res->check_out)->format('d M Y') }}</div>
                            </td>
                            <td style="padding:.875rem 1rem; white-space:nowrap;">
                                <span style="font-weight:700; color:var(--brand-primary);">{{ number_format($res->total_price, 2) }} MAD</span>
                            </td>
                            <td style="padding:.875rem 1rem;">
                                <span style="display:inline-flex; align-items:center; background:{{ $statusBg }}; color:{{ $statusColor }}; border:1px solid {{ $statusBorder }}; padding:.25rem .75rem; border-radius:50px; font-size:.72rem; font-weight:700; letter-spacing:.04em;">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="padding:.875rem 1.25rem; text-align:right;">
                                <a href="{{ route('client.reservations.show', $res) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600;">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $reservations->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
