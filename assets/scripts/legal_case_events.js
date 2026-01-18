var eventId, parentId;
var count = 2;
function eventForm(id, subEvent, callback, stageId, noStage) {
    subEvent = subEvent || false;
    callback = callback || false;
    stageId = stageId || false;
    noStage = noStage || false;
    eventId = (id && !subEvent) ? id : false;
    var data = {};
    if (subEvent) {
        data['id'] = id;
    }
    data['return_form'] = true;
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/' + (eventId && !subEvent ? 'edit_event/' + eventId : 'add_event'),
        type: 'GET',
        data: data,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html && jQuery('#case-event-form-container').length <= 0) {
                jQuery('<div id="case-event-form-container"></div>').appendTo("body");
                var caseEventContainer = jQuery('#case-event-form-container');
                caseEventContainer.html(response.html);
                if (typeof response.stage_html !== 'undefined' && response.stage_html) {
                    litigationStageDataEvents(response.stage_html, caseEventContainer);
                } else if (!id) {
                    if (!noStage) matterStageMetadata(caseEventContainer, jQuery('#legal-case', '#case-events-container').val(), stageId ? stageId : false);
                } else {
                    jQuery('#stage-div', caseEventContainer).html('');
                }
                if(typeof latestDevelpementHidden !== 'undefined'){
                    jQuery("#latest_development", caseEventContainer).val(latestDevelpementHidden);
                }
                callback ? eventFormEvents(caseEventContainer, callback) : eventFormEvents(caseEventContainer);
                setEventTypeFields(response, caseEventContainer);
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function eventFormSubmit(container, callback) {
    callback = callback || false;
    jQuery('div[id^="event-reminder-"]', container).each(function () {
        jQuery('#reminder-summary', this).val(jQuery("input[field-key='subject']", container).val());
    });
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');

        },
        data: jQuery("form", container).serializeArray(),
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/' + (eventId ? 'edit_event/' + eventId : 'add_event'),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if(jQuery("#latest_development", container).length && typeof latestDevelpementHidden !== 'undefined'){
                    latestDevelpementHidden = jQuery("#latest_development", container).val();
                }
                if (!response.cloned) {
                    jQuery('.modal', container).modal('hide');
                    loadUserLatestReminders('refresh');
                    pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                } else {
                    jQuery('#clone', container).val('no');
                    var eventDate = jQuery("input[field-key='date']:first", container).val();
                    jQuery('input', jQuery('#event-type-fields-defined', container)).val('');
                    jQuery("input[field-key='date']:first", jQuery('#event-type-fields-defined', container)).val(eventDate)
                    jQuery("select[field-type='lookup']", container).each(function () {
                        jQuery(this)[0].selectize.clear();
                    });
                }
                if (response.html) {
                    jQuery('#events-details', '#case-events-container').html(response.html);
                    jQuery('.export-to-excel-link', '#case-events-container').removeClass('d-none');
                    if (eventId) {
                        toggleUsedElements(eventId);
                    }
                    if (parentId) {
                        toggleUsedElements(parentId);
                    }
                }
                if (callback) callback(jQuery("#stage-id").val());
                openSlideDown(true);
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.btn-save', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteEventWithCallback(parms) {
    deleteEvent(parms.id, parms.callback);
}

