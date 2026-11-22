@extends('admin.layouts.app')

@section('title', __('admin.customer_information'))

@push('styles')
<style>
    .page-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
        flex-wrap: wrap;
    }
    .page-title-bar .title-group h4 {
        font-size: 1.55rem;
        letter-spacing: -0.02em;
    }
    .info-card {
        border-radius: 1rem;
        background: #fff;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        padding: 1.5rem;
        height: 100%;
    }
    .info-card h6 {
        font-weight: 700;
        color: #1E293B;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
    }
    .info-item {
        margin-bottom: 1rem;
    }
    .info-label {
        font-size: 0.75rem;
        color: #64748B;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1rem;
        color: #0F172A;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="page-title-bar mb-4">
    <div class="title-group">
        <h4 class="fw-bold mb-1">{{ __('admin.customer_detail_title', ['name' => $customer->name]) }}</h4>
        <p class="mb-0 text-muted">{{ __('admin.detailed_view_history') }}</p>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1 rtl-flip"></i> {{ __('admin.back_to_customers') }}
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Customer Information -->
    <div class="col-md-6">
        <div class="info-card">
            <h6><i class="bi bi-person-fill text-primary me-2"></i> {{ __('admin.customer_information') }}</h6>
            <div class="row">
                <div class="col-sm-6 info-item">
                    <div class="info-label">{{ __('admin.th_full_name') }}</div>
                    <div class="info-value">{{ $customer->name }}</div>
                </div>
                <div class="col-sm-6 info-item">
                    <div class="info-label">{{ __('admin.th_phone') }}</div>
                    <div class="info-value"><span dir="ltr">{{ $customer->phone }}</span></div>
                </div>
                <div class="col-sm-6 info-item mb-0">
                    <div class="info-label">{{ __('admin.th_email') }}</div>
                    <div class="info-value">{{ $customer->email ?? '—' }}</div>
                </div>
                <div class="col-sm-6 info-item mb-0">
                    <div class="info-label">{{ __('admin.created_at') }}</div>
                    <div class="info-value">{{ $customer->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Summary -->
    <div class="col-md-6">
        <div class="info-card">
            <h6><i class="bi bi-pie-chart-fill text-success me-2"></i> {{ __('admin.reservation_summary') }}</h6>
            <div class="row">
                <div class="col-sm-6 info-item">
                    <div class="info-label">{{ __('admin.total_reservations') }}</div>
                    <div class="info-value"><span class="badge bg-light text-dark border">{{ $customer->reservations_count }}</span></div>
                </div>
                <div class="col-sm-6 info-item">
                    <div class="info-label">{{ __('admin.th_status') }}</div>
                    <div class="info-value">
                        @if($customer->reservations_count >= 5)
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">{{ __('admin.vip') }}</span>
                        @elseif($customer->reservations_count >= 2)
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">{{ __('admin.returning') }}</span>
                        @else
                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ __('admin.new') }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 info-item mb-0">
                    <div class="info-label">{{ __('admin.first_reservation') }}</div>
                    <div class="info-value">{{ $customer->reservations->last()?->check_in ? \Carbon\Carbon::parse($customer->reservations->last()->check_in)->format('M d, Y') : '—' }}</div>
                </div>
                <div class="col-sm-6 info-item mb-0">
                    <div class="info-label">{{ __('admin.last_reservation') }}</div>
                    <div class="info-value">{{ $customer->reservations->first()?->check_in ? \Carbon\Carbon::parse($customer->reservations->first()->check_in)->format('M d, Y') : '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reservation History -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.85rem;">
            <i class="bi bi-clock-history text-muted me-2"></i> {{ __('admin.reservation_history') }}
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4">{{ __('admin.th_id') }}</th>
                    <th>{{ __('admin.created_at') }}</th>
                    <th>{{ __('admin.check_in_date') }}</th>
                    <th>{{ __('admin.check_out_date') }}</th>
                    <th>{{ __('admin.th_room') }}</th>
                    <th>{{ __('admin.th_status') }}</th>
                    <th class="text-end pe-4">{{ __('admin.th_total_price') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->reservations as $res)
                <tr>
                    <td class="ps-4"><span class="fw-bold text-dark">#{{ str_pad($res->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                    <td>{{ $res->created_at->format('M d, Y') }}</td>
                    <td><span class="text-success fw-medium">{{ \Carbon\Carbon::parse($res->check_in)->format('M d, Y') }}</span></td>
                    <td><span class="text-danger fw-medium">{{ \Carbon\Carbon::parse($res->check_out)->format('M d, Y') }}</span></td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-door-closed me-1"></i>{{ $res->room ? $res->room->room_number : 'N/A' }}
                        </span>
                    </td>
                    <td>
                        @php
                            $badgeClass = match($res->status) {
                                'confirmed'   => 'bg-success-subtle text-success border-success-subtle',
                                'checked_in'  => 'bg-primary-subtle text-primary border-primary-subtle',
                                'checked_out' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                'pending'     => 'bg-warning-subtle text-warning border-warning-subtle',
                                'cancelled'   => 'bg-danger-subtle text-danger border-danger-subtle',
                                default       => 'bg-light text-dark',
                            };
                            $badgeLabel = match($res->status) {
                                'confirmed'   => __('admin.confirmed'),
                                'checked_in'  => __('admin.checked_in'),
                                'checked_out' => __('admin.checked_out'),
                                'pending'     => __('admin.pending'),
                                'cancelled'   => __('admin.cancelled'),
                                default       => ucfirst($res->status),
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} border px-2 py-1 rounded-pill">{{ $badgeLabel }}</span>
                    </td>
                    <td class="text-end pe-4 fw-bold" style="color: #0F172A;">{{ number_format($res->total_price, 2) }} {{ __('common.currency') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">{{ __('admin.no_reservations_found') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
