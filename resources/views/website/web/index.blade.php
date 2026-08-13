@extends('web.layout.master')
@section('content')
<!-- benner-section -->
<section class="benner-section">
    <!-- Carousel -->
    <div id="demo" class="carousel slide" data-bs-ride="carousel">

        <!-- Indicators/dots -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
        </div>
  
        <!-- The slideshow/carousel -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{asset('assets/img/bg/banner.png')}}" alt="Slide 1" class="d-block" style="width:100%">
            </div>
            <div class="carousel-item">
                <img src="{{asset('assets/img/bg/banner.png')}}" alt="Slide 2" class="d-block" style="width:100%">
            </div>
            <div class="carousel-item">
                <img src="{{asset('assets/img/bg/banner.png')}}" alt="Slide 3" class="d-block" style="width:100%">
            </div>
        </div>
  
        <!-- Left and right controls/icons -->
        <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>
<!-- end benner-section --> 

<!-- home-slider1 -->
<section class="home-slider1">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="home-slider1-img">
                    <div class="wrapper">
                        <div class="my-slider">
                            <div>
                                <a href="{{url('/products?category=eyeglasses')}}">
                                <div class="home-slider1-card">
                                    <div class="home-slider1-card1">
                                        <img src="{{asset('assets/img/icon/specs1.png')}}" alt="Eyeglasses">
                                    </div>
                                    <h6>Eyeglasses</h6>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/products?category=sunglasses')}}">
                                <div class="home-slider1-card">
                                    <div class="home-slider1-card1">
                                        <img src="{{asset('assets/img/icon/specs2.png')}}" alt="Sunglasses">
                                    </div>
                                    <h6>Sunglasses</h6>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/products?category=computer-glasses')}}">
                                <div class="home-slider1-card">
                                    <div class="home-slider1-card1">
                                        <img src="{{asset('assets/img/icon/specs3.png')}}" alt="Computer Glasses">
                                    </div>
                                    <h6>Computer Glasses</h6>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/products?category=kids-glasses')}}">
                                <div class="home-slider1-card">
                                    <div class="home-slider1-card1">
                                        <img src="{{asset('assets/img/icon/specs4.png')}}" alt="Kids Glasses">
                                    </div>
                                    <h6>Kids Glasses</h6>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/products?category=contact-lenses')}}">
                                <div class="home-slider1-card">
                                    <div class="home-slider1-card1">
                                        <img src="{{asset('assets/img/icon/specs5.png')}}" alt="Contact Lenses">
                                    </div>
                                    <h6>Contact Lenses</h6>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end home-slider1 -->

<!-- speckart-adds-banner -->
<section class="speckart-adds-banner">
    <img src="{{asset('assets/img/bg/speckart-ads.png')}}" alt="">
</section>
<!-- end speckart-adds-banner -->

<!-- sunglasses-section -->
<section class="sunglasses-section">
    <div class="container">
        <h3>Sunglasses for You</h3>
        <div class="row">
            <div class="col-lg-12">
                <div class="sunglasses-section-slider">
                    <div class="wrapper">
                        <div class="sunglasses-slider">
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Sunglasses1.png') }}" alt="Aviator Classic">
                                    </div>
                                    <h4>Ray-Ban Aviator Classic</h4>
                                    <p>Starting at Rs.4999</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Sunglasses2.png') }}" alt="Holbrook">
                                    </div>
                                    <h4>Oakley Holbrook</h4>
                                    <p>Starting at Rs.5499</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Sunglasses3.png') }}" alt="Linea Rossa">
                                    </div>
                                    <h4>Prada Linea Rossa</h4>
                                    <p>Starting at Rs.8999</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Sunglasses4.png') }}" alt="Square Frame">
                                    </div>
                                    <h4>Gucci Square Frame</h4>
                                    <p>Starting at Rs.12999</p>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end sunglasses-section -->

<!-- speckart-adds-banner -->
<section class="speckart-adds-banner">
    <img src="{{asset('assets/img/bg/speckart-adds1.png')}}" alt="">
</section>
<!-- end speckart-adds-banner -->

<!-- sunglasses-section -->
<section class="sunglasses-section">
    <div class="container">
        <h3>Eyeglasses for You</h3>
        <div class="row">
            <div class="col-lg-12">
                <div class="sunglasses-section-slider">
                    <div class="wrapper">
                        <div class="sunglasses-slider">
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Eyeglasses1.png') }}" alt="Round Frame">
                                    </div>
                                    <h4>Vincent Chase Round Frame</h4>
                                    <p>Starting at Rs.1999</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Eyeglasses2.png') }}" alt="Wayfarer Style">
                                    </div>
                                    <h4>John Jacobs Wayfarer Style</h4>
                                    <p>Starting at Rs.2499</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Eyeglasses3.png') }}" alt="Air Flex">
                                    </div>
                                    <h4>Lenskart Air Air Flex</h4>
                                    <p>Starting at Rs.1499</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Eyeglasses4.png') }}" alt="Rectangle Classic">
                                    </div>
                                    <h4>Fossil Rectangle Classic</h4>
                                    <p>Starting at Rs.3499</p>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end sunglasses-section -->

