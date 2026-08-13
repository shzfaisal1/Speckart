@extends('web.layout.master')
@section('content')
<section class="account-information-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="account-information-section-form">
                            @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('store_address') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3 mt-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3 mt-md-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone *</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="address_line_1" class="form-label">Address Line 1 *</label>
                                <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="{{ old('address_line_1') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="address_line_2" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="{{ old('address_line_2') }}">
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="state" class="form-label">State *</label>
                                    <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="pincode" class="form-label">Pincode *</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode') }}" required>
                                </div>
                            </div>
                            <label class="form-label">Address Type *</label>
                            <div class="InputGroup"> 
                                <input type="radio" name="type" id="type_home" value="home" {{ old('type', 'home') == 'home' ? 'checked' : '' }} required />
                                <label for="type_home">Home</label>

                                <input type="radio" name="type" id="type_office" value="office" {{ old('type') == 'office' ? 'checked' : '' }} />
                                <label for="type_office">Office</label>
                                
                                <input type="radio" name="type" id="type_other" value="other" {{ old('type') == 'other' ? 'checked' : '' }} />
                                <label for="type_other">Other</label>
                            </div>

                            <div class="form-check mt-3 mb-3">
                                <input class="form-check-input" style="width: 20px; height: 20px; margin-right: 10px;" type="checkbox" name="is_default" value="1" id="is_default" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    Set as Default Address
                                </label>
                            </div>

                            <button type="submit" class="btn btn-success mt-3">Save Address</button>
                            <a href="{{ route('my_address') }}" class="btn btn-secondary mt-3 ms-2">Cancel</a>
                        </form>
                    </div>
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
