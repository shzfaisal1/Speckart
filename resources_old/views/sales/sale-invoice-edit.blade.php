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

.col-md-4
{
    margin-bottom: 10px;
}

.col-md-6
{
    margin-bottom: 10px;
}

.col-md-12
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
  
  
 .tooltip {
    position: relative;
    display: inline-block;
}

.tooltip-text {
    visibility: hidden;
    background-color: #000;
    color: #fff;
    text-align: center;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;

    position: absolute;
    bottom: 120%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;

    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
} 

.quick-info {
    width: 10em;
    height: 9em;
    clear: both;
    box-shadow: 0 0 0px 0px #9FA0A0 inset, 0 2px 3px #4C4B4B;
    background-color: #f9f9f9;
    border: 1px solid #e3e3e3;
    background: -moz-linear-gradient(top, #f9f9f9 0%, #f9f9f9 47%, #f9f9f9 56%, #f9f9f9 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f9f9f9), color-stop(47%, #e3e3e3), color-stop(56%, #d7d7d7), color-stop(100%, #e8e8e8));
    background: -webkit-linear-gradient(top, #f9f9f9 0%, #e3e3e3 47%, #d7d7d7 56%, #e8e8e8 100%);
    background: -o-linear-gradient(top, #f9f9f9 0%, #e3e3e3 47%, #d7d7d7 56%, #e8e8e8 100%);
    background: -ms-linear-gradient(top, #f9f9f9 0%, #e3e3e3 47%, #d7d7d7 56%, #e8e8e8 100%);
    background: linear-gradient(to bottom, #f9f9f9 0%, #f9f9f9 47%, #f9f9f9 56%, #f9f9f9 100%);
    border-radius: 6px;
    -ms-border-radius: 6px;
    margin-bottom: 10px;
}


</style>
@endsection
@section('content')
@php
    $usr = Auth::guard()->user();
@endphp

@php
    // Unique products by product details to avoid duplicates
        $uniqueProducts = $saleproduct->unique(function ($item) {
            return $item['product_type'].'|'.
                   $item['product_code'].'|'.
                   $item['barcode_use'].'|'.
                   $item->base_price . '|' .
                   $item->discount_amt . '|' .
                   $item->qty . '|' .
                   $item->return_status . '|' .
                   $item->no_of_glass . '|' .
                   $item['product_deatils'];
        })->values();
    

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
                        <h3>Edit Order (ORDER NO : {{$sale->order_no}})</h3>
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
            <div id="salesdeatils"></div>
        </div>    
    </div>
</section>


<!--Edit Customer Modal -->
<div class="modal fade" data-backdrop="static" id="customer-edit-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="barcodeModalLabel">Edit Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <form id="customerForm" method="POST">
          @csrf  
          <div class="row">
            <div class="col-md-4">
                <label for="" class="form-label">Full Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" placeholder="Enter customer name" value="{{$sale->cust_name}}" id="cust_name" name="cust_name" >
                <span class="error badge text-danger" id="cust_nameError"></span>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" placeholder="Enter Contact no" value="{{$sale->contact_no}}" name="contact_no" id="contact_no"
                 maxlength="10"  pattern="^[6-9][0-9]{9}$">
                 <span class="error badge text-danger" id="contactError"></span>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Email Id: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" placeholder="Enter Email" value="{{$sale->email_id}}" name="email_id" id="email_id">
                <span class="error badge text-danger" id="email_idError"></span>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Customer Category: </label>
                <select class="form-control select" name="cust_category" id="cust_category" style="width:240px">
                    <option value="">Select Category</option>
                    <option value="EYE TEST" @if($customer->cust_category == 'EYE TEST') Selected @endif >EYE TEST</option>
                    <option value="GOLD MEMBERSHIP" @if($customer->cust_category == 'GOLD MEMBERSHIP') Selected @endif>GOLD MEMBERSHIP</option>
                    <option value="REPAIRING" @if($customer->cust_category == 'REPAIRING') Selected @endif>REPAIRING</option>
                    <option value="WALKOUT" @if($customer->cust_category == 'WALKOUT') Selected @endif>WALKOUT</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Gender : <span class="text-danger">*</span></label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="Male" @if($customer->gender == 'Male') checked @endif>
                  <label class="form-check-label" for="inlineRadio1">Male</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="Female" @if($customer->gender == 'Female') checked @endif>
                  <label class="form-check-label" for="inlineRadio2">Female</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gender" id="inlineRadio3" value="Other" @if($customer->gender == 'Other') checked @endif>
                  <label class="form-check-label" for="inlineRadio3">Other</label>
                </div>
                <span class="error badge text-danger" id="genderError"></span>
            </div>
            <div class="col-md-12">
                <label for="" class="form-label">Address: </label>
                <input type="text" class="form-control" placeholder="Enter address" name="cust_address" id="cust_address"   value="{{$sale->cust_address}}">
            </div>
            
            <div class="col-md-4">
                <label for="" class="form-label">State: </label>
                <select class="form-control select" name="state_id" id="state_id" style="width:240px">
                    <option value="" disabled selected>Select State</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">City: </label>
                <select class="form-control select" name="city_id" id="city_id" style="width:240px">
                    <option value="" disabled selected>Select City</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Pincode: </label>
                <input type="text" maxlength="7" class="form-control" placeholder="Enter Pincode" name="pincode" id="pincode" value="{{$sale->pincode}}">
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Date of Birth: </label>
                <input type="date" class="form-control" id="dob" name="dob" value="{{$customer->dob}}">
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Date of Anniversary: </label>
                <input type="date" class="form-control" id="doa" name="doa" value="{{$customer->doa}}">
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Company Name: </label>
                <input type="text"  class="form-control" placeholder="Company Name" name="company_name" id="company_name" value="{{$customer->company_name}}">
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Customer GST Number: </label>
                <input type="text"  class="form-control" placeholder="Customer GST Number" name="gst_no" id="gst_no" value="{{$customer->gst_no}}">
            </div>
            <div class="col-md-12">
                <label for="" class="form-label">Customer Notes: </label>
                <input type="text"  class="form-control" placeholder="Customer Notes" name="cust_note" id="cust_note" value="{{$customer->cust_note}}">
            </div> 
          </div>
            <hr/>
            <button type="submit" class="btn btn-primary loaderbtn">Submit</button>
        </form> 
      </div>
      
    </div>
  </div>
</div>

<!--Edit Order Details Modal -->
<div class="modal fade" data-backdrop="static" id="order-details-edit-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="barcodeModalLabel">Edit Order Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
       <form id="orderForm" method="POST">
          @csrf  
          <div class="row">
            <div class="col-md-6">
                <label for="" class="form-label">Order No: </label>
                <input type="text" class="form-control"  value="{{$sale->order_no}}"  readonly>
            </div>
            <div class="col-md-6">
                <label for="" class="form-label">Order Date: </label>
                <input type="text" class="form-control"  value="{{$sale->sale_date}}" readonly>
            </div>
            <div class="col-md-6">
                <label for="" class="form-label">Delivery Date: <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control"  value="{{$sale->delivery_date}}"  name="delivery_date" id="delivery_date">
            </div>
            <div class="col-md-6">
                <label for="" class="form-label">Sales Person: </label>
                <select class="form-control select" style="height: 32px !important;" id="sale_person" name="sale_person">
                    <option value="">Select Person</option>
                  <?php  $tbl_users =  DB::table("users")->where('status',1)->get();  ?>
                   @foreach($tbl_users as $tbl_users)
                    <option value="{{$tbl_users->id}}" @if($sale->sale_person == $tbl_users->id) selected @endif>{{$tbl_users->name}} / ({{$tbl_users->user_type}} : {{$tbl_users->staff_id}})</option>
                  @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Customer Has Taken External Warranty</label>
                <div class="d-flex">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="extrnal_warranty" id="extrnal_warranty" value="1" @if($sale->extrnal_warranty == 1) checked @endif>
                        <label class="form-check-label" for="extrnal_warranty">YES</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="extrnal_warranty" id="extrnal_warrantyn" value="0" @if($sale->extrnal_warranty == 0) checked @endif>
                        <label class="form-check-label" for="extrnal_warrantyn">NO</label>
                    </div>
                </div>
            </div>

          </div>
            <hr/>
            <button type="submit" class="btn btn-primary loaderbtn">Submit</button>
        </form> 
      </div>
      
    </div>
  </div>
</div>


<!--Remove Item Modal -->
<div class="modal fade" data-backdrop="static" id="edit-remove-item-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="barcodeModalLabel">Remove Items</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
       <form id="itemsForm" method="POST">
          @csrf  
            <div class="row">
                <div class="col-md-12">
                    <strong>Important Note:</strong>
                    <div style="color: red; font-weight: bold; font-size: 14px;">
                        <ol style="margin: 0;">
                            <li>You cannot delete all items from an order; at least one item must remain. If you wish to remove the entire order, please delete it directly from the Pending Orders section.</li>
                            <li>When an item is removed from the order, any applied Cart Discounts, Loyalty Points, or Discount Coupons will be automatically removed. You will need to reapply them if necessary</li>
                        </ol>
                    </div>    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
        			<div class="row" style="background: aliceblue;">
        				<div class="form-group col-md-12">
        					<table class="table datatables-basic w-100" id="saleitemTable">
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
        								
        					    	 @if($product['return_status'] == 0)		
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
        								
        								@php
                                            $multiplier = ($product['product_type'] === 'Glass') ? $product['qty'] : 1;
                                        
                                            $discountAmt     = $product['discount_amt'] * $multiplier;
                                            $productDiscount = $product['product_discount'] * $multiplier;
                                            $salePrice       = $product['sale_price'] * $multiplier;
                                            $basePrice       = $product['base_price'] * $multiplier;
                                            $gstAmount       = $product['gst_amount'] * $multiplier;
                                        @endphp
        								<td>
                                            <div class="input-group mb-3">
                                                <input type="text"
                                                       class="form-control"
                                                       value="{{ $discountAmt }}"
                                                       style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;"
                                                       readonly>
                                        
                                                <input type="text"
                                                       class="form-control"
                                                       value="{{ $productDiscount }}"
                                                       style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;"
                                                       readonly>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <input type="text"
                                                   class="form-control"
                                                   value="{{ $salePrice }}"
                                                   readonly>
                                        </td>
                                        
                                        <td>
                                            <input type="checkbox"
                                                   class="remove-item-checkbox"
                                                   name="remove_items[]"
                                                   value="{{ $product['id'] }}"
                                                   data-price="{{ $salePrice }}"
                                                   data-discount="{{ $discountAmt }}"
                                                   data-basic="{{ $basePrice }}"
                                                   data-gst="{{ $gstAmount }}"
                                                   data-qty="{{ $product['qty'] }}"
                                                   style="width:20px;height:20px;cursor:pointer;">
                                        </td>
        							</tr>
        							@endif
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
        							
        										</td>
        				
        										<!-- Right Summary -->
        										<td colspan="2">
        											<table class="table table-borderless table-sm mb-0 w-100">
        												<tr>
        													<td class="text-end">Total item price : Rs</td>
        													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_item_price}}"  name="total_item_price" id="total_item_price" readonly></td>
        												</tr>
        												<tr>
        													<td class="text-end">Total discount : Rs</td>
        													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_discount}}" name="total_discount" id="total_discount" readonly></td>
        												</tr>
        												<tr>
        													<td class="text-end">Fitting Fee : Rs</td>
        													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->fitting_fee}}" name="fitting_fee" id="fitting_fee" ></td>
        												</tr>
        												<tr>
        													<td class="text-end">Discount Coupon : Rs</td>
        													<td>
        														<input type="text" class="form-control form-control-sm text-end" value="{{$sale->coupon_amount}}" name="coupon_amount" id="coupon_amount"  readonly>
        													    <input type="hidden" class="form-control form-control-sm text-end" name="coupon_id" id="coupon_id" value="{{$sale->coupon_id}}"readonly>
        													</td>
        												</tr>
        												<tr>
        													<td class="text-end">Cart Discount : Rs</td>
        													<td>
        														<input type="text" class="form-control form-control-sm text-end" value="{{$sale->cart_discount}}" name="cart_discount" id="cart_discount" readonly>
        														<input type="hidden" class="form-control form-control-sm text-end" value="{{$sale->cart_discount_by}}" name="cart_discount_by" id="cart_discount_by"  readonly>
                                                                <input type="hidden" class="form-control form-control-sm text-end" value="{{$sale->cart_discount_per}}" name="cart_discount_per" id="cart_discount_per"  readonly>
                                                                <input type="hidden" class="form-control form-control-sm text-end" value="{{$sale->cart_discount_resion}}" name="cart_discount_resion" id="cart_discount_resion" vreadonly>
        													    
        													</td>
        												</tr>
        												<tr>
        													<td class="text-end">Loyalty Points  : Rs</td>
        													<td>
        														<input type="text" class="form-control form-control-sm text-end" value="{{$sale->loyalty_point_amount}}" name="loyalty_point" id="loyalty_point"  readonly>
        														<input type="hidden" class="form-control form-control-sm text-end" value="{{$sale->loyalty_point_apply}}" name="loyalty_point_apply" id="loyalty_point_apply"  readonly>
        													</td>
        												</tr>
        												<tr>
        													<td class="text-end">Round Off : Rs  (+/-)</td>
        													<td>
        														<input type="text" class="form-control form-control-sm text-end" value="{{$sale->roundoff}}" name="round_off" id="round_off"   value="0.00" readonly>
        													</td>
        												</tr>
        												<tr>
        													<td class="text-end">Total payable : Rs</td>
        													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_payable}}" name="total_payable" id="total_payable" readonly></td>
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
          
            <button type="submit" class="btn btn-primary loaderbtn">Submit</button>
        </form> 
      </div>
      
    </div>
  </div>
