    <!-- navbar -->
    @php
        $initialWishlistCount = 0;
        if (auth()->check()) {
            $initialWishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
        }
        $initialCartCount = count(session('cart', []));
    @endphp

    <style>
        .cart-link {
            text-decoration: none;
        }

        .cart-icon {
            position: relative;
            display: inline-block;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            min-width: 16px;
            height: 16px;
            padding: 0 5px;
            background: #ff3b30;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        /* ===== Loyalty Points Styles ===== */
        .loyalty-link {
            text-decoration: none;
        }

        .loyalty-icon {
            position: relative;
            display: inline-block;
        }

        .loyalty-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            min-width: 16px;
            height: 16px;
            padding: 0 5px;
            background: #16a085;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .loyalty-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 16px;
            width: 240px;
            z-index: 999;
        }

        .loyalty-dropdown.show {
            display: block;
        }

        .loyalty-dropdown h6 {
            margin: 0 0 4px;
            font-size: 13px;
            color: #666;
        }

        .loyalty-dropdown .points-value {
            font-size: 22px;
            font-weight: 700;
            color: #16a085;
            margin-bottom: 8px;
        }

        .loyalty-dropdown .points-worth {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }

        .loyalty-dropdown .btn-redeem {
            display: block;
            text-align: center;
            background: #16a085;
            color: #fff;
            padding: 8px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
        }
        /* ===== End Loyalty Points Styles ===== */
    </style>
    <!-- navbar -->
    @include('website.layout.partial.login-modal')
    <section class="header" data-is-logged-in="{{ auth()->check() ? 'true' : 'false' }}">
        <div class="container header-container">
            <div class="row mb-0">
                <div class="col-lg-12 px-lg-4 px-2">
                    <div class="top-menu px-0  flex-wrap flex-md-wrap flex-lg-nowrap">
                        <div class="d-lg-block d-md-none d-none">
                            <div class="d-flex align-items-center ms-lg-0 ms-mb-3 ms-3">

                                <a href="/" class="logo">
                                    <img src="{{ asset('website/assets/img/logo/Specskart-logo-png.png') }}" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="d-lg-none d-md-block d-block">
                            <a style="padding: 2px 12px;display: flex;flex-direction: column;">
                                <div>
                                    <p class="mb-0" style="color:rgb(255, 94, 72)">Get faster delivery <img
                                            src="{{ asset('website/assets/img/icon/Thunder3x.webp') }}" alt="gold-delivery-icon"
                                            title="gold-delivery-icon" class="gold-delivery-icon"></p>

                                </div>
                                <div class="select-location">

                                    <div class="location-text">
                                        <h5>Select Location</h5>
                                    </div>

                                    <div class="location-arrow">
                                        <svg width="10" height="6" viewBox="0 0 8 4" fill="none">
                                            <path d="M4 4 0 0h8L4 4Z" fill="#fff" />
                                        </svg>
                                    </div>

                                </div>
                            </a>
                        </div>
                        <div class="top-menu-list d-lg-none d-md-block d-block">

                            <ul class="ps-0 mb-0 d-flex align-items-center justify-content-end">



                                <!-- Wishlist -->
                                <li>
                                    <a href="{{ route('wishlist') }}" class="wishlist-link">
                                        <p class="wishlist-icon position-relative mb-0">
                                            <img src="{{ asset('website/assets/img/icon/Wishlist.png') }}" alt="">
                                            <span class="wishlist-badge badge rounded-pill bg-danger position-absolute {{ $initialWishlistCount > 0 ? '' : 'd-none' }}"
                                                style="top: -5px; right: -8px; font-size: 10px; padding: 2px 5px;">{{ $initialWishlistCount }}</span>
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cart') }}" class="cart-link">
                                        <p class="cart-icon position-relative mb-0">
                                            <img src="{{ asset('website/assets/img/icon/My-Cart.png') }}" alt="">
                                            <span class="cart-badge badge rounded-pill bg-danger position-absolute {{ $initialCartCount > 0 ? '' : 'd-none' }}"
                                                style="top: -5px; right: -8px; font-size: 10px; padding: 2px 5px;">{{ $initialCartCount }}</span>
                                        </p>
                                    </a>
                                </li>

                                <!-- Menu -->
                                <li class="pe-2">
                                    <button class="btn mobile-menu-btn p-0" data-bs-toggle="offcanvas"
                                        data-bs-target="#mobileSidebar">

                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 17H14.5M5 12H19M5 7H19" stroke="#fff" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>

                                    </button>
                                </li>

                            </ul>

                        </div>
                        <div class="search-box position-relative">
                            <form action="{{ route('products') }}" method="GET" id="header-search-form">
                                <div class="input-group custom-search">

                                    <input type="text" class="form-control border-0 px-3 ajax-search-input" id="searchInput" name="search" autocomplete="off"
                                        placeholder="Search glasses, lenses, specs..." value="{{ request('search') }}">

                                    <button class="btn search-btn" type="submit" style="border-left:1px solid #000;">
                                        Search
                                    </button>

                                </div>
                            </form>

                            <!-- Search Dropdown -->
                            <div id="search-suggestions-dropdown" class="search-suggestions-dropdown ajax-search-dropdown" style="display:none;">
                                <div class="search-suggestions-content">
                                    <!-- Suggestions will be populated here via AJAX -->
                                </div>
                            </div>
                        </div>
                        <div class="top-menu-list d-lg-block d-md-none d-none">
                            <ul class="ps-0">
                                <li>
                                    <a href="track-order.html">
                                        <p>
                                            <img src="{{ asset('website/assets/img/icon/Track-Order.png') }}" alt="">
                                        </p>
                                        <p>Track Order</p>
                                    </a>
                                </li>



                                <li>
                                    <a href="{{ route('wishlist') }}" class="wishlist-link">
                                        <p class="wishlist-icon position-relative">
                                            <img src="{{ asset('website/assets/img/icon/Wishlist.png') }}" alt="">
                                            <span class="wishlist-badge badge rounded-pill bg-danger position-absolute {{ $initialWishlistCount > 0 ? '' : 'd-none' }}"
                                                style="top: -4px; right: 3px; font-size: 10px; padding: 2px 5px;">{{ $initialWishlistCount }}</span>
                                        </p>
                                        <p>Wishlist</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cart') }}" class="cart-link">
                                        <p class="cart-icon position-relative">
                                            <img src="{{ asset('website/assets/img/icon/My-Cart.png') }}" alt="">
                                            <span class="cart-badge badge rounded-pill bg-danger position-absolute {{ $initialCartCount > 0 ? '' : 'd-none' }}"
                                                style="top: -5px; right: -8px; font-size: 10px; padding: 2px 5px;">{{ $initialCartCount }}</span>
                                        </p>
                                        <p>My Cart</p>
                                    </a>
                                </li>
                                <li class="dropdown">
                                    <a href="#">
                                        <p>
                                            <img src="{{ asset('website/assets/img/icon/More.png') }}" alt="">
                                        </p>
                                        <p>More</p>
                                    </a>
                                    <ul class="dropdown-nav">
                                        <li class="dropdown-item"><a href="#">Lawn Care</a></li>
                                        <li class="dropdown-item"><a href="#">Walling &amp; Fencing</a></li>
                                        <li class="dropdown-item"><a href="#">Landscape design</a></li>
                                        <li class="dropdown-item"><a href="#">Grounds Maintenance</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown pe-0">

                                    <a href="login.html">

                                        <p>
                                            <img src="{{ asset('website/assets/img/icon/Signup.png') }}" alt="Login">
                                        </p>

                                        <p>
                                            Sign up/Sign In
                                        </p>

                                    </a>

                                    <!--
                                        Logged-in state (static example markup, shown when a user is authenticated).
                                        Swap the block above for this one manually if you need to preview the logged-in UI.

                                    <a href="profile.html">
                                        <p>
                                            <img src="{{ asset('website/assets/img/icon/user.png') }}" alt="John Doe" class="profile-avatar">
                                        </p>
                                        <p>John Doe</p>
                                    </a>
                                    <ul class="dropdown-nav">
                                        <li class="dropdown-item"><a href="profile.html">Profile</a></li>
                                        <li class="dropdown-item"><a href="my-order.html">My Order</a></li>
                                        <li class="dropdown-item"><a href="my-address.html">My Addresses</a></li>
                                        <li class="dropdown-item"><a href="my-prescription.html">My Prescription</a></li>
                                        <li class="dropdown-item"><a href="logout.html">Logout</a></li>
                                    </ul>
                                    -->

                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="d-lg-block d-md-none d-none">
            <div class="wrapper">
                <div class="">
                    <div class="row mb-0">
                        <div class="col-lg-12 text-center">
                            <!--<div class="logo"><a href="#"> Mega Menu</a></div> -->
                            <input type="radio" name="slider" id="menu-btn">
                            <input type="radio" name="slider" id="close-btn">
                            <ul class="nav-links gap-2">
                                <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
                                <li>
                                    <a href="#" class="desktop-item">Eyeglasses </a>
                                    <input type="checkbox" id="showMega">
                                    <label for="showMega" class="mobile-item">Mega Menu</label>
                                    <div class="mega-box">
                                        <div class="content1">
                                            <div class="tabs">
                                                <ul id="tabs-nav">
                                                    <li>
                                                        <a href="#tab1">
                                                            <img src="{{ asset('website/assets/img/icon/specs-men.png') }}"
                                                                alt="specs-men">
                                                            Men
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab2">
                                                            <img src="{{ asset('website/assets/img/icon/specs-women.png') }}"
                                                                alt="specs-women">
                                                            Women
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab3">
                                                            <img src="{{ asset('website/assets/img/icon/specs-kid.png') }}"
                                                                alt="specs-kid">
                                                            Kids
                                                        </a>
                                                    </li>
                                                </ul> <!-- END tabs-nav -->
                                                <div id="tabs-content">
                                                    <div id="tab1" class="tab-content">
                                                        <div class="tabs1">
                                                            <ul id="tabs-nav1">
                                                                <li>
                                                                    <a href="#tab5">
                                                                        <p>CLASSIC EYEGLASSES</p>
                                                                        <p>Starting from $2000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tab6">
                                                                        <p>PREMIUM EYEGLASSES</p>
                                                                        <p>Starting from $4000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tab7">
                                                                        <p>SCREEN EYEGLASSES</p>
                                                                        <p>Starting from $600</p>
                                                                    </a>
                                                                </li>
                                                            </ul> <!-- END tabs-nav -->
                                                            <div id="tabs-content1">
                                                                <div id="tab5" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Speckarts BLU
                                                                                        Lenses</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Type</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Wayfarer Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Geometric Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Harry Potter</a></li>
                                                                                <li><a href="#">Aao Twyst Karein</a>
                                                                                </li>
                                                                                <li><a href="#">Hustlr - As Seen on
                                                                                        Shark Tank</a></li>
                                                                                <li><a href="#">Switch - Magnetic
                                                                                        Clips-On</a></li>
                                                                                <li><a href="#">Patriot</a></li>
                                                                                <li><a href="#">Hip Hop</a></li>
                                                                                <li><a href="#">Turban Edit</a></li>
                                                                                <li><a href="#">Classic Acetates</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">Vincent Chase</a>
                                                                                </li>
                                                                                <li><a href="#">Speckarts Air</a></li>
                                                                                <li><a href="#">Speckarts STUDIO</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tab6" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">John Jacobs</a></li>
                                                                                <li><a href="#">Owndays</a></li>
                                                                                <li><a href="#">New Balance</a></li>
                                                                                <li><a href="#">Fossil</a></li>
                                                                                <li><a href="#">Le Petit Lunetier</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Shape</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Square Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Tilak Varma for John
                                                                                        Jacobs</a></li>
                                                                                <li><a href="#">Zodiac</a></li>
                                                                                <li><a href="#">Wildgear</a></li>
                                                                                <li><a href="#">Timeless Metals</a>
                                                                                </li>
                                                                                <li><a href="#">Headspace</a></li>
                                                                                <li><a href="#">Break the Frame</a>
                                                                                </li>
                                                                                <li><a href="#">Amore by Aditi Rao
                                                                                        Hydari</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tab7" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Starting from
                                                                                        Rs.600</a></li>
                                                                                <li><a href="#">For your Kids</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- END tabs-content -->
                                                        </div> <!-- END tabs -->
                                                    </div>
                                                    <div id="tab2" class="tab-content">
                                                        <div class="tabs2">
                                                            <ul id="tabs-nav2">
                                                                <li>
                                                                    <a href="#tab8">
                                                                        <p>CLASSIC EYEGLASSES</p>
                                                                        <p>Starting from $2000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tab9">
                                                                        <p>PREMIUM EYEGLASSES</p>
                                                                        <p>Starting from $4000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tab10">
                                                                        <p>SCREEN EYEGLASSES</p>
                                                                        <p>Starting from $600</p>
                                                                    </a>
                                                                </li>
                                                            </ul> <!-- END tabs-nav -->
                                                            <div id="tabs-content2">
                                                                <div id="tab8" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Speckarts BLU
                                                                                        Lenses</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Type</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Wayfarer Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Geometric Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Harry Potter</a></li>
                                                                                <li><a href="#">Aao Twyst Karein</a>
                                                                                </li>
                                                                                <li><a href="#">Hustlr - As Seen on
                                                                                        Shark Tank</a></li>
                                                                                <li><a href="#">Switch - Magnetic
                                                                                        Clips-On</a></li>
                                                                                <li><a href="#">Patriot</a></li>
                                                                                <li><a href="#">Hip Hop</a></li>
                                                                                <li><a href="#">Turban Edit</a></li>
                                                                                <li><a href="#">Classic Acetates</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">Vincent Chase</a>
                                                                                </li>
                                                                                <li><a href="#">Speckarts Air</a></li>
                                                                                <li><a href="#">Speckarts STUDIO</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tab9" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">John Jacobs</a></li>
                                                                                <li><a href="#">Owndays</a></li>
                                                                                <li><a href="#">New Balance</a></li>
                                                                                <li><a href="#">Fossil</a></li>
                                                                                <li><a href="#">Le Petit Lunetier</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Shape</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Square Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Tilak Varma for John
                                                                                        Jacobs</a></li>
                                                                                <li><a href="#">Zodiac</a></li>
                                                                                <li><a href="#">Wildgear</a></li>
                                                                                <li><a href="#">Timeless Metals</a>
                                                                                </li>
                                                                                <li><a href="#">Headspace</a></li>
                                                                                <li><a href="#">Break the Frame</a>
                                                                                </li>
                                                                                <li><a href="#">Amore by Aditi Rao
                                                                                        Hydari</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tab10" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Starting from
                                                                                        Rs.600</a></li>
                                                                                <li><a href="#">For your Kids</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- END tabs-content -->
                                                        </div> <!-- END tabs -->
                                                    </div>
                                                    <div id="tab3" class="tab-content">
                                                        <div class="tabs3">
                                                            <ul id="tabs-nav3">
                                                                <li>
                                                                    <a href="#tab11">
                                                                        <p>CLASSIC EYEGLASSES</p>
                                                                        <p>Starting from ₹1000</p>
                                                                    </a>
                                                                </li>
                                                            </ul> <!-- END tabs-nav -->
                                                            <div id="tabs-content3">
                                                                <div id="tab11" class="tab-content3">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Trending</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Type</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Wayfarer Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Oval Frames</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Creatr</a></li>
                                                                                <li><a href="#">Hooper Mini</a></li>
                                                                                <li><a href="#">Flexi</a></li>
                                                                                <li><a href="#">Memphis</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Age</h4>
                                                                                <li><a href="#">2-5 Yrs</a></li>
                                                                                <li><a href="#">5-8 Yrs</a></li>
                                                                                <li><a href="#">8-12 Yrs</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- END tabs-content -->
                                                        </div> <!-- END tabs -->
                                                    </div>
                                                </div> <!-- END tabs-content -->
                                            </div> <!-- END tabs -->

                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="#">Screen Glasses </a>
                                    <input type="checkbox" id="showMega">
                                    <label for="showMega" class="mobile-item">Mega Menu</label>
                                    <div class="mega-box">
                                        <div class="content1">
                                            <div class="tabs">
                                                <ul id="tabs-nav">
                                                    <li>
                                                        <a href="#tabS1">
                                                            <img src="{{ asset('website/assets/img/icon/specs-men.png') }}"
                                                                alt="specs-men">
                                                            Men
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tabS2">
                                                            <img src="{{ asset('website/assets/img/icon/specs-women.png') }}"
                                                                alt="specs-women">
                                                            Women
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tabS3">
                                                            <img src="{{ asset('website/assets/img/icon/specs-kid.png') }}"
                                                                alt="specs-kid">
                                                            Kids
                                                        </a>
                                                    </li>
                                                </ul> <!-- END tabs-nav -->
                                                <div id="tabs-content">
                                                    <div id="tabS1" class="tab-content">
                                                        <div class="tabs1">
                                                            <ul id="tabs-nav1">
                                                                <li>
                                                                    <a href="#tabS5">
                                                                        <p>CLASSIC EYEGLASSES</p>
                                                                        <p>Starting from $2000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tabS6">
                                                                        <p>PREMIUM EYEGLASSES</p>
                                                                        <p>Starting from $4000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tabS7">
                                                                        <p>SCREEN EYEGLASSES</p>
                                                                        <p>Starting from $600</p>
                                                                    </a>
                                                                </li>
                                                            </ul> <!-- END tabs-nav -->
                                                            <div id="tabs-content1">
                                                                <div id="tabS5" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Speckarts BLU
                                                                                        Lenses</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Type</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Wayfarer Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Geometric Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Harry Potter</a></li>
                                                                                <li><a href="#">Aao Twyst Karein</a>
                                                                                </li>
                                                                                <li><a href="#">Hustlr - As Seen on
                                                                                        Shark Tank</a></li>
                                                                                <li><a href="#">Switch - Magnetic
                                                                                        Clips-On</a></li>
                                                                                <li><a href="#">Patriot</a></li>
                                                                                <li><a href="#">Hip Hop</a></li>
                                                                                <li><a href="#">Turban Edit</a></li>
                                                                                <li><a href="#">Classic Acetates</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">Vincent Chase</a>
                                                                                </li>
                                                                                <li><a href="#">Speckarts Air</a></li>
                                                                                <li><a href="#">Speckarts STUDIO</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tabS6" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">John Jacobs</a></li>
                                                                                <li><a href="#">Owndays</a></li>
                                                                                <li><a href="#">New Balance</a></li>
                                                                                <li><a href="#">Fossil</a></li>
                                                                                <li><a href="#">Le Petit Lunetier</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Shape</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Square Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Tilak Varma for John
                                                                                        Jacobs</a></li>
                                                                                <li><a href="#">Zodiac</a></li>
                                                                                <li><a href="#">Wildgear</a></li>
                                                                                <li><a href="#">Timeless Metals</a>
                                                                                </li>
                                                                                <li><a href="#">Headspace</a></li>
                                                                                <li><a href="#">Break the Frame</a>
                                                                                </li>
                                                                                <li><a href="#">Amore by Aditi Rao
                                                                                        Hydari</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tabS7" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Starting from
                                                                                        Rs.600</a></li>
                                                                                <li><a href="#">For your Kids</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- END tabs-content -->
                                                        </div> <!-- END tabs -->
                                                    </div>
                                                    <div id="tabS2" class="tab-content">
                                                        <div class="tabs2">
                                                            <ul id="tabs-nav2">
                                                                <li>
                                                                    <a href="#tabS8">
                                                                        <p>CLASSIC EYEGLASSES</p>
                                                                        <p>Starting from $2000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tabS9">
                                                                        <p>PREMIUM EYEGLASSES</p>
                                                                        <p>Starting from $4000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tabS10">
                                                                        <p>SCREEN EYEGLASSES</p>
                                                                        <p>Starting from $600</p>
                                                                    </a>
                                                                </li>
                                                            </ul> <!-- END tabs-nav -->
                                                            <div id="tabs-content2">
                                                                <div id="tabS8" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Speckarts BLU
                                                                                        Lenses</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Type</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Wayfarer Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Geometric Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Harry Potter</a></li>
                                                                                <li><a href="#">Aao Twyst Karein</a>
                                                                                </li>
                                                                                <li><a href="#">Hustlr - As Seen on
                                                                                        Shark Tank</a></li>
                                                                                <li><a href="#">Switch - Magnetic
                                                                                        Clips-On</a></li>
                                                                                <li><a href="#">Patriot</a></li>
                                                                                <li><a href="#">Hip Hop</a></li>
                                                                                <li><a href="#">Turban Edit</a></li>
                                                                                <li><a href="#">Classic Acetates</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">Vincent Chase</a>
                                                                                </li>
                                                                                <li><a href="#">Speckarts Air</a></li>
                                                                                <li><a href="#">Speckarts STUDIO</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tabS9" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">John Jacobs</a></li>
                                                                                <li><a href="#">Owndays</a></li>
                                                                                <li><a href="#">New Balance</a></li>
                                                                                <li><a href="#">Fossil</a></li>
                                                                                <li><a href="#">Le Petit Lunetier</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Shape</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Square Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Tilak Varma for John
                                                                                        Jacobs</a></li>
                                                                                <li><a href="#">Zodiac</a></li>
                                                                                <li><a href="#">Wildgear</a></li>
                                                                                <li><a href="#">Timeless Metals</a>
                                                                                </li>
                                                                                <li><a href="#">Headspace</a></li>
                                                                                <li><a href="#">Break the Frame</a>
                                                                                </li>
                                                                                <li><a href="#">Amore by Aditi Rao
                                                                                        Hydari</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tabS10" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Starting from
                                                                                        Rs.600</a></li>
                                                                                <li><a href="#">For your Kids</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- END tabs-content -->
                                                        </div> <!-- END tabs -->
                                                    </div>
                                                    <div id="tabS3" class="tab-content">
                                                        <div class="tabs3">
                                                            <ul id="tabs-nav3">
                                                                <li>
                                                                    <a href="#tabS11">
                                                                        <p>CLASSIC EYEGLASSES</p>
                                                                        <p>Starting from ₹1000</p>
                                                                    </a>
                                                                </li>
                                                            </ul> <!-- END tabs-nav -->
                                                            <div id="tabs-content3">
                                                                <div id="tabS11" class="tab-content3">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Trending</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Type</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Wayfarer Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Oval Frames</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Creatr</a></li>
                                                                                <li><a href="#">Hooper Mini</a></li>
                                                                                <li><a href="#">Flexi</a></li>
                                                                                <li><a href="#">Memphis</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Age</h4>
                                                                                <li><a href="#">2-5 Yrs</a></li>
                                                                                <li><a href="#">5-8 Yrs</a></li>
                                                                                <li><a href="#">8-12 Yrs</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- END tabs-content -->
                                                        </div> <!-- END tabs -->
                                                    </div>
                                                </div> <!-- END tabs-content -->
                                            </div> <!-- END tabs -->

                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="#" class="desktop-item">Kids Glasses </a>
                                    <input type="checkbox" id="showMegaKids">
                                    <label for="showMegaKids" class="mobile-item">Mega Menu</label>
                                    <div class="mega-box kidsglasses">
                                        <div class="content">

                                            <div class="rows">
                                                <header>Eyeglasses</header>
                                                <ul class="mega-links">
                                                    <li>
                                                        <a href="products.html?gender=kids&tag=new-arrival">
                                                            New Arrivals
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="products.html?gender=kids&frame_type=rectangle">
                                                            Rectangle Frames
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="products.html?gender=kids&frame_type=round">
                                                            Round Frames
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="products.html?gender=kids&frame_type=cat-eye">
                                                            Cat-Eye Frames
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="rows">
                                                <header>Screen Glasses</header>
                                                <ul class="mega-links">
                                                    <li>
                                                        <a href="products.html?gender=kids&category=screen-glasses">
                                                            All Screen Glasses
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="products.html?gender=kids&category=screen-glasses&collection=creatr">
                                                            Creatr
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="products.html?gender=kids&category=screen-glasses&collection=flexi">
                                                            Flexi
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="rows">
                                                <header>Sunglasses</header>
                                                <ul class="mega-links">
                                                    <li>
                                                        <a href="products.html?gender=kids&category=sunglasses">
                                                            All Sunglasses
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="products.html?gender=kids&category=sunglasses&brand=vincent-chase">
                                                            Vincent Chase
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="products.html?gender=kids&category=sunglasses&brand=speckarts-air">
                                                            Speckarts Air
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="contact-lenses.html" class="desktop-item">Contact Lenses </a>
                                    <input type="checkbox" id="showMega">
                                    <label for="showMega" class="mobile-item">Mega Menu</label>
                                    <div class="mega-box contactLenses">
                                        <div class="content">
                                            <div class="rows">
                                                <header>Brands</header>
                                                <ul class="mega-links">
                                                    <li><a href="#">Aqualens</a></li>
                                                    <li><a href="#">Bausch Lomb</a></li>
                                                    <li><a href="#">Soflens</a></li>
                                                    <li><a href="#">Acuvue</a></li>
                                                    <li><a href="#">Iconnect</a></li>
                                                    <li><a href="#">Alcon</a></li>
                                                </ul>
                                            </div>
                                            <div class="rows">
                                                <header>Explore By Disposablity</header>
                                                <ul class="mega-links">
                                                    <li><a href="#">Monthly</a></li>
                                                    <li><a href="#">Day & Night</a></li>
                                                    <li><a href="#">Daily</a></li>
                                                    <li><a href="#">Yearly</a></li>
                                                    <li><a href="#">Bi-weekly</a></li>
                                                </ul>
                                            </div>
                                            <div class="rows">
                                                <header>Explore By Power</header>
                                                <ul class="mega-links">
                                                    <li><a href="#">Spherical - (CYL 0.5) </a></li>
                                                    <li><a href="#">Spherical + (CYL 0.5)</a></li>
                                                    <li><a href="#">Cylindrical Power(&gt;0.75)</a></li>
                                                    <li><a href="#">Toric Power</a></li>
                                                </ul>
                                            </div>
                                            <div class="rows">
                                                <header>Explore By Color</header>
                                                <ul class="mega-links">
                                                    <li><a href="#">Green</a></li>
                                                    <li><a href="#">Blue</a></li>
                                                    <li><a href="#">Brown</a></li>
                                                    <li><a href="#">Turquoise</a></li>
                                                    <li><a href="#">View all colors</a></li>
                                                </ul>
                                            </div>
                                            <div class="rows">
                                                <header>Solution</header>
                                                <ul class="mega-links">
                                                    <li><a href="#">Small</a></li>
                                                    <li><a href="#">Large</a></li>
                                                    <li><a href="#">View all solutions</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="#">Sunglasses </a>
                                    <input type="checkbox" id="showMega">
                                    <label for="showMega" class="mobile-item">Mega Menu</label>
                                    <div class="mega-box">
                                        <div class="content1">
                                            <div class="tabs">
                                                <ul id="tabs-nav">
                                                    <li>
                                                        <a href="#tabSun1">
                                                            <img src="{{ asset('website/assets/img/icon/specs-men.png') }}"
                                                                alt="specs-men">
                                                            Men
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tabSun2">
                                                            <img src="{{ asset('website/assets/img/icon/specs-women.png') }}"
                                                                alt="specs-women">
                                                            Women
                                                        </a>
                                                    </li>
                                                </ul> <!-- END tabs-nav -->
                                                <div id="tabs-content">
                                                    <div id="tabSun1" class="tab-content">
                                                        <div class="tabs1">
                                                            <ul id="tabs-nav1">
                                                                <li>
                                                                    <a href="#tabSun5">
                                                                        <p>CLASSIC EYEGLASSES</p>
                                                                        <p>Starting from $2000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tabSun6">
                                                                        <p>PREMIUM EYEGLASSES</p>
                                                                        <p>Starting from $4000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tabSun7">
                                                                        <p>SCREEN EYEGLASSES</p>
                                                                        <p>Starting from $600</p>
                                                                    </a>
                                                                </li>
                                                            </ul> <!-- END tabs-nav -->
                                                            <div id="tabs-content1">
                                                                <div id="tabSun5" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Speckarts BLU
                                                                                        Lenses</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Type</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Wayfarer Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Geometric Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Harry Potter</a></li>
                                                                                <li><a href="#">Aao Twyst Karein</a>
                                                                                </li>
                                                                                <li><a href="#">Hustlr - As Seen on
                                                                                        Shark Tank</a></li>
                                                                                <li><a href="#">Switch - Magnetic
                                                                                        Clips-On</a></li>
                                                                                <li><a href="#">Patriot</a></li>
                                                                                <li><a href="#">Hip Hop</a></li>
                                                                                <li><a href="#">Turban Edit</a></li>
                                                                                <li><a href="#">Classic Acetates</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">Vincent Chase</a>
                                                                                </li>
                                                                                <li><a href="#">Speckarts Air</a></li>
                                                                                <li><a href="#">Speckarts STUDIO</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tabSun6" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">John Jacobs</a></li>
                                                                                <li><a href="#">Owndays</a></li>
                                                                                <li><a href="#">New Balance</a></li>
                                                                                <li><a href="#">Fossil</a></li>
                                                                                <li><a href="#">Le Petit Lunetier</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Shape</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Square Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Tilak Varma for John
                                                                                        Jacobs</a></li>
                                                                                <li><a href="#">Zodiac</a></li>
                                                                                <li><a href="#">Wildgear</a></li>
                                                                                <li><a href="#">Timeless Metals</a>
                                                                                </li>
                                                                                <li><a href="#">Headspace</a></li>
                                                                                <li><a href="#">Break the Frame</a>
                                                                                </li>
                                                                                <li><a href="#">Amore by Aditi Rao
                                                                                        Hydari</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tabSun7" class="tab-content1">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Starting from
                                                                                        Rs.600</a></li>
                                                                                <li><a href="#">For your Kids</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- END tabs-content -->
                                                        </div> <!-- END tabs -->
                                                    </div>
                                                    <div id="tabSun2" class="tab-content">
                                                        <div class="tabs2">
                                                            <ul id="tabs-nav2">
                                                                <li>
                                                                    <a href="#tabSun8">
                                                                        <p>CLASSIC EYEGLASSES</p>
                                                                        <p>Starting from $2000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tabSun9">
                                                                        <p>PREMIUM EYEGLASSES</p>
                                                                        <p>Starting from $4000</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tabSun10">
                                                                        <p>SCREEN EYEGLASSES</p>
                                                                        <p>Starting from $600</p>
                                                                    </a>
                                                                </li>
                                                            </ul> <!-- END tabs-nav -->
                                                            <div id="tabs-content2">
                                                                <div id="tabSun8" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Speckarts BLU
                                                                                        Lenses</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Type</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Wayfarer Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Geometric Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Harry Potter</a></li>
                                                                                <li><a href="#">Aao Twyst Karein</a>
                                                                                </li>
                                                                                <li><a href="#">Hustlr - As Seen on
                                                                                        Shark Tank</a></li>
                                                                                <li><a href="#">Switch - Magnetic
                                                                                        Clips-On</a></li>
                                                                                <li><a href="#">Patriot</a></li>
                                                                                <li><a href="#">Hip Hop</a></li>
                                                                                <li><a href="#">Turban Edit</a></li>
                                                                                <li><a href="#">Classic Acetates</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">Vincent Chase</a>
                                                                                </li>
                                                                                <li><a href="#">Speckarts Air</a></li>
                                                                                <li><a href="#">Speckarts STUDIO</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tabSun9" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Brands</h4>
                                                                                <li><a href="#">John Jacobs</a></li>
                                                                                <li><a href="#">Owndays</a></li>
                                                                                <li><a href="#">New Balance</a></li>
                                                                                <li><a href="#">Fossil</a></li>
                                                                                <li><a href="#">Le Petit Lunetier</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Our Top Picks</h4>
                                                                                <li><a href="#">New Arrivals</a></li>
                                                                                <li><a href="#">Best Seller</a></li>
                                                                                <li><a href="#">Progressive
                                                                                        Eyeglasses</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Frame Shape</h4>
                                                                                <li><a href="#">Rectangle Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Round Frames</a></li>
                                                                                <li><a href="#">Square Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Aviator Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Cat-Eye Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Rimless Frames</a>
                                                                                </li>
                                                                                <li><a href="#">Halfrim Frames</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Tilak Varma for John
                                                                                        Jacobs</a></li>
                                                                                <li><a href="#">Zodiac</a></li>
                                                                                <li><a href="#">Wildgear</a></li>
                                                                                <li><a href="#">Timeless Metals</a>
                                                                                </li>
                                                                                <li><a href="#">Headspace</a></li>
                                                                                <li><a href="#">Break the Frame</a>
                                                                                </li>
                                                                                <li><a href="#">Amore by Aditi Rao
                                                                                        Hydari</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tabSun10" class="tab-content2">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <ul>
                                                                                <h4>Collection</h4>
                                                                                <li><a href="#">Starting from
                                                                                        Rs.600</a></li>
                                                                                <li><a href="#">For your Kids</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- END tabs-content -->
                                                        </div> <!-- END tabs -->
                                                    </div>

                                                </div> <!-- END tabs-content -->
                                            </div> <!-- END tabs -->

                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <a href="#" class="desktop-item">Home Eye-test </a>
                                    <input type="checkbox" id="showMega">
                                    <label for="showMega" class="mobile-item">Mega Menu</label>
                                    <div class="mega-box">
                                        <div class="content d-block">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="home-eye-test-img">
                                                        <img src="{{ asset('website/assets/img/bg/eye-test.png') }}"
                                                            alt="">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="home-eye-test-txt1">
                                                        <div class="home-eye-test-txt">
                                                            <h3>Get your eyes checked at home</h3>
                                                            <p>A certified refractionist will visit you with latest eye
                                                                testing machines & 100 trial frames</p>
                                                            <a href="#" class="btn">Book appointment</a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <a href="#" class="desktop-item">Store Locator </a>
                                    <input type="checkbox" id="showMega">
                                    <label for="showMega" class="mobile-item">Mega Menu</label>
                                    <div class="mega-box">
                                        <div class="content d-block">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="home-eye-test-sec">
                                                        <ul>
                                                            <li>
                                                                <a href="#">
                                                                    <img src="{{ asset('website/assets/img/icon/Andra-Pradesh-W.png') }}"
                                                                        alt="">
                                                                    <p>Delhi</p>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#">
                                                                    <img src="{{ asset('website/assets/img/icon/Gujarat-W-1.png') }}"
                                                                        alt="">
                                                                    <p>Bangalore</p>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#">
                                                                    <img src="{{ asset('website/assets/img/icon/Maharashtra-W.png') }}"
                                                                        alt="">
                                                                    <p>Mumbai</p>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#">
                                                                    <img src="{{ asset('website/assets/img/icon/Gujarat-W.png') }}"
                                                                        alt="">
                                                                    <p>Ahmedabad</p>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#">
                                                                    <img src="{{ asset('website/assets/img/icon/Lakshadweep-W.png') }}"
                                                                        alt="">
                                                                    <p>Chennai</p>
                                                                </a>
                                                            </li>

                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="home-eye-test-txt">
                                                        <h3>Over 1800+ Speckarts Store</h3>
                                                        <p>Experience eyewear in a whole new way: Visit your nearest store
                                                            and treat yourself to 5000+ eyewear styles.</p>
                                                        <a href="#" class="btn">Locate a Store</a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>
                            <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </section>

    <!--Sidebar -->
    <div class="offcanvas offcanvas-end w-75 w-md-50" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center ms-lg-0 ms-mb-3 ms-3">

                <a href="/" class="logo" style="width: 60%">
                    <img src="{{ asset('website/assets/img/logo/Specskart-logo-png.png') }}" alt="">
                </a>
            </div>
            <button type="button" class="btn btn-dark e-3" data-bs-dismiss="offcanvas">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-x-lg" viewBox="0 0 16 16">
                    <path
                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                </svg>
            </button>
        </div>

        <div class="offcanvas-body">
            <ul class="mobile-menu ps-0">

                <li>
                    <a href="track-order.html">
                        <img src="{{ asset('website/assets/img/icon/Track-Order.png') }}">
                        <span>Track Order</span>
                    </a>
                </li>

                <li class="d-lg-block d-md-none d-none">
                    <a href="">
                        <img src="{{ asset('website/assets/img/icon/Loyalty-Points.png') }}">
                        <span>My Points</span>
                    </a>
                </li>

                <li class="d-lg-block d-md-none d-none">
                    <a href="{{ route('wishlist') }}">
                        <img src="{{ asset('website/assets/img/icon/Wishlist.png') }}">
                        <span>Wishlist</span>
                    </a>
                </li>

                <li class="d-lg-block d-md-none d-none">
                    <a href="cart.html">
                        <img src="{{ asset('website/assets/img/icon/My-Cart.png') }}">
                        <span>My Cart</span>
                    </a>
                </li>

                <li class="has-dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle">
                        <img src="{{ asset('website/assets/img/icon/More.png') }}">
                        <span>More</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="#">Lawn Care</a></li>
                        <li><a href="#">Walling & Fencing</a></li>
                        <li><a href="#">Landscape design</a></li>
                        <li><a href="#">Grounds Maintenance</a></li>
                    </ul>
                </li>

                <li class="has-dropdown">

                    <a href="login.html">

                        <p class="mb-0">
                            <img src="{{ asset('website/assets/img/icon/Signup.png') }}" alt="Login">
                        </p>

                        <p class="mb-0">
                            Sign up/Sign In
                        </p>

                    </a>

                    <!--
                        Logged-in state (static example markup, shown when a user is authenticated).
                        Swap the block above for this one manually if you need to preview the logged-in UI.

                    <a href="profile.html" class="dropdown-toggle">
                        <p class="mb-0">
                            <img src="{{ asset('website/assets/img/icon/user.png') }}" alt="John Doe" class="profile-avatar">
                        </p>
                        <p class="mb-0">John Doe</p>
                    </a>
                    <ul class="submenu">
                        <li><a href="profile.html">Profile</a></li>
                        <li><a href="my-order.html">My Order</a></li>
                        <li><a href="my-address.html">My Addresses</a></li>
                        <li><a href="my-prescription.html">My Prescription</a></li>
                        <li><a href="logout.html">Logout</a></li>
                    </ul>
                    -->

                </li>

                <li class="has-dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle">
                        <span>Shop Eyeglasses</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="#">Eyeglasses</a></li>
                        <li><a href="#">Speckarts BLU Lenses</a></li>
                        <li><a href="#">Progressive Eyeglasses</a></li>
                        <li><a href="#">Men's Eyeglasses</a></li>
                        <li><a href="#">Women's Eyeglasses</a></li>
                        <li><a href="#">Kids Eyeglasses</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle">
                        <span>Shop Sunglasses</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="#">All Sunglasses</a></li>
                        <li><a href="#">Power Sunglasses</a></li>
                        <li><a href="#">Vincent Chase</a></li>
                        <li><a href="#">Polarized Sunglasses</a></li>
                        <li><a href="#">Aviator</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle">
                        <span>Shop Contact Lens</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="#">Colored Contact Lens</a></li>
                        <li><a href="#">Yearly</a></li>
                        <li><a href="#">Daily</a></li>
                        <li><a href="#">Monthly</a></li>
                        <li><a href="#">Day & Night</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('#searchInput').on('focus', function() {
                $('#searchDropdown').fadeIn(200);
            });

            $(document).on('click', function(e) {

                if (!$(e.target).closest('.search-box').length) {
                    $('#searchDropdown').fadeOut(200);
                }

            });

            // ===== Loyalty Points: toggle dropdown =====
            $('#loyaltyToggle').on('click', function(e) {
                e.preventDefault();
                $('#loyaltyDropdown').toggleClass('show');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.loyalty-link, .loyalty-dropdown').length) {
                    $('#loyaltyDropdown').removeClass('show');
                }
            });

            // ===== Loyalty Points: fetch balance via AJAX =====
            function loadLoyaltyPoints() {
                $.ajax({
                    url: "",
                    method: "GET",
                    success: function(res) {
                        if (res.points > 0) {
                            $('#loyaltyPointsCount, #loyaltyPointsCountMobile')
                                .text(res.points)
                                .removeClass('d-none');
                            $('#loyaltyPointsValue').text(res.points + ' pts');
                            $('#loyaltyPointsWorth').text(res.worth);
                        }
                    },
                    error: function() {
                        // Guest user or API error — badge stays hidden
                    }
                });
            }

            // loadLoyaltyPoints();
            // ===== End Loyalty Points JS =====

        });
        document.querySelectorAll('.dropdown-toggle').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('active');
            });
        });

        document.querySelectorAll('.has-sub-dropdown > a').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('active');
            });
        });
    </script>
    <!-- end navbar -->