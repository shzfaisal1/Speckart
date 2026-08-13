@extends('web.layout.master')
@section('content')
<!-- my-order-section -->
<section class="account-information-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="account-information-section-form">
                    <h3>Account Information</h3>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('account_information.post') }}" method="POST">
                        @csrf
                        <div class="mb-3 mt-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="John" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="Doe" required>
                        </div>
                        <label class="form-label">Gender</label>
                        <div class="InputGroup"> 
                            <input type="radio" name="gender" id="gender_male" value="male" checked required />
                            <label for="gender_male">Male</label>

                            <input type="radio" name="gender" id="gender_female" value="female" />
                            <label for="gender_female">Female</label>

                            <input type="radio" name="gender" id="gender_other" value="other" />
                            <label for="gender_other">Other</label>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Update Profile</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="account-information-section-img">
                    <img src="{{asset('assets/img/bg/Account-Information.png')}}" alt="">
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
@endsection