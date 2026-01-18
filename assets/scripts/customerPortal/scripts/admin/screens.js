var availableUsers, availableEmails, REGEX_EMAIL;
jQuery(document).ready(function () {
    var screenForm = jQuery('#screenForm');
    jQuery('.fieldsPackageList', screenForm).chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseField,
        height: 130,
        width: "100%"
    });
    screenForm.validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
    screenForm.submit(function () {
        submitScreenForm();
    });
    jQuery('.cp-field-visible-list1').each(function () {
        jQuery(this).change(function () {
            changeVisibleField(this, jQuery(this).val());
        });
    });

    jQuery(".cpFieldsTable tbody").sortable({
        stop: function (e, ui) {
            assignOrderForScreenFields();
        }
    });
    jQuery(".cpFieldsTable tbody").disableSelection();
    jQuery('.requiredDefaultValue', jQuery('#provider_group_id-default-value-container')).change(function () {
        if (this.value > 0) {
            reloadUsersListByAssignedTeam(this.value, jQuery('.requiredDefaultValue', jQuery('#assignee-default-value-container')));
        }
    });

    assignOrderForScreenFields();
    jQuery('#case_type_id', screenForm).on('change', function () { //retrun Custom FIelds related to the category and practice area
        if (this.value && jQuery('#applicable_on', screenForm).val()) {
            returnRelatedFields(jQuery('#applicable_on', screenForm).val(), this.value);
        }
    });

    jQuery('#applicable_on, #case_type_id', screenForm).change(function (){
        validateScreenDataCombination(jQuery('#applicable_on', screenForm).val(), jQuery('#case_type_id', screenForm).val());
    });
    // initialize Notification Tab
    jQuery('#notify-to', "#request-type-tabs").selectize({
        plugins: ['remove_button'],
        placeholder: _lang.usersExternalEmails,
        delimiter: ';',
        persist: false,
        maxItems: null,
        valueField: 'email',
        labelField: 'email',
        searchField: ['email'],
        createOnBlur: true,
        options: availableEmails,
        items: externalToEmails,
        render: {
            item: function (item, escape) {
                return '<div>' +
                    (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                    '</div>';
            }
        },
        createFilter: function (input) {
            var match, regex;
            // email@address.com
            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
            match = input.match(regex);
            if (match)
                return !this.options.hasOwnProperty(match[0]);

            return false;
        },
        create: function (input) {
            if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
                return {email: input};
            }
            alert('Invalid email address.');
            return false;
        }
    });
    jQuery('#notify-cc', "#request-type-tabs").selectize({
        plugins: ['remove_button'],
        placeholder: _lang.usersExternalEmails,
        delimiter: ';',
        persist: false,
        maxItems: null,
        valueField: 'email',
        labelField: 'email',
        searchField: ['email'],
        createOnBlur: true,
        options: availableEmails,
        items: externalCCEmails,
        render: {
            item: function (item, escape) {
                return '<div>' +
                    (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                    '</div>';
            }
        },
        createFilter: function (input) {
            var match, regex;
            // email@address.com
            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
            match = input.match(regex);
            if (match)
                return !this.options.hasOwnProperty(match[0]);

            return false;
        },
        create: function (input) {
            if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
                return {email: input};
            }
            alert('Invalid email address.');
            return false;
        }
    });
    if (screenMode === 'edit'){
        getPracticeAreas(applicableOn, function() {
            jQuery('#case_type_id option[value='+ jQuery('#hidden-case-type-id').val() +']').attr('selected', 'selected');
            jQuery('#request_type_category_id option[value='+ jQuery('#hidden-request-type-category-id').val() +']').attr('selected', 'selected');
        });
    }
});

function assignOrderForScreenFields() {
    jQuery('.cpFieldsTable tbody input[name="sortOrder[]"]').each(function () {
        jQuery(this).val(jQuery(this).parents('tr').index() + 1);
    });
}

