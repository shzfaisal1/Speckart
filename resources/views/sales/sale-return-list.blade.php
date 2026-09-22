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
                        <h3>Sales Return History</h3>
                        <a href="{{route('admin.sale-return')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add Sale Return
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
                                <th class="wd-10p">Amount</th>
                                <th class="wd-10p">Return Date</th>
                                <th class="wd-10p">Payment Return</th>
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

@endsection


<div class="modal fade" data-backdrop="static" id="reqModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="requestForm" method="POST">
            <div class="modal-body">
                <div class="alert alert-dark">
                     <strong id="product_text_deatils"></strong>
                </div>
                <div class="alert alert-dark">
                     <strong id="producttype_text"></strong>
                </div>
                <div class="alert alert-dark">
                     <strong id="product_code_text"></strong>
                </div>
                <div class="alert alert-dark">
                     <strong id="oid_text"></strong>
                </div>
                <hr/>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="uid" id="uid">
                
                 <div class="row">
                    <div class="col-md-12">
                        <h5>Payment Method <span class="text-danger">*</span></h5>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pay_method" id="cust_wallet" value="Credit Issue">
                                <label class="form-check-label" for="cust_wallet">Customer Issue Credit</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pay_method" id="pay_cash" value="Cash">
                                <label class="form-check-label" for="pay_cash">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pay_method" id="pay_upi" value="UPI">
                                <label class="form-check-label" for="pay_upi">UPI</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Pay Details </label>
                            <input type="text" class="form-control" placeholder="Enter Details" name="pay_deatils" id="pay_deatils">
                        </div>
                    </div>
                    
                </div>
                
                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="button" class="btn btn-primary"  data-carrier="" onclick="updatedPayment(this)">Submit</button>
            </div>
        </form>
      </div>
    </div>
  </div>

@section('scripts')
<script>
  $(document).ready(function() {
    $('.select').select2({
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
            url: "{{ route('admin.sale-return-datatable') }}",
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
                "data": "amount",
                orderable: false,
            },
            {
                "data": "return_date",
                orderable: false,
            },
            {
                "data": "return_payment_status",
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
                // Actions
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function(data, type, full) 
                {
                    let baseUrl = "{{ url('sale/invoice') }}";
                    let orderUrl   = baseUrl + '/' + full['encryptedIdss'] + '/order';
                        
                    if (full.gatepass_status == 1 && full.return_payment_statusss == 1)
                    {
                        
                        return `<span class="text-muted">
                        <a href="${orderUrl}" target="_blank" class="tooltip">
                                <img class="action-icon" src="{{asset('assets/images/icon/form.webp')}}">
                                <span class="tooltip-text">View & Print Order Form</span>
                            </a>
                        </span>`;
                    }
                    
                    if (full.return_payment_statusss == 1)
                    {
                        return `
                             <span 
                              class="badge badge-dark pointer action-delete dropdown-item" href="#"  data-id="` + full['encryptedId'] + `">
                                Create GatePass
                            </span>
                        `;
                    }
                    else
                    {
                        return (`
                        
                            <a class="tooltip pointer" onclick="openreturnModal(
                               '${full.pid}',
                               '${full.product_code}',
                               '${full.description}',
                               '${full.product_type}',
                               '${full.order_no}'
                           )">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-payment-details.webp')}}">
                            <span class="tooltip-text">Add Payment Return</span>
                    
                            <a href="${orderUrl}" target="_blank" class="tooltip">
                                <img class="action-icon" src="{{asset('assets/images/icon/form.webp')}}">
                                <span class="tooltip-text">View & Print Order Form</span>
                            </a>
       
                            
                            
               
                            
                        `);
                        
                        
                    }
                    

                }
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
    
    
    $("table").delegate(".action-delete", "click", function (e) {

    e.preventDefault();
    e.stopPropagation();

    let id = $(this).data('id');

    Swal.fire({
        title: "Are you sure?",
        text: "You want to create gatepass!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, create it!",
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger ml-2'
        },
        buttonsStyling: false,
    }).then(function (result) {

        if (result.isConfirmed) {

            $.ajax({
                url: "/gatepass/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },

                success: function (data) {

                    if (data.status) {

                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        );

                        dataListView.ajax.reload(null, false);
                    }
                },

                error: function (xhr) {

                    Swal.fire(
                        'Error!',
                        xhr.responseJSON.message ?? 'Something went wrong',
                        'error'
                    );
                }
            });
        }
    });
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
    
    
    function openreturnModal(id,product_code,description,ptype,oid)
   {
       
        $('#product_text_deatils').text('');
        $('#producttype_text').text('');
        $('#product_code_text').text('');
        $('#oid_text').text('');
        
        var product_text_deatils = description == 'null' ? '' : ' Description : ' + description;
        var producttype_text = ptype == 'null' ? '' : 'Product Type : ' + ptype;
        var product_code_text = product_code == 'null' ? '' : '   Product Code : ' + product_code;
        var oid_text = oid == 'null' ? '' : ' Order No : ' + oid;

        document.getElementById('modalTitle').innerText = 'Add Return Payment';
        document.getElementById('uid').value = id;
        
        $('#product_text_deatils').text(product_text_deatils);
        $('#producttype_text').text(producttype_text);
        $('#product_code_text').text(product_code_text);
        $('#oid_text').text(oid_text);
    
        $('#reqModal').modal('show');
    } 
    
    
    function updatedPayment(btn)
    {
        let uid = $("#uid").val();
        let pay_method = $("input[name='pay_method']:checked").val();
        let pay_deatils = $("#pay_deatils").val();
    
        /* -------------------------
           RADIO VALIDATION
        -------------------------*/
        if(!pay_method)
        {
            $.toaster({
                priority:'warning',
                title:'Validation',
                message:'Please select payment method'
            });
            return;
        }
    
        /* -------------------------
           Disable Button
        -------------------------*/
        $(btn).prop('disabled', true).text('Processing...');
    
        $.ajax({
            url: "{{ route('admin.sale-returen-payment-stored') }}",
            type: "POST",
            data: {
                uid: uid,
                pay_method: pay_method,
                pay_deatils: pay_deatils,
                _token: "{{ csrf_token() }}"
            },
    
            success: function(response)
            {
                if(response.status === true)
                {
                    $.toaster({
                        priority:'success',
                        title:'Success!',
                        message:response.message
                    });
    
                    $('#reqModal').modal('hide');
    
                    if(typeof dataListView !== "undefined"){
                        dataListView.ajax.reload(null,false);
                    }
                }
                else
                {
                    $.toaster({
                        priority:'warning',
                        title:'Warning',
                        message:response.message
                    });
                }
            },
    
            error: function(xhr)
            {
                if(xhr.status === 422)
                {
                    let errors = xhr.responseJSON.errors;
    
                    $.each(errors, function(key, value){
                        $.toaster({
                            priority:'warning',
                            title:'Validation',
                            message:value[0]
                        });
                    });
                }
                else
                {
                    $.toaster({
                        priority:'error',
                        title:'Error',
                        message:'Something went wrong'
                    });
                }
            },
    
            complete: function(){
                $(btn).prop('disabled', false).text('Submit');
            }
        });
    }
        
</script>




@endsection
