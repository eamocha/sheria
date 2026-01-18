var expensesTable = jQuery('#expenses-table');
var exRates = null;
var voucher_header = null;
var paymentMethod = null;
var clientName = null;
var clientAccountId = null;
var clientId = null;
var caseClientId = null;
var totalAmount = 0;
var totalAmountWithoutTax = 0;
var totalTax = 0;
var total = 0;
var expensesForm = null;

jQuery(document).ready(function(){
    expensesForm = jQuery('#expensesForm');
    expensesTable = jQuery('#expenses-table');
    exRates = jQuery.parseJSON(jQuery('#exchangeRates').html());
    voucher_header = jQuery('#voucher_header_id').val();
    clientName = jQuery("#clientName", expensesForm);
    clientAccountId = jQuery("#account_id", expensesForm);
    clientId = jQuery("#client_id", expensesForm);
    
    if (voucher_header == '') {
        paymentMethod = jQuery('#paymentMethod').val();
        changeAccountsValues(paymentMethod);
    }

    selectBillingStatus(jQuery('input[name="billingStatus"]:checked').val(), jQuery('#voucher_header_id').val());

    caseExpenseLookup();
    caseSubjectTooltip();

    var itemRow = jQuery('tbody tr:first-child', expensesTable);

    setItemRowEvents(itemRow);

    jQuery('#paid_through').chosen({width: "100%"}).change(function () {
        setCurrencyCode();
    });

    expensesFormValidate();

    expensesForm.bind('submit', function (e) {
        var formIsValid = expensesForm.validationEngine('validate');
        return formIsValid ? true : false;
    });
});

function setItemRowEvents(itemRow){
    var itemRowIndex = itemRow.index();

    jQuery('input[name^="expenses"][name$="[vendorName]"]', itemRow).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.type = 'supplier';

            jQuery.ajax({
                url: getBaseURL('money') + 'vendors/autocomplete',
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
                jQuery(this).siblings('input[name^="expenses"][name$="[vendor_id]"]').val(ui.item.record.id);
                jQuery(this).parent().parent().siblings('td').children('input[name^="expenses"][name$="[tax_number]"]').val(ui.item.record.tax_number);
            } else if (ui.item.record.id === -1) {
                quickAddNewVendor(ui.item.record.term, jQuery(this).parent().parent().parent());
            }
        }
    });

    setCurrencyCode(itemRow);
    makeFieldsDatePicker({fields: ['paidOn' + itemRowIndex]});

    jQuery('select[name^="expenses"][name$="[expense_category_id]"]', itemRow).chosen({width: "100%"}).change(function () {
        setExpenseCategory(jQuery(this));
    });
}

function checkVendorName(element) {
    if (element.val() === '') {
        element.siblings('input[name^="expenses"][name$="[vendor_id]"]').val('');
    }
}

function deleteItemRow(selector, event){
    if(countItemRows() > 1){
        preventFormPropagation(event);

        var itemRow = jQuery(selector).closest('tr');

        if(itemRow){
            itemRow.remove();
        }
    } else{
        var itemRow = jQuery('#expenses-table > tbody tr:first-child');
        jQuery('input', itemRow).val('');
        jQuery('textarea', itemRow).val('');
        jQuery('select', itemRow).val('');
    }

    setTotalAmount();
}

function countItemRows(offsetOne){
    return offsetOne ? jQuery('#expenses-table > tbody tr').length + 1 : jQuery('#expenses-table > tbody tr').length;
}

function setCurrencyCode(itemRow) {
    var selectedAccount = jQuery('#paid_through').find('option:selected');
    var currencyID = selectedAccount.attr('currency');
    var currencyCode = selectedAccount.attr('currencyCode');

    if (selectedAccount.val() === '') {
        jQuery('.paidThroughCurrency', itemRow).html('?');
        jQuery("#currencyID").val('');
        jQuery("#rate").val('');
        jQuery("#rateText").html('1.00');
        jQuery('.totalCurrency').html('?');
    } else {
        jQuery('.paidThroughCurrency', itemRow).html(currencyCode);
        jQuery("#currencyID").val(currencyID);
        jQuery("#rate").val(exRates[currencyID]);
        jQuery("#rateText").html(exRates[currencyID] * 1);
        jQuery('.totalCurrency').html(currencyCode);
    }
}

