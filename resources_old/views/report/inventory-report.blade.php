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
                        <h3>Inventory Report</h3>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
              
                <div class="col-lg-3">
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
                 <div class="col-lg-3">
                    <div class="form-group">
                        <div class="d-flex">
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="inv_status" id="inlineRadio1" value="1" checked>
                              <label class="form-check-label" for="inlineRadio1">All</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="inv_status" id="inlineRadio2" value="2">
                              <label class="form-check-label" for="inlineRadio2">Positive </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="inv_status" id="inlineRadio3" value="3">
                              <label class="form-check-label" for="inlineRadio3">Negative </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Product Code,Description" id="search" name="search" style="width: 265px;">
                    </div>
                </div> 
                
                
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3">
                     <div class="form-group">
                        <select class="form-control select" style="height: 32px !important;" id="store_id" name="store_id">
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
                    <div id="inventorydataload"></div>
               </div>
            </div>
            <div class="row">
                <table class="table datatables-basic w-100">
                    <thead>
                        <tr>
                            <th class="wd-10p">Sr.No</th>
                            <th class="wd-10p">Product</th>
                            <th class="wd-10p">Product ID</th>
                            <th class="wd-10p">Product Code</th>
                            <th class="wd-10p">Description</th>
                            <th class="wd-10p">Available Quantity</th>
                            <th class="wd-10p">Store</th>
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
    
    get_inventorydata();
});
</script> 
<script>

function get_inventorydata()
{
    var inv_status = $('input[name="inv_status"]:checked').val();
    
    $.ajax({
	   type: "POST",
	   url: "{{ route('admin.get-inventorydata-report') }}",
	   data: {
	       product_type: $('#product_type').val(),
	       stid: $('#store_id').val(),
	       search: $('#search').val(),
	       inv_status: inv_status,
	       _token: "{{ csrf_token() }}"
	   },
	   dataType: "json",
	   beforeSend: function () {
            $("#ajaxLoader").show(); 
        },
	   success: function (success)  
	   {
		    var main_data=success.inventorydata_section;
		    $('#inventorydataload').empty();
		    if (success.status === 'success') 
            {
                $('#inventorydataload').show();
                $('#inventorydataload').append(main_data);
                
                $('#global-loader').hide();
            }
            else
            {
                get_inventorydata();
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
            url: "{{ route('admin.inventory-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
               d.product_type = $('#product_type').val(),
    	       d.store_id = $('#store_id').val(),
    	       d.search = $('#search').val(),
    	       d.inv_status =  $('input[name="inv_status"]:checked').val(),
               d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "product_type",
                orderable: false,
            },
            {
                "data": "product_id",
                orderable: false,
            },
            {
                "data": "product_code",
                orderable: false,
            },
            {
                "data": "description",
                orderable: false,
            },
            {
                "data": "qty",
                orderable: false,
            },
            {
                "data": "store_name",
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
        get_inventorydata();
    });
    
    $('.select').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
        get_inventorydata();
    });

    $('.form-check-input').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
        get_inventorydata();
    });
</script>

<script>
        $('#bulkexport').on('click', function() {

            $('#processingLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('admin.inventory-excel-download') }}",
                data: {
                    product_type: $('#product_type').val(),
                    store_id: $('#store_id').val(),
                    search: $('#search').val(),
                    inv_status: $('input[name="inv_status"]:checked').val(),
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
