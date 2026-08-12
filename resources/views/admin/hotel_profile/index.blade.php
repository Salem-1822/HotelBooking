@extends('admin.layouts.app')

@section('title', 'Hotel Profile')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
/* ════════════════════════════════════════════════════════════
   HOTEL PROFILE — Page Styles
   Design system tokens inherited from admin layout:
     --brand-primary, --brand-secondary, --brand-accent,
     --brand-accent-dark, --bg-body, --text-primary,
     --text-muted, --border-color, --card-shadow,
     --card-shadow-hover
   ════════════════════════════════════════════════════════════ */

/* ── Cover Banner ─────────────────────────────────────────── */
.hp-cover {
    position: relative;
    height: 300px;
    border-radius: 1.375rem;
    overflow: hidden;
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0F172A 100%);
    box-shadow: 0 8px 32px -8px rgba(15,23,42,0.25);
    margin-bottom: 1.5rem;
}
.hp-cover-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}
.hp-cover:hover .hp-cover-img { transform: scale(1.02); }
.hp-cover-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        170deg,
        rgba(15,23,42,0.10) 0%,
        rgba(15,23,42,0.55) 55%,
        rgba(15,23,42,0.85) 100%
    );
}
.hp-cover-body {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 2rem 2.25rem;
    gap: 0.5rem;
}

/* Cover edit button (top-right) */
.hp-cover-actions {
    position: absolute;
    top: 1.25rem;
    right: 1.5rem;
    z-index: 3;
    display: flex;
    gap: 0.5rem;
}
.btn-glass {
    background: rgba(255,255,255,0.14);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.22);
    color: #fff;
    border-radius: 0.625rem;
    font-size: 0.8125rem;
    font-weight: 600;
    padding: 0.45rem 0.9rem;
    transition: background 0.2s ease, transform 0.15s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    cursor: pointer;
}
.btn-glass:hover {
    background: rgba(255,255,255,0.24);
    color: #fff;
    transform: translateY(-1px);
}
.btn-glass-accent {
    background: rgba(212,175,55,0.25);
    border-color: rgba(212,175,55,0.4);
}
.btn-glass-accent:hover { background: rgba(212,175,55,0.4); }

/* Hotel name & meta in cover */
.hp-hotel-name {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    line-height: 1.15;
    letter-spacing: -0.02em;
    font-family: 'Poppins', sans-serif;
    text-shadow: 0 2px 12px rgba(0,0,0,0.35);
}
.hp-hotel-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.625rem;
}
.hp-meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 2rem;
    color: rgba(255,255,255,0.88);
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.3rem 0.75rem;
}
.hp-stars {
    display: inline-flex;
    gap: 1px;
    font-size: 1rem;
    color: var(--brand-accent);
    text-shadow: 0 1px 4px rgba(0,0,0,0.3);
}

/* ── Status badge ─────────────────────────────────────────── */
.badge-active {
    background: rgba(34,197,94,0.2);
    color: #16A34A;
    border: 1px solid rgba(34,197,94,0.3);
}
.badge-inactive {
    background: rgba(100,116,139,0.15);
    color: #64748B;
    border: 1px solid rgba(100,116,139,0.25);
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 0.35rem 0.875rem;
    border-radius: 2rem;
}
.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* ── Cover upload overlay ─────────────────────────────────── */
.hp-cover-upload-trigger {
    position: absolute;
    inset: 0;
    z-index: 4;
    cursor: pointer;
    opacity: 0;
}
.hp-cover-upload-hint {
    position: absolute;
    inset: 0;
    z-index: 3;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(15,23,42,0.55);
    opacity: 0;
    transition: opacity 0.25s ease;
    pointer-events: none;
    border-radius: inherit;
}
.hp-cover:hover .hp-cover-upload-hint { opacity: 1; }

/* ── Page Layout ──────────────────────────────────────────── */
.hp-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.5rem;
    align-items: start;
}
@media (max-width: 1199.98px) {
    .hp-grid { grid-template-columns: 1fr; }
}

