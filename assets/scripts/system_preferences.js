
var cloudInstallation;
var systemValuesValidationRules = {
    'passwordDisallowedPrevious': 'validate[custom[number]]',
    'passwordForceChange': 'validate[custom[number]]',
    'passwordLockout': 'validate[custom[number]]',
    'passwordMinimumLength': 'validate[required, custom[number], min[1], max[16]]',
    'shareholderVoteFactor': 'validate[custom[number]]',
    'shareholderVoteYear': 'validate[custom[number]]',
    'businessDayEquals': 'validate[required, min[1], custom[number], max[24]]',
    'businessWeekEquals': 'validate[required, min[1], custom[number], max[7]]',
    'reminderIntervalDate': 'validate[required, min[0], custom[number], max[365]]',
    'reminderShowList': 'validate[required, min[3], custom[number], max[8]]',
    'notificationShowList': 'validate[required, min[3], custom[number], max[8]]',
    'notificationIntervalDate': 'validate[required, min[1], custom[number], max[999]]',
    'sysDaysOff': 'validate[required]',
    'warningMessageOnLoginPage': 'validate[maxSize[400]]',
    'caseMaxOpponents': 'validate[required, min[1], custom[integer], max[100]]'
};
function saveSystemDefaultValue(jsIdKey, url,currentVal) {
    data =[];
    url = undefined === url || '' === url ? getBaseURL() + 'system_preferences/save_system_value' : url;
    var sysKey = jQuery('#' + jsIdKey).val();
    var sysVal = jQuery('#value' + jsIdKey).val();
    var sysGrp = jQuery('#groupNameOf' + jsIdKey).val();
    var valAsArr = jQuery(".keyName" + jsIdKey);
    data['url'] = url;
    dataArr = {keyName: sysKey, keyValue: sysVal, groupName: sysGrp};
    if (valAsArr.length > 1) {
        dataArr.keyValue = {};
        valAsArr.each(function (index, element) {
            var name = element.name.substring(element.name.lastIndexOf('[') + 1, element.name.lastIndexOf(']'));
            dataArr.keyValue[name] = element.value;
        });
    }
    var fieldDataNotValid = jQuery("#value" + jsIdKey).validationEngine('validate');
    if (jsIdKey === 'allowTimeEntryLoggingRule') {
        var sysVal2 = jQuery('#value2' + jsIdKey).val();
        dataArr.keyValue = sysVal + ', ' + sysVal2;
        fieldDataNotValid = fieldDataNotValid || jQuery('#value2' + jsIdKey).validationEngine('validate');
    }
    data['dataArr'] = dataArr;
    if (jsIdKey === 'makerCheckerFeatureStatus' && jQuery("#value" + jsIdKey).val() != 'yes' && hasPendingChangesInUsersGroupsData) {
        pinesMessage({ty: 'warning', m: _lang.disableMakerCheckerControlMsg});
    } else if(jsIdKey === 'userGroupsAppearInUserRatePerHourGrid'){
        has_related_user_group(data.dataArr.keyValue,function (response) {
            if(response){
                confirmationDialog('confim_delete_action', {resultHandler: submitSystemDefaultValue, parm: data});
            }else {
                _submitSystemDefaultValue(fieldDataNotValid,sysKey,sysVal,data);
            }
        });
    } else {
        _submitSystemDefaultValue(fieldDataNotValid,sysKey,sysVal,data);
    }
}

function _submitSystemDefaultValue(fieldDataNotValid,sysKey,sysVal,data) {
    if (!fieldDataNotValid) {
        var currentVal = jQuery('#groupNameOfexpenseStatus').closest('tr').find('.btn').attr('currentValue');
        if(sysKey === "expenseStatus" && ( currentVal ==="open" && sysVal !== "open")){
            confirmationDialog('confirmation_expense_status',{resultHandler: submitSystemDefaultValue, parm: data});
        }else {
            submitSystemDefaultValue(data);
        }
    }
}

