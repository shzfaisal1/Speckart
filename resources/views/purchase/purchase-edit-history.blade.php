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
 
  <div id="ajaxLoader" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading, please wait...</p>
    </div>
</div>
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Purchase Edit History</h3>
                        
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
                        <input type="text" class="form-control input" placeholder="Bill Number Wise,Supplier Name" id="search" name="search" style="width: 250px;margin-top: 10px;">
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
                                <th class="wd-15p">Supplier Name</th>
                                <th class="wd-20p">Bill Number</th>
                                <th class="wd-15p">Purchase Date</th>
                                <th class="wd-10p">Created Date</th>
                                <th class="wd-10p">Created By</th>
                                <th class="wd-10p">Last Edit Date</th>
                                <th class="wd-10p">Last Edit By</th>
                                <th class="wd-10p">Action</th>
                            </tr>
                        </thead>
                        <tbody id="history-body"></tbody>
                    </table>
                </div>
        
               </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" data-backdrop="static" id="historytModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Purchase Edit History Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="multisteps-form__content">
                    <div class="multisteps-form__content1">
                        <div id="table-container"></div>
                    </div>
                    
                   
                </div>
            </div>
        </div>
    </div>
</div>

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
            url: "{{ route('admin.purchase-edit-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
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
                "data": "store_name",
                orderable: false,
            },

            {
                "data": "supplier_name",
                orderable: false,
            },
            {
                "data": "bill_no",
                orderable: false,
            },
            {
                "data": "p_date",
                orderable: false,
            },
            {
                "data": "c_date",
                orderable: false,
            },

            {
                "data": "created_by",
                orderable: false,
            },
            {
                "data": "last_date",
                orderable: false,
            },
            {
                "data": "updated_by",
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
                render: function(data, type, full)  {
                    return `
                         <span class="badge badge-info pointer"
                           onclick="openEditModal('` + full['encryptedId'] + `')">
                            Edit History
                        </span>
                    `;
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
    
    

    function openEditModal(encryptedId) {
        var purchaseid = encryptedId;
    

        $.ajax({
            url: "{{ route('admin.getedithistroy') }}", // Replace with your route
            type: "GET",
            data: { purchaseid: purchaseid },
            dataType: "json",
            beforeSend: function () 
            {
                $("#ajaxLoader").show(); 
            },
            success: function(data) {
                buildFullTable(data);
            },
            error: function(xhr, status, error) {
                 $("#table-container").html('<p>Failed to load data.</p>');
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        });
    
        // Show modal after AJAX call
        $('#historytModal').modal('show');
    }
    
    function buildFullTable(data) {
        let table = `
            <table class="table-bordered">
                <thead>
                    <tr>
                        <th style="color:#000">Store Name</th>
                        <th style="color:#000">Supplier Name</th>
                        <th style="color:#000">Bill Number</th>
                        <th style="color:#000">Purchase Date</th>
                        <th style="color:#000">Created Date</th>
                        <th style="color:#000">Created By</th>
                        <th style="color:#000">Last Edit Date</th>
                        <th style="color:#000">Last Edit By</th>
                    </tr>
                </thead>
                <tbody>
        `;
    
        if (data.length === 0) {
            table += `<tr><td colspan="8">No history found.</td></tr>`;
        } else {
            data.forEach(item => {
                table += `<tr>
                    <td>${item.store_name || '-'}</td>
                    <td>${item.supplier_name || '-'}</td>
                    <td>${item.bill_number || '-'}</td>
                    <td>${item.purchase_date || '-'}</td>
                    <td>${item.created_date || '-'}</td>
                    <td>${item.created_by || '-'}</td>
                    <td>${item.last_edit_date || '-'}</td>
                    <td>${item.last_edit_by || '-'}</td>
                </tr>`;
            });
        }
    
        table += `</tbody></table>`;
    
        // Append the whole table to a container div
        $("#table-container").html(table); // Replace existing table if needed
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
