<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/aro/rules">ARO Rules</a></li>
                    <li class="breadcrumb-item active">Slab Editor: Instruction Fee - Civil Suit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Slab Editor</h1>
            <p class="text-muted mb-0">Manage tiered computation brackets for Instruction Fee - Civil Suit</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export Slabs
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSlabModal">
                <i class="bi bi-plus-circle me-1"></i> Add New Slab
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Slabs Table -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Slab Configuration</h5>
                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#testCalculatorModal">
                        <i class="bi bi-calculator me-1"></i> Test Calculator
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>From (KES)</th>
                                <th>To (KES)</th>
                                <th>Percentage/Fee</th>
                                <th>Calculation Example</th>
                                <th width="150">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>0</td>
                                <td>1,000,000</td>
                                <td>7.5%</td>
                                <td><small>KES 500,000 → KES 37,500</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>1,000,001</td>
                                <td>5,000,000</td>
                                <td>5.0%</td>
                                <td><small>KES 3,000,000 → KES 150,000</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>5,000,001</td>
                                <td>10,000,000</td>
                                <td>1.44%</td>
                                <td><small>KES 7,500,000 → KES 108,000</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>10,000,001</td>
                                <td>Above</td>
                                <td>0.72%</td>
                                <td><small>KES 15,000,000 → KES 108,000</small></td>
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

            <!-- Validation Rules -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Validation Rules</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="checkOverlap" checked>
                                <label class="form-check-label" for="checkOverlap">
                                    Prevent slab overlaps
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="checkGaps" checked>
                                <label class="form-check-label" for="checkGaps">
                                    Prevent value gaps
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="autoSort" checked>
                                <label class="form-check-label" for="autoSort">
                                    Auto-sort slabs by value
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="validatePercentages">
                                <label class="form-check-label" for="validatePercentages">
                                    Validate percentage ranges
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Calculation Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Calculation Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Test Value (KES)</label>
                        <input type="number" class="form-control" id="testValue" value="7500000" min="0">
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
                        <strong>Total: KES 311,000</strong>
                    </div>
                    <button class="btn btn-outline-primary w-100" onclick="calculateTest()">
                        <i class="bi bi-calculator me-1"></i> Calculate
                    </button>
                </div>
            </div>

            <!-- Rule Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Rule Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Rule Name:</dt>
                        <dd class="col-sm-7">Instruction Fee - Civil Suit</dd>

                        <dt class="col-sm-5">Schedule:</dt>
                        <dd class="col-sm-7">Schedule 1</dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7"><span class="badge bg-success">Active</span></dd>

                        <dt class="col-sm-5">Effective Date:</dt>
                        <dd class="col-sm-7">2024-01-01</dd>

                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">2024-01-15</dd>

                        <dt class="col-sm-5">Slab Count:</dt>
                        <dd class="col-sm-7">4 slabs</dd>
                    </dl>
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
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">From (KES) *</label>
                            <input type="number" class="form-control" placeholder="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To (KES) *</label>
                            <input type="number" class="form-control" placeholder="1000000" min="0">
                            <small class="form-text text-muted">Use 0 for 'above' the last slab</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Percentage/Fee *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" placeholder="7.5" step="0.01" min="0">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text text-muted">Enter percentage value or fixed amount</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="fixedAmount">
                        <label class="form-check-label" for="fixedAmount">
                            This is a fixed amount (not percentage)
                        </label>
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