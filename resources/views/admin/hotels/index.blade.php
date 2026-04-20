@extends('admin.layouts.app')

@section('title', 'Hotel Management')

@section('content')
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">All Hotels</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.hotels.export') }}" class="btn-export">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHotelModal">
                <i class="bi bi-plus-lg me-1"></i> Add Hotel
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">Hotel Name</th>
                    <th>City</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hotels as $hotel)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px;">
                                <i class="bi bi-building text-primary opacity-50"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold h6">{{ $hotel->name }}</h6>
                                <small class="text-muted small">ID: #HTL-{{ $hotel->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark fw-normal border">{{ $hotel->city->name }}</span></td>
                    <td><small class="text-muted">{{ $hotel->address }}</small></td>
                    <td>
                        <span class="badge {{ $hotel->status == 'active' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3">
                            {{ ucfirst($hotel->status) }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.hotels.show', $hotel) }}" class="btn btn-sm btn-light border text-primary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editHotelModal{{ $hotel->id }}" title="Edit Hotel">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" onsubmit="return confirm('Delete this hotel?')">
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
                <div class="modal fade" id="editHotelModal{{ $hotel->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.hotels.update', $hotel) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content text-start">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Hotel</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Hotel Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ $hotel->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <select name="city_id" class="form-select" required>
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}" {{ $hotel->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="address" class="form-control" value="{{ $hotel->address }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price per Night</label>
                                        <!-- Added price_per_night input to fix the 1364 default value error -->
                                        <input type="number" name="price_per_night" class="form-control" value="{{ $hotel->price_per_night }}" step="0.01" min="0" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" {{ $hotel->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $hotel->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
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
        {{ $hotels->links() }}
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.hotels.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Hotel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Hotel Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <select name="city_id" class="form-select" required>
                            <option value="">Select City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price per Night</label>
                        <!-- Added price_per_night input to fix the 1364 default value error -->
                        <input type="number" name="price_per_night" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Hotel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
