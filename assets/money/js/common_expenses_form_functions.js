function selectBillingStatus(selectedOption, voucher_header_id) {
    voucher_header_id = undefined === voucher_header_id ? '' : voucher_header_id;
    var expenseStatus = jQuery('#expense-status', expensesForm).val();
    if(expenseStatus != 'approved'){
        clientName.removeAttr('disabled');
        clientId.removeAttr('disabled');
        clientAccountId.removeAttr('disabled');
        jQuery(":radio").removeAttr('disabled');
        jQuery(":input").removeAttr('disabled');
        clientAccountId.removeAttr('disabled');
    }
    switch (selectedOption) {
        case 'to-invoice':
            jQuery('#client', expensesForm).slideDown('fast');
            jQuery('#account_id', expensesForm).removeClass('d-none').attr('data-validation-engine', 'validate[required]');
            jQuery('#client_radio_button').addClass('d-none');
            jQuery('#radio_button_icon').removeClass('d-none');
            jQuery('#clientDetailsContainer').removeClass('d-none');
            jQuery('#clientBillingDetails').removeClass('d-none');
            if (clientId.val() > 0) {
                jQuery('#client_accounts').removeClass('d-none');
                jQuery('#addAccountId').removeClass('d-none');
            }
            client_lookup();
            appendClientAccountList(clientId.val());
            break;
        case 'non-billable':
            jQuery('#client', expensesForm).slideDown('fast');
            jQuery('#account_id', expensesForm).addClass('d-none').removeAttr('data-validation-engine');
            jQuery('#client_account_id').val('');
            jQuery('#client_accounts').addClass('d-none');
            jQuery('#addAccountId').addClass('d-none');
            jQuery('#client_radio_button').addClass('d-none');
            jQuery('#radio_button_icon').removeClass('d-none');
            client_lookup();
            jQuery('select#account_id').html('');
            jQuery('#clientDetailsContainer').removeClass('d-none');
            jQuery('#clientBillingDetails').removeClass('d-none');
            break;
        case 'not-set':
            checkCaseClient();
            jQuery('#account_id', expensesForm).addClass('d-none').removeAttr('data-validation-engine');
            jQuery('#addAccountId').addClass('d-none');
            jQuery('#client_radio_button').addClass('d-none');
            jQuery('#radio_button_icon').removeClass('d-none');
            client_lookup();
            jQuery('select#account_id').html('');
            jQuery('#clientDetailsContainer').removeClass('d-none');
            if (clientId.val() > 0)
                jQuery('#clientBillingDetails').removeClass('d-none');
            break;
        default: //internal
            jQuery('#account_id', expensesForm).addClass('d-none').removeAttr('data-validation-engine');
            jQuery('#client', expensesForm).slideUp('fast');
            if (jQuery('#isCasePreset').val() != '1') {
                clientName.attr('disabled', 'disabled').val('');
                jQuery('#clientBillingDetails').addClass('d-none');
                clientId.attr('disabled', 'disabled').val('');
            }
            clientAccountId.attr('disabled', 'disabled').val('');
            jQuery('#client_accounts').addClass('d-none');
            jQuery('#addAccountId').addClass('d-none');
            jQuery('select#account_id').html('');
            jQuery('#client_radio_button').removeClass('d-none');
            jQuery('#radio_button_icon').addClass('d-none');
            jQuery('#clientDetailsContainer').addClass('d-none');
            break;
    }
}

function caseExpenseLookup() {
    var $lookupField = jQuery('#caseLookup', expensesForm);

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
                                label: item.id + ': ' + item.subject,
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
                jQuery('#caseLookupId').val(ui.item.record.id);
                jQuery('#caseSubject').text(ui.item.record.subject);
                jQuery('#caseSubjectLinkId').removeClass('d-none').attr('href', (ui.item.record.category == "IP" ? 'intellectual_properties/edit/' : 'cases/edit/') + ui.item.record.id);
                caseClientId = ui.item.record.client_id;
                caseClient = ui.item.record.clientName;
                if (caseClientId) {
                    jQuery('#client_id', expensesForm).val('');
                    checkCaseClient();
                } else {
                    jQuery('#clientName', expensesForm).attr("readonly", false).val('');
                    jQuery('#client_id', expensesForm).val('');
                }
                   jQuery('#relate-to-container', expensesForm).removeClass('d-none');
                   if(ui.item.record.category == 'Litigation'){
                       jQuery('#relate-to-hearing','#relate-to-container', expensesForm).removeClass('d-none');
                       jQuery('#relate-to-event','#relate-to-container', expensesForm).removeClass('d-none');
                   }else{
                       jQuery('#relate-to-hearing','#relate-to-container', expensesForm).addClass('d-none');
                       jQuery('#relate-to-event','#relate-to-container', expensesForm).addClass('d-none');
                   }
            }
        }
    });

    relateTo(jQuery('#relate-task',expensesForm),'task',relateTask);
    relateTo(jQuery('#relate-hearing',expensesForm),'hearing',relateHearing);
    relateTo(jQuery('#relate-event',expensesForm),'event',relateEvent);
}

