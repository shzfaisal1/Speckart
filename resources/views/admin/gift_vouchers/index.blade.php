@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.voucher-list-wrap {
    font-family: 'Inter', sans-serif;
    padding: 0 8px;
}
.voucher-list-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}
.voucher-list-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: #1a1d29;
    margin: 0;
}
.voucher-list-header h2 i {
    color: #7e22ce;
    margin-right: 8px;
}
.btn-create-voucher {
    background: linear-gradient(135deg, #7e22ce, #9333ea);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(126,34,206,0.25);
}
.btn-create-voucher:hover {
    color: #fff;
    box-shadow: 0 6px 18px rgba(126,34,206,0.35);
    transform: translateY(-1px);
    text-decoration: none;
}
.voucher-list-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
    padding: 24px;
}
.status-toggle {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 22px;
    margin: 0;
    vertical-align: middle;
}
.status-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}
.status-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    border-radius: 22px;
    transition: all 0.3s ease;
}
.status-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: all 0.3s ease;
}
.status-toggle input:checked + .status-slider {
    background-color: #10b981;
}
.status-toggle input:checked + .status-slider:before {
    transform: translateX(22px);
}
</style>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid voucher-list-wrap">

            <div class="voucher-list-header">
                <h2><i class="fa fa-ticket"></i> Gift Vouchers</h2>
                <a href="{{ url(config('app.admin_path') . '/gift-vouchers/create') }}" class="btn-create-voucher">
                    <i class="fa fa-plus-circle"></i> Create New Voucher
                </a>
            </div>

            <div class="voucher-list-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="vouchersTable" style="width:100%">
                        <thead>
                            <tr style="background:#f8fafc;color:#475569;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;">
                                <th style="width:40px">#</th>
                                <th>Voucher Name</th>
                                <th>Voucher Code</th>
                                <th>Value</th>
                                <th>Eligible Membership</th>
                                <th>Validity</th>
                                <th>Status</th>
                                <th style="width:90px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    const table = $('#vouchersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url(config('app.admin_path') . '/gift-vouchers') }}",
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'voucher_value', name: 'voucher_value' },
            { data: 'membership', name: 'membership' },
            { data: 'validity', name: 'validity' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search vouchers..."
        }
    });

    // Toggle Status
    $(document).on('change', '.toggle-status', function () {
        const id = $(this).data('id');
        $.ajax({
            url: "{{ url(config('app.admin_path') . '/gift-vouchers') }}/" + id + "/toggle-status",
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message,
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            error: function () {
                table.ajax.reload(null, false);
                Swal.fire('Error', 'Could not update status.', 'error');
            }
        });
    });

    // Delete Voucher
    $(document).on('click', '.btn-delete-voucher', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete Gift Voucher?',
            text: "This voucher will be permanently removed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url(config('app.admin_path') . '/gift-vouchers') }}/" + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        Swal.fire('Deleted!', res.message, 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function () {
                        Swal.fire('Error', 'Could not delete voucher.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
