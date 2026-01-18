let workflowData = {}; // Object to store the entire workflow data
let currentEditingStepId = null; // To keep track of which step's transitions are being edited

// Predefined Font Awesome 4.7 icons for selection (expanded list)
const faIcons = [
    "fa-file-text-o", "fa-user", "fa-gavel", "fa-handshake-o", "fa-file-signature", // General step icons
    "fa-eye", "fa-edit", "fa-tasks", "fa-bell", "fa-comment", "fa-folder-open",
    "fa-upload", "fa-check", "fa-question-circle", "fa-balance-scale", "fa-paper-plane",
    "fa-play", "fa-user-check", "fa-search-plus", "fa-lightbulb-o", "fa-flag", "fa-money", "fa-times-circle",
    "fa-chevron-down", "fa-chevron-right", "fa-check-circle", "fa-list-ul", "fa-exchange", "fa-share-square-o", "fa-pencil", "fa-trash", "fa-plus-circle", "fa-link"
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
    // Ensure the alertModal exists in your HTML.
    // If not, you'll need to add a basic modal structure for alerts.
    let $alertModal = jQuery('#alertModal');
    if ($alertModal.length === 0) {
        // Create a simple alert modal if it doesn't exist
        $alertModal = jQuery(`
            <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-body text-center"></div>
                    </div>
                </div>
            </div>
        `);
        jQuery('body').append($alertModal);
    }

    const $modalBody = $alertModal.find('.modal-body');
    $modalBody.removeClass().addClass(`modal-body text-white bg-${type}`);
    $modalBody.text(message);

    // Use Bootstrap 4's jQuery plugin syntax for modal
    $alertModal.modal('show');

    setTimeout(() => $alertModal.modal('hide'), 3000); // Auto-hide after 3 seconds
}

/**
 * A helper function to get the base URL for API calls.
 * Assumes a structure like http://<domain>/ca/modules/<moduleName>/.
 * @param {string} moduleName The name of the module (e.g., 'contract').
 * @returns {string} The base URL for the specified module.
 */
function getBaseURL(moduleName) {
    // This is a dynamic way to construct the base URL based on the current location.
    // Adjust this logic if the application's folder structure is different.
    const pathSegments = window.location.pathname.split('/');
    // Assuming the URL is like example.com/ca/some_page or example.com/ca/modules/contract/configure.php
    // We want to get to example.com/ca/
    let basePath = window.location.origin;
    if (pathSegments.length > 1) {
        basePath += `/${pathSegments[1]}`; // This should capture '/ca'
    }
    return `${basePath}/modules/${moduleName}/`;
}

/**
 * Fetches workflow data from the server.
 */
async function fetchWorkflowData() {
    const workflowId = jQuery('#workflow_id').val();
    const $displayContainer = jQuery('#configuredWorkflowDisplay');

    // Show global loader
    jQuery('#loader-global').show();

    try {
        const url = getBaseURL('contract') + 'contract_workflows/fetch_workflow_data/' + workflowId;
        const response = await fetch(url);

        if (!response.ok) {
            // Attempt to read error message from response body if available
            const errorBody = await response.text();
            throw new Error(`HTTP error! Status: ${response.status}. Details: ${errorBody || 'No additional error message.'}`);
        }

        const data = await response.json();
        // Assuming the JSON structure wraps workflow inside a 'workflow' key.
        // Adjust `data.workflow` to just `data` if your API directly returns the workflow object.
        workflowData = data.workflow;
        renderWorkflowConfig(); // Render the fetched data

        // Clear any previous error messages if data is successfully loaded
        if (workflowData && workflowData.steps && workflowData.steps.length > 0) {
            jQuery('#noStepsMessage').remove(); // Remove initial "no steps" message if present
        } else {
             $displayContainer.html('<p class="text-muted text-center" id="noStepsMessage">No steps configured yet. Add a step using the form on the left.</p>');
        }


    } catch (error) {
        console.error("Could not fetch workflow data:", error);
        $displayContainer.html(`
            <div class="alert alert-danger" role="alert">
                Failed to load workflow data. Please check the server connection or API endpoint. Error: ${error.message}
            </div>`);
    } finally {
        // Hide global loader regardless of success or failure
        jQuery('#loader-global').hide();
    }
}

