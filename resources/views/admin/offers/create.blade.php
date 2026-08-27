@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
/* ═══════════════════════════════════════════════════════
   CREATE OFFER — MULTI-STEP WIZARD
   Premium SaaS admin panel design
   ═══════════════════════════════════════════════════════ */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.offer-wizard-wrap {
    font-family: 'Inter', sans-serif;
    padding: 0 8px;
}

/* ── Page Header ── */
.offer-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.offer-page-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: #1a1d29;
    margin: 0;
}
.offer-page-header h2 i {
    color: #07484A;
    margin-right: 8px;
}

/* ── Stepper Bar ── */
.stepper-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 32px;
    background: #fff;
    border-radius: 16px;
    padding: 20px 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
    overflow-x: auto;
}
.stepper-step {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 8px 14px;
    border-radius: 10px;
    white-space: nowrap;
}
.stepper-step:hover {
    background: #f0f7f7;
}
.stepper-step.active {
    background: #07484A;
}
.stepper-step.active .step-circle {
    background: #fff;
    color: #07484A;
    box-shadow: 0 2px 8px rgba(80, 137, 139, 0.3);
}
.stepper-step.active .step-label {
    color: #fff;
    font-weight: 600;
}
.stepper-step.completed .step-circle {
    background: #10b981;
    color: #fff;
    border-color: #10b981;
}
.stepper-step.completed .step-label {
    color: #10b981;
}
.step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    background: #e8ecf1;
    color: #6b7280;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    flex-shrink: 0;
}
.step-label {
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    transition: all 0.3s ease;
}
.step-label small {
    display: block;
    font-size: 11px;
    font-weight: 400;
    opacity: 0.7;
    margin-top: 1px;
}
.stepper-arrow {
    font-size: 16px;
    color: #d1d5db;
    margin: 0 6px;
    flex-shrink: 0;
}

/* ── Form Cards ── */
.wizard-step-panel {
    display: none;
    animation: fadeInUp 0.4s ease;
}
.wizard-step-panel.active {
    display: block;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}

.offer-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
    padding: 28px;
    margin-bottom: 20px;
    transition: box-shadow 0.3s ease;
}
.offer-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.offer-card-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a1d29;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 8px;
}
.offer-card-title i {
    color: #07484A;
    font-size: 18px;
}

/* ── Form Controls ── */
.offer-form-group {
    margin-bottom: 18px;
}
.offer-form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}
.offer-form-group label .required {
    color: #ef4444;
    margin-left: 2px;
}
.offer-form-group .form-control,
.offer-form-group .form-select {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    color: #1a1d29;
    background: #fafbfc;
    transition: all 0.25s ease;
    height: auto;
}
.offer-form-group .form-control:focus,
.offer-form-group .form-select:focus {
    border-color: #07484A;
    box-shadow: 0 0 0 3px rgba(7,72,74,0.1);
    background: #fff;
    outline: none;
}
.offer-form-group .form-control::placeholder {
    color: #9ca3af;
}
.offer-form-group textarea.form-control {
    min-height: 80px;
    resize: vertical;
}
.offer-form-group .input-hint {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
}

/* ── Radio Cards ── */
.radio-card-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.radio-card {
    flex: 1;
    min-width: 140px;
    position: relative;
}
.radio-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}
.radio-card-label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.25s ease;
    background: #fafbfc;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
}
.radio-card-label:hover {
    border-color: #07484A;
    background: #f0f7f7;
}
.radio-card input:checked + .radio-card-label,
.radio-card-label.active {
    border-color: #07484A;
    background: linear-gradient(135deg, #f0f9f9, #e6f3f3);
    color: #07484A;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(7,72,74,0.12);
}
.radio-card input:checked + .radio-card-label .radio-dot,
.radio-card-label.active .radio-dot {
    border-color: #07484A;
    background: #07484A;
    box-shadow: inset 0 0 0 3px #fff;
}
.radio-dot {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    background: #fff;
    flex-shrink: 0;
    transition: all 0.25s ease;
}

/* ── Status Toggle ── */
.status-toggle-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}
.status-toggle {
    position: relative;
    width: 50px;
    height: 26px;
}
.status-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}
.status-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #d1d5db;
    border-radius: 26px;
    transition: 0.3s;
}
.status-slider::before {
    content: '';
    position: absolute;
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: 0.3s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.status-toggle input:checked + .status-slider {
    background: #10b981;
}
.status-toggle input:checked + .status-slider::before {
    transform: translateX(24px);
}
.status-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
}
.status-label.active-label { color: #10b981; }

/* ── Apply On Radio Grid ── */
.apply-radio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}
.apply-radio-card {
    position: relative;
}
.apply-radio-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}
.apply-radio-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 18px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.25s ease;
    background: #fafbfc;
    text-align: center;
}
.apply-radio-label i {
    font-size: 22px;
    color: #9ca3af;
    transition: color 0.25s ease;
}
.apply-radio-label span {
    font-size: 13px;
    font-weight: 500;
    color: #374151;
}
.apply-radio-label:hover {
    border-color: #07484A;
    background: #f0f7f7;
}
.apply-radio-card input:checked + .apply-radio-label {
    border-color: #07484A;
    background: linear-gradient(135deg, #07484A, #0a5e60);
    box-shadow: 0 4px 14px rgba(7,72,74,0.25);
}
.apply-radio-card input:checked + .apply-radio-label i,
.apply-radio-card input:checked + .apply-radio-label span {
    color: #fff;
}

/* ── Conditional Panels ── */
.conditional-panel {
    display: none;
    animation: fadeInUp 0.3s ease;
    margin-top: 16px;
    padding: 16px;
    background: #f8fafb;
    border-radius: 10px;
    border: 1px dashed #c8d6e5;
}
.conditional-panel.show {
    display: block;
}

/* ── Product Search Table ── */
.product-search-box {
    position: relative;
    margin-bottom: 14px;
}
.product-search-box input {
    padding-left: 38px !important;
}
.product-search-box .search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 14px;
}
.product-table-wrap {
    max-height: 320px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
}
.product-table-wrap table {
    margin-bottom: 0;
    font-size: 12px;
}
.product-table-wrap thead th {
    position: sticky;
    top: 0;
    background: #f1f5f9;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    color: #64748b;
    padding: 10px 12px;
    border-bottom: 2px solid #e2e8f0;
    z-index: 2;
}
.product-table-wrap tbody td {
    padding: 8px 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.product-table-wrap tbody tr:hover {
    background: #f8fafb;
}
.product-table-wrap .product-thumb {
    width: 36px;
    height: 36px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #f8f8f8;
}
.product-table-wrap .form-check-input {
    width: 16px;
    height: 16px;
    cursor: pointer;
}
.selected-count-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: linear-gradient(135deg, #f0f9f9, #e6f3f3);
    border: 1px solid #07484A;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #07484A;
    margin-top: 10px;
}

/* ── Preview Panel ── */
.preview-panel {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
    padding: 24px;
    position: sticky;
    top: 20px;
}
.preview-panel-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a1d29;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.preview-panel-title i {
    color: #07484A;
}
.preview-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
}
.preview-item:last-child {
    border-bottom: none;
}
.preview-item-label {
    color: #6b7280;
    font-weight: 500;
    flex-shrink: 0;
    margin-right: 8px;
}
.preview-item-value {
    color: #1a1d29;
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}
.preview-divider {
    border: none;
    border-top: 2px dashed #e2e8f0;
    margin: 12px 0;
}
.preview-highlight {
    background: linear-gradient(135deg, #f0f9f9, #e6f3f3);
    border-radius: 10px;
    padding: 14px;
    margin-top: 12px;
    text-align: center;
}
.preview-highlight .count {
    font-size: 28px;
    font-weight: 700;
    color: #07484A;
    line-height: 1;
}
.preview-highlight .label {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}

/* ── Info Alert ── */
.offer-info-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    border-radius: 0 8px 8px 0;
    margin-top: 12px;
    font-size: 12px;
    color: #1e40af;
}
.offer-info-alert i {
    font-size: 16px;
    margin-top: 1px;
    flex-shrink: 0;
}

