<div class="row gy-3 gx-3" id="product-container">
    @if(isset($productsList) && $productsList->count() > 0)
        @foreach($productsList as $product)
        <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
            <div class="product-card">
                    <div class="wishlist-btn btn-wishlist-toggle" data-product-id="{{ $product->product_id ?: $product->id }}" data-wishlist-product-id="{{ $product->product_id ?: $product->id }}">
                        <i
                            class="bi {{ in_array($product->product_id ?: $product->id, $wishlistProductIds ?? []) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                    </div>
                    <a href="{{ $product->detail_url }}" class="product-card-link text-decoration-none">
                        <div class="product-image">
                            <img src="{{ $product->image_url }}" alt="{{ $product->product_name ?: $product->product_code }}" class="img-default">
                        </div>
                        <div class="product-info">
                        <h6 class="brand-name">{{ $product->Company ?: 'Speckart' }}</h6>
                        <p class="product-title">{{ $product->product_name ?: $product->product_code }}</p>

                        <div class="size-rating">
                            <span class="size-text">Size : <span>{{ $product->Size ?: 'Medium' }}</span></span>
                            <div class="rating">
                                <span><i class="bi bi-star-fill"></i></span> 4.5 (210)
                            </div>
                        </div>

                        <div class="price-section">
                            @if(!empty($product->discount_price) && $product->discount_price < $product->Retail_Price)
                                <span class="price">₹{{ number_format($product->discount_price, 2) }}</span>
                                <span class="text-muted text-decoration-line-through ms-2" style="font-size: 13px; font-weight: 500;">₹{{ number_format($product->Retail_Price, 2) }}</span>
                            @else
                                <span class="price">₹{{ number_format($product->Retail_Price, 2) }}</span>
                            @endif
                            <button class="try-btn">Try on you</button>
                        </div>
                    </div>
                    </a>
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
