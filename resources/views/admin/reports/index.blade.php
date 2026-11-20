@extends('admin.layouts.app')

@section('title', __('admin.reports_title'))

@push('styles')
<style>
    :root {
        --reports-radius: 12px;
        --reports-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        --reports-shadow-hover: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07);
    }
    
    .reports-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    .page-header-bar {
        background: #ffffff;
        border-radius: var(--reports-radius);
        box-shadow: var(--reports-shadow);
        padding: 1.5rem;
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: var(--reports-radius);
        box-shadow: var(--reports-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--reports-shadow-hover);
        border-color: #cbd5e1;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: transparent;
        transition: background-color 0.3s ease;
    }

    .kpi-card.kpi-revenue::before { background-color: #10b981; }
    .kpi-card.kpi-total::before { background-color: #3b82f6; }
    .kpi-card.kpi-completed::before { background-color: #059669; }
    .kpi-card.kpi-pending::before { background-color: #d97706; }
    .kpi-card.kpi-cancelled::before { background-color: #dc2626; }
    .kpi-card.kpi-customers::before { background-color: #8b5cf6; }

    .kpi-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        transition: transform 0.3s ease;
    }

    .kpi-card:hover .kpi-icon-wrapper {
        transform: scale(1.1);
    }

    .kpi-value {
        font-size: 2.1rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 1.2;
    }

    .kpi-label {
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .kpi-description {
        font-size: 0.78rem;
    }

    .badge-premium {
        padding: 0.35em 0.75em;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-radius: 50rem;
    }

    /* Skeleton Loading Simulation Utility */
    .skeleton {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: loading-pulse 1.5s infinite;
        border-radius: 4px;
        display: inline-block;
    }

    @keyframes loading-pulse {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

@section('content')
<div class="reports-container">
    
    {{-- Header Bar --}}
    <div class="page-header-bar d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow text-primary"></i> {{ __('admin.reports_title') }}
            </h4>
            <p class="text-muted mb-0 small">{{ __('admin.reports_desc') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export') }}" class="btn btn-primary px-3 py-2 btn-sm rounded-3 shadow-sm">
                <i class="bi bi-download me-1.5"></i> {{ __('admin.export_pdf') }}
            </a>
        </div>
    </div>

    @if($totalReservations === 0)
        {{-- Premium Empty State --}}
        <div class="card border-0 shadow-sm py-5 text-center px-4" style="border-radius: var(--reports-radius);">
            <div class="py-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle p-4 mb-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-folder-x" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">{{ __('admin.no_report_data') }}</h5>
                <p class="text-muted mx-auto mb-4" style="max-width: 400px; font-size: 0.9rem;">
                    {{ __('admin.no_report_data_desc') }}
                </p>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-dark rounded-3 px-4 py-2 btn-sm fw-semibold shadow-sm">
                    <i class="bi bi-plus-lg me-1.5"></i> {{ __('admin.create_first_reservation') }}
                </a>
            </div>
        </div>
    @else
        {{-- KPI Cards Grid --}}
        <div class="row g-4">
            {{-- Total Revenue --}}
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card kpi-card kpi-revenue w-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="kpi-label text-muted d-block mb-1">{{ __('admin.total_revenue') }}</span>
                                <h3 class="kpi-value text-dark mb-0">{{ number_format($totalRevenue, 2) }} <span style="font-size: 1.2rem; font-weight: 600; color: #64748b;">MAD</span></h3>
                            </div>
                            <div class="kpi-icon-wrapper bg-success bg-opacity-10 text-success">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-premium bg-success bg-opacity-10 text-success">
                                <i class="bi bi-arrow-up-right me-0.5"></i> {{ __('admin.actualized_revenue') }}
                            </span>
                            <span class="kpi-description text-muted">{{ __('admin.confirmed_and_stayed') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Reservations --}}
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card kpi-card kpi-total w-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="kpi-label text-muted d-block mb-1">{{ __('admin.total_reservations') }}</span>
                                <h3 class="kpi-value text-dark mb-0">{{ $totalReservations }}</h3>
                            </div>
                            <div class="kpi-icon-wrapper bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-calendar-range-fill"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-premium bg-primary bg-opacity-10 text-primary">{{ __('admin.all_status') }}</span>
                            <span class="kpi-description text-muted">{{ __('admin.all_bookings_sum') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Completed Reservations --}}
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card kpi-card kpi-completed w-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="kpi-label text-muted d-block mb-1">{{ __('admin.completed_stays') }}</span>
                                <h3 class="kpi-value text-dark mb-0">{{ $completedReservations }}</h3>
                            </div>
                            <div class="kpi-icon-wrapper bg-emerald bg-opacity-10 text-emerald" style="background-color: rgba(16, 185, 129, 0.1); color: #059669;">
                                <i class="bi bi-calendar2-check-fill"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-premium text-emerald" style="background-color: rgba(16, 185, 129, 0.1); color: #059669;">{{ __('admin.checked_out') }}</span>
                            <span class="kpi-description text-muted">{{ __('admin.checked_out_guests') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Reservations --}}
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card kpi-card kpi-pending w-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="kpi-label text-muted d-block mb-1">{{ __('admin.pending_bookings') }}</span>
                                <h3 class="kpi-value text-dark mb-0">{{ $pendingReservations }}</h3>
                            </div>
                            <div class="kpi-icon-wrapper bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-premium bg-warning bg-opacity-10 text-warning">{{ __('admin.action_required') }}</span>
                            <span class="kpi-description text-muted">{{ __('admin.awaiting_confirmation') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cancelled Reservations --}}
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card kpi-card kpi-cancelled w-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="kpi-label text-muted d-block mb-1">{{ __('admin.cancelled_bookings') }}</span>
                                <h3 class="kpi-value text-dark mb-0">{{ $cancelledReservations }}</h3>
                            </div>
                            <div class="kpi-icon-wrapper bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-premium bg-danger bg-opacity-10 text-danger">{{ __('admin.cancelled') }}</span>
                            <span class="kpi-description text-muted">{{ __('admin.no_shows_cancellations') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Customers --}}
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card kpi-card kpi-customers w-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="kpi-label text-muted d-block mb-1">{{ __('admin.total_customers') }}</span>
                                <h3 class="kpi-value text-dark mb-0">{{ $totalCustomers }}</h3>
                            </div>
                            <div class="kpi-icon-wrapper bg-info bg-opacity-10 text-info" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge-premium text-info" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">{{ __('admin.unique_guests') }}</span>
                            <span class="kpi-description text-muted">{{ __('admin.scoped_to_hotel') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
