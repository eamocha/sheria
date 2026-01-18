let workflowConfig = []; // Array to store all configured workflow steps
let currentEditingStepId = null; // To keep track of which step's transitions are being edited

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
function handleAddStepFormSubmit(event) {
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
                dataAction: funcSelect.value
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
        actions: actionButtons,
        transitions: [] // Initialize transitions array for new steps
    };

    workflowConfig.push(newStep);
    renderWorkflowConfig();
   
    this.reset(); // Clear the form
    document.getElementById('actionButtonsContainer').innerHTML = ''; // Clear action button inputs
    document.getElementById('checklistItemsContainer').innerHTML = ''; // Clear checklist inputs
}

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

        // Display transitions summary
        let transitionsSummaryHtml = '';
        if (step.transitions && step.transitions.length > 0) {
            transitionsSummaryHtml = `
                <div class="transitions-summary mt-3">
                    <h6><i class="fa fa-exchange mr-1"></i> Configured Transitions:</h6>
                    <ul>
                        ${step.transitions.map(t => `<li><strong>${t.transitionLabel}</strong> to Step ${t.targetStepId} (${workflowConfig.find(s => s.id === t.targetStepId)?.title || 'N/A'})</li>`).join('')}
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
                ${transitionsSummaryHtml} <!-- Display transitions summary -->
                <div class="mt-3">
                    ${actionsHtml}
                </div>
                <div class="step-footer">
                    <button class="btn btn-sm btn-primary" onclick="openTransitionModal(${step.id})"><i class="fa fa-share-square-o"></i> Configure Transitions</button>
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
            jQueryicon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        } else {
            jQueryicon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        }

        // Bind to Bootstrap's collapse events to update icon and button visibility
        jQuerytarget.off('show.bs.collapse hide.bs.collapse').on('show.bs.collapse', function () {
            jQueryicon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }).on('hide.bs.collapse', function () {
            jQueryicon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
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
        actions: [],
        transitions: workflowConfig[stepIndex].transitions // Preserve existing transitions
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
    if (confirm('Are-you sure you want to delete this step?')) {
        workflowConfig = workflowConfig.filter(step => step.id !== stepId);
        // Re-index IDs to maintain sequential order (optional, but good for display)
        workflowConfig.forEach((step, index) => step.id = index + 1);
        renderWorkflowConfig();
       
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

// Function to open the transition modal
function openTransitionModal(stepId) {
    currentEditingStepId = stepId;
    const currentStep = workflowConfig.find(step => step.id === currentEditingStepId);
    if (!currentStep) return;

    document.getElementById('currentStepTitleInModal').textContent = currentStep.title;
    renderTransitionsInModal(); // Render existing transitions

    jQuery('#transitionModal').modal('show'); // Show the modal
}

// Function to render transitions in the modal
function renderTransitionsInModal() {
    const availableTransitionsList = document.getElementById('availableTransitionsList');
    availableTransitionsList.innerHTML = ''; // Clear previous list

    const transitionModalFooter = document.getElementById('transitionModalFooter');
    transitionModalFooter.innerHTML = ''; // Clear footer buttons

    const currentStep = workflowConfig.find(step => step.id === currentEditingStepId);
    if (!currentStep || !currentStep.transitions || currentStep.transitions.length === 0) {
        availableTransitionsList.innerHTML = '<p class="text-muted">No transitions configured for this step.</p>';
    } else {
        currentStep.transitions.forEach((transition, index) => {
            const transitionDiv = document.createElement('div');
            transitionDiv.className = 'card mb-2';
            transitionDiv.innerHTML = `
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><strong>${transition.transitionLabel}</strong> to Step ${transition.targetStepId} (${workflowConfig.find(s => s.id === transition.targetStepId)?.title || 'N/A'})</h6>
                        <div>
                            <button class="btn btn-sm btn-info mr-1" onclick="editTransition(${index})"><i class="fa fa-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTransition(${index})"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                    <small class="text-muted">Description: ${transition.description || 'N/A'}</small><br>
                    <small class="text-muted">Notify To: ${transition.notifyTo || 'N/A'}</small><br>
                    <small class="text-muted">Notify CC: ${transition.notifyCc || 'N/A'}</small><br>
                    <small class="text-muted">Permissions (Users): ${transition.permissionsUsers || 'N/A'}</small><br>
                    <small class="text-muted">Permissions (Groups): ${transition.permissionsGroups || 'N/A'}</small><br>
                    <small class="text-muted">Requires All Approvals: ${transition.requiresAllApprovals ? 'Yes' : 'No'}</small>
                </div>
            `;
            availableTransitionsList.appendChild(transitionDiv);
        });
    }

    // Add button to add new transition to the footer
    const addTransitionBtn = document.createElement('button');
    addTransitionBtn.className = 'btn btn-success mr-2';
    addTransitionBtn.textContent = 'Add New Transition';
    addTransitionBtn.onclick = () => addTransitionForm();
    transitionModalFooter.appendChild(addTransitionBtn);

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'btn btn-secondary';
    closeButton.setAttribute('data-dismiss', 'modal');
    closeButton.textContent = 'Close';
    transitionModalFooter.appendChild(closeButton);
}


// Function to display the form for adding/editing a transition
function addTransitionForm(transitionData = null, index = null) {
    const availableTransitionsList = document.getElementById('availableTransitionsList');
    availableTransitionsList.innerHTML = ''; // Clear previous list/buttons

    const transitionModalFooter = document.getElementById('transitionModalFooter');
    transitionModalFooter.innerHTML = ''; // Clear footer buttons

    const formHtml = `
        <div class="card p-3 mb-3">
            <h5 class="mb-3">${transitionData ? 'Edit Transition' : 'Add New Transition'}</h5>
            <div class="form-group">
                <label for="transitionLabel">Transition Button Label:</label>
                <input type="text" class="form-control" id="transitionLabel" placeholder="e.g., Send for Review" value="${transitionData?.transitionLabel || ''}" required>
            </div>
            <div class="form-group">
                <label for="targetStepId">Transition To Step:</label>
                <select class="form-control" id="targetStepId" required>
                    <option value="">Select Target Step</option>
                    ${workflowConfig.map(step => `
                        <option value="${step.id}" ${transitionData?.targetStepId === step.id ? 'selected' : ''}>
                            Step ${step.id}: ${step.title}
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="form-group">
                <label for="transitionDescription">Description:</label>
                <textarea class="form-control" id="transitionDescription" rows="2" placeholder="Brief description of this transition">${transitionData?.description || ''}</textarea>
            </div>
            <div class="form-group">
                <label for="notifyTo">Notifications (To):</label>
                <input type="text" class="form-control" id="notifyTo" placeholder="emails or user IDs (comma-separated)" value="${transitionData?.notifyTo || ''}">
            </div>
            <div class="form-group">
                <label for="notifyCc">Notifications (CC):</label>
                <input type="text" class="form-control" id="notifyCc" placeholder="emails or user IDs (comma-separated)" value="${transitionData?.notifyCc || ''}">
            </div>
            <div class="form-group">
                <label for="permissionsUsers">Permissions (Users):</label>
                <input type="text" class="form-control" id="permissionsUsers" placeholder="user IDs (comma-separated)" value="${transitionData?.permissionsUsers || ''}">
            </div>
            <div class="form-group">
                <label for="permissionsGroups">Permissions (Groups):</label>
                <input type="text" class="form-control" id="permissionsGroups" placeholder="group IDs (comma-separated)" value="${transitionData?.permissionsGroups || ''}">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="requiresAllApprovals" ${transitionData?.requiresAllApprovals ? 'checked' : ''}>
                <label class="form-check-label" for="requiresAllApprovals">Requires Approval by All Involved Parties</label>
            </div>
        </div>
    `;
    availableTransitionsList.innerHTML = formHtml;

    // Add Save and Cancel buttons to the modal footer
    const saveButton = document.createElement('button');
    saveButton.type = 'button';
    saveButton.className = 'btn btn-primary mr-2';
    saveButton.textContent = 'Save Transition';
    saveButton.onclick = () => saveTransition(index);
    transitionModalFooter.appendChild(saveButton);

    const cancelButton = document.createElement('button');
    cancelButton.type = 'button';
    cancelButton.className = 'btn btn-secondary';
    cancelButton.textContent = 'Cancel';
    cancelButton.onclick = () => renderTransitionsInModal(); // Go back to list view
    transitionModalFooter.appendChild(cancelButton);
}

// Function to save a transition
function saveTransition(index) {
    const currentStep = workflowConfig.find(step => step.id === currentEditingStepId);
    if (!currentStep) return;

    const transitionLabel = document.getElementById('transitionLabel').value;
    const targetStepId = parseInt(document.getElementById('targetStepId').value);
    const description = document.getElementById('transitionDescription').value;
    const notifyTo = document.getElementById('notifyTo').value;
    const notifyCc = document.getElementById('notifyCc').value;
    const permissionsUsers = document.getElementById('permissionsUsers').value;
    const permissionsGroups = document.getElementById('permissionsGroups').value;
    const requiresAllApprovals = document.getElementById('requiresAllApprovals').checked;

    const newTransition = {
        transitionLabel,
        targetStepId,
        description,
        notifyTo,
        notifyCc,
        permissionsUsers,
        permissionsGroups,
        requiresAllApprovals
    };

    if (index !== null && currentStep.transitions[index]) {
        // Update existing transition
        currentStep.transitions[index] = newTransition;
    } else {
        // Add new transition
        currentStep.transitions.push(newTransition);
    }

    renderTransitionsInModal(); // Re-render the list of transitions
    renderWorkflowConfig(); // Re-render main workflow to update summary
    
}

// Function to edit an existing transition
function editTransition(index) {
    const currentStep = workflowConfig.find(step => step.id === currentEditingStepId);
    if (!currentStep || !currentStep.transitions[index]) return;

    const transitionData = currentStep.transitions[index];
    addTransitionForm(transitionData, index);
}

// Function to delete a transition
function deleteTransition(index) {
    if (confirm('Are you sure you want to delete this transition?')) {
        const currentStep = workflowConfig.find(step => step.id === currentEditingStepId);
        if (!currentStep) return;

        currentStep.transitions.splice(index, 1); // Remove transition at index

        renderTransitionsInModal(); // Re-render the list of transitions
        renderWorkflowConfig(); // Re-render main workflow to update summary
      
    }
}



// Initial setup when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    populateStepIconDropdown(); // Populate the main step icon dropdown
    renderWorkflowConfig();
   

    // Move the form submit event listener inside DOMContentLoaded
    document.getElementById('addStepForm').addEventListener('submit', handleAddStepFormSubmit);
});

