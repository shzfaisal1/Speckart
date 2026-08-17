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
                        <h3>Order Item Tracking</h3>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                 <div class="col-md-3">
                    <label for="">Select Store </label>
                    <select class="form-control select" style="height: 32px !important" id="store_id" name="store_id" >
                        <option value="">Select  Store</option>
                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                       @foreach($tbl_store as $tbl_store)
                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                      @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="" class="form-label">Order No:</label>
                    <input type="text" class="form-control"  placeholder="Enter Order No" id="order_no" name="order_no">
                </div>
                 <div class="col-md-3">
                    <label for="" class="form-label">Mobile No: </label>
                    <input type="text" class="form-control" placeholder="Enter Contact no" name="contact_no" id="contact_no" maxlength="10"  pattern="^[6-9][0-9]{9}$">
                </div>
                <div class="col-md-3">
                    <label for="" class="form-label">Full Name: </label>
                    <input type="text" class="form-control" placeholder="Enter customer name" id="cust_name" name="cust_name" >
                </div>
            </div>   
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="" class="form-label">Membership ID: </label>
                    <input type="text" class="form-control" placeholder="Enter Membership ID" id="membership_id" name="membership_id" >
                </div>
                <div class="col-md-3">
                    <label for="">From  Date </label>
                    <div id="reportrangesale1" class="pull-left"
                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                    <input type="hidden" class="form-control" id="from_date" name="from_date">
                </div>
                <div class="col-md-3">
                    <label for="">To  Date </label>
                    <div id="reportrangesale2" class="pull-left"
                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                    <input type="hidden" class="form-control" id="to_date" name="to_date">
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
                            <div class="row justify-content-between align-items-center mb-3 mt-3 mr-3">
                                <div class="col-md-8">
                                    <span id="checked-order-count" class="btn btn-success">Selected Item: 0</span>
                                </div>
                                <div class="col-md-2">
                                   <span id="bulk_action1" class="btn btn-success">Bulk Update  Index</span>
                                </div>
                                <div class="col-md-2">
                                   <span id="bulk_action" class="btn btn-success">Bulk Change Status</span>
                                </div>
                            </div>
                
                            <tr>
                                <th style="width: 0px;"></th>
                                <th class="wd-15p"><div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll">
                                <label class="form-check-label" for="checkboxSelectAll"></label></div></th>
                                <th class="wd-15p">Store Name</th>
                                <th class="wd-15p">Order Details</th>
                                <th class="wd-20p">Customer Name	</th>
                                <th class="wd-10p">Product Details</th>
                                <th class="wd-10p">Courier	</th>
                                <th class="wd-10p">Tracking Status</th>
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


    
<div class="modal fade" data-backdrop="static" id="TrackingModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
       <div class="modal-header">
            <h5 class="modal-title" id="modalTitleTracking"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            
            
            <div id="Trackingdiv">
            </div>
            
           
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
        </div>
     </div>
   </div>
</div>  