function deleteEvent(id, callback) {
    callback = callback || false;
    jQuery.ajax({
        url: getBaseURL() + 'cases/delete_event',
        dataType: "json",
        type: "POST",
        data: {
            id: id
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ ty: 'information', m: _lang.deleteRecordSuccessfull });
                if (callback) callback();
            } else if (!response.status) {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.deleteCaseEventFailed });
            } else {
                pinesMessage({ ty: 'error', m: _lang.deleteRecordFailed });
            }

        }, error: defaultAjaxJSONErrorsHandler
    });
}
function eventFormEvents(container, callback) {
    callback = callback || false;
    if (jQuery('#event-type', container).val()) {
        loadEventTypeFields(jQuery('#event-type', container).val(), container);
    }
    jQuery('#event-type', container).selectpicker().change(function () {
        if (this.value == 0) {
            jQuery('#event-type-fields-defined', container).html('');
        } else {
            loadEventTypeFields(this.value, container);
        }
    });
    jQuery('#legal-case', container).val(jQuery('#legal-case', '#case-events-container').val());
    lookUpUsers(jQuery('#created-by', container), jQuery('#created-by-id', container), 'createdBy', jQuery('.created-by-container', container), container);
    jQuery("#reminders-container *", container).attr("disabled", "disabled");
    callback ? commonModalDialogEvents(container, eventFormSubmit, callback) : commonModalDialogEvents(container, eventFormSubmit);
    initializeModalSize(container);

    if (jQuery('#case-assignee-id', '#case-events-container').val()) {
        jQuery("#case-event-reminders", container).removeClass('d-none');
        jQuery("#event-reminder-1", container).removeClass('d-none');
        jQuery('#users-to-remind-id', jQuery("#event-reminder-1", container)).val(jQuery('#case-assignee-id', '#case-events-container').val());
        jQuery('#users-to-remind', jQuery("#event-reminder-1", container)).val(jQuery('#case-assignee-name', '#case-events-container').val());
        setDatePicker('#remind-on-date', jQuery("#event-reminder-1", container));
        jQuery('#remind-on-time', jQuery("#event-reminder-1", container)).timepicker({
            'timeFormat': 'H:i',
        });
        lookUpUsers(jQuery('#users-to-remind', jQuery("#event-reminder-1", container)), jQuery('#users-to-remind-id', jQuery("#event-reminder-1", container)), 'user_id', jQuery("#event-reminder-1", container), container);

    } else {
        jQuery('#event-reminder-1 input', container).attr('disabled', 'disabled');
    }
    jQuery('form', container).data('serialize', jQuery('form', container).serialize());
}
function reminderEventForm(container) {
    var clonedContainer;
    if (!jQuery('#case-event-reminders', container).is(':visible')) {
        jQuery('#case-event-reminders', container).removeClass('d-none');
    }
    clonedContainer = jQuery('#event-reminder-1', container).clone().attr('id', 'event-reminder-' + count).removeClass('d-none');
    jQuery('#remind-on-date-input',clonedContainer).attr('id','remind-on-date-input'+ count);
    
    jQuery('#HijriConverterContainer',clonedContainer).attr('id','HijriConverterContainer'+ count);
    jQuery('input', clonedContainer).val('').removeAttr('disabled');
    clonedContainer.insertAfter('div[id^="event-reminder-"]:last');


    $( "#HijriConverterContainer"+ count ).on( "click", function() {
        var prevCount = parseInt(count)-1;
        HijriConverter(jQuery('#remind-on-date-input'+prevCount, jQuery('#event-reminder-' + prevCount)), true, true); 
    });

    jQuery('#remind-on-date-input', jQuery("#event-reminder-" + count, container)).val(jQuery("input[field-key='date']:first", container).val());
    jQuery('#remind-on-time', jQuery("#event-reminder-" + count, container)).val(jQuery("input[field-key='date']:last", container).val());

    if (jQuery('.hijri-date-picker',jQuery("#event-reminder-" + count, container)).length > 0)
        makeFieldsHijriDatePicker({ fields: ['remind-on-date-input'+ count] },jQuery("#event-reminder-" + count, container));
    else
    setDatePicker('#remind-on-date', jQuery("#event-reminder-" + count, container));//not needed?

    jQuery('#remind-on-time', jQuery("#event-reminder-" + count, container)).timepicker({
        'timeFormat': 'H:i',
    });

    lookUpUsers(jQuery('#users-to-remind', jQuery("#event-reminder-" + count, container)), jQuery('#users-to-remind-id', jQuery("#event-reminder-" + count, container)), 'user_id', jQuery("#event-reminder-" + count, container), container);
    count++;
}

