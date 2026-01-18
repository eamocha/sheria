$(document).ready(function() {
    // Initialize tooltips
    jQuery('[data-toggle="tooltip"]').tooltip();

    // Handle file input label
    jQuery('.custom-file-input').on('change', function() {
        let fileName = jQuery(this).val().split('\\').pop();
        jQuery(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Show edit modal when edit button is clicked
    jQuery('#editRequestBtn').click(function() {
       // jQuery('#requestDetailsModal').modal('hide');
    //    jQuery('#editRequestModal').modal('show');
        fetchLegalOpinionItem(jQuery('#requestDetailsModal').data('request-id'));
    });

    // Handle row click to show details
    jQuery('.request-row').click(function(e) {
        // Prevent triggering when clicking on action buttons
        if (!jQuery(e.target).closest('button').length) {
            const requestId = jQuery(this).data('request-id');
            // Here you would typically load the request details via AJAX
            console.log('Loading details for request ID:', requestId);
        }
    });
});
function activateTabs() {
    jQuery('#opinions-table').DataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
    setActiveTab('contracts');
}
function addLegalOpinionRequest(module, id, callback) {
    callback = callback || false;
    id = id || 0;

    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL(module) + 'legal_opinions/add',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    // Remove any existing modal to avoid duplicates
                    jQuery('#addRequestModal').remove();

                    // Append the new modal to the body
                    jQuery('body').append(response.html);

                    // Ensure the modal structure is valid
                    const modalContainer = jQuery('#addRequestModal');
                    if (!modalContainer.find('.modal-dialog').length) {
                        console.error('Invalid modal structure in response.html');
                        return;
                    }

                    // Initialize modal size and other components
                    initializeModalSize(modalContainer);
                    commonModalDialogEvents(modalContainer)
                    initTinymyce('background_info', "#addRequestModal", "customer-portal");
                    initTinymyce('detailed_info', "#addRequestModal", "customer-portal");
                    initTinymyce('legal_question', "#addRequestModal", "customer-portal");

                    // Attach event to save button
                    jQuery("#save-opinion-btn", modalContainer).click(function () {
                        submitLegalOpinionForm(module, modalContainer, id, callback);
                    });

                    // Properly initialize and show the modal
                    modalContainer.modal({
                        backdrop: 'static', // Prevent closing by clicking outside
                        keyboard: false    // Prevent closing with the Esc key
                    }).modal('show');

                    
                } else {
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.conveyancingInstrumentError });
                }
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ ty: 'warning', m: response.display_message });
                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function submitLegalOpinionForm(module, container, id, opinionCallBack) {
    id = id || 0;

    // Retrieve the form element
    var formElement = document.getElementById(jQuery("form#opinion-form", container).attr('id'));
    var formData = new FormData(formElement);

    // Ensure hidden fields are included
    formData.set('id', jQuery('#id', formElement).val() || id);
    formData.set('archived', jQuery('input[name="archived"]', formElement).val() || '');

    // Retrieve TinyMCE content and append it to the FormData
    if (tinymce.get('background_info')) {
        formData.set('background_info', tinymce.get('background_info').getContent());
    }
    if (tinymce.get('detailed_info')) {
        formData.set('detailed_info', tinymce.get('detailed_info').getContent());
    }
    if (tinymce.get('legal_question')) {
        formData.set('legal_question', tinymce.get('legal_question').getContent());
    }

    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: getBaseURL(module) + 'legal_opinions/' + (id ? 'edit/' + id : 'add'),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (!response.cloned && typeof opinionCallBack === "function") {
                    opinionCallBack();
                }

                var msg = _lang.feedback_messages.addedNewOpinionSuccessfully.sprintf(['<a href="' + getBaseURL(module) + 'legal_opinions/index/' + response.id + '">' + response.opinion_code + '</a>']);
                pinesMessage({ ty: 'success', m: id ? _lang.feedback_messages.updatesSavedSuccessfully : msg });

                // Hide the modal and wait for it to be fully hidden before removing it
                container.modal('hide').on('hidden.bs.modal', function () {
                    jQuery('#addRequestModal').remove(); // Remove the modal from DOM
                    jQuery('.modal-backdrop').remove(); // Remove any remaining backdrop
                });
            } else {
                displayValidationErrors(response.validationErrors, container);
            }
        },
        complete: function () {
            jQuery('.save-opinion-btn').removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function commonModalDialogEvents(legalOpinionContainer){
    setDatePicker('#due_date', legalOpinionContainer);
    setDatePicker('#form-due-date', legalOpinionContainer);
 
}
// function to fetch and display legal opinions on a modal

function fetchLegalOpinionItem(id) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('customer-portal') + 'legal_opinions/fetch_legal_opinion_item/' + id,
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                // Remove any existing modal to avoid duplicates
                jQuery('#requestDetailsModal').remove();

                // Append the new modal to the body
                jQuery('body').append(response.html);
            let modalContainer = jQuery('#requestDetailsModal');
                  initializeModalSize(modalContainer);
                    commonModalDialogEvents(modalContainer)
                // Initialize the modal
                jQuery('#requestDetailsModal').modal('show');
            } else {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.conveyancingInstrumentError });
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
///function to save opinion comment
function addCommentFromCP(id) { 
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('customer-portal') + 'legal_opinions/add_comment/' + id,
        type: 'POST',   
        data: {
            comment: jQuery('#newComment').val(),
            opinion_id: id
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },  
        success: function (response) {
            if (response.result) {
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.commentAddedSuccessfully });
                jQuery('#newComment').val(''); // Clear the comment input
                // Optionally, you can refresh the comments section or append the new comment
                jQuery('#commentsSection').append(response.commentHtml); // Assuming response.commentHtml contains the new comment HTML
            } else {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.errorAddingComment });
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        }
    });
}