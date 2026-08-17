<!-- navbar -->
@php
    $initialWishlistCount = 0;
    if (auth()->check()) {
        $initialWishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
    }
    $initialCartCount = count(session('cart', []));

    // Dynamic Navigation Models
    $navCategories = \Illuminate\Support\Facades\Schema::hasTable('categories')
        ? \App\Models\Category::where('is_active', 1)->get()
        : collect();

    $navFrameTypes = \Illuminate\Support\Facades\Schema::hasTable('tbl_type') 
        ? \App\Models\FrameType::where(function($q) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_type', 'is_active')) {
                $q->where('is_active', 1);
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('tbl_type', 'status')) {
                $q->where('status', 1);
            }
        })->get()
        : collect();

    $navCollections = \Illuminate\Support\Facades\Schema::hasTable('collections') 
        ? \App\Models\Collection::where('is_active', 1)->get() 
        : collect();

    $navBrands = \Illuminate\Support\Facades\Schema::hasTable('tbl_brand') 
        ? \App\Models\Brand::where(function($q) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_brand', 'is_active')) {
                $q->where('is_active', 1);
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('tbl_brand', 'status')) {
                $q->where('status', 1);
            }
        })->get()
        : collect();

    $menuItems = [
        [
            'label'    => 'EYEGLASSES',
            'genders'  => ['Men', 'Women', 'Kids'],
            'category' => null,
        ],
        [
            'label'    => 'SCREEN GLASSES',
            'genders'  => ['Men', 'Women', 'Kids'],
            'category' => 'screen-glasses',
        ],
        [
            'label'    => 'KIDS GLASSES',
            'genders'  => ['Kids'],
            'category' => null,
        ],
        [
            'label'    => 'CONTACT LENSES',
            'href'     => url('/contact-lenses'),
            'link'     => true,
        ],
        [
            'label'    => 'SUNGLASSES',
            'genders'  => ['Men', 'Women'],
            'category' => 'sunglasses',
        ],
        [
            'label'    => 'HOME EYE-TEST',
            'is_home_test' => true,
        ],
        [
            'label'    => 'STORE LOCATOR',
            'is_store_locator' => true,
        ],
    ];
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
                <div class="top-menu px-0 flex-wrap flex-md-wrap flex-lg-nowrap">
                    <div class="d-lg-block d-md-none d-none">
                        <div class="d-flex align-items-center ms-lg-0 ms-mb-3 ms-3">
                            <a href="{{ route('home') }}" class="logo">
                                <img src="{{ asset('website/assets/img/logo/Specskart-logo-png.png') }}" alt="Speckarts">
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

                            <li class="dropdown pe-0">
                                @guest
                                    {{-- Guest: show Sign up / Sign In link --}}
                                    <a href="{{ route('login.web') }}">
                                        <p>
                                            <img src="{{ asset('website/assets/img/icon/Signup.png') }}" alt="Login">
                                        </p>
                                        <p>Sign up / Sign In</p>
                                    </a>
                                @endguest

                                @auth
                                    {{-- Authenticated: show user avatar + name with dropdown --}}
                                    <a href="javascript:void(0);" class="dropdown-toggle">
                                        <p>
                                            <img src="{{ asset('website/assets/img/icon/user.png') }}" alt="{{ Auth::user()->name }}" class="profile-avatar">
                                        </p>
                                        <p>{{ Auth::user()->name }}</p>
                                    </a>
                                    <ul class="dropdown-nav">
                                        <li class="dropdown-item"><a href="{{ route('profile') }}">Profile</a></li>
                                        <li class="dropdown-item"><a href="{{ route('my-orders') }}">My Orders</a></li>
                                        <li class="dropdown-item"><a href="{{ route('my-addresses') }}">My Addresses</a></li>
                                        <li class="dropdown-item"><a href="{{ route('my-prescriptions') }}">My Prescriptions</a></li>
                                        <li class="dropdown-item">
                                            <form method="POST" action="{{ route('logout.web') }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:inherit;">Logout</button>
                                            </form>
                                        </li>
                                    </ul>
                                @endauth
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Mega Navigation Bar -->
    <nav class="d-lg-block d-md-none d-none">
        <div class="wrapper">
            <div class="container">
                <div class="row mb-0">
                    <div class="col-lg-12 text-center">
                        <input type="radio" name="slider" id="menu-btn">
                        <input type="radio" name="slider" id="close-btn">
                        <ul class="nav-links gap-2">
                            <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
                            
                            @foreach($menuItems as $menu)
                                @if(!empty($menu['is_home_test']))
                                    {{-- Home Eye-Test Teaser Flyout --}}
                                    <li>
                                        <a href="#" class="desktop-item">{{ $menu['label'] }}</a>
                                        <input type="checkbox" id="showMegaEyeTest">
                                        <label for="showMegaEyeTest" class="mobile-item">Mega Menu</label>
                                        <div class="mega-box">
                                            <div class="content d-block">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="home-eye-test-img">
                                                            <img src="{{ asset('website/assets/img/bg/eye-test.png') }}" alt="Eye Test">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="home-eye-test-txt1">
                                                            <div class="home-eye-test-txt">
                                                                <h3>Get your eyes checked at home</h3>
                                                                <p>A certified refractionist will visit you with latest eye testing machines & 100 trial frames</p>
                                                                <a href="#" class="btn">Book appointment</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @elseif(!empty($menu['is_store_locator']))
                                    {{-- Store Locator Flyout --}}
                                    <li>
                                        <a href="#" class="desktop-item">{{ $menu['label'] }}</a>
                                        <input type="checkbox" id="showMegaStoreLocator">
                                        <label for="showMegaStoreLocator" class="mobile-item">Mega Menu</label>
                                        <div class="mega-box">
                                            <div class="content d-block">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="home-eye-test-sec">
                                                            <ul>
                                                                <li>
                                                                    <a href="#">
                                                                        <img src="{{ asset('website/assets/img/icon/Andra-Pradesh-W.png') }}" alt="">
                                                                        <p>Delhi</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#">
                                                                        <img src="{{ asset('website/assets/img/icon/Gujarat-W-1.png') }}" alt="">
                                                                        <p>Bangalore</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#">
                                                                        <img src="{{ asset('website/assets/img/icon/Maharashtra-W.png') }}" alt="">
                                                                        <p>Mumbai</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#">
                                                                        <img src="{{ asset('website/assets/img/icon/Gujarat-W.png') }}" alt="">
                                                                        <p>Ahmedabad</p>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#">
                                                                        <img src="{{ asset('website/assets/img/icon/Lakshadweep-W.png') }}" alt="">
                                                                        <p>Chennai</p>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="home-eye-test-txt">
                                                            <h3>Over 1800+ Speckarts Store</h3>
                                                            <p>Experience eyewear in a whole new way: Visit your nearest store and treat yourself to 5000+ eyewear styles.</p>
                                                            <a href="#" class="btn">Locate a Store</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @elseif(!empty($menu['link']))
                                    {{-- Standard Direct Link (e.g. Contact Lenses) --}}
                                    <li><a href="{{ $menu['href'] }}" class="desktop-item">{{ $menu['label'] }}</a></li>
                                @else
                                    {{-- Dynamic Mega Menu (Eyeglasses, Screen Glasses, Sunglasses, Kids Glasses) --}}
                                    @php
                                        $menuSlug = \Illuminate\Support\Str::slug($menu['label']);
                                        $primaryGender = $menu['genders'][0] ?? 'Men';
                                        $primaryRoute = ['gender' => strtolower($primaryGender)];
                                        if (!empty($menu['category'])) {
                                            $primaryRoute['category'] = $menu['category'];
                                        }
                                    @endphp
                                    <li>
                                        <a href="{{ route('products', $primaryRoute) }}" class="desktop-item">{{ $menu['label'] }}</a>
                                        <input type="checkbox" id="showMega-{{ $menuSlug }}">
                                        <label for="showMega-{{ $menuSlug }}" class="mobile-item">{{ $menu['label'] }}</label>
                                        <div class="mega-box">
                                            <div class="content1">
                                                <div class="tabs">
                                                    {{-- Gender Tabs Nav --}}
                                                    <ul id="tabs-nav" class="tabs-nav">
                                                        @foreach($menu['genders'] as $gender)
                                                            @php
                                                                $genderSlug = \Illuminate\Support\Str::slug($gender);
                                                                $tabTargetId = 'tab-' . $menuSlug . '-' . $genderSlug;
                                                                $iconName = 'specs-' . (strtolower($gender) == 'kids' ? 'kid' : strtolower($gender)) . '.png';
                                                            @endphp
                                                            <li>
                                                                <a href="#{{ $tabTargetId }}">
                                                                    <img src="{{ asset('website/assets/img/icon/' . $iconName) }}" alt="{{ $gender }}">
                                                                    {{ $gender }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    {{-- Gender Tab Content Panes --}}
                                                    <div id="tabs-content" class="tabs-content">
                                                        @foreach($menu['genders'] as $gender)
                                                            @php
                                                                $genderSlug = \Illuminate\Support\Str::slug($gender);
                                                                $tabTargetId = 'tab-' . $menuSlug . '-' . $genderSlug;
                                                                $baseFilters = ['gender' => strtolower($gender)];
                                                                if (!empty($menu['category'])) {
                                                                    $baseFilters['category'] = $menu['category'];
                                                                }
                                                            @endphp
                                                            <div id="{{ $tabTargetId }}" class="tab-content">
                                                                <div class="row">
                                                                    {{-- Col 1: Top Picks --}}
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="{{ route('products', array_merge($baseFilters, ['tag' => 'new-arrival'])) }}">New Arrivals</a></li>
                                                                            <li><a href="{{ route('products', array_merge($baseFilters, ['tag' => 'best-seller'])) }}">Best Seller</a></li>
                                                                            <li><a href="{{ route('products', array_merge($baseFilters, ['tag' => 'trending'])) }}">Trending</a></li>
                                                                            <li><a href="{{ route('products', array_merge($baseFilters, ['type' => 'frame'])) }}">Frame Only</a></li>
                                                                        </ul>
                                                                    </div>

                                                                    {{-- Col 2: Dynamic Frame Types --}}
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            @if($navFrameTypes->count() > 0)
                                                                                @foreach($navFrameTypes->take(8) as $type)
                                                                                    @php
                                                                                        $tName = $type->name ?? $type->type_name;
                                                                                        $tSlug = $type->slug ?? $type->type_name;
                                                                                    @endphp
                                                                                    <li><a href="{{ route('products', array_merge($baseFilters, ['frame_type' => $tSlug])) }}">{{ $tName }}</a></li>
                                                                                @endforeach
                                                                            @else
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['frame_type' => 'Rectangle'])) }}">Rectangle Frames</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['frame_type' => 'Round'])) }}">Round Frames</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['frame_type' => 'Aviator'])) }}">Aviator Frames</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['frame_type' => 'Cat-Eye'])) }}">Cat-Eye Frames</a></li>
                                                                            @endif
                                                                        </ul>
                                                                    </div>

                                                                    {{-- Col 3: Dynamic Collections --}}
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            @if($navCollections->count() > 0)
                                                                                @foreach($navCollections->take(8) as $collection)
                                                                                    @php
                                                                                        $cName = $collection->name;
                                                                                        $cSlug = $collection->slug ?? $collection->name;
                                                                                    @endphp
                                                                                    <li><a href="{{ route('products', array_merge($baseFilters, ['collection' => $cSlug])) }}">{{ $cName }}</a></li>
                                                                                @endforeach
                                                                            @else
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['tag' => 'classic'])) }}">Classic Eyeglasses</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['tag' => 'premium'])) }}">Premium Collection</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['tag' => 'matte'])) }}">Matte Essentials</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['tag' => 'ultralight'])) }}">Ultralight Series</a></li>
                                                                            @endif
                                                                        </ul>
                                                                    </div>

                                                                    {{-- Col 4: Dynamic Brands --}}
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            @if($navBrands->count() > 0)
                                                                                @foreach($navBrands->take(8) as $brand)
                                                                                    @php
                                                                                        $bName = $brand->name ?? $brand->brand_name;
                                                                                        $bSlug = $brand->slug ?? $brand->brand_name;
                                                                                    @endphp
                                                                                    <li><a href="{{ route('products', array_merge($baseFilters, ['brand' => $bSlug])) }}">{{ $bName }}</a></li>
                                                                                @endforeach
                                                                            @else
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['brand' => 'Speckarts'])) }}">Speckarts</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['brand' => 'Vincent Chase'])) }}">Vincent Chase</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['brand' => 'John Jacobs'])) }}">John Jacobs</a></li>
                                                                                <li><a href="{{ route('products', array_merge($baseFilters, ['brand' => 'Titan'])) }}">Titan</a></li>
                                                                            @endif
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</section>

