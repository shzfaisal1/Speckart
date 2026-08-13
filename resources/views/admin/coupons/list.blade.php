@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Coupons Master</h3>
                        <a href="#" class="btn" data-toggle="modal" data-target="#couponModal" onclick="openModal('add')">
                            <span><i class="fa fa-plus"></i></span>
                            Add Coupon
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-table">
                        <table id="coupons-table" class="table datatables-basic w-100">
                            <thead>
                                <tr>
                                    <th class="wd-10p">ID</th>
                                    <th class="wd-15p">Code</th>
                                    <th class="wd-15p">Discount</th>
                                    <th class="wd-25p">Validity Window</th>
                                    <th class="wd-15p">Usage Limit</th>
                                    <th class="wd-10p">Status</th>
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

{{-- ══════════════════════════════════════════════
     ADD / EDIT MODAL
════════════════════════════════════════════════ --}}
<div class="modal fade" data-backdrop="static" id="couponModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 600px;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="couponModalLabel">
                    <i class="fa fa-plus-circle me-2 text-primary"></i>
                    <span id="modal-title-text">Add Coupon</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="couponForm">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Code --}}
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Coupon Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="cp_code" name="code"
                                       placeholder="e.g. PAYDAY" required style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Discount Type --}}
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Discount Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="cp_discount_type" name="discount_type" required>
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount (₹)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Discount Value --}}
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Discount Value <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control" id="cp_discount_value"
                                       name="discount_value" placeholder="10" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Max Discount Amount --}}
                            <div class="form-group">
                                <label class="form-label fw-medium">Max Discount (for %)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="cp_max_discount"
                                       name="max_discount_amount" placeholder="500">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Min Order Value --}}
                            <div class="form-group">
                                <label class="form-label fw-medium">Min Order Value</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="cp_min_order"
                                       name="min_order_value" placeholder="999">
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Max Uses --}}
                            <div class="form-group">
                                <label class="form-label fw-medium">Max Uses Limit</label>
                                <input type="number" min="1" class="form-control" id="cp_max_uses"
                                       name="max_uses" placeholder="Unlimited">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Valid From --}}
                            <div class="form-group">
                                <label class="form-label fw-medium">Valid From</label>
                                <input type="datetime-local" class="form-control" id="cp_valid_from" name="valid_from">
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Valid Until --}}
                            <div class="form-group">
                                <label class="form-label fw-medium">Valid Until</label>
                                <input type="datetime-local" class="form-control" id="cp_valid_until" name="valid_until">
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">Description</label>
                        <input type="text" class="form-control" id="cp_description" name="description"
                               placeholder="e.g. Get flat 10% off on premium packages">
                    </div>

                    {{-- Target Lens Packages --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Target Lens Packages</label>
                        <div class="row" style="max-height: 150px; overflow-y: auto; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; margin: 0 1px;">
                            @foreach($lensPackages as $pkg)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input package-checkbox" type="checkbox"
                                               name="lens_packages[]" value="{{ $pkg->id }}" id="pkg_{{ $pkg->id }}">
                                        <label class="form-check-label" for="pkg_{{ $pkg->id }}">
                                            {{ $pkg->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-muted" style="font-size:11px;margin-top:4px">Leave all unchecked to apply to all packages</div>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Status</label>
                        <div class="toggle-btn">
                            <input type="checkbox" id="cp_status" name="is_active" class="toggle-switch" value="1" checked>
                            <label for="cp_status">Toggle</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Coupon</span>
                    </button>
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

$(document).ready(function() {
    const baseUrl = "{{ url('coupons') }}";

    var table = $('#coupons-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.coupons.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'code_display', name: 'code' },
            { data: 'discount_display', name: 'discount_value' },
            { data: 'validity', name: 'valid_from' },
            { data: 'usage', name: 'max_uses' },
            { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Form submit
    $('#couponForm').on('submit', function (e) {
        e.preventDefault();

        var id     = $('#record_id').val();
        var isEdit = id !== '';
        var url    = isEdit ? `${baseUrl}/${id}` : "{{ route('admin.coupons.store') }}";

        var formData = $(this).serialize();
        if (isEdit) {
            formData += '&_method=PUT';
        }

        $('#submitBtn').prop('disabled', true);
        $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        $.ajax({
            url:  url,
            type: 'POST',
            data: formData,
            success: function (res) {
                $('#couponModal').modal('hide');
                Toast.fire({
                    icon:  'success',
                    title: res.message
                });
                table.ajax.reload();
                resetForm();
            },
            error: function (xhr) {
                var errors = xhr.responseJSON?.errors;
                var msg    = xhr.responseJSON?.message ?? 'Something went wrong.';

                if (errors) {
                    msg = '<ul class="text-start mb-0 ps-3">'
                        + Object.values(errors).flat().map(e => `<li>${e}</li>`).join('')
                        + '</ul>';
                }

                Swal.fire({
                    icon:             'error',
                    title:            'Oops!',
                    html:             msg,
                    confirmButtonText:'OK, fix it',
                    confirmButtonColor:'#07484A',
                });
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false);
                $('#submitBtnText').text(isEdit ? 'Update Coupon' : 'Save Coupon');
            }
        });
    });

    // Edit button click
    $(document).on('click', '.btn-edit-coupon', function () {
        var id = $(this).data('id');
        openModal('edit');

        $.get(`${baseUrl}/${id}/edit`, function (data) {
            $('#record_id').val(data.id);
            $('#cp_code').val(data.code);
            $('#cp_discount_type').val(data.discount_type);
            $('#cp_discount_value').val(data.discount_value);
            $('#cp_max_discount').val(data.max_discount_amount);
            $('#cp_min_order').val(data.min_order_value);
            $('#cp_max_uses').val(data.max_uses);
            $('#cp_description').val(data.description);
            $('#cp_status').prop('checked', data.is_active == 1);

            // Format dates
            if (data.valid_from) {
                $('#cp_valid_from').val(data.valid_from.substring(0, 16));
            }
            if (data.valid_until) {
                $('#cp_valid_until').val(data.valid_until.substring(0, 16));
            }

            // Pre-select packages
            $('.package-checkbox').prop('checked', false);
            if (data.lens_packages) {
                data.lens_packages.forEach(function (pkg) {
                    $('#pkg_' + pkg.id).prop('checked', true);
                });
            }
        });
    });

    // Delete button click
    $(document).on('click', '.btn-delete-coupon', function () {
        var id = $(this).data('id');

        Swal.fire({
            title:              'Delete this Coupon?',
            text:               'This action cannot be undone.',
            icon:               'warning',
            showCancelButton:   true,
            confirmButtonText:  'Yes, delete it!',
            cancelButtonText:   'Cancel',
            confirmButtonColor: '#dc3545',
            cancelButtonColor:  '#6c757d',
            reverseButtons:     true,
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url:  `${baseUrl}/${id}`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function (res) {
                    table.ajax.reload();
                    Toast.fire({
                        icon:  'success',
                        title: res.message
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon:               'error',
                        title:              'Cannot Delete',
                        text:               xhr.responseJSON?.message ?? 'Something went wrong.',
                        confirmButtonColor: '#07484A',
                    });
                }
            });
        });
    });

    // Status toggle click
    $(document).on('change', '.toggle-status', function () {
        var id       = $(this).data('id');
        var checkbox = $(this);
        var newState = checkbox.is(':checked');

        Swal.fire({
            title:              newState ? 'Activate this Coupon?' : 'Deactivate this Coupon?',
            text:               newState
                                    ? 'It will become visible on the frontend.'
                                    : 'It will be hidden from the frontend.',
            icon:               'question',
            showCancelButton:   true,
            confirmButtonText:  'Yes, proceed',
            cancelButtonText:   'Cancel',
            confirmButtonColor: '#07484A',
            cancelButtonColor:  '#6c757d',
        }).then((result) => {
            if (!result.isConfirmed) {
                checkbox.prop('checked', !newState);
                return;
            }

            $.ajax({
                url:  `${baseUrl}/${id}/toggle`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PATCH' },
                success: function (res) {
                    table.ajax.reload(null, false);
                    Toast.fire({
                        icon:  'success',
                        title: res.message
                    });
                },
                error: function () {
                    checkbox.prop('checked', !newState);
                    Toast.fire({ icon: 'error', title: 'Toggle failed. Try again.' });
                }
            });
        });
    });

    $('#couponModal').on('hidden.bs.modal', resetForm);
});

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Coupon' : 'Edit Coupon';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Coupon' : 'Update Coupon';
}

function resetForm() {
    document.getElementById('couponForm').reset();
    document.getElementById('record_id').value = '';
    $('.package-checkbox').prop('checked', false);
    document.getElementById('cp_status').checked = true;
}
</script>
@endsection
