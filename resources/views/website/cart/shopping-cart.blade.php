@extends('website.layout.master')
@section('content')

{{-- ═══════════════════════════════════════════════════
     SPECKART — Premium Shopping Cart UI
     Lenskart-Inspired · Compact · Responsive
═══════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ══════════════════════════════════════
   DESIGN TOKENS
══════════════════════════════════════ */
:root {
    --sc-primary: #329a9a;
    --sc-primary-dark: #277878;
    --sc-primary-soft: #eef8f8;
    --sc-green: #16a34a;
    --sc-green-soft: #f0fdf4;
    --sc-amber: #d97706;
    --sc-amber-soft: #fffbeb;
    --sc-red: #dc2626;
    --sc-red-soft: #fef2f2;
    --sc-blue: #2563eb;
    --sc-blue-soft: #eff6ff;
    --sc-purple: #7c3aed;
    --sc-purple-soft: #f5f3ff;
    --sc-text: #111827;
    --sc-text-secondary: #6b7280;
    --sc-text-muted: #9ca3af;
    --sc-border: #e5e7eb;
    --sc-bg: #f3f4f6;
    --sc-card: #ffffff;
    --sc-radius: 12px;
    --sc-radius-sm: 8px;
    --sc-shadow: 0 1px 3px rgba(0,0,0,0.06);
    --sc-shadow-md: 0 4px 12px rgba(0,0,0,0.08);
    --sc-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.sc-page, .sc-page *,
.sc-progress-bar, .sc-progress-bar * {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    box-sizing: border-box;
}

.sc-page {
    background: var(--sc-bg);
    min-height: 60vh;
    padding-bottom: 32px;
}

/* ══════════════════════════════════════
   BREADCRUMBS
══════════════════════════════════════ */
.sc-breadcrumb-wrap {
    background: #ffffff;
    border-bottom: 1px solid var(--sc-border);
    padding: 12px 0;
}
.sc-breadcrumb {
    margin-bottom: 0;
    font-size: 13px;
    font-weight: 500;
}
.sc-breadcrumb a {
    color: var(--sc-text-secondary);
    text-decoration: none;
    transition: var(--sc-transition);
}
.sc-breadcrumb a:hover {
    color: var(--sc-primary);
}
.sc-breadcrumb .breadcrumb-item.active {
    color: var(--sc-text);
    font-weight: 600;
}

/* ══════════════════════════════════════
   HEADER STRIP
══════════════════════════════════════ */
.sc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 8px;
}
.sc-header-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--sc-text);
    letter-spacing: -0.4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sc-header-count {
    background: var(--sc-bg);
    color: var(--sc-text-secondary);
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid var(--sc-border);
}
.sc-bogo-badge {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    letter-spacing: 0.3px;
}
.sc-bogo-warning {
    background: var(--sc-amber-soft);
    border: 1px solid #fde68a;
    color: #92400e;
    font-size: 12px;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: var(--sc-radius-sm);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ══════════════════════════════════════
   CART ITEM ROW
══════════════════════════════════════ */
.sc-item {
    background: var(--sc-card);
    border: 1px solid var(--sc-border);
    border-radius: var(--sc-radius);
    padding: 16px;
    margin-bottom: 12px;
    position: relative;
    transition: var(--sc-transition);
}
.sc-item:hover {
    border-color: #d1d5db;
    box-shadow: var(--sc-shadow);
}
.sc-item:last-child {
    margin-bottom: 0;
}
.sc-item-inner {
    display: flex;
    gap: 14px;
    align-items: flex-start;
}

/* Free ribbon */
.sc-free-ribbon {
    position: absolute;
    top: 0; left: 0;
    width: 72px; height: 72px;
    overflow: hidden;
    z-index: 2;
    pointer-events: none;
}
.sc-free-ribbon span {
    transform: rotate(-45deg);
    position: absolute;
    top: 13px; left: -20px;
    width: 100px;
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: #fff;
    text-align: center;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.2px;
    padding: 3px 0;
    box-shadow: 0 2px 6px rgba(22,163,74,0.35);
}

/* Image */
.sc-item-img {
    width: 90px;
    height: 80px;
    border-radius: var(--sc-radius-sm);
    background: #f9fafb;
    border: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
    transition: var(--sc-transition);
}
.sc-item-img img {
    max-width: 78px;
    max-height: 65px;
    object-fit: contain;
    transition: transform 0.3s ease;
}
.sc-item:hover .sc-item-img img {
    transform: scale(1.06);
}
.sc-item-img.membership-img {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border-color: #fde68a;
}

/* Details */
.sc-item-details {
    flex: 1;
    min-width: 0;
}
.sc-brand {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--sc-text-secondary);
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
    margin-bottom: 4px;
}
.sc-item-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--sc-text);
    line-height: 1.3;
    margin-bottom: 6px;
    letter-spacing: -0.2px;
}
.sc-size-tag {
    font-size: 10px;
    font-weight: 500;
    color: var(--sc-text-secondary);
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    padding: 1px 6px;
    border-radius: 4px;
    margin-left: 6px;
}

/* Lens pill */
.sc-lens-pill {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--sc-primary-soft);
    border: 1px solid #d5eded;
    border-radius: var(--sc-radius-sm);
    padding: 6px 10px;
    margin-top: 6px;
    font-size: 12px;
    gap: 8px;
}
.sc-lens-pill .sc-lens-name {
    font-weight: 600;
    color: var(--sc-text);
    display: flex;
    align-items: center;
    gap: 4px;
    min-width: 0;
}
.sc-lens-pill .sc-lens-name i {
    color: var(--sc-primary);
    font-size: 13px;
    flex-shrink: 0;
}
.sc-lens-pill .sc-lens-price {
    font-weight: 700;
    color: var(--sc-green);
    white-space: nowrap;
    flex-shrink: 0;
}
.sc-lens-detail {
    font-size: 11px;
    color: var(--sc-text-muted);
    margin-top: 2px;
    padding-left: 10px;
    line-height: 1.3;
}

/* Rx box */
.sc-rx-box {
    background: #fafafa;
    border: 1px solid #f0f0f0;
    border-radius: var(--sc-radius-sm);
    margin-top: 8px;
    overflow: hidden;
}
.sc-rx-header {
    padding: 7px 10px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    color: var(--sc-text);
    transition: background 0.15s;
}
.sc-rx-header:hover {
    background: #f3f4f6;
}
.sc-rx-header i {
    color: var(--sc-primary);
    font-size: 14px;
}
.sc-rx-header .sc-rx-toggle {
    font-size: 11px;
    font-weight: 600;
    color: var(--sc-primary);
    display: flex;
    align-items: center;
    gap: 3px;
}
.sc-rx-table {
    width: 100%;
    font-size: 11px;
    margin: 0;
    border-collapse: collapse;
}
.sc-rx-table th {
    background: var(--sc-primary-soft);
    color: var(--sc-text);
    font-weight: 700;
    text-align: center;
    padding: 5px 6px;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.sc-rx-table td {
    padding: 5px 6px;
    text-align: center;
    border-top: 1px solid #f3f4f6;
    font-weight: 500;
    color: var(--sc-text-secondary);
}
.sc-rx-table td:first-child {
    font-weight: 600;
    color: var(--sc-text);
}
.sc-rx-upload {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    font-size: 12px;
}
.sc-rx-upload i {
    font-size: 18px;
    color: var(--sc-blue);
}

/* Promo tags */
.sc-promo-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    margin-top: 6px;
}
.sc-promo-tag.green {
    background: var(--sc-green-soft);
    color: var(--sc-green);
    border: 1px solid #bbf7d0;
}
.sc-promo-tag.blue {
    background: var(--sc-blue-soft);
    color: var(--sc-blue);
    border: 1px solid #bfdbfe;
}
.sc-promo-tag.amber {
    background: var(--sc-amber-soft);
    color: var(--sc-amber);
    border: 1px solid #fde68a;
}

