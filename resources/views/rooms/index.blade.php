@extends($layout)

@section('title', 'Manage Rooms')

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
    .summary-card {
        border-radius: 1rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
    }
    .summary-card .card-body {
        min-height: 120px;
    }
    .room-table tbody tr:hover {
        background: rgba(15, 23, 42, 0.02);
    }
    .room-table td,
    .room-table th {
        vertical-align: middle;
    }
    .room-image-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 0.95rem;
        border: 1px solid #E2E8F0;
        background: #F8FAFC;
    }
    .badge-animate {
        animation: badgePop 0.45s cubic-bezier(0.34,1.56,0.64,1) both;
    }
    @keyframes badgePop {
        0% { opacity: 0; transform: scale(0.85); }
        60% { transform: scale(1.08); }
        100% { opacity: 1; transform: scale(1); }
    }
    .table-animate {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.35s ease, transform 0.35s ease;
    }
    .table-animate.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    .modal {
        z-index: 9999 !important;
    }
    .modal-backdrop {
        z-index: 9998 !important;
    }
    .modal-content {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    .modal .form-label {
        font-weight: 600;
    }
    .modal .form-control:focus,
    .modal .form-select:focus {
        box-shadow: 0 0 0 0.15rem rgba(15, 23, 42, 0.12);
    }
    .toast-shell {
        position: fixed;
        top: 1.25rem;
        right: 1.25rem;
        z-index: 1200;
    }
    .toast-item {
        min-width: 280px;
        padding: 1rem 1.2rem;
        border-radius: 1rem;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.16);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        animation: toastIn 0.4s ease both;
    }
    .toast-item.success { background: #0F172A; border-left: 4px solid #22C55E; }
    .toast-item.danger { background: #0F172A; border-left: 4px solid #EF4444; }
    @keyframes toastIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>
@endpush

@section('content')
<div class="page-title-bar mb-4">
    <div class="title-group">
        <h4 class="fw-bold mb-1">Manage Rooms</h4>
        <p class="mb-0 text-muted">Create and update room inventory for your hotel without page reload.</p>
    </div>
    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addRoomModal">
        <i class="bi bi-plus-lg me-2"></i> Add Room
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Total Rooms</p>
                <h3 class="mb-1 fw-bold counter" data-count="{{ $totalRooms }}">0</h3>
                <span class="badge bg-light text-dark">Hotel scope</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Available</p>
                <h3 class="mb-1 fw-bold counter text-success" data-count="{{ $availableRooms }}">0</h3>
                <span class="badge bg-success bg-opacity-10 text-success">Ready</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Occupied</p>
                <h3 class="mb-1 fw-bold counter text-danger" data-count="{{ $occupiedRooms }}">0</h3>
                <span class="badge bg-danger bg-opacity-10 text-danger">In Use</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">Maintenance</p>
                <h3 class="mb-1 fw-bold counter text-warning" data-count="{{ $maintenanceRooms }}">0</h3>
                <span class="badge bg-warning bg-opacity-10 text-warning">Service</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row gx-3 gy-3 align-items-center">
            <div class="col-lg-4">
                <div class="input-group shadow-sm rounded overflow-hidden border border-1 border-light">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    <input id="roomSearchInput" type="search" class="form-control border-0" placeholder="Search room number, name or type">
                </div>
            </div>
            <div class="col-sm-4 col-lg-3">
                <select id="filterStatus" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statusOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4 col-lg-3">
                <select id="filterType" class="form-select">
                    <option value="">All Types</option>
                    @foreach($roomTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 col-lg-2 text-end ms-auto">
                <button id="clearFilters" class="btn btn-light border">Clear Filters</button>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 room-table table-animate" id="roomsTable">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th>Image</th>
                    <th>Room Number</th>
                    <th>Room Name</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Updated At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="roomsTableBody">
                @forelse($rooms as $room)
                <tr data-id="{{ $room->id }}" data-status="{{ $room->status }}" data-type="{{ $room->type }}" data-floor="{{ $room->floor }}">
                    <td class="pe-0">
                        @if($room->main_image && Storage::disk('public')->exists($room->main_image))
                        <img src="{{ asset('storage/' . $room->main_image) }}" class="room-image-thumb" alt="{{ $room->name }}">
                        @else
                        <div class="room-image-thumb d-flex align-items-center justify-content-center bg-light text-muted">
                            <i class="bi bi-door-closed fs-5"></i>
                        </div>
                        @endif
                    </td>
                    <td>#{{ $room->room_number }}</td>
                    <td>
                        <div class="fw-semibold">{{ $room->name }}</div>
                        @if(isset($room->hotel) && $routePrefix === 'super_admin.')
                        <small class="text-muted">{{ $room->hotel->name }}</small>
                        @endif
                    </td>
                    <td>{{ $room->type }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>{{ number_format($room->price_per_night, 2) }} MAD</td>
                    <td>
                        @php
                            $badgeClass = match($room->status) {
                                'available'   => 'bg-success bg-opacity-15 text-success',
                                'occupied'    => 'bg-danger bg-opacity-15 text-danger',
                                'maintenance' => 'bg-warning bg-opacity-15 text-warning',
                                default       => 'bg-secondary bg-opacity-15 text-secondary',
                            };
                        @endphp
                        <span class="badge rounded-pill px-3 badge-animate {{ $badgeClass }}">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                    <td>{{ $room->updated_at->format('Y-m-d H:i') }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-light border" data-action="view" data-id="{{ $room->id }}" title="View Room">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border" data-action="edit" data-id="{{ $room->id }}" title="Edit Room">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border text-danger" data-action="delete" data-id="{{ $room->id }}" title="Delete Room">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-door-open fs-1 mb-3"></i>
                            <h5 class="fw-bold">No rooms found</h5>
                            <p class="mb-0">Add your first room using the button above to manage availability and pricing.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createRoomForm" method="POST" action="{{ route($routePrefix . 'rooms.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Room Number</label>
                            <input name="room_number" type="text" class="form-control" placeholder="e.g. 101" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Name</label>
                            <input name="name" type="text" class="form-control" placeholder="e.g. Deluxe Ocean View" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                @foreach($roomTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price Per Night (DH)</label>
                            <input name="price_per_night" type="number" min="0" step="0.01" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacity</label>
                            <input name="capacity" type="number" min="1" class="form-control" placeholder="e.g. 2" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-muted fw-normal">(Optional)</span></label>
                            <textarea name="description" rows="3" class="form-control"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Room Image <span class="text-muted fw-normal">(Optional)</span></label>
                            <input name="main_image" type="file" accept="image/*" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoomForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Room Number</label>
                            <input name="room_number" type="text" class="form-control" placeholder="e.g. 101" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Name</label>
                            <input name="name" type="text" class="form-control" placeholder="e.g. Deluxe Ocean View" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                @foreach($roomTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price Per Night (DH)</label>
                            <input name="price_per_night" type="number" min="0" step="0.01" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacity</label>
                            <input name="capacity" type="number" min="1" class="form-control" placeholder="e.g. 2" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-muted fw-normal">(Optional)</span></label>
                            <textarea name="description" rows="3" class="form-control"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Room Image <span class="text-muted fw-normal">(Optional)</span></label>
                            <input name="main_image" type="file" accept="image/*" class="form-control">
                            <div class="form-text">Upload only when replacing the current main image.</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Room Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <img id="viewMainImage" src="" alt="Room Image" class="img-fluid rounded-4 mb-4" style="max-height:320px; width:auto;">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="mb-4">
                            <h4 id="viewRoomName" class="fw-bold"></h4>
                            <p id="viewRoomSubtitle" class="text-muted mb-1"></p>
                            <span id="viewStatusBadge" class="badge rounded-pill px-3"></span>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="text-muted small">Room Number</div>
                                <div id="viewRoomNumber" class="fw-semibold"></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Type</div>
                                <div id="viewRoomType" class="fw-semibold"></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Capacity</div>
                                <div id="viewCapacity" class="fw-semibold"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Price Per Night</div>
                                <div id="viewPrice" class="fw-semibold"></div>
                            </div>
                            @if($hotels)
                            <div class="col-md-6">
                                <div class="text-muted small">Hotel</div>
                                <div id="viewHotelName" class="fw-semibold"></div>
                            </div>
                            @endif
                        </div>
                        <div class="mt-4">
                            <div class="text-muted small mb-2">Description</div>
                            <p id="viewDescription" class="mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete <strong id="deleteRoomName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmDeleteRoom" type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-shell" id="toastShell"></div>
@endsection

@push('scripts')
<script>
    window.rooms = @json($roomsForJs);
    window.roomsBaseUrl = "{{ $routePrefix === 'admin.' ? url('/admin/rooms') : url('/super-admin/rooms') }}";
    window.isSuperAdminArea = {{ $routePrefix === 'super_admin.' ? 'true' : 'false' }};
    window.assetBasePath = "{{ asset('storage') }}";

    const state = {
        search: '',
        status: '',
        type: '',
        floor: '',
        selectedRoomId: null,
    };

    document.addEventListener('DOMContentLoaded', function () {
        animateCounters();
        renderRooms();
        initControls();
        setTimeout(() => document.getElementById('roomsTable').classList.add('is-visible'), 100);
    });

    function initControls() {
        document.getElementById('roomSearchInput').addEventListener('input', function () {
            state.search = this.value.trim().toLowerCase();
            renderRooms();
        });
        document.getElementById('filterStatus').addEventListener('change', function () {
            state.status = this.value;
            renderRooms();
        });
        document.getElementById('filterType').addEventListener('change', function () {
            state.type = this.value;
            renderRooms();
        });
        document.getElementById('clearFilters').addEventListener('click', function () {
            state.search = '';
            state.status = '';
            state.type = '';
            state.floor = '';
            document.getElementById('roomSearchInput').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterType').value = '';
            renderRooms();
        });

        document.getElementById('createRoomForm').addEventListener('submit', function (event) {
            event.preventDefault();
            submitRoomForm(this, window.roomsBaseUrl, 'POST', '#addRoomModal');
        });

        document.getElementById('editRoomForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const roomId = state.selectedRoomId;
            if (! roomId) return;
            submitRoomForm(this, `${window.roomsBaseUrl}/${roomId}`, 'PUT', '#editRoomModal');
        });
    }

    function renderRooms() {
        const tbody = document.getElementById('roomsTableBody');
        const filtered = window.rooms.filter(room => {
            if (state.status && room.status !== state.status) return false;
            if (state.type && room.type !== state.type) return false;
            if (state.floor && String(room.floor) !== state.floor) return false;
            if (!state.search) return true;
            return [room.room_number, room.name, room.type, room.hotel_name || '']
                .join(' ').toLowerCase().includes(state.search);
        });

        if (!filtered.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-door-open fs-1 mb-3"></i>
                            <h5 class="fw-bold">No rooms match your filters</h5>
                            <p class="mb-0">Try clearing filters or add a new room to see it here.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filtered.map(room => roomRowTemplate(room)).join('');
        bindRowActions();
    }

    function roomRowTemplate(room) {
        const statusMap = {
            available:   'bg-success bg-opacity-15 text-success',
            occupied:    'bg-danger bg-opacity-15 text-danger',
            maintenance: 'bg-warning bg-opacity-15 text-warning',
        };
        const statusClass = statusMap[room.status] || 'bg-secondary bg-opacity-15 text-secondary';
        const hotelInfo = window.isSuperAdminArea && room.hotel_name ? `<small class="text-muted">${escapeHtml(room.hotel_name)}</small>` : '';
        const image = room.main_image_url ? `<img src="${room.main_image_url}" class="room-image-thumb" alt="${escapeHtml(room.name)}">` : '<div class="room-image-thumb d-flex align-items-center justify-content-center bg-light text-muted"><i class="bi bi-door-closed fs-5"></i></div>';

        return `
            <tr data-id="${room.id}" data-status="${escapeHtml(room.status)}" data-type="${escapeHtml(room.type)}">
                <td class="pe-0">${image}</td>
                <td>#${escapeHtml(room.room_number)}</td>
                <td>
                    <div class="fw-semibold">${escapeHtml(room.name)}</div>
                    ${hotelInfo}
                </td>
                <td>${escapeHtml(room.type)}</td>
                <td>${escapeHtml(room.capacity)}</td>
                <td>${escapeHtml(room.price_per_night)} MAD</td>
                <td>
                    <span class="badge rounded-pill px-3 badge-animate ${statusClass}">${escapeHtml(capitalize(room.status))}</span>
                </td>
                <td>${escapeHtml(room.updated_at || '')}</td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light border" data-action="view" data-id="${room.id}" title="View Room">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border" data-action="edit" data-id="${room.id}" title="Edit Room">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-danger" data-action="delete" data-id="${room.id}" title="Delete Room">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    function bindRowActions() {
        document.querySelectorAll('button[data-action="view"]').forEach(button => {
            button.addEventListener('click', function () {
                const room = findRoom(this.dataset.id);
                if (room) showViewModal(room);
            });
        });
        document.querySelectorAll('button[data-action="edit"]').forEach(button => {
            button.addEventListener('click', function () {
                const room = findRoom(this.dataset.id);
                if (room) showEditModal(room);
            });
        });
        document.querySelectorAll('button[data-action="delete"]').forEach(button => {
            button.addEventListener('click', function () {
                const room = findRoom(this.dataset.id);
                if (room) showDeleteModal(room);
            });
        });
    }

    function findRoom(id) {
        return window.rooms.find(room => String(room.id) === String(id));
    }

    function showViewModal(room) {
        const modal = new bootstrap.Modal(document.getElementById('viewRoomModal'));
        state.selectedRoomId = room.id;
        document.getElementById('viewMainImage').src = room.main_image_url || 'https://via.placeholder.com/600x400?text=No+Image';
        document.getElementById('viewRoomName').textContent = room.name;
        document.getElementById('viewRoomSubtitle').textContent = room.type;
        document.getElementById('viewStatusBadge').textContent = capitalize(room.status);
        document.getElementById('viewStatusBadge').className = `badge rounded-pill px-3 ${badgeClasses(room.status)}`;
        document.getElementById('viewRoomNumber').textContent = `#${room.room_number}`;
        document.getElementById('viewRoomType').textContent = room.type;
        document.getElementById('viewCapacity').textContent = room.capacity;
        document.getElementById('viewPrice').textContent = `${room.price_per_night} MAD`;
        if (window.isSuperAdminArea) {
            document.getElementById('viewHotelName').textContent = room.hotel_name || '—';
        }
        document.getElementById('viewDescription').textContent = room.description || 'No description provided.';
        modal.show();
    }

    function showEditModal(room) {
        state.selectedRoomId = room.id;
        const form = document.getElementById('editRoomForm');
        form.reset();
        clearFormErrors(form);
        form.querySelector('[name="room_number"]').value = room.room_number;
        form.querySelector('[name="name"]').value = room.name;
        form.querySelector('[name="type"]').value = room.type;
        form.querySelector('[name="capacity"]').value = room.capacity;
        form.querySelector('[name="price_per_night"]').value = room.price_per_night;
        form.querySelector('[name="description"]').value = room.description || '';
        form.querySelector('[name="status"]').value = room.status;
        if (window.isSuperAdminArea) {
            const hotelSelect = form.querySelector('[name="hotel_id"]');
            if (hotelSelect) hotelSelect.value = room.hotel_id || '';
        }
        const modal = new bootstrap.Modal(document.getElementById('editRoomModal'));
        modal.show();
    }

    function showDeleteModal(room) {
        state.selectedRoomId = room.id;
        document.getElementById('deleteRoomName').textContent = room.name;
        const modalEl = document.getElementById('deleteRoomModal');
        const deleteModal = new bootstrap.Modal(modalEl);
        document.getElementById('confirmDeleteRoom').onclick = function () {
            deleteRoom(room.id, deleteModal);
        };
        deleteModal.show();
    }

    function submitRoomForm(form, url, method, modalSelector) {
        clearFormErrors(form);
        const formData = new FormData(form);
        if (method === 'PUT') {
            formData.set('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    showFormErrors(form, data.errors);
                    return;
                }
                throw new Error(data.message || 'Unable to save room.');
            }
            if (data.room) {
                const normalized = normalizeRoomData(data.room);
                const existingIndex = window.rooms.findIndex(item => Number(item.id) === Number(normalized.id));
                if (existingIndex >= 0) {
                    window.rooms[existingIndex] = normalized;
                } else {
                    window.rooms.unshift(normalized);
                }
                renderRooms();
                updateStats();
            }
            showToast(data.message || 'Saved successfully.', 'success');
            form.reset();
            bootstrap.Modal.getInstance(document.querySelector(modalSelector)).hide();
        })
        .catch(error => showToast(error.message, 'danger'));
    }

    function deleteRoom(roomId, modal) {
        const token = document.querySelector('input[name="_token"]').value;
        fetch(`${window.roomsBaseUrl}/${roomId}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({ _method: 'DELETE', _token: token }),
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(data.message || 'Unable to delete room.');
            }
            window.rooms = window.rooms.filter(room => Number(room.id) !== Number(roomId));
            renderRooms();
            updateStats();
            showToast(data.message || 'Room deleted.', 'success');
            modal.hide();
        })
        .catch(error => showToast(error.message, 'danger'));
    }

    function normalizeRoomData(room) {
        const date = room.updated_at ? new Date(room.updated_at) : new Date();
        return {
            id: room.id,
            room_number: room.room_number,
            name: room.name,
            type: room.type,
            capacity: room.capacity,
            price_per_night: Number(room.price_per_night).toFixed(2),
            description: room.description,
            status: room.status,
            main_image_url: room.main_image ? `${window.assetBasePath}/${room.main_image}` : null,
            hotel_id: room.hotel_id,
            hotel_name: room.hotel_name || '',
            updated_at: `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')} ${String(date.getHours()).padStart(2,'0')}:${String(date.getMinutes()).padStart(2,'0')}`,
        };
    }

    function updateStats() {
        const counts = {
            total:       window.rooms.length,
            available:   window.rooms.filter(r => r.status === 'available').length,
            occupied:    window.rooms.filter(r => r.status === 'occupied').length,
            maintenance: window.rooms.filter(r => r.status === 'maintenance').length,
        };
        document.querySelectorAll('.counter').forEach(element => {
            const label = element.closest('.summary-card')?.querySelector('p')?.textContent?.trim().toLowerCase();
            if (!label) return;
            let value = 0;
            if (label.includes('total'))       value = counts.total;
            if (label.includes('available'))   value = counts.available;
            if (label.includes('occupied'))    value = counts.occupied;
            if (label.includes('maintenance')) value = counts.maintenance;
            element.dataset.count = value;
            element.textContent = value;
        });
    }

    function animateCounters() {
        document.querySelectorAll('.counter').forEach(el => {
            const target = Number(el.dataset.count || 0);
            let current = 0;
            const step = Math.max(1, Math.round(target / 30));
            const interval = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(interval);
                }
                el.textContent = current;
            }, 16);
        });
    }

    function clearFormErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    function showFormErrors(form, errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const arrayField = field.replace(/\.\d+$/, '[]');
            const input = form.querySelector(`[name="${field}"]`) || form.querySelector(`[name="${arrayField}"]`) || form.querySelector(`[name="${field}[]"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.closest('.col-md-6, .col-md-4, .col-12, .col-sm-6')?.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = messages.join(' ');
                }
            }
        });
    }

    function showToast(message, type = 'success') {
        const shell = document.getElementById('toastShell');
        const toast = document.createElement('div');
        toast.className = `toast-item ${type}`;
        toast.innerHTML = `<span>${escapeHtml(message)}</span><button type="button" class="btn btn-sm btn-close btn-close-white ms-3"></button>`;
        toast.querySelector('button').addEventListener('click', () => toast.remove());
        shell.appendChild(toast);
        setTimeout(() => toast.classList.add('fade-out'), 3200);
        setTimeout(() => toast.remove(), 3800);
    }

    function escapeHtml(value) {
        return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function capitalize(value) {
        return String(value || '').replace(/(^|\s)\S/g, t => t.toUpperCase());
    }

    function badgeClasses(status) {
        const classes = {
            available:   'bg-success bg-opacity-15 text-success',
            occupied:    'bg-danger bg-opacity-15 text-danger',
            maintenance: 'bg-warning bg-opacity-15 text-warning',
        };
        return classes[status] || 'bg-secondary bg-opacity-15 text-secondary';
    }
</script>
@endpush
