@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h3 class="mb-1">Shipping Charges Master</h3>
                            <p class="text-muted small mb-0">Manage delivery charges, COD availability, and serviceable pincodes.</p>
                        </div>
                        <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#shippingModal" onclick="openModal('add')">
                            <i class="fa fa-plus me-1"></i> Add Shipping Charge
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stats Summary --}}
            <div class="row px-4 pt-3 pb-2">
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Total Pincodes</div>
                            <h4 class="mb-0 fw-bold" id="stat-total">{{ $total_count ?? 0 }}</h4>
                        </div>
                        <i class="fa fa-map-pin fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Active / Serviceable</div>
                            <h4 class="mb-0 fw-bold text-success" id="stat-active">{{ $active_count ?? 0 }}</h4>
                        </div>
                        <i class="fa fa-check-circle fa-2x text-success opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Disabled / Unserviceable</div>
                            <h4 class="mb-0 fw-bold text-danger" id="stat-disabled">{{ $disabled_count ?? 0 }}</h4>
                        </div>
                        <i class="fa fa-times-circle fa-2x text-danger opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-table p-3">
                        <table id="shipping-charges-table" class="table datatables-basic table-hover w-100 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="wd-10p">ID</th>
                                    <th class="wd-20p">Pincode</th>
                                    <th class="wd-20p">Shipping Charge</th>
                                    <th class="wd-15p">COD Available</th>
                                    <th class="wd-15p">Service Status</th>
                                    <th class="wd-20p text-end">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     ADD / EDIT MODAL (CLEAN & SIMPLE DESIGN)
════════════════════════════════════════════════ --}}
<div class="modal fade" data-backdrop="static" id="shippingModal" tabindex="-1" role="dialog" aria-labelledby="shippingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-sm">

            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold mb-0" id="modal-title-text">Add Shipping Charge</h5>
                <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="shippingForm" autocomplete="off">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body p-4">
                    {{-- Alert Box for Errors --}}
                    <div class="alert alert-danger d-none py-2 px-3 small" id="modal-error-alert"></div>

                    {{-- Pincode --}}
                    <div class="form-group mb-3">
                        <label class="form-label fw-medium mb-1" for="sp_pincode">
                            Pincode <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="sp_pincode" name="pincode"
                               placeholder="e.g. 400001" maxlength="10" required>
                    </div>

                    {{-- Amount --}}
                    <div class="form-group mb-3">
                        <label class="form-label fw-medium mb-1" for="sp_amount">
                            Shipping Charge (₹) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" class="form-control" id="sp_amount"
                               name="amount" placeholder="0.00" value="0.00" required>
                    </div>

                    {{-- Toggles Row --}}
                    <div class="row pt-2">
                        <div class="col-6">
                            <label class="form-label fw-medium mb-1 d-block">Cash on Delivery</label>
                            <div class="toggle-btn">
                                <input type="checkbox" id="sp_is_cod" name="is_cod_available" class="toggle-switch" value="1" checked>
                                <label for="sp_is_cod">Toggle</label>
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-medium mb-1 d-block">Status</label>
                            <div class="toggle-btn">
                                <input type="checkbox" id="sp_status" name="status" class="toggle-switch" value="1" checked>
                                <label for="sp_status">Toggle</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="btn-save">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

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

function updateStats(stats) {
    if (stats) {
        if (stats.total !== undefined) $('#stat-total').text(stats.total);
        if (stats.active !== undefined) $('#stat-active').text(stats.active);
        if (stats.disabled !== undefined) $('#stat-disabled').text(stats.disabled);
    }
}

function openModal(mode) {
    $('#shippingForm')[0].reset();
    $('#record_id').val('');
    $('#modal-error-alert').addClass('d-none').html('');

    if (mode === 'add') {
        $('#modal-title-text').text('Add Shipping Charge');
        $('#sp_amount').val('0.00');
        $('#sp_is_cod').prop('checked', true);
        $('#sp_status').prop('checked', true);
        $('#shippingModal').modal('show');
    }
}
window.openModal = openModal;

