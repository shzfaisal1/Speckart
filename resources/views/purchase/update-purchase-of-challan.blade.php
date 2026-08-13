@extends('layouts.master')
@section('styles')
  
<style>

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
                    <h3>Update Purchase Details of Challan</h3>
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
                            <label for="">Invoice / Bill Date <span class="text-danger">*</span></label>
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
                                <input class="form-control"  placeholder="Enter Supplier Name" value="{{$supplier_name}}" id="supplier_name" name="supplier_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">Invoice / Bill Number<span class="text-danger">*</span></label>
                            <input class="form-control"  placeholder="Enter Purchase Bill Number" id="p_bill_no" name="p_bill_no">
                            <span class="error badge text-danger" id="p_bill_noError"></span>
                        </div>
                        <div class="col-md-3">
                            <label for="">Store<span class="text-danger">*</span></label>
                        <select class="form-control select" style="height: 32px !important" id="store_id" name="store_id" >
                            <?php 
                              $tbl_store = DB::table("tbl_store")->where('status',1)->get(); 
                              $matchedStore = $tbl_store->firstWhere('id', $stid);
                            ?>
                    
                            @if($matchedStore)
                                <option value="{{ $matchedStore->id }}" selected>
                                    {{ $matchedStore->store_name }} / ({{ $matchedStore->store_id }})
                                </option>
                            @endif
                        </select>
                        <span class="error badge text-danger" id="store_idError"></span>
                    </div>
                        <div class="col-md-3">
                            <label for="">Tax Rule <span class="text-danger">*</span></label>
                            <select class="form-control select" style="height: 32px !important;" id="tax_rule" name="tax_rule">
                                  <option value="">Select Tax rule</option>
                                  <option value="Not Applicable" @if($taxRule == 'Not Applicable') selected @endif>Not Applicable</option>
                                  <option value="Include"  @if($taxRule == 'Include') selected @endif>Include</option>
                                  <option value="Exclude" @if($taxRule == 'Exclude') selected @endif>Exclude</option>
                                  
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
                                            <th>Challan Details</th>
                                            <th>Product Details</th>
                                            <th>Unit Price</th>
                                            <th>Base Price</th>
                                            <th>HSN/SAC Code</th>
                                            <th>GST %</th>
                                            <th>Purchase Price</th>
                                            <th>Qty</th>
                                            <th>Total Purchase Price</th>
                                            <th>Retail Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($challanproduct as $product)
                                    
                                    @php
                                        $tbl_challan = DB::table("tbl_challan")
                                                        ->where('id', $product->challan_id)
                                                        ->first();
                                    @endphp
                                    
                                    <tr>
                                    
                                        <td>
                                            <input type="hidden" name="challanproductid[]" value="{{ $product->id }}">
                                            <input type="hidden" name="product_type[]" value="{{ $product->product_type }}">
                                            <input type="hidden" name="product_code[]" value="{{ $product->product_code }}">
                                    
                                            {{ $product->challan_no }} <br>
                                            {{ $tbl_challan->challan_date ?? '' }}
                                        </td>
                                    
                                        <td>
                                            Product : {{ $product->product_type }} <br>
                                            Product Code : {{ $product->product_code }} <br>
                                            Description : {{ $product->product_details }}
                                        </td>
                                    
                                        <td>
                                                <input type="text"
                                               style="width:80px"
                                               class="form-control unit-price"
                                               name="product_price[]"
                                               value="{{ $product->product_base_price }}">
                                        </td>
                                    
                                        <td>
                                            <input type="text"
                                                   style="width:80px"
                                                   class="form-control base-price"
                                                   name="product_base_price[]"
                                                   value="{{ $product->product_base_price }}">
                                        </td>
                                    
                                        <td>
                                            <input type="text"
                                                   style="width:80px"
                                                   class="form-control hsn-code"
                                                   name="hsn_code[]"
                                                   value="{{ $product->hsn_code }}"
                                                   readonly>
                                        </td>
                                    
                                        <td>
                                            <div class="input-group" style="width:120px">
                                                <input type="text"
                                                       class="form-control gst-amount"
                                                       name="gst_amt[]"
                                                       value="{{ $product->gst_amt }}"
                                                       readonly>
                                    
                                                <input type="text"
                                                       class="form-control gst"
                                                       name="gst[]"
                                                       value="{{ $product->gst }}"
                                                       readonly>
                                            </div>
                                        </td>
                                    
                                        <td>
                                            <input type="text"
                                                   style="width:80px"
                                                   class="form-control product-purchase-price"
                                                   name="product_purchase_price[]"
                                                   value="{{ $product->product_purchase_price }}"
                                                   readonly>
                                        </td>
                                    
                                        <td>
                                            <input type="text"
                                                   style="width:50px"
                                                   class="form-control product-qty"
                                                   name="product_qty[]"
                                                   value="{{ $product->qty }}"
                                                   readonly>
                                        </td>
                                    
                                        <td>
                                            <input type="text"
                                                   style="width:80px"
                                                   class="form-control total-purchase-price"
                                                   name="total_purchase_price[]"
                                                   value="{{ $product->total_purchase_price }}"
                                                   readonly>
                                        </td>
                                    
                                        <td>
                                            <input type="text"
                                                   style="width:80px"
                                                   class="form-control retail-price"
                                                   name="product_retail_price[]"
                                                   value="{{ $product->product_retail_price }}"
                                                   readonly>
                                        </td>
                                    
                                    </tr>
                                    
                                    @endforeach
                                    </tbody>
                                </table>
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
                                    <input class="form-control" type="text"  id="total_qty" name="total_qty" readonly>
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







