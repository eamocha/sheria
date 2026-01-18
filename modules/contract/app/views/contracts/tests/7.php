    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif; /* Ensure Inter font is used */
            background-color: #f8f9fa;
        }
        .workflow-step {
            position: relative;
            margin-bottom: 30px;
        }
        .workflow-step .step-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: 0;
            top: 10px;
            border: 4px solid;
            z-index: 2;
            font-size: 1.5rem; /* Larger icon size */
            color: #6c757d; /* Default icon color */
        }
        .workflow-connector {
            position: absolute;
            left: 30px;
            top: 70px;
            width: 2px;
            height: calc(100% - 40px);
            background-color: #dee2e6;
            border-left: 2px dashed #adb5bd;
        }
        .step-card {
            margin-left: 80px;
            transition: all 0.3s ease;
            border-radius: 0.375rem; /* Bootstrap default border-radius */
        }
        .step-pending .step-icon {
            background-color: #ffffff;
            border-color: #dee2e6;
        }
        .step-active .step-icon {
            background-color: #cfe2ff;
            border-color: #0d6efd;
            color: #0d6efd; /* Active icon color */
        }
        .step-completed .step-icon {
            background-color: #d1e7dd;
            border-color: #198754;
            color: #198754; /* Completed icon color */
        }
        .step-active .step-card {
            border-color: #0d6efd;
            box-shadow: 0 0.25rem 0.75rem rgba(13, 110, 253, 0.15);
        }
        .step-completed .step-card {
            background-color: #f8f9fa;
            border-color: #198754;
        }
        .contract-summary-box { /* Custom class for the summary div */
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid #dee2e6;
        }
        .contract-summary-box .summary-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }
        .contract-summary-box .summary-item {
            margin-bottom: 0.5rem;
        }
        .contract-summary-box .summary-item .label {
            color: #6c757d;
        }
        .contract-summary-box .summary-item .value {
            text-align: right;
        }

        /* Styling for dropdown items */
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px; /* Space between icon and text in dropdown */
        }
        .dropdown-item i {
            width: 18px; /* Fixed width for icons to align text */
            text-align: center;
        }
        .card-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 8px; /* Space between main buttons and dropdown */
            justify-content: flex-end; /* Align buttons to the right */
            align-items: center;
        }
        .card-footer .btn {
            padding: 0.375rem 0.75rem; /* Adjust padding for regular buttons */
            font-size: 0.875rem; /* Smaller font for regular buttons */
        }
        /* Ensure text color for badges */
        .badge.badge-warning { /* Bootstrap 4 uses badge-warning directly */
            color: #212529 !important; /* Dark text for warning background */
        }
        .badge.badge-primary, .badge.badge-success, .badge.badge-secondary { /* Bootstrap 4 uses badge-primary directly */
            color: #fff !important; /* White text for other backgrounds */
        }

        /* Responsive adjustments */
        @media (max-width: 767px) {
            .workflow-step .step-icon {
                left: 10px; /* Adjust icon position for smaller screens */
            }
            .workflow-connector {
                left: 40px; /* Adjust connector position */
            }
            .step-card {
                margin-left: 70px; /* Adjust card margin */
            }
            .dropdown-item {
                font-size: 0.9rem; /* Adjust font size for mobile */
            }
        }
    </style>

<header class="bg-white shadow-sm mb-4">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-primary font-weight-bold">Contract Lifecycle Management</h1>
            <div>
                <small class="text-muted">Last updated: July 29, 2025</small>
            </div>
        </div>
    </div>
</header>

