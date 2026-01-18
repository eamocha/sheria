var caseId, changed = false, stage, editOldStage;
jQuery(document).ready(function(){
    showToolTip();
})
function changeLitigationStageStatus(stageId) {
    caseId = jQuery("input[data-field='case_id']", '#case-events-container').val();
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/change_litigation_stage_status/' + caseId + '/' + stageId,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            jQuery('#litigation-stage-status-dialog').remove();
            if (response.html) {
                jQuery('<div id="litigation-stage-status-dialog"></div>').appendTo("body");
                var litigationStageDialog = jQuery('#litigation-stage-status-dialog');
                litigationStageDialog.html(response.html);
                jQuery('.modal', litigationStageDialog).modal({
                    keyboard: false,
                    backdrop: 'static',
                    show: true
                });
                jQuery("#save-litigation-stage-status", litigationStageDialog).click(function () {
                    litigationStageStatusFormSubmit(litigationStageDialog, caseId, stageId);
                });
                jQuery(litigationStageDialog).find('input').keypress(function (e) {
                    if (e.which == 13) { // pressing enter
                        litigationStageStatusFormSubmit(litigationStageDialog, caseId, stageId);
                    }
                });
                jQuery('.select-picker', litigationStageDialog).selectpicker();
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function litigationStageStatusFormSubmit(container, caseId, stageId) {
    var formData = jQuery("form#stage-status-form", container).serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/change_litigation_stage_status/' + caseId + '/' + stageId,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                if (typeof response.stages_html !== 'undefined' && response.stages_html) {
                    jQuery('#litigation-stages-data', '#litigation-details-container').html(response.stages_html);
                }
                if(jQuery('#case-events-container').is(':visible')){ // reload stages html in activities tab
                    legalCaseEvents.goToPage("stages",{id: caseId});
                }
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');
            showToolTip();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function changeLitigationStage(fromContainer, stageId) {
    stage = stageId || false;
    editOldStage = (typeof stage.id == 'undefined') && stage ? 1 : 0;
    stage = (typeof stage.id !== 'undefined') ? stageId.id : stage;
    fromContainer = fromContainer || 'legalCaseAddForm'; //by default the selector of the case edit form
    caseId = jQuery("input[data-field='case_id']", '#' + fromContainer).val();
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/change_litigation_stage/' + caseId + (editOldStage ? '/' + stage : ''),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html && jQuery('#litigation-stage-dialog').length <= 0) {
                jQuery('<div id="litigation-stage-dialog"></div>').appendTo("body");
                var litigationStageDialog = jQuery('#litigation-stage-dialog');
                litigationStageDialog.html(response.html);
                jQuery('.modal', litigationStageDialog).modal({
                    keyboard: false,
                    backdrop: 'static',
                    show: true
                });
                jQuery("#save-litigation-stage-btn", litigationStageDialog).click(function () {
                    litigationStageFormSubmit(litigationStageDialog);
                });
                litigationStageFormEvents(litigationStageDialog, fromContainer);
                jQuery(litigationStageDialog).find('input').keypress(function (e) {
                    if (e.which == 13) { // pressing enter
                        e.preventDefault();
                        litigationStageFormSubmit(litigationStageDialog);
                    }
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function litigationStageFormEvents(container, fromContainer) {
    if(jQuery('#legal_case_stage_id').val() != ''){
        jQuery('select#litigation-stage-to', '#litigation-stage-dialog').val(jQuery('#legal_case_stage_id').val());
        jQuery('#legal_case_stage_id').val('')
    }
    setDatePicker('#sentence-date', container);
    setDatePicker('#constitution-date', container);
    setDatePicker('#judgment-date', container);
    makeFieldsHijriDatePicker({fields: ['judgment-date-hijri']});
    jQuery('#judgment-date-hijri').val(gregorianToHijri(jQuery('#judgment-date-gregorian', '#judgment-date-hijri-container').val()));
    jQuery('.select-picker', '#litigation-stage-dialog').selectpicker();
    jQuery('#litigation-stage-to', '#litigation-stage-dialog').on('shown.bs.select', function (e) {
        jQuery('.dropdown-menu.inner').animate({
            scrollTop: jQuery(".selected").offset().top
        }, "fast");
        jQuery('.modal-body').animate({
            scrollTop: '0px'
        }, "fast");
    });
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#constitution-date', container), jQuery('#constitution-date-container', container));
    }
    opponentsInitialization(jQuery('#opponents-container', container));
    initializeModalSize(container);
    jQuery('.modal-body',container).on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal', container).on('hidden.bs.modal', function () {
        destroyModal(container);
        jQuery("select[data-field='administration-case_stages']", '#' + fromContainer).removeClass('changed');
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery("div", container).find("[data-id=litigation-stage-to]").focus();
        jQuery('#pendingReminders').parent().popover('hide');
    });
}
function litigationStageFormSubmit(container) {
    var formData = jQuery("form#litigation-stage-form", container).serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/change_litigation_stage/' + caseId + (editOldStage ? '/' + stage : ''),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery('#screen-field-sentenceDate').val(response.new_stage_sentence_date);
                jQuery('#matter-stage-transition-form').text(response.stages[response.new_stage]);
                jQuery(".modal", container).modal("hide");
                jQuery('#matter-stage').text(response.stages[response.new_stage]);
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                if (typeof response.stage_id !== 'undefined' && response.stage_id) {
                    jQuery('#litigation-stage-id', '#legalCaseAddForm').val(response.stage_id);
                }
                if (typeof response.stages_html !== 'undefined' && response.stages_html) {
                    jQuery('#litigation-stages-data', '#litigation-details-container').html(response.stages_html);
                }
                if(jQuery('#case-events-container').is(':visible')){ // reload stages html in activities tab
                    legalCaseEvents.goToPage("stages",{id: caseId});
                }
                if(jQuery('#hiddenStageId').length > 0){
                    jQuery('#hiddenStageId').val(response.new_stage);
                }
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');
            showToolTip();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function litigationExternalRefForm(isEdit, rowElementEdited, editedTableId, stageId, recordId, forceAdd, callback) {
    forceAdd = forceAdd || false;
    stageId = stageId || false;
    recordId = recordId || false;
    isEdit = isEdit || false; // if variable is sent then it's the edit form
    rowElementEdited = rowElementEdited || false; // edited element in table
    editedTableId = editedTableId || false; // edited table id
    callback = callback || false; // callback function
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/load_litigation_stage_forms/external_court_reference/' + isEdit,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#litigation-external-dialog').length <= 0) {
                    jQuery('<div id="litigation-external-dialog"></div>').appendTo("body");
                    var litigationStageDialog = jQuery('#litigation-external-dialog');
                    litigationStageDialog.html(response.html);
                    if(isEdit){
                        jQuery('#form-action', litigationStageDialog).val(isEdit ? "edit" : "add");
                        jQuery('#table-element-edited', litigationStageDialog).val(rowElementEdited ? rowElementEdited : "");
                        jQuery('#stage_id', litigationStageDialog).val(stageId ? stageId : "");
                        jQuery('#id', litigationStageDialog).val(recordId ? recordId : "");
                        jQuery('#table-id-edited', litigationStageDialog).val(editedTableId ? editedTableId : "");
                        jQuery('#external-number', litigationStageDialog).val(jQuery("tr#" + rowElementEdited).find("td:nth-child(1)").text().trim());
                        jQuery("input[selector='court-ref-date']", litigationStageDialog).val(jQuery("tr#" + rowElementEdited).find("td:nth-child(2)").text().trim());
                        jQuery('#external-comments', litigationStageDialog).val(jQuery("tr#" + rowElementEdited).find("td:nth-child(3)").text().trim());
                    }
                    if(jQuery('#litigation-stage-modal').length > 0){ // edit stage window
                        commonModalDialogEvents(litigationStageDialog, litigationExternalFormSubmit, callback);
                    }else if(isEdit){ // edit reference record in activities tab
                        commonModalDialogEvents(litigationStageDialog, activitiesExternalFormSubmit, callback);
                    }else if(forceAdd){ // force add external and submit to DB in activities tab
                        jQuery('#stage_id', litigationStageDialog).val(stageId ? stageId : "");
                        commonModalDialogEvents(litigationStageDialog, activitiesExternalFormAddSubmit, callback);
                    }
                    litigationExternalFormEvents(litigationStageDialog);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function activitiesExternalFormAddSubmit(referenceContainer, callback) {
    callback = callback || false;
    var externalNumber = jQuery('#external-number', referenceContainer).val();
    var externalRefDate = jQuery("input[selector='court-ref-date']", referenceContainer).val();
    if (!externalNumber || !externalRefDate) {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.requiredFormFields});
        return;
    } else if (!validateDate(externalRefDate)) {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.dateFormat});
        return;
    }
    var formData = jQuery("form#litigation-external-form").serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/add_court_external_ref',
        success: function (response) {
            if (response.result) {
                jQuery('.modal',referenceContainer).modal('hide');
                if(callback && isFunction(callback)) callback();
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function activitiesExternalFormSubmit(referenceContainer, callback) {
    callback = callback || false;
    var externalNumber = jQuery('#external-number', referenceContainer).val();
    var externalRefDate = jQuery("input[selector='court-ref-date']", referenceContainer).val();
    externalRefDate = externalRefDate.replace(/ /g,'');
    if (!externalNumber || !externalRefDate) {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.requiredFormFields});
        return;
    } else if (!validateDate(externalRefDate)) {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.dateFormat});
        return;
    }
    var formData = jQuery("form#litigation-external-form").serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/update_court_external_ref',
        success: function (response) {
            if (response.result) {
                var table = jQuery('#table-id-edited').val();
                var trElement = jQuery('#table-element-edited').val();
                jQuery('#no-data-container', '#litigation-reference-container').addClass('d-none');
                var number = jQuery('#external-number', referenceContainer).val();
                var refDate = jQuery("input[selector='court-ref-date']", referenceContainer).val();
                var comments = jQuery('#external-comments', referenceContainer).val();
                jQuery('table#' + table + ' tbody tr#' + trElement + ' td:nth-child(1)').text(number);
                jQuery('table#' + table + ' tbody tr#' + trElement + ' td:nth-child(2)').text(refDate);
                jQuery('table#' + table + ' tbody tr#' + trElement + ' td:nth-child(3)').text(comments);
                if(jQuery('#form-action', referenceContainer).val() == "edit"){
                    jQuery('#table-element-edited', referenceContainer).val("");
                }
                if(callback && isFunction(callback)) callback();
                jQuery('.modal',referenceContainer).modal('hide');
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function litigationExternalFormEvents(container) {
    setDatePicker('#refDate', container);
    makeFieldsHijriDatePicker({fields: ['external-refDate-hijri']});
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#refDate', container), jQuery('#external-date-container', container));
    }
    initializeModalSize(container);
}
function validateDate(date) {
    var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
    return date_regex.test(date);
}
function litigationExternalFormSubmit(referenceContainer) {
    var externalNumber = jQuery('#external-number', referenceContainer).val();
    var externalRefDate = jQuery("input[selector='court-ref-date']", referenceContainer).val();
    if (!externalNumber || !externalRefDate) {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.requiredFormFields});
        return;
    } else if (!validateDate(externalRefDate)) {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.dateFormat});
        return;
    }
    var table = jQuery('#data-table-external-reference');
    jQuery('#no-data-container', '#litigation-reference-container').addClass('d-none');
    table.removeClass('d-none');
    var table = jQuery('#data-table-external-reference').DataTable();
    var number = jQuery('#external-number', referenceContainer).val();
    var refDate = jQuery("input[selector='court-ref-date']", referenceContainer).val();
    var comments = jQuery('#external-comments', referenceContainer).val();
    table.rows.add([{
            0: number + '<input type="hidden" name="external_ref[number][]" value="' + number + '"/>',
            1: refDate + '<input type="hidden" name="external_ref[refDate][]" value="' + refDate + '"/>',
            2: comments + '<input type="hidden" name="external_ref[comments][]" value="' + comments + '"/>',
            3: litigationExternalRefTableActions('data-table-external-reference')

    }]).draw();
    if(jQuery('#form-action', referenceContainer).val() == "edit"){
        var rowId = jQuery('#table-element-edited', referenceContainer).val();
        jQuery('#table-element-edited', referenceContainer).val("");
        table.row(jQuery('tr#' + rowId)).remove().draw();
    }
    jQuery('.modal-body', '#litigation-stage-modal').scrollTo(jQuery('#data-table-external-reference'));
    jQuery('#data-table-external-reference_filter').show();
    jQuery('.modal',referenceContainer).modal('hide');
}
function dataTableRemoveRow(element, tableId) {
    var dataTable = jQuery('#' + tableId).DataTable();
    dataTable.row(jQuery(element).parents('tr')).remove().draw();
}
function litigationExternalRefEditForm(currentElement, tableId, stageId, recordId, callback) {
    stageId = stageId || false;
    recordId = recordId || false;
    callback = callback || false;
    // generate random number for data table row
    var tableRowRandomNum = 'tableRow-' + Math.floor((Math.random() * 10) + 1);
    jQuery(currentElement, tableId).parents('tr').attr('id', tableRowRandomNum);
    litigationExternalRefForm(true, tableRowRandomNum, tableId, stageId, recordId, false, callback);
}
function litigationContactEditForm(currentElement, tableId, type) {
    // generate random number for data table row
    var tableRowRandomNum = 'tableRow-' + Math.floor((Math.random() * 10) + 1);
    jQuery(currentElement, tableId).parents('tr').attr('id', tableRowRandomNum);
    litigationContactForm(type, true, tableRowRandomNum);
}
function litigationContactTableActions(type, tableId) {
    return '<a title="' + _lang.edit + '" href="javascript:;" onClick="litigationContactEditForm(this, \'' + tableId + '\', \'' + type + '\');" class="btn btn-default btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>' +
     '&nbsp;&nbsp;' + '<a title="' + _lang.delete + '" href="javascript:;" onClick="dataTableRemoveRow(this, \'' + tableId + '\');" class="btn btn-default btn-sm"><i class="fa-solid fa-trash-can red"></i></a>';
}
function litigationExternalRefTableActions(tableId) {
    return '<a title="' + _lang.edit + '" href="javascript:;" onClick="litigationExternalRefEditForm(this, \'' + tableId + '\');" class="btn btn-default btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>' +
     '&nbsp;&nbsp;' + '<a title="' + _lang.delete + '" href="javascript:;" onClick="dataTableRemoveRow(this, \'' + tableId + '\');" class="btn btn-default btn-sm"><i class="fa-solid fa-trash-can red"></i></a>';
}
function litigationContactForm(type, isEdit, rowElementEdited) {
    isEdit = isEdit || false; // if variable is sent then it's the edit form
    rowElementEdited = rowElementEdited || false; // edited element in table
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/load_litigation_stage_forms/' + type + '/' + isEdit,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#litigation-' + type + '-dialog').length <= 0) {
                    jQuery('<div id="litigation-' + type + '-dialog"></div>').appendTo("body");
                    var litigationStageDialog = jQuery('#litigation-' + type + '-dialog');
                    litigationStageDialog.html(response.html);
                    if(isEdit){
                        jQuery('#form-action', litigationStageDialog).val(isEdit ? "edit" : "add");
                        jQuery('#table-element-edited', litigationStageDialog).val(rowElementEdited ? rowElementEdited : "");
                        jQuery('#lookup-litigation-contact', litigationStageDialog).val(jQuery("tr#" + rowElementEdited).find("td:nth-child(1)").text());
                        jQuery('#lookup-litigation-contact-id', litigationStageDialog).val(jQuery('input.' + type + '-id', jQuery("tr#" + rowElementEdited).find("td:nth-child(1)")).val());
                        jQuery('#litigation-' + type + '-comments', litigationStageDialog).val(jQuery("tr#" + rowElementEdited).find("td:nth-child(2)").text());
                    }
                    litigationContactCommonModalDialogEvents(litigationStageDialog, type);
                    jQuery("#save-litigation-" + type + "-btn", litigationStageDialog).click(function () {
                        litigationContactFormSubmit(litigationStageDialog, type);
                    });
                    jQuery(litigationStageDialog).find('input').keypress(function (e) {
                        if (e.which == 13) { // pressing enter
                            e.preventDefault();
                            litigationContactFormSubmit(litigationStageDialog, type);
                        }
                    });
                    litigationContactFormEvents(litigationStageDialog, type);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
//common functions used to all litigation contact modal dialogs
function litigationContactCommonModalDialogEvents(container, type) {
    jQuery(".modal", container).modal({
        keyboard: false,
        backdrop: "static",
        show: true
    });
    jQuery('#pendingReminders').parent().popover('hide');
    jQuery('.modal-body',container).on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal', container).on('shown.bs.modal', function (e) { // IE9 not supported
        if (jQuery(".first-input", container).val() == '') {
            jQuery(".first-input", container).focus();
        }
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery(".modal", container).modal("hide");
        }
    });
    jQuery('.modal', container).on('hidden.bs.modal', function () {
        jQuery("tr", jQuery('#data-table-' + type)).each(function () {
            jQuery(this).attr('id', '');
        });
        destroyModal(container);
    });
}
function litigationContactFormEvents(container, type) {
    jQuery('.select-picker', '#litigation-' + type + '-dialog').selectpicker();
    initializeModalSize(container, 0.4, 0.5);
    var lookupDetails = {'lookupField': jQuery('#lookup-litigation-contact', container), 'errorDiv': 'contact_id', 'hiddenId': '#lookup-litigation-contact-id', 'resultHandler': 'litigationContactResultHandler'};
    lookUpContacts(lookupDetails, container);
}
function litigationContactResultHandler(record, container) {
    var clientName = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#lookup-litigation-contact-id', container).val(record.id);
    jQuery('#lookup-litigation-contact', container).val(clientName);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#lookup-litigation-contact", container).typeahead('destroy');
        var lookupDetails = {'lookupField': jQuery('#lookup-litigation-contact', container), 'errorDiv': 'contact_id', 'hiddenId': '#lookup-litigation-contact-id', 'resultHandler': 'litigationContactResultHandler'};
        lookUpContacts(lookupDetails, container);
    }
}
function litigationContactFormSubmit(contactContainer, type) {
    var contactName = jQuery('#lookup-litigation-contact', contactContainer).val();
    var contactId = jQuery('#lookup-litigation-contact-id', contactContainer).val();
    if (!contactId) {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.requiredFormFields});
        return;
    }
    var addRecord = true;
    var rowId = jQuery('#table-element-edited', contactContainer).val();
    var table = jQuery('#data-table-' + type);
    if (table.hasClass('d-none')) {
        jQuery('#no-data-container', '#litigation-' + type + '-container').addClass('d-none');
        table.removeClass('d-none');
    } else {
        jQuery('tr', jQuery('tbody', '#data-table-' + type)).each(function () {
            if (contactId === jQuery('.' + type + '-id', this).val() && jQuery(this).attr('id') != rowId) {
                pinesMessage({ty: 'warning', m: _lang.fieldAlreadyExists});
                addRecord = false;
                return;
            }
        });
    }
    if (addRecord) {
        var table = jQuery('#data-table-' + type).DataTable();
        var comments = jQuery('#litigation-' + type + '-comments', contactContainer).val();
        table.rows.add([{
                0: contactName + '<input type="hidden" name="litigationContact[contact][]" value="' + contactId + '" class="' + type + '-id"/>',
                1: comments + '<input type="hidden" name="litigationContact[comments][]" value="' + comments + '"/>' + '<input type="hidden" name="litigationContact[contact_type][]" value="' + type + '"/>',
                2: litigationContactTableActions(type, 'data-table-' + type)
            }])
                .draw();
        if(jQuery('#form-action', contactContainer).val() == "edit"){
            jQuery('#table-element-edited', contactContainer).val("");
            table.row(jQuery('tr#' + rowId)).remove().draw();
        }
        jQuery('.modal', contactContainer).modal('hide');
        jQuery('.modal-body', '#litigation-stage-modal').scrollTo(jQuery('#data-table-' + type));
        jQuery('#data-table-' + type + '_filter').show();
    }
}
function courtCallBack(id) {
    if (!jQuery("#litigation-stage-court-type-id option[value='" + jQuery('#court-form-type-id', '#court-dialog').val() + "']", '#litigationStageContainer').text()) {
        jQuery('#litigation-stage-court-type-id', '#litigationStageContainer').append(jQuery('<option/>', {value: jQuery('#court-form-type-id', '#court-dialog').val(), text: jQuery('#court-form-type-id option:selected', '#court-dialog').text()}));

    }
    if (!jQuery("#litigation-stage-court-degree-id option[value='" + jQuery('#court-form-degree-id', '#court-dialog').val() + "']", '#litigationStageContainer').text()) {
        jQuery('#litigation-stage-court-degree-id', '#litigationStageContainer').append(jQuery('<option/>', {value: jQuery('#court-form-degree-id', '#court-dialog').val(), text: jQuery('#court-form-degree-id option:selected', '#court-dialog').text()}));

    }
    if (!jQuery("#litigation-stage-court-region-id option[value='" + jQuery('#court-form-region-id', '#court-dialog').val() + "']", '#litigationStageContainer').text()) {
        jQuery('#litigation-stage-court-region-id', '#litigationStageContainer').append(jQuery('<option/>', {value: jQuery('#court-form-region-id', '#court-dialog').val(), text: jQuery('#court-form-region-id option:selected', '#court-dialog').text()}));

    }
    if (!jQuery("#litigation-stage-court-id option[value='" + jQuery('#court-form-id', '#court-dialog').val() + "']", '#litigationStageContainer').text()) {
        jQuery('#litigation-stage-court-id', '#litigationStageContainer').append(jQuery('<option/>', {value: id, text: jQuery('#court-form-id', '#court-dialog').val()}));

    }
    jQuery('#litigation-stage-court-type-id', '#litigationStageContainer').val(jQuery('#court-form-type-id', '#court-dialog').val()).selectpicker('refresh');
    jQuery('#litigation-stage-court-degree-id', '#litigationStageContainer').val(jQuery('#court-form-degree-id', '#court-dialog').val()).selectpicker('refresh');
    jQuery('#litigation-stage-court-region-id', '#litigationStageContainer').val(jQuery('#court-form-region-id', '#court-dialog').val()).selectpicker('refresh');
    jQuery('#litigation-stage-court-id', '#litigationStageContainer').val(id).selectpicker('refresh');
    return true;
}
function toggleElements(elementsToggleIcon, elementContainer) {
    if (elementContainer.is(':visible')) {
        elementContainer.addClass('d-none').slideUp();
        elementsToggleIcon.removeClass('fa-solid fa-angle-down');
        elementsToggleIcon.addClass('fa-solid fa-angle-right');
        updateURL(elementContainer.attr("id"),true);
    } else {
        elementContainer.removeClass('d-none').slideDown();
        elementsToggleIcon.removeClass('fa-solid fa-angle-right');
        elementsToggleIcon.addClass('fa-solid fa-angle-down');
        updateURL(elementContainer.attr("id"));
    }
}
function setDataToCaseOpponentField(record, container) {
    var containerId = '#' + jQuery(container).attr('id');
    var opponentName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    container = (undefined !== companyContactFormMatrix.commonLookup[containerId] && undefined !== companyContactFormMatrix.commonLookup[containerId].parentContainer) ? companyContactFormMatrix.commonLookup[containerId].parentContainer : '#litigation-dialog';
    jQuery('#opponent-member-id', container).val(record.id);
    jQuery('#opponent-lookup', container).val(opponentName);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#opponent-lookup", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#opponent-lookup', container),
            'hiddenInput': jQuery('#opponent-member-id', container),
            'errorDiv': 'opponent_member_id_' + container.attr('id').charAt(container.attr('id').length - 1) + '',
            'resultHandler': setDataToCaseOpponentField,
            'parentContainer': container
        };
        lookupCompanyContactType(lookupDetails, container);
    }
}
function expandAllStages() {
    jQuery('.stage-box', jQuery('#litigation-stages-data')).slideDown();
    jQuery('a.collapsing-arrow > i', jQuery('#litigation-stages-data')).removeClass('fa-solid fa-angle-right').addClass('fa-solid fa-angle-down');
}
function collapseAllStages() {
    jQuery('.stage-box', jQuery('#litigation-stages-data')).slideUp();
    jQuery('a.collapsing-arrow > i', jQuery('#litigation-stages-data')).removeClass('fa-solid fa-angle-down').addClass('fa-solid fa-angle-right');
}
function deleteStage(matterId, stageId){
    var data = {
        matterId: matterId,
        stageId: stageId
    };
    confirmationDialog('confim_delete_stage', {resultHandler: deleteStageSubmission, parm: data, confirmationCategory: 'danger'});
}
function deleteStageSubmission(data){
    jQuery.ajax({
        url: getBaseURL() + 'cases/delete_case_stage',
        method: 'post',
        data: data,
        dataType: 'json',
        success: function(response){
            if(response.result){
                window.location = window.location.href;
            }else{
                pinesMessage({ty: "error", m: _lang.recordNotDeleted});
            }
        }
    });
}

