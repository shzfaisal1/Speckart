@extends('website.layout.master')
@section('content')

<style>
    .my-prescription-section {
        padding: 40px 0 60px;
        background: #f4faf9;
        min-height: 80vh;
    }
    .my-prescription-header h3 {
        color: #07484A;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .my-prescription-header p {
        color: #7d8a8b;
        margin-bottom: 0;
    }
    .my-prescription-card {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #dcebea;
        box-shadow: 0 8px 25px rgba(7, 72, 74, .06);
        transition: transform .3s ease, box-shadow .3s ease;
        height: 100%;
        position: relative;
    }
    .my-prescription-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 35px rgba(7, 72, 74, .12);
    }
    .rx-badge {
        background: rgba(0, 185, 185, .12);
        color: #00B9B9;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
    }
    .rx-table {
        margin-top: 15px;
        margin-bottom: 15px;
    }
    .rx-table th {
        background: #f8fbfb;
        color: #07484A;
        font-weight: 600;
        font-size: 13px;
        text-align: center;
    }
    .rx-table td {
        text-align: center;
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }
    .btn-theme {
        background: #07484A;
        border: 1px solid #07484A;
        color: #FAF59E;
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 20px;
    }
    .btn-theme:hover {
        background: #042e2f;
        color: #FAF59E;
    }
    .btn-outline-theme {
        background: #fff;
        border: 1.5px solid #00B9B9;
        color: #00B9B9;
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 20px;
    }
    .btn-outline-theme:hover {
        background: #00B9B9;
        color: #fff;
    }
    .empty-rx {
        background: #fff;
        border-radius: 20px;
        padding: 60px 20px;
        text-align: center;
        border: 1px solid #dcebea;
        box-shadow: 0 8px 25px rgba(7, 72, 74, .06);
    }
    .empty-rx i {
        font-size: 42px;
        color: #00B9B9;
        background: rgba(0, 185, 185, .1);
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
</style>

<section class="my-prescription-section">
    <div class="container">

        <!-- Top Header & Action Buttons -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 my-prescription-header">
            <div>
                <h3>My Prescriptions</h3>
                <p>Saved vision parameters and uploaded doctor slips</p>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-theme" data-bs-toggle="modal" data-bs-target="#uploadRxModal">
                    <i class="fas fa-file-upload me-1"></i> Upload Prescription Slip
                </button>
                <button type="button" class="btn btn-theme" data-bs-toggle="modal" data-bs-target="#manualRxModal">
                    <i class="fas fa-plus-circle me-1"></i> Enter Power Manually
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 p-3 mb-4" style="background:#e6fdf5; color:#0f5132;">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 p-3 mb-4" style="background:#fdf2f2; color:#842029;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Prescriptions Grid -->
        <div class="row g-4">

            @forelse($prescriptions as $rx)
                <div class="col-lg-6">
                    <div class="my-prescription-card">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1" style="color: #07484A;">{{ $rx->prescription_name }}</h5>
                                <span class="rx-badge">{{ $rx->power_type ?? 'Single Vision' }}</span>
                            </div>

                            <form action="{{ route('my-prescriptions.delete', $rx->id) }}" method="POST" onsubmit="return confirm('Delete this prescription?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger border-0 bg-transparent p-1" title="Delete Prescription">
                                    <i class="fas fa-trash-alt fs-5"></i>
                                </button>
                            </form>
                        </div>

                        @if(!empty($rx->rx_file))
                            <!-- Uploaded File View -->
                            <div class="p-3 rounded-3 mb-3 text-center" style="background:#f8fbfb; border: 1px dashed #00B9B9;">
                                @php
                                    $isPdf = strtolower(pathinfo($rx->rx_file, PATHINFO_EXTENSION)) === 'pdf';
                                @endphp

                                @if($isPdf)
                                    <div class="py-3">
                                        <i class="fas fa-file-pdf text-danger display-4 mb-2 d-block"></i>
                                        <a href="{{ asset($rx->rx_file) }}" target="_blank" class="btn btn-sm btn-outline-theme">
                                            <i class="fas fa-external-link-alt me-1"></i> View Doctor Slip (PDF)
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ asset($rx->rx_file) }}" target="_blank">
                                        <img src="{{ asset($rx->rx_file) }}" alt="Prescription Slip" class="img-fluid rounded-3" style="max-height: 180px; object-fit: contain;">
                                    </a>
                                    <small class="d-block text-muted mt-2">Click image to enlarge</small>
                                @endif
                            </div>
                        @else
                            <!-- Manual Power Parameter Table -->
                            <table class="table table-bordered rx-table mb-3">
                                <thead>
                                    <tr>
                                        <th>EYE</th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>ADD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold" style="color:#07484A;">RIGHT (OD)</td>
                                        <td>{{ $rx->r_sph ?: '-' }}</td>
                                        <td>{{ $rx->r_cyl ?: '-' }}</td>
                                        <td>{{ $rx->r_axis ?: '-' }}</td>
                                        <td>{{ $rx->r_add ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold" style="color:#07484A;">LEFT (OS)</td>
                                        <td>{{ $rx->l_sph ?: '-' }}</td>
                                        <td>{{ $rx->l_cyl ?: '-' }}</td>
                                        <td>{{ $rx->l_axis ?: '-' }}</td>
                                        <td>{{ $rx->l_add ?: '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif

                        <div class="d-flex justify-content-between align-items-center text-muted small mt-2 pt-2 border-top">
                            <span><i class="far fa-calendar-alt me-1"></i> Added on {{ \Carbon\Carbon::parse($rx->created_at)->format('d M Y') }}</span>
                            @if(!empty($rx->pd))
                                <span class="fw-bold text-dark"><i class="fas fa-ruler-horizontal me-1 text-info"></i> PD: {{ $rx->pd }} mm</span>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                @if($eyeTests->isEmpty())
                    <div class="col-12">
                        <div class="empty-rx">
                            <i class="fas fa-file-medical"></i>
                            <h4 class="fw-bold text-dark mb-2">No Saved Prescriptions</h4>
                            <p class="text-muted mb-4">Upload your doctor's slip or manually enter your eye power details for faster ordering.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" class="btn btn-outline-theme" data-bs-toggle="modal" data-bs-target="#uploadRxModal">
                                    <i class="fas fa-file-upload me-1"></i> Upload Prescription Slip
                                </button>
                                <button type="button" class="btn btn-theme" data-bs-toggle="modal" data-bs-target="#manualRxModal">
                                    <i class="fas fa-plus-circle me-1"></i> Enter Power Manually
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endforelse

            <!-- Display Clinic Eye Test Records if available -->
            @foreach($eyeTests as $test)
                <div class="col-lg-6">
                    <div class="my-prescription-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1" style="color: #07484A;">Optometrist Test Result</h5>
                                <span class="rx-badge" style="background: rgba(7, 72, 74, .1); color: #07484A;">In-Clinic Eye Checkup</span>
                            </div>
                        </div>

                        <table class="table table-bordered rx-table mb-3">
                            <thead>
                                <tr>
                                    <th>EYE</th>
                                    <th>SPH</th>
                                    <th>CYL</th>
                                    <th>AXIS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold" style="color:#07484A;">RIGHT (OD)</td>
                                    <td>{{ $test->re_sph ?: '-' }}</td>
                                    <td>{{ $test->re_cyl ?: '-' }}</td>
                                    <td>{{ $test->re_axis ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold" style="color:#07484A;">LEFT (OS)</td>
                                    <td>{{ $test->le_sph ?: '-' }}</td>
                                    <td>{{ $test->le_cyl ?: '-' }}</td>
                                    <td>{{ $test->le_axis ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-muted small mt-2 pt-2 border-top d-flex justify-content-between">
                            <span><i class="far fa-calendar-alt me-1"></i> Test Date: {{ \Carbon\Carbon::parse($test->created_at)->format('d M Y') }}</span>
                            <span>Patient: {{ $test->cust_name }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    </div>
</section>

<!-- Modal 1: Upload Doctor Slip -->
<div class="modal fade" id="uploadRxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-file-upload text-info me-2"></i> Upload Prescription Slip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('my-prescriptions.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Prescription Title / Label</label>
                        <input type="text" name="prescription_name" class="form-control rounded-3" placeholder="e.g., Doctor Prescription Slip 2026">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Power Type</label>
                        <select name="power_type" class="form-select rounded-3">
                            <option value="Single Vision">Single Vision (Distance / Reading)</option>
                            <option value="Bifocal">Bifocal</option>
                            <option value="Progressive">Progressive</option>
                            <option value="Contact Lens">Contact Lens</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Prescription File (Photo / PDF) *</label>
                        <input type="file" name="rx_file" class="form-control rounded-3" accept=".jpg,.jpeg,.png,.pdf,.webp" required>
                        <small class="text-muted">Supported formats: JPG, PNG, WEBP, PDF (Max 10MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Doctor / Clinic Notes (Optional)</label>
                        <textarea name="remarks" class="form-control rounded-3" rows="2" placeholder="e.g., Prescribed by Dr. Smith"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme rounded-3 px-4">Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Enter Power Manually -->
<div class="modal fade" id="manualRxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-glasses text-info me-2"></i> Enter Eye Power Manually</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('my-prescriptions.manual') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prescription Title</label>
                            <input type="text" name="prescription_name" class="form-control rounded-3" placeholder="e.g., My Distance Glasses Power">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Power Type *</label>
                            <select name="power_type" class="form-select rounded-3" required>
                                <option value="Single Vision">Single Vision</option>
                                <option value="Bifocal">Bifocal</option>
                                <option value="Progressive">Progressive</option>
                                <option value="Contact Lens">Contact Lens</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3" style="color:#07484A;"><i class="fas fa-eye me-1"></i> Eye Power Parameters</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th>EYE</th>
                                    <th>SPH (Spherical)</th>
                                    <th>CYL (Cylinder)</th>
                                    <th>AXIS (Degree)</th>
                                    <th>ADD (Addition)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-center" style="color:#07484A;">RIGHT (OD)</td>
                                    <td><input type="text" name="r_sph" class="form-control form-control-sm text-center" placeholder="-0.00"></td>
                                    <td><input type="text" name="r_cyl" class="form-control form-control-sm text-center" placeholder="-0.00"></td>
                                    <td><input type="text" name="r_axis" class="form-control form-control-sm text-center" placeholder="0° - 180°"></td>
                                    <td><input type="text" name="r_add" class="form-control form-control-sm text-center" placeholder="+0.00"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-center" style="color:#07484A;">LEFT (OS)</td>
                                    <td><input type="text" name="l_sph" class="form-control form-control-sm text-center" placeholder="-0.00"></td>
                                    <td><input type="text" name="l_cyl" class="form-control form-control-sm text-center" placeholder="-0.00"></td>
                                    <td><input type="text" name="l_axis" class="form-control form-control-sm text-center" placeholder="0° - 180°"></td>
                                    <td><input type="text" name="l_add" class="form-control form-control-sm text-center" placeholder="+0.00"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pupillary Distance (PD in mm)</label>
                            <input type="text" name="pd" class="form-control rounded-3" placeholder="e.g., 63 mm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Remarks / Notes</label>
                            <input type="text" name="remarks" class="form-control rounded-3" placeholder="e.g., Computer glasses">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme rounded-3 px-4">Save Prescription</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection