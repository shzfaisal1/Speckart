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

<!-- home-slider1 -->
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<!-- sunglasses-section-slider -->
<script>
    $(document).ready(function() {
        $('.my-slider').slick({
            slidesToShow: 7,
            slidesToScroll: 1,
            arrows: true,
            dots: false,
            speed: 300,
            infinite: true,
            autoplaySpeed: 5000,
            autoplay: true,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                    }
                }
            ]
        });

        $('.sunglasses-slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            arrows: true,
            dots: false,
            speed: 300,
            // rtl: false,
            infinite: false,
            autoplaySpeed: 5000,
            autoplay: false,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });

        $('.eyeglasses-slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            arrows: true,
            dots: false,
            // rtl: false,
            speed: 300,
            infinite: false,
            autoplaySpeed: 5000,
            autoplay: false,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });

        $('.new-arrivals-slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            arrows: true,
            dots: false,
            // rtl: false,
            speed: 300,
            infinite: false,
            autoplaySpeed: 5000,
            autoplay: false,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
        
        $('.trending-slider').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: true,
            dots: false,
            speed: 300,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 800,
            prevArrow: '#swiper1-prev',
            nextArrow: '#swiper1-next',
            responsive: [
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ]
        });


        $('.shop-by-brand-slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            arrows: false,
            dots: false,
            speed: 300,
            infinite: false,
            autoplaySpeed: 5000,
            autoplay: false,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.js"></script>
<script>
    let swiper1 = new Swiper(".swiper", {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        loop: true,
        // autoplay: {
        //   delay: 3000, 
        //   disableOnInteraction: false, 
        // },
        coverflowEffect: {
            rotate: 0,
            stretch: 0,
            depth: 100,
            modifier: 4,
            slideShadows: true
        },
        navigation: {
            prevEl: '#swiper2-prev',
            nextEl: '#swiper2-next',
        },
        keyboard: {
            enabled: true
        },
        mousewheel: {
            thresholdDelta: 70
        },
        breakpoints: {
            560: {
                slidesPerView: 2.5
            },
            768: {
                slidesPerView: 3
            },
            1024: {
                slidesPerView: 2.5
            }
        }
    });

    // let swiper2 = new Swiper('.swiper_own-creation', {
    //     loop: true,
    //     spaceBetween: 30,

    //     navigation: {
    //         prevEl: '#swiper1-prev',
    //         nextEl: '#swiper1-next'
    //     },

    //     breakpoints: {

    //         0: {
    //              slidesPerView: 1.1,
    //             spaceBetween: 16
    //         },

    //         768: {
    //             slidesPerView: 3,
    //         },

    //         1024: {
    //             slidesPerView: 4,
    //         }
    //     }
    // });
</script>

<!-- Search Auto-suggest Script -->
<script>
    $(document).ready(function() {
        var ajaxCall = null;

        function getRecentSearches() {
            var searches = localStorage.getItem('recentSearches');
            return searches ? JSON.parse(searches) : [];
        }

        function addRecentSearch(query) {
            if (!query) return;
            var searches = getRecentSearches();
            searches = searches.filter(function(s) { return s !== query; });
            searches.unshift(query);
            if (searches.length > 5) {
                searches.pop();
            }
            localStorage.setItem('recentSearches', JSON.stringify(searches));
        }

        $('form:has(.ajax-search-input)').on('submit', function() {
            var query = $(this).find('.ajax-search-input').val().trim();
            addRecentSearch(query);
        });

        function renderRecentSearches() {
            var searches = getRecentSearches();
            if (searches.length === 0) return '';
            
            var html = '<div class="suggestion-section"><div class="suggestion-header">RECENT SEARCH</div><ul class="suggestion-list">';
            searches.forEach(function(s) {
                html += '<li><a href="{{ route("products") }}?search=' + encodeURIComponent(s) + '">';
                html += '<i class="bi bi-clock-history text-secondary me-2"></i>' + s;
                html += '</a></li>';
            });
            html += '</ul></div>';
            return html;
        }

        function fetchSuggestions(inputElement) {
            var query = inputElement.val().trim();
            var searchBox = inputElement.closest('.search-box');
            var searchDropdown = searchBox.find('.ajax-search-dropdown');
            var searchContent = searchBox.find('.search-suggestions-content');

            if (ajaxCall) {
                ajaxCall.abort();
            }

            ajaxCall = $.ajax({
                url: '{{ route("ajax.search") }}',
                method: 'GET',
                data: { search: query },
                success: function(response) {
                    if (response.status === 'success') {
                        var html = '';
                        
                        if (query === '') {
                            html += renderRecentSearches();
                            html += '<div class="suggestion-section"><ul class="suggestion-list">';
                            response.data.forEach(function(item) {
                                html += '<li><a href="' + item.url + '">';
                                html += '<img src="' + item.image_url + '" alt="icon" class="suggestion-img">';
                                html += item.name;
                                html += '</a></li>';
                            });
                            html += '</ul></div>';
                        } else {
                            if (response.data.length > 0) {
                                html += '<div class="suggestion-section"><ul class="suggestion-list">';
                                response.data.forEach(function(item) {
                                    html += '<li><a href="' + item.url + '">';
                                    html += '<img src="' + item.image_url + '" alt="product" class="suggestion-img">';
                                    html += item.name;
                                    html += '</a></li>';
                                });
                                html += '</ul></div>';
                            } else {
                                html += '<div class="suggestion-section p-3 text-center text-muted">No results found for "' + query + '"</div>';
                            }
                        }

                        searchContent.html(html);
                        searchDropdown.show();
                    }
                }
            });
        }

        $('.ajax-search-input').on('keyup focus', function() {
            fetchSuggestions($(this));
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-box').length) {
                $('.ajax-search-dropdown').hide();
            }
        });
    });
