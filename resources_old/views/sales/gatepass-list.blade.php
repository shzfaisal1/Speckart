@extends('layouts.master')
@section('styles')
<style>
/* Spinner when input has `loading` class */
input.loading {
    background-image: url('https://i.imgur.com/6RMhx.gif'); 
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
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
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Gatepass History</h3>
                        
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-3">
                    <div class="domestic-orders-date">
                        <div id="reportrange" class="pull-left"
                            style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                        <input type="hidden" class="form-control" id="date_from" name="date_from">
                        <input type="hidden" class="form-control" id="date_to" name="date_to">
                    </div> 
                </div> 
                 @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3" style="margin-top: 10px;">
                     <div class="form-group">
                        <select class="form-control select" style="height: 32px !important;margin-top:10px" id="store_id" name="store_id">
                            <option value="">Select  Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3" style="margin-top: 10px;">
                    <select class="form-control select" style="height: 32px !important;" id="product_type" name="product_type">
                        <option value="">Select Product </option>
                        <option value="Frame">Frame</option>
                        <option value="Glass">Glass</option>
                        <option value="Goggles">Goggles</option>
                        <option value="Lens">Contact Lens</option>
                        <option value="Solution">Solution</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Bill Number,Barcode,Product Code" id="search" name="search" style="width: 200px;margin-top: 10px;">
                    </div>
                </div>    
            </div>
            <div id="gatepassdataload"></div> 
            <br>
            <div class="row">
               <div class="col-lg-12">
                <div class="domestic-orders-table">
                    <div id="processingLoader" class="processing-loader" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <strong class="text-success">Please wait...</strong>
                                    <div class="spinner-border ms-auto text-success spinner-grow" role="status"
                                        aria-hidden="true"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table datatables-basic w-100">
                        <thead>
                            <div class="row justify-content-between align-items-center mb-3 mt-3 mr-3">
                                <div class="col-md-10">
                                    <span id="checked-order-count" class="btn btn-success">Selected Rows: 0</span>
                                </div>
                                <div class="col-md-2">
                                     <span id="bulk_action" class="btn btn-success">Update Bulk Status</span>
                                   
                                </div>
                            </div>
                            <tr>
                                <th colspan="7" style="color: #FF0000;" colspan="2">Select checkbox and click on "Update Status" </td>
                            </tr>
                            <tr>
                                <th style="width: 0px;"></th>
                                <th class="wd-15p"><div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll">
                                <label class="form-check-label" for="checkboxSelectAll"></label></div></th>
                                <th class="wd-15p">Store Name</th>
                                <th class="wd-15p">Order Deatils</th>
                                <th class="wd-10p">Product</th>
                                <th class="wd-10p">Product Code</th>
                                <th class="wd-10p">Description</th>
                                <th class="wd-10p">Gatepass Date</th>
                                <th class="wd-10p">Status</th>
                                <th class="wd-10p">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
        
               </div>
            </div>
        </div>
    </div>
</section>

@endsection




@section('scripts')
<script>
  $(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
    
    get_gatepassdata();
  });
  
  
function get_gatepassdata()
{

    $.ajax({
	   type: "POST",
	   url: "{{ route('admin.get-gatepassdata') }}",
	   data: {
	       date_from: $('#date_from').val(),
	       date_to: $('#date_to').val(),
	       product_type: $('#product_type').val(),
	       store_id: $('#store_id').val(),
	       search: $('#search').val(),
	       _token: "{{ csrf_token() }}"
	   },
	   dataType: "json",
	   beforeSend: function () {
            $("#ajaxLoader").show(); 
        },
	   success: function (success)  
	   {
		    var main_data=success.requestdata_section;
		    $('#gatepassdataload').empty();
		    if (success.status === 'success') 
            {
                $('#gatepassdataload').show();
                $('#gatepassdataload').append(main_data);
                
                $('#global-loader').hide();
            }
            else
            {
                get_gatepassdata();
            }
    	},
    	complete: function () 
    	{
            $("#ajaxLoader").fadeOut(); 
        }
   });
}
</script>
<script>

var start = moment();   // default today
var end   = moment();   // default today