<div class="modal fade" data-backdrop="static" id="ChangeStatusModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="productstatusForm" method="POST">
         <!--<form action="{{ route('admin.single-barcode-update') }}" method="post" enctype="multipart/form-data">-->
            <div class="modal-body">
                
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="uid" id="uid">
                <input type="hidden" name="sender_id" id="sender_id">
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Current Tracking Status</label><br>
                        <strong id="tracking_text"></strong>
                    </div>
                    <div class="col-md-6">
                        <label for="">Change Tracking Status <span class="text-danger">*</span></label><br>
                         <select class="form-control select1" style="height: 32px !important;width:280px" id="tracking_status" name="tracking_status">
                            <option value="">Select Product </option>
                            <option value="ORDER PLACED AND  READY TO SHIP">ORDER PLACED AND  READY TO SHIP</option>
                            <option value="ORDER SEND TO WAREHOUSE">ORDER SEND TO WAREHOUSE</option>
                            <option value="ORDER RECEIVED FROM VENDOR">ORDER RECEIVED FROM VENDOR</option>
                            <option value="WARRANTY">WARRANTY</option>
                            <option value="QUALITY OK SENT FOR FITTING">QUALITY OK SENT FOR FITTING</option>
                            <option value="QUALITY REJECTED SENT BACK TO VENDOR">QUALITY REJECTED SENT BACK TO VENDOR</option>
                            <option value="RECEIVED FROM FITTING AND CLEAN PACKED">RECEIVED FROM FITTING AND CLEAN PACKED</option>
                            <option value="SEND TO STORE">SEND TO STORE</option>
                            <option value="RECEIVED BY STORE">RECEIVED BY STORE</option>
                            <option value="PRODUCT READY">PRODUCT READY</option>
                            <option value="DELIVERED">DELIVERED</option>
 
                        </select>
                        <span class="error badge text-danger" id="tracking_statusError"></span>
                    </div>
                </div>
				 <div class="row">
				    <div class="col-md-12">
					      <label for="">Tracking Comment </label>
					    <textarea class="form-control"   id="tracking_comment" name="tracking_comment"></textarea>
					</div>
				 </div>
				
                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="submit" class="btn btn-primary"  >Change Tracking Status</button>
            </div>
        </form>
      </div>
    </div>
 </div>
 
 
 <div class="modal fade" data-backdrop="static" id="IndexModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Updated Index</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="indexForm" method="POST">
            <div class="modal-body">
                
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="sender_ids" id="sender_ids">
                <div class="row">
                   <div class="col-md-12">
                       <div class="form-group">
                        <label for="" class="form-label">Select Index : <span class="text-danger">*</span></label>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="product_index" id="inlineRadio1" value="1.56">
                          <label class="form-check-label" for="inlineRadio1">1.56</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="product_index" id="inlineRadio2" value="1.59">
                          <label class="form-check-label" for="inlineRadio2">1.59</label>
                        </div>
                         <div class="form-check">
                          <input class="form-check-input" type="radio" name="product_index" id="inlineRadio3" value="1.60">
                          <label class="form-check-label" for="inlineRadio3">1.60</label>
                        </div>
                         <div class="form-check">
                          <input class="form-check-input" type="radio" name="product_index" id="inlineRadio4" value="1.67">
                          <label class="form-check-label" for="inlineRadio4">1.67</label>
                        </div>
                         <div class="form-check">
                          <input class="form-check-input" type="radio" name="product_index" id="inlineRadio5" value="1.74">
                          <label class="form-check-label" for="inlineRadio5">1.74</label>
                        </div>
                        <span class="error badge text-danger" id="product_indexError"></span>
                    </div>
                   </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="submit" class="btn btn-primary"  >Update Index</button>
            </div>
        </form>
      </div>
    </div>
 </div>
@endsection

@section('scripts')
<script>

$(function () {

    var today = moment();

    // FROM DATE
    $('#reportrangesale1').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        startDate: today,
        minDate: today,
        autoUpdateInput: false,
        locale: {
            format: 'MMMM D, YYYY'
        }
    }, function (date) {

        $('#reportrangesale1 span').html(date.format('MMMM D, YYYY'));
        $('#from_date').val(date.format('YYYY-MM-DD'));

        var toPicker = $('#reportrangesale2').data('daterangepicker');
        if (toPicker) {
            toPicker.minDate = date;
        }
    });



    // TO DATE
    $('#reportrangesale2').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        startDate: today,
        minDate: today,
        autoUpdateInput: false,
        locale: {
            format: 'MMMM D, YYYY'
        }
    }, function (date) {

        $('#reportrangesale2 span').html(date.format('MMMM D, YYYY'));
        $('#to_date').val(date.format('YYYY-MM-DD'));
    });



});

$(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
    
    $('.select1').select2({
      allowClear: true
    });
});


