@extends('website.layout.master')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════════════
     PREMIUM MY ORDERS — SPECKART
     Consistent with My Addresses, Prescriptions & Account Dashboard UI
══════════════════════════════════════════════════════════════════════════════ --}}

<!-- Icon CDNs (Bootstrap Icons + FontAwesome 6) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ==========================================================================
   PREMIUM ORDERS DASHBOARD STYLES (MATCHING SPECKARTS ACCOUNT UI)
   ========================================================================== */
:root {
    --order-primary: #07484A;
    --order-primary-dark: #032729;
    --order-teal: #00B9B9;
    --order-teal-light: #e6f9f9;
    --order-gold: #fbbf24;
    --order-bg: #f8fafc;
    --order-card-bg: #ffffff;
    --order-border: #e2e8f0;
    --order-text-main: #0f172a;
    --order-text-muted: #64748b;
}

.orders-page-section {
    background: linear-gradient(180deg, #f0f7f7 0%, #f8fafc 180px, #f8fafc 100%);
    min-height: 85vh;
    padding: 30px 0 70px;
    font-family: 'Poppins', sans-serif;
}

/* ── Breadcrumb Navigation ── */
.order-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--order-text-muted);
    margin-bottom: 22px;
}

.order-breadcrumb a {
    color: var(--order-text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.order-breadcrumb a:hover {
    color: var(--order-primary);
}

.order-breadcrumb i {
    font-size: 11px;
    color: #94a3b8;
}

.order-breadcrumb .active {
    color: var(--order-primary);
    font-weight: 600;
}

/* ── Hero Top Header Banner ── */
.order-hero-banner {
    background: linear-gradient(135deg, #07484A 0%, #0a5658 50%, #0c676a 100%);
    border-radius: 20px;
    padding: 28px 32px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 36px -10px rgba(7, 72, 74, 0.25);
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.order-hero-banner::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -30px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0, 185, 185, 0.25) 0%, transparent 70%);
    pointer-events: none;
}

.order-hero-title h2 {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 4px;
    letter-spacing: -0.2px;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
}

.order-hero-title p {
    font-size: 13.5px;
    color: #ccfbf1;
    margin: 0;
}

.btn-order-shop {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #07484A !important;
    font-weight: 700;
    font-size: 13.5px;
    padding: 11px 22px;
    border-radius: 12px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 18px rgba(245, 158, 11, 0.3);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    cursor: pointer;
}

.btn-order-shop:hover {
    background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(245, 158, 11, 0.4);
    color: #032b2d !important;
}

/* ── Filter Tabs & Search Controls ── */
.order-controls-bar {
    background: #ffffff;
    border: 1.5px solid var(--order-border);
    border-radius: 16px;
    padding: 12px 18px;
    box-shadow: 0 6px 20px rgba(7, 72, 74, 0.03);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.order-filter-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
}
.order-filter-pills::-webkit-scrollbar { display: none; }

.order-pill-btn {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    padding: 7px 15px;
    border-radius: 50px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.order-pill-btn:hover {
    border-color: var(--order-teal);
    color: var(--order-teal);
    background: var(--order-teal-light);
}

.order-pill-btn.active {
    background: var(--order-primary);
    border-color: var(--order-primary);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(7, 72, 74, 0.18);
}

.order-pill-count {
    background: rgba(0, 0, 0, 0.08);
    font-size: 11px;
    padding: 1px 7px;
    border-radius: 12px;
}
.order-pill-btn.active .order-pill-count {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

.order-search-box {
    position: relative;
    min-width: 260px;
}

.order-search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
}

.order-search-input {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px 12px 8px 34px;
    font-size: 13px;
    color: var(--order-text-main);
    outline: none;
    transition: all 0.2s;
}

.order-search-input:focus {
    background: #ffffff;
    border-color: var(--order-teal);
    box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.12);
}

/* ── Order Card Component ── */
.order-box-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1.5px solid var(--order-border);
    box-shadow: 0 8px 24px rgba(7, 72, 74, 0.04);
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
    margin-bottom: 22px;
}

.order-box-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 36px rgba(7, 72, 74, 0.1);
    border-color: rgba(0, 185, 185, 0.6);
}

.order-card-top-accent {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3.5px;
    background: linear-gradient(90deg, #07484A, #00B9B9);
}

/* Card Header */
.order-card-header {
    background: #fafcfc;
    border-bottom: 1px solid var(--order-border);
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.order-meta-cells {
    display: flex;
    align-items: center;
    gap: 28px;
    flex-wrap: wrap;
}

.order-meta-cell {
    display: flex;
    flex-direction: column;
}

.order-meta-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--order-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}

.order-meta-val {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--order-text-main);
}

/* Status Badges */
.order-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.2px;
}

.order-status-badge.delivered {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #059669;
}

.order-status-badge.transit {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #2563eb;
}

.order-status-badge.processing {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #d97706;
}

.order-status-badge.cancelled {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

/* Card Body */
.order-card-body {
    padding: 20px 24px;
}

.order-product-row {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}
.order-product-row:first-child { padding-top: 0; }
.order-product-row:last-child { padding-bottom: 0; border-bottom: none; }

.order-prod-thumb {
    width: 88px;
    height: 70px;
    background: #f8fafc;
    border: 1px solid rgba(7, 72, 74, 0.08);
    border-radius: 12px;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.order-prod-thumb img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.2s ease;
}
.order-box-card:hover .order-prod-thumb img {
    transform: scale(1.06);
}

.order-prod-details {
    flex: 1;
}

.order-prod-brand {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--order-teal);
    margin-bottom: 2px;
}

.order-prod-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--order-text-main);
    margin-bottom: 4px;
    line-height: 1.35;
}

.order-prod-meta {
    font-size: 12.5px;
    color: var(--order-text-muted);
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.order-prod-price {
    font-size: 16px;
    font-weight: 800;
    color: var(--order-primary);
    text-align: right;
    flex-shrink: 0;
}

/* Card Footer */
.order-card-footer {
    background: #fafcfc;
    border-top: 1px solid var(--order-border);
    padding: 14px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
}

.order-delivery-status-note {
    font-size: 13px;
    color: var(--order-text-muted);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 7px;
}

.order-action-btns {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-order-outline {
    background: #ffffff;
    border: 1.5px solid var(--order-border);
    color: var(--order-text-main);
    font-size: 12.5px;
    font-weight: 600;
    padding: 7px 16px;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
}
.btn-order-outline:hover {
    border-color: var(--order-primary);
    color: var(--order-primary);
    background: var(--order-teal-light);
}

.btn-order-cancel {
    background: #ffffff;
    border: 1.5px solid #fecaca;
    color: #dc2626;
    font-size: 12.5px;
    font-weight: 600;
    padding: 7px 16px;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
}
.btn-order-cancel:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}

/* ── Empty State Card ── */
.order-empty-card {
    background: #ffffff;
    border: 1.5px solid var(--order-border);
    border-radius: 20px;
    padding: 55px 24px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(7, 72, 74, 0.04);
    max-width: 520px;
    margin: 40px auto;
}

.order-empty-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--order-teal-light);
    color: var(--order-teal);
    font-size: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
}

