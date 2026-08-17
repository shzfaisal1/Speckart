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
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Tax Master</h3>
                        <input type="text" class="form-control input" placeholder="Search By,Company Name,Contact,gst no,state" id="search"
                                name="search" style="width:320px">
                        <a href="#" class=" btn" data-toggle="modal" data-target="#taxModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Create Tax Master
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
                                <th class="wd-15p">#ID</th>
                                <th class="wd-15p">Product Type	</th>
                                <th class="wd-15p">HSN/SAC Code	</th>
                                <th class="wd-15p">Percentage</th>
                                <th class="wd-10p">Description</th>
                                <th class="wd-10p">Created At</th>
                                <th class="wd-10p">Last Update</th>
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
<div class="modal fade" data-backdrop="static" id="taxModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Tax Master</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="taxForm" method="POST" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="uid">
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
                                <div class="col-6">
                                    <label for="">Product Type <span class="text-danger">*</span></label><br>
                                    <select class="form-control select-tax" style="height: 32px !important;" id="product_type" name="product_type">
                                          <option value="">Select Product Type</option>
                                          <option value="Frame">Frame</option>
                                          <option value="Glass">Glass</option>
                                          <option value="Goggles">Goggles</option>
                                          <option value="Lens">Contact Lens</option>
                                          <option value="Solution">Solution</option>
                                          <option value="Other">Other</option>
                                          <option value="Repair">Repair</option>
                                          <option value="Non Chargeable">Non Chargeable</option> 
                                    </select>
                                    <span class="error badge text-danger" id="product_typeError"></span>
                                </div>
                                <div class="col-6">
                                    <label for="">HSN/SAC Code<span class="text-danger">*</span></label>
                                    <input class="form-control"  placeholder="Enter HSN/SAC Code" id="hsn_code" name="hsn_code">
                                    <span class="error badge text-danger" id="hsn_codeError"></span>

                                </div>
                                <div class="col-md-6">
                                    <label for="">Percentage  <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" placeholder="Enter Percentage" name="percentage" id="percentage">
                                    <span class="error badge text-danger" id="percentageError"></span>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Description </label>
                                    <input type="text" class="form-control" placeholder="Enter Description" name="description" id="description">
                                    <span class="error badge text-danger" id="descriptionError"></span>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Set Default HSN/SAC Code </label>
                                    <input type="hidden" name="set_default" value="0">
                                    <input class="form-check-input" type="checkbox" name="set_default" value="1" id="set_default"  style="margin-left: 20px;" checked>
                                      <label class="form-check-label" for="set_default">
                                      </label>
                                    <span class="error badge text-danger" id="set_defaultError"></span>
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
    $('.select-tax').select2({
      allowClear: true
    });
});
</script>
<script>
function openCreateModal()
{
    document.getElementById('modalTitle').innerText = 'Add New Tax Master';
    document.getElementById('taxForm').action = '{{ route('admin.tax-master.store') }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('taxForm').reset();
    $('#taxModal').modal('show');
}

function openEditModal(tax) {
    var tax = JSON.parse(decodeURIComponent(tax));
    
    document.getElementById('modalTitle').innerText = 'Edit Tax Master';
    document.getElementById('taxForm').action = `{{ url('/tax-master/` + tax.id + `') }}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('hsn_code').value = tax.hsn_code || '';
    document.getElementById('percentage').value = tax.percentage_t || '';
    document.getElementById('description').value = tax.description || '';
    document.getElementById('set_default').checked = tax.set_default == 1 ? true : false;
    document.getElementById('uid').value = tax.id;
    
     // Set select value safely
    const productType = (tax.product_type || '').trim();
    const productTypeElement = document.getElementById('product_type');

    // Set the value directly
    productTypeElement.value = productType;

    // If the value is not matched, this fallback forces it
    if (productTypeElement.value !== productType) {
        const options = productTypeElement.options;
        for (let i = 0; i < options.length; i++) {
            if (options[i].text.trim() === productType) {
                productTypeElement.selectedIndex = i;
                break;
            }
        }
    }

    $('#taxModal').modal('show');
}


$("#taxForm").submit(function(e)
{
    e.preventDefault(); 
    
    let isValid = true;
    let class_name = '';

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let product_type = document.getElementById("product_type" + class_name).value.trim();
    let hsn_code = document.getElementById("hsn_code" + class_name).value.trim();
    let percentage = document.getElementById("percentage" + class_name).value.trim();



    if (!product_type || product_type.trim() === "") {
        document.getElementById("product_typeError" + class_name).textContent = "Product Type required.";
        document.getElementById("product_type" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (!hsn_code || hsn_code.trim() === "") {
        document.getElementById("hsn_codeError" + class_name).textContent = "Hsn code  required";
        document.getElementById("hsn_code" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (!percentage || percentage.trim() === "") {
        document.getElementById("percentageError" + class_name).textContent = "Percentage required.";
        document.getElementById("percentage" + class_name).classList.add("is-invalid");
        isValid = false;
    }


    

    if (!isValid) {
        return;
    }

    let form = $("#taxForm")[0];
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
                if (value.includes("product_type")) {
                    $("#product_typeError").text(value);
                    $("#product_type").addClass("is-invalid");
                }
                if (value.includes("hsn_code")) {
                    $("#hsn_codeError").text(value);
                    $("#hsn_code").addClass("is-invalid");
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
            url: "{{ route('admin.tax-master.search') }}",
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
                "data": "product_type",
                orderable: false,
            },
            {
                "data": "hsn_code",
                orderable: false,
            },
            {
                "data": "percentage",
                orderable: false,
            },
            {
                "data": "description",
                orderable: false,
            },

            {
                "data": "created_at",
                orderable: false,
            },
            {
                "data": "update_at",
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
                        url: "{{ url('/tax') }}" + '/' + id + '/destroy',
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
$(document).ready(function () {
    $('#state').on('keyup', function () {
        let query = $(this).val();
        if (query.length > 2)
        {
             $('#state').addClass('loading');
            $.ajax({
                url: "{{ route('admin.state-dropdown') }}", // Laravel route
                type: "GET",
                data: { name: query },
                success: function (data) 
                {
                    $('#state').removeClass('loading');
                    let dropdown = $('#stateListName');
                    dropdown.empty();
                    if (data.length > 0) {
                        data.forEach(product => {
                            dropdown.append(`<a class="dropdown-item-list" href="#">${product.name}</a>`);
                        });
                        dropdown.show();
                    } else {
                        dropdown.hide();
                    }
                }
            });
        } 
        else
        {
            $('#stateListName').hide();
        }
    });

    // Optional: handle click on a dropdown item
    $(document).on('click', '.dropdown-item-list', function () {
        $('#state').val($(this).text());
        $('#stateListName').hide();
    });
});
</script>


@endsection
