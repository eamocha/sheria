<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Bulk Import</h1>
            <p class="text-muted mb-0">Mass import of ARO rules, matter data, and calculations</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Download Templates
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importHistoryModal">
                <i class="bi bi-clock-history me-1"></i> Import History
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Import Steps -->
        <div class="col-lg-8">
            <!-- Import Wizard -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Import Wizard</h5>
                </div>
                <div class="card-body">
                    <!-- Step 1: Select Import Type -->
                    <div class="mb-4" id="step1">
                        <h6 class="mb-3">1. Select Import Type</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card import-type-card border-2">
                                    <div class="card-body text-center">
                                        <i class="bi bi-journal-text fs-1 text-primary mb-3"></i>
                                        <h6>ARO Rules</h6>
                                        <p class="small text-muted mb-2">Import new rules and schedules</p>
                                        <button class="btn btn-outline-primary btn-sm" onclick="selectImportType('rules')">
                                            Select
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card import-type-card">
                                    <div class="card-body text-center">
                                        <i class="bi bi-calculator fs-1 text-success mb-3"></i>
                                        <h6>Fee Calculations</h6>
                                        <p class="small text-muted mb-2">Import historical calculations</p>
                                        <button class="btn btn-outline-primary btn-sm" onclick="selectImportType('calculations')">
                                            Select
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card import-type-card">
                                    <div class="card-body text-center">
                                        <i class="bi bi-people fs-1 text-warning mb-3"></i>
                                        <h6>Matter Data</h6>
                                        <p class="small text-muted mb-2">Import matters and clients</p>
                                        <button class="btn btn-outline-primary btn-sm" onclick="selectImportType('matters')">
                                            Select
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Upload File (Initially Hidden) -->
                    <div class="mb-4 d-none" id="step2">
                        <h6 class="mb-3">2. Upload File</h6>
                        <div class="border-2 border-dashed rounded p-5 text-center bg-light">
                            <i class="bi bi-cloud-upload fs-1 text-muted mb-3"></i>
                            <h5>Drop your file here or click to browse</h5>
                            <p class="text-muted mb-3">Supports .csv, .xlsx, .xls files (Max 10MB)</p>
                            <input type="file" class="d-none" id="fileInput" accept=".csv,.xlsx,.xls">
                            <button class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                                <i class="bi bi-folder2-open me-1"></i> Browse Files
                            </button>
                            <div class="mt-3" id="fileInfo"></div>
                        </div>
                    </div>

                    <!-- Step 3: Data Mapping (Initially Hidden) -->
                    <div class="mb-4 d-none" id="step3">
                        <h6 class="mb-3">3. Map Data Fields</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>CSV Column</th>
                                    <th>System Field</th>
                                    <th>Sample Data</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody id="mappingTable">
                                <!-- Dynamic mapping rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="autoMapFields" checked>
                            <label class="form-check-label" for="autoMapFields">
                                Auto-map similar field names
                            </label>
                        </div>
                    </div>

                    <!-- Step 4: Validation & Preview (Initially Hidden) -->
                    <div class="d-none" id="step4">
                        <h6 class="mb-3">4. Validation & Preview</h6>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Validation Results:</strong>
                            <span id="validationResults">15 records ready, 2 warnings, 0 errors</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Rule Name</th>
                                    <th>Schedule</th>
                                    <th>Status</th>
                                    <th>Issues</th>
                                </tr>
                                </thead>
                                <tbody id="previewTable">
                                <!-- Preview data will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button class="btn btn-outline-secondary" id="prevBtn" disabled>
                        <i class="bi bi-arrow-left me-1"></i> Previous
                    </button>
                    <button class="btn btn-primary" id="nextBtn" onclick="nextStep()">
                        Next <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- Import Templates -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Import Templates</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card template-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <i class="bi bi-file-earmark-spreadsheet text-primary fs-4"></i>
                                        <span class="badge bg-success">Latest</span>
                                    </div>
                                    <h6>ARO Rules Template</h6>
                                    <p class="small text-muted mb-3">Import new rules and schedules with proper formatting</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">v2.1 • Updated Dec 2023</small>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download me-1"></i> Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card template-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <i class="bi bi-calculator text-success fs-4"></i>
                                        <span class="badge bg-warning">Updated</span>
                                    </div>
                                    <h6>Fee Calculations Template</h6>
                                    <p class="small text-muted mb-3">Import historical fee calculations and matter data</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">v1.4 • Updated Nov 2023</small>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download me-1"></i> Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card template-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <i class="bi bi-people text-warning fs-4"></i>
                                        <span class="badge bg-secondary">Legacy</span>
                                    </div>
                                    <h6>Matter Data Template</h6>
                                    <p class="small text-muted mb-3">Import matters, clients, and related information</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">v1.2 • Updated Oct 2023</small>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download me-1"></i> Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Instructions & Status -->
        <div class="col-lg-4">
            <!-- Import Instructions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Import Instructions</h5>
                </div>
                <div class="card-body">
                    <h6>Supported Formats:</h6>
                    <ul class="small mb-3">
                        <li><strong>CSV:</strong> Comma-separated values with header row</li>
                        <li><strong>Excel:</strong> .xlsx or .xls format with single sheet</li>
                        <li><strong>Maximum Size:</strong> 10MB per file</li>
                    </ul>

                    <h6>Required Fields:</h6>
                    <div class="bg-light p-3 rounded small mb-3">
                        <strong>ARO Rules:</strong> rule_name, schedule, computation_type, status<br>
                        <strong>Calculations:</strong> matter_id, fee_type, amount, calculation_date<br>
                        <strong>Matters:</strong> matter_id, client_name, matter_type, open_date
                    </div>

                    <h6>Best Practices:</h6>
                    <ul class="small">
                        <li>Use provided templates to ensure proper formatting</li>
                        <li>Validate data before importing</li>
                        <li>Backup existing data before large imports</li>
                        <li>Test with small files first</li>
                    </ul>
                </div>
            </div>

            <!-- Import Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Import Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Last Import Success Rate</small>
                            <small>98.5%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 98.5%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Average Processing Time</small>
                            <small>45 sec</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Data Quality Score</small>
                            <small>96.2%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 96.2%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-arrow-clockwise me-2"></i>Validate Current Data
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-gear me-2"></i>Import Settings
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-question-circle me-2"></i>Get Help
                        </button>
                        <button class="btn btn-outline-danger text-start">
                            <i class="bi bi-trash me-2"></i>Clear Import Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import History Modal -->
