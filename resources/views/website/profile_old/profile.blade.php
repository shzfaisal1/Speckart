@extends('web.layout.master')
@section('content')
 <!-- profile-section -->
<section class="profile-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4">
                <div class="profile-section-left">
                    <div class="profile-section-left1">
                        <div class="avatar-upload">
                            <div class="avatar-edit">
                                <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg" />
                                <label for="imageUpload"></label>
                            </div>
                            <div class="avatar-preview">
                                <div id="imagePreview" style="background-image: url('{{ asset('assets/img/bg/profile.png') }}');">
                                </div>
                            </div>
                        </div>
                        <h4>John Doe</h4>
                        <p>+91 9876543210</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="profile-section-right">
                    <ul>
                        <li><a href="{{route('my_order')}}" class="active">My Orders</a></li>
                        <li><a href="">My 3D Model</a></li>
                        <li><a href="{{route('account_information')}}">Account Information</a></li>
                        <li><a href="{{route('manage_notification')}}">Manage Notification</a></li>
                        <li><a href="{{route('my_address')}}">Address Book</a></li>
                        <li><a href="{{route('my_prescription')}}">My Prescriptions</a></li>
                        <li><a href="">Saved Cards</a></li>
                        <li><a href="">Check Voucher Balance</a></li>
                        <li><a href="">Store Credit</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- img avatar-upload -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').css('background-image', 'url('+e.target.result +')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imageUpload").change(function() {
        readURL(this);
    });
</script>
<!-- end img avatar-upload -->

@endsection 