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
    $isAdmin = $usr->roles[0]->name == 'Admin';
@endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Barcode Wise Discount</h3>
                            <a href="#" class=" btn"  data-toggle="modal" data-target="#productModal">
                                <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                                Bulk Barcode Discount
                            </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="">Select  Store<span class="text-danger">*</span></label>
                            <select class="form-control select" style="height: 32px !important;" id="from_store" name="from_store">
                                <option value="">Select  Store</option>
                              <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                               @foreach($tbl_store as $tbl_store)
                                <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                              @endforeach
                            </select>
                            <span class="error badge text-danger" id="from_storeError"></span>
                        </div>

                        <div class="col-lg-3">
                            <label for="">Barcode </label>
                            <input type="text" class="form-control input" placeholder="Enter Valid barcode" id="barcode_no" name="barcode_no" style="width: 250px;">
                        </div>
                        <div class="col-lg-3" style="margin-top: 30px;">
                            <div class="form-group">
                                <button class="btn btn-success js-btn-next" type="button" id="checkBarcodeBtn">Check Barcode</button>
                            </div> 
                        </div>   
                    </div>
                    <div class="row mb-3">
                        <label for="">Validated Barcodes </label>
                        <textarea class="form-control input" rows="6" id="valid_barcode" name="valid_barcode"></textarea>
                        <button class="btn btn-success" id="submittransferBtn" type="button" style="margin-top:10px">Validate Barcode</button>
                   </div>
                    <br>
                    
                    <div class="row">
                        <table class="table card-table table-vcenter text-nowrap" id="barcodeDetailsTable">
                            <thead>
                                <tr>
                                    <th style="color: #6b6f80;">Barcode</th>
                                    <th style="color: #6b6f80;">Product Code</th>
                                    <th style="color: #6b6f80;">Description</th>
                                    <th style="color: #6b6f80;">Purchase Price</th>
                                    <th style="color: #6b6f80;">Retail Price</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                     </div>
                    <br>
                    <div class="row mb-3">
                        <div class="col-md-3">
                             <input type="text" class="form-control" name="apply_discount" id="apply_discount" placeholder="Enter barcode discount"><br>
                            <button class="btn btn-success" id="submitconfirmBtn"  type="button">Apply Discount</button>
                        </div>
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
                       @if($isAdmin)
                        <div class="col-lg-3">
                            <label for="reportrange" class="form-label">Store</label>
                            <select class="form-control select" id="store_id" name="store_id" style="margin-top:10px">
                                <option value="">Select Store</option>
                                @php $tbl_store = DB::table("tbl_store")->where('status',1)->get(); @endphp
                                @foreach($tbl_store as $tbl)
                                    <option value="{{$tbl->id}}">{{$tbl->store_name}} / ({{$tbl->store_id}})</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

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
                                <th class="wd-15p">Barcode</th>
                                <th class="wd-20p">Product Details	</th>
                                <th class="wd-10p">Discount %	</th>
                                <th class="wd-10p">Purchase Price</th>
                                <th class="wd-10p">Retail Price	</th>
                                <th class="wd-10p">Store </th>
                                <th class="wd-10p">Updated By</th>
                                <th class="wd-10p">Updated At</th>
                                @if($isAdmin)
                                    <th lass="wd-10p">Action</th>
                                @endif
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
                <h5 class="modal-title" id="modalTitle">Start New Import</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="heading">STEP 1</p>
                        <ul>
                            <li>Download respective templates to fill in your data. Use below links to download the latest and updated template.</li>
                            <li style="margin-top: 10px;">
                                <a href="{{asset('import/csv_format_discount_barcode.csv')}}" download>
                                <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Frame Inventory');">Barcode Discount Sample</button>
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
                            <div class="import-bulk-order-upload">
                                <div class="drop-zone">
                                  <form method="POST" action="{{ route('admin.bulk-barcode-discount') }}" enctype="multipart/form-data">
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
                                </div>
                                <div>
                                    <label for="search" class="form-label">Select Store</label>
                                    <select class="form-control select2" style="height: 32px !important;" id="store_id" name="store_id" required>
                                        <option value="">Select  Store</option>
                                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                                       @foreach($tbl_store as $tbl_store)
                                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                                      @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-success" type="submit"> upload</button>
                                </form>
                            </div>
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
        $('.select').select2({
          allowClear: true
        });
    });

    $(document).ready(function () 
    {
        
        $('#checkBarcodeBtn').on('click', function () 
        {
            let barcode = $('#barcode_no').val().trim();
            let from_store = $('#from_store').val();

            if (barcode === '') {
                $.toaster({ priority: "warning", title: "Error..!", message: "Please enter barcode." });
                return;
            }
    
            if (from_store === '') {
                $.toaster({ priority: "warning", title: "Error..!", message: "Please select Transferred From store." });
                return;
            }
            

    
            $.ajax({
                url: "{{ route('admin.check-barcode') }}", 
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    barcode: barcode,
                    from_store: from_store,
                    
                },
                success: function (response) {
                    if (response.valid) 
                    {
                        let currentText = $('#valid_barcode').val();
                        if (!currentText.includes(barcode)) {
                            $('#valid_barcode').val(currentText + (currentText ? "\n" : "") + barcode);
                        }
                        $('#barcode_no').val('').focus();
                    } 
                    else 
                    {
                        $.toaster({ priority: "warning", title: "Error..!", message: "Invalid barcode." });
                    }
                },
                error: function () {
                    $.toaster({ priority: "warning", title: "Error..!", message: "Server error. Try again." });
                }
            });
        });
    });
    
    
    $(document).on("click", "#submittransferBtn", function () {
        let rawBarcodes = $('#valid_barcode').val().trim();
        let from_store = $('#from_store').val();
    
        if (from_store === '') {
            $.toaster({ priority: "warning", title: "Error..!", message: "Please select Transferred From store." });
            return;
        }
    
        if (rawBarcodes === '') {
            $.toaster({ priority: "warning", title: "Error..!", message: "No barcodes entered." });
            return;
        }
    
        let barcodeArray = rawBarcodes.split('\n').map(b => b.trim()).filter(b => b !== '');
    
        $.ajax({
            url: "{{ route('admin.get-barcode-details') }}", // Your route
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                barcodes: barcodeArray,
            },
            success: function (response) {
                if (response.success && response.data.length > 0) {
                    let tbody = $('#barcodeDetailsTable tbody');
                    tbody.empty(); // Clear previous results
    
                    response.data.forEach(item => {
                        let row = `
                            <tr>
                                <td class="barcode_no">${item.barcode_no}</td>
                                <td class="product_code">${item.product_code}</td>
                                <td class="product_details">${item.product_details}</td>
                                <td class="purchase_price"><input type="text" class="form-control input" name="purchase_price[]" value="${item.purchase_price}" readonly></td>
                                <td class="retail_price"><input type="text" class="form-control input" name="retail_price[]" value="${item.retail_price}" readonly></td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } 
                else
                {
                    $.toaster({ priority: "warning", title: "Error..!", message: "No valid barcode details found." });
                }
            },
            error: function () {
                alert("Server error. Please try again.");
                $.toaster({ priority: "warning", title: "Error..!", message: "Server error. Please try again." });
            }
        });
    });

    $(document).on("click", "#submitconfirmBtn", function () {
        let rows = $("#barcodeDetailsTable tbody tr");
        let rowData = [];
    
        rows.each(function () {
            let $row = $(this);
    
            rowData.push({
                barcode_no: $row.find('.barcode_no').text().trim(),
                product_code: $row.find('.product_code').text().trim(),
                product_details: $row.find('.product_details').text().trim(),
                retail_price: $row.find('.retail_price input').val().trim(),
                purchase_price: $row.find('.purchase_price input').val().trim()
            });
        });
    
        if (rowData.length === 0) {
            $.toaster({ priority: "warning", title: "Error..!", message: "No barcode found." });
            return;
        }
    
        let from_store = $('#from_store').val();
        let apply_discount = $('#apply_discount').val();
    
        if (apply_discount === '' ) 
        {
            $.toaster({ priority: "warning", title: "Error..!", message: "barcode discount feild required." });
            return;
        }
    
        $.ajax({
            url: "{{ route('admin.apply-discount-barcode') }}", // replace with your actual route
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                from_store: from_store,
                apply_discount: apply_discount,
                items: rowData
            },
            success: function (response) {
                if (response.success) 
                {
                    $.toaster({ priority: "success", title: "Success..!", message: "Barcode discount apply successfully." });
                    setTimeout(function () {
                        window.location.href = "{{ route('admin.barcode-wise-discount') }}";
                    }, 1000);
                    
                } else {
                    $.toaster({ priority: "warning", title: "Error..!", message: "Transfer failed." });
                }
            },
            error: function () {
                $.toaster({ priority: "warning", title: "Error..!", message: "Error in apply discount." });
            }
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
            url: "{{ route('admin.discountbarcode-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                 d.search_input = $('#search').val(),
                d.product_type = $('#product_type').val(),
                d.store_id = $('#store_id').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "barcode",
                orderable: false,
            },

            {
                "data": "product_details",
                orderable: false,
            },
            {
                "data": "discount",
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
                "data": "store_name",
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
