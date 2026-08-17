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
                    <div class="col-md-3">
                        <h3>Create Bulk Invoice</h3>
                    </div>
                    <div class="col-md-2">
                        @if ($usr->can('Sales-History'))
                        <a href="{{route('admin.sale-history')}}" class="btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Sales Order List
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
                        <div class="col-md-22">
                            <div class="alert alert-danger ml-0 mr-0">
                                <ul class="mb-0">
                                    <li>All fields marked with * are mandatory.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="saleForm" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Sale Date <span class="text-danger">*</span></label>
                            <div id="reportrangesale" class="pull-left"
                                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                            <input type="hidden" class="form-control" id="sale_date" name="sale_date">
                        </div>
                       
                         <div class="col-md-3">
                            <label for="">Select Store <span class="text-danger">*</span></label>
                            <select class="form-control select" style="height: 32px !important" id="store_id" name="store_id" >
                                <option value="">Select  Store</option>
                              <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                               @foreach($tbl_store as $tbl_store)
                                <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                              @endforeach
                            </select>
                            <span class="error badge text-danger" id="store_idError"></span>
                        </div>
                        <div class="col-md-3">
                            <label for="" class="form-label">Order No: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"  placeholder="Enter Order No" id="order_no" name="order_no" readonly>
                            <span class="error badge text-danger" id="order_noError"></span>
                        </div>
                        <div class="col-md-3">
                            <label for="">Sales Person<span class="text-danger">*</span></label>
                            <select class="form-control select" style="height: 32px !important;" id="sale_person" name="sale_person">
                                <option value="">Select Person</option>
                              <?php  $tbl_users =  DB::table("users")->where('status',1)->get();  ?>
                               @foreach($tbl_users as $tbl_users)
                                <option value="{{$tbl_users->id}}">{{$tbl_users->name}} / ({{$tbl_users->user_type}} : {{$tbl_users->staff_id}})</option>
                              @endforeach
                            </select>
                            <span class="error badge text-danger" id="sale_personError"></span>
                        </div>
                        <div class="col-md-3">
                            <label for="">Tax Rule <span class="text-danger">*</span></label><br>
                            <select class="form-control select" style="height: 32px !important;" id="tax_rule" name="tax_rule">
                              <option value="">Select tax Rule</option>    
                              <option value="Not Applicable">Not Appicable</option>    
                              <option value="Include">Include</option>
                              <option value="Exclude ">Exclude</option>
                            </select>
                            <span class="error badge text-danger" id="tax_ruleError"></span>
                            <input type="hidden" class="form-control"   id="sales_text_per" name="sales_text_per">
                            <input type="hidden" class="form-control"   id="taxrule" name="taxrule">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                             <div class="row">
                                <div class="col-md-12">
                                   <h5><strong>Customer Information</strong></h5> 
                                </div> 
                                <div class="col-md-3">
                                    <label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Contact no" name="contact_no" id="contact_no" maxlength="10"  pattern="^[6-9][0-9]{9}$">
                                    <span class="error badge text-danger" id="contact_noError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Full Name: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter customer name" id="cust_name" name="cust_name" >
                                    <span class="error badge text-danger" id="cust_nameError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Email Id: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Email" name="email_id" id="email_id">
                                    <span class="error badge text-danger" id="email_idError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Customer Category:</label>
                                    <select class="form-control select" name="cust_category" id="cust_category" >
                                        <option value="">Select Category</option>
                                        <option value="WALKOUT">WALKOUT</option>
                                        <option value="REPAIRING">REPAIRING</option>
                                        <option value="EYE TEST">EYE TEST</option>
                                        <option value="GOLD MEMBERSHIP">GOLD MEMBERSHIP</option>
                                    </select>
                                    <span class="error badge text-danger" id="cust_categoryError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Gender : <span class="text-danger">*</span></label>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="gender" id="inlineRadio8" value="Male">
                                      <label class="form-check-label" for="inlineRadio8">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="gender" id="inlineRadio9" value="Female">
                                      <label class="form-check-label" for="inlineRadio9">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="gender" id="inlineRadio10" value="Other">
                                      <label class="form-check-label" for="inlineRadio10">Other</label>
                                    </div>
                                    <span class="error badge text-danger" id="genderError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Address: <span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" placeholder="Enter address" name="cust_address" id="cust_address"></textarea>
                                    <span class="error badge text-danger" id="cust_addressError"></span>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="" class="form-label">State: <span class="text-danger">*</span></label>
                                    <select class="form-control select" name="state_id" id="state_id" >
                                        <option value="" disabled selected>Select State</option>
                                    </select>
                                    <span class="error badge text-danger" id="state_idError"></span>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="" class="form-label">City: <span class="text-danger">*</span></label>
                                    <select class="form-control select" name="city_id" id="city_id" >
                                        <option value="" disabled selected>Select City</option>
                                    </select>
                                    <span class="error badge text-danger" id="city_idError"></span>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="" class="form-label">Pincode: <span class="text-danger">*</span></label>
                                    <input type="text" maxlength="7" class="form-control" placeholder="Enter Pincode" name="pincode" id="pincode">
                                    <span class="error badge text-danger" id="pincodeError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Date of Birth: </label>
                                     <div id="reportrange" class="pull-left"
                                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100% !important;border-radius: 8px;">
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                        <span></span> <b class="caret"></b>
                                    </div>
                                    <input type="hidden" class="form-control" id="date_from" name="date_from">
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Date of Anniversary: </label>
                                    <div id="reportrange1" class="pull-left"
                                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 100% !important;border-radius: 8px;">
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                        <span></span> <b class="caret"></b>
                                    </div>
                                    <input type="hidden" class="form-control" id="date_from1" name="date_from1">
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">GST No: </label>
                                    <input type="text" class="form-control" placeholder="Enter GST No" id="cust_gst" name="cust_gst" >
                                    <span class="error badge text-danger" id="cust_gstError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Company Name: </label>
                                    <input type="text" class="form-control" placeholder="Enter Company Name" id="cust_company_name" name="cust_company_name" >
                                    <span class="error badge text-danger" id="company_nameError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="form-label">Customer Notes: </label>
                                    <textarea type="text"  class="form-control" placeholder="Customer Notes" name="cust_note" id="cust_note"></textarea>
                                    <span class="error badge text-danger" id="cust_noteError"></span>
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
                                                <th style="width: 14%;">Product</th>
                                                <th>Product Description</th>
                                                <th style="width: 6%;">Qty</th>
                                                <th style="width: 9%;" class="tax-col">HSN/SAC Code</th>
                                                <th style="width: 7%;" class="tax-col">GST %</th>
                                                <th style="width: 13%;">Item Discount</th>
                                                <th style="width: 9%;">Price</th>
                                                <th style="width: 4%;">--</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                
                                                <td>
                                                    <select class="form-control product-type" style="height: 32px !important;" name="product_type[]">
                                                        <option value="">Select Product</option>
                                                        <option value="Frame">Frame</option>
                                                        <option value="Goggles">Goggles</option>
                                                        <option value="Solution">Solution</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                    <input type="hidden" class="form-control product-code" name="product_code[]">
                                                    <input type="hidden" class="form-control product-id" name="product_id[]">
                                                    <input type="hidden" class="form-control product-company" name="product_company[]">
                                                    <input type="hidden" class="form-control product-quality" name="product_quality[]">
                                                    <input type="hidden" class="form-control product-color" name="product_color[]">
                                                    <input type="hidden" class="form-control product-typesss" name="product_typesss[]">
                                                    <input type="hidden" class="form-control product-variant" name="product_variant[]">
                                                    <input type="hidden" class="form-control product-shape" name="product_shape[]">
                                                    <input type="hidden" class="form-control product-size" name="product_size[]">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control product-description" name="product_description[]" placeholder="Enter Product Description" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control product-qty" name="product_qty[]" value="0" readonly>
                                                </td>
                                                <td class="tax-col">
                                                    <input type="text" class="form-control hsn-code" name="hsn_code[]">
                                                </td>
                                                <td class="tax-col">
                                                    <input type="text" class="form-control gst-per" name="gst[]">
                                                    <input type="hidden" class="form-control gst-amount" name="gst_amount[]" value="0.00">
                                                </td>
                                                <td>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control discount-amount" name="discount_amt[]" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                                                        <input type="text" class="form-control discount" name="discount[]" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="hidden" class="form-control purchase-price" name="purchase_price[]" value="0.00" placeholder="0.00">
                                                    <input type="hidden" class="form-control base-price" name="base_price[]" value="0.00" placeholder="0.00">
                                                    <input type="hidden" class="form-control retail-price" name="retail_price[]" value="0.00" placeholder="0.00">
                                                    <input type="text" class="form-control sale-price" name="sale_price[]" value="0.00" placeholder="0.00">
                                                </td>
                                                <td><button type="button" class="removeBtn">❌</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group col-md-12">
                                    <button type="button" id="addRowBtn">➕ Add Row</button>
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
                                                            <div class="row" id="totalbasicdiv">
                                                                <div class="col-md-4">
                                                                    <label for="">Total Basic Amount </label>
                                                                    <input type="text" class="form-control" name="total_basic_amount" id="total_basic_amount" value="0.00" readonly>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="">Total GST Amount </label>
                                                                    <input type="text" class="form-control" name="total_gst_amount" id="total_gst_amount" value="0.00" readonly>
                                                                </div>
                                                            </div>
                                                            <br>
                                                
                                                            <!-- Payment Details -->
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h5>Payment Method <span class="text-danger">*</span></h5>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="d-flex">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="pay_method" id="pay_cash" value="cash">
                                                                            <label class="form-check-label" for="pay_cash">Cash</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="pay_method" id="pay_upi" value="upi">
                                                                            <label class="form-check-label" for="pay_upi">UPI</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="">Pay Amount <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" placeholder="Enter Amount" name="pay_amount" id="pay_amount">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="">Pay Details </label>
                                                                        <input type="text" class="form-control" placeholder="Enter Details" name="pay_deatils" id="pay_deatils">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label for="">Pending Amount <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" value="0.00" name="pending_amount" id="pending_amount" readonly>
                                                                    </div>
                                                                </div>
                                                                
                                                                
                                                                
                                                            </div>
                                                            <br>
                                                            <div class="row">
                                                                     <div class="col-md-3" id="addcreditBtn">
                                                                        <button class="btn btn-primary" type="button" id="creditBtn">Allow Customer Account</button>
                                                                    </div>
                                                                    <div class="col-md-3" style="display:none" id="removecreditBtn">
                                                                        <button class="btn btn-danger" type="button" id="removecreditDisc">Remove Customer Account</button>
                                                                    </div>
                                                                </div>

                                                            
                                                        </td>
                                
                                                        <!-- Right Summary -->
                                                        <td colspan="2">
                                                            <table class="table table-borderless table-sm mb-0 w-100">
                                                                <tr>
                                                                    <td class="text-end">Total item price : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="total_item_price" id="total_item_price" value="0.00" readonly></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Total discount : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="total_discount" id="total_discount" value="0.00" readonly></td>
                                                                </tr>
                                                               
                                                                <tr>
                                                                    <td class="text-end">Round Off : Rs  (+/-)	</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="roundoff" id="roundoff" value="0.00"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Total payable : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="total_payable" id="total_payable" value="0.00" readonly></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Customer Account : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="customer_account" id="customer_account" value="0.00" readonly></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Advance Amount : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="advance_amount" id="advance_amount" value="0.00" readonly></td>
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
                    <button type="submit" class="btn btn-primary loaderbtn">Submit</button>
                </form>    
            </div>
        </div>    
    </div>
