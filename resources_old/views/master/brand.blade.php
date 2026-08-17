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
                        <a href="#" class=" btn" data-toggle="modal" data-target="#brandModal">
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
                                <th class="wd-15p">Sr.No</th>
                                <th class="wd-15p">Company / Brand Name</th>
                                <!--<th class="wd-15p">Product</th>-->
                                <!--<th class="wd-15p">By One Get One</th>-->
                                <!--<th class="wd-15p">Store</th>-->
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
                <form id="brandForm" method="POST"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="uid" id="uid">

                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li>Fields marked with * are mandatory.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="row">-->
                            <!--    <div class="col-md-12">-->
                            <!--          <label>Products <span class="text-danger">*</span></label>-->
                            <!--          <select class="form-control select1 product-type" name="product_type" id="product_type">-->
                            <!--            <option value="">Select Product Type</option>-->
                            <!--            <option value="Frame">Frame</option>-->
                            <!--            <option value="Glass">Glass</option>-->
                            <!--            <option value="Goggles">Goggles</option>-->
                            <!--            <option value="Lens">Contact Lens</option>-->
                            <!--            <option value="Solution">Solution</option>-->
                            <!--            <option value="Other">Other</option>-->
                            <!--          </select>-->
                            <!--        </div>-->
                            <!--        <span class="error badge text-danger" id="product_typeError"></span>-->
                            <!--</div>-->
                            <br>
                            
                            <div class="row">    
                                <div class="col-12">
                                    <label for="">Brand  / Company Name <span class="text-danger">*</span></label>
                                    <input class="form-control"  placeholder="Enter Brand / Company Name" id="brand_name" name="brand_name">
                                    <span class="error badge text-danger" id="brand_nameError"></span>

                                </div>
                                
                            </div>
                            <!--<div class="row">    -->
                            <!--    <div class="col-12">-->
                            <!--        <label for="">By One Get One</label>-->
                            <!--        <div class="d-flex">-->
                            <!--            <div class="form-check form-check-inline">-->
                            <!--              <input class="form-check-input" type="radio" name="by_one_get_one" id="inlineRadio1" value="1" checked>-->
                            <!--              <label class="form-check-label" for="inlineRadio1">Yes</label>-->
                            <!--            </div>-->
                            <!--            <div class="form-check form-check-inline">-->
                            <!--              <input class="form-check-input" type="radio" name="by_one_get_one" id="inlineRadio2" value="0">-->
                            <!--              <label class="form-check-label" for="inlineRadio2">No</label>-->
                            <!--            </div>-->
                            <!--        </div>-->
                            <!--        <span class="error badge text-danger" id="by_one_get_oneError"></span>-->

                            <!--    </div>-->
                                
                            <!--</div>-->
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
    $('.select1').select2({
      allowClear: true,
      width:'100%',
    });
});
</script>
<script>
function openCreateModal()
{
    document.getElementById('modalTitle').innerText = 'Brand / Company';
    $('#brandModal').modal('show');
}

function openEditModal(brand) {

    var brand = JSON.parse(decodeURIComponent(brand));

    document.getElementById('modalTitle').innerText = 'Edit Brand / Company';

    document.getElementById('brand_name').value = brand.brand_name || '';

    document.getElementById('uid').value = brand.brand_id || '';

    // check only if field exists
    let byOneGetOne = document.querySelector(
        `input[name="by_one_get_one"][value="${brand.byonegetone}"]`
    );

    if(byOneGetOne){
        byOneGetOne.checked = true;
    }

    $('#brandModal').modal('show');
}


$("#brandForm").submit(function(e)
{
    e.preventDefault(); 
    
    let isValid = true;
    let class_name = '';

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    // let brand_name = document.getElementById("brand_name" + class_name).value.trim();
    // let product_type = document.getElementById("product_type" + class_name).value.trim();


    // if (brand_name == '') {
    //     document.getElementById("brand_nameError" + class_name).textContent = "Company name required.";
    //     document.getElementById("brand_name" + class_name).classList.add("is-invalid");
    //     isValid = false;
    // }

    
    // if (product_type == '') {
    //     document.getElementById("product_typeError" + class_name).textContent = "Select Product.";
    //     document.getElementById("product_type" + class_name).classList.add("is-invalid");
    //     isValid = false;
    // }


    

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
        if (response) {
            $.toaster({
                priority: 'success',
                title: response.success,
                message: ''
            });
            location.reload();
        } else {
            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

        }
    }
    }).fail(function(jqXHR, textStatus, errorThrown) {

    console.log("Status Code:", jqXHR.status);
    console.log("Response:", jqXHR.responseText);
    console.log("Error:", errorThrown);

});
});

/***********************************************************/
        

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
                "data": "brand_name",
                orderable: false,
            },
            
            // {
            //     "data": "product_type",
            //     orderable: false,
            // },
            // {
            //     "data": "by_one_get_one",
            //     orderable: false,
            // },
            
            // {
            //     "data": "store_name",
            //     orderable: false,
            // },


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
