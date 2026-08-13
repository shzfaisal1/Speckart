@extends('website.layout.master')
@section('content')

<style>
    .shop-gender-section {
        background: #f8f9fa;
    }

    .shop-gender-section-left h5 {
        color: #888;
        letter-spacing: 2px;
    }

    .shop-gender-section-left h2 {
        font-size: 40px;
        color: #222;
    }

    .gender-card-link {
        text-decoration: none;
    }

    .shop-gender-section-card {
        background: #fff;
        border-radius: 20px;
        padding: 35px 20px 30px;
        text-align: center;
        height: 100%;
        transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
        box-shadow: 0 5px 20px rgba(0,0,0,.06);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.04);
    }

    .shop-gender-section-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 14px 32px rgba(6, 165, 170, 0.18);
        border-color: rgba(6, 165, 170, 0.25);
    }

    .gender-card-count-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        background: linear-gradient(135deg, #06a5aa, #00797d);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 6px rgba(6, 165, 170, 0.3);
    }

    .gender-card-img-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 130px;
        margin-bottom: 12px;
    }

    .shop-gender-section-card img {
        max-width: 120px;
        max-height: 110px;
        width: auto;
        height: auto;
        transition: transform .35s ease;
    }

    .shop-gender-section-card:hover img {
        transform: scale(1.1);
    }

    .shop-gender-section-card p {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
        color: #222;
        transition: color 0.3s ease;
    }

    .shop-gender-section-card:hover p {
        color: #06a5aa;
    }

    /* Tablet */
    @media (max-width: 991px) {
        .shop-gender-section-left h2 {
            font-size: 32px;
        }

        .shop-gender-section-card {
            padding: 25px 15px;
        }

        .shop-gender-section-card img {
            max-width: 100px;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        .shop-gender-section {
            padding: 40px 0;
        }

        .shop-gender-section-left {
            text-align: center;
            margin-bottom: 20px;
        }

        .shop-gender-section-left h2 {
            font-size: 28px;
        }

        .shop-gender-section-card {
            padding: 20px;
        }

        .shop-gender-section-card img {
            max-width: 90px;
        }

        .shop-gender-section-card p {
            font-size: 18px;
        }
    }
</style>
<!-- benner-section -->
<section class="benner-section">
    <!-- Carousel -->
  <div id="demo" class="carousel slide" data-bs-ride="carousel">  
        @if(!empty($main_slider) && count($main_slider) > 0)
        <!-- Indicators/dots -->
        <div class="carousel-indicators">
            @foreach($main_slider as $index => $slide)
                <button type="button" data-bs-target="#demo" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>
  
        <!-- The slideshow/carousel -->
        <div class="carousel-inner">
            @foreach($main_slider as $index => $slide)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <a href="{{ $slide->link_url }}">
                    <img src="{{ asset($slide->image) }}" alt="{{ $slide->title ?: 'Slide ' . ($index + 1) }}" class="d-block" style="width:100%">
                </a>
            </div>
            @endforeach
        </div>
        @else
        <!-- Fallback: static slides -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{asset('website/assets/img/bg/banner.png')}}" alt="Slide 1" class="d-block" style="width:100%">
            </div>
            <div class="carousel-item">
                <img src="{{asset('website/assets/img/bg/banner.png')}}" alt="Slide 2" class="d-block" style="width:100%">
            </div>
            <div class="carousel-item">
                <img src="{{asset('website/assets/img/bg/banner.png')}}" alt="Slide 3" class="d-block" style="width:100%">
            </div>
        </div>
        @endif
  
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
                          @if(isset($categories))
                            @foreach($categories as $category)
                            <div>
                                <a href="{{url('/products?category=' . $category->slug)}}">
                                <div class="home-slider1-card">
                                    <div class="home-slider1-card1">
                                        <img src="{{asset($category->image)}}" alt="Eyeglasses">
                                    </div>
                                    <h6>{{$category->name}}</h6>
                                </div>
                                </a>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end home-slider1 -->

<!-- speckart-adds-banner (Promo Row 1) -->
<section class="speckart-adds-banner">
    @if(!empty($promo_1) && count($promo_1) > 0)
        @php $promoBanner1 = $promo_1[0]; @endphp
        <a href="{{ $promoBanner1->link_url }}">
            <img src="{{ asset($promoBanner1->image) }}" alt="{{ $promoBanner1->title ?: 'Promo Banner' }}">
        </a>
    @else
        <img src="{{asset('website/assets/img/bg/speckart-ads.png')}}" alt="">
    @endif
</section>
<!-- end speckart-adds-banner -->

