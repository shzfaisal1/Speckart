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
                                    <th class="wd-30p">Package</th>
                                    <th class="wd-15p">Price</th>
                                    <th class="wd-15p">Mode</th>
                                    <th class="wd-15p">Tags</th>
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

                    {{-- Row 1: Package Name + Package Mode (side by side) --}}
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Package Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="pkg_name" name="name"
                                       placeholder="e.g. Premium Blu Cut Coating 1.60" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label fw-medium">Package Mode</label>
                                <select class="form-control" id="pkg_package_type" name="package_type">
                                    <option value="free_lens" selected>🟢 Free Lens (Pay Frame Only)</option>
                                    <option value="free_frame">🟣 Free Frame (Pay Lens Package Only)</option>
                                    {{-- <option value="frame_and_lens">🔵 Frame + Lens (Paid Combo)</option> --}}
                                </select>
                                <small class="text-muted" id="pkg_mode_hint" style="font-size:11px; margin-top:4px; display:block;">
                                    Lens is FREE. Customer pays only the Frame price. A "Free Lenses" badge is shown automatically.
                                </small>
                            </div>
                        </div>
                        {{-- Slug auto-generated server-side from name; hidden from form --}}
                        <input type="hidden" id="pkg_slug" name="slug" value="">
                        {{-- is_free_lens auto-set by Package Mode JS --}}
                        <input type="hidden" id="pkg_free_lens" name="is_free_lens" value="0">
                        {{-- Hidden fields --}}
                        <input type="hidden" id="pkg_warranty" name="warranty_months" value="0">
                        <input type="hidden" id="pkg_sort_order" name="sort_order" value="0">
                    </div>

                    {{-- Row 2: Pricing --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Current Price (₹) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" class="form-control" id="pkg_current_price"
                                           name="current_price" placeholder="1499" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">
                                    Original Price (₹)
                                    <span class="text-muted" style="font-size:11px; font-weight:400;">(MRP / Strike-through)</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" class="form-control" id="pkg_original_price"
                                           name="original_price" placeholder="2499">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Short Description --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">Short Description</label>
                        <textarea class="form-control" id="pkg_description" name="short_description"
                                  rows="2" placeholder="e.g. Advanced blue light filtration with anti-glare double coating..."></textarea>
                    </div>

                    <hr class="my-2">

                    {{-- Row 4: Filter Tags --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block mb-2">
                            🏷️ Filter Tags
                            <span class="text-muted" style="font-size:11px; font-weight:400;">(used for lens filter chips on PDP)</span>
                        </label>
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            @foreach($tags as $tag)
                                <label class="badge-chip-label" for="tag_{{ $tag->id }}" style="cursor:pointer;">
                                    <input class="tag-checkbox" type="checkbox" name="tags[]"
                                           value="{{ $tag->id }}" id="tag_{{ $tag->id }}" style="display:none;">
                                    <span class="badge border px-3 py-2" style="font-size:12px; border-radius:20px; font-weight:500; transition:all 0.15s;">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Row 5: Power Type Categories --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block mb-2">
                            👓 Power Type Categories
                            <span class="text-muted" style="font-size:11px; font-weight:400;">(determines which prescriptions this package supports)</span>
                        </label>
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            @foreach($powerTypes as $pType)
                                <label class="badge-chip-label" for="power_type_{{ $pType->id }}" style="cursor:pointer;">
                                    <input class="power-type-checkbox" type="checkbox" name="power_types[]"
                                           value="{{ $pType->id }}" id="power_type_{{ $pType->id }}" style="display:none;">
                                    <span class="badge border px-3 py-2" style="font-size:12px; border-radius:20px; font-weight:500; transition:all 0.15s;">
                                        {{ $pType->description }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Row 6: Key Benefits --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block mb-2">
                            ✨ Key Benefits
                            <span class="text-muted" style="font-size:11px; font-weight:400;">(shown as bullet points on PDP)</span>
                        </label>
                        <div id="benefitsList" style="border: 1px solid #ced4da; border-radius: 6px; padding: 10px; max-height: 180px; overflow-y: auto; background:#fafafa;">
                            @foreach($benefits as $benefit)
                                <div class="d-flex align-items-center mb-1 benefit-row">
                                    <div class="form-check flex-grow-1 mb-0">
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

                    {{-- Row 7: Promotional Ribbon / Badge (Hidden as of now) --}}
                    {{--
                    <div class="form-group">
                        <label class="form-label fw-medium d-block mb-1">
                            🎖️ Promotional Ribbon / Badge
                            <span class="text-muted" style="font-size:11px; font-weight:400;">(Single ribbon displayed on the top-left of the lens card)</span>
                        </label>
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <select class="form-control" id="pkg_badge_preset">
                                    <option value="" data-bg="" data-text="">-- None (No custom ribbon) --</option>
                                    <option value="Bestseller" data-bg="#10b981" data-text="#ffffff">🟢 Bestseller</option>
                                    <option value="Most Popular" data-bg="#f59e0b" data-text="#ffffff">🟠 Most Popular</option>
                                    <option value="Doctor Recommended" data-bg="#07484A" data-text="#ffffff">🔵 Doctor Recommended</option>
                                    <option value="Trending" data-bg="#0284c7" data-text="#ffffff">🔷 Trending</option>
                                    <option value="Limited Deal" data-bg="#dc2626" data-text="#ffffff">🔴 Limited Deal</option>
                                    <option value="custom" data-bg="#07484A" data-text="#ffffff">✍️ Custom Text & Color...</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="customBadgeFields" style="display:none;">
                                <div class="d-flex align-items-center" style="gap:8px;">
                                    <input type="text" class="form-control" id="pkg_badge_custom_label" placeholder="e.g. Premium HD">
                                    <div class="d-flex align-items-center" style="gap:3px;" title="Background Color">
                                        <small class="text-muted" style="font-size:10px;">BG:</small>
                                        <input type="color" class="form-control p-0" id="pkg_badge_custom_bg" value="#07484A" style="width:34px;height:34px;cursor:pointer;">
                                    </div>
                                    <div class="d-flex align-items-center" style="gap:3px;" title="Text Color">
                                        <small class="text-muted" style="font-size:10px;">Text:</small>
                                        <input type="color" class="form-control p-0" id="pkg_badge_custom_text" value="#ffffff" style="width:34px;height:34px;cursor:pointer;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 d-flex align-items-center" style="gap:10px;">
                            <span class="text-muted" style="font-size:11px;">Card Preview:</span>
                            <span id="badgeLivePreview" class="badge px-2 py-1 shadow-sm" style="font-size:10px; font-weight:600; display:none; border-radius:3px;"></span>
                            <span id="badgeLivePreviewNone" class="text-muted fst-italic" style="font-size:11px;">No custom badge (will show Package Mode auto-badge if Free Lens/Frame)</span>
                        </div>
                    </div>
                    --}}

                    {{-- Row 8: Package Demo Images --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block mb-1">
                            🖼️ Package Demo Images
                            <span class="text-muted" style="font-size: 11px; font-weight: 400;">(displayed in the lens selector on PDP)</span>
                        </label>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <label for="mediaFileInput" class="btn btn-sm btn-outline-secondary mb-0" style="cursor:pointer; white-space:nowrap;">
                                <i class="fa fa-upload"></i> Choose Images
                            </label>
                            <input type="file" id="mediaFileInput" multiple accept="image/*" style="display:none;">
                            <small class="text-muted mb-0">JPG, PNG, WEBP — max 5 MB each</small>
                        </div>
                        <div id="mediaPreviewGrid" class="media-preview-grid"></div>
                    </div>

                    {{-- Row 9: Status --}}
                    <div class="d-flex align-items-center" style="gap:12px; padding: 10px 0 4px;">
                        <label class="form-label fw-medium mb-0">⚡ Status</label>
                        <div class="toggle-btn">
                            <input type="checkbox" id="pkg_status" name="is_active" class="toggle-switch" value="1" checked>
                            <label for="pkg_status">Toggle</label>
                        </div>
                        <small class="text-muted">Active packages are visible to customers on the website.</small>
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

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .benefit-row:hover { background-color: #f0f0f0 !important; }
    .badge-row { background: #fdfdfd; border: 1px dashed #ccc; border-radius: 4px; padding: 10px; margin-bottom: 8px; }

    /* ── Pill-Chip style for Tags & Power Types ── */
    .badge-chip-label {
        cursor: pointer !important;
        margin-bottom: 0 !important;
        user-select: none !important;
        display: inline-block !important;
    }
    .badge-chip-label input[type="checkbox"] {
        display: none !important;
    }
    .badge-chip-label span {
        display: inline-flex !important;
        align-items: center !important;
        padding: 6px 14px !important;
        border-radius: 20px !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        border: 1.5px solid #d1d5db !important;
        background-color: #ffffff !important;
        color: #374151 !important;
        transition: all 0.15s ease-in-out !important;
        cursor: pointer !important;
    }
    .badge-chip-label:hover span {
        border-color: #07484A !important;
        color: #07484A !important;
        background-color: #f0fdfa !important;
    }
    .badge-chip-label input[type="checkbox"]:checked + span,
    .badge-chip-label.active span {
        background-color: #07484A !important;
        color: #ffffff !important;
        border-color: #07484A !important;
        box-shadow: 0 2px 6px rgba(7, 72, 74, 0.25) !important;
    }

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

$(document).ready(function() {
    const baseUrl = "{{ route('admin.lens-packages.index') }}";

    var table = $('#lens-packages-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.lens-packages.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'current_price' },
            { data: 'package_mode', name: 'package_type', orderable: false },
            { data: 'tags_list', name: 'tags_list', orderable: false, searchable: false },
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

        // Add single badge (if enabled)
        if ($('#pkg_badge_preset').length) {
            var badgePreset = $('#pkg_badge_preset').val();
            var badges = [];
            if (badgePreset === 'custom') {
                var customLabel = $('#pkg_badge_custom_label').val().trim();
                if (customLabel) {
                    badges.push({
                        label: customLabel,
                        bg_color: $('#pkg_badge_custom_bg').val() || '#07484A',
                        text_color: $('#pkg_badge_custom_text').val() || '#ffffff'
                    });
                }
            } else if (badgePreset) {
                var selectedOpt = $('#pkg_badge_preset option:selected');
                badges.push({
                    label: badgePreset,
                    bg_color: selectedOpt.data('bg') || '#10b981',
                    text_color: selectedOpt.data('text') || '#ffffff'
                });
            }
            formData.push({ name: 'badges_json', value: JSON.stringify(badges) });
        }

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
            $('#pkg_package_type').val(data.package_type || 'free_lens').trigger('change');
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

            // Pre-fill single badge (if enabled)
            if ($('#pkg_badge_preset').length) {
                var primaryBadge = (data.badges && data.badges.length) ? data.badges[0] : null;
                if (primaryBadge) {
                    var matchingOption = $(`#pkg_badge_preset option[value="${primaryBadge.label}"]`);
                    if (matchingOption.length) {
                        $('#pkg_badge_preset').val(primaryBadge.label).trigger('change');
                    } else {
                        $('#pkg_badge_preset').val('custom').trigger('change');
                        $('#pkg_badge_custom_label').val(primaryBadge.label);
                        $('#pkg_badge_custom_bg').val(primaryBadge.bg_color || '#07484A');
                        $('#pkg_badge_custom_text').val(primaryBadge.text_color || '#ffffff');
                        updateBadgePreview();
                    }
                } else {
                    $('#pkg_badge_preset').val('').trigger('change');
                }
            }

            // Pre-fill existing media images
            pendingFiles = [];
            $('#mediaPreviewGrid').empty();
            if (data.media && data.media.length) {
                data.media.forEach(function (media) {
                    addExistingMediaPreview(media);
                });
            }

            // Sync visual chip styling with pre-selected checkboxes
            syncChipStyles();
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
    syncChipStyles();
});

function syncChipStyles() {
    $('.badge-chip-label').each(function () {
        var isChecked = $(this).find('input[type="checkbox"]').is(':checked');
        if (isChecked) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });
}

$(document).on('change', '.badge-chip-label input[type="checkbox"]', function () {
    syncChipStyles();
});

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Lens Package' : 'Edit Lens Package';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Package' : 'Update Package';
    setTimeout(syncChipStyles, 50);
}

/* ── Package Mode → auto-sync is_free_lens + hint text ── */
var packageModeHints = {
    'frame_and_lens': 'Customer pays for Frame + Lens upgrade price combined.',
    'free_lens':      'Lens is FREE. Customer pays only the Frame price. A "Free Lenses" badge is shown automatically.',
    'free_frame':     'Frame is FREE. Customer pays only the Lens Package price. A "Free Frame" badge is shown automatically.'
};

$(document).on('change', '#pkg_package_type', function () {
    var mode = $(this).val();
    // Auto-set is_free_lens based on chosen package mode
    var isFreeLens = (mode === 'free_lens') ? '1' : '0';
    $('#pkg_free_lens').val(isFreeLens);
    // Update contextual hint
    var hint = packageModeHints[mode] || '';
    $('#pkg_mode_hint').text(hint);
});

function updateBadgePreview() {
    var preset = $('#pkg_badge_preset').val();
    if (!preset) {
        $('#customBadgeFields').hide();
        $('#badgeLivePreview').hide();
        $('#badgeLivePreviewNone').show();
        return;
    }

    if (preset === 'custom') {
        $('#customBadgeFields').show();
        var customLabel = $('#pkg_badge_custom_label').val().trim() || 'Custom Ribbon';
        var customBg = $('#pkg_badge_custom_bg').val() || '#07484A';
        var customText = $('#pkg_badge_custom_text').val() || '#ffffff';
        $('#badgeLivePreview').text(customLabel).css({ 'background-color': customBg, 'color': customText }).show();
        $('#badgeLivePreviewNone').hide();
    } else {
        $('#customBadgeFields').hide();
        var opt = $('#pkg_badge_preset option:selected');
        var label = opt.val();
        var bg = opt.data('bg') || '#10b981';
        var text = opt.data('text') || '#ffffff';
        $('#badgeLivePreview').text(label).css({ 'background-color': bg, 'color': text }).show();
        $('#badgeLivePreviewNone').hide();
    }
}

$(document).on('change', '#pkg_badge_preset', updateBadgePreview);
$(document).on('input change', '#pkg_badge_custom_label, #pkg_badge_custom_bg, #pkg_badge_custom_text', updateBadgePreview);

function resetForm() {
    document.getElementById('lensPackageForm').reset();
    document.getElementById('record_id').value = '';
    // Reset hidden fields
    $('#pkg_slug').val('');
    $('#pkg_free_lens').val('0');
    $('#pkg_sort_order').val('0');
    // Reset chip checkboxes
    $('.tag-checkbox').prop('checked', false);
    $('.power-type-checkbox').prop('checked', false);
    $('.benefit-checkbox').prop('checked', false);
    $('.coupon-checkbox').prop('checked', false);
    $('.highlight-toggle').prop('checked', false);
    // Reset package mode hint
    $('#pkg_package_type').val('free_lens').trigger('change');
    // Reset promotional badge selector (if enabled)
    if ($('#pkg_badge_preset').length) {
        $('#pkg_badge_preset').val('').trigger('change');
        $('#pkg_badge_custom_label').val('');
        $('#pkg_badge_custom_bg').val('#07484A');
        $('#pkg_badge_custom_text').val('#ffffff');
    }
    // Reset status & media
    document.getElementById('pkg_status').checked = true;
    pendingFiles = [];
    $('#mediaPreviewGrid').empty();
    syncChipStyles();
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

    var baseUrl = '{{ route("admin.lens-packages.index") }}';

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
    var baseUrl = '{{ route("admin.lens-packages.index") }}';

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