</div>


<!--Round Off Value Modal -->
<div class="modal fade" data-backdrop="static" id="round-off-amount-edit-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="barcodeModalLabel">Edit Round Off Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
       <form id="roundoffForm" method="POST">
          @csrf 
               <div class="row">
                   <div class="col-md-6">
                       <label class="label-control">Old Payable Amount : &nbsp;</label>
                        <input type="text" name="old_total_payable" id="old_total_payable" value="{{$sale->total_payable}}" class="form-control" readonly="">
                   </div>
               </div>
               <div class="row">
                   <div class="col-md-6">
                    <label class="label-control"><span class="round-off-label">Round Off</span> : &nbsp;(+/-)</label>
                    <input type="text" name="roundOffAmount" id="roundOffAmount" value="" class="form-control">
                    <span class="error badge text-danger" id="roundOffAmountError"></span>
                    </div>
               </div>
               <div class="row">
                   <div class="col-md-6">
                   <label class="label-control">Updated Payable Amount. : &nbsp;</label>
                   <input type="text" name="newtotalpayable" id="newtotalpayable" value="" class="form-control"  readonly="">
                   </div>
               </div>
                
                
            <hr/>
            <button type="submit" class="btn btn-primary loaderbtn">Submit</button>
        </form> 
      </div>
      
    </div>
  </div>
</div>


<!--New payment Modal -->
<div class="modal fade" data-backdrop="static" id="edit-new-payment-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="barcodeModalLabel">Add Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
       <form id="newpaymentForm" method="POST">
           <div class="row">
               <div class="col-md-4">
                   <strong>Order No : </strong>{{$sale->order_no}}
               </div>
           </div>
           <div class="row">
               <div class="col-md-4">
                   <strong>Payable Amount : </strong>{{$sale->total_payable}}
               </div>
               <div class="col-md-4">
                   <strong>Advance Paid : </strong>{{$sale->pay_amount}}
               </div>
               <div class="col-md-4">
                   <strong>Return Payment : </strong>{{$sale->return_amount}}
               </div>
           </div>
           <div class="row">
               <div class="col-md-4">
                   <strong>Balance Amount : </strong>{{$sale->pending_amount}}
               </div>
               <div class="col-md-4">
                   <strong>Customer Credit : </strong>{{$sale->credit_amount}}
               </div>
               <div class="col-md-4">
                   <strong>Return Amount  : </strong>{{$sale->return_pay_amount}}
               </div>
           </div>
          @csrf 
          @if($sale->pending_amount > 0)
               <div class="row">
					<div class="col-md-12">
						<h5>Payment Method <span class="text-danger">*</span></h5>
					</div>
					<div class="col-md-12">
						<div class="d-flex">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="paymethod" id="paycash" value="cash" checked>
								<label class="form-check-label" for="paycash">Cash</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="paymethod" id="payupi" value="upi" >
								<label class="form-check-label" for="payupi">UPI</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<label for="">Pay Amount <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="payamount" id="payamount"  placeholder="Enter Amount">
						</div>
						<div class="col-md-6">
							<label for="">Pay Details </label>
							<input type="text" class="form-control" name="paydetails" id="paydetails"   placeholder="Enter Details" >
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<label for="">Pending Amount <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="pendingamount" id="pendingamount"    readonly>
						</div>
					</div>
				</div>
                
                
            <hr/>
            <button type="submit" class="btn btn-primary loaderbtn">Submit</button>
        @endif   
        @if($sale->pending_amount <= 0)
          <div class="row">
                <div class="col-md-12">
                    <strong>Note :</strong>
                        <ol style="margin: 0;">
                            <li>Already Full Paid.You can not add new payment.</li>
                        </ol>
                </div>
            </div>
        @endif 
        </form> 
      </div>
      
    </div>
  </div>
</div>


