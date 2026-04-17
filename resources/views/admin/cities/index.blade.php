@extends('admin.layouts.app')

@section('title', 'City Management')

@section('content')
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Manage Cities</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.cities.export') }}" class="btn-export">
            <i class="bi bi-file-pdf"></i> Export PDF
        </a>
        <button class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addCityModal">
            <i class="bi bi-plus-lg me-2"></i>Add New City
        </button>
    </div>
</div>

<div class="row g-4">
    @foreach($cities as $city)
    <div class="col-md-3">
        <a href="{{ route('admin.cities.show', $city) }}" class="text-decoration-none text-dark">
            <div class="card h-100 overflow-hidden shadow-sm border-0 card-hover transition">
                <div class="position-relative">
                    <img src="{{ Str::startsWith($city->image, ['http://', 'https://']) ? $city->image : asset('storage/' . $city->image) }}" 
                         class="card-img-top" 
                         alt="{{ $city->name }}" 
                         style="height: 180px; object-fit: cover;"
                         onerror="this.src='https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800'">
                    <span class="badge bg-primary position-absolute top-0 end-0 m-3 shadow-sm">{{ $city->hotels_count }} Hotels</span>
                </div>
                <div class="card-body text-center bg-white">
                    <h6 class="fw-bold mb-3">{{ $city->name }}</h6>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-sm btn-light border px-3" data-bs-toggle="modal" data-bs-target="#editCityModal{{ $city->id }}" onclick="event.stopPropagation(); event.preventDefault();">Edit</button>
                        <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" onsubmit="return confirm('Delete this city?')" class="d-inline" onclick="event.stopPropagation();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-3"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editCityModal{{ $city->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.cities.update', $city) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit City</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label">City Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $city->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City Image (Upload File)</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">OR Image URL</label>
                            <input type="url" name="image_url" class="form-control" value="{{ Str::startsWith($city->image, ['http://', 'https://']) ? $city->image : '' }}" placeholder="https://source.unsplash.com/...">
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
</div>

<div class="mt-4">
    {{ $cities instanceof \Illuminate\Pagination\LengthAwarePaginator ? $cities->links() : '' }}
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCityModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.cities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label">City Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Casablanca" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City Image (Upload File)</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">OR Image URL</label>
                        <input type="url" name="image_url" class="form-control" placeholder="https://source.unsplash.com/...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create City</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
