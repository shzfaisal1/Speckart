<script>
var start = moment('2025-01-01'); 
var end = moment(); // Today

function isCurrentMonth(date) {
    return date.month() === moment().month() && date.year() === moment().year();
}


function cb(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    $('#date_from').val(start.format('YYYY-MM-DD'));
    $('#date_to').val(end.format('YYYY-MM-DD'));

    if (isCurrentMonth(start) || isCurrentMonth(end)) {
        console.log("Start or end date is in the current month.");
    } else {
        console.log("Neither date is in the current month.");
    }

    const column = dataListView.column(0);
    column.search(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    dataListView.draw();
}

$('#reportrange').daterangepicker({
    startDate: start,
    endDate: end,
    autoUpdateInput: false,
    showDropdowns: true,
    maxDate: moment(),
    ranges: {
        'Today': [moment(), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [
            moment().subtract(1, 'month').startOf('month'),
            moment().subtract(1, 'month').endOf('month')
        ],
        'Lifetime': [moment('2025-01-01'), moment()]
    }
}, function(start, end) {
    cb(start, end);
});

$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
    cb(picker.startDate, picker.endDate);
});

cb(start, end);
</script>


<script>
let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function() {
        $('#processingLoader').show();
    })
    .on('draw.dt', function() 
    {
      $('#processingLoader').hide();
      
    }).DataTable({

        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "ajax": {
            url: "{{ route('admin.sales-pending-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d.search1 = $('#search').val(),
                d.sale_person = $('#sale_person').val(),
                d.store_id = $('#filter_store_id').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "createdRow": function(row, data, dataIndex) {
            if (data.is_cancelled == 1) {
                $(row).addClass('row-cancelled');
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "order_details",
                orderable: false,
            },
            {
                "data": "bill_details",
                orderable: false,
            },
            {
                "data": "customer_details",
                orderable: false,
            },

            {
                "data": "invoice_details",
                orderable: false,
            },
            {
                "data": "store_name",
                orderable: false,
            },

            {
                "data": "sale_person",
                orderable: false,
            },

            {
                "data": "action",
                orderable: false,
                searchable: false
            },
        ],

        searchDelay: 1500,
        columnDefs: [{
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0
            },
            {
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function(data, type, full) 
                
                {
                    // Check if order is cancelled
                    if (full['is_cancelled'] == 1) {
                        let cancelReason = encodeURIComponent(full['cancellation_reason'] || 'No cancellation reason specified');
                        let customerName = encodeURIComponent(full['customer_name'] || '');
                        let disabledMsg = 'Action Disabled (Order Cancelled)';

                        return (`
                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img src="{{asset('assets/images/icon/sms.png')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-whatsapp.webp')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon_unblock.png')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>
                            
                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/edit.png')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-courier-no.webp')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-update-price.webp')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/receipt.webp')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/form.webp')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/print.png')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-payment-details.webp')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-update-redeem-points.png')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-discount-coupon.png')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-cart-discount.png')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/user.png')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <!-- View Product Details (ENABLED for cancelled orders) -->
                            <a class="tooltip pointer" onclick="openCancelledOrderProductsModal('${full['oid']}')">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-udpate-prescription.webp')}}">
                                <span class="tooltip-text">View Product Details</span>
                            </a>

                            <!-- View Cancellation Reason (NEW ACTION BUTTON) -->
                            <a href="javascript:void(0);" class="tooltip pointer btn-show-cancel-reason" 
                               data-oid="${full['oid']}" 
                               data-reason="${cancelReason}" 
                               data-cust="${customerName}">
                                <span class="action-icon-cancel-reason">
                                    <i class="fa fa-ban"></i>
                                </span>
                                <span class="tooltip-text">View Cancellation Reason</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon-mail-send.webp')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>

                            <a class="tooltip icon-disabled-cancelled" href="javascript:void(0);">
                                <img class="action-icon" src="{{asset('assets/images/icon/icon_block.webp')}}">
                                <span class="tooltip-text">${disabledMsg}</span>
                            </a>
                        `);
                    }

                    // Base URLs from Laravel
                    let baseUrl  = "{{ url(config('app.admin_path', 'admin').'/sale/invoice') }}";
                    let baseUrll = "{{ url(config('app.admin_path', 'admin').'/sale/edit') }}";
                
                    // Dynamic URLs
                    let invoiceUrl = baseUrl + '/' + full['encryptedId'] + '/invoice';
                    let receiptUrl = baseUrl + '/' + full['encryptedId'] + '/receipt';
                    let orderUrl   = baseUrl + '/' + full['encryptedId'] + '/order';
                    let editUrl    = baseUrll + '/' + full['encryptedId'];
                    let confirmUrl = "{{ url(config('app.admin_path', 'admin').'/sale/confirm') }}" + '/' + full['encryptedId'];
                
                    // SMS condition
                    const smsDisabledClass = full['ready_reminder_sms'] == 1 ? 'icon-dark' : '';
                    const smsTooltip = full['ready_reminder_sms'] == 1
                        ? 'Reminder Already Sent'
                        : 'Send Order Ready Reminder SMS';
                
                    return (`
                        <a class="tooltip pointer ${smsDisabledClass}"
                           onclick="openreminderModal('${full['oid']}')">
                            <img src="{{asset('assets/images/icon/sms.png')}}">
                            <span class="tooltip-text">${smsTooltip}</span>
                        </a>
                
                        <a class="tooltip pointer" onclick="openwhatsappModal('${full['oid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-whatsapp.webp')}}">
                            <span class="tooltip-text">Send WhatsApp Messages With Web</span>
                        </a>
                
                        <a href="${confirmUrl}" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon_unblock.png')}}">
                            <span class="tooltip-text">Confirm Sale</span>
                        </a>
                        
                        <a href="${editUrl}" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/edit.png')}}">
                            <span class="tooltip-text">Edit Order</span>
                        </a>
                
                        <a href="" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-courier-no.webp')}}">
                            <span class="tooltip-text">Courier Order</span>
                        </a>
                
                        <a class="tooltip pointer" onclick="openpurchasepriceModal('${full['oid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-update-price.webp')}}">
                            <span class="tooltip-text">Update Purchase Price</span>
                        </a>
                
                        <a href="${receiptUrl}" target="_blank" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/receipt.webp')}}">
                            <span class="tooltip-text">View & Print Advance Receipt</span>
                        </a>
                
                        <a href="${orderUrl}" target="_blank" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/form.webp')}}">
                            <span class="tooltip-text">View & Print Order Form</span>
                        </a>
                
                        <a href="${invoiceUrl}" target="_blank" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/print.png')}}">
                            <span class="tooltip-text">Print Receipts</span>
                        </a>
                
                        <a class="tooltip pointer" onclick="openpaymentModal('${full['oid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-payment-details.webp')}}">
                            <span class="tooltip-text">View Payment Details</span>
                        </a>
                
                        <a class="tooltip pointer"  onclick="openredeemModal('${full['oid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-update-redeem-points.png')}}">
                            <span class="tooltip-text">Redeem Loyalty Points</span>
                        </a>
                
                        <a class="tooltip pointer"  onclick="opencouponModal('${full['oid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-discount-coupon.png')}}">
                            <span class="tooltip-text">Update Discount Coupon</span>
                        </a>
                
                        <a class="tooltip pointer"  onclick="opencartModal('${full['oid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-cart-discount.png')}}">
                            <span class="tooltip-text">Update Cart Discount</span>
                        </a>
                        <a href="" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/user.png')}}">
                            <span class="tooltip-text">Update Customer Account</span>
                        </a>
                        <a class="tooltip pointer"  onclick="openprescriptionModal('${full['oid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-udpate-prescription.webp')}}">
                            <span class="tooltip-text">Update Prescription</span>
                        </a>
                
                        <a href="" class="tooltip">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon-mail-send.webp')}}">
                            <span class="tooltip-text">Send Mail of Advance Receipt PDF</span>
                        </a>
                
                        <a class="tooltip pointer"  onclick="opendeleteModal('${full['oid']}')">
                            <img class="action-icon" src="{{asset('assets/images/icon/icon_block.webp')}}">
                            <span class="tooltip-text">Delete Order</span>
                        </a>
                    `);
                }

                
            }

        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        language: {
            paginate: {

                previous: '&nbsp;',
                next: '&nbsp;'
            },
            sLengthMenu: "_MENU_",
            sZeroRecords: "{{ __('No results available') }}",
            sSearch: "{{ __('search') }}",
            sProcessing: "{{ __('processing') }}",
            sInfo: "{{ __('Showing :start to :end of :total entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}",
            sInfoFiltered: "" 
        },
        responsive: {
            details: {
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    let data = $.map(columns, function(col) {
                        return col.title !==
                            '' 
                            ?
                            '<tr data-dt-column="' +
                            col.columnIndex +
                            '">' +
                            '<td>' +
                            col.title +
                            ':' +
                            '</td> ' +
                            '<td>' +
                            col.data +
                            '</td>' +
                            '</tr>' :
                            '';
                    }).join('');

                    return data ? $('<table class="table"/>').append('<tbody>' + data +
                        '</tbody>') : false;
                }
            }
        },
        aLengthMenu: [
            [10, 20, 50, 100],
            [10, 20, 50, 100]
        ],
        select: {
            style: "multi"
        },
        order: [
            [2, "desc"]
        ],
        displayLength: 10,
    });
    
    
    
     let debounceTimer;
    $('.input').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            const column = dataListView.column($(this).attr('name'));
            column.search($(this).val()).draw();
        }.bind(this), 500);
    });
    
    
    
    $('.select').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });
    
    

    function openpaymentModal(oid) 
    {
        document.getElementById('modalTitle').innerText = 'Payment Details of Order No : '+oid;
        
         $.ajax({
            url: "{{ route('admin.getsalespayment') }}",  // Laravel route
            method: 'GET',
            data: { oid: oid },
            beforeSend: function () {
              $("#ajaxLoader").show(); 
            },
            success: function(response) {
                let tableBody = $('#salepaymentTable tbody');
                tableBody.empty(); 
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(salepayment) 
                    {
                        
    
                        let row = `
                            <tr>
                                <td>${salepayment.pay_details}</td>
                                <td>${salepayment.pay_method}</td>
                                <td></td>
                                <td>${salepayment.pay_amount}</td>
                                <td>${salepayment.pay_date}</td>
                                <td>${salepayment.created_by}</td>
                                <td>${salepayment.created_at}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="7" class="text-center">No Payment found.</td></tr>');
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch payment details.',
                    timeout: 3000
                });
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        });
        
        
         $.ajax({
            url: "{{ route('admin.getreturnpayment') }}",  // Laravel route
            method: 'GET',
            data: { oid: oid },
            beforeSend: function () {
              $("#ajaxLoader").show(); 
            },
            success: function(response) {
                let tableBody = $('#returnpaymentTable tbody');
                tableBody.empty(); 
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(salepayment) 
                    {
                        
    
                        let row = `
                            <tr>
                                <td>${salepayment.pay_details}</td>
                                <td>${salepayment.pay_method}</td>
                                <td></td>
                                <td>${salepayment.pay_amount}</td>
                                <td>${salepayment.pay_date}</td>
                                <td>${salepayment.created_by}</td>
                                <td>${salepayment.created_at}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="7" class="text-center">No Payment found.</td></tr>');
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch payment details.',
                    timeout: 3000
                });
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        });
        $('#PaymentModal').modal('show');
    }
    
    
    function openpurchasepriceModal(oid) 
    {
        document.getElementById('modalTitlep').innerText = 'Purchase Prices of Order No  : '+oid;
        document.getElementById('oid').value = oid;
        $.ajax({
            url: "{{ route('admin.getsalesproduct') }}",  // Laravel route
            method: 'GET',
            data: { oid: oid },
            beforeSend: function () {
              $("#ajaxLoader").show(); 
            },
            success: function(response) {
                let tableBody = $('#purchaseTable tbody');
                tableBody.empty(); 
    
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(saleproduct, index) {
                    let row = `
                        <tr>
                            <td>${index + 1}</td>  <!-- SR No -->
                            <td>${saleproduct.product_code}</td>
                            <td>${saleproduct.product_type}</td>
                            <td>${saleproduct.product_deatils}
                            ${saleproduct.qty == 2 ? ' <span class="badge badge-danger">Pair</span>' : ''}</td>
                            <td>${saleproduct.purchase_price}</td>
                            <td>${saleproduct.qty}</td>
                            <td>
                                <input class="form-control" 
                                       placeholder="Enter New Price" 
                                       name="new_purchase_price[]">
                                <input type="hidden" 
                                       value="${saleproduct.pid}" 
                                       name="pid[]">
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
                } 
                else
                {
                    tableBody.append('<tr><td colspan="7" class="text-center">No Payment found.</td></tr>');
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch payment details.',
                    timeout: 3000
                });
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        });
        $('#PurchaseModal').modal('show');
    }
    
    function openreminderModal(oid) {
        document.getElementById('modalTitless').innerText =
            'Order Ready Reminder message : ' + oid;
    
        $('#sms_status_response').html('');
    
        $.ajax({
            url: "{{ route('admin.checkremindersms') }}",
            method: 'GET',
            data: { oid: oid },
            beforeSend: function () {
              $("#ajaxLoader").show(); 
            },
            success: function (response) {
    
                let html = '';
    
                response.messages.forEach(function (msg) {
                    if (msg.type === 'success') {
                        html += `
                            <div class="alert alert-success mb-1">
                                ${msg.text}
                            </div>`;
                    } else {
                        html += `
                            <div class="alert alert-danger mb-1">
                                ${msg.text}
                            </div>`;
                    }
                });
    
                $('#sms_status_response').html(html);
            },
            error: function () {
                $('#sms_status_response').html(
                    '<div class="alert alert-danger">Something went wrong.</div>'
                );
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        });
    
        $('#readyreminderModal').modal('show');
    }
    
    
    function openwhatsappModal(oid)
    {
        document.getElementById('modalTitleWhatsapp').innerText ='SEND WHATSAPP MESSAGE WITH WEB FOR ORDER NUMBER : ' + oid;
    
        $.ajax({
            url: "{{ route('admin.getallwhatsapptamplete') }}",
            method: 'GET',
            data: { oid: oid },
            beforeSend: function () {
              $("#ajaxLoader").show(); 
            },
            success: function(response) {
    
                let tableBody = $('#whatsappTable tbody');
                tableBody.empty();
    
                if (response.data && response.data.length > 0) {
    
                    response.data.forEach(function(whatsapp, index) {
    
                        let row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${whatsapp.title}</td>
                                <td>${whatsapp.pay_method}</td>
                                <td>
                                    <a class="pointer" onclick="sendWhatsapp('${whatsapp.orderid},${whatsapp.title}')">
                                    <img class="action-icon" src="{{asset('assets/images/icon/icon-whatsapp.webp')}}">
                                    </a>
                                   
                                </td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
    
                } else {
                    tableBody.append(
                        '<tr><td colspan="4" class="text-center">No Whatsapp found.</td></tr>'
                    );
                }
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch whatsapp details.',
                    timeout: 3000
                });
            },
            complete: function () {
                $("#ajaxLoader").fadeOut(); 
            }
        });
    
        $('#whatsappModal').modal('show');
    }
    
    
    function sendWhatsapp(oid,title) 
    {
        if (oid == '') 
    	{
    	    $.toaster({
    		  priority: "danger",
    		  title: "Error..!",
    		  message: "Order no not found. Please try again.",
    		  timeout: 3000
    		});
    	}
    	else
    	{
    	    $.ajax({
    			url: "{{ route('admin.sendmessageonwhtasapp') }}",
    			method: 'GET',
    			data: { oid: oid,title: title },
    			beforeSend: function () {
                  $("#ajaxLoader").show(); 
                },
    			success: function (response)
    			{
                  if (response.status_code === '200')
                  {
                      $.toaster({
                        priority: "warning",
                        title: "Oops..!",
                        message: response.msg,
                            timeout: 3000
                      });
                      
                  }
                  else if (response.status_code === '201')
                  {
                      $.toaster({
                        priority: "success",
                        title: "Success..!",
                        message: response.msg,
                            timeout: 3000
                      });
                  }
                  else if (response.status_code === '202')
                  {
                      $.toaster({
                        priority: "danger",
                        title: "Error..!",
                        message: response.msg,
                            timeout: 3000
                      });
                  }
                  
                  $('#whatsappModal').modal('hide');
    				
    			},
    			error: function () {
    				$.toaster({
                        priority: "warning",
                        title: "Oops..!",
                        message: "Something went wrong!",
                         timeout: 3000
                      });
    			},
                complete: function () {
                    $("#ajaxLoader").fadeOut(); 
                }
    		});
    	}
    		
    }
    
    
   function openredeemModal(oid) {

        $('#modalTitleRedeem').text('Redeem Loyalty Points Of Order No : ' + oid);
    
        $.ajax({
            url: "{{ route('admin.applyredeempoint') }}",
            type: "GET",
            data: { oid: oid },
            beforeSend: () => $("#ajaxLoader").show(),
    
            success: function (res) {
    
                if (res.status !== 'success') return;
    
                
                $('#availablePoints').val(res.points);
                $('#payableAmount').text(res.pending_amount);
                $('#contact_no').val(res.contact_no);
                $('#orderon').val(res.order_no);
    
                // Reset
                $('#redeemPoints, #redeemPointsAmount').val('');
                $('#otpWrapper').hide();
    
                // CONDITIONS
                if (res.can_redeem) {
                    $('#pointsMessage').html(
                        `<p class="text-success">
                            You have ${res.points} loyalty points available.
                         </p>`
                    );
    
                    $('#redeemPoints').prop('disabled', false);
                    $('#confirmRedeem').prop('disabled', false);
                    $('#otpWrapper').show();
    
                } else {
                    $('#pointsMessage').html(
                        `<p class="text-danger">
                            You cannot redeem loyalty points.
                         </p>`
                    );
    
                    $('#redeemPoints').prop('disabled', true);
                    $('#confirmRedeem').prop('disabled', true);
                }
    
                $('#RedeemModal').modal('show');
            },
    
            complete: () => $("#ajaxLoader").fadeOut()
        });
    }

    
    
     /* -------------------------
       Redeem Point Apply
    ------------------------- */

    
    const pointValue = 1; // 1 point 

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
        let redeemPoints = $('#redeemPoints').val().trim();
        let orderon = $('#orderon').val().trim();
        let rotp = $('#rotp').val().trim();
        let contact_no = $('#contact_no').val().trim();
    
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
          url: "{{ route('admin.updateredeempoint') }}",
          data: {
            rotp: rotp, 
            redeemPointsAmount: redeemPointsAmount, 
            redeemPoints: redeemPoints,
            orderon: orderon, 
            contact_no: contact_no,
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
                  
                  $.toaster({
                    priority: "success",
                    title: "Success..!",
                    message: "Redeem point apply successfully.",
                        timeout: 3000
                  });
                  
                  $('#RedeemModal').modal('hide');

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
    
    
    function opencouponModal(oid) {
        $('#modalTitleCoupon').text('Update Discount Coupon Of Order No: ' + oid);
    
        $.ajax({
            url: "{{ route('admin.checkcouponapplyornot') }}",
            type: "GET",
            data: { oid: oid },
            beforeSend: () => $("#ajaxLoader").show(),
    
            success: function (res) {
                if (res.status !== 'success') {
                    $.toaster({ 
                        priority: 'danger', 
                        title: 'Error', 
                        message: 'Failed to load coupon details', 
                        timeout: 3000 
                    });
                    return;
                }
    
                
                $('#pending_amount').text(res.pending_amount);
                $('#payableAmount').val(res.total_payable);
                $('#contact_no').val(res.contact_no);
                $('#orderon').val(res.order_no);
                $('#oldcoupon').html(res.oldcoupon); 
                
                if (res.canCoupon) 
                {
                    $('#confirmCoupon').prop('disabled', false);
                }
                else
                {
                    $('#confirmCoupon').prop('disabled', true);
                }
    
                $('#CouponModal').modal('show');
            },
    
            error: function () {
                $.toaster({ 
                    priority: 'danger', 
                    title: 'Error', 
                    message: 'Something went wrong while fetching coupon details', 
                    timeout: 3000 
                });
            },
    
            complete: () => $("#ajaxLoader").fadeOut()
        });
    }
    
    $('#DiscountCoupon').on('input', function ()
    {
        let DiscountCoupon = $('#DiscountCoupon').val().trim();
        let contact_no = $('#contact_no').val().trim();
        let total_item_price = $('#payableAmount').val().trim();
    
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
                    $('#coupondiscountAmount').val(response.discount_amount);
                    $('#coupon_id').val(response.coupon_id);
               
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
    
    
    $('#confirmCoupon').on('click', function() 
    {
        let DiscountCoupon = $('#DiscountCoupon').val().trim();
        let contact_no = $('#contact_no').val().trim();
        let orderon = $('#orderon').val().trim();
        let coupon_id = $('#coupon_id').val().trim();

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
            url: "{{ route('admin.couponupdateonorder') }}",
            data: {
                DiscountCoupon: DiscountCoupon,
                contact_no: contact_no,
                orderon: orderon,
                coupon_id: coupon_id,
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(response) {
                if (response.status_code === '200') 
                {
                    $('#CouponModal').modal('hide');
                    $.toaster({
                        priority: "success",
                        title: "Success..!",
                        message: "Coupon applied successfully.",
                        timeout: 3000
                    });
                }
                else if (response.status_code === '201') 
                {
                    $.toaster({
                        priority: "error",
                        title: "Error..!",
                        message: "Coupon code required.",
                        timeout: 3000
                    });
                }
                else if (response.status_code === '202') 
                {
                    $.toaster({
                        priority: "error",
                        title: "Error..!",
                        message: response.message,
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
    
    
    
    function opencartModal(oid) {
        $('#modalTitleCart').text('Update Cart Discount Of Order No: ' + oid);
    
        $.ajax({
            url: "{{ route('admin.checkcartapplyornot') }}",
            type: "POST",
            data: {
                oid: oid,
                _token: "{{ csrf_token() }}"
            },
            beforeSend: () => $("#ajaxLoader").show(),
    
            success: function (res) {
                if (res.status !== 'success') {
                    $.toaster({
                        priority: 'danger',
                        title: 'Error',
                        message: 'Failed to load cart details',
                        timeout: 3000
                    });
                    return;
                }
    
                $('#pending-amount').text(res.pending_amount);
                $('#payable-Amount').text(res.total_payable);
                $('#contact-no').val(res.contact_no);
                $('#order-no').val(res.order_no);
                $('#oldcart').html(res.oldcart || '');
                $('#total-payable').val(res.total_payable);
                $('#CartModal').modal('show');
            },
    
            error: function () {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Server error while fetching cart details',
                    timeout: 3000
                });
            },
    
            complete: () => $("#ajaxLoader").fadeOut()
        });
    }


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
        let total_item_price = parseFloat($('#total-payable').val() || 0);
        
    
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
        let total_item_price = parseFloat($('#total-payable').val() || 0);
    
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
    
    function parseNum(val) {
        val = parseFloat(val);
        return isNaN(val) ? 0 : val;
    }
    
    /*****===========================
     * Cart Discount OTP
     * ================================*/
     
    $('#sendOtpcart').on('click', function() 
    {
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
    

    $('#confirmCart').on('click', function() 
    {
        let selectedUser = $('input[name="selected_user"]:checked').val();
        let discountAmount = parseFloat($('#modalCartDiscountAmount').val().trim()) || 0;
        let discountPercent = parseFloat($('#modalCartDiscountPercentage').val().trim()) || 0;
        let reason = $('#modalCartDiscountOTPReason').val().trim();
        let approvedLimit = parseFloat($('#approvedDiscountLimit').val().trim()) || 0;
        let cotp = $('#cotp').val().trim();
        let orderno = $('#order-no').val().trim();
        
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
          url: "{{ route('admin.updatecartdiscount') }}",
          data: {
            selectedUser: selectedUser,
            discountAmount: discountAmount,
            discountPercent: discountPercent,
            reason: reason,
            orderno: orderno,
            cotp: cotp, 
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
                $('#CartModal').modal('hide');
            
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
            else if (response.status_code === '203') {
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: response.message ,
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
    

    function opendeleteModal(oid) 
    {
        $('#modalTitleDelete').text('Delete Order No: ' + oid);
        
        $('#orderid').val(oid);
    
        $('#DeleteModal').modal('show');
    }
    
    
    /*****===========================
     * Delete Ordder OTP
     * ================================*/
     
    $('#sendOtpdeleteorder').on('click', function() 
    {
        let delete_contactno = $('#delete_contactno').val().trim();
            
        $.ajax({
              type: "POST",
              url: "{{ route('admin.deleteOtp') }}",
              data: {
                delete_contactno: delete_contactno, 
                _token: "{{ csrf_token() }}"
              },
              dataType: "json",
              success: function (response) {
                if (response.status_code === '200')
                {
                    document.getElementById('sendOtpdeleteorder').style.display = 'none'; 
                  showOTPSectionDelete();    
                  $.toaster({
                    priority: "success",
                    title: "Success..!",
                    message: "OTP sent to your mobile number.",
                    timeout: 3000
                  });
                }
                
                else if (response.status_code === '201') {
                  document.getElementById('otp-delete-section').style.display = 'none';    
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Something went wrong!",
                     timeout: 3000
                  });
                }
                else if (response.status_code === '202') {
                  document.getElementById('otp-delete-section').style.display = 'none';  
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Mobile No already registered.",
                     timeout: 3000
                  });
                }
              },
              error: function () {
                document.getElementById('otp-delete-section').style.display = 'none';    
                $.toaster({
                  priority: "danger",
                  title: "Error..!",
                  message: "Failed to send OTP. Please try again.",
                  timeout: 3000
                });
              }
            });
         
    
    });
    
    let countdownIntervaldelete;
    function showOTPSectionDelete() {
      document.getElementById('otp-delete-section').style.display = 'block';
      document.getElementById('resend-btn-delete').disabled = true;
      startCountdowndelete(30); // Start timer with 60 seconds
      // Optionally: trigger actual OTP send via AJAX
    }
    
    function startCountdowndelete(seconds) {
      clearInterval(countdownIntervaldelete);
      let timeLeft = seconds;
    
      const countdownEl = document.getElementById('countdowndelete');
      const timerEl = document.getElementById('timerdelete');
      const resendBtn = document.getElementById('resend-btn-delete');
    
      if (!countdownEl || !timerEl || !resendBtn) return; 
    
      countdownEl.textContent = timeLeft;
    
      countdownIntervaldelete = setInterval(() => {
        timeLeft--;
        countdownEl.textContent = timeLeft;
    
        if (timeLeft <= 0) {
          clearInterval(countdownIntervaldelete);
          resendBtn.disabled = false;
          timerEl.textContent = "Didn't get the OTP?";
        }
      }, 1000);
    }
    
    
    function resendOTPdelete() 
    {
          // Resend OTP logic (e.g., via AJAX)
          document.getElementById('resend-btn-cart').disabled = true;
          document.getElementById('timerdelete').innerHTML = 'Resend OTP in <span id="countdowndelete">60</span>s';
          startCountdowndelete(30);
          let delete_contactno = $('#delete_contactno').val().trim();
          
          $.ajax({
              type: "POST",
              url: "{{ route('admin.deleteOtp') }}",
              data: {
                delete_contactno: delete_contactno, 
                _token: "{{ csrf_token() }}"
              },
              dataType: "json",
              success: function (response) {
                if (response.status_code === '200')
                {
                  showOTPSectionDelete();    
                  $.toaster({
                    priority: "success",
                    title: "Success..!",
                    message: "OTP sent to your mobile number.",
                        timeout: 3000
                  });
                } else {
                  document.getElementById('otp-delete-section').style.display = 'none';    
                  $.toaster({
                    priority: "warning",
                    title: "Oops..!",
                    message: "Something went wrong!",
                        timeout: 3000
                  });
                }
              },
              error: function () {
                document.getElementById('otp-delete-section').style.display = 'none';    
                $.toaster({
                  priority: "danger",
                  title: "Error..!",
                  message: "Failed to send OTP. Please try again.",
                        timeout: 3000
                });
              }
            });
    }
    
    
    
    $('#confirmDelete').on('click', function() 
    {

        let dotp = $('#dotp').val().trim();
        let deletordercomment = $('#deletordercomment').val().trim();
        let keepPaymentRecords = $('#keepPaymentRecords').val();
        let orderid = $('#orderid').val().trim();
        

        $.ajax({
          type: "POST",
          url: "{{ route('admin.orderdelete') }}",
          data: {
            dotp: dotp,
            deletordercomment: deletordercomment,
            keepPaymentRecords: keepPaymentRecords,
            orderid: orderid,
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (response) {
            if (response.status_code === '200')
            {
                
            
                $.toaster({
                    priority: 'success',
                    title: ' Discount Applied',
                    message: 'Order Delete successfully.'
                });
                
                window.location.href = "{{ route('admin.sale-pending-history') }}";
              
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
            else if (response.status_code === '203') {
              $.toaster({
                priority: "warning",
                title: "Oops..!",
                message: response.message ,
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
    
    
    function openprescriptionModal(oid, isReadOnly = false) {
        if (isReadOnly) {
            $('#modalTitlePrescription').html('<i class="fa fa-eye text-primary mr-1"></i> Product Details - Order No: #' + oid + ' <span class="badge badge-danger ml-2" style="background-color: #dc2626; color: #fff; font-size: 12px; padding: 3px 8px;"><i class="fa fa-ban mr-1"></i> CANCELLED (Read Only)</span>');
            $('#btnUpdatePrescriptionSubmit').hide();
            $('#prescriptionImportantNote').hide();
        } else {
            $('#modalTitlePrescription').text('Update Prescription Order No: ' + oid);
            $('#btnUpdatePrescriptionSubmit').show();
            $('#prescriptionImportantNote').show();
        }
    
        // Clear previous prescriptions
        $('#Prescriptionglassdiv').empty();
    
        $.ajax({
            type: "GET",
            url: "{{ route('admin.getorderprescription') }}",
            data: { oid: oid },
            dataType: "json",
            beforeSend: function () {
                $("#ajaxLoader").show(); 
            },
            success: function(response) {
                if (!response.data || response.data.length === 0) {
                    if (isReadOnly) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Product Details',
                            text: 'No product details found for Order #' + oid,
                            confirmButtonColor: '#07484A'
                        });
                    } else {
                        alert('Prescription not available for this order');
                    }
                    return;
                }
    
                // Loop through all prescriptions
                response.data.forEach(function(p, index) {
    
                    // Full HTML template for a single prescription
                    let wearingTypes = p.wearing_type
                        ? (Array.isArray(p.wearing_type)
                            ? p.wearing_type
                            : String(p.wearing_type).split(','))
                        : [];
                
                    let rightChecked = false;
                    let leftChecked = false;
                    
                    let rightLeft = String(p.right_left || '').split(',').map(x => x.trim());
                    
                    if (p.qty == 2) {
                        rightChecked = true;
                        leftChecked = true;
                    } else {
                        rightChecked = rightLeft.includes('Right');
                        leftChecked = rightLeft.includes('Left');
                    }
                    const prescriptionBaseUrl = "{{ url('/public') }}/";
                    let rxImageUrl = p.prescription_file_url  ? prescriptionBaseUrl + p.prescription_file_url.replace(/^\/+/, '') : '';
                    let html = `
                    <div class="prescription-card">
                
                        <!-- HEADER -->
                        <div class="prescription-card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4><i class="fa fa-eye me-2"></i> Prescription ${index + 1}</h4>
                                <h4>Product type : <span class="ptype">${p.product_type || ''}</span></h5>
                                <h4>Description  : <span class="pdescription">${p.product_deatils || ''}</span></h5>
                                <span class="badge bg-secondary">
                                    ${p.product_type || 'Eyewear'}
                                </span>
                            </div>
                        </div>
                        <div class="prescription-card-body">
                            <div class="row g-3 mb-4">
                                <!-- PRESCRIPTION IMAGE -->
                                ${rxImageUrl ? `
                                <div class="col-lg-4">
                                    <div class="parameter-box prescription-image-viewer-wrap">
                                        <h6 class="mb-3"><i class="fa fa-image me-1"></i>Uploaded Prescription</h6>
                                        <div class="rx-image-toolbar mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary rx-zoom-in" title="Zoom In"> <i class="fa fa-search-plus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-primary rx-zoom-out" title="Zoom Out"><i class="fa fa-search-minus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-primary rx-rotate" title="Rotate"> <i class="fa fa-repeat"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-primary rx-reset" title="Reset"><i class="fa fa-refresh"></i></button>
                                            <a href="${rxImageUrl}" download target="_blank" class="btn btn-sm btn-outline-primary" title="Download">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </div>
                                        <div class="rx-image-container">
                                            <img src="${rxImageUrl}" class="rx-prescription-img" data-zoom="1" data-rotate="0" style="max-width:100%;">
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                
                                <!-- EYE POWERS -->
                                <div class="${p.prescription_file_url ? 'col-lg-8' : 'col-lg-12'}">
                                    <div class="row g-3">
                                        <!-- RIGHT EYE -->
                                        <div class="col-xl-6">
                                            <div class="eye-card">
                                                <div class="eye-card-header">
                                                    RIGHT EYE (OD) <i class="fa fa-clone copy-right-to-left" title="Copy To Left"></i>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="eye-table">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>SPH</th>
                                                                <th>CYL</th>
                                                                <th>AXIS</th>
                                                                <th><span class="mandatory">*</span>PD</th>
                                                                <th>VA</th>
                                                                <th class="hide-prism">PRISM</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Distance</td>
                                                                <td><input type="text" name="GL_EYE_RS_D_${index}" value="${p.GL_EYE_RS_D || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_RC_D_${index}" value="${p.GL_EYE_RC_D || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_RA_D_${index}" value="${p.GL_EYE_RA_D || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_RP_D_${index}" value="${p.GL_EYE_RP_D || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_RV_D_${index}" value="${p.GL_EYE_RV_D || ''}"></td>
                                                                <td class="hide-prism"><input type="text" name="GL_EYE_RPRISM_D_${index}" value="${p.GL_EYE_RPRISM_D || ''}"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Near</td>
                                                                <td><input type="text" name="GL_EYE_RS_N_${index}" value="${p.GL_EYE_RS_N || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_RC_N_${index}" value="${p.GL_EYE_RC_N || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_RA_N_${index}" value="${p.GL_EYE_RA_N || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_RP_N_${index}" value="${p.GL_EYE_RP_N || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_RV_N_${index}" value="${p.GL_EYE_RV_N || ''}"></td>
                                                                <td class="hide-prism"><input type="text" name="GL_EYE_RPRISM_N_${index}" value="${p.GL_EYE_RPRISM_N || ''}"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>ADD</td>
                                                                <td colspan="6"><input type="text" name="GL_EYE_RADD_${index}" value="${p.GL_EYE_RADD || ''}"></td>
                                                            </tr>
                                                            <tr class="hide-total-pd">
                                                                <td>Total PD</td>
                                                                <td colspan="6"><input type="text" name="GL_EYE_totalPD_${index}" value="${p.GL_EYE_totalPD || ''}"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                
                                        <!-- LEFT EYE -->
                                        <div class="col-xl-6">
                                            <div class="eye-card">
                                                <div class="eye-card-header">
                                                    <i class="fa fa-clone copy-left-to-right"  title="Copy To Right"></i> LEFT EYE (OS)
                                                </div>
                                                <div class="table-responsive">
                                                <table class="eye-table">
                                                    <thead>
                                                        <tr>
                                                            <th>SPH</th>
                                                            <th>CYL</th>
                                                            <th>AXIS</th>
                                                            <th><span class="mandatory">*</span>PD</th>
                                                            <th>VA</th>
                                                            <th class="hide-prism">PRISM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                            <tr>
                                                                <td><input type="text" name="GL_EYE_LS_D_${index}" value="${p.GL_EYE_LS_D || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_LC_D_${index}" value="${p.GL_EYE_LC_D || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_LA_D_${index}" value="${p.GL_EYE_LA_D || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_LP_D_${index}" value="${p.GL_EYE_LP_D || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_LV_D_${index}" value="${p.GL_EYE_LV_D || ''}"></td>
                                                                <td class="hide-prism"><input type="text" name="GL_EYE_LPRISM_D_${index}" value="${p.GL_EYE_LPRISM_D || ''}"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><input type="text" name="GL_EYE_LS_N_${index}" value="${p.GL_EYE_LS_N || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_LC_N_${index}" value="${p.GL_EYE_LC_N || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_LA_N_${index}" value="${p.GL_EYE_LA_N || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_LP_N_${index}" value="${p.GL_EYE_LP_N || ''}"></td>
                                                                <td><input type="text" name="GL_EYE_LV_N_${index}" value="${p.GL_EYE_LV_N || ''}"></td>
                                                                <td class="hide-prism"><input type="text" name="GL_EYE_LPRISM_N_${index}" value="${p.GL_EYE_LPRISM_N || ''}"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="6"><input type="text" name="GL_EYE_LADD_${index}" value="${p.GL_EYE_LADD || ''}"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="6"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                
                            <!-- WEARING PARAMETERS -->
                            <div class="section-title">
                                <i class="fa fa-sliders me-1"></i>  Wearing Parameters
                            </div>
                
                            <div class="parameter-box mb-4">
                                <div class="row g-3">
                                <div class="mb-3 col-md-4 lens-types">
                                    <label class="form-label-custom">Frame Type</label>
                                    <div>
                                        <label class="form-check form-check-inline">
                                            <input class="form-check-input frametype-input" type="radio" name="frametypeglass_${index}" value="Full frame" ${p.frametypeglass === 'Full frame' ? 'checked' : ''}>
                                            <span class="form-check-label"> Full Frame  </span>
                                        </label>
                                        <label class="form-check form-check-inline">
                                            <input class="form-check-input frametype-input" type="radio" name="frametypeglass_${index}" value="Half frame" ${p.frametypeglass === 'Half frame' ? 'checked' : ''}>
                                            <span class="form-check-label"> Half Frame </span>
                                        </label>
                                        <label class="form-check form-check-inline">
                                            <input class="form-check-input frametype-input" type="radio"  name="frametypeglass_${index}"  value="Rimless frame"   ${p.frametypeglass === 'Rimless frame' ? 'checked' : ''}>
                                            <span class="form-check-label">Rimless Frame </span>
                                        </label>
                
                                    </div>
                
                                </div>
                                <div class="mb-3 col-md-3 lens-types">
                                        <label class="form-label-custom mb-0">
                                            Count In Eye Testing Records?
                                        </label>
                                        <div>
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input"  type="radio" name="count_eye_test_${index}" value="1"${p.count_eye_test == 1 ? 'checked' : ''}>
                                                <span class="form-check-label"> Yes  </span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="count_eye_test_${index}" value="0" ${p.count_eye_test == 0 ? 'checked' : ''}>
                                                <span class="form-check-label"> No</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label-custom"> Fitting Height</label>
                                        <input type="text" class="form-control frame-fh" name="frame_fh_${index}" value="${p.frame_fh || ''}">
                                    </div>
                                    <div class="col-md-2 framesizea">
                                        <label class="form-label-custom">A Size</label>
                                        <input type="text" class="form-control frame-asize" name="frame_asize_${index}" value="${p.frame_asize || ''}">
                                    </div>
                                    <div class="col-md-2 framesizeb">
                                        <label class="form-label-custom">B Size</label>
                                        <input type="text" class="form-control frame-bsize" name="frame_bsize_${index}" value="${p.frame_bsize || ''}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label-custom">DBL</label>
                                        <input type="text" class="form-control frame-dbl" name="frame_dbl_${index}" value="${p.frame_dbl || ''}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label-custom">ED</label>
                                        <input type="text" class="form-control frame-ed" name="frame_ed_${index}" value="${p.frame_ed || ''}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label-custom">Eye</label>
                                        <div class="pt-1">
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="modal_rightleft_${index}[]" value="Right" ${rightChecked ? 'checked' : ''}>
                                                <span class="form-check-label">Right</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="modal_rightleft_${index}[]" value="Left" ${leftChecked ? 'checked' : ''}>
                                                <span class="form-check-label">Left</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- PATIENT INFORMATION -->
                            <div class="section-title"><i class="fa fa-user me-1"></i>Patient & Doctor Information</div>
                
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label-custom">Patient Name</label>
                                    <input type="text" class="form-control" name="patient_name_${index}" value="${p.patient_name || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Doctor / Optometrist Name</label>
                                    <input type="text" class="form-control" name="doc_name_${index}" value="${p.doc_name || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Prescription Notes</label>
                                    <input type="text" class="form-control" name="prescription_notes_${index}" value="${p.prescription_notes || ''}">
                                </div>
                            </div>
                            <!-- LENS TYPE -->
                            <div class="section-title"><i class="fa fa-cogs me-1"></i>Lens Type</div>
                            <div class="lens-types mb-4">
                                ${
                                    [
                                        'Constant Use',
                                        'Reading Wear',
                                        'Distance Wear',
                                        'Single Vision',
                                        'Progressive',
                                        'Bifocal',
                                        'Trifocal'
                                    ].map(type => `
                                        <label class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="glassWearingType_${index}[]" value="${type}" ${wearingTypes.includes(type) ? 'checked' : ''}>
                                            <span class="form-check-label">${type}</span>
                                        </label>
                
                                    `).join('')
                                }
                            </div>
                        </div>
                        <!-- FOOTER -->
                        <div class="prescription-footer">
                            <input type="hidden" name="pid_${index}" value="${p.id || ''}">
                            <small class="text-muted"> Prescription ID:  <strong>${p.id || 'New'}</strong>  </small>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" value="${p.id || ''}" name="pid">
                    `;
    
                    // Append prescription HTML
                    $('#Prescriptionglassdiv').append(html);
                    
                    $(`input[name="GL_EYE_RP_D_${index}"], input[name="GL_EYE_LP_D_${index}"]`)
                        .off('input.totalpd change.totalpd')
                        .on('input.totalpd change.totalpd', function () {
                            calculateTotalPD(index);
                        });
                    
                    calculateTotalPD(index);
                });
    
                if (isReadOnly) {
                    $('#Prescriptionglassdiv input, #Prescriptionglassdiv select, #Prescriptionglassdiv textarea').prop('disabled', true);
                    $('.copy-right-to-left, .copy-left-to-right').hide();
                } else {
                    $('#Prescriptionglassdiv input, #Prescriptionglassdiv select, #Prescriptionglassdiv textarea').prop('disabled', false);
                    $('.copy-right-to-left, .copy-left-to-right').show();
                }

                $('#PrescriptionModal').modal('show');
            },
            error: function() {
                alert('Something went wrong. Please try again.');
            },
            complete: function() {
                $("#ajaxLoader").fadeOut(); 
            }
        });
    }
    
    function calculateTotalPD(index) {
        const rightPD = parseFloat($(`input[name="GL_EYE_RP_D_${index}"]`).val()) || 0;
        const leftPD  = parseFloat($(`input[name="GL_EYE_LP_D_${index}"]`).val()) || 0;
    
        const total = rightPD + leftPD;
    
        // Only update if at least one value is entered
        if (rightPD || leftPD) {
            $(`input[name="GL_EYE_totalPD_${index}"]`).val(total.toFixed(1).replace(/\.0$/, ''));
        } else {
            $(`input[name="GL_EYE_totalPD_${index}"]`).val('');
        }
    }
    
    
    // Copy Right Eye → Left Eye
    $(document).on('click', '.copy-right-to-left', function () {
        const $card = $(this).closest('.prescription-card');
        const index = $card.find('input[name^="GL_EYE_RS_D_"]').attr('name').split('_').pop();
    
        // Distance
        $card.find(`input[name="GL_EYE_LS_D_${index}"]`).val($card.find(`input[name="GL_EYE_RS_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LC_D_${index}"]`).val($card.find(`input[name="GL_EYE_RC_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LA_D_${index}"]`).val($card.find(`input[name="GL_EYE_RA_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LP_D_${index}"]`).val($card.find(`input[name="GL_EYE_RP_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LV_D_${index}"]`).val($card.find(`input[name="GL_EYE_RV_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LPRISM_D_${index}"]`).val($card.find(`input[name="GL_EYE_RPRISM_D_${index}"]`).val());
    
        // Near
        $card.find(`input[name="GL_EYE_LS_N_${index}"]`).val($card.find(`input[name="GL_EYE_RS_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LC_N_${index}"]`).val($card.find(`input[name="GL_EYE_RC_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LA_N_${index}"]`).val($card.find(`input[name="GL_EYE_RA_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LP_N_${index}"]`).val($card.find(`input[name="GL_EYE_RP_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LV_N_${index}"]`).val($card.find(`input[name="GL_EYE_RV_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_LPRISM_N_${index}"]`).val($card.find(`input[name="GL_EYE_RPRISM_N_${index}"]`).val());
    
        // ADD
        $card.find(`input[name="GL_EYE_LADD_${index}"]`).val($card.find(`input[name="GL_EYE_RADD_${index}"]`).val());
    
        // Recalculate Total PD after copy
        if (typeof calculateTotalPD === 'function') {
            calculateTotalPD(index);
        }
    
    });
    
    // Copy Left Eye → Right Eye
    $(document).on('click', '.copy-left-to-right', function () {
        const $card = $(this).closest('.prescription-card');
        const index = $card.find('input[name^="GL_EYE_LS_D_"]').attr('name').split('_').pop();
    
        // Distance
        $card.find(`input[name="GL_EYE_RS_D_${index}"]`).val($card.find(`input[name="GL_EYE_LS_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RC_D_${index}"]`).val($card.find(`input[name="GL_EYE_LC_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RA_D_${index}"]`).val($card.find(`input[name="GL_EYE_LA_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RP_D_${index}"]`).val($card.find(`input[name="GL_EYE_LP_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RV_D_${index}"]`).val($card.find(`input[name="GL_EYE_LV_D_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RPRISM_D_${index}"]`).val($card.find(`input[name="GL_EYE_LPRISM_D_${index}"]`).val());
    
        // Near
        $card.find(`input[name="GL_EYE_RS_N_${index}"]`).val($card.find(`input[name="GL_EYE_LS_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RC_N_${index}"]`).val($card.find(`input[name="GL_EYE_LC_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RA_N_${index}"]`).val($card.find(`input[name="GL_EYE_LA_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RP_N_${index}"]`).val($card.find(`input[name="GL_EYE_LP_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RV_N_${index}"]`).val($card.find(`input[name="GL_EYE_LV_N_${index}"]`).val());
        $card.find(`input[name="GL_EYE_RPRISM_N_${index}"]`).val($card.find(`input[name="GL_EYE_LPRISM_N_${index}"]`).val());
    
        // ADD
        $card.find(`input[name="GL_EYE_RADD_${index}"]`).val($card.find(`input[name="GL_EYE_LADD_${index}"]`).val());
    
        // Recalculate Total PD after copy
        if (typeof calculateTotalPD === 'function') {
            calculateTotalPD(index);
        }
    
       
    });
        /* -------------------------
           Prescription Image Viewer
           (Zoom, Rotate, Download)
        ------------------------- */
        function applyRxImageTransform($img) {
            let zoom = parseFloat($img.data('zoom')) || 1;
            let rotate = parseFloat($img.data('rotate')) || 0;
            $img.css('transform', `scale(${zoom}) rotate(${rotate}deg)`);
            $img.css('cursor', zoom >= 3 ? 'zoom-out' : 'zoom-in');
        }
        
        $(document).on('click', '.rx-zoom-in', function () {
            let $img = $(this).closest('.prescription-image-viewer-wrap').find('.rx-prescription-img');
            let zoom = Math.min((parseFloat($img.data('zoom')) || 1) + 0.25, 3);
            $img.data('zoom', zoom);
            applyRxImageTransform($img);
        });
        
        $(document).on('click', '.rx-zoom-out', function () {
            let $img = $(this).closest('.prescription-image-viewer-wrap').find('.rx-prescription-img');
            let zoom = Math.max((parseFloat($img.data('zoom')) || 1) - 0.25, 0.5);
            $img.data('zoom', zoom);
            applyRxImageTransform($img);
        });
        
        $(document).on('click', '.rx-rotate', function () {
            let $img = $(this).closest('.prescription-image-viewer-wrap').find('.rx-prescription-img');
            let rotate = ((parseFloat($img.data('rotate')) || 0) + 90) % 360;
            $img.data('rotate', rotate);
            applyRxImageTransform($img);
        });
        
        $(document).on('click', '.rx-reset', function () {
            let $img = $(this).closest('.prescription-image-viewer-wrap').find('.rx-prescription-img');
            $img.data('zoom', 1).data('rotate', 0);
            applyRxImageTransform($img);
        });
        
        // Click the image itself to quick-cycle zoom
        $(document).on('click', '.rx-prescription-img', function () {
            let $img = $(this);
            let zoom = parseFloat($img.data('zoom')) || 1;
            zoom = zoom >= 2 ? 1 : zoom + 0.5;
            $img.data('zoom', zoom);
            applyRxImageTransform($img);
        });
    /* ========================================================
     * Open Cancelled Order Product Details Modal
     * Shows all items, quantities, prices, descriptions, and cancellation reason
     * ======================================================== */
    function openCancelledOrderProductsModal(oid) {
        $("#ajaxLoader").show();

        // Reset fields
        $('#copm_order_no').text(oid);
        $('#copm_cust_name').text('Loading...');
        $('#copm_contact').text('...');
        $('#copm_order_date').text('...');
        $('#copm_cancellation_box').hide();
        $('#copm_cancel_reason').text('');
        $('#copm_products_tbody').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Loading product details...</td></tr>');
        $('#copm_total_val').text('Rs 0.00');
        $('#copm_discount').text('Rs 0.00');
        $('#copm_payable').text('Rs 0.00');
        $('#copm_paid').text('Rs 0.00');
        $('#copm_balance').text('Rs 0.00');

        $.ajax({
            url: "{{ route('admin.getsalesproduct') }}",
            type: "GET",
            data: { oid: oid },
            dataType: "json",
            success: function(res) {
                // Populate Order info
                if (res.order) {
                    let ord = res.order;
                    $('#copm_cust_name').text(ord.cust_name || 'N/A');
                    $('#copm_contact').text(ord.contact_no || 'N/A');
                    $('#copm_order_date').text(ord.created_at || ord.sale_date || 'N/A');

                    if (ord.order_status === 'cancelled' || ord.sales_status == 3) {
                        $('#copm_status_badge').html('<i class="fa fa-ban mr-1"></i> CANCELLED').removeClass('badge-success').addClass('badge-danger').show();
                    } else {
                        $('#copm_status_badge').html('<i class="fa fa-clock-o mr-1"></i> ' + (ord.order_status ? ord.order_status.toUpperCase() : 'PENDING')).removeClass('badge-danger').addClass('badge-info').show();
                    }

                    if (ord.cancellation_reason && ord.cancellation_reason.trim() !== '') {
                        $('#copm_cancel_reason').text(ord.cancellation_reason);
                        $('#copm_cancellation_box').show();
                    }

                    $('#copm_total_val').text('Rs ' + (parseFloat(ord.total_item_price) || 0).toFixed(2));
                    $('#copm_discount').text('Rs ' + (parseFloat(ord.total_discount) || 0).toFixed(2));
                    $('#copm_payable').text('Rs ' + (parseFloat(ord.total_payable) || 0).toFixed(2));
                    $('#copm_paid').text('Rs ' + (parseFloat(ord.pay_amount) || 0).toFixed(2));
                    $('#copm_balance').text('Rs ' + (parseFloat(ord.pending_amount) || 0).toFixed(2));
                }

                // Populate Products
                let tbody = $('#copm_products_tbody');
                tbody.empty();

                if (!res.data || res.data.length === 0) {
                    tbody.html('<tr><td colspan="8" class="text-center text-muted py-4"><i class="fa fa-info-circle mr-1"></i> No items found for this order.</td></tr>');
                } else {
                    res.data.forEach(function(item, idx) {
                        let rxHtml = '<span class="text-muted" style="font-size:12px;">Non-prescription</span>';
                        if (item.has_prescription && item.rx) {
                            let rx = item.rx;
                            let rDetails = (rx.r_sph || rx.r_cyl || rx.r_axis || rx.r_add) 
                                ? `<strong>R:</strong> SPH: ${rx.r_sph || '-'}, CYL: ${rx.r_cyl || '-'}, AXIS: ${rx.r_axis || '-'}, ADD: ${rx.r_add || '-'}` 
                                : '';
                            let lDetails = (rx.l_sph || rx.l_cyl || rx.l_axis || rx.l_add) 
                                ? `<strong>L:</strong> SPH: ${rx.l_sph || '-'}, CYL: ${rx.l_cyl || '-'}, AXIS: ${rx.l_axis || '-'}, ADD: ${rx.l_add || '-'}` 
                                : '';
                            let pdDetails = rx.total_pd ? `<br><small class="text-muted">PD: ${rx.total_pd}</small>` : '';
                            let fileLink = rx.file_url ? `<br><a href="${rx.file_url}" target="_blank" class="badge badge-info mt-1"><i class="fa fa-paperclip mr-1"></i> View Rx File</a>` : '';
                            rxHtml = `<div style="font-size: 11px; line-height: 1.4;">${rDetails ? rDetails + '<br>' : ''}${lDetails}${pdDetails}${fileLink}</div>`;
                        }

                        let statusBadge = (item.item_status === 'cancelled' || (res.order && (res.order.order_status === 'cancelled' || res.order.sales_status == 3)))
                            ? '<span class="badge badge-danger" style="background-color:#dc2626; color:#fff; font-size:11px; padding:3px 6px;">Cancelled</span>'
                            : '<span class="badge badge-success" style="font-size:11px; padding:3px 6px;">Active</span>';

                        let itemRow = `
                            <tr>
                                <td class="text-center font-weight-bold">${idx + 1}</td>
                                <td><span class="badge badge-dark" style="font-size: 11px;">${item.product_type || 'N/A'}</span></td>
                                <td class="font-weight-bold text-dark">${item.product_code || 'N/A'}</td>
                                <td>${item.product_deatils || '-'}</td>
                                <td class="text-center font-weight-bold">${item.qty || 1}</td>
                                <td class="font-weight-bold text-dark">Rs ${(parseFloat(item.sale_price) || 0).toFixed(2)}</td>
                                <td>${rxHtml}</td>
                                <td class="text-center">${statusBadge}</td>
                            </tr>
                        `;
                        tbody.append(itemRow);
                    });
                }

                $('#CancelledOrderProductModal').modal('show');
            },
            error: function() {
                $.toaster({
                    priority: 'danger',
                    title: 'Error',
                    message: 'Failed to fetch product details for Order #' + oid,
                    timeout: 4000
                });
            },
            complete: function() {
                $("#ajaxLoader").fadeOut();
            }
        });
    }

    // Modal popup to view customer cancellation reason
    $(document).on('click', '.btn-show-cancel-reason', function(e) {
        e.preventDefault();
        let oid = $(this).data('oid');
        let reason = decodeURIComponent($(this).data('reason') || 'No cancellation reason provided');
        let customerName = decodeURIComponent($(this).data('cust') || 'Customer');

        Swal.fire({
            title: '<div style="color: #dc2626; font-weight: 700; font-size: 20px;"><i class="fa fa-ban mr-2"></i> Order Cancelled</div>',
            html: `
                <div style="text-align: left; padding: 12px 16px; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; margin-top: 10px;">
                    <div style="margin-bottom: 8px; font-size: 14px; color: #1f2937;">
                        <strong>Order Number:</strong> <span class="badge badge-secondary" style="font-size: 13px;">#${oid}</span>
                    </div>
                    <div style="margin-bottom: 12px; font-size: 14px; color: #1f2937;">
                        <strong>Customer:</strong> <span>${customerName}</span>
                    </div>
                    <div style="font-size: 13px; font-weight: 700; color: #dc2626; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fa fa-comment mr-1"></i> Customer Cancellation Reason:
                    </div>
                    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-left: 4px solid #dc2626; border-radius: 6px; padding: 12px; font-size: 14px; color: #374151; line-height: 1.5; word-break: break-word;">
                        ${reason}
                    </div>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: '<i class="fa fa-times mr-1"></i> Close',
            confirmButtonColor: '#07484A',
            showCloseButton: true,
            customClass: {
                popup: 'rounded-lg'
            }
        });
    });




     function generateWearingCheckboxes(wearing_type, index) {
        let types = wearing_type ? wearing_type.split(',').map(t => t.trim()) : [];
        let allTypes = ["Constant Use","Reading Wear","Distance Wear","Single Vision","Progressive","Bifocal","Trifocal"];
        let html = '';
    
        allTypes.forEach(function(type) {
            let checked = types.includes(type) ? 'checked' : '';
            html += `
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="glassWearingType_${index}[]" value="${type}" ${checked}>
                <label class="form-check-label">${type}</label>
            </div>
            `;
        });
    
        return html;
    }
    
    
    /****=============================
     * Wareing TYpe
     *  ==============================*/
         
   function toggleFrameFields(prescriptionDiv) {
        let frametype = prescriptionDiv.find('input.frametype-input:checked').val();
        let aField = prescriptionDiv.find('.framesizea');
        let bField = prescriptionDiv.find('.framesizeb');
    
        if (frametype === 'Full frame') {
            aField.hide();
            bField.hide();
        } else if (frametype === 'Half frame') {
            aField.hide();
            bField.show();
        } else if (frametype === 'Rimless frame') {
            aField.show();
            bField.show();
        }
    }
    
    // Initialize toggle for all prescriptions
    $('#Prescriptionglassdiv .prescription-section').each(function() {
        let prescDiv = $(this);
        toggleFrameFields(prescDiv);
    
        // Bind change event
        prescDiv.find('input.frametype-input').on('change', function() {
            toggleFrameFields(prescDiv);
        });
    });




    
    function showResponseMessage(data) 
    {

        if (data.status === 'success') 
        {
            $.toaster({ priority : 'success', title : 'Success..!' , message : data.message });
            dataListView.draw();
        } 
        else if (data.status === 'error') 
        {
            $.toaster({ priority : 'danger', title : 'Opps...!' , message : data.message });
            dataListView.draw();
        } 
        else 
        {
            $.toaster({ priority : 'danger', title : 'Opps..!' , message : 'Something went wrong. Please try again' });
        }
    }
        
</script>


