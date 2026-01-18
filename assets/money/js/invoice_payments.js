var balance_due, exRates, clientCurrencyId, clientCurrencyCode, recordPaymentForm, organizationCurrencyID, trustBalanceDue;
jQuery(document).ready(function () {
    makeFieldsDatePicker({fields: ['paidOn']});
    recordPaymentFormValidate();
    setRates();
    jQuery('#account_id', jQuery('#payment-transaction-div', recordPaymentForm)).chosen({width: '100%'});
    jQuery('#other_account_id', jQuery('#other-payment-transaction-div', recordPaymentForm)).chosen({width: '100%'});
    paymentMethodListEvents(jQuery('#payment-transaction-div', recordPaymentForm), jQuery('#other-payment-transaction-div', recordPaymentForm));
});
uploadInProgress('submitBtn1', 'uploadDoc');
function paymentMethodListEvents(list1Container, list2Container) {
    jQuery('.payment-method', list1Container).change(function () {
        if (jQuery(this).val() === 'Trust Account') {//check if there is a balance in the trust amount
            jQuery('#other-payment-link').removeClass('d-none');
            jQuery(".payment-method option[value='Trust Account']", list2Container).remove();
            if (typeof trustBalanceDue === 'undefined' || !trustBalanceDue) {
                jQuery(this).val('');
                pinesMessage({ty: 'error', m: _lang.feedback_messages.trustAccountHasNoBalance});
            } else {
                jQuery('.trust-asset-account', list1Container).removeAttr('disabled');
                jQuery('.trust-liability-account', list1Container).removeAttr('disabled');
                jQuery('.trust-account-balance', list1Container).removeClass('d-none');
                jQuery('.balance-amount', list1Container).html(trustBalanceDue);
            }
        } else {
            jQuery('#other-payment-link').addClass('d-none');
            if (!jQuery('#other-payment-transaction-div', recordPaymentForm).hasClass('d-none')) {
                addRemoveOtherPayment();
            }
            jQuery('.trust-asset-account', list1Container).attr('disabled', 'disabled');
            jQuery('.trust-liability-account', list1Container).attr('disabled', 'disabled');
            jQuery('.trust-account-balance', list1Container).addClass('d-none');
            jQuery('.balance-amount', list1Container).html('');
        }
    });
}
function setRates() {
    var account = jQuery('.account-id', jQuery('#payment-transaction-div', recordPaymentForm)).find('option:selected');
    if (account.val() === '') {
        jQuery('#deposit-to-rate', '.payment-data-container').html(exRates[jQuery('#clientCurrencyId').val()] * 1);
    }
    else {
        jQuery('#deposit-to-rate', '.payment-data-container').html(exRates[account.attr('currency')] * 1);
    }
    var otherAccount = jQuery('.account-id', jQuery('#other-payment-transaction-div', recordPaymentForm)).find('option:selected');
    if (otherAccount.val() === '') {
        jQuery('#deposit-to-rate', '.other-payment-data-container').html(exRates[jQuery('#clientCurrencyId').val()] * 1);
    }
    else {
        jQuery('#deposit-to-rate', '.other-payment-data-container').html(exRates[otherAccount.attr('currency')] * 1);
    }
    jQuery('#invoice_rateText', recordPaymentForm).html(exRates[jQuery('#clientCurrencyId').val()] * 1);
}
function change_exchange_rate() {
    exchangeRateFormDialog = jQuery('#exchangeRateFormDialog');
    jQuery.ajax({
        beforeSend: function () {
        },
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'setup/rate_between_money_currencies',
        success: function (response) {
            exchangeRateFormDialog.html(response.html);
            jQuery('.form-action', '#currenciesRate').remove();
            jQuery('#currencyRateBody').removeClass('col-md-6').addClass('col-md-12');
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
                                            setRates();
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
                },
                draggable: true,
                modal: false,
                open: function () {
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '400');
                    }));
                    resizeNewDialogWindow(that, '50%', '400');
                    jQuery('.form-action', '#currenciesRate').remove();
                    jQuery('#currencyRateBody').removeClass('col-md-6').addClass('col-md-12');
                },
                resizable: true,
                title: _lang.changeExchangeRates
            });
        }, error: defaultAjaxJSONErrorsHandler
    });
}
function recordPaymentFormValidate() {
    recordPaymentForm.validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false,
        prettySelect: true,
        useSuffix: "_chosen",
        'custom_error_messages': {
            '.account-id': {'required': {'message': _lang.validation_field_required.sprintf([_lang.depositTo])}},
            '#paidOn': {'required': {'message': _lang.validation_field_required.sprintf([_lang.date])}},
            '#amount': {'required': {'message': _lang.validation_field_required.sprintf([_lang.amount])}},
            '#referenceNum': {'required': {'message': _lang.validation_field_required.sprintf([_lang.paymentNumber])}},
            '.payment-method': {'required': {'message': _lang.validation_field_required.sprintf([_lang.paymentMethod])}},
            '#other-amount': {'required': {'message': _lang.validation_field_required.sprintf([_lang.amount])}}
        }
    });
}
function set_currency(container) {
    container = jQuery(container, recordPaymentForm);
    var selectedAccount = jQuery('.account-id option:selected', container);
    var clientCurrency = jQuery('#clientCurrencyCode', recordPaymentForm).val();
    if (exRates[selectedAccount.attr('currency')] != undefined && exRates[jQuery('#clientCurrencyId', recordPaymentForm).val()] != undefined) {
        if (jQuery(selectedAccount).val() === '') {
            jQuery('#currency-code', container).html(clientCurrency);
            jQuery('#deposit-to-currency-code', container).html(clientCurrency);
            jQuery('#deposit-to-rate', container).html(exRates[jQuery('#clientCurrencyId', recordPaymentForm).val()] * 1);
            jQuery('.deposit-to-label', container).attr('title', '');
        } else {
            jQuery('#currency-code', container).html(jQuery(selectedAccount).attr('currencyCode'));
            jQuery('#deposit-to-currency-code', container).html(selectedAccount.attr('currencyCode'));
            jQuery('#deposit-to-rate', container).html(exRates[selectedAccount.attr('currency')] * 1);
            jQuery('.deposit-to-label', container).attr('title', selectedAccount.text());
        }
    }
}
function validateAmount(field) {
    return validateAmountWithInvBalance(field, jQuery('#other-amount', '#other-payment-transaction-div'), jQuery('#payment-transaction-div', recordPaymentForm), jQuery('#other-payment-transaction-div', recordPaymentForm));

}
function validateOtherAmount(field) {
    return   validateAmountWithInvBalance(field, jQuery('#amount', '#payment-transaction-div'), jQuery('#other-payment-transaction-div', recordPaymentForm), jQuery('#payment-transaction-div', recordPaymentForm));

}
//check if the total payment amount (amount 1 and amount 2) is greater or equal than the balance due also check if the other value of amount is a decimal number.
function validateAmountWithInvBalance(amount1, amount2, transactionDiv1, transactionDiv2) {
    amount1 = amount1.val();
    var decimalPattern = /^(\d+\.?\d{0,2}|\.\d{1,2})$/;
    var account1 = jQuery('.account-id', transactionDiv1).find('option:selected');
    var localAmount2;
    var totalAmount = '';
    amount2 = amount2.val();
    var account2 = jQuery('.account-id', transactionDiv2).find('option:selected');
    if (account2.attr('value') !== '') {
        localAmount2 = round((amount2 * exRates[account2.attr('currency')]) / exRates[clientCurrencyId], 2);
    }
    if (account1.attr('value') !== '') {
        var localAmount1 = round((amount1 * exRates[account1.attr('currency')]) / exRates[clientCurrencyId], 2);
        totalAmount = localAmount1;
        if (typeof localAmount2 !== 'undefined' && localAmount2 !== '') {
            totalAmount = parseFloat(localAmount2) + parseFloat(localAmount1);
        }
        if (isNaN(totalAmount) || totalAmount > parseFloat(balance_due) || !decimalPattern.test(amount1)) {
            return _lang.allowedAmount.sprintf([_lang.invoice]);
        }
        return   validateAmountWithTrustAccount(amount1, transactionDiv1);
    }
}
//check if the trust asset balance account is greater than the paid amount through the Trust Account
function validateAmountWithTrustAccount(amount, transactionDiv) {
    var account = jQuery('.account-id', transactionDiv).find('option:selected');
    if (jQuery('.payment-method', transactionDiv).val() == 'Trust Account' && typeof trustBalanceDue !== 'undefined') {
        if (parseFloat(round((amount * exRates[account.attr('currency')]) / exRates[organizationCurrencyID], 2)) > trustBalanceDue) {
            return _lang.feedback_messages.amountGreaterThanTrustAccountBalance;
        }
    }
}
function addRemoveOtherPayment() {
    var container = jQuery('#other-payment-transaction-div', recordPaymentForm);
    if (container.hasClass('d-none')) {
        container.removeClass('d-none');
        jQuery('#other-deposit-to-currency-div').removeClass('d-none');
        jQuery('#add-remove-other-payment-link', recordPaymentForm).html(_lang.hideOtherPayment);
    } else {
        container.addClass('d-none');
        jQuery('input', container).val('');
        jQuery('select', container).val('');
        jQuery(".account-id", container).val('').trigger("chosen:updated");
        jQuery('#other-deposit-to-currency-div').addClass('d-none');
        jQuery('#add-remove-other-payment-link', recordPaymentForm).html(_lang.addOtherPayment);

    }
}