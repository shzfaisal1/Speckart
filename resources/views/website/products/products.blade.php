@extends('website.layout.master')

@section('content')

{{-- ═══════════════════════════════════════════════════
     SPECKART — Premium Product Catalog & Filter UI
     Lenskart-Inspired · Compact · Responsive
═══════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ══════════════════════════════════════
   CATALOG TOKENS & BASE STYLES
══════════════════════════════════════ */
:root {
    --cat-primary: #329a9a;
    --cat-primary-dark: #277878;
    --cat-primary-soft: #eef8f8;
    --cat-green: #16a34a;
    --cat-green-soft: #f0fdf4;
    --cat-amber: #d97706;
    --cat-amber-soft: #fffbeb;
    --cat-text: #111827;
    --cat-text-secondary: #4b5563;
    --cat-text-muted: #9ca3af;
    --cat-border: #e5e7eb;
    --cat-bg: #f8fafc;
    --cat-card: #ffffff;
    --cat-radius: 12px;
    --cat-radius-sm: 8px;
    --cat-shadow: 0 1px 3px rgba(0,0,0,0.05);
    --cat-shadow-hover: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
    --cat-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.catalog-page, .catalog-page * {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    box-sizing: border-box;
}

.catalog-page {
    background: var(--cat-bg);
    min-height: 100vh;
    padding-bottom: 30px;
}

/* ══════════════════════════════════════
   CUSTOM CATALOG CONTAINER (FLUID WIDE)
══════════════════════════════════════ */
.cat-container {
    width: 100%;
    max-width: 1440px;
    padding-left: 24px;
    padding-right: 24px;
    margin-left: auto;
    margin-right: auto;
    box-sizing: border-box;
}

@media (min-width: 1600px) {
    .cat-container {
        max-width: 1560px;
        padding-left: 32px;
        padding-right: 32px;
    }
}

@media (min-width: 1920px) {
    .cat-container {
        max-width: 1720px;
        padding-left: 40px;
        padding-right: 40px;
    }
}

@media (max-width: 767px) {
    .cat-container {
        padding-left: 12px;
        padding-right: 12px;
    }
}

/* ══════════════════════════════════════
   BREADCRUMBS & TOP STRIP (COMPACT)
══════════════════════════════════════ */
.cat-breadcrumb-wrap {
    background: #ffffff;
    border-bottom: 1px solid var(--cat-border);
    padding: 10px 0;
}
.cat-breadcrumb {
    margin-bottom: 0;
    font-size: 12.5px;
    font-weight: 500;
}
.cat-breadcrumb a {
    color: var(--cat-text-secondary);
    text-decoration: none;
    transition: var(--cat-transition);
}
.cat-breadcrumb a:hover {
    color: var(--cat-primary);
}
.cat-breadcrumb .breadcrumb-item.active {
    color: var(--cat-text);
    font-weight: 600;
}

.cat-header-strip {
    background: #ffffff;
    border-bottom: 1px solid var(--cat-border);
    padding: 12px 0;
}
.cat-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--cat-text);
    letter-spacing: -0.3px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.cat-count-badge {
    background: var(--cat-primary-soft);
    color: var(--cat-primary-dark);
    font-size: 11.5px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 20px;
    border: 1px solid #cceaea;
}

/* Search bar */
.cat-search-bar {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid var(--cat-border);
    border-radius: 30px;
    overflow: hidden;
    height: 38px;
    min-width: 250px;
    box-shadow: var(--cat-shadow);
    transition: var(--cat-transition);
}
.cat-search-bar:focus-within {
    border-color: var(--cat-primary);
    box-shadow: 0 0 0 3px rgba(50, 154, 154, 0.15);
}
.cat-search-bar input {
    border: none;
    outline: none;
    padding: 0 14px;
    font-size: 12.5px;
    color: var(--cat-text);
    flex: 1;
    height: 100%;
    background: transparent;
}
.cat-search-bar input::placeholder {
    color: var(--cat-text-muted);
}
.cat-search-bar button {
    background: var(--cat-primary);
    color: #ffffff;
    border: none;
    padding: 0 16px;
    font-size: 12.5px;
    font-weight: 700;
    height: 100%;
    cursor: pointer;
    transition: var(--cat-transition);
}
.cat-search-bar button:hover {
    background: var(--cat-primary-dark);
}

/* ══════════════════════════════════════
   PREMIUM SORT DROPDOWN
══════════════════════════════════════ */
.cat-sort-btn {
    background: #ffffff;
    border: 1.5px solid var(--cat-border);
    border-radius: 50px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    color: var(--cat-text);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: var(--cat-shadow);
    transition: var(--cat-transition);
}

.cat-sort-btn:hover,
.cat-sort-btn[aria-expanded="true"] {
    border-color: var(--cat-primary);
    color: var(--cat-primary);
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(50, 154, 154, 0.15);
}

.cat-sort-btn i {
    font-size: 13px;
    color: var(--cat-primary);
}

.cat-sort-dropdown-menu {
    background: #ffffff !important;
    border: 1px solid var(--cat-border) !important;
    border-radius: 14px !important;
    padding: 6px !important;
    min-width: 205px !important;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08) !important;
}

.cat-sort-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 9px 14px !important;
    border-radius: 9px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: var(--cat-text) !important;
    transition: all 0.18s ease !important;
    cursor: pointer !important;
}

