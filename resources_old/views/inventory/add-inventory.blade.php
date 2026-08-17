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

.suggestion-box {
    z-index: 9999;
    max-height: 200px;
    overflow-y: auto;
}
.col-md-2
{
    margin-bottom: 10px;
}

.col-md-3
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
                        <h3>Add Inventory</h3>
                        <a href="{{route('admin.inventory-level')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                             Inventory List
                        </a>
                    </div>
                </div>
            </div>
             <div class="row">
                <div class="col-md-12">
                    <p style="color:red">Select product type to show respective product parameters</p>
                </div>
                
            </div>
            <form id="inventoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="row mb-3">
                
                <div class="col-md-3">
                     <label for="">Select Product <span class="text-danger">*</span></label>
                    <select class="form-control select product-type" style="height: 32px !important;" id="product_type" name="product_type">
                        <option value="">Select Product </option>
                        <option value="Frame">Frame</option>
                        <option value="Glass">Glass</option>
                        <option value="Goggles">Goggles</option>
                        <option value="Lens">Contact Lens</option>
                        <option value="Solution">Solution</option>
                        <option value="Other">Other</option>
                    </select>
                    <span class="error badge text-danger" id="product_typeError"></span>
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
                    <label for="">Tax Rule <span class="text-danger">*</span></label>
                    <select class="form-control select" style="height: 32px !important;" id="tax_rule" name="tax_rule">
                           <option value="Include">Include</option>
                          <option value="Exclude ">Exclude</option>
                          <option value="Not Appicable">Not Appicable</option>
                    </select>
                    <span class="error badge text-danger" id="tax_ruleError"></span>
                </div>
                
            </div>
            <div class="row" id="pcode" style="display:none">
                    <div class="col-md-3">
                        <label>Product Code <span class="text-danger">*</span></label>
                        <input type="text" id="modal_product_code" name="modal_product_code" class="form-control">
                        <div class="suggestion-box list-group" style="display:none; position:absolute; z-index:1000;"></div>
                         <span class="error badge text-danger" id="modal_product_codeError"></span>

                    </div>
                    
                    <input type="hidden" id="modal_product_id" name="modal_product_id" class="form-control">
                </div>
                <!----  FRAME OR GOGGLES --------->
                <div class="row" id="FrameDive" style="display:none;">
                    <div class="col-md-2">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" id="modal_frame_name" name="modal_frame_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_frame_company" name="modal_frame_company" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quality </label>
                        <input type="text" id="modal_frame_quality" name="modal_frame_quality" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quantity  </label>
                        <input type="text" id="modal_frame_qty" name="modal_frame_qty" value="1" class="form-control">
                    </div>
                </div>
                
                <!----  GLASS --------->
                <div class="row" id="GlassDiveA" style="display:none;">
                    <div class="col-md-4">
                        <label>Details</label>
                        <input type="text" id="modal_glass_details" name="modal_glass_details" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_glass_company" name="modal_glass_company" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quality </label>
                        <input type="text" id="modal_glass_quality" name="modal_glass_quality" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quantity  </label>
                        <input type="text" id="modal_glass_qty" name="modal_glass_qty" value="1" class="form-control">
                    </div>
                </div>
                <div class="row" id="GlassDiveB" style="display:none;">
                    <div class="col-md-2">
                        <label>Color </label>
                        <input type="text" id="modal_glass_color" name="modal_glass_color" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Material </label>
                        <input type="text" id="modal_glass_Material" name="modal_glass_Material" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Coating  </label>
                        <input type="text" id="modal_glass_Coating" name="modal_glass_Coating" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Design  </label>
                        <input type="text" id="modal_glass_Design" name="modal_glass_Design" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Index  </label>
                        <input type="text" id="modal_glass_Index" name="modal_glass_Index" class="form-control">
                    </div>
                </div>
                <div class="row" id="GlassDiveC" style="display:none;">
                    <div class="col-md-2">
                        <label>SPH  </label>
                        <input type="text" id="modal_glass_SPH" name="modal_glass_SPH" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>CYL  </label>
                        <input type="text" id="modal_glass_CYL" name="modal_glass_CYL" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Addition   </label>
                        <input type="text" id="modal_glass_Addition" name="modal_glass_Addition" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Axis   </label>
                        <input type="text" id="modal_glass_Axis" name="modal_glass_Axis" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Consider As Pair  </label>
                    </div>
                </div>
                
                <!------------------- CONTACT LENS ------------------------>
                <div class="row" id="LensDiveA" style="display:none;">
                    <div class="col-md-2">
                        <label>Product Name</label>
                        <input type="text" id="modal_lens_product_name" name="modal_lens_product_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_lens_company" name="modal_lens_company" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
                        <label>Color  </label>
                        <input type="text" id="modal_lens_color" name="modal_lens_color"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Number</label>
                        <input type="text" id="modal_lens_number" name="modal_lens_number"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>CT (Center Thickness)</label>
                        <input type="text" id="modal_lens_tc" name="modal_lens_tc"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Type</label> 
                        <input type="text" id="modal_lens_type" name="modal_lens_type" class="form-control">
                    </div>
                </div>
                <div class="row" id="LensDiveB" style="display:none;">
                    
                    <div class="col-md-2">
                        <label>Materials</label>
                        <input type="text" id="modal_lens_Materials" name="modal_lens_Materials"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Validity In Days</label>
                        <input type="text" id="modal_lens_validity" name="modal_lens_validity"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>SPH</label>
                        <input type="text" id="modal_lens_sph" name="modal_lens_sph"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>CYL </label>
                        <input type="text" id="modal_lens_cyl" name="modal_lens_cyl"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Addition </label>
                        <input type="text" id="modal_lens_addition" name="modal_lens_addition"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Axis  </label>
                        <input type="text" id="modal_lens_axis" name="modal_lens_axis"  class="form-control">
                    </div>
                </div> 
                <div class="row" id="LensDiveC" style="display:none;">
                    
                    <div class="col-md-2">
                        <label>Base Curves (BC)  </label>
                        <input type="text" id="modal_lens_bc" name="modal_lens_bc"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Diameter (DIA) </label>
                        <input type="text" id="modal_lens_diameter" name="modal_lens_diameter"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Power Type</label>
                        <input type="text" id="modal_lens_powertype" name="modal_lens_powertype"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quality </label>
                        <input type="text" id="modal_lens_quality" name="modal_lens_quality"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Batch Number </label>
                        <input type="text" id="modal_lens_batch" name="modal_lens_batch"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Mfg Date </label>
                        <input type="date" id="modal_lens_mfg" name="modal_lens_mfg"  class="form-control">
                    </div>
                </div> 
                <div class="row" id="LensDiveD" style="display:none;">
                    
                    <div class="col-md-2">
                        <label>Expiry Date</label>
                        <input type="date" id="modal_lens_expiry" name="modal_lens_expiry"  class="form-control">
                    </div>
                    <div class="col-md-2 box-div">
                      <label>No of Boxes</label>
                      <input type="number" class="form-control box-detail" id="modal_noofbox" name="modal_noofbox">
                    </div>
                    
                    <div class="col-md-2 perbox-div">
                      <label>Pieces Per Box</label>
                      <input type="number" class="form-control perbox-detail" id="modal_perbox" name="modal_perbox">
                    </div>
                    <div class="col-md-2">
                        <label>Quantity  </label>
                        <input type="text" id="modal_lens_quantity"  class="form-control" name="modal_lens_quantity">
                    </div>
                    <div class="col-md-4">
                        <label>Invoice Description  <span class="text-danger">*</span></label>
                        <input type="text" id="modal_lens_invoicedescription"  class="form-control">
                    </div>
                </div> 
                
                 <!------------------- SOLUTION LENS ------------------------>
                <div class="row" id="SolutionDiveA" style="display:none;">
                    <div class="col-md-2">
                        <label>Product Name</label>
                        <input type="text" id="modal_solution_product_name" name="modal_solution_product_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_solution_company" name="modal_solution_company" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
                        <label>Color  </label>
                        <input type="text" id="modal_solution_color" name="modal_solution_color" class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Variant </label>
                        <input type="text" id="modal_solution_Variant" name="modal_solution_Variant"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Packing Type </label>
                        <input type="text" id="modal_solution_packingtype" name="modal_solution_packingtype"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quality     </label>
                        <input type="text" id="modal_solution_quality" name="modal_solution_quality" class="form-control">
                    </div>
                </div>
                <div class="row" id="SolutionDiveB" style="display:none;">
                    <div class="col-md-2">
                        <label>Quantity      </label>
                        <input type="text" id="modal_solution_quantity" name="modal_solution_quantity" value="1"  class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Invoice Description  <span class="text-danger">*</span></label>
                        <input type="text" id="modal_solution_invoicedescription" name="modal_solution_invoicedescription"  class="form-control">
                    </div>
                </div>  
                
                <!------------------- OTHER LENS ------------------------>
                <div class="row" id="OtherDiveA" style="display:none;">
                    <div class="col-md-2">
                        <label>Product Name</label>
                        <input type="text" id="modal_other_product_name" name="modal_other_product_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_other_company" name="modal_other_company" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
                        <label>Color  </label>
                        <input type="text" id="modal_solution_color" name="modal_solution_color"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Type </label>
                        <input type="text" id="modal_other_type" name="modal_other_type"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Shape </label>
                        <input type="text" id="modal_other_shape" name="modal_other_shape"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Size     </label>
                        <input type="text" id="modal_other_size" name="modal_other_size"  class="form-control">
                    </div>
                </div>
                <div class="row" id="OtherDiveB" style="display:none;">
                    <div class="col-md-2">
                        <label>Quality     </label>
                        <input type="text" id="modal_other_quality" name="modal_other_quality" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quantity      </label>
                        <input type="text" id="modal_other_quantity" name="modal_other_quantity" value="1"  class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Invoice Description  <span class="text-danger">*</span></label>
                        <input type="text" id="modal_other_invoicedescription" name="modal_other_invoicedescription"  class="form-control">
                    </div>
                </div>
                
                <!------------------- COMMON FOR ALL ------------------------>
                <div id="common" style="display:none">
                    <div class="row">
                        <div class="col-md-2"> <button class="btn btn-primary" type="button" id="getoldvalue">Get Old Purchase Value</button></div>
                        <div class="col-md-4">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                                Purchase Rs 
                              </label>
                              <div class="col-lg-8">
                                <input type="text" id="modal_purchase_price" name="modal_purchase_price" class="form-control" placeholder="Enter amount">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                                Retail Price 
                              </label>
                              <div class="col-lg-8">
                                <input type="text" id="modal_retail_price" name="modal_retail_price" class="form-control" placeholder="Enter amount">
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-6 col-form-label">
                                HSN/SAC Code 
                              </label>
                              <div class="col-lg-6">
                                <input type="text" id="modal_hsn_code" name="modal_hsn_code" class="form-control" placeholder="">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                           <div class="row">
                              <label for="modal_purchase_price" name="modal_purchase_price" class="col-lg-5 col-form-label">
                                GST %  
                              </label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_gst" name="modal_gst" class="form-control">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-6">
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
                

                <div class="row">
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Add Inventory</button>
                    </div>
                </div>
            </form>
            

        </div>
    </div>
