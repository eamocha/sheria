var accountNumberPrefixesPerEntity = {};
function setAccountNumberPrefix() {
    jQuery.ajax({
        url: getBaseURL('money') + 'accounts/set_account_number_prefix',
        type: "GET",
        data: {return_html: true},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var accountNbPrefix = "#account-nb-prefix-for-account-types";
                if (jQuery(accountNbPrefix).length <= 0) {
                    jQuery("<div id='account-nb-prefix-for-account-types'></div>").appendTo("body");
                    var accountNbPrefixForm = jQuery(accountNbPrefix);
                    accountNbPrefixForm.html(response.html);
                    commonModalDialogEvents(accountNbPrefixForm, setAccountNumberPrefixSubmit);
                    initializeModalSize(accountNbPrefixForm);
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function setAccountNumberPrefixSubmit(container) {
    var formData = jQuery("form#account-number-prefix-form", container).serialize();
    jQuery.ajax({
        url: getBaseURL('money') + 'accounts/set_account_number_prefix',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            }
            if (typeof response.validation_errors !== 'undefined') {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function inheritAccountNumberPrefixes(container) {
    jQuery('#inherit-from-other-organizations-link', container).addClass('d-none');
    jQuery('#inherit-from-other-organizations-container', container).removeClass('d-none');
    jQuery('#organizations', container).selectpicker().off().on('change', function () {
        var org = this.value;
        if (org !== '') {
            if (typeof accountNumberPrefixesPerEntity[org] !== 'undefined') {
                jQuery('tr[id^=account-number-prefix-]', container).each(function () {
                    jQuery('input#prefix', this).val(accountNumberPrefixesPerEntity[org][jQuery('#account-type', this).val()]);
                });
            } else {
                pinesMessage({ty: 'warning', m: _lang.noData});
            }
        }
    });
}
function hideInheritAccountNumberContainer(container) {
    jQuery('#organizations', container).val('').selectpicker('destroy');
    jQuery('#inherit-from-other-organizations-link', container).removeClass('d-none');
    jQuery('#inherit-from-other-organizations-container', container).addClass('d-none');
}