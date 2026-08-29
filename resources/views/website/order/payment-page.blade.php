@extends('website.layout.master')
@section('content')

{{-- ═══════════════════════════════════════════════════
     SPECKART — Modern Premium Payment Page UI
     Consistent with Shipping Details & Cart Flow
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

    .payment-page-wrap {
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

    /* ── Delivery Address Summary Card (Clean & Uncrowded) ── */
    .delivery-summary-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .delivery-summary-left {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex: 1;
        min-width: 0;
    }

    .delivery-pin-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f0fdfc;
        color: var(--sp-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .delivery-summary-info {
        flex: 1;
        min-width: 0;
    }

    .delivery-recipient-line {
        font-size: 14.5px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .addr-type-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
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

    .delivery-addr-text {
        font-size: 13px;
        color: #475569;
        line-height: 1.45;
        margin-bottom: 3px;
    }

    .delivery-phone-text {
        font-size: 12px;
        color: #64748b;
    }

    .btn-change-addr {
        font-size: 12px;
        font-weight: 600;
        color: var(--sp-primary);
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 6px 14px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-change-addr:hover {
        background: var(--sp-primary);
        color: #ffffff;
        border-color: var(--sp-primary);
    }

    /* ── Payment Options Cards (Clean & Uncrowded) ── */
    .pay-options-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }

    .pay-option-card {
        background: #ffffff;
        border: 1.5px solid var(--sp-border);
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        overflow: hidden;
        position: relative;
    }

    .pay-option-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .pay-option-card:hover {
        border-color: #00B9B9;
        box-shadow: 0 4px 14px rgba(7, 72, 74, 0.06);
    }

    .pay-option-card.is-selected {
        border: 2px solid var(--sp-primary);
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(7, 72, 74, 0.08);
    }

    .pay-option-head {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
    }

    .pay-radio-indicator {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background: #ffffff;
        flex-shrink: 0;
    }

    .pay-option-card.is-selected .pay-radio-indicator {
        border-color: var(--sp-primary);
        background: var(--sp-primary);
        box-shadow: 0 0 0 2px rgba(7, 72, 74, 0.15);
    }

    .pay-radio-indicator i {
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.2s ease;
    }

    .pay-option-card.is-selected .pay-radio-indicator i {
        opacity: 1;
        transform: scale(1);
    }

    .pay-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #f0fdfc;
        color: var(--sp-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .pay-option-card.is-selected .pay-icon-box {
        background: var(--sp-primary);
        color: #ffffff;
    }

    .pay-meta {
        flex: 1;
        min-width: 0;
    }

    .pay-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--sp-text);
        margin-bottom: 2px;
    }

    .pay-subtitle {
        font-size: 12.5px;
        color: var(--sp-text-muted);
    }

    .pay-recommended-pill {
        font-size: 11px;
        font-weight: 600;
        color: #059669;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .pay-option-body {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        padding: 0 18px;
        transition: all 0.2s ease;
        border-top: 1px solid transparent;
        background: #f8fafc;
    }

    .pay-option-card.is-selected .pay-option-body {
        max-height: 140px;
        opacity: 1;
        padding: 12px 18px;
        border-top-color: #f1f5f9;
    }

    .pay-secure-note {
        font-size: 12.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── Delivery Instructions (Clean Modern Card) ── */
    .delivery-instructions-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
    }

    .inst-label {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .inst-optional-tag {
        font-size: 10.5px;
        font-weight: 600;
        color: #94a3b8;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 1px 7px;
        border-radius: 6px;
    }

    .inst-quick-chips {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .inst-chip {
        font-size: 11.5px;
        font-weight: 500;
        color: #475569;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.18s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .inst-chip:hover {
        background: #f0fdfc;
        color: var(--sp-primary);
        border-color: #ccfbf1;
    }

    .inst-textarea {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        font-size: 13px;
        color: #0f172a;
        padding: 10px 12px;
        resize: none;
        transition: all 0.2s ease;
        box-shadow: none;
    }

    .inst-textarea:focus {
        border-color: var(--sp-primary);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(7, 72, 74, 0.08);
        outline: none;
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
                <a href="{{ route('shipping-details') }}" class="wizard-step-item completed">
                    <span class="step-number"><i class="bi bi-check-lg"></i></span>
                    <span>2. Shipping Address</span>
                </a>
            </li>
            <li class="wizard-step-divider"><i class="bi bi-chevron-right"></i></li>
            <li>
                <span class="wizard-step-item active">
                    <span class="step-number">3</span>
                    <span>3. Payment</span>
                </span>
            </li>
        </ul>
    </div>
</div>

<div class="payment-page-wrap pt-4">
    <div class="container">

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

        <div class="row g-4">

            {{-- ══════════════════════════════════════════════════
                 LEFT COLUMN: DELIVERY PREVIEW & PAYMENT METHODS
            ══════════════════════════════════════════════════ --}}
            <div class="col-lg-7">
                <div class="sp-main-card">

                    {{-- Section Header --}}
                    <div class="mb-3">
                        <h3 class="sp-card-title">
                            <i class="bi bi-credit-card-2-front-fill text-teal"></i> Select Payment Method
                        </h3>
                        <p class="sp-card-subtitle mb-0">
                            Choose your preferred secure payment method to place your order.
                        </p>
                    </div>

                    {{-- ── Delivery Location Preview ── --}}
                    @if(!empty($shippingData))
                        @php
                            $addrType = $shippingData['address_type'] ?? 'Home';
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
                        @endphp
                        <div class="delivery-summary-card">
                            <div class="delivery-summary-left">
                                <div class="delivery-pin-icon">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div class="delivery-summary-info">
                                    <div class="delivery-recipient-line">
                                        <span>Deliver to: <strong>{{ $shippingData['full_name'] ?? 'Customer' }}</strong></span>
                                        <span class="addr-type-tag {{ $typeClass }}">
                                            <i class="bi {{ $typeIcon }}"></i> {{ ucfirst($addrType) }}
                                        </span>
                                    </div>
                                    <div class="delivery-addr-text">
                                        {{ $shippingData['full_address'] ?? (($shippingData['address_line_1'] ?? $shippingData['house_no'] ?? '') . ', ' . ($shippingData['address_line_2'] ?? $shippingData['road_area'] ?? '') . ' - ' . ($shippingData['pincode'] ?? '')) }}
                                    </div>
                                    <div class="delivery-phone-text">
                                        <i class="bi bi-telephone text-teal me-1"></i> {{ $shippingData['phone'] ?? '' }}
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('shipping-details') }}" class="btn-change-addr">
                                <i class="bi bi-pencil-square me-1"></i> Change
                            </a>
                        </div>
                    @endif

                    {{-- ── Payment Form ── --}}
                    <form action="{{ route('checkout.complete') }}" method="POST" id="checkoutPaymentForm">
                        @csrf

                        <div class="pay-options-group" id="paymentOptions">

                            {{-- Option 1: Pay Online --}}
                            <label class="pay-option-card is-selected" for="pay_online">
                                <input type="radio" name="payment_method" id="pay_online" value="online" checked>
                                <div class="pay-option-head">
                                    <span class="pay-radio-indicator">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                    <div class="pay-icon-box">
                                        <i class="bi bi-qr-code-scan"></i>
                                    </div>
                                    <div class="pay-meta">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                            <div class="pay-title">Pay Online</div>
                                            <span class="pay-recommended-pill">Recommended</span>
                                        </div>
                                        <div class="pay-subtitle">UPI, GooglePay, PhonePe, Cards, Net Banking</div>
                                    </div>
                                </div>
                                <div class="pay-option-body">
                                    <div class="pay-secure-note">
                                        <i class="bi bi-shield-lock-fill text-teal fs-5"></i>
                                        <span>Fast, 100% encrypted & secure payment with instant order confirmation.</span>
                                    </div>
                                </div>
                            </label>

                            {{-- Option 2: Cash on Delivery (COD) --}}
                            @php
                                $isCodAvailable = (bool)($cartData['is_cod_available'] ?? true);
                            @endphp
                            @if($isCodAvailable)
                                <label class="pay-option-card" for="pay_cod">
                                    <input type="radio" name="payment_method" id="pay_cod" value="cod">
                                    <div class="pay-option-head">
                                        <span class="pay-radio-indicator">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                        <div class="pay-icon-box">
                                            <i class="bi bi-cash-stack"></i>
                                        </div>
                                        <div class="pay-meta">
                                            <div class="pay-title">Cash on Delivery (COD)</div>
                                            <div class="pay-subtitle">Pay in cash upon doorstep delivery</div>
                                        </div>
                                    </div>
                                    <div class="pay-option-body">
                                        <div class="form-check my-1">
                                            <input class="form-check-input" type="checkbox" id="codConfirm" name="cod_confirm">
                                            <label class="form-check-label text-dark fw-semibold small" for="codConfirm">
                                                I agree to pay <strong class="text-teal" style="color: #07484a;">₹{{ number_format($grandTotal, 0) }}</strong> in cash at the time of delivery.
                                            </label>
                                        </div>
                                    </div>
                                </label>
                            @else
                                <div class="p-3 mb-2 rounded-3 border bg-light text-muted d-flex align-items-center gap-2" style="font-size: 13px;">
                                    <i class="bi bi-info-circle text-secondary fs-5"></i>
                                    <div>
                                        <strong class="d-block text-dark">Cash on Delivery Unavailable</strong>
                                        COD is not available for this pincode. Please complete payment online.
                                    </div>
                                </div>
                            @endif

                        </div>

                        {{-- Modern Delivery Instructions Card --}}
                        <div class="delivery-instructions-card mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="inst-label mb-0" for="customerNote">
                                    <i class="bi bi-chat-square-quote-fill text-teal"></i>
                                    <span>Delivery Instructions</span>
                                    <span class="inst-optional-tag">Optional</span>
                                </label>
                            </div>

                            {{-- Quick-pick Suggestion Chips --}}
                            <div class="inst-quick-chips mb-2">
                                <button type="button" class="inst-chip" onclick="addInstruction('Call before delivery')">
                                    <i class="bi bi-telephone"></i> Call before delivery
                                </button>
                                <button type="button" class="inst-chip" onclick="addInstruction('Leave with security')">
                                    <i class="bi bi-shield-check"></i> Leave with security
                                </button>
                                <button type="button" class="inst-chip" onclick="addInstruction('Do not ring bell')">
                                    <i class="bi bi-bell-slash"></i> Do not ring bell
                                </button>
                            </div>

                            <textarea class="form-control inst-textarea" id="customerNote" name="customer_note" rows="2" placeholder="Add specific delivery notes (e.g. Landmark, gate number, preferred call time)..."></textarea>
                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-4">
                            <button type="submit" class="sp-btn-proceed" id="btnPlaceOrder">
                                <span>Place Order &bull; ₹{{ number_format($grandTotal, 0) }}</span>
                                <i class="bi bi-arrow-right fs-5"></i>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
                 RIGHT COLUMN: ORDER SUMMARY & SECURITY BADGES
            ══════════════════════════════════════════════════ --}}
            <div class="col-lg-5">
                <div class="sp-main-card order-summary-sticky">

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

                    {{-- Loyalty Cashback / Points Perk --}}
                    @if(!empty($cartData['order_reward_pts']) && $cartData['order_reward_pts'] > 0)
                        <div class="p-3 mb-3 rounded-3 d-flex align-items-center gap-2" style="background: #fefce8; border: 1px dashed #facc15; font-size: 12.5px; color: #854d0e;">
                            <i class="bi bi-gift-fill fs-5 text-warning"></i>
                            <div>
                                You will earn <strong>{{ $cartData['order_reward_pts'] }} Loyalty Points</strong> on delivery of this order!
                            </div>
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

<script>
    function addInstruction(text) {
        var noteField = document.getElementById('customerNote');
        if (!noteField) return;
        if (noteField.value.trim() === '') {
            noteField.value = text;
        } else if (!noteField.value.includes(text)) {
            noteField.value += ', ' + text;
        }
        noteField.focus();
    }

    document.addEventListener('DOMContentLoaded', function(){
        const radios = document.querySelectorAll('input[name="payment_method"]');
        const form   = document.getElementById('checkoutPaymentForm');
        const btn    = document.getElementById('btnPlaceOrder');
        const codConfirm = document.getElementById('codConfirm');

        function syncSelectedState(){
            radios.forEach((r) => {
                r.closest('.pay-option-card').classList.toggle('is-selected', r.checked);
            });
        }

        radios.forEach((r) => {
            r.addEventListener('change', function(){
                syncSelectedState();
            });
        });

        syncSelectedState();

        if (form) {
            form.addEventListener('submit', function(e){
                const selectedRadio = document.querySelector('input[name="payment_method"]:checked');
                const method = selectedRadio ? selectedRadio.value : 'online';

                if (method === 'cod') {
                    if (codConfirm && !codConfirm.checked) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Confirmation Required',
                            text: 'Please check the agreement box to confirm Cash on Delivery.',
                            confirmButtonColor: '#07484a'
                        }).then(() => {
                            codConfirm.focus();
                        });
                        return false;
                    }
                }

                // Prevent duplicate submission
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing Order...';
            });
        }
    });
</script>

{{-- Prevent browser back-forward cache from showing stale payment page after checkout --}}
<script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>

@endsection