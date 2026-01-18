
const stepIcons = {
    1: 'fa fa-file-text-o',
    2: 'fa fa-user',
    3: 'fa fa-balance-scale',
    4: 'fa fa-handshake-o',
    5: 'fa fa-file-text-o'
};

// Status to badge class mapping
const statusToBadgeClass = {
    'pending': 'secondary',
    'active': 'primary',
    'completed': 'success'
};

function loadContractData(contract_id) {
   
    
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/test/'+contract_id,
        type: 'GET',
        dataType: 'json',
        beforesend: function() {      
            showLoading(true);
        },
        success: function(response) {
            if (response && response.success) {
                updateContractUI(response.data);
            } else {
                showError('Invalid data received from server');
            }
        },
        error: function(xhr, status, error) {
            showError('Failed to load contract data: ' + error);
        },
        complete: function() {
            showLoading(false);
        }
    });
}

function updateContractUI(data) {
    // Update last updated date
    //jQuery('#last-updated').text('Last updated: ' + formatDate(data.last_updated));
    
    // Render workflow steps
    renderWorkflowSteps(data.workflow_steps);
    
    
    // Update progress
   // updateProgressBar(data.contract_summary.progress, data.workflow_steps);
    // Update attachments
    console.log(data.attachments)
     updateAttachments(data.attachments || []);
}

function renderWorkflowSteps(steps) {
    const container = jQuery('#workflow-steps-container');
    container.empty();
    
    steps.forEach((step, index) => {
        const stepElement = createStepElement(step, index < steps.length - 1);
        container.append(stepElement);
    });
    
    // Reattach event listeners
    attachEventHandlers();
}

function createStepElement(step, hasConnector) {
    const stepId = step.step_id;
    const status = step.status;
    const badgeClass = statusToBadgeClass[status] || 'secondary';
    
    // Create main step container
    const stepElement = jQuery('<div>')
        .addClass(`workflow-step step-${status}`)
        .attr('data-step-id', stepId);
    
    // Add connector if needed
    if (hasConnector) {
        stepElement.append('<div class="workflow-connector"></div>');
    }
    
    // Add step icon
    stepElement.append(`
        <div class="step-icon">
            <i class="${stepIcons[stepId]}"></i>
        </div>
    `);
    
    // Create card
    const card = jQuery(`
        <div class="card step-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">${step.title}</h5>
                <span class="badge badge-${badgeClass} text-white">${step.badge_text}</span>
            </div>
            <div class="card-body">
                <p><strong>Responsibility:</strong> ${step.responsibility}</p>
                <p><strong>Activity:</strong> ${step.activity}</p>
                <p><strong>Output:</strong> ${step.output}</p>
                <p class="text-muted small mt-2">${step.description}</p>
                <p class="mt-3 mb-0">
                    <i class="fa fa-link mr-2 text-muted"></i>
                    <a href="#" class="text-primary" onclick="console.log('Opening Contract: ${step.document_link}'); return false;">
                        Contract Link: ${step.document_link}
                    </a>
                </p>
            </div>
            <div class="card-footer text-right"></div>
        </div>
    `);
    
    // Add main actions to footer
    const footer = card.find('.card-footer');
    addMainActionsToStep(footer, stepId, status);
    
    // Add dropdown to footer
    addDropdownToStep(footer, stepId, step.actions);
    
    // Add card to step
    stepElement.append(card);
    
    return stepElement;
}

function addMainActionsToStep(footer, stepId, status) {
    if (status !== 'active') return;
    
    const actions = {
        2: [
            { label: 'Return for Revisions', class: 'btn-outline-secondary', onclick: 'returnToPrevious(2, 1)' },
            { label: 'Approve', class: 'btn-primary', onclick: 'completeStep(2)', icon: 'fa fa-arrow-right' }
        ],
        3: [
            { label: 'Start Review', class: 'btn-success', onclick: 'completeStep(3)', icon: 'fa fa-play' }
        ],
        4: [
            { label: 'Send Contract', class: 'btn-primary', onclick: 'completeStep(4)', icon: 'fa fa-paper-plane' }
        ],
        5: [
            { label: 'Request Approval', class: 'btn-success', onclick: 'completeStep(5)', icon: 'fa fa-user-check' }
        ]
    };
    
    const stepActions = actions[stepId] || [];
    
    stepActions.forEach(action => {
        footer.append(`
            <button class="btn btn-sm ${action.class} mr-2" onclick="${action.onclick}">
                ${action.label} ${action.icon ? `<i class="${action.icon} ml-1"></i>` : ''}
            </button>
        `);
    });
}

function addDropdownToStep(footer, stepId, actions) {
    const dropdown = jQuery(`
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                id="dropdownMenuButton${stepId}" data-toggle="dropdown" aria-expanded="false">
                Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton${stepId}"></div>
        </div>
    `);
    
    const menu = dropdown.find('.dropdown-menu');
    
    actions.forEach(action => {
        if (action.divider) {
            menu.append('<div class="dropdown-divider"></div>');
        } else {
            menu.append(`
                <a class="dropdown-item" href="#" data-action="${action.action}" title="${action.action}">
                    <i class="${action.icon}"></i><span>${action.action}</span>
                </a>
            `);
        }
    });
    
    footer.append(dropdown);
}



