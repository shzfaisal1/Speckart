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
                    <h3>Generate Token</h3>
                    
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:10px">
            <div class="card-body" style="padding: 5px 10px;">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger ml-0 mr-0">
                                    <ul class="mb-0">
                                        <li>All fields marked with * are mandatory.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <form id="tokenForm" method="POST">
                            @csrf
                            <div class="row">
                                 <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="form-label">Choose Option: <span class="text-danger">*</span></label>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="visit_purpose" id="inlineRadio1" value="Full Eye Test">
                                          <label class="form-check-label" for="inlineRadio1">Full Eye Test  (Procedure Time: 15min)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="visit_purpose" id="inlineRadio2" value="Prescription Check ">
                                          <label class="form-check-label" for="inlineRadio2">Prescription Check (Procedure Time: 5min)</label>
                                        </div>
                                        <span class="error badge text-danger" id="visit_purposeError"></span>
                                    </div>
                                </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Contact no" name="contact_no" id="contact_no"
                                         maxlength="10"  pattern="^[6-9][0-9]{9}$">
                                         <span class="error badge text-danger" id="contactError"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="form-label">Customer's Full Name: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter customer name" id="cust_name" name="cust_name" >
                                        <span class="error badge text-danger" id="cust_nameError"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="form-label">Age Group : <span class="text-danger">*</span></label>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="age_group" id="inlineRadio1" value="Kid (Age 5 to 16)">
                                          <label class="form-check-label" for="inlineRadio1">Kid (Age 5 to 16)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="age_group" id="inlineRadio2" value="Adult (Age 16+)">
                                          <label class="form-check-label" for="inlineRadio2">Adult (Age 16+)</label>
                                        </div>
                                        
                                        <span class="error badge text-danger" id="age_groupError"></span>
                                    </div>
                                </div>
                               
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="form-label">Gender : <span class="text-danger">*</span></label>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="gender" id="inlineRadio3" value="Male">
                                          <label class="form-check-label" for="inlineRadio3">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="gender" id="inlineRadio4" value="Female">
                                          <label class="form-check-label" for="inlineRadio4">Female</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="gender" id="inlineRadio5" value="Other">
                                          <label class="form-check-label" for="inlineRadio5">Other</label>
                                        </div>
                                    </div>
                                    <span class="error badge text-danger" id="genderError"></span>
                                </div>
                            </div>
                            <hr/>
                            <button type="submit" class="btn btn-primary loaderbtn">Generate Token</button>
                        </form>    
                        
                    </div>
                    <div class="col-md-8">
                        <div class="col-lg-12">
                            <h5>Today's generated Token</h5>
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
                                <div class="row mb-3">
                                    <div class="col-lg-3">
                                        <div class="domestic-orders-date">
                                            <div id="reportrange" class="pull-left"
                                                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                                <span></span> <b class="caret"></b>
                                            </div>
                                            <input type="hidden" class="form-control" id="date_from" name="date_from">
                                        </div> 
                                    </div>    
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control input" placeholder="Contact no,name,token no" id="search" name="search" style="width: 300px;margin-top: 10px;">
                                        </div>
                        
                                       
                                    </div>    
                                </div>
                                <table class="table datatables-basic w-100">
                                    <thead>
                                        <tr>
                                            <th class="wd-15p">Sr.No</th>
                                            <th class="wd-15p">Customer</th>
                                            <th class="wd-15p">Token</th>
                                            <th class="wd-15p">Waiting Time</th>
                                            <th class="wd-15p">Status</th>
                                            <th class="wd-10p">Date</th>
                                            <th class="wd-10p">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                    
                           </div>
                        
                    </div>
                </div>
                
            </div>
        </div>    
    </div>
</section>