/* ── Cards ────────────────────────────────────────────────── */
.hp-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 1.125rem;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.hp-card-head {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: #FAFBFC;
}
.hp-card-icon {
    width: 36px;
    height: 36px;
    border-radius: 0.625rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
    color: #fff;
}
.hp-card-icon-navy  { background: linear-gradient(135deg, #0F172A, #334155); }
.hp-card-icon-gold  { background: linear-gradient(135deg, #D4AF37, #B8860B); }
.hp-card-icon-blue  { background: linear-gradient(135deg, #3B82F6, #1D4ED8); }
.hp-card-icon-green { background: linear-gradient(135deg, #22C55E, #15803D); }
.hp-card-icon-purple{ background: linear-gradient(135deg, #8B5CF6, #6D28D9); }
.hp-card-title {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
}
.hp-card-subtitle {
    font-size: 0.72rem;
    color: var(--text-muted);
    margin-top: 1px;
}
.hp-card-body { padding: 1.5rem; }

/* ── Form elements ────────────────────────────────────────── */
.hp-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-primary);
    letter-spacing: 0.01em;
    margin-bottom: 0.375rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.hp-label i { color: var(--text-muted); font-size: 0.85rem; }
.hp-label .req { color: var(--brand-danger); }

.hp-input {
    width: 100%;
    border: 1.5px solid var(--border-color);
    border-radius: 0.625rem;
    font-size: 0.875rem;
    color: var(--text-primary);
    background: #fff;
    padding: 0.625rem 0.875rem;
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
    font-family: 'Inter', sans-serif;
}
.hp-input:focus {
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 3px rgba(15,23,42,0.07);
    outline: none;
}
.hp-input.is-invalid { border-color: var(--brand-danger); }
.hp-input::placeholder { color: #94A3B8; }

.hp-input-icon-wrap {
    position: relative;
}
.hp-input-icon-wrap .hp-input { padding-left: 2.5rem; }
.hp-input-icon-wrap .field-icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94A3B8;
    font-size: 0.9rem;
    pointer-events: none;
}
.hp-input-icon-wrap textarea.hp-input { padding-left: 0.875rem; }

.hp-field { margin-bottom: 1.125rem; }

/* ── Star rating widget ───────────────────────────────────── */
.hp-stars-picker {
    display: inline-flex;
    gap: 0.25rem;
}
.hp-star-btn {
    width: 42px;
    height: 42px;
    border: 1.5px solid var(--border-color);
    border-radius: 0.5rem;
    background: #fff;
    color: #CBD5E1;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    line-height: 1;
}
.hp-star-btn:hover,
.hp-star-btn.lit {
    border-color: var(--brand-accent);
    background: rgba(212,175,55,0.08);
    color: var(--brand-accent);
    transform: scale(1.05);
}
.hp-star-clear {
    width: 42px;
    height: 42px;
    border: 1.5px solid var(--border-color);
    border-radius: 0.5rem;
    background: #fff;
    color: #CBD5E1;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    margin-left: 0.25rem;
}
.hp-star-clear:hover { border-color: var(--brand-danger); color: var(--brand-danger); }

/* ── Read-only field (city) ───────────────────────────────── */
.hp-readonly {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.625rem 0.875rem;
    border: 1.5px solid var(--border-color);
    border-radius: 0.625rem;
    background: #F8FAFC;
    color: var(--text-muted);
    font-size: 0.875rem;
}
.hp-readonly .badge-lock {
    margin-left: auto;
    background: #F1F5F9;
    color: #94A3B8;
    font-size: 0.65rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    letter-spacing: .04em;
    white-space: nowrap;
}

/* ── Image upload zones ───────────────────────────────────── */
.hp-upload-zone {
    border: 2px dashed var(--border-color);
    border-radius: 0.875rem;
    padding: 1.5rem 1rem;
    text-align: center;
    background: #F8FAFC;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    position: relative;
    overflow: hidden;
}
.hp-upload-zone:hover {
    border-color: var(--brand-primary);
    background: #F1F5F9;
}
.hp-upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}
.hp-upload-zone-icon {
    font-size: 1.75rem;
    color: #CBD5E1;
    margin-bottom: 0.5rem;
    display: block;
    transition: color 0.2s;
}
.hp-upload-zone:hover .hp-upload-zone-icon { color: var(--brand-primary); }
.hp-upload-zone-text {
    font-size: 0.8125rem;
    color: var(--text-muted);
    line-height: 1.5;
}
.hp-upload-zone-text strong { color: var(--brand-primary); }

.hp-cover-preview {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    margin-top: 0.875rem;
    display: none;
}
.hp-cover-preview.show { display: block; }

/* Current image badge */
.hp-img-current {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: #16A34A;
    font-weight: 500;
    margin-top: 0.5rem;
}

/* ── Gallery ──────────────────────────────────────────────── */
.hp-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.625rem;
    margin-bottom: 0.875rem;
}
.hp-gallery-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 0.625rem;
    overflow: hidden;
    border: 1px solid var(--border-color);
    background: #F8FAFC;
}
.hp-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.2s ease;
}
.hp-gallery-item:hover img { transform: scale(1.05); }
.hp-gallery-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(239,68,68,0.9);
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.15s ease;
    padding: 0;
}
.hp-gallery-item:hover .hp-gallery-remove { opacity: 1; }
.hp-gallery-item.removing {
    opacity: 0.35;
    pointer-events: none;
}

/* ── Amenity chips ────────────────────────────────────────── */
.hp-amenity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 0.5rem;
}
.hp-amenity-chip {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.625rem 0.875rem;
    border: 1.5px solid var(--border-color);
    border-radius: 0.625rem;
    background: #fff;
    cursor: pointer;
    transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
    user-select: none;
    overflow: hidden;
}
.hp-amenity-chip input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.hp-amenity-chip-icon {
    width: 28px;
    height: 28px;
    border-radius: 0.4rem;
    background: #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    color: #94A3B8;
    flex-shrink: 0;
    transition: background 0.18s, color 0.18s;
}
.hp-amenity-chip-label {
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--text-muted);
    line-height: 1.2;
    transition: color 0.18s;
}
.hp-amenity-chip-check {
    margin-left: auto;
    width: 16px;
    height: 16px;
    border: 1.5px solid var(--border-color);
    border-radius: 4px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
    font-size: 0.55rem;
    color: transparent;
}
/* Checked state */
.hp-amenity-chip.is-checked {
    border-color: var(--brand-primary);
    background: #F0F4FF;
    box-shadow: 0 2px 8px rgba(15,23,42,0.07);
}
.hp-amenity-chip.is-checked .hp-amenity-chip-icon {
    background: var(--brand-primary);
    color: #fff;
}
.hp-amenity-chip.is-checked .hp-amenity-chip-label {
    color: var(--text-primary);
    font-weight: 600;
}
.hp-amenity-chip.is-checked .hp-amenity-chip-check {
    background: var(--brand-primary);
    border-color: var(--brand-primary);
    color: #fff;
}

/* ── Policy selects ───────────────────────────────────────── */
.hp-policy-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.875rem;
}
@media (max-width: 767.98px) {
    .hp-policy-row { grid-template-columns: 1fr; }
}
.hp-policy-card {
    border: 1.5px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1rem;
    background: #FAFBFC;
    transition: border-color 0.18s;
}
.hp-policy-card:focus-within { border-color: var(--brand-primary); }
.hp-policy-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.625rem;
}
.hp-policy-icon {
    width: 28px;
    height: 28px;
    border-radius: 0.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    color: #fff;
    flex-shrink: 0;
}
.hp-policy-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text-primary);
}

/* ── Time inputs ──────────────────────────────────────────── */
.hp-time-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.875rem;
}
@media (max-width: 575.98px) {
    .hp-time-row { grid-template-columns: 1fr; }
}
.hp-time-card {
    border: 1.5px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1rem 1.125rem;
    background: #FAFBFC;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    transition: border-color 0.18s;
}
.hp-time-card:focus-within { border-color: var(--brand-primary); }
.hp-time-icon {
    width: 36px;
    height: 36px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #fff;
    flex-shrink: 0;
}
.hp-time-input {
    border: none;
    background: transparent;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'Poppins', sans-serif;
    width: 100%;
    outline: none;
}

/* ── Save bar ─────────────────────────────────────────────── */
.hp-save-bar {
    background: rgba(255,255,255,0.96);
    backdrop-filter: blur(16px);
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    box-shadow: 0 4px 20px -4px rgba(15,23,42,0.1);
    position: sticky;
    bottom: 1rem;
    z-index: 50;
    margin-top: 1.5rem;
}
.hp-save-hint {
    font-size: 0.78rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.hp-save-hint i { color: var(--brand-success); }

/* ── Stat cards (right sidebar) ───────────────────────────── */
.hp-stat {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.875rem 0;
    border-bottom: 1px solid var(--border-color);
}
.hp-stat:last-child { border-bottom: none; padding-bottom: 0; }
.hp-stat:first-child { padding-top: 0; }
.hp-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 0.625rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.hp-stat-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--text-primary);
    font-family: 'Poppins', sans-serif;
    line-height: 1;
}
.hp-stat-label {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-top: 2px;
}

