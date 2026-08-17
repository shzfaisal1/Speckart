<script>
$(document).ready(function () 
{
    $(function () 
    {

        var today = moment();
        var futureDate = moment().add(7, 'days'); 
    
        function cb(date) {
            $('#reportrangesale1 span').html(
                date.format('MMMM D, YYYY h:mm A')
            );
    
            $('#delivery_date').val(
                date.format('YYYY-MM-DD HH:mm:ss')
            );
        }
    
        $('#reportrangesale1').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            startDate: futureDate, 
            minDate: today,
            timePicker: true,
            timePicker24Hour: false,
            timePickerIncrement: 1,
            autoUpdateInput: false,
            locale: {
                format: 'MMMM D, YYYY h:mm A'
            }
        }, cb);
    
        cb(futureDate);
    
    });

    /** ==============================
     *  Sale Date
     *  ==============================*/
     
    /* =======================
       First Date + Time Picker
    ======================= */
    function cb(date) {
        $('#reportrange span').html(date.format('MMMM D, YYYY h:mm A'));
        $('#date_from').val(date.format('YYYY-MM-DD HH:mm:ss'));
    }
    
    $('#reportrange').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: false,     // set true for 24-hour format
        timePickerIncrement: 1,
        autoUpdateInput: false,
        locale: {
            format: 'MMMM D, YYYY h:mm A'
        }
    }, cb);
    
    
    /* =======================
       Second Date + Time Picker
    ======================= */
    function cb1(date) {
        $('#reportrange1 span').html(date.format('MMMM D, YYYY h:mm A'));
        $('#date_from1').val(date.format('YYYY-MM-DD HH:mm:ss'));
    }
    
    $('#reportrange1').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: false,
        timePickerIncrement: 1,
        autoUpdateInput: false,
        locale: {
            format: 'MMMM D, YYYY h:mm A'
        }
    }, cb1);
    
    
    /* =======================
       Default Sale Date + Time
    ======================= */
    let selectedDate = moment();
    
    $('#reportrangesale span').html(
        selectedDate.format('MMMM D, YYYY h:mm A')
    );
    
    $('#sale_date').val(
        selectedDate.format('YYYY-MM-DD HH:mm:ss')
    );
    
    /* -------------------------
    Select2 & Mobile Validation
    ------------------------- */
    $('.select1').select2({ allowClear:true, width:'22%' });
    $('.select').select2({ allowClear:true, width:'100%' });

    var mobileFields = ['contact_no', 'bb_mobile_no'];
    var pattern = /^[6-9][0-9]{0,9}$/;
    mobileFields.forEach(function (id) {
        var input = document.getElementById(id);
        if (!input) return;
        var lastValid = '';
        input.addEventListener('input', function () {
            if (pattern.test(this.value)) {
                lastValid = this.value;
            } else {
                this.value = lastValid;
            }
        });
    });
    
    /* -------------------------
    Get Customer Details
    ------------------------- */
    let debounceTimer;
    $('#contact_no').on('change', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            let contactNo = $('#contact_no').val().trim();
    
            if (!/^[6-9]\d{9}$/.test(contactNo)) {
                $('#contactError').text("Enter valid 10-digit mobile number.");
                return;
            } else {
                $('#contactError').text("");
            }
    
            $.ajax({
                url: "{{ route('admin.getcustomer') }}",
                method: "GET",
                data: { contact_no: contactNo },
                beforeSend: function () {
                    $("#ajaxLoader").show();
                },
                success: function (response) {
                    if (response.success) {
                        let data = response.data;
    
                        $('#cust_name').val(data.cust_name);
                        $('#email_id').val(data.email_id);
                        $('#cust_category').val(data.cust_category).trigger('change');
                        $('#cust_address').val(data.cust_address);
                        $('#pincode').val(data.pincode);
                        $('#cust_note').val(data.cust_note);
    
                        $("input[name='gender'][value='" + data.gender + "']").prop("checked", true);
    
                        if (data.dob) {
                            let dob = moment(data.dob, 'YYYY-MM-DD');
                            $('#date_from').val(dob.format('YYYY-MM-DD'));
                            $('#reportrange span').html(dob.format('MMMM D, YYYY'));
                            $('#reportrange').data('daterangepicker').setStartDate(dob).setEndDate(dob);
                        }
    
                        if (data.doa) {
                            let ann = moment(data.doa, 'YYYY-MM-DD');
                            $('#date_from1').val(ann.format('YYYY-MM-DD'));
                            $('#reportrange1 span').html(ann.format('MMMM D, YYYY'));
                            $('#reportrange1').data('daterangepicker').setStartDate(ann).setEndDate(ann);
                        }
    
                        if (data.state_id) {
                            loadStates(data.state_id, data.city_id);
                        }
    
                    } else {
                        $.toaster({ priority: 'danger', title: 'Error', message: response.message,
                       timeout: 3000 });
                    }
                },
                error: function () {
                    $.toaster({ priority: 'danger', title: 'Error', message: 'Error fetching customer data.',
                    timeout: 3000 });
                },
                complete: function () {
                    $("#ajaxLoader").fadeOut();
                }
            });
        }, 500);
    });
    
    /* -------------------------
    Load State
    ------------------------- */
    function loadStates(selectedState, selectedCity)
    {
        $.ajax({
            url: "{{ route('get-state') }}",
            method: "GET",
            success: function (data) {
                let stateSelect = $('#state_id');
                stateSelect.empty().append('<option value="" disabled selected>Select State</option>');
                $.each(data, (key, value) => {
                    stateSelect.append(`<option value="${value.id}">${value.name}</option>`);
                });

                if (selectedState) {
                    $('#state_id').val(selectedState).trigger('change');
                    if (selectedCity) {
                        $('#city_id').data('selected', selectedCity);
                    }
                }
            }
        });
    }
    
    $('#state_id').on('change', function () {
        const stateId = $(this).val();
        $('#city_id').empty().append('<option value="" disabled selected>Loading...</option>');
    
        if (stateId) {
            $.ajax({
                url: "{{ route('get-city-by-state') }}",
                method: "GET",
                data: { state_id: stateId },
                beforeSend: function () {
                    $("#ajaxLoader").show();
                },
                success: function (data) {
                    let citySelect = $('#city_id');
                    citySelect.empty().append('<option value="" disabled selected>Select City</option>');
                    $.each(data, (key, value) => {
                        citySelect.append(`<option value="${value.id}">${value.name}</option>`);
                    });

                    let selectedCity = $('#city_id').data('selected');
                    if (selectedCity) {
                        $('#city_id').val(selectedCity).trigger('change');
                        $('#city_id').removeData('selected');
                    }
                },
                error: function () {
                    $('#city_id').html('<option value="" disabled selected>No city found</option>');
                },
                complete: function () {
                    $("#ajaxLoader").fadeOut();
                }
            });
        } else {
            $('#city_id').html('<option value="" disabled selected>Select City</option>');
        }
    });

    loadStates();
    
    /* -------------------------
      Barcode Search  Modal
    ------------------------- */
    $('#pcode').on('change', function () {
        let code = $(this).val().trim();
        if (code.length >= 2) {
            $.ajax({
                url: "{{ route('admin.get.barcode.table') }}",  
                method: "GET",
                data: { pcode: code },
                beforeSend: function () {
                    $("#ajaxLoader").show();
                },
                success: function (response) {
                    let $tbody = $('#barcode-table tbody');
                    $tbody.empty();

                    if (response.length > 0) {
                        response.forEach((item, index) => {
                            $tbody.append(`
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input barcode-radio" 
                                                type="radio" 
                                                name="barcodeSelect" 
                                                data-barcode='${JSON.stringify(item)}' 
                                                id="barcode_${index}">
                                        </div>
                                    </td>
                                    <td>${item.barcode_no}</td>
                                    <td>${item.product_type}</td>
                                    <td>${item.product_code}</td>
                                    <td>${item.product_details}</td>
                                    <td>тВ╣${parseFloat(item.retail_price).toFixed(2)}</td>
                                </tr>
                            `);
                        });
                    } else {
                        $tbody.append(`<tr><td colspan="6">No matching products found.</td></tr>`);
                    }
                    $('#barcodeModal').modal('show');
                },
                error: function () {
                    $('#barcode-table tbody').html('<tr><td colspan="6">Error fetching data</td></tr>');
                    $('#barcodeModal').modal('show');
                },
                complete: function () {
                    $("#ajaxLoader").fadeOut();
                }
            });
        }
    });
    
    /** ==============================
     *  Table row handling
     *  ============================== */
    let activeRow = null;

    const addRowBtn = document.getElementById("addRowBtn");
    const tableBody = document.querySelector("#saleTable tbody");
    
    // ----------------- SERIAL NUMBERS -----------------
    function updateSerialNumbers() {
        tableBody.querySelectorAll("tr").forEach((row, index) => {
            row.cells[0].textContent = index + 1;
    
            const removeBtn = row.querySelector(".removeBtn");
            if (removeBtn) {
                removeBtn.style.display = (index === 0) ? "none" : "inline-block";
            }
        });
    }
    
    // ----------------- VALIDATE LAST ROW -----------------
    function validateLastRow() {
        const lastRow = tableBody.querySelector("tr:last-child");
        let isValid = true;
    
        if (!lastRow) return true;
    
        const productType = lastRow.querySelector("select.product-type");
        const productDescription = lastRow.querySelector("input.product-description");
        const salePrice = lastRow.querySelector("input.sale-price");
    
        if (!productType.value.trim()) {
            productType.classList.add("error");
            isValid = false;
        } else productType.classList.remove("error");
    
        if (!productDescription.value.trim()) {
            productDescription.classList.add("error");
            isValid = false;
        } else productDescription.classList.remove("error");
    
        if (!salePrice.value.trim() || parseFloat(salePrice.value) <= 0) {
            salePrice.classList.add("error");
            isValid = false;
        } else salePrice.classList.remove("error");
    
        return isValid;
    }
    
    // ----------------- ADD ROW -----------------
    addRowBtn.addEventListener("click", function () {
    
        if (!validateLastRow()) {
            $.toaster({
                priority: 'danger',
                title: ' Please fill all required fields in the last row before adding a new one.',
                message: ''
            });
            return;
        }
    
        const newRow = document.createElement("tr");
    
        newRow.innerHTML = `
            <td></td>
            <td><input type="text" class="form-control barcode" name="barcode[]" placeholder="Enter barcode"></td>
    
            <td>
                <select class="form-control product-type" style="height: 32px !important;" name="product_type[]">
                    <option value="">Select Product</option>
                    <option value="Frame">Frame</option>
                    <option value="Glass">Glass</option>
                    <option value="Goggles">Goggles</option>
                    <option value="Lens">Contact Lens</option>
                    <option value="Solution">Solution</option>
                    <option value="Repair">Repair</option>
                    <option value="Other">Other</option>
                </select>
                <input type="hidden" class="form-control product-code" name="product_code[]">
                <input type="hidden" class="form-control product-id" name="product_id[]">
                <input type="hidden" class="form-control product-company" name="product_company[]">
                <input type="hidden" class="form-control product-quality" name="product_quality[]">
                <input type="hidden" class="form-control product-materiale" name="product_material[]">
                <input type="hidden" class="form-control product-color" name="product_color[]">
                <input type="hidden" class="form-control product-design" name="product_design[]">
                <input type="hidden" class="form-control product-coating" name="product_coating[]">
                <input type="hidden" class="form-control product-index" name="product_index[]">
                <input type="hidden" class="form-control product-number" name="product_number[]">
                <input type="hidden" class="form-control product-ct" name="product_ct[]">
                <input type="hidden" class="form-control product-typesss" name="product_typesss[]">
                <input type="hidden" class="form-control product-validity" name="product_validity[]">
                <input type="hidden" class="form-control product-shape" name="product_shape[]">
                <input type="hidden" class="form-control product-size" name="product_size[]">
                <input type="hidden" class="form-control product-variant" name="product_variant[]">
                <input type="hidden" class="form-control package-id" name="package_id[]">
                <input type="hidden" class="form-control coating-apply" name="coating_apply[]">
            </td>
            <td>
                <input type="text" class="form-control product-description" name="product_description[]" placeholder="Enter Product Description" readonly>
                <input type="hidden" class="form-control GL_EYE_RS_D" name="GL_EYE_RS_D[]">
                <input type="hidden" class="form-control GL_EYE_RC_D" name="GL_EYE_RC_D[]">
                <input type="hidden" class="form-control GL_EYE_RA_D" name="GL_EYE_RA_D[]">
                <input type="hidden" class="form-control GL_EYE_RP_D" name="GL_EYE_RP_D[]">
                <input type="hidden" class="form-control GL_EYE_RV_D" name="GL_EYE_RV_D[]">
                <input type="hidden" class="form-control GL_EYE_RS_N" name="GL_EYE_RS_N[]">
                <input type="hidden" class="form-control GL_EYE_RC_N" name="GL_EYE_RC_N[]">
                <input type="hidden" class="form-control GL_EYE_RA_N" name="GL_EYE_RA_N[]">
                <input type="hidden" class="form-control GL_EYE_RP_N" name="GL_EYE_RP_N[]">
                <input type="hidden" class="form-control GL_EYE_RV_N" name="GL_EYE_RV_N[]">
                <input type="hidden" class="form-control GL_EYE_RADD" name="GL_EYE_RADD[]">
                <input type="hidden" class="form-control GL_EYE_totalPD" name="GL_EYE_totalPD[]">
                <input type="hidden" class="form-control GL_EYE_LS_D" name="GL_EYE_LS_D[]">
                <input type="hidden" class="form-control GL_EYE_LC_D" name="GL_EYE_LC_D[]">
                <input type="hidden" class="form-control GL_EYE_LA_D" name="GL_EYE_LA_D[]">
                <input type="hidden" class="form-control GL_EYE_LP_D" name="GL_EYE_LP_D[]">
                <input type="hidden" class="form-control GL_EYE_LV_D" name="GL_EYE_LV_D[]">
                <input type="hidden" class="form-control GL_EYE_LS_N" name="GL_EYE_LS_N[]">
                <input type="hidden" class="form-control GL_EYE_LC_N" name="GL_EYE_LC_N[]">
                <input type="hidden" class="form-control GL_EYE_LA_N" name="GL_EYE_LA_N[]">
                <input type="hidden" class="form-control GL_EYE_LP_N" name="GL_EYE_LP_N[]">
                <input type="hidden" class="form-control GL_EYE_LV_N" name="GL_EYE_LV_N[]">
                <input type="hidden" class="form-control GL_EYE_LADD" name="GL_EYE_LADD[]">
                
                <input type="hidden" class="form-control frame-dbl" name="frame_dbl[]">
                <input type="hidden" class="form-control frame-fh" name="frame_fh[]">
                <input type="hidden" class="form-control frame-ed" name="frame_ed[]">
                <input type="hidden" class="form-control frame-asize" name="frame_asize[]">
                <input type="hidden" class="form-control frame-bsize" name="frame_bsize[]">
                <input type="hidden" class="form-control frametypeglass" name="frametypeglass[]">

                
                <input type="hidden" class="form-control right-left" name="right_left[]">
                <input type="hidden" class="form-control doctor-name" name="doc_name[]">
                <input type="hidden" class="patient-name" name="patient_name[]">
                <input type="hidden" class="form-control wearing-type" name="wearing_type[]">
                <input type="hidden" class="form-control wearing-inhouse" name="wearing_types_inhouse[]">
                <input type="hidden" class="prescription-notes" name="prescription_notes[]">
                <input type="hidden" class="count-eye-test" name="count_eye_test[]">
                
                <input type="hidden" class="lensRightNoOfBoxes" name="lensRightNoOfBoxes[]">
                <input type="hidden" class="lensRightTotalPieces" name="lensRightTotalPieces[]">
                <input type="hidden" class="lensLeftNoOfBoxes" name="lensLeftNoOfBoxes[]">
                <input type="hidden" class="lensLeftTotalPieces" name="lensLeftTotalPieces[]">
                <input type="hidden"   class="lens_bids" name="lens_bids[]">
            </td>
            <td>
                <input type="text" class="form-control product-qty" name="product_qty[]" value="0" readonly>
            </td>
            <td class="tax-col">
                <input type="text" class="form-control hsn-code" name="hsn_code[]">
            </td>
            <td class="tax-col">
                <input type="text" class="form-control gst-per" name="gst[]">
                <input type="hidden" class="form-control gst-amount" name="gst_amount[]" value="0.00">
            </td>
            <td>
                <div class="input-group mb-3">
                    <input type="text" class="form-control discount-amount" name="discount_amt[]" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                    <input type="text" class="form-control discount" name="discount[]" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                </div>
            </td>
            <td>
                <input type="hidden" class="form-control purchase-price" name="purchase_price[]" value="0.00" placeholder="0.00">
                <input type="hidden" class="form-control base-price" name="base_price[]" value="0.00" placeholder="0.00">
                <input type="hidden" class="form-control retail-price" name="retail_price[]" value="0.00" placeholder="0.00">
                <input type="text" class="form-control sale-price" name="sale_price[]" value="0.00" placeholder="0.00">
            </td>
    
            <td><button type="button" class="removeBtn">X</button></td>
        `;
    
        tableBody.appendChild(newRow);
        updateSerialNumbers();
    });
    
    // ----------------- REMOVE ROW -----------------
    $(document).on("click", ".removeBtn", function () {
        $(this).closest("tr").remove();
        updateSerialNumbers();
        recalcTotals();
    });
    
    // ----------------- SET ACTIVE ROW ON CLICK -----------------
    $(document).on("click", "#saleTable tbody tr", function () {
        $("#saleTable tbody tr").removeClass("active");
        $(this).addClass("active");
        activeRow = $(this);
    });
    
    updateSerialNumbers();

    
    
    
    /** ==============================
     *  Barcode Wise Product Details
     *  ============================== */
    $(document).on("change", ".barcode", function () 
    {
        let $row = $(this).closest("tr");
        let barcode = $(this).val().trim();
        let tax_rule = $("#tax_rule").val();
        let store_id = $("#store_id").val();
        let sale_type = 'customer';
    
        if (barcode !== '' && tax_rule !== '') 
        {
            let duplicate = false;
            $("#saleTable .barcode").not(this).each(function () {
                if ($(this).val().trim() === barcode) {
                    duplicate = true;
                    return false; 
                }
            });
    
            if (duplicate) 
            {
                $row.find(".barcode").val("").focus();
                $.toaster({
                    priority: 'danger',
                    title: ' Duplicate Barcode',
                    message: 'This barcode is already added in another row.',
                    timeout: 3000
                });
                return;
            }

            $.ajax({
                url: "{{ route('admin.get-store-product-by-barcode') }}",
                type: "GET",
                data: { barcode: barcode,tax_rule:tax_rule,sale_type:sale_type,to_store:store_id},
                beforeSend: function () {
                    $("#ajaxLoader").show(); 
                },
                success: function (res) 
                {
                    if (res.success) 
                    {
                        $("#modal_product_type").val(res.data.product_type);
                        handleProductType(res.data.product_type,store_id);
                        $("#modalTitle").text("Add " + res.data.product_type + " Details");
                        $("#productModal").modal("show");
                        $("#modal_product_details").val(res.data.product_details);
                        $("#modal_company").val(res.data.product_company);
                        $("#modal_quality").val(res.data.product_quality);
                        $("#modal_product_material").val(res.data.product_material);
                        $("#modal_product_color").val(res.data.product_color);
                        $("#modal_product_design").val(res.data.product_design);
                        $("#modal_product_coating").val(res.data.product_coating);
                        $("#modal_product_index").val(res.data.product_index);
                        $("#modal_product_number").val(res.data.product_number);
                        $("#modal_product_ct").val(res.data.product_ct);
                        $("#modal_product_validity").val(res.data.product_validity);
                        $("#modal_product_typess").val(res.data.product_typesss);
                        $("#modal_product_variant").val(res.data.product_variant);
                        $("#modal_product_shape").val(res.data.product_shape);
                        $("#modal_product_size").val(res.data.product_size);
                        $("#modal_purchase_price").val(res.data.purchase_price);
                        $("#modal_retail_price").val(res.data.retail_price);
                        $("#modal_product_code").val(res.data.product_code);
                        $("#modal_hsncode").val(res.data.hsn_code);
                        $("#modal_gst").val(res.data.gstRate);
                        $("#modal_base_price").val(res.data.basePrice);
                        $("#modal_gst_amount").val(res.data.gstAmount);
                        $("#modal_total_sale").val(res.data.totalSale);
                        $("#modal_tax_rule").val(tax_rule);
                        $("#modal_product_id").val(res.data.product_id);
                        $("#modal_quantity").val(res.data.product_qty);
                        $("#modal_discount").val(res.data.discount);
                        $("#modal_discount_amount").val(res.data.discountamt);
                        
                        if (res.data.is_pair == 1) {
                            $("input[name='modal_rightleft'][value='Left']").prop("checked", false);
                        } else {
                            $("input[name='modal_rightleft'][value='Left']").prop("checked", true);
                        }
                        
                        
                        $(this).val("").trigger("change");
                    }
                    else 
                    {
                        $row.find(".barcode").val("").focus();
                        $row.find(".product-type, .product-description, .product-qty, .purchase-price, .retail-price, .discount-amount, .discount").val("");
                
                        $.toaster({
                            priority: 'danger',
                            title: ' Barcode Not Found',
                            message: 'Please check and try again.',
                           timeout: 3000
                        });
                    }
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
                title: ' Error',
                message: 'Something went wrong while fetching product.'
            });
        }
    });
    
    
    /** ==============================
     *  Product Type Wise Modal Div Open
     *  ============================== */
     
    $(document).on("change", ".product-type", function () {

        activeRow = $(this).closest("tr");   
    
        let selectedType = $(this).val();
        let store_id = $("#store_id").val();
        let tax_rule = $("#tax_rule").val();
        
        if (store_id == '') {
            $.toaster({
                priority: 'danger',
                title: ' error',
                message: 'Select Store',
                timeout: 3000
            });
            activeRow.find(".product-type").val("");

            return;
        }
    
        $.ajax({
            url: "{{ route('admin.get-gst-details') }}",
            type: "GET",
            data: { product_type: selectedType },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
            success: function (res) {
    
                if (selectedType == 'Frame' || selectedType == 'Goggles') {
                    $.toaster({
                        priority: 'danger',
                        title: ' error',
                        message: 'Frame or Goggles use barcode',
                        timeout: 3000
                    });
    
                    activeRow.find(".product-type").val("");
                    return;
                }
                

                $("#modal_product_type").val(selectedType);
                $("#modal_tax_rule").val(tax_rule);
                handleProductType(selectedType,store_id);
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
     
    function handleProductType(type,store_id)
    {
        $.ajax({
                url: "{{ route('admin.get-store-details') }}",
                type: "GET",
                data: { selectedType: store_id },
                beforeSend: function () {
                    $("#ajaxLoader").show();
                },
                success: function (res) {
                if (res.success) {

                    if(res.data.sales_tax_type == 0) 
                    {
                        $("#modal_product_code").prop("readonly", true);
                        $("#modal_product_details").prop("readonly", true);
                        
                        switch (type) 
                        {
                            case "Frame":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                $("#qtydiv, #purchasediv").show();
                                $("#modal_product_code").prop("readonly", true);
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                break;
                            
                             case "Goggles":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                $("#modal_product_code").prop("readonly", true);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                break;    
                                
                            case "Glass":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#materialediv, #colordiv, #designdiv, #coatingdiv, #indexdiv,#branddiv").show();
                                $("#Prescriptionglassdiv, #purchasediv,#branddiv").show();
                                $("#rlinventory,#counteye,#SelectBox,#qtydiv").hide();
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                $("#modal_product_code").prop("readonly", false);
                                break;
                    
                            case "Lens":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#numberdiv, #ctdiv, #typediv, #validitydiv, #materialediv,#colordiv,#Prescriptionglassdiv").show();
                                $("#qtydiv, #purchasediv,#SelectBox").show();
                                $("#inhousepackage, #branddiv,#designdiv,#coatingdiv,#indexdiv").hide();
                                $("#nearvisionright, #addright, #nearvisionleft, #addleft, #wparameter, #pdright").hide();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv,#qtydiv").hide();
                                break;
                    
                            case "Solution":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#colordiv,#packingtypediv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                break;
                    
                            case "Other":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#shapediv, #colordiv, #sizediv, #typediv").show();
                                $("#qtydiv,#purchasediv").show();
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                $("#modal_product_code").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                break;
                    
                            case "Repair":
                                $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#materialediv,#colordiv,#designdiv,#coatingdiv,#indexdiv,#numberdiv,#ctdiv,#typediv,#validitydiv,#shapediv,#sizediv,#variantdiv").hide();
                                $("#Prescriptionglassdiv, #inhousepackage, #branddiv,#qtydiv,#purchasediv").hide();
                                $("#modal_product_details").prop("readonly", false);
                                $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").hide();
                                break;
                        }        
            
                    }
                    else 
                    {
                        $("#modal_product_code").prop("readonly", true);
                        $("#modal_product_details").prop("readonly", true);
                        
                        switch (type) 
                        {
                            case "Frame":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                    $("#qtydiv, #purchasediv").show();
                                    $("#modal_product_code").prop("readonly", true);
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    break;
                                
                                 case "Goggles":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                    $("#modal_product_code").prop("readonly", true);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    break;    
                                    
                                case "Glass":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#materialediv, #colordiv, #designdiv, #coatingdiv, #indexdiv,#branddiv").show();
                                    $("#Prescriptionglassdiv, #purchasediv,#branddiv").show();
                                    $("#rlinventory,#counteye,#SelectBox,#qtydiv").hide();
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    $("#modal_product_code").prop("readonly", false);
                                    break;
                        
                                case "Lens":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#numberdiv, #ctdiv, #typediv, #validitydiv, #materialediv,#colordiv,#Prescriptionglassdiv").show();
                                    $("#qtydiv, #purchasediv").show();
                                    $("#inhousepackage, #branddiv,#designdiv,#coatingdiv,#indexdiv").hide();
                                    $("#nearvisionright, #addright, #nearvisionleft, #addleft, #wparameter, #pdright,#qtydiv").hide();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv, #taxdiv,#gstamtdiv").show();
                                    break;
                        
                                case "Solution":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#colordiv,#packingtypediv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    break;
                        
                                case "Other":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#shapediv, #colordiv, #sizediv, #typediv").show();
                                    $("#qtydiv,#purchasediv").show();
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv").hide();
                                    $("#modal_product_code").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    break;
                        
                                case "Repair":
                                    $("#codediv, #iddiv, #companydiv, #qualitydiv,#variantdiv,#materialediv,#colordiv,#designdiv,#coatingdiv,#indexdiv,#numberdiv,#ctdiv,#typediv,#validitydiv,#shapediv,#sizediv,#variantdiv").hide();
                                    $("#Prescriptionglassdiv, #inhousepackage, #branddiv,#qtydiv,#purchasediv").hide();
                                    $("#modal_product_details").prop("readonly", false);
                                    $("#hsncodediv, #gstdiv,#gstamtdiv").show();
                                    break;
                        }    
                        
                    }
                    
                   
                } else {
                    $.toaster({
                        priority: 'danger',
                        title: ' Store Not Found',
                        message: 'Please check and try again.',
                        timeout: 3000
                    });
                    $("#order_no").val(''); // Clear input if store not found
                }
            },
                complete: function () {
                $("#ajaxLoader").fadeOut();
            }
        });
    
    }
    
    
    
    /** ==============================
     *  Lenstype wise Package List
     *  ============================== */
    
    $(document).on('click', 'input[name="lenstype"]', function () {

        let lensType = $(this).val();
    
        $("#lenspackage").html('<p class="text-info">Loading packages...</p>');
    
        $.ajax({
            url: "{{ route('admin.get.lens.packages') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                lens_type: lensType
            },
            success: function (response) {
    
                if (!response.success || !response.packages || response.packages.length === 0) {
                    $("#lenspackage").html(
                        '<div class="alert alert-warning">No packages found for this lens type.</div>'
                    );
                    return;
                }
    
                let html = `<div class="row gy-3">`;
    
                response.packages.forEach(pkg => {
    
                    let imagesHtml = '';
    
                    if (pkg.product_image) {
                        try {
                            let imgs = JSON.parse(pkg.product_image);
    
                            if (Array.isArray(imgs) && imgs.length > 0) {
                                let basePath = "{{ asset('uploads/glass/product') }}";
                                let path = `${basePath}/${pkg.product_id}`;
    
                                imgs.forEach(img => {
                                    imagesHtml += `
                                        <img src="${path}/${img.trim()}"
                                             class="img-thumbnail me-2 mb-2"
                                             style="max-width:70px;">
                                    `;
                                });
                            } else {
                                imagesHtml = '<small class="text-muted">No images available</small>';
                            }
                        } catch (e) {
                            imagesHtml = '<small class="text-muted">No images available</small>';
                        }
                    } else {
                        imagesHtml = '<small class="text-muted">No images available</small>';
                    }
    
                    html += `
                        <div class="col-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
    
                                        <input type="radio"
                                               name="lens_package"
                                               class="form-check-input me-3 mt-1 lens-package-radio"
                                               value="${pkg.id}"
                                               data-id="${pkg.id}"
                                               data-name="${pkg.productdetails}"
                                               data-price="${pkg.Retail_Price}"
                                               data-productcode="${pkg.product_code}"
                                               data-productid="${pkg.product_id}"
                                               data-description="${pkg.Description ? pkg.Description.replace(/"/g, '&quot;') : ''}">
    
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">${pkg.productdetails}</h5>
    
                                            <p class="text-muted mb-2">
                                                ${pkg.Description || ''}
                                            </p>
    
                                            <div class="mb-2">
                                                ${imagesHtml}
                                            </div>
    
                                            <h6 class="text-success mb-0">
                                                Price: ₹ ${pkg.Retail_Price}
                                            </h6>
                                        </div>
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
    
                html += `</div>`;
    
                $("#lenspackage").html(html);
            },
            error: function () {
                $("#lenspackage").html(
                    '<p class="text-danger">Error loading packages.</p>'
                );
            }
        });
    });

    
    
    /** ==============================
     *  Package Details Get In Input
     *  ============================== */
     
    $(document).on("click", ".lens-package-radio", function () {

    let pkgName = $(this).data("name");
    let pkgproductcode = $(this).data("productcode");
    let pkgproductid = $(this).data("productid");
    let pkgPrice = parseFloat($(this).data("price")) || 0;
    let pkgId = $(this).data("id");

    $.ajax({
        url: "{{ route('admin.get.lens.packages.coating') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            pkgId: pkgId
        },
        success: function (response) {

            if (!response.success || !response.packages || response.packages.length === 0) {
                $("#glasscoating").html('');
                return;
            }

            let html = `<div class="row gy-3">`;

            response.packages.forEach(pkg => {

                let path = "{{ asset('frontend/asset/img/Photo-Chromatic_Coating-update.webp') }}";

                html += `
                    <div class="col-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">

                                    <input type="radio"
                                           name="is_Coating"
                                           class="form-check-input me-3 lens-coating-radio"
                                           value="${pkg.id}"
                                           data-coatingprice="${pkg.coating_price}"
                                           data-lens_price="${pkgPrice}"
                                           data-name="${pkgName}"
                                           data-coatingname="${pkg.coating_name}">

                                    <img src="${path}"
                                         class="img-thumbnail me-3"
                                         style="max-width:70px;">

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${pkg.coating_name}</h6>
                                        <span class="text-success fw-semibold">
                                            ₹ ${pkg.coating_price}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += `</div>`;

            $("#glasscoating").html(html);
        },
        error: function () {
            $("#glasscoating").html(
                '<p class="text-danger">Error loading packages.</p>'
            );
        }
    });

    /* =============================
       Fill product details
    ============================== */

    $("#modal_product_code").val(pkgproductcode);
    $("#modal_product_id").val(pkgproductid);
    $("#modal_product_details").val(pkgName);

    /* =============================
       Default values
    ============================== */

    $("#modal_purchase_price").val(0);
    $("#modal_discount").val(0);
    $("#modal_discount_amount").val(0.00);
    $("#modal_gst").val(0);
    $("#modal_tax_rule").val('Include');

    /* =============================
       GST Calculation
    ============================== */

    let gstPercent = parseFloat($("#modal_gst").val()) || 0;

    let basePrice = pkgPrice / (1 + gstPercent / 100);
    let gstAmount = pkgPrice - basePrice;
    let totalPrice = pkgPrice;

    $("#modal_retail_price").val(pkgPrice.toFixed(2));
    $("#modal_base_price").val(basePrice.toFixed(2));
    $("#modal_gst_amount").val(gstAmount.toFixed(2));
    $("#modal_total_sale").val(totalPrice.toFixed(2));
});

    
    
    
    /** ==============================
     *  Package Coating Details Get In Input
     *  ============================== */
     
     $(document).on("click", ".lens-coating-radio", function () {
        let pkgName = $(this).data("name");
        let pkgCoatingName = $(this).data("coatingname");
        let pkgCoatingPrice = parseFloat($(this).data("coatingprice")) || 0;
        let pkgPrice = parseFloat($(this).data("lens_price")) || 0;
        let pkgId = $(this).data("id");


        let details = pkgName;
        $("#modal_product_details").val(details);
        $("#modal_product_coating").val(pkgCoatingName);
    
        $("#modal_purchase_price").val(0);
        $("#modal_discount").val(0);
        $("#modal_discount_amount").val(0.00);
        $("#modal_gst").val(0);
        $("#modal_tax_rule").val('Include');
        
    
        let gstPercent = parseFloat($("#modal_gst").val()) || 0;
    
        let basePrice = 0, gstAmount = 0, totalPrice = 0;
    
        basePrice = (pkgPrice+pkgCoatingPrice) / (1 + gstPercent / 100);
        gstAmount = (pkgPrice+pkgCoatingPrice) - basePrice;
        totalPrice = (pkgPrice+pkgCoatingPrice);
    
    
        $("#modal_retail_price").val((pkgPrice+pkgCoatingPrice).toFixed(2));
        $("#modal_base_price").val(basePrice.toFixed(2));
        $("#modal_gst_amount").val(gstAmount.toFixed(2));
        $("#modal_total_sale").val(totalPrice.toFixed(2));
    });
    
    
    /** ==============================
     *  Product Code Wise Product Details
     *  ============================== */
    $(document).on('keyup', '#modal_product_code', function () {
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
        let tax_rule = $("#tax_rule").val();
        
        let rightLeft = [];
        $('input[name="modal_rightleft"]:checked').each(function () {
            rightLeft.push($(this).val());
        });
        

        $input.val(selectedCode);
        $this.closest('.suggestion-box').hide();

        $.ajax({
            url: "{{ route('admin.get-store-product-by-product-code') }}",
            method: 'GET',
            data: {
                tax_rule: tax_rule,
                selectedCode: selectedCode,
                rightLeft: rightLeft,
                productType: productType
                
            },
            
            success: function (res) 
            {
                if (res.success) 
                {
                    $("#modal_product_type").val(res.data.product_type);
                    $("#modal_product_details").val(res.data.product_details);
                    $("#modal_company").val(res.data.product_company);
                    $("#modal_quality").val(res.data.product_quality);
                    $("#modal_product_material").val(res.data.product_material);
                    $("#modal_product_color").val(res.data.product_color);
                    $("#modal_product_design").val(res.data.product_design);
                    $("#modal_product_coating").val(res.data.product_coating);
                    $("#modal_product_index").val(res.data.product_index);
                    $("#modal_product_number").val(res.data.product_number);
                    $("#modal_product_ct").val(res.data.product_ct);
                    $("#modal_product_validity").val(res.data.product_validity);
                    $("#modal_product_typess").val(res.data.product_typesss);
                    $("#modal_product_variant").val(res.data.product_variant);
                    $("#modal_product_shape").val(res.data.product_shape);
                    $("#modal_product_size").val(res.data.product_size);
                    $("#modal_purchase_price").val(res.data.purchase_price);
                    $("#modal_retail_price").val(res.data.retail_price);
                    $("#modal_product_code").val(res.data.product_code);
                    $("#modal_hsncode").val(res.data.hsn_code);
                    $("#modal_gst").val(res.data.gstRate);
                    $("#modal_base_price").val(res.data.basePrice);
                    $("#modal_gst_amount").val(res.data.gstAmount);
                    $("#modal_total_sale").val(res.data.totalSale);
                    $("#modal_product_id").val(res.data.product_id);
                    $("#modal_quantity").val(res.data.product_qty);
                    $("#modal_discount").val(res.data.discount);
                    $("#modal_discount_amount").val(res.data.discountamt);
                }    
            }
            
        });
    });
    
    /** =================================
     *  Glass Brand Wise Div Show
     * ================================== */
    
    $(document).on('click', 'input[name="brand_type"]', function () 
    {
        let selected = $(this).val();
    
        if (selected == '0') { 
            $("#codediv, #iddiv, #companydiv, #qualitydiv,#materialediv, #colordiv, #designdiv, #coatingdiv, #indexdiv").show();
            $("#Prescriptionglassdiv,#qtydiv, #hsncodediv, #gstdiv, #taxdiv, #purchasediv, #gstamtdiv").show();
            $("#inhousepackage,#branddiv").show();

        } 
        else if (selected == '1') { 
            $("#codediv, #iddiv, #companydiv, #qualitydiv,#materialediv, #colordiv, #designdiv, #coatingdiv, #indexdiv,#branddiv").show();
            $("#Prescriptionglassdiv,#qtydiv, #hsncodediv, #gstdiv, #taxdiv, #purchasediv, #gstamtdiv").show();
            $("#inhousepackage").hide();
        }
    });

    
    /** =================================
     *  Modal Data Add In Table Row
     * ================================== */
    
    $(".addmodalbtn").click(function () {

        let product_type = $("#modal_product_type").val();
        let product_details = $("#modal_product_details").val();
        let purchasePrice = parseFloat($("#modal_purchase_price").val()) || 0;
        let retailPrice = parseFloat($("#modal_retail_price").val()) || 0;
        let totalSale = parseFloat($("#modal_total_sale").val()) || 0;
        let brand_type = $('input[name="brand_type"]:checked').val();
        let lens_type = $('input[name="lenstype"]:checked').val();
    
        // ---------------- VALIDATION ----------------
        if (brand_type === '0') {
            if (!lens_type) { alert('Lens Type required'); return; }
            if (retailPrice <= 0) { alert('Retail Price required'); return; }
            if (totalSale <= 0) { alert('Total Sale required'); return; }
        } 
        else if (product_type == 'Repair') {
            if (!product_details) { alert('Product Details required'); return; }
            if (retailPrice <= 0) { alert('Retail Price required'); return; }
            if (totalSale <= 0) { alert('Total Sale required'); return; }
        } 
        else {
            if (!product_details) { alert('Product Details required'); return; }
            if (retailPrice <= 0) { alert('Retail Price required'); return; }
            if (totalSale <= 0) { alert('Total Sale required'); return; }
        }
    
        // ---------------- ROW SELECTION FIX ----------------
        if (!activeRow || activeRow.length === 0) {
            alert("No row selected!");
            return;
        }
        
        if(product_type == 'Glass')
        {
            const checkboxes = document.querySelectorAll('input[name="modal_rightleft"]');
            const errorDiv = document.getElementById('checkboxError');
            let isChecked = false;
        
            checkboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    isChecked = true;
                }
            });
        
            if (!isChecked) {
                alert("Right Or Left Select One to  Checked");
                return;
            } 
        }
    
        let row = activeRow;
        
        if (product_type == 'Glass') {
            const checkboxes = document.querySelectorAll('input[name="modal_rightleft"]');
            let checkedCount = 0;
        
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) checkedCount++;
            });
        
            // If both are checked, qty = 2, else qty = 1
            let quantity = checkedCount === 2 ? 2 : 1;
            row.find(".product-qty").val(quantity);
        }
        else
        {
            row.find(".product-qty").val($("#modal_quantity").val());
        }
    
        // ---------------- BASIC FIELDS ----------------
        row.find(".product-id").val($("#modal_product_id").val());
        row.find(".product-type").val($("#modal_product_type").val());
        row.find(".product-code").val($("#modal_product_code").val());
        row.find(".product-description").val($("#modal_product_details").val());
        row.find(".product-company").val($("#modal_company").val());
        row.find(".product-quality").val($("#modal_quality").val());
        row.find(".product-materiale").val($("#modal_product_material").val());
        row.find(".product-color").val($("#modal_product_color").val());
        row.find(".product-design").val($("#modal_product_design").val());
        row.find(".product-coating").val($("#modal_product_coating").val());
        row.find(".product-index").val($("#modal_product_index").val());
        row.find(".product-number").val($("#modal_product_number").val());
        row.find(".product-ct").val($("#modal_product_ct").val());
        row.find(".product-typesss").val($("#modal_product_typesss").val());
        row.find(".product-validity").val($("#modal_product_validity").val());
        row.find(".product-shape").val($("#modal_product_shape").val());
        row.find(".product-size").val($("#modal_product_size").val());
        row.find(".product-variant").val($("#modal_product_variant").val());
        row.find(".hsn-code").val($("#modal_hsncode").val());
        row.find(".gst-per").val($("#modal_gst").val());
        row.find(".gst-amount").val($("#modal_gst_amount").val());
        row.find(".base-price").val($("#modal_base_price").val());
        row.find(".retail-price").val($("#modal_retail_price").val());
        row.find(".sale-price").val($("#modal_total_sale").val());
        row.find(".purchase-price").val($("#modal_purchase_price").val());
        row.find(".discount-amount").val($("#modal_discount_amount").val());
        row.find(".discount").val($("#modal_discount").val());
        row.find(".product-typesss").val($("#modal_packing_type").val());
        
        row.find(".lensRightNoOfBoxes").val($("#lensRightNoOfBoxes").val());
        row.find(".lensRightTotalPieces").val($("#lensRightTotalPieces").val());
        row.find(".lensLeftNoOfBoxes").val($("#lensLeftNoOfBoxes").val());
        row.find(".lensLeftTotalPieces").val($("#lensLeftTotalPieces").val());
        row.find(".lens_bids").val($("#modal_lens_bids").val());
        
    
        // ---------------- PRESCRIPTION FIELDS ----------------
        let fields = [
            "GL_EYE_RS_D","GL_EYE_RC_D","GL_EYE_RA_D","GL_EYE_RP_D","GL_EYE_RV_D",
            "GL_EYE_RS_N","GL_EYE_RC_N","GL_EYE_RA_N","GL_EYE_RP_N","GL_EYE_RV_N",
            "GL_EYE_RADD","GL_EYE_totalPD","GL_EYE_LS_D","GL_EYE_LC_D","GL_EYE_LA_D",
            "GL_EYE_LP_D","GL_EYE_LV_D","GL_EYE_LS_N","GL_EYE_LC_N","GL_EYE_LA_N",
            "GL_EYE_LP_N","GL_EYE_LV_N","GL_EYE_LADD"
        ];
    
        fields.forEach(f => {
            row.find("." + f).val($("#" + f).val());
        });
    
        // ---------------- FRAME FIELDS ----------------
        row.find(".frame-asize").val($("#modal_asize").val());
        row.find(".frame-bsize").val($("#modal_bsize").val());
        row.find(".frame-dbl").val($("#modal_dbl").val());
        row.find(".frame-fh").val($("#modal_FH").val());
        row.find(".frame-ed").val($("#modal_ED").val());
        
        row.find(".prescription-notes").val($("#modal_prescription_notes").val());
    
        // ---------------- RIGHT/LEFT CHECKBOX ----------------
        let RL = $('input[name="modal_rightleft"]:checked')
                    .map(function(){ return $(this).val(); })
                    .get()
                    .join(", ");
        row.find(".right-left").val(RL);
        
         // ---------------- WEARING (radio) ----------------
        row.find(".wearing-inhouse").val($('input[name="lenstype"]:checked').val() || "");
    
        // ---------------- DOCTOR ----------------
        row.find(".doctor-name").val($("#modal_doctor_name").val());
        row.find(".patient-name").val($("#modal_patient_name").val());
    
        // ---------------- WEARING TYPE (checkbox) ----------------
        let wearing = $('input[name="glassWearingType[]"]:checked')
                        .map(function(){ return $(this).val(); })
                        .get()
                        .join(", ");
        row.find(".wearing-type").val(wearing);
    
       
    
        // ---------------- PACKAGE + COATING ----------------
        row.find(".package-id").val($('input[name="lens_package"]:checked').val() || "");
        row.find(".coating-apply").val($('input[name="is_Coating"]:checked').val() || "");
    
        // ---------------- FRAME TYPE GLASS ----------------
        row.find(".frametypeglass").val($('input[name="frametypeglass"]:checked').val() || "");
        
        row.find(".count-eye-test").val($('input[name="count_eye_test"]:checked').val() || "");
    
        // ---------------- CLOSE MODAL ----------------
        $("#productModal").modal("hide");
        $("#productModal").find("input:not([type=radio]):not([type=checkbox]), textarea, select").val("");
        recalcTotals();
    });

    $(document).on("focus", ".barcode", function ()
    {
        $("#saleTable tbody tr").removeClass("active");
        $(this).closest("tr").addClass("active");
    });
    
    $(document).on("focus", ".product-type", function ()
    {
        $("#saleTable tbody tr").removeClass("active");
        $(this).closest("tr").addClass("active");
    });
    
    
    /** =================================
     *  Calculate Modal Input Price
     * ================================== */
    
    function recalcModalTotals() {
        let qty = parseFloat($("#modal_quantity").val()) || 1;
        let retailPrice = parseFloat($("#modal_retail_price").val()) || 0;
        let gst = parseFloat($("#modal_gst").val()) || 0;
        let discountPercent = parseFloat($("#modal_discount").val()) || 0;
        let discountAmount = parseFloat($("#modal_discound_amount").val()) || 0;
        let taxRule = $("#tax_rule").val();

        let baseBeforeDiscount = retailPrice * qty;

        let appliedDiscount = 0;
        if (discountPercent > 0) {
            appliedDiscount = baseBeforeDiscount * (discountPercent / 100);
            $("#modal_discound_amount").val(appliedDiscount.toFixed(2)); // auto-update
        } else if (discountAmount > 0) {
            appliedDiscount = discountAmount;
        }
        let afterDiscount = baseBeforeDiscount - appliedDiscount;

        let basePrice = 0, gstAmount = 0, totalSale = 0;

        if (taxRule === "Include") {
            basePrice = afterDiscount / (1 + (gst / 100));
            gstAmount = afterDiscount - basePrice;
            totalSale = afterDiscount;
        } 
        else if (taxRule === "Exclude ") { // note: value has space in your HTML!
            basePrice = afterDiscount;
            gstAmount = basePrice * (gst / 100);
            totalSale = basePrice + gstAmount;
        } 
        else { 
            basePrice = afterDiscount;
            gstAmount = 0;
            totalSale = basePrice;
        }

        
        $("#modal_base_price").val(basePrice.toFixed(2));
        $("#modal_gst_amount").val(gstAmount.toFixed(2));
        $("#modal_total_sale").val(totalSale.toFixed(2));
    }

    // Trigger on change
    $("#modal_gst, #modal_retail_price, #modal_discount, #modal_discound_amount, #tax_rule")
        .on("input change", recalcModalTotals);

    // Initial calculation
    recalcModalTotals();
    

    /* -------------------------
       Pay Amount  Pending Calculation
    ------------------------- */
    $(document).on("keyup", "#pay_amount", function () {
        // Get numeric values, defaulting to 0 if empty or invalid
        let totalPayable = parseFloat($("#total_payable").val()) || 0;
        let customer_account = parseFloat($("#customer_account").val()) || 0;
        let payAmount = parseFloat($(this).val()) || 0;
    
        // Calculate pending amount, never less than 0
        let pending = totalPayable - (payAmount + customer_account);

        // Update fields with 2 decimal places
        $("#pending_amount").val(pending.toFixed(2));
        $("#advance_amount").val((payAmount + customer_account).toFixed(2));
    });
    
    /* -------------------------
       Redeem Point Apply
    ------------------------- */
    $('#redeemBtn').on('click', function() 
    {
        let contact_no = $('#contact_no').val().trim();
        let errorField = $('#mobileError');
    
        errorField.text('');
    
    
        if (!/^[6-9]\d{9}$/.test(contact_no)) {
            $.toaster({
                priority: 'danger',
                title: ' Mobile No not valid',
                message: 'Please enter a valid 10-digit mobile number.',
                timeout: 3000
            });
            return;
        }
    
        $.ajax({
            url: "{{ route('admin.check-loyalty-point') }}",
            type: 'POST',
            data: { contact_no: contact_no, _token: '{{ csrf_token() }}' },
            beforeSend: function() {
                $('#redeemBtn').prop('disabled', true).text('Checking...');
            },
            success: function(response) 
            {
                $('#redeemBtn').prop('disabled', false).text('Redeem Loyalty Points');
                if(response.exists) 
                {
                    $('#availablePoints').val(response.Loyalty_Points_Bal);
                    $('#redeemModal').modal('show');
                } 
                else 
                {
                    $.toaster({
                        priority: 'danger',
                        title: ' Redeem Loyalty Points',
                        message: 'Redeem Loyalty Points Not Avilable.',
                        timeout: 3000
                    });
                }
            },
            error: function() {
                $('#redeemBtn').prop('disabled', false).text('Redeem Loyalty Points');
                $.toaster({
                    priority: 'danger',
                    title: ' error',
                    message: 'Something went wrong. Please try again.'
                });
            }
        });
    
    });
    
    const pointValue = 1; // 1 point = тВ╣1

    $('#redeemPoints').on('input', function () 
    {
        let points = parseFloat($(this).val()) || 0;
        let available = parseFloat($('#availablePoints').val()) || 0;
        let contact_no = $('#contact_no').val().trim();
        
        $.ajax({
              type: "POST",
              url: "{{ route('admin.checksetloyaltypointvalue') }}",
              data: {
                points: points, 
                available: available,
                _token: "{{ csrf_token() }}"
              },
              dataType: "json",
              success: function (response) {
                if (response.status_code === '200')
                {
                    $.toaster({
                        priority: 'warning',
                        title: 'Loyalty Points',
                        message: 'You can use maximum '  +response.maxAllowedPoints+ ' points only.',
                        timeout: 9000
                    });
                    $('#redeemPoints').val('');
                    return;
                }
                
                else if (response.status_code === '201') {
                    let amount = points * response.one_point_redem; 
                    $('#redeemPointsAmount').val(amount.toFixed(2));
                }
              },
              error: function () {
                document.getElementById('otp-section').style.display = 'none';    
                $.toaster({
                  priority: "danger",
                  title: "Error..!",
                  message: "Failed to send OTP. Please try again.",
                  timeout: 3000
                });
              }
            });
    

    
        
    });
    
    $('#sendOtpredeem').on('click', function() 
    {
        let contact_no = $('#contact_no').val().trim();
    
    
    
        if (!/^[6-9]\d{9}$/.test(contact_no)) {
            $.toaster({
                priority: 'danger',
                title: ' Mobile No not valid',
                message: 'Please enter a valid 10-digit mobile number.',
                timeout: 3000
            });
            return;
        }
    
         if (contact_no.length === 10) {
            
            $.ajax({
              type: "POST",
              url: "{{ route('admin.redeemOtp') }}",
              data: {
                contact: contact_no, 
                _token: "{{ csrf_token() }}"
              },
              dataType: "json",
              success: function (response) {
                if (response.status_code === '200')
                {
                    document.getElementById('sendOtpredeem').style.display = 'none'; 
                  showOTPSection();    
                  $.toaster({
                    priority: "success",
                    title: "Success..!",
                    message: "OTP sent to your mobile number.",
                    timeout: 3000
                  });
                }
                
                else if (response.status_code === '201') {
                  document.getElementById('otp-section').style.display = 'none';    
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Something went wrong!",
                     timeout: 3000
                  });
                }
                else if (response.status_code === '202') {
                  document.getElementById('otp-section').style.display = 'none';  
                  document.getElementById("contact").value = "";
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Mobile No already registered.",
                     timeout: 3000
                  });
                }
              },
              error: function () {
                document.getElementById('otp-section').style.display = 'none';    
                $.toaster({
                  priority: "danger",
                  title: "Error..!",
                  message: "Failed to send OTP. Please try again.",
                  timeout: 3000
                });
              }
            });
          }
    
    });
    
    let countdownInterval;
    function showOTPSection() {
      document.getElementById('otp-section').style.display = 'block';
      document.getElementById('resend-btn').disabled = true;
      startCountdown(30); // Start timer with 60 seconds
    }
    
    function startCountdown(seconds) {
      clearInterval(countdownInterval);
      let timeLeft = seconds;
    
      const countdownEl = document.getElementById('countdown');
      const timerEl = document.getElementById('timer');
      const resendBtn = document.getElementById('resend-btn');
    
      if (!countdownEl || !timerEl || !resendBtn) return; // prevent errors
    
      countdownEl.textContent = timeLeft;
    
      countdownInterval = setInterval(() => {
        timeLeft--;
        countdownEl.textContent = timeLeft;
    
        if (timeLeft <= 0) {
          clearInterval(countdownInterval);
          resendBtn.disabled = false;
          timerEl.textContent = "Didn't get the OTP?";
        }
      }, 1000);
    }
    
    
    function resendOTP() 
    {
          // Resend OTP logic (e.g., via AJAX)
          document.getElementById('resend-btn').disabled = true;
          document.getElementById('timer').innerHTML = 'Resend OTP in <span id="countdown">60</span>s';
          startCountdown(30);
          const contact = document.getElementById('contact_no').value;
          
          $.ajax({
              type: "POST",
              url: "{{ route('admin.redeemOtp') }}",
              data: {
                contact: contact, 
                _token: "{{ csrf_token() }}"
              },
              dataType: "json",
              success: function (response) {
                if (response.status_code === '200')
                {
                  showOTPSection();    
                  $.toaster({
                    priority: "success",
                    title: "Success..!",
                    message: "OTP sent to your mobile number.",
                        timeout: 3000
                  });
                } else {
                  document.getElementById('otp-section').style.display = 'none';    
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Something went wrong!",
                        timeout: 3000
                  });
                }
              },
              error: function () {
                document.getElementById('otp-section').style.display = 'none';    
                $.toaster({
                  priority: "danger",
                  title: "Error..!",
                  message: "Failed to send OTP. Please try again.",
                        timeout: 3000
                });
              }
            });
        }
        
        
    $('#confirmRedeem').on('click', function() 
    {
        let redeemPointsAmount = $('#redeemPointsAmount').val().trim();
        let rotp = $('#rotp').val().trim();
    
        if (redeemPointsAmount <= 0) {
            $.toaster({
                priority: 'danger',
                title: ' Redeem amount ',
                message: 'Redeem amount should be grather then 0.',
                        timeout: 3000
            });
            return;
        }
        
        if (rotp == '') {
            $.toaster({
                priority: 'danger',
                title: ' OTP ',
                message: 'Please enter valid otp.',
                        timeout: 3000
            });
            return;
        }
        
        $.ajax({
          type: "POST",
          url: "{{ route('admin.checkredeemOtp') }}",
          data: {
            rotp: rotp, 
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
                  $('#redeemModal').modal('hide');
                  $('#aadredeemBtn').hide();
                  $('#removeBtn').show();
                  $('#loyalty_point').val(redeemPointsAmount);
                  $('#loyalty_point_apply').val(redeemPointsAmount);
                  $.toaster({
                    priority: "success",
                    title: "Success..!",
                    message: "OTP match successfully.",
                        timeout: 3000
                  });
                   recalcTotals();
              
            }
            else if (response.status_code === '201') {
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Please enter valid otp.",
                        timeout: 3000
              });
            }
            else if (response.status_code === '202') {
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Otp expire.",
                        timeout: 3000
              });
            }
          },
          error: function () {
            $.toaster({
              priority: "danger",
              title: "Error..!",
              message: "Failed to verify OTP. Please try again.",
                        timeout: 3000
            });
          }
        });
    
    });  
    
    $('#removeredeemBtn').on('click', function() 
    {
        $.toaster({
            priority: "success",
            title: "Error..!",
            message: "Redeem point remove successfully.",
                        timeout: 3000
        });
        $('#aadredeemBtn').show();
        $('#removeBtn').hide();
        $('#loyalty_point').val('');
        recalcTotals();
    });
    
    
    /* -------------------------
       Coupon Apply
    ------------------------- */
    
    $('#couponBtn').on('click', function() 
    {
        let contact_no = $('#contact_no').val().trim();
        let errorField = $('#mobileError');
    
        errorField.text('');
    
    
        if (!/^[6-9]\d{9}$/.test(contact_no)) {
            $.toaster({
                priority: 'danger',
                title: ' Mobile No not valid',
                message: 'Please enter a valid 10-digit mobile number.',
                        timeout: 3000
            });
            return;
        }
        
         $('#customerMobile').val(contact_no);
         $('#couponModal').modal('show');
    });
    
    
    $('#confirmCoupon').on('click', function() 
    {
        let DiscountCoupon = $('#DiscountCoupon').val().trim();
        let contact_no = $('#contact_no').val().trim();
        let total_item_price = $('#total_item_price').val().trim();
    
        if (DiscountCoupon === '') {
            $.toaster({
                priority: 'danger',
                title: ' Coupon',
                message: 'Please enter a valid coupon.',
                        timeout: 3000
            });
            return;
        }
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.checkcoupon') }}",
            data: {
                DiscountCoupon: DiscountCoupon,
                contact_no: contact_no,
                total_item_price: total_item_price,
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(response) {
                if (response.status_code === '200') 
                {
                    $('#couponModal').modal('hide');
                    $('#addcouponBtn').hide();
                    $('#removeCBtn').show();
                    $('#coupon_amount').val(response.discount_amount);
                    $('#coupon_id').val(response.coupon_id);
                    $.toaster({
                        priority: "success",
                        title: "Success..!",
                        message: "Coupon applied successfully.",
                        timeout: 3000
                    });
                    recalcTotals();
                } 
                else if (response.status_code === '201') {
                    $.toaster({
                        priority: "warning",
                        title: "Oops..!",
                        message: "Please enter a valid coupon.",
                        timeout: 3000
                    });
                } 
                else if (response.status_code === '202') {
                    $.toaster({
                        priority: "warning",
                        title: "Oops..!",
                        message: "Coupon already used.",
                        timeout: 3000
                    });
                } 
                else if (response.status_code === '203') {
                    $.toaster({
                        priority: "warning",
                        title: "Oops..!",
                        message: "Coupon expired.",
                        timeout: 3000
                    });
                } 
                else if (response.status_code === '204') {
                    $.toaster({
                        priority: "warning",
                        title: "Oops..!",
                        message: "Required minimum payable amount Rs. " + response.min_sale_vale + " to apply this discount coupon.",
                        timeout: 3000
                    });
                }
                else if (response.status_code === '205') {
                    $.toaster({
                        priority: "warning",
                        title: "Oops..!",
                        message: "Coupon apply only for first order.",
                        timeout: 3000
                    });
                }
                else if (response.status_code === '205') {
                    $.toaster({
                        priority: "warning",
                        title: "Oops..!",
                        message: "Auto Coupon is invalid.",
                        timeout: 3000
                    });
                }
            },
            error: function() {
                $.toaster({
                    priority: "danger",
                    title: "Error..!",
                    message: "Failed to apply coupon. Please try again.",
                    timeout: 3000
                });
            }
        });
    });
    
    $('#removecouponBtn').on('click', function() 
    {
        $.toaster({
            priority: "success",
            title: "Error..!",
            message: "Coupon amount remove successfully.",
            timeout: 3000
        });
                
        $('#addcouponBtn').show();
        $('#removeCBtn').hide();
        $('#coupon_amount').val('');
        recalcTotals();
    });
    
    
    
     /*****===========================
     * Cart Discount OTP
     * ================================*/
     
    $('#sendOtpcart').on('click', function() 
    {
        let contact_no = $('#contact_no').val().trim();
        let modalCartmobile = $('#modalCartmobile').val().trim();

    
        if (!/^[6-9]\d{9}$/.test(contact_no)) {
            $.toaster({
                priority: 'danger',
                title: ' Mobile No not valid',
                message: 'Please enter a valid 10-digit mobile number.',
                timeout: 3000
            });
            return;
        }
    
         if (contact_no.length === 10) {
            
            $.ajax({
              type: "POST",
              url: "{{ route('admin.cartOtp') }}",
              data: {
                modalCartmobile: modalCartmobile, 
                _token: "{{ csrf_token() }}"
              },
              dataType: "json",
              success: function (response) {
                if (response.status_code === '200')
                {
                    document.getElementById('sendOtpcart').style.display = 'none'; 
                  showOTPSectionCart();    
                  $.toaster({
                    priority: "success",
                    title: "Success..!",
                    message: "OTP sent to your mobile number.",
                    timeout: 3000
                  });
                }
                
                else if (response.status_code === '201') {
                  document.getElementById('otp-cart-section').style.display = 'none';    
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Something went wrong!",
                     timeout: 3000
                  });
                }
                else if (response.status_code === '202') {
                  document.getElementById('otp-cart-section').style.display = 'none';  
                  document.getElementById("contact").value = "";
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Mobile No already registered.",
                     timeout: 3000
                  });
                }
              },
              error: function () {
                document.getElementById('otp-cart-section').style.display = 'none';    
                $.toaster({
                  priority: "danger",
                  title: "Error..!",
                  message: "Failed to send OTP. Please try again.",
                  timeout: 3000
                });
              }
            });
          }
    
    });
    
    let countdownIntervalcart;
    function showOTPSectionCart() {
      document.getElementById('otp-cart-section').style.display = 'block';
      document.getElementById('resend-btn-cart').disabled = true;
      startCountdowncart(30); // Start timer with 60 seconds
      // Optionally: trigger actual OTP send via AJAX
    }
    
    function startCountdowncart(seconds) {
      clearInterval(countdownIntervalcart);
      let timeLeft = seconds;
    
      const countdownEl = document.getElementById('countdowncart');
      const timerEl = document.getElementById('timercart');
      const resendBtn = document.getElementById('resend-btn-cart');
    
      if (!countdownEl || !timerEl || !resendBtn) return; 
    
      countdownEl.textContent = timeLeft;
    
      countdownIntervalcart = setInterval(() => {
        timeLeft--;
        countdownEl.textContent = timeLeft;
    
        if (timeLeft <= 0) {
          clearInterval(countdownIntervalcart);
          resendBtn.disabled = false;
          timerEl.textContent = "Didn't get the OTP?";
        }
      }, 1000);
    }
    
    
    function resendcartOTP() 
    {
          // Resend OTP logic (e.g., via AJAX)
          document.getElementById('resend-btn-cart').disabled = true;
          document.getElementById('timercart').innerHTML = 'Resend OTP in <span id="countdowncart">60</span>s';
          startCountdowncart(30);
          let modalCartmobile = $('#modalCartmobile').val().trim();
          
          $.ajax({
              type: "POST",
              url: "{{ route('admin.cartOtp') }}",
              data: {
                modalCartmobile: modalCartmobile, 
                _token: "{{ csrf_token() }}"
              },
              dataType: "json",
              success: function (response) {
                if (response.status_code === '200')
                {
                  showOTPSectionCart();    
                  $.toaster({
                    priority: "success",
                    title: "Success..!",
                    message: "OTP sent to your mobile number.",
                        timeout: 3000
                  });
                } else {
                  document.getElementById('otp-cart-section').style.display = 'none';    
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Something went wrong!",
                        timeout: 3000
                  });
                }
              },
              error: function () {
                document.getElementById('otp-cart-section').style.display = 'none';    
                $.toaster({
                  priority: "danger",
                  title: "Error..!",
                  message: "Failed to send OTP. Please try again.",
                        timeout: 3000
                });
              }
            });
        }
    
    /* -------------------------
       Cart Discount Apply
    ------------------------- */
    
    $('#cartBtn').on('click', function() 
    {
        let contact_no = $('#contact_no').val().trim();
        let errorField = $('#mobileError');
    
        errorField.text('');
    
    
        if (!/^[6-9]\d{9}$/.test(contact_no)) {
            $.toaster({
                priority: 'danger',
                title: ' Mobile No not valid',
                message: 'Please enter a valid 10-digit mobile number.',
                        timeout: 3000
            });
            return;
        }
        
         $('#cartModal').modal('show');
    });
    
    $(document).on('change', 'input[name="selected_user"]', function() {
        let limit = parseFloat($(this).data('approve-discount')) || 0;
        let approvemobile = parseFloat($(this).data('approve-mobile')) || 0;
    
        // Store approved discount limit
        $('#approvedDiscountLimit').val(limit);
        $('#modalCartmobile').val(approvemobile);
    
        // Reset fields when a new user is selected
        $('#modalCartDiscountAmount, #modalCartDiscountPercentage, #modalCartDiscountOTPReason').val('');
    
        // Show the discount form
        $('#cartdiv').slideDown();
    });
    
    $('#modalCartDiscountAmount').on('input', function() {
        let amount = parseFloat($(this).val()) || 0;
        let limit = parseFloat($('#approvedDiscountLimit').val()) || 0;
        let total_item_price = parseFloat($('#total_item_price').val() || 0);
        
    
        if (total_item_price <= 0) {
            $.toaster({ priority: 'danger', title: '', message: 'Total amount must be greater than zero.' });
            $(this).val('');
            return;
        }
    
        if (amount > total_item_price) {
            $.toaster({
                priority: 'danger',
                title: ' Invalid Amount',
                message: 'Discount cannot exceed total item price.'
            });
            $(this).val('');
            $('#modalCartDiscountPercentage').val('');
            return;
        }
    
        // Calculate percentage
        let percentage = (amount / total_item_price) * 100;
    
        if (percentage > limit) {
            $.toaster({
                priority: 'danger',
                title: ' Limit Exceeded',
                message: 'You cannot exceed ' + limit + '% discount.'
            });
            $(this).val('');
            $('#modalCartDiscountPercentage').val('');
            return;
        }
    
        $('#modalCartDiscountPercentage').val(percentage.toFixed(2));
    });
    
    $('#modalCartDiscountPercentage').on('input', function() {
        let percentage = parseFloat($(this).val()) || 0;
        let limit = parseFloat($('#approvedDiscountLimit').val()) || 0;
        let total_item_price = parseFloat($('#total_item_price').val() || 0);
    
        if (total_item_price <= 0) {
            $.toaster({ priority: 'danger', title: '', message: 'Total amount must be greater than zero.' });
            $(this).val('');
            return;
        }
    
        if (percentage > limit) {
            $.toaster({
                priority: 'danger',
                title: ' Limit Exceeded',
                message: 'You cannot exceed ' + limit + '% discount.'
            });
            $(this).val('');
            $('#modalCartDiscountAmount').val('');
            return;
        }
    
        // Calculate discount amount
        let amount = (percentage / 100) * total_item_price;
        $('#modalCartDiscountAmount').val(amount.toFixed(2));
    });
    
    $('#confirmCart').on('click', function() {
        let selectedUser = $('input[name="selected_user"]:checked');
        let discountAmount = parseFloat($('#modalCartDiscountAmount').val().trim()) || 0;
        let discountPercent = parseFloat($('#modalCartDiscountPercentage').val().trim()) || 0;
        let reason = $('#modalCartDiscountOTPReason').val().trim();
        let approvedLimit = parseFloat($('#approvedDiscountLimit').val().trim()) || 0;
        
        let cotp = $('#cotp').val().trim();
    
        let totalAmount = parseFloat($('#total_item_price').val()) || 0;
    
        if (selectedUser.length === 0) {
            $.toaster({
                priority: 'danger',
                title: ' Select User',
                message: 'Please select a discount approver first.'
            });
            return;
        }
    
        if (discountAmount === 0 ) {
            $.toaster({
                priority: 'danger',
                title: ' Discount Error',
                message: 'Please enter either discount amount OR discount percentage.'
            });
            return;
        }
    
        if (reason === '') {
            $.toaster({
                priority: 'danger',
                title: ' Reason Missing',
                message: 'Please enter reason for discount approval.'
            });
            return;
        }
    
        if (discountPercent > 0) {
            discountAmount = (totalAmount * discountPercent) / 100;
        } else {
            discountPercent = (discountAmount / totalAmount) * 100;
        }
    
        if (discountPercent > approvedLimit) {
            $.toaster({
                priority: 'danger',
                title: ' Limit Exceeded',
                message: 'Entered discount exceeds the approved limit (' + approvedLimit + '%).'
            });
            return;
        }
        
        $.ajax({
          type: "POST",
          url: "{{ route('admin.checkcartOtp') }}",
          data: {
            cotp: cotp, 
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
                   $('#cart_discount').val(discountAmount.toFixed(2));
                    $('#cart_discount_per').val(discountPercent.toFixed(2));
                    $('#cart_discount_by').val(selectedUser.closest('tr').find('td:nth-child(2)').text().trim());
                    $('#cart_discount_resion').val(reason);
                    recalcTotals();
                    
                    $('#addcartBtn').hide();
                    $('#removecartBtn').show();
                
                    $('#cartModal').modal('hide');
                
                    $.toaster({
                        priority: 'success',
                        title: ' Discount Applied',
                        message: 'Cart discount has been successfully applied.'
                    });
              
            }
            else if (response.status_code === '201') {
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Please enter valid otp.",
                        timeout: 3000
              });
            }
            else if (response.status_code === '202') {
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Otp expire.",
                        timeout: 3000
              });
            }
          },
          error: function () {
            $.toaster({
              priority: "danger",
              title: "Error..!",
              message: "Failed to verify OTP. Please try again.",
                        timeout: 3000
            });
          }
        });
    
        
    });
    
    
    $('#removecartDisc').on('click', function() 
    {
        $.toaster({
            priority: "success",
            title: "Error..!",
            message: "Cart Discount remove successfully.",
                        timeout: 3000
        });
        $('#addcartBtn').show();
        $('#removecartBtn').hide();
        $('#cart_discount').val('');
        recalcTotals();
    });
    
    
    function parseNum(val) {
        val = parseFloat(val);
        return isNaN(val) ? 0 : val;
    }
        
    
    /** =================================
     *  Calculate Table Price
     * ================================== */
    
    function recalcTotals() {
        let totalBasic = 0,
            totalGst = 0,
            totalSale = 0,
            totalDis = 0;
    
        $(".base-price, .gst-amount, .sale-price, .discount-amount").each(function () {
            let currentVal = $(this).val();
            if (!currentVal || currentVal === '') {
                $(this).val($(this).attr("value") || 0);
            }
        });
    
        $(".base-price").each(function () {
            totalBasic += parseNum($(this).val());
        });
    
        $(".gst-amount").each(function () {
            totalGst += parseNum($(this).val());
        });
    
        $(".sale-price").each(function () {
            totalSale += parseNum($(this).val());
        });
    
        $(".discount-amount").each(function () {
            totalDis += parseNum($(this).val());
        });
    
        let fitting_fee   = parseNum($("#fitting_fee").val());
        let coupon_amount = parseNum($("#coupon_amount").val());
        let cart_discount = parseNum($("#cart_discount").val());
        let loyalty_point = parseNum($("#loyalty_point").val());
        let roundoff      = parseNum($("#roundoff").val()); // 👈 added
    
        let totalPayable =
            totalSale
            - totalDis
            + fitting_fee
            - coupon_amount
            - cart_discount
            - loyalty_point
            + roundoff; // 👈 added
    
        $("#total_basic_amount").val(totalBasic.toFixed(2));
        $("#total_gst_amount").val(totalGst.toFixed(2));
        $("#total_item_price").val(totalSale.toFixed(2));
        $("#total_discount").val(totalDis.toFixed(2));
        $("#total_payable").val(totalPayable.toFixed(2));
        $("#pending_amount").val(totalPayable.toFixed(2));
    }
    
    $("#roundoff").on("input", function () {
        recalcTotals();
    });

    
    /** =================================
     *  Select Customer Prescription
     * ================================== */
     
    $('#PrescriptionBtn').on('click', function() 
    {
        let contact_no = $('#contact_no').val().trim();
        let errorField = $('#mobileError');
    
        errorField.text('');
    
    
        if (!/^[6-9]\d{9}$/.test(contact_no)) {
            $.toaster({
                priority: 'danger',
                title: ' Mobile No not valid',
                message: 'Please enter a valid 10-digit mobile number.',
                        timeout: 3000
            });
            return;
        }
        
        $.ajax({
            url: "{{ route('admin.getprescription') }}",  // Laravel route
            method: 'GET',
            data: { contact_no: contact_no },
            success: function(response) {
                let tableBody = $('#prescriptionTable tbody');
                tableBody.empty(); // Clear old data
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(prescription) 
                    {
                         function formatValue(value) {
                          return value === null || value === undefined || value === "" ? "-" : value;
                        }
    
                        let row = `
                            <tr>
                                <td>
                                <input type="radio" name="prescriptioneyetest" class="prescription-eyetest"
                                        value="1"
                                        data-re_sph_new="${prescription.re_sph_new}"
                                        data-re_cyl_new="${prescription.re_cyl_new}"
                                        data-re_axis_new="${prescription.re_axis_new}"
                                        data-pd_re_new="${prescription.pd_re_new}"
                                        data-le_sph_new="${prescription.le_sph_new}"
                                        data-le_cyl_new="${prescription.le_cyl_new}"
                                        data-le_axis_new="${prescription.le_axis_new}"
                                        data-pd_le_new="${prescription.pd_le_new}"
                                        data-cust_name="${prescription.cust_name}"
                                        data-optometrist="${prescription.optometrist}">
                                </td>
                                <td>${prescription.cust_name}</td>
                                <td>
                                     <table class="table card-table table-vcenter text-nowrap">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>SPH</th>
                                                <th>CYL</th>
                                                <th>AXIS</th>
                                                <th>PD</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                                <tr>
                                                <th scope="row">RE</th>
                                                <td>${formatValue(prescription.re_sph_new)}</td>
                                                <td>${formatValue(prescription.re_cyl_new)}</td>
                                                <td>${formatValue(prescription.re_axis_new)}</td>
                                                <td>${formatValue(prescription.pd_re_new)}</td>
                                              </tr>
                                              <tr>
                                                <th scope="row">LE</th>
                                                <td>${formatValue(prescription.le_sph_new)}</td>
                                                <td>${formatValue(prescription.le_cyl_new)}</td>
                                                <td>${formatValue(prescription.le_axis_new)}</td>
                                                <td>${formatValue(prescription.pd_le_new)}</td>
                                              </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>${prescription.optometrist}</td>
                                <td>${prescription.date}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="5" class="text-center">No prescriptions found.</td></tr>');
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch prescription details.',
                    timeout: 3000
                });
            }
        });
        
        $('#PrescriptionModal').modal('show');
    });
    
    $(document).on('click', '.prescription-eyetest', function()
    {
        let re_sph_new = $(this).data('re_sph_new');
        let re_cyl_new = $(this).data('re_cyl_new');
        let re_axis_new = $(this).data('re_axis_new');
        let pd_re_new = $(this).data('pd_re_new');
    
        let le_sph_new = $(this).data('le_sph_new');
        let le_cyl_new = $(this).data('le_cyl_new');
        let le_axis_new = $(this).data('le_axis_new');
        let pd_le_new = $(this).data('pd_le_new');
        
        let optometrist = $(this).data('optometrist');
        let cust_name = $(this).data('cust_name');
    
        // Fill modal dropdowns
        $('#GL_EYE_RS_D').val(re_sph_new);
        $('#GL_EYE_RC_D').val(re_cyl_new);
        $('#GL_EYE_RA_D').val(re_axis_new);
        $('#GL_EYE_RP_D').val(pd_re_new);
    
        $('#GL_EYE_LS_D').val(le_sph_new);
        $('#GL_EYE_LC_D').val(le_cyl_new);
        $('#GL_EYE_LA_D').val(le_axis_new);
        $('#GL_EYE_LP_D').val(pd_le_new);
        
        $('#modal_doctor_name').val(optometrist);
        $('#modal_patient_name').val(cust_name);

        $('#PrescriptionModal').modal('hide');
    });
    
    $('#clearPrescriptionBtn').on('click', function() {
        // Reset all RE inputs
        $('#GL_EYE_RS_D').val('');
        $('#GL_EYE_RC_D').val('');
        $('#GL_EYE_RA_D').val('');
        $('#GL_EYE_RP_D').val('');
    
        // Reset all LE inputs
        $('#GL_EYE_LS_D').val('');
        $('#GL_EYE_LC_D').val('');
        $('#GL_EYE_LA_D').val('');
        $('#GL_EYE_LP_D').val('');
        $('#modal_doctor_name').val('');
        $('#modal_patient_name').val('');
    
    
        $('.prescription-eyetest').closest('tr').removeClass('table-active');
    });
    
    
    
    /** =================================
     *  Sales Form Submit
     * ================================== */
     
    $("#saleForm").submit(function(e)
    {
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        
        let order_no = $("#order_no").val().trim();
        let cust_name = document.getElementById("cust_name" + class_name).value.trim();
        let contact_no = document.getElementById("contact_no" + class_name).value.trim();
        let email_id = document.getElementById("email_id" + class_name).value.trim();
        let pincode = document.getElementById("pincode" + class_name).value.trim();
        let state_id = document.getElementById("state_id" + class_name).value.trim();
        let city_id = document.getElementById("city_id" + class_name).value.trim();
        let cust_category = document.getElementById("cust_category" + class_name).value.trim();
        let sale_person = document.getElementById("sale_person" + class_name).value.trim();
        let tax_rule = document.getElementById("tax_rule" + class_name).value.trim();
        let submit_type = document.getElementById("submit_type" + class_name).value;
        let scotp = document.getElementById("scotp" + class_name).value;
        
        if (order_no === "") {
            $("#order_noError").text("Order number is required.");
            $("#order_no").addClass("is-invalid");
            isValid = false;
        } else if (!/^[a-zA-Z0-9\-]+$/.test(order_no)) {
            $("#order_noError").text("Order number can only contain letters, numbers, and dashes.");
            $("#order_no").addClass("is-invalid");
            isValid = false;
        } else {
            $("#order_noError").text("");
            $("#order_no").removeClass("is-invalid");
        }
    
        if (cust_name === "") {
            document.getElementById("cust_nameError" + class_name).textContent = "Customer Name Required.";
            document.getElementById("cust_name" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (scotp === "") {
            document.getElementById("scotpError" + class_name).textContent = "Customer OTP required.";
            document.getElementById("scotp" + class_name).classList.add("is-invalid");
            isValid = false;
        } else if (scotp.length < 4) {

            document.getElementById("scotpError" + class_name).textContent = "OTP must be 4 digits.";
            document.getElementById("scotp" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (scotp === "") {
            document.getElementById("scotpError" + class_name).textContent = "Customer Otp Required.";
            document.getElementById("scotp" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (tax_rule === "") {
            document.getElementById("tax_ruleError" + class_name).textContent = "Tax rule Required.";
            document.getElementById("tax_rule" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (!/^\d{10}$/.test(contact_no)) {
            document.getElementById("contact_noError" + class_name).textContent = "Contact must be a 10-digit number.";
            document.getElementById("contact_no" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (!/^\S+@\S+\.\S+$/.test(email_id)) {
            document.getElementById("email_idError" + class_name).textContent = "Please enter a valid email.";
            document.getElementById("email_id" + class_name).classList.add("is-invalid");
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
        
        if (cust_category === "") {
            document.getElementById("cust_categoryError" + class_name).textContent = "Category is required.";
            document.getElementById("cust_category" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (sale_person   === "")
        {
            document.getElementById("sale_personError" + class_name).textContent = "Select sales person.";
            document.getElementById("sale_person" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if ($("input[name='gender']:checked").length === 0) {
            $("#genderError").text("Please select gender.");
            isValid = false;
        } else {
            $("#genderError").text("");
        }
        
    
        
        if ($("input[name='pay_method']:checked").length === 0) {
            $("#pay_cash").closest(".d-flex").after(
                '<span class="text-danger error" id="payMethodError">Please select a payment method.</span>'
            );
            isValid = false;
        } else {
            $("#payMethodError").remove();
        }
        
        if ($("input[name='extrnal_warranty']:checked").length === 0) {
            $("#yes_w").closest(".d-flex").after(
                '<span class="text-danger error" id="warrantyError">Please select external warranty option.</span>'
            );
            isValid = false;
        } else {
            $("#warrantyError").remove();
        }
        
        let payAmount = parseFloat($("#pay_amount").val()) || 0;
        let totalPayable = parseFloat($("#total_payable").val()) || 0;
        
        if (payAmount <= 0) {
            $("#pay_amount").addClass("is-invalid");
            if ($("#payAmountError").length === 0) {
                $("#pay_amount").after('<span class="text-danger error" id="payAmountError">Please enter a valid pay amount.</span>');
            }
            isValid = false;
        } else if (payAmount > totalPayable) {
            $("#pay_amount").addClass("is-invalid");
            if ($("#payAmountError").length === 0) {
                $("#pay_amount").after('<span class="text-danger error" id="payAmountError">Pay amount cannot be greater than total payable.</span>');
            }
            isValid = false;
        } else {
            $("#payAmountError").remove();
            $("#pay_amount").removeClass("is-invalid");
        }
        
        let form = $("#saleForm")[0];
        let data = new FormData(form);
        
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.add-sale-record') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
                if ($.isEmptyObject(response.error)) 
                {
                    $.toaster({
                        priority: 'success',
                        title: response.success,
                        message: '',
                        timeout: 3000
                    });
                    
                    if(submit_type == '0')
                    {
                        window.location.href = "{{ route('admin.sale-pending-history') }}";
                    }
                    else
                    {
                        window.location.href = "{{ route('admin.sale-history') }}";
                    }
                    
                }
                else 
                {
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
    
    
   
        
        
    /****=============================
     * Wareing TYpe
     *  ==============================*/
         
    function toggleFrameFields() 
    {
        let frametype = $('input[name="frametypeglass"]:checked').val();

        if (frametype === 'Full frame') 
        {
            $('#framesizea').hide();
            $('#framesizeb').hide();
        } 
        else if (frametype === 'Half frame')
        {
            $('#framesizea').hide();
            $('#framesizeb').show();
        } 
        else if (frametype === 'Rimless  frame') 
        { 
            $('#framesizea').show();
            $('#framesizeb').show();
        }
    }

    toggleFrameFields();

    $('input[name="frametypeglass"]').on('change', toggleFrameFields);
    

    // ===========================
    // Create dropdown for each input
    // ===========================
    $('.search_input_function').each(function () {
        const id = $(this).attr('id');

        if ($("#" + id + "ListName").length === 0) {
            $(this).after(`
                <div id="${id}ListName" 
                     class="suggestion-box-glass dropdown-menu"
                     style="display:none; position:relative; z-index:9999; 
                     max-height:180px; overflow-y:auto; background:white;
                     border:1px solid #ccc; width:${$(this).outerWidth()}px;">
                </div>
            `);
        }
    });


    // ===========================
    // Fetch Suggestions (Keyup)
    // ===========================
    $(document).on('keyup', '.search_input_function', function () {

        const input = $(this);
        const id = input.attr('id');
        const query = input.val().trim();
        const listBox = $('#' + id + 'ListName');

        if (query === "") {
            listBox.hide();
            return;
        }

        $.ajax({
            url: "{{ route('admin.glassnumber-dropdown') }}",
            type: "GET",
            data: { name: query },
            success: function (data) {

                listBox.empty();

                if (data.length > 0) {
                    data.forEach(item => {
                        listBox.append(`
                            <div class="dropdown-item-list" 
                                 data-input="${id}"
                                 style="cursor:pointer; padding:6px 10px;">
                                ${item.glass_number}
                            </div>
                        `);
                    });

                    listBox.show();
                } else {
                    listBox.hide();
                }
            }
        });
    });


    // ===========================
    // Click on Suggestion (WORKING)
    // ===========================
    $(document).on('click', '.dropdown-item-list', function () {
        const inputId = $(this).data('input');
        const value = $(this).text().trim();

        $('#' + inputId).val(value);
        $('#' + inputId + 'ListName').hide();
    });


    // ===========================
    // Hide when clicking outside
    // ===========================
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.search_input_function, .suggestion-box-glass').length) {
            $(".suggestion-box-glass").hide();
        }
    });


    $(document).on('click', '.copy-right-to-left', function () {
    
        // List of all Right  Left field ID pairs
        const fields = [
            ["GL_EYE_RS_D", "GL_EYE_LS_D"],
            ["GL_EYE_RC_D", "GL_EYE_LC_D"],
            ["GL_EYE_RA_D", "GL_EYE_LA_D"],
            ["GL_EYE_RP_D", "GL_EYE_LP_D"],
            ["GL_EYE_RV_D", "GL_EYE_LV_D"],
            ["GL_EYE_RPRISM_D", "GL_EYE_LPRISM_D"],
    
            ["GL_EYE_RS_N", "GL_EYE_LS_N"],
            ["GL_EYE_RC_N", "GL_EYE_LC_N"],
            ["GL_EYE_RA_N", "GL_EYE_LA_N"],
            ["GL_EYE_RP_N", "GL_EYE_LP_N"],
            ["GL_EYE_RV_N", "GL_EYE_LV_N"],
            ["GL_EYE_RPRISM_N", "GL_EYE_LPRISM_N"],
    
            ["GL_EYE_RADD", "GL_EYE_LADD"],
    
            ["GL_EYE_totalPD", "GL_EYE_totalPD"] 
        ];
    
        fields.forEach(pair => {
            let right = $("#" + pair[0]).val();
            $("#" + pair[1]).val(right);
        });
    
    });
    
    
    $(document).on('click', '.copy-left-to-right', function () {

        // List of all Left  Right field ID pairs
        const fields = [
            ["GL_EYE_LS_D", "GL_EYE_RS_D"],
            ["GL_EYE_LC_D", "GL_EYE_RC_D"],
            ["GL_EYE_LA_D", "GL_EYE_RA_D"],
            ["GL_EYE_LP_D", "GL_EYE_RP_D"],
            ["GL_EYE_LV_D", "GL_EYE_RV_D"],
            ["GL_EYE_LPRISM_D", "GL_EYE_RPRISM_D"],
    
            ["GL_EYE_LS_N", "GL_EYE_RS_N"],
            ["GL_EYE_LC_N", "GL_EYE_RC_N"],
            ["GL_EYE_LA_N", "GL_EYE_RA_N"],
            ["GL_EYE_LP_N", "GL_EYE_RP_N"],
            ["GL_EYE_LV_N", "GL_EYE_RV_N"],
            ["GL_EYE_LPRISM_N", "GL_EYE_RPRISM_N"],
    
            ["GL_EYE_LADD", "GL_EYE_RADD"],
    
            ["GL_EYE_totalPD", "GL_EYE_totalPD"] 
        ];
    
        fields.forEach(pair => {
            let left = $("#" + pair[0]).val();
            $("#" + pair[1]).val(left);
        });
    
    });
    
    
    /** ==============================
     * Store Wise Tax or order id get
     *  ============================== */
     
    $(document).on("change", "#store_id", function () {
        let selectedType = $(this).val();
    
        $.ajax({
            url: "{{ route('admin.get-store-details') }}",
            type: "GET",
            data: { selectedType: selectedType },
            beforeSend: function () {
                $("#ajaxLoader").show();
            },
            success: function (res) {
            if (res.success)
            {
                $("#order_no").val(res.data.order_no);
        
                if(res.data.sales_tax_type == 0) 
                {
                    $("#saleTable .tax-col").hide();
                    $("#totalbasicdiv").hide();

                    $("#tax_rule").val("Not Applicable").trigger("change");
                    $("#taxrule").val("Not Applicable");
                }
                else 
                {
                    $("#saleTable .tax-col").show();
                    $("#totalbasicdiv").show();
                    
                    if(res.data.tax_rule == 1)
                    {
                       $("#tax_rule").val("Include").trigger("change");
                       $("#taxrule").val("Include");
                    }
                    else
                    {
                        $("#tax_rule").val("Exclude").trigger("change");
                        $("#taxrule").val("Exclude");
                    }
                    
                    $("#sales_text_per").val(res.data.sales_text_per);
                    $("#tax_rule").prop("disabled", true);
                }

            } else {
                $.toaster({
                    priority: 'danger',
                    title: ' Store Not Found',
                    message: 'Please check and try again.',
                    timeout: 3000
                });
                $("#order_no").val(''); // Clear input if store not found
            }
        },

            
            complete: function () {
                $("#ajaxLoader").fadeOut();
            }
        });
    });
    
    
    /* -------------------------
       Coupon Apply
    ------------------------- */
    
    $('#creditBtn').on('click', function() 
    {
        let contact_no = $('#contact_no').val().trim();
        let errorField = $('#mobileError');
    
        errorField.text('');
    
    
        if (!/^[6-9]\d{9}$/.test(contact_no)) {
            $.toaster({
                priority: 'danger',
                title: ' Mobile No not valid',
                message: 'Please enter a valid 10-digit mobile number.',
                        timeout: 3000
            });
            return;
        }
        
        $.ajax({
            url: "{{ route('admin.getcustomer') }}",
            method: "GET",
            data: { contact_no: contact_no },
            success: function (response) {
                if (response.success) {
                    let data = response.data;
    
                     $('#wallet_bal').val(data.credit_amount|| 0);
                     $('#creditModal').modal('show');
    
                } else {
                    $.toaster({ priority: 'danger', title: 'Error', message: response.message,
                   timeout: 3000 });
                }
            },
            error: function () {
                $.toaster({ priority: 'danger', title: 'Error', message: 'Error fetching customer data.',
                timeout: 3000 });
            }
        });
        
         
    });
    
    
    
    $('#confirmCredit').on('click', function() {
        // Convert all input values to numbers
        let pending_amount = parseFloat($('#pending_amount').val().trim()) || 0;
        let credit_amount = parseFloat($('#credit_amount').val().trim()) || 0;
        let pay_amount = parseFloat($('#pay_amount').val().trim()) || 0;
        let total_payable = parseFloat($('#total_payable').val().trim()) || 0;
        let wallet_bal = parseFloat($('#wallet_bal').val().trim()) || 0;
    
        if (credit_amount === 0) {
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Please enter a credit amount.',
                timeout: 3000
            });
            return;
        }
        
        
    
        if (wallet_bal > pending_amount)
        {
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Amount cannot be more than pending amount or wallet bal.',
                timeout: 3000
            });
            return;
        }
    
        $('#pending_amount').val(total_payable - (pay_amount + credit_amount));
        $('#advance_amount').val(pay_amount + credit_amount);
        $('#customer_account').val(credit_amount);
        $('#creditModal').modal('hide');
        $('#addcreditBtn').hide();
        $('#removecreditBtn').show();
    });
    
    
    $('#removecreditDisc').on('click', function() {
        // Convert values to numbers
        let pending_amount = parseFloat($('#pending_amount').val().trim()) || 0;
        let credit_amount = parseFloat($('#credit_amount').val().trim()) || 0;
    
        $.toaster({
            priority: "success",
            title: "Success",
            message: "Redeem point removed successfully.",
            timeout: 3000
        });
    
        $('#addcreditBtn').show();
        $('#removecreditBtn').hide();
        $('#customer_account').val('');
        $('#advance_amount').val(pending_amount - credit_amount);
        $('#pending_amount').val(pending_amount + credit_amount); // use +, not -
    });
    
    
    
    // ==========================
// Global arrays for Right and Left selections
// ==========================
let selectedRightBarcodes = [];
let selectedLeftBarcodes = [];

// ==========================
// Open Lens Barcode Modal
// ==========================
$('.checkLensFromBarcode').on('click', function() {
    let clickedButtonId = this.id;
    let callbtn = clickedButtonId === 'checkRightLensFromBarcode' ? 'Right' : 'Left';
    
    $('#lensSide').val(callbtn);

    let modal_product_details = $('#modal_product_details').val().trim();
    let modal_product_code = $('#modal_product_code').val().trim();
    let store_id = $('#store_id').val().trim();

    $.ajax({
        url: "{{ route('admin.getlensbarcode') }}",
        method: 'GET',
        data: { 
            product_details: modal_product_details,
            product_code: modal_product_code,
            store_id: store_id 
        },
        success: function(response) {
            let tableBody = $('#LensBarcodeTable tbody');
            tableBody.empty();

            if (response.data && response.data.length > 0) {
                response.data.forEach(function(lensbarcode) {
                    let row = `
                        <tr>
                            <td>
                                <input type="checkbox" class="lensCheckbox"
                                    name="barcodeselectedid[]"
                                    value="${lensbarcode.barcode_id}"
                                    data-perbox="${lensbarcode.perbox}"
                                    data-purchase="${lensbarcode.purchase_price}"
                                    data-retail="${lensbarcode.retail_price}">
                            </td>
                            <td>${lensbarcode.barcode_no}</td>
                            <td>${lensbarcode.product_code}</td>
                            <td>${lensbarcode.p_details}</td>
                            <td>${lensbarcode.perbox}</td>
                            <td>${lensbarcode.purchase_price}</td>
                            <td>${lensbarcode.retail_price}</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            } else {
                tableBody.append('<tr><td colspan="7" class="text-center">No Inventory found.</td></tr>');
            }

            $('.lensCheckbox').prop('checked', false);
        },
        error: function() {
            $.toaster({
                priority: 'danger',
                title: 'Error',
                message: 'Failed to fetch inventory details.',
                timeout: 3000
            });
        }
    });

    $('#LensBarcodeModal').modal('show');
});