.cat-sort-item:hover {
    background: #f8fafc !important;
    color: var(--cat-primary) !important;
}

.cat-sort-item.active,
.cat-sort-item:active {
    background: var(--cat-primary-soft) !important;
    color: var(--cat-primary-dark) !important;
    font-weight: 700 !important;
}

.cat-sort-item .active-check-icon {
    display: none;
    font-size: 15px;
    color: var(--cat-primary);
}

.cat-sort-item.active .active-check-icon {
    display: inline-block;
}

/* ══════════════════════════════════════
   FILTER SIDEBAR (DESKTOP)
══════════════════════════════════════ */
.filter-sidebar {
    background: #ffffff;
    border-radius: var(--cat-radius);
    border: 1px solid var(--cat-border);
    box-shadow: var(--cat-shadow);
    position: sticky;
    top: 140px;
    max-height: calc(100vh - 160px);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
.filter-sidebar::-webkit-scrollbar { width: 4px; }
.filter-sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.filter-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    position: sticky;
    top: 0;
    background: #ffffff;
    z-index: 10;
}
.filter-top-bar h6 {
    font-size: 14px;
    font-weight: 800;
    color: var(--cat-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
    letter-spacing: -0.2px;
}
.filter-top-bar h6 i { color: var(--cat-primary); font-size: 15px; }

.filter-reset-btn {
    font-size: 11px;
    font-weight: 700;
    color: var(--cat-primary);
    text-decoration: none;
    background: var(--cat-primary-soft);
    padding: 3px 10px;
    border-radius: 20px;
    transition: var(--cat-transition);
    border: 1px solid #cceaea;
}
.filter-reset-btn:hover {
    background: var(--cat-primary);
    color: #ffffff;
    border-color: var(--cat-primary);
}

.active-chips-bar {
    padding: 8px 16px 0;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.active-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--cat-primary-soft);
    color: var(--cat-primary-dark);
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    cursor: pointer;
    border: 1px solid #cceaea;
    transition: var(--cat-transition);
}
.active-chip:hover {
    background: var(--cat-primary);
    color: #ffffff;
}