/* ── Bottom Actions ── */
.wizard-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 24px;
    padding: 20px 24px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
}
.wizard-actions .btn {
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 22px;
    transition: all 0.25s ease;
    border: none;
}
.btn-wizard-cancel {
    background: #f1f5f9;
    color: #64748b;
}
.btn-wizard-cancel:hover {
    background: #e2e8f0;
    color: #475569;
}
.btn-wizard-draft {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d !important;
}
.btn-wizard-draft:hover {
    background: #fde68a;
}
.btn-wizard-back {
    background: #e2e8f0;
    color: #475569;
}
.btn-wizard-back:hover {
    background: #cbd5e1;
}
.btn-wizard-next {
    background: linear-gradient(135deg, #07484A, #0a5e60);
    color: #fff;
    box-shadow: 0 4px 12px rgba(7,72,74,0.25);
}
.btn-wizard-next:hover {
    background: linear-gradient(135deg, #0a5e60, #07484A);
    box-shadow: 0 6px 18px rgba(7,72,74,0.3);
    transform: translateY(-1px);
    color: #fff;
}
.btn-wizard-activate {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    box-shadow: 0 4px 12px rgba(16,185,129,0.25);
}
.btn-wizard-activate:hover {
    box-shadow: 0 6px 18px rgba(16,185,129,0.3);
    transform: translateY(-1px);
    color: #fff;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .stepper-bar { flex-wrap: wrap; gap: 6px; padding: 14px; }
    .stepper-arrow { display: none; }
    .radio-card-group { flex-direction: column; }
    .apply-radio-grid { grid-template-columns: 1fr 1fr; }
    .wizard-actions { flex-direction: column; }
    .wizard-actions .btn { width: 100%; }
}

/* ── Loading Spinner for product search ── */
.search-spinner {
    display: none;
    text-align: center;
    padding: 24px;
    color: #9ca3af;
}
.search-spinner.show {
    display: block;
}

/* ── Input Group Enhancement ── */
.input-with-unit {
    position: relative;
}
.input-with-unit .unit-label {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    font-weight: 600;
    color: #9ca3af;
    pointer-events: none;
}

/* ── Select2 Custom Premium Styling ── */
.select2-container--default .select2-selection--multiple {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 6px 10px !important;
    min-height: 44px !important;
    background: #fafbfc !important;
    transition: all 0.25s ease !important;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #07484A !important;
    box-shadow: 0 0 0 3px rgba(7,72,74,0.1) !important;
    background: #fff !important;
    outline: none !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #07484A !important;
    border: none !important;
    color: #fff !important;
    border-radius: 6px !important;
    padding: 4px 10px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    margin-top: 4px !important;
    margin-right: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255, 255, 255, 0.7) !important;
    border: none !important;
    background: none !important;
    font-size: 14px !important;
    margin-right: 0 !important;
    padding: 0 !important;
    order: 2 !important;
    transition: color 0.2s !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #fff !important;
    background: none !important;
}
.select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 6px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
}
.select2-dropdown {
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
    overflow: hidden !important;
    z-index: 9999 !important;
}
.select2-results__option {
    padding: 8px 14px !important;
    font-size: 13px !important;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #07484A !important;
    color: #fff !important;
}
.select2-container--default .select2-results__option[aria-selected="true"] {
    background-color: #f1f5f9 !important;
    color: #07484A !important;
    font-weight: 600 !important;
}

/* ══════════════════════════════════════════════
   OFFER TYPE SELECTOR CARDS  (V2 addition)
   ══════════════════════════════════════════════ */
