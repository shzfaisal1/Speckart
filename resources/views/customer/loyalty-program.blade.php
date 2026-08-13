@extends('layouts.master')
@section('styles')
<style>
/* Spinner when input has `loading` class */
input.loading {
    background-image: url('https://i.imgur.com/6RMhx.gif'); /* or any spinner gif you like */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}


</style>  
    

@endsection
@section('content')

@php
 $usr = Auth::guard()->user();
 $tbl_loyalty =  DB::table("tbl_loyalty")->where('id',1)->first();
 $tbl_store =  DB::table("tbl_store")->where('id',$usr->store_id)->first();
 
 $tbl_loyalty_auto =  DB::table("tbl_loyalty")->where('id',2)->first();
@endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Loyalty Program</h3>
                        <input type="text" class="form-control input" placeholder="Name,Mobile no,membership Id" id="search" name="search" style="width:320px">
                        <a class="btn pointer" style="color:#fff" data-toggle="modal" data-target="#loyaltyModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Set Loyalty Point Value
                        </a>
                        <a  class="btn pointer"  style="color:#fff" data-toggle="modal" data-target="#autoloyaltyModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Auto Generate Loyalty Points
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
               <div class="col-lg-12">
                <div class="domestic-orders-table">
                    <div id="processingLoader" class="processing-loader" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <strong class="text-success">Please wait...</strong>
                                    <div class="spinner-border ms-auto text-success spinner-grow" role="status"  aria-hidden="true"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">Sr.No</th>
                                <th class="wd-15p">Customer ID</th>
                                <th class="wd-15p">Customer Name</th>
                                <th class="wd-15p">Category</th>
                                <th class="wd-10p">Mobile No</th>
                                <th class="wd-10p">Loyalty Point Received</th>
                                <th class="wd-10p">Loyalty Points Redeem</th>
                                <th class="wd-10p">Loyalty Point Balance</th>
                                <th class="wd-10p">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
        
               </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" data-backdrop="static" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add/Remove Loyalty Point</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="loyaltyForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="customer_id" id="customer_id">

                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li>All fields marked with * are mandatory.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Add/Remove <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="add_remove" id="add_remove">
                                        <option value="">Select Type</option>
                                        <option value="1">Add</option>
                                        <option value="2">Remove</option>
                                      </select>
                                    <span class="error badge text-danger" id="add_removeError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Loyalty Points <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control"  name="Loyalty_Points_Redeem" id="Loyalty_Points_Redeem">
                                    <span class="error badge text-danger" id="Loyalty_Points_RedeemError"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Notes <span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" name="description" id="description"></textarea>
                                     <span class="error badge text-danger" id="descriptionError"></span>
                                </div>
                            </div>
                        </div>
                        
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" data-backdrop="static" id="loyaltyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Loyalty Points Value</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                   <form id="loyaltyForm">
                       
                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li>All fields marked with * are mandatory.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">    
                                <div class="col-12">
                                    <label for="">Loyalty Point Calculation: 1 Point redemption   <span class="text-danger">*</span></label>
                                    <input class="form-control" id="one_point_redem" value="{{$tbl_loyalty->one_point_redem}}" name="one_point_redem">
                                    <span class="error badge text-danger" id="one_point_redemError"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="redeemPoints">One Time Password (OTP) </label>
                                    <input type="text" class="form-control"  id="lotp" maxlength="4" oninput="numOnly(this.id);">
                                </div>
                                <div class="col-md-12">
                                      <button type="button" class="btn btn-success" id="sendOtpsetloyalty" style="margin-top: 30px;">Send Otp</button>
                                      <div id="otp-section" style="{{ old('lotp') || $errors->has('lotp') ? '' : 'display:none' }}">
                                        <div class="d-flex justify-content-between pt-2">
                                          <p id="timer" style="margin-top:20px;font-size:14px;">
                                            Resend OTP in <span id="countdown">60</span>s
                                          </p>
                                           <button id="resend-btn" class="btn btn-primary" onclick="resendOTPLoyalty()" disabled
                                                  style="font-size:12px;padding:4px 14px;height:auto;max-height:fit-content;margin-top: 30px">
                                            Resend OTP
                                          </button>
                                        </div>
                                      </div>
                                </div>
                                <p style="color:red">This OTP will be sent to {{ substr($contact_no, 0, 2) . "XXXXXX" . substr($contact_no, -2)}}.</p>
                              </div>

                        </div>
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="button" id="setpointvalue" title="Next">Submit
                            </button>
                        </div>
                    </div>
                </form>    
            </div>
        </div>
    </div>
