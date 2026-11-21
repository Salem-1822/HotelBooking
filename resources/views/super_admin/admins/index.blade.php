@extends('super_admin.layouts.app')

@section('title', __('super_admin.admin_management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('super_admin.admin_management') }}</h4>
        <p class="text-muted mb-0">{{ __('super_admin.admin_management_subtitle') }}</p>
    </div>
    <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addAdminModal">
        <i class="bi bi-person-plus-fill me-2"></i> {{ __('super_admin.add_administrator') }}
    </button>
</div>

<!-- Errors Display -->
@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-octagon-fill fs-5 me-2"></i>
            <strong>{{ __('super_admin.please_fix_errors') }}</strong>
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
                    <i class="bi bi-shield-lock-fill text-primary fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.8rem; letter-spacing: 0.05em;">{{ __('super_admin.total_administrators') }}</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalAdmins }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 border-0 shadow-sm rounded-4 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-4">
                    <i class="bi bi-geo-alt-fill text-success fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.8rem; letter-spacing: 0.05em;">{{ __('super_admin.assigned_to_cities') }}</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $adminsWithCity }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.admins.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-8 position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted"></i>
                <input type="text" name="search" class="form-control bg-light border-0 ps-5 py-2 rounded-3" placeholder="{{ __('super_admin.search_admins_placeholder') }}" value="{{ request('search') }}" oninput="if(this.value.length >= 3 || this.value.length === 0) { clearTimeout(window.searchTimeout); window.searchTimeout = setTimeout(() => this.form.submit(), 500); }">
            </div>
            <div class="col-md-4 text-end">
                @if(request('search'))
                    <a href="{{ route('super_admin.admins.index') }}" class="btn btn-light px-4 py-2 rounded-3 text-muted">
                        <i class="bi bi-x-lg me-1"></i> {{ __('super_admin.clear_search') }}
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
                        <th class="px-4 py-3 text-muted fw-semibold border-bottom-0">{{ __('super_admin.administrator') }}</th>
                        <th class="py-3 text-muted fw-semibold border-bottom-0">{{ __('super_admin.assigned_city') }}</th>
                        <th class="py-3 text-muted fw-semibold border-bottom-0">{{ __('super_admin.status') }}</th>
                        <th class="py-3 text-muted fw-semibold border-bottom-0">{{ __('super_admin.date_added') }}</th>
                        <th class="px-4 py-3 text-muted fw-semibold border-bottom-0 text-end">{{ __('super_admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($admins as $admin)
                        <tr>
                            <td class="px-4 py-3 border-light">
                                <div class="d-flex align-items-center">
                                    @php
                                        $img = $admin->getProfilePhotoUrl('EBF4FF', '7F9CF5', 100);
                                    @endphp
                                    <img src="{{ $img }}" alt="{{ $admin->name }}" class="rounded-circle shadow-sm me-3" style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #fff;" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=EBF4FF&color=7F9CF5&size=100';">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $admin->name }}</h6>
                                        <small class="text-muted">{{ $admin->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 border-light">
                                @if($admin->city && $admin->hotel)
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fw-semibold border border-info border-opacity-25 text-start" style="width: fit-content;">
                                            <i class="bi bi-geo-alt-fill me-1"></i> {{ $admin->city->name }}
                                        </span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold border border-primary border-opacity-25 text-start" style="width: fit-content;">
                                            <i class="bi bi-building me-1"></i> {{ $admin->hotel->name }}
                                        </span>
                                    </div>
                                @elseif($admin->city)
                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fw-semibold border border-info border-opacity-25">
                                        <i class="bi bi-geo-alt-fill me-1"></i> {{ $admin->city->name }}
                                    </span>
                                @else
                                    <span class="text-muted small"><i class="bi bi-dash"></i> {{ __('super_admin.unassigned') }}</span>
                                @endif
                            </td>
                            <td class="py-3 border-light">
                                @if($admin->is_blocked || $admin->status === 'blocked')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-semibold border border-danger border-opacity-25">
                                        <i class="bi bi-slash-circle me-1"></i> {{ __('super_admin.blocked') }}
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold border border-success border-opacity-25">
                                        <i class="bi bi-check-circle me-1"></i> {{ __('super_admin.active') }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 border-light">
                                <small class="text-muted fw-medium"><i class="bi bi-calendar-event me-1"></i> {{ $admin->created_at->format('M d, Y') }}</small>
                            </td>
                            <td class="px-4 py-3 border-light text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-light btn-sm text-info px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#viewAdminModal{{ $admin->id }}" title="{{ __('super_admin.view') }}">
                                        <i class="bi bi-eye"></i> {{ __('super_admin.view') }}
                                    </button>
                                    <button class="btn btn-light btn-sm text-primary px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                        <i class="bi bi-pencil-square"></i> {{ __('super_admin.edit') }}
                                    </button>
                                    @if(Auth::guard('admin')->id() !== $admin->id)
                                    <button class="btn btn-light btn-sm text-danger px-3 rounded-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#deleteAdminModal{{ $admin->id }}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                    @else
                                    <button class="btn btn-light btn-sm text-muted px-3 rounded-3 shadow-sm border disabled" disabled title="{{ __('super_admin.cannot_delete_self') }}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <div class="bg-light d-inline-block p-4 rounded-circle mb-3 shadow-sm">
                                        <i class="bi bi-shield-lock text-primary fs-1"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mt-3">{{ __('super_admin.no_admins_found') }}</h5>
                                    <p class="text-muted mb-4">{{ __('super_admin.no_admins_desc') }}</p>
                                    @if(request('search'))
                                        <a href="{{ route('super_admin.admins.index') }}" class="btn btn-light border px-4 py-2 rounded-3">{{ __('super_admin.clear_search') }}</a>
                                    @else
                                        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                                            <i class="bi bi-person-plus-fill me-2"></i> {{ __('super_admin.add_first_admin') }}
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
@if($admins->hasPages())
<div class="mt-4 d-flex justify-content-end">
    {{ $admins->links('pagination::bootstrap-5') }}
</div>
@endif

<!-- Add Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('super_admin.admins.store') }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus-fill text-primary me-2"></i>{{ __('super_admin.add_new_administrator') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.full_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-white border" placeholder="{{ __('auth.full_name_placeholder') }}" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.email_address') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control bg-white border" placeholder="admin@example.com" required value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control bg-white border" placeholder="{{ __('auth.password_placeholder') }}" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.assign_city') }} <span class="text-danger">*</span></label>
                        <select name="city_id" id="create_city_id" class="form-select bg-white border" required>
                            <option value="">{{ __('super_admin.select_city') }}</option>
                            @foreach($cities as $c)
                                <option value="{{ $c->id }}" {{ old('city_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.assign_hotel') }} <span class="text-danger">*</span></label>
                        <select name="hotel_id" id="create_hotel_id" class="form-select bg-white border" required>
                            <option value="">{{ __('super_admin.select_hotel') }}</option>
                        </select>
                        <div class="form-text mt-1"><i class="bi bi-info-circle me-1"></i>{{ __('super_admin.admin_manages_only_hotel') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.profile_image') }}</label>
                        <input type="file" name="image_file" class="form-control bg-white border" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text mt-1"><i class="bi bi-info-circle me-1"></i>{{ __('super_admin.optional_max_2mb') }}</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-white border px-4 rounded-3 fw-medium text-muted" data-bs-dismiss="modal">{{ __('super_admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm fw-medium">{{ __('super_admin.create_administrator') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Extracted Edit & Delete Modals -->
@foreach($admins as $admin)
<!-- View Modal -->
<div class="modal fade" id="viewAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-circle text-primary me-2"></i>{{ __('super_admin.admin_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @php
                    $adminViewImg = $admin->getProfilePhotoUrl('EBF4FF', '7F9CF5', 140);
                @endphp
                <div class="text-center mb-4">
                    <img src="{{ $adminViewImg }}" alt="{{ $admin->name }}" class="rounded-circle shadow-sm border mb-3" style="width: 70px; height: 70px; object-fit: cover;" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=EBF4FF&color=7F9CF5&size=140';">
                    <h4 class="fw-bold mb-1">{{ $admin->name }}</h4>
                    <p class="text-muted small mb-0 fw-medium">{{ $admin->email }}</p>
                </div>
                <div class="bg-light rounded-4 p-4 border shadow-sm">
                    <div class="row g-4">
                        <div class="col-6">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">{{ __('super_admin.status') }}</span>
                            @if($admin->is_blocked || $admin->status === 'blocked')
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-semibold border border-danger border-opacity-25">
                                    <i class="bi bi-slash-circle me-1"></i> {{ __('super_admin.blocked') }}
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold border border-success border-opacity-25">
                                    <i class="bi bi-check-circle me-1"></i> {{ __('super_admin.active') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-6">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">{{ __('super_admin.date_added') }}</span>
                            <span class="fw-medium text-dark"><i class="bi bi-calendar-event text-primary me-1"></i>{{ $admin->created_at->format('M d, Y') }}</span>
                        </div>
                        @if(($admin->is_blocked || $admin->status === 'blocked') && $admin->blocked_reason)
                        <div class="col-12">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">{{ __('super_admin.block_reason') }}</span>
                            <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25 text-danger small" style="word-break: break-word; overflow-wrap: anywhere; white-space: pre-line;">
                                <i class="bi bi-info-circle-fill me-1"></i> {{ $admin->blocked_reason }}
                            </div>
                        </div>
                        @endif
                        <div class="col-12">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">{{ __('super_admin.assigned_city') }}</span>
                            @if($admin->city)
                                <span class="fw-medium text-dark"><i class="bi bi-geo-alt text-primary me-1"></i>{{ $admin->city->name }}</span>
                            @else
                                <span class="text-muted small">{{ __('super_admin.unassigned') }}</span>
                            @endif
                        </div>
                        @if($admin->hotel)
                        <div class="col-12">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">{{ __('super_admin.assigned_hotel') }}</span>
                            <span class="fw-medium text-dark"><i class="bi bi-building text-primary me-1"></i>{{ $admin->hotel->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light px-4 py-3">
                <button type="button" class="btn btn-primary px-4 rounded-3 shadow-sm fw-medium w-100" data-bs-dismiss="modal">{{ __('super_admin.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('super_admin.admins.update', $admin) }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>{{ __('super_admin.edit_administrator') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.full_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-white border" value="{{ $admin->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.email_address') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control bg-white border" value="{{ $admin->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.new_password') }}</label>
                        <input type="password" name="password" class="form-control bg-white border" placeholder="{{ __('super_admin.leave_empty_password') }}" minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.assign_city') }} <span class="text-danger">*</span></label>
                        <select name="city_id" id="edit_city_{{ $admin->id }}" class="form-select bg-white border city-select" data-admin-id="{{ $admin->id }}" required>
                            <option value="">{{ __('super_admin.select_city') }}</option>
                            @foreach($cities as $c)
                                <option value="{{ $c->id }}" {{ $admin->city_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.assign_hotel') }} <span class="text-danger">*</span></label>
                        <select name="hotel_id" id="edit_hotel_{{ $admin->id }}" class="form-select bg-white border" data-current-hotel="{{ $admin->hotel_id }}" required>
                            <option value="">{{ __('super_admin.select_hotel') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.account_status') }} <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status_{{ $admin->id }}" class="form-select bg-white border admin-status-select" data-admin-id="{{ $admin->id }}" required>
                            <option value="active" {{ (!$admin->is_blocked && $admin->status === 'active') ? 'selected' : '' }}>{{ __('super_admin.status_active_unblocked') }}</option>
                            <option value="blocked" {{ ($admin->is_blocked || $admin->status === 'blocked') ? 'selected' : '' }}>{{ __('super_admin.status_blocked') }}</option>
                        </select>
                    </div>
                    <div class="mb-3 {{ ($admin->is_blocked || $admin->status === 'blocked') ? '' : 'd-none' }}" id="blocked_reason_container_{{ $admin->id }}">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.blocking_reason_label') }} <span class="text-danger">*</span></label>
                        <textarea name="blocked_reason" 
                                  id="edit_blocked_reason_{{ $admin->id }}" 
                                  class="form-control bg-white border" 
                                  rows="3" 
                                  placeholder="{{ __('super_admin.blocking_reason_placeholder') }}">{{ old('blocked_reason', $admin->blocked_reason) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-2">{{ __('super_admin.update_profile_image') }}</label>
                        <input type="file" name="image_file" class="form-control bg-white border" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text mt-1"><i class="bi bi-info-circle me-1"></i>{{ __('super_admin.optional_keep_image') }}</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-white border px-4 rounded-3 fw-medium text-muted" data-bs-dismiss="modal">{{ __('super_admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm fw-medium">{{ __('super_admin.save_changes') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
@if(Auth::guard('admin')->id() !== $admin->id)
<div class="modal fade" id="deleteAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-4">
            <div class="mb-3 mt-2">
                <div class="d-inline-block bg-danger bg-opacity-10 p-3 rounded-circle text-danger mb-4">
                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('super_admin.delete_admin_confirm_title', ['name' => $admin->name]) }}</h5>
                <p class="text-muted small mb-4">{{ __('super_admin.delete_admin_confirm_text') }}</p>
            </div>
            <form action="{{ route('super_admin.admins.destroy', $admin) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light px-4 w-50 rounded-3 fw-medium" data-bs-dismiss="modal">{{ __('super_admin.cancel') }}</button>
                    <button type="submit" class="btn btn-danger px-4 w-50 rounded-3 shadow-sm fw-medium">{{ __('super_admin.yes_delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cities = @json($cities);

        function populateHotels(citySelectId, hotelSelectId, selectedHotelId = null) {
            const citySelect = document.getElementById(citySelectId);
            const hotelSelect = document.getElementById(hotelSelectId);
            
            if (!citySelect || !hotelSelect) return;

            const cityId = citySelect.value;
            hotelSelect.innerHTML = '<option value="">-- Select Hotel --</option>';

            if (cityId) {
                const city = cities.find(c => c.id == cityId);
                if (city && city.hotels) {
                    city.hotels.forEach(hotel => {
                        const option = document.createElement('option');
                        option.value = hotel.id;
                        option.textContent = hotel.name;
                        if (selectedHotelId && selectedHotelId == hotel.id) {
                            option.selected = true;
                        }
                        hotelSelect.appendChild(option);
                    });
                }
            }
        }

        // Create form
        const createCitySelect = document.getElementById('create_city_id');
        if (createCitySelect) {
            createCitySelect.addEventListener('change', function() {
                populateHotels('create_city_id', 'create_hotel_id');
            });
            // Initial population if old('city_id') exists
            if (createCitySelect.value) {
                populateHotels('create_city_id', 'create_hotel_id', "{{ old('hotel_id') }}");
            }
        }

        // Edit forms
        const editCitySelects = document.querySelectorAll('.city-select');
        editCitySelects.forEach(select => {
            select.addEventListener('change', function() {
                const adminId = this.getAttribute('data-admin-id');
                populateHotels('edit_city_' + adminId, 'edit_hotel_' + adminId);
            });
            
            // Initial population
            const adminId = select.getAttribute('data-admin-id');
            const hotelSelect = document.getElementById('edit_hotel_' + adminId);
            const currentHotelId = hotelSelect.getAttribute('data-current-hotel');
            if (select.value) {
                populateHotels('edit_city_' + adminId, 'edit_hotel_' + adminId, currentHotelId);
            }
        });

        // Account Status change handling (show/hide blocking reason)
        const statusSelects = document.querySelectorAll('.admin-status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const adminId = this.getAttribute('data-admin-id');
                const container = document.getElementById('blocked_reason_container_' + adminId);
                const reasonTextarea = document.getElementById('edit_blocked_reason_' + adminId);

                if (this.value === 'blocked') {
                    if (container) container.classList.remove('d-none');
                    if (reasonTextarea) {
                        reasonTextarea.setAttribute('required', 'required');
                        reasonTextarea.removeAttribute('disabled');
                    }
                } else {
                    if (container) container.classList.add('d-none');
                    if (reasonTextarea) {
                        reasonTextarea.removeAttribute('required');
                    }
                }
            });
        });
    });
</script>

@endsection
