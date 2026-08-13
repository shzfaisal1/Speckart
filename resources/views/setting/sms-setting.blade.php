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
            <form id="smsForm" method="POST" method="POST" enctype="multipart/form-data">
             @csrf
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Send Welcome SMS While Creating New Customers 
              </label>
              <div class="col-lg-8">
                <div class="d-flex" style="margin-top: 10px;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="welcome_sms" id="inlineRadio1" value="0" @if($sms->welcome_sms == '0') checked @endif>
                      <label class="form-check-label" for="inlineRadio1">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="welcome_sms" id="inlineRadio2" value="1" @if($sms->welcome_sms == '1') checked @endif>
                      <label class="form-check-label" for="inlineRadio2">No</label>
                    </div>
                </div>    
                  
              </div>
            </div>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Secure Important Actions Using OTP 
              </label>
              <div class="col-lg-8">
                <div class="d-flex" style="margin-top: 10px;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="important_otp_status" id="inlineRadio3" value="0" @if($sms->important_otp_status == '0') checked @endif>
                      <label class="form-check-label" for="inlineRadio3">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="important_otp_status" id="inlineRadio4" value="1" @if($sms->important_otp_status == '1') checked @endif>
                      <label class="form-check-label" for="inlineRadio4">No</label>
                    </div>
                </div>    
                  
              </div>
            </div>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Secure Admin OTP Options
              </label>
              <div class="col-lg-8">
                <div class="d-flex" style="margin-top: 10px;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="secure_otp_option" id="inlineRadio5" value="0"  @if($sms->secure_otp_option == '0') checked @endif>
                      <label class="form-check-label" for="inlineRadio5">Sent to Registered Admin Mobile Number</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="secure_otp_option" id="inlineRadio6" value="1"  @if($sms->secure_otp_option == '1') checked @endif>
                      <label class="form-check-label" for="inlineRadio6">Sent to Branch Owner Mobile Number</label>
                    </div>
                     <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="secure_otp_option" id="inlineRadio7" value="2"  @if($sms->secure_otp_option == '2') checked @endif>
                      <label class="form-check-label" for="inlineRadio7">Sent to given mobile number</label>
                    </div>
                </div>    
                  
              </div>
            </div>
            <div class="row" id="divA"  @if($sms->secure_otp_option != '2') style="display:none" @endif>
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Mobile Number to Get OTP for Secure Important Actions
              </label>
              <div class="col-lg-3">
                  <input class="form-control"  id="manually_mobile_no" value="{{$sms->manually_mobile_no}}" name="manually_mobile_no">
                  
              </div>
            </div>
            <br>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Actions where Secure Important Actions Using OTP is Applicable
              </label>
              <div class="col-lg-8">
                 <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="deleteOrder" id="deleteOrder" value="1" @if($sms->deleteOrder == '1') checked @endif>
                    <label class="form-check-label" for="deleteOrder">Delete Order</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="deleteStock" id="deleteStock" value="1"  @if($sms->deleteStock == '1') checked @endif>
                    <label class="form-check-label" for="deleteStock">Delete Stock</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="deleteChallan" id="deleteChallan" value="1"  @if($sms->deleteChallan == '1') checked @endif>
                    <label class="form-check-label" for="deleteChallan">Delete Challan</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="deleteExpense" id="deleteExpense" value="1"  @if($sms->deleteExpense == '1') checked @endif>
                    <label class="form-check-label" for="deleteExpense">Delete Expense</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="deleteVouchers" id="deleteVouchers" value="1"  @if($sms->deleteVouchers == '1') checked @endif>
                    <label class="form-check-label" for="deleteVouchers">Delete Vouchers</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="deleteProductCode" id="deleteProductCode" value="1"  @if($sms->deleteProductCode == '1') checked @endif>
                    <label class="form-check-label" for="deleteProductCode">Delete Product Code</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="deleteCustomer" id="deleteCustomer" value="1"  @if($sms->deleteCustomer == '1') checked @endif>
                    <label class="form-check-label" for="deleteCustomer">Delete Customer</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="deletePrescription" id="deletePrescription" value="1"  @if($sms->deletePrescription == '1') checked @endif>
                    <label class="form-check-label" for="deletePrescription">Delete Prescription</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="userFranchise" id="userFranchise" value="1"  @if($sms->userFranchise == '1') checked @endif>
                    <label class="form-check-label" for="userFranchise">User &amp; Franchise </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="loyaltyProgram" id="loyaltyProgram" value="1"  @if($sms->loyaltyProgram == '1') checked @endif>
                    <label class="form-check-label" for="loyaltyProgram">Loyalty Program</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="discountCoupons" id="discountCoupons" value="1"  @if($sms->discountCoupons == '1') checked @endif>
                    <label class="form-check-label" for="discountCoupons">Discount Coupons</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="customerAccountOption" id="customerAccountOption" value="1"  @if($sms->customerAccountOption == '1') checked @endif>
                    <label class="form-check-label" for="customerAccountOption">Customer Account Option</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"  name="allowNegativeInventoryInProducts" id="allowNegativeInventoryInProducts" value="1"  @if($sms->allowNegativeInventoryInProducts == '1') checked @endif>
                    <label class="form-check-label" for="allowNegativeInventoryInProducts">Allow Negative Inventory in Products</label>
                </div>
                
                  
              </div>
            </div>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Security for Download Report
              </label>
              <div class="col-lg-8">
                <div class="d-flex" style="margin-top: 10px;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="download_otp_status" id="inlineRadio8" value="0"  @if($sms->download_otp_status == '0') checked @endif>
                      <label class="form-check-label" for="inlineRadio8">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="download_otp_status" id="inlineRadio9" value="1"  @if($sms->download_otp_status == '1') checked @endif>
                      <label class="form-check-label" for="inlineRadio9">No</label>
                    </div>
                </div>    
                  
              </div>
            </div>
            <div class="row">
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Secure Admin OTP Options
              </label>
              <div class="col-lg-8">
                <div class="d-flex" style="margin-top: 10px;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="secure_download_option" id="inlineRadio10" value="0" @if($sms->secure_download_option == '0') checked @endif>
                      <label class="form-check-label" for="inlineRadio10">Sent to Registered Admin Mobile Number</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="secure_download_option" id="inlineRadio11" value="1" @if($sms->secure_download_option == '1') checked @endif>
                      <label class="form-check-label" for="inlineRadio11">Sent to Branch Owner Mobile Number</label>
                    </div>
                     <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="secure_download_option" id="inlineRadio12" value="2" @if($sms->secure_download_option == '2') checked @endif>
                      <label class="form-check-label" for="inlineRadio12">Sent to given mobile number</label>
                    </div>
                </div>    
                  
              </div>
            </div>
            <div class="row" id="divB" @if($sms->secure_download_option != '2') style="display:none" @endif>
              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                Mobile Number to Get OTP for Download Report
              </label>
              <div class="col-lg-3">
                  <input class="form-control" value="{{$sms->manually_mobile_no_report}}"  id="manually_mobile_no_report" name="manually_mobile_no_report">
                  
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
  // Get references
  const otpOptions = document.querySelectorAll('input[name="secure_otp_option"]');
  const divA = document.getElementById('divA');

  // Function to toggle divA visibility
  function toggleDivA() {
    const selectedValue = document.querySelector('input[name="secure_otp_option"]:checked').value;
    if (selectedValue === '2') {
      divA.style.display = 'flex'; // or 'block' if you prefer
    } else {
      divA.style.display = 'none';
    }
  }

  // Attach event listener to all radio buttons
  otpOptions.forEach(radio => {
    radio.addEventListener('change', toggleDivA);
  });

  // Initial check in case value=2 is already selected on page load
  toggleDivA();
</script>
<script>
  // Get references
  const downloadOptions = document.querySelectorAll('input[name="secure_download_option"]');
  const divB = document.getElementById('divB');

  // Function to toggle divB visibility
  function toggleDivB() {
    const selectedValue = document.querySelector('input[name="secure_download_option"]:checked').value;
    if (selectedValue === '2') {
      divB.style.display = 'flex'; // or 'block' if you prefer
    } else {
      divB.style.display = 'none';
    }
  }

  // Attach event listener to all radio buttons
  downloadOptions.forEach(radio => {
    radio.addEventListener('change', toggleDivB);
  });

  // Initial check on page load
  toggleDivB();
  
  
  $("#smsForm").submit(function(e) {
    e.preventDefault(); 

    let form = $("#smsForm")[0];
    let data = new FormData(form);

    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.sms-update') }}",
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
</script>


@endsection
