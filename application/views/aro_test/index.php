<!-- Navigation Menu -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="bi bi-house-fill me-2"></i>
            Sheria360
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#">
                        <i class="bi bi-speedometer2 me-1"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-folder me-1"></i>
                        Matters
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-calculator me-1"></i>
                        ARO Tools
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#aroCalculatorModal">
                                <i class="bi bi-calculator me-2"></i>ARO Calculator
                            </a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#billOfCostsModal">
                                <i class="bi bi-file-text me-2"></i>Bill of Costs
                            </a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#aroDatabaseModal">
                                <i class="bi bi-search me-2"></i>ARO Database
                            </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">
                                <i class="bi bi-graph-up me-2"></i>Fee Reports
                            </a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-people me-1"></i>
                        Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-cash-coin me-1"></i>
                        Billing
                    </a>
                </li>
            </ul>
            <div class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        User Name
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>Profile
                            </a></li>
                        <li><a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a></li>
                    </ul>
                </li>
            </div>
        </div>
    </div>
</nav><!-- ARO Performance Widget -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">ARO Fee Performance</h5>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary active">This Month</button>
            <button class="btn btn-outline-secondary">Quarter</button>
            <button class="btn btn-outline-secondary">Year</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="d-flex align-items-center">
                    <div class="me-3 text-success">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">ARO Compliance</h6>
                        <p class="mb-0">98%</p>
                    </div>
                </div>
                <div class="progress mt-1" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 98%"></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="d-flex align-items-center">
                    <div class="me-3 text-primary">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Avg. Uplift</h6>
                        <p class="mb-0">12%</p>
                    </div>
                </div>
                <div class="progress mt-1" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: 80%"></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="d-flex align-items-center">
                    <div class="me-3 text-warning">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Bill Recovery</h6>
                        <p class="mb-0">85%</p>
                    </div>
                </div>
                <div class="progress mt-1" style="height: 6px;">
                    <div class="progress-bar bg-warning" style="width: 85%"></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="d-flex align-items-center">
                    <div class="me-3 text-info">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Time Saved</h6>
                        <p class="mb-0">42 hrs</p>
                    </div>
                </div>
                <div class="progress mt-1" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: 70%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Quick Actions -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">ARO Quick Actions</h5>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#aroCalculatorModal">
                <i class="bi bi-calculator me-1"></i> ARO Calculator
            </button>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#billOfCostsModal">
                <i class="bi bi-file-text me-1"></i> Generate Bill of Costs
            </button>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#aroDatabaseModal">
                <i class="bi bi-search me-1"></i> ARO Database
            </button>
            <button class="btn btn-outline-primary">
                <i class="bi bi-graph-up me-1"></i> Fee Reports
            </button>
        </div>
    </div>
</div>
<!-- ARO Calculator Modal -->
<div class="modal fade" id="aroCalculatorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ARO Fee Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Select Matter</label>
                        <select class="form-select" id="matterSelect">
                            <option value="">Choose matter...</option>
                            <option value="MAT2024-001" data-value="5000000">MAT2024-001 - John Mwangi (KES 5M)</option>
                            <option value="MAT2024-002" data-value="10000000">MAT2024-002 - ABC Ltd (KES 10M)</option>
                            <option value="MAT2024-003" data-value="8500000">MAT2024-003 - Sarah Johnson (KES 8.5M)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Matter Type</label>
                        <select class="form-select" id="calcMatterType">
                            <option>Civil Suit - Money Claim</option>
                            <option>Conveyancing</option>
                            <option>Probate</option>
                            <option>Commercial Agreement</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Case Value (KES)</label>
                        <input type="text" class="form-control" id="calcCaseValue" readonly>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Fee Breakdown</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Calculation</th>
                                    <th>Amount (KES)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Instruction Fee</td>
                                    <td id="instructionCalculation">-</td>
                                    <td id="instructionAmount">-</td>
                                </tr>
                                <tr>
                                    <td>Getting-Up Fee</td>
                                    <td id="gettingUpCalculation">-</td>
                                    <td id="gettingUpAmount">-</td>
                                </tr>
                                <tr>
                                    <td>VAT (16%)</td>
                                    <td id="vatCalculation">-</td>
                                    <td id="vatAmount">-</td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Total Estimate</strong></td>
                                    <td></td>
                                    <td><strong id="totalAmount">-</strong></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyToMatter">Apply to Matter</button>
            </div>
        </div>
    </div>
</div>
<!-- Bill of Costs Modal -->
<div class="modal fade" id="billOfCostsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Bill of Costs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Select Matter</label>
                        <select class="form-select" id="billMatterSelect">
                            <option value="">Choose matter...</option>
                            <option value="MAT2024-001">MAT2024-001 - Recovery of Commercial Debt</option>
                            <option value="MAT2024-002">MAT2024-002 - Commercial Agreement Drafting</option>
                            <option value="MAT2024-003">MAT2024-003 - Property Conveyancing</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="mt-4">
                            <span id="matterDetails" class="text-muted">Select a matter to view details</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Bill Components</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>Component</th>
                                    <th>Amount (KES)</th>
                                    <th width="100">Actions</th>
                                </tr>
                                </thead>
                                <tbody id="billComponents">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Select a matter to load components</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr class="table-active">
                                    <td><strong>Grand Total</strong></td>
                                    <td><strong id="billTotal">-</strong></td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="generateBill">Generate Bill</button>
            </div>
        </div>
    </div>
