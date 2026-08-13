@extends('web.layout.master')
@section('content')
 <!-- profile-section -->
@php
    $sessionVoucher = session()->get('applied_voucher', null);
    $now = \Carbon\Carbon::now()->toDateString();
    $profileAvailableVouchers = DB::table('offers')
        ->where('offer_type', 'gift_voucher')
        ->whereNotNull('coupon_code')
        ->where('coupon_code', '!=', '')
        ->where('status', 'active')
        ->where(function ($q) use ($now) {
            $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
        })
        ->where(function ($q) use ($now) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
        })
        ->get();
@endphp
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
                        <h4>{{ Auth::user()->name ?? 'Customer' }}</h4>
                        <p>{{ Auth::user()->mobile ?? (Auth::user()->email ?? '+91 9876543210') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="profile-section-right">
                    <ul>
                        <li><a href="{{route('my_order')}}" class="active">My Orders</a></li>
                        <li><a href="#">My 3D Model</a></li>
                        <li><a href="{{route('account_information')}}">Account Information</a></li>
                        <li><a href="{{route('manage_notification')}}">Manage Notification</a></li>
                        <li><a href="{{route('my_address')}}">Address Book</a></li>
                        <li><a href="{{route('my_prescription')}}">My Prescriptions</a></li>
                        <li><a href="#">Saved Cards</a></li>
                        <li>
                            <a href="{{ route('cart') }}" style="color:#6b4bcf;font-weight:600;">
                                <i class="fas fa-ticket-alt me-1"></i> Check Voucher Balance
                                @if($sessionVoucher)
                                    <span class="badge ms-2" style="background:#6b4bcf;color:#fff;">Applied: ₹{{ number_format($sessionVoucher['amount_applied'] ?? 0) }}</span>
                                @elseif($profileAvailableVouchers->count() > 0)
                                    <span class="badge ms-2" style="background:#6b4bcf;color:#fff;">{{ $profileAvailableVouchers->count() }} Available</span>
                                @endif
                            </a>
                        </li>
                        <li><a href="#">Store Credit</a></li>
                    </ul>

                    {{-- My Gift Vouchers Box --}}
                    @if($sessionVoucher || $profileAvailableVouchers->count() > 0)
                        <div class="mt-4 p-3 rounded-3" style="background:#f4f0fd;border:1.5px solid #c8b7f0;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold" style="color:#4c28a8;font-size:14px;">
                                    <i class="fas fa-gift me-1" style="color:#6b4bcf;"></i> My Gift Vouchers
                                </span>
                                <a href="{{ route('cart') }}" class="btn btn-sm fw-bold px-2 py-1" style="background:#6b4bcf;color:#fff;font-size:11px;border-radius:6px;">Go to Cart</a>
                            </div>
                            @if($sessionVoucher)
                                <div class="small p-2 rounded bg-white border mb-2" style="color:#4c28a8;">
                                    <i class="bi bi-check-circle-fill me-1" style="color:#6b4bcf;"></i>
                                    Voucher <strong>{{ $sessionVoucher['code'] }}</strong> applied — ₹{{ number_format($sessionVoucher['amount_applied'] ?? 0) }} off
                                </div>
                            @endif
                            @foreach($profileAvailableVouchers as $pv)
                                <div class="small p-2 rounded bg-white border mb-1 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold" style="color:#4c28a8;"><i class="bi bi-ticket-fill me-1"></i>{{ strtoupper($pv->coupon_code) }}</span>
                                    <span class="fw-bold" style="color:#6b4bcf;">₹{{ number_format($pv->voucher_value) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
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