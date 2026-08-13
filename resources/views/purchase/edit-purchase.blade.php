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

.col-md-4
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
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="domestic-orders-header">
                    <h3>Edit  Purchase</h3>
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

                    <div class="row">
                        <div class="col-md-4">
                           <div class="row">
                              <label for="supplier_name" class="col-lg-4 col-form-label">
                                Supplier Name : 
                              </label>
                              <div class="col-lg-8">
                                <input type="text"  value="{{$purchase->supplier_name}}"  class="form-control" readonly>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="row">
                              <label  class="col-lg-6 col-form-label">
                                Purchase Bill Number : 
                              </label>
                              <div class="col-lg-6">
                                <input type="text" name="p_bill_no" value="{{$purchase->p_bill_no}}" class="form-control" readonly>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="row">
                              <label  class="col-lg-4 col-form-label">
                                Store Name :
                              </label>
                              <div class="col-lg-8">
                                <input type="text"  value="{{$purchase->p_bill_no}}" class="form-control" readonly>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                           <div class="row">
                              <label for="purchase_date" class="col-lg-4 col-form-label">
                                Purchase Date :
                              </label>
                              <div class="col-lg-8">
                                <input type="text" value="{{$purchase->purchase_date}}"  class="form-control" readonly>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="row">
                              <label for="tax_rule" class="col-lg-6 col-form-label">
                                Tax Rule :
                              </label>
                              <div class="col-lg-6">
                                <input type="text" id="tax_rule" name="tax_rule" value="{{$purchase->tax_rule}}" class="form-control" readonly>
                              </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <table class="table table-bordered" id="oldvalueTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Products</th>
                      <th>Product Code	</th>
                      <th>Product Details</th>
                      <th>Price Details	</th>
                      @if($purchase->tax_rule != 'Not Applicable')
                      <th>Tax Details	</th>
                      @endif
                      <th>Purchase Price	</th>
                      <th>Qty</th>
                      <th>Total Purchase Price	</th>
                      <th>Retail Price	</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($purchaseproduct as $product)
                      @if($product['p_status'] == '1')
                      <tr style="background-color:red;color:#fff">
                          <td></td>
                          <td>{{$product['product_type']}}</td>
                          <td>{{$product['product_code']}}</td>
                          <td>{{$product['product_details']}}</td>
                          <td>Unit Price :{{$product['product_price']}} <BR> Base Price : {{$product['product_base_price']}} </td>
                          @if($purchase->tax_rule != 'Not Applicable')
                          <td>HSN Code :{{$product['hsn_code']}} <BR> GST: {{$product['gst_amt']}} ({{$product['gst']}} %)</td>
                          @endif
                          <td>{{$product['product_purchase_price']}}</td>
                          <td>{{$product['qty']}}</td>
                          <td>{{$product['total_purchase_price']}}</td>
                          <td>{{$product['product_retail_price']}}</td>
                      </tr>
                      @endif
                      @if($product['p_status'] == '0')
                      <tr>
                          <td></td>
                          <td>{{$product['product_type']}}</td>
                          <td>{{$product['product_code']}}</td>
                          <td>{{$product['product_details']}}</td>
                          <td>Unit Price :{{$product['product_price']}} <BR> Base Price : {{$product['product_base_price']}} </td>
                          @if($purchase->tax_rule != 'Not Applicable')
                          <td>HSN Code :{{$product['hsn_code']}} <BR> GST: {{$product['gst_amt']}} ({{$product['gst']}} %)</td>
                          @endif
                          <td>{{$product['product_purchase_price']}}</td>
                          <td>{{$product['qty']}}</td>
                          <td>{{$product['total_purchase_price']}}</td>
                          <td>{{$product['product_retail_price']}}</td>
                      </tr>
                      @endif
                     @endforeach
                     <tr class="pageText">
                        <td colspan="10">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tbody><tr>
                                    <td style="vertical-align: top;">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="3">
                                            <tbody><tr class="pageText">
                                                <td>
                                                    <div class="inline-block" style="margin-top: 25px;">
                                                        <div style="float: left; margin-left: 15px;">
                                                            <a class="pointer" data-toggle="modal" data-target="#purchaseModal">
                                                                <div class="quick-links-info" style="text-align: center;">
                                                                    <div style="padding-top: 10px;">
                                                                        <i class="fa fa-edit fa-4x" aria-hidden="true" style="margin-left: 10px;"></i>
                                                                        <div class="text-bold" style="line-height: 18px; margin-top: 3px;">Edit<br> Purchase Details</div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                                                                                                                                                            </div>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                    <td width="40%" style="vertical-align: top;">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr class="pageText">
                                                <td style="text-align: right; width:39%;"><b>Total Quantity : </b></td>
                                                <td align="left" style="padding-left: 3px;">
                                                    <div class="readOnlyDiv td-data-height" style="text-align: right;">
                                                        {{$purchase->total_qty}} 
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="pageText">
                                                <td style="text-align: right;"><b>Total Unit Amount : </b></td>
                                                <td align="left" style="padding-left: 3px;">
                                                    <div class="readOnlyDiv td-data-height" style="text-align: right;">
                                                        {{$purchase->total_unit_amount}}                                                                     
                                                    </div>
                                                </td>
                                            </tr>
                                            @if($purchase->tax_rule != 'Not Applicable')
                                            <tr class="pageText">
                                                <td style="text-align: right;"><b>Total Base Price :</b></td>
                                                <td align="left" style="padding-left: 3px;">
                                                    <div class="readOnlyDiv td-data-height" style="text-align: right;">
                                                        {{$purchase->total_base_amount}}                                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="pageText">
                                                <td style="text-align: right;"><b>Total GST Amount :</b></td>
                                                <td align="left" style="padding-left: 3px;">
                                                    <div class="readOnlyDiv td-data-height" style="text-align: right;">
                                                       {{$purchase->total_gst_amount}}                                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr class="pageText">
                                                <td style="text-align: right;"><b>Total Purchase :</b></td>
                                                <td align="left" style="padding-left: 3px;">
                                                    <div class="readOnlyDiv td-data-height" style="text-align: right;">
                                                        {{$purchase->total_p_amount}}                                                                     
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="pageText">
                                                <td style="text-align: right;"><b>Round Off :&nbsp;</b></td>
                                                <td align="left" style="padding-left: 3px;">
                                                    <div class="readOnlyDiv td-data-height" style="text-align: right;">
                                                        {{$purchase->round_off}}                                                                         
                                                </div>
                                                </td>
                                            </tr>
                                            <tr class="pageText">
                                                <td style="text-align: right;"><b>Total Net Purchase :</b></td>
                                                <td align="left" style="padding-left: 3px;">
                                                    <div class="readOnlyDiv td-data-height" style="text-align: right;">
                                                        {{$purchase->net_purchase_amount}}                                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                  </tbody>
                </table>
                    </div>
            </div>
        </div>   
            </div>
        </div>
         
    </div>
