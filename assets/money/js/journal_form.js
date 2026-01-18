jQuery(document).ready(function () {
    makeFieldsDatePicker({fields: ['dated']});
});
function addRow(rowCount) {
    var deleteIcon = '<a class="pull-right" href="javascript:;" onclick="jQuery(this).parent().parent().remove();calTotal();"><i class="remove-icon"></i></a>';
    if (rowCount === 1)
        deleteIcon = '';
    if (undefined !== rowCount && rowCount > 0)
        count = rowCount;
    journal_details
            .append(jQuery('<tr id="' + count + '">')
                    .append(jQuery('<td>').html(jQuery('#accountsDiv', journal_details_row).html()))
                    .append(jQuery('<td>').html(jQuery('#descriptionDiv', journal_details_row).html()))
                    .append(jQuery('<td>').html(jQuery('#voucherTypesDiv', journal_details_row).html()))
                    .append(jQuery('<td>').html(jQuery('#foreignAmountDiv', journal_details_row).html()))
                    .append(jQuery('<td>').html(jQuery('#localAmountDiv', journal_details_row).html()))
                    .append(jQuery('<td>').html(deleteIcon))
                    );
    jQuery('#accountList', journal_details).attr('id', 'accountIds_' + count).attr('onchange', 'setLocalAmount(' + count + ')');
    jQuery('#descriptionTextarea', journal_details).attr('id', 'description_' + count);
    jQuery('#voucherTypeList', journal_details).attr('id', 'voucherTypes_' + count).attr('onchange', 'calTotal()');
    jQuery('#foreignAmountInput', journal_details).attr('id', 'foreignAmount_' + count).attr('onchange', 'setLocalAmount(' + count + ')');
    jQuery('#currencyCode', journal_details).attr('id', 'currencyCode_' + count);
    jQuery('#localAmountInput', journal_details).attr('id', 'localAmount_' + count).attr('onchange', 'calTotal()');
    jQuery('#accountIds_' + count).chosen({width: "100%"}).change(function () {
        setLocalAmount(count);
    });
    count++;
    journalFormValidate();
}
function convert_accounts_list_to_chosen() {
    jQuery('tr', journal_details).each(function () {
        var id = jQuery(this).attr('id');
        jQuery('#accountIds_' + id).chosen({width: "100%"}).change(function () {
            setLocalAmount(id);
        });
    });
}
function setLocalAmount(count) {
    foreignAmount = jQuery('#foreignAmount_' + count).val();
    var selectedAccount = jQuery('#accountIds_' + count).find('option:selected');
    var currencyCode = selectedAccount.attr('currencyCode');
    if (foreignAmount !== '' && jQuery('#accountIds_' + count).val() !== '') {
        var currencyID = selectedAccount.attr('currency');
        jQuery('#foreignAmount_' + count).val(foreignAmount * 1);
        jQuery('#localAmount_' + count).val(round(foreignAmount * exRates[currencyID] * 1, 2, 2));
    } else {
        jQuery('#localAmount_' + count).val('');
    }
    jQuery('#currencyCode_' + count).html(currencyCode);
    calTotal();
}
function calTotal() {
    var credit = 0;
    var debit = 0;
    jQuery('tr', journal_details).each(function () {
        var id = jQuery(this).attr('id');
        var amount = jQuery('#localAmount_' + id, this).val() * 1;
        if (jQuery('#voucherTypes_' + id, this).val() === 'C')
            credit += amount;
        else if (jQuery('#voucherTypes_' + id, this).val() === 'D')
            debit += amount;

    });
    jQuery('#credit').val(money_format('%i', credit));
    jQuery('.credit').html(money_format('%i', credit));
    jQuery('#debit').val(money_format('%i', debit));
    jQuery('.debit').html(money_format('%i', debit));
}
function check_credit_debit() {
    var accounts = new Array();
    var types = new Array();
    var account_exists = false;
    var account_exists_same_types = false;
    jQuery('tr', journal_details).each(function () {
        var id = jQuery(this).attr('id');
        var account = jQuery('#accountIds_' + id).val() * 1;
        var type = jQuery('#voucherTypes_' + id).val();
        var index = accounts.indexOf(account);
        if (index >= 0) {
            if (type == types[index]) {
                pinesMessage({ty: 'warning', m: _lang.differentsFromToAccountsWithSameType});
                account_exists_same_types = true;
                return false;
            }
        } else {
            accounts.push(account);
            types.push(type);
        }

    });
    if (account_exists || account_exists_same_types) {
        return false;
    }
    if (jQuery('#credit', journalForm).val() !== jQuery('#debit', journalForm).val()) {
        pinesMessage({ty: 'warning', m: _lang.creditEntriesMustEqualDebitEntries});
        return false;
    } else {
        return true;
    }
}

function journalFormValidate() {
    jQuery("#journalForm").validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false,
        prettySelect: true,
        useSuffix: "_chosen",
        'custom_error_messages': {
            '#referenceNum': {'required': {'message': _lang.validation_field_required.sprintf([_lang.journalRefNum])}},
            '#dated': {'required': {'message': _lang.validation_field_required.sprintf([_lang.date])}},
            '#from_account': {'required': {'message': _lang.validation_field_required.sprintf([_lang.FromAccount])}},
            '#to_account': {'required': {'message': _lang.validation_field_required.sprintf([_lang.toAcount])}},
            '#amount': {'required': {'message': _lang.validation_field_required.sprintf([_lang.amount])}},
            '#description': {'required': {'message': _lang.validation_field_required.sprintf([_lang.description])}}
        }
    });
}
function validateAmount(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^(\d+\.?\d{0,2}|\.\d{1,2})$/;
    if (!decimalPattern.test(val) || val <= 0) {
        return _lang.decimalPositiveAllowed;
    }
    if (Math.round(val).toString().length > 12) {
        return  _lang.maxAmountAllowedDigitsNum;
    }
}