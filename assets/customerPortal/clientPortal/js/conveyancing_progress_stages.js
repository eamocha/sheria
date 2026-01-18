// Global variable to store the instrument ID

// Initialize the timeline with real data
function loadTimeline() { 
    fetch(getBaseURL() +`/conveyancing/get_timeline/${instrumentId}`)
        .then(response => response.json())
        .then(data => {
            renderTimeline(data);
        })
        .catch(error => console.error('Error loading timeline:', error));
}

// Initialize the timeline with dynamic data
function renderTimeline(stages) {
    const timeline = document.getElementById('processTimeline');
    timeline.innerHTML = '';
    
    stages.forEach(stage => {
        const statusClass = (stage.status || '').toLowerCase();
        const timelineItem = document.createElement('div');
        timelineItem.className = `timeline-item ${statusClass}`;
        timelineItem.dataset.stageId = stage.id;
        
        timelineItem.innerHTML = `
            <h6>${stage.title}</h6>
            <p class="text-muted small">${stage.date} ${stage.updatedBy ? 'by ' + stage.updatedBy : ''}</p>
            <p>${stage.details}</p>
            ${statusClass === 'current' ? '<button class="btn btn-sm btn-primary mt-2 update-stage-btn" data-toggle="modal" data-target="#updateProgressModal">Update</button>' : ''}
        `;
        
        timeline.appendChild(timelineItem);
    });
}

// Show modal with stage pre-selected
function showUpdateModal(stageId) {
   
    updateProcessTimeline(instrumentId, stageId);
  
}

// Save progress to backend
function saveProgress(stageId,stageText, status, details,container) {
     jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'conveyancing/update_instrument_stage_progress',
        type: 'POST',
        data: {
            instrument_id: instrumentId,
            stage_id: stageId,
            status: status,
            comments: details,
            stageText: stageText
        },
      
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                 loadTimeline();   
                   document.getElementById('progressForm').reset();
                 jQuery(".modal", container).modal("hide");   
                jQuery('#conveyancing-instrument-timeline-container').remove();   
                jQuery('.modal-backdrop').remove();     
                     // Remove any leftover modal-open class and inline styles from body
                jQuery('body').removeClass('modal-open').css('overflow', '');          
              
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
                 // Remove any leftover modal-open class and inline styles from body
                jQuery('body').removeClass('modal-open').css('overflow', '');
        },
        error: defaultAjaxJSONErrorsHandler
    });
    
}
function updateProcessTimelineG(instrument_id,general) {
    updateProcessTimeline(instrument_id,current_stage_id,general)
}

function updateProcessTimeline(instrument_id,current_stage_id) {
    current_stage_id = (current_stage_id === undefined ? 1 : current_stage_id);
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'conveyancing/get_process_stages'+'/'+instrument_id+'/'+current_stage_id,
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    if (jQuery('#conveyancing-instrument-timeline-container').length <= 0) {
                        jQuery('<div id="conveyancing-instrument-timeline-container" class="primary-style"></div>').appendTo("body");
                        var container = jQuery('#conveyancing-instrument-timeline-container');
                        container.html(response.html);
                        initializeModalSize(container,0.7);
                        commonModalDialogEvents(container);
                         
                        jQuery('#stageSelect').val(current_stage_id); 
                        if(current_stage_id=='general'){
                           jQuery('#stageSelectContainer').removeClass('d-none');
                        }else{
                            jQuery('#stageSelectContainer').addClass('d-none');
                        }

                         jQuery('#stageStatus').focus();
                        
                        jQuery("#saveProgressBtn", container).click(function () {
                             updateStage(container);
                        });
                      
                    }
                }else{
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.conveyancingInstrumentError});
                }
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

$(document).ready(function() {

    // Get instrument ID from the page
    instrumentId = document.getElementById('instrument-id').value;
    
    // Load initial timeline data
    loadTimeline();
    
    // Populate stage dropdown when modal opens
    jQuery('#updateProgressModal').on('show.bs.modal', function() {
        const stageSelect = document.getElementById('stageSelect');
        stageSelect.innerHTML = '<option value="">Select a stage</option>';
        
        // Get stages from backend
        fetch(getBaseURL() +`conveyancing/get_process_stages`)
            .then(response => response.json())
            .then(stages => {
    const stageArray = Array.isArray(stages) ? stages : (Array.isArray(stages.data) ? stages.data : []);
    stageArray.forEach(stage => {
        const option = document.createElement('option');
        option.value = stage.id;
        option.textContent = stage.name;
        stageSelect.appendChild(option);
    });
})
            .catch(error => console.error('Error loading stages:', error));
    });
    
    // Handle update button clicks
    document.addEventListener('click', function(e) { 
        if (e.target.classList.contains('update-stage-btn')) {
            const stageId = parseInt(e.target.closest('.timeline-item').dataset.stageId);
            showUpdateModal(stageId);
        }
    });
    
    
    
});
//update the process timeline stage
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

// Helper function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.prepend(alertDiv);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}