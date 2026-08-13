@extends('web.layout.master')
@section('content')
<section class="account-information-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="account-information-section-form">
                    <h3>Edit Address</h3>
                    <form action="{{ url('/my-address') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3 mt-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="John" required>
                            </div>
                            <div class="col-md-6 mb-3 mt-md-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="Doe" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone *</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="+91 9876543210" required>
                        </div>
                        <div class="mb-3">
                            <label for="address_line_1" class="form-label">Address Line 1 *</label>
                            <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="123 MG Road, Near Central Mall" required>
                        </div>
                        <div class="mb-3">
                            <label for="address_line_2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="">
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" name="city" value="Mumbai" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State *</label>
                                <input type="text" class="form-control" id="state" name="state" value="Maharashtra" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pincode" class="form-label">Pincode *</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" value="400001" required>
                            </div>
                        </div>
                        <label class="form-label">Address Type *</label>
                        <div class="InputGroup"> 
                            <input type="radio" name="type" id="type_home" value="home" checked required />
                            <label for="type_home">Home</label>

                            <input type="radio" name="type" id="type_office" value="office" />
                            <label for="type_office">Office</label>
                            
                            <input type="radio" name="type" id="type_other" value="other" />
                            <label for="type_other">Other</label>
                        </div>

                        <div class="form-check mt-3 mb-3">
                            <input class="form-check-input" style="width: 20px; height: 20px; margin-right: 10px;" type="checkbox" name="is_default" value="1" id="is_default" checked>
                            <label class="form-check-label" for="is_default">
                                Set as Default Address
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Update Address</button>
                        <a href="{{ route('my_address') }}" class="btn btn-secondary mt-3 ms-2">Cancel</a>
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
@endsection
