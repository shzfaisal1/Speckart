@extends('website.layout.master')
@section('content')

<!-- Breadcrumbs -->
<section class="breadcrumbs-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul id="breadcrumbs">
                    <li><a href="#">Shipping Address</a></li>
                    <li><a href="#" class="active">Payment</a></li>
                    <li><a href="#">Summary</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Payment Section -->
<section class="payment-page-sec">
    <div class="container">
        <div class="row">

            <!-- Left: Payment Methods -->
            <div class="col-lg-7">
                <div class="payment-page-sec-txt">

                    <!-- Payment Options -->
                    <div class="pay-options" id="paymentOptions">

                        <h4 class="pay-options-heading">Choose payment method</h4>

                        {{-- Online Payment (Razorpay) --}}
                        <label class="pay-option" for="pay_online">
                            <input type="radio" name="payment_method" id="pay_online" value="online" checked>
                            <div class="pay-option-row">
                                <span class="pay-option-radio"></span>
                                <span class="pay-option-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="5" width="20" height="14" rx="2.5"/>
                                        <path d="M2 10h20"/>
                                    </svg>
                                </span>
                                <span class="pay-option-text">
                                    <span class="pay-option-title">Pay Online</span>
                                      <span class="pay-option-sub">secured by Razorpay</span>
                                    <!--<span class="pay-option-sub">UPI, Cards &amp; Net Banking &middot; secured by Razorpay</span>-->
                                </span>
                                <span class="pay-option-badge">Recommended</span>
                            </div>
                            <!--<div class="pay-option-detail">-->
                            <!--    <p>You'll be redirected to Razorpay's secure checkout to complete your payment.</p>-->
                            <!--    <div class="pay-option-marks">-->
                            <!--        <span>UPI</span>-->
                            <!--        <span>Visa</span>-->
                            <!--        <span>Mastercard</span>-->
                            <!--        <span>RuPay</span>-->
                            <!--        <span>Net Banking</span>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </label>

                        {{-- Cash on Delivery --}}
                        <label class="pay-option" for="pay_cod">
                            <input type="radio" name="payment_method" id="pay_cod" value="cod">
                            <div class="pay-option-row">
                                <span class="pay-option-radio"></span>
                                <span class="pay-option-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H7"/>
                                    </svg>
                                </span>
                                <span class="pay-option-text">
                                    <span class="pay-option-title">Cash on Delivery</span>
                                    <span class="pay-option-sub">Pay when your order arrives</span>
                                </span>
                            </div>
                            <div class="pay-option-detail">
                                <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="codConfirm" name="cod_confirm" style="
    margin-left: -17px;