</div>


<div class="modal fade" data-backdrop="static" id="autoloyaltyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Auto Generate Loyalty Points</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                   <form id="autoloyaltyForm">
                       @csrf
                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li>If you enable this option then loyalty points will be added automatically when system confirm the order.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Enable </label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="auto_status" id="inlineRadio3" value="0" @if($tbl_loyalty_auto->auto_status == '0') checked @endif>
                                          <label class="form-check-label" for="inlineRadio3">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="auto_status" id="inlineRadio4" value="1" @if($tbl_loyalty_auto->auto_status == '1') checked @endif>
                                          <label class="form-check-label" for="inlineRadio4">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Sales Value </label>
                                    <div class="d-flex">
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="sales_value" id="inlineRadio5" value="0"  @if($tbl_loyalty_auto->sales_value == '0') checked @endif>
                                          <label class="form-check-label" for="inlineRadio5">Total Gross Sales (Before Discounts & Round Off & After Return Amount)</label>
                                        </div>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="sales_value" id="inlineRadio6" value="1" @if($tbl_loyalty_auto->sales_value == '1') checked @endif>
                                          <label class="form-check-label" for="inlineRadio6">Total Net Sales (After Discounts & Round Off & Return Amount)</label>
                                        </div>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="sales_value" id="inlineRadio7" value="2" @if($tbl_loyalty_auto->sales_value == '2') checked @endif>
                                          <label class="form-check-label" for="inlineRadio7">Total Payable Amount (After Discounts, Round Off, Loyalty Point Redemption, Coupon Redeemed, Cart Discount, Return Amount)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Loyalty Points </label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="auto_set_loyalty_point" id="inlineRadio8" value="0" @if($tbl_loyalty_auto->auto_set_loyalty_point == '0') checked @endif>
                                          <label class="form-check-label" for="inlineRadio8">Number Of Points For Each X Number Of Sales Value</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="auto_set_loyalty_point" id="inlineRadio9" value="1" @if($tbl_loyalty_auto->auto_set_loyalty_point == '1') checked @endif>
                                          <label class="form-check-label" for="inlineRadio9">Fixed Percentage Of Sales Value (Rounded Off)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Number Of Points <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"  value="{{$tbl_loyalty_auto->no_of_points}}"  name="no_of_points" id="no_of_points">
                                </div>
                                <div class="col-md-4">
                                    <label for="">Each X Number Of Sales Value <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{$tbl_loyalty_auto->x_number_sale_value}}"  name="x_number_sale_value" id="x_number_sale_value">
                                </div>
                                <div class="col-md-4">
                                    <label for="">Fixed Percentage <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{$tbl_loyalty_auto->fixed_per}}"  name="fixed_per" id="fixed_per">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Order use loyalty Point (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control"  value="{{$tbl_loyalty_auto->order_use_loyalty}}"   name="order_use_loyalty" id="order_use_loyalty">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label >One Time Password (OTP) </label>
                                    <input type="text" class="form-control"  id="aotp" maxlength="4" oninput="numOnly(this.id);">
                                </div>
                                <div class="col-md-4">
                                      <button type="button" class="btn btn-success" id="sendOtpautoloyalty" style="margin-top: 30px;">Send Otp</button>
                                      <div id="otp-section-auto" style="{{ old('aotp') || $errors->has('aotp') ? '' : 'display:none' }}">
                                        <div class="d-flex justify-content-between pt-2">
                                          <p id="timerauto" style="margin-top:20px;font-size:14px;">
                                            Resend OTP in <span id="countdownauto">60</span>s
                                          </p>
                                           <button id="resend-btn-auto" class="btn btn-primary" onclick="resendOTPautoLoyalty()" disabled
                                                  style="font-size:12px;padding:4px 14px;height:auto;max-height:fit-content;margin-top: 30px">
                                            Resend OTP
                                          </button>
                                        </div>
                                      </div>
                                </div>
                                <p style="color:red">This OTP will be sent to {{ substr($contact_no, 0, 2) . "XXXXXX" . substr($contact_no, -2)}}.</p>
                              </div>


                        </div>
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="button" id="setautopointvalue" title="Next">Submit </button>
                        </div>
                    </div>
                </form>    
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')


