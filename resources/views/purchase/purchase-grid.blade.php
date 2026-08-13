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

table input {
  width: 40px;
  text-align: center;
}
.table-responsive {
      max-height: 300px;
      overflow: auto;
    }

    table {
      border-collapse: separate;
      border-spacing: 0;
    }

    thead th {
      position: sticky;
      top: 0;
      background: #f8f9fa;
      z-index: 2;
    }

    tbody th {
      position: sticky;
      left: 0;
      background: #f8f9fa;
      z-index: 1;
    }

    thead th:first-child {
      left: 0;
      z-index: 3; /* fixes overlapping corner */
    }



    th, td {
      text-align: center;
      vertical-align: middle;
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
                    <h3>Glass Grid Purchase</h3>
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
                    <div class="col-md-22">
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
                            <label for="">Tax Rule <span class="text-danger">*</span></label><br>
                            <select class="form-control select" style="height: 32px !important;" id="tax_rule" name="tax_rule">
                                 <option value="Include">Include</option>
                                  <option value="Not Appicable">Not Appicable</option>
                                  <option value="Exclude ">Exclude</option>
                                  
                            </select>
                            <span class="error badge text-danger" id="tax_ruleError"></span>
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
                    </div>
                    <br>
                    <div class="product-row">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Product Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control product-code" name="product_code" id="product_code">
                                <span class="error badge text-danger" id="product_codeError"></span>
                            </div>
                            <input type="hidden" class="form-control product-id" name="product_id" id="product_id">
                            
                            <div class="col-md-9">
                                <label>Details <span class="text-danger">*</span></label>
                                <input type="text" class="form-control product-detail" name="product_details" id="product_details">
                                <span class="error badge text-danger" id="product_detailsError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Company </label>
                                <input type="text" class="form-control company-detail" name="company_detail" id="company_detail">
                                <span class="error badge text-danger" id="company_detailError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Quality </label>
                                <input type="text" class="form-control quality-detail" name="quality_detail" id="quality_detail">
                                <span class="error badge text-danger" id="quality_detailError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Color </label>
                                <input type="text" class="form-control color-detail" name="color_details" id="color_details">
                                <span class="error badge text-danger" id="color_detailsError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Material </label>
                                <input type="text" class="form-control material-detail" name="material_detail" id="material_detail">
                                <span class="error badge text-danger" id="material_detailError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Coating </label>
                                <input type="text" class="form-control coating-detail" name="coating_detail" id="coating_detail">
                                <span class="error badge text-danger" id="coating_detailError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Design </label>
                                <input type="text" class="form-control design-detail" name="design_details" id="design_details">
                                <span class="error badge text-danger" id="design_detailsError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Index </label>
                                <input type="text" class="form-control index-detail" name="index_detail" id="index_detail">
                                <span class="error badge text-danger" id="index_detailError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Addition </label>
                                <input type="text" class="form-control" name="addiional_detail" id="addiional_detail">
                                <span class="error badge text-danger" id="addiional_detailError"></span>
                            </div>
                            <div class="col-md-2">
                                <label>Axis </label>
                                <input type="text" class="form-control" name="axis_detail" id="axis_detail">
                                <span class="error badge text-danger" id="axis_detailError"></span>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <label>Positive </label>
                            <div class="table-responsive">
                              <table class="table card-table table-vcenter text-nowrap" style="color: #000;">
                                <thead>
                                  <tr>
                                    <th style="color: #6b6f80;">SPH<br>CYL</th>
                                    <script>
                                      let headerRow = '';
                                      for (let i = 0; i <= 16; i++) {
                                        let val = (i * 0.25).toFixed(2);
                                        headerRow += `<th style="color: #6b6f80;">+${val}</th>`;
                                      }
                                      document.write(headerRow);
                                    </script>
                                  </tr>
                                </thead>
                                <tbody>
                                  <script>
                                    for (let i = 0; i <= 16; i++) {
                                      let cylVal = (i * 0.25).toFixed(2);
                                      document.write(`<tr><th>+${cylVal}</th>`);
                                      for (let j = 0; j <= 16; j++) {
                                        let sphVal = (j * 0.25).toFixed(2);
                                        document.write(`<td><input type="text" name="matrix[+${sphVal}][+${cylVal}]" value="0"></td>`);
                                      }
                                      document.write(`</tr>`);
                                    }
                                  </script>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        
                          <div class="col-md-6">
                            <label>Negative </label>
                            <div class="table-responsive">
                              <table class="table card-table table-vcenter text-nowrap" style="color: #000;">
                                <thead>
                                  <tr>
                                    <th style="color: #6b6f80;">SPH<br>CYL</th>
                                    <script>
                                      let headerRowneg = '';
                                      for (let i = 0; i <= 16; i++) {
                                        let val = (i * 0.25).toFixed(2);
                                        headerRowneg += `<th style="color: #6b6f80;">-${val}</th>`;
                                      }
                                      document.write(headerRowneg);
                                    </script>
                                  </tr>
                                </thead>
                                <tbody>
                                  <script>
                                    for (let i = 0; i <= 16; i++) {
                                      let cylVal = (i * 0.25).toFixed(2);
                                      document.write(`<tr><th>-${cylVal}</th>`);
                                      for (let j = 0; j <= 16; j++) {
                                        let sphVal = (j * 0.25).toFixed(2);
                                        document.write(`<td><input type="text" name="matrix[-${sphVal}][-${cylVal}]" value="0"></td>`);
                                      }
                                      document.write(`</tr>`);
                                    }
                                  </script>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        
                        <br>
                        
                        <div class="row">
                          <div class="col-md-4">
                            <div class="row">
                              <label class="col-lg-4 col-form-label">Purchase Rs <span class="text-danger">*</span></label>
                              <div class="col-lg-8">
                                <input type="text" id="product_price" name="product_price" class="form-control product-price" placeholder="Enter amount">
                              </div>
                            </div>
                          </div>
                        
                          <div class="col-md-4">
                            <div class="row">
                              <label class="col-lg-4 col-form-label">Retail Price <span class="text-danger">*</span></label>
                              <div class="col-lg-8">
                                <input type="text" id="product_retail_price" name="product_retail_price" class="form-control product-retail-price" placeholder="Enter amount">
                              </div>
                            </div>
                          </div>
                        
                          <div class="col-md-4">
                            <div class="row">
                              <label class="col-lg-4 col-form-label">Qty <span class="text-danger">*</span></label>
                              <div class="col-lg-8">
                                <input type="text" id="qty" name="qty" class="form-control product-qty" readonly>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <br>
                        
                        <div class="row">
                          <div class="col-md-4">
                            <div class="row">
                              <label class="col-lg-5 col-form-label">HSN/SAC Code <span class="text-danger">*</span></label>
                              <div class="col-lg-7">
                                <input type="text" id="modal_hsn_code" name="hsn_code" class="form-control">
                              </div>
                            </div>
                          </div>
                        
                          <div class="col-md-4">
                            <div class="row">
                              <label class="col-lg-4 col-form-label">GST % <span class="text-danger">*</span></label>
                              <div class="col-lg-8">
                                <input type="text" id="modal_gst" name="gst" class="form-control">
                              </div>
                            </div>
                          </div>
                        
                          
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
                    <div class="col-md-6">
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
                                <input type="text" id="total_net_purchase" name="total_purchase_price" class="form-control" readonly placeholder="0.00">
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
    const isAdmin = @json($usr->roles[0]->name === 'Admin');
</script>
<script>
$(function () {
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

$(document).ready(function () 
{
    function toggleProductContainer()
    {
        const supplierName = $('#supplier_name').val()?.trim();
        const billNo       = $('#p_bill_no').val()?.trim();
        const taxRule      = $('#tax_rule').val()?.trim();
        const store_id  = $('#store_id').val()?.trim();
    
        let enabled = supplierName && billNo && taxRule && store_id;

        $('.product-code')
            .prop('disabled', !enabled);
    
        $('#product-container').show();
    }
    
    $('#supplier_name, #p_bill_no, #store_id, #tax_rule')
        .on('input change', toggleProductContainer);
    
    toggleProductContainer();
    
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

    $(document).on('keyup', '#product_code', function () {
        let $input = $(this);
        let productCode = $input.val();
        let productType = 'Glass';

        if (productCode.length >= 2 && productType !== '') {
            $.ajax({
                url: "{{ route('admin.get-product-wise-code') }}",
                method: 'GET',
                data: {
                    product_type: productType,
                    query: productCode
                },
                success: function (response) {
                    let suggestionBox = $input.siblings('.suggestion-box');
                    if (suggestionBox.length === 0) {
                        suggestionBox = $('<div class="suggestion-box list-group position-absolute w-100"></div>');
                        $input.after(suggestionBox);
                    }

                    suggestionBox.empty();
                    if (response.length > 0) {
                        response.forEach(function (item) {
                            suggestionBox.append(`<a href="#" class="list-group-item list-group-item-action">${item.productdetails}</a>`);
                        });
                    } else {
                        suggestionBox.append('<div class="list-group-item text-muted">No results</div>');
                    }

                    suggestionBox.show();
                }
            });
        } else {
            $input.siblings('.suggestion-box').hide();
        }
    });
    
    $(document).on('click', '.suggestion-box a', function (e) {
        e.preventDefault();
    
        let $this = $(this);
        let selectedCode = $this.text().trim();
        let $input = $this.closest('.suggestion-box').prev('.product-code');
        let tax_rule = $('#tax_rule').val().trim();
    
        $input.val(selectedCode);
        $this.closest('.suggestion-box').hide();
    
        let productType = 'Glass';
    
        $.ajax({
            url: "{{ route('admin.get-product-details') }}",
            method: 'GET',
            data: {
                product_type: productType,
                tax_rule: tax_rule,
                productdetails: selectedCode
            },
            success: function (response) {
                if (!response) return;
    
                const fieldMap = {
                    '.product-code': 'product_code',
                    '.product-detail': 'product_name',
                    '.company-detail': 'Company',
                    '.quality-detail': 'Quality',
                    '.color-detail': 'Color',
                    '.material-detail': 'Material',
                    '.coating-detail': 'Coating',
                    '.design-detail': 'Design',
                    '.index-detail': 'Index',
                    '.product-price': 'Purchase_Price',
                    '.product-retail-price': 'Retail_Price',
                    '.product-id': 'product_id',
                };
    
                let $container = $input.closest('.product-row');
    
                for (const [selector, key] of Object.entries(fieldMap)) {
                    let $field = $container.find(selector);
                    if ($field.length) {
                        if ($field.is(':visible')) {
                            $field.val(response[key] ?? '').prop('disabled', false);
                        } else if ($field.is('[type="hidden"]')) {
                            $field.val(response[key] ?? '');
                        }
                    }
                }
    
                calculateRow($container); // Pass row to calculate specific row
            },
            error: function (xhr) {
                console.error("Failed to fetch product details:", xhr.responseText);
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch product details.'
                });
            }
        });
    });
    
     $(document).on('input', '.product-price, .gst, .product-qty, .product-base-price', function () {
        calculateRow($(this).closest('.product-row'));
    });
    
    function calculatePrices(price, gstRate, qty, tax_rule) {
        let basePrice = 0, gstAmount = 0, purchasePrice = 0;
    
        if (tax_rule === 'Include') {
            basePrice = price / (1 + gstRate / 100);
            gstAmount = price - basePrice;
            purchasePrice = price;
        } else {
            basePrice = price;
            gstAmount = (basePrice * gstRate) / 100;
            purchasePrice = basePrice + gstAmount;
        }
    
        const totalPrice = purchasePrice * qty;
    
        return {
            basePrice: basePrice.toFixed(2),
            gstAmount: gstAmount.toFixed(2),
            purchasePrice: purchasePrice.toFixed(2),
            totalPrice: totalPrice.toFixed(2)
        };
    }
    
    function updateTotals() {
        let totalQty = 0;
        $('input[name^="matrix"]').each(function () {
            const val = parseFloat($(this).val());
            if (!isNaN(val)) totalQty += val;
        });
        $('#qty').val(totalQty);
    
        const tax_rule = $('#tax_rule').val();
        const price = parseFloat($('#product_price').val()) || 0;
        const gstRate = parseFloat($('#modal_gst').val()) || 0;
        const qty = totalQty;
    
        const { basePrice, gstAmount, purchasePrice, totalPrice } = calculatePrices(price, gstRate, qty, tax_rule);
    
        $('#product_base_price').val(basePrice);
        $('#gst_amt').val(gstAmount);
        $('#total_purchase').val(purchasePrice);
        $('#total_purchase_price').val(totalPrice);
    
        const roundOff = parseFloat($('#round_off').val()) || 0;
        const netPurchase = parseFloat(totalPrice) + roundOff;
        $('#total_net_purchase').val(netPurchase.toFixed(2));
    }
    
    // Trigger updates on user actions
    $(document).on('input', 'input[name^="matrix"], #product_price, #modal_gst, #round_off, #tax_rule', function () {
        updateTotals();
    });
    
    $("#purchaseForm").submit(function(e) {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let supplier_name = document.getElementById("supplier_name" + class_name).value.trim();
        let p_bill_no = document.getElementById("p_bill_no" + class_name).value.trim();
        let product_code = document.getElementById("product_code" + class_name).value.trim();
        let store_id = document.getElementById("store_id" + class_name).value.trim();

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
    
        if (product_code === "") {
            document.getElementById("product_codeError" + class_name).textContent = "Product Code.";
            document.getElementById("product_code" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        

        
        if (tax_rule === "") {
            document.getElementById("tax_ruleError" + class_name).textContent = "Select tax rule.";
            document.getElementById("tax_rule" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        if (store_id === "") {
            document.getElementById("store_idError" + class_name).textContent = "Select store.";
            document.getElementById("store_id" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        let hasMatrixValue = false;
        $('input[name^="matrix"]').each(function () {
            let val = parseFloat($(this).val());
            if (!isNaN(val) && val > 0) {
                hasMatrixValue = true;
                return false; // break loop
            }
        });
    
        if (!hasMatrixValue) {
            $.toaster({ priority: 'danger', title: 'At least one matrix value must be greater than 0.', message: '' });
            isValid = false;
        }
    
        if (!isValid) {
            return;
        }
        
        
        $('input[name^="matrix"]').each(function () {
            const val = parseFloat($(this).val());
            if (isNaN(val) || val <= 0) {
                $(this).removeAttr('name');
            }
        });
    
        let form = $("#purchaseForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.purchase-grid-add') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
                beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function(response) {
                if ($.isEmptyObject(response.error)) {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: ''
                    });
                    window.location.href = "{{ route('admin.purchase-history') }}";
                } else {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                    $.each(response.error, function(index, value) {
                        if (value.includes("supplier_name")) {
                            $("#supplier_nameError").text(value);
                            $("#supplier_name").addClass("is-invalid");
                        }
                        if (value.includes("p_bill_no")) {
                            $("#p_bill_noError").text(value);
                            $("#p_bill_no").addClass("is-invalid");
                        }
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
 

    
}); 



$(document).on("change", "#store_id", function () 
{
    let selectedType = 'Glass';
    
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
           
       
        },
        complete: function () {
            $("#ajaxLoader").fadeOut(); 
        }
        
    });

});
</script>





    
@endsection