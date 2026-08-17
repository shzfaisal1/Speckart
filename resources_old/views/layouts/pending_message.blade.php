
@extends('layouts.master')

@php
    $usr = Auth::guard()->user();
@endphp

@section('content')
<style>
    .domestic-orders-date {
        padding-top: 0px;
    }
    .section-title {
        font-weight: 600;
        border-left: 4px solid #0d6efd;
        padding-left: 10px;
        color: #2c3e50;
    }
    .table thead tr th {
        font-size: 12px;
        font-weight: 500 !important;
        color: #000;
    }
    .dashboard-nav {
        background: #f8f9fa;
        padding: 8px;
        border-radius: 10px;
    }
    .dashboard-nav .nav-item {
        margin-right: 8px;
    }
    .dashboard-nav .nav-link {
        color: #2c3e50;
        font-weight: 600;
        border-radius: 8px;
        padding: 10px 18px;
        background: #ffffff;
        border: 1px solid #e3e6f0;
        transition: all .3s ease;
    }
    .dashboard-nav .nav-link i {
        margin-right: 6px;
    }
    .dashboard-nav .nav-link:hover {
        background: #eef4ff;
        color: #00484a;
        transform: translateY(-2px);
    }
    .dashboard-nav .nav-link.active {
        background: #00484a;
        color: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .nav-link i {
        color: #000;
    }
    .nav-link.active i {
        color: #fff;
    }
</style>

<div class="container-fluid mt-4">
    <div class="max-w-7xl mx-auto">

        <!-- Navigation tabs -->
        <div class="dashboard-tabs mb-4">
            <ul class="nav nav-pills dashboard-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('index') }}">
                        <i class="fa fa-sign-in"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.walkin-dashboard') }}">
                        <i class="fa fa-sign-in"></i> Walk-In Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">
                        <i class="fa fa-search"></i> Mystery Audit
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.nps-dashboard') }}">
                        <i class="fa fa-smile-o"></i> NPS Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.sale-dashboard') }}">
                        <i class="fa fa-line-chart"></i> Sales Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.pending-messages') }}">
                        <i class="fa fa-whatsapp"></i> Pending Messages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.account-dashboard') }}">
                        <i class="fa fa-money"></i> Account Dashboard
                    </a>
                </li>
            </ul>
        </div>

        <!-- Header + Send Button -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="section-title mb-0">Pending WhatsApp Follow-up Messages</h3>
                <small class="text-muted">
                    Sales from {{ today()->subDays(2)->format('d M Y') }} with WhatsApp follow-up not sent
                </small>
            </div>

            @if(!$pending->isEmpty())
            <form action="{{ route('admin.mark-pending-sent') }}" method="POST" onsubmit="return confirm('Are you sure you want to mark ALL these as sent?');">
                @csrf
                <button type="submit" class="btn btn-success btn-sm px-4">
                    <i class="fa fa-paper-plane me-2"></i> Send All Pending
                </button>
            </form>
            @endif
        </div>

        <!-- Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="pendingTable" class="table table-bordered table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Sale Date</th>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Store</th>
                                <th>Order No</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pending as $index => $sale)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                                    <td>{{ $sale->cust_name ?? '-' }}</td>
                                    <td>
                                        @if($sale->contact_no)
                                            <a href="https://wa.me/91{{ $sale->contact_no }}" target="_blank" class="text-success">
                                                {{ $sale->contact_no }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $sale->store_name ?? '-' }}</td>
                                    <td>{{ $sale->order_no ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>

$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#pendingTable')) {
        $('#pendingTable').DataTable().destroy();
    }

    $('#pendingTable').DataTable({
        responsive: true,
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        order: [[1, 'desc']],
        language: {
            emptyTable: "No pending follow-up messages found",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            infoEmpty: "No records available",
            search: "Filter records:",
            lengthMenu: "Show _MENU_ records"
        }
    });

    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
});
</script>


@endsection