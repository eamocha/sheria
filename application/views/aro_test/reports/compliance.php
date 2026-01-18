<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">ARO Compliance Reports</h1>
            <p class="text-muted mb-0">Monitor ARO rule adherence and identify exceptions</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export Report
            </button>
            <button class="btn btn-primary">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">Overall Compliance</h6>
                            <h3 class="mb-0 text-success">98.2%</h3>
                            <span class="text-success small">
                                <i class="bi bi-arrow-up-short"></i> 2.1% vs last month
                            </span>
                        </div>
                        <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-shield-check text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">Exceptions</h6>
                            <h3 class="mb-0 text-warning">14</h3>
                            <span class="text-warning small">
                                <i class="bi bi-arrow-down-short"></i> 3 from last week
                            </span>
                        </div>
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">Matters Reviewed</h6>
                            <h3 class="mb-0 text-primary">247</h3>
                            <span class="text-primary small">
                                <i class="bi bi-arrow-up-short"></i> 47 this month
                            </span>
                        </div>
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-folder-check text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">Avg. Uplift Used</h6>
                            <h3 class="mb-0 text-info">12.3%</h3>
                            <span class="text-info small">
                                <i class="bi bi-dash"></i> 0.2% vs target
                            </span>
                        </div>
                        <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-graph-up text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Compliance Trends -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Compliance Trends</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">Month</button>
                        <button class="btn btn-outline-secondary">Quarter</button>
                        <button class="btn btn-outline-secondary">Year</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <!-- Chart would go here -->
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100%;">
                            <div class="text-center text-muted">
                                <i class="bi bi-bar-chart fs-1 mb-2"></i>
                                <p>Compliance Trend Chart</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exceptions Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Compliance Exceptions</h5>
                    <button class="btn btn-sm btn-outline-primary">View All Exceptions</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Matter ID</th>
                                <th>Client</th>
                                <th>Exception Type</th>
                                <th>Fee Type</th>
                                <th>Deviation</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-018</a></td>
                                <td>Tech Solutions Ltd</td>
                                <td><span class="badge bg-danger">Over-charge</span></td>
                                <td>Instruction Fee</td>
                                <td>+25% above ARO</td>
                                <td><span class="badge bg-warning">Under Review</span></td>
                                <td>Dec 17, 2023</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-015</a></td>
                                <td>Kenya Commercial Bank</td>
                                <td><span class="badge bg-warning">Uplift Exception</span></td>
                                <td>Instruction Fee</td>
                                <td>15% uplift applied</td>
                                <td><span class="badge bg-success">Approved</span></td>
                                <td>Dec 15, 2023</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-009</a></td>
                                <td>Premium Properties</td>
                                <td><span class="badge bg-info">Under-charge</span></td>
                                <td>Conveyancing</td>
                                <td>-10% below ARO</td>
                                <td><span class="badge bg-success">Resolved</span></td>
                                <td>Dec 12, 2023</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-005</a></td>
                                <td>Sarah Johnson</td>
                                <td><span class="badge bg-warning">Uplift Exception</span></td>
                                <td>Getting-Up Fee</td>
                                <td>20% uplift applied</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>Dec 10, 2023</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-002</a></td>
                                <td>James Mwangi</td>
                                <td><span class="badge bg-danger">Rule Violation</span></td>
                                <td>Attendance Fee</td>
                                <td>Wrong schedule used</td>
                                <td><span class="badge bg-success">Corrected</span></td>
                                <td>Dec 5, 2023</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Compliance by Department -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Compliance by Department</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Commercial Litigation</small>
                            <small>99.5%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 99.5%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Property & Conveyancing</small>
                            <small>98.8%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 98.8%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Corporate & Commercial</small>
                            <small>97.2%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 97.2%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Probate & Estate</small>
                            <small>96.5%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 96.5%"></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Dispute Resolution</small>
                            <small>95.8%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: 95.8%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exception Types -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Exception Types</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 200px;">
                        <!-- Pie chart would go here -->
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100%;">
                            <div class="text-center text-muted">
                                <i class="bi bi-pie-chart fs-1 mb-2"></i>
                                <p>Exception Types Chart</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small">
                            <span>Uplift Exceptions</span>
                            <span class="fw-bold">8 (57%)</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>Over-charge</span>
                            <span class="fw-bold">3 (21%)</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>Under-charge</span>
                            <span class="fw-bold">2 (14%)</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>Rule Violations</span>
                            <span class="fw-bold">1 (7%)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Report Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF Report
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-file-spreadsheet me-2"></i>Export to Excel
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-envelope me-2"></i>Email to Partners
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-clock me-2"></i>Schedule Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>