// Function to populate the stepIcon dropdown
function populateStepIconDropdown() {
    const $stepIconSelect = jQuery('#stepIcon');
    // Clear existing options first, except the "Select Icon" one
    $stepIconSelect.html('<option value="">Select Icon for Step</option>');
    faIcons.forEach(icon => {
        const option = `<option value="${icon}">${icon.startsWith('fa-') ? icon.substring(3) : icon}</option>`;
        $stepIconSelect.append(option);
    });
}

// Function to add input fields for a new action button
function addActionButtonInput(initialLabel = '', initialIcon = '', initialFunction = '') {
    const $container = jQuery('#actionButtonsContainer');
    const newIndex = $container.children().length;

    const actionGroupHtml = `
        <div class="form-row mb-2 align-items-end">
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
                <button type="button" class="btn btn-sm btn-danger remove-action-button">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    `;
    const $actionGroup = jQuery(actionGroupHtml);
    $container.append($actionGroup);

    // Add event listener for the new select element using delegation
    $actionGroup.find(`#actionFunction${newIndex}`).on('change', function() {
        populateActionFields(jQuery(this), newIndex);
    });
}

// Function to populate action label and icon based on selected function
function populateActionFields($selectElement, index) {
    const selectedFunctionName = $selectElement.val();
    const selectedFunction = predefinedFunctions.find(func => func.name === selectedFunctionName);

    const $labelInput = jQuery(`#actionLabel${index}`);
    const $iconSelect = jQuery(`#actionIcon${index}`);

    if (selectedFunction) {
        $labelInput.val(selectedFunction.label);
        $iconSelect.val(selectedFunction.iconClass); // This will set the full class, e.g., "fa fa-edit"
    } else {
        $labelInput.val('');
        $iconSelect.val('');
    }
}

// Function to remove action button input fields
function removeActionButtonInput(button) {
    jQuery(button).closest('.form-row').remove();
}

// Function to add input fields for a new checklist item
function addChecklistItemInput(initialText = '') {
    const $container = jQuery('#checklistItemsContainer');
    const newIndex = $container.children().length;

    const checklistGroupHtml = `
        <div class="checklist-item-row mb-2">
            <input type="text" class="form-control form-control-sm checklist-item-text" id="checklistItem${newIndex}" placeholder="e.g., All required documents submitted" value="${initialText}" required>
            <button type="button" class="btn btn-sm btn-danger remove-checklist-item">
                <i class="fa fa-times"></i>
            </button>
        </div>
    `;
    const $checklistGroup = jQuery(checklistGroupHtml);
    $container.append($checklistGroup);
}

// Function to remove checklist item input fields
function removeChecklistItemInput(button) {
    jQuery(button).closest('.checklist-item-row').remove();
}

