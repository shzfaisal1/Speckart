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
    align-items: center;
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
    .order-action-btns .btn-order-cancel {
        flex: 1;
        width: 100%;
        justify-content: center;
    }
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
            $cancelledCount  = $orders->filter(fn($o) => in_array(strtolower((string)($o->order_status ?? ($o->sales_status ?? 0))), ['3', 'cancelled', 'returned']))->count();
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
                    
                    if (in_array($statusStr, ['1', 'shipped', 'transit', 'ready_to_ship', 'out_for_delivery'])) {
                        $dataStatus   = 'transit';
                        $statusLabel  = ($statusStr === 'ready_to_ship') ? 'Ready to Ship' : 'In Transit';
                        $statusClass  = 'transit';
                        $statusIcon   = 'bi-truck';
                    } elseif (in_array($statusStr, ['2', 'delivered', 'completed'])) {
                        $dataStatus   = 'delivered';
                        $statusLabel  = 'Delivered';
                        $statusClass  = 'delivered';
                        $statusIcon   = 'bi-check-circle-fill';
                    } elseif (in_array($statusStr, ['3', 'cancelled', 'returned'])) {
                        $dataStatus   = 'cancelled';
                        $statusLabel  = ($statusStr === 'returned') ? 'Returned' : 'Cancelled';
                        $statusClass  = 'cancelled';
                        $statusIcon   = 'bi-x-circle-fill';
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
                    $searchCorpus = strtolower($orderNo . ' ' . $statusLabel . ' ' . $products->pluck('product_deatils')->join(' '));
                @endphp

                <div class="order-box-card" data-status="{{ $dataStatus }}" data-search="{{ $searchCorpus }}">
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

                        <div>
                            <span class="order-status-badge {{ $statusClass }}">
                                <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
                            </span>
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
                                <i class="bi bi-check-circle-fill text-success"></i> Package has been delivered successfully
                            @elseif($dataStatus === 'cancelled')
                                <i class="bi bi-x-circle-fill text-danger"></i> This order was cancelled
                            @else
                                <i class="bi bi-truck text-primary"></i> Est. Delivery by: <strong>{{ \Carbon\Carbon::parse($order->sale_date ?? now())->addDays(4)->format('d M Y') }}</strong>
                            @endif
                        </div>

                        <div class="order-action-btns">
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

<script>
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
                const status = card.dataset.status;
                const searchCorpus = card.dataset.search || '';

                const matchesStatus = (activeFilter === 'all' || status === activeFilter);
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
    });
</script>
@endsection
