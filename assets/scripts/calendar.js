jQuery(document).ready(function () {
    loadUserSpectrumColor();
    loadCalendarEventsHandler();
    scheduler.init('scheduler_here', selectedDate ? new Date(selectedDate) : new Date(), mode);
    /* loading data from the server */
    if (!jQuery('input', '#user_' + authIdLoggedIn).is(':checked') || !calendarIntegrationEnabled) {
        scheduler.load(getBaseURL() + 'calendars/view');
    }
    jQuery('#refresh_dhx_scheduler').click(function() {
        var selected = new Array();
        jQuery('#attendees input:checked').each(function () {
            selected.push(jQuery(this).attr('value'));
        });
        setCalendarPreferences('usersIds', selected);
        scheduler.clearAll();
        scheduler.load(getBaseURL() + 'calendars/view?min_date=' + convert(scheduler._min_date).date + '&max_date=' + convert(scheduler._max_date).date + '&users[]=' + selected);
    });
    if (showCalendarIntegrationPopup) {
        integrationPopup();
    }
});

scheduler.config.show_loading = true;
scheduler.config.xml_date = "%Y-%m-%d %H:%i";
scheduler.config.icons_select = ["icon_edit", "icon_delete"];
scheduler.config.icons_edit = ["icon_details", "icon_save", "icon_cancel"];
scheduler.config.separate_short_events = true;
scheduler.config.max_month_events = 5;
scheduler.config.mark_now = true;
scheduler.config.scroll_hour = new Date().getHours();
scheduler.config.time_step = 30;
scheduler.config.event_duration = 60;
scheduler.config.auto_end_date = true;
scheduler.config.year_y = 3;
var calendarPalette = [
	'384c81', '9aabd6', '8e725d', 'd95459', '4f2b4f',
	'513b2d', 'bec3c7', '16a086', '27ae61', '262626',
	'2d3e50', 'd5c395', '662722', 'd55c9f', '8fb021',
	'5b48a2', '2e5037', '7e8c8d', '2a80b9', '346272',
	'8f44ad', 'ffa800', 'd55401', 'c1392b', '3598db'
];
var spectrumPaletteOptions = {
    change: function (color) {
        getCalendarUserTheme(jQuery(this).parent().attr('id').substr(5), String(calendarPalette.indexOf(color.toHex())));
        updateEventsTheme();
    },
    palette: [
        [calendarPalette[0], calendarPalette[1], calendarPalette[2], calendarPalette[3], calendarPalette[4]],
        [calendarPalette[5], calendarPalette[6], calendarPalette[7], calendarPalette[8], calendarPalette[9]],
        [calendarPalette[10], calendarPalette[11], calendarPalette[12], calendarPalette[13], calendarPalette[14]],
        [calendarPalette[15], calendarPalette[16], calendarPalette[17], calendarPalette[18], calendarPalette[19]],
        [calendarPalette[20], calendarPalette[21], calendarPalette[22], calendarPalette[23], calendarPalette[24]]
    ],
    showPaletteOnly: true,
    showPalette: true,
    theme: 'sp-light pull-right inline'
}
scheduler.templates.year_tooltip = function(start,end,ev){
    return "<a onclick=openEditInYearView('"+ev.id+"')>"+ev.text+"</a>";
};
scheduler.attachEvent("onEmptyClick", function (date, e){
    if(mode == 'year'){
        scheduler.init('scheduler_here',date,"week");
    }
});
scheduler._click.buttons.details = function (id) {
        var event = scheduler.getEvent(id);
        if(mode != 'year'){
            scheduler.getEvent(id).text = jQuery('textarea.dhx_cal_editor').val();
        }
        scheduler.updateEvent(id); //update the event with the new title
        meetingForm(event);
};
scheduler._click.buttons.edit = function (id) {
    meetingForm(scheduler.getEvent(id));
};
scheduler._click.buttons.delete = function (id) {
    confirmationDialog('confim_delete_event', {resultHandler: deleteEvent, parm: id});
}

