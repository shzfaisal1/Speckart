@extends('layouts.master')
@section('styles')
  
    
<style>
.ms-auto {
    margin-left: auto !important;
}
.alert 
{
    font-size: 13px;
    text-align: left;
    padding: 0px 0px;
}
.tooltip {
  position: relative;
  display: inline-block;
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
    width: 250px;
    background-color: black;
    color: #fff;
    /* text-align: center; */
    padding: 10px;
    border-radius: 6px;
    position: absolute;
    /* z-index: 1; */
    font-size: 11px !important;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}

.select2-container--default .select2-selection--multiple 
{
    width: 100% !important;
}

.form-group {
     margin-bottom: 0px !important; 
}
</style>
@endsection
@section('content')
@php
     $usr = Auth::guard()->user();
 @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="domestic-orders-header">
                    <h3>Edit Customer</h3>
                     @if ($usr->can('Customer-List'))
                    <a href="{{route('admin.customer-list')}}" class=" btn">
                        <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                        Customer List
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:10px">
            <div class="card-body" style="padding: 5px 10px;">
                <div class="row">
                    <div class="col-md-22">
                        <div class="alert alert-danger ml-0 mr-0">
                            <ul class="mb-0">
                                <li>All fields marked with * are mandatory.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form id="customerForm" method="POST">
                    @csrf
                    <div class="row">
                         <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">B2C Customer : <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="cust_type" id="inlineRadio1" value="B2B" {{ $customer->cust_type == 'B2B' ? 'checked' : '' }}>
                                  <label class="form-check-label" for="inlineRadio1">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="cust_type" id="inlineRadio2" value="B2C" {{ $customer->cust_type == 'B2C' ? 'checked' : '' }}>
                                  <label class="form-check-label" for="inlineRadio2">No</label>
                                </div>
                                <span class="error badge text-danger" id="cust_typeError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Full Name: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{$customer->cust_name}}" id="cust_name" name="cust_name" >
                                <span class="error badge text-danger" id="cust_nameError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{$customer->contact_no}}" name="contact_no" id="contact_no"
                                 maxlength="10"  pattern="^[6-9][0-9]{9}$">
                                 <span class="error badge text-danger" id="contactError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Email Id: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{$customer->email_id}}" name="email_id" id="email_id">
                                <span class="error badge text-danger" id="email_idError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Customer Category: <span class="text-danger">*</span></label>
                                <select class="form-control select" name="cust_category" id="cust_category" >
                                    <option value="">Select Category</option>
                                    <option value="EYE TEST" {{ $customer->cust_category == 'EYE TEST' ? 'selected' : '' }}>EYE TEST</option>
                                    <option value="GOLD MEMBERSHIP" {{ $customer->cust_category == 'GOLD MEMBERSHIP' ? 'selected' : '' }}>GOLD MEMBERSHIP</option>
                                    <option value="REPAIRING" {{ $customer->cust_category == 'REPAIRING' ? 'selected' : '' }}>REPAIRING</option>
                                    <option value="WALKOUT" {{ $customer->cust_category == 'WALKOUT' ? 'selected' : '' }}>WALKOUT</option>
                                </select>
                                <span class="error badge text-danger" id="cust_categoryError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Gender : <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="Male" {{ $customer->gender == 'Male' ? 'checked' : '' }}>
                                  <label class="form-check-label" for="inlineRadio1">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="Female" {{ $customer->gender == 'Female' ? 'checked' : '' }}>
                                  <label class="form-check-label" for="inlineRadio2">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="gender" id="inlineRadio3" value="Other" {{ $customer->gender == 'Other' ? 'checked' : '' }}>
                                  <label class="form-check-label" for="inlineRadio3">Other</label>
                                </div>
                                <span class="error badge text-danger" id="genderError"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Address: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{$customer->cust_address}}" name="cust_address" id="cust_address">
                                <span class="error badge text-danger" id="cust_addressError"></span>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">State: <span class="text-danger">*</span></label>
                                <select class="form-control select" name="state_id" id="state_id" >
                                    <option value="" disabled selected>Select State</option>
                                </select>
                                <span class="error badge text-danger" id="state_idError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">City: <span class="text-danger">*</span></label>
                                <select class="form-control select" name="city_id" id="city_id" >
                                    <option value="" disabled selected>Select City</option>
                                </select>
                                <span class="error badge text-danger" id="city_idError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Pincode: <span class="text-danger">*</span></label>
                                <input type="text" maxlength="7" class="form-control" value="{{$customer->pincode}}" name="pincode" id="pincode">
                                <span class="error badge text-danger" id="pincodeError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Date of Birth: </label>
                                 <div id="reportrange" class="pull-left"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                    <span></span> <b class="caret"></b>
                                </div>
                                <input type="hidden" class="form-control" id="date_from" value="{{$customer->dob}}" name="date_from">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Date of Anniversary: </label>
                                <div id="reportrange1" class="pull-left"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                    <span></span> <b class="caret"></b>
                                </div>
                                <input type="hidden" class="form-control" id="date_from1" value="{{$customer->doa}}" name="date_from1">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="" class="form-label">Customer Notes: </label>
                                <input type="text"  class="form-control" value="{{$customer->cust_note}}" name="cust_note" id="cust_note">
                                <span class="error badge text-danger" id="cust_noteError"></span>
                            </div>
                        </div>

                    </div>
                    <hr/>
                    <input class="form-control" type="hidden" value="{{$customer->customer_id}}"  id="customer_id" name="customer_id">
                    <button type="submit" class="btn btn-primary loaderbtn">Update</button>
                </form>    
            </div>
        </div>    
    </div>