// ==========================
// Select Barcodes from Modal
// ==========================
$('#selectLensBarcode').on('click', function () {
    let callbtn = $('#lensSide').val();
    let newlySelectedIds = [];
    let boxCount = 0;
    let totalPieces = 0;
    let totalPurchase = 0;
    let totalRetail = 0;

    $('.lensCheckbox:checked').each(function () {
        let bid = $(this).val();
        let perbox = parseFloat($(this).data('perbox')) || 0;
        let purchase = parseFloat($(this).data('purchase')) || 0;
        let retail = parseFloat($(this).data('retail')) || 0;

        newlySelectedIds.push({ id: bid, perbox, purchase, retail });
    });

    if (newlySelectedIds.length === 0) {
        $.toaster({
            priority: 'warning',
            title: 'Warning',
            message: 'Please select at least one barcode.',
            timeout: 3000
        });
        return;
    }

    // Add to Right or Left selection array
    if (callbtn === "Right") 
    {
        newlySelectedIds.forEach(item => {
            if (!selectedRightBarcodes.some(x => x.id === item.id)) selectedRightBarcodes.push(item);
        });
        
        $('#GL_EYE_RS_D').prop('readonly', true);
        $('#GL_EYE_RC_D').prop('readonly', true);
        $('#GL_EYE_RA_D').prop('readonly', true);
        $('#GL_EYE_RP_D').prop('readonly', true);
        $('#GL_EYE_RV_D').prop('readonly', true);
    } else {
        newlySelectedIds.forEach(item => {
            if (!selectedLeftBarcodes.some(x => x.id === item.id)) selectedLeftBarcodes.push(item);
        });
        
        
        $('#GL_EYE_LS_D').prop('readonly', true);
        $('#GL_EYE_LC_D').prop('readonly', true);
        $('#GL_EYE_LA_D').prop('readonly', true);
        $('#GL_EYE_LP_D').prop('readonly', true);
        $('#GL_EYE_LV_D').prop('readonly', true);
    }

    // ✅ Calculate per-side totals
    let rightBoxes = selectedRightBarcodes.length;
    let leftBoxes = selectedLeftBarcodes.length;

    let rightPieces = selectedRightBarcodes.reduce((sum, x) => sum + x.perbox, 0);
    let leftPieces = selectedLeftBarcodes.reduce((sum, x) => sum + x.perbox, 0);

    let rightPurchase = selectedRightBarcodes.reduce((sum, x) => sum + x.purchase, 0);
    let leftPurchase = selectedLeftBarcodes.reduce((sum, x) => sum + x.purchase, 0);

    let rightRetail = selectedRightBarcodes.reduce((sum, x) => sum + x.retail, 0);
    let leftRetail = selectedLeftBarcodes.reduce((sum, x) => sum + x.retail, 0);

    // Update Right/Left fields
    $('#lensRightNoOfBoxes').val(rightBoxes).prop('readonly', true);
    $('#lensRightTotalPieces').val(rightPieces).prop('readonly', true);
    $('#lensLeftNoOfBoxes').val(leftBoxes).prop('readonly', true);
    $('#lensLeftTotalPieces').val(leftPieces).prop('readonly', true);

    // ✅ Calculate combined totals
    let totalBoxes = rightBoxes + leftBoxes;
    let totalPiecesCombined = rightPieces + leftPieces;
    let totalPurchaseCombined = rightPurchase + leftPurchase;
    let totalRetailCombined = rightRetail + leftRetail;

    $('#modal_purchase_price').val(totalPurchaseCombined.toFixed(2)).prop('readonly', true);
    $('#modal_quantity').val(totalBoxes).prop('readonly', true);
    $('#modal_retail_price').val(totalRetailCombined.toFixed(2)).prop('readonly', true);

    // GST & Discount calculation
    let gst = parseFloat($("#modal_gst").val()) || 0;
    let discountPercent = parseFloat($("#modal_discount").val()) || 0;
    let discountAmount = parseFloat($("#modal_discound_amount").val()) || 0;
    let taxRule = $("#tax_rule").val();

    let baseBeforeDiscount = totalRetailCombined;
    let appliedDiscount = 0;
    if (discountPercent > 0) appliedDiscount = baseBeforeDiscount * (discountPercent / 100);
    else if (discountAmount > 0) appliedDiscount = discountAmount;

    $("#modal_discound_amount").val(appliedDiscount.toFixed(2));
    let afterDiscount = baseBeforeDiscount - appliedDiscount;

    let basePrice = 0, gstAmount = 0, totalSale = 0;
    if (taxRule === "Include") {
        basePrice = afterDiscount / (1 + (gst / 100));
        gstAmount = afterDiscount - basePrice;
        totalSale = afterDiscount;
    } else if (taxRule === "Exclude ") {
        basePrice = afterDiscount;
        gstAmount = basePrice * (gst / 100);
        totalSale = basePrice + gstAmount;
    } else {
        basePrice = afterDiscount;
        gstAmount = 0;
        totalSale = basePrice;
    }

    $("#modal_base_price").val(basePrice.toFixed(2));
    $("#modal_gst_amount").val(gstAmount.toFixed(2));
    $("#modal_total_sale").val(totalSale.toFixed(2));

    // Save all selected barcode IDs globally
    let allSelectedBarcodes = [...selectedRightBarcodes, ...selectedLeftBarcodes].map(x => x.id);
    $("#modal_lens_bids").val(allSelectedBarcodes.join(','));

    $('#PrescriptionBtn').prop('disabled', true);
    $('#clearPrescriptionBtn').prop('disabled', true);
    $('#LensBarcodeModal').modal('hide');

});