<div class="modal fade" data-backdrop="static" id="ARModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xxl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">AR TEST (Token No : <span id="tokenno"></span> / Customer Name : <span id="customer_name"></span>
                 / Contact No :  <span id="custmobile"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ARForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="uid">
                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-9">
                                    <label for="">Select Reason for visit <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" name="visit_rason[]" id="inlineRadio1" value="Regular Visit">
                                          <label class="form-check-label" for="inlineRadio1">Regular Visit</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" name="visit_rason[]" id="inlineRadio2" value="Headache">
                                          <label class="form-check-label" for="inlineRadio2">Headache</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" name="visit_rason[]" id="inlineRadio3" value="Blured Vision for Distance">
                                          <label class="form-check-label" for="inlineRadio3">Blured Vision for Distance</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" name="visit_rason[]" id="inlineRadio4" value="Blured Vision for Near">
                                          <label class="form-check-label" for="inlineRadio4">Blured Vision for Near</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" name="visit_rason[]" id="inlineRadio5" value="Watering / Dryness">
                                          <label class="form-check-label" for="inlineRadio5">Watering / Dryness</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" name="visit_rason[]" id="inlineRadio6" value="Eye Irrition">
                                          <label class="form-check-label" for="inlineRadio6">Eye Irrition</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" name="visit_rason[]" id="inlineRadio6" value="Other">
                                          <label class="form-check-label" for="inlineRadio6">Other</label>
                                        </div>
                                    </div>
                                    <span class="error badge text-danger" id="visit_rasonError"></span>
                                </div> 
                                <div class="col-md-3">
                                    <label for="">Select year of Birth <span class="text-danger">*</span></label>
                                    <input type="year" class="form-control" id="yob" name="yob">
                                    <span class="error badge text-danger" id="yobError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Select Screen Time <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="screen_time" id="inlineRadio7" value="0-2 Hours">
                                          <label class="form-check-label" for="inlineRadio7">0-2 Hours</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="screen_time" id="inlineRadio8" value="2-4 Hours">
                                          <label class="form-check-label" for="inlineRadio8">2-4 Hours</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="screen_time" id="inlineRadio9" value="4-8 Hours">
                                          <label class="form-check-label" for="inlineRadio9">4-8 Hours</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="screen_time" id="inlineRadio10" value="8-12 Hours">
                                          <label class="form-check-label" for="inlineRadio10">8-12 Hours</label>
                                        </div>
                                    </div>
                                    <span class="error badge text-danger" id="screen_timeError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Select Occupation <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Occupation" id="inlineRadio11" value="Salaried">
                                          <label class="form-check-label" for="inlineRadio11">Salaried</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Occupation" id="inlineRadio12" value="Business">
                                          <label class="form-check-label" for="inlineRadio12">Business</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Occupation" id="inlineRadio13" value="Student">
                                          <label class="form-check-label" for="inlineRadio13">Student</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Occupation" id="inlineRadio14" value="Other">
                                          <label class="form-check-label" for="inlineRadio14">Other</label>
                                        </div>
                                        
                                    </div>
                                    <span class="error badge text-danger" id="OccupationError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Is the customer carrying glasses or an existing prescription?<span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="cust_carry" id="inlineRadio15" value="Both glasses & pres.">
                                          <label class="form-check-label" for="inlineRadio15">Both glasses & pres.</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="cust_carry" id="inlineRadio16" value="Only glasses">
                                          <label class="form-check-label" for="inlineRadio16">Only glasses</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="cust_carry" id="inlineRadio17" value="Student">
                                          <label class="form-check-label" for="inlineRadio17">Only prescription </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="cust_carry" id="inlineRadio18" value="None">
                                          <label class="form-check-label" for="inlineRadio18">None</label>
                                        </div>
                                        
                                    </div>
                                    <span class="error badge text-danger" id="cust_carryError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Have you had an eye test before?<span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="eye_test_before" id="inlineRadio19" value="Yes">
                                          <label class="form-check-label" for="inlineRadio19">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="eye_test_before" id="inlineRadio20" value="No">
                                          <label class="form-check-label" for="inlineRadio20">No</label>
                                        </div>
                                    </div>
                                    <span class="error badge text-danger" id="eye_test_beforeError"></span>
                                </div>
                            </div>
                            <?php 
                                $sph_values = [];

                                for ($i = -80; $i <= 80; $i++) {
                                    $value = number_format($i * 0.25, 2, '.', '');
                                
                                    // Add + sign for positive values (optional)
                                    if ($value > 0) {
                                        $value = '+' . $value;
                                    }
                                
                                    $sph_values[] = $value;
                                }
                            ?>
                            <div class="row">
                                <div class="col-md-6">
                                     <h5>Enter AR Power</h5>
                                     <div class="table-responsive">
    									<table class="table card-table table-vcenter text-nowrap">
    										<thead >
    											<tr>
    												<th></th>
    												<th style="color: #000;">SPH</th>
    												<th style="color: #000;">CYL</th>
    												<th style="color: #000;">AXIS</th>
    											</tr>
    										</thead>
    										<tbody>
    											<tr>
    												<th scope="row">RE</th>
    												<td>
    												     <select class="form-control select" name="re_sph" id="re_sph" >
    												         <option value="">Select</option>
    												           @foreach ($sph_values as $sph)
                    											    <option value="{{ $sph }}">{{ $sph }}</option>
                                                                @endforeach
    												     </select>  
    												     
    												</td>
    												<td>
    												    <select class="form-control select" name="re_cyl" id="re_cyl" >
    												         <option value="">Select</option>
    												           @foreach ($sph_values as $sph)
                    											    <option value="{{ $sph }}">{{ $sph }}</option>
                                                                @endforeach
    												     </select> 
    												</td>
    												<td>
    												    <select class="form-control select" name="re_axis" id="re_axis" >
    												         <option value="">Select</option>
    												           @for ($i = 1; $i <= 180; $i++)
                    											    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
    												     </select>  
    												</td>
    											</tr>
    											<tr>
    												<th scope="row">LE</th>
    												<td>
    												     <select class="form-control select" name="le_sph" id="le_sph" >
    												         <option value="">Select</option>
    												           @foreach ($sph_values as $sph)
                    											    <option value="{{ $sph }}">{{ $sph }}</option>
                                                                @endforeach
    												     </select>     
    												</td>
    												<td>
    												    <select class="form-control select" name="le_cyl" id="le_cyl" >
    												         <option value="">Select</option>
    												           @foreach ($sph_values as $sph)
                    											    <option value="{{ $sph }}">{{ $sph }}</option>
                                                                @endforeach
    												     </select> 
    												</td>
    												<td>
    												    <select class="form-control select" name="le_axis" id="le_axis" >
    												         <option value="">Select</option>
    												           @for ($i = 1; $i <= 180; $i++)
                    											    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
    												     </select>
    												</td>
    											</tr>
    											
    										</tbody>
    									</table>
    								</div>
    								<div class="col-md-12">
                                        <label for="">Remarks</label>
                                        <textarea type="input" class="form-control" id="remark_arpower" name="remark_arpower"></textarea>
                                        <span class="error badge text-danger" id="remark_arpowerError"></span>
                                    </div>
								</div>
								<div class="col-md-6">
                                     <h5>Inform Customer About the Pupilometry Process</h5>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="form-label">Right Eye (RE): <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control"  id="right_eye" name="right_eye" >
                                            <span class="error badge text-danger" id="right_eyeError"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="form-label">Left Eye (LE): <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="left_eys" name="left_eys" >
                                            <span class="error badge text-danger" id="left_eysError"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="form-label">Both Eyes (BE= RE+LE): <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="both_eyes" name="both_eyes" readonly>
                                            <span class="error badge text-danger" id="both_eyesError"></span>
                                        </div>
                                    </div>
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

@endsection

@section('scripts')

<script>
   $('.select').select2({
      allowClear: true,
      width: '100%'
    });

    function cb(date) {
        $('#reportrange span').html(date.format('MMMM D, YYYY'));
        $('#date_from').val(date.format('YYYY-MM-DD'));
    }
    
    const today = moment();
    
    $('#reportrange').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false,
        startDate: today,
        maxDate: today,
        locale: {
            format: 'MMMM D, YYYY'
        }
    }, function (date) {
        cb(date);             // Set the displayed date and hidden input
        dataListView.draw();  // Redraw the DataTable after date change
    });
    
    cb(today); // Initial set
    let dataListView = $('.datatables-basic')
        .on('preXhr.dt', function () {
            $('#processingLoader').show();
        })
        .on('draw.dt', function () {
            $('#processingLoader').hide();
        }).DataTable({
            processing: true,
            serverSide: true,
            bFilter: false,
            ajax: {
                url: "{{ route('admin.token-datatable') }}",
                dataType: "json",
                type: "POST",
                data: function (d) {
                    d.search1 = $('#search').val() || null;
                    d.date_from = $('#date_from').val() || null;
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                { data: "sr_no", orderable: false },
                { data: "cust_details", orderable: false },
                { data: "token_no", orderable: false },
                { data: "wating_time", orderable: false },
                { data: "status", orderable: false },
                { data: "created_at", orderable: false },
                {
                    data: "action", orderable: false, searchable: false,
                    render: function (data, type, full) {
                        
                        if (full['estatus'] == '5') {    
                            return `
                                ----
                            `;
                        }
                        else if (full['estatus'] == '2') {    
                            return `
                                <a href="">Print Report</a>
                            `;
                        }
                        else
                        {
                            let printUrl = `{{ route('admin.token.print', ':tid') }}`.replace(':tid', full['tid']);
                            return (`<div class="dropdown"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button><div class="dropdown-menu">`+
                                '<a class="dropdown-item" href="'+printUrl+'" target="_blank">Print Token</a>'+
                                '<a class="dropdown-item" href="#" onclick="openEditModal(`' + encodeURIComponent(JSON.stringify(full)) + '`)">Start AR Test</a>'+
                                `<a class="action-delete dropdown-item" href="#" data-id="${full['test_id']}">Cancel</a>`+
                            `</div></div>`);
                        }
                    }
                }
            ],
            searchDelay: 1500,
            columnDefs: [
                {
                    className: 'control',
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0
                },
                {
                    targets: -1,
                    title: 'Actions',
                    orderable: false
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
                    renderer: function (api, rowIdx, columns) {
                        const data = columns.map(col => col.title !== '' ?
                            `<tr data-dt-column="${col.columnIndex}"><td>${col.title}:</td><td>${col.data}</td></tr>` : ''
                        ).join('');
                        return data ? $('<table class="table"/>').append(`<tbody>${data}</tbody>`) : false;
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
            order: [[2, "desc"]],
            displayLength: 10,
        });

    // Debounced column search
    let debounceTimer;
    $('.input').on('keyup', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const column = dataListView.column($(this).attr('name'));
            column.search($(this).val()).draw();
        }, 500);
    });

    $('.select').on('change', function () {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });

    // Action: Delete
    $("table").delegate(".action-delete", "click", function (e) {
        e.stopPropagation();
        const id = $(this).data('id');
        Swal.fire({
            title: "{{ __('Are you sure ?') }}",
            text: "{{ __('You would not be able to revert this!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ __('Yes, Cancel it!') }}",
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ml-2'
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "{{ url('/eyetest') }}/" + id + "/destroy",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (data) {
                        showResponseMessage(data);
                    },
                    error: function (reject) {
                        if (reject.status === 422) {
                            let errors = reject.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                toastr['warning'](value[0], "{{ __('locale.labels.attention') }}", {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });
                            });
                        } else {
                            toastr['warning'](reject.responseJSON.message, "{{ __('locale.labels.attention') }}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        }
                    }
                });
            }
        });
    });

    function showResponseMessage(data) {
        if (data.status === 'success') {
            $.toaster({ priority: 'success', title: 'Success..!', message: data.message });
            dataListView.draw();
        } else {
            $.toaster({ priority: 'danger', title: 'Oops..!', message: data.message || 'Something went wrong. Please try again' });
        }
    }


