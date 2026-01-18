exRates = null, expensesForm = null, clientName = null, clientAccountId = null, clientId = null, caseClient = null, caseClientId = null;
jQuery(document).ready(function () {
    if (clients_do_not_match) { //message that shows client of the expense is not the same as the client of the case
        pinesMessage({ty: 'warning', m: _lang.clientsDonotMatch});
    }
    exRates = jQuery.parseJSON(jQuery('#exchangeRates').html());
    expensesForm = jQuery("#expensesForm");
    clientName = jQuery("#clientName", expensesForm);
    clientAccountId = jQuery("#account_id", expensesForm);
    clientId = jQuery("#client_id", expensesForm);
    if (licenseHasExpired) {
        disableExpiredFields(expensesForm);
        jQuery(':submit').click(function () {
            alertLicenseExpirationMsg();
            return false;
        });
    }
    selectBillingStatus(jQuery('input[name="billingStatus"]:checked').val(), jQuery('#voucher_header_id').val());
    var voucher_header = jQuery('#voucher_header_id').val();
    if (voucher_header == '') {
        var paymentMethod = jQuery('#paymentMethod').val();
        changeAccountsValues(paymentMethod);
    }
    makeFieldsDatePicker({fields: ['paidOn']});
    expensesFormValidate();
    caseExpenseLookup();
    setCurrencyCode();
    caseSubjectTooltip();
    setExpenseCategory();
    jQuery('#expense_category_id').chosen({width: "100%"}).change(function () {
        setExpenseCategory();
    });
    jQuery('#paid_through').chosen({width: "100%"}).change(function () {
        setCurrencyCode();
    });
    expensesForm.bind('submit', function (e) {
        var formIsValid = expensesForm.validationEngine('validate');
        return formIsValid ? true : false;
    });
    //lookup (supplier)
    if(jQuery('#expense-status', expensesForm).val() == 'approved'){
        jQuery(':input', expensesForm).prop('disabled', true);
        jQuery('select', expensesForm).trigger("chosen:updated");
    }
    jQuery("#vendorName", expensesForm).autocomplete({
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
                jQuery('#vendor_id', expensesForm).val(ui.item.record.id);
                jQuery('#tax-number').val(ui.item.record.tax_number);
            } else if (ui.item.record.id === -1) {
                quickAddNewSupplier(ui.item.record.term);
            }
        }
    });
    jQuery("#amount", expensesForm).on("change paste keyup", function() {
        displayTaxAmount();
     });
});

