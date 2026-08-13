@extends('layouts.master')
@section('styles')
<style>
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
                        <h3>Package</h3>
                        <input type="text" class="form-control input" placeholder="Search By,Product,Package Name" id="search"
                                name="search" style="width:320px">
                        <a href="#" class=" btn" data-toggle="modal" data-target="#packageModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Create Package
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
                                    <div class="spinner-border ms-auto text-success spinner-grow" role="status"   aria-hidden="true"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">Sr.No</th>
                                <th class="wd-15p">Lens Type</th>
                                <th class="wd-15p">Package Name</th>
                                <th class="wd-15p">Image</th>
                                <th class="wd-10p">Description</th>
                                <th class="wd-10p">Price </th>
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
<div class="modal fade" data-backdrop="static" id="packageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Package</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="packageForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="uid" id="uid">
                    <input type="hidden"  name="updated_by" id="updated_by" value="{{auth()->id()}}">

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
                               
                                <div class="col-md-4">
                                    <label for="">Product Code<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Product Code " name="product_code" id="product_code">
                                    <span class="error badge text-danger" id="product_codeError"></span>
                                </div>
                                <div class="col-4">
                                    <label for="">Lens Type <span class="text-danger">*</span></label><br>
                                    <select class="form-control select lens-type" id="lens_type" name="lens_type">
                                        <option value="">Select Lens Type</option>
                                        <option value="Single Vision">Single Vision</option>
                                        <option value="Bifocal/Progressive">Bifocal/Progressive</option>
                                        <option value="Zero Power">Zero Power</option>
                                        <option value="Reading Power">Reading Power</option>
                                    </select>
                                    <span class="error badge text-danger" id="lens_typeError"></span>

                                </div>
                                <div class="col-md-4">
                                    <label for="">Lens Package Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Lens Package Name " name="package_name" id="package_name">
                                    <span class="error badge text-danger" id="package_nameError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Package Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="images" name="images[]" multiple>
                                      <div id="preview"></div>
                                     <div id="package_image_gallery" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                                     <span class="error badge text-danger" id="imageError"></span>
                                </div>
                               
                                
                               
                                
                                <div class="col-md-4">
                                    <label for="">Lens Package Price <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Package Price" name="lens_price" id="lens_price">
                                    <span class="error badge text-danger" id="lens_priceError"></span>
                                </div>
                                
                                 <div class="col-md-4">
                                    <label for="">IS Coating <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="is_coating" id="inlineRadio3" value="1">
                                          <label class="form-check-label" for="inlineRadio3">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="is_coating" id="inlineRadio4" value="0">
                                          <label class="form-check-label" for="inlineRadio4">No</label>
                                        </div>
                                    </div>
                                    <span class="error badge text-danger" id="is_coatingError"></span>
                                </div>
                                
                                 <div class="col-md-12" id="coat-div" style="display:none">
                                    <table border="1" class="table basic w-100" id="productTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                              <th style="color:#000">Coating Name</th>
                                              <th style="color:#000">Coating Price</th>
                                              <th style="color:#000">Action</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                        </tbody>
                                    </table>
                                    <button type="button" onclick="addRow()">Add More</button>
                                </div>
                                
                               
                                
                                <div class="col-md-12">
                                    <label for=""> Package Description <span class="text-danger">*</span></label>
                                    <textarea id="myCKEditor" name="package_details"></textarea>
                                    <span class="error badge text-danger" id="package_detailsError"></span>
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
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
let myCKEditorInstance;
ClassicEditor.create(document.querySelector('#myCKEditor')).then(editor => {
    myCKEditorInstance = editor;
});
</script>
<script>
$(document).ready(function() {
    $('.select').select2({
      allowClear: true
    });
});
CKEDITOR.replace('myCKEditor');
</script>
<script>
function openCreateModal()
{
    document.getElementById('modalTitle').innerText = 'Add New Package';
    $('#packageModal').modal('show');
}

function openEditModal(pack) {

    pack = JSON.parse(decodeURIComponent(pack));

    $('#modalTitle').text('Edit Package');

    $('#uid').val(pack.package_id || '');
    $('#lens_type').val(pack.lens_type).trigger('change');
    $('#package_name').val(pack.package_name || '');
    $('#lens_price').val(pack.lens_price || '');
    $('#product_code').val(pack.product_code || '');

    /* ---------- CKEditor ---------- */
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.myCKEditor) {
        CKEDITOR.instances.myCKEditor.setData(pack.package_details || '');
    } else {
        $('#myCKEditor').val(pack.package_details || '');
    }

    /* ---------- IS COATING ---------- */
    $('#productTable tbody').html(''); // clear old rows

    if (pack.is_coating == 1) {
        $('#inlineRadio3').prop('checked', true);
        $('#coat-div').show();

        if (pack.coatings && pack.coatings.length > 0) {
            pack.coatings.forEach(function (coat) {
                addRow(coat.coating_name, coat.coating_price);
            });
        } else {
            addRow(); // at least one empty row
        }
    } else {
        $('#inlineRadio4').prop('checked', true);
        $('#coat-div').hide();
    }

    /* ---------- Images ---------- */
    let gallery = $('#package_image_gallery');
    gallery.html('');

    try {
        let images = JSON.parse(pack.package_image);

        if (Array.isArray(images)) {
            images.forEach(function (img) {
                gallery.append(`
                    <img src="/public/uploads/glass/product/${pack.pid}/${img}"
                         style="max-width:120px;border:1px solid #ccc;margin:5px;">
                `);
            });
        }
    } catch (e) {
        console.error(e);
        gallery.html('<p>No images available</p>');
    }

    $('#packageModal').modal('show');
}




