@extends('admin.layouts.app')

@section('title', 'Global Reservations')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Recent Reservations</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reservations.export') }}" class="btn-export">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>
    </div>
    <div class="p-3 border-bottom bg-light">
        <form action="{{ route('admin.reservations.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Status: All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-secondary w-100">Clear</a>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-nowrap">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Reference</th>
                    <th>Guest Name</th>
                    <th>Hotel Name</th>
                    <th>Check In/Out</th>
                    <th>Total Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $res)
                <tr>
                    <td class="ps-4 fw-bold text-primary">#RSV-{{ $res->id }}</td>
                    <td class="fw-bold">{{ $res->guest_name }}</td>
                    <td>{{ $res->hotel->name }}</td>
                    <td><small class="text-muted">{{ $res->check_in }} - {{ $res->check_out }}</small></td>
                    <td><span class="fw-bold text-dark">${{ number_format($res->total_price, 2) }}</span></td>
                    <td>
                        @php
                            $badge = match($res->status) {
                                'confirmed' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'cancelled' => 'bg-danger',
                                'completed' => 'bg-info',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badge }} rounded-pill px-3">{{ ucfirst($res->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No reservations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $reservations->links() }}
    </div>
</div>
@endsection
