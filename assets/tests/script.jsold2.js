let workflowData = {}; // Object to store the entire workflow data
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

/**
 * Creates and shows a simple alert modal.
 * @param {string} message The message to display.
 * @param {string} type The type of alert (e.g., 'success', 'info', 'danger').
 */
function showAlert(message, type = 'info') {
    const modalElement = document.getElementById('alertModal');
    const modalBody = document.getElementById('alertModalBody');
    modalBody.textContent = message;

    // Set alert type styling if needed
    jQuery(modalBody).removeClass().addClass(`modal-body alert alert-${type}`);

    const alertModal = new bootstrap.Modal(modalElement);
    alertModal.show();
}

/**
 * A helper function to show a custom confirmation modal.
 * @param {string} message The message to display.
 * @param {function} onConfirm The callback function to execute on confirmation.
 */
function showConfirm(message, onConfirm) {
    const modalElement = document.getElementById('confirmModal');
    const modalBody = document.getElementById('confirmModalBody');
    const confirmButton = document.getElementById('confirmActionBtn');

    modalBody.textContent = message;

    // Set up the click handler for the confirm button
    jQuery(confirmButton).off('click').on('click', function() {
        onConfirm();
        const confirmModal = bootstrap.Modal.getInstance(modalElement);
        confirmModal.hide();
    });

    const confirmModal = new bootstrap.Modal(modalElement);
    confirmModal.show();
}
/**
 * Fetches workflow data from the server using jQuery AJAX.
 */
function fetchWorkflowData() {
    const workflowId = jQuery('#workflow_id').val();
    const url = getBaseURL('contract') + 'contract_workflows/fetch_workflow_data/' + workflowId;

    jQuery.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        success: function (data) {
            workflowData = data;
            renderWorkflowConfig();
        },
        error: function (xhr, status, error) {
            console.error("Could not fetch workflow data:", error);
            const displayContainer = jQuery('#configuredWorkflowDisplay');
            displayContainer.html(`
                <div class="alert alert-danger" role="alert">
                    Failed to load workflow data. Please check the server connection.
                </div>`);
        }
    });
}

// Function to populate the stepIcon dropdown
function populateStepIconDropdown() {
    const stepIconSelect = jQuery('#stepIcon');
    faIcons.forEach(icon => {
        const option = jQuery('<option>').val(icon).text(icon);
        stepIconSelect.append(option);
    });
}

// Function to add input fields for a new action button
function addActionButtonInput(initialLabel = '', initialIcon = '', initialFunction = '') {
    const container = jQuery('#actionButtonsContainer');
    const newIndex = container.children().length;

    const actionGroup = jQuery('<div>').addClass('form-row mb-2 align-items-end').html(`
        <div class="col-4">
            <label for="actionFunction${newIndex}">Function:</label>
            <select class="form-control form-control-sm action-function" id="actionFunction${newIndex}">
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
    `);
    container.append(actionGroup);

    // Add event listener for the new select element
    const funcSelect = jQuery(`#actionFunction${newIndex}`);
    funcSelect.on('change', () => populateActionFields(funcSelect, newIndex));
}

// Function to populate action label and icon based on selected function
function populateActionFields(selectElement, index) {
    const selectedFunctionName = selectElement.val();
    const selectedFunction = predefinedFunctions.find(func => func.name === selectedFunctionName);

    const labelInput = jQuery(`#actionLabel${index}`);
    const iconSelect = jQuery(`#actionIcon${index}`);

    if (selectedFunction) {
        labelInput.val(selectedFunction.label);
        iconSelect.val(selectedFunction.iconClass);
    } else {
        labelInput.val('');
        iconSelect.val('');
    }
}

// Function to remove action button input fields
function removeActionButtonInput(button) {
    jQuery(button).closest('.form-row').remove();
}

// Function to add input fields for a new checklist item
function addChecklistItemInput(initialText = '') {
    const container = jQuery('#checklistItemsContainer');
    const newIndex = container.children().length;

    const checklistGroup = jQuery('<div>').addClass('checklist-item-row mb-2').html(`
        <input type="text" class="form-control form-control-sm checklist-item-text" id="checklistItem${newIndex}" placeholder="e.g., All required documents submitted" value="${initialText}" required>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeChecklistItemInput(this)">
            <i class="fa fa-times"></i>
        </button>
    `);
    container.append(checklistGroup);
}