/* Price + Actions */
.sc-item-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: flex-start;
    min-width: 120px;
    flex-shrink: 0;
    gap: 6px;
}
.sc-price-block {
    text-align: right;
    margin-bottom: 2px;
}
.sc-price-old {
    font-size: 12px;
    color: var(--sc-text-muted);
    text-decoration: line-through;
    font-weight: 500;
    line-height: 1.1;
}
.sc-price-now {
    font-size: 18px;
    font-weight: 800;
    color: var(--sc-text);
    letter-spacing: -0.3px;
    line-height: 1.2;
}
.sc-price-now.green { color: var(--sc-green); }
.sc-price-now.blue { color: var(--sc-blue); }

/* Qty stepper */
.sc-qty {
    display: inline-flex;
    align-items: center;
    background: #f3f4f6;
    border: 1px solid var(--sc-border);
    border-radius: 8px;
    overflow: hidden;
    height: 30px;
}
.sc-qty button {
    width: 28px;
    height: 30px;
    border: none;
    background: transparent;
    color: var(--sc-text);
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--sc-transition);
}
.sc-qty button:hover {
    background: var(--sc-primary);
    color: #fff;
}
.sc-qty .sc-qty-val {
    font-weight: 700;
    font-size: 13px;
    min-width: 24px;
    text-align: center;
    color: var(--sc-text);
}

/* Action buttons */
.sc-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 2px;
}
.sc-btn-remove {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    border: 1px solid #fecaca;
    background: var(--sc-red-soft);
    color: var(--sc-red);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--sc-transition);
    font-size: 13px;
    flex-shrink: 0;
}
.sc-btn-remove:hover {
    background: var(--sc-red);
    color: #fff;
    border-color: var(--sc-red);
    transform: scale(1.05);
}
.sc-membership-badge {
    font-size: 11px;
    font-weight: 600;
    color: var(--sc-green);
    background: var(--sc-green-soft);
    border: 1px solid #bbf7d0;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.sc-free-qty-badge {
    font-size: 11px;
    font-weight: 700;
    color: var(--sc-green);
    background: var(--sc-green-soft);
    border: 1px solid #bbf7d0;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Membership item extras */
.sc-membership-desc {
    font-size: 12px;
    color: var(--sc-text-secondary);
    line-height: 1.4;
    margin-bottom: 6px;
}
.sc-membership-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
}
.sc-membership-actions a,
.sc-membership-actions button {
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.sc-membership-actions .sc-remove-link { color: var(--sc-red); }
.sc-membership-actions .sc-view-link { color: var(--sc-primary); }
.sc-membership-actions .sc-divider {
    color: #d1d5db;
    font-size: 10px;
}

/* ══════════════════════════════════════
   SIDEBAR
══════════════════════════════════════ */
.sc-sidebar {
    position: sticky;
    top: 20px;
}

/* Voucher perk */
.sc-voucher-perk {
    background: linear-gradient(135deg, #f5f3ff, #ede9fe);
    border: 1.5px solid var(--sc-purple);
    border-radius: var(--sc-radius);
    padding: 12px 14px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sc-voucher-perk-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--sc-purple);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.sc-voucher-perk-info {
    flex: 1;
    min-width: 0;
}
.sc-voucher-perk-title {
    font-size: 12px;
    font-weight: 700;
    color: #5b21b6;
}
.sc-voucher-perk-desc {
    font-size: 11px;
    color: var(--sc-text-secondary);
    line-height: 1.35;
}
.sc-voucher-perk .sc-btn-apply-quick {
    background: var(--sc-purple);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 4px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    flex-shrink: 0;
    transition: var(--sc-transition);
}
.sc-voucher-perk .sc-btn-apply-quick:hover {
    background: #6d28d9;
}

/* Sidebar card */
.sc-sidebar-card {
    background: var(--sc-card);
    border: 1px solid var(--sc-border);
    border-radius: var(--sc-radius);
    box-shadow: var(--sc-shadow);
    overflow: hidden;
    margin-bottom: 12px;
}
.sc-sidebar-section {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
}
.sc-sidebar-section:last-child {
    border-bottom: none;
}

/* Coupon section */
.sc-coupon-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    padding: 14px 16px;
    transition: background 0.15s;
    user-select: none;
}
.sc-coupon-header:hover {
    background: #fafafa;
}
.sc-coupon-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
    color: var(--sc-text);
}
.sc-coupon-title i {
    color: var(--sc-primary);
    font-size: 16px;
}
.sc-coupon-subtitle {
    font-size: 11px;
    color: var(--sc-text-muted);
    margin-top: 1px;
}
.sc-coupon-arrow {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 1px solid var(--sc-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: var(--sc-text-muted);
    transition: var(--sc-transition);
    flex-shrink: 0;
}
.sc-coupon-header:hover .sc-coupon-arrow {
    border-color: var(--sc-primary);
    color: var(--sc-primary);
}
.sc-coupon-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
    padding: 0 16px;
}
.sc-coupon-section.open .sc-coupon-body {
    max-height: 600px;
    padding: 0 16px 14px;
}
.sc-coupon-section.open .sc-coupon-arrow {
    transform: rotate(90deg);
}

.sc-input-row {
    display: flex;
    gap: 0;
    margin-bottom: 8px;
}
.sc-input-row input {
    flex: 1;
    border: 1px solid var(--sc-border);
    border-right: none;
    border-radius: var(--sc-radius-sm) 0 0 var(--sc-radius-sm);
    padding: 9px 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    outline: none;
    transition: border-color 0.2s;
    color: var(--sc-text);
    letter-spacing: 0.5px;
}
.sc-input-row input:focus {
    border-color: var(--sc-primary);
    box-shadow: inset 0 0 0 1px var(--sc-primary);
}
.sc-input-row input::placeholder {
    color: var(--sc-text-muted);
    font-weight: 500;
    text-transform: none;
    letter-spacing: 0;
}
.sc-input-row .sc-btn-apply {
    background: var(--sc-primary);
    color: #fff;
    border: none;
    border-radius: 0 var(--sc-radius-sm) var(--sc-radius-sm) 0;
    padding: 9px 16px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
    white-space: nowrap;
}
.sc-input-row .sc-btn-apply:hover {
    background: var(--sc-primary-dark);
}
.sc-applied-chip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--sc-green-soft);
    border-radius: 6px;
    padding: 6px 10px;
    margin-bottom: 8px;
    font-size: 11px;
    color: var(--sc-green);
    font-weight: 600;
}
.sc-applied-chip .sc-btn-remove-coupon {
    background: none;
    border: none;
    color: var(--sc-red);
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    padding: 0;
}
.sc-available-list {
    border-top: 1px solid #f3f4f6;
    padding-top: 8px;
    margin-top: 4px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.sc-available-label {
    font-size: 10px;
    font-weight: 600;
    color: var(--sc-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.sc-available-chip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f9fafb;
    border: 1.5px dashed #d1d5db;
    border-radius: var(--sc-radius-sm);
    padding: 8px 10px;
    cursor: pointer;
    transition: var(--sc-transition);
    font-size: 12px;
    width: 100%;
    text-align: left;
}
.sc-available-chip:hover {
    border-color: var(--sc-primary);
    background: var(--sc-primary-soft);
}
.sc-available-chip.voucher {
    background: #faf5ff;
    border-color: #c4b5fd;
}
.sc-available-chip.voucher:hover {
    background: var(--sc-purple-soft);
    border-color: var(--sc-purple);
}
.sc-available-chip .sc-chip-code {
    font-weight: 700;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}
.sc-available-chip .sc-chip-tag {
    font-size: 9px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    color: #fff;
    white-space: nowrap;
}

/* Loyalty section */
.sc-loyalty {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
}
.sc-loyalty-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sc-loyalty-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--sc-text);
    display: flex;
    align-items: center;
    gap: 6px;
}
.sc-loyalty-label i {
    color: var(--sc-primary);
    font-size: 16px;
}
.sc-loyalty-pts {
    font-size: 14px;
    font-weight: 800;
    color: var(--sc-primary);
}
.sc-loyalty-earn {
    font-size: 11px;
    color: var(--sc-text-muted);
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.sc-loyalty-earn i {
    color: var(--sc-green);
    font-size: 12px;
}
.sc-loyalty-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #f3f4f6;
}
.sc-loyalty-toggle input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--sc-primary);
    cursor: pointer;
    flex-shrink: 0;
}
.sc-loyalty-toggle label {
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: var(--sc-text);
}
.sc-loyalty-toggle .sc-loyalty-discount {
    color: var(--sc-green);
    font-weight: 700;
}