function cb(start, end)
{
    $('#reportrange span').html(
        start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY')
    );

    $('#date_from').val(start.format('YYYY-MM-DD'));
    $('#date_to').val(end.format('YYYY-MM-DD'));

    dataListView.draw();
    get_gatepassdata();// reload datatable
}


/* =========================
   DATE PICKER
========================= */

$('#reportrange').daterangepicker({
    startDate: start,
    endDate: end,
    autoUpdateInput: false,
    showDropdowns: true,
    linkedCalendars: false,
    maxDate: moment(),

    locale: {
        format: 'DD-MM-YYYY',
        cancelLabel: 'Clear'
    }

}, function(start, end) {
    cb(start, end);
});


/* APPLY EVENT */
$('#reportrange').on('apply.daterangepicker', function(ev, picker)
{
    cb(picker.startDate, picker.endDate);
});


/* CLEAR EVENT */
$('#reportrange').on('cancel.daterangepicker', function()
{
    $('#date_from').val('');
    $('#date_to').val('');
    $('#reportrange span').html('Select Date');

    dataListView.draw();
    get_gatepassdata();
});


/* DEFAULT LOAD */
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
            url: "{{ route('admin.gatepass-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d.product_type = $('#product_type').val(),
                d.store_id = $('#store_id').val(),
                d.search1 = $('#search').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": 'responsive_id',
                orderable: false,
                searchable: false
            },
            {
                "data": "pid",
                orderable: false,
                searchable: false
            },

            {
                "data": "store_details",
                orderable: false,
            },
            {
                "data": "order_details",
                orderable: false,
            },

            
            {
                "data": "product_type",
                orderable: false,
            },
            {
                "data": "product_code",
                orderable: false,
            },

            {
                "data": "description",
                orderable: false,
            },
        
            {
                "data": "gatepass_create_date",
                orderable: false,
            },
            
            {
                "data": "warehouse_status",
                orderable: false,
            },

            {
                "data": "action",
                orderable: false,
            },

            
        ],

        searchDelay: 1500,
        columnDefs: [{
                // For Responsive
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0
            },

            {
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function (data, type, full)
                {
                    
                        let baseUrl = "{{ url('sale/invoice') }}";
                        let orderUrl   = baseUrl + '/' + full['encryptedId'] + '/order';
                        return `<span class="text-muted">
                        <a href="${orderUrl}" target="_blank" class="tooltip">
                                <img class="action-icon" src="{{asset('assets/images/icon/form.webp')}}">
                                <span class="tooltip-text">View & Print Order Form</span>
                            </a>
                        </span>`;
                    
            
                    
                }
            },
            
            {
                // For Checkboxes
                targets: 1,
                orderable: false,
                responsivePriority: 1,
                render: function (data,type, full) {
                    
                        return (
                            '<div class="form-check"> <input class="form-check-input dt-checkboxes" type="checkbox" value="" id="' +
                            data +
                            '" /><label class="form-check-label" for="' +
                            data +
                            '"></label></div>'
                        );
                   
                },
                checkboxes: {
                    selectAllRender:'<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>',
                    selectRow: true
                }
            },
            

         
            
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
        get_gatepassdata();
    });
    
    $('.select').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
        get_gatepassdata();
    });
    
    

    function updateCheckedOrderCount() {
        // Count checked checkboxes (excluding disabled ones)
        let count = $('input.dt-checkboxes:checked:not(:disabled)').length;
    
        // Update the display
        $('#checked-order-count').text(`Selected Orders: ${count}`);
        $('#checked-order-count').removeClass('badge-info badge-dark');
        $('#checked-order-count').addClass(count > 0 ? 'badge-dark' : 'badge-info');
    }
    
    // Initialize the count on page load
    $(document).ready(function () {
        updateCheckedOrderCount();
    });
    
    // Handle individual checkbox changes
    $(document).on('change', 'input.dt-checkboxes', function () {
        updateCheckedOrderCount();
    });
    
    // Handle "Select All" checkbox
    $(document).on('change', '#checkboxSelectAll', function () {
        // Trigger change on all non-disabled checkboxes to sync state
        if ($(this).is(':checked')) {
            $('input.dt-checkboxes:not(:disabled)').prop('checked', true).trigger('change');
        } else {
            $('input.dt-checkboxes:not(:disabled)').prop('checked', false).trigger('change');
        }
        updateCheckedOrderCount();
    });
    
    // Update count after table redraw
    dataListView.on('draw.dt', function () {
        updateCheckedOrderCount();
    });
    
    $('#bulk_action').on('click', function(e) {
       
        
        let rows_selected = dataListView.column(1).checkboxes.selected();
        let sender_ids = [];
        $.each(rows_selected, function(index, rowId) {
            let checkbox = $('input[type="checkbox"][id="' + rowId + '"]');
            if (checkbox.prop('disabled') == false) {
                sender_ids.push(rowId);
            }            
        });
        if (sender_ids.length > 0) 
        {
            
                ajaxCall("{{ route('admin.bulk-confirm-gatepass') }}",sender_ids);
                $('#bulk_action option:first-child').attr("selected", "selected");
            
            
        }
        else 
        {
            $.toaster({ priority : 'warning', title : 'Attention!!' , message : "Please select at least one data" });
        }
    });
    
    function ajaxCall(url,ids){
        Swal.fire({
            title: "Are you sure?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: "Submit",
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger mx-1'
            },
            buttonsStyling: false,
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: ids
                        },
                         beforeSend: function () {
                          document.getElementById("global-loader").style.display = "";
                        },
                        success: function(data) {
                             document.getElementById("global-loader").style.display = "none";
                            if (data.status === true ) {
                                $.toaster({ priority : 'success', title : 'Success..!' , message : data.message});
                                dataListView.draw();
                            } else {
                                $.toaster({ priority : 'warning', title : 'Attention!!' , message : data.message});
                                if(data.code == 202){
                                    window.setTimeout(function () {
                                        location.href = data.redirect;
                                    }, 3000);
                                }
                            }
                        },
                        error: function(reject) {
                            if (reject.status === 422) {
                                let errors = reject.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $.toaster({ priority : 'warning', title : 'Attention!!' , message : value[0]});
                                });
                            } else {
                                $.toaster({ priority : 'warning', title : 'Attention!!' , message : reject.responseJSON.message});
                            }
                        }
                    })
            }
        })
    }

    
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
    
    
    $(document).on('click', '.return-img', function ()
    {
        let imgSrc = $(this).data('img');
    
        $('#previewImage').attr('src', imgSrc);
    
        $('#imagePreviewModal').modal('show');
    });
    
    
    
    function updatedRequest()
    {
        // clear old error
        $("#return_remarkError").text('');
    
        let uid            = $("#uid").val();
        let return_status  = $("input[name='return_status']:checked").val();
        let return_remark  = $("#return_remark").val().trim();
    
        /* -------------------------
           Validation
        -------------------------*/
        if(return_remark === '')
        {
            $("#return_remarkError").text("Remark required");
            return;
        }
    
        /* -------------------------
           Disable Button (prevent double click)
        -------------------------*/
        let btn = event.target;
        $(btn).prop('disabled', true).text('Processing...');
    
        $.ajax({
            url: "{{ route('admin.sale-returen-stored') }}",
            type: "POST",
            data: {
                uid: uid,
                return_status: return_status,
                return_remark: return_remark,
                _token: "{{ csrf_token() }}"
            },
    
            success: function(response)
            {
                /* ✅ Laravel response uses STATUS */
                if(response.status === true)
                {
                    $.toaster({
                        priority:'success',
                        title:'Success!',
                        message:response.message
                    });
    
                    $('#reqModal').modal('hide');
    
                    // reload datatable
                    if(typeof dataListView !== "undefined"){
                        dataListView.ajax.reload(null,false);
                    }
                }
                else
                {
                    $.toaster({
                        priority:'warning',
                        title:'Warning',
                        message:response.message
                    });
                }
            },
    
            error: function(xhr)
            {
                if(xhr.status === 422)
                {
                    let errors = xhr.responseJSON.errors;
    
                    $.each(errors, function(key, value){
                        $.toaster({
                            priority:'warning',
                            title:'Validation',
                            message:value[0]
                        });
                    });
                }
                else
                {
                    $.toaster({
                        priority:'error',
                        title:'Error',
                        message:'Something went wrong'
                    });
                }
            },
    
            complete: function(){
                $(btn).prop('disabled', false).text('Submit');
            }
        });
    }
        
</script>




@endsection