function changeVisibleField(obj, checked) {
    // on change the visible field in A4L by put it as non visible => display the default value
    var field = jQuery(obj).parent().attr("fieldName");
    if (checked == 'yes') {
        jQuery('.visible-hidden-value', 'td[fieldName="' + field + '"]').val(1);
        jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).val('').addClass('d-none');
        jQuery('#selected-' + field, jQuery('#' + field + '-default-value-container')).html('').addClass('d-none');
        if (field === 'provider_group_id' || field === 'assignee') {//If the assigned team is Visible (Yes) => assignee can either be Yes or No (either required or no with no default values)
            jQuery('.requiredDefaultValue', jQuery('#assignee-default-value-container')).val('').addClass('d-none');
            jQuery('.assignee-assignment-rule', jQuery('#assignee-default-value-container')).addClass('d-none');
            jQuery('.assignee-assignment-rule input[type=radio]', '#assignee-default-value-container').attr('disabled', 'disabled');
        }
        if(jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).hasClass('date')){
            jQuery('.date-picker-' + field).addClass('d-none');
        }
    } else {
        if(jQuery('.requiredHiddenValue', 'td[fieldName="' + field + '"]').val() == "1"){
            jQuery('.visible-hidden-value', 'td[fieldName="' + field + '"]').val(0);
            if (field === 'assignee' && (jQuery('.cp-field-visible-list1', '#provider_group_id').val() === 'yes')) {
                return false;
            }
            if (field === 'assignee') {
                jQuery('.assignee-assignment-rule', jQuery('#assignee-default-value-container')).removeClass('d-none');
                radioButtonsEvents();
            }
            pinesMessage({ty: 'information', m: _lang.cpRequiredShouldHaveDefaultVal});
            jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).val('').removeClass('d-none');
            jQuery('#selected-' + field, jQuery('#' + field + '-default-value-container')).html('').removeClass('d-none');
            if (field === 'provider_group_id' && jQuery('.cp-field-visible-list1', '#assignee').val() === 'no') {//if the assigned team is Not visible (it has a default value) => assignee can either be Yes(visible) or No 
                //If No, the user should choose from the 2 options in the default value either a specific user (to be chosen from the dropdown) or Round Robin
                jQuery('.assignee-assignment-rule', jQuery('#assignee-default-value-container')).removeClass('d-none');
                jQuery('.requiredDefaultValue', jQuery('#assignee-default-value-container')).val('').removeClass('d-none');
                radioButtonsEvents();
            }
            if(jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).hasClass('date')){//date
                jQuery('.date-picker-' + field).removeClass('d-none');
            }
            if(jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).hasClass('time')){//date and time
                jQuery('.time-picker-' + field).removeClass('d-none');
            }
            if(jQuery('.requiredDefaultValue', jQuery('#' + field + '-default-value-container')).hasClass('select-picker')){//list
                jQuery('.select-picker').selectpicker();
            }
        }else{
            jQuery('.cp-field-visible-list1', '#' + field).val('yes');
            pinesMessage({ty: 'warning', m: _lang.feedback_messages.fieldCantBeNotRequiredAndHidden});
        }
    }
        
}

function updateRequiredField(select) {
    // on change the optional field in A4L
    var field = jQuery(select).parent().attr("fieldName");
    if (select.value == 'yes') {
        jQuery('.requiredHiddenValue', 'td[fieldName="' + field + '"]').val(1);
    } else {
        if(jQuery('.visible-hidden-value', 'td[fieldName="' + field + '"]').val() == "1"){
            jQuery('.requiredHiddenValue', 'td[fieldName="' + field + '"]').val(0);
        }else{
            jQuery('.cp-field-required-list1', '#' + field).val('yes');
            pinesMessage({ty: 'warning', m: _lang.feedback_messages.fieldCantBeNotRequiredAndHidden});
        }
    }
}

