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

.alert {
    text-align: left !important;
}

</style>  

@endsection
@section('content')
@php
     $usr = Auth::guard()->user();
     $tbl_coupon_auto =  DB::table("tbl_coupon_auto")->where('id',1)->first();
     $tbl_coupon_auto_for =  DB::table("tbl_coupon_auto")->where('id','!=',1)->get();
 @endphp
 
 <div id="ajaxLoader" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading, please wait...</p>
    </div>
</div>
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        
        <div class="card">
            <div class="row">
                <div class="domestic-orders-header">
                    <div class="col-lg-10">
                         <h3>Discount Coupons</h3>
                    </div>
                    
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="row align-items-end g-3">
                        <div class="col-md-3">
                            <label for="reportrange" class="form-label">Date Range</label>
                            <div id="reportrange" class="form-control d-flex align-items-center" style="cursor:pointer;">
                                <i class="fa fa-calendar me-2"></i> &nbsp;
                                <span>Select Date</span>
                                <b class="caret ms-auto"></b>
                                <input type="hidden" id="date_from" name="date_from">
                                <input type="hidden" id="date_to" name="date_to">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="search" class="form-label">Coupon Type </label>
                           <select class="form-control select" style="height: 32px !important" id="coupon_generate_type" name="coupon_generate_type" >
                                <option value="">Select Type</option>
                                <option value="0">Auto Generate Coupon</option>
                                <option value="1">Manullay Create Coupon</option>
                                  
                                </select>
                        </div>

                        <div class="col-md-2">
                            <label for="payment_method" class="form-label">Coupon Code</label>
                            <input type="text" class="form-control input" placeholder="Coupon Code" id="coupon_code_table" name="coupon_code_table">
                        </div>
                        <div class="col-md-2">
                            <label for="payment_method" class="form-label">Coupon Value</label>
                            <input type="text" class="form-control input" placeholder="Coupon Code" id="coupon_value_table" name="coupon_value_table">
                        </div>
                        <div class="col-md-2">
                            <label for="payment_method" class="form-label">Mobile No</label>
                            <input type="text" class="form-control input" placeholder="Mobile No" id="mobile_no" name="mobile_no">
                        </div>
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
                                        <div class="spinner-border ms-auto text-success spinner-grow" role="status"
                                            aria-hidden="true"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table datatables-basic w-100">
                        <thead>
                            <div class="row justify-content-between align-items-center mb-3 mt-3 mr-3">
                                <div class="col-md-6">
                                    <span id="checked-order-count" class="btn btn-success">Selected Coupon: 0</span>
                                </div>
                                <div class="col-md-6">
                                     <span class="btn btn-success" data-toggle="modal" data-target="#autocouponModal">Auto Generate Coupons</span>
                                     <span  class="btn btn-success" data-toggle="modal" data-target="#manuallycouponModal">Manually Create New Coupons</span>
                                     <span id="bulk_action" class="btn btn-success">Delete Coupons</span>
                                </div>
                            </div>
                            <tr>
                                <th colspan="7" style="color: #FF0000;" colspan="2">Select checkbox and click on "Delete Coupon" button to Delete Coupon</td>
                            </tr>
                            <tr>
                                <th style="width: 0px;"></th>
                                <th class="wd-15p"><div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll">
                                <label class="form-check-label" for="checkboxSelectAll"></label></div></th>
                                <th class="wd-15p">Coupon Type</th>
                                <th class="wd-15p">Coupon Code</th>
                                <th class="wd-20p">Coupon Value	</th>
                                <th class="wd-10p">Mobile Number</th>
                                <th class="wd-10p">Minimum Sales Value	</th>
                                <th class="wd-10p">Date Range</th>
                                <th class="wd-10p">Used Date</th>
                                <th class="wd-10p">Usage</th>
                                <th class="wd-10p">Created Date</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
               </div>
            </div>
        </div>
    </div>
</section>