<!-- speckart-adds-banner -->
<section class="speckart-adds-banner">
    <img src="{{asset('assets/img/bg/speckart-adds3.png')}}" alt="">
</section>
<!-- end speckart-adds-banner -->

<!-- new-arrivals-section -->
<section class="new-arrivals-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2">
                <div class="new-arrivals-section-txt1">
                    <div class="new-arrivals-section-txt">
                        <h3>NEW</h3>
                        <h4>ARRIVALS</h4>
                        <a href="{{url('/products')}}">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-10">
                <div class="new-arrivals-section-slider">
                    <div class="wrapper">
                        <div class="new-arrivals-slider">
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="arrivals-slider-card">
                                    <div class="arrivals-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Sunglasses1.png') }}" alt="Clubmaster">
                                    </div>
                                    <h4>Ray-Ban Clubmaster</h4>
                                    <p>Starting at Rs.5999</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="arrivals-slider-card">
                                    <div class="arrivals-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Sunglasses2.png') }}" alt="Frogskins">
                                    </div>
                                    <h4>Oakley Frogskins</h4>
                                    <p>Starting at Rs.4499</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="arrivals-slider-card">
                                    <div class="arrivals-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Sunglasses3.png') }}" alt="Aviator Gradient">
                                    </div>
                                    <h4>Vincent Chase Aviator Gradient</h4>
                                    <p>Starting at Rs.3999</p>
                                </div>
                                </a>
                            </div>
                            <div>
                                <a href="{{url('/product/sample-product')}}">
                                <div class="arrivals-slider-card">
                                    <div class="arrivals-slider-card-img">
                                        <img src="{{ asset('assets/img/bg/Sunglasses4.png') }}" alt="Half Rim">
                                    </div>
                                    <h4>John Jacobs Half Rim</h4>
                                    <p>Starting at Rs.2999</p>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end new-arrivals-section -->

<!-- shop-by-brand-section -->
<section class="shop-by-brand-section">
    <h3>SHOP BY <span>BRANDS</span></h3>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper">
                    <div class="shop-by-brand-slider">
                        <div>
                            <a href="{{url('/products?brand=ray-ban')}}">
                            <div class="shop-by-brand-slider-card">
                                <div class="shop-by-brand-slider-card-img">
                                    <img src="{{asset('assets/img/bg/brands1.png')}}" alt="Ray-Ban">
                                </div>
                                <div class="shop-by-brand-slider-card-img-sm">
                                    <img src="{{asset('assets/img/bg/brand-sm1.png')}}" alt="Ray-Ban">
                                </div>
                            </div>
                            </a>
                        </div>
                        <div>
                            <a href="{{url('/products?brand=oakley')}}">
                            <div class="shop-by-brand-slider-card">
                                <div class="shop-by-brand-slider-card-img">
                                    <img src="{{asset('assets/img/bg/brands2.png')}}" alt="Oakley">
                                </div>
                                <div class="shop-by-brand-slider-card-img-sm">
                                    <img src="{{asset('assets/img/bg/brand-sm2.png')}}" alt="Oakley">
                                </div>
                            </div>
                            </a>
                        </div>
                        <div>
                            <a href="{{url('/products?brand=vincent-chase')}}">
                            <div class="shop-by-brand-slider-card">
                                <div class="shop-by-brand-slider-card-img">
                                    <img src="{{asset('assets/img/bg/brands3.png')}}" alt="Vincent Chase">
                                </div>
                                <div class="shop-by-brand-slider-card-img-sm">
                                    <img src="{{asset('assets/img/bg/brand-sm3.png')}}" alt="Vincent Chase">
                                </div>
                            </div>
                            </a>
                        </div>
                        <div>
                            <a href="{{url('/products?brand=john-jacobs')}}">
                            <div class="shop-by-brand-slider-card">
                                <div class="shop-by-brand-slider-card-img">
                                    <img src="{{asset('assets/img/bg/brands4.png')}}" alt="John Jacobs">
                                </div>
                                <div class="shop-by-brand-slider-card-img-sm">
                                    <img src="{{asset('assets/img/bg/brand-sm4.png')}}" alt="John Jacobs">
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end shop-by-brand-section -->

