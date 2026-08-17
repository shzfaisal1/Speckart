@extends('website.layout.master')

@section('content')
<style>
/* ═══════════════════════════════════════════
   SPECKART CATALOG & FILTER DESIGN SYSTEM
═══════════════════════════════════════════ */
.catalog-page {
    background-color: #f8fafc;
    min-height: 100vh;
}

/* --- Filter Sidebar (Desktop) --- */
.filter-sidebar {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    position: sticky;
    top: 90px;
    max-height: calc(100vh - 110px);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
.filter-sidebar::-webkit-scrollbar { width: 4px; }
.filter-sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.filter-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    position: sticky;
    top: 0;
    background: #ffffff;
    z-index: 10;
}
.filter-top-bar h6 {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.filter-top-bar h6 i { color: #00b9b9; font-size: 16px; }

.filter-reset-btn {
    font-size: 12px;
    font-weight: 600;
    color: #00b9b9;
    text-decoration: none;
    background: #e6f8f8;
    padding: 5px 14px;
    border-radius: 20px;
    transition: all 0.2s ease;
}
.filter-reset-btn:hover { background: #00b9b9; color: #ffffff; }

.active-chips-bar {
    padding: 10px 20px 0;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.active-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e6f8f8;
    color: #00b9b9;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    cursor: pointer;
    border: 1px solid rgba(0, 185, 185, 0.3);
    transition: all 0.2s ease;
}
.active-chip:hover { background: #00b9b9; color: #ffffff; }

.filter-section { border-bottom: 1px solid #f1f5f9; }
.filter-section:last-child { border-bottom: none; }
.filter-section-btn {
    width: 100%;
    background: none;
    border: none;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
    transition: background 0.15s;
    text-align: left;
}
.filter-section-btn:hover { background: #f8fafc; }
.filter-section-btn i.chevron { font-size: 12px; color: #64748b; transition: transform 0.25s; }
.filter-section-btn.open i.chevron { transform: rotate(180deg); }
.filter-section-body { padding: 4px 20px 16px; display: none; }
.filter-section-body.open { display: block; }

.brand-search-wrap { position: relative; margin-bottom: 12px; }
.brand-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
.brand-search-input {
    width: 100%; padding: 8px 12px 8px 34px; border: 1px solid #cbd5e1;
    border-radius: 10px; font-size: 13px; color: #334155; outline: none; transition: border 0.2s;
}
.brand-search-input:focus { border-color: #00b9b9; box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.1); }

.icon-filter-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.icon-filter-item {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 10px 6px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;
    transition: all 0.2s ease; background: #ffffff; text-align: center;
    font-size: 11px; font-weight: 600; color: #475569; gap: 6px; line-height: 1.2;
}
.icon-filter-item img { width: 38px; height: 26px; object-fit: contain; }
.icon-filter-item:hover { border-color: #00b9b9; background: #f0fdfd; color: #00b9b9; }
.icon-filter-item.active-filter { border-color: #00b9b9; background: #e6f8f8; color: #00b9b9; font-weight: 700; box-shadow: 0 2px 8px rgba(0,185,185,0.15); }

.filter-checkbox-item {
    display: flex; align-items: center; gap: 10px; padding: 6px 0;
    cursor: pointer; font-size: 13px; color: #334155; font-weight: 500; transition: color 0.15s;
    user-select: none;
}
.filter-checkbox-item:hover { color: #00b9b9; }
.filter-checkbox-item input { width: 16px; height: 16px; accent-color: #00b9b9; cursor: pointer; flex-shrink: 0; border-radius: 4px; }
.filter-checkbox-item .color-dot { width: 16px; height: 16px; border-radius: 50%; border: 1px solid #cbd5e1; flex-shrink: 0; }

.gender-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.gender-pill {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px 6px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;
    font-size: 12px; font-weight: 600; color: #475569; background: #ffffff;
    transition: all 0.2s ease; user-select: none;
}
.gender-pill:hover { border-color: #00b9b9; color: #00b9b9; background: #f0fdfd; }
.gender-pill.active-filter { border-color: #00b9b9; background: #e6f8f8; color: #00b9b9; font-weight: 700; }

/* --- Mobile Filter Trigger Bar & Offcanvas --- */
.mobile-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 99;
}
.mobile-filter-trigger-btn, .mobile-sort-trigger-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px;
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.mobile-filter-trigger-btn i, .mobile-sort-trigger-btn i {
    color: #00b9b9;
    font-size: 15px;
}
.mobile-filter-badge {
    background: #00b9b9;
    color: #ffffff;
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 20px;
    font-weight: 700;
}

.mobile-filter-offcanvas {
    width: 88% !important;
    max-width: 400px !important;
    border-radius: 0 20px 20px 0;
    box-shadow: 10px 0 30px rgba(0, 0, 0, 0.15);
}
.mobile-filter-offcanvas .offcanvas-header {
    border-bottom: 1px solid #f1f5f9;
    padding: 16px 20px;
    background: #ffffff;
}
.mobile-filter-offcanvas .offcanvas-body {
    padding: 0;
    overflow-y: auto;
}
.btn-apply-mobile-filters {
    width: 100%;
    background: #00b9b9;
    color: #ffffff;
    border: none;
    border-radius: 12px;
    padding: 14px;
    font-size: 15px;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(0, 185, 185, 0.3);
    transition: all 0.2s ease;
}
.btn-apply-mobile-filters:hover {
    background: #009999;
}

/* --- Top Header & Search Bar --- */
.filter-search-bar {
    display: flex; align-items: center; background: #ffffff;
    border: 1px solid #cbd5e1; border-radius: 25px; overflow: hidden; height: 42px; min-width: 260px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.filter-search-bar input { border: none; outline: none; padding: 0 16px; font-size: 13px; color: #334155; flex: 1; height: 100%; }
.filter-search-bar button {
    background: #00b9b9; color: #ffffff; border: none; padding: 0 18px;
    font-size: 14px; font-weight: 600; height: 100%; cursor: pointer; transition: background 0.2s;
}
.filter-search-bar button:hover { background: #009999; }

.sort-dropdown-btn {
    background: #ffffff; border: 1px solid #cbd5e1; border-radius: 25px; padding: 8px 18px;
    font-size: 13px; font-weight: 600; color: #334155; display: flex; align-items: center; gap: 8px; cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: all 0.2s ease;
}
.sort-dropdown-btn:hover { border-color: #00b9b9; color: #00b9b9; }

/* --- Product Cards --- */
.product-card {
    position: relative;
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
}
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e1;
}

.wishlist-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 5;
    transition: all 0.2s ease;
}
.wishlist-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
}
.wishlist-btn i { font-size: 16px; color: #64748b; }
.wishlist-btn i.bi-heart-fill { color: #ef4444 !important; }

.product-image {
    position: relative;
    background: #f8fafc;
    padding: 20px;
    text-align: center;
    height: 170px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.product-image img {
    max-height: 130px;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}
.product-card:hover .product-image img {
    transform: scale(1.05);
}
.img-hover { display: none; }

.product-info {
    padding: 16px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.brand-name {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #00b9b9;
    margin-bottom: 4px;
}
.product-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.4;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.size-rating {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 12px;
}
.rating {
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 600;
    color: #334155;
}
.rating i { color: #f59e0b; }

.price-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px dashed #e2e8f0;
}
.price-section .price {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}
.try-btn {
    background: #00b9b9;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}
.try-btn:hover {
    background: #009999;
    box-shadow: 0 4px 12px rgba(0, 185, 185, 0.25);
}

/* --- Modern Circular Pagination Styling --- */
.modern-pagination {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    padding: 8px 20px;
    border-radius: 50px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    margin: 0;
}
.modern-pagination .page-item { margin: 0; list-style: none; }
.modern-pagination .page-link {
    width: 40px; height: 40px; border-radius: 50% !important;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 600; color: #475569; background: #ffffff;
    border: 1px solid #f1f5f9; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
    text-decoration: none; position: relative;
}
.modern-pagination .page-link:hover {
    background: #e6f8f8; color: #00b9b9; border-color: #00b9b9;
    transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0, 185, 185, 0.15);
}
.modern-pagination .page-item.active .page-link {
    background: #07484A !important; color: #ffffff !important;
    border-color: #07484A !important; font-weight: 700;
    box-shadow: 0 4px 14px rgba(7, 72, 74, 0.35) !important; transform: translateY(-2px);
}
.modern-pagination .page-item.active .page-link::after {
    content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%);
    width: 6px; height: 6px; background: #ff7675; border-radius: 50%;
    box-shadow: 0 0 6px rgba(255, 118, 117, 0.6);
}
.modern-pagination .page-item.disabled .page-link {
    opacity: 0.35; cursor: not-allowed; pointer-events: none; background: #f8fafc;
    border-color: #e2e8f0; box-shadow: none;
}
</style>

<div class="catalog-page">
    {{-- MOBILE FILTER ACTION BAR (Visible on < 992px) --}}
    <div class="mobile-filter-bar d-flex d-lg-none">
        <button class="mobile-filter-trigger-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterOffcanvas">
            <i class="bi bi-sliders"></i> Filters <span class="mobile-filter-badge d-none" id="mobile-filter-badge">0</span>
        </button>
        <div class="dropdown flex-grow-1">
            <button class="mobile-sort-trigger-btn w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-sort-down"></i> Sort By
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3 w-100">
                <li><a class="dropdown-item py-2 sort-item fw-medium" data-sort="">Popularity</a></li>
                <li><a class="dropdown-item py-2 sort-item fw-medium" data-sort="newest">Newest First</a></li>
                <li><a class="dropdown-item py-2 sort-item fw-medium" data-sort="price_low">Price: Low to High</a></li>
                <li><a class="dropdown-item py-2 sort-item fw-medium" data-sort="price_high">Price: High to Low</a></li>
            </ul>
        </div>
    </div>

    {{-- MOBILE OFFCANVAS FILTER DRAWER --}}
    <div class="offcanvas offcanvas-start mobile-filter-offcanvas" tabindex="-1" id="mobileFilterOffcanvas" aria-labelledby="mobileFilterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="mobileFilterOffcanvasLabel">
                <i class="bi bi-sliders text-teal"></i> Filters
            </h5>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('products', ($activeCategory ?? null) ? ['category' => $activeCategory->slug] : []) }}" class="filter-reset-btn">Clear All</a>
                <button type="button" class="btn-close text-reset ms-1" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        </div>
        <div class="offcanvas-body p-0">
            <div class="filter-sidebar border-0 shadow-none sticky-top-0 rounded-0">
                @include('website.products.filter_sections')
            </div>
        </div>
        <div class="offcanvas-footer p-3 border-top bg-white">
            <button type="button" class="btn-apply-mobile-filters" data-bs-dismiss="offcanvas" id="mobile-apply-btn">
                Apply Filters ({{ $productsList->total() }} Products)
            </button>
        </div>
    </div>

    {{-- TOP CATEGORY & DESKTOP SEARCH BAR --}}
    <section class="product py-4 border-bottom bg-white d-none d-lg-block">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1" style="font-size:22px; color:#0f172a;">
                        {{ ($activeCategory ?? null) ? $activeCategory->name : 'All Products' }}
                    </h4>
                    <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fw-semibold" style="font-size: 12px;">
                        {{ $productsList->total() }} products found
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="filter-search-bar">
                        <input type="text" id="search-input" placeholder="Search products…" autocomplete="off">
                        <button type="button" id="search-btn"><i class="bi bi-search"></i></button>
                    </div>
                    <div class="dropdown">
                        <button class="sort-dropdown-btn dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-sort-down fs-6"></i> Sort By
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" style="min-width:180px;">
                            <li><a class="dropdown-item py-2 sort-item fw-medium" data-sort="">Popularity</a></li>
                            <li><a class="dropdown-item py-2 sort-item fw-medium" data-sort="newest">Newest First</a></li>
                            <li><a class="dropdown-item py-2 sort-item fw-medium" data-sort="price_low">Price: Low to High</a></li>
                            <li><a class="dropdown-item py-2 sort-item fw-medium" data-sort="price_high">Price: High to Low</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="prod py-4">
        <div class="container">
            <div class="row g-4">

                {{-- DESKTOP FILTER SIDEBAR (Visible only on lg screens >= 992px) --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="filter-sidebar">
                        <div class="filter-top-bar">
                            <h6><i class="bi bi-sliders"></i> Filters</h6>
                            <a href="{{ route('products', ($activeCategory ?? null) ? ['category' => $activeCategory->slug] : []) }}"
                               class="filter-reset-btn">Clear All</a>
                        </div>
                        <div class="active-chips-bar" id="active-chips-bar"></div>
                        @include('website.products.filter_sections')
                    </div>
                </div>

                {{-- PRODUCTS GRID CONTAINER --}}
                <div class="col-lg-9 col-md-12">
                    <div id="product-grid-wrapper">
                        @include('website.products.product_grid')
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Toggle filter section open/close
    $(document).on('click', '.filter-section-btn', function() {
        $(this).toggleClass('open');
        const targetId = $(this).attr('data-target');
        if (targetId) {
            $('[id="' + targetId + '"]').toggleClass('open');
        }
    });

    // Brand Search Filter
    $(document).on('input', '.brand-search-input', function() {
        const q = $(this).val().toLowerCase();
        $('.brand-item').each(function() {
            const txt = $(this).text().toLowerCase();
            $(this).toggle(txt.includes(q));
        });
    });

    // Filter Items Click Listeners
    $(document).on('click', '.icon-filter-item', function() {
        $(this).toggleClass('active-filter');
        fetchFilteredProducts();
    });

    $(document).on('click', '.gender-pill', function() {
        $(this).toggleClass('active-filter');
        fetchFilteredProducts();
    });

    $(document).on('change', '.filter-checkbox', function() {
        fetchFilteredProducts();
    });

    // Wishlist Toggle
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const productId = $btn.data('product-id');
        const $heart = $btn.find('i');

        if (!productId) return;

        $.ajax({
            url: "{{ route('wishlist.toggle') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                product_id: productId
            },
            success: function(res) {
                if (res.status === 'success') {
                    if (res.is_in_wishlist || res.action === 'added') {
                        $heart.removeClass('bi-heart').addClass('bi-heart-fill text-danger');
                        if (typeof toastr !== 'undefined') toastr.success('Added to wishlist!');
                    } else {
                        $heart.removeClass('bi-heart-fill text-danger').addClass('bi-heart');
                        if (typeof toastr !== 'undefined') toastr.warning('Removed from wishlist.');
                    }
                    var $badges = $('.wishlist-badge');
                    if (res.count > 0) {
                        $badges.text(res.count).removeClass('d-none');
                    } else {
                        $badges.addClass('d-none').text(0);
                    }
                }
            }
        });
    });

    // Sort Item Click Handler
    $(document).on('click', '.sort-item', function(e) {
        e.preventDefault();
        $('.sort-item').removeClass('active');
        $(this).addClass('active');
        $('.sort-dropdown-btn, .mobile-sort-trigger-btn').html('<i class="bi bi-sort-down fs-6"></i> ' + $(this).text());
        fetchFilteredProducts();
    });

    // Search Input Enter or Search Button Click
    $('#search-btn').on('click', function() {
        fetchFilteredProducts();
    });
    $('#search-input').on('keyup', function(e) {
        if (e.key === 'Enter') fetchFilteredProducts();
    });

    // AJAX Pagination click handler
    $(document).on('click', '.modern-pagination a.page-link', function(e) {
        e.preventDefault();
        const pageUrl = $(this).attr('href');
        if (!pageUrl || pageUrl === '#') return;

        fetchFilteredProducts(pageUrl);

        // Smooth scroll up to top of catalog section
        $('html, body').animate({
            scrollTop: $('.catalog-page').offset().top - 70
        }, 300);
    });

    // Fetch Filtered Products AJAX
    function fetchFilteredProducts(targetUrl = null) {
        let requestUrl = targetUrl;

        if (!requestUrl) {
            const params = new URLSearchParams();
            let totalCheckedCount = 0;

            ['frame_type', 'shape', 'brand', 'gender', 'occasion', 'age', 'color', 'material', 'size', 'modality'].forEach(key => {
                const vals = [];
                document.querySelectorAll(`.icon-filter-item[data-filter="${key}"].active-filter`).forEach(el => {
                    const v = el.getAttribute('data-value');
                    if (!vals.includes(v)) vals.push(v);
                });
                document.querySelectorAll(`.gender-pill[data-filter="${key}"].active-filter`).forEach(el => {
                    const v = el.getAttribute('data-value');
                    if (!vals.includes(v)) vals.push(v);
                });
                document.querySelectorAll(`.filter-checkbox[data-filter="${key}"]:checked`).forEach(el => {
                    const v = el.getAttribute('data-value');
                    if (!vals.includes(v)) vals.push(v);
                });

                if (vals.length > 0) {
                    params.append(key, vals.join(','));
                    totalCheckedCount += vals.length;
                }
            });

            // Update mobile filter badge
            const $mobileBadge = $('#mobile-filter-badge');
            if (totalCheckedCount > 0) {
                $mobileBadge.text(totalCheckedCount).removeClass('d-none');
            } else {
                $mobileBadge.addClass('d-none').text(0);
            }

            const searchVal = document.getElementById('search-input')?.value;
            if (searchVal) params.append('search', searchVal);

            const activeSort = $('.sort-item.active').data('sort');
            if (activeSort) params.append('sort', activeSort);

            const currentCategory = "{{ ($activeCategory ?? null) ? $activeCategory->slug : '' }}";
            if (currentCategory) params.append('category', currentCategory);

            requestUrl = "{{ route('products') }}?" + params.toString();
        }

        fetch(requestUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('product-grid-wrapper').innerHTML = html;
        })
        .catch(err => console.error(err));
    }
});
</script>
@endsection
