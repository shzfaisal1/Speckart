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
                        <h3>Supplier</h3>
                        <input type="text" class="form-control input" placeholder="Search By,Company Name,Contact,gst no,state" id="search"
                                name="search" style="width:320px">
                        <a href="#" class=" btn" data-toggle="modal" data-target="#supplierModal">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Create Supplier
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
                                <th class="wd-15p">Company Name</th>
                                <th class="wd-15p">Contact Name</th>
                                <th class="wd-15p">Contact Number</th>
                                <th class="wd-10p">GST Number</th>
                                <th class="wd-10p">State </th>
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
<div class="modal fade" data-backdrop="static" id="supplierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="supplierForm" method="POST" method="POST" enctype="multipart/form-data">
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
                                    <label for="">Supplier Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Company Name"
                                        maxlength="25" name="supplier_company" id="supplier_company">
                                    <span class="error badge text-danger" id="supplier_companyError"></span>
                                </div>
                                <div class="col-6">
                                    <label for="">Contact Name <span class="text-danger">*</span></label>
                                    <input class="form-control"  placeholder="Enter Product Name" id="contact_name" name="contact_name">
                                    <span class="error badge text-danger" id="contact_nameError"></span>

                                </div>
                                <div class="col-md-6">
                                    <label for="">Contact No <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Contact No" name="contact_no" id="contact_no">
                                    <span class="error badge text-danger" id="contact_noError"></span>
                                </div>
                                <div class="col-md-6">
                                    <label for="">GST No </label>
                                    <input type="text" class="form-control" placeholder="Enter GST No" name="gst_no" id="gst_no">
                                    <span class="error badge text-danger" id="QualityError"></span>
                                </div>
                                <div class="col-md-6">
                                    <label for="">State </label>
                                    <input type="text" class="form-control" placeholder="Enter GST No" name="state" id="state">
                                    <div id="stateListName" class="dropdown-menu" style="display: none; position: absolute;"></div>
                                    <span class="error badge text-danger" id="stateError"></span>
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
});
</script>
<script>
function openCreateModal()
{
    document.getElementById('modalTitle').innerText = 'Add New Supplier';
    document.getElementById('supplierForm').action = '{{ route('admin.supplier.store') }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('supplierForm').reset();
    $('#supplierModal').modal('show');
}

function openEditModal(supplier) {
    var supplier = JSON.parse(decodeURIComponent(supplier));
    
    document.getElementById('modalTitle').innerText = 'Edit Supplier';
    document.getElementById('supplierForm').action = `{{ url('/supplier/` + supplier.id + `') }}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('supplier_company').value = supplier.supplier_company || '';
    document.getElementById('contact_name').value = supplier.contact_name || '';
    document.getElementById('contact_no').value = supplier.contact_no || '';
    document.getElementById('gst_no').value = supplier.gst_no || '';
    document.getElementById('state').value = supplier.state || '';
    document.getElementById('uid').value = supplier.id;

    $('#supplierModal').modal('show');
}


$("#supplierForm").submit(function(e)
{
    e.preventDefault(); 
    
    let isValid = true;
    let class_name = '';

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let supplier_company = document.getElementById("supplier_company" + class_name).value.trim();
    let contact_name = document.getElementById("contact_name" + class_name).value.trim();
    let contact_no = document.getElementById("contact_no" + class_name).value.trim();



    if (supplier_company.length < 3) {
        document.getElementById("supplier_companyError" + class_name).textContent = "Company name must be at least 3 characters.";
        document.getElementById("supplier_company" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (contact_name.length < 3) {
        document.getElementById("contact_nameError" + class_name).textContent = "Contact name required";
        document.getElementById("contact_name" + class_name).classList.add("is-invalid");
        isValid = false;
    }
    
    if (!/^\d{10}$/.test(contact_no)) {
        document.getElementById("contact_noError" + class_name).textContent = "Contact must be a 10-digit number.";
        document.getElementById("contact_no" + class_name).classList.add("is-invalid");
        isValid = false;
    }


    

    if (!isValid) {
        return;
    }

    let form = $("#supplierForm")[0];
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
                if (value.includes("supplier_company")) {
                    $("#supplier_companyError").text(value);
                    $("#supplier_company").addClass("is-invalid");
                }
                if (value.includes("contact_name")) {
                    $("#contact_nameError").text(value);
                    $("#contact_name").addClass("is-invalid");
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
            url: "{{ route('admin.supplier.search') }}",
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
                "data": "supplier_id",
                orderable: false,
            },

            {
                "data": "supplier_company",
                orderable: false,
            },
            {
                "data": "contact_name",
                orderable: false,
            },
            {
                "data": "contact_no",
                orderable: false,
            },
            {
                "data": "gst_no",
                orderable: false,
            },
            {
                "data": "state",
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
                        url: "{{ url('/supplier') }}" + '/' + id + '/destroy',
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
