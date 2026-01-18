var enableQuickSearch = false;
var usersSearchDataSrc;
var contractSearchGridOptions;
jQuery(document).ready(function () {
    usersSearchDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "users/index",
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
                    animateDropdownMenuInGrids('searchResults', 200);
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    options.filter = getFormFilters();
                    options.returnData = 1;
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                }
                return options;
            }
        },
        schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {type: "integer"},
                    userGroupName: {type: "string"},
                    userGroupDescription: {type: "string"},
                    department: {type: "string"},
                    providerGroup: {type: "integer"},
                    title: {type: "string"},
                    firstName: {type: "string"},
                    lastName: {type: "string"},
                    jobTitle: {type: "string"},
                    isLaywer: {type: "string"},
                    username: {type: "string"},
                    email: {type: "string"},
                    phone: {type: "string"},
                    fax: {type: "string"},
                    mobile: {type: "string"},
                    city: {type: "string"},
                    seniorityLevel: {type: "string"},
                    nationality: {type: "string"},
                    country: {type: "string"},
                    banned: {type: "string"},
                    ban_reason: {type: "string"},
                    status: {type: "string"},
                    overridePrivacy: {type: "string"},
                    last_login: {type: "date"},
                    modified: {type: "date"},
                    userModifiedName: {type: "string"},
                    flagChangePassword: {type: "string"},
                    isAd: {type: "string"},
                    user_code: {type: "string"},
                    userDirectory: {type: "string"},
                    type: {type: "string"},
                }
            },
            parse: function(response) {
                var rows = [];
                if(response.data){
                    var data = response.data;
                    rows = response;
                    rows.data = [];
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        row['userGroupDescription'] = escapeHtml(row['userGroupDescription']);
                        row['overridePrivacy'] = escapeHtml(row['overridePrivacy']);
                        row['userModifiedName'] = escapeHtml(row['userModifiedName']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        pageSize: 20, serverPaging: true, serverFiltering: true, serverSorting: true
    });
    contractSearchGridOptions = {
        autobind: true,
        dataSource: usersSearchDataSrc,
        columns: [
            {field: "id", filterable: false, title: ' ', template: '<div class="dropdown">' + gridActionIconHTML +
                    '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" style="#= (flagNeedApproval == 1 && makerCheckerFeatureStatus) ? \'display: none;\' : \'\'  #" href="' + getBaseURL() + 'users/edit/#= id #">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" style="#= (flagNeedApproval == 1 && makerCheckerFeatureStatus || (status == \'Inactive\')) ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="banUnban(\'#= id #\');">' + _lang.banUnban + '</a>' +
                    '<a class="dropdown-item" style="#= (flagNeedApproval == 1 && makerCheckerFeatureStatus) || (loggedInUser == id) ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="activateDeactivate(\'#= id #\', \'#= status #\');">' + _lang.activateDeactivate + '</a>' +
                    '<a class="dropdown-item" style="#= ((banned == 1)||(status==\'Inactive\')||(flagNeedApproval == 1 && makerCheckerFeatureStatus)) ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="overridePrivacy(\'#= id #\', \'#= overridePrivacy #\');">' + _lang.overridePrivacy + '</a>' +
                    '<a class="dropdown-item" style="#= ((banned == 1)||(status==\'Inactive\')|| isAd == 1 || userDirectory || (flagNeedApproval == 1 && makerCheckerFeatureStatus)) ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="flagUserToChangePassword(\'#= id #\', \'#= addslashes(encodeURIComponent(firstName + \' \' + lastName)) #\');">' + _lang.flagToChangePassword + '</a>' +
                    '<a class="dropdown-item" style="#= ((banned == 1)||(status==\'Inactive\') || !api_is_enabled || (flagNeedApproval == 1 && makerCheckerFeatureStatus)) ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="revokeAPIId(\'#= id #\');">' + _lang.RevokeAPIKey + '</a>' +
                    '<a class="dropdown-item" style="#= (flagNeedApproval == 1 && isUserChecker) ? ( \'\' ) : \'display: none;\' #" href="javascript:;" onclick="checkerApproveChanges(\'#= id #\');">' + _lang.approveChanges + '</a>' +
                    '<a class="dropdown-item" style="#= ((banned == 1)||(status==\'Inactive\')|| isAd == 1 || !active_directory_is_enabled || (flagNeedApproval == 1 && makerCheckerFeatureStatus)) ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="convertLocalUserToAD(\'#= id #\');">' + _lang.convertToActiveDirectory + '</a>' +
                    '<a class="dropdown-item" style="#= ((banned == 1)||(status==\'Inactive\')|| isAd != 1 || !active_directory_is_enabled || (flagNeedApproval == 1 && makerCheckerFeatureStatus)) ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="syncUserByAD(\'#= id #\');">' + _lang.syncUserByActiveDirectory + '</a>' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'user_groups/permissions_list_by_user/#= id #">' + _lang.permissionsList + '</a>' +
                    '</div></div>', width: '70px'},
            {field: "id", filterable: false, title: _lang.id, template: '<a href="' + getBaseURL() + 'users/edit/#= id #" title="' + _lang.viewEdit + '">U#= id-1 #</a><i class="iconLegal iconPrivacyOverrid#= overridePrivacy #"  title="#= overridePrivacy == \"yes\" ? \"' + _lang.overridePrivacyTitleYes + '\" : \"' + _lang.overridePrivacyTitleNo + '\" #"></i>', width: '135px'},
            {field: "firstName", title: _lang.firstName, width: '120px'},
            {field: "lastName", title: _lang.lastName, width: '120px'},
            isCloudInstance ? '' : {field: "username", title: _lang.username, width: '192px'},
            {field: "email", title: _lang.email, width: '192px'},
            {field: "user_code", title: _lang.userFields['user_code'], width: '110px'},
            {field: "userGroupName", title: _lang.user_group, width: '192px'},
            {field: "status", template: "#=  getTranslation(status)#", title: _lang.status, width: '99px'},
            {field: "type", template: "#=  getTranslation(type)#", title: _lang.type, width: '99px'},
            {field: "seniorityLevel", title: _lang.SeniorityLevel, width: '150px'},
            {field: "providerGroup", encoded: false, title: _lang.provider_group_users, width: '192px', template: "#= (providerGroup == null) ? '' : providerGroup #"},
            {field: "isLawyer", template: "#= getTranslation(isLawyer)#", title: _lang.is_lawyer, width: '120px'},
            {field: "userDirectory",template: "#= (userDirectory !== null) ? getTranslation(userDirectory) : (isAd == '1') ? _lang.activeDirectory : _lang.localDirectory #", title: _lang.userDirectory, width: '192px'},
            {field: "department", title: _lang.department, width: '192px'},
            {field: "jobTitle", title: _lang.position, width: '192px'},
            {field: "phone", title: _lang.phone, width: '120px'},
            {field: "mobile", title: _lang.mobile, width: '120px'},
            {field: "userGroupDescription", title: _lang.groupDescription, template: '<span rel="tooltip" title="#=userGroupDescription#"> #=(userGroupDescription!=null&&userGroupDescription!="") ? ((userGroupDescription.length>40)? userGroupDescription.substring(0,40)+"..." : userGroupDescription) : ""#</span>', width: '320px'},
            {field: "banned", template: "#= (banned == 0) ? _lang.no : _lang.yes #", title: _lang.banned, width: '171px'},
            {field: "ban_reason", title: _lang.banReason, width: '178px'},
            {field: "last_login", title: _lang.userFields.last_login, format: "{0:yyyy-MM-dd}", width: '158px'},
            {field: "flagNeedApproval", template: "#= (flagNeedApproval == '1') ? _lang.yes : _lang.no #", title: _lang.pendingApprovals, width: '175px'},
            {field: "userModifiedName", title: _lang.lastModifiedBy, width: '152px', template: '#= (userModifiedName!=null && modifiedByStatus=="Inactive")? userModifiedName+" ("+_lang.custom[modifiedByStatus]+")":((userModifiedName!=null)?userModifiedName:"") #'},
            {field: "modified", title: _lang.lastModifiedOn, format: "{0:yyyy-MM-dd}", width: '151px'},
//		{field: "title", template: "#= getTranslation(title)#", title: _lang.title, width: '112px'},
//		{field: "city", title: _lang.city, width: '120px'},
//		{field: "country", title: _lang.country, width: '120px'},
//		{field: "nationality", title: _lang.nationality, width: '120px'},
        ],
        editable: false,
        filterable: false,
        height: 480,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, refresh: true, pageSizes: [10, 20, 50, 100]},
        reorderable: true,
        resizable: true,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        columnMenu: {
            messages: _lang.kendo_grid_sortable_messages
        }
        ,
        toolbar: [{
            name: "users-grid-toolbar",
            template: '<div class="row w-100">'
                + '<h4 class="col-md-3">' + _lang.users + '</h4>'
                + ' <div class="input-group col-md-5">'
                + ' <input type="text" class="form-control search" placeholder=" '
                + _lang.search + '" name="userLookUp" id="userLookUp" onkeyup="userQuickSearch(event.keyCode, this.value);" title="'
                + _lang.searchUser + '" />'
                + '</div>'
                + '</div>'
                + '<a href="javascript:;" onclick="advancedSearchFilters()" class="btn btn-default btn-link">'
                + _lang.advancedSearch + '</a>'
                + '<div class="col-md-1 pull-right">'
                + '<div class="dropdown">'
                + '<button type="button" class="nav-link btn btn-info dropdown-toggle" data-toggle="dropdown" id="navbarDropdown">'
                + _lang.actions + ' <span class="caret"></span>'
                + '<span class="sr-only">Toggle Dropdown</span>'
                + '</button>'
                + '<div class="dropdown-menu" aria-labelledby="navbarDropdown" role="menu">'
                + '<a class="dropdown-item" href="javascript:;" onClick="addUser();" title="' + _lang.addUser + '" class="" href="javascript:;" >' + _lang.addUser + '</a>'
                + '<a class="dropdown-item" onclick="exportUsersToExcel()" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a>'
                + '<a class="dropdown-item" href="' + getBaseURL() + '#= (isCloudInstance === \'1\' && enabledIdp != "") ? \'saml_sso/import_users\' : \'users/import_from_ad\' #">' + '#= (isCloudInstance === \'1\' && enabledIdp != "") ? _lang.importUsersfromIdP.sprintf([_lang.custom[enabledIdp]]) : _lang.importUsersfromAD #' + '</a>'
                + '</div>'
                + '</div>'
                + '</div>'
        }]
    };

    searchUsers();
    jQuery('.multi-select', '#userSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    jQuery("#userSearchFilters").submit();
    jQuery('#userSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        enableQuickSearch = false;
        searchUsers();
    });
});