function removeReminderEvent(that) {
    if (jQuery(that).attr('id') === 'event-reminder-1') {
        that.addClass('d-none');
        jQuery('input', that).attr('disabled', 'disabled');
    } else {
        that.remove();
    }
    if (jQuery('div[id^=event-reminder-]', jQuery('#case-event-reminders', '#case-event-form-container')).length === 1 && jQuery('#event-reminder-1', '#case-event-form-container').hasClass('d-none')) {
        jQuery('#case-event-reminders', '#case-event-form-container').addClass('d-none');
    }
}
function toggleElements(elementsToggleIcon, elementContainer) {
    if (elementContainer.is(':visible')) {
        elementContainer.slideUp();
        elementsToggleIcon.removeClass('fa-solid fa-chevron-down');
        elementsToggleIcon.addClass('fa-solid fa-chevron-right');
        elementContainer.addClass('d-none');
        updateURL(elementContainer.attr("id"), true);
    } else {
        elementContainer.slideDown();
        elementsToggleIcon.removeClass('fa-solid fa-chevron-right');
        elementsToggleIcon.addClass('fa-solid fa-chevron-down');
        elementContainer.removeClass('d-none');
        updateURL(elementContainer.attr("id"));
    }
}
function toggleCheckbox(hiddenInput, checkBoxInput) {
    hiddenInput.val(checkBoxInput.is(':checked') ? 'yes' : 'no');
}
jQuery(document).ready(function () {
    if (jQuery('.no-data', '#case-events-container').is(':visible')) {
        jQuery('.export-to-excel-link', '#case-events-container').addClass('d-none');
    }
    jQuery('#legal-case-stage-id', '#case-events-container').selectpicker();
});
function loadEventTypeFields(eventType, container) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/' + (eventId ? 'edit_event/' + eventId : 'add_event'),
        type: 'GET',
        data: { event_type: eventType },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            setEventTypeFields(response, container);
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
    });
}
function setEventTypeFields(response, container) {
    if (response.fields_html) {
        jQuery('#event-type-fields-defined', container).html(response.fields_html);
        fixDateTimeFieldDesign('#case-event-form-container');
        jQuery("#event-type-fields-defined [id^='form-field-']", container).each(function () {
            var fieldTypeData = jQuery(this).attr('field-type-data');
            switch (jQuery(this).attr('field-type')) {
                case 'date':
                    setDatePicker(jQuery(this).parent(), container);
                    break;
                case 'date_time':
                    if (jQuery(this).parent().hasClass('date-picker')) {
                        setDatePicker(jQuery(this).parent(), container);
                    }
                    if (jQuery(this).hasClass('time-picker')) {
                        jQuery(this).timepicker({
                            'timeFormat': 'H:i',
                        });
                    }
                    break;
                case 'list':
                    jQuery(this).selectpicker();
                    break;
                case 'public_list':
                    jQuery(this).selectpicker();
                    break;
                case 'lookup':
                    var displaySegments = jQuery(this).attr('display-segments').split(',');
                    var displayFormatSingleSegment = jQuery(this).attr('display-format-single-segment');
                    var displayFormatDoubleSegment = jQuery(this).attr('display-format-double-segment');
                    var displayFormatTripleSegment = jQuery(this).attr('display-format-triple-segment');
                    jQuery('#' + jQuery(this).attr('id')).selectize({
                        plugins: ['remove_button'],
                        placeholder: _lang.startTyping,
                        valueField: 'id',
                        labelField: displaySegments[0],
                        searchField: [displaySegments[0], displaySegments[1]],
                        create: false,
                        render: {
                            option: function (item, escape) {
                                var displayOption = '';
                                if (typeof displayFormatTripleSegment !== 'undefined') {
                                    if (item[displaySegments[2]] !== null && typeof item[displaySegments[2]] !== 'undefined') {
                                        displayOption = displayFormatTripleSegment.sprintf([escape(item[displaySegments[0]]), escape(item[displaySegments[1]]), escape(item[displaySegments[2]])]);
                                    } else if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined') {
                                        displayOption = escape(item[displaySegments[0]]) + ' ' + escape(item[displaySegments[1]]);
                                    } else {
                                        displayOption = escape(item[displaySegments[0]]);
                                    }
                                } else
                                    if (typeof displayFormatDoubleSegment !== 'undefined') {
                                        if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined') {
                                            displayOption = displayFormatDoubleSegment.sprintf([escape(item[displaySegments[0]]), escape(item[displaySegments[1]])]);
                                        } else {
                                            displayOption = escape(item[displaySegments[0]]);
                                        }
                                    } else {
                                        displayOption = displayFormatSingleSegment.sprintf([escape(item[displaySegments[0]])]);
                                    }
                                return '<div><span>' + displayOption + '</span></div>';
                            }, item: function (item, escape) {
                                var displayOption = '';
                                if (typeof displayFormatTripleSegment !== 'undefined') {
                                    if (item[displaySegments[2]] !== null && typeof item[displaySegments[2]] !== 'undefined') {
                                        displayOption = displayFormatTripleSegment.sprintf([escape(item[displaySegments[0]]), escape(item[displaySegments[1]]), escape(item[displaySegments[2]])]);
                                    } else if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined') {
                                        displayOption = escape(item[displaySegments[0]]) + ' ' + escape(item[displaySegments[1]]);
                                    } else {
                                        displayOption = escape(item[displaySegments[0]]);
                                    }
                                } else
                                    if (typeof displayFormatDoubleSegment !== 'undefined') {
                                        if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined' && fieldTypeData !== 'companies') {//to not show the short name of the company shortName
                                            displayOption = displayFormatDoubleSegment.sprintf([escape(item[displaySegments[0]]), escape(item[displaySegments[1]])]);
                                        } else {
                                            displayOption = escape(item[displaySegments[0]]);
                                        }
                                    } else {
                                        displayOption = displayFormatSingleSegment.sprintf([escape(item[displaySegments[0]])]);
                                    }
                                return '<div>' + displayOption + '</div>';
                            }
                        },
                        load: function (query, callback) {
                            if (query.length < 3)
                                return callback();
                            jQuery.ajax({
                                url: getBaseURL() + jQuery('#' + this.$input[0].id).attr('field-type-url'),
                                type: 'GET',
                                data: {
                                    term: encodeURIComponent(query)
                                },
                                dataType: 'json',
                                error: function () {
                                    callback();
                                },
                                success: function (res) {
                                    callback(res);
                                }
                            });
                        }
                    });
                    break;
                default:
                    break;
            }
        });
    }
    jQuery('form', container).data('serialize', jQuery('form', container).serialize());
}
function calendarEventForm(container) {
    if (jQuery('#calendar-container', container).is(':visible')) {
        jQuery('#calendar-container', container).addClass('d-none');
        jQuery('input#calendar-container', container).attr('disabled', 'disabled');
    } else {
        jQuery('#calendar-container').removeClass('d-none');
        jQuery('input#calendar-container', container).removeAttr('disabled');
        jQuery('#calendar-subject', container).val(jQuery("input[field-key='subject']", container).val());
        jQuery('#start-date-input', container).val(jQuery("input[field-key='date']:first", container).val());
        jQuery('#end-date-input', container).val(jQuery("input[field-key='date']:first", container).val());
        jQuery('#start-time', container).val(jQuery("input[field-key='date']:last", container).val());
        setDatePicker('#start-date', container);
        setDatePicker('#end-date', container);
        jQuery('#start-time', container).timepicker({
            'timeFormat': 'H:i',
        });
        jQuery('#end-time', container).timepicker({
            'timeFormat': 'H:i',
            'showDuration': true,
        });
        if (!jQuery('.twitter-typeahead', jQuery('#calendar-container', container)).length) {

            lookupPrivateUsers(jQuery('#attendees-lookup', container), 'calendar[attendees]', '#selected-attendees', 'attendees-container', container);
        }
        jQuery('#date-pair', container).datepair();
        // on change value subject => update value calendar subject
        jQuery("input[field-key='subject']", container).change(function () {
            jQuery("#calendar-subject", container).val(jQuery(this, container).val());
        });
    }
}
function reminderCallBack() {
    return true;
}
function taskCallBack() {
    return true;
}
function relatedReminder(relatedEvent, id, callback) {
    id = id || false;
    callback = callback || false;
    if (id) {
        eventId = relatedEvent.event_id;
        callback ? reminderForm(id, false, false, false, callback) : reminderForm(id);
    } else {
        caseId = relatedEvent.case_id;
        callback ? reminderForm(false, false, caseId, false, callback) : reminderForm(false, false, caseId);
        updateReminderFromContainer(relatedEvent);
    }
}

