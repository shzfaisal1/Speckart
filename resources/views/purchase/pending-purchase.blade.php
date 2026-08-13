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
                        <h3>Pending Purchases</h3>
                        
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
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder=" Order Number,Description,Product Code" id="search" name="search" style="width: 250px;margin-top: 10px;">
                    </div>
                </div> 
                <div class="col-lg-3">
                    <div class="form-group">
                        <select class="form-control select" style="height: 32px !important;margin-top:10px" id="product_type" name="product_type">
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
                                <div class="col-md-11">
                                    <span id="checked-order-count" class="btn btn-success">Selected Order: 0</span>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-primary loaderbtn" id="bulk_action">Add Purchase</button>
                                    
                                </div>
                            </div>
                            <tr>
                                <th colspan="9" style="color: #FF0000;" colspan="2">Select checkbox and click on "Add Purchase" button to Add Purchase <strong>OR</strong> click on "Add Challan" to Add Challan</td>
                            </tr>
                            <tr>
                                <th style="width: 0px;"></th>
                                <th class="wd-15p"><div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll">
                                <label class="form-check-label" for="checkboxSelectAll"></label></div></th>
                                <th class="wd-15p">Order Details</th>
                                <th class="wd-15p">Store Name</th>
                                <th class="wd-20p">Customer  Name</th>
                                <th class="wd-10p">Product Type </th>
                                <th class="wd-10p">Product Code</th>
                                <th class="wd-10p">Description</th>
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
            url: "{{ route('admin.pending-purchase-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d.search1 = $('#search').val(),
                d.store_id = $('#store_id').val(),
                d.ptype = $('#ptype').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
            "data": 'responsive_id',
                orderable: false,
                searchable: false
            },
            {
                "data": "sid",
                orderable: false,
                searchable: false
            },

            {
                "data": "order_details",
                orderable: false,
            },
            {
                "data": "store_name",
                orderable: false,
            },
            {
                "data": "cust_name",
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
                targets: 1,
                orderable: false,
                responsivePriority: 1,
                render: function (data, type, full) {
            
                    // challan_type = 1 → disable checkbox, show icon with tooltip
                    if (full.challan_type == 1) {
                        return (
                            '<span class="text-info" style="cursor:pointer;" title="B2B Purchase Challan">' +
                            '<i class="fa fa-info-circle fa-2x"></i>' +   // icon (can change)
                            '</span>'
                        );
                    }
            
                    // Default checkbox
                    return (
                        '<div class="form-check">' +
                        '<input class="form-check-input dt-checkboxes" type="checkbox" id="' + data + '" />' +
                        '<label class="form-check-label" for="' + data + '"></label>' +
                        '</div>'
                    );
                },
                checkboxes: {
                    selectAllRender:
                        '<div class="form-check">' +
                        '<input class="form-check-input" type="checkbox" id="checkboxSelectAll" />' +
                        '<label class="form-check-label" for="checkboxSelectAll"></label>' +
                        '</div>',
                    selectRow: true
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
    
    
    //------------------------------
     // Function to update the checked order count
    function updateCheckedOrderCount() {
        // Count checked checkboxes (excluding disabled ones)
        let count = $('input.dt-checkboxes:checked:not(:disabled)').length;
    
        // Update the display
        $('#checked-order-count').text(`Selected Orders: ${count}`);
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
     //------------------------------
    
    $('#bulk_action').on('click', function(e) {

        e.preventDefault();
    
        const id = $(this).val();
    
        let rows_selected = dataListView.column(1).checkboxes.selected();
        let sender_ids = [];
    
        $.each(rows_selected, function(index, rowId) {
    
            let checkbox = $('input[type="checkbox"][id="' + rowId + '"]');
    
            if (checkbox.prop('disabled') == false) {
                sender_ids.push(rowId);
            }
    
        });
    
        if (sender_ids.length > 0) {
    
            ajaxCall("{{ route('admin.check-same-store-order') }}", sender_ids);
    
        } else {
    
            $.toaster({
                priority : 'warning',
                title : 'Attention!!',
                message : "Please select at least one data"
            });
    
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
                
                    if (data.status === true) {
                
                        let ids = data.sales_product_ids;
                
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('admin.update-purchase-of-order') }}";
                
                        let token = document.createElement('input');
                        token.type = 'hidden';
                        token.name = '_token';
                        token.value = "{{ csrf_token() }}";
                        form.appendChild(token);
                
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'sales_product_ids';
                        input.value = JSON.stringify(ids);
                        form.appendChild(input);
                
                        document.body.appendChild(form);
                        form.submit();
                    } 
                    else {
                        $.toaster({
                            priority: 'warning',
                            title: 'Attention!!',
                            message: data.message
                        });
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