function deleteExernalRef(id, row, tableId){
    // generate random number for table row
    var tableRowRandomNum = 'tableRow-' + Math.floor((Math.random() * 10) + 1);
    jQuery(row, tableId).parents('tr').attr('id', tableRowRandomNum);
    var data = {
        id: id,
        rowId: tableRowRandomNum
    };
    confirmationDialog('confirm_delete_record', {resultHandler: deleteExernalRefSubmission, parm: data, confirmationCategory: 'danger'});
}
function deleteExernalRefSubmission(data){
    jQuery.ajax({
        url: getBaseURL() + 'cases/delete_court_external_ref',
        method: 'post',
        data: data,
        dataType: 'json',
        success: function(response){
            if(response.result){
                jQuery('tr#' + data.rowId).remove();
            }else{
                pinesMessage({ty: "error", m: _lang.recordNotDeleted});
            }
        }
    });
}

function showMoreDescription(element,id,container,expendMore) {
    var containerElement = jQuery('#'+container + id);
    var descriptionElement = jQuery("#short-description-" +id,containerElement);
    var fullDescriptionElement = jQuery("#full-description-" +id,containerElement);
    if(expendMore){
        descriptionElement.addClass("d-none");
        fullDescriptionElement.removeClass("hide");
    } else {
        descriptionElement.removeClass("hide");
        fullDescriptionElement.addClass("d-none");
    }
}

