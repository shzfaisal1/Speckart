@extends('website.layout.master')

@section('content')

{{-- ═══════════════════════════════════════════════════
     SPECKART — Ultra-Premium Wishlist UI
     Inter Typography · Responsive · Snappy AJAX
═══════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
/* ══════════════════════════════════════
   DESIGN SYSTEM TOKENS & RESET
══════════════════════════════════════ */
:root {
    --wl-primary: #329a9a;
    --wl-primary-dark: #277878;
    --wl-primary-soft: #eef7f7;
    --wl-bg: #f8fafc;
    --wl-card-bg: #ffffff;
    --wl-text: #111827;
    --wl-text-secondary: #4b5563;
    --wl-text-muted: #9ca3af;
    --wl-border: #e5e7eb;
    --wl-radius: 14px;
    --wl-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    --wl-shadow-hover: 0 14px 28px -4px rgba(0, 0, 0, 0.08), 0 4px 12px -2px rgba(50, 154, 154, 0.06);
    --wl-transition: all 0.22s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.wl-page, .wl-page * {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    box-sizing: border-box;
}

.wl-page {
    background: var(--wl-bg);
    min-height: 70vh;
    padding-bottom: 60px;
}

.wl-container {
    max-width: 1440px;
    margin: 0 auto;
    padding: 0 20px;
}
@media (max-width: 576px) {
    .wl-container { padding: 0 12px; }
}

/* ══════════════════════════════════════
   BREADCRUMBS SECTION
══════════════════════════════════════ */
.wl-breadcrumb-wrap {
    background: #ffffff;
    border-bottom: 1px solid var(--wl-border);
    padding: 12px 0;
}
.wl-breadcrumb {
    margin-bottom: 0;
    font-size: 13px;
    font-weight: 500;
}
.wl-breadcrumb a {
    color: var(--wl-text-secondary);
    text-decoration: none;
    transition: var(--wl-transition);
}
.wl-breadcrumb a:hover {
    color: var(--wl-primary);
}
.wl-breadcrumb .breadcrumb-item.active {
    color: var(--wl-primary-dark);
    font-weight: 700;
}

/* ══════════════════════════════════════
   HEADER STRIP
══════════════════════════════════════ */
.wl-header-strip {
    background: #ffffff;
    border-bottom: 1px solid var(--wl-border);
    padding: 24px 0;
    margin-bottom: 24px;
}
.wl-title-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.wl-title {
    font-size: 24px;
    font-weight: 800;
    color: var(--wl-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.4px;
}
.wl-title i {
    color: #ef4444;
    font-size: 22px;
}
.wl-count-badge {
    background: var(--wl-primary-soft);
    color: var(--wl-primary-dark);
    border: 1px solid #d5eded;
    font-size: 12.5px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
}

/* ══════════════════════════════════════
   WISHLIST CARD
══════════════════════════════════════ */
.wl-card {
    position: relative;
    background: #ffffff;
    border-radius: var(--wl-radius);
    border: 1px solid var(--wl-border);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: var(--wl-transition);
}
.wl-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--wl-shadow-hover);
    border-color: var(--wl-primary);
}

.wl-img-wrap {
    position: relative;
    background: #ffffff;
    padding: 12px 14px 4px 14px;
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.wl-img-wrap img {
    max-height: 115px;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    transition: transform 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.wl-card:hover .wl-img-wrap img {
    transform: scale(1.05);
}

/* Remove button */
.btn-remove-wishlist {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(229, 231, 235, 0.8);
    color: #9ca3af;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 5;
    transition: var(--wl-transition);
    font-size: 14px;
}
.btn-remove-wishlist:hover {
    background: #ef4444;
    color: #ffffff;
    border-color: #ef4444;
    transform: scale(1.08);
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
}

/* Content */
.wl-content {
    padding: 10px 14px 14px 14px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #ffffff;
}
.wl-brand {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    color: var(--wl-primary);
    margin-bottom: 3px;
}
.wl-title-text {
    font-size: 13px;
    font-weight: 700;
    color: var(--wl-text);
    line-height: 1.35;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 34px;
    transition: color 0.2s ease;
}
.wl-card:hover .wl-title-text {
    color: var(--wl-primary-dark);
}
.wl-price-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 8px;
    border-top: 1px dashed var(--wl-border);
    margin-bottom: 10px;
}
.wl-price {
    font-size: 16.5px;
    font-weight: 800;
    color: var(--wl-text);
    letter-spacing: -0.4px;
}

/* Add to cart CTA */
.btn-add-to-cart {
    width: 100%;
    background: linear-gradient(135deg, var(--wl-primary), var(--wl-primary-dark));
    color: #ffffff;
    border: none;
    border-radius: 9px;
    padding: 8px 12px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: var(--wl-transition);
    box-shadow: 0 2px 6px rgba(50, 154, 154, 0.25);
}
.btn-add-to-cart:hover {
    background: linear-gradient(135deg, #3bb3b3, #1f6565);
    box-shadow: 0 4px 12px rgba(50, 154, 154, 0.4);
    transform: translateY(-1px);
    color: #ffffff;
}
.btn-add-to-cart:disabled {
    opacity: 0.75;
    cursor: not-allowed;
}

/* Animation on remove */
.wishlist-item-col {
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.wishlist-item-col.removing {
    opacity: 0;
    transform: scale(0.9);
}

/* ══════════════════════════════════════
   EMPTY WISHLIST STATE
══════════════════════════════════════ */
.wl-empty-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid var(--wl-border);
    padding: 60px 24px;
    text-align: center;
    box-shadow: var(--wl-shadow);
    max-width: 520px;
    margin: 40px auto 0;
}
.wl-empty-icon-wrap {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #fef2f2;
    color: #ef4444;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin: 0 auto 20px;
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.15);
}
.wl-empty-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--wl-text);
    margin-bottom: 8px;
}
.wl-empty-desc {
    font-size: 14px;
    color: var(--wl-text-secondary);
    max-width: 360px;
    margin: 0 auto 24px;
    line-height: 1.5;
}
.btn-wl-shop {
    background: linear-gradient(135deg, var(--wl-primary), var(--wl-primary-dark));
    color: #ffffff;
    text-decoration: none;
    padding: 11px 28px;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(50, 154, 154, 0.3);
    transition: var(--wl-transition);
}
.btn-wl-shop:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(50, 154, 154, 0.45);
}
</style>