.order-empty-card h3 {
    font-size: 19px;
    font-weight: 800;
    color: var(--order-text-main);
    margin-bottom: 6px;
}

.order-empty-card p {
    font-size: 13.5px;
    color: var(--order-text-muted);
    margin-bottom: 22px;
    line-height: 1.55;
}

@media (max-width: 768px) {
    .orders-page-section {
        padding: 20px 0 50px;
    }
    .order-hero-banner {
        padding: 20px;
        border-radius: 16px;
    }
    .order-card-header, .order-card-body, .order-card-footer {
        padding: 14px 16px;
    }
    .order-meta-cells {
        gap: 16px;
    }
    .order-product-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    .order-prod-price {
        text-align: left;
    }
    .order-card-footer {
        flex-direction: column;
        align-items: stretch;
    }
    .order-action-btns {
        width: 100%;
    }
    .order-action-btns form,
    .order-action-btns .btn-order-outline,
    .order-action-btns .btn-order-cancel,
    .order-action-btns .lenskart-return-exchange-wrapper {
        flex: 1;
        width: 100%;
        justify-content: center;
    }
}

/* ══════════════════════════════════════════════════════════════════════════════
   LENSKART-STYLE RETURN & EXCHANGE COMPONENT STYLES
   ══════════════════════════════════════════════════════════════════════════════ */
.lenskart-return-exchange-wrapper {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    vertical-align: top;
    text-align: center;
}

.btn-lenskart-return-exchange {
    background: #e9eff2;
    border: 1px solid #b8ccd6;
    color: #009999;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 7px 14px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    text-transform: uppercase;
    line-height: 1.2;
    text-decoration: none;
}

.btn-lenskart-return-exchange:hover,
.btn-lenskart-return-exchange:focus,
.btn-lenskart-return-exchange.show {
    background: #dbe6eb;
    border-color: #009999;
    color: #008080;
    box-shadow: 0 2px 6px rgba(0, 153, 153, 0.15);
}

.btn-lenskart-return-exchange.dropdown-toggle::after {
    display: none !important;
}

.btn-lenskart-return-exchange i {
    font-size: 8px;
    color: #009999;
    transition: transform 0.2s ease;
    display: inline-block;
    vertical-align: middle;
}

.btn-lenskart-return-exchange.show i {
    transform: rotate(180deg);
}

.lenskart-return-caption {
    font-size: 11px;
    color: #7b8a95;
    font-weight: 400;
    margin-top: 4px;
    white-space: nowrap;
    text-align: center;
}

/* Return status badge in order card */
.return-status-pill-box {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
}