">
                                    <label class="form-check-label" for="codConfirm" style="margin-left:7px;">
                                                I agree to pay <span class="rupee">&#8377;</span><span class="payable-amount">3000</span> in cash at the time of delivery
                                    </label>
                                </div>
                            </div>
                        </label>

                        <input type="hidden" name="selected_payment_method" id="selectedPaymentMethod" value="online">

                        <button type="button" class="btn btn-checkout-submit w-100">Continue</button>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="col-lg-5">
                <div class="add-shipping-details-right">
                    <div class="add-shipping-details-right-img">
                        <img src="{{ asset('assets/img/bg/Add-Shipping-details-bg.png') }}" alt="">
                    </div>
                    <div class="add-shipping-details-right-card">
                        <div class="order-summary">
                            <div class="order-summary-row">
                                <span class="order-summary-label">Total Item Price</span>
                                <span class="order-summary-value"><span class="rupee">&#8377;</span><span class="item-price">3499</span></span>
                            </div>
                            <div class="order-summary-row">
                                <span class="order-summary-label">Total Discount</span>
                                <span class="order-summary-value order-summary-discount">&#8722; <span class="rupee">&#8377;</span><span class="discount-amount">499</span></span>
                            </div>
                            <div class="order-summary-row order-summary-total">
                                <span class="order-summary-label">Total Payable</span>
                                <span class="order-summary-value"><span class="rupee">&#8377;</span><span class="payable-amount">3000</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    :root{
        --ink:#12201f;
        --teal:#0f4c4b;
        --teal-deep:#0a3534;
        --teal-tint:#eef6f5;
        --line:#e3e7e6;
        --muted:#6b7674;
        --success:#1f8a70;
        --radius:14px;
    }

    .pay-options{
        margin-top: 24px;
    }
    .pay-options-heading{
        font-size: 15px;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 14px;
        letter-spacing: 0.01em;
    }

    .pay-option{
        display: block;
        border: 1.5px solid var(--line);
        border-radius: var(--radius);
        margin-bottom: 12px;
        cursor: pointer;
        background: #fff;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        overflow: hidden;
    }
    .pay-option input[type="radio"]{
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .pay-option-row{
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 18px;
    }

    .pay-option-radio{
        flex: 0 0 auto;
        width: 19px;
        height: 19px;
        border-radius: 50%;
        border: 1.5px solid #c7cfcd;
        position: relative;
        transition: border-color 0.18s ease;
    }
    .pay-option-radio::after{
        content:"";
        position:absolute;
        inset: 3px;
        border-radius: 50%;
        background: var(--teal);
        transform: scale(0);
        transition: transform 0.18s ease;
    }

    .pay-option-icon{
        flex: 0 0 auto;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--teal-tint);
        color: var(--teal);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.18s ease, color 0.18s ease;
    }

    .pay-option-text{
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1 1 auto;
        min-width: 0;
    }
    .pay-option-title{
        font-size: 15px;
        font-weight: 600;
        color: var(--ink);
    }
    .pay-option-sub{
        font-size: 12.5px;
        color: var(--muted);
    }

    .pay-option-badge{
        flex: 0 0 auto;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.02em;
        color: var(--teal-deep);
        background: var(--teal-tint);
        border: 1px solid #cfe6e4;
        padding: 4px 9px;
        border-radius: 20px;
    }

    .pay-option-detail{
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        padding: 0 18px;
        transition: max-height 0.25s ease, opacity 0.2s ease, padding 0.25s ease;
        border-top: 1px solid transparent;
    }
    .pay-option-detail p{
        font-size: 13px;
        color: var(--muted);
        margin: 14px 0 12px;
    }

    .pay-option-marks{
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-bottom: 16px;
    }
    .pay-option-marks span{
        font-size: 11.5px;
        font-weight: 500;
        color: var(--muted);
        border: 1px solid var(--line);
        border-radius: 6px;
        padding: 4px 9px;
    }

    .pay-option .form-check{
        padding: 14px 0 18px;
    }
    .pay-option .form-check-label{
        font-size: 13.5px;
        color: var(--ink);
    }

    .pay-option.is-selected{
        border-color: var(--teal);
        box-shadow: 0 0 0 3px var(--teal-tint);
    }
    .pay-option.is-selected .pay-option-radio{
        border-color: var(--teal);
    }
    .pay-option.is-selected .pay-option-radio::after{
        transform: scale(1);
    }
    .pay-option.is-selected .pay-option-icon{
        background: var(--teal);
        color: #fff;
    }
    .pay-option.is-selected .pay-option-detail{
        max-height: 200px;
        opacity: 1;
        border-top-color: var(--line);
    }

    .pay-option:hover{
        border-color: #b9c7c5;
    }

    .rupee{ font-family: inherit; }

    /* Order summary */
    .order-summary{
        display: flex;
        flex-direction: column;
    }
    .order-summary-row{
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--line);
        font-size: 14px;
        color: var(--ink);
    }
    .order-summary-row:first-child{
        padding-top: 0;
    }
    .order-summary-label{
        color: var(--muted);
    }
    .order-summary-value{
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .order-summary-discount{
        color: var(--success);
    }
    .order-summary-total{
        border-bottom: none;
        padding-top: 14px;
        margin-top: 2px;
    }
    .order-summary-total .order-summary-label,
    .order-summary-total .order-summary-value{
        color: var(--ink);
        font-weight: 700;
        font-size: 16px;
    }

    /* Checkout button */
    .btn-checkout-submit{
        background: var(--teal);
        color: #fff;
        padding: 15px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin-top: 6px;
        transition: background 0.18s ease, transform 0.06s ease;
    }
    .btn-checkout-submit:hover{
        background: var(--teal-deep);
    }
    .btn-checkout-submit:active{
        transform: scale(0.99);
    }

    @media (max-width: 991px){
        .pay-option-row{ padding: 15px; }
        .pay-option-badge{ display:none; }
    }
</style>

<script>
    (function(){
        const radios = document.querySelectorAll('input[name="payment_method"]');
        const hiddenField = document.getElementById('selectedPaymentMethod');

        function syncSelectedState(){
            radios.forEach((r) => {
                r.closest('.pay-option').classList.toggle('is-selected', r.checked);
            });
        }

        radios.forEach((r) => {
            r.addEventListener('change', function(){
                hiddenField.value = this.value;
                syncSelectedState();
            });
        });

        syncSelectedState(); // reflect the pre-checked "online" option on load

        document.querySelector('.btn-checkout-submit').addEventListener('click', function(e){
            e.preventDefault();

            const method = hiddenField.value;

            if (method === 'cod') {
                const confirmed = document.getElementById('codConfirm').checked;
                if (!confirmed) {
                    alert('Please confirm Cash on Delivery to continue.');
                    return;
                }
            }

            const form = document.createElement('form');
            form.action = "{{ route('checkout.complete') }}";
            form.method = 'POST';

            form.appendChild(Object.assign(document.createElement('input'), {
                type: 'hidden', name: '_token', value: "{{ csrf_token() }}"
            }));

            form.appendChild(Object.assign(document.createElement('input'), {
                type: 'hidden', name: 'payment_method', value: method
            }));

            document.body.appendChild(form);
            form.submit();
        });
    })();
</script>

@endsection