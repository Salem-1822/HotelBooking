@extends('admin.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
    }
    
    .dashboard-header::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 40%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05));
        pointer-events: none;
    }
    
    .hotel-badge {
        background: rgba(212, 175, 55, 0.2);
        color: #D4AF37;
        border: 1px solid rgba(212, 175, 55, 0.3);
        padding: 0.35rem 0.85rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .stat-card-modern {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .stat-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -8px rgba(15, 23, 42, 0.1);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .stat-primary { background: #F1F5F9; color: #0F172A; }
    .stat-success { background: #F0FDF4; color: #22C55E; }
    .stat-warning { background: #FEF3C7; color: #F59E0B; }
    .stat-danger  { background: #FEF2F2; color: #EF4444; }
    .stat-info    { background: #EFF6FF; color: #3B82F6; }
    .stat-accent  { background: #FEF9C3; color: #D4AF37; }
    
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1E293B;
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1E293B;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.6rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #E2E8F0;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -2rem;
        top: 0.25rem;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #3B82F6;
        z-index: 1;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-time {
        font-size: 0.75rem;
        color: #64748B;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .timeline-content {
        font-size: 0.875rem;
        color: #1E293B;
        font-weight: 500;
    }
    
    .quick-action-btn {
        background: #fff;
        border: 1px solid #E2E8F0;
        padding: 1rem;
        border-radius: 0.875rem;
        text-align: center;
        text-decoration: none;
        color: #1E293B;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    
    .quick-action-btn:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #0F172A;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    
    .quick-action-icon {
        font-size: 1.5rem;
        color: #D4AF37;
    }
</style>
@endpush

@section('content')

<!-- Header -->
<div class="dashboard-header">
    <div class="row align-items-center relative z-1">
        <div class="col-lg-8">
            <div class="hotel-badge mb-3">
                <i class="bi bi-geo-alt-fill"></i> {{ $hotel->city->name ?? 'Morocco' }}
            </div>
            <h1 class="fw-bold mb-2" style="font-size: 2.25rem; letter-spacing: -0.02em;">{{ $hotel->name }}</h1>
            <p class="mb-0 text-light" style="opacity: 0.8; font-size: 0.95rem; max-width: 600px;">
                <i class="bi bi-map me-1"></i> {{ $hotel->address }}
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
            <div class="d-inline-flex flex-column align-items-lg-end">
                <span class="text-uppercase fw-bold" style="font-size: 0.75rem; color: #94A3B8; letter-spacing: 0.1em;">Occupancy Rate</span>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <h2 class="mb-0 fw-bold" style="color: #D4AF37; font-size: 2.5rem;">{{ $occupancyRate }}%</h2>
                    <div class="ms-2" style="width: 80px; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                        <div style="width: {{ $occupancyRate }}%; height: 100%; background: #D4AF37; border-radius: 3px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Primary Stats -->
<div class="row g-4 mb-4">
    <!-- Total Rooms -->
    <div class="col-6 col-md-3 col-xl-2">
        <div class="stat-card-modern">
            <div class="stat-icon stat-primary"><i class="bi bi-door-closed-fill"></i></div>
            <div class="mt-auto">
                <div class="stat-value">{{ $totalRooms }}</div>
                <div class="stat-label">Total Rooms</div>
            </div>
        </div>
    </div>
    
    <!-- Available -->
    <div class="col-6 col-md-3 col-xl-2">
        <div class="stat-card-modern">
            <div class="stat-icon stat-success"><i class="bi bi-check-circle-fill"></i></div>
            <div class="mt-auto">
                <div class="stat-value">{{ $availableRooms }}</div>
                <div class="stat-label">Available</div>
            </div>
        </div>
    </div>
    
    <!-- Occupied -->
    <div class="col-6 col-md-3 col-xl-2">
        <div class="stat-card-modern">
            <div class="stat-icon stat-info"><i class="bi bi-person-badge-fill"></i></div>
            <div class="mt-auto">
                <div class="stat-value">{{ $occupiedRooms }}</div>
                <div class="stat-label">Occupied</div>
            </div>
        </div>
    </div>
    
    <!-- Maintenance -->
    <div class="col-6 col-md-3 col-xl-2">
        <div class="stat-card-modern">
            <div class="stat-icon stat-warning"><i class="bi bi-tools"></i></div>
            <div class="mt-auto">
                <div class="stat-value">{{ $maintenanceRooms }}</div>
                <div class="stat-label">Maintenance</div>
            </div>
        </div>
    </div>
    
    <!-- Check-ins -->
    <div class="col-6 col-md-3 col-xl-2">
        <div class="stat-card-modern">
            <div class="stat-icon stat-accent"><i class="bi bi-box-arrow-in-right"></i></div>
            <div class="mt-auto">
                <div class="stat-value">{{ $todayCheckIns }}</div>
                <div class="stat-label">Check-ins</div>
            </div>
        </div>
    </div>
    
    <!-- Check-outs -->
    <div class="col-6 col-md-3 col-xl-2">
        <div class="stat-card-modern">
            <div class="stat-icon stat-danger"><i class="bi bi-box-arrow-right"></i></div>
            <div class="mt-auto">
                <div class="stat-value">{{ $todayCheckOuts }}</div>
                <div class="stat-label">Check-outs</div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats & Charts -->
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="section-title mb-0"><i class="bi bi-graph-up text-primary"></i> Performance Overview</h5>
                    <select class="form-select form-select-sm w-auto">
                        <option>Last 6 Months</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-4 p-md-5 d-flex flex-column">
                <h5 class="section-title"><i class="bi bi-pie-chart-fill text-primary"></i> Reservation Status</h5>
                
                <div class="flex-grow-1 d-flex align-items-center justify-content-center my-4" style="position: relative; height: 220px;">
                    <canvas id="statusChart"></canvas>
                </div>
                
                <div class="d-flex justify-content-between text-center mt-3 pt-3 border-top">
                    <div>
                        <div class="fs-5 fw-bold text-success">{{ $confirmedReservations }}</div>
                        <div class="text-muted" style="font-size:0.75rem;font-weight:600;text-transform:uppercase;">Confirmed</div>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold text-warning">{{ $pendingReservations }}</div>
                        <div class="text-muted" style="font-size:0.75rem;font-weight:600;text-transform:uppercase;">Pending</div>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold text-danger">{{ $cancelledReservations }}</div>
                        <div class="text-muted" style="font-size:0.75rem;font-weight:600;text-transform:uppercase;">Cancelled</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables & Activity -->
<div class="row g-4 mb-5">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="section-title mb-0"><i class="bi bi-calendar-check-fill text-primary"></i> Recent Reservations</h5>
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-light border fw-semibold" style="font-size: 0.8rem;">View All</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Room</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestReservations as $res)
                            <tr>
                                <td>
                                    <span class="fw-bold" style="color: #0F172A;">#{{ str_pad($res->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $res->user ? $res->user->name : $res->guest_name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $res->user ? $res->user->email : $res->guest_phone }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-door-closed me-1"></i>{{ $res->room ? $res->room->room_number : 'N/A' }}</span>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem;"><span class="text-success fw-medium">In:</span> {{ \Carbon\Carbon::parse($res->check_in)->format('M d') }}</div>
                                    <div style="font-size: 0.85rem;"><span class="text-danger fw-medium">Out:</span> {{ \Carbon\Carbon::parse($res->check_out)->format('M d') }}</div>
                                </td>
                                <td class="fw-bold" style="color: #0F172A;">{{ number_format($res->total_price, 2) }} MAD</td>
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
                                            'confirmed'   => 'Confirmed',
                                            'checked_in'  => 'Checked In',
                                            'checked_out' => 'Checked Out',
                                            'pending'     => 'Pending',
                                            'cancelled'   => 'Cancelled',
                                            default       => ucfirst($res->status),
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} border px-2 py-1 rounded-pill">{{ $badgeLabel }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No recent reservations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="section-title"><i class="bi bi-lightning-fill text-warning"></i> Quick Actions</h5>
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('admin.reservations.index') }}" class="quick-action-btn">
                            <i class="bi bi-calendar-plus quick-action-icon"></i>
                            New Booking
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-action-btn">
                            <i class="bi bi-door-open quick-action-icon text-primary"></i>
                            Room Status
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-action-btn">
                            <i class="bi bi-people quick-action-icon text-success"></i>
                            Guests
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-action-btn">
                            <i class="bi bi-file-earmark-bar-graph quick-action-icon text-info"></i>
                            Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Customers -->
        <div class="card">
            <div class="card-body p-4">
                <h5 class="section-title"><i class="bi bi-star-fill text-accent"></i> Top Customers</h5>
                <div class="d-flex flex-column gap-3 mt-3">
                    @forelse($latestCustomers as $customer)
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=E2E8F0&color=0F172A&size=48" class="rounded-circle" width="40" height="40" alt="">
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-truncate" style="font-size: 0.9rem; color: #0F172A;">{{ $customer->name }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ $customer->email }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary-subtle text-primary border rounded-pill">{{ $customer->reservations_count }} Bookings</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted" style="font-size: 0.85rem;">No customer data available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colors based on requested palette
    const colorPrimary = '#0F172A';
    const colorAccent = '#D4AF37';
    const colorSuccess = '#22C55E';
    const colorWarning = '#F59E0B';
    const colorDanger = '#EF4444';
    const colorBorder = '#E2E8F0';

    // Chart Data from backend
    const chartLabels = {!! json_encode(array_reverse($chartLabels)) !!};
    const chartReservations = {!! json_encode(array_reverse($monthlyReservations)) !!};
    
    // Performance Chart (Bar/Line Combo)
    const ctxPerf = document.getElementById('performanceChart');
    if (ctxPerf) {
        new Chart(ctxPerf, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Reservations',
                        data: chartReservations,
                        backgroundColor: colorPrimary,
                        borderRadius: 6,
                        barThickness: 24
                    },
                    {
                        label: 'Trend',
                        data: chartReservations,
                        type: 'line',
                        borderColor: colorAccent,
                        borderWidth: 3,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: colorAccent,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: { family: "'Inter', sans-serif", size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        padding: 12,
                        titleFont: { family: "'Inter', sans-serif", size: 13 },
                        bodyFont: { family: "'Inter', sans-serif", size: 13 },
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: colorBorder, drawBorder: false },
                        ticks: { font: { family: "'Inter', sans-serif" }, color: '#64748B' }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { family: "'Inter', sans-serif", weight: '500' }, color: '#64748B' }
                    }
                }
            }
        });
    }

    // Status Chart (Doughnut)
    const ctxStatus = document.getElementById('statusChart');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Confirmed', 'Pending', 'Cancelled'],
                datasets: [{
                    data: [{{ $confirmedReservations }}, {{ $pendingReservations }}, {{ $cancelledReservations }}],
                    backgroundColor: [colorSuccess, colorWarning, colorDanger],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        padding: 12,
                        bodyFont: { family: "'Inter', sans-serif", size: 13 },
                        cornerRadius: 8
                    }
                }
            }
        });
    }
});
</script>
@endpush