@endsection

@section('scripts')

<script>

$(function () {

    // =========================
    // DATE PICKER
    // =========================

    var selectedDate = moment();

    function cb(date) {
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

$(document).ready(function () {
     // ==========================
    // UNIT PRICE CHANGE
    // ==========================
    
    $(document).on(
        "change blur",
        ".unit-price",
        function ()
        {
            let row = $(this).closest("tr");
    
            calculateRow(row, 'unit-price');
    
            calculateTotals();
        }
    );
    
    // ==========================
    // BASE PRICE CHANGE
    // ==========================
    
    $(document).on(
        "change blur",
        ".base-price",
        function ()
        {
            let row = $(this).closest("tr");
    
            calculateRow(row, 'base-price');
    
            calculateTotals();
        }
    );
    
    // ==========================
    // OTHER FIELD CHANGES
    // ==========================
    
    $(document).on(
        "change blur",
        ".product-qty, .gst, #tax_rule, #round_off",
        function ()
        {
            $("#saleTable tbody tr").each(function ()
            {
                calculateRow($(this), 'unit-price');
            });
    
            calculateTotals();
        }
    );
    // ==========================
    // NUMBER FORMAT
    // ==========================

    function parseNum(val)
    {
        return parseFloat(val) || 0;
    }

    // ==========================
    // CALCULATE SINGLE ROW
    // ==========================

    function calculateRow(row, changedField = '')
{
    let taxRule = $("#tax_rule").val();

    let unitPrice = parseFloat(
        row.find(".unit-price").val()
    ) || 0;

    let basePrice = parseFloat(
        row.find(".base-price").val()
    ) || 0;

    let qty = parseFloat(
        row.find(".product-qty").val()
    ) || 0;

    let gst = parseFloat(
        row.find(".gst").val()
    ) || 0;

    let gstAmount = 0;
    let purchasePrice = 0;
    let totalPurchase = 0;

    // ==========================
    // UNIT PRICE CHANGED
    // ==========================

    if (changedField === 'unit-price')
    {
        if (taxRule === "Include")
        {
            basePrice = unitPrice / (1 + gst / 100);
        }
        else
        {
            basePrice = unitPrice;
        }
    }

    // ==========================
    // BASE PRICE CHANGED
    // ==========================

    if (changedField === 'base-price')
    {
        if (taxRule === "Include")
        {
            unitPrice = basePrice + ((basePrice * gst) / 100);
        }
        else
        {
            unitPrice = basePrice;
        }
    }

    // ==========================
    // GST
    // ==========================

    gstAmount = (basePrice * gst) / 100;

    // ==========================
    // PURCHASE PRICE
    // ==========================

    if (taxRule === "Include")
    {
        purchasePrice = unitPrice;
    }
    else if (taxRule === "Exclude")
    {
        purchasePrice = basePrice + gstAmount;
    }
    else
    {
        purchasePrice = basePrice;
    }

    totalPurchase = purchasePrice * qty;

    // ==========================
    // IMPORTANT FIX
    // ==========================

    // DO NOT RESET CURRENT INPUT

    if (changedField !== 'unit-price')
    {
        row.find(".unit-price")
            .val(unitPrice.toFixed(2));
    }

    if (changedField !== 'base-price')
    {
        row.find(".base-price")
            .val(basePrice.toFixed(2));
    }

    row.find(".gst-amount")
        .val(gstAmount.toFixed(2));

    row.find(".product-purchase-price")
        .val(purchasePrice.toFixed(2));

    row.find(".total-purchase-price")
        .val(totalPurchase.toFixed(2));
}

    // ==========================
    // TOTAL CALCULATION
    // ==========================

    function calculateTotals()
    {
        let totalQty = 0;
        let totalUnit = 0;
        let totalBase = 0;
        let totalGst = 0;
        let totalPurchase = 0;

        $("#saleTable tbody tr").each(function ()
        {
            let row = $(this);

            totalQty += parseNum(
                row.find(".product-qty").val()
            );

            totalUnit += parseNum(
                row.find(".unit-price").val()
            );

            totalBase += parseNum(
                row.find(".base-price").val()
            );

            totalGst += parseNum(
                row.find(".gst-amount").val()
            );

            totalPurchase += parseNum(
                row.find(".total-purchase-price").val()
            );
        });

        $("#total_qty").val(totalQty);

        $("#total_unit_amount")
            .val(totalUnit.toFixed(2));

        $("#total_base_amount")
            .val(totalBase.toFixed(2));

        $("#total_gst_amount")
            .val(totalGst.toFixed(2));

        $("#total_p_amount")
            .val(totalPurchase.toFixed(2));

        let roundOff = parseNum(
            $("#round_off").val()
        );

        $("#net_purchase_amount")
            .val((totalPurchase + roundOff).toFixed(2));
    }

    // ==========================
    // UNIT PRICE CHANGE
    // ==========================

    $(document).on(
        "keyup change input",
        ".unit-price",
        function ()
        {
            let row = $(this).closest("tr");

            calculateRow(row, 'unit-price');

            calculateTotals();
        }
    );

    // ==========================
    // BASE PRICE CHANGE
    // ==========================

    $(document).on(
        "keyup change input",
        ".base-price",
        function ()
        {
            let row = $(this).closest("tr");

            calculateRow(row, 'base-price');

            calculateTotals();
        }
    );

    // ==========================
    // OTHER FIELD CHANGES
    // ==========================

    $(document).on(
        "keyup change input",
        ".product-qty, .gst, #tax_rule, #round_off",
        function ()
        {
            $("#saleTable tbody tr").each(function ()
            {
                calculateRow($(this), 'unit-price');
            });

            calculateTotals();
        }
    );

    // ==========================
    // INITIAL LOAD
    // ==========================

    $("#saleTable tbody tr").each(function ()
    {
        calculateRow($(this), 'unit-price');
    });

    calculateTotals();

});


// =========================
// FORM SUBMIT
// =========================

$("#purchaseForm").submit(function (e) {

    e.preventDefault();

    let isValid = true;

    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");

    let supplier_name = $("#supplier_name").val().trim();
    let p_bill_no     = $("#p_bill_no").val().trim();
    let store_id      = $("#store_id").val();
    let tax_rule      = $("#tax_rule").val();

    // =========================
    // VALIDATION
    // =========================

    if (supplier_name === "") {

        $("#supplier_nameError")
            .text("Supplier required");

        $("#supplier_name")
            .addClass("is-invalid");

        isValid = false;
    }

    if (p_bill_no === "") {

        $("#p_bill_noError")
            .text("Bill number required");

        $("#p_bill_no")
            .addClass("is-invalid");

        isValid = false;
    }

    if (store_id === "") {

        $("#store_idError")
            .text("Select Store");

        $("#store_id")
            .addClass("is-invalid");

        isValid = false;
    }

    if (tax_rule === "") {

        $("#tax_ruleError")
            .text("Select Tax Rule");

        $("#tax_rule")
            .addClass("is-invalid");

        isValid = false;
    }

    if (!isValid) {
        return false;
    }

    // =========================
    // AJAX SUBMIT
    // =========================

    let form = $("#purchaseForm")[0];

    let data = new FormData(form);

    $.ajax({

        type: "POST",

        url: "{{ route('admin.add-challan-to-purchase-record') }}",

        data: data,

        processData: false,

        contentType: false,

        dataType: "json",

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

                window.location.href =
                    "{{ route('admin.purchase-history') }}";
            }
            else {

                $.each(response.error, function (key, value) {

                    $("#" + key + "Error").text(value);

                    $("#" + key).addClass("is-invalid");
                });
            }
        },

        error: function (xhr) {

            console.log(xhr.responseText);

            alert("Something went wrong.");
        },

        complete: function () {

            $("#ajaxLoader").hide();
        }

    });

});

</script>


    
@endsection
