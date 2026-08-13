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
                    <div class="col-lg-8">
                         <h3>Additional Discount & Other Cost</h3>
                    </div>
                    <a href="#" class=" btn" data-toggle="modal" data-target="#productModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Create Additional Discount
                        </a>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="row align-items-end g-3">
                        
                        <div class="col-md-3">
                            <label for="search" class="form-label">Supplier Name</label>
                            <select class="form-control select" style="height: 32px !important;" id="supplier_company" name="supplier_company">
                                <option value="">Select Supplier Name </option>
                                @foreach($suppliers as $supplier)
                                <option value="{{$supplier->supplier_company}}">{{$supplier->supplier_company}} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="payment_method" class="form-label">Purchase Bill Number</label>
                            <input type="text" class="form-control input" placeholder="Bill Number" id="search" name="search" style="width: 300px;">
                        </div>
                    </div>
                </div>
            </div>
            <br>
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
                                <th class="wd-15p">Sr.No</th>
                                <th class="wd-15p">Supplier Name</th>
                                <th class="wd-20p">Purchase Bill Number</th>
                                <th class="wd-10p">Purchase Date</th>
                                <th class="wd-10p">Additional Discount</th>
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

<div class="modal fade" data-backdrop="static" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Additional Discount & Other Cost</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               
                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <span class="error badge text-danger" id="bill_noError"></span>
                            <div class="row">
                                <div class="col-4">
                                    <select class="form-control select2" style="height: 32px !important;" id="supplier_company" name="supplier_company">
                                        <option value="">Select Supplier Name </option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->supplier_company}}">{{$supplier->supplier_company}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <input class="form-control"  placeholder="Purchase Bill Number" id="bill_no" name="bill_no">
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-gradient js-btn-next" type="button" title="Next">Search</button>
                                </div>
                            </div>    
                            <hr/>
                            <div id="additionaldis"></div>
                            
                        </div>
                 
                    </div>
            </div>
        </div>
    </div>
</div>


</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#supplier_company').select2({
        allowClear: true,
        width: '100%'
    });
    
    $('.select').select2({
        allowClear: true,
        width: '100%'
    });

    $(".js-btn-next").on('click', function () {
        $("#bill_noError").text("");

        let supplier = $("#supplier_company").val();
        let billNo = $("#bill_no").val().trim();

        let hasError = false;

        if (!supplier && !billNo) {
            $("#bill_noError").text("Please enter supplier or bill number.");
            hasError = true;
        }

        if (hasError) return;
        $('#additionaldis').empty();
        $.ajax({
            url: "{{ route('admin.purchase.filter') }}", 
            type: "GET",
            data: {
                supplier_company: supplier,
                bill_no: billNo
            },
            beforeSend: function () {
                $(".js-btn-next").prop("disabled", true).text("Searching...");
            },
            success: function (response) 
            {
                $('#additionaldis').append(response);
            },
            error: function (xhr) {
                alert("An error occurred. Please try again.");
            },
            complete: function () {
                $(".js-btn-next").prop("disabled", false).text("Search");
            }
        });
    });
});
</script>



<script>
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
            "url": "{{ route('admin.additional-discount-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) {
                d.search_input = $('#search').val(),
                d.supplier_company  = $('#supplier_company').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [{
                "data": 'sr_no',
                orderable: false,
                searchable: false
            },
            {
                "data": "supplier_name",
                orderable: false,
                searchable: false
            },
            {
                "data": "bill_no",
                orderable: false,
                searchable: false
            },
            {
                "data": "p_date",
                orderable: false,
            },
            {
                "data": "additional_dis",
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
                    return (`
                             <button class="btn btn-danger remove_btn1 action-delete" type="button" data-id="` + full['encryptedId'] + `"><i class="fa fa-trash"></i></button>
                            `
                    );
                }    
            }    

           
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
                url: "{{ url('admin.additionaldis-delete') }}/" + id,
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