</section>
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
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $(document).on("change", ".product-type", function () 
    {
        let $row = $(this).closest("tr");
        let selectedType = $(this).val();
        
        $.ajax({
            url: "{{ route('admin.get-gst-details') }}",
            type: "GET",
            data: {product_type:selectedType},
            beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function (res) 
            {
               
                $("#modal_hsn_code").val(res.hsn_code);
                $("#modal_gst").val(res.percentage);
                handleProductType(selectedType);
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
            
        });

    });
    
    
    /** =================================
     *  Modal Div Handle Product Type Wise
     * ================================== */
     
    function handleProductType(type)
    {
        switch (type) 
        {
            case "Frame":
                $("#FrameDive,#common,#pcode").show();
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;
                
            case "Goggles":
                $("#FrameDive,#common,#pcode").show();
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;     
             case "Glass":
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB,#common,#pcode").show();
                $("#FrameDive").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;
            case "Lens":
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#FrameDive").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD,#common,#pcode").show();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;
             case "Solution":
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#FrameDive").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB,#common,#pcode").show();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;  
             case "Other":
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#FrameDive").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#OtherDiveA,#OtherDiveB,#common,#pcode").show();
                break;     
        }
    }
    
    /** ==============================
     *  Product Code Wise Product Details
     *  ============================== */
     
    $(document).on('keyup', '#modal_product_code', function () {
        let $input = $(this);
        let productCode = $input.val();
        let productType = $("#product_type").val();
    
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
        let productType = $("#product_type").val();

    
        $input.val(selectedCode);
        $this.closest('.suggestion-box').hide();
    

        $.ajax({
            url: "{{ route('admin.get-product-details') }}",
            method: 'GET',
            data: {
                product_type: productType,
                productdetails: selectedCode
            },
            success: function (res) 
            {
                $("#modal_product_code").val(res.product_code);
                $("#modal_product_id").val(res.product_id);
                $("#modal_frame_name").val(res.product_name);
                $("#modal_frame_company").val(res.Company);
                $("#modal_frame_quality").val(res.Quality);
                $('input[name="modal_Track_Inventory"][value="' + res.track_inventory + '"]').prop('checked', true);
                $('input[name="modal_Negative_Inventory"][value="' + res.Allow_Negative_Inventory + '"]').prop('checked', true);
                $("#modal_purchase_price").val(res.Purchase_Price);
                $("#modal_retail_price").val(res.Retail_Price);
                $("#modal_glass_details").val(res.product_name);
                $("#modal_glass_company").val(res.Company);
                $("#modal_glass_quality").val(res.Quality);
                $("#modal_glass_color").val(res.Color);
                $("#modal_glass_Material").val(res.Material);
                $("#modal_glass_Coating").val(res.Coating);
                $("#modal_glass_Design").val(res.Design);
                $("#modal_glass_Index").val(res.Index);
                $("#modal_glass_SPH").val(res.SPH);
                $("#modal_glass_CYL").val(res.CYL);
                $("#modal_glass_Addition").val(res.ADD);
                $("#modal_glass_Axis").val(res.AXIS);
                
                $("#modal_lens_product_name").val(res.product_name);
                $("#modal_lens_company").val(res.Company);
                $("#modal_lens_quality").val(res.Quality);
                $("#modal_lens_color").val(res.Color);
                $("#modal_lens_number").val(res.LNumber);
                $("#modal_lens_tc").val(res.CT);
                $("#modal_lens_type").val(res.PType);
                $("#modal_lens_Materials").val(res.Material);
                $("#modal_lens_validity").val(res.Validity);
                $("#modal_lens_sph").val(res.SPH);
                $("#modal_lens_cyl").val(res.CYL);
                $("#modal_lens_addition").val(res.ADD);
                $("#modal_lens_axis").val(res.AXIS);
                $("#modal_lens_bc").val(res.base_carve);
                $("#modal_lens_diameter").val(res.Diameter);
                $("#modal_lens_powertype").val(res.Power_Type);
                $("#modal_lens_quality").val(res.Quality);
                $("#modal_lens_batch").val(res.Batch_Number);
                $("#modal_lens_mfg").val(res.Mfg_Date);
                $("#modal_lens_expiry").val(res.Expiry_Date);
                
                $("#modal_solution_product_name").val(res.product_name);
                $("#modal_solution_company").val(res.Company);
                $("#modal_solution_quality").val(res.Quality);
                $("#modal_solution_color").val(res.Color);
                $("#modal_solution_Variant").val(res.Variant);
                $("#modal_solution_packingtype").val(res.Packing_Type);
                
                $("#modal_other_product_name").val(res.product_name);
                $("#modal_other_company").val(res.Company);
                $("#modal_other_color").val(res.Color);
                $("#modal_other_type").val(res.PType);
                $("#modal_other_shape").val(res.Shape);
                $("#modal_other_size").val(res.Size);
                $("#modal_other_quality").val(res.Quality);

                

                calculateProductModal();
            }
            
        });
    });
    
    $(document).on('click', '#getoldvalue', function() 
    {
        let productType = $("#product_type").val();
        let productCode = $("#modal_product_code").val();
        
        $.ajax({
            url: "{{ route('admin.get-old-value') }}",  
            method: 'GET',
            data: { productType: productType,productCode: productCode },
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

        
        $('#modal_purchase_price').val(product_price);
        $('#modal_retail_price').val(product_retail_price);

         calculateProductModal();
        $('#OldvalueModal').modal('hide');
    });
    

    
    $(document).on('keyup', '#modal_noofbox, #modal_perbox', function () {
        let boxCount = parseInt($('#modal_noofbox').val()) || 0;
        let perBox = parseInt($('#modal_perbox').val()) || 0;
    
        let totalPieces = boxCount * perBox;
    
        $('#modal_lens_quantity').val(totalPieces);
    });

        
    function calculateProductModal() {
        const taxType = $('#tax_rule').val()?.trim() || '';
        const gstRate = parseFloat($('#modal_gst').val()) || 0;
        const purchasePrice = parseFloat($('#modal_purchase_price').val()) || 0;
        const product_type = $('#product_type').val()?.trim() || '';
    
        let qty = 0;
    
        if (product_type === 'Lens') {
            qty = parseFloat($('#modal_noofbox').val()) || 0;
        } else if (product_type === 'Frame' || product_type === 'Goggles') {
            qty = parseFloat($('#modal_frame_qty').val()) || 0;
        } else if (product_type === 'Glass') {
            qty = parseFloat($('#modal_glass_qty').val()) || 0;
        } else if (product_type === 'Solution') {
            qty = parseFloat($('#modal_solution_quantity').val()) || 0;
        } else if (product_type === 'Other') {
            qty = parseFloat($('#modal_other_quantity').val()) || 0;
        }
    
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
    $('#modal_purchase_price, #modal_frame_qty, #modal_glass_qty, #modal_noofbox, #modal_solution_quantity, #modal_other_quantity, #modal_gst, #tax_rule, #product_type')
    .on('keyup change', function() {
        calculateProductModal();
    });

    $("#inventoryForm").submit(function (e) {
        e.preventDefault();
    
        let isValid = true;
        let class_name = '';
    
        // Reset old errors
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        // Collect fields

        let store_id      = document.getElementById("store_id" + class_name).value.trim();
        let product_type      = document.getElementById("product_type" + class_name).value.trim();
        let tax_rule      = document.getElementById("tax_rule" + class_name).value.trim();
    
        // Validation
        if (store_id === "") {
            document.getElementById("store_idError" + class_name).textContent = "Select store Name.";
            document.getElementById("store_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (product_type === "") {
            document.getElementById("product_typeError" + class_name).textContent = "Select Product.";
            document.getElementById("product_type" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
    
        if (tax_rule === "") {
            document.getElementById("tax_ruleError" + class_name).textContent = "Select Tax Type.";
            document.getElementById("tax_rule" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        let modal_product_code = document.getElementById("modal_product_code" + class_name).value.trim();


        if (modal_product_code === "") {
            document.getElementById("modal_product_codeError" + class_name).textContent = "Product Code required.";
            document.getElementById("modal_product_code" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        
        
    
        if (!isValid) return;
    
        // Submit via AJAX
        let form = $("#inventoryForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.add-inventory-record') }}",
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
    
    
    
});           
</script>





@endsection
