@extends('layouts.master')
@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
.report-card {
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      text-align: center;
      padding: 30px 15px;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .report-card:hover {
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transform: translateY(-3px);
    }
    .report-card i {
      font-size: 40px;
      color: #00484a;
      margin-bottom: 15px;
    }
    .report-title {
      font-weight: 600;
      font-size: 16px;
      color: #333;
    }
    
    .table thead tr th {
        font-size: 12px;
        font-weight: 500 !important;
        color: #000; 
    }
    
    #chart-pie2,
    #chart-pie3 {
        width: 100%;
        max-width: 500px;   /* Controls max chart width */
        height: 400px;      /* Keeps a consistent size */
        margin: auto;
    }
    
    @media (max-width: 992px) {
        #chart-pie2,
        #chart-pie3 {
            max-width: 100%;
            height: 300px;  /* Slightly smaller on tablets */
        }
    }
    
    @media (max-width: 600px) {
        #chart-pie2,
        #chart-pie3 {
            height: 250px;  /* Smaller height for mobile */
        }
    }
</style>  

@endsection
@section('content')
@php
    $usr = Auth::guard()->user();
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
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Sales Report</h3>
                    </div>
                </div>
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
    
});


</script> 
<script>

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
    $('#bulkexport').on('click', function () {
    
        $('#processingLoader').show();
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.sale-excel-download') }}",
            data: {
                store_id: $('#store_id').val(),
                product_type: $('#product_type').val(),
                sale_person: $('#sale_person').val(),
                sale_type: $('#sale_type').val(),
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
    
            xhrFields: {
                responseType: 'blob'
            },
    
            success: function (blobData, status, xhr) {
    
                let filename = getFileNameFromDisposition(
                    xhr.getResponseHeader('Content-Disposition')
                ) || 'pending_orders.xlsx';
    
                let blob = new Blob([blobData], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
    
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;
    
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
    
                $('#processingLoader').hide();
            },
    
            error: function (xhr, status, error) {
                console.error("Download failed:", error);
                alert('Something went wrong while downloading file');
                $('#processingLoader').hide(); // ✅ FIX
            }
        });
    
        function getFileNameFromDisposition(disposition) {
            if (!disposition) return null;
    
            const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            const matches = filenameRegex.exec(disposition);
    
            return (matches != null && matches[1])
                ? matches[1].replace(/['"]/g, '')
                : null; // ✅ FIX
        }
    
    });
    </script>

@endsection