</script>
<script>

document.addEventListener("DOMContentLoaded", function () {
    const fields = ['contact_no', 'bb_mobile_no'];
    const pattern = /^[6-9][0-9]{0,9}$/;

    fields.forEach(function (fieldId) {
        const input = document.getElementById(fieldId);
        if (!input) return;

        let lastValidValue = '';

        input.addEventListener('input', function () {
            const currentValue = this.value;
            if (pattern.test(currentValue)) {
                lastValidValue = currentValue;
            } else {
                this.value = lastValidValue;
            }
        });
    });
});

$("#tokenForm").submit(function (e) {
    e.preventDefault();

    let isValid = true;
    let class_name = '';

    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");

    const visit_purpose = $('input[name="visit_purpose"]:checked').val() || "";
    const contact_no = $("#contact_no" + class_name).val().trim();
    const age_group = $('input[name="age_group"]:checked').val() || "";
    const gender = $('input[name="gender"]:checked').val() || "";
    const cust_name = $("#cust_name" + class_name).val().trim();

    if (cust_name === "") {
        $("#cust_nameError" + class_name).text("Customer Name Required.");
        $("#cust_name" + class_name).addClass("is-invalid");
        isValid = false;
    }

    if (!/^\d{10}$/.test(contact_no)) {
        $("#contactError" + class_name).text("Contact must be a 10-digit number.");
        $("#contact_no" + class_name).addClass("is-invalid");
        isValid = false;
    }

    if (visit_purpose === "") {
        $("#visit_purposeError" + class_name).text("Select Purpose.");
        isValid = false;
    }

    if (age_group === "") {
        $("#age_groupError" + class_name).text("Select Age group.");
        isValid = false;
    }

    if (gender === "") {
        $("#genderError" + class_name).text("Select gender.");
        isValid = false;
    }

    if (!isValid) return;

    const form = $("#tokenForm")[0];
    const data = new FormData(form);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.eyetesttoken-stored') }}",
        data: data,
        dataType: "JSON",
        processData: false,
        contentType: false,
        success: function (response) {
            if ($.isEmptyObject(response.error)) {
                $.toaster({
                    priority: 'success',
                    title: response.success,
                    message: ''
                });
                location.reload();
            } else {
                $(".error").text("");
                $(".is-invalid").removeClass("is-invalid");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        }
    });
});
</script>
<script>
function openEditModal(test) {
    var test = JSON.parse(decodeURIComponent(test));

    document.getElementById('customer_name').textContent = test.cust_name || '';
    document.getElementById('custmobile').textContent = test.contact_no || '';
    document.getElementById('uid').value = test.test_id;
    document.getElementById('tokenno').textContent = test.tokenno ? 'S' + test.tokenno : '';

    $('#ARModal').modal('show');
}

