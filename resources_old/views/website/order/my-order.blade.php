@extends('website.layout.master')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/X.X.X/css/all.min.css">

    <style>
        /* =========================
               MY ORDERS PAGE
            ========================= */

        .my-orders {
            background: linear-gradient(180deg,
                    rgba(250, 245, 158, .15) 0%,
                    #f8fbfb 25%,
                    #f8fbfb 100%);
            min-height: 100vh;
            padding: 30px 0;
        }

        /* =========================
               PAGE HEADER
            ========================= */

        .page-header h3 {
            color: #07484A;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #7d8a8b;
            margin: 0;
        }

        /* =========================
               SUMMARY CARDS (also act as filters)
            ========================= */

        .order-stat-card {
            background: linear-gradient(135deg,
                    #07484A 0%,
                    #00B9B9 100%);
            color: #fff;
            border-radius: 20px;
            padding: 22px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(7, 72, 74, .15);
            transition: .3s ease;
            border: none;
            width: 100%;
            cursor: pointer;
            position: relative;
        }

        .order-stat-card:hover {
            transform: translateY(-4px);
        }

        .order-stat-card:focus-visible {
            outline: 3px solid #FAF59E;
            outline-offset: 3px;
        }

        .order-stat-card.is-active {
            box-shadow: 0 0 0 3px #FAF59E, 0 15px 35px rgba(7, 72, 74, .25);
        }

        .order-stat-card h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .order-stat-card p {
            margin: 0;
            color: rgba(255, 255, 255, .85);
            font-size: 14px;
        }

        /* =========================
               FILTERS
            ========================= */

        .order-filters {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 5px;
            scrollbar-width: none;
        }

        .order-filters::-webkit-scrollbar {
            display: none;
        }

        .order-filters button {
            border: none;
            background: #fff;
            color: #07484A;
            padding: 10px 18px;
            border-radius: 50px;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .05);
            transition: .3s;
            cursor: pointer;
        }

        .order-filters button:hover {
            background: #00B9B9;
            color: #fff;
        }

        .order-filters button.active {
            background: #07484A;
            color: #FAF59E;
        }

        /* =========================
               ORDER CARD
            ========================= */

        .order-card {
            background: #fff;
            border: 1px solid rgba(7, 72, 74, .08);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 8px 30px rgba(7, 72, 74, .06);
            transition: .3s ease;
        }

        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(7, 72, 74, .12);
        }

        /* =========================
               CARD HEADER
            ========================= */

        .order-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .order-id {
            color: #7d8a8b;
            font-size: 14px;
            font-weight: 500;
        }

        /* =========================
               STATUS BADGES
            ========================= */

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
        }

        .status i {
            font-size: 12px;
        }

        .transit {
            background: rgba(0, 185, 185, .12);
            color: #00B9B9;
        }

        .delivered {
            background: rgba(7, 72, 74, .12);
            color: #07484A;
        }

        .cancelled {
            background: rgba(220, 53, 69, .1);
            color: #dc3545;
        }

        /* =========================
               PRODUCT IMAGE
            ========================= */

        .product-thumb {
            background: #f8fbfb;
            border-radius: 18px;
            padding: 15px;
            border: 1px solid rgba(7, 72, 74, .06);
        }

        .product-thumb img {
            width: 100%;
            height: 130px;
            object-fit: contain;
            transition: .3s;
        }

        .order-card:hover .product-thumb img {
            transform: scale(1.05);
        }

        /* =========================
               PRODUCT DETAILS
            ========================= */

        .brand {
            color: #00B9B9;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .product-title {
            font-size: 17px;
            font-weight: 600;
            color: #222;
            margin-bottom: 10px;
        }

        .order-meta {
            color: #7d8a8b;
            font-size: 14px;
        }

        .delivery-date {
            margin-top: 6px;
            color: #00B9B9;
            font-size: 14px;
            font-weight: 500;
        }

        .order-note {
            margin-top: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .order-note.is-positive {
            color: #07484A;
        }

        .order-note.is-muted {
            color: #dc3545;
        }

        .price {
            margin-top: 12px;
            font-size: 24px;
            font-weight: 700;
            color: #07484A;
        }

        /* =========================
               ACTION BUTTONS
            ========================= */

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-theme {
            background: #07484A;
            border: 1px solid #07484A;
            color: #FAF59E;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 16px;
        }

        .btn-theme:hover {
            background: #063739;
            border-color: #063739;
            color: #FAF59E;
        }

        .btn-outline-theme {
            background: #fff;
            border: 1px solid #00B9B9;
            color: #00B9B9;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 16px;
        }

        .btn-outline-theme:hover {
            background: #00B9B9;
            color: #fff;
        }

        .btn-danger-theme {
            background: #fff;
            border: 1px solid #f1b3b9;
            color: #dc3545;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 16px;
        }

        .btn-danger-theme:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        /* =========================
               ORDER PROGRESS
            ========================= */

        .order-progress {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .order-progress .step {
            background: #edf6f6;
            color: #07484A;
            border-radius: 30px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .order-progress .step.active {
            background: #07484A;
            color: #FAF59E;
        }

        /* =========================
               EMPTY STATE
            ========================= */

        .empty-orders {
            background: #fff;
            border-radius: 24px;
            padding: 50px 20px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(7, 72, 74, .06);
        }

        .empty-orders i {
            font-size: 42px;
            color: #00B9B9;
            background: rgba(0, 185, 185, .1);
            width: 84px;
            height: 84px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
        }

        .empty-orders h4 {
            color: #07484A;
            font-weight: 700;
        }

        .empty-orders p {
            color: #7d8a8b;
            max-width: 360px;
            margin: 6px auto 20px;
        }

        /* =========================
               MOBILE
            ========================= */

        @media (max-width:768px) {

            .my-orders {
                padding: 15px 0;
            }

            .order-card {
                padding: 16px;
                border-radius: 18px;
            }

            .order-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .product-thumb img {
                height: 90px;
            }

            .product-title {
                font-size: 14px;
                line-height: 1.4;
            }

            .price {
                font-size: 18px;
            }

            .action-buttons {
                margin-top: 15px;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .order-stat-card {
                padding: 16px;
            }

            .order-stat-card h3 {
                font-size: 22px;
            }
        }
    </style>

    <section class="my-orders py-4">
        <div class="container">

            @php
                $user = auth()->user();
                $activeMembership = null;
                if ($user) {
                    $customer = DB::table('tbl_customer')
                        ->where('customer_id', $user->id)
                        ->first();
                    if ($customer && $customer->membership_card_id && $customer->membership_expiry && \Carbon\Carbon::parse($customer->membership_expiry)->isFuture()) {
                        $dbCard = DB::table('tbl_membership_card')->where('card_id', $customer->membership_card_id)->first();
                        if ($dbCard) {
                            $activeMembership = [
                                'card_name' => $dbCard->card_name,
                                'expiry' => $customer->membership_expiry,
                                'enable_bogo' => $dbCard->enable_bogo,
                                'coupon_percent' => $dbCard->coupon_percent
                            ];
                        }
                    }
                }
                if (!$activeMembership && session()->has('active_membership')) {
                    $sessMembership = session()->get('active_membership');
                    if (\Carbon\Carbon::parse($sessMembership['expiry'])->isFuture()) {
                        $dbCard = DB::table('tbl_membership_card')->where('card_id', $sessMembership['card_id'])->first();
                        if ($dbCard) {
                            $activeMembership = [
                                'card_name' => $dbCard->card_name,
                                'expiry' => $sessMembership['expiry'],
                                'enable_bogo' => $dbCard->enable_bogo,
                                'coupon_percent' => $dbCard->coupon_percent
                            ];
                        }
                    }
                }
            @endphp

            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 p-3 mb-4" role="alert" style="background-color: #e6fdf5; color: #0f5132; border: 1px solid #c2f4e3 !important;">
                    <i class="fa-solid fa-circle-check me-2 text-success"></i> {!! session()->get('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 p-3 mb-4" role="alert" style="background-color: #fdf2f2; color: #842029; border: 1px solid #fde2e2 !important;">
                    <i class="fa-solid fa-circle-exclamation me-2 text-danger"></i> {{ session()->get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Membership Status Banner -->
            @if($activeMembership)
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #07484A 0%, #00B9B9 100%); color: #fff;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2" style="font-size: 11px;">👑 MEMBER BENEFITS ACTIVE</span>
                            <h4 class="fw-bold text-white mb-1">{{ $activeMembership['card_name'] }}</h4>
                            <p class="mb-0 small opacity-90">Your premium privileges are active. Expiry: <strong>{{ \Carbon\Carbon::parse($activeMembership['expiry'])->format('d M Y') }}</strong></p>
                        </div>
                        <div class="d-flex gap-3">
                            @if(!empty($activeMembership['enable_bogo']))
                                <div class="px-3 py-2 rounded-3 bg-white bg-opacity-10 text-center">
                                    <div class="fw-bold text-warning" style="font-size: 16px;">ON</div>
                                    <div class="small opacity-80" style="font-size: 11px;">Buy 1 Get 1</div>
                                </div>
                            @endif
                            @if(!empty($activeMembership['coupon_percent']))
                                <div class="px-3 py-2 rounded-3 bg-white bg-opacity-10 text-center">
                                    <div class="fw-bold text-warning" style="font-size: 16px;">{{ $activeMembership['coupon_percent'] }}%</div>
                                    <div class="small opacity-80" style="font-size: 11px;">Discount</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #e6f7f7, #ffffff); border: 1.5px solid #00b9b9 !important;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="badge px-3 py-1 text-white font-weight-bold mb-2" style="background-color: #00b9b9; border-radius: 20px; font-size: 11px;">👑 JOIN THE CLUB</span>
                            <h5 class="fw-bold text-dark mb-1">Unlock Buy 1 Get 1 Free & VIP Perks</h5>
                            <p class="text-muted mb-0 small">Get exclusive member coupons, early flash sale access, and loyalty cashbacks.</p>
                        </div>
                        <a href="{{ route('website.membership') }}" class="btn text-white fw-bold px-4 py-2" style="background-color: #00b9b9; border-radius: 25px;">
                            View Plans ➔
                        </a>
                    </div>
                </div>
            @endif

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">My Orders</h3>
                    <p class="text-muted mb-0">Track and manage your orders</p>
                </div>
            </div>

            @php
                $totalCount      = $orders->count();
                $processingCount = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['0', 'pending', 'confirmed', 'processing', 'in_lab']))->count();
                $transitCount    = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['1', 'ready_to_ship', 'shipped', 'transit', 'out_for_delivery']))->count();
                $deliveredCount  = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['2', 'delivered', 'completed']))->count();
                $cancelledCount  = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['3', 'cancelled', 'returned']))->count();
            @endphp

            <!-- Summary Cards (double as filters — click to filter by status) -->
            <div class="row g-3 mb-4">

                <div class="col-md-3 col-6">
                    <button type="button" class="order-stat-card is-active" data-filter="all">
                        <h3>{{ $totalCount }}</h3>
                        <p>Total Orders</p>
                    </button>
                </div>

                <div class="col-md-3 col-6">
                    <button type="button" class="order-stat-card" data-filter="transit">
                        <h3>{{ $transitCount }}</h3>
                        <p>In Transit</p>
                    </button>
                </div>

                <div class="col-md-3 col-6">
                    <button type="button" class="order-stat-card" data-filter="delivered">
                        <h3>{{ $deliveredCount }}</h3>
                        <p>Delivered</p>
                    </button>
                </div>

                <div class="col-md-3 col-6">
                    <button type="button" class="order-stat-card" data-filter="cancelled">
                        <h3>{{ $cancelledCount }}</h3>
                        <p>Cancelled</p>
                    </button>
                </div>

            </div>

            <!-- Filters -->
            <div class="order-filters mb-4">
                <button class="active" data-filter="all">All Orders ({{ $totalCount }})</button>
                <button data-filter="processing">Processing ({{ $processingCount }})</button>
                <button data-filter="transit">Shipped ({{ $transitCount }})</button>
                <button data-filter="delivered">Delivered ({{ $deliveredCount }})</button>
                <button data-filter="cancelled">Cancelled ({{ $cancelledCount }})</button>
            </div>

            @forelse($orders as $order)
                @php
                    $statusStr = strtolower((string)($order->order_status ?? ($order->sales_status ?? 0)));
                    if (in_array($statusStr, ['1', 'shipped', 'transit', 'ready_to_ship', 'out_for_delivery'])) {
                        $dataStatus  = 'transit';
                        $statusLabel = ($statusStr === 'ready_to_ship') ? 'Ready to Ship' : 'In Transit';
                        $statusClass = 'transit';
                        $statusIcon  = 'fa-truck-fast';
                    } elseif (in_array($statusStr, ['2', 'delivered', 'completed'])) {
                        $dataStatus  = 'delivered';
                        $statusLabel = 'Delivered';
                        $statusClass = 'delivered';
                        $statusIcon  = 'fa-circle-check';
                    } elseif (in_array($statusStr, ['3', 'cancelled', 'returned'])) {
                        $dataStatus  = 'cancelled';
                        $statusLabel = ($statusStr === 'returned') ? 'Returned' : 'Cancelled';
                        $statusClass = 'cancelled';
                        $statusIcon  = 'fa-circle-xmark';
                    } else {
                        $dataStatus  = 'processing';
                        $statusLabel = ($statusStr === 'confirmed') ? 'Confirmed' : (($statusStr === 'pending') ? 'Order Placed' : 'Processing');
                        $statusClass = 'transit';
                        $statusIcon  = 'fa-clock';
                    }

                    $orderId      = $order->id ?? ($order->sale_id ?? 0);
                    $orderNo      = $order->order_no ?? ('SPECK' . $orderId);
                    $orderDate    = !empty($order->sale_date) ? \Carbon\Carbon::parse($order->sale_date)->format('d M Y') : (\Carbon\Carbon::parse($order->created_at)->format('d M Y'));
                    $totalPayable = (float)($order->total_payable ?? ($order->pay_amount ?? 0));
                    $products     = $order->products ?? collect();
                @endphp

                <div class="order-card" data-status="{{ $dataStatus }}">

                    <div class="order-top">
                        <span class="status {{ $statusClass }}">
                            <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusLabel }}
                        </span>

                        <span class="order-id">
                            Order ID : {{ $orderNo }}
                        </span>
                    </div>

                    @forelse($products as $prod)
                        <div class="row align-items-center {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">

                            <div class="col-lg-2 col-4">
                                <div class="product-thumb">
                                    <img src="{{ $prod->image ?? asset('website/assets/img/bg/Eyeglasses1.png') }}" alt="{{ $prod->product_deatils ?? 'Product' }}">
                                </div>
                            </div>

                            <div class="col-lg-7 col-8">

                                <h6 class="brand mb-1">
                                    {{ $prod->product_company ?? 'Speckarts' }}
                                </h6>

                                <h5 class="product-title mb-1">
                                    {{ $prod->product_deatils ?? 'Eyeglasses Product' }}
                                </h5>

                                <div class="order-meta mb-1">
                                    Ordered on {{ $orderDate }}
                                    @if(!empty($prod->qty) && $prod->qty > 1)
                                        | Qty: {{ $prod->qty }}
                                    @endif
                                </div>

                                @if($dataStatus === 'processing' || $dataStatus === 'transit')
                                    <div class="delivery-date">
                                        Expected Delivery: <strong>{{ \Carbon\Carbon::parse($order->sale_date ?? now())->addDays(4)->format('d M Y') }}</strong>
                                    </div>
                                @elseif($dataStatus === 'delivered')
                                    <div class="order-note is-positive">
                                        <i class="fa-solid fa-box-open me-1"></i> Package delivered — enjoy your new pair
                                    </div>
                                @elseif($dataStatus === 'cancelled')
                                    <div class="order-note is-muted">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Order cancelled
                                    </div>
                                @endif

                                <div class="price mt-2">
                                    ₹{{ number_format((float)($prod->item_price ?? $prod->sale_price ?? $totalPayable), 2) }}
                                </div>

                                @if($dataStatus !== 'cancelled')
                                    <div class="order-progress mt-3">
                                        <div class="step active">Ordered</div>
                                        <div class="step {{ in_array($dataStatus, ['transit', 'delivered']) ? 'active' : '' }}">Packed</div>
                                        <div class="step {{ in_array($dataStatus, ['transit', 'delivered']) ? 'active' : '' }}">Shipped</div>
                                        <div class="step {{ $dataStatus === 'delivered' ? 'active' : '' }}">Delivered</div>
                                    </div>
                                @endif

                            </div>

                            <div class="col-lg-3 mt-3 mt-lg-0">

                                <div class="action-buttons">
                                    @if($dataStatus === 'processing' || $dataStatus === 'transit')
                                        <form action="{{ route('my-orders.cancel', $orderId) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger-theme w-100">
                                                Cancel Order
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('my-orders.reorder', $orderId) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-theme w-100">
                                                Reorder
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </div>

                        </div>
                    @empty
                        <div class="row align-items-center">
                            <div class="col-lg-2 col-4">
                                <div class="product-thumb">
                                    <img src="{{ asset('website/assets/img/bg/Eyeglasses1.png') }}" alt="Speckart Order">
                                </div>
                            </div>
                            <div class="col-lg-7 col-8">
                                <h6 class="brand mb-1">Speckarts</h6>
                                <h5 class="product-title">Order #{{ $orderNo }}</h5>
                                <div class="order-meta">Ordered on {{ $orderDate }}</div>
                                <div class="price">₹{{ number_format($totalPayable, 2) }}</div>
                            </div>
                            <div class="col-lg-3 mt-3 mt-lg-0">
                                <div class="action-buttons">
                                    @if($dataStatus === 'processing' || $dataStatus === 'transit')
                                        <form action="{{ route('my-orders.cancel', $orderId) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger-theme w-100">
                                                Cancel Order
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('my-orders.reorder', $orderId) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-theme w-100">
                                                Reorder
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforelse

                </div>
            @empty
                <div class="empty-orders py-5" id="emptyOrdersState">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <h4>No orders here yet</h4>
                    <p>Once you place an order, you'll be able to track its status and history right here.</p>
                    <a href="{{ route('home') }}" class="btn btn-theme px-4 py-2 mt-2">Start Shopping</a>
                </div>
            @endforelse

            <div class="empty-orders d-none py-5" id="emptyOrdersFilterState">
                <i class="fa-solid fa-bag-shopping"></i>
                <h4>No matching orders found</h4>
                <p>There are no orders in this filter category.</p>
            </div>

        </div>
    </section>

    <script>
        (function() {
            var pillFilters = document.querySelectorAll('.order-filters button');
            var statCards = document.querySelectorAll('.order-stat-card');
            var orderCards = document.querySelectorAll('.order-card');
            var emptyFilterState = document.getElementById('emptyOrdersFilterState');

            function applyFilter(filter) {
                var visibleCount = 0;

                orderCards.forEach(function(card) {
                    var matches = filter === 'all' || card.dataset.status === filter;
                    card.classList.toggle('d-none', !matches);
                    if (matches) visibleCount++;
                });

                if (emptyFilterState && orderCards.length > 0) {
                    emptyFilterState.classList.toggle('d-none', visibleCount !== 0);
                }

                pillFilters.forEach(function(btn) {
                    btn.classList.toggle('active', btn.dataset.filter === filter);
                });

                statCards.forEach(function(card) {
                    card.classList.toggle('is-active', card.dataset.filter === filter);
                });
            }

            pillFilters.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    applyFilter(btn.dataset.filter);
                });
            });

            statCards.forEach(function(card) {
                card.addEventListener('click', function() {
                    applyFilter(card.dataset.filter);
                });
            });
        })();
    </script>




    <!-- menu tab -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script>
        // Show the first tab and hide the rest
        $('#tabs-nav li:first-child').addClass('active');
        $('.tab-content').hide();
        $('.tab-content:first').show();

        // Click function
        $('#tabs-nav li').mouseenter(function() {
            $('#tabs-nav li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();

            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    </script>

    <script>
        // Show the first tab and hide the rest
        $('#tabs-navs1 li:first-child').addClass('active');
        $('.tab-content1').hide();
        $('.tab-content1:first').show();

        // Click function
        $('#tabs-nav1 li').mouseenter(function() {
            $('#tabs-nav1 li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content1').hide();

            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    </script>

    <script>
        // Show the first tab and hide the rest
        $('#tabs-navs2 li:first-child').addClass('active');
        $('.tab-content2').hide();
        $('.tab-content2:first').show();

        // Click function
        $('#tabs-nav2 li').mouseenter(function() {
            $('#tabs-nav2 li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content2').hide();

            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    </script>

    <script>
        // Show the first tab and hide the rest
        $('#tabs-navs3 li:first-child').addClass('active');
        $('.tab-content3').hide();
        $('.tab-content3:first').show();

        // Click function
        $('#tabs-nav3 li').mouseenter(function() {
            $('#tabs-nav3 li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content3').hide();

            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    </script>
    <!-- end menu tab -->
@endsection
