<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Export Manager</h1>
            <p class="text-muted mb-0">Export ARO data, reports, and analytics in various formats</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportTemplatesModal">
                <i class="bi bi-collection me-1"></i> Templates
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleExportModal">
                <i class="bi bi-clock me-1"></i> Schedule Export
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Export Configuration -->
        <div class="col-lg-8">
            <!-- Export Builder -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Export Configuration</h5>
                </div>
                <div class="card-body">
                    <!-- Data Selection -->
                    <div class="mb-4">
                        <h6 class="mb-3">1. Select Data to Export</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="exportRules" checked>
                                    <label class="form-check-label" for="exportRules">
                                        ARO Rules & Schedules
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="exportCalculations" checked>
                                    <label class="form-check-label" for="exportCalculations">
                                        Fee Calculations
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="exportMatters">
                                    <label class="form-check-label" for="exportMatters">
                                        Matter Data
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="exportReports">
                                    <label class="form-check-label" for="exportReports">
                                        Compliance Reports
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="exportAnalytics">
                                    <label class="form-check-label" for="exportAnalytics">
                                        Performance Analytics
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="exportAudit">
                                    <label class="form-check-label" for="exportAudit">
                                        Audit Logs
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="mb-4">
                        <h6 class="mb-3">2. Apply Filters</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Date Range</label>
                                <select class="form-select" id="dateRange">
                                    <option value="all">All Dates</option>
                                    <option value="today">Today</option>
                                    <option value="week" selected>This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="quarter">This Quarter</option>
                                    <option value="year">This Year</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Schedule</label>
                                <select class="form-select" id="scheduleFilter">
                                    <option value="all">All Schedules</option>
                                    <option value="1">Schedule 1 - Civil</option>
                                    <option value="2">Schedule 2 - Conveyancing</option>
                                    <option value="3">Schedule 3 - Probate</option>
                                    <option value="4">Schedule 4 - Commercial</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="all">All Status</option>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>
                                    <option value="draft">Draft Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-2 d-none" id="customDateRange">
                            <div class="col-md-6">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control" value="2023-12-01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control" value="2023-12-18">
                            </div>
                        </div>
                    </div>

                    <!-- Format & Options -->
                    <div class="mb-4">
                        <h6 class="mb-3">3. Format & Options</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Export Format</label>
                                <select class="form-select" id="exportFormat">
                                    <option value="csv">CSV (Comma Separated)</option>
                                    <option value="excel" selected>Excel Workbook</option>
                                    <option value="pdf">PDF Report</option>
                                    <option value="json">JSON Data</option>
                                    <option value="xml">XML Format</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">File Name</label>
                                <input type="text" class="form-control" value="ARO_Export_20231218" placeholder="Enter file name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Compression</label>
                                <select class="form-select" id="compression">
                                    <option value="none">No Compression</option>
                                    <option value="zip" selected>ZIP Archive</option>
                                    <option value="gzip">GZIP Compressed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Options -->
                    <div>
                        <h6 class="mb-3">4. Advanced Options</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeMetadata" checked>
                                    <label class="form-check-label" for="includeMetadata">
                                        Include metadata and timestamps
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="anonymizeData">
                                    <label class="form-check-label" for="anonymizeData">
                                        Anonymize client data
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="splitLargeFiles">
                                    <label class="form-check-label" for="splitLargeFiles">
                                        Split large files (1000+ records)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="emailWhenReady">
                                    <label class="form-check-label" for="emailWhenReady">
                                        Email when export is ready
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Export Preview</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="generatePreview()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Preview
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Estimated Export:</strong>
                        <span id="exportStats">24 rules, 147 calculations, 89 matters (Approx. 2.4 MB)</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>Data Type</th>
                                <th>Records</th>
                                <th>Size</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>ARO Rules</td>
                                <td>24</td>
                                <td>45 KB</td>
                                <td><span class="badge bg-success">Ready</span></td>
                            </tr>
                            <tr>
                                <td>Fee Calculations</td>
                                <td>147</td>
                                <td>1.2 MB</td>
                                <td><span class="badge bg-success">Ready</span></td>
                            </tr>
                            <tr>
                                <td>Matter Data</td>
                                <td>89</td>
                                <td>856 KB</td>
                                <td><span class="badge bg-warning">Filtered</span></td>
                            </tr>
                            <tr>
                                <td>Compliance Reports</td>
                                <td>12</td>
                                <td>320 KB</td>
                                <td><span class="badge bg-success">Ready</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary" onclick="startExport()">
                        <i class="bi bi-download me-1"></i> Start Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Exports & History -->
        <div class="col-lg-4">
            <!-- Quick Export Templates -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Export Templates</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Compliance Report</h6>
                                <small class="text-muted">Monthly compliance summary</small>
                            </div>
                            <i class="bi bi-download text-primary"></i>
                        </button>
                        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Fee Performance</h6>
                                <small class="text-muted">Quarterly performance analytics</small>
                            </div>
                            <i class="bi bi-download text-primary"></i>
                        </button>
                        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Rule Usage</h6>
                                <small class="text-muted">Rule utilization statistics</small>
                            </div>
                            <i class="bi bi-download text-primary"></i>
                        </button>
                        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Audit Trail</h6>
                                <small class="text-muted">Complete system audit log</small>
                            </div>
                            <i class="bi bi-download text-primary"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Exports -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Exports</h5>
                    <button class="btn btn-sm btn-outline-primary">View All</button>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-success rounded-circle me-3" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <small class="text-muted">Today, 11:30</small>
                                    <p class="mb-0 small">Compliance Report (PDF) • 2.1 MB</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-primary rounded-circle me-3" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <small class="text-muted">Yesterday, 16:45</small>
                                    <p class="mb-0 small">Rule Data (Excel) • 1.8 MB</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-info rounded-circle me-3" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <small class="text-muted">Dec 15, 2023</small>
                                    <p class="mb-0 small">Audit Log (CSV) • 4.2 MB</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-marker bg-warning rounded-circle me-3" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <small class="text-muted">Dec 12, 2023</small>
                                    <p class="mb-0 small">Performance Data (JSON) • 890 KB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Export Statistics</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Total Exports:</dt>
                        <dd class="col-sm-6">147</dd>

                        <dt class="col-sm-6">This Month:</dt>
                        <dd class="col-sm-6">24</dd>

                        <dt class="col-sm-6">Most Used Format:</dt>
                        <dd class="col-sm-6">Excel (68%)</dd>

                        <dt class="col-sm-6">Average Size:</dt>
                        <dd class="col-sm-6">2.1 MB</dd>

                        <dt class="col-sm-6">Last Export:</dt>
                        <dd class="col-sm-6">Today, 11:30</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Export Modal -->
<div class="modal fade" id="scheduleExportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Recurring Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Schedule Name</label>
                        <input type="text" class="form-control" placeholder="e.g., Weekly Compliance Report">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frequency</label>
                        <select class="form-select">
                            <option value="daily">Daily</option>
                            <option value="weekly" selected>Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" value="2024-01-01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Export Format</label>
                        <select class="form-select">
                            <option value="excel">Excel Workbook</option>
                            <option value="pdf">PDF Report</option>
                            <option value="csv">CSV File</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="emailRecipients" checked>
                        <label class="form-check-label" for="emailRecipients">
                            Email export to recipients
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Schedule Export</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('dateRange').addEventListener('change', function() {
        const customRange = document.getElementById('customDateRange');
        if (this.value === 'custom') {
            customRange.classList.remove('d-none');
        } else {
            customRange.classList.add('d-none');
        }
    });

    function generatePreview() {
        // Simulate preview generation
        document.getElementById('exportStats').textContent =
            '24 rules, 147 calculations, 89 matters (Approx. 2.4 MB)';
    }

    function startExport() {
        // Simulate export process
        alert('Export process started! You will be notified when it is ready for download.');
    }
</script>