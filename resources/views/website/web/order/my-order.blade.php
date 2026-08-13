
   @extends('web.layout.master')
    @section('content')

<!-- my-order-section -->
<section class="my-order-section">
    <div class="container">
        <h3>My Orders</h3>
        <div class="row">
            <div class="col-lg-12">
                <div class="my-order-section-card">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="my-order-section-card-img">
                                <img src="{{asset('assets/img/bg/Eyeglasses1.png')}}" alt="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="my-order-section-card-txt">
                                <h5>Fastrack</h5>
                                <h6>Blue Wayfarer Rimmed Eyeglasses for Men (Medium)</h6>
                                <h4>₹800</h4>
                                <div class="my-order-section-card-txt1">
                                    <p>Order Date : 05 April 2025</p>
                                    <p><a href=""><span><img src="{{asset('assets/img/icon/close.png')}}" alt=""> Cancel Order</span></a></p>
                                    <p><a href=""><span><img src="{{asset('assets/img/icon/box.png')}}" alt=""> Track Order</span></a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="my-order-section-card-right">
                                <h6><span>Order ID : SPECK12345</span></h6>
                                <div class="my-order-section-card-right1">
                                    <p>Status</p>
                                    <h5 class="text-warning">In - Transit</h5>
                                </div>
                                <div class="my-order-section-card-right2">
                                    <p>Delivery Expected by</p>
                                    <h4>09 April 2025</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="my-order-section-card">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="my-order-section-card-img">
                                <img src="{{asset('assets/img/bg/Eyeglasses2.png')}}" alt="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="my-order-section-card-txt">
                                <h5>Fastrack</h5>
                                <h6>Blue Wayfarer Rimmed Eyeglasses for Men (Medium)</h6>
                                <h4>₹800</h4>
                                <div class="my-order-section-card-txt1">
                                    <p>Order Date : 05 April 2025</p>
                                    <p><a href=""><span><img src="{{asset('assets/img/icon/close.png')}}" alt=""> Return Order</span></a></p>
                                    <!-- <p><span><img src="assets/img/icon/box.png" alt=""> Track Order</span></p> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="my-order-section-card-right">
                                <h6><span>Order ID : SPECK12345</span></h6>
                                <div class="my-order-section-card-right1">
                                    <p>Status</p>
                                    <h5 class="text-success">Delivered</h5>
                                </div>
                                <div class="my-order-section-card-right2">
                                    <p>Delivery Expected by</p>
                                    <h4>09 April 2025</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="my-order-section-card">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="my-order-section-card-img">
                                <img src="{{asset('assets/img/bg/Eyeglasses3.png')}}" alt="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="my-order-section-card-txt">
                                <h5>Fastrack</h5>
                                <h6>Blue Wayfarer Rimmed Eyeglasses for Men (Medium)</h6>
                                <h4>₹800</h4>
                                <div class="my-order-section-card-txt1">
                                    <p>Order Date : 05 April 2025</p>
                                    <p><a href=""><span><img src="{{asset('assets/img/icon/close.png')}}" alt=""> Cancel Order</span></a></p>
                                    <p><a href=""><span><img src="{{asset('assets/img/icon/box.png')}}" alt=""> Track Order</span></a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="my-order-section-card-right">
                                <h6><span>Order ID : SPECK12345</span></h6>
                                <div class="my-order-section-card-right1">
                                    <p>Status</p>
                                    <h5 class="text-danger">Cancelled</h5>
                                </div>
                                <div class="my-order-section-card-right2">
                                    <p>Delivery Expected by</p>
                                    <h4>09 April 2025</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end my-order-section -->




<!-- menu tab -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
    // Show the first tab and hide the rest
    $('#tabs-nav li:first-child').addClass('active');
    $('.tab-content').hide();
    $('.tab-content:first').show();

    // Click function
    $('#tabs-nav li').mouseenter(function(){
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
    $('#tabs-nav1 li').mouseenter(function(){
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
    $('#tabs-nav2 li').mouseenter(function(){
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
    $('#tabs-nav3 li').mouseenter(function(){
        $('#tabs-nav3 li').removeClass('active');
        $(this).addClass('active');
        $('.tab-content3').hide();
    
        var activeTab = $(this).find('a').attr('href');
        $(activeTab).fadeIn();
        return false;
    });
</script>
<!-- end menu tab -->


@endsection