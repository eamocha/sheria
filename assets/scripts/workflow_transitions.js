var availableUsers, availableEmails, REGEX_EMAIL;
//edit_trigger
function edit_trigger(transitionId, id,workflowId) {
    var url = getBaseURL() + 'triggers/add_in_transition';
    if (id != 0) {
        url = getBaseURL() + 'triggers/edit_in_transition/' + id +'/'+workflowId;//if trigger already saved ,
    }
    jQuery.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var triggerFormId = "#trigger-form-container";
                if (jQuery(triggerFormId).length <= 0) {
                    jQuery("<div id='trigger-form-container'></div>").appendTo("body");
                    var triggerFormContainer = jQuery(triggerFormId);
                    triggerFormContainer.html(response.html);
                    triggerFormEvents(triggerFormId, response, -1);
                    initializeModalSize(triggerFormContainer, 0.5, 0.5);
                    commonModalDialogEvents(triggerFormContainer)
                    var formObj = jQuery('#trigger-form');
                    formObj.validationEngine({ validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false });
                    jQuery("#submitFormBtn", triggerFormContainer).click(function () {
                        if (formObj.validationEngine('validate')) {
                            triggerFormEditSubmit(triggerFormContainer, transitionId, id);
                        }
                    });

                }
            } else {
                pinesMessage({ ty: 'error', m: _lang.invalid_record });
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function triggerFormEditSubmit(container, transitionId, triggerId) {
    var formData = jQuery('form#trigger-form', container).serialize();
    jQuery.ajax({
        url: getBaseURL() + 'triggers/edit_trigger/' + transitionId + '/' + triggerId,
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery('.inline-error', container).addClass('d-none');
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            if (response.result) {
                window.location = window.location.href;
            } else {
                if (response.message) {
                    pinesMessage({ ty: 'error', m: response.message });
                } else {
                    displayValidationErrors(response.validationErrors, container);
                }
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function triggerDelete(params) {
    jQuery.ajax({
        url: getBaseURL() + 'triggers/delete_trigger/' + params.id,
        dataType: 'json',
        data: { id: params.id },
        type: 'POST',
        success: function (response) {
            if (response.result) {
                jQuery('#trigger-' + parseInt(params.id)).remove();
                pinesMessage({ ty: 'success', m: _lang.deleteRecordSuccessfull });
            } else {
                pinesMessage({ ty: 'error', m: _lang.deleteRecordFailed });
            }
            jQuery('.modal', '.confirmation-dialog-container').modal('hide');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function showTriggerForm(id,workflowId) {
    var url = getBaseURL() + 'triggers/add_in_transition/'+workflowId;
    jQuery.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var triggerFormId = "#trigger-form-container";
                if (jQuery(triggerFormId).length <= 0) {
                    jQuery("<div id='trigger-form-container'></div>").appendTo("body");
                    var triggerFormContainer = jQuery(triggerFormId);
                    triggerFormContainer.html(response.html);
                    triggerFormEvents(triggerFormId, response, -1);
                    initializeModalSize(triggerFormContainer, 0.5, 0.5);
                    commonModalDialogEvents(triggerFormContainer)
                    var formObj = jQuery('#trigger-form');
                    formObj.validationEngine({ validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false });
                    jQuery("#submitFormBtn", triggerFormContainer).click(function () {
                        if (formObj.validationEngine('validate')) {
                            triggerFormSubmit(triggerFormContainer, id);
                        }
                    });

                }
            } else {
                pinesMessage({ ty: 'error', m: _lang.invalid_record });
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function triggerFormSubmit(container, transition_id) {
    var formData = jQuery('form#trigger-form', container).serialize();
    //-------------------addding request to backend 
    jQuery.ajax({
        url: getBaseURL() + 'triggers/add_trigger/' + transition_id,
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery('.inline-error', container).addClass('d-none');
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            if (response.result) {
                window.location = window.location.href;
            } else {
                if (response.message) {
                    pinesMessage({ ty: 'error', m: response.message });
                } else {
                    displayValidationErrors(response.validationErrors, container);
                }
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}//end of triggerFormSubmit

jQuery(document).ready(function () {
    var tabName = sessionStorage.getItem("tab");
    if (tabName) {
        sessionStorage.removeItem("tab");
        jQuery('#' + tabName + '-tab', '.tabs-container').click();
    }
    var formObj = jQuery('#workflowStatusTransitionForm');
    formObj.validationEngine({ validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false });
    jQuery('#submitFormBtn').click(function () {
        // if (formObj.validationEngine('validate')) {
        formObj.submit();
        //}
    });
    initializeSelectPermissions(jQuery('#workflowStatusTransitionForm'));
    initializeSelectNotifications(jQuery('#workflowStatusTransitionForm'));

    //add here button function of add new triggers pop up form 
});
