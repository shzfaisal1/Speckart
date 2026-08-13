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
#supplierListName{
    width: 100%;
    padding: 5px 15px;
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

.table-responsive {
      overflow-x: auto;
    }
    input.form-control, select.form-control {
      font-size: 0.9rem;
    }
    .removeBtn {
      border: none;
      background: transparent;
      color: red;
      cursor: pointer;
      font-size: 1.2rem;
    }
    .input-group input {
      text-align: center;
    }
    
    .table thead tr th {
        font-size: 12px;
        font-weight: 500 !important;
        color: #000;
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
                        <h3>Inventory Levels</h3>
                         <a href="{{route('admin.add-inventory')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add New Inventory
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-3">
                    <div class="form-group">
                        <select class="form-control select"  id="product_type" name="product_type">
                            <option value="">Select Product </option>
                            <option value="Frame">Frame</option>
                            <option value="Glass">Glass</option>
                            <option value="Goggles">Goggles</option>
                            <option value="Lens">Contact Lens</option>
                            <option value="Solution">Solution</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Product code,Description" id="search" name="search" style="width: 250px;">
                    </div>
                </div> 
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3">
                     <div class="form-group">
                        <select class="form-control select" style="height: 32px !important;margin-top:10px" id="store_id" name="store_id">
                            <option value="">Select  Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">
               <div class="col-lg-12">
                <div class="domestic-orders-table">
                    <div id="processingLoader" class="processing-loader" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <strong class="text-success">Please wait...</strong>
                                    <div class="spinner-border ms-auto text-success spinner-grow" role="status"
                                        aria-hidden="true"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-10p">Sr.No</th>
                                <th class="wd-10p">Product</th>
                                <th class="wd-10p">Product ID</th>
                                <th class="wd-10p">Product Code</th>
                                <th class="wd-10p">Description</th>
                                <th class="wd-10p">Available Quantity</th>
                                <th class="wd-10p">Store</th>
                                <th class="wd-10p">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
        
               </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" data-backdrop="static" id="addInvModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Add Inventory</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="inventoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="store_name_text"></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="product_type_text"></strong>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                         <div class="alert alert-dark">
                             <strong id="product_code_text"></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="product_id_text"></strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-dark">
                             <strong id="description_text"></strong>
                        </div>
                    </div>
                </div>
                
                <hr/>
                 <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="uid" id="uid">
                <input type="hidden" name="ptype" id="ptype">
                <input type="hidden" name="pcode" id="pcode">
                <input type="hidden" name="stid" id="stid">
                <input type="hidden" name="pid" id="pid">
                
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Tax Rule <span class="text-danger">*</span></label><br>
                            <select class="form-control select2" style="height: 32px !important;" id="tax_rule" name="tax_rule">
                                   <option value="Include">Include</option>
                                  <option value="Exclude ">Exclude</option>
                                  <option value="Not Appicable">Not Appicable</option>
                            </select>
                            <span class="error badge text-danger" id="tax_ruleError"></span>
                        </div>
                        <div class="col-md-4">
                            <label>Quantity <span class="text-danger">*</span></label>
                            <input type="text" id="qty" name="qty" class="form-control">
                            <span class="error badge text-danger" id="qtyError"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                                Purchase Rs 
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_purchase_price" name="modal_purchase_price" class="form-control" placeholder="Enter amount">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                                Retail Price 
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_retail_price" name="modal_retail_price" class="form-control" placeholder="Enter amount">
                              </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                                HSN/SAC Code 
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_hsn_code" name="modal_hsn_code" class="form-control" placeholder="">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" name="modal_purchase_price" class="col-lg-5 col-form-label">
                                GST %  
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_gst" name="modal_gst" class="form-control">
                              </div>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row">
                         <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" name="modal_purchase_price" class="col-lg-7 col-form-label">
                                Track Inventory  
                              </label>
                              <div class="col-lg-5">
                                <div class="d-flex" style="margin-top: 10px;">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="modal_Track_Inventory" id="inlineRadio1" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio1">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="modal_Track_Inventory" id="inlineRadio2" value="0">
                                      <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                </div>    
                                  
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-7 col-form-label">
                                Allow Negative Inventory  
                              </label>
                              <div class="col-lg-5">
                                 <div class="d-flex" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="modal_Negative_Inventory" id="inlineRadio3" value="1" checked>
                                          <label class="form-check-label" for="inlineRadio3">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="modal_Negative_Inventory" id="inlineRadio4" value="0">
                                          <label class="form-check-label" for="inlineRadio4">No</label>
                                        </div>
                                    </div> 
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-3 col-form-label">
                                Barcode Options  
                              </label>
                              <div class="col-lg-9">
                                 <div class="d-flex" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="modal_barcode_option" id="inlineRadio5" value="1" checked>
                                          <label class="form-check-label" for="inlineRadio5">System Generated / Unique</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="modal_barcode_option" id="inlineRadio6" value="0">
                                          <label class="form-check-label" for="inlineRadio6">Not Required</label>
                                        </div>
                                    </div> 
                              </div>
                            </div>
                        </div>
                   </div>
                    <div class="row">
                        <div class="col-md-4">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                                Basic Price 
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_basic_price" name="modal_basic_price" class="form-control" placeholder="0.00" readonly>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                                GST Amount Rs  
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_gst_amount" name="modal_gst_amount" class="form-control" placeholder="0.00" readonly>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                                Total Purchase
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_total_price" name="modal_total_price" class="form-control" placeholder="0.00" readonly>
                              </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                               Total Basic Price 
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_total_basic_price" name="modal_total_basic_price" class="form-control" placeholder="0.00" readonly>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                                Total GST Amount Rs  
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_total_gst_amount" name="modal_total_gst_amount" class="form-control" placeholder="0.00" readonly>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                                Total Purchase amount
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_total_purchase_price" name="modal_total_purchase_price" class="form-control" placeholder="0.00" readonly>
                              </div>
                            </div>
                        </div>
                        
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="submit" class="btn btn-primary">Add Inventory</button>
            </div>
        </form>
      </div>
    </div>
  </div>


<div class="modal fade" data-backdrop="static" id="addpurchaseModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Add Purchase</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="purchaseForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="store_name_p"></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="product_type_p"></strong>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                         <div class="alert alert-dark">
                             <strong id="product_code_p"></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="product_id_p"></strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-dark">
                             <strong id="description_p"></strong>
                        </div>
                    </div>
                </div>
                
                <hr/>
                 <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="inventory_id" id="inventory_id">
                <input type="hidden" name="producttype_p" id="producttype_p">
                <input type="hidden" name="productcode_p" id="productcode_p">
                <input type="hidden" name="storeid_p" id="storeid_p">
                <input type="hidden" name="productid_p" id="productid_p">
                
                <div class="row">
                     <div class="col-md-3">
                            <label for="">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                            <span class="error badge text-danger" id="purchase_dateError"></span>
                        </div>
                        <div class="col-md-3">
                            <div class="SupplierName">
                                <label for="">Supplier Name <span class="text-danger">*</span></label>
                                <input class="form-control"  placeholder="Enter Supplier Name" id="supplier_name" name="supplier_name" autocomplete="off">
                                <div id="supplierListName" class="dropdown-menu" style="display: none; position: relative;"></div>
                                <span class="error badge text-danger" id="supplier_nameError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">Purchase Bill Number <span class="text-danger">*</span></label>
                            <input class="form-control"  placeholder="Enter Purchase Bill Number" id="p_bill_no" name="p_bill_no">
                            <span class="error badge text-danger" id="p_bill_noError"></span>
                        </div>
                    <div class="col-md-3">
                        <label for="">Tax Rule <span class="text-danger">*</span></label><br>
                        <select class="form-control select2" style="height: 32px !important;" id="tax_rule_p" name="tax_rule_p">
                              <option value="">Select tax rule</option>
                              <option value="Not Applicable">Not Appicable</option>
                              <option value="Include">Include</option>
                              <option value="Exclude">Exclude</option>
                              
                        </select>
                        <span class="error badge text-danger" id="tax_rule_pError"></span>
                    </div>
                    <div class="col-md-2">
                        <label>No of Boxes <span class="text-danger">*</span></label>
                        <input type="text" id="no_of_box" name="no_of_box" class="form-control">
                        <span class="error badge text-danger" id="no_of_boxError"></span>
                    </div>
                    <div class="col-md-2">
                        <label>Pieces Per Box </label>
                        <input type="text" id="per_box" name="per_box" class="form-control" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="text" id="qty_p" name="qty_p" class="form-control">
                        <span class="error badge text-danger" id="qty_pError"></span>
                    </div>
                    <div class="col-md-2">
                        <label>Batch number</label>
                        <input type="text" id="batch_no" name="batch_no" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Mfg Date</label>
                        <input type="date" id="mfg_date" name="mfg_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Expiry Date</label>
                        <input type="date" id="exp_date" name="exp_date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Consider As Pair  </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="ispairp" name="ispairp" value="1">
                        </div>
                    </div>
                </div>
                <div class="row">
                      
                      <div class="col-md-6">
                        <div class="row">
                          <label class="col-lg-4 col-form-label">Purchase Rs <span class="text-danger">*</span></label>
                          <div class="col-lg-8">
                            <input type="text" id="product_price" name="product_price" class="form-control product-price" placeholder="Enter amount">
                            <span class="error badge text-danger" id="product_priceError"></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <label class="col-lg-4 col-form-label">Retail Price</label>
                          <div class="col-lg-8">
                            <input type="text" id="product_retail_price" name="product_retail_price" class="form-control product-retail-price" placeholder="Enter amount">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <label class="col-lg-4 col-form-label">B2B Price</label>
                          <div class="col-lg-8">
                            <input type="text" id="bb_price" name="bb_price" class="form-control bb-retail-price" placeholder="Enter amount">
                          </div>
                        </div>
                      </div>
                    
                    </div>
                    
                    <br>
                    
                    <div class="row">
                      <div class="col-md-4">
                        <div class="row">
                          <label class="col-lg-5 col-form-label">HSN/SAC Code</label>
                          <div class="col-lg-7">
                            <input type="text" id="hsn_code" name="hsn_code" class="form-control">
                          </div>
                        </div>
                      </div>
                    
                      <div class="col-md-4">
                        <div class="row">
                          <label class="col-lg-4 col-form-label">GST % </label>
                          <div class="col-lg-8">
                            <input type="text" id="gst" name="gst" class="form-control">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2"> <button class="btn btn-primary" type="button" id="getoldvalue">Get Old Purchase Value</button></div>
                      
                    </div>
                    <br>
                    <div class="row">

                         <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-7 col-form-label">
                                Track Inventory  
                              </label>
                              <div class="col-lg-5">
                                <div class="d-flex" style="margin-top: 10px;">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Track_Inventory" id="inlineRadio1" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio1">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Track_Inventory" id="inlineRadio2" value="0">
                                      <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                </div>    
                                  
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-7 col-form-label">
                                Allow Negative Inventory  
                              </label>
                              <div class="col-lg-5">
                                 <div class="d-flex" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Negative_Inventory" id="inlineRadio3" value="1" checked>
                                          <label class="form-check-label" for="inlineRadio3">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Negative_Inventory" id="inlineRadio4" value="0">
                                          <label class="form-check-label" for="inlineRadio4">No</label>
                                        </div>
                                    </div> 
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-3 col-form-label">
                                Barcode Options  
                              </label>
                              <div class="col-lg-9">
                                 <div class="d-flex" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="barcode_option" id="inlineRadio5" value="1" checked>
                                          <label class="form-check-label" for="inlineRadio5">System Generated / Unique</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="barcode_option" id="inlineRadio6" value="0">
                                          <label class="form-check-label" for="inlineRadio6">Not Required</label>
                                        </div>
                                    </div> 
                              </div>
                            </div>
                        </div>
                    </div>
                    
                <br>
                
                <div class="row">
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Basic Price</label>
                      <div class="col-lg-7">
                        <input type="text" id="product_base_price" name="product_base_price" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">GST Amount Rs</label>
                      <div class="col-lg-7">
                        <input type="text" id="gst_amt" name="gst_amt" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total Purchase</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_purchase" name="total_purchase" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                </div>
                
                <br>
                
                <div class="row">
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total Unit Amount</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_purchase_price" name="total_unit_price" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                   <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total basic Amount</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_basic_price" name="total_basic_price" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                   <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total GST Amount</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_gst_amount" name="total_gst_amount" class="form-control" readonly placeholder="0.00" value="0">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total  Purchase amount</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_purchase_price" name="total_purchase_price" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">RoundOff Amount (+/-)</label>
                      <div class="col-lg-7">
                        <input type="text" id="round_off" name="round_off" class="form-control" placeholder="0.00" value="0">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total Net Purchase</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_net_purchase_price" name="total_net_purchase_price" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                
                  
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="submit" class="btn btn-primary">Add Purchase</button>
            </div>
        </form>
      </div>
    </div>
  </div> 
  
  
<div class="modal fade" id="OldvalueModal" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog full-scrren" role="document">
        <div class="modal-content" >
            <div class="modal-header" style="background: cornsilk;">
                <h5 class="modal-title" id="modalTitle">Purchase old value Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="oldvalueTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Supplier Name</th>
                      <th>Purchase Date</th>
                      <th>Product Details</th>
                      <th>Purchase price</th>
                      <th>Retail price</th>
                      <th>HSN Code</th>
                      <th>GST %</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: cornsilk;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
    </div>
</div> 


<div class="modal fade" data-backdrop="static" id="addchallanModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitles">Add Challan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="challanForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="store_name_c"></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="product_type_c"></strong>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                         <div class="alert alert-dark">
                             <strong id="product_code_c"></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-dark">
                             <strong id="product_id_c"></strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-dark">
                             <strong id="description_c"></strong>
                        </div>
                    </div>
                </div>
                
                <hr/>
                 <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="inventory_ids" id="inventory_ids">
                <input type="hidden" name="producttype_c" id="producttype_c">
                <input type="hidden" name="productcode_c" id="productcode_c">
                <input type="hidden" name="storeid_c" id="storeid_c">
                <input type="hidden" name="productid_c" id="productid_c">
                
                <div class="row">
                     <div class="col-md-3">
                            <label for="">Challan Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="challan_date" name="challan_date">
                            <span class="error badge text-danger" id="challan_dateError"></span>
                        </div>
                        <div class="col-md-3">
                            <div class="SupplierName">
                                <label for="">Supplier Name <span class="text-danger">*</span></label>
                                <input class="form-control"  placeholder="Enter Supplier Name" id="supplier_name_challan" name="supplier_name_challan" autocomplete="off">
                                <div id="supplierListNamechallan" class="dropdown-menu-challan" style="display: none; position: relative;"></div>
                                <span class="error badge text-danger" id="supplier_name_challanError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">Challan Number<span class="text-danger">*</span></label>
                            <input class="form-control"  placeholder="Enter Challan Number" id="challan_no" name="challan_no">
                            <span class="error badge text-danger" id="challan_noError"></span>
                        </div>
                    <div class="col-md-3">
                        <label for="">Tax Rule <span class="text-danger">*</span></label><br>
                        <select class="form-control select2" style="height: 32px !important;" id="tax_rule_c" name="tax_rule_c">
                              <option value="">Select Tax Rule</option>
                              <option value="Not Applicable">Not Appicable</option>
                              <option value="Include">Include</option>
                              <option value="Exclude ">Exclude</option>
                              
                        </select>
                        <span class="error badge text-danger" id="tax_rule_cError"></span>
                    </div>
                    <div class="col-md-2">
                        <label>No of Boxes <span class="text-danger">*</span></label>
                        <input type="text" id="no_of_box_challan" name="no_of_box_challan" class="form-control">
                        <span class="error badge text-danger" id="no_of_box_challanError"></span>
                    </div>
                    <div class="col-md-2">
                        <label>Pieces Per Box </label>
                        <input type="text" id="per_box_challan" name="per_box_challan" class="form-control" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="text" id="qty_c" name="qty_c" class="form-control">
                        <span class="error badge text-danger" id="qty_cError"></span>
                    </div>
                    <div class="col-md-2">
                        <label>Batch number</label>
                        <input type="text" id="batch_no_challan" name="batch_no_challan" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Mfg Date</label>
                        <input type="text" id="mfg_date_challan" name="mfg_date_challan" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Expiry Date</label>
                        <input type="text" id="exp_date_challan" name="exp_date_challan" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Consider As Pair  </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="ispair" name="ispair" value="1">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                      
                      <div class="col-md-4">
                        <div class="row">
                          <label class="col-lg-4 col-form-label">Purchase Rs </label>
                          <div class="col-lg-8">
                            <input type="text" id="product_price_challan" name="product_price_challan" class="form-control product-price-challan" placeholder="Enter amount">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="row">
                          <label class="col-lg-4 col-form-label">Retail Price</label>
                          <div class="col-lg-8">
                            <input type="text" id="product_retail_price_challan" name="product_retail_price_challan" class="form-control product-retail-price-challan" placeholder="Enter amount">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="row">
                          <label class="col-lg-4 col-form-label">B2B  Price</label>
                          <div class="col-lg-8">
                            <input type="text" id="bb_price_challan" name="bb_price_challan" class="form-control bb-price-challan" placeholder="Enter amount">
                          </div>
                        </div>
                      </div>
                    
                    </div>
                    
                    <br>
                    
                    <div class="row">
                      <div class="col-md-4">
                        <div class="row">
                          <label class="col-lg-5 col-form-label">HSN/SAC Code</label>
                          <div class="col-lg-7">
                            <input type="text" id="hsn_code_challan" name="hsn_code_challan" class="form-control">
                          </div>
                        </div>
                      </div>
                    
                      <div class="col-md-4">
                        <div class="row">
                          <label class="col-lg-4 col-form-label">GST % </label>
                          <div class="col-lg-8">
                            <input type="text" id="gst_challan" name="gst_challan" class="form-control">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2"> <button class="btn btn-primary" type="button" id="getoldvaluechallan">Get Old Purchase Value</button></div>
                      
                    </div>
                    <br>
                    <div class="row">

                         <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-7 col-form-label">
                                Track Inventory  
                              </label>
                              <div class="col-lg-5">
                                <div class="d-flex" style="margin-top: 10px;">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Track_Inventory_challan" id="inlineRadio1" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio1">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Track_Inventory_challan" id="inlineRadio2" value="0">
                                      <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                </div>    
                                  
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-7 col-form-label">
                                Allow Negative Inventory  
                              </label>
                              <div class="col-lg-5">
                                 <div class="d-flex" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Negative_Inventory_challan" id="inlineRadio3" value="1" checked>
                                          <label class="form-check-label" for="inlineRadio3">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Negative_Inventory_challan" id="inlineRadio4" value="0">
                                          <label class="form-check-label" for="inlineRadio4">No</label>
                                        </div>
                                    </div> 
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-3 col-form-label">
                                Barcode Options  
                              </label>
                              <div class="col-lg-9">
                                 <div class="d-flex" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="barcode_option_challan" id="inlineRadio5" value="1" checked>
                                          <label class="form-check-label" for="inlineRadio5">System Generated / Unique</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="barcode_option_challan" id="inlineRadio6" value="0">
                                          <label class="form-check-label" for="inlineRadio6">Not Required</label>
                                        </div>
                                    </div> 
                              </div>
                            </div>
                        </div>
                    </div>
                    
                <br>
                
                <div class="row">
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Basic Price</label>
                      <div class="col-lg-7">
                        <input type="text" id="product_base_price_challan" name="product_base_price_challan" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">GST Amount Rs</label>
                      <div class="col-lg-7">
                        <input type="text" id="gst_amt_challan" name="gst_amt_challan" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total Purchase</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_purchase_challan" name="total_purchase_challan" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                </div>
                
                <br>
                
                <div class="row">
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total Basic Price</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_basic_price_challan" name="total_basic_price_challan" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total GST Amount</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_gst_amount_challan" name="total_gst_amount_challan" class="form-control" readonly placeholder="0.00" value="0">
                      </div>
                    </div>
                  </div>
                
                  <div class="col-md-4">
                    <div class="row">
                      <label class="col-lg-5 col-form-label">Total Purchase Amount</label>
                      <div class="col-lg-7">
                        <input type="text" id="total_net_purchase_challan" name="total_net_purchase_challan" class="form-control" readonly placeholder="0.00">
                      </div>
                    </div>
                  </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="submit" class="btn btn-primary">Add Challan</button>
            </div>
        </form>
      </div>
    </div>
  </div> 
  
  
  <div class="modal fade" id="OldvaluechallanModal" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog full-scrren" role="document">
        <div class="modal-content" >
            <div class="modal-header" style="background: cornsilk;">
                <h5 class="modal-title" id="modalTitle">Purchase old value Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="oldvaluechallanTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Supplier Name</th>
                      <th>Purchase Date</th>
                      <th>Product Details</th>
                      <th>Purchase price</th>
                      <th>Retail price</th>
                      <th>HSN Code</th>
                      <th>GST %</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: cornsilk;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
    </div>
</div> 



<div class="modal fade" id="addadjustModal" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog full-scrren" role="document">
        <div class="modal-content" >
            <div class="modal-header" style="background: cornsilk;">
                <h5 class="modal-title" id="modalTitle">Sales Order List of Glass</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="salesorderTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Order Date</th>
                      <th>Order Number</th>
                      <th>Description</th>
                      <th>Purchase Price</th>
                      <th>Right/Left</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: cornsilk;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
    </div>
</div> 


<div class="modal fade" data-backdrop="static" id="adjuststock" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Inventory - Adjust Stock</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="adjustForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="alert alert-dark">
                             <strong id="store_name_glass"></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-dark">
                             <strong id="product_type_glass"></strong>
                        </div>
                    </div>
					 <div class="col-md-4">
                         <div class="alert alert-dark">
                             <strong id="product_code_glass"></strong>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                   
                    <div class="col-md-4">
                        <div class="alert alert-dark">
                             <strong id="product_id_glass"></strong>
                        </div>
                    </div>
					<div class="col-md-4">
                        <div class="alert alert-dark">
                             <strong id="orderno_glass"></strong>
                        </div>
                    </div>
					<div class="col-md-4">
                        <div class="alert alert-dark">
                             <strong id="order_date_glass"></strong>
                        </div>
                    </div>
                </div>
                <div class="row">
				    <div class="col-md-4">
                        <div class="alert alert-dark">
                             <strong id="position_glass"></strong>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="alert alert-dark">
                             <strong id="description_glass"></strong>
                        </div>
                    </div>
                </div>
                
                <hr/>
                 <input type="hidden" name="_method" id="formMethod" value="POST">
                 <input type="hidden" name="salesProductId" id="salesProductId">
				 <input type="hidden" name="invid" id="invid">
				 <input type="hidden" name="stid" id="stid">

                
                <div class="row">
                     <div class="col-md-3">
                        <label for="">Barcode / Product Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bp_code" name="bp_code">
                        <span class="error badge text-danger" id="bp_codeError"></span>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="submit" class="btn btn-primary">Adjist Stock</button>
            </div>
        </form>
      </div>
    </div>
  </div> 
  
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
  });