.offer-type-selector-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}
@media (max-width: 768px) {
    .offer-type-selector-grid { grid-template-columns: repeat(2, 1fr); }
}
.offer-type-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 12px;
    cursor: pointer;
    background: #fafbfc;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    text-align: center;
    transition: all 0.2s ease;
    user-select: none;
}
.offer-type-card:hover {
    border-color: #07484A;
    background: #f0f7f7;
}
.offer-type-card .ot-icon {
    font-size: 20px;
    font-weight: 700;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    background: #f1f5f9;
    border-radius: 10px;
    transition: all 0.2s ease;
    color: #6b7280;
}
.offer-type-card .ot-label {
    font-size: 12.5px;
    font-weight: 600;
    color: #374151;
    transition: color 0.2s;
    line-height: 1.3;
}
/* Active — coupon (teal) */
.offer-type-card[data-group="coupon"].active {
    border-color: #07484A;
    background: linear-gradient(135deg, #f0f9f9, #e6f3f3);
    box-shadow: 0 2px 10px rgba(7,72,74,0.12);
}
.offer-type-card[data-group="coupon"].active .ot-icon {
    background: #07484A;
    color: #fff;
}
.offer-type-card[data-group="coupon"].active .ot-label { color: #07484A; }
/* Active — bogo (amber) */
.offer-type-card[data-group="bogo"].active {
    border-color: #d68f1f;
    background: #fdf9f0;
    box-shadow: 0 2px 10px rgba(214,143,31,0.12);
}
.offer-type-card[data-group="bogo"].active .ot-icon {
    background: #d68f1f;
    color: #fff;
}
.offer-type-card[data-group="bogo"].active .ot-label { color: #d68f1f; }
/* Active — voucher (purple) */
.offer-type-card[data-group="voucher"].active {
    border-color: #6b4bcf;
    background: #f3f0fd;
    box-shadow: 0 2px 10px rgba(107,75,207,0.12);
}
.offer-type-card[data-group="voucher"].active .ot-icon {
    background: #6b4bcf;
    color: #fff;
}
.offer-type-card[data-group="voucher"].active .ot-label { color: #6b4bcf; }

/* ── Inline type-field panels ── */
.offer-type-fields {
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 16px;
    animation: fadeInUp 0.3s ease;
}
.offer-type-fields.fields-coupon {
    border-left: 3px solid #07484A;
    background: #f7fcf9;
}
.offer-type-fields.fields-bogo {
    border-left: 3px solid #d68f1f;
    background: #fdf9f0;
}
.offer-type-fields.fields-voucher {
    border-left: 3px solid #6b4bcf;
    background: #f8f6fd;
}
/* ── Coupon limits hint bar ── */
.coupon-limits-note {
    font-size: 11.5px;
    color: #9ca3af;
    margin-top: 6px;
    font-style: italic;
}
</style>

<section class="domestic-orders mt-0">
<div class="container-fluid">
<div class="offer-wizard-wrap">

    <!-- ── Page Header ── -->
    <div class="offer-page-header">
        <h2><i class="fa fa-gift"></i> {{ isset($offer) ? 'Edit Offer' : 'Create New Offer' }}</h2>
    </div>

    <form id="offerForm" novalidate enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- ══════════ LEFT COLUMN — FORM ══════════ -->
            <div class="col-lg-8">

                <!-- ═══ Card 0: Offer Type Selector ═══ -->
                <div class="offer-card">
                    <div class="offer-card-title"><i class="fa fa-question-circle"></i> What kind of offer is this?</div>
                    <input type="hidden" id="offer_type_hidden" name="offer_type"
                           value="{{ old('offer_type', isset($offer) ? $offer->offer_type : 'buy1get1') }}">
                    <div class="offer-type-selector-grid" id="offerTypeRow">
                        <div class="offer-type-card d-none"
                             data-type="percentage_discount" data-group="coupon">
                            <span class="ot-icon">%</span>
                            <span class="ot-label">Percentage Discount</span>
                        </div>
                        <div class="offer-type-card d-none"
                             data-type="flat_discount" data-group="coupon">
                            <span class="ot-icon">₹</span>
                            <span class="ot-label">Flat Discount</span>
                        </div>
                        <div class="offer-type-card {{ (!isset($offer) || $offer->offer_type === 'buy1get1') ? 'active' : '' }}"
                             data-type="buy1get1" data-group="bogo">
                            <span class="ot-icon"><i class="fa fa-plus"></i></span>
                            <span class="ot-label">Buy 1 Get 1 Free</span>
                        </div>
                        <div class="offer-type-card d-none {{ (isset($offer) && in_array($offer->offer_type, ['cashback','gift_voucher'])) ? 'active' : '' }}"
                             data-type="gift_voucher" data-group="voucher">
                            <span class="ot-icon"><i class="fa fa-ticket"></i></span>
                            <span class="ot-label">Gift Voucher</span>
                        </div>
                    </div>
                </div>

                <!-- ═══ Card 1: Offer Details ═══ -->
                <div class="offer-card">
                    <div class="offer-card-title">
                        <i class="fa fa-tags" id="offerDetailIcon"></i>
                        <span id="offerDetailTitleText">Discount Details</span>
                    </div>

                    <!-- Offer Name (always shown) -->
                    <div class="offer-form-group">
                        <label>Offer Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="offer_name" name="name"
                               placeholder="e.g. Diwali Sale 30% OFF"
                               value="{{ $offer->name ?? old('name') }}" required>
                    </div>

                    <!-- ──────── COUPON / DISCOUNT FIELDS (Percentage & Flat) ──────── -->
                    <div id="fieldsCoupon" class="offer-type-fields fields-coupon">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="offer-form-group">
                                    <label>Discount Type <span class="required">*</span></label>
                                    <div class="radio-card-group">
                                        <div class="radio-card">
                                            <input type="radio" name="discount_type" id="dt_percentage" value="percentage"
                                                   {{ (!isset($offer) || $offer->discount_type === 'percentage') ? 'checked' : '' }}>
                                            <label class="radio-card-label {{ (!isset($offer) || $offer->discount_type === 'percentage') ? 'active' : '' }}" for="dt_percentage">
                                                <span class="radio-dot"></span>
                                                <i class="fa fa-percent" style="font-size:14px"></i> Percentage
                                            </label>
                                        </div>
                                        <div class="radio-card">
                                            <input type="radio" name="discount_type" id="dt_fixed" value="fixed"
                                                   {{ (isset($offer) && $offer->discount_type === 'fixed') ? 'checked' : '' }}>
                                            <label class="radio-card-label {{ (isset($offer) && $offer->discount_type === 'fixed') ? 'active' : '' }}" for="dt_fixed">
                                                <span class="radio-dot"></span>
                                                <i class="fa fa-inr" style="font-size:14px"></i> Fixed Amount
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="offer-form-group">
                                    <label>Discount Value <span class="required">*</span></label>
                                    <div class="input-with-unit">
                                        <input type="number" class="form-control" id="discount_value"
                                               name="discount_value" placeholder="30" min="0" step="0.01"
                                               value="{{ isset($offer) ? rtrim(rtrim($offer->discount_value, '0'), '.') : old('discount_value') }}">
                                        <span class="unit-label" id="discountUnit">{{ (isset($offer) && $offer->discount_type === 'fixed') ? '₹' : '%' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 d-none">
                                <div class="offer-form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="mb-0">Coupon Code <small class="text-muted" style="font-weight:400;">(optional)</small></label>
                                        <button type="button" class="btn btn-sm text-primary p-0 border-0 bg-transparent fw-bold btn-generate-speckart-code" style="font-size: 11px; cursor: pointer;">
                                            <i class="fa fa-magic"></i> Generate Code
                                        </button>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="coupon_code" name="coupon_code"
                                               placeholder="e.g. SPECKART30"
                                               value="{{ $offer->coupon_code ?? old('coupon_code') }}"
                                               style="text-transform: uppercase;">
                                        <button type="button" class="btn btn-outline-secondary btn-generate-speckart-code" title="Generate Code with SPECKART prefix">
                                            <i class="fa fa-random"></i>
                                        </button>
                                    </div>
                                    <div class="input-hint">Leave blank for auto discount or click Generate for SPECKART prefix</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="offer-form-group">
                                    <label>Minimum Cart Amount</label>
                                    <div class="input-with-unit">
                                        <input type="number" class="form-control" id="min_cart_amount"
                                               name="min_cart_amount" placeholder="1000" min="0" step="0.01"
                                               value="{{ isset($offer) && $offer->min_cart_amount ? rtrim(rtrim($offer->min_cart_amount, '0'), '.') : old('min_cart_amount') }}">
                                        <span class="unit-label">₹</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="offer-form-group">
                                    <label>Maximum Discount Cap</label>
                                    <div class="input-with-unit">
                                        <input type="number" class="form-control" id="max_discount"
                                               name="max_discount" placeholder="2000" min="0" step="0.01"
                                               value="{{ isset($offer) && $offer->max_discount ? rtrim(rtrim($offer->max_discount, '0'), '.') : old('max_discount') }}">
                                        <span class="unit-label">₹</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ──────── BUY 1 GET 1 FREE + BONUS TIER PACKAGE ──────── -->
                    <div id="fieldsBogo" class="offer-type-fields fields-bogo" style="display:none;">

                        <!-- Base Rule: always Buy 1 Get 1 Free -->
                        <div class="offer-info-alert mb-3" style="background:#fffbeb;border-left-color:#d68f1f;color:#92400e;">
                            <i class="fa fa-check-circle fa-lg" style="color:#d68f1f;margin-top:2px;"></i>
                            <div>
                                <strong>Buy 1 Get 1 Free</strong> is the base rule of this offer.<br>
                                <span style="font-size:11px;">The 2nd item in the customer's cart is automatically 100% FREE.</span>
                            </div>
                        </div>

                        <!-- Hidden fields: always fixed for standard BOGO -->
                        <input type="hidden" name="bogo_buy_qty"      id="bogo_buy_qty"      value="1">
                        <input type="hidden" name="bogo_get_qty"      id="bogo_get_qty"      value="1">
                        <input type="hidden" name="bogo_free_discount" id="bogo_free_discount" value="100">

                        <!-- ── Bonus Tier Toggle ── -->
                        <div class="card mt-2" style="border-radius:10px;border:1px dashed #d68f1f;background:#fffdf5;">
                            <div class="card-body" style="padding:16px 20px;">

                                <div class="d-flex align-items-center justify-content-between mb-0">
                                    <div>
                                        <div style="font-size:13.5px;font-weight:700;color:#92400e;">
                                            <i class="fa fa-plus-circle" style="margin-right:6px;"></i>Add Bonus Tier
                                        </div>
                                        <div style="font-size:11.5px;color:#a16207;margin-top:2px;">
                                            Give an extra % discount when the customer adds a 3rd product
                                        </div>
                                    </div>
                                    <label class="status-toggle" style="margin-left:16px;flex-shrink:0;">
                                        <input type="checkbox" id="bogo_bonus_enabled" name="bogo_extra_enabled" value="1"
                                               {{ (isset($offer) && $offer->bogo_extra_enabled) ? 'checked' : '' }}>
                                        <span class="status-slider"></span>
                                    </label>
                                </div>

                                <!-- Bonus Tier Fields (revealed when toggle ON) -->
                                <div id="bogoTierFields" style="display:{{ (isset($offer) && $offer->bogo_extra_enabled) ? 'block' : 'none' }};margin-top:16px;padding-top:14px;border-top:1px solid #fde68a;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="offer-form-group mb-0">
                                                <label>Discount on 3rd Product <span class="required">*</span></label>
                                                <div class="input-with-unit">
                                                    <input type="number" class="form-control" id="bogo_extra_discount"
                                                           name="bogo_extra_discount" placeholder="60"
                                                           min="1" max="99"
                                                           value="{{ $offer->bogo_extra_discount ?? old('bogo_extra_discount', 60) }}">
                                                    <span class="unit-label">%</span>
                                                </div>
                                                <div class="input-hint">e.g. 60 = customer gets 60% OFF on their 3rd item</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="offer-info-alert mb-0" style="height:100%;align-items:center;font-size:12px;">
                                                <i class="fa fa-lightbulb-o"></i>
                                                <div>
                                                    <strong>How it works:</strong><br>
                                                    Item 1 → Full price<br>
                                                    Item 2 → <strong>FREE</strong> (BOGO)<br>
                                                    Item 3 → <strong><span id="preview_bonus_pct">{{ $offer->bogo_extra_discount ?? 60 }}</span>% OFF</strong> (Bonus)
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ── 3rd Product Scope Targeting ── -->
                                    <div class="mt-3 pt-3" style="border-top: 1px dashed #fde68a;">
                                        <label class="fw-semibold mb-2" style="font-size:12.5px;color:#92400e;">
                                            <i class="fa fa-bullseye me-1"></i> Which products are eligible for this 3rd Item Discount?
                                        </label>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6 col-md-3">
                                                <label class="radio-card-label {{ (!isset($offer) || empty($offer->bogo_third_apply_on) || $offer->bogo_third_apply_on === 'same_as_bogo') ? 'active' : '' }}" for="bogo_third_same" style="font-size:12px;padding:8px 10px;cursor:pointer;">
                                                    <input type="radio" name="bogo_third_apply_on" id="bogo_third_same" value="same_as_bogo"
                                                           {{ (!isset($offer) || empty($offer->bogo_third_apply_on) || $offer->bogo_third_apply_on === 'same_as_bogo') ? 'checked' : '' }} style="margin-right:6px;">
                                                    <span>Same as BOGO</span>
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="radio-card-label {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_brand') ? 'active' : '' }}" for="bogo_third_brand" style="font-size:12px;padding:8px 10px;cursor:pointer;">
                                                    <input type="radio" name="bogo_third_apply_on" id="bogo_third_brand" value="specific_brand"
                                                           {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_brand') ? 'checked' : '' }} style="margin-right:6px;">
                                                    <span>Specific Brand</span>
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="radio-card-label {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_category') ? 'active' : '' }}" for="bogo_third_category" style="font-size:12px;padding:8px 10px;cursor:pointer;">
                                                    <input type="radio" name="bogo_third_apply_on" id="bogo_third_category" value="specific_category"
                                                           {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_category') ? 'checked' : '' }} style="margin-right:6px;">
                                                    <span>Specific Category</span>
                                                </label>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="radio-card-label {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_products') ? 'active' : '' }}" for="bogo_third_products" style="font-size:12px;padding:8px 10px;cursor:pointer;">
                                                    <input type="radio" name="bogo_third_apply_on" id="bogo_third_products" value="specific_products"
                                                           {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_products') ? 'checked' : '' }} style="margin-right:6px;">
                                                    <span>Specific Products</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- 3rd Item Brand Selector -->
                                        <div id="panel-bogo-third-brand" class="mt-2" style="display: {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_brand') ? 'block' : 'none' }};">
                                            <label style="font-size:12px;font-weight:600;color:#374151;">Select Brands for 3rd Item <span class="required">*</span></label>
                                            <select class="form-control" id="select_bogo_third_brand" name="bogo_third_brands[]" multiple>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->brand_id }}" {{ (isset($offer) && is_array($offer->bogo_third_brand_ids) && in_array($brand->brand_id, $offer->bogo_third_brand_ids)) ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- 3rd Item Category Selector -->
                                        <div id="panel-bogo-third-category" class="mt-2" style="display: {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_category') ? 'block' : 'none' }};">
                                            <label style="font-size:12px;font-weight:600;color:#374151;">Select Categories for 3rd Item <span class="required">*</span></label>
                                            <select class="form-control" id="select_bogo_third_category" name="bogo_third_categories[]" multiple>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ (isset($offer) && is_array($offer->bogo_third_category_ids) && in_array($cat->id, $offer->bogo_third_category_ids)) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- 3rd Item Product Search & Table -->
                                        <div id="panel-bogo-third-products" class="mt-2" style="display: {{ (isset($offer) && $offer->bogo_third_apply_on === 'specific_products') ? 'block' : 'none' }};">
                                            <div class="product-search-box mb-2">
                                                <span class="search-icon"><i class="fa fa-search"></i></span>
                                                <input type="text" class="form-control" id="bogoThirdProductSearchInput"
                                                       placeholder="Search products for 3rd item discount...">
                                            </div>
                                            <div class="product-table-wrap" style="max-height:220px;overflow-y:auto;">
                                                <table class="table table-sm" id="bogoThirdProductTable">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:36px"><input type="checkbox" class="form-check-input" id="selectAllBogoThirdProducts"></th>
                                                            <th style="width:40px">Image</th>
                                                            <th>Product Name</th>
                                                            <th>SKU</th>
                                                            <th>Brand</th>
                                                            <th>Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bogoThirdProductTableBody">
                                                        <tr><td colspan="6" class="text-center text-muted py-3">Type above to search products for 3rd item...</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="selected-count-badge" id="bogoThirdSelectedBadge" style="display:none;margin-top:6px;">
                                                <i class="fa fa-check-circle"></i> <span id="bogoThirdSelectedCount">0</span> products selected for 3rd item
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- ──────── GIFT VOUCHER FIELDS ──────── -->
                    <div id="fieldsVoucher" class="offer-type-fields fields-voucher" style="display:none;">

                        <!-- HOW IS THIS VOUCHER GIVEN? -->
                        <div class="offer-form-group mb-3">
                            <label class="fw-semibold" style="font-size:13px;">How is this voucher given? <span class="required">*</span></label>
                            <div class="row g-2 mt-1">
                                <!-- Auto-linked (hidden for now) -->
                                <div class="col-md-6" style="display:none;">
                                    <label class="voucher-delivery-card" id="card-auto" for="delivery_auto"
                                        style="display:flex;align-items:flex-start;gap:10px;padding:14px 16px;border:2px solid #dee2e6;border-radius:10px;cursor:pointer;background:#fff;transition:all .2s;">
                                        <input type="radio" name="voucher_delivery_type" id="delivery_auto" value="auto"
                                            style="margin-top:3px;accent-color:#4e54c8;">
                                        <div>
                                            <div style="font-weight:600;font-size:13px;color:#343a40;">Auto-linked to account</div>
                                            <div style="font-size:11.5px;color:#6c757d;margin-top:2px;">No code required — issued automatically on trigger event</div>
                                        </div>
                                    </label>
                                </div>
                                <!-- Manual code -->
                                <div class="col-md-12">
                                    <label class="voucher-delivery-card active" id="card-manual" for="delivery_manual"
                                        style="display:flex;align-items:flex-start;gap:10px;padding:14px 16px;border:2px solid #4e54c8;border-radius:10px;cursor:pointer;background:#f0efff;transition:all .2s;">
                                        <input type="radio" name="voucher_delivery_type" id="delivery_manual" value="manual" checked
                                            style="margin-top:3px;accent-color:#4e54c8;">
                                        <div>
                                            <div style="font-weight:600;font-size:13px;color:#4e54c8;">Manual code entry</div>
                                            <div style="font-size:11.5px;color:#6c757d;margin-top:2px;">Customer enters a code to claim</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Voucher Code Input — shown for Manual delivery -->
                        <div id="voucher-code-wrap" class="offer-form-group mb-3" style="display:block;">
                            <label>Voucher Code <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="voucher_coupon_code"
                                       name="coupon_code"
                                       placeholder="e.g. GV-GOLD500"
                                       style="text-transform:uppercase;font-weight:600;letter-spacing:1px;"
                                       value="{{ $offer->coupon_code ?? old('coupon_code') }}">
                                <button type="button" class="btn btn-outline-secondary" id="btn-generate-voucher-code" title="Auto-generate code">
                                    <i class="fa fa-random"></i> Generate
                                </button>
                            </div>
                            <div class="input-hint">Code customers will enter at checkout to claim this voucher</div>
                        </div>

                        <!-- Value + Validity -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="offer-form-group mb-0">
                                    <label>Voucher Value <span class="required">*</span></label>
                                    <div class="input-with-unit">
                                        <input type="number" class="form-control" id="voucher_value"
                                               name="voucher_value" placeholder="500" min="1"
                                               value="{{ $offer->voucher_value ?? old('voucher_value') }}">
                                        <span class="unit-label">₹</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="offer-form-group mb-0">
                                    <label>Valid for (after issue)</label>
                                    <div class="input-with-unit">
                                        <input type="number" class="form-control" id="voucher_validity_days"
                                               name="voucher_validity_days" placeholder="90" min="1"
                                               value="{{ $offer->voucher_validity_days ?? old('voucher_validity_days', 30) }}">
                                        <span class="unit-label" style="right:12px;font-size:12px;">days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="offer-form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="offer_desc" name="description"
                                  placeholder="Flat 30% OFF on selected products" rows="3">{{ $offer->description ?? old('description') }}</textarea>
                    </div>

                    <!-- Homepage Banner Settings -->
                    <div class="card mt-4 border-light-gray" style="background: #fdfdfd; border-radius: 8px; border: 1px solid #e3e6f0;">
                        <div class="card-body">
                            <h5 class="fw-semibold text-primary mb-3"><i class="fa fa-picture-o me-2"></i>Homepage Banner Settings</h5>

                            <div class="offer-form-group mb-3">
                                <div class="status-toggle-wrap">
                                    <label class="status-toggle">
                                        <input type="checkbox" id="show_as_banner" name="show_as_banner" value="1"
                                               {{ (isset($offer) && $offer->show_as_banner) ? 'checked' : '' }}>
                                        <span class="status-slider"></span>
                                    </label>
                                    <span class="status-label" style="font-weight: 500; font-size: 13px; margin-left: 8px;">Show as Homepage Banner</span>
                                </div>
                            </div>

                            <div id="banner_fields" class="{{ (isset($offer) && $offer->show_as_banner) ? '' : 'd-none' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="offer-form-group">
                                            <label>Banner Position <span class="text-danger">*</span></label>
                                            <select class="form-control" id="banner_position" name="banner_position">
                                                <option value="">Select position…</option>
                                                <option value="main_slider" {{ (isset($offer) && $offer->banner_position === 'main_slider') ? 'selected' : '' }}>Top Slider (Main Carousel)</option>
                                                <option value="promo_1" {{ (isset($offer) && $offer->banner_position === 'promo_1') ? 'selected' : '' }}>Promo Banner Row 1</option>
                                                <option value="promo_2" {{ (isset($offer) && $offer->banner_position === 'promo_2') ? 'selected' : '' }}>Promo Banner Row 2</option>
                                                <option value="spotlight" {{ (isset($offer) && $offer->banner_position === 'spotlight') ? 'selected' : '' }}>Spotlight Section (Buy 1 Get 1)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="offer-form-group">
                                            <label>Banner Image <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*">
                                            @if(isset($offer) && $offer->banner_image)
                                                <div class="mt-2">
                                                    <label class="d-block text-muted" style="font-size:11px;">Current Banner:</label>
                                                    <img src="{{ asset($offer->banner_image) }}" alt="Banner Image" style="max-height: 80px; object-fit: cover; border-radius: 4px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="offer-form-group mt-3 mb-0">
                        <label>Status</label>
                        <div class="status-toggle-wrap mt-1">
                            <label class="status-toggle">
                                <input type="checkbox" id="offer_status"
                                       {{ (!isset($offer) || $offer->status === 'active') ? 'checked' : '' }}>
                                <span class="status-slider"></span>
                            </label>
                            <span class="{{ (!isset($offer) || $offer->status === 'active') ? 'status-label active-label' : 'status-label' }}" id="statusText">
                                {{ (!isset($offer) || $offer->status === 'active') ? 'Active' : ucfirst($offer->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ═══ Card 2: Offer Validity & Conditions ═══ -->
                <div class="offer-card">
                    <div class="offer-card-title"><i class="fa fa-calendar"></i> When does it run?</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="offer-form-group">
                                <label>Start Date <span class="required">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ isset($offer) && $offer->start_date ? $offer->start_date->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="offer-form-group">
                                <label>End Date <span class="required">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ isset($offer) && $offer->end_date ? $offer->end_date->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- ── Audience Targeting (always visible for ALL offer types) ── -->
                    <div id="audiencePanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="offer-form-group">
                                    <label>Who can use it?</label>
                                    <select class="form-control" id="user_type" name="user_type">
                                        <option value="all" {{ (isset($offer) && $offer->user_type === 'all') ? 'selected' : '' }}>All Users / Guests</option>
                                        @if(isset($memberships))
                                            @foreach($memberships as $m)
                                                <option value="{{ $m->card_id }}" {{ (isset($offer) && $offer->user_type == $m->card_id) ? 'selected' : '' }}>{{ $m->card_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="input-hint">Restrict this offer to a specific membership tier, or leave as "All Users" for everyone</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Coupon-only: Usage Limits (hidden for BOGO / Voucher) ── -->
                    <div id="couponUsageLimitsPanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="offer-form-group mb-0">
                                    <label>Total Usage Limit</label>
                                    <input type="number" class="form-control" id="usage_limit"
                                           name="usage_limit" placeholder="Unlimited"
                                           value="{{ $offer->usage_limit ?? old('usage_limit') }}" min="1">
                                    <div class="input-hint">Total redemptions storewide</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="offer-form-group mb-0">
                                    <label>Per-User Limit</label>
                                    <input type="number" class="form-control" id="usage_limit_per_user"
                                           name="usage_limit_per_user" placeholder="1"
                                           value="{{ $offer->usage_limit_per_user ?? 1 }}" min="1">
                                    <div class="input-hint">Max uses per customer</div>
                                </div>
                            </div>
                        </div>
                        <div class="coupon-limits-note d-none" style="margin-top:10px;">Usage limits apply to discount offers only — BOGO and Gift Vouchers are managed by their own trigger rules.</div>
                    </div>
                </div>

                <!-- ═══ Card 3: Apply Offer On ═══ -->
                <div class="offer-card">
                    <div class="offer-card-title"><i class="fa fa-bullseye"></i> Apply Offer On</div>

                    <div class="apply-radio-grid">
                        <div class="apply-radio-card">
                            <input type="radio" name="apply_on" id="apply_all" value="all_products"
                                   {{ (!isset($offer) || $offer->apply_on === 'all_products') ? 'checked' : '' }}>
                            <label class="apply-radio-label" for="apply_all">
                                <i class="fa fa-globe"></i>
                                <span>All Products</span>
                            </label>
                        </div>
                        <div class="apply-radio-card">
                            <input type="radio" name="apply_on" id="apply_category" value="specific_category"
                                   {{ (isset($offer) && $offer->apply_on === 'specific_category') ? 'checked' : '' }}>
                            <label class="apply-radio-label" for="apply_category">
                                <i class="fa fa-th-large"></i>
                                <span>Specific Category</span>
                            </label>
                        </div>
                        <div class="apply-radio-card">
                            <input type="radio" name="apply_on" id="apply_brand" value="specific_brand"
                                   {{ (isset($offer) && $offer->apply_on === 'specific_brand') ? 'checked' : '' }}>
                            <label class="apply-radio-label" for="apply_brand">
                                <i class="fa fa-star"></i>
                                <span>Specific Brand</span>
                            </label>
                        </div>
                        <div class="apply-radio-card">
                            <input type="radio" name="apply_on" id="apply_products" value="specific_products"
                                   {{ (isset($offer) && $offer->apply_on === 'specific_products') ? 'checked' : '' }}>
                            <label class="apply-radio-label" for="apply_products">
                                <i class="fa fa-cube"></i>
                                <span>Specific Products</span>
                            </label>
                        </div>
                    </div>

                    <!-- Category Selector -->
                    <div class="conditional-panel {{ (isset($offer) && $offer->apply_on === 'specific_category') ? 'show' : '' }}" id="panel-category">
                        <div class="offer-form-group mb-0">
                            <label>Select Category <span class="required">*</span></label>
                            <select class="form-control" id="select_category" name="categories[]" multiple>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (isset($offer) && is_array($offer->category_ids) && in_array($cat->id, $offer->category_ids)) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-hint">Search or select one or more categories</div>
                        </div>
                    </div>

                    <!-- Brand Selector -->
                    <div class="conditional-panel {{ (isset($offer) && $offer->apply_on === 'specific_brand') ? 'show' : '' }}" id="panel-brand">
                        <div class="offer-form-group mb-0">
                            <label>Select Brand <span class="required">*</span></label>
                            <select class="form-control" id="select_brand" name="brands[]" multiple>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->brand_id }}" {{ (isset($offer) && is_array($offer->brand_ids) && in_array($brand->brand_id, $offer->brand_ids)) ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                                @endforeach
                            </select>
                            <div class="input-hint">Search or select one or more brands</div>
                        </div>
                    </div>

                    <!-- Product Selector -->
                    <div class="conditional-panel {{ (isset($offer) && $offer->apply_on === 'specific_products') ? 'show' : '' }}" id="panel-products">
                        <div class="product-search-box">
                            <span class="search-icon"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control" id="productSearchInput"
                                   placeholder="Search products by name, SKU, or brand...">
                        </div>

                        <div class="search-spinner" id="productSearchSpinner">
                            <i class="fa fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Searching products...</p>
                        </div>

                        <div class="product-table-wrap" id="productTableWrap">
                            <table class="table" id="productSelectTable">
                                <thead>
                                    <tr>
                                        <th style="width:40px">
                                            <input type="checkbox" class="form-check-input" id="selectAllProducts">
                                        </th>
                                        <th style="width:50px">Image</th>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Brand</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fa fa-search fa-2x mb-2 d-block" style="opacity:0.3"></i>
                                            Type to search products...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="selected-count-badge" id="selectedProductsBadge" style="display:none">
                            <i class="fa fa-check-circle"></i>
                            <span id="selectedProductsCount">0</span> products selected
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="offer-info-alert" id="applyInfoAlert">
                        <i class="fa fa-info-circle"></i>
                        <div id="applyInfoText">This offer will apply on <strong>all products</strong> in your store.</div>
                    </div>
                </div>

            </div>

            <!-- ══════════ RIGHT COLUMN — LIVE PREVIEW PANEL ══════════ -->
            <div class="col-lg-4">
                <div class="preview-panel">
                    <div class="preview-panel-title">
                        <i class="fa fa-eye"></i> Offer Preview
                    </div>

                    <div class="preview-item">
                        <span class="preview-item-label">Offer Name</span>
                        <span class="preview-item-value" id="live_name">—</span>
                    </div>
                    <div class="preview-item">
                        <span class="preview-item-label">Discount</span>
                        <span class="preview-item-value" id="live_discount">—</span>
                    </div>
                    <div class="preview-item d-none">
                        <span class="preview-item-label">Coupon Code</span>
                        <span class="preview-item-value" id="live_coupon">—</span>
                    </div>
                    <div class="preview-item">
                        <span class="preview-item-label">Validity</span>
                        <span class="preview-item-value" id="live_validity">—</span>
                    </div>

                    <hr class="preview-divider">

                    <div class="preview-highlight">
                        <div class="count" id="live_product_count">All</div>
                        <div class="label">Applied Products</div>
                    </div>

                    <div class="offer-info-alert mt-3" style="font-size:11px">
                        <i class="fa fa-info-circle"></i>
                        <div id="live_apply_desc">This offer applies to all products</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Bottom Actions ── -->
        <div class="wizard-actions">
            <div>
                <a href="{{ url(config('app.admin_path').'/offers') }}" class="btn btn-wizard-cancel">
                    <i class="fa fa-times mr-1"></i> Cancel
                </a>
            </div>
            <div class="d-flex gap-2" style="gap:10px">
                <button type="button" class="btn btn-wizard-draft" id="btnDraft" onclick="submitOffer('draft')">
                    <i class="fa fa-save mr-1"></i> Save Draft
                </button>
                <button type="button" class="btn btn-wizard-activate" id="btnActivate" onclick="submitOffer('active')">
                    <i class="fa fa-check-circle mr-1"></i> Save &amp; Activate
                </button>
            </div>
        </div>

    </form>

</div>
</div>
</section>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

let selectedProducts = new Set();
let productSearchTimer = null;
let selectedBogoThirdProducts = new Set();
let bogoThirdProductSearchTimer = null;

/* ═══════════════════════════════════════════
   OFFER TYPE — STATE & CONFIG MAP
   ═══════════════════════════════════════════ */

let offerType = '{{ old('offer_type', isset($offer) ? $offer->offer_type : 'buy1get1') }}';

const offerTypeCfg = {
    'percentage_discount': { group: 'coupon',  fields: 'fieldsCoupon',  title: 'Discount Details',     icon: 'fa-tags',   discSuffix: '%' },
    'flat_discount':       { group: 'coupon',  fields: 'fieldsCoupon',  title: 'Discount Details',     icon: 'fa-tags',   discSuffix: '₹' },
    'buy1get1':            { group: 'bogo',    fields: 'fieldsBogo',    title: 'Buy X Get Y Details',  icon: 'fa-plus',   discSuffix: '' },
    'gift_voucher':        { group: 'voucher', fields: 'fieldsVoucher', title: 'Gift Voucher Details', icon: 'fa-ticket', discSuffix: '' },
};

/* ── Apply config when type changes ── */
function applyOfferTypeConfig() {
    const cfg = offerTypeCfg[offerType] || offerTypeCfg['buy1get1'];

    // Update card title
    document.getElementById('offerDetailTitleText').textContent = cfg.title;
    document.getElementById('offerDetailIcon').className = 'fa ' + cfg.icon;

    // Show correct fields panel
    ['fieldsCoupon', 'fieldsBogo', 'fieldsVoucher'].forEach(id => {
        document.getElementById(id).style.display = (id === cfg.fields) ? 'block' : 'none';
    });

    // Sync discount unit suffix for coupon types
    if (cfg.group === 'coupon') {
        const dtRadio = document.querySelector('input[name="discount_type"]:checked');
        document.getElementById('discountUnit').textContent = (dtRadio && dtRadio.value === 'fixed') ? '₹' : '%';
    }

    // Audience panel — ALWAYS visible for all offer types
    // (membership targeting applies to BOGO and Voucher too)

    // Usage limits panel — coupon-only (hidden for BOGO / Voucher)
    const usageLimitsPanel = document.getElementById('couponUsageLimitsPanel');
    if (usageLimitsPanel) {
        usageLimitsPanel.style.display = (cfg.group === 'coupon') ? 'block' : 'none';
    }

    updateLivePreview();
}

/* ── Offer type card click ── */
document.querySelectorAll('#offerTypeRow .offer-type-card').forEach(card => {
    card.addEventListener('click', function () {
        document.querySelectorAll('#offerTypeRow .offer-type-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        offerType = this.dataset.type;
        document.getElementById('offer_type_hidden').value = offerType;
        applyOfferTypeConfig();
    });
});

/* ═══════════════════════════════════════════
   FORM VALIDATION (type-aware)
   ═══════════════════════════════════════════ */

function validateForm() {
    const name = document.getElementById('offer_name').value.trim();
    if (!name) {
        Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter an Offer Name.', confirmButtonColor: '#07484A' });
        document.getElementById('offer_name').focus();
        return false;
    }

    const cfg = offerTypeCfg[offerType] || offerTypeCfg['buy1get1'];

    if (cfg.group === 'coupon') {
        const dv = document.getElementById('discount_value').value;
        if (!dv || parseFloat(dv) <= 0) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter a valid Discount Value.', confirmButtonColor: '#07484A' });
            document.getElementById('discount_value').focus();
            return false;
        }
    }

    if (cfg.group === 'bogo') {
        const bonusEnabled = document.getElementById('bogo_bonus_enabled')?.checked;
        if (bonusEnabled) {
            const extraDisc = document.getElementById('bogo_extra_discount')?.value;
            if (!extraDisc || parseFloat(extraDisc) <= 0 || parseFloat(extraDisc) > 99) {
                Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter a valid 3rd item discount percentage (1-99%).', confirmButtonColor: '#07484A' });
                document.getElementById('bogo_extra_discount').focus();
                return false;
            }

            const thirdScope = document.querySelector('input[name="bogo_third_apply_on"]:checked')?.value || 'same_as_bogo';
            if (thirdScope === 'specific_brand') {
                const sel = document.getElementById('select_bogo_third_brand');
                if (!sel.selectedOptions.length) {
                    Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select at least one brand for the 3rd item discount.', confirmButtonColor: '#07484A' });
                    return false;
                }
            } else if (thirdScope === 'specific_category') {
                const sel = document.getElementById('select_bogo_third_category');
                if (!sel.selectedOptions.length) {
                    Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select at least one category for the 3rd item discount.', confirmButtonColor: '#07484A' });
                    return false;
                }
            } else if (thirdScope === 'specific_products' && selectedBogoThirdProducts.size === 0) {
                Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select at least one product for the 3rd item discount.', confirmButtonColor: '#07484A' });
                return false;
            }
        }
    }

    if (cfg.group === 'voucher') {
        const vv = document.getElementById('voucher_value').value;
        const vd = document.getElementById('voucher_validity_days').value;
        if (!vv || parseFloat(vv) <= 0) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter a valid Voucher Value.', confirmButtonColor: '#07484A' });
            document.getElementById('voucher_value').focus();
            return false;
        }
        if (!vd || parseInt(vd) < 1) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter a valid Voucher Validity (days).', confirmButtonColor: '#07484A' });
            document.getElementById('voucher_validity_days').focus();
            return false;
        }
    }

    const startDate = document.getElementById('start_date').value;
    const endDate   = document.getElementById('end_date').value;
    if (!startDate || !endDate) {
        Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select Start and End dates.', confirmButtonColor: '#07484A' });
        return false;
    }
    if (new Date(endDate) < new Date(startDate)) {
        Swal.fire({ icon: 'warning', title: 'Invalid Dates', text: 'End date must be after start date.', confirmButtonColor: '#07484A' });
        return false;
    }

    const applyOn = document.querySelector('input[name="apply_on"]:checked').value;
    if (applyOn === 'specific_category') {
        const sel = document.getElementById('select_category');
        if (!sel.selectedOptions.length) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select at least one category.', confirmButtonColor: '#07484A' });
            return false;
        }
    }
    if (applyOn === 'specific_brand') {
        const sel = document.getElementById('select_brand');
        if (!sel.selectedOptions.length) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select at least one brand.', confirmButtonColor: '#07484A' });
            return false;
        }
    }
    if (applyOn === 'specific_products' && selectedProducts.size === 0) {
        Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select at least one product.', confirmButtonColor: '#07484A' });
        return false;
    }

    if (document.getElementById('show_as_banner').checked) {
        const position = document.getElementById('banner_position').value;
        if (!position) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select a Banner Position.', confirmButtonColor: '#07484A' });
            return false;
        }
        const image = document.getElementById('banner_image').value;
        const hasExistingImage = "{{ isset($offer) && $offer->banner_image ? 'true' : 'false' }}";
        if (!image && hasExistingImage !== 'true') {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please upload a Banner Image.', confirmButtonColor: '#07484A' });
            return false;
        }
    }

    return true;
}

/* ═══════════════════════════════════════════
   DISCOUNT TYPE TOGGLE
   ═══════════════════════════════════════════ */

document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
    radio.addEventListener('change', function () {
        document.getElementById('discountUnit').textContent = this.value === 'percentage' ? '%' : '₹';
        updateLivePreview();
    });
});

/* ═══════════════════════════════════════════
   STATUS TOGGLE & BANNER TOGGLE
   ═══════════════════════════════════════════ */

document.getElementById('show_as_banner').addEventListener('change', function () {
    const fields = document.getElementById('banner_fields');
    if (this.checked) {
        fields.classList.remove('d-none');
    } else {
        fields.classList.add('d-none');
    }
});

document.getElementById('offer_status').addEventListener('change', function () {
    const label = document.getElementById('statusText');
    if (this.checked) {
        label.textContent = 'Active';
        label.className = 'status-label active-label';
    } else {
        label.textContent = 'Inactive';
        label.className = 'status-label';
    }
});

/* ═══════════════════════════════════════════
   APPLY ON — CONDITIONAL PANELS
   ═══════════════════════════════════════════ */

document.querySelectorAll('input[name="apply_on"]').forEach(radio => {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.conditional-panel').forEach(p => p.classList.remove('show'));
        switch (this.value) {
            case 'specific_category': document.getElementById('panel-category').classList.add('show'); break;
            case 'specific_brand':    document.getElementById('panel-brand').classList.add('show');    break;
            case 'specific_products': document.getElementById('panel-products').classList.add('show'); break;
        }
        updateApplyInfo();
        updateLivePreview();
    });
});

function updateApplyInfo() {
    const applyOn = document.querySelector('input[name="apply_on"]:checked').value;
    const infoText = document.getElementById('applyInfoText');
    const texts = {
        'all_products':      'This offer will apply on <strong>all products</strong> in your store.',
        'specific_category': 'This offer will apply on all products under <strong>selected categories</strong>.',
        'specific_brand':    'This offer will apply on all products under <strong>selected brands</strong>.',
        'specific_products': 'This offer will apply on <strong>specific products</strong> you select below.',
    };
    infoText.innerHTML = texts[applyOn] || '';
}

/* ═══════════════════════════════════════════
   PRODUCT SEARCH (AJAX) — Base Products
   ═══════════════════════════════════════════ */

document.getElementById('productSearchInput').addEventListener('input', function () {
    clearTimeout(productSearchTimer);
    const query = this.value.trim();

    if (query.length < 2) {
        document.getElementById('productTableBody').innerHTML = `
            <tr><td colspan="6" class="text-center text-muted py-4">
                <i class="fa fa-search fa-2x mb-2 d-block" style="opacity:0.3"></i>
                Type at least 2 characters to search...
            </td></tr>`;
        return;
    }

    productSearchTimer = setTimeout(() => {
        document.getElementById('productSearchSpinner').classList.add('show');
        document.getElementById('productTableWrap').style.display = 'none';

        $.ajax({
            url: "{{ url(config('app.admin_path').'/offers/search-products') }}",
            data: { search: query },
            success: function (products) {
                renderProductTable(products);
            },
            error: function () {
                document.getElementById('productTableBody').innerHTML = `
                    <tr><td colspan="6" class="text-center text-danger py-4">
                        <i class="fa fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                        Error loading products
                    </td></tr>`;
            },
            complete: function () {
                document.getElementById('productSearchSpinner').classList.remove('show');
                document.getElementById('productTableWrap').style.display = 'block';
            }
        });
    }, 400);
});

function renderProductTable(products) {
    const tbody = document.getElementById('productTableBody');

    if (!products.length) {
        tbody.innerHTML = `
            <tr><td colspan="6" class="text-center text-muted py-4">
                <i class="fa fa-inbox fa-2x mb-2 d-block" style="opacity:0.3"></i>
                No products found
            </td></tr>`;
        return;
    }

    tbody.innerHTML = products.map(p => {
        const checked = selectedProducts.has(p.id) ? 'checked' : '';
        const imgSrc  = p.product_image
            ? '{{ asset("") }}' + p.product_image
            : '{{ asset("assets/images/speckart-Icons/Dashboard.png") }}';
        return `
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input product-checkbox"
                           value="${p.id}" data-name="${escapeHtml(p.product_name || '')}"
                           data-price="${p.Retail_Price || 0}" ${checked}>
                </td>
                <td><img src="${imgSrc}" class="product-thumb" alt=""
                         onerror="this.src='{{ asset('assets/images/speckart-Icons/Dashboard.png') }}'"></td>
                <td><strong>${escapeHtml(p.product_name || '—')}</strong></td>
                <td><code>${escapeHtml(p.product_code || '—')}</code></td>
                <td>${escapeHtml(p.Company || '—')}</td>
                <td>₹${numberFormat(p.Retail_Price || 0)}</td>
            </tr>`;
    }).join('');

    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const id = parseInt(this.value);
            if (this.checked) selectedProducts.add(id);
            else selectedProducts.delete(id);
            updateProductCount();
            updateLivePreview();
        });
    });
}