function quickAddNewSupplier(SupplierName) {
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
                                            jQuery('#vendorName', expensesForm).val(response.account.fullName);
                                            jQuery('#vendor_id', expensesForm).val(response.account.model_id);
                                            jQuery('#tax-number').val(response.account.vendor_tax_number);
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
            //jQuery('#vendorName', '#supplierFormDialog').val(SupplierName);
        },
        error: defaultAjaxJSONErrorsHandler
    });
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
            '#amount': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.amount])
                }
            },
            '#paid_through': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.paidThrough])
                }
            },
            '#expense_account': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.expensesCategory])
                }
            },
            '#expense_category_id_chosen': {
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

function checkVendorName() {
    if (jQuery('#vendorName', expensesForm).val() === '') {
        jQuery('#vendor_id', expensesForm).val('');
    }
}

function setCurrencyCode() {
    var selectedAccount = jQuery('#paid_through').find('option:selected');
    var currencyID = selectedAccount.attr('currency');
    var currencyCode = selectedAccount.attr('currencyCode');
    if (selectedAccount.val() === '') {
        jQuery('.paidThroughCurrency').html('?');
        jQuery("#currencyID").val('');
        jQuery("#rate").val('');
        jQuery("#rateText").html('1.00');
    } else {
        jQuery('.paidThroughCurrency').html(currencyCode);
        jQuery("#currencyID").val(currencyID);
        jQuery("#rate").val(exRates[currencyID]);
        jQuery("#rateText").html(exRates[currencyID] * 1);
        displayTaxAmount();
    }
}

function setExpenseCategory() {
    var selectedExpense = jQuery('#expense_category_id').find('option:selected');
    var expense_account = selectedExpense.attr('expense_account');
    var amount = selectedExpense.attr('amount');
    if (selectedExpense.val() === '') {
        jQuery("#expense_account").val('');
        jQuery("#amount").val('');
    } else {
        jQuery("#expense_account").val(expense_account);
        if (!isEditMode) {
            jQuery("#amount").val(amount);
        }
    }
}

function set_account_id() {
    return true;
}

function toggleComments(expenseId,fetchFromServer,expenseCommentId) {
        fetchFromServer = fetchFromServer || false;
    if (jQuery('#comments-list').html() == '' || fetchFromServer) {
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/get_expense_notes/',
            data: {
                'id': expenseId
            },
            type: 'post',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    var commentsList = jQuery('#comments-list');
                    jQuery('#expense-comments-fieldset').removeClass('d-none');

                    commentsList.html(response.html);
                    if (expenseCommentId > 0) {
                        jQuery('span#commentText', jQuery('div.comments-list', commentsList)).slideUp();
                        jQuery('a > i', commentsList).removeClass('fa-solid fa-angle-down');
                        jQuery('a > i', commentsList).addClass('fa-solid fa-angle-right');
                        jQuery('i', '#expense-comment_' + expenseCommentId + ' a:first').removeClass('fa-solid fa-angle-right');
                        jQuery('i', '#expense-comment_' + expenseCommentId + ' a:first').addClass('fa-solid fa-angle-down');
                        jQuery('span#commentText', jQuery('#expense-comment_' + expenseCommentId)).slideDown();
                    }
                    if (!fetchFromServer) {
                        toggle_notes();
                    }
                    if (response.nbOfNotesHistory === 0) {
                        jQuery('#expenseCommentsPaginationContainer').html('');
                    }
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        toggle_notes();
    }
}

function toggleComment(id) {
    expense_comment = jQuery('#expense-comment_' + id);
    if (jQuery('#commentText', expense_comment).is(':visible')) {
        jQuery('#commentText', expense_comment).slideUp();
        jQuery('i', '#expense-comment_' + id + ' a:first').removeClass('fa-solid fa-angle-down');
        jQuery('i', '#expense-comment_' + id + ' a:first').addClass('fa-solid fa-angle-right');
    } else {
        jQuery('#commentText', expense_comment).slideDown();
        jQuery('i', '#expense-comment_' + id + ' a:first').removeClass('fa-solid fa-angle-right');
        jQuery('i', '#expense-comment_' + id + ' a:first').addClass('fa-solid fa-angle-down');
    }
}

function expandAllNotes() {
    var commentsList = jQuery('#commentsList');
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', commentsList)).slideDown();
    jQuery('a > i', commentsList).removeClass('fa-solid fa-angle-right');
    jQuery('a > i', commentsList).addClass('fa-solid fa-angle-down');
}

function collapseAllNotes() {
    var commentsList = jQuery('#commentsList');
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', commentsList)).slideUp();
    jQuery('a > i', commentsList).removeClass('fa-solid fa-angle-down');
    jQuery('a > i', commentsList).addClass('fa-solid fa-angle-right');
}

function toggle_notes() {
    var notesContainer = jQuery('#notes');
    var notesToggleIcon = jQuery('#notesToggleIcon');
    if (notesContainer.is(':visible')) {
        notesContainer.slideUp();
        notesToggleIcon.removeClass('fa-solid fa-angle-down');
        notesToggleIcon.addClass('fa-solid fa-angle-right');
    } else {
        notesContainer.slideDown();
        notesToggleIcon.removeClass('fa-solid fa-angle-right');
        notesToggleIcon.addClass('fa-solid fa-angle-down');
    }
}
