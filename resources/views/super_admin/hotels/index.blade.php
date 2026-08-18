@extends('super_admin.layouts.app')

@section('title', 'Hotel Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Hotel Management</h4>
        <p class="text-muted mb-0">Manage hotel properties, locations, and statuses.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('super_admin.hotels.export') }}" class="btn btn-light px-4 py-2 rounded-3 shadow-sm border text-danger">
            <i class="bi bi-file-pdf me-2"></i> Export PDF
        </a>
        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addHotelModal">
            <i class="bi bi-plus-lg me-2"></i> Add Hotel
        </button>
    </div>
</div>

<!-- Errors Display -->
@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-octagon-fill fs-5 me-2"></i>
            <strong>Please fix the following errors:</strong>
        </div>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted fw-semibold border-bottom-0">Hotel Details</th>
                        <th class="py-3 text-muted fw-semibold border-bottom-0">City</th>
                        <th class="py-3 text-muted fw-semibold border-bottom-0">Address</th>
                        <th class="py-3 text-muted fw-semibold border-bottom-0">Status</th>
                        <th class="px-4 py-3 text-muted fw-semibold border-bottom-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($hotels as $hotel)
                        <tr>
                            <td class="px-4 py-3 border-light">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-4 me-3 d-flex align-items-center justify-content-center border shadow-sm" style="width: 45px; height: 45px;">
                                        <i class="bi bi-building text-primary fs-3 opacity-50"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $hotel->name }}</h6>
                                        <small class="text-muted">ID: #HTL-{{ $hotel->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 border-light">
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fw-semibold border border-info border-opacity-25">
                                    <i class="bi bi-geo-alt-fill me-1"></i> {{ $hotel->city->name }}
                                </span>
                            </td>
                            <td class="py-3 border-light">
                                <small class="text-muted fw-medium">{{ $hotel->address }}</small>
                            </td>
                            <td class="py-3 border-light">
                                @if($hotel->status == 'active')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold border border-success border-opacity-25">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-semibold border border-secondary border-opacity-25">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-light text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-light btn-sm text-info px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#viewHotelModal{{ $hotel->id }}" title="View Details">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-light btn-sm text-primary px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#editHotelModal{{ $hotel->id }}" title="Edit Hotel">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button class="btn btn-light btn-sm text-danger px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#deleteHotelModal{{ $hotel->id }}" title="Delete Hotel">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <div class="bg-light d-inline-block p-4 rounded-circle mb-3 shadow-sm">
                                        <i class="bi bi-building text-primary fs-1"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mt-3">No Hotels Found</h5>
                                    <p class="text-muted mb-4">You haven't added any hotels yet.</p>
                                    <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addHotelModal">
                                        <i class="bi bi-plus-lg me-2"></i> Add Your First Hotel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
@if(method_exists($hotels, 'hasPages') && $hotels->hasPages())
<div class="mt-4 d-flex justify-content-end">
    {{ $hotels->links('pagination::bootstrap-5') }}
</div>
@elseif(!method_exists($hotels, 'hasPages'))
<div class="mt-4 d-flex justify-content-end">
    {{ $hotels->links('pagination::bootstrap-5') }}
</div>
@endif

<!-- Add Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('super_admin.hotels.store') }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle text-primary me-2"></i>Add New Hotel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Hotel Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control bg-white border" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">City <span class="text-danger">*</span></label>
                        <select name="city_id" class="form-select form-select bg-white border" required>
                            <option value="">Select City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control form-control bg-white border" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Price per Night <span class="text-danger">*</span></label>
                        <!-- Added price_per_night input to fix the 1364 default value error -->
                        <input type="number" name="price_per_night" class="form-control form-control bg-white border" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Status</label>
                        <select name="status" class="form-select form-select bg-white border">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-white border px-4 rounded-3 fw-medium text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm fw-medium">Create Hotel</button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($hotels as $hotel)
<!-- View Modal -->
<div class="modal fade" id="viewHotelModal{{ $hotel->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-building text-primary me-2"></i>Hotel Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border" style="width: 70px; height: 70px;">
                        <i class="bi bi-building fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $hotel->name }}</h4>
                    <p class="text-muted small mb-0 fw-medium">ID: #HTL-{{ $hotel->id }}</p>
                </div>

                <div class="bg-light rounded-4 p-4 border shadow-sm">
                    <div class="row g-4">
                        <div class="col-6">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">Location</span>
                            <span class="fw-medium text-dark"><i class="bi bi-geo-alt text-primary me-1"></i>{{ $hotel->city->name }}</span>
                        </div>
                        <div class="col-6">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">Status</span>
                            @if($hotel->status == 'active')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold border border-success border-opacity-25">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-semibold border border-secondary border-opacity-25">Inactive</span>
                            @endif
                        </div>
                        <div class="col-12">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">Address</span>
                            <span class="fw-medium text-dark"><i class="bi bi-map text-primary me-1"></i>{{ $hotel->address }}</span>
                        </div>
                        <div class="col-12">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">Price per Night</span>
                            <span class="fs-4 fw-bold text-dark">{{ number_format($hotel->price_per_night, 2) }} <small class="text-muted fs-6">MAD</small></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light px-4 py-3">
                <button type="button" class="btn btn-primary px-4 rounded-3 shadow-sm fw-medium w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editHotelModal{{ $hotel->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('super_admin.hotels.update', $hotel) }}" method="POST" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Hotel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Hotel Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control bg-white border" value="{{ $hotel->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">City <span class="text-danger">*</span></label>
                        <select name="city_id" class="form-select form-select bg-white border" required>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ $hotel->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control form-control bg-white border" value="{{ $hotel->address }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Price per Night <span class="text-danger">*</span></label>
                        <!-- Added price_per_night input to fix the 1364 default value error -->
                        <input type="number" name="price_per_night" class="form-control form-control bg-white border" value="{{ $hotel->price_per_night }}" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Status</label>
                        <select name="status" class="form-select form-select bg-white border">
                            <option value="active" {{ $hotel->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $hotel->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-white border px-4 rounded-3 fw-medium text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm fw-medium">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteHotelModal{{ $hotel->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-4">
            <div class="mb-3 mt-2">
                <div class="d-inline-block bg-danger bg-opacity-10 p-3 rounded-circle text-danger mb-4">
                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2">Delete {{ $hotel->name }}?</h5>
                <p class="text-muted small mb-4">This action is permanent and cannot be undone.</p>
            </div>
            <form action="{{ route('super_admin.hotels.destroy', $hotel) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light px-4 w-50 rounded-3 fw-medium" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 w-50 rounded-3 shadow-sm fw-medium">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