/* Occupancy bar */
.hp-occ-bar {
    height: 5px;
    border-radius: 5px;
    background: var(--border-color);
    overflow: hidden;
    margin-top: 0.375rem;
}
.hp-occ-fill {
    height: 100%;
    border-radius: 5px;
    background: linear-gradient(90deg, var(--brand-accent) 0%, #F59E0B 100%);
    transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Revenue highlight card */
.hp-revenue-card {
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1.25rem;
    border: none;
}
.hp-revenue-icon {
    width: 44px;
    height: 44px;
    border-radius: 0.75rem;
    background: rgba(212,175,55,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--brand-accent);
    flex-shrink: 0;
}
.hp-revenue-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    line-height: 1;
}
.hp-revenue-label {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-top: 3px;
}

/* Quick links */
.hp-quick-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0.875rem;
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    text-decoration: none;
    color: var(--text-primary);
    font-size: 0.8125rem;
    font-weight: 500;
    transition: all 0.18s ease;
    background: #fff;
    margin-bottom: 0.5rem;
}
.hp-quick-link:last-child { margin-bottom: 0; }
.hp-quick-link:hover {
    border-color: var(--brand-primary);
    background: #F8FAFC;
    color: var(--brand-primary);
    transform: translateX(3px);
}
.hp-quick-link i:first-child {
    width: 20px;
    text-align: center;
    font-size: 1rem;
}
.hp-quick-link .hp-link-arrow {
    margin-left: auto;
    color: var(--border-color);
    font-size: 0.8rem;
    transition: color 0.18s, transform 0.18s;
}
.hp-quick-link:hover .hp-link-arrow {
    color: var(--brand-primary);
    transform: translateX(2px);
}

/* Details list */
.hp-details-list { list-style: none; padding: 0; margin: 0; }
.hp-details-list li {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.625rem 0;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.8125rem;
}
.hp-details-list li:last-child { border-bottom: none; padding-bottom: 0; }
.hp-details-list .dt { color: var(--text-muted); font-weight: 500; white-space: nowrap; }
.hp-details-list .dd { color: var(--text-primary); font-weight: 600; text-align: right; }

/* ── Validation errors ────────────────────────────────────── */
.hp-field-error {
    font-size: 0.75rem;
    color: var(--brand-danger);
    margin-top: 0.3rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* ── Char counter ─────────────────────────────────────────── */
.hp-char-counter {
    font-size: 0.7rem;
    color: var(--text-muted);
    text-align: right;
    margin-top: 0.25rem;
}
.hp-char-counter.warn { color: var(--brand-warning); }
.hp-char-counter.over { color: var(--brand-danger); }

/* ── Section divider ──────────────────────────────────────── */
.hp-divider {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--text-muted);
    margin: 1.375rem 0 1.125rem;
}
.hp-divider::before, .hp-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border-color);
}

/* ── Validation alert ─────────────────────────────────────── */
.hp-alert-error {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-left: 4px solid var(--brand-danger);
    border-radius: 0.875rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
}
</style>
@endpush

@section('content')
@php
    $admin          = Auth::guard('admin')->user();
    $cityName       = $hotel->city?->name ?? '—';
    $selectedAmens  = $hotel->amenities ?? [];
    $gallery        = $hotel->gallery_images ?? [];
    $currentStars   = (int) ($hotel->stars ?? 0);
@endphp

{{-- ══════════════════════════════════════════════════════════
     COVER BANNER
     ══════════════════════════════════════════════════════════ --}}
<div class="hp-cover" id="hpCover">

    {{-- Background image --}}
    @if($hotel->image)
        <img class="hp-cover-img" id="hpCoverBg"
             src="{{ asset('storage/' . $hotel->image) }}" alt="Cover">
    @else
        <img class="hp-cover-img" id="hpCoverBg"
             src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1920&q=70"
             alt="Cover placeholder" style="opacity:0.2;">
    @endif

    <div class="hp-cover-overlay"></div>

    {{-- Hover upload hint --}}
    <div class="hp-cover-upload-hint">
        <div class="text-center text-white">
            <i class="bi bi-camera-fill fs-3 d-block mb-1"></i>
            <span style="font-size:0.82rem;font-weight:600;">Change Cover Photo</span>
        </div>
    </div>

    {{-- Glass action buttons --}}
    <div class="hp-cover-actions">
        <span class="status-pill {{ $hotel->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
            <span class="status-dot"
                  style="background:{{ $hotel->status === 'active' ? '#16A34A' : '#94A3B8' }};"></span>
            {{ ucfirst($hotel->status) }}
        </span>
        <label for="coverFileInput" class="btn-glass btn-glass-accent mb-0" style="cursor:pointer;">
            <i class="bi bi-camera-fill"></i> Change Cover
        </label>
    </div>

    {{-- Hotel identity --}}
    <div class="hp-cover-body">
        @if($currentStars > 0)
            <div class="hp-stars">
                @for($s = 1; $s <= 5; $s++)
                    <span style="{{ $s <= $currentStars ? 'opacity:1' : 'opacity:0.25' }}">★</span>
                @endfor
            </div>
        @endif

        <div class="hp-hotel-name">{{ $hotel->name }}</div>

        <div class="hp-hotel-meta">
            <span class="hp-meta-chip">
                <i class="bi bi-geo-alt-fill"></i> {{ $cityName }}
            </span>
            @if($hotel->address)
                <span class="hp-meta-chip">
                    <i class="bi bi-map"></i>
                    {{ Str::limit($hotel->address, 48) }}
                </span>
            @endif
            @if($hotel->phone)
                <span class="hp-meta-chip">
                    <i class="bi bi-telephone-fill"></i> {{ $hotel->phone }}
                </span>
            @endif
            <span class="hp-meta-chip">
                <i class="bi bi-clock"></i>
                Last updated {{ $hotel->updated_at->diffForHumans() }}
            </span>
        </div>
    </div>
</div>

{{-- ── Validation errors ─────────────────────────────────────────── --}}
@if ($errors->any())
    <div class="hp-alert-error mb-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-exclamation-circle-fill text-danger"></i>
            <strong style="font-size:0.875rem;color:#991B1B;">
                Please correct the following before saving:
            </strong>
        </div>
        <ul class="mb-0 ps-3" style="font-size:0.8125rem;color:#B91C1C;">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ══════════════════════════════════════════════════════════
     MAIN GRID
     ══════════════════════════════════════════════════════════ --}}
