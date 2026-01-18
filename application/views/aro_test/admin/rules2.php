<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">ARO Rule Management</h1>
            <p class="text-muted mb-0">Manage all remuneration rules and schedules</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                <i class="bi bi-plus-circle me-1"></i> Add New Rule
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Schedule</label>
                    <select class="form-select">
                        <option value="">All Schedules</option>
                        <option>Schedule 1 - Civil Matters</option>
                        <option>Schedule 2 - Conveyancing</option>
                        <option>Schedule 3 - Probate</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                        <option>Draft</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Computation Type</label>
                    <select class="form-select">
                        <option value="">All Types</option>
                        <option>Slab-based</option>
                        <option>Fixed Amount</option>
                        <option>Percentage</option>
                        <option>Formula</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" placeholder="Search rules...">
                </div>
            </div>
        </div>
    </div>

    <!-- Rules Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">ARO Rules (24 rules)</h5>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="showInactive">
                <label class="form-check-label" for="showInactive">Show Inactive</label>
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
                        <th>Rule Name</th>
                        <th>Schedule</th>
                        <th>Computation Type</th>
                        <th>Effective Date</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th width="200">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Instruction Fee - Civil Suit</strong>
                            <br><small class="text-muted">For money claims and civil litigation</small>
                        </td>
                        <td>Schedule 1</td>
                        <td><span class="badge bg-info">Slab-based</span></td>
                        <td>2024-01-01</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>2024-01-15</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRuleModal">Edit</button>
                                <button class="btn btn-outline-secondary" onclick="location.href='/admin/aro/rules/1/slabs'">Slabs</button>
                                <button class="btn btn-outline-danger">Deactivate</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Conveyancing - Sale/Purchase</strong>
                            <br><small class="text-muted">Property transfer agreements</small>
                        </td>
                        <td>Schedule 2</td>
                        <td><span class="badge bg-info">Slab-based</span></td>
                        <td>2024-01-01</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>2024-01-10</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-secondary">Slabs</button>
                                <button class="btn btn-outline-danger">Deactivate</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Getting-Up Fee - Civil</strong>
                            <br><small class="text-muted">Case preparation for trial</small>
                        </td>
                        <td>Schedule 1</td>
                        <td><span class="badge bg-warning">Percentage</span></td>
                        <td>2024-01-01</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>2024-01-08</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-secondary">Formula</button>
                                <button class="btn btn-outline-danger">Deactivate</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Probate Administration</strong>
                            <br><small class="text-muted">Estate administration fees</small>
                        </td>
                        <td>Schedule 3</td>
                        <td><span class="badge bg-info">Slab-based</span></td>
                        <td>2024-01-01</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>2023-12-20</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-secondary">Slabs</button>
                                <button class="btn btn-outline-danger">Deactivate</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="table-warning">
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Commercial Agreement Drafting</strong>
                            <br><small class="text-muted">New rule - pending review</small>
                        </td>
                        <td>Schedule 4</td>
                        <td><span class="badge bg-secondary">Fixed Amount</span></td>
                        <td>2024-02-01</td>
                        <td><span class="badge bg-warning">Draft</span></td>
                        <td>2024-01-18</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-success">Activate</button>
                                <button class="btn btn-outline-danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">5 rules selected</span>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm">Bulk Activate</button>
                    <button class="btn btn-outline-secondary btn-sm">Bulk Deactivate</button>
                    <button class="btn btn-outline-danger btn-sm">Delete Selected</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Rule Modal -->
<div class="modal fade" id="addRuleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New ARO Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Rule Name *</label>
                            <input type="text" class="form-control" placeholder="e.g., Instruction Fee - Civil Suit">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Schedule *</label>
                            <select class="form-select">
                                <option value="">Select Schedule</option>
                                <option>Schedule 1 - Civil Matters</option>
                                <option>Schedule 2 - Conveyancing</option>
                                <option>Schedule 3 - Probate</option>
                                <option>Schedule 4 - Commercial</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Computation Type *</label>
                            <select class="form-select" id="computationType">
                                <option value="">Select Type</option>
                                <option value="slab">Slab-based</option>
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                                <option value="formula">Formula</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Effective Date *</label>
                            <input type="date" class="form-control" value="2024-02-01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3" placeholder="Rule description and usage notes..."></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="activateRule">
                        <label class="form-check-label" for="activateRule">
                            Activate rule immediately
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Rule</button>
            </div>
        </div>
    </div>
</div>