<!--------------- Manually Create New Coupon Modal -------------------------->
<div class="modal fade" data-backdrop="static" id="manuallycouponModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Manually Create New Coupons</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                   <form id="manuallycouponForm" method="POST">
                       @csrf
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
                            <div class="row" >
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="couptype" id="inlineRadio1" value="0" checked>
                                  <label class="form-check-label" for="inlineRadio1">Number of Coupons</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="couptype" id="inlineRadio2" value="1" >
                                  <label class="form-check-label" for="inlineRadio2">Send To Customers</label>
                                </div>
                            </div> 
                            
                            <div class="row" id="sendcust" style="display:none">
                                    <div class="col-md-4">
                                        <label for="">Select Store <span class="text-danger">*</span></label><br>
                                        <select class="form-control" style="height: 32px !important;width:220px" id="store_id" name="store_id" >
                                            <option value="">Select  Store</option>
                                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                                           @foreach($tbl_store as $tbl_store)
                                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                                          @endforeach
                                        </select>
                                        <span class="error badge text-danger" id="store_idError"></span>
                                    </div>
                                     <div class="col-md-4">
                                        <label for="">Select Category <span class="text-danger">*</span></label><br>
                                        <select class="form-control" style="height: 32px !importantwidth:220px" id="cust_category" name="cust_category">
                                          <option value="">Select Category</option>    
                                          <option value="WALKOUT">WALKOUT</option>    
                                          <option value="GOLD MEMBERSHIP">GOLD MEMBERSHIP</option>
                                          <option value="REPAIRING ">REPAIRING</option>
                                          <option value="EYE TEST ">EYE TEST</option>
                                        </select>
                                        
                                    </div>
                                </div>
                            <br>
                                <div class="row">
                                    <div class="col-md-3" id="noscoupons">
                                        <label for="">Enter No. Of Coupons <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control"   name="no_of_coupon" id="no_of_coupon">
                                        <span class="error badge text-danger" id="no_of_couponError"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Coupon Codes </label>
                                        <div class="d-flex">
                                            <div class="form-check">
                                              <input class="form-check-input" type="radio" name="coupon_codes_type" id="inlineRadio1" value="0" checked>
                                              <label class="form-check-label" for="inlineRadio1">Unique Coupon Codes </label>
                                            </div>
                                            <div class="form-check">
                                              <input class="form-check-input" type="radio" name="coupon_codes_type" id="inlineRadio2" value="1" >
                                              <label class="form-check-label" for="inlineRadio2">Common Coupon Code</label>
                                            </div>
                                           
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="commonCouponDiv" style="display:none">
                                        <label for="">Enter Common Coupon Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control"   name="coupon_code" id="coupon_code">
                                        <span class="error badge text-danger" id="coupon_codeError"></span>
                                    </div>
                                </div>
                                <br>
                                
                                <div class="row">
                                   
                                    <div class="col-md-6">
                                        <label for="">Coupon Value Type </label>
                                        <div class="d-flex">
                                            <div class="form-check">
                                              <input class="form-check-input" type="radio" name="coupon_type" id="inlineRadio3" value="1" checked>
                                              <label class="form-check-label" for="inlineRadio3">Amount  </label>
                                            </div>
                                            <div class="form-check">
                                              <input class="form-check-input" type="radio" name="coupon_type" id="inlineRadio4" value="0" >
                                              <label class="form-check-label" for="inlineRadio4">Percentage of Total Sales Amount</label>
                                            </div>
                                           
                                        </div>
                                    </div>
                                    <div class="col-md-4" >
                                        <label for="">Enter Coupon Value <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control"   name="coupon_value_manually" id="coupon_value_manually">
                                        <span class="error badge text-danger" id="coupon_value_manuallyError"></span>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4" >
                                        <label for="">Minimum Sales Value <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="min_sale_vale" id="min_sale_vale">
                                        <span class="error badge text-danger" id="min_sale_valeError"></span>
                                    </div>
                                    <div class="col-md-3" >
                                        <label for="">Valid From <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="valid_from" id="valid_from">
                                        <span class="error badge text-danger" id="valid_fromError"></span>
                                    </div>
                                     <div class="col-md-3" >
                                        <label for="">Valid To <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="valid_to" id="valid_to">
                                        <span class="error badge text-danger" id="valid_toError"></span>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Coupon Usage</label>
                                        <div class="d-flex">
                                            <div class="form-check">
                                              <input class="form-check-input" type="radio" name="coupon_usages" id="inlineRadio5" value="0" checked>
                                              <label class="form-check-label" for="inlineRadio5">For All Customers  </label>
                                            </div>
                                            <div class="form-check">
                                              <input class="form-check-input" type="radio" name="coupon_usages" id="inlineRadio6" value="1" >
                                              <label class="form-check-label" for="inlineRadio6">For New Customer Only For First Order </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                
                                
                            </div>
                            
                        </div>
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Submit </button>
                        </div>
                    </div>
                </form>    
            </div>
        </div>
    </div>