// Function to remove checklist item input fields
function removeChecklistItemInput(button) {
    jQuery(button).closest('.checklist-item-row').remove();
}

// Function to handle adding a new step
function handleAddStepFormSubmit(event) {
    event.preventDefault();
    const workflowId = jQuery('#workflow_id').val();
    const stepTitle = jQuery('#stepTitle').val();
    const stepResponsibility = jQuery('#stepResponsibility').val();
    const stepIcon = jQuery('#stepIcon').val();
    const stepActivity = jQuery('#stepActivity').val();
    const stepOutput = jQuery('#stepOutput').val();
    const stepContractLink = jQuery('#stepContractLink').val();

    const checklistItems = [];
    jQuery('.checklist-item-text').each(function() {
        if (jQuery(this).val()) {
            checklistItems.push({ item_text: jQuery(this).val() });
        }
    });

    const actionButtons = [];
    jQuery('.action-function').each(function(index) {
        const labelInput = jQuery('.action-label').eq(index);
        const iconSelect = jQuery('.action-icon').eq(index);
        if (jQuery(this).val() && labelInput.val() && iconSelect.val()) {
            actionButtons.push({
                function_name: jQuery(this).val(),
                label: labelInput.val(),
                icon_class: iconSelect.val(),
                data_action: jQuery(this).val()
            });
        }
    });

    const newStep = {
        workflow_id: workflowId,
        step_name: stepTitle,
        responsible_user_roles: stepResponsibility,
        step_icon: stepIcon,
        activity: stepActivity,
        step_output: stepOutput,
        contract_link: stepContractLink,
        checklist: checklistItems,
        functions: actionButtons,
        transitions: []
    };
    
    jQuery.ajax({
        url: getBaseURL('contract') + 'contract_workflows/add_step',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(newStep),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        success: function () {
            fetchWorkflowData();
            event.target.reset();
            jQuery('#actionButtonsContainer').empty();
            jQuery('#checklistItemsContainer').empty();
            showAlert('New step added successfully!', 'success');
        },
        error: function (xhr, status, error) {
            console.error("Could not add new step:", error);
            showAlert('Failed to add new step. Please check the server.', 'danger');
        }
    });
}

