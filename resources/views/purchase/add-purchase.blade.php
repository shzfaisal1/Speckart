@extends('layouts.master')
@section('styles')
  
<style>
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
 @php $tbl_setting_frame =  DB::table("tbl_product_code_setting")->where('product_type','Frame')->first();   @endphp
 @php $tbl_setting_goggles =  DB::table("tbl_product_code_setting")->where('product_type','Goggles')->first();   @endphp
 @php $tbl_setting_glass =  DB::table("tbl_product_code_setting")->where('product_type','Glass')->first();   @endphp
 @php $tbl_setting_lens =  DB::table("tbl_product_code_setting")->where('product_type','Lens')->first();   @endphp
 @php $tbl_setting_solution =  DB::table("tbl_product_code_setting")->where('product_type','Solution')->first();   @endphp
 @php $tbl_setting_other =  DB::table("tbl_product_code_setting")->where('product_type','Other')->first();   @endphp
 
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
                    <h3>Add  Purchase</h3>
                     @if ($usr->can('Purchase-History'))
                    <a href="{{route('admin.purchase-history')}}" class=" btn">
                        <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                        Purchase List
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card" style="margin-top:10px">
                   <div class="card-body" style="padding: 15px 10px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger ml-0 mr-0">
                            <ul class="mb-0">
                                <li>All fields marked with * are mandatory.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form id="purchaseForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Purchase Date <span class="text-danger">*</span></label>
                            <div id="reportrange" class="pull-left"
                                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;width: 255px !important;border-radius: 8px;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                            <input type="hidden" class="form-control" id="date_from" name="date_from">
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
                                  <option value="">Select Tax</option>
                                  <option value="Include">Include</option>
                                  <option value="Exclude ">Exclude</option>
                                  <option value="Not Applicable">Not Applicable</option>
                            </select>
                            <span class="error badge text-danger" id="tax_ruleError"></span>
                        </div>
                        
                        
                    </div>
                    <br>
                    <div class="col-md-12">
                        <div class="row" style="background: aliceblue;">
                            <div class="form-group col-md-12 table-responsive">
                                <table class="table datatables-basic w-100 table-bordered align-middle" id="saleTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Product Code</th>
                                            <th>Product Description</th>
                                            <th>Unit Price</th>
                                            <th>Base Price</th>
                                            <th>HSN/SAC Code</th>
                                            <th>GST %</th>
                                            <th>Purchase Price</th>
                                            <th>Qty</th>
                                            <th>Total Purchase Price</th>
                                            <th>Retail Price</th>
                                            <th>--</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <select class="form-control product-type" style="height: 32px !important;width:130px" name="product_type[]">
                                                    <option value="">Select Product</option>
                                                    <option value="Frame">Frame</option>
                                                    <option value="Glass">Glass</option>
                                                    <option value="Goggles">Goggles</option>
                                                    <option value="Lens">Contact Lens</option>
                                                    <option value="Solution">Solution</option>
                                                    <option value="Other">Other</option>
                                                </select>

                                            </td>
                                            <td class="tax-col">
                                                <input type="text" style="width:120px" class="form-control product-code" name="product_code[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-id" name="product_id[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-name" name="product_name[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-company" name="product_company[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-quality" name="product_quality[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-color" name="product_color[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-material" name="product_material[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-coating" name="product_coating[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-design" name="product_design[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-index" name="product_index[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-sph" name="product_sph[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-cyl" name="product_cyl[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-addition" name="product_addition[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-axis" name="product_axis[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-number" name="product_number[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-tc" name="product_tc[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-lenstype" name="product_lenstype[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-validity" name="product_validity[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-bc" name="product_bc[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-diameter" name="product_diameter[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-powertype" name="product_powertype[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-batch" name="product_batch[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-mfg" name="product_mfg[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-expiry" name="product_expiry[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-noofbox" name="product_noofbox[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-perbox" name="product_perbox[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-invoicedescription" name="product_invoicedescription[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-variant" name="product_variant[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-shape" name="product_shape[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control product-size" name="product_size[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control barcode-option" name="barcode_option[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control negative-inventory" name="negative_inventory[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control track-inventory" name="track_inventory[]" readonly>
                                                <input type="hidden" style="width:120px" class="form-control ispair" name="ispairglass[]" readonly>

                                            </td>
                                            <td>
                                                <input type="text"  style="width:300px"  class="form-control product-description" name="product_description[]" placeholder="Enter Product Description" readonly>
                                                <input type="hidden"  style="width:300px"  class="form-control product-details" name="product_details[]" placeholder="Enter Product Description" readonly>
                                            </td>
                                            <td class="tax-col">
                                                <input type="text" style="width:80px" class="form-control unit-price" name="product_price[]" placeholder="0.00" readonly>
                                            </td>
                                            <td class="tax-col">
                                                <input type="text" style="width:80px" class="form-control base-price" name="product_base_price[]" placeholder="0.00" readonly>
                                            </td>
                                             <td class="tax-col">
                                                <input type="text" style="width:80px" class="form-control hsn-code" name="hsn_code[]" readonly>
                                            </td>
                                            <td>
                                               <div class="input-group mb-3" style="width:120px" >
                                                    <input type="text" class="form-control gst-amount" name="gst_amt[]" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                                                    <input type="text" class="form-control gst" value="0"  name="gst[]" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" style="width:80px" class="form-control product-purchase-price" name="product_purchase_price[]" placeholder="0.00" readonly>
                                            </td>
                                            <td>
                                                <input type="text" style="width:50px" class="form-control product-qty" name="product_qty[]" value="0" readonly>
                                            </td>
                                            <td>
                                                <input type="text" style="width:80px" class="form-control total-purchase-price" name="total_purchase_price[]" placeholder="0.00" readonly>
                                            </td>
                                           <td>
                                                <input type="text" style="width:80px" class="form-control retail-price" name="product_retail_price[]" placeholder="0.00" readonly>
                                            </td>
                                            <td><button type="button" class="removeBtn">❌</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group col-md-12">
                                <button type="button" id="addRowBtn">➕ Add Row</button>
                            </div> 
                        </div>
                    </div>
                    
                    <div class="add-purchase-pg">
                        <div class="add-purchase-pg1">
                            <div class="row mb-2"> 
                                <div class="col-sm-5">
                                    <label for="example-text-input" class="col-form-label">Total Quantity</label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" type="text"  id="total_qty" name="total_qty">
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <label for="example-text-input" class="col-form-label">Total Unit Amount</label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" type="text"  id="total_unit_amount" name="total_unit_amount" readonly>
                                </div>
                            </div>
                            <div class="row mb-2" id="totalbasediv">
                                <div class="col-sm-5">
                                    <label for="example-text-input" class="col-form-label">Total Base Price</label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" type="text"  id="total_base_amount" name="total_base_amount" readonly>
                                </div>
                            </div>
                            <div class="row mb-2" id="totalgstdiv">
                                <div class="col-sm-5">
                                    <label for="example-text-input" class="col-form-label">Total GST Amount</label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" type="text"  id="total_gst_amount" name="total_gst_amount" readonly>
                                </div>   
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <label for="example-text-input" class="col-form-label">Total Purchase </label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" type="text"  id="total_p_amount" name="total_p_amount" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <label for="example-text-input" class="col-form-label">Round Off : (+/-) </label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" type="text" value="0.00"  id="round_off" name="round_off">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <label for="example-text-input" class="col-form-label">Total Net Purchase </label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" type="text" id="net_purchase_amount" name="net_purchase_amount">
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
         
    </div>
