<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/matters">Matters</a></li>
                    <li class="breadcrumb-item"><a href="/matters/MAT2024-015">MAT2024-015</a></li>
                    <li class="breadcrumb-item active">Fee Computation</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Fee Computation</h1>
            <p class="text-muted mb-0">Kenya Commercial Bank vs Debtor Ltd • MAT2024-015</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Matter
            </button>
            <button class="btn btn-outline-primary">
                <i class="bi bi-clock-history me-1"></i> View History
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Computation Form -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Fee Computation Parameters</h5>
                </div>
                <div class="card-body">
                    <form id="feeComputationForm">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Fee Schedule *</label>
                                <select class="form-select" id="feeSchedule" required>
                                    <option value="">Select Schedule</option>
                                    <option value="schedule_1" selected>Schedule 1 - Civil Matters</option>
                                    <option value="schedule_2">Schedule 2 - Conveyancing</option>
                                    <option value="schedule_3">Schedule 3 - Probate</option>
                                    <option value="schedule_4">Schedule 4 - Commercial</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fee Item *</label>
                                <select class="form-select" id="feeItem" required>
                                    <option value="">Select fee item</option>
                                    <option value="instruction_fee" selected>Instruction Fee</option>
                                    <option value="getting_up_fee">Getting-Up Fee</option>
                                    <option value="attendance_fee">Attendance Fee</option>
                                    <option value="drafting_fee">Drafting Fee</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Subject Matter Value (KES) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="number" class="form-control" id="matterValue"
                                           value="12500000" required min="0" step="1000">
                                </div>
                                <small class="form-text text-muted">Value of the subject matter in dispute</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Discretionary Uplift (%)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="upliftPercentage"
                                           value="15" min="0" max="100" step="0.5">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="form-text text-muted">Additional percentage above ARO scale (0-100%)</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Calculation Notes</label>
                            <textarea class="form-control" id="calculationNotes" rows="3"
                                      placeholder="Add any notes or justification for this calculation..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Using <strong>ARO Rules v2.1</strong> effective from January 1, 2024
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-secondary me-md-2" id="resetForm">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                            </button>
                            <button type="button" class="btn btn-primary" id="computeFee">
                                <i class="bi bi-calculator me-1"></i> Compute Fee
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Calculation History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Calculations</h5>
                    <a href="/matters/MAT2024-015/fees/history" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Instruction Fee</h6>
                                <small class="text-muted">Dec 15, 2023</small>
                            </div>
                            <p class="mb-1">Value: KES 12,500,000 • Fee: KES 347,000</p>
                            <small class="text-muted">Calculated by John Advocate • <span class="badge bg-success">Approved</span></small>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Getting-Up Fee</h6>
                                <small class="text-muted">Dec 10, 2023</small>
                            </div>
                            <p class="mb-1">Based on Instruction Fee • Fee: KES 173,500</p>
                            <small class="text-muted">Calculated by John Advocate • <span class="badge bg-warning">Pending</span></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Results Preview -->
        <div class="col-lg-4">
            <!-- Calculation Results -->
            <div class="card mb-4">
                <div class="card-header bg-success bg-opacity-10">
                    <h5 class="mb-0 text-success">
                        <i class="bi bi-calculator me-2"></i>Computation Results
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="text-primary" id="totalFee">KES 399,050</h3>
                        <small class="text-muted">Total Estimated Fee</small>
                    </div>

                    <div class="breakdown-table">
                        <table class="table table-sm table-borderless">
                            <tbody>
                            <tr>
                                <td>Base Instruction Fee:</td>
                                <td class="text-end" id="baseFee">KES 347,000</td>
                            </tr>
                            <tr>
                                <td>Discretionary Uplift (15%):</td>
                                <td class="text-end" id="upliftAmount">KES 52,050</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end"><strong>KES 399,050</strong></td>
                            </tr>
                            <tr>
                                <td>VAT (16%):</td>
                                <td class="text-end" id="vatAmount">KES 63,848</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Grand Total:</strong></td>
                                <td class="text-end"><strong id="grandTotal">KES 462,898</strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <h6>Calculation Breakdown:</h6>
                        <div class="bg-light p-3 rounded small">
                            <div>• First KES 1,000,000 @ 7.5% = KES 75,000</div>
                            <div>• Next KES 4,000,000 @ 5.0% = KES 200,000</div>
                            <div>• Next KES 7,500,000 @ 1.44% = KES 108,000</div>
                            <div class="mt-2">• Base Fee Total = KES 383,000</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button class="btn btn-success" id="saveCalculation">
                            <i class="bi bi-check-circle me-1"></i> Save Calculation
                        </button>
                        <button class="btn btn-outline-primary" id="generateBill">
                            <i class="bi bi-file-text me-1"></i> Generate Bill of Costs
                        </button>
                    </div>
                </div>
            </div>

            <!-- Rule Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Rule Information</h5>
                </div>
                <div class="card-body">
                    <h6>Instruction Fee - Civil Suit</h6>
                    <p class="small text-muted">For money claims and civil litigation matters</p>

                    <div class="mb-3">
                        <small class="text-muted d-block">Slab Structure:</small>
                        <div class="bg-light p-2 rounded small">
                            <div>0 - 1M: 7.5%</div>
                            <div>1M - 5M: 5.0%</div>
                            <div>5M - 10M: 1.44%</div>
                            <div>Above 10M: 0.72%</div>
                        </div>
                    </div>

                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Discretionary uplift requires client agreement
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Calculation Modal -->
<div class="modal fade" id="saveCalculationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Save Calculation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Calculation Title</label>
                        <input type="text" class="form-control" value="Instruction Fee - Final Calculation" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" required>
                            <option value="draft">Draft</option>
                            <option value="submitted" selected>Submitted for Approval</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyPartner">
                        <label class="form-check-label" for="notifyPartner">
                            Notify partner for approval
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Calculation</button>
            </div>
        </div>
    </div>
</div>