.filter-section { border-bottom: 1px solid #f3f4f6; }
.filter-section:last-child { border-bottom: none; }
.filter-section-btn {
    width: 100%;
    background: none;
    border: none;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 700;
    color: var(--cat-text);
    cursor: pointer;
    transition: background 0.15s;
    text-align: left;
}
.filter-section-btn:hover { background: #fafafa; }
.filter-section-btn i.chevron { font-size: 11px; color: var(--cat-text-muted); transition: transform 0.25s; }
.filter-section-btn.open i.chevron { transform: rotate(180deg); }
.filter-section-body { padding: 2px 16px 12px; display: none; }
.filter-section-body.open { display: block; }

.brand-search-wrap { position: relative; margin-bottom: 8px; }
.brand-search-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--cat-text-muted); font-size: 12px; }
.brand-search-input {
    width: 100%; padding: 6px 10px 6px 28px; border: 1px solid var(--cat-border);
    border-radius: 8px; font-size: 11.5px; color: var(--cat-text); outline: none; transition: border 0.2s;
}
.brand-search-input:focus { border-color: var(--cat-primary); box-shadow: 0 0 0 2px rgba(50, 154, 154, 0.12); }

.icon-filter-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; }
.icon-filter-item {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 6px 3px; border: 1px solid var(--cat-border); border-radius: 8px; cursor: pointer;
    transition: var(--cat-transition); background: #ffffff; text-align: center;
    font-size: 10px; font-weight: 600; color: var(--cat-text-secondary); gap: 3px; line-height: 1.15;
}
.icon-filter-item img { width: 32px; height: 20px; object-fit: contain; }
.icon-filter-item:hover { border-color: var(--cat-primary); background: var(--cat-primary-soft); color: var(--cat-primary); }
.icon-filter-item.active-filter { border-color: var(--cat-primary); background: var(--cat-primary-soft); color: var(--cat-primary-dark); font-weight: 700; box-shadow: 0 2px 6px rgba(50,154,154,0.15); }

.filter-checkbox-item {
    display: flex; align-items: center; gap: 8px; padding: 4px 0;
    cursor: pointer; font-size: 12px; color: var(--cat-text-secondary); font-weight: 500; transition: color 0.15s;
    user-select: none;
}
.filter-checkbox-item:hover { color: var(--cat-primary); }
.filter-checkbox-item input { width: 14px; height: 14px; accent-color: var(--cat-primary); cursor: pointer; flex-shrink: 0; border-radius: 4px; }
.filter-checkbox-item .color-dot { width: 13px; height: 13px; border-radius: 50%; border: 1px solid #d1d5db; flex-shrink: 0; }

.gender-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5px; }
.gender-pill {
    display: flex; align-items: center; justify-content: center; gap: 4px;
    padding: 6px 4px; border: 1px solid var(--cat-border); border-radius: 8px; cursor: pointer;
    font-size: 11px; font-weight: 600; color: var(--cat-text-secondary); background: #ffffff;
    transition: var(--cat-transition); user-select: none;
}
.gender-pill:hover { border-color: var(--cat-primary); color: var(--cat-primary); background: var(--cat-primary-soft); }
.gender-pill.active-filter { border-color: var(--cat-primary); background: var(--cat-primary-soft); color: var(--cat-primary-dark); font-weight: 700; }

/* Offcanvas Backdrop & Layering (Correctly below drawers and above header) */
.offcanvas-backdrop.show {
    z-index: 1040 !important;
    opacity: 0.5 !important;
}

/* ══════════════════════════════════════
   MOBILE FILTER BAR & DRAWER
══════════════════════════════════════ */
.mobile-filter-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #ffffff;
    border-bottom: 1px solid var(--cat-border);
    position: sticky !important;
    top: 105px !important;
    z-index: 10 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.mobile-filter-trigger-btn, .mobile-sort-trigger-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid var(--cat-border);
    border-radius: 8px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 700;
    color: var(--cat-text);
    cursor: pointer;
    box-shadow: var(--cat-shadow);
}
.mobile-filter-trigger-btn i, .mobile-sort-trigger-btn i {
    color: var(--cat-primary);
    font-size: 13px;
}
.mobile-filter-badge {
    background: var(--cat-primary);
    color: #ffffff;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 20px;
    font-weight: 800;
}