<div class="hp-grid">

    {{-- ─────────────────────────────────────────────────────
         LEFT — Edit Form
         ───────────────────────────────────────────────────── --}}
    <div>
        <form id="hpForm"
              action="{{ route('admin.hotel-profile.update') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Hidden cover input (triggered by label in banner) --}}
            <input type="file" id="coverFileInput" name="image"
                   accept="image/jpeg,image/png,image/webp"
                   class="d-none"
                   onchange="hpPreviewCover(this)">

            {{-- ══ CARD 1: Basic Info ════════════════════════════ --}}
            <div class="hp-card">
                <div class="hp-card-head">
                    <div class="hp-card-icon hp-card-icon-navy">
                        <i class="bi bi-building-fill"></i>
                    </div>
                    <div>
                        <div class="hp-card-title">Basic Information</div>
                        <div class="hp-card-subtitle">Public-facing hotel details</div>
                    </div>
                </div>
                <div class="hp-card-body">

                    {{-- Hotel Name --}}
                    <div class="hp-field">
                        <label for="hp_name" class="hp-label">
                            <i class="bi bi-type-bold"></i>
                            Hotel Name <span class="req">*</span>
                        </label>
                        <div class="hp-input-icon-wrap">
                            <i class="bi bi-building field-icon"></i>
                            <input type="text"
                                   id="hp_name"
                                   name="name"
                                   class="hp-input @error('name') is-invalid @enderror"
                                   value="{{ old('name', $hotel->name) }}"
                                   placeholder="e.g. Grand Palace Hotel"
                                   maxlength="255">
                        </div>
                        @error('name')
                            <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Star Rating --}}
                    <div class="hp-field">
                        <label class="hp-label">
                            <i class="bi bi-star-fill"></i>
                            Star Rating
                        </label>
                        <input type="hidden" name="stars" id="hpStarsVal"
                               value="{{ old('stars', $hotel->stars ?? '') }}">
                        <div class="d-flex align-items-center gap-1">
                            <div class="hp-stars-picker" id="hpStarsPicker">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            class="hp-star-btn {{ $currentStars >= $i ? 'lit' : '' }}"
                                            data-n="{{ $i }}"
                                            title="{{ $i }} Star{{ $i > 1 ? 's' : '' }}">
                                        ★
                                    </button>
                                @endfor
                            </div>
                            <button type="button" id="hpStarsClear" class="hp-star-clear" title="Clear">
                                <i class="bi bi-x"></i>
                            </button>
                            <span id="hpStarsLabel" class="ms-2"
                                  style="font-size:0.8rem;color:var(--text-muted);font-weight:500;">
                                @if($currentStars > 0)
                                    {{ $currentStars }}-Star Hotel
                                @else
                                    Not rated
                                @endif
                            </span>
                        </div>
                        @error('stars')
                            <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="hp-field mb-0">
                        <label for="hp_description" class="hp-label">
                            <i class="bi bi-text-paragraph"></i>
                            Description
                        </label>
                        <textarea id="hp_description"
                                  name="description"
                                  class="hp-input @error('description') is-invalid @enderror"
                                  rows="4"
                                  maxlength="3000"
                                  placeholder="Describe what makes your hotel unique — location, atmosphere, services, and special offerings…">{{ old('description', $hotel->description) }}</textarea>
                        <div class="hp-char-counter" id="hpDescCounter">
                            <span id="hpDescCount">{{ strlen(old('description', $hotel->description ?? '')) }}</span> / 3000
                        </div>
                        @error('description')
                            <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ══ CARD 2: Location & Contact ════════════════════ --}}
            <div class="hp-card">
                <div class="hp-card-head">
                    <div class="hp-card-icon hp-card-icon-blue">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <div class="hp-card-title">Location & Contact</div>
                        <div class="hp-card-subtitle">Address and contact information</div>
                    </div>
                </div>
                <div class="hp-card-body">

                    {{-- City (read-only) --}}
                    <div class="hp-field">
                        <label class="hp-label">
                            <i class="bi bi-pin-map-fill"></i>
                            City
                        </label>
                        <div class="hp-readonly">
                            <i class="bi bi-geo-alt text-muted"></i>
                            <span>{{ $cityName }}</span>
                            <span class="badge-lock">
                                <i class="bi bi-lock-fill me-1"></i>Super Admin
                            </span>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="hp-field">
                        <label for="hp_address" class="hp-label">
                            <i class="bi bi-signpost-fill"></i>
                            Full Address <span class="req">*</span>
                        </label>
                        <div class="hp-input-icon-wrap">
                            <i class="bi bi-map field-icon"></i>
                            <input type="text"
                                   id="hp_address"
                                   name="address"
                                   class="hp-input @error('address') is-invalid @enderror"
                                   value="{{ old('address', $hotel->address) }}"
                                   placeholder="Street, District, ZIP Code"
                                   maxlength="500">
                        </div>
                        @error('address')
                            <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone & Email side-by-side --}}
                    <div class="row g-3 mb-0">
                        <div class="col-12 col-sm-6">
                            <label for="hp_phone" class="hp-label">
                                <i class="bi bi-telephone-fill"></i>
                                Phone
                            </label>
                            <div class="hp-input-icon-wrap">
                                <i class="bi bi-telephone field-icon"></i>
                                <input type="tel"
                                       id="hp_phone"
                                       name="phone"
                                       class="hp-input @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $hotel->phone) }}"
                                       placeholder="+1 555 000 0000"
                                       maxlength="50">
                            </div>
                            @error('phone')
                                <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="hp_email" class="hp-label">
                                <i class="bi bi-envelope-fill"></i>
                                Email
                            </label>
                            <div class="hp-input-icon-wrap">
                                <i class="bi bi-envelope field-icon"></i>
                                <input type="email"
                                       id="hp_email"
                                       name="email"
                                       class="hp-input @error('email') is-invalid @enderror"
                                       value="{{ old('email', $hotel->email) }}"
                                       placeholder="contact@hotel.com"
                                       maxlength="255">
                            </div>
                            @error('email')
                                <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="mt-4 mb-4" style="border-color: var(--border-color); opacity: 1;">

                    {{-- Map Location --}}
                    <div class="hp-field mb-0">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div>
                                <label class="hp-label mb-1">
                                    <i class="bi bi-crosshair"></i>
                                    Map Location
                                </label>
                                <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0;">
                                    Click on the map to set your hotel's exact location. This will be used by clients for navigation.
                                </p>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2 mb-1" id="btn-use-my-location" style="border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600;">
                                    <i class="bi bi-geo-alt-fill"></i> 
                                    @if(old('latitude', $hotel->latitude) && old('longitude', $hotel->longitude))
                                        Update My Location
                                    @else
                                        Use My Current Location
                                    @endif
                                </button>
                                <div id="location-status-msg" style="font-size: 0.7rem; font-weight: 600;" 
                                     class="{{ old('latitude', $hotel->latitude) ? 'text-success' : 'text-danger' }}">
                                    @if(old('latitude', $hotel->latitude) && old('longitude', $hotel->longitude))
                                        <i class="bi bi-check-circle-fill"></i> Hotel location selected
                                    @else
                                        Hotel location not set
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div id="hotelMap" style="height: 300px; border-radius: 0.75rem; border: 1.5px solid var(--border-color); z-index: 1;"></div>
                        
                        <input type="hidden" id="hp_latitude" name="latitude" value="{{ old('latitude', $hotel->latitude) }}">
                        <input type="hidden" id="hp_longitude" name="longitude" value="{{ old('longitude', $hotel->longitude) }}">
                        
                        @error('latitude')
                            <div class="hp-field-error mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                        @error('longitude')
                            <div class="hp-field-error mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ══ CARD 3: Cover Image & Gallery ════════════════ --}}
            <div class="hp-card">
                <div class="hp-card-head">
                    <div class="hp-card-icon hp-card-icon-purple">
                        <i class="bi bi-images"></i>
                    </div>
                    <div>
                        <div class="hp-card-title">Photos & Media</div>
                        <div class="hp-card-subtitle">JPG, PNG or WebP — max 5 MB each</div>
                    </div>
                </div>
                <div class="hp-card-body">

                    {{-- Cover Image --}}
                    <div class="hp-field">
                        <label class="hp-label">
                            <i class="bi bi-image-fill"></i>
                            Cover Photo
                        </label>
                        <div class="hp-upload-zone" id="coverDropZone"
                             onclick="document.getElementById('coverFileInput2').click()">
                            <input type="file" id="coverFileInput2" name="image"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="d-none"
                                   onchange="hpPreviewCover(this)">
                            <i class="bi bi-cloud-arrow-up hp-upload-zone-icon"></i>
                            <div class="hp-upload-zone-text">
                                <strong>Click to upload</strong> or drag & drop a cover image<br>
                                <span style="font-size:0.75rem;">Recommended size: 1920 × 600 px</span>
                            </div>
                        </div>
                        @if($hotel->image)
                            <div class="hp-img-current">
                                <i class="bi bi-check-circle-fill"></i> Current cover saved
                            </div>
                        @endif
                        <img id="hpCoverPreview" class="hp-cover-preview
                            @if($hotel->image) show @endif"
                            @if($hotel->image) src="{{ asset('storage/' . $hotel->image) }}" @endif
                            alt="Cover preview">
                        @error('image')
                            <div class="hp-field-error mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Gallery Images --}}
                    <div class="hp-field mb-0">
                        <label class="hp-label">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            Gallery Images
                            @if(!empty($gallery))
                                <span style="color:var(--text-muted);font-weight:400;">
                                    ({{ count($gallery) }} saved)
                                </span>
                            @endif
                        </label>

                        {{-- Existing gallery --}}
                        @if(!empty($gallery))
                            <div class="hp-gallery" id="hpExistingGallery">
                                @foreach($gallery as $idx => $img)
                                    <div class="hp-gallery-item" id="hpGallItem{{ $idx }}">
                                        <img src="{{ asset('storage/' . $img) }}"
                                             alt="Gallery {{ $idx + 1 }}">
                                        <button type="button"
                                                class="hp-gallery-remove"
                                                onclick="hpRemoveGallery({{ $idx }})"
                                                title="Remove image">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                        <input type="hidden"
                                               name="remove_gallery[]"
                                               id="hpRemFlag{{ $idx }}"
                                               value="{{ $idx }}"
                                               disabled>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Upload new --}}
                        <div class="hp-upload-zone mt-2">
                            <input type="file" name="gallery_images[]"
                                   id="hpGalleryInput"
                                   accept="image/jpeg,image/png,image/webp"
                                   multiple
                                   onchange="hpPreviewGallery(this)">
                            <i class="bi bi-plus-circle hp-upload-zone-icon"></i>
                            <div class="hp-upload-zone-text">
                                <strong>Add gallery photos</strong><br>
                                <span style="font-size:0.75rem;">Select multiple images at once</span>
                            </div>
                        </div>
                        <div class="hp-gallery mt-2" id="hpNewGallery"></div>

                        @error('gallery_images.*')
                            <div class="hp-field-error mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ══ CARD 4: Amenities ═════════════════════════════ --}}
            <div class="hp-card">
                <div class="hp-card-head">
                    <div class="hp-card-icon hp-card-icon-gold">
                        <i class="bi bi-stars"></i>
                    </div>
                    <div>
                        <div class="hp-card-title">Amenities & Facilities</div>
                        <div class="hp-card-subtitle">Select everything your hotel offers</div>
                    </div>
                </div>
                <div class="hp-card-body">
                    <div class="hp-amenity-grid">
                        @foreach($amenitiesOptions as $key => $info)
                            @php $isChecked = in_array($key, $selectedAmens); @endphp
                            <label class="hp-amenity-chip {{ $isChecked ? 'is-checked' : '' }}"
                                   for="amen_{{ $key }}">
                                <input type="checkbox"
                                       id="amen_{{ $key }}"
                                       name="amenities[]"
                                       value="{{ $key }}"
                                       {{ $isChecked ? 'checked' : '' }}>
                                <div class="hp-amenity-chip-icon">
                                    <i class="bi {{ $info['icon'] }}"></i>
                                </div>
                                <span class="hp-amenity-chip-label">{{ $info['label'] }}</span>
                                <div class="hp-amenity-chip-check">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ══ CARD 5: Policies & House Rules ═══════════════ --}}
            <div class="hp-card">
                <div class="hp-card-head">
                    <div class="hp-card-icon hp-card-icon-green">
                        <i class="bi bi-clipboard2-check-fill"></i>
                    </div>
                    <div>
                        <div class="hp-card-title">Policies & House Rules</div>
                        <div class="hp-card-subtitle">Shown to guests before booking</div>
                    </div>
                </div>
                <div class="hp-card-body">

                    {{-- Check-in / Check-out --}}
                    <div class="hp-field">
                        <label class="hp-label">
                            <i class="bi bi-clock-history"></i>
                            Check-in & Check-out Times
                        </label>
                        <div class="hp-time-row">
                            <div class="hp-time-card">
                                <div class="hp-time-icon"
                                     style="background:linear-gradient(135deg,#22C55E,#15803D);">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:2px;">
                                        Check-in
                                    </div>
                                    <input type="time"
                                           id="hp_check_in"
                                           name="check_in_time"
                                           class="hp-time-input @error('check_in_time') is-invalid @enderror"
                                           value="{{ old('check_in_time', $hotel->check_in_time) }}">
                                </div>
                            </div>
                            <div class="hp-time-card">
                                <div class="hp-time-icon"
                                     style="background:linear-gradient(135deg,#F59E0B,#D97706);">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:2px;">
                                        Check-out
                                    </div>
                                    <input type="time"
                                           id="hp_check_out"
                                           name="check_out_time"
                                           class="hp-time-input @error('check_out_time') is-invalid @enderror"
                                           value="{{ old('check_out_time', $hotel->check_out_time) }}">
                                </div>
                            </div>
                        </div>
                        @error('check_in_time')
                            <div class="hp-field-error mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                        @error('check_out_time')
                            <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Children / Pets / Smoking --}}
                    <div class="hp-field">
                        <label class="hp-label">
                            <i class="bi bi-shield-check"></i>
                            Guest Policies
                        </label>
                        <div class="hp-policy-row">
                            {{-- Children --}}
                            <div class="hp-policy-card">
                                <div class="hp-policy-header">
                                    <div class="hp-policy-icon"
                                         style="background:linear-gradient(135deg,#3B82F6,#1D4ED8);">
                                        <i class="bi bi-people-fill" style="font-size:0.8rem;"></i>
                                    </div>
                                    <div class="hp-policy-label">Children</div>
                                </div>
                                <select name="children_policy"
                                        class="hp-input @error('children_policy') is-invalid @enderror"
                                        style="padding:0.5rem 0.75rem;font-size:0.8rem;">
                                    <option value="">— Select —</option>
                                    @foreach($policies['children'] as $opt)
                                        <option value="{{ $opt }}"
                                            {{ old('children_policy', $hotel->children_policy) === $opt ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('children_policy')
                                    <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pets --}}
                            <div class="hp-policy-card">
                                <div class="hp-policy-header">
                                    <div class="hp-policy-icon"
                                         style="background:linear-gradient(135deg,#EC4899,#BE185D);">
                                        <i class="bi bi-heart-fill" style="font-size:0.8rem;"></i>
                                    </div>
                                    <div class="hp-policy-label">Pets</div>
                                </div>
                                <select name="pets_policy"
                                        class="hp-input @error('pets_policy') is-invalid @enderror"
                                        style="padding:0.5rem 0.75rem;font-size:0.8rem;">
                                    <option value="">— Select —</option>
                                    @foreach($policies['pets'] as $opt)
                                        <option value="{{ $opt }}"
                                            {{ old('pets_policy', $hotel->pets_policy) === $opt ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pets_policy')
                                    <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Smoking --}}
                            <div class="hp-policy-card">
                                <div class="hp-policy-header">
                                    <div class="hp-policy-icon"
                                         style="background:linear-gradient(135deg,#64748B,#334155);">
                                        <i class="bi bi-slash-circle" style="font-size:0.8rem;"></i>
                                    </div>
                                    <div class="hp-policy-label">Smoking</div>
                                </div>
                                <select name="smoking_policy"
                                        class="hp-input @error('smoking_policy') is-invalid @enderror"
                                        style="padding:0.5rem 0.75rem;font-size:0.8rem;">
                                    <option value="">— Select —</option>
                                    @foreach($policies['smoking'] as $opt)
                                        <option value="{{ $opt }}"
                                            {{ old('smoking_policy', $hotel->smoking_policy) === $opt ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('smoking_policy')
                                    <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Cancellation Policy --}}
                    <div class="hp-field mb-0">
                        <label for="hp_cancel" class="hp-label">
                            <i class="bi bi-x-circle-fill"></i>
                            Cancellation Policy
                        </label>
                        <textarea id="hp_cancel"
                                  name="cancellation_policy"
                                  class="hp-input @error('cancellation_policy') is-invalid @enderror"
                                  rows="3"
                                  maxlength="1000"
                                  placeholder="e.g. Free cancellation up to 48 hours before check-in. After that, the first night is charged.">{{ old('cancellation_policy', $hotel->cancellation_policy) }}</textarea>
                        @error('cancellation_policy')
                            <div class="hp-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ── Save Bar ──────────────────────────────────────── --}}
            <div class="hp-save-bar">
                <div class="hp-save-hint">
                    <i class="bi bi-shield-check-fill"></i>
                    All changes are saved to the database immediately.
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.dashboard') }}"
                       class="btn btn-light border"
                       style="border-radius:0.625rem;font-size:0.875rem;font-weight:600;">
                        Cancel
                    </a>
                    <button type="submit" id="hpSaveBtn" class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-2"></i>Save Changes
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- ─────────────────────────────────────────────────────
         RIGHT — Admin Sidebar
         ───────────────────────────────────────────────────── --}}
    <div>

        {{-- ══ STATS CARD ═══════════════════════════════════ --}}
        <div class="hp-card mb-0">
            <div class="hp-card-head">
                <div class="hp-card-icon hp-card-icon-gold">
                    <i class="bi bi-bar-chart-line-fill"></i>
                </div>
                <div>
                    <div class="hp-card-title">Hotel Statistics</div>
                    <div class="hp-card-subtitle">Admin only · Not visible to guests</div>
                </div>
            </div>
            <div class="hp-card-body" style="padding-top:1.125rem;">

                {{-- Rooms overview --}}
                <div class="hp-stat">
                    <div class="hp-stat-icon" style="background:#EFF6FF;color:#3B82F6;">
                        <i class="bi bi-door-open-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="hp-stat-value">{{ $totalRooms }}</div>
                        <div class="hp-stat-label">Total Rooms</div>
                    </div>
                    <div class="text-end" style="font-size:0.75rem;">
                        <div style="color:#16A34A;font-weight:600;">
                            <i class="bi bi-circle-fill" style="font-size:0.45rem;vertical-align:middle;"></i>
                            {{ $availableRooms }} avail.
                        </div>
                        <div style="color:#F59E0B;font-weight:600;">
                            <i class="bi bi-circle-fill" style="font-size:0.45rem;vertical-align:middle;"></i>
                            {{ $occupiedRooms }} occ.
                        </div>
                        @if($maintenanceRooms > 0)
                            <div style="color:#EF4444;font-weight:600;">
                                <i class="bi bi-circle-fill" style="font-size:0.45rem;vertical-align:middle;"></i>
                                {{ $maintenanceRooms }} maint.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Occupancy rate --}}
                <div class="hp-stat">
                    <div class="hp-stat-icon" style="background:#FEF9C3;color:#D4AF37;">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="hp-stat-value">{{ $occupancyRate }}%</div>
                        <div class="hp-stat-label">Occupancy Rate</div>
                        <div class="hp-occ-bar">
                            <div class="hp-occ-fill" style="width:{{ $occupancyRate }}%;"></div>
                        </div>
                    </div>
                </div>

                {{-- Reservations --}}
                <div class="hp-stat">
                    <div class="hp-stat-icon" style="background:#F5F3FF;color:#8B5CF6;">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div>
                        <div class="hp-stat-value">{{ number_format($totalReservations) }}</div>
                        <div class="hp-stat-label">Total Reservations</div>
                    </div>
                </div>

                {{-- Customers --}}
                <div class="hp-stat">
                    <div class="hp-stat-icon" style="background:#F0FDF4;color:#22C55E;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="hp-stat-value">{{ number_format($totalCustomers) }}</div>
                        <div class="hp-stat-label">Unique Guests</div>
                    </div>
                </div>

                {{-- Reviews --}}
                <div class="hp-stat">
                    <div class="hp-stat-icon" style="background:#FEF3C7;color:#F59E0B;">
                        <i class="bi bi-star-half"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="hp-stat-value">
                            {{ number_format($totalReviews) }}
                            @if($avgRating)
                                <span style="font-size:0.85rem;font-weight:600;color:var(--brand-accent);margin-left:4px;">
                                    ★ {{ $avgRating }}
                                </span>
                            @endif
                        </div>
                        <div class="hp-stat-label">Reviews{{ $avgRating ? ' · Avg Rating' : '' }}</div>
                    </div>
                </div>

                {{-- Monthly Revenue highlight --}}
                <div class="hp-revenue-card">
                    <div class="hp-revenue-icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="hp-revenue-label">This Month's Revenue</div>
                        <div class="hp-revenue-value">${{ number_format($monthlyRevenue, 0) }}</div>
                        @if($totalRevenue > 0)
                            <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);margin-top:2px;">
                                All-time: ${{ number_format($totalRevenue, 0) }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- ══ HOTEL DETAILS ════════════════════════════════ --}}
        <div class="hp-card mt-4">
            <div class="hp-card-head">
                <div class="hp-card-icon hp-card-icon-navy">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <div class="hp-card-title">Hotel Details</div>
                    <div class="hp-card-subtitle">System information</div>
                </div>
            </div>
            <div class="hp-card-body" style="padding-top:1rem;">
                <ul class="hp-details-list">
                    <li>
                        <span class="dt">Hotel ID</span>
                        <span class="dd">
                            <code style="background:#F1F5F9;padding:1px 7px;border-radius:4px;font-size:0.78rem;">
                                #{{ $hotel->id }}
                            </code>
                        </span>
                    </li>
                    <li>
                        <span class="dt">Status</span>
                        <span class="dd">
                            <span class="status-pill {{ $hotel->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                <span class="status-dot"
                                      style="background:{{ $hotel->status === 'active' ? '#16A34A' : '#94A3B8' }};"></span>
                                {{ ucfirst($hotel->status) }}
                            </span>
                        </span>
                    </li>
                    <li>
                        <span class="dt">City</span>
                        <span class="dd">{{ $cityName }}</span>
                    </li>
                    @if($currentStars > 0)
                    <li>
                        <span class="dt">Star Rating</span>
                        <span class="dd" style="color:var(--brand-accent);">
                            @for($s = 1; $s <= $currentStars; $s++)★@endfor
                            <span style="color:var(--text-muted);font-size:0.78rem;">({{ $currentStars }}★)</span>
                        </span>
                    </li>
                    @endif
                    @if(!empty($selectedAmens))
                    <li>
                        <span class="dt">Amenities</span>
                        <span class="dd">{{ count($selectedAmens) }} selected</span>
                    </li>
                    @endif
                    @if(!empty($gallery))
                    <li>
                        <span class="dt">Gallery</span>
                        <span class="dd">{{ count($gallery) }} photo{{ count($gallery) !== 1 ? 's' : '' }}</span>
                    </li>
                    @endif
                    <li>
                        <span class="dt">Created</span>
                        <span class="dd">{{ $hotel->created_at->format('M d, Y') }}</span>
                    </li>
                    <li>
                        <span class="dt">Last Updated</span>
                        <span class="dd">{{ $hotel->updated_at->format('M d, Y · H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ══ ADMIN INFO ═══════════════════════════════════ --}}
        <div class="hp-card mt-4">
            <div class="hp-card-head">
                <div class="hp-card-icon hp-card-icon-gold">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="hp-card-title">Account</div>
                    <div class="hp-card-subtitle">Signed in as</div>
                </div>
            </div>
            <div class="hp-card-body" style="padding-top:1rem;">
                <div class="d-flex align-items-center gap-3">
                    @if($admin->profile_image && \Storage::disk('public')->exists('profiles/' . $admin->profile_image))
                        <img src="{{ asset('storage/profiles/' . $admin->profile_image) }}"
                             style="width:46px;height:46px;border-radius:0.75rem;object-fit:cover;border:2px solid var(--border-color);"
                             alt="{{ $admin->name }}">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=0F172A&color=fff&size=80"
                             style="width:46px;height:46px;border-radius:0.75rem;" alt="">
                    @endif
                    <div>
                        <div style="font-weight:700;font-size:0.875rem;color:var(--text-primary);">
                            {{ $admin->name }}
                        </div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $admin->email }}</div>
                        <div class="mt-1">
                            <span style="display:inline-block;padding:2px 10px;border-radius:20px;
                                         background:rgba(212,175,55,0.12);color:#92700A;
                                         font-size:0.67rem;font-weight:700;letter-spacing:.05em;text-transform:capitalize;">
                                {{ str_replace('_', ' ', $admin->role) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ QUICK ACTIONS ════════════════════════════════ --}}
        <div class="hp-card mt-4">
            <div class="hp-card-head">
                <div class="hp-card-icon hp-card-icon-navy">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div>
                    <div class="hp-card-title">Quick Actions</div>
                </div>
            </div>
            <div class="hp-card-body" style="padding-top:1rem;">
                <a href="{{ route('admin.rooms.index') }}" class="hp-quick-link">
                    <i class="bi bi-door-open-fill" style="color:#3B82F6;"></i>
                    Manage Rooms
                    <i class="bi bi-chevron-right hp-link-arrow"></i>
                </a>
                <a href="{{ route('admin.reservations.index') }}" class="hp-quick-link">
                    <i class="bi bi-calendar-check-fill" style="color:#8B5CF6;"></i>
                    Reservations
                    <i class="bi bi-chevron-right hp-link-arrow"></i>
                </a>
                <a href="{{ route('admin.customers.index') }}" class="hp-quick-link">
                    <i class="bi bi-people-fill" style="color:#22C55E;"></i>
                    Customers
                    <i class="bi bi-chevron-right hp-link-arrow"></i>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="hp-quick-link">
                    <i class="bi bi-graph-up-arrow" style="color:#D4AF37;"></i>
                    Reports
                    <i class="bi bi-chevron-right hp-link-arrow"></i>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="hp-quick-link">
                    <i class="bi bi-grid-1x2-fill" style="color:#0F172A;"></i>
                    Dashboard
                    <i class="bi bi-chevron-right hp-link-arrow"></i>
                </a>
            </div>
        </div>

    </div>{{-- end right col --}}
</div>{{-- end hp-grid --}}

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
/* ════════════════════════════════════════════════════════════
   HOTEL PROFILE — Page Scripts
   ════════════════════════════════════════════════════════════ */

// ── Cover image preview (updates both banner + card preview) ──
function hpPreviewCover(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const src = e.target.result;
        // Update banner background
        const bannerImg = document.getElementById('hpCoverBg');
        if (bannerImg) { bannerImg.src = src; bannerImg.style.opacity = '0.55'; }
        // Update card preview
        const prev = document.getElementById('hpCoverPreview');
        if (prev) { prev.src = src; prev.classList.add('show'); }
        // Sync the other hidden input so only one file is submitted
        syncCoverInputs(input);
    };
    reader.readAsDataURL(input.files[0]);
}