</section>


<!--Main Product Modal -->

<div class="modal fade" id="productModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Product Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="stock_msg" class="text-danger mt-1"></div>
                <div class="row" style="margin-top:10px">
                    <div class="col-md-3">
                        <label>Product</label>
                        <input type="text" id="modal_product_type" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="codediv" style="display:none">
                        <label>Product Code</label>
                        <input type="text" id="modal_product_code" class="form-control" readonly>
                         <div class="suggestion-box list-group" style="display:none; position:absolute; z-index:1000;"></div>
                    </div>
                    <div class="col-md-3" id="iddiv" style="display:none">
                        <label>Product ID</label>
                        <input type="text" id="modal_product_id" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="companydiv">
                        <label>Company </label>
                        <input type="text" id="modal_company" class="form-control" readonly>
                    </div>
                </div> 
                <div class="row" style="margin-top:10px">
                    <div class="col-md-3" id="qualitydiv" style="display:none">
                        <label>Quality </label>
                        <input type="text" id="modal_quality" class="form-control" readonly>
                    </div>
                     <div class="col-md-6">
                        <label>Product Detail</label>
                        <textarea type="text" id="modal_product_details" class="form-control" readonly></textarea>
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-3" id="colordiv" style="display:none">
                        <label>Color</label>
                        <input type="text" id="modal_product_color" class="form-control" readonly>
                    </div>

                    <div class="col-md-3" id="typediv" style="display:none">
                        <label>Type   </label>
                        <input type="text" id="modal_product_typesss" class="form-control" readonly>
                    </div>

                    <div class="col-md-3" id="shapediv" style="display:none">
                        <label>Shape   </label>
                        <input type="text" id="modal_product_shape" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="sizediv" style="display:none">
                        <label>Size   </label>
                        <input type="text" id="modal_product_size" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="variantdiv" style="display:none">
                        <label>Variant   </label>
                        <input type="text" id="modal_product_variant" class="form-control" readonly>
                    </div>
                     <div class="col-md-3" id="packingtypediv" style="display:none">
                        <label>Packing Type   </label>
                        <input type="text" id="modal_packing_type" class="form-control" readonly>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label>Quantity </label>
                        <input type="text" id="modal_quantity"   value="1" class="form-control">
                    </div>
                    <div class="col-md-3" id="hsncodediv" style="display:none">
                        <label>HSN/SAC Code </label>
                        <input type="text" id="modal_hsncode"  class="form-control">
                    </div>
                    <div class="col-md-3" id="gstdiv" style="display:none">
                        <label>GST % </label>
                        <input type="number" id="modal_gst"  class="form-control">
                    </div>
                    <div class="col-md-3" id="taxdiv" style="display:none">
                        <label>Tax Rule</label>
                        <input type="text" id="modal_tax_rule"  class="form-control" readonly>
                    </div>
                </div>
                <div class="row mt-3">
                
                    <div class="col-md-3" id="purchasediv" style="display:none">
                        <label>Purchase Price </label>
                        <input type="text" id="modal_purchase_price"  class="form-control">
                    </div>
                     <div class="col-md-3">
                        <label>Retail Price </label>
                        <input type="text" id="modal_retail_price"  class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Discount  </label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="modal_discount" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                            <input type="text" class="form-control" id="modal_discount_amount" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Base Price </label>
                        <input type="text" id="modal_base_price"  class="form-control" readonly>
                    </div>
                </div>    
                <div class="row mt-3">    
                    <div class="col-md-3" id="gstamtdiv" style="display:none">
                        <label>GST Amount </label>
                        <input type="text" id="modal_gst_amount"  class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Total Sales Price </label>
                        <input type="text" id="modal_total_sale"  class="form-control" readonly>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary addmodalbtn">Add</button>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div> 


