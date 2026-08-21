@extends('website.layout.master')
@section('content')

    <style>
        .view-details {
            font-size: 12px;
            color: #00b9b9;
            cursor: pointer;
            display: inline-block;
            margin-top: 5px;
        }

        /* --- Desktop view (>= 768px) --- */
        @media (min-width: 768px) {
            .lens-sheet-modal .modal-dialog {
                max-width: 720px !important;
                margin: 1.75rem auto;
            }

            .lens-sheet-modal .modal-content {
                position: relative;
                transform: none !important;
                transition: none !important;
                height: 520px !important;
                border-radius: 24px !important;
                flex-direction: row !important;
                overflow: hidden !important;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
                border: none !important;
            }

            .lens-visual-pane {
                width: 44%;
                background: linear-gradient(135deg, #f0f7f7 0%, #e1eff0 100%);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                position: relative;
                padding: 24px;
                border-right: 1px solid rgba(0, 185, 185, 0.1);
            }

            .lens-details-pane {
                width: 56%;
                display: flex;
                flex-direction: column;
                height: 100%;
                background: #ffffff;
            }

            .sheet-handle {
                display: none;
            }

            .lens-content {
                flex-grow: 1;
                overflow-y: auto;
                padding: 30px !important;
                display: flex;
                flex-direction: column;
            }

            .sheet-close {
                position: absolute !important;
                top: 20px !important;
                right: 20px !important;
                background-color: rgba(255, 255, 255, 0.9);
                border: 1px solid rgba(0, 0, 0, 0.1);
                border-radius: 50%;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                transition: all 0.2s ease;
                z-index: 100;
                opacity: 0.8;
            }

            .sheet-close:hover {
                transform: scale(1.05);
                background-color: #ffffff;
                border-color: rgba(0, 185, 185, 0.3);
                opacity: 1;
            }
        }

        /* --- Mobile view (< 768px) --- */
        @media (max-width: 767.98px) {
            .lens-sheet-modal .modal-dialog {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                height: 100% !important;
            }

            .lens-sheet-modal .modal-content {
                position: absolute !important;
                bottom: 0 !important;
                width: 100% !important;
                height: 85vh !important;
                border: none !important;
                border-radius: 24px 24px 0 0 !important;
                overflow: hidden !important;
                transform: translateY(100%);
                transition: transform .35s ease;
                display: flex !important;
                flex-direction: column !important;
                box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.15) !important;
            }

            .lens-sheet-modal.show .modal-content {
                transform: translateY(0) !important;
            }

            .lens-visual-pane {
                width: 100%;
                height: 220px;
                background: linear-gradient(135deg, #f0f7f7 0%, #e1eff0 100%);
                position: relative;
            }

            .lens-details-pane {
                width: 100%;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                height: calc(85vh - 220px);
                background: #ffffff;
            }

            .sheet-handle {
                width: 50px;
                height: 5px;
                background: rgba(0, 0, 0, 0.15);
                border-radius: 10px;
                margin: 10px auto;
                position: absolute;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                z-index: 200;
            }

            .sheet-close {
                position: absolute !important;
                top: 15px !important;
                right: 15px !important;
                background-color: rgba(255, 255, 255, 0.9);
                border-radius: 50%;
                width: 30px;
                height: 30px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                z-index: 100;
            }

            .lens-content {
                flex-grow: 1;
                overflow-y: auto;
                padding: 20px !important;
            }
        }

        /* --- Shared visual styles --- */
        .lens-visual-pane .carousel {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .lens-visual-pane .carousel-inner {
            border-radius: 12px;
            overflow: hidden;
        }

        .lens-visual-pane .carousel-item img {
            width: 100%;
            height: 240px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        @media (max-width: 767.98px) {
            .lens-visual-pane .carousel-item img {
                height: 160px;
            }
        }

        .carousel-progress {
            display: flex;
            gap: 4px;
            padding: 10px 8px;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 5;
        }

        .progress-item {
            flex: 1;
            height: 4px;
            background: rgba(0, 185, 185, 0.15);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            display: block;
            width: 0%;
            height: 100%;
            background: #00b9b9;
        }

        .progress-item.active .progress-fill {
            animation: fillProgress 5s linear forwards;
        }

        @keyframes fillProgress {
            from {
                width: 0%;
            }

            to {
                width: 100%;
            }
        }

        .lens-content h3 {
            font-size: 24px;
            font-weight: 700;
            color: #0b1a30;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .lens-desc {
            font-size: 13.5px;
            color: #5d6d7e;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .lens-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 185, 185, 0.1);
        }

        .lens-price-group {
            display: flex;
            flex-direction: column;
        }

        .lens-price-group .price {
            font-size: 24px;
            font-weight: 800;
            color: #02045c;
            line-height: 1.1;
        }

        .lens-price-group del {
            font-size: 13.5px;
            color: #a0aec0;
            margin-top: 3px;
        }

        .lens-coupon-badge {
            background: rgba(0, 185, 185, 0.06);
            border: 1.5px dashed #00b9b9;
            padding: 6px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-size: 12.5px;
            color: #00b9b9;
        }

        .benefits-heading {
            font-size: 14px;
            font-weight: 700;
            color: #0b1a30;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex-grow: 1;
            margin-bottom: 1rem;
        }

        .features-heading {
            font-size: 14px;
            font-weight: 700;
            color: #0b1a30;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex-grow: 1;
        }

        .benefit-item-card {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fcfdfe;
            border: 1px solid rgba(0, 185, 185, 0.08);
            padding: 10px;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .benefit-item-card:hover {
            border-color: rgba(0, 185, 185, 0.2);
            box-shadow: 0 4px 12px rgba(0, 185, 185, 0.04);
            transform: translateY(-1px);
        }

        .benefit-img-wrapper {
            width: 100px;
            height: 67px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: #ffffff;
            border: 1px solid rgba(0, 185, 185, 0.12);
        }

        .benefit-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .benefit-info-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .benefit-title {
            font-size: 14.5px;
            font-weight: 700;
            color: #1b2a4a;
        }

        .benefit-desc {
            font-size: 11.5px;
            color: #718096;
            margin-top: 2px;
            line-height: 1.3;
        }

        .select-lens-btn {
            width: 100%;
            border: none;
            background: linear-gradient(135deg, #00b9b9 0%, #02045c 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 13px;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0, 185, 185, 0.2);
            transition: all 0.3s ease;
        }

        .select-lens-btn:hover {
            box-shadow: 0 6px 20px rgba(0, 185, 185, 0.3);
            transform: translateY(-2px);
            background: linear-gradient(135deg, #02045c 0%, #00b9b9 100%);
        }

        .select-lens-btn:active {
            transform: translateY(0);
        }

        /* ====== lens [Filters] ======= */
        .lens-filters {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: none;
        }

        .lens-filters::-webkit-scrollbar {
            display: none;
        }

        .lens-filter {
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #52557a;

            border-radius: 50px;
            padding: 10px 18px;

            font-size: 15px;
            font-weight: 500;

            white-space: nowrap;
            transition: all .3s ease;

            display: flex;
            align-items: center;
            gap: 6px;
        }

        .lens-filter:hover {
            border-color: #00b9b9;
            color: #00b9b9;
        }

        .lens-filter.active {
            background: #02045c;
            color: #fff;
            border-color: #02045c;
        }

        .lens-filter.active:hover {
            color: #fff;
        }

        .details-backdrop {
            background-color: rgb(61 61 61 / 50%) !important;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            opacity: 1 !important;
        }

        .forwardBtn {
            position: absolute;
            right: 8px;
            top: 8px;
        }

        .benefits-list li{
            font-size: 11px
        }
    </style>

    <section class="product breadcrumbs-section bg-white border-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8 col-9 pe-0">
                    <ul id="breadcrumbs" class="m-0 p-0 list-unstyled d-flex align-items-center gap-2"
                        style="font-size: 13px;">
                        <li><a href="index.html" class="text-muted text-decoration-none">Home</a></li>
                        <li><i class="bi bi-chevron-right text-muted" style="font-size: 10px;"></i></li>
                        <li><a href="products.html" class="text-muted text-decoration-none">Products</a></li>
                        <li><i class="bi bi-chevron-right text-muted" style="font-size: 10px;"></i></li>
                        <li>
                            <a class="fw-medium text-decoration-none">
                                {{ $categoryName }}
                            </a>
                        </li>
                        <li><i class="bi bi-chevron-right text-muted" style="font-size: 10px;"></i></li>
                        <li>
                            <a class="fw-medium text-decoration-none">
                                {{ $product->Company }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 col-3 ps-0">
                    <div class="right-section row align-items-center mt-md-0">
                        <div class="wishshre position-relative d-flex align-items-center">
                            <div class="wishlis btn-wishlist-toggle me-1" data-product-id="{{ $product->product_id ?: $product->id }}" data-wishlist-product-id="{{ $product->product_id ?: $product->id }}" style="cursor: pointer;">
                                <i class="bi {{ in_array($product->product_id ?: $product->id, $wishlistProductIds ?? []) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                            </div>

                            <div class="share position-relative">
                                <i class="bi bi-share"></i>
                                <div class="share-options">
                                    <a href="https://api.whatsapp.com/send?text=https%3A%2F%2Fexample.com%2Fproduct%2Fclassic-aviator-sunglasses" target="_blank"
                                        title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fexample.com%2Fproduct%2Fclassic-aviator-sunglasses"
                                        target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                                    <a href="https://twitter.com/intent/tweet?url=https%3A%2F%2Fexample.com%2Fproduct%2Fclassic-aviator-sunglasses" target="_blank"
                                        title="Twitter"><i class="bi bi-twitter-x"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- end first one -->


    <!-- start 2 360 degrees -->

    <section class="degree mt-4">
    <div class="container">
        <div class="row g-4">

            <!-- Left: Product Image & Thumbnails -->
            <div class="col-lg-6 col-12">

                <div class="product-box text-center">
                    <div class="main-img-area position-relative">
                        <a href="{{ $product->image_url }}" class="image-lightbox" id="main-image-link">
                        <img src="{{ $product->image_url }}"
                            class="main-image" id="main-image" alt="{{ $product->product_name }}">
                         </a>
                        <button class="btn-360">360 VIEW</button>
                    </div>
                </div>

                <div class="thumb-section d-flex align-items-center justify-content-center position-relative">
                    <button class="arrow-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="thumbs-container">
                        <div class="thumbs d-flex justify-content-center p-1" id="thumb-container">
                            @foreach($galleryImages as $index => $img)
                            <img src="{{ $img }}" class="thumb {{ $index == 0 ? 'active' : '' }}" alt="">
                            @endforeach
                        </div>
                    </div>
                    <button class="arrow-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>

            </div>

            <!-- Right: Product Info -->
            <div class="col-lg-6 col-12">

                <p class="semibold mb-1" id="product-company">{{ $product->Company }}</p>
                <h4 class="fbold mb-3" id="product-name">{{ $product->product_name }}</h4>

                @php
                    $p1 = (float)($product->Retail_Price ?? 0);
                    $p2 = (float)($product->Purchase_Price ?? 0);
                    $dPrice = (float)($product->discount_price ?? 0);

                    if ($dPrice > 0 && $p1 > 0 && $dPrice < $p1) {
                        $calcSellingPrice = $dPrice;
                        $calcMrp = $p1;
                    } else {
                        if ($p1 > 0 && $p2 > 0) {
                            $calcMrp = max($p1, $p2);
                            $calcSellingPrice = min($p1, $p2);
                        } else {
                            $calcMrp = max($p1, $p2);
                            $calcSellingPrice = $calcMrp;
                        }
                    }

                    $hasDiscount = ($calcMrp > $calcSellingPrice && $calcSellingPrice > 0);
                    $discountPercent = 0;
                    if ($hasDiscount) {
                        $discountPercent = round((($calcMrp - $calcSellingPrice) / $calcMrp) * 100);
                    }
                @endphp
                <!-- Price -->
                <div class="back">
                    <span class="fastrack" id="display-discount-price">
                        ₹{{ number_format($calcSellingPrice, 0) }}
                    </span><br>
                    @if($hasDiscount)
                    <span class="tmuted text-decoration-line-through ms-2" id="display-price">
                        ₹{{ number_format($calcMrp, 0) }}
                    </span>
                    <span class="tmuted" id="display-discount-percent">{{ $discountPercent }}% OFF</span>
                    @else
                    <span class="tmuted text-decoration-line-through ms-2 d-none" id="display-price"></span>
                    <span class="tmuted d-none" id="display-discount-percent"></span>
                    @endif
                </div>

                @php
                    $catLower  = strtolower($categoryName ?? '');
                    $typeLower = strtolower($product->product_type ?? '');
                    $isContactLens = str_contains($catLower, 'contact')
                                     || str_contains($typeLower, 'contact')
                                     || ($typeLower === 'lens')
                                     || !empty($product->Modality);
                    $isSolution = str_contains($catLower, 'solution') || ($typeLower === 'solution');
                    $isAccessory = str_contains($catLower, 'accessory') || ($typeLower === 'accessory') || ($typeLower === 'other');
                    $isSunglass = str_contains($catLower, 'sunglass') || ($typeLower === 'sunglass') || !empty($product->polarized);
                    $isFrame = !$isContactLens && !$isSolution && !$isAccessory;
                @endphp

                <!-- Size & Variant Options -->
                <div class="product-options mt-4">
                    @if(!empty($product->Size))
                    <div class="option-group mb-4 d-flex align-items-center flex-wrap gap-2">
                        <label class="option-label mb-0">
                            @if($isSolution)
                                Bottle Volume :
                            @elseif($isAccessory)
                                Pack Size :
                            @else
                                Frame Size :
                            @endif
                        </label>
                        <div class="size-options d-flex flex-wrap gap-2" id="size-options">
                            @php
                                // Size is stored as comma-separated string e.g. "Small,Medium,Large"
                                $sizeRaw   = $product->Size ?: 'Medium';
                                $sizeParts = array_values(array_filter(array_map('trim', explode(',', $sizeRaw))));
                                // Abbreviation map
                                $sizeMap = [
                                    'Extra Small'  => 'XS',
                                    'Small'        => 'S',
                                    'Medium'       => 'M',
                                    'Large'        => 'L',
                                    'Extra Large'  => 'XL',
                                    'XXL'          => 'XXL',
                                ];
                            @endphp
                            @foreach($sizeParts as $i => $sz)
                                @php
                                    $abbr = $sizeMap[$sz] ?? ($isSolution || $isAccessory ? $sz : strtoupper(substr($sz, 0, 1)));
                                @endphp
                                <button class="size-btn {{ $i === 0 ? 'active' : '' }}"
                                        data-size="{{ $sz }}"
                                        title="{{ $sz }}">
                                    {{ $abbr }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Colour -->
                    @if(!empty($colorVariants) && $colorVariants->count() > 0 && !$isSolution)
                    <div class="option-group d-flex align-items-center flex-wrap gap-2">
                        <label class="option-label mb-0">Colour :</label>
                        <div class="color-options d-flex flex-wrap gap-2 align-items-center" id="color-options-container">
                            @foreach($colorVariants as $variant)
                            @php
                                $isPrimary = $variant->id == $product->id;
                                $c1 = $variant->color_primary   ?? '#1a1a1a';
                                $c2 = $variant->color_secondary ?? null;
                                // Build background: split diagonal if dual-color
                                $bgStyle = $c2
                                    ? "background: linear-gradient(135deg, {$c1} 50%, {$c2} 50%);"
                                    : "background: {$c1};";
                            @endphp
                            <div class="color-swatch {{ $isPrimary ? 'active' : '' }}"
                                 style="{{ $bgStyle }}"
                                 data-variant-id="{{ $variant->id }}"
                                 data-variant-url="{{ $variant->detail_url }}"
                                 onclick="window.location.href='{{ $variant->detail_url }}'">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                @php
                    $supportedTypeIds = $product->supported_product_types ?? [];
                    if (is_string($supportedTypeIds)) {
                        $supportedTypeIds = json_decode($supportedTypeIds, true) ?? [];
                    }

                    if (!empty($supportedTypeIds) && $isFrame) {
                        $productTypes = \App\Models\ProductType::where('is_active', 1)
                            ->whereIn('id', $supportedTypeIds)
                            ->get();
                    } else {
                        $productTypes = collect([]);
                    }
                @endphp
                
                @if($productTypes->isNotEmpty())
                <!-- Product Type (Eyeglasses Only) -->
                <div class="product-type-section mt-4 overflow-hidden" id="product-type-section">
                    <p class="option-label mb-2">Product Type :</p>
                    <div class="product-type-tabs d-flex gap-2 p-1">
                        @foreach($productTypes as $index => $ptype)
                        <div class="ptype-tab {{ $index === 0 ? 'active' : '' }}"
                            data-type-id="{{ $ptype->id }}"
                            data-has-power="{{ $ptype->has_power ? '1' : '0' }}"
                            data-powers="{{ json_encode($ptype->default_powers ?: []) }}"
                            onclick="selectProductType(this)">
                            <span class="ptype-name">{{ $ptype->name }}</span>
                            <span class="ptype-sub">{{ $ptype->subtitle }}</span>
                        </div>
                        @endforeach
                    </div>

                    @foreach($productTypes as $index => $ptype)
                        @if(strtolower(trim($ptype->name)) !== 'powered eyeglass')
                        <div class="power-chips-wrap mt-3 {{ $index === 0 && strtolower(trim($productTypes[0]->name)) !== 'powered eyeglass' ? '' : 'd-none' }}" id="powers-{{ $ptype->id }}">
                            <p class="option-label mb-2">Select Power :</p>
                            <div class="d-flex flex-wrap gap-2">
                                @if(!empty($ptype->default_powers))
                                    @foreach($ptype->default_powers as $power)
                                    <button type="button" class="power-chip">{{ $power }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                @if($isContactLens)
                <!-- Power Type & Manual Power Selection Section (Only for Contact Lenses) -->
                <div class="power-type-section mt-4" id="power-type-section">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="option-label mb-0" style="color: #7d879c; font-size: 14px; font-weight: 500;">Power Type</span>
                        <div class="position-relative d-inline-block">
                            <button type="button" class="btn text-white rounded-pill px-3 py-1 fw-medium" style="background-color: #0d1430; font-size: 14px; border: none;">
                                With Power
                            </button>
                            <div style="width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid #0d1430; position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%);"></div>
                        </div>
                    </div>

                    <div class="p-3 rounded-4 mt-2" style="background-color: #eceff6;">
                        <div class="bg-white rounded-4 p-3 p-md-4 shadow-sm border border-light">
                            <!-- Option 1: Manual Power -->
                            <div class="power-option-group">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <label class="d-flex align-items-center gap-2 cursor-pointer mb-0">
                                        <input type="radio" name="power_submission" value="manual" checked style="width: 18px; height: 18px; accent-color: #0d1430;">
                                        <span class="fw-bold" style="color: #0d1430; font-size: 15px;">Enter power Manually</span>
                                    </label>

                                    <!-- Eye Checkboxes -->
                                    <div class="d-flex align-items-center gap-3">
                                        <label class="d-flex align-items-center gap-1 cursor-pointer mb-0" style="font-size: 13px; font-weight: 600; color: #0d1430;">
                                            <input type="checkbox" id="check-right" checked style="accent-color: #0d1430;"> RIGHT
                                        </label>
                                        <label class="d-flex align-items-center gap-1 cursor-pointer mb-0" style="font-size: 13px; font-weight: 600; color: #0d1430;">
                                            <input type="checkbox" id="check-left" checked style="accent-color: #0d1430;"> LEFT
                                        </label>
                                    </div>
                                </div>

                                <!-- Power Dropdowns Grid -->
                                <div class="bg-white rounded-3 p-3 mb-3" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                                    <div class="row g-3">
                                        <!-- Spherical (SPH) -->
                                        <div class="col-md-4 d-flex flex-column justify-content-center">
                                            <span class="fw-bold" style="color: #0d1430; font-size: 14px;">Spherical</span>
                                            <span class="text-muted" style="font-size: 11px;">SPH</span>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <select id="cl-right-sph" class="form-select border-light-subtle rounded-3 py-2 text-muted" style="font-size: 13px;">
                                                <option value="" selected>Right SPH</option>
                                                <option value="0.00">0.00 (Plano)</option>
                                                @for($p = -0.50; $p >= -12.00; $p -= 0.25)
                                                    <option value="{{ number_format($p, 2) }}">{{ number_format($p, 2) }}</option>
                                                @endfor
                                                @for($p = 0.50; $p <= 6.00; $p += 0.25)
                                                    <option value="+{{ number_format($p, 2) }}">+{{ number_format($p, 2) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <select id="cl-left-sph" class="form-select border-light-subtle rounded-3 py-2 text-muted" style="font-size: 13px;">
                                                <option value="" selected>Left SPH</option>
                                                <option value="0.00">0.00 (Plano)</option>
                                                @for($p = -0.50; $p >= -12.00; $p -= 0.25)
                                                    <option value="{{ number_format($p, 2) }}">{{ number_format($p, 2) }}</option>
                                                @endfor
                                                @for($p = 0.50; $p <= 6.00; $p += 0.25)
                                                    <option value="+{{ number_format($p, 2) }}">+{{ number_format($p, 2) }}</option>
                                                @endfor
                                            </select>
                                        </div>

                                        <!-- No. of Boxes -->
                                        <div class="col-md-4 d-flex flex-column justify-content-center">
                                            <span class="fw-bold" style="color: #0d1430; font-size: 14px;">No. of Boxes</span>
                                            <span class="text-muted" style="font-size: 11px;">{{ $product->Packing_Type ?: ($product->pack_size ?: '30 lens/box') }}</span>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <select id="cl-right-boxes" class="form-select border-light-subtle rounded-3 py-2 text-muted" style="font-size: 13px;">
                                                <option value="1" selected>1 Box</option>
                                                <option value="2">2 Boxes</option>
                                                <option value="3">3 Boxes</option>
                                                <option value="4">4 Boxes</option>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <select id="cl-left-boxes" class="form-select border-light-subtle rounded-3 py-2 text-muted" style="font-size: 13px;">
                                                <option value="1" selected>1 Box</option>
                                                <option value="2">2 Boxes</option>
                                                <option value="3">3 Boxes</option>
                                                <option value="4">4 Boxes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Option 2: Submit power later -->
                            <div class="power-option-group">
                                <label class="d-flex align-items-center gap-2 cursor-pointer mb-0">
                                    <input type="radio" name="power_submission" value="later" style="width: 18px; height: 18px; accent-color: #0d1430;">
                                    <span class="fw-bold" style="color: #0d1430; font-size: 15px;">I will submit power later</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lenses per Pack Section -->
                <div class="lenses-pack-section mt-4" id="lenses-pack-section">
                    <h6 class="fw-bold mb-3" style="color: #0d1430; font-size: 16px;">Lenses per Pack</h6>
                    <div class="lens-pack-card rounded-3 overflow-hidden d-inline-block" style="border: 1.5px solid #0d1430; width: 140px; background: #fff;">
                        <div class="px-3 py-1 text-start fw-medium" style="background-color: #edeafb; font-size: 13px; color: #0d1430;">
                            {{ $product->Packing_Type ?: ($product->pack_size ?: '30 Lenses / Box') }}
                        </div>
                        <div class="p-2 text-start">
                            @if($hasDiscount)
                            <div class="text-muted text-decoration-line-through" style="font-size: 12px; color: #94a3b8;">₹{{ number_format($calcMrp, 0) }}</div>
                            @endif
                            <div class="fw-bold" style="color: #0d1430; font-size: 18px;">₹{{ number_format($calcSellingPrice, 0) }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-3 mt-4">
                    @if($isSolution || $isAccessory)
                        <button id="main-action-btn"
                            class="btn btn-outline-custom active"
                            data-has-power="0"
                            data-default-text="BUY NOW"
                            onclick="addToCartAjax('Direct', null, null, null)">
                            <i class="bi bi-bag-check me-2"></i>BUY NOW
                        </button>
                    @elseif($isContactLens)
                        <button id="main-action-btn"
                            class="btn btn-outline-custom active"
                            data-has-power="0"
                            data-default-text="BUY NOW"
                            onclick="submitContactLensCart()">
                            <i class="bi bi-bag-check me-2"></i>BUY NOW
                        </button>
                    @else
                        <button id="main-action-btn"
                            class="btn btn-outline-custom active select-lenses-mode"
                            data-has-power="1"
                            data-default-text="Select Lenses">
                            Select Lenses
                        </button>
                        <button class="btn btn-outline-custom">Try on you</button>
                    @endif
                </div>

            </div>
        </div>
    </div>
</section>
    <!-- ============================ -->
    <!-- end 2 360 degrees -->
    <!-- ============================ -->

    <!-- ============================ -->
    <!-- start hide and show   start 3 -->
    <!-- ============================ -->
    <div class="tech">
        <div class="container my-4">

            <!-- Technical Information -->
            <div class="accordion" id="productAccordion">
                <div class="accordion-item mb-3 rounded-3">
                    <h2 class="accordion-header" id="headingOne">

                        <button class="accordion-button " type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Technical information
                        </button>
                    </h2>

                    <div id="collapseOne" class="accordion-collapse collapse show"
                        aria-labelledby="headingOne"
                        data-bs-parent="#productAccordion">

                        <div class="accordion-body p-4">
                            <div class="row g-3">

                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="spec-card">
                                        <span class="spec-label">Product ID</span>
                                        <span class="spec-value">{{ $product->product_id ?? $product->id }}</span>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="spec-card">
                                        <span class="spec-label">SKU</span>
                                        <span class="spec-value">{{ $product->product_code  ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="spec-card">
                                        <span class="spec-label">Brand</span>
                                        <span class="spec-value">{{ $product->Company ?? 'Speckart' }}</span>
                                    </div>
                                </div>

                                @php
                                    $pTypeLower = strtolower($product->product_type ?? '');
                                    $isContactLens = ($pTypeLower === 'lens') || !empty($product->Modality);
                                    $isSolution = ($pTypeLower === 'solution');
                                    $isAccessory = ($pTypeLower === 'accessory') || ($pTypeLower === 'other');
                                    $isSunglass = ($pTypeLower === 'sunglass') || !empty($product->polarized);
                                @endphp

                                @if($isContactLens)
                                    {{-- ── Contact Lens Specs ── --}}
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Disposability (Modality)</span>
                                            <span class="spec-value">{{ $product->Modality ?? 'Daily' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Pack Size</span>
                                            <span class="spec-value">{{ $product->Packing_Type ?? $product->Size ?? 'Standard Pack' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Water Content</span>
                                            <span class="spec-value">{{ $product->WC ? (str_contains($product->WC, '%') ? $product->WC : $product->WC . '%') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Base Curve (BC)</span>
                                            <span class="spec-value">{{ $product->base_carve ?? $product->BC ?? '8.6 mm' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Diameter (DIA)</span>
                                            <span class="spec-value">{{ $product->Diameter ?? $product->DIA ?? '14.2 mm' }}</span>
                                        </div>
                                    </div>
                                    @if(!empty($product->Color))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Lens Color / Tint</span>
                                            <span class="spec-value">{{ $product->Color }}</span>
                                        </div>
                                    </div>
                                    @endif
                                @elseif($isSolution)
                                    {{-- ── Solution Specs ── --}}
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Volume / Size</span>
                                            <span class="spec-value">{{ $product->Packing_Type ?? $product->Size ?? 'Standard Bottle' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Solution Type</span>
                                            <span class="spec-value">{{ $product->Type ?? 'Multi-Purpose Solution' }}</span>
                                        </div>
                                    </div>
                                    @if(!empty($product->Temple_Detail))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Inclusions</span>
                                            <span class="spec-value">{{ $product->Temple_Detail }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    @if(!empty($product->Material))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Suitable For</span>
                                            <span class="spec-value">{{ $product->Material }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    @if(!empty($product->Validity))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Shelf Life</span>
                                            <span class="spec-value">{{ $product->Validity }}</span>
                                        </div>
                                    </div>
                                    @endif
                                @elseif($isAccessory)
                                    {{-- ── Accessory Specs ── --}}
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Accessory Type</span>
                                            <span class="spec-value">{{ $product->Type ?? 'Eyewear Accessory' }}</span>
                                        </div>
                                    </div>
                                    @if(!empty($product->Color))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Color</span>
                                            <span class="spec-value">{{ $product->Color }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    @if(!empty($product->Material))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Material</span>
                                            <span class="spec-value">{{ $product->Material }}</span>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    {{-- ── Eyeglasses & Sunglasses Specs ── --}}
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Frame Size</span>
                                            <span class="spec-value">{{ $product->Size ?? 'Medium' }}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Gender</span>
                                            <span class="spec-value">
                                                @php
                                                    $genderStr = 'Unisex';
                                                    if(!empty($product->Gender)){
                                                        $genders = json_decode($product->Gender, true);
                                                        if(is_array($genders)){
                                                            $genderStr = implode(', ', array_unique($genders));
                                                        } else {
                                                            $genderStr = $product->Gender;
                                                        }
                                                    }
                                                @endphp
                                                {{ $genderStr }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Frame Dimensions</span>
                                            <span class="spec-value">
                                                @php
                                                    $dimParts = array_filter([$product->lens_width ?? null, $product->Bridge_Size ?? $product->bridge_size ?? null, $product->temple_length ?? null]);
                                                    $dimStr = !empty($dimParts) ? implode('-', $dimParts) . ' mm' : ($product->frame_width ? $product->frame_width . ' mm' : 'Standard Fit');
                                                @endphp
                                                {{ $dimStr }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Frame Color</span>
                                            <span class="spec-value">{{ $product->Color ?? 'N/A' }}</span>
                                        </div>
                                    </div>

                                    @if(!empty($product->Shape))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Shape</span>
                                            <span class="spec-value">{{ $product->Shape }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($product->Type))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Frame Structure</span>
                                            <span class="spec-value">{{ $product->Type }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Material</span>
                                            <span class="spec-value">{{ $product->Material ?? 'Premium Handcrafted' }}</span>
                                        </div>
                                    </div>

                                    @if($isSunglass || !empty($product->polarized))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">Polarized</span>
                                            <span class="spec-value">{{ !empty($product->polarized) ? 'Yes (Anti-Glare)' : 'No' }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    @if($isSunglass || !empty($product->uv_protection))
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="spec-card">
                                            <span class="spec-label">UV Protection</span>
                                            <span class="spec-value">{{ $product->uv_protection ?? 'UV400 (100% Protection)' }}</span>
                                        </div>
                                    </div>
                                    @endif
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visit Nearby Store -->
                <div class="accordion-item   mb-3 rounded-3">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Visit Nearby Store
                        </button>
                    </h2>

                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">

                                <!-- Store 1 -->
                                <div class="col-md-6 col-12">
                                    <div class="store-card p-3 rounded-4 shadow-sm">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-4">
                                                <img src="{{asset('website/assets/img/productimg/grand.png')}}"
                                                    class="rounded-3 img-fluid" alt="Store Image">
                                            </div>
                                            <div class="col-8">
                                                <h6 class="fw-bold">Grand Central, Seawoods</h6>
                                                <p class="small text-muted">
                                                    P 80, Prozone Mall, Unit No G 53, Midc Api Road,<br>
                                                    Chikalthana, Near Mcdonalds, Aurangabad, Maharashtra, 431210
                                                </p>
                                                <p class="phon"><i class="bi bi-telephone text-success me-1"></i> +91
                                                    7428891313</p>
                                                <button class="btn btn-map mt-1">Open Google Map</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Store 2 -->
                                <div class="col-md-6 col-12">
                                    <div class="store-card p-3 rounded-4 shadow-sm">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-4">
                                                <img src="{{asset('website/assets/img/productimg/image@2x.png')}}"
                                                    class="rounded-3 img-fluid" alt="Store Image">
                                            </div>
                                            <div class="col-8">
                                                <h6 class="fw-bold">Inorbit Mall, Vashi</h6>
                                                <p class="mb-2 small text-muted">
                                                    P 80, Prozone Mall, Unit No G 53, Midc Api Road,<br>
                                                    Chikalthana, Near Mcdonalds, Aurangabad, Maharashtra, 431210
                                                </p>
                                                <p class="phon"><i class="bi bi-telephone text-success me-1"></i> +91
                                                    7428891313</p>
                                                <button class="btn btn-map mt-1">Open Google Map</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Check Delivery Options -->
                <div class="accordion-item   mb-3 rounded-3">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Check Delivery Options
                        </button>
                    </h2>

                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <!-- Store 2 -->
                                <div class="col-lg-6 col-12">

                                    <!-- <div class="pincode-box mt-3">
                                        <input type="text" placeholder="Enter pin code" />
                                        <button type="button">CHECK</button>
                                    </div> -->
                                    <div class="input-group pincode-box">
                                        <input type="text" class="form-control" placeholder="Enter pin code">
                                        <button type="button" class="btn btn-primary">
                                            CHECK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Reviews (126) -->
                <div class="accordion-item   rounded-3">
                    <h2 class="accordion-header" id="headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
                            Reviews (126)
                        </button>
                    </h2>

                    <div id="collapsefour" class="accordion-collapse collapse" aria-labelledby="headingfour"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="testimonial-section container">
                                <div class="testimonial-slider">
                                    <div class="testimonial-track">
                                        <!-- ===== Cards (duplicated for seamless loop) ===== -->
                                        <div class="testimonial-card">
                                            <div class="stars">★★★★☆</div>
                                            <p>SpeckArts has been an absolute game changer for our business! Their design
                                                team delivered stunning visuals that perfectly captured our brand’s essence!
                                            </p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Sarah L.</span>
                                                <span>24 March 2025</span>
                                            </div>
                                        </div>

                                        <div class="testimonial-card">
                                            <div class="stars">★★★☆☆</div>
                                            <p>I recently purchased from SpeckArts.com, and the shopping experience was
                                                seamless. The website was easy to navigate, and my order arrived ahead of
                                                schedule.</p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Ashok D.</span>
                                                <span>22 March 2025</span>
                                            </div>
                                        </div>

                                        <div class="testimonial-card">
                                            <div class="stars">★★★★☆</div>
                                            <p>I hired SpeckArts for a custom digital art piece for my home, and I’m
                                                absolutely thrilled with the final product! The artist was professional and
                                                passionate.</p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Maria W.</span>
                                                <span>19 March 2025</span>
                                            </div>
                                        </div>

                                        <div class="testimonial-card">
                                            <div class="stars">★★★★★</div>
                                            <p>Excellent service! The communication was great throughout, and the final
                                                design exceeded expectations. Highly recommend SpeckArts.</p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Rahul K.</span>
                                                <span>17 March 2025</span>
                                            </div>
                                        </div>

                                        <!-- Duplicate same cards for seamless infinite loop -->
                                        <div class="testimonial-card">
                                            <div class="stars">★★★★☆</div>
                                            <p>SpeckArts has been an absolute game changer for our business! Their design
                                                team delivered stunning visuals that perfectly captured our brand’s essence!
                                            </p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Sarah L.</span>
                                                <span>24 March 2025</span>
                                            </div>
                                        </div>

                                        <div class="testimonial-card">
                                            <div class="stars">★★★☆☆</div>
                                            <p>I recently purchased from SpeckArts.com, and the shopping experience was
                                                seamless. The website was easy to navigate, and my order arrived ahead of
                                                schedule.</p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Ashok D.</span>
                                                <span>22 March 2025</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================ -->
    <!-- end hide and show -->
    <!-- ============================ -->

    @if($relatedProducts && $relatedProducts->count() > 0)
    <!-- similar-products-section -->
    <section class="new-arrivals-section mt-5 mb-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center px-2 mb-4">
                        <h3 style="color: #000; background-image: linear-gradient(to right, #B9FDFE, #f3f6fd); display: inline-block; padding: 8px 20px; font-size: 24px; font-weight: 500; margin-bottom: 0;">Similar Products</h3>
                        <div>
                            <a href="{{ url('/products?category='.$product->category_id) }}" class="view-all-btn">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12"> 
                    <div class="new-arrivals-section-slider">
                        <div class="wrapper">
                            <div class="new-arrivals-slider">
                                @foreach($relatedProducts as $p)
                                <div>
                                    <a href="{{ $p->detail_url }}">
                                    <div class="arrivals-slider-card">
                                        <div class="arrivals-slider-card-img">
                                            <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}">
                                        </div>
                                        <h4>{{ $p->product_name }}</h4>
                                        <p>Starting at Rs.{{ number_format($p->Retail_Price, 0) }}</p>
                                    </div>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end similar-products-section -->
    @endif

    <!-- ══════════════════════════════════════════
                         SELECT LENSES MODAL
                    ══════════════════════════════════════════ -->
    <div class="modal fade" id="lensModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg m-auto modal-lg modal-md-full" id="lens-modal-dialog">
            <div class="modal-content lens-popup">

                <!-- Header -->
                <div class="lens-popup-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <button id="goPrev" class="lens-prev-btn fs-6" style="visibility:hidden;">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                        <div class="lens-steps-indicator">
                            <span class="step-dot active" id="dot1"></span>
                            <span class="step-dot" id="dot2"></span>
                            <span class="step-dot" id="dot3"></span>
                            <span class="step-dot" id="dot4"></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <!-- Body -->
                <div class="lens-popup-body">

                    <!-- ── Step 1: Want to add Lenses? ── -->
                    <div id="step1" class="lens-step">
                        <div class="lens-step-icon">
                            <img src="{{asset('website/assets/img/productimg/mask1.png')}}" alt="Lens">
                        </div>
                        <h4 class="lens-step-title">Want to add Lenses?</h4>
                        <p class="lens-step-sub">Choose lenses with the frame or buy just the frame</p>
                        <div class="lens-step1-btns">
                            <button class="lens-main-btn" id="goStep2">
                                <i class="bi bi-eye me-2"></i>Buy with Lenses
                            </button>
                            <button class="lens-frame-btn" onclick="addToCartAjax('Frame Only', null, null, null)">
                                <i class="bi bi-border-style me-2"></i>Only the Frame
                            </button>
                        </div>
                    </div>

                    <!-- ── Step 2: Choose Lens Type ── -->
                    <div id="step2" class="lens-step" style="display:none;">
                        <h4 class="lens-step-title">Select your Power Type</h4>
                        <p class="lens-step-sub">Pick the lens type that suits your need</p>
                        <div class="lens-type-grid">
                            @php
                                $rawSupportedTypes = $product->supported_product_types ?? null;
                                if (is_string($rawSupportedTypes)) {
                                    $supportedTypeIds = json_decode($rawSupportedTypes, true);
                                } elseif (is_array($rawSupportedTypes)) {
                                    $supportedTypeIds = $rawSupportedTypes;
                                } else {
                                    $supportedTypeIds = [];
                                }

                                $powerTypesQuery = \App\Models\PowerType::where('is_active', 1);
                                if (!empty($supportedTypeIds) && is_array($supportedTypeIds)) {
                                    $powerTypesQuery->whereIn('id', $supportedTypeIds);
                                }
                                $powerTypesMaster = $powerTypesQuery->get();
                            @endphp
                            @foreach($powerTypesMaster as $pt)
                            <div class="lens-type-card" onclick="selectLensType(this, 3)" data-power-type-id="{{ $pt->id }}">
                                <div class="lens-type-icon">
                                    <img src="{{ $pt->images ? asset('website/uploads/power_types/' . $pt->images) : 'https://static5.lenskart.com/media/uploads/ZeroPowerComputer.png' }}"
                                        alt="{{ $pt->description }}">
                                </div>
                                <p class="lens-type-name">{{ $pt->description }}</p>
                                <p class="lens-type-desc">{{ $pt->tag }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- ── Step 3: Choose Package ── -->
                    <div id="step3" class="lens-step" style="display:none;">
                        <h4 class="lens-step-title">Choose Lens Package</h4>
                        <p class="lens-step-sub">Select the best lens coating for you</p>
                        <div class="lens-package-list">
                            @php
                                $rawSelectedPackages = $product->selected_lens_packages ?? null;
                                if (is_string($rawSelectedPackages)) {
                                    $selectedPackageIds = json_decode($rawSelectedPackages, true);
                                } elseif (is_array($rawSelectedPackages)) {
                                    $selectedPackageIds = $rawSelectedPackages;
                                } else {
                                    $selectedPackageIds = [];
                                }

                                $lensTags = \App\Models\LensPackageTag::where('is_active', 1)->orderBy('sort_order')->get();
                                
                                $lensPackagesQuery = \App\Models\LensPackage::with(['tags', 'benefits', 'badges', 'coupons', 'powerTypes', 'media'])->where('is_active', 1);
                                if (!empty($selectedPackageIds) && is_array($selectedPackageIds)) {
                                    $lensPackagesQuery->whereIn('id', $selectedPackageIds);
                                }
                                $lensPackages = $lensPackagesQuery->orderBy('sort_order')->get();
                            @endphp

                            <div class="lens-filters">
                                <button class="lens-filter active" data-slug="all">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    All Lenses
                                </button>
                                @foreach($lensTags as $tag)
                                <button class="lens-filter" data-slug="{{ $tag->slug }}">
                                    @if($tag->icon_url)
                                        <img src="{{ asset('website/uploads/lens_tags/' . $tag->icon_url) }}" alt="{{ $tag->name }}" style="width: 16px; height: 16px; object-fit: contain;">
                                    @else
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    @endif
                                            {{ $tag->name }}
                                </button>
                                @endforeach
                            </div>
                            
                            @foreach($lensPackages as $package)
                                @php
                                    $tagSlugs = $package->tags->pluck('slug')->implode(',');
                                    $powerTypeIds = $package->powerTypes->pluck('id')->implode(',');
                                    $primaryMedia = $package->media->first();
                                    $primaryBadge = $package->badges->first();
                                    $coupon = $package->coupons->first();
                                    
                                    $isFreeLensPackage = ($package->is_free_lens || $package->current_price == 0);
                                    
                                    $p1_temp = (float)($product->Retail_Price ?? 0);
                                    $p2_temp = (float)($product->Purchase_Price ?? 0);
                                    $dPrice_temp = (float)($product->discount_price ?? 0);
                                    
                                    if ($dPrice_temp > 0 && $p1_temp > 0 && $dPrice_temp < $p1_temp) {
                                        $frameSalePrice = $dPrice_temp;
                                        $frameMrp = $p1_temp;
                                    } else {
                                        if ($p1_temp > 0 && $p2_temp > 0) {
                                            $frameMrp = max($p1_temp, $p2_temp);
                                            $frameSalePrice = min($p1_temp, $p2_temp);
                                        } else {
                                            $frameMrp = max($p1_temp, $p2_temp);
                                            $frameSalePrice = $frameMrp;
                                        }
                                    }

                                    $pkgType = $package->package_type ?? ($package->is_free_lens ? 'free_lens' : 'frame_and_lens');

                                    if ($pkgType === 'free_frame') {
                                        // Customer pays ONLY for the Lens Package; Frame is FREE (₹0)
                                        $combinedPrice = (float)($package->current_price ?? 0);
                                        $originalLensPrice = (float)($package->original_price > $package->current_price ? $package->original_price : $package->current_price);
                                        $combinedOriginalPrice = $originalLensPrice > 0 ? $originalLensPrice : $combinedPrice;
                                    } elseif ($pkgType === 'free_lens' || $pkgType === 'frame_only' || $package->is_free_lens) {
                                        // Customer pays ONLY for the Frame; Lens is FREE (₹0)
                                        $combinedPrice = $frameSalePrice;
                                        $originalLensPrice = (float)($package->original_price > $package->current_price ? $package->original_price : $package->current_price);
                                        $combinedOriginalPrice = $frameMrp + $originalLensPrice;
                                    } elseif ($pkgType === 'lens_only') {
                                        // Customer pays ONLY for Lens Package
                                        $combinedPrice = (float)($package->current_price ?? 0);
                                        $originalLensPrice = (float)($package->original_price > $package->current_price ? $package->original_price : $package->current_price);
                                        $combinedOriginalPrice = $originalLensPrice > 0 ? $originalLensPrice : $combinedPrice;
                                    } else {
                                        // Standard Frame + Lens Combo
                                        $combinedPrice = $frameSalePrice + (float)($package->current_price ?? 0);
                                        $originalLensPrice = (float)($package->original_price > $package->current_price ? $package->original_price : $package->current_price);
                                        $combinedOriginalPrice = $frameMrp + $originalLensPrice;
                                    }

                                    $isFreeLensPackage = ($pkgType === 'free_lens' || $pkgType === 'frame_only' || $package->is_free_lens);

                                    $benefitsArray = $package->benefits->map(function($b) {
                                        return [
                                            'name' => $b->name,
                                            'description' => $b->description,
                                            'icon_image' => $b->icon_image ? asset('website/uploads/lens_benefits/' . $b->icon_image) : null,
                                            'icon_emoji' => $b->icon_emoji
                                        ];
                                    })->toJson();

                                    $mediaUrls = $package->media->map(function($m) {
                                        return asset($m->url);
                                    })->toJson();
                                @endphp
                                <div class="lens-package-card text-start border rounded position-relative align-items-center justify-content-between mb-3 p-3 shadow-xs bg-white"
                                    data-tags="{{ $tagSlugs }}"
                                    data-power-types="{{ $powerTypeIds }}">
                                    <div class="forwardBtn" style="cursor:pointer;"
                                        onclick="selectLensPackage({{ $package->id }}, {{ $isFreeLensPackage ? 'true' : 'false' }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                            viewBox="0 0 20 20" fill="none">
                                            <rect width="20" height="20" rx="10" fill="#00b9b9" />
                                            <rect x="2" y="2" width="16" height="16" rx="8"
                                                fill="#00b9b9" />
                                            <path d="M8 14L12 10L8 6" stroke="white" stroke-width="1.5"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="lens-left position-relative me-3">
                                            <div class="lens-card-left" style="width: 140px;">
                                                @if($primaryMedia)
                                                    <img src="{{ asset($primaryMedia->url) }}" class="img-fluid rounded" alt="{{ $primaryMedia->alt_text ?: $package->name }}">
                                                @else
                                                    <img src="https://static5.lenskart.com/media/uploads/Antiglare_1_updated.png" class="img-fluid rounded" alt="{{ $package->name }}">
                                                @endif
                                            </div>
                                            @if($primaryBadge)
                                            <div class="lens-badge ps-2 pe-3 py-1 rounded rounded-start-0 position-absolute top-0 start-0 shadow-sm"
                                                style="font-size: 10px; font-weight: 600; background-color: {{ $primaryBadge->bg_color ?? '#198754' }}; color: {{ $primaryBadge->text_color ?? '#ffffff' }}; z-index: 2;">
                                                {{ $primaryBadge->label }}
                                            </div>
                                            @elseif($pkgType === 'free_frame')
                                            <div class="lens-badge bg-purple text-white ps-2 pe-3 py-1 rounded rounded-start-0 position-absolute top-0 start-0 shadow-sm"
                                                style="font-size: 10px; font-weight: 700; text-transform: uppercase; background: #6f42c1 !important; letter-spacing: 0.5px; z-index: 2;">
                                                Free Frame
                                            </div>
                                            @elseif($pkgType === 'free_lens' || $pkgType === 'frame_only' || $package->is_free_lens)
                                            <div class="lens-badge bg-primary text-white ps-2 pe-3 py-1 rounded rounded-start-0 position-absolute top-0 start-0 shadow-sm"
                                                style="font-size: 10px; font-weight: 700; text-transform: uppercase; background: #0052cc !important; letter-spacing: 0.5px; z-index: 2;">
                                                Free Lenses
                                            </div>
                                            @endif
                                        </div>

                                        <div class="lens-card-right py-0 text-start d-flex flex-column flex-grow-1">
                                            <div>
                                                <h5 class="fw-bold mb-1" style="font-size: 16px; color: #1c2b4a;">
                                                    {{ $package->name }}</h5>
                                            </div>
                                            <div>
                                                <ul class="lens-features m-0 p-0 text-muted"
                                                    style="list-style: none; font-size: 12px; line-height: 1.5;">
                                                    @foreach($package->benefits->take(3) as $benefit)
                                                    <li class="d-flex align-items-center mb-1">
                                                        @if($benefit->icon_image)
                                                            <img src="{{ asset('website/uploads/lens_benefits/' . $benefit->icon_image) }}" style="width: 16px; height:16px;" class="me-1">
                                                        @else
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                                <path d="M12 5C5.636 5 2 12 2 12s3.636 7 10 7 10-7 10-7-3.636-7-10-7z"/>
                                                                <circle cx="12" cy="12" r="3"/>
                                                            </svg> 
                                                        @endif
                                                        {{ $benefit->name }}
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                <span class="view-details text-decoration-none fw-semibold" style="color: #00b9b9; cursor: pointer; font-size: 13px;" data-id="{{ $package->id }}"
                                                    data-name="{{ $package->name }}"
                                                    data-price="₹{{ number_format($combinedPrice, 0) }}"
                                                    data-original-price="{{ $combinedOriginalPrice > $combinedPrice ? '₹'.number_format($combinedOriginalPrice, 0) : '' }}"
                                                    data-desc="{{ $package->short_description }}"
                                                    data-free="{{ $isFreeLensPackage ? 'true' : 'false' }}"
                                                    data-coupon="{{ $coupon ? $coupon->code : '' }}"
                                                    data-benefits="{{ $benefitsArray }}"
                                                    data-media="{{ $mediaUrls }}"
                                                    data-type="{{ $package->package_type ?? ($isFreeLensPackage ? 'frame_only' : 'frame_and_lens') }}">
                                                    View Details
                                                    <span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 14 14" fill="none">
                                                            <path d="M5.25 10.5L8.75 7L5.25 3.5" stroke="#00b9b9"
                                                                stroke-width="1.5" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Warranty & Frame + Lens Price Section -->
                                    <div class="col-12 d-flex mt-3 pt-2 border-top align-items-center justify-content-between">
                                        <div class="warranty">
                                            @if($package->warranty_months)
                                            <p class="mb-0 text-muted" style="font-size: 12px; font-weight: 600;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#00b9b9" class="bi bi-shield-check me-1" viewBox="0 0 16 16">
                                                    <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
                                                    <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                                </svg>
                                                {{ $package->warranty_months }} Months Warranty
                                            </p>
                                            @endif
                                        </div>
                                        <div class="coupon-section d-flex align-items-center">
                                            @if($coupon)
                                            <div class="me-3">
                                                <span class="small text-muted">Coupon : <span class="fw-bold text-dark">{{ $coupon->code }}</span></span>
                                            </div>
                                            @else
                                            <div class="me-3">
                                                <span class="small text-muted">Coupon : <span class="fw-bold text-dark">SINGLE</span></span>
                                            </div>
                                            @endif

                                            <!-- Price box matching user screenshot -->
                                            <div class="text-end">
                                                <div class="text-uppercase text-muted fw-semibold" style="font-size: 10px; letter-spacing: 0.5px; line-height: 1.1;">
                                                    @if(($package->package_type ?? '') === 'free_lens' || ($package->package_type ?? '') === 'frame_only' || $package->is_free_lens)
                                                        Free Lens
                                                    @elseif(($package->package_type ?? '') === 'free_frame')
                                                        Free Frame
                                                    @elseif(($package->package_type ?? '') === 'lens_only')
                                                        Lens Only
                                                    @else
                                                        Frame + Lens
                                                    @endif
                                                </div>
                                                <span class="new-price fw-bold" style="color: #00b9b9; font-size: 17px;">₹{{ number_format($combinedPrice, 0) }}</span>
                                                @if($combinedOriginalPrice > $combinedPrice)
                                                <span class="old-price text-decoration-line-through text-muted small ms-1">₹{{ number_format($combinedOriginalPrice, 0) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- ── Step 4: Do you know your power? ── -->
                    <div id="step4" class="lens-step" style="display:none;">
                        <h4 class="lens-step-title" id="step4-title">Do you know your power?</h4>
                        <p class="lens-step-sub" id="step4-sub">Choose how you'd like to provide your prescription</p>

                        <!-- Selection grid -->
                       <div class="power-options-grid" id="power-options-grid">

                            <div class="power-option-card"
                                onclick="selectPowerOption('know')">

                                <div class="option-icon">
                                    <svg width="35" height="35" viewBox="0 0 24 24" fill="none">
                                        <path d="M2 12C4.5 7.5 8 5 12 5C16 5 19.5 7.5 22 12C19.5 16.5 16 19 12 19C8 19 4.5 16.5 2 12Z"
                                            stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                </div>

                                <h6 class="option-title">I Know My Power</h6>

                                <p class="option-desc">
                                    Enter your prescription values manually.
                                </p>

                                <span class="option-arrow">
                                    <i class="ti ti-arrow-right"></i>
                                </span>
                            </div>

                            <div class="power-option-card" onclick="selectPowerOption('upload')">
                                <div class="option-icon">
                                    <svg width="35" height="35" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 16V4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M8 8L12 4L16 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 18V20H20V18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </div>

                                <h6 class="option-title">Upload Prescription</h6>

                                <p class="option-desc">
                                    Upload your doctor's prescription.
                                </p>

                                <span class="option-arrow">
                                    <i class="ti ti-arrow-right"></i>
                                </span>
                            </div>

                            <div class="power-option-card"
                                onclick="selectPowerOption('dontknow')">

                                <div class="option-icon">
                                    <svg width="35" height="35" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M9.5 9.5C9.5 8.1 10.6 7 12 7C13.4 7 14.5 8.1 14.5 9.5C14.5 10.8 13.5 11.5 12.7 12.1C12.2 12.5 12 12.8 12 13.5"
                                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <circle cx="12" cy="17" r="1" fill="currentColor"/>
                                    </svg>
                                </div>

                                <h6 class="option-title">I Don't Know My Power</h6>

                                <p class="option-desc">
                                    Schedule an eye test before ordering.
                                </p>

                                <span class="option-arrow">
                                    <i class="ti ti-arrow-right"></i>
                                </span>
                            </div>

                        </div>

                        <!-- Manual entry form -->
                        <div id="manual-power-form" style="display:none;">

    <div class="prescription-card">

        <div class="text-center mb-4">
            <div class="rx-icon">
                <i class="bi bi-eye"></i>
            </div>

            <h5 class="fw-bold mb-2">Enter Prescription Details</h5>

            <!-- <p class="text-muted mb-0">
                Fill in your eye prescription values below.
            </p> -->
        </div>

        <div class="row g-3">

            <!-- Right Eye -->
            <div class="col-md-6">
                <div class="eye-card">
                    <div class="eye-title">
                        <i class="bi bi-eye-fill"></i>
                        Right Eye (OD)
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SPH (Spherical)</label>
                        <select class="form-select" id="rx_right_sph">
                                                        <option value="0.00">0.00</option>
                            <option value="-6.00">
                                -6.00
                            </option>
                            <option value="-5.75">
                                -5.75
                            </option>
                            <option value="-5.50">
                                -5.50
                            </option>
                            <option value="-5.25">
                                -5.25
                            </option>
                            <option value="-5.00">
                                -5.00
                            </option>
                            <option value="-4.75">
                                -4.75
                            </option>
                            <option value="-4.50">
                                -4.50
                            </option>
                            <option value="-4.25">
                                -4.25
                            </option>
                            <option value="-4.00">
                                -4.00
                            </option>
                            <option value="-3.75">
                                -3.75
                            </option>
                            <option value="-3.50">
                                -3.50
                            </option>
                            <option value="-3.25">
                                -3.25
                            </option>
                            <option value="-3.00">
                                -3.00
                            </option>
                            <option value="-2.75">
                                -2.75
                            </option>
                            <option value="-2.50">
                                -2.50
                            </option>
                            <option value="-2.25">
                                -2.25
                            </option>
                            <option value="-2.00">
                                -2.00
                            </option>
                            <option value="-1.75">
                                -1.75
                            </option>
                            <option value="-1.50">
                                -1.50
                            </option>
                            <option value="-1.25">
                                -1.25
                            </option>
                            <option value="-1.00">
                                -1.00
                            </option>
                            <option value="-0.75">
                                -0.75
                            </option>
                            <option value="-0.50">
                                -0.50
                            </option>
                            <option value="-0.25">
                                -0.25
                            </option>
                            <option value="+0.25">
                                +0.25
                            </option>
                            <option value="+0.50">
                                +0.50
                            </option>
                            <option value="+0.75">
                                +0.75
                            </option>
                            <option value="+1.00">
                                +1.00
                            </option>
                            <option value="+1.25">
                                +1.25
                            </option>
                            <option value="+1.50">
                                +1.50
                            </option>
                            <option value="+1.75">
                                +1.75
                            </option>
                            <option value="+2.00">
                                +2.00
                            </option>
                            <option value="+2.25">
                                +2.25
                            </option>
                            <option value="+2.50">
                                +2.50
                            </option>
                            <option value="+2.75">
                                +2.75
                            </option>
                            <option value="+3.00">
                                +3.00
                            </option>
                            <option value="+3.25">
                                +3.25
                            </option>
                            <option value="+3.50">
                                +3.50
                            </option>
                            <option value="+3.75">
                                +3.75
                            </option>
                            <option value="+4.00">
                                +4.00
                            </option>
                            <option value="+4.25">
                                +4.25
                            </option>
                            <option value="+4.50">
                                +4.50
                            </option>
                            <option value="+4.75">
                                +4.75
                            </option>
                            <option value="+5.00">
                                +5.00
                            </option>
                            <option value="+5.25">
                                +5.25
                            </option>
                            <option value="+5.50">
                                +5.50
                            </option>
                            <option value="+5.75">
                                +5.75
                            </option>
                            <option value="+6.00">
                                +6.00
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">CYL (Cylindrical)</label>
                        <select class="form-select" id="rx_right_cyl">
                                                        <option value="0.00">0.00</option>
                            <option value="-4.00">
                                -4.00
                            </option>
                            <option value="-3.75">
                                -3.75
                            </option>
                            <option value="-3.50">
                                -3.50
                            </option>
                            <option value="-3.25">
                                -3.25
                            </option>
                            <option value="-3.00">
                                -3.00
                            </option>
                            <option value="-2.75">
                                -2.75
                            </option>
                            <option value="-2.50">
                                -2.50
                            </option>
                            <option value="-2.25">
                                -2.25
                            </option>
                            <option value="-2.00">
                                -2.00
                            </option>
                            <option value="-1.75">
                                -1.75
                            </option>
                            <option value="-1.50">
                                -1.50
                            </option>
                            <option value="-1.25">
                                -1.25
                            </option>
                            <option value="-1.00">
                                -1.00
                            </option>
                            <option value="-0.75">
                                -0.75
                            </option>
                            <option value="-0.50">
                                -0.50
                            </option>
                            <option value="-0.25">
                                -0.25
                            </option>
                            <option value="+0.25">
                                +0.25
                            </option>
                            <option value="+0.50">
                                +0.50
                            </option>
                            <option value="+0.75">
                                +0.75
                            </option>
                            <option value="+1.00">
                                +1.00
                            </option>
                            <option value="+1.25">
                                +1.25
                            </option>
                            <option value="+1.50">
                                +1.50
                            </option>
                            <option value="+1.75">
                                +1.75
                            </option>
                            <option value="+2.00">
                                +2.00
                            </option>
                            <option value="+2.25">
                                +2.25
                            </option>
                            <option value="+2.50">
                                +2.50
                            </option>
                            <option value="+2.75">
                                +2.75
                            </option>
                            <option value="+3.00">
                                +3.00
                            </option>
                            <option value="+3.25">
                                +3.25
                            </option>
                            <option value="+3.50">
                                +3.50
                            </option>
                            <option value="+3.75">
                                +3.75
                            </option>
                            <option value="+4.00">
                                +4.00
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Left Eye -->
            <div class="col-md-6">
                <div class="eye-card">
                    <div class="eye-title">
                        <i class="bi bi-eye-fill"></i>
                        Left Eye (OS)
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SPH (Spherical)</label>
                        <select class="form-select" id="rx_left_sph">
                                                        <option value="0.00">0.00</option>
                            <option value="-6.00">
                                -6.00
                            </option>
                            <option value="-5.75">
                                -5.75
                            </option>
                            <option value="-5.50">
                                -5.50
                            </option>
                            <option value="-5.25">
                                -5.25
                            </option>
                            <option value="-5.00">
                                -5.00
                            </option>
                            <option value="-4.75">
                                -4.75
                            </option>
                            <option value="-4.50">
                                -4.50
                            </option>
                            <option value="-4.25">
                                -4.25
                            </option>
                            <option value="-4.00">
                                -4.00
                            </option>
                            <option value="-3.75">
                                -3.75
                            </option>
                            <option value="-3.50">
                                -3.50
                            </option>
                            <option value="-3.25">
                                -3.25
                            </option>
                            <option value="-3.00">
                                -3.00
                            </option>
                            <option value="-2.75">
                                -2.75
                            </option>
                            <option value="-2.50">
                                -2.50
                            </option>
                            <option value="-2.25">
                                -2.25
                            </option>
                            <option value="-2.00">
                                -2.00
                            </option>
                            <option value="-1.75">
                                -1.75
                            </option>
                            <option value="-1.50">
                                -1.50
                            </option>
                            <option value="-1.25">
                                -1.25
                            </option>
                            <option value="-1.00">
                                -1.00
                            </option>
                            <option value="-0.75">
                                -0.75
                            </option>
                            <option value="-0.50">
                                -0.50
                            </option>
                            <option value="-0.25">
                                -0.25
                            </option>
                            <option value="+0.25">
                                +0.25
                            </option>
                            <option value="+0.50">
                                +0.50
                            </option>
                            <option value="+0.75">
                                +0.75
                            </option>
                            <option value="+1.00">
                                +1.00
                            </option>
                            <option value="+1.25">
                                +1.25
                            </option>
                            <option value="+1.50">
                                +1.50
                            </option>
                            <option value="+1.75">
                                +1.75
                            </option>
                            <option value="+2.00">
                                +2.00
                            </option>
                            <option value="+2.25">
                                +2.25
                            </option>
                            <option value="+2.50">
                                +2.50
                            </option>
                            <option value="+2.75">
                                +2.75
                            </option>
                            <option value="+3.00">
                                +3.00
                            </option>
                            <option value="+3.25">
                                +3.25
                            </option>
                            <option value="+3.50">
                                +3.50
                            </option>
                            <option value="+3.75">
                                +3.75
                            </option>
                            <option value="+4.00">
                                +4.00
                            </option>
                            <option value="+4.25">
                                +4.25
                            </option>
                            <option value="+4.50">
                                +4.50
                            </option>
                            <option value="+4.75">
                                +4.75
                            </option>
                            <option value="+5.00">
                                +5.00
                            </option>
                            <option value="+5.25">
                                +5.25
                            </option>
                            <option value="+5.50">
                                +5.50
                            </option>
                            <option value="+5.75">
                                +5.75
                            </option>
                            <option value="+6.00">
                                +6.00
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">CYL (Cylindrical)</label>
                        <select class="form-select" id="rx_left_cyl">
                                                        <option value="0.00">0.00</option>
                            <option value="-4.00">
                                -4.00
                            </option>
                            <option value="-3.75">
                                -3.75
                            </option>
                            <option value="-3.50">
                                -3.50
                            </option>
                            <option value="-3.25">
                                -3.25
                            </option>
                            <option value="-3.00">
                                -3.00
                            </option>
                            <option value="-2.75">
                                -2.75
                            </option>
                            <option value="-2.50">
                                -2.50
                            </option>
                            <option value="-2.25">
                                -2.25
                            </option>
                            <option value="-2.00">
                                -2.00
                            </option>
                            <option value="-1.75">
                                -1.75
                            </option>
                            <option value="-1.50">
                                -1.50
                            </option>
                            <option value="-1.25">
                                -1.25
                            </option>
                            <option value="-1.00">
                                -1.00
                            </option>
                            <option value="-0.75">
                                -0.75
                            </option>
                            <option value="-0.50">
                                -0.50
                            </option>
                            <option value="-0.25">
                                -0.25
                            </option>
                            <option value="+0.25">
                                +0.25
                            </option>
                            <option value="+0.50">
                                +0.50
                            </option>
                            <option value="+0.75">
                                +0.75
                            </option>
                            <option value="+1.00">
                                +1.00
                            </option>
                            <option value="+1.25">
                                +1.25
                            </option>
                            <option value="+1.50">
                                +1.50
                            </option>
                            <option value="+1.75">
                                +1.75
                            </option>
                            <option value="+2.00">
                                +2.00
                            </option>
                            <option value="+2.25">
                                +2.25
                            </option>
                            <option value="+2.50">
                                +2.50
                            </option>
                            <option value="+2.75">
                                +2.75
                            </option>
                            <option value="+3.00">
                                +3.00
                            </option>
                            <option value="+3.25">
                                +3.25
                            </option>
                            <option value="+3.50">
                                +3.50
                            </option>
                            <option value="+3.75">
                                +3.75
                            </option>
                            <option value="+4.00">
                                +4.00
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- PD -->
            <div class="col-12">
                <div class="pd-card">
                    <label class="form-label fw-semibold">
                        Pupillary Distance (PD)
                    </label>

                    <select class="form-select" id="rx_pd">
                                                <option value="50">
                            50 mm
                        </option>
                        <option value="51">
                            51 mm
                        </option>
                        <option value="52">
                            52 mm
                        </option>
                        <option value="53">
                            53 mm
                        </option>
                        <option value="54">
                            54 mm
                        </option>
                        <option value="55">
                            55 mm
                        </option>
                        <option value="56">
                            56 mm
                        </option>
                        <option value="57">
                            57 mm
                        </option>
                        <option value="58">
                            58 mm
                        </option>
                        <option value="59">
                            59 mm
                        </option>
                        <option value="60">
                            60 mm
                        </option>
                        <option value="61">
                            61 mm
                        </option>
                        <option value="62">
                            62 mm
                        </option>
                        <option value="63" selected>
                            63 mm
                        </option>
                        <option value="64">
                            64 mm
                        </option>
                        <option value="65">
                            65 mm
                        </option>
                        <option value="66">
                            66 mm
                        </option>
                        <option value="67">
                            67 mm
                        </option>
                        <option value="68">
                            68 mm
                        </option>
                        <option value="69">
                            69 mm
                        </option>
                        <option value="70">
                            70 mm
                        </option>
                        <option value="71">
                            71 mm
                        </option>
                        <option value="72">
                            72 mm
                        </option>
                        <option value="73">
                            73 mm
                        </option>
                        <option value="74">
                            74 mm
                        </option>
                        <option value="75">
                            75 mm
                        </option>
                        <option value="76">
                            76 mm
                        </option>
                        <option value="77">
                            77 mm
                        </option>
                        <option value="78">
                            78 mm
                        </option>
                        <option value="79">
                            79 mm
                        </option>
                        <option value="80">
                            80 mm
                        </option>
                    </select>
                </div>
            </div>

        </div>

        <div class="d-flex gap-2 justify-content-end mt-4 action-buttons">
            <button type="button"
                class="btn btn-outline-secondary px-4"
                onclick="backToPowerOptions()">
                Back
            </button>

            <button type="button"
                class="btn btn-primary px-4"
                onclick="submitManualPrescription()">
                Add to Cart & Checkout
            </button>
        </div>

    </div>
</div>

                        <!-- Upload form -->
                        <div id="upload-power-form" style="display:none;">
                            <div class="prescription-upload-card">

                                <!-- <div class="text-center mb-4">
                                    <div class="upload-icon">
                                        <i class="bi bi-file-earmark-medical"></i>
                                    </div>

                                    <h5 class="fw-bold mb-2">Upload Doctor Prescription</h5>

                                    <p class="upload-subtitle mb-0">
                                        Upload your prescription and we'll process your order accordingly.
                                    </p>
                                </div> -->

                                <div class="upload-area">
                                    <input
                                        class="form-control upload-input"
                                        type="file"
                                        id="rx_file"
                                        accept=".jpg,.jpeg,.png,.pdf">

                                    <label for="rx_file" class="upload-label">
                                        <i class="bi bi-cloud-arrow-up"></i>
                                        <h6 class="mb-2">Drag & Drop or Click to Upload</h6>
                                        <span>JPG, PNG, PDF • Maximum 5MB</span>
                                    </label>
                                </div>

                                <div id="selected-file" class="selected-file mt-3 d-none">
                                    <i class="bi bi-file-earmark-check"></i>
                                    <span class="file-name"></span>
                                </div>

                                <div class="d-flex gap-2 justify-content-end mt-4 flex-wrap">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        onclick="backToPowerOptions()">
                                        Back
                                    </button>

                                    <button
                                        type="button"
                                        class="btn text-white" style="background:#00b9b9;"
                                        onclick="submitUploadedPrescription()" >
                                        Add to Cart & Checkout
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div><!-- /lens-popup-body -->
            </div>
        </div>
    </div>
    <!-- ── end lensModal ── -->

    <div class="modal fade lens-sheet-modal" id="viewDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered m-auto">
            <div class="modal-content">
                <!-- Mobile Drag Handle -->
                <div class="sheet-handle"></div>

                <!-- Circular Close Button -->
                <button type="button" class="btn-close sheet-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <!-- Left Visual Pane (Carousel) -->
                <div class="lens-visual-pane">
                    <div id="lensBannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-inner">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <!-- Progress Indicators -->
                        <div class="carousel-progress">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- Right Details Pane -->
                <div class="lens-details-pane">
                    <div class="lens-content">
                        <h3>Anti-Glare Premium</h3>
                        <p class="lens-desc">Premium anti-glare lenses with double-sided coating for crystal clear vision
                        </p>

                        <div class="lens-price-row mb-3">
                            <div class="lens-coupon-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    fill="currentColor" class="bi bi-ticket-perforated-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 0 0 0-3A.5.5 0 0 1 0 6V4.5Zm4-1v1h1v-1H4Zm0 3v1h1v-1H4Zm0 3v1h1v-1H4Zm0 3v1h1v-1H4Zm7-10v1h1v-1h-1Zm0 3v1h1v-1h-1Zm0 3v1h1v-1h-1Zm0 3v1h1v-1h-1Z" />
                                </svg>
                                <span>PAYDAY</span>
                            </div>
                            <div class="lens-price-group text-end">
                                <div class="text-uppercase text-muted fw-bold lens-price-mode-label" style="font-size: 11px; letter-spacing: 0.5px; line-height: 1;">Frame + Lens</div>
                                <span class="price fw-bold" style="font-size: 20px; color: #00b9b9;">₹1,500</span>
                                <del class="text-muted ms-1" style="font-size: 14px; display: none;">₹2,000</del>
                            </div>
                        </div>

                        <h4 class="benefits-heading">Top Benefits</h4>
                        <ul class="benefits-list">
                            <!-- Populated dynamically via JS -->
                        </ul>

                        <h4 class="features-heading">Top Features</h4>
                        <ul class="features-list">
                            <!-- Populated dynamically via JS -->
                        </ul>

                        <!-- Bottom Frame + Lens Combo Bar -->
                        <div class="lens-bottom-bar d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
                            <span class="text-uppercase text-muted fw-bold lens-price-mode-label" style="font-size: 12px; letter-spacing: 0.5px;">Frame + Lens</span>
                            <div class="lens-bottom-price text-end">
                                <span class="price fw-bold" style="color: #00b9b9; font-size: 18px;">₹1,500</span>
                                <del class="old-price text-muted text-decoration-line-through ms-1" style="font-size: 13px; display: none;">₹2,000</del>
                            </div>
                        </div>

                        <button class="select-lens-btn">
                            Select This Lens
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- 3 imgs left right start -->

    <script>
        const variantImages = {
            "1": ["assets/img/productimg/thumb1.png", "assets/img/productimg/thumb2.png"],
            "2": ["assets/img/productimg/tortoise1.png", "assets/img/productimg/tortoise2.png"],
            "3": ["assets/img/productimg/blue1.png", "assets/img/productimg/blue2.png"]
        };
        const variantImagesByLabel = {
            "Black": ["assets/img/productimg/thumb1.png", "assets/img/productimg/thumb2.png"],
            "Tortoise": ["assets/img/productimg/tortoise1.png", "assets/img/productimg/tortoise2.png"],
            "Blue": ["assets/img/productimg/blue1.png", "assets/img/productimg/blue2.png"]
        };
        const defaultImages = ["assets/img/productimg/thumb1.png", "assets/img/productimg/thumb2.png", "assets/img/productimg/thumb3.png", "assets/img/productimg/thumb4.png"];
        let index = 0;
        const visibleCount = 3;

        function initializeThumbnails() {
            const thumbs = document.querySelectorAll('.thumb');
            const mainImg = document.querySelector('.main-image');

            if (thumbs.length === 0) return;

            // Ensure first thumbnail is marked active if none is active
            if (!document.querySelector('.thumb.active')) {
                thumbs[0].classList.add('active');
            }

            let activeThumb = document.querySelector('.thumb.active') || thumbs[0];

            thumbs.forEach(thumb => {
                thumb.addEventListener('mouseenter', () => {
                    if (mainImg) mainImg.src = thumb.src;
                });
                thumb.addEventListener('mouseleave', () => {
                    const currentActive = document.querySelector('.thumb.active') || activeThumb;
                    if (mainImg && currentActive) mainImg.src = currentActive.src;
                });
                thumb.addEventListener('click', () => {
                    thumbs.forEach(t => t.classList.remove('active'));
                    thumb.classList.add('active');
                    activeThumb = thumb;
                    if (mainImg) mainImg.src = thumb.src;
                    
                    if ($('.image-lightbox').length > 0) {
                        $('.image-lightbox')[0].href = thumb.src;
                    }
                    
                    if (window.imageGallery && typeof window.imageGallery.destroy === 'function') {
                        window.imageGallery.destroy();
                    }
                    window.imageGallery = GLightbox({
                        selector: '.image-lightbox'
                    });
                });
            });            
        }

        function updateSlider() {
            const thumbs = document.querySelectorAll('.thumb');
            const thumbsContainer = document.querySelector('.thumbs');
            if (thumbs.length > 0 && thumbsContainer) {
                const thumbWidth = thumbs[0].offsetWidth + 12; // width + gap
                thumbsContainer.style.transform = `translateX(-${index * thumbWidth}px)`;
            }
        }
    </script>

    <script>
        // Global variables for variant & cart management
        const productVariants = [];
        const defaultPrice = {{ $calcMrp }};
        const defaultDiscountPrice = {{ $calcSellingPrice }};

        let selectedColor = $('.color-box.active').data('color-label') || '';
        let selectedSize = $('.size-btn.active').data('size') || '';
        let currentVariantId = 2;

        let selectedLensType = 'Frame Only';
        let selectedPowerTypeId = null;
        let selectedLensPackageId = null;
        let isFreeLens = false;

        function updateVariantDetails() {
            // Find active variants matching selected color
            const variantsForColor = productVariants.filter(v => v.label === selectedColor && v.is_available == 1);

            // Enable/disable size buttons based on color variant availability
            $('.size-btn').each(function() {
                const sizeName = $(this).data('size');
                const isAvailableForColor = variantsForColor.some(v => v.size === sizeName);

                if (isAvailableForColor) {
                    $(this).removeClass('disabled').prop('disabled', false).css({
                        'opacity': '1',
                        'cursor': 'pointer',
                        'text-decoration': 'none'
                    });
                } else {
                    $(this).addClass('disabled').prop('disabled', true).css({
                        'opacity': '0.5',
                        'cursor': 'not-allowed',
                        'text-decoration': 'line-through'
                    });
                    if (sizeName === selectedSize) {
                        $(this).removeClass('active');
                    }
                }
            });

            // If the previously selected size is not available, pick first enabled size
            const activeSizeBtn = $('.size-btn.active:not(:disabled)');
            if (activeSizeBtn.length === 0) {
                const firstAvailableSizeBtn = $('.size-btn:not(:disabled)').first();
                if (firstAvailableSizeBtn.length > 0) {
                    firstAvailableSizeBtn.addClass('active');
                    selectedSize = firstAvailableSizeBtn.data('size');
                } else {
                    selectedSize = '';
                }
            } else {
                selectedSize = activeSizeBtn.data('size');
            }

            // Find matching variant
            const matchingVariant = productVariants.find(v => v.label === selectedColor && v.size === selectedSize && v
                .is_available == 1);

            if (matchingVariant) {
                currentVariantId = matchingVariant.id;
                if (typeof updateBrowserUrl === 'function') {
                    updateBrowserUrl(matchingVariant.slug);
                }

                // Update technical information Accordion section
                $('.spec-value').each(function() {
                    const labelText = $(this).siblings('.spec-label').text().trim();
                    if (labelText === 'Frame Size') {
                        $(this).text(matchingVariant.size || 'N/A');
                    } else if (labelText === 'Frame Colour') {
                        $(this).text(matchingVariant.label || 'N/A');
                    }
                });

                // Update prices
                let price = matchingVariant.price || defaultPrice;
                let discountPrice = matchingVariant.sale_price || matchingVariant.price || defaultDiscountPrice;

                if (!price || price == 0) price = defaultPrice;
                if (!discountPrice || discountPrice == 0) discountPrice = defaultDiscountPrice;

                $('#display-price').text('₹' + parseFloat(price).toLocaleString('en-IN'));
                $('#display-discount-price').text('₹' + parseFloat(discountPrice).toLocaleString('en-IN'));

                if (price > 0 && discountPrice < price) {
                    const discountPercent = Math.round(((price - discountPrice) / price) * 100);
                    $('#display-discount-percent').text(discountPercent + '% OFF').show();
                    $('#display-price').show();
                } else {
                    $('#display-price').hide();
                    $('#display-discount-percent').hide();
                }
            } else {
                currentVariantId = null;
            }
        }

        // Helper for Contact Lens Direct Buy with Power Submission
        function submitContactLensCart() {
            const isManual = $('input[name="power_submission"]:checked').val() === 'manual';
            let rxData = null;

            if (isManual) {
                const rightSph = $('#check-right').is(':checked') ? ($('#cl-right-sph').val() || 'Plano (0.00)') : null;
                const rightBoxes = $('#check-right').is(':checked') ? $('#cl-right-boxes').val() : 1;
                const leftSph = $('#check-left').is(':checked') ? ($('#cl-left-sph').val() || 'Plano (0.00)') : null;
                const leftBoxes = $('#check-left').is(':checked') ? $('#cl-left-boxes').val() : 1;

                rxData = JSON.stringify({
                    type: 'contact_lens_manual',
                    right: { sph: rightSph, boxes: rightBoxes },
                    left: { sph: leftSph, boxes: leftBoxes }
                });
            } else {
                rxData = JSON.stringify({
                    type: 'contact_lens_later',
                    later: true
                });
            }

            addToCartAjax('Contact Lens', null, rxData, null);
        }

        // Cart Submission helper via AJAX
        function addToCartAjax(lensType, lensPackageId, prescriptionData, prescriptionFile) {
            const frameId = currentVariantId || '{{ $product->id }}';
            if (!frameId) {
                if (typeof toastr !== 'undefined') toastr.error('Please select a valid frame product.');
                return;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('frame_id', frameId);
            formData.append('quantity', 1);
            if (lensPackageId) {
                formData.append('lens_package_id', lensPackageId);
            }
            if (lensType) {
                formData.append('lens_type', lensType);
            }
            if (prescriptionData) {
                formData.append('prescription_data', prescriptionData);
            }
            if (prescriptionFile) {
                formData.append('prescription_file', prescriptionFile);
            }

            // Disable checkout buttons to prevent double click
            const activeBtn = $('#main-action-btn');
            const originalText = activeBtn.text();
            activeBtn.prop('disabled', true).text('Adding...');

            $.ajax({
                url: "{{ route('cart.add') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success' || response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Added to cart successfully!');
                        }
                        const countBadge = $('.cart-count');
                        if (countBadge.length > 0 && response.cart_count) {
                            countBadge.text(response.cart_count);
                        }
                        setTimeout(function() {
                            window.location.href = "{{ route('cart') }}";
                        }, 500);
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'Failed to add item to cart.');
                        } else {
                            alert(response.message || 'Failed to add item to cart.');
                        }
                        activeBtn.prop('disabled', false).text(originalText);
                    }
                },
                error: function(xhr) {
                    let errMsg = 'Something went wrong. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errMsg);
                    } else {
                        alert(errMsg);
                    }
                    activeBtn.prop('disabled', false).text(originalText);
                }
            });
        }

        // Prescription options inside Step 4
        function selectPowerOption(option) {
            if (option === 'know') {
                $('#power-options-grid').hide();
                $('#manual-power-form').show();
                $('#step4-title').text('Enter Prescription Values');
                $('#step4-sub').text('Fill in Spherical and Cylindrical values for each eye.');
            } else if (option === 'upload') {
                $('#power-options-grid').hide();
                $('#upload-power-form').show();
                $('#step4-title').text('Upload Prescription');
                $('#step4-sub').text('Upload image or PDF document of prescription.');
            } else if (option === 'dontknow') {
                const rxData = JSON.stringify({
                    later: true
                });
                addToCartAjax(selectedLensType, selectedLensPackageId, rxData, null);
            }
        }

        function backToPowerOptions() {
            $('#manual-power-form').hide();
            $('#upload-power-form').hide();
            $('#power-options-grid').show();
            $('#step4-title').text('Do you know your power?');
            $('#step4-sub').text("Choose how you'd like to provide your prescription");
        }

        function submitManualPrescription() {
            const rxData = {
                right_eye_sph: $('#rx_right_sph').val(),
                right_eye_cyl: $('#rx_right_cyl').val(),
                right_eye_axis: 0,
                right_eye_ap: '',
                left_eye_sph: $('#rx_left_sph').val(),
                left_eye_cyl: $('#rx_left_cyl').val(),
                left_eye_axis: 0,
                left_eye_ap: '',
                pd: $('#rx_pd').val()
            };
            addToCartAjax(selectedLensType, selectedLensPackageId, JSON.stringify(rxData), null);
        }

        function submitUploadedPrescription() {
            const fileInput = document.getElementById('rx_file');
            if (fileInput.files.length === 0) {
                toastr.warning('Please select a file to upload.');
                return;
            }
            const file = fileInput.files[0];
            addToCartAjax(selectedLensType, selectedLensPackageId, null, file);
        }

        // Lens Package selection in Step 3
        function selectLensPackage(packageId, freeLensFlag) {
            selectedLensPackageId = packageId;
            isFreeLens = freeLensFlag;

            console.log('Selected Package ID:', selectedLensPackageId, 'Type:', selectedLensType);

            if (selectedLensType === 'Zero Power') {
                addToCartAjax(selectedLensType, selectedLensPackageId, null, null);
            } else {
                showLensStep(4);
            }
        }
    </script>

    <script>
        // Helper to update the browser URL bar dynamically
        function updateBrowserUrl(slug) {
            if (slug) {
                const newUrl = "product/" + slug;
                window.history.pushState(null, '', newUrl);
            }
        }

        // Helper to swap main image and thumbnails
        function swapImagesList(images) {
            const thumbsContainer = document.querySelector('.thumbs');
            const mainImg = document.querySelector('.main-image');

            if (images.length > 0) {
                if (mainImg) {
                    mainImg.src = images[0];
                }
                if (thumbsContainer) {
                    thumbsContainer.innerHTML = '';
                    images.forEach((imgUrl, i) => {
                        const img = document.createElement('img');
                        img.src = imgUrl;
                        img.className = 'thumb' + (i === 0 ? ' active' : '');
                        img.alt = 'Product Variant Image';
                        thumbsContainer.appendChild(img);
                    });
                }
                index = 0;
                updateSlider();
                initializeThumbnails();
            }
        }

        $(document).ready(function() {
            initializeThumbnails();

            // Color variant clicking handled by page reload (onclick in html)

            // Size variant clicking — toggle active state on click
            $(document).on('click', '.size-btn:not(:disabled)', function() {
                $('.size-btn').removeClass('active');
                $(this).addClass('active');
                selectedSize = $(this).data('size');
            });

            // Scroll arrows
            const arrowRight = document.querySelector('.arrow-btn.right');
            const arrowLeft = document.querySelector('.arrow-btn.left');
            if (arrowRight) {
                arrowRight.addEventListener('click', () => {
                    const thumbs = document.querySelectorAll('.thumb');
                    if (index < thumbs.length - visibleCount) {
                        index++;
                        updateSlider();
                    }
                });
            }
            if (arrowLeft) {
                arrowLeft.addEventListener('click', () => {
                    if (index > 0) {
                        index--;
                        updateSlider();
                    }
                });
            }

            // Initial details check
            // updateVariantDetails();
        });
    </script>


    <!-- three button active  end -->
    <!-- testimonial start -->
    <script>
        const slider = document.querySelector('.testimonial-slider');
        let isDown = false;
        let startX;
        let scrollLeft;
        let autoScroll;
        let isHovered = false;


        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            clearInterval(autoScroll);
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
            if (!isHovered) startAutoScroll();
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
            if (!isHovered) startAutoScroll();
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5;
            slider.scrollLeft = scrollLeft - walk;
        });


        slider.addEventListener('touchstart', (e) => {
            isDown = true;
            clearInterval(autoScroll);
            startX = e.touches[0].pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener('touchend', () => {
            isDown = false;
            if (!isHovered) startAutoScroll();
        });
        slider.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            const x = e.touches[0].pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5;
            slider.scrollLeft = scrollLeft - walk;
        });

        // 🖱️ Hover pause
        slider.addEventListener('mouseenter', () => {
            isHovered = true;
            clearInterval(autoScroll);
        });

        slider.addEventListener('mouseleave', () => {
            isHovered = false;
            startAutoScroll();
        });


        function startAutoScroll() {
            clearInterval(autoScroll);
            autoScroll = setInterval(() => {
                if (!isHovered && !isDown) {
                    slider.scrollLeft += 1; // scroll speed


                    if (slider.scrollLeft >= slider.scrollWidth - slider.clientWidth - 2) {
                        slider.scrollLeft = 0;
                    }
                }
            }, 15); // smaller = faster
        }

        // Start
        startAutoScroll();
    </script>


    <!-- testimonial end -->
    <!-- heart and share button start -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".share").forEach((shareDiv) => {
                const shareIcon = shareDiv.querySelector("i.bi-share");
                const shareOptions = shareDiv.querySelector(".share-options");

                shareIcon.addEventListener("click", (e) => {
                    e.stopPropagation();

                    // Hide all other share menus first
                    document.querySelectorAll(".share-options").forEach((opt) => {
                        if (opt !== shareOptions) opt.style.display = "none";
                    });

                    // Toggle current one
                    shareOptions.style.display =
                        shareOptions.style.display === "flex" ? "none" : "flex";
                });
            });

            document.addEventListener("click", (e) => {
                if (!e.target.closest(".share")) {
                    document.querySelectorAll(".share-options").forEach((opt) => {
                        opt.style.display = "none";
                    });
                }
            });
        });
    </script>

    <!-- popup ens -->
    <!-- ── Product Type + Lens Modal Styles & Logic ── -->
    <style>
        /* ── Option label ── */
        .option-label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            letter-spacing: 0.3px;
        }

        /* ── Product Type pill tabs ── */
        .product-type-tabs {
            gap: 10px !important;
        }

        .ptype-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* min-width: 100px; */
            white-space: nowrap;
            padding: 10px 14px;
            border: 1.5px solid #d0d0d0;
            border-radius: 50px;
            cursor: pointer;
            background: #fff;
            transition: all 0.2s ease;
            text-align: center;
        }

        .ptype-tab:hover {
            border-color: #1c3a5e;
            background: #f5f8ff;
        }

        .ptype-tab.active {
            border-color: #1c3a5e;
            box-shadow: 0 0 0 1.5px #1c3a5e;
        }

        .ptype-name {
            font-size: 13px;
            font-weight: 600;
            color: #1c2b4a;
            line-height: 1.3;
        }

        .ptype-sub {
            font-size: 10px;
            color: #888;
            margin-top: 2px;
            line-height: 1.2;
        }

        /* ── Power chips ── */
        .power-chip {
            padding: 6px 14px;
            border: 1.5px solid #c0c0c0;
            border-radius: 50px;
            background: #fff;
            font-size: 12px;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .power-chip:hover {
            border-color: #1c3a5e;
            color: #1c3a5e;
            background: #f0f4ff;
        }

        .power-chip.active {
            background: #1c3a5e;
            color: #fff;
            border-color: #1c3a5e;
        }

        /* ══════════════════════════════
                           LENS MODAL
                        ══════════════════════════════ */
        .lens-popup {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        }

        .lens-popup-header {
            padding: 16px 22px;
            border-bottom: 1px solid #f0f0f0;
            background: #fff;
        }

        .lens-prev-btn {
            border: none;
            background: none;
            font-size: 13px;
            font-weight: 600;
            color: #1c3a5e;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: opacity 0.2s;
        }

        .lens-prev-btn:hover {
            opacity: 0.7;
        }

        .lens-steps-indicator {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ddd;
            transition: all 0.25s ease;
        }

        .step-dot.active {
            background: #1c3a5e;
            width: 22px;
            border-radius: 4px;
        }

        .lens-popup-body {
            padding: 30px 28px;
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Step layout */
        .lens-step {
            text-align: center;
            animation: fadein 0.25s ease;
        }

        @keyframes fadein {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .lens-step-icon img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-bottom: 16px;
        }

        .lens-step-title {
            font-size: 25px;
            font-weight: 800;
            color: #1c2b4a;
            margin-bottom: 6px;
        }

        .lens-step-sub {
            font-size: 13px;
            color: #3ca0a3;
            margin-bottom: 24px;
        }

        /* Step 1 buttons */
        .lens-step1-btns {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 320px;
            margin: 0 auto;
        }

        .lens-main-btn {
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: #1c3a5e;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .lens-main-btn:hover {
            background: #142d4c;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(28, 58, 94, 0.3);
        }

        .lens-frame-btn {
            padding: 14px;
            border: 1.5px solid #1c3a5e;
            border-radius: 12px;
            background: #fff;
            color: #1c3a5e;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .lens-frame-btn:hover {
            background: #f0f5ff;
        }

        /* Step 2 lens type grid */
        .lens-type-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 10px;
        }

        @media(max-width:600px) {
            .lens-type-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .lens-popup-body {
                padding: 30px 15px;
            }
        }

        .lens-type-card {
            border: 1.5px solid #e0e0e0;
            border-radius: 14px;
            padding: 16px 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }

        .lens-type-card:hover {
            border-color: #1c3a5e;
            background: #f5f8ff;
            transform: translateY(-2px);
        }

        .lens-type-card.active {
            border-color: #1c3a5e;
            box-shadow: 0 0 0 1.5px #1c3a5e;
        }

        .lens-type-icon img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .lens-type-name {
            font-size: 15px;
            font-weight: 700;
            color: #1c2b4a;
            margin: 8px 0 2px;
        }

        .lens-type-desc {
            font-size: 11px;
            color: #3ca0a3;
            margin: 0;
        }

        /* Step 3 package cards */
        .lens-package-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 8px;
        }

        .lens-package-card {
            /* display: flex; */
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            border: 1.5px solid #eee;
            border-radius: 14px;
            background: #fff;
            transition: all 0.2s ease;
        }

        .lens-pkg-left {
            text-align: left;
        }

        .lens-pkg-badge {
            display: inline-block;
            background: #fff3e0;
            color: #e65100;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 50px;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .lens-pkg-name {
            font-size: 14px;
            font-weight: 700;
            color: #1c2b4a;
            margin: 0 0 3px;
        }

        .lens-pkg-features {
            font-size: 11px;
            color: #999;
            margin: 0;
        }

        .lens-pkg-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
            flex-shrink: 0;
        }

        .lens-pkg-price {
            font-size: 18px;
            font-weight: 800;
            color: #1c3a5e;
        }

        .lens-pkg-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: #1c3a5e;
            color: #fff;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .lens-pkg-btn:hover {
            background: #142d4c;
            transform: scale(1.1);
        }

        /* Select Lenses button pulse */
        #main-action-btn.select-lenses-mode {
            animation: pulse-btn 2s infinite;
        }

        @keyframes pulse-btn {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(28, 58, 94, 0.4);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(28, 58, 94, 0);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            window.lightbox = GLightbox({
                selector: '.glightbox'
            });

             window.imageGallery = GLightbox({
                selector: '.image-lightbox'
            });
        });
        /* ══════════════════════════════
                           PRODUCT TYPE TAB SWITCHER
                        ══════════════════════════════ */
        function selectProductType(el) {
    document.querySelectorAll('.ptype-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');

    // Center active tab in scroll container
    const container = el.closest('.product-type-tabs');

    const scrollLeft =
        el.offsetLeft -
        (container.offsetWidth / 2) +
        (el.offsetWidth / 2);

    container.scrollTo({
        left: scrollLeft,
        behavior: 'smooth'
    });

    const typeId = el.dataset.typeId;
    const hasPower = el.dataset.hasPower === '1';

    document.querySelectorAll('.power-chips-wrap').forEach(p => p.classList.add('d-none'));

    if (hasPower) {
        const panel = document.getElementById('powers-' + typeId);
        if (panel) panel.classList.remove('d-none');
    }

    const btn = document.getElementById('main-action-btn');
    if (btn) {
        if (hasPower) {
            btn.textContent = 'Select Lenses';
            btn.classList.add('select-lenses-mode');
            btn.dataset.hasPower = '1';
        } else {
            btn.textContent = 'BUY NOW';
            btn.classList.remove('select-lenses-mode');
            btn.dataset.hasPower = '0';
        }
    }
}

        $('#main-action-btn').on('click', function() {
            const hasPower = this.dataset.hasPower === '1';

            if (!hasPower) {
                addToCartAjax('Frame Only', null, null, null);
            } else {
                const lensModal = new bootstrap.Modal(
                    document.getElementById('lensModal')
                );
                lensModal.show();
            }
        });

        // Helper function when select button in details modal is clicked
        function selectLensFromDetails(packageId, isFree) {
            const detailsModalEl = document.getElementById('viewDetailsModal');
            const detailsModal = bootstrap.Modal.getInstance(detailsModalEl) || new bootstrap.Modal(detailsModalEl);
            if (detailsModal) {
                detailsModal.hide();
            }

            // Execute selecting package
            selectLensPackage(packageId, isFree);
        }

        $('.view-details').on('click', function() {
            const btn = $(this);
            const packageId = btn.data('id');
            const name = btn.data('name');
            const price = btn.data('price');
            const originalPrice = btn.data('original-price');
            const desc = btn.data('desc');
            const free = btn.data('free') === true || btn.data('free') === 'true';
            const coupon = btn.data('coupon');
            const benefits = btn.data('benefits') || [];
            const media = btn.data('media') || [];
            const pkgType = btn.data('type') || (free ? 'frame_only' : 'frame_and_lens');

            let modeLabel = 'FRAME + LENS';
            if (pkgType === 'free_lens' || pkgType === 'frame_only' || free) {
                modeLabel = 'FREE LENS (PAY FRAME)';
            } else if (pkgType === 'free_frame') {
                modeLabel = 'FREE FRAME (PAY LENS)';
            } else if (pkgType === 'lens_only') {
                modeLabel = 'LENS ONLY';
            }

            // Update modal content
            const modal = $('#viewDetailsModal');
            modal.find('.lens-content h3').text(name);
            modal.find('.lens-desc').text(desc);
            modal.find('.lens-price-group .price, .lens-bottom-bar .price').text(price);
            modal.find('.lens-price-mode-label').text(modeLabel);

            if (originalPrice) {
                modal.find('.lens-price-group del, .lens-bottom-bar del').text(originalPrice).show();
            } else {
                modal.find('.lens-price-group del, .lens-bottom-bar del').hide();
            }

            if (coupon) {
                modal.find('.lens-coupon-badge span').text(coupon);
                modal.find('.lens-coupon-badge').show();
            } else {
                modal.find('.lens-coupon-badge').hide();
            }

            // Populate benefits list and features list
            const benefitsList = modal.find('.benefits-list');
            const featuresList = modal.find('.features-list');
            
            benefitsList.empty();
            featuresList.empty();
            
            if (benefits.length === 0) {
                modal.find('.benefits-heading').hide();
                modal.find('.features-heading').hide();
            } else {
                modal.find('.benefits-heading').show();
                modal.find('.features-heading').show();
                
                benefits.forEach(benefit => {
                    // Benefit list (small text with icon)
                    let iconHtml = benefit.icon_image 
                        ? `<img src="${benefit.icon_image}" style="width: 16px; height: 16px;">` 
                        : `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5C5.636 5 2 12 2 12s3.636 7 10 7 10-7 10-7-3.636-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
                    
                    benefitsList.append(`
                        <li>
                            ${iconHtml}
                            ${benefit.name}
                        </li>
                    `);
    
                    // Features list (large cards with images)
                    let imgUrl = '';
                    if (benefit.icon_image) {
                        imgUrl = benefit.icon_image;
                    } else {
                        const nameLower = benefit.name.toLowerCase();
                        if (nameLower.includes('anti-glare') || nameLower.includes('antiglare')) {
                            imgUrl = 'https://static5.lenskart.com/media/uploads/Antiglare_1_updated.png';
                        } else if (nameLower.includes('scratch')) {
                            imgUrl = 'https://static5.lenskart.com/media/uploads/Stratch_Resistant.png';
                        } else if (nameLower.includes('smudge')) {
                            imgUrl = 'https://static5.lenskart.com/media/uploads/Smudge_Resistant.png';
                        } else if (nameLower.includes('screen') || nameLower.includes('eyestrain') || nameLower.includes('blue')) {
                            imgUrl = 'https://static5.lenskart.com/media/uploads/ZeroPowerComputer.png';
                        } else {
                            imgUrl = 'https://static5.lenskart.com/media/uploads/Antiglare_1_updated.png';
                        }
                    }
    
                    featuresList.append(`
                        <li class="benefit-item-card">
                            <a class="benefit-img-wrapper glightbox"
                            data-gallery="benefits-gallery"
                            href="${imgUrl}">
                                <img src="${imgUrl}" alt="${benefit.name}">
                            </a>
                            <div class="benefit-info-wrapper">
                                <span class="benefit-title">${benefit.name}</span>
                            </div>
                        </li>
                    `);
                });
            }

            lightbox.destroy();

            lightbox = GLightbox({
                selector: '.glightbox'
            });
            
            // Update action button select action
            modal.find('.select-lens-btn').off('click').on('click', function() {
                selectLensFromDetails(packageId, free);
            });

            // Dynamically set carousel images based on package name or slug
            const carouselInner = modal.find('.carousel-inner');
            carouselInner.empty();

            let images = [];
            
            if (media && media.length > 0) {
                // Use dynamically passed media from the Lens Package
                images = media;
            } else {
                // Fallback to old hardcoded logic if no media exists
                const frameImgUrl = $('.main-image').attr('src') || '';
                if (frameImgUrl) {
                    images.push(frameImgUrl);
                }
    
                let lensImages = [
                    'https://static5.lenskart.com/media/uploads/Antiglare_1_updated.png',
                    'https://static5.lenskart.com/media/uploads/Stratch_Resistant.png'
                ];
    
                const nameLower = name.toLowerCase();
                if (nameLower.includes('blu') || nameLower.includes('blue')) {
                    lensImages = [
                        'https://static5.lenskart.com/media/uploads/ZeroPowerComputer.png',
                        'https://static5.lenskart.com/media/uploads/ZeroPowerComputer.png'
                    ];
                } else if (nameLower.includes('japan') || nameLower.includes('owndays')) {
                    lensImages = [
                        'https://static5.lenskart.com/media/uploads/Dual_Power.png',
                        'https://static5.lenskart.com/media/uploads/ProgressiveBifocal.png'
                    ];
                }
    
                images = images.concat(lensImages);
            }

            images.forEach((imgUrl, idx) => {
                carouselInner.append(`
                    <div class="carousel-item ${idx === 0 ? 'active' : ''}">
                        <img src="${imgUrl}" class="d-block w-100">
                    </div>
                `);
            });

            // Rebuild progress indicators
            const carouselProgress = modal.find('.carousel-progress');
            carouselProgress.empty();
            images.forEach((imgUrl, idx) => {
                carouselProgress.append(`
                    <span class="progress-item ${idx === 0 ? 'active' : ''}">
                        <span class="progress-fill"></span>
                    </span>
                `);
            });

            // Show details modal
            const detailsModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
            detailsModal.show();
        });

        /* ══════════════════════════════
           LENS MODAL STEP MANAGEMENT
        ══════════════════════════════ */
        let currentLensStep = 1;

        function showLensStep(step) {
            // Hide all steps
            document.querySelectorAll('.lens-step').forEach(s => s.style.display = 'none');
            // Show target step
            const target = document.getElementById('step' + step);
            if (target) target.style.display = 'block';

            // Update dots
            document.querySelectorAll('.step-dot').forEach((d, i) => {
                d.classList.toggle('active', i + 1 === step);
            });

            // Show/hide back button
            const prevBtn = document.getElementById('goPrev');
            if (prevBtn) prevBtn.style.visibility = step > 1 ? 'visible' : 'hidden';

            currentLensStep = step;
        }

        function selectLensType(card, nextStep) {
            selectedPowerTypeId = $(card).data('power-type-id');
            const nameEl = card.querySelector('.lens-type-name');
            selectedLensType = nameEl ? nameEl.textContent.trim() : 'Frame Only';

            console.log('Lens Type Selected:', selectedLensType, 'ID:', selectedPowerTypeId);

            document.querySelectorAll('.lens-type-card')
                .forEach(c => c.classList.remove('active'));
            card.classList.add('active');

            // If selectedLensType is "Frame Only", add directly to cart!
            if (selectedLensType.toLowerCase() === 'frame only' || selectedLensType.toLowerCase() === 'only frame') {
                addToCartAjax('Frame Only', null, null, null);
                return;
            }

            // Adjust step 4 indicator visibility based on Zero Power
            if (selectedLensType === 'Zero Power') {
                $('.lens-steps-indicator #dot4').addClass('d-none');
            } else {
                $('.lens-steps-indicator #dot4').removeClass('d-none');
            }

            // Filter packages list based on selected power type and active filter tag
            filterLensPackages();

            setTimeout(() => showLensStep(nextStep), 220);
        }

        function filterLensPackages() {
            const activeTag = $('.lens-filter.active').data('slug') || 'all';
            
            $('.lens-package-card').each(function() {
                const card = $(this);
                const cardPowerTypes = card.data('power-types') ? String(card.data('power-types')).split(',') : [];
                const cardTags = card.data('tags') ? String(card.data('tags')).split(',') : [];
                
                const matchesPowerType = selectedPowerTypeId ? cardPowerTypes.includes(String(selectedPowerTypeId)) : true;
                const matchesTag = (activeTag === 'all') ? true : cardTags.includes(activeTag);
                
                if (matchesPowerType && matchesTag) {
                    card.show();
                } else {
                    card.hide();
                }
            });
            
            // Show "No packages available" message if all are hidden
            if ($('.lens-package-card:visible').length === 0) {
                if ($('#no-packages-msg').length === 0) {
                    $('.lens-package-list').append('<p id="no-packages-msg" class="text-muted text-center py-4 w-100">No lens packages available for this power type and category.</p>');
                } else {
                    $('#no-packages-msg').show();
                }
            } else {
                $('#no-packages-msg').hide();
            }
        }

        /* ── Lens modal open / close ── */
        document.addEventListener('DOMContentLoaded', function() {
            const lensModal = document.getElementById('lensModal');
            if (!lensModal) return;

            lensModal.addEventListener('show.bs.modal', () => {
                showLensStep(1);
                backToPowerOptions();
            });
            lensModal.addEventListener('hidden.bs.modal', () => {
                document.querySelectorAll('.lens-type-card').forEach(c => c.classList.remove('active'));
            });

            // "Buy with Lenses" button → Step 2
            const goStep2 = document.getElementById('goStep2');
            if (goStep2) goStep2.addEventListener('click', () => showLensStep(2));

            // Back button
            const goPrev = document.getElementById('goPrev');
            if (goPrev) {
                goPrev.addEventListener('click', () => {
                    if (currentLensStep === 4) {
                        backToPowerOptions();
                        showLensStep(3);
                    } else if (currentLensStep === 3) {
                        $('.lens-steps-indicator #dot4').removeClass('d-none');
                        showLensStep(2);
                    } else if (currentLensStep === 2) {
                        showLensStep(1);
                    }
                });
            }
        });

        /* ── Power chip single-select ── */
        document.addEventListener('DOMContentLoaded', function() {

            $('#viewDetailsModal').on('shown.bs.modal', function() {
                $(this).addClass('details-backdrop');
            });

            $('#viewDetailsModal').on('hidden.bs.modal', function() {
                $(this).removeClass('details-backdrop');
            });
            const carousel = document.getElementById('lensBannerCarousel');

            carousel.addEventListener('slide.bs.carousel', function(e) {

                document.querySelectorAll('.progress-item')
                    .forEach(item => {
                        item.classList.remove('active');
                        item.querySelector('.progress-fill').style.width = '0';
                    });

                const current =
                    document.querySelectorAll('.progress-item')[e.to];

                current.classList.add('active');
            });

            document.querySelectorAll('.power-chips-wrap').forEach(wrap => {
                wrap.querySelectorAll('.power-chip').forEach(chip => {
                    chip.addEventListener('click', function() {
                        wrap.querySelectorAll('.power-chip').forEach(c => c.classList
                            .remove('active'));
                        this.classList.add('active');
                    });
                });
            });
        });

        $('.lens-filter').on('click', function() {
            $('.lens-filter').removeClass('active');
            $(this).addClass('active');
            filterLensPackages();
        });

        document.getElementById('rx_file').addEventListener('change', function () {

            const file = this.files[0];

            if (file) {
                document.querySelector('.file-name').textContent = file.name;
                document.getElementById('selected-file').classList.remove('d-none');
            }else{
                document.querySelector('.file-name').textContent = '';
                document.getElementById('selected-file').classList.add('d-none');
            }
        });

    </script>

@endsection