function updateTotals() {
    // 1. Get quantities
    let lensLeftNoOfBoxes = parseFloat($("#lensLeftNoOfBoxes").val()) || 0;
    let lensRightNoOfBoxes = parseFloat($("#lensRightNoOfBoxes").val()) || 0;
    let qty = lensLeftNoOfBoxes + lensRightNoOfBoxes;

    // 2. Get price, GST, discounts
    let retailPrice = parseFloat($("#modal_retail_price").val()) || 0;
    let gst = parseFloat($("#modal_gst").val()) || 0;
    let discountPercent = parseFloat($("#modal_discount").val()) || 0;
    let discountAmount = parseFloat($("#modal_discound_amount").val()) || 0;
    let purchasePrice = parseFloat($("#modal_purchase_price").val()) || 0;
    let taxRule = $("#tax_rule").val().trim(); // remove extra spaces

    // 3. Base before discount
    let retailPriceTotal = retailPrice * qty;
    let purchasePriceTotal = purchasePrice * qty;

    // 4. Apply discount
    let appliedDiscount = 0;
    if (discountPercent > 0) {
        appliedDiscount = retailPriceTotal * (discountPercent / 100);
        $("#modal_discound_amount").val(appliedDiscount.toFixed(2)); // auto-update discount amount
    } else if (discountAmount > 0) {
        appliedDiscount = discountAmount;
    }

    let afterDiscount = retailPriceTotal - appliedDiscount;

    // 5. Calculate GST and total sale
    let basePrice = 0, gstAmount = 0, totalSale = 0;

    if (taxRule === "Include") {
        basePrice = afterDiscount / (1 + (gst / 100));
        gstAmount = afterDiscount - basePrice;
        totalSale = afterDiscount;
    } else if (taxRule === "Exclude") {
        basePrice = afterDiscount;
        gstAmount = basePrice * (gst / 100);
        totalSale = basePrice + gstAmount;
    } else {
        basePrice = afterDiscount;
        gstAmount = 0;
        totalSale = basePrice;
    }

    // 6. Update the DOM
    $("#modal_purchase_price").val(purchasePriceTotal.toFixed(2));
    $("#modal_retail_price").val(retailPriceTotal.toFixed(2));
    $("#modal_base_price").val(basePrice.toFixed(2));
    $("#modal_gst_amount").val(gstAmount.toFixed(2));
    $("#modal_total_sale").val(totalSale.toFixed(2));
}
$("#lensLeftNoOfBoxes, #lensRightNoOfBoxes").on("keyup", updateTotals);


});
</script>


