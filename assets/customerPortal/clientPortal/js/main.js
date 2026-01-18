
function setActiveTab(tabId) {
    jQuery('#cp-tabs-list li.active').removeClass('active');
    jQuery('#cp-tabs-list li#cp-tab-' + tabId).addClass('active');
}

function addTicketComment(ticketId) {
    jQuery.ajax({
        url: 'modules/customer-portal/tickets/addComment/' + ticketId,
        dataType: 'JSON',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (jQuery('#ticketCommentFormContainer').length == 0) {
                jQuery('<div id="ticketCommentFormContainer" class="d-none"></div>').appendTo('body');
            }
            var ticketCommentContainer = jQuery('#ticketCommentFormContainer');
            ticketCommentContainer.html(response.html).removeClass('d-none');
            jQuery('.modal', ticketCommentContainer).modal({
                keyboard: false
            });
            jQuery("#saveBtn").click(function () {
                jQuery("#loader-global").show();
                jQuery("form#ticketCommentForm", ticketCommentContainer).submit();
            });
        }
    });
}

function cpUploadDocumentDone(type, error) {
    if (type == 'success') {
        window.location = window.location.href;
    } else {
        jQuery("#loader-global").hide();
        jQuery('#errorMessgae').html(error);
    }
}

jQuery(document).ready(function () {
    jQuery(".submit-with-loader").click(function () {
        jQuery(".form-submit-loader").show();
        var formDataIsValid = jQuery(this).parents('form:first').validationEngine('validate');
        jQuery(this).parents('form:first').find('input').focus(function () {
            jQuery(".form-submit-loader").hide();
        });
    });
    displayMessage();
});

//hide the license msg
function hideLicenseMessage() {
    jQuery('.lisence-message').remove();
    jQuery.ajax({
        url: 'modules/customer-portal/home/hide_license_warning/'
    });
}

/*
 * Lookup for customer_portal_users
 * Retreiving customer_portal_users depending on the term entered with 1 characters and above
 * @param array lookupDetails( details for the lookup input),string container(jQuery selector of modal container),boolean isBoxContainer(whether the lookup field will be set in a box or input)
 */
