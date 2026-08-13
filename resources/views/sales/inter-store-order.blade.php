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

.table thead tr th
{
    color: #6b6f80;
}

.form-group {
    display: inline-flex
;
}
</style>

@endsection
@section('content')
@php
     $usr = Auth::guard()->user();
     $tblstore =  DB::table("tbl_store")->where('id',$usr->store_id)->first();
 @endphp
 @php $tbl_setting_frame =  DB::table("tbl_product_code_setting")->where('product_type','Frame')->first();   @endphp
 @php $tbl_setting_goggles =  DB::table("tbl_product_code_setting")->where('product_type','Goggles')->first();   @endphp
 @php $tbl_setting_glass =  DB::table("tbl_product_code_setting")->where('product_type','Glass')->first();   @endphp
 @php $tbl_setting_lens =  DB::table("tbl_product_code_setting")->where('product_type','Lens')->first();   @endphp
 @php $tbl_setting_solution =  DB::table("tbl_product_code_setting")->where('product_type','Solution')->first();   @endphp
 @php $tbl_setting_other =  DB::table("tbl_product_code_setting")->where('product_type','Other')->first();   @endphp
@php
    $rowCount = DB::table("tbl_sales")
        ->where('store_id', $usr->store_id)
        ->count();
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
                    <h3>Create Inter Store Sales </h3>
                     @if ($usr->can('Sales-History'))
                    <a href="{{route('admin.sale-history')}}" class=" btn">
                        <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                         Sales History
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
                        <form id="saleForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" id="formMethod" value="POST">
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
                                    <label for="">Bill Number <span class="text-danger">*</span></label>
                                    <input class="form-control"  placeholder="Enter Bill Number"  id="bill_no" name="bill_no" readonly>
                                    <span class="error badge text-danger" id="bill_noError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="">From Store<span class="text-danger">*</span></label>
                                    <select class="form-control select" style="height: 32px !important;" id="from_store" name="from_store">
                                        <option value="">Select  Store</option>
                                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                                       @foreach($tbl_store as $tbl_store)
                                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                                      @endforeach
                                    </select>
                                    <span class="error badge text-danger" id="from_storeError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="">From Store GST Number  <span class="text-danger">*</span></label>
                                    <input class="form-control"  id="from_gst_no" name="from_gst_no" readonly>
                                    <span class="error badge text-danger" id="from_gst_noError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="">To Store<span class="text-danger">*</span></label>
                                    <select class="form-control select" style="height: 32px !important;" id="to_store" name="to_store">
                                        <option value="">Select Store</option>
                                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                                       @foreach($tbl_store as $tbl_store)
                                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                                      @endforeach
                                    </select>
                                    <span class="error badge text-danger" id="to_storeError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label for="">To Store GST Number  <span class="text-danger">*</span></label>
                                    <input class="form-control"   id="to_gst_no" name="to_gst_no" readonly>
                                    <span class="error badge text-danger" id="to_gst_noError"></span>
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
                                          <option value="Include">Include</option>
                                          <option value="Not Applicable">Not Appicable</option>
                                          <option value="Exclude ">Exclude</option>
                                    </select>
                                    <span class="error badge text-danger" id="tax_ruleError"></span>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="multisteps-form__content1 pb-0">
                                        <h5>Product Details <span style="color:red;font-size:12px">(Note : First Select From store or To Store then Barcode input enable.)</span></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <table class="table datatables-basic w-100" id="saleTable">
                                  <thead>
                                    <tr>
                                      <th style="width: 5%;">#</th>
                                      <th style="width: 12%;">Barcode <span style="color:red">*</span></th>
                                      <th style="width: 12%;">Product <span style="color:red">*</span></th>
                                      <th>Product Description <span style="color:red">*</span></th>
                                      <th style="width: 6%;">Qty <span style="color:red">*</span></th>
                                      <th style="width: 9%;">HSN/SAC Code<span style="color:red">*</span></th>
                                      <th style="width: 9%;">GST %<span style="color:red">*</span></th>
                                      <th style="width: 13%;">Margin <span style="color:red">*</span></th>
                                      <th style="width: 9%;">Price<span style="color:red">*</span></th>
                                      <th style="width: 4%;">--</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <!-- First Row -->
                                    <tr>
                                      <td>1</td>
                                      <td><input type="text" class="form-control barcode" name="barcode[]" placeholder="Enter barcode"></td>
                                      <td>
                                          <input type="text" class="form-control product-type" name="product_type[]" placeholder="Enter Product" readonly>
                                          <input type="hidden" class="form-control product-code" name="product_code[]" placeholder="Enter Product" readonly>
                                          <input type="hidden" class="form-control product-id" name="product_id[]" placeholder="Enter Product" readonly>
                                      </td>
                                      <td><input type="text" class="form-control product-description" name="product_description[]" placeholder="Enter Product Discription" readonly></td>
                                      <td><input type="text" class="form-control product-qty" name="product_qty[]" readonly></td>
                                      <td><input type="text" class="form-control hsn-code" name="hsn_code[]"  readonly></td>
                                      <td>
                                          <input type="text" class="form-control gst-per" name="gst[]"  readonly>
                                          <input type="hidden" class="form-control gst-amount" name="gst_amount[]"  readonly>
                                      </td>
                                      <td>
                                          <div class="input-group mb-3">
                                            <input type="text" class="form-control margin-amount" name="margin_amt[]" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                                            <input type="text" class="form-control margin" name="margin[]" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                                          </div>
                                      </td>
                                      <td>
                                          <input type="hidden" class="form-control base-price" name="base_price[]" placeholder="0.00"  readonly>
                                          <input type="hidden" class="form-control retail-price" name="retail_price[]" placeholder="0.00"  readonly>
                                          <input type="text" class="form-control sale-price" name="sale_price[]" placeholder="0.00" readonly>
                                      </td>
                                      <td><button type="button" class="removeBtn">❌</button></td>
                                    </tr>
                                    
                                  </tbody>
                                </table>
                            </div>
                            <button type="button" id="addRowBtn">➕ Add Row</button>
                            <br>
                            <div class="row">
                                <div class="col-md-3" id="totalbasicdiv">
                                    <div class="form-group row mb-2">
                                        <label class="col-sm-7 col-form-label">Total Basic Amount</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="total_basic_amount" id="total_basic_amount" placeholder="0.00" readonly>
                                        </div>    
                                    </div>
                                </div>
                                 <div class="col-md-3" id="totalgstdiv">
                                    <div class="form-group row mb-2">
                                        <label class="col-sm-7 col-form-label">Total GST Amount</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="total_gst_amount" id="total_gst_amount" placeholder="0.00" readonly>
                                        </div>    
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-2">
                                        <label class="col-sm-7 col-form-label">Total Sales </label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="total_sale_amount" id="total_sale_amount" placeholder="0.00" readonly>
                                        </div>    
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-2">
                                        <label class="col-sm-7 col-form-label">Total Payble Amount </label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="total_payable_amount" id="total_payable_amount" placeholder="0.00" readonly>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <!--<div class="row">
                                <div class="col-md-12">
                                    <h5>Payment Method</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex">
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pay_method" id="pay_cash" value="cash">
                                        <label class="form-check-label" for="pay_cash">Cash</label>
                                      </div>
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pay_method" id="pay_upi" value="upi" checked>
                                        <label class="form-check-label" for="pay_upi">UPI</label>
                                      </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Pay Amount <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Amount" value="0.00" name="pay_amount" id="pay_amount">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Pay Deatils </label>
                                        <input type="text" class="form-control" placeholder="Enter Deatils" name="pay_deatils" id="pay_deatils">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">Pending Amount <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="0.00" name="pending_amount" id="pending_amount" readonly>
                                    </div>
                                </div>
                            </div>-->
                            <hr/>
                            <button type="submit" class="btn btn-primary loaderbtn">Submit</button>
                        </form>    
                    </div>
                </div>   
            </div>
        </div>
         
    </div>
