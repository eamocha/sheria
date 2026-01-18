<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Fee Performance Analytics</h1>
            <p class="text-muted mb-0">Analyze fee calculation performance and revenue trends</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export Data
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dateRangeModal">
                <i class="bi bi-calendar-range me-1"></i> Dec 1-18, 2023
            </button>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1">KES 4.2M</h3>
                    <small class="text-muted">Total ARO Fees</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">247</h3>
                    <small class="text-muted">Matters Billed</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">KES 17.1K</h3>
                    <small class="text-muted">Avg. Fee per Matter</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-1">12.3%</h3>
                    <small class="text-muted">Avg. Uplift Used</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-danger mb-1">KES 528K</h3>
                    <small class="text-muted">Uplift Revenue</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-dark mb-1">98.2%</h3>
                    <small class="text-muted">Collection Rate</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Revenue Trends -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Revenue Trends</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">Monthly</button>
                        <button class="btn btn-outline-secondary">Quarterly</button>
                        <button class="btn btn-outline-secondary">Yearly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100%;">
                            <div class="text-center text-muted">
                                <i class="bi bi-graph-up fs-1 mb-2"></i>
                                <p>Revenue Trend Chart</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Matters -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top Performing Matters</h5>
                    <button class="btn btn-sm btn-outline-primary">View All</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Matter ID</th>
                                <th>Client</th>
                                <th>Matter Type</th>
                                <th>Value</th>
                                <th>ARO Fees</th>
                                <th>Uplift</th>
                                <th>Efficiency</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-015</a></td>
                                <td>Kenya Commercial Bank</td>
                                <td>Civil Litigation</td>
                                <td>KES 12.5M</td>
                                <td>KES 486,910</td>
                                <td><span class="badge bg-success">15%</span></td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 100px;">
                                        <div class="progress-bar bg-success" style="width: 95%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-012</a></td>
                                <td>ABC Manufacturing</td>
                                <td>Commercial</td>
                                <td>KES 25M</td>
                                <td>KES 312,500</td>
                                <td><span class="badge bg-warning">25%</span></td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 100px;">
                                        <div class="progress-bar bg-success" style="width: 88%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-008</a></td>
                                <td>Premium Properties</td>
                                <td>Conveyancing</td>
                                <td>KES 8.5M</td>
                                <td>KES 127,500</td>
                                <td><span class="badge bg-secondary">0%</span></td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 100px;">
                                        <div class="progress-bar bg-warning" style="width: 76%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-005</a></td>
                                <td>Sarah Johnson</td>
                                <td>Probate</td>
                                <td>KES 15M</td>
                                <td>KES 225,000</td>
                                <td><span class="badge bg-success">12%</span></td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 100px;">
                                        <div class="progress-bar bg-warning" style="width: 82%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="#" class="text-decoration-none">MAT2024-003</a></td>
                                <td>James Mwangi</td>
                                <td>Civil Litigation</td>
                                <td>KES 5M</td>
                                <td>KES 325,380</td>
                                <td><span class="badge bg-danger">-5%</span></td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 100px;">
                                        <div class="progress-bar bg-danger" style="width: 65%"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Fee Type Distribution -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Fee Type Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 200px;">
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100%;">
                            <div class="text-center text-muted">
                                <i class="bi bi-pie-chart fs-1 mb-2"></i>
                                <p>Fee Distribution Chart</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Instruction Fees</span>
                            <span class="fw-bold">KES 2.8M (67%)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Getting-Up Fees</span>
                            <span class="fw-bold">KES 850K (20%)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Conveyancing</span>
                            <span class="fw-bold">KES 320K (8%)</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>Other Fees</span>
                            <span class="fw-bold">KES 230K (5%)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uplift Performance -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Uplift Performance</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Commercial Litigation</small>
                            <small>18.5% avg</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 18.5%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Corporate</small>
                            <small>15.2% avg</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 15.2%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Property</small>
                            <small>8.7% avg</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 8.7%"></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Probate</small>
                            <small>5.3% avg</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: 5.3%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Benchmarks -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Performance Benchmarks</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small>Calculation Accuracy</small>
                            <small class="fw-bold text-success">98.5%</small>
                        </div>
                        <small class="text-muted">Industry avg: 95%</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small>Time per Calculation</small>
                            <small class="fw-bold text-success">2.3 min</small>
                        </div>
                        <small class="text-muted">Industry avg: 8 min</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small>Uplift Utilization</small>
                            <small class="fw-bold text-warning">67%</small>
                        </div>
                        <small class="text-muted">Target: 75%</small>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <small>Client Satisfaction</small>
                            <small class="fw-bold text-success">4.8/5</small>
                        </div>
                        <small class="text-muted">Based on fee transparency</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>