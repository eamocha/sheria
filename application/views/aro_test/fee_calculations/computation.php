<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Fee Calculation Variables</h1>
            <p class="text-muted mb-0">Configure and manage all variables for ARO fee calculations</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export Variables
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVariableModal">
                <i class="bi bi-plus-circle me-1"></i> Add Variable
            </button>
        </div>
    </div>

    <!-- Variables Dashboard -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-2">Primary Variables</h6>
                            <h3 class="mb-0 text-primary">24</h3>
                            <span class="text-primary small">Active variables</span>
                        </div>
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-input-cursor text-primary fs-4"></i>
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
                            <h6 class="card-title text-muted mb-2">Adjustment Factors</h6>
                            <h3 class="mb-0 text-success">12</h3>
                            <span class="text-success small">Uplift & discount rules</span>
                        </div>
                        <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-sliders text-success fs-4"></i>
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
                            <h6 class="card-title text-muted mb-2">Tax & Charges</h6>
                            <h3 class="mb-0 text-warning">8</h3>
                            <span class="text-warning small">VAT and additional fees</span>
                        </div>
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cash-coin text-warning fs-4"></i>
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
                            <h6 class="card-title text-muted mb-2">Validation Rules</h6>
                            <h3 class="mb-0 text-info">15</h3>
                            <span class="text-info small">Data validation rules</span>
                        </div>
                        <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-shield-check text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Variables Management -->
        <div class="col-lg-8">
            <!-- Primary Input Variables -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Primary Input Variables</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">All</button>
                        <button class="btn btn-outline-secondary">Required</button>
                        <button class="btn btn-outline-secondary">Optional</button>
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
                                <th>Variable Name</th>
                                <th>Data Type</th>
                                <th>Default Value</th>
                                <th>Validation Rules</th>
                                <th>Required</th>
                                <th width="120">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td>
                                    <strong>matterValue</strong>
                                    <br><small class="text-muted">Value of subject matter in KES</small>
                                </td>
                                <td><span class="badge bg-primary">Number</span></td>
                                <td><code>0</code></td>
                                <td>
                                    <small>min: 0, max: 1000000000</small>
                                </td>
                                <td><span class="badge bg-success">Yes</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td>
                                    <strong>matterType</strong>
                                    <br><small class="text-muted">Classification of legal matter</small>
                                </td>
                                <td><span class="badge bg-info">String</span></td>
                                <td><code>"civil_litigation"</code></td>
                                <td>
                                    <small>enum: civil, commercial, conveyancing, probate</small>
                                </td>
                                <td><span class="badge bg-success">Yes</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td>
                                    <strong>feeType</strong>
                                    <br><small class="text-muted">Type of fee being calculated</small>
                                </td>
                                <td><span class="badge bg-info">String</span></td>
                                <td><code>"instruction_fee"</code></td>
                                <td>
                                    <small>enum: instruction_fee, getting_up_fee, attendance_fee</small>
                                </td>
                                <td><span class="badge bg-success">Yes</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td>
                                    <strong>complexityLevel</strong>
                                    <br><small class="text-muted">Case complexity assessment</small>
                                </td>
                                <td><span class="badge bg-info">String</span></td>
                                <td><code>"medium"</code></td>
                                <td>
                                    <small>enum: simple, medium, complex, highly_complex</small>
                                </td>
                                <td><span class="badge bg-warning">No</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td>
                                    <strong>urgencyFactor</strong>
                                    <br><small class="text-muted">Urgency multiplier</small>
                                </td>
                                <td><span class="badge bg-primary">Number</span></td>
                                <td><code>1.0</code></td>
                                <td>
                                    <small>min: 1.0, max: 2.0, step: 0.1</small>
                                </td>
                                <td><span class="badge bg-warning">No</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ARO Rule Structure Variables -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">ARO Rule Structure Variables</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Slab-based Calculation Rules</h6>
                            <div class="bg-light p-3 rounded mb-3">
                                <pre class="mb-0 small"><code>slabs: [
  {
    minValue: 0,
    maxValue: 1000000,
    percentage: 7.5,
    fixedAmount: null
  },
  {
    minValue: 1000001,
    maxValue: 5000000,
    percentage: 5.0,
    fixedAmount: null
  }
]</code></pre>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Fixed Amount Rules</h6>
                            <div class="bg-light p-3 rounded">
                                <pre class="mb-0 small"><code>fixedAmountRanges: {
  simple: 15000,
  medium: 35000,
  complex: 75000,
  highly_complex: 150000
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adjustment Variables -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Adjustment & Multiplier Variables</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Uplift Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" value="15.0" min="0" max="100" step="0.5">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="form-text text-muted">Default discretionary uplift</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Complexity Multiplier</label>
                            <select class="form-select">
                                <option value="1.0">Simple (1.0x)</option>
                                <option value="1.2" selected>Medium (1.2x)</option>
                                <option value="1.5">Complex (1.5x)</option>
                                <option value="2.0">Highly Complex (2.0x)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Urgency Factor</label>
                            <select class="form-select">
                                <option value="1.0">Standard (1.0x)</option>
                                <option value="1.2">Urgent (1.2x)</option>
                                <option value="1.5">Very Urgent (1.5x)</option>
                                <option value="2.0">Emergency (2.0x)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="autoApplyUplift" checked>
                                <label class="form-check-label" for="autoApplyUplift">
                                    Auto-apply default uplift
                                </label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="requireUpliftJustification">
                                <label class="form-check-label" for="requireUpliftJustification">
                                    Require uplift justification
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="enableVolumeDiscount">
                                <label class="form-check-label" for="enableVolumeDiscount">
                                    Enable volume discounts
                                </label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="enableProBono">
                                <label class="form-check-label" for="enableProBono">
                                    Enable pro bono reductions
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Configuration & Preview -->
        <div class="col-lg-4">
            <!-- Tax & Additional Charges -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tax & Additional Charges</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">VAT Percentage</label>
                        <div class="input-group">
                            <input type="number" class="form-control" value="16.0" min="0" max="100" step="0.1">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Disbursements</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" class="form-control" value="25000" min="0" step="1000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filing Fees</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" class="form-control" value="2500" min="0" step="100">
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="vatInclusive" checked>
                        <label class="form-check-label" for="vatInclusive">
                            Amounts include VAT by default
                        </label>
                    </div>
                </div>
            </div>

            <!-- Professional Factors -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Professional & Experience Factors</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Advocate Experience Level</label>
                        <select class="form-select">
                            <option value="1.0">Junior (1.0x)</option>
                            <option value="1.3">Mid-Level (1.3x)</option>
                            <option value="1.7" selected>Senior (1.7x)</option>
                            <option value="2.0">Partner (2.0x)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Firm Tier Multiplier</label>
                        <select class="form-select">
                            <option value="1.0">Small Firm (1.0x)</option>
                            <option value="1.2">Medium Firm (1.2x)</option>
                            <option value="1.5" selected>Large Firm (1.5x)</option>
                            <option value="2.0">Boutique (2.0x)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location Factor</label>
                        <select class="form-select">
                            <option value="1.0">Rural (1.0x)</option>
                            <option value="1.2">Town (1.2x)</option>
                            <option value="1.5" selected>Urban (1.5x)</option>
                            <option value="2.0">CBD (2.0x)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Variable Preview -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Current Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded small">
                        <pre><code>{
  "matterValue": 12500000,
  "matterType": "civil_litigation",
  "feeType": "instruction_fee",
  "complexityLevel": "medium",
  "urgencyFactor": 1.0,
  "upliftPercentage": 15.0,
  "vatPercentage": 16.0,
  "advocateExperience": "senior",
  "firmTier": "large",
  "slabs": [...]
}</code></pre>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-play-circle me-1"></i> Test Configuration
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Variable Modal -->
<div class="modal fade" id="addVariableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Variable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Variable Name</label>
                        <input type="text" class="form-control" placeholder="e.g., courtType" required>
                        <small class="form-text text-muted">Use camelCase naming convention</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Type</label>
                        <select class="form-select" required>
                            <option value="">Select data type</option>
                            <option value="number">Number</option>
                            <option value="string">String</option>
                            <option value="boolean">Boolean</option>
                            <option value="array">Array</option>
                            <option value="object">Object</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Value</label>
                        <input type="text" class="form-control" placeholder="e.g., 0 or 'civil_litigation'">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Validation Rules</label>
                        <textarea class="form-control" rows="3" placeholder="e.g., min: 0, max: 100 or enum: civil, commercial"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="variableRequired">
                        <label class="form-check-label" for="variableRequired">
                            Required for calculations
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Add Variable</button>
            </div>
        </div>
    </div>
</div>