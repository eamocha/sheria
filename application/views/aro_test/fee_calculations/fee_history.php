<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/matters">Matters</a></li>
                    <li class="breadcrumb-item"><a href="/matters/MAT2024-015">MAT2024-015</a></li>
                    <li class="breadcrumb-item active">Fee History</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Fee Calculation History</h1>
            <p class="text-muted mb-0">All ARO fee calculations for MAT2024-015</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export History
            </button>
            <button class="btn btn-primary" onclick="location.href='/matters/MAT2024-015/fees/compute'">
                <i class="bi bi-plus-circle me-1"></i> New Calculation
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fee Type</label>
                    <select class="form-select">
                        <option value="">All Types</option>
                        <option>Instruction Fee</option>
                        <option>Getting-Up Fee</option>
                        <option>Attendance Fee</option>
                        <option>Drafting Fee</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option>Draft</option>
                        <option>Pending Approval</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" class="form-control" value="2023-12-01">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" class="form-control" value="2023-12-18">
                </div>
            </div>
        </div>
    </div>

    <!-- Calculations Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Calculation History (8 entries)</h5>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="showSuperseded">
                <label class="form-check-label" for="showSuperseded">Show Superseded</label>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th width="30">
                            <input class="form-check-input" type="checkbox">
                        </th>
                        <th>Calculation</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Calculated By</th>
                        <th>Date</th>
                        <th width="120">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="table-success">
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Instruction Fee - Final</strong>
                            <br><small class="text-muted">With 15% uplift</small>
                        </td>
                        <td>Instruction Fee</td>
                        <td>KES 486,910</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>John Advocate</td>
                        <td>Dec 18, 2023</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">View</button>
                                <button class="btn btn-outline-secondary">Copy</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Getting-Up Fee</strong>
                            <br><small class="text-muted">Based on instruction fee</small>
                        </td>
                        <td>Getting-Up Fee</td>
                        <td>KES 173,500</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>John Advocate</td>
                        <td>Dec 10, 2023</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">View</button>
                                <button class="btn btn-outline-secondary">Copy</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Instruction Fee - Revised</strong>
                            <br><small class="text-muted">Updated matter value</small>
                        </td>
                        <td>Instruction Fee</td>
                        <td>KES 365,000</td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>John Advocate</td>
                        <td>Dec 15, 2023</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">View</button>
                                <button class="btn btn-outline-secondary">Copy</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="table-secondary">
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Instruction Fee - Initial</strong>
                            <br><small class="text-muted">Superseded by revision</small>
                        </td>
                        <td>Instruction Fee</td>
                        <td>KES 320,000</td>
                        <td><span class="badge bg-secondary">Superseded</span></td>
                        <td>John Advocate</td>
                        <td>Dec 1, 2023</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">View</button>
                                <button class="btn btn-outline-secondary">Restore</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Attendance Fees</strong>
                            <br><small class="text-muted">Court appearances</small>
                        </td>
                        <td>Attendance Fee</td>
                        <td>KES 25,000</td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>Sarah Paralegal</td>
                        <td>Dec 5, 2023</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">View</button>
                                <button class="btn btn-outline-secondary">Copy</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <nav aria-label="History pagination">
                <ul class="pagination mb-0 justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>