<script>

function parseNum(val) 
{
    val = parseFloat(val);
    return isNaN(val) ? 0 : val;
}
    
$('#sendOtpsetloyalty').on('click', function() {

    let contact_no = "{{ $contact_no }}";  // FIXED

    if (contact_no.length !== 10) {
        $.toaster({
            priority: "danger",
            title: "Invalid",
            message: "Invalid contact number.",
            timeout: 3000
        });
        return false;
    }

    $.ajax({
        type: "POST",
        url: "{{ route('admin.setLoyaltypointOtp') }}",
        data: {
            contact: contact_no,
            _token: "{{ csrf_token() }}"
        },
        dataType: "json",
        success: function(response) {

            if (response.status_code === '200') {
                $('#sendOtpsetloyalty').hide();
                showOTPSection();
                $.toaster({
                    priority: "success",
                    title: "Success",
                    message: "OTP sent to your mobile number.",
                    timeout: 3000
                });
            }

            else {
                $('#otp-section').hide();
                $.toaster({
                    priority: "warning",
                    title: "Oops",
                    message: "Something went wrong!",
                    timeout: 3000
                });
            }
        },
        error: function() {
            $('#otp-section').hide();
            $.toaster({
                priority: "danger",
                title: "Error",
                message: "Failed to send OTP. Please try again.",
                timeout: 3000
            });
        }
    });
});


let countdownInterval;

function showOTPSection() {
    $('#otp-section').show();
    $('#resend-btn').prop('disabled', true);
    startCountdown(60); // FIXED to 60 seconds
}

function startCountdown(seconds) {

    clearInterval(countdownInterval);

    let timeLeft = seconds;
    $('#countdown').text(timeLeft);

    countdownInterval = setInterval(() => {
        timeLeft--;
        $('#countdown').text(timeLeft);

        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            $('#resend-btn').prop('disabled', false);
            $('#timer').text("Didn't get the OTP?");
        }
    }, 1000);
}

function resendOTPLoyalty(){

    $('#resend-btn').prop('disabled', true);
    $('#timer').html('Resend OTP in <span id="countdown">60</span>s');

    startCountdown(60); // Restart timer FIXED

    const contact = "{{ $contact_no }}"; // FIXED

    $.ajax({
        type: "POST",
        url: "{{ route('admin.setLoyaltypointOtp') }}",
        data: {
            contact: contact,
            _token: "{{ csrf_token() }}"
        },
        dataType: "json",
        success: function(response) {
            if (response.status_code === '200') {
                $.toaster({
                    priority: "success",
                    title: "Success",
                    message: "OTP resent successfully.",
                    timeout: 3000
                });
            } else {
                $('#otp-section').hide();
                $.toaster({
                    priority: "warning",
                    title: "Oops",
                    message: "Something went wrong!",
                    timeout: 3000
                });
            }
        },
        error: function() {
            $('#otp-section').hide();
            $.toaster({
                priority: "danger",
                title: "Error",
                message: "Failed to resend OTP.",
                timeout: 3000
            });
        }
    });
}

