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
.dropdown-menu{
    width: 100%;
    padding: 5px 15px;
}
.suggestion-box {
    z-index: 9999;
    max-height: 200px;
    overflow-y: auto;
}

</style> 

@endsection
@section('content')
@php $tbl_setting =  DB::table("tbl_product_code_setting")->where('product_type','Lens')->first();   @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Lens Product Code</h3>
                        <input type="text" class="form-control input" placeholder="Search By,Product Name,Product Code,Quality,Price" id="search"
                                name="search" style="width:320px">
                        <a href="#" class=" btn" data-toggle="modal" data-target="#productModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Create Lens Product Code
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
                                    <div class="spinner-border ms-auto text-success spinner-grow" role="status"
                                        aria-hidden="true"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">Product ID</th>
                                <th class="wd-15p">Product Code</th>
                                <th class="wd-15p">Description</th>
                                <th class="wd-15p">Price</th>
                                <th class="wd-10p">Inventory</th>
                                <th class="wd-10p">Negative Inventory</th>
                                <th class="wd-10p">Created At</th>
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
<div class="modal fade" data-backdrop="static" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Lens</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="productForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="uid">
                    <input type="hidden"  name="updated_by" id="updated_by" value="{{auth()->id()}}">
                    <input type="hidden" name="product_id" id="product_id">

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
                            </div>
                            <div class="row">
                                @if($tbl_setting->product_code == '0')
                                <div class="col-4">
                                    <label for="">Product Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Product Code"
                                        maxlength="25" name="product_code" id="product_code">
                                    <span class="error badge text-danger" id="product_codeError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->product_name == '0')
                                <div class="col-4">
                                    <label for="">Name <span class="text-danger">*</span></label>
                                    <input class="form-control"  placeholder="Enter Product Details" id="product_name" name="product_name">
                                    <span class="error badge text-danger" id="product_nameError"></span>

                                </div>
                                @endif
                                @if($tbl_setting->company_name == '0')
                                <div class="col-md-4">
                                    <label for="">Company </label>
                                    <input type="text" class="form-control" placeholder="Enter Company Name" name="Company" id="Company" autocomplete="off">
                                    <div id="companyListName" class="dropdown-menu" style="display: none; position: static;"></div>
                                    <span class="error badge text-danger" id="CompanyError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->color == '0')
                                <div class="col-md-4">
                                    <label for="">Color </label>
                                    <input class="form-control"  placeholder="Enter Color" id="Color" name="Color" autocomplete="off">
                                    <div id="colorListName" class="dropdown-menu" style="display: none; position: static;"></div>
                                    <span class="error badge text-danger" id="ColorError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->Numbers == '0')
                                <div class="col-md-4">
                                    <label for="">Number  </label>
                                    <input class="form-control"  placeholder="Enter NUmber" id="Number" name="Number">
                                    <span class="error badge text-danger" id="NumberError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->CT == '0')
                                <div class="col-md-4">
                                    <label for="">CT (Center Thickness) </label>
                                    <input class="form-control"  placeholder="Enter Number" id="CT" name="CT">
                                    <div id="ctListName" class="dropdown-menu" style="display: none; position: static;"></div>
                                    <span class="error badge text-danger" id="CTError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->type == '0')
                                <div class="col-md-4">
                                    <label for="">Type </label>
                                    <input class="form-control"  placeholder="Enter Type" id="Type" name="Type" autocomplete="off">
                                    <div id="typeListName" class="dropdown-menu" style="display: none; position: static;"></div>
                                    <span class="error badge text-danger" id="TypeError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->Material == '0')
                                <div class="col-md-4">
                                    <label for="">Materials  </label>
                                    <input class="form-control"  placeholder="Enter Materials" id="Materials" name="Materials" autocomplete="off">
                                    <div id="materialListName" class="dropdown-menu" style="display: none; position: static;"></div>
                                    <span class="error badge text-danger" id="MaterialsError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->Validity_In_Days == '0')
                                <div class="col-md-4">
                                    <label for="">Validity In Days </label>
                                    <input class="form-control"  placeholder="Enter Validity" id="Validity" name="Validity">
                                    <div id="validityListName" class="dropdown-menu" style="display: none; position: static;"></div>
                                    <span class="error badge text-danger" id="ValidityError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->Modality == '0')
                                <div class="col-md-4">
                                    <label for="">Modality </label>
                                    <input class="form-control"  placeholder="Enter Modality" id="Modality" name="Modality">
                                    <span class="error badge text-danger" id="ModalityError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->WC == '0')
                                <div class="col-md-4">
                                    <label for="">WC (Water Content)  </label>
                                    <input class="form-control"  placeholder="Enter WC" id="WC" name="WC">
                                    <span class="error badge text-danger" id="WCError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->Dk_t == '0')
                                <div class="col-md-4">
                                    <label for="">Dk/t (Permeability)  </label>
                                    <input class="form-control"  placeholder="Enter Validity" id="Dk_t" name="Dk_t">
                                    <span class="error badge text-danger" id="Dk_tError"></span>
                                </div>
                                @endif
                                @if($tbl_setting->quality == '0')
                                <div class="col-md-4">
                                    <label for="">Quality </label>
                                    <input type="text" class="form-control" placeholder="Enter Quality" name="Quality" id="Quality">
                                    <span class="error badge text-danger" id="QualityError"></span>
                                </div>
                                @endif
                                 @if($tbl_setting->Description == '0')
                                <div class="col-md-12">
                                    <label for="">Description </label>
                                    <input type="text" class="form-control" placeholder="Enter Description" name="Description" id="Description">
                                    <span class="error badge text-danger" id="DescriptionError"></span>
                                </div>
                                @endif
                                <div class="col-md-4">
                                    <label for="">Track Inventory </label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Track_Inventory" id="inlineRadio1" value="1" checked>
                                          <label class="form-check-label" for="inlineRadio1">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Track_Inventory" id="inlineRadio2" value="0">
                                          <label class="form-check-label" for="inlineRadio2">No</label>
                                        </div>
                                    </div>
                                    <span class="error badge text-danger" id="Track_InventoryError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Allow Negative Inventory </label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Allow_Negative_Inventory" id="inlineRadio3" value="1" checked>
                                          <label class="form-check-label" for="inlineRadio3">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="Allow_Negative_Inventory" id="inlineRadio4" value="0">
                                          <label class="form-check-label" for="inlineRadio4">No</label>
                                        </div>
                                    </div>
                                    <span class="error badge text-danger" id="Allow_Negative_InventoryError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Purchase Base Price </label>
                                    <input type="text" class="form-control" placeholder="Enter Purchase Base Price" name="Purchase_Base_Price" id="Purchase_Base_Price">
                                    <span class="error badge text-danger" id="Purchase_Base_PriceError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Purchase Price </label>
                                    <input type="text" class="form-control" placeholder="Enter Purchase  Price" name="Purchase_Price" id="Purchase_Price">
                                    <span class="error badge text-danger" id="Purchase_PriceError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Retail  Price </label>
                                    <input type="text" class="form-control" placeholder="Enter Retail   Price" name="Retail_Price" id="Retail_Price">
                                    <span class="error badge text-danger" id="Retail_PriceError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">B2B  Price </label>
                                    <input type="text" class="form-control" placeholder="Enter Retail   Price" name="BB_Price" id="BB_Price">
                                    <span class="error badge text-danger" id="BB_PriceError"></span>
                                </div> 
                                <div class="col-md-4">
                                    <label for="">Main Image</label>
                                     <input type="file" class="form-control" name="main_image" id="main_image"   onchange="return imageValidation()">
                                     <div id="main_imagePreview"></div>
                                    
                                </div> 
                                <div class="col-md-8">
                                     <label for="">Upload Multiple Images </label>
                                     <input type="file" class="form-control" id="images" name="images[]" multiple>
                                      <div id="preview"></div>
                                     <div id="product_image_gallery" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                                     <p id="error" style="color:red;"></p>
                                </div>
                            </div>
                        </div>
                        
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
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
    
    function setupAutocomplete(inputId, dropdownId, routeName, dataKey) {
        let isSelected = false;

        $(`#${inputId}`).on("keyup", function () {
            let query = $(this).val().trim();
            let product_type = "Lens";
            isSelected = false;

            if (query.length > 2) {
                $.ajax({
                    url: routeName,
                    type: "GET",
                    data: { name: query, product_type: product_type },
                    success: function (data) {
                        let dropdown = $(`#${dropdownId}`);
                        dropdown.empty();

                        if (data.length > 0) {
                            data.forEach(item => {
                                dropdown.append(
                                    `<a class="dropdown-item" data-input="${inputId}">${item[dataKey]}</a>`
                                );
                            });
                            dropdown.show();
                        } else {
                            dropdown.hide();
                        }
                    }
                });
            } else {
                $(`#${dropdownId}`).hide();
            }
        });

        $(document).on("click", `.dropdown-item[data-input="${inputId}"]`, function () {
            $(`#${inputId}`).val($(this).text());
            isSelected = true;
            $(`#${dropdownId}`).hide();
        });

        $(document).click(function (e) {
            if (!$(e.target).closest(`#${inputId}, #${dropdownId}`).length) {
                $(`#${dropdownId}`).hide();
            }
        });

        
    }

    setupAutocomplete("Company", "companyListName", "{{ route('admin.companyname-dropdown') }}", "brand_name");
    setupAutocomplete("Color", "colorListName", "{{ route('admin.colorname-dropdown') }}", "color_name");
    setupAutocomplete("Size", "sizeListName", "{{ route('admin.sizename-dropdown') }}", "size_name");
    setupAutocomplete("Type", "typeListName", "{{ route('admin.typename-dropdown') }}", "type_name");
    setupAutocomplete("Shape", "shapeListName", "{{ route('admin.shapename-dropdown') }}", "shape_name");
    setupAutocomplete("Material", "materialListName", "{{ route('admin.materialname-dropdown') }}", "material_name");
    setupAutocomplete("CT", "ctListName", "{{ route('admin.ctname-dropdown') }}", "ct_name");
    setupAutocomplete("Validity", "validityListName", "{{ route('admin.validityname-dropdown') }}", "validity_name");
});
</script>
<script>
function openCreateModal()
{
    document.getElementById('modalTitle').innerText = 'Add New Lens';
    document.getElementById('productForm').action = '{{ route('admin.lensproduct.store') }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('productForm').reset();
    $('#productModal').modal('show');
}

