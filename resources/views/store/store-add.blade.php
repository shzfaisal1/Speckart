@extends('layouts.master')
@section('styles')
  
    
<style>
.ms-auto {
    margin-left: auto !important;
}
.alert 
{
    font-size: 13px;
    text-align: left;
    padding: 0px 0px;
}
.tooltip {
  position: relative;
  display: inline-block;
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
    width: 250px;
    background-color: black;
    color: #fff;
    /* text-align: center; */
    padding: 10px;
    border-radius: 6px;
    position: absolute;
    /* z-index: 1; */
    font-size: 11px !important;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}

.select2-container--default .select2-selection--multiple 
{
    width: 100% !important;
}
</style>
@endsection
@section('content')

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
                    <h3>Add New Store</h3>
                    <a href="{{route('admin.store-list')}}" class=" btn">
                        <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                        Store List
                    </a>
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:10px">
            <div class="card-body" style="padding: 5px 10px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <ul>
                                <li>All fields marked with * are mandatory.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form id="storeForm" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Store Name: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter store name" id="store_name" name="store_name" >
                                <span class="error badge text-danger" id="store_nameError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Contact no" name="contact_no" id="contact_no"
                                 maxlength="10"  pattern="^[6-9][0-9]{9}$">
                                 <span class="error badge text-danger" id="contactError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Email Id: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Email" name="email_id" id="email_id">
                                <span class="error badge text-danger" id="email_idError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">State: <span class="text-danger">*</span></label>
                                <select class="form-control select" name="state_id" id="state_id" >
                                    <option value="" disabled selected>Select State</option>
                                </select>
                                <span class="error badge text-danger" id="state_idError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">City: <span class="text-danger">*</span></label>
                                <select class="form-control select" name="city_id" id="city_id" >
                                    <option value="" disabled selected>Select City</option>
                                </select>
                                <span class="error badge text-danger" id="city_idError"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Store Address: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter address" name="store_address" id="store_address">
                                <span class="error badge text-danger" id="store_addressError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Pincode: <span class="text-danger">*</span></label>
                                <input type="text" maxlength="7" class="form-control" placeholder="Enter Pincode" name="pincode" id="pincode">
                                <span class="error badge text-danger" id="pincodeError"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <label for="" class="form-label">Barcode Name: <span class="text-danger">*</span>
                                <div class="tooltip">
                                    <span class="tooltiptext">Maximum 25 characters are allowed for barcode name.</span>
                                    <i class="fa fa-question-circle" aria-hidden="true"></i>
                                </div>    
                            </label>
                            <input type="text" class="form-control" maxlength="25" placeholder="Enter barcode name" name="barcode_name" id="barcode_name">
                             <span class="error badge text-danger" id="barcode_nameError"></span>
                            </div>
                           
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Order Number Prefix: <span class="text-danger">*</span>
                                    <div class="tooltip">
                                        <span class="tooltiptext">
                                            <ul>
                                                <li>This will be prefix for your ORDER numbers. You can use any letters or numbers of your choice.</li>
                                                <li>We recommend that you should use it like FY2018-19/OPT/.</li>
                                                <li>Here FY2018-19 is financial year and you should change it to next year on 1st of April so that you can differentiate ORDERS of each financial year separately.</li>
                                                <li>Here OPT is the branch code. If you have multiple branches you can define few letters as branch code so that you know from which branch this ORDER is generated.</li>
                                            </ul>
                                        </span>
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                    </div>    
                                </label>
                                 <input type="text" class="form-control" maxlength="25" placeholder="Enter Order Number Prefix" name="order_no_prefix" id="order_no_prefix">
                                 <span class="error badge text-danger" id="order_no_prefixError"></span>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Next Order Number: <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" maxlength="25" placeholder="Enter Order no"  name="next_order_no" id="next_order_no">
                                 <span class="error badge text-danger" id="next_order_noError"></span>
                            </div>
                        </div>
 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Invoice Number Same As Order Number:
                                    <div class="tooltip">
                                        <span class="tooltiptext">
                                            <ul>
                                                <li>Here you can decide whether ORDER number and INVOICE number is always same or not</li>
                                                <li>If you select NO, then you must be set new INVOICE prefix and next INVOICE number.</li>
                                            </ul>
                                        </span>
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                    </div>    
                                </label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Is_same_orderon" id="inlineRadio1" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio1">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Is_same_orderon" id="inlineRadio2" value="0">
                                      <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3" id="show_invoice_pr_input" style="display:none">
                            <div class="form-group">
                                <label for="" class="form-label">Invoice Number Prefix: <span class="text-danger">*</span>
                                    <div class="tooltip">
                                        <span class="tooltiptext">
                                            <ul>
                                                <li>This will be prefix for your Invoice numbers. You can use any letters or numbers of your choice.</li>
                                                <li>We recommend that you should use it like FY2018-19/OPT/.</li>
                                                <li>Here FY2018-19 is financial year and you should change it to next year on 1st of April so that you can differentiate Invoice of each financial year separately.</li>
                                                <li>Here OPT is the branch code. If you have multiple branches you can define few letters as branch code so that you know from which branch this Invoice is generated.</li>
                                            </ul>
                                        </span>
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                    </div>    
                                </label>
                                 <input type="text" class="form-control" maxlength="25" placeholder="Enter Invoice Number Prefix" name="invoice_no_prefix">
                            </div>
                        </div> 

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Order Number Auto Fill:</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Is_orderno_autofill" id="inlineRadio3" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio3">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Is_orderno_autofill" id="inlineRadio4" value="0">
                                      <label class="form-check-label" for="inlineRadio4">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Order Number Editable:</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Is_orderno_editable" id="inlineRadio5" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio5">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Is_orderno_editable" id="inlineRadio6" value="0">
                                      <label class="form-check-label" for="inlineRadio6">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Bill Number Editable:</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Is_bill_editable" id="inlineRadio7" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio7">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="Is_bill_editable" id="inlineRadio8" value="0">
                                      <label class="form-check-label" for="inlineRadio8">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                       
                        
                        
                        
                        
                         <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Sales Tax type:</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="sales_tax_type" id="inlineRadio21" value="0" >
                                      <label class="form-check-label" for="inlineRadio21">Not Applicable </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="sales_tax_type" id="inlineRadio22" value="1">
                                      <label class="form-check-label" for="inlineRadio22">Fixed Percentage / GST composite scheme </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="sales_tax_type" id="inlineRadio23" value="2" checked>
                                      <label class="form-check-label" for="inlineRadio23">Same as Purchase / Follow Tax Master</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="show_sales_tax_rulue_input">
                            <div class="form-group">
                                <label for="" class="form-label">Tax Rule:</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="tax_rule" id="inlineRadio244" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio244">Include </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="tax_rule" id="inlineRadio255" value="0" >
                                      <label class="form-check-label" for="inlineRadio255">Exclude</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3" id="show_sales_tax_input" style="display:none">
                            <div class="form-group">
                                <label for="" class="form-label">Sales Tax Percentage: <span class="text-danger">*</span></label>
                                 <input type="number" class="form-control" value="0" placeholder="Enter Sales Tax Percentage" name="sales_text_per">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="form-label">Tax Voucher Entry:</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="tax_voucher_entry" id="inlineRadio24" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio24">During Advance Receipt </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="tax_voucher_entry" id="inlineRadio25" value="0" >
                                      <label class="form-check-label" for="inlineRadio25">During Final Invoice</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="form-label">Minimum advance amount creation on store (%): <span class="text-danger">*</span>
                                    <div class="tooltip">
                                        <span class="tooltiptext">
                                            <ul>
                                                <li>Here you can set minimum advance amount for new order.</li>
                                                <li>Minimum advance amount should be in percentage.</li>
                                                <li>Minimum advance amount applied on total payable amount of your sales. Example: You set 20% as minimum advance amount; Your total payable amount is Rs.1500; then advance amount must be Rs. 300 or more.
                                                Minimum advance amount must be between 0 to 100
                                                Minimum advance amount must be in Integer. Decimal value not allowed</li>
                                            </ul>
                                        </span>
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                    </div>    
                                </label>
                                 <input type="text" class="form-control" value="100" placeholder="Enter Minimum Advance Amount in (%)" name="min_advance_amt">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Additional Notes/Terms & Conditions: </label>
                                <textarea class="form-control" name="terms_cond" id="terms_cond" ></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h3 class="card-title mb-0">B2B Settings</h3>
                            </div>
						</div>
                        <br>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">GST Number: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" maxlength="15" placeholder="Enter GST" name="gst_no" id="gst_no" oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Mobile Number: </label>
                                <input type="text" class="form-control" placeholder="Enter Mobile Number" name="bb_mobile_no"
                                id="bb_mobile_no"  maxlength="10"  pattern="^[6-9][0-9]{9}$" oninput="numOnly(this.id);">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Email ID: </label>
                                <input type="text" class="form-control" placeholder="Enter Email ID" name="bb_email">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Print B2B Customer Copy of Challan :</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="print_cust_challan" id="inlineRadio28" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio28">Yes </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="print_cust_challan" id="inlineRadio29" value="0" >
                                      <label class="form-check-label" for="inlineRadio29">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Print B2B Customer Copy of Invoice :</label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="print_cust_invoice" id="inlineRadio30" value="1" checked>
                                      <label class="form-check-label" for="inlineRadio30">Yes </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="print_cust_invoice" id="inlineRadio31" value="0" >
                                      <label class="form-check-label" for="inlineRadio31">No</label>
                                    </div>
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

