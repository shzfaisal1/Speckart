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
                        <h3>Return From Store</h3>
                        
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
                        <input type="text" class="form-control input" placeholder="Bill Number,Supplier,Barcode,Product Code" id="search" name="search" style="width: 250px;margin-top: 10px;">
                    </div>
                </div> 
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3" style="margin-top: 10px;">
                        <select class="form-control select" style="height: 32px !important;" id="store_id" name="store_id">
                            <option value="">Select  Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
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
                            <tr>
                                <th class="wd-10p">Sr.No</th>
                                <th class="wd-15p">Reference No</th>
                                <th class="wd-10p">Total Qty</th>
                                <th class="wd-10p">Total Amount</th>
                                <th class="wd-10p">Return Date</th>
                                <th class="wd-10p">Return From</th>
                                <th class="wd-10p">Return To</th>
                                <th class="wd-10p">Status</th>
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
                <h5 class="modal-title" id="modalTitle">Return Stock Received or Reject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stockForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="refrence_no" id="refrence_no">


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
                                <div class="col-md-12">
                                    <label for="">Status <span class="text-danger">*</span></label>
                                    <select class="form-control select1" style="height: 32px !important;" id="stock_recevied_status" name="stock_recevied_status">
                                         <option value="">Select</option>
                                         <option value="2">Accept Return Stock</option>
                                         <option value="3">Reject Return Stock</option>
                                    </select>
                                    <span class="error badge text-danger" id="stock_recevied_statusError"></span>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Remark</label>
                                    <input type="text" class="form-control"  name="remark" id="remark">
                                    <span class="error badge text-danger" id="remarkError"></span>
                                </div>
                                <br>
                                <div class="col-md-12">
                                    <p style="color:red">Note : If u selected Accept Stock all product added into warehouse inventory.</p>
                                </div>
                            </div>
                        </div>
                        
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Submit </button>
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
            url: "{{ route('admin.store-return-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d.product_type = $('#product_type').val(),
                d.search1 = $('#search').val(),
                d.store_id = $('#store_id').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "ref_no",
                orderable: false,
            },

            {
                "data": "total_qty",
                orderable: false,
            },
            {
                "data": "total_amount",
                orderable: false,
            },
            {
                "data": "return_date",
                orderable: false,
            },
            {
                "data": "return_from",
                orderable: false,
            },
            {
                "data": "return_to",
                orderable: false,
            },

            {
                "data": "status",
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
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function(data, type, full) 
                {
                    let printUrl = `{{ route('admin.return-view-page', ':refrence_no') }}`.replace(':refrence_no', full['refrence_no']);

                    if (full['status_s'] == '1') {
                        return (`<div class="dropdown"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button><div class="dropdown-menu">`+
                                    `<a class="dropdown-item" href="${printUrl}" target="_blank">View</a>` +
                                    '<a class="dropdown-item" href="#" onclick="openEditModal(`' + encodeURIComponent(JSON.stringify(full)) + '`)">Accept/Reject</a>'+
                                `</div>`
                        );
                    }
                    else
                    {
                        return (`<div class="dropdown"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button><div class="dropdown-menu">`+
                                    `<a class="dropdown-item" href="${printUrl}" target="_blank">View</a>` +
                                `</div>`
                        );        
                    }
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
    
    
    function openEditModal(stock) 
    {
        var stock = JSON.parse(decodeURIComponent(stock));
        
        document.getElementById('modalTitle').innerText = 'Return Stock Accept OR Reject';
        document.getElementById('refrence_no').value = stock.refrence_no;

        $('#stockModal').modal('show');
    }
    
     $("#stockForm").submit(function(e)
    {
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let stock_recevied_status = document.getElementById("stock_recevied_status" + class_name).value.trim();
    
        
        if (stock_recevied_status === "")
        {
            document.getElementById("stock_recevied_statusError" + class_name).textContent = "Select Status.";
            document.getElementById("stock_recevied_status" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
    
        if (!isValid) {
            return;
        }
    
        let form = $("#stockForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.return-stock-status') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
            if ($.isEmptyObject(response.error)) {
                $.toaster({
                    priority: 'success',
                    title: response.success,
                    message: 'stock status updated successfully.'
                });
                location.reload();
            } else {
                document.querySelectorAll(".error").forEach(el => el.textContent = "");
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                $.each(response.error, function(index, value) {
                    
                });
            }
        }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
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
