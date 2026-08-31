@extends('client.layouts.app')

@section('title', 'Browse Hotels')

@push('styles')
<style>
/* ══════════════════════════════════════════════════════
   Page Header
══════════════════════════════════════════════════════ */
.page-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, #1e3a5f 100%);
    padding: 3rem 0 2rem;
    color: #fff;
}
.page-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
.page-header p  { color: rgba(255,255,255,0.7); margin: 0; font-size: 0.95rem; }

/* Breadcrumb */
.breadcrumb-custom .breadcrumb-item a { color: rgba(255,255,255,0.6); text-decoration:none; }
.breadcrumb-custom .breadcrumb-item a:hover { color: var(--brand-accent); }
.breadcrumb-custom .breadcrumb-item.active { color: rgba(255,255,255,0.9); }
.breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }

/* ══════════════════════════════════════════════════════
   Search Bar (inline, inside header)
══════════════════════════════════════════════════════ */
.search-bar-wrap {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 0.875rem;
    padding: 1rem 1.25rem;
    margin-top: 1.5rem;
}
.search-bar-wrap .form-control,
.search-bar-wrap .form-select {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    border-radius: 0.625rem;
    padding: 0.625rem 1rem;
    font-size: 0.9rem;
}
.search-bar-wrap .form-control::placeholder { color: rgba(255,255,255,0.5); }
.search-bar-wrap .form-control:focus,
.search-bar-wrap .form-select:focus {
    background: rgba(255,255,255,0.18);
    border-color: var(--brand-accent);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.2);
    color: #fff;
}
.search-bar-wrap .form-select option { color: var(--text-primary); background: #fff; }
.search-bar-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: rgba(255,255,255,0.5);
    margin-bottom: 0.3rem;
    display: block;
}

/* ══════════════════════════════════════════════════════
   Layout: Sidebar + Results
══════════════════════════════════════════════════════ */
.results-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
    align-items: start;
}
@media (max-width: 991px) {
    .results-layout {
        grid-template-columns: 1fr;
    }
    .filter-sidebar { display: none; } /* replaced by drawer on mobile */
}

/* ══════════════════════════════════════════════════════
   Filter Sidebar
══════════════════════════════════════════════════════ */
.filter-sidebar {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 1.5rem;
    position: sticky;
    top: 90px;
    box-shadow: var(--card-shadow);
}
.filter-section-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 0.875rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}
.filter-sidebar .form-check-label { font-size: 0.9rem; font-weight: 500; cursor: pointer; }
.filter-sidebar .form-check-input:checked {
    background-color: var(--brand-primary);
    border-color: var(--brand-primary);
}
.star-radio label { cursor: pointer; font-size: 0.9rem; }
.star-radio input[type=radio]:checked + label { color: var(--brand-primary); font-weight: 600; }
.price-range-display {
    display: flex; justify-content: space-between;
    font-size: 0.85rem; font-weight: 600; color: var(--text-primary);
    margin-bottom: 0.5rem;
}
.filter-sidebar input[type=range] {
    width: 100%;
    accent-color: var(--brand-accent);
}
.btn-clear-filters {
    width: 100%;
    border: 1px solid var(--border-color);
    border-radius: 0.625rem;
    background: transparent;
    color: var(--text-muted);
    font-size: 0.85rem;
    font-weight: 500;
    padding: 0.5rem;
    transition: all 0.2s;
    text-decoration: none;
    display: block;
    text-align: center;
    margin-top: 0.5rem;
}
.btn-clear-filters:hover { border-color: var(--brand-primary); color: var(--brand-primary); }

/* ══════════════════════════════════════════════════════
   Toolbar (results count + sort)
══════════════════════════════════════════════════════ */
.results-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.results-count {
    font-size: 0.9rem;
    color: var(--text-muted);
}
.results-count strong { color: var(--text-primary); }
.sort-select {
    border: 1px solid var(--border-color);
    border-radius: 0.625rem;
    padding: 0.45rem 0.875rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
    background: #fff;
    cursor: pointer;
    box-shadow: none;
}
.sort-select:focus { border-color: var(--brand-primary); box-shadow: none; outline: none; }