</section>



<div class="modal fade" id="productModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Product Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="row">
                    <div class="col-md-2">
                        <label>Product Code <span class="text-danger">*</span></label>
                        <input type="text" id="modal_product_code" class="form-control">
                        <div class="suggestion-box list-group" style="display:none; position:absolute; z-index:1000;"></div>

                    </div>
                    <div class="col-md-2">
                        <label>Product Type <span class="text-danger">*</span></label>
                        <input type="text" id="modal_product_type" class="form-control">

                    </div>
                    <input type="hidden" id="modal_product_id" class="form-control">
                </div>
                <!----  FRAME OR GOGGLES --------->
                <div class="row" id="FrameDive">
                    <div class="col-md-2">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" id="modal_frame_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_frame_company" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quality </label>
                        <input type="text" id="modal_frame_quality" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quantity  </label>
                        <input type="text" id="modal_frame_qty" value="1" class="form-control">
                    </div>
                </div>
                
                <!----  GLASS --------->
                <div class="row" id="GlassDiveA">
                    <div class="col-md-4">
                        <label>Details</label>
                        <input type="text" id="modal_glass_details" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_glass_company" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quality </label>
                        <input type="text" id="modal_glass_quality" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quantity  </label>
                        <input type="text" id="modal_glass_qty" value="1" class="form-control">
                    </div>
                </div>
                <div class="row" id="GlassDiveB">
                    <div class="col-md-2">
                        <label>Color </label>
                        <input type="text" id="modal_glass_color" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Material </label>
                        <input type="text" id="modal_glass_Material" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Coating  </label>
                        <input type="text" id="modal_glass_Coating" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Design  </label>
                        <input type="text" id="modal_glass_Design" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Index  </label>
                        <input type="text" id="modal_glass_Index" class="form-control">
                    </div>
                </div>
                <div class="row" id="GlassDiveC">
                    <div class="col-md-2">
                        <label>SPH  </label>
                        <input type="text" id="modal_glass_SPH" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>CYL  </label>
                        <input type="text" id="modal_glass_CYL" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Addition   </label>
                        <input type="text" id="modal_glass_Addition" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Axis   </label>
                        <input type="text" id="modal_glass_Axis" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Consider As Pair  </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="ispair" value="1">
                        </div>
                    </div>
                </div>
                
                <!------------------- CONTACT LENS ------------------------>
                <div class="row" id="LensDiveA">
                    <div class="col-md-2">
                        <label>Product Name</label>
                        <input type="text" id="modal_lens_product_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_lens_company" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
                        <label>Color  </label>
                        <input type="text" id="modal_lens_color"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Number   </label>
                        <input type="text" id="modal_lens_number"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>CT (Center Thickness)  </label>
                        <input type="text" id="modal_lens_tc"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Type    </label>
                        <input type="text" id="modal_lens_type"  class="form-control">
                    </div>
                </div>
                <div class="row" id="LensDiveB">
                    
                    <div class="col-md-2">
                        <label>Materials     </label>
                        <input type="text" id="modal_lens_Materials"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Validity In Days      </label>
                        <input type="text" id="modal_lens_validity"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>SPH</label>
                        <input type="text" id="modal_lens_sph"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>CYL </label>
                        <input type="text" id="modal_lens_cyl"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Addition </label>
                        <input type="text" id="modal_lens_addition"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Axis  </label>
                        <input type="text" id="modal_lens_axis"  class="form-control">
                    </div>
                </div> 
                <div class="row" id="LensDiveC">
                    
                    <div class="col-md-2">
                        <label>Base Curves (BC)  </label>
                        <input type="text" id="modal_lens_bc"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Diameter (DIA) </label>
                        <input type="text" id="modal_lens_diameter"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Power Type</label>
                        <input type="text" id="modal_lens_powertype"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quality </label>
                        <input type="text" id="modal_lens_quality"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Batch Number </label>
                        <input type="text" id="modal_lens_batch"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Mfg Date </label>
                        <input type="date" id="modal_lens_mfg"  class="form-control">
                    </div>
                </div> 
                <div class="row" id="LensDiveD">
                    
                    <div class="col-md-2">
                        <label>Expiry Date</label>
                        <input type="date" id="modal_lens_expiry"  class="form-control">
                    </div>
                    <div class="col-md-2 box-div">
                      <label>No of Boxes</label>
                      <input type="number" class="form-control box-detail" id="modal_noofbox">
                    </div>
                    
                    <div class="col-md-2 perbox-div">
                      <label>Pieces Per Box</label>
                      <input type="number" class="form-control perbox-detail" id="modal_perbox">
                    </div>
                    <div class="col-md-2">
                        <label>Quantity  </label>
                        <input type="text" id="modal_lens_quantity"  class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Invoice Description  <span class="text-danger">*</span></label>
                        <input type="text" id="modal_lens_invoicedescription"  class="form-control">
                    </div>
                </div> 
                
                 <!------------------- SOLUTION LENS ------------------------>
                <div class="row" id="SolutionDiveA">
                    <div class="col-md-2">
                        <label>Product Name</label>
                        <input type="text" id="modal_solution_product_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_solution_company" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
                        <label>Color  </label>
                        <input type="text" id="modal_solution_color"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Variant </label>
                        <input type="text" id="modal_solution_Variant"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Packing Type </label>
                        <input type="text" id="modal_solution_packingtype"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quality     </label>
                        <input type="text" id="modal_solution_quality"  class="form-control">
                    </div>
                </div>
                <div class="row" id="SolutionDiveB">
                    <div class="col-md-2">
                        <label>Quantity      </label>
                        <input type="text" id="modal_solution_quantity" value="1"  class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Invoice Description  <span class="text-danger">*</span></label>
                        <input type="text" id="modal_solution_invoicedescription"  class="form-control">
                    </div>
                </div>  
                
                <!------------------- OTHER LENS ------------------------>
                <div class="row" id="OtherDiveA">
                    <div class="col-md-2">
                        <label>Product Name</label>
                        <input type="text" id="modal_other_product_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Company </label>
                        <input type="text" id="modal_other_company" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
                        <label>Color  </label>
                        <input type="text" id="modal_solution_color"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Type </label>
                        <input type="text" id="modal_other_type"  class="form-control">
                    </div>
                     <div class="col-md-2">
                        <label>Shape </label>
                        <input type="text" id="modal_other_shape"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Size     </label>
                        <input type="text" id="modal_other_size"  class="form-control">
                    </div>
                </div>
                <div class="row" id="OtherDiveB">
                    <div class="col-md-2">
                        <label>Quality     </label>
                        <input type="text" id="modal_other_quality"  class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>Quantity      </label>
                        <input type="text" id="modal_other_quantity" value="1"  class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Invoice Description  <span class="text-danger">*</span></label>
                        <input type="text" id="modal_other_invoicedescription"  class="form-control">
                    </div>
                </div>
                
                <!------------------- COMMON FOR ALL ------------------------>
                <div class="row">
                    <div class="col-md-2"> <button class="btn btn-primary" type="button" id="getoldvalue">Get Old Purchase Value</button></div>
                    <div class="col-md-3">
                       <div class="row">
                          <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                            Purchase Rs <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-8">
                            <input type="text" id="modal_purchase_price" class="form-control" placeholder="Enter amount">
                          </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                       <div class="row">
                          <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                            Retail Price <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-8">
                            <input type="text" id="modal_retail_price" class="form-control" placeholder="Enter amount">
                          </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                       <div class="row">
                          <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                            HSN/SAC Code 
                          </label>
                          <div class="col-lg-7">
                            <input type="text" id="modal_hsn_code" class="form-control" placeholder="">
                          </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                       <div class="row">
                          <label for="modal_purchase_price" class="col-lg-4 col-form-label">
                            GST %  
                          </label>
                          <div class="col-lg-8">
                            <input type="text" id="modal_gst" class="form-control">
                          </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                       <div class="row">
                          <label for="modal_purchase_price" class="col-lg-7 col-form-label">
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
                    <div class="col-md-3">
                       <div class="row">
                          <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                            Basic Price <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-7">
                            <input type="text" id="modal_basic_price" class="form-control" placeholder="0.00" readonly>
                          </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                       <div class="row">
                          <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                            GST Amount Rs  <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-7">
                            <input type="text" id="modal_gst_amount" class="form-control" placeholder="0.00" readonly>
                          </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                       <div class="row">
                          <label for="modal_purchase_price" class="col-lg-5 col-form-label">
                            Total Purchase<span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-7">
                            <input type="text" id="modal_total_price" class="form-control" placeholder="0.00" readonly>
                          </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <button type="button"  class="btn btn-primary addmodalbtn">Add</button>
                    </div>
                </div>
            </div>    
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