<!-- own-creation -->
<div class="own-creation">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5" style="padding-left: 0;">
                <div class="own-creation-left">
                    <h3>Our</h3>
                    <h3>Own Creation</h3>
                    <p>Designed in Speckarts.com</p>
                    <!-- Add Arrows -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="own-creation-section">
                    <div class="bk-slider">
                        <div class="swiperrr">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <a href="{{url('/product/sample-product')}}">
                                    <div class="own-creation-slide">
                                        <div class="own-creation-slide-img">
                                            <img src="{{ asset('assets/img/bg/Creation1.png') }}" alt="Signature Frame">
                                        </div>
                                        <div class="own-creation-slide-txt">
                                            <h4>Speckarts Signature Frame</h4>
                                            <h5>Starting at Rs.2999</h5>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="{{url('/product/sample-product')}}">
                                    <div class="own-creation-slide">
                                        <div class="own-creation-slide-img">
                                            <img src="{{ asset('assets/img/bg/Creation2.png') }}" alt="Classic Round">
                                        </div>
                                        <div class="own-creation-slide-txt">
                                            <h4>Speckarts Classic Round</h4>
                                            <h5>Starting at Rs.3499</h5>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="{{url('/product/sample-product')}}">
                                    <div class="own-creation-slide">
                                        <div class="own-creation-slide-img">
                                            <img src="{{ asset('assets/img/bg/Creation1.png') }}" alt="Bold Square">
                                        </div>
                                        <div class="own-creation-slide-txt">
                                            <h4>Speckarts Bold Square</h4>
                                            <h5>Starting at Rs.1999</h5>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="{{url('/product/sample-product')}}">
                                    <div class="own-creation-slide">
                                        <div class="own-creation-slide-img">
                                            <img src="{{ asset('assets/img/bg/Creation2.png') }}" alt="Minimalist Wire">
                                        </div>
                                        <div class="own-creation-slide-txt">
                                            <h4>Speckarts Minimalist Wire</h4>
                                            <h5>Starting at Rs.2499</h5>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Add Arrows -->
                        <!-- <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div> -->
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- end own-creation -->

<!-- testmonial-section -->
    <section class="testmonial-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="testmonial-section-slider">
                        <h3>VOICE OF THE <span>PEOPLES</span></h3>
                        <div class="swiper-container">
                            <div class="swiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide swiper-slide--one">
                                        <div class="author">
                                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=870&q=80" alt="Jane Eyre">
                                        </div>
                                        <div class="slide-content">
                                            <p>" "The Speckart Eyewear app made it so easy to find the perfect pair of glasses! The virtual try-on feature was incredibly accurate, and the checkout process was smooth. Highly recommend!""</p>
                                            <h3>-- Jane Eyre</h3>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="author">
                                            <img src="https://images.pexels.com/photos/874158/pexels-photo-874158.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="J. R. R. Tolkien">
                                        </div>
                                        <div class="slide-content">
                                            <p>epic high-fantasy novel by the English author and scholar J. R. R. Tolkien</p>
                                            <h3>-- The Lord</h3>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="author">
                                            <img src="https://images.pexels.com/photos/262391/pexels-photo-262391.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Erich Maria Remarque">
                                        </div>
                                        <div class="slide-content">
                                            <p>The book describes the German soldiers' extreme physical and mental trauma during the war</p>
                                            <h3>-- All Quiet</h3>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="author">
                                            <img src="https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?auto=compress&cs=tinysrgb&w=600" alt="William Shakespeare">
                                        </div>
                                        <div class="slide-content">
                                            <p>a tragedy between two youths from feuding families</p>
                                            <h3>-- Romeo and Juliet</h3>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="author">
                                            <img src="https://images.pexels.com/photos/775358/pexels-photo-775358.jpeg?auto=compress&cs=tinysrgb&w=600" alt="John Steinbeck">
                                        </div>
                                        <div class="slide-content">
                                            <p>a novell</p>
                                            <h3>-- Mice and Men</h3>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="author">
                                            <img src="https://images.unsplash.com/photo-1553514029-1318c9127859?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=464&q=80" alt="J. K. Rowling">
                                        </div>
                                        <div class="slide-content">
                                            <p>The novels chronicle the lives of a young wizard, Harry Potter, and his friends Hermione Granger and Ron Weasley</p>
                                            <h3>Harry Potter</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- If we need navigation buttons -->
                            <div class="swiper-button-prev">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                                </svg>
                            </div>
                            <div class="swiper-button-next">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- end testmonial-section -->

<!-- shop-gender-section -->
<section class="shop-gender-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="shop-gender-section-left1">
                    <div class="shop-gender-section-left">
                        <h4>SHOP BY</h4>
                        <h3>GENDER</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    @php
                        $genderItems = $genders ?? [
                            ['name' => 'Men', 'slug' => 'Men', 'icon' => asset('assets/img/icon/specs-men.png'), 'url' => route('products', ['gender' => 'Men'])],
                            ['name' => 'Women', 'slug' => 'Women', 'icon' => asset('assets/img/icon/specs-women.png'), 'url' => route('products', ['gender' => 'Women'])],
                            ['name' => 'Kids', 'slug' => 'Kids', 'icon' => asset('assets/img/icon/specs-kid.png'), 'url' => route('products', ['gender' => 'Kids'])],
                        ];
                    @endphp
                    @foreach($genderItems as $gender)
                    <div class="col-lg-4">
                        <a href="{{ $gender['url'] ?? route('products', ['gender' => $gender['slug'] ?? $gender['name']]) }}">
                            <div class="shop-gender-section-card">
                                <img src="{{ $gender['icon'] }}" alt="{{ $gender['name'] }}">
                                <p>{{ $gender['name'] }}</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end shop-gender-section -->
@endsection