document.getElementById('selectAllProducts').addEventListener('change', function () {
    const cbs = document.querySelectorAll('.product-checkbox');
    cbs.forEach(cb => {
        cb.checked = this.checked;
        const id = parseInt(cb.value);
        if (this.checked) selectedProducts.add(id);
        else selectedProducts.delete(id);
    });
    updateProductCount();
    updateLivePreview();
});

function updateProductCount() {
    const count = selectedProducts.size;
    const badge = document.getElementById('selectedProductsBadge');
    document.getElementById('selectedProductsCount').textContent = count;
    badge.style.display = count > 0 ? 'inline-flex' : 'none';
}

/* ═══════════════════════════════════════════
   3RD PRODUCT BOGO SEARCH (AJAX)
   ═══════════════════════════════════════════ */

const bogoThirdSearchInput = document.getElementById('bogoThirdProductSearchInput');
if (bogoThirdSearchInput) {
    bogoThirdSearchInput.addEventListener('input', function () {
        clearTimeout(bogoThirdProductSearchTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            document.getElementById('bogoThirdProductTableBody').innerHTML = `
                <tr><td colspan="6" class="text-center text-muted py-3">
                    Type at least 2 characters to search products...
                </td></tr>`;
            return;
        }

        bogoThirdProductSearchTimer = setTimeout(() => {
            $.ajax({
                url: "{{ url(config('app.admin_path').'/offers/search-products') }}",
                data: { search: query },
                success: function (products) {
                    renderBogoThirdProductTable(products);
                },
                error: function () {
                    document.getElementById('bogoThirdProductTableBody').innerHTML = `
                        <tr><td colspan="6" class="text-center text-danger py-3">Error loading products</td></tr>`;
                }
            });
        }, 400);
    });
}

