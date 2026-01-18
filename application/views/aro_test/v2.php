<!-- ARO Rule Management Dashboard -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">ARO Rule Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRuleModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Rule
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Rule Name</th>
                    <th>Schedule</th>
                    <th>Computation Type</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Instruction Fee - Civil Suit</td>
                    <td>Schedule 1</td>
                    <td>Slab-based</td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>2024-01-15</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                        <button class="btn btn-sm btn-outline-secondary">Slabs</button>
                        <button class="btn btn-sm btn-outline-danger">Deactivate</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Slab Editor Interface -->
<div class="modal fade" id="slabEditorModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Slab Editor: Instruction Fee - Civil Suit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>From (KES)</th>
                            <th>To (KES)</th>
                            <th>Percentage/Fee</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>0</td>
                            <td>1,000,000</td>
                            <td>7.5%</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>1,000,001</td>
                            <td>5,000,000</td>
                            <td>5.0%</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Add New Slab
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Computation with Rule Version Tracking -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Fee Computation</h6>
        <small class="text-muted">Using ARO Rules v2.1 (Effective: 2024-01-01)</small>
    </div>
    <div class="card-body">
        <!-- Computation form here -->
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            This calculation uses rule version 2.1. View <a href="#">version history</a>
        </div>
    </div>
</div>
<!-- Matter-Level Fee Computation Panel -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">ARO Fee Computation</h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Fee Schedule</label>
                <select class="form-select" id="feeSchedule">
                    <option value="">Select Schedule</option>
                    <option value="schedule_1">Schedule 1 - Civil Matters</option>
                    <option value="schedule_2">Schedule 2 - Conveyancing</option>
                    <option value="schedule_3">Schedule 3 - Probate</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fee Item</label>
                <select class="form-select" id="feeItem" disabled>
                    <option value="">Select schedule first</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Subject Matter Value (KES)</label>
                <input type="number" class="form-control" id="matterValue" placeholder="Enter value">
            </div>
            <div class="col-md-6">
                <label class="form-label">Discretionary Uplift (%)</label>
                <input type="number" class="form-control" id="upliftPercentage" value="0" min="0" max="100">
            </div>
        </div>

        <div class="d-grid">
            <button class="btn btn-primary" id="computeFee">
                <i class="bi bi-calculator me-1"></i> Compute Fee
            </button>
        </div>
    </div>
</div>
<!-- Matter-Level Fee Computation Panel -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">ARO Fee Computation</h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Fee Schedule</label>
                <select class="form-select" id="feeSchedule">
                    <option value="">Select Schedule</option>
                    <option value="schedule_1">Schedule 1 - Civil Matters</option>
                    <option value="schedule_2">Schedule 2 - Conveyancing</option>
                    <option value="schedule_3">Schedule 3 - Probate</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fee Item</label>
                <select class="form-select" id="feeItem" disabled>
                    <option value="">Select schedule first</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Subject Matter Value (KES)</label>
                <input type="number" class="form-control" id="matterValue" placeholder="Enter value">
            </div>
            <div class="col-md-6">
                <label class="form-label">Discretionary Uplift (%)</label>
                <input type="number" class="form-control" id="upliftPercentage" value="0" min="0" max="100">
            </div>
        </div>

        <div class="d-grid">
            <button class="btn btn-primary" id="computeFee">
                <i class="bi bi-calculator me-1"></i> Compute Fee
            </button>
        </div>
    </div>
</div>
<!-- Audit History Interface -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">ARO Rule Version History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>Version</th>
                    <th>Effective Date</th>
                    <th>Changes</th>
                    <th>Modified By</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>v2.1</td>
                    <td>2024-01-01</td>
                    <td>Updated slab rates for civil matters</td>
                    <td>admin@lawfirm.com</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">View</button>
                        <button class="btn btn-sm btn-outline-success">Restore</button>
                    </td>
                </tr>
                <tr>
                    <td>v2.0</td>
                    <td>2023-07-01</td>
                    <td>Initial version</td>
                    <td>admin@lawfirm.com</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">View</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Taxation Tracking Interface -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Taxation Tracking</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Bill Amount:</strong> KES 430,708
            </div>
            <div class="col-md-6">
                <strong>Taxed Amount:</strong>
                <input type="number" class="form-control d-inline-block w-50 ms-2" value="430708">
            </div>
        </div>
        <div class="mt-3">
            <label class="form-label">Taxation Notes</label>
            <textarea class="form-control" rows="3" placeholder="Taxing officer's remarks..."></textarea>
        </div>
        <div class="mt-3">
            <button class="btn btn-success">Save Taxation Result</button>
        </div>
    </div>
</div>