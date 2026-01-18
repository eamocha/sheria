var enableQuickSearch = false;
var gridOptions = {};
jQuery(document).ready(function () {
    if (jQuery("#expenseGrid").length) {
        jQuery('.multi-select', '#expenseSearchFilters').chosen({
            no_results_text: _lang.no_results_matched,
            placeholder_text: _lang.select,
            width: "100%"
        }).change();
        jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
            jQuery('#expenseGrid').data('kendoGrid').dataSource.read();
        });
        jQuery('#expenseSearchFilters').bind('submit', function (e) {
            jQuery("form#expenseSearchFilters").validationEngine({
                validationEventTrigger: "submit",
                autoPositionUpdate: true,
                promptPosition: 'bottomRight',
                scroll: false
            });
            if (!jQuery('form#expenseSearchFilters').validationEngine("validate")) {
                return false;
            }
            enableQuickSearch = false;
            e.preventDefault();
            if (jQuery('#submitAndSaveFilter').is(':visible')) {
                gridAdvancedSearchLinkState = true;
            }
            jQuery('#expenseGrid').data("kendoGrid").dataSource.page(1);
        });
        gridFiltersEvents('Expense', 'expenseGrid', 'expenseSearchFilters');
        gridInitialization();
        jQuery('#expenseSearchLookUp').val('');
    }
    jQuery('#gridFiltersList', jQuery('#gridFormContent')).change(function () {
        var expensesUrl = window.location.href;
        var lastIndex = expensesUrl.lastIndexOf("/");
        if (typeof jQuery.parseJSON(uriSegmentDecoded)[4] !== 'undefined') {
            var newExpensesUrl = window.location.href.substring(0, lastIndex);
            var newExpensesUrlLastIndex = newExpensesUrl.lastIndexOf("/");
            window.location.href = newExpensesUrl.substring(0, newExpensesUrlLastIndex);
        }
    });
});
function expenseQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterExpenseValue', '#filtersFormWrapper').val(term);
        jQuery('#expenseGrid').data("kendoGrid").dataSource.page(1);
    }
}
function loadEventsForFilters() {
    makeFieldsDatePicker({ fields: ['paidOnValue', 'paidOnEndValue', 'createdOnValue', 'createdOnEndValue', 'modifiedOnValue', 'modifiedOnEndValue'] });
    caseLookup(jQuery('#caseValue'));
    clientLookup({ "lookupField": jQuery("#clientValue") });
    clientAccountLookup(jQuery('#accountNameValue'));
    supplierLookup(jQuery('#vendorValue'));
    userLookup('createdByValue');
    userLookup('modifiedByValue');
    taskLookup(jQuery('#task_idValue'));
    relateToLookup({container: jQuery('#expenseSearchFilters'), input: "#hearingValue", hiddenInput: '#hearingIdHiddenFilter',funtionName : 'hearings_autocomplete'});
    relateToLookup({container: jQuery('#expenseSearchFilters'), input: "#eventValue", hiddenInput: '#eventIdHiddenFilter',funtionName : 'events_autocomplete'});
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}

