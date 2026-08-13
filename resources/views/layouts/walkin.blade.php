@extends('layouts.master')
@php
     $usr = Auth::guard()->user();
 @endphp
@section('content')
<style>
    .domestic-orders-date {
        padding-top: 0px;
    }
    
    .section-title{
        font-weight:600;
        border-left:4px solid #0d6efd;
        padding-left:10px;
        color:#2c3e50;
    }
    
    .table thead tr th {
        font-size: 12px;
        font-weight: 500 !important;
        color: #000;
    }
    
    .dashboard-nav{
        background:#f8f9fa;
        padding:8px;
        border-radius:10px;
    }
    
    .dashboard-nav .nav-item{
        margin-right:8px;
    }
    
    .dashboard-nav .nav-link{
        color:#2c3e50;
        font-weight:600;
        border-radius:8px;
        padding:10px 18px;
        background:#ffffff;
        border:1px solid #e3e6f0;
        transition:all .3s ease;
        font-size: 12px;
    }
    
    .dashboard-nav .nav-link i{
        margin-right:6px;
    }
    
    .dashboard-nav .nav-link:hover{
        background:#eef4ff;
        color:#00484a;
        transform:translateY(-2px);
    }
    
    .dashboard-nav .nav-link.active{
        background:#00484a;
        color:#fff;
        box-shadow:0 4px 12px rgba(0,0,0,0.1);
        font-size: 12px;
    }
    
    .nav-link i {
        color: #000;
    }
    
    .nav-link.active i {
        color: #fff;
    }
    
        .col-md-3
{
    margin-bottom: 10px;
}
.staff-performance-dashboard-card{
    border: 1px solid #d9dde7;
    height: 365px;
    overflow: auto;
}
.store-performance-dashboard{
    border: 1px solid #d9dde7;
    width: 100%;
    height: 250px;
    overflow: auto;
}
.staff-performance-dashboard-card .table-responsive,
.store-performance-dashboard .table-responsive{
    height: 100%;
}
.walk-in-entry-history{
    /*border: 1px solid #d9dde7;*/
    width: 100%;
    height: 500px;
    overflow: auto;
}
.walk-in-entry-history .table-responsive{height: 100%;}
</style>
    
    
<div id="ajaxLoader" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading, please wait...</p>
    </div>
</div>
<div class="dashboard-tabs mb-4">

    <ul class="nav nav-pills dashboard-nav">
        <li class="nav-item">
            <a class="nav-link"  href="{{route('index')}}">
                <i class="fa fa-sign-in"></i>
                Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{route('admin.walkin-dashboard')}}">
                <i class="fa fa-sign-in"></i>
                Walk-In Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.audit-dashboard')}}">
                <i class="fa fa-search"></i>
                Mystery Audit
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.nps-dashboard')}}">
                <i class="fa fa-smile-o"></i>
                NPS Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.sale-dashboard')}}">
                <i class="fa fa-line-chart"></i>
                Sales Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.account-dashboard')}}">
                <i class="fa fa-money"></i>
                Account Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.counting-dashboard')}}">
                <i class="fa fa-google-wallet"></i>
                Product Counting Dashboard
            </a>
        </li>

    </ul>

