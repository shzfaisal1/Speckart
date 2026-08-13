@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Lens Packages Master</h3>
                        <a href="#" class="btn" data-toggle="modal" data-target="#lensPackageModal" onclick="openModal('add')">
                            <span><i class="fa fa-plus"></i></span>
                            Add Package
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-table">
                        <table id="lens-packages-table" class="table datatables-basic w-100">
                            <thead>
                                <tr>
                                    <th class="wd-5p">ID</th>
                                    <th class="wd-20p">Name</th>
                                    <th class="wd-15p">Price</th>
                                    <th class="wd-10p">Warranty</th>
                                    <th class="wd-20p">Tags</th>
                                    <th class="wd-10p">Free Lens</th>
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
<div class="modal fade" data-backdrop="static" id="lensPackageModal" tabindex="-1" role="dialog" aria-labelledby="lensPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 800px;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="lensPackageModalLabel">
                    <i class="fa fa-plus-circle me-2 text-primary"></i>
                    <span id="modal-title-text">Add Lens Package</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="lensPackageForm">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                    
                    {{-- Row 1: Name and Slug --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Package Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="pkg_name" name="name"
                                       placeholder="e.g. Premium Blu Cut" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Slug <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="pkg_slug" name="slug"
                                       placeholder="e.g. premium_blu_cut" required>
                                <div class="text-muted" style="font-size:11px;margin-top:4px">Auto-generated · editable</div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Pricing and Warranty --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Current Price (INR) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control" id="pkg_current_price"
                                       name="current_price" placeholder="1500" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-medium">Original Price (INR)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="pkg_original_price"
                                       name="original_price" placeholder="2000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-medium">Warranty (Months)</label>
                                <input type="number" min="0" class="form-control" id="pkg_warranty"
                                       name="warranty_months" placeholder="6" value="0">
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Short Description --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">Short Description</label>
                        <textarea class="form-control" id="pkg_description" name="short_description"
                                  rows="2" placeholder="Provide a summary of the lens package..."></textarea>
                    </div>

                    {{-- Row 4: Filter Tags --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Filter Tags</label>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            @foreach($tags as $tag)
                                <div class="form-check">
                                    <input class="form-check-input tag-checkbox" type="checkbox"
                                           name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}">
                                    <label class="form-check-label" for="tag_{{ $tag->id }}">{{ $tag->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Row 5: Benefits --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Benefits</label>
                        <div id="benefitsList" style="border: 1px solid #ced4da; border-radius: 4px; padding: 10px; max-height: 200px; overflow-y: auto;">
                            @foreach($benefits as $benefit)
                                <div class="d-flex align-items-center mb-2 p-2 border rounded benefit-row" style="background-color: #fcfcfc;">
                                    <div class="form-check flex-grow-1">
                                        <input class="form-check-input benefit-checkbox" type="checkbox"
                                               name="benefits[]" value="{{ $benefit->id }}" id="benefit_{{ $benefit->id }}">
                                        <label class="form-check-label" for="benefit_{{ $benefit->id }}">
                                            {{ $benefit->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Row 6: Coupons --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Applicable Coupons</label>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            @foreach($coupons as $coupon)
                                <div class="form-check">
                                    <input class="form-check-input coupon-checkbox" type="checkbox"
                                           name="coupons[]" value="{{ $coupon->id }}" id="coupon_{{ $coupon->id }}">
                                    <label class="form-check-label" for="coupon_{{ $coupon->id }}">
                                        <code>{{ $coupon->code }}</code>
                                        <span class="text-muted">({{ $coupon->discount_type === 'fixed' ? '₹'.$coupon->discount_value : $coupon->discount_value.'%' }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Row 7: Power Types --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Power Type Categories</label>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            @foreach($powerTypes as $pType)
                                <div class="form-check">
                                    <input class="form-check-input power-type-checkbox" type="checkbox"
                                           name="power_types[]" value="{{ $pType->id }}" id="power_type_{{ $pType->id }}">
                                    <label class="form-check-label" for="power_type_{{ $pType->id }}">
                                        {{ $pType->description }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Frame Types --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Frame Types</label>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            @foreach($frameTypes as $fType)
                                <div class="form-check">
                                    <input class="form-check-input frame-type-checkbox" type="checkbox"
                                           name="frame_types[]" value="{{ $fType->type_id }}" id="frame_type_{{ $fType->type_id }}">
                                    <label class="form-check-label" for="frame_type_{{ $fType->type_id }}">
                                        {{ $fType->type_name }} ({{ $fType->product_type }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Frame Shapes --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Frame Shapes</label>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            @foreach($frameShapes as $fShape)
                                <div class="form-check">
                                    <input class="form-check-input frame-shape-checkbox" type="checkbox"
                                           name="frame_shapes[]" value="{{ $fShape->shape_id }}" id="frame_shape_{{ $fShape->shape_id }}">
                                    <label class="form-check-label" for="frame_shape_{{ $fShape->shape_id }}">
                                        {{ $fShape->shape_name }} ({{ $fShape->product_type }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Brands --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Brands</label>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            @foreach($brands as $brand)
                                <div class="form-check">
                                    <input class="form-check-input brand-checkbox" type="checkbox"
                                           name="brands[]" value="{{ $brand->brand_id }}" id="brand_{{ $brand->brand_id }}">
                                    <label class="form-check-label" for="brand_{{ $brand->brand_id }}">
                                        {{ $brand->brand_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Row 8: Dynamic Promotional Badges --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">
                            Promotional Badges
                            <button type="button" class="btn btn-sm btn-outline-primary ml-2" onclick="addBadgeRow()">
                                <i class="fa fa-plus"></i> Add Badge
                            </button>
                        </label>
                        <div id="badgesContainer" class="p-2 border rounded" style="background-color: #f9f9f9; min-height: 50px;">
                            {{-- Dynamic rows injected here --}}
                        </div>
                    </div>

                    {{-- Row 8½: Package Images --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">
                            Package Images
                            <span class="text-muted" style="font-size: 11px; font-weight: 400;">(multiple allowed)</span>
                        </label>

                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <label for="mediaFileInput" class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer; white-space:nowrap;">
                                <i class="fa fa-upload"></i> Choose Images
                            </label>
                            <input type="file" id="mediaFileInput" multiple accept="image/*" style="display:none;">
                            <small class="text-muted mb-0">JPG, PNG, WEBP, GIF — max 5 MB each</small>
                        </div>

                        <div id="mediaPreviewGrid" class="media-preview-grid"></div>
                    </div>

                    {{-- Row 9: Flags and Sorting --}}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-medium">Package Mode</label>
                                <select class="form-control" id="pkg_package_type" name="package_type">
                                    <option value="frame_and_lens">Frame + Lens (Paid Combo)</option>
                                    <option value="free_lens">Free Lens (Pay Frame Only)</option>
                                    <option value="free_frame">Free Frame (Pay Lens Package Only)</option>
                                    <option value="lens_only">Lens Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-medium d-block">Free Lens Badge</label>
                                <div class="toggle-btn">
                                    <input type="checkbox" id="pkg_free_lens" name="is_free_lens" class="toggle-switch" value="1">
                                    <label for="pkg_free_lens">Toggle</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-medium d-block">Status</label>
                                <div class="toggle-btn">
                                    <input type="checkbox" id="pkg_status" name="is_active" class="toggle-switch" value="1" checked>
                                    <label for="pkg_status">Toggle</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-medium">Sort Order</label>
                                <input type="number" min="0" class="form-control" id="pkg_sort_order" name="sort_order" value="0">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Package</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .benefit-row:hover { background-color: #f0f0f0 !important; }
    .badge-row { background: #fdfdfd; border: 1px dashed #ccc; border-radius: 4px; padding: 10px; margin-bottom: 8px; }

    /* ── Image Upload Styles ── */
    .media-preview-grid {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        margin-top: 10px !important;
    }
    .media-preview-item {
        position: relative !important;
        border-radius: 4px !important;
        overflow: hidden !important;
        border: 1px solid #dee2e6 !important;
        background: #fff !important;
        width: 100px !important;
        height: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
        min-height: 100px !important;
        max-height: 100px !important;
        flex-shrink: 0 !important;
        display: inline-block !important;
    }
    .media-preview-item img {
        width: 100px !important;
        height: 100px !important;
        max-width: 100px !important;
        max-height: 100px !important;
        object-fit: cover !important;
        display: block !important;
    }
    .media-preview-item .remove-btn {
        position: absolute !important;
        top: 2px !important;
        right: 2px !important;
        width: 18px !important;
        height: 18px !important;
        background: rgba(220, 53, 69, 0.9) !important;
        border: none !important;
        border-radius: 50% !important;
        color: #fff !important;
        font-size: 10px !important;
        line-height: 18px !important;
        text-align: center !important;
        cursor: pointer !important;
        padding: 0 !important;
        opacity: 0;
        transition: opacity 0.15s;
    }
    .media-preview-item:hover .remove-btn {
        opacity: 1;
    }
</style>
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
    const baseUrl = "{{ url('lens-packages') }}";

    var table = $('#lens-packages-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.lens-packages.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'current_price' },
            { data: 'warranty', name: 'warranty_months' },
            { data: 'tags_list', name: 'tags_list', orderable: false, searchable: false },
            { data: 'is_free_lens', name: 'is_free_lens', orderable: false },
            { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Auto slug generator
    $('#pkg_name').on('input', function () {
        $('#pkg_slug').val(
            $(this).val().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '')
        );
    });

    // Form submit
    $('#lensPackageForm').on('submit', function (e) {
        e.preventDefault();

        var id     = $('#record_id').val();
        var isEdit = id !== '';
        var url    = isEdit ? `${baseUrl}/${id}` : "{{ route('admin.lens-packages.store') }}";

        var formData = $(this).serializeArray();

        // Add badges
        var badges = [];
        $('.badge-row').each(function () {
            badges.push({
                label: $(this).find('.badge-label').val(),
                bg_color: $(this).find('.badge-bg-color').val(),
                text_color: $(this).find('.badge-text-color').val()
            });
        });
        formData.push({ name: 'badges_json', value: JSON.stringify(badges) });

        // Add benefits
        var benefitsData = [];
        var sortIdx = 0;
        $('.benefit-checkbox:checked').each(function () {
            var benefitId = $(this).val();
            var isHighlighted = $(`.highlight-toggle[data-benefit-id="${benefitId}"]`).is(':checked') ? 1 : 0;
            benefitsData.push({
                id: benefitId,
                sort_order: sortIdx++,
                is_highlighted: isHighlighted
            });
        });
        formData.push({ name: 'benefits_json', value: JSON.stringify(benefitsData) });

        if (isEdit) {
            formData.push({ name: '_method', value: 'PUT' });
        }

        $('#submitBtn').prop('disabled', true);
        $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        $.ajax({
            url:  url,
            type: 'POST',
            data: $.param(formData),
            success: function (res) {
                // Upload pending images for NEW packages
                if (!isEdit && res.data && res.data.id) {
                    uploadPendingFiles(res.data.id);
                }
                $('#lensPackageModal').modal('hide');
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
                $('#submitBtnText').text(isEdit ? 'Update Package' : 'Save Package');
            }
        });
    });

    // Edit button click
    $(document).on('click', '.btn-edit-package', function () {
        var id = $(this).data('id');
        openModal('edit');

        $.get(`${baseUrl}/${id}/edit`, function (data) {
            $('#record_id').val(data.id);
            $('#pkg_name').val(data.name);
            $('#pkg_slug').val(data.slug);
            $('#pkg_description').val(data.short_description);
            $('#pkg_current_price').val(data.current_price);
            $('#pkg_original_price').val(data.original_price);
            $('#pkg_warranty').val(data.warranty_months);
            $('#pkg_sort_order').val(data.sort_order);
            $('#pkg_package_type').val(data.package_type || 'frame_and_lens');
            $('#pkg_free_lens').prop('checked', data.is_free_lens == 1);
            $('#pkg_status').prop('checked', data.is_active == 1);

            // Pre-select tags
            $('.tag-checkbox').prop('checked', false);
            if (data.tags) {
                data.tags.forEach(function (tag) {
                    $('#tag_' + tag.id).prop('checked', true);
                });
            }

            // Pre-select coupons
            $('.coupon-checkbox').prop('checked', false);
            if (data.coupons) {
                data.coupons.forEach(function (coupon) {
                    $('#coupon_' + coupon.id).prop('checked', true);
                });
            }

            // Pre-select power types
            $('.power-type-checkbox').prop('checked', false);
            if (data.power_types) {
                data.power_types.forEach(function (pType) {
                    $('#power_type_' + pType.id).prop('checked', true);
                });
            }

            // Pre-select frame types
            $('.frame-type-checkbox').prop('checked', false);
            if (data.frame_types) {
                data.frame_types.forEach(function (fType) {
                    $('#frame_type_' + fType.type_id).prop('checked', true);
                });
            }

            // Pre-select frame shapes
            $('.frame-shape-checkbox').prop('checked', false);
            if (data.frame_shapes) {
                data.frame_shapes.forEach(function (fShape) {
                    $('#frame_shape_' + fShape.shape_id).prop('checked', true);
                });
            }

            // Pre-select brands
            $('.brand-checkbox').prop('checked', false);
            if (data.brands) {
                data.brands.forEach(function (brand) {
                    $('#brand_' + brand.brand_id).prop('checked', true);
                });
            }

            // Pre-select benefits
            $('.benefit-checkbox').prop('checked', false);
            $('.highlight-toggle').prop('checked', false);
            if (data.benefits) {
                data.benefits.forEach(function (benefit) {
                    $('#benefit_' + benefit.id).prop('checked', true);
                    if (benefit.pivot && benefit.pivot.is_highlighted == 1) {
                        $(`.highlight-toggle[data-benefit-id="${benefit.id}"]`).prop('checked', true);
                    }
                });
            }

            // Pre-fill badges
            $('#badgesContainer').empty();
            if (data.badges) {
                data.badges.forEach(function (badge) {
                    addBadgeRow(badge.label, badge.bg_color, badge.text_color);
                });
            }

            // Pre-fill existing media images
            pendingFiles = [];
            $('#mediaPreviewGrid').empty();
            if (data.media && data.media.length) {
                data.media.forEach(function (media) {
                    addExistingMediaPreview(media);
                });
            }
        });
    });

    // Delete button click
    $(document).on('click', '.btn-delete-package', function () {
        var id = $(this).data('id');

        Swal.fire({
            title:              'Delete this Lens Package?',
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
            title:              newState ? 'Activate this Package?' : 'Deactivate this Package?',
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

    $('#lensPackageModal').on('hidden.bs.modal', resetForm);
});

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Lens Package' : 'Edit Lens Package';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Package' : 'Update Package';
}

function addBadgeRow(label, bgColor, textColor) {
    label     = label     || '';
    bgColor   = bgColor   || '#07484A';
    textColor = textColor || '#ffffff';

    var html = `
        <div class="badge-row d-flex align-items-center mb-2" style="gap: 10px;">
            <input type="text" class="form-control form-control-sm badge-label"
                   placeholder="e.g. Bestseller" value="${label}" style="max-width:200px" required>
            <div class="d-flex align-items-center" style="gap: 5px;">
                <label class="text-muted mb-0" style="font-size:11px;white-space:nowrap">BG:</label>
                <input type="color" class="form-control form-control-sm badge-bg-color"
                       value="${bgColor}" style="width:40px;height:30px;padding:2px;">
            </div>
            <div class="d-flex align-items-center" style="gap: 5px;">
                <label class="text-muted mb-0" style="font-size:11px;white-space:nowrap">Text:</label>
                <input type="color" class="form-control form-control-sm badge-text-color"
                       value="${textColor}" style="width:40px;height:30px;padding:2px;">
            </div>
            <span class="badge badge-preview" style="background:${bgColor};color:${textColor};padding:5px 10px;border-radius:4px">
                ${label || 'Preview'}
            </span>
            <button type="button" class="btn btn-sm btn-danger ml-auto" onclick="$(this).closest('.badge-row').remove()">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    `;
    $('#badgesContainer').append(html);

    // Live preview binding
    var row = $('#badgesContainer .badge-row:last');
    row.find('.badge-label, .badge-bg-color, .badge-text-color').on('input change', function () {
        var preview = row.find('.badge-preview');
        preview.text(row.find('.badge-label').val() || 'Preview');
        preview.css({
            'background': row.find('.badge-bg-color').val(),
            'color': row.find('.badge-text-color').val()
        });
    });
}

function resetForm() {
    document.getElementById('lensPackageForm').reset();
    document.getElementById('record_id').value = '';
    $('.tag-checkbox').prop('checked', false);
    $('.coupon-checkbox').prop('checked', false);
    $('.power-type-checkbox').prop('checked', false);
    $('.benefit-checkbox').prop('checked', false);
    $('.frame-type-checkbox').prop('checked', false);
    $('.frame-shape-checkbox').prop('checked', false);
    $('.brand-checkbox').prop('checked', false);
    $('.highlight-toggle').prop('checked', false);
    $('#badgesContainer').empty();
    document.getElementById('pkg_status').checked = true;
    // Clear image preview
    pendingFiles = [];
    $('#mediaPreviewGrid').empty();
}

// ── Image Upload Logic ──

var pendingFiles = [];   // files selected but not yet uploaded (used for NEW packages)

// Handle file selection via input button
$('#mediaFileInput').on('change', function () {
    handleSelectedFiles(this.files);
    this.value = ''; // reset so same file can be re-selected
});

function handleSelectedFiles(fileList) {
    var packageId = $('#record_id').val();

    if (packageId) {
        // EDIT mode — upload immediately
        uploadFilesNow(packageId, fileList);
    } else {
        // ADD mode — queue files for after save
        Array.from(fileList).forEach(function (file) {
            if (!file.type.startsWith('image/')) return;
            pendingFiles.push(file);
            addLocalPreview(file, pendingFiles.length - 1);
        });
    }
}

function addLocalPreview(file, index) {
    var reader = new FileReader();
    reader.onload = function (e) {
        var html = `
            <div class="media-preview-item" data-pending-index="${index}" style="width:100px;height:100px;max-width:100px;max-height:100px;overflow:hidden;position:relative;display:inline-block;border-radius:4px;border:1px solid #dee2e6;">
                <img src="${e.target.result}" alt="Preview" style="width:100px;height:100px;max-width:100px;max-height:100px;object-fit:cover;display:block;">
                <button type="button" class="remove-btn" onclick="removePendingFile(${index})" style="position:absolute;top:2px;right:2px;width:18px;height:18px;background:rgba(220,53,69,0.9);border:none;border-radius:50%;color:#fff;font-size:10px;line-height:18px;text-align:center;cursor:pointer;padding:0;">
                    <i class="fa fa-times"></i>
                </button>
            </div>`;
        $('#mediaPreviewGrid').append(html);
    };
    reader.readAsDataURL(file);
}

function removePendingFile(index) {
    pendingFiles[index] = null; // mark as removed
    $(`.media-preview-item[data-pending-index="${index}"]`).remove();
}

function addExistingMediaPreview(media) {
    var imgSrc = '/public/' + media.url;
    var html = `
        <div class="media-preview-item" data-media-id="${media.id}" style="width:100px;height:100px;max-width:100px;max-height:100px;overflow:hidden;position:relative;display:inline-block;border-radius:4px;border:1px solid #dee2e6;">
            <img src="${imgSrc}" alt="${media.alt_text || ''}" style="width:100px;height:100px;max-width:100px;max-height:100px;object-fit:cover;display:block;">
            <button type="button" class="remove-btn" onclick="deleteExistingMedia(${media.id}, this)" style="position:absolute;top:2px;right:2px;width:18px;height:18px;background:rgba(220,53,69,0.9);border:none;border-radius:50%;color:#fff;font-size:10px;line-height:18px;text-align:center;cursor:pointer;padding:0;">
                <i class="fa fa-times"></i>
            </button>
        </div>`;
    $('#mediaPreviewGrid').append(html);
}

function uploadFilesNow(packageId, fileList) {
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');

    var count = 0;
    Array.from(fileList).forEach(function (file) {
        if (file && file.type.startsWith('image/')) {
            formData.append('images[]', file);
            count++;
        }
    });

    if (count === 0) return;

    var baseUrl = '{{ url("lens-packages") }}';

    $.ajax({
        url: `${baseUrl}/${packageId}/media`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            Toast.fire({ icon: 'success', title: res.message });
            // Add returned media items to preview grid
            if (res.data) {
                res.data.forEach(function (media) {
                    addExistingMediaPreview(media);
                });
            }
        },
        error: function (xhr) {
            var msg = xhr.responseJSON?.message ?? 'Image upload failed.';
            Toast.fire({ icon: 'error', title: msg });
        }
    });
}

function uploadPendingFiles(packageId) {
    var actualFiles = pendingFiles.filter(function (f) { return f !== null; });
    if (actualFiles.length === 0) return;
    uploadFilesNow(packageId, actualFiles);
    pendingFiles = [];
}

function deleteExistingMedia(mediaId, btnEl) {
    var baseUrl = '{{ url("lens-packages") }}';

    Swal.fire({
        title: 'Delete this image?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `${baseUrl}/media/${mediaId}`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
            success: function (res) {
                $(btnEl).closest('.media-preview-item').remove();
                Toast.fire({ icon: 'success', title: res.message });
            },
            error: function (xhr) {
                Toast.fire({ icon: 'error', title: xhr.responseJSON?.message ?? 'Delete failed.' });
            }
        });
    });
}
</script>
@endsection

