<!-- navbar -->

<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="top-menu">
                    <a href="#">
                        <div class="logo">
                            <img src="{{ asset('website/assets/img/logo/Specskart-logo-png.png') }}" alt="">
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
                                <a href="#">
                                    <p><img src="{{ asset('website/assets/img/icon/Track-Order.png') }}" alt=""></p>
                                    <p>Track Order</p>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <p><img src="{{ asset('website/assets/img/icon/Wishlist.png') }}" alt=""></p>
                                    <p>Wishlist</p>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <p><img src="{{ asset('website/assets/img/icon/My-Cart.png') }}" alt=""></p>
                                    <p>My Cart</p>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a href="#">
                                    <p><img src="{{ asset('website/assets/img/icon/More.png') }}" alt=""></p>
                                    <p>More</p>
                                </a>
                                <ul class="dropdown-nav">
                                    <li><a href="#">Lawn Care</a></li>
                                    <li><a href="#">Walling &amp; Fencing</a></li>
                                    <li><a href="#">Landscape design</a></li>
                                    <li><a href="#">Grounds Maintenance</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="#">
                                    <p><img src="{{ asset('website/assets/img/icon/Signup.png') }}" alt=""></p>
                                    <p>Sign up/Sign In</p>
                                </a>
                            </li>
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
                        <input type="radio" name="slider" id="menu-btn">
                        <input type="radio" name="slider" id="close-btn">

                        <ul class="nav-links">
                            <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>

                            <!-- Eyeglasses -->
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
                                                        <img src="{{ asset('website/assets/img/icon/specs-men.png') }}" alt="specs-men">
                                                        Men
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tab2">
                                                        <img src="{{ asset('website/assets/img/icon/specs-women.png') }}" alt="specs-women">
                                                        Women
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tab3">
                                                        <img src="{{ asset('website/assets/img/icon/specs-kid.png') }}" alt="specs-kid">
                                                        Kids
                                                    </a>
                                                </li>
                                            </ul>

                                            <div id="tabs-content">
                                                <!-- MEN -->
                                                <div id="tab1" class="tab-content">
                                                    <div class="tabs1">
                                                        <ul id="tabs-nav1">
                                                            <li>
                                                                <a href="#tab5">
                                                                    <p>CLASSIC EYEGLASSES</p>
                                                                    <p>Starting from ₹2000</p>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#tab6">
                                                                    <p>PREMIUM EYEGLASSES</p>
                                                                    <p>Starting from ₹4000</p>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#tab7">
                                                                    <p>SCREEN EYEGLASSES</p>
                                                                    <p>Starting from ₹600</p>
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <div id="tabs-content1">
                                                            <div id="tab5" class="tab-content1">
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
                                                                            <li><a href="#">Rectangle</a></li>
                                                                            <li><a href="#">Round</a></li>
                                                                            <li><a href="#">Square</a></li>
                                                                            <li><a href="#">Aviator</a></li>
                                                                            <li><a href="#">Wayfarer</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="#">Classic</a></li>
                                                                            <li><a href="#">Premium</a></li>
                                                                            <li><a href="#">Trending Collection</a></li>
                                                                            <li><a href="#">New Collection</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="#">John Jacobs</a></li>
                                                                            <li><a href="#">Owndays</a></li>
                                                                            <li><a href="#">Fossil</a></li>
                                                                            <li><a href="#">Vincent Chase</a></li>
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
                                                                            <li><a href="#">Le Petit Lunetier</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="#">New Arrivals</a></li>
                                                                            <li><a href="#">Best Seller</a></li>
                                                                            <li><a href="#">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Shape</h4>
                                                                            <li><a href="#">Rectangle Frames</a></li>
                                                                            <li><a href="#">Round Frames</a></li>
                                                                            <li><a href="#">Square Frames</a></li>
                                                                            <li><a href="#">Aviator Frames</a></li>
                                                                            <li><a href="#">Cat-Eye Frames</a></li>
                                                                            <li><a href="#">Rimless Frames</a></li>
                                                                            <li><a href="#">Halfrim Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="#">Zodiac</a></li>
                                                                            <li><a href="#">Wildgear</a></li>
                                                                            <li><a href="#">Timeless Metals</a></li>
                                                                            <li><a href="#">Headspace</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="tab7" class="tab-content1">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="#">Starting from ₹600</a></li>
                                                                            <li><a href="#">For your Kids</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- WOMEN -->
                                                <div id="tab2" class="tab-content">
                                                    <div class="tabs2">
                                                        <ul id="tabs-nav2">
                                                            <li>
                                                                <a href="#tab8">
                                                                    <p>CLASSIC EYEGLASSES</p>
                                                                    <p>Starting from ₹2000</p>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#tab9">
                                                                    <p>PREMIUM EYEGLASSES</p>
                                                                    <p>Starting from ₹4000</p>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#tab10">
                                                                    <p>SCREEN EYEGLASSES</p>
                                                                    <p>Starting from ₹600</p>
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <div id="tabs-content2">
                                                            <div id="tab8" class="tab-content2">
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
                                                                            <li><a href="#">Rectangle</a></li>
                                                                            <li><a href="#">Round</a></li>
                                                                            <li><a href="#">Square</a></li>
                                                                            <li><a href="#">Cat Eye</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="#">Classic</a></li>
                                                                            <li><a href="#">Premium</a></li>
                                                                            <li><a href="#">New Collection</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="#">John Jacobs</a></li>
                                                                            <li><a href="#">Owndays</a></li>
                                                                            <li><a href="#">Fossil</a></li>
                                                                            <li><a href="#">Vincent Chase</a></li>
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
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="#">New Arrivals</a></li>
                                                                            <li><a href="#">Best Seller</a></li>
                                                                            <li><a href="#">Progressive Eyeglasses</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Shape</h4>
                                                                            <li><a href="#">Rectangle Frames</a></li>
                                                                            <li><a href="#">Round Frames</a></li>
                                                                            <li><a href="#">Square Frames</a></li>
                                                                            <li><a href="#">Aviator Frames</a></li>
                                                                            <li><a href="#">Cat-Eye Frames</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="#">Zodiac</a></li>
                                                                            <li><a href="#">Wildgear</a></li>
                                                                            <li><a href="#">Headspace</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="tab10" class="tab-content2">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="#">Starting from ₹600</a></li>
                                                                            <li><a href="#">For your Kids</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- KIDS -->
                                                <div id="tab3" class="tab-content">
                                                    <div class="tabs3">
                                                        <ul id="tabs-nav3">
                                                            <li>
                                                                <a href="#tab11">
                                                                    <p>CLASSIC EYEGLASSES</p>
                                                                    <p>Starting from ₹1000</p>
                                                                </a>
                                                            </li>
                                                        </ul>

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
                                                                            <li><a href="#">Rectangle</a></li>
                                                                            <li><a href="#">Round</a></li>
                                                                            <li><a href="#">Square</a></li>
                                                                            <li><a href="#">Aviator</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            <li><a href="#">Kids Classic</a></li>
                                                                            <li><a href="#">Kids Premium</a></li>
                                                                            <li><a href="#">Trending Collection</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            <li><a href="#">John Jacobs</a></li>
                                                                            <li><a href="#">Owndays</a></li>
                                                                            <li><a href="#">Fossil</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- end tabs-content -->
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Contact Lenses -->
                            <li>
                                <a href="#" class="desktop-item">Contact Lenses +</a>
                                <input type="checkbox" id="showMegaContact">
                                <label for="showMegaContact" class="mobile-item">Mega Menu</label>
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
                                            <header>Explore By Disposability</header>
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
                                                <li><a href="#">Spherical - (CYL 0.5)</a></li>
                                                <li><a href="#">Spherical + (CYL 0.5)</a></li>
                                                <li><a href="#">Cylindrical Power (>0.75)</a></li>
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

                            <!-- Home Eye-test -->
                            <li>
                                <a href="#" class="desktop-item">Home Eye-test +</a>
                                <input type="checkbox" id="showMegaEye">
                                <label for="showMegaEye" class="mobile-item">Mega Menu</label>
                                <div class="mega-box">
                                    <div class="content d-block">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="home-eye-test-img">
                                                    <img src="{{ asset('website/assets/img/bg/eye-test.png') }}" alt="">
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

                            <!-- Store Locator -->
                            <li>
                                <a href="#" class="desktop-item">Store Locator +</a>
                                <input type="checkbox" id="showMegaStore">
                                <label for="showMegaStore" class="mobile-item">Mega Menu</label>
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
                                                    <h3>Over 1800+ Lenskart Store</h3>
                                                    <p>Experience eyewear in a whole new way: Visit your nearest store and treat yourself to 5000+ eyewear styles.</p>
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

<!-- end navbar -->