/**
 * Set reminder form input data
 * @param relatedEvent
 */
function updateReminderFromContainer(relatedEvent) {
        
       if (jQuery("#reminder-form-container").is(":visible")) {
        
        jQuery('#remind-on-date-input', '#reminder-form-container').val(relatedEvent.start_date);

        if (jQuery('.hijri-date-picker', '#remind-on-date').length > 0)
        {
            makeFieldsHijriDatePicker({ fields: ['remind-on-date-input'] }); 
            var HijriDate = gregorianToHijri(jQuery('#remind-on-date-input', '#reminder-form-container').val()); 
            jQuery('#remind-on-date-input', '#reminder-form-container').val(HijriDate);
        }
        else
        setDatePicker('#remind-on-date');
        
        jQuery('#remind-on-time', '#reminder-form-container').val(relatedEvent.start_time);
        jQuery('#reminder-summary', '#reminder-form-container').val(relatedEvent.summary);
        jQuery('#reminder-form', '#reminder-form-container').prepend(
            jQuery('<input>', {
                type: 'hidden',
                val: relatedEvent.id,
                name: 'legal_case_event'
            })
        );
        jQuery('form', '#reminder-form-container').data('serialize', jQuery('form', '#reminder-form-container').serialize());
    } else {
        setTimeout(updateReminderFromContainer, 250, relatedEvent);
    }
}
function relatedTask(relatedEvent, id, callback) {
    id = id || false;
    callback = callback || false;
    if (id) {
        eventId = relatedEvent.event_id;
        callback ? taskEditForm(id, callback) : taskEditForm(id);
    } else {
        callback ? taskAddForm(relatedEvent.case_id, relatedEvent.stage_id, callback) : taskAddForm(relatedEvent.case_id, relatedEvent.stage_id);
        addRelatedEventIdToTaskForm(relatedEvent);
    }
}
/**
 * Add event related id to task add form when task is related to a event
 * @param relatedEvent object relate event
 */
