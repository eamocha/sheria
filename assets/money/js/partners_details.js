function quickAddPartnerAccount(id, partnerName) {
    var accountsFormDialog = jQuery('#accountsFormDialog');
    var type = 'partner';
    jQuery.ajax({
        data: {id: id, type: type},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'accounts/quick_add',
        success: function(response) {
            accountsFormDialog.html(response.html);
            jQuery('form#accountsForm').submit(function(e) {
                jQuery('#btnSubmitSave').trigger('click');
                e.preventDefault();
            });
            accountsFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function() {
                            var dataIsValid = jQuery("form#accountsForm", this).validationEngine('validate');
                            var formData = jQuery("form#accountsForm", this).serializeArray();
                            formData.push({name: 'id', value: id}, {name: 'type', value: type});
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function() {
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'accounts/quick_add',
                                    success: function(response) {
                                        if (!response.status) {
                                            for (i in response.validationErrors) {
                                                pinesMessage({ty: 'error', m: response.validationErrors[i]});
                                            }
                                        } else {
                                            jQuery(that).dialog("close");
                                            window.location = getBaseURL('money') + 'partners/partner_details/' + id;
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
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                    }],
                close: function() {
                    jQuery(window).unbind('resize');
                },
                draggable: false,
                modal: false,
                open: function() {
					jQuery('#addAnother', accountsFormDialog).hide();
                    jQuery('ul.breadcrumb', accountsFormDialog).remove();
                    jQuery('.form-action', accountsFormDialog).remove();
                    jQuery('#accountForm').removeClass('col-md-6').addClass('col-md-12');
                    jQuery(this).removeClass('d-none');
                    jQuery('#account_type_id', '#accountsForm').parent().parent().remove();
                     jQuery('#account-number', '#accountsForm').removeClass('d-none');
                    jQuery('#name', '#accountsForm').val(partnerName);
                    jQuery('#currency_id', '#accountsForm').removeAttr('disabled');
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '500');
                    }));
                    resizeNewDialogWindow(that, '50%', '500');
                },
                resizable: true,
                responsive: true,
                title: _lang.newAccountFor.sprintf([partnerName])
            });
        },error: defaultAjaxJSONErrorsHandler
    });
}
function editPartnerAccount(accountData) {
    accountData = accountData || false;
    if (!accountData) {
        return false;
    }

    var accountsFormDialog = jQuery('#accountsFormDialog');
    var type = 'partner';
    jQuery.ajax({
        data: {id: accountData.id, partnerId: accountData.partnerId, type: type, action: 'getForm'},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'accounts/quick_edit',
        success: function(response) {
            accountsFormDialog.html(response.html);
            jQuery('form#accountsForm').submit(function(e) {
                jQuery('#btnSubmitSave').trigger('click');
                e.preventDefault();
            });
            accountsFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function() {
                            var dataIsValid = jQuery("form#accountsForm", this).validationEngine('validate');
                            var formData = jQuery("form#accountsForm", this).serializeArray();
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function() {
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'accounts/quick_edit',
                                    success: function(response) {
                                        if (!response.status) {
                                            for (i in response.validationErrors) {
                                                pinesMessage({ty: 'error', m: response.validationErrors[i]});
                                            }
                                        } else {
                                            jQuery(that).dialog("close");
                                            window.location = getBaseURL('money') + 'partners/partner_details/' + accountData.partnerId;
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
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                    }],
                close: function() {
                    jQuery(window).unbind('resize');
                },
                draggable: false,
                modal: false,
                open: function() {
                    accountsFormDialog.removeClass('d-none');
					jQuery('#addAnother', accountsFormDialog).hide();
                    jQuery('ul.breadcrumb', accountsFormDialog).remove();
                    jQuery('.form-action', accountsFormDialog).remove();
                    jQuery(this).removeClass('d-none');
                    jQuery('#accountForm').removeClass('col-md-6').addClass('col-md-12');;
                    jQuery('#account_type_id', '#accountsForm').parent().parent().remove();
                    jQuery('#account-number', '#accountsForm').removeClass('d-none');
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '400');
                    }));
                    resizeNewDialogWindow(that, '50%', '400');
                },
                resizable: true,
                responsive: true,
                title: _lang.edit_account_for.sprintf([accountData.accountName])
            });
        },error: defaultAjaxJSONErrorsHandler
    });
}
function deletePartnerAccount(accountId) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL('money') + 'accounts/delete_account',
            type: 'POST',
            dataType: 'JSON',
            data: {accountID: accountId},
            success: function(response) {
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
                        m = _lang.unableToDeleteAnPartnerAccount;
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

function deleteSettlement(accountId, voucherId) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/delete_settlement/' + voucherId,
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                var ty = 'error';
                var m = '';
                if (response) {
                    ty = 'success';
                    m = _lang.deleteRecordSuccessfull;
                    jQuery('#account-' + accountId + '-transaction-' + voucherId, '#partners-settlements-tab').remove();
                    window.location.reload();
                } else {
                    ty = 'warning';
                    m = _lang.recordNotDeleted;
                }
                pinesMessage({ty: ty, m: m});
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

jQuery(document).ready(function () {
    jQuery('.collapse').on('show.bs.collapse', function (event) {
        jQuery('#parentIcon-' + event.target.id.replace(/[^0-9]/g,'')).removeClass("fa-plus-circle").addClass("fa-minus-circle");
      });
      
      jQuery('.collapse').on('hide.bs.collapse', function (event) {
        jQuery('#parentIcon-' + event.target.id.replace(/[^0-9]/g,'')).removeClass("fa-minus-circle").addClass("fa-plus-circle");
      });
});
