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
                        <h3>B2B Create New Invoice</h3>
                    </div>
                   
                    <div class="col-md-2">
                        @if ($usr->can('Sales-History'))
                        <a href="{{route('admin.bb-sales-history')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                           B2B Sales Order List
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
                            <label for="">Create Invoice For </label>
                            <div class="d-flex">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="invoice_for" id="inlineRadio1" value="1" checked>
                                  <label class="form-check-label" for="inlineRadio1">Internal Branch</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="invoice_for" id="inlineRadio2" value="0">
                                  <label class="form-check-label" for="inlineRadio2">B2B Customer</label>
                                </div>
                            </div>
                            <span class="error badge text-danger" id="Allow_Negative_InventoryError"></span>
                        </div>
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
                            <label for="">Delivery  Date <span class="text-danger">*</span></label>
                            <div id="reportrangesale1" class="pull-left"
                                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                            <input type="hidden" class="form-control" id="delivery_date" name="delivery_date">
                        </div>
                        <div class="col-md-3">
                            <label for="" class="form-label">Order No: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"  placeholder="Enter Order No" id="order_no" name="order_no" readonly>
                            <span class="error badge text-danger" id="order_noError"></span>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-3">
                            <label for="">Select From Store <span class="text-danger">*</span></label>
                            <select class="form-control select" style="height: 32px !important" id="store_id" name="store_id" >
                                <option value="">Select  Store</option>
                              <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                               @foreach($tbl_store as $tbl_store)
                                <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                              @endforeach
                            </select>
                            <span class="error badge text-danger" id="store_idError"></span>
                        </div>
                        <div class="col-md-3" id="TostoreDiv">
                            <label for="">Select To Store <span class="text-danger">*</span></label>
                            <select class="form-control select" style="height: 32px !important" id="to_store_id" name="to_store_id" >
                                <option value="">Select  Store</option>
                              <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                               @foreach($tbl_store as $tbl_store)
                                <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                              @endforeach
                            </select>
                            <span class="error badge text-danger" id="to_store_idError"></span>
                        </div>
                        <div class="col-md-3" id="custdDiv" style="display:none">
                            <label for="" class="form-label">Customer Details: </label>
                            <input type="text" class="form-control"  id="company_name" name="company_name" autocomplete="off">
                            <input type="hidden" class="form-control"  id="cust_id" name="cust_id" >
                            <input type="hidden" class="form-control"  id="contact_no" name="contact_no" >
                            <input type="hidden" class="form-control"  id="email_id" name="email_id" >
                            <input type="hidden" class="form-control"  id="cust_address" name="cust_address" >
                            <input type="hidden" class="form-control"  id="state_id" name="state_id" >
                            <input type="hidden" class="form-control"  id="city_id" name="city_id" >
                            <input type="hidden" class="form-control"  id="pincode" name="pincode" >
                            <input type="hidden" class="form-control"  id="gst_no" name="gst_no" >
                            <input type="hidden" class="form-control"  id="cust_name" name="cust_name" >
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
                            <input type="hidden" class="form-control" id="sales_text_per" name="sales_text_per">
                            <input type="hidden" class="form-control" id="taxrule" name="taxrule">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                             <label for="" class="form-label">From Store Details: </label>
                            <textarea type="text" class="form-control" rows="5"   id="from_store_details"  readonly></textarea>
                        </div>
                        <div class="col-md-4" id="TostoreDetailsDiv">
                             <label for="" class="form-label">To Store Details: </label>
                            <textarea type="text" class="form-control" rows="5"   id="to_store_details"  readonly></textarea>
                        </div>
                        <div class="col-md-4" id="custdDetailsDiv" style="display:none">
                             <label for="" class="form-label">Customer Details: </label>
                            <textarea type="text" class="form-control" rows="5"    id="customer_details"  readonly></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
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
                                                    <input type="text" class="form-control barcode" name="barcode[]" placeholder="Enter barcode">
                                                </td>
                                                <td>
                                                    <select class="form-control product-type" style="height: 32px !important;" name="product_type[]">
                                                        <option value="">Select Product</option>
                                                        <option value="Frame">Frame</option>
                                                        <option value="Glass">Glass</option>
                                                        <option value="Goggles">Goggles</option>
                                                        <option value="Lens">Contact Lens</option>
                                                        <option value="Solution">Solution</option>
                                                        <option value="Repair">Repair</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                    <input type="hidden" class="form-control product-code" name="product_code[]">
                                                    <input type="hidden" class="form-control product-id" name="product_id[]">
                                                    <input type="hidden" class="form-control product-company" name="product_company[]">
                                                    <input type="hidden" class="form-control product-quality" name="product_quality[]">
                                                    <input type="hidden" class="form-control product-materiale" name="product_material[]">
                                                    <input type="hidden" class="form-control product-color" name="product_color[]">
                                                    <input type="hidden" class="form-control product-design" name="product_design[]">
                                                    <input type="hidden" class="form-control product-coating" name="product_coating[]">
                                                    <input type="hidden" class="form-control product-index" name="product_index[]">
                                                    <input type="hidden" class="form-control product-number" name="product_number[]">
                                                    <input type="hidden" class="form-control product-ct" name="product_ct[]">
                                                    <input type="hidden" class="form-control product-typesss" name="product_typesss[]">
                                                    <input type="hidden" class="form-control product-validity" name="product_validity[]">
                                                    <input type="hidden" class="form-control product-shape" name="product_shape[]">
                                                    <input type="hidden" class="form-control product-size" name="product_size[]">
                                                    <input type="hidden" class="form-control product-variant" name="product_variant[]">
                                                    <input type="hidden" class="form-control package-id" name="package_id[]">
                                                    <input type="hidden" class="form-control coating-apply" name="coating_apply[]">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control product-description" name="product_description[]" placeholder="Enter Product Description" readonly>
                                                    <input type="hidden" class="form-control GL_EYE_RS_D" name="GL_EYE_RS_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_RC_D" name="GL_EYE_RC_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_RA_D" name="GL_EYE_RA_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_RP_D" name="GL_EYE_RP_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_RV_D" name="GL_EYE_RV_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_RS_N" name="GL_EYE_RS_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_RC_N" name="GL_EYE_RC_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_RA_N" name="GL_EYE_RA_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_RP_N" name="GL_EYE_RP_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_RV_N" name="GL_EYE_RV_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_RADD" name="GL_EYE_RADD[]">
                                                    <input type="hidden" class="form-control GL_EYE_totalPD" name="GL_EYE_totalPD[]">
                                                    <input type="hidden" class="form-control GL_EYE_LS_D" name="GL_EYE_LS_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_LC_D" name="GL_EYE_LC_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_LA_D" name="GL_EYE_LA_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_LP_D" name="GL_EYE_LP_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_LV_D" name="GL_EYE_LV_D[]">
                                                    <input type="hidden" class="form-control GL_EYE_LS_N" name="GL_EYE_LS_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_LC_N" name="GL_EYE_LC_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_LA_N" name="GL_EYE_LA_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_LP_N" name="GL_EYE_LP_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_LV_N" name="GL_EYE_LV_N[]">
                                                    <input type="hidden" class="form-control GL_EYE_LADD" name="GL_EYE_LADD[]">
                                                    
                                                    <input type="hidden" class="form-control frame-dbl" name="frame_dbl[]">
                                                    <input type="hidden" class="form-control frame-fh" name="frame_fh[]">
                                                    <input type="hidden" class="form-control frame-ed" name="frame_ed[]">
                                                    <input type="hidden" class="form-control frame-asize" name="frame_asize[]">
                                                    <input type="hidden" class="form-control frame-bsize" name="frame_bsize[]">
                                                    <input type="hidden" class="form-control frametypeglass" name="frametypeglass[]">
                         
                                                    <input type="hidden" class="form-control right-left" name="right_left[]">
                                                    <input type="hidden" class="form-control doctor-name" name="doc_name[]">
                                                    <input type="hidden" class="patient-name" name="patient_name[]">
                                                    <input type="hidden" class="form-control wearing-type" name="wearing_type[]">
                                                    <input type="hidden" class="form-control wearing-inhouse" name="wearing_types_inhouse[]">
                                                    <input type="hidden" class="prescription-notes" name="prescription_notes[]">
                                                    <input type="hidden" class="count-eye-test" name="count_eye_test[]">
                                                    <input type="hidden" class="lensRightNoOfBoxes" name="lensRightNoOfBoxes[]">
                                                    <input type="hidden" class="lensRightTotalPieces" name="lensRightTotalPieces[]">
                                                    <input type="hidden" class="lensLeftNoOfBoxes" name="lensLeftNoOfBoxes[]">
                                                    <input type="hidden" class="lensLeftTotalPieces" name="lensLeftTotalPieces[]">
                                                    <input type="hidden" class="lens_bids" name="lens_bids[]">
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
                                                            <!-- Buttons and Payment -->
                                                            <div class="row">
                                                                <div class="col-md-3" id="addcouponBtn">
                                                                    <button class="btn btn-primary" type="button" id="couponBtn">Apply Discount</button>
                                                                </div>
                                                                <div class="col-md-3" style="display:none" id="removeCBtn">
                                                                    <button class="btn btn-danger" type="button" id="removecouponBtn">Remove Discount </button>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <!-- Payment Details -->
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h5>Payment Method </h5>
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
                                                           
                                                        </td>
                                
                                                        <!-- Right Summary -->
                                                        <td colspan="2">
                                                            <table class="table table-borderless table-sm mb-0 w-100">
                                                                <tr>
                                                                    <td class="text-end">Total Sales Amount : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="total_item_price" id="total_item_price" value="0.00" readonly></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Total Base Amount : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="total_basic_amount" id="total_basic_amount" value="0.00" readonly></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-end">Total GST Amount : Rs</td>
                                                                    <td><input type="text" class="form-control form-control-sm text-end" name="total_gst_amount" id="total_gst_amount" value="0.00" readonly></td>
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
                                                                    <td class="text-end">Net Payable Amount : Rs</td>
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


<div class="modal fade" id="bbcustomerModal" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog full-scrren" role="document">
        <div class="modal-content" >
            <div class="modal-header" style="background: cornsilk;">
                <h5 class="modal-title" id="modalTitle">Customer Details With Company Name Search</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="customerTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Customer Name</th>
                      <th>Mobile No(s)</th>
                      <th>Email</th>
                      <th>Customer GST Number</th>
                      <th>Company Name</th>
                      <th>Address</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td colspan="7" class="text-center">Loading...</td></tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: cornsilk;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
    </div>
</div> 


@endsection

@section('scripts')

@include('B2B.sale-script')

@endsection