function openEditModal(lens) {
    var lens = JSON.parse(decodeURIComponent(lens));
    document.getElementById('modalTitle').innerText = 'Edit Lens Product';
    document.getElementById('productForm').action = `{{ url('/lensproduct/` + lens.id + `') }}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('Purchase_Base_Price').value = lens.Purchase_Base_Price;
    document.getElementById('Purchase_Price').value = lens.Purchase_Price;
    document.getElementById('Retail_Price').value = lens.Retail_Price;
    document.getElementById('BB_Price').value = lens.BB_Price;
    document.querySelector(`input[name="Track_Inventory"][value="${lens.Track_Inventory}"]`).checked = true;
    document.querySelector(`input[name="Allow_Negative_Inventory"][value="${lens.Allow_Negative_Inventory}"]`).checked = true;
    document.getElementById('uid').value = lens.id;
    document.getElementById('product_id').value = lens.product_id;

    if (document.getElementById('product_code')) document.getElementById('product_code').value = lens.product_code || '';
    if (document.getElementById('product_name')) document.getElementById('product_name').value = lens.product_name || '';
    if (document.getElementById('Company')) document.getElementById('Company').value = lens.Company || '';
    if (document.getElementById('Quality')) document.getElementById('Quality').value = lens.Quality || '';
    if (document.getElementById('Color')) document.getElementById('Color').value = lens.Color || '';
    if (document.getElementById('Number')) document.getElementById('Number').value = lens.Number || '';
    if (document.getElementById('CT')) document.getElementById('CT').value = lens.CT || '';
    if (document.getElementById('Type')) document.getElementById('Type').value = lens.Type || '';
    if (document.getElementById('Materials')) document.getElementById('Materials').value = lens.Materials || '';
    if (document.getElementById('Validity')) document.getElementById('Validity').value = lens.Validity || '';
    if (document.getElementById('Modality')) document.getElementById('Modality').value = lens.Modality || '';
    if (document.getElementById('WC')) document.getElementById('WC').value = lens.WC || '';
    if (document.getElementById('Dk_t')) document.getElementById('Dk_t').value = lens.Dk_t || '';
    if (document.getElementById('Description')) document.getElementById('Description').value = lens.Description || '';
    
    try {
        var images = JSON.parse(lens.product_image);
        var gallery = document.getElementById('product_image_gallery');
        gallery.innerHTML = ''; // Clear any previous images
    
        if (Array.isArray(images) && images.length > 0) {
            images.forEach(function(imageName) {
                var imagePath = `/public/uploads/lens/product/${lens.product_id}/${imageName}`;
                var imgElement = document.createElement('img');
                imgElement.src = imagePath;
                imgElement.alt = "Product Image";
                imgElement.style.maxWidth = '120px';
                imgElement.style.border = '1px solid #ccc';
                imgElement.style.borderRadius = '4px';
                gallery.appendChild(imgElement);
            });
        } else {
            gallery.innerHTML = '<p>No images available.</p>';
        }
    } catch (e) {
        console.error('Error parsing product_image:', e);
        document.getElementById('product_image_gallery').innerHTML = '<p>Image load error.</p>';
    }
    
    
    var mainImagePreview = document.getElementById('main_imagePreview');
    mainImagePreview.innerHTML = ''; 
    if (lens.main_image) {
        var mainImagePath = `/public/uploads/lens/product/${lens.product_id}/${lens.main_image}`;
        var mainImgElement = document.createElement('img');
        mainImgElement.src = mainImagePath;
        mainImgElement.alt = "Main Image";
        mainImgElement.style.maxWidth = '150px';
        mainImgElement.style.border = '2px solid #666';
        mainImgElement.style.borderRadius = '6px';
        mainImagePreview.appendChild(mainImgElement);
    }
    

    $('#productModal').modal('show');
}



$("#productForm").submit(function(e)
{
    e.preventDefault(); 
    
    let isValid = true;
    let class_name = '';

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let product_code = document.getElementById("product_code" + class_name).value.trim();
    let product_name = document.getElementById("product_name" + class_name);


    if (product_code.length < 3) {
        document.getElementById("product_codeError" + class_name).textContent = "Product Code must be at least 8 characters.";
        document.getElementById("product_code" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (product_name=='') {
        document.getElementById("product_nameError" + class_name).textContent = "Product Name required";
        document.getElementById("product_name" + class_name).classList.add("is-invalid");
        isValid = false;
    }




    if (!isValid) {
        return;
    }

    let form = $("#productForm")[0];
    let data = new FormData(form);

    $.ajax({
        type: 'POST',
        url: $(this).attr("action"),
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
            location.reload();
        } else {
            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

            $.each(response.error, function(index, value) {
                if (value.includes("product_code")) {
                    $("#product_codeError").text(value);
                    $("#product_code").addClass("is-invalid");
                }
                if (value.includes("product_name")) {
                    $("#product_nameError").text(value);
                    $("#product_name").addClass("is-invalid");
                }
            });
        }
    }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error: " + textStatus + " - " + errorThrown);
    });
});

/***********************************************************/
        
var start = moment().startOf('month');
var end = moment().endOf('month');

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
    ranges: {},         
}, function(start, end) {
    cb(start, end);
});

// Handle Apply Event
$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
    cb(picker.startDate, picker.endDate);
});

// Initial Callback
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
            url: "{{ route('admin.lensproduct.search') }}",
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
                "data": "product_id",
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
                "data": "price",
                orderable: false,
            },
            {
                "data": "Inventory",
                orderable: false,
            },
            {
                "data": "Neg_Inventory",
                orderable: false,
            },

            {
                "data": "created_at",
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
                render: function(data, type, full) 
                {
                    return (`<div class="dropdown"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button><div class="dropdown-menu">`+
                                '<a class="dropdown-item" href="#" onclick="openEditModal(`' + encodeURIComponent(JSON.stringify(full)) + '`)">Edit</a>'+
                                `<a class="action-delete dropdown-item" href="#"  data-id="` + full['id'] + `">Delete</a>`+
                            `</div></div>`
                    );
                }    
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
    
    
    $("table").delegate(".action-delete", "click", function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            Swal.fire({
                title: "{{ __('Are you sure ?') }}",
                text: "{{ __('You would not be able to revert this!') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-2'
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('/lensproduct') }}" + '/' + id + '/destroy',
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            showResponseMessage(data);
                        },
                        error: function(reject) {
                            if (reject.status === 422) {
                                let errors = reject.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    toastr['warning'](value[0],
                                        "{{ __('locale.labels.attention') }}", {
                                            closeButton: true,
                                            positionClass: 'toast-top-right',
                                            progressBar: true,
                                            newestOnTop: true,
                                            rtl: isRtl
                                        });
                                });
                            } else {
                                toastr['warning'](reject.responseJSON.message,
                                    "{{ __('locale.labels.attention') }}", {
                                        closeButton: true,
                                        positionClass: 'toast-top-right',
                                        progressBar: true,
                                        newestOnTop: true,
                                        rtl: isRtl
                                    });
                            }
                        }
                    })
                }
            })
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
document.getElementById('images').addEventListener('change', function (e) {
    const files = e.target.files;
    const preview = document.getElementById('preview');
    const error = document.getElementById('error');
    preview.innerHTML = '';
    error.innerText = '';

    const maxFiles = 10;
    const maxSize = 2 * 1024 * 1024; // 2MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (files.length > maxFiles) {
        $.toaster({ priority : 'danger', title : 'Opps..!' , message : 'You can upload a maximum of ${maxFiles} images.' });
        e.target.value = ''; // Clear input
        return;
    }

    Array.from(files).forEach(file => {
        if (!allowedTypes.includes(file.type)) {
            $.toaster({ priority : 'danger', title : 'Opps..!' , message : 'File "${file.name}" is not a valid image type.' });
            e.target.value = '';
            return;
        }

        if (file.size > maxSize) {
            $.toaster({ priority : 'danger', title : 'Opps..!' , message : 'File "${file.name}" exceeds 2MB size limit.' });
            e.target.value = '';
            return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.width = 100;
            img.style.margin = "5px";
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});

// Optional: prevent form submission if needed
document.getElementById('imageForm').addEventListener('submit', function (e) {
    const files = document.getElementById('images').files;
    if (files.length === 0) {
        e.preventDefault();
        document.getElementById('error').innerText = 'Please select at least one image.';
        
    }
});
</script>
<script>
  const fileInput = document.getElementById('main_image');
  const preview = document.getElementById('main_imagePreview');

  function clearFileInput() {
    fileInput.value = '';
    preview.innerHTML = '';
  }

  function imageValidation() {
    const file = fileInput.files[0];

    if (!file) {
      clearFileInput();
      return;
    }

    const allowedExtensions = /\.(jpg|jpeg|png)$/i;
    const maxSize = 10 * 1024 * 1024; // 10MB

    if (!allowedExtensions.test(file.name)) {
      Swal.fire({
        icon: 'error',
        title: 'Invalid file type',
        text: 'Please upload files with extensions: .jpeg, .jpg, .png,  only.',
      });
      clearFileInput();
      return;
    }

    if (file.size > maxSize) {
      Swal.fire({
        icon: 'error',
        title: 'File too large',
        text: 'Maximum file size is 10MB.',
      });
      clearFileInput();
      return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
      let content = '';
        content = `<img src="${e.target.result}" alt="Image Preview" width= "150" height= "150"/>`;
      

      // Add remove button overlay
      content += `<button class="remove-btn" title="Remove Preview">&times;</button>`;

      preview.innerHTML = content;

      // Attach event listener to remove button
      const removeBtn = preview.querySelector('.remove-btn');
      if (removeBtn) {
        removeBtn.addEventListener('click', clearFileInput);
      }
    };

    reader.readAsDataURL(file);
  }

  fileInput.addEventListener('change', imageValidation);
</script>




@endsection