function caseSubjectTooltip() {
    var element = jQuery("#caseSubject");
    element.tooltip({
        show: {
            effect: "highlight",
            duration: 1100
        },
        content: function () {
            jQuery.ajax({
                url: getBaseURL() + 'cases/autocomplete',
                dataType: "json",
                data: {term: 'M' + jQuery('#caseLookupId').val(), fullSelectFlag: true},
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    jQuery(element).attr("title", data[0].subject);
                    return jQuery(element).attr("title");
                }
            });
        },
        track: false
    });
}

/**
 * js function to make the input of the client read only if the case selected has a client (client of the case will automatically be displayed on adding the client in the expense)
 * if the expense is already created with a client not the same as the case client,the client name input will not be readonly
 */
function checkCaseClient() {
    var caseId = jQuery('#caseLookup', expensesForm).val();
    var clientId = jQuery('#client_case_id', expensesForm).val(); //id of the client for the case
    var caseClientName = jQuery('#client_case_name', expensesForm).val(); //name of the client for the case

    if (caseId) {
        if (caseClientId || clientId) {
            if (jQuery('#client_id', expensesForm).val() == '') {
                jQuery('#clientName', expensesForm).val(caseClient ? caseClient : caseClientName).attr("readonly", true);
                jQuery('#clientBillingDetails').removeClass('d-none');
                jQuery('#account_id', expensesForm).val('');
                jQuery('#client_id', expensesForm).val(caseClientId ? caseClientId : clientId);
                var billingStatusSelected = jQuery('input[name="billingStatus"]:checked').val();

                if (billingStatusSelected == 'to-invoice') {
                    appendClientAccountList(caseClientId ? caseClientId : clientId);
                }
            }
        }
    }
}

function relateTo(that, object, resultHandler) {
    if (jQuery(that).is(':checked')) {
        jQuery('#related-' + object, '#expensesForm').removeClass('d-none');
        resultHandler();
        jQuery('#' + object + '-lookup', '#expensesForm').on('blur', function () {
            if (this.value === '') {
                commonLookupActions(object);
            }
        });
    } else {
        jQuery('#related-' + object, '#expensesForm').addClass('d-none');
        commonLookupActions(object);
    }
}

function relateTask() {
    taskLookup("task-lookup", "task-id", {container: jQuery('#expensesForm'), taskDescriptionId: "#task-detail", filterCases: 'yes', legalCaseLookupId: '#caseLookupId', legalCaseLookup: '#caseLookup', legalCaseSubject: '#caseSubject'});
}

function relateHearing() {
    relateToLookup({container: jQuery('#expensesForm'), input: "#hearing-lookup", hiddenInput: '#hearing-id', link: '#hearing-link',funtionName : 'hearings_autocomplete'}, {filterOn: 'legal_case_id', filterContainer: jQuery('#caseLookupId', '#expensesForm')});
}

function relateEvent() {
    relateToLookup({container: jQuery('#expensesForm'), input: "#event-lookup", hiddenInput: '#event-id', link: '#event-link', funtionName : 'events_autocomplete'}, {filterOn: 'legal_case', filterContainer: jQuery('#caseLookupId', '#expensesForm')});
}

function commonLookupActions(object) {
    var container = jQuery('#related-' + object, '#expensesForm');
    jQuery('#' + object + '-id', container).val('');
    jQuery('#' + object + '-lookup', container).val('');
    jQuery('#' + object + '-detail', container).text('');
    jQuery('#' + object + '-link', container).addClass('d-none');
}

