@extends('super_admin.layouts.app')

@section('title', 'Cities Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Cities Management</h4>
        <p class="text-muted mb-0">Manage regions, upload images, and track active hotels.</p>
    </div>
    <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCityModal">
        <i class="bi bi-plus-lg me-2"></i> Add City
    </button>
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

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card p-4 border-0 shadow-sm rounded-4 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-4">
                    <i class="bi bi-geo-alt-fill text-primary fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.8rem; letter-spacing: 0.05em;">Total Cities</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalCities }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 border-0 shadow-sm rounded-4 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-4">
                    <i class="bi bi-building-fill text-success fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.8rem; letter-spacing: 0.05em;">Total Hotels</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalHotels }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.cities.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-8 position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted"></i>
                <input type="text" name="search" class="form-control bg-light border-0 ps-5 py-2 rounded-3" placeholder="Search cities by name..." value="{{ request('search') }}" oninput="if(this.value.length >= 3 || this.value.length === 0) { clearTimeout(window.searchTimeout); window.searchTimeout = setTimeout(() => this.form.submit(), 500); }">
            </div>
            <div class="col-md-4 text-end">
                @if(request('search'))
                    <a href="{{ route('super_admin.cities.index') }}" class="btn btn-light px-4 py-2 rounded-3 text-muted">
                        <i class="bi bi-x-lg me-1"></i> Clear Search
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted fw-semibold border-bottom-0">City Details</th>
                        <th class="py-3 text-muted fw-semibold border-bottom-0">Hotels Active</th>
                        <th class="py-3 text-muted fw-semibold border-bottom-0">Date Added</th>
                        <th class="px-4 py-3 text-muted fw-semibold border-bottom-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($cities as $city)
                        <tr>
                            <td class="px-4 py-3 border-light">
                                <div class="d-flex align-items-center">
                                    @php
                                        $img = Str::startsWith($city->image, ['http://', 'https://']) ? $city->image : ($city->image ? asset('storage/' . $city->image) : 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=100&h=100&fit=crop');
                                    @endphp
                                    <img src="{{ $img }}" alt="{{ $city->name }}" class="rounded-4 shadow-sm me-3" style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #fff;">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $city->name }}</h6>
                                        <small class="text-muted">Slug: {{ $city->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 border-light">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold border border-primary border-opacity-25">
                                    <i class="bi bi-building me-1"></i> {{ $city->hotels_count ?? 0 }} Hotels
                                </span>
                            </td>
                            <td class="py-3 border-light">
                                <small class="text-muted fw-medium"><i class="bi bi-calendar-event me-1"></i> {{ $city->created_at->format('M d, Y') }}</small>
                            </td>
                            <td class="px-4 py-3 border-light text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-light btn-sm text-info px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#viewCityModal{{ $city->id }}" title="View Details">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-light btn-sm text-primary px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#editCityModal{{ $city->id }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button class="btn btn-light btn-sm text-danger px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#deleteCityModal{{ $city->id }}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-5">
                                    <div class="bg-light d-inline-block p-4 rounded-circle mb-3 shadow-sm">
                                        <i class="bi bi-map text-primary fs-1"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mt-3">No Cities Found</h5>
                                    <p class="text-muted mb-4">You haven't added any cities yet, or your search didn't match anything.</p>
                                    @if(request('search'))
                                        <a href="{{ route('super_admin.cities.index') }}" class="btn btn-light border px-4 py-2 rounded-3">Clear Search</a>
                                    @else
                                        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCityModal">
                                            <i class="bi bi-plus-lg me-2"></i> Add Your First City
                                        </button>
                                    @endif
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
@if($cities->hasPages())
<div class="mt-4 d-flex justify-content-end">
    {{ $cities->links('pagination::bootstrap-5') }}
</div>
@endif

<!-- Add Modal -->
<div class="modal fade" id="addCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('super_admin.cities.store') }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle text-primary me-2"></i>Add New City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">City Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-white border" placeholder="e.g. Paris" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Upload Image</label>
                        <input type="file" name="image_file" class="form-control bg-white border" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>Optional. Max 2MB (JPG, PNG, WEBP).</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-white border px-4 rounded-3 fw-medium text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm fw-medium">Create City</button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($cities as $city)
<!-- View Modal -->
<div class="modal fade" id="viewCityModal{{ $city->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-geo-alt-fill text-primary me-2"></i>City Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @php
                    $viewImg = Str::startsWith($city->image, ['http://', 'https://']) ? $city->image : ($city->image ? asset('storage/' . $city->image) : 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=300&h=200&fit=crop');
                @endphp
                <div class="text-center mb-4">
                    <img src="{{ $viewImg }}" alt="{{ $city->name }}" class="rounded-4 shadow-sm mb-3" style="width: 100%; max-height: 160px; object-fit: cover;">
                    <h4 class="fw-bold mb-1">{{ $city->name }}</h4>
                    <p class="text-muted small mb-0 fw-medium">Slug: {{ $city->slug }}</p>
                </div>
                <div class="bg-light rounded-4 p-4 border shadow-sm">
                    <div class="row g-4">
                        <div class="col-6">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">Hotels Active</span>
                            <span class="fw-medium text-dark"><i class="bi bi-building text-primary me-1"></i>{{ $city->hotels_count ?? 0 }} Hotels</span>
                        </div>
                        <div class="col-6">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">Date Added</span>
                            <span class="fw-medium text-dark"><i class="bi bi-calendar-event text-primary me-1"></i>{{ $city->created_at->format('M d, Y') }}</span>
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
<div class="modal fade" id="editCityModal{{ $city->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('super_admin.cities.update', $city) }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Edit City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">City Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-white border" value="{{ $city->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">Upload New Image</label>
                        <input type="file" name="image_file" class="form-control bg-white border" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>Optional. Leave empty to keep the current image. Max 2MB (JPG, PNG, WEBP).</div>
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
<div class="modal fade" id="deleteCityModal{{ $city->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-4">
            <div class="mb-3 mt-2">
                <div class="d-inline-block bg-danger bg-opacity-10 p-3 rounded-circle text-danger mb-4">
                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2">Delete {{ $city->name }}?</h5>
                <p class="text-muted small mb-4">This action is permanent and cannot be undone.</p>
            </div>
            <form action="{{ route('super_admin.cities.destroy', $city) }}" method="POST">
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
