var oldForm;
jQuery(document).ready(function () {
    jQuery('tbody', '#custom-fields').sortable({
        update: function (event, ui) {
            var fieldsOrderData = [];
            jQuery("[id^=custom-field-order-]").each(function (index, obj) {
                fieldsOrderData.push({
                    'id': jQuery(this).attr('id').replace("custom-field-order-", ""),
                    'field_order': index
                });
            });
            jQuery.ajax({
                url: getBaseURL('contract') + 'custom_fields/set_fields_order',
                data: {
                    'fields_order_data': fieldsOrderData
                },
                dataType: 'JSON',
                type: 'POST',
                success: function (response) {
                    if (response.result) {
                        pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    } else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    });
});

function customFieldForm(model, id) {
    id = id || null;
    jQuery.ajax({
        url: getBaseURL(model) + 'custom_fields/' + (id ? 'edit/' + id : 'add/'),
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var customFieldContainerId = "#custom-field-container";
                if (jQuery(customFieldContainerId).length <= 0) {
                    jQuery("<div id='custom-field-container'></div>").appendTo("body");
                    var customFieldContainer = jQuery(customFieldContainerId);
                    customFieldContainer.html(response.html);
                    customFieldFormEvents(model, customFieldContainer, id);
                }
            }
            if (undefined !== response.html_types) {//custom fields per type
                jQuery('#model-types-container', customFieldContainer).html(response.html_types);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function customFieldFormEvents(model, container, id) {
    initializeModalSize(container);
    commonModalDialogEvents(container);
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            customFieldFormValidate(model, container, id);
        }
    });
    jQuery('.modal-body',container).on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery("#title", container).focus();
        oldForm = {
            category: jQuery('#model-category', container).serialize(),
            type: jQuery('#model-type', container).serialize()
        };
    });
    jQuery("#custom-field-save-btn", container).click(function () {
        customFieldFormValidate(model, container, id);
    });
    var listOptions = jQuery('#list-options', container).selectize({
        plugins: ['remove_button'],
        placeholder: 'options',
        delimiter: ',',
        persist: false,
        create: function (input) {
            return {
                value: input,
                text: input
            }
        }
    });

    var listOptionsSelectize = listOptions[0].selectize;
    jQuery('#type', container).selectpicker().change(function (eventData) {
        if (jQuery('#type', container).selectpicker('val') == 'list') {
            jQuery('#list-options-container').removeClass('d-none');
        } else {
            jQuery('#list-options-container').addClass('d-none');
            listOptionsSelectize.clear();
        }
    });
    jQuery('#other-lang').click(function () {
        if (jQuery('#other-lang-container').hasClass('d-none')) {
            jQuery('#other-lang-container').removeClass('d-none');
        } else {
            jQuery('#other-lang-container').addClass('d-none');
        }
    });
}

function customFieldFormValidate(model, container, id) {
    var formData = jQuery("form#custom-field-form", container).serializeArray();
    if (id) {//if edit legal case custom fields => check if this custom field used in cp or matter form
        var editedForm = {
            type: jQuery('#model-type', container).serialize()
        };
        if ((editedForm.type) && (oldForm.type !== editedForm.type)) {
            isCustomFieldUsed(id, formData);
        } else {
            customFieldFormSubmit(model, container, id);
        }
    } else {
        customFieldFormSubmit(model, container);
    }
}

function customFieldFormSubmit(model, container, id) {
    id = id || false;
    var formData = jQuery("form#custom-field-form", container).serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL(model) + 'custom_fields/' + (id ? 'edit/' + id : 'add'),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                updateTableRows(id ? 'edit' : 'add', response);
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.modal', container).modal('hide');
            } else {
                jQuery('.inline-error', container).removeClass('validation-error');
                if (response.hasOwnProperty('validationErrors')) {
                    for (i in response.validationErrors) {
                        jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
                        showMoreFields(jQuery('#custom-field-form'), jQuery('#other-lang-container', '#custom-field-form'));
                        if (jQuery("#" + i).val() == "") {
                            jQuery("#" + i).val(jQuery("#name_" + _lang.languageSettings['langName'].substring(0, 2)).val());
                        }
                    }
                    scrollToValidationError(container);
                } else {
                    pinesMessage({ty: 'error', m: _lang.saveRecordFailed.sprintf([_lang.customField])});
                    jQuery('.modal', container).modal('hide');
                }
            }
        }, complete: function () {
            jQuery('.btn-save', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteCustomField(customFieldId) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL('contract') + 'custom_fields/delete/' + customFieldId,
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                if (response.result) {
                    updateTableRows('delete', customFieldId);
                }
                pinesMessage({ty: response.feedbackMessage.ty, m: response.feedbackMessage.m});
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function updateTableRows(mode, data) {
    if (mode == 'delete') {
        customFieldId = data;
        jQuery('#custom-field-order-' + customFieldId).remove();
        if (jQuery('#custom-field-table-body').children().length == 0) {
            jQuery('#custom-fields').addClass('d-none');
        }
        current_total = jQuery("#total-records").html();
        new_total = parseInt(current_total) - 1;
        jQuery("#total-records").html(new_total);
    } else if (mode == 'showHide'){
        showHideImg =data.showInCP == "1" ?'unhide.svg' : 'hide.svg';
        customFieldVisibility = data.showInCP == "1" ? '0' : '1';
        screenLine = data.showInCP == "1" ? _lang.hideInCustomerPortal : _lang.showInCustomerPortal;
        container = jQuery("#custom-field-table-body");
        customFieldRow = jQuery('#custom-field-order-' + data.customFieldId, container);
        customFieldName = customFieldRow;
        customFieldRow.find('.show-hide-link').replaceWith('<div class="col-md-3 show-hide-link"><a href="javascript:;" onclick="showHideInCP(\''+ data.customFieldId + '\','+ '\'' + customFieldVisibility +'\','+ '\'' + data.customFieldName +'\')"' +
        'title= "'+ screenLine +'">' + '<img src="assets/images/contract/'+ showHideImg +'" width="20" class="ui-icon visible-cp"></a></div>');
    } else {
        if (jQuery('#custom-fields').hasClass('d-none')) {
            jQuery('#custom-fields').removeClass('d-none');
        }
        var tableRow = '<tr id="custom-field-order-' + data.customFieldId + '">';
        data.customFieldLanguageData.forEach(function (item, index) {
            tableRow += '<td title="' + item + '" class="custom-fields-table-cell">' + item + '</td>'
        });
        showHideImg =data.showInCP == "1" ?'unhide.svg' : 'hide.svg';
        customFieldVisibility = data.showInCP == "1" ? '0' : '1';
        screenLine = data.showInCP == "1" ? _lang.hideInCustomerPortal : _lang.showInCustomerPortal;
        tableRow +=
            '<td class="custom-fields-table-cell">' + data.field_type + '</td>'+
            '<td class="custom-fields-table-cell">' + data.types + '</td>' +
            '<td class="custom-fields-table-cell">' +
            '<div class="row"><div class="col-md-3"><span title="' + _lang.helperOrderFields + '" class="ui-icon ui-icon-arrowthick-2-n-s" style="cursor: pointer;"></span></div>' +
            '<div class="col-md-3 show-hide-link">' + '<a href="javascript:;" onclick="showHideInCP(\''+ data.customFieldId + '\','+ '\'' + customFieldVisibility +'\', \'' + data.customFieldLanguageData[0] + '\')"' +
            'title= "'+ screenLine +'">'+
            '<img src="assets/images/contract/'+ showHideImg +'" width="20" class="ui-icon visible-cp"></a>'+'</div>'+
            '<div class="col-md-6">'+ '<a href="javascript:;" onClick="customFieldForm(\'' + data.customFieldModel + '\', \'' + data.customFieldId + '\');">' + _lang.edit + '</a>&nbsp' +
            '<a href="javascript:;" onclick="deleteCustomField(\'' + data.customFieldId + '\');">' + _lang.delete + '</a>&nbsp</div></div>' +
            '</td>' +
            '</tr>';
        if (mode == 'add') {
            jQuery('#custom-field-table-body').append(tableRow);
            current_total = jQuery("#total-records").html();
            new_total = parseInt(current_total) + 1;
            jQuery("#total-records").html(new_total);
        } else if (mode == 'edit') {
            jQuery('#custom-field-order-' + data.customFieldId).replaceWith(tableRow);
        }
    }
}

function initializeSelectizeFields(input, options) {
    input.selectize({
        plugins: ['remove_button'],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: options,
        createOnBlur: true,
        groups: [],
        optgroupField: 'class',
    });
}

function updateFields(that) {
    var checked = jQuery(that).is(':checked') ? true : false;
    if (checked) {
        jQuery('.model-type-all', '#custom-field-container').removeAttr('disabled');
        jQuery('select[id=model-type]', '#custom-field-container').attr('disabled', 'disabled');
        jQuery('#model-types-container', '#custom-field-container').addClass('d-none');
    } else {
        jQuery('.model-type-all', '#custom-field-container').attr('disabled', 'disabled');
        jQuery('select[id=model-type]', '#custom-field-container').removeAttr('disabled');
        jQuery('#model-types-container', '#custom-field-container').removeClass('d-none');
    }
}

//check if custom field is used and the user wants to change its category or type then show a confirmation popup to the user to confirm there action
function isCustomFieldUsed(id, formData) {
    formData.push({name: 'id', value: id});
    jQuery.ajax({
        url: getBaseURL('contract') + 'custom_fields/is_cf_used',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (!response.result) {
                if (undefined !== response.display_message_key) {
                    confirmationDialog(response.display_message_key, {
                        resultHandler: customFieldFormRedirect,
                        parm: {id: id, container: jQuery("#custom-field-container")}
                    });
                }
            } else {
                customFieldFormSubmit('contract', jQuery("#custom-field-container"),  id)
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
    return true;
}

function customFieldFormRedirect(params) {
    customFieldFormSubmit('contract', params.container, params.id);
}

function toggleCheckbox(hiddenInput, checkBoxInput) {
    hiddenInput.val(checkBoxInput.is(':checked') ? '1' : '0');
}

function showHideInCP(customFieldId, isVisible, customFieldName) {
        jQuery.ajax({
            url: getBaseURL('contract') + 'custom_fields/show_hide/' + customFieldId + '/' + isVisible + '/' +customFieldName,
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                if (response.result) {
                    updateTableRows('showHide', response);
                    if (response.showInCP == "1"){ 
                        pinesMessage({ty: 'success', m: _lang.showInCustomerPortalSuccess.sprintf([customFieldName])});
                    } else {
                        pinesMessage({ty: 'success', m: _lang.hideInCustomerPortalSuccess.sprintf([customFieldName])});
                    }

                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
}