<!--Credit Apply Modal -->
<div class="modal fade" id="creditModal" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Customer Account Credit </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="couponForm">
          <div class="row">
            <label>Wallet Balnce </label>
            <input type="text" class="form-control" id="wallet_bal" readonly >
          </div><br>    
          <div class="row">
            <label>Amount </label>
            <input type="text" class="form-control" id="credit_amount" >
          </div><br>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmCredit">Apply</button>
      </div>

    </div>
  </div>
</div>



@endsection

@section('scripts')
<script>
$(document).ready(function () 
{
    /* =======================
       First Date + Time Picker
    ======================= */
    function cb(date) {
        $('#reportrange span').html(date.format('MMMM D, YYYY h:mm A'));
        $('#date_from').val(date.format('YYYY-MM-DD HH:mm:ss'));
    }
    
    $('#reportrange').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: false,     // set true for 24-hour format
        timePickerIncrement: 1,
        autoUpdateInput: false,
        locale: {
            format: 'MMMM D, YYYY h:mm A'
        }
    }, cb);
    
    
    /* =======================
       Default Sale Date + Time
    ======================= */
    let selectedDate = moment();
    
    $('#reportrangesale span').html(
        selectedDate.format('MMMM D, YYYY h:mm A')
    );
    
    $('#sale_date').val(
        selectedDate.format('YYYY-MM-DD HH:mm:ss')
    );
    
     /* -------------------------
    Select2 & Mobile Validation
    ------------------------- */
    $('.select1').select2({ allowClear:true, width:'22%' });
    $('.select').select2({ allowClear:true, width:'100%' });

    var mobileFields = ['contact_no', 'bb_mobile_no'];
    var pattern = /^[6-9][0-9]{0,9}$/;
    mobileFields.forEach(function (id) {
        var input = document.getElementById(id);
        if (!input) return;
        var lastValid = '';
        input.addEventListener('input', function () {
            if (pattern.test(this.value)) {
                lastValid = this.value;
            } else {
                this.value = lastValid;
            }
        });
    });
    
    /* -------------------------
    Get Customer Details
    ------------------------- */
    let debounceTimer;
    $('#contact_no').on('change', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            let contactNo = $('#contact_no').val().trim();
    
            if (!/^[6-9]\d{9}$/.test(contactNo)) {
                $('#contactError').text("Enter valid 10-digit mobile number.");
                return;
            } else {
                $('#contactError').text("");
            }
    
            $.ajax({
                url: "{{ route('admin.getcustomer') }}",
                method: "GET",
                data: { contact_no: contactNo },
                success: function (response) {
                    if (response.success) {
                        let data = response.data;
    
                        $('#cust_name').val(data.cust_name);
                        $('#email_id').val(data.email_id);
                        $('#cust_category').val(data.cust_category).trigger('change');
                        $('#cust_address').val(data.cust_address);
                        $('#pincode').val(data.pincode);
                        $('#cust_note').val(data.cust_note);
    
                        $("input[name='gender'][value='" + data.gender + "']").prop("checked", true);
    
                        if (data.dob) {
                            let dob = moment(data.dob, 'YYYY-MM-DD');
                            $('#date_from').val(dob.format('YYYY-MM-DD'));
                            $('#reportrange span').html(dob.format('MMMM D, YYYY'));
                            $('#reportrange').data('daterangepicker').setStartDate(dob).setEndDate(dob);
                        }
    
                        if (data.doa) {
                            let ann = moment(data.doa, 'YYYY-MM-DD');
                            $('#date_from1').val(ann.format('YYYY-MM-DD'));
                            $('#reportrange1 span').html(ann.format('MMMM D, YYYY'));
                            $('#reportrange1').data('daterangepicker').setStartDate(ann).setEndDate(ann);
                        }
    
                        if (data.state_id) {
                            loadStates(data.state_id, data.city_id);
                        }
    
                    } else {
                        $.toaster({ priority: 'danger', title: 'Error', message: response.message,
                       timeout: 3000 });
                    }
                },
                error: function () {
                    $.toaster({ priority: 'danger', title: 'Error', message: 'Error fetching customer data.',
                    timeout: 3000 });
                }
            });
        }, 500);
    });
    
    /* -------------------------
    Load State
    ------------------------- */
    function loadStates(selectedState, selectedCity)
    {
        $.ajax({
            url: "{{ route('get-state') }}",
            method: "GET",
            success: function (data) {
                let stateSelect = $('#state_id');
                stateSelect.empty().append('<option value="" disabled selected>Select State</option>');
                $.each(data, (key, value) => {
                    stateSelect.append(`<option value="${value.id}">${value.name}</option>`);
                });

                if (selectedState) {
                    $('#state_id').val(selectedState).trigger('change');
                    if (selectedCity) {
                        $('#city_id').data('selected', selectedCity);
                    }
                }
            }
        });
    }
    
    $('#state_id').on('change', function () {
        const stateId = $(this).val();
        $('#city_id').empty().append('<option value="" disabled selected>Loading...</option>');
    
        if (stateId) {
            $.ajax({
                url: "{{ route('get-city-by-state') }}",
                method: "GET",
                data: { state_id: stateId },
                success: function (data) {
                    let citySelect = $('#city_id');
                    citySelect.empty().append('<option value="" disabled selected>Select City</option>');
                    $.each(data, (key, value) => {
                        citySelect.append(`<option value="${value.id}">${value.name}</option>`);
                    });

                    let selectedCity = $('#city_id').data('selected');
                    if (selectedCity) {
                        $('#city_id').val(selectedCity).trigger('change');
                        $('#city_id').removeData('selected');
                    }
                },
                error: function () {
                    $('#city_id').html('<option value="" disabled selected>No city found</option>');
                }
            });
        } else {
            $('#city_id').html('<option value="" disabled selected>Select City</option>');
        }
    });

    loadStates();
    
        /** ==============================
     *  Table row handling
     *  ============================== */
    let activeRow = null;

    const addRowBtn = document.getElementById("addRowBtn");
    const tableBody = document.querySelector("#saleTable tbody");
    
    // ----------------- SERIAL NUMBERS -----------------
    function updateSerialNumbers() {
        tableBody.querySelectorAll("tr").forEach((row, index) => {
            row.cells[0].textContent = index + 1;
    
            const removeBtn = row.querySelector(".removeBtn");
            if (removeBtn) {
                removeBtn.style.display = (index === 0) ? "none" : "inline-block";
            }
        });
    }
    
    // ----------------- VALIDATE LAST ROW -----------------
    function validateLastRow() {
        const lastRow = tableBody.querySelector("tr:last-child");
        let isValid = true;
    
        if (!lastRow) return true;
    
        const productType = lastRow.querySelector("select.product-type");
        const productDescription = lastRow.querySelector("input.product-description");
        const salePrice = lastRow.querySelector("input.sale-price");
    
        if (!productType.value.trim()) {
            productType.classList.add("error");
            isValid = false;
        } else productType.classList.remove("error");
    
        if (!productDescription.value.trim()) {
            productDescription.classList.add("error");
            isValid = false;
        } else productDescription.classList.remove("error");
    
        if (!salePrice.value.trim() || parseFloat(salePrice.value) <= 0) {
            salePrice.classList.add("error");
            isValid = false;
        } else salePrice.classList.remove("error");
    
        return isValid;
    }
    
    // ----------------- ADD ROW -----------------
    addRowBtn.addEventListener("click", function () {
    
        if (!validateLastRow()) {
            $.toaster({
                priority: 'danger',
                title: ' Please fill all required fields in the last row before adding a new one.',
                message: ''
            });
            return;
        }
    
        const newRow = document.createElement("tr");
    
        newRow.innerHTML = `
            <td></td>
            <td>
                <select class="form-control product-type" style="height: 32px !important;" name="product_type[]">
                    <option value="">Select Product</option>
                    <option value="Frame">Frame</option>
                    <option value="Goggles">Goggles</option>
                    <option value="Solution">Solution</option>
                    <option value="Other">Other</option>
                </select>
                <input type="hidden" class="form-control product-code" name="product_code[]">
                <input type="hidden" class="form-control product-id" name="product_id[]">
                <input type="hidden" class="form-control product-company" name="product_company[]">
                <input type="hidden" class="form-control product-quality" name="product_quality[]">
                <input type="hidden" class="form-control product-color" name="product_color[]">
                <input type="hidden" class="form-control product-typesss" name="product_typesss[]">
                <input type="hidden" class="form-control product-variant" name="product_variant[]">
                <input type="hidden" class="form-control product-shape" name="product_shape[]">
                <input type="hidden" class="form-control product-size" name="product_size[]">

            </td>
            <td>
                <input type="text" class="form-control product-description" name="product_description[]" placeholder="Enter Product Description" readonly>

            </td>
            <td>
                <input type="text" class="form-control product-qty" name="product_qty[]" value="0" readonly>
            </td>
            <td class="tax-col">
                <input type="text" class="form-control hsn-code" name="hsn_code[]">
            </td>
            <td class="tax-col">
                <input type="text" class="form-control gst-per" name="gst[]">
                <input type="hidden" class="form-control gst-amount" name="gst_amount[]" value="0.00">
            </td>
            <td>
                <div class="input-group mb-3">
                    <input type="text" class="form-control discount-amount" name="discount_amt[]" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                    <input type="text" class="form-control discount" name="discount[]" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                </div>
            </td>
            <td>
                <input type="hidden" class="form-control purchase-price" name="purchase_price[]" value="0.00" placeholder="0.00">
                <input type="hidden" class="form-control base-price" name="base_price[]" value="0.00" placeholder="0.00">
                <input type="hidden" class="form-control retail-price" name="retail_price[]" value="0.00" placeholder="0.00">
                <input type="text" class="form-control sale-price" name="sale_price[]" value="0.00" placeholder="0.00">
            </td>
    
            <td><button type="button" class="removeBtn">X</button></td>
        `;
    
        tableBody.appendChild(newRow);
        updateSerialNumbers();
    });
    
    // ----------------- REMOVE ROW -----------------
    $(document).on("click", ".removeBtn", function () {
        $(this).closest("tr").remove();
        updateSerialNumbers();
        recalcTotals();
    });
    
    // ----------------- SET ACTIVE ROW ON CLICK -----------------
    $(document).on("click", "#saleTable tbody tr", function () {
        $("#saleTable tbody tr").removeClass("active");
        $(this).addClass("active");
        activeRow = $(this);
    });
    
    updateSerialNumbers();
    
    
    /** ==============================
     *  Product Type Wise Modal Div Open
     *  ============================== */
     
    $(document).on("change", ".product-type", function () {

        activeRow = $(this).closest("tr");   
    
        let selectedType = $(this).val();
        let store_id = $("#store_id").val();
        let tax_rule = $("#tax_rule").val();
        
        if (store_id == '') {
            $.toaster({
                priority: 'danger',
                title: ' error',
                message: 'Select Store',
                timeout: 3000
            });
            activeRow.find(".product-type").val("");

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
    
                $("#modal_product_type").val(selectedType);
                $("#modal_tax_rule").val(tax_rule);
                handleProductType(selectedType,store_id);
                $("#modalTitle").text("Add " + selectedType + " Details");
                $("#productModal").modal("show");
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
                        $("#modal_product_details").prop("readonly", true);
                        
                        switch (type) 
                        {
                            case "Frame":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                $("#qtydiv, #purchasediv").show();
                                $("#modal_product_code").prop("readonly", true);
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                break;
                            
                             case "Goggles":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                break;
                                
                            case "Solution":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#colordiv,#packingtypediv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                break;    

                            case "Other":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#shapediv, #colordiv, #sizediv, #typediv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
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
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    break;
                                
                                 case "Goggles":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    break;    
                                    
                                case "Solution":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#colordiv,#packingtypediv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    break;
                        
                                case "Other":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#shapediv, #colordiv, #sizediv, #typediv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
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
                    $("#order_no").val(''); // Clear input if store not found
                }
            }
        });
    
    }
    
    
    
        /** ==============================
     *  Product Code Wise Product Details
     *  ============================== */
    $(document).on('keyup', '#modal_product_code', function () {
        let $input = $(this);
        let productCode = $input.val();
        let productType = $("#modal_product_type").val();
    
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
        let productType = $("#modal_product_type").val();
        let tax_rule = $("#tax_rule").val();
        
        let rightLeft = [];
        $('input[name="modal_rightleft"]:checked').each(function () {
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
                    $("#modal_product_type").val(res.data.product_type);
                    $("#modal_product_details").val(res.data.product_details);
                    $("#modal_company").val(res.data.product_company);
                    $("#modal_quality").val(res.data.product_quality);
                    $("#modal_product_color").val(res.data.product_color);
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
    
    
    $('#modal_quantity').on('input', function () {
        let qty = $(this).val();
        let product_code = $('#modal_product_code').val();
        let store_id = $('#store_id').val();
    
        if (qty <= 0 || product_code === '') {
            return;
        }
    
        $.ajax({
            url: "{{ route('admin.check.inventory') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                product_code: product_code,
                quantity: qty,
                store_id: store_id
            },
            success: function (response) {
                if (response.status === false) {
                    $('#stock_msg').text(response.message);
                    $('#modal_quantity').val('');
                } else {
                    $('#stock_msg').text('');
                }
            }
        });
    });
    
    
        
    /** =================================
     *  Modal Data Add In Table Row
     * ================================== */
    
    $(".addmodalbtn").click(function () {

        let qty = parseFloat($("#modal_quantity").val()) || 0;
        let retailPrice = parseFloat($("#modal_retail_price").val()) || 0;
        let totalSale = parseFloat($("#modal_total_sale").val()) || 0;
    
        if (!$("#modal_product_details").val()) {
            alert('Product Details required');
            return;
        }
        if (retailPrice <= 0) {
            alert('Retail Price required');
            return;
        }
        if (totalSale <= 0) {
            alert('Total Sale required');
            return;
        }
        if (qty <= 0) {
            alert('Quantity required');
            return;
        }
    
        if (!activeRow || activeRow.length === 0) {
            alert("No row selected!");
            return;
        }
    
        let row = activeRow;
    
        // BASIC FIELDS
        row.find(".product-id").val($("#modal_product_id").val());
        row.find(".product-type").val($("#modal_product_type").val());
        row.find(".product-code").val($("#modal_product_code").val());
        row.find(".product-description").val($("#modal_product_details").val());
        row.find(".product-company").val($("#modal_company").val());
        row.find(".product-quality").val($("#modal_quality").val());
        row.find(".product-color").val($("#modal_product_color").val());
        row.find(".product-validity").val($("#modal_product_validity").val());
        row.find(".product-variant").val($("#modal_product_variant").val());
        row.find(".product-shape").val($("#modal_product_shape").val());
        row.find(".product-size").val($("#modal_product_size").val());
        row.find(".product-qty").val(qty);
        row.find(".hsn-code").val($("#modal_hsncode").val());
        row.find(".gst-per").val($("#modal_gst").val());
        row.find(".discount").val($("#modal_discount").val());
    
        // PRICES (converted to numbers)
        let gstAmount = parseFloat($("#modal_gst_amount").val()) || 0;
        let basePrice = parseFloat($("#modal_base_price").val()) || 0;
        let discountAmount = parseFloat($("#modal_discount_amount").val()) || 0;
        let purchasePrice = parseFloat($("#modal_purchase_price").val()) || 0;
    
        row.find(".gst-amount").val(gstAmount * qty);
        row.find(".base-price").val(basePrice * qty);
        row.find(".retail-price").val(retailPrice * qty);
        row.find(".sale-price").val(totalSale * qty);
        row.find(".purchase-price").val(purchasePrice);
        row.find(".discount-amount").val(discountAmount * qty);
    
        // CLOSE MODAL
        $("#productModal").modal("hide");
        $("#productModal")
            .find("input:not([type=radio]):not([type=checkbox]), textarea, select")
            .val("");
    
        recalcTotals();
    });



    
    $(document).on("focus", ".product-type", function ()
    {
        $("#saleTable tbody tr").removeClass("active");
        $(this).closest("tr").addClass("active");
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
        let taxRule = $("#tax_rule").val();

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
    

    /* -------------------------
       Pay Amount  Pending Calculation
    ------------------------- */
    $(document).on("keyup", "#pay_amount", function () {
        // Get numeric values, defaulting to 0 if empty or invalid
        let totalPayable = parseFloat($("#total_payable").val()) || 0;
        let customer_account = parseFloat($("#customer_account").val()) || 0;
        let payAmount = parseFloat($(this).val()) || 0;
    
        // Calculate pending amount, never less than 0
        let pending = totalPayable - (payAmount + customer_account);

        // Update fields with 2 decimal places
        $("#pending_amount").val(pending.toFixed(2));
        $("#advance_amount").val((payAmount + customer_account).toFixed(2));
    });
    
    
        function parseNum(val) {
        val = parseFloat(val);
        return isNaN(val) ? 0 : val;
    }
        
    
    /** =================================
     *  Calculate Table Price
     * ================================== */
    
    function recalcTotals() {
        let totalBasic = 0,
            totalGst = 0,
            totalSale = 0,
            totalDis = 0;
    
        $(".base-price, .gst-amount, .sale-price, .discount-amount").each(function () {
            let currentVal = $(this).val();
            if (!currentVal || currentVal === '') {
                $(this).val($(this).attr("value") || 0);
            }
        });
    
        $(".base-price").each(function () {
            totalBasic += parseNum($(this).val());
        });
    
        $(".gst-amount").each(function () {
            totalGst += parseNum($(this).val());
        });
    
        $(".sale-price").each(function () {
            totalSale += parseNum($(this).val());
        });
    
        $(".discount-amount").each(function () {
            totalDis += parseNum($(this).val());
        });
    
        let roundoff      = parseNum($("#roundoff").val()); // 👈 added
    
        let totalPayable =
            totalSale
            - totalDis
            + roundoff; // 👈 added
    
        $("#total_basic_amount").val(totalBasic.toFixed(2));
        $("#total_gst_amount").val(totalGst.toFixed(2));
        $("#total_item_price").val(totalSale.toFixed(2));
        $("#total_discount").val(totalDis.toFixed(2));
        $("#total_payable").val(totalPayable.toFixed(2));
        $("#pending_amount").val(totalPayable.toFixed(2));
    }
    
    $("#roundoff").on("input", function () {
        recalcTotals();
    });
    
    
        /** ==============================
     * Store Wise Tax or order id get
     *  ============================== */
     
    $(document).on("change", "#store_id", function () {
        let selectedType = $(this).val();
    
        $.ajax({
            url: "{{ route('admin.get-store-details') }}",
            type: "GET",
            data: { selectedType: selectedType },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
            success: function (res) {
            if (res.success)
            {
                $("#order_no").val(res.data.order_no);
        
                if(res.data.sales_tax_type == 0) {
                    $("#saleTable .tax-col").hide();
                    $("#totalbasicdiv").hide();
        
                    // Set tax_rule value AND trigger change event
                    $("#tax_rule").val("Not Applicable").trigger("change");
                    $("#taxrule").val("Not Applicable");
                }
                else 
                {
                    $("#saleTable .tax-col").show();
                    $("#totalbasicdiv").show();
                    
                    if(res.data.tax_rule == 1)
                    {
                       $("#tax_rule").val("Include").trigger("change");
                       $("#taxrule").val("Include");
                    }
                    else
                    {
                        $("#tax_rule").val("Exclude").trigger("change");
                        $("#taxrule").val("Exclude");
                    }
                    
                    $("#sales_text_per").val(res.data.sales_text_per);
                     $("#tax_rule").prop("disabled", true);
                }
                
                
                
               
            } else {
                $.toaster({
                    priority: 'danger',
                    title: ' Store Not Found',
                    message: 'Please check and try again.',
                    timeout: 3000
                });
                $("#order_no").val(''); // Clear input if store not found
            }
        },

            
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
        });
    });
    
    
        
    $('#creditBtn').on('click', function() 
    {
        let contact_no = $('#contact_no').val().trim();
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
            url: "{{ route('admin.getcustomer') }}",
            method: "GET",
            data: { contact_no: contact_no },
            success: function (response) {
                if (response.success) {
                    let data = response.data;
    
                     $('#wallet_bal').val(data.credit_amount|| 0);
                     $('#creditModal').modal('show');
    
                } else {
                    $.toaster({ priority: 'danger', title: 'Error', message: response.message,
                   timeout: 3000 });
                }
            },
            error: function () {
                $.toaster({ priority: 'danger', title: 'Error', message: 'Error fetching customer data.',
                timeout: 3000 });
            }
        });
        
         
    });
    
    
    
    $('#confirmCredit').on('click', function() {
        // Convert all input values to numbers
        let pending_amount = parseFloat($('#pending_amount').val().trim()) || 0;
        let credit_amount = parseFloat($('#credit_amount').val().trim()) || 0;
        let pay_amount = parseFloat($('#pay_amount').val().trim()) || 0;
        let total_payable = parseFloat($('#total_payable').val().trim()) || 0;
        let wallet_bal = parseFloat($('#wallet_bal').val().trim()) || 0;
    
        if (credit_amount === 0) {
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Please enter a credit amount.',
                timeout: 3000
            });
            return;
        }
        
        
    
        if (wallet_bal > pending_amount) {
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Amount cannot be more than pending amount or wallet bal.',
                timeout: 3000
            });
            return;
        }
    
        $('#pending_amount').val(total_payable - (pay_amount + credit_amount));
        $('#advance_amount').val(pay_amount + credit_amount);
        $('#customer_account').val(credit_amount);
        $('#creditModal').modal('hide');
        $('#addcreditBtn').hide();
        $('#removecreditBtn').show();
    });
    
    
    $('#removecreditDisc').on('click', function() {
        // Convert values to numbers
        let pending_amount = parseFloat($('#pending_amount').val().trim()) || 0;
        let credit_amount = parseFloat($('#credit_amount').val().trim()) || 0;
    
        $.toaster({
            priority: "success",
            title: "Success",
            message: "Redeem point removed successfully.",
            timeout: 3000
        });
    
        $('#addcreditBtn').show();
        $('#removecreditBtn').hide();
        $('#customer_account').val('');
        $('#advance_amount').val(pending_amount - credit_amount);
        $('#pending_amount').val(pending_amount + credit_amount); // use +, not -
    });
    
    
    
     /** =================================
     *  Sales Form Submit
     * ================================== */
     
    $("#saleForm").submit(function(e)
    {
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        
        let order_no = $("#order_no").val().trim();
        let cust_name = document.getElementById("cust_name" + class_name).value.trim();
        let contact_no = document.getElementById("contact_no" + class_name).value.trim();
        let email_id = document.getElementById("email_id" + class_name).value.trim();
        let cust_address = document.getElementById("cust_address" + class_name).value.trim();
        let pincode = document.getElementById("pincode" + class_name).value.trim();
        let state_id = document.getElementById("state_id" + class_name).value.trim();
        let city_id = document.getElementById("city_id" + class_name).value.trim();
        let cust_category = document.getElementById("cust_category" + class_name).value.trim();
        let sale_person = document.getElementById("sale_person" + class_name).value.trim();
        let tax_rule = document.getElementById("tax_rule" + class_name).value.trim();

        if (order_no === "") {
            $("#order_noError").text("Order number is required.");
            $("#order_no").addClass("is-invalid");
            isValid = false;
        } else if (!/^[a-zA-Z0-9\-]+$/.test(order_no)) {
            $("#order_noError").text("Order number can only contain letters, numbers, and dashes.");
            $("#order_no").addClass("is-invalid");
            isValid = false;
        } else {
            $("#order_noError").text("");
            $("#order_no").removeClass("is-invalid");
        }
    
        if (cust_name === "") {
            document.getElementById("cust_nameError" + class_name).textContent = "Customer Name Required.";
            document.getElementById("cust_name" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (tax_rule === "") {
            document.getElementById("tax_ruleError" + class_name).textContent = "Tax rule Required.";
            document.getElementById("tax_rule" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (!/^\d{10}$/.test(contact_no)) {
            document.getElementById("contact_noError" + class_name).textContent = "Contact must be a 10-digit number.";
            document.getElementById("contact_no" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (!/^\S+@\S+\.\S+$/.test(email_id)) {
            document.getElementById("email_idError" + class_name).textContent = "Please enter a valid email.";
            document.getElementById("email_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (cust_address === "") {
            document.getElementById("cust_addressError" + class_name).textContent = "Address is required.";
            document.getElementById("cust_address" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (!/^\d{6}$/.test(pincode)) 
        { 
            document.getElementById("pincodeError" + class_name).textContent = "Pincode must be exactly 6 digits.";
            document.getElementById("pincode" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (state_id === "") {
            document.getElementById("state_idError" + class_name).textContent = "State is required.";
            document.getElementById("state_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (city_id === "") {
            document.getElementById("city_idError" + class_name).textContent = "City is required.";
            document.getElementById("city_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (cust_category === "") {
            document.getElementById("cust_categoryError" + class_name).textContent = "Category is required.";
            document.getElementById("cust_category" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (sale_person   === "")
        {
            document.getElementById("sale_personError" + class_name).textContent = "Select sales person.";
            document.getElementById("sale_person" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if ($("input[name='gender']:checked").length === 0) {
            $("#genderError").text("Please select gender.");
            isValid = false;
        } else {
            $("#genderError").text("");
        }
        
    
        
        if ($("input[name='pay_method']:checked").length === 0) {
            $("#pay_cash").closest(".d-flex").after(
                '<span class="text-danger error" id="payMethodError">Please select a payment method.</span>'
            );
            isValid = false;
        } else {
            $("#payMethodError").remove();
        }
        

        
        let payAmount = parseFloat($("#pay_amount").val()) || 0;
        let totalPayable = parseFloat($("#total_payable").val()) || 0;
        
        if (payAmount <= 0) {
            $("#pay_amount").addClass("is-invalid");
            if ($("#payAmountError").length === 0) {
                $("#pay_amount").after('<span class="text-danger error" id="payAmountError">Please enter a valid pay amount.</span>');
            }
            isValid = false;
        } else if (payAmount > totalPayable) {
            $("#pay_amount").addClass("is-invalid");
            if ($("#payAmountError").length === 0) {
                $("#pay_amount").after('<span class="text-danger error" id="payAmountError">Pay amount cannot be greater than total payable.</span>');
            }
            isValid = false;
        } else {
            $("#payAmountError").remove();
            $("#pay_amount").removeClass("is-invalid");
        }
        
        let form = $("#saleForm")[0];
        let data = new FormData(form);
        
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.bulk-sale-record') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
                if ($.isEmptyObject(response.error)) 
                {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: '',
                        timeout: 3000
                    });
                    
         
                    window.location.href = "{{ route('admin.sale-history') }}";
                    
                    
                }
                else 
                {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                    $.each(response.error, function(index, value) {
                        
                    });
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    });

    
    
});

</script>

@endsection

