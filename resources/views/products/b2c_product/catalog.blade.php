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
    transition: all 0.15s ease-in-out;
}
.badge-variant:hover {
    background-color: #e0e7ff;
    color: #4338ca;
    border-color: #a5b4fc;
    transform: translateY(-1px);
}
.variant-color-card {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 3px 8px;
    margin: 2px 2px;
    font-size: 0.78rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    transition: all 0.15s ease-in-out;
    cursor: pointer;
    vertical-align: middle;
}
.variant-color-card:hover {
    background-color: #f8fafc;
    border-color: #6366f1;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(99,102,241,0.15);
}
.variant-swatch {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: inline-block;
    border: 1.5px solid #94a3b8;
    vertical-align: middle;
    margin-right: 5px;
    flex-shrink: 0;
    box-shadow: inset 0 0 2px rgba(0,0,0,0.25);
}
.variant-swatch-split {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: inline-block;
    border: 1.5px solid #94a3b8;
    vertical-align: middle;
    margin-right: 5px;
    flex-shrink: 0;
}
.variant-sku-code {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.8rem;
}
.variant-price-tag {
    font-weight: 700;
    color: #16a34a;
    font-size: 0.72rem;
    margin-left: 5px;
    background: #f0fdf4;
    padding: 1px 5px;
    border-radius: 4px;
    border: 1px solid #bbf7d0;
}
.variant-mini-thumb {
    width: 22px;
    height: 22px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    margin-right: 5px;
    flex-shrink: 0;
}
.qv-swatch {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-block;
    border: 2px solid #e2e8f0;
    vertical-align: middle;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.qv-badge {
    background: #e0e7ff;
    color: #3730a3;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 6px;
    display: inline-block;
    margin: 2px;
}
.qv-variant-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    text-align: left;
}
.qv-variant-box:hover {
    border-color: #c7d2fe;
    box-shadow: 0 4px 14px rgba(79,70,229,0.08);
}
.qv-thumb {
    width: 110px;
    height: 55px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}
