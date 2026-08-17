@extends('layouts.master')
@section('styles')
<style>
/* Spinner when input has `loading` class */
input.loading {
    background-image: url('https://i.imgur.com/6RMhx.gif'); /* or any spinner gif you like */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}

.alert {
    text-align: left !important;
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
                <div class="domestic-orders-header">
                    <div class="col-lg-10">
                         <h3>Confirm Barcode</h3>
                    </div>
                    <!--<div class="col-lg-2">
                        <button id="exportButton" class="btn btn-success btn-sm"> <span><i class="fa fa-download" aria-hidden="true"></i></span>    
                             Export to Excel
                        </button>
                    </div>-->
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="row align-items-end g-3">
                        <div class="col-md-3">
                            <label for="reportrange" class="form-label">Date Range</label>
                            <div id="reportrange" class="form-control d-flex align-items-center" style="cursor:pointer;">
                                <i class="fa fa-calendar me-2"></i> &nbsp;
                                <span>Select Date</span>
                                <b class="caret ms-auto"></b>
                                <input type="hidden" id="date_from" name="date_from">
                                <input type="hidden" id="date_to" name="date_to">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Product</label>
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
                        <div class="col-md-3">
                            <label for="search" class="form-label">Store </label>
                           <select class="form-control select" style="height: 32px !important" id="store_id" name="store_id" >
                                <option value="">Select  Store</option>
                              <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                               @foreach($tbl_store as $tbl_store)
                                <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                              @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="payment_method" class="form-label">Search</label>
                            <input type="text" class="form-control input" placeholder="Bill Number Wise,Barcode No,Product Code" id="search" name="search" style="width: 250px;">
                        </div>
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
                            <div class="row justify-content-between align-items-center mb-3 mt-3 mr-3">
                                <div class="col-md-10">
                                    <span id="checked-order-count" class="btn btn-success">Selected Barcode: 0</span>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" style="height: 35px !important;" id="bulk_action" name="bulk_action">
                                        <option value="">Select Bulk Action</option>
                                        <option value="1">Print All Barcode</option>
                                        <option value="2">Update Barcode Number</option>
                                    </select>
                                </div>
                            </div>
                            <tr>
                                <th colspan="7" style="color: #FF0000;" colspan="2">Select checkbox and click on "Print All Barcode" button to print multiple barcode.</td>
                            </tr>
                            <tr>
                                <th style="width: 0px;"></th>
                                <th class="wd-15p"><div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll">
                                <label class="form-check-label" for="checkboxSelectAll"></label></div></th>
                                <th class="wd-15p">Barcode</th>
                                <th class="wd-15p">Purchase/Import/Inventory Details</th>
                                <th class="wd-20p">Product Details	</th>
                                <th class="wd-10p">Purchase Price</th>
                                <th class="wd-10p">Retail Price	</th>
                                <th class="wd-10p">Store Name	</th>

                                <th class="wd-10p">Action	</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
               </div>
            </div>
        </div>
    </div>
</section>

</div>


<div class="modal fade" data-backdrop="static" id="updatenumberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Bulk Update Barcode Numbers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="numberForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="sender_id" id="sender_id">


                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li>All selected barcodes will be updated as per new barcode</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">New Barcode<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control"  placeholder="Enter New Barcode" id="newbarcode_no" name="newbarcode_no">
                                    <span class="error badge text-danger" id="newbarcode_noError"></span>
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


<div class="modal fade" data-backdrop="static" id="ConfirmModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Update Barcode / Retail Price</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="warehouseForm" method="POST">
         <!--<form action="{{ route('admin.single-barcode-update') }}" method="post" enctype="multipart/form-data">-->
            <div class="modal-body">
                <div class="alert alert-dark">
                     <strong id="product_text_deatils"></strong>
                </div>
                <div class="alert alert-dark">
                     <strong id="barcode_text"></strong>
                </div>
                <div class="alert alert-dark">
                     <strong id="retail_text_price"></strong>
                </div>
                <hr/>
                @csrf
                 <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="uid" id="uid">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Update Barcode<span class="text-danger">*</span></label>
                        <input type="number" class="form-control"  placeholder="Enter Update Barcode" id="barcode_no" name="barcode_no">
                        <span class="error badge text-danger" id="update_barcode_noError"></span>
                    </div>
                    <div class="col-md-3">
                        <label for="">Update Retail Price <span class="text-danger">*</span></label>
                        <input class="form-control"  placeholder="Enter Update Retail Price " id="retail_price" name="retail_price">
                        <span class="error badge text-danger" id="update_retail_priceError"></span>
                    </div>
                </div>
                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="button" class="btn btn-primary" id="updated_barcode" data-carrier="" onclick="updatedBarcode(this)">Update Barcode</button>
            </div>
        </form>
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
    
$('.select2').select2({
   width: '100%' 
});
let sender_ids = [];
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
            "url": "{{ route('admin.barcode-confirm-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) {
                d.date_to = $('#date_to').val(),
                d.date_from = $('#date_from').val(),
                d.search_input = $('#search').val(),
                d.product_type = $('#product_type').val(),
                d.store_id = $('#store_id').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [{
                "data": 'responsive_id',
                orderable: false,
                searchable: false
            },
            {
                "data": "barcode_id",
                orderable: false,
                searchable: false
            },
            {
                "data": "barcode",
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
                "data": "purchase_price",
                orderable: false,
            },
            
            {
                "data": "retail_price",
                orderable: false,
            },
            {
                "data": "store_name",
                orderable: false,
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
                    return `
                         <span class="badge badge-dark pointer"
                          onclick="openubarcodeModal('` + full['barcode_id'] + `','` + full['barcode'] + `','` + full['retail_price']+ `','` + full['pdeatils'] + `')">
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </span>
                    `;
                    
                    
                }
            },

           
            {
                // For Checkboxes
                targets: 1,
                orderable: false,
                responsivePriority: 1,
                render: function (data,type, full) {
                    
                        return (
                            '<div class="form-check"> <input class="form-check-input dt-checkboxes" type="checkbox" value="" id="' +
                            data +
                            '" /><label class="form-check-label" for="' +
                            data +
                            '"></label></div>'
                        );
                   
                },
                checkboxes: {
                    selectAllRender:'<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>',
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
    $('.input').on('keyup', function() {
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
    console.log('Checked checkboxes count:', count);
    console.log('Checked checkbox IDs:', $('input.dt-checkboxes:checked:not(:disabled)').map(function() { return $(this).attr('id'); }).get());

    // Update the display
    $('#checked-order-count').text(`Selected Orders: ${count}`);
    $('#checked-order-count').removeClass('badge-info badge-dark');
    $('#checked-order-count').addClass(count > 0 ? 'badge-dark' : 'badge-info');
    console.log('Updated #checked-order-count text:', $('#checked-order-count').text());
}

// Initialize the count on page load
$(document).ready(function () {
    console.log('Initializing checkbox count');
    updateCheckedOrderCount();
});

// Handle individual checkbox changes
$(document).on('change', 'input.dt-checkboxes', function () {
    console.log('Individual checkbox changed - ID:', $(this).attr('id'), 'Checked:', $(this).is(':checked'));
    updateCheckedOrderCount();
});

// Handle "Select All" checkbox
$(document).on('change', '#checkboxSelectAll', function () {
    console.log('Select All checkbox changed - Checked:', $(this).is(':checked'));
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
    console.log('DataTable redrawn');
    updateCheckedOrderCount();
});
 //------------------------------


function openubarcodeModal(id, barcode, retail_price, pdeatils) {
    $('#retail_text_price').text('');
    $('#product_text_deatils').text('');
    $('#barcode_text').text('');
    
    var barcode_no_text = barcode == 'null' ? '' : ' Existing Barcode : ' + barcode;
    var retail_text = retail_price == 'null' ? '' : ' Existing Retail Price : ' + retail_price;
    var product_text = pdeatils == 'null' ? '' : '   Product Description : ' + pdeatils;
    document.getElementById('modalTitle').innerText = 'Update Barcode / Retail Price';
    document.getElementById('uid').value = id;
    $('#barcode_text').text(barcode_no_text);
    $('#retail_text_price').text(retail_text);
    $('#product_text_deatils').text(product_text);

    $('#ConfirmModal').modal('show');
}

$('#bulk_action').on('change', function(e) {
    const id = $(this).val();
    if(id == ''){
        return false;
    }
    e.preventDefault();
    
    let rows_selected = dataListView.column(1).checkboxes.selected();
    let sender_ids = [];
    $.each(rows_selected, function(index, rowId) {
        let checkbox = $('input[type="checkbox"][id="' + rowId + '"]');
        console.log(checkbox.prop('disabled'));
        if (checkbox.prop('disabled') == false) {
            sender_ids.push(rowId);
        }            
    });
    if (sender_ids.length > 0) 
    {
        
        if(id == '1')
        {

            $('#bulk_action option:first-child').attr("selected", "selected");
            $.ajax({
                    url: "{{ route('admin.bulk-generate-barcode')}}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        action: 'destroy',
                        ids: sender_ids
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data,status,xhr)
                    {
                        var blob = new Blob([data], { type: 'application/pdf' });
                        var url = URL.createObjectURL(blob);
                        window.open(url); // Opens the PDF in a new browser tab

                    },
                    error: function(reject) 
                    {
                        if (reject.status === 422) 
                        {
                            let errors = reject.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $.toaster({ priority : 'warning', title : value[0], message : '' });
                            });
                        } else {
                            $.toaster({ priority : 'warning', title : reject.responseJSON.message, message : '' });
                        }
                    }
                })
        }
        if(id == '2')
        {
            document.getElementById('sender_id').value = sender_ids;
            $('#updatenumberModal').modal('show');
        }
    }
    else 
    {
        $.toaster({ priority : 'warning', title : 'Attention!!' , message : "Please select at least one data" });
    }
});

function ajaxCall(url,ids){
    Swal.fire({
        title: "Are you sure?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: "Submit",
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger mx-1'
        },
        buttonsStyling: false,
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: ids
                    },
                     beforeSend: function () {
                      document.getElementById("global-loader").style.display = "";
                    },
                    success: function(data) {
                         document.getElementById("global-loader").style.display = "none";
                        if (data.status === true ) {
                            $.toaster({ priority : 'success', title : 'Success..!' , message : data.message});
                            dataListView.draw();
                        } else {
                            $.toaster({ priority : 'warning', title : 'Attention!!' , message : data.message});
                            if(data.code == 202){
                                window.setTimeout(function () {
                                    location.href = data.redirect;
                                }, 3000);
                            }
                        }
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            let errors = reject.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $.toaster({ priority : 'warning', title : 'Attention!!' , message : value[0]});
                            });
                        } else {
                            $.toaster({ priority : 'warning', title : 'Attention!!' , message : reject.responseJSON.message});
                        }
                    }
                })
        }
    })
}


 $("#updatenumberModal").submit(function(e)
    {
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let newbarcode_no = document.getElementById("newbarcode_no" + class_name).value.trim();
    
    
    
        if (newbarcode_no === "") {
            document.getElementById("newbarcode_noError" + class_name).textContent = "Barcode No required.";
            document.getElementById("newbarcode_no" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        
    
    
        if (!isValid) {
            return;
        }
    
        let form = $("#updatenumberModal")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.barcode-new-update') }}",
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
                window.location.href = "{{ route('admin.confirm-barcode') }}";
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


function updatedBarcode(getData)
{
    let uid = document.getElementById("uid").value.trim();
    let barcode_no = document.getElementById("barcode_no").value.trim();
    let retail_price = document.getElementById("retail_price").value.trim();
    if (!barcode_no && !retail_price ) {
        $.toaster({ priority: 'warning', title: 'Attention!!', message: 'Please enter new barcode or new retail price to update' });
        return;
    }

    $.ajax({
    url: "{{ route('admin.single-barcode-update') }}",
    type: "POST",
    data: {
        uid:uid,
        barcode_no:barcode_no,
        retail_price:retail_price,
        _token: "{{ csrf_token() }}",
    },
    success: function(data) {
        if(data.code == '200'){
            $.toaster({ priority : 'success', title : 'Success..!' , message : data.message });
            $('#ConfirmModal').modal('hide');
        }else if(data.code == '202'){
            $.toaster({ priority : 'warning', title : 'Opps..!' , message : data.message });
        }
    },
    error: function(reject) {
        if (reject.status === 422 || reject.status === 400) {
            let errors = reject.responseJSON.errors;
            if (errors.order_details && typeof errors.order_details === 'object') {
                Object.entries(errors.order_details).forEach(([field, messages]) => {
                    messages.forEach(msg => {
                        $.toaster({ priority : 'warning', title : 'Attention!!', message : `${msg}`,timeout :3000 });
                    });
                });
            }else{
                $.each(errors, function(key, value) {
                    if(value.isArray){
                        $.toaster({ priority : 'warning', title : 'Attention..!' , message : value[0] });
                    }else{
                        $.toaster({ priority : 'warning', title : 'Attention..!' , message : value });
                    }
                });
            }
        } else {
            $.toaster({ priority : 'warning', title : 'Attention..!' , message : reject.responseJSON.message });
        }
    }
})
       
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
</script>




@endsection
