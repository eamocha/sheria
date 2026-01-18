var container, fieldsDetails;
jQuery(document).ready(function () {
    container = jQuery('#screen-form');
    jQuery('.select-picker', container).selectpicker();
    jQuery('.cp-field-visible-list1', '.cpFieldsTable').selectpicker().change(function () {
        changeVisibleField(this, jQuery(this).val());
    });
    jQuery('.cp-field-required-list1', '.cpFieldsTable').selectpicker().change(function () {
        updateRequiredField(this);
    });
    jQuery(".cpFieldsTable tbody").sortable({
        stop: function (e, ui) {
            assignOrderForScreenFields();
        }
    });
    jQuery(".cpFieldsTable tbody").disableSelection();
    jQuery('.requiredDefaultValue', jQuery('#assigned_team_id-default-value-container')).change(function () {
        if (this.value > 0) {
            reloadUsersListByAssignedTeam(this.value, jQuery('.requiredDefaultValue', jQuery('#assignee_id-default-value-container')));
        }
    });

    assignOrderForScreenFields();
    jQuery('#type-id', container).on('change', function () { //retrun Custom FIelds related to the category and practice area
        if (this.value) {
            returnRelatedFields(this.value);
            contractTypeEvent(jQuery(this).val(), jQuery("#sub-type-id", container));
        }
    });
});

function assignOrderForScreenFields() {
    jQuery('.cpFieldsTable tbody input[name="sortOrder[]"]').each(function () {
        jQuery(this).val(jQuery(this).parents('tr').index() + 1);
    });
}

function changeVisibleField(obj, checked) {
    // on change the visible field in A4L by put it as non visible => display the default value
    var field = jQuery(obj).closest('td').attr("fieldName");
    jQuery("div", '#cp-screen-container').find("[data-field=default_" + field + "]").addClass('d-none').html('');
    if (checked == 'yes') {
        jQuery('.visible-hidden-value', 'td[fieldName="' + field + '"]').val(1);
        jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).val('').addClass('d-none');
        jQuery('#selected-' + field, jQuery('#' + field + '-default-value-container')).html('').addClass('d-none');
        if (jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).hasClass('date')) {
            jQuery('.date-picker-' + field).addClass('d-none');
        }
        if (field === 'assigned_team_id' || field === 'assignee_id') {//If the assigned team is Visible (Yes) => assignee can either be Yes or No (either required or no with no default values)
            jQuery('.requiredDefaultValue', jQuery('#assignee_id-default-value-container')).val('').addClass('d-none');
            jQuery('.assignee-assignment-rule', jQuery('#assignee_id-default-value-container')).addClass('d-none');
            jQuery('.assignee-assignment-rule input[type=radio]', '#assignee_id-default-value-container').attr('disabled', 'disabled');
        }
    } else {
        if (jQuery('.requiredHiddenValue', 'td[fieldName="' + field + '"]').val() == "1") {
            jQuery('.visible-hidden-value', 'td[fieldName="' + field + '"]').val(0);
            if (field === 'assignee_id' && (jQuery('select.cp-field-visible-list1', '#assigned_team_id').val() === "yes")) {
                jQuery('.cp-field-visible-list1', '#' + field).val('yes').selectpicker('refresh');
                pinesMessage({ty: 'warning', m: _lang.feedback_messages.assigneeRelatedToAssignedTeam});
                return false;
            }
            if ((field === 'assigned_team_id' && (jQuery('select.cp-field-visible-list1', '#assignee_id').val() === "no") || (field === 'assignee_id') )) {
                jQuery('.requiredDefaultValue', jQuery('#assignee_id-default-value-container')).val('').removeClass('d-none');
                jQuery('.assignee-assignment-rule', jQuery('#assignee_id-default-value-container')).removeClass('d-none');
            }
            pinesMessage({ty: 'information', m: _lang.cpRequiredShouldHaveDefaultVal});
            jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).val('').removeClass('d-none');
            jQuery('#selected-' + field, jQuery('#' + field + '-default-value-container')).html('').removeClass('d-none');
            if (jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).hasClass('date')) {//date
                jQuery('.date-picker-' + field).removeClass('d-none');
            }
            if (jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).hasClass('time')) {//date and time
                jQuery('.time-picker-' + field).removeClass('d-none');
            }
            if (jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).hasClass('select')) {//list
                jQuery('.select').selectpicker();
            }
        } else {
            jQuery('.cp-field-visible-list1', '#' + field).val('yes').selectpicker('refresh');
            pinesMessage({ty: 'warning', m: _lang.feedback_messages.fieldCantBeNotRequiredAndHidden});
        }
    }

}

