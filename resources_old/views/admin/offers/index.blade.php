@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.offer-list-wrap {
    font-family: 'Inter', sans-serif;
    padding: 0 8px;
}
.offer-list-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}
.offer-list-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: #1a1d29;
    margin: 0;
}
.offer-list-header h2 i {
    color: #07484A;
    margin-right: 8px;
}
.btn-create-offer {
    background: linear-gradient(135deg, #07484A, #0a5e60);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(7,72,74,0.25);
}
.btn-create-offer:hover {
    color: #fff;
    box-shadow: 0 6px 18px rgba(7,72,74,0.35);
    transform: translateY(-1px);
    text-decoration: none;
}
.offer-list-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
    padding: 24px;
}
.offer-list-card .table {
    font-size: 12px;
}
.offer-list-card .table thead th {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 600;
    color: #64748b;
    border-bottom: 2px solid #e8ecf1;
    padding: 12px;
    background: #fafbfc;
}
.offer-list-card .table tbody td {
    padding: 12px;
    vertical-align: middle;
}
.offer-stats-bar {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.stat-card {
    flex: 1;
    min-width: 160px;
    background: #fff;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e8ecf1;
    display: flex;
    align-items: center;
    gap: 14px;
}
.stat-card .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.stat-card .stat-icon.green  { background: #dcfce7; color: #16a34a; }
.stat-card .stat-icon.amber  { background: #fef3c7; color: #d97706; }
.stat-card .stat-icon.gray   { background: #f1f5f9; color: #64748b; }
.stat-card .stat-icon.blue   { background: #dbeafe; color: #2563eb; }
.stat-card .stat-value { font-size: 20px; font-weight: 700; color: #1a1d29; line-height: 1; }
.stat-card .stat-label { font-size: 11px; color: #9ca3af; margin-top: 2px; }
</style>

<section class="domestic-orders mt-0">
<div class="container-fluid">
<div class="offer-list-wrap">

    <div class="offer-list-header">
        <h2><i class="fa fa-gift"></i> Offers & Promotions</h2>
        <a href="{{ url(config('app.admin_path').'/offers/create') }}" class="btn-create-offer">
            <i class="fa fa-plus-circle"></i> Create New Offer
        </a>
    </div>

    <!-- Stats Bar -->
    <div class="offer-stats-bar" id="statsBar">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa fa-gift"></i></div>
            <div>
                <div class="stat-value" id="stat_total">—</div>
                <div class="stat-label">Total Offers</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
            <div>
                <div class="stat-value" id="stat_active">—</div>
                <div class="stat-label">Active</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fa fa-pencil"></i></div>
            <div>
                <div class="stat-value" id="stat_draft">—</div>
                <div class="stat-label">Drafts</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gray"><i class="fa fa-ban"></i></div>
            <div>
                <div class="stat-value" id="stat_inactive">—</div>
                <div class="stat-label">Inactive</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="offer-list-card">
        <table id="offers-table" class="table datatables-basic w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Offer Name</th>
                    <th>Discount</th>
                    <th>Coupon</th>
                    <th>Validity</th>
                    <th>Applied On</th>
                    <th>Banner</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

</div>
</div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

$(document).ready(function () {
    const baseUrl = "{{ url(config('app.admin_path').'/offers') }}";

    var table = $('#offers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url(config('app.admin_path').'/offers/data') }}",
            dataSrc: function (json) {
                // Update stats
                if (json.stats) {
                    $('#stat_total').text(json.stats.total || 0);
                    $('#stat_active').text(json.stats.active || 0);
                    $('#stat_draft').text(json.stats.draft || 0);
                    $('#stat_inactive').text(json.stats.inactive || 0);
                }
                return json.data;
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'offer_name', name: 'name' },
            { data: 'discount_display', name: 'discount_value' },
            { data: 'coupon', name: 'coupon_code' },
            { data: 'validity', name: 'start_date' },
            { data: 'apply_info', name: 'apply_on' },
            { data: 'banner_info', name: 'show_as_banner', orderable: true, searchable: false },
            { data: 'status_display', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    // Toggle Status
    $(document).on('change', '.toggle-offer-status', function () {
        var id = $(this).data('id');
        var $checkbox = $(this);
        var $desc = $checkbox.siblings('.custom-switch-description');
        var isChecked = $checkbox.prop('checked');
        
        // Temporarily disable the checkbox during the request
        $checkbox.prop('disabled', true);

        $.ajax({
            url: `{{ url(config('app.admin_path').'/offers') }}/${id}/toggle-status`,
            type: 'POST',
            data: {
                _method: 'PATCH',
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                if (res.success) {
                    $desc.text(res.status.charAt(0).toUpperCase() + res.status.slice(1));
                    Toast.fire({
                        icon: 'success',
                        title: res.message
                    });
                    // Reload table silently (without changing page) to update statistics bar
                    table.ajax.reload(null, false);
                } else {
                    $checkbox.prop('checked', !isChecked);
                    $desc.text(isChecked ? 'Inactive' : 'Active');
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Failed to update status.',
                        confirmButtonColor: '#07484A'
                    });
                }
            },
            error: function () {
                $checkbox.prop('checked', !isChecked);
                $desc.text(isChecked ? 'Inactive' : 'Active');
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Failed to update status.',
                    confirmButtonColor: '#07484A'
                });
            },
            complete: function () {
                $checkbox.prop('disabled', false);
            }
        });
    });

    // Preview/View Offer Details
    $(document).on('click', '.btn-view-offer', function () {
        var id = $(this).data('id');
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: `{{ url(config('app.admin_path').'/offers') }}/${id}`,
            type: 'GET',
            success: function (res) {
                if (!res.success) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load offer details.' });
                    return;
                }
                
                const offer = res.offer;
                const details = res.details;
                
                // Formulate fields
                const offerTypes = {
                    'percentage_discount': 'Percentage Discount',
                    'flat_discount': 'Flat Discount',
                    'buy1get1': 'Buy 1 Get 1',
                    'cashback': 'Cashback'
                };
                const offerTypeLabel = offerTypes[offer.offer_type] || offer.offer_type;

                const userTypeLabel = details.user_type_label || offer.user_type;

                const statuses = {
                    'active': '<span class="badge badge-success" style="font-size: 11px; padding: 4px 10px;">Active</span>',
                    'inactive': '<span class="badge badge-secondary" style="font-size: 11px; padding: 4px 10px;">Inactive</span>',
                    'draft': '<span class="badge badge-warning" style="font-size: 11px; padding: 4px 10px;">Draft</span>'
                };
                const statusBadge = statuses[offer.status] || offer.status;

                let discountDisplay = '';
                if (offer.discount_type === 'percentage') {
                    discountDisplay = parseFloat(offer.discount_value) + '% OFF';
                } else {
                    discountDisplay = '₹' + parseFloat(offer.discount_value).toLocaleString('en-IN') + ' OFF';
                }

                const couponCode = offer.coupon_code ? offer.coupon_code.toUpperCase() : '<span style="color:#9ca3af; font-style:italic">Auto-applied (No Coupon)</span>';
                const description = offer.description ? offer.description : '<span style="color:#9ca3af; font-style:italic">No description provided.</span>';

                const formatDateStr = (dStr) => {
                    if (!dStr) return '—';
                    const d = new Date(dStr);
                    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    return d.getDate().toString().padStart(2,'0') + '-' + months[d.getMonth()] + '-' + d.getFullYear();
                };
                const startDate = formatDateStr(offer.start_date);
                const endDate = formatDateStr(offer.end_date);

                const minCart = offer.min_cart_amount ? '₹' + parseFloat(offer.min_cart_amount).toLocaleString('en-IN') : '—';
                const maxDiscount = offer.max_discount ? '₹' + parseFloat(offer.max_discount).toLocaleString('en-IN') : '—';
                const usageLimit = offer.usage_limit ? offer.usage_limit + ' times per user' : 'Unlimited';

                let applyOnLabel = '';
                let hasItems = false;
                let itemsListHtml = '';

                if (offer.apply_on === 'all_products') {
                    applyOnLabel = '<span class="badge badge-primary" style="font-size: 12px; padding: 5px 12px;"><i class="fa fa-globe"></i> All Products</span>';
                } else if (offer.apply_on === 'specific_category') {
                    applyOnLabel = '<span class="badge badge-warning" style="font-size: 12px; padding: 5px 12px;"><i class="fa fa-th-large"></i> Specific Categories</span>';
                    if (details.categories && details.categories.length > 0) {
                        hasItems = true;
                        itemsListHtml = details.categories.map(cat => `<span class="badge badge-light border text-dark m-1" style="font-size:12px; padding: 5px 10px; display: inline-block;">${cat}</span>`).join('');
                    }
                } else if (offer.apply_on === 'specific_brand') {
                    applyOnLabel = '<span class="badge badge-warning" style="font-size: 12px; padding: 5px 12px;"><i class="fa fa-star"></i> Specific Brands</span>';
                    if (details.brands && details.brands.length > 0) {
                        hasItems = true;
                        itemsListHtml = details.brands.map(brand => `<span class="badge badge-light border text-dark m-1" style="font-size:12px; padding: 5px 10px; display: inline-block;">${brand}</span>`).join('');
                    }
                } else if (offer.apply_on === 'specific_products') {
                    applyOnLabel = '<span class="badge badge-warning" style="font-size: 12px; padding: 5px 12px;"><i class="fa fa-cube"></i> Specific Products</span>';
                    if (details.products && details.products.length > 0) {
                        hasItems = true;
                        itemsListHtml = '<ul style="margin: 0; padding-left: 20px; font-size: 12px; line-height: 1.6; text-align: left;">' +
                            details.products.map(prod => `<li>${prod}</li>`).join('') + '</ul>';
                    }
                }

                let bannerHtml = '';
                if (offer.show_as_banner) {
                    const posLabels = {
                        'main_slider': 'Top Slider',
                        'promo_1': 'Promo Row 1',
                        'promo_2': 'Promo Row 2',
                        'spotlight': 'Spotlight'
                    };
                    const posLabel = posLabels[offer.banner_position] || offer.banner_position || 'Not Set';
                    const imgHtml = details.banner_image_url ? `<img src="${details.banner_image_url}" style="width: 100%; max-height: 140px; object-fit: cover; border-radius: 8px; margin-top: 8px; border: 1px solid #cbd5e1;">` : '<span style="color:#9ca3af; font-style:italic">No banner image uploaded</span>';
                    bannerHtml = `
                        <div style="margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                            <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">Banner Information</div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="badge badge-success" style="font-size: 11px; padding: 4px 10px;"><i class="fa fa-check-circle"></i> Active Banner</span>
                                <span style="font-size: 13px; font-weight: 600; color: #1a1d29;">Position: ${posLabel}</span>
                            </div>
                            ${imgHtml}
                        </div>
                    `;
                }

                // Show SweetAlert modal
                Swal.fire({
                    title: `<div style="font-size: 18px; font-weight: 700; color: #1a1d29; border-bottom: 2px solid #e8ecf1; padding-bottom: 12px; width: 100%; text-align: left;"><i class="fa fa-gift" style="color:#07484A; margin-right:8px;"></i>${offer.name}</div>`,
                    html: `
                        <div style="text-align: left; font-family: 'Inter', sans-serif; padding-top: 10px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Offer Type</div>
                                    <div style="font-size: 13px; font-weight: 600; color: #1a1d29; margin-top: 2px;">${offerTypeLabel}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Status</div>
                                    <div style="margin-top: 2px;">${statusBadge}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Discount Value</div>
                                    <div style="font-size: 14px; font-weight: 700; color: #07484A; margin-top: 2px;">${discountDisplay}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Coupon Code</div>
                                    <div style="font-size: 13px; font-weight: 600; font-family: monospace; color: #00484a; margin-top: 2px;">${couponCode}</div>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Start Date</div>
                                    <div style="font-size: 13px; font-weight: 500; color: #374151; margin-top: 2px;">${startDate}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">End Date</div>
                                    <div style="font-size: 13px; font-weight: 500; color: #374151; margin-top: 2px;">${endDate}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Min Cart Amount</div>
                                    <div style="font-size: 13px; font-weight: 500; color: #374151; margin-top: 2px;">${minCart}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Max Discount</div>
                                    <div style="font-size: 13px; font-weight: 500; color: #374151; margin-top: 2px;">${maxDiscount}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">User Scope</div>
                                    <div style="font-size: 13px; font-weight: 500; color: #374151; margin-top: 2px;">${userTypeLabel}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Usage Limit</div>
                                    <div style="font-size: 13px; font-weight: 500; color: #374151; margin-top: 2px;">${usageLimit}</div>
                                </div>
                            </div>

                            ${bannerHtml}

                            <div style="margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                                <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">Description</div>
                                <div style="font-size: 13px; color: #4b5563; line-height: 1.5;">${description}</div>
                            </div>

                            <div>
                                <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 8px;">Applied Targets</div>
                                <div style="margin-bottom: 8px;">${applyOnLabel}</div>
                                <div style="max-height: 160px; overflow-y: auto; margin-top: 8px; padding: 10px; background: #f8fafb; border-radius: 8px; border: 1px dashed #cbd5e1; display: ${hasItems ? 'block' : 'none'};">
                                    ${itemsListHtml}
                                </div>
                            </div>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonColor: '#07484A',
                    confirmButtonText: 'Close',
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Failed to fetch offer details.',
                    confirmButtonColor: '#07484A'
                });
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-eye"></i>');
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete-offer', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Delete this Offer?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `{{ url(config('app.admin_path')) }}/offers/${id}`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function (res) {
                    table.ajax.reload();
                    Toast.fire({ icon: 'success', title: res.message });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Delete',
                        text: xhr.responseJSON?.message ?? 'Something went wrong.',
                        confirmButtonColor: '#07484A',
                    });
                }
            });
        });
    });
});
</script>
@endsection
