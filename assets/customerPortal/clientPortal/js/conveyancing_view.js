
// Sample document types - replace with your actual document types
const documentTypes = [
    "Contract",
    "Title Deed",
    "Mortgage Agreement",
    "Inspection Report",
    "Disclosure Form"
];

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
                        <option value="">Select Status</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="paused">Paused</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            `;
            break;
            
        case 'reassign':
            
    // Show provider group and assignee fields
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
    // Load provider groups via AJAX
    loadProviderGroups();
    
            break;
            
        case 'document':
            let docOptions = documentTypes.map(doc => 
                `<option value="${doc.toLowerCase().replace(' ', '-')}">${doc}</option>`
            ).join('');
            
            dynamicFields.innerHTML = `
                <div class="form-group">
                    <label for="documentType">Document Type</label>
                    <select class="form-control" id="documentType" required>
                        <option value="">Select Document Type</option>
                        ${docOptions}
                    </select>
                </div>
            `;
            // Show file upload for document requests
            fileUploadGroup.style.display = 'block';
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
    $.ajax({
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
    $(document).off('change', '#providerGroupSelect').on('change', '#providerGroupSelect', function() {
        const groupId = this.value;
        loadProviderGroupUsers(groupId);
    });
}

function loadProviderGroupUsers(groupId) {
     const assigneeSelect = document.getElementById('assigneeSelect');
    assigneeSelect.innerHTML = '<option value="">Loading...</option>';
    if (!groupId) {
        assigneeSelect.innerHTML = '<option value="">Select User</option>';
        return;
    }
    $.ajax({
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

// In your script section
function loadActivitiesLog(instrument_id) {
    $('#loader-global').show();
    $.ajax({
        url: getBaseURL() + 'conveyancing/get_activities_log'+'/'+instrument_id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            let tbody = $('#activity-log tbody');
            tbody.empty();
            
            if (response.length > 0) {
                response.forEach(function(activity) {
                    let row = `
                        <tr>
                            <td>${formatDate(activity.createdOn)}</td>
                            <td>${escapeHtml(activity.performed_by_name)} 
                                ${activity.performed_by_role ? '('+activity.performed_by_role+')' : ''}
                            </td>
                            <td>${escapeHtml(activity.action)}</td>
                            <td>
                                ${escapeHtml(activity.activity_details)}
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
            $('#loader-global').hide();
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
 * @param {integer} id
 * @param {integer} moduleId
 * @param {string} lineage
 * @param {string} extension
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
    if (params.fileInput && params.fileInput.files.length > 0) {
        formData.append('attachment', params.fileInput.files[0]);
    }

    $.ajax({
        url: getBaseURL() + 'conveyancing/add_note_update',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false, 
        contentType: false, 
        beforeSend: function () {
            $('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
              if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                } else {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                }
                // Optionally reload logs or UI here
                loadActivitiesLog(instrumentId);
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
            $('#loader-global').hide();
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
    moduleController=module;
    jQuery.ajax({
        url: getBaseURL(module) + moduleController + '/preview_document' ,
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

                    if(response.image_document){
                        var fileContainer = jQuery('.file-container', viewDocDialog);
                        fileContainer.html(response.image_document);
                    } else{
                        var fileContainer = jQuery('.file-container', viewDocDialog);
                        if (fileContainer.length > 0) {
                            if(!detectIE()){
                                fileContainer.html('<embed src="javascript:void(0);">');
                                fileContainer.find('embed').attr('src', response.document.url);
                            } else{
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
    if(controller){
        var downloadUrl = (module && module !== 'core' ? getBaseURL(module) : getBaseURL()) + controller + '/download_file/' + id + '/' + newest_version;
        window.location = downloadUrl;
    }
}


// Call this when page loads
$(document).ready(function() {
    const instrumentId = $('#instrument-id').val();
   
    loadActivitiesLog(instrumentId);
    
// Handle notes form submission
document.getElementById('updateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const updateType = document.getElementById('updateType').value;
    const details = document.getElementById('updateDetails').value.trim();
    
    // Create update object based on type
    const update = { type: updateType, details: details };
    
    switch(updateType) {
        case 'status':
            update.newStatus = document.getElementById('statusSelect').value;
            add_note_update(updateType,{
                instrument_id: instrumentId,    details: details,    status: update.newStatus, file: update.file });
            break;
        case 'note':
            add_note_update(updateType,{
                instrument_id: instrumentId,    details: details });
        
            break;
        case 'reassign':
           let providerGroupId = document.getElementById('providerGroupSelect').value;
        let assigneeId = document.getElementById('assigneeSelect').value;
        add_note_update(updateType, {
        instrument_id: instrumentId,
        provider_group_id: providerGroupId,
        assignee_id: assigneeId,
        details: details
    });
            break;
            
        case 'document':
            update.documentType = document.getElementById('documentType').value;
            let fileInput = document.getElementById('uploadDoc');
         

            add_note_update(updateType,{
                instrument_id: instrumentId, details: details,document_type: update.documentType, fileInput: fileInput });
            break;
    } 
    
    console.log('Update to submit:', update);
   
    this.reset();
    document.querySelector('.custom-file-label').textContent = 'Choose file...';
});

// Show selected file name

    // $('.custom-file-input').on('change', function() {
    //     const fileName = $(this).val().split('\\').pop();
    //     $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    // });

    // document.getElementById('uploadDoc').addEventListener('change', function(e) {
    // const fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file...';
    // document.querySelector('.custom-file-label').textContent = fileName;});
}

);