</section>


<div class="modal fade" data-backdrop="static" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Product Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <label>Product</label>
                        <input type="text" id="modal_product_type" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Product Code</label>
                        <input type="text" id="modal_product_code" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Product ID</label>
                        <input type="text" id="modal_product_id" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Product Detail</label>
                        <input type="text" id="modal_product_details" class="form-control" readonly>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-3">
                        <label>Quantity </label>
                        <input type="text" id="modal_quantity" value="1" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>HSN/SAC Code </label>
                        <input type="text" id="modal_hsncode"  class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>GST % </label>
                        <input type="number" id="modal_gst"  class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Tax Rule</label>
                        <input type="text" id="modal_tax_rule"  class="form-control" readonly>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-3">
                        <label>Purchase Price </label>
                        <input type="text" id="modal_purchase_price"  class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Retail Price </label>
                        <input type="text" id="modal_retail_price"  class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Margin </label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="modal_margin" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;">
                            <input type="text" class="form-control" id="modal_margin_amount" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                        </div>
                    </div>
                </div> <br>
                <div class="row">
                    <div class="col-md-3">
                        <label>Base Price </label>
                        <input type="text" id="modal_base_price"  class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>GST Amount </label>
                        <input type="text" id="modal_gst_amount"  class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Total Sales </label>
                        <input type="text" id="modal_total_sale"  class="form-control" readonly>
                    </div>
                </div> <br>
                
                <div class="row">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary addmodalbtn">Add</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