/* Bill summary */
.sc-bill {
    padding: 14px 16px;
}
.sc-bill-title {
    font-size: 14px;
    font-weight: 800;
    color: var(--sc-text);
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f3f4f6;
    letter-spacing: -0.2px;
}
.sc-bill-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 0;
    font-size: 12px;
}
.sc-bill-label {
    color: var(--sc-text-secondary);
    font-weight: 500;
}
.sc-bill-value {
    font-weight: 600;
    color: var(--sc-text);
}
.sc-bill-value.green { color: var(--sc-green); }
.sc-bill-value.amber { color: var(--sc-amber); }
.sc-bill-label.green { color: var(--sc-green); font-weight: 600; }
.sc-bill-label.blue { color: var(--sc-blue); font-weight: 600; }
.sc-bill-label.amber { color: var(--sc-amber); font-weight: 600; }
.sc-bill-divider {
    height: 1px;
    background: var(--sc-border);
    margin: 8px 0;
}
.sc-bill-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0 4px;
}
.sc-bill-total-label {
    font-size: 14px;
    font-weight: 700;
    color: var(--sc-text);
}
.sc-bill-total-label small {
    display: block;
    font-size: 10px;
    font-weight: 400;
    color: var(--sc-text-muted);
    margin-top: 1px;
}
.sc-bill-total-value {
    font-size: 22px;
    font-weight: 800;
    color: var(--sc-primary);
    letter-spacing: -0.5px;
}

/* Checkout CTA */
.sc-btn-checkout {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    background: linear-gradient(135deg, var(--sc-primary), var(--sc-primary-dark));
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 13px 20px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--sc-transition);
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(50,154,154,0.3);
    gap: 8px;
    letter-spacing: 0.2px;
}
.sc-btn-checkout:hover {
    background: linear-gradient(135deg, #2db0b0, #1f6d6d);
    box-shadow: 0 6px 20px rgba(50,154,154,0.4);
    transform: translateY(-1px);
    color: #fff;
}

/* Cashback banner */
.sc-cashback {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: var(--sc-green-soft);
    border: 1px dashed #86efac;
    border-radius: var(--sc-radius-sm);
    padding: 7px 10px;
    margin-top: 10px;
    font-size: 11px;
    font-weight: 600;
    color: var(--sc-green);
}

/* Trust badges */
.sc-trust {
    display: flex;
    justify-content: space-around;
    padding: 10px 0 0;
    margin-top: 10px;
    border-top: 1px solid #f3f4f6;
}
.sc-trust-item {
    text-align: center;
    font-size: 10px;
    color: var(--sc-text-muted);
    font-weight: 500;
}
.sc-trust-item i {
    display: block;
    font-size: 16px;
    color: var(--sc-primary);
    margin-bottom: 2px;
}

/* ══════════════════════════════════════
   GOLD MEMBERSHIP BANNER
══════════════════════════════════════ */
.sc-gold-banner {
    border-radius: var(--sc-radius);
    padding: 14px 16px;
    margin-bottom: 12px;
    position: relative;
    overflow: hidden;
}
.sc-gold-banner.state1 {
    background: linear-gradient(135deg, #fffbeb, #fef3c7, #ffffff);
    border: 1.5px solid #f59e0b;
}
.sc-gold-banner.state1::after {
    content: '';
    position: absolute;
    top: -40%;
    right: -15%;
    width: 120px;
    height: 120px;
    background: rgba(245,158,11,0.08);
    border-radius: 50%;
    pointer-events: none;
}
.sc-gold-banner.state2 {
    background: #fff9db;
    border: 1.5px solid #ffd8a8;
}
.sc-gold-banner.state3 {
    background: var(--sc-amber-soft);
    border: 1.5px solid #fde68a;
}
.sc-gold-tag {
    background: var(--sc-amber);
    color: #fff;
    font-size: 9px;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.sc-gold-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--sc-text);
    line-height: 1.35;
    margin-bottom: 2px;
}
.sc-gold-subtitle {
    font-size: 11.5px;
    color: #92400e;
    font-weight: 500;
    line-height: 1.35;
    margin-bottom: 10px;
}
.sc-gold-cta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 8px;
    border-top: 1px dashed #fde68a;
}
.sc-gold-btn {
    background: transparent;
    border: none;
    font-size: 13px;
    font-weight: 700;
    color: #b45309;
    cursor: pointer;
    padding: 0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: var(--sc-transition);
}
.sc-gold-btn:hover {
    color: #92400e;
}
.sc-gold-arrow {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--sc-amber);
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(217,119,6,0.25);
    transition: var(--sc-transition);
}
.sc-gold-arrow:hover {
    background: #b45309;
    transform: scale(1.06);
}

/* ══════════════════════════════════════
   EMPTY CART
══════════════════════════════════════ */
.sc-empty {
    background: var(--sc-card);
    border: 1px solid var(--sc-border);
    border-radius: var(--sc-radius);
    padding: 48px 24px;
    text-align: center;
    max-width: 480px;
    margin: 40px auto;
    box-shadow: var(--sc-shadow);
}
.sc-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--sc-primary-soft);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}
.sc-empty-icon i {
    font-size: 32px;
    color: var(--sc-primary);
}
.sc-empty h3 {
    font-size: 20px;
    font-weight: 800;
    color: var(--sc-text);
    margin-bottom: 8px;
    letter-spacing: -0.3px;
}
.sc-empty p {
    font-size: 14px;
    color: var(--sc-text-secondary);
    margin-bottom: 24px;
    line-height: 1.5;
}
.sc-btn-explore {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--sc-primary), var(--sc-primary-dark));
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 12px 28px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(50,154,154,0.3);
    transition: var(--sc-transition);
}
.sc-btn-explore:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(50,154,154,0.4);
    color: #fff;
}

/* ══════════════════════════════════════
   MOBILE BOTTOM BAR
══════════════════════════════════════ */
.sc-mobile-bar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--sc-card);
    border-top: 1px solid var(--sc-border);
    box-shadow: 0 -4px 16px rgba(0,0,0,0.1);
    padding: 12px 16px;
    z-index: 1000;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.sc-mobile-bar-price {
    flex-shrink: 0;
}
.sc-mobile-bar-price .sc-total-label {
    font-size: 11px;
    color: var(--sc-text-muted);
    font-weight: 500;
}
.sc-mobile-bar-price .sc-total-value {
    font-size: 20px;
    font-weight: 800;
    color: var(--sc-primary);
    letter-spacing: -0.5px;
    line-height: 1.2;
}
.sc-mobile-bar .sc-btn-checkout {
    flex: 1;
    max-width: 220px;
    padding: 12px 16px;
    font-size: 13px;
}

/* ══════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════ */
@media (max-width: 991px) {
    .sc-sidebar {
        position: static;
    }
    .sc-header-title {
        font-size: 18px;
    }
    .sc-page {
        padding-bottom: 80px;
    }
    .sc-mobile-bar {
        display: flex;
    }
    .sc-desktop-checkout {
        display: none;
    }
}

@media (max-width: 575px) {
    .sc-page {
        padding-bottom: 80px;
    }
    .sc-item {
        padding: 12px;
    }
    .sc-item-inner {
        flex-wrap: wrap;
    }
    .sc-item-img {
        width: 72px;
        height: 66px;
    }
    .sc-item-img img {
        max-width: 62px;
        max-height: 52px;
    }
    .sc-item-right {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        min-width: 0;
        padding-top: 8px;
        margin-top: 4px;
        border-top: 1px solid #f3f4f6;
    }
    .sc-price-block {
        text-align: left;
    }
    .sc-price-now {
        font-size: 16px;
    }
    .sc-header-title {
        font-size: 16px;
    }
    .sc-bill-total-value {
        font-size: 20px;
    }
    .sc-steps {
        gap: 0;
    }
    .sc-step-line {
        width: 28px;
        margin: 0 6px;
    }
    .sc-step {
        font-size: 11px;
    }
    .sc-step-num {
        width: 22px;
        height: 22px;
        font-size: 10px;
    }
    .sc-empty {
        padding: 32px 16px;
        margin: 20px 12px;
    }
}