function lookUpCustomerPortalUsers(lookupDetails, container, isBoxContainer, controller) {
    isBoxContainer = isBoxContainer || false;
    controller = controller || 'tickets';
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
            url: getBaseURL('customer-portal') + controller+ '/customer_portal_users_autocomplete?term=%QUERY&object_category=' + jQuery('#object-category').val() + '&object_id=' + jQuery('.object-id').val(),
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupDetails['lookupField']).typeahead({
            hint: false,
            highlight: true,
            minLength: 3
        },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (!isBoxContainer) {
                    return item.firstName + ' ' + item.lastName;
                }
            },
            templates: {
                empty: [
                    '<div class="empty click" ></div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + data.firstName + ' ' + data.lastName + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
        if (isBoxContainer) {
            jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
            lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
            setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), {
                id: datum.id,
                value: datum.firstName + ' ' + datum.lastName,
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
//initialize objects customer portal
function objectInitialization(object, container) {
    var objectsCount = jQuery('#' + object + '-count', container).val();
    var count = 1;
    jQuery('.' + object + '-div', container).each(function () {
        if (count > objectsCount) {
            return;
        }
        objectContainer = jQuery('#' + object + '-' + count, container);
        objectEvents(object, objectContainer);
        count++;
    });
}

//clone object container customer portal
function objectContainerClone(object, container, event) {
    var objectContainer = jQuery('#' + object + '-container', container);
    var nbOfObject = parseInt(jQuery('#' + object + '-count', objectContainer).val());
    jQuery('#' + object + '-member-type', jQuery('.' + object + '-div', objectContainer).last()).selectpicker('destroy');//Before cloning ,destroy selectpicker in the container to clone from
    jQuery('#' + object + '-category', jQuery('.' + object + '-div', objectContainer).last()).selectpicker('destroy');//Before cloning ,destroy selectpicker in the container to clone from
    var clonedHtml = jQuery('.' + object + '-div', objectContainer).last().clone();
    var newId = nbOfObject + 1;
    var clonedContainer = '#' + object + '-' + (newId);
    var clonedDiv = clonedHtml.insertAfter(jQuery('.' + object + '-div', objectContainer).last());
    clonedDiv.attr("id", '' + object + '-' + (newId));
    jQuery('#' + object + '-member-type', jQuery('#' + object + '-' + nbOfObject, objectContainer)).selectpicker();//after cloning re initialize the selectpicker of the picker that was destroyed
    jQuery('#' + object + '-category', jQuery('#' + object + '-' + nbOfObject, objectContainer)).selectpicker();//after cloning re initialize the selectpicker of the picker that was destroyed
    jQuery('#' + object + '-count', objectContainer).val(newId);
    jQuery('.delete-link-' + object, jQuery(clonedContainer, objectContainer)).attr("onclick", 'objectDelete(\'' + object + '\', ' + newId + ',\'' + container + '\' , event)');
    jQuery('.label-count', jQuery(clonedContainer, objectContainer)).html(' (' + (newId) + ')');
    jQuery('#' + object + '-member-id', jQuery(clonedContainer, objectContainer)).val('');
    jQuery('#' + object + '-lookup', jQuery(clonedContainer, objectContainer)).val('');
    jQuery('#' + object + '-member-type', jQuery(clonedContainer, objectContainer)).find('option').removeAttr('selected');
    jQuery('#' + object + '-category', jQuery(clonedContainer, objectContainer)).find('option').removeAttr('selected');
    jQuery('.delete-' + object, jQuery(clonedContainer, objectContainer)).removeClass('d-none');
    jQuery('.inline-error', jQuery(clonedContainer, objectContainer)).attr('data-field', '' + object + '_member_id_' + newId).html('');
    jQuery('#' + object + '-category', jQuery(clonedContainer, objectContainer)).attr('data-field-id', '' + object + '-category-' + newId);
    jQuery('.' + object + '-category-quick-add', jQuery(clonedContainer, objectContainer)).removeAttr('onClick').click(function () {
     });
    if (nbOfObject == 1) {
        jQuery('.delete-' + object, jQuery('#' + object + '-' + (nbOfObject), objectContainer)).removeClass('d-none');
    }

    objectEvents(object, jQuery(clonedContainer, objectContainer));

    event.preventDefault();
}

//load object events customer portal
function objectEvents(object, container) {
    var idName = container.attr('id');
    var objectId = '#' + idName;
    var objectNumber = idName.charAt(idName.length - 1);
    var lookupDetails = {
        'lookupField': jQuery('#' + object + '-lookup', container),
        'hiddenInput': jQuery('#' + object + '-member-id', container),
        'errorDiv': object + '_member_id_' + objectNumber,
        'resultHandler': setDataToObjectField
    };
    jQuery('#' + object + '-member-type', container).selectpicker().change(function () {
        jQuery('#' + object + '-lookup', container).val('');
        jQuery('#' + object + '-member-id', container).val('');
        jQuery(".inline-error", container).html('');
        jQuery('#' + object + '-lookup', container).typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it.
        companyContactFormMatrix.commonLookup[objectId]['lookupType'] = jQuery("select#" + object + "-member-type", container).val();
        lookupCompanyContactType(lookupDetails, container, false , 'customer-portal' , systemPreferenceContact);
    });
    jQuery('#' + object + '-category', container).selectpicker();
    companyContactFormMatrix.commonLookup[objectId] = {
        "lookupType": jQuery("select#" + object + "-member-type", container).val(),
        "referalContainerId": container
    }
    jQuery('#' + object + '-lookup', container).typeahead('destroy');//destroy the typeahead when changing the type(company/contact) and re-initialize it To avoid conflict of lookup initializing
    lookupCompanyContactType(lookupDetails, container , false , 'customer-portal', systemPreferenceContact);
}


//delete object customer portal
function objectDelete(object, objectId, container, event) {
    var objectContainer = jQuery('#' + object + '-container', container);
    var nbOfObjects = jQuery('#' + object + '-count', objectContainer).val();
    if (nbOfObjects > 1) {
        jQuery('#' + object + '-' + objectId, objectContainer).remove();
        companyContactFormMatrix.commonLookup = {};//empty the commonLookup array
        jQuery('#' + object + '-count', objectContainer).val(nbOfObjects - 1);
        objectUpdateLabels(object, container);
        if (jQuery('#' + object + '-count', objectContainer).val() == 1) {
            jQuery('.delete-' + object, objectContainer).addClass('d-none');

        }
    } else {
        pinesMessage({ ty: 'warning', m: _lang.invalid_request });
    }
    event.preventDefault();
}

function objectUpdateLabels(object, container) {
    var objectContainer = jQuery('#' + object + '-container', container);
    var nbOfObjects = jQuery('#' + object + '-count', objectContainer).val();
    var count = 1;
    jQuery('.' + object + '-div', objectContainer).each(function () {
        if (count <= nbOfObjects) {
            jQuery(this).attr("id", object + '-' + count);
            jQuery('.label-count', this).html(' (' + count + ')');
            jQuery('.inline-error', this).attr("data-field", object + '_member_id_' + count);
            jQuery('.delete-link-' + object, this).attr("onclick", 'objectDelete(\'' + object + '\', ' + count + ',\'' + container + '\' , event)');
            jQuery("[data-field=administration-party_categories]", this).attr("data-field-id", 'contract-categories-' + count);

            count++;
        } else {
            return true;
        }
    });
    objectInitialization(object, container);
}

function setDataToObjectField(record, container) {
    container = jQuery(container);
    var objectName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    var object = 'parties'; //to make it dynamic when using those funcions for other object
    jQuery('#' + object + '-member-id', container).val(record.id);
    jQuery('#' + object + '-lookup', container).val(objectName);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery('#' + object + '-lookup', container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#' + object + '-lookup', container),
            'hiddenInput': jQuery('#' + object + '-member-id', container),
            'errorDiv': object + '_member_id_' + container.attr('id').charAt(container.attr('id').length - 1) + '',
            'resultHandler': setDataToObjectField
        };
        lookupCompanyContactType(lookupDetails, container);
    }
}
/*
 * Lookup for customer_portal_users
 * Retreiving customer_portal_users depending on the term entered with 1 characters and above
 * @param array lookupDetails( details for the lookup input),string container(jQuery selector of modal container),boolean isBoxContainer(whether the lookup field will be set in a box or input)
 */
function lookUpCustomFields(lookupDetails, container, isBoxContainer) {
    var displaySegments = lookupDetails.lookupField.attr('display-segments').split(',');
    var displayFormatValue = lookupDetails.lookupField.attr('display-format-value');
    var displayFormatSingleSegment = lookupDetails.lookupField.attr('display-format-single-segment');
    var displayFormatDoubleSegment = lookupDetails.lookupField.attr('display-format-double-segment');
    var displayFormatTripleSegment = lookupDetails.lookupField.attr('display-format-triple-segment');
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
            url: getBaseURL('customer-portal') + 'tickets/lookup_custom_fields_autocomplete?lookup_type=' + lookupDetails.lookupField.attr('field-type-data') + '&term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupDetails['lookupField']).typeahead({
            hint: false,
            highlight: true,
            minLength: 3
        },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (!isBoxContainer) {
                    var displayName = customFieldLookupDataHandle(item, displaySegments, displayFormatSingleSegment, displayFormatDoubleSegment, displayFormatTripleSegment);
                    return displayName;
                }
            },
            templates: {
                empty: [
                    '<div class="empty click" ></div>'].join('\n'),
                suggestion: function (data) {
                    var displayName = customFieldLookupDataHandle(data, displaySegments, displayFormatSingleSegment, displayFormatDoubleSegment, displayFormatTripleSegment);
                    return '<div>' + displayName + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
        var displayName = customFieldLookupDataHandle(datum, displaySegments, displayFormatSingleSegment, displayFormatDoubleSegment, displayFormatTripleSegment, displayFormatValue);
        if (isBoxContainer) {
            jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
            lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
            setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), {
                id: datum.id,
                value: displayName,
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

function customFieldLookupDataHandle(data, displaySegments, displayFormatSingleSegment, displayFormatDoubleSegment, displayFormatTripleSegment, displayFormatValue) {
    displayFormatValue = displayFormatValue || null;
    var displayName = '';
    if (typeof displayFormatTripleSegment !== 'undefined') {
        if (data[displaySegments[2]] !== null && typeof data[displaySegments[2]] !== 'undefined') {
            displayName = displayFormatTripleSegment.sprintf([data[displaySegments[0]], data[displaySegments[1]], data[displaySegments[2]]]);
        } else if (data[displaySegments[1]] !== null && typeof data[displaySegments[1]] !== 'undefined') {
            displayName = data[displaySegments[0]] + ' ' + data[displaySegments[1]];
        } else {
            displayName = data[displaySegments[0]];
        }
    } else if (typeof displayFormatDoubleSegment !== 'undefined') {
        if (data[displaySegments[1]] !== null && typeof data[displaySegments[1]] !== 'undefined') {
            displayName = displayFormatDoubleSegment.sprintf([data[displaySegments[0]], data[displaySegments[1]]]);
        } else {
            displayName = data[displaySegments[0]];
        }
    } else {
        displayName = displayFormatSingleSegment.sprintf([data[displaySegments[0]]]);
    }
    return displayName;
}

/*
 * Common actions for lookup functions
 * Actions that are common for lookup functions
 * @param string lookupField( jQuery selector for lookup input ),inputIdField( jQuery selector for hidden input field ),string errorField( string name of the error field for the lookup ),string container( jQuery selector for modal container )
 */
function lookupCommonFunctions(lookupField, inputIdField, errorField, container) {
    jQuery(lookupField, container).on('input', function (e) {
        if (!jQuery('.tt-selectable', '.twitter-typeahead').length) {
            jQuery(inputIdField, container).val('');
        }
    });
    jQuery(lookupField, container).bind('typeahead:select', function (ev, suggestion) {
        jQuery(inputIdField, container).val(suggestion.id);
        jQuery("div", container).find("[data-field=" + errorField + "]").addClass('d-none');
    });

    jQuery(lookupField, container).keydown(function (e) {
        if (e.which !== 13 && e.which !== 9) { //neither enter nor tab
            jQuery(inputIdField, container).val('');
        }
    });
}

/*
 *Add classes to the box container of the lookup
 * @param string container( jQuery selector for lookup container )
 */
function lookupBoxContainerDesign(container) {
    jQuery('.autocomplete-helper', container).addClass('d-none');
    jQuery('.lookup-box-container', container).addClass('margin-bottom');
    jQuery('.inline-error', container).addClass('d-none');
}

/*
 *Add element to the box container of the lookup
 * @param string containerId (jQuery selector for box container ),  string lookupClassContainer (jQuery selector for lookup container ), string lookupClassContainer (jQuery selector for form container ), array setOption (array of id ,value and name)
 */
function setNewBoxElement(containerId, lookupClassContainer, formContainer, setOption, onSelect, onSelectParameters) {
    var wrapper = jQuery(containerId, formContainer);
    var lookupBoxContainer = jQuery('.' + lookupClassContainer, formContainer);
    lookupBoxContainerDesign(lookupBoxContainer);
    var removeContainer='#' + setOption.name + setOption.id;
    if (setOption.id && !jQuery('#' + setOption.name + setOption.id, wrapper).length) {
        var onSelectString = '';
        if (typeof onSelect !== "undefined") {
            onSelectString = (typeof onSelectParameters !== "undefined" ? (onSelect + '(' + JSON.stringify(onSelectParameters) + ');').replace(/"/g, "&quot;") : (onSelect + '();'));
        }
        wrapper.css('border', 'solid 1px #ddd');
        wrapper.append(jQuery('<div class="d-flex justify-content-between multi-option-selected-items no-margin" id="' + setOption.name + setOption.id + '">' + '<div><span id="' + setOption.id + '">' + setOption.value + '</span>').append(jQuery('<input type="hidden" value="' + setOption.id + '" name="' + setOption.name + '[]" /></div>')).append(jQuery('<div><a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button flex-end-item" tabindex="-1" onclick="removeBoxElement(\''+ removeContainer +'\',\'' + containerId + '\',\'' + lookupClassContainer + '\',\'' + formContainer + '\');' + onSelectString + '"><i class="fa-solid fa-x"></i></a></div></div>')));
        if (typeof onSelect !== "undefined") {
            typeof onSelectParameters !== "undefined" ? window[onSelect](onSelectParameters) : window[onSelect]();
        }
    }
}

/*
 *Remove element from the mutli option box
 *If the box is empty(no element left) then remove the border of the box and add re show the inline text of the lookup
 *@param string element( jQuery selector for the element to remove ),string container( jQuery selector for the box container ),string lookupClassContainer( jQuery selector for lookup container ),string formContainer( jQuery selector for form container )
 */
function removeBoxElement(element, container, lookupClassContainer, formContainer) {
    var boxContainer = jQuery(container, formContainer);
    var lookupContainer = jQuery('.' + lookupClassContainer, formContainer);
    jQuery(element, boxContainer).remove();
    if (boxContainer.html().trim() == '') {
        jQuery('.lookup-box-container', lookupContainer).removeClass('margin-bottom');
        jQuery('.autocomplete-helper', lookupContainer).removeClass('d-none');
        boxContainer.css('border', 'none');
        boxContainer.removeClass('border');
    }
}

/*
 * handle the required state of required multi-select lookup field in customer portal add screen
 * if the lookup is empty the required attribute is added if not the attribute is removed
 * @param abject parametersObject(an object with the following properties fieldName, isRequired, selectedItemContainer)
 */
function handleRequiredMultiselect(parametersObject) {
    if (parametersObject.isRequired == 1) {
        if (jQuery(parametersObject.selectedItemContainer).html() != '') {
            jQuery(parametersObject.fieldName).removeAttr('required');
        } else {
            jQuery(parametersObject.fieldName).attr('required', 'required');
        }
    }
}

/*
 * get query string parameter
 * @param name(the name of the parameter)
 * @param url(the url to get the parameter from [optional])
 * @return value of the paramter
 */
function getParameterByName(name, url) {
    if (!url) {
        url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results)
        return null;
    if (!results[2])
        return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

/*
 * display a pines message that is derived from another page through url query string
 */
function displayMessage() {
    var type = getParameterByName("ty");
    var message = getParameterByName("m");
    if (type != null && message != null) {
        window.history.replaceState("", "", getBaseURL('customer-portal/tickets'));
        pinesMessage({ty: type.toString(), m: message.toString()});
    }
}

/*
 * handle lookup input when the user type a string in the text box and leave the field without selecting an option
 * @param string hiddenInputId(id of the hidden input wish hold the id of the record)
 * @param string lookupId(id of lookup field)
 */
function handleLookupInput(hiddenInputId, lookupId) {
    if (jQuery(hiddenInputId).val() == '') {
        jQuery(lookupId).typeahead('val', '');
    }
}

/*
 * handle multiselect lookup input when the user type a string in the text box and leave the field without selecting an option
 * @param string lookupId(id of the multiselect lookup field)
 */
function handleMultiselectLookupInput(lookupId) {
    if (jQuery(lookupId).val() != '') {
        jQuery(lookupId).typeahead('val', '');
    }
}

function lookUpA4LUser(lookupDetails, container, isBoxContainer) {
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
            url: getBaseURL('customer-portal') + 'users/autocomplete?term=%QUERY',
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
                    return item.firstName + ' ' + item.lastName;
                }
            },
            templates: {
                empty: [
                    '<div class="empty click" ></div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + data.firstName + ' ' + data.lastName + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
        if (isBoxContainer) {
            jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
            lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
            setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), {
                id: datum.id,
                value: datum.firstName + ' ' + datum.lastName,
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

// display server side validation
function displayValidationErrors(errors, container) {
    jQuery('.inline-error', container).removeClass('validation-error');
    jQuery('.input-warning', container).removeClass('input-warning');
    var selector;
    for (i in errors) {
        selector = jQuery("div", container).find("[data-field=" + i + "]").length > 0 ? jQuery("div", container).find("[data-field=" + i + "]") : (jQuery("td", container).find("[data-field=" + i + "]").length > 0 ? jQuery("td", container).find("[data-field=" + i + "]") : false);
        if (selector) {
            selector.removeClass('d-none').html(errors[i]).addClass('validation-error');
            jQuery("input[data-field=" + i + "]", container).each(function () {
                if (this.value === '') {
                    jQuery(this).addClass('input-warning');
                }
            });
        } else {
            pinesMessage({ty: 'error', m: errors[i]});
        }

    }
    scrollToValidationError(container);
}

//scroll to the validation error when submitting any of the dialogs(case,contact,company,task..)
function scrollToValidationError(container) {
    jQuery('.modal-body').scrollTo(jQuery('div.validation-error:first', container).parent());
}

/**
 * mange drop down position
 * @param {DOM} container
 */
function dropDownPosition(container) {
    jQuery('.dropdown-toggle', container).on('click', function () {
        jQuery(this).each(function () {
            var openInside = (jQuery(container).offset().top + jQuery(container).height()) / 1.5;
            if (jQuery(this).offset().top > openInside && _lang.languageSettings['langName'] != 'arabic') {
                var dopDownHeight = (jQuery(".select-picker .dropdown-menu.open", container).height() * 10 / 100 > 400) ? (134 * 10 / 100) : jQuery(".select-picker .dropdown-menu.open", container).height() * 10 / 100;
                jQuery(".bs-container.btn-group.bootstrap-select.select-picker.open").css(
                    {
                        "top": jQuery(this).offset().top - dopDownHeight,
                        "left": jQuery(this).offset().left + jQuery(this).width() + parseInt(jQuery(this).css('borderLeftWidth')) + parseInt(jQuery(this).css('padding-left')) + parseInt(jQuery(this).css('padding-right'))
                    }
                );
            }
        });
    });
}

function confirmationDialog(key, resultHandlerArray) {
    var confirmationCategory = resultHandlerArray.confirmationCategory ? resultHandlerArray.confirmationCategory : 'default'; // this flag will be used to color the button "yes", the default is blue
    jQuery.ajax({
        url: getBaseURL() + 'home/confirm_request/',
        dataType: 'JSON',
        type: 'POST',
        data: {
            key_message: key,
            confirmation_category: confirmationCategory // default => blue, danger => red (btn-danger), warning => orange (btn-warning), success => green (btn-warning), ...
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery(".confirmation-dialog-container").length <= 0) {
                    jQuery('<div class="d-none confirmation-dialog-container"></div>').appendTo("body");
                    var confirmationContainer = jQuery('.confirmation-dialog-container');
                    confirmationContainer.html(response.html).removeClass('d-none');
                    jQuery('.modal', confirmationContainer).addClass("confirmation-dialog-modal");
                    jQuery('.modal', confirmationContainer).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'
                    });
                    resizeMiniModal(confirmationContainer);
                    jQuery(window).bind('resize', (function () {
                        resizeMiniModal(confirmationContainer);
                    }));
                    jQuery("#cancel-confirmation-dialog", confirmationContainer).click(function () {
                        if (resultHandlerArray.onCloseHandler) {
                            resultHandlerArray.onCloseHandler(resultHandlerArray.onCloseParm ? resultHandlerArray.onCloseParm : false);
                        }
                    });
                    jQuery("#confirmation-dialog-submit", confirmationContainer).click(function () {
                        if (!resultHandlerArray.resultHandler) {
                            jQuery('.modal', confirmationContainer).modal('hide');
                            resultHandlerArray.modelOpen.modal('hide');
                            return;
                        }
                        resultHandlerArray.resultHandler(resultHandlerArray.parm ? resultHandlerArray.parm : false, typeof resultHandlerArray.module !== 'undefined' ? resultHandlerArray.module : false);
                        jQuery('.modal', confirmationContainer).modal('hide');
                    });
                    jQuery("input", confirmationContainer).keypress(function (e) {
                        if (e.which == 13) {
                            e.preventDefault();
                            jQuery("#confirmation-dialog-submit", confirmationContainer).click();
                        }
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(confirmationContainer);
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function destroyModal(container) {
    container.remove();
}
//common functions used to all bootstrap modal dialog
function commonModalDialogEvents(container, submitFunction) {
    submitFunction = submitFunction || false;
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
    jQuery('.modal-body',container).on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery(".modal", container).modal("hide");
        }
    });
    jQuery('.modal', container).on('hidden.bs.modal', function () {
        destroyModal(container);
    });
    if (submitFunction) {
        jQuery("#form-submit", container).click(function () {
            submitFunction(container);
        });
        jQuery(container).find('input').keypress(function (e) {
            // Enter pressed?
            if (e.which == 13) {
                e.preventDefault();
                submitFunction(container);
            }
        });
    }
    jQuery('.search').attr('autocomplete','off');
    jQuery('.lookup').attr('autocomplete','off');
}

function signupForm(){
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('customer-portal') + 'users/signup',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#signup-dialog').length <= 0) {
                    jQuery('<div id="signup-dialog"></div>').appendTo("body");
                    var signupDialog = jQuery('#signup-dialog');
                    signupDialog.html(response.html);
                    commonModalDialogEvents(signupDialog, signupFormSubmit);
                    initializeModalSize(signupDialog, 0.5, 0.5);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function signupFormSubmit(container){
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        data: formData,
        url: getBaseURL('customer-portal') + 'users/signup',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if(response.result){
                jQuery('.modal', '#signup-dialog').modal('hide');
                pinesMessage({ ty: 'information', m: response.approved ? _lang.signupSuccessfullYouCanLogin : _lang.signupSuccessfullAccountShouldBeApproved });
            }else{
                jQuery('.inline-error', container).removeClass('validation-error');
                for (i in response.validationErrors) {
                    jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
                }
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('#loader-global').hide();
        },
    });
}

var uploadNumber = 2;
function addFileInput(event, reference) {
    let elementToAppendTo = jQuery(jQuery(event.target));
    let additionalClass = '';
    if(reference == 'contract') {
        elementToAppendTo = elementToAppendTo.parent().prev();
        additionalClass = 'p-0';
    } else {
        elementToAppendTo = elementToAppendTo.prev();
    }
    elementToAppendTo.append(`<div class="attachment-container d-flex ${additionalClass}"><div class="col-md-12 col-xs-11 no-padding"><input type="file" class="trim-file-width" name="attachment_${uploadNumber}" id="attachment-${uploadNumber}"/></div>&nbsp;&nbsp;<button type="button" class="remove-file-input col-2" onclick="jQuery(this).parent().remove();">×</button><input type="hidden" name="attachment[]" value="attachment_${uploadNumber}"/></div>`);
    uploadNumber++;
}

/*
 *Add company
 *Open company dialog and load the even 
 */
function companyAddForm() {
    var randomNumber = Math.round(Math.random() * (upperBound - lowerBound) + lowerBound);
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('customer-portal') + 'companies/add',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var companyDialogId = '#company-dialog-' + randomNumber;
                if (jQuery(companyDialogId).length <= 0) {
                    jQuery('<div id="company-dialog-' + randomNumber + '"></div>').appendTo("body");
                    companyContactFormMatrix[companyDialogId] = {
                        "referalContainerId": companyContactFormMatrix.companyDialog.referalContainerId,
                        "lookupResultHandler": companyContactFormMatrix.companyDialog.lookupResultHandler,
                        "lookupValue": companyContactFormMatrix.companyDialog.lookupValue
                    };
                    companyContactFormMatrix.companyDialog = {};
                    var companyDialog = jQuery(companyDialogId);
                    companyDialog.html(response.html);
                    jQuery("form", companyDialog).attr('id', 'company-add-form-' + randomNumber);
                    jQuery( "#name", companyDialog).focusout(function() {
                        jQuery('#shortName', companyDialog).val(jQuery(this).val().substring(0, 14));
                    });
                    companyAddFormEvents(companyDialogId);
                    if (companyContactFormMatrix[companyDialogId].lookupValue) {
                        jQuery('#name', companyDialog).val(companyContactFormMatrix[companyDialogId].lookupValue);
                    }
                    if (jQuery('#company-group-grid').length) {
                        if (jQuery('#container-id', '#sub-title').val()) {
                            jQuery('#company-id', companyDialog).selectpicker('val', jQuery('#container-id', '#sub-title').val());
                        }
                    }
                    jQuery('.modal', companyDialog).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function companyAddFormEvents(container) {
    // Functions to be loaded before opening the add company modal
    jQuery('.select-picker', container).selectpicker({
        dropupAuto: false
    });
    if (jQuery('.hijri-date-picker', '#cr-released-on-hijri-container').length > 0) {
        makeFieldsHijriDatePicker({ fields: ['crReleasedOn'] });
        jQuery('#crReleasedOn', '#cr-released-on-hijri-container').val(gregorianToHijri(jQuery('#cr-released-on-gregorian', '#cr-released-on-hijri-container').val()));
    } else {
        setDatePicker('.cr-released-on', container);
    }
    var lookupDetails = {
        'lookupField': jQuery('#lookup-lawyers', container),
        'lookupContainer': 'lawyer-lookup-container',
        'errorDiv': 'lookupLawyers',
        'boxName': 'companyLawyers',
        'boxId': '#selected-lawyers',
        'resultHandler': setSelectedContactToCompany
    };
    lookUpContacts(lookupDetails, jQuery(container), true);
    initializeModalSize(container);
    jQuery("#save-company-btn", container).click(function () { //submit the form
        companyAddFormSubmit(container , 'customer-portal');
    });
    jQuery("#show-more-fields", container).click(function () { //show more fields
        showMoreFields(container, jQuery('#capitalCurrency', container));
    });
    jQuery("#show-less-fields", container).click(function () { //show less fields
        showLessFields(container);
    });
    jQuery("#add-container", container).click(function () { //add company container
        companyContainerForm('', container);
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.cr-released-on', container).bootstrapDP("remove");
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            companyAddFormSubmit(container , 'customer-portal');
        }
    });
    jQuery('.modal-body', container).on("scroll", function () {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(jQuery(container));
        delete companyContactFormMatrix[container]; //delete the array of this container if the modal was closed
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#name', container).focus();
    });
}

/*
 * Add new contact
 * Open contact dialog to add
 * @param boolean enableCreateAnother(whether to show the create another button or no),string cloneFromId( id of the contact in edit mode )
 */
function contactAddForm(enableCreateAnother, cloneFromId) {
    var randomNumber = Math.round(Math.random() * (upperBound - lowerBound) + lowerBound);
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('customer-portal') + (cloneFromId ? ('contacts/clone_contact/' + cloneFromId) : 'contacts/add'),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var contactDialogId = "#contact-dialog-" + randomNumber;
                if (jQuery(contactDialogId).length <= 0) {
                    jQuery('<div id="contact-dialog-' + randomNumber + '"></div>').appendTo("body");
                    companyContactFormMatrix[contactDialogId] = {
                        "referalContainerId": companyContactFormMatrix.contactDialog.referalContainerId,
                        "lookupResultHandler": companyContactFormMatrix.contactDialog.lookupResultHandler,
                        "lookupValue": companyContactFormMatrix.contactDialog.lookupValue,
                        "company": companyContactFormMatrix.contactDialog.company,
                    };
                    companyContactFormMatrix.contactDialog = {};
                    var contactDialog = jQuery(contactDialogId);
                    contactDialog.html(response.html);
                    jQuery("form", contactDialog).attr('id', 'contact-add-form-' + randomNumber);
                    contactAddFormEvents(contactDialogId);
                    if (cloneFromId) {
                        checkBoxContainersValues({
                            'company-lookup-container': jQuery('#selected_companies', contactDialog),
                            'nationality-lookup-container': jQuery('#selected_nationalities', contactDialog)
                        }, contactDialog);
                    }
                    if (!enableCreateAnother) {
                        jQuery('.create-another-button', contactDialog).addClass('d-none');
                        jQuery('#save-contact-btn', contactDialog).css('border-radius', '4px');
                    }
                    jQuery('.modal', contactDialog).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
                    if (companyContactFormMatrix[contactDialogId].lookupValue) {
                        jQuery('#firstName', contactDialog).val(companyContactFormMatrix[contactDialogId].lookupValue);
                    }
                    if (companyContactFormMatrix[contactDialogId].company != null) {
                        setSelectedCompanyToContact(companyContactFormMatrix[contactDialogId].company, contactDialogId);
                    }
                    // setTimeout(addContactDemo,1500);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contactAddFormEvents(container) {
    // Function to be loaded before opening the add contact modal
    jQuery('.select-picker', container).selectpicker({
        dropupAuto: false
    });
    jQuery('#copyAddressFromType', container).on('change', function () {
        jQuery('#copyAddressFromLookup', container).typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it.
        lookupTypeToCopyAddress(jQuery('#copyAddressFromLookup', container), jQuery(container));
    });
    setDatePicker('.date-of-birth', container);
    jQuery(".date-of-birth", container).bootstrapDP('setStartDate', '1900-1-1');
    jQuery(".date-of-birth", container).bootstrapDP('setEndDate', getCurrentDate());
    lookUpNationalities(jQuery('#lookup-nationalities', container), "Contact_Nationalities", jQuery('.nationality-lookup-container', container), container , 'customer-portal');
    var lookupDetails = {
        'lookupField': jQuery('#lookupCompanies', container),
        'errorDiv': 'companies_contacts',
        'lookupContainer': jQuery('.company-lookup-container', container),
        'resultHandler': setSelectedCompanyToContact
    };
    lookUpCompanies(lookupDetails, jQuery(container) , 'customer-portal');
    lookupTypeToCopyAddress(jQuery('#copyAddressFromLookup', container), jQuery(container), true);
    initializeModalSize(container);
    jQuery("#show-more-fields", container).click(function () { //show more fields
        showMoreFields(container, jQuery('#mobile', container));
    });
    jQuery("#show-less-fields", container).click(function () { //show less fields
        showLessFields(container);
    });
    jQuery("#create-another", container).click(function () { //clone dialog
        cloneDialog(container, contactAddFormSubmit);
    });
    jQuery("#remove-nationality-element", container).click(function () { //remove nationality element
        removeBoxElement(jQuery(this.parentNode), '#selected_nationalities', 'nationality-lookup-container', container);
    });
    jQuery("#remove-watcher-element", container).click(function () { //remove watcher element
        removeBoxElement(jQuery(this.parentNode), '#selected-watchers', 'users-lookup-container', container);
    });
    jQuery("#remove-company-element", container).click(function () { //remove company element
        removeBoxElement(jQuery(this.parentNode), '#selected_companies', 'company-lookup-container', container);
    });
    jQuery("#isLawyerCheck", container).on('change', function () { //set lawyer to yes if checbox is checked else no
        jQuery('#isLawyer', container).val(this.checked ? 'yes' : 'no');
    });
    jQuery("#lawyerForCompanyCheck", container).on('change', function () { //set inhouse lawyer to yes if checbox is checked else no
        jQuery('#lawyerForCompany', container).val(this.checked ? 'yes' : 'no');
    });
    jQuery("#save-contact-btn", container).click(function () {
        contactAddFormSubmit(container , 'customer-portal');
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.date-of-birth', container).bootstrapDP("remove");
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            contactAddFormSubmit(container , 'customer-portal');
        }
    });
    jQuery('.modal-body', container).on("scroll", function () {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(jQuery(container));
        delete companyContactFormMatrix[container]; //delete the array of this container if the modal was closed(clone is not ture)
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery("div", container).find("[data-id=title]").focus();
    });
}

function showMoreText(linkId, moreId, dotsId) {
    var dots = jQuery('#' + dotsId);
    var moreText = jQuery('#' + moreId);
    var btnText = jQuery('#' + linkId);
    if (dots.hasClass("d-none")) {
        dots.removeClass("d-none");
        btnText.html("Show more");
        moreText.addClass("d-none");
    } else {
        dots.addClass("d-none");
        btnText.html("Show less");
        moreText.removeClass("d-none");
    }
  }
  /**
   * to ebale tiny myce editor for the text area
   * @param {*} id 
   * @param {*} containerTxt 
   * @param {*} module 
   */
  function initTinymyce(id, containerTxt, module) {
    tinymce.remove(containerTxt + ' #' + id);
    tinymce.init({
        selector: containerTxt + ' #' + id,
        menubar: false,
        statusbar: false,
        branding: false,
        height: 200,
        resize: false,
        relative_urls: false,
        remove_script_host: false,
        plugins: ['link', 'lists', 'paste', 'image'],
        link_assume_external_targets: true,
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline numlist bullist  | link | undo redo ' + (jQuery.inArray(module, ['task', 'meeting']) == -1 ? '| image code' : ''),
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        formats: {
            underline: { inline: 'u', exact: true },
            alignleft: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: { align: 'left' } },
            aligncenter: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: { align: 'center' } },
            alignright: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: { align: 'right' } },
            alignjustify: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: { align: 'justify' }
            },
            p: { block: 'p', styles: { 'font-size': '10pt' } },
        }, paste_preprocess: function (plugin, args) {
            tinymce.activeEditor.windowManager.alert('Some of the pasted content will not stay with the same format');
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                jQuery('#' + id + '_ifr', containerTxt).contents().find('body').attr("dir", "auto");
                e.pasteAsPlainText = true;
                jQuery('.mce-i-image').parent().on('click', function (e) {
                    setTimeout(function () {
                        jQuery('.mce-browsebutton input[type="file"]').attr('accept', '*');
                    }, 200);
                });
            });
        },
        /* without images_upload_url set, Upload tab won't show up*/
        images_upload_url: getBaseURL(module) + '/upload_file',
        /* we override default upload handler to simulate successful upload*/
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', getBaseURL() + module + '/upload_file');
            xhr.onload = function () {
                var json;
                json = JSON.parse(xhr.responseText);
                if (json.status == false) {
                    failure(json.message);
                    return;
                }
                if (!json) {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                var images = ['tif', 'jpg', 'png', 'gif', 'jpeg', 'bmp', 'jfif'];
                if (images.includes(json.file.extension)) {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, '<img data-name="' + json.file.full_name + '" src="' + getBaseURL(module)+ '/return_doc_thumbnail/' + json.file.id + '" width="100" height="100"  />');
                } else {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, '<a href="' + getBaseURL( module) + '/download_file/' + json.file.id + '" >' + json.file.full_name + '</a>');
                }
                var ed = parent.tinymce.editors[0];
                ed.windowManager.windows[0].close();
            };
            formData = new FormData();
            formData.append('uploadDoc', blobInfo.blob());
            formData.append('module_record_id', jQuery('#' + id, '#' + containerTxt).val());
            formData.append('module', module);
            formData.append('dragAndDrop', true);
            formData.append('lineage', '');
            xhr.send(formData);
        },
        init_instance_callback: function (editor) {
            editor.on('click', function (e) {
                jQuery('.bootstrap-select.open').removeClass('open');
            });
        }
    });
}
function setDatePicker(selector, container = document) {
    console.log('Initializing date picker for:', selector);
    // Find elements within the container (or whole document if not specified)
    const $elements = (container instanceof jQuery) 
        ? jQuery(selector, container) 
        : jQuery(container).find(selector);
    
    // Initialize datepicker if elements exist and plugin is available
    if ($elements.length && jQuery.fn.datepicker) {
        $elements.datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            startDate: '1900-01-01' // Better to set this in initialization
        });
    } else {
        console.warn('Datepicker elements not found or plugin not loaded', selector);
    }
}