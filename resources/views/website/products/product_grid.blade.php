<div class="row gy-3 gx-3" id="product-container">
    @if(isset($productsList) && $productsList->count() > 0)
        @foreach($productsList as $product)
        <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
            <div class="product-card">
                {{-- TOP OVERLAY: Rating Badge (Left) & Wishlist Button (Right) --}}
                <div class="card-top-overlay">
                    <div class="card-rating-pill">
                        <i class="bi bi-star-fill"></i> 4.5
                    </div>
                    <div class="wishlist-btn btn-wishlist-toggle" data-product-id="{{ $product->product_id ?: $product->id }}" data-wishlist-product-id="{{ $product->product_id ?: $product->id }}" title="Save to wishlist">
                        <i class="bi {{ in_array($product->product_id ?: $product->id, $wishlistProductIds ?? []) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                    </div>
                </div>

                {{-- PRODUCT LINK & IMAGE --}}
                <a href="{{ $product->detail_url }}" class="product-card-link text-decoration-none">
                    <div class="product-image">
                        <img src="{{ $product->image_url }}" alt="{{ $product->product_name ?: $product->product_code }}" class="img-default" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Sunglasses1.png') }}';">
                    </div>
                </a>

                {{-- SUB-IMAGE UTILITY BAR: View Similar & Color Options --}}
                <div class="sub-image-bar">
                    <button type="button" class="view-similar-btn btn-open-similar-modal" data-product-id="{{ $product->product_id ?: $product->id }}" data-product-name="{{ $product->product_name ?: $product->product_code }}" data-product-brand="{{ $product->Company ?: 'Speckart' }}" title="View Similar Products">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="14"
                            height="14"
                            viewBox="6 5 12 10"
                            fill="none"
                            >
                            <path
                                d="M15.0002 13.5L15.4684 13.5425C15.7496 13.5973 16.0193 13.4051 16.0591 13.1213L16.8179 7.72263C16.8573 7.44197 16.6558 7.18466 16.3739 7.15567L15.2502 7M9.00024 13.5L8.42739 13.5557C8.14925 13.6036 7.88656 13.412 7.84728 13.1325L7.08697 7.72263C7.04752 7.44197 7.24901 7.18466 7.53094 7.15567L8.75024 7M10.5001 14H13.5001C13.7763 14 14.0001 13.7761 14.0001 13.5V6.5C14.0001 6.22386 13.7763 6 13.5001 6H10.5001C10.224 6 10.0001 6.22386 10.0001 6.5V13.5C10.0001 13.7761 10.224 14 10.5001 14Z"
                                stroke="#333368"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            </svg> View Similar
                    </button>

                    @php
                        $cardColorPalette = [
                            'Black' => '#111827', 'Charcoal' => '#374151', 'Grey' => '#6B7280', 'Gray' => '#6B7280',
                            'Silver' => '#CBD5E1', 'White' => '#FFFFFF', 'Maroon' => '#7F1D1D', 'Red' => '#DC2626',
                            'Rose' => '#FB7185', 'Pink' => '#EC4899', 'Purple' => '#7C3AED', 'Navy Blue' => '#1E3A8A',
                            'Blue' => '#2563EB', 'Cyan' => '#06B6D4', 'Teal' => '#0D9488', 'Turquoise' => '#21E3C6',
                            'Green' => '#16A34A', 'Olive' => '#84CC16', 'Lime' => '#A3E635', 'Gold' => '#D97706',
                            'Yellow' => '#EAB308', 'Orange' => '#EA580C', 'Brown' => '#78350F', 'Tortoise' => '#B45309',
                        ];

                        $resolveSwatchBg = function($colorStr) use ($cardColorPalette) {
                            $c = trim($colorStr ?? '');
                            if (empty($c)) return '#111827';
                            if (strpos($c, '/') !== false) {
                                $parts = array_map('trim', explode('/', $c));
                                $p1 = $parts[0] ?? '#111827';
                                $p2 = $parts[1] ?? $p1;
                                $c1 = str_starts_with($p1, '#') ? $p1 : ($cardColorPalette[ucfirst(strtolower($p1))] ?? '#111827');
                                $c2 = str_starts_with($p2, '#') ? $p2 : ($cardColorPalette[ucfirst(strtolower($p2))] ?? '#6B7280');
                                return "linear-gradient(135deg, {$c1} 50%, {$c2} 50%)";
                            }
                            if (str_starts_with($c, '#')) return $c;
                            return $cardColorPalette[ucfirst(strtolower($c))] ?? '#111827';
                        };

                        $variantsToDisplay = [];
                        $rawList = (!empty($product->color_variants_list) && count($product->color_variants_list) > 0)
                            ? $product->color_variants_list
                            : [$product];

                        foreach ($rawList as $varObj) {
                            $variantsToDisplay[] = [
                                'id'             => $varObj->id,
                                'product_id'     => $varObj->product_id ?: $varObj->id,
                                'color'          => $varObj->Color ?? '',
                                'swatch_bg'      => $resolveSwatchBg($varObj->Color ?? ''),
                                'image_url'      => $varObj->image_url ?? $product->image_url,
                                'detail_url'     => $varObj->detail_url ?? $product->detail_url,
                                'name'           => $varObj->product_name ?: $varObj->product_code,
                                'brand'          => $varObj->Company ?: 'Speckart',
                                'size'           => $varObj->Size ?: 'Medium',
                                'retail_price'   => (float)($varObj->Retail_Price ?? 0),
                                'purchase_price' => (float)($varObj->Purchase_Price ?? 0),
                                'discount_price' => (float)($varObj->discount_price ?? 0),
                                'is_current'     => ($varObj->id == $product->id),
                            ];
                        }

                        $displayVariants = array_slice($variantsToDisplay, 0, 4);
                        $remainingVariantCount = count($variantsToDisplay) - 4;
                    @endphp

                    <div class="color-options-wrap">
                        @foreach($displayVariants as $vItem)
                            <span class="card-color-dot {{ $vItem['is_current'] ? 'active' : '' }}"
                                  style="background: {{ $vItem['swatch_bg'] }};"
                                  data-image-url="{{ $vItem['image_url'] }}"
                                  data-detail-url="{{ $vItem['detail_url'] }}"
                                  data-product-id="{{ $vItem['product_id'] }}"
                                  data-product-name="{{ $vItem['name'] }}"
                                  data-product-brand="{{ $vItem['brand'] }}"
                                  data-size="{{ $vItem['size'] }}"
                                  data-retail-price="{{ $vItem['retail_price'] }}"
                                  data-purchase-price="{{ $vItem['purchase_price'] }}"
                                  data-discount-price="{{ $vItem['discount_price'] }}"
                                  title="{{ $vItem['color'] ?: 'Color option' }}">
                            </span>
                        @endforeach
                        @if($remainingVariantCount > 0)
                            <span class="color-more-count">+{{ $remainingVariantCount }}</span>
                        @endif
                    </div>
                </div>

                {{-- PRODUCT DETAILS --}}
                <a href="{{ $product->detail_url }}" class="product-card-link text-decoration-none">
                    <div class="product-info">
                        <div class="brand-name">{{ $product->Company ?: 'Speckart' }}</div>
                        <h6 class="product-title">{{ $product->product_name ?: $product->product_code }}</h6>

                        <div class="size-display-row">
                            <span class="size-label">Size : </span><span class="size-val">{{ $product->Size ?: 'Medium' }}</span>
                        </div>

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

                        <div class="price-section">
                            <div class="price-main-row">
                                <span class="current-price">₹{{ number_format($calcSellingPrice, 0) }}</span>
                            </div>
                            @if($hasDiscount)
                            <div class="price-strike-row">
                                <span class="original-price">₹{{ number_format($calcMrp, 0) }}</span>
                                <span class="discount-percent">({{ $discountPercent }}% OFF)</span>
                            </div>
                            @else
                            <div class="price-strike-row" style="display:none;">
                                <span class="original-price"></span>
                                <span class="discount-percent"></span>
                            </div>
                            @endif
                        </div>
                    </div>
                </a>

                {{-- BOTTOM ON-SALE / PROMO STRIP --}}
                {{-- @if($hasDiscount)
                <div class="card-bottom-banner">
                    <i class="bi bi-percent-circle-fill"></i> on sale price applied!
                </div>
                @else
                <div class="card-bottom-banner" style="display:none;">
                    <i class="bi bi-percent-circle-fill"></i> on sale price applied!
                </div>
                @endif --}}
            </div>
        </div>
        @endforeach

        <div class="col-12 mt-5 d-flex justify-content-center">
            @if($productsList->hasPages())
            <nav aria-label="Page navigation">
                <ul class="pagination modern-pagination">
                    {{-- Previous Page Link --}}
                    @if ($productsList->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $productsList->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($productsList->getUrlRange(max(1, $productsList->currentPage() - 2), min($productsList->lastPage(), $productsList->currentPage() + 2)) as $page => $url)
                        @if ($page == $productsList->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($productsList->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $productsList->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    @else
        {{-- ══════════════════════════════════════════════════════════════
             CLEAN & MINIMAL "NO EYEWEAR FOUND" EMPTY STATE
        ══════════════════════════════════════════════════════════════ --}}
        <div class="col-12">
            <div class="empty-products-state">
                {{-- Modern Bespoke Optical Search SVG Illustration --}}
                <div class="empty-svg-wrapper">
                    <svg width="200" height="150" viewBox="0 0 220 170" fill="none" xmlns="http://www.w3.org/2000/svg" class="empty-vector-art">
                        <defs>
                            <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#eef8f8" stop-opacity="0.9"/>
                                <stop offset="100%" stop-color="#d5f0f0" stop-opacity="0.5"/>
                            </linearGradient>
                            <linearGradient id="tealGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#329a9a"/>
                                <stop offset="100%" stop-color="#11abb0"/>
                            </linearGradient>
                            <linearGradient id="lensGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#ffffff" stop-opacity="0.8"/>
                                <stop offset="100%" stop-color="#cbebed" stop-opacity="0.4"/>
                            </linearGradient>
                            <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f59e0b"/>
                                <stop offset="100%" stop-color="#fbbf24"/>
                            </linearGradient>
                            <filter id="softShadow" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="6" stdDeviation="8" flood-color="#329a9a" flood-opacity="0.15"/>
                            </filter>
                        </defs>

                        {{-- Ambient Background Soft Circles --}}
                        <circle cx="110" cy="85" r="75" fill="url(#bgGrad)"/>
                        <circle cx="110" cy="85" r="62" stroke="#329a9a" stroke-width="1.5" stroke-dasharray="4 6" opacity="0.35"/>
                        <circle cx="45" cy="40" r="16" fill="#f0fdf4" opacity="0.7"/>
                        <circle cx="178" cy="130" r="22" fill="#fffbeb" opacity="0.6"/>

                        {{-- Eyeglasses Frame Illustration --}}
                        <g transform="translate(36, 45)" filter="url(#softShadow)">
                            {{-- Left Lens --}}
                            <rect x="0" y="8" width="56" height="42" rx="14" fill="url(#lensGrad)" stroke="url(#tealGrad)" stroke-width="3.5"/>
                            <path d="M8 16 L22 16" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.7"/>
                            
                            {{-- Bridge --}}
                            <path d="M56 24 C64 16, 82 16, 90 24" fill="none" stroke="url(#tealGrad)" stroke-width="3.5" stroke-linecap="round"/>

                            {{-- Right Lens --}}
                            <rect x="90" y="8" width="56" height="42" rx="14" fill="url(#lensGrad)" stroke="url(#tealGrad)" stroke-width="3.5"/>
                            <path d="M98 16 L112 16" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.7"/>

                            {{-- Temples / Hinges --}}
                            <path d="M0 20 L-10 18" stroke="url(#tealGrad)" stroke-width="3.5" stroke-linecap="round"/>
                            <path d="M146 20 L156 18" stroke="url(#tealGrad)" stroke-width="3.5" stroke-linecap="round"/>
                        </g>

                        {{-- Floating Magnifying Search Tool with Lens Reflection --}}
                        <g transform="translate(118, 55)" filter="url(#softShadow)">
                            {{-- Glass Handle --}}
                            <path d="M38 38 L62 62" stroke="#07484a" stroke-width="6" stroke-linecap="round"/>
                            <path d="M42 42 L58 58" stroke="#11abb0" stroke-width="2.5" stroke-linecap="round"/>

                            {{-- Outer Ring --}}
                            <circle cx="20" cy="20" r="24" fill="#ffffff" stroke="url(#tealGrad)" stroke-width="4"/>
                            
                            {{-- Inner Magnifier Reflection --}}
                            <circle cx="20" cy="20" r="19" fill="url(#lensGrad)"/>
                            <path d="M10 14 C14 8, 26 8, 30 14" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" opacity="0.9"/>
                            
                            {{-- Search Question / Exclamation Pulse --}}
                            <circle cx="20" cy="20" r="5" fill="url(#tealGrad)"/>
                        </g>

                        {{-- Sparkles / Stars --}}
                        <path d="M32 95 L34 88 L41 86 L34 84 L32 77 L30 84 L23 86 L30 88 Z" fill="url(#accentGrad)"/>
                        <path d="M185 35 L186.5 30 L191.5 28.5 L186.5 27 L185 22 L183.5 27 L178.5 28.5 L183.5 30 Z" fill="#329a9a" opacity="0.8"/>
                        <circle cx="58" cy="130" r="3" fill="#329a9a" opacity="0.4"/>
                        <circle cx="160" cy="30" r="2.5" fill="#f59e0b" opacity="0.6"/>
                    </svg>
                </div>

                <h3 class="empty-title">No Eyewear Found</h3>
                <p class="empty-description">We couldn't find any products matching your search.</p>
            </div>
        </div>

        {{-- Clean Minimalist Styles --}}
        <style>
            .empty-products-state {
                background: #ffffff;
                border-radius: 20px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.04);
                padding: 60px 24px;
                text-align: center;
                margin: 0px 0 10px 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .empty-svg-wrapper {
                margin-bottom: 18px;
                transition: transform 0.3s ease;
            }

            .empty-products-state:hover .empty-svg-wrapper {
                transform: translateY(-3px);
            }

            .empty-title {
                font-size: 22px;
                font-weight: 800;
                color: #07484A;
                margin-bottom: 6px;
                letter-spacing: -0.3px;
            }

            .empty-description {
                font-size: 14px;
                color: #64748b;
                margin: 0;
                line-height: 1.5;
            }

            @media (max-width: 576px) {
                .empty-products-state {
                    padding: 40px 16px;
                    border-radius: 16px;
                }

                .empty-title {
                    font-size: 19px;
                }
            }
        </style>
    @endif
</div>
