
@extends('web.layout.master')
@section('content')

<style>
    .account-info-section input, label, button{
        font-family: 'Poppins', sans-serif !important;
    }
    
</style>

<section class="account-info-section">
    <div class="container">

        <div class="account-info-card">

            <div class="account-info-header">
                <div class="account-info-avatar">
                    {{ strtoupper(substr($user->first_name,0,1)) }}
                </div>

                <div>
                    <h2>Account Information</h2>
                    <p>Manage your personal details and preferences.</p>
                </div>
            </div>

            <div class="account-info-highlight">
                Keep your profile information updated for a better shopping experience.
            </div>
    
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('account_information.post') }}" method="POST">
                @csrf

                <div class="row g-4">

                    <div class="col-md-4">
                        <label class="custom-label">First Name</label>

                        <div class="input-wrapper">
                            <i class="fa-solid fa-user"></i>

                            <input
                                type="text"
                                name="first_name"
                                class="custom-input"
                                value="{{ old('first_name',$user->first_name) }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="custom-label">Last Name</label>

                        <div class="input-wrapper">
                            <i class="fa fa-user"></i>

                            <input
                                type="text"
                                name="last_name"
                                class="custom-input"
                                value="{{ old('last_name',$user->last_name) }}"
                            >
                        </div>
                    </div>

                </div>

                <div class="mt-4">
                    <label class="custom-label mb-3">
                        Gender
                    </label>

                    <div class="gender-selector">

                        <input type="radio"
                            id="male"
                            name="gender"
                            value="male"
                            {{ old('gender',$user->gender)=='male'?'checked':'' }}>

                        <label for="male">
                            <i class="fa-solid fa-person"></i>
                            <span>Male</span>
                        </label>

                        <input type="radio"
                            id="female"
                            name="gender"
                            value="female"
                            {{ old('gender',$user->gender)=='female'?'checked':'' }}>

                        <label for="female">
                            <i class="fa-solid fa-venus"></i>
                            <span>Female</span>
                        </label>

                        <input type="radio"
                            id="other"
                            name="gender"
                            value="other"
                            {{ old('gender',$user->gender)=='other'?'checked':'' }}>

                        <label for="other">
                            <i class="fa-solid fa-genderless"></i>
                            <span>Other</span>
                        </label>

                    </div>
                </div>

                <div class="mt-5 text-center">
                    <a href="{{url('profile')}}" class="me-3 px-4 py-3 save-back-btn">
                        Back
                    </a>
                    <button type="submit" class="px-4 py-3 save-btn">
                        Save Changes
                    </button>
                </div>

           </form>

        </div>

    </div>
</section>





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