function cd(date) {
    $('#reportrange1 span').html(date.format('YYYY')); 
    $('#yob').val(date.format('YYYY'));         
}


$('#reportrange1').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false, 
    locale: {
        format: 'YYYY' 
    }
}, function (date) {
    cd(date); 
});


$(document).ready(function() {
    function calculateBothEyes() {
        let re = parseFloat($('#right_eye').val()) || 0;
        let le = parseFloat($('#left_eys').val()) || 0;
        let total = re + le;
        $('#both_eyes').val(total.toFixed(2));
    }

    $('#right_eye, #left_eys').on('keyup input', function() {
        calculateBothEyes();
    });
});



$("#ARForm").submit(function (e) {
    e.preventDefault();
    let isValid = true;
    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");

    if ($("input[name='visit_rason[]']:checked").length === 0) {
        $("#visit_rasonError").text("Please select at least one reason.");
        isValid = false;
    }

    const yob = $("#yob").val().trim();
    if (!/^\d{4}$/.test(yob)) {
        $("#yobError").text("Enter a valid 4-digit year.");
        $("#yob").addClass("is-invalid");
        isValid = false;
    }

    if (!$("input[name='screen_time']:checked").val()) {
        $("#screen_timeError").text("Select screen time.");
        isValid = false;
    }

    if (!$("input[name='Occupation']:checked").val()) {
        $("#OccupationError").text("Select occupation.");
        isValid = false;
    }

    if (!$("input[name='cust_carry']:checked").val()) {
        $("#cust_carryError").text("Select if customer carries glasses/prescription.");
        isValid = false;
    }

    if (!$("input[name='eye_test_before']:checked").val()) {
        $("#eye_test_beforeError").text("Please select if eye test was done before.");
        isValid = false;
    }

    const selects = ["re_sph", "re_cyl", "re_axis", "le_sph", "le_cyl", "le_axis"];
    selects.forEach(id => {
        if ($("#" + id).val() === "") {
            $("#" + id).addClass("is-invalid");
            isValid = false;
        }
    });

    const re = parseFloat($("#right_eye").val());
    const le = parseFloat($("#left_eys").val());

    if (isNaN(re)) {
        $("#right_eyeError").text("Enter right eye value.");
        $("#right_eye").addClass("is-invalid");
        isValid = false;
    }

    if (isNaN(le)) {
        $("#left_eysError").text("Enter left eye value.");
        $("#left_eys").addClass("is-invalid");
        isValid = false;
    }

    if (!isNaN(re) && !isNaN(le)) {
        const total = re + le;
        $("#both_eyes").val(total.toFixed(2));
    }

    if (!isValid) return;

    const visitReasons = $("input[name='visit_rason[]']:checked")
        .map(function () {
            return $(this).val();
        }).get().join(',');
    
    // Append manually to FormData
    const form = $("#ARForm")[0];
    const data = new FormData(form);
    data.append("visit_rason_combined", visitReasons);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.artest-stored') }}",
        data: data,
        dataType: "JSON",
        processData: false,
        contentType: false,
        success: function (response) {
            if ($.isEmptyObject(response.error)) 
            {
                $.toaster({
                    priority: 'success',
                    title: 'Success',
                    message: response.success
                });
                window.location.href = "{{ route('admin.pretest-queue') }}";
            } else if (response.errors) {
                $.each(response.errors, function (key, val) {
                    $("#" + key + "Error").text(val[0]);
                    $("#" + key).addClass("is-invalid");
                });
            }
        },
        error: function (jqXHR) {
            console.error("AJAX error", jqXHR.responseText);
        }
    });
});
</script>




@endsection
