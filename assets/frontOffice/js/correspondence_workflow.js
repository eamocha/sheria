               function renderTimeline(steps) {
                    let html = '';
                    // Find the first non-completed/cancelled/paused step as current
                    let currentIndex = steps.findIndex(step => ['pending','in progress','ongoing'].includes((step.workflow_status||'').toLowerCase()));
                    steps.forEach(function(step, idx) {
                        let status = (step.workflow_status || '').toLowerCase();
                        let statusClass = '';
                        if (status === 'completed') statusClass = 'completed';
                        else if (status === 'pending') statusClass = 'pending';
                        else if (status === 'in progress' || status === 'ongoing') statusClass = 'current';
                        else if (status === 'delayed') statusClass = 'delayed';
                        else statusClass = status;
                        html += `<div class="timeline-item ${statusClass}">
                            <strong>${step.step_name}</strong>
                                <div class="small text-muted">${step.date_actioned ? step.date_actioned : ''}${step.step_created_by_name ? ' ' + step.step_created_by_name : ''}</div>
                                    <div>${step.remarks ? step.remarks : ''}</div>
                                        ${statusClass === 'current' ? `<button class="btn btn-sm btn-primary mt-2" onclick="update_workflow_timeline('${step.id}')">Update</button>` : ''}
                                        </div>
                                        `;
                    });
                    $('#processTimeline').html(html);
                }

               
                function load_workflow_steps(){
                    const correspondenceId = $('#item-id').val();
                    const correspondenceTypeId = $('#correspondence_type_id').val();
                     $.ajax({
                        url: getBaseURL() + 'front_office/get_timeline',
                        type: 'POST',
                        data: { id: correspondenceId, correspondence_type_id: correspondenceTypeId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.workflow_steps) {
                                renderTimeline(response.workflow_steps);
                            } else {
                                $('#processTimeline').html('<div class="text-muted">No workflow steps found.</div>');
                            }
                        },
                        error: function() {
                            $('#processTimeline').html('<div class="text-danger">Failed to load workflow steps.</div>');
                        }
                    });
                                }

               

        function update_workflow_timeline(current_stage_id) {
    current_stage_id = (current_stage_id === undefined ? 1 : current_stage_id);
    var correspondence_id = jQuery('#item-id').val();
    var correspondence_type_id = jQuery('#correspondence_type_id').val();
    if (correspondence_id === '') {
        pinesMessage({ty: 'error', m: _lang.feedback_messages.conveyancingInstrumentError});
        return;
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'front_office/update_workflow_step/' + correspondence_id + '/' + correspondence_type_id + '/' + current_stage_id,
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    if (jQuery('#correspondence-timeline-container').length <= 0) {
                        jQuery('<div id="correspondence-timeline-container" class="primary-style"></div>').appendTo("body");
                        var container = jQuery('#correspondence-timeline-container');
                        container.html(response.html);
                        initializeModalSize(container, 0.4);
                        commonModalDialogEvents(container);

                        jQuery('#stageSelect').val(current_stage_id);
                        if (current_stage_id == 'general') {
                            jQuery('#stageSelectContainer').removeClass('d-none');
                        } else {
                            jQuery('#stageSelectContainer').addClass('d-none');
                        }

                        jQuery('#stageStatus').focus();

                        jQuery("#saveProgressBtn", container).click(function () {
                            updateStage(container);
                        });
                    }
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.conveyancingInstrumentError});
                }
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
// function to handle the update process

function updateStage(container) {
    
        const stageId = parseInt(document.getElementById('stageSelect').value);
        const stageText = document.getElementById('stageSelect').options[document.getElementById('stageSelect').selectedIndex].text;

        const status = document.getElementById('stageStatus').value;
        const details = document.getElementById('stageDetails').value.trim();
        
        if (!stageId || !status || !details) {
            alert('Please fill all required fields');
            return;
        }
        
        // Send update to server
        saveProgress(stageId,stageText, status, details,container)          
 
 }
function saveProgress(stageId, stageText, status, details, container) {
    const correspondenceId = document.getElementById('item-id').value;
    const correspondenceTypeId = document.getElementById('correspondence_type_id').value;
    
    jQuery.ajax({
        url: getBaseURL() + 'front_office/update_workflow_step',
        type: 'POST',   
        data: {
            correspondence_id: correspondenceId,
            correspondence_type_id: correspondenceTypeId,
            stage_id: stageId,
            stage_text: stageText,
            status: status,
            details: details
        },
        dataType: 'json',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) { 
            if (response.result) {
                document.getElementById('progressForm').reset();
                jQuery(".modal", container).modal("hide");   
                jQuery('#correspondence-timeline-container').remove();   
                jQuery('.modal-backdrop').remove();   
                load_workflow_steps();       

                // Remove any leftover modal-open class and inline styles from body
                jQuery('body').removeClass('modal-open').css('overflow', '');

                pinesMessage({ty: 'success', m: 'Workflow progress updated successfully.'});
                if (response.workflow_steps) {
                    renderTimeline(response.workflow_steps);
                }
            } else {
                pinesMessage({ty: 'error', m: response.message || 'Failed to update workflow progress.'});
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
            container.modal('hide');
            // Ensure scrolling is restored
            jQuery('body').removeClass('modal-open').css('overflow', '');
        },
        error: function () {
            pinesMessage({ty: 'error', m: 'Error updating workflow progress.'});        
        }
    }); 
} 
