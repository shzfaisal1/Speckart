@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Lens Tags Master</h3>
                        <a href="#" class="btn" data-toggle="modal" data-target="#tagModal" onclick="openModal('add')">
                            <span><i class="fa fa-plus"></i></span>
                            Add Tag
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-table">
                        <table id="tags-table" class="table datatables-basic w-100">
                            <thead>
                                <tr>
                                    <th class="wd-10p">ID</th>
                                    <th class="wd-30p">Name</th>
                                    <th class="wd-30p">Slug</th>
                                    <th class="wd-10p">Sort Order</th>
                                    <th class="wd-10p">Packages</th>
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
<div class="modal fade" data-backdrop="static" id="tagModal" tabindex="-1" role="dialog" aria-labelledby="tagModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="tagModalLabel">
                    <i class="fa fa-plus-circle me-2 text-primary"></i>
                    <span id="modal-title-text">Add Lens Tag</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="tagForm">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body">

                    {{-- Name --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Tag Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="tag_name" name="name"
                               placeholder="e.g. Work Friendly" required>
                    </div>

                    {{-- Slug --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Slug <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="tag_slug" name="slug"
                               placeholder="e.g. work-friendly" required>
                        <div class="text-muted" style="font-size:11px;margin-top:4px">Auto-generated · editable</div>
                    </div>

                    {{-- Icon URL --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">Icon URL</label>
                        <input type="text" class="form-control" id="tag_icon_url" name="icon_url"
                               placeholder="e.g. /assets/icons/work.png">
                    </div>

                    {{-- Sort Order --}}
                    <div class="form-group">
                        <label class="form-label fw-medium">Sort Order</label>
                        <input type="number" min="0" class="form-control" id="tag_sort_order" name="sort_order"
                               value="0">
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Status</label>
                        <div class="toggle-btn">
                            <input type="checkbox" id="tag_status" name="is_active" class="toggle-switch" value="1" checked>
                            <label for="tag_status">Toggle</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Tag</span>
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
    const baseUrl = "{{ url('lens-tags') }}";

    var table = $('#tags-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.lens-tags.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'slug_badge', name: 'slug' },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'packages_count', name: 'packages_count', searchable: false },
            { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Auto slug generator
    $('#tag_name').on('input', function () {
        $('#tag_slug').val(
            $(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9_-]/g, '')
        );
    });

    // Form submit
    $('#tagForm').on('submit', function (e) {
        e.preventDefault();

        var id     = $('#record_id').val();
        var isEdit = id !== '';
        var url    = isEdit ? `${baseUrl}/${id}` : "{{ route('admin.lens-tags.store') }}";

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
                $('#tagModal').modal('hide');
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
                $('#submitBtnText').text(isEdit ? 'Update Tag' : 'Save Tag');
            }
        });
    });

    // Edit button click
    $(document).on('click', '.btn-edit-tag', function () {
        var id = $(this).data('id');
        openModal('edit');

        $.get(`${baseUrl}/${id}/edit`, function (data) {
            $('#record_id').val(data.id);
            $('#tag_name').val(data.name);
            $('#tag_slug').val(data.slug);
            $('#tag_icon_url').val(data.icon_url);
            $('#tag_sort_order').val(data.sort_order);
            $('#tag_status').prop('checked', data.is_active == 1);
        });
    });

    // Delete button click
    $(document).on('click', '.btn-delete-tag', function () {
        var id = $(this).data('id');

        Swal.fire({
            title:              'Delete this Lens Tag?',
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
            title:              newState ? 'Activate this Tag?' : 'Deactivate this Tag?',
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

    $('#tagModal').on('hidden.bs.modal', resetForm);
});

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Lens Tag' : 'Edit Lens Tag';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Tag' : 'Update Tag';
}

function resetForm() {
    document.getElementById('tagForm').reset();
    document.getElementById('record_id').value = '';
    document.getElementById('tag_status').checked = true;
}
</script>
@endsection
