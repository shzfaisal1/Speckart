@extends('layouts.master')
@section('styles')
<style>
/* Spinner when input has `loading` class */
input.loading 
{
    background-image: url('https://i.imgur.com/6RMhx.gif'); /* or any spinner gif you like */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}


</style>  
    

@endsection
@section('content')
@php $tbl_setting =  DB::table("tbl_product_code_setting")->where('product_type','Frame')->first();   @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Import Data From Excel</h3>
                        <input type="text" class="form-control input" placeholder="Search By,Product Name" id="search" name="search" style="width:320px">
                        <a href="#" class=" btn" data-toggle="modal" data-target="#productModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Import Product
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
               <div class="col-lg-12">
                <div class="domestic-orders-table">
                    <div id="processingLoader" class="processing-loader" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <strong class="text-success">Please wait...</strong>
                                    <div class="spinner-border ms-auto text-success spinner-grow" role="status"  aria-hidden="true"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">Sr.No</th>
                                <th class="wd-15p">Refrence No</th>
                                <th class="wd-15p">Product Type</th>
                                <th class="wd-15p">Total Uploded Records</th>
                                <th class="wd-10p">Total Invalid Records</th>
                                <th class="wd-10p">Total Records</th>
                                <th class="wd-10p">Created At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
        
               </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" data-backdrop="static" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Start New Import</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="heading">STEP 1</p>
                        <ul>
                            <li>Download respective templates to fill in your data. Use below links to download the latest and updated template.</li>
                            <li style="margin-top: 10px;">
                                <a href="{{asset('import/csv_format_frame_inventory.csv')}}" download>
                                <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Frame Inventory');">Frame</button>
                                </a>
                                <a href="{{asset('import/csv_format_goggles_inventory.csv')}}" download>
                                <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Goggles Inventory');">Goggles</button>
                                </a>
                                <a href="{{asset('import/csv_format_glass_inventory.csv')}}" download>
                                <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Glass Inventory');">Glass</button>
                                </a>
                                <a href="{{asset('import/csv_format_lens_inventory.csv')}}" download>
                                <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Contact Lens Inventory');" style="margin-top: 10px;">Contact Lens</button>
                                </a>
                                <a href="{{asset('import/csv_format_solution_inventory.csv')}}" download>
                                <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Solution Inventory');" style="margin-top: 10px;">Solution</button>
                                </a>
                                <a href="{{asset('import/csv_format_other_inventory.csv')}}" download>
                                <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Other Inventory');" style="margin-top: 10px;">Other</button>
                                </a>
                                <!--<a href="{{asset('import/csv_format_non_chargeable_inventory.csv')}}" download>
                                <button type="button" class="btn btn-primary" onclick="downloadCSVFormat('Non Chargeable Inventory');" style="margin-top: 10px;">Non Chargeable</button>
                                </a>-->
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <p class="heading">STEP 2</p>
                        <ul>
                            <li>Fill all details as per the rules provided below, make sure you save the file as .csv only. Also, do not delete the header row as it will be removed automatically when our system is importing your data.</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <p class="heading">STEP 3</p>
                        <ul>
                            <li>Open the saved file in NOTEPAD and copy all data and paste it in the box provided. Make sure you select the correct import type and branch for which you want to import respective data.</li>
                        </ul>
                    </div>
                </div>
               
                <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li>All fields marked with * are mandatory.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="import-bulk-order-upload">
                                    <div class="drop-zone">
                                      <form method="POST" action="{{ route('admin.bulk-product-add') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div>
                                            <img src="{{asset('assets/images/icon/upload.png')}}" alt="" style="width:45px">
                                            <p class="drop-zone__prompt">Drag & Drop files or <span>Browse</span></p>
                                            <h6>Please download and use sample template file.Only csv file format is acceptable</h6>
                                            <p class="drop-zone__prompt">Creating Product</p>
                                            <h6>This is going to take a few minutes. Kindly request your patience.</h6>
                                            <div id="csvPreview" style="color: #1c59bf;font-size: 16px;font-weight: 600;"></div>
                                        </div>
                                        <input type="file" name="myFile" id="myFile" class="drop-zone__input" onchange="return csvValidation()" required>
                                    </div>
                                    <div>
                                        <label for="search" class="form-label">Product</label>
                                        <select class="form-control select2" style="height: 32px !important;" id="product_type" name="product_type" required>
                                            <option value="">Select Product </option>
                                            <option value="Frame">Frame</option>
                                            <option value="Glass">Glass</option>
                                            <option value="Goggles">Goggles</option>
                                            <option value="Lens">Contact Lens</option>
                                            <option value="Solution">Solution</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <br>
                                    
                                    <button class="btn btn-success" type="submit"> Upload</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
      allowClear: true,
      width: "100%"
    });
});
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
            url: "{{ route('admin.importproduct.search') }}",
            "dataType": "json",
            "type": "POST",
            "data": function(d) 
            {
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
                "data": "refrence_no",
                orderable: false,
            },
            {
                "data": "product_type",
                orderable: false,
            },
            {
                "data": "total_records_upload",
                orderable: false,
            },
            {
                data: 'total_invalid_records',
                orderable: false,
                searchable: false
            },
            {
                "data": "total_records",
                orderable: false,
            },

            {
                "data": "created_at",
                orderable: false,
            },
            

        

            
        ],

        searchDelay: 1500,
        columnDefs: [{
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0
            }

         
            
        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-4"i><"col-sm-12 col-md-4"p>>',

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

<script>
function csvValidation()
{
    var fileInput = document.getElementById('myFile');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.csv)$/i;
    if(!allowedExtensions.exec(filePath)){
         Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "Please upload file having extensions .csv  only.",
            showConfirmButton: true
        });
        fileInput.value = '';
        return false;
    }else{
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('csvPreview').innerHTML = '"'+filePath+'"';
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
}
</script>



@endsection
