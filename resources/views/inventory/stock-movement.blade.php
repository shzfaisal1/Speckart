@extends('layouts.master')
@section('styles')
<style>
input.loading {
    background-image: url('https://i.imgur.com/6RMhx.gif'); 
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}

.table-responsive {
      overflow-x: auto;
    }
    input.form-control, select.form-control {
      font-size: 0.9rem;
    }
    .removeBtn {
      border: none;
      background: transparent;
      color: red;
      cursor: pointer;
      font-size: 1.2rem;
    }
    .input-group input {
      text-align: center;
    }
    
    .table thead tr th {
        font-size: 12px;
        font-weight: 500 !important;
        color: #000;
    }

</style>  

@endsection
@section('content')
@php
     $usr = Auth::guard()->user();
 @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Stock Moment</h3>
                        
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
                        <select class="form-control select"  id="product_type" name="product_type" style="margin-top:10px">
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
                        <input type="text" class="form-control input" placeholder="Product Code, Product Description" id="search" name="search" style="width: 250px;margin-top: 10px;">
                    </div>
                </div>
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3" style="margin-top:10px">
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
                                    <th class="wd-10p">Store Name</th>
                                    <th class="wd-10p">Description</th>
                                    <th class="wd-10p">Inward Movement</th>
                                    <th class="wd-10p">Outward Movement</th>
                                    <th class="wd-10p">Total Inward</th>
                                    <th class="wd-10p">Total Outward</th>
                                    <th class="wd-10p">Avilable</th>
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