<div class="container mb-5">
    <div class="row">
        <!-- Workflow Column (now on left) -->
        <div class="col-lg-8">
            <div class="mb-4">
                <h2 class="h4 text-primary font-weight-bold">Contract Processing Workflow</h2>
                <p class="text-muted">Track the progress of contract development and approvals</p>
            </div>

            <!-- Step 1: Draft Contract -->
            <div class="workflow-step step-completed" data-step-id="1">
                <div class="workflow-connector"></div>
                <div class="step-icon">
                    <i class="fa fa-file-text-o"></i> <!-- Updated FA icon -->
                </div>
                <div class="card step-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">1. Draft Contract</h5>
                        <span class="badge badge-success text-white">Completed</span> <!-- Updated badge class -->
                    </div>
                    <div class="card-body">
                        <p><strong>Responsibility:</strong> SO/OCP (Senior Officer/Orders and Contracts Progression)</p>
                        <p><strong>Activity:</strong> Drafting of contract</p>
                        <p><strong>Output:</strong> Draft contract</p>
                        <p class="text-muted small mt-2">Based on approval of award, letter of offer, acceptance letter and successful bidder's bid document.</p>
                        <p class="mt-3 mb-0"><i class="fa fa-link mr-2 text-muted"></i><a href="#" class="text-primary" onclick="console.log('Opening Contract: CA/SCM/2023/001_Draft.pdf'); return false;">Contract Link: CA/SCM/2023/001_Draft.pdf</a></p>
                    </div>
                    <div class="card-footer text-right">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="#" data-action="View Draft"><i class="fa fa-eye"></i><span>View Draft</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Edit Draft"><i class="fa fa-edit"></i><span>Edit Draft</span></a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="#" data-action="Create Task"><i class="fa fa-tasks"></i><span>Create Task</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Add Comment"><i class="fa fa-comment"></i><span>Comment</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Upload Document"><i class="fa fa-upload"></i><span>Upload</span></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Head of Department Review -->
            <div class="workflow-step step-active" data-step-id="2">
                <div class="workflow-connector"></div>
                <div class="step-icon">
                    <i class="fa fa-user"></i> <!-- Updated FA icon -->
                </div>
                <div class="card step-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">2. Head of Department Review</h5>
                        <span class="badge badge-primary text-white">In Progress</span> <!-- Updated badge class -->
                    </div>
                    <div class="card-body">
                        <p><strong>Responsibility:</strong> HoD (Head of Department)</p>
                        <p><strong>Activity:</strong> Review of the draft contract</p>
                        <p><strong>Output:</strong> Reviewed draft contract</p>
                        <p class="text-muted small mt-2">HoD reviews the draft contract for departmental requirements and compliance.</p>
                        <p class="mt-3 mb-0"><i class="fa fa-link mr-2 text-muted"></i><a href="#" class="text-primary" onclick="console.log('Opening Contract: CA/SCM/2023/001_HOD_Review.pdf'); return false;">Contract Link: CA/SCM/2023/001_HOD_Review.pdf</a></p>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-outline-secondary btn-sm mr-2" onclick="returnToPrevious(2, 1)">Return for Revisions</button>
                        <button class="btn btn-primary btn-sm" onclick="completeStep(2)">Approve <i class="fa fa-arrow-right ml-1"></i></button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                <li><a class="dropdown-item" href="#" data-action="View Comments"><i class="fa fa-comments"></i><span>Comments</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Add Reminder"><i class="fa fa-bell"></i><span>Reminder</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Open Document"><i class="fa fa-folder-open"></i><span>Open Doc</span></a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="#" data-action="Mark as Reviewed"><i class="fa fa-check"></i><span>Mark Reviewed</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Request Clarification"><i class="fa fa-question-circle"></i><span>Clarify</span></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Legal Services Review -->
            <div class="workflow-step step-pending" data-step-id="3">
                <div class="workflow-connector"></div>
                <div class="step-icon">
                    <i class="fa fa-balance-scale"></i> <!-- Updated FA icon -->
                </div>
                <div class="card step-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">3. Legal Services Review</h5>
                        <span class="badge badge-secondary text-white">Pending</span> <!-- Updated badge class -->
                    </div>
                    <div class="card-body">
                        <p><strong>Responsibility:</strong> Manager Legal Services</p>
                        <p><strong>Activity:</strong> Review of the draft contract</p>
                        <p><strong>Output:</strong> Reviewed draft contract</p>
                        <p class="text-muted small mt-2">Manager Legal Services reviews the draft contract, approval of award, letter of offer, acceptance letter and successful bidder's bid document.</p>
                        <p class="mt-3 mb-0"><i class="fa fa-link mr-2 text-muted"></i><a href="#" class="text-primary" onclick="console.log('Opening Contract: CA/SCM/2023/001_Legal_Review.pdf'); return false;">Contract Link: CA/SCM/2023/001_Legal_Review.pdf</a></p>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-success btn-sm mr-2" onclick="completeStep(3)">Start Review <i class="fa fa-play ml-1"></i></button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton3" data-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                <li><a class="dropdown-item" href="#" data-action="Add Reminder"><i class="fa fa-bell"></i><span>Reminder</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Add Comment"><i class="fa fa-comment"></i><span>Comment</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Open Document"><i class="fa fa-folder-open"></i><span>Open Doc</span></a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="#" data-action="Request Legal Opinion"><i class="fa fa-balance-scale"></i><span>Legal Opinion</span></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Supplier Review -->
            <div class="workflow-step step-pending" data-step-id="4">
                <div class="workflow-connector"></div>
                <div class="step-icon">
                    <i class="fa fa-handshake-o"></i> <!-- Updated FA icon -->
                </div>
                <div class="card step-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">4. Supplier Review</h5>
                        <span class="badge badge-secondary text-white">Pending</span> <!-- Updated badge class -->
                    </div>
                    <div class="card-body">
                        <p><strong>Responsibility:</strong> Supplier</p>
                        <p><strong>Activity:</strong> Review of the contract</p>
                        <p><strong>Output:</strong> Reviewed draft contract</p>
                        <p class="text-muted small mt-2">Supplier reviews the contract terms and conditions before final approval.</p>
                        <p class="mt-3 mb-0"><i class="fa fa-link mr-2 text-muted"></i><a href="#" class="text-primary" onclick="console.log('Opening Contract: CA/SCM/2023/001_Supplier_Review.pdf'); return false;">Contract Link: CA/SCM/2023/001_Supplier_Review.pdf</a></p>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-primary btn-sm mr-2" onclick="completeStep(4)">Send Contract <i class="fa fa-paper-plane ml-1"></i></button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton4" data-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton4">
                                <li><a class="dropdown-item" href="#" data-action="Add Reminder"><i class="fa fa-bell"></i><span>Reminder</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Add Comment"><i class="fa fa-comment"></i><span>Comment</span></a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="#" data-action="Upload Signed Document"><i class="fa fa-upload"></i><span>Upload Signed</span></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: DG Approval -->
            <div class="workflow-step step-pending" data-step-id="5">
                <div class="step-icon">
                    <i class="fa fa-file-text-o"></i> <!-- Updated FA icon -->
                </div>
                <div class="card step-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">5. DG Approval</h5>
                        <span class="badge badge-secondary text-white">Pending</span> <!-- Updated badge class -->
                    </div>
                    <div class="card-body">
                        <p><strong>Responsibility:</strong> DG (Director General)</p>
                        <p><strong>Activity:</strong> Approving of contract</p>
                        <p><strong>Output:</strong> Approved contract</p>
                        <p class="text-muted small mt-2">DG approves contract with approval of award, draft contract, performance bond where applicable.</p>
                        <p class="mt-3 mb-0"><i class="fa fa-link mr-2 text-muted"></i><a href="#" class="text-primary" onclick="console.log('Opening Contract: CA/SCM/2023/001_Approved.pdf'); return false;">Contract Link: CA/SCM/2023/001_Approved.pdf</a></p>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-success btn-sm mr-2" onclick="completeStep(5)">Request Approval <i class="fa fa-user-check ml-1"></i></button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton5" data-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton5">
                                <li><a class="dropdown-item" href="#" data-action="Add Reminder"><i class="fa fa-bell"></i><span>Reminder</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Add Comment"><i class="fa fa-comment"></i><span>Comment</span></a></li>
                                <li><a class="dropdown-item" href="#" data-action="Open Document"><i class="fa fa-folder-open"></i><span>Open Doc</span></a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="#" data-action="Finalize Contract"><i class="fa fa-certificate"></i><span>Finalize</span></a></li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contract Summary Column (now on right) -->
        <div class="col-lg-4 mb-4">
            <div class="contract-summary-box">
                <div class="summary-header">
                    <h5 class="mb-0 text-primary font-weight-bold">Contract Summary</h5>
                    <small class="text-muted">Contract reference: CA/SCM/2023/001</small>
                </div>
                <div class="summary-content">
                    <div class="row summary-item">
                        <div class="col-6 label">Title:</div>
                        <div class="col-6 value">Software Service Agreement</div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Supplier:</div>
                        <div class="col-6 value">TechSolutions Ltd.</div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Value:</div>
                        <div class="col-6 value">Kes 125,000.00</div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Duration:</div>
                        <div class="col-6 value">12 months</div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Start Date:</div>
                        <div class="col-6 value">2023-07-01</div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">End Date:</div>
                        <div class="col-6 value">2024-06-30</div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Status:</div>
                        <div class="col-6 value">In Progress</div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Remaining Days:</div>
                        <div class="col-6 value">336 days</div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Surety Bond Status:</div>
                        <div class="col-6 value"><span class="badge badge-success">Active</span></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Surety Bond Expiry:</div>
                        <div class="col-6 value">2024-12-31</div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Workflow Progress</span>
                            <span id="workflow-progress-text">20%</span>
                        </div>
                        <div class="progress" style="height: 10px;" title="Current step: Draft contract review by Head of Department">
                            <div id="workflow-progress-bar" class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white py-3 border-top">
    <div class="container text-center">
        <p class="text-muted mb-0">© 2025 Contract Authority Management System</p>
    </div>