function addRelatedEventIdToTaskForm(relatedEvent) {
    if (jQuery("#task-dialog").is(":visible")) {
        let taskDialog = jQuery('#task-dialog');
        jQuery('#task-form', '#task-dialog').prepend(
            jQuery('<input>', {
                type: 'hidden',
                val: relatedEvent.id,
                name: 'legal_case_event'
            })
        );
        jQuery('#due-date', taskDialog).bootstrapDP('update', relatedEvent.start_date);
        jQuery('#assignedToId', taskDialog).val(jQuery('#case-assignee-id', '#case-events-container').val());
        jQuery('#assignedToLookUp', taskDialog).val(jQuery('#case-assignee-name', '#case-events-container').val());
        jQuery('form', taskDialog).data('serialize', jQuery('form', taskDialog).serialize());
    } else {
        setTimeout(addRelatedEventIdToTaskForm, 250, relatedEvent);
    }
}

function subEventForm(id, parent, callback) {
    parentId = parent || false;
    callback = callback || false;
    eventForm(id, false, callback);
}
function toggleUsedElements(id) {
    toggleElements(jQuery('#elements-toggle-icon', '#collapse-icon-' + id), jQuery('#element-' + id, '#case-events-container'));
    toggleElements(jQuery('#elements-toggle-icon', '#collapse-sub-events-icon-' + id), jQuery('#sub-events-' + id, '#case-events-container'));
    jQuery('#case-events-container').scrollTo(jQuery('#element-' + id, '#case-events-container'));
}
function deleteCaseHearingWithCallback(parms) {
    jQuery.ajax({
        url: getBaseURL() + 'cases/delete_case_hearing/' + parms.id,
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response.result) {
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.hearingDeletedSuccessfully });
                if (parms.callback) parms.callback();
            }
            else {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.deleteHearingFailed });
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteCaseHearing(id, callback) {
    callback = callback || false;
    if (confirm(_lang.confirmationOfDeletingHearing)) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/delete_case_hearing/' + id,
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                if (response.result) {
                    pinesMessage({ ty: 'success', m: _lang.feedback_messages.hearingDeletedSuccessfully });
                    if (callback) callback();
                }
                else {
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.deleteHearingFailed });
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });

    }
}
/**
 * upldate page uri
 */
function updateURL(stage, remove) {
    if (stage && (stage.substring(0, 14) != 'stage-hearing-' && stage.substring(0, 13) != 'stage-events-')) {
        if (stage.substring(0, 6) == 'stage-') {
            if (history.pushState) {
                if (remove) {
                    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.history.pushState({}, '', newurl);
                } else {
                    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?stage=' + stage;
                    window.history.pushState({ path: newurl }, '', newurl);
                }
            }
        }
    }
}
/**
 * openSlideDown function
 */
function openSlideDown(hideEmtpyStagePine) {
    hideEmtpyStagePine = hideEmtpyStagePine || false;
    var elementContainer = jQuery("#" + getUrlVars()["stage"]);
    if (elementContainer.length > 0) {
        var elementsToggleIcon = jQuery("#" + getUrlVars()['stage'] + '-collapse-icon i');
        elementContainer.slideDown();
        elementsToggleIcon.removeClass('fa-solid fa-chevron-right');
        elementsToggleIcon.addClass('fa-solid fa-chevron-down');
        elementContainer.removeClass('d-none');
    }
}
/**
 * get uri vars
 */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}
function expandAllActivities() {
    jQuery('.stage-box', jQuery('#case-events-container')).removeClass('d-none').slideDown();
    jQuery('a.collapsing-arrow > i', jQuery('#case-events-container')).removeClass('fa-solid fa-chevron-right').addClass('fa-solid fa-chevron-down');
}
function collapseAllActivities() {
    jQuery('.stage-box', jQuery('#case-events-container')).addClass('d-none').slideUp();
    jQuery('a.collapsing-arrow > i', jQuery('#case-events-container')).removeClass('fa-solid fa-chevron-down').addClass('fa-solid fa-chevron-right');
}

