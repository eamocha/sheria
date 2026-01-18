<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Slab Configuration</h1>
            <p class="text-muted mb-0">Manage tiered calculation brackets for ARO fee schedules</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importSlabsModal">
                <i class="bi bi-upload me-1"></i> Import Slabs
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSlabModal">
                <i class="bi bi-plus-circle me-1"></i> Add Slab
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Slab Management -->
        <div class="col-lg-8">
            <!-- Schedule 1 - Civil Matters -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">Schedule 1</span>
                        Civil Matters - Instruction Fee
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary">Copy</button>
                        <button class="btn btn-outline-primary">Export</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>From (KES)</th>
                                <th>To (KES)</th>
                                <th>Percentage</th>
                                <th>Fixed Amount</th>
                                <th>Calculation Example</th>
                                <th width="120">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="0" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="1000000" min="0">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" value="7.5" step="0.1" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="" placeholder="Fixed amount" disabled>
                                </td>
                                <td><small>KES 500,000 → KES 37,500</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Update</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="1000001" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="5000000" min="0">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" value="5.0" step="0.1" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="" placeholder="Fixed amount" disabled>
                                </td>
                                <td><small>KES 3,000,000 → KES 150,000</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Update</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="5000001" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="10000000" min="0">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" value="1.44" step="0.01" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="" placeholder="Fixed amount" disabled>
                                </td>
                                <td><small>KES 7,500,000 → KES 108,000</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Update</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="10000001" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="0" placeholder="Above" disabled>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" value="0.72" step="0.01" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="" placeholder="Fixed amount" disabled>
                                </td>
                                <td><small>KES 15,000,000 → KES 108,000</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Update</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Schedule 2 - Conveyancing -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="badge bg-success me-2">Schedule 2</span>
                        Conveyancing - Sale/Purchase
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary">Copy</button>
                        <button class="btn btn-outline-primary">Export</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>From (KES)</th>
                                <th>To (KES)</th>
                                <th>Percentage</th>
                                <th>Fixed Amount</th>
                                <th>Calculation Example</th>
                                <th width="120">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="0" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="1000000" min="0">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" value="1.5" step="0.1" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="" placeholder="Fixed amount" disabled>
                                </td>
                                <td><small>KES 500,000 → KES 7,500</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Update</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="1000001" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="5000000" min="0">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" value="1.0" step="0.1" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="" placeholder="Fixed amount" disabled>
                                </td>
                                <td><small>KES 3,000,000 → KES 30,000</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Update</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="5000001" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="10000000" min="0">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" value="0.72" step="0.01" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" value="" placeholder="Fixed amount" disabled>
                                </td>
                                <td><small>KES 7,500,000 → KES 54,000</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Update</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Fixed Amount Rules -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Fixed Amount Rules</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Commercial Agreement Drafting</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Complexity</th>
                                        <th>Fixed Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Simple</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">KES</span>
                                                <input type="number" class="form-control" value="15000" min="0">
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Update</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Medium</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">KES</span>
                                                <input type="number" class="form-control" value="35000" min="0">
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Update</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Complex</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">KES</span>
                                                <input type="number" class="form-control" value="75000" min="0">
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Update</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Attendance Fees</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Duration</th>
                                        <th>Fixed Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Half Day</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">KES</span>
                                                <input type="number" class="form-control" value="10000" min="0">
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Update</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Full Day</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">KES</span>
                                                <input type="number" class="form-control" value="20000" min="0">
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Update</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Per Hour</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">KES</span>
                                                <input type="number" class="form-control" value="5000" min="0">
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Update</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Tools & Preview -->
        <div class="col-lg-4">
            <!-- Calculation Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Calculation Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Test Matter Value</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" class="form-control" id="testValue" value="7500000" min="0">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Calculation Result</h6>
                        <div class="mb-2">
                            <small class="text-muted">Breakdown:</small>
                            <div>• First 1M @ 7.5% = KES 75,000</div>
                            <div>• Next 4M @ 5.0% = KES 200,000</div>
                            <div>• Next 2.5M @ 1.44% = KES 36,000</div>
                        </div>
                        <hr>
                        <strong>Total Instruction Fee: KES 311,000</strong>
                    </div>
                    <button class="btn btn-outline-primary w-100" onclick="calculateTest()">
                        <i class="bi bi-calculator me-1"></i> Calculate
                    </button>
                </div>
            </div>

            <!-- Validation Rules -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Validation Rules</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="checkOverlap" checked>
                        <label class="form-check-label" for="checkOverlap">
                            Prevent slab value overlaps
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="checkGaps" checked>
                        <label class="form-check-label" for="checkGaps">
                            Prevent value gaps between slabs
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="autoSort" checked>
                        <label class="form-check-label" for="autoSort">
                            Auto-sort slabs by value
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="validatePercentages">
                        <label class="form-check-label" for="validatePercentages">
                            Validate percentage ranges (0-100%)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="requireJustification">
                        <label class="form-check-label" for="requireJustification">
                            Require justification for changes
                        </label>
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
                            <i class="bi bi-calculator me-2"></i>Test All Schedules
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-clipboard-check me-2"></i>Validate Configuration
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-arrow-left-right me-2"></i>Compare Versions
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-graph-up me-2"></i>Performance Analysis
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Slab Modal -->
<div class="modal fade" id="addSlabModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Slab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Schedule</label>
                        <select class="form-select" required>
                            <option value="">Select Schedule</option>
                            <option value="1">Schedule 1 - Civil Matters</option>
                            <option value="2">Schedule 2 - Conveyancing</option>
                            <option value="3">Schedule 3 - Probate</option>
                            <option value="4">Schedule 4 - Commercial</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">From Value (KES)</label>
                            <input type="number" class="form-control" placeholder="0" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To Value (KES)</label>
                            <input type="number" class="form-control" placeholder="1000000" min="0">
                            <small class="form-text text-muted">Leave empty for "above" value</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Calculation Type</label>
                        <select class="form-select" id="calculationType" required>
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="mb-3" id="percentageField">
                        <label class="form-label">Percentage</label>
                        <div class="input-group">
                            <input type="number" class="form-control" placeholder="7.5" step="0.1" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="mb-3 d-none" id="fixedAmountField">
                        <label class="form-label">Fixed Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" class="form-control" placeholder="15000" min="0">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Add Slab</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('calculationType').addEventListener('change', function() {
        const percentageField = document.getElementById('percentageField');
        const fixedAmountField = document.getElementById('fixedAmountField');

        if (this.value === 'percentage') {
            percentageField.classList.remove('d-none');
            fixedAmountField.classList.add('d-none');
        } else {
            percentageField.classList.add('d-none');
            fixedAmountField.classList.remove('d-none');
        }
    });

    function calculateTest() {
        const testValue = document.getElementById('testValue').value;
        // Simulate calculation
        alert(`Calculating fee for KES ${testValue}...`);
    }
</script>