</div>


<!--------------- Auto Coupon Modal -------------------------->
<div class="modal fade" data-backdrop="static" id="autocouponModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Auto Generate Coupons</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                   <form id="autocouponForm" method="POST">
                       @csrf
                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                       <p>If you enable this option then coupon will be generated automatically when system confirm the order and system automatically send coupon code SMS to customer</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Enable  <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="auto_status" id="inlineRadio11" value="0" @if($tbl_coupon_auto->auto_status == '0') checked @endif>
                                          <label class="form-check-label" for="inlineRadio11">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="auto_status" id="inlineRadio12" value="1" @if($tbl_coupon_auto->auto_status == '1') checked @endif>
                                          <label class="form-check-label" for="inlineRadio12">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Coupon Value   <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="coupon_value_type" id="inlineRadio13" value="0" @if($tbl_coupon_auto->coupon_value_type == '0') checked @endif>
                                          <label class="form-check-label" for="inlineRadio13">Fixed Amount</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="coupon_value_type" id="inlineRadio14" value="1" @if($tbl_coupon_auto->coupon_value_type == '1') checked @endif>
                                          <label class="form-check-label" for="inlineRadio14">Percentage</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Sales Value for Minimum Sales Required  </label>
                                    <div class="d-flex">
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="sales_value" id="inlineRadio15" value="0"  @if($tbl_coupon_auto->sales_value == '0') checked @endif>
                                          <label class="form-check-label" for="inlineRadio15">Total Gross Sales (Before Discounts & Round Off & After Return Amount)</label>
                                        </div>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="sales_value" id="inlineRadio16" value="1" @if($tbl_coupon_auto->sales_value == '1') checked @endif>
                                          <label class="form-check-label" for="inlineRadio16">Total Net Sales (After Discounts & Round Off & Return Amount)</label>
                                        </div>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="sales_value" id="inlineRadio17" value="2" @if($tbl_coupon_auto->sales_value == '2') checked @endif>
                                          <label class="form-check-label" for="inlineRadio17">Total Payable Amount (After Discounts, Round Off, Loyalty Point Redemption, Coupon Redeemed, Cart Discount, Return Amount)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                   <p>Total Sales  Amount Range</p>
                                </div>    
                            </div>
                            <div class="row">
                                <table class="table table-bordered" id="couponTable">
                                  <thead>
                                    <tr>
                                      <th style="color:#000">#</th>    
                                      <th style="color:#000">From Range <span style="color:red">*</span></th>
                                      <th style="color:#000">To Range  <span style="color:red">*</span></th>
                                      <th style="color:#000">Coupon Value <span style="color:red">*</span></th>
                                      <th style="color:#000">Minimum Sales Value <span style="color:red">*</span></th>
                                      <th style="color:#000">--</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <!-- First Row -->
                                   @foreach($tbl_coupon_auto_for as $coupon)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>    
                                        <td><input type="text" class="form-control from-range" value="{{ $coupon->from_range }}" name="from_range[]"></td>
                                        <td><input type="text" class="form-control to-range" value="{{ $coupon->to_range }}" name="to_range[]"></td>
                                        <td><input type="text" class="form-control coupon-value" value="{{ $coupon->coupon_value }}" name="coupon_value[]"></td>
                                        <td><input type="text" class="form-control sales-value-amount" value="{{ $coupon->sales_value_amount }}" name="sales_value_amount[]"></td>
                                        <td><button type="button" class="deletecouponBtn" data-id="{{ $coupon->id }}">❌</button></td>
                                        <input type="hidden" class="form-control auto-id" value="{{ $coupon->id }}" name="auto_id[]">
                                    </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                            </div>
                            <button type="button" id="addRowBtn">➕ Add Row</button>
                            <hr/>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Coupon Validity (Enter Days) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"  value="{{$tbl_coupon_auto->valid_dyas}}"  name="valid_dyas" id="valid_dyas">
                                </div>
                                
                            </div>

                        </div>
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Submit </button>
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
  $(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
  });