function syncCoverInputs(source) {
    // There are two cover inputs (banner label + card upload zone).
    // Sync them so the form only submits the chosen file once.
    // We rely on the name="image" — only the last non-empty one counts.
    // Actually both have name="image"; the browser sends the last filled one.
    // Just update the other's label visually; no extra sync needed.
}

// ── Gallery: remove existing ──────────────────────────────────
function hpRemoveGallery(idx) {
    const item = document.getElementById('hpGallItem' + idx);
    const flag = document.getElementById('hpRemFlag' + idx);
    if (item) item.classList.add('removing');
    if (flag) flag.disabled = false;
}

// ── Gallery: preview new uploads ─────────────────────────────
function hpPreviewGallery(input) {
    const container = document.getElementById('hpNewGallery');
    container.innerHTML = '';
    if (!input.files) return;
    Array.from(input.files).forEach(function(file, i) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'hp-gallery-item';
            div.innerHTML = '<img src="' + e.target.result + '" alt="New ' + (i+1) + '">';
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// ── Star rating widget ────────────────────────────────────────
(function() {
    const btns   = document.querySelectorAll('.hp-star-btn[data-n]');
    const input  = document.getElementById('hpStarsVal');
    const clear  = document.getElementById('hpStarsClear');
    const label  = document.getElementById('hpStarsLabel');
    const labels = ['', '1-Star Hotel', '2-Star Hotel', '3-Star Hotel', '4-Star Hotel', '5-Star Hotel'];

    function setStars(n) {
        btns.forEach(function(b) {
            b.classList.toggle('lit', parseInt(b.dataset.n) <= n);
        });
        input.value = n || '';
        label.textContent = labels[n] || 'Not rated';
    }

    btns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const n = parseInt(this.dataset.n);
            setStars(input.value == n ? 0 : n);
        });
    });
    if (clear) {
        clear.addEventListener('click', function() { setStars(0); });
    }
})();

