function invoiceTemplateAddForm(type) {
    jQuery.ajax({
        url: getBaseURL('money') + 'organization_invoice_templates/save',
        type: "POST",
        data: {return: 'html', action: 'add', type: type},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var invoiceTemplateInfoId = "#invoice-template-info-form-container";
                if (jQuery(invoiceTemplateInfoId).length <= 0) {
                    jQuery("<div id='invoice-template-info-form-container'></div>").appendTo("body");
                    var invoiceTemplateInfoContainer = jQuery(invoiceTemplateInfoId);
                    invoiceTemplateInfoContainer.html(response.html);
                    jQuery('.select-picker', invoiceTemplateInfoContainer).selectpicker();
                    commonModalDialogEvents(invoiceTemplateInfoContainer);
                      jQuery("#form-submit", invoiceTemplateInfoContainer).click(function () {
            invoiceTemplateAddFormSubmit(invoiceTemplateInfoContainer );
        });
          jQuery(invoiceTemplateInfoContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                          invoiceTemplateAddFormSubmit(invoiceTemplateInfoContainer);
                        }
                    });
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.invalid_record});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function invoiceTemplateAddFormSubmit(container) {
    var formData = jQuery('#invoice-template-info-form', container).serializeArray();
    formData.push({name: "action", value: 'add'});
    jQuery.ajax({
        url: getBaseURL('money') + 'organization_invoice_templates/save',
        type: "POST",
        data:formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                window.location = getBaseURL('money') + 'organization_invoice_templates/save/' + response.id;
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function invoiceTemplateRename(id, templateContainer) {
    jQuery.ajax({
        url: getBaseURL('money') + 'organization_invoice_templates/save/',
        type: "POST",
        data: {return: 'html', action: 'rename', 'id': id},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var invoiceTemplateNameId = "#invoice-template-name-form-container";
                if (jQuery(invoiceTemplateNameId).length <= 0) {
                    jQuery("<div id='invoice-template-name-form-container'></div>").appendTo("body");
                    var invoiceTemplateNameContainer = jQuery(invoiceTemplateNameId);
                    invoiceTemplateNameContainer.html(response.html);
                    commonModalDialogEvents(invoiceTemplateNameContainer);
                    jQuery("#form-submit", invoiceTemplateNameContainer).click(function () {
                        invoiceTemplateRenameFormSubmit(id, invoiceTemplateNameContainer, templateContainer);
                    });
                    jQuery(invoiceTemplateNameContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                                 e.preventDefault();
                          invoiceTemplateRenameFormSubmit(id, invoiceTemplateNameContainer, templateContainer);
                        }
                    });
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.invalid_record});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function invoiceTemplateRenameFormSubmit(id, container, templateContainer) {
    var formData = jQuery('#invoice-template-info-form', container).serializeArray();
    formData.push({name: "action", value: 'rename'});
    formData.push({name: "id", value: id});
    jQuery.ajax({
        url: getBaseURL('money') + 'organization_invoice_templates/save/',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                jQuery('.template-name', '#' + templateContainer).html(jQuery('#name', container).val());
                jQuery(".modal", container).modal("hide");
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function customizeTemplate(templateId) {
    window.location = getBaseURL('money') + 'organization_invoice_templates/save/' + templateId;
}

function changeDefaultTemplate(element, id) {
    var is_checked = jQuery(element).is(':checked');
    if(is_checked)
        jQuery(element).closest('.btn-switch').addClass('active');
    else
        jQuery(element).closest('.btn-switch').removeClass('active');
    jQuery.ajax({
        url: getBaseURL('money') + 'organization_invoice_templates/save/',
        type: "POST",
        data: {'id': id, 'action': (is_checked ? 'set_default' : 'remove_default')},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                setTimeout(function(){
                    window.location = getBaseURL('money') + 'organization_invoice_templates';
                }, 1000);
            } else {
                pinesMessageV2({ ty: 'error', m: response.validation_errors });
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}