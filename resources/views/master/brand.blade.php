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
                        <h3>Brand / Company Master</h3>
                        <input type="text" class="form-control input" placeholder="Search By,Company Name" id="search"
                                name="search" style="width:320px">
                        <a href="javascript:void(0)" class="btn" onclick="openCreateModal()">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add New Brand / Company
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
                                <th class="wd-10p">Sr.No</th>
                                <th class="wd-15p">Logo / Image</th>
                                <th class="wd-20p">Company / Brand Name</th>
                                <!--<th class="wd-15p">Product</th>-->
                                <!--<th class="wd-15p">By One Get One</th>-->
                                <!--<th class="wd-15p">Store</th>-->
                                <th class="wd-15p">Created At</th>
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
<div class="modal fade" data-backdrop="static" id="brandModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Brand / Company</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="brandForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="uid" id="uid">

                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger p-2 mb-2" style="font-size: 12px;">
                                        <span>Fields marked with <span class="text-danger">*</span> are mandatory.</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">    
                                <div class="col-12">
                                    <label for="brand_name">Brand / Company Name <span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Enter Brand / Company Name" id="brand_name" name="brand_name">
                                    <span class="error badge text-danger" id="brand_nameError"></span>
                                </div>
                            </div>

                            <div class="row mt-3">    
                                <div class="col-12">
                                    <label for="brand_image">Brand Logo / Image</label>
                                    <input type="file" class="form-control" id="brand_image" name="image" accept="image/*" onchange="previewBrandImage(this)">
                                    <small class="text-muted d-block" style="font-size: 11px;">Allowed: JPG, PNG, WEBP, SVG (Max: 2MB)</small>
                                    
                                    <div id="brandImagePreviewContainer" class="mt-2" style="display: none;">
                                        <div class="position-relative d-inline-block">
                                            <img id="brandImagePreview" src="" alt="Brand Logo Preview" style="max-height: 70px; max-width: 130px; object-fit: contain; border: 1px solid #e2e8f0; padding: 4px; border-radius: 6px; background: #f8f9fa;">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: -6px; right: -6px; border-radius: 50%; padding: 2px 6px; font-size: 10px; line-height: 1;" onclick="removeBrandImagePreview()" title="Remove Image">&times;</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Submit">Submit
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
    $('.select1').select2({
      allowClear: true,
      width:'100%',
    });
});

function previewBrandImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#brandImagePreview').attr('src', e.target.result);
            $('#brandImagePreviewContainer').show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeBrandImagePreview() {
    $('#brand_image').val('');
    $('#brandImagePreview').attr('src', '');
    $('#brandImagePreviewContainer').hide();
}
</script>
<script>
function openCreateModal()
{
    document.getElementById('modalTitle').innerText = 'Add Brand / Company';
    document.getElementById('uid').value = '';
    document.getElementById('brand_name').value = '';
    removeBrandImagePreview();
    $('#brandModal').modal('show');
}

function openEditModal(brand) {

    var brand = JSON.parse(decodeURIComponent(brand));

    document.getElementById('modalTitle').innerText = 'Edit Brand / Company';
    document.getElementById('brand_name').value = brand.brand_name || '';
    document.getElementById('uid').value = brand.brand_id || '';

    $('#brand_image').val('');
    if (brand.image) {
        var imgUrl = "{{ asset('') }}" + brand.image;
        $('#brandImagePreview').attr('src', imgUrl);
        $('#brandImagePreviewContainer').show();
    } else {
        removeBrandImagePreview();
    }

    $('#brandModal').modal('show');
}


$("#brandForm").submit(function(e)
{
    e.preventDefault(); 
    
    let isValid = true;
    let brand_name = document.getElementById("brand_name").value.trim();

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    if (brand_name === '') {
        document.getElementById("brand_nameError").textContent = "Brand / Company name is required.";
        document.getElementById("brand_name").classList.add("is-invalid");
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    let form = $("#brandForm")[0];
    let data = new FormData(form);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.brand-stored') }}",
        data: data,
        dataType: "JSON",
        processData: false,
        contentType: false,
        success: function(response) {
            if (response && response.success) {
                $('#brandModal').modal('hide');
                $.toaster({
                    priority: 'success',
                    title: response.success,
                    message: ''
                });
                dataListView.draw();
            } else {
                document.querySelectorAll(".error").forEach(el => el.textContent = "");
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.status === 422) {
                let errors = jqXHR.responseJSON.errors;
                if (errors.brand_name) {
                    $('#brand_nameError').text(errors.brand_name[0]);
                    $('#brand_name').addClass('is-invalid');
                }
                if (errors.image) {
                    alert(errors.image[0]);
                }
            } else {
                alert('Something went wrong. Please try again.');
            }
        }
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
            url: "{{ route('admin.brand-list') }}",
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
                "data": "brand_image",
                orderable: false,
            },
            {
                "data": "brand_name",
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
                render: function(data, type, full) 
                {
                    return (`<div class="dropdown"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button><div class="dropdown-menu">`+
                                '<a class="dropdown-item" href="#" onclick="openEditModal(`' + encodeURIComponent(JSON.stringify(full)) + '`)">Edit</a>'+
                                `<a class="action-delete dropdown-item" href="#"  data-id="` + full['brand_id'] + `">Delete</a>`+
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
                        url: "{{ url('/brand') }}" + '/' + id + '/destroy',
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



@endsection