<div class="wl-page">

    {{-- BREADCRUMBS SECTION --}}
    <div class="wl-breadcrumb-wrap">
        <div class="wl-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb wl-breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- HEADER STRIP --}}
    <div class="wl-header-strip">
        <div class="wl-container">
            <div class="wl-title-wrap">
                <div>
                    <h1 class="wl-title">
                        <i class="bi bi-heart-fill"></i> My Saved Wishlist
                    </h1>
                </div>
                <div>
                    <span class="wl-count-badge" id="wishlist-title-count">
                        {{ $wishlistItems->count() }} Saved {{ Str::plural('Item', $wishlistItems->count()) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN WISHLIST GRID --}}
    <div class="wl-container">
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4" id="wishlist-items-container">
            @forelse($wishlistItems as $item)
                @if($item->product)
                <div class="col wishlist-item-col" data-wishlist-id="{{ $item->id }}">
                    <div class="wl-card">
                        {{-- Image & Remove Button --}}
                        <div class="wl-img-wrap">
                            <button class="btn-remove-wishlist" data-wishlist-id="{{ $item->id }}" title="Remove from Wishlist">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                            <a href="{{ $item->product->resolved_detail_url }}" class="d-block w-100 text-center">
                                <img src="{{ $item->product->resolved_image_url }}" alt="{{ $item->product->resolved_name }}">
                            </a>
                        </div>

                        {{-- Product Content --}}
                        <div class="wl-content">
                            <div>
                                <div class="wl-brand">{{ $item->product->resolved_brand }}</div>
                                <a href="{{ $item->product->resolved_detail_url }}" class="text-decoration-none">
                                    <h5 class="wl-title-text">{{ $item->product->resolved_name }}</h5>
                                </a>
                            </div>

                            <div>
                                <div class="wl-price-row">
                                    <span class="wl-price">₹{{ number_format($item->product->resolved_price, 0) }}</span>
                                </div>

                                <button class="btn-add-to-cart"
                                        data-product-id="{{ $item->product->product_id ?: $item->product->id }}"
                                        data-product-name="{{ $item->product->resolved_name }}">
                                    <i class="bi bi-cart-plus-fill fs-6"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                {{-- Empty state rendered below --}}
            @endforelse
        </div>

        {{-- EMPTY STATE CARD --}}
        <div id="wishlist-empty-state" class="{{ $wishlistItems->count() > 0 ? 'd-none' : '' }}">
            <div class="wl-empty-card">
                <div class="wl-empty-icon-wrap">
                    <i class="bi bi-heart"></i>
                </div>
                <h4 class="wl-empty-title">Your Wishlist is Empty</h4>
                <p class="wl-empty-desc">Explore our premium catalog of eyeglasses, sunglasses, and contact lenses to save your favorite styles!</p>
                <a href="{{ route('products') }}" class="btn-wl-shop">
                    Explore Catalog <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            // ──────────────────────────────────────────────
            // Remove from Wishlist (AJAX DELETE)
            // ──────────────────────────────────────────────
            $(document).on('click', '.btn-remove-wishlist', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var wishlistId = $btn.data('wishlist-id');
                var $col = $btn.closest('.wishlist-item-col');

                $btn.prop('disabled', true);

                $.ajax({
                    url: '/wishlist/' + wishlistId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: csrfToken
                    },
                    success: function(response) {
                        // Fade & scale out animation
                        $col.addClass('removing');
                        setTimeout(function() {
                            $col.remove();

                            // Update header badge
                            var remaining = $('.wishlist-item-col').length;
                            var itemText = remaining === 1 ? '1 Saved Item' : remaining + ' Saved Items';
                            $('#wishlist-title-count').text(itemText);

                            // Update header navigation badge
                            updateWishlistBadges(response.count);

                            // Show empty state if no items remain
                            if (remaining === 0) {
                                $('#wishlist-empty-state').removeClass('d-none');
                            }
                        }, 300);

                        if (typeof toastr !== 'undefined') {
                            toastr.warning(response.message || 'Removed from wishlist');
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Could not remove item. Please try again.');
                        }
                    }
                });
            });

            // ──────────────────────────────────────────────
            // Add to Cart from Wishlist (AJAX POST)
            // ──────────────────────────────────────────────
            $(document).on('click', '.btn-add-to-cart', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var productId = $btn.data('product-id');
                var productName = $btn.data('product-name');
                var $col = $btn.closest('.wishlist-item-col');
                var originalHtml = $btn.html();

                $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Adding...');

                $.ajax({
                    url: '{{ route('cart.add') }}',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        frame_id: productId,
                        quantity: 1
                    },
                    success: function(response) {
                        if (response.status === 'success' || response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Added to cart!');
                            }
                            if (response.cart_count !== undefined) {
                                updateCartBadges(response.cart_count);
                            }
                            if (response.wishlist_count !== undefined) {
                                updateWishlistBadges(response.wishlist_count);
                            }

                            $btn.html('<i class="bi bi-check-circle-fill me-1"></i> Added!');

                            // Smoothly animate and remove card from Wishlist UI
                            setTimeout(function() {
                                $col.addClass('removing');
                                setTimeout(function() {
                                    $col.remove();

                                    var remaining = $('.wishlist-item-col').length;
                                    var itemText = remaining === 1 ? '1 Saved Item' : remaining + ' Saved Items';
                                    $('#wishlist-title-count').text(itemText);

                                    if (remaining === 0) {
                                        $('#wishlist-empty-state').removeClass('d-none');
                                    }
                                }, 300);
                            }, 500);
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Could not add to cart.');
                            }
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function() {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('An error occurred. Please try again.');
                        }
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // ──────────────────────────────────────────────
            // Header Badge Update Helpers
            // ──────────────────────────────────────────────
            function updateWishlistBadges(count) {
                var $badges = $('.wishlist-badge');
                if (count > 0) {
                    $badges.text(count).removeClass('d-none');
                } else {
                    $badges.addClass('d-none').text(0);
                }
            }

            function updateCartBadges(count) {
                var $badges = $('.cart-badge');
                if (count > 0) {
                    $badges.text(count).removeClass('d-none');
                } else {
                    $badges.addClass('d-none').text(0);
                }
            }
        });
    </script>
@endsection
