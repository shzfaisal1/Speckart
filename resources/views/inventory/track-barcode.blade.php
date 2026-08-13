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
                        <h3>Track Barcode Record</h3>
                        
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3" style="margin-top: 10px;">
                    <select class="form-control select" style="height: 32px !important;margin-top:10px" id="store_id" name="store_id">
                            <option value="">Select  Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Barcode" id="barcode_no" name="barcode_no" style="width: 250px;margin-top: 10px;">
                    </div>
                </div> 
                <div class="col-lg-3" style="margin-top: 10px;">
                    <div class="form-group">
                        <button class="btn btn-gradient js-btn-next" type="button">Search</button>
                    </div> 
                </div>   
            </div>
            <span class="error badge text-danger" id="bill_noError"></span>
            <div id="returnbarcodelist"></div>

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

        let store_id = $("#store_id").val();
        let barcode_no = $("#barcode_no").val().trim();

        let hasError = false;

        if (!store_id && !barcode_no) {
            $("#bill_noError").text("Please select store or barcode no.");
            hasError = true;
        }

        if (hasError) return;
        $('#returnbarcodelist').empty();
        $.ajax({
            url: "{{ route('admin.barcode-activity-list') }}", 
            type: "GET",
            data: {
                store_id: store_id,
                barcode_no: barcode_no
            },
            beforeSend: function () {
                $(".js-btn-next").prop("disabled", true).text("Searching...");
            },
            success: function (response) 
            {
                $('#returnbarcodelist').append(response);
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




@endsection
