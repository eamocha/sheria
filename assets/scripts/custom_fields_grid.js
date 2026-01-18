var oldForm;
jQuery(document).ready(function (){
    jQuery('tbody', '#custom-fields').sortable({
        update: function( event, ui ) {
            var fieldsOrderData = [];
            jQuery("[id^=custom-field-order-]").each(function (index, obj){
                fieldsOrderData.push({'id': jQuery(this).attr('id').replace("custom-field-order-", ""), 'field_order': index});
            });
            jQuery.ajax({
                url: getBaseURL() + 'custom_fields/set_fields_order',
                data: {
                    'fields_order_data': fieldsOrderData
                },
                dataType: 'JSON',
                type: 'POST',
                success: function (response) {
                    if (response.result) {
                        pinesMessage({ty: 'success', m:  _lang.feedback_messages.updatesSavedSuccessfully});
                    } else {
                        pinesMessage({ty: 'error', m:  _lang.feedback_messages.updatesFailed});
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    });
});

function customFieldForm(model, id) {
    var id = id || null;
    jQuery.ajax({
        url: getBaseURL() + 'custom_fields/' + (id ? 'edit/' + model + '/' + id : 'add/' + model),
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
                    customFieldFormEvents(customFieldContainer, model, id);
                }
            }
            if(undefined !== response.html_case_types){//custom fields for case
                jQuery('#model-types-container', customFieldContainer).html(response.html_case_types);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function customFieldFormEvents(container, model, id) {
    jQuery(".modal", container).modal({
        keyboard: false,
        backdrop: "static",
        show: true
    });
    initializeModalSize(container);
    // oldForm = jQuery('form#custom-field-form', container).serializeArray();

    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery(".modal", container).modal("hide");
        }
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(container);
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            customFieldFormValidate(container, model, id);
        }
    });
    jQuery('.modal-body',container).on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery("#title", container).focus();
        oldForm = {category: jQuery('#model-category', container).serialize(), type: jQuery('#model-type', container).serialize()};
        console.log(oldForm);

    });
    jQuery("#custom-field-save-btn", container).click(function () {
        customFieldFormValidate(container, model, id);
    });
    var listOptions = jQuery('#list-options').selectize({
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
    // initializeSelectizeFields(jQuery('#model-category', container));

    var listOptionsSelectize = listOptions[0].selectize;
    jQuery('#type', container).selectpicker().change(function (eventData) {
        if (jQuery('#type', container).selectpicker('val') == 'list') {
            jQuery('#list-options-container').removeClass('d-none');
        } else {
            jQuery('#list-options-container').addClass('d-none');
            listOptionsSelectize.clear();
        }
    });
    jQuery('#other-lang').click(function (){
        if (jQuery('#other-lang-container').hasClass('d-none')) {
            jQuery('#other-lang-container').removeClass('d-none');
        } else {
            jQuery('#other-lang-container').addClass('d-none');
        }
    });
}
function customFieldFormValidate(container, model, id){
    var formData = jQuery("form#custom-field-form", container).serializeArray();
    if(model=== 'legal_case' && id){//if edit legal case custom fields => check if this custom field used in cp or matter form
        var editedForm =  {category: jQuery('#model-category', container).serialize(), type: jQuery('#model-type', container).serialize()};
        if((editedForm.category && editedForm.type) && (oldForm.category !== editedForm.category || oldForm.type !== editedForm.type)){
            isCustomFieldUsed(id, formData);
        }else{
            customFieldFormSubmit(container, model, id);
        }
    }else{
        customFieldFormSubmit(container, model, id);
    }
}
function customFieldFormSubmit(container, model, id) {
    var formData = jQuery("form#custom-field-form", container).serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'custom_fields/' + (id ? 'edit/' + model + '/' + id : 'add' + '/' + model),
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
                        showMoreFields(jQuery('#custom-field-form'),jQuery('#other-lang-container','#custom-field-form'));
                        if(jQuery("#" + i).val() == ""){
                            jQuery("#" + i).val(jQuery("#name_"+_lang.languageSettings['langName'].substring(0,2)).val());
                        }
                    }
                    scrollToValidationError(container);
                } else {
                    pinesMessage({ty: 'error', m:  _lang.saveRecordFailed.sprintf([_lang.customField])});
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

function deleteCustomField(customFieldModel, customFieldId) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'custom_fields/delete/' + customFieldModel + '/' + customFieldId,
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                if (response.result) {
                    updateTableRows('delete',customFieldId);
                }
                pinesMessage({ty: response.feedbackMessage.ty, m: response.feedbackMessage.m});
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function updateTableRows(mode, data)
{   
    if(mode=='delete'){
        customFieldId = data;
        jQuery('#custom-field-order-' + customFieldId).remove();
        if (jQuery('#custom-field-table-body').children().length == 0) {
            jQuery('#custom-fields').addClass('d-none');
        }
        current_total = jQuery("#total-records").html();
        new_total = parseInt(current_total) - 1;
        jQuery("#total-records").html(new_total);
    }else{

        if (jQuery('#custom-fields').hasClass('d-none')) {
            jQuery('#custom-fields').removeClass('d-none');
        }
        var tableRow = '<tr id="custom-field-order-' + data.customFieldId + '">';
        data.customFieldLanguageData.forEach(function (item, index){
            tableRow += '<td title="' + item + '" class="custom-fields-table-cell">' + item + '</td>'
        });
        tableRow +=
        '<td class="custom-fields-table-cell ">'+ data.field_type +'</td><td class="custom-fields-table-cell">' +
                '<div class="row"><div class="col-md-4"><span title="' + _lang.helperOrderFields + '" class="ui-icon ui-icon-arrowthick-2-n-s" style="cursor: pointer;"></span></div>' +
                '<div class="col-md-4"><a href="javascript:;" onClick="customFieldForm(\'' + data.customFieldModel + '\', \'' + data.customFieldId + '\');"><i class="fa fa-edit fa-lg"></i></a></div>' +
                '<div class="col-md-4"><a href="javascript:;" onclick="deleteCustomField(\'' + data.customFieldModel + '\', \'' + data.customFieldId + '\');"><i class="fa fa-trash fa-lg"></i></a>&nbsp</div>' +
            '</div></td>' +
        '</tr>';
        if (mode == 'add') {
            jQuery('#custom-field-table-body').append(tableRow);
            current_total = jQuery("#total-records").html();
            new_total = parseInt(current_total)+ 1;
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
        groups: [
        ],
        optgroupField: 'class',
    });
}
function updateFields(that) {
    var checked = jQuery(that).is(':checked') ? true : false;
    if(checked){
        jQuery('.model-type-all', '#custom-field-container').removeAttr('disabled');
        jQuery('select[id=model-type]', '#custom-field-container').attr('disabled', 'disabled');
        jQuery('#model-types-container', '#custom-field-container').addClass('d-none');
    }else{
        jQuery('.model-type-all', '#custom-field-container').attr('disabled', 'disabled');
        jQuery('select[id=model-type]', '#custom-field-container').removeAttr('disabled');
        jQuery('#model-types-container', '#custom-field-container').removeClass('d-none');
    }
}
function getAreaOfPracticeByCategory() {
    var category = jQuery('#model-category').val();
    var id = jQuery('#id').val();
    jQuery.ajax({
        url: getBaseURL() + 'custom_fields/get_practice_area_by_category',
        type: "POST",
        data: {category: category, id: id},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                jQuery('#model-types-container').html(response.html);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}
//check if custom field is used and the user wants to change its category or type then show a confirmation popup to the user to confirm there action
function isCustomFieldUsed(id, formData)
{
    formData.push({name: 'id', value: id});
    jQuery.ajax({
        url: getBaseURL() + 'custom_fields/is_cf_used',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (!response.result) {
                if(undefined !== response.display_message_key){
                    confirmationDialog(response.display_message_key, {resultHandler: customFieldFormRedirect, parm: {id: id, container: jQuery("#custom-field-container"), model: 'legal_case' }});
                }
            }else {
                customFieldFormSubmit(jQuery("#custom-field-container"), 'legal_case', id)
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
    return true;
}
function customFieldFormRedirect(params) {
    customFieldFormSubmit(params.container, params.model, params.id);
}