</div>

    <!--Row-->
	<div class="row">
		<div class="col-xl-12 col-md-12 col-lg-12">
		    <a href="#" class="btn btn-primary loaderbtn" data-toggle="modal" data-target="#walkinModal">
                Walk-In Entry
            </a> 
		     <a href="#" class="btn btn-primary loaderbtn" data-toggle="modal" data-target="#walkinFollowModal">
                Followup List
            </a> 
                
			<div class="card">
                <div class="card-body">
                    <div class="row align-items-center mb-2">
                    
                        <!-- Left Text -->
                        <div class="col-lg-9">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Daily Store Performance
                                </span>
                            </h5>
                        </div>
                    
                        <!-- Right Select -->
                        @if($usr->roles[0]->name == 'Admin')
                        <div class="col-lg-3 ms-auto">
                            <div class="form-group">
                                <select class="form-control select1" style="height:32px" id="store_id" name="store_id">
                                    <option value="">Select Store</option>
                                    <?php $tbl_store = DB::table("tbl_store")->where('status',1)->get(); ?>
                                    @foreach($tbl_store as $tbl_store)
                                        <option value="{{$tbl_store->id}}">
                                            {{$tbl_store->store_name}} / ({{$tbl_store->store_id}})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    
                    </div>
					<div class="row">
						<div class=" col-xl-2 col-sm-6 d-flex mb-5 mb-xl-0">
							<div class="feature ">
								<i class="fa fa-sign-in primary feature-icon bg-primary"></i>
							</div>
							<div class="ml-3">
								<small class=" mb-0">Walk-ins Today</small><br>
								<h3 class="font-weight-semibold mb-0" id="walkins">0</h3>
							</div>
						</div>
						<div class=" col-xl-2 col-sm-6 d-flex mb-5 mb-xl-0">
							<div class="feature">
								<i class="si si-layers danger feature-icon bg-danger"></i>
							</div>
							<div class=" d-flex flex-column  ml-3"> <small class=" mb-0">Sales Today</small>
								<h3 class="font-weight-semibold mb-0" id="sales">0</h3>
							</div>
						</div>
						<div class=" col-xl-2 col-sm-6 d-flex  mb-5 mb-sm-0">
							<div class="feature">
								<i class="fa fa-sign-out secondary feature-icon bg-secondary"></i>
							</div>
							<div class=" d-flex flex-column ml-3"> <small class=" mb-0">Walkouts</small>
								<h3 class="font-weight-semibold mb-0" id="walkouts">0</h3>
							</div>
						</div>
						<div class=" col-xl-2 col-sm-6 d-flex mb-5 mb-xl-0">
							<div class="feature">
								<i class="fa fa-percent warning feature-icon bg-warning"></i>
							</div>
							<div class=" d-flex flex-column  ml-3"> <small class=" mb-0">Conversion Rate</small>
								<h3 class="font-weight-semibold mb-0" id="conversion">0%</h3>
							</div>
						</div>
						<div class=" col-xl-2 col-sm-6 d-flex mb-5 mb-xl-0">
							<div class="feature">
								<i class="fa fa-wrench danger feature-icon bg-danger"></i>
							</div>
							<div class=" d-flex flex-column  ml-3"> <small class=" mb-0">Pending Repairs</small>
								<h3 class="font-weight-semibold mb-0" id="repairs">0</h3>
							</div>
						</div>
						<div class=" col-xl-2 col-sm-6 d-flex">
							<div class="feature">
								<i class="si si-basket-loaded success feature-icon bg-success"></i>
							</div>
							<div class=" d-flex flex-column  ml-3"> <small class=" mb-0">Pending Follow-ups</small>
								<h3 class="font-weight-semibold mb-0" id="followups">0</h3>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--End row-->
	
    <div class="row">
        <div class="col-xl-12 col-md-12 col-lg-6">
            <div class="card">
            <div class="row align-items-center mb-2" style="margin: 10px;">
                <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                    <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                        Walk-In Entry History
                    </span>
                </h5>
            </div>
            <div class="row mb-3">
                <div class="col-lg-3" style="margin-top: 10px;">
                    <div class="domestic-orders-date">
                        <div id="reportrange" class="pull-left"
                            style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                        <input type="hidden" class="form-control" id="date_from" name="date_from">
                        <input type="hidden" class="form-control" id="date_to" name="date_to">
                    </div> 
                </div>    
               
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3" style="width: 200px;margin-top: 10px;">
                        <select class="form-control select2" style="height: 32px !important;" id="tb_store_id" name="tb_store_id">
                            <option value="">Select  Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
                </div>
                @endif
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Customer Name,Mobile No" id="search" name="search" style="width: 200px;margin-top: 10px;">
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
    				    <div class="table-responsive">
                            <table class="table datatables-basic w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-10p">Sr.No</th>
                                        <th class="wd-15p">Customer Details</th>
                                        <th class="wd-15p">Visit Purpose</th>
                                        <th class="wd-10p">Customer Final Status Selection</th>
                                        <th class="wd-10p">Store Name</th>
                                        <th class="wd-10p">Staff Name</th>
                                        <th class="wd-10p">Visit Date</th>
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

	<!--Row-->
	<div class="row">
		<div class="col-xl-12 col-md-12 col-lg-6">
			<div class="card">
				<div class="card-body">
				    <div class="row align-items-center mb-2">
                        <!-- Left Text -->
                        <div class="col-lg-6">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Staff Performance Dashboard
                                </span>
                            </h5>
                        </div>
                        <div class="col-lg-3 ms-auto">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" id="staff_d_date" name="staff_d_date">
                        </div> 
                        <!-- Right Select -->
                        @if($usr->roles[0]->name == 'Admin')
                        <div class="col-lg-3 ms-auto">
                            <select class="form-control select1" style="height:32px;" id="staff_d_store_id" name="staff_d_store_id">
                                <option value="">Select Store</option>
                                <?php $tbl_store = DB::table("tbl_store")->where('status',1)->get(); ?>
                                @foreach($tbl_store as $tbl_store)
                                    <option value="{{$tbl_store->id}}">
                                        {{$tbl_store->store_name}} / ({{$tbl_store->store_id}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    
                    </div>
					<div class="row">
                        <div class="col-md-4">
                            <div class="staff-performance-dashboard-card">
                                    <div class="table-responsive">
                                        <table class="table table-vcenter border mb-0 text-nowrap">
                                
                                            <thead>
                                                <tr>
                                                    <th>Staff</th>
                                                    <th>Walk-ins</th>
                                                    <th>Sales</th>
                                                    <th>Walkouts</th>
                                                </tr>
                                            </thead>
                                
                                            <tbody id="staffTable">
                                                <!-- Dynamic data will load here -->
                                            </tbody>
                                
                                        </table>
                                    </div>
                            </div>
                        </div>
                    
                        <div class="col-md-8">
                            <div id="barChart" style="width:100%;overflow: auto;height: 370px;"></div>
                        </div>
                    </div>
		    	</div>
	    	</div>
	    </div>
	 </div>  
	<!--End row-->
	
	@if($usr->roles[0]->name == 'Admin')
       <div class="row">
		<div class="col-xl-12 col-md-12 col-lg-6">
			<div class="card">
				<div class="card-body">
				    <div class="row align-items-center mb-2">
                        <!-- Left Text -->
                        <div class="col-lg-9">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Head Office Dashboard 
                                </span>
                            </h5>
                        </div>
                        <div class="col-lg-3 ms-auto">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" id="head_date" name="head_date">
                        </div> 
                    </div>
					<div class="row">
					    <div class="col-md-4">
					        <div class="staff-performance-dashboard-card">
                                <div class="table-responsive">
                                    <table class="table table-vcenter border mb-0 text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Store</th>
                                                <th>Walk-ins</th>
                                                <th>Sales</th>
                                                <th>Conversion</th>
                                            </tr>
                                        </thead>
                                
                                        <tbody id="headOfficeTable">
                                            <!-- Dynamic rows will load here -->
                                        </tbody>
                                
                                    </table>
                                </div>
                            </div>
                        </div>
						<div class="col-md-8">
                            <div id="barChartHead" style="width:100%;overflow: auto;height: 370px;"></div>
					    </div>
				   </div>
		    	</div>
	    	</div>
	    </div>
	 </div> 
	@endif
	 
	 
	<div class="row">
		<div class="col-xl-6 col-md-12 col-lg-12">
			<div class="card">
				<div class="card-body">
				    <div class="row align-items-center mb-2">
                        <!-- Left Text -->
                        <div class="col-lg-8">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Store Performance Dashboard
                                </span>
                            </h5>
                        </div>
						@if($usr->roles[0]->name == 'Admin')
                        <div class="col-lg-4">
                            <select class="form-control select1" style="height:32px;width:180px" id="staff_metric_store_id" name="staff_d_store_id">
                                <option value="">Select Store</option>
                                <?php $tbl_store = DB::table("tbl_store")->where('status',1)->get(); ?>
                                @foreach($tbl_store as $tbl_store)
                                    <option value="{{$tbl_store->id}}">
                                        {{$tbl_store->store_name}} / ({{$tbl_store->store_id}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                    </div>
					<div class="row">
    					<div class="store-performance-dashboard">
    					    <div class="table-responsive">
    					    <table class="table table-vcenter border mb-0 text-nowrap">
        							<thead class="">
        								<tr>
        									<th>Metric</th>
        									<th>Today</th>
        									<th>This Week</th>
        									<th>This Month</th>
        								</tr>
        							</thead>
        							<tbody id="storeMetricTable">
        								
        							</tbody>
    						</table>
    				    </div>
    				   </div>
				   </div>
		    	</div>
	    	</div>
	    </div>
	    <div class="col-xl-6 col-md-12 col-lg-12">
			<div class="card">
				<div class="card-body">
				    <div class="row align-items-center mb-2">
                        <!-- Left Text -->
                        <div class="col-lg-6">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Walkout Reason Dashboard
                                </span>
                            </h5>
                        </div>
						@if($usr->roles[0]->name == 'Admin')
                        <div class="col-lg-3 ms-auto">
                            <select class="form-control select1" style="height:32px;" id="walk_resion_store_id" name="walk_resion_store_id">
                                <option value="">Select Store</option>
                                <?php $tbl_store = DB::table("tbl_store")->where('status',1)->get(); ?>
                                @foreach($tbl_store as $tbl_store)
                                    <option value="{{$tbl_store->id}}">
                                        {{$tbl_store->store_name}} / ({{$tbl_store->store_id}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-lg-3 ms-auto">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" id="walk_resion_d_date" name="walk_resion_d_date">
                        </div> 
                    </div>
					<div class="row">
					    <div class="store-performance-dashboard">
    					    <div class="table-responsive">
						    <table class="table  table-vcenter border mb-0 text-nowrap">
							<thead class="">
								<tr>
									<th>Reason</th>
									<th>Count</th>
									<th>%</th>
								</tr>
							</thead>
							<tbody id="walkoutReasonTable">
                            </tbody>
						</table>
						
				   </div>
				   </div>
				   </div>
		    	</div>
	    	</div>
	    </div>
	 </div> 
	 
	 
	<div class="row">
		<div class="col-xl-6 col-md-12 col-lg-12">
			<div class="card">
				<div class="card-body">
				    <div class="row align-items-center mb-2">
                        <!-- Left Text -->
                        <div class="col-lg-6">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Staff Performance Dashboard
                                </span>
                            </h5>
                        </div>
						@if($usr->roles[0]->name == 'Admin')
                        <div class="col-lg-3 ms-auto">
                            <select class="form-control select1" style="height:32px;" id="staff_all_store_id" name="staff_all_store_id">
                                <option value="">Select Store</option>
                                <?php $tbl_store = DB::table("tbl_store")->where('status',1)->get(); ?>
                                @foreach($tbl_store as $tbl_store)
                                    <option value="{{$tbl_store->id}}">
                                        {{$tbl_store->store_name}} / ({{$tbl_store->store_id}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-lg-3 ms-auto">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" id="staff_all_date" name="staff_all_date">
                        </div> 
                    </div>
					<div class="row">
					    <div class="store-performance-dashboard">
    					    <div class="table-responsive">
						    <table class="table  table-vcenter border mb-0 text-nowrap">
							<thead class="">
								<tr>
									<th>Staff</th>
									<th>Walk-ins Handled</th>
									<th>Sales</th>
									<th>Walkouts</th>
									<th>Follow-up Conversion</th>
								</tr>
							</thead>
							<tbody id="staffPerformanceTable">
                            </tbody>
						</table>
						
				   </div>
				   </div>
				   </div>
		    	</div>
	    	</div>
	    </div>
	   	<div class="col-xl-6 col-md-12 col-lg-12">
			<div class="card">
				<div class="card-body">
				    <div class="row align-items-center mb-2">
                        <!-- Left Text -->
                        <div class="col-lg-9">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Store Comparison Dashboard
                                </span>
                            </h5>
                        </div>
						<div class="col-lg-3 ms-auto">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" id="store_comp_date" name="store_comp_date">
                        </div> 
                       
                    </div>
					<div class="row">
					    <div class="store-performance-dashboard">
    					    <div class="table-responsive">
						    <table class="table  table-vcenter border mb-0 text-nowrap">
							<thead class="">
								<tr>
									<th>Store</th>
									<th>Walk-ins</th>
									<th>Sales</th>
									<th>Walkouts</th>
									<th>Conversion</th>
								</tr>
							</thead>
							<tbody id="storeComparisonTable"></tbody>
						</table>
						
				   </div>
				   </div>
				   </div>
		    	</div>
	    	</div>
	    </div>
	 </div> 
	 
     <div class="row">
	
	    <div class="col-xl-6 col-md-12 col-lg-12">
			<div class="card">
				<div class="card-body">
				    <div class="row align-items-center mb-2">
                        <!-- Left Text -->
                        <div class="col-lg-9">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Walkout Reason (All Stores)
                                </span>
                            </h5>
                        </div>
						<div class="col-lg-3 ms-auto">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" id="walk_all_date" name="walk_all_date">
                        </div> 
                       
                    </div>
					<div class="row">
					    <div class="store-performance-dashboard">
    					    <div class="table-responsive">
						    <table class="table  table-vcenter border mb-0 text-nowrap">
							<thead class="">
								<tr>
									<th>Reason</th>
									<th>Count</th>
								</tr>
							</thead>
							<tbody id="walkoutAllReasonTable"></tbody>
						</table>
						
				   </div>
				   </div>
				   </div>
		    	</div>
	    	</div>
	    </div>
	    <div class="col-xl-6 col-md-12 col-lg-12">
			<div class="card">
				<div class="card-body">
				    <div class="row align-items-center mb-2">
                        <!-- Left Text -->
                        <div class="col-lg-9">
                            <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    Follow-Up Dashboard(All Stores)
                                </span>
                            </h5>
                        </div>
						@if($usr->roles[0]->name == 'Admin')
                        <div class="col-lg-3 ms-auto">
                            <select class="form-control select1" style="height:32px;" id="followup_store_id" name="followup_store_id">
                                <option value="">Select Store</option>
                                <?php $tbl_store = DB::table("tbl_store")->where('status',1)->get(); ?>
                                @foreach($tbl_store as $tbl_store)
                                    <option value="{{$tbl_store->id}}">
                                        {{$tbl_store->store_name}} / ({{$tbl_store->store_id}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
						<div class="col-lg-3 ms-auto">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" id="followp_date" name="followp_date">
                        </div> 
                       
                    </div>
					<div class="row">
					    
						    <table class="table  table-vcenter border mb-0 text-nowrap">
							<thead class="">
								<tr>
									<th>Reason</th>
									<th>Count</th>
								</tr>
							</thead>
							<tbody id="followupDashboardTable"></tbody>
						</table>
						
				   </div>
		    	</div>
	    	</div>
	    </div>
	 </div> 
	 


    <div class="modal fade" data-backdrop="static" id="walkinModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Walk-In Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="walkinForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

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
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Contact no" name="contact_no" id="contact_no" maxlength="10"  pattern="^[6-9][0-9]{9}$">
                                <span class="error badge text-danger" id="contact_noError"></span>
                            </div>
                            <div class="col-md-12">
                                <label for="" class="form-label">Full Name: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter customer name" id="cust_name" name="cust_name" >
                                <span class="error badge text-danger" id="cust_nameError"></span>
                            </div>
                            <div class="col-md-12">
                                <label for="" class="form-label">Area / Location: <span class="text-danger">*</span></label>
                                <textarea type="text" class="form-control" placeholder="Enter location" id="location" name="location"></textarea>
                                <span class="error badge text-danger" id="locationError"></span>
                            </div>
                            <div class="col-md-12">
                                <label for="" class="form-label">Visit Purpose: <span class="text-danger">*</span></label>
                                <select class="form-control select1" name="visit_purpose" id="visit_purpose" style="width:240px">
                                    <option value="">Select</option>
                                    <option value="Eyeglasses">Eyeglasses</option>
                                    <option value="Sunglasses">Sunglasses</option>
                                    <option value="Contact Lens">Contact Lens</option>
                                    <option value="Eye Test">Eye Test</option>
                                    <option value="Repair">Repair</option>
                                    <option value="Browsing">Browsing</option>
                                </select>
                                <span class="error badge text-danger" id="visit_purposeError"></span>
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



<div class="modal fade" data-backdrop="static" id="walkinFollowModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Folow Up List</h5>
                 @if($usr->roles[0]->name == 'Admin')
                    <select class="form-control select2" style="height: 32px !important;width: 250px;margin-left:10px" id="f_store_id" name="f_store_id">
                        <option value="">Select  Store</option>
                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                       @foreach($tbl_store as $tbl_store)
                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                      @endforeach
                    </select>
                @endif
                <div class="col-lg-3 ms-auto">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" id="f_date" name="f_date">
                        </div> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="table-responsive">
				<table class="table table-vcenter border mb-0 text-nowrap">
		
					<thead>
						<tr>
							<th>Sr.No</th>
							<th>Followup Date</th>
							<th>Customer Details</th>
							<th>Walkout Reason</th>
							<th>Call Status</th>
							<th>Customer Response</th>
							<th>Followup Response</th>
							<th>Staff Name</th>
							<th>Store Name</th>
						</tr>
					</thead>
		
					<tbody id="followuplisttable">
						 
					</tbody>
		
				</table>
			</div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" data-backdrop="static" id="walkinupdatessModal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitles">Update Walkin Customer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="walkinupdateForm" method="POST" enctype="multipart/form-data">
            @csrf
            
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="walkin_id" id="walkin_id">
                <input type="hidden" name="walkintype" id="walkintype">

              <div class="modal-body">  
                <div class="row" id="fibdiv">
                    <div class="col-md-3">
                        <label for="" class="form-label">Frame Category: </label>
                        <select class="form-control select1" name="frame_category" id="frame_category" style="width:160px">
                            <option value="">Select</option>
                            <option value="Budget">Budget</option>
                            <option value="Premium">Premium</option>
                            <option value="Branded">Branded</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="" class="form-label">Brand Interest: </label>
                        <select class="form-control select1" name="brand_intrest" id="brand_intrest" style="width:160px">
                            <option value="">Select</option>
                            <option value="Budget">Ray-Ban</option>
                            <option value="Premium">Titan</option>
                            <option value="Branded">Fastrack</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="" class="form-label">Budget Range:</label>
                        <select class="form-control select1" name="budget_range" id="budget_range" style="width:160px">
                            <option value="">Select</option>
                            <option value="999 – 1999">₹999 – ₹1999</option>
                            <option value="2000 – 4000">₹2000 – ₹4000</option>
                            <option value="4000+">4000+</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Eye Test</label>
                        <div class="d-flex">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="eye_test" id="eye_test_yes" value="YES">
                                <label class="form-check-label" for="eye_test_yes">YES</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="eye_test" id="eye_test_no" value="NO" >
                                <label class="form-check-label" for="eye_test_no">NO</label>
                            </div>
                        </div>
                    </div>    
                </div>
                 <br>
  
        
                <div class="row" id="pdiv">
                    <div class="col-md-3">
                        <label for="" class="form-label">Frame:</label>
                        <input type="text" class="form-control" placeholder="Enter Frame" id="Frame" name="Frame">
                    </div> 
                    <div class="col-md-3">
                        <label for="" class="form-label">Lens Type:</label>
                        <input type="text" class="form-control" placeholder="Enter Lens Type" id="Lens_Type" name="Lens_Type">
                    </div> 
                    <div class="col-md-3">
                        <label for="" class="form-label">Order Amount:</label>
                        <input type="text" class="form-control" placeholder="Enter Order Amount" id="order_amount" name="order_amount">
                    </div> 
                    <div class="col-md-3">
                        <label for="" class="form-label">Payment Mode:</label>
                        <input type="text" class="form-control" placeholder="Enter Payment Mode" id="payment_mode" name="payment_mode">
                    </div>
                    <div class="col-md-3">
                        <label for="" class="form-label">Delivery Date:</label>
                        <input type="date" class="form-control" id="delivery_date" name="delivery_date">
                    </div>
                </div> 
                
                <div class="row" id="wdiv">
                    <div class="col-md-3">
                        <label for="" class="form-label">Walkout Reason: <span class="text-danger">*</span></label>
                        <select class="form-control select1" name="walkout_reason" id="walkout_reason" style="width:160px">
                            <option value="">Select</option>
                            <option value="Price High">Price High</option>
                            <option value="Comparing Other Store">Comparing Other Store</option>
                            <option value="Budget Issue">Budget Issue</option>
                            <option value="Will Come Later">Will Come Later</option>
                            <option value="Stock Not Available">Stock Not Available</option>
                            <option value="Just Checking">Just Checking</option>
                        </select>
                    </div> 
                    <div class="col-md-3">
                        <label for="" class="form-label">Product Interest: </label>
                        <select class="form-control select1" name="product_interest" id="product_interest" style="width:160px">
                            <option value="">Select</option>
                            <option value="Brand">Brand </option>
                            <option value="Frame">Frame</option>
                        </select>
                    </div> 
                    <div class="col-md-3">
                        <label for="" class="form-label">Lead Priority: </label>
                        <select class="form-control select1" name="Lead_priority" id="Lead_priority" style="width:160px">
                            <option value="">Select</option>
                            <option value="Hot">Hot </option>
                            <option value="Warm">Warm</option>
                            <option value="Cold">Cold</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="" class="form-label">Follow-Up Date:</label>
                        <input type="date" class="form-control" id="Follow_up_date" name="Follow_up_date">
                    </div>
                </div> 
                
                
                <div class="row" id="rdiv">
                    <div class="col-md-3">
                        <label for="" class="form-label">Product: <span class="text-danger">*</span></label>
                        <select class="form-control select1" name="repair_product" id="repair_product" style="width:160px">
                            <option value="">Select</option>
                            <option value="Eyeglass / Sunglass">Eyeglass / Sunglass</option>
                            <option value="Brand">Brand</option>
                            <option value="Frame Model">Frame Model</option>
                        </select>
                    </div> 
                    <div class="col-md-3">
                        <label for="" class="form-label">Complaint: </label>
                        <select class="form-control select1" name="complaint" id="complaint" style="width:160px">
                            <option value="">Select</option>
                            <option value="Frame Broken">Frame Broken </option>
                            <option value="Lens Scratch">Lens Scratch</option>
                            <option value="Power Issue">Power Issue</option>
                            <option value="Alignment">Alignment</option>
                            <option value="Nose Pad">Nose Pad</option>
                            <option value="Screw Missing">Screw Missing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div> 
                    <div class="col-md-3">
                        <label for="" class="form-label">Product Condition: </label>
                        <select class="form-control select1" name="product_condition" id="product_condition" style="width:160px">
                            <option value="">Select</option>
                            <option value="Frame OK">Frame OK </option>
                            <option value="Frame Damaged">Frame Damaged</option>
                            <option value="Lens Scratch">Lens Scratch</option>
                            <option value="Lens Broken">Lens Broken</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="" class="form-label">Upload Photo :</label>
                        <input type="file" class="form-control" id="photo" name="photo">
                    </div>
                    <div class="col-md-3">
                        <label for="" class="form-label">Repair Type :</label>
                        <div class="d-flex">
                            
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="repair_type" id="In Store Repair" value="In Store Repair">
                                <label class="form-check-label" for="In Store Repair">In Store Repair</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="repair_type" id="Send to Lab" value="Send to Lab" >
                                <label class="form-check-label" for="Send to Lab">Send to Lab</label>
                            </div>
            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="" class="form-label">Delivery Date:</label>
                        <input type="date" class="form-control" id="repair_delivery_date" name="repair_delivery_date">
                    </div>
                </div> 

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
      </div>
    </div>
    </div> 

<div class="modal fade" data-backdrop="static" id="walkinviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Walk-In Entry Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               

                    <div class="multisteps-form__content">
                        
                        <div class="row">
                             <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    1 Customer Entry (Walk-In Module)
                                </span>
                            </h5>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Contact no"  id="contact_no_v" maxlength="10"  pattern="^[6-9][0-9]{9}$">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Full Name: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter customer"  id="cust_name_v" >
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Area / Location: <span class="text-danger">*</span></label>
                                <textarea type="text" class="form-control" placeholder="Enter location" id="location_v" name="location"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Visit Purpose: <span class="text-danger">*</span></label>
                                <select class="form-control select1" name="visit_purpose" id="visit_purpose_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Eyeglasses">Eyeglasses</option>
                                    <option value="Sunglasses">Sunglasses</option>
                                    <option value="Contact Lens">Contact Lens</option>
                                    <option value="Eye Test">Eye Test</option>
                                    <option value="Repair">Repair</option>
                                    <option value="Browsing">Browsing</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                             <h5 style="margin:0;font-weight:600;color:#2c3e50;">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    2 Consultation / Trial Stage
                                </span>
                            </h5>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="" class="form-label">Frame Category: </label>
                                <select class="form-control select1" name="frame_category" id="frame_category_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Budget">Budget</option>
                                    <option value="Premium">Premium</option>
                                    <option value="Branded">Branded</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Brand Interest: </label>
                                <select class="form-control select1" name="brand_intrest" id="brand_intrest_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Budget">Ray-Ban</option>
                                    <option value="Premium">Titan</option>
                                    <option value="Branded">Fastrack</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Budget Range:</label>
                                <select class="form-control select1" name="budget_range" id="budget_range_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="999 – 1999">₹999 – ₹1999</option>
                                    <option value="2000 – 4000">₹2000 – ₹4000</option>
                                    <option value="4000+">4000+</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Eye Test</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="eye_test_v" id="eye_test_yes" value="YES">
                                        <label class="form-check-label" for="eye_test_yes">YES</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="eye_test_v" id="eye_test_no" value="NO" >
                                        <label class="form-check-label" for="eye_test_no">NO</label>
                                    </div>
                                </div>
                            </div>    
                        </div>
                         <br>
                        <div class="row">
                             <h5 style="margin:0;font-weight:600;color:#2c3e50;margin-bottom:10px">
                                <span style="border-left:4px solid #0d6efd;padding-left:8px;">
                                    3 Customer Final Status Selection
                                </span>
                            </h5>
                        </div>    
                        <div class="row">
                            <div class="d-flex">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cust_status_v" id="Purchased" value="PURCHASED">
                                    <label class="form-check-label" for="Purchased">Purchased</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cust_status_v" id="Order Pending" value="ORDER PENDING" >
                                    <label class="form-check-label" for="Order Pending">Order Pending</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cust_status_v" id="Walkout" value="WALKOUT">
                                    <label class="form-check-label" for="Walkout">Walkout</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cust_status_v" id="Repair" value="REPAIRING">
                                    <label class="form-check-label" for="Repair">Repair</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row" id="pdiv_v" style="display:none">
                            <div class="col-md-3">
                                <label for="" class="form-label">Frame:</label>
                                <input type="text" class="form-control" placeholder="Enter Frame" id="Frame_v" name="Frame">
                            </div> 
                            <div class="col-md-3">
                                <label for="" class="form-label">Lens Type:</label>
                                <input type="text" class="form-control" placeholder="Enter Lens Type" id="Lens_Type_v" name="Lens_Type">
                            </div> 
                            <div class="col-md-3">
                                <label for="" class="form-label">Order Amount:</label>
                                <input type="text" class="form-control" placeholder="Enter Order Amount" id="order_amount_v" name="order_amount">
                            </div> 
                            <div class="col-md-3">
                                <label for="" class="form-label">Payment Mode:</label>
                                <input type="text" class="form-control" placeholder="Enter Payment Mode" id="payment_mode_v" name="payment_mode">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Delivery Date:</label>
                                <input type="date" class="form-control" id="delivery_date" name="delivery_date_v">
                            </div>
                        </div> 
                        
                        <div class="row" id="wdiv_v" style="display:none">
                            <div class="col-md-3">
                                <label for="" class="form-label">Walkout Reason: <span class="text-danger">*</span></label>
                                <select class="form-control select1" name="walkout_reason" id="walkout_reason_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Price High">Price High</option>
                                    <option value="Comparing Other Store">Comparing Other Store</option>
                                    <option value="Budget Issue">Budget Issue</option>
                                    <option value="Will Come Later">Will Come Later</option>
                                    <option value="Stock Not Available">Stock Not Available</option>
                                    <option value="Just Checking">Just Checking</option>
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <label for="" class="form-label">Product Interest: </label>
                                <select class="form-control select1" name="product_interest" id="product_interest_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Brand">Brand </option>
                                    <option value="Frame">Frame</option>
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <label for="" class="form-label">Lead Priority: </label>
                                <select class="form-control select1" name="Lead_priority" id="Lead_priority_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Hot">Hot </option>
                                    <option value="Warm">Warm</option>
                                    <option value="Cold">Cold</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Follow-Up Date:</label>
                                <input type="date" class="form-control" id="Follow_up_date_v" name="Follow_up_date">
                            </div>
                        </div> 
                        
                        
                        <div class="row" id="rdiv_v" style="display:none">
                            <div class="col-md-3">
                                <label for="" class="form-label">Product: <span class="text-danger">*</span></label>
                                <select class="form-control select1" name="repair_product" id="repair_product_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Eyeglass / Sunglass">Eyeglass / Sunglass</option>
                                    <option value="Brand">Brand</option>
                                    <option value="Frame Model">Frame Model</option>
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <label for="" class="form-label">Complaint: </label>
                                <select class="form-control select1" name="complaint" id="complaint_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Frame Broken">Frame Broken </option>
                                    <option value="Lens Scratch">Lens Scratch</option>
                                    <option value="Power Issue">Power Issue</option>
                                    <option value="Alignment">Alignment</option>
                                    <option value="Nose Pad">Nose Pad</option>
                                    <option value="Screw Missing">Screw Missing</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <label for="" class="form-label">Product Condition: </label>
                                <select class="form-control select1" name="product_condition" id="product_condition_v" style="width:160px">
                                    <option value="">Select</option>
                                    <option value="Frame OK">Frame OK </option>
                                    <option value="Frame Damaged">Frame Damaged</option>
                                    <option value="Lens Scratch">Lens Scratch</option>
                                    <option value="Lens Broken">Lens Broken</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Upload Photo :</label>
                                <input type="file" class="form-control" id="photo" name="photo">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Repair Type :</label>
                                <div class="d-flex">
                                    
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="repair_type_v" id="In Store Repair" value="In Store Repair">
                                        <label class="form-check-label" for="In Store Repair">In Store Repair</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="repair_type_v" id="Send to Lab" value="Send to Lab" >
                                        <label class="form-check-label" for="Send to Lab">Send to Lab</label>
                                    </div>
                    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Delivery Date:</label>
                                <input type="date" class="form-control" id="repair_delivery_date_v" name="repair_delivery_date">
                            </div>
                        </div> 
        
                       
                    </div>
            </div>
        </div>
    </div>
</div>	 
    
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script>
    $(document).ready(function() {
        $('.select').select2({
        });
        
        $('.select2').select2({
          allowClear: true
        });
        
      });


    
    var start = moment();   // default today
    var end   = moment();   // default today
    
    function cb(start, end)
    {
        $('#reportrange span').html(
            start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY')
        );
    
        $('#date_from').val(start.format('YYYY-MM-DD'));
        $('#date_to').val(end.format('YYYY-MM-DD'));
    
        dataListView.draw();
    }
    
    
    /* =========================
       DATE PICKER
    ========================= */
    
    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        showDropdowns: true,
        linkedCalendars: false,
        maxDate: moment(),
    
        locale: {
            format: 'DD-MM-YYYY',
            cancelLabel: 'Clear'
        }
    
    }, function(start, end) {
        cb(start, end);
    });
    
    
    /* APPLY EVENT */
    $('#reportrange').on('apply.daterangepicker', function(ev, picker)
    {
        cb(picker.startDate, picker.endDate);
    });
    
    
    /* CLEAR EVENT */
    $('#reportrange').on('cancel.daterangepicker', function()
    {
        $('#date_from').val('');
        $('#date_to').val('');
        $('#reportrange span').html('Select Date');
    
        dataListView.draw();
    });
    
    
    /* DEFAULT LOAD */
    cb(start, end);
    
    </script>
    
    <script>

    

    
     let debounceTimer;
     $('#contact_no').on('change', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            let contactNo = $('#contact_no').val().trim();
    
            if (!/^[6-9]\d{9}$/.test(contactNo)) {
                $('#contactError').text("Enter valid 10-digit mobile number.");
                return;
            } else {
                $('#contactError').text("");
            }
    
            $.ajax({
                url: "{{ route('admin.getcustomer') }}",
                method: "GET",
                data: { contact_no: contactNo },
                beforeSend: function () {
                    $("#ajaxLoader").show();
                },
                success: function (response) {
                    if (response.success) {
                        let data = response.data;
    
                        $('#cust_name').val(data.cust_name);
                        $('#location').val(data.cust_address);
            
    
                    } else {
                        $.toaster({ priority: 'danger', title: 'Error', message: response.message,
                       timeout: 3000 });
                    }
                },
                error: function () {
                    $.toaster({ priority: 'danger', title: 'Error', message: 'Error fetching customer data.',
                    timeout: 3000 });
                },
                complete: function () {
                    $("#ajaxLoader").fadeOut();
                }
            });
        }, 500);
    });
    
    
    $("#walkinForm").submit(function(e) {
    
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        $(".error").text('');
        $(".is-invalid").removeClass("is-invalid");
    
        let cust_name = $("#cust_name").val().trim();
        let contact_no = $("#contact_no").val().trim();
        let location = $("#location").val().trim();
        let visit_purpose = $("#visit_purpose").val().trim();

    
        /* -------------------------
           BASIC VALIDATION
        -------------------------*/
    
        if (cust_name === "") {
            $("#cust_nameError").text("Customer Name Required.");
            $("#cust_name").addClass("is-invalid");
            isValid = false;
        }
    
        if (!/^\d{10}$/.test(contact_no)) {
            $("#contact_noError").text("Contact must be a 10-digit number.");
            $("#contact_no").addClass("is-invalid");
            isValid = false;
        }
    
        if (location === "") {
            $("#locationError").text("Location is required.");
            $("#location").addClass("is-invalid");
            isValid = false;
        }
    
        if (visit_purpose === "") {
            $("#visit_purposeError").text("Select Visit Purpose.");
            $("#visit_purpose").addClass("is-invalid");
            isValid = false;
        }

    
        if (!isValid) return;
    
        /* -------------------------
           AJAX SUBMIT
        -------------------------*/
    
        let form = $("#walkinForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.add-walkin-record') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
    
            success: function(response) {

            if(response.status === true){
        
                $.toaster({
                    priority: 'success',
                    title: response.message,
                    message: '',
                    timeout: 3000
                });
        
                $("#walkinForm")[0].reset();
        
                // Hide modal if form is inside modal
                $("#walkinModal").modal('hide');
        
                setTimeout(function(){
                    location.reload();
                },1500);
        
            } else {
        
                alert(response.message);
        
            }
        
        }
    
        });
    
    });
    
    $(document).ready(function(){
    
        loadStorePerformance();
        loadStaffPerformance();
        loadHeadOfficePerformance();
        loadStoreMetrics();
        loadWalkoutReasons();
        loadStaffPerformanceoverall();
        loadStoreComparison();
        loadAllWalkoutReasons();
        loadFollowupDashboard();

    
    });
    
    $("#store_id").change(function(){

        let store_id = $(this).val();
    
        loadStorePerformance(store_id);
    
    });
    
    
    function loadStorePerformance(store_id = '')
    {
        $.ajax({
    
            url: "{{ route('admin.store.performance') }}",
            type: "POST",
            data: {
                store_id: store_id,
                _token: "{{ csrf_token() }}"
            },
             beforeSend: function () {
                $("#ajaxLoader").show();
            },
    
            success: function(res){
    
                $("#walkins").text(res.walkins);
                $("#sales").text(res.sales);
                $("#walkouts").text(res.walkouts);
                $("#conversion").text(res.conversion + "%");
                $("#repairs").text(res.repairs);
                $("#followups").text(res.followups);
    
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
    
        });
    }
    
    
    $("#staff_d_date, #staff_d_store_id").change(function(){

        loadStaffPerformance();
    
    });
    
    let chart;


    function loadStaffPerformance()
    {
    
        let date = $("#staff_d_date").val();
        let store_id = $("#staff_d_store_id").val();
    
        $.ajax({
    
            url:"{{ route('admin.staff.performance') }}",
            type:"POST",
            data:{
                date:date,
                store_id:store_id,
                _token:"{{ csrf_token() }}"
            },
             beforeSend: function () {
                $("#ajaxLoader").show();
            },
    
            success:function(res){
    
                // TABLE
                let html = "";
    
                res.table.forEach(function(row){
    
                    html += `
                    <tr>
                        <td class="text-sm font-weight-600">${row.name}</td>
                        <td>${row.walkins}</td>
                        <td>${row.sales}</td>
                        <td>${row.walkouts}</td>
                    </tr>`;
    
                });
    
                $("#staffTable").html(html);
    
    
                // GRAPH
                let options = {
    
                    chart:{ type:"bar", height:350 },
    
                    series:[
                        { name:"Walk-ins", data:res.walkins },
                        { name:"Sales", data:res.sales },
                        { name:"Conversion %", data:res.conversion }
                    ],
    
                    xaxis:{ categories:res.staff },
    
                    colors:["#0d6efd","#28a745","#dc3545"],
    
                    plotOptions:{
                        bar:{
                            columnWidth:"55%",
                            borderRadius:4
                        }
                    },
    
                    dataLabels:{ enabled:true },
    
                    legend:{ position:"top" }
    
                };
    
                if(chart){
                    chart.updateOptions(options);
                }
                else{
                    chart = new ApexCharts(document.querySelector("#barChart"), options);
                    chart.render();
                }
    
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
    
        });
    
    }
    
    $("#head_date").change(function(){

        loadHeadOfficePerformance();
    
    });
    
    
    let headChart;

    function loadHeadOfficePerformance(){
    
        let date = $("#head_date").val();
    
        $.ajax({
    
            url:"{{ route('admin.head.performance') }}",
            type:"POST",
            data:{
                date:date,
                _token:"{{ csrf_token() }}"
            },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
    
            success:function(res){
    
                // TABLE
                let html = "";
    
                res.table.forEach(function(row){
    
                    html += `
                    <tr>
                        <td class="text-sm font-weight-600">${row.store}</td>
                        <td>${row.walkins}</td>
                        <td>${row.sales}</td>
                        <td>${row.conversion}%</td>
                    </tr>`;
    
                });
    
                $("#headOfficeTable").html(html);
    
    
                // GRAPH
                let options = {
    
                    chart:{ type:"bar", height:350 },
    
                    series:[
                        { name:"Walk-ins", data:res.walkins },
                        { name:"Sales", data:res.sales },
                        { name:"Conversion %", data:res.conversion }
                    ],
    
                    xaxis:{ categories:res.stores },
    
                    colors:["#0d6efd","#28a745","#dc3545"],
    
                    plotOptions:{
                        bar:{
                            columnWidth:"55%",
                            borderRadius:4
                        }
                    },
    
                    dataLabels:{ enabled:true },
    
                    legend:{ position:"top" }
    
                };
    
                if(headChart){
                    headChart.updateOptions(options);
                }else{
                    headChart = new ApexCharts(document.querySelector("#barChartHead"), options);
                    headChart.render();
                }
    
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
    
        });
    
    }
    
    
    $("#staff_metric_store_id").change(function(){
    
        loadStoreMetrics();
    
    });
    function loadStoreMetrics()
    {

        let store_id = $("#staff_metric_store_id").val();
    
        $.ajax({
    
            url:"{{ route('admin.store.metrics') }}",
            type:"POST",
            data:{
                store_id:store_id,
                _token:"{{ csrf_token() }}"
            },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
    
    
            success:function(res){
    
                let html = `
    
                <tr>
                    <td class="fw-bold">Walk-ins</td>
                    <td>${res.walkins_today}</td>
                    <td>${res.walkins_week}</td>
                    <td>${res.walkins_month}</td>
                </tr>
    
                <tr>
                    <td class="fw-bold">Sales Bills</td>
                    <td>${res.sales_today}</td>
                    <td>${res.sales_week}</td>
                    <td>${res.sales_month}</td>
                </tr>
    
                <tr>
                    <td class="fw-bold">Walkouts</td>
                    <td>${res.walkout_today}</td>
                    <td>${res.walkout_week}</td>
                    <td>${res.walkout_month}</td>
                </tr>
    
                <tr>
                    <td class="fw-bold text-success">Conversion Rate</td>
                    <td>${res.conversion_today}%</td>
                    <td>${res.conversion_week}%</td>
                    <td>${res.conversion_month}%</td>
                </tr>
    
                <tr>
                    <td class="fw-bold">Walkout Follow-ups Done</td>
                    <td>${res.follow_today}</td>
                    <td>${res.follow_week}</td>
                    <td>${res.follow_month}</td>
                </tr>
    
                `;
    
                $("#storeMetricTable").html(html);
    
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
    
        });
    
    }
    
    $("#walk_resion_store_id, #walk_resion_d_date").change(function(){

        loadWalkoutReasons();
    
    });
    
    
    function loadWalkoutReasons()
    {
    
        let store_id = $("#walk_resion_store_id").val();
        let date = $("#walk_resion_d_date").val();
    
        $.ajax({
    
            url: "{{ route('admin.walkout.reasons') }}",
            type: "POST",
    
            data:{
                store_id: store_id,
                date: date,
                _token: "{{ csrf_token() }}"
            },
            
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
    
            success:function(res){
    
                let html = "";
    
                res.forEach(function(row){
    
                    html += `
                    <tr>
                        <td class="text-sm font-weight-600">${row.reason}</td>
                        <td>${row.count}</td>
                        <td>${row.percent}%</td>
                    </tr>
                    `;
    
                });
    
                $("#walkoutReasonTable").html(html);
    
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
    
        });
    
    }
    
    
    $("#staff_all_store_id, #staff_all_date").change(function(){

        loadStaffPerformanceoverall();
    
    });
    
    
    function loadStaffPerformanceoverall()
    {
        let store_id = $("#staff_all_store_id").val();
        let date = $("#staff_all_date").val();
    
        $.ajax({
    
            url: "{{ route('admin.staff.performanceoverall') }}",
            type: "POST",
    
            data:{
                store_id: store_id,
                date: date,
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
            success:function(res){
    
                let html = "";
    
                res.forEach(function(row){
    
                    html += `
                    <tr>
                        <td class="text-sm font-weight-600">${row.staff}</td>
                        <td>${row.walkins}</td>
                        <td class="text-success">${row.sales}</td>
                        <td class="text-danger">${row.walkouts}</td>
                        <td>${row.followups}</td>
                    </tr>
                    `;
    
                });
    
                $("#staffPerformanceTable").html(html);
    
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
    
        });
    
    }
    
    $("#store_comp_date").change(function(){

        loadStoreComparison();
    
    });
    
    function loadStoreComparison()
    {
        let date = $("#store_comp_date").val();
    
        $.ajax({
    
            url: "{{ route('admin.store.comparison') }}",
            type: "POST",
    
            data:{
                date: date,
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
    
            success:function(res){
    
                let html = "";
    
                res.forEach(function(row){
    
                    html += `
                    <tr>
                        <td class="text-sm font-weight-600">${row.store}</td>
                        <td>${row.walkins}</td>
                        <td class="text-success">${row.sales}</td>
                        <td class="text-danger">${row.walkouts}</td>
                        <td class="fw-bold">${row.conversion}%</td>
                    </tr>
                    `;
    
                });
    
                $("#storeComparisonTable").html(html);
    
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
    
        });
    
    }
    
    $("#walk_all_date").change(function(){

        loadAllWalkoutReasons();
    
    });

    function loadAllWalkoutReasons()
    {
        let date = $("#walk_all_date").val();
    
        $.ajax({
    
            url: "{{ route('admin.walkout.allreasons') }}",
            type: "POST",
    
            data:{
                date: date,
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
    
            success:function(res){
    
                let html = "";
    
                res.forEach(function(row){
    
                    html += `
                    <tr>
                        <td class="text-sm font-weight-600">${row.reason}</td>
                        <td>${row.count}</td>
                    </tr>
                    `;
    
                });
    
                $("#walkoutAllReasonTable").html(html);
    
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
    
        });
    
    }
    
    $("#followup_store_id, #followp_date").change(function(){

        loadFollowupDashboard();
    
    });

    function loadFollowupDashboard()
    {
    
        let store_id = $("#followup_store_id").val();
        let date = $("#followp_date").val();
    
        $.ajax({
    
            url:"{{ route('admin.followup.dashboard') }}",
            type:"POST",
    
            data:{
                store_id:store_id,
                date:date,
                _token:"{{ csrf_token() }}"
            },
    
            success:function(res)
            {

                let html = `
                
                <tr>
                    <td class="text-sm font-weight-600">Pending Follow-ups</td>
                    <td>${res.pending}</td>
                </tr>
    
                <tr>
                    <td class="text-sm font-weight-600">Calls Done Today</td>
                    <td>${res.calls_done}</td>
                </tr>
    
                <tr>
                    <td class="text-sm font-weight-600">Converted Customers</td>
                    <td>${res.converted}</td>
                </tr>
    
                <tr>
                    <td class="text-sm font-weight-600 text-success">Conversion Rate</td>
                    <td>${res.conversion}%</td>
                </tr>
                `;
    
                $("#followupDashboardTable").html(html);
    
            }
    
        });
    
    }
    
    
    let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function () {
        $('#processingLoader').show();
    })
    .on('draw.dt', function () {
        $('#processingLoader').hide();
    })
    .DataTable({
    
        processing: true,
        serverSide: true,
        bFilter: false,
    
        ajax: {
            url: "{{ route('admin.walkin-datatable') }}",
            type: "POST",
            dataType: "json",
            data: function (d) {
    
                d.date_from = $('#date_from').val();
                d.date_to   = $('#date_to').val();
                d.search1   = $('#search').val();
                d.store_id  = $('#tb_store_id').val();
                d._token    = "{{ csrf_token() }}";
    
            },
            error:function(xhr){
                console.log(xhr.responseText);
            }
        },
    
        columns: [
    
            { data: "sr_no", orderable:false },
    
            { data: "cust_details", orderable:false },
    
            { data: "visit_purpose", orderable:false },
    

            { data: "final_stage", orderable:false },
    
            { data: "store_name", orderable:false },
    
            { data: "staff_name", orderable:false },
    
            { data: "walkin_date", orderable:false },
    
            {
                data: "action",
                orderable:false,
                searchable:false,
                render: function (data, type, full) {

                    let html = `
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                ACTION
                            </button>
                            <div class="dropdown-menu">`;
                
                    if (full.final_stage === 'ORDER PENDING' || full.visit_purpose === 'Eye Test') {
                        html += `
                            <a class="dropdown-item pointer" onclick="walkinupdateModal('${full.id}','PURCHASED')">Purchased</a>
                            <a class="dropdown-item pointer" onclick="walkinupdateModal('${full.id}','WALKOUT')">Walkout</a>
                            <a class="dropdown-item pointer" onclick="walkinupdateModal('${full.id}','REPAIRING')">Repair</a>
                        `;
                    }
                
                    html += `
                        <a class="dropdown-item pointer view-walkin" data-row='${JSON.stringify(full)}'>View</a>
                        <a class="dropdown-item pointer action-delete" data-id="${full.id}">Delete</a>
                        </div>
                    </div>`;
                
                    return html;
                }
            }
    
        ],
    
        searchDelay:1500,
    
        columnDefs:[
            {
                className:'control',
                orderable:false,
                responsivePriority:2,
                targets:0
            }
        ],
    
        dom:'<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6">>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    
        language:{
            paginate:{
                previous:'&nbsp;',
                next:'&nbsp;'
            },
            sLengthMenu:"_MENU_",
            sZeroRecords:"No results available",
            sSearch:"Search",
            sProcessing:"Processing",
            sInfo:"Showing _START_ to _END_ of _TOTAL_ entries",
            sInfoFiltered:""
        },
    
        responsive:{
            details:{
                type:'column',
                renderer:function(api,rowIdx,columns){
    
                    let data = $.map(columns,function(col){
    
                        return col.title !== ''
                            ?
                            `<tr data-dt-column="${col.columnIndex}">
                                <td>${col.title} :</td>
                                <td>${col.data}</td>
                            </tr>`
                            : '';
    
                    }).join('');
    
                    return data ?
                    $('<table class="table"/>').append('<tbody>'+data+'</tbody>')
                    : false;
    
                }
            }
        },
    
        aLengthMenu:[
            [10,20,50,100],
            [10,20,50,100]
        ],
    
        select:{
            style:"multi"
        },
    
        order:[
            [7,"desc"]
        ],
    
        displayLength:10
    
    });
    
    
    $('.input').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            const column = dataListView.column($(this).attr('name'));
            column.search($(this).val()).draw();
        }.bind(this), 500);
    });
    
    $('.select2').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });
    
    
    $("table").delegate(".action-delete", "click", function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        Swal.fire({
            title: "{{ __('Are you sure ?') }}",
            text: "{{ __('You would not be able to revert this!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ __('Yes, delete it!') }}",
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ml-2'
            },
            buttonsStyling: false,
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ url('/walkin') }}" + '/' + id + '/destroy',
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        showResponseMessage(data);
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            let errors = reject.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr['warning'](value[0],
                                    "{{ __('locale.labels.attention') }}", {
                                        closeButton: true,
                                        positionClass: 'toast-top-right',
                                        progressBar: true,
                                        newestOnTop: true,
                                        rtl: isRtl
                                    });
                            });
                        } else {
                            toastr['warning'](reject.responseJSON.message,
                                "{{ __('locale.labels.attention') }}", {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });
                        }
                    }
                })
            }
        })
    });
    
    
    $(document).on("click",".view-walkin",function(){
    
        let walkin = $(this).data("row");
    
        openWalkinModal(walkin);
    
    });
    
    
    function openWalkinModal(data)
    {

        $('#modalTitle').text('Walk-In Details');
    
        /* Fill fields */
        $("#contact_no_v").val(data.mobile_no);
        $("#cust_name_v").val(data.cust_name);
        $("#location_v").val(data.location);
        $("#visit_purpose_v").val(data.visit_purpose).trigger('change');
        $("#frame_category_v").val(data.frame_category).trigger('change');
        $("#brand_intrest_v").val(data.brand_intrest).trigger('change');
        $("#budget_range_v").val(data.budget_range).trigger('change');
    
        $("input[name='cust_status_v'][value='"+data.final_stage+"']").prop("checked",true);
        $("input[name='eye_test_v'][value='"+data.eye_test+"']").prop("checked",true);
        
        $("#walkout_reason_v").val(data.walkout_reason).trigger('change');
        $("#product_interest_v").val(data.product_interest).trigger('change');
        $("#Lead_priority_v").val(data.Lead_priority).trigger('change');
        $("#Follow_up_date_v").val(data.Follow_up_date);
        
        $("#Frame_v").val(data.Frame);
        $("#Lens_Type_v").val(data.Lens_Type);
        $("#order_amount_v").val(data.order_amount);
        $("#payment_mode_v").val(data.payment_mode);
        $("#delivery_date_v").val(data.delivery_date);
        
        $("#repair_product_V").val(data.repair_product).trigger('change');
        $("#complaint_v").val(data.complaint).trigger('change');
        $("#product_condition_v").val(data.product_condition).trigger('change');
        $("input[name='repair_type_v'][value='"+data.repair_type+"']").prop("checked",true);
        $("#repair_delivery_date_v").val(data.repair_delivery_date);
        
        var preview = document.getElementById("repairPhotoPreview");

            if(preview)
            {
                preview.innerHTML = "";
            
                if(data.upload_photo){
            
                    var img = document.createElement("img");
            
                    img.src = `/speckarts/storage/${data.upload_photo}`;
            
                    img.style.maxWidth = "120px";
                    img.className = "img-thumbnail";
            
                    preview.appendChild(img);
                }
            }
                
        /* show sections */
        $('#pdiv_v, #wdiv_v, #rdiv_v').hide();

        if(data.final_stage == "PURCHASED"){
            $('#pdiv_v').show();
        }
        else if(data.final_stage == "WALKOUT"){
            $('#wdiv_v').show();
        }
        else if(data.final_stage == "REPAIRING"){
            $('#rdiv_v').show();
        }
    
        /* make form readonly */
        $("#walkinForm input, #walkinForm select, #walkinForm textarea").prop("disabled", true);
        $("#walkinviewModal").modal("show");
    }
    
    
    function addWalkinModal()
    {
        $("#walkinForm")[0].reset();
    
        $('#modalTitle').text('Add Walk-In');
    
        $('#pdiv').hide();
        $('#wdiv').hide();
        $('#rdiv').hide();
    
        $("#walkinForm input, #walkinForm select, #walkinForm textarea").prop("disabled", false);
    
        $("#walkinModal").modal("show");
    }
    
    function formatDate(dateString)
    {
        if(!dateString) return '';
    
        let d = new Date(dateString);
        let day = ("0" + d.getDate()).slice(-2);
        let month = ("0" + (d.getMonth()+1)).slice(-2);
        let year = d.getFullYear();
    
        return day + "-" + month + "-" + year;
    }
    
    
    function loadFollowupList()
    {
        let store_id = $("#f_store_id").val();
        let date = $("#f_date").val();
    
        $.ajax({
            url:"{{ route('admin.followup.list') }}",
            type:"POST",
            data:{
                store_id:store_id,
                date:date,
                _token:"{{ csrf_token() }}"
            },
            success:function(res){
    
                let html = "";
                let i = 1;
    
                if(res.length > 0){
    
                    res.forEach(function(row){
    
                        html += `
                        <tr>
                            <td>${i++}</td>
                            <td>
                                Visit : ${formatDate(row.walkin_date)}<br>
                                Followup : ${formatDate(row.Follow_up_date)}
                            </td>
                            <td>
                                ${row.cust_name ?? ''} <br>
                                ${row.mobile_no ?? ''}
                            </td>
                            <td>${row.walkout_reason ?? ''}</td>
                            <td>
                                <input type="text"
                                    class="form-control"
                                    value="${row.call_status ?? ''}"
                                    onchange="updateFollowup(${row.id},'call_status',this.value)">
                            </td>
            
                            <td>
                                <textarea class="form-control"
                                    onchange="updateFollowup(${row.id},'customer_response',this.value)">${row.customer_response ?? ''}</textarea>
                            </td>
                            <td>
                                <select class="form-control"
                                    onchange="updateFollowup(${row.id},'followup_response',this.value)">
                            
                                    <option value="">Select</option>
                            
                                    <option value="Pending Follow-ups" 
                                        ${row.followup_response == 'Pending Follow-ups' ? 'selected' : ''}>
                                        Pending Follow-ups
                                    </option>
                            
                                    <option value="Calls Done Today" 
                                        ${row.followup_response == 'Calls Done Today' ? 'selected' : ''}>
                                        Calls Done Today
                                    </option>
                            
                                    <option value="Converted Customers" 
                                        ${row.followup_response == 'Converted Customers' ? 'selected' : ''}>
                                        Converted Customers
                                    </option>
                            
                                </select>
                            </td>
                            <td>${row.staff_name ?? ''}</td>
                            <td>${row.store_name ?? ''}</td>
                            
                        </tr>`;
                    });
    
                }else{
    
                    html = `<tr><td colspan="10" class="text-center">No Record Found</td></tr>`;
                }
    
                $("#followuplisttable").html(html);
            }
        });
    }
    
    $('#walkinFollowModal').on('shown.bs.modal', function () {
        loadFollowupList();
    });
    
    
    $("#f_store_id, #f_date").change(function(){
        loadFollowupList();
    });
    
    
    function updateFollowup(id, column, value)
    {
        $.ajax({
    
            url: "{{ route('admin.followup.update') }}",
            type: "POST",
    
            data: {
                id: id,
                column: column,
                value: value,
                _token: "{{ csrf_token() }}"
            },
    
            success: function(res){
    
                if(res.status == 1)
                {
                    $.toaster({
                        priority: 'success',
                        title: 'Success',
                        message: 'Walkin Followup Updated Done.',
                        timeout: 3000
                    });
                
                    console.log("Updated Successfully");
                }
    
            }
        });
    }
    
    
    
    function walkinupdateModal(id, walkintype) 
    {
        document.getElementById('modalTitles').innerText = 'Update Walkin Customer';
        document.getElementById('walkin_id').value = id;
        document.getElementById('walkintype').value = walkintype;
    
        // hide all sections first
        $("#fibdiv").hide();
        $("#pdiv").hide();
        $("#wdiv").hide();
        $("#rdiv").hide();
    
        if(walkintype === 'PURCHASED')
        {
            $("#fibdiv").show();
            $("#pdiv").show();
        }
        else if(walkintype === 'WALKOUT')
        {
            $("#wdiv").show();
        }
        else if(walkintype === 'REPAIRING')
        {
            $("#rdiv").show();
        }
    
        $('#walkinupdatessModal').modal('show');
    }
    
    
    
    $("#walkinupdateForm").submit(function(e) {
    
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        $(".error").text('');
        $(".is-invalid").removeClass("is-invalid");

    
        let walkout_reason = $("#walkout_reason").val();
        let repair_product = $("#repair_product").val();
        let complaint = $("#complaint").val();
        let product_condition = $("#product_condition").val();
        let walkintype = $("#walkintype").val();
    

    
        /* -------------------------
           WALKOUT VALIDATION
        -------------------------*/
        if (walkintype === "Walkout") {
    
            if (walkout_reason === "") {
                $("#walkout_reason").addClass("is-invalid");
                alert("Please select Walkout Reason");
                isValid = false;
            }
        }
    
        /* -------------------------
           REPAIR VALIDATION
        -------------------------*/
        if (walkintype === "Repair") {
    
            if (repair_product === "") {
                $("#repair_product").addClass("is-invalid");
                isValid = false;
            }
    
            if (complaint === "") {
                $("#complaint").addClass("is-invalid");
                isValid = false;
            }
    
            if (product_condition === "") {
                $("#product_condition").addClass("is-invalid");
                isValid = false;
            }
    
            if (!isValid) {
                alert("Please fill all repair details");
            }
        }
    
        if (!isValid) return;
    
        /* -------------------------
           AJAX SUBMIT
        -------------------------*/
    
        let form = $("#walkinupdateForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.update-walkin-record') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
    
            success: function(response) {

            if(response.status === true){
        
                $.toaster({
                    priority: 'success',
                    title: response.message,
                    message: '',
                    timeout: 3000
                });
        
                $("#walkinupdateForm")[0].reset();
        
                // Hide modal if form is inside modal
                $("#walkinupdatessModal").modal('hide');
        
                setTimeout(function(){
                    location.reload();
                },1500);
        
            } else {
        
                alert(response.message);
        
            }
        
        }
    
        });
    
    });
    
    
    
    
    </script>


@endsection


    