function relateToLookup(lookupDetails, extraFilter) {
    extraFilter = extraFilter || false;
    jQuery(lookupDetails.input, lookupDetails.container).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            if (extraFilter) {
                request.more_filters = {};
                request.more_filters[extraFilter.filterOn] = extraFilter.filterContainer.val();
            }
            jQuery.ajax({
                url: getBaseURL() + 'cases/'+lookupDetails.funtionName,
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
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
                                label: item.hearingID + ': ' + item.subject,
                                value: item.hearingID + ': ' + item.subject,
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
                jQuery(lookupDetails.hiddenInput, lookupDetails.container).val(ui.item.record.id);
                jQuery(lookupDetails.link, lookupDetails.container).removeClass('d-none').attr('href', 'cases/events/' + ui.item.record.legal_case_id);
            }
        }
    });
}

function client_lookup() {
    jQuery("#clientName", expensesForm).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.type = 'client';
            jQuery.ajax({
                url: getBaseURL('money') + 'clients/autocomplete',
                dataType: "json",
                data: request,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched_add.sprintf([request.term]),
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
                }, error: defaultAjaxJSONErrorsHandler
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#clientBillingDetails').removeClass('d-none');
                jQuery('#account_id', expensesForm).val('');
                jQuery('#client_id', expensesForm).val(ui.item.record.id);
                var billingStatusSelected = jQuery('input[name="billingStatus"]:checked').val();
                if (billingStatusSelected == 'to-invoice') {
                    appendClientAccountList(ui.item.record.id);
                }
            } else if (ui.item.record.id === -1) {
                quickAddNewClient(ui.item.record.term);
            }
        }
    });
}

function client_account_lookup() {
    jQuery("#clientName", expensesForm).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL('money') + 'accounts/lookup_client',
                dataType: "json",
                data: request,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched_add.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.name + ' - ' + item.currencyCode,
                                value: item.name + ' - ' + item.currencyCode,
                                record: item
                            };
                        }));
                    }
                }, error: defaultAjaxJSONErrorsHandler
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#account_id', expensesForm).val(ui.item.record.id);
                jQuery('#client_id', expensesForm).val(ui.item.record.model_id);
            } else if (ui.item.record.id === -1) {
                quickAddNewClient(ui.item.record.term);
            }
        }
    });
}

function quickAddNewClient(ClientName) {
    clientFormDialog = jQuery('#clientFormDialog');
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'clients/add',
        success: function (response) {
            clientFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function () {
                            var dataIsValid = jQuery("form#clientForm", this).validationEngine('validate');
                            var formData = jQuery("form#clientForm", this).serialize();
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function () {
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'clients/add',
                                    success: function (response) {
                                        if (!response.status) {
                                            pinesMessage({
                                                ty: 'error', m: _lang.saveRecordFailed.sprintf([_lang.clients_Money])});
                                            jQuery(that).dialog("close");
                                        } else {
                                            pinesMessage({ty: 'success', m: _lang.record_added_successfull.sprintf([_lang.clients_Money])});
                                            jQuery('#clientBillingDetails').removeClass('d-none');
                                            if (jQuery('#billingStatus:checked').val() === 'to-invoice') {
                                                jQuery('#clientName', expensesForm).val(response.account.name);
                                                jQuery('#account_id', expensesForm).val(response.account.id);
                                            } else {
                                                jQuery('#clientName', expensesForm).val(response.account.name);
                                                jQuery('#account_id', expensesForm).val('');
                                            }
                                            jQuery('#client_id', expensesForm).val(response.account.model_id);
                                            jQuery(that).dialog("close");
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
                draggable: true,
                modal: false,
                open: function () {
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '70%', '500');
                    }));
                    resizeNewDialogWindow(that, '70%', '500');
                },
                resizable: true,
                responsive: true,
                title: _lang.client_Money
            });
            clientFormDialog.html(response.html);
            jQuery('.form-action', '#clientForm').remove();
            jQuery('#client_form').removeClass('col-md-6').addClass('col-md-12');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function checkClientName() {
    if (clientName.val() === '') {
        clientId.val('');
        clientAccountId.val('');
        jQuery('#account_id').html('');
        jQuery('#client_accounts').addClass('d-none');
        jQuery('#addAccountId').addClass('d-none');
    }
}

