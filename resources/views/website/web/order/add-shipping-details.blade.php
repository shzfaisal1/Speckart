   @extends('website.layout.master')
@section('content')
<!-- breadcrumbs-section -->
<section class="breadcrumbs-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul id="breadcrumbs">
                    <li><a href="#">Shipping Address</a></li>
                    <li><a href="#">Payment</a></li>
                    <li><a href="#">Summery</a></li>
                  
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- end breadcrumbs-section -->
 

<!-- add-shipping-details -->
<section class="add-shipping-details">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="add-shipping-details-left">
                    <h3>Add Shipping Details</h3>
                    <div class="InputGroup"> 
                        <input type="radio" name="size" id="size_1" value="small" checked />
                        <label for="size_1">Home</label>

                        <input type="radio" name="size" id="size_2" value="small" />
                        <label for="size_2">Work</label>

                        <input type="radio" name="size" id="size_3" value="small" />
                        <label for="size_3">Friend snd Family</label>

                        <input type="radio" name="size" id="size_4" value="small" />
                        <label for="size_4">Other</label>

                    </div>

                    <form action="{{url('/payment-page')}}">
                        <div class="mb-3 mt-3">
                            <input type="text" class="form-control" id="" placeholder="Pincode*" name="email">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="" placeholder="House Number, Building name*" name="pswd">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="" placeholder="Road name, Area, location*" name="pswd">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="" placeholder="landmark (optional)" name="pswd">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="" placeholder="Name*" name="pswd">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="" placeholder="Phone Number" name="pswd">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="" placeholder="Email (Optional)" name="pswd">
                        </div>
                        
                         <button type="submit" class="btn">Continue</button>
                    </form>

                </div>
            </div>
            <div class="col-lg-6">
                <div class="add-shipping-details-right">
                    <div class="add-shipping-details-right-img">
                        <img src="{{asset('website/assets/img/bg/Add-Shipping-details-bg.png')}}" alt="">
                    </div>
                    <div class="add-shipping-details-right-card">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td>Total Item Price</td>
                                    <td>₹3499</td>
                                </tr>
                                <tr>
                                    <td>Total Discount</td>
                                    <td>₹499</td>
                                </tr>
                                <tr>
                                    <td><b>Total Payable</b></td>
                                    <td><b>₹3000</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
	</div>
</section>
<!-- end add-shipping-details -->





<!-- menu tab -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>



@endsection