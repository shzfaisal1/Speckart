@php
    $navCategories = \App\Models\Category::where('is_active', 1)->get();
    $navFrameTypes = \App\Models\FrameType::where('is_active', 1)->get();
    $navCollections = \App\Models\Collection::where('is_active', 1)->get();
    $navBrands = \App\Models\Brand::where('is_active', 1)->get();
@endphp
<!-- navbar -->

{{-- Login Modal Popup (available on all frontend pages) --}}
@include('website.web.layout.partial.login-modal')

<section class="header" data-is-logged-in="{{ auth()->check() ? 'true' : 'false' }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="top-menu">
                    <a href="{{ route('home')}}">
                    <div class="logo">
                        <img src="{{asset('assets/img/logo/Specskart-logo-png.png')}}" alt="">
                    </div>
                    </a>
                    <div class="search-box position-relative">
                        <form action="{{ route('products') }}" method="GET" id="header-search-form">
                            <div class="input-group">
                                <input type="text" name="search" id="header-search-input" class="form-control ajax-search-input" placeholder="search glasses, lenses, specs.." value="{{ request('search') }}" autocomplete="off">
                                <button class="btn btn-success" type="submit">Search</button>
                            </div>
                        </form>
                        <div id="search-suggestions-dropdown" class="search-suggestions-dropdown ajax-search-dropdown" style="display:none;">
                            <div class="search-suggestions-content">
                                <!-- Suggestions will be populated here via AJAX -->
                            </div>
                        </div>
                    </div>
                    <div class="top-menu-list">
                        <ul>
                            <li>
                                <a href="">
                                    <p>
                                        <img src="{{asset('assets/img/icon/Track-Order.png')}}" alt="">
                                    </p>
                                    <p>Track Order</p>
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <p>
                                        <img src="{{asset('assets/img/icon/Wishlist.png')}}" alt="">
                                    </p>
                                    <p>Wishlist</p>
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <p>
                                        <img src="{{asset('assets/img/icon/My-Cart.png')}}" alt="">
                                    </p>
                                    <p>My Cart</p>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a href="">
                                    <p>
                                        <img src="{{asset('assets/img/icon/More.png')}}" alt="">
                                    </p>
                                    <p>More</p>
                                </a>
                                <ul class="dropdown-nav">
					                <li><a href="">Lawn Care</a></li>
					                <li><a href="">Walling &amp; Fencing</a></li>
					                <li><a href="">Landscape design</a></li>
					                <li><a href="">Grounds Maintenance</a></li>
				                </ul>
			                </li>
		                
                            @guest
                            <li>
                                <a href="{{route('login.web')}}">
                                    <p>
                                        <img src="{{asset('assets/img/icon/Signup.png')}}" alt="">
                                    </p>
                                    <p>Sign up/Sign In</p>
                                </a>
                            </li>
                            @else
                            <li class="dropdown">
                                <a href="{{route('profile')}}">
                                    <p>
                                        <img src="{{asset('assets/img/icon/Signup.png')}}" alt="">
                                    </p>
                                    <p>{{ Auth::user()->name ?: 'My Profile' }}</p>
                                </a>
                                <ul class="dropdown-nav">
					                <li><a href="{{route('profile')}}">Profile</a></li>
					                <li><a href="{{route('my_order')}}">My Order</a></li>
					                <li><a href="{{route('my_address')}}">My Addresses</a></li>
					                <li><a href="{{route('my_prescription')}}">My Prescription</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none;">
                                            @csrf
                                        </form>
                                        <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit();">Logout</a>
                                    </li>
				                </ul>
                            </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <nav>
        <div class="wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- <div class="logo"><a href="#"> Mega Menu</a></div> -->
                        <input type="radio" name="slider" id="menu-btn">
                        <input type="radio" name="slider" id="close-btn">
                        <ul class="nav-links">
                            <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
                            <li>
                                <a href="#" class="desktop-item">Eyeglasses +</a>
                                <input type="checkbox" id="showMega">
                                <label for="showMega" class="mobile-item">Mega Menu</label>
                                <div class="mega-box">
                                    <div class="content1">
                                        <div class="tabs">
                                            <ul id="tabs-nav">
                                                <li>
                                                    <a href="#tab1">
                                                        <img src="{{asset('assets/img/icon/specs-men.png')}}" alt="specs-men">
                                                        Men
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tab2">
                                                        <img src="{{asset('assets/img/icon/specs-women.png')}}" alt="specs-women">
                                                        Women
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tab3">
                                                        <img src="{{asset('assets/img/icon/specs-kid.png')}}" alt="specs-kid">
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
                                                                            <li><a href="{{ url('products/men/new-arrival') }}">New Arrivals</a></li>
                                                                            <li><a href="{{ url('products/men/best-seller') }}">Best Seller</a></li>
                                                                            <li><a href="{{ url('products/men/trending') }}">Trending</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            @foreach($navFrameTypes as $type)
                                                                                <li><a href="{{ url('products/men/' . $type->slug) }}">{{ $type->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            @foreach($navCollections as $collection)
                                                                                <li><a href="{{ url('products/men/' . $collection->slug) }}">{{ $collection->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            @foreach($navBrands as $brand)
                                                                                <li><a href="{{ url('products/men/' . $brand->slug) }}">{{ $brand->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tab6" class="tab-content1">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="">John Jacobs</a></li>
                                                                            <li><a href="">Owndays</a></li>
                                                                            <li><a href="">New Balance</a></li>
                                                                            <li><a href="">Fossil</a></li>
                                                                            <li><a href="">Le Petit Lunetier</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="">New Arrivals</a></li>
                                                                            <li><a href="">Best Seller</a></li>
                                                                            <li><a href="">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Shape</h4>
                                                                            <li><a href="">Rectangle Frames</a></li>
                                                                            <li><a href="">Round Frames</a></li>
                                                                            <li><a href="">Square Frames</a></li>
                                                                            <li><a href="">Aviator Frames</a></li>
                                                                            <li><a href="">Cat-Eye Frames</a></li>
                                                                            <li><a href="">Rimless Frames</a></li>
                                                                            <li><a href="">Halfrim Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Tilak Varma for John Jacobs</a></li>
                                                                            <li><a href="">Zodiac</a></li>
                                                                            <li><a href="">Wildgear</a></li>
                                                                            <li><a href="">Timeless Metals</a></li>
                                                                            <li><a href="">Headspace</a></li>
                                                                            <li><a href="">Break the Frame</a></li>
                                                                            <li><a href="">Amore by Aditi Rao Hydari</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tab7" class="tab-content1">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Starting from Rs.600</a></li>
                                                                            <li><a href="">For your Kids</a></li>
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
                                                                                    <li><a href="{{ route('products', ['gender' => 'women', 'tag' => 'new-arrival']) }}">New Arrivals</a></li>
                                                                                    <li><a href="{{ route('products', ['gender' => 'women', 'tag' => 'best-seller']) }}">Best Seller</a></li>
                                                                                    <li><a href="{{ route('products', ['gender' => 'women', 'tag' => 'trending']) }}">Trending</a></li>
                                                                                </ul>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <ul>
                                                                                    <h4>Frame Type</h4>
                                                                                    @foreach($navFrameTypes as $type)
                                                                                        <li><a href="{{ route('products', ['gender' => 'women', 'frame_type' => $type->slug]) }}">{{ $type->name }}</a></li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <ul>
                                                                                    <h4>Collection</h4>
                                                                                    @foreach($navCollections as $collection)
                                                                                        <li><a href="{{ route('products', ['gender' => 'women', 'collection' => $collection->slug]) }}">{{ $collection->name }}</a></li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <ul>
                                                                                    <h4>Brands</h4>
                                                                                    @foreach($navBrands as $brand)
                                                                                        <li><a href="{{ route('products', ['gender' => 'women', 'brand' => $brand->slug]) }}">{{ $brand->name }}</a>
                                                                                    </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            <div id="tab9" class="tab-content2">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="">John Jacobs</a></li>
                                                                            <li><a href="">Owndays</a></li>
                                                                            <li><a href="">New Balance</a></li>
                                                                            <li><a href="">Fossil</a></li>
                                                                            <li><a href="">Le Petit Lunetier</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="">New Arrivals</a></li>
                                                                            <li><a href="">Best Seller</a></li>
                                                                            <li><a href="">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Shape</h4>
                                                                            <li><a href="">Rectangle Frames</a></li>
                                                                            <li><a href="">Round Frames</a></li>
                                                                            <li><a href="">Square Frames</a></li>
                                                                            <li><a href="">Aviator Frames</a></li>
                                                                            <li><a href="">Cat-Eye Frames</a></li>
                                                                            <li><a href="">Rimless Frames</a></li>
                                                                            <li><a href="">Halfrim Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Tilak Varma for John Jacobs</a></li>
                                                                            <li><a href="">Zodiac</a></li>
                                                                            <li><a href="">Wildgear</a></li>
                                                                            <li><a href="">Timeless Metals</a></li>
                                                                            <li><a href="">Headspace</a></li>
                                                                            <li><a href="">Break the Frame</a></li>
                                                                            <li><a href="">Amore by Aditi Rao Hydari</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tab10" class="tab-content2">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Starting from Rs.600</a></li>
                                                                            <li><a href="">For your Kids</a></li>
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
                                                                            <li><a href="{{ route('products', ['gender' => 'kids', 'tag' => 'new-arrival']) }}">New Arrivals</a></li>
                                                                            <li><a href="{{ route('products', ['gender' => 'kids', 'tag' => 'best-seller']) }}">Best Seller</a></li>
                                                                            <li><a href="{{ route('products', ['gender' => 'kids', 'tag' => 'trending']) }}">Trending</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            @foreach($navFrameTypes as $type)
                                                                                <li><a href="{{ route('products', ['gender' => 'kids', 'frame_type' => $type->slug]) }}">{{ $type->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            @foreach($navCollections as $collection)
                                                                                <li><a href="{{ route('products', ['gender' => 'kids', 'collection' => $collection->slug]) }}">{{ $collection->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            @foreach($navBrands as $brand)
                                                                                <li><a href="{{ route('products', ['gender' => 'kids', 'brand' => $brand->slug]) }}">{{ $brand->name }}</a></li>
                                                                            @endforeach
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
                                <a href="#">Screen Glasses +</a>
                                <input type="checkbox" id="showMega">
                                <label for="showMega" class="mobile-item">Mega Menu</label>
                                <div class="mega-box">
                                    <div class="content1">
                                        <div class="tabs">
                                            <ul id="tabs-nav">
                                                <li>
                                                    <a href="#tabS1">
                                                        <img src="{{asset('assets/img/icon/specs-men.png')}}" alt="specs-men">
                                                        Men
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tabS2">
                                                        <img src="{{asset('assets/img/icon/specs-women.png')}}" alt="specs-women">
                                                        Women
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tabS3">
                                                        <img src="{{asset('assets/img/icon/specs-kid.png')}}" alt="specs-kid">
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
                                                                            <li><a href="{{ route('products', ['gender' => 'men', 'category' => 'screen-glasses', 'tag' => 'new-arrival']) }}">New Arrivals</a></li>
                                                                            <li><a href="{{ route('products', ['gender' => 'men', 'category' => 'screen-glasses', 'tag' => 'best-seller']) }}">Best Seller</a></li>
                                                                            <li><a href="{{ route('products', ['gender' => 'men', 'category' => 'screen-glasses', 'tag' => 'trending']) }}">Trending</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            @foreach($navFrameTypes as $type)
                                                                                <li><a href="{{ route('products', ['gender' => 'men', 'category' => 'screen-glasses', 'frame_type' => $type->slug]) }}">{{ $type->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            @foreach($navCollections as $collection)
                                                                                <li><a href="{{ route('products', ['gender' => 'men', 'category' => 'screen-glasses', 'collection' => $collection->slug]) }}">{{ $collection->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            @foreach($navBrands as $brand)
                                                                                <li><a href="{{ route('products', ['gender' => 'men', 'category' => 'screen-glasses', 'brand' => $brand->slug]) }}">{{ $brand->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tabS6" class="tab-content1">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="">John Jacobs</a></li>
                                                                            <li><a href="">Owndays</a></li>
                                                                            <li><a href="">New Balance</a></li>
                                                                            <li><a href="">Fossil</a></li>
                                                                            <li><a href="">Le Petit Lunetier</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="">New Arrivals</a></li>
                                                                            <li><a href="">Best Seller</a></li>
                                                                            <li><a href="">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Shape</h4>
                                                                            <li><a href="">Rectangle Frames</a></li>
                                                                            <li><a href="">Round Frames</a></li>
                                                                            <li><a href="">Square Frames</a></li>
                                                                            <li><a href="">Aviator Frames</a></li>
                                                                            <li><a href="">Cat-Eye Frames</a></li>
                                                                            <li><a href="">Rimless Frames</a></li>
                                                                            <li><a href="">Halfrim Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Tilak Varma for John Jacobs</a></li>
                                                                            <li><a href="">Zodiac</a></li>
                                                                            <li><a href="">Wildgear</a></li>
                                                                            <li><a href="">Timeless Metals</a></li>
                                                                            <li><a href="">Headspace</a></li>
                                                                            <li><a href="">Break the Frame</a></li>
                                                                            <li><a href="">Amore by Aditi Rao Hydari</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tabS7" class="tab-content1">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Starting from Rs.600</a></li>
                                                                            <li><a href="">For your Kids</a></li>
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
                                                                    <p>Starting from ₹1000</p>
                                                                </a>
                                                            </li>
                                                        </ul> <!-- END tabs-nav -->
                                                        <div id="tabs-content2">
                                                            <div id="tabS8" class="tab-content2">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="{{ route('products', ['gender' => 'women', 'category' => 'screen-glasses', 'tag' => 'new-arrival']) }}">New Arrivals</a></li>
                                                                            <li><a href="{{ route('products', ['gender' => 'women', 'category' => 'screen-glasses', 'tag' => 'best-seller']) }}">Best Seller</a></li>
                                                                            <li><a href="{{ route('products', ['gender' => 'women', 'category' => 'screen-glasses', 'tag' => 'trending']) }}">Trending</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            @foreach($navFrameTypes as $type)
                                                                                <li><a href="{{ route('products', ['gender' => 'women', 'category' => 'screen-glasses', 'frame_type' => $type->slug]) }}">{{ $type->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            @foreach($navCollections as $collection)
                                                                                <li><a href="{{ route('products', ['gender' => 'women', 'category' => 'screen-glasses', 'collection' => $collection->slug]) }}">{{ $collection->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            @foreach($navBrands as $brand)
                                                                                <li><a href="{{ route('products', ['gender' => 'women', 'category' => 'screen-glasses', 'brand' => $brand->slug]) }}">{{ $brand->name }}</a></li>
                                                                            @endforeach
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
                                                                    <p>KIDS SCREEN GLASSES</p>
                                                                    <p>Starting from ₹600</p>
                                                                </a>
                                                            </li>
                                                        </ul> <!-- END tabs-nav -->
                                                        <div id="tabs-content3">
                                                            <div id="tabS11" class="tab-content3">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'screen-glasses', 'tag' => 'new-arrival']) }}">New Arrivals</a></li>
                                                                            <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'screen-glasses', 'tag' => 'best-seller']) }}">Best Seller</a></li>
                                                                            <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'screen-glasses', 'tag' => 'trending']) }}">Trending</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            @foreach($navFrameTypes as $type)
                                                                                <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'screen-glasses', 'frame_type' => $type->slug]) }}">{{ $type->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            @foreach($navCollections as $collection)
                                                                                <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'screen-glasses', 'collection' => $collection->slug]) }}">{{ $collection->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            @foreach($navBrands as $brand)
                                                                                <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'screen-glasses', 'brand' => $brand->slug]) }}">{{ $brand->name }}</a></li>
                                                                            @endforeach
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
                                <a href="#" class="desktop-item">Kids Glasses +</a>
                                <input type="checkbox" id="showMegaKids">
                                <label for="showMegaKids" class="mobile-item">Mega Menu</label>
                                <div class="mega-box">
                                    <div class="content content2">
                                        <div class="row justify-content-center p-4">
                                            <div class="col-lg-4 border-end">
                                                <ul class="list-unstyled">
                                                    <h5 class="fw-bold mb-3 border-bottom pb-2">Eyeglasses</h5>
                                                    <li><a href="{{ route('products', ['gender' => 'kids', 'tag' => 'new-arrival']) }}">New Arrivals</a></li>
                                                    @foreach($navFrameTypes as $type)
                                                        <li><a href="{{ route('products', ['gender' => 'kids', 'frame_type' => $type->slug]) }}">{{ $type->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="col-lg-4 border-end">
                                                <ul class="list-unstyled">
                                                    <h5 class="fw-bold mb-3 border-bottom pb-2">Screen Glasses</h5>
                                                    <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'screen-glasses']) }}">All Screen Glasses</a></li>
                                                    @foreach($navCollections as $collection)
                                                        <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'screen-glasses', 'collection' => $collection->slug]) }}">{{ $collection->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="col-lg-4">
                                                <ul class="list-unstyled">
                                                    <h5 class="fw-bold mb-3 border-bottom pb-2">Sunglasses</h5>
                                                    <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'sunglasses']) }}">All Sunglasses</a></li>
                                                    @foreach($navBrands as $brand)
                                                        <li><a href="{{ route('products', ['gender' => 'kids', 'category' => 'sunglasses', 'brand' => $brand->slug]) }}">{{ $brand->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a href="{{url('/contact-lenses')}}" class="desktop-item">Contact Lenses +</a>
                                <input type="checkbox" id="showMega">
                                <label for="showMega" class="mobile-item">Mega Menu</label>
                                <div class="mega-box">
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
                                                <li><a href="#">Cylindrical Power(>0.75)</a></li>
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
                                <a href="#">Sunglasses +</a>
                                <input type="checkbox" id="showMega">
                                <label for="showMega" class="mobile-item">Mega Menu</label>
                                <div class="mega-box">
                                    <div class="content1">
                                        <div class="tabs">
                                            <ul id="tabs-nav">
                                                <li>
                                                    <a href="#tabSun1">
                                                        <img src="{{asset('assets/img/icon/specs-men.png')}}" alt="specs-men">
                                                        Men
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tabSun2">
                                                        <img src="{{asset('assets/img/icon/specs-women.png')}}" alt="specs-women">
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
                                                                            <li><a href="">New Arrivals</a></li>
                                                                            <li><a href="">Best Seller</a></li>
                                                                            <li><a href="">Lenskart BLU Lenses</a></li>
                                                                            <li><a href="">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            <li><a href="">Rectangle Frames</a></li>
                                                                            <li><a href="">Wayfarer Frames</a></li>
                                                                            <li><a href="">Round Frames</a></li>
                                                                            <li><a href="">Aviator Frames</a></li>
                                                                            <li><a href="">Cat-Eye Frames</a></li>
                                                                            <li><a href="">Rimless Frames</a></li>
                                                                            <li><a href="">Halfrim Frames</a></li>
                                                                            <li><a href="">Geometric Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Harry Potter</a></li>
                                                                            <li><a href="">Aao Twyst Karein</a></li>
                                                                            <li><a href="">Hustlr - As Seen on Shark Tank</a></li>
                                                                            <li><a href="">Switch - Magnetic Clips-On</a></li>
                                                                            <li><a href="">Patriot</a></li>
                                                                            <li><a href="">Hip Hop</a></li>
                                                                            <li><a href="">Turban Edit</a></li>
                                                                            <li><a href="">Classic Acetates</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="">Vincent Chase</a></li>
                                                                            <li><a href="">Lenskart Air</a></li>
                                                                            <li><a href="">Lenskart STUDIO</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tabSun6" class="tab-content1">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="">John Jacobs</a></li>
                                                                            <li><a href="">Owndays</a></li>
                                                                            <li><a href="">New Balance</a></li>
                                                                            <li><a href="">Fossil</a></li>
                                                                            <li><a href="">Le Petit Lunetier</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="">New Arrivals</a></li>
                                                                            <li><a href="">Best Seller</a></li>
                                                                            <li><a href="">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Shape</h4>
                                                                            <li><a href="">Rectangle Frames</a></li>
                                                                            <li><a href="">Round Frames</a></li>
                                                                            <li><a href="">Square Frames</a></li>
                                                                            <li><a href="">Aviator Frames</a></li>
                                                                            <li><a href="">Cat-Eye Frames</a></li>
                                                                            <li><a href="">Rimless Frames</a></li>
                                                                            <li><a href="">Halfrim Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Tilak Varma for John Jacobs</a></li>
                                                                            <li><a href="">Zodiac</a></li>
                                                                            <li><a href="">Wildgear</a></li>
                                                                            <li><a href="">Timeless Metals</a></li>
                                                                            <li><a href="">Headspace</a></li>
                                                                            <li><a href="">Break the Frame</a></li>
                                                                            <li><a href="">Amore by Aditi Rao Hydari</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tabSun7" class="tab-content1">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Starting from Rs.600</a></li>
                                                                            <li><a href="">For your Kids</a></li>
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
                                                                            <li><a href="">New Arrivals</a></li>
                                                                            <li><a href="">Best Seller</a></li>
                                                                            <li><a href="">Lenskart BLU Lenses</a></li>
                                                                            <li><a href="">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            <li><a href="">Rectangle Frames</a></li>
                                                                            <li><a href="">Wayfarer Frames</a></li>
                                                                            <li><a href="">Round Frames</a></li>
                                                                            <li><a href="">Aviator Frames</a></li>
                                                                            <li><a href="">Cat-Eye Frames</a></li>
                                                                            <li><a href="">Rimless Frames</a></li>
                                                                            <li><a href="">Halfrim Frames</a></li>
                                                                            <li><a href="">Geometric Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Harry Potter</a></li>
                                                                            <li><a href="">Aao Twyst Karein</a></li>
                                                                            <li><a href="">Hustlr - As Seen on Shark Tank</a></li>
                                                                            <li><a href="">Switch - Magnetic Clips-On</a></li>
                                                                            <li><a href="">Patriot</a></li>
                                                                            <li><a href="">Hip Hop</a></li>
                                                                            <li><a href="">Turban Edit</a></li>
                                                                            <li><a href="">Classic Acetates</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="">Vincent Chase</a></li>
                                                                            <li><a href="">Lenskart Air</a></li>
                                                                            <li><a href="">Lenskart STUDIO</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tabSun9" class="tab-content2">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="">John Jacobs</a></li>
                                                                            <li><a href="">Owndays</a></li>
                                                                            <li><a href="">New Balance</a></li>
                                                                            <li><a href="">Fossil</a></li>
                                                                            <li><a href="">Le Petit Lunetier</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="">New Arrivals</a></li>
                                                                            <li><a href="">Best Seller</a></li>
                                                                            <li><a href="">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Shape</h4>
                                                                            <li><a href="">Rectangle Frames</a></li>
                                                                            <li><a href="">Round Frames</a></li>
                                                                            <li><a href="">Square Frames</a></li>
                                                                            <li><a href="">Aviator Frames</a></li>
                                                                            <li><a href="">Cat-Eye Frames</a></li>
                                                                            <li><a href="">Rimless Frames</a></li>
                                                                            <li><a href="">Halfrim Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Tilak Varma for John Jacobs</a></li>
                                                                            <li><a href="">Zodiac</a></li>
                                                                            <li><a href="">Wildgear</a></li>
                                                                            <li><a href="">Timeless Metals</a></li>
                                                                            <li><a href="">Headspace</a></li>
                                                                            <li><a href="">Break the Frame</a></li>
                                                                            <li><a href="">Amore by Aditi Rao Hydari</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="tabSun10" class="tab-content2">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="">Starting from Rs.600</a></li>
                                                                            <li><a href="">For your Kids</a></li>
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
                                <a href="#" class="desktop-item">Home Eye-test +</a>
                                <input type="checkbox" id="showMega">
                                <label for="showMega" class="mobile-item">Mega Menu</label>
                                <div class="mega-box">
                                    <div class="content d-block">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="home-eye-test-img">
                                                    <img src="{{asset('assets/img/bg/eye-test.png')}}" alt="">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="home-eye-test-txt1">
                                                    <div class="home-eye-test-txt">
                                                        <h3>Get your eyes checked at home</h3>
                                                        <p>A certified refractionist will visit you with latest eye testing machines & 100 trial frames</p>
                                                        <a href="" class="btn">Book appointment</a>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <a href="#" class="desktop-item">Store Locator +</a>
                                <input type="checkbox" id="showMega">
                                <label for="showMega" class="mobile-item">Mega Menu</label>
                                <div class="mega-box">
                                    <div class="content d-block">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="home-eye-test-sec">
                                                    <ul>
                                                        <li>
                                                            <a href="">
                                                                <img src="{{asset('assets/img/icon/Andra-Pradesh-W.png')}}" alt="">
                                                                <p>Delhi</p>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="">
                                                                <img src="{{asset('assets/img/icon/Gujarat-W-1.png')}}" alt="">
                                                                <p>Bangalore</p>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="">
                                                                <img src="{{asset('assets/img/icon/Maharashtra-W.png')}}" alt="">
                                                                <p>Mumbai</p>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="">
                                                                <img src="{{asset('assets/img/icon/Gujarat-W.png')}}" alt="">
                                                                <p>Ahmedabad</p>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="">
                                                                <img src="{{asset('assets/img/icon/Lakshadweep-W.png')}}" alt="">
                                                                <p>Chennai</p>
                                                            </a>
                                                        </li>
                                                        
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="home-eye-test-txt">
                                                    <h3>Over 1800+ Lenskart Store</h3>
                                                    <p>Experience eyewear in a whole new way: Visit your nearest store and treat yourself to 5000+ eyewear styles.</p>
                                                    <a href="" class="btn">Locate a Store</a>
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
<!-- end navbar -->