// Function to render the configured workflow steps
function renderWorkflowConfig() {
    const displayContainer = jQuery('#configuredWorkflowDisplay');
    displayContainer.empty();

    if (!workflowData || !workflowData.steps || workflowData.steps.length === 0) {
        displayContainer.html('<p class="text-muted text-center" id="noStepsMessage">No steps configured yet. Add a step using the form on the left.</p>');
        return;
    }

    workflowData.steps.forEach(step => {
        const stepDiv = jQuery('<div>').addClass('configured-step').attr('data-step-id', step.id);

        let actionsHtml = '';
        if (step.functions && step.functions.length > 0) {
            actionsHtml = `
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="stepActionsDropdown${step.id}" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="stepActionsDropdown${step.id}">
                        ${step.functions.map(action => `
                            <li><a class="dropdown-item" href="#" data-action="${action.data_action}" title="${action.label}">
                                <i class="${action.icon_class}"></i><span>${action.label}</span>
                            </a></li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }

        let checklistHtml = '';
        if (step.checklist && step.checklist.length > 0) {
            checklistHtml = `
                <div class="checklist-items mt-3">
                    <h6><i class="fa fa-list-ul mr-1"></i> Transition Checklist:</h6>
                    <ul>
                        ${step.checklist.map(item => `<li><i class="fa fa-check"></i> ${item.item_text}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        let transitionsSummaryHtml = '';
        if (step.transitions && step.transitions.length > 0) {
            transitionsSummaryHtml = `
                <div class="transitions-summary mt-3">
                    <h6><i class="fa fa-exchange mr-1"></i> Configured Transitions:</h6>
                    <ul>
                        ${step.transitions.map(t => `<li><strong>${t.name}</strong> to Step ${t.to_step} (${workflowData.steps.find(s => s.id === t.to_step)?.step_name || 'N/A'})</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        const collapseId = `collapseStep${step.id}`;

        stepDiv.html(`
            <div class="step-header" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="true" aria-controls="${collapseId}">
                <h5>Step ${step.id}: <i class="fa ${step.step_icon} mr-2"></i> ${step.step_name}</h5>
                <i class="fa fa-chevron-down collapse-toggle-icon"></i>
            </div>
            <div class="collapse show" id="${collapseId}">
                <p class="mb-1"><strong>Responsibility:</strong> ${step.responsible_user_roles || 'N/A'}</p>
                <p class="mb-1"><strong>Activity:</strong> ${step.activity || 'N/A'}</p>
                <p class="mb-1"><strong>Output:</strong> ${step.step_output || 'N/A'}</p>
                ${step.contract_link ? `<p class="mb-1"><i class="fa fa-link mr-1 text-muted"></i> Contract Link: <a href="#" onclick="console.log('Opening ${step.contract_link}'); return false;">${step.contract_link}</a></p>` : ''}
                ${checklistHtml}
                ${transitionsSummaryHtml}
                <div class="mt-3">
                    ${actionsHtml}
                </div>
                <div class="step-footer">
                    <button class="btn btn-sm btn-primary" onclick="openTransitionModal(${step.id})"><i class="fa fa-share-square-o"></i> Configure Transitions</button>
                    <button class="btn btn-sm btn-info" onclick="editStep(${step.id})"><i class="fa fa-pencil"></i> Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteStep(${step.id})"><i class="fa fa-trash"></i> Delete</button>
                </div>
            </div>
        `);
        displayContainer.append(stepDiv);
    });

    jQuery('[data-bs-toggle="collapse"]').each(function() {
        const toggle = jQuery(this);
        const target = jQuery(toggle.data('bs-target'));
        const icon = toggle.find('.collapse-toggle-icon');

        if (target.hasClass('show')) {
            icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        } else {
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        }
        
        target.on('show.bs.collapse', () => {
            icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        });

        target.on('hide.bs.collapse', () => {
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });
    });

    jQuery('.dropdown-item').on('click', handleDropdownItemClick);
}

// Function to edit a step
function editStep(stepId) {
    const stepToEdit = workflowData.steps.find(step => step.id === stepId);
    if (!stepToEdit) return;

    jQuery('#stepTitle').val(stepToEdit.step_name);
    jQuery('#stepResponsibility').val(stepToEdit.responsible_user_roles);
    jQuery('#stepIcon').val(stepToEdit.step_icon);
    jQuery('#stepActivity').val(stepToEdit.activity);
    jQuery('#stepOutput').val(stepToEdit.step_output);
    jQuery('#stepContractLink').val(stepToEdit.contract_link);

    const checklistItemsContainer = jQuery('#checklistItemsContainer');
    checklistItemsContainer.empty();
    stepToEdit.checklist.forEach(item => {
        addChecklistItemInput(item.item_text);
    });

    const actionButtonsContainer = jQuery('#actionButtonsContainer');
    actionButtonsContainer.empty();
    stepToEdit.functions.forEach(action => {
        addActionButtonInput(action.label, action.icon_class, action.function_name);
    });

    const addStepForm = jQuery('#addStepForm');
    const submitButton = addStepForm.find('button[type="submit"]');
    submitButton.text('Update Step');
    
    addStepForm.off('submit').on('submit', function onUpdateSubmit(event) {
        event.preventDefault();
        updateStep(stepId);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Function to update an existing step
function updateStep(stepId) {
    const stepToUpdate = workflowData.steps.find(step => step.id === stepId);
    if (!stepToUpdate) return;
    const workflowId = jQuery('#workflow_id').val();

    const updatedStep = {
        id: stepId,
        workflow_id: workflowId,
        step_name: jQuery('#stepTitle').val(),
        responsible_user_roles: jQuery('#stepResponsibility').val(),
        step_icon: jQuery('#stepIcon').val(),
        activity: jQuery('#stepActivity').val(),
        step_output: jQuery('#stepOutput').val(),
        contract_link: jQuery('#stepContractLink').val(),
        checklist: [],
        functions: [],
    };

    jQuery('.checklist-item-text').each(function() {
        if (jQuery(this).val()) {
            updatedStep.checklist.push({ item_text: jQuery(this).val() });
        }
    });

    jQuery('.action-function').each(function(index) {
        const labelInput = jQuery('.action-label').eq(index);
        const iconSelect = jQuery('.action-icon').eq(index);
        if (jQuery(this).val() && labelInput.val() && iconSelect.val()) {
            updatedStep.functions.push({
                function_name: jQuery(this).val(),
                label: labelInput.val(),
                icon_class: iconSelect.val(),
                data_action: jQuery(this).val()
            });
        }
    });
    
    jQuery.ajax({
        url: getBaseURL('contract') + 'contract_workflows/update_step/' + stepId,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(updatedStep),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        success: function () {
            fetchWorkflowData();
            jQuery('#addStepForm').trigger('reset');
            jQuery('#actionButtonsContainer').empty();
            jQuery('#checklistItemsContainer').empty();
            const submitButton = jQuery('#addStepForm').find('button[type="submit"]');
            submitButton.text('Add Step');
            jQuery('#addStepForm').off('submit').on('submit', handleAddStepFormSubmit);
            showAlert('Step updated successfully!', 'success');
        },
        error: function (xhr, status, error) {
            console.error("Could not update step:", error);
            showAlert('Failed to update step. Please check the server.', 'danger');
        }
    });
}

// Function to delete a step
function deleteStep(stepId) {
    showConfirm('Are you sure you want to delete this step?', function() {
        jQuery.ajax({
            url: getBaseURL('contract') + 'contract_workflows/delete_step/' + stepId,
            method: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            success: function () {
                fetchWorkflowData();
                showAlert('Step deleted successfully!', 'success');
            },
            error: function (xhr, status, error) {
                console.error("Could not delete step:", error);
                showAlert('Failed to delete step. Please check the server.', 'danger');
            }
        });
    });
}

// Function to handle clicks on dropdown items (for console logging)
function handleDropdownItemClick(event) {
    event.preventDefault();
    const button = jQuery(event.currentTarget);
    const stepElement = button.closest('.configured-step');
    const stepTitle = stepElement ? stepElement.find('h5').text() : 'Unknown Step';
    const action = button.data('action');
    console.log(`Action "${action}" initiated for step: "${stepTitle}"`);
}

// Function to open the transition modal
function openTransitionModal(stepId) {
    currentEditingStepId = stepId;
    const currentStep = workflowData.steps.find(step => step.id === currentEditingStepId);
    if (!currentStep) return;

    jQuery('#currentStepTitleInModal').text(currentStep.step_name);
    renderTransitionsInModal();

    const transitionModal = new bootstrap.Modal(document.getElementById('transitionModal'));
    transitionModal.show();
}

// Function to render transitions in the modal
function renderTransitionsInModal() {
    const availableTransitionsList = jQuery('#availableTransitionsList');
    availableTransitionsList.empty();

    const transitionModalFooter = jQuery('#transitionModalFooter');
    transitionModalFooter.empty();

    const currentStep = workflowData.steps.find(step => step.id === currentEditingStepId);
    if (!currentStep || !currentStep.transitions || currentStep.transitions.length === 0) {
        availableTransitionsList.html('<p class="text-muted">No transitions configured for this step.</p>');
    } else {
        currentStep.transitions.forEach((transition) => {
            const transitionDiv = jQuery('<div>').addClass('card mb-2').html(`
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><strong>${transition.name}</strong> to Step ${transition.to_step} (${workflowData.steps.find(s => s.id === transition.to_step)?.step_name || 'N/A'})</h6>
                        <div>
                            <button class="btn btn-sm btn-info me-1" onclick="editTransition(${transition.id})"><i class="fa fa-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTransition(${transition.id})"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                    <small class="text-muted">Description: ${transition.comment || 'N/A'}</small><br>
                </div>
            `);
            availableTransitionsList.append(transitionDiv);
        });
    }

    const addTransitionBtn = jQuery('<button>').addClass('btn btn-success me-2').text('Add New Transition').on('click', () => addTransitionForm());
    transitionModalFooter.append(addTransitionBtn);

    const closeButton = jQuery('<button>').attr('type', 'button').addClass('btn btn-secondary').attr('data-bs-dismiss', 'modal').text('Close');
    transitionModalFooter.append(closeButton);
}


// Function to display the form for adding/editing a transition
function addTransitionForm(transitionData = null) {
    const availableTransitionsList = jQuery('#availableTransitionsList');
    availableTransitionsList.empty();

    const transitionModalFooter = jQuery('#transitionModalFooter');
    transitionModalFooter.empty();

    const currentStep = workflowData.steps.find(step => step.id === currentEditingStepId);
    if (!currentStep) return;

    const formHtml = `
        <div class="card p-3 mb-3">
            <h5 class="mb-3">${transitionData ? 'Edit Transition' : 'Add New Transition'}</h5>
            <div class="form-group">
                <label for="transitionLabel">Transition Button Label:</label>
                <input type="text" class="form-control" id="transitionLabel" placeholder="e.g., Send for Review" value="${transitionData?.name || ''}" required>
            </div>
            <div class="form-group">
                <label for="targetStepId">Transition To Step:</label>
                <select class="form-control" id="targetStepId" required>
                    <option value="">Select Target Step</option>
                    ${workflowData.steps.map(step => `
                        <option value="${step.id}" ${transitionData?.to_step === step.id ? 'selected' : ''}>
                            Step ${step.id}: ${step.step_name}
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="form-group">
                <label for="transitionDescription">Description:</label>
                <textarea class="form-control" id="transitionDescription" rows="2" placeholder="Brief description of this transition">${transitionData?.comment || ''}</textarea>
            </div>
        </div>
    `;
    availableTransitionsList.html(formHtml);

    const saveButton = jQuery('<button>').attr('type', 'button').addClass('btn btn-primary me-2').text('Save Transition').on('click', () => saveTransition(transitionData?.id));
    transitionModalFooter.append(saveButton);

    const cancelButton = jQuery('<button>').attr('type', 'button').addClass('btn btn-secondary').text('Cancel').on('click', () => renderTransitionsInModal());
    transitionModalFooter.append(cancelButton);
}

// Function to save a transition
function saveTransition(transitionId) {
    const currentStep = workflowData.steps.find(step => step.id === currentEditingStepId);
    if (!currentStep) return;

    const transitionLabel = jQuery('#transitionLabel').val();
    const targetStepId = jQuery('#targetStepId').val();
    const description = jQuery('#transitionDescription').val();
    const workflowId = jQuery('#workflow_id').val();

    const newTransition = {
        workflow_id: workflowId,
        from_step: currentStep.id,
        to_step: targetStepId,
        name: transitionLabel,
        comment: description,
        approval_needed: 0,
    };
    
    const url = transitionId
        ? getBaseURL('contract') + 'contract_workflows/update_transition/' + transitionId
        : getBaseURL('contract') + 'contract_workflows/add_transition';

    jQuery.ajax({
        url: url,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(newTransition),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        success: function () {
            fetchWorkflowData();
            renderTransitionsInModal();
            showAlert(transitionId ? 'Transition updated successfully!' : 'New transition added successfully!', 'success');
        },
        error: function (xhr, status, error) {
            console.error("Could not save transition:", error);
            showAlert('Failed to save transition. Please check the server.', 'danger');
        }
    });
}

// Function to edit an existing transition
function editTransition(transitionId) {
    const currentStep = workflowData.steps.find(step => step.id === currentEditingStepId);
    if (!currentStep) return;

    const transitionData = currentStep.transitions.find(t => t.id === transitionId);
    addTransitionForm(transitionData);
}

// Function to delete a transition
function deleteTransition(transitionId) {
    showConfirm('Are you sure you want to delete this transition?', function() {
        jQuery.ajax({
            url: getBaseURL('contract') + 'contract_workflows/delete_transition/' + transitionId,
            method: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            success: function () {
                fetchWorkflowData();
                renderTransitionsInModal();
                showAlert('Transition deleted successfully!', 'success');
            },
            error: function (xhr, status, error) {
                console.error("Could not delete transition:", error);
                showAlert('Failed to delete transition. Please check the server.', 'danger');
            }
        });
    });
}

// Initial setup when DOM is loaded
jQuery(document).ready(function() {
    populateStepIconDropdown();
    fetchWorkflowData();

    jQuery('#addStepForm').on('submit', handleAddStepFormSubmit);
});
