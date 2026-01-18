/* Vars  */
var newCaseFormDialogId = 'newCaseFormDialog';
var refreshNotificationsIntervalId;
var refreshRemindersIntervalId;
var newCaseFormDialog = jQuery('#' + newCaseFormDialogId);
var caseFormElement = jQuery('form#legalCaseAddForm', newCaseFormDialog),
    gridActionIconHTML = '<a class="dropdown-toggle dropdown-toggle-new btn btn-default btn-sm dms-action-wheel" data-toggle="dropdown" href="##"><i class="fa-solid fa-gear purple_color"></i> <span class="caret no-margin"></span></a>',
    notificationsTemplate = '';
var $timeTrackingGrid = null, $tasksGrid = null, $legalCaseGrid = null, $moneyTimeTrackingGrid = null,
    $bulkTimeTrackingGrid = null;
var reminderPopoverAnchor = null, notificationPopoverAnchor = null, csrfName = '', csrfValue = '';
var disableBlurEventToCheckLookupValidity = false;
var AllowedFilesToDisplay = ["jpg", "png", "gif", "jpeg", "bmp", "html", "htm", "txt", "jfif"];
var loggedUserIsAdminForGrids = false, gridSavedFiltersParams = false, gridSavedPageSize = false,
    gridAdvancedSearchLinkState = false;
var authIdLoggedIn = '';
var getSystemRateValue = false;
var hearingFixedFilters = ["judged_hearings", "my_hearings", "verified_hearings", "non_verified_hearings", "all_todays_hearings", "my_todays_hearings", "all_hearings_for_tomorrow", "my_hearings_for_tomorrow", "all_hearings_for_this_week", "my_hearings_for_this_week", "all_hearings_for_this_month", "my_hearings_for_this_month"];
var gridActionIconHTMLV2 = '<a class="btn btn-default dropdown-items btn-sm dms-action-wheel" data-toggle="dropdown" href="##"><i class="icon fa fa-fw fa-ellipsis-v"></i> <span class="caret no-margin"></span></a>',


    datePickerOptionsBottom = {
        weekStart: 1,
        todayHighlight: true,
        format: "yyyy-mm-dd",
        autoclose: true,
        showOnFocus: false,
        language: _lang.languageSettings['langName'],
        startDate: -Infinity,
        endDate: Infinity,
        viewMode: 'days',
        minViewMode: 'days',
        orientation: "bottom"
    };
var gridDefaultPageSize = 20;
var scheduler = null;
var userLoggedInName;
var hearingGlobalData, activeOrganizations, REGEX_EMAIL;
var hearingAttachmentCount = 0;
var saveHtml = '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>';
REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
    '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

/* Funs  */
function copyHearingSummaryFromLawyerToClient() {
    if (jQuery('#summary', '#hearing-form').val()) {
        jQuery('#summary-to-client', '#hearing-form').val(jQuery('#summary', '#hearing-form').val());
    }
}
function rejectHearing(hearingId, caseId) {

}
// import Api from './../../advisor-portal/src/api/Api' //error undefined can't use api outside model

function verifyHearingSubmitWindow(container, caseId) {
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        type: 'POST',
        url: getBaseURL() + 'cases/hearings/' + caseId,
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery('#hearingsGrid').length) {
                    jQuery('#hearingsGrid').data("kendoGrid").dataSource.read();
                }
                jQuery(".modal", container).modal("hide");
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                if (jQuery('#case-events-container').length) {
                    legalCaseEvents.goToPage("stages", { id: response.caseId });
                }
            } else if (typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                displayValidationErrors(response.validationErrors, container);
            } else if (response.error) {
                pinesMessage({ ty: 'error', m: response.error });
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function fillHearingSummary(id, callback) {
    callback = callback || false;
    jQuery.ajax({
        url: getBaseURL() + 'cases/fill_hearing_summary/' + id,
        type: 'POST',
        dataType: 'JSON',
        data: { action: 'return_html' },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var hearingDialogId = '#hearing-summary-form-container';
                jQuery('<div id="hearing-summary-form-container"></div>').appendTo("body");
                var hearingDialog = jQuery(hearingDialogId);
                hearingDialog.html(response.html);
                commonModalDialogEvents(hearingDialog);
                initializeModalSize(hearingDialog);
                jQuery('.modal').on('hidden.bs.modal', function () {
                    destroyModal(hearingDialog);
                });
                jQuery("#form-summary-submit", hearingDialog).click(function () {
                    summaryHearingSubmitWindow(hearingDialog, id, callback);
                });
                jQuery(hearingDialog).find('input').keypress(function (e) {
                    if (e.which == 13) {// Enter pressed?
                        summaryHearingSubmitWindow(hearingDialog, id, callback);
                    }
                });
            } else if (response.error) {
                pinesMessage({ ty: 'error', m: response.error });
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function summaryHearingSubmitWindow(container, id, callback) {
    callback = callback || false;
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        type: 'POST',
        url: getBaseURL() + 'cases/fill_hearing_summary/' + id,
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery('#hearingsGrid').length) {
                    jQuery('#hearingsGrid').data("kendoGrid").dataSource.read();
                }
                jQuery(".modal", container).modal("hide");
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                if (callback && isFunction(callback)) callback();
            } else if (typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                displayValidationErrors(response.validationErrors, container);
            } else if (response.error) {
                pinesMessage({ ty: 'error', m: response.error });
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function verifyHearingWindow(hearingId, caseId) {
    hearingId = hearingId || 0;
    caseId = caseId || 0;
    if (!hearingId || !caseId) {
        pinesMessage({ ty: 'error', m: _lang.invalid_record });
    } else {
        jQuery.ajax({
            url: getBaseURL() + 'cases/hearings/' + caseId,
            type: 'POST',
            dataType: 'JSON',
            data: { hearingId: hearingId, action: 'hearingVerifySummaryWindow' },
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.html) {
                    var hearingDialogId = '#hearing-verify-form-container';
                    jQuery('<div id="hearing-verify-form-container"></div>').appendTo("body");
                    var hearingDialog = jQuery(hearingDialogId);
                    hearingDialog.html(response.html);
                    commonModalDialogEvents(hearingDialog);
                    initializeModalSize(hearingDialog);
                    if (typeof response.stage_html !== 'undefined' && response.stage_html) {
                        litigationStageDataEvents(response.stage_html, hearingDialog);
                        jQuery('#relate-to-case-stage-link', hearingDialog).remove();
                        jQuery('#remove-related-stage', hearingDialog).remove();
                        jQuery('.stage-name-container', hearingDialog).addClass('padding-top7');
                    } else {
                        jQuery('#stage-div', hearingDialog).html('');
                    }
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(hearingDialog);
                    });
                    jQuery("#form-submit", hearingDialog).click(function () {
                        verifyHearingSubmitWindow(hearingDialog, caseId);
                    });
                    jQuery(hearingDialog).find('input').keypress(function (e) {
                        if (e.which == 13) {// Enter pressed?
                            verifyHearingSubmitWindow(hearingDialog, caseId);
                        }
                    });
                } else if (response.error) {
                    pinesMessage({ ty: 'error', m: response.error });
                }
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function verifyHearingSummary() {
    var container = jQuery("#hearing-form");
    var caseId = jQuery("#legal_case_id", container).val();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        dataType: 'JSON',
        data: {
            id: jQuery("#id", container).val(),
            summary: jQuery("#summary", container).val(),
            summaryToClient: jQuery("#summary-to-client", container).val(),
            judgment: jQuery("#judgment", container).val(),
            action: 'hearingSubmitVerifiedSummary'
        },
        type: 'POST',
        url: getBaseURL() + 'cases/hearings/' + caseId,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery('#copy-summary-id').hide();
                jQuery('#verify-summary-id').hide();
                jQuery('#unverified-summary-label').tooltipster('destroy').removeAttr('title').html(_lang.hearingVerifiedTooltip);
                jQuery('#unverified-summary-icon').attr('src', 'assets/images/icons/verified-hearing.svg');
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
            } else if (typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                displayValidationErrors(response.validationErrors, container);
            } else if (response.error) {
                pinesMessage({ ty: 'error', m: response.error });
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function switchLang(value) {
    jQuery('#top-links-navbar-collapse-2').removeClass('in');
    jQuery.ajax({
        url: getBaseURL() + 'home/switch_lang/',
        type: 'POST',
        dataType: 'JSON',
        data: { 'lang': value },
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            setTimeout(function () {
                switch (response.status) {
                    case 'user_not_logged_in':
                        window.location = getBaseURL() + 'users/login';
                        break;
                    case 'lang_not_exists':
                        pinesMessage({ ty: 'warning', m: _lang.invalid_request });
                        break;
                    case 'success':
                        document.location.reload(true);
                        break;
                }
            }, 400);
        }
    });
}

function legalCaseHearingForm($id, $isCloneAction, $caseId, $isCaseHearing, callback, stageId, noStage) {
    $id = $id || 0;
    $isCloneAction = $isCloneAction || false;
    $isCaseHearing = $isCaseHearing || false;
    callback = callback || false;
    stageId = stageId || false;
    noStage = noStage || false;
    if ($isCaseHearing === true) {
        $caseId = jQuery('#legal-case', '#case-events-container').val();
    } else {
        $caseId = ($caseId === '' || undefined == $caseId) ? '' : $caseId;
    }
    jQuery.ajax({
        url: getBaseURL() + 'cases/hearings/' + $caseId,
        type: 'POST',
        dataType: 'JSON',
        data: { hearingId: $id, action: 'getHearingForm' },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var hearingDialogId = '#hearing-form-container';
                jQuery('<div id="hearing-form-container"></div>').appendTo("body");
                var hearingDialog = jQuery(hearingDialogId);
                hearingDialog.html(response.html);
                commonModalDialogEvents(hearingDialog);
                hearingFormEvents(hearingDialog, $id, $isCaseHearing);
                if ($isCloneAction) {
                    jQuery('#id', hearingDialog).val('');
                    jQuery('#start-date-input', hearingDialog).val('');
                    jQuery('#sTime', hearingDialog).val('');
                    jQuery('#postponed-date-input', hearingDialog).val('');
                    jQuery('#postponedTime', hearingDialog).val('');
                    jQuery('#event-id', hearingDialog).val('');
                    jQuery('.modal-title', hearingDialog).html(_lang.addHearing);
                }
                jQuery("#form-submit", hearingDialog).click(function () {
                    hearingFormSubmit(hearingDialog, $caseId, callback);
                    hearingGlobalData = {
                        'hearingsDialog': hearingDialog,
                        'isCaseHearing': $isCaseHearing,
                        'caseId': $caseId
                    };
                });
                jQuery(hearingDialog).find('input').keypress(function (e) {
                    // Enter pressed?
                    if (e.which == 13) {
                        hearingFormSubmit(hearingDialog, $caseId, callback);
                        hearingGlobalData = {
                            'hearingsDialog': hearingDialog,
                            'isCaseHearing': $isCaseHearing,
                            'caseId': $caseId
                        };
                    }
                });
                if (!$id && $isCaseHearing && !noStage) {
                    matterStageMetadata(hearingDialog, jQuery('#legal_case_id', hearingDialog).val(), stageId ? stageId : false);
                }

                if (typeof response.stage_html !== 'undefined' && response.stage_html) {
                    litigationStageDataEvents(response.stage_html, hearingDialog);
                } else {
                    jQuery('#stage-div', hearingDialog).html('');
                }
            } else if (typeof response.error !== 'undefined' && response.error) {
                pinesMessageV2({ ty: 'error', m: response.error });
            }
            jQuery('.effort-time-tooltip', hearingDialog).tooltipster()
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function hearingFormEvents(container, id, isCaseHearing) {
    initializeModalSize(container);
    checkBoxContainersValues({ 'attendees-container': jQuery('#selected-assignees', container) }, container);
    jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
    setDatePicker('#start-date', container);
    setDatePicker('#postponed-date', container);
    jQuery("#postponed-date", container).bootstrapDP("setStartDate", jQuery('#start-date-input', container).val());
    jQuery("#postponed-date", container).bootstrapDP("defaultViewDate", jQuery('#start-date-input', container).val());
    jQuery('#sTime', container).timepicker({
        'timeFormat': 'H:i'
    });
    jQuery('#postponedTime', container).timepicker({
        'timeFormat': 'H:i'
    });
    setDatePicker('#judgment-date', container);
    makeFieldsHijriDatePicker({ fields: ['start-date-hijri', 'postponed-date-hijri', 'judgment-date-hijri'] });
    // Convert Gregorian date in DB to Hijri date and put it in the picker
    jQuery('#start-date-hijri', '#start-date-hijri-container').val(gregorianToHijri(jQuery('#start-date-gregorian', '#start-date-hijri-container').val()));
    jQuery('#postponed-date-hijri').val(gregorianToHijri(jQuery('#postponed-date-gregorian', '#postponed-date-hijri-container').val()));
    jQuery('#judgment-date-hijri').val(gregorianToHijri(jQuery('#judgment-date-gregorian', '#judgment-date-hijri-container').val()));
    var dates = {
        'postponed-date': {
            'minOf': 'start-date',
            'maxOf': ''
        },
        'postponed-date-hijri': {
            'minOf': 'start-date-hijri',
            'maxOf': ''
        }
    }
    datesCombination(container, dates);
    if (jQuery('#postponed-date-input').length) {
        jQuery('#postponed-date-input, #postponedTime', container).on('change paste', function () {
            handlePostponeHearing(jQuery('#postponed-date-input', container).val(), jQuery('#start-date-input', container).val(), jQuery('#postponedTime', container).val(), jQuery('#sTime', container).val(), container);
        });
    }
    if (jQuery('#postponed-date-hijri').length) {
        jQuery('#postponedTime', container).on('change paste', function () {
            handlePostponeHearing(jQuery('#postponed-date-hijri', container).val(), jQuery('#start-date-hijri', container).val(), jQuery('#postponedTime', container).val(), jQuery('#sTime', container).val(), container);
        });
    }
    if (id == 0) {
        jQuery('#postponed-date-input', container).attr('disabled', 'disabled');
        jQuery('#postponed-date-hijri', container).attr('disabled', 'disabled');
        jQuery('#postponedTime', container).attr('disabled', 'disabled');
    }
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#start-date-input', container), jQuery('#start-date-container', container), true);
        getHijriDate(jQuery('#postponed-date-input', container), jQuery('#postponed-date-container', container), true);
    }
    jQuery('#start-date-input', container).change(function () {
        var newDate = new Date(jQuery('#start-date-input', container).val());
        var now = new Date();
        // keep only main fields if it's a add of a hearing
        if (!jQuery('#id', '#hearing-form').val()) {
            if (newDate > now) {
                jQuery('.hide-on-add-future-hearing').addClass('d-none');
            } else {
                jQuery('.hide-on-add-future-hearing').removeClass('d-none');
            }
        }
        // display time spent field when changed date is less than or equal current date
        if (newDate <= now) {
            jQuery('#time-spent-container').removeClass('d-none');
            jQuery('#hearing-time-spent-value').val('1:00');
        } else {
            jQuery('#time-spent-container').addClass('d-none');
            jQuery('#hearing-time-spent-value').val('');
        }
    });

    lookupPrivateUsers(jQuery('#lookupHearingLawyers', container), 'Hearing_Lawyers', '#selected-assignees', 'assignees-container', container);
    resizeLookupDropDownWidth(jQuery('.assignees-container', container));
    if (isCaseHearing === false) {
        var moreFilters = {
            'keyName': 'category',
            'value': 'Litigation',
            'resultHandler': fetchCaseRelatedDataToHearingFrom
        };
        lookUpCases(jQuery('#caseLookup', container), jQuery('#legal_case_id', container), 'legal_case_id', container, moreFilters,
            {
                'callback': function (data) {
                    jQuery("#latest_development", container).val(data.latest_development);
                }
            });
    } else {
        jQuery('#caseLookup').attr('readonly', 'readonly');
    }
    jQuery("#create-hearing-task", container).click(function () { //clone dialog
        jQuery("#trigger-create-task", container).val("yes");
        jQuery("#form-submit").click();
    });
    jQuery("#create-another-hearing", container).click(function () { // create another hearing
        jQuery("#trigger-create-another", container).val("yes");
        jQuery("#form-submit").click();
    });
}

// hide or show "add new hearing" checkbox when changing the postponed date
function handlePostponeHearing(pDate, sDate, pTime, sTime, container) {
    pTime = pTime ? pTime : '00:00';
    sTime = sTime ? sTime : '00:00';
    moment.locale('en');
    var startDate = moment(sDate + ' ' + sTime).format("YYYY/MM/DD hh:mm:ss UTC");
    var postponedDate = pDate ? moment(pDate + ' ' + pTime).format("YYYY/MM/DD hh:mm:ss UTC") : null;
    if (postponedDate && postponedDate > startDate) {
        jQuery('#new-hearing-div', container).removeClass('d-none');
        jQuery('#add-new-hearing', container).attr('checked', 'checked');
    } else if (postponedDate && postponedDate < startDate) {
        pinesMessage({ ty: 'error', m: _lang.feedback_messages.postponedDateGreaterThanStartDate });
        jQuery('#postponed-date-input', container).val(null);
        jQuery('#postponed-date-hijri', container).val(null);
        jQuery('#postponedTime', container).val(null);
        jQuery('#add-new-hearing', container).removeAttr('checked');
        jQuery('#new-hearing-div', container).addClass('d-none');
    }
}

// convert Hijri to Gregorian before sending Gregorian in post data
function convertToGregorianPrePost(hijriElement, gregorianElement) {
    if (hijriElement.val()) {
        m = moment(hijriElement.val(), 'iYYYY-iM-iD');
        // put Gregorian date in post data
        if (m.locale('en').format('YYYY-M-D') !== 'Invalid date') {
            gregorianElement.val(m.format('YYYY-M-D'));
        }
    } else {
        gregorianElement.val('');
    }
}

function hearingFormSubmit(container, caseId, callback) {
    // convert Hijri to Gregorian before sending it in post data
    // not needed as we are converting the dates in server side
    //    convertToGregorianPrePost(jQuery('#start-date-hijri', '#start-date-hijri-container'), jQuery('#start-date-gregorian', '#start-date-hijri-container'));
    //    convertToGregorianPrePost(jQuery('#postponed-date-hijri', '#postponed-date-hijri-container'), jQuery('#postponed-date-gregorian', '#postponed-date-hijri-container'));
    // get form data
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    formData.append('action', 'submitHearingForm');
    callback = callback || false;
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        type: 'POST',
        url: getBaseURL() + 'cases/hearings/' + caseId,
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery('#hearingsGrid').length) {
                    jQuery('#hearingsGrid').data("kendoGrid").dataSource.read();
                }
                if (jQuery('#my-dashboard').length > 0 || jQuery('#litigation-dashboard').length > 0) {
                    loadDashboardData('hearings');
                }
                if (!response.triggerCreateAnother) {
                    jQuery(".modal", container).modal("hide");
                } else {
                    // keep only main fields when saving and adding new hearing
                    jQuery(jQuery('#hearing-form').prop('elements')).each(function () {
                        if (jQuery.inArray(jQuery(this).attr('id'), ['start-date-gregorian', 'start-date-hijri', 'start-date-input', 'sTime', 'comments', 'summary', 'summary-to-client']) !== -1) {
                            jQuery(this).val('');
                            jQuery('input[type=file]', '#hearing-attachments').each(function () {
                                jQuery(this).attr("value", "");
                            });
                            if (jQuery("#judged-checkbox").is(":checked")) {
                                jQuery("#judged-checkbox").trigger('click');
                            }
                        }
                    });
                    jQuery('#trigger-create-another', '#hearing-form').val('');
                    jQuery('#id', '#hearing-form').val('');
                    jQuery('.modal-title', '#hearing-form-container').html(_lang.addHearing);
                    jQuery('#verified-hearing-icon', '#hearing-form').remove();
                    jQuery('#verify-summary-id', '#hearing-form').remove();
                    jQuery('#unverified-summary-label', '#hearing-form').remove();
                    jQuery('#unverified-summary-icon', '#hearing-form').remove();
                    jQuery('#verified-hearing-text', '#hearing-form').remove();
                    jQuery('#postponed-date-container', '#hearing-form').remove();
                    jQuery('#reasons-of-postponement-container', '#hearing-form').remove();
                }
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                if (response.postponed !== null && response.postponed) {
                    pinesMessage({ ty: 'success', m: _lang.feedback_messages.reminderPostponed });
                }
                if (typeof response.message !== 'undefined' && response.message) {
                    pinesMessage({ ty: response.message.type, m: response.message.text });
                }
                if (response.triggerCreateTask) {
                    setTimeout(function () {
                        taskForm(false, response.case_id, response.id, jQuery('#stage-id', '#hearing-form').val() ? jQuery('#stage-id', '#hearing-form').val() : null, callback);
                    }, 200);
                } else {
                    if (isFunction(callback)) {
                        callback(jQuery("#stage-id", container).val());
                    }
                }
                if (typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                    displayValidationErrors(response.validationErrors, container);
                }
            } else {
                if (typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                    displayValidationErrors(response.validationErrors, container);
                } else if (typeof response.error !== 'undefined' && response.error) {
                    pinesMessageV2({ ty: 'error', m: response.error });
                }
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });

}

function fixCaseDueDate(datedVal) {
    if (undefined === datedVal || datedVal === '') {
        jQuery('#dueDate').val('');
        jQuery('#closedOn').val('');
    } else {
        jQuery('#dueDate').datepicker('destroy');
        jQuery('#closedOn').datepicker('destroy');
        makeFieldsDatePicker({ fields: ['dueDate', 'closedOn'], minDate: datedVal });
    }
}

function fixCaseArrivalDate(datedVal) {
    if (undefined === datedVal || datedVal === '') {
        jQuery('#arrivalDate').val('');
    } else {
        jQuery('#arrivalDate').datepicker('destroy');
        makeFieldsDatePicker({ fields: ['arrivalDate'], maxDate: datedVal });
    }
}

function bindNewCaseFormEvents(allowSubmit, formContainer) {
    //bind form submit event
    if (!allowSubmit) {
        caseFormElement.submit(function () {
            if (!jQuery('#legalCaseDialogSave', newCaseFormDialog.parent()).is(":disabled")) {
                jQuery('#legalCaseDialogSave', newCaseFormDialog.parent()).click();
            }
            return false;
        });
    }
    //users
    jQuery('#userId', newCaseFormDialog).change(function () {
        if (jQuery('#userId', newCaseFormDialog).val() == 'quick_add') {
            jQuery('#userId', newCaseFormDialog).val('').trigger("chosen:updated");
            addUserToTheProviderGroup(jQuery('#provider_group_id', newCaseFormDialog).val(), 'userId');
        }
    }).chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseUsers,
        height: 130,
        width: "100%"
    });
    jQuery('#provider_group_id', newCaseFormDialog)
        .change(function () {
            if (jQuery('#provider_group_id', newCaseFormDialog).val() != '') {
                jQuery("#userId", newCaseFormDialog).removeAttr('disabled');
                reloadUsersListByProviderGroupSelected(jQuery('#provider_group_id', newCaseFormDialog).val(), jQuery("#userId", newCaseFormDialog));
            } else {
                jQuery("#userId", newCaseFormDialog).html('').attr('disabled', 'disabled').trigger("chosen:updated");
                jQuery("#user_id", newCaseFormDialog).val('');
            }
        }).chosen({
            no_results_text: _lang.no_results_matched,
            placeholder_text: _lang.chooseProviderGroup, height: "150px", width: "100%"
        });
    //make date pickers
    var container = '#' + formContainer;
    makeFieldsDatePicker({ fields: ['arrivalDate'], hiddenField: 'arrivalDate_Hidden', container: formContainer });
    makeFieldsDatePicker({
        fields: ['caseArrivalDate'],
        hiddenField: 'caseArrivalDate_Hidden',
        container: formContainer
    });
    makeFieldsDatePicker({ fields: ['dueDate'], hiddenField: 'dueDate_Hidden', container: formContainer });
    makeFieldsDatePicker({ fields: ['closedOn'], hiddenField: 'closedOn_Hidden', container: formContainer });
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#arrivalDate', container), jQuery('#filed-on-date-container', container), true);
        getHijriDate(jQuery('#dueDate', container), jQuery('#due-date-container', container), true);
        getHijriDate(jQuery('#caseArrivalDate', container), jQuery('#arrival-date-container', container), true);
        getHijriDate(jQuery('#closedOn', container), jQuery('#closed-on-date-container', container), true);
    }
    //add form validation
    newCaseFormValidation();
    caseFormWatchersUsersLookUp();
    caseFormWatchersEventsHandler();
    ctrlS(function () {
        caseFormElement.submit();
    });
    contactAutocompleteMultiOption('lookupCaseContact', 2, setContactToCaseForm);
    companyAutocompleteMultiOption(jQuery('#lookupCaseCompany', '#legalCaseAddForm'), setCompanyToCaseForm, true);
    userLookup(jQuery('#assigneeValue', '#legalCaseAddForm'), "user_id");
    clientInitialization(jQuery('#legalCaseAddForm'), { 'onselect': onCaseClientSelect });
    var legalCaseContainerLookupDetails = {
        'lookupField': jQuery('#legal-case-related-container-lookup', container),
        'errorDiv': 'legalCaseRelatedContainerId',
        'hiddenId': '#legal-case-related-container-id',
        'resultHandler': setLegalCaseContainerToCaseForm,
        'callback': {
            'onEraseLookup': function () {
                jQuery('#legal-case-related-container-link').addClass('d-none');
            },
            'onSelect': function (data) {
                if (jQuery('#legal-case-related-container-link').length) {
                    jQuery('#legal-case-related-container-link').removeClass('d-none').html('<i class="fa fa-external-link"></i>').attr('href', getBaseURL() + 'case_containers/edit/' + data.id).attr('title', 'MC' + data.id);
                }
            },
            'onChange': function () {
                return false;
            }
        }
    };
    lookUpLegalCaseContainers(legalCaseContainerLookupDetails, container);
    opponentsInitialization(jQuery('#opponents-container', container), { 'onselect': onCaseOpponentSelect });
}

function onCaseClientSelect(events, data, container) {
    var lookupType = jQuery("select#client-type", container).val();
    if (data.id > 0) {
        if (jQuery('#clientLinkId', container).length) {
            var clientHref = '';
            if (lookupType == 'contact') {
                clientHref = 'contacts/edit/';
            } else {
                if (data.category === 'Internal') {
                    clientHref = 'companies/tab_company/';
                } else if (ui.item.record.category === 'Group') {
                    jQuery('#clientLinkId', container).addClass('d-none');
                }
            }
            if (data.category !== 'Group') {
                jQuery('#clientLinkId', container).attr('href', getBaseURL() + clientHref + data.id).removeClass('d-none');
            }
        }
    }
}

function onCaseOpponentSelect(events, data, container) {
    var lookupType = jQuery("select#opponent-member-type", container).val();
    if (data.id > 0) {
        if (jQuery('.opponentLinkId', container).length) {
            var opponentHref = '';
            if (lookupType == 'contact') {
                opponentHref = 'contacts/edit/';
            } else {
                if (data.category === 'Internal') {
                    opponentHref = 'companies/tab_company/';
                } else if (ui.item.record.category === 'Group') {
                    jQuery('.opponentLinkId', container).addClass('d-none');
                }
            }
            if (data.category !== 'Group') {
                jQuery('.opponentLinkId', container).attr('href', getBaseURL() + opponentHref + data.id).removeClass('d-none');
            }
        }
    }
}

function caseClientFieldEvents(container) {
    jQuery('#clientType', '#' + container).change(function () {
        jQuery("#contact_company_id", '#' + container).val('');
        jQuery("#clientLookup", '#' + container).val('');
        jQuery("#clientLookup", '#' + container).attr('title', '');
        if (jQuery('#clientLinkId', '#' + container).length) {
            jQuery('#clientLinkId', '#' + container).addClass('d-none');
        }
    });
    jQuery('#clientLookup', '#' + container).change(function () {
        if (this.value == '')
            jQuery("#contact_company_id", '#' + container).val('');
    });
    jQuery("#clientLookup", '#' + container).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery("select#clientType", '#' + container).val();
            jQuery.ajax({
                url: getBaseURL() + lookupType + '/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched_add.sprintf([request.term]),
                            value: '',
                            record: { id: -1, term: request.term }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            if (lookupType == 'contacts') {
                                foreignFullName = item.foreignFullName.trim();
                                return {
                                    label: (item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName) + (foreignFullName ? (' - ' + foreignFullName) : ''),
                                    value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    record: item
                                }
                            } else if (lookupType == 'companies') {
                                return {
                                    label: (null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')') + (null == item.foreignName ? '' : ' - ' + item.foreignName),
                                    value: item.name,
                                    record: item
                                }
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            var lookupType = jQuery("select#clientType", '#' + container).val();
            if (ui.item.record.id > 0) {
                if (jQuery('#clientLinkId', '#' + container).length) {
                    var clientHref = '';
                    if (lookupType == 'contacts') {
                        clientHref = 'contacts/edit/';
                    } else {
                        if (ui.item.record.category === 'Internal') {
                            clientHref = 'companies/tab_company/';
                        } else if (ui.item.record.category === 'Group') {
                            jQuery('#clientLinkId', '#' + container).addClass('d-none');
                        }
                    }
                    if (ui.item.record.category !== 'Group') {
                        jQuery('#clientLinkId', '#' + container).attr('href', getBaseURL() + clientHref + ui.item.record.id).removeClass('d-none');
                    }
                }
                jQuery("#clientLookup", '#' + container).attr("title", lookupType == 'contacts' ? (ui.item.record.foreignFirstName != null ? ui.item.record.foreignFirstName : '') + ' ' + (ui.item.record.foreignLastName != null ? ui.item.record.foreignLastName : '') : (ui.item.record.foreignName != null ? ui.item.record.foreignName : ''));
                openManageMoneyAccounts(lookupType == 'contacts' ? 'contact' : 'company', ui.item.record.id, lookupType == 'contacts' ? ui.item.record.contact_category_id : ui.item.record.company_category_id, 'client');
                jQuery('#contact_company_id', jQuery(this).parent()).val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                if (jQuery('#clientLinkId', '#' + container).length) {
                    jQuery('#clientLinkId', '#' + container).addClass('d-none');
                }
                if (lookupType == 'contacts') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": jQuery("#" + container),
                        "lookupResultHandler": setContactCompanyDataToCaseClientField,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                    companyContactFormMatrix.companyDialog = {
                        "referalContainerId": jQuery("#" + container),
                        "lookupResultHandler": setContactCompanyDataToCaseClientField,
                        "lookupValue": ui.item.record.term
                    };
                    companyAddForm();
                }
            }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

function caseOpponentFieldEvents(opponentContainer) {
    jQuery('#opponent_member_type', opponentContainer).change(function () {
        jQuery("#opponent_member_id", opponentContainer).val('');
        jQuery("#opponentLookup", opponentContainer).val('');
        jQuery("#opponentLookup", opponentContainer).attr('title', '');
        if (jQuery('#opponentLinkId', opponentContainer).length) {
            jQuery('#opponentLinkId', opponentContainer).addClass('d-none');
        }
    });
    jQuery('#opponentLookup', opponentContainer).change(function () {
        if (this.value == '') {
            jQuery("#opponent_member_id", opponentContainer).val('');
        }
    });
    jQuery("#opponentLookup", opponentContainer).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var opponentLookup = jQuery("select#opponent_member_type", opponentContainer).val();
            var opponentUrl = (opponentLookup == 'contact') ? 'contacts' : 'companies';
            jQuery.ajax({
                url: getBaseURL() + opponentUrl + '/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched_add.sprintf([request.term]),
                            value: '',
                            record: { id: -1, term: request.term }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            if (opponentLookup == 'contact') {
                                foreignFullName = item.foreignFullName.trim();
                                return {
                                    label: (item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName) + (foreignFullName ? (' - ' + foreignFullName) : ''),
                                    value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    record: item
                                }
                            } else if (opponentLookup == 'company') {
                                return {
                                    label: (null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')') + (null == item.foreignName ? '' : ' - ' + item.foreignName),
                                    value: item.name,
                                    record: item
                                }
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            var opponentLookup = jQuery("select#opponent_member_type", opponentContainer).val();
            if (ui.item.record.id > 0) {
                if (jQuery('#opponentLinkId', opponentContainer).length) {
                    var opponentHref = '';
                    if (opponentLookup == 'contact') {
                        opponentHref = 'contacts/edit/';
                    } else {
                        if (ui.item.record.category === 'Internal') {
                            opponentHref = 'companies/tab_company/';
                        } else if (ui.item.record.category === 'Group') {
                            jQuery('#opponentLinkId', opponentContainer).addClass('d-none');
                        }
                    }
                    if (ui.item.record.category !== 'Group') {
                        jQuery('#opponentLinkId', opponentContainer).attr('href', getBaseURL() + opponentHref + ui.item.record.id).removeClass('d-none');
                    }
                }
                jQuery("#opponentLookup", opponentContainer).attr("title", opponentLookup == 'contact' ? (ui.item.record.foreignFirstName != null ? ui.item.record.foreignFirstName : '') + ' ' + (ui.item.record.foreignLastName != null ? ui.item.record.foreignLastName : '') : (ui.item.record.foreignName != null ? ui.item.record.foreignName : ''));
                jQuery('#opponent_member_id', opponentContainer).val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                if (jQuery('#opponentLinkId', opponentContainer).length) {
                    jQuery('#opponentLinkId', opponentContainer).addClass('d-none');
                }
                if (opponentLookup == 'contact') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": opponentContainer,
                        "lookupResultHandler": setContactCompanyDataToCaseOpponentField,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                    companyContactFormMatrix.companyDialog = {
                        "referalContainerId": opponentContainer,
                        "lookupResultHandler": setContactCompanyDataToCaseOpponentField,
                        "lookupValue": ui.item.record.term
                    }
                    companyAddForm();
                }
            }
        }
    });
}

function initializeOpponents(container) {
    var opponentsCount = jQuery('#opponentsCount', container).val();
    var count = 1;
    jQuery('.opponent-div', container).each(function () {
        if (count > opponentsCount) {
            return;
        }
        opponentContainer = jQuery('#opponent-' + count, '#opponentsContainer');
        caseOpponentFieldEvents(opponentContainer);
        count++;
    });
}

function updateOpponentsLables(nbOfOpponents) {
    var count = 1;
    jQuery('.opponent-label', '#opponentsContainer').each(function () {
        if (count < nbOfOpponents) {
            jQuery(this).html(_lang.opponent + ' (' + count + ')');
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.opponent-div', '#opponentsContainer').each(function () {
        if (count < nbOfOpponents) {
            jQuery(this).attr("id", 'opponent-' + count);
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.delete-opponent', '#opponentsContainer').each(function () {
        if (count < nbOfOpponents) {
            jQuery(this).attr("onclick", 'deleteOpponent(' + count + ', event)');
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.lookup', '#opponentsContainer').each(function () {
        if (count < nbOfOpponents) {
            jQuery(this).attr("onblur", "fixOpponentSearchInDialog();checkLookupValidity(jQuery(this), jQuery('#opponent_member_id', '#opponent-" + count + "'));");
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery("[data-field=administration-case_opponent_positions]", '#opponentsContainer').each(function () {
        if (count <= nbOfOpponents) {
            jQuery(this).attr("data-field-id", 'opponent-position-' + count);
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.opponent-position-quick-add', '#opponentsContainer').each(function () {
        if (count <= nbOfOpponents) {
            jQuery(this).removeAttr('onClick').attr("onclick", 'quickAdministrationDialog(\'case_opponent_positions\', \'#opponentsContainer\' , ' + false + ', ' + false + ', ' + false + ',jQuery("[data-field-id=opponent-position-' + count + ']"))');
            count++;
        } else {
            return true;
        }
    });
    initializeOpponents();
}

function deleteOpponent(opponentId, event, opponentsContainer) {
    var nbOfOpponents = jQuery('#opponentsCount', opponentsContainer).val();
    if (nbOfOpponents > 1) {
        jQuery('#opponent-' + opponentId, opponentsContainer).remove();
        jQuery('#opponentsCount', opponentsContainer).val(nbOfOpponents - 1);
        updateOpponentsLables(nbOfOpponents);
        if (nbOfOpponents == 2) {
            jQuery('.delete-icon', '#opponent-' + (1)).addClass('d-none');
        }
    } else {
        pinesMessage({ ty: 'warning', m: _lang.invalid_request });
    }
    event.preventDefault();
}

function addNewOpponent(opponentsContainer, event, maxNumber) {
    if (jQuery('.opponent-div', opponentsContainer).length == maxNumber) {
        pinesMessage({ ty: 'information', m: _lang.caseMaxOpponentsInfo.sprintf([maxNumber]), d: 10000 });
        return;
    }
    var nbOfOpponents = parseInt(jQuery('#opponentsCount', opponentsContainer).val());
    var clonedHtml = jQuery('.opponent-div', opponentsContainer).last().clone();
    clonedHtml.insertAfter(jQuery('.opponent-div', opponentsContainer).last());
    jQuery('#opponentsCount', opponentsContainer).val(nbOfOpponents + 1);
    updateOpponentsLables(nbOfOpponents + 2);
    jQuery('#opponentLinkId', jQuery('#opponent-' + (nbOfOpponents + 1), opponentsContainer)).addClass('d-none');
    jQuery('#opponent_member_id', jQuery('#opponent-' + (nbOfOpponents + 1), opponentsContainer)).val('');
    jQuery('#opponentLookup', jQuery('#opponent-' + (nbOfOpponents + 1), opponentsContainer)).val('');
    jQuery('#opponentLookup', jQuery('#opponent-' + (nbOfOpponents + 1), opponentsContainer)).attr('title', '');
    jQuery('#opponent_member_type', jQuery('#opponent-' + (nbOfOpponents + 1), opponentsContainer)).find('option').removeAttr('selected');
    jQuery('#opponent-position', jQuery('#opponent-' + (nbOfOpponents + 1), opponentsContainer)).find('option').removeAttr('selected');
    jQuery('#opponent-position', jQuery('#opponent-' + (nbOfOpponents + 1), opponentsContainer)).attr('data-field-id', 'opponent-position-' + (nbOfOpponents + 1));
    jQuery('.opponent-position-quick-add', jQuery('#opponent-' + (nbOfOpponents + 1), opponentsContainer)).removeAttr('onClick').click(function () {
        quickAdministrationDialog('case_opponent_positions', jQuery(opponentsContainer), false, false, false, jQuery('[data-field-id=opponent-position-' + (nbOfOpponents + 1) + ']'));
    });
    jQuery('.delete-icon', '#opponent-' + (nbOfOpponents + 1)).removeClass('d-none');
    if (nbOfOpponents == 1) {
        jQuery('.delete-icon', '#opponent-' + (nbOfOpponents)).removeClass('d-none');
    }
    jQuery("form#legalCaseAddForm").dirty_form({ includeHidden: true });
    event.preventDefault();
}

function setContactCompanyDataToCaseClientField(record, container, elementId, elementLookup) {
    elementId = elementId || '#contact_company_id';
    elementLookup = elementLookup || '#clientLookup';
    var clientName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery(elementId, container).val(record.id);
    jQuery(elementLookup, container).val(clientName);
}

function setContactCompanyDataToOutsourceField(record, container) {
    // the name may be company name or contact name
    var outsourceName = record.name ? record.name : (record.father ? (record.firstName + ' ' + record.father + ' ' + record.lastName) : (record.firstName + ' ' + record.lastName));
    jQuery('#outsource-id', container).val(record.id);
    jQuery('#outsource-lookup', container).val(outsourceName);
}

function sumTwoNumbers(x, y) {
    return parseInt(x + y);
}

function setCompanyRelatedContactData(record, container) {
    var contactName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#relatedContactId', container).val(record.id);
    jQuery('#relatedContactLookUp', container).val(contactName);
    jQuery('#btnAdd', container).removeAttr('disabled');
    jQuery('.k-grid').data('kendoGrid').dataSource.read();
}

function setContactCompanyDataToCaseOpponentField(record, container) {
    var opponentName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    var opponentForeignName = record.foreignName ? (record.foreignName ? record.foreignName : '') : ((record.foreignFirstName ? record.foreignFirstName : '') + ' ' + (record.foreignLastName ? record.foreignLastName : ''));
    jQuery('#opponent_member_id', container).val(record.id);
    jQuery('#opponentLookup', container).val(opponentName);
    jQuery('#opponentLookup', container).attr('title', opponentForeignName);
}

function setContactToCaseForm(record) {
    var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#lookupCaseContact').val(name);
    jQuery('#caseContactId').val(record.id);
}

function setReferredByContactToCaseForm(record) {
    var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#referredByLookup').val(name);
    jQuery('#referredBy').val(record.id);
    if (jQuery('#referredByLinkId').length && record.id > 0) {
        jQuery('#referredByLinkId').attr('href', getBaseURL() + 'contacts/edit/' + record.id).removeClass('d-none');
    } else {
        jQuery('#referredByLinkId').addClass('d-none');
    }
}

function setRequestedByContactToCaseForm(record) {
    var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#requestedByLookup').val(name);
    jQuery('#requestedBy').val(record.id);
    if (jQuery('#requestedByLinkId').length && record.id > 0) {
        jQuery('#requestedByLinkId').attr('href', getBaseURL() + 'contacts/edit/' + record.id).removeClass('d-none');
    } else {
        jQuery('#requestedByLinkId').addClass('d-none');
    }
}

function setCompanyToCaseForm(record) {
    jQuery('#lookupCaseCompany').val(record.name);
    jQuery('#caseCompanyId').val(record.id);
}

function caseFormWatchersEventsHandler() {
    var e = jQuery("#lookupWatchersUsers");
    var t = jQuery("#private");
    if (t.is(":checked")) {
        e.autocomplete("enable").removeAttr("readonly").removeClass("ui-state-disabled");
    } else {
        e.autocomplete("disable").attr("readonly", "readonly").addClass("ui-state-disabled");
    }
    t.click(function () {
        var caseAddFormWatchersContainer = jQuery('#caseAddFormWatchersContainer', newCaseFormDialog);
        if (this.checked) {
            e.autocomplete("enable").removeAttr("readonly").removeClass("ui-state-disabled");
            if (caseAddFormWatchersContainer.length) {
                caseAddFormWatchersContainer.removeClass('d-none');
            }
        } else {
            e.autocomplete("disable").attr("readonly", "readonly").addClass("ui-state-disabled");
            if (caseAddFormWatchersContainer.length) {
                caseAddFormWatchersContainer.addClass('d-none');
            }
        }
    });
}

function caseFormWatchersUsersLookUp() {
    jQuery("#lookupWatchersUsers", newCaseFormDialog).autocomplete({
        autoFocus: false, delay: 600, source: function (e, t) {
            e.term = e.term.trim();
            jQuery.ajax({
                url: getBaseURL() + "users/autocomplete/active",
                dataType: "json",
                data: e,
                error: defaultAjaxJSONErrorsHandler,
                success: function (n) {
                    if (n.length < 1) {
                        t([{
                            label: _lang.no_results_matched_for.sprintf([e.term]),
                            value: "",
                            record: { user_id: -1, term: e.term }
                        }])
                    } else {
                        t(jQuery.map(n, function (e) {
                            return { label: e.firstName + " " + e.lastName, value: "", record: e }
                        }))
                    }
                }
            })
        }, minLength: 2, select: function (e, t) {
            if (t.item.record.id > 0) {
                setWatchersUsersDataToBoxMultiOptionAfterAutocomplete(t.item.record)
            }
        }
    })
}

function caseLookup(lookupField, legalCaseIdField, extraOptions) {
    var extraOptions = jQuery.extend({
        legalCaseSubject: false, onSelect: function () {
        }
    }, extraOptions), legalCaseIdField = legalCaseIdField || false;
    $lookupField = ('string' == typeof lookupField) ? jQuery('#' + lookupField) : lookupField;
    $legalCaseIdField = ('string' == typeof legalCaseIdField) ? jQuery('#' + legalCaseIdField) : legalCaseIdField;
    extraOptions.legalCaseSubject = ('string' == typeof extraOptions.legalCaseSubject) ? jQuery('#' + extraOptions.legalCaseSubject) : extraOptions.legalCaseSubject;
    $lookupField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'cases/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: {
                                id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.id.replace(/^0+/, '') + ': ' + item.subject,
                                value: item.id,
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                if (false != $legalCaseIdField)
                    $legalCaseIdField.val(ui.item.record.id);
                if (false != extraOptions.legalCaseSubject)
                    extraOptions.legalCaseSubject.text(ui.item.record.subject);
                if (false !== extraOptions.afterSaveResultHandlerFunction && undefined !== extraOptions.afterSaveResultHandlerFunction) {
                    caseResultHandlerFunction = extraOptions.afterSaveResultHandlerFunction;
                    caseResultHandlerFunction(ui.item.record);
                }
            }
            extraOptions.onSelect(ui.item.record);
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

function clearTaskUnbindCase() {
    jQuery("#taskDescription").html('');
    if (!jQuery('#case-log-time').length && !jQuery('input[name=action][value=StartTimer]').length) {
        jQuery('#client-div').addClass('d-none');
        clearLegalCaseData();
    }
}

function clearLegalCaseData() {
    jQuery('#legalCaseLookupId').val('');
    jQuery('#legalCaseLookup').val('').autocomplete('option', 'disabled', false);
    jQuery('#legalCaseSubject').html('');
    jQuery('#task-related-case-subject').remove();
    jQuery('#client-id').val('');
    jQuery('#client-name').val('');
}

function showTaskRelatedCaseSubject(legalCaseSubject, container, category) {
    var taskRelatedCaseSubjectText = _lang.relatedCase + ': ' + jQuery(legalCaseSubject, container).html() + ' <a target="_blank" href="' + getBaseURL() + (category == undefined || category != 'IP' ? 'cases' : 'intellectual_properties') + '/edit/' + jQuery('#legalCaseLookupId', container).val() + '">' + _lang.goTo + '</a>';
    if (jQuery('#task-related-case-subject', container).length) {
        jQuery('#task-related-case-subject', container).html(taskRelatedCaseSubjectHtml);
    } else {
        jQuery('#taskDescription', container).after('<div id="task-related-case-subject" class="help-inline">' + taskRelatedCaseSubjectText + '</div>');
    }
}

function companyAutocompleteMultiOption(jQueryField, resultHandlerFunction, flag, multiSelect) {
    flag = flag || false;
    multiSelect = multiSelect || false;
    jQueryField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'companies/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: (flag) ? _lang.no_results_matched_add.sprintf([request.term]) : _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: {
                                id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            var value = '';
                            if (!flag)
                                value = (null == item.shortName) ? item.name : (item.name + ' (' + item.shortName + ')');
                            else
                                value = '';
                            return {
                                label: ((flag != false) ? ((null == item.shortName) ? item.name : (item.name + ' (' + item.shortName + ')')) : value) + (null == item.foreignName ? '' : ' - ' + item.foreignName),
                                value: multiSelect ? '' : item.name,
                                record: item
                            };
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                resultHandlerFunction(ui.item.record);
            } else if (ui.item.record.id == -1) {
                if (flag) {
                    companyContactFormMatrix.companyDialog = {
                        "lookupResultHandler": resultHandlerFunction,
                        "lookupValue": ui.item.record.term
                    };
                    companyAddForm();
                }
            }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

function contactAutocompleteMultiOption(fieldId, flag, resultHandlerFunction) {
    fieldId = ('string' == typeof fieldId) ? jQuery('#' + fieldId) : fieldId;
    jQuery(fieldId).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'contacts/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: (flag == '1' || flag == '2') ? _lang.no_results_matched_add.sprintf([request.term]) : _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: {
                                id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            foreignFullName = item.foreignFullName.trim();
                            return {
                                label: (item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName) + (foreignFullName ? (' - ' + foreignFullName) : ''),
                                value: (flag == '2' || flag == '3') ? (item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName) : '',
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                resultHandlerFunction(ui.item.record);
            } else if (ui.item.record.id == -1 && flag != '3' && flag != '4') {
                companyContactFormMatrix.contactDialog = {
                    "lookupResultHandler": resultHandlerFunction,
                    "lookupValue": ui.item.record.term
                }
                contactAddForm();
            }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

function partyAutocompleteMultiOption (fieldId, flag, resultHandlerFunction, multiSelect) {
    flag = flag || false;
    multiSelect = multiSelect || false;
    fieldId = ('string' == typeof fieldId) ? jQuery('#' + fieldId) : fieldId;
    jQuery(fieldId).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL('contract') + 'contracts/autocomplete_party',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched,
                            value: '',
                            record: {
                                id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            var contact_type = (item.company_id)? 'company' : 'contact';
                            var value = '';
                            if(item.company_id){
                                if (!flag){
                                    value = (null == item.shortName) ? item.name : (item.name + ' (' + item.shortName + ')');
                                } else {
                                    value = '';
                                }
                                return {
                                    label: ((flag != false) ? ((null == item.shortName) ? (item.name + ' (' + contact_type + ')'): (item.name + ' (' + item.shortName + ')'+ ' (' + contact_type + ')')) : value) + (null == item.foreignName ? '' : ' - ' + item.foreignName),
                                    value: multiSelect ? '' : item.name,
                                    record: item
                                };
                            } else {
                                foreignFullName = item.foreignFullName.trim();
                                return {
                                    label: (item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName + ' (' + contact_type + ')' : item.firstName + ' ' + item.lastName + ' (' + contact_type + ')') + (foreignFullName ? (' - ' + foreignFullName) : ''),
                                    value: multiSelect ? '':(item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName),
                                    record: item
                                }
                        }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                resultHandlerFunction(ui.item.record);
            } 
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;

        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

//check if reminder has recurrence=> show the dialog options
function checkReminderRecurrence(id) {
    jQuery.ajax({
        url: getBaseURL() + "reminders/check_is_recurrence",
        type: "GET",
        dataType: "JSON",
        data: { id: id },
        async: false,
        success: function (response) {
            if (response.html) {
                if (jQuery('#recurrence-options-container').length <= 0) {
                    jQuery('<div id="recurrence-options-container"></div>').appendTo("body");
                    var recurrenceContainer = jQuery('#recurrence-options-container');
                    recurrenceContainer.html(response.html);
                    jQuery('.tooltip-title', recurrenceContainer).tooltipster();
                    jQuery('.modal-container', recurrenceContainer).addClass('modal');
                    commonModalDialogEvents(recurrenceContainer);
                    jQuery("#form-submit", recurrenceContainer).click(function () {
                        var deleteRecurrence = jQuery('input[type=radio]:checked', recurrenceContainer).val() == 1 ? 'yes' : 'no';
                        jQuery(".modal", recurrenceContainer).modal("hide");
                        deleteReminder(id, deleteRecurrence);
                    });
                }
            } else {
                confirmationDialog('confim_delete_action', { resultHandler: deleteReminder, parm: id });
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteReminder(e, deleteRecurrence) {
    data = { reminderId: e };
    if (deleteRecurrence) {
        data['delete_recurrence'] = deleteRecurrence;
    }
    jQuery.ajax({
        url: getBaseURL() + "reminders/delete",
        type: "POST",
        dataType: "JSON",
        data: data,
        success: function (t) {
            switch (t.status) {
                case 202:
                    updateRemindersAfterActions(e);
                    if (jQuery("#reminderGrid").length > 0) {
                        jQuery("#reminderGrid").data("kendoGrid").dataSource.read()
                    }
                    if (jQuery("#reminders-grid").length > 0) {
                        jQuery("#reminders-grid").data("kendoGrid").dataSource.read()
                    }
                    if (jQuery("#related-reminders-grid").length > 0) {
                        jQuery("#related-reminders-grid").data("kendoGrid").dataSource.read()
                    }
                    break;
                case 101:
                    pinesMessage({ ty: "error", m: _lang.recordNotDeleted });
                    break;
                case 102:
                    pinesMessage({ ty: "error", m: _lang.dischargeOfSocialSecurityRelatedReminders });
                    break;
                default:
                    break
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function disableEmpty(e) {
    // select and input not date picker
    jQuery('div.form-group input.form-control:last-of-type, div.form-group select.form-control:last-of-type', e).each(function (e, t) {
        if (String(t.value).trim() === "" && (!jQuery(t).hasClass('hasDatepicker') && !jQuery(t).hasClass('hijri-date-picker') )  )
            jQuery('input, select', jQuery(t).parent().parent()).attr("disabled", "disabled");     
    });

    // first input date picker
    jQuery('div.form-group input.form-control.hasDatepicker:first-of-type', e).each(function (e, t) {
        if (String(t.value).trim() === "") {
            var containerParent = jQuery(t).parent().parent();
            containerParent.children().slice(1, 3).each(function () {
                jQuery('input, select', jQuery(this)).attr("disabled", "disabled");
            });
        }
    });
    // last input date picker "end value"
    jQuery('div.form-group input.form-control.hasDatepicker:last-of-type', e).each(function (e, t) {
        if (String(t.value).trim() === "") {
            jQuery('input, select', jQuery(t).parent()).attr("disabled", "disabled");
        }
    });
    // Hijri date picker
    jQuery('div.form-group input.form-control.hijri-date-picker', e).each(function (e, t) {
        
        if (String(t.value).trim() === "") {
            
            var containerParent = jQuery(t).parent();
            
            /*********Using Use a safety counter to prevent Infinite loop In case hijri-date Input parent with id "*.Contianer" not found ******/
            /*********Note : In html forms each hijri-date input should have one of its parent with id "*.Contianer" */
            var safety = 0;
            const maxSafety = 5;

            var containerParentID = containerParent.attr('id');

            while( typeof containerParentID == "undefined" || ( typeof containerParentID != "undefined" && !containerParentID.endsWith('Container') )
            && safety++ < maxSafety )
            {
                containerParent = containerParent.parent();
                containerParentID = containerParent.attr('id');    
            }

            if (safety > maxSafety)
             throw new Error('Infinite loop detected and prevented');

            containerParent.find('[data-input-filter-id=\'${t.id}\']').each(function () {
                jQuery(this).attr("disabled", "disabled");
            });    
        }
    }); 
}

/**
 * Disable all input and select picker inside container "not that html stracture should follows the same structure of element selector below"
 * @param container
 */
function disableEmptyFilter(container) {
    // select and input not date picker
    jQuery('div.form-group select.form-control, div.form-group input.form-control', container).each(function (index, value) {
        if (String(value.value).trim() === "" && !jQuery(value).hasClass('hasDatepicker')) {
            jQuery('input, select', jQuery(value).parent().parent()).prop("disabled", true);
        }
    });
    // first input date picker
    jQuery('div.form-group input.form-control.hasDatepicker, div.form-group input.form-control.hijri-date-picker', container).each(function (index, value) {
        if (String(value.value).trim() === "") {
            var containerParent = jQuery(value).parent().parent();
            containerParent.children().each(function () {
                jQuery('input, select', jQuery(this)).prop("disabled", true);
            });
        }
    });
}
/**
 * Enable all input and select picker inside container "not that html stracture should follows the same structure of element selector below"
 * @param container
 */
function enableEmptyFilter(container) {
    // select and input not date picker
    jQuery('div.form-group select.form-control, div.form-group input.form-control', container).each(function (e, t) {
        if (String(t.value).trim() === "" && !jQuery(t).hasClass('hasDatepicker')) {
            jQuery('input, select', jQuery(t).parent().parent()).prop("disabled", false);
        }
    });
    // first input date picker
    jQuery('div.form-group input.form-control.hasDatepicker, div.form-group input.form-control.hijri-date-picker', container).each(function (e, t) {
        if (String(t.value).trim() === "") {
            var containerParent = jQuery(t).parent().parent();
            containerParent.children().each(function () {
                jQuery('input, select', jQuery(this)).prop("disabled", false);
            });
        }
    });
}

function disableUnCheckedFilters() {
    jQuery('.chk-filter').each(function () {
        var chk_filter = jQuery(this);
        var cont = chk_filter.parent().parent().find(".data-filter");
        cont.each(function () {
            if (!chk_filter.is(":checked")) {
                jQuery('input, select', jQuery(this)).attr("disabled", "disabled");
            } else {
                if (String(jQuery('.sf-value', jQuery(this)).val()).trim() === "" || jQuery('.sf-value', jQuery(this)).val() == null) {
                    jQuery('input, select', jQuery(this)).attr("disabled", "disabled");
                }
                if (jQuery('.end-date-icon-filter', jQuery(this)).hasClass("d-none")) {
                    jQuery('input, select', jQuery(".end-date-container", jQuery(this))).attr("disabled", "disabled");
                }
            }
        });
    });
}

function dismissReminder(e) {
    jQuery.ajax({
        url: getBaseURL() + "reminders/dismiss",
        type: "POST",
        dataType: "JSON",
        data: { reminderId: e },
        success: function (t) {
            switch (t.status) {
                case 202:
                    updateRemindersAfterActions(e);
                    if (jQuery("#reminderGrid").length > 0) {
                        jQuery("#reminderGrid").data("kendoGrid").dataSource.read()
                    }
                    if (jQuery("#reminders-grid").length > 0) {
                        jQuery("#reminders-grid").data("kendoGrid").dataSource.read()
                    }
                    if (jQuery("#related-reminders-grid").length > 0) {
                        jQuery("#related-reminders-grid").data("kendoGrid").dataSource.read()
                    }
                    break;
                case 101:
                    pinesMessage({ ty: "error", m: _lang.feedback_messages.updatesFailed });
                    break;
                default:
                    break
            }
        },
        error: defaultAjaxJSONErrorsHandler
    })
}

function taskEditForm(id, e, callback) {
    callback = callback || false;
    if ('undefined' !== typeof e && e) {
        e.preventDefault();
    }
    callback ? taskForm(id, false, false, false, callback) : taskForm(id, false);
}

function enableAll(filtersForm) {
    jQuery('div.form-group input.form-control:last-of-type[disabled=disabled], div.form-group select.form-control:last-of-type[disabled=disabled]', filtersForm).each(function (index, element) {
        jQuery(element).parent().children().removeAttr('disabled');
        if (typeof jQuery(this).attr('field-type') !== typeof undefined && jQuery(this).attr('field-type') !== false && jQuery(this).attr('field-type') == 'list') {
            jQuery(this).trigger("chosen:updated");
        }
    });
    jQuery('input:disabled', filtersForm).each(function () {
        jQuery(this).removeAttr('disabled');
    });
}

function enableDisableTaskUsersLookup() {
    var taskDialog = jQuery('#task-dialog');
    var lookupTaskUsers = jQuery('#lookupTaskUsers', taskDialog), privateTask = jQuery('#private', taskDialog);
    if (privateTask.is(':checked')) {
        jQuery('.task-users-container', taskDialog).removeClass('d-none');
        jQuery('.modal-body', taskDialog).scrollTo(jQuery("#selected_task_users", taskDialog));
        lookupTaskUsers.focus();
    } else {
        jQuery('.task-users-container', taskDialog).addClass('d-none')
    }
}

function isKendoInlineFormHasUnsavedData() {
    jQuery(window).bind('beforeunload', function (event) {
        if (jQuery('.k-dirty').length > 0) {
            return _lang.warning_you_have_unsaved_changes;
        }
    });
}
function triggerFormEvents(container, data, usersLookUpId) {
    if(usersLookUpId == -1){
        usersLookUpId = '';
    }
    jQuery('.select-picker', container).selectpicker({
        dropupAuto: false
    });
    lookUpUsers(jQuery('#assignedToLookUp'+usersLookUpId, container), jQuery('#assignedToId'+usersLookUpId, container), 'assigned_to'+usersLookUpId, jQuery('.assignee-container', container), container);
    jQuery('#assignedToId'+usersLookUpId, container).change(function () {
        if (jQuery('#user-relation', container).val() > 0 && jQuery('#assignedToId'+usersLookUpId, container).val() > 0 && jQuery('#user-relation', container).val() !== jQuery('#assignedToId'+usersLookUpId, container).val()) {
            pinesMessage({ ty: 'warning', m: _lang.feedback_messages.assignmentChangeForAssignee });
        }
    });
}

function taskFormEvents(container, data) {
    setDatePicker('#form-due-date', container);
    if (typeof data.restricted_by_task_reporter !== "undefined" && data.restricted_by_task_reporter) {
        jQuery('#form-due-date-input', container).attr("disabled", "disabled");
        tinyMCE.get('description').setMode('readonly');
        jQuery('#assignedToLookUp', container).attr("disabled", "disabled");
        jQuery('#type', container).attr("disabled", "disabled");
        jQuery('#quickAddButton', container).attr("disabled", "disabled");
        jQuery('#reporterLookUp', container).attr("disabled", "disabled");
        jQuery('#assignToMeLinkId', container).addClass("disabled-anchor");
        jQuery('#notify-me-link', container).addClass("disabled-anchor");
        jQuery('#date-conversion', container).addClass("disabled-anchor");
        jQuery('.assign-to-me-link-id-wrapper', container).addClass("drop-pointer");
        jQuery(".restriction-tooltip", container).each(function () {
            jQuery(this).append('<span title="' + _lang.onlyReporterCanEdit + '" class="tooltip-title m-h-3"><i class="fa-solid fa-circle-question purple_color"></i></span>');
        });
    }
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#due-date', container), jQuery('#due-date-container', container));
    }
    notifyMeBeforeEvent({ 'input': 'due-date-input', 'inputContainer': 'due-date' }, container, true);
    jQuery('.select-picker', '#task-dialog').selectpicker();
    jQuery('#type', '#task-dialog').on('shown.bs.select', function (e) {
        jQuery('.dropdown-menu.inner').animate({
            scrollTop: jQuery(".selected").offset().top
        }, "fast");
        jQuery('.modal-body').animate({
            scrollTop: '0px'
        }, "fast");
    });
    var moreFilters = { 'keyName': 'isDeleted', 'value': '0', 'resultHandler': fetchCaseRelatedDataToHearingFrom };
    lookUpCases(jQuery('#caseLookup', container), jQuery('#caseLookupId', container), 'legal_case_id', container, moreFilters);
    lookUpUsers(jQuery('#assignedToLookUp', container), jQuery('#assignedToId', container), 'assigned_to', jQuery('.assignee-container', container), container);
    jQuery('#assignedToId', container).change(function () {
        if (jQuery('#user-relation', container).val() > 0 && jQuery('#assignedToId', container).val() > 0 && jQuery('#user-relation', container).val() !== jQuery('#assignedToId', container).val()) {
            pinesMessage({ ty: 'warning', m: _lang.feedback_messages.assignmentChangeForAssignee });
        }
    });
    lookUpUsers(jQuery('#reporterLookUp', container), jQuery('#reporter-id', container), 'reporter', jQuery('.reporter-container', container), container);
    lookUpLocations(jQuery('#location', container), jQuery('#task_location_id', container), 'task_location_id', container);
    lookupPrivateUsers(jQuery('#lookupTaskUsers', container), 'Task_Users', '#selected_task_users', 'users-lookup-container', jQuery('#task-form'));
    lookupPrivateUsers(jQuery('#contributors-lookup', container), 'contributors', '#selected-contributors', 'contributors-container', container);
    lookUpContracts({
        'lookupField': jQuery('#lookup-contract', container),
        'hiddenId': jQuery('#lookup-contract-id', container),
        'errorDiv': 'contract_id'
    }, container);
    checkBoxContainersValues({ 'contributors-container': jQuery('#selected-contributors', container) }, container);
    enableDisableTaskUsersLookup();
    initializeModalSize(container);
}

function loadUserLatestReminders(load_type) {
    jQuery.ajax({
        url: getBaseURL() + 'reminders/reminders_list',
        dataType: "json",
        type: 'POST',
        data: { typeReturn: 'list' },
        success: function (response) {
            if (load_type === 'refresh') {
                if (response.totalRows >= 1) {
                    jQuery('#pendingReminders').css('display', 'inline-block').text(response.totalRows);
                }
            } else if (load_type === 'load_all') {
                clearInterval(refreshRemindersIntervalId);
                jQuery('h3.popover-header', jQuery('#remindersListLatestContainer').parent().parent()).html(_lang.remindersCounter.sprintf([response.totalRows]) + remindersPopoverIconClose).popover('show');

                if (response.totalRows >= 1) {
                    jQuery('#pendingReminders').css('display', 'inline-block').text(response.totalRows);
                } else {
                    jQuery('#pendingReminders').html('');
                }
                jQuery('#remindersListLatest').removeClass('loading').html(response.html);
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function is_touch_device() {
    return (('ontouchstart' in window)
        || (navigator.MaxTouchPoints > 0)
        || (navigator.msMaxTouchPoints > 0));
}

function makeFieldsDatePicker(extraOptions) {
    // This line is used to prevent keyboard provoking when selecting datepicker on small devices
    //jQuery('*[placeholder="YYYY-MM-DD"]').attr('readonly', 'readonly');
    if (is_touch_device())
        jQuery('*[placeholder="YYYY-MM-DD"]').attr('readonly', 'readonly');

    function ii() {
        var e = jQuery(this);
        if (e.val() == "")
            return;
        var t = null;
        s(e, t);
    }

    function s(e, t) {
        if (undefined != extraOptions.yearRange || extraOptions.yearRange != '') {
            e.datepicker("option", "yearRange", extraOptions.yearRange);
        } else {
            e.datepicker("option", "yearRange", 'c-25:c+25');
        }
        if (undefined != extraOptions.minDate || extraOptions.minDate != '') {
            e.datepicker("option", "minDate", extraOptions.minDate);
        } else {
            e.datepicker("option", "minDate", null);
        }
        if (undefined != extraOptions.maxDate || extraOptions.maxDate != '') {
            e.datepicker("option", "maxDate", extraOptions.maxDate);
        } else {
            e.datepicker("option", "maxDate", null);
        }
        return
    }

    var options = jQuery.extend({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        firstDay: 1
    }, _lang.jQuery_datepicker_options);
    if (undefined === extraOptions.yearRange || extraOptions.yearRange === '')
        options.yearRange = 'c-25:c+25';
    else
        options.yearRange = extraOptions.yearRange;
    if (undefined != extraOptions.minDate || extraOptions.minDate != '')
        options.minDate = extraOptions.minDate;
    if (undefined != extraOptions.maxDate || extraOptions.maxDate != '')
        options.maxDate = extraOptions.maxDate;
    if ((undefined != extraOptions.hiddenField || extraOptions.hiddenField != '') && (undefined != extraOptions.container || extraOptions.container != '')) {
        options.hiddenField = extraOptions.hiddenField;
        options.container = extraOptions.container;
        options.onSelect = function () {
            var date = jQuery(this).datepicker('getDate');
            if (date) {
                var day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                var month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                var year = date.getFullYear();
                var fullDate = year + "-" + month + "-" + day;
                jQuery('#' + options.hiddenField, '#' + options.container).val(fullDate).trigger('change');
                jQuery(this).blur().change();
            }
        };
    } else {
        options.onSelect = function () {
            jQuery(this).blur().change();
        };
    }

    var fields = extraOptions.fields;
    for (i in fields) {
        if (undefined !== extraOptions.container && extraOptions.container !== '') {
            fieldSelector = ('string' === typeof fields[i]) ? jQuery('#' + fields[i], '#' + extraOptions.container) : jQuery(fields[i], '#' + extraOptions.container);
        } else {
            fieldSelector = ('string' === typeof fields[i]) ? jQuery('#' + fields[i]) : jQuery(fields[i]);
        }

        var additionalOptions = {};

        if (typeof extraOptions.startDate != 'undefined' && extraOptions.startDate.length > 0) {
            for (j in extraOptions.startDate) {
                jQuery.each(extraOptions.startDate[j], function (key, item) {
                    if (key == fields[i]) {
                        additionalOptions.minDate = jQuery('#' + item).val();
                    }
                });
            }
        }

        fieldSelector.datepicker(jQuery.extend({}, options, additionalOptions));
        fieldSelector.on("changeDate change", ii);
    }
}

function newCaseFormAssigneeLookUp() {
    jQuery('#userId', newCaseFormDialog).autocomplete({
        autoFocus: false,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.join = ['provider_groups_users'];
            request.more_filters = {};
            request.more_filters.provider_group_id = jQuery("#provider_group_id").val();
            jQuery.ajax({
                url: getBaseURL() + 'users/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched_add.sprintf([request.term]),
                            value: '',
                            record: {
                                user_id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.firstName + ' ' + item.lastName,
                                value: item.firstName + ' ' + item.lastName,
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#user_id', newCaseFormDialog).val(ui.item.record.user_id);
            } else if (ui.item.record.id == -1) {

            }
        }
    });
}

function newCaseFormValidation() {
    jQuery("#legalCaseAddForm", newCaseFormDialog).validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomLeft',
        scroll: false,
        prettySelect: true,
        useSuffix: "_chosen",
        'custom_error_messages': {
            '#case_status_id': { 'required': { 'message': _lang.validation_field_required.sprintf([_lang.case_status]) } },
            '#case_type_id': { 'required': { 'message': _lang.validation_field_required.sprintf([_lang.caseType]) } },
            '#subject': { 'required': { 'message': _lang.validation_field_required.sprintf([_lang.subject_Case]) } },
            '#provider_group_id': { 'required': { 'message': _lang.validation_field_required.sprintf([_lang.providerGroup]) } },
            '#arrivalDate': { 'custom': { 'message': _lang.validation_field_date_format_required.sprintf([_lang.filedOn]) } }
        }
    });
}

function niceDropDownLists() {
    jQuery("#nationality_id").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseNationality,
        height: 100,
        width: '100%'
    });
    jQuery("#country_id").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseCountry,
        height: 100,
        width: '100%'
    });
}

function taskAddForm(caseId, stageId, callback, noStage) {
    callback = callback || false;
    noStage = noStage || false;
    callback ? taskForm(false, caseId, false, stageId, callback, noStage) : taskForm(false, caseId, false, stageId);
}

function reloadUsersListByProviderGroupSelected(pGId, userListFieldId, isDialog) {
    isDialog = isDialog || false;
    var showAssignee = false;
    jQuery.ajax({
        url: getBaseURL() + 'users/autocomplete/active',
        dataType: 'JSON',
        async: false,
        data: { join: ['provider_groups_users'], more_filters: { 'provider_group_id': pGId }, term: '' },
        success: function (results) {
            var newOptions = '<option value="">&nbsp;</option>';
            if (jQuery('#all-users-provider-group').val() !== undefined && jQuery('#all-users-provider-group').val() !== '') {
                if (jQuery('#all-users-provider-group').val() !== pGId) {
                    newOptions += '<option value="quick_add">' + _lang.clickToAddUserToTeam + '</option>';
                }
            }
            if (typeof results != "undefined" && results != null && results.length > 0) {
                for (i in results) {
                    newOptions += '<option value="' + results[i].id + '">' + results[i].firstName + ' ' + results[i].lastName + '</option>';
                    if (!isDialog && assigneeInfo != "undefined" && pGId === assigneeInfo.assignedTeamId && assigneeInfo.assigneeId !== results[i].id) {//edit of the case form and the assginee id is not in the list of the assignee ids for this assigned team=> The user was assigned before in this assigned team
                        showAssignee = true;
                    }
                }
            } else {
                if (!isDialog && assigneeInfo != "undefined" && pGId === assigneeInfo.assignedTeamId) {//edit of the case form and there is no users in this assigned team and their is an assignee for this case=>The user was assigned before in this assigned team
                    showAssignee = true;
                }
            }
            if (showAssignee) {
                newOptions += '<option value="' + assigneeInfo.assigneeId + '">' + assigneeInfo.assigneeName + '</option>';
            }
            if (isDialog) {
                userListFieldId.html(newOptions).selectpicker("refresh");
            } else {
                userListFieldId.html(newOptions).trigger("chosen:updated");
            }

        }, error: defaultAjaxJSONErrorsHandler
    });
}

function resetContactCheckboxesValues() {
    var contactDialog = jQuery("#contactDialog");
    jQuery("#isLawyer", contactDialog).val('no');
    jQuery("#lawyerForCompany", contactDialog).val('no');
}

function resetNewCaseDialogForm() {
    caseFormElement[0].reset();
    jQuery("#provider_group_id").trigger("chosen:updated");
    jQuery('[name="provider_group_id"]').val('').trigger("chosen:updated");
    jQuery("#userId").trigger("chosen:updated");
    jQuery('[name="user_id"]').val('').trigger("chosen:updated");
    jQuery("#selected_companies, #selected_referrers, #selected_opponents, #selected_opponent_lawyers, #selected_watchers_users").html('');
    jQuery('#category').val('Matter');
    jQuery('#private').attr('checked', false);
    jQuery('#caseAddFormWatchersContainer', newCaseFormDialog).addClass('d-none');
    jQuery('#caseValuetoDisplay', newCaseFormDialog).text('0.00');
    jQuery('#recoveredValuetoDisplay', newCaseFormDialog).text('0.00');
    jQuery('#judgmentValuetoDisplay', newCaseFormDialog).text('0.00');
}

function resetTaskFormValues() {
    var taskDialog = jQuery("#taskDialog");
    if (jQuery("form#taskForm", taskDialog).length > 0) {
        jQuery("form#taskForm", taskDialog)[0].reset();
        jQuery("#selected_task_users", taskDialog).html('');
        enableDisableTaskUsersLookup();
    }
}

function setCompanyLawyerToFormAfterAutocomplete(record) {
    var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#lawyer_id', '#companyDetailedForm').val(record.id);
    jQuery('#lookupLawyer', '#companyDetailedForm').val(name);
}

function setNewCaseMultiOption(wrapper, setOption, objectName) {
    objectName = objectName || '';
    if (setOption.id && !jQuery('#' + setOption.name + setOption.id, wrapper).length) {
        wrapper.css('border', 'solid 1px #ddd');
        wrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" objectName="' + objectName + '" id="' + setOption.name + setOption.id + '">' + ((setOption.name == 'Legal_Case_Referrer' || setOption.name == 'Legal_Case_Opponent_Lawyers' || setOption.name == 'Legal_Case_External_Lawyers') ? ((setOption.isLawyer) ? '<i class="person-lawyer-icon">&nbsp;</i>' : '<i class="person-contact-icon">&nbsp;</i>') : '') + '<span id="' + setOption.id + '">' + setOption.value + '</span> </div>').append(jQuery('<input type="hidden" value="' + setOption.id + '" name="' + setOption.name + '[]" />')).append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right flex-end-item" tabindex="-1" onclick="unsetNewCaseMultiOption(this.parentNode);"><i class="fa-solid fa-xmark"></i></a>')));
    }
}

function setOpponentLawyersContactDataToBoxMultiOptionAfterAutocomplete(record) {
    setNewCaseMultiOption(jQuery('#selected_opponent_lawyers'), {
        id: record.id,
        value: (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName),
        name: 'Legal_Case_Opponent_Lawyers',
        isLawyer: (record.isLawyer == 'yes') ? true : false
    });
}

function setSelectedCompany(company) {
    var container = jQuery('#contactDetailsForm');
    var theWrapper = jQuery('#selected_companies', container);
    if (company.id && !jQuery('#contact_company' + company.id, theWrapper).length) {
        theWrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="contact_company' + company.id + '"><span id="' + company.id + '">' + company.name + '</span> </div>').append(jQuery('<input type="hidden" value="' + company.id + '" name="companies_contacts[]" />')).append(jQuery('<input value="x" type="button" class="btn btn-default btn-sm pull-right x-icon flex-end-item" onclick="jQuery(this.parentNode).remove();" />')));
    }
}

function setWatchersUsersDataToBoxMultiOptionAfterAutocomplete(record) {
    setNewCaseMultiOption(jQuery('#selected_watchers_users'), {
        id: record.id,
        value: record.firstName + ' ' + record.lastName,
        name: 'Legal_Case_Watchers_Users'
    });
}

function taskFormSubmit(container, id, callback) {
    id = id || false;
    var formData = new FormData(document.getElementById(jQuery("form#task-form", container).attr('id')));
    formData.append('description', tinymce.activeEditor.getContent());
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: getBaseURL() + 'tasks/' + (id ? 'edit/' + id : 'add'),
        success: function (response) {
            if (response.totalNotifications >= 1) {
                jQuery('#pendingNotifications').css('display', 'inline-block').text(response.totalNotifications);
            } else {
                jQuery('#pendingNotifications').html('');
            }
            jQuery('.inline-error', '#task-dialog').addClass('d-none');
            if (response.result) {
                if (jQuery('#notify-me-before-container', container).is(':visible')) {
                    loadUserLatestReminders('refresh');
                }
                if (!response.cloned && typeof taskCallBack === "function") {
                    taskCallBack();
                }
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('tasks');
                    pieCharts();
                }
                updateGetingStartedSteps('task');
                var msg = _lang.feedback_messages.addedNewTaskSuccessfully.sprintf(['<a href="' + getBaseURL() + 'tasks/view/' + response.id + '">' + response.task_code + '</a>']);
                pinesMessage({ ty: 'success', m: id ? _lang.feedback_messages.updatesSavedSuccessfully : msg });
                if (!response.cloned) {
                    jQuery('.modal', '#task-dialog').modal('hide');
                } else {
                    jQuery("#clone", container).val("no");
                    assignmentPerType(jQuery('#type', container).val(), 'task', container);
                }
                if (isFunction(callback)) {
                    callback(jQuery("#stage-id").val());
                }
            } else {
                displayValidationErrors(response.validationErrors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function taskLookup(taskLookupField, taskIdField, extraOptions) {
    var extraOptions = extraOptions || {}, taskIdField = taskIdField || false, subjectId = subjectId || false,
        caseIdFld = caseIdFld || false;
    var $taskIdField = ('string' == typeof taskIdField ? jQuery('#' + taskIdField) : taskIdField),
        $taskLookupField = ('string' == typeof taskLookupField ? jQuery('#' + taskLookupField) : taskLookupField);
    $taskLookupField.autocomplete({
        autoFocus: true, delay: 600, source: function (request, response) {
            if (extraOptions.filterCases !== null && extraOptions.filterCases == "yes" && jQuery(extraOptions.legalCaseLookupId).val() != "") {
                request.more_filters = {};
                request.more_filters.legal_case_id = jQuery(extraOptions.legalCaseLookupId).val();
            }
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'tasks/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: { id: -1, term: request.term }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.id + ': ' + item.title,
                                value: item.id,
                                record: item
                            }
                        }));
                    }
                }
            });
        }, response: function (event, ui) {
        }, minLength: 3,
        search: function (event, ui) {
            clearTaskUnbindCase();
        },
        select: function (event, ui) {
            if (ui.item.record.id > 0 && false != $taskIdField) {
                $taskIdField.val(ui.item.record.id);
                if (extraOptions.taskDescriptionId)
                    jQuery(extraOptions.taskDescriptionId, extraOptions.container).text(ui.item.record.description);
                if (!jQuery('input[name=action][value=StartTimer]').length && !jQuery('#case-log-time').length) {
                    if (ui.item.record.legal_case_id != null) {
                        jQuery(extraOptions.legalCaseLookupId, extraOptions.container).val(ui.item.record.legal_case_id);
                        jQuery(extraOptions.legalCaseLookup, extraOptions.container).val(ui.item.record.legal_case_id).attr('readonly', 'readonly').addClass('disabled').autocomplete("option", "disabled", true);
                        jQuery(extraOptions.legalCaseSubject, extraOptions.container).html(ui.item.record.caseSubject);
                        showTaskRelatedCaseSubject(extraOptions.legalCaseSubject, extraOptions.container, ui.item.record.category);
                        if (jQuery('input[name="timeStatus"][value=billable]').attr('checked')) {
                            jQuery('#client-div').removeClass('d-none');
                            if (ui.item.record.client_id) {
                                jQuery("#client-id").val(ui.item.record.client_id);
                                jQuery("#client-name").val(ui.item.record.client_name).attr("readonly", true);
                            } else {
                                jQuery("#client-name").val(ui.item.record.client_name).attr("readonly", false);
                            }
                        }
                    } else {
                        jQuery(extraOptions.legalCaseLookupId, extraOptions.container).val('');
                        jQuery(extraOptions.legalCaseLookup, extraOptions.container).val('').removeAttr('readonly').removeClass('disabled').autocomplete("option", "disabled", false);
                        jQuery(extraOptions.legalCaseSubject, extraOptions.container).html('');
                        jQuery('#client-div').addClass('d-none');
                        jQuery("#client-id").val('');
                        jQuery("#client-name").val('').attr("readonly", false);
                    }
                }
            }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

function unsetNewCaseMultiOption(wrapper) {
    jQuery(wrapper).remove();
}

function updateRemindersAfterActions(id) {
    id = id || false;
    jQuery('#reminder_' + id).remove();
    var pendingReminders = parseInt(jQuery('#pendingReminders').text());
    pendingReminders--;
    if (jQuery('#my-dashboard').length > 0) {
        loadDashboardData('reminders');
    }
    if (pendingReminders >= 1) {
        jQuery('#pendingReminders').css('display', 'inline-block').text(pendingReminders);
    } else {
        jQuery('#pendingReminders').text('').css("background-color", "").css('display', 'none');
    }
    jQuery('h3.popover-header', jQuery('#remindersListLatestContainer').parent().parent()).html(_lang.remindersCounter.sprintf([pendingReminders]) + '<a class="pull-right" href="javascript:;" onclick="jQuery(\'#pendingReminders\').parent().popover(\'hide\');if(remindersRefreshInterval!=\'\'){refreshRemindersIntervalId = window.setInterval(function(){loadUserLatestReminders(\'refresh\');}, remindersRefreshInterval);}"><sub style="color: #fff;">x</sub></a>');
}

function userLookup(field, id, resultHandler, active, excludedProviderGroupUsersFlag) {
    id = id || false;
    resultHandler = resultHandler || false;
    excludedProviderGroupUsersFlag = excludedProviderGroupUsersFlag || false;
    var excludedProviderGroupUsers = '';
    if (excludedProviderGroupUsersFlag === true) {
        excludedProviderGroupUsers = jQuery('#provider_group_id').val();
    }
    var userLookupURL = getBaseURL() + 'users/autocomplete/' + active;
    field = ('string' == typeof field) ? jQuery('#' + field) : field;
    jQuery(field).autocomplete({
        autoFocus: true, delay: 600, source: function (request, response) {
            request.term = request.term.trim();
            if (excludedProviderGroupUsersFlag) {
                request.more_filters = {};
                request.more_filters.excludedProviderGroupUsers = excludedProviderGroupUsers;
            }
            jQuery.ajax({
                url: userLookupURL,
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched_for.sprintf([request.term]),
                            value: '',
                            record: { id: -1, term: request.term }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            var activeStatus = '';
                            if (item.status == 'Inactive')
                                activeStatus = ' (' + _lang.custom[item.status] + ')';
                            return {
                                label: item.firstName + ' ' + item.lastName + activeStatus,
                                value: item.firstName + ' ' + item.lastName,
                                record: item
                            }
                        }));
                    }
                }
            });
        }, response: function (event, ui) {
        }, minLength: 1, select: function (event, ui) {
            if (ui.item.record.id > 0 && id) {
                if (id)
                    jQuery('#' + id).val(ui.item.record.id);
                if (resultHandler)
                    resultHandler(ui.item.record);
            } else if (ui.item.record.id == -1) {
            }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

function loadLatestNotifications(load_type) {
    var typereturn = '';
    if (load_type == 'refresh') {
        typeReturn = 'counter';
    } else {
        typeReturn = 'list';
    }
    jQuery.ajax({
        url: getBaseURL() + 'notifications/get_pending_list',
        dataType: "json",
        type: 'POST',
        data: {
            typeReturn: typeReturn
        },
        success: function (response) {
            if (load_type === 'refresh') {
                if (response.pending_notifications >= 1) {
                    jQuery('#pendingNotifications').css('display', 'inline-block').text(response.pending_notifications);
                }
            } else if (load_type === 'load_all') {
                clearInterval(refreshNotificationsIntervalId);
                jQuery('h3.popover-header', jQuery('#notificationsContainer').parent().parent()).html(_lang.notificationsCounter.sprintf([response.totalRows]) + notificationsPopoverIconClose);
                jQuery('#pendingNotifications').html('');
                jQuery('#notificationsListLatest').removeClass('loading').html(response.html);
                if (jQuery(element).attr("objectType") != 'expenses') {
                    jQuery('a.notify-links', '#notificationsContainer').each(function (index, element) {
                        jQuery(element).tooltip({
                            show: {
                                effect: "highlight",
                                duration: 1100
                            },
                            items: "[title], [objectType], [objectID]",
                            content: function () {
                                var objectType = jQuery(element).attr("objectType");
                                jQuery.ajax({
                                    url: getBaseURL() + objectType + '/autocomplete',
                                    dataType: "json",
                                    data: { term: jQuery(element).attr("objectID") }, error: defaultAjaxJSONErrorsHandler,
                                    success: function (data) {
                                        if (data.length !== 0) {
                                            if (objectType == 'cases') {
                                                jQuery(element).attr("title", data[0].fullSubject);
                                            } else {
                                                jQuery(element).attr("title", data[0].description);
                                            }
                                            return jQuery(element).attr("title");
                                        }
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            },
                            track: false
                        });
                    });
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function dismissNotification(id) {
    jQuery.ajax({
        url: getBaseURL() + 'notifications/dismiss',
        type: 'POST',
        dataType: 'JSON',
        data: {
            notificationId: id
        },
        success: function (response) {
            switch (response.status) {
                case 202:
                    updateNotificationsAfterActions(id);
                    break;
                case 101:
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.updatesFailed });
                    break;
                default:
                    break;
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function updateNotificationsAfterActions(id) {
    id = id || false;
    jQuery('#notification_' + id).remove();

    const notificationHeaderText = jQuery('#notificationsContainer').parent().prev().text(); // Get the popover-header for notifications dropdown
    var pendingNotifications = notificationHeaderText.match(/\d+/)[0]; // Retrieve digit from Notifications text

    pendingNotifications--;
    if (pendingNotifications >= 1) {
        jQuery('#pendingNotifications').css('display', 'none').text(pendingNotifications);
    } else {
        jQuery('#pendingNotifications').text('').css("background-color", "").css('display', 'none');
    }
    jQuery('h3.popover-header', jQuery('#notificationsContainer').parent().parent().parent()).html(_lang.notificationsCounter.sprintf([pendingNotifications]) + '<a class="pull-right" href="javascript:;" onclick="jQuery(\'#pendingNotifications\').parent().popover(\'hide\');if(notificationRefreshInterval!=\'\'){refreshNotificationsIntervalId = window.setInterval(function(){loadLatestNotifications(\'refresh\');}, notificationRefreshInterval);}"><sub style="color: #fff;">x</sub></a>');
}

function notificationSendEmailClickPopup(that, container) {
    container = container || '';
    notificationSendEmailClick(that, container, '#notificationSendEmailId');
    notificationSendEmailClick(that, container, '#send_notifications_email');
}

function notificationSendEmailClick(that, container, id) {
    id = id || '#send_notifications_email';
    jQuery(id, container).val(jQuery(that).is(':checked') ? 'yes' : '');
}

function resetUserActivityLogForm() {
    jQuery("[aria-describedby='userActivityLogDialog']").remove();
    jQuery("#userActivityLogDialog").remove();
}

function logActivityDialog(id, LogElement, caseLogTime, readOnlyFlag, reloadFlag, afterLockBulkAddDates) {
    id = id || false;
    LogElement = LogElement || {};

    if (LogElement) {
        if (LogElement.taskDescription) {
            if (checkIfStringIsURIEncoded(LogElement.taskDescription)) {
                LogElement.taskDescription = decodeURIComponent(LogElement.taskDescription);
            }
            LogElement.taskDescription = replaceHtmlCharacter(LogElement.taskDescription);
        }
        if (LogElement.legalCaseSubject) {
            if (checkIfStringIsURIEncoded(LogElement.legalCaseSubject)) {
                LogElement.legalCaseSubject = decodeURIComponent(LogElement.legalCaseSubject);
            }
            LogElement.legalCaseSubject = replaceHtmlCharacter(LogElement.legalCaseSubject);
        }
    }

    var logActivityDialogUrl = id ? getBaseURL().concat('time_tracking/edit/').concat(id) : getBaseURL().concat('time_tracking/add');
    var data = {};
    jQuery.ajax({
        url: logActivityDialogUrl,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                jQuery("#noty_topRight_layout_container").remove();
                var LogTimeDialogId = "time-log-dialog";
                jQuery('<div id="time-log-dialog"></div>').appendTo("body");
                var container = jQuery("#" + LogTimeDialogId);
                container.html(response.html);
                initializeModalSize(container, 0.4, 'auto');
                commonModalDialogEvents(container);
                jQuery('#log-options-menu', container).selectpicker();
                jQuery('#time-status', container).selectpicker();
                jQuery('#time-internal-status', container).selectpicker();
                jQuery('#rate-time-log', container).selectpicker();
                jQuery('#entity-time-log', container).selectpicker();
                jQuery('#task_lookup', container).val(jQuery('#task_lookup', container).val());
                jQuery('#time-logs-comment', container).val(replaceHtmlCharacter((jQuery('#time-logs-comment', container).val())));
                jQuery('#case_lookup', container).val(replaceHtmlCharacter((jQuery('#case_lookup', container).val())));

                if (afterLockBulkAddDates) {
                    jQuery("#log-date", container).val(afterLockBulkAddDates.last_date);
                    jQuery("#repeat-until-date-input", container).val(afterLockBulkAddDates.log_date);
                    jQuery("#is-repeat", container).prop( "checked", true );
                    timeLogsDialog.repeatInput("#is-repeat");
                }

                lookUpCases(jQuery('#case_lookup', container), jQuery('#legal_case_id', container), 'legal_case_id', container, false, {
                    'callback': timeLogsDialog.onCaseLookup,
                    "onEraseLookup": timeLogsDialog.onEraseCaseLookup
                });
                lookUpTasks({
                    'lookupField': jQuery('#task_lookup', container),
                    'hiddenId': jQuery('#task_id', container),
                    'errorDiv': 'task_id'
                }, container, {
                    'callback': timeLogsDialog.onTaskLookup,
                    "onEraseLookup": timeLogsDialog.onErasetaskLookup
                });
                lookUpClients({
                    'lookupField': jQuery('#client-name', container),
                    'hiddenId': jQuery('#client-id', container),
                    'errorDiv': 'client-lookup-wrapper'
                }, container);
                //to not get rate on edit
                getSystemRateValue = false;
                lookUpUsers(jQuery('#user-lookup', container), jQuery('#user-id', container), 'user_id', jQuery('#user-lookup-wrapper', container), container, { 'resize': 'false' }, {
                    'callback': timeLogsDialog.onUserLookupSelect()
                });
                setDatePicker('#log-date-wrapper', container);
                setDatePicker('#to-date-picker-wrapper', container);
                if (!id) RelateTimeLogTo(container, id, LogElement, caseLogTime, readOnlyFlag, response.defaultNewTimeLogStatus, 'no');
                var minDate = jQuery("#log-date-wrapper", container).data('datepicker').getFormattedDate('yyyy-mm-dd');
                jQuery("#to-date-picker-wrapper", container).bootstrapDP("setStartDate", minDate ? minDate : -Infinity);
                jQuery("#log-date-wrapper", container).bootstrapDP().on('changeDate', function (e) {
                    jQuery("#to-date-picker-wrapper", container).bootstrapDP("setStartDate", timeActions.get_next_date(e.date));
                    jQuery("#to-date-picker-wrapper", container).bootstrapDP("setDate", '');
                });
                jQuery("#effectiveEffortHour", container).keyup(function () {
                    jQuery("#splited-time-value", container).html("");
                    jQuery("#split-none-billable-time-value", container).val("");
                    jQuery("#split-billable-time-value", container).val("");
                });
                jQuery("#add-timer-dialog-submit").on('click', function () {
                    timeLogsDialog.logTimeSave(jQuery("#form-action", container).val(), false);
                    if (reloadFlag) {
                        setTimeout(function () {
                            window.location = window.location.href;
                        }, 1000);
                    }
                });
                jQuery("#add-timer-duplicate-button").on('click', function () {
                    timeLogsDialog.logTimeSave(jQuery("#form-action", container).val(), true);
                    if (reloadFlag) {
                        setTimeout(function () {
                            window.location = window.location.href;
                        }, 1000);
                    }
                });
                jQuery("#time-status", container).change(function () {
                    jQuery.ajax({
                        url: getBaseURL().concat('time_types/get_record_by_id/').concat(jQuery(this).val()),
                        data: data,
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            jQuery('#loader-global').show();
                        },
                        success: function (response) {
                            if (response.status) {
                                if (response.data.default_comment && response.data.default_time_effort) {
                                    changeTimeLogsDefaultValue(response.data.default_time_effort, 'replace_time', "#effectiveEffortHour", container,
                                        function () {
                                            changeTimeLogsDefaultValue(response.data.default_comment, 'replace_comment', "#time-logs-comment", container);
                                        }
                                    );
                                } else {
                                    changeTimeLogsDefaultValue(response.data.default_comment, 'replace_comment', "#time-logs-comment", container);
                                    changeTimeLogsDefaultValue(response.data.default_time_effort, 'replace_time', "#effectiveEffortHour", container);
                                }
                            }
                        }, complete: function () {
                            jQuery('#loader-global').hide();
                        },
                        error: defaultAjaxJSONErrorsHandler
                    });
                    jQuery("#time-logs-comment", container).val();
                });
                jQuery('.effort-time-tooltip', container).tooltipster();
                jQuery('.billable-check-box-message', container).tooltipster({
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
                jQuery('.repeat-check-box-message', container).tooltipster();
                jQuery("#log-options-menu", container).on("logType:toggle", function (event) {
                    var logType = jQuery(this);
                    logType.is(".task-type") ? logType.trigger("logType:matter") : logType.trigger("logType:task");
                }).on("logType:task", function (event) {
                    jQuery(this).removeClass("matter-type").addClass("task-type");
                    timeLogsDialog.taskLogTypeSelected(event, container);
                }).on("logType:matter", function (event) {
                    jQuery(this).removeClass("task-type").addClass("matter-type");
                    timeLogsDialog.matterLogTypeSelected(event, container);
                });
                timeLogsDialog.onChangeRate("#rate-time-log", 'no', jQuery('#client-id', container).val() ? true : "noClient");
                disableAutocomplete(container);
                if (response.has_billing_options) {
                    helpers.readonlyFieldsEnable(container);
                    jQuery("#time-status", container).prop('readonly', false);
                    jQuery("#time-internal-status", container).prop('readonly', false);
                    jQuery("[data-id='time-status']").prop('disabled', false);
                    jQuery("#time-logs-comment", container).prop('readonly', false);
                    jQuery("#log-date-wrapper", container).bootstrapDP('remove').prop('readonly', true);
                    jQuery("#related-entity", container).addClass("d-none");
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function changeTimeLogsDefaultValue(responseVal, langlableTitleMessage, elementId, container, returnFunction) {
    returnFunction = returnFunction || false;
    if (jQuery(elementId, container).val() && responseVal) {
        confirmationDialog(langlableTitleMessage, {
            resultHandler: function () {
                jQuery(elementId, container).val(responseVal);
                if (returnFunction) returnFunction();
            }
        });
    }
}

function onchangeOperatorsFiltersDate(that, groupContainer, hijriEnable) {
    hijriEnable = hijriEnable || false;
    var container = jQuery('#' + groupContainer);
    var operator = jQuery(that).val();
    var endDate = jQuery('.end-date-filter', container);
    var endDateIcon = jQuery('.end-date-icon-filter', container);
    var startDate = container.find("input[type='text']:first");
    var startDateOperator = jQuery('.start-date-operator', container);
    startDateOperator.val(operator);
    if (operator == 'between') {
        startDateOperator.val('cas_gte');
        endDate.val('').removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'cast_between') {
        startDateOperator.val('cast_gte');
        endDate.val('').removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'yd') {
        startOf = moment().locale('en').subtract(1, 'days').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0) {
            startOf = gregorianToHijri(startOf);
        }
        startDateOperator.val('cast_eq');
        startDate.val(startOf).removeClass('d-none');
        endDate.val('').addClass('d-none');
        endDateIcon.addClass('d-none');
        jQuery('#arrivalDateAnd', container).addClass('d-none');
    } else if (operator === 'tday') {
        startOf = moment().locale('en').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = gregorianToHijri(startOf);
        }
        startDateOperator.val('cast_eq');
        startDate.val(startOf).removeClass('d-none');
        endDate.val('').addClass('d-none');
        endDateIcon.addClass('d-none');
        jQuery('#arrivalDateAnd', container).addClass('d-none');
    } else if (operator === 'tomorrow') {
        startOf = moment().locale('en').add(1, 'days').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = gregorianToHijri(startOf);
        }
        startDateOperator.val('cast_eq');
        startDate.val(startOf).removeClass('d-none');
        endDate.val('').addClass('d-none');
        endDateIcon.addClass('d-none');
        jQuery('#arrivalDateAnd', container).addClass('d-none');
    } else if (operator === 'tw') {
        startOf = moment().startOf('week').toDate().format('Y-m-d');
        endOf = moment().endOf('week').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = gregorianToHijri(startOf);
            endOf = gregorianToHijri(endOf);
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'lw') {
        startOf = moment().subtract(1, 'weeks').startOf('week').toDate().format('Y-m-d');
        endOf = moment().subtract(1, 'weeks').endOf('week').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = gregorianToHijri(startOf);
            endOf = gregorianToHijri(endOf);
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'nw') {
        startOf = moment().add(1, 'weeks').startOf('week').toDate().format('Y-m-d');
        endOf = moment().add(1, 'weeks').endOf('week').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = gregorianToHijri(startOf);
            endOf = gregorianToHijri(endOf);
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'tm') {
        startOf = moment().startOf('month').toDate().format('Y-m-d');
        endOf = moment().endOf('month').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = moment().locale('en').startOf('iMonth').format('iYYYY-iMM-iDD');
            endOf = moment().locale('en').endOf('iMonth').format('iYYYY-iMM-iDD');
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'lm') {
        startOf = moment().subtract(1, 'months').startOf('month').toDate().format('Y-m-d');
        endOf = moment().subtract(1, 'months').endOf('month').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = moment().locale('en').subtract(1, 'iMonth').startOf('iMonth').format('iYYYY-iMM-iDD');
            endOf = moment().locale('en').subtract(1, 'iMonth').endOf('iMonth').format('iYYYY-iMM-iDD');
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'nm') {
        startOf = moment().add(1, 'months').startOf('month').toDate().format('Y-m-d');
        endOf = moment().add(1, 'months').endOf('month').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = moment().locale('en').add(1, 'iMonth').startOf('iMonth').format('iYYYY-iMM-iDD');
            endOf = moment().locale('en').add(1, 'iMonth').endOf('iMonth').format('iYYYY-iMM-iDD');
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'tq') {
        startOf = moment().startOf('quarter').toDate().format('Y-m-d');
        endOf = moment().endOf('quarter').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = getCurrentYearQuarterInHijri().startOf;
            endOf = getCurrentYearQuarterInHijri().endOf;
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'lq') {
        startOf = moment().subtract(1, 'quarters').startOf('quarter').toDate().format('Y-m-d');
        endOf = moment().subtract(1, 'quarters').endOf('quarter').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = getPreviousYearQuarterInHijri().startOf;
            endOf = getPreviousYearQuarterInHijri().endOf;
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'ty') {
        startOf = moment().startOf('year').toDate().format('Y-m-d');
        endOf = moment().endOf('year').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = moment().locale('en').startOf('iYear').format('iYYYY-iMM-iDD');
            endOf = moment().locale('en').endOf('iYear').format('iYYYY-iMM-iDD');
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else if (operator === 'ly') {
        startOf = moment().subtract(1, 'years').startOf('year').toDate().format('Y-m-d');
        endOf = moment().subtract(1, 'years').endOf('year').toDate().format('Y-m-d');
        if (jQuery('.hijri-date-picker', container).length > 0 && hijriEnable) {
            startOf = moment().locale('en').subtract(1, 'iYear').startOf('iYear').format('iYYYY-iMM-iDD');
            endOf = moment().locale('en').subtract(1, 'iYear').endOf('iYear').format('iYYYY-iMM-iDD');
        }
        startDateOperator.val('cast_gte');
        startDate.val(startOf).removeClass('d-none');
        endDate.val(endOf).removeClass('d-none');
        endDateIcon.removeClass('d-none');
        jQuery('#arrivalDateAnd', container).removeClass('d-none');
    } else {
        endDate.val('').addClass('d-none');
        endDateIcon.addClass('d-none');
        jQuery('#arrivalDateAnd', container).addClass('d-none');
    }
}

function RelateTimeLogTo(container, id, LogElement, caseLogTime, readOnlyFlag, defaultNewTimeLogStatus, _getSystemRate) {
    LogElement = LogElement || {};
    _getSystemRate = _getSystemRate || true;
    defaultNewTimeLogStatus = defaultNewTimeLogStatus || true;
    var selectedOption = (LogElement.hasOwnProperty("task_id") && LogElement.task_id) ? 'task' : 'case';
    var logOptionsMenu = jQuery("#log-options-menu", container);
    switch (selectedOption) {
        case 'task':
            logOptionsMenu.val("task").selectpicker('refresh');
            timeLogsDialog.changeTimeLogAction("#log-options-menu", _getSystemRate);
            timeLogsDialog.setLookupValue('task', LogElement, defaultNewTimeLogStatus);
            break;
        case 'case':
            logOptionsMenu.val("case").selectpicker('refresh');
            timeLogsDialog.changeTimeLogAction("#log-options-menu", _getSystemRate);
            timeLogsDialog.setLookupValue('case', LogElement, defaultNewTimeLogStatus);
            break;
        default:
            break;
    }
}

if (typeof String.prototype.parseDuration !== "function")
    String.prototype.parseDuration = function (e) {
        if (/(^\dw|^\dd|^\d{1,2}h|^\d{1,2}m)/i.test(this) && /\d[wdhm]$/.test(this))
            return this.toLowerCase().replace(/\s/g, '').replace(/w/g, '*' + $_systemDefaults.businessWeekEquals + 'd').replace(/d/g, '*' + $_systemDefaults.businessDayEquals + ' ').replace(/h/g, '/1 ').replace(/m/g, '/60 ').replace(/\s/g, '+') + '0';
        if (this.length > 0) {
            var roundedNumber = Math.floor(this);
            var roundedNumberLength = roundedNumber.toString().length;
            var net = String(Math.floor(this)) + 'h ' + String(Math.floor((((this * Math.pow(10, roundedNumberLength) - roundedNumber * Math.pow(10, roundedNumberLength)) / Math.pow(10, roundedNumberLength)) * 60))) + 'm';
            if (e) {
                e.addClass('label label-success').text(net);
            } else {
                return net;
            }
        } else {
            if (e) {
                e.removeClass('label label-success').text('');
            }
            return '';
        }
        return false;
    }

function taskLocationLookup(lookupField) {
    $lookupField = jQuery('#' + lookupField);
    $lookupField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'tasks/location_autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: { id: -1, term: request.term }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return { label: item.location, value: item.location, record: item }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 2,
        select: function (event, ui) {
            for (i in ui.item.record)
                if (ui.item.record.id > 0) {
                    jQuery('#task_location_id').val(ui.item.record.id);
                }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

function isLayoutRTL() {
    return jQuery('body').css('direction') == 'rtl';
}

function copyAddressFromLookup(container) {
    container = ('string' === typeof container) ? jQuery('#' + container) : jQuery(container);
    jQuery('#copyAddressFromType', container).change(function () {
        jQuery("#copyAddressFromLookup", container).val('');
    });
    jQuery("#copyAddressFromLookup", container).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery("select#copyAddressFromType", container).val();
            jQuery.ajax({
                url: getBaseURL() + lookupType + '/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched,
                            value: '',
                            record: { id: -1, term: request.term }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            if (lookupType == 'contacts') {
                                return {
                                    label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    record: item
                                }
                            } else if (lookupType == 'companies') {
                                return {
                                    label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                    value: item.name,
                                    record: item
                                }
                            }
                        }));
                    }
                }
            });
        },
        open: function (event, ui) {
            jQuery('ul', container).css('width', jQuery('.lookup.ui-autocomplete-input', container).parent().width());
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                if (jQuery("select#copyAddressFromType", container).val() == 'contacts') {
                    copyAddressFromContact(ui.item.record, container);
                } else {
                    copyAddressFromCompany(ui.item.record, container);
                }
            }
        }
    });
    jQuery("#copyAddressFromLookup", container).autocomplete("option", "appendTo", jQuery("#copyAddressFromContainer", container));
}

function copyAddressFromContact(record, container, isDialog) {
    isDialog = isDialog || false;
    jQuery("#copyAddressFromLookup", container).val('');
    jQuery('#address1', container).focus();
    jQuery('#address1', container).val(record.address1);
    jQuery('#address2', container).val(record.address2);
    jQuery('#city', container).val(record.city);
    if (isDialog) {
        jQuery('#country_id', container).val(record.country_id).selectpicker('refresh');
    } else {
        jQuery('#country_id', container).val(record.country_id).trigger("chosen:updated");
    }
    jQuery('#state', container).val(record.state);
    jQuery('#zip', container).val(record.zip);
    jQuery('#street_name', container).val(record.street_name);
    jQuery('#additional_street_name', container).val(record.additional_street_name);
    jQuery('#building_number', container).val(record.building_number);
    jQuery('#address_additional_number', container).val(record.address_additional_number);
    jQuery('#district_neighborhood', container).val(record.district_neighborhood);
    setTimeout(function () {
        toggleCopyAddressFrom(container);
    }, 100);
}

function checkLookupValidity(targetField, hiddenField) {
    setTimeout(function () {
        if (!disableBlurEventToCheckLookupValidity && hiddenField.val() === '' && targetField.val() !== '') {
            pinesMessage({ ty: 'error', m: _lang.checkLookupValidity.sprintf([targetField.val()]), d: 5000 });
            targetField.val('').focus();
        }
        if (targetField.val() === '') {
            hiddenField.val('');
        } else {
            targetField.validationEngine('hide');
        }
    }, 100);
}

function resetFormFields() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
    var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);
    if (msie > 0 || isIE11) {
        window.location = window.location.href;
    } else { // If another browser, return 0
        jQuery('#resetBtnHidden').click();
        jQuery('.data-filter').hide();
        jQuery('.checkbox-inline').attr('style', 'font-weight: normal !important;');
        jQuery('.chk-filter').attr('checked', false);
        if (jQuery('#gridFiltersList').length) {
            if (jQuery('#gridFiltersList').val().length === 0) {
                if (typeof default_active_filters !== 'undefined') {
                    jQuery.each(default_active_filters, function (index, element) {
                        jQuery('.chk-filter', '#' + element.id + 'Container').attr('checked', true);
                        jQuery('.data-filter', '#' + element.id + 'Container').show();
                    });
                }
            }
        }
        jQuery('#resetBtnHiddenV2').click();
        if (jQuery('.empty-value-field').length) jQuery('.empty-value-field').show();
        jQuery('.sf-value').each(function () {
            if (typeof jQuery(this).attr('data-field') !== typeof undefined && jQuery(this).attr('data-field') === 'hidden-lookup-input') {
                jQuery('.sf-value').attr('data-field', 'hidden-lookup-input').val('');
            }
        });
        jQuery('.multi-select', '#filtersFormWrapper').each(function (index, element) {
            jQuery('option', element).each(function () {
                if (!jQuery(this).is(':disabled')) {//if an option is diabled => then the value won't be emptied
                    jQuery(element).val('').trigger("chosen:updated");
                } else {//remove the default select field
                    jQuery('li.search-field', '#' + jQuery(element).attr('id') + '_chosen').remove();
                }
                return false;
            });
        });
        if (jQuery('.start-date-operator', '#filtersFormWrapper').length) {
            jQuery('.start-date-operator', '#filtersFormWrapper').val('cast_eq');
        }
    }
}

function clearCaseHiddenFieldsValues(formId) {
    jQuery('#contact_company_id', formId).val('');
    jQuery('#caseCompanyId', formId).val('');
    jQuery('#caseContactId', formId).val('');
    jQuery('#referredBy', formId).val('');
    jQuery('#requestedBy', formId).val('');
    jQuery('#opponent_member_id', formId).val('');
    jQuery('#opponentLookup', formId).val('');
}

function subscribe() {
    jQuery.ajax({
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        dataType: 'JSON',
        success: function (response) {
            if (response) {
                jQuery("#loader-global").hide();
                if (response.error) {
                    pinesMessage({ ty: 'information', m: response.error });
                } else {
                    if (!jQuery('#subscribe-temp-form').length) {
                        $subscribeTempContainer = jQuery('<div id="subscribe-temp-form"></div>').addClass("d-none").appendTo("body");
                    }
                    $subscribeTempContainer.html(response.html);
                    jQuery('form#subscribe-temp-form', $subscribeTempContainer).submit();
                    $subscribeTempContainer.remove();
                }
            }
        }, error: defaultAjaxJSONErrorsHandler,
        url: getBaseURL() + 'subscription/subscribe'
    });
}

function uploadInProgress(submitBtnName, inputFileId) {
    jQuery('[name="' + submitBtnName + '"]').click(function (e) {
        var form = jQuery(this).parents('form:first');
        if (jQuery('#' + inputFileId).val() !== '' && form.validationEngine("validate")) {
            jQuery("#loader-global").show().html('<div class="alert alert-warning" style="opacity: 1; display: block; cursor: auto; width: 300px; margin-left: -29%; margin-top: 25px;height:50px;box-shadow:0px 5px 7px 4px #cccccc;"><a href="#" class="close" data-dismiss="meeting">&times;</a><h4 class="global-notify-title">' + _lang.uploadInProgress + '</h4></div>');
        }
        return true;
    });
}

function collapse(id, collapse_content_selector, showHide, speed, downClass, rightClass, chevronAtTheEnd, chevronAtTheFirst) {
    showHide = showHide || false;
    chevronAtTheEnd = chevronAtTheEnd || false;
    chevronAtTheFirst = chevronAtTheFirst || false;
    speed = speed || "fast";
    downClass = downClass || 'fa-solid fa-angle-down';
    rightClass = rightClass || 'fa-solid fa-angle-right';
    if (showHide) {
        jQuery("#" + collapse_content_selector).toggleClass('d-none');
        if (jQuery("#" + collapse_content_selector).hasClass('d-none')) {
            tgleImgTitle = _lang.showDetails;
        } else {
            tgleImgTitle = _lang.hideDetails;
        }
        jQuery('#' + id).find("img").attr("title", tgleImgTitle);
    } else {
        jQuery("#" + collapse_content_selector).toggleClass('d-none');
        if (jQuery("#" + collapse_content_selector).hasClass('d-none')) {
            old_class = downClass;
            new_class = rightClass;
        } else {
            old_class = rightClass;
            new_class = downClass;
        }
        if (!chevronAtTheEnd) {
            if (!chevronAtTheFirst) {
                jQuery('#' + id).find("svg").removeClass(old_class).addClass(new_class);
                jQuery('#' + id).find("i").removeClass(old_class).addClass(new_class);
            } else {
                jQuery('#' + id).find("svg:first").removeClass(old_class).addClass(new_class);
            }
        } else {
            jQuery('#' + id).find("svg:last-child").removeClass(old_class).addClass(new_class);
            jQuery('#' + id).find("i:last-child").removeClass(old_class).addClass(new_class);
        }
    }
}

function universalSearchFocus(jqObj, keyCode, clickAction) {
    var searchRelatedObjectView = jQuery("#search-related-object-view");
    var universalSearchSlide = jQuery("#universal-search-slide");
    searchRelatedObjectView.html("<div class=\"col-md-12\">\n" +
        "        <h4 class=\"mb-15 no-margin-top\"><strong>" + recentVisitedLang + "</strong></h4>\n" +
        "    </div><div id=\"universal-loading\"></div>");
    jQuery.ajax({
        beforeSend: function () {
        },
        data: {},
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#universal-loading').show();
            universalSearchSlide.animate({
                width: "400px"
            }, {
                duration: 100,
                specialEasing: {
                    width: 'linear'
                }
            });
            jQuery('body').addClass("hidden-overflow");
        },
        success: function (response) {
            if (response.status) {
                setTimeout(function () {
                    searchRelatedObjectView.html(response.html);
                }, 2000);
                universalSearchSlide.removeClass('d-none');
                jQuery("#universal-search-slide-wrapper").click(function () {
                    jQuery("#universal-search-slide-wrapper").addClass("d-none");
                    universalSearchSlide.animate({
                        width: "0px"
                    }, {
                        duration: 100,
                        specialEasing: {
                            width: 'linear'
                        }
                    });
                    jQuery('body').removeClass("hidden-overflow");
                });
                jQuery('.tooltip-title').tooltipster();
                jQuery("#universal-search-input").focus();
            }
        },
        complete: function () {
            jQuery('#universal-loading').show();
        },
        error: defaultAjaxJSONErrorsHandler,
        type: 'POST',
        url: getBaseURL() + 'home/get_recent_search_data'
    });
    jQuery("#universal-search-slide-wrapper").removeClass('d-none');
}

function universalSearch(jqObj, keyCode, clickAction) {
    if (jQuery('#search-input').hasClass('d-none')) {
        jQuery('#search-input').removeClass('d-none');
    } else {
        clickAction = clickAction || false;
        keyCode = keyCode || false;
        var term = jqObj.val();
        var pinesMsgFired = jQuery('.feedback-message-type-error', '#feedback-message-container').length;
        if (clickAction) {
            if (term.length < 2) {
                if (!pinesMsgFired) {
                    pinesMessageV2({ ty: 'error', m: _lang.quickSearchMinLength });
                    return;
                }
            } else {
                quick_search(String(term).trim());
            }
        } else {
            if (keyCode === 13) {
                if (term.length < 2) {
                    if (!pinesMsgFired) {
                        pinesMessageV2({ ty: 'error', m: _lang.quickSearchMinLength });
                        return;
                    }
                } else {
                    quick_search(String(term).trim());
                }
            }
        }
    }
}

function closeFormattingField(targetField, fieldToDisplay, event, visualizeDecimals) {
    visualizeDecimals = visualizeDecimals || false;
    fieldToDisplay.addClass('d-none');
    targetField.removeClass('d-none').focus();
    if (visualizeDecimals) {
        visualizeDecimals.addClass('d-none');
    }
    event.preventDefault();
}

function numberFormattingInput(targetField, fieldToDisplay, visualizeDecimalsContainer, visualizeDecimalsCheckbox) {
    visualizeDecimalsContainer = visualizeDecimalsContainer || false;
    visualizeDecimalsCheckbox = visualizeDecimalsCheckbox || false;
    var fieldValue = eval(targetField.attr('id') + 'Value').rawValue;
    if (fieldValue == '') {
        fieldValue = 0;
    }
    targetField.addClass('d-none');
    var fieldValueToDisplay = '';
    if (visualizeDecimalsContainer) {
        visualizeDecimalsContainer.removeClass('d-none');
        //allow two numbers only after the decimal
        fieldValueToDisplay = fieldValue.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
        if (visualizeDecimalsCheckbox.attr('checked') != 'checked') {
            fieldValueToDisplay = parseInt(fieldValueToDisplay);
        }
    } else {
        fieldValueToDisplay = fieldValue.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
    }
    fieldToDisplay.html('');
    fieldToDisplay.html(' <span id="capital-text">' + fieldValueToDisplay + '</span>' + '&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="' + fieldToDisplay.attr('onclick') + '">' + _lang.edit + '</a>').removeClass('d-none');
    new AutoNumeric('#capital-text', 'float');
}

function companyCapitalVisualizeDecimalsClick(field, companyId, container) {
    var displayDecimals = field.attr('checked') == 'checked' ? 'yes' : 'no';
    field.val(displayDecimals);
    if (companyId) { // edit form
        jQuery.ajax({
            beforeSend: function () {
            },
            data: { visualizeDecimals: displayDecimals, companyId: companyId },
            dataType: 'JSON',
            success: function (response) {
                if (!response.result) {
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.updatesFailed });
                }
                displayHideCapitalDecimals(displayDecimals, container);
            }, error: defaultAjaxJSONErrorsHandler,
            type: 'POST',
            url: getBaseURL() + 'companies/capital_visualize_decimals'
        });
    } else {// add form
        displayHideCapitalDecimals(displayDecimals, container);
    }
}

function displayHideCapitalDecimals(displayDecimals, container) {
    var capital = jQuery('#capital', container).val();
    capital = capital.replace(/,(?=[\d,]*\.\d{2}\b)/g, '');
    var fieldValueToDisplay = number_format(capital, '2', '.', ',');
    if (displayDecimals != 'yes') {
        fieldValueToDisplay = fieldValueToDisplay.replace(/,(?=[\d,]*\.\d{2}\b)/g, '');
        fieldValueToDisplay = parseInt(fieldValueToDisplay);
        fieldValueToDisplay = fieldValueToDisplay.toLocaleString();
    }
    var containerToDisplay = jQuery('#capitaltoDisplay', container);
    containerToDisplay.html(' <span id="capital-text">' + fieldValueToDisplay + '</span>' + '&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="' + containerToDisplay.attr('onclick') + '">' + _lang.edit + '</a>').removeClass('d-none');
}

function preventFormPropagation(event) {
    if (undefined != event) {
        event.preventDefault();
    }
}

function topStickyRelocate(options) {
    var window_top = jQuery(window).scrollTop();
    var div_top = jQuery('#' + options['affectedDivs'].stickyAnchor).offset().top - jQuery('#header_div').height();
    var toStick = jQuery('#' + options['affectedDivs'].toStick);
    if (window_top > div_top) {
        if (jQuery.isEmptyObject(options['cssRules']) === false)
            toStick.css(options['cssRules']['fixedObject']);
        else
            toStick.addClass('stick');
    } else {
        if (jQuery.isEmptyObject(options['cssRules']) === false)
            toStick.css(options['cssRules']['relativeObject']);
        else
            toStick.removeClass('stick');
    }
}

function showHideGridContent(response, ContainerID) {
    ContainerID = ('string' == typeof ContainerID) ? jQuery('#' + ContainerID) : ContainerID;
    if (response.totalRows == 0) {
        jQuery('.k-grid-header', ContainerID).hide();
        jQuery('.k-grid-content', ContainerID).hide();
        jQuery('.k-pager-wrap', ContainerID).hide();
    } else {
        jQuery('.k-grid-header', ContainerID).show();
        jQuery('.k-grid-content', ContainerID).show();
        jQuery('.k-pager-wrap', ContainerID).show();
    }
}

function gridScrollRTL() {
    var chromeAgent = navigator.userAgent.indexOf("Chrome") > -1;
    if (chromeAgent) {
        jQuery(".k-grid-content").css("overflow-y", "scroll").css("overflow-x", "auto").scroll(function () {
            var left = jQuery(this).scrollLeft();
            var wrap = jQuery(".k-grid-header-wrap");
            if (wrap.scrollLeft() != left)
                wrap.scrollLeft(left);
        });
    }
}

function disableExpiredFields(container) {
    container = ('string' == typeof container) ? jQuery('#' + container) : container;
    jQuery('.form-control, .btn', container).each(function (e, t) {
        if (jQuery(this).attr('type') != "submit")
            jQuery(this).attr("disabled", "disabled");
    });
}

jQuery(document).ready(function () {
    licensePackageAccessCheck();
    //function for more than model overlapped,to check when a model is closed for the cloass model-open so it will bypass it if there's a modal still open
    //this function is to solve the scroll that occurs when a model is closed and another model is still opened.
    var originalRemoveClassMethod = jQuery.fn.removeClass;
    jQuery.fn.removeClass = function () {
        if (arguments[0] === 'modal-open' && jQuery('.modal.in').length > 1) {
            return this;
        }
        var result = originalRemoveClassMethod.apply(this, arguments);
        return result;
    }

    var remindersPopoverOptions = {
        html: true, trigger: 'click', placement: 'bottom', //container: '#pendingReminders',
        sanitize: false,    //to allow onclick inside content
        content: '<div id="remindersListLatestContainer" style="width:220px;"><div class="reminders_list_latest loading" id="remindersListLatest">&nbsp;</div><p><a href="javascript:;" onclick="reminderForm();" class="btn btn-link" title="' + _lang.scheduleNewReminder + '">' + _lang.add + '</a><a href="' + getBaseURL() + 'reminders/reminders_router" class=" btn btn-link" title="' + _lang.showAllReminders + '">' + _lang.showAll + '</a></p></div>'
    }, notificationsOptions = {
        html: true,
        sanitize: false,
        trigger: 'click',
        placement: 'bottom',
        content: '<div id="notificationsContainer"><div class="notifications_list_latest loading" id="notificationsListLatest">&nbsp;</div><p><a href="' + getBaseURL() + 'notifications/broadcast" class="btn-notify btn-link" title="' + _lang.sendNotification + '">' + _lang.notify + '</a><a href="' + getBaseURL() + 'notifications" class="btn-notify btn-link" title="' + _lang.showAllNotifications + '">' + _lang.showAll + '</a><span class="btn-notify btn-link" title="' + _lang.dismissAll + '" onclick="dismissAllNotification()">' + _lang.dismissAll + '</span></p></div>'
    };
    remindersPopoverIconClose = '<a class="pull-right" href="javascript:;" onclick="jQuery(\'#pendingReminders\').parent().popover(\'hide\');if(remindersRefreshInterval!=\'\'){refreshRemindersIntervalId = window.setInterval(function(){loadUserLatestReminders(\'refresh\');}, remindersRefreshInterval);};return false;"><span>x</span></a>';
    notificationsPopoverIconClose = '<a class="pull-right" href="javascript:;" onclick="jQuery(\'#pendingNotifications\').parent().popover(\'hide\');if(notificationRefreshInterval!=\'\'){refreshNotificationsIntervalId = window.setInterval(function(){loadLatestNotifications(\'refresh\');}, notificationRefreshInterval);};return false;"><span>x</span></a>';
    reminderPopoverAnchor = jQuery('#pendingReminders', '#header_div').parent();
    notificationPopoverAnchor = jQuery('#pendingNotifications').parent();
    if (remindersRefreshInterval != '') {
        refreshRemindersIntervalId = window.setInterval(function () {
            loadUserLatestReminders('refresh');
        }, remindersRefreshInterval);
    }
    reminderPopoverAnchor.attr('data-original-title', _lang.reminders + remindersPopoverIconClose)
        .popover(remindersPopoverOptions).on({
            'click': function (e) {
                e.preventDefault();
                jQuery('#pendingNotifications').parent().popover('hide');
                if (jQuery("#remindersListLatestContainer").parents().eq(1).hasClass('show')) {
                    loadUserLatestReminders('load_all');
                } else {
                    jQuery('#remindersListLatest').addClass('loading').html('');
                }
            }
        });
    if (notificationRefreshInterval != '') {
        refreshNotificationsIntervalId = window.setInterval(function () {
            loadLatestNotifications('refresh');
        }, notificationRefreshInterval);
    }
    notificationPopoverAnchor.attr('data-original-title', _lang.notifications + notificationsPopoverIconClose).popover(notificationsOptions).on({
        'click': function (e) {
            e.preventDefault();
            jQuery('#pendingReminders').parent().popover('hide');
            if (jQuery("#notificationsContainer").parents().eq(1).hasClass('show')) {
                loadLatestNotifications('load_all');
            } else {
                jQuery('#notificationsListLatest').addClass('loading').html('');
            }
        }
    });

    jQuery(window).scroll(function () {
        if (jQuery(this).scrollTop() > 100) {
            jQuery('.scrollToTop').fadeIn();
        } else {
            jQuery('.scrollToTop').fadeOut();
        }
    });
    //Click event to scroll to top
    jQuery('.scrollToTop').click(function () {
        jQuery('html, body').animate({ scrollTop: 0 }, 100);
        return false;
    });

    jQuery('.tooltipTable').each(function (index, element) {
        jQuery(element).tooltipster({
            content: jQuery(element).attr('tooltipTitle'),
            contentAsHTML: true,
            timer: 22800,
            animation: 'grow',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
            maxWidth: 350,
        });
    });
    jQuery(document).on('event', 'selector', function () {
        var formDataIsValid = jQuery(this).parents('form:first').validationEngine('validate');
        if (!formDataIsValid) {
            jQuery(".form-submit-loader").hide();
        } else {
            jQuery(".form-submit-loader").show();
        }
    });
    // adding grid container to new grid templates
    jQuery('#main-container:has(.grid-header)').addClass('grid-main-container');
    whatsNew();
    jQuery('.dropdown-menu-timer').click(function (e) {
        e.stopPropagation();
    });
    // jQuery.fn.selectpicker.Constructor.DEFAULTS.container = 'body';
    // jQuery.fn.selectpicker.Constructor.DEFAULTS.dropupAuto = false;
    // jQuery.fn.modal.Constructor.DEFAULTS.keyboard = false;
    // jQuery.fn.modal.Constructor.DEFAULTS.backdrop = 'static';
    jQuery(document).on('show.bs.modal', function (e) {
        var modelOpen = jQuery('.modal', jQuery(this)).last();
        disableAutocomplete(modelOpen);
        if (modelOpen.not(".confirmation-dialog-modal") && jQuery('form', modelOpen).data('serialize') == undefined) {
            setTimeout(function () {
                jQuery('form', modelOpen).data('serialize', jQuery('form', modelOpen).serialize());
            }, 200);
        }
        jQuery('[data-dismiss="modal"]', modelOpen).on('click', function (e) {
            var modelOpen = jQuery(this).closest('div.modal');
            var origForm = jQuery('form', modelOpen).data('serialize');
            var editedForm = jQuery('form', modelOpen).serialize();
            var confirmLength = jQuery(".confirmation-dialog-container").length;
            if ((origForm && origForm.includes("clone")) || (origForm && origForm.includes("trigger-create-task")) || (origForm && origForm.includes("trigger-create-another"))) {
                origForm = origForm.replace("clone=no&", "");
                origForm = origForm.replace("clone=yes&", "");
                origForm = origForm.replace("trigger-create-task=yes&", "");
                origForm = origForm.replace("trigger-create-task=&", "");
                origForm = origForm.replace("trigger-create-another=yes&", "");
                origForm = origForm.replace("trigger-create-another=&", "");
                editedForm = editedForm.replace("clone=no&", "");
                editedForm = editedForm.replace("clone=yes&", "");
                editedForm = editedForm.replace("trigger-create-task=yes&", "");
                editedForm = editedForm.replace("trigger-create-task=&", "");
                editedForm = editedForm.replace("trigger-create-another=yes&", "");
                editedForm = editedForm.replace("trigger-create-another=&", "");
            }
            if (confirmLength == 0 && origForm != undefined && origForm !== editedForm && !modelOpen.hasClass('esc-true')) {
                confirmationDialog('confirm_close_model', { resultHandler: null, modelOpen: modelOpen })
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });
    });
    jQuery(document).on('keyup', function (e) {
        var keyCode = (window.event) ? e.which : e.keyCode;
        if (keyCode === 27 && jQuery('body').hasClass('modal-open') && !jQuery('[data-dismiss="modal"]').last().closest('div.modal').hasClass('esc-true')) {
            jQuery('[data-dismiss="modal"]').last().click();
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
    });
    targetDialogOpen();
    helpers.onPageLoadEvents();
    if (userGuide != "" && isloggedIn == "logged") {
        // supportDemo();
    }

    jQuery('a', '#notificationsPageList').css('color', '#6FD9C4');
    jQuery('a', '#notificationsPageList').hover(function () {
        jQuery(this).css({ 'color': '#30CBAC', 'font-weight': 700 });
    }, function () {
        jQuery(this).css({ 'color': '#6FD9C4', 'font-weight': 'normal' });
    });
    jQuery(document).on('shown.bs.modal', '.modal', function () {
        jQuery(this).find('[autofocus]').focus();
    });
});

function fixOpponentSearchInDialog() {
    var caseDialog = jQuery('.ui-dialog[aria-describedby="newCaseFormDialog"]');
    if (caseDialog.length)
        caseDialog.css('z-index', '101');
}

function addGridFilter(model, gridId, gridFormId, isGlobalFilter) {
    isGlobalFilter = isGlobalFilter || 0;
    jQuery.ajax({
        url: getBaseURL() + 'grid_saved_filters/add/' + model + '/' + isGlobalFilter,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery(".save-filter-as").attr('disabled', 'disabled');
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (jQuery('#gridSavedFilterFormContainer').length == 0) {
                jQuery('<div id="gridSavedFilterFormContainer" class="d-none"></div>').appendTo('body');
            }
            var gridSavedFilterForm = jQuery('#gridSavedFilterFormContainer');
            gridSavedFilterForm.html(response.html).removeClass('d-none');
            jQuery('.modal', gridSavedFilterForm).modal({
                keyboard: false
            });
            gridSavedForm = jQuery("form#gridSavedFilterForm", gridSavedFilterForm);
            gridSavedForm.validationEngine({
                validationEventTrigger: "submit",
                autoPositionUpdate: true,
                promptPosition: 'bottomRight',
                scroll: false
            });
            jQuery("#saveGridFormBtn").click(function () {
                if (gridSavedForm.validationEngine("validate")) {
                    var filters = prepareGridFilters(gridFormId);
                    jQuery('#gridFilters', gridSavedForm).val(JSON.stringify(filters));
                    jQuery('#gridPageSize', gridSavedForm).val(jQuery("#" + gridId).data("kendoGrid").dataSource.pageSize());
                    var gridColumns = [];
                    jQuery("#" + gridId).data("kendoGrid").options.columns.forEach(function (item, index) {
                        if (item.title.trim() != '') {
                            gridColumns.push(item.field);
                        }
                    });
                    jQuery('#grid-columns', gridSavedForm).val(gridColumns.toString());
                    jQuery('#grid-columns-sort', gridSavedForm).val(JSON.stringify(jQuery("#" + gridId).data("kendoGrid").dataSource.sort()));
                    jQuery.ajax({
                        data: gridSavedForm.serialize(),
                        beforeSend: function () {
                            jQuery("#saveGridFormBtn").attr('disabled', 'disabled');
                            jQuery("#loader-global").show();
                        },
                        success: function (response) {
                            jQuery("#loader-global").hide();
                            if (response.success) {
                                jQuery('.modal', gridSavedFilterForm).modal('hide');
                                jQuery("#gridFiltersList").append('<option value="' + response.data.id + '" isGlobalFilter="' + response.data.isGlobalFilter + '">' + jQuery('#filterName', gridSavedForm).val() + '</option>').val(response.data.id);
                                displayGridFiltersActionEvents();
                            } else {
                                pinesMessage({ ty: 'error', m: response.error });
                                jQuery("#saveGridFormBtn").removeAttr('disabled');
                            }
                        },
                        type: 'POST',
                        url: getBaseURL() + 'grid_saved_filters/add/' + model + '/' + isGlobalFilter
                    });
                }
            });
            jQuery("#filterName", gridSavedForm).keypress(function (e) {
                if (e.which == 13) {
                    jQuery("#saveGridFormBtn").click();
                }
            });
            jQuery('.modal-body').on("scroll", function () {
                jQuery('.bootstrap-select.open').removeClass('open');
            });
            jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
                jQuery('#filterName', '#gridSavedFilterForm').focus();
            });
        },
        complete: function () {
            jQuery("#saveGridFormBtn").removeAttr('disabled');
            jQuery(".save-filter-as").removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function browserIsIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
    var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);
    if (msie > 0 || isIE11) {
        return true;
    } else {
        return false;
    }
}

function prepareGridFilters(gridFormId) {
    var filtersForm = jQuery('#' + gridFormId);
    disableEmpty(filtersForm);
    disableUnCheckedFilters();
    var searchFilters = form2js(gridFormId, '.', true);
    var filters = '';
    filters = searchFilters.filter;
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}

function updateGridFilterData(filterId, gridId, gridFormId) {
    var filters = prepareGridFilters(gridFormId);
    jQuery.ajax({
        url: getBaseURL() + 'grid_saved_filters/update_data/' + filterId,
        dataType: 'JSON',
        type: 'POST',
        data: {
            gridFilters: JSON.stringify(filters),
            gridPageSize: jQuery("#" + gridId).data("kendoGrid").dataSource.pageSize()
        },
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.success) {
                jQuery('#' + gridId).data("kendoGrid").dataSource.read();
            } else {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.updatesFailed });
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteGridFilter(filterId) {
    if (filterId) {
        if (confirm(_lang.confirmationDeleteSelectedFilter)) {
            jQuery.ajax({
                url: getBaseURL() + 'grid_saved_filters/delete/' + filterId,
                dataType: 'JSON',
                type: 'POST',
                beforeSend: function () {
                    jQuery("#loader-global").show();
                },
                success: function (response) {
                    jQuery("#loader-global").hide();
                    if (response.success) {
                        var preSelectedFilter = jQuery("#gridFiltersList").val();
                        pinesMessage({ ty: 'information', m: _lang.deleteRecordSuccessfull });
                        jQuery("option[value='" + filterId + "']", "#gridFiltersList").remove();
                        jQuery("tr#" + filterId, '#manageFiltersTable').remove();
                        hideGridFiltersActionEvents();
                        if (jQuery('tr', jQuery('tbody', '#manageFiltersTable')).length == 0) {
                            jQuery('.modal', jQuery('#gridManageFiltersContainer')).modal('hide');
                        }
                        if (preSelectedFilter != '' && preSelectedFilter == filterId && response.gridDetails != undefined) {
                            setGridDetails(response.gridDetails);
                        }
                    } else {
                        pinesMessage({ ty: 'error', m: response.error ? response.error : _lang.recordNotDeleted });
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    }
}

function clearGridFilters(model, selectedFilter) {
    selectedFilter = selectedFilter || '';
    if (model) {
        jQuery.ajax({
            url: getBaseURL() + 'grid_saved_filters/load_data/0/' + model,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                jQuery("#loader-global").hide();
                if (response.success) {
                    switch (model) {
                        case 'Legal_Case_Hearing':
                            if (hearingFixedFilters.indexOf(selectedFilter) !== -1) {
                                window.location = getBaseURL() + 'cases/' + selectedFilter;
                            } else if (!selectedFilter) { // All Hearings
                                window.location = getBaseURL() + 'cases/list_hearings';
                            }
                            break;
                        case 'awaiting_approvals':
                            if (selectedFilter === 'awaiting_my_approvals') {
                                window.location = getBaseURL('contract') + 'contracts/awaiting_my_approvals';
                            } else if (!selectedFilter) { // All contracts awaiting approvals
                                window.location = getBaseURL('contract') + 'contracts/awaiting_approvals';
                            }
                            break;
                        case 'awaiting_signatures':
                            if (selectedFilter === 'awaiting_my_signatures') {
                                window.location = getBaseURL('contract') + 'contracts/awaiting_my_signatures';
                            } else if (!selectedFilter) { // All contracts awaiting signatures
                                window.location = getBaseURL('contract') + 'contracts/awaiting_signatures';
                            }
                            break;
                        default:
                            window.location = window.location.href;
                            break;
                    }
                } else {
                    pinesMessage({ ty: 'error', m: response.error });
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function loadGridFilters(filterId, gridId, gridFormId) {
    if (filterId) {
        jQuery.ajax({
            url: getBaseURL() + 'grid_saved_filters/load_data/' + filterId,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                jQuery("#loader-global").hide();
                if (response.success) {
                    setGridFiltersData(response.data, gridId, true);
                } else {
                    pinesMessage({ ty: 'error', m: response.error });
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function hideGridFiltersActionEvents() {
    jQuery('#submitAndSaveFilter').addClass('d-none');
}

function displayGridFiltersActionEvents() {
    jQuery('#submitAndSaveFilter').removeClass('d-none');
}

function setGridFiltersData(data, gridId, readGrid) {
    readGrid = readGrid || false;
    var gridFilters = data.formData.gridFilters;
    var kendoGrid = jQuery("#" + gridId);
    setGridFormFilters(gridFilters);
    if (readGrid) {
        gridSavedFiltersParams = gridFilters;
    }
    if (data.gridDetails != undefined) {
        setGridDetails(data.gridDetails);
    }
    if (data.client_account != undefined) {
        if (jQuery('#accountNameValue', '#filtersFormWrapper').length) {
            jQuery('#accountNameValue').val(data.client_account);
        }
        if (jQuery('#accountIDValue', '#filtersFormWrapper').length) {
            jQuery('#accountIDValue').val(data.client_account);
        }
        if (jQuery('#supplierAccountValue', '#filtersFormWrapper').length) {
            jQuery('#supplierAccountValue').val(data.client_account);
        }
    }
    if (data.outsource_type != undefined || data.outsource_to_value != undefined || data.outsource_to_function != undefined) {
        if (jQuery('#outsourceTypeOperator', '#filtersFormWrapper').length) {
            jQuery('#outsourceTypeOperator').val(data.outsource_type);
        }
        if (jQuery('#outsourceToValue', '#filtersFormWrapper').length) {
            jQuery('#outsourceToValue').val(data.outsource_to_value);
        }
        if (jQuery('#outsourceToFunction', '#filtersFormWrapper').length) {
            jQuery('#outsourceToFunction').val(data.outsource_to_function);
        }
    }
}

function setGridDetails(gridDetails) {
    if (gridDetails.selected_columns) {
        jQuery('#display-columns').val(gridDetails.selected_columns.toString());
    }
    gridSavedPageSize = gridDetails.grid_saved_details.pageSize ? gridDetails.grid_saved_details.pageSize : gridDefaultPageSize;
    gridSavedColumnsSorting = gridDetails.grid_saved_details.sort ? gridDetails.grid_saved_details.sort : false;
    gridInitialization();
}

function gridFiltersEvents(model, gridId, gridFormId) {
    if (loggedUserIsAdminForGrids != 1) {
        jQuery('#gridGlobalFilterContainer').remove();
    }
    jQuery('#gridFiltersContainer').html(jQuery('#gridFiltersTempContainer').html());
    var selectedFilter = jQuery("#gridFiltersList", jQuery('#gridFiltersContainer'));
    if (selectedFilter.val()) { // grid have selected filter
        if (hearingFixedFilters.indexOf(selectedFilter.val()) !== -1 || selectedFilter.val() === 'awaiting_my_approvals' || selectedFilter.val() === 'awaiting_my_signatures') { // my hearings & awaiting_my_approvals are fixed filters and not saved filters so no need to let the user submit and save those filters
            hideGridFiltersActionEvents();
        } else {
            if (selectedFilter.find("option:selected").attr('isGlobalFilter') == 1) { // selected filter is Global
                if (loggedUserIsAdminForGrids != 1) {
                    hideGridFiltersActionEvents();
                }
            }
        }
    } else {
        hideGridFiltersActionEvents();
    }
    selectedFilter.change(function () {
        enableQuickSearch = false;
        var val = this.value;
        if (val) {
            if (hearingFixedFilters.indexOf(val) !== -1 || val === 'awaiting_my_approvals' || val === 'awaiting_my_signatures') { // my hearings & awaiting_my_approvals are fixed filters and not saved filter so selecting this filter will redirect the user to my_hearing action
                hideGridFiltersActionEvents();
                clearGridFilters(model, val);
            } else {
                if (jQuery(this).find("option:selected").attr('isGlobalFilter') == 1) {
                    if (loggedUserIsAdminForGrids != 1) {
                        hideGridFiltersActionEvents();
                    } else {
                        displayGridFiltersActionEvents();
                    }
                } else {
                    displayGridFiltersActionEvents();
                }
                jQuery('.quick-search-filter').val('');
                loadGridFilters(val, gridId, gridFormId);
                // clear fixed filters when selecting a saved filter (i.e when selecting a hearing filter, the system should clear the assignee value when the current URL is my_hearings)
                if (jQuery('.grid-fixed-filter', '#' + gridFormId).length) {
                    jQuery('.grid-fixed-filter', '#' + gridFormId).val();
                }
            }

        } else {
            hideGridFiltersActionEvents();
            clearGridFilters(model);
        }
    });
    jQuery('#submitAndSaveFilter').click(function () {
        if (selectedFilter.val()) {
            updateGridFilterData(selectedFilter.val(), gridId, gridFormId);
        }
    });
}

function setGridFormFilters(gridFilters) {
    // reset form fields
    jQuery('#resetBtnHidden').click();
    jQuery('.sf-value').each(function () {
        if (jQuery(this).hasClass('multi-select')) {
            jQuery(this).val("").trigger("chosen:updated");
        }
    });
    jQuery('.data-filter').hide();
    jQuery('.checkbox-inline').prop('style', 'font-weight: normal !important;');
    jQuery('.chk-filter').prop('checked', false);
    // set new filter values
    var jsonFilters = json_decode(gridFilters);
    jQuery('.empty-value-field').show();
    if (undefined != jsonFilters['filters']) {
        for (var i = 0; i < jsonFilters['filters'].length; i++) {
            var field = jsonFilters['filters'][i]['filters']['0']['field'];
            var fieldContainer = jQuery("div").find("[sf-field='" + field + "']");
            var operator = jsonFilters['filters'][i]['filters']['0']['operator'];
            var value = jsonFilters['filters'][i]['filters']['0']['value'];
            var valueSelector = jQuery('.sf-value', fieldContainer);
            jQuery('.sf-operator', fieldContainer).val(operator);
            if (value) {
                jQuery('.chk-filter', fieldContainer).prop("checked", "true")
                jQuery('.data-filter', fieldContainer).show()
                jQuery('.checkbox-inline', fieldContainer).prop('style', 'font-weight: 600 !important;');
                jQuery('.chk-filter', fieldContainer).parent().parent().insertBefore(jQuery("#sortable .form-group-filter:eq(0)"));
            }
            if (operator === 'not_empty' || operator === 'empty') {
                valueSelector.hide();
            } else {
                valueSelector.show();
            }
            if (jQuery('.sf-operator-list', fieldContainer).length) {
                jQuery('.sf-operator-list', fieldContainer).val((operator == 'eq' || operator == 'startswith') ? 'startswith' : operator);
            }
            if (jQuery('.empty-value-field', fieldContainer).length) {
                jQuery('.empty-value-field', fieldContainer).val((operator == 'eq' || operator == 'startswith') ? 'startswith' : operator);
            }
            valueSelector.val(value);
            if (valueSelector.hasClass('multi-select')) {
                valueSelector.trigger("chosen:updated");
            }
            if (jsonFilters['filters'][i]['filters'].length > 1) { // i.e date filters (between 2 dates)
                var operator2 = jsonFilters['filters'][i]['filters']['1']['operator'];
                var value2 = jsonFilters['filters'][i]['filters']['1']['value'];
                // to avoid creating a php script to migrate the fields in saved filters when we change the field name in html
                // for example, I changed the field in html from "legal_cases.clientType" to "clientType" and in my database i have a saved filter in a json format that contains the old field "legal_cases.clientType" so if we didn't make a replacement in the json, the value will not be selected in the advanced filters.
                if ((field == 'clientType' || field == 'legal_cases.clientType') && (jsonFilters['filters'][i]['filters']['1']['field'] == 'client_foreign_name' || jsonFilters['filters'][i]['filters']['1']['field'] == 'legal_cases.client_foreign_name' || jsonFilters['filters'][i]['filters']['1']['field'] == 'clientForeignName' || jsonFilters['filters'][i]['filters']['1']['field'] == 'legal_cases.clientForeignName')) {
                    jQuery('.sf-value', jQuery("div").find("[sf-field='" + jsonFilters['filters'][i]['filters']['1']['field'] + "']")).val(value2);
                }
                if ((field == 'clientType' || field == 'legal_cases.clientType') && (jsonFilters['filters'][i]['filters']['1']['field'] == 'clientName' || jsonFilters['filters'][i]['filters']['1']['field'] == 'legal_cases.clientName' || jsonFilters['filters'][i]['filters']['1']['field'] == 'clients' || jsonFilters['filters'][i]['filters']['1']['field'] == 'legal_cases.clients')) {
                    jQuery('.sf-value', jQuery("div").find("[sf-field='" + jsonFilters['filters'][i]['filters']['1']['field'] + "']")).val(value2);
                    jQuery('.sf-operator', jQuery("div").find("[sf-field='" + jsonFilters['filters'][i]['filters']['1']['field'] + "']")).val(operator2);
                } else {// date filters
                    jQuery('.sf-operator-list', fieldContainer).val('cast_between');
                    jQuery('.sf-operator2', fieldContainer).val(operator2);
                    jQuery('.sf-value2', fieldContainer).val(value2).removeClass('d-none');
                    jQuery('.end-date-icon-filter', fieldContainer).removeClass('d-none');
                }
            }
        }
    }
    // set custom fields
    if (undefined != jsonFilters['customFields']) {
        for (var i = 0; i < jsonFilters['customFields'].length; i++) {
            var field = "customFiled-" + jsonFilters['customFields'][i]['id'];
            var fieldContainer = jQuery("div").find("[sf-field='" + field + "']");
            var operator = jsonFilters['customFields'][i]['operator'];
            jQuery('.chk-filter', fieldContainer).prop("checked", "true")
            jQuery('.data-filter', fieldContainer).show()
            jQuery('.checkbox-inline', fieldContainer).prop('style', 'font-weight: 600 !important;');
            jQuery('.chk-filter', fieldContainer).parent().parent().insertBefore(jQuery("#sortable .form-group-filter:eq(0)"));
            switch (jsonFilters['customFields'][i]['type']) {
                case 'date':
                    var dateValue = jsonFilters['customFields'][i]['date_value'];
                    jQuery('.sf-operator', fieldContainer).val(operator.start);
                    jQuery('.sf-value', fieldContainer).val(dateValue.start);
                    if (jQuery('.sf-operator-list', fieldContainer).length) {
                        jQuery('.sf-operator-list', fieldContainer).val((operator.start == 'eq' || operator.start == 'startswith') ? 'startswith' : operator.start);
                    }
                    if (operator.end_date !== undefined) {
                        jQuery('.sf-operator-list', fieldContainer).val('cast_between');
                        jQuery('.sf-operator2', fieldContainer).val(operator.end);
                        jQuery('.sf-value2', fieldContainer).val(dateValue.end).removeClass('d-none');
                        jQuery('.end-date-icon-filter', fieldContainer).removeClass('d-none');

                    }
                    break;
                case 'date_time':
                    var dateValue = jsonFilters['customFields'][i]['date_value'];
                    var timeValue = jsonFilters['customFields'][i]['time_value'];
                    if (jQuery('.sf-operator-date-time', fieldContainer).length) {
                        jQuery('.sf-operator-date-time', fieldContainer).val((operator.start == 'cast_eq' || operator.start == 'startswith') ? 'cast_eq' : operator.start);
                    }
                    if (dateValue.start !== undefined) {
                        jQuery('.sf-operator-start-date', fieldContainer).val(operator.start);
                        jQuery('.sf-value-start-date', fieldContainer).val(dateValue.start);
                    }
                    if (dateValue.end !== undefined) {
                        jQuery('.sf-operator-date-time', fieldContainer).val('cast_between');
                        jQuery('.sf-operator-end-date', fieldContainer).val(operator.end);
                        jQuery('.sf-value-end-date', fieldContainer).val(dateValue.end).removeClass('d-none');
                    }
                    if (timeValue !== undefined) {
                        jQuery('.sf-operator-time', fieldContainer).val(operator.time);
                        jQuery('.sf-value-time', fieldContainer).val(timeValue);
                    }
                    break;
                case 'list':
                    var textValue = jsonFilters['customFields'][i]['text_value'];
                    jQuery('.sf-operator', fieldContainer).val(operator);
                    jQuery('.sf-value', fieldContainer).val(textValue);
                    jQuery('.sf-value', fieldContainer).trigger("chosen:updated");
                    break;
                default:
                    var textValue = jsonFilters['customFields'][i]['text_value'];
                    jQuery('.sf-operator', fieldContainer).val(operator);
                    jQuery('.sf-value', fieldContainer).val(textValue);
                    break;
            }

        }
    }
    loadEventsForFilters();
}

function manageGridFilters(model, gridId, gridFormId, advancedSearchEventsFunction) {
    jQuery.ajax({
        url: getBaseURL() + 'grid_saved_filters/manage_filters/' + model,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (jQuery('#gridManageFiltersContainer').length == 0) {
                jQuery('<div id="gridManageFiltersContainer" class="d-none"></div>').appendTo('body');
            }
            var gridManageFiltersContainer = jQuery('#gridManageFiltersContainer');
            gridManageFiltersContainer.html(response.html).removeClass('d-none');
            jQuery('#advancedSearchEventsFunction').val(advancedSearchEventsFunction);
            jQuery('.modal', gridManageFiltersContainer).modal({
                keyboard: false
            });
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function onthefly_Ajax(component, component_type, form_Container, event) {
    /*
     * Component: The field that we are adding. Required.
     * Component Type: If the field has several types, e.g: Litigation or Legal Matter.
     * If there are no types then the values is the same as the Component variable. Required.
     * Form Container: If the form is in a dialog box then this value should be the id
     * of the box, else it should be the id of the form itself. Optional.
     */
    if (undefined != event) {
        event.preventDefault();
    }
    if (jQuery(".onthefly_hidden").length <= 0) {
        jQuery('<div class="d-none onthefly_hidden"></div>').appendTo("body");
    }
    form_Container = form_Container || "";
    jQuery.ajax({
        url: getBaseURL() + component + '/add/',
        dataType: 'JSON',
        type: 'GET',
        data: {
            quick_add_form: true,
            type: component_type,
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            var formContainer = "";
            formContainer = jQuery('#' + form_Container);
            var onthefly_hidden = jQuery('.onthefly_hidden');
            onthefly_hidden.html(response.html).removeClass('d-none');
            jQuery(document).keyup(function (e) {
                if (e.keyCode == 27) {
                    jQuery('.modal', onthefly_hidden).modal('hide');
                    if (formContainer.hasClass('ui-dialog-content')) {
                        formContainer.dialog('close');
                        jQuery('.ui-widget-overlay.ui-front').hide();
                    }
                }
            });
            jQuery('.modal', onthefly_hidden).modal({
                keyboard: false,
                show: true,
                backdrop: 'static'


            });
            resizeMiniModal(onthefly_hidden);
            jQuery(window).bind('resize', (function () {
                resizeMiniModal(onthefly_hidden);
            }));
            jQuery("input", onthefly_hidden).keypress(function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    jQuery("#ontheflyFormBtn").click();
                }
            });
            jQuery('.modal-body').on("scroll", function () {
                jQuery('.bootstrap-select.open').removeClass('open');
            });
            jQuery('.modal').on('hidden.bs.modal', function () {
                destroyModal(onthefly_hidden);
            });
            jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
                jQuery('#fieldName', onthefly_hidden).focus();

            });
            ctrlS(function () {
                jQuery("#ontheflyFormBtn").click();
            });
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function copyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.style.position = 'fixed';
    textArea.style.top = 0;
    textArea.style.left = 0;
    // Ensure it has a small width and height. Setting to 1px / 1em
    // doesn't work as this gives a negative w/h on some browsers.
    textArea.style.width = '2em';
    textArea.style.height = '2em';
    // We don't need padding, reducing the size if it does flash render.
    textArea.style.padding = 0;
    // Clean up any borders.
    textArea.style.border = 'none';
    textArea.style.outline = 'none';
    textArea.style.boxShadow = 'none';
    // Avoid flash of white box if rendered for any reason.
    textArea.style.background = 'transparent';
    textArea.value = text.trim();
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
    } catch (err) {
        console.log('Unable to copy');
    }
    document.body.removeChild(textArea);
}

function deleteCaseRecord(caseId) {
    var msg = _lang.confirmationOfCaseDelete;
    if (confirm(msg)) {
        window.location = getBaseURL() + 'cases/delete_case/' + caseId;
    }
}

// initialize private watchers events
function initPrivateWatchers(itemId, container, opts, isPrivate) {
    isPrivate = isPrivate || false;
    jQuery('#' + itemId, container).selectize({
        plugins: ['remove_button'],
        persist: false,
        maxItems: null,
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: opts
    });
    if (!isPrivate) { // public item - hide watchers text box when item is private
        jQuery('.selectize-control', container).addClass('d-none');
        jQuery('.alert', container).addClass('d-none');
    } else { // private item
        jQuery('.shared-with-label', container).addClass('d-none');
    }
}

// clicking on set as private link in case, contact or comapny
function setAsPrivate(container, controller, itemObj, creator, loggedUser, addOnTheFly) {
    creator = creator || false;
    loggedUser = loggedUser || false;
    itemObj = itemObj || false;
    addOnTheFly = addOnTheFly || false;
    jQuery.ajax({
        url: getBaseURL() + controller + '/set_privacy',
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            if (addOnTheFly) {
                jQuery('.loader-submit', container).addClass('loading');
            } else {
                jQuery("#loader-global").show();
            }
        },
        success: function (response) {
            if (response.result) {
                jQuery('#privateLink', container).addClass('d-none');
                jQuery('#publicLink', container).removeClass('d-none');
                jQuery('.shared-with-label', container).addClass('d-none');
                jQuery('#private', container).val('yes');
                jQuery('.alert-message', container).removeClass('d-none');
                if (addOnTheFly) {
                    jQuery('.lookup-box-container', container).removeClass('d-none');
                    jQuery('#selected-watchers', container).removeClass('d-none').css('border', 'solid 1px #ddd');
                    jQuery('.autocomplete-helper', container).removeClass('d-none');
                    resizeLookupDropDownWidth(container);

                } else {
                    jQuery('#watchersUsers', container).removeClass('d-none');
                    jQuery('.selectize-control', container).removeClass('d-none');
                    jQuery('.alert', container).removeClass('d-none');

                    if (creator && loggedUser && (Number(creator) != Number(loggedUser))) { // add modified user to watchers list in edit mode in case the logged user is not the owner / creator - Number using to cast id with leading zero to number in mysql
                        var control = itemObj[0].selectize;
                        control.addItem(loggedUser, true);
                    }
                }
            }
        }, complete: function () {
            if (addOnTheFly) {
                jQuery('.loader-submit', container).removeClass('loading');
            } else {
                jQuery("#loader-global").hide();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

// clicking on set as public link in case, contact or comapny
function setAsPublic(container, controller, addOnTheFly) {
    addOnTheFly = addOnTheFly || false;
    jQuery.ajax({
        url: getBaseURL() + controller + '/set_privacy',
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            if (addOnTheFly) {
                jQuery('.loader-submit').addClass('loading');
            } else {
                jQuery("#loader-global").show();
            }
        },
        success: function (response) {
            if (response.result) {
                jQuery('#private', container).val('no');
                jQuery('#privateLink', container).removeClass('d-none');
                jQuery('#publicLink', container).addClass('d-none');
                jQuery('.shared-with-label', container).removeClass('d-none');
                jQuery('.alert-message', container).addClass('d-none');
                if (addOnTheFly) {
                    jQuery('.lookup-box-container', container).addClass('d-none');
                    jQuery('#selected-watchers', container).addClass('d-none');
                    jQuery('.autocomplete-helper', container).addClass('d-none');
                    jQuery('.inline-error', container).addClass('d-none');
                } else {
                    jQuery('#watchersUsers', container).addClass('d-none');
                    jQuery('.selectize-control', container).addClass('d-none');
                    jQuery('.alert', container).addClass('d-none');
                }
            }
        }, complete: function () {
            if (addOnTheFly) {
                jQuery('.loader-submit').removeClass('loading');
            } else {
                jQuery("#loader-global").hide();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/*
 * Lookup for Users(assignee/reporter)
 * Retreiving users on click on the input(retreive all users) or searching for users(depending on the term)
 * @param string lookupField( jQuery selector for lookup input ),hiddenInputIdField( jQuery selector for hidden input field )
 */
function lookUpUsers(lookupField, hiddenInputIdField, errorField, container, formContainer, moreFilters, callback) {
    moreFilters = moreFilters || false;
    callback = callback || false;
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('firstName');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: getBaseURL() + (typeof moreFilters['keyName'] != 'undefined' ? ('users/autocomplete/active?term=%QUERY&more_filters[' + moreFilters['keyName'] + ']=%MORE_FILTERS ') : 'users/autocomplete/active?term=%QUERY'),
            replace: function (url, uriEncodedQuery) {
                if (typeof moreFilters['value'] != 'undefined') {
                    var keyValue = encodeURIComponent(moreFilters['value']);
                    return url.replace('%QUERY', uriEncodedQuery).replace('%MORE_FILTERS', keyValue);
                }
                return url.replace('%QUERY', uriEncodedQuery);
            },
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        },
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupField).typeahead({
        hint: false,
        highlight: true,
        minLength: typeof moreFilters['minLength'] != 'undefined' ? moreFilters['minLength'] : 0
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                return item.firstName + ' ' + item.lastName
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                header: moreFilters ? '' : '<div class="suggestions-header">' + _lang.allUsers + '</div>'
            }
        }
    ).on('typeahead:selected', function (obj, datum) {
        if (callback['callback'] && isFunction(callback['callback'])) {
            callback.callback(datum, container);
        }
    }).on('typeahead:asyncrequest', function () {
        jQuery('.loader-submit', formContainer).addClass('loading');
    }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
        if (obj.currentTarget['value'] == '' && datum == undefined && moreFilters) {
            //if searching for all result(no term sent) and the response is undefined and moreFilters array is defined
            // then change the response message of the lookup
            jQuery('.empty', '#' + obj.currentTarget['form']['id']).html(moreFilters['messageDisplayed']);
        }
        jQuery('.loader-submit', formContainer).removeClass('loading');
        highLightFirstSuggestion();
    }).on('focus', function () {
        highLightFirstSuggestion();
    });
    if (typeof moreFilters['resize'] == 'undefined') resizeLookupDropDownWidth(container);
    if (!isFunction(callback['onEraseLookup']) || !callback['onEraseLookup']) {
        callback['onEraseLookup'] = false;
    }
    if (!isFunction(callback['onClearLookup']) || !callback['onClearLookup']) {
        callback['onClearLookup'] = false;
    }
    lookupCommonFunctions(lookupField, hiddenInputIdField, errorField, formContainer, callback['onEraseLookup'], callback['onClearLookup']);
}

/*
 * Lookup for Cases
 * Retreiving cases depending on the term entered with 2 characters and above
 * @param string lookupField( jQuery selector for case lookup input ),legalCaseIdField( jQuery selector for hidden case id input field ),string errorField( string name of the error field for the lookup ),string container( jQuery selector for modal container )
 */
function lookUpCases(lookupField, legalCaseIdField, errorField, container, moreFilters, callback) {
    moreFilters = moreFilters || false;
    callback = callback || false;
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('fullSubject');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: getBaseURL() + 'cases/autocomplete/active?term=%QUERY' + (moreFilters ? '&more_filters[' + moreFilters['keyName'] + ']=%MORE_FILTERS ' : ''),
            filter: function (data) {
                return data;
            },
            replace: function (url, uriEncodedQuery) {
                if (moreFilters) {
                    var keyValue = encodeURIComponent(moreFilters['value']);
                    return url.replace('%QUERY', uriEncodedQuery).replace('%MORE_FILTERS', keyValue);
                }
                return url.replace('%QUERY', uriEncodedQuery);
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupField).typeahead({
        hint: false,
        highlight: true,
        minLength: 2
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                return item.caseID + ': ' + replaceHtmlCharacter(item.subject)
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div title="' + data.fullSubject + '">' + data.caseID + ': ' + replaceHtmlCharacter(data.subject) + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            jQuery(lookupField, container).attr("title", replaceHtmlCharacter(datum.fullSubject));
            jQuery("#case-subject", container).removeClass("d-none");
            jQuery("#case-link", container).attr("href", (datum.category == 'IP' ? 'intellectual_properties/edit/' : 'cases/edit/') + datum.id);
            if (moreFilters) {
                moreFilters.resultHandler(datum, container);
            }
            if (callback['callback'] && isFunction(callback['callback'])) {
                callback.callback(datum, container);
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            jQuery('.loader-submit', container).removeClass('loading');
        });
    jQuery(lookupField).on('input', function (e) {
        if (jQuery(lookupField, container).val() == '') {
            jQuery(lookupField, container).removeAttr("title");
            jQuery("#case-subject", container).addClass("d-none");
        }
    });
    if (!isFunction(callback['onEraseLookup']) || !callback['onEraseLookup']) {
        callback['onEraseLookup'] = false;
    }
    if (!isFunction(callback['onClearLookup']) || !callback['onClearLookup']) {
        callback['onClearLookup'] = false;
    }
    lookupCommonFunctions(lookupField, legalCaseIdField, errorField, container, callback['onEraseLookup'], callback['onClearLookup']);
}

/*
 * Lookup for locations
 * Retreiving locations depending on the term entered with 2 characters and above
 * @param string lookupField( jQuery selector for location lookup input ),hiddenInputIdField( jQuery selector for hidden input field ),string errorField( string name of the error field for the lookup ),string container( jQuery selector for modal container )
 */
function lookUpLocations(lookupField, hiddenInputIdField, errorField, container) {
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('location');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL() + 'tasks/location_autocomplete?term=%QUERY',
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
    jQuery(lookupField).typeahead({
        hint: false,
        highlight: true,
        minLength: 2
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                return item.location
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + data.location + '</div>'
                }
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            jQuery('.loader-submit', container).removeClass('loading');
        });

    lookupCommonFunctions(lookupField, hiddenInputIdField, errorField, container);
}

/*
 * Common actions for lookup functions
 * Actions that are common for lookup functions
 * @param string lookupField( jQuery selector for lookup input ),inputIdField( jQuery selector for hidden input field ),string errorField( string name of the error field for the lookup ),string container( jQuery selector for modal container )
 */
function lookupCommonFunctions(lookupField, inputIdField, errorField, container, onEraseLookupCallback, onClearLookup) {
    onEraseLookupCallback = onEraseLookupCallback || false;
    onClearLookup = onClearLookup || false;

    jQuery(lookupField).on('input', function (e) {
        if (!jQuery('.tt-selectable', '.twitter-typeahead').length) {
            jQuery(inputIdField, container).val('').trigger('change');

            if (onEraseLookupCallback && isFunction(onEraseLookupCallback)) {
                onEraseLookupCallback();
            }
        }

        if (jQuery(this).val() === '') {
            if (onClearLookup && isFunction(onClearLookup)) {
                onClearLookup();
            }
        }
    });

    jQuery(lookupField).bind('typeahead:select', function (ev, suggestion) {
        jQuery(inputIdField, container).val(suggestion.id).trigger('change');
        jQuery("div", container).find("[data-field=" + errorField + "]").addClass('d-none');

        if (onSelectLookupCallback && isFunction(onSelectLookupCallback)) {
            onSelectLookupCallback(ev, suggestion);
        }
    });

    jQuery(lookupField).keydown(function (e) {
        if (e.which !== 13 && e.which !== 9) { //neither enter nor tab
            jQuery(inputIdField, container).val('').trigger('change');

            if (onEraseLookupCallback && isFunction(onEraseLookupCallback)) {
                onEraseLookupCallback();
            }
        }
    });

    setTimeout(function () {
        // jQuery(lookupField, container).blur();
    }, 600);
}

/*
 * Open Task Form
 * Open task form in add/edit Task
 * @param int id (id of Task if edit), int caseId(caseId of related case to this task), int hearingId(hearing Id of case related to this task, this parameter will be used to create a task after creating a hearing so in task form the system will clone the assignee and date from hearing)
 */
function taskForm(id, caseId, hearingId, stageId, callback, noStage) {
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    id = id || false;
    caseId = caseId || 0;
    hearingId = hearingId || 0;
    stageId = stageId || false;
    callback = callback || false;
    noStage = noStage || false;
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'tasks/' + (id ? 'edit/' + id : 'add/' + caseId + '/' + hearingId + '/' + (stageId ? '/' + stageId : '')),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#task-dialog').length <= 0) {
                    jQuery('<div id="task-dialog"></div>').appendTo("body");
                    var taskDialog = jQuery('#task-dialog');
                    taskDialog.html(response.html);
                    initTinyTemp('description', "#task-dialog", "task");
                    commonModalDialogEvents(taskDialog);
                    var taskId = jQuery("#id", taskDialog).val();
                    fixDateTimeFieldDesign(taskDialog);
                    loadCustomFieldsEvents('custom-field-', taskDialog);
                    jQuery("#save-task-btn", taskDialog).click(function () {
                        taskFormSubmit(taskDialog, taskId, callback);
                    });
                    jQuery(taskDialog).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            taskFormSubmit(taskDialog, taskId, callback);
                        }
                    });
                    if (caseId && !id) {
                        jQuery('#caseLookupId', taskDialog).val(caseId);
                    }
                    taskFormEvents(taskDialog, response);
                    if (!id) {
                        jQuery('#type', taskDialog).change(function () {
                            assignmentPerType(this.value, 'task', taskDialog);
                        });
                    }
                    if (!id && caseId && !noStage) {
                        matterStageMetadata(taskDialog, caseId, stageId);
                    }
                    if (stageId === '' && typeof response.stage_html === 'undefined' && !response.stage_html) {
                        jQuery('#stage-div', taskDialog).html('');
                    } else {
                        !noStage ? litigationStageDataEvents(response.stage_html, taskDialog) : jQuery('#stage-div', taskDialog).html('');
                    }
                }
            } else {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.privateTaskMeetingMessage });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}

/**
 * conveyancing
 */

function conveyancingAddForm(caseId, stageId, callback, noStage) {
    //pine message with message that this is an HRA role.
       pinesMessage({ ty: 'warning', m:"You do not have permission to add a conveyancing record as this is an HRA Role" });
       return false;
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }

    callback = callback || false;
    noStage = noStage || false;
    callback ? conveyancingForm(false, caseId, false, stageId, callback, noStage) : conveyancingForm(false, caseId, false, stageId);
}
/**
 *
 * @param {*} id
 * @param {*} caseId
 * @param {*} hearingId
 * @param {*} stageId
 * @param {*} callback
 * @param {*} noStage
 * @returns
 */
function conveyancingForm(id, caseId, hearingId, stageId, callback, noStage) {
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    id = id || false;
    caseId = caseId || 0;
    hearingId = hearingId || 0;
    stageId = stageId || false;
    callback = callback || false;
    noStage = noStage || false;
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'legal_opinions/' + (id ? 'edit_conveyancing/' + id : 'add_conveyancing/' + caseId + '/' + hearingId + '/' + (stageId ? '/' + stageId : '')),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#opinion-dialog').length <= 0) {
                    jQuery('<div id="opinion-dialog"></div>').appendTo("body");
                    var opinionDialog = jQuery('#opinion-dialog');
                    opinionDialog.html(response.html);
                   
        
                    commonModalDialogEvents(opinionDialog);
                    var opinionId = jQuery("#id", opinionDialog).val();
                    fixDateTimeFieldDesign(opinionDialog);
                    loadCustomFieldsEvents('custom-field-', opinionDialog);

                  
                    // initTinyTemp('detailed_info', opinionDialog, "core");
                    // initTinyTemp('legal_question', opinionDialog, "core");
                    // initTinyTemp('background_info', opinionDialog, "core");

                    jQuery("#save-opinion-btn", opinionDialog).click(function () {
                        opinionFormSubmit(opinionDialog, opinionId, callback);
                    });
                    jQuery(opinionDialog).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            opinionFormSubmit(opinionDialog, opinionId, callback);
                        }
                    });
                    if (caseId && !id) {
                        jQuery('#caseLookupId', opinionDialog).val(caseId);
                    }
                    opinionFormEvents(opinionDialog, response);
                    if (!id) {
                        jQuery('#type', opinionDialog).change(function () {
                            assignmentPerType(this.value, 'opinion', opinionDialog);
                        });
                    }
                    if (!id && caseId && !noStage) {
                        matterStageMetadata(opinionDialog, caseId, stageId);
                    }
                    if (stageId === '' && typeof response.stage_html === 'undefined' && !response.stage_html) {
                        jQuery('#stage-div', opinionDialog).html('');
                    } else {
                        !noStage ? litigationStageDataEvents(response.stage_html, opinionDialog) : jQuery('#stage-div', opinionDialog).html('');
                    }
                }
            } else {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.privateOpinionMeetingMessage });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}
/**
 * 
 * @param {*} caseId 
 * @param {*} stageId 
 * @param {*} callback 
 * @param {*} noStage 
 */
function opinionAddForm(caseId, stageId, callback, noStage) {
    callback = callback || false;
    noStage = noStage || false;
    callback ? opinionForm(false, caseId, false, stageId, callback, noStage) : opinionForm(false, caseId, false, stageId);
}
/**
 * 
 * @param {*} id 
 * @param {*} caseId 
 * @param {*} hearingId 
 * @param {*} stageId 
 * @param {*} callback 
 * @param {*} noStage 
 * @returns 
 */
function opinionForm(id, caseId, hearingId, stageId, callback, noStage) {
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    id = id || false;
    caseId = caseId || 0;
    hearingId = hearingId || 0;
    stageId = stageId || false;
    callback = callback || false;
    noStage = noStage || false;
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'legal_opinions/' + (id ? 'edit/' + id : 'add/' + caseId + '/' + hearingId + '/' + (stageId ? '/' + stageId : '')),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#opinion-dialog').length <= 0) {
                    jQuery('<div id="opinion-dialog"></div>').appendTo("body");
                    var opinionDialog = jQuery('#opinion-dialog');
                    opinionDialog.html(response.html);

                    initTinyTemp('background_info', '#opinion-dialog', 'core');
                    initTinyTemp('detailed_info', '#opinion-dialog', 'core');
                    initTinyTemp('legal_question', '#opinion-dialog', 'core');


                    commonModalDialogEvents(opinionDialog);
                    var opinionId = jQuery("#id", opinionDialog).val();
                    fixDateTimeFieldDesign(opinionDialog);
                    loadCustomFieldsEvents('custom-field-', opinionDialog);
                    jQuery("#save-opinion-btn", opinionDialog).click(function () {
                        opinionFormSubmit(opinionDialog, opinionId, callback);
                    });
                    jQuery(opinionDialog).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            opinionFormSubmit(opinionDialog, opinionId, callback);
                        }
                    });
                    if (caseId && !id) {
                        jQuery('#caseLookupId', opinionDialog).val(caseId);
                    }
                    opinionFormEvents(opinionDialog, response);
                    if (!id) {
                        jQuery('#type', opinionDialog).change(function () {
                            assignmentPerType(this.value, 'opinion', opinionDialog);
                        });
                    }
                    if (!id && caseId && !noStage) {
                        matterStageMetadata(opinionDialog, caseId, stageId);
                    }
                    if (stageId === '' && typeof response.stage_html === 'undefined' && !response.stage_html) {
                        jQuery('#stage-div', opinionDialog).html('');
                    } else {
                        !noStage ? litigationStageDataEvents(response.stage_html, opinionDialog) : jQuery('#stage-div', opinionDialog).html('');
                    }
                }
            } else {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.privateOpinionMeetingMessage });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}
/**
 * 
 * @param {*} container 
 * @param {*} data 
 */
function opinionFormEvents(container, data) {
    setDatePicker('#form-due-date', container);
    if (typeof data.restricted_by_opinion_reporter !== "undefined" && data.restricted_by_opinion_reporter) {
        jQuery('#form-due-date-input', container).attr("disabled", "disabled");
        tinyMCE.get('background_info').setMode('readonly');
        tinyMCE.get('detailed_info').setMode('readonly');
        tinyMCE.get('legal_question').setMode('readonly');
        jQuery('#assignedToLookUp', container).attr("disabled", "disabled");
        jQuery('#type', container).attr("disabled", "disabled");
        jQuery('#quickAddButton', container).attr("disabled", "disabled");
        jQuery('#reporterLookUp', container).attr("disabled", "disabled");
        jQuery('#assignToMeLinkId', container).addClass("disabled-anchor");
        jQuery('#notify-me-link', container).addClass("disabled-anchor");
        jQuery('#date-conversion', container).addClass("disabled-anchor");
        jQuery('.assign-to-me-link-id-wrapper', container).addClass("drop-pointer");
        jQuery(".restriction-tooltip", container).each(function () {
            jQuery(this).append('<span title="' + _lang.onlyReporterCanEdit + '" class="tooltip-title m-h-3"><i class="fa-solid fa-circle-question purple_color"></i></span>');
        });
    }
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#due-date', container), jQuery('#due-date-container', container));
    }
    notifyMeBeforeEvent({ 'input': 'due-date-input', 'inputContainer': 'due-date' }, container, true);
    jQuery('.select-picker', '#opinion-dialog').selectpicker();
    jQuery('#type', '#opinion-dialog').on('shown.bs.select', function (e) {
        jQuery('.dropdown-menu.inner').animate({
            scrollTop: jQuery(".selected").offset().top
        }, "fast");
        jQuery('.modal-body').animate({
            scrollTop: '0px'
        }, "fast");
    });
    var moreFilters = { 'keyName': 'isDeleted', 'value': '0', 'resultHandler': fetchCaseRelatedDataToHearingFrom };
    lookUpCases(jQuery('#caseLookup', container), jQuery('#caseLookupId', container), 'legal_case_id', container, moreFilters);
    lookUpUsers(jQuery('#assignedToLookUp', container), jQuery('#assignedToId', container), 'assigned_to', jQuery('.assignee-container', container), container);
    jQuery('#assignedToId', container).change(function () {
        if (jQuery('#user-relation', container).val() > 0 && jQuery('#assignedToId', container).val() > 0 && jQuery('#user-relation', container).val() !== jQuery('#assignedToId', container).val()) {
            pinesMessage({ ty: 'warning', m: _lang.feedback_messages.assignmentChangeForAssignee });
        }
    });
    lookUpUsers(jQuery('#reporterLookUp', container), jQuery('#reporter-id', container), 'reporter', jQuery('.reporter-container', container), container);
    lookUpOpinionLocations(jQuery('#location', container), jQuery('#opinion_location_id', container), 'opinion_location_id', container);
    lookupPrivateUsers(jQuery('#lookupOpinionUsers', container), 'Opinion_Users', '#selected_opinion_users', 'users-lookup-container', jQuery('#opinion-form'));
    lookupPrivateUsers(jQuery('#contributors-lookup', container), 'contributors', '#selected-contributors', 'contributors-container', container);
    lookUpContracts({
        'lookupField': jQuery('#lookup-contract', container),
        'hiddenId': jQuery('#lookup-contract-id', container),
        'errorDiv': 'contract_id'
    }, container);
    checkBoxContainersValues({ 'contributors-container': jQuery('#selected-contributors', container) }, container);
    enableDisableOpinionUsersLookup();
    initializeModalSize(container);
}
/**
 * 
 */


function enableDisableOpinionUsersLookup() {
    var opinionDialog = jQuery('#opinion-dialog');
    var lookupOpinionUsers = jQuery('#lookupOpinionUsers', opinionDialog), privateOpinion = jQuery('#private', opinionDialog);
    if (privateOpinion.is(':checked')) {
        jQuery('.opinion-users-container', opinionDialog).removeClass('d-none');
        jQuery('.modal-body', opinionDialog).scrollTo(jQuery("#selected_opinion_users", opinionDialog));
        lookupOpinionUsers.focus();
    } else {
        jQuery('.opinion-users-container', opinionDialog).addClass('d-none')
    }
}
/**
 * 
 * @param {*} container 
 * @param {*} id 
 * @param {*} callback 
 */

function opinionFormSubmit(container, id, callback) {
    id = id || false;
    var formData = new FormData(document.getElementById(jQuery("form#opinion-form", container).attr('id')));
    formData.append('detailed_info', tinymce.activeEditor.getContent());
    formData.append('legal_question', tinymce.get('legal_question').getContent());
    formData.append('background_info', tinymce.get('background_info').getContent());
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: getBaseURL() + 'legal_opinions/' + (id ? 'edit/' + id : 'add'),
        success: function (response) {
            if (response.totalNotifications >= 1) {
                jQuery('#pendingNotifications').css('display', 'inline-block').text(response.totalNotifications);
            } else {
                jQuery('#pendingNotifications').html('');
            }
            jQuery('.inline-error', '#opinion-dialog').addClass('d-none');
            if (response.result) {
                if (jQuery('#notify-me-before-container', container).is(':visible')) {
                    loadUserLatestReminders('refresh');
                }
                if (!response.cloned && typeof opinionCallBack === "function") {
                    opinionCallBack();
                }
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('opinions');
                    pieCharts();
                }
                updateGetingStartedSteps('opinion');
                var msg = _lang.feedback_messages.addedNewOpinionSuccessfully.sprintf(['<a href="' + getBaseURL() + 'legal_opinions/view/' + response.id + '">' + response.opinion_code + '</a>']);
                pinesMessage({ ty: 'success', m: id ? _lang.feedback_messages.updatesSavedSuccessfully : msg });
                if (!response.cloned) {
                    jQuery('.modal', '#opinion-dialog').modal('hide');
                } else {
                    jQuery("#clone", container).val("no");
                    assignmentPerType(jQuery('#type', container).val(), 'opinion', container);
                }
                if (isFunction(callback)) {
                    callback(jQuery("#stage-id").val());
                }
            } else {
                displayValidationErrors(response.validationErrors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
/**
 * 
 * @param {*} lookupField 
 */


function opinionLocationLookup(lookupField) {
    $lookupField = jQuery('#' + lookupField);
    $lookupField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'legal_opinions/location_autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: { id: -1, term: request.term }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return { label: item.location, value: item.location, record: item }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 2,
        select: function (event, ui) {
            for (i in ui.item.record)
                if (ui.item.record.id > 0) {
                    jQuery('#opinion_location_id').val(ui.item.record.id);
                }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}
/**
 * 
 * @param {*} id 
 * @param {*} e 
 * @param {*} callback 
 */

function opinionEditForm(id, e, callback) {
    callback = callback || false;
    if ('undefined' !== typeof e && e) {
        e.preventDefault();
    }
    callback ? opinionForm(id, false, false, false, callback) : opinionForm(id, false);
}

/**
 * 
 * @param {*} lookupField 
 * @param {*} hiddenInputIdField 
 * @param {*} errorField 
 * @param {*} container 
 */
function lookUpOpinionLocations(lookupField, hiddenInputIdField, errorField, container) {
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('location');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL() + 'legal_opinions/location_autocomplete?term=%QUERY',
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
    jQuery(lookupField).typeahead({
        hint: false,
        highlight: true,
        minLength: 2
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                return item.location
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + data.location + '</div>'
                }
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            jQuery('.loader-submit', container).removeClass('loading');
        });

    lookupCommonFunctions(lookupField, hiddenInputIdField, errorField, container);
}
/**
 * 
 * @param {*} id 
 * @param {*} workflowId 
 */

function opinionStatusForm(id, workflowId) {
    id = id || false;
    workflowId = workflowId || false;
    jQuery.ajax({
        url: getBaseURL() + 'opinion_statuses/' + (id ? 'edit/' + id : 'add'),
        type: "GET",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var opinionStatusId = "#opinion-status-container";
                if (jQuery(opinionStatusId).length <= 0) {
                    jQuery("<div id='opinion-status-container'></div>").appendTo("body");
                    var opinionStatusContainer = jQuery(opinionStatusId);
                    opinionStatusContainer.html(response.html);
                    commonModalDialogEvents(opinionStatusContainer);
                    jQuery("#form-submit", opinionStatusContainer).click(function () {
                        opinionStatusFormSubmit(id, workflowId, opinionStatusContainer);
                    });
                    jQuery(opinionStatusContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            opinionStatusFormSubmit(id, workflowId, opinionStatusContainer);
                        }
                    });
                    resizeMiniModal(opinionStatusContainer);
                    jQuery(window).bind('resize', (function () {
                        resizeMiniModal(opinionStatusContainer);
                    }));
                    showToolTip();
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function opinionStatusFormSubmit(id, workflowId, container) {
    id = id || false;
    workflowId = workflowId || false;
    var formData = jQuery('#opinion-status-form', container).serializeArray();
    if (workflowId) {
        formData.push({ name: "workflow_id", value: workflowId });
    }
    jQuery.ajax({
        url: getBaseURL() + 'opinion_statuses/' + (id ? 'edit/' + id : 'add'),
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                window.location = getBaseURL() + 'opinion_workflows/index/' + workflowId;

            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
    });
}

// new design add scroll top to kendo grid content when clicking on actions wheels if drop down menu don't fit
function animateDropdownMenuInGridsV2(gridId, scrollTop) {
    scrollTop = scrollTop || 85;
    jQuery('.dropdown-toggle', '#' + gridId + ' .k-grid-content').click(function () {
        let offset = jQuery(this).offset();
        gridContentSetAnimation(offset.top);
    });
    jQuery('.dropdown .dropdown-menu', '#' + gridId + ' .k-grid-content').mouseleave(function () {
        jQuery(this.parentNode).removeClass('open');
    });

    function gridContentSetAnimation(offset) {
        let gridHeight = jQuery('.grid-container').prop("scrollHeight")
        if (offset > gridHeight) {
            let gridSpace = jQuery('.k-grid-content');
            gridSpace.animate({ scrollTop: gridSpace.scrollTop() + scrollTop });
        }
    }
}

function fixGridHeader(pageLoad) {
    pageLoad = pageLoad || false;
    if (jQuery('.grid-header', '.grid-main-container').length) { // new grid designs where grid header does not included in kendo grid context
        // fix grid header
        if (!jQuery('#grid-header-fixed-navbar').length) {
            jQuery('.grid-header', '.grid-main-container').wrap('<nav id="grid-header-fixed-navbar"></nav>');
        }
        var marginTop = jQuery('#header_div').outerHeight(true);
        var headerWidth = jQuery('.k-grid-header-wrap').find('table').width() > screen.width ? jQuery('.k-grid-header-wrap').find('table').width() : screen.width;
        if (jQuery('#grid-scrollable').length) {
            jQuery('#grid-header-fixed-navbar', '#grid-scrollable').attr('style', 'width: ' + headerWidth + 'px !important');
            jQuery('.grid-container').attr('style', 'width: ' + headerWidth + 'px !important');
            jQuery('.footer-bg').attr('style', 'width: ' + headerWidth + 'px !important ;' + 'background-color:' + jQuery('.footer').css('background-color') + '!important');
            jQuery('.nav-money-bg').attr('style', 'width: ' + headerWidth + 'px !important ;' + 'background-color:' + jQuery('#subNavMenu').css('background-color') + '!important');
            jQuery('.grid-header').attr('style', 'width: ' + headerWidth + 'px !important');
        }
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
        var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);
        if (msie > 0 || isIE11) {
            jQuery('.grid-container').css('min-width', jQuery('#header_div').width());
        }
        jQuery('#grid-header-fixed-navbar').scrollToFixed({ marginTop: marginTop, zIndex: 100 });
        if (pageLoad && msie < 0 && !isIE11 && jQuery(window).outerWidth() > 994) {
            jQuery('#grid-header-fixed-navbar', '#grid-unscrollable').css('width', 'inherit');
        }
        jQuery('#grid-header-fixed-navbar').next().css('width', 'inherit');
        // fix kendo grid columns
        if (!jQuery('#fixed-navbar').length) {
            jQuery('.k-grid-toolbar').remove();
            jQuery('.k-grid-header').wrap('<nav id="fixed-navbar"></nav>');
            jQuery('.k-grid-header').appendTo('#fixed-navbar');
        }
        var chromeAgent = navigator.userAgent.indexOf("Chrome") > -1;
        if (pageLoad && msie < 0 && !isIE11) {
            if (_lang.languageSettings['langName'] !== 'arabic' || (jQuery('#grid-scrollable').length && _lang.languageSettings['langName'] === 'arabic' && !chromeAgent) || (!jQuery('#grid-scrollable').length && _lang.languageSettings['langName'] === 'arabic') && jQuery(window).outerWidth() > 994) {
                jQuery('#fixed-navbar', '.grid-main-container').addClass('inherited-width');
            }
        }

        if (msie < 0 && !isIE11 && _lang.languageSettings['langName'] === 'arabic') {
            jQuery('.k-grid-header', '.grid-main-container').addClass('padding-right');
        }
        marginTop = marginTop + jQuery('#grid-header-fixed-navbar').outerHeight(true);
        jQuery('#fixed-navbar').scrollToFixed({ marginTop: marginTop, zIndex: 99 });
        if (pageLoad) {
            jQuery('#fixed-navbar', '#grid-unscrollable').next().css('width', 'inherit');
        }
        reorderGridPagination();
        resizeHeaderAndFooter();
    } else {
        //the first two record used to put the quick search div and the kendo grid header div in the same component with specific id (fixed-navbar) in order to fix it when scroling
        jQuery('.k-grid-toolbar').wrap('<nav id="fixed-navbar"></nav>');
        // fix kendo grid columns and toolbar (header nav)
        jQuery('.k-grid-header').appendTo('#fixed-navbar');
        jQuery('#fixed-navbar').scrollToFixed({ marginTop: jQuery('#header_div').outerHeight(true) });
    }
}

function reorderGridPagination() {
    if (jQuery('.k-grid-info-refresh', '.grid-main-container').length == 0) {
        jQuery('.k-pager-refresh', '.grid-main-container').wrap('<div class="k-grid-info-refresh"></div>');
        jQuery('.k-pager-info', '.grid-main-container').appendTo('.k-grid-info-refresh');
    }
}

function reinitializeGridFixedNav() {
    // destroy fixed nav (grid title and columns) and re-initialize fixed header after opening the adv. search panel
    jQuery('#grid-header-fixed-navbar').trigger('detach.ScrollToFixed');
    jQuery('#fixed-navbar').trigger('detach.ScrollToFixed');
    setTimeout(function () {
        fixGridHeader();
        fixFooterPosition();
    }, 400);
}

/*
 * Logo Position
 * Set the width of the logo + Position the logo (top)
 */
function logoPosition() {
    jQuery("#image").width(jQuery(".logo").width());
    var windowHeight = jQuery(window).height();
    var logContainerHeight = jQuery("#main-container").height();
    var headerHeight = jQuery(".login-header").height();
    var top = (windowHeight - headerHeight - logContainerHeight) / 5;
    jQuery("#main-container").css("top", top + "px");
}

/*
 * Lookup for Private Users
 * Retreiving users on click on the input(retreive all users) or searching for users(depending on the term) => On selecting users ,users will be added to the box of private users
 * @param string lookupFieldId( jQuery selector for lookup input ),string lookupFieldName( lookup field name ),string boxContainerId( jQuery selector for box container of the lookup fields ),string lookupContainerClass( jQuery selector for lookup container ),string formContainerId( jQuery selector for form container )
 */
function lookupPrivateUsers(lookupFieldId, lookupFieldName, boxContainerId, lookupContainerClass, formContainerId, formId) {
    formId = formId || '';
    formContainerId = '#' + formContainerId.attr('id');
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('firstName');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL() + 'users/autocomplete/active?term=%QUERY',
            filter: function (data) {
                var sanitized_data = [];
                data.forEach(function (element) {
                    if (element.firstName) {
                        element.firstName = escapeHtml(element.firstName);
                    }
                    if (element.lastName) {
                        element.lastName = escapeHtml(element.lastName);
                    }
                    if (element.email) {
                        element.email = escapeHtml(element.email);
                    }
                    sanitized_data.push(element);
                });
                return sanitized_data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupFieldId).typeahead({
        hint: false,
        highlight: true,
        minLength: 0
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + data.firstName + ' ' + data.lastName + '</div>'
                    header: '<div class="suggestions-header">' + _lang.allUsers + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            setNewBoxElement(boxContainerId, lookupContainerClass, formContainerId, {
                id: datum.id,
                value: datum.firstName + ' ' + datum.lastName,
                name: lookupFieldName,
                formId: formId
            });
            lookupBoxContainerDesign(jQuery('.' + lookupContainerClass, formContainerId));
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', formContainerId).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            jQuery('.loader-submit', formContainerId).removeClass('loading');
            highLightFirstSuggestion();
        }).on('focus', function () {
            highLightFirstSuggestion();
        });
}

function lookupApproversSignees(lookupFieldId, approverSigneeFieldName, approverSigneeTypeFieldName, boxContainerId, lookupContainerClass, formContainerId, formId, matrixType, isTable) {
    formId = formId || '';
    isTable = isTable || false;
    formContainerId = '#' + formContainerId.attr('id');
    var lookupField = matrixType == 'approval' ? 'approver' : 'signee';
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('firstName');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL('contract') + 'contracts/approvers_signees_autocomplete?term=%QUERY&lookup_field=' + lookupField,
            filter: function (data) {
                var sanitized_data = [];
                data.forEach(function (element) {
                    if (element.firstName) {
                        element.firstName = escapeHtml(element.firstName);
                    }
                    if (element.lastName) {
                        element.lastName = escapeHtml(element.lastName);
                    }
                    if (element.type) {
                        element.type = escapeHtml(element.type);
                    }
                    sanitized_data.push(element);
                });
                return sanitized_data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupFieldId).typeahead({
        hint: false,
        highlight: true,
        minLength: 0
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + data.firstName + ' ' + data.lastName + ' (' + data.type + ')</div>'
                    header: '<div class="suggestions-header">' + _lang.allUsers + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            var wrapper = jQuery(boxContainerId, formContainerId);
            var lookupBoxContainer = jQuery('.' + lookupContainerClass, formContainerId);
            var setOption = {
                id: datum.id,
                type: datum.type,
                value: datum.firstName + ' ' + datum.lastName,
                name: approverSigneeFieldName,
                typeFieldName: approverSigneeTypeFieldName,
                formId: formId
            };
            lookupBoxContainerDesign(lookupBoxContainer);
            var idAttend = setOption.type + setOption.id;
            if (setOption.id && !jQuery('#' + idAttend, wrapper).length) {
                var formID = setOption.formId != undefined && setOption.formId ? 'form="' + setOption.formId + '"' : "";
                if (isTable) {
                    var rowCount = jQuery('tbody', formContainerId).attr('data-count-row');
                    wrapper.append(jQuery('<div class="col-md-12 padding-a-5"><div class="multi-option-selected-items-new padding-a-5" id="' + idAttend + '">' + '<span id="' + setOption.type + setOption.id + '" class="padding-h-5"><span class="selected-name">' + setOption.value + '</span> (' + setOption.type + ')</span><input type="hidden" value="' + setOption.id + '" name="' + setOption.name + '"' + formID + ' /><input type="hidden" value="' + setOption.type + '" name="' + setOption.typeFieldName + '"' + formID + ' /><a href="javascript:;" class="btn btn-sm btn-link pull-right remove-button" tabindex="-1" onclick="removeBoxElement(jQuery(this.parentNode),\'' + boxContainerId + '\',\'' + lookupContainerClass + '\',\'' + formContainerId + '\');"><i class="fa fa-remove margin-bottom-15"></i></a></div></div>'));
                } else {
                    wrapper.append(jQuery('<div class="m-0 col-md-6 padding-a-5" id="box-' + setOption.type + setOption.id + '"><div class="col-md-12 multi-option-selected-items-new padding-a-5" id="' + idAttend + '">' + '<span id="' + setOption.type + setOption.id + '" class="padding-h-5"><span class="selected-name">' + setOption.value + '</span> (' + setOption.type + ')</span><input type="hidden" value="' + setOption.id + '" name="' + setOption.name + '"' + formID + ' /><input type="hidden" value="' + setOption.type + '" name="' + setOption.typeFieldName + '"' + formID + ' /><a href="javascript:;" class="btn btn-sm btn-link pull-right remove-button" tabindex="-1" onclick="removeBoxElement(\'#box-' + setOption.type + setOption.id + '\', \'' + boxContainerId + '\',\'' + lookupContainerClass + '\',\'' + formContainerId + '\');"><i class="fa fa-remove"></i></a></div></div>'));
                }
            }
            lookupBoxContainerDesign(jQuery('.' + lookupContainerClass, formContainerId));
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', formContainerId).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            jQuery('.loader-submit', formContainerId).removeClass('loading');
            highLightFirstSuggestion();
        }).on('focus', function () {
            highLightFirstSuggestion();
        });
}

/*
 *Destroy Modal
 *Destroy modal on close/cancel/save/escape of the modal
 *To avoid the problem when opening dialogs on the fly(small popups)in jquery dialog then openning modal dialog and the dialog on the fly
 *(the dialog on the fly will have the html before the modal dialog and thus the dialog on the fly will open first not over the main dialog)
 *@param string container( jQuery selector for modal container )
 */
function destroyModal(container) {
    container.remove();
}

function contractSubTypeFormEvents(formContainer, response) {
    formContainer = formContainer || false;
    response = response || false;
    if (response.id) {
        jQuery('#type', '#' + formContainer).val(jQuery('#type-id', ".administration-dialog-container").val()).selectpicker("refresh");
        contractTypeEvent(jQuery('#type', '#' + formContainer).val(), jQuery("#sub-type", '#' + formContainer), response.id);
    } else {
        jQuery('#type-id', ".administration-dialog-container").val(jQuery('#type', '#' + formContainer).val());
    }
}

/*
 * Add administration type on the fly
 * @parm string componentType(The field that we are adding. Required.),string formContainer( jQuery selector for modal container ),boolean isDialog (add event is coming from a dialog container)
 */
function quickAdministrationDialog(componentType, formContainer, isDialog, caseType, resultHandler, dropdownId, dialogTitle, module, formEventsFunction) {
    isDialog = isDialog || false;
    caseType = caseType || '';
    dialogTitle = dialogTitle || false;
    resultHandler = resultHandler || false;
    formEventsFunction = formEventsFunction || false;
    module = module || false;
    dropdownId = dropdownId || false; // is used to differentiate between multiple elements in form when they have same data field like opponent positions
    url = (module ? getBaseURL(module) : getBaseURL()) + componentType + '/add/';
    jQuery.ajax({
        url: url,
        dataType: 'JSON',
        type: 'GET',
        data: {
            quick_add_form: true,
            is_new_form: true
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery(".administration-dialog-container").length <= 0) {
                    jQuery('<div class="d-none administration-dialog-container"></div>').appendTo("body");
                    var administrationDialogContainer = jQuery('.administration-dialog-container');
                    administrationDialogContainer.html(response.html).removeClass('d-none');

                    if (dialogTitle) {
                        administrationDialogContainer.find('.modal-title').html(dialogTitle);
                    }

                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery('.modal', administrationDialogContainer).modal('hide');
                        }
                    });
                    jQuery('.modal', administrationDialogContainer).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'

                    });
                    resizeMiniModal(administrationDialogContainer);
                    jQuery(window).bind('resize', (function () {
                        resizeMiniModal(administrationDialogContainer);
                    }));

                    if (typeof formEventsFunction === "function") {
                        formEventsFunction(jQuery(formContainer).attr('id'));
                    }
                    jQuery('.select-picker', administrationDialogContainer).selectpicker({ dropupAuto: false });

                    jQuery("#administration-dialog-submit").click(function () {
                        var formData = jQuery("form#administration-dialog-form", administrationDialogContainer).serializeArray();
                        if (caseType) {
                            formData.push({ name: "caseType", value: caseType });
                        }
                        jQuery.ajax({
                            beforeSend: function () {
                                jQuery('.loader-submit', administrationDialogContainer).addClass('loading');
                                jQuery('#administration-dialog-submit', administrationDialogContainer).attr('disabled', 'disabled');
                            },
                            data: formData,
                            dataType: 'JSON',
                            type: 'POST',
                            url: url,
                            success: function (response) {
                                jQuery('.inline-error', administrationDialogContainer).html('');
                                if (undefined !== response.validationErrors) {
                                    for (i in response.validationErrors) {
                                        jQuery('.validation-error-container', '#administration-dialog-form').removeClass('d-none');
                                        jQuery("div", administrationDialogContainer).find("[data-field=" + i + "]").html(response.validationErrors[i]);

                                    }
                                } else {
                                    jQuery('.modal', administrationDialogContainer).modal('hide');
                                    var administrationComponent = '';
                                    administrationComponent = "administration-" + componentType;
                                    var administrationComponentType = jQuery("div", formContainer).find("[data-field=" + administrationComponent + "]");
                                    if (administrationComponent == 'administration-task_locations') {//update the lookup
                                        jQuery('#task_location_id', formContainer).val(response.id);
                                        administrationComponentType.val(response.name);
                                        if (jQuery('.twitter-typeahead', formContainer).length) {
                                            jQuery(administrationComponentType).typeahead('destroy');
                                            lookUpLocations(administrationComponentType, jQuery('#task_location_id', formContainer), 'task_location_id');
                                        }
                                    } else {//update the dropdown
                                        administrationComponentType.append(jQuery("<option/>", {
                                            value: response.id,
                                            text: response.name
                                        }));

                                        if (dropdownId) {
                                            dropdownId.val(response.id);
                                        } else {
                                            administrationComponentType.val(response.id);
                                        }
                                        if (isDialog) {
                                            administrationComponentType.selectpicker('refresh');
                                        } else {
                                            administrationComponentType.trigger("chosen:updated");
                                        }
                                    }
                                    if (typeof resultHandler === "function") {
                                        setTimeout(function () {
                                            resultHandler(jQuery(formContainer).attr('id'), response);
                                        }, 50);
                                    }
                                    if (!isDialog) {
                                        setTimeout(function () { location.reload(); }, 1000)
                                        pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.recordSaved });
                                    }
                                }

                                if (componentType === 'task_types') {
                                    enableDisableTaskUsersLookup();
                                }
                            }, complete: function () {
                                jQuery('.loader-submit', administrationDialogContainer).removeClass('loading');
                                jQuery('#administration-dialog-submit', administrationDialogContainer).removeAttr('disabled');

                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                    });
                    jQuery("input", administrationDialogContainer).keypress(function (e) {
                        if (e.which == 13) {
                            e.preventDefault();
                            jQuery("#administration-dialog-submit", administrationDialogContainer).click();
                        }
                    });
                    jQuery('.modal-body').on("scroll", function () {
                        jQuery('.bootstrap-select.open').removeClass('open');
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(administrationDialogContainer);
                    });
                    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
                        jQuery('#field-name', administrationDialogContainer).focus();

                    });
                    ctrlS(function () {
                        jQuery("#administration-dialog-submit", administrationDialogContainer).click();
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/*
 * Add new contact
 * Open contact dialog to add
 * @param boolean enableCreateAnother(whether to show the create another button or no),string cloneFromId( id of the contact in edit mode )
 */
function contactAddForm(enableCreateAnother, cloneFromId) {
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var randomNumber = Math.round(Math.random() * (upperBound - lowerBound) + lowerBound);

    var queryParams = '';
    if (companyContactFormMatrix.contactDialog.customData && companyContactFormMatrix.contactDialog.customData.fromAdvisor)
        queryParams = '?fromAdvisor=true';

    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + (cloneFromId ? ('contacts/clone_contact/' + cloneFromId) : ('contacts/add' + queryParams)),
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
                        "customData": companyContactFormMatrix.contactDialog.customData,
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
                    setTimeout(addContactDemo, 1500);
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
    lookUpNationalities(jQuery('#lookup-nationalities', container), "Contact_Nationalities", jQuery('.nationality-lookup-container', container), container);
    var lookupDetails = {
        'lookupField': jQuery('#lookupCompanies', container),
        'errorDiv': 'companies_contacts',
        'lookupContainer': jQuery('.company-lookup-container', container),
        'resultHandler': setSelectedCompanyToContact
    };
    lookUpCompanies(lookupDetails, jQuery(container));
    lookupPrivateUsers(jQuery('#lookup-contact-users', container), 'Contact_Users', '#selected-watchers', 'contact-privacy-container', jQuery(container));
    lookupTypeToCopyAddress(jQuery('#copyAddressFromLookup', container), jQuery(container), true);
    initializeModalSize(container);
    jQuery("#add-contact-sub-categories", container).click(function () { //quick add contact / company sub-category
        quickAdministrationDialog('contact_company_sub_categories', container, true);
    });
    jQuery("#add-contact-title", container).click(function () { //quick add contact title
        quickAdministrationDialog('titles', container, true);
    });
    jQuery("#privateLink", container).click(function () { //set the contact as private
        setAsPrivate(jQuery('#contact-privacy-container', container), 'contacts', '', '', '', true);
    });
    jQuery("#publicLink", container).click(function () { //set the contact as public
        setAsPublic(jQuery('#contact-privacy-container', container), 'contacts', true);
    });
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
    jQuery("#toggle-copy-address-from", container).click(function () { //show copy address from container
        toggleCopyAddressFrom(jQuery(container));
    });
    jQuery("#isLawyerCheck", container).on('change', function () { //set lawyer to yes if checbox is checked else no
        jQuery('#isLawyer', container).val(this.checked ? 'yes' : 'no');
    });
    jQuery("#lawyerForCompanyCheck", container).on('change', function () { //set inhouse lawyer to yes if checbox is checked else no
        jQuery('#lawyerForCompany', container).val(this.checked ? 'yes' : 'no');
    });
    jQuery("#shared-with-users", container).click(function () {
        jQuery('#lookup-contact-users', container).focus();
    });
    jQuery("#save-contact-btn", container).click(function () {
        contactAddFormSubmit(container);
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
            contactAddFormSubmit(container);
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

/*
 *Remove element from the mutli option box
 *If the box is empty(no element left) then remove the border of the box and add re show the inline text of the lookup
 *@param string element( jQuery selector for the element to remove ),string container( jQuery selector for the box container ),string lookupClassContainer( jQuery selector for lookup container ),string formContainer( jQuery selector for form container )
 */
function removeBoxElement(element, container, lookupClassContainer, formContainer) {
    var boxContainer = jQuery(container, formContainer);
    var lookupContainer = jQuery('.' + lookupClassContainer, formContainer);
    jQuery(element, boxContainer).remove();
    if (jQuery.trim(boxContainer.html()) == '') {
        jQuery('.lookup-box-container', lookupContainer).removeClass('margin-bottom');
        jQuery('.autocomplete-helper', lookupContainer).removeClass('d-none');
        boxContainer.css('border', 'none');
        boxContainer.removeClass('border');
    }
}


/*
 *Clone Dialog
 * trigger submit of the form according to the container
 * @param string container( jQuery selector for modal container ),function submitFunction to be triggered
 */
function cloneDialog(container, submitFunction) {
    jQuery("#clone", container).val("yes");
    submitFunction(container);
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
 *Check if box container of lookup has value(clone event)
 *If yes,add css and trigger the lookupBoxContainerDesign function
 * @param array boxContainersArray (array of lookup containers), container( jQuery selector for lookup container )
 */
function checkBoxContainersValues(boxContainersArray, container) {
    for (i in boxContainersArray) {
        if (jQuery.trim(boxContainersArray[i].html()) !== '') {
            lookupBoxContainerDesign(jQuery("." + i, container));
            boxContainersArray[i].css('border', 'solid 1px #ddd');
        }
    }
}

/*
 *Add element to the box container of the lookup
 * @param string containerId (jQuery selector for box container ),  string lookupClassContainer (jQuery selector for lookup container ), string lookupClassContainer (jQuery selector for form container ), array setOption (array of id ,value and name)
 */
function setNewBoxElement(containerId, lookupClassContainer, formContainer, setOption) {
    var wrapper = jQuery(containerId, formContainer);
    var lookupBoxContainer = jQuery('.' + lookupClassContainer, formContainer);
    lookupBoxContainerDesign(lookupBoxContainer);
    // fix duplicate in add event in Litigation Cases
    var idAttend = setOption.name + setOption.id;
    if (setOption.name == 'calendar[attendees]') {
        idAttend = 'cases_calendar_attende_' + setOption.id;
    }
    if (setOption.id && !jQuery('#' + idAttend, wrapper).length) {
        wrapper.css('border', 'solid 1px #ddd');
        if (jQuery('#meeting-dialog').is(':visible') || jQuery('#case-event-form-container').is(':visible')) {
            if (jQuery('#meeting-dialog #event-id').val()) {
                // custome from meeting dialog edit an image and attend status (mandatory - optional) (participant - none participant)
                wrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px"  onmouseout="checkParticipant(\'' + setOption.id + '\')" onmouseover="showParticipant(\'' + setOption.id + '\')"  id="' + idAttend + '"><img class="img-circle" width="30" src="' + 'users/get_profile_picture/' + setOption.id + '/1' + '" >' + '<span id="' + setOption.id + '">' + setOption.value + '</span> </div>').append(jQuery('<input type="hidden" value="' + setOption.id + '" name="' + setOption.name + '[]" />')).append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button remove-button-event flex-end-item" tabindex="-1" onclick="removeBoxElement(jQuery(this.parentNode),\'' + containerId + '\',\'' + lookupClassContainer + '\',\'' + formContainer + '\');"><i class="fa-solid fa-x"></i></a> <input id="input_participant_' + setOption.id + '" type="hidden" name="participant[]" value="0" > <input id="input_attend_status_' + setOption.id + '" type="hidden" name="mandatory[]" value="1" ><a class="attend_status"  href="javascript:;" id="attend_status_' + setOption.id + '" data-type="optional" onclick="changeAttendStatus(\'' + setOption.id + '\');" ><img width="25" src="assets/images/icons/mark_mandatory.png" ><span></span></a> <a class="attend_status hide"  id="participant_status_' + setOption.id + '" onclick="changeParticipantStatus(\'' + setOption.id + '\');"  href="javascript:;" ><img class="img-circle tooltipTable"  tooltipTitle="' + _lang.mark_participant + '"  src="assets/images/icons/unparticipant.png" ></a>')));
                runTooltipMeeting("#attend_status_" + setOption.id + " img", _lang.mark_optional);
                runTooltipMeeting("#participant_status_" + setOption.id + " img", _lang.mark_participant);
            } else {
                // custome from meeting dialog add an image and attend status (mandatory - optional)
                var formID = setOption.formId != undefined ? 'form="' + setOption.formId + '"' : "";
                wrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="' + idAttend + '"><img class="img-circle" width="30" src="' + 'users/get_profile_picture/' + setOption.id + '/1' + '" >' + '<span id="' + setOption.id + '">' + setOption.value + '</span> </div>').append(jQuery('<input type="hidden" value="' + setOption.id + '" name="' + setOption.name + '[]" />')).append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button remove-button-event flex-end-item" tabindex="-1" onclick="removeBoxElement(jQuery(this.parentNode),\'' + containerId + '\',\'' + lookupClassContainer + '\',\'' + formContainer + '\');"><i class="fa-solid fa-xmark"></i></a> <input id="input_attend_status_' + setOption.id + '" type="hidden" name="mandatory[]" value="1" ><a class="attend_status"  href="javascript:;" id="attend_status_' + setOption.id + '" data-type="optional" onclick="changeAttendStatus(\'' + setOption.id + '\');" ><img width="25" src="assets/images/icons/mark_mandatory.png" ><span></span></a>')));
                runTooltipMeeting("#attend_status_" + setOption.id + " img", _lang.mark_optional);
            }
        } else if (typeof setOption.isAzure !== 'undefined') {
            wrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="' + idAttend + '">' + '<span id="' + setOption.id + '">' + setOption.value + '</span> </div>')
                .append(jQuery('<input type="hidden" value="' + setOption.email + '" name="' + 'users[email]' + '[]" />'))
                .append(jQuery('<input type="hidden" value="' + setOption.firstName + '" name="' + 'users[firstname]' + '[]" />'))
                .append(jQuery('<input type="hidden" value="' + setOption.lastName + '" name="' + 'users[lastname]' + '[]" />'))
                .append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button flex-end-item" tabindex="-1" onclick="removeBoxElement(jQuery(this.parentNode),\'' + containerId + '\',\'' + lookupClassContainer + '\',\'' + formContainer + '\');"><i class="fa-solid fa-xmark"></i></a>')));
        } else if (typeof setOption.isIdp !== 'undefined') {
            wrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="' + idAttend + '">' + '<span id="' + setOption.id + '">' + setOption.value + '</span> </div>')
                .append(jQuery('<input type="hidden" class="idp-users" value="' + setOption.email + '" data-email="' + setOption.email + '" data-firstname="' + setOption.firstName + '" data-lastname="' + setOption.lastName + '" />'))
                .append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button flex-end-item" tabindex="-1" onclick="removeBoxElement(jQuery(this.parentNode),\'' + containerId + '\',\'' + lookupClassContainer + '\',\'' + formContainer + '\');"><i class="fa-solid fa-x"></i></a>')));
        } else {
            var formID = setOption.formId != undefined && setOption.formId ? 'form="' + setOption.formId + '"' : "";
            wrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="' + idAttend + '">' + '<span id="' + setOption.id + '">' + setOption.value + '</span> </div>').append(jQuery('<input type="hidden" value="' + setOption.id + '" name="' + setOption.name + '[]"  ' + formID + ' />')).append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button flex-end-item" tabindex="-1" onclick="removeBoxElement(jQuery(this.parentNode),\'' + containerId + '\',\'' + lookupClassContainer + '\',\'' + formContainer + '\');"><i class="fa-solid fa-xmark"></i></a>')));
        }
    }
}

/*
 *Add company
 *Open company dialog and load the events
 */
function companyAddForm() {
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var randomNumber = Math.round(Math.random() * (upperBound - lowerBound) + lowerBound);
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'companies/add',
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
                    jQuery("#name", companyDialog).focusout(function () {
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
                    setTimeout(addCompanyDemo, 1500);
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
    lookupPrivateUsers(jQuery('#lookup-company-watchers', container), 'Company_Users', '#selected-watchers', 'company-privacy-container', jQuery(container));
    initializeModalSize(container);
    jQuery("#save-company-btn", container).click(function () { //submit the form
        companyAddFormSubmit(container);
    });
    jQuery("#add-legal-type", container).click(function () { //quick add company legal type
        quickAdministrationDialog('company_legal_types', container, true);
    });
    jQuery("#privateLink", container).click(function () { //set the company as private
        setAsPrivate(jQuery('#company-privacy-container', container), 'companies', '', '', '', true);
    });
    jQuery("#publicLink", container).click(function () { //set the company as public
        setAsPublic(jQuery('#company-privacy-container', container), 'companies', true);
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
    jQuery("#shared-with-users", container).click(function () {
        jQuery('#lookup-company-watchers', container).focus();
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
            companyAddFormSubmit(container);
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
    jQuery("#add-company-sub-categories", container).click(function () { //quick add contact / company sub category
        quickAdministrationDialog('contact_company_sub_categories', container, true, false, false, false, _lang.addContactCompanySubCategory);
    });
}

/*
 *Add/Edit company container
 */
function companyContainerForm(id, containerToAddTo) {
    containerToAddTo = containerToAddTo || false;
    id = id || '';
    jQuery.ajax({
        url: getBaseURL() + 'companies/' + (id ? 'container_edit/' + id : 'container_add'),
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (jQuery("#company-container").length <= 0) {
                jQuery('<div id="company-container"></div>').appendTo("body");
                var companyContainer = jQuery('#company-container');
                companyContainer.html(response.html).removeClass('d-none');
                jQuery(document).keyup(function (e) {
                    if (e.keyCode == 27) {
                        jQuery('.modal', companyContainer).modal('hide');
                    }
                });
                jQuery('.modal', companyContainer).modal({
                    keyboard: false,
                    show: true,
                    backdrop: 'static'

                });
                jQuery("#company-container-submit").click(function () {
                    var formData = jQuery("form#company-container-form", "#company-container").serializeArray();
                    jQuery.ajax({
                        beforeSend: function () {
                            jQuery('.loader-submit', companyContainer).addClass('loading');
                            jQuery('#company-container-submit', companyContainer).attr('disabled', 'disabled');
                        },
                        data: formData,
                        dataType: 'JSON',
                        type: 'POST',
                        url: getBaseURL() + 'companies/' + (id ? 'container_edit/' + id : 'container_add'),
                        success: function (response) {
                            if (undefined !== response.validationErrors) {
                                for (i in response.validationErrors) {
                                    jQuery('.validation-error-container', companyContainer).removeClass('d-none');
                                    jQuery("div", companyContainer).find("[data-field=" + i + "]").html(response.validationErrors[i]);
                                }
                            } else {
                                if (containerToAddTo) {
                                    jQuery("#company-id", containerToAddTo).append(jQuery("<option/>", {
                                        value: response.records.id,
                                        text: response.records.name
                                    }));
                                    jQuery("#company-id", containerToAddTo).val(response.records.id);
                                    jQuery("#company-id", containerToAddTo).selectpicker('refresh');
                                } else {
                                    jQuery('#company_id').append(jQuery("<option/>", {
                                        value: response.records.id,
                                        text: response.records.name
                                    }));
                                    jQuery('#company_id').val(response.records.id).trigger("chosen:updated");
                                }
                                jQuery('.modal', companyContainer).modal('hide');
                                if (id) {
                                    pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                                } else {
                                    pinesMessage({
                                        ty: 'success',
                                        m: _lang.feedback_messages.addedNewCompanyGroupSuccessfully.sprintf([response.records.companyID])
                                    });
                                }
                                if (jQuery('#companyContainersGrid').length) {
                                    jQuery('#companyContainersGrid').data("kendoGrid").dataSource.read();
                                }
                                if (jQuery('#company-group-grid').length) {
                                    window.location.reload();
                                }
                            }
                        }, complete: function () {
                            jQuery('.loader-submit', companyContainer).removeClass('loading');
                            jQuery('#company-container-submit', companyContainer).removeAttr('disabled');

                        },
                        error: defaultAjaxJSONErrorsHandler
                    });
                });
                jQuery("input", companyContainer).keypress(function (e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        jQuery("#company-container-submit").click();
                    }
                });
                jQuery('.modal-body', companyContainer).on("scroll", function () {
                    jQuery('.bootstrap-select.open').removeClass('open');
                });
                jQuery('.modal').on('hidden.bs.modal', function () {
                    destroyModal(companyContainer);
                });
                jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
                    jQuery('#name', companyContainer).focus();

                });
                ctrlS(function () {
                    jQuery("#company-container-submit").click();
                });
                resizeMiniModal(companyContainer);

            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler

    });
}

function getCurrentDate() {
    //return the current date
    var today = new Date();
    return today;
}

/*
 * Lookup for contacts
 * Retreiving contacts depending on the term entered with 1 characters and above
 * @param array lookupDetails( details for the lookup input),string container(jQuery selector of modal container),boolean isBoxContainer(whether the lookup field will be set in a box or input)
 */
function lookUpContacts(lookupDetails, container, isBoxContainer, moreFilters, showEmail) {
    moreFilters = moreFilters || false;
    isBoxContainer = isBoxContainer || false;
    showEmail = showEmail || false;
    lookupDetails['onEraseLookup'] = lookupDetails['onEraseLookup'] || false;
    lookupDetails['onChangeEvent'] = lookupDetails['onChangeEvent'] || false;
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: getBaseURL() + (moreFilters ? ('contacts/autocomplete?term=%QUERY&more_filters[' + moreFilters['keyName'] + ']=%MORE_FILTERS ') : 'contacts/autocomplete?term=%QUERY&show_email=' + showEmail),
            replace: function (url, uriEncodedQuery) {
                if (moreFilters) {
                    var keyValue = encodeURIComponent(moreFilters['value']);
                    return url.replace('%QUERY', uriEncodedQuery).replace('%MORE_FILTERS', keyValue);
                }
                return url.replace('%QUERY', uriEncodedQuery);
            },
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
        minLength: 3
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (!isBoxContainer) {
                    return (item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName) + (showEmail ? (item.email != null ? ' (' + item.email + ')' : " <span class='text-danger'>(" + _lang.noEmailAddress + ")</span>") : '');
                }
            },
            templates: {
                empty: [
                    '<div class="empty click" ></div>'].join('\n'),
                suggestion: function (data) {
                    foreignFullName = data.foreignFullName.trim();
                    return '<div>' + ((data.father ? data.firstName + ' ' + data.father + ' ' + data.lastName : data.firstName + ' ' + data.lastName) + (foreignFullName ? (' - ' + foreignFullName) : '')) + (showEmail ? (data.email != null ? ' (' + data.email + ')' : " <span class='text-danger'>(" + _lang.noEmailAddress + ")</span>") : '') + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            if (isBoxContainer) {
                jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
                lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
                setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), {
                    id: datum.id,
                    value: (datum.father ? datum.firstName + ' ' + datum.father + ' ' + datum.lastName : datum.firstName + ' ' + datum.lastName) + (showEmail ? (datum.email != null ? ' (' + datum.email + ')' : " <span class='text-danger'>(" + _lang.noEmailAddress + ")</span>") : ''),
                    name: lookupDetails['boxName']
                });
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
            if (datum == undefined) {
                //number of dialogs allowed to open is 2(if dialogs count is less than 2,user can open another dialog else user will not have the permission to open a new dialog)
                if (countDialog('contact-dialog-') < 2) {
                    jQuery('.empty', container).html(_lang.no_results_matched_add.sprintf([lookupDetails['lookupField'].val()])).attr('onClick', 'triggerAddContact("' + lookupDetails['lookupField'].val() + '",' + lookupDetails['resultHandler'] + ',"' + container.attr('id') + '")');

                } else {
                    jQuery('.empty', container).html(_lang.no_results_matched).removeClass('click').attr('onClick', '');
                }
            }
            if (obj.currentTarget['value'] == '' && datum == undefined && moreFilters) {
                //if searching for all result(no term sent) and the response is undefined and moreFilters array is defined
                // then change the response message of the lookup
                jQuery('.empty', '#' + obj.currentTarget['form']['id']).html(moreFilters['messageDisplayed']);
            }
            jQuery('.loader-submit', container).removeClass('loading');
        }).on('focus', function () {
            highLightFirstSuggestion();
        }).on('typeahead:change', function () {
            if (lookupDetails['onChangeEvent'] && isFunction(lookupDetails['onChangeEvent'])) {
                lookupDetails['onChangeEvent']();
            }
        });
    if (!isBoxContainer) {
        lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container, lookupDetails['onEraseLookup']);
    }
}

/*
 * Lookup for legal case matter containers
 * Retreiving legal case matter containers depending on the term entered with 1 characters and above
 * @param array lookupDetails( details for the lookup input),string container(jQuery selector of modal container),boolean isBoxContainer(whether the lookup field will be set in a box or input)
 */
function lookUpLegalCaseContainers(lookupDetails, container, isBoxContainer) {
    // Set up variables to store the selection and the original
    // value.
    lookupDetails.callback = lookupDetails.callback || {};
    lookupDetails['callback']['onEraseLookup'] = lookupDetails['callback']['onEraseLookup'] || false;
    lookupDetails['callback']['onSelect'] = lookupDetails['callback']['onSelect'] || false;
    lookupDetails['callback']['onChange'] = lookupDetails['callback']['onChange'] || false;
    var typeaheadSelected = null;
    var typeaheadOriginalVal = null;
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
            url: getBaseURL() + 'case_containers/autocomplete?term=%QUERY',
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
        minLength: 1
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (!isBoxContainer) {
                    return item.subject;
                }
            },
            templates: {
                empty: [
                    '<div class="empty"></div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + (data.subject) + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            selected = datum;
            if (isBoxContainer) {
                jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
                lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
                setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), {
                    id: datum.id,
                    value: datum.subject,
                    name: lookupDetails['boxName']
                });
            } else {
                if (lookupDetails.callback['onSelect'] && isFunction(lookupDetails.callback['onSelect'])) {
                    lookupDetails.callback.onSelect(datum);
                } else {
                    if (jQuery('#legal-case-related-container-link').length) {
                        jQuery('#legal-case-related-container-link').removeClass('d-none').text('MC' + datum.id).attr('href', getBaseURL() + 'case_containers/edit/' + datum.id);
                    }
                }
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
            if (datum == undefined) {
                jQuery('.empty', container).html(_lang.no_results_matched);
            }
            jQuery('.loader-submit', container).removeClass('loading');
        }).on('typeahead:active', function (e) {
            selected = null;
            originalVal = jQuery(lookupDetails['lookupField']).typeahead("val");
        }).on('typeahead:change', function (e, datum) {
            if (lookupDetails.callback['onChange'] && isFunction(lookupDetails.callback['onChange'])) {
                lookupDetails.callback.onChange(e, datum);
            } else {
                if (typeof selected !== 'undefined' && !selected) {
                    jQuery(lookupDetails['lookupField']).typeahead("val", originalVal);
                }
            }
        });
    if (!isBoxContainer) {
        if (!isFunction(lookupDetails['callback']['onEraseLookup']) || !lookupDetails['callback']['onEraseLookup']) {
            lookupDetails['callback']['onEraseLookup'] = false;
        }
        lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container, lookupDetails['callback']['onEraseLookup']);
    }
}

/*
 *Set the selected contact to approver dialog in a box
 *@parm array company(array of company selected details) , string container(jQuery selector of modal container)
 */
function setSelectedContactToApprover(contact, container) {
    setNewBoxElement('#selected-contacts', 'contact-lookup-container', container, {
        id: contact.id,
        value: (contact.father ? contact.firstName + ' ' + contact.father + ' ' + contact.lastName : contact.firstName + ' ' + contact.lastName) + (contact.email != null ? ' (' + contact.email + ')' : " <span class='text-danger'>(" + _lang.noEmailAddress + ")</span>"),
        name: 'approverContacts'
    });
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#lookup-contacts", container).val('').typeahead('destroy');
        var ContactlookupDetails = {
            'lookupField': jQuery('#lookup-contacts', container),
            'lookupContainer': 'contact-lookup-container',
            'errorDiv': 'lookupContacts',
            'boxName': 'approverContacts',
            'boxId': '#selected-contacts',
            'resultHandler': setSelectedContactToApprover
        };
        lookUpContacts(ContactlookupDetails, jQuery(container), true);
    }
}
/*
 *Set the selected contact to signee dialog in a box
 *@parm array company(array of company selected details) , string container(jQuery selector of modal container)
 */
function setSelectedContactToSignee(contact, container) {
    setNewBoxElement('#selected-signees', 'contact-lookup-container', container, {
        id: contact.id,
        value: (contact.father ? contact.firstName + ' ' + contact.father + ' ' + contact.lastName : contact.firstName + ' ' + contact.lastName) + (contact.email != null ? ' (' + contact.email + ')' : " <span class='text-danger'>(" + _lang.noEmailAddress + ")</span>"),
        name: 'signeeContacts'
    });
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#lookup-contacts", container).val('').typeahead('destroy');
        var ContactlookupDetails = {
            'lookupField': jQuery('#lookup-contacts', container),
            'lookupContainer': 'contact-lookup-container',
            'errorDiv': 'lookupContacts',
            'boxId': '#selected-signees',
            'boxName': 'signeeContacts',
            'resultHandler': setSelectedContactToSignee
        };
        lookUpContacts(ContactlookupDetails, jQuery(container), true);
    }
}

/*
 * Return the grid template of the company
 * If member is company and of Group category,only the name will be returned else the link of the internal company or contact
 * @param string memberType (type of the member),  string category (member category), string memberName (member name), array linkId (member id)
 */
function getCompanyGridTemplate(memberType, category, memberName, linkId) {
    if (memberType == 'Company' && category == 'Group') {
        return memberName;
    } else {
        if (memberType === 'Company') {
            return '<a href="' + getBaseURL() + 'companies/tab_company/' + linkId + '">' + memberName + '</a>';
        } else {
            return '<a href="' + getBaseURL() + 'contacts/edit/' + linkId + '">' + memberName + '</a>';
        }
    }
}

/*
 * Add new litigation case
 * Open litigation case dialog to add
 * if isAppeal is true, it will open the litigation append the previus case id with remarks appeal case
 * if relatedAppealCaseId is set, it will append the related appeal case id to the litigation case
 */
function litigationCaseAddForm(containerId,isAppeal,relatedAppealCaseId) {
    isAppeal = isAppeal || false;
    relatedAppealCaseId = relatedAppealCaseId || false;
    containerId = containerId || false;
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var url = containerId > 0 ? (getBaseURL() + 'cases/add_litigation/' + containerId) : (getBaseURL() + 'cases/add_litigation');
    jQuery.ajax({
        dataType: 'JSON',
        url: url,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#litigation-dialog').length <= 0) {
                    jQuery('<div id="litigation-dialog"></div>').appendTo("body");
                    var litigationDialog = jQuery('#litigation-dialog');
                    litigationDialog.html(response.html);
                    caseAddFormEvents(litigationDialog);
                    if (isAppeal) {
                        jQuery('#remarks', litigationDialog).val(_lang.remarksAppealCase + ' ' + relatedAppealCaseId);
                        //create a hidden input and append the related appeal case id to the new litigation case form as the containerwhose id is litigation-add-form

                        jQuery('<input type="hidden" id="related_appeal_case_id" name="related_appeal_case_id" value="' + relatedAppealCaseId + '" />').appendTo("#litigation-add-form");
                        
                    }
                   
                    if (containerId) {
                        jQuery('#legal-case-related-container-id', litigationDialog).val(jQuery('#id', '#caseContainerContainer').val());
                    }
                    opponentsInitialization(jQuery('#opponents-container', litigationDialog));
                    jQuery('#case_type_id', litigationDialog).change(function () {
                        assignmentPerType(this.value, 'litigation', litigationDialog, true);
                    });
                    jQuery('#provider-group-id', litigationDialog).change(function () {
                        assignmentPerType(jQuery('#case_type_id', litigationDialog).val(), 'litigation', litigationDialog, false);
                    });
                }
            } else if (typeof response.error !== 'undefined' && response.error) {
                pinesMessageV2({ ty: 'error', m: response.error });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/*
 * Add new legal matter case
 * Open legal matter dialog to add
 */
function legalMatterAddForm(containerId) {
    containerId = containerId || false;
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var url = containerId > 0 ? (getBaseURL() + 'cases/add_legal_matter/' + containerId) : (getBaseURL() + 'cases/add_legal_matter');
    jQuery.ajax({
        dataType: 'JSON',
        url: url,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#legal-matter-dialog').length <= 0) {
                    jQuery('<div id="legal-matter-dialog"></div>').appendTo("body");
                    var legalMatterDialog = jQuery('#legal-matter-dialog');
                    legalMatterDialog.html(response.html);
                    caseAddFormEvents(legalMatterDialog);
                    if (containerId) {
                        jQuery('#legal-case-related-container-id', legalMatterDialog).val(jQuery('#id', '#caseContainerContainer').val());
                    }
                    jQuery('#case_type_id', legalMatterDialog).change(function () {
                        assignmentPerType(this.value, 'matter', legalMatterDialog, true);
                    });
                    jQuery('#provider-group-id', legalMatterDialog).change(function () {
                        assignmentPerType(jQuery('#case_type_id', legalMatterDialog).val(), 'matter', legalMatterDialog, false);
                    });
                }
            } else if (typeof response.error !== 'undefined' && response.error) {
                pinesMessageV2({ ty: 'error', m: response.error });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

////add criminal litigation case
function litigationCriminalCaseAddForm(containerId) {
    containerId = containerId || false;
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var url = containerId > 0 ? (getBaseURL() + 'cases/add_criminal_case/' + containerId) : (getBaseURL() + 'cases/add_criminal_case/');
    jQuery.ajax({
        dataType: 'JSON',
        url: url,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#litigation-dialog').length <= 0) {
                    jQuery('<div id="litigation-dialog"></div>').appendTo("body");
                    var litigationDialog = jQuery('#litigation-dialog');
                    litigationDialog.html(response.html);
                    //modal size
                    
                    criminalCaseAddFormEvents(litigationDialog);
                    if (containerId) {
                        jQuery('#legal-case-related-container-id', litigationDialog).val(jQuery('#id', '#caseContainerContainer').val());
                    }
                    opponentsInitialization(jQuery('#opponents-container', litigationDialog));
                    jQuery('#case_type_id', litigationDialog).change(function () {
                        assignmentPerType(this.value, 'litigation', litigationDialog, true);
                    });
                    jQuery('#provider-group-id', litigationDialog).change(function () {
                        assignmentPerType(jQuery('#case_type_id', litigationDialog).val(), 'litigation', litigationDialog, false);
                    });
                }
            } else if (typeof response.error !== 'undefined' && response.error) {
                pinesMessageV2({ ty: 'error', m: response.error });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

//load case form events
function caseAddFormEvents(container) {
    jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
    setDatePicker('#arrival-date-add-new', container);
    setDatePicker('#filed-on', container);
    setDatePicker('#due-date-add-new', container);
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#arrival-date-add-new', container), jQuery('#arrival-date-container', container));
        getHijriDate(jQuery('#filed-on', container), jQuery('#filed-on-date-container', container));
        getHijriDate(jQuery('#due-date-add-new', container), jQuery('#due-date-container', container));
    }
    notifyMeBeforeEvent({ 'input': 'due-date-input', 'inputContainer': 'due-date-add-new' }, container, true);
    initializeModalSize(container);
    lookupPrivateUsers(jQuery('#lookup-case-users', container), 'case_watchers', '#selected-watchers', 'case-privacy-container', container);
    clientInitialization(container);
    var lookupDetails = {
        'lookupField': jQuery('#lookup-requested-by', container),
        'errorDiv': 'requestedBy',
        'hiddenId': '#requested-by',
        'resultHandler': setRequestedByToForm
    };
    lookUpContacts(lookupDetails, container);
    jQuery('#user-id', container).change(function () {
        if (jQuery('#user-id', container).val() == 'quick_add') {
            jQuery('#user-id', container).val('').selectpicker('refresh');
            addUserToTheProviderGroup(jQuery('#provider-group-id', container).val(), 'user-id', true);
        } else if (jQuery('#user-relation', container).val() > 0 && jQuery('#user-relation', container).val() !== this.value) {
            pinesMessage({ ty: 'warning', m: _lang.feedback_messages.assignmentChangeForAssignee });
        }
    });
    var legalCaseContainerLookupDetails = {
        'lookupField': jQuery('#legal-case-related-container-lookup', container),
        'errorDiv': 'legal-case-related-container-id',
        'hiddenId': '#legal-case-related-container-id',
        'resultHandler': setLegalCaseContainerToCaseForm
    };
    lookUpLegalCaseContainers(legalCaseContainerLookupDetails, container);
    jQuery('#provider-group-id', container).change(function () {
        if (jQuery('#related-assigned-team', container).val() !== this.value) {
            pinesMessage({ ty: 'warning', m: _lang.feedback_messages.assignmentChangeForAssignedTeam });
        }
        reloadUsersListByProviderGroupSelected(jQuery('#provider-group-id', container).val(), jQuery("#user-id", container), true);
    });
    jQuery("#case-submit", container).click(function () {
        caseAddFormSubmit(container);
    });
    jQuery('.modal', container).modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.date-picker', container).bootstrapDP("remove");
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            caseAddFormSubmit(container);
        }
    });
    jQuery('.modal-body').on("scroll", function () {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(container);
        companyContactFormMatrix.commonLookup = {};//empty the commonLookup array
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#subject', container).focus();
        jQuery('#pendingReminders').parent().popover('hide');
    });
}

///criminal litigation
function criminalCaseAddFormEvents(container) {
    jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
    setDatePicker('#arrival-date-add-new', container);
    setDatePicker('#filed-on', container);
    setDatePicker('#due-date-add-new', container);
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#arrival-date-add-new', container), jQuery('#arrival-date-container', container));
        getHijriDate(jQuery('#filed-on', container), jQuery('#filed-on-date-container', container));
        getHijriDate(jQuery('#due-date-add-new', container), jQuery('#due-date-container', container));
    }
    notifyMeBeforeEvent({ 'input': 'due-date-input', 'inputContainer': 'due-date-add-new' }, container, true);
    initializeModalSize(container);
    lookupPrivateUsers(jQuery('#lookup-case-users', container), 'case_watchers', '#selected-watchers', 'case-privacy-container', container);
    clientInitialization(container);
    var lookupDetails = {
        'lookupField': jQuery('#lookup-requested-by', container),
        'errorDiv': 'requestedBy',
        'hiddenId': '#requested-by',
        'resultHandler': setRequestedByToForm
    };
    lookUpContacts(lookupDetails, container);
    jQuery('#user-id', container).change(function () {
        if (jQuery('#user-id', container).val() == 'quick_add') {
            jQuery('#user-id', container).val('').selectpicker('refresh');
            addUserToTheProviderGroup(jQuery('#provider-group-id', container).val(), 'user-id', true);
        } else if (jQuery('#user-relation', container).val() > 0 && jQuery('#user-relation', container).val() !== this.value) {
            pinesMessage({ ty: 'warning', m: _lang.feedback_messages.assignmentChangeForAssignee });
        }
    });
    var legalCaseContainerLookupDetails = {
        'lookupField': jQuery('#legal-case-related-container-lookup', container),
        'errorDiv': 'legal-case-related-container-id',
        'hiddenId': '#legal-case-related-container-id',
        'resultHandler': setLegalCaseContainerToCaseForm
    };
    lookUpLegalCaseContainers(legalCaseContainerLookupDetails, container);
    jQuery('#provider-group-id', container).change(function () {
        if (jQuery('#related-assigned-team', container).val() !== this.value) {
            pinesMessage({ ty: 'warning', m: _lang.feedback_messages.assignmentChangeForAssignedTeam });
        }
        reloadUsersListByProviderGroupSelected(jQuery('#provider-group-id', container).val(), jQuery("#user-id", container), true);
    });
    jQuery("#case-submit", container).click(function () {
        criminalCaseAddFormSubmit(container);
    });
    jQuery('.modal', container).modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.date-picker', container).bootstrapDP("remove");
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            criminalCaseAddFormSubmit(container);
        }
    });
    jQuery('.modal-body').on("scroll", function () {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(container);
        companyContactFormMatrix.commonLookup = {};//empty the commonLookup array
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#subject', container).focus();
        jQuery('#pendingReminders').parent().popover('hide');
    });
}

//initialize opponents
function opponentsInitialization(container, callback) {
    callback = callback || false;
    var opponentsCount = jQuery('#opponents-count', container).val();
    var count = 1;
    jQuery('.opponent-div', container).each(function () {
        if (count > opponentsCount) {
            return;
        }
        opponentContainer = jQuery('#opponent-' + count, container);
        opponentsEvents(opponentContainer, callback);
        count++;
    });
}

//load opponents events
function opponentsEvents(container, callback) {
    callback = callback || false;
    var idName = container.attr('id');
    var opponentId = '#' + idName;
    var opponentNumber = idName.charAt(idName.length - 1);
    var lookupDetails = {
        'lookupField': jQuery('#opponent-lookup', container),
        'hiddenInput': jQuery('#opponent-member-id', container),
        'errorDiv': 'opponent_member_id_' + opponentNumber + '',
        'resultHandler': setDataToCaseOpponentField,
        'callback': callback,
    };
    jQuery('#opponent-member-type', container).selectpicker().change(function () {
        jQuery("#opponent-lookup", container).val('');
        jQuery("#opponent-member-id", container).val('');
        jQuery(".inline-error", container).html('');
        jQuery('#opponent-lookup', container).typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it.
        companyContactFormMatrix.commonLookup[opponentId]['lookupType'] = jQuery("select#opponent-member-type", container).val();
        lookupCompanyContactType(lookupDetails, container);
    });
    jQuery('#opponent-position', container).selectpicker();
    companyContactFormMatrix.commonLookup[opponentId] = {
        "lookupType": jQuery("select#opponent-member-type", container).val(),
        "referalContainerId": container,
        "parentContainer": container,
    }
    companyContactFormMatrix.commonLookup[opponentId].callback = callback;
    jQuery('#opponent-lookup', container).typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it To avoid conflict of lookup initializing
    lookupCompanyContactType(lookupDetails, container);
}

//clone opponent container
function opponentAddContainer(container, event, maxNumber) {
    if (jQuery('.opponent-div', container).length == maxNumber) {
        pinesMessage({ ty: 'information', m: _lang.caseMaxOpponentsInfo.sprintf([maxNumber]), d: 10000 });
        return;
    }
    var opponentsContainer = jQuery('#opponents-container', container);
    var nbOfOpponents = parseInt(jQuery('#opponents-count', opponentsContainer).val());
    jQuery('#opponent-member-type', jQuery('.opponent-div', opponentsContainer).last()).selectpicker('destroy');//Before cloning ,destroy selectpicker in the container to clone from
    jQuery('#opponent-position', jQuery('.opponent-div', opponentsContainer).last()).selectpicker('destroy');//Before cloning ,destroy selectpicker in the container to clone from
    var clonedHtml = jQuery('.opponent-div', opponentsContainer).last().clone();
    var newId = nbOfOpponents + 1;
    var clonedContainer = '#opponent-' + (newId);
    var clonedDiv = clonedHtml.insertAfter(jQuery('.opponent-div', opponentsContainer).last());
    clonedDiv.attr("id", 'opponent-' + (newId));
    jQuery('#opponent-member-type', jQuery('#opponent-' + nbOfOpponents, opponentsContainer)).selectpicker();//after cloning re initialize the selectpicker of the picker that was destroyed
    jQuery('#opponent-position', jQuery('#opponent-' + nbOfOpponents, opponentsContainer)).selectpicker();//after cloning re initialize the selectpicker of the picker that was destroyed
    jQuery('#opponents-count', opponentsContainer).val(newId);
    jQuery('.delete-opponent', jQuery(clonedContainer, opponentsContainer)).attr("onclick", 'opponentDelete(' + newId + ',\'' + container + '\' , event)');
    jQuery('#opponent-lookup', jQuery(clonedContainer, opponentsContainer)).attr("onblur", "if (this.value === '') { jQuery('.opponentLinkId','#opponent-" + newId + "').addClass('d-none');jQuery(this).attr('title', ''); }");
    jQuery('.opponent-label', jQuery(clonedContainer, opponentsContainer)).html(_lang.opponent + ' (' + (newId) + ')');
    jQuery('#opponent-member-id', jQuery(clonedContainer, opponentsContainer)).val('');
    jQuery('#opponent-lookup', jQuery(clonedContainer, opponentsContainer)).val('');
    jQuery('#opponent-member-type', jQuery(clonedContainer, opponentsContainer)).find('option').removeAttr('selected');
    jQuery('#opponent-position', jQuery(clonedContainer, opponentsContainer)).find('option').removeAttr('selected');
    jQuery('.delete-icon', jQuery(clonedContainer, opponentsContainer)).removeClass('d-none');
    jQuery('.inline-error', jQuery(clonedContainer, opponentsContainer)).attr('data-field', 'opponent_member_id_' + newId).html('');
    jQuery('#opponent-position', jQuery(clonedContainer, opponentsContainer)).attr('data-field-id', 'opponent-position-' + newId);
    jQuery('.opponent-position-quick-add', jQuery(clonedContainer, opponentsContainer)).removeAttr('onClick').click(function () {
        quickAdministrationDialog('case_opponent_positions', jQuery(container), true, false, false, jQuery('[data-field-id=opponent-position-' + newId + ']'));
    });
    if (nbOfOpponents == 1) {
        jQuery('.delete-icon', jQuery('#opponent-' + (nbOfOpponents), opponentsContainer)).removeClass('d-none');
    }

    opponentsEvents(jQuery(clonedContainer, opponentsContainer), { 'onselect': onCaseOpponentSelect });
    var litigationStageDialog = jQuery('#litigation-stage-dialog');
    jQuery(litigationStageDialog).find('input').keypress(function (e) {
        if (e.which == 13) { // pressing enter
            e.preventDefault();
            litigationStageFormSubmit(litigationStageDialog);
        }
    });

    event.preventDefault();
}

//update opponent labels and ids - used in dialog forms
function opponentsUpdateLabels(container) {
    var opponentsContainer = jQuery('#opponents-container', container);
    var nbOfOpponents = jQuery('#opponents-count', opponentsContainer).val();
    var count = 1;
    jQuery('.opponent-label', opponentsContainer).each(function () {
        if (count <= nbOfOpponents) {
            jQuery(this).html(_lang.opponent + ' (' + count + ')');
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.opponent-div', opponentsContainer).each(function () {
        if (count <= nbOfOpponents) {
            jQuery(this).attr("id", 'opponent-' + count);
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.inline-error', opponentsContainer).each(function () {
        if (count <= nbOfOpponents) {
            jQuery(this).attr("data-field", 'opponent_member_id_' + count);
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.delete-opponent', opponentsContainer).each(function () {
        if (count <= nbOfOpponents) {
            jQuery(this).attr("onclick", 'opponentDelete(' + count + ',\'' + container + '\' , event)');
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery("[data-field=administration-case_opponent_positions]", opponentsContainer).each(function () {
        if (count <= nbOfOpponents) {
            jQuery(this).attr("data-field-id", 'opponent-position-' + count);
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    var isDialog = jQuery('#opponent-position').hasClass('select-picker');
    jQuery('.opponent-position-quick-add', opponentsContainer).each(function () {
        if (count <= nbOfOpponents) {
            jQuery(this).removeAttr('onClick').attr("onclick", 'quickAdministrationDialog(\'case_opponent_positions\', ' + container + '\' , ' + isDialog + ', ' + false + ', ' + false + ', ' + jQuery('[data-field-id=opponent-position-' + count + ']') + ')');
            count++;
        } else {
            return true;
        }
    });
    opponentsInitialization(container);
}

//delete opponent
function opponentDelete(opponentId, container, event) {
    var opponentsContainer = jQuery('#opponents-container', container);
    var nbOfOpponents = jQuery('#opponents-count', opponentsContainer).val();
    if (nbOfOpponents > 1) {
        jQuery('#opponent-' + opponentId, opponentsContainer).remove();
        companyContactFormMatrix.commonLookup = {};//empty the commonLookup array
        jQuery('#opponents-count', opponentsContainer).val(nbOfOpponents - 1);
        opponentsUpdateLabels(container);
        if (jQuery('#opponents-count', opponentsContainer).val() == 1) {
            jQuery('.delete-icon', opponentsContainer).addClass('d-none');

        }
    } else {
        pinesMessage({ ty: 'warning', m: _lang.invalid_request });
    }
    event.preventDefault();
}

//initialize client
function clientInitialization(container, callback) {
    callback = callback || false;
    if (callback) callback['clientTypeChange'] = callback['clientTypeChange'] || false;
    var containerId = '#' + container.attr('id');
    companyContactFormMatrix.commonLookup[containerId] = {
        "lookupType": jQuery("select#client-type", container).val(),
    }
    var lookupDetails = {
        'lookupField': jQuery('#client-lookup', container),
        'hiddenInput': jQuery('#contact-company-id', container),
        'errorDiv': 'contact_company_id',
        'resultHandler': setDataToCaseClientField,
        'callback': callback
    };
    companyContactFormMatrix.commonLookup[containerId].callback = callback;
    lookupCompanyContactType(lookupDetails, container);
    jQuery('#client-type', container).change(function () {
        if (callback && lookupDetails['callback']['clientTypeChange'] && isFunction(lookupDetails['callback']['clientTypeChange'])) {
            lookupDetails['callback']['clientTypeChange'](jQuery("#contact-company-id", container).val());
        }
        jQuery("div", container).find("[data-field=contact_company_id]").addClass('d-none').html('');
        jQuery(".inline-error", container).html('');
        jQuery("#contact-company-id", container).val('');
        jQuery("#client-lookup", container).val('');
        jQuery('#client-lookup', container).typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it.
        companyContactFormMatrix.commonLookup[containerId].lookupType = jQuery("#client-type", container).val();
        lookupCompanyContactType(lookupDetails, container);
    });
}
function clientInitializationCaseTypes(container, callback, id) {
    callback = callback || false;
    if (callback) callback['clientTypeChange'] = callback['clientTypeChange'] || false;
    var containerId = '#' + container.attr('id');
    companyContactFormMatrix.commonLookup[containerId] = {
        "lookupType": jQuery("select#client-type" + id, container).val(),
    }
    var lookupDetails = {
        'lookupField': jQuery('#client-lookup' + id, container),
        'hiddenInput': jQuery('#contact-company-id' + id, container),
        'errorDiv': 'contact_company_id' + id,
        'resultHandler': setDataToCaseClientField,
        'callback': callback
    };
    companyContactFormMatrix.commonLookup[containerId].callback = callback;
    lookupCompanyContactType(lookupDetails, container);
    jQuery('#client-type' + id, container).change(function () {
        if (callback && lookupDetails['callback']['clientTypeChange'] && isFunction(lookupDetails['callback']['clientTypeChange'])) {
            lookupDetails['callback']['clientTypeChange'](jQuery("#contact-company-id" + id, container).val());
        }
        jQuery("div", container).find("[data-field=contact_company_id" + id + "]").addClass('d-none').html('');
        jQuery(".inline-error", container).html('');
        jQuery("#contact-company-id" + id, container).val('');
        jQuery("#client-lookup" + id, container).val('');
        jQuery('#client-lookup' + id, container).typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it.
        companyContactFormMatrix.commonLookup[containerId].lookupType = jQuery("#client-type" + id, container).val();
        lookupCompanyContactType(lookupDetails, container);
    });
}
//initialize Outsource
function OutsourceInitialization(container, callback) {
    callback = callback || false;
    var containerId = '#' + jQuery(container).attr('id');
    companyContactFormMatrix.commonLookup[containerId] = {
        "lookupType": jQuery("select#outsource-type-operator", container).val(),
    }
    jQuery("#outsource-type-operator").selectpicker();
    var lookupDetails = {
        'lookupField': jQuery('#outsource-lookup', container),
        'hiddenInput': jQuery('#outsource-id', container),
        'errorDiv': 'outsource_id',
        'resultHandler': setDataToCaseOutsourceField,
        'callback': callback,
        'type': 'outsource'
    };
    companyContactFormMatrix.commonLookup[containerId].callback = callback;
    lookupCompanyContactType(lookupDetails, container);
    jQuery('#outsource-type-operator', container).change(function () {
        jQuery("div", container).find("[data-field=outsource_id]").addClass('d-none').html('');
        jQuery(".inline-error", container).html('');
        jQuery("#outsource-id", container).val('');
        jQuery("#outsource-lookup", container).val('');
        jQuery('#outsource-lookup', container).typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it.
        companyContactFormMatrix.commonLookup[containerId].lookupType = jQuery("#outsource-type-operator", container).val();
        lookupCompanyContactType(lookupDetails, container);
    });
}

function setDataToCaseOutsourceField(record, container) {
    var containerId = '#' + jQuery(container).attr('id');
    var outsourceName = record.name ? record.name : (record.father ? (record.firstName + ' ' + record.father + ' ' + record.lastName) : (record.firstName + ' ' + record.lastName));
    jQuery('#outsource-id', container).val(record.id);
    jQuery('#outsource-lookup', container).val(outsourceName);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#outsource-lookup", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#outsource-lookup', container),
            'hiddenInput': jQuery('#outsource-id', container),
            'errorDiv': 'outsource_id',
            'resultHandler': setDataToCaseOutsourceField,
            'callback': companyContactFormMatrix.commonLookup[containerId].callback
        };
        lookupCompanyContactType(lookupDetails, jQuery(container));
    }
}

//Add user to the provider group
function addUserToTheProviderGroup(pGId, userId, isDialog) {
    isDialog = isDialog || false;
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'provider_groups/quick_add_user_to_team',
        data: { modeType: 'getForm', id: pGId },
        beforeSend: function () {
            jQuery('#loader-global').show();
            ;
        },
        success: function (response) {
            if (!jQuery('#quick-add-user-team').length) {
                jQuery('<div id="quick-add-user-team" class="d-none"></div>').appendTo('body');
                var quickAddDialog = jQuery('#quick-add-user-team');
                if (response.html) {
                    quickAddDialog.html(response.html).removeClass('d-none');
                    addUserToTheProviderGroupEvents(quickAddDialog);
                    jQuery("#add-user-submit", quickAddDialog).click(function () {
                        var formData = jQuery("form#quick-add-user-provider-group-form", quickAddDialog).serializeArray();
                        jQuery.ajax({
                            beforeSend: function () {
                                jQuery('.loader-submit', quickAddDialog).addClass('loading');
                                jQuery('#add-user-submit', quickAddDialog).attr('disabled', 'disabled');
                            },
                            data: formData,
                            dataType: 'JSON',
                            type: 'POST',
                            url: getBaseURL() + 'provider_groups/quick_add_user_to_team',
                            success: function (response) {
                                if (response.result) {
                                    pinesMessageV2({
                                        ty: 'success',
                                        m: _lang.feedback_messages.addeNewUserToTeamSuccessfully.sprintf([response.result.user, response.result.user_group_name])
                                    });
                                    jQuery('.modal', quickAddDialog).modal('hide');
                                    var newOptions = '<option selected="selected" value="' + response.result.user_id + '">' + response.result.user + '</option>';
                                    if (isDialog) {
                                        jQuery('#' + userId).append(newOptions).selectpicker('refresh');
                                    } else {
                                        jQuery('#' + userId).append(newOptions).trigger("chosen:updated");
                                    }
                                } else if (undefined !== response.validationErrors) {
                                    for (i in response.validationErrors) {
                                        jQuery('.validation-error-container', quickAddDialog).removeClass('d-none');
                                        jQuery("div", quickAddDialog).find("[data-field=" + i + "]").html(response.validationErrors[i]);
                                    }
                                }
                            }, complete: function () {
                                jQuery('.loader-submit', quickAddDialog).removeClass('loading');
                                jQuery('#add-user-submit', quickAddDialog).removeAttr('disabled');

                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                    });

                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function addUserToTheProviderGroupEvents(container) {
    jQuery('.modal', container).modal({
        keyboard: false,
        show: true,
        backdrop: 'static'

    });
    resizeMiniModal(container);
    jQuery(window).bind('resize', (function () {
        resizeMiniModal(container);
    }));
    var moreFilters = {
        'keyName': 'excludedProviderGroupUsers',
        'value': jQuery('#provider-group-id', container).val(),
        'messageDisplayed': _lang.feedback_messages.allUsersAddedToTheTeam
    };
    lookUpUsers(jQuery('#user', container), jQuery('#user-id', container), 'user_id', jQuery('.assignee-container', container), container, moreFilters);
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery("input", container).keypress(function (e) {
        if (e.which == 13) {
            e.preventDefault();
            jQuery("#add-user-submit", container).click();
        }
    });
    jQuery('.modal-body', container).on("scroll", function () {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(container);
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#user', container).focus();
    });
}

function setDataToCaseOpponentField(record, container) {
    var containerId = '#' + jQuery(container).attr('id');
    var opponentName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    container = (undefined !== companyContactFormMatrix.commonLookup[containerId] && undefined !== companyContactFormMatrix.commonLookup[containerId].parentContainer) ? companyContactFormMatrix.commonLookup[containerId].parentContainer : '#litigation-dialog';
    jQuery('#opponent-member-id', container).val(record.id);
    jQuery('#opponent-lookup', container).val(opponentName);
    onCaseOpponentSelect("", record, container);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#opponent-lookup", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#opponent-lookup', container),
            'hiddenInput': jQuery('#opponent-member-id', container),
            'errorDiv': 'opponent_member_id_' + container.attr('id').charAt(container.attr('id').length - 1) + '',
            'resultHandler': setDataToCaseOpponentField,
            'callback': companyContactFormMatrix.commonLookup[containerId].callback,
            'parentContainer': container,
        };
        lookupCompanyContactType(lookupDetails, container);
    }
}

function setDataToCaseClientField(record, container) {
    var containerId = '#' + jQuery(container).attr('id');
    var clientName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#contact-company-id', container).val(record.id);
    jQuery('#client-lookup', container).val(clientName);
    onCaseClientSelect("", record, container);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#client-lookup", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#client-lookup', container),
            'hiddenInput': jQuery('#contact-company-id', container),
            'errorDiv': 'contact_company_id',
            'resultHandler': setDataToCaseClientField,
            'callback': companyContactFormMatrix.commonLookup[containerId].callback
        };
        lookupCompanyContactType(lookupDetails, jQuery(container));
    }
}



function setReferredByToForm(record, container) {
    var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#referredByLookup').val(name);
    jQuery('#referredBy').val(record.id);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#referredByLookup", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#referredByLookup', container),
            'errorDiv': 'referredBy',
            'hiddenId': '#referredBy',
            'resultHandler': setReferredByToForm
        };
        lookUpContacts(lookupDetails, jQuery(container));
    }
    if (jQuery('#referredByLinkId', container).length && record.id > 0) {
        jQuery('#referredByLinkId', container).attr('href', getBaseURL() + 'contacts/edit/' + record.id).removeClass('d-none');
    } else {
        jQuery('#referredByLinkId', container).addClass('d-none');
    }
}

function setRequestedByToForm(record, container) {
    var name = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#lookup-requested-by', container).val(name);
    jQuery('#requested-by', container).val(record.id);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#lookup-requested-by", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#lookup-requested-by', container),
            'errorDiv': 'requestedBy',
            'hiddenId': '#requested-by',
            'resultHandler': setRequestedByToForm
        };
        lookUpContacts(lookupDetails, jQuery(container));
    }
    if (jQuery('#requestedByLinkId', container).length && record.id > 0) {
        jQuery('#requestedByLinkId', container).attr('href', getBaseURL() + 'contacts/edit/' + record.id).removeClass('d-none');
    } else {
        jQuery('#requestedByLinkId', container).addClass('d-none');
    }
}

function setLegalCaseContainerToCaseForm(record, container) {
    var name = record.subject;
    jQuery('#legal-case-related-container-lookup', container).val(name);
    jQuery('#legal-case-related-container-id', container).val(record.id);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#legal-case-related-container-lookup", container).typeahead('destroy');
        var legalCaseContainerLookupDetails = {
            'lookupField': jQuery('#legal-case-related-container-lookup', container),
            'errorDiv': 'legal-case-related-container-id',
            'hiddenId': '#legal-case-related-container-id',
            'resultHandler': setLegalCaseContainerToCaseForm
        };
        lookUpLegalCaseContainers(legalCaseContainerLookupDetails, jQuery(container));
    }
}

/*
 * Submit add case
 * Save case ,if errors show erros as inline text else success message
 * @param string container(jQuery selector of form container)
 */
function caseAddFormSubmit(container) {
    var action = jQuery('#action', container).val();
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/' + action,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery('#notify-me-before-container', container).is(':visible')) {
                    loadUserLatestReminders('refresh');
                }
                if (jQuery('#legalCaseGrid').length) {
                    jQuery('#legalCaseGrid').data("kendoGrid").dataSource.read();
                }
                if (action == 'add_legal_matter') {
                    updateGetingStartedSteps('legal_matter');
                }
                if (jQuery('#related-containers-cases-div', '#caseContainerContainer').length) {
                    // refresh page
                    displayRelatedCases();
                }
                var msg = _lang.done_case_n_created.sprintf([getBaseURL() + 'cases/edit/' + response.caseId, response.modelCode + response.caseId]);
                if (typeof response.display_message !== 'undefined') {
                    msg = msg + '</br>' + response.display_message;
                }
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('matters');
                    pieCharts();
                }
                if (action == 'add_litigation' && jQuery('#litigation-dashboard').length > 0) {
                    loadDashboardData('cases');
                }
                pinesMessage({ ty: 'success', m: msg });
                jQuery('.modal', container).modal('hide');
            } else {
                jQuery('.inline-error', container).removeClass('validation-error');
                for (i in response.validationErrors) {
                    jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
                }
                scrollToValidationError(container);
                if (typeof response.error !== 'undefined' && response.error) {
                    pinesMessageV2({ ty: 'error', m: response.error });
                }
            }
        }, complete: function () {
            jQuery('.btn-save', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/**
 * submit criminal case form
 * @param container
 */
function criminalCaseAddFormSubmit(container) {
    var action = jQuery('#action', container).val();
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/' + action,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery('#notify-me-before-container', container).is(':visible')) {
                    loadUserLatestReminders('refresh');
                }
                if (jQuery('#legalCaseGrid').length) {
                    jQuery('#legalCaseGrid').data("kendoGrid").dataSource.read();
                }
                if (action == 'add_legal_matter') {
                    updateGetingStartedSteps('legal_matter');
                }
                if (jQuery('#related-containers-cases-div', '#caseContainerContainer').length) {
                    // refresh page
                    displayRelatedCases();
                }
                var msg = _lang.done_criminal_case_n_created.sprintf([getBaseURL() + 'cases/complaint_details/' + response.caseId, response.modelCode + response.caseId]);
                if (typeof response.display_message !== 'undefined') {
                    msg = msg + '</br>' + response.display_message;
                }
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('matters');
                    pieCharts();
                }
                if (action == 'add_litigation' && jQuery('#litigation-dashboard').length > 0) {
                    loadDashboardData('cases');
                }
                pinesMessage({ ty: 'success', m: msg });
                jQuery('.modal', container).modal('hide');
            } else {
                jQuery('.inline-error', container).removeClass('validation-error');
                for (i in response.validationErrors) {
                    jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
                }
                scrollToValidationError(container);
                if (typeof response.error !== 'undefined' && response.error) {
                    pinesMessageV2({ ty: 'error', m: response.error });
                }
            }
        }, complete: function () {
            jQuery('.btn-save', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
/*end submit criminal case*/

function linkIpToContainer(matterId, containerId) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'case_containers/link_matter',
        type: 'POST',
        data: { matter_id: matterId, container_id: containerId },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (!response.result) {
                pinesMessage({ ty: 'error', m: _lang.updatesFailed });
            } else {
                displayRelatedCases();
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

//set due date if there is SLA already set
function setDueDate(SLA, container) {
    if (SLA != '' && SLA != null) {
        var arrivalDate = jQuery('#arrival-date-input', container).val();
        var actualDate = new Date(arrivalDate);
        var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate() + (SLA * 1));
        newDate = returnDateinFormat(newDate);
        if (newDate > jQuery('#filed-on-hidden', container).val()) { //if only the newdate is larger than the filed on date=>larger than the due date
            jQuery("#due-date-input", container).val(newDate);
            setDatePicker('#due-date', container);
            jQuery("#due-date", container).bootstrapDP('update', newDate);
            jQuery('#due-date-hidden', container).val(newDate);
            jQuery('#filed-on', container).bootstrapDP('setEndDate', newDate);
            jQuery('#notify-me-before-link', container).removeClass('d-none');
        }
    }
}

/*
 * Setting min and max of dates according to the array datesArray
 * @param string formContainer(jQuery selector of form container),array datesArray (array of the min and max dates for each date input)
 */
function datesCombination(formContainer, datesArray) {
    for (i in datesArray) {
        var minMaxDate = { 'minDate': {}, 'maxDate': {} };
        jQuery('#' + i, formContainer).on('changeDate', function (e) { // on change on the datepicker=>trigger change for the min and max of the dates refering to the array datesArray
            var id = jQuery(this).attr('id');
            var inputDate = jQuery('#' + id + '-input', formContainer).val();
            jQuery('#' + id + '-hidden', formContainer).val(inputDate).trigger('change');
            var date = jQuery('#' + id + '-hidden', formContainer).val();
            if (datesArray[id]['minOf']) {
                if (jQuery.isArray(datesArray[id]['minOf'])) {
                    for (j in datesArray[id]['minOf']) {
                        if (minMaxDate.minDate[datesArray[id]['minOf'][j]] == undefined) {
                            minMaxDate.minDate[datesArray[id]['minOf'][j]] = date;
                            minMaxDate.minDate[datesArray[id]['minOf'][j][id]] = date;
                            minMaxDate.minDate[datesArray[id]['minOf'][j][id]] = date;
                            var minDate = minMaxDate.minDate[datesArray[id]['minOf'][j]] ? minMaxDate.minDate[datesArray[id]['minOf'][j]] : -Infinity;
                            jQuery('#' + datesArray[id]['minOf'][j], formContainer).bootstrapDP('setStartDate', minDate);
                        }
                    }
                } else {
                    minMaxDate.minDate[datesArray[id]['minOf']] = date;
                    var minDate = minMaxDate.minDate[datesArray[id]['minOf']] ? minMaxDate.minDate[datesArray[id]['minOf']] : -Infinity;
                    jQuery('#' + datesArray[id]['minOf'], formContainer).bootstrapDP('setStartDate', minDate);
                }
            }
            if (datesArray[id]['maxOf']) {
                minMaxDate.maxDate[datesArray[id]['maxOf']] = date;
                var maxDate = minMaxDate.maxDate[datesArray[id]['maxOf']] ? minMaxDate.maxDate[datesArray[id]['maxOf']] : Infinity;
                jQuery('#' + datesArray[id]['maxOf'], formContainer).bootstrapDP('setEndDate', maxDate);

            }
        });
        jQuery('#' + i + '-input', formContainer).on('blur', function () { //on blur on the input of the dates => trigger change of the min and max of the dates refering to the array datesArray and set the value of the dates in the hidden input
            var inputId = jQuery(this).attr('id');
            var inputDate = jQuery('#' + inputId, formContainer).val();
            var key = inputId.substring(0, inputId.length - 6);
            if (jQuery('#' + key + '-hidden', formContainer).val() !== '') {
                if (inputDate == '') {
                    jQuery('#' + key + '-hidden', formContainer).val('');
                    if (jQuery.isArray(datesArray[key]['minOf'])) {
                        for (j in datesArray[key]['minOf']) {
                            if (minMaxDate.minDate[datesArray[key]['minOf'][j]] == undefined) {
                                jQuery('#' + datesArray[key]['minOf'][j], formContainer).bootstrapDP('setStartDate', -Infinity);
                            }
                        }
                    } else {
                        if (datesArray[key]['minOf']) {

                            minMaxDate.minDate[datesArray[key]['minOf']] = inputDate;

                            jQuery('#' + datesArray[key]['minOf'], formContainer).bootstrapDP('setStartDate', -Infinity);
                        }
                    }
                    if (datesArray[key]['maxOf']) {
                        minMaxDate.maxDate[datesArray[key]['maxOf']] = inputDate;
                        jQuery('#' + datesArray[key]['maxOf'], formContainer).bootstrapDP('setEndDate', Infinity);

                    }
                } else {
                    jQuery('#' + key + '-hidden', formContainer).val(inputDate).trigger('change');
                    if (datesArray[key]['minOf']) {
                        minMaxDate.minDate[datesArray[key]['minOf']] = inputDate;
                        jQuery('#' + datesArray[key]['minOf'], formContainer).bootstrapDP('setStartDate', inputDate);

                    }
                    if (datesArray[key]['maxOf']) {
                        minMaxDate.maxDate[datesArray[key]['maxOf']] = inputDate;
                        jQuery('#' + datesArray[key]['maxOf'], formContainer).bootstrapDP('setEndDate', inputDate);

                    }

                }
            }
        });
    }

}

//return date in YYYY-MM-DD FORMAT
function returnDateinFormat(newDate) {
    // GET YYYY, MM AND DD FROM THE DATE OBJECT
    var yyyy = newDate.getFullYear().toString();
    var mm = (newDate.getMonth() + 1).toString();
    var dd = newDate.getDate().toString();

    // CONVERT mm AND dd INTO chars
    var mmChars = mm.split('');
    var ddChars = dd.split('');

    // CONCAT THE STRINGS IN YYYY-MM-DD FORMAT
    var datestring = yyyy + '-' + (mmChars[1] ? mm : "0" + mmChars[0]) + '-' + (ddChars[1] ? dd : "0" + ddChars[0]);
    return datestring;
}

/*
 * Add new intellectual property
 * Open intellectual property dialog to add
 */
function intellectualPropertyAddForm(containerId) {
    containerId = containerId || false;
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var url = containerId > 0 ? (getBaseURL() + 'intellectual_properties/add/' + containerId) : (getBaseURL() + 'intellectual_properties/add');
    jQuery.ajax({
        dataType: 'JSON',
        url: url,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#intellectual-property-dialog').length <= 0) {
                    jQuery('<div id="intellectual-property-dialog"></div>').appendTo("body");
                    var intellectualPropertyDialog = jQuery('#intellectual-property-dialog');
                    intellectualPropertyDialog.html(response.html);
                    intellectualPropertyAddFormEvents(intellectualPropertyDialog, containerId);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


//load intellectual property form events
function intellectualPropertyAddFormEvents(container, containerId) {
    jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
    setDatePicker('#filed-on', container);
    setDatePicker('#registration-date', container);
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#filed-on', container), jQuery('#filed-on-date-container', container));
        getHijriDate(jQuery('#registration-date', container), jQuery('#registration-date-container', container));
    }
    clientInitialization(container);
    agentInitialization(container);
    jQuery('#user-id', container).change(function () {
        if (jQuery('#user-id', container).val() == 'quick_add') {
            jQuery('#user-id', container).val('').selectpicker('refresh');
            addUserToTheProviderGroup(jQuery('#provider-group-id', container).val(), 'user-id', true);
        }
    });
    jQuery('#provider-group-id', container).change(function () {
        reloadUsersListByProviderGroupSelected(jQuery('#provider-group-id', container).val(), jQuery("#user-id", container), true);
    });

    var legalCaseContainerLookupDetails = {
        'lookupField': jQuery('#legal-case-related-container-lookup', container),
        'errorDiv': 'legal-case-related-container-id',
        'hiddenId': '#legal-case-related-container-id',
        'resultHandler': setLegalCaseContainerToCaseForm
    };
    lookUpLegalCaseContainers(legalCaseContainerLookupDetails, container);

    initializeModalSize(container);
    jQuery("#intellectual-property-submit", container).click(function () {
        intellectualPropertyAddFormSubmit(container, containerId);
    });
    jQuery('.modal', container).modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.date-picker', container).bootstrapDP("remove");
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            intellectualPropertyAddFormSubmit(container, containerId);
        }
    });
    jQuery('.modal-body', container).on("scroll", function () {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(container);
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery("div", container).find("[data-id=intellectual_property_right_id]").focus();
    });
}


/*
 * Submit add intellectual property
 * Save intellectual property ,if errors show erros as inline text else success message
 * @param string container(jQuery selector of form container)
 */
function intellectualPropertyAddFormSubmit(container, containerId) {
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'intellectual_properties/add',
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery('#intellectualPropertyGrid').length) {
                    jQuery('#intellectualPropertyGrid').data("kendoGrid").dataSource.read();
                }
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('IP');
                }
                if (containerId) {
                    // link matter to container and refresh page
                    linkIpToContainer(response.id, jQuery('#id', '#caseContainerContainer').val());
                }
                pinesMessage({
                    ty: 'success',
                    m: _lang.done_case_n_created.sprintf([getBaseURL() + 'intellectual_properties/edit/' + response.id, response.id])
                });
                jQuery('.modal', container).modal('hide');
            } else {
                jQuery('.inline-error', container).removeClass('validation-error');
                for (i in response.validationErrors) {
                    jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
                }
                scrollToValidationError(container);
            }
        }, complete: function () {
            jQuery('.btn-save', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

//initialize agent
function agentInitialization(container) {
    var containerId = '#' + container.attr('id');
    companyContactFormMatrix.commonLookup[containerId] = {
        "lookupType": jQuery("select#agent-type", container).val(),
    }
    var lookupDetails = {
        'lookupField': jQuery('#agent-lookup', container),
        'hiddenInput': jQuery('#agent-id', container),
        'errorDiv': 'agentId',
        'resultHandler': setDataToAgentField
    };
    lookupCompanyContactType(lookupDetails, container);
    jQuery('#agent-type', container).change(function () {
        jQuery("div", container).find("[data-field=agentId]").addClass('d-none').html('');
        jQuery(".inline-error", container).html('');
        jQuery("#agent-id", container).val('');
        jQuery("#agent-lookup", container).val('');
        jQuery('#agent-lookup', container).typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it.
        companyContactFormMatrix.commonLookup[containerId].lookupType = jQuery("#agent-type", container).val();
        lookupCompanyContactType(lookupDetails, container);


    });
}

//set data to agent field after adding company or contact
function setDataToAgentField(record, container) {
    var agentName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#agent-id', container).val(record.id);
    jQuery('#agent-lookup', container).val(agentName);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#agent-lookup", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#agent-lookup', container),
            'hiddenInput': jQuery('#agent-id', container),
            'errorDiv': 'agentId',
            'resultHandler': setDataToAgentField
        };
        lookupCompanyContactType(lookupDetails, jQuery(container));
    }
}

//scroll to the validation error when submitting any of the dialogs(case,contact,company,task..)
function scrollToValidationError(container) {
    jQuery('.modal-body').scrollTo(jQuery('div.validation-error:first', container).parent());
}

// initialise Hijri Date Pickers
function makeFieldsHijriDatePicker(elements) {
    if (typeof jQuery().datetimepickerhijri === "function") { // this condition is used cz the JS libraries are not loaded when this feature is not enabled
        // Umm ALqura Hijri Calendar
        var fields = elements.fields;
        for (i in fields) { 
            jQuery('#' + fields[i]).datetimepickerhijri({
                locale: { calender: 'ummalqura', lang: 'ar' }, format: "YYYY-MM-DD"
            }).on('dp.show dp.update', function () { // disable the decade view (select from available decades like month and year views) due to this issue https://github.com/fesksa/bootstrap-calendars/issues/1
                jQuery(".datepicker-years .picker-switch").removeAttr('title')
                    .css('cursor', 'default')
                    .css('background', 'inherit')
                    .on('click', function (e) {
                        e.stopPropagation();
                    });
            }).on('dp.change', function (e) {
                var newDate = new Date(hijriToGregorian(jQuery('#start-date-hijri', '#start-date-hijri-container').val()));
                var now = new Date();
                if (!jQuery('#id', '#hearing-form').val()) {
                    if (newDate > now) {
                        jQuery('.hide-on-add-future-hearing').addClass('d-none');
                    } else {
                        jQuery('.hide-on-add-future-hearing').removeClass('d-none');
                    }
                }
                if (e.oldDate != null && jQuery('#hearing-form').length && jQuery('#postponed-date-hijri', '#postponed-date-hijri-container').length) { // force trigger the change event for posponed hijri calendar
                    handlePostponeHearing(jQuery('#postponed-date-hijri', '#postponed-date-hijri-container').val(), jQuery('#start-date-hijri', '#start-date-hijri-container').val(), jQuery('#postponedTime', '#postponed-date-hijri-container').val(), jQuery('#sTime', '#start-date-hijri-container').val(), '#hearing-form');
                    var newDate = new Date(hijriToGregorian(jQuery('#start-date-hijri', '#start-date-hijri-container').val()));
                    var now = new Date();
                    if (!jQuery('#id', '#hearing-form').val()) {
                        // keep only main fields if it's a add of a hearing
                        if (newDate > now) {
                            jQuery('.hide-on-add-future-hearing').addClass('d-none');
                        } else {
                            jQuery('.hide-on-add-future-hearing').removeClass('d-none');
                        }
                    }
                    // display time spent field when changed date is less than or equal current date
                    if (newDate <= now) {
                        jQuery('#time-spent-container').removeClass('d-none');
                        jQuery('#hearing-time-spent-value').val('1:00');
                    } else {
                        jQuery('#time-spent-container').addClass('d-none');
                        jQuery('#hearing-time-spent-value').val('');
                    }
                }
            });
        }
        // show picker when clickong on the calendar icon
        jQuery('.input-group.date .input-group-addon .fa-calendar').click(function () {
            jQuery('.hijri-date-picker', jQuery(this).parent().parent()).focus();
            jQuery('.hijri-date-picker-container', jQuery(this).parent().parent()).focus();
            jQuery(".bootstrap-datetimepicker-widget", jQuery(this).parent().parent()).addClass("d-block");
        });
    }
}

//hide the license msg
function hideLicenseMessage() {
    jQuery('.lisence-message').remove();
    if (jQuery('#getting-started-container').length) {
        jQuery('.section-img', '.next-section').tipsy("hide");
        setTimeout(function () {
            jQuery('.section-img', '.next-section').tipsy({
                position: isLayoutRTL() ? 'top-left' : 'top-right', //tipsy position - top-left | top-center | top-right | bottom-left | bottom-center | bottom-right | left | right
                trigger: 'manual', // how tooltip is triggered - hover | focus | click | manual
            });
            jQuery('.section-img', '.next-section').tipsy("show");
        }, 400);
    }
    jQuery.ajax({
        url: getBaseURL() + 'home/hide_license_warning/'
    });
}

function meetingForm(event, callback) {
    event = event || false;
    callback = callback || false;
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'calendars/' + (event.ev_id !== undefined ? 'edit/' + event.ev_id : 'add'),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html && jQuery('#meeting-container').length <= 0) {
                jQuery('<div id="meeting-container"></div>').appendTo("body");
                var meetingDiv = jQuery('#meeting-container');
                meetingDiv.html(response.html);
                meetingFormEvents(meetingDiv, callback);
                if (event) {
                    var startDateAndTime = convert(event.start_date);
                    var endDateAndTime = convert(event.end_date);
                    jQuery('#title', meetingDiv).val(event.text);
                    jQuery('#start-date-input', meetingDiv).val(startDateAndTime.date);
                    jQuery('#start-date', meetingDiv).bootstrapDP('update', startDateAndTime.date);
                    jQuery('#start-time', meetingDiv).val(startDateAndTime.time);
                    jQuery('#end-date-input', meetingDiv).val(endDateAndTime.date);
                    jQuery('#end-date', meetingDiv).bootstrapDP('update', endDateAndTime.date);
                    jQuery('#end-time', meetingDiv).val(endDateAndTime.time);
                    if (event.related_case_id) {
                        jQuery('#legal-case-id', meetingDiv).val(event.related_case_id);
                        jQuery('#case-lookup', meetingDiv).val(event.related_case_subject);
                        lookUpCases(jQuery('#case-lookup', meetingDiv), jQuery('#legal-case-id', meetingDiv), 'legal_case_id', meetingDiv);
                    }
                }
                // initialize datepair
                jQuery('#date-pair', meetingDiv).datepair();
                jQuery('.modal').on('hidden.bs.modal', function () {
                    destroyModal(meetingDiv);
                    if (scheduler && !event.ev_id) {
                        scheduler.deleteEvent(event.id);
                    }

                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function meetingFormEvents(container, callback) {
    callback = callback || false;
    initTinyTemp('description', "#meeting-container", "meeting");
    lookUpCases(jQuery('#case-lookup', container), jQuery('#legal-case-id', container), 'legal_case_id', container);
    lookupPrivateUsers(jQuery('#attendees-lookup', container), 'attendees', '#selected-attendees', 'attendees-container', container);
    lookUpLocations(jQuery('#location', container), jQuery('#task_location_id', container), 'task_location_id', container);
    jQuery('#priority', container).selectpicker();
    jQuery("#event_types", container).selectpicker();
    setDatePicker('#start-date', container);
    setDatePicker('#end-date', container);
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#start-date', container), jQuery('#start-date-container', container));
        getHijriDate(jQuery('#end-date', container), jQuery('#end-date-container', container));
    }
    jQuery("#start-date", container).on('changeDate', function (e) {
        var startDate = jQuery("#start-date-input", container).val();
        var endDate = jQuery("#end-date-input", container).val();
        if (startDate > endDate) {
            jQuery('#end-date-input', container).val(startDate);
        }
        jQuery('#end-date', container).bootstrapDP('setStartDate', startDate);

    });
    jQuery('#start-time', container).timepicker({
        'timeFormat': 'H:i',
    });
    jQuery('#end-time', container).timepicker({
        'timeFormat': 'H:i',
        'showDuration': true,
    });
    initializeModalSize(container);
    checkBoxContainersValues({ 'attendees-container': jQuery('#selected-attendees', container) }, container);
    jQuery('.modal', container).modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.date-picker', container).bootstrapDP("remove");
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            meetingFormSubmit(container);
        }
    });
    jQuery('.modal-body', container).on("scroll", function () {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery("#title", container).focus();
    });
    jQuery("#event-save", container).click(function () {
        meetingFormSubmit(container, callback);
    });
    jQuery('.tooltipTable').each(function (index, element) {
        jQuery(element).tooltipster({
            content: jQuery(element).attr('tooltipTitle'),
            contentAsHTML: true,
            timer: 22800,
            animation: 'grow',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
            maxWidth: 350,
        });
    });
}

function meetingFormSubmit(container, callback) {
    callback = callback || false;
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    formData.append('description', tinymce.activeEditor.getContent());
    var id = jQuery('#event-id', container).val();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: getBaseURL() + 'calendars/' + (id ? 'edit/' + id : 'add'),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                updateGetingStartedSteps('calendar_meeting');
                pinesMessage({
                    ty: 'success',
                    m: id ? _lang.feedback_messages.updatesSavedSuccessfully : _lang.feedback_messages.addedMeetingSuccessfully
                });
                jQuery('#pendingNotifications').css('display', 'inline-block').text(response.total_notifications);
                if (response.clone == 'no') {
                    jQuery('.modal', container).modal('hide');
                    if (scheduler) {
                        scheduler.clearAll();
                        scheduler.load(getBaseURL() + 'calendars/view');
                    }
                } else {
                    jQuery("#clone", container).val("no");
                }
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('meetings');
                }
                if (isFunction(callback)) callback();
            } else {
                jQuery('.inline-error', container).removeClass('validation-error');
                for (i in response.validation_errors) {
                    jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validation_errors[i]).addClass('validation-error');
                }
                scrollToValidationError(container);
            }
        }, complete: function () {
            jQuery('.btn-save', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function enableDisablePrivacy(that, container) {
    jQuery('#private', container).val(jQuery(that).is(':checked') ? 'yes' : '');
}

//convert date time to Y-m-d H:m
function convert(date) {
    if (date instanceof Date === false) {
        date = new Date(date);
    }
    var mnth = ("0" + (date.getMonth() + 1)).slice(-2),
        day = ("0" + date.getDate()).slice(-2),
        hours = ("0" + date.getHours()).slice(-2),
        minutes = ("0" + date.getMinutes()).slice(-2);
    var dateAndTime = { 'date': [date.getFullYear(), mnth, day].join("-"), 'time': [hours, minutes].join(":") };
    return dateAndTime;
}

function confirmationDialog(key, resultHandlerArray) {
    var confirmationCategory = resultHandlerArray.confirmationCategory ? resultHandlerArray.confirmationCategory : 'default'; // this flag will be used to color the button "yes", the default is blue
    resultHandlerArray.isKeyJs = resultHandlerArray.isKeyJs || false;
    jQuery.ajax({
        url: getBaseURL() + 'home/confirm_request/',
        dataType: 'JSON',
        type: 'POST',
        data: {
            key_message: key,
            confirmation_category: confirmationCategory, // default => blue, danger => red (btn-danger), warning => orange (btn-warning), success => green (btn-warning), ...
            key_message_js: resultHandlerArray.isKeyJs ? key : '',
            confirmation_title: resultHandlerArray.confirmationTitle ? resultHandlerArray.confirmationTitle : ''
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

function deleteEvent(param) {
    jQuery.ajax({
        data: { id: scheduler.getEvent(param).ev_id },
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'calendars/delete',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                scheduler.deleteEvent(param);
            } else {
                pinesMessage({
                    ty: 'warning',
                    m: response.hearing_related ? _lang.feedback_messages.deleteMeetingHearingRelatedFailed : _lang.feedback_messages.deleteMeetingFailed
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function resultHandlerAfterCompanyAutocomplete() {
    return true;
}

function avatarUploaderForm() {
    jQuery.ajax({
        url: getBaseURL() + 'users/avatar_uploader',
        dataType: 'JSON',
        type: 'POST',
        data: {},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery(".user-avatar-container").length <= 0) {
                    jQuery('<div class="d-none user-avatar-container"></div>').appendTo("body");
                    var userAvatarContainer = jQuery('.user-avatar-container');
                    userAvatarContainer.html(response.html).removeClass('d-none');
                    jQuery('.modal', userAvatarContainer).modal({
                        keyboard: true,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery('.modal', userAvatarContainer).modal('hide');
                        }
                    });
                    jQuery("#avatar-uploader", userAvatarContainer).change(function () {
                        jQuery('.loader-submit').addClass('loading');
                        jQuery("form#avatar-uploader-form", userAvatarContainer).submit();
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(userAvatarContainer);
                        if (jQuery("#display-whats-new").val() === "") {
                            if (userGuide == "" && isloggedIn == "logged") {
                                userGuideObject.userGuideSetup();
                            }
                        } else {
                            _whatsNew();
                        }
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function avatarUploaderFormSubmit(selectedAvatar) {
    avatarNb = jQuery(selectedAvatar).data('id');
    jQuery.ajax({
        url: getBaseURL() + 'users/avatar_uploader',
        dataType: 'JSON',
        type: 'POST',
        data: { avatar_number: avatarNb },
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
        },
        success: function (response) {
            var userAvatarContainer = jQuery('.user-avatar-container');
            if (response.result) {
                if (jQuery('#getting-started-container').length) {
                    jQuery('#user-avatar', '#getting-started-container').attr('src', 'assets/images/avatars/a4l_avatar_' + avatarNb + '.png');
                    updateGetingStartedSteps('avatar');
                }
                if (jQuery('#user-profile').length) {
                    jQuery('#thumbParentLogo', '#user-profile').attr('src', 'assets/images/avatars/a4l_avatar_' + avatarNb + '.png');
                    jQuery('#thumb-profile-pic', '#user-profile').attr('src', 'assets/images/avatars/a4l_avatar_' + avatarNb + '.png');
                }
                jQuery('.header-image', '#header_div').attr('src', 'assets/images/avatars/a4l_avatar_' + avatarNb + '.png');
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.avatarUpdatedSuccessfully });
                jQuery('.modal', userAvatarContainer).modal('hide');
            }
        }, complete: function () {
            jQuery('.loader-submit').removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function avatarUploaderDone(error) {
    if (!error) {
        if (jQuery('#getting-started-container').length) {
            window.location.replace(getBaseURL() + 'dashboard');
            return false;
        }
        window.location = window.location.href;
    } else {
        jQuery('.loader-submit').removeClass('loading');
        jQuery('#error-message').html(error);
    }
}

function hideGettingStarted() { // hide Getting Started
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'dashboard/getting_started',
        data: { hide: true },
        success: function () {
            window.location.replace(getBaseURL() + 'dashboard');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function updateGettingStartedHelpers(step) {
    var step_translation = step == 'legal_matter' ? 'corporate_matter' : step;
    jQuery('.section', '.steps-section').removeClass('next-section');
    jQuery('.section-img', '.' + step + '-section').tipsy("hide");
    var nextStepSection = jQuery('.' + step + '-section', '.steps-section').addClass('next-section');
    jQuery('.section-img', nextStepSection).attr('title', step === 'avatar' ? _lang.startByChoosingAvatar : _lang.startByAdding.sprintf([_lang.custom[step_translation]]));
    setTimeout(function () {
        jQuery('.section-img', nextStepSection).tipsy({
            position: isLayoutRTL() ? 'top-left' : 'top-right', //tipsy position - top-left | top-center | top-right | bottom-left | bottom-center | bottom-right | left | right
            trigger: 'manual', // how tooltip is triggered - hover | focus | click | manual
        });
        jQuery('.section-img', nextStepSection).tipsy("show");
    }, 400);
    jQuery(".next-step").remove();
    jQuery("<div class='next-step'></div>").insertAfter('.' + step + "-section .section-img");
    jQuery(window).bind('resize', (function () {
        jQuery('.section-img', nextStepSection).tipsy({
            position: isLayoutRTL() ? 'top-left' : 'top-right',
            trigger: 'manual'
        });
        jQuery('.section-img', nextStepSection).tipsy("show");
    }));
}

function clientLookup(parameters) {
    parameters.lookupField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL('money') + 'clients/autocomplete',
                dataType: "json",
                data: request,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: {
                                id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.name,
                                value: item.name,
                                record: item
                            };
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (typeof parameters.callback !== "undefined") {
                parameters.callback(ui);
            }
            if (ui.item.record.id > 0) {
                if (typeof parameters.hiddenField !== "undefined" && parameters.hiddenField !== null) {
                    parameters.hiddenField.val(ui.item.record.id);
                }
            }
        }
    });
}

function fetchCaseClient(parameters) {
    jQuery.ajax({
        url: getBaseURL() + 'cases/fetch_case_client/' + parameters.caseId,
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response.clientId != null && response.clientName != null) {
                parameters.clientHiddenField.val(response.clientId);
                parameters.clientLookupField.val(response.clientName);
                parameters.clientLookupField.attr("readonly", true);
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function HijriConverter(dateContainer, oldPicker, convertToGregorian) {
    oldPicker = oldPicker || false;//true if the picker is from the old picker => to be removed when revamping all pickers that use this function
    convertToGregorian = convertToGregorian || false;
    dateContainer = dateContainer || '';
    var minDate = -Infinity, maxDate = Infinity;
    var hijriLink = jQuery('#date-conversion', '.visualize-hijri-date');
    jQuery.ajax({
        url: getBaseURL() + 'home/hijri_date_converter/',
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response.html) {
                if (jQuery("#hijri-converter-container").length <= 0) {
                    jQuery('<div id="hijri-converter-container"></div>').appendTo("body");
                    var hijriConverterContainer = jQuery('#hijri-converter-container');
                    hijriConverterContainer.html(response.html);
                    moment.locale(_lang.languageSettings['langName'] == 'arabic' ? 'ar-sa' : _lang.languageSettings['langName'].substr(0, 2));
                    jQuery('.select-picker', hijriConverterContainer).selectpicker();
                    setDatePicker('#gregorian', hijriConverterContainer);
                    jQuery('.modal', hijriConverterContainer).modal({
                        keyboard: true,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery('.modal', hijriConverterContainer).modal('hide');
                        }
                    });
                    jQuery('.modal', hijriConverterContainer).on('hidden.bs.modal', function () {
                        destroyModal(hijriConverterContainer);
                    });
                    if (dateContainer) {
                        if (!oldPicker) {
                            if (jQuery(dateContainer).bootstrapDP('getStartDate') !== -Infinity) {
                                minDate = moment(jQuery(dateContainer).bootstrapDP('getStartDate')).format('YYYY-MM-DD');
                            }
                            if (jQuery(dateContainer).bootstrapDP('getEndDate') !== Infinity) {
                                maxDate = moment(jQuery(dateContainer).bootstrapDP('getEndDate')).format('YYYY-MM-DD');
                            }
                        }
                        jQuery('#gregorian', hijriConverterContainer).bootstrapDP('setStartDate', minDate);
                        jQuery('#gregorian', hijriConverterContainer).bootstrapDP('setEndDate', maxDate);
                        var inputDate = jQuery('#' + dateContainer.attr('id'), dateContainer.prevObject.selector);
                        
                        jQuery('#insert-value-to-date-field', hijriConverterContainer).removeClass('d-none');
                        var hijriYearContainer = jQuery('#hijri-year', hijriConverterContainer);
                        var hijriMonthContainer = jQuery('#hijri-month', hijriConverterContainer);
                        var hijriDayContainer = jQuery('#hijri-day', hijriConverterContainer);
                        if (convertToGregorian) {
                            hijriYearContainer.val(inputDate.val().substring(0, 4)); 
                            hijriMonthContainer.val(inputDate.val().substring(5, 7) <= 9 ? inputDate.val().substring(6, 7) : inputDate.val().substring(5, 7)).selectpicker('refresh');
                            hijriDayContainer.val(inputDate.val().substring(8, 10) <= 9 ? inputDate.val().substring(9, 10) : inputDate.val().substring(8, 10)).selectpicker('refresh');

                            toGregorian();
                        } else {
                            jQuery('#gregorian', hijriConverterContainer).bootstrapDP('update', oldPicker ? dateContainer.val() : inputDate.val());
                            toHijri();
                        }
                        //insert the gregorian value into the date field and set the hijri date as a title
                        jQuery('#insert-value-to-date-field', hijriConverterContainer).on('click', function () {
                            var gregorianDateValue = jQuery('#gregorian-date', hijriConverterContainer).val();
                            var hijriDateValue = hijriYearContainer.val() + '-' + (hijriMonthContainer.val() > 9 ? hijriMonthContainer.val() : '0' + hijriMonthContainer.val()) + '-' + (hijriDayContainer.val() > 9 ? hijriDayContainer.val() : '0' + hijriDayContainer.val());
                            if (oldPicker) {
                                dateContainer.val(convertToGregorian ? hijriDateValue : gregorianDateValue).datepicker('refresh');
                                
                                inputDate.val(convertToGregorian ? hijriDateValue : gregorianDateValue);

                                if (gregorianDateValue) {
                                    var hijriValue = _lang.hijriDate + ': ' + toHijri();
                                    dateContainer.attr('title', _lang.hijriDate + ': ' + toHijri());
                                    hijriLink.attr('title', _lang.hijriDate + ': ' + toHijri());
                                }
                            } else {//modal dialog => bootstrap datepicker
                                dateContainer.bootstrapDP('update', convertToGregorian ? hijriDateValue : gregorianDateValue);
                                if (gregorianDateValue) {
                                    var hijriValue = _lang.hijriDate + ': ' + toHijri();
                                    inputDate.attr('title', hijriValue);
                                    hijriLink.attr('title', hijriValue);
                                } else {
                                    inputDate.attr('title', '');
                                    hijriLink.attr('title', _lang.hijriDateConverter);
                                }
                            }
                        });
                    }
                    jQuery('#gregorian', hijriConverterContainer).on('changeDate', function (e) {
                        toHijri();
                    });
                    jQuery('#gregorian-date', hijriConverterContainer).on('keyup', function (e) {
                        toHijri();
                    });
                    jQuery('#hijri-year', hijriConverterContainer).on('blur', function () {
                        toGregorian();
                    });
                    jQuery('#hijri-month', hijriConverterContainer).on('change', function () {
                        toGregorian();
                    });
                    jQuery('#hijri-day', hijriConverterContainer).on('change', function () {
                        toGregorian();
                    });
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function toGregorian() {
    var hijriConverterContainer = jQuery('#hijri-converter-container');
    var hijriYear = jQuery('#hijri-year', hijriConverterContainer).val();
    var hijriMonth = jQuery('#hijri-month', hijriConverterContainer).val();
    var hijriDay = jQuery('#hijri-day', hijriConverterContainer).val();
    var hijriDate = hijriYear + '-' + hijriMonth + '-' + hijriDay;
    m = moment(hijriDate, 'iYYYY-iM-iD');
    if (m.format('YYYY-M-D') !== 'Invalid date') {
        jQuery('#gregorian', hijriConverterContainer).bootstrapDP('update', m.lang("en").format('YYYY-M-D'));
    } else {
        jQuery('#gregorian-date', hijriConverterContainer).val('');
    }
}

function toHijri() {
    var hijriConverterContainer = jQuery('#hijri-converter-container');
    var gregorian = jQuery('#gregorian-date', hijriConverterContainer).val();
    m = moment(gregorian);
    if (m.format('iYYYY-iM-iD') !== 'Invalid date') {
        jQuery('#hijri-year', hijriConverterContainer).val(m.iYear());
        jQuery('#hijri-month', hijriConverterContainer).val(m.iMonth() + 1).selectpicker('refresh');
        jQuery('#hijri-day', hijriConverterContainer).val(m.iDate()).selectpicker('refresh');
        return m.format('iYYYY-iMMMM-iD');
    } else {
        jQuery('#hijri-year', hijriConverterContainer).val('');
        jQuery('#hijri-month', hijriConverterContainer).val('').selectpicker('refresh');
        jQuery('#hijri-day', hijriConverterContainer).val('').selectpicker('refresh');
    }
}

function getHijriDate(dateContainer, dateCotainer, oldPicker) {
    var hijriLink = jQuery('#date-conversion', dateCotainer);
    oldPicker = oldPicker || false;//true if the picker is from the old picker => to be removed when revamping all pickers that use this function
    var changedDate;
    moment.locale(_lang.languageSettings['langName'] == 'arabic' ? 'ar-sa' : _lang.languageSettings['langName'].substr(0, 2));
    if (oldPicker) {
        if (dateContainer.val()) {
            m = moment(dateContainer.val());
            if (m.format('iYYYY-iM-iD') !== 'Invalid date') {
                dateContainer.attr('title', _lang.hijriDate + ': ' + m.format('iYYYY-iMMMM-iD'));
                hijriLink.attr('title', _lang.hijriDate + ': ' + m.format('iYYYY-iMMMM-iD'));
            }
        } else {
            dateContainer.attr('title', '');
            hijriLink.attr('title', _lang.hijriDateConverter);
        }
        jQuery(dateContainer).change(function () {
            changedDate = moment(dateContainer.val());
            if (dateContainer.val()) {
                dateContainer.attr('title', _lang.hijriDate + ': ' + changedDate.format('iYYYY-iMMMM-iD'));
                hijriLink.attr('title', _lang.hijriDate + ': ' + changedDate.format('iYYYY-iMMMM-iD'));
            } else {
                dateContainer.attr('title', '');
                hijriLink.attr('title', _lang.hijriDateConverter);
            }
        });
    } else {
        var inputDate = jQuery('#' + dateContainer.attr('id') + '-input', dateContainer.prevObject.selector);
        var inputDateValue = inputDate.val();
        if (inputDateValue) {
            m = moment(inputDateValue);
            if (m.format('iYYYY-iM-iD') !== 'Invalid date') {
                inputDate.attr('title', _lang.hijriDate + ': ' + m.format('iYYYY-iMMMM-iD'));
                hijriLink.attr('title', _lang.hijriDate + ': ' + m.format('iYYYY-iMMMM-iD'));
            }
        } else {
            inputDate.attr('title', '');
            hijriLink.attr('title', _lang.hijriDateConverter);
        }
        jQuery(dateContainer).on('changeDate', function (e) {
            changedDate = moment(inputDate.val());
            inputDate.attr('title', _lang.hijriDate + ': ' + changedDate.format('iYYYY-iMMMM-iD'));
            hijriLink.attr('title', _lang.hijriDate + ': ' + changedDate.format('iYYYY-iMMMM-iD'));
        });
        jQuery(inputDate).on('blur', function () {
            changedDate = moment(inputDate.val());
            if (inputDate.val()) {
                inputDate.attr('title', _lang.hijriDate + ': ' + changedDate.format('iYYYY-iMMMM-iD'));
                hijriLink.attr('title', _lang.hijriDate + ': ' + changedDate.format('iYYYY-iMMMM-iD'));
            } else {
                inputDate.attr('title', '');
                hijriLink.attr('title', _lang.hijriDateConverter);
            }
        });
    }
}

//fix footer top position
function fixFooterPosition() {
    var gridHeaderHeight1 = 0;
    var headerHeight = jQuery('#header_div').outerHeight(true);
    var gridHeaderHeight = jQuery('.grid-main-container').outerHeight(true);
    if (!gridHeaderHeight)
        gridHeaderHeight1 = jQuery('.main-grid-container').outerHeight(true) - jQuery('.grid-container').outerHeight(true) + 50;
    var gridContent = jQuery('.grid-container').outerHeight(true);
    var licenseMsgHeight = jQuery('.lisence-message').outerHeight(true);
    var top = headerHeight + gridHeaderHeight + gridHeaderHeight1 + gridContent + (licenseMsgHeight ? licenseMsgHeight : 0);
    var windowHeight = jQuery(window).height();
    if (windowHeight > top + 100) {
        top = windowHeight - jQuery('#footer').height();
        jQuery('#footer').css('top', top + 'px');
    } else {
        jQuery('#footer').css('top', top + 20 + 'px');
    }
}

function advancedSearchFilters(oldGrid) {
    oldGrid = oldGrid || false;
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        loadEventsForFilters();
        jQuery('#filtersFormWrapper').slideDown();
        if (jQuery('#submitAndSaveFilter').is(':visible')) {
            jQuery("#advancedSearchFields").removeClass('d-none');
        }
    }
    jQuery('html, body').animate({ scrollTop: 0 }, 0);
    if (!oldGrid) {
        reinitializeGridFixedNav();
    }
}

function hideAdvancedSearch(oldGrid) {
    oldGrid = oldGrid || false;
    jQuery('#filtersFormWrapper').slideUp(1, function () {
        if (!oldGrid) {
            fixFooterPosition();
        }
    });
}

// add backgroud to fit nav header and window footer
function resizeHeaderAndFooter() {
    var elementTop = jQuery('.grid-header').outerHeight(true);
    var elementWidth = jQuery('#fixed-navbar').width();
    //jQuery('.grid-header-hidden-background').css('height', elementTop + 'px').css('width', elementWidth + 15);
    if (jQuery('#subNavMenu').length > 0) {
        // jQuery('#subNavMenu').css('width', elementWidth + 15);
        jQuery('#tabs-nav-menu-items').removeClass('margin-bottom').parent().removeClass('margin-bottom');
    }
    if (jQuery('#expense-info-message').length > 0) {
        jQuery('#expense-info-message').removeClass('centered-text').css('width', elementWidth + 30).css('margin-bottom', 0);
    }
    if (jQuery('#grid-scrollable', '.grid-main-container').length > 0) {
        jQuery('.footer-hidden-background').removeClass('d-none').css('width', elementWidth);
    } else {
        jQuery('.footer-hidden-background').removeClass('d-none').css('width', elementWidth - 18);
    }
    fixGridPaginationList();
}

function revertAllFilters() {
    jQuery('#gridFiltersList').val('');
    jQuery('#submitAndSaveFilter').addClass('d-none');
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
    var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);
    if (msie < 0 && !isIE11) {
        resetFormFields();
    }
}

function whatsNew() {
    if (jQuery("#display-whats-new").val()) {
        if ((typeof openAvatarForm !== 'undefined' && !openAvatarForm) || (typeof openAvatarForm === 'undefined')) {
            _whatsNew();
        }
    }
}

function _whatsNew() {
    jQuery.ajax({
        url: getBaseURL() + "home/whats_new_popup/",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var whatsNewDialogId = "#whats-new-dialog";
                if (jQuery(whatsNewDialogId).length <= 0) {
                    jQuery("<div id='whats-new-dialog'></div>").appendTo("body");
                    var whatsNewDialog = jQuery(whatsNewDialogId);
                    whatsNewDialog.html(response.html);
                    initializeModalSize(whatsNewDialog);
                    jQuery(".modal", whatsNewDialog).modal({
                        keyboard: false,
                        backdrop: "static",
                        show: true
                    });
                    jQuery(".modal", whatsNewDialog).on("hidden.bs.modal", function () {
                        if (userGuide == "" && isloggedIn == "logged") {
                            userGuideObject.userGuideSetup();
                        }
                    });
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery(".modal", whatsNewDialog).modal("hide");
                        }
                    });
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function saveGridColumns(restoreDefaultColumns, orderedColumnsArray, numberOfCustomFields) {
    restoreDefaultColumns = restoreDefaultColumns || false;
    orderedColumnsArray = orderedColumnsArray || [];
    numberOfCustomFields = numberOfCustomFields || false;
    formData = [];
    if (orderedColumnsArray.length === 0) {
        var formData = jQuery("form#selected-columns").serializeArray();
        if (restoreDefaultColumns) {
            formData.push({ name: "restore_default_columns", value: restoreDefaultColumns });
        }
    } else {
        formData.push({ name: "ordered_columns", value: orderedColumnsArray });
    }
    formData.push({ name: "model", value: jQuery('#model').val() });
    var selectedFilter = jQuery('#gridFiltersList').val();
    if (selectedFilter && hearingFixedFilters.indexOf(selectedFilter) === -1 && selectedFilter != 'awaiting_my_approvals') {
        formData.push({ name: "grid_saved_filter_id", value: selectedFilter });
    }
    if (numberOfCustomFields) {
        var countCustomFields = 0;
        if (formData) {
            jQuery.each(formData, function (index, value) {
                if (value.value.match("^custom_field_")) {
                    countCustomFields++;
                }
            });
            if (countCustomFields > numberOfCustomFields) {
                pinesMessage({ ty: 'warning', m: _lang.limitCustomFieldWarning.sprintf([numberOfCustomFields]) });
            } else {
                jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover('hide');
                savGridDetails(formData);
            }
        }
    } else {
        savGridDetails(formData);
    }
}

function savGridDetails(formData) {
    if (formData && formData[0].value && formData[0].value instanceof Array && formData[1]
        && (formData[1].value === "User_Activity_Log" || formData[1].value === "User_Activity_Log_Money_Module")) {
        formData[0].value = formData[0].value.map(col => col === "allRecordsClientName" ? 'clientName' : col);
    }

    jQuery.ajax({
        url: getBaseURL() + "grid_saved_columns/save_grid_details/",
        dataType: "JSON",
        type: "POST",
        data: formData,
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result && _lang.languageSettings['langName'] === 'arabic') {
                jQuery('.k-grid-header', '.grid-main-container').removeClass('padding-right');
            }
            if (!response.result) {
                if (response.feedbackMessage != undefined) {
                    pinesMessage({ ty: response.feedbackMessage.ty, m: response.feedbackMessage.m });
                } else {
                    pinesMessage({ ty: 'error', m: _lang.updatesFailed });
                }
                jQuery("#loader-global").hide();
            }
            if (response.result || (!response.result && !restoreDefaultColumns && orderedColumnsArray.length)) {
                setGridDetails(response.gridDetails);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function getTranslation(fieldValue) {
    return _lang[fieldValue];
}

function gridEvents() {
    var options = {
        html: true,
        trigger: 'click',
        placement: 'bottom',
        sanitize: false,
        content: jQuery('#columns-suggestions').html(),
        container: jQuery('#column-picker-trigger-container')
    };
    jQuery('#columns-suggestions').remove();
    jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover(options);
    jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').on('shown.bs.popover', function () {
        var lastScrollLeft = 0;
        jQuery(window).scroll(function () {
            var documentScrollLeft = jQuery(document).scrollLeft();
            if (jQuery('#filtersFormWrapper').is(':visible') && !jQuery('#grid-header-fixed-navbar').hasClass('scroll-to-fixed-fixed')) {
                jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover('hide');
            }
            if (lastScrollLeft !== documentScrollLeft) {
                jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover('hide');
            }
            lastScrollLeft = documentScrollLeft;
        });
        jQuery('.restore-defaults-columns').on('click', function () {
            saveGridColumns(true);
            jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover('hide');
        });
        jQuery('#submit-grid-columns').on('click', function () {
            jQuery("#task-contributors").prop('disabled', false).trigger('chosen:updated'); // temporarily enable the task-contributors dropdown so the value can be sent
            if (typeof selectedCustomFieldsLimit !== 'undefined') {
                saveGridColumns(false, [], selectedCustomFieldsLimit);
            } else {
                saveGridColumns();
                jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover('hide');
            }
        });
        jQuery('.cancel-popover').on('click', function () {
            jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover('hide');
        });
        jQuery(document).keyup(function (e) {
            if (e.keyCode == 27) {
                jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover('hide');
            }
        });
        jQuery('#search-columns-suggestions', '#selected-columns').keypress(function (e) {
            // Enter pressed?
            if (e.which == 13) {
                e.preventDefault();
            }
        });
    });
    jQuery('body').on('click', function (e) {
        //the 'is' for buttons that trigger popups
        //the 'has' for icons within a button that triggers a popup
        if (!jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').is(e.target) && jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').has(e.target).length === 0 && jQuery('.popover', '#column-picker-trigger-container').has(e.target).length === 0) {
            jQuery('[data-toggle="popover"]', '#column-picker-trigger-container').popover('hide');
        }
    });
    if (_lang.languageSettings['langDirection'] === 'rtl')
        gridScrollRTL();
    jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
    if (jQuery('.grid-main-container').length > 0) { //applied only for the grids that have the new design
        setTimeout(function () {
            fixFooterPosition();
        }, 300);
    }
    jQuery('.k-pager-info-top', '.k-grid-info-refresh-top').text(jQuery('.k-pager-info', '.k-grid-pager').text());
}

function orderColumns(e) {
    var columns = e.sender.columns.slice(0);
    var column = columns.splice(e.oldIndex, 1)[0];
    columns.splice(e.newIndex, 0, column);
    var orderedColumns = [];
    columns.map(function (item) {
        for (i in item) {
            if (i == 'field' && item['title'].trim() && item['title'] !== _lang.actions) {
                orderedColumns.push(item[i]);
            }
        }
    });
    saveGridColumns(false, orderedColumns);
}
String.prototype.replaceAt = function (index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}
function displayCourtReferenceContent(unsafe, hijriCalendarEnabled) {
    if (hijriCalendarEnabled) {
        var textArr = unsafe.split("\n");
        for (i in textArr) {
            var line = textArr[i];
            var refDate = line.substring(line.lastIndexOf("/ ") + 1, line.length);
            var hijriDate = kendo.toString(gregorianToHijri(refDate), 'yyyy-MM-dd');
            textArr[i] = line.replaceAt(line.lastIndexOf("/ ") + 2, hijriDate);
        }
        unsafe = textArr.join("\n");
    }
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
function displayContent(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
function alertLicenseExpirationMsg() {
    pinesMessage({
        ty: 'information',
        m: clientLicenseType === 'lead' ? _lang.feedback_messages.trialLicenseExpiration.sprintf(['subscribe()']) : _lang.feedback_messages.clientLicenseExpiration.sprintf([contactUsLink]),
        d: 0
    });
}

function gridTriggers(gridDetails) {
    if (gridDetails.gridColumnsLength > 7) {
        jQuery('#grid-unscrollable').attr('id', 'grid-scrollable');
    } else {
        jQuery('#grid-scrollable').attr('id', 'grid-unscrollable');
    }
    if (undefined == gridDetails.gridContainer.data('kendoGrid')) {
        gridDetails.gridContainer.kendoGrid(gridDetails.gridOptions);
        fixGridHeader(true);
    } else {
        gridDetails.gridContainer.empty().kendoGrid(gridDetails.gridOptions);
        jQuery('#grid-header-fixed-navbar').trigger('detach.ScrollToFixed');
        jQuery('#fixed-navbar').trigger('detach.ScrollToFixed');
        fixGridHeader();
    }
    jQuery('[data-role="dropdownlist"]', gridDetails.gridContainer).change(function (e) {
        savePageSize = true;
    });
}

function validateWebsite(field, rules, i, options) {
    var websitePattern = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/;
    if (!websitePattern.test(field.val())) {
        return _lang.invalidUrl;
    }
}

function integrationPopup() {
    jQuery.ajax({
        url: getBaseURL() + "calendars/integrations_list",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var calendarIntegrationsDialogId = "#calendar-integration-dialog";
                if (jQuery(calendarIntegrationsDialogId).length <= 0) {
                    jQuery("<div id='calendar-integration-dialog'></div>").appendTo("body");
                    var calendarIntegrationsDialog = jQuery(calendarIntegrationsDialogId);
                    calendarIntegrationsDialog.html(response.html);
                    initializeModalSize(calendarIntegrationsDialog);
                    jQuery(".modal", calendarIntegrationsDialog).modal({
                        keyboard: false,
                        backdrop: "static",
                        show: true
                    });
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery(".modal", calendarIntegrationsDialog).modal("hide");
                        }
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(calendarIntegrationsDialog);
                    });
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function resetIntegrationToggle(params) {
    params.integrationToggle.bootstrapToggle(params.status);
}

function enableCalendarIntegration(integrationType, integrationToggle) {
    jQuery.ajax({
        url: getBaseURL() + 'calendar_integrations/index',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'status': 'on',
            'integration_type': integrationType,
            'pre_integration_location': window.location.href
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.calendarIntegrationEnabled) {
                if (!jQuery('#calendar-integration-message').length) {
                    jQuery('#integration-list').prepend('<div id="calendar-integration-message" class="alert alert-warning"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + _lang.cannotSyncWithBothCalendars + '</div>');
                }
                resetIntegrationToggle({ status: 'off', integrationToggle: integrationToggle });
            } else {
                window.location = response.oauthUrl;
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function disableCalendarIntegration(params) {
    jQuery.ajax({
        url: getBaseURL() + 'calendar_integrations/index',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'status': 'off'
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.syncTurnedOff) {
                params.integrationCalendarLabel.remove();
                params.integrationStatusHolder.val('no');
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function makeFieldsTimePicker(fields) {
    for (i in fields) {
        jQuery("#" + fields[i]).timepicker({ 'timeFormat': 'H:i' });
    }
}

function resultHandlerAfterContactsLegalCasesAutocomplete() {
    return true;
}

function resultHandlerAfterContactsLegalCaseContainersAutocomplete() {
    return true;
}

function resultHandlerAfterCompaniesLegalCasesAutocomplete() {
    return true;
}

function reminderForm(id, action, caseId, contractId, callback) {
    id = id || false;
    action = action || false;
    contractId = contractId || false;
    callback = callback || false;
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'reminders/' + (action ? action : (id ? 'edit' : 'add')),
        data: id ? { id: id } : '',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html && jQuery('#reminder-form-container').length <= 0) {
                jQuery('<div id="reminder-form-container"></div>').appendTo("body");
                var reminderFormContainer = jQuery('#reminder-form-container');
                reminderFormContainer.html(response.html);
                if (jQuery('#company-reminders-search-filters').length > 0) {
                    jQuery('#lookup-company-id', reminderFormContainer).val(jQuery('#companyValue', '#company-reminders-search-filters').val());
                    jQuery('#lookup-companies', reminderFormContainer).val(jQuery('#company-name', '#company-reminders-search-filters').val());
                }
                if (caseId) {
                    if (jQuery('#legal-case-reminders-search-filters').length > 0) {
                        jQuery('#lookup-case-id', reminderFormContainer).val(jQuery('#quickSearchFilterLegalCaseIdValue', '#legal-case-reminders-search-filters').val());
                        jQuery('#lookup-cases', reminderFormContainer).val(jQuery('#case-subject', '#legal-case-reminders-search-filters').val());
                    }
                    if (jQuery('#case-events-container').length > 0) {
                        jQuery('#lookup-cases', reminderFormContainer).val(jQuery('#legal-case-subject', '#case-events-container').val());
                        jQuery('#lookup-case-id', reminderFormContainer).val(jQuery('#legal-case', '#case-events-container').val());
                    }
                }
                if (contractId) {
                    if (jQuery('#reminders-grid').length > 0) {
                        jQuery('#lookup-contract-id', reminderFormContainer).val(jQuery('#contractIdInPage', '#gridFormContent').val());
                        jQuery('#lookup-contract', reminderFormContainer).val(jQuery('#contract-full-name', '#gridFormContent').val());
                    }
                }
                if (action === 'postpone') {
                    jQuery("form#reminder-form :input", reminderFormContainer).attr("disabled", true);
                    jQuery("input[type=hidden]", reminderFormContainer).removeAttr("disabled");
                    jQuery("#remind-on-date-input", reminderFormContainer).removeAttr("disabled");
                    jQuery("#remind-on-time", reminderFormContainer).removeAttr("disabled");
                    jQuery("#remind-me", reminderFormContainer).hide();
                    jQuery(".users-lookup-icon", reminderFormContainer).addClass('disabled');
                    commonModalDialogEvents(reminderFormContainer);
                    jQuery("#form-submit", reminderFormContainer).click(function () {
                        reminderFormSubmit(id, reminderFormContainer, 'postpone', callback);
                    });
                    jQuery(reminderFormContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            reminderFormSubmit(id, reminderFormContainer, 'postpone', callback);
                        }
                    });
                } else {
                    commonModalDialogEvents(reminderFormContainer, reminderFormValidate, callback);
                }
                reminderFormEvents(reminderFormContainer);
            }
            if (typeof response.status !== 'undefined' && !response.status) {
                pinesMessage({ ty: 'error', m: _lang.invalid_record });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function reminderFormEvents(container) {
    initializeModalSize(container);
    jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
    
    if (jQuery('.hijri-date-picker', '#remind-on-date').length > 0)
     makeFieldsHijriDatePicker({ fields: ['remind-on-date-input'] }); 
    else
     // * Important For Gregorian Date Picker //
     setDatePicker('#remind-on-date', container);
    
   
    if (jQuery('.hijri-date-picker', '#repeat-until-date').length > 0)
     makeFieldsHijriDatePicker({ fields: ['repeat-until-date-input'] }); 
    else
     // * Important For Gregorian Date Picker //
     setDatePicker('#remind-on-date-recurrence', container); 
   
     jQuery('.time-picker', container).timepicker({
        'timeFormat': 'H:i',
    });
    jQuery('#recurring-type', container).change(function () {
        if (this.value) {
            jQuery("#recurrence-stop-date-container", container).removeClass('d-none');
        } else {
            jQuery("#recurrence-stop-date-container", container).addClass('d-none');
            jQuery("#repeat-until-date-input", container).val('');
        }
    });
    lookUpUsers(jQuery('#lookup-user-to-remind', container), jQuery('#user-to-remind-id', container), 'user_id', jQuery('.user-to-remind-container', container), container);
    lookUpCases(jQuery('#lookup-cases', container), jQuery('#lookup-case-id', container), 'legal_case_id', container);
    var lookupDetails = {
        'lookupField': jQuery('#lookup-contacts', container),
        'errorDiv': 'contact_id',
        'hiddenId': '#lookup-contact-id',
        'resultHandler': 'reminderContactResultHandler'
    };
    lookUpContacts(lookupDetails, container);
    lookupDetails = {
        'lookupField': jQuery('#lookup-companies', container),
        'errorDiv': 'companies_contacts',
        'hiddenId': '#lookup-company-id',
        'isBoxContainer': false
    };
    lookUpCompanies(lookupDetails, container);
    lookUpTasks({
        'lookupField': jQuery('#lookup-tasks', container),
        'hiddenId': jQuery('#lookup-task-id', container),
        'errorDiv': 'task_id'
    }, container);
    lookUpContracts({
        'lookupField': jQuery('#lookup-contract', container),
        'hiddenId': jQuery('#lookup-contract-id', container),
        'errorDiv': 'contract_id'
    }, container);

}

function reminderContactResultHandler(record, container) {
    var clientName = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#lookup-contact-id', container).val(record.id);
    jQuery('#lookup-contacts', container).val(clientName);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#lookup-contacts", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#lookup-contacts', container),
            'errorDiv': 'contact_id',
            'hiddenId': '#lookup-contact-id',
            'resultHandler': 'reminderContactResultHandler'
        };
        lookUpContacts(lookupDetails, container);
    }
}

function reminderFormValidate(container, callback) {
    callback = callback || false;
    var id = jQuery('#reminder-id', container).val();
    var recurrenceId = jQuery('#recurrence-id', container).val();
    var modelOpen = jQuery('.modal', container);
    var origForm = jQuery('form', modelOpen).data('serialize');
    var editedForm = jQuery('form', modelOpen).serialize();
    var submit = false;
    if (id && recurrenceId && origForm != editedForm) {
        if (jQuery('#recurrence-options-container').length <= 0) {
            jQuery('<div id="recurrence-options-container"></div>').appendTo("body");
            var recurrenceContainer = jQuery('#recurrence-options-container');
            recurrenceContainer.html(jQuery('#recurrence-options', container).html());
            jQuery('.tooltip-title', recurrenceContainer).tooltipster();
            jQuery('.modal-container', recurrenceContainer).addClass('modal');
            commonModalDialogEvents(recurrenceContainer);
            jQuery("#form-submit", recurrenceContainer).click(function () {
                jQuery('#recurrence-modification', container).val(jQuery('input[type=radio]:checked', recurrenceContainer).val() == 1 ? 'yes' : 'no');
                jQuery(".modal", recurrenceContainer).modal("hide");
                reminderFormSubmit(id, container, false, callback);
            });
        }
    } else {
        submit = true;
    }
    if (submit) {
        reminderFormSubmit(id, container, false, callback);
    }
}

function reminderFormSubmit(id, container, action, callback) {
    action = action || false;
    callback = callback || false;
    var formData = jQuery("form#reminder-form", container).serializeArray();

    for (index = 0; index < formData.length; ++index) {       
        if (formData[index].name == "remindDate" && (jQuery('.hijri-date-picker', '#remind-on-date').length > 0))
        {
          convertToGregorianPrePost(jQuery("#remind-on-date-input"), jQuery('#remindDate-gregorian') );
          formData[index].value =  jQuery('#remindDate-gregorian', '#reminder-date-hijri-container').val();
        } 
        else if (formData[index].name == "recurrence[stop_date]" && jQuery('.hijri-date-picker', '#repeat-until-date').length > 0)
        {
                    convertToGregorianPrePost(jQuery("#repeat-until-date-input"), jQuery('#repeat-until-date-input-gregorian') );
                    formData[index].value = jQuery('#repeat-until-date-input-gregorian', '#recurrence-stop-date-container').val();
        }    
    }

    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'reminders/' + (action ? action : (id ? 'edit' : 'add')),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                pinesMessage({ ty: 'success', m: response.message });
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ ty: 'warning', m: response.display_message });
                }
                loadUserLatestReminders('refresh');
                reminderCallBack();
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('reminders');
                }
                if (jQuery("#related-reminders-grid").length > 0) {
                    jQuery("#related-reminders-grid").data("kendoGrid").dataSource.read()
                }
                if (isFunction(callback)) callback();
            } else {
                if (response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                    return false;
                }
                pinesMessage({ ty: 'error', m: _lang.saveRecordFailed.sprintf([_lang.newReminder]) });
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

//common functions used to all bootstrap modal dialog
function commonModalDialogEvents(container, submitFunction, callback, onDestroy) {
    submitFunction = submitFunction || false;
    callback = callback || false;
    onDestroy = onDestroy || false;
    licensePackageAccessCheck();
    jQuery(".modal", container).modal({
        keyboard: false,
        backdrop: "static",
        show: true
    });
    jQuery('#pendingReminders').parent().popover('hide');
    jQuery('.modal-body', container).on("scroll", function () {
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
        jQuery('.datepicker-dropdown').css('display', 'none');
        if (onDestroy && isFunction(onDestroy)) onDestroy();
        destroyModal(container);
    });
    if (submitFunction) {
        jQuery("#form-submit", container).click(function () {
            callback ? submitFunction(container, callback) : submitFunction(container);
        });
        jQuery(container).find('input').keypress(function (e) {
            // Enter pressed?
            if (e.which == 13) {
                e.preventDefault();
                callback ? submitFunction(container, callback) : submitFunction(container);
            }
        });
    }
    jQuery('.search').attr('autocomplete', 'off');
    jQuery('.lookup').attr('autocomplete', 'off');
}

function addMe(field) {
    field = field || false;
    field.hidden_id.val(authIdLoggedIn).trigger('change');
    field.lookup_field.val(userLoggedInName);
    //if the validation error is found for the user lookup,then it will be cleared after clicking on add me link
    jQuery(".inline-error", field.lookup_container).addClass('d-none');
    //typeahead will be destroyed after clicking on add me link to avoid conflict of the previous chosen user form the dropdown and the user filled after the click
    destroyUserLookup(field);
}

//typeahead will be destroyed to avoid saving of the previous chosen user form the dropdown
function destroyUserLookup(field) {
    if (jQuery('.twitter-typeahead', field.lookup_container).length) {
        field.lookup_field.typeahead('destroy');
        lookUpUsers(field.lookup_field, field.hidden_id, 'user_id', field.lookup_container, field.container);
    }
}

function showHideCloneUsers(container) {
    var cloneUsersCheckbox = jQuery('#clone-users');
    if (cloneUsersCheckbox.is(':checked')) {
        jQuery('#cloned-users-container').removeClass('d-none');
        lookupPrivateUsers(jQuery('#lookup-clone-users', container), 'cloned_users', '#selected-users', 'cloned-users-container', container);
    } else {
        jQuery('#cloned-users-container').addClass('d-none');
        jQuery('#lookup-clone-users').html('');
    }
}

function lookUpTasks(lookupDetails, container, callback) {
    callback = callback || false;
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('title');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
        },
        remote: {
            url: getBaseURL() + 'tasks/autocomplete?term=%QUERY',
            'cache': false,
            wildcard: '%QUERY',
        }
    });

    mySuggestion.initialize();
    lookupDetails['lookupField'].typeahead({
        hint: false,
        highlight: true,
        minLength: 2
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                return getTaskSuggestionsDisplayName(item);
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div title="' + (data.title) + '">' + getTaskSuggestionsDisplayName(data) + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            lookupDetails['lookupField'].attr("title", datum.title);
            jQuery("#task-subject", container).removeClass("d-none");
            jQuery("#task-link", container).attr("onclick", 'taskForm(\'' + datum.id + '\')');
            if (typeof lookupDetails.callback !== "undefined") {
                lookupDetails.callback(datum);
            }
            if (callback['callback'] && isFunction(callback['callback'])) {
                callback.callback(datum, container);
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            jQuery('.loader-submit', container).removeClass('loading');
        });
    lookupDetails.lookupField.on('input', function (e) {
        if (lookupDetails['lookupField'].val() == '') {
            lookupDetails['lookupField'].removeAttr("title");
            jQuery("#task-subject", container).addClass("d-none");
        }
    });
    if (!isFunction(callback['onEraseLookup']) || !callback['onEraseLookup']) {
        callback['onEraseLookup'] = false;
    }
    lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container, callback['onEraseLookup']);
}

function getTaskLookUpDisplayName(task) {
    return 'T' + task.taskId + ": " + task.taskSubject;
}

function getTaskSuggestionsDisplayName(task) {
    const maxTitleLength = 50;
    if (task.title.length > maxTitleLength) {
        return task.task_id + ": " + task.title.substring(0, maxTitleLength) + '...';
    }
    return task.task_id + ": " + task.title;
}

// display server side validation
function displayValidationErrors(errors, container, noDialog) {
    noDialog = noDialog || false;
    jQuery('.inline-error', container).removeClass('validation-error');
    jQuery('.input-warning', container).removeClass('input-warning');
    var selector;
    for (i in errors) {
        selector = jQuery("div", container).find("[data-field=" + i + "]").length > 0 ? jQuery("div", container).find("[data-field=" + i + "]") : (jQuery("td", container).find("[data-field=" + i + "]").length > 0 ? jQuery("td", container).find("[data-field=" + i + "]") : false);
        // selector = jQuery("div", container).find("[data-field=" + i + "]").length > 0 ? jQuery("div", container).find("[data-field=" + i + "]") : jQuery("td", container).find("[data-field=" + i + "]");

        if (selector) {
            selector.removeClass('d-none').html(errors[i]).addClass('validation-error');
            jQuery("input[data-field=" + i + "]", container).each(function () {
                if (this.value === '') {
                    jQuery(this).addClass('input-warning');
                }
            });
        } else {
            if (!noDialog) pinesMessageV2({ ty: 'error', m: errors[i] });
        }

    }
    scrollToValidationError(container);
}

function reminderCallBack() {
    return true;
}

function fixGridPaginationList() {
    if (navigator.userAgent.indexOf("Chrome") > -1 || navigator.userAgent.indexOf("Edge") > -1) {
        var style = 'style= "padding: 4px;margin-top: -2px;margin: 0 .4em 0;border-color: #ccc;width: 3.5em;border-radius: 4px;cursor: pointer;overflow: visible;vertical-align: middle;background-color: #ffffff !important;" ';
        jQuery('.k-dropdown').replaceWith("<select " + style + ">" + jQuery(".k-dropdown select").html() + "</select>");
    }
}

function administrationForm(type, id, submitCallBackFunction, module) {
    submitCallBackFunction = submitCallBackFunction || false;
    module = module || false;
    jQuery.ajax({
        url: (module ? getBaseURL(module) : getBaseURL()) + type + (id ? '/edit/' + id : '/add'),
        type: "GET",
        data: { add_edit_form: true },
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var administrationFormId = "#administration-form-container";
                if (jQuery(administrationFormId).length <= 0) {
                    jQuery("<div id='administration-form-container'></div>").appendTo("body");
                    var administrationForm = jQuery(administrationFormId);
                    administrationForm.html(response.html);
                    commonModalDialogEvents(administrationForm);
                    jQuery('.select-picker', administrationForm).selectpicker();
                    jQuery("#form-submit", administrationForm).click(function () {
                        submitCallBackFunction ? submitCallBackFunction() : administrationFormSubmit(id, administrationForm, module);
                    });
                    jQuery(administrationForm).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            submitCallBackFunction ? submitCallBackFunction(type, administrationForm) : administrationFormSubmit(id, administrationForm, module);
                        }
                    });
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function administrationFormSubmit(id, container, module) {
    module = module || false;
    var type = jQuery('#field-type', '#administration-type-container').val();
    jQuery.ajax({
        url: (module ? getBaseURL(module) : getBaseURL()) + type + (id ? '/edit/' + id : '/add'),
        type: "POST",
        data: jQuery("form#administration-form", container).serialize(),
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                updateRecords(id ? 'edit' : 'add', response, module);
            } else {
                jQuery('.inline-error', container).removeClass('validation-error');
                for (i in response.validationErrors) {
                    jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
                    // showMoreFields(jQuery('#custom-field-form'),jQuery('#other-lang-container','#custom-field-form'));
                    showMoreFields(jQuery('#administration-form'), jQuery('.hide-rest-fields', '#administration-form'));
                    if (jQuery("#" + i).val() == "") {
                        jQuery("#" + i).val(jQuery("#name_" + _lang.languageSettings['langName'].substring(0, 2)).val());
                    }
                }
                scrollToValidationError(container);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function updateRecords(mode, element, module) {
    module = module || '';
    if (mode === 'delete') {
        jQuery('#administration-type-record-' + element, '#administration-type-container').remove();
        if (jQuery('#administration-type-table tbody', '#administration-type-container').children().length == 0) {
            jQuery('#administration-type-table', '#administration-type-container').addClass('d-none');
        }
        var totalRecords = jQuery('#rows-number', '#administration-type-container').val() - 1;
        jQuery('#rows-number', '#administration-type-container').val(totalRecords);
        jQuery('#total-records', '#administration-type-container').html(totalRecords);
    } else {
        jQuery('#administration-type-table').removeClass('d-none');
        var tableRow = '<tr id="administration-type-record-' + element.id + '">';
        jQuery.each(element.records, function (item, index) {
            if (jQuery('#lang-' + item, '#administration-type-table').length) {
                tableRow += '<td title="' + element.records[item] + '">' + element.records[item] + '</td>';
            }
        });
        if (element.records_keys) {
            jQuery.each(element.records_keys, function (item, index) {
                if (jQuery('#' + item, '#administration-type-table').length) {
                    tableRow += '<td>' + element.records_keys[item] + '</td>';
                }
            });
        }
        tableRow += '<td><a href="javascript:;" onclick="administrationForm(\'' + element.type + '\',' + element.id + ', false, \'' + module + '\');">' + '<i class="fa fa-edit fa-lg"></i>' + '</a></td><td><a href="javascript:;" onclick="confirmationDialog(\'confirm_delete_record\',{resultHandler: deleteAdministrationRecord, parm: \'' + element.id + '\', module: \'' + module + '\'});">' + '<i class="fa fa-trash fa-lg"></i>' + '</a></td></tr>';
        if (mode === 'add') {
            jQuery('#administration-type-table tbody').append(tableRow);
            var totalRecords = parseInt(jQuery('#rows-number', '#administration-type-container').val(), 10) + 1;
            jQuery('#rows-number', '#administration-type-container').val(totalRecords);
            jQuery('#total-records', '#administration-type-container').html(totalRecords);
        } else {
            jQuery('#administration-type-record-' + element.id).replaceWith(tableRow);
        }
    }
}

function deleteAdministrationRecord(id, module) {
    module = module || false;
    var type = jQuery('#field-type', '#administration-type-container').val();
    jQuery.ajax({
        url: (module ? getBaseURL(module) : getBaseURL()) + type + '/delete',
        type: "POST",
        data: { id: id },
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                updateRecords('delete', id);
                pinesMessage({ ty: 'success', m: response.feedback_message });
            } else {
                pinesMessage({ ty: 'warning', m: response.feedback_message });
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function recordRelatedExpense(caseId, object, objectId, bulk) {
    object = object || 'case';
    objectId = objectId || false;
    bulk = bulk || false;
    jQuery.ajax({
        url: getBaseURL() + 'cases/add_client_and_update_case',
        type: "GET",
        data: { case_id: caseId },
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                window.location = getBaseURL('money') + 'vouchers/' + object + '_expenses_add/' + caseId + (objectId ? ('/' + objectId) : '') + (bulk ? '/true' : '');
            } else {
                if (response.html) {
                    var caseClientFormId = "#case-client-form-container";
                    if (jQuery(caseClientFormId).length <= 0) {
                        jQuery("<div id='case-client-form-container'></div>").appendTo("body");
                        var caseClientForm = jQuery(caseClientFormId);
                        caseClientForm.html(response.html);
                        clientInitialization(caseClientForm);
                        commonModalDialogEvents(caseClientForm);
                        jQuery('#client-type', caseClientForm).selectpicker();
                        jQuery("#form-submit", caseClientForm).click(function () {
                            recordClientExpense(caseClientForm, object, objectId);
                        });
                        jQuery(caseClientForm).find('input').keypress(function (e) {
                            // Enter pressed?
                            if (e.which == 13) {
                                recordClientExpense(caseClientForm, object, objectId);
                            }
                        });
                    }
                } else {
                    pinesMessage({ ty: 'error', m: _lang.invalid_record });
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function recordClientExpense(container, object, objectId) {
    object = object || 'case';
    objectId = objectId || false;
    var formData = jQuery('#case-client-form', container).serialize();
    jQuery.ajax({
        url: getBaseURL() + 'cases/add_client_and_update_case',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                window.location = getBaseURL('money') + 'vouchers/' + object + '_expenses_add/' + response.case_id + (objectId ? ('/' + objectId) : '');
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function copyAddressFromCompany(record, container, isDialog) {
    isDialog = isDialog || false;
    jQuery("#copyAddressFromLookup", container).val('');
    jQuery('#address1', container).focus().val(record.address);
    jQuery('#city', container).val(record.city);
    if (isDialog) {
        jQuery('#country_id', container).val(record.country).selectpicker('refresh');
    } else {
        jQuery('#country_id', container).val(record.country).trigger("chosen:updated");
    }
    jQuery('#state', container).val(record.state);
    jQuery('#zip', container).val(record.zip);
    jQuery('#street_name', container).val(record.street_name);
    jQuery('#additional_street_name', container).val(record.additional_street_name);
    jQuery('#building_number', container).val(record.building_number);
    jQuery('#address_additional_number', container).val(record.address_additional_number);
    jQuery('#district_neighborhood', container).val(record.district_neighborhood);
    setTimeout(function () {
        toggleCopyAddressFrom(container);
    }, 100);
}

function cloneAddress(container, isDialog) {
    isDialog = isDialog || false;
    var lastDivId = jQuery('div.address-details:last', container).attr('id');
    var prefix = 'address-details-container-';
    var lastDivCount = lastDivId.substring(prefix.length, lastDivId.length);
    var newDivCount = parseInt(lastDivCount) + 1;
    var addressDetailsContainer = jQuery('#address-details-container-1', container).clone().attr('id', prefix + newDivCount);
    addressDetailsContainer.insertAfter(jQuery('div.address-details:last', container));
    jQuery('input', addressDetailsContainer).val('');
    jQuery('#adddress-seperator', addressDetailsContainer).removeClass('d-none');
    jQuery('#remove-address', addressDetailsContainer).removeClass('d-none');
    jQuery('#remove-address a', addressDetailsContainer).attr('onclick', '').click(function (e) {
        e.preventDefault();
        addressDetailsContainer.remove();
    });
    if (!isDialog) {
        jQuery('#copyAddressFromContainer', addressDetailsContainer).addClass('d-none');
        jQuery('#country_id_chosen', addressDetailsContainer).remove();
        jQuery(".country-id", addressDetailsContainer).val('').chosen({
            no_results_text: _lang.no_results_matched,
            placeholder_text: _lang.chooseCountry,
            width: '100%'
        });
        jQuery('.copyAddressFromLink', addressDetailsContainer).attr('onclick', '');
        jQuery('.copyAddressFromLink', addressDetailsContainer).click(function (e) {
            e.preventDefault();
            toggleCopyAddressFrom(addressDetailsContainer);
        });
        copyAddressFromLookup(addressDetailsContainer);
    } else {
        addressDetailsContainer.find('.bootstrap-select').parent().append(jQuery('#country_id', addressDetailsContainer).clone());
        addressDetailsContainer.find('.bootstrap-select').remove();
        jQuery('#country_id', addressDetailsContainer).selectpicker();
    }
}

function notifyMeBefore(container) {
    jQuery('#notify-me-before-container', container).removeClass('d-none');
    jQuery('#notify-me-before-link', container).addClass('d-none');
    jQuery('input', jQuery('#notify-me-before-container', container)).removeAttr('disabled');
    jQuery('select', jQuery('#notify-me-before-container', container)).removeAttr('disabled');
    jQuery('#notify-me-before-time-type', jQuery('#notify-me-before-container', container)).removeAttr('disabled').selectpicker('refresh');
    jQuery('#notify-me-before-type', jQuery('#notify-me-before-container', container)).removeAttr('disabled').selectpicker('refresh');
}

function hideRemindMeBefore(container) {
    jQuery('#notify-me-before-container', container).addClass('d-none');
    jQuery('#notify-me-before-link', container).removeClass('d-none');
    jQuery('input', jQuery('#notify-me-before-container', container)).attr('disabled', true);
    jQuery('select', jQuery('#notify-me-before-container', container)).attr('disabled', true);
    jQuery('#notify-me-before-time-type', jQuery('#notify-me-before-container', container)).attr('disabled', true);
    jQuery('#notify-me-before-type', jQuery('#notify-me-before-container', container)).attr('disabled', true);
}

function notifyMeBeforeEvent(inputDetails, container, isDialog) {
    isDialog = isDialog || false;
    if (isDialog) {
        jQuery('#' + inputDetails.inputContainer, container).on('changeDate', function (e) {
            if (jQuery('#' + inputDetails.input, container).val() !== '') {
                if (jQuery('#notify-me-before-container', container).hasClass('d-none')) {
                    jQuery('#notify-me-before-link', container).removeClass('d-none');
                }
            } else {
                hideRemindMeBefore(container);
                jQuery('#notify-me-before-link', container).addClass('d-none');
            }
        });
    }
    jQuery('#' + inputDetails.input, container).on('blur', function () {
        if (this.value !== '') {
            if (jQuery('#notify-me-before-container', container).hasClass('d-none')) {
                jQuery('#notify-me-before-link', container).removeClass('d-none');
            }
        } else {
            hideRemindMeBefore(container);
            jQuery('#notify-me-before-link', container).addClass('d-none');
        }
    });
}

function deleteIPRecord(ipId) {
    jQuery.ajax({
        url: getBaseURL() + 'intellectual_properties/delete/' + ipId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.status) {
                pinesMessage({ ty: 'information', m: response.messsage });
                if (jQuery('#intellectualPropertyGrid').length > 0) {
                    jQuery('#intellectualPropertyGrid').data("kendoGrid").dataSource.read();
                } else {
                    window.location = getBaseURL() + 'intellectual_properties/';
                }
            } else {
                pinesMessage({ ty: 'error', m: response.messsage ? response.messsage : _lang.recordNotDeleted });
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function displayColHeaderPlaceholder() {
    jQuery("th[col-header-placeholder]").each(function () {
        jQuery(this).attr('title', jQuery(this).attr('col-header-placeholder'));
    });
}

function showToolTip() {
    jQuery('.tooltip-title').tooltipster();
}

function lookUpClients(lookupDetails, container) {
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('name');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
        },
        remote: {
            url: getBaseURL('money') + 'clients/autocomplete?term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });

    mySuggestion.initialize();
    lookupDetails['lookupField'].typeahead({
        hint: false,
        highlight: true,
        minLength: 3
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                return item.name
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + data.name + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            if (typeof lookupDetails.callBackFunction !== "undefined") {
                lookupDetails.callBackFunction(container);
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
            jQuery('.loader-submit', container).removeClass('loading');
        });
    lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container);
}

function clientStatusesEvents(container) {
    jQuery('.widget-data', container).each(function (index, element) {
        jQuery(element).tooltipster({
            content: jQuery('.popover-content', element).html(),
            contentAsHTML: true,
            timer: 22800,
            animation: 'grow',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
            maxWidth: 350,
            interactive: true,
            repositionOnScroll: true,
            position: 'bottom'

        });
    });
}

function clientTransactionsBalance(data, container, controller, onPageLoad) {
    onPageLoad = onPageLoad || false;//ajax is sent on page load
    if (!data.client || !jQuery('#system-client-currency', container).val()) {//if client or currency are not set=> then show a help message
        jQuery('.widget-data', container).addClass('disable');
        if (data.client && !jQuery('#system-client-currency', container).val()) {
            jQuery('.widget-data', container).tooltipster('content', _lang.feedback_messages.caseCurrencyNotSet.sprintf(['system_preferences']));
        }
    } else {
        if (!onPageLoad) {
            jQuery('.widget-data', container).removeClass('disable').tooltipster('destroy');
            jQuery('.help-sign', container).addClass('d-none');
            jQuery('span.warning-msg', jQuery('.popover-content', container)).addClass('d-none');
            jQuery('span.help-msg', jQuery('.popover-content', container)).removeClass('d-none');
            clientStatusesEvents(jQuery('#client-account-status'));
        }
        var postData = { case_id: data.case, client_id: data.client };
        if (typeof data.organization !== 'undefined') {
            postData['organization'] = data.organization;
        }
        jQuery.ajax({
            url: getBaseURL() + controller + '/load_client_widgets/',
            dataType: 'JSON',
            data: postData,
            type: 'GET',
            success: function (response) {
                if (response.account_transactions) {  //paid and balance due amount for the client
                    if (!jQuery('.amount', jQuery('#paid-container', container)).length) {
                        jQuery('.details', jQuery('#paid-container', container)).append('<span class="amount"></span>');
                    }
                    if (!jQuery('.amount', jQuery('#balance-due-container', container)).length) {
                        jQuery('.details', jQuery('#balance-due-container', container)).append('<span class="amount"></span>');
                    }
                    jQuery('.amount', jQuery('#paid-container', container)).html(response.account_transactions.paid_balance);
                    jQuery('.amount', jQuery('#balance-due-container', container)).html(response.account_transactions.due_balance);
                }
                if (response.amount) {  //client trust account balance
                    if (!jQuery('.amount', jQuery('#trust-container', container)).length) {
                        jQuery('.details', jQuery('#trust-container', container)).append('<span class="amount"></span>');
                    }
                    jQuery('.amount', jQuery('#trust-container', container)).html(response.amount);
                }
                if (response.billable) { //billable logs and expenses that will be generated to an invoice
                    var logs = response.billable.logs;
                    if (!jQuery('.amount', jQuery('#billable-container', container)).length) {
                        jQuery('.details', jQuery('#billable-container', container)).append('<span class="amount"></span>');
                    }
                    jQuery('#details-table', jQuery('#billable-container', container)).removeClass('d-none');
                    if (typeof logs.organizations !== 'undefined' && logs.organizations && logs.organization_id) {
                        var message = '';
                        activeOrganizations = logs.organizations;
                        if (Object.keys(logs.organizations).length > 1) {
                            message = logs.organizations[logs.organization_id] + ', <a href="javascript:;" onclick="moneyEntities( ' + logs.organization_id + ');">' + _lang.clickToChange + '</a>';

                        } else {
                            message = logs.organizations[logs.organization_id];
                        }
                        jQuery('span', jQuery('#billable-container', container)).html(_lang.popoverClientBillableStatus.sprintf([message]));
                    }
                    jQuery('.amount', jQuery('#billable-container', container)).html(response.billable.total);
                    jQuery('#expenses-amount', jQuery('#billable-container', container)).html(response.billable.expenses);
                    jQuery('#logs-amount', jQuery('#billable-container', container)).html(logs.amount);
                    jQuery('#billable-container', container).tooltipster('content', jQuery('.popover-content', jQuery('#billable-container', container)).html());
                }
                if (response.capping) {
                    var capping = response.capping;
                    if (!jQuery('.capping-amount', jQuery('#capping-container', container)).length) {
                        jQuery('.details', jQuery('#capping-container', container)).append('<span class="capping-amount"></span>');
                    }
                    jQuery('#details-table', jQuery('#capping-container', container)).removeClass('d-none');
                    jQuery('.capping-amount', jQuery('#capping-container', container)).html(response.capping.capping_amount);
                    jQuery('#expenses-amount', jQuery('#capping-container', container)).html(response.capping.cap_expenses_amount);
                    jQuery('#logs-amount', jQuery('#capping-container', container)).html(response.capping.cap_time_logs_amount);
                    jQuery('#remaining-cap-amount', jQuery('#capping-container', container)).html(response.capping.remaining_cap_amount);
                    jQuery('#capping-container', container).tooltipster('content', jQuery('.popover-content', jQuery('#capping-container', container)).html());
                } else {
                    var widgetColumns = jQuery('.widget-columns', jQuery('#client-account-status'));
                    var capAmountContainer = jQuery('#cap-amount-container', jQuery('#client-account-status'));
                    capAmountContainer.addClass('d-none');
                    widgetColumns.each(function (index, value) {
                        jQuery(this).removeClass('col-sm-5ths col-md-5ths');
                        jQuery(this).addClass('col-sm-3 col-md-3');
                    });
                }
            }, complete: function () {
                jQuery('.loader-submit').remove();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function clientCaseForm(caseId) {
    jQuery.ajax({
        url: getBaseURL() + 'cases/add_client_and_update_case',
        type: "GET",
        data: { case_id: caseId },
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                jQuery('.widget-data', '#client-account-status').tooltipster('hide');
                var caseClientFormId = "#case-client-form-container";
                if (jQuery(caseClientFormId).length <= 0) {
                    jQuery("<div id='case-client-form-container'></div>").appendTo("body");
                    var caseClientForm = jQuery(caseClientFormId);
                    caseClientForm.html(response.html);
                    clientInitialization(caseClientForm);
                    jQuery('#client-type', caseClientForm).selectpicker();
                    commonModalDialogEvents(caseClientForm, clientCaseFormSubmit);
                }
            } else {
                pinesMessage({ ty: 'error', m: _lang.invalid_record });
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function clientCaseFormSubmit(container) {
    var formData = jQuery('#case-client-form', container).serialize();
    jQuery.ajax({
        url: getBaseURL() + 'cases/add_client_and_update_case',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                if (jQuery('#newCaseFormDialog').length || jQuery('#ipEditForm').length) {
                    var caseContainer = jQuery('#newCaseFormDialog').length ? jQuery('#newCaseFormDialog') : jQuery('#ipEditForm');
                    var clientType = jQuery('#client-type', container).val() === 'contact' ? 'contacts' : 'companies';
                    jQuery('#clientType', caseContainer).val(clientType);
                    jQuery('#contact_company_id', caseContainer).val(jQuery('#contact-company-id', container).val());
                    jQuery('#clientLookup', caseContainer).val(jQuery('#client-lookup', container).val());
                    clientTransactionsBalance({
                        case: jQuery('#id', caseContainer).val(),
                        client: response.client_id
                    }, jQuery('#client-account-status'), jQuery('#controller', '#object-header').val());
                }
                jQuery(".modal", container).modal("hide");
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function moneyEntities(selectedOrg) {
    jQuery('.widget-data', '#client-account-status').tooltipster('hide');
    var select = '<select class="select-picker" id="organizations-list">';
    var selected;
    jQuery.each(activeOrganizations, function (item, index) {
        if (item == selectedOrg) {
            selected = 'selected = "selected" ';
        } else {
            selected = '';
        }
        select += '<option value="' + item + '" ' + selected + ' >' + activeOrganizations[item] + '</option>';
    });
    select += '</select>';
    var moneyEntitiesForm = "#money-entities-form-container";
    if (jQuery(moneyEntitiesForm).length <= 0) {
        jQuery("<div id='money-entities-form-container' class='primary-style'><div class='modal fade modal-container modal-resizable'><div class='modal-dialog'><div class='modal-content'>\n\
                <div class='modal-header'><h4 id='title' class='modal-title'>" + _lang.chooseEntity + "</h4><button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button></div>\n\
                <div class='modal-body form-horizontal overflow-unset'><div class='form-group row col-md-12 col-xs-12 padding-10'><label class='control-label col-md-4 col-xs-5'>" + _lang.relatedEntity + "</label><div class='col-md-6'>" + select + "</div></div></div>\n\
                <div class='modal-footer'><span class='loader-submit'></span><div class='btn-group'><button id='save-btn' type='button' class='btn btn-save btn-add-dropdown modal-save-btn'>" + _lang.ok + "</button></div><button type='button' class='btn btn-link' data-dismiss='modal'></button></div></div>\n\
                </div></div></div></div>").appendTo("body");
        jQuery('.select-picker', moneyEntitiesForm).selectpicker();
        initializeModalSize(moneyEntitiesForm, 0.4, 0.2);
        jQuery(".modal", moneyEntitiesForm).modal({
            keyboard: false,
            backdrop: "static",
            show: true
        });
        jQuery(document).keyup(function (e) {
            if (e.keyCode == 27) {
                jQuery(".modal", moneyEntitiesForm).modal("hide");
            }
        });
        jQuery('.modal', moneyEntitiesForm).on('hidden.bs.modal', function () {
            destroyModal(jQuery(moneyEntitiesForm));
        });
        jQuery("#save-btn", moneyEntitiesForm).click(function () {
            clientTransactionsBalance({
                case: jQuery('#case-id', '#object-header').val(),
                client: jQuery('#case-client-id', '#object-header').val(),
                organization: jQuery('#organizations-list', moneyEntitiesForm).find(":selected").val()
            }, jQuery('#client-account-status'), jQuery('#controller', '#object-header').val());
            jQuery(".modal", moneyEntitiesForm).modal("hide");
        });
    }
}

function disableAutocomplete(container) {
    jQuery(container).find('input').not('.lookup').not('[type=hidden]').each(function () {
        jQuery(this).removeAttr("autocomplete");
        if (jQuery(this).attr('data-rand-autocomplete')) {
            jQuery(this).attr("autocomplete", "stop-" + jQuery(this).attr('name') + "-" + parseInt(Math.random() * (1000 - 1) + 1));
        } else {
            jQuery(this).attr("autocomplete", "off");
        }
        jQuery(this).closest("form").attr("autocomplete", "off");
    });
}

function applyExportingModuleMethod(el) {
    jQuery('#exporting-module').modal('show');
    jQuery('#export-module-btn').attr('onClick', jQuery(el).data('callexport'));
}

function loadExportModalRanges(totalRowsNumber, fromRange, toRange) {
    var ulElements = "";
    var from = fromRange || 1;
    var to = toRange || 10000;
    var numberOfPages = Math.ceil(totalRowsNumber / to);
    for (var i = 1; i <= numberOfPages; i++) {
        if (i === 1) {
            ulElements += "<li><input type='radio' name='num-of-records' value='" + i + "' style='height: 10px;' checked><span style='padding-left: 5px;'>" + number_format(from, 0, '.', ',') + " <b>-</b> " + number_format(to, 0, '.', ',') + "</span></li><br>";
        } else if (i === numberOfPages) {
            ulElements += "<li><input type='radio' name='num-of-records' value='" + i + "' style='height: 10px;'><span style='padding-left: 5px;'>" + number_format(from, 0, '.', ',') + " <b>-</b> " + number_format(totalRowsNumber, 0, '.', ',') + "</span></li><br>";
        } else {
            ulElements += "<li><input type='radio' name='num-of-records' value='" + i + "' style='height: 10px;'><span style='padding-left: 5px;'>" + number_format(from, 0, '.', ',') + " <b>-</b> " + number_format(to, 0, '.', ',') + "</span></li><br>";
        }
        from += to;
        to += to;
    }
    jQuery('#exporting-module-body ul').html(ulElements);
}

function archiveUnarchiveCase(id, archived) {
    confirmationDialog(archived == 'no' ? 'confirmation_message_to_archive_case' : 'confirmation_message_to_unarchive_case', {
        resultHandler: archiveUnarchiveCaseSubmission,
        parm: id
    });
}

function archiveUnarchiveCaseSubmission(id) {
    jQuery.ajax({
        url: getBaseURL() + 'cases/archive_unarchive_cases',
        type: 'POST',
        dataType: 'JSON',
        data: { 'case_id': id },
        success: function (response) {
            pinesMessageV2(response.status ? {
                ty: 'information',
                m: _lang.feedback_messages.caseArchivedSuccessfully
            } : { ty: 'error', m: _lang.feedback_messages.caseArchiveFailed });
            if (jQuery('#id', '#newCaseFormDialog').length) {
                //in case edit form
                jQuery('#archive-unarchive-btn', '#newCaseFormDialog').attr("onclick", "archiveUnarchiveCase('" + id + "', '" + response.archived + "')").text(response.archived == "yes" ? _lang.unarchive : _lang.archive);
            } else {
                $legalCaseGrid.data("kendoGrid").dataSource.read();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function disabledUnArchiveBtn() {
    jQuery('#unarchivedButtonId').addClass('disabled');
    var temp_title = jQuery('#archive_tooltip').attr('data-title');
    jQuery('#archive_tooltip').attr('title', temp_title);
}

function disabledArchiveBtn() {
    jQuery('#archivedButtonId').addClass('disabled');
    var temp_title = jQuery('#archive_tooltip_litigation').attr('data-title');
    var temp_title2 = jQuery('#archive_tooltip_matter').attr('data-title');
    jQuery('#archive_tooltip_litigation').attr('title', temp_title);
    jQuery('#archive_tooltip_matter').attr('title', temp_title2);
}

function fetchCaseRelatedDataToHearingFrom(data, container) {
    jQuery.ajax({
        url: getBaseURL() + 'cases/hearings',
        type: 'POST',
        dataType: "json",
        data: { caseId: data.id, action: 'fetchRelatedCaseData' },
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response) {
                if (response.hearingLawyers) {
                    jQuery.each(response.hearingLawyers, function (key, value) {
                        setNewBoxElement('#selected-assignees', 'assignees-container', '#' + container.attr('id'), {
                            id: key,
                            value: value,
                            name: 'Hearing_Lawyers'
                        });
                        lookupBoxContainerDesign(jQuery('.' + 'assignees-container', container));
                    });

                }
                if (response.judgmentValue) {
                    jQuery('#hearing-judgment-value', container).val(response.judgmentValue);
                }
            }
        }
    });
    matterStageMetadata(container, data.id);
}

function litigationStageDataEvents(html, container) {
    jQuery('#stage-div', container).html(html);
    jQuery('#remove-related-stage', container).removeClass('d-none');
    if (container.attr('id') === 'case-event-form-container') {//change the css of the stage container
        jQuery('#case-stage-container', container).removeClass('col-md-12 no-padding');
        jQuery('label', jQuery('#case-stage-container', container)).removeClass('col-md-3').addClass('col-md-3 col-xs-7');
    }
    jQuery('#relate-to-case-stage-link', container).on('click', function () {
        showLitigationStages(container);
    });
    jQuery('#remove-related-stage', container).on('click', function () {
        removeStage(container);
    });
    jQuery('.select-picker', '#stage-div').selectpicker();
    jQuery('#show-stage-details', container).tooltipster({
        content: jQuery('#stage-card-div', container).html(),
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true,
        repositionOnScroll: true,
        position: 'bottom'

    });
    jQuery('form', container).data('serialize', jQuery('form', container).serialize());
}

function showLitigationStages(container) {
    var caseId = jQuery('input[data-field="case_id"]', container).val();
    jQuery.ajax({
        url: getBaseURL() + 'cases/return_litigation_stages',
        type: 'POST',
        dataType: "json",
        data: { caseId: caseId },
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.html) {
                var litigationStages = "#litigation-stages-form-container";
                if (jQuery(litigationStages).length <= 0) {
                    jQuery("<div id='litigation-stages-form-container'></div>").appendTo("body");
                    var litigationStagesContainer = jQuery(litigationStages);
                    litigationStagesContainer.html(response.html);
                    commonModalDialogEvents(litigationStagesContainer);
                    jQuery("#form-submit", litigationStagesContainer).click(function () {
                        relateLitigationStage(litigationStagesContainer, container);
                    });
                    jQuery(litigationStagesContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            relateLitigationStage(litigationStagesContainer, container);
                        }
                    });
                    initializeModalSize(litigationStagesContainer);
                    if (jQuery('#stage-id', container).val() !== '') {
                        jQuery('input[type=radio]', jQuery('#stage-container-' + jQuery('#stage-id').val(), litigationStagesContainer)).prop("checked", true);
                    }
                }
            }
        }
    });
}

function changeAttendStatus(id) {
    var attend_status = jQuery("#attend_status_" + id).data("type");
    if (attend_status == "mandatory") {
        jQuery("#attend_status_" + id).data("type", "optional");
        jQuery("#attend_status_" + id + " img").attr("src", "assets/images/icons/mark_mandatory.png");
        jQuery("#input_attend_status_" + id).val("1");
        runTooltipMeeting("#attend_status_" + id + " img", _lang.mark_optional);
    } else {
        jQuery("#attend_status_" + id).data("type", "mandatory");
        jQuery("#attend_status_" + id + " img").attr("src", "assets/images/icons/mark_optional.png");
        jQuery("#input_attend_status_" + id).val("0");
        runTooltipMeeting("#attend_status_" + id + " img", _lang.mark_required);
    }
}

function changeParticipantStatus(id) {
    var participantStatus = jQuery("#input_participant_" + id).val()
    if (participantStatus == 0) {
        jQuery("#input_participant_" + id).val("1");
        jQuery("#participant_status_" + id).removeClass('d-none');
        jQuery("#participant_status_" + id + "  .tooltipTable").tooltipster('content', _lang.mark_non_participant);
    } else {
        jQuery("#input_participant_" + id).val("0");
        jQuery("#participant_status_" + id + "  .tooltipTable").tooltipster('content', _lang.mark_participant);
        jQuery("#participant_status_" + id).addClass('d-none');
    }
}

function runTooltipMeeting(id, content) {
    if (!jQuery(id).hasClass("tooltipstered")) {
        jQuery(id).tooltipster({
            content: content,
            contentAsHTML: true,
            animation: 'grow',
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
        });
    } else {
        jQuery(id).tooltipster('content', content);
    }
}

function showParticipant(id) {
    var participantStatus = jQuery("#input_participant_" + id).val();
    if (participantStatus == 0) {
        jQuery("#participant_status_" + id).removeClass('d-none');
    }
}

function checkParticipant(id) {
    var participantStatus = jQuery("#input_participant_" + id).val();
    if (participantStatus == 0) {
        jQuery("#participant_status_" + id).addClass('d-none');
    } else {
        jQuery("#participant_status_" + id).removeClass('d-none');
    }
}

function matterStageMetadata(container, caseId, stageId) {
    stageId = stageId || false;
    if (caseId) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/matter_stage_metadata/' + caseId + (stageId ? '/' + stageId : ''),
            success: function (response) {
                if (response.html) {
                    litigationStageDataEvents(response.html, container);
                } else {
                    jQuery('#stage-div', container).html('');
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        jQuery('#stage-div').html('');
    }
}

function relateLitigationStage(stagesContainer, mainContainer) {
    var stage = jQuery('input[type=radio]:checked', stagesContainer).attr('data-id');
    jQuery('#stage-id', mainContainer).val(stage);
    var caseId = jQuery('input[data-field="case_id"]', mainContainer).val();
    if (caseId !== '') {
        matterStageMetadata(mainContainer, caseId, stage);
        jQuery(".modal", stagesContainer).modal("hide");
    }
}

function removeStage(container) {
    jQuery('#show-stage-details', container).addClass('d-none');
    jQuery('#stage-name-label', container).addClass('d-none');
    jQuery('#show-stage-details', container).addClass('d-none');
    jQuery('#remove-related-stage', container).addClass('d-none');
    jQuery('#relate-to-case-stage-link', container).removeClass('d-none');
    jQuery('#stage-id', container).val('');
}

function caseContainerForm(id, name) {
    id = id || false;
    name = name || false;
    jQuery.ajax({
        url: getBaseURL() + 'case_containers/' + (!id ? 'add' : ('edit/' + id)),
        type: 'GET',
        dataType: "json",
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.html) {
                var caseContainerId = "#case-container-form-div";
                if (jQuery(caseContainerId).length <= 0) {
                    jQuery("<div id='case-container-form-div'></div>").appendTo("body");
                    var caseContainerDiv = jQuery(caseContainerId);
                    caseContainerDiv.html(response.html);
                    if (name) {
                        jQuery('#subject', caseContainerDiv).val(name);
                        jQuery('#legal-case-id', caseContainerDiv).val(jQuery('#legal-case-id', '#containerForm').val());
                    }
                    caseContainerFormEvents(caseContainerDiv);
                    opponentsInitialization(jQuery('#opponents-container', caseContainerDiv));
                }
            } else if (typeof response.error !== 'undefined' && response.error) {
                pinesMessageV2({ ty: 'error', m: response.error });
            }
        }
    });
}

//load case container form events
function caseContainerFormEvents(container) {
    jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
    setDatePicker('#arrival-date-add-new', container);
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#arrival-date-add-new', container), jQuery('#arrival-date-container', container));
    }
    setDatePicker('#closedOn-add-new', container);
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#closedOn-add-new', container), jQuery('#closedOn-container', container));
    }
    initializeModalSize(container);
    clientInitialization(container);
    var lookupDetails = {
        'lookupField': jQuery('#lookup-requested-by', container),
        'errorDiv': 'requestedBy',
        'hiddenId': '#requested-by',
        'resultHandler': setRequestedByToForm
    };
    lookUpContacts(lookupDetails, container);
    jQuery('#user-id', container).change(function () {
        if (jQuery('#user-id', container).val() == 'quick_add') {
            jQuery('#user-id', container).val('').selectpicker('refresh');
            addUserToTheProviderGroup(jQuery('#provider-group-id', container).val(), 'user-id', true);
        }
    });
    jQuery('#provider-group-id', container).change(function () {
        reloadUsersListByProviderGroupSelected(jQuery('#provider-group-id', container).val(), jQuery("#user-id", container), true);
    });
    jQuery("#case-submit", container).click(function () {
        caseContainerFormSubmit(container);
    });
    jQuery('.modal', container).modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.date-picker', container).bootstrapDP("remove");
            jQuery('.modal', container).modal('hide');
        }
    });
    jQuery(container).find('input').keypress(function (e) {
        // Enter pressed?
        if (e.which == 13) {
            caseContainerFormSubmit(container);
        }
    });
    jQuery('.modal-body', container).on("scroll", function () {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(container);
        companyContactFormMatrix.commonLookup = {};//empty the commonLookup array
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#subject', container).focus();
    });
}

function caseContainerFormSubmit(container) {
    var id = jQuery('#id', container).val() ? jQuery('#id', container).val() : false;
    var formData = jQuery('#case-container-form', container).serializeArray();
    if (jQuery('#legal-case-id', container).val()) {
        formData.push({ name: "action", value: "addRelatedCaseContainer" });
    }
    jQuery.ajax({
        url: getBaseURL() + 'case_containers/' + (!id ? 'add' : ('edit/' + id)),
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (id) {
                    pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                } else {
                    pinesMessage({
                        ty: 'success',
                        m: _lang.feedback_messages.addedNewContainerSuccessfully.sprintf([getBaseURL() + 'case_containers/edit/' + response.id, response.ID])
                    });
                }
                if (!id && typeof caseContainerCallBack === "function") {
                    // load container grid in matter tab
                    caseContainerCallBack();
                } else if (jQuery('#container-display-fields', '#caseContainerContainer').length > 0) {
                    // load container fields after saving the edit form
                    loadContainerFields();
                } else if (jQuery('#containerGrid').length > 0) {
                    // load container grid
                    jQuery('#containerGrid').data("kendoGrid").dataSource.read();
                }
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('containers');
                    pieCharts();
                }
                jQuery(".modal", container).modal("hide");
            } else {
                if (response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                } else if (typeof response.error !== 'undefined' && response.error) {
                    pinesMessageV2({ ty: 'error', m: response.error });
                }
            }
        }
    });
}

function fixDateTimeFieldDesign(container) {
    jQuery('.date-picker', container).each(function () {
        jQuery('.time-picker', this).remove();
        jQuery('.time', this).remove();
    });
    jQuery('.time-container', container).each(function () {
        jQuery('.date', this).remove();
    });
}

function dialogObjectAttachFile(object, container) { //attach file for objects that has the dialog form
    attachmentCount++;
    jQuery('#' + object + '-attachments', container).append(
        '<div class="col-md-11 d-flex">' +
        '<div class="col-md-9 p-0">' +
        '<input id="' + object + '-attachment-' + attachmentCount + '" name="' + object + '_attachment_' + attachmentCount + '" type="file" class="margin-top"/>' +
        '</div>' +
        '<div class="col-md-2 p-0">' +
        '<button class="remove-record-icon" type="button" onclick="jQuery(this).parent().parent().remove();">×</button>' +
        '<input name="' + object + '_attachments[]" type="hidden" value="' + object + '_attachment_' + attachmentCount + '"/>' +
        '</div>' +
        '</div>'
    );
    jQuery('#' + object + '-attachments input:last').focus();
}

function loadCustomFieldsEvents(prefixId, container) {
    jQuery("[id^=" + prefixId + "]", container).each(function () {
        var id = jQuery(this).attr('id');
        var field = id.substring(prefixId.length, id.length);
        switch (jQuery(this).attr('field-type')) {
            case 'date':
                setDatePicker(jQuery(this).parent(), container, datePickerOptionsBottom);
                break;
            case 'date_time':
                if (jQuery(this).parent().hasClass('date-picker')) {
                    setDatePicker(jQuery(this).parent(), container, datePickerOptionsBottom);
                }
                if (jQuery(this).hasClass('time')) {
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
                var fieldTypeData = jQuery(this).attr('field-type-data');
                jQuery(this).selectize({
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
                            } else if (typeof displayFormatDoubleSegment !== 'undefined') {
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
                            } else if (typeof displayFormatDoubleSegment !== 'undefined') {
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
                        if (!query.length)
                            return callback();
                        jQuery.ajax({
                            url: getBaseURL() + jQuery('#' + this.$input[0].id).attr('field-type-data') + "/autocomplete" + (fieldTypeData == 'users' ? '/active' : ''),
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
            case 'single_lookup':
                var thisContainer = this;
                var fieldTypeData = jQuery(thisContainer).attr('field-type-data');
                var mySuggestion = new Bloodhound({
                    datumTokenizer: function (datum) {
                        return Bloodhound.tokenizers.whitespace('');
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: getBaseURL() + jQuery(this).attr('field-type-data') + '?term=%QUERY',
                        filter: function (data) {
                            return data;
                        },
                        replace: function (url, uriEncodedQuery) {
                            return url.replace('%QUERY', uriEncodedQuery);
                        },
                        'cache': false,
                        wildcard: '%QUERY',
                    }
                });
                mySuggestion.initialize();
                jQuery(this).typeahead({
                    hint: false,
                    highlight: true,
                    minLength: 2
                },
                    {
                        source: mySuggestion.ttAdapter(),
                        display: function (item) {
                            return getDisplayOptions(item, thisContainer);
                        },
                        templates: {
                            empty: [
                                '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                            suggestion: function (item) {
                                return '<div>' + getDisplayOptions(item, thisContainer) + '</div>'
                            }
                        }
                    }).on('typeahead:asyncrequest', function () {
                        jQuery('.loader-submit', container).addClass('loading');
                    }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
                        jQuery('.loader-submit', container).removeClass('loading');
                    });
                lookupCommonFunctions(jQuery(thisContainer), jQuery('#hidden-field-' + field, container), field, container);
                break;
            case 'lookup_per_type':
                var thisContainer = this;
                var lookupType = jQuery('#' + field + '-company-contact-type', jQuery(thisContainer).parent()).val()
                var lookupDetails = {
                    'lookupField': jQuery(this, container),
                    'hiddenInput': jQuery('#hidden-field-' + field, container),
                    'errorDiv': field
                };
                jQuery('.select-picker', jQuery(thisContainer).parent()).selectpicker().change(function () {
                    jQuery('#hidden-field-' + field, container).val('');
                    jQuery(thisContainer, container).val('');
                    jQuery("div", container).find("[data-field=" + field + "]").addClass('d-none');
                    jQuery(thisContainer, container).typeahead('destroy');
                    lookupCompanyContactType(lookupDetails, container, jQuery(this).val());
                });
                lookupCompanyContactType(lookupDetails, container, lookupType);
                break;
            case 'multiple_lookup_per_type':
                multipleFieldsInitialization(jQuery('#fields-with-multiple-values', container));
                break;
            case 'multiple_lookup':
                lookupPrivateUsers(jQuery(this, container), 'multiple_records[' + field + ']', '#selected-' + field, field + '-container', container);
                checkBoxContainersValues({ 'container': jQuery('#selected-' + field, container) }, container);
                break;
            default:
                break;
        }
    });
}
// Convert Gregorian date to Hijri date
function gregorianToHijri(gregorianDate, dateTime) {
    dateTime = dateTime || false;
    if (gregorianDate && dateTime) {
        dateParts = gregorianDate.split(" - ");
        m = moment(dateParts[0]);
        return m.locale('en').format('YYYY-MM-DD') !== 'Invalid date' ? (m.format('iYYYY-iMM-iDD') + ' - ' + dateParts[1]) : '';
    } else {
        m = moment(gregorianDate);
        return m.locale('en').format('YYYY-MM-DD') !== 'Invalid date' ? m.format('iYYYY-iMM-iDD') : '';
    }
}

function screenTransitionForm(id, transitionId, object) {
    id = id || false;
    transitionId = transitionId || false;
    if (!id || !transitionId) {
        pinesMessage({ ty: 'error', m: _lang.invalid_record });
        return false;
    }
    jQuery.ajax({
        url: getBaseURL() + object + '/transition_screen_fields/' + id + '/' + transitionId,
        type: 'GET',
        dataType: "json",
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (!response.html) {
                    jQuery(".form-submit-loader").show();
                    pinesMessage({ ty: 'success', m: response.display_message });
                    window.location = window.location.href;
                } else {
                    screenTransitionFormEvents(id, transitionId, response.html, object, false, false, getBaseURL() + object + '/transition_screen_fields/');
                }
            } else if (typeof response.display_message !== 'undefined') {
                pinesMessage({ ty: 'error', m: response.display_message });
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
    });

}

function screenTransitionFormEvents(id, transitionId, html, object, callback, notRefreshPage, url) {
    callback = callback || false;
    notRefreshPage = notRefreshPage || false;
    var screenFieldId = "#screen-fields-form-container";
    if (jQuery(screenFieldId).length <= 0) {
        jQuery("<div id='screen-fields-form-container'></div>").appendTo("body");
        var screenFieldsContainer = jQuery(screenFieldId);
        screenFieldsContainer.html(html);
        if (typeof caseBoards !== "undefined") {
            commonModalDialogEvents(screenFieldsContainer, false, false, function () { caseBoards.quickFilters(); });
        } else {
            commonModalDialogEvents(screenFieldsContainer, false, false);
        }
        jQuery("#form-submit", screenFieldsContainer).click(function () {
            screenTransitionFormSubmit(id, transitionId, screenFieldsContainer, object, callback, notRefreshPage, url);
        });
        jQuery(screenFieldsContainer).find('input').keypress(function (e) {
            // Enter pressed?
            if (e.which == 13) {
                screenTransitionFormSubmit(id, transitionId, screenFieldsContainer, object, callback, notRefreshPage, url);
            }
        });
        initializeModalSize(screenFieldsContainer);
        fixDateTimeFieldDesign(screenFieldsContainer);
        loadCustomFieldsEvents('screen-field-', screenFieldsContainer);
        loadCustomFieldsEvents('custom-field-', screenFieldsContainer);
        if (jQuery('#screen-field-provider_group_id', screenFieldsContainer).length > 0 && jQuery('#screen-field-user_id', screenFieldsContainer).length > 0) {
            jQuery('#screen-field-provider_group_id', screenFieldsContainer).change(function () {
                reloadUsersListByAssignedTeam(jQuery('#screen-field-provider_group_id', screenFieldsContainer).val(), jQuery("#screen-field-user_id", screenFieldsContainer), true);
            });
        }
    }
}

function screenTransitionFormSubmit(id, transitionId, container, object, callback, notRefreshPage, url) {
    id = id || false;
    transitionId = transitionId || false;
    callback = callback || false;
    notRefreshPage = notRefreshPage || false;
    if (!id || !transitionId) {
        pinesMessage({ ty: 'error', m: _lang.invalid_record });
        return false;
    }
    var formData = jQuery('#transition-screen-fields-form', container).serializeArray();
    jQuery.ajax({
        url: url + id + '/' + transitionId,
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".form-submit-loader").show();
                jQuery(".modal", container).modal("hide");
                pinesMessage({ ty: 'success', m: response.display_message });
                if (!notRefreshPage) window.location = window.location.href;
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ ty: 'error', m: response.display_message });
                }
                if (typeof response.validation_errors !== 'undefined') {
                    displayValidationErrors(response.validation_errors, container);
                }
                if (callback && isFunction(callback)) callback();
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
    });
}

function getDisplayOptions(item, container) {
    var displaySegments = jQuery(container).attr('display-segments').split(',');
    var displayFormatSingleSegment = jQuery(container).attr('display-format-single-segment');
    var displayFormatDoubleSegment = jQuery(container).attr('display-format-double-segment');
    var displayFormatTripleSegment = jQuery(container).attr('display-format-triple-segment');
    var displayOption = '';
    if (typeof displayFormatTripleSegment !== 'undefined') {
        if (item[displaySegments[0]] && item[displaySegments[1]] && item[displaySegments[2]]) {
            displayOption = displayFormatTripleSegment.sprintf([item[displaySegments[0]], item[displaySegments[1]], item[displaySegments[2]]]);
        } else if (item[displaySegments[0]] && item[displaySegments[displaySegments.length - 1]]) {
            displayOption = item[displaySegments[0]] + ' ' + item[displaySegments[displaySegments.length - 1]];
        } else {
            displayOption = item[displaySegments[0]];
        }
    } else if (typeof displayFormatDoubleSegment !== 'undefined') {
        if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined') {
            displayOption = displayFormatDoubleSegment.sprintf([item[displaySegments[0]], item[displaySegments[1]]]);
        } else {
            displayOption = item[displaySegments[0]];
        }
    } else {
        displayOption = displayFormatSingleSegment.sprintf([item[displaySegments[0]]]);
    }
    return displayOption;
}

function reloadUsersListByAssignedTeam(pGId, userListFieldId) {
    jQuery.ajax({
        url: getBaseURL() + 'users/autocomplete/active',
        dataType: 'JSON',
        data: {
            join: ['provider_groups_users'],
            more_filters: { 'provider_group_id': pGId },
            term: ''
        },
        success: function (results) {
            var newOptions = '<option value="">' + _lang.chooseUsers + '</option>';
            if (typeof results != "undefined" && results != null && results.length > 0) {
                for (i in results) {
                    newOptions += '<option value="' + results[i].id + '">' + results[i].firstName + ' ' + results[i].lastName + '</option>';
                }
            }
            userListFieldId.html(newOptions).selectpicker("refresh");

        }, error: defaultAjaxJSONErrorsHandler
    });
}


function multipleFieldsInitialization(container) {
    var fieldsCount = jQuery('#fields-count', container).val();
    var count = 1;
    jQuery('.field-div', container).each(function () {
        if (count > fieldsCount) {
            return;
        }
        multipleFieldsEvents(jQuery(this));
        count++;
    });
}

function multipleFieldsEvents(container) {
    var parentId = jQuery(container).parent().attr('id');
    var suffixId = '-fields-container';
    var field = parentId.substring(suffixId.length, parentId.length);
    var lookupDetails = {
        'lookupField': jQuery('.field-lookup', container),
        'hiddenInput': jQuery('.field-member-id', container),
        'errorDiv': field
    };
    jQuery('.field-member-type', container).selectpicker().change(function () {
        jQuery(".field-lookup", container).val('');
        jQuery(".field-member-id", container).val('');
        jQuery(".inline-error", container).html('');
        jQuery('.field-lookup', container).typeahead('destroy');
        lookupCompanyContactType(lookupDetails, container, jQuery(this).val());
    });
    jQuery('.field-lookup', container).typeahead('destroy');
    lookupCompanyContactType(lookupDetails, container, jQuery('.field-member-type option:selected', container).val());
}

//clone field container
function multipleFieldsAddContainer(container, event, maxNumber = 0) {
    if (container == '#opponent-fields-container') {
        if (jQuery('.field-div', container).length == maxNumber) {
            pinesMessage({ ty: 'information', m: _lang.caseMaxOpponentsInfo.sprintf([maxNumber]), d: 10000 });
            return;
        }
    }
    var fieldsContainer = jQuery(container, '#fields-with-multiple-values');
    var fieldsNumber = parseInt(jQuery('#fields-count', fieldsContainer).val());
    jQuery('.field-member-type', jQuery('.field-div', fieldsContainer).last()).selectpicker('destroy');//Before cloning ,destroy selectpicker in the container to clone from
    jQuery('#screen-field-opponent-position', jQuery('.field-div', fieldsContainer).last()).selectpicker('destroy');//Before cloning ,destroy selectpicker in the container to clone from
    var clonedHtml = jQuery('.field-div', fieldsContainer).last().clone();
    var newId = fieldsNumber + 1;
    var clonedContainer = '#field-' + (newId);
    var clonedDiv = clonedHtml.insertAfter(jQuery('.field-div', fieldsContainer).last());
    clonedDiv.attr("id", 'field-' + (newId));
    jQuery('.field-member-type', jQuery('#field-' + fieldsNumber, fieldsContainer)).selectpicker();//after cloning re initialize the selectpicker of the picker that was destroyed
    jQuery('#fields-count', fieldsContainer).val(newId);
    jQuery('.delete-field', jQuery(clonedContainer, fieldsContainer)).attr("onclick", 'multipleFieldsDelete(' + newId + ',\'' + container + '\' , event)');
    jQuery('.count-number', jQuery(clonedContainer, fieldsContainer)).html(' (' + (newId) + ')');
    jQuery('.field-member-id', jQuery(clonedContainer, fieldsContainer)).val('');
    jQuery('.field-lookup', jQuery(clonedContainer, fieldsContainer)).val('');

    jQuery('#screen-field-opponent-position', jQuery(clonedContainer, fieldsContainer)).attr('data-field-id', 'opponent-position-' + newId);
    jQuery('#screen-field-opponent-position', jQuery(clonedContainer, fieldsContainer)).find('option').removeAttr('selected');
    jQuery('#screen-field-opponent-position', jQuery('.field-div', fieldsContainer)).selectpicker();//after cloning re initialize the selectpicker of the picker that was destroyed

    jQuery('.field-member-type', jQuery(clonedContainer, fieldsContainer)).find('option').removeAttr('selected');
    jQuery('.delete-icon', jQuery(clonedContainer, fieldsContainer)).removeClass('d-none');
    if (fieldsNumber == 1) {
        jQuery('.delete-icon', jQuery('#field-' + (fieldsNumber), fieldsContainer)).removeClass('d-none');
    }
    multipleFieldsEvents(jQuery(clonedContainer, fieldsContainer));

    event.preventDefault();
}

//update field labels and ids
function multipleFieldsUpdateLabels(container) {
    var fieldsContainer = jQuery(container, '#fields-with-multiple-values');
    var fieldsNumber = jQuery('#fields-count', fieldsContainer).val();
    var count = 1;
    jQuery('.count-number', fieldsContainer).each(function () {
        if (count <= fieldsNumber) {
            jQuery(this).html(' (' + count + ')');
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.field-div', fieldsContainer).each(function () {
        if (count <= fieldsNumber) {
            jQuery(this).attr("id", 'field-' + count);
            count++;
        } else {
            return true;
        }
    });
    count = 1;
    jQuery('.delete-field', fieldsContainer).each(function () {
        if (count <= fieldsNumber) {
            jQuery(this).attr("onclick", 'multipleFieldsDelete(' + count + ',\'' + container + '\' , event)');
            count++;
        } else {
            return true;
        }
    });
    multipleFieldsInitialization(container);
}

//delete field
function multipleFieldsDelete(fieldId, container, event) {
    var fieldsNumber = jQuery('#fields-count', container).val();
    if (fieldsNumber > 1) {
        jQuery('#field-' + fieldId, container).remove();
        jQuery('#fields-count', container).val(fieldsNumber - 1);
        multipleFieldsUpdateLabels(container);
        if (jQuery('#fields-count', container).val() == 1) {
            jQuery('.delete-icon', container).addClass('d-none');
        }
    } else {
        pinesMessage({ ty: 'warning', m: _lang.invalid_request });
    }
    event.preventDefault();
}

/**
 *  TimerForm function
 * @param {int} id
 * @param {string} action
 * @param {JSON} selectedMatter
 * @param {JSON} selectedTask
 */
function TimerForm(id, action, selectedMatter, selectedTask) {
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var randomNumber = Math.round(Math.random() * (upperBound - lowerBound) + lowerBound);
    id = id || false;
    action = action || false;
    if (id && action) {
        var data = { action: action, id: id, html: true };
        if (action != 'pauseform') {
            var endtimer = true;
        }
    } else {
        var data = { action: action };
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'timers/' + (id ? 'edit/' + id : 'add'),
        data: data,
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var timerDialogId = "#timer-dialog-" + randomNumber;
                jQuery('<div id="timer-dialog-' + randomNumber + '"></div>').appendTo("body");
                var container = jQuery(timerDialogId);
                jQuery(timerDialogId).html(response.html);
                if (!id) {
                    selectTimeLogRelation(jQuery("input:radio[name=timer-type]:checked", timerDialogId));
                }
                if (endtimer) {
                    jQuery('#action', timerDialogId).val("end");
                }
                emptyInputTimer("#task , #legal-caselookup", jQuery("#add-edit-timer"));
                if (!selectedMatter && !selectedTask) {
                    var url = window.location.href;
                    var matterSearchEdit = url.search("cases/edit");
                    var matterSearchTasks = url.search("cases/tasks");
                    var matterSearchReminders = url.search("cases/reminders");
                    var matterSearchDocuments = url.search("cases/documents");
                    var matterSearchExpenses = url.search("cases/expenses");
                    var matterSearchTimeLogs = url.search("cases/time_logs");
                    var matterSearchRelatedCases = url.search("cases/related");
                    var matterSearchRelatedContracts = url.search("cases/related_contracts");
                    var matterSearchSettings = url.search("cases/settings");
                    var taskSearch = url.search("tasks/view");
                    if (matterSearchEdit > 0 || matterSearchTasks > 0 || matterSearchReminders > 0 || matterSearchDocuments > 0 || matterSearchExpenses > 0
                        || matterSearchTimeLogs > 0 || matterSearchRelatedCases > 0 || matterSearchRelatedContracts > 0 || matterSearchSettings > 0) {
                        var matterContainerTimer = jQuery(".matter");
                        var matterIdTimer = jQuery(".matter-code", matterContainerTimer).attr('data-id');
                        var matterCodeTimer = jQuery(".matter-code", matterContainerTimer).text();
                        var matterSubjectTimer = jQuery(".matter-subject", matterContainerTimer).text().trim();
                        jQuery("#legal-caselookup", container).val(matterCodeTimer + ": " + matterSubjectTimer);
                        jQuery("#legal-caselookup-id", container).val(matterIdTimer);
                    }
                    if (taskSearch > 0) {
                        var taskContainerTimer = jQuery(".task");
                        var taskIdTimer = jQuery(".task-id", taskContainerTimer).val();
                        var taskSubjectTimer = jQuery(".task-subject", taskContainerTimer).text();
                        jQuery("input:radio[name=timer-type][value=task]").attr('checked', 'checked');
                        selectTimeLogRelation(jQuery("input:radio[name=timer-type][value=task]", timerDialogId));
                        jQuery("#task", container).val(taskSubjectTimer);
                        jQuery("#task-id", container).val(taskIdTimer);
                    }
                }
                if (selectedMatter) {
                    if (checkIfStringIsURIEncoded(selectedMatter.subject)) {
                        selectedMatter.subject = decodeURIComponent(selectedMatter.subject);
                    }
                    jQuery("#legal-caselookup", container).val(replaceHtmlCharacter(selectedMatter.subject));
                    jQuery("#legal-caselookup-id", container).val(selectedMatter.legal_case_id);
                }
                if (selectedTask) {
                    if (checkIfStringIsURIEncoded(selectedTask.subject)) {
                        selectedTask.subject = decodeURIComponent(selectedTask.subject);
                    }
                    jQuery("input:radio[name=timer-type][value=task]").attr('checked', 'checked');
                    selectTimeLogRelation(jQuery("input:radio[name=timer-type][value=task]", timerDialogId));
                    jQuery("#task", container).val(getTaskLookUpDisplayName(selectedTask));
                    jQuery("#task-id", container).val(selectedTask.taskId);
                }
                if (jQuery('#task', timerDialogId).length) {
                    jQuery('#task', timerDialogId).val(replaceHtmlCharacter(jQuery('#task', timerDialogId).val()));
                }
                var callback = { 'callback': onTimerCaseLookup };
                jQuery('#legal-caselookup', timerDialogId).val(replaceHtmlCharacter(jQuery('#legal-caselookup', timerDialogId).val()));
                lookUpCases(jQuery('#legal-caselookup', timerDialogId), jQuery('#legal-caselookup-id', timerDialogId), 'legal_case_id', timerDialogId, false, callback);
                lookUpTasks({
                    'lookupField': jQuery('#task', timerDialogId),
                    'hiddenId': jQuery('#task-id', timerDialogId),
                    'errorDiv': 'task_id',
                    'callback': onTimerTaskSelection
                }, timerDialogId);
                clientLookup({
                    "lookupField": jQuery('#client-name', '#add-edit-timer'),
                    "hiddenField": jQuery("#client-id", "#add-edit-timer")
                });
                commonModalDialogEvents(container);
                initializeModalSize(container);
                jQuery("#add-timer-dialog-submit").on('click', function () {
                    TimerFormSubmit(action, timerDialogId);
                });
                jQuery("#add-timer-dialog-cancel").on('click', function () {
                    TimerFormCancel(action, timerDialogId);
                });
                jQuery("#timer-time-type", container).change(function () {
                    jQuery.ajax({
                        url: getBaseURL().concat('time_types/get_record_by_id/').concat(jQuery(this).val()),
                        data: data,
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            jQuery('#loader-global').show();
                        },
                        success: function (response) {
                            if (response.status && response.data.default_comment) {
                                if (jQuery("#summary", container).val()) {
                                    confirmationDialog('replace_comment', {
                                        resultHandler: function () {
                                            jQuery("#summary", container).val(response.data.default_comment);
                                        }
                                    });
                                } else {
                                    jQuery("#summary", container).val(response.data.default_comment);
                                }
                            }
                        }, complete: function () {
                            jQuery('#loader-global').hide();
                        },
                        error: defaultAjaxJSONErrorsHandler
                    });
                });
                if (action != 'add') {
                    timerList();
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/**
 * TimerFormSubmit function
 * @param {string} type
 * @param {int} id
 * @param {boolean} fromTimer
 * @param {data} data
 */
function TimerFormSubmit(type, container, fromTimer, data) {
    var formData;
    if (fromTimer) {
        formData = data;
    } else {
        const modifiedFormData = jQuery("form", jQuery(container)).serializeArray().map(function (obj) {
            if (obj.name == 'legalCase' || obj.name == 'task') {
                obj.value = escapeHtml(obj.value);
            }
            return obj;
        });
        formData = jQuery.param(modifiedFormData);
    }
    if (!container) {
        container = jQuery('#add-edit-timer');
    }
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'timers/' + (type == 'add' ? 'add' : 'edit'),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.status) {
                timerList();
                jQuery('.modal', container).modal('hide');
                if (response.endtimer) {
                    pinesMessage({ ty: 'success', m: response.message });
                }
                if (response.warning) {
                    pinesMessage({ ty: 'error', m: response.warning });
                }
                if (response.message && response.cancel_time) {
                    pinesMessage({ ty: 'success', m: response.message });
                }
            } else {
                if (response.validationErrors) {
                    if (response.validationErrors.length == 0) {
                        pinesMessage({ ty: 'error', m: _lang.timer_can_not_start });
                    } else {
                        displayValidationErrors(response.validationErrors, container);
                    }
                }
                if (response.display_message) {
                    jQuery.each(response.display_message, function (key, value) {
                        pinesMessage({ ty: 'error', m: value });
                    });
                }
                if (response.message) {
                    pinesMessage({ ty: 'error', m: response.message });
                }
                if (response.total_validation) {
                    confirmationDialog('validation_less_one_min', {
                        resultHandler: function () {
                            jQuery("#cancel-time-log").val(false);
                            jQuery("#less-one-min").val(true);
                            TimerFormSubmit(type, container);
                        }, onCloseHandler: function () {
                            jQuery("#cancel-time-log").val(false);
                            jQuery("#less-one-min").val(false);
                        }
                    });
                }
            }
        }, complete: function () {
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });

}

/**
 * list timers function
 */
function timerList() {
    jQuery('.popover').popover('hide');
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + "timers/index",
        type: 'POST',
        data: { action: "list_timers" },
        success: function (response) {
            if (response.html) {
                jQuery('.dropdown-menu-timer').html(response.html);
                var timerDropdown = jQuery('.dropdown-menu-timer').height();
                if (timerDropdown > 430) {
                    jQuery('.dropdown-menu-timer').css("overflow-y", "scroll");
                }
                jQuery('.stopwatch').each(function () {
                    var timerId = jQuery(this).attr('id').replace("timer-", "");
                    timerActions(timerId, response.serverTime);
                });
                if (response.timer_active_status) {
                    jQuery('.timer-up .header-timer-icon').addClass('timer-active-icon');
                } else {
                    jQuery('.timer-up .header-timer-icon').removeClass('timer-active-icon');
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/**
 * timerActions start - pause
 * @param {int} timerID
 */
function timerActions(timerID, serverTime) {
    // Cache very important elements, especially the ones used always
    var element = jQuery("#timer-" + timerID);
    var running = element.data('autostart');
    var hoursElement = element.find('.hours');
    var minutesElement = element.find('.minutes');
    var secondsElement = element.find('.seconds');
    var millisecondsElement = element.find('.milliseconds');
    var toggleElement = element;
    var toggleElementIcon = jQuery(".start-text");
    var pauseText = toggleElement.data('pausetext');
    var resumeText = toggleElement.data('resumetext');
    var startTime = toggleElement.data('starttime');
    var status = toggleElement.data('status');
    // And it's better to keep the state of time in variables
    // than parsing them from the html.
    var hours, minutes, seconds, milliseconds, timer;

    function prependZero(time, length) {
        // Quick way to turn number to string is to prepend it with a string
        // Also, a quick way to turn floats to integers is to complement with 0
        time = '' + (time | 0);
        // And strings have length too. Prepend 0 until right.
        while (time.length < length)
            time = '0' + time;
        return time;
    }

    function setStopwatch(hours, minutes, seconds, milliseconds) {

        // Using text(). html() will construct HTML when it finds one, overhead.
        hoursElement.text(prependZero(hours, 2));
        minutesElement.text(prependZero(minutes, 2));
        secondsElement.text(prependZero(seconds, 2));
        millisecondsElement.text(prependZero(milliseconds, 3));
    }

    // Update time in stopwatch periodically - every 25ms
    function runTimer() {
        // Using ES5 Date.now() to get current timestamp
        var prevHours = hours;
        var prevMinutes = minutes;
        var prevSeconds = seconds;
        var prevMilliseconds = milliseconds;
        setInterval(serverTimeUpdate, 25);

        function serverTimeUpdate() {
            serverTime = (serverTime + 25);
            var timeElapsed = serverTime - (startTime * 1000);
            hours = (timeElapsed / 3600000) + prevHours;
            minutes = ((timeElapsed / 60000) + prevMinutes) % 60;
            seconds = ((timeElapsed / 1000) + prevSeconds) % 60;
            milliseconds = (timeElapsed + prevMilliseconds) % 1000;

            setStopwatch(hours, minutes, seconds, milliseconds);
        }
    }

    // Split out timer functions into functions.
    // Easier to read and write down responsibilities
    function run() {
        if (status == 'paused') {
            timerSwitch('run', timerID);
        } else {
            running = true;
            runTimer();
        }
    }

    function pause() {
        running = false;
        clearTimeout(timer);
        toggleElementIcon.html(resumeText);
        pause = true;
        TimerForm(timerID, 'pauseform');
    }

    function reset() {
        running = false;
        hours = minutes = seconds = milliseconds = 0;
        setStopwatch(hours, minutes, seconds, milliseconds);
    }

    // And button handlers merely call out the responsibilities
    toggleElement.on('click', function (event) {
        event.preventDefault();
        (running) ? pause() : run();
    });
    if (running) {
        reset();
        run();
    }
}

/**
 *
 * @param {*} action
 * @param {*} timerID
 */
function timerSwitch(action, timerID) {
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + "timers/switch_timer",
        type: 'POST',
        data: { action: action, id: timerID },
        success: function (response) {
            if (response.status) {
                timerList();
                jQuery('.dropdown-menu-timer').animate({
                    scrollTop: jQuery(".navbar-header").offset().top
                }, "slow");
            }
        }
    });
}

/**
 * emptyInput function
 * @param {string} id
 * @param {DOM} container
 */
function emptyInputTimer(id, container) {
    jQuery(id, container)
        .focusout(function () {
            if (jQuery(this).val() == "") {
                jQuery("#" + jQuery(this).attr('id') + "-id").val("");
                if (jQuery(this).attr('id') == "task") {
                    jQuery("#task-description").text("");
                } else {
                    jQuery("#legal-case-subject").text("");
                }
                jQuery("[data-field='task_or_matter']", container).addClass('d-none');

            }
        })
}

function taskStatusForm(id, workflowId) {
    id = id || false;
    workflowId = workflowId || false;
    jQuery.ajax({
        url: getBaseURL() + 'task_statuses/' + (id ? 'edit/' + id : 'add'),
        type: "GET",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var taskStatusId = "#task-status-container";
                if (jQuery(taskStatusId).length <= 0) {
                    jQuery("<div id='task-status-container'></div>").appendTo("body");
                    var taskStatusContainer = jQuery(taskStatusId);
                    taskStatusContainer.html(response.html);
                    commonModalDialogEvents(taskStatusContainer);
                    jQuery("#form-submit", taskStatusContainer).click(function () {
                        taskStatusFormSubmit(id, workflowId, taskStatusContainer);
                    });
                    jQuery(taskStatusContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            taskStatusFormSubmit(id, workflowId, taskStatusContainer);
                        }
                    });
                    resizeMiniModal(taskStatusContainer);
                    jQuery(window).bind('resize', (function () {
                        resizeMiniModal(taskStatusContainer);
                    }));
                    showToolTip();
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function taskStatusFormSubmit(id, workflowId, container) {
    id = id || false;
    workflowId = workflowId || false;
    var formData = jQuery('#task-status-form', container).serializeArray();
    if (workflowId) {
        formData.push({ name: "workflow_id", value: workflowId });
    }
    jQuery.ajax({
        url: getBaseURL() + 'task_statuses/' + (id ? 'edit/' + id : 'add'),
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                window.location = getBaseURL() + 'task_workflows/index/' + workflowId;

            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
    });
}

/**
 * hearingAttachFile
 */
function hearingAttachFile() {
    hearingAttachmentCount++;
    jQuery('#hearing-attachments').append(
        '<div class="row no-margin col-md-9 no-padding">' +
        '<div class="col-md-10 no-padding">' +
        '<input id="hearing-attachment-' + hearingAttachmentCount + '" name="attachment_' + hearingAttachmentCount + '" type="file" class="margin-top"/>' +
        '</div>' +
        '<button class="remove-record-icon margin-top" type="button" onclick="jQuery(this).parent().remove();">×</button>' +
        '<input name="hearing_attachments[]" type="hidden" value="hearing_attachment_' + hearingAttachmentCount + '"/>' +
        '</div>'
    );
    jQuery('#hearing-attachments input:last').focus();
}

/**
 * delete document hearing function
 * @param {*} id
 * @param {*} document_id
 * @param {*} versionDocument
 */
function deleteHearingDocument(id, document_id, versionDocument) {
    versionDocument = versionDocument || false;
    moduleController = 'cases';
    if (confirm(_lang.confirmationDeleteFile)) {
        _deleteHearingDocument(id, document_id, versionDocument, moduleController);
    }
}

function _deleteHearingDocument(id, document_id, versionDocument, moduleController, hearingId) {
    hearingId = hearingId || false;
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/delete_document_hearing',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'document_id': document_id,
            'type': 'hearing',
            'hearing_id': id
        },
        success: function (response) {
            pinesMessage({ ty: response.status ? 'information' : 'error', m: response.message });
            if (response.status) {
                jQuery('#document-item-' + id).remove();
                if (hearingId) updateDocCount(hearingId);
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function _deleteCommentDocument(id, document_id, comment_id, moduleController) {
    comment_id = comment_id || false;
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/delete_document_comment',
        type: 'POST',
        dataType: 'JSON',
        data: {
            document_id: document_id,
            id: id,
            newest_version: true
        },
        success: function (response) {
            pinesMessage({ ty: response.status ? 'information' : 'error', m: response.message });
            if (response.status) {
                jQuery('#document-item-' + id).remove();
                if (comment_id) updateDocCount(comment_id);
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function updateDocCount(recordId) {
    var documentItemCount = jQuery('#document-item-count-' + recordId);
    if (documentItemCount) {
        var docCount = documentItemCount.data('count');
        if (docCount > 1) {
            documentItemCount.html('<a href="javascript:;">' + (docCount - 1) + " " + _lang.documents + '</a>');
            documentItemCount.data('count', docCount - 1);
        } else {
            documentItemCount.attr('onclick', '');
            documentItemCount.html(0 + " " + _lang.documents);
            jQuery('.modal', jQuery("#document-dialog-container")).modal('hide');
        }
    }
}
/**
 * targetDialogOpen function
 */
function targetDialogOpen() {
    // target element that we will observe
    var target = document.body;
    // config object
    var config = { attributes: true, childList: true, subtree: true };

    // subscriber function
    function subscriber(mutations) {
        mutations.forEach(function (item, index) {
            if (item.type == "childList") {
                if (jQuery(item.addedNodes).hasClass("ui-dialog")) {
                    disableAutocomplete(item.addedNodes);
                }
            }
        });
    }

    // instantiating observer
    var observer = new MutationObserver(subscriber);

    // observing target
    observer.observe(target, config);
}

function initializeSelectPermissions(container) {
    jQuery('.users-selectized', container).selectize({
        plugins: ['remove_button'],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: availableUsers,
        createOnBlur: true,
        groups: [
            { value: 'user', label: 'Users' },
            { value: 'user_group', label: 'User Groups' }
        ],
        optgroupField: 'class',
    });
    jQuery('.user-groups-selectized', container).selectize({
        plugins: ['remove_button'],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: availableUserGroups,
        createOnBlur: true,
        groups: [
            { value: 'user', label: 'Users' },
            { value: 'user_group', label: 'User Groups' }
        ],
        optgroupField: 'class',
    });
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

function redirectToTab(url, tab) {
    sessionStorage.setItem("tab", tab);
    window.location = url;
}

function hearingJudgedEvent(checkbox, container) {
    var isChecked = jQuery(checkbox).is(':checked');
    jQuery(checkbox, container).val(isChecked ? 'yes' : '');
    if (isChecked) {
        jQuery('#judged-container').removeClass('d-none');
        jQuery('input[name=judgment_date]', container).val(jQuery('input[name=startDate]', container).val());
    } else {
        jQuery('#judged-container').addClass('d-none');
        jQuery('input[name=judgment_date]', container).val('');
    }

}

function hearingSetJudgment($id, $caseId, callback) {
    $id = $id || 0;
    $caseId = $caseId || 0;
    callback = callback || false;
    if (!$id || !$caseId) {
        pinesMessage({ ty: 'error', m: _lang.invalid_record });
    } else {
        jQuery.ajax({
            url: getBaseURL() + 'cases/hearings/' + $caseId,
            type: 'POST',
            dataType: 'JSON',
            data: { hearingId: $id, action: 'hearingSetJudgment' },
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.html) {
                    var hearingDialogId = '#hearing-judgment-form-container';
                    jQuery('<div id="hearing-judgment-form-container"></div>').appendTo("body");
                    var hearingDialog = jQuery(hearingDialogId);
                    hearingDialog.html(response.html);
                    commonModalDialogEvents(hearingDialog);
                    setDatePicker('#sentence-date', hearingDialog);
                    makeFieldsHijriDatePicker({ fields: ['sentence-date-hijri'] });
                    jQuery('#sentence-date-hijri').val(gregorianToHijri(jQuery('#sentence-date-gregorian', '#sentence-date-hijri-container').val()));
                    jQuery('.select-picker', hearingDialog).selectpicker();
                    initializeModalSize(hearingDialog);
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(hearingDialog);
                    });
                    jQuery("#form-submit", hearingDialog).click(function () {
                        hearingSubmitJudgment(hearingDialog, $caseId, callback);
                    });
                    jQuery(hearingDialog).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            hearingSubmitJudgment(hearingDialog, $caseId, callback);
                        }
                    });
                } else if (response.error) {
                    pinesMessage({ ty: 'error', m: response.error });
                }
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function hearingSubmitJudgment(container, caseId, callback) {
    callback = callback || false;
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    formData.append('action', 'hearingSubmitJudgment');
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        type: 'POST',
        url: getBaseURL() + 'cases/hearings/' + caseId,
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery('#hearingsGrid').length) {
                    jQuery('#hearingsGrid').data("kendoGrid").dataSource.read();
                }
                jQuery('#hearing-judged-label').html(response.judged_label);
                jQuery('#hearing-judged-icon').attr("src", "assets/images/icons/judged-yes.svg");
                jQuery(".modal", container).modal("hide");
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                if (callback && isFunction(callback)) callback();
            } else if (typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                displayValidationErrors(response.validationErrors, container);
            } else if (response.error) {
                pinesMessage({ ty: 'error', m: response.error });
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function quickAddStageStatus(formContainer, isDialog) {
    isDialog = isDialog || false;
    jQuery.ajax({
        url: getBaseURL() + 'stage_statuses/add/',
        dataType: 'JSON',
        type: 'GET',
        data: {
            add_edit_form: true,
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery("#stage-status-form-container").length <= 0) {
                    jQuery("<div id='stage-status-form-container'></div>").appendTo("body");
                    var stageStatusDialogContainer = jQuery('#stage-status-form-container');
                    stageStatusDialogContainer.html(response.html);
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery('.modal', stageStatusDialogContainer).modal('hide');
                        }
                    });
                    jQuery('.modal', stageStatusDialogContainer).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'

                    });
                    resizeMiniModal(stageStatusDialogContainer);
                    jQuery(window).bind('resize', (function () {
                        resizeMiniModal(stageStatusDialogContainer);
                    }));
                    jQuery("#form-submit", stageStatusDialogContainer).click(function () {
                        var formData = jQuery("form#stage-status-form", stageStatusDialogContainer).serializeArray();
                        jQuery.ajax({
                            beforeSend: function () {
                                jQuery("#loader-global").show();
                            },
                            data: formData,
                            dataType: 'JSON',
                            type: 'POST',
                            url: getBaseURL() + 'stage_statuses/add',
                            success: function (response) {
                                jQuery('.inline-error', stageStatusDialogContainer).addClass('d-none');
                                if (response.result) {
                                    jQuery(".modal", stageStatusDialogContainer).modal("hide");
                                    updateStageStatusLanguageRecords(stageStatusDialogContainer, response);
                                } else {
                                    displayValidationErrors(response.validationErrors, stageStatusDialogContainer);
                                }
                            }, complete: function () {
                                jQuery("#loader-global").hide();
                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                    });
                    jQuery("input", stageStatusDialogContainer).keypress(function (e) {
                        if (e.which == 13) {
                            e.preventDefault();
                            jQuery("#form-submit", stageStatusDialogContainer).click();
                        }
                    });
                    jQuery('.modal-body', stageStatusDialogContainer).on("scroll", function () {
                        jQuery('.bootstrap-select.open').removeClass('open');
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(stageStatusDialogContainer);
                    });
                    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
                        jQuery('#field-name', stageStatusDialogContainer).focus();

                    });
                    ctrlS(function () {
                        jQuery("#form-submit", stageStatusDialogContainer).click();
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function updateStageStatusLanguageRecords(container, response) {
    jQuery('.modal', container).modal('hide');
    var administrationComponent = "administration-stage_statuses";
    var administrationComponentType = jQuery('body').find("[data-field=" + administrationComponent + "]");
    administrationComponentType.append(jQuery("<option/>", {
        value: response.id,
        text: response.name
    }));
    administrationComponentType.val(response.id);
    administrationComponentType.selectpicker('refresh');
}

function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);

    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0)
            return null;
    } else {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);

        if (end == -1) {
            end = dc.length;
        }
    }
    return decodeURI(dc.substring(begin + prefix.length, end));
}

function updateHiddenFields(that, hiddenInput) {
    jQuery(hiddenInput).val(jQuery(that).is(':checked') ? 1 : 0);
}

function assignmentPerType(type, category, container, casetypeChanged) {
    if (!type) {
        return false;
    }
    jQuery.ajax({
        url: getBaseURL() + 'assignments/return_rules',
        type: 'GET',
        dataType: 'JSON',
        data: {
            'type': type,
            'category': category,
        },
        success: function (response) {
            if (category === 'task') {
                if (response.data) {
                    jQuery('#user-relation', container).val(response.data.user.id);
                    jQuery('#assignment-id', container).val(response.data.id);
                    if (response.data.visible_assignee == 1) {
                        jQuery('.assignee-container', container).removeClass('d-none');
                    } else {
                        jQuery('.assignee-container', container).addClass('d-none');
                    }
                    jQuery('#assignedToId', container).val(response.data.user.id);
                    jQuery('#assignedToLookUp', container).val(response.data.user.name);
                } else {
                    jQuery('.assignee-container', container).removeClass('d-none');
                    jQuery('#assignedToId', container).val('');
                    jQuery('#assignedToLookUp', container).val('');
                    destroyUserLookup({
                        hidden_id: jQuery('#assignedToId', '#task-form'),
                        lookup_field: jQuery('#assignedToLookUp', '#task-form'),
                        lookup_container: jQuery('.assignee-container', '#task-form'),
                        container: jQuery('#task-form')
                    });
                    jQuery('#assignment-id', container).val('');
                    jQuery('#user-relation', container).val('');
                }
            } else {
                if (response.data) {
                    jQuery('#user-relation', container).val(response.data.user_id);
                    jQuery('#assignment-relation', container).val(response.data.assignment_relation);
                    jQuery('#related-assigned-team', container).val(response.data.assigned_team);
                    jQuery('#assignment-id', container).val(response.data.id);
                    if (response.data.visible_assigned_team == 1) {
                        jQuery('#assigned-team-container', container).removeClass('d-none');
                    } else {
                        jQuery('#assigned-team-container', container).addClass('d-none');
                    }
                    if (response.data.visible_assignee == 1) {
                        jQuery('#assignee-container', container).removeClass('d-none');
                    } else {
                        jQuery('#assignee-container', container).addClass('d-none');
                    }
                    if (response.data.assigned_team && casetypeChanged) {
                        jQuery('#provider-group-id', container).val(response.data.assigned_team).selectpicker('refresh');
                        reloadUsersListByProviderGroupSelected(jQuery('#provider-group-id', container).val(), jQuery("#user-id", container), true);
                        jQuery('#user-id', container).val(response.data.user_id).selectpicker('refresh');
                    } else if (response.data.assigned_team) {
                        if (jQuery('#provider-group-id', container).val() == response.data.assigned_team) {
                            jQuery('#user-id', container).val(response.data.user_id).selectpicker('refresh');
                        }
                    }
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function validateHumanReadableTime(field) {
    var val = field.val();
    val = val.toLowerCase();
    val = val.replace(/\s/g, '');
    var pattern = /^([0-9]+h?|[0-9]+h[0-9]m|[0-9]+h[0-5][0-9]m|^[0-9]m|[0-5][0-9]m|^[+-]?((\d+(\.\d*)?)|(\.\d+))h?$)$/i;
    if (!pattern.test(val)) {
        return _lang.invalidFormat;
    }
}

/**
 * detect IE
 * returns version of IE or false, if browser is not Internet Explorer
 */
function detectIE() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        // IE 11 => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
        // Edge (IE 12+) => return version number
        return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    // other browser
    return false;
}

function initializeSelectNotifications(container) {
    jQuery('.select-picker', container).selectpicker({
        dropupAuto: false
    });
    jQuery('#notify-to', container).selectize({
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
                return { email: input };
            }
            alert('Invalid email address.');
            return false;
        }
    });
    jQuery('#notify-cc', container).selectize({
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
                return { email: input };
            }
            alert('Invalid email address.');
            return false;
        }
    });
}


function collapseExpandRows(container) {
    jQuery("i.first-grouping", container).click(function () {
        $this = jQuery(this);
        $targetParent = $this.parent().parent();
        $target = $targetParent.find("table.first-grouping");
        if ($target.is(':visible')) {
            $target.hide();
            $this.removeClass('fa-solid fa-angle-down');
            $this.addClass('fa-solid fa-angle-right');
        } else {
            $target.show();
            $this.removeClass('fa-solid fa-angle-right');
            $this.addClass('fa-solid fa-angle-down');
        }
    });
}

/**
 * Hide input value field when operator is empty or not empty
 * @param that
 * @param groupContainer
 */
function onchangeOperatorsFiltersToEmpty(that, groupContainer) {
    var container = jQuery('#' + groupContainer);
    var operator = jQuery(that).val();
    var inputValueField = jQuery(".empty-value-field", container);
    if (operator === 'empty') {
        inputValueField.hide();
        inputValueField.val('null');
    } else if (operator === 'not_empty') {
        inputValueField.hide();
        inputValueField.val('null');
    } else {
        inputValueField.show();
        inputValueField.val('');
    }
}

function showMatterInCustomerPortal(matterId, event) {
    preventFormPropagation(event);
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/show_hide_matter_in_customer_portal/' + matterId,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var showCustomerDialogExit = document.getElementById("show-customer-dialog");
                if (showCustomerDialogExit) {
                    showCustomerDialogExit.remove();
                }
                jQuery('<div id="show-customer-dialog"></div>').appendTo("body");
                var showCustomerDialog = jQuery('#show-customer-dialog');
                showCustomerDialog.html(response.html);
                jQuery(".select-picker", showCustomerDialog).selectpicker();
                initializeModalSize(showCustomerDialog, 0.25, 'auto');
                commonModalDialogEvents(showCustomerDialog, function () {
                });
            } else {
                if (!response.result) {
                    pinesMessageV2({
                        ty: response.info ? 'information' : 'error',
                        m: response.error ? response.error : response.info,
                        d: response.error ? 3000 : 10000
                    });
                } else {
                    pinesMessageV2({
                        ty: 'success',
                        m: _lang.hideInCustomerPortalSuccess.sprintf(response.category === 'Litigation' ? [_lang.litigation.toLowerCase()] : [_lang.matter.toLowerCase()])
                    });
                    if (jQuery('#legalCaseGrid').length) {
                        // grid
                        jQuery('#legalCaseGrid').data('kendoGrid').dataSource.read();

                    } else {

                        // edit form
                        var legalCaseTopHeaderProfile = jQuery("#legal-case-top-header-profile", '#legal-case-top-header-container');
                        var cpIcon = jQuery("#cp-icon", '#legal-case-top-header-container');
                        cpIcon.removeClass(response.visible ? 'client-portal-grey' : 'client-portal-blue').addClass(response.visible ? 'client-portal-blue' : 'client-portal-grey');
                        legalCaseTopHeaderProfile.removeClass(response.visible ? 'label-normal-style' : 'light_green').addClass(response.visible ? 'light_green' : 'label-normal-style');
                        jQuery('#visibleToCP', jQuery('#legalCaseAddForm')).val(response.visible ? 1 : 0);
                        jQuery('#show-hide-btn', '#edit-legal-case-container').text(response.visible ? _lang.hideInCustomerPortal : _lang.showInCustomerPortal);
                        legalCaseTopHeaderProfile.tooltipster("destroy");
                        legalCaseTopHeaderProfile.attr("title", response.visible ? _lang.matterVisibleFromCP : _lang.hiddenInCP).tooltipster({
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
                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function recommendMatterClosure(matterId, event) {
    preventFormPropagation(event);
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/recommend_case_closure/' + matterId,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var showCustomerDialogExit = document.getElementById("show-case-closure-dialog");
                if (showCustomerDialogExit) {
                    showCustomerDialogExit.remove();
                }
                jQuery('<div id="show-case-closure-dialog"></div>').appendTo("body");
                var showCustomerDialog = jQuery('#show-case-closure-dialog');
                showCustomerDialog.html(response.html);
                jQuery(".select-picker", showCustomerDialog).selectpicker();
                initializeModalSize(showCustomerDialog, 0.25, 'auto');
                commonModalDialogEvents(showCustomerDialog, function () {
                    
                });
            } else {
                if (!response.result) {
                    pinesMessageV2({
                        ty: response.info ? 'information' : 'error',
                        m: response.error ? response.error : response.info,
                        d: response.error ? 3000 : 10000
                    });
                } else {
                    pinesMessageV2({
                        ty: 'success',
                        m: _lang.hideInCustomerPortalSuccess.sprintf(response.category === 'Litigation' ? [_lang.litigation.toLowerCase()] : [_lang.matter.toLowerCase()])
                    });
                    if (jQuery('#legalCaseGrid').length) {
                        // grid
                        jQuery('#legalCaseGrid').data('kendoGrid').dataSource.read();

                    } else {//edit form

                    }
                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}
function addContactEmail(contactId, emailField) {
    var email = emailField.val();
    var validation = validateEmail(emailField);

    if (validation === true) {
        jQuery.ajax({
            url: getBaseURL() + 'contacts/add_email',
            method: 'post',
            data: { contact_id: contactId, email: email },
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    addContactEmailRow(response.contact_email.id, email, jQuery('#selected-emails'));
                    emailField.val(null);
                    pinesMessage({ ty: 'success', m: _lang.recordAddedSuccessfully });
                } else {
                    pinesMessage({ ty: 'error', m: response.error_message });
                }
            },
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        pinesMessage({ ty: 'error', m: validation });
    }
}

function disableFields(container) {
    container = ('string' == typeof container) ? jQuery('#' + container) : container;
    jQuery('.form-control, .btn, .btn-action', container).each(function (e, t) {
        jQuery(this).attr("disabled", "disabled");
    });
}
function disableAnchors(container) {
    container = ('string' == typeof container) ? jQuery('#' + container) : container;
    jQuery('.disable-anchor', container).each(function (e, t) {
        jQuery(this).addClass("disabled-anchor");
        jQuery(this).css("pointer-events", "none");
    });
}

function showHideContainerInCp(id) {
    jQuery.ajax({
        url: getBaseURL() + 'case_containers/show_hide_container_in_cp/',
        type: 'POST',
        dataType: 'JSON',
        data: { 'id': id },
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.html) {
                jQuery("<div id='share-matters-container'></div>").appendTo("body");
                var relateMattersContainer = jQuery('#share-matters-container');
                relateMattersContainer.html(response.html);
                commonModalDialogEvents(relateMattersContainer);
                initializeModalSize(relateMattersContainer, 0.8);
                initializeSelectAllCheckbox(relateMattersContainer);
                jQuery('#submit-actions', relateMattersContainer).on('click', function () {
                    var formData = jQuery('form#shared-matters-form', relateMattersContainer).serializeArray();
                    jQuery.ajax({
                        url: getBaseURL() + 'case_containers/show_hide_container_in_cp/',
                        dataType: "json",
                        type: 'POST',
                        data: { 'id': id, 'form_data': formData, 'submit_form': 1 },
                        beforeSend: function () {
                            jQuery("#loader-global").show();
                        },
                        success: function (response) {
                            if (response.validation_errors) {
                                var relateMattersContainer = jQuery('#share-matters-container');
                                jQuery(".inline-error").addClass('d-none');
                                displayValidationErrors(response.validation_errors, relateMattersContainer);
                            } else {
                                if (!response.result && response.info) {
                                    pinesMessage({ ty: 'information', m: response.info });
                                } else {
                                    jQuery(".modal", relateMattersContainer).modal("hide");
                                    pinesMessage({ ty: response.result ? 'success' : 'error', m: response.message });
                                    updateContainerCpFlag(response);
                                }
                            }
                        }, complete: function () {
                            jQuery("#loader-global").hide();
                        }, error: defaultAjaxJSONErrorsHandler
                    });
                });
            } else {
                pinesMessage(response.result ? {
                    ty: 'success',
                    m: response.message
                } : { ty: response.info ? 'information' : 'error', m: response.error ? response.error : response.info });
            }
            updateContainerCpFlag(response);
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function updateContainerCpFlag(response) {
    if (response.result) {
        if (jQuery('#caseContainerContainer').length > 0) {
            // edit form
            jQuery(response.visible ? '.circle.gray' : '.circle.green', '#caseContainerContainer').removeClass(response.visible ? 'gray' : 'green').addClass(response.visible ? 'green' : 'gray').attr('title', response.visible ? _lang.containerVisibleFromCP : _lang.hiddenInCP);
            jQuery('#visible_in_cp', '#caseContainerContainer').val(response.visible);
            jQuery('#show-hide-btn', '#caseContainerContainer').text(response.visible ? _lang.hideInCustomerPortal : _lang.showInCustomerPortal);
        } else {
            // grid
            jQuery('#containerGrid').data('kendoGrid').dataSource.read();
        }
    }
}

function initializeSelectAllCheckbox(container) {
    jQuery('#select-all', container).click(function () {
        jQuery('[data-field="record-checkbox"]', container).each(function () {
            this.checked = jQuery('#select-all', container).is(':checked') ? true : false;
        });
    });
}

function dismissAllNotification() {
    var notificationListLatest = jQuery('#notificationsListLatest');
    jQuery.ajax({
        url: getBaseURL() + 'notifications/dismiss_all',
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response.status) {
                notificationListLatest.empty();
                notificationListLatest.html('<p>' + _lang.noLatestNotifications + '</p>');
                pinesMessage({ ty: 'success', m: _lang.dismissAllSuccessfully });
                jQuery('h3.popover-header', jQuery('#notificationsContainer').parent().parent()).html(_lang.notificationsCounter.sprintf([0]) + notificationsPopoverIconClose);
            } else {
                if (response.empty) {
                    pinesMessage({ ty: 'information', m: _lang.noLatestNotifications });
                } else {
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.updatesFailed });
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function selectTimeLogRelation(element) {
    var addEditTimer = jQuery("#add-edit-timer");
    var caseContainer = jQuery("#case-input-container", addEditTimer);
    var taskContainer = jQuery("#task-input-container", addEditTimer);
    var taskInput = jQuery("#task", addEditTimer);
    var caseInput = jQuery("#legal-caselookup", addEditTimer);
    caseInput.typeahead('val', '');
    taskInput.typeahead('val', '');
    jQuery("#legal-caselookup-id", addEditTimer).val("");
    jQuery("#task-id", addEditTimer).val("");
    jQuery("#client-name", addEditTimer).attr("readonly", false);
    clearTimerTaskUnbindCase(true);
    if (jQuery(element).val() === 'case') {
        taskContainer.addClass("d-none");
        caseContainer.removeClass("d-none");
        taskInput.attr("disable", true);
        caseInput.attr("disable", false);
    } else {
        caseContainer.addClass("d-none");
        taskContainer.removeClass("d-none");
        taskInput.attr("disable", false);
        caseInput.attr("disable", true);
    }
}

function selectTypeOfTransaction(element) {
    var addEditTimer = jQuery("#add-edit-timer");
    var clientInputContainer = jQuery("#client-input-container", addEditTimer);
    if (element === 'internal') {
        disableClientInput(addEditTimer, true);
        clientInputContainer.addClass("d-none");
    } else {
        if (jQuery("input:radio[name=timer-type]:checked", addEditTimer).val() === 'case') {
            clientInputContainer.removeClass("d-none");
            disableClientInput(addEditTimer, false);
        } else {
            if (jQuery('#legal-caselookup-id', addEditTimer).val()) {
                clientInputContainer.removeClass("d-none");
                disableClientInput(addEditTimer, false);
            } else {
                clientInputContainer.addClass("d-none");
                disableClientInput(addEditTimer, true);
            }
        }
    }
}

var onTimerTaskSelection = function onTimerTaskSelection(taskDetails) {
    var addEditTimer = jQuery("#add-edit-timer");
    var timerStatus = jQuery("input:radio[name=time-status]:checked", addEditTimer).val();
    if (taskDetails.legal_case_id != null) {
        jQuery("#legal-caselookup-id", addEditTimer).val(taskDetails.legal_case_id);
        jQuery("#task-related-case-subject", addEditTimer).html(taskDetails.description);
        showTimerTaskRelatedCaseSubject(taskDetails.caseSubject, addEditTimer, taskDetails.category);
        var clientInputContainer = jQuery("#client-input-container", addEditTimer);
        jQuery("#client-id", addEditTimer).val(taskDetails.client_id);
        jQuery("#client-name", addEditTimer).val(taskDetails.client_name);
        if (timerStatus === 'billable') {
            clientInputContainer.removeClass("d-none");
            (taskDetails.client_id != null) ? disableClientInput(addEditTimer, false, true, true) : disableClientInput(addEditTimer, false, true, false);
        } else {
            clientInputContainer.addClass("d-none");
            (taskDetails.client_id != null) ? disableClientInput(addEditTimer, true, true, true) : disableClientInput(addEditTimer, true, true, false);
        }
    } else {
        clearTimerTaskUnbindCase(false);
    }
};

function showTimerTaskRelatedCaseSubject(legalCaseSubject, container, category) {
    var taskRelatedCaseSubjectText = _lang.relatedCase + ': ' + legalCaseSubject + ' <a target="_blank" href="' + getBaseURL() + (category == undefined || category != 'IP' ? 'cases' : 'intellectual_properties') + '/edit/' + jQuery('#legal-caselookup-id', container).val() + '">' + _lang.goTo + '</a>';
    jQuery('#task-related-case-subject', container).append("<br/>" + taskRelatedCaseSubjectText);
}

function clearTimerTaskUnbindCase(isClearTask) {
    isClearTask = isClearTask || false;
    var addEditTimer = jQuery("#add-edit-timer");
    var clientInputContainer = jQuery("#client-input-container", addEditTimer);
    jQuery("input:radio[name=time-status]", addEditTimer).prop('checked', false);
    clientInputContainer.addClass("d-none");
    jQuery("input:radio[name=time-status][value=internal]", addEditTimer).prop('checked', true);
    if (isClearTask) jQuery("#task-id", addEditTimer).val("");
    jQuery("#legal-caselookup-id", addEditTimer).val("");
    jQuery("#task-related-case-subject", addEditTimer).html("");
    jQuery('#legal-caselookup-id', addEditTimer).val('');
    jQuery('#legal-caselookup', addEditTimer).val('');
    jQuery("#client-name", clientInputContainer).val("").attr("disable", true);
}

var onTimerCaseLookup = function onTimerCaseLookup(lookupFiled, container) {
    var addEditTimer = jQuery("#add-edit-timer");
    var clientInputContainer = jQuery("#client-input-container", addEditTimer);
    var timerStatus = jQuery("input:radio[name=time-status]:checked", addEditTimer).val();
    jQuery("#client-id", addEditTimer).val(lookupFiled.client_id);
    jQuery("#client-name", addEditTimer).val(lookupFiled.clientName);
    if (timerStatus === 'billable') {
        clientInputContainer.removeClass("d-none");
    } else {
        clientInputContainer.addClass("d-none");
    }
    if (lookupFiled.client_id != null) {
        disableClientInput(addEditTimer, false, true, true);
    } else {
        disableClientInput(addEditTimer, false, true, false);
    }
};

function disableClientInput(container, option, changeReadOnly, readOnlyOption) {
    changeReadOnly = changeReadOnly || false;
    readOnlyOption = readOnlyOption || false;
    jQuery("#client-id", container).attr("disable", option);
    jQuery("#client-name", container).attr("disable", option);
    if (changeReadOnly) {
        jQuery("#client-name", container).attr("readonly", readOnlyOption);
    }
}

//load contract form events
function contractFormEvents(container) {
    jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
    setDatePicker('#contract-date', container);
    setDatePicker('#start-date', container);
    setDatePicker('#end-date', container);
    initializeModalSize(container);
    objectInitialization('parties', container);
    lookupDetails = {
        'lookupField': jQuery('#requester-lookup', container),
        'hiddenId': '#requester-id',
        'errorDiv': 'requester_id',
        'resultHandler': setRequesterToContract
    };
    lookUpContacts(lookupDetails, jQuery(container));
    lookUpUsers(jQuery('#authorized-signatory-lookup', container), jQuery('#authorized-signatory-id', container), 'authorized_signatory', jQuery('.authorized-signatory-container', container), container);
    jQuery('#assignee-id', container).change(function () {
        if (jQuery('#assignee-id', container).val() == 'quick_add') {
            jQuery('#assignee-id', container).val('').selectpicker('refresh');
            addUserToTheProviderGroup(jQuery('#assigned-team-id', container).val(), 'assignee-id', true);
        }
    });
    jQuery('#assigned-team-id', container).change(function () {
        reloadUsersListByProviderGroupSelected(jQuery('#assigned-team-id', container).val(), jQuery("#assignee-id", container), true);
    });
    loadCustomFieldsEvents('custom-field-', container);
    lookupPrivateUsers(jQuery('#contributors-lookup', container), 'contributors', '#selected-contributors', 'contributors-container', container);
    jQuery('#type', container).change(function () {
        if (jQuery(this).val()) {
            contractTypeEvent(jQuery(this).val(), jQuery("#sub-type", container));
        }
    });
}

function setRequesterToContract(record, container) {
    var name = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#requester-lookup', container).val(name);
    jQuery('#requester-id', container).val(record.id);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#requester-lookup", container).typeahead('destroy');
        lookupDetails = {
            'lookupField': jQuery('#requester-lookup', container),
            'hiddenId': '#requester-id',
            'errorDiv': 'requester_id',
            'resultHandler': setRequesterToContract
        };
        lookUpContacts(lookupDetails, jQuery(container));
    }

}

function setQuestionnairelookupValues(record, container) {
    container = jQuery(container);
    var objectName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    var objectType = record.name ? 'company' : 'contact';
    jQuery('.field-member-id', container).val(record.id);
    jQuery('.field-lookup', container).val(objectName);
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery('.field-lookup', container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('.field-lookup', container),
            'hiddenInput': jQuery('.field-member-id', container),
            'errorDiv': jQuery(this).attr('data-lookup-type'),
            'resultHandler': setQuestionnairelookupValues
        };
        lookupCompanyContactType(lookupDetails, container, objectType);
    }
}


//initialize objects
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

//clone object container
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
        quickAdministrationDialog('party_categories', jQuery(container), true, false, false, jQuery('[data-field-id=' + object + '-category-' + newId + ']'), false, 'contract');
    });
    if (nbOfObject == 1) {
        jQuery('.delete-' + object, jQuery('#' + object + '-' + (nbOfObject), objectContainer)).removeClass('d-none');
    }

    objectEvents(object, jQuery(clonedContainer, objectContainer));

    event.preventDefault();
}

//load object events
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
        lookupCompanyContactType(lookupDetails, container);
    });
    jQuery('#' + object + '-category', container).selectpicker();
    companyContactFormMatrix.commonLookup[objectId] = {
        "lookupType": jQuery("select#" + object + "-member-type", container).val(),
        "referalContainerId": container
    }
    jQuery('#' + object + '-lookup', container).typeahead('destroy');//destroy the typeahead when changing the type(company/contact) and re-initialize it To avoid conflict of lookup initializing
    lookupCompanyContactType(lookupDetails, container);
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

//update object labels and ids - used in dialog forms
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
            jQuery('.' + object + '-category-quick-add', this).removeAttr('onClick').attr("onclick", 'quickAdministrationDialog(\'party_categories\', ' + container + '\' , ' + true + ', ' + false + ', ' + false + ', ' + jQuery('[data-field-id=' + object + '-category-' + count + ']') + ')', false, 'contract');

            count++;
        } else {
            return true;
        }
    });
    objectInitialization(object, container);
}

//delete object
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

function zoomMeetingForm() {
    var randomNumber = Math.round(Math.random() * (upperBound - lowerBound) + lowerBound);
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + "zoom_meetings/create_zoom_meeting",
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var zoomMeetingDialogId = "#zoom-meeting-dialog-" + randomNumber;
                if (jQuery(zoomMeetingDialogId).length <= 0) {
                    jQuery('<div id="zoom-meeting-dialog-' + randomNumber + '"></div>').appendTo("body");
                    var zoomMeetingDialog = jQuery(zoomMeetingDialogId);
                    zoomMeetingDialog.html(response.html);
                    jQuery("form", zoomMeetingDialog).attr('id', 'contact-add-form-' + randomNumber);
                    jQuery('#contact-add-form-' + randomNumber).validationEngine({
                        validationEventTrigger: "submit",
                        autoPositionUpdate: true,
                        promptPosition: 'bottomRight',
                        scroll: false
                    });
                    initializeModalSize(zoomMeetingDialog, 0.5, 0.45);
                    lookupPrivateUsers(jQuery('#lookup-participants', zoomMeetingDialog), 'participants', '#selected-participants', 'participants-container', jQuery('#zoom-meeting-body'));
                    jQuery('.modal', zoomMeetingDialog).modal({
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

/**
 * Convert Hours to minutes and hours
 * @param hoursValue
 * @return {string}
 */
function hoursToMinutesHours(hoursValue) {
    var minutesRemain = (hoursValue % 1).toFixed(2);
    var minutesRemainValue = 0;
    var returnValue = hoursValue;
    if (minutesRemain > 0) {
        minutesRemainValue = minutesRemain * 60
    }
    if (minutesRemainValue > 0) {
        if (Math.floor(hoursValue) > 0) {
            returnValue = Math.floor(hoursValue) + 'h ' + parseFloat(minutesRemainValue).toFixed(0) + 'm'
        } else {
            returnValue = parseFloat(minutesRemainValue).toFixed(0) + 'm'
        }
    } else {
        if (Math.floor(hoursValue) > 0) {
            returnValue = Math.floor(hoursValue) + 'h';
        } else {
            returnValue = '0';
        }
    }
    return returnValue;
}

function submitManageMoneyAccounts(model, modelId, category) {
    var formData = jQuery("form#manage-money-accounts-form").serializeArray();
    var targetModel = jQuery('#target-model', '#manage-money-accounts-form').val();
    jQuery.ajax({
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        type: 'POST',
        dataType: 'JSON',
        data: formData,
        success: function (response) {
            if (response) {
                jQuery("#loader-global").hide();
                if (response.error) {
                    pinesMessage({ ty: 'information', m: response.error });
                } else if (!response.result) {
                    pinesMessage({ ty: 'information', m: _lang.feedback_messages.updatesFailed });
                } else if (response.result) {
                    jQuery(".modal", jQuery("#manage-money-accounts-dialog")).modal("hide");
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
        url: getBaseURL() + 'contacts/manage_money_accounts/' + model + '/' + modelId + '/' + category + '/' + targetModel
    });
}

function renewSubscription() {
    window.location = getBaseURL() + 'subscription/details';
}

function upgradeSubscription() {
    window.location = getBaseURL() + 'subscription/details';
    //    jQuery.ajax({
    //        beforeSend: function () {
    //            jQuery("#loader-global").show();
    //        },
    //        type: 'POST',
    //        dataType: 'JSON',
    //        success: function (response) {
    //            if (response) {
    //                if (response.error) {
    //                    jQuery("#loader-global").hide();
    //                    pinesMessage({ty: 'information', m: response.error});
    //                } else {
    //                    $subscribeTempContainer = jQuery('<div id="adjust-temp-Form"></div>').addClass("d-none").appendTo("body").html(response.html);
    //                    jQuery('form#subscribe-temp-form', $subscribeTempContainer).submit();
    //                    $subscribeTempContainer.remove();
    //                }
    //            }
    //        },
    //        error: defaultAjaxJSONErrorsHandler,
    //        url: getBaseURL() + 'subscription/upgrade'
    //    });
}

function subscriptionAddUser() {
    jQuery.ajax({
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response) {
                if (response.error) {
                    jQuery("#loader-global").hide();
                    pinesMessage({ ty: 'information', m: response.error });
                } else {
                    jQuery(".modal", jQuery("#subscription-msg-dialog")).modal("hide");
                    document.getElementById("overlay-container").style.height = "100%";
                    $subscribeTempContainer = jQuery('<div id="adjust-temp-Form"></div>').addClass("d-none").appendTo("body").html(response.html);
                    jQuery('form#subscribe-temp-form', $subscribeTempContainer).submit();
                    $subscribeTempContainer.remove();
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
        url: getBaseURL() + 'subscription/add_user'
    });
}

function displaySubscriptionAdditionalUserForm() {
    // cloud customer cannot increase license users without purchase
    jQuery.ajax({
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response) {
                jQuery("#loader-global").hide();
                if (response.error) {
                    pinesMessage({ ty: 'information', m: response.error });
                } else {
                    if (response.request_being_processed) {
                        pinesMessage({ ty: 'information', m: response.request_being_processed });
                    } else {
                        if (!jQuery('#subscription-msg-dialog').length) {
                            jQuery("<div id='subscription-msg-dialog'></div>").appendTo("body");
                        }
                        var subscriptionMsgDialog = jQuery("#subscription-msg-dialog");
                        subscriptionMsgDialog.html(response.html);
                        jQuery(".modal", subscriptionMsgDialog).modal({
                            keyboard: false,
                            backdrop: "static",
                            show: true
                        });
                        jQuery(document).keyup(function (e) {
                            if (e.keyCode == 27) {
                                jQuery(".modal", subscriptionMsgDialog).modal("hide");
                            }
                        });
                    }
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
        url: getBaseURL() + 'subscription/add_user_window'
    });
}

function TimerFormCancel(action, container) {
    confirmationDialog('cancel_timer_message', {
        resultHandler: function () {
            jQuery("#cancel-time-log").val(true);
            jQuery("#less-one-min").val(false);
            TimerFormSubmit(action, container);
        }, onCloseHandler: function () {
            jQuery("#cancel-time-log").val(false);
            jQuery("#less-one-min").val(false);
        }
    });
}

function convertMinsToHrsMins(inputElement) {
    var timeHoursMinutes = inputElement.val();
    timeHoursMinutes = timeHoursMinutes.toLowerCase();
    var timeHoursMinutesValue = timeHoursMinutes.replace(' ', '');
    var result = parseFloat(0);
    var timesArrHours = timeHoursMinutesValue.split("h");
    var timesArrM = timeHoursMinutesValue.split("m");
    var timesArrHoursValue = (timesArrHours[0].indexOf('m') > -1) ? '' : timesArrHours[0];
    var hoursStr = timesArrHoursValue + 'h';
    var timesArrMinValue = (timesArrM[0].indexOf('h') > -1) ? (timesArrM[0].replace(hoursStr, '')) : (((timeHoursMinutesValue.indexOf('m') > -1) || (timeHoursMinutesValue.indexOf('h') > -1)) ? timesArrM[0] : '');
    if (timesArrHoursValue) {
        result = result + parseFloat(timesArrHoursValue);
    }
    if (timesArrMinValue) {
        result = result + Math.floor(timesArrMinValue / 60, 2);
        var remainedMin = timesArrMinValue % 60;
        if (result > 0) {
            if (remainedMin > 0) {
                inputElement.val(result + 'h ' + remainedMin + 'm');
            } else {
                inputElement.val(result + 'h');
            }
        } else {
            if (remainedMin > 0) {
                inputElement.val(remainedMin + 'm');
            } else {
                inputElement.val('');
            }
        }
    }
}

var ajaxEvents = (function () {
    'use strict';

    /**
     * @param element
     */

    /**
     * on complete ajax action events
     */
    function completeEventsAction(container, duplicate, options, saveText, saveAndDuplicate) {
        options = options || {};
        duplicate = duplicate || false;
        saveText = saveText || _lang.save;
        saveAndDuplicate = saveAndDuplicate || _lang.saveAndDuplicate;
        var duplicateButton = options.hasOwnProperty("duplicate_button") ? "." + options.duplicate_button : ".duplicate-button";
        var saveButton = options.hasOwnProperty("save_button") ? "." + options.save_button : ".save-button";
        jQuery('#loader-global').hide();
        if (duplicate) {
            jQuery(duplicateButton, container).html(saveAndDuplicate)
        } else {
            jQuery(saveButton, container).html(saveText);
        }
        jQuery(duplicateButton, container).attr('disabled', false);
        jQuery(saveButton, container).attr('disabled', false);
    }

    /**
     * before complete ajax events
     */
    function beforeActionEvents(container, duplicate, options) {
        options = options || {};
        duplicate = duplicate || false;
        var duplicateButton = options.hasOwnProperty("duplicate-button") ? "." + options.duplicate_button : ".duplicate-button";
        var saveButton = options.hasOwnProperty("save-button") ? "." + options.save_button : ".save-button";
        jQuery('#loader-global').show();
        jQuery(duplicateButton, container).attr('disabled', true);
        jQuery(saveButton, container).attr('disabled', true);
        duplicate ? jQuery(duplicateButton, container).html(saveHtml) : jQuery(saveButton, container).html(saveHtml);
    }

    /**
     * display error message
     */
    function displayValidationError(field, container, message) {
        jQuery('.inline-error', container).removeClass('validation-error');
        jQuery('.input-warning', container).removeClass('input-warning');
        var selector = jQuery("div", container).find("[data-field=" + field + "]").length > 0 ? jQuery("div", container).find("[data-field=" + field + "]") : (jQuery("td", container).find("[data-field=" + field + "]").length > 0 ? jQuery("td", container).find("[data-field=" + field + "]") : false);
        if (selector) {
            selector.removeClass('d-none').html(message).addClass('validation-error');
            jQuery("input[data-field=" + field + "]", container).each(function () {
                if (this.value === '') {
                    jQuery(this).addClass('input-warning');
                }
            });
        } else {
            pinesMessage({ ty: 'error', m: _lang[message].sprintf([_lang[field]]) });
        }
    }

    function onSuccessfulChange(container, duplicate, options) {
        options = options || {};
        var changeOnDuplicate = duplicate && options.hasOwnProperty("change_on_duplicate") ? options.change_on_duplicate : false;
        if (changeOnDuplicate && isFunction(changeOnDuplicate)) options.change_on_duplicate();
    }

    return {
        completeEventsAction: completeEventsAction,
        beforeActionEvents: beforeActionEvents,
        displayValidationError: displayValidationError,
        onSuccessfulChange: onSuccessfulChange
    };
}());

/**
 * @module time log dialog
 * @public
 */
var timeLogsDialog = (function () {
    'use strict';

    /**
     *
     * @param element
     */

    function changeTimeLogAction(element, _getSystemRate) {
        _getSystemRate = _getSystemRate || true;
        var container = jQuery("#add-edit-time-log");
        jQuery("#log-options-menu", container).trigger("logType:toggle");
        jQuery("#task_id", container).val('');
        jQuery("#task_lookup", container).typeahead('val', '');
        jQuery("#legal_case_id", container).val('');
        jQuery("#case_lookup", container).typeahead('val', '');
        jQuery("#audit-info-toggle-link", container).addClass('d-none');
        jQuery("#audit-info", container).addClass('d-none');
        eraseClient();
        if (_getSystemRate !== 'no') getSystemRate();
        if (jQuery(element, container).val() == 'task') {
            toggleLookup(true, '#matter_lookup_wrapper');
            toggleLookup(false, '#task_lookup_wrapper');
            if (jQuery("#billable", container).is(':checked')) {
                toggleLookup(true, '#client-lookup-wrapper', true);
                jQuery("#billable", container).parent().removeClass('pull-right');
            } else {
                toggleLookup(false, '#client-lookup-wrapper', true);
                jQuery("#billable", container).parent().addClass('pull-right');
            }
        } else {
            toggleLookup(false, '#matter_lookup_wrapper');
            toggleLookup(true, '#task_lookup_wrapper');
            if (jQuery("#billable", container).is(':checked')) {
                toggleLookup(true, '#client-lookup-wrapper', true);
                jQuery("#billable", container).parent().removeClass('pull-right');
            } else {
                toggleLookup(false, '#client-lookup-wrapper', true);
                jQuery("#billable", container).parent().addClass('pull-right');
            }
        }
    }

    /**
     * To hide and disable a given lookpup "client, matter or task"
     *
     * @param state
     * @param element
     * @param readOnly
     * @param disable
     */
    function toggleLookup(state, element, readOnly, disable) {
        readOnly = readOnly || "false";
        disable = disable || "false";
        var container = jQuery("#add-edit-time-log");
        var LookupWrapper = jQuery(element, container);
        var LookupWrapperInputs = jQuery(element + " :input", container);
        if (state) {
            LookupWrapper.addClass('d-none').fadeIn(500);
            LookupWrapperInputs.prop("disabled", true);
            LookupWrapperInputs.prop("readonly", true);
        } else {
            LookupWrapper.removeClass('d-none').fadeOut(0).fadeIn(500);
            (disable != "false") ? LookupWrapperInputs.prop('disabled', true) : LookupWrapperInputs.prop('disabled', false);
            (readOnly != "false") ? LookupWrapperInputs.prop('readonly', true) : LookupWrapperInputs.prop("readonly", false);
        }
    }

    function billableChecked(element) {
        var container = jQuery("#add-edit-time-log");
        var taskIdValue = jQuery("#task_id", container).val();
        var clientIdValue = jQuery("#client-id", container).val();
        if (jQuery(element, container).is(':checked')) {
            addClientInput(true);
            jQuery("#billable", container).val("internal");
            jQuery("#billable-id", container).val("internal");
            toggleRateSection(false, container);
        } else {
            if (!taskIdValue || clientIdValue) {
                addClientInput(false);
                toggleRateSection(true, container);
            } else {
                jQuery("#split-time-link", container).removeClass("d-none");
            }
            jQuery("#billable", container).val("billable");
            jQuery("#billable-id", container).val("billable");
        }
    }

    function addClientInput(billableState) {
        var container = jQuery("#add-edit-time-log");
        var action = jQuery('#log-options-menu', container).val();
        if (billableState) {
            //non billable
            toggleLookup(true, "#client-lookup-wrapper", true);
            jQuery("#billable", container).parent().removeClass('pull-right');
            jQuery("#billable", container).prop("checked", true);
            jQuery("#billable", container).val("internal");
            jQuery("#billable-id", container).val("internal");
            jQuery("#split-time-link", container).addClass("d-none");
        } else if (!billableState) {
            //billable
            jQuery("#billable", container).prop("checked", false);
            if (!jQuery("#legal_case_id", container).val() || (jQuery("#client-id", container).val() != "null" && jQuery("#client-id", container).val() != "")) {
                toggleLookup(false, "#client-lookup-wrapper", true);
            } else {
                if (action == 'case') {
                    toggleLookup(false, "#client-lookup-wrapper", "false");
                } else {
                    toggleLookup(true, "#client-lookup-wrapper");
                }
            }
            jQuery("#billable", container).parent().addClass('pull-right');
            jQuery("#split-time-link", container).removeClass("d-none");
            jQuery("#billable", container).prop("checked", false);
            jQuery("#billable", container).val("billable");
            jQuery("#billable-id", container).val("billable");
        }
    }

    function repeatInput(element) {
        var container = jQuery("#add-edit-time-log");
        var checkboxParent = jQuery(element, container).parent();
        if (jQuery(element, container).is(':checked')) {
            jQuery("#to-date-wrapper", container).removeClass('d-none').fadeOut(0).fadeIn(500);
            checkboxParent.addClass('pull-right');
            var logDate = new Date(jQuery('#log-date', container).val());
            var minDate = new Date(logDate.setDate(logDate.getDate() + 1));
            jQuery('#repeat-until-date-input', container).datepicker('option', 'setStartDate', minDate);
        } else {
            checkboxParent.removeClass('pull-right');
            jQuery("#to-date-wrapper", container).addClass('d-none').fadeOut(500);
            jQuery("#repeat-until-date-input", container).val('');
        }
    }

    var onCaseLookup = function onCaseLookup(lookupFiled) {
        var container = jQuery("#add-edit-time-log");
        getSystemRate();
        changeClientOnCaseLookupSelect(container, lookupFiled);
        jQuery("#audit-info-toggle-link", container).removeClass('d-none');
        setAuditSection(lookupFiled);
    };

    var onTaskLookup = function onTaskLookup(lookupFiled) {
        var container = jQuery("#add-edit-time-log");
        jQuery("#rate-time-log", container).selectpicker('val', '');
        toggleRateSection(false, container);
        changeClientOnTaskLookupSelect(container, lookupFiled);
        setAuditSection(lookupFiled);
        jQuery("#audit-info-toggle-link", container).removeClass('d-none');
    };

    var onEraseCaseLookup = function onEraseCaseLookup() {
        var container = jQuery("#add-edit-time-log");
        eraseClient();
        jQuery("#audit-info", container).addClass('d-none');
        jQuery("#audit-info-toggle-link", container).addClass('d-none');
    };

    /**
     * On select matter change value of client based on value selected
     */
    function changeClientOnCaseLookupSelect(container, lookupFiled) {
        jQuery("#client-id", container).val(lookupFiled.client_id);
        jQuery("#client-name", container).val(lookupFiled.clientName);
        if (lookupFiled.client_id) {
            jQuery("#client-name", container).prop("readonly");
            jQuery("#client-name", container).prop('disabled');
            jQuery("#client-id", container).prop('disabled');
            toggleLookup(true, "#client-lookup-wrapper", true);
        } else {
            jQuery("#client-name", container).removeProp('readonly');
            jQuery("#client-name", container).removeProp('disabled');
            jQuery("#client-id", container).removeProp('disabled');
            toggleLookup(false, "#client-lookup-wrapper", true);
        }
        addClientInput(jQuery("#billable", container).is(':checked'));
    }

    /**
     * On select task change value of client based on value selected
     */
    function changeClientOnTaskLookupSelect(container, lookupFiled) {
        jQuery("#client-id", container).val(lookupFiled.client_id);
        jQuery("#client-name", container).val(lookupFiled.client_name);
        if (lookupFiled.client_id) {
            jQuery("#client-name", container).prop("readonly");
            jQuery("#client-name", container).prop('disabled');
            jQuery("#client-id", container).prop('disabled');
            toggleLookup(true, "#client-lookup-wrapper", true);
            addClientInput(jQuery("#billable", container).is(':checked'));
            toggleLookup(false, '#rate-time-log-lookup-wrapper', false);
        } else {
            toggleLookup(true, "#client-lookup-wrapper");
            toggleLookup(true, '#rate-time-log-lookup-wrapper');
            jQuery("#billable", container).parent().removeClass('pull-right');
        }
    }

    function setAuditSection(data) {
        var container = jQuery("#add-edit-time-log");
        jQuery("#audit-id-lable", container).html(jQuery("#task_id", container).val() ? _lang.taskId : _lang.caseId);
        jQuery("#audit-id", container).html(jQuery("#task_id", container).val() ? data.task_id : data.caseID ? data.caseID : data.legalCaseLookup);
        jQuery("#audit-modified-date", container).html(data.modifiedOn);
        jQuery("#audit-workflow", container).html(jQuery("#task_id", container).val() ? data.taskStatus : data.status);
        jQuery("#created-by-audit", container).html(data.createdByName);
        jQuery("#created-on-audit", container).html(data.createdOn);
        jQuery("#modified-by-audit", container).html(data.modifiedByName);
    }

    var onErasetaskLookup = function onErasetaskLookup() {
        jQuery("#rate-time-log", container).selectpicker('val', '');
        toggleRateSection(false, container);
        var container = jQuery("#add-edit-time-log");
        eraseClient();
        jQuery("#audit-info", container).addClass('d-none');
        jQuery("#audit-info-toggle-link", container).addClass('d-none');
        toggleLookup(true, '#rate-time-log-lookup-wrapper');
    };

    function eraseClient() {
        var container = jQuery("#add-edit-time-log");
        jQuery("#client-id", container).val('');
        jQuery("#client-name", container).typeahead('val', '');
        jQuery("#client-name", container).prop("readonly");
    }

    function logTimeSave(action, duplicate) {
        var container = jQuery("#add-edit-time-log");
        if (jQuery("#is-repeat", container).is(':checked') && jQuery("#repeat-until-date-input", container).val() !== '') {
            confirmationDialog('multiple_time_logs_confirmation', {
                resultHandler: function () {
                    logTime(action, duplicate);
                }
            });
        } else {
            logTime(action, duplicate);
        }
    }

    function logTime(action, duplicate) {
        var container = jQuery("#add-edit-time-log");
        var formData = jQuery("form#userActivityLogForm", container).serializeArray().map(function (obj) {
            if (obj.name == 'case_lookup' || obj.name == 'task_lookup' || obj.name == 'comments') {
                obj.value = escapeHtml(obj.value);
            }
            return obj;
        });
        formData.push({ name: 'duplicate', value: duplicate });
        var actionId = jQuery("#id", container).val();
        var url = actionId ? getBaseURL() + 'time_tracking/' + action + '/' + actionId : getBaseURL() + 'time_tracking/' + action;
        jQuery.ajax({
            dataType: 'JSON',
            url: url,
            data: formData,
            type: 'POST',
            beforeSend: function () {
                ajaxEvents.beforeActionEvents(container, duplicate);
            },
            success: function (response) {
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('time_logs');
                }
                jQuery(".inline-error").addClass('d-none');
                var actionStatus = response.status;
                switch (actionStatus) {
                    case "200":
                        pinesMessageV2({ ty: 'success', m: response.msg });
                        if (response.warning) {
                            pinesMessageV2({ ty: "warning", m: response.warning });
                        }
                        if (!duplicate) {
                            jQuery('.modal', "#time-log-dialog").modal('hide');
                        }
                        if (null != $legalCaseGrid) {
                            try {
                                $legalCaseGrid.data("kendoGrid").dataSource.read();
                            } catch (e) {
                            }
                        }
                        if (null != $timeTrackingGrid) {
                            try {
                                $timeTrackingGrid.data("kendoGrid").dataSource.read();
                            } catch (e) {
                            }
                        }
                        if (null != $tasksGrid) {
                            try {
                                $tasksGrid.data("kendoGrid").dataSource.read();
                            } catch (e) {
                            }
                        }
                        if (null != $moneyTimeTrackingGrid) {
                            try {
                                $moneyTimeTrackingGrid.data("kendoGrid").dataSource.read();
                            } catch (e) {
                            }
                        }
                        if (null != $bulkTimeTrackingGrid) {
                            try {
                                $bulkTimeTrackingGrid.data("kendoGrid").dataSource.read();
                            } catch (e) {
                            }
                        }
                        if (jQuery('#client-account-status', '#object-header').length && (jQuery('#case-id', '#object-header').val() == jQuery('#legal_case_id', container).val()) && (!jQuery("#billable", container).is(':checked'))) {
                            clientTransactionsBalance({
                                case: jQuery('#legal_case_id', container).val(),
                                client: jQuery('#client-id', container).val()
                            }, jQuery('#client-account-status', '#object-header'), jQuery('#controller', '#object-header').val());
                        }
                        ajaxEvents.onSuccessfulChange(container, duplicate, {
                            "change_on_duplicate": function () {
                                jQuery("#effectiveEffortHour", container).val("");
                                jQuery("#form-action", container).val("add");
                                jQuery("#split_billable_time_value", container).val("");
                                jQuery("#split_none_billable_time_value", container).val("");
                                jQuery("#splited-time-value", container).html("");
                                jQuery("#split-time-link", container).addClass("d-none");
                                jQuery("#log-date", container).val(jQuery.datepicker.formatDate('yy-mm-dd', new Date()));
                                jQuery("#id", container).val("");
                                jQuery("form#userActivityLogForm", container).attr("action", getBaseURL() + 'time_tracking/add')
                            }
                        });
                        break;
                    case "400":
                        if ('dates' in response) {
                            timeEntryConfirmation(response, action);
                        } else {
                            displayValidationErrors(response.validationErrors, container);
                        }
                        break;
                    case "401":
                        pinesMessageV2({
                            ty: "error",
                            m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page
                        });
                        break;
                    default:
                        break
                }
            }, complete: function () {
                ajaxEvents.completeEventsAction(container, duplicate);
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function splitTime() {
        var container = jQuery("#add-edit-time-log");
        var effortValue = jQuery("#effectiveEffortHour", container).val();
        if (timeValidation(effortValue)) {
            jQuery('.inline-error', container).removeClass('validation-error');
            jQuery("*[data-field=\"effectiveEffort\"]", container).addClass('d-none');
            splitTimePopup(effortValue);
        } else {
            ajaxEvents.displayValidationError('effectiveEffort', container, _lang.timeValidateFormat.sprintf([_lang.effort]));
        }
    }

    function splitTimePopup(effortValue) {
        var containerAddEdit = jQuery("#add-edit-time-log");
        var splitDialogUrl = getBaseURL().concat('time_tracking/split_time/').concat(effortValue);
        var data = {};
        jQuery.ajax({
            url: splitDialogUrl,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    var splitDialogId = "split-time-container";
                    jQuery('<div id="split-time-container"></div>').appendTo("body");
                    var container = jQuery("#" + splitDialogId);
                    container.html(response.html);
                    initializeModalSize(container, 0.35, 'auto');
                    commonModalDialogEvents(container);
                    var originalTime = jQuery("#original-time", container).val();
                    if (jQuery("#log-date", containerAddEdit).val()) {
                        jQuery("#split-date-wrapper span", container).html(jQuery("#log-date", containerAddEdit).val());
                        jQuery("#split-date-wrapper", container).removeClass("d-none");
                    }
                    jQuery("#effective-effort-hour-billable", container).keyup(function () {
                        var originalTimeDecimal = convertTimeToDecimal(originalTime);
                        if (jQuery(this).val()) {
                            var convertedBillableTime = convertTimeToDecimal(jQuery(this).val());
                            if (convertedBillableTime) {
                                jQuery("#effective-effort-hour-non-billable", container).val(convertDecimalToTime({ time: (originalTimeDecimal - convertedBillableTime).toFixed(2) }));
                            }
                        }
                    });
                    jQuery("#split-time-dialog-submit", container).click(function () {
                        var convertedBillableTime = convertTimeToDecimal(jQuery("#effective-effort-hour-billable", container).val());
                        var originalTimeDecimal = convertTimeToDecimal(originalTime);
                        if (!jQuery("#effective-effort-hour-billable", container).val()) {
                            ajaxEvents.displayValidationError('effective-effort-hour-billable', container, _lang.validation_field_required.sprintf([_lang.timeTrackingStatus.billable]));
                        } else if (!convertedBillableTime) {
                            ajaxEvents.displayValidationError('effective-effort-hour-billable', container, _lang.timeValidateFormat.sprintf([_lang.timeTrackingStatus.billable]));
                        } else if (originalTimeDecimal <= convertedBillableTime) {
                            ajaxEvents.displayValidationError('effective-effort-hour-billable', container, _lang.splitTimeMessage);
                        } else {
                            jQuery("#splited-time-value", containerAddEdit).html(_lang.BillableNonBillable.sprintf([convertDecimalToTime({ time: convertedBillableTime }), jQuery('#effective-effort-hour-non-billable', container).val()]));
                            jQuery("#split-none-billable-time-value", containerAddEdit).val(jQuery('#effective-effort-hour-non-billable', container).val());
                            jQuery("#split-billable-time-value", containerAddEdit).val(jQuery('#effective-effort-hour-billable', container).val());
                            jQuery('.modal', "#split-time-container").modal('hide');
                        }
                    });
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    /**
     * Set lookup value id and summary
     *
     * @param action string task or matter
     * @param action string task or matter
     * @param defaultTimeLog
     */
    function setLookupValue(action, element, defaultTimeLog) {
        var container = jQuery("#add-edit-time-log");
        if (action === "task") {
            if (!jQuery.isEmptyObject(element)) {
                jQuery("#task_id", container).val(element.taskId);
                jQuery("#task_lookup", container).typeahead('val', getTaskLookUpDisplayName(element));
                jQuery("#audit-info-toggle-link", container).removeClass('d-none');
                if (element.task_legal_case && element.clientId && element.clientName) {
                    jQuery("#client-name", container).typeahead('val', element.clientName);
                    jQuery("#client-id", container).val(element.clientId);
                } else {
                    toggleLookup(true, "#client-lookup-wrapper");
                    jQuery("#billable", container).parent().removeClass('pull-right');
                }
                setAuditSection(element);
                if (jQuery.isEmptyObject(element) && defaultTimeLog == 0) {
                    jQuery("#billable", container).val("billable");
                    jQuery("#billable-id", container).val("billable");
                    jQuery("#billable", container).prop("checked", false);
                }
            }
        } else {
            if (!jQuery.isEmptyObject(element)) {
                jQuery("#legal_case_id", container).val(element.legalCaseLookupId);
                jQuery("#client-id", container).val(element.clientId);
                jQuery("#case_lookup", container).typeahead('val', element.legalCaseLookup + ": " + element.legalCaseSubject);
                if (element.clientName != '') {
                    jQuery("#client-name", container).typeahead('val', element.clientName);
                    toggleLookup(true, "#client-lookup-wrapper", true);
                } else {
                    toggleLookup(false, "#client-lookup-wrapper", true);
                }
                jQuery("#audit-info-toggle-link", container).removeClass('d-none');
                setAuditSection(element);
            }
            var billableStatusValue = (typeof timeTrackingBillable !== 'undefined' && (timeTrackingBillable != 0)) ? (timeTrackingBillable == 0) : !(defaultTimeLog == 0);
            addClientInput(billableStatusValue);
        }
    }

    /**
     * On task select as time logs in add time logs popup
     */
    function taskLogTypeSelected(event, container) {
        toggleLookup(false, '#rate-time-log-lookup-wrapper');
        jQuery("#rate-time-log", container).selectpicker('val', '');
        jQuery("#fixed-rate-input", container).val('');
    }

    /**
     * On matter select as time logs in add time logs popup
     */
    function matterLogTypeSelected(event, container) {
        toggleLookup(true, '#rate-time-log-lookup-wrapper');
        toggleLookup(true, '#fixed-rate-time-wrapper');
        jQuery("#entity-time-log-wrapper").addClass('d-none').fadeIn(500);
    }

    /**
     * On changing rate of log time popup
     * @param element
     */
    function onChangeRate(element, _getSystemRate, clientId) {
        _getSystemRate = _getSystemRate || true;
        clientId = clientId || true;
        var container = jQuery("#add-edit-time-log");
        if (getSystemRateValue) {
            jQuery("#fixed-rate-input", container).val('');
        } else {
            getSystemRateValue = true
        }
        if (_getSystemRate !== 'no') getSystemRate();
        if (jQuery(element).val() == 'fixed_rate') {
            toggleLookup(false, '#fixed-rate-time-wrapper');
            jQuery("#entity-time-log-wrapper", container).addClass('d-none').fadeIn(500);
            jQuery("#related-entity", container).addClass('d-none').fadeIn(500);
        } else if (jQuery(element).val() == 'system_rate') {
            toggleLookup(false, '#fixed-rate-time-wrapper', true);
            jQuery("#rate-section-time-log-wrapper", container).removeClass('d-none').fadeOut(0).fadeIn(500);
            jQuery("#entity-time-log-wrapper", container).addClass('d-none').fadeOut(0).fadeIn(500);
            jQuery("#related-entity", container).removeClass('d-none').fadeOut(0).fadeIn(500);
        } else {
            jQuery("#rate-section-time-log-wrapper", container).addClass('d-none').fadeIn(500);
            jQuery("#entity-time-log-wrapper", container).addClass('d-none').fadeIn(500);
            jQuery("#related-entity", container).addClass('d-none').fadeIn(500);
            toggleLookup(true, '#fixed-rate-time-wrapper');
            if (jQuery("#billable", container).is(':checked')) {
                jQuery("#rate-time-log-lookup-wrapper", container).addClass('d-none').fadeIn(500);
            } else {
                if (clientId != "noClient") {
                    jQuery("#rate-time-log-lookup-wrapper", container).removeClass('d-none').fadeOut(0).fadeIn(500);
                }
            }
        }
    }

    /**
     * Hide and show Rate system seciton
     * @param state
     * @param container
     */
    function toggleRateSection(state, container) {
        jQuery("#entity-time-log-wrapper").addClass('d-none').fadeOut(0).fadeIn(500);
        jQuery("#fixed-rate-input", container).val('');
        if (state) {
            jQuery("#rate-time-log-lookup-wrapper").removeClass('d-none').fadeOut(0).fadeIn(500);
            jQuery("#related-entity").removeClass('d-none').fadeOut(0).fadeIn(500);
        } else {
            toggleLookup(true, '#fixed-rate-time-wrapper');
            jQuery("#rate-time-log-lookup-wrapper").addClass('d-none').fadeIn(500);
            jQuery("#related-entity").addClass('d-none').fadeIn(500);
            jQuery("#rate-time-log", container).selectpicker('val', '');
        }
    }

    /**
     * Get system rate based on entity, matter id, and user id
     */
    function getSystemRate() {
        var container = jQuery("#add-edit-time-log");
        var formData = jQuery("form#userActivityLogForm", container).serializeArray();
        jQuery.ajax({
            url: getBaseURL() + 'cases/get_system_rate',
            data: formData,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status && jQuery("#rate-time-log").val() == 'system_rate') {
                    jQuery("#fixed-rate-input", container).val(response.rate);
                } else {
                    jQuery("#fixed-rate-input", container).val();
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    /**
     * On changing entity of log time popup
     */
    function onChangeEntity() {
        getSystemRate();
    }

    /**
     * On changing user of log time popup
     */
    function onUserLookupSelect() {
        if (getSystemRateValue) {
            getSystemRate();
        }
    }

    /**
     * To show entity select drop down list
     */
    function showEntity() {
        toggleLookup(false, '#entity-time-log-wrapper');
    }

    return {
        changeTimeLogAction: changeTimeLogAction,
        billableChecked: billableChecked,
        repeatInput: repeatInput,
        logTimeSave: logTimeSave,
        onCaseLookup: onCaseLookup,
        splitTime: splitTime,
        onEraseCaseLookup: onEraseCaseLookup,
        onErasetaskLookup: onErasetaskLookup,
        onTaskLookup: onTaskLookup,
        setLookupValue: setLookupValue,
        addClientInput: addClientInput,
        taskLogTypeSelected: taskLogTypeSelected,
        matterLogTypeSelected: matterLogTypeSelected,
        onChangeRate: onChangeRate,
        onChangeEntity: onChangeEntity,
        onUserLookupSelect: onUserLookupSelect,
        showEntity: showEntity
    };
}());

var timeActions = (function () {
    'use strict';

    function get_next_date(date) {
        var oldDate = new Date(date);
        var newDate = new Date(date);
        newDate.setDate(oldDate.getDate() + 1);
        return newDate.getFullYear() + "-" + ("0" + (newDate.getMonth() + 1)).slice(-2) + "-" + ("0" + newDate.getDate()).slice(-2);
    }

    function get_next_week(date) {
        var oldDate = new Date(date);
        var newDate = new Date(date);
        newDate.setDate(oldDate.getDate() + 7);
        return newDate.getFullYear() + "-" + ("0" + (newDate.getMonth() + 1)).slice(-2) + "-" + ("0" + newDate.getDate()).slice(-2);
    }

    function get_previous_week(date) {
        var oldDate = new Date(date);
        var newDate = new Date(date);
        newDate.setDate(oldDate.getDate() - 7);
        return newDate.getFullYear() + "-" + ("0" + (newDate.getMonth() + 1)).slice(-2) + "-" + ("0" + newDate.getDate()).slice(-2);
    }

    function get_previous_month(date) {
        var oldDate = new Date(date);
        var newDate = new Date(date);
        newDate.setMonth(oldDate.getMonth() - 1);
        return newDate.getFullYear() + "-" + ("0" + (newDate.getMonth() + 1)).slice(-2) + "-" + ("0" + newDate.getDate()).slice(-2);
    }

    function get_next_month(date) {
        var oldDate = new Date(date);
        var newDate = new Date(date);
        newDate.setMonth(oldDate.getMonth() + 1);
        return newDate.getFullYear() + "-" + ("0" + (newDate.getMonth() + 1)).slice(-2) + "-" + ("0" + newDate.getDate()).slice(-2);
    }

    function get_current_date(date) {
        var newDate = new Date(date);
        return newDate.getFullYear() + "-" + ("0" + (newDate.getMonth() + 1)).slice(-2) + "-" + ("0" + newDate.getDate()).slice(-2);
    }

    return {
        get_next_date: get_next_date,
        get_next_week: get_next_week,
        get_previous_week: get_previous_week,
        get_current_date: get_current_date,
        get_previous_month: get_previous_month,
        get_next_month: get_next_month,
    };
}());

/**
 * This module containes all function which is globle and help us to reduce the repetitive code
 * @type {{capitalizeFirstLetter: *, getSettingGridTemplate: *, numberWithCommas: *}}
 */
var helpers = (function () {
    'use strict';

    /**
     * make the first letter of string capital
     * @param string
     * @returns {string}
     */
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    /**
     * Create the gear template of kendo grid
     * @param options [] first option it is the event and the second for title "[list of events or classes, title]"
     * @returns {string}
     */
    function getSettingGridTemplate(options) {
        var listOptions = '';
        if (Array.isArray(options) && options.length) {
            jQuery.each(options, function (index, value) {
                listOptions += '<a class="dropdown-item" href="javascript:;" ' + value[0] + '>' + value[1] + '</a>';
            });
        }
        return '<div class="dropdown">' +
            '<a class="dropdown-toggle-new btn btn-default btn-sm dms-action-wheel" data-toggle="dropdown" href="##">' +
            '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-three-dots-vertical" fill="currentColor" xmlns="http://www.w3.org/2000/svg">\n' +
            '<path fill-rule="evenodd" d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>\n' +
            '</svg>' +
            '</a>' +
            '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' + listOptions + '</div>' +
            '</div>';
    }

    /**
     * make the number with camma sperator 1000 == 1,000
     * @param number
     * @returns {string}
     */
    function numberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    /**
     * Get name of from id of array {text: value}
     * @param arr
     * @param text
     * @param value
     * @param targetValue
     * @returns {string}
     */
    function getObjectFromArr(arr, text, value, targetValue) {
        targetValue = typeof targetValue.value === "undefined" ? targetValue : targetValue.value;
        var result = '';
        jQuery.each(arr, function (index) {
            var item = arr[index];
            if (item[value] == targetValue) {
                result = item[text];
                return true;
            }
        });
        return result;
    }

    /**
     * Toggel all fields inside a container
     * @param container string the container of fields
     * @param state boolean if enable or disable
     */
    function readonlyFieldsEnable(container, state) {
        state = state || 'false';
        container = ('string' == typeof container) ? jQuery('#' + container) : container;
        jQuery('.form-control, .btn, select, .select-picker, .input-group-addon, .dropdown-toggle', container).each(function (e, t) {
            jQuery(this).prop('readonly', state === 'false');
        });
        jQuery('.btn, input[type=checkbox], .duplicate-button', container).each(function (e, t) {
            jQuery(this).attr("disabled", "disabled");
        });
    }
    function onPageLoadEvents() {
        jQuery('.notification-send-email').on('click', function () {
            var checkBox = jQuery(this).find("input:checkbox");
            checkBox.prop("checked") ? checkBox.val(1) : checkBox.val(0);
        });
        var activeTitle = jQuery('.active-title');
        var parentMenu = activeTitle.parent().parent().siblings();
        if (parentMenu.hasClass('nav-link')) parentMenu.addClass("opacity-title");
    }
    return {
        capitalizeFirstLetter: capitalizeFirstLetter,
        getSettingGridTemplate: getSettingGridTemplate,
        numberWithCommas: numberWithCommas,
        getObjectFromArr: getObjectFromArr,
        readonlyFieldsEnable: readonlyFieldsEnable,
        onPageLoadEvents: onPageLoadEvents,
    };
}());

function renewalEvents(container) {
    if (jQuery('#renewal', container).val() == 'unlimited_period') {
        jQuery('#end-date-input', container).val('');
        hideRemindMeBefore(container);
        jQuery('#notify-me-before-link', container).addClass('d-none');
        return true;
    }

    notifyMeBefore(container);
    var startDate = jQuery('#start-date-input', container).val();
    if (startDate && jQuery('#end-date-input', container).val() == '') {
        var d = new Date(startDate);
        var year = d.getFullYear();
        var month = d.getMonth();
        var day = d.getDate();
        var renewalDate = new Date(year + 1, month, day);
        jQuery('#end-date-input').val(jQuery.datepicker.formatDate('yy-mm-dd', renewalDate));
    }
    renewalLookupsEvents(container);
}

function renewalLookupsEvents(container) {
    jQuery('#notify-to-emails', container).selectize({
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
        items: selectedEmails,
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
                return { email: input };
            }
            alert('Invalid email address.');
            return false;
        }
    });
    jQuery('#notify-to-teams', container).selectize({
        plugins: ['remove_button'],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: availableAssignedTeams
    });
}

function lookUpContracts(lookupDetails, container, callback, displayOnlyName) {
    callback = callback || false;
    displayOnlyName = displayOnlyName || false;
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('name');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
        },
        remote: {
            url: getBaseURL('contract') + 'contracts/autocomplete?term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });

    mySuggestion.initialize();
    lookupDetails['lookupField'].typeahead({
        hint: false,
        highlight: true,
        minLength: 2
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (displayOnlyName) {
                    return item.name
                } else {
                    return item.contract_id + ': ' + item.name
                }
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div title="' + data.name + '">' + data.contract_id + ': ' + data.name + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            lookupDetails['lookupField'].attr("title", datum.name);
            jQuery("#contract-name", container).removeClass("d-none");
            jQuery("#contract-link", container).attr("onclick", 'contractForm(\'' + datum.id + '\')');
            if (typeof lookupDetails.callback !== "undefined") {
                lookupDetails.callback(datum);
            }
            if (callback['callback'] && isFunction(callback['callback'])) {
                callback.callback(datum, container);
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            jQuery('.loader-submit', container).removeClass('loading');
        });
    lookupDetails.lookupField.on('input', function (e) {
        if (lookupDetails['lookupField'].val() == '') {
            lookupDetails['lookupField'].removeAttr("title");
            jQuery("#contract-name", container).addClass("d-none");
        }
    });
    if (!isFunction(callback['onEraseLookup']) || !callback['onEraseLookup']) {
        callback['onEraseLookup'] = false;
    }
    lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container, callback['onEraseLookup']);
}

function findVersionDifferences() {
    var url = getBaseURL() + 'docs/compare_file_versions'
    var versions = jQuery(".file-versions-checkboxes:checked").map(function () {
        var version_id = jQuery(this).val();
        var version_name = jQuery("#file-version-" + version_id).val();
        return { 'version_id': version_id, 'version_name': version_name };
    }).get();
    jQuery.ajax({
        url: url,
        data: { 'data': versions },
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                if (!jQuery('#file-versions-dialog').length) {
                    jQuery("<div id='file-versions-dialog'></div>").appendTo("body");
                }
                var fileVersionsDialog = jQuery("#file-versions-dialog");
                fileVersionsDialog.html(response.html);
                jQuery(".modal", fileVersionsDialog).modal({
                    keyboard: false,
                    backdrop: "static",
                    show: true
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function onSelectLookupCallback() {
    return true;
}

function docuSignGoLive() {
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('contract') + 'docusign_integration/convert_to_live_process',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
            } else {
                if ('undefined' !== typeof response.needs_authenticate && response.needs_authenticate) {
                    window.location.href = getBaseURL('contract') + 'docusign_integration/authenticate';
                    return;
                }
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ ty: 'warning', m: response.display_message });
                }


            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

// This function should be renamed to (initTiny) and replace all of the initTiny functions
// It must also handle the Textarea Editor in all the application with handling different modules and text areas options
// 

function initTinyTemp(id, containerTxt, module) {
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
        images_upload_url: getBaseURL() + module + '/upload_file',
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
                    tinymce.activeEditor.execCommand('mceInsertContent', false, '<img data-name="' + json.file.full_name + '" src="' + getBaseURL() + module + '/return_doc_thumbnail/' + json.file.id + '" width="100" height="100"  />');
                } else {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, '<a href="' + getBaseURL() + module + '/download_file/' + json.file.id + '" >' + json.file.full_name + '</a>');
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

function addLatestDevTimeStamp(container, elementId) {
    container = container || '#personal_info_div';
    elementId = elementId || '#latestDevelopment';
    var timestamp = new Date().toISOString().replace(/T/, ' ').replace(/\..+/, '');
    console.log(jQuery(elementId, container));
    jQuery(elementId, container).val(jQuery(elementId).val() + (jQuery(elementId, container).val() ? '\n[' : '[') + timestamp + ']:');
    if (jQuery(elementId, container).length)
        jQuery(elementId, container).scrollTop(jQuery(elementId, container)[0].scrollHeight - jQuery(elementId, container).height());
    jQuery(elementId, container).focus();
}

function contractGenerate(option,category="contract") {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'contracts/add',
        data: {option: option, step: 1,commercial_service_category:category},
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    if (jQuery('#contract-generate-container').length <= 0) {
                        jQuery('<div id="contract-generate-container" class="primary-style"></div>').appendTo("body");
                        var contractGenerateContainer = jQuery('#contract-generate-container');
                        contractGenerateContainer.html(response.html);
                        initializeModalSize(contractGenerateContainer);
                        commonModalDialogEvents(contractGenerateContainer);
                        contractGenerateEvents(contractGenerateContainer, option);
                    }
                }
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ ty: 'warning', m: response.display_message });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function isValidDate(dateString) {
    var regEx = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateString.match(regEx)) return false;  // Invalid format
    var d = new Date(dateString);
    var dNum = d.getTime();
    if (!dNum && dNum !== 0) return false; // NaN value, Invalid date
    return d.toISOString().slice(0, 10) === dateString;
}

/**
 * get All Filters for an export
 *
 * @param formId
 */
function getExportInfoFilter(formId) {
    filters = [];
    jQuery('#' + formId).find('.form-group').each(function () {
        if (jQuery(this).hasClass("form-group-filter")) {
            var chk_filter = jQuery(this).find(".chk-filter");
            if (!chk_filter.is(":checked")) return;
        }
        filter = {};
        filter['fieldTitle'] = jQuery(this).find("label.control-label").first().text();
        if (jQuery(this).find(".sf-operator").hasClass("start-date-operator"))
            filter['fieldOperator'] = jQuery(this).find('select.sf-operator-list option:selected').first().text();
        else
            filter['fieldOperator'] = jQuery(this).find('select.sf-operator option:selected').first().text();
        filter['fieldValue'] = "";
        if (jQuery(this).find(':input.sf-value').hasClass('multi-select')) {
            var option_all = jQuery(this).find('select.sf-value option:selected').map(function () {
                if (jQuery(this).val().trim())
                    return jQuery(this).text();
            }).get().join(', ');
            filter['fieldValue'] = option_all;
        } else if (jQuery(this).find(':input.sf-value').is("select")) {
            if (jQuery(this).find('select.sf-value option:selected').val().trim())
                filter['fieldValue'] = jQuery(this).find('select.sf-value option:selected').text()
        } else
            filter['fieldValue'] = jQuery(this).find(':input.sf-value').val()
        if (jQuery(this).find('.sf-value2').val())
            filter['fieldValue'] = filter['fieldValue'] + " , " + jQuery(this).find('.sf-value2').val();
        if (filter['fieldValue'] && filter['fieldTitle'])
            filters.push(filter)
    });
    return filters;
}

function contractGenerateEvents(container, option) {
    var currentForm, currentFs, nextForm, nextFs, previousFs;
    var $continue;
    var step, nextStep, prevStep, pagesNb, currentPageNb, previousPageNb = 0;
    switch (option) {
        case 'add':
            jQuery('#form-submit', jQuery('.modal-footer', container)).removeClass('hide');
            jQuery('#notification-div', jQuery('.modal-footer', container)).removeClass('hide');
            jQuery('#type', jQuery('#fieldset1', container)).change(function () {
                jQuery('#type', jQuery('#fieldset2', container)).val(jQuery(this).val()).selectpicker("refresh");
                if (jQuery(this).val()) {
                    contractTypeEvent(jQuery(this).val(), jQuery("#sub-type", jQuery('#fieldset2', container)));
                }
            });
            contractFormEvents(jQuery('#form2', jQuery('#fieldset2', container)));
            jQuery('#type', jQuery('#fieldset2', container)).change(function () {
                addContractTypeCustomFields(jQuery(this).val());
                jQuery('#type', jQuery('#fieldset1', container)).val(jQuery(this).val()).selectpicker("refresh");
            });
            break;
        case 'choose':
            jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
            jQuery('#type', container).change(function () {
                if (jQuery(this).val()) {
                    contractTypeEvent(jQuery(this).val(), jQuery("#sub-type", container));
                    contractTemplateEvent(jQuery(this).val(), jQuery("#sub-type").val(), jQuery("#templates", container));
                }
            });
            jQuery('#sub-type', container).change(function () {
                contractTemplateEvent(jQuery("#type", container).val(), jQuery(this).val(), jQuery("#templates", container));
            });
            break;
        default:
            break;
    }
    jQuery(".next").click(function () {
        step = parseInt(jQuery('.modal-footer', container).attr('data-field'));
        currentFs = jQuery('#fieldset' + step, container);
        currentForm = jQuery('form', currentFs);
        $continue = true;
        if (currentForm) {
            switch (true) {
                case step == 1:
                    jQuery('.inline-error', currentFs).addClass('d-none').html('');
                    if (option == 'choose') {
                        if (!jQuery('#templates', currentForm).val()) {
                            jQuery("[data-field='templates']", currentFs).removeClass('d-none').html(_lang.feedback_messages.templateRequired);
                            $continue = false;
                        }
                        if ($continue) {
                            jQuery('#doc-name', currentForm).val(jQuery('#doc-name-preffix', currentForm).val() + jQuery('#doc-name-suffix', currentForm).text());
                            nextStep = (step + 1);
                            nextFs = jQuery('#fieldset' + nextStep, container);
                            nextForm = jQuery('#form' + nextStep, nextFs);
                            if ('undefined' == typeof option || (option && option !== jQuery('#option', '#form1').val()) || (option && (previousPageNb == 0 || !previousPageNb))) {
                                jQuery.ajax({
                                    url: getBaseURL('contract') + 'contracts/add',
                                    dataType: 'JSON',
                                    type: 'GET',
                                    data: {
                                        option: option,
                                        step: nextStep,
                                        template_id: jQuery('#templates', currentForm).val()
                                    },
                                    beforeSend: function () {
                                        jQuery('#loader-global').show();
                                    },
                                    success: function (response) {
                                        if (response.result) {
                                            nextForm.html(response.html);
                                            jQuery('.tooltip-title', nextForm).tooltipster({
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
                                            jQuery("[id^='variable-']", nextForm).each(function () {
                                                var parent = jQuery('.' + jQuery(this).attr('id'), container);
                                                switch (jQuery(this).attr('data-type')) {
                                                    case 'date':
                                                        setDatePicker(jQuery(this).parent(), container);
                                                        break;
                                                    case 'date_time':
                                                        if (jQuery(this).parent().hasClass('date-picker')) {
                                                            setDatePicker(jQuery(this).parent(), container);
                                                        }
                                                        if (jQuery(this).hasClass('time')) {
                                                            jQuery(this).timepicker({
                                                                'timeFormat': 'H:i',
                                                            });
                                                        }
                                                        break;
                                                    case 'list':
                                                        jQuery(this).selectpicker();
                                                        break;
                                                    case 'single_lookup':
                                                        var fieldTypeData = jQuery(this).attr('data-lookup-type');
                                                        var lookupDetails = {
                                                            'lookupField': jQuery(this),
                                                            'hiddenId': '.field-member-id',
                                                            'errorDiv': fieldTypeData,
                                                            'resultHandler': setQuestionnairelookupValues
                                                        };
                                                        lookUpContacts(lookupDetails, parent);
                                                        break;
                                                    case 'multiple_lookup_per_type':
                                                        var lookupDetails = {
                                                            'lookupField': jQuery('.field-lookup', parent),
                                                            'hiddenInput': jQuery('.field-member-id', parent),
                                                            'resultHandler': setQuestionnairelookupValues
                                                        };
                                                        jQuery('.field-member-type', parent).selectpicker().change(function () {
                                                            jQuery(".field-lookup", parent).val('');
                                                            jQuery(".field-member-id", parent).val('');
                                                            // jQuery(".inline-error", parent).html('');
                                                            jQuery('.field-lookup', parent).typeahead('destroy');
                                                            lookupCompanyContactType(lookupDetails, parent, jQuery(this).val());
                                                        });
                                                        jQuery('.field-lookup', parent).typeahead('destroy');
                                                        lookupCompanyContactType(lookupDetails, parent, jQuery('.field-member-type option:selected', parent).val());
                                                        break;
                                                    case 'multiple_fields_per_type':
                                                        jQuery(this).selectpicker();
                                                        setDatePicker('#end-date', container);
                                                        break;
                                                    default:
                                                        break;

                                                }
                                            });
                                            if (response.pages) {
                                                pagesNb = response.pages;
                                                currentPageNb = 1;
                                                progressPerc = 100 / pagesNb;
                                                jQuery('.progress-bar', jQuery('#progress-bar', container)).css('width', progressPerc + '%').attr('progress', progressPerc);
                                                jQuery('#pages-count', jQuery('#progress-bar', container)).html(response.pages);
                                                if (response.pages == 1) {
                                                    jQuery('.next', jQuery('.modal-footer', container)).addClass('d-none');
                                                    jQuery('#form-submit', jQuery('.modal-footer', container)).removeClass('d-none');
                                                    jQuery('#notification-div', jQuery('.modal-footer', container)).removeClass('d-none');
                                                }
                                                jQuery("#progress-bar", container).removeClass("d-none");
                                                jQuery('.previous', jQuery('.modal-footer', container)).removeClass('d-none');
                                                jQuery(".modal-header", container).addClass("progress-padding");
                                                jQuery('.modal-footer', container).attr('data-field', nextStep);
                                                currentFs.hide();
                                                nextFs.show();
                                            } else {
                                                pinesMessage({ ty: 'error', m: _lang.feedback_messages.contractQuestionnairePagesError });

                                            }
                                        } else {
                                            $continue = false;
                                            pinesMessage({ ty: 'warning', m: response.display_message });
                                        }

                                    }, complete: function () {
                                        jQuery('#loader-global').hide();
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            }
                        }
                    }else{
                        jQuery('.inline-error', currentForm).addClass('hide');
                        var validationErrors = {};
                        if(jQuery('input[type=file]', jQuery('#fieldset' + step, container)).val() == ""){
                            validationErrors.contract_document = _lang.validation_field_required.sprintf([_lang.contractDocument]);
                        }
                        if(jQuery('#type', jQuery('#fieldset' + step, container)).val() == ''){
                            validationErrors.type_id = _lang.feedback_messages.fieldRequired;
                        }
                        if(jQuery.isEmptyObject(validationErrors)){
                            nextStep = (step + 1);
                            nextFs = jQuery('#fieldset' + nextStep, container);
                            jQuery('.modal-footer', container).attr('data-field', nextStep);
                            jQuery('.next', jQuery('.modal-footer', container)).addClass('hide');
                            jQuery('.previous', jQuery('.modal-footer', container)).removeClass('hide');
                            jQuery('#name', jQuery('#fieldset' + nextStep, container)).val(jQuery('input[type=file]', jQuery('#fieldset' + step, container))[0].files[0].name.replace(/\.[^/.]+$/, ""));
                            currentFs.hide();
                            nextFs.show();
                        }else{
                            displayValidationErrors(validationErrors, currentForm);
                        }
                    }
                    break;
                case (step > 1):
                    jQuery('.inline-error', currentForm).addClass('d-none');
                    nextPageNb = parseInt(currentPageNb) + 1;
                    nextStep = (step + 1);
                    if (contractGenerateEventCheckRequired(jQuery("[page-number=" + currentPageNb + "]", container))) {
                        jQuery('#current-page').html(nextPageNb);
                        jQuery('.modal-footer', container).attr('data-field', nextStep);

                        lastProgress = jQuery('.progress-bar', container).attr('progress');
                        newProgress = parseInt(lastProgress) + parseInt(progressPerc);

                        jQuery('.progress-bar', container).css('width', newProgress + '%').attr('progress', newProgress);

                        jQuery("[page-number=" + currentPageNb + "]", container).addClass('d-none');
                        currentPageNb = nextPageNb;
                        jQuery("[page-number=" + nextPageNb + "]", container).removeClass('d-none');
                        if (nextPageNb == pagesNb) {
                            jQuery('#form-submit', jQuery('.modal-footer', container)).removeClass('d-none');
                            jQuery('#notification-div', jQuery('.modal-footer', container)).removeClass('d-none');
                            jQuery('.next', jQuery('.modal-footer', container)).addClass('d-none');
                        }
                    }
                    break;
            }
        }

    });
    jQuery(".previous", container).click(function () {
        step = parseInt(jQuery('.modal-footer', container).attr('data-field'));
        currentFs = jQuery('#fieldset' + step, container);
        currentForm = jQuery('form', currentFs);
        prevStep = (step - 1);
        previousFs = jQuery('#fieldset' + prevStep, container);
        previousPageNb = parseInt(currentPageNb) - 1;
        //moving back between template pages
        if (option == 'choose' && (step > 1) && (previousPageNb > 0)) {
            previousStep = (step - 1);
            jQuery('#current-page').html(previousPageNb);
            jQuery('.modal-footer', container).attr('data-field', previousStep);

            lastProgress = jQuery('.progress-bar', container).attr('progress');
            newProgress = parseInt(lastProgress) - parseInt(progressPerc);

            jQuery('.progress-bar', container).css('width', newProgress + '%').attr('progress', newProgress);
            jQuery("[page-number=" + currentPageNb + "]", container).addClass('d-none');
            currentPageNb = previousPageNb;
            jQuery("[page-number=" + previousPageNb + "]", container).removeClass('d-none');
            jQuery('#form-submit', jQuery('.modal-footer', container)).addClass('d-none');
            jQuery('#notification-div', jQuery('.modal-footer', container)).addClass('d-none');
            jQuery('.next', jQuery('.modal-footer', container)).removeClass('d-none');
        } else {
            currentFs.hide();
            previousFs.show();
            jQuery('.modal-footer', container).attr('data-field', prevStep);
            jQuery('.next', jQuery('.modal-footer', container)).removeClass('d-none');
            jQuery('.previous', jQuery('.modal-footer', container)).addClass('d-none');
        }
    });
    jQuery("#form-submit", container).click(function () {
        jQuery('.inline-error', currentForm).addClass('d-none');
        step = parseInt(jQuery('.modal-footer', container).attr('data-field'));
        currentFs = jQuery('#fieldset' + step, container);
        currentForm = jQuery('form', currentFs);
        if (option == 'add') {
            var validationErrors = {};
            if(step == 1){
                if(jQuery('input[type=file]', jQuery('#fieldset' + step, container)).val() == ""){
                    validationErrors.contract_document = _lang.validation_field_required.sprintf([_lang.contractDocument]);
                }else{
                    jQuery('#name', jQuery('#fieldset' + (step + 1), container)).val(jQuery('input[type=file]', jQuery('#fieldset' + step, container))[0].files[0].name.replace(/\.[^/.]+$/, ""));
                }
                if(jQuery('#type', jQuery('#fieldset' + step, container)).val() == ''){
                    validationErrors.type_id = _lang.feedback_messages.fieldRequired;
                }
            }
            if(jQuery.isEmptyObject(validationErrors)){
                var formData = new FormData(document.getElementById('form2'));
                formData.append('option', option);
                formData.append('step', step);
                formData.append('contract_attachment_0', jQuery('input[type=file]', jQuery('#fieldset1', container))[0].files[0]);
                //save the contract
                jQuery.ajax({
                    url: getBaseURL('contract') + 'contracts/add',
                    dataType: 'JSON',
                    type: 'POST',
                    contentType: false, // required to be disabled
                    cache: false,
                    processData: false,
                    data: formData,
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    },
                    success: function (response) {
                        jQuery('.inline-error', currentForm).addClass('d-none');
                        if (response.result) {
                            window.location.href = getBaseURL('contract') + 'contracts/view/' + response.id;

                        } else {
                            displayValidationErrors(response.validationErrors, currentForm);
                        }
                    }, complete: function () {
                        jQuery('#loader-global').hide();
                    },
                    error: defaultAjaxJSONErrorsHandler
                });
            }else{
                displayValidationErrors(validationErrors, currentForm);
            }
        }
        if (option == 'choose') {
            if (contractGenerateEventCheckRequired(jQuery("[page-number=" + currentPageNb + "]", container))) {
                var formData = jQuery("#form1, #form2, #form3").serialize();
                //save the contract
                jQuery.ajax({
                    url: getBaseURL('contract') + 'contracts/add',
                    dataType: 'JSON',
                    type: 'POST',
                    data: formData,
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    },
                    success: function (response) {
                        jQuery('.inline-error', currentForm).addClass('d-none');
                        if (response.result) {
                            window.location.href = getBaseURL('contract') + 'contracts/view/' + response.id;
                        } else {
                            displayValidationErrors(response.validationErrors, currentForm);
                        }
                    }, complete: function () {
                        jQuery('#loader-global').hide();
                    },
                    error: defaultAjaxJSONErrorsHandler
                });
            }
        }
    });
}

function addContractTypeCustomFields(typeId) {
    jQuery(".type-custum-fields-div", "#custom_fields_div").html("");
    if (typeId) {
        jQuery.ajax({
            url: getBaseURL('contract') + 'contracts/add_contract_type_custom_fields',
            dataType: 'JSON',
            data: { 'type_id': typeId },
            type: 'GET',
            success: function (response) {
                if (response.html) {
                    jQuery(".type-custum-fields-div", "#custom_fields_div").html(response.html);
                    loadCustomFieldsEvents('custom-field-', ".type-custum-fields-div");
                }
            }, error: defaultAjaxJSONErrorsHandler
        });
    }
}

function initializeField(input, options) {
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

function notifyMeBeforeRenewal(container) {
    notifyMeBefore(container);
    renewalLookupsEvents(container);
}
function lookUpPartners(lookupDetails, container) {
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('name');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
        },
        remote: {
            url: getBaseURL('money') + 'accounts/lookup_partner?term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });

    mySuggestion.initialize();
    lookupDetails['lookupField'].typeahead({
        hint: false,
        highlight: true,
        minLength: 3
    }, {
        source: mySuggestion.ttAdapter(),
        display: function (item) {
            return item.name + ' - ' + item.currencyCode;
        },
        templates: {
            empty: [
                '<div class="empty"> ' + _lang.noMatchesFound + '</div>'
            ].join('\n'),
            suggestion: function (data) {
                return '<div>' + data.name + ' - ' + data.currencyCode + '</div>'
            }
        }
    }).on('typeahead:selected', function (obj, datum) {
        if (typeof lookupDetails.callBackFunction !== "undefined") {
            lookupDetails.callBackFunction(container);
        }
    }).on('typeahead:asyncrequest', function () {
        jQuery('.loader-submit', container).addClass('loading');
    }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
        jQuery('.loader-submit', container).removeClass('loading');
    });
    lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container);
}

function contractGenerateEventCheckRequired(container) {
    jQuery(".inline-error", container).addClass('d-none');
    $next = true;
    jQuery("[id^='variable-']", container).each(function () {
        var parent = jQuery('.' + jQuery(this).attr('id'), container);
        isRequired = jQuery(this).attr('is_required');
        if (isRequired == 1) {//check value if set
            if (!jQuery(this).val()) {
                $next = false;
                jQuery("div", parent).find("[data-field=" + jQuery(this).attr('id') + "]").html(_lang.feedback_messages.fieldRequired).removeClass('d-none');
                jQuery('.modal-body').scrollTo(parent);
            }
        }
    });
    var radios = jQuery(".radio_buttons", container);
    for (var j = 0; j < radios.length; j++) {
        if (!isOneInputChecked(radios[j], 'radio')) {
            $next = false;
            jQuery(".inline-error", radios[j]).html(_lang.feedback_messages.fieldRequired).removeClass('d-none');
            jQuery('.modal-body').scrollTo(radios[j]);
        }
    }
    var checkboxes = jQuery(".check_boxes", container);
    for (var j = 0; j < checkboxes.length; j++) {
        if (!isOneInputChecked(checkboxes[j], 'checkbox')) {
            $next = false;
            jQuery(".inline-error", checkboxes[j]).html(_lang.feedback_messages.fieldRequired).removeClass('d-none');
            jQuery('.modal-body').scrollTo(checkboxes[j]);
        }
    }
    return $next;
}

function contractTypeEvent(typeId, subTypeContainer, selectedId) {
    selectedId = selectedId || 0;
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/load_sub_contract_types',
        dataType: 'JSON',
        data: { 'type_id': typeId },
        type: 'GET',
        success: function (results) {
            var newOptions = '<option value="0">' + _lang.none + '</option>';
            if (typeof results != "undefined" && results != null && results.length > 0) {
                for (i in results) {
                    newOptions += '<option value="' + results[i].id + '">' + results[i].name + '</option>';
                }
            }
            subTypeContainer.html(newOptions).val(selectedId).selectpicker("refresh");
        }, error: defaultAjaxJSONErrorsHandler
    });
}

function deleteTaskLocationSelectedRow(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'task_locations/delete/' + id,
            type: 'GET',
            dataType: 'JSON',
            // data: {taskId: id},
            success: function (response) {
                var ty = 'error';
                var m = '';
                if (response.status) {
                    ty = 'information';
                    m = response.message;
                    jQuery('table').find("#tl_" + id).remove();
                } else {
                    m = response.message;
                }
                pinesMessage({ ty: ty, m: m });
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function change_invoice_status(voucherID, status) {
    var url = '';
    var msg101 = '';
    var msg102 = '';
    switch (status) {
        case 'open':
            url = getBaseURL('money') + 'vouchers/convert_invoice_to_open';
            msg101 = _lang.invoiceHasBeenConvertedToOpen;
            msg202 = _lang.invalid_request;
            break;
        case 'draft':
            url = getBaseURL('money') + 'vouchers/set_invoice_as_draft';
            msg101 = _lang.invoiceHasBeenSetAsDraft;
            msg202 = _lang.you_can_not_set_paid_or_partially_paid_invoice_as_draft;
            break;
        case 'cancel':
            url = getBaseURL('money') + 'vouchers/cancel_invoice';
            msg101 = _lang.invoiceHasBeenCancelled;
            msg202 = _lang.you_can_not_cancel_any_paid_or_partially_paid_invoice;
            break;
    }
    jQuery.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        data: { voucherID: voucherID },
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.status) {
                case 101:	// changed successfuly
                    ty = 'success';
                    m = msg101;
                    break;
                case 102:	// changed successfuly
                    ty = 'warning';
                    m = _lang.youHaveToSetTheDefaultSystemCommissionAccount.sprintf(['<a href="' + getBaseURL('money/setup/global_partner_shares_account') + '">' + _lang.here + '</a>']);
                    break;
                case 202:	// invalid request
                    ty = 'warning';
                    m = msg202;
                    break;
                default:
                    break;
            }
            pinesMessage({ ty: ty, m: m });
            if (jQuery('#invoiceGrid').length > 0) {
                jQuery('#invoiceGrid').data("kendoGrid").dataSource.read();
            }
            if (jQuery('#invoiceForm').length > 0) {
                location.reload();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteInvoiceRecord(invoiceId) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/delete_invoice/' + invoiceId,
        dataType: "json",
        data: {},
        success: function (data) {
            if (data.status) {
                if (jQuery('#invoiceGrid').length > 0) {
                    jQuery('#invoiceGrid').data("kendoGrid").dataSource.read();
                }
                pinesMessage({ ty: 'success', m: _lang.deleteRecordSuccessfull });
            } else {
                pinesMessage({ ty: 'error', m: _lang.deleteRecordFailed });
            }
            if (jQuery('#invoiceForm').length > 0) {
                window.location = getBaseURL('money') + 'vouchers/invoices_list';
            }
        }
    });
}
function customerSupport() {
    jQuery('.popover').popover('hide');
    let customrSupportLi = jQuery('.dropdown-menu-customer-support').parent();
    if (!customrSupportLi.hasClass('open')) {
        jQuery.ajax({
            dataType: 'JSON',
            url: getBaseURL() + "users/customer_support",
            type: 'GET',
            success: function (response) {
                if (response.result) {
                    let jsonObj = {
                        "x-api-key": response.apiKey
                    };
                    let iframeEl = document.getElementById('customer-support');
                    if (iframeEl != null) {
                        iframeEl.onload = function () {
                            iframeEl.contentWindow.postMessage({ payload: jsonObj }, "*");
                        };
                    }
                    jQuery('#customer-support').attr('src', response.src);
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function licensePackageAccessCheck() {
    if (licensePackage == 'contract') {
        jQuery('.core-access').addClass('d-none');
    } else if (licensePackage == 'core') {
        jQuery('.contract-access').addClass('d-none');
    }
}
function getCurrentYearQuarterInHijri() {
    var currentHijriMonth = moment().iMonth(); // starting from zero
    if (currentHijriMonth <= 2) {
        return {
            startOf: moment().locale('en').iMonth(0).startOf('iMonth').format('iYYYY-iMM-iDD'),
            endOf: moment().locale('en').iMonth(2).endOf('iMonth').format('iYYYY-iMM-iDD')
        }
    } else if (currentHijriMonth > 2 && currentHijriMonth <= 5) {
        return {
            startOf: moment().locale('en').iMonth(3).startOf('iMonth').format('iYYYY-iMM-iDD'),
            endOf: moment().locale('en').iMonth(5).endOf('iMonth').format('iYYYY-iMM-iDD')
        }

    } else if (currentHijriMonth > 5 && currentHijriMonth <= 8) {
        return {
            startOf: moment().locale('en').iMonth(6).startOf('iMonth').format('iYYYY-iMM-iDD'),
            endOf: moment().locale('en').iMonth(8).endOf('iMonth').format('iYYYY-iMM-iDD')
        }

    } else if (currentHijriMonth > 8 && currentHijriMonth <= 11) {
        return {
            startOf: moment().locale('en').iMonth(9).startOf('iMonth').format('iYYYY-iMM-iDD'),
            endOf: moment().locale('en').iMonth(11).endOf('iMonth').format('iYYYY-iMM-iDD')
        }
    }
}

function getPreviousYearQuarterInHijri() {
    var currentHijriMonth = moment().iMonth(); // starting from zero
    if (currentHijriMonth <= 2) {
        return {
            startOf: moment().locale('en').iMonth(-3).startOf('iMonth').format('iYYYY-iMM-iDD'),
            endOf: moment().locale('en').iMonth(-1).endOf('iMonth').format('iYYYY-iMM-iDD')
        }
    } else if (currentHijriMonth > 2 && currentHijriMonth <= 5) {
        return {
            startOf: moment().locale('en').iMonth(0).startOf('iMonth').format('iYYYY-iMM-iDD'),
            endOf: moment().locale('en').iMonth(2).endOf('iMonth').format('iYYYY-iMM-iDD')
        }
    } else if (currentHijriMonth > 5 && currentHijriMonth <= 8) {
        return {
            startOf: moment().locale('en').iMonth(3).startOf('iMonth').format('iYYYY-iMM-iDD'),
            endOf: moment().locale('en').iMonth(5).endOf('iMonth').format('iYYYY-iMM-iDD')
        }

    } else if (currentHijriMonth > 8 && currentHijriMonth <= 11) {
        return {
            startOf: moment().locale('en').iMonth(6).startOf('iMonth').format('iYYYY-iMM-iDD'),
            endOf: moment().locale('en').iMonth(8).endOf('iMonth').format('iYYYY-iMM-iDD')
        }
    }
}
function contractTemplateEvent(typeId, subTypeId, templateContainer) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/load_templates_per_contract_types',
        dataType: 'JSON',
        data: { 'type_id': typeId, 'sub_type_id': subTypeId },
        type: 'GET',
        success: function (response) {
            var newOptions = '<option value="">' + _lang.choose + '</option>';
            if (typeof response.templates != "undefined" && response.templates != null) {
                for (i in response.templates) {
                    newOptions += '<option value="' + i + '">' + response.templates[i] + '</option>';
                }
            }
            templateContainer.html(newOptions).selectpicker("refresh");

        }, error: defaultAjaxJSONErrorsHandler
    });
}

function toggleCheckbox(hiddenInput, checkBoxInput) {
    hiddenInput.val(checkBoxInput.is(':checked') ? '1' : '0');
}

// archive/unarchive contract
function toolsActionsContract(id, action) {

    if (id == 'fromSelection') {
        // to archive-unarchive contracts by selecting checkboxes.
        contracts_ids = [];
        jQuery('input[name="contract-ids"]:checked').each(function () {
            contracts_ids.push(this.value);
        });
        switch (action) {
            case 'archive':
                confirmationMessage = 'confirmation_archive_selected_contracts';
                resultHandler = archiveUnarchiveContractSubmission;
                parm = [action, contracts_ids];
                break;
            case 'unarchive':
                confirmationMessage = 'confirmation_unarchive_selected_contracts';
                resultHandler = archiveUnarchiveContractSubmission;
                parm = [action, contracts_ids];
                break;
            case 'delete':
                confirmationMessage = 'confim_delete_all_contract_action';
                resultHandler = contractDelete;
                parm = [contracts_ids];
                break;
        }

        confirmationDialog(confirmationMessage, {
            resultHandler: resultHandler,
            parm: parm
        });
    }
    else {
        // to archive-unarchive single contract from the single dropdown.
        archived = document.getElementById(action).name;
        confirmationDialog(archived == 'no' ? 'confirmation_message_to_archive_contract' : 'confirmation_message_to_unarchive_contract', {
            resultHandler: archiveUnarchiveContractSubmission,
            parm: id
        });
    }
}

function archiveUnarchiveContractSubmission(contracts_ids) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/archive_unarchive_contracts',
        type: 'POST',
        dataType: 'JSON',
        data: { 'contracts_ids': contracts_ids },
        success: function (response) {
            pinesMessageV2(response.status ? {
                ty: 'information',
                m: _lang.feedback_messages.contractArchivedSuccessfully
            } : { ty: 'error', m: _lang.feedback_messages.contractArchiveFailed });

            if (jQuery('#contracts-details').length) {
                //in single contract form
                jQuery("#archive-unarchive-btn", ".contract-container").attr('name', response.archived == "yes" ? "yes" : "no");
                jQuery('#archive-unarchive-btn', ".contract-container").text(response.archived == "yes" ? _lang.unarchive : _lang.archive);
                jQuery("#archive-unarchive-btn", ".contract-container").attr('title', response.archived == "yes" ? _lang.unarchive : _lang.archive);
                jQuery(".archived-flag", ".contract-container").html(response.archived == "yes" ? "(" + _lang.archived + ")" : '');
            } else {
                // in contracts grid
                jQuery('#' + gridId).data('kendoGrid').dataSource.read();
                jQuery('#unarchive-button-id', '#' + gridId).addClass('disabled');
                jQuery('#archive-button-id', '#' + gridId).addClass('disabled');
                jQuery('#delete-all-button-id', '#' + gridId).addClass('disabled');

            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

// check-uncheck checboxes for arhiving-unarchiving contracts
function checkUncheckCheckboxes(statusChkBx, flag) {
    //if the "Select All" checkbox is selected
    if (flag == "all") {
        jQuery("tbody input[type='checkbox']", "#" + gridId).attr('checked', statusChkBx.checked);
    }
    var $checkboxes = jQuery("tbody input[type='checkbox']", "#" + gridId);
    var countCheckedCheckboxes = $checkboxes.filter(':checked').length;
    if (countCheckedCheckboxes >= 1) {
        jQuery('#unarchive-button-id').removeClass('disabled');
        jQuery('#archive-button-id').removeClass('disabled');
        jQuery('#delete-all-button-id').removeClass('disabled');
    }
    else {
        jQuery('#unarchive-button-id').addClass('disabled');
        jQuery('#archive-button-id').addClass('disabled');
        jQuery('#delete-all-button-id').addClass('disabled');
    }
}

function addRemoveMatterContributor(trigger, step, action) {
    step = step || "confirm_message";
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'system_preferences/add_remove_matter_contributors_from_trigger',
        data: { 'step': step, 'trigger': trigger, action: action },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                jQuery("#remove-matter-contributor-dialog").remove();
                if (jQuery('#remove-matter-contributor-dialog').length <= 0) {
                    jQuery('<div id="remove-matter-contributor-dialog"></div>').appendTo("body");
                    jQuery('#remove-matter-contributor-dialog').html(response.html);
                }
                jQuery('.modal', jQuery('#remove-matter-contributor-dialog')).modal({
                    keyboard: false,
                    show: true,
                    backdrop: 'static'
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

let userGuideObject = (function () {
    'use strict';

    function userGuideSetup(guide) {
        guide = guide || false;
        let url = getBaseURL().concat('users/user_guide/')
        let data = {};
        jQuery.ajax({
            url: url,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    let userGuideWrapper = "user-guide-wrapper";
                    jQuery('<div id="user-guide-wrapper"></div>').appendTo("body");
                    var container = jQuery("#" + userGuideWrapper);
                    container.html(response.html);
                    initializeModalSize(container, 0.35, 'auto');
                    commonModalDialogEvents(container, function () {
                        jQuery('.modal', container).modal('hide');
                        if (firstSignIn === '') {
                            setTimeout(function () { initialSetup() }, 500);
                        } else {
                            // if(!guide) supportDemo();
                        }
                        if (!guide) submitGuide(false, false, true);
                    });
                    jQuery(".modal", container).on("hidden.bs.modal", function () {
                        jQuery('.modal', container).modal('hide');
                        if (firstSignIn === '') {
                            setTimeout(function () { initialSetup() }, 500);
                        } else {
                            // if(!guide) supportDemo();
                        }
                        if (!guide) submitGuide(false, false, true);
                    });
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function nextGuidePage(element, page) {
        page = parseInt(page);
        jQuery('.pages-guide').addClass("d-none");
        jQuery(element).closest('.modal-content').find('.pages-guide').eq(page - 1).removeClass("d-none");
        var nextPageNb = page + 1;
        var backPageNb = page - 1;
        var pagesNbs = page + " of 5";
        jQuery("#back-guide-page").attr("onclick", "userGuideObject.nextGuidePage(this, '" + nextPageNb + "')");
        jQuery("#next-guide-page").attr("onclick", "userGuideObject.nextGuidePage(this, '" + backPageNb + "')");
        jQuery("#guides-nbs").html(pagesNbs);
        if (page == 1) {
            jQuery("#next-guide-page").addClass("d-none");
        } else {
            jQuery("#next-guide-page").removeClass("d-none");
        }
        if (page == 5) {
            jQuery("#back-guide-page").html(helpers.capitalizeFirstLetter(_lang.done));
            jQuery("#back-guide-page").attr("onclick", "userGuideObject.submitGuide(false, false, true)");
        } else {
            jQuery("#back-guide-page").html(helpers.capitalizeFirstLetter(_lang.next));
            jQuery("#back-guide-page").attr("onclick", "userGuideObject.nextGuidePage(this, '" + nextPageNb + "')");
        }
    }

    function submitGuide(module, steps, hideDialog, _firstSignIn) {
        module = module || false;
        steps = steps || false;
        hideDialog = hideDialog || false;
        _firstSignIn = firstSignIn || false;
        let url = getBaseURL().concat('users/user_guide/')
        let data = module ? { "submit": true, module: module } : { "submit": true, "firstSignIn": _firstSignIn };
        jQuery.ajax({
            url: url,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status && hideDialog) {
                    jQuery('.modal').modal('hide');
                }
                if (!module) {
                    userGuide = timeActions.get_current_date(new Date());
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function initialSetup() {
        let url = getBaseURL().concat('users/initial_setup/')
        let data = {};
        jQuery.ajax({
            url: url,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    let userGuideWrapper = "user-guide-wrapper";
                    jQuery('<div id="user-guide-wrapper"></div>').appendTo("body");
                    var container = jQuery("#" + userGuideWrapper);
                    container.html(response.html);
                    initializeModalSize(container, 0.35, 'auto');
                    commonModalDialogEvents(container, function () {
                        userGuideObject.submitInitialSetup();
                        jQuery('.modal').modal('hide');
                        // supportDemo();
                    });
                    jQuery(".modal", container).on("hidden.bs.modal", function () {
                        jQuery('.modal').modal('hide');
                        // supportDemo();
                    });
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function submitInitialSetup() {
        var container = jQuery('#user-guide-wrapper');
        var formData = jQuery('#initial-setup-form', container).serialize();
        let url = getBaseURL().concat('users/initial_setup/');
        jQuery.ajax({
            url: url,
            data: formData,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                jQuery('.modal').modal('hide');
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function manageWalkthrough() {
        var formData = { 'manage': true, 'module': true };
        let url = getBaseURL().concat('users/user_guide/');
        jQuery.ajax({
            url: url,
            data: formData,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.html) {
                    var walkthroughSettingsId = '#walkthrough-settings-container';
                    jQuery('<div id="walkthrough-settings-container"></div>').appendTo("body");
                    var walkthroughSettings = jQuery(walkthroughSettingsId);
                    walkthroughSettings.html(response.html);
                    commonModalDialogEvents(walkthroughSettings);
                    initializeModalSize(walkthroughSettings);
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(walkthroughSettings);
                    });
                } else if (response.error) {
                    pinesMessage({ ty: 'error', m: response.error });
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function submitManageWalkthrough(container) {
        var formData = jQuery("form#walkthroughSettings", container).serializeArray();
        formData.push({ name: 'manage', value: true }, { name: 'module', value: true }, { name: 'submit', value: true });
        let url = getBaseURL().concat('users/user_guide/');
        jQuery.ajax({
            url: url,
            data: formData,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                if (response.status) {
                    if (response.workthrough) workthrough = response.workthrough;
                    jQuery('.modal', container).modal('hide');
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function enableWalkthrough(container) {
        var formData = { 'clear_all': true, 'module': true };
        let url = getBaseURL().concat('users/user_guide/');
        jQuery.ajax({
            url: url,
            data: formData,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                if (response.status) {
                    workthrough = [];
                    jQuery('.modal').modal('hide');
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    return {
        enableWalkthrough: enableWalkthrough,
        manageWalkthrough: manageWalkthrough,
        submitManageWalkthrough: submitManageWalkthrough,
        userGuideSetup: userGuideSetup,
        nextGuidePage: nextGuidePage,
        submitGuide: submitGuide,
        initialSetup: initialSetup,
        submitInitialSetup: submitInitialSetup
    };
}());

function uploadDirectoryNotAllowed() {
    pinesMessage({ ty: 'error', m: _lang.uploadDirectoryNotAllowed });
}

function addUserDiablog() {
    var addUserUrl = getBaseURL().concat('users/add');
    var data = {};
    jQuery.ajax({
        url: addUserUrl,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var addUserDialogId = "add-user-dialog";
                jQuery('<div id="add-user-dialog"></div>').appendTo("body");
                var container = jQuery("#" + addUserDialogId);
                container.html(response.html);
                initializeModalSize(container, 0.35, 'auto');
                commonModalDialogEvents(container);
                jQuery("#user-group-id", container).selectpicker();
                jQuery("#seniority-level-id", container).selectpicker();
                jQuery("#add-user-dialog-submit", container).on('click', function () {
                    if(helpers.emailValid(jQuery(jQuery("#email-input", container)).val())){
                        jQuery(".inline-error", container).addClass('d-none');
                        if(jQuery("#generated-password-input", container).val().length < 7){
                            ajaxEvents.displayValidationError('password', container, _lang.shortPasswordMsg.sprintf([8]));
                        }else{
                            addUserFormSubmit();
                        }
                    } else{
                        jQuery(".inline-error", container).addClass('d-none');
                        console.log(jQuery("#generated-password-input", container).val().length);

                        if(jQuery("#generated-password-input", container).val().length < 7){
                            ajaxEvents.displayValidationError('password', container, _lang.shortPasswordMsg.sprintf([8]));
                        }
                        ajaxEvents.displayValidationError('email', container, _lang.invalidFormat);
                    }
                });
                jQuery("#add-timer-duplicate-button", container).on('click', function () {
                    if(helpers.emailValid(jQuery(jQuery("#email-input", container)).val())){
                        jQuery(".inline-error", container).addClass('d-none');
                        if(jQuery("#generated-password-input", container).val().length < 7){
                            ajaxEvents.displayValidationError('password', container, _lang.shortPasswordMsg.sprintf([8]));
                        }else{
                            addUserFormSubmit(true);
                        }
                    } else{
                        jQuery(".inline-error", container).addClass('d-none');
                        if(jQuery("#generated-password-input", container).val().length < 7){
                            ajaxEvents.displayValidationError('password', container, _lang.shortPasswordMsg.sprintf([8]));
                        }
                        ajaxEvents.displayValidationError('email', container, _lang.invalidFormat);
                    }
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function addUserFormSubmit(redirect) {
    redirect = redirect || false;
    var addUserUrl = getBaseURL().concat('users/add');
    var container = jQuery("#add-user-dialog");
    jQuery.ajax({
        url: addUserUrl,
        data: jQuery("form#add-user-dialog-form", container).serializeArray(),
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                jQuery(".modal", container).modal("hide");
                pinesMessageV2({ ty: 'success', m: response.message });
                if (redirect) {
                    window.location.href = getBaseURL().concat('users/edit/').concat(response.id);
                }
            } else {
                console.log(response.validationErrors);
                if (typeof response.validationErrors.status != 'undefined') {
                    displayValidationErrors(response.validationErrors, container);
                } else {
                    displayValidationErrors(response.validationErrors, container, true);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function generatePassword() {
    var addUserUrl = getBaseURL().concat('users/add');
    var data = { 'generate_pass': true };
    jQuery.ajax({
        url: addUserUrl,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            // jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var container = jQuery("#add-user-dialog");
                jQuery('#generated-password-input', container).val(response.random_password);
            }
        }, complete: function () {
            // jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function systemSettingDropDown() {
    if (jQuery("#user-dropdown-list").hasClass('user-dropdown-list-hide')) {
        jQuery("#user-dropdown-list").toggle();
    } else {
        jQuery("#user-dropdown-list").addClass('user-dropdown-list-hide');
    }
}

function changePasswordType(element, container) {
    var _container = jQuery(container);
    var _faEyePass = jQuery(".fa-eye-pass", _container);
    if (jQuery(element).attr('type') == 'password') {
        jQuery(element).attr('type', 'text');
        _faEyePass.removeClass('fa-eye');
        _faEyePass.addClass('fa-eye-slash');
    } else {
        jQuery(element).attr('type', 'password');
        _faEyePass.addClass('fa-eye');
        _faEyePass.removeClass('fa-eye-slash');
    }
}

function initApiAccessToken(callbackFunction, errorUrl) {
    if (!_tokenGlobal)   //if not logged in yet
        return false;
    if (!localStorage.getItem('api-access-token')) {
        errorUrl = (errorUrl || '');
        jQuery.ajax({
            url: getBaseURL() + "api/v2/money?token=" + _tokenGlobal,
            dataType: 'json',
            type: 'GET',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (data) {
                if (data.access_token) {
                    localStorage.setItem('api-access-token', data.access_token);
                    if (typeof callbackFunction != 'undefined' && callbackFunction)
                        callbackFunction();
                }
                else {
                    pinesMessageV2({ ty: 'error', m: _lang.access_denied });
                    if (errorUrl != '')
                        setTimeout(() => window.location = errorUrl, 700);
                }
            },
            complete: function (XHRObj) {
                jQuery('#loader-global').hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                pinesMessageV2({ ty: 'error', m: jqXHR.responseJSON && jqXHR.responseJSON.message ? jqXHR.responseJSON.message : _lang.feedback_messages.error + ': ' + errorThrown });
                if (errorUrl != '')
                    setTimeout(() => window.location = errorUrl, 700);
            }
        });
    }
    else if (typeof callbackFunction != 'undefined' && callbackFunction)
        callbackFunction();
}

function uploadDirectoryNotAllowed() {
    pinesMessage({ ty: 'error', m: _lang.uploadDirectoryNotAllowed });
}

function getContractDesciption(value) {
    return (value != null && value != "") ? ("<span class='tooltip-title' title='" + value + "'>" + helpers.truncate(value, 50, true, _lang.languageSettings['langDirection'] === 'rtl') + "</span>") : "";
}

function trimHtmlTags(string) {
    if (string && string.length > 0) {
        return string.replace(/<[^>]*>?/gm, '');
    }
    return "";
}
function timeEntryConfirmation(response, action)
{
    var date1 = new Date(response.dates.last_date);
    var date2 = new Date(response.dates.log_date);
    var diffTime = Math.abs(date2 - date1);
    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    var allowViewSettings = response.allow_go_to_settings;
    var timeEntryRuleConfirmation = "#time-entry-confirmation-container";
    var container = jQuery("#add-edit-time-log");
    if (jQuery(timeEntryRuleConfirmation).length <= 0) {
        jQuery("<div id='time-entry-confirmation-container' class='primary-style'>\n\
                    <div class='modal fade modal-container modal-resizable'>\n\
                        <div class='modal-dialog'>\n\
                            <div class='modal-content'>\n\
                                <div class='modal-header'>\n\
                                    <h4 id='title' class='modal-title'>" + _lang.timeEntryRuleMessageTitle + "</h4><button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>\n\
                                </div>\n\
                                <div class='modal-body form-horizontal overflow-unset'>\n\
                                    <div class='row mx-0'>\n\
                                        <i class='fa fa-exclamation-triangle col-md-1' style='font-size: 2rem'></i>\n\
                                        <p class='col-md-11'>" + _lang.periodWithNoTimeEntries.sprintf([diffDays, jQuery("#user-lookup", container).val()]) + "</p>\n\
                                        <p class='offset-md-1 col-md-11'>" + _lang.timeEntryLoggingRuleMessage + "</p>\n\
                                    </div>\n\
                                </div>\n\
                                <div class='modal-footer'>\n\
                                    <span class='loader-submit'></span>\n\
                                    <button id='settings-btn' type='button' class='btn link-style-with-underline hide'>" + _lang.goToSettings + "</button>\n\
                                    <button id='cancel-btn' type='button' class='btn link-style-with-underline'>" + _lang.cancel + "</button>\n\
                                    <button id='add-btn' type='button' class='btn btn-save btn-add-dropdown modal-save-btn'>" + _lang.addBulkTimeEntires + "</button>\n\
                                </div>\n\
                            </div>\n\
                        </div>\n\
                    </div>\n\
                </div>").appendTo("body");
        if (allowViewSettings) {
            jQuery("#settings-btn", timeEntryRuleConfirmation).removeClass("hide");
            jQuery("#cancel-btn", timeEntryRuleConfirmation).addClass('hide');
        }
        initializeModalSize(timeEntryRuleConfirmation, 0.35, 0.15);
        jQuery(".modal", timeEntryRuleConfirmation).modal({
            keyboard: false,
            backdrop: "static",
            show: true
        });
        jQuery(document).keyup(function (e) {
            if (e.keyCode == 27) {
                jQuery(".modal", timeEntryRuleConfirmation).modal("hide");
            }
        });
        jQuery('.modal', timeEntryRuleConfirmation).on('hidden.bs.modal', function () {
            destroyModal(jQuery(timeEntryRuleConfirmation));
        });
        jQuery("#add-btn", timeEntryRuleConfirmation).click(function () {
            if (action === 'add') {
                jQuery("#log-date", container).val(response.dates.last_date);
                jQuery("#repeat-until-date-input", container).val(response.dates.log_date);
                jQuery("#is-repeat", container).prop( "checked", true );
                timeLogsDialog.repeatInput("#is-repeat");
            } else {
                jQuery(".modal", '#time-log-dialog').modal("hide");
                jQuery('.modal-backdrop').remove();
                logActivityDialog(false, {}, false, false, false, response.dates);
            }
            jQuery(".modal", timeEntryRuleConfirmation).modal("hide");
        });
        jQuery("#settings-btn", timeEntryRuleConfirmation).click(function () {
            window.open("system_preferences#SystemValues", '_blank').focus();
        });
        jQuery("#cancel-btn", timeEntryRuleConfirmation).click(function () {
            jQuery(".modal", timeEntryRuleConfirmation).modal("hide");
        });
    }
}
////for milestones 
function collapseDownUp(id , iconRotateId , speed) {
    speed = speed || 'fast'
    if(!jQuery('#' + iconRotateId).hasClass('rotate-origin')) {
        jQuery('#' + iconRotateId ).addClass('rotate-origin');
    } else {
        jQuery('#' + iconRotateId).removeClass('rotate-origin');
    }
    jQuery('#' + id).slideToggle(speed)
}

//add correspondence
 function loadCorrespondenceForm() {

    jQuery.ajax({
        url: getBaseURL() + 'front_office/add',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            jQuery('#correspondence-dialog-container').remove(); // Remove previous instance
            if (response.html) {
                 var correspondenceDialogId = '#correspondence-dialog-container';
                 // Append new modal container to body
                 jQuery('<div id="correspondence-dialog-container"></div>').appendTo("body");
                  var correspondenceDialog = jQuery(correspondenceDialogId);

                correspondenceDialog.html(response.html);
                correspondenceAddFormEvents(correspondenceDialog);

                  var correspondence_id = jQuery("#id", correspondenceDialog).val();
                jQuery('.modal').on('hidden.bs.modal', function () {
                    destroyModal(correspondenceDialog);
                });
                
                jQuery("#correspondence-form-submit", correspondenceDialog).click(function () {
                    correspondenceFormSubmit(correspondenceDialog, correspondence_id);
                });
                jQuery(correspondenceDialog).find('input').keypress(function (e) {
                    if (e.which == 13) {// Enter pressed?
                        correspondenceFormSubmit(container, id);
                    }
                });

                // Show the modal inside the returned HTML... done above already
              //  jQuery('#correspondenceModal').modal('show');
            } else if (typeof response.error !== 'undefined' && response.error) {
                pinesMessageV2({ ty: 'error', m: response.error });
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function correspondenceAddFormEvents(container) {
      // Initialize datepicker for date inputs
     setDatePicker('#form-document-date', container);
        setDatePicker('#form_date_recieved', container);
        setDatePicker('#form-date_dispatched', container);
        setDatePicker('#form-due_date', container);
        setDatePicker('#form-record_date', container);
        //initialize commondialog ecents
         commonModalDialogEvents(container);
        initializeModalSize(container);
      
        senderInitialization(container);
        recipientInitialization(container, { 'onselect': onCorrespondenceAddresseeTypeChange });
    // 

    jQuery('.select-picker',container).selectpicker();
    lookUpUsers(jQuery('#assignedToLookUp', container), jQuery('#assignedToId', container), 'assigned_to', jQuery('.assignee-container', container), container);
    jQuery('#assignedToId', container).change(function () {
        if (jQuery('#user-relation', container).val() > 0 && jQuery('#assignedToId', container).val() > 0 && jQuery('#user-relation', container).val() !== jQuery('#assignedToId', container).val()) {
            pinesMessage({ ty: 'warning', m: _lang.feedback_messages.assignmentChangeForAssignee });
        }
    });
}

/*
 * Submit correspondence form.
 * @param {jQuery} container - The form/modal container
 * @param {function} callback - Optional callback after success
 */
function correspondenceFormSubmit(container, id, callback) {
    callback = callback || false;
   // var formData = jQuery("form", container).serialize();
     var formData = new FormData(document.getElementById(jQuery("form#correspondence-form", container).attr('id')));
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: getBaseURL() + 'front_office/add',
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.addedSuccessfully || 'Correspondence added successfully.' });
                if (typeof callback === 'function') callback(response, container);
            } else if (typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                displayValidationErrors(response.validationErrors, container);
            } else if (response.message) {
                pinesMessage({ ty: 'error', m: response.message });
            }
        },
        complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
   

//initialize sender
/**
 * Initialize sender autocomplete for the correspondence form.
 * Usage: call senderInitialization(jQuery('#correspondence-form'));
 * @param {jQuery} container - The form container (e.g., $('#correspondence-form'))
 */
function senderInitialization(container, callback) {
    callback = callback || false;
    var containerId = '#' + container.attr('id');
    // Store lookup type for future use if needed
    companyContactFormMatrix = companyContactFormMatrix || {};
    companyContactFormMatrix.commonLookup = companyContactFormMatrix.commonLookup || {};
    companyContactFormMatrix.commonLookup[containerId] = {
        "lookupType": jQuery("select#sender-type", container).val(),
    };
    var lookupDetails = {
        'lookupField': jQuery('#lookup-sent-by', container),
        'hiddenInput': jQuery('#sent-by', container),
        'errorDiv': 'sender_id',
        'resultHandler': setDataToSenderField,
        'callback': callback
    };
    companyContactFormMatrix.commonLookup[containerId].callback = callback;
    lookupCompanyContactType(lookupDetails, container);
    jQuery('#sender-type', container).change(function () {
        // Optionally, you can re-initialize the lookup if sender type changes
        lookupCompanyContactType(lookupDetails, container);
    });
}

/**
 * Set the selected sender data to the sender field in the correspondence form.
 * @param {object} record - The selected sender record from autocomplete.
 * @param {jQuery} container - The form container.
 */
function setDataToSenderField(record, container) {
    var containerId = '#' + jQuery(container).attr('id');
    var senderName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#sent-by', container).val(record.id);
    jQuery('#lookup-sent-by', container).val(senderName);
    // Optionally, trigger any additional logic after selection
    if (typeof companyContactFormMatrix !== 'undefined' && companyContactFormMatrix.commonLookup && companyContactFormMatrix.commonLookup[containerId] && typeof companyContactFormMatrix.commonLookup[containerId].callback === 'function') {
        companyContactFormMatrix.commonLookup[containerId].callback("", record, container);
    }
}
/**
 * Initialize recipient autocomplete for the correspondence form.
 *  Usage: call recipientInitialization(jQuery('#correspondence-form'));
 * @param {jQuery} container - The form container (e.g., $('#correspondence-form')) 
 * */
function recipientInitialization(container, callback) {
    callback = callback || false;
    var containerId = '#' + container.attr('id');
    // Store lookup type for future use if needed
    companyContactFormMatrix = companyContactFormMatrix || {};
    companyContactFormMatrix.commonLookup = companyContactFormMatrix.commonLookup || {};
    companyContactFormMatrix.commonLookup[containerId] = {
        "lookupType": jQuery("select#addressee-type", container).val(),
    };
    var lookupDetails = {
        'lookupField': jQuery('#addressee-lookup', container),
        'hiddenInput': jQuery('#addressee_id', container),
        'errorDiv': 'addressee_id',
        'resultHandler': setDataToRecipientField,
        'callback': callback
    };
    companyContactFormMatrix.commonLookup[containerId].callback = callback;
    lookupCompanyContactType(lookupDetails, container);
    jQuery('#addressee-type', container).change(function () {
        // Optionally, you can re-initialize the lookup if recipient type changes
        lookupCompanyContactType(lookupDetails, container);
    });
}
/**
 * Set the selected recipient data to the recipient field in the correspondence form.
 * @param {object} record - The selected recipient record from autocomplete.
 *  * @param {jQuery} container - The form container.
 *  */
function setDataToRecipientField(record, container) {
    var containerId = '#' + jQuery(container).attr('id');
    var recipientName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#addressee_id', container).val(record.id); //put the ID in the hidden input
   
    jQuery('#addressee-lookupp', container).val(recipientName); // Set the name in the lookup field

    // Optionally, trigger any additional logic after selection
    if (typeof companyContactFormMatrix !== 'undefined' && companyContactFormMatrix.commonLookup && companyContactFormMatrix.commonLookup[containerId] && typeof companyContactFormMatrix.commonLookup[containerId].callback === 'function') {
        companyContactFormMatrix.commonLookup[containerId].callback("", record, container);
    }
}
function onCorrespondenceAddresseeTypeChange(events, data, container) {
    var lookupType = jQuery("select#addressee-type", container).val();
    if (data.id > 0) {
        if (jQuery('#clientLinkId', container).length) {
            var clientHref = '';
            if (lookupType == 'contact') {
                clientHref = 'contacts/edit/';
            } else {
                if (data.category === 'Internal') {
                    clientHref = 'companies/tab_company/';
                } else if (ui.item.record.category === 'Group') {
                    jQuery('#clientLinkId', container).addClass('d-none');
                }
            }
            if (data.category !== 'Group') {
                jQuery('#clientLinkId', container).attr('href', getBaseURL() + clientHref + data.id).removeClass('d-none');
            }
        }
    }
}

function suretyForm(contractId, suretyId,mode,withinContract) {
   
    suretyId = suretyId || false;
    data = [{name: "contract_id", value: contractId}];
      data.push({name: 'mode', value:mode });
    if (suretyId) {
        data.push({name: "suretyId", value: suretyId});
    }
  
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'contracts/related_sureties/'+contractId,
        type: 'GET',
        data: data,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#surety-form-container').length <= 0) {
                    jQuery('<div id="surety-form-container"></div>').appendTo("body");
                    var form = jQuery('#surety-form-container');
                    form.html(response.html);
                    jQuery('.select-picker', form).selectpicker();
                  
                    commonModalDialogEvents(form);
                    initializeModalSize(form, 0.5, 0.6);
                    jQuery("#form-submit", form).click(function () {
                        suretyFormFormSubmit(form, withinContract,contractId);
                    });
                    jQuery(form).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            suretyFormFormSubmit(form, withinContract,contractId);
                        }
                    });
                    
                }
                if (response.notification_available) {
                    notifyMeBefore(jQuery('#milestone-form-container'));
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function suretyFormFormSubmit(form, withinContract,contract_id) {
    const rawForm = jQuery("form#suretyBondForm", form)[0];
    const formData = new FormData(rawForm);

    const submitButton = form.find('#form-submit');
    submitButton.prop('disabled', true).text('Saving...');

    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_sureties',
        type: 'POST',
        data: formData,
        processData: false,     // required for file upload
        contentType: false,     // required for file upload
        dataType: 'json',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function(response) {
            if (response.status === 'success') {
                if (withinContract) {
                 pinesMessageV2({ ty: 'success', m: response.message || 'Surety bond saved successfully.' });
                relatedSuretiesTab(contract_id);
                }
               jQuery(".modal", form).modal("hide");
            } else {
                displayValidationErrors(response.validation_errors, form);
            }

        },
        error: defaultAjaxJSONErrorsHandler,
        complete: function() {
            jQuery('#loader-global').hide();
            submitButton.prop('disabled', false).text('Save Surety Bond');
        }
    });
}
function showLoader(show){
    if(!show){
        jQuery("#loader-global").hide();
    }else
    jQuery("#loader-global").show();
}
/*
for autocompletion on fields from the database with controller/autocomplete
*/
function initializeAutocompleteField(config) {
        // Destructure configuration for easy access
        const {
            inputSelector,
            hidden_idSelector,
            controller,
            nameProperty
        } = config;

        // 1. Initialize Bloodhound Source
        var bloodhoundSource = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace(nameProperty || 'name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                // Dynamically construct the URL using the controller path
                url: getBaseURL() + controller + "/autocomplete?term=%QUERY",
                wildcard: '%QUERY'
            }
        });

        // 2. Initialize Typeahead
        jQuery(inputSelector).typeahead(null, {
            name: controller, // Use the controller name for the Typeahead dataset name
            display: nameProperty || 'name', // Use 'name' as default display property
            source: bloodhoundSource,
            templates: {
                empty: [
                    '<div class="m-2">',
                    // Attach a common class and store necessary data attributes for the click handler
                    ' <i role="button" class="tt-quick-add" data-controller="' + controller + '" data-module="' + controller + '">No matches found.Click here to add</i>',
                    '</div>'
                ].join('\n'),
                suggestion: function (data) {
                    // Ensure the suggestion displays the correct property
                    return '<div><strong>' + data[nameProperty || 'name'] + '</strong></div>';
                }
            }
            // 3. Attach Event Listeners
        }).on('typeahead:select', function (event, suggestion) {
            // When selected, update the hidden ID field
            jQuery(hidden_idSelector).val(suggestion.id);
        }).on('typeahead:autocomplete', function (event, suggestion) {
            // Update hidden ID when autocompleted
            jQuery(hidden_idSelector).val(suggestion.id);
        }).on('change', function () {
            // Clear the hidden ID if the user types something that's not a suggestion
            if (!jQuery(this).typeahead('val')) {
                jQuery(hidden_idSelector).val('');
            }
        });
    }