$('#setpointvalue').on('click', function() {

    let one_point_redem = $('#one_point_redem').val().trim();
    let lotp = $('#lotp').val().trim();

    if (Number(one_point_redem) <= 0 || isNaN(one_point_redem)) {
        $.toaster({
            priority: 'danger',
            title: '⚠️ Invalid Amount',
            message: 'Point amount should be greater than 0.',
            timeout: 3000
        });
        return false;
    }

    if (lotp.length !== 4) {
        $.toaster({
            priority: 'danger',
            title: '⚠️ OTP Error',
            message: 'Please enter a valid 4-digit OTP.',
            timeout: 3000
        });
        return false;
    }

    $.ajax({
        type: "POST",
        url: "{{ route('admin.checksetloyaltypointvalueOtp') }}",
        data: {
            lotp: lotp,
            one_point_redem: one_point_redem,
            _token: "{{ csrf_token() }}"
        },
        dataType: "json",
        success: function(response) {

            if (response.status_code === '200') {

                $('#loyaltyModal').modal('hide');

                $.toaster({
                    priority: "success",
                    title: "Success",
                    message: "Loyalty point value set successfully.",
                    timeout: 3000
                });

                recalcTotals(); // Your existing function
            }

            else if (response.status_code === '201') {
                $.toaster({
                    priority: "warning",
                    title: "Invalid OTP",
                    message: "Please enter valid OTP.",
                    timeout: 3000
                });
            }

            else if (response.status_code === '202') {
                $.toaster({
                    priority: "warning",
                    title: "OTP Expired",
                    message: "OTP has expired.",
                    timeout: 3000
                });
            }
        },
        error: function() {
            $.toaster({
                priority: "danger",
                title: "Error",
                message: "Failed to verify OTP.",
                timeout: 3000
            });
        }
    });

});