// ── Amenity chip toggle ───────────────────────────────────────
document.querySelectorAll('.hp-amenity-chip input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        this.closest('.hp-amenity-chip').classList.toggle('is-checked', this.checked);
    });
});

// ── Description character counter ─────────────────────────────
(function() {
    const area  = document.getElementById('hp_description');
    const cnt   = document.getElementById('hpDescCount');
    const wrap  = document.getElementById('hpDescCounter');
    if (!area || !cnt) return;
    area.addEventListener('input', function() {
        const len = this.value.length;
        cnt.textContent = len;
        wrap.classList.toggle('warn', len > 2700);
        wrap.classList.toggle('over', len > 3000);
    });
})();

// ── Form submit: loading state ────────────────────────────────
(function() {
    const form = document.getElementById('hpForm');
    const btn  = document.getElementById('hpSaveBtn');
    if (!form || !btn) return;
    form.addEventListener('submit', function() {
        btn.disabled = true;
        btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving…';
    });
})();

// ── Cover drop zone: drag-over highlight ──────────────────────
(function() {
    const zone = document.getElementById('coverDropZone');
    if (!zone) return;
    zone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--brand-primary)';
        this.style.background  = '#F1F5F9';
    });
    zone.addEventListener('dragleave', function() {
        this.style.borderColor = '';
        this.style.background  = '';
    });
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '';
        this.style.background  = '';
        const files = e.dataTransfer.files;
        if (files.length) {
            const fi = document.getElementById('coverFileInput2');
            // Programmatic file setting via DataTransfer
            try {
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                fi.files = dt.files;
                hpPreviewCover(fi);
            } catch(err) {}
        }
    });
})();

