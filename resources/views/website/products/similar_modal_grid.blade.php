@if(isset($similarProducts) && $similarProducts->count() > 0)
    <div class="row gy-3 gx-3">
        @foreach($similarProducts as $simProduct)
        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="product-card">
                {{-- TOP OVERLAY: Rating Badge (Left) & Wishlist Button (Right) --}}
                <div class="card-top-overlay">
                    <div class="card-rating-pill">
                        <i class="bi bi-star-fill"></i> 4.5
                    </div>
                    <div class="wishlist-btn btn-wishlist-toggle" data-product-id="{{ $simProduct->product_id ?: $simProduct->id }}" data-wishlist-product-id="{{ $simProduct->product_id ?: $simProduct->id }}" title="Save to wishlist">
                        <i class="bi {{ in_array($simProduct->product_id ?: $simProduct->id, $wishlistProductIds ?? []) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                    </div>
                </div>

                {{-- PRODUCT LINK & IMAGE --}}
                <a href="{{ $simProduct->detail_url }}" class="product-card-link text-decoration-none">
                    <div class="product-image">
                        <img src="{{ $simProduct->image_url }}" alt="{{ $simProduct->product_name ?: $simProduct->product_code }}" class="img-default" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Sunglasses1.png') }}';">
                    </div>
                </a>

                {{-- SUB-IMAGE UTILITY BAR: Color Options --}}
                <div class="sub-image-bar">
                    <span class="text-muted small fw-medium">
                        {{ $simProduct->Shape ?: ($simProduct->Rim_Type ?: 'Eyewear') }}
                    </span>

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
                        $rawList = (!empty($simProduct->color_variants_list) && count($simProduct->color_variants_list) > 0)
                            ? $simProduct->color_variants_list
                            : [$simProduct];

                        foreach ($rawList as $varObj) {
                            $variantsToDisplay[] = [
                                'id'             => $varObj->id,
                                'product_id'     => $varObj->product_id ?: $varObj->id,
                                'color'          => $varObj->Color ?? '',
                                'swatch_bg'      => $resolveSwatchBg($varObj->Color ?? ''),
                                'image_url'      => $varObj->image_url ?? $simProduct->image_url,
                                'detail_url'     => $varObj->detail_url ?? $simProduct->detail_url,
                                'name'           => $varObj->product_name ?: $varObj->product_code,
                                'brand'          => $varObj->Company ?: 'Speckart',
                                'size'           => $varObj->Size ?: 'Medium',
                                'retail_price'   => (float)($varObj->Retail_Price ?? 0),
                                'purchase_price' => (float)($varObj->Purchase_Price ?? 0),
                                'discount_price' => (float)($varObj->discount_price ?? 0),
                                'is_current'     => ($varObj->id == $simProduct->id),
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
                <a href="{{ $simProduct->detail_url }}" class="product-card-link text-decoration-none">
                    <div class="product-info">
                        <div class="brand-name">{{ $simProduct->Company ?: 'Speckart' }}</div>
                        <h6 class="product-title">{{ $simProduct->product_name ?: $simProduct->product_code }}</h6>

                        <div class="size-display-row">
                            <span class="size-label">Size : </span><span class="size-val">{{ $simProduct->Size ?: 'Medium' }}</span>
                        </div>

                        @php
                            $p1 = (float)($simProduct->Retail_Price ?? 0);
                            $p2 = (float)($simProduct->Purchase_Price ?? 0);
                            $dPrice = (float)($simProduct->discount_price ?? 0);

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
                @if($hasDiscount)
                <div class="card-bottom-banner">
                    <i class="bi bi-percent-circle-fill"></i> on sale price applied!
                </div>
                @else
                <div class="card-bottom-banner" style="display:none;">
                    <i class="bi bi-percent-circle-fill"></i> on sale price applied!
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-box-seam text-muted" style="font-size: 40px;"></i>
        <h6 class="mt-3 fw-bold text-dark">No other similar products found</h6>
        <p class="text-muted small">Explore our full eyewear catalog.</p>
    </div>
@endif