let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function() {
        $('#processingLoader').show();
    })
    .on('draw.dt', function() 
    {
      $('#processingLoader').hide();
      
    }).DataTable({

        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "ajax": {
            url: "{{ route('admin.loyaltyprogram-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.search1 = $('#search').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "cust_unique_id",
                orderable: false,
            },
            {
                "data": "cust_name",
                orderable: false,
            },
            {
                "data": "cust_category",
                orderable: false,
            },
            {
                "data": "contact_no",
                orderable: false,
            },
            {
                "data": "Loyalty_Points",
                orderable: false,
            },

            {
                "data": "Loyalty_Points_Redeem",
                orderable: false,
            },
            {
                "data": "Loyalty_Points_Bal",
                orderable: false,
            },
            {
                "data": "action",
                orderable: false,
                searchable: false
            },

            
        ],

        searchDelay: 1500,
        columnDefs: [{
                // For Responsive
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0
            },
            {
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function(data, type, full) 
                {
                    let viewUrl = `{{ route('admin.loyaltyrogram.view', ':customer_id') }}`.replace(':customer_id', full['customer_id']);

                    return (
                        `<div class="dropdown">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">ACTION</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="${viewUrl}" target="_blank">View Loyalty Point Statement</a>
                                <a class="dropdown-item" href="#" onclick="openaddModal(${full['cid']})">Add/Remove Loyalty Point</a>
                            </div>
                        </div>`
                    );
                }
            }


        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-4"i><"col-sm-12 col-md-4"p>>',

        language: {
            paginate: {

                previous: '&nbsp;',
                next: '&nbsp;'
            },
            sLengthMenu: "_MENU_",
            sZeroRecords: "{{ __('No results available') }}",
            sSearch: "{{ __('search') }}",
            sProcessing: "{{ __('processing') }}",
            sInfo: "{{ __('Showing :start to :end of :total entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}",
            sInfoFiltered: "" 
        },
        responsive: {
            details: {
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    let data = $.map(columns, function(col) {
                        return col.title !==
                            '' 
                            ?
                            '<tr data-dt-column="' +
                            col.columnIndex +
                            '">' +
                            '<td>' +
                            col.title +
                            ':' +
                            '</td> ' +
                            '<td>' +
                            col.data +
                            '</td>' +
                            '</tr>' :
                            '';
                    }).join('');

                    return data ? $('<table class="table"/>').append('<tbody>' + data +
                        '</tbody>') : false;
                }
            }
        },
        aLengthMenu: [
            [10, 20, 50, 100],
            [10, 20, 50, 100]
        ],
        select: {
            style: "multi"
        },
        order: [
            [2, "desc"]
        ],
        displayLength: 10,
    });
     let debounceTimer;
    $('.input').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            const column = dataListView.column($(this).attr('name'));
            column.search($(this).val()).draw();
        }.bind(this), 500);
    });
    
    $('.select').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });
    
    
    function openaddModal(cid) 
    {
        document.getElementById('modalTitle').innerText = 'Add/Remove Loyalty Point';
        document.getElementById('customer_id').value = cid;

        $('#addModal').modal('show');
   }
   
   
   
   $("#loyaltyForm").submit(function(e)
    {
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let add_remove = document.getElementById("add_remove" + class_name).value.trim();
        let Loyalty_Points_Redeem = document.getElementById("Loyalty_Points_Redeem" + class_name).value.trim();
        let description = document.getElementById("description" + class_name).value.trim();

        if (add_remove === "") {
            document.getElementById("add_removeError" + class_name).textContent = "Select type required.";
            document.getElementById("add_remove" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (Loyalty_Points_Redeem === "") {
            document.getElementById("Loyalty_Points_RedeemError" + class_name).textContent = "Loyalty  point is required.";
            document.getElementById("Loyalty_Points_Redeem" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (description === "")
        {
            document.getElementById("descriptionError" + class_name).textContent = "Notes is required.";
            document.getElementById("description" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
    
        if (!isValid) {
            return;
        }
    
        let form = $("#loyaltyForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.loyaltyaddremove') }}",
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
                location.reload();
            } else {
                document.querySelectorAll(".error").forEach(el => el.textContent = "");
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                
            }
        }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    });
    
    
    document.addEventListener("DOMContentLoaded", function () {
    
        const radios = document.getElementsByName("auto_set_loyalty_point");
    
        const no_of_points = document.getElementById("no_of_points").closest(".col-md-4");
        const x_number_sale_value = document.getElementById("x_number_sale_value").closest(".col-md-4");
        const fixed_per = document.getElementById("fixed_per").closest(".col-md-4");
    
        function toggleFields() {
            const selectedValue = document.querySelector('input[name="auto_set_loyalty_point"]:checked').value;
    
            if (selectedValue == "1") {
                // Show Fixed Percentage
                fixed_per.style.display = "block";
    
                // Hide Others
                no_of_points.style.display = "none";
                x_number_sale_value.style.display = "none";
            } else {
                // Show Number of points & X Sales Value
                no_of_points.style.display = "block";
                x_number_sale_value.style.display = "block";
    
                // Hide Fixed Percentage
                fixed_per.style.display = "none";
            }
        }
    
        // Trigger on load
        toggleFields();
    
        // Trigger on change
        radios.forEach(radio => {
            radio.addEventListener("change", toggleFields);
        });
    
    });
    
    
    $('#sendOtpautoloyalty').on('click', function() {

        let contact_no = "{{ $contact_no }}";  // FIXED
    
        if (contact_no.length !== 10) {
            $.toaster({
                priority: "danger",
                title: "Invalid",
                message: "Invalid contact number.",
                timeout: 3000
            });
            return false;
        }
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.setautoLoyaltypointOtp') }}",
            data: {
                contact: contact_no,
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(response) {
    
                if (response.status_code === '200') {
                    $('#sendOtpautoloyalty').hide();
                    showOTPSectionauto();
                    $.toaster({
                        priority: "success",
                        title: "Success",
                        message: "OTP sent to your mobile number.",
                        timeout: 3000
                    });
                }
    
                else {
                    $('#otp-section-auto').hide();
                    $.toaster({
                        priority: "warning",
                        title: "Oops",
                        message: "Something went wrong!",
                        timeout: 3000
                    });
                }
            },
            error: function() {
                $('#otp-section-auto').hide();
                $.toaster({
                    priority: "danger",
                    title: "Error",
                    message: "Failed to send OTP. Please try again.",
                    timeout: 3000
                });
            }
        });
    });
    
    
    let countdownIntervalauto;
    
    function showOTPSectionauto() {
        $('#otp-section-auto').show();
        $('#resend-btn-auto').prop('disabled', true);
        startCountdownauto(60); // FIXED to 60 seconds
    }
    
    function startCountdownauto(seconds) {
    
        clearInterval(countdownIntervalauto);
    
        let timeLeft = seconds;
        $('#countdownauto').text(timeLeft);
    
        countdownIntervalauto = setInterval(() => {
            timeLeft--;
            $('#countdownauto').text(timeLeft);
    
            if (timeLeft <= 0) {
                clearInterval(countdownIntervalauto);
                $('#resend-btn-auto').prop('disabled', false);
                $('#timerauto').text("Didn't get the OTP?");
            }
        }, 1000);
    }
    
    function resendOTPautoLoyalty(){
    
        $('#resend-btn-auto').prop('disabled', true);
        $('#timerauto').html('Resend OTP in <span id="countdown">60</span>s');
    
        startCountdownauto(60); // Restart timer FIXED
    
        const contact = "{{ $contact_no }}"; // FIXED
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.setautoLoyaltypointOtp') }}",
            data: {
                contact: contact,
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(response) {
                if (response.status_code === '200') {
                    $.toaster({
                        priority: "success",
                        title: "Success",
                        message: "OTP resent successfully.",
                        timeout: 3000
                    });
                } else {
                    $('#otp-section-auto').hide();
                    $.toaster({
                        priority: "warning",
                        title: "Oops",
                        message: "Something went wrong!",
                        timeout: 3000
                    });
                }
            },
            error: function() {
                $('#otp-section-auto').hide();
                $.toaster({
                    priority: "danger",
                    title: "Error",
                    message: "Failed to resend OTP.",
                    timeout: 3000
                });
            }
        });
    }
    
    $('#setautopointvalue').on('click', function() {
    
        let aotp = $('#aotp').val().trim();
    
        
    
        if (aotp.length !== 4) {
            $.toaster({
                priority: 'danger',
                title: '⚠️ OTP Error',
                message: 'Please enter a valid 4-digit OTP.',
                timeout: 3000
            });
            return false;
        }
        
        let formData = $('#autoloyaltyForm').serializeArray();
        formData.push({ name: 'aotp', value: aotp }); // add OTP manually
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.setautoloyaltyprogram') }}",
            data: formData,   // send all form values
            dataType: "json",
            success: function(response) {
    
                if (response.status_code === '200') {
    
                    $('#autoloyaltyModal').modal('hide');
    
                    $.toaster({
                        priority: "success",
                        title: "Success",
                        message: "Auto Loyalty point value set successfully.",
                        timeout: 3000
                    });
    
                    recalcTotals(); // Your existing function
                }
    
                else if (response.status_code === '201') {
                    $.toaster({
                        priority: "warning",
                        title: "Invalid OTP",
                        message: "Please enter valid OTP.",
                        timeout: 3000
                    });
                }
    
                else if (response.status_code === '202') {
                    $.toaster({
                        priority: "warning",
                        title: "OTP Expired",
                        message: "OTP has expired.",
                        timeout: 3000
                    });
                }
            },
            error: function() {
                $.toaster({
                    priority: "danger",
                    title: "Error",
                    message: "Failed to verify OTP.",
                    timeout: 3000
                });
            }
        });
    
    });
    
    function showResponseMessage(data) 
    {

        if (data.status === 'success') 
        {
            $.toaster({ priority : 'success', title : 'Success..!' , message : data.message });
            dataListView.draw();
        } else if (data.status === 'error') 
        {
            $.toaster({ priority : 'danger', title : 'Opps...!' , message : data.message });
            dataListView.draw();
        } else 
        {
            $.toaster({ priority : 'danger', title : 'Opps..!' , message : 'Something went wrong. Please try again' });
        }
    }
        
</script>





@endsection