function getRiskBadge(riskLevel) {
    const riskClasses = {
        'Low': 'badge-risk-low',
        'Medium': 'badge-risk-medium',
        'High': 'badge-risk-high'
    };
    const className = riskClasses[riskLevel] || 'badge-secondary';
    return `<span class="badge ${className}">${riskLevel}</span>`;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function updateProgressBar(progress, steps) {
    jQuery('#workflow-progress-bar')
        .css('width', progress + '%')
        .attr('aria-valuenow', progress);
    
    jQuery('#workflow-progress-text').text(progress + '%');
    
    // Update progress tooltip with current step
    const activeStep = steps.find(step => step.status === 'active');
    if (activeStep) {
        jQuery('#workflow-progress-bar').parent().attr('title', 
            'Current step: ' + activeStep.title);
    }
}

// UI Helper Functions
function showLoading(show) {
    if (show) {
        jQuery('#loader-global').show();
    } else {
        jQuery('#loader-global').hide();
    }
}

function showError(message) {
    jQuery('#workflow-steps-container').html(`
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            ${message}
            <button class="btn btn-sm btn-outline-secondary ml-3" onclick="loadContractData()">
                <i class="fa fa-refresh"></i> Retry
            </button>
        </div>
    `);
}

// Event Handlers
function attachEventHandlers() {
    jQuery('.dropdown-item').off('click').on('click', handleDropdownItemClick);
}

function handleDropdownItemClick(event) {
    event.preventDefault();
    const action = jQuery(this).data('action');
    const stepId = jQuery(this).closest('.workflow-step').data('step-id');
    console.log(`Action "${action}" clicked for step ${stepId}`);
    // Implement specific action handlers here
    alert(`Action "${action}" triggered for step ${stepId}`);
}

function completeStep(stepId) {
    console.log(`Completing step ${stepId}`);
    // In a real app, this would make an AJAX call to update the backend
    alert(`Step ${stepId} marked as complete`);
    loadContractData();
}

function returnToPrevious(currentStepId, targetStepId) {
    console.log(`Returning from step ${currentStepId} to step ${targetStepId}`);
    // In a real app, this would make an AJAX call to update the backend
    alert(`Returning to step ${targetStepId} for revisions`);
    loadContractData();
}
function updateAttachments(attachments) {
    const container = jQuery('#attachments-list');
    container.empty();
    
    if (attachments.length === 0) {
        container.append('<tr><td colspan="3" class="text-center">No attachments found</td></tr>');
        return;
    }
    
    attachments.forEach(attachment => {
        const iconClass = getFileIconClass(attachment.extension);
        const row = jQuery(`
            <tr>
                <td>
                    <i class="fa ${iconClass} attachment-icon mr-2"></i>
                    ${attachment.name}
                </td>
                <td>${attachment.type.toUpperCase()}</td>
                <td class="attachment-actions">
                    <button class="btn btn-sm btn-outline-primary mr-1" onclick="downloadAttachment('${attachment.name}')">
                        <i class="fa fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary mr-1" onclick="previewAttachment('${attachment.name}')">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAttachment('${attachment.name}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
        container.append(row);
    });
}

// Helper function for file icons
function getFileIconClass(fileType) {
    const iconMap = {
        'pdf': 'fa-file-pdf-o text-danger',
        'doc': 'fa-file-word-o text-primary',
        'docx': 'fa-file-word-o text-primary',
        'xls': 'fa-file-excel-o text-success',
        'xlsx': 'fa-file-excel-o text-success',
        'ppt': 'fa-file-powerpoint-o text-warning',
        'pptx': 'fa-file-powerpoint-o text-warning',
        'jpg': 'fa-file-image-o text-info',
        'jpeg': 'fa-file-image-o text-info',
        'png': 'fa-file-image-o text-info',
        'zip': 'fa-file-archive-o text-secondary',
        'rar': 'fa-file-archive-o text-secondary'
    };
    return iconMap[fileType.toLowerCase()] || 'fa-file-o';
}

// Attachment action handlers
function downloadAttachment(filename) {
    console.log('Downloading:', filename);
    alert('Download functionality would initiate for: ' + filename);
}

function previewAttachment(filename) {
    console.log('Previewing:', filename);
    alert('Preview would open for: ' + filename);
}

function deleteAttachment(filename) {
    if (confirm('Are you sure you want to delete ' + filename + '?')) {
        console.log('Deleting:', filename);
        alert(filename + ' would be deleted');
    
    }
}

// Upload button handler
jQuery(document).on('click', '#upload-new-btn', function() {
    alert('File upload dialog would open');
  
});


