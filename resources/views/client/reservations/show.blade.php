@extends('client.layouts.app')

@section('title', 'Reservation Details')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-family: 'Poppins', sans-serif; font-weight: 700;">Reservation Details</h2>
        <a href="{{ route('client.reservations.index') }}" class="btn btn-outline-secondary" style="border-radius: 0.625rem;">Back to Reservations</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4" style="border-radius: 1rem; border: 1px solid var(--border-color);">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Booking Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Reference</div>
                        <div class="col-sm-8 font-weight-bold">#MOR-RSV-{{ $reservation->id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Hotel</div>
                        <div class="col-sm-8">{{ $reservation->hotel->name ?? '—' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Room</div>
                        <div class="col-sm-8">@if($reservation->room) Room {{ $reservation->room->room_number }} @else — @endif</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Check-in</div>
                        <div class="col-sm-8">{{ \Carbon\Carbon::parse($reservation->check_in)->format('l, d M Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Check-out</div>
                        <div class="col-sm-8">{{ \Carbon\Carbon::parse($reservation->check_out)->format('l, d M Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Duration</div>
                        <div class="col-sm-8">
                            @php
                                $nights = \Carbon\Carbon::parse($reservation->check_in)->diffInDays(\Carbon\Carbon::parse($reservation->check_out));
                            @endphp
                            {{ $nights }} night{{ $nights !== 1 ? 's' : '' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Guests</div>
                        <div class="col-sm-8">{{ $reservation->guests_count }} guest{{ $reservation->guests_count !== 1 ? 's' : '' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Total Price</div>
                        <div class="col-sm-8 font-weight-bold text-success">{{ number_format($reservation->total_price, 2) }} MAD</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4" style="border-radius: 1rem; border: 1px solid var(--border-color);">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</h5>
                    
                    @php
                        [$statusBg, $statusColor, $statusBorder, $statusLabel] = match($reservation->status) {
                            'pending'     => ['rgba(245,158,11,.1)',  '#92400E', 'rgba(245,158,11,.3)', 'Pending'],
                            'confirmed'   => ['rgba(34,197,94,.1)',   '#166534', 'rgba(34,197,94,.3)',  'Confirmed'],
                            'cancelled'   => ['rgba(239,68,68,.1)',   '#991B1B', 'rgba(239,68,68,.3)',  'Cancelled'],
                            'checked_in'  => ['rgba(59,130,246,.1)', '#1E3A8A', 'rgba(59,130,246,.3)', 'Checked In'],
                            'checked_out' => ['rgba(100,116,139,.1)','#374151', 'rgba(100,116,139,.3)','Checked Out'],
                            default       => ['rgba(156,163,175,.1)', '#6B7280', 'rgba(156,163,175,.3)', ucfirst($reservation->status)],
                        };
                    @endphp
                    
                    <div class="mb-4">
                        <span style="display:inline-flex; align-items:center; background:{{ $statusBg }}; color:{{ $statusColor }}; border:1px solid {{ $statusBorder }}; padding:.5rem 1rem; border-radius:50px; font-size:.85rem; font-weight:700; letter-spacing:.04em;">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    @if(in_array($reservation->status, ['pending', 'confirmed']))
                        <hr>
                        <h6 class="text-danger mt-3">Cancel Reservation</h6>
                        <p class="text-muted small">You can cancel your reservation before check-in. This action cannot be undone.</p>
                        
                        <form action="{{ route('client.reservations.cancel', $reservation) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100" style="border-radius: 0.625rem;">Cancel Booking</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
