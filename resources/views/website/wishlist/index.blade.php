@extends('website.layout.master')

@section('content')
    <style>
        .cart-card-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;

        }

        .wishlist-count-badge {
            background: #ecfeff;
            color: #0891b2;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
        }

        .wishlist-card {
            position: relative;
            background: #fff;
            border: 1px solid #bbbbbb87;
            border-radius: 18px;
            overflow: hidden;
            transition: .3s ease;
            height: 100%;
        }

        .wishlist-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .08);
        }

        .card-img-wrap {
            position: relative;
            background: #f8fafc;
            padding: 0;
        }

        .card-img-wrap img {
            width: 100%;
            height: 130px;
            object-fit: contain;
            transition: .4s ease;
        }

        .wishlist-card:hover img {
            transform: scale(1.05);
        }



        .btn-remove-wishlist {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 50%;
            background: #ffffffd4;
            color: #dc3545;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .3s;
        }

        .btn-remove-wishlist:hover {
            background: rgba(255, 0, 0, 0.819);
            color: white;
        }



        .wishlist-content {
            padding: 10px;
        }

        .brand-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #0891b2;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .wishlist-content h5 {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            line-height: 1.5;


            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 10px;


        }

        .wishlist-price {
            display: block;
            font-size: 22px;
            font-weight: 700;
            color: #0f766e;
            margin-bottom: 14px;
        }



        .btn-add-to-cart {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 14px;
            font-weight: 600;
            background: #0ea5a8;
            color: #fff;
            transition: .3s;
        }

        .btn-add-to-cart:hover {
            background: #0d9488;
            transform: translateY(-1px);
        }

        .btn-add-to-cart:disabled {
            opacity: .6;
        }



        .wishlist-item-col {
            transition: all .3s ease;
        }

        .wishlist-item-col.removing {
            opacity: 0;
            transform: scale(.9);
        }



        .wishlist-empty {
            padding: 80px 20px;
            text-align: center;
        }

        .wishlist-empty i {
            font-size: 70px;
            color: #14b8a6;
            margin-bottom: 20px;
        }

        .wishlist-empty h4 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .wishlist-empty p {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .btn-shop {
            background: #14b8a6;
            color: #fff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 999px;
            display: inline-block;
            font-weight: 600;
        }

        .btn-shop:hover @media(max-width:768px) {


            .cart-card {
                padding: 16px;
            }

            .cart-card-title {
                font-size: 20px;
            }

            .card-img-wrap img {
                height: 150px;
            }

            .wishlist-content {
                padding: 14px;
            }

            .wishlist-price {
                font-size: 18px;
            }


        }
    </style>

    <section class="breadcrumbs-section py-3 bg-white border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul id="breadcrumbs" class="m-0 p-0 list-unstyled d-flex align-items-center gap-2"
                        style="font-size: 13px;">
                        <li><a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a></li>
                        <li><i class="bi bi-chevron-right text-muted" style="font-size: 10px;"></i></li>
                        <li class="fw-medium text-decoration-none">Wishlist</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    <div class="container">
        <div class="card my-3 border-0 shadow-lg rounded-4">
            <div class="card-header bg-white my-2">
                <h4 class="cart-card-title d-flex justify-content-between align-items-center ">
                    <span>My Wishlist</span>
                    <span class="wishlist-count-badge" id="wishlist-title-count">{{ $wishlistItems->count() }}
                        Items</span>
                </h4>
            </div>
            <div class="card-body ">
                {{-- <div class="row" id="wishlist-items-container"> --}}
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 gx-2 gx-md-4 gy-2 gy-md-3" id="wishlist-items-container">
                    @forelse($wishlistItems as $item)
                        @if($item->product)
                        <div class="col wishlist-item-col" data-wishlist-id="{{ $item->id }}">
                            <div class="wishlist-card">
                                <div class="card-img-wrap">
                                    <button class="btn-remove-wishlist" data-wishlist-id="{{ $item->id }}"
                                        title="Remove from Wishlist">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <a href="{{ $item->product->resolved_detail_url }}">
                                        <img src="{{ $item->product->resolved_image_url }}" alt="{{ $item->product->resolved_name }}">
                                    </a>
                                </div>
                                <div class="wishlist-content">
                                    <p class="brand-label">{{ $item->product->resolved_brand }}</p>
                                    <h5>{{ $item->product->resolved_name }}</h5>
                                    <span class="wishlist-price">₹{{ number_format($item->product->resolved_price, 2) }}</span>
                                    <div class="wishlist-actions">
                                        <button class="btn-add-to-cart btn w-100 py-2 fw-semibold border-0 text-white rounded-3 d-flex align-items-center justify-content-center gap-2"
                                                style="background:#0ea5a8;"
                                                data-product-id="{{ $item->product->product_id ?: $item->product->id }}"
                                                data-product-name="{{ $item->product->resolved_name }}">
                                            <i class="bi bi-cart-plus fs-6"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        {{-- empty state rendered below --}}
                    @endforelse

                </div>

                {{-- Empty state --}}
                <div id="wishlist-empty-state" class="{{ $wishlistItems->count() > 0 ? 'd-none' : '' }}">
                    <div class="wishlist-empty">
                        <i class="bi bi-heart"></i>
                        <h4>Your Wishlist is Empty</h4>
                        <p>Save the items you love and come back to them anytime.</p>
                        <a href="{{ route('products') }}" class="btn-shop">Start Shopping</a>
                    </div>
                </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            // ──────────────────────────────────────────────
            // Remove from Wishlist
            // ──────────────────────────────────────────────
            $(document).on('click', '.btn-remove-wishlist', function() {
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
                        // Fade & remove card
                        $col.addClass('removing');
                        setTimeout(function() {
                            $col.remove();

                            // Update title badge
                            var remaining = $('.wishlist-item-col').length;
                            $('#wishlist-title-count').text(remaining + ' Items');

                            // Update header badges
                            updateWishlistBadges(response.count);

                            // Show empty state if no items remain
                            if (remaining === 0) {
                                $('#wishlist-empty-state').removeClass('d-none');
                            }
                        }, 400);

                        toastr.warning(response.message || 'Removed from wishlist');
                    },
                    error: function() {
                        $btn.prop('disabled', false);
                        toastr.error('Could not remove item. Please try again.');
                    }
                });
            });

            // ──────────────────────────────────────────────
            // Add to Cart from Wishlist page (Auto-remove from wishlist)
            // ──────────────────────────────────────────────
            $(document).on('click', '.btn-add-to-cart', function() {
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
                            toastr.success(response.message || 'Added to cart!');
                            if (response.cart_count !== undefined) {
                                updateCartBadges(response.cart_count);
                            }
                            if (response.wishlist_count !== undefined) {
                                updateWishlistBadges(response.wishlist_count);
                            }

                            // Smoothly fade & remove the item card from the Wishlist view
                            $col.addClass('removing');
                            setTimeout(function() {
                                $col.remove();

                                var remaining = $('.wishlist-item-col').length;
                                $('#wishlist-title-count').text(remaining + ' Items');

                                if (remaining === 0) {
                                    $('#wishlist-empty-state').removeClass('d-none');
                                }
                            }, 350);
                        } else {
                            toastr.error(response.message || 'Could not add to cart.');
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred. Please try again.');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // ──────────────────────────────────────────────
            // Badge Helpers
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