function deleteExpense(voucherID) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/expense_delete',
            type: 'POST',
            dataType: 'JSON',
            data: { voucherID: voucherID },
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 'success':	// removed successfuly
                        ty = 'information';
                        m = _lang.selectedExpenseDeleted;
                        break;
                    case 'failed':	// could not remove record
                        ty = 'warning';
                        m = _lang.recordNotDeleted;
                        break;
                    case 'exists_invoice_fk':	// foreign key to invoice table exists
                        ty = 'warning';
                        m = _lang.expenseRelatedToInvoice;
                        break;
                    default:
                        break;
                }
                pinesMessage({ ty: ty, m: m });
                jQuery('#expenseGrid').data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function changeBillingStatus(voucherID) {
    expenseStatusFormDialog = jQuery('#expenseStatusFormDialog');
    jQuery.ajax({
        data: { voucher_id: voucherID },
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'vouchers/change_expense_status',
        success: function (response) {
            expenseStatusFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info',
                    id: 'btnSubmitSave',
                    click: function () {
                        var dataIsValid = jQuery("form#expenseStatusForm", this).validationEngine('validate');
                        var formData = jQuery("form#expenseStatusForm", this).serialize();
                        if (dataIsValid) {
                            var that = this;
                            jQuery.ajax({
                                beforeSend: function () {
                                },
                                data: formData,
                                dataType: 'JSON',
                                type: 'POST',
                                url: getBaseURL('money') + 'vouchers/change_expense_status',
                                success: function (response) {
                                    if (!response.status) {
                                        for (i in response.validationErrors) {
                                            pinesMessage({ ty: 'error', m: response.validationErrors[i] });
                                        }
                                    } else {
                                        if (response.client_added_to_case) {
                                            pinesMessage({ ty: 'success', m: _lang.feedback_messages.clientAddedToCase });
                                        }
                                        pinesMessage({ ty: 'success', m: _lang.expenseStatusChangedSuccessfully.sprintf([_lang.ExpenseStatus[jQuery('#billingStatus:checked').val()]]) });
                                        jQuery(that).dialog("close");
                                        jQuery('#expenseGrid').data("kendoGrid").dataSource.read();
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
                        resizeNewDialogWindow(that, '70%', '450');
                    }));
                    resizeNewDialogWindow(that, '70%', '450');
                },
                resizable: true,
                responsive: true,
                title: _lang.changeBillingStatus
            });
            expenseStatusFormDialog.html(response.html);
        }, error: defaultAjaxJSONErrorsHandler
    });
}
function changeExpenseStatus(voucherID, status, action) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/move_expense_status_to_' + status,
        type: "POST",
        data: { voucher_id: voucherID },
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                jQuery('.widget-data', '#expense-status-container').tooltipster('hide');
                var expenseStatusId = "#expense-status-container";
                if (jQuery(expenseStatusId).length <= 0) {
                    jQuery("<div id='expense-status-container'></div>").appendTo("body");
                    var expenseStatusContainer = jQuery(expenseStatusId);
                    expenseStatusContainer.html(response.html);
                    commonModalDialogEvents(expenseStatusContainer);
                    jQuery("#form-submit", expenseStatusContainer).click(function () {
                        expenseStatusSubmit(expenseStatusContainer, status, action);
                    });
                    jQuery(expenseStatusContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            expenseStatusSubmit(expenseStatusContainer);
                        }
                    });
                }
            } else {
                pinesMessage({ ty: 'error', m: _lang.invalid_record });
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function expenseStatusSubmit(container, status, action) {
    var formData = jQuery('form#expense-status-form', container).serialize();
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/move_expense_status_to_' + status,
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (!response.status) {
                displayValidationErrors(response.validationErrors, container);
                if (response.message) {
                    pinesMessage({ ty: 'error', m: response.message });
                } else {
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.expenseStatusNotUpdated });
                }
            } else {
                if (response.warning) {
                    pinesMessage({ ty: 'warning', m: response.warning });
                }
                if (action == "reload") {
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                } else {
                    jQuery(".modal", container).modal("hide");
                    pinesMessage({ ty: 'success', m: _lang.expenseStatusChangedSuccessfully.sprintf([getTranslation(response.expense_status)]) });
                    jQuery('#expenseGrid').data("kendoGrid").dataSource.read();
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function exportExpnsesToExcel(expenseType, exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFiltersToExpense();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL('money') + 'vouchers/expense_export_to_excel/' + expenseType).submit();
}
function clientAccountLookup(clientAccountID) {
    clientAccountID.autocomplete({
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
                                label: item.name + ' - ' + item.currencyCode,
                                value: item.name + ' - ' + item.currencyCode,
                                record: item
                            };
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            jQuery('#clientAccountID').val(ui.item.record.id);
        }
    });
}
function supplierLookup(supplierName) {
    supplierName.autocomplete({
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
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
}
function getExpenseStatusTranslation(val) {
    return _lang.ExpenseStatus[val];
}
function validateIntegers(field, rules, i, options) {
    var val = field.val();
    var integerPattern = /^(?:[1-9]\d*|0)$/;
    if (!integerPattern.test(val)) {
        return _lang.integerAllowed;
    }
}

function validateDecimals(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
    if (!decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
}

function gridInitialization() {
    var tableColumns = [];
    tableColumns.push({
        field: 'actionsCol',
        title: ' ',
        filterable: false,
        sortable: false,
        template: '<input type="checkbox" name="voucherIds[]" id="voucherId_#= id #" value="#= id #" onchange="toggleBulkActions(this)"/>' + '<div class="dropdown more">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
            '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/expense_edit/#= id #">' + _lang.viewEdit + '</a>' +
            '<a class="dropdown-item #= jQuery.inArray(billingStatus,["invoiced","reimbursed"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="changeBillingStatus(#= id #);">' + _lang.changeBillingStatus + '</a>' +
            '<a class="dropdown-item #= jQuery.inArray(status,["approved"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="changeExpenseStatus(#= id #,\'approved\');">' + _lang.approve + '</a>' +
            '<a class="dropdown-item #= jQuery.inArray(billingStatus,["invoiced","reimbursed"])!=-1 || jQuery.inArray(status,["open"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="changeExpenseStatus(#= id #,\'open\');">' + _lang.backToOpen + '</a>' +
            '<a class="dropdown-item #= jQuery.inArray(billingStatus,["invoiced","reimbursed"])!=-1 || jQuery.inArray(status,["needs_revision"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="changeExpenseStatus(#= id #,\'needs_revision\');">' + _lang.moveToNeedsRevision + '</a>' +
            '<a class="dropdown-item #= jQuery.inArray(billingStatus,["invoiced","reimbursed"])!=-1 || jQuery.inArray(status,["cancelled"])!=-1  ? "d-none" : ""#" href="javascript:;" onclick="changeExpenseStatus(#= id #,\'cancelled\');">' + _lang.cancel + '</a>' +
            '<a class="dropdown-item #= jQuery.inArray(billingStatus,["invoiced","reimbursed"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="deleteExpense(#= id #);">' + _lang.deleteRow + '</a>' +
            '</div></div>',
        width: '70px'
    });
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'dated') {
                array_push = { field: "dated", title: _lang.paidOn, format: "{0:yyyy-MM-dd}", width: '120px' };
            } else if (item === 'expenseID') {
                array_push = { field: "expenseID", template: '<a href="' + getBaseURL('money') + 'vouchers/expense_edit/#= id #">#= str_pad(expenseID, 8, "0", "STR_PAD_LEFT") #</a>', title: _lang.expenseID, width: '120px' };
            } else if (item === 'referenceNum') {
                array_push = { field: "referenceNum", title: _lang.internalReferenceNb, width: '150px' };
            } else if (item === 'expenseCategory') {
                array_push = { field: "expenseCategory", title: _lang.expenseCategory, width: '150px' };
            } else if (item === 'amount') {
                array_push = { field: "amount", template: "#= accounting.formatMoney(accounting.toFixed(amount, 2), \"\") #", title: _lang.expenseAmount, width: '150px' };
            }
            else if (item === 'paymentMethod') {
                array_push = { field: "paymentMethod", template: '#= paymentMethod #', title: _lang.paymentMethod, width: '200px' };
            }
            else if (item === 'paidThroughAccount') {
                array_push = { field: "paidThroughAccount", template: '#= paidThroughAccount #', title: _lang.paidThrough, width: '200px' };
            }
            else if (item === 'paid_through_account_number') {
                array_push = { field: "paid_through_account_number", template: '#= paid_through_account_number #', title: _lang.accountTypeAccountNumber.sprintf([_lang.paidThrough]), width: '200px' };
            }
            else if (item === 'supplier') {
                array_push = { field: "supplier", title: _lang.supplierName, width: '160px' };
            }
            else if (item === 'billingStatus') {
                array_push = { field: "billingStatus", title: _lang.billingStatus, template: '<span class="#= billingStatus == "invoiced" ? "lightGreen" : billingStatus == "to-invoice" ? "red" : billingStatus == "reimbursed" ? "darkGreen" : "" #">#= getExpenseStatusTranslation(billingStatus) #</span>', width: '133px' };
            }
            else if (item === 'status') {
                array_push = { field: "status", title: _lang.status, template: '<span class="#= status == "approved" ? "darkGreen" : status == "needs_revision" ? "red" : status == "cancelled" ? "purple" : status=="open" ? "orange" : "" #">#= getTranslation(status) #</span>', width: '145px' };
            }
            else if (item === 'clientName') {
                array_push = { field: "clientName", template: '<a href="' + getBaseURL('money') + 'clients/client_details/#= clientID #">#= clientName ? clientName : "" #</a>', title: _lang.clientName_Money, width: '200px' };
            }
            else if (item === 'clientAccount') {
                array_push = { field: "clientAccount", title: _lang.clientAccount, width: '200px' };
            }
            else if (item === 'client_account_number') {
                array_push = { field: "client_account_number", template: '#= client_account_number != null ? client_account_number :  "" #', title: _lang.accountTypeAccountNumber.sprintf([_lang.client_Money]), width: '200px' };
            }
            else if (item === 'caseID') {
                array_push = { field: "caseID", template: '<a href="' + getBaseURL() + '#= (caseCategory!="IP") ? "cases/edit/"+case_id : "intellectual_properties/edit/"+case_id #">#= case_id ? caseID : "" #</a>', title: _lang.caseId, width: '136px' };
            }
            else if (item === 'caseSubject') {
                array_push = { field: "caseSubject", title: _lang.caseSubject, width: '200px' };
            }
            else if (item === 'task_id') {
                array_push = { field: "task_id", title: _lang.task, template: '<a href="' + getBaseURL() + '#= "tasks/view/" + task_id #">#= task_id ? task_id : "" #</a>', title: _lang.task, width: '136px' };
            }
            else if (item === 'hearing') {
                array_push = { field: "hearing", title: _lang.hearing, template: '#= hearing ? hearing : "" #', title: _lang.hearing, width: '136px' };
            }
            else if (item === 'event') {
                array_push = { field: "event", title: _lang.event, template: '#= event ? event : "" #</a>', title: _lang.event, width: '136px' };
            }
            else if (item === 'description') {
                array_push = { field: "description", title: _lang.comments, template: '#= (description!=null&&description!="") ? description.substring(0,40)+"..." : ""#', width: '350px' };
            }
            else if (item === 'createdByName') {
                array_push = { field: "createdByName", title: _lang.createdBy, width: '145px', template: '#= (createdByName!=null ? createdByName : "") #' };
            }
            else if (item === 'createdOn') {
                array_push = { field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '130px' };
            }
            else if (item === 'modifiedByName') {
                array_push = { field: "modifiedByName", title: _lang.modifiedBy, width: '140px', template: '#= (modifiedByName!=null ? modifiedByName : "") #' };
            }
            else if (item === 'modifiedOn') {
                array_push = { field: "modifiedOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '145px' };
            }
            else if (item === 'totalTax') {
                array_push = { field: "totalTax", template: "#= accounting.formatMoney(accounting.toFixed(totalTax, 2), \"\") #", title: _lang.total + ' ' + _lang.tax, width: '160px' };
            }
            else if (item === 'subTotal') {
                array_push = { field: "subTotal", template: "#= accounting.formatMoney(accounting.toFixed(subTotal, 2), \"\") #", title: _lang.subTotal, width: '160px' };
            }
            else {
                array_push = { field: item, title: getTranslation(item), width: '182px' };
            }
            tableColumns.push(array_push);
        });
    }
    try {
        var gridDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: ((!myExpenses) ? getBaseURL('money') + "vouchers/expenses_list" : getBaseURL('money') + "vouchers/my_expenses_list/"),
                    dataType: "JSON",
                    type: "POST",
                    complete: function (XHRObj) {
                        jQuery('#loader-global').hide();
                        if (XHRObj.responseText == 'access_denied') {
                            return false;
                        }
                        $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        if ($response.result != undefined && !$response.result) {
                            if ($response.gridDetails != undefined) {
                                setGridDetails($response.gridDetails);
                            }
                            if ($response.feedbackMessage != undefined) {
                                pinesMessage({ ty: $response.feedbackMessage.ty, m: $response.feedbackMessage.m });
                            } else {
                                pinesMessage({ ty: 'error', m: _lang.updatesFailed });
                            }
                        }
                        animateDropdownMenuInGrids('expenseGrid');
                        if ($response.columns_html) {
                            jQuery('#column-picker-trigger-container').html($response.columns_html);
                            jQuery('*[data-callexport]').on('click', function () {
                                if (hasAccessToExport != 1) {
                                    pinesMessage({ ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page });
                                } else {
                                    // we are retrieving up to 10000 rows only, because the excel lib can't handle more than that!
                                    if ($response.totalRows <= 10000) {
                                        eval(jQuery(this).data('callexport'));
                                    } else {
                                        applyExportingModuleMethod(this);
                                    }
                                }
                            });
                            gridEvents();
                            loadExportModalRanges($response.totalRows);
                        }
                        if (gridAdvancedSearchLinkState) {
                            gridAdvancedSearchLinkState = false;
                        } else {
                            if (jQuery('#filtersFormWrapper').is(':visible')) {
                                jQuery('#filtersFormWrapper').slideUp();
                                scrollToId('#filtersFormWrapper');
                            }
                        }
                    },
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" == operation) {
                        options.loadWithSavedFilters = 0;
                        if (gridSavedFiltersParams) {
                            options.filter = gridSavedFiltersParams;
                            var gridFormData = [];
                            gridFormData.formData = ["gridFilters"];
                            gridFormData.formData.gridFilters = gridSavedFiltersParams;
                            setGridFiltersData(gridFormData, 'expenseGrid');
                            options.loadWithSavedFilters = 1;
                            options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                            gridSavedFiltersParams = '';
                        }
                        else {
                            options.filter = checkWhichTypeOfFilterIUseAndReturnFiltersToExpense();
                        }
                        options.sortData = JSON.stringify(gridDataSrc.sort());
                        jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    }
                    return options;
                }
            },
            schema: {
                type: "json",
                data: "data",
                total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: { editable: false, type: "number" },
                        dated: { type: "date" },
                        expenseID: { type: "number" },
                        expenseCategory: { type: "string" },
                        amount: { type: "number" },
                        referenceNum: { type: "string" },
                        paidThroughID: { type: "string" },
                        paidThroughAccount: { type: "string" },
                        billingStatus: { type: "string" },
                        supplier: { type: "string" },
                        clientID: { type: "number" },
                        clientName: { type: "string" },
                        clientAccountID: { type: "number" },
                        clientAccount: { type: "string" },
                        case_id: { type: "string" },
                        caseID: { type: "string" },
                        caseSubject: { type: "string" },
                        task_id: { type: "string" },
                        hearing: { type: "string" },
                        event: { type: "string" },
                        description: { type: "string" },
                        createdByName: { type: "string" },
                        createdOn: { type: "date" },
                        modifiedByName: { type: "string" },
                        modifiedOn: { type: "date" },
                        paid_through_account_number: { type: "string" },
                        client_account_number: { type: "string" },
                        status: { type: "string" },
                        subTotal: { type: "number" },
                        totalTax: { type: "number" },
                        taxNumber: { type: "string" }
                    }
                },
                parse: function (response) {
                    var rows = [];
                    if (response.data) {
                        var data = response.data;
                        rows = response;
                        rows.data = [];
                        for (var i = 0; i < data.length; i++) {
                            var row = data[i];
                            row['paidThroughAccount'] = escapeHtml(row['paidThroughAccount']);
                            row['paid_through_account_number'] = escapeHtml(row['paid_through_account_number']);
                            row['clientName'] = escapeHtml(row['clientName']);
                            row['client_account_number'] = escapeHtml(row['client_account_number']);
                            row['description'] = escapeHtml(row['description']);
                            row['createdByName'] = escapeHtml(row['createdByName']);
                            row['modifiedByName'] = escapeHtml(row['modifiedByName']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            },
            error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            page: 1,
            pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
            sort: jQuery.parseJSON(gridSavedColumnsSorting || "null"),
        });
        gridOptions = {
            autobind: true,
            dataSource: gridDataSrc,
            columns: tableColumns,
            editable: false,
            filterable: false,
            height: 480,
            pageable: { input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true },
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: "single",
            sortable: { mode: "multiple" },
            toolbar: [{
                name: "toolbar-menu",
                template: '<div></div>'

            }],
            columnResize: function (e) {
                fixFooterPosition();
                resizeHeaderAndFooter();
            },
            columnReorder: function (e) {
                orderColumns(e);
            }
        };
    } catch (e) {
    }
    gridTriggers({ 'gridContainer': jQuery('#expenseGrid'), 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length });
    var grid = jQuery('#expenseGrid').data('kendoGrid');
    grid.thead.find("th:first").append(jQuery('<input id="selectAllCheckboxes" class="selectAll" type="checkbox" title"' + _lang.selectAllRecords + '" onclick="checkUncheckAllCheckboxes(this);" />'));

}