</footer>

<script>
    // Function to create dropdown menu items dynamically
    function createDropdownItems(stepId, actions) {
        let itemsHtml = '';
        actions.forEach(action => {
            if (action === 'divider') {
                itemsHtml += `<div class="dropdown-divider"></div>`; // Bootstrap 4 divider
            } else {
                itemsHtml += `
                    <li><a class="dropdown-item" href="#" data-action="jQuery{action.dataAction}" title="jQuery{action.title}">
                        <i class="jQuery{action.iconClass}"></i><span>jQuery{action.label}</span>
                    </a></li>
                `;
            }
        });
        return itemsHtml;
    }

    // Define common icon actions for various steps
    const commonActionsDropdown = [
        { dataAction: "Create Task", title: "Create Task", iconClass: "fa fa-tasks", label: "Create Task" },
        { dataAction: "Add Reminder", title: "Add Reminder", iconClass: "fa fa-bell", label: "Reminder" },
        { dataAction: "Add Comment", title: "Add Comment", iconClass: "fa fa-comment", label: "Comment" },
        { dataAction: "Open Document", title: "Open Document", iconClass: "fa fa-folder-open", label: "Open Doc" },
    ];

    // Specific dropdown actions for each step - Updated Font Awesome 4.x icons
    const stepDropdownActions = {
        1: [ // Draft Contract
            { dataAction: "View Draft", title: "View Draft", iconClass: "fa fa-eye", label: "View Draft" },
            { dataAction: "Edit Draft", title: "Edit Draft", iconClass: "fa fa-edit", label: "Edit Draft" },
            'divider',
            ...commonActionsDropdown,
            { dataAction: "Upload Document", title: "Upload Document", iconClass: "fa fa-upload", label: "Upload" }
        ],
        2: [ // HOD Review
            { dataAction: "View Comments", title: "View Comments", iconClass: "fa fa-comments", label: "Comments" },
            ...commonActionsDropdown,
            'divider',
            { dataAction: "Mark as Reviewed", title: "Mark as Reviewed", iconClass: "fa fa-check", label: "Mark Reviewed" },
            { dataAction: "Request Clarification", title: "Request Clarification", iconClass: "fa fa-question-circle", label: "Clarify" }
        ],
        3: [ // Legal Review
            ...commonActionsDropdown,
            'divider',
            { dataAction: "Request Legal Opinion", title: "Request Legal Opinion", iconClass: "fa fa-balance-scale", label: "Legal Opinion" }
        ],
        4: [ // Supplier Review
            ...commonActionsDropdown.filter(action => action.dataAction !== 'Open Document'), // Supplier might not open internal docs
            'divider',
            { dataAction: "Upload Signed Document", title: "Upload Signed Document", iconClass: "fa fa-upload", label: "Upload Signed" } /* Using fa-upload as fa-file-signature is FA5+ */
        ],
        5: [ // DG Approval
            ...commonActionsDropdown,
            'divider',
            { dataAction: "Finalize Contract", title: "Finalize Contract", iconClass: "fa fa-certificate", label: "Finalize" } /* Using fa-certificate as fa-stamp is FA5+ */
        ]
    };

    // Simple JavaScript to handle workflow steps
    function completeStep(stepId) {
        const currentStepElement = document.querySelector(`.workflow-step[data-step-id="jQuery{stepId}"]`);
        const nextStepElement = document.querySelector(`.workflow-step[data-step-id="jQuery{stepId + 1}"]`);

        // Mark current step as completed
        if (currentStepElement) {
            currentStepElement.classList.remove('step-active');
            currentStepElement.classList.add('step-completed');
            currentStepElement.querySelector('.badge').className = 'badge badge-success text-white'; // Bootstrap 4 badge class
            currentStepElement.querySelector('.badge').textContent = 'Completed';
            currentStepElement.querySelector('.step-icon i').className = currentStepElement.querySelector('.step-icon i').className.replace('text-primary', 'text-success');

            // Update dropdown for completed step
            const footer = currentStepElement.querySelector('.card-footer');
            if (footer) {
                // Keep only the dropdown
                footer.innerHTML = `
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButtonjQuery{stepId}" data-toggle="dropdown" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonjQuery{stepId}">
                            jQuery{createDropdownItems(stepId, stepDropdownActions[stepId])}
                        </div>
                    </div>
                `;
                // Re-attach event listeners for dropdown items
                footer.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', handleDropdownItemClick);
                });
            }
        }


        // Activate next step
        if (nextStepElement) {
            nextStepElement.classList.remove('step-pending');
            nextStepElement.classList.add('step-active');

            // Update badge
            const nextBadge = nextStepElement.querySelector('.badge');
            nextBadge.className = 'badge badge-primary text-white'; // Bootstrap 4 badge class
            nextBadge.textContent = 'In Progress';

            // Add icon color
            const nextIcon = nextStepElement.querySelector('.step-icon i');
            nextIcon.classList.add('text-primary');

            // Add action buttons to next step if it doesn't have them or needs updates
            const nextFooter = nextStepElement.querySelector('.card-footer');
            if (nextFooter) {
                // Clear existing buttons and add relevant ones
                nextFooter.innerHTML = ''; // Clear existing content

                if (stepId + 1 === 2) { // HOD Review
                    nextFooter.innerHTML += `<button class="btn btn-outline-secondary btn-sm mr-2" onclick="returnToPrevious(jQuery{stepId + 1}, jQuery{stepId})">Return for Revisions</button>`;
                    nextFooter.innerHTML += `<button class="btn btn-primary btn-sm" onclick="completeStep(jQuery{stepId + 1})">Approve <i class="fa fa-arrow-right ml-1"></i></button>`;
                } else if (stepId + 1 === 3) { // Legal Review
                    nextFooter.innerHTML += `<button class="btn btn-success btn-sm mr-2" onclick="completeStep(jQuery{stepId + 1})">Start Review <i class="fa fa-play ml-1"></i></button>`;
                } else if (stepId + 1 === 4) { // Supplier Review
                    nextFooter.innerHTML += `<button class="btn btn-primary btn-sm mr-2" onclick="completeStep(jQuery{stepId + 1})">Send Contract <i class="fa fa-paper-plane ml-1"></i></button>`;
                } else if (stepId + 1 === 5) { // DG Approval
                    nextFooter.innerHTML += `<button class="btn btn-success btn-sm mr-2" onclick="completeStep(jQuery{stepId + 1})">Request Approval <i class="fa fa-user-check ml-1"></i></button>`;
                }

                nextFooter.innerHTML += `
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButtonjQuery{stepId + 1}" data-toggle="dropdown" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonjQuery{stepId + 1}">
                            jQuery{createDropdownItems(stepId + 1, stepDropdownActions[stepId + 1])}
                        </div>
                    </div>
                `;
                // Re-attach event listeners for all new buttons and dropdown items
                nextFooter.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', handleDropdownItemClick);
                });
            }


            // Update progress bar
            const progressPercentage = (stepId) * 20;
            document.getElementById('workflow-progress-bar').style.width = progressPercentage + '%';
            document.getElementById('workflow-progress-bar').setAttribute('aria-valuenow', progressPercentage);
            document.getElementById('workflow-progress-bar').parentNode.title = 'Current step: ' + nextStepElement.querySelector('.card-title').textContent;

            // Update progress text
            document.getElementById('workflow-progress-text').textContent = progressPercentage + '%';

        } else {
            // Workflow completed
            document.getElementById('workflow-progress-bar').style.width = '100%';
            document.getElementById('workflow-progress-bar').setAttribute('aria-valuenow', 100);
            document.getElementById('workflow-progress-text').textContent = '100%';


            // Create completion message
            const workflowColumn = document.querySelector('.col-lg-8');
            const completionCard = document.createElement('div');
            completionCard.className = 'card bg-success text-white mt-4 rounded-lg shadow-sm';
            completionCard.innerHTML = `
                    <div class="card-body text-center">
                        <i class="fa fa-check-circle fa-4x mb-3"></i>
                        <h3>Contract Process Complete</h3>
                        <p>The contract has been fully approved and is ready for execution</p>
                    </div>
                `;
            workflowColumn.appendChild(completionCard);
        }
    }

    function returnToPrevious(currentStepId, targetStepId) {
        const currentStepElement = document.querySelector(`.workflow-step[data-step-id="jQuery{currentStepId}"]`);
        const targetStepElement = document.querySelector(`.workflow-step[data-step-id="jQuery{targetStepId}"]`);

        // Mark current step as pending
        if (currentStepElement) {
            currentStepElement.classList.remove('step-active');
            currentStepElement.classList.add('step-pending');
            currentStepElement.querySelector('.badge').className = 'badge badge-secondary text-white'; // Bootstrap 4 badge class
            currentStepElement.querySelector('.badge').textContent = 'Pending';
            currentStepElement.querySelector('.step-icon i').classList.remove('text-primary');

            // Update dropdown for current step
            const footer = currentStepElement.querySelector('.card-footer');
            if (footer) {
                // Keep only the dropdown
                footer.innerHTML = `
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButtonjQuery{currentStepId}" data-toggle="dropdown" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonjQuery{currentStepId}">
                            jQuery{createDropdownItems(currentStepId, stepDropdownActions[currentStepId])}
                        </div>
                    </div>
                `;
                footer.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', handleDropdownItemClick);
                });
            }
        }

        // Mark target step as active
        if (targetStepElement) {
            targetStepElement.classList.remove('step-completed');
            targetStepElement.classList.add('step-active');
            targetStepElement.querySelector('.badge').className = 'badge badge-primary text-white'; // Bootstrap 4 badge class
            targetStepElement.querySelector('.badge').textContent = 'In Progress';
            targetStepElement.querySelector('.step-icon i').classList.remove('text-success');
            targetStepElement.querySelector('.step-icon i').classList.add('text-primary');

            // Re-add action buttons to target step
            const targetFooter = targetStepElement.querySelector('.card-footer');
            if (targetFooter) {
                targetFooter.innerHTML = ''; // Clear existing content
                if (targetStepId === 1) { // Draft Contract
                    // No main action buttons, just dropdown
                } else if (targetStepId === 2) { // HOD Review
                    targetFooter.innerHTML += `<button class="btn btn-outline-secondary btn-sm mr-2" onclick="returnToPrevious(jQuery{targetStepId}, jQuery{targetStepId - 1})">Return for Revisions</button>`;
                    targetFooter.innerHTML += `<button class="btn btn-primary btn-sm" onclick="completeStep(jQuery{targetStepId})">Approve <i class="fa fa-arrow-right ml-1"></i></button>`;
                }
                targetFooter.innerHTML += `
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButtonjQuery{targetStepId}" data-toggle="dropdown" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonjQuery{targetStepId}">
                            jQuery{createDropdownItems(targetStepId, stepDropdownActions[targetStepId])}
                        </div>
                    </div>
                `;
                // Re-attach event listeners for all new buttons and dropdown items
                targetFooter.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', handleDropdownItemClick);
                });
            }
        }

        // Update progress bar
        const progressPercentage = (targetStepId - 1) * 20;
        document.getElementById('workflow-progress-bar').style.width = progressPercentage + '%';
        document.getElementById('workflow-progress-bar').setAttribute('aria-valuenow', progressPercentage);
        document.getElementById('workflow-progress-bar').parentNode.title = 'Current step: ' + targetStepElement.querySelector('.card-title').textContent;

        // Update progress text
        document.getElementById('workflow-progress-text').textContent = progressPercentage + '%';
    }

    // Handle clicks on dropdown items
    function handleDropdownItemClick(event) {
        event.preventDefault(); // Prevent default link behavior
        const button = event.currentTarget;
        const stepElement = button.closest('.workflow-step');
        const stepTitle = stepElement ? stepElement.querySelector('.card-title').textContent : 'Unknown Step';
        const action = button.dataset.action;
        console.log(`Action "jQuery{action}" initiated for step: "jQuery{stepTitle}"`);
        // In a real application, you would implement specific logic here
        // e.g., open a modal, trigger an API call, navigate to a different view
    }

    // Attach event listeners to initial dropdown items (for step 1)
    // This needs to be done after the DOM is fully loaded and before any dynamic content changes
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', handleDropdownItemClick);
        });
    });

</script>