$(document).ready(function () {
     $('.select1').select2({ allowClear:true, width:'22%' });
    /** ==============================
     *  Enable/disable product fields
     *  ============================== */
    function toggleProductContainer() {
        const from_store = $('#from_store').val().trim();
        const to_store = $('#to_store').val().trim();
        const enabled =  from_store !== '' && to_store !== '';
        $('.barcode, .product-type, .product-description, .product-qty, .purchase-price, .retail-price, .margin-amount, .margin')
            .prop('disabled', !enabled);
        $('#product-container').show();
    }

    $('#from_store,#to_store').on('input', toggleProductContainer);
    toggleProductContainer();


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
            const inputs = lastRow.querySelectorAll("input");
            inputs.forEach(input => {
                if (input.value.trim() === "") {
                    input.classList.add("error");
                    isValid = false;
                } else {
                    input.classList.remove("error");
                }
            });
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
             <td><input type="text" class="form-control barcode" name="barcode[]" placeholder="Enter barcode"></td>
              <td>
                  <input type="text" class="form-control product-type" name="product_type[]" placeholder="Enter Product" readonly>
                  <input type="hidden" class="form-control product-code" name="product_code[]" placeholder="Enter Product" readonly>
                  <input type="hidden" class="form-control product-id" name="product_id[]" placeholder="Enter Product" readonly>
              </td>
              <td><input type="text" class="form-control product-description" name="product_description[]" placeholder="Enter Product Discription" readonly></td>
              <td><input type="text" class="form-control product-qty" name="product_qty[]" readonly></td>
              <td><input type="text" class="form-control hsn-code" name="hsn_code[]"  readonly></td>
              <td>
                  <input type="text" class="form-control gst-per" name="gst[]"  readonly>
                  <input type="hidden" class="form-control gst-amount" name="gst_amount[]"  readonly>
              </td>
              <td>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control margin-amount" name="margin_amt[]" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                    <input type="text" class="form-control margin" name="margin[]" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                  </div>
              </td>
              <td>
                  <input type="text" class="form-control base-price" name="base_price[]" placeholder="0.00"  readonly>
                  <input type="hidden" class="form-control retail-price" name="retail_price[]" placeholder="0.00"  readonly>
                  <input type="hidden" class="form-control sale-price" name="sale_price[]" placeholder="0.00" readonly>
              </td>
            <td><button type="button" class="removeBtn">❌</button></td>
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


    /** ==============================
     *  Barcode -> Fetch product details
     *  ============================== */
    $(document).on("change", ".barcode", function () 
    {
        let $row = $(this).closest("tr");
        let barcode = $(this).val().trim();
        let from_store = $("#from_store").val();
        let to_store = $("#to_store").val();
        let tax_rule = $("#tax_rule").val();
        let sale_type = 'inter';
    
        if (barcode !== '' && from_store !== '' && to_store !== '' && tax_rule !== '') 
        {
    
            let duplicate = false;
            $("#saleTable .barcode").not(this).each(function () {
                if ($(this).val().trim() === barcode) {
                    duplicate = true;
                    return false; 
                }
            });
    
            if (duplicate) {
                $row.find(".barcode").val("").focus();
                $.toaster({
                    priority: 'danger',
                    title: '⚠️ Duplicate Barcode',
                    message: 'This barcode is already added in another row.'
                });
                return;
            }
    
            $.ajax({
                url: "{{ route('admin.get-store-product-by-barcode') }}",
                type: "GET",
                data: { barcode: barcode, from_store: from_store , to_store: to_store,tax_rule:tax_rule,sale_type:sale_type},
                beforeSend: function () {
                    $("#ajaxLoader").show(); 
                },
                success: function (res) {
                    if (res.success) 
                    {
                        $("#productModal").modal("show");
                        $("#modal_product_type").val(res.data.product_type);
                        $("#modal_product_details").val(res.data.product_details);
                        $("#modal_purchase_price").val(res.data.purchase_price);
                        $("#modal_retail_price").val(res.data.retail_price);
                        $("#modal_product_code").val(res.data.product_code);
                        $("#modal_margin").val(res.data.margin);
                        $("#modal_margin_amount").val(res.data.margin_amount);
                        $("#modal_hsncode").val(res.data.hsn_code);
                        $("#modal_gst").val(res.data.gstRate);
                        $("#modal_base_price").val(res.data.basePrice);
                        $("#modal_gst_amount").val(res.data.gstAmount);
                        $("#modal_total_sale").val(res.data.totalSale);
                        $("#modal_tax_rule").val(res.data.tax_rule);
                        $("#modal_product_id").val(res.data.product_id);
                        
                    } else {
                        $row.find(".barcode").val("").focus();
                        $row.find(".product-type, .product-description, .product-qty, .purchase-price, .retail-price, .margin-amount, .margin").val("");
    
                        $.toaster({
                            priority: 'danger',
                            title: '❌ Barcode Not Found',
                            message: 'Please check and try again.'
                        });
                    }
                },
                error: function () {
                    $.toaster({
                        priority: 'danger',
                        title: '⚠️ Error',
                        message: 'Something went wrong while fetching product.'
                    });
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
                title: '⚠️ Error',
                message: 'Something went wrong while fetching product.'
            });
        }
    });

});

$(document).on("keyup change", ".purchase-price, .margin", function () {
    let $row = $(this).closest("tr");

    let purchasePrice = parseFloat($row.find(".purchase-price").val()) || 0;
    let qty = parseFloat($row.find(".product-qty").val()) || 1;
    let marginPercent = parseFloat($row.find(".margin").val()) || 0;

    // Margin amount = purchasePrice * marginPercent / 100
    let marginAmount = (purchasePrice * marginPercent) / 100;

    // Total purchase price = purchasePrice * qty
    let totalPurchase = purchasePrice + marginAmount;


    // Update fields
    $row.find(".margin-amount").val(marginAmount.toFixed(2));
    $row.find(".total-purchase-price").val(totalPurchase.toFixed(2));
    
    calculateTotals();
});

function calculateTotals() {
    let totalQty = 0;
    let totalPurchase = 0;
    let totalRetail = 0;

    $("#saleTable tbody tr").each(function () {
        let qty = parseFloat($(this).find(".product-qty").val()) || 0;
        let totalPurchasePrice = parseFloat($(this).find(".total-purchase-price").val()) || 0;

        totalQty += qty;
        totalPurchase += totalPurchasePrice;
    });

    $("#total_qty").val(totalQty.toFixed(2));
    $("#total_final_purchase").val(totalPurchase.toFixed(2));
}
</script>

<script>
$(function () {
     function cb(date) {
        $('#reportrange span').html(date.format('MMMM D, YYYY'));
        $('#date_from').val(date.format('YYYY-MM-DD'));
    }
    $('#reportrange').daterangepicker({ singleDatePicker:true, showDropdowns:true, autoUpdateInput:false, locale:{ format:'MMMM D, YYYY' }}, cb);

    function cb1(date) {
        $('#reportrange1 span').html(date.format('MMMM D, YYYY'));
        $('#date_from1').val(date.format('YYYY-MM-DD'));
    }
    $('#reportrange1').daterangepicker({ singleDatePicker:true, showDropdowns:true, autoUpdateInput:false, locale:{ format:'MMMM D, YYYY' }}, cb1);

    let selectedDate = moment();
    $('#reportrangesale span').html(selectedDate.format('MMMM D, YYYY'));
    $('#sale_date').val(selectedDate.format('YYYY-MM-DD'));
});
</script>


<script>

$(document).on("keyup", "#pay_amount", function () 
{
    let totalPayable = parseFloat($("#total_payable").val()) || 0;
    let payAmount    = parseFloat($(this).val()) || 0;
    let pending      = Math.max(totalPayable - payAmount, 0);
    $("#pending_amount").val(pending.toFixed(2));
});

$(document).ready(function() {
    // From Store GST fetch
    $('#from_store').change(function() {
        let storeId = $(this).val();

        if(storeId && storeId === $('#to_store').val()) {
            $.toaster({
                priority: 'danger',
                title: '❌ error',
                message: 'From Store and To Store cannot be the same!'
            });
            $(this).val(''); // reset selection
            $('#from_gst_no').val('');
            return;
        }

        if(storeId) {
            $.get('/get-store-gst/' + storeId, function(data) {
                $('#from_gst_no').val(data.gst_no);
            });
        } else {
            $('#from_gst_no').val('');
        }
    });

    // To Store GST fetch
    $('#to_store').change(function() {
        let storeId = $(this).val();

        if(storeId && storeId === $('#from_store').val()) {
            $.toaster({
                priority: 'danger',
                title: '❌ error',
                message: 'From Store and To Store cannot be the same!'
            });
            $(this).val(''); // reset selection
            $('#to_gst_no').val('');
            return;
        }

        if(storeId) {
            $.get('/get-store-gst/' + storeId, function(data) {
                $('#to_gst_no').val(data.gst_no);
            });
        } else {
            $('#to_gst_no').val('');
        }
    });
});


$(document).on("change", "#tax_rule", function () {
    let taxRule = $(this).val();

    if (taxRule === "Not Appicable") {
        // Hide table headers
        $("#saleTable thead th:nth-child(6), #saleTable thead th:nth-child(7)").hide();

        // Hide all rows' HSN and GST inputs
        $("#saleTable tbody tr").each(function () {
            $(this).find(".hsn-code").closest("td").hide();
            $(this).find(".gst-per").closest("td").hide();
        });
        
        $("#totalbasicdiv").hide();
        $("#totalgstdiv").hide();
    } else {
        // Show table headers
        $("#saleTable thead th:nth-child(6), #saleTable thead th:nth-child(7)").show();

        // Show all rows' HSN and GST inputs
        $("#saleTable tbody tr").each(function () {
            $(this).find(".hsn-code").closest("td").show();
            $(this).find(".gst-per").closest("td").show();
        });
        
        $("#totalbasicdiv").show();
        $("#totalgstdiv").show();
    }
});
</script>
<script>
$(document).ready(function() {

    function calculateValues() {
        let purchasePrice = parseFloat($("#modal_purchase_price").val()) || 0;
        let gstRate = parseFloat($("#modal_gst").val()) || 0;
        let margin = parseFloat($("#modal_margin").val()) || 0;
        let taxRule = $("#modal_tax_rule").val(); // Include | Exclude | Not Applicable

        let basePrice = 0, gstAmount = 0, totalSale = 0, marginAmount = 0;

        // Margin always on purchase
        marginAmount = (purchasePrice * margin) / 100;
        $("#modal_margin_amount").val(marginAmount.toFixed(2));

        if (taxRule === "Include") {
            // Total sale price includes GST
            totalSale = purchasePrice + marginAmount;   // customer pays this
            basePrice = totalSale / (1 + (gstRate / 100)); // back-calc GST-exclusive price
            gstAmount = totalSale - basePrice;
        } 
        else if (taxRule === "Exclude") {
            // GST applied on top
            basePrice = purchasePrice + marginAmount;
            gstAmount = (basePrice * gstRate) / 100;
            totalSale = basePrice + gstAmount;
        } 
        else { 
            // Not Applicable
            basePrice = purchasePrice + marginAmount;
            gstAmount = 0;
            totalSale = basePrice;
        }

        // update fields
        $("#modal_base_price").val(basePrice.toFixed(2));
        $("#modal_gst_amount").val(gstAmount.toFixed(2));
        $("#modal_total_sale").val(totalSale.toFixed(2));
    }

    // Trigger calculation when values change
    $("#modal_purchase_price, #modal_gst, #modal_margin").on("input", calculateValues);

    // Initial calc on modal open
    $('#productModal').on('shown.bs.modal', function () {
        calculateValues();
    });

});

</script>

<script>
$(document).ready(function () {

    // Function to recalc totals
    function recalcTotals() {
        let totalBasic = 0, totalGst = 0, totalSale = 0;

        $(".base-price").each(function () {
            totalBasic += parseFloat($(this).val()) || 0;
        });
        $(".gst-amount").each(function () {
            totalGst += parseFloat($(this).val()) || 0;
        });
        $(".sale-price").each(function () {
            totalSale += parseFloat($(this).val()) || 0;
        });

        $("#total_basic_amount").val(totalBasic.toFixed(2));
        $("#total_gst_amount").val(totalGst.toFixed(2));
        $("#total_sale_amount").val(totalSale.toFixed(2));
        $("#total_payable_amount").val(totalSale.toFixed(2));
        $("#pay_amount").val(totalSale.toFixed(2));
        $("#pending_amount").val(totalSale.toFixed(2)); // here payable = sales
    }

    // Click add button in modal
    $(".addmodalbtn").click(function () {
        let purchasePrice = parseFloat($("#modal_purchase_price").val()) || 0;
        let retailPrice = parseFloat($("#modal_retail_price").val()) || 0;
        let totalSale = parseFloat($("#modal_total_sale").val()) || 0;

        if (purchasePrice <= 0) {
            $.toaster({
                priority: 'danger',
                title: '❌ error',
                message: 'Purchase Price is required'
            });
            return;
        }
        if (retailPrice <= 0) {
            $.toaster({
                priority: 'danger',
                title: '❌ error',
                message: 'Retail Price is required'
            });
            return;
        }
        if (totalSale <= 0) {
             $.toaster({
                priority: 'danger',
                title: '❌ error',
                message: 'Total Sale  is required'
            });
            return;
        }

        // Assume you're editing the current row where barcode was entered
        let currentRow = $("#saleTable tbody tr.active"); 
        if (currentRow.length === 0) {
            currentRow = $("#saleTable tbody tr:first"); // fallback to first row
        }

        // Fill row values
        currentRow.find(".product-type").val($("#modal_product_type").val());
        currentRow.find(".product-description").val($("#modal_product_details").val());
        currentRow.find(".product-qty").val($("#modal_quantity").val());
        currentRow.find(".hsn-code").val($("#modal_hsncode").val());
        currentRow.find(".gst-per").val($("#modal_gst").val());
        currentRow.find(".gst-amount").val($("#modal_gst_amount").val());
        currentRow.find(".margin").val($("#modal_margin").val());
        currentRow.find(".margin-amount").val($("#modal_margin_amount").val());
        currentRow.find(".base-price").val($("#modal_base_price").val());
        currentRow.find(".retail-price").val($("#modal_retail_price").val());
        currentRow.find(".sale-price").val($("#modal_total_sale").val());
        currentRow.find(".product-code").val($("#modal_product_code").val());
        currentRow.find(".product-id").val($("#modal_product_id").val());

        // Close modal
        $("#productModal").modal("hide");

        // Recalc totals
        recalcTotals();
    });

      // Make row active when clicking barcode/product input
    $(document).on("focus", ".barcode", function () {
        $("#saleTable tbody tr").removeClass("active");
        $(this).closest("tr").addClass("active");
    });

    // Remove row and recalc
    $(document).on("click", ".removeBtn", function () {
        $(this).closest("tr").remove();
        recalcTotals();
    });

});

$("#saleForm").submit(function(e)
{
    e.preventDefault(); 
    
    let isValid = true;
    let class_name = '';

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let bill_no = document.getElementById("bill_no" + class_name).value.trim();
    let from_store = document.getElementById("from_store" + class_name).value.trim();
    let to_store = document.getElementById("to_store" + class_name).value.trim();
    let sale_person = document.getElementById("sale_person" + class_name).value.trim();

    if (bill_no   === "")
    {
        document.getElementById("bill_noError" + class_name).textContent = "Bill no required.";
        document.getElementById("bill_no" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (from_store   === "")
    {
        document.getElementById("from_storeError" + class_name).textContent = "Select From store.";
        document.getElementById("from_store" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (to_store   === "")
    {
        document.getElementById("to_storeError" + class_name).textContent = "Select To store.";
        document.getElementById("to_store" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (sale_person   === "")
    {
        document.getElementById("sale_personError" + class_name).textContent = "Select sales person.";
        document.getElementById("sale_person" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (!isValid)
    {
        return;
    }
    
    let form = $("#saleForm")[0];
    let data = new FormData(form);
    
    $.ajax({
        type: 'POST',
        url: "{{ route('admin.add-inter-sale-record') }}",
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
            window.location.href = "{{route('admin.sale-history') }}";
        } else {
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


$(document).on("change", "#from_store", function () {
        let selectedType = $(this).val();
    
        $.ajax({
            url: "{{ route('admin.get-store-details') }}",
            type: "GET",
            data: { selectedType: selectedType },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
            success: function (res) {
            if (res.success) {
                $("#bill_no").val(res.data.order_no);
                if(res.data.sales_tax_type == 0) {
                    $("#saleTable .tax-col").hide();
                    $("#totalbasicdiv").hide();
        
                    // Set tax_rule value AND trigger change event
                    $("#tax_rule").val("Not Applicable").trigger("change");
                }
                else 
                {
                    $("#saleTable .tax-col").show();
                    $("#totalbasicdiv").show();
                    
                    if(res.data.tax_rule == 1)
                    {
                       $("#tax_rule").val("Include").trigger("change");
                    }
                    else
                    {
                        $("#tax_rule").val("Exclude").trigger("change");
                    }
                    
                    $("#sales_text_per").val(res.data.sales_text_per);
                }
        
            } else {
                $.toaster({
                    priority: 'danger',
                    title: '❌ Store Not Found',
                    message: 'Please check and try again.',
                    timeout: 3000
                });
                $("#bill_no").val(''); // Clear input if store not found
            }
        },

            
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
        });
    });
</script>








    
@endsection