<!-- Mobile Sidebar Offcanvas -->
<div class="offcanvas offcanvas-end w-75 w-md-50" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header">
        <div class="d-flex align-items-center ms-lg-0 ms-mb-3 ms-3">
            <a href="{{ route('home') }}" class="logo" style="width: 60%">
                <img src="{{ asset('website/assets/img/logo/Specskart-logo-png.png') }}" alt="Speckarts">
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
            <li class="d-lg-block d-md-none d-none">
                <a href="{{ route('wishlist') }}">
                    <img src="{{ asset('website/assets/img/icon/Wishlist.png') }}">
                    <span>Wishlist</span>
                </a>
            </li>

            <li class="d-lg-block d-md-none d-none">
                <a href="{{ route('cart') }}">
                    <img src="{{ asset('website/assets/img/icon/My-Cart.png') }}">
                    <span>My Cart</span>
                </a>
            </li>

            <li class="has-dropdown">
                @guest
                    <a href="{{ route('login.web') }}">
                        <p class="mb-0">
                            <img src="{{ asset('website/assets/img/icon/Signup.png') }}" alt="Login">
                        </p>
                        <p class="mb-0">Sign up / Sign In</p>
                    </a>
                @endguest

                @auth
                    <a href="javascript:void(0);" class="dropdown-toggle">
                        <p class="mb-0">
                            <img src="{{ asset('website/assets/img/icon/user.png') }}" alt="{{ Auth::user()->name }}" class="profile-avatar">
                        </p>
                        <p class="mb-0">{{ Auth::user()->name }}</p>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('profile') }}">Profile</a></li>
                        <li><a href="{{ route('my-orders') }}">My Orders</a></li>
                        <li><a href="{{ route('my-addresses') }}">My Addresses</a></li>
                        <li><a href="{{ route('my-prescriptions') }}">My Prescriptions</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout.web') }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:inherit;">Logout</button>
                            </form>
                        </li>
                    </ul>
                @endauth
            </li>

            {{-- Dynamic Categories in Mobile Menu --}}
            @foreach($menuItems as $menu)
                @if(!empty($menu['link']))
                    <li>
                        <a href="{{ $menu['href'] }}">
                            <span>{{ $menu['label'] }}</span>
                        </a>
                    </li>
                @elseif(!empty($menu['genders']))
                    <li class="has-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle">
                            <span>Shop {{ ucwords(strtolower($menu['label'])) }}</span>
                        </a>
                        <ul class="submenu">
                            @foreach($menu['genders'] as $gender)
                                @php
                                    $mRoute = ['gender' => strtolower($gender)];
                                    if (!empty($menu['category'])) {
                                        $mRoute['category'] = $menu['category'];
                                    }
                                @endphp
                                <li><a href="{{ route('products', $mRoute) }}">{{ $gender }}'s {{ ucwords(strtolower($menu['label'])) }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
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

        // Dynamic Mega Menu Tabs Hover Handling (Scoped per mega-box)
        $('.mega-box .tabs').each(function() {
            var $tabs = $(this);
            $tabs.find('.tabs-nav li:first-child, #tabs-nav li:first-child').addClass('active');
            $tabs.find('.tab-content').hide();
            $tabs.find('.tab-content:first').show();
        });

        $(document).on('mouseenter', '.mega-box .tabs ul li', function() {
            var $tabs = $(this).closest('.tabs');
            $tabs.find('li').removeClass('active');
            $(this).addClass('active');
            $tabs.find('.tab-content').hide();

            var activeTab = $(this).find('a').attr('href');
            if (activeTab && activeTab.startsWith('#')) {
                $(activeTab).fadeIn(150);
            }
            return false;
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