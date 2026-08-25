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
        padding: 30px 20px;
        text-align: center;
        height: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,.08);

        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .shop-gender-section-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,.15);
    }

    .shop-gender-section-card img {
        max-width: 120px;
        width: 100%;
        height: auto;
        margin-bottom: 15px;
        transition: transform .3s ease;
    }

    .shop-gender-section-card:hover img {
        transform: scale(1.08);
    }

    .shop-gender-section-card p {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
        color: #333;
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
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Sunglasses1.png') }}';">
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
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Sunglasses1.png') }}';">
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
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Eyeglasses7.png') }}';">
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
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Sunglasses1.png') }}';">
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

<!-- ══════════════════════════════════════════════════════
     SHOP BY BRANDS (MODERN LUXURY BRAND CARDS - NO CELEBRITY)
══════════════════════════════════════════════════════ -->
<style>
.shop-by-brand-section-modern { 
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    padding: 55px 0 20px 0;
    position: relative;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}

.brand-section-header {
    text-align: center;
    margin-bottom: 40px;
}

.brand-eyebrow-badge {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #008f8f;
    background: #e6f8f8;
    border: 1px solid rgba(0, 180, 180, 0.3);
    padding: 5px 16px;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
}

.brand-main-title {
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.5px;
    margin-bottom: 8px;
}

.brand-main-title span {
    color: #00b4b4;
}

.brand-sub-title {
    font-size: 14.5px;
    color: #64748b;
    max-width: 580px;
    margin: 0 auto;
    line-height: 1.5;
}

/* Brand Card */
.brand-slider-wrapper {
    position: relative;
    padding: 0 6px;
}

.brand-card-modern {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 22px;
    padding: 28px 22px;
    margin: 12px;
    text-align: center;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    min-height: 330px;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
    position: relative;
    overflow: hidden;
}

.brand-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #02045c, #00b4b4);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.brand-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 42px rgba(0, 180, 180, 0.16);
    border-color: #00b4b4;
    text-decoration: none;
}

.brand-card-modern:hover::before {
    opacity: 1;
}

/* Large Brand Logo Box */
.brand-logo-stage {
    width: 100%;
    height: 125px;
    background: #f8fafc;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    /* padding: 14px 20px; */
    margin-bottom: 20px;
    border: 1.5px solid #eef2f6;
    transition: all 0.3s ease;
}

.brand-card-modern:hover .brand-logo-stage {
    background: #f0fdfd;
    border-color: rgba(0, 180, 180, 0.3);
    box-shadow: inset 0 0 0 1px rgba(0, 180, 180, 0.1);
}

.brand-logo-stage img {
    max-height: 90px;
    max-width: 90%;
    width: auto;
    object-fit: contain;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.04));
}

.brand-card-modern:hover .brand-logo-stage img {
    transform: scale(1.1);
}

.brand-monogram-badge {
    width: 76px;
    height: 76px;
    border-radius: 20px;
    background: linear-gradient(135deg, #02045c 0%, #00b4b4 100%);
    color: #ffffff;
    font-size: 26px;
    font-weight: 900;
    display: flex;
    align-items: center;
    justify-content: center;
    letter-spacing: 0.5px;
    box-shadow: 0 6px 18px rgba(2, 4, 92, 0.2);
    transition: transform 0.35s ease;
}

.brand-card-modern:hover .brand-monogram-badge {
    transform: scale(1.1);
}

/* Brand Typography */
.brand-title-text {
    font-size: 21px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.25;
    margin: 0 0 6px 0;
    transition: color 0.2s ease;
}

.brand-card-modern:hover .brand-title-text {
    color: #008f8f;
}

.brand-tagline-text {
    font-size: 13.5px;
    color: #64748b;
    font-weight: 500;
    margin: 0 0 16px 0;
    line-height: 1.4;
    min-height: 38px;
}

/* Brand Discount Tag */
.brand-discount-badge {
    font-size: 12px;
    font-weight: 700;
    color: #008f8f;
    background: #e6f8f8;
    border: 1.5px solid rgba(0, 180, 180, 0.3);
    padding: 5px 14px;
    border-radius: 50px;
    margin-bottom: 14px;
    display: inline-block;
}

/* Explore CTA Button */
.brand-explore-btn {
    font-size: 13px;
    font-weight: 700;
    color: #02045c;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    padding: 8px 22px;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s ease;
}

.brand-card-modern:hover .brand-explore-btn {
    background: #02045c;
    border-color: #02045c;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(2, 4, 92, 0.25);
    gap: 12px;
}
</style>

<section class="shop-by-brand-section-modern">
    <div class="container">
        <div class="brand-section-header">
            {{-- <span class="brand-eyebrow-badge"><i class="bi bi-stars"></i> Iconic Eyewear Houses</span> --}}
            <h3 class="brand-main-title">SHOP BY <span>BRANDS</span></h3>
            <p class="brand-sub-title">Explore authentic craftsmanship, precision optics, and timeless silhouettes from leading eyewear labels.</p>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="brand-slider-wrapper">
                    <div class="shop-by-brand-slider">
                        @if(isset($brands) && $brands->isNotEmpty())
                            @foreach($brands as $brand)
                            <div>
                                <a href="{{ $brand->url }}" class="brand-card-modern">
                                    <div class="w-100">
                                        <div class="brand-logo-stage">
                                            @if(!empty($brand->logo_img))
                                                <img src="{{ $brand->logo_img }}" alt="{{ $brand->name }}" loading="lazy">
                                            @else
                                                <div class="brand-monogram-badge" style="background: linear-gradient(135deg, #02045c 0%, {{ $brand->accent ?? '#00b4b4' }} 100%);">
                                                    {{ $brand->initials ?? substr($brand->name, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>

                                        <h4 class="brand-title-text">{{ $brand->name }}</h4>
                                        {{-- <p class="brand-tagline-text">{{ $brand->tagline }}</p> --}}
                                    </div>

                                    <div>
                                        <div>
                                            <span class="brand-explore-btn">
                                                Explore Collection <i class="bi bi-arrow-right"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        @else
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
                                        <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/bg/Sunglasses1.png') }}';">
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

            <!-- Left Title -->
            {{-- <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                <div class="shop-gender-section-left text-center text-lg-start">
                    <h5 class="text-uppercase mb-2 fw-bold text-black">Shop By</h5>
                    <h2 class="fw-bold" style="color:#06A5AA;">GENDER</h2>
                </div>
            </div> --}}

            <!-- Cards -->
            <div class="col-lg-12">
                <div class="row g-4">

                    <div class="col-lg-4 col-md-4 col-12">
                        <a href="{{ route('products', ['gender' => 'Men']) }}" class="gender-card-link">
                            <div class="shop-gender-section-card">
                                <img src="{{asset('website/assets/img/icon/specs-men.png')}}"  alt="Men">
                                <p>Men</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4 col-md-4 col-12">
                        <a href="{{ route('products', ['gender' => 'Women']) }}" class="gender-card-link">
                            <div class="shop-gender-section-card">
                                <img src="{{ asset('website/assets/img/icon/specs-women.png') }}" alt="Women">
                                <p>Women</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4 col-md-4 col-12">
                        <a href="{{ route('products', ['gender' => 'Kids']) }}" class="gender-card-link">
                            <div class="shop-gender-section-card">
                                <img src="{{ asset('website/assets/img/icon/specs-kids.png') }}" alt="Kids">
                                <p>Kids</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
<!-- end shop-gender-section -->
@endsection
