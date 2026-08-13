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

.drop-zone__input {
  opacity: 0;
  position: absolute;
  z-index: 10;
  width: 100%;
  height: 100%;
  cursor: pointer;
}

</style>  

@endsection
@section('content')
@php
    $usr = Auth::guard()->user();
    $isAdmin = $usr->roles[0]->name == 'Admin';
@endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Product Wise Discount</h3>
                           
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="row align-items-end g-3">
                       
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
                                <th class="wd-15p">Product ID</th>
                                <th class="wd-20p">Product Code	</th>
                                <th class="wd-10p">Product Type	</th>
                                <th class="wd-10p">Product Details	</th>
                                <th class="wd-10p">Discount %</th>
                                <th class="wd-10p">Updated By</th>
                                <th class="wd-10p">Updated At</th>
                               
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
    $(document).ready(function() {
        $('.select').select2({
          allowClear: true
        });
    });


    let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function () {
        $('#processingLoader').show();
    })
    .on('draw.dt', function () {
        $('#processingLoader').hide();
    })
    .DataTable({

        processing: true,
        serverSide: true,
        bFilter: false,

        ajax: {
            url: "{{ route('admin.discountproduct-datatable') }}",
            type: "POST",
            data: function (d) {
                d.search_input = $('#search').val();
                d.product_type = $('#product_type').val();
                d._token = "{{ csrf_token() }}";
            }
        },

        columns: [
            { data: "product_id", orderable: false },
            { data: "product_code", orderable: false },
            { data: "product_type", orderable: false },
            { data: "productdetails", orderable: false },

            // ✅ Editable Discount Column
            {
                data: "discount",
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <input type="number"
                            class="form-control discount-input"
                            value="${data ?? 0}"
                            data-id="${row.id}"
                            style="width:80px;">
                    `;
                }
            },

            { data: "discount_updated_by", orderable: false },
            { data: "updated_at_discount", orderable: false },
        ],

        columnDefs: [{
            className: 'control',
            orderable: false,
            targets: 0
        }],

        dom: '<"d-flex justify-content-between row"<"col-md-6"l><"col-md-6"f>>t<"d-flex justify-content-between row"<"col-md-6"i><"col-md-6"p>>',

        language: {
            paginate: { previous: '&nbsp;', next: '&nbsp;' },
            sLengthMenu: "_MENU_",
            sZeroRecords: "No results available",
            sSearch: "Search",
            sProcessing: "Processing",
            sInfo: "Showing _START_ to _END_ of _TOTAL_ entries",
            sInfoFiltered: ""
        },

        responsive: true,
        displayLength: 10,
        order: [[0, "desc"]],
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
                    url: "{{ url('/purchase') }}" + '/' + id + '/destroy',
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
<script>
function csvValidation()
{
    var fileInput = document.getElementById('myFile');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.csv)$/i;
    if(!allowedExtensions.exec(filePath)){
         Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "Please upload file having extensions .csv  only.",
            showConfirmButton: true
        });
        fileInput.value = '';
        return false;
    }else{
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('csvPreview').innerHTML = '"'+filePath+'"';
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
}



// Debounce for better performance
let timer;

$(document).on('keyup', '.discount-input', function () {
    clearTimeout(timer);
    let el = $(this);

    timer = setTimeout(function () {
        el.trigger('change');
    }, 800);
});

// Update on change
$(document).on('change', '.discount-input', function () {

    let input = $(this);
    let discount = input.val();
    let id = input.data('id');

    input.prop('disabled', true);

    $.ajax({
        url: "{{ route('admin.update-discount') }}",
        type: "POST",
        data: {
            id: id,
            discount: discount,
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {

            input.prop('disabled', false);

            if (res.status === 'success') {
                $.toaster({ priority : 'success', title : 'Success..!' , message : 'Discount update' });

                dataListView.ajax.reload(null, false);
            } else {
                $.toaster({ priority : 'danger', title : 'Opps...!' , message : 'Update failed' });
            }
        },
        error: function () {
            input.prop('disabled', false);
            $.toaster({ priority : 'danger', title : 'Opps...!' , message : 'Something went wrong' });
        }
    });
});
</script>




@endsection