scheduler.attachEvent("onXLE", function () {
    updateEventsTheme();
    return true;
});
scheduler.attachEvent("onClick", function (id,e) {
    if(jQuery(e.target).attr("date")){
        var event = scheduler.getEvent(id);
        scheduler.init('scheduler_here',jQuery(e.target).attr("date"),"week");
    }else{
        if (!checkIfEventIsAccessible(id)) {
            return false;
        }
        return true;
    }
});
scheduler.attachEvent("onDblClick", function (id) {
    if (!checkIfEventIsAccessible(id)) {
        return false;
    }
    return true;
});
scheduler.attachEvent("onEventAdded", function (id, ev) {
    quickAddMeeting(ev);
    return true;
});
scheduler.attachEvent("onEventChanged", function (id, ev) {
    if (!scheduler.getEvent(id).ev_id) {
        quickAddMeeting(ev);
    } else {
        quickEditMeeting(id);
    }
    return true;
});
scheduler.attachEvent("onBeforeLightbox", function (id) {
    var event = scheduler.getEvent(id);
    meetingForm(event);
    return false;
});
scheduler.attachEvent("onBeforeDrag", function (event_id, mode) {
    if (mode === 'resize' && jQuery('textarea.dhx_cal_editor').val()) {
        scheduler.getEvent(event_id).text = jQuery('textarea.dhx_cal_editor').val();
        scheduler.updateEvent(event_id);
    }
    if (mode !== 'create') {
        return checkIfEventIsAccessible(event_id) ? true : false;
    }
    return true;
});
scheduler.attachEvent("onViewChange", function (new_mode, new_date) {
    if (new_mode !== mode) {
        setCalendarPreferences('view', new_mode);
    }
    if (jQuery('input', '#user_' + authIdLoggedIn).is(':checked') && calendarIntegrationEnabled) {
        scheduler.clearAll();
        scheduler.load(getBaseURL() + 'calendars/view?min_date=' + convert(scheduler._min_date).date + '&max_date=' + convert(scheduler._max_date).date);
    }

});
function loadUserSpectrumColor() {
    var op = getCalendarPreferences();
    var usersListInPreferences = op.usersIds;
    usersListInPreferences = String(usersListInPreferences).split(",");
    if (usersListInPreferences != '') {
        for (var j = 0; j < usersListInPreferences.length; j++) {
            jQuery('.color-palette', jQuery('input[value=' + usersListInPreferences[j] + ']', '#attendees').attr('checked', 'checked').parent().parent()
                    ).removeAttr('disabled').spectrum(spectrumPaletteOptions).spectrum('set', calendarPalette[getCalendarUserTheme(usersListInPreferences[j])]);
        }
    }
    return usersListInPreferences;
}
function loadCalendarEventsHandler() {
    jQuery('#attendees :checkbox').click(function () {
        $this = jQuery(this);
        if ($this.is(':checked')) {
            jQuery('.color-palette', $this.parent().parent()).spectrum(spectrumPaletteOptions).spectrum("set", calendarPalette[getCalendarUserTheme($this.val())]);
        } else {
            jQuery('.color-palette', $this.parent().parent()).spectrum("destroy");
        }
        var selected = new Array();
        jQuery('#attendees input:checked').each(function () {
            selected.push(jQuery(this).attr('value'));
        });
        setCalendarPreferences('usersIds', selected);
        scheduler.clearAll();
        scheduler.load(getBaseURL() + 'calendars/view?users[]=' + selected);

    });

    jQuery('.user-label', '#attendees').each(function (index, element) {
        jQuery(element).tooltip({
            show: {
                effect: "highlight",
                duration: 1000
            },
            track: true
        });
    });
}
function getCalendarUserTheme(userId, theme) {
    theme = theme || false;
    var calOptions = getCalendarPreferences();
    var usersThemesArr = String(calOptions.calendarUsersThemes).split('**');
    var usersThemes = [];
    for (i in usersThemesArr) {
        if (String(usersThemesArr[i]).length > 2) {
            var usersThemesTmp = String(usersThemesArr[i]).split('*');
            if (usersThemesTmp.length == 2)
                usersThemes[usersThemesTmp[0]] = usersThemesTmp[1];
        }
    }
    if (theme !== false || 'string' != typeof usersThemes[userId]) {
        usersThemes[userId] = theme !== false ? theme : String(Math.floor(Math.random() * calendarPalette.length));
        calOptions.calendarUsersThemes = "";
        for (i in usersThemes)
            calOptions.calendarUsersThemes += String(i) + '*' + String(usersThemes[i]) + '**';
        setCalendarPreferences('calendarUsersThemes', calOptions.calendarUsersThemes.substring(0, calOptions.calendarUsersThemes.length - 2));
    } else {
        usersThemes[userId] = usersThemes[userId];
    }
    return usersThemes[userId];
}
function getCalendarPreferences() {
    var calendarOptions = {};
    var changableDefaults = {
        usersIds: jQuery('#user-auth').val(),
        calendarUsersThemes: ''
    };
    var d;
    d = new Date();
    var constDefaults = {
        url: getBaseURL() + "calendars/load_events"
    };
    if (calendarSettings != null && calendarSettings != '') {
        calendarOptions = parseCalendarPreferences();
    }
    else {
        setCalendarPreferences(changableDefaults);
    }
    return jQuery.extend(constDefaults, changableDefaults, calendarOptions);
}
function parseCalendarPreferences() {
    var calendarOptions = {};
    if (calendarSettings != '' && calendarSettings != null) {
        var storedOptionArr = String(calendarSettings).split('&');
        for (i in storedOptionArr) {
            var calOption = String(storedOptionArr[i]).split('=');
            if (calOption.length == 2) {
                calendarOptions[calOption[0]] = decodeURIComponent(calOption[1]);
            }
        }
        if ('string' == typeof calendarOptions.usersIds) {
            calendarOptions.usersIds = String(decodeURIComponent(calendarOptions.usersIds)).split(',');
        }
        if ('string' == typeof calendarOptions.calendarUsersThemes) {
            calendarOptions.calendarUsersThemes = decodeURIComponent(calendarOptions.calendarUsersThemes);
        }
    }
    return calendarOptions;
}
function setCalendarPreferences(key, value) {
    if ('string' == typeof key) {
        value = value || '';
        var calOptions = parseCalendarPreferences();
        calOptions[key] = value;
    } else {
        var calOptions = key;
    }
    for (i in calOptions) {
        calOptions[i] = encodeURIComponent(String(calOptions[i]));
    }
    calendarSettings = jQuery.param(calOptions);
    jQuery.ajax({
        url: getBaseURL() + 'calendars/view',
        error: defaultAjaxJSONErrorsHandler,
        dataType: 'JSON',
        type: 'POST',
        data: {
            'calendarSettings': calendarSettings
        },
        success: function (response) {
            if (response.result) {
                mode = response.mode.view;
            }
        },
        error: defaultAjaxJSONErrorsHandler
    })
}
function quickAddMeeting(ev) {
    if (meetingFormValidation(ev)) {
        var startDateAndTime = convert(ev.start_date);
        var endDateAndTime = convert(ev.end_date)
        jQuery.ajax({
            data: {type: 'quick_add', start_date: startDateAndTime.date, end_date: endDateAndTime.date, start_time: startDateAndTime.time, end_time: endDateAndTime.time, title: ev.text},
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'calendars/add',
            success: function (response) {
                if (response.result) {
                    if (response.event_id) {
                        ev.ev_id = response.event_id;
                        ev.color = "#" + calendarPalette[getCalendarUserTheme(authIdLoggedIn)];
                        scheduler.updateEvent(ev.id); //update the event with the event id from db
                        if (!jQuery('input', '#user_' + authIdLoggedIn).is(':checked')) {//if logged user not checked=>check it
                            jQuery('input:checkbox', '#user_' + authIdLoggedIn).trigger("click");
                        }
                    }
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.addedMeetingSuccessfully});
                    jQuery('#pendingNotifications').css('display', 'inline-block').text(response.total_notifications);
                } else {
                    var errorMsg = '';
                    for (i in response.validation_errors) {
                        errorMsg += '<li>' + response.validation_errors[i] + '</li>';
                    }
                    if (errorMsg != '') {
                        pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                    }
                    return false;
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
        return true;
    }
    return false;
}
function quickEditMeeting(id) {
    var event = scheduler.getEvent(id);
    if (meetingFormValidation(event)) {
        if (event) {
            var startDateAndTime = convert(event.start_date);
            var endDateAndTime = convert(event.end_date);
            jQuery.ajax({
                data: {type: 'quick_edit', start_date: startDateAndTime.date, end_date: endDateAndTime.date, start_time: startDateAndTime.time, end_time: endDateAndTime.time, title: event.text},
                dataType: 'JSON',
                type: 'POST',
                url: getBaseURL() + 'calendars/edit/' + event.ev_id,
                success: function (response) {
                    if (response.result) {
                        jQuery('#pendingNotifications').css('display', 'inline-block').text(response.total_notifications);
                    } else {
                        var errorMsg = '';
                        for (i in response.validation_errors) {
                            errorMsg += '<li>' + response.validation_errors[i] + '</li>';
                        }
                        if (errorMsg != '') {
                            pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                        }
                        return false;
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
            return true;
        }
    }
    return false;
}
function updateEventsTheme() {
    var usersList = loadUserSpectrumColor();
    var evs = scheduler.getEvents();
    for (var i = 0; i < evs.length; i++) {
        if (usersList != '') {
            for (var j = 0; j < usersList.length; j++) {
                if (evs[i].user_id == usersList[j]) {
                    scheduler.getEvent(evs[i].id).color = "#" + calendarPalette[getCalendarUserTheme(usersList[j])];
                    scheduler.updateEvent(evs[i].id);

                } else {
                    if (evs[i].private == 'yes' && evs[i].user_id !== authIdLoggedIn) {
                        scheduler.getEvent(evs[i].id).text = _lang.busy;

                    }
                }
            }
        }
    }
}
function checkIfEventIsAccessible(id) {
    var ev = scheduler.getEvent(id);
    if (ev.user_id) {
        if (ev.user_id !== authIdLoggedIn && ev.private === 'yes' || ev.user_id !== authIdLoggedIn) {
            return false;
        }
    }
    return true;
}
function  meetingFormValidation(ev) {
    if (!ev.text) {
        alert(_lang.feedback_messages.notAllowedBlankTitle);
        return false;
    }
    if (ev.text.length > 255) {
        dhtmlx.alert(_lang.feedback_messages.titleMaxCharacters);
        return false;
    }
    if (ev.start_date > ev.end_date) {
        dhtmlx.alert(_lang.feedback_messages.meetingDatesRule);
        return false;
    }
    return true;
}
/**
 * open Edit In Year View function
 * @param {int} id 
 */
function openEditInYearView(id){
    if (!checkIfEventIsAccessible(id)) {
        return false;
    }
    meetingForm(scheduler.getEvent(id));
}