<div class="modal fade" id="importHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Import Type</th>
                            <th>File Name</th>
                            <th>Records</th>
                            <th>Status</th>
                            <th>Imported By</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Dec 18, 2023 14:30</td>
                            <td>ARO Rules</td>
                            <td>aro_rules_v2.1.csv</td>
                            <td>24</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>admin@lawfirm.com</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">View Log</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Dec 15, 2023 11:15</td>
                            <td>Fee Calculations</td>
                            <td>historical_calculations.xlsx</td>
                            <td>147</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>john@lawfirm.com</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">View Log</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Dec 10, 2023 09:45</td>
                            <td>Matter Data</td>
                            <td>matters_import.csv</td>
                            <td>89</td>
                            <td><span class="badge bg-warning">Partial</span></td>
                            <td>sarah@lawfirm.com</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">View Log</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Dec 5, 2023 16:20</td>
                            <td>ARO Rules</td>
                            <td>legacy_rules.csv</td>
                            <td>18</td>
                            <td><span class="badge bg-danger">Failed</span></td>
                            <td>admin@lawfirm.com</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">View Log</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;

    function selectImportType(type) {
        document.querySelectorAll('.import-type-card').forEach(card => {
            card.classList.remove('border-2', 'border-primary');
        });
        event.target.closest('.import-type-card').classList.add('border-2', 'border-primary');
        document.getElementById('step2').classList.remove('d-none');
    }

    function nextStep() {
        if (currentStep < 4) {
            document.getElementById(`step${currentStep}`).classList.add('d-none');
            currentStep++;
            document.getElementById(`step${currentStep}`).classList.remove('d-none');
            document.getElementById('prevBtn').disabled = false;

            if (currentStep === 4) {
                document.getElementById('nextBtn').innerHTML = 'Import Data <i class="bi bi-upload ms-1"></i>';
            }
        } else {
            // Import data
            alert('Import process started!');
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            document.getElementById(`step${currentStep}`).classList.add('d-none');
            currentStep--;
            document.getElementById(`step${currentStep}`).classList.remove('d-none');

            if (currentStep === 1) {
                document.getElementById('prevBtn').disabled = true;
            }
            document.getElementById('nextBtn').innerHTML = 'Next <i class="bi bi-arrow-right ms-1"></i>';
        }
    }

    document.getElementById('fileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('fileInfo').innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-file-earmark-text me-2"></i>
                <strong>${file.name}</strong> (${(file.size / 1024 / 1024).toFixed(2)} MB)
                <br><small>Ready for mapping</small>
            </div>
        `;
            document.getElementById('step3').classList.remove('d-none');
        }
    });
</script>

<style>
    .import-type-card {
        cursor: pointer;
        transition: all 0.2s;
    }
    .import-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .border-dashed {
        border-style: dashed !important;
    }
</style>