function renderBogoThirdProductTable(products) {
    const tbody = document.getElementById('bogoThirdProductTableBody');
    if (!tbody) return;

    if (!products.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-3">No products found</td></tr>`;
        return;
    }

    tbody.innerHTML = products.map(p => {
        const checked = selectedBogoThirdProducts.has(p.id) ? 'checked' : '';
        const imgSrc  = p.product_image
            ? '{{ asset("") }}' + p.product_image
            : '{{ asset("assets/images/speckart-Icons/Dashboard.png") }}';
        return `
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input bogo-third-product-checkbox"
                           value="${p.id}" ${checked}>
                </td>
                <td><img src="${imgSrc}" class="product-thumb" style="width:28px;height:28px;" alt=""></td>
                <td><strong>${escapeHtml(p.product_name || '—')}</strong></td>
                <td><code>${escapeHtml(p.product_code || '—')}</code></td>
                <td>${escapeHtml(p.Company || '—')}</td>
                <td>₹${numberFormat(p.Retail_Price || 0)}</td>
            </tr>`;
    }).join('');

    document.querySelectorAll('.bogo-third-product-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const id = parseInt(this.value);
            if (this.checked) selectedBogoThirdProducts.add(id);
            else selectedBogoThirdProducts.delete(id);
            updateBogoThirdProductCount();
        });
    });
}

