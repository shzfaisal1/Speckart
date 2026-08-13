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
                        <h3>Pending Courier</h3>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
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
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Bill Number,Customer Name,MobileNo" id="search" name="search" style="width: 250px;margin-top: 10px;">
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
                                <th class="wd-5p">Sr.No</th>
                                <th class="wd-15p">Order Date</th>
                                <th class="wd-15p">Delivery Date</th>
                                <th class="wd-15p">Order No</th>
                                <th class="wd-15p">Customer Details</th>
                                <th class="wd-20p">Total Amount Rs</th>
                                <th class="wd-10p">Store Name</th>
                                <th class="wd-10p">Total Courier Products</th>
                                <th class="wd-10p">Product Ready</th>
                                <th class="wd-10p">Pending Product</th>
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


<div class="modal fade" data-backdrop="static" id="CourierModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                
                    <h5>* Select items which you want to courier</h5>  
                     <table class="table table-bordered" id="courierTable">
                          <thead>
                            <tr>
                              <th style="color:#000"></th>
                              <th style="color:#000">Product Type</th>
                              <th style="color:#000">Description</th>
                              <th style="color:#000">Qty</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                          </tbody>
                        </table>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <label for="" class="form-label">Courier Tracking Details: </label>
                                <textarea type="text" class="form-control"  name="tracking_details" id="tracking_details"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="" class="form-label">Courier Partner: </label>
                                <input type="text" class="form-control" name="courier_partner" id="courier_partner">
                            </div>
                            <div class="col-md-4">
                                <label for="" class="form-label">Tracking ID / No: </label>
                                <input type="text" class="form-control" name="tracking_id" id="tracking_id">
                            </div>
                        </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="updateCourierBtn" >Update Courier Details</button>
                </div>
            </form>
          </div>
        </div>
      </div>

@endsection

@section('scripts')



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
            url: "{{ route('admin.pending-courier-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.store_id = $('#store_id').val(),
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
                "data": "order_date",
                orderable: false,
            },
            {
                "data": "delivery_date",
                orderable: false,
            },
            {
                "data": "order_no",
                orderable: false,
            },
            {
                "data": "customer_details",
                orderable: false,
            },
            {
                "data": "order_amount",
                orderable: false,
            },
            {
                "data": "store_name",
                orderable: false,
            },

            {
                "data": "total_product",
                orderable: false,
            },
            {
                "data": "total_product_ready",
                orderable: false,
            },
            {
                "data": "total_courier_status",
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
                    
                        return (`
                        
                            <a class="tooltip pointer" onclick="opencourierModal('${full['oid']}')">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-courier-no.webp')}}">
                                <span class="tooltip-text">Courier Order</span>
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
    

    function opencourierModal(oid) 
    {
        document.getElementById('modalTitle').innerText = 'Update Courier Details of Order No  : '+oid;
        
         $.ajax({
            url: "{{ route('admin.getpendingcourierproduct') }}",  // Laravel route
            method: 'GET',
            data: { oid: oid },
            beforeSend: function () {
              $("#ajaxLoader").show(); 
            },
            success: function(response) {
                let tableBody = $('#courierTable tbody');
                tableBody.empty(); 
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(product) 
                    {
                        
    
                        let row = `
                            <tr>
							    <td>
                                <input type="checkbox" name="sale_pid" class="sales-product"
                                        value="${product.pids}">
                                </td>
                                <td>${product.product_type}</td>
                                <td>${product.product_deatils} ${product.ispair}</td>
                                <td>${product.qty}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="7" class="text-center">No product found.</td></tr>');
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch product details.',
                    timeout: 3000
                });
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        });

        $('#CourierModal').modal('show');
    }
    
    
    $(document).on('click', '#updateCourierBtn', function () {
    
        let productIds = [];
        $('.sales-product:checked').each(function () {
            productIds.push($(this).val());
        });
    
        let trackingDetails = $('#tracking_details').val().trim();
        let courierPartner  = $('#courier_partner').val().trim();
        let trackingId      = $('#tracking_id').val().trim();
    
        // ✅ Validation: at least checkbox OR tracking id
        if (productIds.length === 0 && trackingId === '') {
            $.toaster({
                priority: 'warning',
                title: 'Validation',
                message: 'Please select at least one product or enter Tracking ID',
                timeout: 3000
            });
            return false;
        }
    
        $.ajax({
            url: "{{ route('admin.update.courier') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                product_ids: productIds,
                tracking_details: trackingDetails,
                courier_partner: courierPartner,
                tracking_id: trackingId
            },
            beforeSend: function () {
                $('#updateCourierBtn')
                    .prop('disabled', true)
                    .text('Updating...');
            },
            success: function (response) {
    
                if (response.status) {
                    $.toaster({
                        priority: 'success',
                        title: 'Success',
                        message: response.message,
                        timeout: 3000
                    });
    
                    $('#CourierModal').modal('hide');
    
                    // reload datatable if exists
                    if ($.fn.DataTable.isDataTable('#courierTable')) {
                        $('#courierTable').DataTable().ajax.reload(null, false);
                    }
                } else {
                    $.toaster({
                        priority: 'danger',
                        title: 'Error',
                        message: response.message,
                        timeout: 3000
                    });
                }
            },
            error: function () {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Something went wrong. Please try again.',
                    timeout: 3000
                });
            },
            complete: function () {
                $('#updateCourierBtn')
                    .prop('disabled', false)
                    .text('Update Courier Details');
            }
        });
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




@endsection
