@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Categories Master</h3>
                        <a href="#" class=" btn" data-toggle="modal" data-target="#categoryModal" onclick="openModal('add')">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add Category
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
               <div class="col-lg-12">
                <div class="domestic-orders-table">
                    <table id="categories-table" class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-5p">ID</th>
                                <th class="wd-10p">Image</th>
                                <th class="wd-15p">Name</th>
                                <th class="wd-15p">Category Type</th>
                                <th class="wd-15p">Slug</th>
                                <th class="wd-10p">Subcategories</th>
                                <th class="wd-10p">Store</th>
                                <th class="wd-10p">Created At</th>
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
<div class="modal fade" data-backdrop="static" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 540px;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="categoryModalLabel">
                    <i class="fa fa-plus-circle me-2 text-primary"></i>
                    <span id="modal-title-text">Add Category</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="categoryForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body">

                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="cat_name" name="name"
                               placeholder="e.g. Eyeglasses, Contact Lenses, Lens Solutions" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Category Type <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="cat_type" name="category_type" required>
                            <option value="frame">👓 Eyeglasses / Frame</option>
                            <option value="sunglass">🕶️ Sunglasses</option>
                            <option value="lens">👁️ Contact Lenses</option>
                            <option value="solution">🧴 Contact Lens Solution</option>
                            <option value="accessory">🎁 Eyewear Accessories</option>
                            <option value="glass">🔬 Spectacle Glass (Lab)</option>
                        </select>
                        <div class="text-muted" style="font-size:11px;margin-top:4px">Controls which product fields, specifications, and lens options appear on the website and product builder.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Slug <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="cat_slug" name="slug"
                               placeholder="e.g. eyeglasses" required>
                        <div class="text-muted" style="font-size:11px;margin-top:4px">Auto-generated · editable</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-medium">Image</label>
                        <input type="file" class="form-control" id="cat_image" name="image" accept="image/*">
                        <div class="mt-2" id="current_image_preview"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-medium">Description</label>
                        <textarea class="form-control" id="cat_description" name="description"
                                  rows="3" placeholder="Optional description…" maxlength="500"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Status</label>
                        <div class="toggle-btn">
                            <input type="checkbox" id="cat_status" name="is_active" class="toggle-switch" value="1" checked>
                            <label for="cat_status">Toggle</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Category</span>
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

$(document).ready(function () {
    const baseUrl = "{{ route('admin.categories.index') }}";

    var table = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.categories.data') }}",
        columns: [
            { data: 'id',                  name: 'id' },
            { data: 'image',               name: 'image', orderable: false, searchable: false },
            { data: 'name',                name: 'name' },
            { data: 'category_type_badge', name: 'category_type' },
            {
                data: 'slug', name: 'slug',
                render: function (data) {
                    return `<code class="bg-light px-2 py-1 rounded">${data}</code>`;
                }
            },
            { data: 'subcategories_count', name: 'subcategories_count', orderable: false, searchable: false },
            { data: 'store_name',          name: 'store_name', orderable: false, searchable: false },
            { data: 'created_at_formatted',name: 'created_at_formatted', orderable: false, searchable: false },
            { data: 'is_active',           name: 'is_active' },
            { data: 'action',              name: 'action', orderable: false, searchable: false }
        ]
    });

    // Auto slug generator
    $('#cat_name').on('input', function () {
        $('#cat_slug').val(
            $(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9_-]/g, '')
        );
    });

    // Submit Form (Add/Edit)
    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();

        var id     = $('#record_id').val();
        var isEdit = id !== '';
        var url    = isEdit ? `${baseUrl}/${id}` : "{{ route('admin.categories.store') }}";

        $('#submitBtn').prop('disabled', true);
        $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        var formData = new FormData(this);
        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url:  url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#categoryModal').modal('hide');
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
                $('#submitBtnText').text(isEdit ? 'Update Category' : 'Save Category');
            }
        });
    });

    // Edit Modal Prefill
    $(document).on('click', '.btn-edit-category', function () {
        var id = $(this).data('id');
        openModal('edit');

        $.get(`${baseUrl}/${id}/edit`, function (data) {
            $('#record_id').val(data.id);
            $('#cat_name').val(data.name);
            $('#cat_type').val(data.category_type || 'frame');
            $('#cat_slug').val(data.slug);
            $('#cat_description').val(data.description ?? '');
            $('#cat_status').prop('checked', data.is_active == 1);
            if(data.image) {
                var imageUrl = "{{ asset('') }}" + data.image;
                $('#current_image_preview').html(`<img src="${imageUrl}" width="60" style="border-radius:4px; object-fit:cover;">`);
            } else {
                $('#current_image_preview').html('');
            }
        });
    });

    // Delete Category
    $(document).on('click', '.btn-delete-category', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name') ?? 'this category';

        Swal.fire({
            title:              `Delete "${name}"?`,
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
            title:              newState ? 'Activate this category?' : 'Deactivate this category?',
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

    // Image Preview Modal
    $(document).on('click', '.preview-image', function() {
        var src = $(this).attr('src');
        Swal.fire({
            imageUrl: src,
            imageAlt: 'Image Preview',
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                image: 'img-fluid rounded'
            },
            width: 'auto',
            padding: '1em'
        });
    });

    $('#categoryModal').on('hidden.bs.modal', resetForm);
});

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Category' : 'Edit Category';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Category' : 'Update Category';
}

function resetForm() {
    document.getElementById('categoryForm').reset();
    document.getElementById('record_id').value = '';
    document.getElementById('cat_type').value = 'frame';
    document.getElementById('cat_status').checked = true;
    document.getElementById('current_image_preview').innerHTML = '';
}
</script>
@endsection