const selectAllBogoThird = document.getElementById('selectAllBogoThirdProducts');
if (selectAllBogoThird) {
    selectAllBogoThird.addEventListener('change', function () {
        document.querySelectorAll('.bogo-third-product-checkbox').forEach(cb => {
            cb.checked = this.checked;
            const id = parseInt(cb.value);
            if (this.checked) selectedBogoThirdProducts.add(id);
            else selectedBogoThirdProducts.delete(id);
        });
        updateBogoThirdProductCount();
    });
}

function updateBogoThirdProductCount() {
    const count = selectedBogoThirdProducts.size;
    const badge = document.getElementById('bogoThirdSelectedBadge');
    const span = document.getElementById('bogoThirdSelectedCount');
    if (span) span.textContent = count;
    if (badge) badge.style.display = count > 0 ? 'inline-flex' : 'none';
}

/* ═══════════════════════════════════════════
   LIVE PREVIEW (RIGHT PANEL)
   ═══════════════════════════════════════════ */

document.querySelectorAll('#offerForm input, #offerForm select, #offerForm textarea').forEach(el => {
    el.addEventListener('input',  updateLivePreview);
    el.addEventListener('change', updateLivePreview);
});

function updateLivePreview() {
    const name    = document.getElementById('offer_name').value || '—';
    const startDate = document.getElementById('start_date').value;
    const endDate   = document.getElementById('end_date').value;
    const applyOn = document.querySelector('input[name="apply_on"]:checked')?.value || 'all_products';
    const cfg     = offerTypeCfg[offerType] || offerTypeCfg['buy1get1'];

    document.getElementById('live_name').textContent = name;

    // Coupon code (only relevant for coupon types)
    const couponEl = document.getElementById('coupon_code');
    document.getElementById('live_coupon').textContent = (couponEl && couponEl.value) ? couponEl.value.toUpperCase() : '—';

    // Discount display per offer type
    if (cfg.group === 'coupon') {
        const discType = document.querySelector('input[name="discount_type"]:checked')?.value || 'percentage';
        const discVal  = document.getElementById('discount_value').value || '0';
        document.getElementById('live_discount').textContent =
            discType === 'percentage' ? discVal + '% OFF' : '₹' + numberFormat(discVal) + ' OFF';
    } else if (cfg.group === 'bogo') {
        const bonusEnabled = document.getElementById('bogo_bonus_enabled')?.checked;
        const bonusPct     = document.getElementById('bogo_extra_discount')?.value || '60';
        const previewBonusSpan = document.getElementById('preview_bonus_pct');
        if (previewBonusSpan) previewBonusSpan.textContent = bonusPct;

        if (bonusEnabled) {
            document.getElementById('live_discount').textContent = 'Buy 1 Get 1 Free + ' + bonusPct + '% OFF on 3rd';
        } else {
            document.getElementById('live_discount').textContent = 'Buy 1 Get 1 Free (100% OFF)';
        }
        document.getElementById('live_coupon').textContent = '—';
    } else if (cfg.group === 'voucher') {
        const vval = document.getElementById('voucher_value').value || '0';
        document.getElementById('live_discount').textContent = '₹' + numberFormat(vval) + ' Voucher';
        document.getElementById('live_coupon').textContent   = '—';
    }

    // Validity
    document.getElementById('live_validity').textContent = (startDate && endDate)
        ? formatDate(startDate) + ' → ' + formatDate(endDate)
        : '—';

    // Product count
    const applyLabels = {
        'all_products':      'All',
        'specific_category': getSelectedText('select_category'),
        'specific_brand':    getSelectedText('select_brand'),
        'specific_products': selectedProducts.size.toString()
    };
    document.getElementById('live_product_count').textContent = applyLabels[applyOn] || 'All';

    const applyDescs = {
        'all_products':      'This offer applies to all products',
        'specific_category': 'Applied to selected categories',
        'specific_brand':    'Applied to selected brands',
        'specific_products': `This offer applies to ${selectedProducts.size} product(s)`
    };
    document.getElementById('live_apply_desc').textContent = applyDescs[applyOn] || '';
}

