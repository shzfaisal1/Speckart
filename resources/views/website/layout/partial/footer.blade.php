<style>
    .footer-section {
        background: #07484A;
        color: #fff;
        padding: 40px 0 25px;
    }

    .footer-logo {
        max-width: 180px;
        margin-bottom: 20px;
    }

    .footer-desc {
        color: #fff;
        font-size: 14px;
        line-height: 1.8;
        margin-bottom: 25px;
    }

    .footer-widget h5 {
        color: #22F8FF;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
    }

    .footer-widget ul {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .footer-widget ul li {
        margin-bottom: 10px;
    }

    .footer-widget ul li a {
        color: #fff;
        text-decoration: none;
        transition: .3s;
        font-size: 14px;
    }

    .footer-widget ul li a:hover {
        color: #fff;
        padding-left: 5px;
    }

    .footer-social {
        display: flex;
        gap: 12px;
    }

    .footer-social a {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgb(255, 255, 255);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .3s;
    }

    .footer-social a:hover {
        transform: translateY(-4px);
        background: rgba(255,255,255,.18);
    }

    .footer-social img {
        width: 18px;
        height: 18px;
        object-fit: contain;
    }

    .footer-apps {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .footer-apps img {
        max-width: 140px;
        border-radius: 8px;
        transition: .3s;
    }

    .footer-apps img:hover {
        transform: scale(1.05);
    }

    .footer-section hr {
        margin: 40px 0 20px;
        border-color: rgba(255,255,255,.1);
    }

    .footer-bottom {
        text-align: center;
    }

    .footer-bottom p {
        color: #94a3b8;
        margin: 0;
        font-size: 14px;
    }

    /* Tablet */
    @media (max-width: 991px) {

        .footer-section {
            padding: 50px 0 20px;
        }

        .footer-widget {
            margin-top: 15px;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {

        .footer-section {
            text-align: center;
        }

        .footer-social {
            justify-content: center;
        }

        .footer-apps {
            justify-content: center;
        }

        .footer-logo {
            max-width: 150px;
        }

        .footer-widget h5 {
            margin-top: 15px;
        }
    }
</style>

<!-- Footer -->
<footer class="footer-section">
    <div class="container">

        <div class="row gy-4">

            <!-- Logo & Social -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-brand">
                    <img src="{{ asset('assets/img/logo/Specskart-logo-png.png') }}" alt="Specskart" class="footer-logo">

                    <p class="footer-desc">
                        Discover stylish eyewear, sunglasses, and contact lenses designed for every lifestyle.
                    </p>

                    <div class="footer-social">
                        <a href="#"><img src="{{ asset('assets/img/icon/fb.png') }}" alt=""></a>
                        <a href="#"><img src="{{ asset('assets/img/icon/insta.png') }}" alt=""></a>
                        <a href="#"><img src="{{ asset('assets/img/icon/twiter.png') }}" alt=""></a>
                        <a href="#"><img src="{{ asset('assets/img/icon/in.png') }}" alt=""></a>
                    </div>
                </div>
            </div>

            <!-- Eyeglasses -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-widget">
                    <h5>Eyeglasses</h5>
                    <ul>
                        <li><a href="#">Men</a></li>
                        <li><a href="#">Women</a></li>
                        <li><a href="#">Kids</a></li>
                        <li><a href="#">Fastrack</a></li>
                        <li><a href="#">Rimless</a></li>
                        <li><a href="#">Titan</a></li>
                    </ul>
                </div>
            </div>

            <!-- About -->
            <div class="col-lg-3 col-md-3 col-6">
                <div class="footer-widget">
                    <h5>About Specskart</h5>
                    <ul>
                        <li><a href="{{ url('aboutus') }}">About Us</a></li>
                        <li><a href="#">We Are Hiring</a></li>
                        <li><a href="{{url('refer-earn')}}">Refer & Earn</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="{{url('contact-us')}}">Contact Us</a></li>
                        <li><a href="{{url('coupon')}}">Coupons</a></li>
                        <li><a href="{{ url('terms-conditions') }}">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>

            <!-- Services -->
            <div class="col-lg-4 col-md-6">
                <div class="row">

                    <div class="col-6">
                        <div class="footer-widget">
                            <h5>Services</h5>
                            <ul>
                                <li><a href="{{url('store-locator')}}">Store Locator</a></li>
                                <li><a href="#">Enter My Power</a></li>
                                <li><a href="{{url('buying-guide')}}">Buying Guide</a></li>
                                <li><a href="{{url('frame-size-guide')}}">Frame Size</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="footer-widget">
                            <h5>Contact Lenses</h5>
                            <ul>
                                <li><a href="#">Bausch & Lomb</a></li>
                                <li><a href="#">Johnson & Johnson</a></li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="footer-apps mt-4">
                    <a href="#">
                        <img src="{{ asset('assets/img/logo/app-store.png') }}" alt="App Store">
                    </a>

                    <a href="#">
                        <img src="{{ asset('assets/img/logo/play-store.png') }}" alt="Play Store">
                    </a>
                </div>
            </div>

        </div>

        <hr>

        <div class="footer-bottom">
            <p>
                © {{ date('Y') }} Specskart. All Rights Reserved.
            </p>
        </div>

    </div>
</footer>
<!-- end footer -->