<!-- bestSeller-section -->
<section class="sunglasses-section">
    <div class="container">
        <h3>Best Seller</h3>
        <div class="row">
            <div class="col-lg-12">
                <div class="sunglasses-section-slider">
                    <div class="wrapper">
                        <div class="sunglasses-slider">
                            @foreach($best_sellers as $p)
                            <div>
                                <a href="{{ $p->detail_url }}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}">
                                    </div>
                                    <h4>{{ $p->product_name }}</h4>
                                    <p>Starting at Rs.{{ number_format($p->Retail_Price, 0) }}</p>
                                </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end best-seller -->


<!-- sunglasses-section -->
<section class="sunglasses-section">
    <div class="container">
        <h3>Sunglasses for You</h3>
        <div class="row">
            <div class="col-lg-12">
                <div class="sunglasses-section-slider">
                    <div class="wrapper">
                        <div class="sunglasses-slider">
                            @foreach($sunglasses as $p)
                            <div>
                                <a href="{{ $p->detail_url }}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}">
                                    </div>
                                    <h4>{{ $p->product_name }}</h4>
                                    <p>Starting at Rs.{{ number_format($p->Retail_Price, 0) }}</p>
                                </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end sunglasses-section -->


<!-- speckart-adds-banner (Promo Row 2) -->
<section class="speckart-adds-banner">
    @if(!empty($promo_2) && count($promo_2) > 0)
        @php $promoBanner2 = $promo_2[0]; @endphp
        <a href="{{ $promoBanner2->link_url }}">
            <img src="{{ asset($promoBanner2->image) }}" alt="{{ $promoBanner2->title ?: 'Promo Banner' }}">
        </a>
    @else
        <img src="{{asset('website/assets/img/bg/speckart-ads1.png')}}" alt="">
    @endif
</section>
<!-- end speckart-adds-banner -->


<!-- eyeglasses-section -->
<section class="sunglasses-section">
    <div class="container">
        <h3>Eyeglasses for You</h3>
        <div class="row">
            <div class="col-lg-12">
                <div class="sunglasses-section-slider">
                    <div class="wrapper">
                        <div class="sunglasses-slider">
                            @foreach($eyeglasses as $p)
                            <div>
                                <a href="{{ $p->detail_url }}">
                                <div class="sunglasses-slider-card">
                                    <div class="sunglasses-slider-card-img">
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}">
                                    </div>
                                    <h4>{{ $p->product_name }}</h4>
                                    <p>Starting at Rs.{{ number_format($p->Retail_Price, 0) }}</p>
                                </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end eyeglasses-section -->


<!-- speckart-adds-banner (Spotlight) -->
<section class="speckart-adds-banner">
    @if(!empty($spotlight) && count($spotlight) > 0)
        @php $spotBanner = $spotlight[0]; @endphp
        <a href="{{ $spotBanner->link_url }}">
            <img src="{{ asset($spotBanner->image) }}" alt="{{ $spotBanner->title ?: 'Spotlight Banner' }}">
        </a>
    @else
        <img src="{{asset('website/assets/img/bg/speckart-adds3.png')}}" alt="">
    @endif
</section>
<!-- end speckart-adds-banner -->

