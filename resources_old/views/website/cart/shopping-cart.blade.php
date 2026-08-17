@extends('website.layout.master')
@section('content')

<!-- Google Fonts Import (Plus Jakarta Sans) & Bootstrap Icons -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Custom Production CSS for Cart UI -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --cart-primary: #00b9b9;
        --cart-primary-dark: #008b8b;
        --cart-primary-light: #e6f8f8;
        --cart-success: #10b981;
        --cart-success-light: #ecfdf5;
        --cart-warning: #d97706;
        --cart-warning-light: #fffbeb;
        --cart-text-main: #0f172a;
        --cart-text-muted: #64748b;
        --cart-border-color: #e2e8f0;
        --cart-card-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
        --cart-card-shadow-hover: 0 20px 35px -5px rgba(0, 185, 185, 0.12), 0 8px 16px -4px rgba(15, 23, 42, 0.04);
    }

    /* Force modern Plus Jakarta Sans typography across entire cart view */
    .cart-breadcrumbs,
    .shopping-cart-page,
    .shopping-cart-page *,
    .cart-breadcrumbs * {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }

    body {
        background-color: #f8fafc;
        color: var(--cart-text-main);
    }

    /* Breadcrumbs */
    .cart-breadcrumbs {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }
    .cart-breadcrumbs a {
        color: var(--cart-text-muted);
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: color 0.2s ease;
    }
    .cart-breadcrumbs a:hover {
        color: var(--cart-primary);
    }
    .cart-breadcrumbs .breadcrumb-item.active {
        color: var(--cart-text-main);
        font-weight: 600;
        font-size: 14px;
    }

    /* Modern Card Architecture */
    .cart-card {
        background: #ffffff;
        border: 1px solid var(--cart-border-color);
        border-radius: 16px;
        box-shadow: var(--cart-card-shadow);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    /* Cart Item Row */
    .cart-item-card {
        background: #ffffff;
        border: 1px solid var(--cart-border-color);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        position: relative;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .cart-item-card:hover {
        box-shadow: var(--cart-card-shadow-hover);
        border-color: #cbd5e1;
    }

    /* FREE Ribbon Badge */
    .free-ribbon-badge {
        position: absolute;
        top: 0;
        left: 0;
        width: 85px;
        height: 85px;
        overflow: hidden;
        z-index: 2;
        pointer-events: none;
    }
    .free-ribbon-badge span {
        transform: rotate(-45deg);
        position: absolute;
        top: 16px;
        left: -26px;
        width: 115px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        text-align: center;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 4px 0;
        box-shadow: 0 3px 6px rgba(16, 185, 129, 0.3);
    }

    /* Product Image Container */
    .cart-img-wrapper {
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 110px;
        border: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
    }
    .cart-img-wrapper img {
        max-height: 85px;
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    .cart-item-card:hover .cart-img-wrapper img {
        transform: scale(1.04);
    }

    /* Brand Tag & Title */
    .brand-badge {
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .item-title {
        color: var(--cart-text-main);
        font-size: 16px;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: -0.2px;
    }

    /* Lens Details Card */
    .lens-package-box {
        background: linear-gradient(135deg, #f0fdfa 0%, #e6f8f8 100%);
        border: 1px dashed var(--cart-primary);
        border-radius: 12px;
        padding: 10px 14px;
        margin-top: 10px;
    }

    /* Prescription Power Box */
    .rx-box {
        background: #ffffff;
        border: 1px solid #e0f2f1;
        border-radius: 12px;
        margin-top: 12px;
        overflow: hidden;
    }
    .rx-box-header {
        background: #f4fbfb;
        padding: 10px 14px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rx-table {
        margin: 0;
        font-size: 12px;
        width: 100%;
    }
    .rx-table th {
        background: #e6f8f8;
        color: var(--cart-text-main);
        font-weight: 700;
        text-align: center;
        padding: 6px 8px;
        border: none;
    }
    .rx-table td {
        padding: 6px 8px;
        text-align: center;
        border-top: 1px solid #f1f5f9;
    }

    /* Quantity Stepper Control */
    .qty-stepper {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 30px;
        padding: 3px 6px;
        border: 1px solid #e2e8f0;
    }
    .qty-stepper button {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: #ffffff;
        color: var(--cart-text-main);
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .qty-stepper button:hover {
        background: var(--cart-primary);
        color: #ffffff;
    }
    .qty-stepper .item-qty {
        font-weight: 700;
        font-size: 14px;
        min-width: 28px;
        text-align: center;
        color: var(--cart-text-main);
    }

    /* Delete Button */
    .btn-remove-item {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid #fee2e2;
        background: #fff5f5;
        color: #ef4444;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-remove-item:hover {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
        transform: scale(1.05);
    }

    /* Sticky Summary Column */
    .sticky-summary {
        position: sticky;
        top: 24px;
    }

    /* Primary CTA Button */
    .btn-cart-primary {
        background: linear-gradient(135deg, var(--cart-primary) 0%, var(--cart-primary-dark) 100%);
        color: #ffffff;
        font-weight: 700;
        border: none;
        border-radius: 12px;
        padding: 14px 24px;
        box-shadow: 0 8px 20px -4px rgba(0, 185, 185, 0.4);
        transition: all 0.25s ease;
    }
    .btn-cart-primary:hover {
        background: linear-gradient(135deg, #02c7c7 0%, #007777 100%);
        color: #ffffff;
        box-shadow: 0 12px 25px -4px rgba(0, 185, 185, 0.5);
        transform: translateY(-2px);
    }

    /* Coupon Input */
    .coupon-input-group .form-control {
        border-radius: 10px 0 0 10px;
        border: 1px solid var(--cart-border-color);
        padding: 12px 16px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .coupon-input-group .form-control:focus {
        border-color: var(--cart-primary);
        box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.15);
    }
    .coupon-input-group .btn-apply {
        border-radius: 0 10px 10px 0;
        background: var(--cart-primary);
        color: #ffffff;
        font-weight: 700;
        padding: 12px 20px;
        border: none;
        transition: background 0.2s ease;
    }
    .coupon-input-group .btn-apply:hover {
        background: var(--cart-primary-dark);
    }

    /* Gift Voucher Card */
    .voucher-input-group .form-control {
        border-radius: 10px 0 0 10px;
        border: 1px solid var(--cart-border-color);
        padding: 12px 16px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .voucher-input-group .form-control:focus {
        border-color: var(--cart-primary);
        box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.15);
    }
    .voucher-input-group .btn-apply {
        border-radius: 0 10px 10px 0;
        background: var(--cart-primary);
        color: #ffffff;
        font-weight: 700;
        padding: 12px 20px;
        border: none;
        transition: background 0.2s ease;
    }
    .voucher-input-group .btn-apply:hover {
        background: var(--cart-primary-dark);
    }
    .saved-voucher-chip {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .saved-voucher-chip:hover {
        border-color: var(--cart-primary);
        background: var(--cart-primary-light);
    }

    /* Gold Membership Banner */
    .gold-banner-state1 {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #ffffff 100%);
        border: 1.5px solid #f59e0b !important;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }
    .gold-banner-state1::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 150px;
        height: 150px;
        background: rgba(245, 158, 11, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }
</style>

<!-- Breadcrumbs Section -->
<section class="cart-breadcrumbs py-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products') }}">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Main Shopping Cart Section -->
<section class="shopping-cart-page py-5">
    <div class="container">
        @if(empty($cartData['items']) || count($cartData['items']) == 0)
            <!-- Empty Cart State -->
            <div class="cart-card p-5 text-center my-4 mx-auto" style="max-width: 600px;">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px; background: #e6f8f8;">
                        <i class="bi bi-cart-x fs-1" style="color: var(--cart-primary);"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-2" style="color: var(--cart-text-main);">Your Cart is Currently Empty</h3>
                <p class="text-secondary mb-4" style="font-size: 15px;">Looks like you haven't added any prescription glasses or sunglasses yet.</p>
                <a href="{{ route('products') }}" class="btn btn-cart-primary text-white text-decoration-none px-4 py-3 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-bag-plus me-2 fs-5"></i> Explore Eyewear Catalog
                </a>
            </div>
        @else
            <!-- Active Cart Layout -->
            <div class="row g-4">
                <!-- Left Column: Cart Items -->
                <div class="col-lg-7 col-xl-8">
                    <!-- Header Card -->
                    <div class="cart-card p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 fw-bold" style="color: var(--cart-text-main);">
                                    Shopping Cart <span class="badge bg-light text-dark border ms-2 rounded-pill fw-semibold" style="font-size: 13px; padding: 6px 12px;">{{ $cartData['item_count'] }} {{ Str::plural('item', $cartData['item_count']) }}</span>
                                </h4>
                                <span class="text-secondary small">Review your frames, lens packages, and powers</span>
                            </div>
                            @if($cartData['is_bogo_active'])
                                <span class="badge px-3 py-2 rounded-pill fw-semibold shadow-xs d-inline-flex align-items-center" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; font-size: 12px;">
                                    <i class="bi bi-award-fill me-1"></i> BOGO Promo Active
                                </span>
                            @endif
                        </div>

                        @if(!empty($cartData['bogo_fallback_message']))
                            <div class="alert alert-warning py-2 px-3 small mt-3 mb-0 border-0 rounded-3 d-flex align-items-center shadow-xs" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a !important;">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i> {{ Str::replace(['⚠️', '👑', '★'], '', $cartData['bogo_fallback_message']) }}
                            </div>
                        @endif
                    </div>

                    <!-- Items List -->
                    <div class="cart-items-wrapper">
                        @foreach($cartData['items'] as $item)
                            <div class="cart-item-card data-key-item" data-key="{{ $item['key'] }}">
                                @if(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                    <!-- Green FREE Ribbon Badge -->
                                    <div class="free-ribbon-badge">
                                        <span>FREE</span>
                                    </div>
                                @endif

                                <div class="row align-items-start g-3">
                                    <!-- Image Column -->
                                    <div class="col-sm-4 col-md-3">
                                        @if(isset($item['is_membership']) && $item['is_membership'])
                                            <div class="cart-img-wrapper" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a;">
                                                @if(!empty($item['frame_image']) && (Str::startsWith($item['frame_image'], 'http') || Str::startsWith($item['frame_image'], '/')))
                                                    <img src="{{ $item['frame_image'] }}" alt="{{ $item['frame_name'] }}" class="img-fluid" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                                    <div class="d-none align-items-center justify-content-center w-100 h-100">
                                                        <i class="bi bi-award-fill text-warning fs-1"></i>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center w-100 h-100">
                                                        <i class="bi bi-award-fill text-warning fs-1"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="cart-img-wrapper">
                                                <img src="{{ $item['frame_image'] }}" alt="{{ $item['frame_name'] }}" class="img-fluid">
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Content Column -->
                                    <div class="col-sm-8 col-md-6">
                                        @if(isset($item['is_membership']) && $item['is_membership'])
                                            <!-- Membership Item View -->
                                            <div class="mb-1">
                                                <span class="badge bg-warning text-dark font-weight-bold px-2 py-1 mb-1 d-inline-flex align-items-center" style="font-size: 10px; border-radius: 4px;">
                                                    <i class="bi bi-award-fill me-1"></i> GOLD VIP
                                                </span>
                                                <h5 class="item-title mb-1">{{ $item['frame_name'] }}</h5>
                                            </div>
                                            <p class="text-secondary small mb-2" style="font-size: 13px; line-height: 1.4;">
                                                Buy 1 Get 1 Free On Over 5000+ Items, Applicable Everywhere for 1 Full Year
                                            </p>
                                            
                                            <div class="d-flex align-items-center gap-3 mt-2">
                                                <form action="{{ route('cart.remove_membership') }}" method="POST" class="d-inline m-0 p-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link p-0 text-decoration-none border-0 fw-semibold text-danger d-inline-flex align-items-center" style="font-size: 13px;">
                                                        <i class="bi bi-trash3 me-1"></i>Remove
                                                    </button>
                                                </form>
                                                <span class="text-muted opacity-50">|</span>
                                                <a href="{{ route('website.membership') }}" class="fw-semibold text-decoration-none d-inline-flex align-items-center" style="font-size: 13px; color: var(--cart-primary);">
                                                    View Benefits <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        @else
                                            <!-- Regular Eyewear Item View -->
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                @if(!empty($item['brand']))
                                                    <span class="brand-badge">{{ $item['brand'] }}</span>
                                                @endif
                                                @if(!empty($item['size']))
                                                    <span class="badge bg-light text-secondary border fw-normal" style="font-size: 11px;">Size: {{ $item['size'] }}</span>
                                                @endif
                                            </div>

                                            <h5 class="item-title mb-1">{{ $item['frame_name'] }}</h5>

                                            <!-- Lens Package Info Card -->
                                            <div class="lens-package-box">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small fw-bold text-dark d-flex align-items-center">
                                                        <i class="bi bi-layers-fill me-1 text-teal" style="color: var(--cart-primary);"></i> Lens: {{ $item['lens_name'] }}
                                                    </span>
                                                    <span class="small fw-bold" style="color: var(--cart-success);">
                                                        {{ $item['lens_price'] > 0 ? '+₹' . number_format($item['lens_price'], 2) : 'Included (₹0)' }}
                                                    </span>
                                                </div>
                                                @if(!empty($item['lens_details']))
                                                    <div class="small text-secondary mt-1" style="font-size: 11px; line-height: 1.3;">
                                                        {{ Str::limit($item['lens_details'], 70) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Prescription Power Accordion Box -->
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
                                                <div class="rx-box">
                                                    <div class="rx-box-header" data-bs-toggle="collapse" data-bs-target="#rx-details-{{ $loop->index }}" aria-expanded="true">
                                                        <span class="small fw-bold text-dark d-flex align-items-center">
                                                            <i class="bi bi-eye-fill me-2" style="color: var(--cart-primary);"></i>
                                                            Prescription Details
                                                        </span>
                                                        <span class="small fw-semibold d-flex align-items-center" style="color: var(--cart-primary); font-size: 12px;">
                                                            View Power Matrix <i class="bi bi-chevron-down ms-1"></i>
                                                        </span>
                                                    </div>

                                                    <div class="collapse show" id="rx-details-{{ $loop->index }}">
                                                        <div class="p-2 bg-white">
                                                            @if(isset($rx['type']) && $rx['type'] === 'upload')
                                                                <div class="d-flex align-items-center p-2 rounded border" style="background: #f8fafc;">
                                                                    <i class="bi bi-file-earmark-medical-fill fs-4 text-primary me-2"></i>
                                                                    <div class="small">
                                                                        <div class="fw-semibold text-dark">Uploaded Doctor Prescription</div>
                                                                        @if(!empty($rx['file']))
                                                                            <a href="{{ asset($rx['file']) }}" target="_blank" class="text-primary text-decoration-underline small">
                                                                                <i class="bi bi-box-arrow-up-right me-1"></i>View Uploaded File
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="table-responsive">
                                                                    <table class="table rx-table align-middle">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>EYE</th>
                                                                                <th>SPH</th>
                                                                                <th>CYL</th>
                                                                                <th>AXIS</th>
                                                                                <th>ADD</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td class="fw-bold text-secondary">R (Right)</td>
                                                                                <td class="fw-bold text-dark">{{ !empty($rx['right_eye_sph']) ? $rx['right_eye_sph'] : '-' }}</td>
                                                                                <td class="text-muted">{{ !empty($rx['right_eye_cyl']) ? $rx['right_eye_cyl'] : '-' }}</td>
                                                                                <td class="text-muted">{{ !empty($rx['right_eye_axis']) && $rx['right_eye_axis'] != '0' ? $rx['right_eye_axis'] : '-' }}</td>
                                                                                <td class="text-muted">{{ !empty($rx['right_eye_ap']) ? $rx['right_eye_ap'] : '-' }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="fw-bold text-secondary">L (Left)</td>
                                                                                <td class="fw-bold text-dark">{{ !empty($rx['left_eye_sph']) ? $rx['left_eye_sph'] : '-' }}</td>
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
                                                </div>
                                            @endif

                                            <!-- Promo Badges -->
                                            @if(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                                <div class="mt-2 text-success small fw-bold d-inline-flex align-items-center" style="font-size: 12px; background: #ecfdf5; padding: 6px 12px; border-radius: 8px; border: 1px solid #a7f3d0;">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Free with Gold Membership!
                                                </div>
                                            @elseif(isset($item['is_bogo_third_discount']) && $item['is_bogo_third_discount'])
                                                <div class="mt-2 text-primary small fw-bold d-inline-flex align-items-center" style="font-size: 12px; background: #eff6ff; padding: 6px 12px; border-radius: 8px; border: 1px solid #bfdbfe; color: #1d4ed8;">
                                                    <i class="bi bi-percent me-1"></i> {{ (int)($item['bogo_third_discount_percent'] ?? 60) }}% OFF on 3rd Pair (Gold Bonus)
                                                </div>
                                            @elseif(isset($item['is_bogo_half']) && $item['is_bogo_half'])
                                                <div class="mt-2">
                                                    <span class="badge bg-warning text-dark px-2 py-1 fw-semibold d-inline-flex align-items-center">
                                                        <i class="bi bi-percent me-1"></i> Frame 50% OFF (BOGO Fallback)
                                                    </span>
                                                </div>
                                            @elseif(isset($item['is_first_frame_free_applied']) && $item['is_first_frame_free_applied'])
                                                <div class="mt-2">
                                                    <span class="badge bg-success text-white px-2 py-1 fw-semibold d-inline-flex align-items-center">
                                                        <i class="bi bi-gift-fill me-1"></i> Frame Free (First Pair Free Promo)
                                                    </span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Price & Actions Column -->
                                    <div class="col-md-3 text-md-end d-flex flex-column justify-content-between align-items-md-end h-100">
                                        <!-- Price Breakdown -->
                                        <div class="mb-3">
                                            @if(isset($item['is_membership']) && $item['is_membership'])
                                                <div class="text-decoration-line-through text-muted small">₹{{ number_format($item['membership_mrp'] ?? 6000, 0) }}</div>
                                                <div class="fw-bold fs-4 text-dark">₹{{ number_format($item['frame_price'], 0) }}</div>
                                            @elseif(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                                <div class="text-decoration-line-through text-muted small">₹{{ number_format($item['frame_price'], 2) }}</div>
                                                <div class="fw-bold fs-4 text-success">₹{{ number_format($item['lens_price'], 2) }}</div>
                                            @elseif(isset($item['is_bogo_third_discount']) && $item['is_bogo_third_discount'])
                                                @php 
                                                    $pct = (float)($item['bogo_third_discount_percent'] ?? 60);
                                                    $discountedFramePrice = $item['frame_price'] * (1 - ($pct / 100));
                                                @endphp
                                                <div class="text-decoration-line-through text-muted small">₹{{ number_format($item['frame_price'], 2) }}</div>
                                                <div class="fw-bold fs-4 text-primary">₹{{ number_format($discountedFramePrice + $item['lens_price'], 2) }}</div>
                                            @elseif(isset($item['is_bogo_half']) && $item['is_bogo_half'])
                                                <div class="text-decoration-line-through text-muted small">₹{{ number_format($item['frame_price'], 2) }}</div>
                                                <div class="fw-bold fs-4 text-success">₹{{ number_format(($item['frame_price'] * 0.5) + $item['lens_price'], 2) }}</div>
                                            @elseif(isset($item['is_first_frame_free_applied']) && $item['is_first_frame_free_applied'])
                                                <div class="text-decoration-line-through text-muted small">₹{{ number_format($item['frame_price'], 2) }}</div>
                                                <div class="fw-bold fs-4 text-success">₹{{ number_format($item['lens_price'], 2) }}</div>
                                            @else
                                                <div class="fw-bold fs-4 text-dark">₹{{ number_format($item['frame_price'] + $item['lens_price'], 2) }}</div>
                                            @endif
                                        </div>

                                        <!-- Quantity / Actions -->
                                        <div class="d-flex align-items-center justify-content-md-end gap-2">
                                            @if(isset($item['is_membership']) && $item['is_membership'])
                                                <span class="badge bg-light text-success border px-3 py-2 fw-semibold rounded-pill d-inline-flex align-items-center">
                                                    <i class="bi bi-shield-check me-1"></i> Active Perk
                                                </span>
                                            @elseif(isset($item['is_bogo_free']) && $item['is_bogo_free'])
                                                <span class="badge bg-light text-success border border-success px-3 py-2 fw-bold rounded-pill d-inline-flex align-items-center" style="font-size:12px;">
                                                    <i class="bi bi-gift-fill me-1"></i> Qty: 1 (Free Pair)
                                                </span>
                                                <button type="button" class="btn-remove-item remove-cart-item ms-1" data-key="{{ $item['key'] }}" title="Remove item">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            @else
                                                <div class="qty-stepper">
                                                    <button type="button" class="qty-minus" data-key="{{ $item['key'] }}" title="Decrease">-</button>
                                                    <span class="item-qty">{{ $item['quantity'] }}</span>
                                                    <button type="button" class="qty-plus" data-key="{{ $item['key'] }}" title="Increase">+</button>
                                                </div>

                                                <button type="button" class="btn-remove-item remove-cart-item" data-key="{{ $item['key'] }}" title="Remove item">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Column: Coupon, Summary & Gold Banner -->
                <div class="col-lg-5 col-xl-4">
                        <!-- Dynamic Gift Voucher Perk Banner -->
                        @if(!empty($cartData['gift_voucher_perk']))
                            @php
                                $perk         = $cartData['gift_voucher_perk'];
                                $perkIsManual = ($perk['delivery_type'] ?? 'auto') === 'manual';
                                $perkIsAuto   = !$perkIsManual;
                            @endphp
                            <div class="cart-card p-3 mb-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #f3f0fd, #e9e3fb); border: 1.5px solid #6b4bcf !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: #6b4bcf; color: #fff; font-size: 20px;">
                                        <i class="bi bi-gift-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="font-size: 13.5px; color: #4c28a8;">
                                            {{ $perk['name'] }}
                                        </div>
                                        <div class="small text-muted mt-1" style="font-size: 12px; line-height: 1.5;">
                                            @if($perkIsAuto)
                                                {{-- Auto-linked: teaser only, no code needed --}}
                                                Complete this order to unlock a <strong>₹{{ number_format($perk['voucher_value']) }} Gift Voucher</strong> on your next order
                                                <span class="text-muted">(valid {{ $perk['validity_days'] }} days after issue)</span>.
                                            @else
                                                {{-- Manual: they have a code, can apply now --}}
                                                @if(!empty($perk['description']))
                                                    {{ $perk['description'] }}
                                                @else
                                                    Apply code <strong>{{ $perk['coupon_code'] }}</strong> to get
                                                    <strong>₹{{ number_format($perk['voucher_value']) }} off</strong> on this order.
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Only manual-type perks show an Apply button on the teaser --}}
                                    @if($perkIsManual && !empty($perk['coupon_code']))
                                        <button type="button"
                                                class="btn btn-sm apply-quick-voucher fw-bold px-3 py-1 flex-shrink-0"
                                                data-code="{{ $perk['coupon_code'] }}"
                                                style="background:#6b4bcf;color:#fff;font-size:11px;border-radius:6px;white-space:nowrap;">
                                            Apply
                                        </button>
                                    @endif
                                </div>
                                @if($perkIsAuto)
                                    <div class="mt-2 pt-2 border-top" style="border-color:#c8b7f0 !important;">
                                        <span style="font-size:11px; color:#6b4bcf;">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Voucher will be <strong>auto-credited</strong> to your account — no code needed.
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif


                        <!-- Coupon Code Card -->
                        <div class="cart-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center">
                                <i class="bi bi-ticket-perforated-fill me-2" style="color: var(--cart-primary);"></i> Apply Coupon Code
                            </h5>
                            <div class="input-group coupon-input-group mb-2">
                                <input type="text" id="coupon-code-input" class="form-control text-uppercase" placeholder="ENTER COUPON CODE" value="{{ session('applied_coupon.code', '') }}">
                                <button id="apply-coupon-btn" class="btn btn-apply" type="button">APPLY</button>
                            </div>

                            @if(session()->has('applied_coupon'))
                                <div class="alert alert-success py-2 px-3 small mb-0 d-flex justify-content-between align-items-center border-0 rounded-3" style="background: var(--cart-success-light); color: var(--cart-success);">
                                    <span><i class="bi bi-check-circle-fill me-1"></i> Coupon <strong>{{ session('applied_coupon.code') }}</strong> applied!</span>
                                    <button type="button" id="remove-coupon-btn" class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none" style="font-size:12px;">Remove</button>
                                </div>
                            @endif

                            @php $availableCoupons = $cartData['available_coupons'] ?? []; @endphp
                            @if(!empty($availableCoupons) && !session()->has('applied_coupon'))
                                <div class="mt-3 pt-2 border-top">
                                    <div class="small fw-semibold text-muted mb-2 d-flex align-items-center">
                                        <i class="bi bi-stars me-1 text-warning"></i> Available Offers & Coupons:
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($availableCoupons as $ac)
                                            <button type="button" class="btn border p-2 text-start apply-quick-coupon w-100 rounded-3" data-code="{{ $ac['code'] }}" style="background: #f8fafc; border: 1.5px dashed #cbd5e1 !important; transition: all 0.2s ease;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold text-primary text-uppercase me-2" style="font-size: 13px;">
                                                        <i class="bi bi-ticket-fill me-1"></i>{{ $ac['code'] }}
                                                    </span>
                                                    <span class="badge bg-primary text-white px-2 py-1" style="font-size: 10px;">{{ $ac['title'] }}</span>
                                                </div>
                                                @if(!empty($ac['description']))
                                                    <div class="text-secondary small" style="font-size: 11px; line-height: 1.3;">{{ Str::limit($ac['description'], 50) }}</div>
                                                @endif
                                                @if(!empty($ac['min_cart_amount']) && $ac['min_cart_amount'] > 0)
                                                    <div class="text-muted small mt-1" style="font-size: 10px;">Min Cart: ₹{{ number_format($ac['min_cart_amount']) }}</div>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Gift Voucher Card -->
                        @php
                            $appliedVoucher = $cartData['applied_voucher'] ?? null;
                            $savedVouchers  = $cartData['available_vouchers'] ?? [];
                            // Separate: auto-linked (no code) vs manual-code vouchers
                            $autoVouchers   = array_filter($savedVouchers, fn($v) => empty($v['code']) || ($v['is_auto'] ?? false));
                            $manualVouchers = array_filter($savedVouchers, fn($v) => !empty($v['code']) && !($v['is_auto'] ?? false));
                        @endphp

                        <div class="cart-card p-4 mb-4">

                            {{-- ── Applied Voucher Success State ── --}}
                            @if($appliedVoucher)
                                <h5 class="fw-bold mb-3 text-dark d-flex align-items-center">
                                    <i class="bi bi-gift-fill me-2" style="color:#6b4bcf;"></i> Gift Voucher
                                </h5>
                                <div class="alert py-2 px-3 small mb-0 d-flex justify-content-between align-items-center border-0 rounded-3"
                                     style="background:#eee9fd; color:#4c28a8;">
                                    <span>
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Voucher <strong>{{ $appliedVoucher['code'] }}</strong> applied
                                        — <strong>₹{{ number_format($appliedVoucher['amount_applied'], 2) }} off</strong>
                                        @if(($appliedVoucher['remaining_balance'] ?? 0) > 0)
                                            <br><span class="text-muted" style="font-size:11px;">₹{{ number_format($appliedVoucher['remaining_balance'], 2) }} balance saved for your next order</span>
                                        @endif
                                    </span>
                                    <button type="button" id="remove-voucher-btn"
                                            class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none"
                                            style="font-size:12px;">Remove</button>
                                </div>

                            @else
                                {{-- ── Saved / Auto-linked Vouchers (1-click apply) ── --}}
                                @if(!empty($savedVouchers))
                                    <div class="mb-3">
                                        <div class="small fw-semibold mb-2 d-flex align-items-center" style="color:#4c28a8;">
                                            <i class="bi bi-wallet2 me-1"></i> Your Gift Vouchers
                                        </div>
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($savedVouchers as $v)
                                                <button type="button"
                                                        class="btn w-100 text-start apply-quick-voucher"
                                                        data-code="{{ $v['code'] }}"
                                                        style="background:#f3f0fd;border:1.5px solid #c8b7f0;border-radius:10px;padding:10px 14px;transition:all .2s;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fw-bold" style="font-size:13px;color:#4c28a8;">
                                                            <i class="bi bi-ticket-fill me-1"></i>{{ $v['code'] }}
                                                        </span>
                                                        <span class="fw-bold" style="font-size:13px;color:#6b4bcf;">
                                                            ₹{{ number_format($v['balance'], 2) }}
                                                            <span class="badge ms-1" style="background:#6b4bcf;color:#fff;font-size:9px;border-radius:4px;">APPLY</span>
                                                        </span>
                                                    </div>
                                                    @if(!empty($v['expires_at']))
                                                        <div class="text-muted mt-1" style="font-size:10px;">
                                                            <i class="bi bi-clock me-1"></i>Expires {{ $v['expires_at'] }}
                                                        </div>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="border-top pt-3 mt-1" style="border-color:#e3dbf7 !important;">
                                @endif

                                {{-- ── Manual Code Entry (purchased / gifted vouchers) ── --}}
                                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center" style="font-size:14px;">
                                    <i class="bi bi-gift-fill me-2" style="color:var(--cart-primary);"></i>
                                    Have a Voucher Code?
                                </h5>
                                <p class="text-muted mb-2" style="font-size:11.5px;">
                                    Enter a purchased or gifted voucher code below.
                                </p>
                                <div class="input-group voucher-input-group mb-0">
                                    <input type="text" id="voucher-code-input"
                                           class="form-control text-uppercase"
                                           placeholder="e.g. GV-8X92KP"
                                           value="">
                                    <button id="apply-voucher-btn" class="btn btn-apply" type="button">APPLY</button>
                                </div>

                                @if(!empty($savedVouchers))
                                    </div>{{-- close border-top wrapper --}}
                                @endif

                            @endif
                        </div>


                        <!-- Loyalty Points Card -->
                        @php
                            $userBal       = (int)($cartData['available_loyalty_points'] ?? 0);
                            $orderReward   = (int)($cartData['order_reward_pts'] ?? 0);
                            $deliveryDate  = $cartData['cashback_release_date'] ?? '';
                            $useLoyalty    = !empty($cartData['use_loyalty_points']);
                            $ptsUsed       = (int)($cartData['points_used'] ?? 0);
                            $pointVal      = (float)($cartData['point_value'] ?? 1.0);
                            $loyaltyRupees = $useLoyalty ? ($cartData['loyalty_discount'] ?? 0) : ($userBal * $pointVal);
                        @endphp
                        <div class="cart-card p-4 mb-4" style="background: #ffffff; border: 1px solid var(--cart-border-color); border-radius: 16px;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-bold text-dark d-flex align-items-center" style="font-size: 14.5px;">
                                    <i class="bi bi-stars me-2" style="color: var(--cart-primary); font-size: 18px;"></i> Your Loyalty Points:
                                </div>
                                <span class="fw-extrabold fs-5" style="color: var(--cart-primary);">{{ number_format($userBal) }} pts</span>
                            </div>

                            <div class="small text-muted mb-3 d-flex align-items-center" style="font-size: 12px; line-height: 1.4;">
                                <i class="bi bi-gift-fill me-1 text-success"></i> Earn <strong>{{ number_format($orderReward) }} pts</strong> after delivery · credited by {{ $deliveryDate }}
                            </div>

                            @if($userBal > 0)
                                <div class="pt-2 border-top">
                                    <div class="form-check d-flex align-items-center mb-0">
                                        <input class="form-check-input me-2" type="checkbox" id="toggle-loyalty-checkbox" {{ $useLoyalty ? 'checked' : '' }} style="cursor: pointer; width: 18px; height: 18px; accent-color: var(--cart-primary);">
                                        <label class="form-check-label fw-bold text-dark" for="toggle-loyalty-checkbox" style="cursor: pointer; font-size: 13.5px;">
                                            Use {{ number_format($useLoyalty && $ptsUsed > 0 ? $ptsUsed : $userBal) }} pts <span class="text-success">(−₹{{ number_format($loyaltyRupees, 2) }})</span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <div class="pt-2 border-top">
                                    <span class="text-muted small d-inline-flex align-items-center" style="font-size: 12px;">
                                        <i class="bi bi-info-circle me-1 text-secondary"></i> Nothing to redeem yet — this order gets you started
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Bill Details Summary Card -->
                        <div class="cart-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">Bill Summary</h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary">Frame Subtotal</span>
                                <span class="fw-semibold text-dark">₹{{ number_format($cartData['frame_subtotal'], 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary">Lens Package Subtotal</span>
                                <span class="fw-semibold text-success">+₹{{ number_format($cartData['lens_subtotal'], 2) }}</span>
                            </div>

                            @if($cartData['bogo_savings'] > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success fw-semibold"><i class="bi bi-tag-fill me-1"></i> BOGO Free Pair Savings</span>
                                    <span class="fw-bold text-success">-₹{{ number_format($cartData['bogo_savings'], 2) }}</span>
                                </div>
                            @endif

                            @if(isset($cartData['third_item_savings']) && $cartData['third_item_savings'] > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-primary fw-semibold"><i class="bi bi-percent me-1"></i> 3rd Pair ({{ (int)($cartData['bogo_extra_discount'] ?? 60) }}% OFF) Savings</span>
                                    <span class="fw-bold text-primary">-₹{{ number_format($cartData['third_item_savings'], 2) }}</span>
                                </div>
                            @endif

                            @if(isset($cartData['first_frame_free_save']) && $cartData['first_frame_free_save'] > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success fw-semibold"><i class="bi bi-gift-fill me-1"></i> First Pair Free Discount</span>
                                    <span class="fw-bold text-success">-₹{{ number_format($cartData['first_frame_free_save'], 2) }}</span>
                                </div>
                            @endif

                            @if($cartData['coupon_discount'] > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success fw-semibold"><i class="bi bi-percent me-1"></i> Coupon Discount</span>
                                    <span class="fw-bold text-success">-₹{{ number_format($cartData['coupon_discount'], 2) }}</span>
                                </div>
                            @endif

                            @if(isset($cartData['voucher_discount']) && $cartData['voucher_discount'] > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success fw-semibold"><i class="bi bi-gift-fill me-1"></i> Gift Voucher Applied</span>
                                    <span class="fw-bold text-success">-₹{{ number_format($cartData['voucher_discount'], 2) }}</span>
                                </div>
                            @endif

                            @if(isset($cartData['loyalty_discount']) && $cartData['loyalty_discount'] > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-warning fw-semibold" style="color: #d97706 !important;"><i class="bi bi-gem me-1"></i> Loyalty Points Discount</span>
                                    <span class="fw-bold text-warning" style="color: #d97706 !important;">-₹{{ number_format($cartData['loyalty_discount'], 2) }}</span>
                                </div>
                            @endif

                            <hr class="text-muted opacity-25 my-3">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <span class="fw-bold fs-5 text-dark d-block">Total Amount</span>
                                    <span class="text-muted small">Inclusive of all taxes</span>
                                </div>
                                <span class="fw-extrabold fs-3" style="color: var(--cart-primary);">₹{{ number_format($cartData['grand_total'], 2) }}</span>
                            </div>

                            @if(auth()->check())
                                <a href="{{ route('shipping-details') }}" class="btn btn-cart-primary w-100 py-3 d-flex align-items-center justify-content-center text-decoration-none">
                                    <span>Proceed to Checkout</span>
                                    <i class="bi bi-arrow-right fs-5 ms-2"></i>
                                </a>
                            @else
                                <button type="button" id="btn-proceed-checkout-auth" class="btn btn-cart-primary w-100 py-3 d-flex align-items-center justify-content-center text-decoration-none border-0">
                                    <span>Proceed to Checkout</span>
                                    <i class="bi bi-arrow-right fs-5 ms-2"></i>
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

                            <!-- Distinct Earned Loyalty Points Reward Banner below Proceed to Checkout -->
                            @if(isset($cartData['pending_cashback']) && $cartData['pending_cashback'] > 0)
                                <div class="p-3 rounded-3 mt-3 text-center" style="background:#ecfdf5; border: 1.5px dashed #10b981;">
                                    <div class="fw-bold text-success small d-flex align-items-center justify-content-center">
                                        <i class="bi bi-gift-fill me-1"></i> 🎉 Order Reward: Earn {{ number_format($cartData['pending_cashback'], 0) }} Loyalty Points!
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 11px;">
                                        Earn {{ (int)$cartData['cashback_percent'] }}% cashback ({{ number_format($cartData['pending_cashback'], 0) }} Pts) credited {{ $cartData['cashback_delay_days'] }} days post delivery ({{ $cartData['cashback_release_date'] }}).
                                    </div>
                                </div>
                            @endif

                            <!-- Trust Badges -->
                            <div class="mt-4 pt-3 border-top d-flex justify-content-around text-center text-secondary" style="font-size: 11px;">
                                <div>
                                    <i class="bi bi-shield-check fs-5 text-teal d-block mb-1" style="color: var(--cart-primary);"></i>
                                    <span>Secure Checkout</span>
                                </div>
                                <div>
                                    <i class="bi bi-truck fs-5 text-teal d-block mb-1" style="color: var(--cart-primary);"></i>
                                    <span>Free Shipping</span>
                                </div>
                                <div>
                                    <i class="bi bi-arrow-counterclockwise fs-5 text-teal d-block mb-1" style="color: var(--cart-primary);"></i>
                                    <span>Easy Returns</span>
                                </div>
                            </div>
                        </div>

                        <!-- Data-Driven 3-State Gold Banner Box -->
                        @php $bs = $cartData['banner_state'] ?? null; @endphp
                        @if($bs)
                            @if($bs['state'] == 1)
                                <!-- State 1: Membership NOT in cart -->
                                <div class="gold-banner-state1 p-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge px-3 py-1 text-white fw-bold d-inline-flex align-items-center" style="background-color: #d97706; border-radius: 20px; font-size: 11px;">
                                            <i class="bi bi-award-fill me-1"></i> GOLD MEMBERSHIP
                                        </span>
                                    </div>
                                    <div class="fw-bold text-dark mb-1" style="font-size: 15px; line-height: 1.4;">
                                        {{ Str::replace(['👑', '★', '⚠️'], '', $bs['title']) }}
                                    </div>
                                    <div class="small mb-3" style="font-size: 13.5px; color: #b45309; font-weight: 600;">
                                        {{ Str::replace(['👑', '★', '⚠️'], '', $bs['subtitle']) }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-3" style="border-top: 1px dashed #fde68a;">
                                        <button type="button" id="btn-add-membership" data-card-id="1"
                                            class="btn border-0 p-0 fw-bold text-decoration-none d-inline-flex align-items-center"
                                            style="font-size: 14px; color: #b45309; background: transparent;">
                                            <span id="btn-add-membership-text">{{ Str::replace(['👑', '★', '⚠️'], '', $bs['btn_text']) }}</span>
                                            <span id="btn-add-membership-spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                                        </button>
                                        <button type="button" id="btn-add-membership-arrow" data-card-id="1"
                                            class="btn btn-sm rounded-circle border-0 d-flex align-items-center justify-content-center text-white"
                                            style="width: 34px; height: 34px; background-color: #d97706; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3);">
                                            <i class="bi bi-arrow-right fs-6"></i>
                                        </button>
                                    </div>
                                </div>
                            @elseif($bs['state'] == 2)
                                <!-- State 2: Membership in cart, free item NOT yet chosen -->
                                <div class="cart-card p-4" style="background-color: #fff9db; border: 1.5px solid #ffd8a8 !important;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 15px;">
                                                <i class="bi bi-award-fill text-warning me-2 fs-5"></i> {{ Str::replace(['👑', '★', '⚠️'], '', $bs['title']) }}
                                            </h6>
                                            <p class="text-secondary mb-3 small" style="line-height: 1.4;">{{ Str::replace(['👑', '★', '⚠️'], '', $bs['subtitle']) }}</p>
                                            
                                            <a href="{{ $bs['cta_url'] }}" class="fw-bold text-decoration-none d-inline-flex align-items-center" style="color: #e8590c; font-size: 14px;">
                                                {{ Str::replace(['👑', '★', '⚠️'], '', $bs['btn_text']) }} <i class="bi bi-chevron-right ms-1"></i>
                                            </a>
                                        </div>
                                        <span class="text-muted" style="cursor: pointer;" title="Membership Privileges Active">
                                            <i class="bi bi-info-circle fs-5"></i>
                                        </span>
                                    </div>
                                </div>
                            @elseif($bs['state'] == 3)
                                <!-- State 3: Free item + 3rd pair discount applied (Lenskart-style Gold Max Membership Benefit Box) -->
                                <div class="cart-card p-4 mt-3" style="background-color: #fffbeb; border: 1.5px solid #fde68a !important; border-radius: 14px;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="pe-2">
                                            <h6 class="fw-bold mb-1" style="font-size: 14.5px; color: #1c1917;">
                                                {{ Str::replace(['👑', '★', '⚠️'], '', $bs['title']) }}
                                            </h6>
                                            <p class="mb-0 small" style="font-size: 12.5px; color: #78350f; line-height: 1.45;">
                                                {{ Str::replace(['👑', '★', '⚠️'], '', $bs['subtitle']) }}
                                            </p>
                                        </div>
                                        <span class="text-muted flex-shrink-0" style="cursor: pointer;" title="Gold Max Membership Benefit Active">
                                            <i class="bi bi-info-circle fs-5" style="color: #b45309;"></i>
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Cart AJAX Scripts -->
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
        $(document).on('click', '.remove-cart-item', function() {
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
                alert('Please enter a voucher code.');
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
                    alert(xhr.responseJSON ? xhr.responseJSON.message : 'Invalid or expired voucher code.');
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
                    alert(xhr.responseJSON ? xhr.responseJSON.message : 'Could not add membership. Please try again.');
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
                    alert('Failed to update loyalty points. Please try again.');
                }
            });
        });
    });
</script>

@endsection