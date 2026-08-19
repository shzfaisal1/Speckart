@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.badge-variant {
    background-color: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 3px 8px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-right: 4px;
    margin-bottom: 4px;
    display: inline-block;
}
.badge-variant:hover {
    background-color: #e2e8f0;
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
                        <h3>B2C Product Catalog</h3>
                        <input type="text" class="form-control input" placeholder="Search by name, SKU, brand..." id="search" name="search" style="width:320px">
                        <a href="{{ route('admin.products.create') }}" class="btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Create B2C Product
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
                                        <div class="spinner-border ms-auto text-success spinner-grow" role="status" aria-hidden="true"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table datatables-basic table-bordered w-100" id="catalogTable">
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Brand / Company</th>
                                    <th>Type</th>
                                    <th>Variants (SKUs)</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Action</th>
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

@section('scripts')
<script>
$(document).ready(function() {
    let dataListView = $('#catalogTable')
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
                "url": "{{ route('admin.catalog.search') }}",
                "dataType": "json",
                "type": "POST",
                "data": function(d) {
                    d.search1 = $('#search').val();
                    d._token = "{{ csrf_token() }}";
                }
            },
            "columns": [
                { 
                    "data": "product_code", 
                    "orderable": false,
                    "render": function(data, type, full) {
                        return '<strong>' + (data || full.product_id) + '</strong>';
                    }
                },
                { "data": "product_name", "orderable": false },
                { "data": "Company", "orderable": false },
                { "data": "product_type", "orderable": false },
                { 
                    "data": "skus", 
                    "orderable": false,
                    "render": function(data) {
                        if (!data || data.length === 0) return '<span class="text-muted">No variants</span>';
                        return data.map(sku => `<span class="badge-variant">${sku}</span>`).join(' ');
                    }
                },
                { "data": "created_at", "orderable": false },
                { 
                    "data": "status", 
                    "orderable": false,
                    "render": function(data, type, full) {
                        let checked = data ? 'checked' : '';
                        return `
                            <div class="toggle-btn">
                                <input type="checkbox" id="prod_${full.product_id}" class="toggle-switch status-toggle" data-pid="${full.product_id}" ${checked}>
                                <label for="prod_${full.product_id}">Toggle</label>
                            </div>
                        `;
                    }
                },
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false,
                    "render": function(data, type, full) {
                        return `
                            <div class="dropdown">
                                <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">ACTION</button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ url(config('app.admin_path').'/products') }}/${full.product_id}/edit">
                                        <i class="fa fa-edit text-primary mr-1"></i> Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger delete-product-btn" href="javascript:void(0);" data-id="${full.product_id}" data-name="${full.product_name || 'this product'}">
                                        <i class="fa fa-trash text-danger mr-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                }
            ],
            "searchDelay": 1000,
            "dom": '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            "language": {
                "paginate": {
                    "previous": "&nbsp;",
                    "next": "&nbsp;"
                },
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "{{ __('No results available') }}",
                "sSearch": "{{ __('search') }}",
                "sProcessing": "{{ __('processing') }}",
                "sInfo": "{{ __('Showing :start to :end of :total entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}",
                "sInfoFiltered": ""
            },
            "aLengthMenu": [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            "displayLength": 10
        });

    // Debounced Search input
    let debounceTimer;
    $('#search').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            dataListView.draw();
        }, 500);
    });

    // AJAX Delete Product Handler with SweetAlert2 Confirmation
    $(document).on('click', '.delete-product-btn', function() {
        let productId = $(this).data('id');
        let productName = $(this).data('name');

        Swal.fire({
            title: 'Delete Product?',
            html: `Are you sure you want to delete <strong>${productName}</strong>?<br><span class="text-muted" style="font-size:0.85rem;">All variants and photos will be permanently removed.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-xl shadow-2xl',
                confirmButton: 'btn btn-danger px-4 py-2 font-medium',
                cancelButton: 'btn btn-secondary px-4 py-2 font-medium'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#processingLoader').show();
                $.ajax({
                    url: '{{ url(config("app.admin_path")."/products") }}/' + productId + '/destroy',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(resp) {
                        $('#processingLoader').hide();
                        if (resp.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: resp.success,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            dataListView.draw();
                        } else {
                            Swal.fire('Error', resp.error || 'Could not delete product.', 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#processingLoader').hide();
                        let msg = 'Failed to delete product.';
                        try {
                            let json = JSON.parse(xhr.responseText);
                            if (json.error) msg = json.error;
                            if (json.message) msg = json.message;
                        } catch(e) {}
                        Swal.fire('Delete Failed', msg, 'error');
                    }
                });
            }
        });
    });

    // AJAX Toggle Switch Status Handler
    $(document).on('change', '.status-toggle', function() {
        let checkbox = $(this);
        let productId = checkbox.data('pid');
        let value = checkbox.prop('checked') ? 1 : 0;

        $.ajax({
            url: '{{ route("admin.catalog.toggle-status") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                value: value
            },
            success: function(response) {
                if (response.success) {
                    $.toaster({ priority: 'success', title: 'Success!!', message: response.message });
                } else {
                    $.toaster({ priority: 'danger', title: 'Error!!', message: 'Failed to update. Please try again.' });
                    checkbox.prop('checked', !value);
                }
            },
            error: function() {
                $.toaster({ priority: 'danger', title: 'Error!!', message: "Something went wrong! Please try again." });
                checkbox.prop('checked', !value);
            }
        });
    });
});
</script>
@endsection
