@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Power Types Master</h3>
                        <a href="#" class="btn" data-toggle="modal" data-target="#powerTypeModal" onclick="openModal('add')">
                            <span><i class="fa fa-plus"></i></span>
                            Add Power Type
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-table">
                        <table id="power-table" class="table datatables-basic w-100">
                            <thead>
                                <tr>
                                    <th class="wd-10p">ID</th>
                                    <th class="wd-30p">Description</th>
                                    <th class="wd-20p">Image</th>
                                    <th class="wd-20p">Tag</th>
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
<div class="modal fade" data-backdrop="static" id="powerTypeModal" tabindex="-1" role="dialog" aria-labelledby="powerTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="powerTypeModalLabel">
                    <i class="fa fa-plus-circle me-2 text-primary"></i>
                    <span id="modal-title-text">Add Power Type</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="powerTypeForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body">

                    {{-- Description --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="pt_description" name="description"
                                  rows="3" placeholder="Enter power type description..." required></textarea>
                    </div>

                    {{-- Image --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">Image</label>
                        <input type="file" class="form-control" id="pt_image" name="image"
                               accept="image/jpeg,image/png,image/webp">
                        <div class="text-muted" style="font-size:11px;margin-top:4px">
                            Accepted: JPG, PNG, WEBP · Max 2MB
                        </div>
                        {{-- Image preview --}}
                        <div id="imagePreviewWrapper" class="mt-2 d-none">
                            <div class="position-relative d-inline-block">
                                <img id="imagePreview" src="" alt="Preview"
                                     class="img-thumbnail" style="max-height:120px;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute"
                                        style="top:0;right:0;border-radius:50%;width:24px;height:24px;padding:0;line-height:24px;font-size:12px;"
                                        onclick="clearImage()">
                                    &times;
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Tag --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">Tag</label>
                        <input type="text" class="form-control" id="pt_tag" name="tag"
                               placeholder="e.g. Popular, New, Trending">
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Status</label>
                        <div class="toggle-btn">
                            <input type="checkbox" id="pt_status" name="is_active" class="toggle-switch" value="1" checked>
                            <label for="pt_status">Toggle</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Power Type</span>
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
    const baseUrl = "{{ route('admin.power-types.index') }}";

    var table = $('#power-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.power-types.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'description', name: 'description' },
            { data: 'images', name: 'images', orderable: false, searchable: false },
            { data: 'tag', name: 'tag', orderable: false },
            { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Image preview
    $('#pt_image').on('change', function () {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewWrapper').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Form submit
    $('#powerTypeForm').on('submit', function (e) {
        e.preventDefault();

        var id     = $('#record_id').val();
        var isEdit = id !== '';
        var url    = isEdit ? `${baseUrl}/${id}` : "{{ route('admin.power-types.store') }}";

        var formData = new FormData(this);
        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        $('#submitBtn').prop('disabled', true);
        $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        $.ajax({
            url:  url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                $('#powerTypeModal').modal('hide');
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
                $('#submitBtnText').text(isEdit ? 'Update Power Type' : 'Save Power Type');
            }
        });
    });

    // Edit button click
    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        openModal('edit');

        $.get(`${baseUrl}/${id}/edit`, function (data) {
            $('#record_id').val(data.id);
            $('#pt_description').val(data.description);
            $('#pt_tag').val(data.tag);
            $('#pt_status').prop('checked', data.is_active == 1);

            if (data.image_url) {
                $('#imagePreview').attr('src', data.image_url);
                $('#imagePreviewWrapper').removeClass('d-none');
            }
        });
    });

    // Delete button click
    $(document).on('click', '.btn-delete', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name') ?? 'this power type';

        Swal.fire({
            title:              'Delete this Power Type?',
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
            title:              newState ? 'Activate this Power Type?' : 'Deactivate this Power Type?',
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

    $('#powerTypeModal').on('hidden.bs.modal', resetForm);
});

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Power Type' : 'Edit Power Type';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Power Type' : 'Update Power Type';
}

function clearImage() {
    document.getElementById('pt_image').value = '';
    document.getElementById('imagePreviewWrapper').classList.add('d-none');
    document.getElementById('imagePreview').src = '';
}

function resetForm() {
    document.getElementById('powerTypeForm').reset();
    document.getElementById('record_id').value = '';
    clearImage();
    document.getElementById('pt_status').checked = true;
}
</script>
@endsection
