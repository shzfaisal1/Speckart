@extends('layouts.master')

@php
    $usr = Auth::guard()->user();
@endphp

@section('content')
<style>
        .domestic-orders-date {
        padding-top: 0px;
    }
    
    .section-title{
        font-weight:600;
        border-left:4px solid #0d6efd;
        padding-left:10px;
        color:#2c3e50;
    }
    
    .table thead tr th {
        font-size: 12px;
        font-weight: 500 !important;
        color: #000;
    }
    
    .dashboard-nav{
        background:#f8f9fa;
        padding:8px;
        border-radius:10px;
    }
    
    .dashboard-nav .nav-item{
        margin-right:8px;
    }
    
    .dashboard-nav .nav-link{
        color:#2c3e50;
        font-weight:600;
        border-radius:8px;
        padding:10px 18px;
        background:#ffffff;
        border:1px solid #e3e6f0;
        transition:all .3s ease;
        font-size: 12px;
    }
    
    .dashboard-nav .nav-link i{
        margin-right:6px;
    }
    
    .dashboard-nav .nav-link:hover{
        background:#eef4ff;
        color:#00484a;
        transform:translateY(-2px);
    }
    
    .dashboard-nav .nav-link.active{
        background:#00484a;
        color:#fff;
        box-shadow:0 4px 12px rgba(0,0,0,0.1);
        font-size: 12px;
    }
    
    .nav-link i {
        color: #000;
    }
    
    .nav-link.active i {
        color: #fff;
    }
    
        .col-md-3
{
    margin-bottom: 10px;
}
.staff-performance-dashboard-card{
    border: 1px solid #d9dde7;
    height: 365px;
    overflow: auto;
}
.store-performance-dashboard{
    border: 1px solid #d9dde7;
    width: 100%;
    height: 250px;
    overflow: auto;
}
.staff-performance-dashboard-card .table-responsive,
.store-performance-dashboard .table-responsive{
    height: 100%;
}
.walk-in-entry-history{
    /*border: 1px solid #d9dde7;*/
    width: 100%;
    height: 500px;
    overflow: auto;
}
.walk-in-entry-history .table-responsive{height: 100%;}

    /* Small improvement - better loader visibility */
    #ajaxLoader {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(255,255,255,0.75);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
</style>

<div id="ajaxLoader">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 fw-bold">Loading dashboard...</p>
    </div>
</div>

<div class="dashboard-tabs mb-4">
    <ul class="nav nav-pills dashboard-nav">
        <li class="nav-item">
            <a class="nav-link"  href="{{route('index')}}">
                <i class="fa fa-sign-in"></i>
                Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('admin.walkin-dashboard')}}">
                <i class="fa fa-sign-in"></i>
                Walk-In Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link "  href="{{route('admin.audit-dashboard')}}">
                <i class="fa fa-search"></i>
                Mystery Audit
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link active"  href="{{route('admin.nps-dashboard')}}">
                <i class="fa fa-smile-o"></i>
                NPS Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link "  href="{{route('admin.sale-dashboard')}}">
                <i class="fa fa-line-chart"></i>
                Sales Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.account-dashboard')}}">
                <i class="fa fa-money"></i>
                Account Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.counting-dashboard')}}">
                <i class="fa fa-google-wallet"></i>
                Product Counting Dashboard
            </a>
        </li>

    </ul>