function _countStageTasksAndEvents(_pageTo, _caseId, _stageId, stageContainer){
    var _params = {id:_caseId, stageId:_stageId};
    var _target_counter = _pageTo === "tasks" ? "#count-tasks-stage-" : "#count-events-stage-";
    jQuery.ajax({
        url: getBaseURL() + 'cases/events',
        type: 'POST',
        dataType: 'JSON',
        data: {
            pageTo: _pageTo,
            params: _params,
            returnCount: true,
        }, beforeSend: function () {
            jQuery(_target_counter + _stageId).text('');
            jQuery('.count-loader', stageContainer).show();
        }, success: function (response) {
            if(response.result > 0){
                jQuery(_target_counter + _stageId).text("(" + response.result + ")");
            }
        }, complete: function () {
            jQuery('.count-loader', stageContainer).hide();
        },
    });
}

function countStageTasksAndEvents(caseId, stageId, stageContainer){
    if (stageContainer.is(':visible')) {
        _countStageTasksAndEvents('tasks', caseId, stageId, stageContainer);
        _countStageTasksAndEvents('events', caseId, stageId, stageContainer);
    }
}
function externalCourtRefLink(targeturl, externalCourtRef) {
    var input = document.body.appendChild(document.createElement("input"));
    input.value = externalCourtRef;
    input.focus();
    input.select();
    document.execCommand('copy');
    input.parentNode.removeChild(input);
    window.open(targeturl, '_blank');
}
