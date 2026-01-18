<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" xintegrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        /* Changed .container to .container-fluid for full width */
        .container-fluid {
            padding-left: 15px; /* Default Bootstrap padding for fluid container */
            padding-right: 15px; /* Default Bootstrap padding for fluid container */
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
        .checklist-item-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .checklist-item-row input {
            flex-grow: 1;
            margin-right: 10px;
        }
        .configured-step .checklist-items ul {
            list-style: none;
            padding-left: 0;
            margin-top: 10px;
        }
        .configured-step .checklist-items li {
            margin-bottom: 5px;
            color: #555;
        }
        .configured-step .checklist-items li i {
            margin-right: 5px;
            color: #28a745; /* Green checkmark */
        }
        .step-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer; /* Indicate clickable area */
            padding-bottom: 10px; /* Space below header */
            border-bottom: 1px solid #eee; /* Separator */
            margin-bottom: 10px; /* Space above content */
        }
        .step-header h5 {
            margin-bottom: 0;
            flex-grow: 1;
        }
        .step-header .collapse-toggle-icon {
            margin-left: 15px;
            transition: transform 0.3s ease;
        }
        /* Rotate icon when collapsed */
        .step-header .collapse-toggle-icon.collapsed {
            transform: rotate(-90deg);
        }
        .step-footer {
            display: flex;
            justify-content: flex-end; /* Align buttons to the right */
            gap: 10px; /* Space between buttons */
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        /* Ensure Font Awesome icons are rendered correctly */
        .fa {
            font-family: 'FontAwesome';
        }
    </style>

<header class="bg-white shadow-sm mb-4">
    <div class="container-fluid py-3">
        <h1 class="h3 mb-0 text-primary font-weight-bold">Contract Workflow Builder</h1>
    </div>
</header>

<div class="container-fluid mb-5">
    <div class="row">
        <!-- Left Column: Add New Step Form (col-lg-8) -->
        <div class="col-lg-8">
            <div class="workflow-config-section">
                <h4 class="mb-4 text-primary">Add New Workflow Step</h4>
                <form id="addStepForm">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="stepTitle">Step Title:</label>
                            <input type="text" class="form-control" id="stepTitle" placeholder="e.g., Draft Contract" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stepResponsibility">Responsible User/Role:</label>
                            <input type="text" class="form-control" id="stepResponsibility" placeholder="e.g., SO/OCP, Legal Team" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="stepIcon">Step Icon:</label>
                            <select class="form-control" id="stepIcon" required>
                                <option value="">Select Icon for Step</option>
                                <!-- Icons will be populated by JS -->
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stepContractLink">Contract Link (Placeholder):</label>
                            <input type="text" class="form-control" id="stepContractLink" placeholder="e.g., CA/SCM/2023/001_Draft.pdf">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="stepActivity">Activity:</label>
                        <textarea class="form-control" id="stepActivity" rows="2" placeholder="e.g., Drafting of contract based on bid documents"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="stepOutput">Output:</label>
                        <input type="text" class="form-control" id="stepOutput" placeholder="e.g., Draft contract">
                    </div>

                    <h5 class="mt-4 mb-3 text-secondary">Transition Checklist Items</h5>
                    <div id="checklistItemsContainer">
                        <!-- Dynamic checklist item inputs will be added here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-info mb-3" onclick="addChecklistItemInput()">
                        <i class="fa fa-plus-circle"></i> Add Checklist Item
                    </button>

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

        <!-- Right Column: Configured Workflow Steps & Export (col-lg-4) -->
        <div class="col-lg-4">
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
    <div class="container-fluid text-center">
        <p class="text-muted mb-0">© 2025 Contract Authority Management System</p>
    </div>
</footer>

<script>
    let workflowConfig = []; // Array to store all configured workflow steps

    // Predefined Font Awesome 4.7 icons for selection (expanded list)
    const faIcons = [
        "fa-file-text-o", "fa-user", "fa-gavel", "fa-handshake-o", "fa-file-signature", // General step icons
        "fa-eye", "fa-edit", "fa-tasks", "fa-bell", "fa-comment", "fa-folder-open",
        "fa-upload", "fa-check", "fa-question-circle", "fa-balance-scale", "fa-paper-plane",
        "fa-play", "fa-user-check", "fa-search-plus", "fa-lightbulb-o", "fa-flag", "fa-money", "fa-times-circle",
        "fa-chevron-down", "fa-chevron-right" // For the collapse toggle
    ];

    // Predefined functions with their default labels and Font Awesome 4.7 icons
    const predefinedFunctions = [
        { name: "editDraft", label: "Edit Draft", iconClass: "fa fa-edit" },
        { name: "reviewDraft", label: "Review Draft", iconClass: "fa fa-search-plus" },
        { name: "addTask", label: "Add Task", iconClass: "fa fa-tasks" },
        { name: "addReminder", label: "Add Reminder", iconClass: "fa fa-bell" },
        { name: "addComment", label: "Add Comment", iconClass: "fa fa-comment" },
        { name: "uploadFile", label: "Upload File", iconClass: "fa fa-upload" },
        { name: "requestClarification", label: "Request Clarification", iconClass: "fa fa-question-circle" },
        { name: "clarify", label: "Clarify", iconClass: "fa fa-lightbulb-o" },
        { name: "legalOpinion", label: "Legal Opinion", iconClass: "fa fa-gavel" },
        { name: "addMilestone", label: "Add Milestone", iconClass: "fa fa-flag" },
        { name: "addSurety", label: "Add Surety", iconClass: "fa fa-money" },
        { name: "finalize", label: "Finalize Contract", iconClass: "fa fa-check-circle" }
    ];

    // Function to populate the stepIcon dropdown
    function populateStepIconDropdown() {
        const stepIconSelect = document.getElementById('stepIcon');
        faIcons.forEach(icon => {
            const option = document.createElement('option');
            option.value = icon;
            option.textContent = icon;
            stepIconSelect.appendChild(option);
        });
    }

    // Function to add input fields for a new action button
    function addActionButtonInput(initialLabel = '', initialIcon = '', initialFunction = '') {
        const container = document.getElementById('actionButtonsContainer');
        const newIndex = container.children.length;

        const actionGroup = document.createElement('div');
        actionGroup.className = 'form-row mb-2 align-items-end';
        actionGroup.innerHTML = `
            <div class="col-4">
                <label for="actionFunction${newIndex}">Function:</label>
                <select class="form-control form-control-sm action-function" id="actionFunction${newIndex}" onchange="populateActionFields(this, ${newIndex})">
                    <option value="">Select Function</option>
                    ${predefinedFunctions.map(func => `<option value="${func.name}" ${initialFunction === func.name ? 'selected' : ''}>${func.name}</option>`).join('')}
                </select>
            </div>
            <div class="col-4">
                <label for="actionLabel${newIndex}">Label:</label>
                <input type="text" class="form-control form-control-sm action-label" id="actionLabel${newIndex}" placeholder="e.g., View Draft" value="${initialLabel}" required>
            </div>
            <div class="col-3">
                <label for="actionIcon${newIndex}">Icon (fa-):</label>
                <select class="form-control form-control-sm action-icon" id="actionIcon${newIndex}" required>
                    <option value="">Select Icon</option>
                    ${faIcons.map(icon => `<option value="${icon}" ${initialIcon === icon ? 'selected' : ''}>${icon}</option>`).join('')}
                </select>
            </div>
            <div class="col-1 text-right">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeActionButtonInput(this)">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(actionGroup);
    }

    // Function to populate action label and icon based on selected function
    function populateActionFields(selectElement, index) {
        const selectedFunctionName = selectElement.value;
        const selectedFunction = predefinedFunctions.find(func => func.name === selectedFunctionName);

        const labelInput = document.getElementById(`actionLabel${index}`);
        const iconSelect = document.getElementById(`actionIcon${index}`);

        if (selectedFunction) {
            labelInput.value = selectedFunction.label;
            iconSelect.value = selectedFunction.iconClass;
        } else {
            labelInput.value = '';
            iconSelect.value = '';
        }
    }

    // Function to remove action button input fields
    function removeActionButtonInput(button) {
        button.closest('.form-row').remove();
    }

    // Function to add input fields for a new checklist item
    function addChecklistItemInput(initialText = '') {
        const container = document.getElementById('checklistItemsContainer');
        const newIndex = container.children.length;

        const checklistGroup = document.createElement('div');
        checklistGroup.className = 'checklist-item-row mb-2';
        checklistGroup.innerHTML = `
            <input type="text" class="form-control form-control-sm checklist-item-text" id="checklistItem${newIndex}" placeholder="e.g., All required documents submitted" value="${initialText}" required>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeChecklistItemInput(this)">
                <i class="fa fa-times"></i>
            </button>
        `;
        container.appendChild(checklistGroup);
    }

    // Function to remove checklist item input fields
    function removeChecklistItemInput(button) {
        button.closest('.checklist-item-row').remove();
    }

    // Function to handle adding a new step
    document.getElementById('addStepForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const stepTitle = document.getElementById('stepTitle').value;
        const stepResponsibility = document.getElementById('stepResponsibility').value;
        const stepIcon = document.getElementById('stepIcon').value;
        const stepActivity = document.getElementById('stepActivity').value;
        const stepOutput = document.getElementById('stepOutput').value;
        const stepContractLink = document.getElementById('stepContractLink').value;

        const checklistItems = [];
        document.querySelectorAll('.checklist-item-text').forEach(input => {
            if (input.value) {
                checklistItems.push(input.value);
            }
        });

        const actionButtons = [];
        document.querySelectorAll('.action-function').forEach((funcSelect, index) => {
            const labelInput = document.querySelectorAll('.action-label')[index];
            const iconSelect = document.querySelectorAll('.action-icon')[index];
            if (funcSelect.value && labelInput.value && iconSelect.value) {
                actionButtons.push({
                    functionName: funcSelect.value, // Store the function name
                    label: labelInput.value,
                    iconClass: iconSelect.value,
                    dataAction: funcSelect.value // data-action directly maps to function name
                });
            }
        });

        const newStep = {
            id: workflowConfig.length + 1,
            title: stepTitle,
            responsibility: stepResponsibility,
            stepIcon: stepIcon, // Store the selected step icon
            activity: stepActivity,
            output: stepOutput,
            contractLink: stepContractLink,
            checklist: checklistItems, // Store checklist items
            actions: actionButtons
        };

        workflowConfig.push(newStep);
        renderWorkflowConfig();
        updateExportConfig();
        this.reset(); // Clear the form
        document.getElementById('actionButtonsContainer').innerHTML = ''; // Clear action button inputs
        document.getElementById('checklistItemsContainer').innerHTML = ''; // Clear checklist inputs
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

            let checklistHtml = '';
            if (step.checklist && step.checklist.length > 0) {
                checklistHtml = `
                    <div class="checklist-items mt-3">
                        <h6><i class="fa fa-list-ul mr-1"></i> Transition Checklist:</h6>
                        <ul>
                            ${step.checklist.map(item => `<li><i class="fa fa-check"></i> ${item}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }

            // Unique ID for the collapse content
            const collapseId = `collapseStep${step.id}`;

            stepDiv.innerHTML = `
                <div class="step-header" data-toggle="collapse" data-target="#${collapseId}" aria-expanded="true" aria-controls="${collapseId}">
                    <h5>Step ${step.id}: <i class="fa ${step.stepIcon} mr-2"></i> ${step.title}</h5>
                    <i class="fa fa-chevron-down collapse-toggle-icon"></i>
                </div>
                <div class="collapse show" id="${collapseId}">
                    <p class="mb-1"><strong>Responsibility:</strong> ${step.responsibility}</p>
                    <p class="mb-1"><strong>Activity:</strong> ${step.activity || 'N/A'}</p>
                    <p class="mb-1"><strong>Output:</strong> ${step.output || 'N/A'}</p>
                    ${step.contractLink ? `<p class="mb-1"><i class="fa fa-link mr-1 text-muted"></i> Contract Link: <a href="#" onclick="console.log('Opening ${step.contractLink}'); return false;">${step.contractLink}</a></p>` : ''}
                    ${checklistHtml}
                    <div class="mt-3">
                        ${actionsHtml}
                    </div>
                    <div class="step-footer">
                        <button class="btn btn-sm btn-info" onclick="editStep(${step.id})"><i class="fa fa-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteStep(${step.id})"><i class="fa fa-trash"></i> Delete</button>
                    </div>
                </div>
            `;
            displayContainer.appendChild(stepDiv);
        });

        // Re-initialize Bootstrap dropdowns and collapse for newly added elements
        jQuery('[data-toggle="dropdown"]').dropdown(); // Use jQuery explicitly
        jQuery('[data-toggle="collapse"]').each(function() { // Use jQuery explicitly
            var jQuerythis = jQuery(this); // Use jQuery explicitly
            var target = jQuerythis.data('target');
            var jQuerytarget = jQuery(target); // Use jQuery explicitly
            var jQueryicon = jQuerythis.find('.collapse-toggle-icon'); // Use jQuery explicitly

            // Set initial icon state based on collapse state
            if (jQuerytarget.hasClass('show')) {
                jQueryicon.removeClass('collapsed');
            } else {
                jQueryicon.addClass('collapsed');
            }

            // Bind to Bootstrap's collapse events to update icon and button visibility
            jQuerytarget.off('show.bs.collapse hide.bs.collapse').on('show.bs.collapse', function () {
                jQueryicon.removeClass('collapsed');
            }).on('hide.bs.collapse', function () {
                jQueryicon.addClass('collapsed');
            });
        });


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
        document.getElementById('stepIcon').value = stepToEdit.stepIcon; // Populate step icon
        document.getElementById('stepActivity').value = stepToEdit.activity;
        document.getElementById('stepOutput').value = stepToEdit.output;
        document.getElementById('stepContractLink').value = stepToEdit.contractLink;

        // Clear existing checklist inputs and add current ones
        const checklistItemsContainer = document.getElementById('checklistItemsContainer');
        checklistItemsContainer.innerHTML = '';
        stepToEdit.checklist.forEach(item => {
            addChecklistItemInput(item);
        });

        // Clear existing action button inputs and add current ones
        const actionButtonsContainer = document.getElementById('actionButtonsContainer');
        actionButtonsContainer.innerHTML = '';
        stepToEdit.actions.forEach(action => {
            addActionButtonInput(action.label, action.iconClass, action.functionName); // Pass functionName
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
            stepIcon: document.getElementById('stepIcon').value,
            activity: document.getElementById('stepActivity').value,
            output: document.getElementById('stepOutput').value,
            contractLink: document.getElementById('stepContractLink').value,
            checklist: [],
            actions: []
        };

        document.querySelectorAll('.checklist-item-text').forEach(input => {
            if (input.value) {
                updatedStep.checklist.push(input.value);
            }
        });

        document.querySelectorAll('.action-function').forEach((funcSelect, index) => {
            const labelInput = document.querySelectorAll('.action-label')[index];
            const iconSelect = document.querySelectorAll('.action-icon')[index];
            if (funcSelect.value && labelInput.value && iconSelect.value) {
                updatedStep.actions.push({
                    functionName: funcSelect.value,
                    label: labelInput.value,
                    iconClass: iconSelect.value,
                    dataAction: funcSelect.value
                });
            }
        });

        workflowConfig[stepIndex] = updatedStep;
        renderWorkflowConfig();
        updateExportConfig();

        // Reset form and submit button
        document.getElementById('addStepForm').reset();
        document.getElementById('actionButtonsContainer').innerHTML = '';
        document.getElementById('checklistItemsContainer').innerHTML = '';
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
        // In your CodeIgniter project, you would call the actual JS function here:
        // if (window[action]) { // Check if function exists globally
        //     window[action](stepElement.dataset.stepId); // Pass step ID if needed
        // }
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
        alert('Workflow configuration copied to clipboard!');
    }

    // Initial setup when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        populateStepIconDropdown(); // Populate the main step icon dropdown
        renderWorkflowConfig();
        updateExportConfig();
    });

</script>