@endsection

@section('scripts')


<script>
$("#storeForm").submit(function(e) 
{
    e.preventDefault(); 
    
    let isValid = true;
    let class_name = '';
    
    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let store_name = document.getElementById("store_name" + class_name).value.trim();
    let contact_no = document.getElementById("contact_no" + class_name).value.trim();
    let email_id = document.getElementById("email_id" + class_name).value.trim();
    let state_id = document.getElementById("state_id" + class_name).value.trim();
    let city_id = document.getElementById("city_id" + class_name).value.trim();
    let store_address = document.getElementById("store_address" + class_name).value.trim();
    let pincode = document.getElementById("pincode" + class_name).value.trim();
    let barcode_name = document.getElementById("barcode_name" + class_name).value.trim();
    let order_no_prefix = document.getElementById("order_no_prefix" + class_name).value.trim();
    let gst_no = document.getElementById("gst_no" + class_name).value.trim();
    let next_order_no = document.getElementById("next_order_no" + class_name).value.trim();

    // Validation rules
    if (!/^\d{10}$/.test(contact_no)) {
        document.getElementById("contactError" + class_name).textContent = "Contact must be a 10-digit number.";
        document.getElementById("contact_no" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    if (store_name.length < 3) {
        document.getElementById("store_nameError" + class_name).textContent = "Name must be at least 3 characters.";
        document.getElementById("store_name" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    if (!/^\S+@\S+\.\S+$/.test(email_id)) {
        document.getElementById("email_idError" + class_name).textContent = "Please enter a valid email.";
        document.getElementById("email_id" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    if (store_address === "") {
        document.getElementById("store_addressError" + class_name).textContent = "Address is required.";
        document.getElementById("store_address" + class_name).classList.add("is-invalid");
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
    
    

    
    if (barcode_name === "") {
        document.getElementById("barcode_nameError" + class_name).textContent = "Barcode name is required.";
        document.getElementById("barcode_name" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (order_no_prefix === "") {
        document.getElementById("order_no_prefixError" + class_name).textContent = "Order no prefix is required.";
        document.getElementById("order_no_prefix" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    

    
    if (next_order_no === "") {
        document.getElementById("next_order_noError" + class_name).textContent = "Next order no required.";
        document.getElementById("next_order_no" + class_name).classList.add("is-invalid");
        isValid = false;
    }

    // Prevent form submission if validation fails
    if (!isValid) {
        const firstError = document.querySelector(".is-invalid");
        if (firstError) {
            firstError.scrollIntoView({ behavior: "smooth", block: "center" });
            firstError.focus();
        }
        return;
    }

    // AJAX Form Submission
    let form = $("#storeForm")[0];
    let data = new FormData(form);
    


    $.ajax({
        type: 'POST',
        url: "{{ route('admin.store-add') }}",
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
            setTimeout(function() {
                window.location.href = "{{ route('admin.store-list') }}";
            }, 2000);
        } else {
            // Clear previous errors
            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

            // Display validation errors
            $.each(response.error, function(index, value) {
                if (value.includes("store_name")) {
                    $("#store_nameError").text(value);
                    $("#store_name").addClass("is-invalid");
                }
                if (value.includes("contact_no")) {
                    $("#contactError").text(value);
                    $("#contact_no").addClass("is-invalid");
                }
            });
        }
    }
    }).fail(function(jqXHR, textStatus, errorThrown) 
    {
        console.error("AJAX Error: " + textStatus + " - " + errorThrown);
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const yesRadio = document.getElementById('inlineRadio1');
    const noRadio = document.getElementById('inlineRadio2');
    const prefixDiv = document.querySelector('#show_invoice_pr_input');

    function togglePrefixDiv() {
        if (noRadio.checked) {
            prefixDiv.style.display = 'block';
        } else {
            prefixDiv.style.display = 'none';
        }
    }

    yesRadio.addEventListener('click', togglePrefixDiv);
    noRadio.addEventListener('click', togglePrefixDiv);

    // Run once on load to set correct state
    togglePrefixDiv();
});


document.addEventListener('DOMContentLoaded', function () {
    const radioButtons = document.querySelectorAll('input[name="sales_tax_type"]');
    const prefixDiv = document.querySelector('#show_sales_tax_input');

    function togglePrefixDiv() {
        const isYesSelected = document.getElementById('inlineRadio22').checked;
        prefixDiv.style.display = isYesSelected ? 'block' : 'none';
    }

    radioButtons.forEach(radio => {
        radio.addEventListener('change', togglePrefixDiv);
    });

    // Run once on load to set correct state
    togglePrefixDiv();
});

$(document).ready(function() {
    // Fetch State dynamically when the page loads
    $.ajax({
        url: "{{ route('get-state') }}",
        method: "GET",
        success: function(data) {
            var serviceDropdown = $('#state_id');
            serviceDropdown.empty(); // Clear existing options
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
    var pattern = /^[6-9][0-9]{0,9}$/; // Allows 1–10 digits, starting with 6–9

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


@endsection