</script> 
<script>
var start = moment('2025-01-01'); // Lifetime start date
var end = moment(); // Today

function isCurrentMonth(date)
{
    return date.month() === moment().month() && date.year() === moment().year();
}

function cb(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    $('#date_from').val(start.format('YYYY-MM-DD'));
    $('#date_to').val(end.format('YYYY-MM-DD'));

    if (isCurrentMonth(start) || isCurrentMonth(end)) {
        console.log("Start or end date is in the current month.");
    } else {
        console.log("Neither date is in the current month.");
    }

    const column = dataListView.column(0);
    column.search(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    dataListView.draw();
}

$('#reportrange').daterangepicker({
    startDate: start,
    endDate: end,
    autoUpdateInput: false,
    showDropdowns: true,
    maxDate: moment(),
    ranges: {
        'Today': [moment(), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [
            moment().subtract(1, 'month').startOf('month'),
            moment().subtract(1, 'month').endOf('month')
        ],
        'Lifetime': [moment('2025-01-01'), moment()]
    }
}, function(start, end) {
    cb(start, end);
});

// Update on apply
$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
    cb(picker.startDate, picker.endDate);
});

// Set initial range to Lifetime on load
cb(start, end);
</script>

<script>
let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function() {
        $('#processingLoader').show();
    })
    .on('draw.dt', function() 
    {
      $('#processingLoader').hide();
      
    }).DataTable({

        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "ajax": {
            url: "{{ route('admin.inventory-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.product_type = $('#product_type').val(),
                d.search1 = $('#search').val(),
                d.store_id = $('#store_id').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "product_type",
                orderable: false,
            },
            {
                "data": "product_id",
                orderable: false,
            },
            {
                "data": "product_code",
                orderable: false,
            },
            {
                "data": "description",
                orderable: false,
            },
            {
                "data": "qty",
                orderable: false,
            },
            {
                "data": "store_name",
                orderable: false,
            },
            {
                "data": "action",
                orderable: false,
            },
            
        ],

        searchDelay: 1500,
        columnDefs: [{
                // For Responsive
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0
            },
            {
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function (data, type, full)
                {
                    let html = `
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item pointer"  onclick="openpurchaseModal('` + full['encryptedId'] + `','` + full['product_type'] + `','` + full['product_code']+ `','` + full['product_id'] + `','` + full['product_details'] + `','` + full['store_name'] + `','` + full['store_id'] + `')">Add Purchase</a>
                                <a class="dropdown-item pointer"  onclick="openinventoryModal('` + full['encryptedId'] + `','` + full['product_type'] + `','` + full['product_code']+ `','` + full['product_id'] + `','` + full['product_details'] + `','` + full['store_name'] + `','` + full['store_id'] + `')">Add Inventory</a>
                                <a class="dropdown-item pointer"  onclick="openchallanModal('` + full['encryptedId'] + `','` + full['product_type'] + `','` + full['product_code']+ `','` + full['product_id'] + `','` + full['product_details'] + `','` + full['store_name'] + `','` + full['store_id'] + `','` + full['perbox'] + `')">Add Challan</a>
                    `;
            
                    if (parseFloat(full['qty_av']) < 0) {
                        html += `<a class="dropdown-item pointer" onclick="openadjustModal('` + full['encryptedId'] + `','` + full['product_type'] + `','` + full['product_code']+ `','` + full['product_details'] + `','` + full['store_id'] + `')">Adjust Stock</a>`;
                    }
            
                    if (parseFloat(full['qty_av']) > 0) 
                    {
                        let deleteUrl = `{{ route('admin.inventory.delete', ':inventory_id') }}`.replace(':inventory_id', full['encryptedId']);
                        html += `<a href="${deleteUrl}" class="dropdown-item">Delete</a>`;
                    }
            
                    html += `
                            </div>
                        </div>
                    `;
            
                    return html;
                }
            }

            

        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        language: {
            paginate: 
            {
                previous: '&nbsp;',
                next: '&nbsp;'
            },
            sLengthMenu: "_MENU_",
            sZeroRecords: "{{ __('No results available') }}",
            sSearch: "{{ __('search') }}",
            sProcessing: "{{ __('processing') }}",
            sInfo: "{{ __('Showing :start to :end of :total entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}",
            sInfoFiltered: "" 
        },
        responsive: {
            details: {
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    let data = $.map(columns, function(col) {
                        return col.title !==
                            '' 
                            ?
                            '<tr data-dt-column="' +
                            col.columnIndex +
                            '">' +
                            '<td>' +
                            col.title +
                            ':' +
                            '</td> ' +
                            '<td>' +
                            col.data +
                            '</td>' +
                            '</tr>' :
                            '';
                    }).join('');

                    return data ? $('<table class="table"/>').append('<tbody>' + data +
                        '</tbody>') : false;
                }
            }
        },
        aLengthMenu: [
            [10, 20, 50, 100],
            [10, 20, 50, 100]
        ],
        select: {
            style: "multi"
        },
        order: [
            [2, "desc"]
        ],
        displayLength: 10,
    });
     let debounceTimer;
    $('.input').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            const column = dataListView.column($(this).attr('name'));
            column.search($(this).val()).draw();
        }.bind(this), 500);
    });
    
    $('.select').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });
    
    

    function openinventoryModal(id, product_type, product_code, product_id,description,store_name,store_id) 
    {
        $('#product_type_text').text('');
        $('#product_code_text').text('');
        $('#product_id_text').text('');
        $('#description_text').text('');
        $('#store_name_text').text('');
        
        var store_name_text = store_name == 'null' ? '' : ' Store Name : ' + store_name;
        var product_type_text = product_type == 'null' ? '' : ' Product Type : ' + product_type;
        var product_code_text = product_code == 'null' ? '' : '   Product Code : ' + product_code;
        var product_id_text = product_id == 'null' ? '' : '   Product Id : ' + product_id;
        var description_text = description == 'null' ? '' : '   Product Description : ' + description;
        document.getElementById('modalTitle').innerText = 'Add Inventory';
        document.getElementById('uid').value = id;
        document.getElementById('ptype').value = product_type;
        document.getElementById('pcode').value = product_code;
        document.getElementById('stid').value = store_id;
        document.getElementById('pid').value = product_id;
        $('#product_type_text').text(product_type_text);
        $('#product_code_text').text(product_code_text);
        $('#product_id_text').text(product_id_text);
        $('#description_text').text(description_text);
        $('#store_name_text').text(store_name_text);

    
        $('#addInvModal').modal('show');
    }
    
    function openpurchaseModal(id, product_type, product_code, product_id,description,store_name,store_id) 
    {
        $('#product_type_p').text('');
        $('#product_code_p').text('');
        $('#product_id_p').text('');
        $('#description_p').text('');
        $('#store_name_p').text('');
        
        var store_name_text = store_name == 'null' ? '' : ' Store Name : ' + store_name;
        var product_type_text = product_type == 'null' ? '' : ' Product Type : ' + product_type;
        var product_code_text = product_code == 'null' ? '' : '   Product Code : ' + product_code;
        var product_id_text = product_id == 'null' ? '' : '   Product Id : ' + product_id;
        var description_text = description == 'null' ? '' : '   Product Description : ' + description;
        document.getElementById('modalTitle').innerText = 'Add Purchase';
        document.getElementById('inventory_id').value = id;
        document.getElementById('producttype_p').value = product_type;
        document.getElementById('productcode_p').value = product_code;
        document.getElementById('storeid_p').value = store_id;
        document.getElementById('productid_p').value = product_id;
        $('#product_type_p').text(product_type_text);
        $('#product_code_p').text(product_code_text);
        $('#product_id_p').text(product_id_text);
        $('#description_p').text(description_text);
        $('#store_name_p').text(store_name_text);
        
        if (product_type === "Glass") {
            $("#ispairp").closest(".col-md-2").show();   // show entire div
        } else {
            $("#ispairp").prop("checked", false);        // uncheck
            $("#ispairp").closest(".col-md-2").hide();   // hide entire div
        }
        
        if (product_type === "Lens") {

            // SHOW fields
            $("#no_of_box").closest(".col-md-2").show();
            $("#per_box").closest(".col-md-2").show();
            $("#batch_no").closest(".col-md-2").show();
            $("#mfg_date").closest(".col-md-3").show();
            $("#exp_date").closest(".col-md-3").show();
        
        } else {
        
            // HIDE fields
            $("#no_of_box").closest(".col-md-2").hide();
            $("#per_box").closest(".col-md-2").hide();
            $("#batch_no").closest(".col-md-2").hide();
            $("#mfg_date").closest(".col-md-3").hide();
            $("#exp_date").closest(".col-md-3").hide();
        
            // Clear values if not Lens
            $("#no_of_box").val('');
            $("#per_box").val('');
            $("#batch_no").val('');
            $("#mfg_date").val('');
            $("#exp_date").val('');
        }

    
        $('#addpurchaseModal').modal('show');
    }
    
    
    
    function openchallanModal(id, product_type, product_code, product_id,description,store_name,store_id,perbox) 
    {
        $('#product_type_c').text('');
        $('#product_code_c').text('');
        $('#product_id_c').text('');
        $('#description_c').text('');
        $('#store_name_c').text('');
        
        var store_name_text = store_name == 'null' ? '' : ' Store Name : ' + store_name;
        var product_type_text = product_type == 'null' ? '' : ' Product Type : ' + product_type;
        var product_code_text = product_code == 'null' ? '' : '   Product Code : ' + product_code;
        var product_id_text = product_id == 'null' ? '' : '   Product Id : ' + product_id;
        var description_text = description == 'null' ? '' : '   Product Description : ' + description;
        document.getElementById('modalTitles').innerText = 'Add Challan';
        document.getElementById('inventory_ids').value = id;
        document.getElementById('producttype_c').value = product_type;
        document.getElementById('productcode_c').value = product_code;
        document.getElementById('storeid_c').value = store_id;
        document.getElementById('productid_c').value = product_id;
        document.getElementById('per_box_challan').value = perbox;
        $('#product_type_c').text(product_type_text);
        $('#product_code_c').text(product_code_text);
        $('#product_id_c').text(product_id_text);
        $('#description_c').text(description_text);
        $('#store_name_c').text(store_name_text);
        
        if (product_type === "Glass") {
            $("#ispair").closest(".col-md-2").show();   // show entire div
        } else {
            $("#ispair").prop("checked", false);        // uncheck
            $("#ispair").closest(".col-md-2").hide();   // hide entire div
        }
        
        if (product_type === "Lens") {

            // SHOW fields
            $("#no_of_box_challan").closest(".col-md-2").show();
            $("#per_box_challan").closest(".col-md-2").show();
            $("#batch_no_challan").closest(".col-md-2").show();
            $("#mfg_date_challan").closest(".col-md-3").show();
            $("#exp_date_challan").closest(".col-md-3").show();
        
        } else {
        
            // HIDE fields
            $("#no_of_box_challan").closest(".col-md-2").hide();
            $("#per_box_challan").closest(".col-md-2").hide();
            $("#batch_no_challan").closest(".col-md-2").hide();
            $("#mfg_date_challan").closest(".col-md-3").hide();
            $("#exp_date_challan").closest(".col-md-3").hide();
        
            // Clear values if not Lens
            $("#no_of_box_challan").val('');
            $("#per_box_challan").val('');
            $("#batch_no_challan").val('');
            $("#mfg_date_challan").val('');
            $("#exp_date_challan").val('');
        }
    
        $('#addchallanModal').modal('show');
    }
    
    
    

    function calculateProductModal() {
        const taxType = $('#tax_rule').val()?.trim() || '';
        const gstRate = parseFloat($('#modal_gst').val()) || 0;
        const purchasePrice = parseFloat($('#modal_purchase_price').val()) || 0;
        const product_type = $('#ptype').val()?.trim() || '';
        const qty = parseFloat($('#qty').val()) || 0;
    
        
    
        let basePrice = 0;
        let gstAmount = 0;
        let totalPrice = 0;
    
        if (taxType === 'Include') {
            basePrice = purchasePrice / (1 + gstRate / 100);
            gstAmount = purchasePrice - basePrice;
        } else if (taxType === 'Exclude') {
            basePrice = purchasePrice;
            gstAmount = (purchasePrice * gstRate) / 100;
        } else {
            basePrice = purchasePrice;
            gstAmount = 0;
        }
    
        totalPrice = basePrice + gstAmount;
    
        // Per unit values
        $("#modal_basic_price").val(basePrice.toFixed(2));
        $("#modal_gst_amount").val(gstAmount.toFixed(2));
        $("#modal_total_price").val(totalPrice.toFixed(2));
    
        // Total (quantity * per unit)
        $("#modal_total_basic_price").val((basePrice * qty).toFixed(2));
        $("#modal_total_gst_amount").val((gstAmount * qty).toFixed(2));
        $("#modal_total_purchase_price").val((totalPrice * qty).toFixed(2));
    }
    
    // Trigger calculation when relevant inputs change
    $('#modal_purchase_price, #qty, #modal_gst,#tax_rule')
    .on('keyup change', function() {
        calculateProductModal();
    });
    
    
    
    function openadjustModal(invid,product_type, product_code,description,store_id) 
    {
        $.ajax({
            url: "{{ route('admin.getglasssels') }}",
            method: 'GET',
            data: { 
                product_type: product_type,
                product_code: product_code,
                description: description,
                store_id: store_id 
            },
            success: function(response) {
                let tableBody = $('#salesorderTable tbody');
                tableBody.empty();
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(sales) {
                        let row = `
                            <tr>
                                <td>
                                    <input type="radio" class="salesradio"
                                        name="salesproductid"
                                        value="${sales.id}"
                                        data-invid="${invid}"
                                        data-stid="${store_id}">
                                </td>
                                <td>${sales.sale_date}</td>
                                <td>${sales.order_no}</td>
                                <td>${sales.product_deatils}</td>
                                <td>${sales.purchase_price}</td>
                                <td>${sales.rightleft_glass}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } else {
                    tableBody.append('<tr><td colspan="5" class="text-center">No Sales found.</td></tr>');
                }
    
                $('.salesradio').prop('checked', false);
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch sales details.',
                    timeout: 3000
                });
            }
        });
    
        $('#addadjustModal').modal('show');
    }
    
    
    $(document).on('change', '.salesradio', function () {

        let salesProductId = $(this).val();
		let invid = $(this).data('invid');
		let stid = $(this).data('stid');
		

        if (!salesProductId) {

             $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Please select a sales product.',
                    timeout: 3000
                });
            return;
        }
		
		
		$('#product_type_glass').text('');
        $('#product_code_glass').text('');
        $('#product_id_glass').text('');
        $('#description_glass').text('');
        $('#store_name_glass').text('');
		$('#orderno_glass').text('');
		$('#order_date_glass').text('');
		$('#position_glass').text('');
    
        $.ajax({
            url: "{{ route('admin.getsalesproductwisedetails') }}", 
            method: "GET",
            data: {
                salesProductId: salesProductId
            },
            success: function (response) {

                if (response.status === true) 
                {
            
                    $('#addadjustModal').modal('hide');
            
                    let sales   = response.sales;
                    let summary = response.summary;
            
                    $('#store_name_glass').text(
                        response.store_name ? 'Store Name : ' + response.store_name  : ''
                    );
            
                    $('#product_type_glass').text(
                        sales?.product_type ? 'Product Type : ' + sales.product_type : ''
                    );
            
                    $('#product_code_glass').text(
                        sales?.product_code ? 'Product Code : ' + sales.product_code : ''
                    );
            
                    $('#product_id_glass').text(
                        sales?.product_id ? 'Product Id : ' + sales.product_id : ''
                    );
            
                    $('#description_glass').text(
                        sales?.product_deatils ? 'Product Description : ' + sales.product_deatils : ''
                    );
            
                    $('#orderno_glass').text(
                        summary?.order_no ? 'Order No : ' + summary.order_no : ''
                    );
            
                    $('#order_date_glass').text(
                        summary?.sale_date ? 'Order Date : ' + summary.sale_date : ''
                    );
            
                    $('#position_glass').text(
                        response.position ? 'Position : ' + response.position : ''
                    );
            
                    document.getElementById('salesProductId').value = sales.id;
                    document.getElementById('invid').value = invid;
                    document.getElementById('stid').value = stid;
            
                    $('#adjuststock').modal('show');
            
                } else {
                    $.toaster({
                        priority: 'danger',
                        title: 'Error',
                        message: response.message || 'Invalid selection.',
                        timeout: 3000
                    });
                    $('.salesradio').prop('checked', false);
                }
            }

        });
    });

    
    
    $("#inventoryForm").submit(function (e) {
        e.preventDefault();
    
        let isValid = true;
        let class_name = '';
    
        // Reset old errors
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        // Collect fields

        let qty      = document.getElementById("qty" + class_name).value.trim();
        let tax_rule      = document.getElementById("tax_rule" + class_name).value.trim();
    
        // Validation
        if (qty === "") {
            document.getElementById("qtyError" + class_name).textContent = "Quantity required.";
            document.getElementById("qty" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (tax_rule === "") {
            document.getElementById("tax_ruleError" + class_name).textContent = "Select Tax Type.";
            document.getElementById("tax_rule" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        

    
        if (!isValid) return;
    
        // Submit via AJAX
        let form = $("#inventoryForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.add-inventory-product-wise') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function (response) {
                if ($.isEmptyObject(response.error)) {
                    $.toaster({
                        priority: "success",
                        title: response.success,
                        message: ""
                    });
                    window.location.href = "{{ route('admin.inventory-level') }}";
                } else {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    });
    
    
    $(document).on('click', '#getoldvalue', function() 
    {
        let productType = $("#producttype_p").val();
        let productCode = $("#productcode_p").val();
        let store_id = $("#storeid_p").val();
        
        $.ajax({
            url: "{{ route('admin.get-old-value') }}",  
            method: 'GET',
            data: { productType: productType,productCode: productCode,store_id: store_id },
            success: function(response) {
                let tableBody = $('#oldvalueTable tbody');
                tableBody.empty(); // Clear old data
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(purchasevalue) 
                    {
                         function formatValue(value) {
                          return value === null || value === undefined || value === "" ? "-" : value;
                        }
    
                        let row = `
                            <tr>
                                <td>
                                <input type="radio" name="prescriptioneyetest" class="oldvalue-purchase"
                                        value="1"
                                        data-product_price="${purchasevalue.product_price}"
                                        data-product_retail_price="${purchasevalue.product_retail_price}">
                                </td>
                                <td>${purchasevalue.supplier_name}</td>
                                <td>${purchasevalue.purchase_date}</td>
                                <td>${purchasevalue.product_details}</td>
                                <td>${purchasevalue.product_price}</td>
                                <td>${purchasevalue.product_retail_price}</td>
                                <td>${purchasevalue.hsn_code}</td>
                                <td>${purchasevalue.gst}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="5" class="text-center">No old value found.</td></tr>');
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch old value details.',
                    timeout: 3000
                });
            }
        });
        
        $('#OldvalueModal').modal('show');

        
    });
    
    
    $(document).on('click', '.oldvalue-purchase', function()
    {
        let product_price = $(this).data('product_price');
        let product_retail_price = $(this).data('product_retail_price');

        
        $('#product_price').val(product_price);
        $('#product_retail_price').val(product_retail_price);

         updateTotals();
        $('#OldvalueModal').modal('hide');
    });
    
    
    function toggleGSTFieldsPurchase() {
        let taxRule = $("#tax_rule_p").val();
    
        if (taxRule === "Not Applicable") {
    
            $("#hsn_code").closest(".col-md-4").hide();
            $("#gst").closest(".col-md-4").hide();
    
            $("#gst_amt").closest(".col-md-4").hide();
            $("#total_gst_amount").closest(".col-md-4").hide();
            $("#total_basic_price").closest(".col-md-4").hide();
    
            $("#gst").val('');
            $("#gst_amt").val('0.00');
            $("#total_gst_amount").val('0.00');
    
        } else {
    
            $("#hsn_code").closest(".col-md-4").show();
            $("#gst").closest(".col-md-4").show();
            $("#gst_amt").closest(".col-md-4").show();
            $("#total_gst_amount").closest(".col-md-4").show();
            $("#total_basic_price").closest(".col-md-4").show();
        }
    }
    
    function calculatePurchaseValues() {
    
        let product_type = $("#producttype_p").val();
    
        let qty = 0;
    
        if (product_type === "Lens") {
            qty = parseFloat($("#no_of_box").val()) || 0;
        } else {
            qty = parseFloat($("#qty_p").val()) || 0;
        }
    
        let purchasePrice = parseFloat($("#product_price").val()) || 0;
        let gstPercent = parseFloat($("#gst").val()) || 0;
        let taxRule = $("#tax_rule_p").val();
    
        let basicPrice = 0;
        let gstAmount = 0;
        let totalPurchase = 0;
    
        if (taxRule === "Include") {
            basicPrice = purchasePrice / (1 + (gstPercent / 100));
            gstAmount = purchasePrice - basicPrice;
    
        } else if (taxRule === "Exclude") {
            basicPrice = purchasePrice;
            gstAmount = (basicPrice * gstPercent) / 100;
    
        } else {
            basicPrice = purchasePrice;
            gstAmount = 0;
        }
    
        totalPurchase = basicPrice + gstAmount;
    
        $("#product_base_price").val(basicPrice.toFixed(2));
        $("#gst_amt").val(gstAmount.toFixed(2));
        $("#total_purchase").val(totalPurchase.toFixed(2));
    
        $("#total_basic_price").val((basicPrice * qty).toFixed(2));
        $("#total_gst_amount").val((gstAmount * qty).toFixed(2));
        $("#total_purchase_price").val((totalPurchase * qty).toFixed(2));
    
        let roundOff = parseFloat($("#round_off").val()) || 0;
    
        $("#total_net_purchase_price").val(((totalPurchase * qty) + roundOff).toFixed(2));
    }
    
    $(document).on("keyup change input", 
        "#no_of_box, #qty_p, #product_price, #gst, #round_off",
        function () {
            calculatePurchaseValues();
        }
    );
    
    $("#tax_rule_p").change(function () {
        toggleGSTFieldsPurchase();
        calculatePurchaseValues();
    });



    
    $('#supplier_name').on('keyup', function () {
        let query = $(this).val();
        if (query.length > 2) {
            $('#supplier_name').addClass('loading');
            $.ajax({
                url: "{{ route('admin.suppliername-dropdown') }}",
                type: "GET",
                data: { name: query },
                success: function (data) {
                    $('#supplier_name').removeClass('loading');
                    let dropdown = $('#supplierListName');
                    dropdown.empty();
                    if (data.length > 0) {
                        data.forEach(supplier => {
                            dropdown.append(`<a class="dropdown-item-list">${supplier.supplier_company}</a>`);
                        });
                        dropdown.show();
                    } else {
                        dropdown.hide();
                    }
                }
            });
        } else {
            $('#supplierListName').hide();
        }
    });

    $(document).on('click', '.dropdown-item-list', function () {
        $('#supplier_name').val($(this).text());
        $('#supplierListName').hide();
    });
    
    
    $("#purchaseForm").submit(function(e) {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let supplier_name = document.getElementById("supplier_name" + class_name).value.trim();
        let p_bill_no = document.getElementById("p_bill_no" + class_name).value.trim();
        let purchase_date = document.getElementById("purchase_date" + class_name).value.trim();
        let tax_rule_p = document.getElementById("tax_rule_p" + class_name).value.trim();
        let qty_p = document.getElementById("qty_p" + class_name).value.trim();
        let product_price = document.getElementById("product_price" + class_name).value.trim();

        if (supplier_name === "") {
            document.getElementById("supplier_nameError" + class_name).textContent = "Select Supplier Name.";
            document.getElementById("supplier_name" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (p_bill_no === "") {
            document.getElementById("p_bill_noError" + class_name).textContent = "Purchase Bill Number.";
            document.getElementById("p_bill_no" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (purchase_date === "") {
            document.getElementById("purchase_dateError" + class_name).textContent = "Select purchase date.";
            document.getElementById("purchase_date" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (tax_rule_p === "") {
            document.getElementById("tax_rule_pError" + class_name).textContent = "Select tax rule.";
            document.getElementById("tax_rule_p" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        if (qty_p === "") {
            document.getElementById("qty_pError" + class_name).textContent = "Quantity required.";
            document.getElementById("qty_p" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        if (product_price === "") {
            document.getElementById("product_priceError" + class_name).textContent = "Purchase price required.";
            document.getElementById("product_price" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    

    
        if (!isValid) {
            return;
        }

        let form = $("#purchaseForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.purchase-product-add') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
                if ($.isEmptyObject(response.error)) {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    window.location.href = "{{ route('admin.inventory-level') }}";
                } else {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
    });
    
    
    $('#supplier_name_challan').on('keyup', function () {
        let query = $(this).val();
        if (query.length > 2) {
            $('#supplier_name_challan').addClass('loading');
            $.ajax({
                url: "{{ route('admin.suppliername-dropdown') }}",
                type: "GET",
                data: { name: query },
                success: function (data) {
                    $('#supplier_name_challan').removeClass('loading');
                    let dropdown = $('#supplierListNamechallan');
                    dropdown.empty();
                    if (data.length > 0) {
                        data.forEach(supplier => {
                            dropdown.append(`<a class="dropdown-item-challan">${supplier.supplier_company}</a>`);
                        });
                        dropdown.show();
                    } else {
                        dropdown.hide();
                    }
                }
            });
        } else {
            $('#supplierListName').hide();
        }
    });

    $(document).on('click', '.dropdown-item-challan', function () {
        $('#supplier_name_challan').val($(this).text());
        $('#supplierListNamechallan').hide();
    });
    
    
    $(document).ready(function () {

    // Function to show/hide GST related fields
    function toggleGSTFields() 
    {
        let taxRule = $("#tax_rule_c").val();

        if (taxRule === "Not Applicable") {
            // Hide GST related fields
            $("#hsn_code_challan").closest(".col-md-4").hide();
            $("#gst_challan").closest(".col-md-4").hide();

            // Hide calculated GST fields
            $("#gst_amt_challan").closest(".col-md-4").hide();
            $("#total_gst_amount_challan").closest(".col-md-4").hide();
            $("#total_basic_price_challan").closest(".col-md-4").hide();

            // Clear GST values
            $("#gst_challan").val('');
            $("#gst_amt_challan").val('0.00');
            $("#total_gst_amount_challan").val('0.00');

        } else {
            // Show GST related fields
            $("#hsn_code_challan").closest(".col-md-4").show();
            $("#gst_challan").closest(".col-md-4").show();
            $("#gst_amt_challan").closest(".col-md-4").show();
            $("#total_gst_amount_challan").closest(".col-md-4").show();
            $("#total_basic_price_challan").closest(".col-md-4").show();
        }
    }


    // GST Calculation function
    function calculateChallanValues() 
    {
        let product_type = $("#producttype_c").val();
        
        $.ajax({
            url: "{{ route('admin.get-gst-details') }}",
            type: "GET",
            data: {product_type:product_type},
            success: function (res) 
            {
                $("#hsn_code_challan").val(res.hsn_code);
                $("#gst_challan").val(res.percentage);
            }
            
        });
    
        let taxRule = $("#tax_rule_c").val();
        
        let qty = 0;

        if (product_type === "Lens") {
            qty = parseFloat($("#no_of_box_challan").val()) || 0;  
        } else {
            qty = parseFloat($("#qty_c").val()) || 0;         
        }
        
        let purchasePrice = parseFloat($("#product_price_challan").val()) || 0;
        let gstPercent = parseFloat($("#gst_challan").val()) || 0;

        let basicPrice = 0;
        let gstAmount = 0;
        let totalPurchase = 0;

        // ================================
        // Tax Rule Calculations
        // ================================
        if (taxRule === "Include") {
            // GST Included → Extract Basic Price
            basicPrice = purchasePrice / (1 + (gstPercent / 100));
            gstAmount = purchasePrice - basicPrice;

        } else if (taxRule === "Exclude") {
            // GST Excluded → Add GST
            basicPrice = purchasePrice;
            gstAmount = (basicPrice * gstPercent) / 100;

        } else {
            // Not Applicable
            basicPrice = purchasePrice;
            gstAmount = 0;
        }

        totalPurchase = basicPrice + gstAmount;

        // Assign values
        $("#product_base_price_challan").val(basicPrice.toFixed(2));
        $("#gst_amt_challan").val(gstAmount.toFixed(2));
        $("#total_purchase_challan").val(totalPurchase.toFixed(2));

        // Multiply by quantity
        $("#total_basic_price_challan").val((basicPrice * qty).toFixed(2));
        $("#total_gst_amount_challan").val((gstAmount * qty).toFixed(2));
        $("#total_net_purchase_challan").val((totalPurchase * qty).toFixed(2));
    }


    // Trigger functions
    $("#tax_rule_c").change(function () {
            toggleGSTFields();
            calculateChallanValues();
        });
    
        $("#qty_c, #product_price_challan, #gst_challan").on("keyup change", function () {
            calculateChallanValues();
        });
    
        // Initial load
        toggleGSTFields();
    });
    
    $(document).on("input change keyup", 
        "#no_of_box_challan, #qty_c, #product_price_challan, #gst_challan", 
        function () {
            calculateChallanValues();
        }
    );
    
    
    
    $("#challanForm").submit(function(e) {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let supplier_name_challan = document.getElementById("supplier_name_challan" + class_name).value.trim();
        let challan_no = document.getElementById("challan_no" + class_name).value.trim();
        let challan_date = document.getElementById("challan_date" + class_name).value.trim();
        let tax_rule_c = document.getElementById("tax_rule_c" + class_name).value.trim();
        let qty_c = document.getElementById("qty_c" + class_name).value.trim();

        if (supplier_name_challan === "") {
            document.getElementById("supplier_name_challanError" + class_name).textContent = "Select Supplier Name.";
            document.getElementById("supplier_name_challan" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (challan_no === "") {
            document.getElementById("challan_noError" + class_name).textContent = "Challan Bill Number.";
            document.getElementById("challan_no" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (challan_date === "") {
            document.getElementById("challan_dateError" + class_name).textContent = "Select challan date.";
            document.getElementById("challan_date" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (tax_rule_c === "") {
            document.getElementById("tax_rule_cError" + class_name).textContent = "Select tax rule.";
            document.getElementById("tax_rule_c" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        if (qty_c === "") {
            document.getElementById("qty_cError" + class_name).textContent = "Quantity required.";
            document.getElementById("qty_c" + class_name).classList.add("is-invalid");
            isValid = false;
        }

    

    
        if (!isValid) {
            return;
        }

        let form = $("#challanForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.challan-product-add') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
                if ($.isEmptyObject(response.error)) {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    window.location.href = "{{ route('admin.inventory-level') }}";
                } else {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
    });
    
    
    $(document).on('click', '#getoldvaluechallan', function() 
    {
        let productType = $("#producttype_c").val();
        let productCode = $("#productcode_c").val();
        let store_id = $("#storeid_p").val();
        
        $.ajax({
            url: "{{ route('admin.get-old-value') }}",  
            method: 'GET',
            data: { productType: productType,productCode: productCode,store_id: store_id },
            success: function(response) {
                let tableBody = $('#oldvaluechallanTable tbody');
                tableBody.empty(); // Clear old data
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(purchasevalue) 
                    {
                         function formatValue(value) {
                          return value === null || value === undefined || value === "" ? "-" : value;
                        }
    
                        let row = `
                            <tr>
                                <td>
                                <input type="radio" name="prescriptioneyetest" class="oldvalue-challan"
                                        value="1"
                                        data-product_price="${purchasevalue.product_price}"
                                        data-product_retail_price="${purchasevalue.product_retail_price}">
                                </td>
                                <td>${purchasevalue.supplier_name}</td>
                                <td>${purchasevalue.purchase_date}</td>
                                <td>${purchasevalue.product_details}</td>
                                <td>${purchasevalue.product_price}</td>
                                <td>${purchasevalue.product_retail_price}</td>
                                <td>${purchasevalue.hsn_code}</td>
                                <td>${purchasevalue.gst}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="5" class="text-center">No old value found.</td></tr>');
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch old value details.',
                    timeout: 3000
                });
            }
        });
        
        $('#OldvaluechallanModal').modal('show');

        
    });
    
    
    $(document).on('click', '.oldvalue-challan', function()
    {
        let product_price = $(this).data('product_price');
        let product_retail_price = $(this).data('product_retail_price');

        
        $('#product_price_challan').val(product_price);
        $('#product_retail_price_challan').val(product_retail_price);

         updateTotals();
        $('#OldvaluechallanModal').modal('hide');
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
