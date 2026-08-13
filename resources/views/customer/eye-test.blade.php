<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts.master')
@section('styles')
<style>
.ms-auto {
    margin-left: auto !important;
}
.alert {
    font-size: 13px;
    text-align: left;
    padding: 0px 0px;
}
.tooltip {
    position: relative;
    display: inline-block;
}
.tooltip .tooltiptext {
    visibility: hidden;
    width: 250px;
    background-color: black;
    color: #fff;
    padding: 10px;
    border-radius: 6px;
    position: absolute;
    font-size: 11px !important;
}
.tooltip:hover .tooltiptext {
    visibility: visible;
}
.select2-container--default .select2-selection--multiple {
    width: 100% !important;
}
.form-group {
    margin-bottom: 0px !important;
}
.is-invalid {
    border: 1px solid red !important;
}

select.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.1rem rgba(220, 53, 69, 0.25);
}

.error {
    color: #dc3545;
    font-size: 13px;
    display: block;
    margin-top: 2px;
}
</style>
@endsection

@section('content')
@php $usr = Auth::guard()->user(); @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="domestic-orders-header">
                    <h3>EYE TEST</h3>
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:10px">
            <div class="card-body" style="padding: 5px 10px;">
                <div class="row">
                    <h5 class="modal-title" id="modalTitle">EYE TEST (Token No : <span>{{$Eyetest->token_no}}</span> / Customer Name : <span>{{$Eyetest->cust_name}}</span> / Contact No : <span>{{$Eyetest->contact_no}}</span>)</h5>
                </div>
                <div class="row">
                    <div class="panel panel-dark" style="width: 100%;">
                        <div class="tab-menu-heading">
                            <div class="tabs-menu">
                                <ul class="nav panel-tabs">
                                    <li><a href="#tab1" class="active" data-toggle="tab">Step 1</a></li>
                                    <li><a href="#tab2" data-toggle="tab">Step 2</a></li>
                                    <li><a href="#tab3" data-toggle="tab">Step 3</a></li>
                                    <li><a href="#tab4" data-toggle="tab">Step 4</a></li>
                                    <li><a href="#tab5" data-toggle="tab">Step 5</a></li>
                                    <li><a href="#tab6" data-toggle="tab">Step 6</a></li>
                                    <li><a href="#tab7" data-toggle="tab">Step 7</a></li>
                                    <li><a href="#tab8" data-toggle="tab">Step 8</a></li>
                                    <li><a href="#tab9" data-toggle="tab">Final Step</a></li>
                                </ul>
                            </div>
                        </div>
   
                        <div class="panel-body tabs-menu-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <div class="multisteps-form__content">
                                        <div class="multisteps-form__content1">
                                            @php
                                                $sph_values = [];
                                                for ($i = -80; $i <= 80; $i++) {
                                                    $value = number_format($i * 0.25, 2, '.', '');
                                                    if ($value > 0) $value = '+' . $value;
                                                    $sph_values[] = $value;
                                                }
                                            @endphp
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-label">Select Optometrist: <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" placeholder="Enter Optometrist Name" value="{{$Eyetest->optometrist}}" id="Optometrist" name="Optometrist">
                                                            <span class="error badge text-danger" id="OptometristError"></span>
                                                        </div>
                                                    </div>
                                                    <h5>AR Power</h5>
                                                    <div class="table-responsive">
                                                        <table class="table card-table table-vcenter text-nowrap">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th style="color:#000">SPH</th>
                                                                    <th style="color:#000">CYL</th>
                                                                    <th style="color:#000">AXIS</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">RE</th>
                                                                    <td>
                                                                        <select class="form-control select" name="re_sph" id="re_sph">
                                                                            @foreach ($sph_values as $sph)
                                                                                <option value="{{ $sph }}" {{ $Eyetest->re_sph == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control select" name="re_cyl" id="re_cyl">
                                                                            @foreach ($sph_values as $sph)
                                                                                <option value="{{ $sph }}" {{ $Eyetest->re_cyl == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control select" name="re_axis" id="re_axis">
                                                                            <option value="">Select</option>
                                                                            @for ($i = 1; $i <= 180; $i++)
                                                                                <option value="{{ $i }}" {{ $Eyetest->re_axis == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                            @endfor
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">LE</th>
                                                                    <td>
                                                                        <select class="form-control select" name="le_sph" id="le_sph">
                                                                            <option value="">Select</option>
                                                                            @foreach ($sph_values as $sph)
                                                                                <option value="{{ $sph }}" {{ $Eyetest->le_sph == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control select" name="le_cyl" id="le_cyl">
                                                                            <option value="">Select</option>
                                                                            @foreach ($sph_values as $sph)
                                                                                <option value="{{ $sph }}" {{ $Eyetest->le_cyl == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control select" name="le_axis" id="le_axis">
                                                                            <option value="">Select</option>
                                                                            @for ($i = 1; $i <= 180; $i++)
                                                                                <option value="{{ $i }}" {{ $Eyetest->le_axis == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                            @endfor
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="card bg-dark">
                                                                <div class="card-body">
                                                                    <div class="d-flex no-block align-items-center">
                                                                        <div>
                                                                            <h6 class="text-white">Reason for Visit</h6>
                                                                            <h6 class="text-white">{{$Eyetest->visit_rason}}</h6>
                                                                        </div>
                                                                        <div class="ml-auto">
                                                                            <span class="text-white display-6"><i class="fa fa-eye"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $birthYear = $Eyetest->yob;
                                                            $currentYear = date("Y");
                                                            $age = $currentYear - $birthYear;
                                                        @endphp
                                                        <div class="col-md-4">
                                                            <div class="card bg-dark">
                                                                <div class="card-body">
                                                                    <div class="d-flex no-block align-items-center">
                                                                        <div>
                                                                            <h6 class="text-white">Age & Birth Year</h6>
                                                                            <h6 class="text-white">{{$age}} years old <br> {{$Eyetest->yob}} born</h6>
                                                                        </div>
                                                                        <div class="ml-auto">
                                                                            <span class="text-white display-6"><i class="fa fa-calendar"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card bg-dark">
                                                                <div class="card-body">
                                                                    <div class="d-flex no-block align-items-center">
                                                                        <div>
                                                                            <h6 class="text-white">Carry</h6>
                                                                            <h6 class="text-white">{{$Eyetest->cust_carry}}</h6>
                                                                        </div>
                                                                        <div class="ml-auto">
                                                                            <span class="text-white display-6"><i class="fa-solid fa-glasses"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card bg-dark">
                                                                <div class="card-body">
                                                                    <div class="d-flex no-block align-items-center">
                                                                        <div>
                                                                            <h6 class="text-white">Occupation</h6>
                                                                            <h6 class="text-white">{{$Eyetest->Occupation}}</h6>
                                                                        </div>
                                                                        <div class="ml-auto">
                                                                            <span class="text-white display-6"><i class="fas fa-industry"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card bg-dark">
                                                                <div class="card-body">
                                                                    <div class="d-flex no-block align-items-center">
                                                                        <div>
                                                                            <h6 class="text-white">Screen Time</h6>
                                                                            <h6 class="text-white">{{$Eyetest->screen_time}}</h6>
                                                                        </div>
                                                                        <div class="ml-auto">
                                                                            <span class="text-white display-6"><i class="fa fa-desktop"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="button-row d-flex mt-4">
                                            <button class="btn btn-success" id="step1" type="button">Okay, Go to Next Step</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab2">
                                    <div class="row">
                                         <div class="col-md-4">
                                             <label for="" class="form-label">Last Eye Test Date: </label> 
                                            <div class="domestic-orders-date" style="margin-top:-10px">
                                                <div id="reportrange" class="pull-left"
                                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                                    <span></span> <b class="caret"></b>
                                                </div>
                                                <input type="hidden" class="form-control" id="date_from" name="date_from">
                                            </div> 
                                        </div> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="" class="form-label">Status of Test: <span class="text-danger">*</span></label>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="test_status" id="inlineRadio1" value="Aided" {{ $Eyetest->test_status == 'Aided' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio1">Aided</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="test_status" id="inlineRadio2" value="Unaided" {{ $Eyetest->test_status == 'Unaided' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio2">Unaided</label>
                                                </div>
                                                
                                                <span class="error badge text-danger" id="test_statusError"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="" class="form-label">Distance Vision: <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                         <select class="form-control select" style="height: 32px !important;" id="re_distance" name="re_distance">
                                                             <option value="">Select RE</option>
                                                          <?php $tbl_visual_acuity =  DB::table("tbl_distance_vision")->get();  ?>
                                                           @foreach($tbl_visual_acuity as $tbl_visual_acuity)
                                                          <option value="{{$tbl_visual_acuity->visual_no}}" {{ $Eyetest->re_distance == $tbl_visual_acuity->visual_no ? 'selected' : '' }}>{{$tbl_visual_acuity->visual_no}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="re_distanceError"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control select" style="height: 32px !important;" id="le_distance" name="le_distance">
                                                             <option value="">Select LE</option>
                                                          <?php $tbl_visual_acuity =  DB::table("tbl_distance_vision")->get();  ?>
                                                           @foreach($tbl_visual_acuity as $tbl_visual_acuity)
                                                          <option value="{{$tbl_visual_acuity->visual_no}}" {{ $Eyetest->le_distance == $tbl_visual_acuity->visual_no ? 'selected' : '' }}>{{$tbl_visual_acuity->visual_no}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="le_distanceError"></span>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="" class="form-label">Pinhole Vision: <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                         <select class="form-control select" style="height: 32px !important;" id="re_pinhole" name="re_pinhole">
                                                             <option value="">Select RE</option>
                                                          <?php $tbl_visual_acuity =  DB::table("tbl_distance_vision")->get();  ?>
                                                           @foreach($tbl_visual_acuity as $tbl_visual_acuity)
                                                          <option value="{{$tbl_visual_acuity->visual_no}}" {{ $Eyetest->re_pinhole == $tbl_visual_acuity->visual_no ? 'selected' : '' }}>{{$tbl_visual_acuity->visual_no}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="re_pinholeError"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control select" style="height: 32px !important;" id="le_pinhole" name="le_pinhole">
                                                             <option value="">Select LE</option>
                                                          <?php $tbl_visual_acuity =  DB::table("tbl_distance_vision")->get();  ?>
                                                           @foreach($tbl_visual_acuity as $tbl_visual_acuity)
                                                          <option value="{{$tbl_visual_acuity->visual_no}}" {{ $Eyetest->le_pinhole == $tbl_visual_acuity->visual_no ? 'selected' : '' }}>{{$tbl_visual_acuity->visual_no}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="le_pinholeError"></span>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="" class="form-label">Near Vision: <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                         <select class="form-control select" style="height: 32px !important;" id="re_near" name="re_near">
                                                             <option value="">Select RE</option>
                                                          <?php $tbl_near_vision =  DB::table("tbl_near_vision")->get();  ?>
                                                           @foreach($tbl_near_vision as $tbl_near_vision)
                                                          <option value="{{$tbl_near_vision->near_vo}}" {{ $Eyetest->re_near == $tbl_near_vision->near_vo ? 'selected' : '' }}>{{$tbl_near_vision->near_vo}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="re_nearError"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control select" style="height: 32px !important;" id="le_near" name="le_near">
                                                             <option value="">Select LE</option>
                                                          <?php $tbl_near_vision =  DB::table("tbl_near_vision")->get();  ?>
                                                           @foreach($tbl_near_vision as $tbl_near_vision)
                                                          <option value="{{$tbl_near_vision->near_vo}}" {{ $Eyetest->le_near == $tbl_near_vision->near_vo ? 'selected' : '' }}>{{$tbl_near_vision->near_vo}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="le_nearError"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="button-row d-flex mt-4">
                                        <button class="btn btn-success" id="step2" type="button">Okay, Go to Next Step</button>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Torch Light Examination - Slit Lamp</h5>
                                             <img src="{{asset('assets/tourch.png')}}" alt="user-img" class="avatar-xl mb-1"> </a>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Select anyone puplilary response: <span class="text-danger">*</span></label>
                                                        <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="torch_light" id="inlineRadio3" value="Normal" {{ $Eyetest->torch_light == 'Normal' ? 'checked' : '' }}>
                                                          <label class="form-check-label" for="inlineRadio3">Normal</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="torch_light" id="inlineRadio4" value="Not Normal" {{ $Eyetest->torch_light == 'Not Normal' ? 'checked' : '' }}>
                                                          <label class="form-check-label" for="inlineRadio4">Not Normal</label>
                                                        </div>
                                                        
                                                        <span class="error badge text-danger" id="torch_lightError"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" id="torchdiv" @if($Eyetest->torch_light == 'Normal') style="display:none" @endif>
                                                     <label for="" class="form-label">Enter reason for not normal</label>
                                                     <input type="text" class="form-control" id="reason_torch" name="reason_torch" value="{{$Eyetest->reason_torch}}">
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Cover - Uncover Test</h5>
                                            <img src="{{asset('assets/Cover.png')}}" alt="user-img" class="avatar-xl mb-1"> </a>
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Select anyone puplilary response: <span class="text-danger">*</span></label>
                                                        <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="cover_uncover" id="inlineRadio3" value="Normal" {{ $Eyetest->cover_uncover == 'Normal' ? 'checked' : '' }}>
                                                          <label class="form-check-label" for="inlineRadio3">Normal</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="cover_uncover" id="inlineRadio4" value="Not Normal" {{ $Eyetest->cover_uncover == 'Not Normal' ? 'checked' : '' }}>
                                                          <label class="form-check-label" for="inlineRadio4">Not Normal</label>
                                                        </div>
                                                        
                                                        <span class="error badge text-danger" id="cover_uncoverError"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" id="cover_uncoverdiv" @if($Eyetest->cover_uncover == 'Normal') style="display:none" @endif>
                                                     <label for="" class="form-label">Enter reason for not normal</label>
                                                     <input type="text" class="form-control" id="reason_cover_uncover" name="reason_cover_uncover" value="{{$Eyetest->reason_cover_uncover}}">
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Convergence Test</h5>
                                            <img src="{{asset('assets/convergence.png')}}" alt="user-img" class="avatar-xl mb-1"> </a>
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Select anyone puplilary response: <span class="text-danger">*</span></label>
                                                        <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="convergence" id="inlineRadio3" value="Normal" {{ $Eyetest->convergence == 'Normal' ? 'checked' : '' }}>
                                                          <label class="form-check-label" for="inlineRadio3">Normal</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="convergence" id="inlineRadio4" value="Not Normal" {{ $Eyetest->convergence == 'Not Normal' ? 'checked' : '' }}>
                                                          <label class="form-check-label" for="inlineRadio4">Not Normal</label>
                                                        </div>
                                                        <span class="error badge text-danger" id="convergenceError"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" id="convergencediv" @if($Eyetest->convergence == 'Normal') style="display:none" @endif>
                                                    <label for="" class="form-label">Enter reason for not normal</label>
                                                    <input type="text" class="form-control" id="reason_convergence" name="reason_convergence" value="{{$Eyetest->reason_convergence}}">
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5>Phoropter / Trial Frame Adjustment</h5>
                                            <img src="{{asset('assets/phoropter.png')}}" alt="user-img" class="avatar-xl mb-1"> </a>
                                            <p>Adjust the trail frame according to the customer's PD</p>
                                            
                                        </div>
                                         <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="" class="form-label">Right Eye (RE): <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"  value="{{$Eyetest->right_eye}}" id="right_eye" name="right_eye" >
                                                <span class="error badge text-danger" id="right_eyeError"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="" class="form-label">Left Eye (LE): <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="{{$Eyetest->left_eys}}" id="left_eys" name="left_eys" >
                                                <span class="error badge text-danger" id="left_eysError"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="" class="form-label">Both: <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="{{$Eyetest->both_eyes}}"  id="both_eyes" name="both_eyes" >
                                                <span class="error badge text-danger" id="both_eyesError"></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="button-row d-flex mt-4">
                                        <button class="btn btn-success" id="step3" type="button">Okay, Go to Next Step</button>
                                    </div>
                                    
                                </div>
                                <div class="tab-pane" id="tab4">
                                    <h5>Subjective Refraction</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="" class="form-label">Right Eye (RE): <span class="text-danger">*</span></label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="re_green_red" id="inlineRadio5" value="Red Better" {{ $Eyetest->re_green_red == 'Red Better' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio5">Red Better</label>
                                                </div>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="re_green_red" id="inlineRadio6" value="Balanced" {{ $Eyetest->re_green_red == 'Balanced' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio6">Balanced</label>
                                                </div>
                                                 <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="re_green_red" id="inlineRadio7" value="Green Better" {{ $Eyetest->re_green_red == 'Green Better' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio7">Green Better</label>
                                                </div>
                                                <span class="error badge text-danger" id="re_green_redError"></span>
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <label for="" class="form-label">Left Eye (LE): <span class="text-danger">*</span></label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="le_green_red" id="inlineRadio8" value="Red Better" {{ $Eyetest->le_green_red == 'Red Better' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio8">Red Better</label>
                                                </div>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="le_green_red" id="inlineRadio9" value="Balanced" {{ $Eyetest->le_green_red == 'Balanced' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio9">Balanced</label>
                                                </div>
                                                 <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="le_green_red" id="inlineRadio10" value="Green Better" {{ $Eyetest->le_green_red == 'Green Better' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio10">Green Better</label>
                                                </div>
                                                <span class="error badge text-danger" id="le_green_redError"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="" class="form-label">Right Eye (RE): <span class="text-danger">*</span></label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="re_refined" id="inlineRadio11" value="Refined" {{ $Eyetest->re_refined == 'Refined' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio11">Refined</label>
                                                </div>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="re_refined" id="inlineRadio12" value="Not Refined" {{ $Eyetest->re_refined == 'Not Refined' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio12">Not Refined</label>
                                                </div>
                                                <span class="error badge text-danger" id="re_refinedError"></span>
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <label for="" class="form-label">Left Eye (LE): <span class="text-danger">*</span></label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="le_refined" id="inlineRadio13" value="Refined" {{ $Eyetest->le_refined == 'Refined' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio13">Refined</label>
                                                </div>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="le_refined" id="inlineRadio14" value="Not Refined" {{ $Eyetest->le_refined == 'Not Refined' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio14">Not Refined</label>
                                                </div>
                                                <span class="error badge text-danger" id="le_refinedError"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="" class="form-label">Right Eye (RE): <span class="text-danger">*</span></label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="re_balanced" id="inlineRadio15" value="Balanced" {{ $Eyetest->re_balanced == 'Balanced' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio15">Balanced</label>
                                                </div>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="re_balanced" id="inlineRadio16" value="Not Balanced" {{ $Eyetest->re_balanced == 'Not Balanced' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio16">Not Balanced</label>
                                                </div>
                                                <span class="error badge text-danger" id="re_balancedError"></span>
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <label for="" class="form-label">Left Eye (RE): <span class="text-danger">*</span></label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="le_balanced" id="inlineRadio17" value="Balanced" {{ $Eyetest->le_balanced == 'Balanced' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio17">Balanced</label>
                                                </div>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="le_balanced" id="inlineRadio18" value="Not Balanced" {{ $Eyetest->le_balanced == 'Not Balanced' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio18">Not Balanced</label>
                                                </div>
                                                <span class="error badge text-danger" id="le_balancedError"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="button-row d-flex mt-4">
                                        <button class="btn btn-success" id="step4" type="button">Okay, Go to Next Step</button>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab5">
                                    <h5>Near Vision Test</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p>Give the test bokklet to customer and ask them to read from it at their usual reading distance. </p>
                                            <p>Type with English numnber and their preferred language.</p>
                                            <br>
                                            <img src="{{asset('assets/reading.png')}}" alt="user-img" class="avatar-xl mb-1" style="width:200px;height:150px"> </a>
                                        </div>
                                        <div class="col-md-6">
                                            <p>Check if customer has Additional Power(AP) in their right & left eyes </p>
                                             <div class="form-group">
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="additional_power" id="inlineRadio19" value="Yes" {{ $Eyetest->additional_power == 'Yes' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio19">Yes</label>
                                                </div>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" name="additional_power" id="inlineRadio20" value="No" {{ $Eyetest->additional_power == 'No' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio20">No</label>
                                                </div>
                                                <span class="error badge text-danger" id="additional_powerError"></span>
                                            </div>
                                                <br>
                                            <div class="form-group"  id="additionaldiv" @if($Eyetest->additional_power == 'No') style="display:none" @endif>
                                                <label class="form-label">Addition Power (AP): <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select class="form-control select" style="height: 32px !important;" id="re_ap" name="re_ap">
                                                             <option value="">Select RE</option>
                                                          <?php $tbl_near_vision =  DB::table("tbl_near_vision")->get();  ?>
                                                           @foreach($tbl_near_vision as $tbl_near_vision)
                                                          <option value="{{$tbl_near_vision->near_vo}}" {{ $Eyetest->re_ap == $tbl_near_vision->near_vo ? 'selected' : '' }}>{{$tbl_near_vision->near_vo}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="re_apError"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control select" style="height: 32px !important;" id="le_ap" name="le_ap">
                                                           <option value="">Select LE</option>
                                                          <?php $tbl_near_vision =  DB::table("tbl_near_vision")->get();  ?>
                                                           @foreach($tbl_near_vision as $tbl_near_vision)
                                                          <option value="{{$tbl_near_vision->near_vo}}" {{ $Eyetest->le_ap == $tbl_near_vision->near_vo ? 'selected' : '' }}>{{$tbl_near_vision->near_vo}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="le_apError"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4">
                                                    <button class="btn btn-success" id="step5" type="button">Okay, Go to Next Step</button>
                                            </div>
                                        </div>    
                                    </div>
                                </div> 
                                <div class="tab-pane" id="tab6">
                                    <h5>New Prescription Verification</h5>
                                    <p>Instruct Customer to look at diffrent distances outside the clinic and  check for new new prescription accepance</p>
                                    <br>
                                    <textarea class="form-control" placeholder="Enter remarks (Optional) " id="p_verify_remark" name="p_verify_remark">{{$Eyetest->p_verify_remark}}</textarea>
                                    
                                    <div class="button-row d-flex mt-4">
                                            <button class="btn btn-success" id="step6" type="button">Okay, Go to Next Step</button>
                                    </div>
                                </div> 
                                <div class="tab-pane" id="tab7">
                                    <h5>Enter New Power</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Single Vision</h5>
                                            <div class="table-responsive">
                                                <table class="table card-table table-vcenter text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th style="color: #000;">SPH</th>
                                                            <th style="color: #000;">CYL</th>
                                                            <th style="color: #000;">AXIS</th>
                                                            <th style="color: #000;">PD</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">RE</th>
                                                            <td>
                                                                <select class="form-control select" name="re_sph_new" id="re_sph_new">
                                                                    <option value="">Select</option>
                                                                    @foreach ($sph_values as $sph)
                                                                        <option value="{{ $sph }}" {{ $Eyetest->re_sph_new == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select" name="re_cyl_new" id="re_cyl_new">
                                                                    <option value="">Select</option>
                                                                    @foreach ($sph_values as $sph)
                                                                        <option value="{{ $sph }}" {{ $Eyetest->re_cyl_new == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select" name="re_axis_new" id="re_axis_new">
                                                                    <option value="">Select</option>
                                                                    @for ($i = 1; $i <= 50; $i++)
                                                                        <option value="{{ $i }}" {{ $Eyetest->re_axis_new == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="pd_re_new" id="pd_re_new" class="form-control select">
                                                                    <option value="">Select</option>
                                                                    @for ($i = 25; $i <= 40; $i++)
                                                                        <option value="{{ $i }}" {{ $Eyetest->pd_re_new == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">LE</th>
                                                            <td>
                                                                <select class="form-control select" name="le_sph_new" id="le_sph_new">
                                                                    <option value="">Select</option>
                                                                    @foreach ($sph_values as $sph)
                                                                        <option value="{{ $sph }}" {{ $Eyetest->le_sph_new == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select" name="le_cyl_new" id="le_cyl_new">
                                                                    <option value="">Select</option>
                                                                    @foreach ($sph_values as $sph)
                                                                        <option value="{{ $sph }}" {{ $Eyetest->le_cyl_new == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select" name="le_axis_new" id="le_axis_new">
                                                                    <option value="">Select</option>
                                                                    @for ($i = 1; $i <= 50; $i++)
                                                                        <option value="{{ $i }}" {{ $Eyetest->le_axis_new == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="pd_le_new" id="pd_le_new" class="form-control select">
                                                                    <option value="">Select</option>
                                                                    @for ($i = 25; $i <= 40; $i++)
                                                                        <option value="{{ $i }}" {{ $Eyetest->pd_le_new == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Bifocal/Progresive</h5>
                                            <div class="table-responsive">
                                                <table class="table card-table table-vcenter text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th style="color: #000;">SPH</th>
                                                            <th style="color: #000;">CYL</th>
                                                            <th style="color: #000;">AXIS</th>
                                                            <th style="color: #000;">PD</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">RE</th>
                                                            <td>
                                                                <select class="form-control select" name="re_sph_bif" id="re_sph_bif">
                                                                    <option value="">Select</option>
                                                                    @foreach ($sph_values as $sph)
                                                                        <option value="{{ $sph }}" {{ $Eyetest->re_sph_bif == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select" name="re_cyl_bif" id="re_cyl_bif">
                                                                    <option value="">Select</option>
                                                                    @foreach ($sph_values as $sph)
                                                                        <option value="{{ $sph }}" {{ $Eyetest->re_cyl_bif == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select" name="re_axis_bif" id="re_axis_bif">
                                                                    <option value="">Select</option>
                                                                    @for ($i = 1; $i <= 50; $i++)
                                                                        <option value="{{ $i }}" {{ $Eyetest->re_axis_bif == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="pd_re_bif" id="pd_re_bif" class="form-control select">
                                                                    <option value="">Select</option>
                                                                    @for ($i = 25; $i <= 40; $i++)
                                                                        <option value="{{ $i }}" {{ $Eyetest->pd_re_bif == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">LE</th>
                                                            <td>
                                                                <select class="form-control select" name="le_sph_bif" id="le_sph_bif">
                                                                    <option value="">Select</option>
                                                                    @foreach ($sph_values as $sph)
                                                                        <option value="{{ $sph }}" {{ $Eyetest->le_sph_bif == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select" name="le_cyl_bif" id="le_cyl_bif">
                                                                    <option value="">Select</option>
                                                                    @foreach ($sph_values as $sph)
                                                                        <option value="{{ $sph }}" {{ $Eyetest->le_cyl_bif == $sph ? 'selected' : '' }}>{{ $sph }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select" name="le_axis_bif" id="le_axis_bif">
                                                                    <option value="">Select</option>
                                                                    @for ($i = 1; $i <= 50; $i++)
                                                                        <option value="{{ $i }}" {{ $Eyetest->le_axis_bif == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="pd_le_bif" id="pd_le_bif" class="form-control select">
                                                                    <option value="">Select</option>
                                                                    @for ($i = 25; $i <= 40; $i++)
                                                                        <option value="{{ $i }}" {{ $Eyetest->pd_le_bif == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div calss="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="" class="form-label">Distance Vision: <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                         <select class="form-control select" style="height: 32px !important;" id="re_distance_new" name="re_distance_new">
                                                             <option value="">Select RE</option>
                                                          <?php $tbl_visual_acuity =  DB::table("tbl_distance_vision")->get();  ?>
                                                           @foreach($tbl_visual_acuity as $tbl_visual_acuity)
                                                          <option value="{{$tbl_visual_acuity->visual_no}}" {{ $Eyetest->re_distance_new == $tbl_visual_acuity->visual_no ? 'selected' : '' }}>{{$tbl_visual_acuity->visual_no}}</option>
                                                          @endforeach
                                                        </select>
                                                        <span class="error badge text-danger" id="re_distance_newError"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control select" style="height: 32px !important;" id="le_distance_new" name="le_distance_new">
                                                             <option value="">Select LE</option>
                                                          <?php $tbl_visual_acuity =  DB::table("tbl_distance_vision")->get();  ?>
                                                           @foreach($tbl_visual_acuity as $tbl_visual_acuity)
                                                          <option value="{{$tbl_visual_acuity->visual_no}}" {{ $Eyetest->le_distance_new == $tbl_visual_acuity->visual_no ? 'selected' : '' }}>{{$tbl_visual_acuity->visual_no}}</option>
                                                          @endforeach
                                                        </select>
                                                        <span class="error badge text-danger" id="le_distance_newError"></span>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="" class="form-label">Near Vision: <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                         <select class="form-control select" style="height: 32px !important;" id="re_near_new" name="re_near_new">
                                                             <option value="">Select RE</option>
                                                          <?php $tbl_near_vision =  DB::table("tbl_near_vision")->get();  ?>
                                                           @foreach($tbl_near_vision as $tbl_near_vision)
                                                          <option value="{{$tbl_near_vision->near_vo}}" {{ $Eyetest->re_near_new == $tbl_near_vision->near_vo ? 'selected' : '' }}>{{$tbl_near_vision->near_vo}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="re_near_newError"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control select" style="height: 32px !important;" id="le_near_new" name="le_near_new">
                                                             <option value="">Select LE</option>
                                                          <?php $tbl_near_vision =  DB::table("tbl_near_vision")->get();  ?>
                                                           @foreach($tbl_near_vision as $tbl_near_vision)
                                                          <option value="{{$tbl_near_vision->near_vo}}" {{ $Eyetest->le_near_new == $tbl_near_vision->near_vo ? 'selected' : '' }}>{{$tbl_near_vision->near_vo}}</option>
                                                          @endforeach
                                                        </select>
                                                         <span class="error badge text-danger" id="le_near_newError"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="button-row d-flex mt-4">
                                            <button class="btn btn-success" id="step7" type="button">Okay, Go to Next Step</button>
                                    </div>
                                </div>  
                                <div class="tab-pane" id="tab8">
                                    <h5>Optom Recommendations</h5>
                                    <div class="row">
                                        <p>Try Diffrent Frame sizes on customer and suggest the best fit.</p>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="frame_size" id="inlineRadio21" value="Extra Narrow" {{ $Eyetest->frame_size == 'Extra Narrow' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio21">Extra Narrow</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="frame_size" id="inlineRadio22" value="Narrow" {{ $Eyetest->frame_size == 'Narrow' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio22">Narrow</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="frame_size" id="inlineRadio23" value="Medium" {{ $Eyetest->frame_size == 'Medium' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio23">Medium</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="frame_size" id="inlineRadio24" value="Wide" {{ $Eyetest->frame_size == 'Wide' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio24">Wide</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="frame_size" id="inlineRadio25" value="Extra Wide" {{ $Eyetest->frame_size == 'Extra Wide' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio25">Extra Wide</label>
                                                </div>
                                                <span class="error badge text-danger" id="frame_sizeError"></span>
                                            </div>
                                        </div>
                                        <br>
                                        <p>Followup Eye Test Date</p>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="followup_date" id="inlineRadio26" value="6" {{ $Eyetest->followup_date == '6' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio26">After 6 Month</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="followup_date" id="inlineRadio27" value="9" {{ $Eyetest->followup_date == '9' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio27">After 9 Month</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="followup_date" id="inlineRadio28" value="12" {{ $Eyetest->followup_date == '12' ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="inlineRadio28">After 1 Year</label>
                                                </div>
                                                
                                                <span class="error badge text-danger" id="followup_dateError"></span>
                                            </div>
                                        </div>
                                         
                                    </div>
                                    <div class="button-row d-flex mt-4">
                                        <button class="btn btn-success" id="step8" type="button">Final Submit</button>
                                    </div>
                                </div> 
                                <div class="tab-pane" id="tab9">
                                    <h5>Eye Test Summary</h5>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5>Single Vision Power</h5>
                                            <div class="table-responsive">
                                                <table class="table card-table table-vcenter text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th style="color: #000;">Rx</th>
                                                            <th style="color: #000;">SPH</th>
                                                            <th style="color: #000;">CYL</th>
                                                            <th style="color: #000;">AXIS</th>
                                                            <th style="color: #000;">PD</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">RE</th>
                                                            <td>{{$Eyetest->re_sph_new}}</td>
                                                            <td>{{$Eyetest->re_cyl_new}}</td>
                                                            <td>{{$Eyetest->re_axis_new}}</td>
                                                            <td>{{$Eyetest->pd_re_new}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">LE</th>
                                                            <td>{{$Eyetest->le_sph_new}}</td>
                                                            <td>{{$Eyetest->le_cyl_new}} </td>
                                                            <td>{{$Eyetest->le_axis_new}}</td>
                                                            <td>{{$Eyetest->pd_le_new}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="card bg-dark">
                                                <div class="card-body">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h6 class="text-white"><strong>Distance Vision</strong></h6>
                                                            <h6 class="text-white">{{$Eyetest->re_near_new }} Right Eye(RE)</h6>
                                                            <h6 class="text-white">{{$Eyetest->le_near_new }} Right Eye(RE)</h6>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-2">
                                             <div class="card bg-dark">
                                                <div class="card-body">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h6 class="text-white"><strong>Distance Vision</strong></h6>
                                                            <h6 class="text-white">{{$Eyetest->re_distance_new }} Right Eye(RE)</h6>
                                                            <h6 class="text-white">{{$Eyetest->le_distance_new }} Right Eye(RE)</h6>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                             <div class="card bg-dark">
                                                <div class="card-body">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h6 class="text-white"><strong>Distance Vision</strong></h6>
                                                            <h6 class="text-white">{{$Eyetest->re_distance }} Right Eye(RE)</h6>
                                                            <h6 class="text-white">{{$Eyetest->le_distance }} Right Eye(RE)</h6>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                             <div class="card bg-dark">
                                                <div class="card-body">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h6 class="text-white"><strong>Pinhole Vision</strong></h6>
                                                            <h6 class="text-white">{{$Eyetest->re_pinhole }} Right Eye(RE)</h6>
                                                            <h6 class="text-white">{{$Eyetest->le_pinhole }} Right Eye(RE)</h6>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                             <div class="card bg-dark">
                                                <div class="card-body">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h6 class="text-white"><strong>Near Vision</strong></h6>
                                                            <h6 class="text-white">{{$Eyetest->re_near }} Right Eye(RE)</h6>
                                                            <h6 class="text-white">{{$Eyetest->le_near }} Right Eye(RE)</h6>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5>AR Power</h5>
                                            <div class="table-responsive">
                                                <table class="table card-table table-vcenter text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th style="color:#000">Rx</th>
                                                            <th style="color:#000">SPH</th>
                                                            <th style="color:#000">CYL</th>
                                                            <th style="color:#000">AXIS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">RE</th>
                                                            <td>{{ $Eyetest->re_sph }}</td>
                                                            <td>{{ $Eyetest->re_cyl }}</td>
                                                            <td>{{ $Eyetest->re_axis }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">LE</th>
                                                            <td>{{ $Eyetest->le_sph }}</td>
                                                            <td>{{ $Eyetest->le_cyl }}</td>
                                                            <td>{{ $Eyetest->le_axis }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h5>PD Deatils</h5>
                                            <div class="card bg-dark" style="color:#fff">
                                                <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-3">RE</div>
                                                            <div class="col-md-1"></div>
                                                            <div class="col-md-3">LE</div>
                                                            <div class="col-md-1"></div>
                                                            <div class="col-md-3">BE</div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">{{$Eyetest->right_eye }}</div>
                                                            <div class="col-md-1">+</div>
                                                            <div class="col-md-3">{{$Eyetest->left_eys }}</div>
                                                            <div class="col-md-1">=</div>
                                                            <div class="col-md-3">{{$Eyetest->both_eyes }}</div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-dark">
                                                <div class="card-body">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h6 class="text-white">Torch Light</h6>
                                                        </div>
                                                        <div class="ml-auto">
                                                            @if($Eyetest->torch_light == 'Normal')
                                                            <span class="text-white display-6"><i class="fa fa-thumbs-up"></i></span>
                                                            @else
                                                            <span class="text-white display-6"><i class="fa fa-thumbs-down"></i></span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-dark">
                                                <div class="card-body">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h6 class="text-white">Cover-Uncover</h6>
                                                        </div>
                                                        <div class="ml-auto">
                                                            @if($Eyetest->cover_uncover == 'Normal')
                                                            <span class="text-white display-6"><i class="fa fa-thumbs-up"></i></span>
                                                            @else
                                                            <span class="text-white display-6"><i class="fa fa-thumbs-down"></i></span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-4">
                                            <div class="card bg-dark">
                                                <div class="card-body">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h6 class="text-white">Convergence</h6>
                                                        </div>
                                                        <div class="ml-auto">
                                                            @if($Eyetest->convergence == 'Normal')
                                                            <span class="text-white display-6"><i class="fa fa-thumbs-up"></i></span>
                                                            @else
                                                            <span class="text-white display-6"><i class="fa fa-thumbs-down"></i></span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h5>Duochome</h5>
                                            <div class="form-group">
                                                <label for="" class="form-label">Right Eye (RE): </label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio"  checked>
                                                  <label class="form-check-label"> {{ $Eyetest->re_green_red }}</label>
                                                </div>
                                                
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <label for="" class="form-label">Left Eye (LE):</label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" checked>
                                                  <label class="form-check-label">{{ $Eyetest->le_green_red }}</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h5>JCC</h5>
                                            <div class="form-group">
                                                <label for="" class="form-label">Right Eye (RE):</label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" checked>
                                                  <label class="form-check-label">{{ $Eyetest->re_refined }}</label>
                                                </div>

                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <label for="" class="form-label">Left Eye (LE):</label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" checked>
                                                  <label class="form-check-label">{{ $Eyetest->le_refined }}</label>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h5>Binocular Balance Test</h5>
                                            <div class="form-group">
                                                <label for="" class="form-label">Right Eye (RE):</label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" checked>
                                                  <label class="form-check-label">{{ $Eyetest->re_balanced }}</label>
                                                </div>
                                                
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <label for="" class="form-label">Left Eye (RE):</label>
                                                <div class="form-check form-check">
                                                  <input class="form-check-input" type="radio" checked>
                                                  <label class="form-check-label">{{ $Eyetest->le_balanced }}</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                     <div class="button-row d-flex mt-4">
                                        <button class="btn btn-success" id="step9" type="button">Send Otp to Verify</button>
                                    </div>  
                                </div>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" data-backdrop="static" id="otpModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">GET PRESCRIPTION</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="verifyOtpForm" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="otp_mobile" id="otp_mobile">
          <input type="hidden" name="otp_test_id" id="otp_test_id">
          <div id="otp-section" style="display: none;">
            <div class="form-group">
              <strong>OTP: <span class="text-danger">*</span></strong>
              <input type="text" class="form-control @error('sotp') is-invalid @enderror"
                     id="sotp" maxlength="4" name="sotp" placeholder="Enter OTP Here"
                     value="{{ old('sotp') }}">
                     <span class="error badge text-danger" id="sotpError"></span>
            </div>

            <div class="d-flex justify-content-between pt-2">
              <p id="timer" style="margin-top:5px;font-size: 14px;">
                Resend OTP in <span id="countdown">60</span>s
              </p>
              <button id="resend-btn" type="button" class="btn btn-primary"
                      onclick="resendOTP()" disabled
                      style="font-size: 12px; padding: 4px 14px; height: auto;">
                Resend OTP
              </button>
            </div>
          </div>

          <div class="button-row d-flex mt-4">
            <button type="submit" class="btn btn-gradient js-btn-next">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


@endsection

@section('scripts')
<script>
function cb(date)
{
    $('#reportrange span').html(date.format('MMMM D, YYYY'));
    $('#date_from').val(date.format('YYYY-MM-DD'));
}

const today = moment();

$('#reportrange').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,  
    autoApply: false,        
    startDate: today,
    maxDate: today,
    locale: {
        format: 'MMMM D, YYYY',
        applyLabel: "Apply",
        cancelLabel: "Clear"
    }
});

$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
    cb(picker.startDate);     
    dataListView.draw();      
});

$('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
    $('#reportrange span').html('');
    $('#date_from').val('');
});


$(document).ready(function() {
    $('.select').select2({
      allowClear: true,
      width:'100%'
    });
  });
$(document).ready(function () 
{
    $("#step1").click(function (e) {
        e.preventDefault();
        let isValid = true;

        $(".error").text("");
        $(".is-invalid").removeClass("is-invalid");

        let optometrist = $("#Optometrist").val().trim();
        if (optometrist === "") {
            $("#OptometristError").text("Please enter the optometrist name");
            $("#Optometrist").addClass("is-invalid");
            isValid = false;
        }

        let fields = ["#re_sph", "#re_cyl", "#re_axis", "#le_sph", "#le_cyl", "#le_axis"];
        fields.forEach(function (fieldId) {
            if ($(fieldId).val() === "") {
                $(fieldId).addClass("is-invalid");
                isValid = false;
            }
        });

        if (isValid) 
        {
            let formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                optometrist: optometrist,
                re_sph: $("#re_sph").val(),
                re_cyl: $("#re_cyl").val(),
                re_axis: $("#re_axis").val(),
                le_sph: $("#le_sph").val(),
                le_cyl: $("#le_cyl").val(),
                le_axis: $("#le_axis").val(),
                test_id: "{{ $Eyetest->test_id }}" 
            };
            
            $.ajax({
                url: "{{ route('admin.eyetest.step1.update') }}", 
                method: "POST",
                data: formData,
                beforeSend: function () {
                    $("#step1").prop("disabled", true).text("Saving...");
                },
                success: function (response) {
                    if (response.success) 
                    {
                        $('.nav a[href="#tab2"]').tab('show');
                    }
                    else 
                    {
                         $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'Failed to save data. Please try again.'
                        });
                    }
                },
                error: function (xhr) 
                {
                     $.toaster({
                        priority: 'danger',
                        title: 'Error',
                        message: 'Error occurred while saving data.'
                    });
                },
                complete: function () 
                {
                    $("#step1").prop("disabled", false).text("Okay, Go to Next Step");
                }
            });
            
        }
    });
    
    $("#step2").click(function (e) {
        e.preventDefault();
        let isValid = true;
    
        $(".error").text("");
        $(".is-invalid").removeClass("is-invalid");
    
        let test_status = $('input[name="test_status"]:checked').val() || "";
        if (test_status === "") {
            $("#test_statusError").text("Please select test status");
            isValid = false;
        }
    
        const fields = [
            { id: "#re_distance", errorId: "#re_distanceError", label: "Right Eye Distance Vision" },
            { id: "#le_distance", errorId: "#le_distanceError", label: "Left Eye Distance Vision" },
            { id: "#re_pinhole", errorId: "#re_pinholeError", label: "Right Eye Pinhole Vision" },
            { id: "#le_pinhole", errorId: "#le_pinholeError", label: "Left Eye Pinhole Vision" },
            { id: "#re_near", errorId: "#re_nearError", label: "Right Eye Near Vision" },
            { id: "#le_near", errorId: "#le_nearError", label: "Left Eye Near Vision" },
        ];
    
        fields.forEach(function (field) {
            if ($(field.id).val() === "") {
                $(field.id).addClass("is-invalid");
                $(field.errorId).text("Please select " + field.label);
                isValid = false;
            }
        });
    
        if (isValid)
        {
            const data = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                test_status: test_status,
                re_distance: $("#re_distance").val(),
                le_distance: $("#le_distance").val(),
                re_pinhole: $("#re_pinhole").val(),
                le_pinhole: $("#le_pinhole").val(),
                re_near: $("#re_near").val(),
                le_near: $("#le_near").val(),
                last_eye_test_date: $("#date_from").val(),
                test_id: "{{ $Eyetest->test_id }}", 
            };
    
            $.ajax({
                url: "{{ route('admin.eyetest.step2.update') }}",
                type: "POST",
                data: data,
                beforeSend: function () {
                    $("#step2").prop("disabled", true).text("Saving...");
                },
                success: function (response) {
                    if (response.success) {
                        $('.nav a[href="#tab3"]').tab('show');
                    } else {
                        $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'Failed to save data. Please try again.'
                        });
                    }
                },
                error: function () {
                    $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'An error occurred while saving step 2.'
                        });
                },
                complete: function () {
                    $("#step2").prop("disabled", false).text("Okay, Go to Next Step");
                }
            });
            
        }
    });
    
    $('input[name="torch_light"]').on('change', function () {
        if ($(this).val() === 'Not Normal') {
            $('#torchdiv').show();
        } else {
            $('#torchdiv').hide();
            $('#reason_torch').val('');
        }
    });
    
    $('input[name="cover_uncover"]').on('change', function () {
        if ($(this).val() === 'Not Normal') {
            $('#cover_uncoverdiv').show();
        } else {
            $('#cover_uncoverdiv').hide();
            $('#reason_cover_uncover').val('');
        }
    });
    
    $('input[name="convergence"]').on('change', function () {
        if ($(this).val() === 'Not Normal') {
            $('#convergencediv').show();
        } else {
            $('#convergencediv').hide();
            $('#reason_convergence').val('');
        }
    });
    
    $("#step3").click(function () {
        let isValid = true;
    
        $(".error").text("");
    
        let torchVal = $("input[name='torch_light']:checked").val();
        if (!torchVal) {
            $("#torch_lightError").text("Please select a response.");
            isValid = false;
        } else if (torchVal === "Not Normal" && $("#reason_torch").val().trim() === "") {
            $("#torch_lightError").text("Please enter a reason for 'Not Normal'.");
            isValid = false;
        }
    
        let coverVal = $("input[name='cover_uncover']:checked").val();
        if (!coverVal) {
            $("#cover_uncoverError").text("Please select a response.");
            isValid = false;
        } else if (coverVal === "Not Normal" && $("#reason_cover_uncover").val().trim() === "") {
            $("#cover_uncoverError").text("Please enter a reason for 'Not Normal'.");
            isValid = false;
        }
    
        let convergenceVal = $("input[name='convergence']:checked").val();
        if (!convergenceVal) {
            $("#convergenceError").text("Please select a response.");
            isValid = false;
        } else if (convergenceVal === "Not Normal" && $("#reason_convergence").val().trim() === "") {
            $("#convergenceError").text("Please enter a reason for 'Not Normal'.");
            isValid = false;
        }
    
        if ($("#right_eye").val().trim() === "") {
            $("#right_eyeError").text("Please enter right eye value.");
            isValid = false;
        }
        if ($("#left_eys").val().trim() === "") {
            $("#left_eysError").text("Please enter left eye value.");
            isValid = false;
        }
        if ($("#both_eyes").val().trim() === "") {
            $("#both_eyesError").text("Please enter both eyes value.");
            isValid = false;
        }
    
        if (isValid) {
            $.ajax({
                url: "{{ route('admin.eyetest.step3.update') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    torch_light: torchVal,
                    reason_torch: $("#reason_torch").val(),
                    cover_uncover: coverVal,
                    reason_cover_uncover: $("#reason_cover_uncover").val(),
                    convergence: convergenceVal,
                    reason_convergence: $("#reason_convergence").val(),
                    right_eye: $("#right_eye").val(),
                    left_eys: $("#left_eys").val(),
                    both_eyes: $("#both_eyes").val(),
                    test_id: "{{ $Eyetest->test_id }}", 
                },
                beforeSend: function () {
                    $("#step3").prop("disabled", true).text("Saving...");
                },
                success: function (response) {
                    if (response.success) {
                        $('.nav a[href="#tab4"]').tab('show');
                    } else {
                        $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'Failed to save data. Please try again.'
                        });
                    }
                },
                error: function () {
                    $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'An error occurred while saving step 3.'
                        });
                },
                complete: function () {
                    $("#step3").prop("disabled", false).text("Okay, Go to Next Step");
                }
            });
        }
    });

    $("#step4").click(function () {
        let isValid = true;
    
        $(".error").text("");
    
        const fields = [
            { name: "re_green_red", errorId: "#re_green_redError" },
            { name: "le_green_red", errorId: "#le_green_redError" },
            { name: "re_refined", errorId: "#re_refinedError" },
            { name: "le_refined", errorId: "#le_refinedError" },
            { name: "re_balanced", errorId: "#re_balancedError" },
            { name: "le_balanced", errorId: "#le_balancedError" }
        ];
    
        fields.forEach(field => {
            if ($(`input[name='${field.name}']:checked`).length === 0) {
                $(field.errorId).text("This field is required.");
                isValid = false;
            }
        });
    
        if (isValid) {
            $.ajax({
                url: "{{ route('admin.eyetest.step4.update') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    re_green_red: $("input[name='re_green_red']:checked").val(),
                    le_green_red: $("input[name='le_green_red']:checked").val(),
                    re_refined: $("input[name='re_refined']:checked").val(),
                    le_refined: $("input[name='le_refined']:checked").val(),
                    re_balanced: $("input[name='re_balanced']:checked").val(),
                    le_balanced: $("input[name='le_balanced']:checked").val(),
                    test_id: "{{ $Eyetest->test_id }}", 
                },
                beforeSend: function () {
                    $("#step4").prop("disabled", true).text("Saving...");
                },
                success: function (response) {
                    if (response.success) {
                        $('.nav a[href="#tab5"]').tab('show');
                    } else {
                        $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'Failed to save data. Please try again.'
                        });
                    }
                },
                error: function () {
                    $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'An error occurred while saving step 4.'
                        });
                },
                complete: function () {
                    $("#step4").prop("disabled", false).text("Okay, Go to Next Step");
                }
            });
        }
    }); 
    
    $("input[name='additional_power']").change(function () {
            let selected = $("input[name='additional_power']:checked").val();
            if (selected === "Yes") {
                $("#additionaldiv").show();
            } else {
                $("#additionaldiv").hide();
                $("#re_ap, #le_ap").val("");
                $("#re_apError, #le_apError").text("");
            }
        });

    $("#step5").click(function () {
        let isValid = true;
        $(".error").text("");

        const apSelected = $("input[name='additional_power']:checked").val();
        if (!apSelected) {
            $("#additional_powerError").text("Please select Yes or No.");
            isValid = false;
        }

        if (apSelected === "Yes") {
            if ($("#re_ap").val() === "") {
                $("#re_apError").text("Please select value for RE.");
                isValid = false;
            }
            if ($("#le_ap").val() === "") {
                $("#le_apError").text("Please select value for LE.");
                isValid = false;
            }
        }

        if (isValid)
        {
            $.ajax({
                url: "{{ route('admin.eyetest.step5.update') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    additional_power: apSelected,
                    re_ap: $("#re_ap").val(),
                    le_ap: $("#le_ap").val(),
                    test_id: "{{ $Eyetest->test_id }}", 
                },
                beforeSend: function () {
                    $("#step5").prop("disabled", true).text("Saving...");
                },
                success: function (response) {
                    if (response.success) {
                        $('.nav a[href="#tab6"]').tab('show');
                    } else {
                        $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'Failed to save data. Please try again.'
                        });
                    }
                },
                error: function () {
                    $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'An error occurred while saving step 5.'
                        });
                },
                complete: function () {
                    $("#step5").prop("disabled", false).text("Okay, Go to Next Step");
                }
            });
        }
    });

    $("#step6").click(function ()
    {
        $.ajax({
            url: "{{ route('admin.eyetest.step6.update') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                p_verify_remark: $("#p_verify_remark").val(),
                test_id: "{{ $Eyetest->test_id }}", 
            },
            beforeSend: function () {
                $("#step6").prop("disabled", true).text("Saving...");
            },
            success: function (response) {
                if (response.success) {
                    $('.nav a[href="#tab7"]').tab('show');
                } else {
                    $.toaster({
                        priority: 'danger',
                        title: 'Error',
                        message: 'Failed to save data. Please try again.'
                    });
                }
            },
            error: function () {
                $.toaster({
                        priority: 'danger',
                        title: 'Error',
                        message: 'An error occurred while saving step 6.'
                    });
            },
            complete: function () {
                $("#step6").prop("disabled", false).text("Okay, Go to Next Step");
            }
        });
        
    });
    
    $("#step7").click(function () {
        let isValid = true;
    
        $(".error").text("");
        $("select").removeClass("is-invalid");
    
        const fields = [
            "re_sph_new", "re_cyl_new", "re_axis_new", "pd_re_new",
            "le_sph_new", "le_cyl_new", "le_axis_new", "pd_le_new",
            "re_sph_bif", "re_cyl_bif", "re_axis_bif", "pd_re_bif",
            "le_sph_bif", "le_cyl_bif", "le_axis_bif", "pd_le_bif",
            "re_distance_new", "le_distance_new",
            "re_near_new", "le_near_new"
        ];
    
        
            const formData = {
                re_sph_new: $("#re_sph_new").val(),
                re_cyl_new: $("#re_cyl_new").val(),
                re_axis_new: $("#re_axis_new").val(),
                pd_re_new: $("#pd_re_new").val(),
                le_sph_new: $("#le_sph_new").val(),
                le_cyl_new: $("#le_cyl_new").val(),
                le_axis_new: $("#le_axis_new").val(),
                pd_le_new: $("#pd_le_new").val(),
                re_sph_bif: $("#re_sph_bif").val(),
                re_cyl_bif: $("#re_cyl_bif").val(),
                re_axis_bif: $("#re_axis_bif").val(),
                pd_re_bif: $("#pd_re_bif").val(),
                le_sph_bif: $("#le_sph_bif").val(),
                le_cyl_bif: $("#le_cyl_bif").val(),
                le_axis_bif: $("#le_axis_bif").val(),
                pd_le_bif: $("#pd_le_bif").val(),
                re_distance_new: $("#re_distance_new").val(),
                le_distance_new: $("#le_distance_new").val(),
                re_near_new: $("#re_near_new").val(),
                le_near_new: $("#le_near_new").val(),
                test_id: "{{ $Eyetest->test_id }}",
                _token: '{{ csrf_token() }}', 
            };
    
            $.ajax({
                url: "{{ route('admin.eyetest.step7.update') }}", 
                method: "POST",
                data: formData,
                beforeSend: function () {
                    $("#step7").prop("disabled", true).text("Saving...");
                },
                success: function (response) {
                    if (response.success) {
                        $('.nav a[href="#tab8"]').tab('show');
                    } else {
                        $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'Failed to save data. Please try again.'
                        });
                    }
                },
                error: function () {
                    $.toaster({
                            priority: 'danger',
                            title: 'Error',
                            message: 'An error occurred while saving step 7.'
                        });
                },
                complete: function () {
                    $("#step7").prop("disabled", false).text("Okay, Go to Next Step");
                }
            });
        
    });

    $("#step8").click(function () 
    {
        let isValid = true;

        $(".error").text("");
        $(".form-check-input").removeClass("is-invalid");
        
        const frameSize = $("input[name='frame_size']:checked").val();
        
        if ($("input[name='frame_size']:checked").length === 0) {
            $("#frame_sizeError").text("Please select a frame size.");
            $("input[name='frame_size']").addClass("is-invalid");
            isValid = false;
        }
        
        const followupDate = $("input[name='followup_date']:checked").val();
        
        if ($("input[name='followup_date']:checked").length === 0) {
            $("#followup_dateError").text("Please select a follow-up time.");
            $("input[name='followup_date']").addClass("is-invalid");
            isValid = false;
        }

        if (isValid) {
            $.ajax({
            url: "{{ route('admin.eyetest.step8.update') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                frame_size: frameSize,
                followup_date: followupDate,
                test_id: "{{ $Eyetest->test_id }}", 
            },
            beforeSend: function () {
                $("#step8").prop("disabled", true).text("Saving...");
            },
            success: function (response) {
                if (response.success) 
                {
                    $('.nav a[href="#tab9"]').tab('show');
                } 
                else {
                    $.toaster({
                        priority: 'danger',
                        title: 'Error',
                        message: 'Failed to save data. Please try again.'
                    });
                }
            },
            error: function () {
                $.toaster({
                        priority: 'danger',
                        title: 'Error',
                        message: 'An error occurred while saving step 8.'
                    });
            },
            complete: function () {
                $("#step8").prop("disabled", false).text("Okay, Go to Final Step");
            }
        });
        } else {
            $('html, body').animate({
                scrollTop: $(".is-invalid:first").offset().top - 100
            }, 400);
        }
    });
    
    let countdownInterval; // Declare globally

    $("#step9").click(function () {
      const contact = "{{ $Eyetest->contact_no }}";
      const test_id = "{{ $Eyetest->test_id }}";// Blade-safe string
      console.log("Contact:", contact);
    
      if (contact.length === 10) {
        $.ajax({
          type: "POST",
          url: "{{ route('admin.test-send-otp') }}",
          data: {
            contact: contact,
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            console.log("OTP Response:", response);
            if (response.status_code === '200') {
              document.getElementById('otp_mobile').value = contact;
              document.getElementById('otp_test_id').value = test_id;
              $('#otpModal').modal('show');
              showOTPSection();
              $.toaster({
                priority: "success",
                title: "Success..!",
                message: "OTP sent to your mobile number."
              });
            } else if (response.status_code === '201') {
              document.getElementById('otp-section').style.display = 'none';
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Something went wrong!"
              });
            } else if (response.status_code === '202') {
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
    });
    
    function showOTPSection() {
      const otpSection = document.getElementById('otp-section');
      const resendBtn = document.getElementById('resend-btn');
      const timerText = document.getElementById('timer');
    
      if (!otpSection || !resendBtn || !timerText) {
        console.error("OTP section elements not found.");
        return;
      }
    
      otpSection.style.display = 'block';
      resendBtn.disabled = true;
      timerText.innerHTML = 'Resend OTP in <span id="countdown">60</span>s';
      startCountdown(60);
    }
    
    function startCountdown(seconds) {
      const countdownSpan = document.getElementById('countdown');
      const resendBtn = document.getElementById('resend-btn');
      const timerText = document.getElementById('timer');
    
      if (!countdownSpan || !resendBtn || !timerText) {
        console.error("Countdown elements missing.");
        return;
      }
    
      clearInterval(countdownInterval);
      let timeLeft = seconds;
      countdownSpan.textContent = timeLeft;
    
      countdownInterval = setInterval(() => {
        timeLeft--;
        countdownSpan.textContent = timeLeft;
    
        if (timeLeft <= 0) {
          clearInterval(countdownInterval);
          resendBtn.disabled = false;
          timerText.innerHTML = "Didn't get the OTP?";
        }
      }, 1000);
    }
    
    function resendOTP() {
      const contact = "{{ $Eyetest->contact_no }}";
    
      document.getElementById('resend-btn').disabled = true;
      document.getElementById('timer').innerHTML = 'Resend OTP in <span id="countdown">60</span>s';
      startCountdown(60);
    
      $.ajax({
        type: "POST",
        url: "{{ route('admin.test-send-otp') }}",
        data: {
          contact: contact,
          _token: "{{ csrf_token() }}"
        },
        dataType: "json",
        success: function (response) {
          if (response.status_code === '200') {
            showOTPSection();
            $.toaster({
              priority: "success",
              title: "Success..!",
              message: "OTP resent successfully."
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
            message: "Failed to resend OTP."
          });
        }
      });
    }
    
    
    $("#verifyOtpForm").submit(function(e) {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let sotp = document.getElementById("sotp" + class_name).value.trim();

        if (!/^\d{4}$/.test(sotp)) 
        { 
            document.getElementById("sotpError" + class_name).textContent = "Otp must be exactly 4 digits.";
            document.getElementById("sotp" + class_name).classList.add("is-invalid");
            isValid = false;
        }

        let form = $("#verifyOtpForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.testotp-verify') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function (response) {
            if (response.status_code === '200')
            {
              $.toaster({
                priority: "success",
                title: "Success..!",
                message: "OTP match successfully."
              });
              
              window.location.href = "{{ route('admin.eye-test-record') }}";
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
    
    });


});
</script>
@endsection