</script> 
<script>
var start = moment('2025-01-01'); // Lifetime start date
var end = moment(); // Today

function isCurrentMonth(date) {
    return date.month() === moment().month() && date.year() === moment().year();
}

function cb(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    $('#date_from').val(start.format('YYYY-MM-DD'));
    $('#date_to').val(end.format('YYYY-MM-DD'));

    if (isCurrentMonth(start) || isCurrentMonth(end)) {
    } else {
    }

    const column = dataListView.column(0);
    column.search(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    dataListView.draw();
}

$('#reportrange').daterangepicker({
    startDate: start,
    endDate: end,
    autoUpdateInput: false,
    showDropdowns: true,
    maxDate: moment(),
    ranges: {
        'Today': [moment(), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [
            moment().subtract(1, 'month').startOf('month'),
            moment().subtract(1, 'month').endOf('month')
        ],
        'Lifetime': [moment('2025-01-01'), moment()]
    }
}, function(start, end) {
    cb(start, end);
});

// Update on apply
$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
    cb(picker.startDate, picker.endDate);
});

// Set initial range to Lifetime on load
cb(start, end);
</script>
<script>
    
$('.select2').select2({
   width: '100%' 
});
let sender_ids = [];
let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function() {
        $('#processingLoader').show();
    })
    .on('draw.dt', function() {
        $('#processingLoader').hide();
    }).DataTable({

        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "ajax": {
            "url": "{{ route('admin.discount-coupon-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) {
                d.date_to = $('#date_to').val(),
                d.date_from = $('#date_from').val(),
                d.coupon_generate_type = $('#coupon_generate_type').val(),
                d.coupon_code = $('#coupon_code_table').val(),
                d.coupon_value = $('#coupon_value_table').val(),
                d.mobile_no = $('#mobile_no').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [{
                "data": 'responsive_id',
                orderable: false,
                searchable: false
            },
            {
                "data": "coupon_id",
                orderable: false,
                searchable: false
            },
            {
                "data": "coupon_generate_type",
                orderable: false,
            },
            {
                "data": "coupon_code",
                orderable: false,
            },
            {
                "data": "coupon_value",
                orderable: false,
            },
            {
                "data": "contact_no",
                orderable: false,
            },
            
            {
                "data": "min_sale_vale",
                orderable: false,
            },
            {
                "data": "valid_to",
                orderable: false,
            },

            {
                "data": "coupon_usages_date",
                orderable: false,
                searchable: false
            },
            {
                "data": "coupon_usages",
                orderable: false,
                searchable: false
            },
            {
                "data": "created_at",
                orderable: false,
                searchable: false
            }
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
                targets: 1,
                orderable: false,
                responsivePriority: 1,
                render: function (data, type, full) {
            
                    // challan_type = 1 → disable checkbox, show icon with tooltip
                    if (full.coupon_status == 1) {
                        return (
                            '<span class="text-info" style="cursor:pointer;" title="Coupon Used">' +
                            '<i class="fa fa-info-circle fa-2x"></i>' +   // icon (can change)
                            '</span>'
                        );
                    }
            
                    // Default checkbox
                    return (
                        '<div class="form-check">' +
                        '<input class="form-check-input dt-checkboxes" type="checkbox" id="' + data + '" />' +
                        '<label class="form-check-label" for="' + data + '"></label>' +
                        '</div>'
                    );
                },
                checkboxes: {
                    selectAllRender:
                        '<div class="form-check">' +
                        '<input class="form-check-input" type="checkbox" id="checkboxSelectAll" />' +
                        '<label class="form-check-label" for="checkboxSelectAll"></label>' +
                        '</div>',
                    selectRow: true
                }
            },
        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        language: {
            paginate: {
                // remove previous & next text from pagination
                previous: 'Prev',
                next: 'Next'
            },
            sLengthMenu: "_MENU_",
            sZeroRecords: "{{ __('No results available') }}",
            sSearch: "{{ __('search') }}",
            sProcessing: "{{ __('processing') }}",
            sInfo: "{{ __('Showing :start to :end of :total entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}",
            sInfoFiltered: "" // Removes the "(filtered from xxx total entries)" text
        },
        responsive: {
            details: {
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    let data = $.map(columns, function(col) {
                        return col.title !==
                            '' // ? Do not show row in modal popup if title is blank (for check box)
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
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6 d-flex "l><"col-sm-12 col-md-6 text-end mt-1"Bf>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        aLengthMenu: [
            [10, 20, 50, 100],
            [10, 20, 50, 100],
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
    
    $('.select').on('change', function() {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });
    
    //------------------------------
     // Function to update the checked order count
    function updateCheckedOrderCount() {
        // Count checked checkboxes (excluding disabled ones)
        let count = $('input.dt-checkboxes:checked:not(:disabled)').length;
    
        // Update the display
        $('#checked-order-count').text(`Selected Coupons: ${count}`);
        $('#checked-order-count').removeClass('badge-info badge-dark');
        $('#checked-order-count').addClass(count > 0 ? 'badge-dark' : 'badge-info');
    }
    
    // Initialize the count on page load
    $(document).ready(function () {
        updateCheckedOrderCount();
    });
    
    // Handle individual checkbox changes
    $(document).on('change', 'input.dt-checkboxes', function () {
        updateCheckedOrderCount();
    });
    
    // Handle "Select All" checkbox
    $(document).on('change', '#checkboxSelectAll', function () {
        // Trigger change on all non-disabled checkboxes to sync state
        if ($(this).is(':checked')) {
            $('input.dt-checkboxes:not(:disabled)').prop('checked', true).trigger('change');
        } else {
            $('input.dt-checkboxes:not(:disabled)').prop('checked', false).trigger('change');
        }
        updateCheckedOrderCount();
    });
    
    // Update count after table redraw
    dataListView.on('draw.dt', function () {
        updateCheckedOrderCount();
    });
     //------------------------------
     
     
     $('#bulk_action').on('click', function(e) {
       
        
        let rows_selected = dataListView.column(1).checkboxes.selected();
        let sender_ids = [];
        $.each(rows_selected, function(index, rowId) {
            let checkbox = $('input[type="checkbox"][id="' + rowId + '"]');
            if (checkbox.prop('disabled') == false) {
                sender_ids.push(rowId);
            }            
        });
        if (sender_ids.length > 0) 
        {
            
                ajaxCall("{{ route('admin.bulk-delete-coupon') }}",sender_ids);
                $('#bulk_action option:first-child').attr("selected", "selected");
            
            
        }
        else 
        {
            $.toaster({ priority : 'warning', title : 'Attention!!' , message : "Please select at least one data" });
        }
    });
    
    function ajaxCall(url,ids){
        Swal.fire({
            title: "Are you sure?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: "Submit",
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger mx-1'
            },
            buttonsStyling: false,
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: ids
                        },
                         beforeSend: function () {
                          document.getElementById("global-loader").style.display = "";
                        },
                        success: function(data) {
                             document.getElementById("global-loader").style.display = "none";
                            if (data.status === true ) {
                                $.toaster({ priority : 'success', title : 'Success..!' , message : data.message});
                                dataListView.draw();
                            } else {
                                $.toaster({ priority : 'warning', title : 'Attention!!' , message : data.message});
                                if(data.code == 202){
                                    window.setTimeout(function () {
                                        location.href = data.redirect;
                                    }, 3000);
                                }
                            }
                        },
                        error: function(reject) {
                            if (reject.status === 422) {
                                let errors = reject.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $.toaster({ priority : 'warning', title : 'Attention!!' , message : value[0]});
                                });
                            } else {
                                $.toaster({ priority : 'warning', title : 'Attention!!' , message : reject.responseJSON.message});
                            }
                        }
                    })
            }
        })
    }
    
    document.querySelectorAll('input[name="coupon_codes_type"]').forEach(function(radio) {
        radio.addEventListener('change', function () {
            if (this.value === "1") {
                document.getElementById('commonCouponDiv').style.display = 'block';
            } else {
                document.getElementById('commonCouponDiv').style.display = 'none';
            }
        });
    });
    
    
    
    $("#manuallycouponForm").on("submit", function (e) {
    e.preventDefault();

    let isValid = true;

    // reset errors
    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");

    let no_of_coupon = $("#no_of_coupon").val().trim();
    let coupon_codes_type = $('input[name="coupon_codes_type"]:checked').val();
    let coupon_code = $("#coupon_code").val().trim();
    let coupon_value = $("#coupon_value_manually").val().trim();
    let min_sale_vale = $("#min_sale_vale").val().trim();
    let valid_from = $("#valid_from").val().trim();
    let valid_to = $("#valid_to").val().trim();
    let couptype = $('input[name="couptype"]:checked').val();
    let store_id = $("#store_id").val();
    let cust_category = $("#cust_category").val();

    // ✅ Validate No of Coupons (only if visible)
    if ($("#noscoupons").is(":visible") && no_of_coupon === "") {
        $("#no_of_couponError").text("No of Coupons Required.");
        $("#no_of_coupon").addClass("is-invalid");
        isValid = false;
    }

    // ✅ Validate Common Coupon Code
    if ($("#commonCouponDiv").is(":visible") && coupon_code === "") {
        $("#coupon_codeError").text("Coupon code Required.");
        $("#coupon_code").addClass("is-invalid");
        isValid = false;
    }

    // ✅ Coupon Value
    if (coupon_value === "") {
        $("#coupon_value_manuallyError").text("Coupon value required.");
        $("#coupon_value_manually").addClass("is-invalid");
        isValid = false;
    }

    // ✅ Minimum Sale
    if (min_sale_vale === "") {
        $("#min_sale_valeError").text("Minimum sales value required.");
        $("#min_sale_vale").addClass("is-invalid");
        isValid = false;
    }

    // ✅ Dates
    if (valid_from === "") {
        $("#valid_fromError").text("Valid From required.");
        $("#valid_from").addClass("is-invalid");
        isValid = false;
    }

    if (valid_to === "") {
        $("#valid_toError").text("Valid To required.");
        $("#valid_to").addClass("is-invalid");
        isValid = false;
    }



    // ❌ STOP if invalid
    if (!isValid) return;

    let formData = new FormData(this);

    $.ajax({
        type: "POST",
        url: "{{ route('admin.manually-coupon-stored') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {

            if (response.success) {

                $.toaster({
                    priority: 'success',
                    title: response.message,
                    message: ''
                });

                window.location.href = "{{ route('admin.discount-coupons') }}";

            } else {

                // show backend validation errors
                if (response.error) {
                    $.each(response.error, function (key, value) {
                        $("#" + key + "Error").text(value);
                        $("#" + key).addClass("is-invalid");
                    });
                } else {
                    $.toaster({
                        priority: 'danger',
                        title: response.message,
                        message: ''
                    });
                }

            }
        },
        error: function () {
            alert("Something went wrong!");
        }
    });
});
    
    
     /** ==============================
     *  Table row handling
     *  ============================== */
    const addRowBtn = document.getElementById("addRowBtn");
    const tableBody = document.querySelector("#couponTable tbody");

    function updateSerialNumbers() {
        tableBody.querySelectorAll("tr").forEach((row, index) => {
            row.cells[0].textContent = index + 1;
            const removeBtn = row.querySelector(".removeBtn");
            if (removeBtn) {
                removeBtn.style.display = (index === 0) ? "none" : "inline-block";
            }
        });
    }

    function validateLastRow() {
        const lastRow = tableBody.querySelector("tr:last-child");
        let isValid = true;
        if (lastRow) {
            const inputs = lastRow.querySelectorAll("input");
            inputs.forEach(input => {
                if (input.value.trim() === "") {
                    input.classList.add("error");
                    isValid = false;
                } else {
                    input.classList.remove("error");
                }
            });
        }
        return isValid;
    }

    addRowBtn.addEventListener("click", function () {
        if (!validateLastRow()) {
            $.toaster({ priority: 'danger', title: '⚠️ Please fill all required fields in the last row before adding a new one.', message: '' });
            return;
        }

        const newRow = document.createElement("tr");
        newRow.innerHTML = `
             <td></td>
             <td><input type="text" class="form-control from-range" name="from_range[]"></td>
			  <td>
				  <input type="text" class="form-control to-range" name="to_range[]">
			  </td>
			  <td><input type="text" class="form-control coupon-value" name="coupon_value[]"></td>
			  <td><input type="text" class="form-control sales-value-amount" name="sales_value_amount[]"></td>
            <td><button type="button" class="removeBtn">❌</button></td>
            <input type="hidden" class="form-control auto-id" name="auto_id[]">
        `;
        tableBody.appendChild(newRow);
        updateSerialNumbers();
    });

    tableBody.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeBtn")) {
            e.target.closest("tr").remove();
            updateSerialNumbers();
        }
    });

    updateSerialNumbers();
    
    
    
    $(document).ready(function () {

        $(document).on('click', '.deletecouponBtn', function () {
    
            var button = $(this);
            var couponId = button.data('id');
    
            if (confirm('Are you sure you want to delete this row?')) {
    
                $.ajax({
                    url: "{{ route('admin.delete-coupon-row') }}",
                    type: "GET", // ⚠️ better to use DELETE (shown below)
                    data: {
                        couponId: couponId,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function () {
                        $("#ajaxLoader").show();
                    },
                    success: function (response) {
                        // remove table row
                        button.closest('tr').remove();
    
                        $.toaster({
                            priority: 'success',
                            title: 'Success..!',
                            message: 'Row deleted successfully.'
                        });
                    },
                    error: function (xhr) {
                        $.toaster({
                            priority: 'error',
                            title: 'Error..!',
                            message: 'Failed to delete the row.'
                        });
                    },
                    complete: function () {
                        $("#ajaxLoader").fadeOut();
                    }
                });
    
            }
        });
    
    });
    
    
    $("#autocouponForm").on("submit", function (e) {
        e.preventDefault();
    
        let isValid = true;
    
        // Clear previous errors
        $(".error").text("");
        $(".is-invalid").removeClass("is-invalid");
    
        // Validate coupon validity days
        let validDays = $("#valid_dyas").val().trim();
        if (validDays === "") {
            isValid = false;
            $("#valid_dyas").addClass("is-invalid");
            alert("Coupon validity days is required");
        }
    
        // Validate at least one row exists
        let rowCount = $("#couponTable tbody tr").length;
        if (rowCount === 0) {
            isValid = false;
            alert("At least one coupon range row is required");
        }
    
        // Validate row fields
        $("#couponTable tbody tr").each(function () {
            $(this).find("input[type='text']").each(function () {
                if ($(this).val().trim() === "") {
                    $(this).addClass("is-invalid");
                    isValid = false;
                }
            });
        });
    
        if (!isValid) {
            return false;
        }
    
        let formData = new FormData(this);
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.auto-coupon-stored') }}",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    window.location.href = "{{ route('admin.discount-coupons') }}";
                }
            },
            error: function (xhr) {
                alert("Something went wrong!");
            }
        });
    });



    function showResponseMessage(data) {
    
        if (data.status === 'success') {
            $.toaster({ priority : 'success', title : 'Success..!' , message : data.message });
            dataListView.draw();
        } else if (data.status === 'error') {
            $.toaster({ priority : 'error', title : 'Opps...!' , message : data.message });
            dataListView.draw();
        } else {
            $.toaster({ priority : 'warning', title : 'Opps..!' , message : 'Something went wrong. Please try again' });
        }
    }
</script>

<script>
$(document).ready(function () {

    // Toggle main sections (Number of Coupons / Send To Customers)
    $('input[name="couptype"]').on('change', function () {
        if ($(this).val() == '0') {
            $('#noscoupons').show();
            $('#sendcust').hide();
        } else {
            $('#noscoupons').hide();
            $('#sendcust').show();
        }
    });

    // Toggle Common Coupon Code field
    $('input[name="coupon_codes_type"]').on('change', function () {
        if ($(this).val() == '1') {
            $('#commonCouponDiv').show();
        } else {
            $('#commonCouponDiv').hide();
        }
    });

});
</script>



@endsection