.badge-return-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
}
.badge-ret-requested {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.badge-ret-approved {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.badge-ret-received {
    background: #f3e8ff;
    color: #6b21a8;
    border: 1px solid #e9d5ff;
}
.badge-ret-completed {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.badge-ret-rejected {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.btn-track-return {
    background: none;
    border: none;
    padding: 0;
    color: #00B9B9;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-track-return:hover {
    color: #07484A;
}

.btn-cancel-return-req {
    background: none;
    border: none;
    padding: 0;
    color: #ef4444;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.btn-cancel-return-req:hover {
    color: #b91c1c;
}

.return-dropdown-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.bg-teal-subtle { background: #e6f9f9; }
.text-teal { color: #00B9B9; }

/* ── LENSKART RETURN / EXCHANGE MODAL STYLES ── */
.modal-lenskart-header {
    background: linear-gradient(135deg, #07484A 0%, #0c676a 100%);
    color: #ffffff;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    padding: 18px 24px;
}
.modal-lenskart-header .modal-title {
    font-weight: 700;
    font-size: 17px;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.modal-lenskart-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.85;
}

.ret-guarantee-strip {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12.5px;
    color: #065f46;
}

/* Two Big Action Selection Cards */
.ret-action-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 20px;
}
.ret-action-card {
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.22s ease;
    background: #ffffff;
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.ret-action-card:hover {
    border-color: #00B9B9;
    background: #f9fefe;
}
.ret-action-card.active {
    border-color: #00B9B9;
    background: #f0fdfa;
    box-shadow: 0 4px 14px rgba(0, 185, 185, 0.12);
}
.ret-action-card .card-radio-check {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.ret-action-card.active .card-radio-check {
    border-color: #00B9B9;
    background: #00B9B9;
}
.ret-action-card .card-radio-check i {
    color: #ffffff;
    font-size: 11px;
    display: none;
}
.ret-action-card.active .card-radio-check i {
    display: block;
}

.ret-action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 4px;
}

.ret-action-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
}
.ret-action-sub {
    font-size: 12px;
    color: #64748b;
    line-height: 1.4;
}
.ret-action-badge {
    align-self: flex-start;
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 50px;
    margin-top: 4px;
}

/* Selectable Sub-Options */
.ret-sub-option {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.18s;
    background: #ffffff;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.ret-sub-option:hover {
    border-color: #00B9B9;
    background: #fafcfc;
}
.ret-sub-option.active {
    border-color: #00B9B9;
    background: #f0fdfa;
}
.ret-sub-option input[type="radio"] {
    margin-top: 3px;
    accent-color: #00B9B9;
}

/* Timeline tracking styles */
.return-timeline {
    position: relative;
    padding: 10px 0 10px 24px;
}
.return-timeline::before {
    content: '';
    position: absolute;
    top: 20px;
    bottom: 20px;
    left: 7px;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    margin-bottom: 22px;
}
.timeline-item:last-child {
    margin-bottom: 0;
}
.timeline-dot {
    position: absolute;
    left: -24px;
    top: 3px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    z-index: 2;
    transition: all 0.2s;
}
.timeline-item.done .timeline-dot {
    border-color: #00B9B9;
    background: #00B9B9;
}
.timeline-item.active .timeline-dot {
    border-color: #f59e0b;
    background: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
}
.timeline-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}
.timeline-desc {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 2px;
}
.timeline-date {
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
}
</style>

<section class="orders-page-section">
    <div class="container">

        <!-- Breadcrumb Navigation -->
        <div class="order-breadcrumb">
            <a href="{{ route('home') }}"><i class="bi bi-house-door-fill"></i> Home</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('profile') }}">Account</a>
            <i class="bi bi-chevron-right"></i>
            <span class="active">My Orders</span>
        </div>

        <!-- Flash Alerts -->
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 p-3 mb-4 d-flex align-items-center" role="alert" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0 !important;">
                <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                <div class="flex-grow-1">{!! session()->get('success') !!}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 p-3 mb-4 d-flex align-items-center" role="alert" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca !important;">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                <div class="flex-grow-1">{{ session()->get('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Hero Banner Header -->
        <div class="order-hero-banner">
            <div class="order-hero-title">
                <h2><i class="bi bi-box-seam-fill" style="color: #00B9B9;"></i> My Orders</h2>
                <p>Track shipments, review past orders, and manage eyewear deliveries</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('products') }}" class="btn-order-shop">
                    <i class="bi bi-eyeglasses fs-5"></i> Explore Eyewear
                </a>
            </div>
        </div>

        @php
            $totalCount      = $orders->count();
            $processingCount = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['0', 'pending', 'confirmed', 'processing', 'in_lab']))->count();
            $transitCount    = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['1', 'ready_to_ship', 'shipped', 'transit', 'out_for_delivery']))->count();
            $deliveredCount  = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['2', 'delivered', 'completed']))->count();
            $cancelledCount  = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['3', 'cancelled']))->count();
            $returnsCount    = $orders->filter(fn($o) => !empty($o->return_stage) || ($o->order_status ?? '') === 'returned')->count();
        @endphp

        <!-- Filter Pills & Search Controls -->
        <div class="order-controls-bar">
            <div class="order-filter-pills">
                <button type="button" class="order-pill-btn active" data-filter="all">
                    <i class="bi bi-grid-fill"></i> All Orders <span class="order-pill-count">{{ $totalCount }}</span>
                </button>
                @if($processingCount > 0)
                    <button type="button" class="order-pill-btn" data-filter="processing">
                        <i class="bi bi-clock-history"></i> Processing <span class="order-pill-count">{{ $processingCount }}</span>
                    </button>
                @endif
                <button type="button" class="order-pill-btn" data-filter="transit">
                    <i class="bi bi-truck"></i> In Transit <span class="order-pill-count">{{ $transitCount }}</span>
                </button>
                <button type="button" class="order-pill-btn" data-filter="delivered">
                    <i class="bi bi-check-circle-fill"></i> Delivered <span class="order-pill-count">{{ $deliveredCount }}</span>
                </button>
                @if($returnsCount > 0)
                    <button type="button" class="order-pill-btn" data-filter="returns">
                        <i class="bi bi-arrow-left-right"></i> Returns & Exchanges <span class="order-pill-count">{{ $returnsCount }}</span>
                    </button>
                @endif
                @if($cancelledCount > 0)
                    <button type="button" class="order-pill-btn" data-filter="cancelled">
                        <i class="bi bi-x-circle-fill"></i> Cancelled <span class="order-pill-count">{{ $cancelledCount }}</span>
                    </button>
                @endif
            </div>

            <div class="order-search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="order-search-input" id="orderSearchInput" placeholder="Search by Order ID or item...">
            </div>
        </div>

        <!-- Orders List Container -->
        <div id="ordersContainer">
            @forelse($orders as $order)
                @php
                    $statusStr = strtolower((string)($order->order_status ?? ($order->sales_status ?? 0)));
                    $hasReturn = !empty($order->return_stage);
                    $isExchange = ($order->return_type !== 'refund');
                    $stageLabel = $hasReturn ? ucfirst(str_replace('_', ' ', $order->return_stage)) : '';
                    
                    if (in_array($statusStr, ['2', 'delivered', 'completed'])) {
                        $dataStatus   = 'delivered';
                        $statusLabel  = 'Delivered';
                        $statusClass  = 'delivered';
                        $statusIcon   = 'bi-check-circle-fill';
                    } elseif (in_array($statusStr, ['1', 'shipped', 'transit', 'ready_to_ship', 'out_for_delivery'])) {
                        $dataStatus   = 'transit';
                        $statusLabel  = ($statusStr === 'ready_to_ship') ? 'Ready to Ship' : 'In Transit';
                        $statusClass  = 'transit';
                        $statusIcon   = 'bi-truck';
                    } elseif (in_array($statusStr, ['3', 'cancelled'])) {
                        $dataStatus   = 'cancelled';
                        $statusLabel  = 'Cancelled';
                        $statusClass  = 'cancelled';
                        $statusIcon   = 'bi-x-circle-fill';
                    } elseif ($statusStr === 'returned') {
                        $dataStatus   = 'returns';
                        $statusLabel  = 'Returned';
                        $statusClass  = 'cancelled';
                        $statusIcon   = 'bi-arrow-counterclockwise';
                    } else {
                        $dataStatus   = 'processing';
                        $statusLabel  = ($statusStr === 'confirmed') ? 'Confirmed' : (($statusStr === 'pending') ? 'Order Placed' : 'Processing');
                        $statusClass  = 'processing';
                        $statusIcon   = 'bi-clock-history';
                    }

                    $orderId      = $order->id ?? ($order->sale_id ?? 0);
                    $orderNo      = $order->order_no ?? ('SPECK' . $orderId);
                    $orderDate    = !empty($order->sale_date) ? \Carbon\Carbon::parse($order->sale_date)->format('d M Y') : (\Carbon\Carbon::parse($order->created_at)->format('d M Y'));
                    $totalPayable = (float)($order->total_payable ?? ($order->pay_amount ?? 0));
                    $products     = $order->products ?? collect();
                    $searchCorpus = strtolower($orderNo . ' ' . $statusLabel . ' ' . ($hasReturn ? 'return exchange ' : '') . $products->pluck('product_deatils')->join(' '));
                    
                    $encryptedId = base64_encode($orderId);
                @endphp

                <div class="order-box-card" data-status="{{ $dataStatus }} {{ $hasReturn ? 'returns' : '' }}" data-search="{{ $searchCorpus }}">
                    <div class="order-card-top-accent"></div>

                    <!-- Header -->
                    <div class="order-card-header">
                        <div class="order-meta-cells">
                            <div class="order-meta-cell">
                                <span class="order-meta-label">Order Placed</span>
                                <span class="order-meta-val">{{ $orderDate }}</span>
                            </div>
                            <div class="order-meta-cell">
                                <span class="order-meta-label">Total Amount</span>
                                <span class="order-meta-val">₹{{ number_format($totalPayable, 2) }}</span>
                            </div>
                            <div class="order-meta-cell">
                                <span class="order-meta-label">Order ID</span>
                                <span class="order-meta-val">#{{ $orderNo }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="order-status-badge {{ $statusClass }}">
                                <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
                            </span>
                            @if($hasReturn)
                                <span class="order-status-badge processing" title="Return/Exchange request status">
                                    <i class="bi {{ $isExchange ? 'bi-arrow-left-right' : 'bi-arrow-counterclockwise' }}"></i>
                                    {{ $isExchange ? 'Exchange' : 'Return' }} {{ $stageLabel }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Products List -->
                    <div class="order-card-body">
                        @forelse($products as $prod)
                            <div class="order-product-row">
                                <div class="order-prod-thumb">
                                    <img src="{{ $prod->image ?? asset('website/assets/img/bg/Eyeglasses1.png') }}"
                                        alt="{{ $prod->product_deatils ?? 'Eyewear Frame' }}"
                                        onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Eyeglasses1.png') }}';">
                                </div>
                                <div class="order-prod-details">
                                    <div class="order-prod-brand">{{ $prod->product_company ?? 'Speckarts' }}</div>
                                    <div class="order-prod-title">{{ $prod->product_deatils ?? 'Premium Eyeglasses' }}</div>
                                    <div class="order-prod-meta">
                                        <span><i class="bi bi-box-seam text-muted me-1"></i> Qty: <strong>{{ $prod->qty ?? 1 }}</strong></span>
                                        <span><i class="bi bi-shield-check text-muted me-1"></i> 1-Year Warranty</span>
                                    </div>
                                </div>
                                <div class="order-prod-price">
                                    ₹{{ number_format((float)($prod->item_price ?? $prod->sale_price ?? $totalPayable), 2) }}
                                </div>
                            </div>
                        @empty
                            <div class="order-product-row">
                                <div class="order-prod-thumb">
                                    <img src="{{ asset('website/assets/img/bg/Eyeglasses1.png') }}" alt="Speckart Frame">
                                </div>
                                <div class="order-prod-details">
                                    <div class="order-prod-brand">Speckarts Signature</div>
                                    <div class="order-prod-title">Order #{{ $orderNo }}</div>
                                    <div class="order-prod-meta">
                                        <span>Ordered on {{ $orderDate }}</span>
                                    </div>
                                </div>
                                <div class="order-prod-price">
                                    ₹{{ number_format($totalPayable, 2) }}
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Footer Actions -->
                    <div class="order-card-footer">
                        <div class="order-delivery-status-note">
                            @if($dataStatus === 'delivered')
                                <i class="bi bi-check-circle-fill text-success"></i> Delivered on <strong>{{ $orderDate }}</strong>
                            @elseif($dataStatus === 'cancelled')
                                <i class="bi bi-x-circle-fill text-danger"></i> This order was cancelled
                            @else
                                <i class="bi bi-truck text-primary"></i> Est. Delivery by: <strong>{{ \Carbon\Carbon::parse($order->sale_date ?? now())->addDays(4)->format('d M Y') }}</strong>
                            @endif
                        </div>

                        <div class="order-action-btns">
                            {{-- Lenskart Return & Exchange Button (always clean & prominent for delivered orders) --}}
                            @if($dataStatus === 'delivered' || $statusStr === 'delivered' || $statusStr === 'completed')
                                <div class="lenskart-return-exchange-wrapper">
                                    <div class="dropdown">
                                        <button type="button" class="btn-lenskart-return-exchange dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="retDrop_{{ $orderId }}">
                                            RETURN/EXCHANGE <i class="bi bi-caret-down-fill ms-1"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 font-13" aria-labelledby="retDrop_{{ $orderId }}" style="min-width: 250px;">
                                            <li>
                                                <a class="dropdown-item py-2 px-3 rounded-2 open-ret-exchange-modal" href="javascript:void(0);" 
                                                   data-action="exchange"
                                                   data-order-id="{{ $orderId }}"
                                                   data-order-no="{{ $orderNo }}"
                                                   data-address="{{ $order->shipping_address ?? '' }}"
                                                   data-phone="{{ $order->contact_no ?? '' }}"
                                                   data-products="{{ json_encode($products) }}">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="return-dropdown-icon text-teal bg-teal-subtle"><i class="bi bi-arrow-left-right"></i></span>
                                                        <div>
                                                            <div class="fw-bold text-dark font-13">Exchange Product</div>
                                                            <div class="text-muted font-11">Free Lens Remake, different frame</div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <a class="dropdown-item py-2 px-3 rounded-2 open-ret-exchange-modal" href="javascript:void(0);" 
                                                   data-action="return"
                                                   data-order-id="{{ $orderId }}"
                                                   data-order-no="{{ $orderNo }}"
                                                   data-address="{{ $order->shipping_address ?? '' }}"
                                                   data-phone="{{ $order->contact_no ?? '' }}"
                                                   data-products="{{ json_encode($products) }}">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="return-dropdown-icon text-danger bg-danger-subtle"><i class="bi bi-cash-stack"></i></span>
                                                        <div>
                                                            <div class="fw-bold text-dark font-13">Return & 100% Refund</div>
                                                            <div class="text-muted font-11">Return item & get money back</div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            @if($hasReturn)
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <a class="dropdown-item py-2 px-3 rounded-2 btn-track-return" href="javascript:void(0);" data-order-id="{{ $orderId }}">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="return-dropdown-icon text-warning bg-warning-subtle"><i class="bi bi-clock-history"></i></span>
                                                            <div>
                                                                <div class="fw-bold text-dark font-13">Track Existing Request</div>
                                                                <div class="text-muted font-11">{{ $isExchange ? 'Exchange' : 'Return' }} ({{ $stageLabel }})</div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    @if($hasReturn)
                                        <div class="lenskart-return-caption">
                                            <span class="text-warning-emphasis fw-bold">{{ $isExchange ? 'Exchange' : 'Return' }} {{ $stageLabel }}</span>
                                            &bull; <a href="javascript:void(0);" class="btn-track-return text-teal fw-semibold" data-order-id="{{ $orderId }}">Track</a>
                                            @if($order->return_stage === 'requested')
                                                &bull; <a href="javascript:void(0);" class="btn-cancel-return-req text-danger" data-order-id="{{ $orderId }}" data-order-no="{{ $orderNo }}">Cancel</a>
                                            @endif
                                        </div>
                                    @else
                                        <div class="lenskart-return-caption">Return or Exchange your product</div>
                                    @endif
                                </div>
                            @endif

                            @if($dataStatus === 'processing' || $dataStatus === 'transit')
                                <form action="{{ route('my-orders.cancel', $orderId) }}" method="POST" class="cancel-order-form" data-order-no="{{ $orderNo }}">
                                    @csrf
                                    <button type="submit" class="btn-order-cancel">
                                        <i class="bi bi-x-lg"></i> Cancel Order
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('my-orders.reorder', $orderId) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-order-outline">
                                        <i class="bi bi-arrow-repeat"></i> Buy Again
                                    </button>
                                </form>
                            @endif
                        

                            <a href="{{ route('my-orders.advance-receipt', ['id' => $encryptedId, 'idd' => 'receipt']) }}" target="_blank" class="order-status-badge processing">
                                <i class="bi bi-receipt"></i> Advance Receipt
                            </a>
                                
                            <a href="{{ route('my-orders.prescription-receipt', ['id' => $encryptedId, 'idd' => 'order']) }}" target="_blank" class="order-status-badge transit">
                                <i class="bi bi-file-medical"></i> Prescription Receipt
                            </a>
                                
                            <a href="{{ route('my-orders.invoice', ['id' => $encryptedId, 'idd' => 'invoice']) }}" target="_blank" class="order-status-badge delivered">
                                <i class="bi bi-file-text"></i> Invoice
                            </a>
                        
                        </div>
                    </div>
                </div>
            @empty
                <div class="order-empty-card" id="emptyOrdersState">
                    <div class="order-empty-icon">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <h3>No Orders Yet</h3>
                    <p>When you place orders on Speckarts, they will appear here so you can easily track deliveries and manage orders.</p>
                    <a href="{{ route('products') }}" class="btn-order-shop">
                        <i class="bi bi-cart-plus-fill"></i> Start Shopping
                    </a>
                </div>
            @endforelse

            <div class="order-empty-card d-none" id="emptyFilterState">
                <div class="order-empty-icon" style="background: #f1f5f9; color: #64748b;">
                    <i class="bi bi-search"></i>
                </div>
                <h3>No Matching Orders Found</h3>
                <p>We couldn't find any orders matching your search query or selected filter.</p>
                <button type="button" class="btn-order-outline" id="resetSearchBtn">
                    <i class="bi bi-arrow-counterclockwise"></i> View All Orders
                </button>
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════════════
     LENSKART-STYLE RETURN & EXCHANGE MODAL
     ══════════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="lenskartReturnExchangeModal" tabindex="-1" aria-labelledby="retModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 20px 45px rgba(0,0,0,0.18);">
            
            <div class="modal-lenskart-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="modal-title m-0">
                        <i class="bi bi-arrow-left-right text-warning"></i> Return & Exchange Request
                    </h5>
                    <div style="font-size: 12px; color: #ccfbf1; margin-top: 3px;">
                        Order <strong id="modalOrderNoDisplay">#SPECK</strong>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="lenskartReturnForm" method="POST" enctype="multipart/form-data" action="">
                @csrf
                <input type="hidden" name="action_type" id="retActionType" value="exchange">
                <input type="hidden" name="return_type" id="retReturnType" value="replacement">
                <input type="hidden" name="exchange_type" id="retExchangeType" value="different_power">
                <input type="hidden" name="order_id" id="retOrderIdHidden" value="">

                <div class="modal-body p-4" style="background: #f8fafc; max-height: 75vh; overflow-y: auto;">
                    
                    <!-- Lenskart Guarantee Banner -->
                    <div class="ret-guarantee-strip">
                        <i class="bi bi-shield-check fs-5 text-success flex-shrink-0"></i>
                        <div>
                            <strong>Speckarts 14-Day Eyewear Guarantee:</strong>
                            Free Optical Lens Remake if you experience any vision or power discomfort, free doorstep exchange & 100% refund support.
                        </div>
                    </div>

                    <!-- Step 1: Action Selection Cards (Exchange vs Return) -->
                    <label class="form-label fw-bold text-dark font-14 mb-2">What would you like to do?</label>
                    <div class="ret-action-cards">
                        
                        <!-- Card 1: Exchange -->
                        <div class="ret-action-card active" id="cardActionExchange" onclick="selectActionType('exchange')">
                            <div class="card-radio-check"><i class="bi bi-check"></i></div>
                            <div class="ret-action-icon bg-teal-subtle text-teal">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                            <div class="ret-action-title">Exchange Product</div>
                            <div class="ret-action-sub">
                                Change optical power (Free Remake), swap frame model or get fresh replacement.
                            </div>
                            <span class="ret-action-badge bg-teal-subtle text-teal">Recommended for Eyewear</span>
                        </div>

                        <!-- Card 2: Return -->
                        <div class="ret-action-card" id="cardActionReturn" onclick="selectActionType('return')">
                            <div class="card-radio-check"><i class="bi bi-check"></i></div>
                            <div class="ret-action-icon bg-danger-subtle text-danger">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div class="ret-action-title">Return & 100% Refund</div>
                            <div class="ret-action-sub">
                                Return the product and receive complete refund to your original payment mode or bank.
                            </div>
                            <span class="ret-action-badge bg-light text-muted">100% Money-back</span>
                        </div>
                    </div>

                    <!-- Products in Order (Selection) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-13 mb-1">Select Item to Return / Exchange</label>
                        <div id="retModalProductList" class="p-3 bg-white rounded-3 border">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Step 2A: Exchange Preferences (Shown if Exchange) -->
                    <div id="sectionExchangeTypes" class="mb-3">
                        <label class="form-label fw-bold text-dark font-13 mb-2">How would you like to exchange?</label>
                        
                        <div class="ret-sub-option active" id="subOptRemake" onclick="selectExchangeOption('different_power', 'lens_remake')">
                            <input type="radio" name="exchange_opt_radio" id="optRemake" value="different_power" checked>
                            <label for="optRemake" class="cursor-pointer mb-0">
                                <div class="fw-bold text-dark font-13">
                                    <i class="bi bi-eyeglasses text-teal me-1"></i> Free Optical Lens Remake (Power Adjustment)
                                    <span class="badge bg-success text-white ms-1 font-11">Free Guarantee</span>
                                </div>
                                <div class="text-muted font-12">
                                    Having blurriness, eye strain or headache? We'll remake your lenses with revised power for FREE.
                                </div>
                            </label>
                        </div>

                        <div class="ret-sub-option" id="subOptDiffFrame" onclick="selectExchangeOption('different_frame', 'replacement')">
                            <input type="radio" name="exchange_opt_radio" id="optDiffFrame" value="different_frame">
                            <label for="optDiffFrame" class="cursor-pointer mb-0">
                                <div class="fw-bold text-dark font-13">
                                    <i class="bi bi-shuffle text-primary me-1"></i> Exchange for Different Frame / Model
                                </div>
                                <div class="text-muted font-12">
                                    Want a different color, shape, or design. Our courier will deliver the new frame to your doorstep.
                                </div>
                            </label>
                        </div>

                        <div class="ret-sub-option" id="subOptSameFrame" onclick="selectExchangeOption('same_product', 'replacement')">
                            <input type="radio" name="exchange_opt_radio" id="optSameFrame" value="same_product">
                            <label for="optSameFrame" class="cursor-pointer mb-0">
                                <div class="fw-bold text-dark font-13">
                                    <i class="bi bi-box-seam text-secondary me-1"></i> Fresh Product Replacement (Same Model)
                                </div>
                                <div class="text-muted font-12">
                                    Frame damaged, scratched, or loose right out of the box? Receive an exact new replacement piece.
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Step 3: Reason Selection Dropdown -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-13 mb-1" id="lblReason">Reason for Exchange <span class="text-danger">*</span></label>
                        <select name="reason" id="retReasonSelect" class="form-select font-13 py-2" required>
                            <!-- Dynamically populated according to Exchange vs Return -->
                        </select>
                    </div>

                    <!-- Step 4: Remarks / Feedback -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-13 mb-1">
                            Additional Details / Remarks <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="customer_remarks" id="retCustomerRemarks" class="form-control font-13" rows="2" 
                                  placeholder="Tell us what went wrong (e.g. prescription power changes, fit issues, frame feel)..."></textarea>
                    </div>

                    <!-- Step 5: Photo Upload (Optional) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-13 mb-1">
                            Upload Photo <span class="text-muted fw-normal">(Optional — Photo of defect or updated prescription)</span>
                        </label>
                        <input type="file" name="photo" id="retPhotoInput" class="form-control font-13" accept="image/*,.pdf">
                        <div class="text-muted mt-1 font-11">Supported formats: JPG, PNG, WEBP, PDF (Max 5MB)</div>
                    </div>

                    <!-- Step 6: Refund Preference (Only if Return & Refund) -->
                    <div id="sectionRefundMode" class="mb-3 d-none">
                        <label class="form-label fw-bold text-dark font-13 mb-1">Refund Preference</label>
                        <div class="p-3 bg-white rounded-3 border">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="refund_mode" id="refModeOriginal" value="original" checked onchange="toggleBankDetails(false)">
                                <label class="form-check-label font-13 fw-semibold" for="refModeOriginal">
                                    Original Payment Source (Prepaid UPI / Card / Netbanking)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="refund_mode" id="refModeBank" value="bank_transfer" onchange="toggleBankDetails(true)">
                                <label class="form-check-label font-13 fw-semibold" for="refModeBank">
                                    Direct Bank Transfer / NEFT (Recommended for COD orders)
                                </label>
                            </div>

                            <!-- Bank Details Sub-form -->
                            <div id="bankDetailsFields" class="mt-3 p-3 bg-light rounded-3 d-none border">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="font-12 fw-semibold text-muted">Account Number</label>
                                        <input type="text" name="bank_account_no" class="form-control font-13" placeholder="Enter bank account number">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="font-12 fw-semibold text-muted">IFSC Code</label>
                                        <input type="text" name="bank_ifsc" class="form-control font-13" placeholder="e.g. HDFC0001234">
                                    </div>
                                    <div class="col-12">
                                        <label class="font-12 fw-semibold text-muted">Account Holder Name</label>
                                        <input type="text" name="bank_holder_name" class="form-control font-13" placeholder="As per bank account">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 7: Pickup Address Confirmation -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark font-13 mb-1">Doorstep Pickup Address</label>
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt-fill text-danger fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div id="modalPickupAddressDisplay" class="font-13 text-dark fw-semibold">
                                    Address on file
                                </div>
                                <div id="modalPickupContactDisplay" class="font-12 text-muted mt-1">
                                    Contact: —
                                </div>
                                <input type="hidden" name="pickup_address" id="retPickupAddressHidden">
                                <input type="hidden" name="pickup_contact" id="retPickupContactHidden">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer p-3 bg-white border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-4 py-2 font-13 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSubmitReturnForm" class="btn btn-order-shop px-4 py-2 font-13">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="submitRetSpinner"></span>
                        <span id="btnSubmitRetText"><i class="bi bi-check2-circle me-1"></i> Submit Exchange Request</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════════
     RETURN / EXCHANGE LIVE TRACKING TIMELINE MODAL
     ══════════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="returnTrackingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 20px 45px rgba(0,0,0,0.18);">
            
            <div class="modal-lenskart-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="modal-title m-0">
                        <i class="bi bi-clock-history text-warning"></i> Return / Exchange Status
                    </h5>
                    <div style="font-size: 12px; color: #ccfbf1; margin-top: 3px;" id="trackOrderNoDisplay">
                        Order #SPECK
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="background: #f8fafc;">
                
                <!-- Status Header Banner -->
                <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded-3 border mb-3">
                    <div>
                        <div class="text-muted font-11 text-uppercase fw-bold">Request Type</div>
                        <div class="fw-bold text-dark font-14" id="trackTypeLabel">Exchange</div>
                    </div>
                    <div>
                        <span class="badge-return-status badge-ret-requested" id="trackStageBadge">Requested</span>
                    </div>
                </div>

                <!-- Timeline Journey -->
                <div class="p-3 bg-white rounded-3 border mb-3">
                    <div class="fw-bold text-dark font-13 mb-3"><i class="bi bi-signpost-split me-1 text-teal"></i> Request Journey</div>
                    <div class="return-timeline" id="trackTimelineContainer">
                        <!-- Loaded dynamically via AJAX -->
                    </div>
                </div>

                <!-- Request Details Card -->
                <div class="p-3 bg-white rounded-3 border mb-3 font-13">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Reason:</span>
                        <strong class="text-dark text-end" id="trackReasonVal">—</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Preference:</span>
                        <strong class="text-dark text-end" id="trackPreferenceVal">—</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Doorstep Pickup:</span>
                        <span class="text-dark text-end" style="max-width: 60%;" id="trackPickupVal">—</span>
                    </div>
                    <div id="trackAdminNotesRow" class="d-none mt-2 pt-2 border-top">
                        <span class="text-muted d-block mb-1">Customer Support Update:</span>
                        <div class="p-2 bg-light rounded text-dark font-12" id="trackAdminNotesVal"></div>
                    </div>
                </div>

                <!-- Cancel Button for Customer -->
                <div id="trackCancelActionBox" class="d-none text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" id="btnTrackCancelRequest">
                        <i class="bi bi-x-circle me-1"></i> Cancel this Return/Exchange Request
                    </button>
                    <div class="text-muted font-11 mt-1">You can cancel anytime before pickup is scheduled</div>
                </div>

            </div>

            <div class="modal-footer p-3 bg-white border-top">
                <button type="button" class="btn btn-secondary px-4 py-2 font-13 rounded-3 w-100" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script>
    // Global reason lists for Lenskart Return/Exchange
    const exchangeReasons = [
        { value: 'power_mismatch', label: 'Optical Power Mismatch / Unclear Vision / Eye Strain' },
        { value: 'fit_issue',      label: 'Frame Fitting Issue (Too loose / too tight / bridge slip)' },
        { value: 'frame_damage',   label: 'Frame Damage / Defect or Scratch on lens' },
        { value: 'changed_mind',   label: 'Want a Different Color or Frame Design' },
        { value: 'other',          label: 'Other Optical / Frame Reason' }
    ];

    const returnReasons = [
        { value: 'fit_issue',      label: 'Frame Fit / Size Issue (Uncomfortable to wear)' },
        { value: 'changed_mind',   label: 'Disliked Style / Did not suit my face' },
        { value: 'power_mismatch', label: 'Vision Discomfort / Power Adaptation Issue' },
        { value: 'frame_damage',   label: 'Defective or Damaged Product on Delivery' },
        { value: 'other',          label: 'Other Reason' }
    ];

    function selectActionType(type) {
        document.getElementById('retActionType').value = type;
        
        const cardExchange = document.getElementById('cardActionExchange');
        const cardReturn = document.getElementById('cardActionReturn');
        const sectionExchange = document.getElementById('sectionExchangeTypes');
        const sectionRefund = document.getElementById('sectionRefundMode');
        const lblReason = document.getElementById('lblReason');
        const reasonSelect = document.getElementById('retReasonSelect');
        const btnSubmitText = document.getElementById('btnSubmitRetText');

        if (type === 'exchange') {
            cardExchange.classList.add('active');
            cardReturn.classList.remove('active');
            sectionExchange.classList.remove('d-none');
            sectionRefund.classList.add('d-none');
            lblReason.innerHTML = 'Reason for Exchange <span class="text-danger">*</span>';
            btnSubmitText.innerHTML = '<i class="bi bi-arrow-left-right me-1"></i> Submit Exchange Request';

            // Populate exchange reasons
            reasonSelect.innerHTML = exchangeReasons.map(r => `<option value="${r.value}">${r.label}</option>`).join('');
            
            // Set return_type & exchange_type according to selected sub-option
            const checkedRadio = document.querySelector('input[name="exchange_opt_radio"]:checked');
            if (checkedRadio && checkedRadio.value === 'different_power') {
                document.getElementById('retReturnType').value = 'lens_remake';
                document.getElementById('retExchangeType').value = 'different_power';
            } else if (checkedRadio) {
                document.getElementById('retReturnType').value = 'replacement';
                document.getElementById('retExchangeType').value = checkedRadio.value;
            } else {
                document.getElementById('retReturnType').value = 'replacement';
                document.getElementById('retExchangeType').value = 'different_power';
            }
        } else {
            cardReturn.classList.add('active');
            cardExchange.classList.remove('active');
            sectionExchange.classList.add('d-none');
            sectionRefund.classList.remove('d-none');
            lblReason.innerHTML = 'Reason for Return & Refund <span class="text-danger">*</span>';
            btnSubmitText.innerHTML = '<i class="bi bi-cash-stack me-1"></i> Submit Return Request';

            // Populate return reasons
            reasonSelect.innerHTML = returnReasons.map(r => `<option value="${r.value}">${r.label}</option>`).join('');

            document.getElementById('retReturnType').value = 'refund';
            document.getElementById('retExchangeType').value = 'none';
        }
    }

    function selectExchangeOption(exchangeVal, returnTypeVal) {
        document.getElementById('retExchangeType').value = exchangeVal;
        document.getElementById('retReturnType').value = returnTypeVal;

        document.querySelectorAll('.ret-sub-option').forEach(el => el.classList.remove('active'));
        const radio = document.querySelector(`input[name="exchange_opt_radio"][value="${exchangeVal}"]`);
        if (radio) {
            radio.checked = true;
            radio.closest('.ret-sub-option').classList.add('active');
        }
    }

    function toggleBankDetails(show) {
        const bankFields = document.getElementById('bankDetailsFields');
        if (bankFields) {
            bankFields.classList.toggle('d-none', !show);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.order-pill-btn');
        const orderCards = document.querySelectorAll('.order-box-card');
        const searchInput = document.getElementById('orderSearchInput');
        const emptyFilterState = document.getElementById('emptyFilterState');
        const resetBtn = document.getElementById('resetSearchBtn');

        let activeFilter = 'all';
        let searchQuery = '';

        function applyFilters() {
            let visibleMatches = 0;

            orderCards.forEach(card => {
                const status = card.dataset.status || '';
                const searchCorpus = card.dataset.search || '';

                const matchesStatus = (activeFilter === 'all' || status.includes(activeFilter));
                const matchesSearch = (!searchQuery || searchCorpus.includes(searchQuery.toLowerCase().trim()));

                if (matchesStatus && matchesSearch) {
                    card.style.display = '';
                    visibleMatches++;
                } else {
                    card.style.display = 'none';
                }
            });

            filterBtns.forEach(btn => btn.classList.toggle('active', btn.dataset.filter === activeFilter));

            if (emptyFilterState) {
                emptyFilterState.classList.toggle('d-none', visibleMatches > 0 || orderCards.length === 0);
            }
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                activeFilter = this.dataset.filter;
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                searchQuery = this.value;
                applyFilters();
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                activeFilter = 'all';
                searchQuery = '';
                if (searchInput) searchInput.value = '';
                applyFilters();
            });
        }

        // Cancel order confirmation with SweetAlert2
        document.querySelectorAll('.cancel-order-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const orderNo = this.dataset.orderNo || '';
                Swal.fire({
                    title: 'Cancel Order?',
                    text: 'Are you sure you want to cancel order #' + orderNo + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, cancel order',
                    cancelButtonText: 'No, keep order'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Open Lenskart Return/Exchange Modal
        document.querySelectorAll('.open-ret-exchange-modal').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.dataset.action || 'exchange';
                const orderId = this.dataset.orderId;
                const orderNo = this.dataset.orderNo;
                const address = this.dataset.address || 'Address on file';
                const phone = this.dataset.phone || '';
                let products = [];
                try {
                    products = JSON.parse(this.dataset.products || '[]');
                } catch(err) {
                    products = [];
                }

                document.getElementById('modalOrderNoDisplay').textContent = '#' + orderNo;
                document.getElementById('retOrderIdHidden').value = orderId;
                document.getElementById('modalPickupAddressDisplay').textContent = address;
                document.getElementById('modalPickupContactDisplay').textContent = phone ? 'Contact: ' + phone : '';
                document.getElementById('retPickupAddressHidden').value = address;
                document.getElementById('retPickupContactHidden').value = phone;

                // Form action URL
                const form = document.getElementById('lenskartReturnForm');
                form.action = "{{ url('my-orders/return-exchange') }}/" + orderId;

                // Render product selection items
                const prodListContainer = document.getElementById('retModalProductList');
                if (products && products.length > 0) {
                    prodListContainer.innerHTML = products.map((prod, idx) => `
                        <div class="d-flex align-items-center gap-3 ${idx > 0 ? 'mt-2 pt-2 border-top' : ''}">
                            <input class="form-check-input mt-0" type="checkbox" name="item_ids[]" value="${prod.id || prod.item_id || prod.product_id || idx}" checked id="chk_prod_${idx}" style="width: 18px; height: 18px; accent-color: #00B9B9;">
                            <img src="${prod.image || '{{ asset("website/assets/img/bg/Eyeglasses1.png") }}'}" style="width: 48px; height: 38px; object-fit: contain; background: #f8fafc; border-radius: 8px; padding: 2px; border: 1px solid #e2e8f0;">
                            <div class="flex-grow-1">
                                <label for="chk_prod_${idx}" class="fw-bold text-dark font-13 mb-0 cursor-pointer">
                                    ${prod.product_deatils || 'Premium Frame'}
                                </label>
                                <div class="text-muted font-11">Qty: ${prod.qty || 1} &bull; Price: ₹${parseFloat(prod.item_price || 0).toFixed(2)}</div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    prodListContainer.innerHTML = `
                        <div class="text-muted font-13">Order items will be included in the return/exchange request.</div>
                    `;
                }

                // Initialize action type & reasons
                selectActionType(action);

                // Show modal
                const modalEl = document.getElementById('lenskartReturnExchangeModal');
                const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                bsModal.show();
            });
        });

        // Return Form AJAX Submit
        const returnForm = document.getElementById('lenskartReturnForm');
        if (returnForm) {
            returnForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('btnSubmitReturnForm');
                const spinner = document.getElementById('submitRetSpinner');
                const reasonVal = document.getElementById('retReasonSelect').value;

                if (!reasonVal) {
                    toastr.warning('Please select a reason for return/exchange.');
                    return;
                }

                submitBtn.disabled = true;
                if (spinner) spinner.classList.remove('d-none');

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    submitBtn.disabled = false;
                    if (spinner) spinner.classList.add('d-none');

                    if (data.success) {
                        const modalEl = document.getElementById('lenskartReturnExchangeModal');
                        const bsModal = bootstrap.Modal.getInstance(modalEl);
                        if (bsModal) bsModal.hide();

                        Swal.fire({
                            title: 'Request Submitted!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#00B9B9',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Could not submit request. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    if (spinner) spinner.classList.add('d-none');
                    console.error(err);
                    toastr.error('An unexpected error occurred. Please try again.');
                });
            });
        }

        // Return Tracking Modal
        document.querySelectorAll('.btn-track-return').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                if (!orderId) return;

                fetch("{{ url('my-orders/return-exchange/details') }}/" + orderId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        toastr.error(data.message || 'Could not load details');
                        return;
                    }

                    document.getElementById('trackOrderNoDisplay').textContent = 'Order #' + data.order_no;
                    document.getElementById('trackTypeLabel').textContent = data.type_label || (data.action_type === 'refund' ? 'Return & Refund' : 'Exchange');
                    
                    const badge = document.getElementById('trackStageBadge');
                    badge.textContent = data.stage_label || 'Requested';
                    badge.className = 'badge-return-status badge-ret-' + (data.stage || 'requested');

                    document.getElementById('trackReasonVal').textContent = data.reason_label || '—';
                    document.getElementById('trackPreferenceVal').textContent = data.exchange_label || '—';
                    document.getElementById('trackPickupVal').textContent = data.pickup_address || '—';

                    const adminRow = document.getElementById('trackAdminNotesRow');
                    const adminVal = document.getElementById('trackAdminNotesVal');
                    if (data.admin_notes) {
                        adminRow.classList.remove('d-none');
                        adminVal.textContent = data.admin_notes;
                    } else {
                        adminRow.classList.add('d-none');
                    }

                    // Render journey timeline
                    const timelineBox = document.getElementById('trackTimelineContainer');
                    if (data.steps && data.steps.length > 0) {
                        timelineBox.innerHTML = data.steps.map(step => `
                            <div class="timeline-item ${step.done ? 'done' : ''} ${step.active ? 'active' : ''}">
                                <div class="timeline-dot"></div>
                                <div class="timeline-title">${step.title}</div>
                                <div class="timeline-desc">${step.desc}</div>
                                <div class="timeline-date">${step.date}</div>
                            </div>
                        `).join('');
                    }

                    // Cancel button in tracking modal
                    const cancelBox = document.getElementById('trackCancelActionBox');
                    const cancelBtn = document.getElementById('btnTrackCancelRequest');
                    if (data.can_cancel) {
                        cancelBox.classList.remove('d-none');
                        cancelBtn.onclick = function() {
                            cancelReturnRequest(orderId, data.order_no);
                        };
                    } else {
                        cancelBox.classList.add('d-none');
                    }

                    const trackModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('returnTrackingModal'));
                    trackModal.show();
                })
                .catch(err => {
                    console.error(err);
                    toastr.error('Failed to load tracking details');
                });
            });
        });

        // Cancel return request function
        function cancelReturnRequest(orderId, orderNo) {
            Swal.fire({
                title: 'Cancel Request?',
                text: 'Are you sure you want to cancel the return/exchange request for order #' + orderNo + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, cancel request',
                cancelButtonText: 'Keep request'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch("{{ url('my-orders/return-exchange/cancel') }}/" + orderId, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Cancelled',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#00B9B9'
                            }).then(() => window.location.reload());
                        } else {
                            toastr.error(data.message || 'Could not cancel request');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        toastr.error('Error cancelling request');
                    });
                }
            });
        }

        // Hook direct cancel buttons on order cards
        document.querySelectorAll('.btn-cancel-return-req').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                const orderNo = this.dataset.orderNo;
                cancelReturnRequest(orderId, orderNo);
            });
        });
    });
</script>
@endsection

