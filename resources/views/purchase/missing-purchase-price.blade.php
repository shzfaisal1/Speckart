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
@php
     $usr = Auth::guard()->user();
 @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Missing Purchase Price </h3>
                        
                    </div>
                </div>
            </div>
             <div class="row">
                        <div class="col-md-6">
                           <div class="row">
                              <label for="modal_purchase_price" class="col-lg-3 col-form-label">
                                Search By :  
                              </label>
                              <div class="col-lg-9">
                                 <div class="d-flex" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="searchradio" id="inlineRadio5" value="Barcode" checked>
                                          <label class="form-check-label" for="inlineRadio5">Barcode</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="searchradio" id="inlineRadio6" value="Orders">
                                          <label class="form-check-label" for="inlineRadio6">Orders</label>
                                        </div>
                                    </div> 
                              </div>
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
                        <input type="text" class="form-control input" placeholder="Company,Barcode,Product Code" id="search" name="search" style="width: 250px;margin-top: 10px;">
                    </div>
                </div> 
                @if($usr->roles[0]->name == 'Admin')
                <div class="col-lg-3" style="margin-top: 10px;">
                        <select class="form-control select" style="height: 32px !important;" id="store_id" name="store_id">
                            <option value="">Select  Store</option>
                          <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                           @foreach($tbl_store as $tbl_store)
                            <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                          @endforeach
                        </select>
                </div>
                @endif
            </div>

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
                                <th class="wd-10p">Date</th>
                                <th class="wd-10p">Product Type	</th>
                                <th class="wd-10p">Product Code	</th>
                                <th class="wd-10p">Description</th>
                                <th class="wd-10p">Barcode</th>
                                <th class="wd-10p">Purchase Price</th>
                                <th class="wd-10p">Retail Price	</th>
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


<div class="modal fade" data-backdrop="static" id="addpurchasepriceModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Update Purchase Price</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="purchaseForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-dark">
                             <strong id="description"></strong>
                        </div>
                    </div>
                </div>
                
                <hr/>
                 <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="barcode_id" id="barcode_id">

                
                <div class="row">
                    <div class="col-md-12">
                        <label for="">Update Purchase Price<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="purchase_price" name="purchase_price">
                        <span class="error badge text-danger" id="purchase_priceError"></span>
                    </div>
               </div>
               <div class="row">
                    <div class="col-md-12">
                        <label for="modal_purchase_price">
                        Update Purchase Price In PRODUCT CODE 
                      </label>
                            <div class="d-flex" style="margin-top: 10px;">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ispcode" id="inlineRadio1" value="1">
                                  <label class="form-check-label" for="inlineRadio1">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ispcode" id="inlineRadio2" value="0" checked>
                                  <label class="form-check-label" for="inlineRadio2">No</label>
                                </div>
                            </div>    
              
                    </div>
               </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
                <button type="submit" class="btn btn-primary">Update Purchase Price</button>
            </div>
        </form>
      </div>
    </div>
  </div> 

@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
  });
</script>
<script>
var start = moment('2025-01-01'); // Lifetime start date
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

// Update on apply
$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
    cb(picker.startDate, picker.endDate);
});

// Set initial range to Lifetime on load
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
            url: "{{ route('admin.missing-price-barcode-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
                d.date_from = $('#date_from').val(),
                d.date_to = $('#date_to').val(),
                d.product_type = $('#product_type').val(),
                d.search1 = $('#search').val(),
                d.store_id = $('#store_id').val(),
                d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [
            {
                "data": "sr_no",
                orderable: false,
            },

            {
                "data": "store_name",
                orderable: false,
            },

            {
                "data": "purchase_date",
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
                "data": "product_details",
                orderable: false,
            },
            {
                "data": "barcode_no",
                orderable: false,
            },

            {
                "data": "purchase_price",
                orderable: false,
            },
             {
                "data": "retail_price",
                orderable: false,
            },
            {
             "data": "action",
             orderable: false,
            searchable: false
         }


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
                    let html = `<a href="#"  onclick="openpurchaseModal('` + full['encryptedId'] + `','` + full['description'] + `')">
                                        <button type="button" class="btn btn-success btn-sm mb-1">Update Price</button>
                                    </a>'`;
            
                    return html;
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
    
    function openpurchaseModal(id, description) 
    {
        $('#description').text('');

        

        var description_text = description == 'null' ? '' : '   Product Description : ' + description;
        document.getElementById('modalTitle').innerText = 'Update Purchase Price';
        document.getElementById('barcode_id').value = id;

        $('#description').text(description_text);

    
        $('#addpurchasepriceModal').modal('show');
    }
    
    
    $("#purchaseForm").submit(function(e) {
        e.preventDefault(); 
    
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let purchase_price = document.getElementById("purchase_price" + class_name).value.trim();


        if (purchase_price === "") {
            document.getElementById("purchase_priceError" + class_name).textContent = "Purchase price required.";
            document.getElementById("purchase_price" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    

    
        if (!isValid) {
            return;
        }

        let form = $("#purchaseForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.barcode-price-update') }}",
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
                    window.location.href = "{{ route('admin.missing-purchase-price') }}";
                } else {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
    });
    

    
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
