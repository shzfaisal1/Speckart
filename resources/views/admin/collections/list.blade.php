@extends('layouts.master')

@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Collections Master</h3>
                        <a href="#" class=" btn" data-toggle="modal" data-target="#collectionModal" onclick="openModal('add')">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add Collection
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
               <div class="col-lg-12">
                <div class="domestic-orders-table">
                    <table id="collections-table" class="table datatables-basic w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">ID</th>
                                <th class="wd-30p">Name</th>
                                <th class="wd-30p">Slug</th>
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
<div class="modal fade" data-backdrop="static" id="collectionModal" tabindex="-1" role="dialog" aria-labelledby="collectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="collectionModalLabel">
                    <i class="fa fa-plus-circle me-2 text-primary"></i>
                    <span id="modal-title-text">Add Collection</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="collectionForm">
                @csrf
                <input type="hidden" id="record_id" name="record_id" value="">

                <div class="modal-body">

                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Collection Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="coll_name" name="name"
                               placeholder="e.g. Classic Acetates" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-medium">
                            Slug <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="coll_slug" name="slug"
                               placeholder="e.g. classic-acetates" required>
                        <div class="text-muted" style="font-size:11px;margin-top:4px">Auto-generated · editable</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-medium d-block">Status</label>
                        <div class="toggle-btn">
                            <input type="checkbox" id="coll_status" name="is_active" class="toggle-switch" value="1" checked>
                            <label for="coll_status">Toggle</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Collection</span>
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
    const baseUrl = "{{ url('collections') }}";

    var table = $('#collections-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.collections.data') }}",
        columns: [
            { data: 'id',   name: 'id' },
            { data: 'name', name: 'name' },
            {
                data: 'slug', name: 'slug',
                render: function (data) {
                    return `<code class="bg-light px-2 py-1 rounded">${data}</code>`;
                }
            },
            { data: 'is_active', name: 'is_active' },
            { data: 'action',    name: 'action', orderable: false, searchable: false }
        ]
    });

    // Auto slug generator
    $('#coll_name').on('input', function () {
        $('#coll_slug').val(
            $(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9_-]/g, '')
        );
    });

    // Submit Form (Add/Edit)
    $('#collectionForm').on('submit', function (e) {
        e.preventDefault();

        var id     = $('#record_id').val();
        var isEdit = id !== '';
        var url    = isEdit ? `${baseUrl}/${id}` : "{{ route('admin.collections.store') }}";

        $('#submitBtn').prop('disabled', true);
        $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

        var formData = $(this).serialize();
        if (isEdit) {
            formData += '&_method=PUT';
        }

        $.ajax({
            url:  url,
            type: 'POST',
            data: formData,
            success: function (res) {
                $('#collectionModal').modal('hide');
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
                $('#submitBtnText').text(isEdit ? 'Update Collection' : 'Save Collection');
            }
        });
    });

    // Edit Modal Prefill
    $(document).on('click', '.btn-edit-collection', function () {
        var id = $(this).data('id');
        openModal('edit');

        $.get(`${baseUrl}/${id}/edit`, function (data) {
            $('#record_id').val(data.id);
            $('#coll_name').val(data.name);
            $('#coll_slug').val(data.slug);
            $('#coll_status').prop('checked', data.is_active == 1);
        });
    });

    // Delete Collection
    $(document).on('click', '.btn-delete-collection', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name') ?? 'this collection';

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
            title:              newState ? 'Activate this collection?' : 'Deactivate this collection?',
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

    $('#collectionModal').on('hidden.bs.modal', resetForm);
});

function openModal(mode) {
    document.getElementById('modal-title-text').textContent =
        mode === 'add' ? 'Add Collection' : 'Edit Collection';
    document.getElementById('submitBtnText').textContent =
        mode === 'add' ? 'Save Collection' : 'Update Collection';
}

function resetForm() {
    document.getElementById('collectionForm').reset();
    document.getElementById('record_id').value = '';
    document.getElementById('coll_status').checked = true;
}
</script>
@endsection
