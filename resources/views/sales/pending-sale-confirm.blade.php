@extends('layouts.master')
@section('styles')
<style>
#supplierListName{
    width: 100%;
    padding: 5px 15px;
}

.suggestion-box-glass {
    z-index: 9999;
    max-height: 200px;
    overflow-y: auto;
}

.suggestion-box {
    z-index: 9999;
    max-height: 200px;
    overflow-y: auto;
}

.col-md-2
{
    margin-bottom: 10px;
}

table, th, td {
  border: 1px solid #444;
}

button 
{
    padding: 0px 4px;
    cursor: pointer;
    background-color: #00484a;
    color: #fff;
}
input.error 
{
  border: 1px solid red;
}

.table thead tr th
{
    color: #6b6f80;
}

.form-group {
    display: inline-flex;
}

}
.table-responsive {

    overflow-x: unset;
    -webkit-overflow-scrolling: touch;
    -ms-overflow-style: -ms-autohiding-scrollbar;
}
  /* Change whole modal background */

</style>
@endsection
@section('content')
@php
    $usr = Auth::guard()->user();
@endphp
 
<div id="ajaxLoader" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading, please wait...</p>
    </div>
</div>
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="domestic-orders-header">
                    <div class="col-md-10">
                        <h3>Confirm Order (ORDER NO : {{$sale->order_no}})</h3>
                    </div>
                   
                    <div class="col-md-2">
                        @if ($usr->can('Sales-Pending-History'))
                        <a href="{{route('admin.sale-pending-history')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Pending Sales Order List
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:10px">
            <div class="card-body" style="padding: 5px 10px;">


                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Sale Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{$sale->sale_date}}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="">Delivery  Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{$sale->delivery_date}}"  readonly>
                        </div>
                         <div class="col-md-3">
                            <label for="">Store <span class="text-danger">*</span></label>
                             <input type="text" class="form-control" value="{{$store->store_name}}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="" class="form-label">Order No: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{$sale->order_no}}"  readonly>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Sales Person<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{$salePerson->name}}"  readonly>
                            
                        </div>
                        <div class="col-md-3">
                            <label for="">Tax Rule <span class="text-danger">*</span></label><br>
                            <input type="text" class="form-control" value="{{$sale->tax_rule}}"  readonly>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                             <div class="row">
                                <div class="col-md-12">
                                   <h5><strong>Customer Information</strong></h5> 
                                </div> 
                                <div class="col-md-3">
                                    <label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{$sale->contact_no}}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Full Name: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{$sale->cust_name}}" readonly >
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Membership ID: </label>
                                    <input type="text" class="form-control" value="{{$sale->membership_id}}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Email Id: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{$sale->email_id}}" readonly>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="" class="form-label">Customer Category:</label>
                                    <input type="text" class="form-control" value="{{$sale->cust_category}}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Gender : <span class="text-danger">*</span></label>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="gender" id="inlineRadio8" value="Male" @if($customer->gender == 'Male') checked @endif>
                                      <label class="form-check-label" for="inlineRadio8">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="gender" id="inlineRadio9" value="Female" @if($customer->gender == 'Female') checked @endif>
                                      <label class="form-check-label" for="inlineRadio9">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="gender" id="inlineRadio10" value="Other" @if($customer->gender == 'Other') checked @endif>
                                      <label class="form-check-label" for="inlineRadio10">Other</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Address: <span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" readonly>{{$sale->cust_address}}</textarea>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="" class="form-label">State: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{$state->name}}" readonly>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                
                                <div class="col-md-3">
                                    <label for="" class="form-label">City: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{$city->name}}" readonly>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="" class="form-label">Pincode: <span class="text-danger">*</span></label>
                                    <input type="text" maxlength="7" class="form-control" value="{{$sale->pincode}}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Date of Birth: </label>
                                     
                                    <input type="text" class="form-control" value="{{$customer->dob}}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Date of Anniversary: </label>
                                    <input type="text" class="form-control" value="{{$customer->doa}}" readonly>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="" class="form-label">GST No: </label>
                                    <input type="text" class="form-control"  readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Company Name: </label>
                                    <input type="text" class="form-control"  readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Customer Notes: </label>
                                    <textarea type="text"  class="form-control" readonly></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row" style="background: aliceblue;">
                                <div class="form-group col-md-12">
                                    <table class="table datatables-basic w-100" id="saleTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 3%;">#</th>
                                                <th style="width: 14%;">Barcode</th>
                                                <th style="width: 14%;">Product</th>
                                                <th>Product Description</th>
                                                <th style="width: 6%;">Qty</th>
                                                @if($sale->tax_rule !='Not Applicable')
                                                <th style="width: 9%;" class="tax-col">HSN/SAC Code</th>
                                                <th style="width: 7%;" class="tax-col">GST %</th>
                                                @endif
                                                <th style="width: 13%;">Item Discount</th>
                                                <th style="width: 9%;">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            	@foreach($saleproduct as $product) 
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $product['barcode_use'] }}" readonly>
                                                </td>
                                                <td>
                                                   
                                                    <input type="text" class="form-control"  value="{{ $product['product_type'] }}">
                                                <td>
                                                    <input type="text" class="form-control"  value="{{ $product['product_deatils'] }}" readonly>
                                                    
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"  value="{{ $product['qty'] }}"  readonly>
                                                </td>
                                                @if($sale->tax_rule !='Not Applicable')
                                                <td class="tax-col">
                                                    <input type="text" class="form-control" value="{{ $product['hsn_code'] }}"  readonly>
                                                </td>
                                                <td class="tax-col">
                                                    <input type="text" class="form-control"  value="{{ $product['gst'] }}"  readonly>
                                                </td>
                                                @endif
                                                <td>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" value="{{ $product['discount_amt'] }}"   style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                                                        <input type="text" class="form-control" value="{{ $product['product_discount'] }}"   style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    
                                                    <input type="text" class="form-control" value="{{ $product['sale_price'] }}"  readonly>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="form-group col-md-12">
                                    <table class="table datatables-basic w-100">
                                          <tbody>
                                              <tr>
                                            <td colspan="10">
                                                <table class="table table-sm mb-0" width="100%">
                                                    <tr>
                                                        <!-- Left Quick Links -->
                                                        <td colspan="4" style="width: 70%;">
                                                            @if($sale->tax_rule !='Not Applicable')
                                                            <div class="row" id="totalbasicdiv">
                                                                <div class="col-md-4">
                                                                    <label for="">Total Basic Amount </label>
                                                                    <input type="text" class="form-control" value="{{$sale->total_basic_amount}}"  name="total_basic_amount" id="total_basic_amount" value="0.00" readonly>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Total GST Amount </label>
                                                                    <input type="text" class="form-control" value="{{$sale->total_gst_amount}}"  name="total_gst_amount" id="total_gst_amount" value="0.00" readonly>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            @endif
             
                                                            <!-- Payment Details -->
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h5>Payment Method <span class="text-danger">*</span></h5>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="d-flex">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="pay_method" id="pay_cash" value="cash" @if($sale->pay_method == 'cash') checked @endif>
                                                                            <label class="form-check-label" for="pay_cash">Cash</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="pay_method" id="pay_upi" value="upi" @if($sale->pay_method == 'upi') checked @endif>
                                                                            <label class="form-check-label" for="pay_upi">UPI</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="">Pay Amount <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" value="{{$sale->pay_amount}}" placeholder="Enter Amount" name="pay_amount" id="pay_amount" readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="">Pay Details </label>
                                                                        <input type="text" class="form-control" value="{{$sale->pay_deatils}}" placeholder="Enter Details" name="pay_deatils" id="pay_deatils" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label for="">Pending Amount <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control"  value="{{$sale->pending_amount}}" name="pending_amount" id="pending_amount" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label>Customer Has Taken External Warranty</label>
                                                                    <div class="d-flex">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="extrnal_warranty" id="extrnal_warranty" value="1"  @if($sale->extrnal_warranty == '1') checked @endif>
                                                                            <label class="form-check-label" for="extrnal_warranty">YES</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="extrnal_warranty" id="extrnal_warrantyn" value="0" @if($sale->extrnal_warranty == '0') checked @endif>
                                                                            <label class="form-check-label" for="extrnal_warrantyn">NO</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                
                                                        <!-- Right Summary -->
                                                        <td colspan="2">
                                                            <table class="table table-borderless table-sm mb-0 w-100">
                                                                <tr>
                                                                    <td class="text-end">Total item price : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_item_price}}"  name="total_item_price" id="total_item_price" value="0.00" readonly></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Total discount : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_discount}}" name="total_discount" id="total_discount" value="0.00" readonly></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Fitting Fee : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->fitting_fee}}" name="fitting_fee" id="fitting_fee" value="0.00"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Discount Coupon : Rs</td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm text-end" value="{{$sale->coupon_amount}}" name="coupon_amount" id="coupon_amount" value="0.00" readonly>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Cart Discount : Rs</td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm text-end" value="{{$sale->cart_discount}}" name="cart_discount" id="cart_discount" value="0.00" readonly>
                                                                        
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Loyalty Points  : Rs</td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm text-end" value="{{$sale->loyalty_point_amount}}" name="loyalty_point" id="loyalty_point" value="0.00" readonly>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Total payable : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_payable}}" name="total_payable" id="total_payable" value="0.00" readonly></td>
                                                                </tr>
                                                                	<tr>
                													<td class="text-end">Advance Amount</td>
                													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->pay_amount}}"  value="0.00" readonly></td>
                												</tr>
                												<tr>
                													<td class="text-end">Customer Credit</td>
                													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->customer_account}}"  value="0.00" readonly></td>
                												</tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                    </table>   
                                </div>    

                            </div>
                        </div>
                    </div>
                    <hr/>
                    <button type="button" id="confirmbtn" class="btn btn-primary loaderbtn">Confirm Order</button>
            </div>
        </div>    
    </div>
</section>

@include('sales.sale-modal')



@endsection

@section('scripts')
<script>
    $('#confirmbtn').on('click', function () {

    let sale_id = {{ $sale->sale_id }};

    if (!sale_id) return;

    // Disable button to prevent double click
    $('#confirmbtn').prop('disabled', true);

    $.ajax({
        type: "POST",
        url: "{{ route('admin.orderconfirm') }}",
        data: {
            sale_id: sale_id,
            _token: "{{ csrf_token() }}"
        },
        dataType: "json",

        success: function (response) {

            if (response.status === true) {

                $.toaster({
                    priority: "success",
                    title: "Success!",
                    message: response.msg,
                    timeout: 3000
                });

                setTimeout(function () {
                    window.location.href = "{{ route('admin.sale-history') }}";
                }, 1000);

            } else {

                $.toaster({
                    priority: "warning",
                    title: "Oops!",
                    message: response.msg,
                    timeout: 3000
                });
            }

            $('#confirmbtn').prop('disabled', false);
        },

        error: function () {

            $.toaster({
                priority: "danger",
                title: "Error!",
                message: "Failed to confirm order. Please try again.",
                timeout: 3000
            });

            $('#confirmbtn').prop('disabled', false);
        }
    });
});


</script>

@endsection