<script>
  // When second modal opens, keep body locked
  $('#LensBarcodeModal').on('shown.bs.modal', function () {
    $('body').addClass('modal-open');
  });

  // When second modal closes
  $('#LensBarcodeModal').on('hidden.bs.modal', function () {
    if ($('#productModal').hasClass('show')) {
      // First modal still open → restore backdrop
      $('body').addClass('modal-open');
      $('<div class="modal-backdrop fade show"></div>').appendTo(document.body);
    }
  });

  // When first modal closes → FULL CLEANUP
  $('#productModal').on('hidden.bs.modal', function () {
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
  });
  
  
  
  $('#PrescriptionModal').on('shown.bs.modal', function () {
    $('body').addClass('modal-open');
  });
  
  
  // When second modal closes
  $('#PrescriptionModal').on('hidden.bs.modal', function () {
    if ($('#productModal').hasClass('show')) {
      // First modal still open → restore backdrop
      $('body').addClass('modal-open');
      $('<div class="modal-backdrop fade show"></div>').appendTo(document.body);
    }
  });
  
  
$('#sendOtpsale').on('click', function() 
{
    let contact_no = $('#contact_no').val().trim();


    if (!/^[6-9]\d{9}$/.test(contact_no)) {
        $.toaster({
            priority: 'danger',
            title: ' Mobile No not valid',
            message: 'Please enter a valid 10-digit mobile number.',
            timeout: 3000
        });
        return;
    }

     if (contact_no.length === 10) {
        
        $.ajax({
          type: "POST",
          url: "{{ route('admin.saleOtp') }}",
          data: {
            contact_no: contact_no, 
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
              document.getElementById('sendOtpsale').style.display = 'none'; 
              showOTPSectionSale();    
              $.toaster({
                priority: "success",
                title: "Success..!",
                message: "OTP sent to your mobile number.",
                timeout: 3000
              });
            }
            
            else if (response.status_code === '201') {
              document.getElementById('otp-sale-section').style.display = 'none';    
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Something went wrong!",
                 timeout: 3000
              });
            }
            else if (response.status_code === '202') {
              document.getElementById('otp-sale-section').style.display = 'none';  
              document.getElementById("contact").value = "";
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Mobile No already registered.",
                 timeout: 3000
              });
            }
          },
          error: function () {
            document.getElementById('otp-sale-section').style.display = 'none';    
            $.toaster({
              priority: "danger",
              title: "Error..!",
              message: "Failed to send OTP. Please try again.",
              timeout: 3000
            });
          }
        });
      }

});

