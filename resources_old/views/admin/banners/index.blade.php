@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Homepage Banners</h3>
                        <a href="#" class="btn" data-toggle="modal" data-target="#bannerModal" onclick="openModal('add')">
                            <span><i class="fa fa-plus"></i></span>
                            Add Banner
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="domestic-orders-table">
                        <table id="banners-table" class="table datatables-basic w-100">
                            <thead>
                                <tr>
                                    <th class="wd-5p">ID</th>
                                    <th class="wd-15p">Preview</th>
                                    <th class="wd-15p">Title</th>
                                    <th class="wd-10p">Position</th>
                                    <th class="wd-20p">Target/Link</th>
                                    <th class="wd-10p">Order</th>
                                    <th class="wd-10p">Status</th>
                                    <th class="wd-15p">Action</th>
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
     ADD / EDIT BANNER MODAL
     ════════════════════════════════════════════════ --}}
<div class="modal fade" data-backdrop="static" id="bannerModal" tabindex="-1" role="dialog" aria-labelledby="bannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="bannerModalLabel">
                    <i class="fa fa-picture-o me-2 text-primary"></i>
                    <span id="modal-title-text">Add Banner</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="bannerForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">Banner Title</label>
                                <input type="text" class="form-control" id="ban_title" name="title" placeholder="e.g. Summer Specs Sale">
                            </div>

                            <div class="form-group">
                                <label class="form-label fw-medium">Banner Image <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="ban_image" name="image" accept="image/*">
                                <small class="text-muted d-block mt-1">Recommended size: 1920x650 (slider) or 1200x400 (promo). Max: 2MB.</small>
                                <div id="image-preview-container" class="mt-2 d-none">
                                    <label class="d-block text-muted" style="font-size:11px;">Current Image:</label>
                                    <img id="image-preview" src="" alt="Banner Preview" style="max-width: 100%; height: 80px; object-fit: cover; border-radius: 4px;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label fw-medium">Display Position <span class="text-danger">*</span></label>
                                <select class="form-control" id="ban_position" name="position" required>
                                    <option value="main_slider">Main Slider (Top Carousel)</option>
                                    <option value="promo_1">Promo Banner Row 1</option>
                                    <option value="promo_2">Promo Banner Row 2</option>
                                    <option value="spotlight">Spotlight (Buy 1 Get 1 Section)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label fw-medium">Sort Order</label>
                                <input type="number" class="form-control" id="ban_sort_order" name="sort_order" value="0" min="0" required>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-medium">Action/Link Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="ban_link_type" name="link_type" required>
                                    <option value="offer">Link to Offer/Promotion</option>
                                    <option value="category">Link to Category</option>
                                    <option value="product">Link to Specific Product</option>
                                    <option value="custom_url">Link to Custom URL</option>
                                </select>
                            </div>

                            <!-- Offer Selector -->
                            <div class="form-group link-selector" id="sec_offer">
                                <label class="form-label fw-medium">Target Offer <span class="text-danger">*</span></label>
                                <select class="form-control" id="link_id_offer" name="link_id_offer">
                                    <option value="">Select Offer</option>
                                    @foreach($offers as $o)
                                        <option value="{{ $o->id }}">{{ $o->name }} ({{ strtoupper($o->offer_type) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category Selector -->
                            <div class="form-group link-selector d-none" id="sec_category">
                                <label class="form-label fw-medium">Target Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="link_id_category" name="link_id_category">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Product Selector -->
                            <div class="form-group link-selector d-none" id="sec_product">
                                <label class="form-label fw-medium">Target Product <span class="text-danger">*</span></label>
                                <select class="form-control select2-product w-100" id="link_id_product" name="link_id_product" style="width: 100%;">
                                    <option value="">Search & Select Product</option>
                                </select>
                            </div>

                            <!-- Custom URL Input -->
                            <div class="form-group link-selector d-none" id="sec_custom_url">
                                <label class="form-label fw-medium">Custom Link URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ban_custom_url" name="custom_url" placeholder="e.g. /new-arrivals or https://google.com">
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label fw-medium">Start Date (Optional)</label>
                                        <input type="date" class="form-control" id="ban_start_date" name="start_date">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label fw-medium">End Date (Optional)</label>
                                        <input type="date" class="form-control" id="ban_end_date" name="end_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label fw-medium d-block">Status</label>
                                <div class="toggle-btn">
                                    <input type="checkbox" id="ban_status" name="is_active" class="toggle-switch" value="1" checked>
                                    <label for="ban_status">Toggle</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Banner</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- select2 stylesheet if required -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        z-index: 1060 !important; /* Ensure Select2 dropdown renders above Bootstrap modal */
    }
</style>
@endpush

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
    const baseUrl = "{{ url('banners') }}";

    var table = $('#banners-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.banners.data') }}",
        columns: [
            { data: 'id',         name: 'id' },
            { data: 'image',      name: 'image', orderable: false, searchable: false },
            { data: 'title',      name: 'title' },
            { data: 'position',   name: 'position' },
            { data: 'link_info',  name: 'link_info', orderable: false },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'is_active',  name: 'is_active' },
            { data: 'action',     name: 'action', orderable: false, searchable: false }
        ]
    });

    // Handle Link Type change to swap selectors
    $('#ban_link_type').on('change', function () {
        var type = $(this).val();
        $('.link-selector').addClass('d-none');
        
        if (type === 'offer') {
            $('#sec_offer').removeClass('d-none');
        } else if (type === 'category') {
            $('#sec_category').removeClass('d-none');
        } else if (type === 'product') {
            $('#sec_product').removeClass('d-none');
        } else if (type === 'custom_url') {
            $('#sec_custom_url').removeClass('d-none');
        }
    });

    // Initialize Select2 for product autocomplete
    $('#link_id_product').select2({
        ajax: {
            url: "{{ route('admin.banners.search-products') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term // search query
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.product_name + ' (' + item.product_code + ')'
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: 'Search & Select Product',
        minimumInputLength: 1,
        dropdownParent: $('#bannerModal')
    });

    // Submit Form (Add/Edit)
    $('#bannerForm').on('submit', function (e) {
        e.preventDefault();

        var id     = $('#record_id').val();
        var isEdit = id !== '';
        var url    = isEdit ? `${baseUrl}/${id}` : "{{ route('admin.banners.store') }}";

        $('#submitBtn').prop('disabled', true);
        $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        var formData = new FormData(this);

        // Determine link ID based on type
        var type = $('#ban_link_type').val();
        if (type === 'offer') {
            formData.append('link_id', $('#link_id_offer').val());
        } else if (type === 'category') {
            formData.append('link_id', $('#link_id_category').val());
        } else if (type === 'product') {
            formData.append('link_id', $('#link_id_product').val());
        }

        $.ajax({
            url:  url,
            type: 'POST', // Always POST, even for edit to upload files cleanly
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#bannerModal').modal('hide');
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
                $('#submitBtnText').text(isEdit ? 'Update Banner' : 'Save Banner');
            }
        });
    });

    // Edit Modal Prefill
    $(document).on('click', '.btn-edit-banner', function () {
        var id = $(this).data('id');
        openModal('edit');

        $.get(`${baseUrl}/${id}/edit`, function (data) {
            $('#record_id').val(data.id);
            $('#ban_title').val(data.title);
            $('#ban_position').val(data.position);
            $('#ban_sort_order').val(data.sort_order);
            $('#ban_link_type').val(data.link_type).trigger('change');
            $('#ban_custom_url').val(data.custom_url);
            $('#ban_status').prop('checked', data.is_active == 1);
            
            if (data.start_date) {
                $('#ban_start_date').val(data.start_date.substring(0, 10));
            }
            if (data.end_date) {
                $('#ban_end_date').val(data.end_date.substring(0, 10));
            }

            if (data.image_path) {
                $('#image-preview').attr('src', "{{ asset('/') }}" + data.image_path);
                $('#image-preview-container').removeClass('d-none');
                $('#ban_image').prop('required', false);
            }

            // Fill selected entity based on type
            if (data.link_type === 'offer') {
                $('#link_id_offer').val(data.link_id);
            } else if (data.link_type === 'category') {
                $('#link_id_category').val(data.link_id);
            } else if (data.link_type === 'product' && data.link_id) {
                // For select2 product, we prefetch the details or make a temp option
                // First get product name via AJAX, then append it
                $.get(`${baseUrl}/search-products?id=` + data.link_id, function () {
                    // Quick query builder check
                    var option = new Option('Selected Product (ID: ' + data.link_id + ')', data.link_id, true, true);
                    $('#link_id_product').append(option).trigger('change');
                });
            }
        });
    });

    // Delete Banner
    $(document).on('click', '.btn-delete-banner', function () {
        var id   = $(this).data('id');
        var title = $(this).data('title') ?? 'this banner';

        Swal.fire({
            title:              `Delete "${title}"?`,
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

    // Toggle Status
    $(document).on('change', '.toggle-status', function () {
        var id       = $(this).data('id');
        var checkbox = $(this);
        var newState = checkbox.is(':checked');

        Swal.fire({
            title:              newState ? 'Activate this banner?' : 'Deactivate this banner?',
            text:               newState ? 'It will show on home page.' : 'It will be hidden from home page.',
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

    $('#bannerModal').on('hidden.bs.modal', resetForm);
});

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Banner' : 'Edit Banner';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Banner' : 'Update Banner';
    
    if (mode === 'add') {
        $('#ban_image').prop('required', true);
        $('#image-preview-container').addClass('d-none');
    }
}

function resetForm() {
    document.getElementById('bannerForm').reset();
    document.getElementById('record_id').value = '';
    document.getElementById('ban_status').checked = true;
    $('#ban_link_type').val('offer').trigger('change');
    $('#link_id_product').val(null).trigger('change');
    $('#image-preview-container').addClass('d-none');
    $('#image-preview').attr('src', '');
}
</script>
@endsection