function updateRequiredField(select) {
    // on change the optional field in A4L
    var field = jQuery(select).closest('td').attr("fieldName");
    if (select.value == 'yes') {
        jQuery('.requiredHiddenValue', 'td[fieldName="' + field + '"]').val(1);
    } else {
        if (jQuery('.visible-hidden-value', 'td[fieldName="' + field + '"]').val() == "1") {
            jQuery('.requiredHiddenValue', 'td[fieldName="' + field + '"]').val(0).selectpicker('refresh');
        } else {
            jQuery('.cp-field-required-list1', '#' + field).val('yes').selectpicker('refresh');
            pinesMessage({ty: 'warning', m: _lang.feedback_messages.fieldCantBeNotRequiredAndHidden});
        }
    }
}

var attachment_count = 1;
function addNewScreenField() {
    var field = jQuery('#screenField').val();
    var type = fieldsDetails[field]['formType'];
    var fieldsContainer = jQuery('.fieldsContainer');
    if (field) {
        if (jQuery('#' + field, fieldsContainer).length && field !== 'attachment') {
            pinesMessage({ty: 'warning', m: _lang.fieldAlreadyExists});
        } else {
            if(field == 'attachment') {
                field = addHiddenAttachmentRowAndModifyFieldName(field);
                attachment_count++;
            }
            var tmpFieldContainer = jQuery("tr.screenField#" + field, '.tempFieldsContainer');
            jQuery("#fields-table-body", fieldsContainer).append('<tr class="screenField" id="' + field + '">' + tmpFieldContainer.html() + '</tr>');
            jQuery('.cp-field-visible-list1', '#' + field, fieldsContainer).selectpicker().change(function () {
                changeVisibleField(this, jQuery(this).val());
            });
            jQuery('.cp-field-required-list1', '#' + field, fieldsContainer).selectpicker().change(function () {
                updateRequiredField(this);
            });
            jQuery('.select-picker-' + field, '#' + field, fieldsContainer).selectpicker();

            switch (type) {
                case 'lookup':
                    jQuery('#lookup-' + field, jQuery('#cp-screen-container')).typeahead('destroy');
                    var lookupDetails = {
                        'lookupField': jQuery('#lookup-' + field, jQuery('#cp-screen-container')),
                        'hiddenId': '#hidden-lookup-' + field,
                        'errorDiv': 'lookup' + field
                    };
                    lookUpContacts(lookupDetails, jQuery(jQuery('#cp-screen-container')));
                    break;
                case 'lookup_multiselect':
                    jQuery('#lookup-' + field, jQuery('#cp-screen-container')).typeahead('destroy');
                    var lookupDetails = {
                        'lookupField': jQuery('#lookup-' + field, jQuery('#cp-screen-container')),
                        'lookupContainer': field + '-lookup-container',
                        'errorDiv': 'lookup' + field,
                        'boxName': '' + field,
                        'boxId': '#selected-' + field,
                        'onSelect': 'handleRequiredMultiselectPicker',
                        'onSelectParameters': {
                            "fieldName": '#lookup-' + field,
                            "isRequired": fieldsDetails[field]['required_in_db'],
                            "selectedItemContainer": '#selected-' + field
                        }
                    };
                    lookUp(lookupDetails, jQuery('#cp-screen-container'), true, fieldsDetails[field]['type_data']);
                    break;

                case 'date':
                    setDatePicker('.date-picker-' + field);

                    break;
                case 'date_time':
                    setDatePicker('.date-picker-' + field);
                    jQuery('#time-' + field).timepicker({
                        'timeFormat': 'H:i'
                    });
                    break;

            }


        }
    }

    if(field.includes('attachment')) {
        jQuery('#screenField option[value = "attachment"]').removeAttr('selected');
        jQuery('#screenField').trigger("chosen:updated"); 
    }
    
    jQuery(".cpFieldsTable tbody").sortable('refresh');
    jQuery(".cpFieldsTable tbody").disableSelection();
    assignOrderForScreenFields();
}

function removeScreenFieldRow(field) {
    jQuery('#' + field, '.fieldsContainer').remove();
}

function submitScreenForm(id) {
    id = id || false;
    var container = jQuery('#screen-form', '#cp-screen-container');
    var formData = container.serializeArray();

    jQuery.ajax({
        url: getBaseURL('contract') + 'customer_portal/' + (id ? 'edit' : 'add'),
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
                window.location = getBaseURL('contract') + 'customer_portal/edit/'+response.id;
            } else {
                if (typeof response.validation_errors !== 'undefined') {
                    displayValidationErrors(response.validation_errors, container);

                }
            }
        }, complete: function () {
            jQuery('#form-submit', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
    });
}

/*
 * handle lookup inputs when the user type a string in the text box and leave the field without selecting an option
 * @param string lookupId(lookup field name)
 */
function handleLookup(field) {
    if (jQuery('#hidden-lookup-' + field).val() == '') {
        jQuery('#lookup-' + field).typeahead('val', '');
    }
}

/*
 * handle the required state of required multi-select lookup field in customer portal add screen
 * if the lookup is empty the required attribute is added if not the attribute is removed
 * @param abject parametersObject(an object with the following properties fieldName, isRequired, selectedItemContainer)
 */
function handleRequiredMultiselectPicker(parametersObject) {
    if (parametersObject.isRequired == 1) {
        if (jQuery(parametersObject.selectedItemContainer).html() != '') {
            jQuery(parametersObject.fieldName).removeAttr('required');
        } else {
            jQuery(parametersObject.fieldName).attr('required', 'required');
        }
    }
}

/**
 * Lookup for core user
 * Retreiving users depending on the term entered with 1 characters and above
 *
 * @param {type} lookupDetails
 * @param {type} container
 * @param {type} isBoxContainer
 * @return {undefined}
 */
function lookUp(lookupDetails, container, isBoxContainer, moduleController) {
    isBoxContainer = isBoxContainer || false;
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL('contract') + moduleController + '/autocomplete?term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY'
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupDetails['lookupField']).typeahead({
            hint: false,
            highlight: true,
            minLength: 1
        },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (!isBoxContainer) {
                    return moduleController == 'companies' ? item.name : (item.firstName + ' ' + item.lastName);
                }
            },
            templates: {
                empty: [
                    '<div class="empty click" ></div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + (moduleController == 'companies' ? data.name : (data.firstName + ' ' + data.lastName)) + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
        if (isBoxContainer) {
            jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
            lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
            setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), {
                id: datum.id,
                value: moduleController == 'companies' ? datum.name : (datum.firstName + ' ' + datum.lastName),
                name: lookupDetails['boxName']
            }, lookupDetails['onSelect'], lookupDetails['onSelectParameters']);
        } else {
            if (typeof lookupDetails['onSelect'] !== "undefined") {
                typeof lookupDetails['onSelectParameters'] !== "undefined" ? window[lookupDetails['onSelect']](lookupDetails['onSelectParameters']) : window[lookupDetails['onSelect']]();
            }
        }
    }).on('typeahead:asyncrequest', function () {
        jQuery('.loader-submit').addClass('loading');
    });
    if (!isBoxContainer) {
        lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container);
    }
}

