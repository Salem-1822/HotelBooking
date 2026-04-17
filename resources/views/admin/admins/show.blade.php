@extends('admin.layouts.app')

@section('title', 'Admin Overview: ' . $admin->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.admins.index') }}" class="btn btn-light btn-sm border mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Admins
    </a>
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=f97316&color=fff" class="rounded-circle border border-4 border-white shadow-sm me-3" width="80">
            <div>
                <h3 class="fw-bold mb-0 text-dark">{{ $admin->name }}</h3>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-primary rounded-pill px-3">{{ ucfirst($admin->role ?? 'Admin') }}</span>
                    <span class="text-muted small"><i class="bi bi-envelope me-1"></i> {{ $admin->email }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light border" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                <i class="bi bi-pencil me-1"></i> Edit Account
            </button>
            <form action="{{ route('admin.admins.update', $admin) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="{{ $admin->status == 'blocked' ? 'active' : 'blocked' }}">
                <button type="submit" class="btn {{ $admin->status == 'blocked' ? 'btn-success' : 'btn-danger' }}">
                    <i class="bi {{ $admin->status == 'blocked' ? 'bi-unlock' : 'bi-lock' }} me-1"></i>
                    {{ $admin->status == 'blocked' ? 'Unblock Admin' : 'Block Admin' }}
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
                    <i class="bi bi-building fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Hotels</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['total_hotels'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3 text-success">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Reservations</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['total_reservations'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center text-success">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-check-circle fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Confirmed</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['confirmed_res'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center text-danger">
                <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-x-circle fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Cancelled</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['cancelled_res'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <ul class="nav nav-pills card-header-pills" id="adminTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="hotels-tab" data-bs-toggle="pill" data-bs-target="#hotels" type="button">Managed Hotels</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="reservations-tab" data-bs-toggle="pill" data-bs-target="#reservations" type="button">Recent Reservations</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="details-tab" data-bs-toggle="pill" data-bs-target="#details" type="button">Account Details</button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content" id="adminTabsContent">
            <!-- Hotels Tab -->
            <div class="tab-pane fade show active" id="hotels" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Hotel Name</th>
                                <th>City</th>
                                <th>Total Reservations</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admin->hotels as $hotel)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $hotel->name }}</td>
                                <td><span class="badge bg-light text-dark fw-normal border">{{ $hotel->city->name }}</span></td>
                                <td class="fw-bold text-primary">{{ $hotel->reservations->count() }}</td>
                                <td>
                                    <span class="badge {{ $hotel->status == 'active' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3">
                                        {{ ucfirst($hotel->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.hotels.show', $hotel) }}" class="btn btn-sm btn-light border">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No hotels managed by this admin yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reservations Tab -->
            <div class="tab-pane fade" id="reservations" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Guest</th>
                                <th>Hotel</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admin->hotels->flatMap->reservations->sortByDesc('created_at')->take(10) as $res)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $res->guest_name }}</td>
                                <td>{{ $res->hotel->name }}</td>
                                <td class="fw-bold text-success">${{ number_format($res->total_price, 2) }}</td>
                                <td>
                                    <span class="badge {{ $res->status == 'confirmed' ? 'bg-success' : ($res->status == 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill px-3">
                                        {{ ucfirst($res->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end text-muted small">{{ $res->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No reservation activity recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Account Details Tab -->
            <div class="tab-pane fade p-4" id="details" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">Login Activity</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted ps-0">Joined Date</td>
                                <td class="fw-bold">{{ $admin->created_at->format('F d, Y @ H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Account Role</td>
                                <td><span class="badge bg-light text-dark fw-bold border">{{ $admin->role ?? 'Standard Admin' }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Account Status</td>
                                <td>
                                    <span class="badge {{ $admin->status == 'blocked' ? 'bg-danger text-white' : 'bg-primary' }} rounded-pill">
                                        {{ ucfirst($admin->status ?? 'Active') }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">Managed Regions</h6>
                        @if($admin->city)
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <div class="bg-primary text-white p-2 rounded-2 me-3">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $admin->city->name }}</h6>
                                <small class="text-muted">Primary assigned city</small>
                            </div>
                        </div>
                        @else
                        <p class="text-muted small">No specific city assigned to this administrator.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Admin Modal (Reuse from index) -->
<div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.admins.update', $admin) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Admin Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
