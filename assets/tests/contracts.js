const approvalStatuses = ['awaiting_approval', 'awaiting_revision'];
const signatureStatuses = ['awaiting_signature', 'partially_signed'];

document.addEventListener('DOMContentLoaded', () => {
    const dataContainer = document.getElementById('contract-detail-view');
  const   contract_id = dataContainer.dataset.contractid;
    loadContractData(contract_id);
});

  //   // var viewableExtensions = [ { extension: 'pdf' }, { extension: 'doc' }, { extension: 'docx' },    { extension: 'xls' },    { extension: 'xlsx' },    { extension: 'png' },    { extension: 'jpg' },    { extension: 'jpeg' },    { extension: 'txt' }];

// Status to badge class mapping
const statusToBadgeClass = {
    'not_started': 'secondary',  
    'pending': 'warning',     
    'active': 'primary',          
    'in_progress': 'info',        
    'completed': 'success',       
    'returned': 'warning',        
    'rejected': 'danger',         
    'skipped': 'dark',            
    'on_hold': 'light'            
};


function loadContractData(contract_id) {
   
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/test/'+contract_id,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {      
           jQuery('#loader-global').show();
        },
        success: function(response) {
            if (response && response.success) {
                updateContractUI(response.data);
            } else {
                showError(contract_id,'Invalid data received from server');
            }
        },
        error: function(xhr, status, error) {
            showError(contract_id,'Failed to load contract data: ' + error);
        },
        complete: function() {
             jQuery('#loader-global').hide();
        }
    });
}