</section>

@endsection

@section('scripts')

<script>
    $(function () {
        function cb(date) {
            $('#reportrange span').html(date.format('MMMM D, YYYY'));
            $('#date_from').val(date.format('YYYY-MM-DD'));
        }
    
        function cd(date) {
            $('#reportrange1 span').html(date.format('MMMM D, YYYY'));
            $('#date_from1').val(date.format('YYYY-MM-DD'));
        }
    
        $('#reportrange').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false, // Don't auto-fill on init
            locale: {
                format: 'MMMM D, YYYY'
            }
        }, function(date) {
            cb(date);
        });
    
        $('#reportrange1').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false, // Don't auto-fill on init
            locale: {
                format: 'MMMM D, YYYY'
            }
        }, function(date) {
            cd(date);
        });
    });


    $(document).ready(function() {
    // Fetch State dynamically when the page loads
        $.ajax({
            url: "{{ route('get-state') }}",
            method: "GET",
            success: function(data) {
                var serviceDropdown = $('#state_id');
                serviceDropdown.empty(); // Clear existing options
                serviceDropdown.append('<option value="" disabled selected>Select State</option>');
    
                    data.forEach(function(state) {
                        serviceDropdown.append(
                            '<option value="' + state.id + '"' +
                            (state.id === {{$customer->state_id}} ? ' selected' : '') +
                            '>' + state.name + '</option>'
                        );
                    });
                },
            error: function(error) {
                console.error('Error fetching state:', error);
            }
        });
        
        
        $('#state_id').on('change', function() {
            const stateId = $(this).val();
            $('#city_id').empty().append('<option value="" disabled selected>Loading...</option>');
    
            if (stateId) {
                $.ajax({
                    url: "{{ route('get-city-by-state') }}",
                    type: "GET",
                    data: {
                        state_id: stateId
                    },
                    success: function(data) {
                        $('#city_id').empty().append(
                            '<option value="" disabled selected>Select City</option>');
                            data.forEach(city => {
                                $('#city_id').append(
                                    `<option value="${city.id}" ${city.id === {{$customer->city_id}} ? 'selected' : ''}>${city.name}</option>`
                                );
                            });
                    },
                    error: function() {
                        $('#city_id').empty().append(
                            '<option value="" disabled selected>No city found</option>');
                    }
                });
            }
        });
        
        $('.select').select2({
          allowClear: true
        });
    });
    
    document.addEventListener("DOMContentLoaded", function () {
        var fields = ['contact_no', 'bb_mobile_no'];
        var pattern = /^[6-9][0-9]{0,9}$/; // Allows 1–10 digits, starting with 6–9
    
        fields.forEach(function (fieldId) {
            var input = document.getElementById(fieldId);
            if (!input) return;
    
            var lastValidValue = '';
    
            input.addEventListener('input', function () {
                var currentValue = this.value;
                if (pattern.test(currentValue)) {
                    lastValidValue = currentValue;
                } else {
                    this.value = lastValidValue;
                }
            });
        });
    });


   $("#customerForm").submit(function(e) {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let cust_name = document.getElementById("cust_name" + class_name).value.trim();
        let contact_no = document.getElementById("contact_no" + class_name).value.trim();
        let email_id = document.getElementById("email_id" + class_name).value.trim();
        let cust_address = document.getElementById("cust_address" + class_name).value.trim();
        let pincode = document.getElementById("pincode" + class_name).value.trim();
        let state_id = document.getElementById("state_id" + class_name).value.trim();
        let city_id = document.getElementById("city_id" + class_name).value.trim();
        let cust_category = document.getElementById("cust_category" + class_name).value.trim();
        
        if (cust_name === "") {
            document.getElementById("cust_nameError" + class_name).textContent = "Customer Name Required.";
            document.getElementById("cust_name" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (!/^\d{10}$/.test(contact_no)) {
            document.getElementById("contactError" + class_name).textContent = "Contact must be a 10-digit number.";
            document.getElementById("contact_no" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (!/^\S+@\S+\.\S+$/.test(email_id)) {
            document.getElementById("email_idError" + class_name).textContent = "Please enter a valid email.";
            document.getElementById("email_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (cust_address === "") {
            document.getElementById("cust_addressError" + class_name).textContent = "Address is required.";
            document.getElementById("cust_address" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (!/^\d{6}$/.test(pincode)) 
        { 
            document.getElementById("pincodeError" + class_name).textContent = "Pincode must be exactly 6 digits.";
            document.getElementById("pincode" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (state_id === "") {
            document.getElementById("state_idError" + class_name).textContent = "State is required.";
            document.getElementById("state_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (city_id === "") {
            document.getElementById("city_idError" + class_name).textContent = "City is required.";
            document.getElementById("city_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (cust_category === "") {
            document.getElementById("cust_categoryError" + class_name).textContent = "Category is required.";
            document.getElementById("cust_category" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        
    
        let form = $("#customerForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.customer-update') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
                if ($.isEmptyObject(response.error)) {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    window.location.href = "{{ route('admin.customer-list') }}";
                } else {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                    $.each(response.error, function(index, value) {
                        if (value.includes("supplier_name")) {
                            $("#supplier_nameError").text(value);
                            $("#supplier_name").addClass("is-invalid");
                        }
                        if (value.includes("p_bill_no")) {
                            $("#p_bill_noError").text(value);
                            $("#p_bill_no").addClass("is-invalid");
                        }
                    });
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
    });
        
</script>


@endsection