// ── Hotel Location Map (Leaflet) ──────────────────────────────
(function() {
    const mapContainer = document.getElementById('hotelMap');
    if (!mapContainer) return;

    const latInput = document.getElementById('hp_latitude');
    const lngInput = document.getElementById('hp_longitude');

    let initialLat = parseFloat(latInput.value) || 0;
    let initialLng = parseFloat(lngInput.value) || 0;
    let zoomLevel = (initialLat && initialLng) ? 15 : 2;

    const map = L.map('hotelMap').setView([initialLat, initialLng], zoomLevel);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker;
    if (initialLat && initialLng) {
        marker = L.marker([initialLat, initialLng]).addTo(map);
    }

    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }

        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
        updateLocationStatus(true);
    });
    
    function updateLocationStatus(isSelected) {
        const statusMsg = document.getElementById('location-status-msg');
        if (statusMsg) {
            if (isSelected) {
                statusMsg.className = 'text-success';
                statusMsg.innerHTML = '<i class="bi bi-check-circle-fill"></i> Hotel location selected';
            } else {
                statusMsg.className = 'text-danger';
                statusMsg.innerHTML = 'Hotel location not set';
            }
        }
        
        const btnMyLocation = document.getElementById('btn-use-my-location');
        if (btnMyLocation && isSelected) {
            btnMyLocation.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Update My Location';
        }
    }
    
    // Use My Current Location
    const btnMyLocation = document.getElementById('btn-use-my-location');
    if (btnMyLocation) {
        btnMyLocation.addEventListener('click', function() {
            if ("geolocation" in navigator) {
                const originalText = btnMyLocation.innerHTML;
                btnMyLocation.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width: 1rem; height: 1rem;"></span> Locating...';
                btnMyLocation.disabled = true;

                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    map.setView([lat, lng], 15);

                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng]).addTo(map);
                    }

                    latInput.value = lat.toFixed(8);
                    lngInput.value = lng.toFixed(8);
                    updateLocationStatus(true);

                    btnMyLocation.disabled = false;
                }, function(error) {
                    let msg = "Unable to determine your current location. Please try again.";
                    if (error.code === error.PERMISSION_DENIED) {
                        msg = "Location permission was denied. Please allow location access in your browser settings and try again.";
                    }
                    alert(msg);
                    btnMyLocation.innerHTML = originalText;
                    btnMyLocation.disabled = false;
                });
            } else {
                alert("Your browser does not support location detection.");
            }
        });
    }

    // Fix leaflet map display within hidden or flex containers
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
})();

</script>
@endpush
