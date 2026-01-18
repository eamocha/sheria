<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Processing Workflow</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Kendo UI CSS -->
    <link href="https://kendo.cdn.telerik.com/themes/6.7.0/default/default-main.css" rel="stylesheet">
    <style>
        .workflow-container {
            overflow-x: auto;
            padding: 20px 0;
        }
        .workflow-steps {
            display: flex;
            min-width: 900px;
            justify-content: space-between;
            position: relative;
            padding-top: 60px;
        }
        .workflow-step {
            width: 180px;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        .step-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            min-height: 180px;
        }
        .step-card:hover {
            border-color: #3f51b5;
            transform: translateY(-5px);
        }
        .step-number {
            background: #3f51b5;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -30px auto 15px;
            font-weight: bold;
        }
        .step-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #3f51b5;
        }
        .step-responsibility {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        .step-connector {
            position: absolute;
            height: 4px;
            background: #e0e0e0;
            top: 20px;
            left: 0;
            right: 0;
            z-index: 1;
        }
        .step-actions {
            margin-top: 10px;
        }
        .action-btn {
            font-size: 12px;
            padding: 4px 8px;
            margin: 2px;
        }
        .status-badge {
            position: absolute;
            top: -10px;
            right: -10px;
        }
        .workflow-header {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="container-fluid workflow-header">
    <h2 class="text-primary">Contract Processing Workflow</h2>
    <p class="text-muted">Procedure Number 4: Contract Management</p>
</div>

<div class="container-fluid workflow-container">
    <div class="workflow-steps">
        <div class="step-connector"></div>

        <!-- Step 1 -->
        <div class="workflow-step">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-title">Draft Contract</div>
                <div class="step-responsibility">Responsibility: SO/OCP</div>
                <div class="step-description small">
                    Using approval of award, letter of offer, acceptance letter and bid document
                </div>
                <div class="step-actions">
                    <button class="btn btn-sm btn-outline-primary action-btn k-button">View Draft</button>
                    <button class="btn btn-sm btn-outline-success action-btn k-button">Start Draft</button>
                </div>
                <span class="badge bg-success status-badge">Active</span>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="workflow-step">
            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-title">HOD Review</div>
                <div class="step-responsibility">Responsibility: Head of Department</div>
                <div class="step-description small">
                    Head of Department reviews the draft contract
                </div>
                <div class="step-actions">
                    <button class="btn btn-sm btn-outline-primary action-btn k-button">Review</button>
                    <button class="btn btn-sm btn-outline-secondary action-btn k-button" disabled>Awaiting</button>
                </div>
                <span class="badge bg-warning text-dark status-badge">Pending</span>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="workflow-step">
            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-title">Legal Review</div>
                <div class="step-responsibility">Responsibility: Manager Legal Services</div>
                <div class="step-description small">
                    Legal Services reviews the draft contract
                </div>
                <div class="step-actions">
                    <button class="btn btn-sm btn-outline-primary action-btn k-button" disabled>Review</button>
                    <button class="btn btn-sm btn-outline-secondary action-btn k-button" disabled>Awaiting</button>
                </div>
                <span class="badge bg-secondary status-badge">Not Started</span>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="workflow-step">
            <div class="step-card">
                <div class="step-number">4</div>
                <div class="step-title">Supplier Review</div>
                <div class="step-responsibility">Responsibility: Supplier</div>
                <div class="step-description small">
                    Supplier reviews the contract terms
                </div>
                <div class="step-actions">
                    <button class="btn btn-sm btn-outline-primary action-btn k-button" disabled>Send to Supplier</button>
                    <button class="btn btn-sm btn-outline-secondary action-btn k-button" disabled>Awaiting</button>
                </div>
                <span class="badge bg-secondary status-badge">Not Started</span>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="workflow-step">
            <div class="step-card">
                <div class="step-number">5</div>
                <div class="step-title">DG Approval</div>
                <div class="step-responsibility">Responsibility: Director General</div>
                <div class="step-description small">
                    Final approval of the contract
                </div>
                <div class="step-actions">
                    <button class="btn btn-sm btn-outline-primary action-btn k-button" disabled>Approve</button>
                    <button class="btn btn-sm btn-outline-secondary action-btn k-button" disabled>Awaiting</button>
                </div>
                <span class="badge bg-secondary status-badge">Not Started</span>
            </div>
        </div>
    </div>
</div>

<!-- Details Panel -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Contract Processing Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <tbody>
                        <tr>
                            <th>Current Stage</th>
                            <td>Draft Contract</td>
                        </tr>
                        <tr>
                            <th>Responsible Party</th>
                            <td>SO/OCP</td>
                        </tr>
                        <tr>
                            <th>Next Step</th>
                            <td>HOD Review</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Contract Reference:</label>
                        <input type="text" class="form-control form-control-sm" value="CON-2023-0042" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initiated Date:</label>
                        <input type="text" class="form-control form-control-sm" value="2023-11-15" readonly>
                    </div>
                </div>
            </div>

            <!-- Kendo Grid for Document History -->
            <h6 class="mt-3">Document History</h6>
            <div id="documentHistoryGrid"></div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Kendo UI JS -->
<script src="https://kendo.cdn.telerik.com/2023.2.718/js/jquery.min.js"></script>
<script src="https://kendo.cdn.telerik.com/2023.2.718/js/kendo.all.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Kendo Grid for document history
        $("#documentHistoryGrid").kendoGrid({
            dataSource: {
                data: [
                    {
                        date: "2023-11-15 09:30",
                        action: "Draft Initiated",
                        by: "J. Mwangi (SO/OCP)",
                        comments: "Initial draft created"
                    },
                    {
                        date: "2023-11-14",
                        action: "Award Approved",
                        by: "Director/SCM",
                        comments: "Contract awarded to supplier"
                    }
                ],
                schema: {
                    model: {
                        fields: {
                            date: { type: "string" },
                            action: { type: "string" },
                            by: { type: "string" },
                            comments: { type: "string" }
                        }
                    }
                },
                pageSize: 5
            },
            height: 200,
            scrollable: true,
            sortable: true,
            pageable: true,
            columns: [
                { field: "date", title: "Date/Time", width: "150px" },
                { field: "action", title: "Action", width: "150px" },
                { field: "by", title: "By", width: "150px" },
                { field: "comments", title: "Comments" }
            ]
        });

        // Add click handlers for action buttons
        $(".action-btn").on("click", function() {
            const stepTitle = $(this).closest(".step-card").find(".step-title").text();
            alert(`Action initiated for: ${stepTitle}`);
            // In a real implementation, this would trigger a modal or API call
        });
    });
</script>
</body>
</html>