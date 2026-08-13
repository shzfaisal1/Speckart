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
            <a class="nav-link" href="{{route('admin.walkin-dashboard')}}">
                <i class="fa fa-sign-in"></i>
                Walk-In Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link "  href="{{route('admin.audit-dashboard')}}">
                <i class="fa fa-search"></i>
                Mystery Audit
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link "  href="{{route('admin.nps-dashboard')}}">
                <i class="fa fa-smile-o"></i>
                NPS Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link active"  href="{{route('admin.sale-dashboard')}}">
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

<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                
            </div>
            <div class="row mb-3">
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3" style="margin-top:10px">
                     <div class="form-group">
                        <select class="form-control select" style="height: 32px !important;margin-top:10px" id="store_id" name="store_id">
                            <option value=""> Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-lg-2" style="margin-top:10px">
                    <div class="form-group">
                        <select class="form-control select"  id="product_type" name="product_type">
                            <option value="">Product Type </option>
                            <option value="Frame">Frame</option>
                            <option value="Glass">Glass</option>
                            <option value="Goggles">Goggles</option>
                            <option value="Lens">Contact Lens</option>
                            <option value="Solution">Solution</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3" style="margin-top:10px">
                    <select class="form-control select" style="height: 32px !important;" id="sale_person" name="sale_person">
                        <option value="">Sales Person</option>
                      <?php  $tbl_users =  DB::table("users")->where('status',1)->get();  ?>
                       @foreach($tbl_users as $tbl_users)
                        <option value="{{$tbl_users->id}}">{{$tbl_users->name}} / ({{$tbl_users->user_type}} {{$tbl_users->staff_id}})</option>
                      @endforeach
                    </select>
                </div> 
                <div class="col-lg-2" style="margin-top:10px">
                    <select class="form-control select" style="height: 32px !important;" id="sale_type" name="sale_type">
                      <option value="">Sales Type</option>    
                      <option value="0">B2C</option>    
                      <option value="1">B2B</option>
                    </select>
                </div>
                
                 <div class="col-lg-2" style="margin-top:10px">
                    <select class="form-control select" style="height: 32px !important;" id="sale_status" name="sale_status">
                      <option value="">Status</option>    
                      <option value="0">Pending</option>    
                      <option value="1">Confirm</option>
                      <option value="2">Return</option>
                      <option value="3">Deleted</option>
                    </select>
                </div>
                
                <div class="col-lg-2" style="margin-top:10px">
                    <select class="form-control select" style="height: 32px !important;" id="search_by" name="search_by">
                      <option value="">Search By</option>    
                      <option value="Customer Name">Customer Name</option>    
                      <option value="Mobile Number">Mobile Number</option>
                      <option value="Membership ID">Membership ID</option>
                      <option value="Barcode">Barcode</option>
                      <option value="Order Numbe">Order Number</option>
                      <option value="Patient Name">Patient Name</option>
                      <option value="Doctor Name">Doctor Name</option>
                      <option value="Product Code">Product Code</option>
                      <option value="Description">Description</option>
                      <option value="Referral Code">Referral Code</option>
                    </select>
                </div>
                <div class="col-lg-3" style="margin-top:10px">
                    <input type="text" class="form-control" id="search_text" placeholder="Search text here..." name="search_text" >
                </div>
                <div class="col-lg-4 d-flex align-items-center mt-2">
                    <label class="form-label mb-0 me-2">From Date</label>
                    <input type="date" class="form-control me-2" name="from_date" id="from_date" style="height:32px">
                
                    <label class="form-label mb-0 me-2">To Date</label>
                    <input type="date" class="form-control" name="to_date" id="to_date" style="height:32px">
                </div>
                
                <div class="col-lg-3 d-flex align-items-center mt-2">
                    <label class="form-label mb-0 me-2"> Retail Price Range</label>
                    <input type="text" class="form-control" id="price_from"  name="price_from" >
                
                    <input type="text" class="form-control" id="price_to"  name="price_to" >
                </div>
                <div class="col-lg-3 d-flex align-items-center mt-2">
                    <label class="form-label mb-0 me-2">Customer's GST Number</label>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gst_no" id="inlineRadio8" value="1">
                        <label class="form-check-label" for="inlineRadio8">Yes</label>
                    </div>
                
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gst_no" id="inlineRadio9" value="0">
                        <label class="form-check-label" for="inlineRadio9">No</label>
                    </div>
                </div>

                <div class="col-lg-3 d-flex align-items-center mt-2">
                    <label class="form-label mb-0 me-2">Sort Type</label>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="sort_by" id="inlineRadio2" value="1">
                        <label class="form-check-label" for="inlineRadio2">ASC</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="sort_by" id="inlineRadio3" value="0">
                        <label class="form-check-label" for="inlineRadio3">DESC</label>
                    </div>
                </div>
                <div class="col-lg-2" style="margin-top:10px">
                   <button type="button" class="btn btn-success" id="searchsales">Search</button>
                </div>
                <div class="col-lg-2" style="margin-top:10px;display:none" id="download">
                   <button type="button" class="btn btn-success" id="bulkexport">Download Report</button>
                </div>

            </div> 
            <div id="saledataload"></div>
        </div>
    </div>
