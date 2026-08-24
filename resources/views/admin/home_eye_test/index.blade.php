@extends('layouts.master')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap');

.het-wrap {
    font-family: 'Inter', sans-serif;
    padding: 0 4px;
}
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.kpi-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
    text-decoration: none !important;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.kpi-title {
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.kpi-value {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 4px;
}
.kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

/* Filter Card */
.filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

/* Table Card */
.table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.het-table th {
    background: #f8fafc;
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
}
.het-table td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
    color: #1e293b;
}
.het-table tr:hover td {
    background: #f8fafc;
}
.badge-status {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-confirmed { background: #dcfce7; color: #166534; }
.badge-assigned  { background: #e0f2fe; color: #075985; }
.badge-completed { background: #dbeafe; color: #1e40af; }
.badge-cancelled { background: #fee2e2; color: #991b1b; }
.booking-id-tag {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 600;
    font-size: 12.5px;
    color: #0c5a5d;
    background: #f0fdfa;
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px solid #ccfbf1;
}
</style>

<div class="het-wrap container-fluid">
    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <h3 class="mb-1 fw-bold text-dark"><i class="bi bi-house-door-fill text-teal me-2"></i> Home Eye Test Appointments</h3>
            <p class="text-muted mb-0" style="font-size:13px;">Manage, track, and assign certified optometrists for doorstep eye testing bookings.</p>
        </div>
        <div class="col-md-6 text-md-end mt-2 mt-md-0">
            <a href="{{ route('home-eye-test') }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Website Booking Page
            </a>
        </div>
    </div>

    {{-- KPI Cards Grid --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div>
                <div class="kpi-title">Total Bookings</div>
                <div class="kpi-value">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="kpi-icon bg-light text-primary"><i class="bi bi-calendar2-check-fill"></i></div>
        </div>
        <div class="kpi-card">
            <div>
                <div class="kpi-title">Confirmed / Pending</div>
                <div class="kpi-value text-success">{{ number_format($stats['confirmed']) }}</div>
            </div>
            <div class="kpi-icon bg-success-subtle text-success"><i class="bi bi-check-circle-fill"></i></div>
        </div>
        <div class="kpi-card">
            <div>
                <div class="kpi-title">Completed Visits</div>
                <div class="kpi-value text-info">{{ number_format($stats['completed']) }}</div>
            </div>
            <div class="kpi-icon bg-info-subtle text-info"><i class="bi bi-patch-check-fill"></i></div>
        </div>
        <div class="kpi-card">
            <div>
                <div class="kpi-title">Cancelled</div>
                <div class="kpi-value text-danger">{{ number_format($stats['cancelled']) }}</div>
            </div>
            <div class="kpi-icon bg-danger-subtle text-danger"><i class="bi bi-x-circle-fill"></i></div>
        </div>
        <div class="kpi-card">
            <div>
                <div class="kpi-title">Paid Revenue</div>
                <div class="kpi-value text-dark">₹{{ number_format($stats['revenue'], 2) }}</div>
            </div>
            <div class="kpi-icon bg-warning-subtle text-warning"><i class="bi bi-currency-rupee"></i></div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.home-eye-test.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by Booking ID, Customer Name, Phone, Pincode..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="all">All Statuses</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="all">All Payments</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending (Pay on Visit)</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid Online</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" title="Filter by Appointment Date">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-filter"></i> Filter</button>
                    @if(request()->hasAny(['search', 'status', 'payment_status', 'date']))
                        <a href="{{ route('admin.home-eye-test.index') }}" class="btn btn-light btn-sm" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Appointments Table --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table het-table mb-0">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer Details</th>
                        <th>Location & Pincode</th>
                        <th>Appointment Slot</th>
                        <th>People / Fee</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $app)
                        <tr>
                            <td>
                                <span class="booking-id-tag">{{ $app->booking_id }}</span>
                                <div class="text-muted mt-1" style="font-size:11px;">{{ $app->created_at->format('d M Y, h:i A') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $app->name }}</div>
                                <div class="text-muted" style="font-size:12px;"><i class="bi bi-telephone me-1"></i>{{ $app->phone }}</div>
                                @if(!empty($app->email))
                                    <div class="text-muted" style="font-size:11px;"><i class="bi bi-envelope me-1"></i>{{ $app->email }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $app->city }} ({{ $app->pincode }})</div>
                                <div class="text-muted text-truncate" style="max-width:220px; font-size:11.5px;" title="{{ $app->address }}">
                                    {{ $app->address }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($app->appointment_date)->format('d M Y (D)') }}</div>
                                <div class="badge bg-light text-dark border mt-1" style="font-size:11px;"><i class="bi bi-clock me-1"></i>{{ $app->time_slot }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $app->people_count }} {{ Str::plural('Person', $app->people_count) }}</div>
                                <div class="text-success fw-bold" style="font-size:12.5px;">₹{{ number_format($app->fee, 0) }}</div>
                                <span class="badge {{ $app->payment_status === 'paid' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size:10px;">
                                    {{ $app->payment_status === 'paid' ? 'Paid Online' : 'Pay on Visit' }}
                                </span>
                            </td>
                            <td>
                                @if($app->status === 'confirmed')
                                    <span class="badge-status badge-confirmed"><i class="bi bi-check-circle-fill"></i> Confirmed</span>
                                @elseif($app->status === 'assigned')
                                    <span class="badge-status badge-assigned"><i class="bi bi-person-check-fill"></i> Assigned</span>
                                @elseif($app->status === 'completed')
                                    <span class="badge-status badge-completed"><i class="bi bi-patch-check-fill"></i> Completed</span>
                                @else
                                    <span class="badge-status badge-cancelled"><i class="bi bi-x-circle-fill"></i> Cancelled</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-outline-info btn-sm btn-view-het me-1" data-id="{{ $app->id }}" title="View Full Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-edit-het me-1" data-id="{{ $app->id }}" data-status="{{ $app->status }}" data-payment="{{ $app->payment_status }}" data-notes="{{ $app->notes }}" title="Update Status">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete-het" data-id="{{ $app->id }}" data-bid="{{ $app->booking_id }}" title="Delete Appointment">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-x" style="font-size: 40px;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No Home Eye Test appointments found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>

{{-- View Details Modal --}}
<div class="modal fade" id="hetViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-house-door-fill text-teal me-2"></i> Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="hetViewBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Status Modal --}}
<div class="modal fade" id="hetEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Update Appointment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="hetStatusForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Visit Status</label>
                        <select name="status" id="het-status-select" class="form-select">
                            <option value="confirmed">Confirmed (Scheduled)</option>
                            <option value="assigned">Assigned (Optometrist En Route)</option>
                            <option value="completed">Completed (Eye Test Done)</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Status</label>
                        <select name="payment_status" id="het-payment-select" class="form-select">
                            <option value="pending">Pending (Pay on Visit)</option>
                            <option value="paid">Paid (Collected)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Admin / Optometrist Notes</label>
                        <textarea name="notes" id="het-notes-input" class="form-control" rows="3" placeholder="Add optometrist name, test notes, or customer instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // View Details Modal
    $('.btn-view-het').on('click', function() {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('hetViewModal'));
        $('#hetViewBody').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        modal.show();

        $.ajax({
            url: "/admin/home-eye-test/" + id,
            type: "GET",
            success: function(res) {
                if (res.status === 'success') {
                    const a = res.appointment;
                    const html = `
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Booking Reference</label>
                                <div class="fw-bold fs-6 text-teal">${a.booking_id}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Booking Date</label>
                                <div class="fw-bold">${new Date(a.created_at).toLocaleString()}</div>
                            </div>
                            <hr class="my-2">
                            <div class="col-md-6">
                                <label class="text-muted small">Customer Name</label>
                                <div class="fw-bold">${a.name}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Mobile Number</label>
                                <div class="fw-bold">${a.phone}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Email Address</label>
                                <div>${a.email || 'N/A'}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">City & Pincode</label>
                                <div class="fw-bold">${a.city} - ${a.pincode}</div>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small">Complete Address</label>
                                <div class="p-2 bg-light rounded">${a.address} ${a.landmark ? '<br><small class="text-muted">Landmark: ' + a.landmark + '</small>' : ''}</div>
                            </div>
                            <hr class="my-2">
                            <div class="col-md-6">
                                <label class="text-muted small">Scheduled Appointment Date</label>
                                <div class="fw-bold text-primary">${a.appointment_date}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Time Slot</label>
                                <div class="badge bg-light text-dark border">${a.time_slot}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">People Count</label>
                                <div class="fw-bold">${a.people_count} Person(s)</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Total Fee & Payment Mode</label>
                                <div class="fw-bold text-success">₹${parseFloat(a.fee).toFixed(2)} (${a.payment_method === 'online' ? 'Online' : 'Pay on Visit'})</div>
                            </div>
                            ${a.notes ? `
                            <div class="col-12">
                                <label class="text-muted small">Notes / Instructions</label>
                                <div class="p-2 bg-warning-subtle text-dark rounded small">${a.notes}</div>
                            </div>` : ''}
                        </div>
                    `;
                    $('#hetViewBody').html(html);
                }
            },
            error: function() {
                $('#hetViewBody').html('<div class="alert alert-danger mb-0">Failed to load appointment details.</div>');
            }
        });
    });

    // Edit Status Modal Pre-fill
    $('.btn-edit-het').on('click', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const payment = $(this).data('payment');
        const notes = $(this).data('notes') || '';

        $('#hetStatusForm').attr('action', '/admin/home-eye-test/' + id + '/status');
        $('#het-status-select').val(status);
        $('#het-payment-select').val(payment);
        $('#het-notes-input').val(notes);

        const modal = new bootstrap.Modal(document.getElementById('hetEditModal'));
        modal.show();
    });

    // Submit Status Update via AJAX
    $('#hetStatusForm').on('submit', function(e) {
        e.preventDefault();
        const actionUrl = $(this).attr('action');

        $.ajax({
            url: actionUrl,
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                window.location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON ? xhr.responseJSON.message : 'Error updating status.');
            }
        });
    });

    // Delete Appointment
    $('.btn-delete-het').on('click', function() {
        const id = $(this).data('id');
        const bid = $(this).data('bid');

        if (confirm('Are you sure you want to delete appointment #' + bid + '?')) {
            $.ajax({
                url: "/admin/home-eye-test/" + id + "/delete",
                type: "POST",
                data: { _token: csrfToken },
                success: function(res) {
                    window.location.reload();
                },
                error: function() {
                    alert('Could not delete appointment.');
                }
            });
        }
    });
});
</script>
@endsection