let countdownIntervalSale;
function showOTPSectionSale() {
  document.getElementById('otp-sale-section').style.display = 'block';
  document.getElementById('resend-btn-sale').disabled = true;
  startCountdownsale(30); // Start timer with 60 seconds
  // Optionally: trigger actual OTP send via AJAX
}

function startCountdownsale(seconds) {
  clearInterval(countdownIntervalSale);
  let timeLeft = seconds;

  const countdownEl = document.getElementById('countdownsale');
  const timerEl = document.getElementById('timersale');
  const resendBtn = document.getElementById('resend-btn-sale');

  if (!countdownEl || !timerEl || !resendBtn) return; 

  countdownEl.textContent = timeLeft;

  countdownIntervalSale = setInterval(() => {
    timeLeft--;
    countdownEl.textContent = timeLeft;

    if (timeLeft <= 0) {
      clearInterval(countdownIntervalSale);
      resendBtn.disabled = false;
      timerEl.textContent = "Didn't get the OTP?";
    }
  }, 1000);
}


function resendsaleOTP() 
{
      // Resend OTP logic (e.g., via AJAX)
      document.getElementById('resend-btn-sale').disabled = true;
      document.getElementById('timersale').innerHTML = 'Resend OTP in <span id="countdownsale">60</span>s';
      startCountdownsale(30);
      let contact_no = $('#contact_no').val().trim();
      
      $.ajax({
          type: "POST",
          url: "{{ route('admin.saleOtp') }}",
          data: {
            contact_no: contact_no, 
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
              showOTPSectionSale();    
              $.toaster({
                priority: "success",
                title: "Success..!",
                message: "OTP sent to your mobile number.",
                    timeout: 3000
              });
            } else {
              document.getElementById('otp-sale-section').style.display = 'none';    
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: "Something went wrong!",
                    timeout: 3000
              });
            }
          },
          error: function () {
            document.getElementById('otp-sale-section').style.display = 'none';    
            $.toaster({
              priority: "danger",
              title: "Error..!",
              message: "Failed to send OTP. Please try again.",
                    timeout: 3000
            });
          }
        });
    }
    
    $('#scotp').on('keyup', function() {
        let scotpVal = $(this).val().trim();
        if (scotpVal.length === 4) {
            verifySaleOtp();
        }
    });
    
    function verifySaleOtp() {
        let scotp = $('#scotp').val().trim();
        if (scotp === '') {
            $.toaster({ priority: 'danger', title: 'Otp', message: 'OTP Field required.' });
            return;
        }
    
        $.ajax({
            type: "POST",
            url: "{{ route('admin.checksaleOtp') }}",
            data: { scotp: scotp, _token: "{{ csrf_token() }}" },
            dataType: "json",
            success: function (response) {
                if (response.status_code === '200') {
                    $.toaster({ priority: 'success', title: 'Otp.', message: 'Otp match successfully..' });
                } else if (response.status_code === '201') {
                    $.toaster({ priority: "warning", title: "Oops..!", message: "Please enter valid otp.", timeout: 3000 });
                    $('#scotp').val('');
                } else if (response.status_code === '202') {
                    $.toaster({ priority: "warning", title: "Oops..!", message: "Otp expired.", timeout: 3000 });
                    $('#scotp').val('');
                }
            },
            error: function () {
                $.toaster({ priority: "danger", title: "Error..!", message: "Failed to verify OTP. Please try again.", timeout: 3000 });
                $('#scotp').val('');
            }
        });
    }
</script>