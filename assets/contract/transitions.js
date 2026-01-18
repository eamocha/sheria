var container;
var availableUsers, availableEmails, REGEX_EMAIL, operators, fieldsDetails;
jQuery(document).ready(function () {
    container = jQuery('#workflow-transition-container');
    jQuery('.select-picker', container).selectpicker();
    jQuery("#form-submit", container).click(function () {
        transitionFormSubmit();
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            transitionFormSubmit();
        }
    });
    initializeSelectPermissions(container);
    initializeSelectNotifications(container);

});

function transitionFormSubmit() {
    var id = jQuery('#id', container).val() ? jQuery('#id', container).val() : false;
    var formData = jQuery('#workflow-transition-form', container).serializeArray();
    jQuery.ajax({
        url: getBaseURL('contract') + 'contract_workflows/' + (id ? 'edit_transition/' + id : 'add_transition'),
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('#form-submit', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                window.location = getBaseURL('contract') + 'contract_workflows/index#' + jQuery('#workflow-id', container).val();
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('#form-submit', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
    });
}
function toggleCheckbox(hiddenInput, checkBoxInput) {
    hiddenInput.val(checkBoxInput.is(':checked') ? 'yes' : 'no');
}