@extends('layouts.master')
@php
     $usr = Auth::guard()->user();
 @endphp
@section('content')
<style>
    .domestic-orders-date
    {
        padding-top: 0px;
    }
    
    .section-title
    {
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
            <a class="nav-link "  href="{{route('admin.sale-dashboard')}}">
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
            <a class="nav-link active"  href="{{route('admin.counting-dashboard')}}">
                <i class="fa fa-google-wallet"></i>
                Product Counting Dashboard
            </a>
        </li>
    </ul>
</div>

<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card" style="margin-top:10px">
            <div class="card-body" style="padding: 5px 10px;">
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

                
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3">
                    <select class="form-control select" style="height: 32px !important;" id="store_id" name="store_id">
                        <option value="">Select  Store</option>
                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                       @foreach($tbl_store as $tbl_store)
                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                      @endforeach
                    </select>
                </div>
                @endif  
                 <div class="col-lg-3">
                    <div class="domestic-orders-date">
                        <div class="form-group">
                            <input type="text" class="form-control input" placeholder="Search by product" id="search" name="search" >
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <a href="#" class=" btn" data-toggle="modal" data-target="#countingModal">
                        <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                        Add Counting Record
                    </a>
                </a>    
               </div>
               </div>
                <div class="row">
                    <div class="col-12">
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
                            <table class="table datatables-basic  table-bordered">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">Store Name</th>
                                        <th class="wd-15p">Counting Date</th>
                                        <th class="wd-15p">Product</th>
                                        <th class="wd-15p">Code</th>
                                        <th class="wd-15p">Product Details</th>
                                        <th class="wd-15p">Count Record</th>
                                        <th class="wd-15p">Inventory Record</th>
                                        <th class="wd-15p">Total Difference </th>
                                        <th class="wd-15p">Action </th>
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

<div class="modal fade" data-backdrop="static" id="countingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Counting Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="countingForm" method="POST" method="POST" enctype="multipart/form-data">
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
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Date <span class="text-danger">*</span></label> <br>
                                    <input type="hidden" class="form-control" id="counting_date" name="counting_date">
                                    <div id="reportrange1" class="pull-left"
                                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                        <span></span> <b class="caret"></b>
                                    </div>
                                    
                                    <span class="error badge text-danger" id="counting_dateError"></span>
                                </div>
                                 @if($usr->roles[0]->name == 'Admin')
                                    <div class="col-md-6">
                                        <label for="">Select Store <span class="text-danger">*</span></label><br>
                                        <select class="form-control" style="height: 32px !important;" id="store_ids" name="store_ids">
                                            <option value="">Select  Store</option>
                                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                                           @foreach($tbl_store as $tbl_store)
                                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    @endif 
                            </div>  
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <table border="1" class="table basic w-100" id="productTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                              <th style="color:#000">Product Type</th>
                                              <th style="color:#000">Product Code</th>
                                              <th style="color:#000">Product Description</th>
                                              <th style="color:#000">Total Count</th>
                                              <th style="color:#000">Action</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                        </tbody>
                                    </table>
                                    <button type="button" onclick="addRow()">Add More</button>
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
    $(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
  });
   $(function () {

    var selectedDate = moment();

    function cb1(date) {

        $('#reportrange1 span').html(
            date.format('MMMM D, YYYY')
        );

        // CORRECT ID
        $('#counting_date').val(
            date.format('YYYY-MM-DD')
        );

    }

    $('#reportrange1').daterangepicker({

        singleDatePicker: true,
        showDropdowns: true,
        startDate: selectedDate,

        locale: {
            format: 'MMMM D, YYYY'
        }

    }, cb1);

    // INITIAL VALUE SET
    cb1(selectedDate);

});
    

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
            url: "{{ route('admin.counting-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.search1 = $('#search').val(),
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d.store_id = $('#store_id').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "store_name",
                orderable: false,
            },
            {
                "data": "counting_date",
                orderable: false,
            },

            {
                "data": "product_type",
                orderable: false,
            },
            {
                "data": "product_code",
                orderable: false,
            },
            {
                "data": "product_details",
                orderable: false,
            },
            {
                "data": "count_record",
                orderable: false,
            },
            {
                "data": "available_quantity",
                orderable: false,
            },
            {
                "data": "missing_total",
                orderable: false,
            },


            {
                "data": "action",
                orderable: false,
                searchable: false
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
                render: function(data, type, full) 
                {
                   
                     return (`<a class="action-delete dropdown-item" href="#"  data-id="` + full['encryptedId'] + `">Delete</a>`);
                    
                }      
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
                        url: "{{ url('/countingrecord') }}" + '/' + id + '/destroy',
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


function addRow() {
  const table = document.getElementById("productTable").getElementsByTagName("tbody")[0];

  const row = table.insertRow();

  const typeCell = row.insertCell(0);
  const codeCell = row.insertCell(1);
  const detailsCell = row.insertCell(2);
  const countCell = row.insertCell(3);
  const actionCell = row.insertCell(4);

  typeCell.innerHTML = `
   <select class="form-control product-type" style="height: 32px !important;" name="product_type[]">
        <option value="">Select Product</option>
        <option value="Frame">Frame</option>
        <option value="Goggles">Goggles</option>
        <option value="Lens">Contact Lens</option>
        <option value="Solution">Solution</option>
    </select>`;
  codeCell.innerHTML = `<input type="text" class="form-control product-code" name="product_code[]">
  <div class="suggestion-box list-group" style="display:none; position:absolute; z-index:1000;"></div>
  `;
  detailsCell.innerHTML = `<textarea type="text" class="form-control" name="product_details[]" placeholder="Description"></textarea>`;
  countCell.innerHTML = `<input type="text" class="form-control" name="count_record[]" placeholder="Count">`;
  actionCell.innerHTML = `<button onclick="removeRow(this)">Remove</button>`;
}

function removeRow(button) {
  const row = button.parentNode.parentNode;
  row.remove();
}


$(document).on('keyup', 'input[name="product_code[]"]', function () {

    let $input = $(this);
    let productCode = $input.val();

    // Get product type from same row
    let productType = $input.closest('tr').find('.product-type').val();

    if (productCode.length >= 2 && productType !== '') {

        $.ajax({
            url: "{{ route('admin.get-product-wise-code') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                product_type: productType,
                query: productCode
            },

            success: function (response) {

                let suggestionBox = $input.siblings('.suggestion-box');

                suggestionBox.empty();

                if (Array.isArray(response) && response.length > 0) {

                    response.forEach(function (item) {

                        suggestionBox.append(`
                            <a href="#" 
                               class="list-group-item list-group-item-action suggestion-item"
                               data-value="${item.productdetails}">
                               ${item.productdetails}
                            </a>
                        `);

                    });

                } else {

                    suggestionBox.append(`
                        <div class="list-group-item text-muted">
                            No results found
                        </div>
                    `);

                }

                suggestionBox.show();
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });

    } else {

        $input.siblings('.suggestion-box').hide();

    }

});


$(document).on('click', '.suggestion-item', function (e) {

    e.preventDefault();

    let value = $(this).data('value');

    let suggestionBox = $(this).closest('.suggestion-box');

    suggestionBox.siblings('input[name="product_code[]"]').val(value);

    suggestionBox.hide();

});

$(document).on('click', '.suggestion-box a', function (e) {

    e.preventDefault();

    let $this = $(this);

    let selectedCode = $this.text().trim();

    // Current row
    let $row = $this.closest('tr');

    // Inputs from same row
    let $input = $row.find('input[name="product_code[]"]');
    let $details = $row.find('textarea[name="product_details[]"]');

    // Product type from same row
    let productType = $row.find('.product-type').val();



    // Set selected product code
    $input.val(selectedCode);

    // Hide suggestion box
    $this.closest('.suggestion-box').hide();

    $.ajax({
        url: "{{ route('admin.get-store-product-by-product-code') }}",
        method: 'GET',

        data: {
            selectedCode: selectedCode,
            productType: productType
        },

        success: function (res) {

            if (res.success) {

                // Fill details in same row
                $details.val(res.data.product_details);
                $input.val(res.data.product_code);

            }

        }

    });

});



$("#countingForm").submit(function(e) 
{
    e.preventDefault();

    let isValid = true;

    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");



    if (!isValid) return;

    let form = $("#countingForm")[0];
    let data = new FormData(form);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.counting-store') }}",
        data: data,
        dataType: "JSON",
        processData: false,
        contentType: false,
        success: function(response) {
            if ($.isEmptyObject(response.error)) 
            {
                $.toaster({
                    priority: 'success',
                    title: response.success,
                    message: ''
                });
                location.reload();
            } else {
                $(".error").text("");
                $(".is-invalid").removeClass("is-invalid");
                $.each(response.error, function(index, value) {
                    $("#" + index + "Error").text(value);
                    $("#" + index).addClass("is-invalid");
                });
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error: " + textStatus + " - " + errorThrown);
    });
});
                    
</script>

@endsection