</div>
<!-- ARO Database Modal -->
<div class="modal fade" id="aroDatabaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ARO Database</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-4">
                    <input type="text" class="form-control" placeholder="Search ARO provisions...">
                    <button class="btn btn-primary" type="button">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Civil Suit - Money Claims</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>Value Bracket (KES)</th>
                                    <th>Fee Percentage</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>First 1,000,000</td>
                                    <td>7.5%</td>
                                </tr>
                                <tr>
                                    <td>Next 4,000,000</td>
                                    <td>5.0%</td>
                                </tr>
                                <tr>
                                    <td>Next 5,000,000</td>
                                    <td>1.44%</td>
                                </tr>
                                <tr>
                                    <td>Above 10,000,000</td>
                                    <td>0.72%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ARO Calculator
        const matterSelect = document.getElementById('matterSelect');
        const caseValueInput = document.getElementById('calcCaseValue');

        if (matterSelect && caseValueInput) {
            matterSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const matterValue = selectedOption.getAttribute('data-value');

                if (matterValue) {
                    caseValueInput.value = new Intl.NumberFormat().format(matterValue);
                    calculateAROFees(parseInt(matterValue));
                } else {
                    caseValueInput.value = '';
                    clearFeeCalculations();
                }
            });
        }

        function calculateAROFees(value) {
            if (value <= 0) return;

            // Simplified ARO calculation for demo
            let instructionFee = 0;
            let calculation = '';

            if (value <= 1000000) {
                instructionFee = value * 0.075;
                calculation = `KES ${(value/1000000).toFixed(1)}M @ 7.5%`;
            } else if (value <= 5000000) {
                instructionFee = 75000 + (value - 1000000) * 0.05;
                calculation = `1M @ 7.5% = 75K<br>${((value-1000000)/1000000).toFixed(1)}M @ 5%`;
            } else {
                instructionFee = 75000 + 200000 + (value - 5000000) * 0.0144;
                calculation = `1M @ 7.5% = 75K<br>4M @ 5% = 200K<br>${((value-5000000)/1000000).toFixed(1)}M @ 1.44%`;
            }

            instructionFee = Math.round(instructionFee);
            const gettingUpFee = Math.round(instructionFee * 0.5);
            const vatFee = Math.round((instructionFee + gettingUpFee) * 0.16);
            const total = instructionFee + gettingUpFee + vatFee;

            document.getElementById('instructionCalculation').innerHTML = calculation;
            document.getElementById('instructionAmount').textContent = new Intl.NumberFormat().format(instructionFee);
            document.getElementById('gettingUpCalculation').textContent = '50% of Instruction Fee';
            document.getElementById('gettingUpAmount').textContent = new Intl.NumberFormat().format(gettingUpFee);
            document.getElementById('vatCalculation').textContent = '16% of total fees';
            document.getElementById('vatAmount').textContent = new Intl.NumberFormat().format(vatFee);
            document.getElementById('totalAmount').textContent = new Intl.NumberFormat().format(total);
        }

        function clearFeeCalculations() {
            document.getElementById('instructionCalculation').textContent = '-';
            document.getElementById('instructionAmount').textContent = '-';
            document.getElementById('gettingUpCalculation').textContent = '-';
            document.getElementById('gettingUpAmount').textContent = '-';
            document.getElementById('vatCalculation').textContent = '-';
            document.getElementById('vatAmount').textContent = '-';
            document.getElementById('totalAmount').textContent = '-';
        }

        // Bill of Costs Generator
        const billMatterSelect = document.getElementById('billMatterSelect');

        if (billMatterSelect) {
            billMatterSelect.addEventListener('change', function() {
                const matterId = this.value;
                if (matterId) {
                    loadBillComponents(matterId);
                } else {
                    clearBillComponents();
                }
            });
        }

        function loadBillComponents(matterId) {
            // Mock data - replace with actual API call
            const components = {
                'MAT2024-001': [
                    { name: 'Instruction Fee', amount: 187000 },
                    { name: 'Getting-Up Fee', amount: 93500 },
                    { name: 'Attendance Fees', amount: 25000 },
                    { name: 'Filing Fees', amount: 2500 },
                    { name: 'Service of Process', amount: 15000 },
                    { name: 'Other Disbursements', amount: 48300 }
                ]
            };

            const tbody = document.getElementById('billComponents');
            const matterDetails = document.getElementById('matterDetails');

            if (components[matterId]) {
                matterDetails.textContent = `MAT2024-001 - Recovery of Commercial Debt (KES 5,000,000)`;

                tbody.innerHTML = '';
                let total = 0;

                components[matterId].forEach(component => {
                    total += component.amount;
                    tbody.innerHTML += `
                    <tr>
                        <td>${component.name}</td>
                        <td>${new Intl.NumberFormat().format(component.amount)}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary">Edit</button>
                        </td>
                    </tr>
                `;
                });

                const vat = Math.round(total * 0.16);
                total += vat;

                tbody.innerHTML += `
                <tr class="table-light">
                    <td>VAT (16%)</td>
                    <td>${new Intl.NumberFormat().format(vat)}</td>
                    <td></td>
                </tr>
            `;

                document.getElementById('billTotal').textContent = new Intl.NumberFormat().format(total);
            }
        }

        function clearBillComponents() {
            document.getElementById('billComponents').innerHTML =
                '<tr><td colspan="3" class="text-center text-muted">Select a matter to load components</td></tr>';
            document.getElementById('matterDetails').textContent = 'Select a matter to view details';
            document.getElementById('billTotal').textContent = '-';
        }
    });
</script>