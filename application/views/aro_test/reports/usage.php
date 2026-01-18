<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Rule Usage Reports</h1>
            <p class="text-muted mb-0">Track ARO rule utilization and performance metrics</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export Report
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel me-1"></i> Filters
            </button>
        </div>
    </div>

    <!-- Usage Summary -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1">24</h3>
                    <small class="text-muted">Active Rules</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">18</h3>
                    <small class="text-muted">Frequently Used</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-1">4</h3>
                    <small class="text-muted">Rarely Used</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-danger mb-1">2</h3>
                    <small class="text-muted">Never Used</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Rule Usage Table -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rule Usage Analysis</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">By Usage</button>
                        <button class="btn btn-outline-secondary">By Revenue</button>
                        <button class="btn btn-outline-secondary">By Efficiency</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Rule Name</th>
                                <th>Schedule</th>
                                <th>Usage Count</th>
                                <th>Total Revenue</th>
                                <th>Avg. Fee</th>
                                <th>Usage Trend</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <strong>Instruction Fee - Civil Suit</strong>
                                    <br><small class="text-muted">Money claims and litigation</small>
                                </td>
                                <td>Schedule 1</td>
                                <td>147</td>
                                <td>KES 2.1M</td>
                                <td>KES 14,286</td>
                                <td>
                                        <span class="text-success">
                                            <i class="bi bi-arrow-up-short"></i> 12%
                                        </span>
                                </td>
                                <td><span class="badge bg-success">High Usage</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Conveyancing - Sale/Purchase</strong>
                                    <br><small class="text-muted">Property transfers</small>
                                </td>
                                <td>Schedule 2</td>
                                <td>89</td>
                                <td>KES 1.3M</td>
                                <td>KES 14,607</td>
                                <td>
                                        <span class="text-success">
                                            <i class="bi bi-arrow-up-short"></i> 8%
                                        </span>
                                </td>
                                <td><span class="badge bg-success">High Usage</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Getting-Up Fee - Civil</strong>
                                    <br><small class="text-muted">Case preparation</small>
                                </td>
                                <td>Schedule 1</td>
                                <td>76</td>
                                <td>KES 850K</td>
                                <td>KES 11,184</td>
                                <td>
                                        <span class="text-warning">
                                            <i class="bi bi-dash"></i> 2%
                                        </span>
                                </td>
                                <td><span class="badge bg-success">Medium Usage</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Probate Administration</strong>
                                    <br><small class="text-muted">Estate administration</small>
                                </td>
                                <td>Schedule 3</td>
                                <td>45</td>
                                <td>KES 675K</td>
                                <td>KES 15,000</td>
                                <td>
                                        <span class="text-success">
                                            <i class="bi bi-arrow-up-short"></i> 15%
                                        </span>
                                </td>
                                <td><span class="badge bg-warning">Medium Usage</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Commercial Agreement Drafting</strong>
                                    <br><small class="text-muted">Contract preparation</small>
                                </td>
                                <td>Schedule 4</td>
                                <td>12</td>
                                <td>KES 180K</td>
                                <td>KES 15,000</td>
                                <td>
                                        <span class="text-success">
                                            <i class="bi bi-arrow-up-short"></i> 25%
                                        </span>
                                </td>
                                <td><span class="badge bg-warning">Low Usage</span></td>
                            </tr>
                            <tr class="table-danger">
                                <td>
                                    <strong>Arbitration Fees</strong>
                                    <br><small class="text-muted">Alternative dispute resolution</small>
                                </td>
                                <td>Schedule 1</td>
                                <td>0</td>
                                <td>KES 0</td>
                                <td>-</td>
                                <td>
                                    <span class="text-muted">No usage</span>
                                </td>
                                <td><span class="badge bg-danger">Never Used</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Usage Trends -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Usage Trends</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100%;">
                            <div class="text-center text-muted">
                                <i class="bi bi-bar-chart fs-1 mb-2"></i>
                                <p>Monthly Usage Trends Chart</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Top Rules by Revenue -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Top Rules by Revenue</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Instruction Fee - Civil</h6>
                                <small class="text-muted">Schedule 1</small>
                            </div>
                            <span class="badge bg-primary">KES 2.1M</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Conveyancing</h6>
                                <small class="text-muted">Schedule 2</small>
                            </div>
                            <span class="badge bg-primary">KES 1.3M</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Getting-Up Fee</h6>
                                <small class="text-muted">Schedule 1</small>
                            </div>
                            <span class="badge bg-primary">KES 850K</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Probate Admin</h6>
                                <small class="text-muted">Schedule 3</small>
                            </div>
                            <span class="badge bg-primary">KES 675K</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Commercial Drafting</h6>
                                <small class="text-muted">Schedule 4</small>
                            </div>
                            <span class="badge bg-primary">KES 180K</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rule Performance Insights -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Performance Insights</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h6 class="alert-heading">
                            <i class="bi bi-lightbulb me-2"></i>High Performers
                        </h6>
                        <p class="mb-0 small">Instruction Fee rules generate 67% of total ARO revenue</p>
                    </div>
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="bi bi-exclamation-triangle me-2"></i>Optimization Opportunity
                        </h6>
                        <p class="mb-0 small">2 rules have never been used - consider review or removal</p>
                    </div>
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-graph-up-arrow me-2"></i>Growth Area
                        </h6>
                        <p class="mb-0 small">Commercial rules show 25% growth - focus training here</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Rule Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-plus-circle me-2"></i>Add New Rule
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-pencil me-2"></i>Edit Rule Settings
                        </button>
                        <button class="btn btn-outline-warning text-start">
                            <i class="bi bi-archive me-2"></i>Archive Unused Rules
                        </button>
                        <button class="btn btn-outline-danger text-start">
                            <i class="bi bi-trash me-2"></i>Delete Inactive Rules
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>