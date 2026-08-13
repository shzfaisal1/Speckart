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
                        <h3>Brand Wise Discount</h3>
                           
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <p class="heading">STEP 1</p>
                    <ul>
                        <li>Download respective templates to fill in your data. Use below links to download the latest and updated template.</li>
                        <li style="margin-top: 10px;">
                            <a href="{{asset('import/csv_format_discount_brand.csv')}}" download>
                            <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Frame Inventory');">Brand Discount Sample</button>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <p class="heading">STEP 2</p>
                    <ul>
                        <li>Fill all details as per the rules provided below, make sure you save the file as .csv only. Also, do not delete the header row as it will be removed automatically when our system is importing your data.</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <p class="heading">STEP 3</p>
                    <ul>
                        <li>Open the saved file in NOTEPAD and copy all data and paste it in the box provided. Make sure you select the correct import type and branch for which you want to import respective data.</li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="multisteps-form__content" style="margin: 20px;">
                    <div class="multisteps-form__content1">
                        <div class="row">
                            <div class="import-bulk-order-upload">
                                <form method="POST" action="{{ route('admin.bulk-brand-discount') }}" enctype="multipart/form-data">
                                    <div class="drop-zone">
                                        @csrf
                                        <div>
                                            <img src="{{asset('assets/images/icon/upload.png')}}" alt="" style="width:45px">
                                            <p class="drop-zone__prompt">Drag & Drop files or <span>Browse</span></p>
                                            <h6>Please download and use sample template file.Only csv file format is acceptable</h6>
                                            <p class="drop-zone__prompt">Creating Product</p>
                                            <h6>This is going to take a few minutes. Kindly request your patience.</h6>
                                            <div id="csvPreview" style="color: #1c59bf;font-size: 16px;font-weight: 600;"></div>
                                        </div>
                                        <input type="file" name="myFile" id="myFile" class="drop-zone__input" onchange="return csvValidation()" required>
                                    </div><br>
                                    <div class="col-md-6">
                                        <label for="search" class="form-label">Product</label>
                                        <select class="form-control select" style="height: 32px !important;" name="product_type" required>
                                            <option value="">Select Product </option>
                                            <option value="Frame">Frame</option>
                                            <option value="Glass">Glass</option>
                                            <option value="Goggles">Goggles</option>
                                            <option value="Lens">Contact Lens</option>
                                            <option value="Solution">Solution</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-success" type="submit"> upload</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <hr/>
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
                            <input type="text" class="form-control input" placeholder="Brand Name,Product Code" id="search" name="search" style="width: 250px;">
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
                                <th class="wd-20p">Brand</th>
                                <th class="wd-10p">Product Type	</th>
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
            url: "{{ route('admin.discountbrand-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                 d.search_input = $('#search').val(),
                d.product_type = $('#product_type').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "brand_name",
                orderable: false,
            },


            {
                "data": "product_type",
                orderable: false,
            },
            {
                "data": "discount",
                orderable: false,
            },

            {
                "data": "discount_updated_by",
                orderable: false,
            },
            {
                "data": "updated_at_discount",
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
            

        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        language: {
            paginate: 
            {
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
</script>




@endsection