.mobile-filter-offcanvas {
    width: 85% !important;
    max-width: 360px !important;
    border-radius: 0 16px 16px 0 !important;
    z-index: 1045 !important;
    overflow-x: hidden !important;
    border: none !important;
}
.mobile-filter-offcanvas .offcanvas-header {
    padding: 14px 16px !important;
    background: #ffffff;
    border-bottom: 1px solid var(--cat-border);
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    border-top-right-radius: 16px !important;
}
.mobile-filter-offcanvas .offcanvas-title {
    font-size: 16px !important;
    font-weight: 800 !important;
    color: var(--cat-text) !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    line-height: 1 !important;
}
.mobile-filter-offcanvas .btn-close {
    margin: 0 !important;
    padding: 4px !important;
    opacity: 0.6;
    transition: opacity 0.2s;
    background-size: 12px;
}
.mobile-filter-offcanvas .btn-close:hover {
    opacity: 1;
}
.mobile-filter-offcanvas .offcanvas-body {
    padding: 0;
    overflow-y: auto;
    overflow-x: hidden;
    background: #ffffff;
}
.mobile-filter-offcanvas .filter-sidebar {
    position: static !important;
    top: 0 !important;
    max-height: none !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    overflow: visible !important;
    background: transparent !important;
    padding: 0 !important;
}
.mobile-filter-offcanvas .offcanvas-footer {
    position: sticky;
    bottom: 0;
    background: #ffffff;
    z-index: 10;
    padding: 12px 16px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
.btn-apply-mobile-filters {
    width: 100% !important;
    box-sizing: border-box !important;
    background: linear-gradient(135deg, var(--cat-primary), var(--cat-primary-dark));
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 11px;
    font-size: 13.5px;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(50, 154, 154, 0.3);
    transition: var(--cat-transition);
}
.btn-apply-mobile-filters:hover {
    box-shadow: 0 6px 18px rgba(50, 154, 154, 0.4);
}

/* ══════════════════════════════════════
   PRODUCT CARDS (Smooth Premium Hover)
══════════════════════════════════════ */
.product-card {
    position: relative;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: transform 0.22s cubic-bezier(0.165, 0.84, 0.44, 1), 
                box-shadow 0.22s cubic-bezier(0.165, 0.84, 0.44, 1), 
                border-color 0.2s ease;
    /* height: 100%; */
    display: flex;
    flex-direction: column;
}
.product-card:hover {
    /* transform: translateY(-0px); */
    box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.08), 0 4px 12px -2px rgba(50, 154, 154, 0.05);
    border-color: #329a9a;
}

.product-card-link {
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
    text-decoration: none !important;
    color: inherit;
    flex-grow: 1;
}

