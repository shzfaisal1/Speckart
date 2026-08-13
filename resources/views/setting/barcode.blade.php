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
</style>  
@endsection
@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Barcode Setting</h3>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
               <div class="col-lg-6"  style="border-right: 1px solid #ccc;">
                   <h5>A4 Size Paper</h5>
                   <form id="AsizeForm" method="POST" method="POST" enctype="multipart/form-data">
                       @csrf
                       @php
                        $asizepaper = DB::table("tbl_barcode_setting")->where("id", "2")->first();
                         @endphp
                        <div class="row">
                            <div class="col-6">
                                <label for="">Setting Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Setting Name" value="{{$asizepaper->setting_name}}"
                                    maxlength="25" name="setting_name" id="setting_name">
                                <span class="error badge text-danger" id="setting_nameError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Paper Width <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="Enter Paper Width" value="{{$asizepaper->paper_width}}"
                                    maxlength="25" name="paper_width" id="paper_width">
                                <span class="error badge text-danger" id="paper_widthError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Paper Height<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="Enter Paper Height" value="{{$asizepaper->paper_height}}"
                                    maxlength="25" name="paper_height" id="paper_height">
                                <span class="error badge text-danger" id="paper_heightError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Label Width</label>
                                <input type="text" class="form-control" placeholder="Enter Label Width" value="{{$asizepaper->paper_width}}"
                                    maxlength="25" name="paper_width" id="label_width">
                                <span class="error badge text-danger" id="label_widthError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Label Height</label>
                                <input type="text" class="form-control" placeholder="Enter Label Height" value="{{$asizepaper->label_height}}"
                                    maxlength="25" name="label_height" id="label_height">
                                <span class="error badge text-danger" id="label_heightError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">No of Columns</label>
                                <input type="text" class="form-control" placeholder="Enter number of columns" value="{{$asizepaper->no_columns}}"
                                    maxlength="25" name="no_columns" id="no_columns">
                                <span class="error badge text-danger" id="no_columnsError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">No of rows</label>
                                <input type="text" class="form-control" placeholder="Enter number of rows" value="{{$asizepaper->no_rows}}"
                                    maxlength="25" name="no_rows" id="no_rows">
                                <span class="error badge text-danger" id="no_rowsError"></span>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" name="barcode_id" id="barcode_id"  value="{{$asizepaper->id}}">
                        <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Update
                            </button>
                        </div>
                    </form>    
                   
               </div>
               <div class="col-lg-6">
                   <h5>Advance</h5>
                   <form id="AdvanceForm" method="POST" method="POST" enctype="multipart/form-data">
                       @csrf
                       @php
                        $advancepaper = DB::table("tbl_barcode_setting")->where("id", "1")->first();
                         @endphp
                        <div class="row">
                            <div class="col-6">
                                <label for="">Setting Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Setting Name" value="{{$advancepaper->setting_name}}"
                                    maxlength="25" name="setting_name" id="setting_name">
                                <span class="error badge text-danger" id="setting_nameError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Paper Width <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="Enter Paper Width" value="{{$advancepaper->paper_width}}"
                                    maxlength="25" name="paper_width" id="paper_width">
                                <span class="error badge text-danger" id="paper_widthError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Paper Height<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="Enter Paper Height" value="{{$advancepaper->paper_height}}"
                                    maxlength="25" name="paper_height" id="paper_height">
                                <span class="error badge text-danger" id="paper_heightError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Label Width</label>
                                <input type="text" class="form-control" placeholder="Enter Label Width" value="{{$advancepaper->paper_width}}"
                                    maxlength="25" name="paper_width" id="label_width">
                                <span class="error badge text-danger" id="label_widthError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">Label Height</label>
                                <input type="text" class="form-control" placeholder="Enter Label Height" value="{{$advancepaper->label_height}}"
                                    maxlength="25" name="label_height" id="label_height">
                                <span class="error badge text-danger" id="label_heightError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">No of Columns</label>
                                <input type="text" class="form-control" placeholder="Enter number of columns" value="{{$advancepaper->no_columns}}"
                                    maxlength="25" name="no_columns" id="no_columns">
                                <span class="error badge text-danger" id="no_columnsError"></span>
                            </div>
                            <div class="col-6">
                                <label for="">No of rows</label>
                                <input type="text" class="form-control" placeholder="Enter number of rows" value="{{$advancepaper->no_rows}}"
                                    maxlength="25" name="no_rows" id="no_rows">
                                <span class="error badge text-danger" id="no_rowsError"></span>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" name="barcode_id" id="barcode_id"  value="{{$advancepaper->id}}">
                        <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Update
                            </button>
                        </div>
                    </form> 
               </div>
        
            </div>
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
            let setting_name = document.getElementById("setting_name" + class_name).value.trim();
            let paper_width = document.getElementById("paper_width" + class_name).value.trim();
            let paper_height = document.getElementById("paper_height" + class_name).value.trim();
    
            // Validate
            if (setting_name.length < 3) {
                document.getElementById("setting_nameError" + class_name).textContent = "Setting name must be at least 3 characters.";
                document.getElementById("setting_name" + class_name).classList.add("is-invalid");
                isValid = false;
            }
    
            if (paper_width === "") {
                document.getElementById("paper_widthError" + class_name).textContent = "Paper Width required";
                document.getElementById("paper_width" + class_name).classList.add("is-invalid");
                isValid = false;
            }
    
            if (paper_height === "") {
                document.getElementById("paper_heightError" + class_name).textContent = "Paper Height required";
                document.getElementById("paper_height" + class_name).classList.add("is-invalid");
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
    
                        $.each(response.error, function (index, value) {
                            if (value.includes("setting_name")) {
                                $("#setting_nameError").text(value);
                                $("#setting_name").addClass("is-invalid");
                            }
                            if (value.includes("paper_width")) {
                                $("#paper_widthError").text(value);
                                $("#paper_width").addClass("is-invalid");
                            }
                            if (value.includes("paper_height")) {
                                $("#paper_heightError").text(value);
                                $("#paper_height").addClass("is-invalid");
                            }
                        });
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error: " + textStatus + " - " + errorThrown);
            });
        });
    }
    
    // Initialize handlers
    handleFormSubmit("#AsizeForm", "{{ route('admin.barcode-update') }}");
    handleFormSubmit("#AdvanceForm", "{{ route('admin.barcode-update') }}");

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