/* ═══════════════════════════════════════════
   FORM SUBMISSION
   ═══════════════════════════════════════════ */

function submitOffer(status) {
    if (!validateForm()) return;

    const formData  = new FormData(document.getElementById('offerForm'));
    let finalStatus = status;
    if (status === 'active') {
        finalStatus = document.getElementById('offer_status').checked ? 'active' : 'inactive';
    }
    formData.set('status', finalStatus);
    formData.set('offer_type', offerType);

    // Uppercase coupon code if present
    const couponEl = document.getElementById('coupon_code');
    if (couponEl) formData.set('coupon_code', couponEl.value.toUpperCase());

    // Append selected base product IDs
    if (document.querySelector('input[name="apply_on"]:checked').value === 'specific_products') {
        selectedProducts.forEach(id => formData.append('products[]', id));
    }

    // Append selected 3rd item product IDs if specific_products chosen
    const thirdScope = document.querySelector('input[name="bogo_third_apply_on"]:checked')?.value;
    if (thirdScope === 'specific_products') {
        selectedBogoThirdProducts.forEach(id => formData.append('bogo_third_products[]', id));
    }

    const btns = document.querySelectorAll('.wizard-actions .btn');
    btns.forEach(b => b.disabled = true);

    const requestUrl = "{{ isset($offer) ? url(config('app.admin_path').'/offers/' . $offer->id) : url(config('app.admin_path').'/offers') }}";
    @if(isset($offer))
        formData.append('_method', 'PUT');
    @endif

    $.ajax({
        url: requestUrl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: status === 'draft' ? 'Draft Saved!' : ("{{ isset($offer) ? 'true' : 'false' }}" === 'true' ? 'Offer Updated!' : 'Offer Created!'),
                html: res.message,
                confirmButtonColor: '#07484A',
                confirmButtonText: 'View Offer List'
            }).then(() => {
                window.location.href = "{{ url(config('app.admin_path').'/offers') }}";
            });
        },
        error: function (xhr) {
            let errors = xhr.responseJSON?.errors;
            let msg    = xhr.responseJSON?.message ?? 'Something went wrong.';
            if (errors) {
                msg = '<ul class="text-start mb-0 ps-3">'
                    + Object.values(errors).flat().map(e => `<li>${e}</li>`).join('')
                    + '</ul>';
            }
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                html: msg,
                confirmButtonText: 'OK, fix it',
                confirmButtonColor: '#07484A',
            });
        },
        complete: function () {
            btns.forEach(b => b.disabled = false);
        }
    });
}

