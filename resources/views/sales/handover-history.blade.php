@extends('layouts.master')
@section('styles')
<style>

input.loading {
    background-image: url('https://i.imgur.com/6RMhx.gif'); /* or any spinner gif you like */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip-text {
    visibility: hidden;
    background-color: #000;
    color: #fff;
    text-align: center;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;

    position: absolute;
    bottom: 120%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;

    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}

.action-icon
{
    margin:2px;
}

.icon-dark {
    filter: grayscale(100%);
    opacity: 0.5;
    pointer-events: none; /* optional: disable click */
}

.alert {
    font-size: 13px;
    text-align: left !important;
    font-weight: 400;
    margin: 10px;
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
                        <h3>Product Handover History</h3>
                        <a href="#" class=" btn" data-toggle="modal" data-target="#HandoverModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add Product Handover
                        </a>
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
                <div class="col-md-3" style="margin-top: 10px;">
                    <select class="form-control select" style="height: 32px !important;" id="product_type" name="product_type">
                        <option value="">Select Product </option>
                        <option value="Frame">Frame</option>
                        <option value="Glass">Glass</option>
                        <option value="Goggles">Goggles</option>
                        <option value="Lens">Contact Lens</option>
                        <option value="Solution">Solution</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Bill Number,Barcode,Product Code" id="search" name="search" style="width: 300px;margin-top: 10px;">
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
                    <table class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-10p">Sr.No</th>
                                <th class="wd-15p">Store Name</th>
                                <th class="wd-15p">Order Deatils</th>
                                <th class="wd-20p">Customer Details</th>
                                <th class="wd-10p">Product</th>
                                <th class="wd-10p">Product Code</th>
                                <th class="wd-10p">Description</th>
                                <th class="wd-10p">Handover By</th>
                                <th class="wd-10p">Handover Date</th>
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


<div class="modal fade" data-backdrop="static" id="HandoverModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Handover Product</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
                
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <input type="text" class="form-control input" placeholder="Sale Order Number" id="order_no" name="order_no" style="margin-top: 10px;">
                        </div>
                    </div> 
                    <div class="col-lg-3" style="margin-top: 10px;">
                        <div class="form-group">
                            <button class="btn btn-gradient js-btn-next" type="button">Search</button>
                        </div> 
                    </div>
                     <span class="error badge text-danger" id="order_noError"></span>
                </div>
               
                <div id="saleproductlist"></div>

                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
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
    
    $(".js-btn-next").on('click', function () {
        $("#order_noError").text("");

        let orderNo = $("#order_no").val().trim();

        let hasError = false;

        if (!orderNo) {
            $("#order_noError").text("Please select  bill number.");
            hasError = true;
        }

        if (hasError) return;
        $('#saleproductlist').empty();
        $.ajax({
            url: "{{ route('admin.sale-handover-product-list') }}", 
            type: "GET",
            data: 
            {
                order_no: orderNo
            },
            beforeSend: function () {
                $(".js-btn-next").prop("disabled", true).text("Searching...");
            },
            success: function (response) 
            {
                $('#saleproductlist').append(response);
            },
            error: function (xhr) {
                alert("An error occurred. Please try again.");
            },
            complete: function () {
                $(".js-btn-next").prop("disabled", false).text("Search");
            }
        });
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

$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
    cb(picker.startDate, picker.endDate);
});


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
            url: "{{ route('admin.sale-handover-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d.product_type = $('#product_type').val(),
                d.search1 = $('#search').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "store_details",
                orderable: false,
            },
            {
                "data": "order_details",
                orderable: false,
            },
            {
                "data": "customer_details",
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
                "data": "description",
                orderable: false,
            },
            
            {
                "data": "handover_by",
                orderable: false,
            },
            {
                "data": "handover_date",
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
                    // Base URL from Laravel
                    let baseUrl = "{{ url('sale/invoice') }}";
                    let baseUrll = "{{ url('sale/edit') }}";
                
                    // Dynamic URLs with both parameters
                    let invoiceUrl = baseUrl + '/' + full['encryptedId'] + '/invoice';
                    let receiptUrl = baseUrl + '/' + full['encryptedId'] + '/receipt';
                    let orderUrl   = baseUrl + '/' + full['encryptedId'] + '/order';
                    let editUrl = baseUrll + '/' + full['encryptedId'];
                    
  
                    return (`
                    
                        <a href="${receiptUrl}" target="_blank" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/receipt.webp')}}">
                            <span class="tooltip-text">View & Print Advance Receipt</span>
                        </a>
                        <a href="${orderUrl}" target="_blank" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/form.webp')}}">
                            <span class="tooltip-text">View & Print Order Form</span>
                        </a>
                        <a href="${invoiceUrl}" target="_blank" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/print.png')}}">
                            <span class="tooltip-text">Print Receipts</span>
                        </a>

                    `);
                       

                }

            }
            

         
            
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
    
    

   function openreturnModal(id)
   {

        document.getElementById('modalTitle').innerText = 'Create Gatepass';
        document.getElementById('uid').value = id;
    
        $('#GateModal').modal('show');
    } 

    
    function showResponseMessage(data) 
    {
        if (data.status === 'success') 
        {
            $.toaster({ priority : 'success', title : 'Success..!' , message : data.message });
            dataListView.draw();
        }
        else if (data.status === 'error') 
        {
            $.toaster({ priority : 'danger', title : 'Opps...!' , message : data.message });
            dataListView.draw();
        } 
        else 
        {
            $.toaster({ priority : 'danger', title : 'Opps..!' , message : 'Something went wrong. Please try again' });
        }
    }
    
    
    $(document).on("click", "#submitHanoverBtn", function () 
    {
        if ($('.row-checkbox:checked').length === 0) 
        {
            $.toaster({ priority: "warning", title: "Oops..!", message: "Please select at least one product." });
            e.preventDefault();
            return false;
        }
        
        if (!confirm("Are you sure you want to handover this product?")) 
        {
            return;
        }
    
        let selectedProducts = [];
        $(".row-checkbox:checked").each(function () 
        {
            selectedProducts.push($(this).val());
        });
    
        var handover_by = $("#handover_by").val();
        var handover_date = $("#handover_date").val();
        
        
        if (handover_by == '' && handover_date == '') 
        {
            $.toaster({ priority: "warning", title: "Oops..!", message: "Please handover person or date." });
            e.preventDefault();
            return false;
        }

        
        $("#submitreturnBtn").prop("disabled", true);
    
        $.ajax({
            url: "{{ route('admin.sale-handover-stored') }}",
            type: "POST",
            data: {
               _token: "{{ csrf_token() }}",
                product_id: selectedProducts,
                handover_by: handover_by,
                handover_date: handover_date,
            },
            success: function (response) 
            {
                $.toaster({ priority: "success", title: "Success..!", message: "Product handover successfully." });
                setTimeout(function () 
                {
                    window.location.href = "{{ route('admin.handover-history') }}";
                }, 1000);
            },
            error: function (xhr)
            {
                $.toaster({ priority: "warning", title: "Oops..!", message: "Something went wrong!" });
                $("#submitHanoverBtn").prop("disabled", false);
            }
        });
    });
        
</script>




@endsection
