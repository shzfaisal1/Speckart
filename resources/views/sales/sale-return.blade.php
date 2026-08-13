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
                        <h3>Sale Return</h3>
                        <a href="{{route('admin.sale-return-history')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                             Sale Return History
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-4">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Sale Order Number" id="order_no" name="order_no" style="margin-top: 10px;">
                    </div>
                </div> 
                <div class="col-lg-3" style="margin-top: 10px;">
                    <div class="form-group">
                        <button class="btn btn-success js-btn-next" type="button">Search</button>
                    </div> 
                </div>   
            </div>
            <span class="error badge text-danger" id="order_noError"></span>
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
        $("#order_noError").text("");

        let orderNo = $("#order_no").val().trim();

        let hasError = false;

        if (!orderNo) {
            $("#order_noError").text("Please select  bill number.");
            hasError = true;
        }

        if (hasError) return;
        $('#returnproductlist').empty();
        $.ajax({
            url: "{{ route('admin.sale-product-list') }}", 
            type: "GET",
            data: 
            {
                order_no: orderNo
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
        } 
        else if (data.status === 'error') 
        {
            $.toaster({ priority : 'danger', title : 'Opps...!' , message : data.message });
            dataListView.draw();
        } 
        else 
        {
            $.toaster({ priority : 'danger', title : 'Opps..!' , message : 'Something went wrong. Please try again' });
        }
    }
        


    $(document).on("click", "#submitapprovalBtn", function (e)
    {
        e.preventDefault();
    
        if ($('.row-checkbox:checked').length === 0)
        {
            $.toaster({ priority: "warning", title: "Oops..!", message: "Please select at least one product." });
            return false;
        }
    
        // ✅ PHOTO REQUIRED CHECK
        if ($("#return_photo")[0].files.length === 0)
        {
            $.toaster({ priority: "warning", title: "Oops..!", message: "Please upload return photo." });
            return false;
        }
    
        if (!confirm("Are you sure you want to return this product?"))
        {
            return;
        }
    
        let selectedProducts = [];
        $(".row-checkbox:checked").each(function ()
        {
            selectedProducts.push($(this).val());
        });
    
        let formData = new FormData();
    
        formData.append('_token', "{{ csrf_token() }}");
    
        selectedProducts.forEach(function(id){
            formData.append('product_id[]', id);
        });
    
        formData.append('orderid', $("#orderid").val());
        formData.append('storeid', $("#storeid").val());
        formData.append('contact_no', $("#contact_no").val());
        formData.append('cust_id', $("#cust_id").val());
        formData.append('store_id', $("#store_id").val());
        formData.append('return_remark', $("#return_remark").val());

    
        // ✅ FILE APPEND
        formData.append('return_photo', $("#return_photo")[0].files[0]);
    
        $("#submitapprovalBtn").prop("disabled", true);
    
        $.ajax({
            url: "{{ route('admin.sale-returen-approval-request') }}",
            type: "POST",
            data: formData,
            processData: false,   // IMPORTANT
            contentType: false,   // IMPORTANT
    
            success: function (response)
            {
                $.toaster({ priority: "success", title: "Success..!", message: "Wait for approval from backend side." });
    
                setTimeout(function ()
                {
                    window.location.href = "{{ route('admin.sale-return-request-history') }}";
                }, 1000);
            },
            error: function ()
            {
                $.toaster({ priority: "warning", title: "Oops..!", message: "Something went wrong!" });
                $("#submitapprovalBtn").prop("disabled", false);
            }
        });
    });
</script>




@endsection
