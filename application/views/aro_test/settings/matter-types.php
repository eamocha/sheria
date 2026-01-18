<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Matter Type Mapping</h1>
            <p class="text-muted mb-0">Map matter types to ARO schedules and default rules</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importMappingModal">
                <i class="bi bi-upload me-1"></i> Import Mapping
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMatterTypeModal">
                <i class="bi bi-plus-circle me-1"></i> Add Matter Type
            </button>
        </div>
    </div>

    <!-- Mapping Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Matter Type to ARO Schedule Mapping</h5>
            <div class="input-group" style="width: 300px;">
                <input type="text" class="form-control" placeholder="Search matter types...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="bi bi-search"></i>
                </button>
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
                        <th>Matter Type</th>
                        <th>Category</th>
                        <th>Default ARO Schedule</th>
                        <th>Default Fee Rule</th>
                        <th>Auto-Apply</th>
                        <th>Matters Using</th>
                        <th width="150">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Civil Litigation</strong>
                            <br><small class="text-muted">Money claims, torts, civil disputes</small>
                        </td>
                        <td>Litigation</td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Schedule 1 - Civil Matters</option>
                                <option>Schedule 2 - Conveyancing</option>
                                <option>Schedule 3 - Probate</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Instruction Fee - Civil Suit</option>
                                <option>Getting-Up Fee - Civil</option>
                                <option>Attendance Fee</option>
                            </select>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </td>
                        <td>147</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Remove</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Commercial Agreement</strong>
                            <br><small class="text-muted">Contracts, partnerships, commercial docs</small>
                        </td>
                        <td>Corporate</td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Schedule 1 - Civil Matters</option>
                                <option selected>Schedule 4 - Commercial</option>
                                <option>Schedule 3 - Probate</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Commercial Agreement Drafting</option>
                                <option>Instruction Fee - Civil Suit</option>
                            </select>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </td>
                        <td>89</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Remove</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Property Conveyancing</strong>
                            <br><small class="text-muted">Sale, purchase, charge of property</small>
                        </td>
                        <td>Property</td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Schedule 1 - Civil Matters</option>
                                <option selected>Schedule 2 - Conveyancing</option>
                                <option>Schedule 3 - Probate</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Conveyancing - Sale/Purchase</option>
                                <option>Conveyancing - Charge</option>
                            </select>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </td>
                        <td>76</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Remove</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Probate & Administration</strong>
                            <br><small class="text-muted">Estate administration, wills, succession</small>
                        </td>
                        <td>Probate</td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Schedule 1 - Civil Matters</option>
                                <option>Schedule 2 - Conveyancing</option>
                                <option selected>Schedule 3 - Probate</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Probate Administration</option>
                                <option>Will Drafting</option>
                            </select>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>45</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Remove</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <strong>Employment Dispute</strong>
                            <br><small class="text-muted">Labor disputes, wrongful termination</small>
                        </td>
                        <td>Litigation</td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option selected>Schedule 1 - Civil Matters</option>
                                <option>Schedule 4 - Commercial</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm">
                                <option>Instruction Fee - Civil Suit</option>
                                <option>Attendance Fee</option>
                            </select>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </td>
                        <td>32</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Remove</button>
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
                    <span class="text-muted">3 matter types selected</span>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm">Bulk Update Schedule</button>
                    <button class="btn btn-outline-secondary btn-sm">Bulk Toggle Auto-Apply</button>
                    <button class="btn btn-outline-danger btn-sm">Remove Selected</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Management -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Matter Categories</h5>
                    <button class="btn btn-sm btn-outline-primary">Add Category</button>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Litigation</h6>
                                <small class="text-muted">12 matter types</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Delete</button>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Corporate</h6>
                                <small class="text-muted">8 matter types</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Delete</button>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Property</h6>
                                <small class="text-muted">6 matter types</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Delete</button>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">Probate</h6>
                                <small class="text-muted">4 matter types</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary">Edit</button>
                                <button class="btn btn-outline-danger">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mapping Statistics</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Total Matter Types:</dt>
                        <dd class="col-sm-6">24</dd>

                        <dt class="col-sm-6">Mapped to ARO Schedules:</dt>
                        <dd class="col-sm-6">22 (92%)</dd>

                        <dt class="col-sm-6">Auto-Apply Enabled:</dt>
                        <dd class="col-sm-6">18 (75%)</dd>

                        <dt class="col-sm-6">Unmapped Types:</dt>
                        <dd class="col-sm-6">2</dd>

                        <dt class="col-sm-6">Last Updated:</dt>
                        <dd class="col-sm-6">Dec 18, 2023</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Matter Type Modal -->
<div class="modal fade" id="addMatterTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Matter Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Matter Type Name *</label>
                        <input type="text" class="form-control" placeholder="e.g., Intellectual Property" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" required>
                            <option value="">Select Category</option>
                            <option>Litigation</option>
                            <option>Corporate</option>
                            <option>Property</option>
                            <option>Probate</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3" placeholder="Brief description of this matter type..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default ARO Schedule</label>
                        <select class="form-select">
                            <option value="">No default schedule</option>
                            <option>Schedule 1 - Civil Matters</option>
                            <option>Schedule 2 - Conveyancing</option>
                            <option>Schedule 3 - Probate</option>
                            <option>Schedule 4 - Commercial</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="autoApplyMapping">
                        <label class="form-check-label" for="autoApplyMapping">
                            Auto-apply this mapping to new matters
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Add Matter Type</button>
            </div>
        </div>
    </div>
</div>