$(document).ready(function () {
    const baseUrl = "{{ route('admin.shipping-charges.index') }}";

    // Initialize DataTable
    var table = $('#shipping-charges-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.shipping-charges.data') }}",
        columns: [
            { data: 'id', name: 'id', width: '8%' },
            { data: 'pincode_display', name: 'pincode', width: '20%' },
            { data: 'amount_display', name: 'amount', width: '20%' },
            { data: 'cod_toggle', name: 'is_cod_available', orderable: false, searchable: false, width: '16%' },
            { data: 'status_toggle', name: 'status', orderable: false, searchable: false, width: '16%' },
            { data: 'action', name: 'action', orderable: false, searchable: false, width: '20%', className: 'text-end' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            searchPlaceholder: "Search Pincode...",
            emptyTable: "No shipping charges configured yet."
        }
    });

    // Edit Shipping Charge
    $(document).on('click', '.btn-edit-shipping', function () {
        var id = $(this).data('id');
        openModal('edit');

        $('#modal-title-text').text('Edit Shipping Charge');

        $.ajax({
            url: `${baseUrl}/${id}`,
            type: 'GET',
            success: function (res) {
                if (res.status === 'success' && res.data) {
                    var data = res.data;
                    $('#record_id').val(data.id);
                    $('#sp_pincode').val(data.pincode);
                    $('#sp_amount').val(parseFloat(data.amount).toFixed(2));
                    $('#sp_is_cod').prop('checked', (data.is_cod_available ?? 1) == 1);
                    $('#sp_status').prop('checked', data.status == 1);
                    $('#shippingModal').modal('show');
                }
            },
            error: function () {
                Toast.fire({
                    icon: 'error',
                    title: 'Could not fetch shipping charge details.'
                });
            }
        });
    });

    // Form Submission (Add / Update)
    $('#shippingForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#record_id').val();
        var isEdit = !!id;
        var url = isEdit ? `${baseUrl}/${id}` : baseUrl;

        var formData = {
            _token: "{{ csrf_token() }}",
            pincode: $('#sp_pincode').val().trim(),
            amount: $('#sp_amount').val(),
            is_cod_available: $('#sp_is_cod').is(':checked') ? 1 : 0,
            status: $('#sp_status').is(':checked') ? 1 : 0
        };

        $('#btn-save').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
        $('#modal-error-alert').addClass('d-none').html('');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (res) {
                $('#btn-save').prop('disabled', false).text('Save');
                if (res.status === 'success') {
                    $('#shippingModal').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: res.message
                    });
                    table.ajax.reload(null, false);
                    updateStats(res.stats);
                    $('#shippingForm')[0].reset();
                    $('#record_id').val('');
                }
            },
            error: function (xhr) {
                $('#btn-save').prop('disabled', false).text('Save');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errorHtml = '<ul class="mb-0 ps-3">';
                    $.each(xhr.responseJSON.errors, function (key, errArr) {
                        errorHtml += `<li>${errArr[0]}</li>`;
                    });
                    errorHtml += '</ul>';
                    $('#modal-error-alert').removeClass('d-none').html(errorHtml);
                } else {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred while saving.';
                    $('#modal-error-alert').removeClass('d-none').text(msg);
                }
            }
        });
    });

    // Toggle COD Quick Switch in Table
    $(document).on('change', '.toggle-cod', function () {
        var id = $(this).data('id');
        var checkbox = $(this);

        $.ajax({
            url: `${baseUrl}/${id}/toggle-cod`,
            type: 'PATCH',
            data: { _token: "{{ csrf_token() }}" },
            success: function (res) {
                if (res.status === 'success') {
                    Toast.fire({
                        icon: 'success',
                        title: res.message
                    });
                    updateStats(res.stats);
                }
            },
            error: function () {
                checkbox.prop('checked', !checkbox.is(':checked'));
                Toast.fire({
                    icon: 'error',
                    title: 'Failed to update COD status.'
                });
            }
        });
    });

    // Toggle Status Quick Switch in Table
    $(document).on('change', '.toggle-status', function () {
        var id = $(this).data('id');
        var checkbox = $(this);

        $.ajax({
            url: `${baseUrl}/${id}/toggle`,
            type: 'PATCH',
            data: { _token: "{{ csrf_token() }}" },
            success: function (res) {
                if (res.status === 'success') {
                    Toast.fire({
                        icon: 'success',
                        title: res.message
                    });
                    updateStats(res.stats);
                }
            },
            error: function () {
                checkbox.prop('checked', !checkbox.is(':checked'));
                Toast.fire({
                    icon: 'error',
                    title: 'Failed to update status.'
                });
            }
        });
    });

    // Delete Shipping Charge
    $(document).on('click', '.btn-delete-shipping', function () {
        var id = $(this).data('id');
        var pincode = $(this).data('pincode');

        Swal.fire({
            title: 'Delete Shipping Charge?',
            text: `Are you sure you want to delete shipping configuration for pincode "${pincode}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${id}`,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        if (res.status === 'success') {
                            Toast.fire({
                                icon: 'success',
                                title: res.message
                            });
                            table.ajax.reload(null, false);
                            updateStats(res.stats);
                        }
                    },
                    error: function () {
                        Toast.fire({
                            icon: 'error',
                            title: 'Could not delete shipping charge.'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
