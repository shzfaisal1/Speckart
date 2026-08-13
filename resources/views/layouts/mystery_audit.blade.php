@extends('layouts.master')
@php
     $usr = Auth::guard()->user();
 @endphp
@section('content')
<style>
    .domestic-orders-date {
        padding-top: 0px;
    }
    
    .section-title{
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
    /*border: 1px solid #d9dde7;*/
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
            <a class="nav-link active"  href="{{route('admin.audit-dashboard')}}">
                <i class="fa fa-search"></i>
                Mystery Audit
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.nps-dashboard')}}">
                <i class="fa fa-smile-o"></i>
                NPS Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.sale-dashboard')}}">
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
            <a class="nav-link"  href="{{route('admin.counting-dashboard')}}">
                <i class="fa fa-google-wallet"></i>
                Product Counting Dashboard
            </a>
        </li>

    </ul>

</div>


    <section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Mystry Audit History</h3>
                         @if ($usr->can('Mystry-Audit-History'))
                        <a href="{{route('admin.mystry-audit-entry')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Mystry Audit Entry
                        </a>
                        @endif
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
               
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3">
                     <div class="form-group">
                        <select class="form-control select" style="height: 32px !important;" id="store_id" name="store_id">
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
                            <tr>
                                <th class="wd-10p">Sr.No</th>
                                <th class="wd-15p">Auditor</th>
                                <th class="wd-15p">Store Name</th>
                                <th class="wd-15p">Audit DateTime</th>
                                <th class="wd-20p">Final Score</th>
                                <th class="wd-10p">Audit Status</th>
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
    const canView   = @json($usr->can('Mystry-Audit-View'));
    const canEdit   = @json($usr->can('Mystry-Audit-Edit'));
    const canDelete = @json($usr->can('Mystry-Audit-Delete'));
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
            url: "{{ route('admin.mystry-audit-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
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
                "data": "auditor",
                orderable: false,
            },
            {
                "data": "store_name",
                orderable: false,
            },
            {
                "data": "audit_date",
                orderable: false,
            },
            {
                "data": "final_score",
                orderable: false,
            },

            {
                "data": "audit_result",
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
                render: function (data, type, full) {
            
                    let viewUrl = `{{ route('admin.mystryaudit.view', ':audit_id') }}`
                                    .replace(':audit_id', full.encryptedId);
            
                    let editUrl = `{{ route('admin.mystryaudit.edit', ':audit_id') }}`
                                    .replace(':audit_id', full.encryptedId);
            
                    let actions = `
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                ACTION
                            </button>
                            <div class="dropdown-menu">
                    `;
            
                    if (canView) {
                        actions += `<a class="dropdown-item" href="${viewUrl}" target="_blank">View</a>`;
                    }
            
                    if (canEdit) {
                        actions += `<a class="dropdown-item" href="${editUrl}">Edit</a>`;
                    }
            
                    if (canDelete) {
                        actions += `<a class="dropdown-item action-delete" href="#" data-id="${full.mystry_audit_id}">Delete</a>`;
                    }
            
                    actions += `</div></div>`;
            
                    return actions;
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
                    url: "{{ url('/mystryaudit') }}" + '/' + id + '/destroy',
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
@endsection
