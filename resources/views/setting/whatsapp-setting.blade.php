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
$sms = DB::table("tbl_sms")->first();
@endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>SMS Settings</h3>
                    </div>
                </div>
            </div>
            <hr/>
            <form id="whatsappForm" method="POST" method="POST" enctype="multipart/form-data">
             @csrf
             
             <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Select Store
              </label>
              <div class="col-lg-3">
                  <select class="form-control select" style="height: 32px !important" id="store_id" name="store_id" >
                    <option value="">Select  Store</option>
                  <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                   @foreach($tbl_store as $tbl_store)
                    <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                  @endforeach
                </select>
                  <span class="error badge text-danger" id="store_idError"></span>
              </div>
            </div>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                WhatsApp Integration
              </label>
              <div class="col-lg-8">
                <div class="d-flex" style="margin-top: 10px;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="integration_status" id="inlineRadio1" value="0">
                      <label class="form-check-label" for="inlineRadio1">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="integration_status" id="inlineRadio2" value="1" >
                      <label class="form-check-label" for="inlineRadio2">No</label>
                    </div>
                </div>    
                  
              </div>
            </div>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                WhatsApp Message
              </label>
              <div class="col-lg-8">
                <div class="d-flex" style="margin-top: 10px;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="whatsapp_mesg" id="inlineRadio3" value="0">
                      <label class="form-check-label" for="inlineRadio3">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="whatsapp_mesg" id="inlineRadio4" value="1" >
                      <label class="form-check-label" for="inlineRadio4">No</label>
                    </div>
                </div>    
                  
              </div>
            </div>
            <br>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                WhatsApp Instance
              </label>
              <div class="col-lg-3">
                <input class="form-control" type="text" name="whatsapp_instance" id="whatsapp_instance" >
                <span class="error badge text-danger" id="whatsapp_instanceError"></span>
              </div>
            </div>
            <br>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Prefix Country Code for Whatsapp Mobile Number
              </label>
              <div class="col-lg-3">
                <input class="form-control" type="text" name="prefix" id="prefix" value="91" >
              </div>
            </div>
            <br>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                WhatsApp Mobile Number
              </label>
              <div class="col-lg-3">
                <input class="form-control" type="text" name="whatsapp_no" id="whatsapp_no"  >
                <span class="error badge text-danger" id="whatsapp_noError"></span>
              </div>
            </div>

            <br>
             <div class="button-row d-flex mt-4">
                <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Update
                </button>
            </div>
            </form>
            
                  
        </div>
    </div>
</section>
@endsection

@section('scripts')

<script>

  
  
  $("#whatsappForm").submit(function(e) {
    e.preventDefault(); 
        
    let isValid = true;
    let class_name = '';

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        
    let store_id = document.getElementById("store_id" + class_name).value.trim();
    let whatsapp_no = document.getElementById("whatsapp_no" + class_name).value.trim();
    let whatsapp_instance = document.getElementById("whatsapp_instance" + class_name).value.trim();
    
    if (store_id === "") {
        document.getElementById("store_idError" + class_name).textContent = "Store is required.";
        document.getElementById("store_id" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (whatsapp_instance === "") {
        document.getElementById("whatsapp_instanceError" + class_name).textContent = "whatsapp instance is required.";
        document.getElementById("whatsapp_instance" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (whatsapp_no === "") {
        document.getElementById("whatsapp_noError" + class_name).textContent = "Number is required.";
        document.getElementById("whatsapp_no" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    let form = $("#whatsappForm")[0];
    let data = new FormData(form);
    

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.whatsapp-update') }}",
        data: data,
        dataType: "json",
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
                $.each(response.error, function(key, value){
                    let field = $('[name="'+key+'"]');
                    field.addClass('is-invalid');
                    field.closest('td').find('.error').text(value[0]);
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        }
    });
});


$(document).ready(function () {

    $('#store_id').on('change', function () {
        let storeId = $(this).val();

        if (storeId === '') {
            return;
        }

        $.ajax({
            url: "{{ route('admin.get-whatsapp-details', ':id') }}".replace(':id', storeId),
            type: "GET",
            dataType: "json",
            success: function (response) {

                if (response.status) {
                    let data = response.data;

                    // Radio buttons
                    $("input[name='integration_status'][value='" + data.integration_status + "']").prop('checked', true);
                    $("input[name='whatsapp_mesg'][value='" + data.whatsapp_mesg + "']").prop('checked', true);

                    // Input fields
                    $('#whatsapp_instance').val(data.whatsapp_instance);
                    $('#prefix').val(data.prefix ?? '91');
                    $('#whatsapp_no').val(data.whatsapp_no);
                }
            }
        });
    });

});
</script>


@endsection
