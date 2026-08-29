@extends('website.layout.master')
@section('content')

{{-- ═══════════════════════════════════════════════════
     SPECKART — Modern Premium Shipping Details UI
     Consistent with Shopping Cart, Profile & Payment Flow
═══════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --sp-primary: #07484a;
        --sp-primary-dark: #053738;
        --sp-primary-soft: #f0fdfc;
        --sp-teal: #11abb0;
        --sp-teal-soft: #e6fffa;
        --sp-green: #16a34a;
        --sp-green-soft: #f0fdf4;
        --sp-text: #0f172a;
        --sp-text-muted: #64748b;
        --sp-border: #e2e8f0;
        --sp-card-bg: #ffffff;
        --sp-radius: 16px;
        --sp-shadow: 0 4px 20px rgba(7, 72, 74, 0.07);
    }

    .shipping-page-wrap {
        background: #f8fafc;
        min-height: 80vh;
        padding-bottom: 60px;
        font-family: 'Poppins', sans-serif;
    }

    /* ── Checkout Progress Wizard ── */
    .checkout-wizard-bar {
        background: #ffffff;
        border-bottom: 1px solid var(--sp-border);
        padding: 18px 0;
    }

    .checkout-wizard-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        margin: 0;
        padding: 0;
        list-style: none;
        flex-wrap: wrap;
    }

    .wizard-step-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
        color: var(--sp-text-muted);
        text-decoration: none;
    }

    .wizard-step-item.completed {
        color: var(--sp-green);
        font-weight: 600;
    }

    .wizard-step-item.completed .step-number {
        background: var(--sp-green-soft);
        color: var(--sp-green);
        border-color: var(--sp-green);
    }

    .wizard-step-item.active {
        color: var(--sp-primary);
        font-weight: 700;
    }

    .wizard-step-item.active .step-number {
        background: var(--sp-primary);
        color: #ffffff;
        border-color: var(--sp-primary);
        box-shadow: 0 2px 10px rgba(7, 72, 74, 0.3);
    }

    .wizard-step-item .step-number {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        border: 1.5px solid var(--sp-border);
        transition: all 0.2s ease;
    }

    .wizard-step-divider {
        color: #cbd5e1;
        font-size: 14px;
    }

    /* ── Main Cards ── */
    .sp-main-card {
        background: var(--sp-card-bg);
        border-radius: var(--sp-radius);
        border: 1px solid var(--sp-border);
        box-shadow: var(--sp-shadow);
        padding: 28px;
        transition: box-shadow 0.25s ease;
    }

    .sp-main-card:hover {
        box-shadow: 0 8px 30px rgba(7, 72, 74, 0.1);
    }

    .sp-card-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--sp-primary);
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sp-card-subtitle {
        font-size: 13px;
        color: var(--sp-text-muted);
        margin-bottom: 22px;
    }

    /* ── Compact & Premium Saved Address Cards ── */
    .saved-addr-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }

    .saved-addr-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
    }

    .saved-addr-card:hover {
        border-color: #00B9B9;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(7, 72, 74, 0.07);
    }

    .saved-addr-card.active {
        border: 2px solid var(--sp-primary);
        background: linear-gradient(135deg, #f0fdfc 0%, #ffffff 80%);
        box-shadow: 0 4px 16px rgba(7, 72, 74, 0.1);
    }

    .addr-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        gap: 8px;
    }

    /* Custom Selection Radio */
    .addr-radio-indicator {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background: #ffffff;
        flex-shrink: 0;
    }

    .saved-addr-card.active .addr-radio-indicator {
        border-color: var(--sp-primary);
        background: var(--sp-primary);
        box-shadow: 0 0 0 2px rgba(7, 72, 74, 0.15);
    }

    .addr-radio-indicator i {
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.2s ease;
    }

    .saved-addr-card.active .addr-radio-indicator i {
        opacity: 1;
        transform: scale(1);
    }

    /* Address Type Tags */
    .addr-type-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .addr-type-tag.type-home {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
    }

    .addr-type-tag.type-office {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ede9fe;
    }

    .addr-type-tag.type-other {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* Default Tag */
    .addr-default-tag {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 700;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    /* Recipient Name */
    .addr-user-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-left: 2px;
    }

    /* Action Buttons (Edit & Delete) */
    .btn-card-action {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }

    .btn-card-action i {
        color: var(--sp-primary);
        transition: color 0.2s ease;
    }

    .btn-card-action.btn-delete-action i {
        color: #ef4444;
    }

    .btn-card-action:hover {
        background: var(--sp-primary);
        color: #ffffff;
        border-color: var(--sp-primary);
    }

    .btn-card-action:hover i {
        color: #ffffff !important;
    }

    .btn-card-action.btn-delete-action:hover {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    .btn-card-action.btn-delete-action:hover i {
        color: #ffffff !important;
    }

    /* Address Text & Inline Contact */
    .addr-card-body {
        padding-left: 26px;
    }

    .addr-full-address {
        font-size: 12.5px;
        color: #475569;
        line-height: 1.4;
        margin-bottom: 4px;
        display: flex;
        align-items: flex-start;
        gap: 6px;
    }

    .addr-full-address i {
        color: #00B9B9;
        font-size: 13px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .addr-contact-inline {
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .addr-contact-inline i {
        color: #00B9B9;
        font-size: 11px;
    }

    .addr-contact-dot {
        color: #cbd5e1;
        font-weight: bold;
    }

    /* ── Add New Address Trigger Button ── */
    .btn-new-address-toggle {
        background: var(--sp-primary);
        color: #ffffff !important;
        border: none;
        border-radius: 10px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-new-address-toggle:hover {
        background: #0b5e61;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(7, 72, 74, 0.2);
    }

    /* ── Empty State ── */
    .addr-empty-state-box {
        text-align: center;
        padding: 40px 20px;
        border: 2px dashed #cbd5e1;
        border-radius: var(--sp-radius);
        background: #f8fafc;
    }

    .addr-empty-icon {
        width: 60px;
        height: 60px;
        background: #e0f2f1;
        color: var(--sp-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin: 0 auto 16px;
    }

    /* ── Modern Form Controls in Modal ── */
    .sp-form-label {
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }

    .sp-input {
        width: 100%;
        height: 46px;
        border-radius: 10px;
        border: 1.5px solid var(--sp-border);
        padding: 10px 14px;
        font-size: 13.5px;
        font-family: 'Poppins', sans-serif;
        color: var(--sp-text);
        background: #ffffff;
        transition: all 0.2s ease;
        outline: none;
    }

    .sp-input:focus {
        border-color: var(--sp-teal);
        box-shadow: 0 0 0 3px rgba(17, 171, 176, 0.15);
    }

    .sp-input::placeholder {
        color: #94a3b8;
        font-size: 13px;
    }

    /* Address Type Radio Selector Group */
    .sp-type-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .sp-type-group input[type="radio"] {
        display: none;
    }

    .sp-type-group label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        border: 1.5px solid var(--sp-border);
        background: #ffffff;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .sp-type-group label:hover {
        border-color: var(--sp-teal);
        color: var(--sp-primary);
    }

    .sp-type-group input[type="radio"]:checked + label {
        background: var(--sp-primary);
        color: #ffffff;
        border-color: var(--sp-primary);
        box-shadow: 0 2px 8px rgba(7, 72, 74, 0.2);
    }

    /* ── Primary Action Buttons ── */
    .sp-btn-proceed {
        width: 100%;
        height: 52px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #07484a 0%, #0c5e61 100%);
        color: #ffffff;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.2px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.22s ease;
        box-shadow: 0 4px 15px rgba(7, 72, 74, 0.2);
        cursor: pointer;
    }

    .sp-btn-proceed:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(7, 72, 74, 0.3);
        color: #ffffff;
    }

    /* ── Order Summary Card ── */
    .order-summary-sticky {
        position: sticky;
        top: 90px;
    }

    .summary-item-preview-list {
        max-height: 240px;
        overflow-y: auto;
        padding-right: 4px;
        margin-bottom: 18px;
    }

    .summary-item-preview-list::-webkit-scrollbar {
        width: 4px;
    }

    .summary-item-preview-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .summary-item-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .summary-item-row:last-child {
        border-bottom: none;
    }

    .summary-item-thumb-wrap {
        width: 54px;
        height: 42px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .summary-item-thumb {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 2px;
    }

    .summary-membership-icon {
        font-size: 22px;
    }

    .summary-item-info {
        flex: 1;
        min-width: 0;
    }

    .summary-item-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--sp-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.25;
    }

    .summary-item-sub {
        font-size: 11.5px;
        color: var(--sp-text-muted);
    }

    .summary-item-price {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--sp-text);
        text-align: right;
    }

    /* Bill Rows */
    .bill-table {
        width: 100%;
        margin-bottom: 16px;
    }

    .bill-table tr td {
        padding: 6px 0;
        font-size: 13.5px;
        color: #475569;
    }

    .bill-table .bill-total-row {
        border-top: 1.5px dashed var(--sp-border);
    }

    .bill-table .bill-total-row td {
        padding-top: 14px;
        font-size: 17px;
        font-weight: 800;
        color: var(--sp-primary);
    }

    .savings-badge-pill {
        background: var(--sp-green-soft);
        border: 1px solid #bbf7d0;
        color: var(--sp-green);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }

    .trust-features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    .trust-feature-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        font-weight: 500;
        color: #64748b;
    }

    .trust-feature-item i {
        color: var(--sp-teal);
        font-size: 15px;
    }

    /* ── Modal Styling from My-Address ── */
    .addr-modal .modal-content {
        border-radius: 22px;
        border: none;
        box-shadow: 0 24px 60px rgba(7, 72, 74, 0.25);
        overflow: hidden;
    }

    .addr-modal .modal-header {
        background: linear-gradient(135deg, #07484A 0%, #0a5658 100%);
        color: #ffffff;
        padding: 18px 24px;
        border-bottom: none;
    }

    .addr-modal .modal-title {
        font-size: 17px;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .addr-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .addr-modal .modal-body {
        padding: 24px;
    }

    .addr-form-label {
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }

    .addr-form-control {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13.5px;
        color: #0f172a;
        transition: all 0.2s ease;
        width: 100%;
    }

    .addr-form-control:focus {
        border-color: #00B9B9;
        box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.15);
        outline: none;
    }

    /* Address Type Selector Pills in Modal */
    .addr-type-selector {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .addr-type-selector input[type="radio"] {
        display: none;
    }

    .addr-type-selector label {
        padding: 8px 18px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .addr-type-selector input[type="radio"]:checked + label {
        background: #07484A;
        color: #ffffff;
        border-color: #07484A;
        box-shadow: 0 4px 12px rgba(7, 72, 74, 0.2);
    }

    .addr-modal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #edf2f7;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn-addr-add {
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

    .btn-addr-add:hover {
        background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(245, 158, 11, 0.4);
        color: #032b2d !important;
    }
</style>

<!-- Checkout Progress Wizard -->
<div class="checkout-wizard-bar">
    <div class="container">
        <ul class="checkout-wizard-steps">
            <li>
                <a href="{{ route('cart') }}" class="wizard-step-item completed">
                    <span class="step-number"><i class="bi bi-check-lg"></i></span>
                    <span>1. Cart</span>
                </a>
            </li>
            <li class="wizard-step-divider"><i class="bi bi-chevron-right"></i></li>
            <li>
                <span class="wizard-step-item active">
                    <span class="step-number">2</span>
                    <span>2. Shipping Address</span>
                </span>
            </li>
            <li class="wizard-step-divider"><i class="bi bi-chevron-right"></i></li>
            <li>
                <span class="wizard-step-item">
                    <span class="step-number">3</span>
                    <span>3. Payment</span>
                </span>
            </li>
        </ul>
    </div>
</div>

<div class="shipping-page-wrap pt-4">
    <div class="container">

        <div class="row g-4">

            {{-- ══════════════════════════════════════════════════
                 LEFT COLUMN: SAVED ADDRESS SELECTION & ACTIONS
            ══════════════════════════════════════════════════ --}}
            <div class="col-lg-7">
                <div class="sp-main-card">

                    {{-- Section Header --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h3 class="sp-card-title">
                                <i class="bi bi-geo-alt-fill text-teal"></i> Select Delivery Address
                            </h3>
                            <p class="sp-card-subtitle mb-0">
                                Choose from your saved delivery addresses or add a new one.
                            </p>
                        </div>

                        <button type="button" class="btn-new-address-toggle" onclick="resetAndOpenModal()">
                            <i class="bi bi-plus-lg"></i> Add New Address
                        </button>
                    </div>

                    {{-- ── Saved Addresses Selection ── --}}
                    @if($savedAddresses->count() > 0)
                        <form action="{{ route('shipping.select') }}" method="POST" id="form-select-address">
                            @csrf

                            @php
                                $selectedId = $shippingData['address_id'] ?? ($savedAddresses->first()->id ?? null);
                            @endphp

                            <div class="saved-addr-list">
                                @foreach($savedAddresses as $index => $addr)
                                    @php
                                        $isObj = is_object($addr);
                                        $addrId   = $isObj ? $addr->id : ($addr['id'] ?? $index);
                                        $addrType = $isObj ? ($addr->address_type ?? 'Home') : ($addr['address_type'] ?? 'Home');
                                        $fullName = $isObj ? $addr->full_name : ($addr['full_name'] ?? '');
                                        $phone    = $isObj ? $addr->phone : ($addr['phone'] ?? '');
                                        $houseNo  = $isObj ? ($addr->address_line_1 ?? $addr->house_no) : ($addr['address_line_1'] ?? ($addr['house_no'] ?? ''));
                                        $roadArea = $isObj ? ($addr->address_line_2 ?? $addr->road_area) : ($addr['address_line_2'] ?? ($addr['road_area'] ?? ''));
                                        $city     = $isObj ? ($addr->city ?? '') : ($addr['city'] ?? '');
                                        $state    = $isObj ? ($addr->state ?? '') : ($addr['state'] ?? '');
                                        $pincode  = $isObj ? $addr->pincode : ($addr['pincode'] ?? '');
                                        $fullAddr = $isObj ? $addr->full_address : ($addr['full_address'] ?? ($houseNo . ', ' . $roadArea . ($city ? ', ' . $city : '') . ' - ' . $pincode));

                                        $typeClass = 'type-home';
                                        $typeIcon  = 'bi-house-door-fill';
                                        $normalizedType = strtolower($addrType);
                                        if ($normalizedType === 'office' || $normalizedType === 'work') {
                                            $typeClass = 'type-office';
                                            $typeIcon  = 'bi-building-fill';
                                        } elseif ($normalizedType === 'other') {
                                            $typeClass = 'type-other';
                                            $typeIcon  = 'bi-geo-alt-fill';
                                        }

                                        $isSelected = ($selectedId == $addrId) || ($index === 0 && !$selectedId);
                                    @endphp

                                    <div class="saved-addr-card {{ $isSelected ? 'active' : '' }}"
                                         data-address-id="{{ $addrId }}"
                                         onclick="selectAddressCard(this, '{{ $addrId }}')">

                                        <input type="radio" name="address_id" value="{{ $addrId }}" id="addr-radio-{{ $addrId }}" class="d-none" {{ $isSelected ? 'checked' : '' }}>

                                        <div class="addr-card-header">
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <span class="addr-radio-indicator">
                                                    <i class="bi bi-check-lg"></i>
                                                </span>
                                                <span class="addr-type-tag {{ $typeClass }}">
                                                    <i class="bi {{ $typeIcon }}"></i> {{ ucfirst($addrType) }}
                                                </span>
                                                @if(!empty($addr->is_default))
                                                    <span class="addr-default-tag">
                                                        <i class="bi bi-patch-check-fill"></i> Default
                                                    </span>
                                                @endif
                                                <span class="addr-user-name">{{ $fullName }}</span>
                                            </div>

                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn-card-action btn-edit-address" data-address="{{ json_encode($addr) }}">
                                                    <i class="bi bi-pencil-square"></i> <span>Edit</span>
                                                </button>
                                                <button type="button" class="btn-card-action btn-delete-action btn-delete-address" data-id="{{ $addrId }}">
                                                    <i class="bi bi-trash3"></i> <span>Delete</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="addr-card-body">
                                            <div class="addr-full-address">
                                                <i class="bi bi-geo-alt-fill"></i>
                                                <span>{{ $fullAddr }}</span>
                                            </div>
                                            <div class="addr-contact-inline">
                                                <span><i class="bi bi-telephone-fill"></i> +91 {{ $phone }}</span>
                                                @if(!empty($addr->email))
                                                    <span class="addr-contact-dot">•</span>
                                                    <span><i class="bi bi-envelope-fill"></i> {{ $addr->email }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="sp-btn-proceed mt-3" id="btn-submit-saved-address">
                                <span>Deliver to This Address</span>
                                <i class="bi bi-arrow-right fs-5"></i>
                            </button>
                        </form>
                    @else
                        {{-- Empty State --}}
                        <div class="addr-empty-state-box">
                            <div class="addr-empty-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">No Saved Address Found</h5>
                            <p class="text-muted small mb-4">Add your shipping address once to enjoy fast, one-click checkout.</p>
                            <button type="button" class="sp-btn-proceed mx-auto" style="max-width: 260px;" onclick="resetAndOpenModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Delivery Address
                            </button>
                        </div>
                    @endif

                    {{-- Hidden delete form --}}
                    <form id="form-delete-address" action="" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
                 RIGHT COLUMN: ORDER SUMMARY & SECURITY BADGES
            ══════════════════════════════════════════════════ --}}
            <div class="col-lg-5">
                <div class="sp-main-card order-summary-sticky">

                    @php
                        $itemSubtotal = ($cartData['frame_subtotal'] ?? 0) + ($cartData['lens_subtotal'] ?? 0);
                        $totalDiscounts = ($cartData['bogo_savings'] ?? 0) 
                                        + ($cartData['third_item_savings'] ?? 0) 
                                        + ($cartData['coupon_discount'] ?? 0) 
                                        + ($cartData['first_frame_free_save'] ?? 0);
                        $loyaltyDiscount = (float)($cartData['loyalty_discount'] ?? 0);
                        $totalSavings = $totalDiscounts + $loyaltyDiscount;
                        $shippingFee = (float)($cartData['shipping_charge'] ?? 0);
                        $grandTotal = (float)($cartData['grand_total'] ?? 0);
                        $itemsList = $cartData['items'] ?? [];
                    @endphp

                    {{-- Summary Header --}}
                    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3">
                        <h4 class="sp-card-title mb-0" style="font-size: 18px;">
                            <i class="bi bi-bag-check-fill text-teal"></i> Order Summary
                        </h4>
                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 11px;">
                            {{ count($itemsList) }} {{ Str::plural('Item', count($itemsList)) }}
                        </span>
                    </div>

                    {{-- Cart Items Mini-List --}}
                    @if(count($itemsList) > 0)
                        <div class="summary-item-preview-list">
                            @foreach($itemsList as $cartItem)
                                @php
                                    $imgUrl = $cartItem['frame_image'] ?? ($cartItem['image'] ?? asset('website/assets/img/bg/Sunglasses1.png'));
                                    $itemName = $cartItem['frame_name'] ?? ($cartItem['name'] ?? 'Eyewear Frame');
                                    $itemQty = (int)($cartItem['quantity'] ?? 1);
                                    $lensTitle = $cartItem['lens_name'] ?? ($cartItem['lens_package_name'] ?? 'Frame Only');
                                    $fPrice = (float)($cartItem['frame_price'] ?? 0);
                                    $lPrice = (float)($cartItem['lens_price'] ?? 0);
                                    $itemTotal = ($fPrice + $lPrice) * $itemQty;
                                @endphp
                                <div class="summary-item-row">
                                    <div class="summary-item-thumb-wrap">
                                        @if(!empty($cartItem['is_membership']))
                                            <div class="summary-membership-icon">
                                                <i class="bi bi-award-fill text-warning"></i>
                                            </div>
                                        @else
                                            <img src="{{ $imgUrl }}" alt="{{ $itemName }}" class="summary-item-thumb" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Sunglasses1.png') }}';">
                                        @endif
                                    </div>
                                    <div class="summary-item-info">
                                        <div class="summary-item-name" title="{{ $itemName }}">{{ $itemName }}</div>
                                        <div class="summary-item-sub">Qty: {{ $itemQty }} · {{ $lensTitle }}</div>
                                    </div>
                                    <div class="summary-item-price">
                                        ₹{{ number_format($itemTotal, 0) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Price Breakdown Table --}}
                    <table class="bill-table">
                        <tbody>
                            <tr>
                                <td>Items Subtotal</td>
                                <td class="text-end fw-semibold text-dark">₹{{ number_format($itemSubtotal, 2) }}</td>
                            </tr>

                            @if($totalDiscounts > 0)
                                <tr>
                                    <td>Special Offers & Coupons</td>
                                    <td class="text-end fw-semibold text-success">- ₹{{ number_format($totalDiscounts, 2) }}</td>
                                </tr>
                            @endif

                            @if($loyaltyDiscount > 0)
                                <tr>
                                    <td>Loyalty Points Redeemed</td>
                                    <td class="text-end fw-semibold text-success">- ₹{{ number_format($loyaltyDiscount, 2) }}</td>
                                </tr>
                            @endif

                            @if($shippingFee > 0)
                                <tr>
                                    <td>Shipping Charges</td>
                                    <td class="text-end fw-semibold text-dark">+ ₹{{ number_format($shippingFee, 2) }}</td>
                                </tr>
                            @endif

                            <tr class="bill-total-row">
                                <td>Total Payable</td>
                                <td class="text-end">₹{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Savings Banner --}}
                    @if($totalSavings > 0)
                        <div class="savings-badge-pill">
                            <i class="bi bi-tag-fill"></i>
                            <span>You are saving ₹{{ number_format($totalSavings, 0) }} on this order!</span>
                        </div>
                    @endif

                    {{-- Trust Badges --}}
                    <div class="trust-features-grid">
                        <div class="trust-feature-item">
                            <i class="bi bi-shield-check"></i>
                            <span>100% Secure Checkout</span>
                        </div>
                        <div class="trust-feature-item">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>14-Day Free Returns</span>
                        </div>
                        <div class="trust-feature-item">
                            <i class="bi bi-patch-check"></i>
                            <span>1-Year Warranty</span>
                        </div>
                        <div class="trust-feature-item">
                            <i class="bi bi-box-seam"></i>
                            <span>Safe Packaging</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- ==========================================================================
     UNIFIED MODAL: ADD / EDIT DELIVERY ADDRESS (Identical to My Address UI)
     ========================================================================== -->
<div class="modal fade addr-modal" id="addressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-geo-alt-fill text-warning"></i> Add New Address
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addressForm" method="POST" action="{{ route('shipping.save') }}">
                @csrf
                <input type="hidden" id="modal_address_id" name="address_id">

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Recipient Names -->
                        <div class="col-md-6">
                            <label class="addr-form-label">First Name *</label>
                            <input type="text" class="form-control addr-form-control" id="modal_first_name" name="first_name" placeholder="e.g. Rahul" value="{{ old('first_name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="addr-form-label">Last Name</label>
                            <input type="text" class="form-control addr-form-control" id="modal_last_name" name="last_name" placeholder="e.g. Sharma" value="{{ old('last_name') }}">
                        </div>

                        <!-- Mobile Number & Country -->
                        <div class="col-md-6">
                            <label class="addr-form-label">Mobile Number *</label>
                            <input type="tel" class="form-control addr-form-control" id="modal_phone" name="phone" placeholder="10-digit mobile number" maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('phone') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="addr-form-label">Country</label>
                            <input type="text" class="form-control addr-form-control" name="country" value="India" readonly style="background:#f8fafc;">
                        </div>

                        <!-- Address Line 1 -->
                        <div class="col-12">
                            <label class="addr-form-label">Flat, House No., Building, Apartment *</label>
                            <input type="text" class="form-control addr-form-control" id="modal_address_line_1" name="address_line_1" placeholder="e.g. Flat 402, Sunshine Heights" value="{{ old('address_line_1') }}" required>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="col-12">
                            <label class="addr-form-label">Area, Street, Sector, Village (Optional)</label>
                            <input type="text" class="form-control addr-form-control" id="modal_address_line_2" name="address_line_2" placeholder="e.g. Near City Mall, MG Road" value="{{ old('address_line_2') }}">
                        </div>

                        <!-- City, State & Pincode -->
                        <div class="col-md-4">
                            <label class="addr-form-label">City / Town *</label>
                            <input type="text" class="form-control addr-form-control" id="modal_city" name="city" placeholder="e.g. Mumbai" value="{{ old('city') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="addr-form-label">State *</label>
                            <input type="text" class="form-control addr-form-control" id="modal_state" name="state" placeholder="e.g. Maharashtra" value="{{ old('state') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="addr-form-label">Pincode *</label>
                            <input type="text" class="form-control addr-form-control" id="modal_pincode" name="pincode" placeholder="6 digits" maxlength="6" pattern="[0-9]{6}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('pincode') }}" required>
                        </div>

                        <!-- Landmark & Email -->
                        <div class="col-md-6">
                            <label class="addr-form-label">Landmark (Optional)</label>
                            <input type="text" class="form-control addr-form-control" id="modal_landmark" name="landmark" placeholder="e.g. Opposite Metro Station" value="{{ old('landmark') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="addr-form-label">Email Address (Optional)</label>
                            <input type="email" class="form-control addr-form-control" id="modal_email" name="email" placeholder="e.g. name@example.com" value="{{ old('email') }}">
                        </div>

                        <!-- Address Type Selector -->
                        <div class="col-12 mt-3">
                            <label class="addr-form-label">Address Type</label>
                            <div class="addr-type-selector">
                                <input type="radio" name="type" value="home" id="modal_type_home" checked>
                                <label for="modal_type_home">
                                    <i class="bi bi-house-door-fill"></i> Home
                                </label>

                                <input type="radio" name="type" value="office" id="modal_type_office">
                                <label for="modal_type_office">
                                    <i class="bi bi-building-fill"></i> Office / Commercial
                                </label>

                                <input type="radio" name="type" value="other" id="modal_type_other">
                                <label for="modal_type_other">
                                    <i class="bi bi-geo-alt-fill"></i> Other
                                </label>
                            </div>
                        </div>

                        <!-- Default Checkbox -->
                        <div class="col-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="modal_is_default" name="is_default" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark small" for="modal_is_default">
                                    Set as default shipping address
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-3 px-3 fw-semibold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-addr-add" id="modalSubmitBtn">
                        <i class="bi bi-check2-circle"></i> Save Address
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
    function selectAddressCard(element, addressId) {
        $('.saved-addr-card').removeClass('active');
        $(element).addClass('active');
        $(element).find('input[type="radio"]').prop('checked', true);
    }

    function resetAndOpenModal() {
        let form = document.getElementById('addressForm');
        form.reset();
        form.action = "{{ route('shipping.save') }}";

        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-geo-alt-fill text-warning"></i> Add New Address';
        document.getElementById('modalSubmitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Save Address';
        document.getElementById('modal_address_id').value = '';
        document.getElementById('modal_type_home').checked = true;
        document.getElementById('modal_is_default').checked = true;
        let modal = new bootstrap.Modal(document.getElementById('addressModal'));
        modal.show();
    }

    function openEditModal(address) {
        if (!address) return;

        let form = document.getElementById('addressForm');
        let updateUrl = "{{ route('update_address', ':id') }}";
        form.action = updateUrl.replace(':id', address.id);

        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square text-warning"></i> Edit Address';
        document.getElementById('modalSubmitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Update Address';
        document.getElementById('modal_address_id').value = address.id;

        document.getElementById('modal_first_name').value = address.first_name || (address.full_name ? address.full_name.split(' ')[0] : '');
        document.getElementById('modal_last_name').value = address.last_name || (address.full_name && address.full_name.split(' ').length > 1 ? address.full_name.split(' ').slice(1).join(' ') : '');
        document.getElementById('modal_phone').value = address.phone || '';
        document.getElementById('modal_email').value = address.email || '';
        document.getElementById('modal_address_line_1').value = address.address_line_1 || address.house_no || '';
        document.getElementById('modal_address_line_2').value = address.address_line_2 || address.road_area || '';
        document.getElementById('modal_city').value = address.city || '';
        document.getElementById('modal_state').value = address.state || '';
        document.getElementById('modal_pincode').value = address.pincode || '';
        document.getElementById('modal_landmark').value = address.landmark || '';

        let type = (address.address_type || address.type || 'home').toLowerCase();
        if (type === 'work' || type === 'office') {
            document.getElementById('modal_type_office').checked = true;
        } else if (type === 'other') {
            document.getElementById('modal_type_other').checked = true;
        } else {
            document.getElementById('modal_type_home').checked = true;
        }

        document.getElementById('modal_is_default').checked = (address.is_default == 1 || address.is_default === true);

        let modal = new bootstrap.Modal(document.getElementById('addressModal'));
        modal.show();
    }

    $(document).on('click', '.btn-edit-address', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var addressData = $(this).data('address');
        if (typeof addressData === 'string') {
            try { addressData = JSON.parse(addressData); } catch(err) {}
        }
        openEditModal(addressData);
    });

    $(document).on('click', '.btn-delete-address', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var addressId = $(this).data('id');

        Swal.fire({
            title: 'Delete Address?',
            text: 'Are you sure you want to delete this saved address?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var deleteForm = document.getElementById('form-delete-address');
                let deleteUrl = "{{ route('shipping.address.delete', ':id') }}";
                deleteForm.action = deleteUrl.replace(':id', addressId);
                deleteForm.submit();
            }
        });
    });
</script>

@if ($errors->any())
<script>
document.addEventListener("DOMContentLoaded", function() {
    @foreach ($errors->all() as $error)
        toastr.error("{{ $error }}");
    @endforeach

    var myModal = new bootstrap.Modal(document.getElementById("addressModal"));
    @if (old('address_id'))
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square text-warning"></i> Edit Address';
        document.getElementById('modalSubmitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Update Address';
        let updateUrl = "{{ route('update_address', ':id') }}";
        document.getElementById('addressForm').action = updateUrl.replace(':id', "{{ old('address_id') }}");
        document.getElementById('modal_address_id').value = "{{ old('address_id') }}";
    @else
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-geo-alt-fill text-warning"></i> Add New Address';
        document.getElementById('modalSubmitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Save Address';
        document.getElementById('addressForm').action = "{{ route('shipping.save') }}";
    @endif
    myModal.show();
});
</script>
@endif
@endsection
