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
        timePicker24Hour: false,     
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
    
      /** ==============================
     * Invoice For Radio SHow hide DIv
     *  ============================== */
    
    $('input[name="invoice_for"]').on('change', function () {
        let invoiceFor = $(this).val();

        if (invoiceFor == "1") {
            // Internal Branch
            $('#TostoreDiv').show();
            $('#TostoreDetailsDiv').show();
            
            $('#company_name').val('');
            $('#customer_details').val('');

            $('#custdDiv').hide();
            $('#custdDetailsDiv').hide();
        } else {
            // B2B Customer
            $('#TostoreDiv').hide();
            $('#TostoreDetailsDiv').hide();
            
            $('#to_store_id').val('');
            $('#to_store_details').val('');

            $('#custdDiv').show();
            $('#custdDetailsDiv').show();
        }
    });
    
     /** ==============================
     * From Store Wise Tax or order id get
     *  ============================== */
     
    $(document).on("change", "#store_id", function () 
    {
        let selectedType = $(this).val();
    
        $.ajax({
            url: "{{ route('admin.get-store-details') }}",
            type: "GET",
            data: { selectedType: selectedType },
            beforeSend: function ()
            {
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
                
                let storeDetails =
                    "Store Name : " + res.data.store_name + "\n" +
                    "GST No     : " + res.data.gst_no + "\n" +
                    "Mobile No  : " + res.data.mobile_no + "\n" +
                    "Address    : " + res.data.address + ", " +
                                  res.data.city + ", " +
                                  res.data.state + " - " +
                                  res.data.pincode;
                
                $("#from_store_details").val(storeDetails);

            } else {
                $.toaster({
                    priority: 'danger',
                    title: ' Store Not Found',
                    message: 'Please check and try again.',
                    timeout: 3000
                });
                $("#order_no").val('');
            }
        },

            
            complete: function () 
            {
                $("#ajaxLoader").fadeOut();
            }
        });
    });
    
    
    
    /** ==============================
     * To Store Wise Details  get
     *  ============================== */
     
    $(document).on("change", "#to_store_id", function () 
    {
        let selectedType = $(this).val();
    
        $.ajax({
            url: "{{ route('admin.get-store-details') }}",
            type: "GET",
            data: { selectedType: selectedType },
            beforeSend: function ()
            {
                $("#ajaxLoader").show();
            },
            success: function (res) {
            if (res.success)
            {
                let storeDetails =
                    "Store Name : " + res.data.store_name + "\n" +
                    "GST No     : " + res.data.gst_no + "\n" +
                    "Mobile No  : " + res.data.mobile_no + "\n" +
                    "Address    : " + res.data.address + ", " +
                                  res.data.city + ", " +
                                  res.data.state + " - " +
                                  res.data.pincode;
                
                $("#to_store_details").val(storeDetails);

            } else {
                $.toaster({
                    priority: 'danger',
                    title: ' Store Not Found',
                    message: 'Please check and try again.',
                    timeout: 3000
                });
            }
        },

            
            complete: function () 
            {
                $("#ajaxLoader").fadeOut();
            }
        });
    });
    
    
    
    /** =================================
     *  Select Customer Details
     * ================================== */
    $('#company_name').on('blur', function () {
    
        let company_name = $(this).val().trim();
        let tableBody = $('#customerTable tbody');
    
        tableBody.empty();
    
        if (company_name.length < 2) {
            return; // do nothing if too short
        }
        
        
    
        $.ajax({
            url: "{{ route('admin.getbbcustomer') }}",
            method: 'GET',
            data: { company_name: company_name },
            beforeSend: function ()
            {
                $("#ajaxLoader").show();
            },
            success: function (response) {
                tableBody.empty();
    
                if (response.data && response.data.length > 0) {
    
                    response.data.forEach(function (customer) {
    
                        let row = `
                            <tr>
                                <td>
                                    <input type="radio" name="customerdetails" class="cust-details"
                                        data-customer_id="${customer.customer_id}"
                                        data-cust_unique_id="${customer.cust_unique_id}"
                                        data-cust_name="${customer.cust_name}"
                                        data-email_id="${customer.email_id ?? ''}"
                                        data-gst_no="${customer.gst_no ?? ''}"
                                        data-contact_no="${customer.contact_no ?? ''}"
                                        data-cust_address="${customer.cust_address ?? ''}"
                                        data-pincode="${customer.pincode ?? ''}"
                                        data-city="${customer.city ?? ''}"
                                        data-state="${customer.state ?? ''}"
                                        data-city_id="${customer.city_id ?? ''}"
                                        data-state_id="${customer.state_id ?? ''}">
                                </td>
                                <td>${customer.cust_name}</td>
                                <td>${customer.contact_no ?? '-'}</td>
                                <td>${customer.email_id ?? '-'}</td>
                                <td>${customer.gst_no ?? '-'}</td>
                                <td>${customer.company_name ?? '-'}</td>
                                <td>
                                    ${customer.cust_address ?? '-'},
                                    ${customer.pincode ?? '-'},
                                    ${customer.city ?? '-'},
                                    ${customer.state ?? '-'} - India
                                </td>
                            </tr>
                        `;
    
                        tableBody.append(row);
                    });
    
                    $('#bbcustomerModal').modal('show');
    
                } else {
                    tableBody.append('<tr><td colspan="7" class="text-center">No customer found.</td></tr>');
                    $('#bbcustomerModal').modal('show');
                }
            },
    
            error: function () {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch customer details.',
                    timeout: 3000
                });
            },

            
            complete: function () 
            {
                $("#ajaxLoader").fadeOut();
            }
        });
    });
    
    
    $(document).on('click', '.cust-details', function()
    {
        let cust_unique_id = $(this).data('cust_unique_id');
        let cust_name = $(this).data('cust_name');
        let email_id = $(this).data('email_id');
        let gst_no = $(this).data('gst_no');
    
        let company_name = $(this).data('company_name');
        let contact_no = $(this).data('contact_no');
        let cust_address = $(this).data('cust_address');
        let pincode = $(this).data('pincode');
        
        let city_id = $(this).data('city_id');
        let state_id = $(this).data('state_id');
        let city = $(this).data('city');
        let state = $(this).data('state');
        
        
        let custDetails =
            "Customer Name : " + cust_name + "\n" +
            "GST No     : " + gst_no + "\n" +
            "Mobile No  : " + contact_no + "\n" +
            "Email  : " + email_id + "\n" +
            "Address    : " + cust_address + ", " +
                          city + ", " +
                          state + " - " +
                          pincode;
        
        $("#customer_details").val(custDetails);
    
        // Fill modal dropdowns
        $('#cust_id').val(cust_unique_id);
        $('#contact_no').val(contact_no);
        $('#cust_name').val(cust_name);

        $('#email_id').val(email_id);
        $('#cust_address').val(cust_address);
        $('#state_id').val(state_id);
        $('#city_id').val(city_id);
        $('#pincode').val(pincode);
        $('#gst_no').val(gst_no);

        $('#bbcustomerModal').modal('hide');
    });


});

</script>