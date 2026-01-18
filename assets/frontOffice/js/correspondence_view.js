function showRelevantFields() {
    const updateType = document.getElementById('updateType').value;
    const dynamicFields = document.getElementById('dynamicFields');
    const fileUploadGroup = document.getElementById('fileUploadGroup');
    
    // Clear previous dynamic fields
    dynamicFields.innerHTML = '';
    
    // Hide file upload by default (we'll show it for document requests)
    fileUploadGroup.style.display = 'none';
    
    switch(updateType) {
        case 'status':
            dynamicFields.innerHTML = `
                <div class="form-group">
                    <label for="statusSelect">New Status</label>
                    <select class="form-control" id="statusSelect" required>
                        <option value="">Loading...</option>
                    
                    </select>
                </div>
            `;
            loadStatusSelectOptions();
            break;
        case 'reassign':
            dynamicFields.innerHTML = `
        <div class="form-group">
            <label for="providerGroupSelect">Provider Group</label>
            <select id="providerGroupSelect" class="form-control" required>
                <option value="">Loading...</option>
            </select>
        </div>
        <div class="form-group">
            <label for="assigneeSelect">Assign To</label>
            <select id="assigneeSelect" class="form-control" required>
                <option value="">Select User</option>
            </select>
        </div>
    `;
            loadProviderGroups();
            break;
        case 'link-correspondence':
            dynamicFields.innerHTML = `
                <div class="form-group">
                <input type="hidden" id="relatedCorrespondenceId" value="">
                    <label for="relatedCorrespondenceName">Related Correspondence ID</label>
                    <input type="text" class="form-control lookup" id="relatedCorrespondenceName" placeholder="Enter Correspondence ID" required>
                </div>
                
            `;
            break;
        case 'document':
                      
            dynamicFields.innerHTML = `
                <div class="form-group">
                    <label for="documentType">Document Type</label>
                    <select class="form-control" id="documentType" required>
                        <option value="">Select Document Type</option>
                        
                    </select>
                </div>
            `;
            // Show file upload for document requests
            fileUploadGroup.style.display = 'block';
            loadDocumentTypesOptions();
            break;
        case 'task':
            // Hide the main form and show task pane
            document.getElementById('updateForm').style.display = 'none';
            document.getElementById('taskPane').style.display = 'block';
            break;
        // Note type doesn't need additional fields
        case 'note':
        default:
            break;
    }
}

function loadProviderGroups() {
    jQuery.ajax({
        url: getBaseURL() + 'conveyancing/get_provider_groups',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const providerGroupSelect = document.getElementById('providerGroupSelect');
            providerGroupSelect.innerHTML = '<option value="">Select Provider Group</option>';
            if (response.assigned_teams) {
                Object.entries(response.assigned_teams).forEach(([id, name]) => {
                    providerGroupSelect.innerHTML += `<option value="${id}">${name}</option>`;
                });
            }
        },
        error: function() {
            alert('Failed to load provider groups');
        }
    });

    // Attach change event to load users when a group is selected
    jQuery(document).off('change', '#providerGroupSelect').on('change', '#providerGroupSelect', function() {
        const groupId = this.value;
        loadProviderGroupUsers(groupId);
    });
}

function loadStatusSelectOptions() {
    jQuery.ajax({
        url: getBaseURL() + 'front_office/get_status_options',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            
            const statusSelect = document.getElementById('statusSelect');
            if (!statusSelect) return;
            statusSelect.innerHTML = '<option value="">Select Status</option>';
            if (response.statusOptions) {
                Object.entries(response.statusOptions).forEach(([id, name]) => {
                    statusSelect.innerHTML += `<option value="${id}">${name}</option>`;
                });
            }
        },
        error: function() {
            defaultAjaxJSONErrorsHandler();
        }
    });
    // Attach change event to load users when a group is selected
    jQuery(document).off('change', '#statusSelect').on('change', '#statusSelect', function() {
        const status = this.value;
        if (status === 'other') {
            const otherStatus = prompt('Please enter the custom status:');
            if (otherStatus) {
                this.value = otherStatus;
            } else {
                this.value = ''; // Reset if no input
            }
        }
    }
    );
}
//load document types from the database
function loadDocumentTypesOptions() {
    jQuery.ajax({
        url: getBaseURL() + 'front_office/get_document_types_options',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const documentTypeSelect = document.getElementById('documentType');
            if (!documentTypeSelect) return;
            documentTypeSelect.innerHTML = '<option value="">Select Document Type</option>';
            if (response.document_types) {
                Object.entries(response.document_types).forEach(([id, name]) => {
                    documentTypeSelect.innerHTML += `<option value="${id}">${name}</option>`;
                });
            } else {
                console.error('Invalid document types response:', response);
                documentTypeSelect.innerHTML = '<option value="">No Document Types Available</option>';
            }
            
        },
        error: function() {
            errorHandler = defaultAjaxJSONErrorsHandler;
                     
        }
    });
    // Attach change event to load users when a group is selected
    jQuery(document).off('change', '#documentType').on('change', '#documentType', function() {
        const selectedType = this.value;
        if (selectedType === 'other') {
            const otherType = prompt('Please enter the custom document type:');
            if (otherType) {
                this.value = otherType;
            } else {
                this.value = ''; // Reset if no input
            }
        }
    });
}

