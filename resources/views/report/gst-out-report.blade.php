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
                        <h3>GST Out Report</h3>
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
                        <select class="form-control select"  id="date_type" name="date_type">
                            <option value="">Date Type </option>
                            <option value="0">Order Date</option>
                            <option value="1">Created Date</option>
                        </select>
                    </div>
                </div>
                @php
                    $firstDay = date('Y-m-01'); 
                    $lastDay  = date('Y-m-t'); 
                @endphp
                
                <div class="row align-items-center">
                    <div class="col-lg-6 d-flex align-items-center">
                        <label class="form-label mb-0 me-2">From Date</label>
                        <input type="date"  class="form-control form-control-sm"  name="from_date"  id="date_from"  value="{{ $firstDay }}">
                    </div>
                
                    <div class="col-lg-6 d-flex align-items-center">
                        <label class="form-label mb-0 me-2">To Date</label>
                        <input type="date" class="form-control form-control-sm"  name="to_date" id="date_to" value="{{ $lastDay }}">
                    </div>
                
                </div>
                
                <div class="col-lg-2 d-flex align-items-center mt-2">
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
               
                <div class="col-lg-2" style="margin-top:10px;" id="download">
                   <button type="button" class="btn btn-success" id="bulkexport">Download Report</button>
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
                            <tr>
                                <th class="wd-5p">Sr.No</th>
                                <th class="wd-15p">Order No</th>
                                <th class="wd-15p">Order Datetime</th>
                                <th class="wd-20p">Party Name</th>
                                <th class="wd-10p">GST No</th>
                                <th class="wd-10p">State</th>
                                <th class="wd-10p">Tax Type</th>
                                <th class="wd-10p">Product Type</th>
                                <th class="wd-10p">HSN/SAC  </th>
                                <th class="wd-10p">Qty</th>
                                <th class="wd-10p">Retail Price  </th>
                                <th class="wd-10p">Base Price  </th>
                                <th class="wd-10p">GST %  </th>
                                <th class="wd-10p">SGST / CGST  </th>
                                <th class="wd-10p">IGST  </th>
                                <th class="wd-10p">Total GST  </th>
                                <th class="wd-10p">Total Net AMount  </th>
                            </tr>
                        </thead>
                    </table>
                </div>
        
               </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')

<script>
$(document).ready(function() 
{
    $('.select').select2({
      allowClear: true
    });
});
</script> 
<script>
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
            url: "{{ route('admin.gstinput-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d.store_id = $('#store_id').val(),
                d.date_type = $('#date_type').val(),
                d.sort_by = $('input[name="sort_by"]:checked').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "bill_no",
                orderable: false,
            },
            {
                "data": "created_at",
                orderable: false,
            },
            {
                "data": "purchase_date",
                orderable: false,
            },

            {
                "data": "supplier_name",
                orderable: false,
            },
            {
                "data": "gst_no",
                orderable: false,
            },

            {
                "data": "state",
                orderable: false,
            },
            
             {
                "data": "qty",
                orderable: false,
            },

            {
                "data": "hsn_code",
                orderable: false,
            },
            {
                "data": "base_value",
                orderable: false,
            },
            {
                "data": "gst",
                orderable: false,
            },
            {
                "data": "gst_amount",
                orderable: false,
            },
            {
                "data": "igst_amount",
                orderable: false,
            },
            {
                "data": "total_gst",
                orderable: false,
            },
            {
                "data": "total_purchase",
                orderable: false,
            },
        ],

        searchDelay: 1500,
        columnDefs: [{
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0
            },
     

        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

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
    
    
    $('#bulkexport').on('click', function () {
    
        $('#processingLoader').show();
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.gstinput-excel-download') }}",
            data: {
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                store_id: $('#store_id').val(),
                date_type: $('#date_type').val(),
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
