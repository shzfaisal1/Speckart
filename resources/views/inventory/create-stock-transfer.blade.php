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
                        <h3>Transfer Stock</h3>
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
                    <label for="">Product Type<span class="text-danger">*</span></label>
                    <select class="form-control select" style="height: 32px !important;" id="product_type" name="product_type">
                        <option value="">Select Product </option>
                        <option value="Frame">Frame</option>
                        <option value="Glass">Glass</option>
                        <option value="Goggles">Goggles</option>
                        <option value="Lens">Contact Lens</option>
                        <option value="Solution">Solution</option>
                        <option value="Other">Other</option>
                    </select>
                    <span class="error badge text-danger" id="product_typeError"></span>
                </div>
                <div class="col-lg-3">
                    <label for="">Product Code</label>
                    <input type="text" class="form-control input" placeholder="Product Code" id="product_code" name="product_code" style="width: 250px;">
                </div>
                <div class="col-lg-3">
                    <label for="">Description</label>
                    <input type="text" class="form-control input" placeholder="Description" id="product_details" name="product_details" style="width: 250px;">
                </div> 
                <div class="col-lg-3" style="margin-top: 30px;">
                    <div class="form-group">
                        <button class="btn btn-gradient js-btn-next" type="button">Search</button>
                    </div> 
                </div>   
            </div>
            <span class="error badge text-danger" id="samestoreError"></span>
            <div id="returnstocklist"></div>

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

    $(".js-btn-next").on('click', function () 
    {
        $("#from_storeError").text("");
        $("#to_storeError").text("");
        $("#product_typeError").text("");

        let from_store = $("#from_store").val();
        let to_store = $("#to_store").val();
        let product_type = $("#product_type").val();
        let product_code = $("#product_code").val();
        let product_details = $("#product_details").val();

        let hasError = false;

        if (!from_store) 
        {
            $("#from_storeError").text("Please select Transfer From.");
            hasError = true;
        }
        
        if (!to_store) 
        {
            $("#to_storeError").text("Please select Transfer To.");
            hasError = true;
        }
        
        if (!product_type) 
        {
            $("#product_typeError").text("Please select product.");
            hasError = true;
        }
        
        if (from_store == to_store) 
        {
            $("#samestoreError").text("From Store or To Store Does Not Same .");
            hasError = true;
        }

        if (hasError) return;
        $('#returnstocklist').empty();
        $.ajax({
            url: "{{ route('admin.stock-product-list') }}", 
            type: "GET",
            data: {
                from_store: from_store,
                to_store: to_store,
                product_type: product_type,
                product_code: product_code,
                product_details: product_details
            },
            beforeSend: function () {
                $(".js-btn-next").prop("disabled", true).text("Searching...");
            },
            success: function (response) 
            {
                $('#returnstocklist').append(response);
            },
            error: function (xhr) {
                alert("An error occurred. Please try again.");
            },
            complete: function () {
                $(".js-btn-next").prop("disabled", false).text("Search");
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
<script>


$(document).on("click", "#submitreturnBtn", function () 
{
    if ($('.row-checkbox:checked').length === 0) {
        $.toaster({ priority: "warning", title: "Oops..!", message: "Please select at least one product." });
        e.preventDefault();
        return false;
    }
    
    if (!confirm("Are you sure you want to return this product?")) {
        return;
    }

    let selectedProducts = [];
    $(".row-checkbox:checked").each(function () {
        selectedProducts.push($(this).val());
    });

    var return_comment = $("#return_comment").val();
    var purchase_id = $("#purchase_id").val();
    
    $("#submitreturnBtn").prop("disabled", true);

    $.ajax({
        url: "{{ route('admin.purchase-returen-stored') }}",
        type: "POST",
        data: {
           _token: "{{ csrf_token() }}",
            product_id: selectedProducts,
            return_comment: return_comment,
            purchase_id: purchase_id,
        },
        success: function (response) 
        {
            $.toaster({ priority: "success", title: "Success..!", message: "Product return successfully." });
            setTimeout(function () 
            {
                window.location.href = "{{ route('admin.purchase-return') }}";
            }, 1000);
        },
        error: function (xhr)
        {
            $.toaster({ priority: "warning", title: "Oops..!", message: "Something went wrong!" });
            $("#submitreturnBtn").prop("disabled", false);
        }
    });
});
</script>

<script>
$(document).on("click", "#submittransferBtn", function () {
    $(this).prop("disabled", true);

    let productData = [];
    let hasValidTransfer = false;

    $("#datatable2 tbody tr").each(function () {
        let $row = $(this);

        let product_type = $row.find("td:eq(1)").text().trim();
        let product_code = $row.find("td:eq(2)").text().trim();
        let product_details = $row.find("td:eq(3)").text().trim();
        let available_quantity = parseFloat($row.find('input[name="available_quantity[]"]').val()) || 0;
        let transfer_quantity = parseFloat($row.find('input[name="transfer_quantity[]"]').val()) || 0;
        let rem_quantity = parseFloat($row.find('input[name="rem_quantity[]"]').val()) || 0;
        let retail_price = parseFloat($row.find('input[name="retail_price[]"]').val()) || 0;
        let perbox = parseFloat($row.find('input[name="perbox[]"]').val()) || '';

        if (transfer_quantity > 0) {
            hasValidTransfer = true;

            productData.push({
                product_type: product_type,
                product_code: product_code,
                product_details: product_details,
                available_quantity: available_quantity,
                transfer_quantity: transfer_quantity,
                rem_quantity: rem_quantity,
                retail_price: retail_price,
                perbox: perbox
            });
        }
    });

    let transfer_comment = $("#transfer_comment").val();
    let stock_id = $("#stock_id").val();
    let from_store = $("#from_store").val();
    let to_store = $("#to_store").val();

    if (!hasValidTransfer) {
        $.toaster({ priority: "warning", title: "Warning", message: "Please enter transfer quantity for at least one product." });
        $("#submittransferBtn").prop("disabled", false);
        return;
    }

    $.ajax({
        url: "{{ route('admin.stock-transfer-update') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            products: productData,
            transfer_comment: transfer_comment,
            stock_id: stock_id,
            from_store: from_store,
            to_store: to_store,
        },
        success: function (response) {
            $.toaster({ priority: "success", title: "Success..!", message: "Product transfred successfully." });
            setTimeout(function () {
                window.location.href = "{{ route('admin.stock-transfer') }}";
            }, 1000);
        },
        error: function (xhr) {
            $.toaster({ priority: "warning", title: "Oops..!", message: "Something went wrong!" });
            $("#submittransferBtn").prop("disabled", false);
        }
    });
});

</script>




@endsection
