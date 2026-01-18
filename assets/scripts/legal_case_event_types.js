var dataTypes, count, publicListsOptions, translatedListOptions = {};
var eventTypes;
function eventTypeForm(eventTypeId) {
    eventTypeId = eventTypeId || false;
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'legal_case_event_types/' + (eventTypeId ? 'edit/' + eventTypeId : 'add'),
        type: 'GET',
        data: {'returnForm': true},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html && jQuery('#event-type-form-container').length <= 0) {
                jQuery('<div id="event-type-form-container"></div>').appendTo("body");
                var eventTypeContainer = jQuery('#event-type-form-container');
                eventTypeContainer.html(response.html);
                jQuery('.tooltip-table').each(function (index, element) {
                    jQuery(element).tooltipster();
                });
                jQuery('tbody', eventTypeContainer).sortable();
                jQuery('.select-picker', jQuery('#field-1', eventTypeContainer)).selectpicker({dropupAuto: false});
                jQuery('.drop-down', jQuery('.event-static-fields', eventTypeContainer)).selectpicker({dropupAuto: false});
                jQuery('.select-picker', jQuery('.defined-fields', eventTypeContainer)).selectpicker({dropupAuto: false});
                if (jQuery('.defined-fields', eventTypeContainer).length > 0) { //edit mode
                    jQuery('.defined-fields', eventTypeContainer).each(function () {
                        eventTypeEvents('.field-types', this);
                        var fieldId = jQuery(this).attr('id');
                        translatedListOptions[fieldId] = {};
                        jQuery('#predefined-list-values', this).each(function () {
                            jQuery(this).selectize({
                                plugins: ['remove_button'],
                                placeholder: _lang.options,
                                delimiter: ',',
                                persist: false,
                                create: function (input) {
                                    return {
                                        value: input,
                                        text: input
                                    }
                                },
                                onItemRemove: function (value) {
                                    updateTranslatedListOptions(value, jQuery('#' + fieldId));
                                },
                            });
                            var definedOptions = jQuery(this).val().split(',');
                            jQuery.each(definedOptions, function (index, value) {
                                jQuery('#list-options-container input[id^=name-]', '#' + fieldId).each(function () {
                                    if (typeof translatedListOptions[fieldId][value] === 'undefined') {
                                        translatedListOptions[fieldId][value] = {};
                                    }
                                    var values = jQuery(this).val().split(',');
                                    translatedListOptions[fieldId][value][jQuery(this).attr('id')] = values[index];
                                });
                            });
                        });
                    });
                    eventTypes = {};
                    jQuery('.data-field-type', eventTypeContainer).each(function (i) {
                        if (dataTypes[this.value] === 'public_list') {
                            if (!jQuery.isArray(eventTypes[this.value])) {
                                eventTypes[this.value] = [];
                            }
                            eventTypes[this.value].push(jQuery(this).attr('field-id'));
                        }
                    });
                    jQuery.each(eventTypes, function (index, value) {
                        var eventTypesUsed = value.length;
                        if (eventTypesUsed > 1) {
                            jQuery.each(value, function (i, val) {
                                if (i + 1 !== eventTypesUsed) {
                                    jQuery('#list-options-container', '#' + val).addClass('d-none');
                                    jQuery('.translate-link', '#' + val).addClass('d-none');
                                    prependHiddenPublicList(val, false);
                                } else {
                                    prependHiddenPublicList(val, true);
                                }
                            });
                        } else {
                            prependHiddenPublicList(value[0], true);
                        }
                    });
                } else { //add mode
                    eventTypeEvents(jQuery('#field-1-types'), jQuery('#field-1', eventTypeContainer));
                }
                commonModalDialogEvents(eventTypeContainer, eventTypeFormSubmit);
                initializeModalSize(eventTypeContainer, 0.8, 0.65);
                jQuery('#is-sub-event', eventTypeContainer).on('click', function () {
                    enableDisableSubEvent(this, eventTypeContainer);
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function eventTypeFormSubmit(container) {
    var eventTypeId = jQuery('#event-type-id', container).val() ? jQuery('#event-type-id', container).val() : false;
    jQuery("#default-field-behavior").find("input,button,textarea,select").attr("disabled", "disabled");
    var formData = jQuery("form#event-type-form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'legal_case_event_types/' + (eventTypeId ? 'edit/' + eventTypeId : 'add'),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery('#event-types-table').removeClass('d-none');
                var tableRow = '<tr id="event-types-' + response.data.id + '"><td>' + response.data.name + '</td><td><a href="javascript:;" onclick="eventTypeForm(' + response.data.id + ');">' + _lang.edit + '</a>&nbsp; <a href="javascript:;" onclick="deleteEventType(' + response.data.id + ');">' + _lang.delete + '</a></td></tr>';
                if (eventTypeId) {
                    jQuery('#event-types-' + response.data.id).replaceWith(tableRow);
                } else {
                    jQuery('#event-types-table-body').append(tableRow);
                    var totalRecords = parseInt(jQuery('#rows-number', '#event-types-container').val(), 10) + 1;
                    jQuery('#rows-number', '#event-types-container').val(totalRecords);
                    jQuery('#total-records', '#event-types-container').html(totalRecords);
                }
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.modal', container).modal('hide');
            } else {
                if (response.validation_errors['field_type_details']) {
                    jQuery('.selectize-input', container).each(function () {
                        if (!jQuery(this).hasClass('has-items')) {
                            jQuery(this).addClass('input-warning');
                        }
                    });
                }
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.btn-save', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function eventTypeEvents(element, container) {
    jQuery('#is-public', container).addClass('d-none');
    jQuery('#public-list', container).attr('disabled', 'disabled');
    jQuery('#list-name-container input', container).val('').attr('disabled', 'disabled');
    if (dataTypes[jQuery(element, container).val()] !== 'list' && dataTypes[jQuery(element, container).val()] !== 'public_list') {
        jQuery('#list-options-container input', container).attr('disabled', 'disabled');
    }
    jQuery('#visible-to-all-checkbox', container).on('click', function () {
        if (jQuery('#visible-to-all-checkbox', container).prop('checked')) {
            jQuery('#public-list', container).val(true);
            jQuery('#list-name-container', container).removeClass('d-none');
            jQuery('#list-name-container input', container).removeAttr('disabled');
        } else {
            jQuery('#public-list', container).val(false);
            jQuery('#list-name-container input', container).val('').attr('disabled', 'disabled');
            jQuery('#list-name-container', container).addClass('d-none');
        }
    });
    jQuery(element, container).selectpicker({
        dropupAuto: false
    }).change(function (e) {
        jQuery('#list-name-container input', container).val('').attr('disabled', 'disabled');
        if (jQuery('.dropdown-list-values', container).is(':visible')) {
            jQuery('.dropdown-list-values', container)[0].selectize.destroy();
            jQuery('.dropdown-list-values', container).val('').addClass('d-none');
            jQuery('#list-options-container input', container).attr('disabled', 'disabled');
        }
        jQuery('#is-public', container).addClass('d-none');
        jQuery('#public-list', container).attr('disabled', 'disabled');
        switch (dataTypes[this.value]) {
            case 'list':
                jQuery('#is-public', container).removeClass('d-none');
                jQuery('#public-list', container).removeAttr('disabled');
                jQuery('#list-name-container input', container).removeAttr('disabled');
                jQuery('#list-name-container', container).addClass('d-none');
                jQuery('#visible-to-all-checkbox', container).prop('checked', false);
                displayDropdownOptions(container);
                break;
            case 'public_list':
                var publicList = this.value;
                jQuery('.data-field-type', '#event-type-form-container').each(function (i) {
                    if (this.value === publicList) {
                        var fieldId = jQuery(this).attr('field-id');
                        if (fieldId !== container.attr('id')) {
                            jQuery('#list-options-container', '#' + fieldId).addClass('d-none');
                            jQuery('.translate-link', '#' + fieldId).addClass('d-none');
                            jQuery('#update-public-list', '#' + fieldId).val(false);

                        } else {
                            prependHiddenPublicList(fieldId, true);
                        }
                    }
                });
                jQuery.each(publicListsNames[publicList], function (index, val) {
                    jQuery('input[data-id=type_name_'+index+']', jQuery('#list-name-container',container)).removeAttr('disabled').val(val);
                });

                jQuery('#public-list', container).removeAttr('disabled').val(true);
                jQuery('.dropdown-list-values', container).val(publicListsOptions[this.value][1]);
                displayDropdownOptions(container);
                break;
            default:
                jQuery('.inline-text', container).addClass('d-none');
                break;
        }
    });
}
function enableDisableSubEvent(that, container) {
    jQuery('#is-sub-event', container).val(jQuery(that).is(':checked') ? 1 : 0);
}
// translate all field details of a row field(container: row of the field )
function translateFields(container) {
    var data = {action: 'translation_form'};
    if (jQuery('.dropdown-list-values', container).is(':visible')) {
        data['options'] = jQuery('.dropdown-list-values', container).val();
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'legal_case_event_types/actions',
        type: 'GET',
        data: data,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html && jQuery('#fields-translation-container').length <= 0) {
                jQuery('<div id="fields-translation-container"></div>').appendTo("body");
                var fieldsTranslationContainer = jQuery('#fields-translation-container');
                fieldsTranslationContainer.html(response.html);
                if (jQuery('.translation-for', container).val() !== '') {
                    jQuery('.modal-title', fieldsTranslationContainer).html(_lang.translationFor.sprintf([jQuery('.translation-for', container).val()]));
                }
                jQuery('.modal', fieldsTranslationContainer).modal({
                    keyboard: false,
                    backdrop: 'static',
                    show: true
                });
                initializeModalSize(fieldsTranslationContainer);
                setValues("name-", container, fieldsTranslationContainer);
                var options = {};
                var id;
                jQuery('input[id^=name-]', jQuery('#list-options-container', container)).each(function (i) {
                    id = jQuery(this).attr('id');
                    options[id] = jQuery(this).val().split(',');
                    jQuery('tr[id^=option-]', jQuery('#list-options-container', fieldsTranslationContainer)).each(function () {
                        jQuery('#' + id, this).val(options[id][(jQuery(this).attr('id')).substring('option-'.length)]);
                    });
                });
                jQuery("#translate-fields", fieldsTranslationContainer).click(function () {
                    setValues("name-", fieldsTranslationContainer, container);
                    var translatedOptions = {};
                    translatedListOptions[jQuery(container).attr('id')] = {};
                    jQuery('tr[id^=option-]', jQuery('#list-options-container', fieldsTranslationContainer)).each(function () {
                        var optionId = '#' + jQuery(this).attr('id');
                        jQuery('input[id^=name-]', jQuery(this)).each(function () {
                            if (typeof translatedListOptions[jQuery(container).attr('id')][jQuery('#name-' + _lang.languageSettings['langName'], optionId).html()] === 'undefined') {
                                translatedListOptions[jQuery(container).attr('id')][jQuery('#name-' + _lang.languageSettings['langName'], optionId).html()] = {};
                            }
                            translatedListOptions[jQuery(container).attr('id')][jQuery('#name-' + _lang.languageSettings['langName'], optionId).html()][jQuery(this).attr('id')] = jQuery(this).val();
                            if (!jQuery.isArray(translatedOptions[jQuery(this).attr('id')])) {
                                translatedOptions[jQuery(this).attr('id')] = [];
                            }
                            translatedOptions[jQuery(this).attr('id')].push(jQuery(this).val());
                        });
                    });
                    jQuery('input[id^=name-]', jQuery('#list-options-container', container)).each(function () {
                        jQuery(this).val(translatedOptions[jQuery(this).attr('id')]);
                    });
                    jQuery('.modal', fieldsTranslationContainer).modal('hide');
                });
                jQuery('.modal', fieldsTranslationContainer).on('hidden.bs.modal', function () {
                    destroyModal(fieldsTranslationContainer);
                });
                jQuery(document).keyup(function (e) {
                    if (e.keyCode == 27) {
                        jQuery('.modal', fieldsTranslationContainer).modal('hide');
                    }
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function addField() {
    jQuery("#default-field-behavior").find("input,button,textarea,select").removeAttr("disabled");
    jQuery('tr#default-field-behavior', '#event-type-form-container').clone().appendTo('.field-details').attr('id', 'field-' + count).removeClass('d-none');
    var container = jQuery('#field-' + count, '#event-type-form-container');
    jQuery('td:first-child input', container).addClass('field-name');
    jQuery('.select-picker', container).selectpicker({dropdownAlignRight: 'auto'});
    jQuery('.field-types', container).attr('id', 'field-' + count + '-types');
    jQuery('.data-field-type', container).attr('field-id', 'field-' + count);
    jQuery('.field-name-translate-link', container).on('click', function () {
        translateFields(container);
    });
    jQuery('.list-name-translate-link', container).on('click', function () {
        translateFields(container);
    });
    eventTypeEvents(jQuery('#field-' + count + '-types'), container);
    count++;
}
function removeRow(element, id, eventTypeId) {
    var container = jQuery(element).parent().parent();
    id = id || false;
    eventTypeId = eventTypeId || false;
    var idOptionsListVisible = jQuery('#list-options-container', container).is(':visible');
    var removed = true;
    if (id && eventTypeId) {
        jQuery.ajax({
            dataType: 'JSON',
            url: getBaseURL() + 'legal_case_event_types/actions',
            type: 'GET',
            data: {action: 'check_field_used', event_type_id: eventTypeId, field_id: id},
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.result) {
                    if (confirm(_lang.confirmationOfDeletingEventTypeField)) {
                        container.remove();
                    } else {
                        removed = false;
                    }
                } else {
                    container.remove();
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        container.remove();
    }
    if (removed) {
        var fieldType = jQuery('.data-field-type', container).val();
        if (dataTypes[fieldType] === 'public_list' && typeof eventTypes[fieldType] !== 'undefined') {
            if (idOptionsListVisible && eventTypes[fieldType].length > 1) {
                var elementIndex = eventTypes[fieldType].indexOf(container.attr('id'));
                var fieldId = typeof eventTypes[fieldType][elementIndex - 1] !== 'undefined' ? eventTypes[fieldType][elementIndex - 1] : eventTypes[fieldType][elementIndex + 1];
                jQuery('#list-options-container', '#' + fieldId).removeClass('d-none');
                jQuery('.translate-link', '#' + fieldId).removeClass('d-none');
                prependHiddenPublicList(fieldId, true);
            }
        }
    }
}
function deleteEventType(id) {
    id = id || false;
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            dataType: 'JSON',
            url: getBaseURL() + 'legal_case_event_types/delete/' + id,
            type: 'GET',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.result) {
                    updateRecords('event-types', id);
                    pinesMessage({ty: 'success', m: _lang.deleteRecordSuccessfull});
                } else {
                    pinesMessage({ty: 'warning', m: _lang.feedback_messages.deleteEventTypeFailed});
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function updateRecords(prefix, deletedId) {
    prefix = '#' + prefix + '-';
    var container = jQuery(prefix + 'container');
    jQuery(prefix + deletedId, container).remove();
    if (jQuery(prefix + 'table-body', container).children().length == 0) {
        jQuery(prefix + 'table', container).addClass('d-none');
    }
    var totalRecords = jQuery('#rows-number', container).val() - 1;
    jQuery('#rows-number', container).val(totalRecords);
    jQuery('#total-records', container).html(totalRecords);
}
function setValues(idPrefix, idContainer, elementsToSetContainer) {
    var containers = ['#event-type-name-container', '#field-name-container', '#field-description-container', '#list-name-container'];
    jQuery.each(containers, function (index, val) {
        jQuery('input[id^=' + idPrefix + ']', jQuery(val, idContainer)).each(function () {
            var elementId = jQuery(this).attr('id');
            if (jQuery(val, idContainer).is(':visible')) {
                jQuery(val, elementsToSetContainer).removeClass('d-none');
                jQuery('#' + elementId, jQuery(val, elementsToSetContainer)).val(jQuery(this).val() ? jQuery(this).val() : jQuery('#' + idPrefix + _lang.languageSettings['langName'], jQuery(val, idContainer)).val());
            }
        });
    });
}
function displayDropdownOptions(container) {
    jQuery('#list-options-container input', container).removeAttr('disabled', 'disabled');
    jQuery('.dropdown-list-values', container).removeClass('d-none').selectize({
        plugins: ['remove_button'],
        placeholder: _lang.options,
        delimiter: ',',
        persist: false,
        create: function (input) {
            return {
                value: input,
                text: input
            }
        },
        onOptionRemove: function (value) {
            updateTranslatedListOptions(value, container);
        },
    });
    jQuery('.inline-text', container).removeClass('d-none');
}
function updateTranslatedListOptions(value, container) {
    delete translatedListOptions[container.attr('id')][value];
    var translatedOptions = {};
    jQuery.each(translatedListOptions[container.attr('id')], function (index, value) {
        jQuery.each(value, function (i, val) {
            if (!jQuery.isArray(translatedOptions[i])) {
                translatedOptions[i] = [];
            }
            translatedOptions[i].push(val);
        });
    });
    jQuery('input[id^=name-]', jQuery('#list-options-container', container)).each(function () {
        jQuery(this).val(translatedOptions[jQuery(this).attr('id')]);
    });
}
function prependHiddenPublicList(id, value) {
    jQuery('#' + id, '#event-type-form-container').prepend(
            jQuery('<input>', {
                type: 'hidden',
                val: value,
                name: 'update_public_list[]',
                id: 'update-public-list'
            })
            );
}