function setExpenseCategory(select) {
    var selectedExpenseAccount = select.find('option:selected');
    var expense_account = selectedExpenseAccount.attr('expense_account');
    select.siblings('input[name^="expenses"][name$="[expense_account]"]').val(selectedExpenseAccount.val() === '' ? '' : expense_account);
}

function quickAddNewVendor(SupplierName, selector) {
    supplierFormDialog = jQuery('#supplierFormDialog');

    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'vendors/add',
        success: function (response) {
            supplierFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function () {
                            var dataIsValid = jQuery("form#vendorForm", this).validationEngine('validate');
                            var formData = jQuery("form#vendorForm", this).serialize();
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function () {
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'vendors/add',
                                    success: function (response) {
                                        if (!response.status) {
                                            pinesMessage({ty: 'error', m: _lang.saveRecordFailed.sprintf([_lang.vendors])});
                                            jQuery(that).dialog("close");
                                        } else {
                                            pinesMessage({ty: 'success', m: _lang.record_added_successfull.sprintf([_lang.vendors])});
                                            jQuery('input[name^="expenses"][name$="[vendorName]"]', selector).val(response.account.fullName);
                                            jQuery('input[name^="expenses"][name$="[vendor_id]"]', selector).val(response.account.model_id);
                                            jQuery('input[name^="expenses"][name$="[tax_number]"]', selector).val(response.account.vendor_tax_number);
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
                    jQuery('.form-actions', '#vendorForm').remove();
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '60%', '500');
                    }));
                    resizeNewDialogWindow(that, '60%', '500');

                },
                resizable: true,
                responsive: true,
                title: _lang.vendor
            });
            supplierFormDialog.html(response.html);
            jQuery('#supplierForm').removeClass('col-md-6').addClass('col-md-12');
            jQuery('.form-action', '#vendorForm').remove();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function getTargetVendorId(target){
    return target.siblings('input[name^="expenses"][name$="[vendor_id]"]');
}

function calculateTotalAmountWithoutTax(){
    totalAmountWithoutTax = 0;

    jQuery('input[name^="expenses"][name$="[amount]"]').each(function(){
        if(jQuery(this).val()){
            totalAmountWithoutTax = parseFloat(totalAmountWithoutTax + parseFloat(jQuery(this).val()));
        }
    });
}

function calculateTotalTax(){
    totalTax = 0;

    jQuery('select[name^="expenses"][name$="[tax_id]"]').each(function(){
        var siblingAmount = parseFloat(jQuery(this).parents('tr').find('input[name^="expenses"][name$="[amount]"]').val());

        if(jQuery(this).children('option:selected').attr('percentage') && siblingAmount){
            var percentage = parseFloat(jQuery(this).children('option:selected').attr('percentage')) / 100;
            var tax = parseFloat(siblingAmount * (percentage / (percentage + 1)));
            totalTax = parseFloat(totalTax + tax);
        }
    });
}

function setTotalAmount(){
    calculateTotalAmountWithoutTax();
    calculateTotalTax();
    displayTotalAmount();
    displayTotalTax();
    setTotal();
}

function setTotalTax(){
    calculateTotalTax();
    displayTotalAmount();
    displayTotalTax();
}

function displayTotalAmount(){
    totalAmount = totalAmountWithoutTax - totalTax;

    jQuery('.sub_total', expensesForm).html(accounting.formatMoney(accounting.toFixed(totalAmount, allowedDecimalFormat), ""));
    jQuery('#sub_total').val(totalAmount);
}

function displayTotalTax(){
    jQuery('.total_tax', expensesForm).html(accounting.formatMoney(accounting.toFixed(totalTax, allowedDecimalFormat), ""));
    jQuery('#total_tax').val(totalTax);
}

function setTotal(){
    total = parseFloat(totalAmount + totalTax);

    jQuery('.total').html(accounting.formatMoney(accounting.toFixed(total, allowedDecimalFormat), ""));
    jQuery('#total').val(total);
}

function expensesFormValidate() {
    expensesForm.validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false,
        prettySelect: true,
        useSuffix: "_chosen",
        'custom_error_messages': {
            'input[name^="expenses"][name$="[amount]"]': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.amount])
                }
            },
            '#paid_through': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.paidThrough])
                }
            },
            'input[name^="expenses"][name$="[expense_account]"]': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.expensesCategory])
                }
            },
            'select[id^="expense_category_id"][id$="_chosen"]': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.expensesCategory])
                }
            },
            '#clientName': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.client_Money])
                }
            },
            '#account_id': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.accountName])
                }
            }
        }
    });
}
