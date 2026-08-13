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
                        <h3>Expenses</h3>
                        <a href="#" class=" btn"  data-toggle="modal" data-target="#expenseModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add Expenses
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
                                <th class="wd-15p">Date</th>
                                <th class="wd-10p">Voucher Number</th>
                                <th class="wd-10p">Total Amount</th>
                                <th class="wd-10p">Purpose</th>
                                <th class="wd-10p">Payment Remark</th>
                                <th class="wd-10p">User Name</th>
                                <th class="wd-10p">Store</th>
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

<div class="modal fade" data-backdrop="static" id="expenseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="voucherForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="uid" id="uid">

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
                                    <label for="">Date <span class="text-danger">*</span></label> <br>
                                    <input type="hidden" class="form-control" id="voucher_date" name="voucher_date">
                                    <div id="reportrange1" class="pull-left"
                                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                        <span></span> <b class="caret"></b>
                                    </div>
                                    
                                    <span class="error badge text-danger" id="voucher_dateError"></span>
                                </div>
                            </div>
                            <div class="row">
                               
                                <div class="col-md-12" style="margin-top: 15px;">
                                    <label for="">Amount  <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Amount"
                                        maxlength="25" name="total_amount" id="total_amount">
                                    <span class="error badge text-danger" id="total_amountError"></span>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Purpose   <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter purpose"
                                        maxlength="25" name="purpose" id="purpose">
                                    <span class="error badge text-danger" id="purposeError"></span>
                                </div>
                                 <div class="col-md-12">
                                    <label for="">Payment Remark</label>
                                    <input type="text" class="form-control" placeholder="Enter Payment Remark"
                                        maxlength="25" name="pay_remark" id="pay_remark">
                                    <span class="error badge text-danger" id="pay_remarkError"></span>
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
@endsection

@section('scripts')
<script>

    function openCreateModal()
    {
        document.getElementById('modalTitle').innerText = 'Add Expense';
        $('#expenseModal').modal('show');
    }
    
    function openEditModal(expense) {
        var expense = JSON.parse(decodeURIComponent(expense));
        
        document.getElementById('uid').value = expense.voucher_id;
        document.getElementById('total_amount').value = expense.total_amount || '';
        document.getElementById('purpose').value = expense.purpose || '';
        document.getElementById('pay_remark').value = expense.pay_remark || '';
        
        if (expense.voucher_date) {
            let date = moment(expense.voucher_date, "YYYY-MM-DD"); // adjust if stored differently
            // update hidden input
            $('#voucher_date').val(date.format("YYYY-MM-DD"));
            // update visible text
            $('#reportrange1 span').html(date.format('MMMM D, YYYY'));
        } 
        

        $('#expenseModal').modal('show');
    }
        
    
  $(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
  });
  
  $(function () {
    var selectedDate = moment();

    function cb1(date) {
        $('#reportrange1 span').html(date.format('MMMM D, YYYY'));
        $('#voucher_date').val(date.format('YYYY-MM-DD'));
    }

    $('#reportrange1').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        startDate: selectedDate,
        locale: {
            format: 'MMMM D, YYYY'
        }
    }, cb);

    cb1(selectedDate);
});


$("#voucherForm").submit(function(e)
{
    e.preventDefault(); 
    
    let isValid = true;
    let class_name = '';

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let total_amount= document.getElementById("total_amount" + class_name).value.trim();
    let purpose = document.getElementById("purpose" + class_name).value.trim();



    if (total_amount === "") {
        document.getElementById("total_amountError" + class_name).textContent = "Amount is required.";
        document.getElementById("total_amount" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    if (purpose === "") {
        document.getElementById("purposeError" + class_name).textContent = "Purpose is required.";
        document.getElementById("purpose" + class_name).classList.add("is-invalid");
        isValid = false;
    }



    if (!isValid) {
        return;
    }

    let form = $("#voucherForm")[0];
    let data = new FormData(form);

    $.ajax({
        type: 'POST',
         url: "{{ route('admin.expense-stored') }}",
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
            location.reload();
        } else {
            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

        }
    }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error: " + textStatus + " - " + errorThrown);
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
            url: "{{ route('admin.expenses-datatable') }}",
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
                "data": "exp_date",
                orderable: false,
            },

            {
                "data": "voucher_no",
                orderable: false,
            },
            {
                "data": "total_amount",
                orderable: false,
            },
            {
                "data": "purpose",
                orderable: false,
            },
            {
                "data": "pay_remark",
                orderable: false,
            },
            {
                "data": "added_by",
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
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function(data, type, full) 
                {
                    let baseUrl = "{{ url('voucher/recepit') }}";
                    let receiptUrl = baseUrl + '/' + full['encryptedId'];
                    
                    return (`<div class="dropdown"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button><div class="dropdown-menu">`+
                                '<a class="dropdown-item" href="#" onclick="openEditModal(`' + encodeURIComponent(JSON.stringify(full)) + '`)">Edit</a>'+
                                `<a class="action-delete dropdown-item" href="${receiptUrl}" target="_blank">Recepit</a>`+
                                `<a class="action-delete dropdown-item" href="#"  data-id="` + full['encryptedId'] + `">Delete</a>`+
                            `</div></div>`
                    );
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
                url: "{{ url('/voucher') }}" + '/' + id + '/destroy',
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
