@extends('super_admin.layouts.app')

@section('title', 'Platform Messages')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h5 class="fw-bold mb-0">Platform Messages</h5>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge bg-primary rounded-pill shadow-sm">{{ $messages->total() }} Messages</span>
            <p class="text-muted small mb-0 d-none d-sm-block">Manage incoming contact inquiries.</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
    <div class="card-body p-4 bg-white">
        <form action="{{ route('super_admin.messages.index') }}" method="GET" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select name="status" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">All Messages</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                <div class="col-12 col-md-8 d-flex justify-content-md-end">
                    <a href="{{ route('super_admin.messages.index') }}" class="btn btn-light border-0 text-muted" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Messages List (Cards format) --}}
<div class="row g-3">
    @forelse($messages as $message)
        <div class="col-12">
            <div class="card border-0 shadow-sm message-card" style="border-radius: 0.75rem; transition: transform 0.2s; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#viewMessageModal{{ $message->id }}">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        {{-- Sender Info --}}
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                    <span class="fw-bold fs-5">{{ strtoupper(substr($message->name, 0, 1)) }}</span>
                                </div>
                                <div class="text-truncate">
                                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                        {{ $message->name }}
                                        @if($message->status == 'unread')
                                            <span class="p-1 bg-danger border border-light rounded-circle" id="icon-unread-{{ $message->id }}"></span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $message->email }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- Message Preview --}}
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6 class="mb-1 fw-bold text-dark text-truncate">{{ $message->subject }}</h6>
                            <p class="mb-0 text-muted small text-truncate" style="max-width: 100%;">
                                {{ Str::limit($message->message, 80) }}
                            </p>
                        </div>

                        {{-- Metadata & Actions --}}
                        <div class="col-md-3 d-flex justify-content-md-end align-items-center gap-3">
                            <div class="text-end me-3 d-none d-md-block">
                                <small class="text-muted d-block">{{ $message->created_at->format('M d, Y') }}</small>
                                <span id="status-card-badge-{{ $message->id }}" class="badge {{ $message->status == 'unread' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-success bg-opacity-10 text-success' }} mt-1">
                                    {{ ucfirst($message->status) }}
                                </span>
                            </div>
                            
                            <button type="button" class="btn btn-light rounded-circle p-2 text-primary border" title="View Message">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- View Message Modal --}}
        <div class="modal fade message-modal" id="viewMessageModal{{ $message->id }}" tabindex="-1" aria-hidden="true" data-message-id="{{ $message->id }}" data-status="{{ $message->status }}">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-0 py-3">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-envelope-open me-2 text-primary"></i>Message Details
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h4 class="fw-bold text-dark mb-1">{{ $message->subject }}</h4>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i> Received on {{ $message->created_at->format('M d, Y \a\t h:i A') }}
                                </div>
                            </div>
                            <div id="status-badge-{{ $message->id }}" class="badge {{ $message->status == 'unread' ? 'bg-warning bg-opacity-10 text-warning border-warning' : 'bg-success bg-opacity-10 text-success border-success' }} px-3 py-2 rounded-pill fw-semibold border border-opacity-25">
                                {{ ucfirst($message->status) }}
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 50px; height: 50px;">
                                        <span class="fw-bold fs-5">{{ strtoupper(substr($message->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $message->name }}</h6>
                                        <a href="mailto:{{ $message->email }}" class="text-decoration-none small">{{ $message->email }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-2">
                            <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Message Content</h6>
                            <p class="text-dark" style="white-space: pre-line; line-height: 1.7;">{{ $message->message }}</p>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3 d-flex justify-content-between">
                        <form action="{{ route('super_admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                                <i class="bi bi-trash3 me-2"></i>Delete
                            </button>
                        </form>
                        <button type="button" class="btn btn-secondary shadow-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-body p-5 text-center">
                    <div class="text-muted mb-3">
                        <i class="bi bi-envelope-paper fs-1 opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No messages found</h5>
                    <p class="text-muted mb-0">There are currently no contact messages to display.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($messages->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $messages->links('pagination::bootstrap-5') }}
    </div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const viewModals = document.querySelectorAll('.message-modal');
        
        viewModals.forEach(modal => {
            modal.addEventListener('show.bs.modal', function (event) {
                const messageId = this.dataset.messageId;
                const currentStatus = this.dataset.status;
                
                if (currentStatus === 'unread') {
                    // Mark as read via AJAX
                    fetch(`/super-admin/messages/${messageId}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            this.dataset.status = 'read';
                            
                            // Update badge inside modal
                            const modalBadge = document.getElementById('status-badge-' + messageId);
                            if(modalBadge) {
                                modalBadge.className = 'badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold border border-success border-opacity-25';
                                modalBadge.innerHTML = 'Read';
                            }
                            
                            // Update badge on card
                            const cardBadge = document.getElementById('status-card-badge-' + messageId);
                            if(cardBadge) {
                                cardBadge.className = 'badge bg-success bg-opacity-10 text-success mt-1';
                                cardBadge.innerHTML = 'Read';
                            }
                            
                            // Remove unread dot indicator
                            const iconBadge = document.getElementById('icon-unread-' + messageId);
                            if(iconBadge) {
                                iconBadge.remove();
                            }
                        }
                    })
                    .catch(error => console.error('Error marking message as read:', error));
                }
            });
        });
        
        // Add hover effect style dynamically if preferred, or via css class
        const cards = document.querySelectorAll('.message-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-2px)';
                card.style.boxShadow = '0 .5rem 1rem rgba(0,0,0,.15)!important';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = '0 .125rem .25rem rgba(0,0,0,.075)!important';
            });
        });
    });
</script>
@endpush