let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function() {
        $('#processingLoader').show();
    })
    .on('draw.dt', function() {
        $('#processingLoader').hide();
    }).DataTable({

        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "ajax": {
            "url": "{{ route('admin.item-tracking-list') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) {
                d.date_to = $('#date_to').val(),
                d.date_from = $('#date_from').val(),
                d.order_no = $('#order_no').val(),
                d.contact_no = $('#contact_no').val(),
                d.store_id = $('#store_id').val(),
                d.cust_name = $('#cust_name').val(),
                d.membership_id = $('#membership_id').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [{
                "data": 'responsive_id',
                orderable: false,
                searchable: false
            },
            {
                "data": "pid",
                orderable: false,
                searchable: false
            },
            {
                "data": "store_name",
                orderable: false,
            },
            {
                "data": "order_details",
                orderable: false,
            },
            {
                "data": "customer_name",
                orderable: false,
            },
            {
                "data": "product_details",
                orderable: false,
            },

            {
                "data": "courier",
                orderable: false,
            },


            {
                "data": "tracking_status",
                orderable: false,
                searchable: false
            },
            {
                "data": "action",
                orderable: false,
                searchable: false
            }
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
                    let baseUrl  = "{{ url('sale/invoice') }}";
                    let orderUrl = baseUrl + '/' + full['encryptedId'] + '/order';
                
                    let trackingStatus = full['tracking_status'] ?? '';
                
                    return (`
                        <a class="tooltip pointer"
                           onclick="openstatuschangeModal('${full['pid']}', '${trackingStatus}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-discount-coupon.png')}}">
                            <span class="tooltip-text">Change Status</span>
                        </a>
                
                        <a href="${orderUrl}" target="_blank" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/form.webp')}}">
                            <span class="tooltip-text">Order Form</span>
                        </a>
                
                        <a class="tooltip pointer"
                           onclick="opentrackinghistoryModal('${full['pid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/receipt.webp')}}">
                            <span class="tooltip-text">History</span>
                        </a>
                    `);
                }

            },
           
            {
                targets: 1,
                orderable: false,
                responsivePriority: 1,
                render: function (data, type, full) {
            
                    let productIndex = full.product_index ?? '';
            
                    return (
                        '<div class="form-check">' +
                            '<input class="form-check-input dt-checkboxes" ' +
                                'type="checkbox" ' +
                                'id="' + data + '" ' +
                                'data-product-index="' + productIndex + '"' +
                            '/>' +
                            '<label class="form-check-label" for="' + data + '"></label>' +
                        '</div>'
                    );
                },
                checkboxes: {
                    selectAllRender:
                        '<div class="form-check">' +
                            '<input class="form-check-input" type="checkbox" id="checkboxSelectAll">' +
                            '<label class="form-check-label" for="checkboxSelectAll"></label>' +
                        '</div>',
                    selectRow: true
                }
            },

        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        language: {
            paginate: {
                // remove previous & next text from pagination
                previous: 'Prev',
                next: 'Next'
            },
            sLengthMenu: "_MENU_",
            sZeroRecords: "{{ __('No results available') }}",
            sSearch: "{{ __('search') }}",
            sProcessing: "{{ __('processing') }}",
            sInfo: "{{ __('Showing :start to :end of :total entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}",
            sInfoFiltered: "" // Removes the "(filtered from xxx total entries)" text
        },
        responsive: {
            details: {
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    let data = $.map(columns, function(col) {
                        return col.title !==
                            '' // ? Do not show row in modal popup if title is blank (for check box)
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
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6 d-flex "l><"col-sm-12 col-md-6 text-end mt-1"Bf>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        aLengthMenu: [
            [10, 20, 50, 100],
            [10, 20, 50, 100],
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
    $('.input').on('keyup', function() 
    {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            const column = dataListView.column($(this).attr('name'));
            column.search($(this).val()).draw();
        }.bind(this), 500);
    });
    
    $('.select').on('change', function() {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });
    
    
    //------------------------------
     // Function to update the checked order count
    function updateCheckedOrderCount() {
        // Count checked checkboxes (excluding disabled ones)
        let count = $('input.dt-checkboxes:checked:not(:disabled)').length;
    
        // Update the display
        $('#checked-order-count').text(`Selected Items: ${count}`);
        $('#checked-order-count').removeClass('badge-info badge-dark');
        $('#checked-order-count').addClass(count > 0 ? 'badge-dark' : 'badge-info');
    }
    
    // Initialize the count on page load
    $(document).ready(function () {
        updateCheckedOrderCount();
    });
    
    // Handle individual checkbox changes
    $(document).on('change', 'input.dt-checkboxes', function () {
        updateCheckedOrderCount();
    });
    
    // Handle "Select All" checkbox
    $(document).on('change', '#checkboxSelectAll', function () {
        // Trigger change on all non-disabled checkboxes to sync state
        if ($(this).is(':checked')) {
            $('input.dt-checkboxes:not(:disabled)').prop('checked', true).trigger('change');
        } else {
            $('input.dt-checkboxes:not(:disabled)').prop('checked', false).trigger('change');
        }
        updateCheckedOrderCount();
    });
    
    // Update count after table redraw
    dataListView.on('draw.dt', function () {
        updateCheckedOrderCount();
    });
    
    
    function opentrackinghistoryModal(pid) 
    {
        $('#modalTitleTracking').text('Order Item Tracking History');
        $('#Trackingdiv').empty();
    
        $.ajax({
            type: "GET",
            url: "{{ route('admin.gettrackinghistory') }}",
            data: { pid: pid },
            dataType: "json",
            beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function(response) 
            {
                let sale = response.sale_product;
                let tracking = response.tracking_history;
    
                /* -------- Sale Product Info -------- */
                let productHtml = `
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <h5>Order Number: <span>${sale.order_number ?? ''}</span></h5>
                        </div>
                        <div class="col-md-4">
                            <h5>Product Type: <span>${sale.product_type ?? ''}</span></h5>
                        </div>
                        <div class="col-md-4">
                            <h5>Product Name: <span>${sale.product_name ?? ''}</span></h5>
                        </div>
                    </div>
    
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <h5>Order Date: <span>${sale.order_date ?? ''}</span></h5>
                        </div>
                        <div class="col-md-4">
                            <h5>Delivery Date: <span>${sale.delivery_date ?? ''}</span></h5>
                        </div>
                        <div class="col-md-4">
                            <h5>Customer Name: <span>${sale.customer_name ?? ''}</span></h5>
                        </div>
                    </div>
    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h5>Mobile No: <span>${sale.mobile ?? ''}</span></h5>
                        </div>
                    </div>
    
                    <hr/>
                `;
    
                $('#Trackingdiv').append(productHtml);
    
                /* -------- Tracking History Table -------- */
                let tableHtml = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr. No</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
    
                if (tracking.length > 0) {
                    tracking.forEach(function(item, index) {
                        tableHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.status ?? ''}</td>
                                <td>${item.created_at ?? ''}</td>
                            </tr>
                        `;
                    });
                } else {
                    tableHtml += `
                        <tr>
                            <td colspan="3" class="text-center">No tracking history found</td>
                        </tr>
                    `;
                }
    
                tableHtml += `
                        </tbody>
                    </table>
                `;
    
                $('#Trackingdiv').append(tableHtml);
                $('#TrackingModal').modal('show');
            },
            error: function() {
                alert('Something went wrong. Please try again.');
            },
            complete: function() {
                $("#ajaxLoader").fadeOut(); 
            }
        });
    }
    
    
    function openstatuschangeModal(id,tracking_status) 
    {

        var tracking_text = tracking_status == 'null' ? '' : tracking_status;

        document.getElementById('modalTitle').innerText = 'Change Order Status ';
        document.getElementById('uid').value = id;
        $('#tracking_text').text(tracking_text);
    
        $('#ChangeStatusModal').modal('show');
    }
    
    
    function showResponseMessage(data) {

        if (data.status === 'success') {
            $.toaster({ priority : 'success', title : 'Success..!' , message : data.message });
            dataListView.draw();
        } else if (data.status === 'error') {
            $.toaster({ priority : 'error', title : 'Opps...!' , message : data.message });
            dataListView.draw();
        } else {
            $.toaster({ priority : 'warning', title : 'Opps..!' , message : 'Something went wrong. Please try again' });
        }
    }
    
    
    $("#productstatusForm").submit(function(e)
    {
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let tracking_status = document.getElementById("tracking_status" + class_name).value.trim();
    
    
    
        if (tracking_status === "") {
            document.getElementById("tracking_statusError" + class_name).textContent = "Tracking status required.";
            document.getElementById("tracking_status" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        
    
    
        if (!isValid) {
            return;
        }
    
        let form = $("#productstatusForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.tracking-status-update') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
            if ($.isEmptyObject(response.error)) {
                $.toaster({
                    priority: 'success',
                    title: response.success,
                    message: ''
                });
                window.location.href = "{{ route('admin.order-item-tracking') }}";
            }
            else 
            {
                document.querySelectorAll(".error").forEach(el => el.textContent = "");
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                
            }
        }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    });
    
    
    $('#bulk_action').on('click', function(e)
    {
        let rows_selected = dataListView.column(1).checkboxes.selected();
        let sender_ids = [];
        $.each(rows_selected, function(index, rowId) {
            let checkbox = $('input[type="checkbox"][id="' + rowId + '"]');
            if (checkbox.prop('disabled') == false) {
                sender_ids.push(rowId);
            }            
        });
        if (sender_ids.length > 0) 
        {
            document.getElementById('sender_id').value = sender_ids;
            $('#ChangeStatusModal').modal('show');
        }
        else 
        {
            $.toaster({ priority : 'warning', title : 'Attention!!' , message : "Please select at least one data" });
        }
    });
    
    
    $('#bulk_action1').on('click', function (e) {
    e.preventDefault();

    let rows_selected = dataListView.column(1).checkboxes.selected();
    let sender_ids = [];
    let alreadyUpdatedIds = [];

    $.each(rows_selected, function (index, rowId) {
        let checkbox = $('input[type="checkbox"][id="' + rowId + '"]');

        if (!checkbox.length) return;

        let productIndex = checkbox.data('product-index');

        if (productIndex) {
            alreadyUpdatedIds.push(rowId);
        } else {
            sender_ids.push(rowId);
        }
    });

    // ❌ If any already updated → show error
    if (alreadyUpdatedIds.length > 0) {
        $.toaster({
            priority: 'warning',
            title: 'Attention!!',
            message: 'One or more selected products are already updated'
        });
        return;
    }

    // ✅ Normal flow
    if (sender_ids.length > 0) {
        $('#sender_ids').val(sender_ids.join(','));
        $('#IndexModal').modal('show');
    } else {
        $.toaster({
            priority: 'warning',
            title: 'Attention!!',
            message: 'Please select at least one product'
        });
    }
});


    
    
    
    $("#indexForm").submit(function(e)
    {
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let product_index = document.querySelector(
            'input[name="product_index"]:checked'
        );
    
    
    
        if (!product_index) {
            $("#product_indexError").text("Index required.");
            isValid = false;
        }

    
        if (!isValid) {
            return;
        }
    
        let form = $("#indexForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.update-index') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
            if ($.isEmptyObject(response.error)) {
                $.toaster({
                    priority: 'success',
                    title: response.success,
                    message: ''
                });
                window.location.href = "{{ route('admin.order-item-tracking') }}";
            }
            else 
            {
                document.querySelectorAll(".error").forEach(el => el.textContent = "");
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                
            }
        }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    });
    



</script>




@endsection