</div>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center mb-8 flex-wrap gap-4">
            
            
    
            <form id="filterForm" class="flex items-center gap-3">
                
                @if($usr->roles[0]->name == 'Admin')
                <div>
                    <select class="form-control select2" style="width: 260px; height: 38px;" id="store_id" name="store_id">
                        <option value="">All Stores</option>
                        @php
                            $stores = DB::table("tbl_store")->where('status', 1)->get();
                        @endphp
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">
                                {{ $store->store_name }} / ({{ $store->store_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="domestic-orders-date">
                    <div id="reportrange" class="pull-left"
                         style="background: #fff; cursor: pointer; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                    <input type="hidden" id="date_from" name="date_from">
                    <input type="hidden" id="date_to" name="date_to">
                </div>
            </form>
            
            <form id="markPendingForm" action="{{ route('admin.mark-pending-sent') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-success btn-sm px-4" id="sendAllPendingBtn">
        <i class="fa fa-paper-plane me-2"></i> Send All Pending
        <span class="badge bg-light text-primary ms-2">
            {{ $pendingCount ?? 0 }}
        </span>
    </button>
</form>
  <button type="button" class="btn btn-primary btn-sm px-4" onclick="exportNpsData()">
        <i class="fa fa-download me-2"></i> Export Data
    </button>
        </div>

        <div id="dashboard-content">
            <!-- Will be filled by AJAX on load -->
            <div class="text-center py-20 text-gray-600">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3">Loading NPS dashboard...</p>
            </div>
        </div>

        <!-- The rest of your sections are moved to layouts.dashboard_content.blade.php -->
    </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    let categoryChart = null;

    function renderChart(chartData) {
        const canvas = document.getElementById('categoryChart');
        if (!canvas) return;

        if (categoryChart) {
            categoryChart.destroy();
        }

        categoryChart = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Excellent', 'Good', 'Average', 'Poor'],
                datasets: [{
                    label: 'Percentage of Responses (%)',
                    data: [
                        chartData.Excellent || 0,
                        chartData.Good || 0,
                        chartData.Average || 0,
                        chartData.Poor || 0
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.65)',
                        'rgba(59, 130, 246, 0.65)',
                        'rgba(234, 179, 8, 0.65)',
                        'rgba(239, 68, 68, 0.65)'
                    ],
                    borderColor: [
                        'rgb(21, 128, 61)',
                        'rgb(29, 78, 216)',
                        'rgb(161, 98, 7)',
                        'rgb(185, 28, 28)'
                    ],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: { display: true, text: 'Percentage (%)' },
                        ticks: { callback: value => value + '%' }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: context => context.raw + '%'
                        }
                    }
                }
            }
        });
    }

    function loadDashboard() {
        const start = $('#date_from').val() || moment().format('YYYY-MM-DD');
        const end   = $('#date_to').val() || moment().format('YYYY-MM-DD');
        const store = $('#store_id').val() || '';

        let url = `{{ url('/reports/nps') }}?date_from=${start}&date_to=${end}`;
        if (store) url += `&store_id=${store}`;

        $('#ajaxLoader').fadeIn(200);
        $('#dashboard-content').css({ opacity: 0.45 });

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
    $('#dashboard-content').html(data.html);

    // Existing chart
    if (data.chartData) {
        renderChart(data.chartData);
    }

    // ✅ ADD THIS (your new feature)
    if (data.questionStats) {
        questionData = data.questionStats;
    }

    // Chart logic removed as per user request to show ratings only
})
        .catch(err => {
            console.error('Dashboard load error:', err);
            $('#dashboard-content').html(`
                <div class="alert alert-danger text-center py-10">
                    Failed to load dashboard. Please try again.<br>
                    <small>${err.message}</small>
                </div>
            `);
        })
        .finally(() => {
            $('#ajaxLoader').fadeOut(200);
            $('#dashboard-content').css({ opacity: 1 });
        });
    }

// Charts removed as per user request to show ratings directly
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2({
            placeholder: "All Stores",
            allowClear: true
        });

        // Date range picker
        const start = moment();
        const end   = moment();

        function updateDateDisplay(start, end) {
            $('#reportrange span').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
            $('#date_from').val(start.format('YYYY-MM-DD'));
            $('#date_to').val(end.format('YYYY-MM-DD'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            maxDate: moment(),
            locale: {
                format: 'DD-MM-YYYY',
                cancelLabel: 'Clear'
            }
        }, function(start, end) {
            updateDateDisplay(start, end);
            loadDashboard();
        });

        // Initial display
        updateDateDisplay(start, end);

        // Load on page ready
        loadDashboard();

        // Also load when store changes
        $('#store_id').on('change', loadDashboard);

        // Form submit (just in case)
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            loadDashboard();
        });
        
         @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
    });
    
    $(document).ready(function () {
    $('#markPendingForm').on('submit', function (e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to mark ALL these as sent?')) {
            return;
        }

        let form = this;
        let formData = new FormData(form);
        let btn = $('#sendAllPendingBtn');

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Sending...');

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(response.message || 'Followup sent successfully.');
                    if (response.pendingCount !== undefined) {
                        $('#sendAllPendingBtn .badge').text(response.pendingCount);
                    }
                    loadDashboard();
                } else {
                    toastr.error(response.message || 'Something went wrong.');
                }
            },
            error: function (xhr) {
                let msg = 'Something went wrong.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            },
            complete: function () {
                btn.prop('disabled', false).html(
                    '<i class="fa fa-paper-plane me-2"></i> Send All Pending <span class="badge bg-light text-primary ms-2">{{ $pendingCount ?? 0 }}</span>'
                );
            }
        });
    });
});

function exportNpsData() {
    const start = $('#date_from').val() || moment().format('YYYY-MM-DD');
    const end   = $('#date_to').val() || moment().format('YYYY-MM-DD');
    const store = $('#store_id').val() || '';

    let url = `{{ route('admin.nps-export') }}?date_from=${start}&date_to=${end}`;
    if (store) {
        url += `&store_id=${store}`;
    }

    window.location.href = url;
}
</script>

@endsection