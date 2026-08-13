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
                        <h3>Purchase Return</h3>
                        <a href="{{route('admin.purchase-return')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                             Purchase Return History
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                 @if($usr->roles[0]->name == 'Admin')
                <div class="col-md-3"  style="margin-top: 10px;">
                        <select class="form-control select" style="height: 32px !important;" id="store_id" name="store_id">
                            <option value="">Select  Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
                </div>
                @endif
                <div class="col-md-3" style="margin-top: 10px;">
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
                <div class="col-lg-3" style="margin-top:10px">
                    <select class="form-control select" style="height: 32px !important;" id="search_by" name="search_by">
                      <option value="">Search By</option>    
                      <option value="1">Supplier Name</option>    
                      <option value="2">Product Code</option>
                      <option value="3">Barcode</option>
                      <option value="4">Purchase Bill No</option>
                      <option value="5">Company Name</option>
                      <option value="6">Purchase Date</option>
                    </select>
                </div>
                <div class="col-lg-3" style="margin-top:10px">
                    <input type="text" class="form-control" id="search_text" placeholder="Search text here..." name="search_text" >
                </div>
                
                
                <div class="col-lg-3" style="margin-top: 10px;">
                    <div class="form-group">
                        <button class="btn btn-gradient js-btn-next" type="button">Search</button>
                    </div> 
                </div>   
            </div>
            <span class="error badge text-danger" id="bill_noError"></span>
            <div id="returnproductlist"></div>

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

    $(".js-btn-next").on('click', function () {
        $("#bill_noError").text("");

        let product_type = $("#product_type").val();
        let search_by = $("#search_by").val().trim();
        let search_text = $("#search_text").val().trim();
        let store_id = $("#store_id").val();

        let hasError = false;

        if (!product_type) {
            $("#bill_noError").text("Please select product or bill number.");
            hasError = true;
        }

        if (hasError) return;
        $('#returnproductlist').empty();
        $.ajax({
            url: "{{ route('admin.purchase-product-list') }}", 
            type: "GET",
            data: {
                product_type: product_type,
                store_id: store_id,
                search_text: search_text,
                search_by: search_by
            },
            beforeSend: function () {
                $(".js-btn-next").prop("disabled", true).text("Searching...");
            },
            success: function (response) 
            {
                $('#returnproductlist').append(response);
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




@endsection
