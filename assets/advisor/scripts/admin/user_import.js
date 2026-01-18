jQuery(window).load(function () {
    jQuery("form#searchPortalUsersForADImport").validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false});
    adUserAutocompleteMultiOption(jQuery('#usernameQuery'));
    companyAutocompleteMultiOption(jQuery('#lookup-companies', '#searchPortalUsersForADImport'), setCustomerPortalADUserSelectedCompany, false, true);
});
function setCustomerPortalADUserSelectedCompany(company) {
    var container = jQuery('#searchPortalUsersForADImport');
    var theWrapper = jQuery('#selected-companies', container);
    if (company.id && !jQuery('#customer-portal-user-company' + company.id, theWrapper).length) {
        theWrapper.append(jQuery('<div class="row multi-option-selected-items no-margin" id="customer-portal-user-company' + company.id + '"><span id="' + company.id + '">' + company.name + '</span> </div>').append(jQuery('<input type="hidden" value="' + company.id + '" name="companies_customer_portal_users[]" />')).append(jQuery('<input value="x" type="button" class="btn btn-default btn-xs pull-right x-icon" onclick="jQuery(this.parentNode).remove();" />')));
    }
}
function addAllUsers() {
    jQuery.ajax({
        url: getBaseURL() + 'customer_portal/users_ad_search_all',
        dataType: 'JSON',
        type: 'POST',
        data: {department: jQuery('option:selected', jQuery(this)).text()},
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.users) {
                jQuery("#usersToAdd").html("");
                for (i in response.users) {
                    var userData = response.users[i];
                    var userEncodedJSONData = userData.encodedJSON;
                    var userEmail = userData.email;
                    jQuery("#usersToAdd").append(jQuery('<div class="row multi-option-selected-items no-margin"><span>' + userEmail + '</span> </div>').append(jQuery('<textarea name="usersToAdd[]" class="d-none">' + userEncodedJSONData + '</textarea>')).append(jQuery('<input type="hidden" value="' + userEmail + '" class="users-to-add" />')).append(jQuery('<a onclick="unsetNewCaseMultiOption(this.parentNode);" tabindex="-1" class="btn btn-default btn-xs btn-link pull-right" href="javascript:;"><i class="fa-solid fa-trash-can red"></i></a>')));
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function adTestConnection() {
    jQuery.ajax({
        url: getBaseURL() + 'users/ad_test_connection',
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#testConnection').html('');
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            switch (response.status) {
                case 101:
                    jQuery('#testConnection').removeClass('connectionSucceeded').addClass('connectionFailed').html(_lang.adTestConnectionErrorMsg);
                    break;
                case 202:
                    jQuery('#testConnection').removeClass('connectionFailed').addClass('connectionSucceeded').html(_lang.adTestConnectionSuccessMsg);
                    break;
                default:
                    jQuery('#testConnection').addClass('d-none');
                    break;
            }
        }
    });
}
function adUserAutocompleteMultiOption(jQueryField) {
    jQueryField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'customer_portal/users_ad_search_by_account',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.noResults) {
                        response([{
                                label: _lang.no_results_matched_for.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
                    } else if (data.users) {
                        response(jQuery.map(data.users, function (item) {
                            return {
                                label: item.email,
                                value: item.encodedJSON,
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
            var match = false;
            var itemValue = ui.item.value; // encoded string data
            var itemLabel = ui.item.label; // email
            jQuery('#usersToAdd .users-to-add').each(function () {
                if (itemLabel == this.value)
                    match = true;
            });
            if (itemValue != '') {
                if (!match) {
                    jQuery("#usersToAdd").append(jQuery('<div class="row multi-option-selected-items no-margin"><span>' + itemLabel + '</span> </div>').append(jQuery('<textarea name="usersToAdd[]" class="d-none">' + itemValue + '</textarea>')).append(jQuery('<input type="hidden" value="' + itemLabel + '" class="users-to-add" />')).append(jQuery('<a onclick="unsetNewCaseMultiOption(this.parentNode);" tabindex="-1" class="btn btn-default btn-xs btn-link pull-right" href="javascript:;"><i class="fa-solid fa-trash-can red"></i></a>')));
                } else {
                    pinesMessage({ty: 'error', m: _lang.adUserAlreadAdded});
                    return false;
                }
            }

        },
        close: function (event, ui) {
            jQuery("#usernameQuery").val('');
        }
    });
}
function displayManualImport() {
    jQuery('#all-users-msg').addClass('d-none');
    jQuery('#manualGroup').removeClass('d-none');
    jQuery('#departmentGroup').addClass('d-none');
}
function displayGroupItme() {
    jQuery('#departmentGroup').removeClass('d-none');
    jQuery('#manualGroup').addClass('d-none');
}
function displayAllUsers() {
    jQuery('#all-users-msg').removeClass('d-none');
    jQuery('#departmentGroup').addClass('d-none');
    jQuery('#manualGroup').addClass('d-none');
    addAllUsers();
}