function reloadUsersListByAssignedTeam(pGId, userListFieldId) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'users/autocomplete/active',
        dataType: 'JSON',
        data: {
            join: ['provider_groups_users'],
            more_filters: {'provider_group_id': pGId},
            term: ''
        },
        success: function (results) {
            var newOptions = '<option value="">' + _lang.chooseUsers + '</option>';
            if (typeof results != "undefined" && results != null && results.length > 0) {
                for (i in results) {
                    newOptions += '<option value="' + results[i].id + '">' + results[i].firstName + ' ' + results[i].lastName + '</option>';
                }
            }
            userListFieldId.html(newOptions);

        }, error: defaultAjaxJSONErrorsHandler
    });
}

function returnRelatedFields(type) {
    jQuery.ajax({
        url: getBaseURL('contract') + "customer_portal/return_cf_related_fields",
        method: "post",
        dataType: "JSON",
        data: {type_id: type},
        success: function (response) {
            if (response.result && response.fields) {
                jQuery('option.cf-options', container).remove();
                jQuery('.fieldsPackageList option[value^="customField_"]').remove();
                jQuery.each(response.fields, function (key, value) {
                    jQuery('#screenField', container).append('<option value="' + key + '" class="cf-options">' + value + '</option>');

                });
                jQuery('#screenField', container).selectpicker("refresh");
                var id;
                var fieldsContainer = jQuery('.fieldsContainer');
                jQuery("[id^=customField_]", fieldsContainer).each(function (index, val) {
                    id = jQuery(this).attr('id');
                    if (id.length < 20 && !(id in response.fields)) {
                        removeScreenFieldRow(id);
                    }
                });
            }
        }
    });
}

function addHiddenAttachmentRowAndModifyFieldName(field) {
    const newTr = jQuery('<tr></tr>');
    newTr.addClass('screenField');
    let tmpField = field + '_' + (attachment_count + 1);
    newTr.attr('id', tmpField);
    const clone = jQuery("tr.screenField#attachment_1", ".tempFieldsContainer").clone();
    const sortOrder = jQuery('#fields-table-body tr').length + 1;
    clone.children().each((index, element) => {
        const td = jQuery(element);
        const newTd = jQuery('<td></td>');
        if(td.attr('fieldName')) {
            newTd.attr('fieldName', tmpField);
        }
        td.children().each((index, element) => {
            const el = jQuery(element);
            const elName = el.attr('name');
            switch(elName) {
                case 'screenFields[]':
                    el.val(tmpField);
                    break;
                case 'sortOrder[]':
                    el.val(sortOrder);
                    break;
                case 'fieldLangValues1[]':
                    break;
                case 'fieldLangValues2[]':
                    newTd.addClass('d-none');
                    break;
                case 'fieldLangValues3[]':
                    newTd.addClass('d-none');
                    break;
                case 'fieldLangValues4[]':
                    newTd.addClass('d-none');
                    break;
                case 'isRequired[attachment_1]':
                    el.attr('name', 'isRequired[' + tmpField + ']');
                    break;
                case 'visible[attachment_1]':
                    el.attr('name', 'visible[' + tmpField + ']');
                    break;
                case 'fieldRequiredDefaultValue[attachment_1]':
                    el.attr('name', 'fieldRequiredDefaultValue[' + tmpField + ']');
                    newTd.attr('id', tmpField + '-default-value-container');
                    break;
                case 'fieldDescription[]':
                    break;
                default:
                    break;
            }
            if(el.is('div')) {
                el.find('a').attr('onclick', 'removeScreenFieldRow("' + tmpField + '");');
            }
            newTd.append(el);
        });
        newTr.append(newTd);
    });
    jQuery('.tempFieldsContainer tbody').append(newTr);
    return field + '_' + attachment_count;
}