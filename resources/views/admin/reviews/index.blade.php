@extends('admin.layouts.app')

@section('title', __('admin.hotel_reviews'))

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
    .reviews-table tbody tr:hover {
        background: rgba(15, 23, 42, 0.02);
    }
    .reviews-table td,
    .reviews-table th {
        vertical-align: middle;
    }
    .client-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #E2E8F0;
        background: #F8FAFC;
        flex-shrink: 0;
    }
    .stars-display {
        color: #D4AF37;
        font-size: 0.95rem;
        letter-spacing: 0.05em;
    }
    .stars-display .empty-star {
        color: #E2E8F0;
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
    .comment-preview {
        max-width: 280px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--text-muted);
        font-size: 0.875rem;
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
</style>
@endpush

@section('content')

<div class="page-title-bar mb-4">
    <div class="title-group">
        <h4 class="fw-bold mb-1">{{ __('admin.hotel_reviews') }}</h4>
        <p class="mb-0 text-muted">{{ __('admin.hotel_reviews_desc') }}</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.total_reviews') }}</p>
                <h3 class="mb-1 fw-bold">{{ $totalReviews }}</h3>
                <span class="badge bg-light text-dark">{{ __('admin.hotel_scope') }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card summary-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.08em;">{{ __('admin.average_rating') }}</p>
                <h3 class="mb-1 fw-bold text-warning">
                    {{ $averageRating > 0 ? $averageRating : '—' }}
                    @if($averageRating > 0)
                        <span style="font-size:1rem;">★</span>
                    @endif
                </h3>
                <span class="badge bg-warning bg-opacity-10 text-warning">{{ __('admin.out_of_5') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Reviews Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 reviews-table table-animate" id="reviewsTable">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th>{{ __('admin.th_client') }}</th>
                    <th>{{ __('admin.th_rating') }}</th>
                    <th>{{ __('admin.th_comment') }}</th>
                    <th>{{ __('admin.th_date') }}</th>
                    <th class="text-center">{{ __('admin.th_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $review->user ? 'https://ui-avatars.com/api/?name=' . urlencode($review->user->name) . '&background=0F172A&color=fff&size=84' : 'https://ui-avatars.com/api/?name=Unknown&background=CBD5E1&color=fff&size=84' }}"
                                class="client-avatar" alt="{{ $review->user->name ?? 'Unknown' }}" loading="lazy" decoding="async">
                            <div>
                                <div class="fw-semibold" style="font-size:0.875rem;">{{ $review->user->name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:0.78rem;"><span dir="ltr">{{ $review->user->email ?? '—' }}</span></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="stars-display">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    ★
                                @else
                                    <span class="empty-star">★</span>
                                @endif
                            @endfor
                        </span>
                        <span class="badge bg-warning bg-opacity-10 text-warning ms-1"><span dir="ltr">{{ $review->rating }}/5</span></span>
                    </td>
                    <td>
                        <div class="comment-preview" title="{{ $review->comment }}">
                            {{ $review->comment ?? '—' }}
                        </div>
                    </td>
                    <td>
                        <span class="text-muted" style="font-size:0.875rem;">{{ $review->created_at->format('Y-m-d H:i') }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button"
                                class="btn btn-sm btn-outline-dark px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#viewReviewModal"
                                data-id="{{ $review->id }}"
                                data-client="{{ e($review->user->name ?? 'Unknown') }}"
                                data-email="{{ e($review->user->email ?? '') }}"
                                data-rating="{{ $review->rating }}"
                                data-comment="{{ e($review->comment ?? '') }}"
                                data-hotel="{{ e($review->hotel->name ?? '') }}"
                                data-date="{{ $review->created_at->format('d M Y, H:i') }}"
                                title="{{ __('admin.view') }}">
                                <i class="bi bi-eye-fill me-1"></i> {{ __('admin.view') }}
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteReviewModal"
                                data-id="{{ $review->id }}"
                                data-client="{{ e($review->user->name ?? 'Unknown') }}"
                                title="{{ __('admin.delete') }}">
                                <i class="bi bi-trash-fill me-1"></i> {{ __('admin.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-star fs-1 mb-3 d-block"></i>
                            <h5 class="fw-bold">{{ __('admin.no_reviews_yet') }}</h5>
                            <p class="mb-0">{{ __('admin.no_reviews_yet_desc') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
    <div class="card-footer bg-transparent border-top d-flex justify-content-end py-3 px-4">
        {{ $reviews->links() }}
    </div>
    @endif
</div>

{{-- View Review Modal --}}
<div class="modal fade" id="viewReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('admin.review_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('admin.th_client') }}</div>
                            <div class="fw-semibold" id="vrClient"></div>
                            <div class="text-muted small" id="vrEmail"></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('admin.hotel') }}</div>
                            <div class="fw-semibold" id="vrHotel"></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('admin.submitted_on') }}</div>
                            <div class="fw-semibold" id="vrDate"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('admin.th_rating') }}</div>
                            <div class="stars-display fs-5" id="vrStars"></div>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">{{ __('admin.th_comment') }}</div>
                            <p class="mb-0" id="vrComment" style="word-break:break-word;overflow-wrap:anywhere;white-space:pre-line;"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.close') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Review Modal --}}
<div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('admin.delete_review') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <p class="mb-0">{{ __('admin.delete_review_confirm', ['client' => '']) }}<strong id="drClient"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                <form id="deleteReviewForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash-fill me-1"></i> {{ __('admin.delete_review') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Animate table in
        setTimeout(() => document.getElementById('reviewsTable').classList.add('is-visible'), 100);

        // View modal
        document.getElementById('viewReviewModal').addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            document.getElementById('vrClient').textContent  = btn.dataset.client  || '—';
            document.getElementById('vrEmail').textContent   = btn.dataset.email   || '—';
            document.getElementById('vrHotel').textContent   = btn.dataset.hotel   || '—';
            document.getElementById('vrDate').textContent    = btn.dataset.date    || '—';
            document.getElementById('vrComment').textContent = btn.dataset.comment || 'No comment provided.';

            const rating = parseInt(btn.dataset.rating, 10) || 0;
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating
                    ? '★'
                    : '<span class="empty-star">★</span>';
            }
            document.getElementById('vrStars').innerHTML = stars +
                `<span class="badge bg-warning bg-opacity-10 text-warning ms-2 fs-6">${rating}/5</span>`;
        });

        // Delete modal
        document.getElementById('deleteReviewModal').addEventListener('show.bs.modal', function (event) {
            const btn    = event.relatedTarget;
            const id     = btn.dataset.id;
            const client = btn.dataset.client || 'this client';
            document.getElementById('drClient').textContent = client;
            document.getElementById('deleteReviewForm').action = `/admin/reviews/${id}`;
        });
    });
</script>
@endpush