// Function to handle adding a new step
async function handleAddStepFormSubmit(event) {
    event.preventDefault();
    const workflowId = jQuery('#workflow_id').val();
    const stepTitle = jQuery('#stepTitle').val();
    const stepResponsibility = jQuery('#stepResponsibility').val();
    const stepIcon = jQuery('#stepIcon').val();
    const stepActivity = jQuery('#stepActivity').val();
    const stepOutput = jQuery('#stepOutput').val();
    const stepInput = jQuery('#stepInput').val(); // Get input field value
    const stepContractLink = jQuery('#stepContractLink').val();
    const isStartPoint = jQuery('#isStartPoint').prop('checked') ? 1 : 0;
    const approvalStartPoint = jQuery('#approvalStartPoint').prop('checked') ? 1 : 0;
    const isSignaturePoint = jQuery('#isSignaturePoint').prop('checked') ? 1 : 0;
    const isGlobal = jQuery('#isGlobal').prop('checked') ? 1 : 0;

    const checklistItems = [];
    jQuery('.checklist-item-text').each(function() {
        if (jQuery(this).val()) {
            checklistItems.push({ item_text: jQuery(this).val() });
        }
    });

    const actionButtons = [];
    jQuery('.action-function').each(function(index) {
        const $funcSelect = jQuery(this);
        const $labelInput = jQuery('.action-label').eq(index);
        const $iconSelect = jQuery('.action-icon').eq(index);
        if ($funcSelect.val() && $labelInput.val() && $iconSelect.val()) {
            actionButtons.push({
                function_name: $funcSelect.val(), // Use function_name to match JSON
                label: $labelInput.val(),
                icon_class: $iconSelect.val(),
                data_action: $funcSelect.val() // Often data_action is the same as function_name
            });
        }
    });

    const newStep = {
        workflow_id: workflowId,
        step_name: stepTitle,
        responsible_user_roles: stepResponsibility,
        step_icon: stepIcon, // Store the selected step icon
        activity: stepActivity,
        step_output: stepOutput,
        step_input: stepInput, // Include step_input
        contract_link: stepContractLink,
        start_point: isStartPoint,
        approval_start_point: approvalStartPoint,
        is_signature_point: isSignaturePoint, // Renamed for consistency with backend
        is_global: isGlobal,
        checklist: checklistItems, // Store checklist items
        functions: actionButtons,
        transitions: [] // Initialize transitions array for new steps
    };

    try {
        const response = await fetch(getBaseURL('contract') + 'contract_workflows/add_step', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newStep),
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        await fetchWorkflowData(); // Re-fetch all data to update the view
        jQuery('#addStepForm')[0].reset(); // Clear the form
        jQuery('#actionButtonsContainer').empty(); // Clear action button inputs
        jQuery('#checklistItemsContainer').empty(); // Clear checklist inputs
        // Reset checkboxes
        jQuery('#isStartPoint').prop('checked', false);
        jQuery('#approvalStartPoint').prop('checked', false);
        jQuery('#isSignaturePoint').prop('checked', false);
        jQuery('#isGlobal').prop('checked', false);

        showAlert('New step added successfully!', 'success');
    } catch (error) {
        console.error("Could not add new step:", error);
        showAlert('Failed to add new step. Please check the server.', 'danger');
    }
}

// Function to handle collapse toggle icons
function setupCollapseToggleIcons() {
    jQuery('[data-toggle="collapse"]').each(function() {
        const $toggle = jQuery(this);
        const $target = jQuery($toggle.data('target'));
        const $icon = $toggle.find('.collapse-toggle-icon');

        // Detach existing handlers to prevent duplicates if renderWorkflowConfig is called multiple times
        $target.off('show.bs.collapse hide.bs.collapse');

        // Initial state check for the icon
        if ($target.hasClass('show')) {
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        } else {
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        }

        // Attach new handlers
        $target.on('show.bs.collapse', function() {
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        });

        $target.on('hide.bs.collapse', function() {
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });
    });
}

