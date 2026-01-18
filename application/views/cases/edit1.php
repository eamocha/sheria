<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Management Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Case Management</h1>
                <span class="badge badge-primary p-2">Case #60114</span>
            </div>
            <p class="text-muted mb-0">Communication Authority - Civil Case</p>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Case Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Case Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Subject:</strong> test case for stages</p>
                            <p><strong>Description:</strong> does not work on stages</p>
                            <p><strong>Case Type:</strong> Civil</p>
                            <p><strong>Case Stage:</strong> Execution</p>
                            <p><strong>Client Position:</strong> None</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Priority:</strong> <span class="badge badge-warning">Medium</span></p>
                            <p><strong>Success Probability:</strong> Medium</p>
                            <p><strong>Provider Group:</strong> Legal Team</p>
                            <p><strong>Arrival Date:</strong> 2025-10-02</p>
                            <p><strong>Due Date:</strong> 2025-10-03</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Status & Workflow -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Case Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Current Status:</span>
                                <span class="badge badge-success p-2">Open Case File</span>
                            </div>

                            <h6 class="mt-4 mb-3">Available Statuses:</h6>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Conflict of Interest Declaration
                                    <span class="badge badge-secondary">9</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center active">
                                    Open Case File
                                    <span class="badge badge-light">19</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Status Transition</h5>
                        </div>
                        <div class="card-body">
                            <div class="border-left border-primary pl-3 mb-3">
                                <p class="mb-1"><strong>Preliminary Review</strong></p>
                                <p class="mb-1">From: Open Case File</p>
                                <p class="mb-0">To: Conflict of Interest Declaration</p>
                            </div>

                            <h6 class="mt-4 mb-3">Workflow Statuses:</h6>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center active">
                                    Open
                                    <span class="badge badge-light">1</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    In Progress
                                    <span class="badge badge-light">2</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Pending
                                    <span class="badge badge-light">3</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Closed
                                    <span class="badge badge-light">4</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Cancelled
                                    <span class="badge badge-light">5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Activity Logs -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Case Activity Logs</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="border-left border-success pl-3 mb-3">
                                <h6>Created</h6>
                                <p class="mb-1"><strong>By:</strong> Miriam Kahiro</p>
                                <p class="mb-1"><strong>Email:</strong> eatinga@yahoo.com</p>
                                <p class="mb-1"><strong>Date:</strong> 2025-10-02 11:53</p>
                                <span class="badge badge-success">Active</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-left border-warning pl-3 mb-3">
                                <h6>Last Updated</h6>
                                <p class="mb-1"><strong>By:</strong> System Administrator</p>
                                <p class="mb-1"><strong>Email:</strong> info@sherial60.com</p>
                                <p class="mb-1"><strong>Date:</strong> 2025-11-20 19:15</p>
                                <span class="badge badge-success">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Navigation Tabs -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Case Navigation</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action active">
                        <i class="fas fa-info-circle mr-2"></i>General Info
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-gavel mr-2"></i>Court Activities
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-folder mr-2"></i>Documents
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-money-bill mr-2"></i>Expenses
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-clock mr-2"></i>Time Logs
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-balance-scale mr-2"></i>Related Matters
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-contract mr-2"></i>Contracts
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-cogs mr-2"></i>Settings
                    </a>
                </div>
            </div>

            <!-- Case Options -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Case Options</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="font-weight-bold">Externalize Lawyers:</span>
                        <span class="badge badge-danger ml-2">No</span>
                    </div>
                    <div class="mb-2">
                        <span class="font-weight-bold">Partners Commissions:</span>
                        <span class="badge badge-danger ml-2">No</span>
                    </div>
                    <div class="mb-2">
                        <span class="font-weight-bold">SLA Feature:</span>
                        <span class="badge badge-success ml-2">Yes</span>
                    </div>
                    <div class="mb-2">
                        <span class="font-weight-bold">Shared Documents:</span>
                        <span class="badge badge-danger ml-2">No</span>
                    </div>
                    <div class="mb-0">
                        <span class="font-weight-bold">Archived Matters:</span>
                        <span class="badge badge-success ml-2">Disabled</span>
                    </div>
                </div>
            </div>

            <!-- Assigned Users -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Assigned Team</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Beatrice Mumbi
                            <span class="badge badge-primary badge-pill">7</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            eric sheria
                            <span class="badge badge-primary badge-pill">10013</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Francis Okuto'yi
                            <span class="badge badge-primary badge-pill">6</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Miriam Kahiro
                            <span class="badge badge-primary badge-pill">10</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Samuel rambo
                            <span class="badge badge-primary badge-pill">13</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Reference Data Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Reference Data</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6>Case Types</h6>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Choose Type of Case
                                    <span class="badge badge-secondary badge-pill"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Arbitration
                                    <span class="badge badge-secondary badge-pill">11</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Civil
                                    <span class="badge badge-secondary badge-pill">1</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Criminal
                                    <span class="badge badge-secondary badge-pill">2</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Other
                                    <span class="badge badge-secondary badge-pill">4</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <h6>Case Stages</h6>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Arbitration
                                    <span class="badge badge-secondary badge-pill">9</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Court of Appeal
                                    <span class="badge badge-secondary badge-pill">10027</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Execution
                                    <span class="badge badge-secondary badge-pill">5</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Initial
                                    <span class="badge badge-secondary badge-pill">18</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Judgement
                                    <span class="badge badge-secondary badge-pill">19</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <h6>Priorities</h6>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Critical
                                    <span class="badge badge-danger badge-pill">critical</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    High
                                    <span class="badge badge-warning badge-pill">high</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Medium
                                    <span class="badge badge-info badge-pill">medium</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    Low
                                    <span class="badge badge-secondary badge-pill">low</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>