/* Animation — fade items in */
@keyframes scFadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
.sc-item {
    animation: scFadeUp 0.35s ease both;
}
.sc-item:nth-child(2) { animation-delay: 0.06s; }
.sc-item:nth-child(3) { animation-delay: 0.12s; }
.sc-item:nth-child(4) { animation-delay: 0.18s; }
.sc-item:nth-child(5) { animation-delay: 0.24s; }
</style>


{{-- ═══ BREADCRUMBS SECTION ═══ --}}
<div class="sc-breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb sc-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products') }}">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
            </ol>
        </nav>
    </div>
</div>


{{-- ═══ MAIN CART SECTION ═══ --}}
<section class="sc-page py-4">
    <div class="container">
        @if(empty($cartData['items']) || count($cartData['items']) == 0)

            {{-- ═══ EMPTY CART ═══ --}}
            <div class="sc-empty">
                <div class="sc-empty-icon">
                    <i class="bi bi-cart-x"></i>
                </div>
                <h3>Your Cart is Empty</h3>
                <p>Looks like you haven't added any prescription glasses or sunglasses yet.</p>
                <a href="{{ route('products') }}" class="sc-btn-explore">
                    <i class="bi bi-bag-plus"></i> Explore Eyewear
                </a>
            </div>

        @else

            <div class="row g-3 g-lg-4">

                {{-- ═══════════════════════════════════
                     LEFT COLUMN — CART ITEMS
                ═══════════════════════════════════ --}}
                <div class="col-lg-8">

                    {{-- Header --}}
                    <div class="sc-header">
                        <div class="sc-header-title">
                            Shopping Cart
                            <span class="sc-header-count">{{ $cartData['item_count'] }} {{ Str::plural('item', $cartData['item_count']) }}</span>
                        </div>
                        @if($cartData['is_bogo_active'])
                            <span class="sc-bogo-badge">
                                <i class="bi bi-award-fill"></i> BOGO Active
                            </span>
                        @endif
                    </div>

                    @if(!empty($cartData['bogo_fallback_message']))
                        <div class="sc-bogo-warning">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            {{ Str::replace(['⚠️', '👑', '★'], '', $cartData['bogo_fallback_message']) }}
                        </div>
                    @endif

                    {{-- Items --}}
                    <div class="sc-items-list">
                        @foreach($cartData['items'] as $item)
                            <div class="sc-item data-key-item" data-key="{{ $item['key'] }}">

                                {{-- FREE Ribbon --}}
                                @if(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                    <div class="sc-free-ribbon"><span>FREE</span></div>
                                @endif

                                <div class="sc-item-inner">

                                    {{-- Image --}}
                                    <div class="sc-item-img {{ (isset($item['is_membership']) && $item['is_membership']) ? 'membership-img' : '' }}">
                                        @if(isset($item['is_membership']) && $item['is_membership'])
                                            @if(!empty($item['frame_image']) && (Str::startsWith($item['frame_image'], 'http') || Str::startsWith($item['frame_image'], '/')))
                                                <img src="{{ $item['frame_image'] }}" alt="{{ $item['frame_name'] }}" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                                <div class="d-none d-flex align-items-center justify-content-center w-100 h-100">
                                                    <i class="bi bi-award-fill text-warning" style="font-size:28px;"></i>
                                                </div>
                                            @else
                                                <i class="bi bi-award-fill text-warning" style="font-size:28px;"></i>
                                            @endif
                                        @else
                                            <img src="{{ $item['frame_image'] }}" alt="{{ $item['frame_name'] }}" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Sunglasses1.png') }}';">
                                        @endif
                                    </div>

                                    {{-- Details --}}
                                    <div class="sc-item-details">
                                        @if(isset($item['is_membership']) && $item['is_membership'])
                                            {{-- Membership Item --}}
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <div>
                                                    <span class="sc-brand" style="background:#fef3c7; color:#92400e;">
                                                        <i class="bi bi-award-fill" style="font-size:9px;"></i> GOLD VIP
                                                    </span>
                                                    <div class="sc-item-name">{{ $item['frame_name'] }}</div>
                                                </div>
                                                <div class="sc-price-block">
                                                    <div class="sc-price-old">₹{{ number_format($item['membership_mrp'] ?? 6000, 0) }}</div>
                                                    <div class="sc-price-now">₹{{ number_format($item['frame_price'], 0) }}</div>
                                                </div>
                                            </div>
                                            <p class="sc-membership-desc mb-2">
                                                Buy 1 Get 1 Free On Over 5000+ Items, Applicable Everywhere for 1 Full Year
                                            </p>
                                            <div class="sc-membership-actions">
                                                <form action="{{ route('cart.remove_membership') }}" method="POST" class="d-inline m-0 p-0">
                                                    @csrf
                                                    <button type="submit" class="sc-remove-link">
                                                        <i class="bi bi-trash3"></i> Remove
                                                    </button>
                                                </form>
                                                <span class="sc-divider">|</span>
                                                <a href="{{ route('website.membership') }}" class="sc-view-link">
                                                    View Benefits <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        @else
                                            {{-- Regular Eyewear --}}
                                            {{-- Top Row: Brand & Name on Left, Price on Right --}}
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <div>
                                                    <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                                        @if(!empty($item['brand']))
                                                            <span class="sc-brand">{{ $item['brand'] }}</span>
                                                        @endif
                                                        @if(!empty($item['product_type']))
                                                            <span class="sc-size-tag" style="background:#e0f2f1; color:#07484A; font-weight:600;">
                                                                <i class="bi bi-eyeglasses me-1" style="font-size:10px;"></i>{{ ucfirst($item['product_type']) }}
                                                            </span>
                                                        @endif
                                                        @if(!empty($item['size']))
                                                            <span class="sc-size-tag">Size: {{ $item['size'] }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="sc-item-name">{{ $item['frame_name'] }}</div>
                                                </div>

                                                <div class="sc-price-block flex-shrink-0 text-end">
                                                    @if(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                                        @php
                                                            $itemQty = (int)($item['quantity'] ?? 1);
                                                            $totalLineMrp = ($item['frame_price'] + $item['lens_price']) * $itemQty;
                                                            // 1 unit is free
                                                            $totalLinePayable = ($item['frame_price'] * max(0, $itemQty - 1)) + ($item['lens_price'] * $itemQty);
                                                        @endphp
                                                        <div class="sc-price-old">₹{{ number_format($totalLineMrp, 0) }}</div>
                                                        <div class="sc-price-now green">₹{{ number_format($totalLinePayable, 0) }}</div>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-block mt-1" style="font-size: 10px; padding: 2px 6px;">
                                                            <i class="bi bi-gift-fill me-1"></i>{{ $itemQty > 1 ? '1 Free + ' . ($itemQty - 1) . ' Paid' : '100% Free Frame' }}
                                                        </span>
                                                    @elseif(isset($item['is_bogo_third_discount']) && $item['is_bogo_third_discount'])
                                                        @php
                                                            $pct = (float)($item['bogo_third_discount_percent'] ?? 60);
                                                            $discountedFramePrice = $item['frame_price'] * (1 - ($pct / 100));
                                                        @endphp
                                                        <div class="sc-price-old">₹{{ number_format($item['frame_price'], 0) }}</div>
                                                        <div class="sc-price-now blue">₹{{ number_format($discountedFramePrice + $item['lens_price'], 0) }}</div>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-block mt-1" style="font-size: 10px; padding: 2px 6px;">
                                                            {{ (int)$pct }}% OFF on 3rd Pair
                                                        </span>
                                                    @elseif(isset($item['is_bogo_half']) && $item['is_bogo_half'])
                                                        <div class="sc-price-old">₹{{ number_format($item['frame_price'], 0) }}</div>
                                                        <div class="sc-price-now green">₹{{ number_format(($item['frame_price'] * 0.5) + $item['lens_price'], 0) }}</div>
                                                    @elseif(isset($item['is_first_frame_free_applied']) && $item['is_first_frame_free_applied'])
                                                        <div class="sc-price-old">₹{{ number_format($item['frame_price'], 0) }}</div>
                                                        <div class="sc-price-now green">₹{{ number_format($item['lens_price'], 0) }}</div>
                                                    @else
                                                        <div class="sc-price-now">₹{{ number_format(($item['frame_price'] + $item['lens_price']) * ($item['quantity'] ?? 1), 0) }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Mid Row: Lens Pill on Left, Actions (Qty Stepper & Remove Button) in ONE line on Right --}}
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                                                <div class="sc-lens-pill my-0 flex-grow-1" style="margin-top:0 !important;">
                                                    <span class="sc-lens-name">
                                                        <i class="bi bi-layers-fill"></i>
                                                        @php
                                                            // FIX: Show contextual prescription info for Reading Glasses & Contact Lenses
                                                            // instead of always showing the generic "Basic / Frame Only" lens_name.
                                                            $rxRaw = $item['prescription_data'] ?? null;
                                                            $rx    = is_string($rxRaw) ? json_decode($rxRaw, true) : (is_array($rxRaw) ? $rxRaw : null);
                                                            $rxType = $rx['type'] ?? null;
                                                        @endphp

                                                        @if($rxType === 'contact_lens_manual' && isset($rx['right'], $rx['left']))
                                                            {{-- Contact Lens: show eye-by-eye SPH & box breakdown --}}
                                                            <span class="d-inline-flex flex-wrap gap-1" style="white-space:normal;">
                                                                @if(!empty($rx['right']['sph']) && ($rx['right']['boxes'] ?? 0) > 0)
                                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:10px;">
                                                                        R: {{ $rx['right']['sph'] }} ({{ $rx['right']['boxes'] }} Box)
                                                                    </span>
                                                                @endif
                                                                @if(!empty($rx['left']['sph']) && ($rx['left']['boxes'] ?? 0) > 0)
                                                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">
                                                                        L: {{ $rx['left']['sph'] }} ({{ $rx['left']['boxes'] }} Box)
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        @elseif($rxType === 'contact_lens_zero')
                                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" style="font-size:10px;">
                                                                Zero Power ({{ $rx['total_boxes'] ?? $item['quantity'] }} Box)
                                                            </span>
                                                        @elseif($rxType === 'contact_lens_later')
                                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size:10px;">
                                                                Power Later ({{ $rx['total_boxes'] ?? $item['quantity'] }} Box)
                                                            </span>
                                                        @elseif(!empty($rx['reading_power']))
                                                            {{-- Reading Glasses: show selected power chip --}}
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:11px;">
                                                                Reading Power: {{ $rx['reading_power'] }}
                                                            </span>
                                                        @else
                                                            {{-- Standard / Powered eyeglass lens package --}}
                                                            <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                                {{ $item['lens_name'] }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                    <span class="sc-lens-price">
                                                        {{ $item['lens_price'] > 0 ? '+₹' . number_format($item['lens_price'], 0) : 'Included' }}
                                                    </span>
                                                </div>

                                                <div class="sc-actions ms-auto flex-shrink-0">
                                                    @if(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                                        <span class="sc-free-qty-badge">
                                                            <i class="bi bi-gift-fill"></i> Qty: 1
                                                        </span>
                                                        <button type="button" class="sc-btn-remove remove-cart-item" data-key="{{ $item['key'] }}" title="Remove">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    @else
                                                        <div class="sc-qty">
                                                            <button type="button" class="qty-minus" data-key="{{ $item['key'] }}">−</button>
                                                            <span class="sc-qty-val item-qty">{{ $item['quantity'] }}</span>
                                                            <button type="button" class="qty-plus" data-key="{{ $item['key'] }}">+</button>
                                                        </div>
                                                        <button type="button" class="sc-btn-remove remove-cart-item" data-key="{{ $item['key'] }}" title="Remove">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>

                                            @if(!empty($item['lens_details']))
                                                <div class="sc-lens-detail mt-1">{{ Str::limit($item['lens_details'], 60) }}</div>
                                            @endif

                                            {{-- Prescription --}}
                                            @php
                                                $rx = null;
                                                if (!empty($item['prescription_data'])) {
                                                    if (is_array($item['prescription_data'])) {
                                                        $rx = $item['prescription_data'];
                                                    } elseif (is_string($item['prescription_data'])) {
                                                        $rx = json_decode($item['prescription_data'], true);
                                                    }
                                                }
                                            @endphp

                                            @if(!empty($rx))
                                                <div class="sc-rx-box">
                                                    <div class="sc-rx-header" data-bs-toggle="collapse" data-bs-target="#sc-rx-{{ $loop->index }}" aria-expanded="true">
                                                        <span><i class="bi bi-eye-fill me-1"></i> Prescription</span>
                                                        <span class="sc-rx-toggle">View <i class="bi bi-chevron-down"></i></span>
                                                    </div>
                                                    <div class="collapse show" id="sc-rx-{{ $loop->index }}">
                                                        @if(isset($rx['type']) && ($rx['type'] === 'upload' || $rx['type'] === 'contact_lens_upload'))
                                                            @php
                                                                $filePath = $rx['file'] ?? ($item['prescription_file'] ?? null);
                                                                $fileName = !empty($filePath) ? basename($filePath) : 'Prescription Document';
                                                                $fileExt  = !empty($filePath) ? strtoupper(pathinfo($filePath, PATHINFO_EXTENSION)) : 'FILE';
                                                                $isClUpload = $rx['type'] === 'contact_lens_upload';
                                                            @endphp
                                                            <div class="p-2.5 m-2 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #ffffff; border-color: #e2e8f0 !important;">
                                                                <div class="d-flex align-items-center gap-2.5">
                                                                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #e6f9f4; color: #00a297; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                                                        <i class="bi bi-file-earmark-medical-fill"></i>
                                                                    </div>
                                                                    <div>
                                                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                                            <span class="fw-bold" style="font-size: 12.5px; color: #0d1430;">Prescription Attached</span>
                                                                            @if(!empty($fileExt) && $fileExt !== 'FILE')
                                                                                <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 9.5px; font-weight: 600; padding: 2px 6px;">{{ $fileExt }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-muted text-truncate" style="font-size: 11px; max-width: 220px;" title="{{ $fileName }}">
                                                                            {{ $fileName }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if(!empty($filePath))
                                                                    <a href="{{ asset($filePath) }}" target="_blank" class="btn btn-sm d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-2 shadow-none" style="background: #00a297; color: #ffffff; font-size: 11.5px; font-weight: 600; text-decoration: none; border: none; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                                                        <i class="bi bi-eye-fill"></i> View Prescription
                                                                    </a>
                                                                @else
                                                                    <span class="badge bg-light text-muted" style="font-size: 10.5px;">File uploaded</span>
                                                                @endif
                                                            </div>

                                                            @if($isClUpload && (!empty($rx['right']['boxes']) || !empty($rx['left']['boxes'])))
                                                                <div class="px-3 pb-2.5">
                                                                    <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 11.5px;">
                                                                        @if(!empty($rx['right']['boxes']))
                                                                            <span><strong style="color: #0d1430;">Right Eye:</strong> {{ $rx['right']['boxes'] }} Box(es)</span>
                                                                        @endif
                                                                        @if(!empty($rx['left']['boxes']))
                                                                            <span><strong style="color: #0d1430;">Left Eye:</strong> {{ $rx['left']['boxes'] }} Box(es)</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div style="padding: 4px;">
                                                                @php
                                                                    $isClManual = isset($rx['type']) && $rx['type'] === 'contact_lens_manual';
                                                                    $isClZero   = isset($rx['type']) && $rx['type'] === 'contact_lens_zero';
                                                                    $isClLater  = isset($rx['type']) && $rx['type'] === 'contact_lens_later';

                                                                    $rSph = $isClManual ? ($rx['right']['sph'] ?? '-') : ($isClZero ? '0.00' : ($rx['right_eye_sph'] ?? '-'));
                                                                    $lSph = $isClManual ? ($rx['left']['sph'] ?? '-') : ($isClZero ? '0.00' : ($rx['left_eye_sph'] ?? '-'));

                                                                    $rBoxes = $isClManual ? ($rx['right']['boxes'] ?? 0) : ($isClZero ? ($rx['total_boxes'] ?? $item['quantity']) : '-');
                                                                    $lBoxes = $isClManual ? ($rx['left']['boxes'] ?? 0) : ($isClZero ? '-' : '-');

                                                                    $hasCylPower = (!$isClManual && !$isClZero) && (
                                                                        (!empty($rx['right_eye_cyl']) && $rx['right_eye_cyl'] !== '0.00' && $rx['right_eye_cyl'] !== '0') ||
                                                                        (!empty($rx['left_eye_cyl']) && $rx['left_eye_cyl'] !== '0.00' && $rx['left_eye_cyl'] !== '0') ||
                                                                        (!empty($rx['right_eye_axis']) && $rx['right_eye_axis'] != '0') ||
                                                                        (!empty($rx['left_eye_axis']) && $rx['left_eye_axis'] != '0')
                                                                    );
                                                                @endphp

                                                                @if($isClLater)
                                                                    <div class="text-muted p-2" style="font-size:12px; font-style:italic;">
                                                                        <i class="bi bi-clock-history me-1"></i> Power will be submitted later
                                                                    </div>
                                                                @else
                                                                    <table class="sc-rx-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>EYE</th>
                                                                                <th>SPH</th>
                                                                                @if($hasCylPower)
                                                                                    <th>CYL</th>
                                                                                    <th>AXIS</th>
                                                                                @endif
                                                                                <th>Boxes</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>R</td>
                                                                                <td>{{ $rSph ?: '-' }}</td>
                                                                                @if($hasCylPower)
                                                                                    <td>{{ !empty($rx['right_eye_cyl']) ? $rx['right_eye_cyl'] : '-' }}</td>
                                                                                    <td>{{ !empty($rx['right_eye_axis']) && $rx['right_eye_axis'] != '0' ? $rx['right_eye_axis'] : '-' }}</td>
                                                                                @endif
                                                                                <td>{{ $rBoxes !== null && $rBoxes !== '' ? $rBoxes : '-' }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>L</td>
                                                                                <td>{{ $lSph ?: '-' }}</td>
                                                                                @if($hasCylPower)
                                                                                    <td>{{ !empty($rx['left_eye_cyl']) ? $rx['left_eye_cyl'] : '-' }}</td>
                                                                                    <td>{{ !empty($rx['left_eye_axis']) && $rx['left_eye_axis'] != '0' ? $rx['left_eye_axis'] : '-' }}</td>
                                                                                @endif
                                                                                <td>{{ $lBoxes !== null && $lBoxes !== '' ? $lBoxes : '-' }}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Promo badges --}}
                                            @if(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                                <div class="sc-promo-tag green">
                                                    <i class="bi bi-check-circle-fill"></i> Free with Gold Membership!
                                                </div>
                                            @elseif(isset($item['is_bogo_third_discount']) && $item['is_bogo_third_discount'])
                                                <div class="sc-promo-tag blue">
                                                    <i class="bi bi-percent"></i> {{ (int)($item['bogo_third_discount_percent'] ?? 60) }}% OFF on 3rd Pair
                                                </div>
                                            @elseif(isset($item['is_bogo_half']) && $item['is_bogo_half'])
                                                <div class="sc-promo-tag amber">
                                                    <i class="bi bi-percent"></i> Frame 50% OFF (BOGO)
                                                </div>
                                            @elseif(isset($item['is_first_frame_free_applied']) && $item['is_first_frame_free_applied'])
                                                <div class="sc-promo-tag green">
                                                    <i class="bi bi-gift-fill"></i> Frame Free (First Pair Promo)
                                                </div>
                                            @endif
                                        @endif
                                    </div>


                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>


                {{-- ═══════════════════════════════════
                     RIGHT COLUMN — SIDEBAR
                ═══════════════════════════════════ --}}
                <div class="col-lg-4">
                    <div class="sc-sidebar">

                        {{-- @if(!empty($cartData['gift_voucher_perk']))
                            @php
                                $perk         = $cartData['gift_voucher_perk'];
                                $perkIsManual = ($perk['delivery_type'] ?? 'auto') === 'manual';
                                $perkIsAuto   = !$perkIsManual;
                            @endphp
                            <div class="sc-voucher-perk">
                                <div class="sc-voucher-perk-icon"><i class="bi bi-gift-fill"></i></div>
                                <div class="sc-voucher-perk-info">
                                    <div class="sc-voucher-perk-title">{{ $perk['name'] }}</div>
                                    <div class="sc-voucher-perk-desc">
                                        @if($perkIsAuto)
                                            Earn <strong>₹{{ number_format($perk['voucher_value']) }}</strong> voucher on next order
                                        @else
                                            @if(!empty($perk['description']))
                                                {{ Str::limit($perk['description'], 55) }}
                                            @else
                                                Apply <strong>{{ $perk['coupon_code'] }}</strong> for ₹{{ number_format($perk['voucher_value']) }} off
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                @if($perkIsManual && !empty($perk['coupon_code']))
                                    <button type="button" class="sc-btn-apply-quick apply-quick-voucher" data-code="{{ $perk['coupon_code'] }}">Apply</button>
                                @endif
                            </div>
                        @endif --}}

                        {{-- Main Sidebar Card --}}
                        <div class="sc-sidebar-card">

                            {{-- Coupon / Voucher Section --}}
                            @php
                                $appliedCoupon    = session('applied_coupon', null);
                                $appliedVoucher   = session('applied_voucher', null);
                                $appliedCode      = $appliedCoupon['code'] ?? ($appliedVoucher['code'] ?? null);
                                $availableCoupons = $cartData['available_coupons'] ?? [];
                                $savedVouchers    = $cartData['available_vouchers'] ?? [];
                            @endphp

                            <div class="sc-coupon-section {{ $appliedCode ? 'open' : '' }}" id="sc-coupon-section">
                                <div class="sc-coupon-header" onclick="document.getElementById('sc-coupon-section').classList.toggle('open')">
                                    <div>
                                        <div class="sc-coupon-title">
                                            <i class="bi bi-ticket-perforated-fill"></i>
                                            @if($appliedCode)
                                                <strong>{{ $appliedCode }}</strong> Applied
                                            @else
                                                Apply Coupon / Voucher
                                            @endif
                                        </div>
                                        <div class="sc-coupon-subtitle">
                                            {{ $appliedCode ? 'Tap to change or remove' : 'Check available offers' }}
                                        </div>
                                    </div>
                                    <div class="sc-coupon-arrow"><i class="bi bi-chevron-right"></i></div>
                                </div>
                                <div class="sc-coupon-body">
                                    <div class="sc-input-row">
                                        <input type="text" id="coupon-code-input" placeholder="Enter coupon code" value="{{ $appliedCode ?? '' }}">
                                        <button id="apply-coupon-btn" class="sc-btn-apply" type="button">APPLY</button>
                                    </div>

                                    @if($appliedCode)
                                        <div class="sc-applied-chip">
                                            <span><i class="bi bi-check-circle-fill me-1"></i> <strong>{{ $appliedCode }}</strong> applied!</span>
                                            <button type="button" id="remove-coupon-btn" class="sc-btn-remove-coupon">Remove</button>
                                        </div>
                                    @endif

                                    @if((!empty($availableCoupons) || !empty($savedVouchers)) && !$appliedCode)
                                        <div class="sc-available-list">
                                            <div class="sc-available-label">
                                                <i class="bi bi-stars text-warning"></i> Available offers
                                            </div>
                                            @foreach($savedVouchers as $v)
                                                <button type="button" class="sc-available-chip voucher apply-quick-coupon" data-code="{{ $v['code'] }}">
                                                    <span class="sc-chip-code" style="color:#5b21b6;">
                                                        <i class="bi bi-gift-fill"></i> {{ $v['code'] }}
                                                    </span>
                                                    <span class="sc-chip-tag" style="background:var(--sc-purple);">₹{{ number_format($v['balance'], 0) }}</span>
                                                </button>
                                            @endforeach
                                            @foreach($availableCoupons as $ac)
                                                <button type="button" class="sc-available-chip apply-quick-coupon" data-code="{{ $ac['code'] }}">
                                                    <span class="sc-chip-code" style="color:var(--sc-primary);">
                                                        <i class="bi bi-ticket-fill"></i> {{ $ac['code'] }}
                                                    </span>
                                                    <span class="sc-chip-tag" style="background:var(--sc-primary);">{{ $ac['title'] }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Loyalty Points --}}
                            @php
                                $userBal       = (int)($cartData['available_loyalty_points'] ?? 0);
                                $orderReward   = (int)($cartData['order_reward_pts'] ?? 0);
                                $deliveryDate  = $cartData['cashback_release_date'] ?? '';
                                $useLoyalty    = !empty($cartData['use_loyalty_points']);
                                $ptsUsed       = (int)($cartData['points_used'] ?? 0);
                                $pointVal      = (float)($cartData['point_value'] ?? 1.0);
                                $loyaltyRupees = $useLoyalty ? ($cartData['loyalty_discount'] ?? 0) : ($userBal * $pointVal);
                            @endphp
                            <div class="sc-loyalty">
                                <div class="sc-loyalty-top">
                                    <span class="sc-loyalty-label">
                                        <i class="bi bi-stars"></i> Loyalty Points
                                    </span>
                                    <span class="sc-loyalty-pts">{{ number_format($userBal) }} pts</span>
                                </div>
                                <div class="sc-loyalty-earn">
                                    <i class="bi bi-gift-fill"></i>
                                    Earn <strong class="mx-1">{{ number_format($orderReward) }} pts</strong> after delivery · by {{ $deliveryDate }}
                                </div>
                                @if($userBal > 0)
                                    <div class="sc-loyalty-toggle">
                                        <input type="checkbox" id="toggle-loyalty-checkbox" {{ $useLoyalty ? 'checked' : '' }}>
                                        <label for="toggle-loyalty-checkbox">
                                            Use {{ number_format($useLoyalty && $ptsUsed > 0 ? $ptsUsed : $userBal) }} pts
                                            <span class="sc-loyalty-discount">(−₹{{ number_format($loyaltyRupees, 2) }})</span>
                                        </label>
                                    </div>
                                @endif
                            </div>

                            {{-- Bill Summary --}}
                            <div class="sc-bill">
                                <div class="sc-bill-title">Bill Summary</div>

                                <div class="sc-bill-line">
                                    <span class="sc-bill-label">Frame Subtotal</span>
                                    <span class="sc-bill-value">₹{{ number_format($cartData['frame_subtotal'], 2) }}</span>
                                </div>
                                <div class="sc-bill-line">
                                    <span class="sc-bill-label">Lens Package</span>
                                    <span class="sc-bill-value green">+₹{{ number_format($cartData['lens_subtotal'], 2) }}</span>
                                </div>

                                @if($cartData['bogo_savings'] > 0)
                                    <div class="sc-bill-line">
                                        <span class="sc-bill-label green"><i class="bi bi-tag-fill me-1"></i>BOGO Savings</span>
                                        <span class="sc-bill-value green">-₹{{ number_format($cartData['bogo_savings'], 2) }}</span>
                                    </div>
                                @endif
                                @if(isset($cartData['third_item_savings']) && $cartData['third_item_savings'] > 0)
                                    <div class="sc-bill-line">
                                        <span class="sc-bill-label blue"><i class="bi bi-percent me-1"></i>3rd Pair Savings</span>
                                        <span class="sc-bill-value" style="color:var(--sc-blue);">-₹{{ number_format($cartData['third_item_savings'], 2) }}</span>
                                    </div>
                                @endif
                                @if(isset($cartData['first_frame_free_save']) && $cartData['first_frame_free_save'] > 0)
                                    <div class="sc-bill-line">
                                        <span class="sc-bill-label green"><i class="bi bi-gift-fill me-1"></i>First Pair Free</span>
                                        <span class="sc-bill-value green">-₹{{ number_format($cartData['first_frame_free_save'], 2) }}</span>
                                    </div>
                                @endif
                                @if($cartData['coupon_discount'] > 0)
                                    <div class="sc-bill-line">
                                        <span class="sc-bill-label green"><i class="bi bi-percent me-1"></i>Coupon</span>
                                        <span class="sc-bill-value green">-₹{{ number_format($cartData['coupon_discount'], 2) }}</span>
                                    </div>
                                @endif
                                @if(isset($cartData['voucher_discount']) && $cartData['voucher_discount'] > 0)
                                    <div class="sc-bill-line">
                                        <span class="sc-bill-label green"><i class="bi bi-gift-fill me-1"></i>Voucher</span>
                                        <span class="sc-bill-value green">-₹{{ number_format($cartData['voucher_discount'], 2) }}</span>
                                    </div>
                                @endif
                                @if(isset($cartData['loyalty_discount']) && $cartData['loyalty_discount'] > 0)
                                    <div class="sc-bill-line">
                                        <span class="sc-bill-label amber"><i class="bi bi-gem me-1"></i>Loyalty Pts</span>
                                        <span class="sc-bill-value amber">-₹{{ number_format($cartData['loyalty_discount'], 2) }}</span>
                                    </div>
                                @endif

                                <div class="sc-bill-divider"></div>

                                <div class="sc-bill-total">
                                    <div class="sc-bill-total-label">
                                        Total Amount
                                        <small>Inclusive of all taxes</small>
                                    </div>
                                    <span class="sc-bill-total-value">₹{{ number_format($cartData['grand_total'], 2) }}</span>
                                </div>

                                {{-- Desktop Checkout Button --}}
                                <div class="sc-desktop-checkout">
                                    @if(auth()->check())
                                        <a href="{{ route('shipping-details') }}" class="sc-btn-checkout">
                                            <span>Proceed to Checkout</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    @else
                                        <button type="button" id="btn-proceed-checkout-auth" class="sc-btn-checkout">
                                            <span>Proceed to Checkout</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                var checkoutBtn = document.getElementById('btn-proceed-checkout-auth');
                                                if (checkoutBtn) {
                                                    checkoutBtn.addEventListener('click', function(e) {
                                                        e.preventDefault();
                                                        sessionStorage.setItem('speckart_pending_checkout', '1');
                                                        var modalEl = document.getElementById('speckartLoginModal');
                                                        if (modalEl) {
                                                            var modal = new bootstrap.Modal(modalEl);
                                                            modal.show();
                                                        } else {
                                                            window.location.href = '{{ route("shipping-details") }}';
                                                        }
                                                    });
                                                }
                                            });
                                        </script>
                                    @endif
                                </div>

                                {{-- Cashback Teaser --}}
                                @if(isset($cartData['pending_cashback']) && $cartData['pending_cashback'] > 0)
                                    <div class="sc-cashback">
                                        <i class="bi bi-gift-fill"></i>
                                        🎉 Earn {{ number_format($cartData['pending_cashback'], 0) }} pts · {{ (int)$cartData['cashback_percent'] }}% cashback
                                    </div>
                                @endif

                                {{-- Trust Badges --}}
                                <div class="sc-trust">
                                    <div class="sc-trust-item"><i class="bi bi-shield-check"></i><span>Secure</span></div>
                                    <div class="sc-trust-item"><i class="bi bi-truck"></i><span>Free Shipping</span></div>
                                    <div class="sc-trust-item"><i class="bi bi-arrow-counterclockwise"></i><span>Easy Returns</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- Gold Banner --}}
                        @php $bs = $cartData['banner_state'] ?? null; @endphp
                        @if($bs)
                            @if($bs['state'] == 1)
                                <div class="sc-gold-banner state1">
                                    <div class="sc-gold-tag"><i class="bi bi-award-fill"></i> GOLD MEMBERSHIP</div>
                                    <div class="sc-gold-title">{{ Str::replace(['👑', '★', '⚠️'], '', $bs['title']) }}</div>
                                    <div class="sc-gold-subtitle">{{ Str::replace(['👑', '★', '⚠️'], '', $bs['subtitle']) }}</div>
                                    <div class="sc-gold-cta">
                                        <button type="button" id="btn-add-membership" data-card-id="{{ $bs['card_id'] ?? 1 }}" class="sc-gold-btn">
                                            <span id="btn-add-membership-text">{{ Str::replace(['👑', '★', '⚠️'], '', $bs['btn_text']) }}</span>
                                            <span id="btn-add-membership-spinner" class="spinner-border spinner-border-sm ms-1 d-none" role="status"></span>
                                        </button>
                                        <button type="button" id="btn-add-membership-arrow" data-card-id="{{ $bs['card_id'] ?? 1 }}" class="sc-gold-arrow">
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            @elseif($bs['state'] == 2)
                                <div class="sc-gold-banner state2">
                                    <div class="sc-gold-title" style="display:flex;align-items:center;gap:6px;">
                                        <i class="bi bi-award-fill text-warning"></i>
                                        {{ Str::replace(['👑', '★', '⚠️'], '', $bs['title']) }}
                                    </div>
                                    <div class="sc-gold-subtitle">{{ Str::replace(['👑', '★', '⚠️'], '', $bs['subtitle']) }}</div>
                                    <a href="{{ $bs['cta_url'] }}" class="sc-gold-btn" style="text-decoration:none; color:#e8590c;">
                                        {{ Str::replace(['👑', '★', '⚠️'], '', $bs['btn_text']) }} <i class="bi bi-chevron-right"></i>
                                    </a>
                                </div>
                            @elseif($bs['state'] == 3)
                                <div class="sc-gold-banner state3">
                                    <div class="sc-gold-title">{{ Str::replace(['👑', '★', '⚠️'], '', $bs['title']) }}</div>
                                    <div class="sc-gold-subtitle" style="margin-bottom:0;">{{ Str::replace(['👑', '★', '⚠️'], '', $bs['subtitle']) }}</div>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>

            </div>

            {{-- ═══ MOBILE BOTTOM BAR ═══ --}}
            <div class="sc-mobile-bar">
                <div class="sc-mobile-bar-price">
                    <div class="sc-total-label">Total</div>
                    <div class="sc-total-value">₹{{ number_format($cartData['grand_total'], 2) }}</div>
                </div>
                @if(auth()->check())
                    <a href="{{ route('shipping-details') }}" class="sc-btn-checkout">
                        Checkout <i class="bi bi-arrow-right"></i>
                    </a>
                @else
                    <button type="button" class="sc-btn-checkout" onclick="
                        sessionStorage.setItem('speckart_pending_checkout', '1');
                        var m = document.getElementById('speckartLoginModal');
                        if(m) { new bootstrap.Modal(m).show(); } else { window.location.href='{{ route('shipping-details') }}'; }
                    ">
                        Checkout <i class="bi bi-arrow-right"></i>
                    </button>
                @endif
            </div>

        @endif
    </div>