/**
 * to fix userDirectory filter beacuse in one filter we use two database fileds
 * @param element DOM "Drop down value of userDirectory"
 */
function changeFieldFilterActiveDirectory(element) {
    var userDirectoryValue = jQuery(element).val();
    if(userDirectoryValue == 0 || userDirectoryValue == 1 ||  userDirectoryValue === ''){
        jQuery("#isAdField").val('users.isAd');
        jQuery("#adoperator-f1").val('empty');
    }else {
        jQuery("#isAdField").val('users.userDirectory');
        jQuery("#adoperator-f1").val('not_empty');
    }
    changeFieldFilterOperator(jQuery("#isAdOpertator")[0]);
}

/**
 * to fix userDirectory filter beacuse in one filter we use two database fileds
 * @param element DOM "Drop down value of userDirectory operator"
 */
function changeFieldFilterOperator(element) {
    var operator = jQuery(element).val();
    if(jQuery("#isAdField").val() === 'users.userDirectory'){
        if(operator === 'eq'){
            jQuery("#adoperator-f1").val('not_empty');
        }else{
            jQuery("#adoperator-f1").val('empty');
        }
    }else{
        if(operator === 'eq'){
            jQuery("#adoperator-f1").val('empty');
            jQuery("#adoperator-logic").val('and');
        }else{
            jQuery("#adoperator-f1").val('not_empty');
            jQuery("#adoperator-logic").val('or');
        }
    }
}

function advancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        loadEventsForFilters();
        jQuery('#filtersFormWrapper').slideDown();
    }
    jQuery('html, body').animate({scrollTop: 0}, 0);
}
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['creationDateValue', 'creationDateEndValue', 'last_loginValue', 'last_loginEndValue', 'modifiedOnValue', 'modifiedOnEndValue']});
    userLookup('modifiedByValue');
}
function getSearchResults(filtersForm, formData) {
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'users/index',
        data: formData,
        beforeSend: function () {
            jQuery('#submit, #reset', filtersForm).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('#submit, #reset', filtersForm).attr('disabled', 'disabled');
            jQuery('#submit, #reset', filtersForm).removeAttr('disabled');
            jQuery('#searchResults').html(response.html);
            scrollToId('#searchResults');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function searchUsers() {
    document.getElementsByName("page").value = 1;
    document.getElementsByName("skip").value = 0;
    if (undefined == jQuery('#searchResults').data('kendoGrid')) {
        var kGrid = jQuery('#searchResults').kendoGrid(contractSearchGridOptions);
        fixGridHeader();
        var kGridData = kGrid.data('kendoGrid');
        if (!makerCheckerFeatureStatus) {
            kGridData.hideColumn("flagNeedApproval");
        }
        return false;
    }
    jQuery('#searchResults').data('kendoGrid').dataSource.page(1);
    return false;
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function exportUsersToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('userSearchFilters')));
    newFormFilter.attr('action', getBaseURL() + 'export/users').submit();
}
function getFormFilters() {
    var filters = '';
    var filtersForm = jQuery('#userSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('userSearchFilters', '.', true);
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterFirstNameValue', '#userSearchFilters').val() || jQuery('#quickSearchFilterLastNameValue', '#userSearchFilters').val() || jQuery('#quickSearchFilterUsernameValue', '#userSearchFilters').val() || jQuery('#quickSearchFilterEmailValue', '#userSearchFilters').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function changePassword(id) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'users/change_password',
        type: 'POST',
        data: {
            userId: id,
            type: 'getForm'
        },
        beforeSend: function () {
            if (jQuery('#changePasswordDialog').length == 0) {
                jQuery('<div id="changePasswordDialog" class="d-none"></div>').appendTo('body');
            }
            jQuery('#changePasswordDialog').html('<div align="center"><img height="18" src="assets/images/icons/16/loader-submit.gif" /></div>');
            var banUnbanDialog = jQuery("#changePasswordDialog");
            banUnbanDialog.dialog({
                autoOpen: true,
                buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info',
                    id: 'btnSubmitSave',
                    click: function () {
                        var dataIsValid = jQuery("form#changePasswordForm", this).validationEngine('validate');
                        var formData = jQuery("form#changePasswordForm", this).serialize();
                        if (dataIsValid) {
                            var that = this;
                            jQuery.ajax({
                                beforeSend: function () {
                                    jQuery("#output", that).html('<img height="18" src="assets/images/icons/16/loader-submit.gif" />');
                                },
                                data: formData,
                                dataType: 'JSON',
                                type: 'POST',
                                url: getBaseURL() + 'users/change_password',
                                success: function (response) {
                                    if (response.resultCode === 101) {
                                        pinesMessage({ty: 'error', m: response.message});
                                    } else {
                                        if (response.resultCode === 0 && !response.result) {
                                            pinesMessage({ty: 'error', m: response.message});
                                        } else {
                                            if (response.status && true == response.status) {
                                                jQuery(that).dialog("close");
                                                pinesMessage({ty: 'success', m: response.message});
                                            } else {
                                                jQuery(that).html(response.html);
                                                jQuery('#actionsHeader', that).hide();
                                            }
                                            jQuery('#searchResults').data('kendoGrid').dataSource.read();
                                        }
                                    }
                                },
                                error: defaultAjaxJSONErrorsHandler
                            });
                        }
                    }
                },
                    {
                        text: _lang.cancel,
                        'class': 'btn btn-link',
                        click: function () {
                            jQuery(this).dialog("close");
                        }
                    }],
                close: function () {
                    jQuery(window).unbind('resize');
                },
                open: function () {
                    var that = jQuery(this);
                    that.removeClass('d-none');
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '500');
                    }));
                    resizeNewDialogWindow(that, '50%', '500');
                },
                draggable: true,
                modal: false,
                title: _lang.changePasswordForm,
                responsive: true,
                resizable: true
            });
        },
        success: function (response) {
            jQuery('#changePasswordDialog').html(response.html);
            jQuery('#actionsHeader').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function activateDeactivate(userId, status) {
    var msg = (status == 'Active') ? _lang.confirmationDeactivateUser : _lang.confirmationActivateUser;
    if (confirm(msg)) {
        var newValue = (status == 'Active') ? 'Inactive' : 'Active';
        jQuery.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'users/activate_deactivate',
            data: {
                id: userId,
                newStatus: newValue
            },
            beforeSend: function () {
            },
            success: function (response) {
                // update this value when activating / deactivating users 
                canAddUser = response.subscription_deny_additions;
                if (response.status) {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    jQuery('#core-active-users-msg').html(response.core_active_users_msg);
                    jQuery('#contract-active-users-msg').html(response.contract_active_users_msg);
                } else {
                    if (response.validationErrors) {
                        var errorMsg = '';
                        for (i in response.validationErrors) {
                            errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                        }
                        if (errorMsg != '') {
                            pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                        }
                    } else if (response.msg) {
                        pinesMessage({ty: 'warning', m: response.msg});
                    } else if (response.licenseMsg) {
                        if (response.subscription_deny_additions) {
                            pinesMessage({ty: 'warning', m: response.licenseMsg});
                        } else {
                            displaySubscriptionAdditionalUserForm();
                        }
                    } else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                }
                jQuery('#searchResults').data('kendoGrid').dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function banUnban(userId) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'users/ban_unban',
        type: 'POST',
        data: {
            id: userId,
            type: 'getForm'
        },
        beforeSend: function () {
            if (jQuery('#banUnbanDialog').length == 0) {
                jQuery('<div id="banUnbanDialog" class="d-none"></div>').appendTo('body');
            }
            jQuery('#banUnbanDialog').html('<div align="center"><img height="18" src="assets/images/icons/16/loader-submit.gif" /></div>');
        },
        success: function (response) {
            var banUnbanDialog = jQuery("#banUnbanDialog");
            banUnbanDialog.dialog({
                autoOpen: true,
                buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info',
                    id: 'btnSubmitSave',
                    click: function () {
                        var formData = jQuery("form#banUnbanForm", this).serialize();
                        var that = this;
                        jQuery.ajax({
                            beforeSend: function () {
                                jQuery("#output", that).html('<img height="18" src="assets/images/icons/16/loader-submit.gif" />');
                            },
                            data: formData,
                            dataType: 'JSON',
                            type: 'POST',
                            url: getBaseURL() + 'users/ban_unban',
                            success: function (response) {
                                if (response.status) {
                                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                } else if (response.msg) {
                                    pinesMessage({ty: 'warning', m: response.msg});
                                } else {
                                    pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                                }
                                jQuery(that).dialog("close");
                                jQuery('#searchResults').data('kendoGrid').dataSource.read();
                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                    }
                },
                    {
                        text: _lang.cancel,
                        'class': 'btn btn-link',
                        click: function () {
                            jQuery(this).dialog("close");
                        }
                    }],
                open: function () {
                    var that = jQuery(this);
                    that.removeClass('d-none');
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '70%', '500');
                    }));
                    resizeNewDialogWindow(that, '70%', '500');
                },
                draggable: true,
                modal: false,
                resizable: true,
                title: _lang.banUnbanForm,
                responsive: true
            });
            jQuery('#banUnbanDialog').html(response.html);
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function onChangeBannedUnbanned(isBanned) {
    var banReason = jQuery('#banReason', '#banUnbanForm');
    var banReasonContainer = jQuery('#banReasonContainer', '#banUnbanForm');
    if (isBanned) {
        banReasonContainer.removeClass('d-none');
        banReason.val('').focus();
    } else {
        banReasonContainer.addClass('d-none');
    }
}
function overridePrivacy(userId, status) {
    var message = (status == 'yes') ? _lang.overridePrivacyMsgSetNo : _lang.overridePrivacyMsgSetYes;
    if (confirm(message)) {
        var newValue = (status == 'yes') ? 'no' : 'yes';
        jQuery.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'users/override_privacy',
            data: {id: userId, overridePrivacy: newValue},
            beforeSend: function () {
            },
            success: function (response) {
                if (response.result) {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                } else if (response.msg) {
                    pinesMessage({ty: 'warning', m: response.msg});
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                }
                jQuery('#searchResults').data('kendoGrid').dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function flagUserToChangePassword(userId, name) {
    name = decodeURIComponent(name);
    if (confirm(_lang.flagToChangePasswordConfirmationMsg.sprintf([name]))) {
        jQuery.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'users/flag_to_change_password',
            data: {id: userId},
            beforeSend: function () {
            },
            success: function (response) {
                if (response.result) {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                }
                jQuery('#searchResults').data('kendoGrid').dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function getTranslation(fieldValue) {
    return _lang.custom[fieldValue];
}
function userQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterFirstNameValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilterLastNameValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilterUsernameValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilterEmailValue', '#filtersFormWrapper').val(term);
        jQuery('#searchResults').data('kendoGrid').dataSource.page(1);
    }
}
function convertLocalUserToAD(userId) {
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'users/convert_local_directory_users_to_active_directory',
        data: {id: userId},
        beforeSend: function () {
        },
        success: function (data) {
            if (data.noResults) {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailedAd});
            } else if (data.error) {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            } else if (data.success) {
                if (data.msg) {
                    pinesMessage({ty: 'warning', m: data.msg});
                } else {
                    pinesMessage({ty: 'information', m: _lang.feedback_messages.updatesSavedSuccessfully});
                }
            }
            jQuery('#searchResults').data('kendoGrid').dataSource.read();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function revokeAPIId(userId) {
    if (confirm(_lang.confirmationRevokeAPIKey)) {
        jQuery.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'users/revoke_api_key',
            data: {id: userId},
            beforeSend: function () {
            },
            success: function (response) {
                if (response.result == 1) {
                    pinesMessage({ty: 'information', m: _lang.feedback_messages.updatesSavedSuccessfully});
                } else if(response.result == -1){
                    pinesMessage({ty: 'information', m: _lang.feedback_messages.userHasNoApiKeys});
                } else{
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                }
                jQuery('#searchResults').data('kendoGrid').dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function checkerApproveChanges(userId) {
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'users/checker_approve_changes',
        data: {modeType: 'getForm', id: userId},
        beforeSend: function () {
        },
        success: function (response) {
            if (!jQuery('#approveChangesDialog').length) {
                jQuery('<div id="approveChangesDialog" class="d-none"></div>').appendTo('body');
            }
            var approveChangesDialog = jQuery('#approveChangesDialog');
            approveChangesDialog.html(response.html).dialog({
                autoOpen: true,
                buttons: [
                    {
                        'class': 'btn btn-info',
                        click: function () {
                            var sendApproveRequest = true;
                            if (!jQuery('input[name="changeIds[]"]:checked', approveChangesDialog).length) {
                                if (!confirm(_lang.confirmationDiscardChangesEditMode)) {
                                    sendApproveRequest = false;
                                }
                            }
                            if (sendApproveRequest) {
                                var formData = jQuery("form#approveChangesForm", approveChangesDialog).serialize();
                                jQuery.ajax({
                                    url: getBaseURL() + 'users/checker_approve_changes',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: formData,
                                    beforeSend: function () {
                                    },
                                    success: function (response) {
                                        if (!response.result) {
                                            var errorMsg = '';
                                            for (i in response.validationErrors) {
                                                errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                            }
                                            if (errorMsg != '') {
                                                pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                            }
                                        } else {
                                            approveChangesDialog.dialog("close");
                                            pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                            jQuery('#searchResults').data('kendoGrid').dataSource.read();
                                        }
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            }
                        },
                        text: _lang.approve
                    },
                    {
                        'class': 'btn btn-info ' + (response.changeType === 'edit' ? 'd-none' : ''),
                        click: function () {
                            if (confirm(_lang.confirmationDiscardChanges.sprintf([_lang.user]))) {
                                jQuery.ajax({
                                    url: getBaseURL() + 'users/checker_approve_changes',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: {modeType: 'discardUser', id: userId, changeType: response.changeType},
                                    beforeSend: function () {
                                    },
                                    success: function () {
                                        approveChangesDialog.dialog("close");
                                        jQuery('#searchResults').data('kendoGrid').dataSource.read();
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            }
                        },
                        text: _lang.discardUser
                    },
                    {
                        text: _lang.cancel,
                        'class': 'btn btn-link',
                        click: function () {
                            jQuery(this).dialog("close");
                        }
                    }
                ],
                open: function () {
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(approveChangesDialog, '60%', '500');
                    }));
                    resizeNewDialogWindow(approveChangesDialog, '60%', '500');
                },
                draggable: true,
                modal: false,
                position: {my: 'center', at: 'center'},
                resizable: false,
                title: _lang.approveChanges
            }).removeClass('d-none');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function syncUserByAD(userId) {
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'users/sync_user_from_active_directory',
        data: {id: userId},
        beforeSend: function () {
        },
        success: function (data) {
            if (data.noResults) {
                pinesMessage({ty: 'error', m: data.noResults});
            } else if (data.success == 0) {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            } else {
                if (data.warning) {
                    pinesMessage({ty: 'warning', m: data.warning});
                } else {
                    pinesMessage({ty: 'information', m: data.msg});
                }
            }

            jQuery('#searchResults').data('kendoGrid').dataSource.read();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function addUser() {
    if (canAddUser) {
        window.location = getBaseURL() + 'users/add';
    } else {
        displaySubscriptionAdditionalUserForm();
    }
}