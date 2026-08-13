@extends('layouts.master')
@section('styles')
<style>
input.loading {
    background-image: url('https://i.imgur.com/6RMhx.gif'); /* or any spinner gif you like */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}

.wrimagecard{	
	margin-top: 0;
    margin-bottom: 1.5rem;
    text-align: left;
    position: relative;
    background: #fff;
    box-shadow: 12px 15px 20px 0px rgba(46,61,73,0.15);
    border-radius: 4px;
    transition: all 0.3s ease;
}
.wrimagecard .fa{
	position: relative;
    font-size: 50px;
}
.wrimagecard-topimage_header{
padding: 20px;
}
a.wrimagecard:hover, .wrimagecard-topimage:hover {
    box-shadow: 2px 4px 8px 0px rgba(46,61,73,0.2);
}
.wrimagecard-topimage a {
    width: 100%;
    height: 100%;
    display: block;
}
.wrimagecard-topimage_title {
    padding: 10px 20px;
    position: relative;
}
.wrimagecard-topimage a {
    border-bottom: none;
    text-decoration: none;
    color: #525c65;
    transition: color 0.3s ease;
}

h4, .h4 {
    font-size: 14px;
    font-weight: 600;
}


</style>  
@endsection
@section('content')


@php
    $usr = Auth::user();
@endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Settings Master</h3>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
                @if ($usr->can('Store-list') || $usr->can('Store-Edit') || $usr->can('Store-Create'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{route('admin.store-list')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-building" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Store Master</h4>
                          </div>
                        </a>
                    </div>
                </div>
                @endif
                @if ($usr->can('Setting-Role'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.admin_path').'/roles') }}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-asterisk" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Role Master</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-Users'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.admin_path').'/users') }}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-users" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Users Master</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-Package')) 
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/package-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-area-chart" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Package Setting</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-Product-Code'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/product-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-product-hunt" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Product and Inventory Settings</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-Suppliers'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/supplier')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-users" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Suppliers</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-Tax-Master'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/tax-master')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-percent" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Tax Master</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-Barcode'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/barcode-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-barcode" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Barcode Setting</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-Sales'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/sales-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-shopping-cart" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Sales Setting</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-SMS'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/sms-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-commenting-o" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>SMS Setting</h4>
                          </div>
                        </a>
                    </div>
                </div>
                @endif
                @if ($usr->can('Setting-SMS-Template'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/smstemplate-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-commenting-o" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>SMS Template Setting</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-WhatsApp'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/whatsapp-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%)">
                            <center><i class="fa fa-whatsapp" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>WhatsApp Setting</h4>
                          </div>
                        </a>
                    </div>
                </div>
                @endif
                @if ($usr->can('Setting-WhatsApp-Template'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/whatsapptemplate-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-whatsapp" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>WhatsApp Template Setting</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Discount-Barcode'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{route('admin.barcode-wise-discount')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-barcode" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Barcode Wise Discount</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Discount-Productid'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{route('admin.productid-wise-discount')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-product-hunt" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Product  Wise Discount</h4>
                          </div>
                        </a>
                    </div>
                </div>
                @endif
                @if ($usr->can('Discount-Brand'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{route('admin.brand-wise-discount')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%) ">
                            <center><i class="fa fa-first-order" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Brand Wise Discount</h4>
                          </div>
                        </a>
                     </div>
                </div>
                @endif
                @if ($usr->can('Setting-Mystry-Audit'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{ url(config('app.vendor_path').'/mystryaudit-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%)">
                            <center><i class="fa fa-question-circle" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Mystry Audit Setting</h4>
                          </div>
                        </a>
                    </div>
                </div>
                @endif
                @if ($usr->can('Setting-Membership'))
                <div class="col-md-3 col-sm-4">
                	<div class="wrimagecard wrimagecard-topimage">
                        <a href="{{route('admin.membership-setting')}}">
                          <div class="wrimagecard-topimage_header" style="background-color:rgb(0 72 74 / 79%)">
                            <center><i class="fa fa-id-card" style="color:#fff"></i></center>
                          </div>
                          <div class="wrimagecard-topimage_title">
                            <h4>Membership Setting</h4>
                          </div>
                        </a>
                    </div>
                </div>
                @endif
            </div>    
        </div>
    </div>
</section>
@endsection

@section('scripts')


@endsection
