@extends('admin.layouts.app')

@section('title', 'Admins Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">System Administrators</h4>
        <p class="text-muted small mb-0">Manage and monitor platform administrators and their performance.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.admins.export') }}" class="btn-export">
            <i class="bi bi-file-pdf"></i> Export Data
        </a>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary btn-sm px-4 d-flex align-items-center">
            <i class="bi bi-person-plus-fill me-2"></i>Create New Admin
        </a>
    </div>
</div>

<div class="row g-4">
    @forelse($admins as $admin)
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm admin-card hover-lift" onclick="window.location='{{ route('admin.admins.show', $admin->id) }}'" style="cursor: pointer;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background={{ $admin->role === 'super_admin' ? 'ef4444' : '3b82f6' }}&color=fff" class="rounded-circle border border-3 border-white shadow-sm" width="60">
                            <span class="position-absolute bottom-0 end-0 p-1 {{ $admin->status == 'blocked' ? 'bg-danger' : 'bg-success' }} border border-2 border-white rounded-circle"></span>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-0">{{ $admin->name }}</h6>
                            <small class="text-muted">{{ $admin->email }}</small>
                        </div>
                    </div>
                    <span class="badge {{ $admin->role === 'super_admin' ? 'bg-danger' : 'bg-primary' }} rounded-pill px-3 text-capitalize">
                        {{ str_replace('_', ' ', $admin->role) }}
                    </span>
                </div>

                <div class="row g-2 mt-3">
                    <div class="col-6">
                        <div class="bg-light p-3 rounded-3 text-center h-100">
                            <h5 class="fw-bold mb-0 text-dark">{{ $admin->city ? $admin->city->hotels->count() : 0 }}</h5>
                            <small class="text-muted opacity-75 text-uppercase fw-bold" style="font-size: 0.6rem;">City Hotels</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light p-3 rounded-3 text-center h-100">
                            <h5 class="fw-bold mb-0 text-dark text-truncate" style="font-size: 0.8rem;">{{ $admin->city->name ?? 'N/A' }}</h5>
                            <small class="text-muted opacity-75 text-uppercase fw-bold" style="font-size: 0.6rem;">Assigned City</small>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <small class="text-muted text-uppercase fw-bold mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">Managing Hotels:</small>
                    @if($admin->city && $admin->city->hotels->count() > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($admin->city->hotels->take(3) as $hotel)
                                <span class="badge bg-white text-dark border fw-normal">{{ $hotel->name }}</span>
                            @endforeach
                            @if($admin->city->hotels->count() > 3)
                                <span class="badge bg-light text-muted fw-normal">+{{ $admin->city->hotels->count() - 3 }} more</span>
                            @endif
                        </div>
                    @else
                        <span class="text-muted small italic">No hotels in this city</span>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        <i class="bi bi-calendar-event me-1"></i> Added {{ $admin->created_at->format('M Y') }}
                    </div>
                    <div class="d-flex gap-2" onclick="event.stopPropagation()">
                        <button class="btn btn-sm btn-light border-0" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                            <i class="bi bi-pencil-fill text-muted"></i>
                        </button>
                        @if($admin->id !== Auth::guard('admin')->id())
                        <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('Delete this admin?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border-0">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal (Copied from previous index) -->
    <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" onclick="event.stopPropagation()">
        <div class="modal-dialog">
            <form action="{{ route('admin.admins.update', $admin) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Edit Admin</h5>
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
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Account Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $admin->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="blocked" {{ $admin->status == 'blocked' ? 'selected' : '' }}>Blocked</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-people fs-1 text-muted opacity-25 d-block mb-3"></i>
        <h5 class="text-muted">No administrators found</h5>
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $admins->links() }}
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.admins.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Create Admin Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="example@hotelia.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Temporary Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Admin</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .admin-card {
        transition: all 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endsection
