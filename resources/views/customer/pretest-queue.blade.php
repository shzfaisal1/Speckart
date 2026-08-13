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
                        <h3>Pre-Test Queue</h3>
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
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <div class="domestic-orders-date">
                                <div id="reportrange" class="pull-left"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                    <span></span> <b class="caret"></b>
                                </div>
                                <input type="hidden" class="form-control" id="date_from" name="date_from">
                            </div> 
                        </div>    
                        <div class="col-lg-3">
                            <div class="form-group">
                                <input type="text" class="form-control input" placeholder="Contact no,name,token no" id="search" name="search" style="width: 300px;margin-top: 10px;">
                            </div>
            
                           
                        </div>    
                    </div>
                    <table class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">Sr.No</th>
                                <th class="wd-15p">Token No</th>
                                <th class="wd-15p">Customer Details</th>
                                <th class="wd-15p">Waiting Time</th>
                                <th class="wd-15p">Status</th>
                                <th class="wd-15p">Datetime</th>
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
function cb(date) {
    $('#reportrange span').html(date.format('MMMM D, YYYY'));
    $('#date_from').val(date.format('YYYY-MM-DD'));
}

const today = moment();

$('#reportrange').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    startDate: today,
    maxDate: today,
    locale: {
        format: 'MMMM D, YYYY'
    }
}, function (date) {
    cb(date);             
    dataListView.draw(); 
});

cb(today); 

let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function () {
        $('#processingLoader').show();
    })
    .on('draw.dt', function () {
        $('#processingLoader').hide();
    }).DataTable({
        processing: true,
        serverSide: true,
        bFilter: false,
        ajax: {
            url: "{{ route('admin.prequeue-datatable') }}",
            dataType: "json",
            type: "POST",
            data: function (d) {
                d.search1 = $('#search').val(),
                d.date_from = $('#date_from').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: "sr_no", orderable: false },
            { data: "token_no", orderable: false },
            { data: "cust_details", orderable: false },
            { data: "wating_time", orderable: false },
            { data: "status", orderable: false },
            { data: "created_at", orderable: false },
            {
                data: "action",
                orderable: false,
                searchable: false
            }
        ],
        searchDelay: 1500,
        columnDefs: [
            {
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0
            },
            {
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function (data, type, full) 
                {
                    let startUrl = `{{ route('admin.eyetest.start', ':tid') }}`.replace(':tid', full['tid']);
                    return (`<div class="dropdown"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button><div class="dropdown-menu">`+
                        `<a class="dropdown-item" href="${startUrl}">Start Eye Test</a>` +
                        `<a class="action-hold dropdown-item" href="#" data-id="${full['test_id']}">Hold</a>`+
                        `<a class="action-skip dropdown-item" href="#" data-id="${full['test_id']}">Skip</a>`+
                    `</div></div>`);
                }
            }
        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-4"i><"col-sm-12 col-md-4"p>>',
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
                renderer: function (api, rowIdx, columns) {
                    let data = $.map(columns, function (col) {
                        return col.title !== ''
                            ? `<tr data-dt-column="${col.columnIndex}">
                                    <td>${col.title}:</td>
                                    <td>${col.data}</td>
                               </tr>`
                            : '';
                    }).join('');
                    return data ? $('<table class="table"/>').append('<tbody>' + data + '</tbody>') : false;
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
$('.input').on('keyup', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    }.bind(this), 500);
});

$('.select').on('change', function () {
    const column = dataListView.column($(this).attr('name'));
    column.search($(this).val()).draw();
});

$("table").delegate(".action-hold", "click", function (e) {
    e.stopPropagation();
    let id = $(this).data('id');
    Swal.fire({
        title: "{{ __('Are you sure ?') }}",
        text: "{{ __('You would not be able to revert this!') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes, Hold it!') }}",
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger ml-2'
        },
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url: "{{ url('/eyetesthold') }}/" + id + "/destroy",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    showResponseMessage(data);
                },
                error: function (reject) {
                    if (reject.status === 422) {
                        let errors = reject.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr['warning'](value[0], "{{ __('locale.labels.attention') }}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        });
                    } else {
                        toastr['warning'](reject.responseJSON.message, "{{ __('locale.labels.attention') }}", {
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

$("table").delegate(".action-skip", "click", function (e) {
    e.stopPropagation();
    let id = $(this).data('id');
    Swal.fire({
        title: "{{ __('Are you sure ?') }}",
        text: "{{ __('You would not be able to revert this!') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes, Skip it!') }}",
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger ml-2'
        },
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url: "{{ url('/eyetestskip') }}/" + id + "/destroy",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    showResponseMessage(data);
                },
                error: function (reject) {
                    if (reject.status === 422) {
                        let errors = reject.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr['warning'](value[0], "{{ __('locale.labels.attention') }}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        });
                    } else {
                        toastr['warning'](reject.responseJSON.message, "{{ __('locale.labels.attention') }}", {
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


function showResponseMessage(data) {
    if (data.status === 'success') {
        $.toaster({ priority: 'success', title: 'Success..!', message: data.message });
        dataListView.draw();
    } else if (data.status === 'error') {
        $.toaster({ priority: 'danger', title: 'Opps...!', message: data.message });
        dataListView.draw();
    } else {
        $.toaster({ priority: 'danger', title: 'Opps..!', message: 'Something went wrong. Please try again' });
    }
}
</script>
@endsection

