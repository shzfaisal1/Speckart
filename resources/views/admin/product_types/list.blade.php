@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Product Types Master</h3>
                        <a href="#" class=" btn" data-toggle="modal" data-target="#productTypeModal" onclick="openModal('add')">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add Product Type
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
               <div class="col-lg-12">
                <div class="domestic-orders-table">
                    <table id="frame-types-table" class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">ID</th>
                                <th class="wd-10p">Icon</th>
                                <th class="wd-15p">Name</th>
                                <th class="wd-15p">Slug</th>
                                <th class="wd-15p">Has Power</th>
                                <th class="wd-15p">Status</th>
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
<div class="modal fade" data-backdrop="static" id="productTypeModal" tabindex="-1" role="dialog" aria-labelledby="productTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="productTypeModalLabel">
                    <i class="fa fa-plus-circle me-2 text-primary"></i>
                    <span id="modal-title-text">Add Product Type</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="productTypeForm">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-medium">
                                Type Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="type_name" name="name"
                                   placeholder="e.g. Reading Glasses" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-medium">
                                Slug <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="type_slug" name="slug"
                                   placeholder="e.g. reading" required>
                            <div class="text-muted" style="font-size:11px;margin-top:4px">Auto-generated · editable</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label class="form-label fw-medium">Subtitle <span class="text-muted">(shown on frontend)</span></label>
                            <input type="text" class="form-control" id="type_subtitle" name="subtitle"
                                   placeholder="e.g. + Positive Power">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label fw-medium">Icon <span class="text-muted">(emoji/symbol)</span></label>
                            <input type="text" class="form-control" id="type_icon" name="icon"
                                   placeholder="e.g. 👓 or 🕶️" maxlength="20">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-medium">Has Lens Power?</label>
                            <select class="form-control" id="has_power" name="has_power"
                                    onchange="togglePowerSection()">
                                <option value="0">No — no power selector</option>
                                <option value="1">Yes — show power chips</option>
                            </select>
                        </div>
                    </div>

                    <div id="powerSection" class="form-group d-none">
                        <label class="form-label fw-medium d-flex justify-content-between align-items-center">
                            <span>
                                Default Available Powers
                                <span class="text-muted" style="font-size:11px">(click to select)</span>
                            </span>
                            <span style="font-size: 12px;">
                                <a href="javascript:void(0)" onclick="selectAllPowers(true)" class="text-primary fw-semibold" style="margin-right: 12px; text-decoration: none;">Select All</a>
                                <a href="javascript:void(0)" onclick="selectAllPowers(false)" class="text-secondary" style="text-decoration: none;">Clear All</a>
                            </span>
                        </label>
                        
                        <!-- Tabs for positive / negative -->
                        <ul class="nav nav-pills mb-2" id="powerTabs" role="tablist" style="font-size: 12px;">
                            <li class="nav-item">
                                <a class="nav-link active py-1 px-3" id="positive-tab" data-toggle="pill" href="#p-positive" role="tab" aria-selected="true">Positive (+)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3" id="negative-tab" data-toggle="pill" href="#p-negative" role="tab" aria-selected="false">Negative (-)</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content border p-2 rounded bg-light mb-3" style="max-height: 150px; overflow-y: auto;">
                            <!-- Positive Powers -->
                            <div class="tab-pane fade show active" id="p-positive" role="tabpanel">
                                <div class="d-flex flex-wrap" id="positiveChips" style="gap: 6px;">
                                    @foreach(['+0.50','+0.75','+1.00','+1.25','+1.50','+1.75','+2.00','+2.25','+2.50','+2.75','+3.00','+3.25','+3.50','+3.75','+4.00','+4.50','+5.00','+5.50','+6.00'] as $p)
                                        <span class="badge power-chip predefined-chip rounded-pill border"
                                              data-value="{{ $p }}"
                                              style="cursor:pointer;font-size:11px;padding:5px 9px;
                                                     background:#f1f1f1;color:#333;border-color:#ccc !important;
                                                     user-select:none"
                                              onclick="togglePowerChip(this)">
                                            {{ $p }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Negative Powers -->
                            <div class="tab-pane fade" id="p-negative" role="tabpanel">
                                <div class="d-flex flex-wrap" id="negativeChips" style="gap: 6px;">
                                    @foreach(['-0.25','-0.50','-0.75','-1.00','-1.25','-1.50','-1.75','-2.00','-2.25','-2.50','-2.75','-3.00','-3.25','-3.50','-3.75','-4.00','-4.25','-4.50','-4.75','-5.00','-5.25','-5.50','-5.75','-6.00','-6.50','-7.00','-7.50','-8.00','-8.50','-9.00','-9.50','-10.00'] as $p)
                                        <span class="badge power-chip predefined-chip rounded-pill border"
                                              data-value="{{ $p }}"
                                              style="cursor:pointer;font-size:11px;padding:5px 9px;
                                                     background:#f1f1f1;color:#333;border-color:#ccc !important;
                                                     user-select:none"
                                              onclick="togglePowerChip(this)">
                                            {{ $p }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Add Custom Power -->
                        <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                            <label class="form-label mb-0" style="font-size: 12px; white-space: nowrap;">Add Custom Power:</label>
                            <div class="input-group input-group-sm" style="max-width: 180px;">
                                <input type="text" class="form-control" id="custom_power_val" placeholder="e.g. -11.50 or +1.10">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="addCustomPowerChip()">+ Add</button>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" id="selected_powers" name="default_powers" value="[]">
                    </div>

                    {{-- ── Default Lens Package (for Zero Power / Screen Glass auto-bundle) ── --}}
                    <div class="form-group" id="defaultPackageSection">
                        <label class="form-label fw-medium">
                            Default Lens Package
                            <span class="text-muted" style="font-size:11px;">(auto-bundled on BUY NOW for Zero Power / non-powered types)</span>
                        </label>
                        <select class="form-control" id="default_lens_package_id" name="default_lens_package_id">
                            <option value="">— None (customer selects manually) —</option>
                            @foreach(\App\Models\LensPackage::where('is_active', 1)->orderBy('name')->get() as $lp)
                                <option value="{{ $lp->id }}">{{ $lp->name }}
                                    @if($lp->current_price == 0) (Free) @else (₹{{ number_format($lp->current_price,0) }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i>
                            Set this for <strong>Zero Power</strong> and <strong>Screen Glass</strong> types so the correct Blue-Cut lens is automatically added to cart.
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Status</label>
                        <div class="toggle-btn">
                            <input type="checkbox" id="type_status" name="is_active" class="toggle-switch" value="1" checked>
                            <label for="type_status">Toggle</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Type</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection


{{-- ══ SweetAlert2 CDN — add in your master layout <head> if not already there ══
     OR keep it here in @push('styles') if your layout supports stacks
════════════════════════════════════════════════════════════════════════════════ --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush


@section('scripts')

{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ══════════════════════════════════════════════════════════════
//  GLOBAL SWEETALERT CONFIG — match your admin theme colours
// ══════════════════════════════════════════════════════════════
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',       // ← top-right corner toast
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// ══════════════════════════════════════════════════════════════
//  DATATABLE
// ══════════════════════════════════════════════════════════════
$(document).ready(function () {
    const baseUrl = "{{ route('admin.product-types.index') }}";

    var table = $('#frame-types-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.product-types.data') }}",
        columns: [
            { data: 'id',   name: 'id' },
            {
                data: 'icon', name: 'icon', orderable: false, searchable: false,
                render: function (data) {
                    return data
                        ? `<span style="font-size: 20px;">${data}</span>`
                        : '<span class="badge badge-light">No Icon</span>';
                }
            },
            { data: 'name', name: 'name' },
            {
                data: 'slug', name: 'slug',
                render: function (data) {
                    return `<code class="bg-light px-2 py-1 rounded">${data}</code>`;
                }
            },
            {
                data: 'has_power', name: 'has_power', orderable: false,
                render: function (data) {
                    return data
                        ? '<span class="badge bg-primary-transparent">Yes</span>'
                        : '<span class="badge bg-light text-muted">No</span>';
                }
            },
            { data: 'is_active', name: 'is_active' },
            { data: 'action',    name: 'action', orderable: false, searchable: false }
        ]
    });


    // ── Auto slug ──────────────────────────────────────────────
    $('#type_name').on('input', function () {
        $('#type_slug').val(
            $(this).val().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '')
        );
    });


    // ══════════════════════════════════════════════════════════
    //  FORM SUBMIT — Add / Edit
    // ══════════════════════════════════════════════════════════
    $('#productTypeForm').on('submit', function (e) {
        e.preventDefault();

        // collect power chips
        var powers = [];
        $('.power-chip.chip-selected').each(function () { powers.push($(this).data('value')); });
        $('#selected_powers').val(JSON.stringify(powers));

        var id     = $('#record_id').val();
        var isEdit = id !== '';
        var url    = isEdit ? `${baseUrl}/${id}` : "{{ route('admin.product-types.store') }}";

        // disable button + spinner
        $('#submitBtn').prop('disabled', true);
        $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        // add _method field for Laravel method spoofing (PUT for edit, POST for create)
        var formData = $(this).serialize();
        if (isEdit) {
            formData += '&_method=PUT';
        }

        $.ajax({
            url:  url,
            type: 'POST',
            data: formData,

            success: function (res) {
                // ✅ close modal
                $('#productTypeModal').modal('hide');

                // ✅ SweetAlert — success toast
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
                    // show each validation error as a list
                    msg = '<ul class="text-start mb-0 ps-3">'
                        + Object.values(errors).flat().map(e => `<li>${e}</li>`).join('')
                        + '</ul>';
                }

                // ❌ SweetAlert — validation / server error popup
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
                $('#submitBtnText').text(isEdit ? 'Update Type' : 'Save Type');

            }
        });
    });


    // ══════════════════════════════════════════════════════════
    //  EDIT BUTTON — pre-fill modal
    // ══════════════════════════════════════════════════════════
    $(document).on('click', '.btn-edit-type', function () {
        var id = $(this).data('id');
        openModal('edit');

        $.get(`${baseUrl}/${id}/edit`, function (data) {
            $('#record_id').val(data.id);
            $('#type_name').val(data.name);
            $('#type_slug').val(data.slug);
            $('#type_subtitle').val(data.subtitle);
            $('#type_icon').val(data.icon);
            $('#has_power').val(data.has_power ? '1' : '0').trigger('change');
            $('#type_status').prop('checked', data.is_active == 1);

            // FIX: Pre-fill Default Lens Package dropdown on edit
            $('#default_lens_package_id').val(data.default_lens_package_id ?? '');

            // Clean up custom chips first
            $('.power-chip:not(.predefined-chip)').remove();
            $('.power-chip').removeClass('chip-selected')
                            .css({ background: '#f1f1f1', color: '#333', borderColor: '#ccc' });

            var saved = data.default_powers ?? [];
            saved.forEach(function (val) {
                var found = false;
                $('.power-chip').each(function () {
                    if ($(this).data('value') == val) {
                        $(this).addClass('chip-selected')
                               .css({ background: '#07484A', color: '#fff', borderColor: '#07484A' });
                        found = true;
                    }
                });
                
                if (!found && val) {
                    // Re-create dynamic custom chip with remove button
                    var isNegative = String(val).startsWith('-');
                    var targetId = isNegative ? '#negativeChips' : '#positiveChips';
                    var newChip = $('<span class="badge power-chip rounded-pill border chip-selected" data-value="' + val + '" style="cursor:pointer;font-size:11px;padding:5px 9px;background:#07484A;color:#fff;border-color:#07484A !important;user-select:none" onclick="togglePowerChip(this)">' + val + ' <span class="ms-1 remove-custom-chip" style="cursor:pointer;font-weight:bold;font-size:12px;" onclick="removeCustomChip(event, this)">&times;</span></span>');
                    $(targetId).append(newChip);
                }
            });
        });
    });


    // ══════════════════════════════════════════════════════════
    //  DELETE BUTTON — SweetAlert confirm dialog
    // ══════════════════════════════════════════════════════════
    $(document).on('click', '.btn-delete-type', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name') ?? 'this type';

        // 🔴 confirm popup before deleting
        Swal.fire({
            title:              `Delete "${name}"?`,
            text:               'This action cannot be undone.',
            icon:               'warning',
            showCancelButton:   true,
            confirmButtonText:  'Yes, delete it!',
            cancelButtonText:   'Cancel',
            confirmButtonColor: '#dc3545',
            cancelButtonColor:  '#6c757d',
            reverseButtons:     true,            // Cancel left, Confirm right
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url:  `${baseUrl}/${id}`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },

                success: function (res) {
                    table.ajax.reload();

                    // ✅ deleted toast
                    Toast.fire({
                        icon:  'success',
                        title: res.message
                    });
                },

                error: function (xhr) {
                    // ❌ blocked delete (products using it)
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


    // ══════════════════════════════════════════════════════════
    //  STATUS TOGGLE — SweetAlert confirm
    // ══════════════════════════════════════════════════════════
    $(document).on('change', '.toggle-status', function () {
        var id       = $(this).data('id');
        var checkbox = $(this);
        var newState = checkbox.is(':checked');

        Swal.fire({
            title:              newState ? 'Activate this type?' : 'Deactivate this type?',
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
                // user cancelled — revert checkbox visually
                checkbox.prop('checked', !newState);
                return;
            }

            $.ajax({
                url:  `${baseUrl}/${id}/toggle`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PATCH' },

                success: function (res) {
                    table.ajax.reload(null, false); // refresh row, stay on current page
                    Toast.fire({
                        icon:  'success',
                        title: res.message
                    });
                },
                error: function () {
                    checkbox.prop('checked', !newState); // revert on error
                    Toast.fire({ icon: 'error', title: 'Toggle failed. Try again.' });
                }
            });
        });
    });


    // ── Reset modal on close ───────────────────────────────────
    $('#productTypeModal').on('hidden.bs.modal', resetForm);

}); // end ready


// ══════════════════════════════════════════════════════════════
//  HELPERS
// ══════════════════════════════════════════════════════════════

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Product Type' : 'Edit Product Type';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Type' : 'Update Type';
}

function togglePowerSection() {
    document.getElementById('powerSection')
            .classList.toggle('d-none', document.getElementById('has_power').value !== '1');
}

function togglePowerChip(el) {
    el.classList.toggle('chip-selected');
    const sel = el.classList.contains('chip-selected');
    el.style.background  = sel ? '#07484A' : '#f1f1f1';
    el.style.color       = sel ? '#fff'    : '#333';
    el.style.borderColor = sel ? '#07484A' : '#ccc';
}

function selectAllPowers(select) {
    $('.power-chip').each(function () {
        const chip = $(this);
        if (select) {
            chip.addClass('chip-selected')
                .css({ background: '#07484A', color: '#fff', borderColor: '#07484A' });
        } else {
            chip.removeClass('chip-selected')
                .css({ background: '#f1f1f1', color: '#333', borderColor: '#ccc' });
        }
    });
}

function addCustomPowerChip() {
    var val = $('#custom_power_val').val().trim();
    if (!val) return;
    
    // Check if chip already exists
    var exists = false;
    $('.power-chip').each(function() {
        if ($(this).data('value') === val) {
            exists = true;
            if (!$(this).hasClass('chip-selected')) {
                togglePowerChip(this);
            }
        }
    });
    
    if (!exists) {
        var isNegative = val.startsWith('-');
        var targetId = isNegative ? '#negativeChips' : '#positiveChips';
        var newChip = $('<span class="badge power-chip rounded-pill border chip-selected" data-value="' + val + '" style="cursor:pointer;font-size:11px;padding:5px 9px;background:#07484A;color:#fff;border-color:#07484A !important;user-select:none" onclick="togglePowerChip(this)">' + val + ' <span class="ms-1 remove-custom-chip" style="cursor:pointer;font-weight:bold;font-size:12px;" onclick="removeCustomChip(event, this)">&times;</span></span>');
        $(targetId).append(newChip);
        
        // Show correct tab visually
        if (isNegative) {
            $('#negative-tab').tab('show');
        } else {
            $('#positive-tab').tab('show');
        }
    }
    
    $('#custom_power_val').val('');
}

function removeCustomChip(e, el) {
    e.stopPropagation(); // prevent triggering parent togglePowerChip
    $(el).closest('.power-chip').remove();
}

function resetForm() {
    document.getElementById('productTypeForm').reset();
    document.getElementById('record_id').value = '';
    document.getElementById('powerSection').classList.add('d-none');

    // FIX: Reset Default Lens Package dropdown
    document.getElementById('default_lens_package_id').value = '';

    // Remove all dynamically added custom chips
    $('.power-chip:not(.predefined-chip)').remove();

    document.querySelectorAll('.power-chip').forEach(c => {
        c.classList.remove('chip-selected');
        c.style.background = '#f1f1f1';
        c.style.color      = '#333';
        c.style.borderColor = '#ccc';
    });

    // Reset tabs
    $('#positive-tab').tab('show');
    $('#custom_power_val').val('');
}
</script>
@endsection