var legalCaseEvents = (function () {
    'use strict';

    function openRemindersTab(stageId, caseId, forceOpen, pageNumber, pageLimit) {
        forceOpen = forceOpen || false;
        pageNumber = pageNumber || false;
        pageLimit = pageLimit || false;
        getTabContent(stageId, caseId, 'reminders', forceOpen, pageNumber, pageLimit);
    }

    function openHearingTab(stageId, caseId, forceOpen, pageNumber, pageLimit) {
        forceOpen = forceOpen || false;
        pageNumber = pageNumber || false;
        pageLimit = pageLimit || false;
        getTabContent(stageId, caseId, 'hearings', forceOpen, pageNumber, pageLimit);
    }

    function openTaskTab(stageId, caseId, forceOpen, pageNumber, pageLimit) {
        forceOpen = forceOpen || false;
        pageNumber = pageNumber || false;
        pageLimit = pageLimit || false;
        getTabContent(stageId, caseId, 'tasks', forceOpen, pageNumber, pageLimit);
        if (forceOpen) getCountTaskEvents(stageId, caseId, 'tasks');
    }

    function getCountTaskEvents(stageId, caseId, tab) {
        var elementContainer = jQuery("#" + getUrlVars()["stage"]);
        if (typeof getUrlVars()["stage"] === 'undefined' && elementContainer.length <= 0) {
            elementContainer = jQuery(".stage-container").first();
        }
        _countStageTasksAndEvents(tab, caseId, stageId, elementContainer);
    }

    function updateHearingCount(stageId, resultCount) {
        var _target_counter = "#count-hearings-stage-";
        jQuery(_target_counter + stageId).text(resultCount);
    }

    function getTabContent(stageId, caseId, tab, forceOpen, pageNumber, pageLimit) {
        forceOpen = forceOpen || false;
        pageNumber = pageNumber || false;
        pageLimit = pageLimit || false;
        let _stageId = stageId === 'null' ? '' : stageId;
        let containerPage = jQuery("#stages-page-container");
        let _params = pageNumber ? (pageLimit ? { stageId: _stageId, id: caseId, pageNumber: pageNumber, pageLimit: pageLimit } : { stageId: _stageId, id: caseId, pageNumber: pageNumber }) : (pageLimit ? { stageId: _stageId, id: caseId, pageLimit: pageLimit } : { stageId: _stageId, id: caseId });
        let tabButton = jQuery("#" + tab + "-button-" + stageId);
        let tabStage = jQuery("#" + tab + "-" + stageId);
        if (tabStage.text().length > 0 && !forceOpen) {
            tabStage.fadeOut(500, function () {
                jQuery(this).empty();
            });
            tabButton.removeClass('green-clicked-on');
        }else {
            jQuery.ajax({
                url: getBaseURL() + "cases/events",
                data: { 'pageTo': tab, 'params': _params },
                dataType: 'JSON',
                type: 'POST',
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                success: function (response) {
                    if (response.status) {
                        tabStage.html(response.html).show();
                        tabButton.removeClass('green-clicked-on').addClass('green-clicked-on');
                        let tabStageInside = null;
                        if (stageId != null) {
                            if (jQuery("#" + tab + "-" + _stageId + "-inside").offset()) {
                                tabStageInside = jQuery("#" + tab + "-" + _stageId + "-inside").offset().top + jQuery('#main-content-side .main-content-section').scrollTop() - 200;
                            }
                        } else {
                            tabStageInside = jQuery("#hearings--inside").offset().top + jQuery('#main-content-side .main-content-section').scrollTop() - 200;
                        }
                        jQuery('#main-content-side .main-content-section').animate({
                            scrollTop: tabStageInside
                        }, 1000, 'swing');
                    }
                    if (forceOpen) updateHearingCount(stageId, response.totalRows);
                    updateUpcomingAndLastAttendingHearing(stageId, response.html_upcoming_last_attending);
                    jQuery('.tooltip-title', containerPage).tooltipster({
                        contentAsHTML: true,
                        timer: 22800,
                        animation: 'grow',
                        delay: 200,
                        theme: 'tooltipster-default',
                        touchDevices: false,
                        trigger: 'hover',
                        maxWidth: 350,
                        interactive: true,
                        multiple: true
                    });
                    jQuery(".bg-eee-click").click(function () {
                        jQuery('.list-card').removeClass('bg-eee').find(".fa-ellipsis-v").removeClass("bg-eee");
                        jQuery(this).closest('div[class^="list-card"]').addClass("bg-eee");
                        jQuery(this).closest('div[class^="list-card"]').find(".fa-ellipsis-v").addClass("bg-eee");
                    });
                    jQuery(".pagination-select").selectpicker();
                }, complete: function () {
                    jQuery('#loader-global').hide();
                    let actionAddBtn = jQuery('.action-add-btn');
                    if('undefined' !== typeof(disableMatter) && disableMatter){
                        disableAnchors(actionAddBtn);
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    }

    function updateUpcomingAndLastAttendingHearing(stageId, data) {
        let upcomingLastAttendingHearing = jQuery("#upcoming_last_attending_hearing-" + stageId);
        if (stageId && data && upcomingLastAttendingHearing.length) {
            upcomingLastAttendingHearing.html(data);
        }
    }

    /**
     * navigate to a specific page
     * @param page string
     * @param params json list of post params to the page
     * @returns {string}
     */
    function goToPage(page, params) {
        params = params || {};
        let containerPage = jQuery("#stages-page-container");
        containerPage.empty();
        getPageContent(page, containerPage, params);
    }

    /**
     *  Get html content of each page in stages and activities view
     * @param page
     * @param params json list of post params to the page
     * @param container
     */
    function getPageContent(page, container, params) {
        jQuery.ajax({
            url: getBaseURL() + "cases/events",
            data: { 'pageTo': page, 'params': params },
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    container.html(response.html);
                    jQuery('.tooltip-title', container).tooltipster({
                        contentAsHTML: true,
                        timer: 22800,
                        animation: 'grow',
                        delay: 200,
                        theme: 'tooltipster-default',
                        touchDevices: false,
                        trigger: 'hover',
                        maxWidth: 350,
                        interactive: true,
                        multiple: true
                    });
                }
                if (page === 'stages') {
                    openSlideDown(page);
                    if (response.stages_data) {
                        jQuery.each(response.stages_data, function (index, value) {
                            jQuery('#tooltip-activities-' + value.id, container).tooltipster({
                                content: jQuery('#tooltip-content-' + value.id).html(),
                                contentAsHTML: true,
                                timer: 22800,
                                animation: 'grow',
                                delay: 200,
                                theme: 'tooltipster-default',
                                touchDevices: false,
                                trigger: 'hover',
                                interactive: true,
                                multiple: true
                            });
                        });
                    }
                }
                if (page === 'events') openSlideDownEvents(params);
                if (page === 'hearings') {
                    jQuery('.summary-activities').tooltipster({
                        content: _lang.fillHearingSummary,
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
                }
                jQuery(".bg-eee-click").click(function () {
                    jQuery('.list-card').removeClass('bg-eee').find(".fa-ellipsis-v").removeClass("bg-eee");
                    jQuery(this).closest('div[class^="list-card"]').addClass("bg-eee");
                    jQuery(this).closest('div[class^="list-card"]').find(".fa-ellipsis-v").addClass("bg-eee");
                });
                jQuery(".pagination-select").selectpicker();
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function judgesDialog(params) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/events',
            data: { 'pageTo': 'judge', 'params': params },
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    var judgeDialogContainer = "judge-dialog-container";
                    jQuery('<div id="judge-dialog-container"></div>').appendTo("body");
                    var container = jQuery("#" + judgeDialogContainer);
                    container.html(response.html);
                    initializeModalSize(container, 0.35, 'auto');
                    commonModalDialogEvents(container);
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function opponentLawyerDialog(params) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/events',
            data: { 'pageTo': 'opponent_lawyer', 'params': params },
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    var opponentLawyerContainer = "opponent-lawyer-container";
                    jQuery('<div id="opponent-lawyer-container"></div>').appendTo("body");
                    var container = jQuery("#" + opponentLawyerContainer);
                    container.html(response.html);
                    initializeModalSize(container, 0.35, 'auto');
                    commonModalDialogEvents(container);
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    /**
     * TO get all related document of tasks or hearing
     * @param type String document type "tsk or hearing"
     * @param id int record id "task id or hearing id"
     * @param onlyTable bool get only table on all dialog content
     */
    function documentsDialog(type, id, onlyTable = false) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/events',
            data: { 'pageTo': 'document_dialog', 'document_type': type, 'related_document_id': id, onlyTable: onlyTable },
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    var opponentLawyerContainer = "document-dialog-container";
                    jQuery('<div id="document-dialog-container"></div>').appendTo("body");
                    var container = jQuery("#" + opponentLawyerContainer);
                    container.html(response.html);
                    initializeModalSize(container, 0.35, 'auto');
                    commonModalDialogEvents(container);
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
    function commentDocumentsDialog(type, id, onlyTable = false) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/events',
            data: {
                'pageTo': 'document_dialog',
                'document_type': type,
                'related_document_id': id,
                onlyTable: onlyTable,
            },
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    var opponentLawyerContainer = "document-dialog-container";
                    jQuery('<div id="document-dialog-container"></div>').appendTo("body");
                    var container = jQuery("#" + opponentLawyerContainer);
                    container.html(response.html);
                    initializeModalSize(container, 0.35, 'auto');
                    commonModalDialogEvents(container);
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function openSlideDown() {
        var elementContainer = jQuery("#" + getUrlVars()["stage"]);
        if (typeof getUrlVars()["stage"] === 'undefined' && elementContainer.length <= 0) {
            elementContainer = jQuery(".stage-container").first();
        }
        if (elementContainer.length > 0) {
            var elementsToggleIcon = typeof getUrlVars()["stage"] === 'undefined' ? jQuery("#" + elementContainer.attr('id') + '-icon') : jQuery("#" + getUrlVars()['stage'] + '-icon');
            elementContainer.slideDown();
            elementsToggleIcon.removeClass('fa-solid fa-chevron-right');
            elementsToggleIcon.addClass('fa-solid fa-chevron-down');
            elementContainer.removeClass('d-none');
        }
        var stageId = jQuery("input[data-field='stage_id']", elementContainer).val();
        countStageTasksAndEvents(caseId, stageId, jQuery(elementContainer, '#case-events-container'));
    }

    function openSlideDownEvents(params) {
        var elementContainer = '';
        var elementsToggleIcon = '';
        if (params.eventId) {
            elementContainer = jQuery("#event-" + params.eventId + "-container");
        } else {
            elementContainer = jQuery(".events-container").first();
        }
        elementsToggleIcon = jQuery("#" + elementContainer.attr('id') + '-icon');
        elementContainer.slideDown(); elementsToggleIcon.removeClass('fa-solid fa-chevron-right');
        elementsToggleIcon.addClass('fa-solid fa-chevron-down');
        elementContainer.removeClass('d-none');
    }

    function deleteHearingDocument(id, document_id, hearingId, versionDocument, callback) {
        versionDocument = versionDocument || false;
        callback = callback || false;
        let parm = callback && isFunction(callback) ? { 'id': id, 'document_id': document_id, callback: callback } : { 'id': id, 'document_id': document_id };
        confirmationDialog('confirm_delete_record',
            {
                resultHandler: function () {
                    _deleteHearingDocument(id, document_id, versionDocument, 'cases', hearingId);
                },
                parm: parm
            }
        );
    }
    function deleteCommentDocument(id, document_id, comment_id, callback) {
        callback = callback || false;
        let parm = callback && isFunction(callback) ? { 'id': id, 'document_id': document_id, callback: callback } : { 'id': id, 'document_id': document_id };
        confirmationDialog('confirm_delete_record',
            {
                resultHandler: function () {
                    _deleteCommentDocument(id, document_id, comment_id, 'cases');
                },
                parm: parm
            }
        );
    }
    function documentTaskDelete(id, taskId) {
        confirmationDialog('confirm_delete_record',
            {
                resultHandler: function () {
                    taskId = taskId || false;
                    jQuery.ajax({
                        url: getBaseURL() + 'tasks' + '/delete_document',
                        data: { document_id: id, module_record_id: taskId },
                        type: 'POST',
                        success: function (response) {
                            pinesMessage({ ty: response.status ? 'success' : 'error', m: response.message });
                            jQuery('#document-item-' + id).remove();
                            if (taskId) updateDocCount(taskId);
                        }, beforeSend: function () {
                            jQuery('#loader-global').show();
                        }, complete: function () {
                            jQuery('#loader-global').hide();
                        },
                        error: defaultAjaxJSONErrorsHandler
                    });
                }
            }
        );
    }

    function changePageLimit(element, stageId, caseId, page) {
        let elementjQ = jQuery(element);
        if (page === 'hearings') {
            legalCaseEvents.openHearingTab(stageId == null ? 'null' : stageId, caseId, true, false, elementjQ.val());
        } else if (page === 'tasks') {
            legalCaseEvents.openTaskTab(stageId == null ? 'null' : stageId, caseId, true, false, elementjQ.val());
        } else if (page === 'reminders') {
            legalCaseEvents.openRemindersTab(stageId == null ? 'null' : stageId, caseId, true, false, elementjQ.val());
        }
    }

    return {
        goToPage: goToPage,
        judgesDialog: judgesDialog,
        opponentLawyerDialog: opponentLawyerDialog,
        documentsDialog: documentsDialog,
        deleteHearingDocument: deleteHearingDocument,
        documentTaskDelete: documentTaskDelete,
        openTaskTab: openTaskTab,
        openHearingTab: openHearingTab,
        openRemindersTab: openRemindersTab,
        changePageLimit: changePageLimit,
        deleteCommentDocument: deleteCommentDocument
    };
}());

jQuery(document).ready(function () {
    legalCaseEvents.goToPage("stages", { id: caseId });
});
