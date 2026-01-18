jQuery(document).ready(function() {
    jQuery('.tooltip-title').tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
});

function externalApprovalFormSubmit(container, approve){
    jQuery('#status').val(approve ? 'approved' : 'rejected');
    var formData = jQuery("form#approval-form", container).serializeArray();
    var externalApprovalId = jQuery('#external-user-token-id', '.external-actions-container').val();
    var approvalKey = jQuery('#external-user-token', '.external-actions-container').val();
    var approvalStatusId = jQuery('#contract-approval-status-id', '#approval-form').val();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL('contract') + 'external_actions/approve_contract/' + externalApprovalId + '/' + approvalKey,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('hide');
            if (response.result) {
                jQuery('#external-approval-container').remove();
                jQuery('.external-actions-body').append(response.html);
            } else {
                displayValidationErrors(response.validation_errors, container);
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function displayValidationErrors(errors, container) {
    jQuery('.inline-error', container).removeClass('validation-error');
    jQuery('.input-warning', container).removeClass('input-warning');
    var selector;
    for (i in errors) {
        selector = jQuery("div", container).find("[data-field=" + i + "]").length > 0 ? jQuery("div", container).find("[data-field=" + i + "]") : (jQuery("td", container).find("[data-field=" + i + "]").length > 0 ? jQuery("td", container).find("[data-field=" + i + "]") : false);
        if (selector) {
            selector.removeClass('hide').html(errors[i]).addClass('validation-error');
            jQuery("input[data-field=" + i + "]", container).each(function () {
                if (this.value === '') {
                    jQuery(this).addClass('input-warning');
                }
            });
        } else {
            pinesMessage({ty: 'error', m: errors[i]});
        }

    }
}