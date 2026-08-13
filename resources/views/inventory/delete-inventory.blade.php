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
                        <h3>Inventory - Delete Stock</h3>
                        
                    </div>
                </div>
            </div>

            <div class="row">
                    <div class="col-md-4">
                        <strong>Store Name : {{$store_name}} </strong>
                    </div>
                    <div class="col-md-4">
                             <strong>Product Type : {{$inventory->product_type}}</strong>
                    </div>
                </div>

            <div class="row">
                <div class="col-md-4">
                         <strong>Product Code : {{$inventory->product_code}}</strong>
                </div>
                <div class="col-md-4">
                         <strong>Product Id : {{$inventory->product_id}}</strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                         <strong>Description : {{$inventory->product_details}}</strong>
                </div>
            </div>
            <hr/>
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
                    <form id="deleteForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <table class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th style="width: 0px;"></th>
                                <th class="wd-15p"><div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll">
                                <label class="form-check-label" for="checkboxSelectAll"></label></div></th>
                                <th class="wd-15p">Barcode</th>
                                <th class="wd-15p">Added Details</th>
                                <th class="wd-20p">Product Details	</th>
                                <th class="wd-10p">Purchase Price</th>
                                <th class="wd-10p">Retail Price	</th>
                                <th class="wd-10p">Store Name</th>
                            </tr>
                        </thead>
                    </table>
                    <hr/>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                              <label for="modal_purchase_price" name="modal_purchase_price" class="col-lg-4 col-form-label">
                                Loss or Damage 
                              </label>
                              <div class="col-lg-5">
                                <div class="d-flex" style="margin-top: 10px;">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="loos_damage" id="inlineRadio1" value="1" >
                                      <label class="form-check-label" for="inlineRadio1">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="loos_damage" id="inlineRadio2" value="0" checked>
                                      <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                </div>    
                                  
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                              <label class="col-lg-2 col-form-label">
                                Comment 
                              </label>
                              <div class="col-lg-10">
                                   <textarea type="text" id="delete_comment" name="delete_comment" class="form-control"></textarea>
                              </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary loaderbtn">Delete</button>
                </form>   
    
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
  });
</script> 
<script>
var start = moment('2025-01-01'); // Lifetime start date
var end = moment(); // Today

function isCurrentMonth(date)
{
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

var perbox = "{{$inventory->perbox ?? ''}}";
if (!perbox || perbox === 'null') {
        var perbox = '';
    }
let dataListView = $('.datatables-basic')
    .on('preXhr.dt', function() {
        $('#processingLoader').show();
    })
    .on('draw.dt', function() {
        $('#processingLoader').hide();
    }).DataTable({

        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "ajax": {
            "url": "{{ route('admin.inventory-barcode-datatable') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) {
               d.product_type = "{{$inventory->product_type}}";
                    d.product_code = "{{$inventory->product_code}}";
                    d.product_details = "{{$inventory->product_details}}";
                    d.perbox = perbox;
                    d.store_id = "{{$inventory->store_id}}";
                    d._token = "{{ csrf_token() }}";
            }
        },
        "columns": [{
                "data": 'responsive_id',
                orderable: false,
                searchable: false
            },
            {
                "data": "barcode_id",
                orderable: false,
                searchable: false
            },
            {
                "data": "barcode",
                orderable: false,
            },
            {
                "data": "purchase_details",
                orderable: false,
            },
            {
                "data": "product_details",
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
                "data": "store_name",
                orderable: false,
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
                // remove previous & next text from pagination
                previous: 'Prev',
                next: 'Next'
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
                            '' // ? Do not show row in modal popup if title is blank (for check box)
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
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6 d-flex "l><"col-sm-12 col-md-6 text-end mt-1"Bf>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

        aLengthMenu: [
            [10, 20, 50, 100],
            [10, 20, 50, 100],
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
    
    $('.select').on('change', function() {
        const column = dataListView.column($(this).attr('name'));
        column.search($(this).val()).draw();
    });
    
    
    $("#deleteForm").submit(function (e) 
    {
        e.preventDefault();
        
        let isValid = true;
        let class_name = '';
    
        let rows_selected = dataListView.column(1).checkboxes.selected();
        let sender_ids = [];
        $.each(rows_selected, function(index, rowId) 
        {
            let checkbox = $('input[type="checkbox"][id="' + rowId + '"]');
            if (checkbox.prop('disabled') == false) {
                sender_ids.push(rowId);
            }            
        });
        
         if (sender_ids == '')  {
            $.toaster({ priority : 'warning', title : 'Attention!!' , message : "Please select at least one data" });
            isValid = false;
        }

        if (!isValid) {
            return;
        }
        let form = $("#deleteForm")[0];
        let data = new FormData(form);
         data.append('sender_ids', JSON.stringify(sender_ids));
       $.ajax({
            type: "POST",
            url: "{{ route('admin.inventory-destroy') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function (response) {
                if ($.isEmptyObject(response.error)) 
                {
                    $.toaster({
                        priority: "success",
                        title: response.success,
                        message: ""
                    });
                    window.location.href = "{{ route('admin.inventory-adjustment-history') }}";
                } 
                else
                {
                    document.querySelectorAll(".error").forEach(el => el.textContent = "");
                    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    
       
    });
    
</script>    


@endsection