function updateContractUI(data) {
    // Update last updated date
    //jQuery('#last-updated').text('Last updated: ' + formatDate(data.last_updated));
    
    // Render workflow steps
       const steps = data.workflow_steps || [];
    const numberOfSteps = data.number_of_steps ?? steps.length;
       
    // NO WORKFLOW STEPS → SHOW ACTION BUTTONS
    if (numberOfSteps === 0) {
        renderNoWorkflowActions(data.contract);
        updateAttachments(data.attachments || []);
        return;
    }
     renderWorkflowSteps(data.workflow_steps);
    
    
    // Update progress
   // updateProgressBar(data.contract_summary.progress, data.workflow_steps);
    // Update attachments
  
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
    console.log('Rendering step:', JSON.stringify(step));
    const stepId = step.step_id;
    const status = step.status.toLowerCase();
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
            <i class="fa ${step.step_icon}"></i>
        </div>
    `);
    

    // Render documents (may be multiple)
    const docs = Array.isArray(step.document_link) ? step.document_link : [];

const docsHtml = docs.length > 0 ? docs.map(doc => `
    <div class="document-item d-flex align-items-center mb-2">
        <i class="fa ${getFileIconClass(doc.extension)} mr-2" aria-hidden="true"></i>
        <span class="document-name mr-3"><a href="javascript:downloadFile(${doc.id}, true, 'contract');">${doc.full_name || doc.name}</a></span>
        
       

        <button type="button" class="btn btn-sm btn-outline-secondary" title="Edit"
                onclick="editDocument(${doc.id}, '${doc.module}', ${doc.module_record_id}, '${doc.parent_lineage}', '${doc.extension}')">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </button>
    </div>
`).join('') : '<div class="text-muted small">No documents</div>';

    const card = jQuery(`
        <div class="card step-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">${step.title}</h5>
                <span class="badge badge-${badgeClass} text-white">${step.status}</span>
            </div>
            <div class="card-body">
                <p><strong>Responsibility:</strong> ${step.responsibility}</p>
                <p><strong>Activity:</strong> ${step.activity}</p>
                <p><strong>Output:</strong> ${step.output}</p>
                ${step.description ? `<p class="text-muted small mt-2">${step.description}</p>` : ''}
                <div class="mt-3">
                    <h6 class="mb-2">Documents</h6>
                    ${docsHtml}
                </div>
            </div>
            <div class="card-footer text-right"></div>
        </div>
    `);
    
    // Add main actions to footer
    const footer = card.find('.card-footer');
    addMainActionsToStep(footer, stepId, status, step.main_actions);

    
    // Add dropdown to footer
    addDropdownToStep(footer, stepId, step.actions);
    
    // Add card to step
    stepElement.append(card);
    
    return stepElement;
}

function addMainActionsToStep(footer, stepId, status, mainActions = []) {
    // Allow actions for active, pending, and on-hold statuses
    const allowedStatuses = ["active", "pending", "on_hold"];

    if (!allowedStatuses.includes(status) || !Array.isArray(mainActions) || mainActions.length === 0) {
        return;
    }

    mainActions.forEach(action => {
        footer.append(`
            <button class="btn btn-sm ${action.class || 'btn-outline-primary'} mr-2"
                    onclick="${action.onclick || ''}">
                ${action.label}
                ${action.icon ? `<i class="${action.icon} ml-1"></i>` : ''}
            </button>
        `);
    });
}


function addDropdownToStep(footer, stepId, actions) {
    if (!Array.isArray(actions) || actions.length === 0) return;
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
                <a class="dropdown-item" href="#" data-action="${action.action}" data-function="${action.function_name}" title="${action.action}">
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

function showError(contract_id,message) {
    jQuery('#workflow-steps-container').html(`
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            ${message}
            <button class="btn btn-sm btn-outline-secondary ml-3" onclick="loadContractData(${contract_id})">
                <i class="fa fa-refresh"></i> Retry
            </button>
        </div>
    `);
}

// Event Handlers
function attachEventHandlers() {
    jQuery('.dropdown-item').off('click').on('click', function(event) {
        handleDropdownItemClick.call(this, event);
    });

    // Open contract links safely without inline onclick
    jQuery('.open-contract-link').off('click').on('click', function(e) {
        e.preventDefault();
        const link = jQuery(this).data('link');
        console.log('Opening Contract:', link);
        if (link) {
            window.open(link, '_blank');
        }
    });
}

function handleDropdownItemClick(event) {
    event.preventDefault();
    const $el = jQuery(event.currentTarget);
    const action = $el.data('action');
    const function_name = $el.data('function');
    const stepId = $el.closest('.workflow-step').data('step-id');
    const contractId = jQuery('#contract-detail-view').data('contractid');
    console.log(`Action "${action}" clicked for step ${stepId}`);

    const callback = () => loadContractData(contractId);
    // Implement specific action handlers here
    switch(function_name) {
        case 'addTask':
           // contractTaskAddForm(contractId,stepId, false, callback) ;
            taskForm( ) ;
            break;
        case 'addReminder':
        reminderForm(false, false, false, contractId);
           // reminderForm(contractId,stepId, false, contractId, callback);
            break;
        case 'addNote':
            commentForm(stepId,contractId,callback);
            break;
        case 'reject':
            rejectStep(stepId,contractId);
            break;
        case 'complete':
            completeStep(stepId,contractId);
            break;
        case 'notify':
           // notifyStep(stepId,contractId);
           window.alert('Notify functionality not yet implemented');
            break;
        case 'returnToPrevious':
            const targetStepId = prompt('Enter the step ID to return to:');
            if (targetStepId) {
                returnToPrevious(stepId, targetStepId,contractId);
            }
            break;
            case 'requestClarification':
           // requestClarification(stepId,contractId);
              window.alert('Request Clarification functionality not yet implemented');
            break;
            case 'addSurety':
            addSurety(stepId,contractId);
            break;
            case 'addMilestone':
            addMilestone(stepId,contractId);
            break;
            case 'addComment':
            addComment(stepId,contractId);
            break;
            case 'uploadFile':
            uploadFile(stepId,contractId);
            break;
            case 'cancel':
          //  cancelStep(stepId,contractId);
         
            window.alert('Cancel not permitted at this time');
            break;
        case 'viewHistory':
            viewHistory(stepId,contractId);
            break;
            
        default:
            console.log(`No handler for action: ${action}`);
    }


   // alert(`Action "${action}" for function "${function_name}" triggered for step ${stepId}`);
}

function completeStep(stepId,contract_id) {
    console.log(`Completing step ${stepId}`);

    alert(`Step ${stepId} marked as complete`);
    loadContractData(contract_id);
}

function returnToPrevious(currentStepId, targetStepId,contract_id) {
    console.log(`Returning from step ${currentStepId} to step ${targetStepId}`);
    // In a real app, this would make an AJAX call to update the backend
    alert(`Returning to step ${targetStepId} for revisions`);
    loadContractData(contract_id);
}
function updateAttachments(attachments) {
    const container = jQuery('#attachments-list');
    container.empty();
    
    if (attachments.length === 0) {
        container.append('<tr><td colspan="2" class="text-center">No attachments found</td></tr>');
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
             
                <td class="attachment-actions">
                    <button class="btn btn-sm btn-outline-primary mr-1" onclick="downloadFile(${attachment.id}, true, 'contract'); return false;">
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

    if (!fileType || typeof fileType !== 'string') {
        return 'fa-folder text-warning'; // default generic file icon
    }

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


function renderNoWorkflowActions(contract) {
    const container = jQuery('#workflow-steps-container');
    container.empty();

    const contractId = contract.id;

    const html = `
        <div class="card border-info text-center">
            <div class="card-body">
                <h5 class="card-title">No active workflow</h5>
                <p class="text-muted mb-4">
                    This contract has no workflow steps. Choose an action to proceed.
                </p>

                <button class="btn btn-primary mr-2"
                    onclick="contractAmendmentForm(${contractId}, event); ">
                    <i class="fa fa-edit mr-1"></i> Create Amendment
                </button>

                <button class="btn btn-success"
                    onclick="contractRenewForm('${contractId}', event); ">
                    <i class="fa fa-refresh mr-1"></i> Renew Contract
                </button>
            </div>
        </div>
    `;

    container.append(html);
}
