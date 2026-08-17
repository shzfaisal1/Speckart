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
                        <h3>Transfer Stock Using Barcode</h3>
                        <a href="{{route('admin.stock-transfer')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                             Transfer Stock History
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                 <div class="col-md-3">
                    <label for="">Transferred From <span class="text-danger">*</span></label>
                    <select class="form-control select" style="height: 32px !important;" id="from_store" name="from_store">
                        <option value="">Select  Store</option>
                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                       @foreach($tbl_store as $tbl_store)
                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                      @endforeach
                    </select>
                    <span class="error badge text-danger" id="from_storeError"></span>
                </div>
                <div class="col-md-3">
                    <label for="">From Store GST Number  <span class="text-danger">*</span></label>
                    <input class="form-control"  id="from_gst_no" name="from_gst_no" readonly>
                    <span class="error badge text-danger" id="from_gst_noError"></span>
                </div>
                 <div class="col-md-3">
                    <label for="">Transferred To <span class="text-danger">*</span></label>
                    <select class="form-control select" style="height: 32px !important;" id="to_store" name="to_store">
                        <option value="">Select  Store</option>
                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                       @foreach($tbl_store as $tbl_store)
                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                      @endforeach
                    </select>
                    <span class="error badge text-danger" id="to_storeError"></span>
                </div>
                 <div class="col-md-3">
                    <label for="">To Store GST Number  <span class="text-danger">*</span></label>
                    <input class="form-control"   id="to_gst_no" name="to_gst_no" readonly>
                    <span class="error badge text-danger" id="to_gst_noError"></span>
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
                <div class="col-md-4">
                    <label for="">Validated Barcodes </label>
                    <textarea class="form-control input" rows="6" id="valid_barcode" name="valid_barcode"></textarea>
                </div>
                <div class="col-md-8">
                    <label for="">Comment  </label>
                    <textarea class="form-control input" rows="6" id="transfer_comment" name="transfer_comment"></textarea>
                </div> 
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <button class="btn btn-success" id="submittransferBtn" type="button">Validate Barcode</button>
                </div>
            </div>
            <br>
            
            <div class="row">
                <table class="table card-table table-vcenter text-nowrap" id="barcodeDetailsTable">
                    <thead>
                        <tr>
                            <th style="color: #6b6f80;">Barcode</th>
                            <th style="color: #6b6f80;">Product ID</th>
                            <th style="color: #6b6f80;">Product Type</th>
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
                    <button class="btn btn-success" id="submitconfirmBtn"  type="button">Confirm Transfer</button>
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

    $(document).ready(function () 
    {
        $('#from_store').change(function() {
            let storeId = $(this).val();
    
            if(storeId && storeId === $('#to_store').val()) {
                $.toaster({
                    priority: 'danger',
                    title: '❌ error',
                    message: 'From Store and To Store cannot be the same!'
                });
                $(this).val(''); // reset selection
                $('#from_gst_no').val('');
                return;
            }
    
            if(storeId) {
                $.get('/get-store-gst/' + storeId, function(data) {
                    $('#from_gst_no').val(data.gst_no);
                });
            } else {
                $('#from_gst_no').val('');
            }
        });
    
        // To Store GST fetch
        $('#to_store').change(function() {
            let storeId = $(this).val();
    
            if(storeId && storeId === $('#from_store').val()) {
                $.toaster({
                    priority: 'danger',
                    title: '❌ error',
                    message: 'From Store and To Store cannot be the same!'
                });
                $(this).val(''); // reset selection
                $('#to_gst_no').val('');
                return;
            }
    
            if(storeId) {
                $.get('/get-store-gst/' + storeId, function(data) {
                    $('#to_gst_no').val(data.gst_no);
                });
            } else {
                $('#to_gst_no').val('');
            }
        });
        $('#checkBarcodeBtn').on('click', function () 
        {
            let barcode = $('#barcode_no').val().trim();
            let from_store = $('#from_store').val();
            let to_store = $('#to_store').val();
            
            let from_gst_no = $('#from_gst_no').val();
            let to_gst_no = $('#to_gst_no').val();
            
            if(from_gst_no != to_gst_no)
            {
                $.toaster({ priority: "warning", title: "Error..!", message: "Gst Not Same Both Store." });
                return;
            }
    
            if (barcode === '') {
                $.toaster({ priority: "warning", title: "Error..!", message: "Please enter barcode." });
                return;
            }
    
            if (from_store === '') {
                $.toaster({ priority: "warning", title: "Error..!", message: "Please select Transferred From store." });
                return;
            }
            
            if (to_store === '') {
                $.toaster({ priority: "warning", title: "Error..!", message: "Please select Transferred To store." });
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
                                <td class="product_id">${item.product_id}</td>
                                <td class="product_type">${item.product_type}</td>
                                <td class="product_code">${item.product_code}</td>
                                <td class="product_details">${item.product_details}</td>
                                <td class="purchase_price"><input type="text" class="form-control input" name="purchase_price[]" value="${item.purchase_price}" readonly></td>
                                <td class="retail_price"><input type="text" class="form-control input" name="retail_price[]" value="${item.retail_price}" ></td>
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
                product_id: $row.find('.product_id').text().trim(),
                product_type: $row.find('.product_type').text().trim(),
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
        let to_store = $('#to_store').val();
        let comment = $('#transfer_comment').val();
    
        if (from_store === '' || to_store === '') {
            $.toaster({ priority: "warning", title: "Error..!", message: "Please select both 'Transferred From' and 'Transferred To' stores." });
            return;
        }
    
        $.ajax({
            url: "{{ route('admin.confirm-transfer') }}", // replace with your actual route
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                from_store: from_store,
                to_store: to_store,
                comment: comment,
                items: rowData
            },
            success: function (response) {
                if (response.success) 
                {
                    $.toaster({ priority: "success", title: "Success..!", message: "Stock transfer is being processed in the background." });
                    setTimeout(function () {
                        window.location.href = "{{ route('admin.stock-transfer') }}";
                    }, 1000);
                    
                } else {
                    $.toaster({ priority: "warning", title: "Error..!", message: "Transfer failed." });
                }
            },
            error: function () {
                $.toaster({ priority: "warning", title: "Error..!", message: "Error in transfer confirmation." });
            }
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
