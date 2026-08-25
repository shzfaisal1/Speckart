@extends('website.layout.master')
@section('content')

<!-- Breadcrumbs Section -->
<section class="breadcrumbs-section py-3 bg-white border-bottom">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul id="breadcrumbs" class="m-0 p-0 list-unstyled d-flex align-items-center gap-2" style="font-size: 13px;">
                    <li><a href="{{ route('cart') }}" class="text-muted text-decoration-none">Cart</a></li>
                    <li><i class="bi bi-chevron-right text-muted" style="font-size: 10px;"></i></li>
                    <li><a href="{{ route('shipping-details') }}" class="text-muted text-decoration-none">Shipping Address</a></li>
                    <li><i class="bi bi-chevron-right text-muted" style="font-size: 10px;"></i></li>
                    <li class="fw-bold text-dark">Payment</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Payment Section -->
<section class="payment-page-sec py-5" style="background: #f7fafb; min-height: 75vh;">
    <div class="container">

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" style="border-radius: 14px; font-size: 14px;" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $itemSubtotal = ($cartData['frame_subtotal'] ?? 0) + ($cartData['lens_subtotal'] ?? 0);
            $totalDiscounts = ($cartData['bogo_savings'] ?? 0) 
                            + ($cartData['third_item_savings'] ?? 0) 
                            + ($cartData['coupon_discount'] ?? 0) 
                            + ($cartData['first_frame_free_save'] ?? 0);
            $loyaltyDiscount = (float)($cartData['loyalty_discount'] ?? 0);
            $totalSavings = $totalDiscounts + $loyaltyDiscount;
            $grandTotal = (float)($cartData['grand_total'] ?? 0);
        @endphp

        <div class="row g-4">

            <!-- Left: Payment Methods & Shipping Summary -->
            <div class="col-lg-7">
                <div class="payment-card bg-white p-4 p-md-4 rounded-4 border shadow-sm">

                    {{-- Address Preview Bar --}}
                    @if(!empty($shippingData))
                        <div class="delivery-address-preview p-3 mb-4 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #f0fdfc; border-color: #ccfbf1 !important;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-geo-alt-fill text-teal fs-5 mt-1" style="color: #07484A;"></i>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-dark" style="font-size: 14px;">Delivering to: {{ $shippingData['full_name'] ?? 'Customer' }}</span>
                                        <span class="badge bg-white text-teal border px-2 py-1" style="color: #07484A; font-size: 11px;">{{ $shippingData['address_type'] ?? 'Home' }}</span>
                                    </div>
                                    <p class="text-muted small mb-0 mt-1" style="line-height: 1.4;">
                                        {{ $shippingData['full_address'] ?? ($shippingData['house_no'] . ', ' . $shippingData['road_area'] . ' - ' . $shippingData['pincode']) }}
                                    </p>
                                    <span class="text-muted small"><i class="bi bi-telephone me-1"></i> {{ $shippingData['phone'] ?? '' }}</span>
                                </div>
                            </div>
                            <a href="{{ route('shipping-details') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 12px; font-weight: 600;">
                                Change
                            </a>
                        </div>
                    @endif

                    <h4 class="pay-options-heading mb-3" style="font-size: 18px; font-weight: 700; color: #07484A;">
                        Choose Payment Method
                    </h4>

                    <!-- Payment Form -->
                    <form action="{{ route('checkout.complete') }}" method="POST" id="checkoutPaymentForm">
                        @csrf

                        <!-- Payment Options -->
                        <div class="pay-options" id="paymentOptions">

                            {{-- Option 1: Pay Online --}}
                            <label class="pay-option" for="pay_online">
                                <input type="radio" name="payment_method" id="pay_online" value="online" checked>
                                <div class="pay-option-row">
                                    <span class="pay-option-radio"></span>
                                    <span class="pay-option-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="5" width="20" height="14" rx="2.5"/>
                                            <path d="M2 10h20"/>
                                        </svg>
                                    </span>
                                    <span class="pay-option-text">
                                        <span class="pay-option-title">Pay Online</span>
                                        <span class="pay-option-sub">UPI, Cards, Net Banking, Wallets</span>
                                    </span>
                                    <span class="pay-option-badge">Recommended</span>
                                </div>
                                <div class="pay-option-detail">
                                    <p class="mb-2">Fast, 100% secure payment gateway with instant order confirmation.</p>
                                    <div class="pay-option-marks mb-3">
                                        <span><i class="bi bi-qr-code me-1"></i> UPI / QR</span>
                                        <span><i class="bi bi-credit-card-2-front me-1"></i> Cards</span>
                                        <span><i class="bi bi-bank me-1"></i> Net Banking</span>
                                        <span><i class="bi bi-wallet2 me-1"></i> Wallets</span>
                                    </div>
                                </div>
                            </label>

                            {{-- Option 2: Cash on Delivery --}}
                            @php
                                $isCodAvailable = (bool)($cartData['is_cod_available'] ?? true);
                            @endphp
                            @if($isCodAvailable)
                                <label class="pay-option" for="pay_cod">
                                    <input type="radio" name="payment_method" id="pay_cod" value="cod">
                                    <div class="pay-option-row">
                                        <span class="pay-option-radio"></span>
                                        <span class="pay-option-icon">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H7"/>
                                            </svg>
                                        </span>
                                        <span class="pay-option-text">
                                            <span class="pay-option-title">Cash on Delivery (COD)</span>
                                            <span class="pay-option-sub">Pay in cash when your order arrives</span>
                                        </span>
                                    </div>
                                    <div class="pay-option-detail">
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="checkbox" id="codConfirm" name="cod_confirm">
                                            <label class="form-check-label text-dark fw-medium" for="codConfirm" style="font-size: 13.5px;">
                                                I agree to pay <strong class="text-teal" style="color: #07484A;">₹{{ number_format($grandTotal, 0) }}</strong> in cash at the time of delivery.
                                            </label>
                                        </div>
                                    </div>
                                </label>
                            @else
                                <div class="p-3 mb-2 rounded-3 border bg-light text-muted d-flex align-items-center gap-2" style="font-size: 13px; opacity: 0.85;">
                                    <i class="bi bi-info-circle text-secondary fs-5"></i>
                                    <div>
                                        <strong class="d-block text-dark">Cash on Delivery (COD) Unavailable</strong>
                                        COD is not available for delivery pincode <strong>{{ $shippingData['pincode'] ?? '' }}</strong>. Please complete payment online.
                                    </div>
                                </div>
                            @endif

                        </div>

                        {{-- Customer Delivery Note / Instruction --}}
                        <div class="mt-4 pt-2">
                            <label class="form-label text-secondary small fw-semibold mb-1" for="customerNote">
                                <i class="bi bi-chat-left-text me-1"></i> Delivery Instructions / Note (Optional)
                            </label>
                            <textarea class="form-control rounded-3" id="customerNote" name="customer_note" rows="2" placeholder="e.g. Call before delivery / Leave at security gate" style="font-size: 13.5px;"></textarea>
                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-4">
                            <button type="submit" class="btn btn-checkout-submit w-100 py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" id="btnPlaceOrder">
                                <span class="btn-text">
                                    Place Order &bull; ₹{{ number_format($grandTotal, 0) }}
                                </span>
                                <i class="bi bi-arrow-right fs-5"></i>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <!-- Right: Dynamic Order Summary -->
            <div class="col-lg-5">
                <div class="order-summary-card bg-white p-4 rounded-4 border shadow-sm">

                    <div class="order-summary-img mb-3 text-center d-none d-lg-block">
                        <img src="{{ asset('website/assets/img/bg/Add-Shipping-details-bg.png') }}" alt="Speckarts Secure Checkout" class="img-fluid" style="max-height: 110px; object-fit: contain;">
                    </div>

                    <h4 class="summary-title pb-2 mb-3 border-bottom fw-bold" style="font-size: 18px; color: #07484A;">
                        Order Summary
                    </h4>

                    {{-- Items Count Badge --}}
                    <div class="d-flex align-items-center justify-content-between text-muted mb-3 small">
                        <span>Total Items in Cart</span>
                        <span class="fw-semibold text-dark">{{ count($cartData['items'] ?? []) }} item(s)</span>
                    </div>

                    <div class="order-summary">
                        {{-- Total Item Price --}}
                        <div class="order-summary-row">
                            <span class="order-summary-label">Total Item Price</span>
                            <span class="order-summary-value fw-semibold">₹{{ number_format($itemSubtotal, 0) }}</span>
                        </div>

                        {{-- Total Discounts (BOGO, Coupon, Promotions) --}}
                        @if($totalDiscounts > 0)
                            <div class="order-summary-row">
                                <span class="order-summary-label">Total Discount</span>
                                <span class="order-summary-value order-summary-discount fw-semibold text-success">
                                    &#8722; ₹{{ number_format($totalDiscounts, 0) }}
                                </span>
                            </div>
                        @endif

                        {{-- Loyalty Points Redeemed --}}
                        @if($loyaltyDiscount > 0)
                            <div class="order-summary-row">
                                <span class="order-summary-label d-flex align-items-center gap-1">
                                    <i class="bi bi-coin text-warning"></i> Loyalty Points Used
                                </span>
                                <span class="order-summary-value order-summary-discount fw-semibold text-success">
                                    &#8722; ₹{{ number_format($loyaltyDiscount, 0) }}
                                </span>
                            </div>
                        @endif

                        {{-- Shipping Fee --}}
                        <div class="order-summary-row">
                            <span class="order-summary-label">Shipping Charges</span>
                            <span class="order-summary-value {{ ($cartData['shipping_charge'] ?? 0) > 0 ? 'fw-bold text-dark' : 'text-success fw-semibold' }}">
                                @if(($cartData['shipping_charge'] ?? 0) > 0)
                                    + ₹{{ number_format($cartData['shipping_charge'], 2) }}
                                @else
                                    FREE
                                @endif
                            </span>
                        </div>

                        {{-- Total Payable --}}
                        <div class="order-summary-row order-summary-total pt-3 mt-2 border-top">
                            <span class="order-summary-label fw-bold fs-5 text-dark">Total Payable</span>
                            <span class="order-summary-value fw-bold fs-5" style="color: #07484A;">
                                ₹{{ number_format($grandTotal, 0) }}
                            </span>
                        </div>
                    </div>

                    {{-- Total Savings Highlight --}}
                    @if($totalSavings > 0)
                        <div class="alert alert-success mt-3 py-2 px-3 mb-3 d-flex align-items-center gap-2 border-0" style="background: #e6f9f0; color: #1f8a70; font-size: 13px; border-radius: 10px;">
                            <i class="bi bi-stars fs-5"></i>
                            <div>
                                You are saving <strong>₹{{ number_format($totalSavings, 0) }}</strong> on this order!
                            </div>
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

                    {{-- 100% Secure Checkout Badge --}}
                    <div class="secure-box mt-3 pt-3 border-top d-flex align-items-center justify-content-center gap-2 text-muted small">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span class="fw-semibold">100% Safe & Secure Payment</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<style>
    :root {
        --ink: #12201f;
        --teal: #07484A;
        --teal-deep: #053335;
        --teal-tint: #eef6f5;
        --line: #e2e8f0;
        --muted: #64748b;
        --success: #16a34a;
        --radius: 16px;
    }

    .payment-card, .order-summary-card {
        border-radius: var(--radius);
        border: 1px solid var(--line);
    }

    .pay-options {
        margin-top: 10px;
    }

    .pay-option {
        display: block;
        border: 2px solid var(--line);
        border-radius: var(--radius);
        margin-bottom: 14px;
        cursor: pointer;
        background: #fff;
        transition: all 0.2s ease;
        overflow: hidden;
        position: relative;
    }

    .pay-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .pay-option-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
    }

    .pay-option-radio {
        flex: 0 0 auto;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        position: relative;
        transition: all 0.2s ease;
    }

    .pay-option-radio::after {
        content: "";
        position: absolute;
        inset: 3px;
        border-radius: 50%;
        background: var(--teal);
        transform: scale(0);
        transition: transform 0.2s ease;
    }

    .pay-option-icon {
        flex: 0 0 auto;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--teal-tint);
        color: var(--teal);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .pay-option-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1 1 auto;
        min-width: 0;
    }

    .pay-option-title {
        font-size: 15.5px;
        font-weight: 700;
        color: var(--ink);
    }

    .pay-option-sub {
        font-size: 13px;
        color: var(--muted);
    }

    .pay-option-badge {
        flex: 0 0 auto;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--teal);
        background: var(--teal-tint);
        border: 1px solid #ccfbf1;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .pay-option-detail {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        padding: 0 20px;
        transition: all 0.25s ease;
        border-top: 1px solid transparent;
        background: #fafcfc;
    }

    .pay-option-detail p {
        font-size: 13px;
        color: var(--muted);
        margin: 12px 0 8px;
    }

    .pay-option-marks {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-bottom: 12px;
    }

    .pay-option-marks span {
        font-size: 12px;
        font-weight: 500;
        color: var(--muted);
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 6px;
        padding: 4px 10px;
    }

    .pay-option.is-selected {
        border-color: var(--teal);
        background: #fff;
        box-shadow: 0 6px 20px rgba(7, 72, 74, 0.08);
    }

    .pay-option.is-selected .pay-option-radio {
        border-color: var(--teal);
    }

    .pay-option.is-selected .pay-option-radio::after {
        transform: scale(1);
    }

    .pay-option.is-selected .pay-option-icon {
        background: var(--teal);
        color: #fff;
    }

    .pay-option.is-selected .pay-option-detail {
        max-height: 200px;
        opacity: 1;
        padding: 10px 20px 16px;
        border-top-color: var(--line);
    }

    .pay-option:hover {
        border-color: #94a3b8;
    }

    /* Order summary rows */
    .order-summary {
        display: flex;
        flex-direction: column;
    }

    .order-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 14.5px;
        color: var(--ink);
    }

    .order-summary-label {
        color: var(--muted);
    }

    .order-summary-discount {
        color: var(--success);
    }

    /* Checkout Submit Button */
    .btn-checkout-submit {
        background: var(--teal);
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-checkout-submit:hover {
        background: var(--teal-deep);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(7, 72, 74, 0.2);
    }

    .btn-checkout-submit:active {
        transform: translateY(0);
    }

    @media (max-width: 991px) {
        .pay-option-row { padding: 14px; }
        .pay-option-badge { display: none; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        const radios = document.querySelectorAll('input[name="payment_method"]');
        const form   = document.getElementById('checkoutPaymentForm');
        const btn    = document.getElementById('btnPlaceOrder');
        const codConfirm = document.getElementById('codConfirm');

        function syncSelectedState(){
            radios.forEach((r) => {
                r.closest('.pay-option').classList.toggle('is-selected', r.checked);
            });
        }

        radios.forEach((r) => {
            r.addEventListener('change', function(){
                syncSelectedState();
                if (typeof toastr !== 'undefined') {
                    if (this.value === 'online') {
                        toastr.info('Selected: Pay Online (Instant & Secure)', 'Payment Method');
                    } else if (this.value === 'cod') {
                        toastr.info('Selected: Cash on Delivery. Please check the agreement box below.', 'Payment Method');
                    }
                }
            });
        });

        if (codConfirm) {
            codConfirm.addEventListener('change', function(){
                if (this.checked) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Cash on Delivery confirmed for ₹{{ number_format($grandTotal, 0) }}', 'COD Agreed');
                    }
                }
            });
        }

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
                            text: 'Please check the confirmation box to place Cash on Delivery order.',
                            confirmButtonColor: '#00a297'
                        }).then(() => {
                            codConfirm.focus();
                        });
                        return false;
                    }
                }

                // Show processing toast
                if (typeof toastr !== 'undefined') {
                    toastr.info('Placing your order... Please do not refresh the page.', 'Processing Order');
                }

                // Prevent multiple clicks
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing Order...';
            });
        }
    });
</script>

@endsection