@endsection

@section('scripts')
<script>
const typeToDivsMap = {
    frame: [
        @if($tbl_setting_frame->color == '0') 'color-div', @endif
        @if($tbl_setting_frame->size == '0') 'size-div', @endif
        @if($tbl_setting_frame->type == '0') 'type-div', @endif
        @if($tbl_setting_frame->gender == '0') 'gender-div', @endif
        @if($tbl_setting_frame->Shape == '0') 'shape-div', @endif
        @if($tbl_setting_frame->Material == '0') 'material-div', @endif
        @if($tbl_setting_frame->Temple_Detail == '0') 'temple-div', @endif
        @if($tbl_setting_frame->Bridge_Size == '0') 'bridge-div', @endif
        @if($tbl_setting_frame->Description == '0') 'description-div', @endif
    ],
    goggles: [
        @if($tbl_setting_goggles->color == '0') 'color-div', @endif
        @if($tbl_setting_goggles->size == '0') 'size-div', @endif
        @if($tbl_setting_goggles->type == '0') 'type-div', @endif
        @if($tbl_setting_goggles->gender == '0') 'gender-div', @endif
        @if($tbl_setting_goggles->Shape == '0') 'shape-div', @endif
        @if($tbl_setting_goggles->Material == '0') 'material-div', @endif
        @if($tbl_setting_goggles->Temple_Detail == '0') 'temple-div', @endif
        @if($tbl_setting_goggles->Bridge_Size == '0') 'bridge-div', @endif
        @if($tbl_setting_goggles->Description == '0') 'description-div', @endif
    ],
    glass: [
        @if($tbl_setting_glass->color == '0') 'color-div', @endif
        @if($tbl_setting_glass->Material == '0') 'material-div', @endif
        @if($tbl_setting_glass->Coating == '0') 'coating-div', @endif
        @if($tbl_setting_glass->Design == '0') 'design-div', @endif
        @if($tbl_setting_glass->Product_Index == '0') 'index-div', @endif
        @if($tbl_setting_glass->Numbers == '0') 'sph-div', 'cyl-div', 'axis-div', 'additional-div', @endif
        @if($tbl_setting_glass->Description == '0') 'description-div', @endif
    ],
    lens: [
        @if($tbl_setting_lens->color == '0') 'color-div', @endif
        @if($tbl_setting_lens->Material == '0') 'material-div', @endif
        @if($tbl_setting_lens->Numbers == '0') 'Number-div', 'sph-div', 'cyl-div', 'axis-div', 'additional-div', @endif
        @if($tbl_setting_lens->CT == '0') 'ct-div', @endif
        @if($tbl_setting_lens->type == '0') 'type-div', @endif
        @if($tbl_setting_lens->Validity_In_Days == '0') 'validity-div', @endif
        @if($tbl_setting_lens->BC == '0') 'bc-div', @endif
        @if($tbl_setting_lens->DIA == '0') 'diameter-div', @endif
        @if($tbl_setting_lens->POWER_TYPE == '0') 'powertype-div', @endif
        @if($tbl_setting_lens->Modality == '0') 'modality-div', @endif
        @if($tbl_setting_lens->WC == '0') 'wc-div', @endif
        @if($tbl_setting_lens->Dk_t == '0') 'dkt-div', @endif
        @if($tbl_setting_lens->Description == '0') 'description-div', @endif
        'box-div', 'perbox-div', 'batchno-div', 'mfg-div', 'expiry-div'
    ],
    solution: [
        @if($tbl_setting_solution->Variant == '0') 'variant-div', @endif
        @if($tbl_setting_solution->Packing_Type == '0') 'packing-div', @endif
        @if($tbl_setting_solution->color == '0') 'color-div', @endif
        @if($tbl_setting_solution->Description == '0') 'description-div', @endif
    ],
    other: [
        @if($tbl_setting_other->type == '0') 'type-div', @endif
        @if($tbl_setting_other->color == '0') 'color-div', @endif
        @if($tbl_setting_other->Shape == '0') 'shape-div', @endif
        @if($tbl_setting_other->size == '0') 'size-div', @endif
         @if($tbl_setting_other->Description == '0') 'description-div', @endif
    ]
};
</script>

