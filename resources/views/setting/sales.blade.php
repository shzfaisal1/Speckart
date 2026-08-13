@extends('layouts.master')
@section('styles')
<style>
    .status-row{
        margin-bottom:15px;
    }

    .status-title{
        font-weight:bold;
        margin-right:10px;
    }

    input[type="text"]{
        width:300px;
        padding:4px;
    }

    .product-options{
        margin-left:160px;
        margin-top:5px;
    }

    .product-options label{
        margin-right:12px;
        font-size:14px;
    }
</style> 
@endsection
@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Sales Settings</h3>
                    </div>
                </div>
            </div>
            <hr/>
            <form id="salesForm" method="POST" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-6"  style="border-right: 1px solid #ccc;">
                   <h5>Inter Branch Sales Settings</h5>
                   
                      
                       @php
                        $sales = DB::table("tbl_sales_setting")->first();
                         @endphp
                        <div class="row">
                            <div class="col-6">
                                <label for="">Margin Percentage for Frame <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" value="{{$sales->frame_margin}}"
                                    name="frame_margin" id="frame_margin">
                                <span class="error badge text-danger" id="frame_marginError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Margin Percentage for Goggles <span class="text-danger">*</span></label>
                                <input type="number" class="form-control"  value="{{$sales->goggles_margin}}"
                                    name="goggles_margin" id="goggles_margin">
                                <span class="error badge text-danger" id="goggles_marginError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Margin Percentage for Glass <span class="text-danger">*</span></label>
                                <input type="number" class="form-control"  value="{{$sales->glass_margin}}"
                                    name="glass_margin" id="glass_margin">
                                <span class="error badge text-danger" id="glass_marginError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Margin Percentage for Contact Lens <span class="text-danger">*</span></label>
                                <input type="number" class="form-control"  value="{{$sales->lens_margin}}"
                                    name="lens_margin" id="lens_margin">
                                <span class="error badge text-danger" id="lens_marginError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Margin Percentage for Solution <span class="text-danger">*</span></label>
                                <input type="number" class="form-control"  value="{{$sales->solution_margin}}"
                                    name="solution_margin" id="solution_margin">
                                <span class="error badge text-danger" id="solution_marginError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Margin Percentage for Other <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" value="{{$sales->other_margin}}"
                                    name="other_margin" id="other_margin">
                                <span class="error badge text-danger" id="other_marginError"></span>
                            </div>
                            
                        </div>
               </div>
                    <div class="col-lg-6">
                   <h5>B2B Sales Settings</h5>
               
                    <div class="row">
                        <div class="col-6">
                            <label for="">Margin Percentage for Frame <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" value="{{$sales->bb_frame_margin}}"
                                name="bb_frame_margin" id="bb_frame_margin">
                            <span class="error badge text-danger" id="bb_frame_marginError"></span>
                        </div>
                        <div class="col-6">
                            <label for="">Margin Percentage for Goggles <span class="text-danger">*</span></label>
                            <input type="number" class="form-control"  value="{{$sales->bb_goggles_margin}}"
                                name="bb_goggles_margin" id="bb_goggles_margin">
                            <span class="error badge text-danger" id="bb_goggles_marginError"></span>
                        </div>
                        <div class="col-6">
                            <label for="">Margin Percentage for Glass <span class="text-danger">*</span></label>
                            <input type="number" class="form-control"  value="{{$sales->bb_glass_margin}}"
                                name="bb_glass_margin" id="bb_glass_margin">
                            <span class="error badge text-danger" id="bb_glass_marginError"></span>
                        </div>
                        <div class="col-6">
                            <label for="">Margin Percentage for Contact Lens <span class="text-danger">*</span></label>
                            <input type="number" class="form-control"  value="{{$sales->bb_lens_margin}}"
                                name="bb_lens_margin" id="bb_lens_margin">
                            <span class="error badge text-danger" id="bb_lens_marginError"></span>
                        </div>
                        <div class="col-6">
                            <label for="">Margin Percentage for Solution <span class="text-danger">*</span></label>
                            <input type="number" class="form-control"  value="{{$sales->bb_solution_margin}}"
                                name="bb_solution_margin" id="bb_solution_margin">
                            <span class="error badge text-danger" id="bb_solution_marginError"></span>
                        </div>
                        <div class="col-6">
                            <label for="">Margin Percentage for Other <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" value="{{$sales->bb_other_margin}}"
                                name="bb_other_margin" id="bb_other_margin">
                            <span class="error badge text-danger" id="bb_other_marginError"></span>
                        </div>
                        
                    </div>
                       
               </div>
                </div>
                <input type="hidden" class="form-control" name="sales_id" id="sales_id"  value="{{$sales->id}}">
                 <div class="button-row d-flex mt-4">
                    <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Update
                    </button>
                </div>
            </form>
            <hr/>
            <form id="salesStatusForm" method="POST" method="POST" enctype="multipart/form-data">
                 @csrf 
                <div class="row">
                <div class="status-row">
                    <label class="status-title">Status 1 :</label>
                    <input type="checkbox" checked>
                    <input type="text" value="ORDER PLACED AND READY TO SHIP">
                
                    <div class="product-options">
                        Default Status 1 For Products :
                        <label><input type="checkbox" name="status1_product"> Frame</label>
                        <label><input type="checkbox" name="status1_product"> Goggles</label>
                        <label><input type="checkbox" name="status1_product"> Glass</label>
                        <label><input type="checkbox" name="status1_product"> Contact Lens</label>
                        <label><input type="checkbox" name="status1_product"> Solution</label>
                        <label><input type="checkbox" name="status1_product"> Other</label>
                        <label><input type="checkbox" name="status1_product"> Repair</label>
                        <label><input type="checkbox" name="status1_product"> Non Chargeable</label>
                    </div>
                </div>
                <!-- STATUS 2 -->
                <div class="status-row">
                    <label class="status-title">Status 2 :</label>
                    <input type="checkbox" checked>
                    <input type="text" value="ORDER SEND TO WAREHOUSE BY WH">
                
                    <div class="product-options">
                        Default Status 2 For Products :
                        <label><input type="checkbox" name="status2_product"> Frame</label>
                        <label><input type="checkbox" name="status2_product"> Goggles</label>
                        <label><input type="checkbox" name="status2_product"> Glass</label>
                        <label><input type="checkbox" name="status2_product"> Contact Lens</label>
                        <label><input type="checkbox" name="status2_product"> Solution</label>
                        <label><input type="checkbox" name="status2_product"> Other</label>
                        <label><input type="checkbox" name="status2_product"> Repair</label>
                        <label><input type="checkbox" name="status2_product"> Non Chargeable</label>
                    </div>
                </div>
                <!-- STATUS 3 -->
                <div class="status-row">
                    <label class="status-title">Status 3 :</label>
                    <input type="checkbox" checked>
                    <input type="text" value="ORDER RECEIVED FROM STORE">
                
                    <div class="product-options">
                        Default Status 3 For Products :
                        <label><input type="checkbox" name="status3_product"> Frame</label>
                        <label><input type="checkbox" name="status3_product"> Goggles</label>
                        <label><input type="checkbox" name="status3_product"> Glass</label>
                        <label><input type="checkbox" name="status3_product"> Contact Lens</label>
                        <label><input type="checkbox" name="status3_product"> Solution</label>
                        <label><input type="checkbox" name="status3_product"> Other</label>
                        <label><input type="checkbox" name="status3_product"> Repair</label>
                        <label><input type="checkbox" name="status3_product"> Non Chargeable</label>
                    </div>
                </div>
                <!-- STATUS 4 -->
                <div class="status-row">
                    <span class="status-title">Status 4 :</span>
                    <input type="checkbox" checked>
                    <input type="text" value="ORDER PLACED TO VENDOR">
                
                    <div class="product-options">
                        Default Status 4 For Products :
                        <label><input type="checkbox" name="status4"> Frame</label>
                        <label><input type="checkbox" name="status4"> Goggles</label>
                        <label><input type="checkbox" name="status4"> Glass</label>
                        <label><input type="checkbox" name="status4"> Contact Lens</label>
                        <label><input type="checkbox" name="status4"> Solution</label>
                        <label><input type="checkbox" name="status4"> Other</label>
                        <label><input type="checkbox" name="status4"> Repair</label>
                        <label><input type="checkbox" name="status4"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 5 -->
                <div class="status-row">
                    <span class="status-title">Status 5 :</span>
                    <input type="checkbox" checked>
                    <input type="text" value="ORDER RECEIVED FROM VENDOR">
                
                    <div class="product-options">
                        Default Status 5 For Products :
                        <label><input type="checkbox" name="status5"> Frame</label>
                        <label><input type="checkbox" name="status5"> Goggles</label>
                        <label><input type="checkbox" name="status5"> Glass</label>
                        <label><input type="checkbox" name="status5"> Contact Lens</label>
                        <label><input type="checkbox" name="status5"> Solution</label>
                        <label><input type="checkbox" name="status5"> Other</label>
                        <label><input type="checkbox" name="status5"> Repair</label>
                        <label><input type="checkbox" name="status5"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 6 -->
                <div class="status-row">
                    <span class="status-title">Status 6 :</span>
                    <input type="checkbox" checked>
                    <input type="text" value="WARRANTY">
                
                    <div class="product-options">
                        Default Status 6 For Products :
                        <label><input type="checkbox" name="status6"> Frame</label>
                        <label><input type="checkbox" name="status6"> Goggles</label>
                        <label><input type="checkbox" name="status6"> Glass</label>
                        <label><input type="checkbox" name="status6"> Contact Lens</label>
                        <label><input type="checkbox" name="status6"> Solution</label>
                        <label><input type="checkbox" name="status6"> Other</label>
                        <label><input type="checkbox" name="status6"> Repair</label>
                        <label><input type="checkbox" name="status6"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 7 -->
                <div class="status-row">
                    <span class="status-title">Status 7 :</span>
                    <input type="checkbox" checked>
                    <input type="text" value="QUALITY OK SENT FOR FITTING">
                
                    <div class="product-options">
                        Default Status 7 For Products :
                        <label><input type="checkbox" name="status7"> Frame</label>
                        <label><input type="checkbox" name="status7"> Goggles</label>
                        <label><input type="checkbox" name="status7"> Glass</label>
                        <label><input type="checkbox" name="status7"> Contact Lens</label>
                        <label><input type="checkbox" name="status7"> Solution</label>
                        <label><input type="checkbox" name="status7"> Other</label>
                        <label><input type="checkbox" name="status7"> Repair</label>
                        <label><input type="checkbox" name="status7"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 8 -->
                <div class="status-row">
                    <span class="status-title">Status 8 :</span>
                    <input type="checkbox" checked>
                    <input type="text" value="QUALITY REJECTED SENT BACK TO VENDOR">
                
                    <div class="product-options">
                        Default Status 8 For Products :
                        <label><input type="checkbox" name="status8"> Frame</label>
                        <label><input type="checkbox" name="status8"> Goggles</label>
                        <label><input type="checkbox" name="status8"> Glass</label>
                        <label><input type="checkbox" name="status8"> Contact Lens</label>
                        <label><input type="checkbox" name="status8"> Solution</label>
                        <label><input type="checkbox" name="status8"> Other</label>
                        <label><input type="checkbox" name="status8"> Repair</label>
                        <label><input type="checkbox" name="status8"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 9 -->
                <div class="status-row">
                    <span class="status-title">Status 9 :</span>
                    <input type="checkbox" checked>
                    <input type="text" value="RECEIVED FROM FITTING AND CLEAN">
                
                    <div class="product-options">
                        Default Status 9 For Products :
                        <label><input type="checkbox" name="status9"> Frame</label>
                        <label><input type="checkbox" name="status9"> Goggles</label>
                        <label><input type="checkbox" name="status9"> Glass</label>
                        <label><input type="checkbox" name="status9"> Contact Lens</label>
                        <label><input type="checkbox" name="status9"> Solution</label>
                        <label><input type="checkbox" name="status9"> Other</label>
                        <label><input type="checkbox" name="status9"> Repair</label>
                        <label><input type="checkbox" name="status9"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 10 -->
                <div class="status-row">
                    <span class="status-title">Status 10 :</span>
                    <input type="checkbox" checked>
                    <input type="text" value="SEND TO BRANCH">
                
                    <div class="product-options">
                        Default Status 10 For Products :
                        <label><input type="checkbox" name="status10"> Frame</label>
                        <label><input type="checkbox" name="status10"> Goggles</label>
                        <label><input type="checkbox" name="status10"> Glass</label>
                        <label><input type="checkbox" name="status10"> Contact Lens</label>
                        <label><input type="checkbox" name="status10"> Solution</label>
                        <label><input type="checkbox" name="status10"> Other</label>
                        <label><input type="checkbox" name="status10"> Repair</label>
                        <label><input type="checkbox" name="status10"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 11 -->
                <div class="status-row">
                    <span class="status-title">Status 11 :</span>
                    <input type="checkbox" checked>
                    <input type="text" value="RECEVIED BY BRANCH">
                
                    <div class="product-options">
                        Default Status 11 For Products :
                        <label><input type="checkbox" name="status11"> Frame</label>
                        <label><input type="checkbox" name="status11"> Goggles</label>
                        <label><input type="checkbox" name="status11"> Glass</label>
                        <label><input type="checkbox" name="status11"> Contact Lens</label>
                        <label><input type="checkbox" name="status11"> Solution</label>
                        <label><input type="checkbox" name="status11"> Other</label>
                        <label><input type="checkbox" name="status11"> Repair</label>
                        <label><input type="checkbox" name="status11"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 12 -->
                <div class="status-row">
                    <span class="status-title">Status 12 :</span>
                    <input type="checkbox" checked disabled>
                    <input type="text" value="PRODUCT READY">
                
                    <div class="product-options">
                        Default Status 12 For Products :
                        <label><input type="checkbox" name="status12"> Frame</label>
                        <label><input type="checkbox" name="status12"> Goggles</label>
                        <label><input type="checkbox" name="status12"> Glass</label>
                        <label><input type="checkbox" name="status12"> Contact Lens</label>
                        <label><input type="checkbox" name="status12"> Solution</label>
                        <label><input type="checkbox" name="status12"> Other</label>
                        <label><input type="checkbox" name="status12"> Repair</label>
                        <label><input type="checkbox" name="status12"> Non Chargeable</label>
                    </div>
                </div>
                
                <!-- STATUS 13 -->
                <div class="status-row">
                    <span class="status-title">Status 13 :</span>
                    <input type="checkbox" checked  disabled>
                    <input type="text" value="DELIVERED">
                
                    <div class="product-options">
                        Default Status 13 For Products :
                        <label><input type="checkbox" name="status13"> Frame</label>
                        <label><input type="checkbox" name="status13"> Goggles</label>
                        <label><input type="checkbox" name="status13"> Glass</label>
                        <label><input type="checkbox" name="status13"> Contact Lens</label>
                        <label><input type="checkbox" name="status13"> Solution</label>
                        <label><input type="checkbox" name="status13"> Other</label>
                        <label><input type="checkbox" name="status13"> Repair</label>
                        <label><input type="checkbox" name="status13"> Non Chargeable</label>
                    </div>
                </div>
                
                
                
            </div>
                <div class="button-row d-flex mt-4">
                    <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    function handleFormSubmit(formId, routeUrl) {
        $(formId).submit(function (e) {
            e.preventDefault();
    
            let isValid = true;
            let class_name = '';
    
            // Clear previous errors
            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
            // Get values
            let frame_margin = document.getElementById("frame_margin" + class_name).value.trim();
            let goggles_margin = document.getElementById("goggles_margin" + class_name).value.trim();
            let glass_margin = document.getElementById("glass_margin" + class_name).value.trim();
            let lens_margin = document.getElementById("lens_margin" + class_name).value.trim();
            let solution_margin = document.getElementById("solution_margin" + class_name).value.trim();
            let other_margin = document.getElementById("other_margin" + class_name).value.trim();
            let bb_frame_margin = document.getElementById("bb_frame_margin" + class_name).value.trim();
            let bb_goggles_margin = document.getElementById("bb_goggles_margin" + class_name).value.trim();
            let bb_glass_margin = document.getElementById("bb_glass_margin" + class_name).value.trim();
            let bb_lens_margin = document.getElementById("bb_lens_margin" + class_name).value.trim();
            let bb_solution_margin = document.getElementById("bb_solution_margin" + class_name).value.trim();
            let bb_other_margin = document.getElementById("bb_other_margin" + class_name).value.trim();
    

    
            if (frame_margin === "") {
                document.getElementById("frame_marginError" + class_name).textContent = "Frame margin required";
                document.getElementById("frame_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
    
            if (goggles_margin === "") {
                document.getElementById("goggles_marginError" + class_name).textContent = "goggles margin required";
                document.getElementById("goggles_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (glass_margin === "") {
                document.getElementById("glass_marginError" + class_name).textContent = "glass margin required";
                document.getElementById("glass_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (lens_margin === "") {
                document.getElementById("lens_marginError" + class_name).textContent = "lens margin required";
                document.getElementById("lens_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (solution_margin === "") {
                document.getElementById("solution_marginError" + class_name).textContent = "Solution margin required";
                document.getElementById("solution_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (other_margin === "") {
                document.getElementById("other_marginError" + class_name).textContent = "Other margin required";
                document.getElementById("other_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (bb_frame_margin === "") {
                document.getElementById("bb_frame_marginError" + class_name).textContent = "Frame margin required";
                document.getElementById("bb_frame_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
    
            if (bb_goggles_margin === "") {
                document.getElementById("bb_goggles_marginError" + class_name).textContent = "goggles margin required";
                document.getElementById("bb_goggles_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (bb_glass_margin === "") {
                document.getElementById("bb_glass_marginError" + class_name).textContent = "glass margin required";
                document.getElementById("bb_glass_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (bb_lens_margin === "") {
                document.getElementById("bb_lens_marginError" + class_name).textContent = "lens margin required";
                document.getElementById("bb_lens_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (bb_solution_margin === "") {
                document.getElementById("bb_solution_marginError" + class_name).textContent = "Solution margin required";
                document.getElementById("bb_solution_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
            
            if (bb_other_margin === "") {
                document.getElementById("bb_other_marginError" + class_name).textContent = "Other margin required";
                document.getElementById("bb_other_margin" + class_name).classList.add("is-invalid");
                isValid = false;
            }
    
            if (!isValid) return;
    
            // Submit via AJAX
            let form = $(formId)[0];
            let data = new FormData(form);
    
            $.ajax({
                type: 'POST',
                url: routeUrl,
                data: data,
                dataType: "JSON",
                processData: false,
                contentType: false,
                success: function (response) {
                    if ($.isEmptyObject(response.error)) {
                        $.toaster({
                            priority: 'success',
                            title: response.success,
                            message: ''
                        });
                        location.reload();
                    } else {
                        document.querySelectorAll(".error").forEach(el => el.textContent = "");
                        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error: " + textStatus + " - " + errorThrown);
            });
        });
    }
    
    // Initialize handlers
    handleFormSubmit("#salesForm", "{{ route('admin.salessetting-update') }}");

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