// Function to render the configured workflow steps
function renderWorkflowConfig() {
    const $displayContainer = jQuery('#configuredWorkflowDisplay');
    $displayContainer.empty(); // Clear previous display

    if (!workflowData || !workflowData.steps || workflowData.steps.length === 0) {
        $displayContainer.html('<p class="text-muted text-center" id="noStepsMessage">No steps configured yet. Add a step using the form on the left.</p>');
        return;
    }

    // Sort steps by ID to ensure consistent display order
    const sortedSteps = [...workflowData.steps].sort((a, b) => a.id - b.id);

    sortedSteps.forEach(step => {
        const $stepDiv = jQuery('<div class="configured-step"></div>');
        $stepDiv.attr('data-step-id', step.id);

        let actionsHtml = '';
        if (step.functions && step.functions.length > 0) {
            actionsHtml = `
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="stepActionsDropdown${step.id}" data-toggle="dropdown" aria-expanded="false">
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

        // Display transitions summary
        let transitionsSummaryHtml = '';
        // Filter transitions to only show those originating from the current workflow's steps
        const currentWorkflowTransitions = step.transitions ? step.transitions.filter(t => t.workflow_id == workflowData.id && t.from_step == step.id) : [];

        if (currentWorkflowTransitions.length > 0) {
            transitionsSummaryHtml = `
                <div class="transitions-summary mt-3">
                    <h6><i class="fa fa-exchange mr-1"></i> Configured Transitions:</h6>
                    <ul>
                        ${currentWorkflowTransitions.map(t => {
                            const toStepName = workflowData.steps.find(s => s.id == t.to_step)?.step_name || 'N/A';
                            return `<li><strong>${t.name}</strong> to Step ${t.to_step} (${toStepName})</li>`;
                        }).join('')}
                    </ul>
                </div>
            `;
        }

        // Unique ID for the collapse content
        const collapseId = `collapseStep${step.id}`;

        // Determine initial collapse state (e.g., first step open, others closed)
        const isFirstStep = sortedSteps.indexOf(step) === 0;
        const collapseShowClass = isFirstStep ? 'show' : '';
        const ariaExpanded = isFirstStep ? 'true' : 'false';
        const toggleIconClass = isFirstStep ? 'fa-chevron-down' : 'fa-chevron-right';

        $stepDiv.html(`
            <button class="step-header" type="button" data-toggle="collapse" data-target="#${collapseId}" aria-expanded="${ariaExpanded}" aria-controls="${collapseId}">
                <h5>Step ${step.id}: <i class="fa ${step.step_icon || 'fa-file-text-o'} mr-2"></i> ${step.step_name}</h5>
                <i class="fa ${toggleIconClass} collapse-toggle-icon"></i>
            </button>
            <div class="collapse ${collapseShowClass}" id="${collapseId}">
                <p class="mb-1"><strong>Responsibility:</strong> ${step.responsible_user_roles || 'N/A'}</p>
                <p class="mb-1"><strong>Activity:</strong> ${step.activity || 'N/A'}</p>
                <p class="mb-1"><strong>Input:</strong> ${step.step_input || 'N/A'}</p>
                <p class="mb-1"><strong>Output:</strong> ${step.step_output || 'N/A'}</p>
                ${step.contract_link ? `<p class="mb-1"><i class="fa fa-link mr-1 text-muted"></i> Contract Link: <a href="${step.contract_link}" target="_blank" rel="noopener noreferrer">${step.contract_link}</a></p>` : ''}
                <p class="mb-1"><strong>Start Point:</strong> ${step.start_point ? 'Yes' : 'No'}</p>
                <p class="mb-1"><strong>Approval Start Point:</strong> ${step.approval_start_point ? 'Yes' : 'No'}</p>
                <p class="mb-1"><strong>Signature Point:</strong> ${step.is_signature_point ? 'Yes' : 'No'}</p>
                <p class="mb-1"><strong>Global Step:</strong> ${step.is_global ? 'Yes' : 'No'}</p>
                ${checklistHtml}
                ${transitionsSummaryHtml} <div class="mt-3">
                    ${actionsHtml}
                </div>
                <div class="step-footer">
                    <button class="btn btn-sm btn-primary configure-transitions-btn" data-step-id="${step.id}"><i class="fa fa-share-square-o"></i> Configure Transitions</button>
                    <button class="btn btn-sm btn-info edit-step-btn" data-step-id="${step.id}"><i class="fa fa-pencil"></i> Edit</button>
                    <button class="btn btn-sm btn-danger delete-step-btn" data-step-id="${step.id}"><i class="fa fa-trash"></i> Delete</button>
                </div>
            </div>
        `);
        $displayContainer.append($stepDiv);
    });

    // Call the function to set up collapse toggles after steps are rendered
    setupCollapseToggleIcons();

    // Attach event listeners for dropdown items and step buttons using delegation
    $displayContainer.off('click', '.dropdown-item').on('click', '.dropdown-item', handleDropdownItemClick);
    $displayContainer.off('click', '.configure-transitions-btn').on('click', '.configure-transitions-btn', function() {
        openTransitionModal(jQuery(this).data('step-id'));
    });
    $displayContainer.off('click', '.edit-step-btn').on('click', '.edit-step-btn', function() {
        editStep(jQuery(this).data('step-id'));
    });
    $displayContainer.off('click', '.delete-step-btn').on('click', '.delete-step-btn', function() {
        deleteStep(jQuery(this).data('step-id'));
    });
}

// Function to edit a step
function editStep(stepId) {
    const stepToEdit = workflowData.steps.find(step => step.id == stepId); // Use == for loose comparison as ID might be string
    if (!stepToEdit) {
        showAlert('Step not found for editing.', 'danger');
        return;
    }

    // Populate the form with current step data
    jQuery('#stepTitle').val(stepToEdit.step_name || '');
    jQuery('#stepResponsibility').val(stepToEdit.responsible_user_roles || '');
    jQuery('#stepIcon').val(stepToEdit.step_icon || ''); // Populate step icon
    jQuery('#stepActivity').val(stepToEdit.activity || '');
    jQuery('#stepOutput').val(stepToEdit.step_output || '');
    jQuery('#stepInput').val(stepToEdit.step_input || ''); // Populate step input
    jQuery('#stepContractLink').val(stepToEdit.contract_link || '');

    // Populate checkboxes
    jQuery('#isStartPoint').prop('checked', stepToEdit.start_point == 1);
    jQuery('#approvalStartPoint').prop('checked', stepToEdit.approval_start_point == 1);
    jQuery('#isSignaturePoint').prop('checked', stepToEdit.is_signature_point == 1); // Assuming 'is_signature_point' in data
    jQuery('#isGlobal').prop('checked', stepToEdit.is_global == 1);

    // Clear existing checklist inputs and add current ones
    const $checklistItemsContainer = jQuery('#checklistItemsContainer');
    $checklistItemsContainer.empty();
    if (stepToEdit.checklist) {
        stepToEdit.checklist.forEach(item => {
            addChecklistItemInput(item.item_text);
        });
    }


    // Clear existing action button inputs and add current ones
    const $actionButtonsContainer = jQuery('#actionButtonsContainer');
    $actionButtonsContainer.empty();
    if (stepToEdit.functions) {
        stepToEdit.functions.forEach(action => {
            addActionButtonInput(action.label, action.icon_class, action.function_name); // Pass function_name
        });
    }


    // Change form submit button to "Update Step"
    const $addStepForm = jQuery('#addStepForm');
    const $submitButton = $addStepForm.find('button[type="submit"]');
    $submitButton.text('Update Step');

    // Remove old event listener and add new one
    // We create a named function for the event listener to be able to remove it specifically
    const onUpdateSubmit = async function(event) {
        event.preventDefault();
        await updateStep(stepId);
        // After update, revert the button and listener
        $submitButton.text('Add Step');
        $addStepForm.off('submit', onUpdateSubmit);
        $addStepForm.on('submit', handleAddStepFormSubmit);
    };

    $addStepForm.off('submit', handleAddStepFormSubmit);
    $addStepForm.on('submit', onUpdateSubmit);

    // Scroll to form
    jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
}

// Function to update an existing step
async function updateStep(stepId) {
    const stepToUpdateIndex = workflowData.steps.findIndex(step => step.id == stepId);
    if (stepToUpdateIndex === -1) {
        showAlert('Step not found for update.', 'danger');
        return;
    }
    const workflowId = jQuery('#workflow_id').val();

    const updatedStepData = {
        id: stepId,
        workflow_id: workflowId,
        step_name: jQuery('#stepTitle').val(),
        responsible_user_roles: jQuery('#stepResponsibility').val(),
        step_icon: jQuery('#stepIcon').val(),
        activity: jQuery('#stepActivity').val(),
        step_output: jQuery('#stepOutput').val(),
        step_input: jQuery('#stepInput').val(), // Include step_input
        contract_link: jQuery('#stepContractLink').val(),
        start_point: jQuery('#isStartPoint').prop('checked') ? 1 : 0,
        approval_start_point: jQuery('#approvalStartPoint').prop('checked') ? 1 : 0,
        is_signature_point: jQuery('#isSignaturePoint').prop('checked') ? 1 : 0,
        is_global: jQuery('#isGlobal').prop('checked') ? 1 : 0,
        checklist: [],
        functions: [],
    };

    jQuery('.checklist-item-text').each(function() {
        if (jQuery(this).val()) {
            updatedStepData.checklist.push({ item_text: jQuery(this).val() });
        }
    });

    jQuery('.action-function').each(function(index) {
        const $funcSelect = jQuery(this);
        const $labelInput = jQuery('.action-label').eq(index);
        const $iconSelect = jQuery('.action-icon').eq(index);
        if ($funcSelect.val() && $labelInput.val() && $iconSelect.val()) {
            updatedStepData.functions.push({
                function_name: $funcSelect.val(),
                label: $labelInput.val(),
                icon_class: $iconSelect.val(),
                data_action: $funcSelect.val()
            });
        }
    });

    try {
        const response = await fetch(getBaseURL('contract') + 'contract_workflows/update_step/' + stepId, {
            method: 'POST', // or 'PUT' depending on your API
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(updatedStepData),
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        await fetchWorkflowData(); // Re-fetch all data to update the view
        // Reset form and submit button state
        jQuery('#addStepForm')[0].reset();
        jQuery('#actionButtonsContainer').empty();
        jQuery('#checklistItemsContainer').empty();
        jQuery('#isStartPoint').prop('checked', false);
        jQuery('#approvalStartPoint').prop('checked', false);
        jQuery('#isSignaturePoint').prop('checked', false);
        jQuery('#isGlobal').prop('checked', false);

        showAlert('Step updated successfully!', 'success');
    } catch (error) {
        console.error("Could not update step:", error);
        showAlert('Failed to update step. Please check the server.', 'danger');
    }
}

// Function to delete a step
async function deleteStep(stepId) {
    if (confirm('Are you sure you want to delete this step? This action cannot be undone.')) {
        try {
            const response = await fetch(getBaseURL('contract') + 'contract_workflows/delete_step/' + stepId, {
                method: 'POST', // or 'DELETE' depending on your API
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            await fetchWorkflowData(); // Re-fetch all data to update the view
            showAlert('Step deleted successfully!', 'success');
        } catch (error) {
            console.error("Could not delete step:", error);
            showAlert('Failed to delete step. Please check the server.', 'danger');
        }
    }
}

// Function to handle clicks on dropdown items (for console logging)
function handleDropdownItemClick(event) {
    event.preventDefault();
    const $button = jQuery(event.currentTarget);
    const $stepElement = $button.closest('.configured-step');
    const stepTitle = $stepElement.find('h5').text();
    const action = $button.data('action');
    console.log(`Action "${action}" initiated for step: "${stepTitle}"`);
    // Here you would typically trigger actual functionality based on the action
}

// Function to open the transition modal
function openTransitionModal(stepId) {
    currentEditingStepId = stepId;
    const currentStep = workflowData.steps.find(step => step.id == currentEditingStepId);
    if (!currentStep) {
        showAlert('Current step not found.', 'danger');
        return;
    }

    jQuery('#currentStepTitleInModal').text(currentStep.step_name);
    renderTransitionsInModal(); // Render existing transitions

    // Use Bootstrap 4's jQuery plugin syntax for modal
    jQuery('#transitionModal').modal('show');
}

// Function to render transitions in the modal
function renderTransitionsInModal() {
    const $availableTransitionsList = jQuery('#availableTransitionsList');
    $availableTransitionsList.empty(); // Clear previous list

    const $transitionModalFooter = jQuery('#transitionModalFooter');
    $transitionModalFooter.empty(); // Clear footer buttons

    const currentStep = workflowData.steps.find(step => step.id == currentEditingStepId);
    if (!currentStep || !currentStep.transitions || currentStep.transitions.length === 0) {
        $availableTransitionsList.html('<p class="text-muted">No transitions configured for this step.</p>');
    } else {
        // Filter transitions relevant to the current workflow and from the current step
        const relevantTransitions = currentStep.transitions.filter(t =>
            t.workflow_id == workflowData.id && t.from_step == currentEditingStepId
        );

        if (relevantTransitions.length === 0) {
            $availableTransitionsList.html('<p class="text-muted">No transitions configured for this step from this workflow.</p>');
        } else {
            relevantTransitions.forEach((transition) => {
                const $transitionDiv = jQuery('<div class="card mb-2"></div>');
                $transitionDiv.html(`
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><strong>${transition.name}</strong> to Step ${transition.to_step} (${workflowData.steps.find(s => s.id == transition.to_step)?.step_name || 'N/A'})</h6>
                            <div>
                                <button class="btn btn-sm btn-info me-1 edit-transition-btn" data-transition-id="${transition.id}"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-sm btn-danger delete-transition-btn" data-transition-id="${transition.id}"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                        <small class="text-muted">Description: ${transition.comment || 'N/A'}</small><br>
                    </div>
                `);
                $availableTransitionsList.append($transitionDiv);
            });
            // Attach delegated event listeners for edit and delete buttons
            $availableTransitionsList.off('click', '.edit-transition-btn').on('click', '.edit-transition-btn', function() {
                editTransition(jQuery(this).data('transition-id'));
            });
            $availableTransitionsList.off('click', '.delete-transition-btn').on('click', '.delete-transition-btn', function() {
                deleteTransition(jQuery(this).data('transition-id'));
            });
        }
    }

    // Add button to add new transition to the footer
    const $addTransitionBtn = jQuery('<button type="button" class="btn btn-success me-2">Add New Transition</button>');
    $addTransitionBtn.on('click', () => addTransitionForm());
    $transitionModalFooter.append($addTransitionBtn);

    const $closeButton = jQuery('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
    $transitionModalFooter.append($closeButton);
}


// Function to display the form for adding/editing a transition
function addTransitionForm(transitionData = null) {
    const $availableTransitionsList = jQuery('#availableTransitionsList');
    $availableTransitionsList.empty(); // Clear previous list/buttons

    const $transitionModalFooter = jQuery('#transitionModalFooter');
    $transitionModalFooter.empty(); // Clear footer buttons

    const currentStep = workflowData.steps.find(step => step.id == currentEditingStepId);
    if (!currentStep) {
        showAlert('Cannot add/edit transition: current step not found.', 'danger');
        return;
    }

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
                        <option value="${step.id}" ${transitionData?.to_step == step.id ? 'selected' : ''}>
                            Step ${step.id}: ${step.step_name}
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="form-group">
                <label for="transitionDescription">Description:</label>
                <textarea class="form-control" id="transitionDescription" rows="2" placeholder="Brief description of this transition">${transitionData?.comment || ''}</textarea>
            </div>
            <div class="form-group form-check mt-3">
                <input type="checkbox" class="form-check-input" id="approvalNeeded" ${transitionData?.approval_needed == 1 ? 'checked' : ''}>
                <label class="form-check-label" for="approvalNeeded">Approval Needed?</label>
            </div>
        </div>
    `;
    $availableTransitionsList.html(formHtml);

    // Add Save and Cancel buttons to the modal footer
    const $saveButton = jQuery('<button type="button" class="btn btn-primary me-2">Save Transition</button>');
    $saveButton.on('click', () => saveTransition(transitionData?.id));
    $transitionModalFooter.append($saveButton);

    const $cancelButton = jQuery('<button type="button" class="btn btn-secondary">Cancel</button>');
    $cancelButton.on('click', () => renderTransitionsInModal()); // Go back to list view
    $transitionModalFooter.append($cancelButton);
}

// Function to save a transition
async function saveTransition(transitionId) {
    const currentStep = workflowData.steps.find(step => step.id == currentEditingStepId);
    if (!currentStep) {
        showAlert('Cannot save transition: current step not found.', 'danger');
        return;
    }

    const transitionLabel = jQuery('#transitionLabel').val();
    const targetStepId = jQuery('#targetStepId').val();
    const description = jQuery('#transitionDescription').val();
    const approvalNeeded = jQuery('#approvalNeeded').prop('checked') ? 1 : 0;
    const workflowId = jQuery('#workflow_id').val();

    if (!transitionLabel || !targetStepId) {
        showAlert('Please fill in all required transition fields.', 'warning');
        return;
    }

    const transitionPayload = {
        workflow_id: workflowId,
        from_step: currentStep.id,
        to_step: targetStepId,
        name: transitionLabel,
        comment: description,
        approval_needed: approvalNeeded,
    };

    try {
        let response;
        if (transitionId) {
            // Update existing transition
            response = await fetch(getBaseURL('contract') + 'contract_workflows/update_transition/' + transitionId, {
                method: 'POST', // or 'PUT'
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(transitionPayload),
            });
        } else {
            // Add new transition
            response = await fetch(getBaseURL('contract') + 'contract_workflows/add_transition', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(transitionPayload),
            });
        }

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Server error: ${response.status} - ${errorText}`);
        }

        await fetchWorkflowData(); // Re-fetch data to update the view
        renderTransitionsInModal(); // Re-render the list of transitions in the modal
        showAlert(`Transition ${transitionId ? 'updated' : 'added'} successfully!`, 'success');
    } catch (error) {
        console.error("Could not save transition:", error);
        showAlert(`Failed to save transition. ${error.message}`, 'danger');
    }
}

// Function to edit an existing transition
function editTransition(transitionId) {
    const currentStep = workflowData.steps.find(step => step.id == currentEditingStepId);
    if (!currentStep) return;

    const transitionData = currentStep.transitions.find(t => t.id == transitionId);
    if (!transitionData) {
        showAlert('Transition not found for editing.', 'danger');
        return;
    }
    addTransitionForm(transitionData);
}

// Function to delete a transition
async function deleteTransition(transitionId) {
    if (confirm('Are you sure you want to delete this transition?')) {
        try {
            const response = await fetch(getBaseURL('contract') + 'contract_workflows/delete_transition/' + transitionId, {
                method: 'POST', // or 'DELETE' depending on your API
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Server error: ${response.status} - ${errorText}`);
            }
            await fetchWorkflowData(); // Re-fetch all data
            renderTransitionsInModal(); // Re-render the list
            showAlert('Transition deleted successfully!', 'success');
        } catch (error) {
            console.error("Could not delete transition:", error);
            showAlert(`Failed to delete transition. ${error.message}`, 'danger');
        }
    }
}

// Initial setup when DOM is loaded using jQuery's ready
jQuery(document).ready(function() {
    populateStepIconDropdown(); // Populate the main step icon dropdown
    fetchWorkflowData(); // Start by fetching the data

    // Add event listeners for the main form
    jQuery('#addStepForm').on('submit', handleAddStepFormSubmit);

    // Event delegation for dynamically added checklist and action buttons
    jQuery('#checklistItemsContainer').on('click', '.remove-checklist-item', function() {
        removeChecklistItemInput(this);
    });

    jQuery('#actionButtonsContainer').on('click', '.remove-action-button', function() {
        removeActionButtonInput(this);
    });

    // Add new checklist item button
    jQuery('#addChecklistItemBtn').on('click', function() {
        addChecklistItemInput();
    });

    // Add new action button
    jQuery('#addActionButtonBtn').on('click', function() {
        addActionButtonInput();
    });

    // Bootstrap 4 modals: No specific jQuery event listeners needed for Bootstrap 4 modal show/hide here,
    // as Bootstrap handles them internally on the element.
});