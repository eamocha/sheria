function quickAddClientAccount(id, clientName) {
    var accountsFormDialog = jQuery('#accountsFormDialog');
    var type = 'client';
    jQuery.ajax({
        data: {id: id, type: type},
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
                            formData.push({name: 'id', value: id}, {name: 'type', value: type});
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
                                            jQuery(that).dialog("close");
                                            window.location = getBaseURL('money') + 'clients/client_details/' + id;
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
                    accountsFormDialog.removeClass('d-none');
                    jQuery('ul.breadcrumb', accountsFormDialog).remove();
                    jQuery('.form-action', accountsFormDialog).remove();
                    jQuery('#accountForm').removeClass('col-md-6').addClass('col-md-12');
                    jQuery('#account_type_id', '#accountsForm').parent().parent().remove();
                    jQuery('#account-number', '#accountsForm').removeClass('d-none');
                    jQuery('#name', '#accountsForm').val(clientName);
                    jQuery('#currency_id', '#accountsForm').removeAttr('disabled');
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '500');
                    }));
                    resizeNewDialogWindow(that, '50%', '500');
                },
                resizable: false,
                responsive: true,
                title: _lang.newAccountFor.sprintf([clientName])
            });
        }, error: defaultAjaxJSONErrorsHandler
    });
}
function editClientAccount(accountData) {
    accountData = accountData || false;
    if (!accountData) {
        return false;
    }

    var accountsFormDialog = jQuery('#accountsFormDialog');
    var type = 'client';
    jQuery.ajax({
        data: {id: accountData.id, clientId: accountData.clientId, type: type, action: 'getForm'},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'accounts/quick_edit',
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
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function () {
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'accounts/quick_edit',
                                    success: function (response) {
                                        if (!response.status) {
                                            for (i in response.validationErrors) {
                                                pinesMessage({ty: 'error', m: response.validationErrors[i]});
                                            }
                                        } else {
                                            jQuery(that).dialog("close");
                                            window.location = getBaseURL('money') + 'clients/client_details/' + accountData.clientId;
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
                    accountsFormDialog.removeClass('d-none');
                    jQuery('ul.breadcrumb', accountsFormDialog).remove();
                    jQuery('.form-action', accountsFormDialog).remove();
                    jQuery('#accountForm').removeClass('col-md-6').addClass('col-md-12');
                    jQuery('#account_type_id', '#accountsForm').parent().parent().remove();
                    jQuery('#account-number', '#accountsForm').removeClass('d-none');
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '500');
                    }));
                    resizeNewDialogWindow(that, '50%', '500');
                },
                resizable: true,
                responsive: true,
                title: _lang.edit_account_for.sprintf([accountData.accountName])
            });
        }, error: defaultAjaxJSONErrorsHandler
    });
}
function deleteClientAccount(accountId) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL('money') + 'accounts/delete_account',
            type: 'POST',
            dataType: 'JSON',
            data: {accountID: accountId},
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 101:	// removed successfuly
                        ty = 'information';
                        m = _lang.selectedAccountDeleted;
                        jQuery('#account_' + accountId, '.table-details').remove();
                        break;
                    case 202:	// could not remove record
                        ty = 'warning';
                        m = _lang.unableToDeleteAnClientAccount;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function openBalance(accountId) {
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
                    pinesMessage({ty: 'information', m: response.error});
                } else if (response.html) {
                    if (!jQuery('#open-balance-dialog').length) {
                        jQuery("<div id='open-balance-dialog'></div>").appendTo("body");
                    }
                    var openBalanceDialog = jQuery("#open-balance-dialog");
                    openBalanceDialog.html(response.html);
                    jQuery(".modal", openBalanceDialog).modal({
                        keyboard: false,
                        backdrop: "static",
                        show: true
                    });
                    setDatePicker('.date-picker', openBalanceDialog);
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery(".modal", openBalanceDialog).modal("hide");
                        }
                    });
                    jQuery('#open-balance-submit-btn').click(function(){
                        openBalanceSubmit(accountId);
                    });
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
        url: getBaseURL('money') + 'clients/open_balance/' + accountId
    });
}
function openBalanceSubmit(accountId) {
    var formData = jQuery("form#open-balance-form").serializeArray();
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
                    pinesMessage({ty: 'error', m: response.error});
                } else if (response.result) {
                    jQuery(".modal", jQuery("#open-balance-dialog")).modal("hide");
                    window.location = window.location.href;
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
        url: getBaseURL('money') + 'clients/open_balance/' + accountId
    });
}