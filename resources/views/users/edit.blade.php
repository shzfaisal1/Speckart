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
            <div class="col-12 mt-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="domestic-orders-header">
                            <h3>Edit User or Staff </h3>
                            
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('layouts.partials.messages')
                        <div class="col-lg-12 margin-tb">
                            <form method="POST" action="{{ route('admin.users.update', $user->id) }}"  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Role: <span class="text-danger">*</span></strong>
                                            <select class="form-control select" style="height: 32px !important;" id="roles" name="roles">
                                                @foreach ($roles as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ isset($userRole[$value]) ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                     @php
                                        $selectedOptions = explode(',', $user->store_id);
                                    @endphp
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Select Store: <span class="text-danger">*</span></strong>
                                            <select 
                                                class="form-control select" 
                                                style="height: 32px !important;" 
                                                id="store_id" 
                                                name="store_id[]"  <!-- important for multiple select -->
                                                multiple
                                            >
                                                @foreach(DB::table('tbl_store')->where('status', 1)->get() as $option)
                                                    <option value="{{ $option->id }}" 
                                                        {{ isset($selectedOptions) && in_array($option->id, $selectedOptions) ? 'selected' : '' }}>
                                                        {{ $option->store_name }} / ({{ $option->store_id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Name: <span class="text-danger">*</span></strong>
                                            <input type="text" name="name" placeholder="Name" class="form-control"
                                                value="{{ $user->name }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Mobile No: <span class="text-danger">*</span></strong>
                                            <input type="text" name="mobile_no" id="contact" value="{{ $user->phone }}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Email:</strong>
                                            <input type="email" name="email" placeholder="Email" class="form-control"
                                                value="{{ $user->email }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Gender: <span class="text-danger">*</span></strong><br>
                                            <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="Male" {{ $user->gender == 'Male' ? 'checked' : '' }}>
                                              <label class="form-check-label" for="inlineRadio1">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="Female" {{ $user->gender == 'Female' ? 'checked' : '' }}>
                                              <label class="form-check-label" for="inlineRadio2">Female</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="gender" id="inlineRadio3" value="Other" {{ $user->gender == 'Other' ? 'checked' : '' }}>
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
                                            <input type="hidden" class="form-control" id="date_from" value="{{$user->dob}}" name="date_from">
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
                                            <input type="hidden" class="form-control" id="date_from1" value="{{$user->doj}}" name="date_from1">
                                        </div>
                                    </div>
                                     <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <strong>Address: <span class="text-danger">*</span></strong>
                                             <textarea type="text" class="form-control" placeholder="Enter address" name="address" id="address">{{$user->address}}</textarea>
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
                                            <input type="text" maxlength="7" value="{{$user->pincode}}" class="form-control" placeholder="Enter Pincode" name="pincode" id="pincode">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Approved Discount: <span class="text-danger">*</span></strong>
                                            <input type="number" name="approve_discount" placeholder="Approved Discount" value="{{$user->approve_discount}}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Aadhaar No: </strong>
                                            <input type="text" class="form-control" name="aadhar_no" value="{{$user->aadhar_no}}" maxlength="12"  oninput="aadharvalidateInput(event)">
                                            <div class="invalid-feedback d-none" id="adharerrorMessage">Only numbers are  allowed!</div>
                                        </div>
                                    </div>
                                     <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>PAN No: </strong>
                                            <input type="text" class="form-control" maxlength="10" value="{{$user->pan_no}}" name="pan_no" oninput="this.value = this.value.toUpperCase()" onblur="validatePAN(this)" >
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Password:</strong>
                                            <input type="password" name="password" placeholder="Password"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Confirm Password:</strong>
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
                                           <input type="file" class="form-control" name="photo" id="photo">
                                        @if ($user->photo)
                                            @php
                                                $ext = strtolower(pathinfo($user->photo, PATHINFO_EXTENSION));
                                                $path = asset('user-kyc/Photo/' . $user->photo);
                                            @endphp
                                            @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                <img src="{{ $path }}" width="150" height="150"
                                                    alt="Photo Image">
                                            @elseif ($ext === 'pdf')
                                                <embed src="{{ $path }}" type="application/pdf" width="150"
                                                    height="150" />
                                            @endif
                                        @endif
                                        <div id="photoPreview" class="preview-container"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Pancard: </strong>
                                            <input type="file" class="form-control" name="pan_img" id="pan_img">
                                        @if ($user->pan_img)
                                            @php
                                                $ext = strtolower(pathinfo($user->pan_img, PATHINFO_EXTENSION));
                                                $path = asset('user-kyc/PAN/' . $user->pan_img);
                                            @endphp
                                            @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                <img src="{{ $path }}" width="150" height="150"
                                                    alt="PAN Image">
                                            @elseif ($ext === 'pdf')
                                                <embed src="{{ $path }}" type="application/pdf" width="150"
                                                    height="150" />
                                            @endif
                                        @endif
                                        <div id="panPreview" class="preview-container"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Adhaar Front: </strong>
                                            <input type="file" class="form-control" name="aadhar_front"
                                                id="front_aadhar">
                                        @if ($user->aadhar_front)
                                            @php
                                                $ext = strtolower(pathinfo($user->aadhar_front, PATHINFO_EXTENSION));
                                                $path = asset('user-kyc/ADHAAR/' . $user->aadhar_front);
                                            @endphp
                                            @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                <img src="{{ $path }}" width="150" height="150"
                                                    alt="Aadhaar Front">
                                            @elseif ($ext === 'pdf')
                                                <embed src="{{ $path }}" type="application/pdf" width="150"
                                                    height="150" />
                                            @endif
                                        @endif
                                            <div id="frontPreview" class="preview-container"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>Adhaar Back: </strong>
                                            <input type="file" class="form-control" name="aadhar_back"
                                                id="back_aadhar">
                                        @if ($user->aadhar_back)
                                            @php
                                                $ext = strtolower(pathinfo($user->aadhar_back, PATHINFO_EXTENSION));
                                                $path = asset('user-kyc/ADHAAR/' . $user->aadhar_back);
                                            @endphp
                                            @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                <img src="{{ $path }}" width="150" height="150"
                                                    alt="Aadhaar Back">
                                            @elseif ($ext === 'pdf')
                                                <embed src="{{ $path }}" type="application/pdf" width="150"
                                                    height="150" />
                                            @endif
                                        @endif
                                            <div id="backPreview" class="preview-container"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>

                                            Update User</button>
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
    
                data.forEach(function(state) 
                {
                    serviceDropdown.append(
                            '<option value="' + state.id + '"' +
                            (state.id === {{$user->state_id}} ? ' selected' : '') +
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
                                    `<option value="${city.id}" ${city.id === {{$user->city_id}} ? 'selected' : ''}>${city.name}</option>`
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
        function setupFileValidation(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            function clear() {
                input.value = '';
                preview.innerHTML = '';
            }

            input.addEventListener('change', () => {
                const file = input.files[0];
                if (!file) return;

                const ext = file.name.split('.').pop().toLowerCase();
                const allowed = ['jpg', 'jpeg', 'png', 'pdf'];
                const maxSize = 10 * 1024 * 1024;

                if (!allowed.includes(ext)) {
                    Swal.fire('Invalid file type', 'Only jpg, jpeg, png, pdf allowed.', 'error');
                    clear();
                    return;
                }

                if (file.size > maxSize) {
                    Swal.fire('File too large', 'Max size is 10MB.', 'error');
                    clear();
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    const result = e.target.result;
                    let html = ext === 'pdf' ?
                        `<embed src="${result}" type="application/pdf" width="150" height="150" />` :
                        `<img src="${result}" width="150" height="150" alt="Preview" />`;

                    html += `<button class="remove-btn" title="Remove Preview">&times;</button>`;
                    preview.innerHTML = html;

                    preview.querySelector('.remove-btn')?.addEventListener('click', clear);
                };
                reader.readAsDataURL(file);
            });
        }

        const fileInputPreviewMap = 
        {
            photo: 'photoPreview',
            pan_img: 'panPreview',
            front_aadhar: 'frontPreview', // <-- updated to match id="front_aadhar"
            back_aadhar: 'backPreview'
        };

        Object.entries(fileInputPreviewMap).forEach(([inputId, previewId]) => {
            setupFileValidation(inputId, previewId);
        });


    </script>
@endsection