</script>

<!-- ══════════════════════════════════════════════════
     GLOBAL WISHLIST TOGGLE SYSTEM
     Works on all pages (home, products, product detail).
     Shows login modal for guests; AJAX toggle for logged-in.
════════════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
    var isLoggedIn = (document.querySelector('.header[data-is-logged-in]') || document.body).getAttribute('data-is-logged-in') === 'true';

    // ── Main Toggle Function ──────────────────────────
    window.speckartToggleWishlist = function (productId, buttonEl) {
        if (!isLoggedIn) {
            // Store pending product so we can save after login
            sessionStorage.setItem('speckart_pending_wishlist', productId);
            // Open the login modal
            var modalEl = document.getElementById('speckartLoginModal');
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
            return;
        }
        doWishlistToggle(productId, buttonEl);
    };

    // ── After Login Success → Redirect to Checkout or Save Pending Wishlist ───
    window.addEventListener('speckartLoginSuccess', function () {
        isLoggedIn = true;
        var pendingCheckout = sessionStorage.getItem('speckart_pending_checkout');
        if (pendingCheckout) {
            sessionStorage.removeItem('speckart_pending_checkout');
            window.location.href = '{{ route("shipping-details") }}';
            return;
        }
        var pending = sessionStorage.getItem('speckart_pending_wishlist');
        if (pending) {
            sessionStorage.removeItem('speckart_pending_wishlist');
            var btn = document.querySelector('[data-wishlist-product-id="' + pending + '"]');
            doWishlistToggle(pending, btn);
        } else {
            $.get('{{ route("wishlist.count") }}', function(res) {
                if (res && res.count !== undefined) updateWishlistBadge(res.count);
            });
        }
    });

    // ── AJAX Toggle Call ──────────────────────────────
    function doWishlistToggle(productId, buttonEl) {
        $.ajax({
            url: '{{ route("wishlist.toggle") }}',
            method: 'POST',
            data: {
                _token:     csrfToken,
                product_id: productId,
            },
            success: function (response) {
                if (response.status === 'added') {
                    if (buttonEl) {
                        $(buttonEl).addClass('wishlist-active').find('i').removeClass('bi-heart').addClass('bi-heart-fill');
                        $(buttonEl).css('color', '#e74c3c');
                    }
                    updateWishlistBadge(response.count);
                    if (typeof toastr !== 'undefined') toastr.success(response.message || 'Added to wishlist!');
                } else if (response.status === 'removed') {
                    if (buttonEl) {
                        $(buttonEl).removeClass('wishlist-active').find('i').removeClass('bi-heart-fill').addClass('bi-heart');
                        $(buttonEl).css('color', '');
                    }
                    updateWishlistBadge(response.count);
                    if (typeof toastr !== 'undefined') toastr.warning(response.message || 'Removed from wishlist.');
                } else if (response.status === 'unauthenticated') {
                    sessionStorage.setItem('speckart_pending_wishlist', productId);
                    var modalEl = document.getElementById('speckartLoginModal');
                    if (modalEl) {
                        var modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    sessionStorage.setItem('speckart_pending_wishlist', productId);
                    var modalEl = document.getElementById('speckartLoginModal');
                    if (modalEl) {
                        var modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Could not update wishlist. Please try again.');
                }
            }
        });
    }

    function updateWishlistBadge(count) {
        var badges = document.querySelectorAll('.wishlist-badge');
        badges.forEach(function (b) {
            b.textContent = count;
            if (count > 0) b.classList.remove('d-none');
            else b.classList.add('d-none');
        });
    }

    window.updateCartBadges = function (count) {
        var badges = document.querySelectorAll('.cart-badge');
        badges.forEach(function (b) {
            b.textContent = count;
            if (count > 0) b.classList.remove('d-none');
            else b.classList.add('d-none');
        });
    };

    // ── Delegate click on any .btn-wishlist-toggle ───
    $(document).on('click', '.btn-wishlist-toggle', function (e) {
        e.preventDefault();
        var productId = $(this).data('wishlist-product-id');
        if (productId) window.speckartToggleWishlist(productId, this);
    });

})();
</script>
<!-- end global wishlist -->