function loadProviderGroupUsers(groupId) {
    const assigneeSelect = document.getElementById('assigneeSelect');
    assigneeSelect.innerHTML = '<option value="">Loading...</option>';
    if (!groupId) {
        assigneeSelect.innerHTML = '<option value="">Select User</option>';
        return;
    }
    jQuery.ajax({
        url: getBaseURL() + 'conveyancing/get_provider_group_users/' + groupId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            assigneeSelect.innerHTML = '<option value="">Select User</option>';
            if (response.usersProviderGroup) {
                Object.entries(response.usersProviderGroup).forEach(([id, name]) => {
                    assigneeSelect.innerHTML += `<option value="${id}">${name}</option>`;
                });
            }
        },
        error: function() {
            assigneeSelect.innerHTML = '<option value="">Failed to load users</option>';
        }
    });
}

function hideTaskPane() {
    // Show the main form and hide task pane
    document.getElementById('updateForm').style.display = 'block';
    document.getElementById('taskPane').style.display = 'none';
    // Reset the select to "Note"
    document.getElementById('updateType').value = 'note';
}

function loadActivitiesLog(correspondence_id) {
    jQuery('#loader-global').show();
    jQuery.ajax({
        url: getBaseURL() + 'front_office/get_activities_log' + '/' + correspondence_id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            let tbody = jQuery('#activity-log tbody');
            tbody.empty();
            
            if (response.length > 0) {
                response.forEach(function(activity) {
                    let row = `
                        <tr>
                            <td>${activity.createdOn}</td>
                            <td>${escapeHtml(activity.createdBy)} 
                              ${activity.createdBy_role ? '('+activity.createdBy_role+')' : ''}
                            </td>
                            <td>${escapeHtml(activity.action)}</td>
                            <td>
                                ${escapeHtml(activity.details)}
                                ${activity.remarks ? '<br><small class="text-muted">'+escapeHtml(activity.remarks)+'</small>' : ''}
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="4" class="text-center">No activities found</td></tr>');
            }
        },
        error: defaultAjaxJSONErrorsHandler,
        complete: function() {
            jQuery('#loader-global').hide();
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB') + ' ' + date.toLocaleTimeString('en-GB', {hour: '2-digit', minute:'2-digit'});
}

function escapeHtml(unsafe) {
    return unsafe ? unsafe.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;") : '';
}

/**
 *
 * @param {string} updateType
 * @param {object} params
 * @returns {void}
 */
function add_note_update(updateType, params) {
    const formData = new FormData();
    formData.append('updateType', updateType);

    // Append params as individual fields
    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            formData.append('params[' + key + ']', params[key]);
        }
    }

    // If there's a file input, append the file
    if (params.fileInput && params.fileInput !== undefined && params.fileInput.files.length > 0) {
        formData.append('attachment', params.fileInput.files[0]);
    }

    jQuery.ajax({
        url: getBaseURL() + 'front_office/add_note_update',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false, 
        contentType: false, 
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                } else {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                }
                // Optionally reload logs or UI here
                loadActivitiesLog(correspondenceId);
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.failedToSaveUpdate});
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
        complete: function () {
            jQuery('#loader-global').hide();
        }
    });
}

/*
* @param {integer} id
* @param {string} module        
* @param {integer} moduleId
* @param {string} lineage
* @param {string} extension
* @returns {void}
*/
function preview_attachment(id, module, moduleId, lineage, extension) {
    moduleController = module;
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/preview_document',
        method: 'post',
        data: {id: id},
        dataType: 'json',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var viewDocDialogId = "#document-view-dialog";
                if (jQuery(viewDocDialogId).length <= 0) {
                    jQuery('<div id="document-view-dialog"></div>').appendTo("body");
                    var viewDocDialog = jQuery(viewDocDialogId);
                    viewDocDialog.html(response.html);
                    jQuery('.file-container', viewDocDialog).addClass('loading');
                    initializeModalSize(viewDocDialogId, 0.9, 0.7);
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery('.modal', viewDocDialogId).modal('hide');
                        }
                    });

                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(jQuery(viewDocDialog));
                    });

                    if (response.image_document) {
                        var fileContainer = jQuery('.file-container', viewDocDialog);
                        fileContainer.html(response.image_document);
                    } else {
                        var fileContainer = jQuery('.file-container', viewDocDialog);
                        if (fileContainer.length > 0) {
                            if (!detectIE()) {
                                fileContainer.html('<embed src="javascript:void(0);">');
                                fileContainer.find('embed').attr('src', response.document.url);
                            } else {
                                fileContainer.html('<iframe src="javascript:void(0);"></iframe>');
                                fileContainer.find('iframe').attr('src', response.document.url);
                            }
                        }
                    }
                    jQuery('#title', viewDocDialog).text(response.document.full_name);
                    jQuery('.modal', viewDocDialog).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
    });
}

function download_attachment(id, newest_version, module, controller) {
    module = module || false;
    controller = controller || moduleController;
    newest_version = newest_version || false;
    if (controller) {
        var downloadUrl = (module && module !== 'core' ? getBaseURL(module) : getBaseURL()) + controller + '/download_file/' + id + '/' + newest_version;
        window.location = downloadUrl;
    }
}

// Load document types on page load
// Call this when page loads
jQuery(document).ready(function() {
    const correspondenceId = jQuery('#item-id').val();
    const correspondenceTypeId = jQuery('#correspondence_type_id').val();

    // Add the new option if not already present
    const updateTypeSelect = document.getElementById('updateType');
    if (updateTypeSelect && !updateTypeSelect.querySelector('option[value="link-correspondence"]')) {
        const option = document.createElement('option');
        option.value = 'link-correspondence';
        option.textContent = 'Link related Correspondence';
        updateTypeSelect.appendChild(option);
    }
//load activities when ready
    loadActivitiesLog(correspondenceId);

    // Handle notes form submission
    document.getElementById('updateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const updateType = document.getElementById('updateType').value;
        const details = document.getElementById('updateDetails').value.trim();
        // get the value of send_notifications_email checkbox
        const send_notifications_email = document.getElementById('send_notifications_email').checked?1:0;
        if (!updateType || !details) {
            pinesMessage({ty: 'error', m: _lang.feedback_messages.requiredFormFields});
            return;
        }

        switch(updateType) {
            case 'status':
                let newStatus = document.getElementById('statusSelect').value;
                add_note_update(updateType, {
                    correspondence_id: correspondenceId,
                    details: details,
                    status: newStatus,
                    send_notifications_email: send_notifications_email
                });
                break;
            case 'note':
                add_note_update(updateType, {
                    correspondence_id: correspondenceId,
                    details: details,
                    send_notifications_email: send_notifications_email
                });
                break;
            case 'reassign':
                let providerGroupId = document.getElementById('providerGroupSelect').value;
                let assigneeId = document.getElementById('assigneeSelect').value;
                add_note_update(updateType, {
                    correspondence_id: correspondenceId,
                    provider_group_id: providerGroupId,
                    assignee_id: assigneeId,
                    details: details,
                    send_notifications_email: send_notifications_email
                });
                break;
            case 'link-correspondence':
                let relatedId = document.getElementById('relatedCorrespondenceId').value;
                let relatedCorrespondenceName = document.getElementById('relatedCorrespondenceName').value.trim();

                add_note_update(updateType, {
                    correspondence_id: correspondenceId,
                    related_correspondence_id: relatedId,
                    related_correspondence_name: relatedCorrespondenceName,
                    details: details,
                    send_notifications_email: send_notifications_email
                });
                break;
            case 'document':
                let documentType = document.getElementById('documentType').value;
                let fileInput = document.getElementById('updateFile');
                add_note_update(updateType, {
                    correspondence_id: correspondenceId,
                    details: details,
                    document_type: documentType,
                    fileInput: fileInput,
                    send_notifications_email: send_notifications_email
                });
                fetchAndDisplayCorrespondenceDocuments(correspondenceId)
                break;
        }

        this.reset();
        document.querySelector('.custom-file-label').textContent = 'Choose file...';
    });

    // Show selected file name
    document.getElementById('updateFile').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file...';
        document.querySelector('.custom-file-label').textContent = fileName;
    });
});