/* Mobile filter trigger button */
.btn-mobile-filter {
    display: none;
    background: var(--brand-primary);
    color: #fff;
    border: none;
    border-radius: 0.625rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    gap: 0.5rem;
    align-items: center;
    cursor: pointer;
}
@media (max-width: 991px) {
    .btn-mobile-filter { display: inline-flex; }
}

/* ══════════════════════════════════════════════════════
   Active Filter Pills
══════════════════════════════════════════════════════ */
.active-filters { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem; }
.filter-pill {
    background: rgba(15,23,42,0.07);
    border: 1px solid rgba(15,23,42,0.15);
    border-radius: 2rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--brand-primary);
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
}
.filter-pill:hover { background: rgba(15,23,42,0.12); color: var(--brand-primary); }
.filter-pill i { font-size: 0.7rem; }

/* ══════════════════════════════════════════════════════
   Hotel Cards
══════════════════════════════════════════════════════ */
.hotel-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    overflow: hidden;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.hotel-card:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-4px);
}
.hotel-img-wrap {
    position: relative;
    height: 210px;
    overflow: hidden;
    flex-shrink: 0;
}
.hotel-img-wrap img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.45s ease;
}
.hotel-card:hover .hotel-img-wrap img { transform: scale(1.04); }
.hotel-img-placeholder {
    width: 100%; height: 100%;
    background: linear-gradient(135deg, var(--brand-primary) 0%, #1e3a5f 100%);
    display: flex; align-items: center; justify-content: center;
}
.hotel-img-placeholder i { font-size: 3.5rem; color: rgba(255,255,255,0.12); }
.hotel-stars-badge {
    position: absolute; top: 0.75rem; left: 0.75rem;
    background: var(--brand-accent);
    color: #fff;
    font-size: 0.72rem; font-weight: 700;
    padding: 0.2rem 0.625rem;
    border-radius: 2rem;
    letter-spacing: 0.02em;
}
.hotel-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
.hotel-name {
    font-size: 1.05rem; font-weight: 700;
    color: var(--brand-primary);
    margin-bottom: 0.3rem;
    text-decoration: none;
    display: block;
    /* Clamp long names to 2 lines */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.hotel-name:hover { color: var(--brand-accent); }
.hotel-location { font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.75rem; }
.hotel-description {
    font-size: 0.85rem;
    color: #475569;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}
.hotel-footer {
    border-top: 1px solid var(--border-color);
    padding-top: 0.875rem;
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.5rem;
}
.hotel-price { font-size: 1.1rem; font-weight: 800; color: var(--brand-primary); }
.hotel-price sup { font-size: 0.7rem; font-weight: 500; vertical-align: super; }
.hotel-price small { font-size: 0.72rem; font-weight: 400; color: var(--text-muted); }
.hotel-rating {
    display: flex; align-items: center; gap: 0.2rem;
    font-size: 0.82rem; font-weight: 600; color: #475569;
}
.hotel-rating .star-filled { color: #F59E0B; }
.btn-view-hotel {
    background: var(--brand-primary);
    color: #fff;
    border-radius: 0.5rem;
    font-size: 0.82rem; font-weight: 600;
    padding: 0.45rem 0.875rem;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}
.btn-view-hotel:hover {
    background: var(--brand-secondary);
    color: #fff;
}

/* ══════════════════════════════════════════════════════
   Empty / Error State
══════════════════════════════════════════════════════ */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border: 1px dashed var(--border-color);
    border-radius: 1rem;
}
.empty-state-icon { font-size: 3.5rem; color: #CBD5E1; margin-bottom: 1.25rem; }
.empty-state h4 { font-weight: 700; color: var(--brand-primary); margin-bottom: 0.5rem; }
.empty-state p { color: var(--text-muted); font-size: 0.95rem; max-width: 360px; margin: 0 auto 1.5rem; }

/* ══════════════════════════════════════════════════════
   Pagination
══════════════════════════════════════════════════════ */
.pagination-wrap { margin-top: 2.5rem; }
.pagination .page-link {
    border-radius: 0.5rem !important;
    margin: 0 2px;
    border-color: var(--border-color);
    color: var(--text-primary);
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.45rem 0.875rem;
    transition: all 0.15s ease;
}
.pagination .page-item.active .page-link {
    background-color: var(--brand-primary);
    border-color: var(--brand-primary);
}
.pagination .page-link:hover:not(.active) {
    background: var(--brand-primary-light);
    border-color: var(--brand-primary);
    color: var(--brand-primary);
}

/* ══════════════════════════════════════════════════════
   Mobile Filter Drawer
══════════════════════════════════════════════════════ */
.filter-drawer-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 1055;
    backdrop-filter: blur(2px);
}
.filter-drawer-overlay.open { display: block; }
.filter-drawer {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    max-height: 92vh;
    background: #fff;
    border-radius: 1.5rem 1.5rem 0 0;
    z-index: 1060;
    transform: translateY(100%);
    transition: transform 0.32s cubic-bezier(0.4,0,0.2,1);
    overflow-y: auto;
    padding: 1.5rem;
}
.filter-drawer.open { transform: translateY(0); }
.filter-drawer-handle {
    width: 40px; height: 4px;
    background: #CBD5E1; border-radius: 2px;
    margin: 0 auto 1.5rem;
}
.filter-drawer-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem;
}
.filter-drawer-header h5 { font-weight: 700; margin: 0; }
</style>
@endpush

@section('content')
<!-- ══════════════════════════════════════════
     Page Header + Inline Search
════════════════════════════════════════════ -->
<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-custom">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hotels</li>
            </ol>
        </nav>
        <h1>Browse Our Hotels</h1>
        <p>Find the perfect stay from our curated collection across top destinations</p>

        <!-- Inline Search Bar -->
        <form method="GET" action="{{ route('hotels.index') }}" class="search-bar-wrap" id="searchForm">
            <div class="row g-3 align-items-end">
                <div class="col-lg-5 col-md-6">
                    <span class="search-bar-label">Search by name or location</span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Hotel name, city, address…"
                           value="{{ $validated['search'] ?? '' }}"
                           autocomplete="off">
                </div>
                <div class="col-lg-3 col-md-6">
                    <span class="search-bar-label">Destination</span>
                    <select name="city_id" class="form-select">
                        <option value="">All destinations</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ ($validated['city_id'] ?? '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <span class="search-bar-label">Star rating</span>
                    <select name="stars" class="form-select">
                        <option value="">Any stars</option>
                        @for($s = 5; $s >= 1; $s--)
                            <option value="{{ $s }}" {{ ($validated['stars'] ?? '') == $s ? 'selected' : '' }}>
                                {{ $s }} Star{{ $s > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
                {{-- carry through sort and price from sidebar --}}
                @if(!empty($validated['sort']))
                    <input type="hidden" name="sort" value="{{ $validated['sort'] }}">
                @endif
                @if(!empty($validated['price_min']))
                    <input type="hidden" name="price_min" value="{{ $validated['price_min'] }}">
                @endif
                @if(!empty($validated['price_max']))
                    <input type="hidden" name="price_max" value="{{ $validated['price_max'] }}">
                @endif
                <div class="col-lg-2 col-md-4">
                    <button type="submit" class="btn btn-accent w-100 py-2">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════
     Main Content Area
════════════════════════════════════════════ -->
<div class="container py-4 py-lg-5">

    <!-- Active Filter Pills (desktop + mobile) -->
    @php
        $hasFilters = !empty($validated['search']) || !empty($validated['city_id'])
            || !empty($validated['stars']) || !empty($validated['price_min'])
            || !empty($validated['price_max']);
    @endphp
    @if($hasFilters)
    <div class="active-filters mb-3">
        @if(!empty($validated['search']))
            <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}" class="filter-pill">
                <i class="bi bi-x"></i> "{{ $validated['search'] }}"
            </a>
        @endif
        @if(!empty($validated['city_id']))
            @php $selectedCity = $cities->firstWhere('id', $validated['city_id']); @endphp
            @if($selectedCity)
            <a href="{{ request()->fullUrlWithQuery(['city_id' => null, 'page' => null]) }}" class="filter-pill">
                <i class="bi bi-x"></i> {{ $selectedCity->name }}
            </a>
            @endif
        @endif
        @if(!empty($validated['stars']))
            <a href="{{ request()->fullUrlWithQuery(['stars' => null, 'page' => null]) }}" class="filter-pill">
                <i class="bi bi-x"></i> {{ $validated['stars'] }} Stars
            </a>
        @endif
        @if(!empty($validated['price_min']) || !empty($validated['price_max']))
            <a href="{{ request()->fullUrlWithQuery(['price_min' => null, 'price_max' => null, 'page' => null]) }}" class="filter-pill">
                <i class="bi bi-x"></i>
                ${{ number_format($validated['price_min'] ?? 0) }} – ${{ number_format($validated['price_max'] ?? ($priceRange->max_price ?? 9999)) }}
            </a>
        @endif
        <a href="{{ route('hotels.index') }}" class="filter-pill" style="color:#EF4444; border-color:rgba(239,68,68,0.3);">
            <i class="bi bi-x-circle"></i> Clear all
        </a>
    </div>
    @endif

    <div class="results-layout">
        <!-- ══════════════════ FILTER SIDEBAR (desktop) ══════════════════ -->
        <aside class="filter-sidebar" aria-label="Filter hotels">
            <form method="GET" action="{{ route('hotels.index') }}" id="filterForm">
                {{-- carry through text search & sort --}}
                @if(!empty($validated['search']))
                    <input type="hidden" name="search" value="{{ $validated['search'] }}">
                @endif
                @if(!empty($validated['sort']))
                    <input type="hidden" name="sort" value="{{ $validated['sort'] }}">
                @endif

                <!-- City -->
                <p class="filter-section-title">Destination</p>
                <div class="mb-4">
                    @foreach($cities as $city)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="radio" name="city_id"
                               id="city_{{ $city->id }}"
                               value="{{ $city->id }}"
                               {{ ($validated['city_id'] ?? '') == $city->id ? 'checked' : '' }}
                               onchange="document.getElementById('filterForm').submit()">
                        <label class="form-check-label" for="city_{{ $city->id }}">
                            {{ $city->name }}
                            <span class="text-muted" style="font-size:0.8rem;">({{ $city->hotels_count }})</span>
                        </label>
                    </div>
                    @endforeach
                    @if(!empty($validated['city_id']))
                    <div class="mt-2">
                        <a href="{{ request()->fullUrlWithQuery(['city_id' => null, 'page' => null]) }}"
                           class="text-muted" style="font-size:0.82rem; text-decoration:underline;">Clear</a>
                    </div>
                    @endif
                </div>

                <!-- Star Rating -->
                <p class="filter-section-title">Star Rating</p>
                <div class="mb-4">
                    @for($s = 5; $s >= 1; $s--)
                    <div class="form-check star-radio mb-1">
                        <input class="form-check-input" type="radio" name="stars"
                               id="stars_{{ $s }}" value="{{ $s }}"
                               {{ ($validated['stars'] ?? '') == $s ? 'checked' : '' }}
                               onchange="document.getElementById('filterForm').submit()">
                        <label class="form-check-label" for="stars_{{ $s }}">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $s ? '-fill text-warning' : '' }}" style="font-size:0.8rem;"></i>
                            @endfor
                            &nbsp;{{ $s }}★
                        </label>
                    </div>
                    @endfor
                    @if(!empty($validated['stars']))
                    <div class="mt-2">
                        <a href="{{ request()->fullUrlWithQuery(['stars' => null, 'page' => null]) }}"
                           class="text-muted" style="font-size:0.82rem; text-decoration:underline;">Clear</a>
                    </div>
                    @endif
                </div>

                <!-- Price Range -->
                @if($priceRange && $priceRange->max_price > 0)
                <p class="filter-section-title">Price per Night</p>
                <div class="mb-4">
                    @php
                        $absMin = (int) floor($priceRange->min_price ?? 0);
                        $absMax = (int) ceil($priceRange->max_price ?? 1000);
                        $curMin = (int) ($validated['price_min'] ?? $absMin);
                        $curMax = (int) ($validated['price_max'] ?? $absMax);
                    @endphp
                    <div class="price-range-display">
                        <span id="displayMin">${{ number_format($curMin) }}</span>
                        <span id="displayMax">${{ number_format($curMax) }}</span>
                    </div>
                    <input type="range" id="rangeMin" name="price_min"
                           min="{{ $absMin }}" max="{{ $absMax }}"
                           value="{{ $curMin }}"
                           oninput="updateRange('min', this.value)"
                           style="margin-bottom:0.5rem;">
                    <input type="range" id="rangeMax" name="price_max"
                           min="{{ $absMin }}" max="{{ $absMax }}"
                           value="{{ $curMax }}"
                           oninput="updateRange('max', this.value)">
                    <button type="submit" class="btn btn-sm w-100 mt-2"
                            style="background:var(--brand-primary);color:#fff;border-radius:0.5rem;font-size:0.82rem;font-weight:600;">
                        Apply Price
                    </button>
                </div>
                @endif

                <a href="{{ route('hotels.index') }}" class="btn-clear-filters">
                    <i class="bi bi-x-circle me-1"></i> Reset all filters
                </a>
            </form>
        </aside>

        <!-- ══════════════════ RESULTS COLUMN ══════════════════ -->
        <section aria-label="Hotel results">
            <!-- Toolbar -->
            <div class="results-toolbar">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn-mobile-filter" id="openFilterDrawer" type="button" aria-label="Open filters">
                        <i class="bi bi-sliders"></i> Filters
                        @if($hasFilters)
                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.7rem;">ON</span>
                        @endif
                    </button>
                    <span class="results-count">
                        <strong>{{ $hotels->total() }}</strong> hotel{{ $hotels->total() !== 1 ? 's' : '' }} found
                        @if(!empty($validated['search']))
                            for "<em>{{ $validated['search'] }}</em>"
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sortSelect" class="text-muted" style="font-size:0.82rem; white-space:nowrap;">Sort by:</label>
                    <select id="sortSelect" class="sort-select form-select"
                            onchange="applySortChange(this.value)">
                        <option value="name_asc"    {{ ($validated['sort'] ?? 'name_asc') === 'name_asc'    ? 'selected' : '' }}>Name A–Z</option>
                        <option value="price_asc"   {{ ($validated['sort'] ?? '') === 'price_asc'   ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc"  {{ ($validated['sort'] ?? '') === 'price_desc'  ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="stars_desc"  {{ ($validated['sort'] ?? '') === 'stars_desc'  ? 'selected' : '' }}>Stars: Highest First</option>
                    </select>
                </div>
            </div>

            @if($hotels->isEmpty())
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-building-x"></i></div>
                    <h4>No hotels found</h4>
                    <p>
                        @if($hasFilters)
                            We couldn't find any hotels matching your current filters. Try adjusting your search or clearing some filters.
                        @else
                            No hotels are available at the moment. Please check back soon.
                        @endif
                    </p>
                    @if($hasFilters)
                        <a href="{{ route('hotels.index') }}" class="btn btn-accent">
                            <i class="bi bi-x-circle me-1"></i> Clear all filters
                        </a>
                    @endif
                </div>
            @else
                <!-- Hotel Grid -->
                <div class="row g-4">
                    @foreach($hotels as $hotel)
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                        <article class="hotel-card" aria-label="{{ $hotel->name }}">
                            <!-- Image -->
                            <div class="hotel-img-wrap">
                                @if($hotel->stars)
                                    <div class="hotel-stars-badge">
                                        <i class="bi bi-star-fill me-1"></i>{{ $hotel->stars }} Stars
                                    </div>
                                @endif
                                @if($hotel->image)
                                    <img src="{{ asset('storage/' . $hotel->image) }}"
                                         alt="{{ $hotel->name }}"
                                         loading="lazy"
                                         onerror="this.parentElement.innerHTML='<div class=\'hotel-img-placeholder\'><i class=\'bi bi-building\'></i></div>'">
                                @else
                                    <div class="hotel-img-placeholder">
                                        <i class="bi bi-building"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Body -->
                            <div class="hotel-body">
                                <a href="{{ route('hotels.show', $hotel) }}" class="hotel-name" title="{{ $hotel->name }}">
                                    {{ $hotel->name }}
                                </a>
                                <div class="hotel-location">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $hotel->city->name ?? '—' }}
                                    @if($hotel->address)
                                        &nbsp;·&nbsp; {{ \Illuminate\Support\Str::limit($hotel->address, 40) }}
                                    @endif
                                </div>

                                <!-- Star icons -->
                                @if($hotel->avg_rating)
                                <div class="hotel-rating mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= round($hotel->avg_rating) ? '-fill star-filled' : '' }}" style="font-size:0.78rem;"></i>
                                    @endfor
                                    <span class="ms-1">{{ $hotel->avg_rating }}</span>
                                    <span class="text-muted">({{ $hotel->reviews_count }})</span>
                                </div>
                                @endif

                                @if($hotel->description)
                                    <p class="hotel-description">{{ $hotel->description }}</p>
                                @else
                                    <div style="flex:1;"></div>
                                @endif

                                <!-- Footer -->
                                <div class="hotel-footer">
                                    <div>
                                        <div class="hotel-price">
                                            <sup>$</sup>{{ number_format($hotel->starting_price ?? $hotel->price_per_night, 2) }}
                                            <small>/ night</small>
                                        </div>
                                        @if(!$hotel->starting_price && $hotel->rooms()->count() === 0)
                                            <div style="font-size:0.75rem; color:var(--text-muted);">No rooms listed</div>
                                        @endif
                                    </div>
                                    <a href="{{ route('hotels.show', $hotel) }}" class="btn-view-hotel" aria-label="View {{ $hotel->name }}">
                                        View Hotel
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($hotels->hasPages())
                <div class="pagination-wrap d-flex justify-content-center">
                    {{ $hotels->links('pagination::bootstrap-5') }}
                </div>
                @endif
            @endif
        </section>
    </div>
</div>

<!-- ══════════════════════════════════════════
     Mobile Filter Drawer
════════════════════════════════════════════ -->
<div class="filter-drawer-overlay" id="filterDrawerOverlay"></div>
<div class="filter-drawer" id="filterDrawer" role="dialog" aria-label="Filter options" aria-modal="true">
    <div class="filter-drawer-handle"></div>
    <div class="filter-drawer-header">
        <h5>Filter Hotels</h5>
        <button type="button" id="closeFilterDrawer" class="btn-close" aria-label="Close filters"></button>
    </div>

    <!-- Mirror the sidebar form for mobile -->
    <form method="GET" action="{{ route('hotels.index') }}" id="mobileFilterForm">
        @if(!empty($validated['search']))
            <input type="hidden" name="search" value="{{ $validated['search'] }}">
        @endif
        @if(!empty($validated['sort']))
            <input type="hidden" name="sort" value="{{ $validated['sort'] }}">
        @endif

        <!-- Destination -->
        <p class="filter-section-title">Destination</p>
        <div class="mb-4">
            <select name="city_id" class="form-select mb-2">
                <option value="">All destinations</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ ($validated['city_id'] ?? '') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }} ({{ $city->hotels_count }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Stars -->
        <p class="filter-section-title">Star Rating</p>
        <div class="mb-4">
            <div class="row g-2">
                @for($s = 5; $s >= 1; $s--)
                <div class="col-auto">
                    <input type="radio" class="btn-check" name="stars" id="m_stars_{{ $s }}" value="{{ $s }}"
                           {{ ($validated['stars'] ?? '') == $s ? 'checked' : '' }}>
                    <label class="btn btn-sm btn-outline-secondary" for="m_stars_{{ $s }}" style="border-radius:0.5rem;">
                        {{ $s }}★
                    </label>
                </div>
                @endfor
            </div>
        </div>

        <!-- Price -->
        @if($priceRange && $priceRange->max_price > 0)
        <p class="filter-section-title">Price per Night</p>
        <div class="mb-4">
            <div class="price-range-display">
                <span id="mDisplayMin">${{ number_format($curMin ?? 0) }}</span>
                <span id="mDisplayMax">${{ number_format($curMax ?? $absMax) }}</span>
            </div>
            <input type="range" name="price_min" id="mRangeMin"
                   min="{{ $absMin }}" max="{{ $absMax }}"
                   value="{{ $curMin ?? $absMin }}"
                   oninput="document.getElementById('mDisplayMin').textContent='$'+parseInt(this.value).toLocaleString()">
            <input type="range" name="price_max" id="mRangeMax"
                   min="{{ $absMin }}" max="{{ $absMax }}"
                   value="{{ $curMax ?? $absMax }}"
                   oninput="document.getElementById('mDisplayMax').textContent='$'+parseInt(this.value).toLocaleString()"
                   style="margin-top:0.5rem;">
        </div>
        @endif

        <div class="d-grid gap-2 mt-3">
            <button type="submit" class="btn btn-accent py-2" style="border-radius:0.75rem;">
                <i class="bi bi-check2 me-1"></i> Apply Filters
            </button>
            <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary py-2" style="border-radius:0.75rem;">
                Reset
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
/* ── Sort: update URL param and reload ─────────────────────── */
function applySortChange(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

/* ── Price range display ───────────────────────────────────── */
function updateRange(which, val) {
    const minEl = document.getElementById('displayMin');
    const maxEl = document.getElementById('displayMax');
    const minInput = document.getElementById('rangeMin');
    const maxInput = document.getElementById('rangeMax');
    if (!minEl || !maxEl) return;

    let minVal = parseInt(minInput.value);
    let maxVal = parseInt(maxInput.value);

    if (which === 'min') {
        if (parseInt(val) > maxVal) { minInput.value = maxVal; val = maxVal; }
        minEl.textContent = '$' + parseInt(val).toLocaleString();
    } else {
        if (parseInt(val) < minVal) { maxInput.value = minVal; val = minVal; }
        maxEl.textContent = '$' + parseInt(val).toLocaleString();
    }
}

/* ── Mobile drawer ─────────────────────────────────────────── */
const overlay  = document.getElementById('filterDrawerOverlay');
const drawer   = document.getElementById('filterDrawer');
const openBtn  = document.getElementById('openFilterDrawer');
const closeBtn = document.getElementById('closeFilterDrawer');

function openDrawer() {
    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    overlay.classList.remove('open');
    drawer.classList.remove('open');
    document.body.style.overflow = '';
}

if (openBtn) openBtn.addEventListener('click', openDrawer);
if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
if (overlay) overlay.addEventListener('click', closeDrawer);

/* Swipe down to close */
let startY = 0;
if (drawer) {
    drawer.addEventListener('touchstart', e => { startY = e.touches[0].clientY; }, { passive: true });
    drawer.addEventListener('touchend', e => {
        if (e.changedTouches[0].clientY - startY > 60) closeDrawer();
    }, { passive: true });
}

/* Keyboard: Escape closes drawer */
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
@endpush
