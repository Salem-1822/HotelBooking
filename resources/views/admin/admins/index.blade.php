@extends('admin.layouts.app')

@section('title', 'Manage Admins')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">System Admins</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.admins.export') }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </a>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="bi bi-person-plus-fill me-1"></i> Create Admin
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Full Name</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=0D6EFD&color=fff" class="rounded-circle me-3 border" width="35">
                            <span class="fw-bold">{{ $admin->name }}</span>
                        </div>
                    </td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->created_at->format('d M Y') }}</td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('Delete this admin?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.admins.update', $admin) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content text-start">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Admin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password (Optional)</label>
                                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $admins->links() }}
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.admins.store') }}" method="POST">
            @csrf
            <div class="modal-content text-start">
                <div class="modal-header">
                    <h5 class="modal-title">Create Admin Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initial Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Admin</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
