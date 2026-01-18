<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Workflow Builder</title>
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" xintegrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <!-- Font Awesome 4.7 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .workflow-config-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 30px;
        }
        .configured-step {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fefefe;
            position: relative;
        }
        .configured-step .step-actions {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .configured-step .step-actions .btn {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dropdown-item i {
            width: 18px;
            text-align: center;
        }
        .form-group label {
            font-weight: 500;
        }
        .form-control {
            border-radius: 0.25rem;
        }
        .btn {
            border-radius: 0.25rem;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .badge {
            font-size: 80%;
            font-weight: 600;
        }
    </style>
</head>
<body>
<header class="bg-white shadow-sm mb-4">
    <div class="container py-3">
        <h1 class="h3 mb-0 text-primary font-weight-bold">Contract Workflow Builder</h1>
    </div>
</header>

<div class="container mb-5">
    <div class="row">
        <!-- Left Column: Add New Step Form -->
        <div class="col-lg-5">
            <div class="workflow-config-section">
                <h4 class="mb-4 text-primary">Add New Workflow Step</h4>
                <form id="addStepForm">
                    <div class="form-group">
                        <label for="stepTitle">Step Title:</label>
                        <input type="text" class="form-control" id="stepTitle" placeholder="e.g., Draft Contract" required>
                    </div>
                    <div class="form-group">
                        <label for="stepResponsibility">Responsible User/Role:</label>
                        <input type="text" class="form-control" id="stepResponsibility" placeholder="e.g., SO/OCP, Legal Team" required>
                    </div>
                    <div class="form-group">
                        <label for="stepActivity">Activity:</label>
                        <textarea class="form-control" id="stepActivity" rows="2" placeholder="e.g., Drafting of contract based on bid documents"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="stepOutput">Output:</label>
                        <input type="text" class="form-control" id="stepOutput" placeholder="e.g., Draft contract">
                    </div>
                    <div class="form-group">
                        <label for="stepContractLink">Contract Link (Placeholder):</label>
                        <input type="text" class="form-control" id="stepContractLink" placeholder="e.g., CA/SCM/2023/001_Draft.pdf">
                    </div>

                    <h5 class="mt-4 mb-3 text-secondary">Configure Action Buttons (Dropdown)</h5>
                    <div id="actionButtonsContainer">
                        <!-- Dynamic action button inputs will be added here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-info mb-3" onclick="addActionButtonInput()">
                        <i class="fa fa-plus-circle"></i> Add Action Button
                    </button>

                    <button type="submit" class="btn btn-primary btn-block">Add Step</button>
                </form>
            </div>
        </div>

        <!-- Right Column: Configured Workflow Steps & Export -->
        <div class="col-lg-7">
            <div class="workflow-config-section">
                <h4 class="mb-4 text-primary">Configured Workflow Steps</h4>
                <div id="configuredWorkflowDisplay">
                    <p class="text-muted text-center" id="noStepsMessage">No steps configured yet. Add a step using the form on the left.</p>
                </div>
                <hr>
                <h4 class="mt-4 mb-3 text-primary">Export Configuration (JSON)</h4>
                <div class="form-group">
                    <textarea class="form-control" id="exportConfig" rows="10" readonly></textarea>
                </div>
                <button type="button" class="btn btn-info btn-block" onclick="copyConfigToClipboard()">
                    <i class="fa fa-copy"></i> Copy to Clipboard
                </button>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white py-3 border-top">
    <div class="container text-center">
        <p class="text-muted mb-0">© 2025 Contract Authority Management System</p>
    </div>
</footer>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" xintegrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" xintegrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" xintegrity="sha384-7ymO4jmHFVCAtgffC/zYnPYxS0Wk4PLsXyGxLzZcBUAD5zQ9QJzGgLwP4TzWzC" crossorigin="anonymous"></script>

<script>
    let workflowConfig = []; // Array to store all configured workflow steps

    // Predefined Font Awesome 4.7 icons for selection
    const faIcons = [
        "fa-eye", "fa-edit", "fa-tasks", "fa-bell", "fa-comment", "fa-folder-open",
        "fa-upload", "fa-check", "fa-question-circle", "fa-balance-scale", "fa-paper-plane",
        "fa-file-text-o", "fa-user", "fa-gavel", "fa-handshake-o", "fa-certificate", "fa-play", "fa-user-check"
    ];

    // Function to add input fields for a new action button
    function addActionButtonInput(initialLabel = '', initialIcon = '') {
        const container = document.getElementById('actionButtonsContainer');
        const newIndex = container.children.length; // Get current number of action buttons

        const actionGroup = document.createElement('div');
        actionGroup.className = 'form-row mb-2 align-items-end';
        actionGroup.innerHTML = `
            <div class="col-5">
                <label for="actionLabel${newIndex}">Label:</label>
                <input type="text" class="form-control form-control-sm action-label" id="actionLabel${newIndex}" placeholder="e.g., View Draft" value="${initialLabel}" required>
            </div>
            <div class="col-5">
                <label for="actionIcon${newIndex}">Icon (fa-):</label>
                <select class="form-control form-control-sm action-icon" id="actionIcon${newIndex}" required>
                    <option value="">Select Icon</option>
                    ${faIcons.map(icon => `<option value="${icon}" ${initialIcon === icon ? 'selected' : ''}>${icon}</option>`).join('')}
                </select>
            </div>
            <div class="col-2 text-right">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeActionButtonInput(this)">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(actionGroup);
    }

    // Function to remove action button input fields
    function removeActionButtonInput(button) {
        button.closest('.form-row').remove();
    }

    // Function to handle adding a new step
    document.getElementById('addStepForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const stepTitle = document.getElementById('stepTitle').value;
        const stepResponsibility = document.getElementById('stepResponsibility').value;
        const stepActivity = document.getElementById('stepActivity').value;
        const stepOutput = document.getElementById('stepOutput').value;
        const stepContractLink = document.getElementById('stepContractLink').value;

        const actionButtons = [];
        document.querySelectorAll('.action-label').forEach((labelInput, index) => {
            const iconSelect = document.querySelectorAll('.action-icon')[index];
            if (labelInput.value && iconSelect.value) {
                actionButtons.push({
                    label: labelInput.value,
                    iconClass: iconSelect.value,
                    dataAction: labelInput.value.replace(/\s+/g, '') // Simple data-action from label
                });
            }
        });

        const newStep = {
            id: workflowConfig.length + 1,
            title: stepTitle,
            responsibility: stepResponsibility,
            activity: stepActivity,
            output: stepOutput,
            contractLink: stepContractLink,
            actions: actionButtons
        };

        workflowConfig.push(newStep);
        renderWorkflowConfig();
        updateExportConfig();
        this.reset(); // Clear the form
        document.getElementById('actionButtonsContainer').innerHTML = ''; // Clear action button inputs
    });

    // Function to render the configured workflow steps
    function renderWorkflowConfig() {
        const displayContainer = document.getElementById('configuredWorkflowDisplay');
        displayContainer.innerHTML = ''; // Clear previous display

        if (workflowConfig.length === 0) {
            displayContainer.innerHTML = '<p class="text-muted text-center" id="noStepsMessage">No steps configured yet. Add a step using the form on the left.</p>';
            return;
        }

        document.getElementById('noStepsMessage')?.remove(); // Remove "No steps" message if present

        workflowConfig.forEach(step => {
            const stepDiv = document.createElement('div');
            stepDiv.className = 'configured-step';
            stepDiv.setAttribute('data-step-id', step.id);

            let actionsHtml = '';
            if (step.actions && step.actions.length > 0) {
                actionsHtml = `
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="stepActionsDropdown${step.id}" data-toggle="dropdown" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="stepActionsDropdown${step.id}">
                            ${step.actions.map(action => `
                                <li><a class="dropdown-item" href="#" data-action="${action.dataAction}" title="${action.label}">
                                    <i class="${action.iconClass}"></i><span>${action.label}</span>
                                </a></li>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            stepDiv.innerHTML = `
                <div class="step-actions">
                    <button class="btn btn-sm btn-info mr-2" onclick="editStep(${step.id})"><i class="fa fa-pencil"></i> Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteStep(${step.id})"><i class="fa fa-trash"></i> Delete</button>
                </div>
                <h5>Step ${step.id}: ${step.title}</h5>
                <p class="mb-1"><strong>Responsibility:</strong> ${step.responsibility}</p>
                <p class="mb-1"><strong>Activity:</strong> ${step.activity || 'N/A'}</p>
                <p class="mb-1"><strong>Output:</strong> ${step.output || 'N/A'}</p>
                ${step.contractLink ? `<p class="mb-1"><i class="fa fa-link mr-1 text-muted"></i> Contract Link: <a href="#" onclick="console.log('Opening ${step.contractLink}'); return false;">${step.contractLink}</a></p>` : ''}
                <div class="mt-3">
                    ${actionsHtml}
                </div>
            `;
            displayContainer.appendChild(stepDiv);
        });

        // Re-initialize Bootstrap dropdowns for newly added elements
        $('[data-toggle="dropdown"]').dropdown();

        // Re-attach event listeners for dropdown items
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', handleDropdownItemClick);
        });
    }

    // Function to edit a step
    function editStep(stepId) {
        const stepToEdit = workflowConfig.find(step => step.id === stepId);
        if (!stepToEdit) return;

        // Populate the form with current step data
        document.getElementById('stepTitle').value = stepToEdit.title;
        document.getElementById('stepResponsibility').value = stepToEdit.responsibility;
        document.getElementById('stepActivity').value = stepToEdit.activity;
        document.getElementById('stepOutput').value = stepToEdit.output;
        document.getElementById('stepContractLink').value = stepToEdit.contractLink;

        // Clear existing action button inputs and add current ones
        const actionButtonsContainer = document.getElementById('actionButtonsContainer');
        actionButtonsContainer.innerHTML = '';
        stepToEdit.actions.forEach(action => {
            addActionButtonInput(action.label, action.iconClass);
        });

        // Change form submit button to "Update Step"
        const addStepForm = document.getElementById('addStepForm');
        const submitButton = addStepForm.querySelector('button[type="submit"]');
        submitButton.textContent = 'Update Step';
        submitButton.onclick = function(event) {
            event.preventDefault();
            updateStep(stepId);
        };

        // Scroll to form
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Function to update an existing step
    function updateStep(stepId) {
        const stepIndex = workflowConfig.findIndex(step => step.id === stepId);
        if (stepIndex === -1) return;

        const updatedStep = {
            id: stepId,
            title: document.getElementById('stepTitle').value,
            responsibility: document.getElementById('stepResponsibility').value,
            activity: document.getElementById('stepActivity').value,
            output: document.getElementById('stepOutput').value,
            contractLink: document.getElementById('stepContractLink').value,
            actions: []
        };

        document.querySelectorAll('.action-label').forEach((labelInput, index) => {
            const iconSelect = document.querySelectorAll('.action-icon')[index];
            if (labelInput.value && iconSelect.value) {
                updatedStep.actions.push({
                    label: labelInput.value,
                    iconClass: iconSelect.value,
                    dataAction: labelInput.value.replace(/\s+/g, '')
                });
            }
        });

        workflowConfig[stepIndex] = updatedStep;
        renderWorkflowConfig();
        updateExportConfig();

        // Reset form and submit button
        document.getElementById('addStepForm').reset();
        document.getElementById('actionButtonsContainer').innerHTML = '';
        const submitButton = document.getElementById('addStepForm').querySelector('button[type="submit"]');
        submitButton.textContent = 'Add Step';
        submitButton.onclick = null; // Remove custom click handler
    }

    // Function to delete a step
    function deleteStep(stepId) {
        if (confirm('Are you sure you want to delete this step?')) {
            workflowConfig = workflowConfig.filter(step => step.id !== stepId);
            // Re-index IDs to maintain sequential order (optional, but good for display)
            workflowConfig.forEach((step, index) => step.id = index + 1);
            renderWorkflowConfig();
            updateExportConfig();
        }
    }

    // Function to handle clicks on dropdown items (for console logging)
    function handleDropdownItemClick(event) {
        event.preventDefault();
        const button = event.currentTarget;
        const stepElement = button.closest('.configured-step');
        const stepTitle = stepElement ? stepElement.querySelector('h5').textContent : 'Unknown Step';
        const action = button.dataset.action;
        console.log(`Action "${action}" initiated for step: "${stepTitle}"`);
    }

    // Function to update the JSON export textarea
    function updateExportConfig() {
        document.getElementById('exportConfig').value = JSON.stringify(workflowConfig, null, 2);
    }

    // Function to copy JSON to clipboard
    function copyConfigToClipboard() {
        const exportTextArea = document.getElementById('exportConfig');
        exportTextArea.select();
        document.execCommand('copy'); // Deprecated but widely supported for iframes
        alert('Workflow configuration copied to clipboard!'); // Using alert for simplicity in this context
    }

    // Initial render and setup
    document.addEventListener('DOMContentLoaded', function() {
        renderWorkflowConfig();
        updateExportConfig();
    });

</script>
</body>
</html>