.wishlist-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 5;
    transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(229, 231, 235, 0.8);
}
.wishlist-btn:hover {
    transform: scale(1.08);
    background: #ffffff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}
.wishlist-btn i { font-size: 14px; color: #9ca3af; transition: color 0.2s ease; }
.wishlist-btn:hover i { color: #ef4444; }
.wishlist-btn i.bi-heart-fill { color: #ef4444 !important; }

/* Pure White Clean Background for Image so Product Photos Blend Seamlessly! */
.product-image {
    position: relative;
    background: #ffffff;
    padding: 8px 12px 2px 12px;
    text-align: center;
    height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
/* Prevent duplicate img-hover fading from external.css */
.product-card .img-default {
    opacity: 1 !important;
}
.product-card .img-hover {

    display: none !important;
}

.product-image img {
    max-height: 110px;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    transition: transform 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.product-card:hover .product-image img {
    transform: translateY(-4px) scale(1.04);
}

.product-info {
    padding: 6px 12px 0px 12px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #ffffff;
}
.brand-name {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    color: #329a9a;
    margin-bottom: 2px;
}
.product-title {
    font-size: 13px;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
    min-height: 34px;
}
.product-card:hover .product-title {
    color: #277878;
}

.size-rating {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    color: #6b7280;
    margin-bottom: 6px;
}
.size-text span {
    font-weight: 600;
    color: #374151;
}
.rating {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-weight: 700;
    font-size: 10.5px;
    color: #1f2937;
    background: #fffbeb;
    border: 1px solid #fef3c7;
    padding: 1.5px 6px;
    border-radius: 20px;
}
.rating i { color: #f59e0b; font-size: 10.5px; }

.price-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 6px;
    border-top: 1px dashed #e5e7eb;
}
.price-section .price {
    font-size: 16px;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.4px;
}
.try-btn {
    background: linear-gradient(135deg, #329a9a, #277878);
    color: #ffffff;
    border: none;
    border-radius: 7px;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    box-shadow: 0 2px 5px rgba(50, 154, 154, 0.2);
}
.try-btn:hover {
    background: linear-gradient(135deg, #3bb3b3, #1f6565);
    box-shadow: 0 4px 10px rgba(50, 154, 154, 0.35);
    transform: translateY(-1px);
}
.price-section .price {
    font-size: 16px;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.4px;
}
.try-btn {
    background: linear-gradient(135deg, #329a9a, #277878);
    color: #ffffff;
    border: none;
    border-radius: 7px;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    box-shadow: 0 2px 5px rgba(50, 154, 154, 0.2);
}
.try-btn:hover {
    background: linear-gradient(135deg, #3bb3b3, #1f6565);
    box-shadow: 0 4px 10px rgba(50, 154, 154, 0.35);
    transform: translateY(-1px);
}

/* ══════════════════════════════════════
   PAGINATION STYLING
══════════════════════════════════════ */
.modern-pagination {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    padding: 6px 14px;
    border-radius: 40px;
    box-shadow: var(--cat-shadow);
    border: 1px solid var(--cat-border);
    margin: 0;
}
.modern-pagination .page-item { margin: 0; list-style: none; }
.modern-pagination .page-link {
    width: 34px; height: 34px; border-radius: 50% !important;
    display: flex; align-items: center; justify-content: center;
    font-size: 12.5px; font-weight: 600; color: var(--cat-text-secondary); background: #ffffff;
    border: 1px solid #f3f4f6;
    transition: var(--cat-transition);
    text-decoration: none;
}
.modern-pagination .page-link:hover {
    background: var(--cat-primary-soft); color: var(--cat-primary); border-color: var(--cat-primary);
}
.modern-pagination .page-item.active .page-link {
    background: var(--cat-primary) !important; color: #ffffff !important;
    border-color: var(--cat-primary) !important; font-weight: 700;
    box-shadow: 0 4px 10px rgba(50, 154, 154, 0.3) !important;
}
.modern-pagination .page-item.disabled .page-link {
    opacity: 0.4; cursor: not-allowed; pointer-events: none; background: #f8fafc;
}
</style>

<div class="catalog-page">

    {{-- BREADCRUMBS SECTION --}}
    <div class="cat-breadcrumb-wrap">
        <div class="cat-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb cat-breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products') }}">Products</a></li>
                    @if($activeCategory ?? null)
                        <li class="breadcrumb-item active" aria-current="page">{{ $activeCategory->name }}</li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">All Eyewear</li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>

    {{-- MOBILE FILTER ACTION BAR (Visible on < 992px) --}}
    <div class="mobile-filter-bar d-flex d-lg-none">
        <button class="mobile-filter-trigger-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterOffcanvas">
            <i class="bi bi-sliders"></i> Filters <span class="mobile-filter-badge d-none" id="mobile-filter-badge">0</span>
        </button>
        <div class="dropdown flex-grow-1">
            <button class="mobile-sort-trigger-btn w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-arrow-down-up me-1"></i> <span class="sort-current-text">Popularity</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end cat-sort-dropdown-menu border-0 shadow-lg mt-2 w-100">
                <li><a class="dropdown-item sort-item cat-sort-item active" data-sort=""><span>Popularity</span><i class="bi bi-check2 active-check-icon"></i></a></li>
                <li><a class="dropdown-item sort-item cat-sort-item" data-sort="newest"><span>Newest First</span><i class="bi bi-check2 active-check-icon"></i></a></li>
                <li><a class="dropdown-item sort-item cat-sort-item" data-sort="price_low"><span>Price: Low to High</span><i class="bi bi-check2 active-check-icon"></i></a></li>
                <li><a class="dropdown-item sort-item cat-sort-item" data-sort="price_high"><span>Price: High to Low</span><i class="bi bi-check2 active-check-icon"></i></a></li>
            </ul>
        </div>
    </div>

    {{-- MOBILE OFFCANVAS FILTER DRAWER --}}
    <div class="offcanvas offcanvas-start mobile-filter-offcanvas" tabindex="-1" id="mobileFilterOffcanvas" aria-labelledby="mobileFilterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileFilterOffcanvasLabel">
                <i class="bi bi-sliders text-teal" style="color:var(--cat-primary);"></i> Filters
            </h5>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('products', ($activeCategory ?? null) ? ['category' => $activeCategory->slug] : []) }}" class="filter-reset-btn">Clear All</a>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        </div>
        <div class="offcanvas-body p-0">
            <div class="filter-sidebar border-0 shadow-none sticky-top-0 rounded-0">
                @include('website.products.filter_sections')
            </div>
        </div>
        <div class="offcanvas-footer p-3 border-top bg-white">
            <button type="button" class="btn-apply-mobile-filters" data-bs-dismiss="offcanvas" id="mobile-apply-btn">
                Apply Filters ({{ $productsList->total() }} Products)
            </button>
        </div>
    </div>

    {{-- DESKTOP CATEGORY HEADER & SEARCH STRIP --}}
    <section class="cat-header-strip d-none d-lg-block">
        <div class="cat-container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="cat-title">
                        {{ ($activeCategory ?? null) ? $activeCategory->name : 'All Eyewear Products' }}
                        <span class="cat-count-badge">
                            {{ $productsList->total() }} items
                        </span>
                    </h4>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="cat-search-bar">
                        <input type="text" id="search-input" placeholder="Search frames, brands, styles…" autocomplete="off" value="{{ request('search') }}">
                        <button type="button" id="search-btn"><i class="bi bi-search"></i></button>
                    </div>
                    <div class="dropdown">
                        <button class="cat-sort-btn dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-arrow-down-up me-1"></i> <span class="sort-current-text">Popularity</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end cat-sort-dropdown-menu border-0 shadow-lg mt-2">
                            <li><a class="dropdown-item sort-item cat-sort-item active" data-sort=""><span>Popularity</span><i class="bi bi-check2 active-check-icon"></i></a></li>
                            <li><a class="dropdown-item sort-item cat-sort-item" data-sort="newest"><span>Newest First</span><i class="bi bi-check2 active-check-icon"></i></a></li>
                            <li><a class="dropdown-item sort-item cat-sort-item" data-sort="price_low"><span>Price: Low to High</span><i class="bi bi-check2 active-check-icon"></i></a></li>
                            <li><a class="dropdown-item sort-item cat-sort-item" data-sort="price_high"><span>Price: High to Low</span><i class="bi bi-check2 active-check-icon"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- MAIN CATALOG GRID SECTION --}}
    <section class="py-3">
        <div class="cat-container">
            <div class="row g-3">

                {{-- DESKTOP FILTER SIDEBAR (Visible on lg screens >= 992px) --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="filter-sidebar">
                        <div class="filter-top-bar">
                            <h6><i class="bi bi-sliders"></i> Filters</h6>
                            <a href="{{ route('products', ($activeCategory ?? null) ? ['category' => $activeCategory->slug] : []) }}"
                               class="filter-reset-btn">Clear All</a>
                        </div>
                        <div class="active-chips-bar" id="active-chips-bar"></div>
                        @include('website.products.filter_sections')
                    </div>
                </div>

                {{-- PRODUCTS GRID CONTAINER --}}
                <div class="col-lg-9 col-md-12">
                    <div id="product-grid-wrapper">
                        @include('website.products.product_grid')
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Toggle filter section open/close
    $(document).on('click', '.filter-section-btn', function() {
        $(this).toggleClass('open');
        const targetId = $(this).attr('data-target');
        if (targetId) {
            $('[id="' + targetId + '"]').toggleClass('open');
        }
    });

    // Brand Search Filter
    $(document).on('input', '.brand-search-input', function() {
        const q = $(this).val().toLowerCase();
        $('.brand-item').each(function() {
            const txt = $(this).text().toLowerCase();
            $(this).toggle(txt.includes(q));
        });
    });

    // Filter Items Click Listeners
    $(document).on('click', '.icon-filter-item', function() {
        $(this).toggleClass('active-filter');
        fetchFilteredProducts();
    });

    $(document).on('click', '.gender-pill', function() {
        $(this).toggleClass('active-filter');
        fetchFilteredProducts();
    });

    $(document).on('change', '.filter-checkbox', function() {
        fetchFilteredProducts();
    });

    // Sort Item Click Handler
    $(document).on('click', '.sort-item', function(e) {
        e.preventDefault();
        const sortVal = $(this).data('sort');
        const sortLabel = $(this).find('span').text() || $(this).text();
        
        $('.sort-item').removeClass('active');
        $(`.sort-item[data-sort="${sortVal}"]`).addClass('active');
        $('.sort-current-text').text(sortLabel);
        fetchFilteredProducts();
    });

    // Search Input Enter or Search Button Click
    $('#search-btn').on('click', function() {
        fetchFilteredProducts();
    });
    $('#search-input').on('keyup', function(e) {
        if (e.key === 'Enter') fetchFilteredProducts();
    });

    // AJAX Pagination click handler
    $(document).on('click', '.modern-pagination a.page-link', function(e) {
        e.preventDefault();
        const pageUrl = $(this).attr('href');
        if (!pageUrl || pageUrl === '#') return;

        fetchFilteredProducts(pageUrl);

        // Smooth scroll up to top of catalog section
        $('html, body').animate({
            scrollTop: $('.catalog-page').offset().top - 70
        }, 300);
    });

    // Fetch Filtered Products AJAX
    function fetchFilteredProducts(targetUrl = null) {
        let requestUrl = targetUrl;

        if (!requestUrl) {
            const params = new URLSearchParams();
            let totalCheckedCount = 0;

            ['frame_type', 'shape', 'brand', 'gender', 'occasion', 'age', 'color', 'material', 'size', 'modality'].forEach(key => {
                const vals = [];
                document.querySelectorAll(`.icon-filter-item[data-filter="${key}"].active-filter`).forEach(el => {
                    const v = el.getAttribute('data-value');
                    if (!vals.includes(v)) vals.push(v);
                });
                document.querySelectorAll(`.gender-pill[data-filter="${key}"].active-filter`).forEach(el => {
                    const v = el.getAttribute('data-value');
                    if (!vals.includes(v)) vals.push(v);
                });
                document.querySelectorAll(`.filter-checkbox[data-filter="${key}"]:checked`).forEach(el => {
                    const v = el.getAttribute('data-value');
                    if (!vals.includes(v)) vals.push(v);
                });

                if (vals.length > 0) {
                    params.append(key, vals.join(','));
                    totalCheckedCount += vals.length;
                }
            });

            // Update mobile filter badge
            const $mobileBadge = $('#mobile-filter-badge');
            if (totalCheckedCount > 0) {
                $mobileBadge.text(totalCheckedCount).removeClass('d-none');
            } else {
                $mobileBadge.addClass('d-none').text(0);
            }

            const searchVal = document.getElementById('search-input')?.value;
            if (searchVal) params.append('search', searchVal);

            const activeSort = $('.sort-item.active').data('sort');
            if (activeSort) params.append('sort', activeSort);

            const currentCategory = "{{ ($activeCategory ?? null) ? $activeCategory->slug : '' }}";
            if (currentCategory) params.append('category', currentCategory);

            requestUrl = "{{ route('products') }}?" + params.toString();
        }

        fetch(requestUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('product-grid-wrapper').innerHTML = html;
        })
        .catch(err => console.error(err));
    }
});
</script>
@endsection