function appendClientAccountList(clt_id) {
    if (clt_id == '') {
        return;
    }

    jQuery.ajax({
        url: getBaseURL('money') + 'accounts/fetch_client_accounts',
        dataType: "json",
        type: "post",
        data: {client_id: clt_id},
        success: function (response) {
            jQuery('#client_accounts').removeClass('d-none');
            jQuery('#addAccountId').removeClass('d-none');
            var accountsListSelect = jQuery("#account_id", jQuery('#client_accounts').html(response.html)).attr('name', 'client_account_id');
            if(jQuery('#expense-status', expensesForm).val() == 'approved'){
                jQuery("#account_id", '#client_accounts').attr("disabled", "disabled");
                jQuery('#addAccountId').addClass('d-none');
            }
            if (jQuery("#clientAccountIdinitialVal").val() != '')
                if (jQuery("option[value=" + jQuery("#clientAccountIdinitialVal").val() + "]", accountsListSelect).length != 0)
                    accountsListSelect.val(jQuery("#clientAccountIdinitialVal").val());
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function addNewClientAccount() {
    var id = jQuery('#client_id').val();
    var clientName = jQuery('#clientName').val();

    if (id == '' || clientName == '') {
        //TODO set message to select client then add account
        return;
    }

    accountsFormDialog = jQuery('#accountsFormDialog');
    jQuery.ajax({
        data: {id: id, type: 'client'},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'accounts/quick_add',
        success: function (response) {
            accountsFormDialog.html(response.html);
            jQuery('form#accountsForm').submit(function (e) {
                jQuery('#btnSubmitSave').trigger('click');
                e.preventDefault();
            });
            accountsFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function () {
                            var dataIsValid = jQuery("form#accountsForm", this).validationEngine('validate');
                            var formData = jQuery("form#accountsForm", this).serializeArray();
                            formData.push({name: 'id', value: id}, {name: 'type', value: 'client'});
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function () {
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'accounts/quick_add',
                                    success: function (response) {
                                        if (!response.status) {
                                            for (i in response.validationErrors) {
                                                pinesMessage({ty: 'error', m: response.validationErrors[i]});
                                            }
                                        } else {
                                            pinesMessage({ty: 'success', m: _lang.record_added_successfull.sprintf([_lang.clientAccount])});
                                            jQuery(that).dialog("close");
                                            // add account to drop down list values with selected option
                                            addAccountListOption(response.record, true);
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
                draggable: true,
                modal: false,
                open: function () {
                    jQuery('#addAnother', accountsFormDialog).hide();
                    jQuery('ul.breadcrumb', accountsFormDialog).remove();
                    jQuery('.form-action', accountsFormDialog).remove();
                    jQuery('#accountForm').removeClass('col-md-6').addClass('col-md-12');
                    jQuery('#name', '#accountsForm').val(clientName);
                    jQuery('#account_type_id', '#accountsForm').parent().parent().remove();
                    jQuery('#account-number', '#accountsForm').removeClass('d-none');
                    jQuery('#currency_id', '#accountsForm').removeAttr('disabled');
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '500');
                    }));
                    resizeNewDialogWindow(that, '50%', '500');
                },
                resizable: true,
                responsive: true,
                title: _lang.newAccountFor.sprintf([clientName])
            });
        }, error: defaultAjaxJSONErrorsHandler
    });
}

function addAccountListOption(record, isSelected) {
    isSelected = isSelected || false;
    var accountList = jQuery('select#account_id');
    accountList.append(
            jQuery('<option></option>').val(record.id).html(record.name + ' - ' + record.currencyCode + ' (' + record.account_number + ')')
            );
    if (isSelected) {
        accountList.val(record.id);
    }
}

function validateDecimal(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^(\d+\.?\d{0,2}|\.\d{1,2})$/;

    if (!decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
}

function change_exchange_rate() {
    exchangeRateFormDialog = jQuery('#exchangeRateFormDialog');
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'setup/rate_between_money_currencies',
        success: function (response) {
            exchangeRateFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function () {
                            var dataIsValid = jQuery("form#currenciesRate", this).validationEngine('validate');
                            var formData = jQuery("form#currenciesRate", this).serialize();
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function () {
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'setup/rate_between_money_currencies',
                                    success: function (response) {
                                        if (!response.status) {
                                            pinesMessage({ty: 'error', m: _lang.invalid_record});
                                            jQuery(that).dialog("close");
                                        } else {
                                            jQuery("#exchangeRates").html(response.rates);
                                            exRates = jQuery.parseJSON(jQuery('#exchangeRates').html());
                                            jQuery("#rate").val(exRates[jQuery('#currencyID').val()]);
                                            jQuery("#rateText").html(exRates[jQuery('#currencyID').val()]);
                                            jQuery(that).dialog("close");
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
                draggable: true,
                modal: false,
                open: function () {
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '400');
                    }));
                    resizeNewDialogWindow(that, '50%', '400');
                },
                resizable: true,
                responsive: true,
                title: _lang.changeExchangeRates
            });
            exchangeRateFormDialog.html(response.html);
            jQuery('.form-action', '#currenciesRate').remove();
            jQuery('#currencyRateBody').removeClass('col-md-6').addClass('col-md-12');
        }, error: defaultAjaxJSONErrorsHandler
    });
}