</section>

	 
    
@endsection

@section('scripts')
<!-- D3 (required) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.16.0/d3.min.js"></script>

<!-- C3 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.js"></script>
<script>
$(document).ready(function() 
{
    $('.select').select2({
      allowClear: true
    });

    // Current Date Set
    let today = new Date().toISOString().split('T')[0];

    $('#from_date').val(today);
    $('#to_date').val(today);

    // Auto Search Current Date Data
    $('#searchsales').trigger('click');
    
});


$('#searchsales').on('click', function() {
    $.ajax({
	   type: "POST",
	   url: "{{ route('admin.get-salesdata-report') }}",
	   data: {
	       store_id: $('#store_id').val(),
	       product_type: $('#product_type').val(),
	       sale_person: $('#sale_person').val(),
	       sale_type: $('#sale_type').val(),
	       sale_status: $('#sale_status').val(),
	       search_by: $('#search_by').val(),
		   search_text: $('#search_text').val(),
		   from_date: $('#from_date').val(),
		   to_date: $('#to_date').val(),
		   price_from: $('#price_from').val(),
		   price_to: $('#price_to').val(),
		   gst_no: $('input[name="gst_no"]:checked').val(),
           sort_by: $('input[name="sort_by"]:checked').val(),
	       _token: "{{ csrf_token() }}"
	   },
	   dataType: "json",
	   beforeSend: function () {
            $("#ajaxLoader").show(); 
        },
	   success: function (success)  
	   {
		    var main_data=success.saledata_section;
		    $('#saledataload').empty();
		    if (success.status === 'success') 
            {
                $('#saledataload').show();
                $('#saledataload').append(main_data);
            }
            
            $('#download').show();
            
    	},
    	complete: function () 
    	{
            $("#ajaxLoader").fadeOut(); 
        }
   });
});


</script>

<script>
    $('#bulkexport').on('click', function() 
    {
    
        $('#processingLoader').show();
        $.ajax({
            type: "POST",
            url: "{{ route('admin.purchase-excel-download') }}",
            data: {
               product_type: $('#product_type').val(),
    	       search: $('#search').val(),
    	       supplier_name: $('#supplier_name').val(),
    	       date_from: $('#date_from').val(),
    	       date_to: $('#date_to').val(),
    	        store_id: $('#store_id').val(),
                _token: "{{ csrf_token() }}"
            },
            xhrFields: {
                responseType: 'blob' // Tells jQuery to treat response as binary
            },
            success: function(blobData, status, xhr) {
                const filename = getFileNameFromDisposition(xhr.getResponseHeader(
                    'Content-Disposition')) || 'users.xlsx';
                const blob = new Blob([blobData], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
    
                $('#processingLoader').hide();
            },
            error: function(xhr, status, error) {
                console.error("Download failed:", error);
            }
    
        });
    
        function getFileNameFromDisposition(disposition) {
            if (!disposition) return null;
            const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            const matches = filenameRegex.exec(disposition);
            return matches != null && matches[1] ? matches[1].replace(/['"]/g, '') null;
        }
    
    
    });
    
    
    </script>

@endsection