<!-- new-arrivals-section -->
<section class="new-arrivals-section">
    <div class="container-fluid">
        <div class="row">
            <!--<div class="col-lg-2">-->
            <!--    <div class="new-arrivals-section-txt1">-->
            <!--        <div class="new-arrivals-section-txt">-->
            <!--            <h3>NEW</h3>-->
            <!--            <h4>ARRIVALS</h4>-->
            <!--            <a href="{{url('/products')}}">View All</a>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <div class="col-lg-12">
                <div class="new-arrivals-section-txt1 justify-content-between px-2">
                    <div class="new-arrivals-section-txt">
                        <div class="d-flex">
                            <h3>NEW&nbsp;<h4 class="mb-0">ARRIVALS</h4></h3>
                        </div>
                    </div>
                    <div>
                        <a href="" class="view-all-btn">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-12"> 
                <div class="new-arrivals-section-slider">
                    <div class="wrapper">
                        <div class="new-arrivals-slider">
                            @foreach($new_arrivals as $p)
                            <div>
                                <a href="{{ $p->detail_url }}">
                                <div class="arrivals-slider-card">
                                    <div class="arrivals-slider-card-img">
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}">
                                    </div>
                                    <h4>{{ $p->product_name }}</h4>
                                    <p>Starting at Rs.{{ number_format($p->Retail_Price, 0) }}</p>
                                </div>
                                </a>
                            </div>
                            @endforeach
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
                                    <img src="{{asset('website/assets/img/bg/brands1.png')}}" alt="Ray-Ban">
                                </div>
                                <div class="shop-by-brand-slider-card-img-sm">
                                    <img src="{{asset('website/assets/img/bg/brand-sm1.png')}}" alt="Ray-Ban">
                                </div>
                            </div>
                            </a>
                        </div>
                        <div>
                            <a href="{{url('/products?brand=oakley')}}">
                            <div class="shop-by-brand-slider-card">
                                <div class="shop-by-brand-slider-card-img">
                                    <img src="{{asset('website/assets/img/bg/brands2.png')}}" alt="Oakley">
                                </div>
                                <div class="shop-by-brand-slider-card-img-sm">
                                    <img src="{{asset('website/assets/img/bg/brand-sm2.png')}}" alt="Oakley">
                                </div>
                            </div>
                            </a>
                        </div>
                        <div>
                            <a href="{{url('/products?brand=vincent-chase')}}">
                            <div class="shop-by-brand-slider-card">
                                <div class="shop-by-brand-slider-card-img">
                                    <img src="{{asset('website/assets/img/bg/brands3.png')}}" alt="Vincent Chase">
                                </div>
                                <div class="shop-by-brand-slider-card-img-sm">
                                    <img src="{{asset('website/assets/img/bg/brand-sm3.png')}}" alt="Vincent Chase">
                                </div>
                            </div>
                            </a>
                        </div>
                        <div>
                            <a href="{{url('/products?brand=john-jacobs')}}">
                            <div class="shop-by-brand-slider-card">
                                <div class="shop-by-brand-slider-card-img">
                                    <img src="{{asset('website/assets/img/bg/brands4.png')}}" alt="John Jacobs">
                                </div>
                                <div class="shop-by-brand-slider-card-img-sm">
                                    <img src="{{asset('website/assets/img/bg/brand-sm4.png')}}" alt="John Jacobs">
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
<div class="own-creation overflow-hidden">
    <div class="container ps-0">
        <div class="row">
            <div class="col-lg-4" style="padding-left: 0;">
                <div class="own-creation-left">
                    <h3>Trending Products</h3>
                    <p>Trending in Speckarts.com</p>
                    <!-- Add Arrows -->
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div id="swiper1-prev"  class="swiper-button-prev"></div>
                             <div id="swiper1-next"  class="swiper-button-next"></div>
                        </div>
                        {{-- <div>
                            <a href="" class="view-all-btn">View All</a>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="new-arrivals-section-slider">
                    <div class="wrapper">
                        <div class="trending-slider">
                            @foreach($trending_products as $p)
                            <div>
                                <a href="{{ $p->detail_url }}">
                                <div class="arrivals-slider-card">
                                    <div class="arrivals-slider-card-img">
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}">
                                    </div>
                                    <h4>{{ $p->product_name }}</h4>
                                    <p>Starting at Rs.{{ number_format($p->Retail_Price, 0) }}</p>
                                </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
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
                            <div id="swiper2-prev" class="swiper-button-prev">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                                </svg>
                            </div>
                            <div id="swiper2-next" class="swiper-button-next">
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
<section class="shop-gender-section py-5 shop-by-brand-section">
    <h3>SHOP BY <span>GENDER</span></h3>
    <div class="container">
        <div class="row align-items-center">

            <!-- Dynamic Cards -->
            <div class="col-lg-12">
                <div class="row g-4 justify-content-center">

                    @php
                        $genderItems = $genders ?? [
                            ['name' => 'Men', 'slug' => 'Men', 'icon' => asset('website/assets/img/icon/specs-men.png'), 'url' => route('products', ['gender' => 'Men']), 'count' => 0],
                            ['name' => 'Women', 'slug' => 'Women', 'icon' => asset('website/assets/img/icon/specs-women.png'), 'url' => route('products', ['gender' => 'Women']), 'count' => 0],
                            ['name' => 'Kids', 'slug' => 'Kids', 'icon' => asset('website/assets/img/icon/specs-kid.png'), 'url' => route('products', ['gender' => 'Kids']), 'count' => 0],
                        ];
                        $itemCount = count($genderItems);
                        $colClass = $itemCount <= 3 ? 'col-lg-4 col-md-4 col-12' : ($itemCount == 4 ? 'col-lg-3 col-md-6 col-12' : 'col-lg-4 col-md-6 col-12');
                    @endphp

                    @foreach($genderItems as $gender)
                    <div class="{{ $colClass }}">
                        <a href="{{ $gender['url'] ?? route('products', ['gender' => $gender['slug'] ?? $gender['name']]) }}" class="gender-card-link">
                            <div class="shop-gender-section-card">
                                @if(!empty($gender['count']) && $gender['count'] > 0)
                                    <span class="gender-card-count-badge">{{ $gender['count'] }}+ Frames</span>
                                @endif
                                <div class="gender-card-img-wrapper">
                                    <img src="{{ $gender['icon'] }}" alt="{{ $gender['name'] }}" loading="lazy">
                                </div>
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