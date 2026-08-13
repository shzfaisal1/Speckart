@extends('website.layout.master')
@section('content')

<!-- breadcrumbs-section -->
<section class="breadcrumbs-section py-3 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul id="breadcrumbs" class="list-inline mb-0">
                    <li class="list-inline-item"><a href="{{ route('home') }}">Home</a> /</li>
                    <li class="list-inline-item"><a href="{{ route('products') }}">Products</a> /</li>
                    <li class="list-inline-item active">Shopping Cart</li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- end breadcrumbs-section -->

<section class="shopping-cart-page py-5">
    <div class="container">
        @if(empty($cartData['items']) || count($cartData['items']) == 0)
            <div class="text-center py-5">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#00b9b9" class="bi bi-cart-x" viewBox="0 0 16 16">
                        <path d="M7.354 5.646a.5.5 0 1 0-.708.708L7.793 7.5 6.646 8.646a.5.5 0 1 0 .708.708L8.5 8.207l1.146 1.147a.5.5 0 0 0 .708-.708L9.207 7.5l1.147-1.146a.5.5 0 0 0-.708-.708L8.5 6.793 7.354 5.646z"/>
                        <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                    </svg>
                </div>
                <h3>Your Cart is Currently Empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any glasses or sunglasses yet.</p>
                <a href="{{ route('products') }}" class="btn btn-lg text-white" style="background-color: #00b9b9; border-radius: 8px;">Explore Products</a>
            </div>
        @else
            <div class="row">
                <!-- Left Column: Cart Items -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0 fw-bold">Items in Cart ({{ $cartData['item_count'] }})</h4>
                            @if($cartData['is_bogo_active'])
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold">
                                    ★ BOGO Promo Active
                                </span>
                            @endif
                        </div>
                        <hr class="text-muted opacity-25">

                        @if(!empty($cartData['bogo_fallback_message']))
                            <div class="alert alert-warning py-2 px-3 small mb-3 border-0 rounded-3 shadow-xs">
                                ⚠️ {{ $cartData['bogo_fallback_message'] }}
                            </div>
                        @endif

                        @foreach($cartData['items'] as $item)
                            <div class="cart-item-row p-3 mb-3 border rounded-3 bg-white shadow-xs position-relative" data-key="{{ $item['key'] }}">
                                <div class="row align-items-center">
                                    <!-- Image -->
                                    <div class="col-md-3 text-center mb-3 mb-md-0">
                                        <img src="{{ $item['frame_image'] }}" alt="{{ $item['frame_name'] }}" class="img-fluid rounded-3" style="max-height: 90px; object-fit: contain;">
                                    </div>

                                    <!-- Details -->
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <span class="badge bg-light text-secondary border mb-1">{{ $item['brand'] }}</span>
                                        <h5 class="fw-bold mb-1 fs-6">{{ $item['frame_name'] }}</h5>
                                        <div class="small text-muted mb-1">Size: <strong>{{ $item['size'] }}</strong></div>

                                        <!-- Selected Lens Package Info -->
                                        <div class="p-2 rounded-2 mt-2" style="background-color: #f8fbfb; border: 1px dashed #00b9b9;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="small fw-semibold text-dark">Lens: {{ $item['lens_name'] }}</span>
                                                <span class="small fw-bold text-success">
                                                    {{ $item['lens_price'] > 0 ? '+₹' . number_format($item['lens_price'], 2) : 'Included (₹0)' }}
                                                </span>
                                            </div>
                                            @if(!empty($item['lens_details']))
                                                <div class="small text-muted mt-1" style="font-size: 11px;">{{ Str::limit($item['lens_details'], 60) }}</div>
                                            @endif
                                        </div>

                                        <!-- Lenskart Style Prescription Power Box -->
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
                                            <div class="prescription-power-box mt-2 p-2 rounded-3" style="background-color: #f4f9f9; border: 1px solid #e0f2f1;">
                                                <div class="d-flex justify-content-between align-items-center mb-1 px-1">
                                                    <span class="small fw-bold text-dark" style="font-size: 13px;">
                                                        Buying for <span style="color: #06a5aa;">Your Power</span>
                                                    </span>
                                                    <button class="btn btn-sm p-0 text-decoration-none small" type="button" data-bs-toggle="collapse" data-bs-target="#rx-details-{{ $loop->index }}" aria-expanded="true" style="color: #06a5aa; font-weight: 600; font-size: 12px;">
                                                        View Details <i class="bi bi-chevron-down ms-1"></i>
                                                    </button>
                                                </div>

                                                <div class="collapse show mt-1" id="rx-details-{{ $loop->index }}">
                                                    @if(isset($rx['type']) && $rx['type'] === 'upload')
                                                        <div class="d-flex align-items-center p-2 bg-white rounded border">
                                                            <i class="bi bi-file-earmark-image fs-5 text-primary me-2"></i>
                                                            <div class="small">
                                                                <div class="fw-semibold">Uploaded Doctor Prescription</div>
                                                                @if(!empty($rx['file']))
                                                                    <a href="{{ asset($rx['file']) }}" target="_blank" class="text-primary text-decoration-underline small">View Uploaded Document</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-borderless text-center align-middle mb-0" style="font-size: 12px; background: #ffffff; border-radius: 6px; overflow: hidden; border: 1px solid #e5e5e5;">
                                                                <thead style="background: #eaf6f6; color: #333; font-weight: 700;">
                                                                    <tr>
                                                                        <th style="padding: 4px 8px;">EYE</th>
                                                                        <th style="padding: 4px 8px;">SPH</th>
                                                                        <th style="padding: 4px 8px;">CYL</th>
                                                                        <th style="padding: 4px 8px;">AXIS</th>
                                                                        <th style="padding: 4px 8px;">ADD</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr style="border-bottom: 1px solid #f0f0f0;">
                                                                        <td class="fw-bold text-secondary">R</td>
                                                                        <td class="fw-semibold">{{ !empty($rx['right_eye_sph']) ? $rx['right_eye_sph'] : '-' }}</td>
                                                                        <td class="text-muted">{{ !empty($rx['right_eye_cyl']) ? $rx['right_eye_cyl'] : '-' }}</td>
                                                                        <td class="text-muted">{{ !empty($rx['right_eye_axis']) && $rx['right_eye_axis'] != '0' ? $rx['right_eye_axis'] : '-' }}</td>
                                                                        <td class="text-muted">{{ !empty($rx['right_eye_ap']) ? $rx['right_eye_ap'] : '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-bold text-secondary">L</td>
                                                                        <td class="fw-semibold">{{ !empty($rx['left_eye_sph']) ? $rx['left_eye_sph'] : '-' }}</td>
                                                                        <td class="text-muted">{{ !empty($rx['left_eye_cyl']) ? $rx['left_eye_cyl'] : '-' }}</td>
                                                                        <td class="text-muted">{{ !empty($rx['left_eye_axis']) && $rx['left_eye_axis'] != '0' ? $rx['left_eye_axis'] : '-' }}</td>
                                                                        <td class="text-muted">{{ !empty($rx['left_eye_ap']) ? $rx['left_eye_ap'] : '-' }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                            <div class="mt-2">
                                                <span class="badge bg-success text-white">Frame Free via BOGO</span>
                                            </div>
                                        @elseif(isset($item['is_bogo_half']) && $item['is_bogo_half'])
                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark">Frame 50% OFF (BOGO Fallback)</span>
                                            </div>
                                        @elseif(isset($item['is_first_frame_free_applied']) && $item['is_first_frame_free_applied'])
                                            <div class="mt-2">
                                                <span class="badge bg-success text-white">Frame Free (First Pair Free Promo)</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Quantity & Price -->
                                    <div class="col-md-3 text-md-end">
                                        <div class="fw-bold text-dark fs-5 mb-2">
                                            @if(isset($item['is_membership']) && $item['is_membership'])
                                                ₹{{ number_format($item['frame_price'], 2) }}
                                            @elseif(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                                <span class="text-decoration-line-through text-muted fs-6">₹{{ number_format($item['frame_price'], 2) }}</span>
                                                <span class="text-success ms-1">₹{{ number_format($item['lens_price'], 2) }}</span>
                                            @elseif(isset($item['is_bogo_half']) && $item['is_bogo_half'])
                                                <span class="text-decoration-line-through text-muted fs-6">₹{{ number_format($item['frame_price'], 2) }}</span>
                                                <span class="text-success ms-1">₹{{ number_format(($item['frame_price'] * 0.5) + $item['lens_price'], 2) }}</span>
                                            @elseif(isset($item['is_first_frame_free_applied']) && $item['is_first_frame_free_applied'])
                                                <span class="text-decoration-line-through text-muted fs-6">₹{{ number_format($item['frame_price'], 2) }}</span>
                                                <span class="text-success ms-1">₹{{ number_format($item['lens_price'], 2) }}</span>
                                            @else
                                                ₹{{ number_format($item['frame_price'] + $item['lens_price'], 2) }}
                                            @endif
                                        </div>

                                        @if(isset($item['is_membership']) && $item['is_membership'])
                                            <div class="small text-muted mb-2"><i class="bi bi-shield-check text-success"></i> Premium Plan</div>
                                            <!-- Form to remove membership -->
                                            <form action="{{ route('cart.remove_membership') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded" style="font-size: 12px;" title="Remove Membership">
                                                    Remove Plan
                                                </button>
                                            </form>
                                        @else
                                            <!-- Quantity selector -->
                                            <div class="d-inline-flex align-items-center border rounded-pill px-2 py-1 bg-light">
                                                <button type="button" class="btn btn-sm p-0 me-2 qty-minus text-secondary border-0" data-key="{{ $item['key'] }}" style="line-height: 1;">-</button>
                                                <span class="fw-bold px-2 item-qty" style="font-size: 14px;">{{ $item['quantity'] }}</span>
                                                <button type="button" class="btn btn-sm p-0 ms-2 qty-plus text-secondary border-0" data-key="{{ $item['key'] }}" style="line-height: 1;">+</button>
                                            </div>

                                            <!-- Delete Item -->
                                            <button type="button" class="btn btn-sm text-danger border-0 ms-2 remove-cart-item" data-key="{{ $item['key'] }}" title="Remove Item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                                    <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06l-.5-8.5a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Column: Order Summary & Coupon -->
                <div class="col-lg-5">
                    <!-- Coupon Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h5 class="fw-bold mb-3">Apply Promo Code</h5>
                        <div class="input-group mb-2">
                            <input type="text" id="coupon-code-input" class="form-control rounded-start-3" placeholder="Enter Coupon Code (e.g. SINGLE)" value="{{ session('applied_coupon.code', '') }}">
                            <button id="apply-coupon-btn" class="btn text-white px-4 rounded-end-3" type="button" style="background-color: #00b9b9;">APPLY</button>
                        </div>
                        @if(session()->has('applied_coupon'))
                            <div class="alert alert-success py-2 px-3 small mb-0 d-flex justify-content-between align-items-center">
                                <span>Coupon <strong>{{ session('applied_coupon.code') }}</strong> applied!</span>
                            </div>
                        @else
                            <div class="small text-muted">Use code <span class="badge bg-light text-dark border">SINGLE</span> for 25% off frame price.</div>
                        @endif
                    </div>

                    <!-- Summary Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-3">Bill Details</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Frame Subtotal</span>
                            <span class="fw-semibold">₹{{ number_format($cartData['frame_subtotal'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Lens Add-ons Total</span>
                            <span class="fw-semibold text-success">+₹{{ number_format($cartData['lens_subtotal'], 2) }}</span>
                        </div>

                        @if($cartData['bogo_savings'] > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success fw-semibold">BOGO Savings</span>
                                <span class="fw-bold text-success">-₹{{ number_format($cartData['bogo_savings'], 2) }}</span>
                            </div>
                        @endif

                        @if(isset($cartData['first_frame_free_save']) && $cartData['first_frame_free_save'] > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success fw-semibold">First Pair Free Discount</span>
                                <span class="fw-bold text-success">-₹{{ number_format($cartData['first_frame_free_save'], 2) }}</span>
                            </div>
                        @endif

                        @if($cartData['coupon_discount'] > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success fw-semibold">Coupon Discount</span>
                                <span class="fw-bold text-success">-₹{{ number_format($cartData['coupon_discount'], 2) }}</span>
                            </div>
                        @endif

                        <hr class="text-muted opacity-25 my-3">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-5 text-dark">Total Amount</span>
                            <span class="fw-bold fs-4" style="color: #00b9b9;">₹{{ number_format($cartData['grand_total'], 2) }}</span>
                        </div>

                        <a href="{{ route('shipping-details') }}" class="btn btn-lg text-white w-100 py-3 fw-bold rounded-3 shadow-sm mb-3" style="background-color: #00b9b9;">
                            Proceed to Checkout
                        </a>
                    </div>

                    <!-- Theme-Matched Gold Membership Banner Box -->
                    @if(empty($cartData['has_membership_in_cart']))
                    <div class="card border-0 shadow-sm rounded-4 p-4 mt-4" style="background: linear-gradient(135deg, #e6f7f7, #ffffff); border: 1.5px solid #00b9b9 !important;">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge px-3 py-1 text-white font-weight-bold" style="background-color: #00b9b9; border-radius: 20px; font-size: 11px;">
                                👑 GOLD MEMBERSHIP
                            </span>
                        </div>
                        <div class="fw-bold text-dark mb-1" style="font-size: 15px; line-height: 1.4;">
                            Add Gold Max Membership and
                        </div>
                        <div class="small mb-3" style="font-size: 14px; color: #008989; font-weight: 600;">
                            Avail Buy 1 Get 1 Free + 10% Loyalty Points
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3" style="border-top: 1px dashed #b2ebd7;">
                            <a href="{{ route('website.membership') }}" class="fw-bold text-decoration-none" style="font-size: 14px; color: #008989;">
                                Add Gold
                            </a>
                            <a href="{{ route('website.membership') }}" class="btn btn-sm rounded-circle border-0 d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background-color: #00b9b9;">
                                ➔
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="card border-0 shadow-sm rounded-4 p-4 mt-4" style="background: linear-gradient(135deg, #f0fdf4, #ffffff); border: 1.5px solid #10b981 !important;">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success px-3 py-1 text-white font-weight-bold" style="border-radius: 20px; font-size: 11px;">
                                👑 MEMBERSHIP ADDED
                            </span>
                        </div>
                        <div class="fw-bold text-dark mb-1" style="font-size: 15px; line-height: 1.4;">
                            Membership Added to Cart!
                        </div>
                        <div class="small text-success fw-semibold">
                            ✓ BOGO & member discounts active.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Cart AJAX JS Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const csrfToken = "{{ csrf_token() }}";

        // Update Quantity Plus
        $('.qty-plus').click(function() {
            const key = $(this).data('key');
            const qtySpan = $(this).siblings('.item-qty');
            const newQty = parseInt(qtySpan.text()) + 1;
            updateCartQty(key, newQty);
        });

        // Update Quantity Minus
        $('.qty-minus').click(function() {
            const key = $(this).data('key');
            const qtySpan = $(this).siblings('.item-qty');
            const currentQty = parseInt(qtySpan.text());
            if (currentQty > 1) {
                updateCartQty(key, currentQty - 1);
            }
        });

        // Remove Item
        $('.remove-cart-item').click(function() {
            const key = $(this).data('key');
            if (confirm('Are you sure you want to remove this item from your cart?')) {
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

        // Apply Coupon
        $('#apply-coupon-btn').click(function() {
            const code = $('#coupon-code-input').val().trim();
            if (!code) {
                alert('Please enter a coupon code.');
                return;
            }

            $.ajax({
                url: "{{ route('cart.coupon') }}",
                type: "POST",
                data: { _token: csrfToken, coupon_code: code },
                success: function(res) {
                    alert(res.message);
                    window.location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON ? xhr.responseJSON.message : 'Invalid coupon code.');
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
    });
</script>

@endsection