var attachment_count = 1;
function addNewScreenField() {
    var field = jQuery('#screenField').val();
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

function submitScreenForm() {
    var screenForm = jQuery('#screenForm');
    if (screenForm.validationEngine('validate')) {
        if (jQuery('.screenField', '.fieldsContainer').length > 0) {
            var atLeastOnevisible = false;
            jQuery('.visible-dropdown option:selected', '.fieldsContainer').each(function () {
                if (this.value == 'yes') {
                    atLeastOnevisible = true;
                }
            });
            if (atLeastOnevisible) {
                screenForm.submit();
            } else {
                pinesMessage({ty: 'warning', m: _lang.screenFormShouldContainsOneFieldAtLeast});
            }

        } else {
            pinesMessage({ty: 'warning', m: _lang.screenFieldsRequired});
        }
    }
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
            url: getBaseURL() + moduleController + '/autocomplete?term=%QUERY',
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
        url: getBaseURL() + 'users/autocomplete/active',
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

function radioButtonsEvents() {
    jQuery('.assignee-assignment-rule input[type=radio]', '#assignee-default-value-container').removeAttr('disabled');
    jQuery('.assignee-assignment-rule input[type=radio]', jQuery('#assignee-default-value-container')).on('change', function () {
        if (this.value == 'specific_user') {
            jQuery('.requiredDefaultValue', jQuery('#assignee-default-value-container')).removeClass('d-none');
        } else {
            jQuery('.requiredDefaultValue', '#assignee-default-value-container').val('').addClass('d-none');
        }
    });
}

function getPracticeAreas(category, callback) {
    jQuery.ajax({
        url: getBaseURL() + "case_types/get_case_types_by_case_category",
        method: "post",
        dataType: "JSON",
        data: {category: category},
        success: function (response) {
            if (response.result) {
                var html = '';
                html += '<option selected value="">' + caseTypesListDefaultOption + '</option>'
                for (var i = 0; i < response.case_types.length; i++) {
                    html += '<option value="' + response.case_types[i].id + '">' + response.case_types[i].name + '</option>';
                }
                jQuery('#case_type_id').html(html);
            }
            if (typeof(callback) == 'function') {
                callback();
            }
        }
    });
}

function returnRelatedFields(category, pacticeArea) {
    var container = jQuery('#screenForm');
    jQuery.ajax({
        url: getBaseURL() + "customer_portal/return_related_fields",
        method: "post",
        dataType: "JSON",
        data: {category: category, type: pacticeArea},
        success: function (response) {
            if (response.result && response.fields) {
                jQuery('option.cf-options', container).remove();
                jQuery('.fieldsPackageList option[value^="customField_"]').remove();
                jQuery.each(response.fields, function (key, value) {
                    jQuery('#screenField', container).append('<option value="' + key + '" class="cf-options">' + value + '</option>');

                });
                jQuery('#screenField', container).trigger("chosen:updated");
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

function validateScreenDataCombination(applicableOn, caseTypeId){
    var container = jQuery('#screenForm');
    jQuery('#screen-submit', container).attr("disabled", true);
    jQuery.ajax({
        url: getBaseURL() + "customer_portal/validate_data_combination",
        method: "post",
        dataType: "JSON",
        data: {applicable_on: applicableOn, case_type_id: caseTypeId},
        success: function (response) {
            if(!response.status){
                jQuery("#applicable_on", container).val(jQuery("#hidden-applicable-on", container).val() != "" ? jQuery("#hidden-applicable-on", container).val() : "");
                jQuery("#case_type_id", container).val(jQuery("#hidden-case-type-id", container).val() != "" ? jQuery("#hidden-case-type-id", container).val() : "");
                pinesMessage({ty: 'warning', m: response.message});
            }else{
                jQuery("#hidden-applicable-on", container).val(jQuery("#applicable_on", container).val());
                jQuery("#hidden-case-type-id", container).val(jQuery("#case_type_id", container).val());
            }
        },complete: function(){
            jQuery('#screen-submit', container).attr("disabled", false);
        }
    });
}

function validateMultiSelectlookup(field, rules, i, options){
    if(!jQuery('#' + field[0]['id']).hasClass('d-none')){
        if(!jQuery(".multi-option-selected-items").is('[id^=' + field[0]['id'].substring(7) + ']')){
            rules.push('required');
        }
    }
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