</section>


{{-- ═══ CART AJAX SCRIPTS ═══ --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const csrfToken = "{{ csrf_token() }}";

        // Update Quantity Plus
        $(document).on('click', '.qty-plus', function() {
            const key = $(this).data('key');
            const qtySpan = $(this).siblings('.item-qty');
            const newQty = parseInt(qtySpan.text()) + 1;
            updateCartQty(key, newQty);
        });

        // Update Quantity Minus
        $(document).on('click', '.qty-minus', function() {
            const key = $(this).data('key');
            const qtySpan = $(this).siblings('.item-qty');
            const currentQty = parseInt(qtySpan.text());
            if (currentQty > 1) {
                updateCartQty(key, currentQty - 1);
            }
        });

        // Remove Item
        // Remove Cart Item
        $(document).on('click', '.remove-cart-item', function() {
            const key = $(this).data('key');
            Swal.fire({
                title: 'Remove item?',
                text: 'Are you sure you want to remove this item from your cart?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cart.remove') }}",
                        type: "POST",
                        data: { _token: csrfToken, cart_key: key },
                        success: function(res) {
                            window.location.reload();
                        }
                    });
                }
            });
        });

        // Quick Apply Available Coupon Chip Click
        $(document).on('click', '.apply-quick-coupon', function() {
            const code = $(this).data('code');
            $('#coupon-code-input').val(code);
            $('#apply-coupon-btn').click();
        });

        // Apply Coupon
        $('#apply-coupon-btn').click(function() {
            const code = $('#coupon-code-input').val().trim();
            if (!code) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Coupon Required',
                    text: 'Please enter a coupon code.',
                    confirmButtonColor: '#00a297'
                });
                return;
            }

            $.ajax({
                url: "{{ route('cart.coupon') }}",
                type: "POST",
                data: { _token: csrfToken, coupon_code: code },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message || 'Coupon applied successfully.',
                        confirmButtonColor: '#00a297'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Coupon',
                        text: xhr.responseJSON ? xhr.responseJSON.message : 'Invalid coupon code.',
                        confirmButtonColor: '#00a297'
                    });
                }
            });
        });

        // Remove Coupon
        $(document).on('click', '#remove-coupon-btn', function() {
            $.ajax({
                url: "{{ route('cart.remove_coupon') }}",
                type: "POST",
                data: { _token: csrfToken },
                success: function(res) {
                    window.location.reload();
                }
            });
        });

        // Quick Apply Saved Voucher Chip Click
        $(document).on('click', '.apply-quick-voucher', function() {
            const code = $(this).data('code');
            $('#voucher-code-input').val(code);
            $('#apply-voucher-btn').click();
        });

        // Apply Gift Voucher
        $('#apply-voucher-btn').click(function() {
            const code = $('#voucher-code-input').val().trim();
            if (!code) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Voucher Required',
                    text: 'Please enter a voucher code.',
                    confirmButtonColor: '#00a297'
                });
                return;
            }
            const $btn = $(this);
            $btn.text('Applying...').prop('disabled', true);

            $.ajax({
                url: "{{ route('cart.apply_voucher') }}",
                type: "POST",
                data: { _token: csrfToken, voucher_code: code },
                success: function(res) {
                    window.location.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Voucher',
                        text: xhr.responseJSON ? xhr.responseJSON.message : 'Invalid or expired voucher code.',
                        confirmButtonColor: '#00a297'
                    });
                    $btn.text('APPLY').prop('disabled', false);
                }
            });
        });

        // Remove Gift Voucher
        $(document).on('click', '#remove-voucher-btn', function() {
            $.ajax({
                url: "{{ route('cart.remove_voucher') }}",
                type: "POST",
                data: { _token: csrfToken },
                success: function(res) {
                    window.location.reload();
                }
            });
        });

        function updateCartQty(key, qty) {
            $.ajax({
                url: "{{ route('cart.update') }}",
                type: "POST",
                data: { _token: csrfToken, cart_key: key, quantity: qty },
                success: function(res) {
                    window.location.reload();
                }
            });
        }

        // Add Membership to Cart (State 1 Banner Button)
        $(document).on('click', '#btn-add-membership, #btn-add-membership-arrow', function() {
            const cardId = $(this).data('card-id');
            $('#btn-add-membership-text').text('Adding...');
            $('#btn-add-membership-spinner').removeClass('d-none');
            $('#btn-add-membership, #btn-add-membership-arrow').prop('disabled', true);

            $.ajax({
                url: "{{ route('cart.add_membership') }}",
                type: "POST",
                data: { _token: csrfToken, card_id: cardId },
                headers: { 'Accept': 'application/json' },
                success: function(res) {
                    window.location.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Membership Error',
                        text: xhr.responseJSON ? xhr.responseJSON.message : 'Could not add membership. Please try again.',
                        confirmButtonColor: '#00a297'
                    });
                    $('#btn-add-membership-text').text('Add Gold');
                    $('#btn-add-membership-spinner').addClass('d-none');
                    $('#btn-add-membership, #btn-add-membership-arrow').prop('disabled', false);
                }
            });
        });

        // Toggle Loyalty Points Checkbox Change Handler
        $(document).on('change', '#toggle-loyalty-checkbox', function() {
            const isChecked = $(this).is(':checked');

            $.ajax({
                url: "{{ route('cart.toggle_loyalty') }}",
                type: "POST",
                data: { _token: csrfToken, use_loyalty: isChecked ? 1 : 0 },
                success: function(res) {
                    window.location.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Loyalty Points Error',
                        text: 'Failed to update loyalty points. Please try again.',
                        confirmButtonColor: '#00a297'
                    });
                }
            });
        });
    });
</script>

@endsection