var enableQuickSearch = false;
var usersSearchDataSrc, contractSearchGridOptions;
jQuery(document).ready(function () {
    usersSearchDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "customer_portal/users",
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
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
                    userType: {type: "string"},
                    firstName: {type: "string"},
                    lastName: {type: "string"},
                    company: {type: "string"},
                    jobTitle: {type: "string"},
                    username: {type: "string"},
                    email: {type: "string"},
                    phone: {type: "string"},
                    mobile: {type: "string"},
                    banned: {type: "string"},
                    ban_reason: {type: "string"},
                    last_login: {type: "date"},
                    status: {type: "string"},
                    modified: {type: "date"},
                    userDirectory: {type: "string"}
                }
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
            {
                title: ' ', 
                template: '<div class="dropdown">' + gridActionIconHTML +
                        '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" style="#= (status == \'Inactive\') ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="banUnban(\'#= id #\');">' + _lang.banUnban + '</a>' +
                        '<a class="dropdown-item" style="#= ((banned == 1)||(status==\'Inactive\') || isAd == 1 || userDirectory) ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="flagUserToChangePassword(\'#= id #\', \'#= addslashes(encodeURIComponent(firstName + \' \' + lastName)) #\');">' + _lang.flagToChangePassword + '</a>' +
                        '<a class="dropdown-item" style="#= (approved == \'1\') ? \'display: none;\' : \'\'  #" href="javascript:;" onclick="userApprove(\'#= id #\');">' + _lang.approve + '</a>' +
                        '</div></div>', 
                width: '70px'
            },
            {field: "id", filterable: false, title: ' ', template: '<i onclick="activateDeactivate(\'#= id #\', \'#= status #\');" class=\'#= (status=="active")?"deactivate_sign":"activate_sign"#\' title=\'#= (status=="active")?_lang.clickToDeactivate:_lang.clickToActivate#\'></i>', width: '70px'},
            {field: "firstName", title: _lang.firstName, width: '120px', template: '<a href="' + getBaseURL() + 'customer_portal/user_edit/#= id #">#=firstName#</a>'},
            {field: "lastName", title: _lang.lastName, width: '120px', template: '<a href="' + getBaseURL() + 'customer_portal/user_edit/#= id #">#=lastName#</a>'},
            {field: "username", title: _lang.username, width: '192px'},
            {field: "email", title: _lang.email, width: '192px'},
            {field: "userType", template: "#= getTranslation(userType) #", title: _lang.type, width: '200px'},
            {field: "status", title: _lang.status, width: '200px'},
            {field: "userDirectory",template: "#= (userDirectory !== null) ? getTranslation(userDirectory) : (isAd == '1') ? _lang.activeDirectory : _lang.localDirectory #", title: _lang.userDirectory, width: '192px'},
            {encoded: false, field: "company", title: _lang.company, width: '192px', template: "#= (company == null) ? '' : company #"},
            {field: "jobTitle", title: _lang.position, width: '192px'},
            {field: "phone", title: _lang.phone, width: '120px'},
            {field: "mobile", title: _lang.mobile, width: '120px'},
            {field: "banned", template: "#= (banned == 1) ? _lang.yes : _lang.no #", title: _lang.banned, width: '171px'},
            {field: "ban_reason", title: _lang.banReason, width: '178px'},
            {field: "last_login", title: _lang.userFields.last_login, format: "{0:yyyy-MM-dd}", width: '158px'},
            {field: "createdByName", title: _lang.createdBy, width: '140px',template: '#= (createdByName!=null && createdStatus=="Inactive")? createdByName+" ("+_lang.custom[createdStatus]+")":((createdByName!=null)?createdByName:"") #'},
            {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '136px'},
            {field: "modifiedByName", title: _lang.modifiedBy, width: '140px',template: '#= (modifiedByName!=null && modifiedStatus=="Inactive")? modifiedByName+" ("+_lang.custom[modifiedStatus]+")":((modifiedByName!=null)?modifiedByName:"") #'},
            {field: "modifiedOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '138px'}
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
                template: '<div class="d-flex"> <div class="d-flex col-md-6">'
                        + '<h4 class="col-2">' + _lang.customers + '</h4>'
                        + ' <div class="input-group col-6">'
                        + ' <input type="text" class="form-control search" placeholder=" '
                        + _lang.search + '" name="userLookUp" id="userLookUp" onkeyup="userQuickSearch(event.keyCode, this.value);" title="'
                        + _lang.searchUser + '" />'
                        + '</div>'
                        + '</div>'
                        + '<div><a href="javascript:;" onclick="advancedSearchFilters()" class="btn btn-default btn-link">'
                        + _lang.advancedSearch + '</a> </div>'
                        + '<div class="col-md-1">'
                        + '<div class="btn-group">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                        + _lang.actions + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" href="' + getBaseURL() + 'customer_portal/user_add" title="' + _lang.addCustomerPortalUser + '" class="" href="javascript:;" >' + _lang.addCustomerPortalUser + '</a>'
                        + '# if (active_directory_is_enabled != "") { # <a class="dropdown-item" href="' + getBaseURL() + 'customer_portal/user_import_from_ad"  >' + _lang.importUsersfromAD + '</a> #} #'
                        + '# if (enabledIdp != "") { # <a class="dropdown-item" href="' + getBaseURL() + 'saml_sso/import_cp_users"  >' + _lang.importUsersfromIdP.sprintf([_lang.custom[enabledIdp]]) + '</a> #} #'
                        + '<a class="dropdown-item" onclick="exportUsersToExcel()" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a>'
                        + '</div>'
                        + '</div>'
                        + '</div></div>'
            }]
    };
    searchUsers();
    jQuery('#userSearchFilters').bind('submit', function (e) {
        enableQuickSearch = false;
    });
});
function advancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        loadEventsForFilters();
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function loadEventsForFilters() {
    companyAutocompleteMultiOption(jQuery('#lookup-companies-value', '#userSearchFilters'),resultHandlerAfterCompanyAutocomplete);
    makeFieldsDatePicker({fields: ['creationDateValue', 'creationDateEndValue', 'modifiedOnValue', 'modifiedOnEndValue']});
    userLookup('modifiedByValue');
    userLookup('createdByValue');
}
function searchUsers() {
    if (undefined == jQuery('#searchResults').data('kendoGrid')) {
        var kGrid = jQuery('#searchResults').kendoGrid(contractSearchGridOptions);
        var kGridData = kGrid.data('kendoGrid');
        return false;
    }
    jQuery('#searchResults').data('kendoGrid').dataSource.read();
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

    newFormFilter.attr('action', getBaseURL() + 'export/customer_portal_users').submit();
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
function getTranslation(fieldValue) {
    if(fieldValue == 'client'){
        fieldValue = 'cp_client';
    }
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
function activateDeactivate(userId, status) {
        var newValue = (status == 'active') ? 'inactive' : 'active';
        jQuery.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'customer_portal/user_activate_deactivate',
            data: {
                id: userId,
                newStatus: newValue
            },
            beforeSend: function () {
            },
            success: function (response) {
                if (response.status) {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
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
                        pinesMessage({ty: 'warning', m: response.licenseMsg});
                    } else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                }
                jQuery('#searchResults').data('kendoGrid').dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
}
function userApprove(userId) {
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'customer_portal/user_approve',
        data: {
            id: userId,
        },
        beforeSend: function () {
        },
        success: function (response) {
            if (response.status) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            } else {
                if (response.msg) {
                    pinesMessage({ty: 'warning', m: response.msg});
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                }
            }
            jQuery('#searchResults').data('kendoGrid').dataSource.read();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function copyURLPath(idp) {
    idp = idp || false;
    var urlForCustomers = jQuery('#url-for-customers' + (idp ? '-idp' : '')).text();
    copyTextToClipboard(urlForCustomers);
    pinesMessage({ty: 'information', m: _lang.pathIsCopiedToClipboard});
    return true;
}
function banUnban(userId) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'customer_portal/ban_unban',
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
                                url: getBaseURL() + 'customer_portal/ban_unban',
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

function flagUserToChangePassword(userId, name) {
    name = decodeURIComponent(name);

    if (confirm(_lang.flagToChangePasswordConfirmationMsg.sprintf([name]))) {
        jQuery.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'customer_portal/flag_to_change_password',
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
