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
                        <h3>Purchase Return Report</h3>
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
                        <input type="hidden" class="form-control" id="date_to" name="date_to">
                    </div> 
                </div>    
                <div class="col-lg-3" style="margin-top:10px">
                    <div class="form-group">
                        <select class="form-control select"  id="product_type" name="product_type">
                            <option value="">Select Product </option>
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
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Product Code,Description" id="search" name="search" style="width: 265px;">
                    </div>
                </div> 

                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3">
                     <div class="form-group">
                        <select class="form-control select" style="height: 32px !important;margin-top:10px" id="store_id" name="store_id">
                            <option value="">Select  Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-lg-3" style="margin-top:10px">
                     <div class="form-group">
                        <select class="form-control select" style="height: 32px !important;margin-top:10px" id="supplier_name" name="supplier_name">
                            <option value="">Select  Supplier</option>
                          <?php $tbl_suppliers =  DB::table("tbl_suppliers")->where('status',1)->get();  ?>
                           @foreach($tbl_suppliers as $tbl_suppliers)
                            <option value="{{$tbl_suppliers->supplier_company}}">{{$tbl_suppliers->supplier_company}} </option>
                          @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3" style="margin-top:10px">
                <button type="button" class="btn btn-success" id="bulkexport">Download Report</button>
                </div>

            </div> 
            <hr/>
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
                    </div>
                    <div id="purchasedataload"></div>
               </div>
            </div>
            <div class="row">
                <table class="table datatables-basic w-100">
                    <thead>
                        <tr>
                            <th class="wd-10p">Sr.No</th>
                            <th class="wd-10p">Store</th>
                            <th class="wd-10p">Supplier Details</th>
                            <th class="wd-10p">Purchase Details</th>
                            <th class="wd-10p">Product Details</th>
                            <th class="wd-10p">Description</th>
                            <th class="wd-10p">Basic Price</th>
                            <th class="wd-10p">GST</th>
                            <th class="wd-10p">Purchase Price</th>
                            <th class="wd-10p">Qty</th>
                            <th class="wd-10p">Total Amount</th>
                            <th class="wd-10p">Sales Price</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() 
{
    $('.select').select2({
      allowClear: true
    });
    
    get_purchasedata();
});

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
        console.log("Start or end date is in the current month.");
    } else {
        console.log("Neither date is in the current month.");
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

function get_purchasedata()
{

    $.ajax({
	   type: "POST",
	   url: "{{ route('admin.get-purchasereturndata-report') }}",
	   data: {
	       product_type: $('#product_type').val(),
	       date_from: $('#date_from').val(),
	       date_to: $('#date_to').val(),
	       search: $('#search').val(),
	       store_id: $('#store_id').val(),
	       supplier_name: $('#supplier_name').val(),
	       _token: "{{ csrf_token() }}"
	   },
	   dataType: "json",
	   beforeSend: function () {
            $("#ajaxLoader").show(); 
        },
	   success: function (success)  
	   {
		    var main_data=success.purchasedata_section;
		    $('#purchasedataload').empty();
		    if (success.status === 'success') 
            {
                $('#purchasedataload').show();
                $('#purchasedataload').append(main_data);
                
                $('#global-loader').hide();
            }
            else
            {
                get_purchasedata();
            }
    	},
    	complete: function () 
    	{
            $("#ajaxLoader").fadeOut(); 
        }
   });
}


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
            url: "{{ route('admin.purchase-return-report-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
               d.product_type = $('#product_type').val(),
    	       d.search1 = $('#search').val(),
    	       d.date_from = $('#date_from').val(),
    	       d.date_to = $('#date_to').val(),
    	       d.store_id = $('#store_id').val(),
    	       d.supplier_name = $('#supplier_name').val(),
               d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "store_name",
                orderable: false,
            },
            {
                "data": "supplier",
                orderable: false,
            },

            {
                "data": "purchase_details",
                orderable: false,
            },
            
            {
                "data": "product_details",
                orderable: false,
            },
            
            {
                "data": "description",
                orderable: false,
            },
            
            {
                "data": "basic_price",
                orderable: false,
            },
            {
                "data": "gst",
                orderable: false,
            },
            {
                "data": "purchase_price",
                orderable: false,
            },
            {
                "data": "qty",
                orderable: false,
            },
   
            {
                "data": "total_amount",
                orderable: false,
            },
            {
                "data": "sale_price",
                orderable: false,
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
            

        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        language: {
            paginate: 
            {
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
        get_purchasedata();
    });
    
    $('.select').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
        get_purchasedata();
    });

    
</script>

<script>
        $('#bulkexport').on('click', function() {

            $('#processingLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('admin.purchase-return-excel-download') }}",
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
                return matches != null && matches[1] ? matches[1].replace(/['"]/g, '') : null;
            }


        });
    </script>

@endsection