function checkWhichTypeOfFilterIUseAndReturnFiltersToExpense() {
    var filtersForm = jQuery('#expenseSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('expenseSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterExpenseValue', filtersForm).val() || jQuery('#quickSearchFilterExpensePaidThroghValue', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function checkUncheckAllCheckboxes(statusChkBx) {
    if (statusChkBx.checked && jQuery("tbody" + " INPUT[type='checkbox']").length >= 1) {
        jQuery('#bulk-open-button').removeClass('d-none');
        jQuery('#bulk-approve-button').removeClass('d-none');
    } else {
        jQuery('#bulk-open-button').addClass('d-none');
        jQuery('#bulk-approve-button').addClass('d-none');
    }
    jQuery("tbody" + " INPUT[type='checkbox']").attr('checked', statusChkBx.checked);
}

function toggleBulkActions(statusChkBx) {
    if (statusChkBx.checked) {
        jQuery('#bulk-open-button').removeClass('d-none');
        jQuery('#bulk-approve-button').removeClass('d-none');
    }
    else if (!statusChkBx.checked && (jQuery("tbody" + " INPUT[type='checkbox']:checked").length == 0)) {
        jQuery('#bulk-open-button').addClass('d-none');
        jQuery('#bulk-approve-button').addClass('d-none');
    }
}
function bulkChangeExpenseStatus(status) {
    jQuery.ajax({
        url: status == 'open' ? getBaseURL('money') + "vouchers/bulk_change_expense_status_to_open" : getBaseURL('money') + "vouchers/bulk_change_expense_status_to_approved",
        type: 'POST',
        dataType: 'JSON',
        data: {
            gridData: form2js('gridFormContent', '.', true)
        },
        success: function (response) {
            var ty = 'warning';
            var m = '';
            switch (response.flag) {
                case true:   // saved successfuly
                    ty = 'information';
                    m = _lang.feedback_messages.updatesSavedSuccessfully;
                    break;
                case false:   // could not save records
                    m = _lang.feedback_messages.expenseStatusNotUpdated;
                    break;
                default:
                    break;
            }
            pinesMessage({ ty: ty, m: m });
            jQuery('#expenseGrid').data("kendoGrid").dataSource.read();
            jQuery('#bulk-open-button').addClass('d-none');
            jQuery('#bulk-approve-button').addClass('d-none');
            jQuery("tbody" + " INPUT[type='checkbox']").attr('checked', false);
            jQuery("#selectAllCheckboxes").attr('checked', false);
        },
        error: defaultAjaxJSONErrorsHandler
    });

}