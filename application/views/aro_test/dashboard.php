
    <style>
        .card-aro { border-left: 4px solid #1e3a5c; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .progress { height: 8px; }
    </style>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">ARO Dashboard</h1>
            <p class="text-muted mb-0">Advocate's Remuneration Order Management & Analytics</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickCalcModal">
                <i class="bi bi-calculator me-1"></i> Quick Calculator
            </button>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">Total ARO Fees</h6>
                            <h3 class="mb-0">KES 4.2M</h3>
                            <span class="text-success small">
                                    <i class="bi bi-arrow-up-short"></i> 12.5% vs last month
                                </span>
                        </div>
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cash-coin text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">Active Matters</h6>
                            <h3 class="mb-0">47</h3>
                            <span class="text-success small">
                                    <i class="bi bi-arrow-up-short"></i> 8 new this week
                                </span>
                        </div>
                        <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-folder text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">ARO Compliance</h6>
                            <h3 class="mb-0">98.2%</h3>
                            <span class="text-success small">
                                    <i class="bi bi-check-circle"></i> 2 exceptions
                                </span>
                        </div>
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-shield-check text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">Time Saved</h6>
                            <h3 class="mb-0">156 hrs</h3>
                            <span class="text-info small">
                                    <i class="bi bi-clock"></i> This month
                                </span>
                        </div>
                        <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-lightning-charge text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Performance Metrics -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ARO Performance Metrics</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">Month</button>
                        <button class="btn btn-outline-secondary">Quarter</button>
                        <button class="btn btn-outline-secondary">Year</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3 text-success">
                                    <i class="bi bi-check-circle-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">ARO Compliance</h6>
                                    <p class="mb-0">98.2%</p>
                                </div>
                            </div>
                            <div class="progress mt-1" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 98.2%"></div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3 text-primary">
                                    <i class="bi bi-graph-up-arrow fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Avg. Uplift</h6>
                                    <p class="mb-0">14.3%</p>
                                </div>
                            </div>
                            <div class="progress mt-1" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 71.5%"></div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3 text-warning">
                                    <i class="bi bi-cash-coin fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Bill Recovery</h6>
                                    <p class="mb-0">87.5%</p>
                                </div>
                            </div>
                            <div class="progress mt-1" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 87.5%"></div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3 text-info">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Calc. Speed</h6>
                                    <p class="mb-0">2.3s avg</p>
                                </div>
                            </div>
                            <div class="progress mt-1" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Calculations -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent ARO Calculations</h5>
                    <a href="/matters/aro" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Matter ID</th>
                                <th>Client</th>
                                <th>Fee Type</th>
                                <th>Value</th>
                                <th>ARO Fees</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-015</a></td>
                                <td>Kenya Commercial Bank</td>
                                <td>Instruction Fee</td>
                                <td>KES 12.5M</td>
                                <td>KES 487,200</td>
                                <td><span class="badge bg-success">Approved</span></td>
                                <td>Today, 14:30</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-014</a></td>
                                <td>Sarah Johnson</td>
                                <td>Conveyancing</td>
                                <td>KES 8.5M</td>
                                <td>KES 127,500</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>Today, 11:15</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-013</a></td>
                                <td>ABC Manufacturing Ltd</td>
                                <td>Commercial Agreement</td>
                                <td>KES 25M</td>
                                <td>KES 85,000</td>
                                <td><span class="badge bg-success">Approved</span></td>
                                <td>Yesterday</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-012</a></td>
                                <td>James Mwangi</td>
                                <td>Civil Litigation</td>
                                <td>KES 5M</td>
                                <td>KES 325,380</td>
                                <td><span class="badge bg-info">In Progress</span></td>
                                <td>Dec 12, 2023</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-011</a></td>
                                <td>Premium Properties</td>
                                <td>Charge Registration</td>
                                <td>KES 15M</td>
                                <td>KES 75,000</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>Dec 10, 2023</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start" data-bs-toggle="modal" data-bs-target="#quickCalcModal">
                            <i class="bi bi-calculator me-2"></i>ARO Calculator
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-file-text me-2"></i>Generate Bill of Costs
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-search me-2"></i>ARO Database
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-plus-circle me-2"></i>New Matter
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-graph-up me-2"></i>Fee Reports
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pending Approvals</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">MAT2024-014</h6>
                                <small class="text-muted">Sarah Johnson - Conveyancing</small>
                            </div>
                            <span class="badge bg-warning">Review</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">MAT2024-016</h6>
                                <small class="text-muted">Tech Solutions Ltd - Agreement</small>
                            </div>
                            <span class="badge bg-warning">Review</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Bill of Costs #1042</h6>
                                <small class="text-muted">James Mwangi - Taxation</small>
                            </div>
                            <span class="badge bg-info">Pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Alerts -->
            <div class="card border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        System Alerts
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <h6 class="alert-heading">
                            <i class="bi bi-clock-history me-2"></i>ARO Rules Update
                        </h6>
                        <p class="mb-0 small">New ARO amendments effective Jan 1, 2024. Review changes in rules management.</p>
                    </div>
                    <div class="alert alert-info mb-0">
                        <h6 class="alert-heading">
                            <i class="bi bi-shield-check me-2"></i>Compliance Check
                        </h6>
                        <p class="mb-0 small">2 matters require ARO compliance review.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Calculator Modal -->
<div class="modal fade" id="quickCalcModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick ARO Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Matter Type</label>
                        <select class="form-select">
                            <option>Civil Suit - Money Claim</option>
                            <option>Conveyancing</option>
                            <option>Probate</option>
                            <option>Commercial Agreement</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subject Value (KES)</label>
                        <input type="text" class="form-control" placeholder="Enter amount">
                    </div>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <h6 class="mb-3">Estimated Fees</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Instruction Fee</small>
                            <div class="fw-bold">-</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Getting-Up Fee</small>
                            <div class="fw-bold">-</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Total</small>
                            <div class="fw-bold">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Calculate</button>
            </div>
        </div>
    </div>
</div>