$("#packageForm").submit(function(e) 
{
    e.preventDefault();

    let isValid = true;

    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");

    let lens_type = $("select[name='lens_type']").val().trim();
    let package_name = $("#package_name").val().trim();
    let images = $("#images")[0].files;
    let is_coating = $("input[name='is_coating']:checked").val();
    let lens_price = $("#lens_price").val().trim();
    let package_details = $("#myCKEditor").val().trim();
    let uid = $("#uid").val().trim();
    
    let product_code = $("#product_code").val().trim();


    
    if (product_code.length < 3) {
        $("#product_codeError").text("product code must be at least 3 characters.");
        $("#product_code").addClass("is-invalid");
        isValid = false;
    }

    if (!lens_type) {
        $("#lens_typeError").text("Please select a lens type.");
        $("select[name='lens_type']").addClass("is-invalid");
        isValid = false;
    }

    if (package_name.length < 3) {
        $("#package_nameError").text("Package name must be at least 3 characters.");
        $("#package_name").addClass("is-invalid");
        isValid = false;
    }
    
    if(uid == '')
    {
        if (images.length === 0) {
            $("#imageError").text("Please select  package image.");
            $("#images").addClass("is-invalid");
            isValid = false;
        }
    }

    

    if (is_coating === undefined) {
        $("#is_coatingError").text("Please select coating option.");
        $("input[name='is_coating']").addClass("is-invalid");
        isValid = false;
    }
    
    if (is_coating == 1) {

        let hasRow = false;
    
        $("input[name='coating_name[]']").each(function () {
            if ($(this).val().trim() !== "") {
                hasRow = true;
            }
        });
    
        if (!hasRow) {
            $("#coatingRowError").text("Please add at least one coating name and price.");
            isValid = false;
        }
    }



    if (!/^\d+(\.\d{1,2})?$/.test(lens_price)) {
        $("#lens_priceError").text("Please enter a valid lens package price.");
        $("#lens_price").addClass("is-invalid");
        isValid = false;
    }



    if (!isValid) return;

    let form = $("#packageForm")[0];
    let data = new FormData(form);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.package-store') }}",
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
                $(".error").text("");
                $(".is-invalid").removeClass("is-invalid");
                $.each(response.error, function(index, value) {
                    $("#" + index + "Error").text(value);
                    $("#" + index).addClass("is-invalid");
                });
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error: " + textStatus + " - " + errorThrown);
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
            url: "{{ route('admin.package.search') }}",
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
                "data": "lens_type",
                orderable: false,
            },
            {
                "data": "package_name",
                orderable: false,
            },
            {
                "data": "pimage",
                orderable: false,
            },
            {
                "data": "package_details",
                orderable: false,
            },
            {
                "data": "lens_price",
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
                    return (`<div class="dropdown"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button><div class="dropdown-menu">`+
                                '<a class="dropdown-item" href="#" onclick="openEditModal(`' + encodeURIComponent(JSON.stringify(full)) + '`)">Edit</a>'+
                                `<a class="action-delete dropdown-item" href="#"  data-id="` + full['package_id'] + `">Delete</a>`+
                            `</div></div>`
                    );
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
    
    $('.select1').on('change', function() 
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
                        url: "{{ url('/package') }}" + '/' + id + '/destroy',
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
                            }
                            else 
                            {
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
    
    
    document.getElementById('images').addEventListener('change', function (e) {
        const files = e.target.files;
        const preview = document.getElementById('preview');
        const error = document.getElementById('error');
        preview.innerHTML = '';
        error.innerText = '';
    
        const maxFiles = 10;
        const maxSize = 2 * 1024 * 1024; 
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
        if (files.length > maxFiles) {
            $.toaster({ priority : 'danger', title : 'Opps..!' , message : 'You can upload a maximum of ${maxFiles} images.' });
            e.target.value = ''; 
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
    

        
</script>
<script>
$(document).ready(function() {
   $('input[name="is_coating"]').on('change', function () {
    
        if ($(this).val() == 1) {
            $('#coat-div').show();
            if ($('#productTable tbody tr').length === 0) {
                addRow();
            }
        } else {
            $('#coat-div').hide();
            $('#productTable tbody').html('');
        }
    });
});
</script>

<script>
function addRow() {
  const table = document.getElementById("productTable").getElementsByTagName("tbody")[0];

  const row = table.insertRow();

  const nameCell = row.insertCell(0);
  const priceCell = row.insertCell(1);
  const actionCell = row.insertCell(2);

  nameCell.innerHTML = `<input type="text" class="form-control" name="coating_name[]" placeholder="Name">`;
  priceCell.innerHTML = `<input type="text" class="form-control" name="coating_price[]" placeholder="Price">
  
  `;
  actionCell.innerHTML = `<button onclick="removeRow(this)">Remove</button>`;
}

function removeRow(button) {
  const row = button.parentNode.parentNode;
  row.remove();
}
</script>







@endsection