<script>

$(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
  });
  
$(function () 
{
    var selectedDate = moment();

    function cb(date) 
    {
        $('#reportrange span').html(date.format('MMMM D, YYYY'));
        $('#date_from').val(date.format('YYYY-MM-DD'));
    }

    $('#reportrange').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        startDate: selectedDate,
        locale: {
            format: 'MMMM D, YYYY'
        }
    }, cb);

    cb(selectedDate);
});
</script>
<script>
$(document).ready(function () {
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
    
    
        /** ==============================
     *  Table row handling
     *  ============================== */
    const addRowBtn = document.getElementById("addRowBtn");
    const tableBody = document.querySelector("#saleTable tbody");

    function updateSerialNumbers() {
        tableBody.querySelectorAll("tr").forEach((row, index) => {
            row.cells[0].textContent = index + 1;
            const removeBtn = row.querySelector(".removeBtn");
            if (removeBtn) {
                removeBtn.style.display = (index === 0) ? "none" : "inline-block";
            }
        });
    }

    function validateLastRow() {
        const lastRow = tableBody.querySelector("tr:last-child");
        let isValid = true;
    
        if (lastRow) {
            // Select only the fields to validate
            const productType = lastRow.querySelector("select.product-type");
            const productDescription = lastRow.querySelector("input.product-description");
            const productPrice = lastRow.querySelector("input.product-purchase-price");
    
            // Validation checks
            if (!productType.value.trim()) {
                productType.classList.add("error");
                isValid = false;
            } else {
                productType.classList.remove("error");
            }
    
            if (!productDescription.value.trim()) {
                productDescription.classList.add("error");
                isValid = false;
            } else {
                productDescription.classList.remove("error");
            }
    
            if (!productPrice.value.trim() || parseFloat(productPrice.value) <= 0) {
                productPrice.classList.add("error");
                isValid = false;
            } else {
                productPrice.classList.remove("error");
            }
        }
    
        return isValid;
    }

    addRowBtn.addEventListener("click", function () {
        if (!validateLastRow()) {
            $.toaster({ priority: 'danger', title: '⚠️ Please fill all required fields in the last row before adding a new one.', message: '' });
            return;
        }

        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td></td>
            <td>
                <select class="form-control product-type" style="height: 32px !important;width:130px" name="product_type[]">
                    <option value="">Select Product</option>
                    <option value="Frame">Frame</option>
                    <option value="Glass">Glass</option>
                    <option value="Goggles">Goggles</option>
                    <option value="Lens">Contact Lens</option>
                    <option value="Solution">Solution</option>
                    <option value="Other">Other</option>
                </select>

            </td>
            <td class="tax-col">
                <input type="text" style="width:120px" class="form-control product-code" name="product_code[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-id" name="product_id[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-name" name="product_name[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-company" name="product_company[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-quality" name="product_quality[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-color" name="product_color[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-material" name="product_material[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-coating" name="product_coating[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-design" name="product_design[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-index" name="product_index[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-sph" name="product_sph[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-cyl" name="product_cyl[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-addition" name="product_addition[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-axis" name="product_axis[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-number" name="product_number[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-tc" name="product_tc[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-lenstype" name="product_lenstype[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-validity" name="product_validity[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-bc" name="product_bc[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-diameter" name="product_diameter[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-powertype" name="product_powertype[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-batch" name="product_batch[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-mfg" name="product_mfg[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-expiry" name="product_expiry[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-noofbox" name="product_noofbox[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-perbox" name="product_perbox[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-invoicedescription" name="product_invoicedescription[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-variant" name="product_variant[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-shape" name="product_shape[]" readonly>
                <input type="hidden" style="width:120px" class="form-control product-size" name="product_size[]" readonly>
                <input type="hidden" style="width:120px" class="form-control barcode-option" name="barcode_option[]" readonly>
                <input type="hidden" style="width:120px" class="form-control negative-inventory" name="negative_inventory[]" readonly>
                <input type="hidden" style="width:120px" class="form-control track-inventory" name="track_inventory[]" readonly>
                <input type="hidden" style="width:120px" class="form-control ispair" name="ispairglass[]" readonly>
            </td>
            <td>
                <input type="text"  style="width:300px"  class="form-control product-description" name="product_description[]" placeholder="Enter Product Description" readonly>
                <input type="hidden"  style="width:300px"  class="form-control product-details" name="product_details[]" placeholder="Enter Product Description" readonly>
            </td>
            <td class="tax-col">
                <input type="text" style="width:80px" class="form-control unit-price" name="product_price[]" placeholder="0.00" readonly>
            </td>
            <td class="tax-col">
                <input type="text" style="width:80px" class="form-control base-price" name="product_base_price[]" placeholder="0.00" readonly>
            </td>
             <td class="tax-col">
                <input type="text" style="width:80px" class="form-control hsn-code" name="hsn_code[]" readonly>
            </td>
            <td>
               <div class="input-group mb-3" style="width:120px" >
                    <input type="text" class="form-control gst-amount" name="gst_amt[]" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                    <input type="text" class="form-control gst" value="0"  name="gst[]" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                </div>
            </td>
            <td>
                <input type="text" style="width:80px" class="form-control product-purchase-price" name="product_purchase_price[]" placeholder="0.00" readonly>
            </td>
            <td>
                <input type="text" style="width:50px" class="form-control product-qty" name="product_qty[]" value="0" readonly>
            </td>
            <td>
                <input type="text" style="width:80px" class="form-control total-purchase-price" name="total_purchase_price[]" placeholder="0.00" readonly>
            </td>
           <td>
                <input type="text" style="width:80px" class="form-control retail-price" name="product_retail_price[]" placeholder="0.00" readonly>
            </td>
            <td><button type="button" class="removeBtn">❌</button></td>
        </tr>
        `;
        tableBody.appendChild(newRow);
        updateSerialNumbers();
    });

    tableBody.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeBtn")) {
            e.target.closest("tr").remove();
            updateSerialNumbers();
        }
    });

    updateSerialNumbers();
    
    $(document).on("click", ".removeBtn", function () {
        $(this).closest("tr").remove();
        recalcTotals();
    });
    
    
    /** ==============================
     *  Product Type Wise Modal Div Open
     *  ============================== */
    
    /*function toggleProductContainer()
    {
        const supplierName = $('#supplier_name').val()?.trim();
        const billNo       = $('#p_bill_no').val()?.trim();
        const taxRule      = $('#tax_rule').val()?.trim();
        const store_id  = $('#store_id').val()?.trim();
    
        let enabled = supplierName && billNo && taxRule && store_id;

        $('.product-type')
            .prop('disabled', !enabled);
    
        $('#product-container').show();
    }
    
    $('#supplier_name, #p_bill_no, #store_id, #tax_rule')
        .on('input change', toggleProductContainer);
    
    toggleProductContainer();*/
    
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
               
                $("#modal_product_type").val(selectedType);
                $("#modal_hsn_code").val(res.hsn_code);
                $("#modal_gst").val(res.percentage);
                handleProductType(selectedType);
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
     
    function handleProductType(type)
    {
        switch (type) 
        {
            case "Frame":
                $("#FrameDive").show();
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;
                
            case "Goggles":
                $("#FrameDive").show();
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;     
             case "Glass":
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").show();
                $("#FrameDive").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;
            case "Lens":
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#FrameDive").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").show();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;
             case "Solution":
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#FrameDive").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").show();
                $("#OtherDiveA,#OtherDiveB").hide();
                break;  
             case "Other":
                $("#GlassDiveA,#GlassDiveC,#GlassDiveB").hide();
                $("#FrameDive").hide();
                $("#LensDiveA,#LensDiveC,#LensDiveB,#LensDiveD").hide();
                $("#SolutionDiveA,#SolutionDiveB").hide();
                $("#OtherDiveA,#OtherDiveB").show();
                break;     
        }
    }

    /** ==============================
     *  Product Code Wise Product Details
     *  ============================== */
     
    $(document).on('keyup', '#modal_product_code', function ()
    {
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
        let productType = $("#modal_product_type").val();
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
    
        totalPrice = (basePrice + gstAmount);
    
        $("#modal_basic_price").val((basePrice).toFixed(2));
        $("#modal_gst_amount").val((gstAmount).toFixed(2));
        $("#modal_total_price").val(totalPrice.toFixed(2));
    }
    
    
     $('#modal_purchase_price, #modal_frame_qty, #modal_glass_qty').on('keyup', function() {
        calculateProductModal();
    });
    
    function parseNum(val) {
        val = parseFloat(val);
        return isNaN(val) ? 0 : val;
    }
    
    function calculateTotals() 
    {
        let totalQty = 0, totalUnit = 0, totalBase = 0, totalGst = 0, totalPurchase = 0;
        
        $(".unit-price, .base-price, .gst-amounte, .product-qty, .total-purchase-price").each(function() {
            let currentVal = $(this).val();
            if (!currentVal || currentVal === '') {
                $(this).val($(this).attr("value") || 0);
            }
        });
    
        // Sum up totals
        $(".unit-price").each(function() {
            totalUnit += parseNum($(this).val());
        });
    
        $(".base-price").each(function() {
            totalBase += parseNum($(this).val());
        });
    
        $(".gst-amount").each(function() {
            totalGst += parseNum($(this).val());
        });
    
        $(".product-qty").each(function() {
            totalQty += parseNum($(this).val());
        });
        
        $(".total-purchase-price").each(function() {
            totalPurchase += parseNum($(this).val());
        });
    

    
        $('#total_qty').val(totalQty);
        $('#total_unit_amount').val(totalUnit.toFixed(2));
        $('#total_base_amount').val(totalBase.toFixed(2));
        $('#total_gst_amount').val(totalGst.toFixed(2));
        $('#total_p_amount').val(totalPurchase.toFixed(2));
    
        const roundOff = parseFloat($('#round_off').val()) || 0;
        $('#net_purchase_amount').val((totalPurchase + roundOff).toFixed(2));
    }
    
    $('#round_off').on('input', calculateTotals);


    
    /** =================================
     *  Modal Data Add In Table Row
     * ================================== */
    
    $(".addmodalbtn").click(function () 
    {
        let product_type = $("#modal_product_type").val();
        let product_code = $("#modal_product_code").val();
        let purchasePrice = parseFloat($("#modal_purchase_price").val()) || 0;
        let retailPrice = parseFloat($("#modal_retail_price").val()) || 0;
        let gst = $("#modal_gst").val();
        let hsncode = $("#modal_hsn_code").val();
        let pname = $("#modal_frame_name").val();
        let modal_lens_invoicedescription = $("#modal_lens_invoicedescription").val();
        let modal_solution_invoicedescription = $("#modal_solution_invoicedescription").val();
        
        if (!product_code) { 
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Product code required.',
                timeout: 3000
            }); 
            return; 
        }
        
        if(product_type == 'Frame' || product_type == 'Goggles')
        {
            if (!pname) { 
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Product name required.',
                    timeout: 3000
                }); 
                return; 
            }
        }
        else if(product_type == 'Lens')
        {
            if (!modal_lens_invoicedescription) { 
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Invoice Description required.',
                    timeout: 3000
                }); 
                return; 
            }
        }
        else if(product_type == 'Solution')
        {
            if (!modal_solution_invoicedescription) { 
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Invoice Description required.',
                    timeout: 3000
                }); 
                return; 
            }
        }
        
        
        if (purchasePrice <= 0) { 
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Purchase Price required.',
                timeout: 3000
            });
            return; 
        }
        if (retailPrice <= 0) { 
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Retail Price required.',
                timeout: 3000
            });
            return; 
        }
    
        
        
        let currentRow = $("#saleTable tbody tr.active");
        if (currentRow.length === 0) currentRow = $("#saleTable tbody tr:first");
        currentRow.find(".product-code").val($("#modal_product_code").val());
        currentRow.find(".product-id").val($("#modal_product_id").val());
        
         let product_qty = 0;
         if(product_type == 'Frame' || product_type == 'Goggles')
         {
            currentRow.find(".product-name").val($("#modal_frame_name").val());
            currentRow.find(".product-company").val($("#modal_frame_company").val());
            currentRow.find(".product-quality").val($("#modal_frame_quality").val());
            let Track_Inventory = $('input[name="modal_Track_Inventory"]:checked').val() || '';
            currentRow.find(".track-inventory").val(Track_Inventory);
            let Negative_Inventory = $('input[name="modal_Negative_Inventory"]:checked').val() || '';
            currentRow.find(".negative-inventory").val(Negative_Inventory);
            
            var fields = [
                $("#modal_frame_name").val(),
                $("#modal_frame_company").val(),
                $("#modal_frame_quality").val()
            ];
            
            // Filter out empty values (like PHP's array_filter)
            fields = fields.filter(function(value) {
                return value && value.trim() !== "";
            });
            
            // Join with " - "
            var combined = fields.join(" - ");
            
             currentRow.find(".product-description").val(combined);
             currentRow.find(".product-details").val(combined);
             
             product_qty = $("#modal_frame_qty").val();
         }
         else if(product_type == 'Glass')
         {
            currentRow.find(".product-name").val($("#modal_glass_details").val());
            currentRow.find(".product-company").val($("#modal_glass_company").val());
            currentRow.find(".product-quality").val($("#modal_glass_quality").val());
            currentRow.find(".product-color").val($("#modal_glass_color").val());
            currentRow.find(".product-material").val($("#modal_glass_Material").val());
            currentRow.find(".product-coating").val($("#modal_glass_Coating").val());
            currentRow.find(".product-design").val($("#modal_glass_Design").val());
            currentRow.find(".product-index").val($("#modal_glass_Index").val());
            currentRow.find(".product-sph").val($("#modal_glass_SPH").val());
            currentRow.find(".product-cyl").val($("#modal_glass_CYL").val());
            currentRow.find(".product-addition").val($("#modal_glass_Addition").val());
            currentRow.find(".product-axis").val($("#modal_glass_Axis").val());
            let fields = [
              $("#modal_glass_details").val(),
              $("#modal_glass_company").val(),
              $("#modal_glass_color").val(),
              $("#modal_glass_Material").val(),
              $("#modal_glass_Coating").val(),
              $("#modal_glass_Design").val(),
              $("#modal_glass_Index").val(),
              $("#modal_glass_quality").val(),
              $("#modal_glass_SPH").val() ? "SPH:" + $("#modal_glass_SPH").val() : null,
              $("#modal_glass_CYL").val() ? "CYL:" + $("#modal_glass_CYL").val() : null,
              $("#modal_glass_Addition").val() ? "ADD:" + $("#modal_glass_Addition").val() : null,
              $("#modal_glass_Axis").val() ? "Axis:" + $("#modal_glass_Axis").val() : null,
            ];
            
            // Filter out empty values (like PHP's array_filter)
            fields = fields.filter(function(value) {
                return value && value.trim() !== "";
            });
            
            // Join with " - "
            var combined = fields.join(" - ");
            
             currentRow.find(".product-description").val(combined);
             
             
             let fieldsProduct = [
              $("#modal_glass_details").val(),
              $("#modal_glass_company").val(),
              $("#modal_glass_color").val(),
              $("#modal_glass_Material").val(),
              $("#modal_glass_Coating").val(),
              $("#modal_glass_Design").val(),
              $("#modal_glass_Index").val(),
              $("#modal_glass_quality").val(),
            ];
            
            // Filter out empty values (like PHP's array_filter)
            fieldsProduct = fieldsProduct.filter(function(value) {
                return value && value.trim() !== "";
            });
            
            // Join with " - "
            var combinedProduct = fieldsProduct.join(" - ");
            
             currentRow.find(".product-details").val(combinedProduct);
             
             product_qty = $("#modal_glass_qty").val();
         }
         
         else if(product_type == 'Lens')
         {
             currentRow.find(".product-name").val($("#modal_lens_product_name").val());
             currentRow.find(".product-company  ").val($("#modal_lens_company").val());
             currentRow.find(".product-color").val($("#modal_lens_color").val());
             currentRow.find(".product-number").val($("#modal_lens_number").val());
             currentRow.find(".product-tc").val($("#modal_lens_tc").val());
             currentRow.find(".product-lenstype").val($("#modal_lens_type").val());
             currentRow.find(".product-material").val($("#modal_lens_Materials").val());
             currentRow.find(".product-validity").val($("#modal_lens_validity").val());
             currentRow.find(".product-sph").val($("#modal_lens_sph").val());
             currentRow.find(".product-cyl").val($("#modal_lens_cyl").val());
             currentRow.find(".product-addition").val($("#modal_lens_addition").val());
             currentRow.find(".product-axis").val($("#modal_lens_axis").val());
             currentRow.find(".product-bc").val($("#modal_lens_bc").val());
             currentRow.find(".product-diameter").val($("#modal_lens_diameter").val());
             currentRow.find(".product-powertype").val($("#modal_lens_powertype").val());
             currentRow.find(".product-batch").val($("#modal_lens_batch").val());
             currentRow.find(".product-mfg").val($("#modal_lens_mfg").val());
             currentRow.find(".product-expiry").val($("#modal_lens_expiry").val());
             currentRow.find(".product-noofbox").val($("#modal_noofbox").val());
             currentRow.find(".product-perbox").val($("#modal_perbox").val());
             currentRow.find(".product-quality").val($("#modal_lens_quality").val());
             currentRow.find(".product-invoicedescription").val($("#modal_lens_invoicedescription").val());
             
             let fields = [
              $("#modal_lens_product_name").val(),
              $("#modal_lens_company").val(),
              $("#modal_lens_quality").val(),
              $("#modal_lens_type").val(),
              $("#modal_lens_color").val(),
              $("#modal_lens_number").val(),
              $("#modal_lens_tc").val(),
              $("#modal_lens_Materials").val(),
              $("#modal_lens_validity").val(),
              $("#modal_lens_sph").val() ? "SPH:" + $("#modal_lens_sph").val() : null,
              $("#modal_lens_cyl").val() ? "CYL:" + $("#modal_lens_cyl").val() : null,
              $("#modal_lens_addition").val() ? "ADD:" + $("#modal_lens_addition").val() : null,
              $("#modal_lens_axis").val() ? "Axis:" + $("#modal_lens_axis").val() : null,
              $("#modal_lens_bc").val() ?  $("#modal_lens_bc").val() : null,
              $("#modal_lens_diameter").val() ?  $("#modal_lens_diameter").val() : null,
              $("#modal_lens_powertype").val() ?  $("#modal_lens_powertype").val() : null,
            ];
            
            // Filter out empty values (like PHP's array_filter)
            fields = fields.filter(function(value) {
                return value && value.trim() !== "";
            });
            
            // Join with " - "
            var combined = fields.join(" - ");
            
             currentRow.find(".product-description").val(combined);
             
             
             let fieldsProduct = [
              $("#modal_lens_product_name").val(),
              $("#modal_lens_company").val(),
              $("#modal_lens_quality").val(),
              $("#modal_lens_type").val(),
              $("#modal_lens_color").val(),
              $("#modal_lens_number").val(),
              $("#modal_lens_tc").val(),
              $("#modal_lens_Materials").val(),
              $("#modal_lens_validity").val(),
            ];
            
            // Filter out empty values (like PHP's array_filter)
            fieldsProduct = fieldsProduct.filter(function(value) {
                return value && value.trim() !== "";
            });
            
            // Join with " - "
            var combinedProduct = fieldsProduct.join(" - ");
            
             currentRow.find(".product-details").val(combinedProduct);
             
             product_qty = $("#modal_noofbox").val();
         }
         else if(product_type == 'Solution')
         {
             currentRow.find(".product-name").val($("#modal_solution_product_name").val());
             currentRow.find(".product-company").val($("#modal_solution_company").val());
             currentRow.find(".product-color").val($("#modal_solution_color").val());
             currentRow.find(".product-variant").val($("#modal_solution_Variant").val());
             currentRow.find(".product-lenstype").val($("#modal_solution_packingtype").val());
             currentRow.find(".product-quality").val($("#modal_solution_quality").val());
             currentRow.find(".product-invoicedescription").val($("#modal_solution_invoicedescription").val());
             
             let fields = [
              $("#modal_solution_product_name").val(),
              $("#modal_solution_company").val(),
              $("#modal_solution_quality").val(),
              $("#modal_solution_Variant").val(),
              $("#modal_solution_packingtype").val(),
              $("#modal_solution_color").val(),

            ];
            
            // Filter out empty values (like PHP's array_filter)
            fields = fields.filter(function(value) {
                return value && value.trim() !== "";
            });
            
            // Join with " - "
            var combined = fields.join(" - ");
            
             currentRow.find(".product-description").val(combined);
             currentRow.find(".product-details").val(combined);
             
             product_qty = $("#modal_solution_quantity").val();
         }
         else if(product_type == 'Other')
         {
             currentRow.find(".product-name").val($("#modal_other_product_name").val());
             currentRow.find(".product-company").val($("#modal_other_company").val());
             currentRow.find(".product-color").val($("#modal_other_color").val());
             currentRow.find(".product-lenstype").val($("#modal_other_type").val());
             currentRow.find(".product-shape").val($("#modal_other_shape").val());
             currentRow.find(".product-size").val($("#modal_other_size").val());
             currentRow.find(".product-quality").val($("#modal_other_quality").val());
             currentRow.find(".product-invoicedescription").val($("#modal_other_invoicedescription").val());
             
             let fields = [
              $("#modal_other_product_name").val(),
              $("#modal_other_company").val(),
              $("#modal_other_color").val(),
              $("#modal_other_type").val(),
              $("#modal_other_shape").val(),
              $("#modal_other_size").val(),
              $("#modal_other_quality").val(),

            ];
            
            // Filter out empty values (like PHP's array_filter)
            fields = fields.filter(function(value) {
                return value && value.trim() !== "";
            });
            
            // Join with " - "
            var combined = fields.join(" - ");
            
             currentRow.find(".product-description").val(combined);
             currentRow.find(".product-details").val(combined);
             
             product_qty = $("#modal_other_quantity").val();
         }
         
        product_qty = parseFloat(product_qty) || 0;
        
        let Track_Inventory = $('input[name="modal_Track_Inventory"]:checked').val() || '';
        currentRow.find(".track-inventory").val(Track_Inventory);
        let Negative_Inventory = $('input[name="modal_Negative_Inventory"]:checked').val() || '';
        currentRow.find(".negative-inventory").val(Negative_Inventory);
        let barcode_option = $('input[name="modal_barcode_option"]:checked').val() || '';
        currentRow.find(".barcode-option").val(barcode_option);
        
        currentRow.find(".unit-price").val($("#modal_purchase_price").val());
        currentRow.find(".base-price").val($("#modal_basic_price").val());
        currentRow.find(".hsn-code").val($("#modal_hsn_code").val());
        currentRow.find(".gst").val($("#modal_gst").val());
        currentRow.find(".gst-amount").val($("#modal_gst_amount").val());
        currentRow.find(".product-purchase-price").val($("#modal_purchase_price").val());
        currentRow.find(".product-qty").val(product_qty);
        
        let modal_total_price = $("#modal_total_price").val() * product_qty;
        currentRow.find(".total-purchase-price").val(modal_total_price);
        currentRow.find(".retail-price").val($("#modal_retail_price").val());
        
        // ---------------- RIGHT/LEFT CHECKBOX ----------------
        let RL = $('input[name="ispair"]:checked')
                    .map(function(){ return $(this).val(); })
                    .get()
                    .join(", ");
        currentRow.find(".ispair").val(RL);
        
        calculateTotals();
        
        $("#productModal").modal("hide");
        const modal = $("#productModal");

        const idsToClear = [
          "#modal_product_code", "#modal_product_type", "#modal_product_id",
          "#modal_frame_name", "#modal_frame_company", "#modal_frame_quality",
          "#modal_glass_details", "#modal_glass_company", "#modal_glass_quality",
          "#modal_glass_color", "#modal_glass_Material", "#modal_glass_Coating", "#modal_glass_Design",
          "#modal_glass_Index", "#modal_glass_SPH", "#modal_glass_CYL", "#modal_glass_Addition", "#modal_glass_Axis",
          "#modal_lens_product_name", "#modal_lens_company", "#modal_lens_color", "#modal_lens_number",
          "#modal_lens_tc", "#modal_lens_type", "#modal_lens_Materials", "#modal_lens_validity",
          "#modal_lens_sph", "#modal_lens_cyl", "#modal_lens_addition", "#modal_lens_axis",
          "#modal_lens_bc", "#modal_lens_diameter", "#modal_lens_powertype", "#modal_lens_quality",
          "#modal_lens_batch", "#modal_lens_mfg", "#modal_lens_expiry", "#modal_noofbox",
          "#modal_perbox", "#modal_lens_quantity", "#modal_lens_invoicedescription",
          "#modal_solution_product_name", "#modal_solution_company", "#modal_solution_color",
          "#modal_solution_Variant", "#modal_solution_packingtype", "#modal_solution_quality"
          , "#modal_solution_invoicedescription",
          "#modal_other_product_name", "#modal_other_company", "#modal_other_color",
          "#modal_other_type", "#modal_other_shape", "#modal_other_size", "#modal_other_quality",
           "#modal_other_invoicedescription",
          "#modal_purchase_price", "#modal_retail_price", "#modal_hsn_code", "#modal_gst",
          "#modal_basic_price", "#modal_gst_amount", "#modal_total_price"
        ];
        
        // Loop through all IDs and clear their values
        idsToClear.forEach(id => modal.find(id).val(""));


        
    });
    
    $(document).on("focus", ".product-type", function ()
    {
        $("#saleTable tbody tr").removeClass("active");
        $(this).closest("tr").addClass("active");
    });
    
});
</script>

<script>
$("#purchaseForm").submit(function (e) {
    e.preventDefault();

    let isValid = true;
    let class_name = '';

    // Reset old errors
    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    // Collect fields
    let supplier_name = document.getElementById("supplier_name" + class_name).value.trim();
    let p_bill_no     = document.getElementById("p_bill_no" + class_name).value.trim();
    let store_id      = document.getElementById("store_id" + class_name).value.trim();  
    let tax_rule      = document.getElementById("tax_rule" + class_name).value.trim();

    // Validation
    if (supplier_name === "") {
        document.getElementById("supplier_nameError" + class_name).textContent = "Select Supplier Name.";
        document.getElementById("supplier_name" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    if (p_bill_no === "") {
        document.getElementById("p_bill_noError" + class_name).textContent = "Purchase Bill Number required.";
        document.getElementById("p_bill_no" + class_name).classList.add("is-invalid");
        isValid = false;
    }


    if (tax_rule === "") {
        document.getElementById("tax_ruleError" + class_name).textContent = "Select Tax Type.";
        document.getElementById("tax_rule" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (store_id === "") {
        document.getElementById("store_idError" + class_name).textContent = "Select Store.";
        document.getElementById("store_id" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    if (!isValid) return;

    // Submit via AJAX
    let form = $("#purchaseForm")[0];
    let data = new FormData(form);

    $.ajax({
        type: "POST",
        url: "{{ route('admin.add-purchase-record') }}",
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
                window.location.href = "{{ route('admin.purchase-history') }}";
            } else {
                document.querySelectorAll(".error").forEach(el => el.textContent = "");
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
            }
        },  // <-- Missing comma was here
        complete: function () {
            $("#ajaxLoader").fadeOut(); 
        }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error: " + textStatus + " - " + errorThrown);
    });
});

$(document).ready(function () {

    $("#tax_rule").on("change", function () {
        let value = $(this).val();

        // Column index numbers (0-based)
        let hsnColIndex = $("#saleTable thead th").filter(function() {
            return $(this).text().trim() === "HSN/SAC Code";
        }).index();

        let gstColIndex = $("#saleTable thead th").filter(function() {
            return $(this).text().trim() === "GST %";
        }).index();

        if (value === "Not Applicable") {

            // Hide HSN and GST columns (TH + TD)
            $("#saleTable tr").each(function () {
                $(this).find("th").eq(hsnColIndex).hide();
                $(this).find("td").eq(hsnColIndex).hide();

                $(this).find("th").eq(gstColIndex).hide();
                $(this).find("td").eq(gstColIndex).hide();
            });

            // Hide total base & total gst sections
            $("#totalbasediv").hide();
            $("#totalgstdiv").hide();

        } else {

            // Show HSN and GST columns
            $("#saleTable tr").each(function () {
                $(this).find("th").eq(hsnColIndex).show();
                $(this).find("td").eq(hsnColIndex).show();

                $(this).find("th").eq(gstColIndex).show();
                $(this).find("td").eq(gstColIndex).show();
            });

            // Show total base & total gst sections
            $("#totalbasediv").show();
            $("#totalgstdiv").show();
        }
    });

});





</script>




    
@endsection