<div class="modal fade" data-backdrop="static" id="stockModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Stock Movement Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                       <strong id="store_name_p"></strong>
                    </div>
                    <div class="col-md-6">
                        <strong id="product_type_p"></strong>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <strong id="product_code_p"></strong>
                    </div>
                    <div class="col-md-9">
                             <strong id="description_p"></strong>
                    </div>
                    
                </div>

                <div class="row">
                    <table class="table table-bordered" id="barcodeTable">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Barcode</th>
                          <th>Inward</th>
                          <th>Outward</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td colspan="4" class="text-center">Loading...</td></tr>
                      </tbody>
                    </table>
                </div>

                
             

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
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
    
    $('.select1').select2({
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
            url: "{{ route('admin.stockmovement-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.product_type = $('#product_type').val(),
                d.store_id = $('#store_id').val(),
                d.search1 = $('#search').val(),
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
           

            {
                "data": "store_name",
                orderable: false,
            },

            {
                "data": "description",
                orderable: false,
            },
            {
                "data": "inward",
                orderable: false,
            },
            
            {
                "data": "outward",
                orderable: false,
            },
            
            {
                "data": "total_inward",
                orderable: false,
            },
            {
                "data": "total_outward",
                orderable: false,
            },
            {
                "data": "balance",
                orderable: false,
            },
            {
                "data": "action",
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
            {
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function (data, type, full)
                {
                    let html = `<a class="pointer"  onclick="openstockModal('` + full['product_type'] + `','` + full['product_code']+ `','` + full['product_details'] + `','` + full['date_from_f'] + `','` + full['date_to_f'] + `','` + full['store_name'] + `','` + full['store_id'] + `')">
                                    <button type="button" class="btn btn-success btn-sm mb-1">View Details</button>
                                </a>'`;
            
                    return html;
                }
            }
            

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

    
    $('.select').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });
    
    function openstockModal(product_type, product_code, product_details,date_from,date_to,store_name,store_id) 
    {
        $('#product_type_p').text('');
        $('#product_code_p').text('');
        $('#description_p').text('');
        $('#store_name_p').text('');
        
        var store_name_text = store_name == 'null' ? '' : ' Store Name : ' + store_name;
        var product_type_text = product_type == 'null' ? '' : ' Product Type : ' + product_type;
        var product_code_text = product_code == 'null' ? '' : '   Product Code : ' + product_code;
        var description_text = product_details == 'null' ? '' : '   Product Description : ' + product_details;
        document.getElementById('modalTitle').innerText = 'Stock Movement Details';

        $('#product_type_p').text(product_type_text);
        $('#product_code_p').text(product_code_text);
        $('#description_p').text(description_text);
        $('#store_name_p').text(store_name_text);
        
        $.ajax({
            url: "{{ route('admin.get-stockdetails') }}",  
            method: 'GET',
            data: { date_from: date_from,date_to: date_to,store_id: store_id
            ,product_type: product_type ,product_code: product_code ,product_details: product_details },
            beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function(response) {
                let tableBody = $('#barcodeTable tbody');
                tableBody.empty(); // Clear old data
    
                if (response.data && response.data.length > 0) {
                     response.data.forEach(function(barcodevalue, index) 
                    {
                         function formatValue(value) {
                          return value === null || value === undefined || value === "" ? "-" : value;
                        }
    
                        let row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${barcodevalue.barcode_no}</td>
                                <td>${barcodevalue.inward_status}</td>
                                <td>${barcodevalue.outward_status}</td>

                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="5" class="text-center">No Barcode found.</td></tr>');
                }
            },
            error: function() 
            {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch barcode details.',
                    timeout: 3000
                });
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        });

    
        $('#stockModal').modal('show');
    }
    

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
    
    
    $('#checkBarcodeBtn').on('click', function () 
    {
        let barcode = $('#barcode_no').val().trim();
        let product_type = $('#product').val();
        let company = $('#company').val();



        if (barcode === '') {
            $.toaster({ priority: "warning", title: "Error..!", message: "Please enter barcode." });
            return;
        }

        if (product_type === '') {
            $.toaster({ priority: "warning", title: "Error..!", message: "Please select product." });
            return;
        }
        
        if (company === '') {
            $.toaster({ priority: "warning", title: "Error..!", message: "Please select company." });
            return;
        }

        $.ajax({
            url: "{{ route('admin.check-barcode') }}", 
            type: "POST",
            data: 
            {
                _token: "{{ csrf_token() }}",
                barcode: barcode,
                product_type: product_type,
                company: company,
                audit: '1',
            },
            success: function (response) 
            {
                if (response.valid) 
                {
                    let currentText = $('#valid_barcode').val();
                    if (!currentText.includes(barcode)) {
                        $('#valid_barcode').val(currentText + (currentText ? "\n" : "") + barcode);
                    }
                    $('#barcode_no').val('').focus();
                } 
                else 
                {
                    $.toaster({ priority: "warning", title: "Error..!", message: "Invalid barcode." });
                }
            },
            error: function () {
                $.toaster({ priority: "warning", title: "Error..!", message: "Server error. Try again." });
            }
        });
    });
    
    $("#auditForm").submit(function (e) {
        e.preventDefault();
    
        let isValid = true;
        let class_name = '';
    
        // Reset old errors
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        // Collect fields
        let product_type = document.getElementById("product" + class_name).value.trim();
        let barcode_no = document.getElementById("barcode_no" + class_name).value.trim();
        let company     = document.getElementById("company" + class_name).value.trim();

    
        // Validation
        if (product_type === "") {
            document.getElementById("productError" + class_name).textContent = "Select Product.";
            document.getElementById("product" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (company === "") {
            document.getElementById("companyError" + class_name).textContent = "Select Company.";
            document.getElementById("company" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        let rawBarcodes = $('#valid_barcode').val().trim();
        let barcodeArray = rawBarcodes
            .split('\n')
            .map(b => b.trim())
            .filter(b => b !== '');
    
        if (barcodeArray.length === 0) {
            document.getElementById("valid_barcodeError" + class_name).textContent = "Audit barcode required.";
            document.getElementById("valid_barcode" + class_name).classList.add("is-invalid");
            isValid = false;
        }

        if (!isValid) return;
        
        // Submit via AJAX
        let form = $("#auditForm")[0];
        let data = new FormData(form);
        data.append("auditbarcodes", JSON.stringify(barcodeArray));
        data.delete("valid_barcode");
        data.delete("barcode_no");
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.add-audit-record') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function (response) {
                if ($.isEmptyObject(response.error)) {
                    $.toaster({
                        priority: "success",
                        title: response.success,
                        message: "Audit Successfully Done."
                    });
                    window.location.href = "{{ route('admin.inventory-audit') }}";
                }
                else 
                {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    });
    
        
</script>




@endsection
