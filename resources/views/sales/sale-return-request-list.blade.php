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
                        <h3>Sales Return Request History</h3>
                        
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
            <div id="requestdataload"></div> 
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
                            <tr>
                                <th class="wd-10p">Sr.No</th>
                                <th class="wd-15p">Store Name</th>
                                <th class="wd-15p">Order Deatils</th>
                                <th class="wd-20p">Customer Details</th>
                                <th class="wd-10p">Product</th>
                                <th class="wd-10p">Product Code</th>
                                <th class="wd-10p">Description</th>
                                <th class="wd-10p">Amount</th>
                                <th class="wd-10p">Return Date</th>
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

<div class="modal fade" data-backdrop="static" id="reqModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="requestForm" method="POST">
            <div class="modal-body">
                <div class="alert alert-dark">
                     <strong id="product_text_deatils"></strong>
                </div>
                <div class="alert alert-dark">
                     <strong id="producttype_text"></strong>
                </div>
                <div class="alert alert-dark">
                     <strong id="product_code_text"></strong>
                </div>
                <div class="alert alert-dark">
                     <strong id="oid_text"></strong>
                </div>
                <hr/>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="uid" id="uid">
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="return_status" id="inlineRadio8" value="1" checked>
                          <label class="form-check-label" for="inlineRadio8">Approved</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="return_status" id="inlineRadio9" value="3">
                          <label class="form-check-label" for="inlineRadio9">Reject</label>
                        </div>

                    </div>
                    <div class="col-md-9">
                        <label for="">Remark <span class="text-danger">*</span></label>
                        <textarea class="form-control"   id="return_remark_back" name="return_remark_back"></textarea>
                    </div>
                   
                </div>
                
                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="button" class="btn btn-primary"  data-carrier="" onclick="updatedRequest(this)">Submit</button>
            </div>
        </form>
      </div>
    </div>
  </div>


<!-- Image Preview Modal -->
<div class="modal fade" data-backdrop="static" id="imagePreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Return Remark /  Photo</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>

           <div class="modal-body text-center">
                <div class="alert alert-info text-left">
                    <strong>Return Remark :</strong>
                    <p id="returnRemarkText" style="margin:0;"></p>
                </div>
                <img id="previewImage" src="" style="max-width:100%;height:auto;margin-bottom:15px;">
            
                
            
            </div>

        </div>
    </div>
</div>

@section('scripts')
<script>
  $(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
    
    get_requestdata();
  });
  
  
function get_requestdata()
{

    $.ajax({
	   type: "POST",
	   url: "{{ route('admin.get-returnrequestdata') }}",
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
		    $('#requestdataload').empty();
		    if (success.status === 'success') 
            {
                $('#requestdataload').show();
                $('#requestdataload').append(main_data);
                
                $('#global-loader').hide();
            }
            else
            {
                get_requestdata();
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
    get_requestdata();// reload datatable
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
    get_requestdata();
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
            url: "{{ route('admin.sale-return-request-datatable') }}",
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
                "data": "sr_no",
                orderable: false,
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
                "data": "customer_details",
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
                "data": "amount",
                orderable: false,
            },
            {
                "data": "return_date",
                orderable: false,
            },
            {
                "data": "request_status",
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
                    // ❌ If rejected → no action
                    if (full.status_value == 3 || full.status_value == 1) {
                        
                        let baseUrl = "{{ url('sale/invoice') }}";
                        let orderUrl   = baseUrl + '/' + full['encryptedId'] + '/order';
                        return `<span class="text-muted">
                        <a href="${orderUrl}" target="_blank" class="tooltip">
                                <img class="action-icon" src="{{asset('assets/images/icon/form.webp')}}">
                                <span class="tooltip-text">View & Print Order Form</span>
                            </a>
                        </span>`;
                    }
            
                    // ✅ Otherwise show action button
                    return `
                        <i class="fa fa-check-circle fa-2x"
                           style="margin-top:10px; cursor:pointer; color:#28a745"
                           onclick="openreturnModal(
                               '${full.pid}',
                               '${full.product_code}',
                               '${full.description}',
                               '${full.ptype}',
                               '${full.oid}'
                           )">
                        </i>
                    `;
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
        get_requestdata();
    });
    
    $('.select').on('change', function() 
    {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
        get_requestdata();
    });
    
    

   function openreturnModal(id,product_code,description,ptype,oid)
   {
       
        $('#product_text_deatils').text('');
        $('#producttype_text').text('');
        $('#product_code_text').text('');
        $('#oid_text').text('');
        
        var product_text_deatils = description == 'null' ? '' : ' Description : ' + description;
        var producttype_text = ptype == 'null' ? '' : 'Product Type : ' + ptype;
        var product_code_text = product_code == 'null' ? '' : '   Product Code : ' + product_code;
        var oid_text = oid == 'null' ? '' : ' Order No : ' + oid;

        document.getElementById('modalTitle').innerText = 'Return Request Approved / Reject';
        document.getElementById('uid').value = id;
        
        $('#product_text_deatils').text(product_text_deatils);
        $('#producttype_text').text(producttype_text);
        $('#product_code_text').text(product_code_text);
        $('#oid_text').text(oid_text);
    
        $('#reqModal').modal('show');
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
        let remark = $(this).data('remark');
        $('#previewImage').attr('src', imgSrc);
        $('#returnRemarkText').text(remark);
    
        $('#imagePreviewModal').modal('show');
    });
    
    
    
    function updatedRequest()
    {
        // clear old error
        $("#return_remarkError").text('');
    
        let uid            = $("#uid").val();
        let return_remark_back            = $("#return_remark_back").val();
        let return_status  = $("input[name='return_status']:checked").val();


    
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
                return_remark_back: return_remark_back,
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
