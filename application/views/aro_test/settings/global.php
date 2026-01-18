<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Global ARO Settings</h1>
            <p class="text-muted mb-0">System-wide ARO configuration and defaults</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-secondary" id="resetSettings">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset to Defaults
            </button>
            <button class="btn btn-primary" id="saveSettings">
                <i class="bi bi-check-circle me-1"></i> Save Changes
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Tax & VAT Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tax & VAT Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">VAT Percentage *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" value="16" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="form-text text-muted">Current VAT rate in Kenya</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">VAT Registration Number</label>
                            <input type="text" class="form-control" value="P0512345678" placeholder="VAT registration number">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="autoCalculateVAT" checked>
                        <label class="form-check-label" for="autoCalculateVAT">
                            Automatically calculate VAT on all fees
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showVATSeparately" checked>
                        <label class="form-check-label" for="showVATSeparately">
                            Show VAT as separate line item in bills
                        </label>
                    </div>
                </div>
            </div>

            <!-- Calculation Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Calculation Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Default Rounding Method</label>
                            <select class="form-select">
                                <option value="nearest">Round to Nearest Shilling</option>
                                <option value="up" selected>Round Up to Nearest Shilling</option>
                                <option value="down">Round Down to Nearest Shilling</option>
                                <option value="nearest10">Round to Nearest 10 Shillings</option>
                                <option value="nearest100">Round to Nearest 100 Shillings</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Decimal Places</label>
                            <select class="form-select">
                                <option value="0">0 (KES 1,234)</option>
                                <option value="2" selected>2 (KES 1,234.56)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="autoSaveCalculations" checked>
                        <label class="form-check-label" for="autoSaveCalculations">
                            Auto-save calculations as draft
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showCalculationBreakdown" checked>
                        <label class="form-check-label" for="showCalculationBreakdown">
                            Always show detailed calculation breakdown
                        </label>
                    </div>
                </div>
            </div>

            <!-- Uplift Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Uplift & Discretionary Fees</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Maximum Uplift Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" value="25" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="form-text text-muted">Maximum allowed uplift above ARO scale</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Uplift Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" value="15" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="form-text text-muted">Pre-filled uplift value in calculator</small>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="requireUpliftApproval" checked>
                        <label class="form-check-label" for="requireUpliftApproval">
                            Require partner approval for uplifts above 15%
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="requireUpliftJustification">
                        <label class="form-check-label" for="requireUpliftJustification">
                            Require written justification for all uplifts
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Court-Specific Overrides -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Court-Specific Rules</h5>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Court
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">High Court of Kenya</label>
                        <select class="form-select">
                            <option value="default">Use Default ARO Rules</option>
                            <option value="custom" selected>Custom Rules Applied</option>
                        </select>
                        <small class="form-text text-muted">Enhanced rates for complex matters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Milimani Commercial Court</label>
                        <select class="form-select">
                            <option value="default" selected>Use Default ARO Rules</option>
                            <option value="custom">Custom Rules</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Magistrate Courts</label>
                        <select class="form-select">
                            <option value="default" selected>Use Default ARO Rules</option>
                            <option value="custom">Custom Rules</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Environment & Land Court</label>
                        <select class="form-select">
                            <option value="default">Use Default ARO Rules</option>
                            <option value="custom" selected>Custom Rules Applied</option>
                        </select>
                        <small class="form-text text-muted">Specialized rates for land matters</small>
                    </div>
                </div>
            </div>

            <!-- System Defaults -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">System Defaults</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Default Currency</label>
                        <select class="form-select">
                            <option value="KES" selected>Kenyan Shilling (KES)</option>
                            <option value="USD">US Dollar (USD)</option>
                            <option value="EUR">Euro (EUR)</option>
                            <option value="GBP">British Pound (GBP)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Format</label>
                        <select class="form-select">
                            <option value="dd/mm/yyyy">DD/MM/YYYY</option>
                            <option value="mm/dd/yyyy">MM/DD/YYYY</option>
                            <option value="yyyy-mm-dd" selected>YYYY-MM-DD</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Number Format</label>
                        <select class="form-select">
                            <option value="1,234.56" selected>1,234.56 (International)</option>
                            <option value="1.234,56">1.234,56 (European)</option>
                            <option value="1234.56">1234.56 (No separator)</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Time Zone</label>
                        <select class="form-select">
                            <option value="EAT" selected>East Africa Time (UTC+3)</option>
                            <option value="UTC">Coordinated Universal Time (UTC)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger">
                <div class="card-header bg-danger bg-opacity-10">
                    <h5 class="mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        These actions are irreversible. Please proceed with caution.
                    </p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-danger">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset All Settings
                        </button>
                        <button class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Clear All Calculation Data
                        </button>
                        <button class="btn btn-outline-danger">
                            <i class="bi bi-archive me-1"></i> Archive Old Calculations
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>