#catalogTable th {
    background-color: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    vertical-align: middle !important;
    white-space: nowrap;
}
#catalogTable td {
    vertical-align: middle !important;
    font-size: 0.85rem;
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
                                    <th>Color</th>
                                    <th>Price</th>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function formatColorSwatch(colorStr, swatchClass) {
    swatchClass = swatchClass || 'variant-swatch';
    if (!colorStr) {
        return `<span class="${swatchClass}" style="background:#cbd5e1;" title="No color specified"></span>`;
    }
    let parts = colorStr.split('/').map(c => c.trim()).filter(Boolean);
    if (parts.length >= 2) {
        return `<span class="${swatchClass} variant-swatch-split" style="background: linear-gradient(135deg, ${parts[0]} 50%, ${parts[1]} 50%); margin-right:5px;" title="Colors: ${parts[0]} / ${parts[1]}"></span>`;
    }
    return `<span class="${swatchClass}" style="background:${parts[0]}; margin-right:5px;" title="Color: ${parts[0]}"></span>`;
}

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
                        return `<a href="javascript:void(0);" class="view-details-btn text-primary" data-id="${full.product_id}" style="font-weight:700; font-size:0.9rem; text-decoration:none;">${data || full.product_id}</a>`;
                    }
                },
                { 
                    "data": "product_name", 
                    "orderable": false,
                    "render": function(data, type, full) {
                        let imgTag = full.main_image_url 
                            ? `<img src="${full.main_image_url}" class="rounded border mr-2 flex-shrink-0" style="width:38px; height:38px; object-fit:cover;" onerror="this.style.display='none'">` 
                            : '';
                        return `
                            <div class="d-flex align-items-center">
                                ${imgTag}
                                <div>
                                    <a href="javascript:void(0);" class="view-details-btn text-dark" data-id="${full.product_id}" style="font-weight:600; text-decoration:none;">${data || '–'}</a>
                                    ${full.size ? `<span class="text-muted d-block" style="font-size:0.72rem;">Size: ${full.size}</span>` : ''}
                                </div>
                            </div>
                        `;
                    }
                },
                { "data": "Company", "orderable": false },
                { "data": "product_type", "orderable": false },
                { 
                    "data": "color", 
                    "orderable": false,
                    "render": function(data, type, full) {
                        let swatch = formatColorSwatch(data, 'variant-swatch');
                        let label = data ? data : '<span class="text-muted">Standard</span>';
                        return `
                            <div class="d-flex align-items-center">
                                ${swatch}
                                <span style="font-size:0.82rem; font-weight:600; color:#334155;">${label}</span>
                            </div>
                        `;
                    }
                },
                { 
                    "data": "retail_price", 
                    "orderable": false,
                    "render": function(data, type, full) {
                        let effective = full.discount_price || data;
                        let priceHtml = effective 
                            ? `<strong class="text-success" style="font-size:0.95rem;">₹${parseFloat(effective).toFixed(0)}</strong>` 
                            : '<span class="text-muted">–</span>';
                        let origHtml = (full.discount_price && data && parseFloat(full.discount_price) < parseFloat(data))
                            ? `<span class="text-muted text-decoration-line-through ml-1" style="font-size:0.75rem; text-decoration:line-through;">₹${parseFloat(data).toFixed(0)}</span>`
                            : '';
                        return `
                            <div>
                                ${priceHtml} ${origHtml}
                                <div class="text-muted" style="font-size:0.7rem; margin-top:2px;">${full.created_at}</div>
                            </div>
                        `;
                    }
                },
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
                                    <a class="dropdown-item view-details-btn" href="javascript:void(0);" data-id="${full.product_id}">
                                        <i class="fa fa-eye text-info mr-1"></i> View Details
                                    </a>
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

    // AJAX 360° Quick View Modal
    $(document).on('click', '.view-details-btn', function() {
        let productId = $(this).data('id');
        if (!productId) return;

        $('#processingLoader').show();

        $.ajax({
            url: '{{ url(config("app.admin_path")."/products") }}/' + productId + '/details',
            type: 'GET',
            dataType: 'json',
            success: function(resp) {
                $('#processingLoader').hide();
                if (!resp.success) {
                    Swal.fire('Error', resp.error || 'Failed to fetch product details.', 'error');
                    return;
                }
                renderProductDetailsModal(resp);
            },
            error: function(xhr) {
                $('#processingLoader').hide();
                Swal.fire('Error', 'Unable to load product data.', 'error');
            }
        });
    });

    function renderProductDetailsModal(p) {
        let isFrame = (p.product_type || 'Frame').toLowerCase() === 'frame';

        // Master Specs Card
        let masterHtml = `
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:20px; text-align:left;">
                <div class="row" style="font-size:0.88rem;">
                    <div class="col-md-4 mb-2">
                        <span class="text-muted d-block" style="font-size:0.75rem;">PRODUCT FAMILY CODE</span>
                        <strong>${p.parent_product_code || p.product_id}</strong>
                    </div>
                    <div class="col-md-4 mb-2">
                        <span class="text-muted d-block" style="font-size:0.75rem;">BRAND / COMPANY</span>
                        <strong>${p.Company || '–'}</strong>
                    </div>
                    <div class="col-md-4 mb-2">
                        <span class="text-muted d-block" style="font-size:0.75rem;">CATEGORY & SUBCATEGORY</span>
                        <strong>${p.category_name || '–'} ${p.subcategory_name ? ' &rsaquo; ' + p.subcategory_name : ''}</strong>
                    </div>
                    <div class="col-md-4 mb-2">
                        <span class="text-muted d-block" style="font-size:0.75rem;">PRODUCT TYPE</span>
                        <span class="badge bg-primary text-white" style="font-size:0.75rem; padding:4px 8px;">${p.product_type}</span>
                    </div>
                    ${isFrame ? `
                    <div class="col-md-4 mb-2">
                        <span class="text-muted d-block" style="font-size:0.75rem;">FRAME SHAPE & RIM TYPE</span>
                        <strong>${p.Shape || '–'} / ${p.Type || '–'}</strong>
                    </div>
                    ` : ''}
                    <div class="col-md-4 mb-2">
                        <span class="text-muted d-block" style="font-size:0.75rem;">STATUS / B2C STORE</span>
                        <span class="badge ${p.status ? 'bg-success' : 'bg-secondary'} text-white" style="font-size:0.75rem; padding:4px 8px;">
                            ${p.status ? 'Active (Live)' : 'Inactive'}
                        </span>
                        <span class="badge ${p.is_b2c ? 'bg-info' : 'bg-dark'} text-white" style="font-size:0.75rem; padding:4px 8px;">
                            ${p.is_b2c ? 'B2C Enabled' : 'B2B Only'}
                        </span>
                    </div>
                    ${p.supported_types && p.supported_types.length > 0 ? `
                    <div class="col-12 mt-2">
                        <span class="text-muted d-block" style="font-size:0.75rem;">SUPPORTED OPTICAL TYPES</span>
                        ${p.supported_types.map(t => `<span class="qv-badge">${t}</span>`).join('')}
                    </div>
                    ` : ''}
                    ${p.lens_packages && p.lens_packages.length > 0 ? `
                    <div class="col-12 mt-1">
                        <span class="text-muted d-block" style="font-size:0.75rem;">LENS PACKAGES ALLOWED</span>
                        ${p.lens_packages.map(pkg => `<span class="qv-badge" style="background:#ecfdf5; color:#065f46;">${pkg}</span>`).join('')}
                    </div>
                    ` : ''}
                </div>
            </div>
        `;

        // Variant Cards List
        let variantsHtml = '<h6 style="text-align:left; font-weight:700; color:#1e293b; margin-bottom:12px;"><i class="fa fa-layer-group text-primary mr-1"></i> Color Variants (' + p.variants.length + ' Total)</h6>';
        
        p.variants.forEach((v, idx) => {
            let colorSwatch = formatColorSwatch(v.Color, 'qv-swatch');
            let discountBadge = '';
            if (v.Retail_Price && v.discount_price && parseFloat(v.Retail_Price) > parseFloat(v.discount_price)) {
                let diff = parseFloat(v.Retail_Price) - parseFloat(v.discount_price);
                let pct = Math.round((diff / parseFloat(v.Retail_Price)) * 100);
                discountBadge = `<span class="badge bg-success text-white" style="font-size:0.7rem; margin-left:6px;">${pct}% OFF</span>`;
            }

            variantsHtml += `
                <div class="qv-variant-box">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-2 mb-md-0">
                            ${v.main_image_url ? `
                                <img src="${v.main_image_url}" class="qv-thumb" alt="Variant Main Photo" onerror="this.src='/assets/images/no-image.png'">
                            ` : `
                                <div class="qv-thumb d-flex align-items-center justify-content-center text-muted" style="font-size:0.75rem;">No Photo</div>
                            `}
                            <div class="mt-1" style="font-size:0.72rem; color:#64748b;">
                                ${v.gallery_urls && v.gallery_urls.length > 0 ? `<i class="fa fa-images"></i> +${v.gallery_urls.length} gallery` : ''}
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong style="color:#1e293b; font-size:0.95rem;">${colorSwatch} Variant #${idx + 1} &ndash; ${v.product_code}</strong>
                                    <span class="text-muted ml-2" style="font-size:0.8rem;">(${v.Size || 'Standard'} / ${v.Material || '–'})</span>
                                </div>
                                <div>
                                    <strong class="text-success" style="font-size:1.05rem;">₹${v.discount_price || v.Retail_Price || '0.00'}</strong>
                                    ${v.discount_price && v.Retail_Price && v.discount_price !== v.Retail_Price ? `
                                        <span class="text-muted text-decoration-line-through ml-1" style="font-size:0.85rem; text-decoration:line-through;">₹${v.Retail_Price}</span>
                                    ` : ''}
                                    ${discountBadge}
                                </div>
                            </div>

                            <div class="row" style="font-size:0.8rem; background:#f8fafc; border-radius:6px; padding:6px 10px; margin:0;">
                                ${isFrame ? `
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted">Dimensions:</span> 
                                        <strong>${v.lens_width || '–'}-${v.Bridge_Size || '–'}-${v.temple_length || '–'} mm</strong> (Frame: ${v.frame_width || '–'}mm)
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted">Protection:</span> 
                                        <strong>${v.polarized ? 'Polarized' : 'Non-Polarized'} | ${v.uv_protection || 'UV400'}</strong>
                                    </div>
                                ` : `
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted">Modality / Pack:</span> 
                                        <strong>${v.Modality || '–'} (${v.pack_size || '–'} Pack)</strong>
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted">Optical Curve:</span> 
                                        <strong>BC: ${v.BC || '–'} | DIA: ${v.DIA || '–'} | SPH: ${v.SPH || '–'}</strong>
                                    </div>
                                `}
                                <div class="col-sm-6">
                                    <span class="text-muted">Purchase Cost:</span> ₹${v.Purchase_Price || '–'}
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Tax / HSN:</span> ${v.tax_hsn_code || '90049000'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        Swal.fire({
            title: `<div style="text-align:left; font-size:1.25rem; font-weight:700; color:#1e293b;"><i class="fa fa-glasses text-primary mr-2"></i> ${p.product_name}</div>`,
            html: `
                <div style="max-height:68vh; overflow-y:auto; padding-right:6px;">
                    ${masterHtml}
                    ${variantsHtml}
                </div>
            `,
            width: '920px',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa fa-edit"></i> Edit Product',
            cancelButtonText: 'Close',
            customClass: {
                popup: 'rounded-xl shadow-2xl',
                confirmButton: 'btn btn-primary px-4 py-2 font-medium',
                cancelButton: 'btn btn-secondary px-4 py-2 font-medium'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = p.edit_url;
            }
        });
    }

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
