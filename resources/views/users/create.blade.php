@extends('layouts.master')
@section('styles')
    <style>
        .form-check-label {
            text-transform: capitalize;
        }
    </style>
@endsection

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12">
                <div class="row">
                        <div class="col-lg-12">
                            <div class="domestic-orders-header">
                                <h3>Create User or Staff </h3>
                                
                            </div>
                        </div>
                    </div>
                <div class="card">

                    {{-- <div class="card-header btn-primary">
                    </div> --}}
                    <div class="card-body">
                        @include('layouts.partials.messages')
                         <div class="row">
                            <div class="col-md-22">
                                <div class="alert alert-danger ml-0 mr-0">
                                    <ul class="mb-0">
                                        <li>All fields marked with * are mandatory.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 margin-tb">
                            <form method="POST" action="{{ url('users') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Role: <span class="text-danger">*</span></strong>
                                                <select class="form-control select" style="height: 32px !important;" id="roles" name="roles">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $value => $label)
                                                    <option value="{{ $value }}">
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Store: <span class="text-danger">*</span></strong>
                                    
                                            <select 
                                                class="form-control select" 
                                                style="height: 32px !important;" 
                                                id="store_id" 
                                                name="store_id[]" 
                                                multiple
                                            >
                                                <?php $tbl_store = DB::table("tbl_store")->where('status',1)->get(); ?>
                                                @foreach($tbl_store as $store)
                                                    <option value="{{ $store->id }}">
                                                        {{ $store->store_name }} / ({{ $store->store_id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Name: <span class="text-danger">*</span></strong>
                                            <input type="text" name="name" placeholder="Name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Mobile No: <span class="text-danger">*</span></strong>
                                            <input type="text" name="mobile_no" id="contact" placeholder="Mobile No" class="form-control" onkeyup="checkMobileLength();">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-3" id="otp-section"  style="{{ old('sotp') || $errors->has('sotp') ? '' : 'display:none' }}">    
                                        <div class="form-group">
                                             <strong>Otp: <span class="text-danger">*</span></strong>
                                            <input type="text" class="form-control @error('sotp') is-invalid @enderror" id="sotp" maxlength="4" oninput="numOnly(this.id);"
                                            name="sotp" value="{{ old('sotp') }}" placeholder="Enter Otp Here" onChange="checksignupotp();">
                                        </div>
                                        <div class="d-flex justify-content-between pt-2">
                                             <p id="timer" style="margin-top:5px;font-size: 14px;">Resend OTP in <span id="countdown">60</span>s</p>
                                             <button id="resend-btn" class="btn btn-primary " onclick="resendOTP()" disabled style="font-size: 12px;padding: 4px 14px;height: auto;max-height: fit-content;">Resend OTP</button>
                                        </div>
                                       
                                        @error('sotp')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                       
                                        
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Email: <span class="text-danger">*</span></strong>
                                            <input type="email" name="email" placeholder="Email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Gender: <span class="text-danger">*</span></strong><br>
                                            <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="Male" checked>
                                              <label class="form-check-label" for="inlineRadio1">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="Female">
                                              <label class="form-check-label" for="inlineRadio2">Female</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="gender" id="inlineRadio3" value="Other">
                                              <label class="form-check-label" for="inlineRadio3">Other</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Date of Birth: </strong>
                                            <div id="reportrange" class="pull-left"
                                                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                                <span></span> <b class="caret"></b>
                                            </div>
                                            <input type="hidden" class="form-control" id="date_from" name="date_from">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Date of Joining: </strong>
                                            <div id="reportrange1" class="pull-left"
                                                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                                <span></span> <b class="caret"></b>
                                            </div>
                                            <input type="hidden" class="form-control" id="date_from1" name="date_from1">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <strong>Address: <span class="text-danger">*</span></strong>
                                             <textarea type="text" class="form-control" placeholder="Enter address" name="address" id="address"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>State: <span class="text-danger">*</span></strong>
                                             <select class="form-control select" name="state_id" id="state_id" >
                                                <option value="" disabled selected>Select State</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>City: <span class="text-danger">*</span></strong>
                                             <select class="form-control select" name="city_id" id="city_id" >
                                                <option value="" disabled selected>Select City</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Pincode: <span class="text-danger">*</span></strong>
                                            <input type="text" maxlength="7" class="form-control" placeholder="Enter Pincode" name="pincode" id="pincode">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Approved Discount: <span class="text-danger">*</span></strong>
                                            <input type="number" name="approve_discount" placeholder="Approved Discount" value="0" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Aadhaar No: </strong>
                                            <input type="text" class="form-control" name="aadhar_no" maxlength="12"  oninput="aadharvalidateInput(event)">
                                            <div class="invalid-feedback d-none" id="adharerrorMessage">Only numbers are  allowed!</div>
                                        </div>
                                    </div>
                                     <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>PAN No: </strong>
                                            <input type="text" class="form-control" maxlength="10" name="pan_no" oninput="this.value = this.value.toUpperCase()" onblur="validatePAN(this)" >
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Password: <span class="text-danger">*</span></strong>
                                            <input type="password" name="password" placeholder="Password"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Confirm Password: <span class="text-danger">*</span></strong>
                                            <input type="password" name="confirm-password" placeholder="Confirm Password"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <h4>Upload Document</h4>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Photo: </strong>
                                            <input type="text"  class="form-control" name="photo" id="photo">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Pancard: </strong>
                                            <input type="text"  class="form-control" name="pan_img" id="pan_img">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Adhaar Front: </strong>
                                            <input type="text"  class="form-control" name="adhaar_f_img" id="adhaar_f_img">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Adhaar Back: </strong>
                                            <input type="text"  class="form-control" name="adhaar_b_img" id="adhaar_b_img">
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i
                                                class="fa fa-floppy-o" aria-hidden="true"></i>
                                            Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- data table end -->

        </div>
    </div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
  });
</script> 
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
                    serviceDropdown.append('<option value="' + state.id + '">' + state
                        .name + '</option>');
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
                                `<option value="${city.id}">${city.name}</option>`
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
        var fields = ['mobile_no', 'bb_mobile_no'];
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
    
    function validatePAN(input) {
        const pan = input.value.trim();
        const regex = /^[A-Z]{5}[0-9]{4}[A-Z]$/;
        if (!regex.test(pan)) {
            Swal.fire('Invalid PAN', 'Format: AAAAA9999A', 'error');
        }
    }
    
    
    function aadharvalidateInput(event) 
    {
        const input = event.target;
        const valid = /^[0-9]*$/.test(input.value);
        const error = document.getElementById('adharerrorMessage');
        if (!valid) {
            error.classList.remove('d-none');
            input.value = input.value.replace(/[^0-9]/g, '');
        } else {
            error.classList.add('d-none');
        }
    }



</script>
<script>
    function checkMobileLength() {
      const contact = document.getElementById('contact').value;
      
      if (contact.length === 10) {
        
        $.ajax({
          type: "POST",
          url: "{{ route('admin.signupOtp') }}",
          data: {
            contact: contact, 
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
              showOTPSection();    
              $.toaster({
                priority: "success",
                title: "Success..!",
                message: "OTP sent to your mobile number."
              });
            }
            
            else if (response.status_code === '201') {
              document.getElementById('otp-section').style.display = 'none';    
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Something went wrong!"
              });
            }
            else if (response.status_code === '202') {
              document.getElementById('otp-section').style.display = 'none';  
              document.getElementById("contact").value = "";
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Mobile No already registered."
              });
            }
          },
          error: function () {
            document.getElementById('otp-section').style.display = 'none';    
            $.toaster({
              priority: "danger",
              title: "Error..!",
              message: "Failed to send OTP. Please try again."
            });
          }
        });
      }
    }
    
    let countdownInterval;
    function showOTPSection() {
      document.getElementById('otp-section').style.display = 'block';
      document.getElementById('resend-btn').disabled = true;
      startCountdown(60); // Start timer with 60 seconds
      // Optionally: trigger actual OTP send via AJAX
    }
    
    function startCountdown(seconds) {
      clearInterval(countdownInterval); // Clear any existing timer
      let timeLeft = seconds;
      document.getElementById('countdown').textContent = timeLeft;
      
      countdownInterval = setInterval(() => {
        timeLeft--;
        document.getElementById('countdown').textContent = timeLeft;
        
        if (timeLeft <= 0) {
          clearInterval(countdownInterval);
          document.getElementById('resend-btn').disabled = false;
          document.getElementById('timer').textContent = 'Didn\'t get the OTP?';
        }
      }, 1000);
    }
    
    function resendOTP() 
    {
      // Resend OTP logic (e.g., via AJAX)
      document.getElementById('resend-btn').disabled = true;
      document.getElementById('timer').innerHTML = 'Resend OTP in <span id="countdown">60</span>s';
      startCountdown(60);
      const contact = document.getElementById('contact').value;
      
      $.ajax({
          type: "POST",
          url: "{{ route('admin.signupOtp') }}",
          data: {
            contact: contact, 
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
              showOTPSection();    
              $.toaster({
                priority: "success",
                title: "Success..!",
                message: "OTP sent to your mobile number."
              });
            } else {
              document.getElementById('otp-section').style.display = 'none';    
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Something went wrong!"
              });
            }
          },
          error: function () {
            document.getElementById('otp-section').style.display = 'none';    
            $.toaster({
              priority: "danger",
              title: "Error..!",
              message: "Failed to send OTP. Please try again."
            });
          }
        });
    }
    
    
    function checksignupotp() 
    {
      const sotp = document.getElementById('sotp').value;
        $.ajax({
          type: "POST",
          url: "{{ route('admin.checksignupOtp') }}",
          data: {
            sotp: sotp, 
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
              $.toaster({
                priority: "success",
                title: "Success..!",
                message: "OTP match successfully."
              });
            } 
            else if (response.status_code === '201') 
            {
                document.getElementById("sotp").value = "";
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Invalid otp please enter valid otp."
                  });
            }
            else if (response.status_code === '202') 
            {
                document.getElementById("sotp").value = "";
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "OTP session expired."
                  });
            }
          },
          error: function () 
          {
              document.getElementById("sotp").value = "";
                $.toaster({
                  priority: "danger",
                  title: "Error..!",
                  message: "Failed to send OTP. Please try again."
                });
          }
        });
      
    }
</script>

<script>
$(document).ready(function() {

    function toggleStoreDropdown() {
        let selectedRole = $('#roles').find('option:selected').text().trim().toLowerCase();

        if (selectedRole === 'warehouse') {
            $('#store_id').closest('.col-md-3').hide();
            $('#store_id').prop('disabled', true).val('');
        } else {
            $('#store_id').closest('.col-md-3').show();
            $('#store_id').prop('disabled', false);
        }
    }

    toggleStoreDropdown();

    $('#roles').on('change', toggleStoreDropdown);
});
</script>

@endsection