<!--Add New Item Modal -->
<div class="modal fade" data-backdrop="static" id="add-new-item-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="barcodeModalLabel">Add New Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
       <form id="newitemsForm" method="POST">
          @csrf  
              <div class="row">
                <div class="col-md-2">
                    <label for="" class="form-label">Product TYpe: </label>
                    <select class="form-control product-type" style="height: 32px !important;" name="producttype" id="producttype">
                        <option value="">Select Product</option>
                        <option value="Frame">Frame</option>
                        <option value="Goggles">Goggles</option>
                        <option value="Glass">Glass</option>
                        <option value="Lens">Contact Lens</option>
                        <option value="Solution">Solution</option>
                        <option value="Repair">Repair</option>
                        <option value="Other">Other</option>
                    </select>
                </div> 
                
                <div class="col-md-2">
                    <label for="" class="form-label">Barcode: </label>
                    <input type="text" class="form-control barcode" id="barcode_u" name="barcode" placeholder="Enter barcode">
                </div>
                  
              </div>
              <div class="row" id="branddiv" style="display:none">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="brand_type" value="1" checked>
                              <label class="form-check-label">Other Brand</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="brand_type" value="0">
                              <label class="form-check-label">In House Brand</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="inhousepackage" style="display:none">
                    <div class="row" style="margin-top: 10px;">
                         <div class="col-md-6">
                         <label for="">Select Lens <span class="text-danger">*</span></label>
                             <div class="d-flex">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="lenstype" id="inlineRadio4" value="Single Vision">
                                  <label class="form-check-label" for="inlineRadio4">Single Vision</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="lenstype" id="inlineRadio5" value="Bifocal/Progressive">
                                  <label class="form-check-label" for="inlineRadio5">Bifocal/Progressive</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="lenstype" id="inlineRadio6" value="Zero Power">
                                  <label class="form-check-label" for="inlineRadio6">Zero Power</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="lenstype" id="inlineRadio7" value="Reading Power">
                                  <label class="form-check-label" for="inlineRadio7">Reading Power</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="lenspackage"></div>
                    <div id="glasscoating"></div>
                </div>
                <div class="row" style="margin-top:10px">
                    <div class="col-md-2" id="codediv" style="display:none">
                        <label>Product Code</label>
                        <input type="text" id="modal_product_code" name="modal_product_code" class="form-control" readonly>
                         <div class="suggestion-box list-group" style="display:none; position:absolute; z-index:1000;"></div>
                    </div>
                    <div class="col-md-2" id="iddiv" style="display:none">
                        <label>Product ID</label>
                        <input type="text" id="modal_product_id" name="modal_product_id" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="companydiv" style="display:none">
                        <label>Company </label>
                        <input type="text" id="modal_company" name="modal_company" class="form-control" readonly>
                    </div>
  
                    <div class="col-md-2" id="qualitydiv" style="display:none">
                        <label>Quality </label>
                        <input type="text" id="modal_quality" name="modal_quality" class="form-control" readonly>
                    </div>
                     <div class="col-md-4" id="pdetailsdiv"  style="display:none">
                        <label>Product Detail</label>
                        <textarea type="text" id="modal_product_details" name="modal_product_details"  class="form-control" readonly></textarea>
                    </div>
     
                    <div class="col-md-2" id="materialediv" style="display:none">
                        <label>Material </label>
                        <input type="text" id="modal_product_material" name="modal_product_material" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="colordiv" style="display:none">
                        <label>Color</label>
                        <input type="text" id="modal_product_color" name="modal_product_color" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="designdiv" style="display:none">
                        <label>Design </label>
                        <input type="text" id="modal_product_design" name="modal_product_design"  class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="coatingdiv" style="display:none">
                        <label>Coating </label>
                        <input type="text" id="modal_product_coating" name="modal_product_coating" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="indexdiv" style="display:none">
                        <label>Index  </label>
                        <input type="text" id="modal_product_index"  name="modal_product_index" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="numberdiv" style="display:none">
                        <label>Number   </label>
                        <input type="text" id="modal_product_number" name="modal_product_number" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="ctdiv" style="display:none">
                        <label>CT (Center Thickness)  </label>
                        <input type="text" id="modal_product_ct" name="modal_product_ct" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="typediv" style="display:none">
                        <label>Type   </label>
                        <input type="text" id="modal_product_typesss" name="modal_product_typesss" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="validitydiv" style="display:none">
                        <label>Validity In Days   </label>
                        <input type="text" id="modal_product_validity" name="modal_product_validity" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="shapediv" style="display:none">
                        <label>Shape   </label>
                        <input type="text" id="modal_product_shape" name="modal_product_shape" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="sizediv" style="display:none">
                        <label>Size   </label>
                        <input type="text" id="modal_product_size" name="modal_product_size" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="variantdiv" style="display:none">
                        <label>Variant   </label>
                        <input type="text" id="modal_product_variant" name="modal_product_variant" class="form-control" readonly>
                    </div>
                     <div class="col-md-2" id="packingtypediv" style="display:none">
                        <label>Packing Type   </label>
                        <input type="text" id="modal_packing_type" name="modal_packing_type" class="form-control" readonly>
                    </div>
                </div>
                <!----- GLASS------------------------------->
                <div class="row" id="Prescriptionglassdiv" style="display:none">
                    <!-- Single Vision -->
                    <div class="col-md-6">
                        <h5>Eyewear Prescription</h5>
                        <div class="table-responsive">
                            <table align="left" id="gl-eyewear-powers" style="float: none; font-size: 13px;">
                        	<tbody><tr>
                        		<td>
                        			<table align="left" id="gl-eyewear-right">
                        				<tbody><tr>
                        					<td></td>
                        					<td colspan="6" align="center" style="font-size: 15px; font-weight: bold; text-decoration: underline;">RIGHT EYE (OD)
                        					<i class="fa fa-clone fa-solid copy-right-to-left" 
                                               style="margin-left: 15px;font-weight: bolder;font-size: large; cursor: pointer; color:#ff7200;" 
                                               title="Copy To Left"></i>
                        					</td>
                        				</tr>
                        				<tr>
                        					<td></td>
                        					<td style="text-align:center">R-SPH</td>
                        					<td style="text-align:center">R-CYL</td>
                        					<td style="text-align:center">R-AXIS</td>
                        					<td style="text-align:center"><span class="mandatory">*</span>R-PD</td>
                        					<td style="text-align:center">R-VA</td>
                        					<td style="text-align: center; display: none;" class="hide-prism">R-PRISM</td>
                        				</tr>
                        				<tr>
                        					<td>Distance Vision (DV)</td>
                        					<td>
                        						<input type="text" name="GL_EYE_RS_D" id="GL_EYE_RS_D" class="search_input_function" style="width:45px;"  autocomplete="off">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_RC_D" id="GL_EYE_RC_D" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_RA_D" id="GL_EYE_RA_D" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_RP_D" id="GL_EYE_RP_D" class="search_input_function"    style="width:45px;">
                        					</td>
                        					<td style="width:45px;"><input type="text" name="GL_EYE_RV_D" id="GL_EYE_RV_D" class="search_input_function"   style="width:45px;"></td>
                        					<td style="width: 45px; display: none;" class="hide-prism"><input type="text" name="GL_EYE_RPRISM_D" id="GL_EYE_RPRISM_D" class="search_input_function"  style="width:45px;"></td>
                        				</tr>
                        				<tr id="nearvisionright">
                        					<td>Near Vision (NV)</td>
                        					<td>
                        						<input type="text" name="GL_EYE_RS_N" id="GL_EYE_RS_N" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td><input type="text" name="GL_EYE_RC_N" class="search_input_function" id="GL_EYE_RC_N" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_RA_N" class="search_input_function" id="GL_EYE_RA_N" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_RP_N" class="search_input_function" id="GL_EYE_RP_N" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_RV_N" class="search_input_function" id="GL_EYE_RV_N"  style="width:45px;"></td>
                        					<td class="hide-prism" style="display: none;"><input type="text" class="search_input_function" name="GL_EYE_RPRISM_N" id="GL_EYE_RPRISM_N" style="width:45px;" ></td>
                        				</tr>
                        				<tr id="addright">
                        					<td>Addition (ADD)</td>
                        					<td>
                        						<input type="text" name="GL_EYE_RADD" id="GL_EYE_RADD" class="search_input_function" style="width:45px;">
                        					</td>
                        				</tr>
                        				<tr class="hide-total-pd" id="pdright">
                        					<td>IPD (Total PD)</td>
                        					<td>
                        						<input type="text" name="GL_EYE_totalPD" class="search_input_function" id="GL_EYE_totalPD"  style="width:45px;">
                        					</td>
                        				</tr>
                        			</tbody></table>
                        		</td>
                        		<td style="vertical-align: top;">
                        			<table align="left" id="gl-eyewear-left">
                        				<tbody><tr>
                        					<td colspan="6" align="center" style="font-size: 15px; font-weight: bold; text-decoration: underline;">
                        					    <i class="fa fa-clone copy-left-to-right"
                                                   style="margin-right: 15px;font-weight: bolder;font-size: large; cursor: pointer; color:#ff7200;"
                                                   title="Copy To Right"></i>LEFT EYE (OS)
                        					   </td>
                        				</tr>
                        				<tr>
                        					<td style="text-align:center">L-SPH</td>
                        					<td style="text-align:center">L-CYL</td>
                        					<td style="text-align:center">L-AXIS</td>
                        					<td style="text-align:center"><span class="mandatory">*</span>L-PD</td>
                        					<td style="text-align:center">L-VA</td>
                        					<td style="text-align: center; display: none;" class="hide-prism">L-PRISM</td>
                        				</tr>
                        				<tr>
                        					<td>
                        						<input type="text" name="GL_EYE_LS_D" id="GL_EYE_LS_D" class="search_input_function" style="width:45px;" >
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_LC_D" id="GL_EYE_LC_D" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_LA_D" id="GL_EYE_LA_D" class="search_input_function"  style="width:45px;">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_LP_D" id="GL_EYE_LP_D" class="search_input_function" style="width:45px;" >
                        					</td>
                        					<td style="width:45px;"><input type="text" name="GL_EYE_LV_D" class="search_input_function"  id="GL_EYE_LV_D" style="width:45px;" ></td>
                        					<td style="width: 45px; display: none;" class="hide-prism"><input type="text" class="search_input_function" name="GL_EYE_LPRISM_D" id="GL_EYE_LPRISM_D" value="" style="width:45px;" ></td>
                        				</tr>
                        				<tr id="nearvisionleft">
                        					<td>
                        						<input type="text" name="GL_EYE_LS_N" id="GL_EYE_LS_N" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td><input type="text" name="GL_EYE_LC_N" id="GL_EYE_LC_N" class="search_input_function" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_LA_N" id="GL_EYE_LA_N" class="search_input_function" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_LP_N" id="GL_EYE_LP_N" class="search_input_function" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_LV_N" id="GL_EYE_LV_N" class="search_input_function" style="width:45px;"></td>
                        					<td class="hide-prism" style="display: none;"><input type="text" name="GL_EYE_LPRISM_N" class="search_input_function" id="GL_EYE_LPRISM_N" style="width:45px;"></td>
                        				</tr>
                        				<tr id="addleft">
                        					<td>
                        						<input type="text" name="GL_EYE_LADD" id="GL_EYE_LADD" class="search_input_function" value="" style="width:45px;">
                        					</td>
                        				</tr>
                        			</tbody></table>
                        		</td>
                        	</tr>
                        </tbody></table>
                            
                        </div>
                    </div>
        
                    <!-- Bifocal/Progressive -->
                    <div class="col-md-6" id="wparameter">
                        <div>
                            <h5>Wearing Parameters</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="frametypeglass" id="inlineRadio88" value="Full frame" checked>
                                      <label class="form-check-label" for="inlineRadio88">Full frame</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="frametypeglass" id="inlineRadio99" value="Half frame">
                                      <label class="form-check-label" for="inlineRadio99">Half frame</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="frametypeglass" id="inlineRadio100" value="Rimless  frame">
                                      <label class="form-check-label" for="inlineRadio100">Rimless frame</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-3">
                                    <label>Fitting Height </label>
                                    <input type="text" id="modal_FH" name="modal_FH" placeholder="Enter  Height " class="form-control">
                                </div>
                                <div class="col-md-3" id="framesizea" style="display:none">
                                    <label>A Size </label>
                                    <input type="text" id="modal_asize" name="modal_asize" placeholder="Enter  A Size " class="form-control">
                                </div>
                                 <div class="col-md-3" id="framesizeb" style="display:none">
                                    <label>B Size </label>
                                    <input type="text" id="modal_bsize" name="modal_bsize"  placeholder="Enter  B Size " class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>DBL</label>
                                    <input type="text" id="modal_dbl" name="modal_dbl" placeholder="Enter DBL" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>ED</label>
                                    <input type="text" id="modal_ED" name="modal_ED" placeholder="Enter ED" class="form-control">
                                </div>
                            </div>   
                        </div>
                        <br>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="modal_rightleft[]" value="Right" checked>
                                <label class="form-check-label">Right</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="modal_rightleft[]"  value="Left" checked>
                                <label class="form-check-label">Left</label>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6" style="margin-top:20px">
                        <div>
                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-primary" type="button" id="PrescriptionBtn"> Select Prescription</button>
                                <button class="btn btn-primary" type="button" id="clearPrescriptionBtn"> Clear Prescription</button>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <div>
                            <label>Patient  Name</label>
                            <input type="text" id="modal_patient_name" name="modal_patient_name" placeholder="Enter Patient Name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                         <div>
                            <label>Doctor / Optometrist Name</label>
                            <input type="text" id="modal_doctor_name" name="modal_doctor_name" placeholder="Enter Doctor Name" class="form-control">
                        </div>
                    </div> 
                    
                     <div class="row" id="SelectBox" style="margin-top:10px">
                         <!-- Right -->
                        <div class="col-md-6 lens-box">
                            <div class="lens-row">
                                <span>Right :</span>
                                <button type="button" class="button checkLensFromBarcode" id="checkRightLensFromBarcode">Select Inventory</button>
                    
                                <span>No of Boxes :</span>
                                <input type="text" class="small-input" id="lensRightNoOfBoxes">
                    
                                <span>Total Pieces :</span>
                                <input type="text" class="small-input" id="lensRightTotalPieces">
                            </div>

                        </div>
                    
                        <!-- Left -->
                        <div class="col-md-6 lens-box">
                            <div class="lens-row">
                                <span>Left :</span>
                                <button type="button" class="button checkLensFromBarcode" id="checkleftLensFromBarcode">Select Inventory</button>
                    
                                <span>No of Boxes :</span>
                                <input type="text" class="small-input" id="lensLeftNoOfBoxes">
                    
                                <span>Total Pieces :</span>
                                <input type="text" class="small-input" id="lensLeftTotalPieces">
                            </div>
       
                        </div>
                        <input type="hidden" id="lensSide">
                        <input type="hidden" id="modal_lens_bids">
                    </div>

                    
                        
                    <div class="col-md-12">
                        <div>
                            <div class="mb-1">Lens Type:</div>
                            @foreach(['Constant Use', 'Reading Wear', 'Distance Wear', 'Single Vision', 'Progressive', 'Bifocal', 'Trifocal'] as $type)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="modal_glassWearingType" name="glassWearingType[]" value="{{ $type }}">
                                    <label class="form-check-label">{{ $type }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4">
                            <div class="mb-1">Prescription Notes:</div>
                            <input class="form-control" type="text" id="modal_prescription_notes" name="modal_prescription_notes">
                    </div>
                    
                    <div class="col-md-4" id="counteye">
                            <div class="mb-1">Count In Eye Testing Records ?:</div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="count_eye_test" id="inlineRadio1111" value="1" checked>
                              <label class="form-check-label" for="inlineRadio1111">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="count_eye_test" id="inlineRadio9999" value="0">
                              <label class="form-check-label" for="inlineRadio9999">No</label>
                            </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-2" id="qtydiv" style="display:none">
                        <label>Quantity </label>
                        <input type="text" id="modal_quantity" name="modal_quantity" value="1" class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="hsncodediv" style="display:none">
                        <label>HSN/SAC Code </label>
                        <input type="text" id="modal_hsncode" name="modal_hsncode"   class="form-control">
                    </div>
                    <div class="col-md-2" id="gstdiv" style="display:none">
                        <label>GST % </label>
                        <input type="number" id="modal_gst" name="modal_gst"   class="form-control">
                    </div>
                    <div class="col-md-2" id="taxdiv" style="display:none">
                        <label>Tax Rule</label>
                        <input type="text" id="modal_tax_rule" name="modal_tax_rule"   class="form-control" readonly>
                    </div>

                
                    <div class="col-md-2" id="purchasediv" style="display:none">
                        <label>Purchase Price </label>
                        <input type="text" id="modal_purchase_price" name="modal_purchase_price"  class="form-control">
                    </div>
                     <div class="col-md-2" id="retailspricediv" style="display:none">
                        <label>Retail Price </label>
                        <input type="text" id="modal_retail_price" name="modal_retail_price"  class="form-control">
                        <span class="error badge text-danger" id="modal_retail_priceError"></span>
                    </div>
                    <div class="col-md-2" id="discountdiv" style="display:none">
                        <label>Discount  </label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="modal_discount" name="modal_discount" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                            <input type="text" class="form-control" id="modal_discount_amount" name="modal_discount_amount" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                        </div>
                    </div>
                    <div class="col-md-2" id="basediv" style="display:none">
                        <label>Base Price </label>
                        <input type="text" id="modal_base_price" name="modal_base_price"  class="form-control" readonly>
                    </div>
   
                    <div class="col-md-2" id="gstamtdiv" style="display:none">
                        <label>GST Amount </label>
                        <input type="text" id="modal_gst_amount" name="modal_gst_amount"  class="form-control" readonly>
                    </div>
                    <div class="col-md-2" id="totalsalediv" style="display:none">
                        <label>Total Sales Price </label>
                        <input type="text" id="modal_total_sale" name="modal_total_sale"  class="form-control" readonly>
                    </div>
                </div>
                <br>

            <button type="submit" class="btn btn-primary loaderbtn">Submit</button>
        </form> 
      </div>
      
    </div>
  </div>
</div>


	  
	  <div class="modal fade" id="LensBarcodeModal" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog lg" role="document">
        <div class="modal-content" >
            <div class="modal-header" style="background: cornsilk;">
                <h5 class="modal-title" id="modalTitle">Contact Lens Inventory List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="LensBarcodeTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Barcode</th>
                      <th>Product Code</th>
                      <th>Description</th>
                      <th>Available Pieces</th>
                      <th>Purchase Price</th>
                      <th>Sales Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td colspan="7" class="text-center">Loading...</td></tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: cornsilk;">
            <button type="button" class="btn btn-secondary" id="selectLensBarcode">Select</button>    
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
    </div>
</div> 

@endsection

@section('scripts')
<script>
$(function(){
   'use strict'
   
   get_orderdetails();
});

function get_orderdetails()
{
     var sale_id  =  {{$sale->sale_id}};
     $.ajax({
	   url: "{{ route('admin.loadorderdetails') }}",
       type: "GET",
       data: { sale_id: sale_id },
	   dataType: "json",
	   success: function (success)  
	   {
		    var main_data=success.order_section;
		    $('#salesdeatils').empty();
		    if (success.status === 'success') 
            {
                $('#salesdeatils').show();
                $('#salesdeatils').append(main_data);
            }
            else
            {
                get_orderdetails();
            }
    	}
   });    
}

$(document).ready(function() {
    $.ajax({
        url: "{{ route('get-state') }}",
        method: "GET",
        success: function(data) {
            var serviceDropdown = $('#state_id');
            serviceDropdown.empty(); 
            serviceDropdown.append('<option value="" disabled selected>Select State</option>');

            data.forEach(function(state) {
                serviceDropdown.append('<option value="' + state.id + '">' + state
                    .name + '</option>');
            });
        },
        error: function(error) {
            console.error('Error fetching state:', error);
        }
    });
    
    
    $('#state_id').on('change', function() {
        const stateId = $(this).val();
        $('#city_id').empty().append('<option value="" disabled selected>Loading...</option>');

        if (stateId) {
            $.ajax({
                url: "{{ route('get-city-by-state') }}",
                type: "GET",
                data: {
                    state_id: stateId
                },
                success: function(data) {
                    $('#city_id').empty().append(
                        '<option value="" disabled selected>Select City</option>');
                    data.forEach(city => {
                        $('#city_id').append(
                            `<option value="${city.id}">${city.name}</option>`
                        );
                    });
                },
                error: function() {
                    $('#city_id').empty().append(
                        '<option value="" disabled selected>No city found</option>');
                }
            });
        }
    });
    
    $('.select').select2({
      allowClear: true
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var fields = ['contact_no', 'bb_mobile_no'];
    var pattern = /^[6-9][0-9]{0,9}$/; // Allows 1â€“10 digits, starting with 6â€“9

    fields.forEach(function (fieldId) {
        var input = document.getElementById(fieldId);
        if (!input) return;

        var lastValidValue = '';

        input.addEventListener('input', function () {
            var currentValue = this.value;
            if (pattern.test(currentValue)) {
                lastValidValue = currentValue;
            } else {
                this.value = lastValidValue;
            }
        });
    });
});

</script>

<script>
    $(document).on("click", ".payment-delete", function (e) {
        e.preventDefault();
    

        let id = $(this).data('id');
        let row = $(this).closest('tr');
    
        Swal.fire({
            title: "Are you sure ?",
            text: "You would not be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ml-2'
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/saleseditpaymentdelete') }}/" + id + "/destroy",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function () {
                        $("#ajaxLoader").show();
                    },
                    success: function (data) {
                        get_orderdetails();
                        showResponseMessage(data);
                    },
                    complete: function () {
                        $("#ajaxLoader").fadeOut();
                    }
                });
            }
        });
    });
    
    
    $(document).on("click", ".payment-return-delete", function (e) {
        e.preventDefault();
    

        let id = $(this).data('id');
        let row = $(this).closest('tr');
    
        Swal.fire({
            title: "Are you sure ?",
            text: "You would not be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ml-2'
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/saleseditpaymentreturndelete') }}/" + id + "/destroy",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function () {
                        $("#ajaxLoader").show();
                    },
                    success: function (data) {
                        get_orderdetails();
                        showResponseMessage(data);
                    },
                    complete: function () {
                        $("#ajaxLoader").fadeOut();
                    }
                });
            }
        });
    });

    


    $("#customerForm").submit(function(e) 
    {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let cust_name = document.getElementById("cust_name" + class_name).value.trim();
        let contact_no = document.getElementById("contact_no" + class_name).value.trim();
        let email_id = document.getElementById("email_id" + class_name).value.trim();

        if (cust_name === "") {
            document.getElementById("cust_nameError" + class_name).textContent = "Customer Name Required.";
            document.getElementById("cust_name" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (!/^\d{10}$/.test(contact_no)) {
            document.getElementById("contactError" + class_name).textContent = "Contact must be a 10-digit number.";
            document.getElementById("contact_no" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (!/^\S+@\S+\.\S+$/.test(email_id)) {
            document.getElementById("email_idError" + class_name).textContent = "Please enter a valid email.";
            document.getElementById("email_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    

        
        let order_no = $("#order_no").val();
        let form = $("#customerForm")[0];
        let data = new FormData(form);
        data.append('order_no', order_no);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.sales-customer-update') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function(response) {
                if ($.isEmptyObject(response.error)) 
                {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    $('#customer-edit-modal').modal('hide');
                    get_orderdetails();
                }
                else 
                {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                    $.each(response.error, function(index, value) {
                        
                    });
                }
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
    });
    
    
    $("#orderForm").submit(function(e) 
    {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let sale_person = document.getElementById("sale_person" + class_name).value.trim();


        if (sale_person === "") {
            document.getElementById("sale_personError" + class_name).textContent = "Customer Name Required.";
            document.getElementById("sale_person" + class_name).classList.add("is-invalid");
            isValid = false;
        }

    

        
        let order_no = $("#order_no").val();
        let form = $("#orderForm")[0];
        let data = new FormData(form);
        data.append('order_no', order_no);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.sales-order-update') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function(response) {
                if ($.isEmptyObject(response.error)) 
                {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    $('#order-details-edit-modal').modal('hide');
                    get_orderdetails();
                }
                else 
                {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                    $.each(response.error, function(index, value) {
                        
                    });
                }
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
    });
    

    $("#itemsForm").submit(function (e) {
        e.preventDefault();
    
        let checkedItems = [];
    
        $(".remove-item-checkbox:checked").each(function () {
            checkedItems.push($(this).val());
        });
    
        if (checkedItems.length === 0) {
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Please select at least one item to remove'
            });
            return;
        }
    
        let formData = new FormData(this);
        formData.append('remove_items', JSON.stringify(checkedItems));
        formData.append('order_no', $("#order_no").val());
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.sales-remove-product') }}",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
            success: function (response) {
                if (response.success) {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    $('#edit-remove-item-modal').modal('hide');
                    get_orderdetails();
                }
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
        });
    });
    
    $("#roundOffAmount").on("input", function () {
        let oldPayable = parseFloat($("#old_total_payable").val()) || 0;
        let roundOff   = parseFloat($(this).val()) || 0;

        let newPayable = oldPayable + roundOff;

        // Optional: keep 2 decimal places
        $("#newtotalpayable").val(newPayable.toFixed(2));
    });
    
    
    $("#roundoffForm").submit(function(e) 
    {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let roundOffAmount = document.getElementById("roundOffAmount" + class_name).value.trim();


        if (roundOffAmount === "") {
            document.getElementById("roundOffAmountError" + class_name).textContent = "Round Off Required.";
            document.getElementById("roundOffAmount" + class_name).classList.add("is-invalid");
            isValid = false;
        }

    

        
        let order_no = $("#order_no").val();
        let form = $("#roundoffForm")[0];
        let data = new FormData(form);
        data.append('order_no', order_no);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.roundoff-value-update') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function(response) {
                if ($.isEmptyObject(response.error)) 
                {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    $('#round-off-amount-edit-modal').modal('hide');
                    get_orderdetails();
                }
                else 
                {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                    $.each(response.error, function(index, value) {
                        
                    });
                }
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
    });
    
    
    $(document).on("keyup", "#payamount", function () {
        let totalPayable = parseFloat({{ $sale->pending_amount }}) || 0;
        let payAmount    = parseFloat($(this).val()) || 0;
    
        if (payAmount > totalPayable) {
            alert("Pay amount cannot be greater than total payable amount");
            $(this).val(totalPayable.toFixed(2));
            $("#pendingamount").val("0.00");
            return;
        }
    
        let pending = totalPayable - payAmount;
        $("#pendingamount").val(pending.toFixed(2));
    });
    
    
    $("#newpaymentForm").submit(function(e) 
    {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let payamount = document.getElementById("payamount" + class_name).value.trim();


        if (payamount === "") {
            document.getElementById("payamountError" + class_name).textContent = "pay amountRequired.";
            document.getElementById("payamount" + class_name).classList.add("is-invalid");
            isValid = false;
        }

        if ($("input[name='paymethod']:checked").length === 0) {
            $("#paycash").closest(".d-flex").after(
                '<span class="text-danger error" id="payMethodError">Please select a payment method.</span>'
            );
            isValid = false;
        } else {
            $("#payMethodError").remove();
        }

        
        let order_no = $("#order_no").val();
        let form = $("#newpaymentForm")[0];
        let data = new FormData(form);
        data.append('order_no', order_no);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.add-new-payment-order') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function(response) {
                if ($.isEmptyObject(response.error)) 
                {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    $('#edit-new-payment-modal').modal('hide');
                    get_orderdetails();
                }
                else 
                {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                    $.each(response.error, function(index, value) {
                        
                    });
                }
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
    });
    
    
    $("#newitemsForm").submit(function (e) {
        e.preventDefault();
    
        let isValid = true;
    
        // Clear old errors
        $(".error").text("");
        $(".is-invalid").removeClass("is-invalid");
    
        // ===============================
        // BASIC VALIDATION EXAMPLE
        // ===============================
        let modal_retail_price = $("#modal_retail_price").val().trim();
    
        if (modal_retail_price === "") {
            $("#modal_retail_priceError").text("Pay amount required.");
            $("#modal_retail_price").addClass("is-invalid");
            isValid = false;
        }
    
        if (!isValid) {
            return false;
        }
    
        // ===============================
        // FORM DATA (AUTO COLLECTS ALL)
        // ===============================
        let form = document.getElementById("newitemsForm");
        let data = new FormData(form);
    
        // Append extra values
        let order_no = $("#order_no").val();
        data.append("order_no", order_no);
    
        // ===============================
        // DEBUG: VIEW ALL FORM VALUES
        // ===============================
        console.log("---- FORM DATA ----");
        for (let pair of data.entries()) {
            console.log(pair[0] + " : " + pair[1]);
        }
    
        // ===============================
        // AJAX SUBMIT
        // ===============================
        $.ajax({
            type: "POST",
            url: "{{ route('admin.add-new-item-order') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
            success: function (response) {
    
                if ($.isEmptyObject(response.error)) {
    
                    $.toaster({
                        priority: "success",
                        title: response.success,
                        message: ""
                    });
    
                    $("#add-new-item-modal").modal("hide");
                    get_orderdetails();
    
                } else {
    
                    // Laravel validation errors
                    $.each(response.error, function (key, value) {
                        let field = $("[name='" + key + "']");
                        field.addClass("is-invalid");
                        $("#" + key + "Error").text(value[0]);
                    });
                }
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
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
document.addEventListener('DOMContentLoaded', function () {

    function toNumber(val) {
        return parseFloat(val) || 0;
    }

    // Count helpers
    function checkedCount() {
        return document.querySelectorAll('.remove-item-checkbox:checked').length;
    }

    function totalItemsCount() {
        return document.querySelectorAll('.remove-item-checkbox').length;
    }

    // Store original values
    [
        'cart_discount',
        'cart_discount_by',
        'cart_discount_per',
        'cart_discount_resion',
        'loyalty_point',
        'loyalty_point_apply',
        'coupon_amount',
        'coupon_id',
        'round_off'
    ].forEach(function (id) {
        let el = document.getElementById(id);
        if (el) el.dataset.original = el.value;
    });

    document.querySelectorAll('.remove-item-checkbox').forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            // ❌ Prevent removing all items
            if (this.checked && checkedCount() === totalItemsCount()) {
                alert('You cannot delete all items from an order. At least one item must remain.');
                this.checked = false;
                return;
            }

            let price    = toNumber(this.dataset.price);
            let discount = toNumber(this.dataset.discount);
            let basic    = toNumber(this.dataset.basic || 0);
            let gst      = toNumber(this.dataset.gst || 0);

            let totalItemPrice = document.getElementById('total_item_price');
            let totalDiscount  = document.getElementById('total_discount');
            let totalPayable   = document.getElementById('total_payable');

            let cartDiscount    = document.getElementById('cart_discount');
            let cartDiscountBy  = document.getElementById('cart_discount_by');
            let cartDiscountPer = document.getElementById('cart_discount_per');
            let cartDiscountRes = document.getElementById('cart_discount_resion');

            let loyaltyPoint = document.getElementById('loyalty_point');
            let loyaltyApply = document.getElementById('loyalty_point_apply');

            let couponAmount = document.getElementById('coupon_amount');
            let couponId     = document.getElementById('coupon_id');

            let roundOff = document.getElementById('round_off');

            let totalBasicAmount = document.getElementById('total_basic_amount');
            let totalGstAmount   = document.getElementById('total_gst_amount');

            let itemPriceVal = toNumber(totalItemPrice.value);
            let discountVal  = toNumber(totalDiscount.value);
            let basicVal     = toNumber(totalBasicAmount?.value);
            let gstVal       = toNumber(totalGstAmount?.value);

            if (this.checked) {
                // REMOVE ITEM
                itemPriceVal -= price;
                discountVal  -= discount;
                basicVal     -= basic;
                gstVal       -= gst;

                // REMOVE all discounts
                cartDiscount.value = 0;
                loyaltyPoint.value = 0;
                couponAmount.value = 0;
                roundOff.value = 0;

                cartDiscountBy.value  = '';
                cartDiscountPer.value = '';
                cartDiscountRes.value = '';
                loyaltyApply.value    = '';
                couponId.value        = '';

            } else {
                // ADD ITEM BACK
                itemPriceVal += price;
                discountVal  += discount;
                basicVal     += basic;
                gstVal       += gst;

                // RESTORE original discounts
                cartDiscount.value = cartDiscount.dataset.original || 0;
                loyaltyPoint.value = loyaltyPoint.dataset.original || 0;
                couponAmount.value = couponAmount.dataset.original || 0;
                roundOff.value     = roundOff.dataset.original || 0;

                cartDiscountBy.value  = cartDiscountBy.dataset.original || '';
                cartDiscountPer.value = cartDiscountPer.dataset.original || '';
                cartDiscountRes.value = cartDiscountRes.dataset.original || '';
                loyaltyApply.value    = loyaltyApply.dataset.original || '';
                couponId.value        = couponId.dataset.original || '';
            }

            // Recalculate payable
            let payableVal =
                itemPriceVal -
                discountVal -
                toNumber(cartDiscount.value) -
                toNumber(loyaltyPoint.value) -
                toNumber(couponAmount.value) +
                toNumber(roundOff.value);

            // Update UI
            totalItemPrice.value = itemPriceVal.toFixed(2);
            totalDiscount.value  = discountVal.toFixed(2);
            totalPayable.value   = payableVal.toFixed(2);

            if (totalBasicAmount) totalBasicAmount.value = basicVal.toFixed(2);
            if (totalGstAmount)   totalGstAmount.value   = gstVal.toFixed(2);
        });
    });
});
</script>

<script>
    /** ==============================
     *  Product Type Wise Modal Div Open
     *  ============================== */
     
    $(document).on("change", ".product-type", function () {


        let selectedType = $(this).val();
        let store_id = '{{$sale->store_id}}';
        let tax_rule = '{{$sale->tax_rule}}';
        
        if (store_id == '') {
            $.toaster({
                priority: 'danger',
                title: ' error',
                message: 'Select Store',
                timeout: 3000
            });

            return;
        }
    
        $.ajax({
            url: "{{ route('admin.get-gst-details') }}",
            type: "GET",
            data: { product_type: selectedType },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
            success: function (res) {
    
                if (selectedType == 'Frame' || selectedType == 'Goggles') {
                    $.toaster({
                        priority: 'danger',
                        title: ' error',
                        message: 'Frame or Goggles use barcode',
                        timeout: 3000
                    });
    
                    return;
                }
                handleProductType(selectedType,store_id);
                $("#modal_tax_rule").val(tax_rule);
   
            },
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
        });
    
    });
    
    
    /** =================================
     *  Modal Div Handle Product Type Wise
     * ================================== */
     
    function handleProductType(type,store_id)
    {
        $.ajax({
                url: "{{ route('admin.get-store-details') }}",
                type: "GET",
                data: { selectedType: store_id },
                success: function (res) {
                if (res.success) {

                    if(res.data.sales_tax_type == 0) 
                    {
                        $("#modal_product_code").prop("readonly", true);
                        $("#modal_product_details").prop("readonly", true);
                        
                        switch (type) 
                        {
                            case "Frame":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                $("#qtydiv, #purchasediv").show();
                                $("#modal_product_code").prop("readonly", true);
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                 $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                break;
                            
                             case "Goggles":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                $("#modal_product_code").prop("readonly", true);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                 $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                break;    
                                
                            case "Glass":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#materialediv, #colordiv, #designdiv, #coatingdiv, #indexdiv,#branddiv").show();
                                $("#Prescriptionglassdiv,#qtydiv, #purchasediv,#branddiv").show();
                                $("#rlinventory,#counteye").hide();
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv,#SelectBox").hide();
                                $("#modal_product_code").prop("readonly", false);
                                 $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                break;
                    
                            case "Lens":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#numberdiv, #ctdiv, #typediv, #validitydiv, #materialediv,#colordiv,#Prescriptionglassdiv").show();
                                $("#purchasediv,#SelectBox").show();
                                $("#inhousepackage, #branddiv,#designdiv,#coatingdiv,#indexdiv").hide();
                                $("#nearvisionright, #addright, #nearvisionleft, #addleft, #wparameter, #pdright").hide();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv,#qtydiv").hide();
                                 $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                break;
                    
                            case "Solution":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#colordiv,#packingtypediv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                 $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                break;
                    
                            case "Other":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#shapediv, #colordiv, #sizediv, #typediv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                 $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                break;
                    
                            case "Repair":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#materialediv,#colordiv,#designdiv,#coatingdiv,#indexdiv,#numberdiv,#ctdiv,#typediv,#validitydiv,#shapediv,#sizediv,#variantdiv").hide();
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv,#qtydiv,#purchasediv").hide();
                                $("#modal_product_details").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                 $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                break;
                        }        
            
                    }
                    else 
                    {
                        $("#modal_product_code").prop("readonly", true);
                        $("#modal_product_details").prop("readonly", true);
                        
                        switch (type) 
                        {
                            case "Frame":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                    $("#qtydiv, #purchasediv").show();
                                    $("#modal_product_code").prop("readonly", true);
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                    break;
                                
                                 case "Goggles":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                    $("#modal_product_code").prop("readonly", true);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                    break;    
                                    
                                case "Glass":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#materialediv, #colordiv, #designdiv, #coatingdiv, #indexdiv,#branddiv").show();
                                    $("#Prescriptionglassdiv,#qtydiv, #purchasediv,#branddiv").show();
                                    $("#rlinventory,#counteye,#SelectBox").hide();
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                     $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                    $("#modal_product_code").prop("readonly", false);
                                    
                                    break;
                        
                                case "Lens":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#numberdiv, #ctdiv, #typediv, #validitydiv, #materialediv,#colordiv,#Prescriptionglassdiv").show();
                                    $("#purchasediv,#SelectBox").show();
                                    $("#inhousepackage, #branddiv,#designdiv,#coatingdiv,#indexdiv").hide();
                                    $("#nearvisionright, #addright, #nearvisionleft, #addleft, #wparameter, #pdright,#qtydiv").hide();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").show();
                                    $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                    break;
                        
                                case "Solution":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#colordiv,#packingtypediv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                     $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                    break;
                        
                                case "Other":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#shapediv, #colordiv, #sizediv, #typediv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                     $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                    break;
                        
                                case "Repair":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#materialediv,#colordiv,#designdiv,#coatingdiv,#indexdiv,#numberdiv,#ctdiv,#typediv,#validitydiv,#shapediv,#sizediv,#variantdiv").hide();
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv,#qtydiv,#purchasediv").hide();
                                    $("#modal_product_details").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                     $("#pdetailsdiv, #retailspricediv,#discountdiv,#basediv,#totalsalediv").show();
                                    break;
                        }    
                        
                    }
                    
                   
                } else {
                    $.toaster({
                        priority: 'danger',
                        title: ' Store Not Found',
                        message: 'Please check and try again.',
                        timeout: 3000
                    });
                }
            }
        });
    
    }
    
        
    /** ==============================
     *  Lenstype wise Package List
     *  ============================== */
    
     $(document).on('click', 'input[name="lenstype"]', function () {

        let lensType = $(this).val();
    
        $("#lenspackage").html('<p class="text-info">Loading packages...</p>');
    
        $.ajax({
            url: "{{ route('admin.get.lens.packages') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                lens_type: lensType
            },
            success: function (response) {
    
                if (!response.success || !response.packages || response.packages.length === 0) {
                    $("#lenspackage").html(
                        '<div class="alert alert-warning">No packages found for this lens type.</div>'
                    );
                    return;
                }
    
                let html = `<div class="row gy-3">`;
    
                response.packages.forEach(pkg => {
    
                    let imagesHtml = '';
    
                    if (pkg.product_image) {
                        try {
                            let imgs = JSON.parse(pkg.product_image);
    
                            if (Array.isArray(imgs) && imgs.length > 0) {
                                let basePath = "{{ asset('uploads/glass/product') }}";
                                let path = `${basePath}/${pkg.product_id}`;
    
                                imgs.forEach(img => {
                                    imagesHtml += `
                                        <img src="${path}/${img.trim()}"
                                             class="img-thumbnail me-2 mb-2"
                                             style="max-width:70px;">
                                    `;
                                });
                            } else {
                                imagesHtml = '<small class="text-muted">No images available</small>';
                            }
                        } catch (e) {
                            imagesHtml = '<small class="text-muted">No images available</small>';
                        }
                    } else {
                        imagesHtml = '<small class="text-muted">No images available</small>';
                    }
    
                    html += `
                        <div class="col-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
    
                                        <input type="radio"
                                               name="lens_package"
                                               class="form-check-input me-3 mt-1 lens-package-radio"
                                               value="${pkg.id}"
                                               data-id="${pkg.id}"
                                               data-name="${pkg.productdetails}"
                                               data-price="${pkg.Retail_Price}"
                                               data-productcode="${pkg.product_code}"
                                               data-productid="${pkg.product_id}"
                                               data-description="${pkg.Description ? pkg.Description.replace(/"/g, '&quot;') : ''}">
    
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">${pkg.productdetails}</h5>
    
                                            <p class="text-muted mb-2">
                                                ${pkg.Description || ''}
                                            </p>
    
                                            <div class="mb-2">
                                                ${imagesHtml}
                                            </div>
    
                                            <h6 class="text-success mb-0">
                                                Price: ₹ ${pkg.Retail_Price}
                                            </h6>
                                        </div>
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
    
                html += `</div>`;
    
                $("#lenspackage").html(html);
            },
            error: function () {
                $("#lenspackage").html(
                    '<p class="text-danger">Error loading packages.</p>'
                );
            }
        });
    });
    
    
    /** ==============================
     *  Package Details Get In Input
     *  ============================== */
     
     $(document).on("click", ".lens-package-radio", function () {

    let pkgName = $(this).data("name");
    let pkgproductcode = $(this).data("productcode");
    let pkgproductid = $(this).data("productid");
    let pkgPrice = parseFloat($(this).data("price")) || 0;
    let pkgId = $(this).data("id");

    $.ajax({
        url: "{{ route('admin.get.lens.packages.coating') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            pkgId: pkgId
        },
        success: function (response) {

            if (!response.success || !response.packages || response.packages.length === 0) {
                $("#glasscoating").html('');
                return;
            }

            let html = `<div class="row gy-3">`;

            response.packages.forEach(pkg => {

                let path = "{{ asset('frontend/asset/img/Photo-Chromatic_Coating-update.webp') }}";

                html += `
                    <div class="col-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">

                                    <input type="radio"
                                           name="is_Coating"
                                           class="form-check-input me-3 lens-coating-radio"
                                           value="${pkg.id}"
                                           data-coatingprice="${pkg.coating_price}"
                                           data-lens_price="${pkgPrice}"
                                           data-name="${pkgName}"
                                           data-coatingname="${pkg.coating_name}">

                                    <img src="${path}"
                                         class="img-thumbnail me-3"
                                         style="max-width:70px;">

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${pkg.coating_name}</h6>
                                        <span class="text-success fw-semibold">
                                            ₹ ${pkg.coating_price}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += `</div>`;

            $("#glasscoating").html(html);
        },
        error: function () {
            $("#glasscoating").html(
                '<p class="text-danger">Error loading packages.</p>'
            );
        }
    });

    /* =============================
       Fill product details
    ============================== */

    $("#modal_product_code").val(pkgproductcode);
    $("#modal_product_id").val(pkgproductid);
    $("#modal_product_details").val(pkgName);

    /* =============================
       Default values
    ============================== */

    $("#modal_purchase_price").val(0);
    $("#modal_discount").val(0);
    $("#modal_discount_amount").val(0.00);
    $("#modal_gst").val(0);
    $("#modal_tax_rule").val('Include');

    /* =============================
       GST Calculation
    ============================== */

    let gstPercent = parseFloat($("#modal_gst").val()) || 0;

    let basePrice = pkgPrice / (1 + gstPercent / 100);
    let gstAmount = pkgPrice - basePrice;
    let totalPrice = pkgPrice;

    $("#modal_retail_price").val(pkgPrice.toFixed(2));
    $("#modal_base_price").val(basePrice.toFixed(2));
    $("#modal_gst_amount").val(gstAmount.toFixed(2));
    $("#modal_total_sale").val(totalPrice.toFixed(2));
});
    
    
    
    /** ==============================
     *  Package Coating Details Get In Input
     *  ============================== */
     
     $(document).on("click", ".lens-coating-radio", function () {
        let pkgName = $(this).data("name");
        let pkgCoatingName = $(this).data("coatingname");
        let pkgCoatingPrice = parseFloat($(this).data("coatingprice")) || 0;
        let pkgPrice = parseFloat($(this).data("lens_price")) || 0;
        let pkgId = $(this).data("id");


        let details = pkgName;
        $("#modal_product_details").val(details);
        $("#modal_product_coating").val(pkgCoatingName);
    
        $("#modal_purchase_price").val(0);
        $("#modal_discount").val(0);
        $("#modal_discount_amount").val(0.00);
        $("#modal_gst").val(0);
        $("#modal_tax_rule").val('Include');
        
    
        let gstPercent = parseFloat($("#modal_gst").val()) || 0;
    
        let basePrice = 0, gstAmount = 0, totalPrice = 0;
    
        basePrice = (pkgPrice+pkgCoatingPrice) / (1 + gstPercent / 100);
        gstAmount = (pkgPrice+pkgCoatingPrice) - basePrice;
        totalPrice = (pkgPrice+pkgCoatingPrice);
    
    
        $("#modal_retail_price").val((pkgPrice+pkgCoatingPrice).toFixed(2));
        $("#modal_base_price").val(basePrice.toFixed(2));
        $("#modal_gst_amount").val(gstAmount.toFixed(2));
        $("#modal_total_sale").val(totalPrice.toFixed(2));
    });
    
    
    /** ==============================
     *  Product Code Wise Product Details
     *  ============================== */
    $(document).on('keyup', '#modal_product_code', function () {
        let $input = $(this);
        let productCode = $input.val();
        let productType = $("#producttype").val();
    
        if (productCode.length >= 2 && productType !== '') {
            $.ajax({
                url: "{{ route('admin.get-product-wise-code') }}",
                method: 'GET',
                dataType: 'json', 
                data: {
                    product_type: productType,
                    query: productCode
                },
                success: function (response) {
                    let suggestionBox = $input.siblings('.suggestion-box');
                    suggestionBox.empty();
    
                    if (Array.isArray(response) && response.length > 0) {
                        response.forEach(function (item) {
                            suggestionBox.append(
                                `<a href="#" class="list-group-item list-group-item-action">${item.productdetails}</a>`
                            );
                        });
                    } else {
                        suggestionBox.append('<div class="list-group-item text-muted">No results found</div>');
                    }
    
                    suggestionBox.show();
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", error);
                }
            });
        } else {
            $input.siblings('.suggestion-box').hide();
        }
    });
    
    $(document).on('click', '.suggestion-box a', function (e) {
        e.preventDefault();
        let selectedText = $(this).text();
        $('#modal_product_code').val(selectedText);
        $(this).closest('.suggestion-box').hide();
    });
    
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#modal_product_code, .suggestion-box').length) {
            $('.suggestion-box').hide();
        }
    });
    
    $(document).on('click', '.suggestion-box a', function (e) {
        e.preventDefault();
    
        let $this = $(this);
        let selectedCode = $this.text().trim();
        let $input = $this.closest('.suggestion-box').prev('.product-code');
        let productType = $("#producttype").val();
        let tax_rule = '{{$sale->tax_rule}}';
        
        let rightLeft = [];
        $('input[name="modal_rightleft[]"]:checked').each(function () {
            rightLeft.push($(this).val());
        });
        
        


        $input.val(selectedCode);
        $this.closest('.suggestion-box').hide();

        $.ajax({
            url: "{{ route('admin.get-store-product-by-product-code') }}",
            method: 'GET',
            data: {
                tax_rule: tax_rule,
                selectedCode: selectedCode,
                rightLeft: rightLeft,
                productType: productType
                
            },
            
            success: function (res) 
            {
                if (res.success) 
                {
                    $("#modal_product_details").val(res.data.product_details);
                    $("#modal_company").val(res.data.product_company);
                    $("#modal_quality").val(res.data.product_quality);
                    $("#modal_product_material").val(res.data.product_material);
                    $("#modal_product_color").val(res.data.product_color);
                    $("#modal_product_design").val(res.data.product_design);
                    $("#modal_product_coating").val(res.data.product_coating);
                    $("#modal_product_index").val(res.data.product_index);
                    $("#modal_product_number").val(res.data.product_number);
                    $("#modal_product_ct").val(res.data.product_ct);
                    $("#modal_product_validity").val(res.data.product_validity);
                    $("#modal_product_typess").val(res.data.product_typesss);
                    $("#modal_product_variant").val(res.data.product_variant);
                    $("#modal_product_shape").val(res.data.product_shape);
                    $("#modal_product_size").val(res.data.product_size);
                    $("#modal_purchase_price").val(res.data.purchase_price);
                    $("#modal_retail_price").val(res.data.retail_price);
                    $("#modal_product_code").val(res.data.product_code);
                    $("#modal_hsncode").val(res.data.hsn_code);
                    $("#modal_gst").val(res.data.gstRate);
                    $("#modal_base_price").val(res.data.basePrice);
                    $("#modal_gst_amount").val(res.data.gstAmount);
                    $("#modal_total_sale").val(res.data.totalSale);
                    $("#modal_product_id").val(res.data.product_id);
                    $("#modal_quantity").val(res.data.product_qty);
                    $("#modal_discount").val(res.data.discount);
                    $("#modal_discount_amount").val(res.data.discountamt);
                }    
            }
            
        });
    });
    
    /** =================================
     *  Glass Brand Wise Div Show
     * ================================== */
    
    $(document).on('click', 'input[name="brand_type"]', function () 
    {
        let selected = $(this).val();
    
        if (selected == '0') { 
            $("#codediv, #iddiv, #companydiv, #qualitydiv,#materialediv, #colordiv, #designdiv, #coatingdiv, #indexdiv").show();
            $("#Prescriptionglassdiv,#qtydiv, #hsncodediv, #gstdiv, #taxdiv, #purchasediv, #gstamtdiv").show();
            $("#inhousepackage,#branddiv").show();

        } 
        else if (selected == '1') { 
            $("#codediv, #iddiv, #companydiv, #qualitydiv,#materialediv, #colordiv, #designdiv, #coatingdiv, #indexdiv,#branddiv").show();
            $("#Prescriptionglassdiv,#qtydiv, #hsncodediv, #gstdiv, #taxdiv, #purchasediv, #gstamtdiv").show();
            $("#inhousepackage").hide();
        }
    });
    
    /** =================================
     *  Calculate Modal Input Price
     * ================================== */
    
    function recalcModalTotals() {
        let qty = parseFloat($("#modal_quantity").val()) || 1;
        let retailPrice = parseFloat($("#modal_retail_price").val()) || 0;
        let gst = parseFloat($("#modal_gst").val()) || 0;
        let discountPercent = parseFloat($("#modal_discount").val()) || 0;
        let discountAmount = parseFloat($("#modal_discound_amount").val()) || 0;
        let taxRule = '{{$sale->tax_rule}}';

        let baseBeforeDiscount = retailPrice * qty;

        let appliedDiscount = 0;
        if (discountPercent > 0) {
            appliedDiscount = baseBeforeDiscount * (discountPercent / 100);
            $("#modal_discound_amount").val(appliedDiscount.toFixed(2)); // auto-update
        } else if (discountAmount > 0) {
            appliedDiscount = discountAmount;
        }
        let afterDiscount = baseBeforeDiscount - appliedDiscount;

        let basePrice = 0, gstAmount = 0, totalSale = 0;

        if (taxRule === "Include") {
            basePrice = afterDiscount / (1 + (gst / 100));
            gstAmount = afterDiscount - basePrice;
            totalSale = afterDiscount;
        } 
        else if (taxRule === "Exclude ") { // note: value has space in your HTML!
            basePrice = afterDiscount;
            gstAmount = basePrice * (gst / 100);
            totalSale = basePrice + gstAmount;
        } 
        else { 
            basePrice = afterDiscount;
            gstAmount = 0;
            totalSale = basePrice;
        }

        
        $("#modal_base_price").val(basePrice.toFixed(2));
        $("#modal_gst_amount").val(gstAmount.toFixed(2));
        $("#modal_total_sale").val(totalSale.toFixed(2));
    }

    // Trigger on change
    $("#modal_gst, #modal_retail_price, #modal_discount, #modal_discound_amount, #tax_rule")
        .on("input change", recalcModalTotals);

    // Initial calculation
    recalcModalTotals();
    
    /** =================================
     *  Select Customer Prescription
     * ================================== */
     
    $('#PrescriptionBtn').on('click', function() 
    {
        let contact_no = '{{$sale->contact_no}}';;
        let errorField = $('#mobileError');
    
        errorField.text('');
    
    
        if (!/^[6-9]\d{9}$/.test(contact_no)) {
            $.toaster({
                priority: 'danger',
                title: ' Mobile No not valid',
                message: 'Please enter a valid 10-digit mobile number.',
                        timeout: 3000
            });
            return;
        }
        
        $.ajax({
            url: "{{ route('admin.getprescription') }}",  // Laravel route
            method: 'GET',
            data: { contact_no: contact_no },
            success: function(response) {
                let tableBody = $('#prescriptionTable tbody');
                tableBody.empty(); // Clear old data
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(prescription) 
                    {
                         function formatValue(value) {
                          return value === null || value === undefined || value === "" ? "-" : value;
                        }
    
                        let row = `
                            <tr>
                                <td>
                                <input type="radio" name="prescriptioneyetest" class="prescription-eyetest"
                                        value="1"
                                        data-re_sph_new="${prescription.re_sph_new}"
                                        data-re_cyl_new="${prescription.re_cyl_new}"
                                        data-re_axis_new="${prescription.re_axis_new}"
                                        data-pd_re_new="${prescription.pd_re_new}"
                                        data-le_sph_new="${prescription.le_sph_new}"
                                        data-le_cyl_new="${prescription.le_cyl_new}"
                                        data-le_axis_new="${prescription.le_axis_new}"
                                        data-pd_le_new="${prescription.pd_le_new}"
                                        data-cust_name="${prescription.cust_name}"
                                        data-optometrist="${prescription.optometrist}">
                                </td>
                                <td>${prescription.cust_name}</td>
                                <td>
                                     <table class="table card-table table-vcenter text-nowrap">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>SPH</th>
                                                <th>CYL</th>
                                                <th>AXIS</th>
                                                <th>PD</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                                <tr>
                                                <th scope="row">RE</th>
                                                <td>${formatValue(prescription.re_sph_new)}</td>
                                                <td>${formatValue(prescription.re_cyl_new)}</td>
                                                <td>${formatValue(prescription.re_axis_new)}</td>
                                                <td>${formatValue(prescription.pd_re_new)}</td>
                                              </tr>
                                              <tr>
                                                <th scope="row">LE</th>
                                                <td>${formatValue(prescription.le_sph_new)}</td>
                                                <td>${formatValue(prescription.le_cyl_new)}</td>
                                                <td>${formatValue(prescription.le_axis_new)}</td>
                                                <td>${formatValue(prescription.pd_le_new)}</td>
                                              </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>${prescription.optometrist}</td>
                                <td>${prescription.date}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="5" class="text-center">No prescriptions found.</td></tr>');
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch prescription details.',
                    timeout: 3000
                });
            }
        });
        
        $('#PrescriptionModal').modal('show');
    });
    
    $(document).on('click', '.prescription-eyetest', function()
    {
        let re_sph_new = $(this).data('re_sph_new');
        let re_cyl_new = $(this).data('re_cyl_new');
        let re_axis_new = $(this).data('re_axis_new');
        let pd_re_new = $(this).data('pd_re_new');
    
        let le_sph_new = $(this).data('le_sph_new');
        let le_cyl_new = $(this).data('le_cyl_new');
        let le_axis_new = $(this).data('le_axis_new');
        let pd_le_new = $(this).data('pd_le_new');
        
        let optometrist = $(this).data('optometrist');
        let cust_name = $(this).data('cust_name');
    
        // Fill modal dropdowns
        $('#GL_EYE_RS_D').val(re_sph_new);
        $('#GL_EYE_RC_D').val(re_cyl_new);
        $('#GL_EYE_RA_D').val(re_axis_new);
        $('#GL_EYE_RP_D').val(pd_re_new);
    
        $('#GL_EYE_LS_D').val(le_sph_new);
        $('#GL_EYE_LC_D').val(le_cyl_new);
        $('#GL_EYE_LA_D').val(le_axis_new);
        $('#GL_EYE_LP_D').val(pd_le_new);
        
        $('#modal_doctor_name').val(optometrist);
        $('#modal_patient_name').val(cust_name);

        $('#PrescriptionModal').modal('hide');
    });
    
    $('#clearPrescriptionBtn').on('click', function() {
        // Reset all RE inputs
        $('#GL_EYE_RS_D').val('');
        $('#GL_EYE_RC_D').val('');
        $('#GL_EYE_RA_D').val('');
        $('#GL_EYE_RP_D').val('');
    
        // Reset all LE inputs
        $('#GL_EYE_LS_D').val('');
        $('#GL_EYE_LC_D').val('');
        $('#GL_EYE_LA_D').val('');
        $('#GL_EYE_LP_D').val('');
        $('#modal_doctor_name').val('');
        $('#modal_patient_name').val('');
    
    
        $('.prescription-eyetest').closest('tr').removeClass('table-active');
    });
    
    
    /** ==============================
     *  Barcode Wise Product Details
     *  ============================== */
    $(document).on("change", "#barcode_u", function () 
    {
        let barcode = $(this).val().trim();
        let tax_rule = '{{$sale->tax_rule}}';
        let store_id = '{{$sale->store_id}}';
        let sale_type = 'customer';
        
        if (barcode !== '' && tax_rule !== '') 
        {
            let duplicate = false;
            $("#saleTable .barcode").not(this).each(function () {
                if ($(this).val().trim() === barcode) {
                    duplicate = true;
                    return false; 
                }
            });
    
            if (duplicate) 
            {
                $.toaster({
                    priority: 'danger',
                    title: 'Duplicate Barcode',
                    message: 'This barcode is already added in another row.',
                    timeout: 3000
                });
                return;
            }

            $.ajax({
                url: "{{ route('admin.get-store-product-by-barcode') }}",
                type: "GET",
                data: { barcode: barcode,tax_rule:tax_rule,sale_type:sale_type,to_store:store_id},
                beforeSend: function () {
                    $("#ajaxLoader").show(); 
                },
                success: function (res) 
                {
                    if (res.success) 
                    {
                        handleProductType(res.data.product_type,store_id);
                        $("#producttype").val(res.data.product_type);
                        $("#modal_product_details").val(res.data.product_details);
                        $("#modal_company").val(res.data.product_company);
                        $("#modal_quality").val(res.data.product_quality);
                        $("#modal_product_material").val(res.data.product_material);
                        $("#modal_product_color").val(res.data.product_color);
                        $("#modal_product_design").val(res.data.product_design);
                        $("#modal_product_coating").val(res.data.product_coating);
                        $("#modal_product_index").val(res.data.product_index);
                        $("#modal_product_number").val(res.data.product_number);
                        $("#modal_product_ct").val(res.data.product_ct);
                        $("#modal_product_validity").val(res.data.product_validity);
                        $("#modal_product_typess").val(res.data.product_typesss);
                        $("#modal_product_variant").val(res.data.product_variant);
                        $("#modal_product_shape").val(res.data.product_shape);
                        $("#modal_product_size").val(res.data.product_size);
                        $("#modal_purchase_price").val(res.data.purchase_price);
                        $("#modal_retail_price").val(res.data.retail_price);
                        $("#modal_product_code").val(res.data.product_code);
                        $("#modal_hsncode").val(res.data.hsn_code);
                        $("#modal_gst").val(res.data.gstRate);
                        $("#modal_base_price").val(res.data.basePrice);
                        $("#modal_gst_amount").val(res.data.gstAmount);
                        $("#modal_total_sale").val(res.data.totalSale);
                        $("#modal_tax_rule").val(tax_rule);
                        $("#modal_product_id").val(res.data.product_id);
                        $("#modal_quantity").val(res.data.product_qty);
                        $("#modal_discount").val(res.data.discount);
                        $("#modal_discount_amount").val(res.data.discountamt);
                        
                        if (res.data.is_pair == 1) {
                            $("input[name='modal_rightleft[]'][value='Left']").prop("checked", false);
                        } else {
                            $("input[name='modal_rightleft[]'][value='Left']").prop("checked", true);
                        }
                    }
                    else 
                    {
                        
                
                        $.toaster({
                            priority: 'danger',
                            title: ' Barcode Not Found',
                            message: 'Please check and try again.',
                           timeout: 3000
                        });
                    }
                },
                complete: function () {
                    $("#ajaxLoader").fadeOut(); 
                }
            });
        }
        else
        {
            $.toaster({
                priority: 'danger',
                title: ' Error',
                message: 'Something went wrong while fetching product.'
            });
        }
    });
    
    
 // ==========================
    // Global arrays for Right and Left selections
    // ==========================
    let selectedRightBarcodes = [];
    let selectedLeftBarcodes = [];
    
    // ==========================
    // Open Lens Barcode Modal
    // ==========================
    $('.checkLensFromBarcode').on('click', function() {
        let clickedButtonId = this.id;
        let callbtn = clickedButtonId === 'checkRightLensFromBarcode' ? 'Right' : 'Left';
        
        $('#lensSide').val(callbtn);
    
        let modal_product_details = $('#modal_product_details').val().trim();
        let modal_product_code = $('#modal_product_code').val().trim();
        let store_id = '{{$sale->store_id}}';
    
        $.ajax({
            url: "{{ route('admin.getlensbarcode') }}",
            method: 'GET',
            data: { 
                product_details: modal_product_details,
                product_code: modal_product_code,
                store_id: store_id 
            },
            success: function(response) {
                let tableBody = $('#LensBarcodeTable tbody');
                tableBody.empty();
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(lensbarcode) {
                        let row = `
                            <tr>
                                <td>
                                    <input type="checkbox" class="lensCheckbox"
                                        name="barcodeselectedid[]"
                                        value="${lensbarcode.barcode_id}"
                                        data-perbox="${lensbarcode.perbox}"
                                        data-purchase="${lensbarcode.purchase_price}"
                                        data-retail="${lensbarcode.retail_price}">
                                </td>
                                <td>${lensbarcode.barcode_no}</td>
                                <td>${lensbarcode.product_code}</td>
                                <td>${lensbarcode.p_details}</td>
                                <td>${lensbarcode.perbox}</td>
                                <td>${lensbarcode.purchase_price}</td>
                                <td>${lensbarcode.retail_price}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } else {
                    tableBody.append('<tr><td colspan="7" class="text-center">No Inventory found.</td></tr>');
                }
    
                $('.lensCheckbox').prop('checked', false);
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch inventory details.',
                    timeout: 3000
                });
            }
        });
    
        $('#LensBarcodeModal').modal('show');
    });
    
    // ==========================
    // Select Barcodes from Modal
    // ==========================
    $('#selectLensBarcode').on('click', function () {
        let callbtn = $('#lensSide').val();
        let newlySelectedIds = [];
        let boxCount = 0;
        let totalPieces = 0;
        let totalPurchase = 0;
        let totalRetail = 0;
    
        $('.lensCheckbox:checked').each(function () {
            let bid = $(this).val();
            let perbox = parseFloat($(this).data('perbox')) || 0;
            let purchase = parseFloat($(this).data('purchase')) || 0;
            let retail = parseFloat($(this).data('retail')) || 0;
    
            newlySelectedIds.push({ id: bid, perbox, purchase, retail });
        });
    
        if (newlySelectedIds.length === 0) {
            $.toaster({
                priority: 'warning',
                title: 'Warning',
                message: 'Please select at least one barcode.',
                timeout: 3000
            });
            return;
        }
    
        // Add to Right or Left selection array
        if (callbtn === "Right") 
        {
            newlySelectedIds.forEach(item => {
                if (!selectedRightBarcodes.some(x => x.id === item.id)) selectedRightBarcodes.push(item);
            });
            
            $('#GL_EYE_RS_D').prop('readonly', true);
            $('#GL_EYE_RC_D').prop('readonly', true);
            $('#GL_EYE_RA_D').prop('readonly', true);
            $('#GL_EYE_RP_D').prop('readonly', true);
            $('#GL_EYE_RV_D').prop('readonly', true);
        } else {
            newlySelectedIds.forEach(item => {
                if (!selectedLeftBarcodes.some(x => x.id === item.id)) selectedLeftBarcodes.push(item);
            });
            
            
            $('#GL_EYE_LS_D').prop('readonly', true);
            $('#GL_EYE_LC_D').prop('readonly', true);
            $('#GL_EYE_LA_D').prop('readonly', true);
            $('#GL_EYE_LP_D').prop('readonly', true);
            $('#GL_EYE_LV_D').prop('readonly', true);
        }
    
        // ✅ Calculate per-side totals
        let rightBoxes = selectedRightBarcodes.length;
        let leftBoxes = selectedLeftBarcodes.length;
    
        let rightPieces = selectedRightBarcodes.reduce((sum, x) => sum + x.perbox, 0);
        let leftPieces = selectedLeftBarcodes.reduce((sum, x) => sum + x.perbox, 0);
    
        let rightPurchase = selectedRightBarcodes.reduce((sum, x) => sum + x.purchase, 0);
        let leftPurchase = selectedLeftBarcodes.reduce((sum, x) => sum + x.purchase, 0);
    
        let rightRetail = selectedRightBarcodes.reduce((sum, x) => sum + x.retail, 0);
        let leftRetail = selectedLeftBarcodes.reduce((sum, x) => sum + x.retail, 0);
    
        // Update Right/Left fields
        $('#lensRightNoOfBoxes').val(rightBoxes).prop('readonly', true);
        $('#lensRightTotalPieces').val(rightPieces).prop('readonly', true);
        $('#lensLeftNoOfBoxes').val(leftBoxes).prop('readonly', true);
        $('#lensLeftTotalPieces').val(leftPieces).prop('readonly', true);
    
        // ✅ Calculate combined totals
        let totalBoxes = rightBoxes + leftBoxes;
        let totalPiecesCombined = rightPieces + leftPieces;
        let totalPurchaseCombined = rightPurchase + leftPurchase;
        let totalRetailCombined = rightRetail + leftRetail;
    
        $('#modal_purchase_price').val(totalPurchaseCombined.toFixed(2)).prop('readonly', true);
        $('#modal_quantity').val(totalBoxes).prop('readonly', true);
        $('#modal_retail_price').val(totalRetailCombined.toFixed(2)).prop('readonly', true);
    
        // GST & Discount calculation
        let gst = parseFloat($("#modal_gst").val()) || 0;
        let discountPercent = parseFloat($("#modal_discount").val()) || 0;
        let discountAmount = parseFloat($("#modal_discound_amount").val()) || 0;
        let taxRule = '{{$sale->tax_rule}}';
    
        let baseBeforeDiscount = totalRetailCombined;
        let appliedDiscount = 0;
        if (discountPercent > 0) appliedDiscount = baseBeforeDiscount * (discountPercent / 100);
        else if (discountAmount > 0) appliedDiscount = discountAmount;
    
        $("#modal_discound_amount").val(appliedDiscount.toFixed(2));
        let afterDiscount = baseBeforeDiscount - appliedDiscount;
    
        let basePrice = 0, gstAmount = 0, totalSale = 0;
        if (taxRule === "Include") {
            basePrice = afterDiscount / (1 + (gst / 100));
            gstAmount = afterDiscount - basePrice;
            totalSale = afterDiscount;
        } else if (taxRule === "Exclude ") {
            basePrice = afterDiscount;
            gstAmount = basePrice * (gst / 100);
            totalSale = basePrice + gstAmount;
        } else {
            basePrice = afterDiscount;
            gstAmount = 0;
            totalSale = basePrice;
        }
    
        $("#modal_base_price").val(basePrice.toFixed(2));
        $("#modal_gst_amount").val(gstAmount.toFixed(2));
        $("#modal_total_sale").val(totalSale.toFixed(2));
    
        // Save all selected barcode IDs globally
        let allSelectedBarcodes = [...selectedRightBarcodes, ...selectedLeftBarcodes].map(x => x.id);
        $("#modal_lens_bids").val(allSelectedBarcodes.join(','));
    
        $('#PrescriptionBtn').prop('disabled', true);
        $('#clearPrescriptionBtn').prop('disabled', true);
        $('#LensBarcodeModal').modal('hide');
    
    });
    
    
    
    function updateTotals() {
        // 1. Get quantities
        let lensLeftNoOfBoxes = parseFloat($("#lensLeftNoOfBoxes").val()) || 0;
        let lensRightNoOfBoxes = parseFloat($("#lensRightNoOfBoxes").val()) || 0;
        let qty = lensLeftNoOfBoxes + lensRightNoOfBoxes;
    
        // 2. Get price, GST, discounts
        let retailPrice = parseFloat($("#modal_retail_price").val()) || 0;
        let gst = parseFloat($("#modal_gst").val()) || 0;
        let discountPercent = parseFloat($("#modal_discount").val()) || 0;
        let discountAmount = parseFloat($("#modal_discound_amount").val()) || 0;
        let purchasePrice = parseFloat($("#modal_purchase_price").val()) || 0;
        let taxRule = '{{$sale->tax_rule}}';
    
        // 3. Base before discount
        let retailPriceTotal = retailPrice * qty;
        let purchasePriceTotal = purchasePrice * qty;
    
        // 4. Apply discount
        let appliedDiscount = 0;
        if (discountPercent > 0) {
            appliedDiscount = retailPriceTotal * (discountPercent / 100);
            $("#modal_discound_amount").val(appliedDiscount.toFixed(2)); // auto-update discount amount
        } else if (discountAmount > 0) {
            appliedDiscount = discountAmount;
        }
    
        let afterDiscount = retailPriceTotal - appliedDiscount;
    
        // 5. Calculate GST and total sale
        let basePrice = 0, gstAmount = 0, totalSale = 0;
    
        if (taxRule === "Include") {
            basePrice = afterDiscount / (1 + (gst / 100));
            gstAmount = afterDiscount - basePrice;
            totalSale = afterDiscount;
        } else if (taxRule === "Exclude") {
            basePrice = afterDiscount;
            gstAmount = basePrice * (gst / 100);
            totalSale = basePrice + gstAmount;
        } else {
            basePrice = afterDiscount;
            gstAmount = 0;
            totalSale = basePrice;
        }
    
        // 6. Update the DOM
        $("#modal_purchase_price").val(purchasePriceTotal.toFixed(2));
        $("#modal_retail_price").val(retailPriceTotal.toFixed(2));
        $("#modal_base_price").val(basePrice.toFixed(2));
        $("#modal_gst_amount").val(gstAmount.toFixed(2));
        $("#modal_total_sale").val(totalSale.toFixed(2));
    }
    $("#lensLeftNoOfBoxes, #lensRightNoOfBoxes").on("keyup", updateTotals);
    
    
      // When second modal opens, keep body locked
      $('#LensBarcodeModal').on('shown.bs.modal', function () {
        $('body').addClass('modal-open');
      });
    
      // When second modal closes
      $('#LensBarcodeModal').on('hidden.bs.modal', function () {
        if ($('#add-new-item-modal').hasClass('show')) {
          // First modal still open → restore backdrop
          $('body').addClass('modal-open');
          $('<div class="modal-backdrop fade show"></div>').appendTo(document.body);
        }
      });
    
      // When first modal closes → FULL CLEANUP
      $('#add-new-item-modal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
      });
      
      
      
      $('#PrescriptionModal').on('shown.bs.modal', function () {
        $('body').addClass('modal-open');
      });
      
      
      // When second modal closes
      $('#PrescriptionModal').on('hidden.bs.modal', function () {
        if ($('#productModal').hasClass('show')) {
          // First modal still open → restore backdrop
          $('body').addClass('modal-open');
          $('<div class="modal-backdrop fade show"></div>').appendTo(document.body);
        }
      });
	  
	  
	  

</script>









@endsection