function submitSystemDefaultValue(data) {
    dataArr = data.dataArr;
    var initialExpenseValue = jQuery('#valueexpenseStatus').val();
    url = data.url;
    jQuery.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        data: {systemValue: dataArr},
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.status) {
                case 202:	// saved successfuly
                    // hide passwords in web form
                    if(dataArr['keyName'] == 'outgoingMailSmtpPass'){ // SMTP password
                        jQuery('#valueoutgoingMailSmtpPass', jQuery('#systemPreferencesForm')).val('');
                    }
                    if(dataArr['keyName'] == 'password'){ // Active Directory password
                        jQuery('#valuepassword', jQuery('#systemPreferencesForm')).val('');
                    }
                    ty = 'success';
                    if(response.message){
                        m = response.message;
                    }else{
                        m = _lang.done;
                     }
                    pinesMessage({ty: ty, m: m, d: 1200});
                   if(response.result && response.result.keyName == 'expenseStatus'){
                       if(response.result.keyValue !='open'){
                           resetExpenseNotificationSystemParams(response.result.keyValue);
                       }else{
                           checkInitialExpenseForNotification(response.result.keyValue);
                       }
                   }
                    break;
                case 101:	// could not save record
                    m = response.message ? response.message : _lang.feedback_messages.updatesFailed;
                    pinesMessage({ty: ty, m: m, d: 1200});
                    break;
                case 102:	// could not save record
                    location.reload(true);
                    break;
                case 500:
                    window.location = getBaseURL('money') + 'setup/rate_between_money_currencies';
                    break;
                default:
                    break;
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function resetExpenseNotificationSystemParams(value) {
    jQuery.ajax({
        url: getBaseURL() + 'system_preferences/reset_expense_notification_system_params',
        type: 'POST',
        dataType: 'JSON',
        data: {},
        success: function (response) {
            var ty = 'error';
            var m = '';
            if (response.result) {
                checkInitialExpenseForNotification(value);
            }else{
                    m = _lang.feedback_messages.updatesFailed;
                    pinesMessage({ty: ty, m: m});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function makeFormValidationRules() {
    for (i in systemValuesValidationRules) {
        jQuery('#value' + i).attr('data-validation-engine', systemValuesValidationRules[i]);
    }
    jQuery("form#systemPreferencesForm").validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
}
function addSuggestionsToSearch() {
    var lookupSearchTxtBx = jQuery('input.lookup.search:first', 'form#systemPreferencesForm').attr('placeholder', _lang.search).addClass('form-control');
    var sysValsTable = jQuery('table.filterable', 'form#systemPreferencesForm');
    var suggestions = [];
    jQuery('td.labelsText', sysValsTable).each(function (index, element) {
        suggestions.push(jQuery(element).text().trim());
    });
    lookupSearchTxtBx.autocomplete({source: suggestions, minLength: 1, close: function (event, ui) {
            lookupSearchTxtBx.trigger('keyup')
        }});
}
function testEmailConfiguration() {
    jQuery.ajax({
        url: getBaseURL() + 'system_preferences/email_test_configurations',
        type: 'POST',
        dataType: 'JSON',
        data: {
            smtpEncryption: jQuery('#valueoutgoingMailSmtpEncryption').val(),
            smtpPort: jQuery('#valueoutgoingMailSmtpPort').val(),
            smtpHost: jQuery('#valueoutgoingMailSmtpHost').val(),
            timeOut: jQuery('#valueoutgoingMailTimeout').val(),
            smtpUser: jQuery('#valueoutgoingMailSmtpNameUser').val(),
            smtpPass: jQuery('#valueoutgoingMailSmtpPass').val(),
            smtpRequiresAuth: jQuery('#valueoutgoingMailSmtpPasRequiresAuthentication').val()
        },
        beforeSend: function () {
            jQuery('#testEmailConf').html('');
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            switch (response.status) {
                case false:
                    jQuery('#testEmailConf').removeClass('connectionSucceeded').addClass('connectionFailed').html(_lang.invalidConfigurations);
                    break;
                case true:
                    jQuery('#testEmailConf').removeClass('connectionFailed').addClass('connectionSucceeded').html(_lang.validConfigurations);
                    break;
                default:
                    jQuery('#testEmailConf').addClass('d-none');
                    break;
            }
        }, error: defaultAjaxJSONErrorsHandler
    });
}

jQuery(document).ready(function () {
    var sysValuesTable = document.getElementById('sysValuesTable');
    makeFormValidationRules();
    jQuery("form#systemPreferencesForm").bind("submit", function () {
        jQuery(this).validationEngine('validate');
    });

    jQuery('select', '#systemPreferencesForm').not('.selectpicker','#systemPreferencesForm').chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.select,
        width: "320px",
    }).change(function (e) {
                var selectId = jQuery(this).attr('id');
                var chosenChoices = jQuery('ul.chosen-choices', '#' + selectId + '_chosen');
                jQuery('li.search-choice :last-child', chosenChoices).eq(-1).parent().attr('class', 'search-choice ui-corner-left');
            });

    jQuery(".search","#sysConfTabs").keyup(function(){
         jQuery('td.labelsText', "#sysConfTabs").each(function (index, element) {
             var text = jQuery(this).text();
            jQuery(this).text(text);
        });
    });
    jQuery('#valuesysDayStartOn, #valuesysDayEndOn').timepicker({timeFormat: 'H:i'});
    jQuery("#sysConfTabs").tabs().addClass("ui-tabs-vertical ui-helper-clearfix no-margin-left");
    jQuery("#sysConfTabs li").removeClass("ui-corner-top").addClass("ui-corner-left");
    var systemPreferencesForm = jQuery('#systemPreferencesForm');
    var providerGroup = jQuery('#valueproviderGroupId', systemPreferencesForm);
    var caseAssignee = jQuery('#valueproviderGroupIdCaseAssignee', systemPreferencesForm);
    providerGroup.change(function () {
        if (this.value != '') {
            reloadUsersListByProviderGroupSelected(this.value, caseAssignee);
        } else {
            caseAssignee.html('').trigger("chosen:updated");
        }
    });
    var smtpRequiresAuth = jQuery('#valueoutgoingMailSmtpPasRequiresAuthentication', systemPreferencesForm);
    var smtpPwd = jQuery('#valueoutgoingMailSmtpPass', systemPreferencesForm);
    requiresAuthEvents(smtpRequiresAuth, smtpPwd);
    var testEmailConfig = '';
    testEmailConfig += '<tr><td>' + _lang.testOutgoingMailLabel + '</td>';
    testEmailConfig += '<td><span connectionSucceeded" id="testEmailConf"></span></td>';
    testEmailConfig += '<td><input type="button" class="btn btn-default btn-info btn-sm" onclick="testEmailConfiguration();" value="' + _lang.test + '" tabindex="-1" name="btnSave"></td></tr>';
    jQuery('#OutgoingMail > table tbody:last').append(testEmailConfig);
    if(cloudInstallation){
        // hide on-server parameters
        jQuery('#valueloggedOutPeriod').parent().parent().addClass('d-none');
    }
    checkInitialExpenseForNotification();
});
function requiresAuthEvents(requiresAuth, passwordField){
    requiresAuth.change(function () {
        if(this.value == 'no'){
            passwordField.val('').attr('style','pointer-events:none').prop('disabled',true);
        }else{
            passwordField.attr('style','').prop('disabled',false);
        }
    });
    // when laoding the page and checking the saved value in DB to disable/enable the password field
    if(requiresAuth.val() == 'no'){
        passwordField.val('').attr('style','pointer-events:none').prop('disabled',true);
    }else{
        passwordField.attr('style','').prop('disabled',false);
    }
}
function checkUsernamesCompatibility()
{
    var loginWithoutDomainInput = jQuery("#valueloginWithoutDomain", jQuery("#systemPreferencesForm"));
    if (loginWithoutDomainInput.val() == true) {
        var randomNumber = Math.round(Math.random() * (upperBound - lowerBound) + lowerBound);
        jQuery.ajax({
            url: getBaseURL() + 'system_preferences/check_usernames_compatibility/',
            dataType: 'JSON',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.html) {
                    var conflictingUsersDialogId = "#conflicting-user-dialog-" + randomNumber;
                    if (jQuery(conflictingUsersDialogId).length <= 0) {
                        jQuery('<div id="conflicting-user-dialog-' + randomNumber + '"></div>').appendTo("body");
                        var conflictingUsersDialog = jQuery(conflictingUsersDialogId);
                        conflictingUsersDialog.html(response.html);
                        initializeModalSize(conflictingUsersDialog);
                        jQuery('.modal', conflictingUsersDialog).modal({
                            keyboard: false,
                            backdrop: 'static',
                            show: true
                        }).on('shown.bs.modal', function () {
                            loginWithoutDomainInput.val(0).trigger("chosen:updated");
                        });
                        jQuery(document).keyup(function (e) {
                            if (e.keyCode == 27) {
                                jQuery('.modal', conflictingUsersDialog).modal('hide');
                            }
                        });
                        jQuery('.modal-body').on("scroll", function() {
                            jQuery('.bootstrap-select.open').removeClass('open');
                        });
                    }
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function checkInitialExpenseForNotification(initialExpenseValue) {
    if(!initialExpenseValue){
         initialExpenseValue = jQuery('#valueexpenseStatus').val();
    }
    jQuery('#groupNameOfexpenseStatus').closest('tr').find('.btn').attr('currentValue',initialExpenseValue);
    if(initialExpenseValue && initialExpenseValue == 'open' ){
        jQuery('#notify-user-group-container, #notify-users-container').attr('style','');
        jQuery('#valuenotifyUserGroupExpense,#valuenotifyUsersExpense').closest('tr').find('.btn').prop('disabled',false);
        jQuery('#valuenotifyUserGroupExpense_chosen, #valuenotifyUsersExpense_chosen').children('ul.chosen-choices').css('background','#fff');
    }else{
        jQuery('#notify-user-group-container, #notify-users-container').attr('style','pointer-events:none');
        jQuery('#valuenotifyUserGroupExpense,#valuenotifyUsersExpense').closest('tr').find('.btn').prop('disabled',true);
        jQuery('#valuenotifyUserGroupExpense_chosen, #valuenotifyUsersExpense_chosen').children('ul.chosen-choices').css('background','#eee');
        jQuery('#valuenotifyUserGroupExpense_chosen, #valuenotifyUsersExpense_chosen').find('ul.chosen-choices li:not(:last-child)').remove();
        jQuery('#valuenotifyUserGroupExpense_chosen, #valuenotifyUsersExpense_chosen').find('ul.chosen-choices li:first-child input').val(_lang.select);
        jQuery("#valuenotifyUserGroupExpense,#valuenotifyUsersExpense").val('').trigger("chosen:updated");
    }
}

/**
 * @function get all group ids from submit form of save all preferences form
 * @return {Array} group ids
 */
function getGroupIdsFromSaveAllPreferencesForm() {
    let systemPreferencesForm = jQuery('#systemPreferencesForm');
    let groupIds = [];
    let formData = systemPreferencesForm.serializeArray();
    jQuery.each(formData,function () {
        if(this.name === 'systemValues[userGroupsAppearInUserRatePerHourGrid][keyValue][]'){
            if(this.value){
                groupIds.push(this.value)
            }
        }
    });
    return groupIds
}
/**
 * @function validate save all Money preferences
 */
function validateSaveAllMoneyPreferences(){
    let groupIds = getGroupIdsFromSaveAllPreferencesForm();
    has_related_user_group(groupIds,function (response) {
        if(response){
            confirmationDialog('confim_delete_action', {resultHandler: _saveAllMoneyPreferences, parm: groupIds});
        }else {
            _saveAllMoneyPreferences();
        }
    });
}

/**
 * @function save all Money preferences
 */
function _saveAllMoneyPreferences() {
    let systemPreferencesForm = jQuery('#systemPreferencesForm');
    if (systemPreferencesForm.validationEngine('validate')){
        systemPreferencesForm.submit();
    }
}

/**
 * @function save all Money preferences
 */
function saveAllMoneyPreferences() {
    validateSaveAllMoneyPreferences();
}

/**
 * @function check if one of no included group has a user rate
 * @param groupIds
 * @param callback
 * @return {boolean}
 */
function has_related_user_group(groupIds,callback) {
    let hasRelatedUserGroupUrl = getBaseURL().concat('modules/money/money_preferences/').concat('has_related_user_group');
    let result = false;
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: hasRelatedUserGroupUrl,
        data: {'group_ids': groupIds},
        success: function (response) {
            callback(response);
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