</section>


<div class="modal fade" data-backdrop="static" id="purchaseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Purchase Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form id="purchaseForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="purchase_id" id="purchase_id" value="{{ $purchase->purchase_id }}">
                    <div class="row">
                        <div class="OverDiv modal-rules-section mt-24 mt-none mlr-none">
                            <div style="color: red; padding: 6px;">
                                <strong>Note :</strong>
                                <ol style="margin: 0;">
                                    <li>The system will not allow you to update the supplier name of a purchase bill number that is already in the system because other details, such as the purchase date and tax details may differ and we are not checking or updating these details during supplier name modification.</li>
                                    <li>The system will not allow users to change the purchasing bill number for a supplier name that is already in the system since other details such as the purchase date and tax details may differ and we do not check or update these facts during purchase bill number edits.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Supplier Name <span class="text-danger">*</span></label>
                            <input class="form-control" id="supplier_name" name="supplier_name"
                                   value="{{ $purchase->supplier_name }}" autocomplete="off">
                            <span class="error badge text-danger" id="supplier_nameError"></span>
                        </div>

                        <div class="col-md-4">
                            <label>Purchase Bill No <span class="text-danger">*</span></label>
                            <input class="form-control" id="p_bill_no" name="p_bill_no"
                                   value="{{ $purchase->p_bill_no }}">
                            <span class="error badge text-danger" id="p_bill_noError"></span>
                        </div>

                        <div class="col-md-4">
                            <label>Purchase Date <span class="text-danger">*</span></label><br>
          
                            <input type="date" id="purchase_date" value="{{ $purchase->purchase_date }}" name="date_from">
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-gradient" type="submit">Submit</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>


@endsection

@section('scripts')



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


});



</script>

<script>
$("#purchaseForm").submit(function (e) {
    e.preventDefault();

    let isValid = true;

    // Reset errors
    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let supplier_name = $("#supplier_name").val().trim();
    let p_bill_no = $("#p_bill_no").val().trim();

    // Validate Supplier Name
    if (supplier_name === "") {
        $("#supplier_nameError").text("Select Supplier Name.");
        $("#supplier_name").addClass("is-invalid");
        isValid = false;
    }

    // Validate Bill No
    if (p_bill_no === "") {
        $("#p_bill_noError").text("Enter Purchase Bill Number.");
        $("#p_bill_no").addClass("is-invalid");
        isValid = false;
    }

    if (!isValid) return;

    let form = $("#purchaseForm")[0];
    let data = new FormData(form);

    $.ajax({
        type: "POST",
        url: "{{ route('admin.update-purchase-record') }}",
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
                    priority: 'success',
                    title: response.success,
                    message: ''
                });

                window.location.href = "{{ route('admin.purchase-history') }}";
            } else {
                // Reset errors
                $(".error").text("");
                $(".is-invalid").removeClass("is-invalid");

                $.each(response.error, function (index, value) {
                    $("#" + index + "Error").text(value);
                    $("#" + index).addClass("is-invalid");
                });
            }
        },
        complete: function () {
            $("#ajaxLoader").fadeOut();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error: " + textStatus + " - " + errorThrown);
    });

});



</script>




    
@endsection
