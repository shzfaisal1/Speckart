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
                    <div class="col-lg-12">
                         <h3>Inventory Adjustment History</h3>
                    </div>
                </div>
            </div>
            <br>
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
                            <input type="text" class="form-control input" placeholder="Barcode No,Product Code" id="search" name="search" style="width: 250px;">
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
                            <tr>
                                <th class="wd-15p">#</th>
                                 <th class="wd-15p">Date</th>
                                <th class="wd-15p">Barcode</th>
                                <th class="wd-15p">Type</th>
                                <th class="wd-10p">Store Name</th>
                                <th class="wd-20p">Product Type	</th>
                                <th class="wd-20p">Product Code	</th>
                                <th class="wd-20p">Description	</th>
                                <th class="wd-10p">Purchase Price</th>
                                <th class="wd-10p">Retail Price	</th>
                                <th class="wd-10p">Comment</th>
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
    } else {
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
            "url": "{{ route('admin.adjustment-datatable') }}",
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
                "data": 'sr_no',
                orderable: false,
                searchable: false
            },
            {
                "data": "adj_date",
                orderable: false,
                searchable: false
            },
            {
                "data": "barcode",
                orderable: false,
            },
            {
                "data": "type",
                orderable: false,
            },
            {
                "data": "store_name",
                orderable: false,
            },
            {
                "data": "purchase_type",
                orderable: false,
            },
            {
                "data": "purchase_code",
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
                "data": "comment",
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


           
           
        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        language: {
            paginate: {
                previous: 'Prev',
                next: 'Next'
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
