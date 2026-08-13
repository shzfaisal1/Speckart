@extends('web.layout.master')

@section('content')
<section class="my-addresses-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="my-addresses-section-form">
                    <h3>
                        My Addresses 
                        <a href="javascript:void(0)" 
                           data-bs-toggle="modal" 
                           data-bs-target="#addAddressModal"
                           onclick="resetForm()"
                           class="btn">Add New Address</a>
                    </h3>

                    <!-- Address Card 1 (Default Home) -->
                    <div class="my-addresses-section-card" style="border:1px solid #11ABB0;">
                        <h4>
                            John Doe
                            <span style="font-size:14px;color:#11ABB0;">(Default)</span>
                        </h4>
                        <p>123 MG Road, Near Central Mall</p>
                        <p>Mumbai, Maharashtra - 400001</p>
                        <p>Phone : +91 9876543210</p>

                        <div class="my-addresses-section-card-btn">
                            <!-- EDIT -->
                            <a href="{{ route('edit_address', 1) }}">
                                <img src="{{asset('assets/img/icon/edit.png')}}">
                            </a>

                            <!-- DELETE -->
                            <a href="javascript:void(0)" onclick="confirm('Delete this address?')">
                                <img src="{{asset('assets/img/icon/delete.png')}}">
                            </a>
                        </div>
                    </div>

                    <!-- Address Card 2 (Office) -->
                    <div class="my-addresses-section-card">
                        <h4>
                            John Doe
                        </h4>
                        <p>456 Business Park, Tower B, 5th Floor</p>
                        <p>Pune, Maharashtra - 411001</p>
                        <p>Phone : +91 9876543210 &nbsp; | &nbsp;
                            <a href="javascript:void(0)" style="color:#11ABB0;">Set as Default</a>
                        </p>

                        <div class="my-addresses-section-card-btn">
                            <!-- EDIT -->
                            <a href="{{ route('edit_address', 2) }}">
                                <img src="{{asset('assets/img/icon/edit.png')}}">
                            </a>

                            <!-- DELETE -->
                            <a href="javascript:void(0)" onclick="confirm('Delete this address?')">
                                <img src="{{asset('assets/img/icon/delete.png')}}">
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="account-information-section-img">
                    <img src="{{asset('assets/img/bg/Account-Information.png')}}">
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ====================== SINGLE MODAL ====================== -->
<div class="modal fade" id="addAddressModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalTitle">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="addressForm" action="{{ route('store_address') }}" method="POST">
                    @csrf

                    <input type="hidden" name="address_id" id="address_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="">
                    </div>

                    <div class="mb-3">
                        <label>Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" name="address_line_1" class="form-control" value="">
                    </div>

                    <div class="mb-3">
                        <label>Address Line 2</label>
                        <input type="text" name="address_line_2" class="form-control" value="">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" value="">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>State <span class="text-danger">*</span></label>
                            <input type="text" name="state" class="form-control" value="">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Pincode <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" class="form-control" value="">
                        </div>
                    </div>

                    <!-- TYPE -->
                    <label>Address Type <span class="text-danger">*</span></label>
                    <div class="segmented-control">
                        <input type="radio" name="type" id="add_type_home" value="home" checked>
                        <label for="add_type_home">Home</label>

                        <input type="radio" name="type" id="add_type_office" value="office">
                        <label for="add_type_office">Office</label>

                        <input type="radio" name="type" id="add_type_other" value="other">
                        <label for="add_type_other">Other</label>
                    </div>

                    <div class="address-checkbox mt-3">
                        <input type="checkbox" name="is_default" id="add_is_default" value="1">
                        <label for="add_is_default">Set as Default</label>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="submitBtn">Save Address</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
function resetForm() {
    let form = document.getElementById('addressForm');
    form.reset();
    document.getElementById('modalTitle').innerText = "Add New Address";
    document.getElementById('submitBtn').innerText = "Save Address";
    document.getElementById('address_id').value = '';
    document.getElementById('add_type_home').checked = true;
}
</script>

@endsection