/* ═══════════════════════════════════════════
   HELPER FUNCTIONS
   ═══════════════════════════════════════════ */

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
function numberFormat(num) {
    return parseFloat(num || 0).toLocaleString('en-IN');
}
function formatDate(dateStr) {
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return d.getDate().toString().padStart(2,'0') + '-' + months[d.getMonth()] + '-' + d.getFullYear();
}
function getSelectedText(selectId) {
    const sel = document.getElementById(selectId);
    if (!sel) return '0';
    return sel.selectedOptions.length ? sel.selectedOptions.length.toString() : '0';
}

/* ═══════════════════════════════════════════
   DOM READY INIT
   ═══════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

    // Set default dates for new offers
    @if(!isset($offer))
        const today     = new Date().toISOString().split('T')[0];
        const nextMonth = new Date(Date.now() + 30*24*60*60*1000).toISOString().split('T')[0];
        document.getElementById('start_date').value = today;
        document.getElementById('end_date').value   = nextMonth;
    @else
        @if($offer->apply_on === 'specific_products' && isset($selected_products))
            @foreach($offer->product_ids as $pId)
                selectedProducts.add({{ $pId }});
            @endforeach
            const preloadedProducts = @json($selected_products);
            renderProductTable(preloadedProducts);
            updateProductCount();
        @endif

        @if($offer->bogo_third_apply_on === 'specific_products' && isset($selected_bogo_third_products))
            @foreach($offer->bogo_third_product_ids as $pId)
                selectedBogoThirdProducts.add({{ $pId }});
            @endforeach
            const preloadedBogoThird = @json($selected_bogo_third_products);
            renderBogoThirdProductTable(preloadedBogoThird);
            updateBogoThirdProductCount();
        @endif
    @endif

    // Pre-activate discount type radio visual
    const checkedRadio = document.querySelector('input[name="discount_type"]:checked');
    if (checkedRadio) {
        const label = document.querySelector(`label[for="${checkedRadio.id}"]`);
        if (label) label.classList.add('active');
    }

    // Bonus Tier toggle listener
    const bogoBonus = document.getElementById('bogo_bonus_enabled');
    if (bogoBonus) {
        bogoBonus.addEventListener('change', function () {
            document.getElementById('bogoTierFields').style.display = this.checked ? 'block' : 'none';
            updateLivePreview();
        });
    }

    // 3rd Item Scope radio change listener
    document.querySelectorAll('input[name="bogo_third_apply_on"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('input[name="bogo_third_apply_on"]').forEach(r => {
                const lbl = r.closest('.radio-card-label');
                if (lbl) lbl.classList.remove('active');
            });
            const activeLbl = this.closest('.radio-card-label');
            if (activeLbl) activeLbl.classList.add('active');

            document.getElementById('panel-bogo-third-brand').style.display    = (this.value === 'specific_brand') ? 'block' : 'none';
            document.getElementById('panel-bogo-third-category').style.display = (this.value === 'specific_category') ? 'block' : 'none';
            document.getElementById('panel-bogo-third-products').style.display = (this.value === 'specific_products') ? 'block' : 'none';
        });
    });

    // Initialize Select2 for Categories & Brands
    $('#select_category').select2({ placeholder: "Select categories...", allowClear: true, width: '100%' });
    $('#select_brand').select2({    placeholder: "Select brands...",     allowClear: true, width: '100%' });
    $('#select_bogo_third_brand').select2({    placeholder: "Select brands for 3rd item...",     allowClear: true, width: '100%' });
    $('#select_bogo_third_category').select2({ placeholder: "Select categories for 3rd item...", allowClear: true, width: '100%' });

    $('#select_category, #select_brand').on('change', function () { updateLivePreview(); });

    // Generate SPECKART Coupon Code click handler
    $(document).on('click', '.btn-generate-speckart-code', function () {
        const discVal = $('#discount_value').val();
        let suffix = '';
        if (discVal && parseFloat(discVal) > 0) {
            suffix = parseInt(discVal).toString();
        }
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let rand = '';
        for (let i = 0; i < 4; i++) {
            rand += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const code = 'SPECKART' + (suffix ? suffix : '') + rand;
        $('#coupon_code').val(code);
        updateLivePreview();
    });

    // ── Voucher Delivery Type Toggle ──────────────────────────────
    function applyVoucherDeliveryState(val) {
        const codeWrap = document.getElementById('voucher-code-wrap');
        const cardAuto = document.getElementById('card-auto');
        const cardManual = document.getElementById('card-manual');
        if (!codeWrap) return;

        if (val === 'manual') {
            codeWrap.style.display = 'block';
            // Style active card
            if (cardAuto)   { cardAuto.style.border   = '2px solid #dee2e6'; cardAuto.style.background   = '#fff'; }
            if (cardManual) { cardManual.style.border = '2px solid #4e54c8'; cardManual.style.background = '#f0efff'; }
        } else {
            codeWrap.style.display = 'none';
            // Clear coupon code when switching back to auto
            const ccInput = document.getElementById('voucher_coupon_code');
            if (ccInput) ccInput.value = '';
            if (cardManual) { cardManual.style.border = '2px solid #dee2e6'; cardManual.style.background = '#fff'; }
            if (cardAuto)   { cardAuto.style.border   = '2px solid #4e54c8'; cardAuto.style.background   = '#f0efff'; }
        }
    }

    // Listen for radio changes
    document.querySelectorAll('input[name="voucher_delivery_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            applyVoucherDeliveryState(this.value);
        });
    });

    // Default delivery type to manual (Auto-linked option hidden)
    const manualRadio = document.getElementById('delivery_manual');
    if (manualRadio) { manualRadio.checked = true; applyVoucherDeliveryState('manual'); }

    // Generate Voucher Code Button
    document.getElementById('btn-generate-voucher-code') && document.getElementById('btn-generate-voucher-code').addEventListener('click', function () {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let rand = '';
        for (let i = 0; i < 6; i++) rand += chars.charAt(Math.floor(Math.random() * chars.length));
        const code = 'GV-' + rand;
        document.getElementById('voucher_coupon_code').value = code;
        updateLivePreview();
    });

    // Apply correct offer type config on page load
    applyOfferTypeConfig();
    updateLivePreview();
});
</script>
@endsection