function setAccountBalance() {
    var selectedAccount = jQuery('#paid_through').find('option:selected');
    var currencyCode = selectedAccount.attr('currencyCode');

    if (selectedAccount.val() !== '') {
        jQuery.ajax({
            url: getBaseURL('money') + 'accounts/get_account_details',
            dataType: "json",
            type: 'POST',
            data: {accountID: selectedAccount.val()}, error: defaultAjaxJSONErrorsHandler,
            success: function (response) {
                var totalAccountBalance;
                var totalAccountBalanceValue;

                if (response.data) {
                    if (response.data.currency_id == organizationCurrencyID || response.data.systemAccount === 'yes') {
                        totalAccountBalance = money_format('%i', round(response.data.localAmount, 2));
                        totalAccountBalanceValue = response.data.localAmount;
                    } else {
                        totalAccountBalance = money_format('%i', round(response.data.foreignAmount, 2));
                        totalAccountBalanceValue = response.data.foreignAmount;
                    }

                    jQuery('#balanceDiv').removeClass('d-none');
                    jQuery('#accountBalance').html(totalAccountBalance + ' ' + currencyCode).css('color', totalAccountBalanceValue < 0 ? 'red' : 'green');
                } else {
                    jQuery('#balanceDiv').addClass('d-none');
                    jQuery('#accountBalance').html('');
                }
            }, error: defaultAjaxJSONErrorsHandler
        });
    } else {
        jQuery('#balanceDiv').addClass('d-none');
        jQuery('#accountBalance').html('');
    }
}

function setExpenseNeedApprovedBalance() {
    var selectedAccount = jQuery('#paid_through').find('option:selected');
    var currencyCode = selectedAccount.attr('currencyCode');
    var currencyID = selectedAccount.attr('currency');
    if (selectedAccount.val() !== '') {
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/get_expenses_need_approved',
            dataType: "json",
            type: 'POST',
            data: {accountID: selectedAccount.val(),currencyID:currencyID}, error: defaultAjaxJSONErrorsHandler,
            success: function (response) {
                var totalExpensesWaitingApproval;
                if (response.data) {
                    jQuery('#need-approved-div').removeClass('d-none');
                    totalExpensesWaitingApproval = money_format('%i', round(response.data.amount, 2));
                    jQuery('#expence-need-approved-balance').html(totalExpensesWaitingApproval + ' ' + currencyCode).css('color','gray');
                } else {
                    jQuery('#need-approved-div').addClass('d-none');
                    jQuery('#expence-need-approved-balance').html('');
                }
            }, error: defaultAjaxJSONErrorsHandler
        });
    } else {
        jQuery('#need-approved-div').addClass('d-none');
        jQuery('#expence-need-approved-balance').html('');
    }
}
function displayTaxAmount(){
    var expenseAmount = jQuery('#amount', '#expensesForm').val();
    var percentage = jQuery('option:selected', jQuery('#tax_id', '#expensesForm')).attr('percentage');
    percentage = Math.abs(percentage);
    if(expenseAmount && percentage){
        var expenseNetAmount = (expenseAmount * 100) / (percentage + 100);
        taxAmount = expenseAmount - expenseNetAmount;
        if(taxAmount > 0){
            taxAmount = money_format('%i', round(taxAmount, 2));
            jQuery('#taxAmountContainer', '#expensesForm').removeClass('d-none');
            jQuery('#taxAmountValue', '#expensesForm').html(taxAmount + '&nbsp;' + jQuery('.paidThroughCurrency').html()).css('color', 'green');
        }else{
            jQuery('#taxAmountContainer', '#expensesForm').addClass('d-none');
        }
    }else{
        jQuery('#taxAmountContainer', '#expensesForm').addClass('d-none');
    }
    
}