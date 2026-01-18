var enableQuickSearch = false;
var gridOptions = {};
var currencyCode = '';
jQuery(document).ready(function () {
    jQuery('.multi-select', '#invoiceSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery('#invoiceGrid').data('kendoGrid').dataSource.read();
    });
    currencyCode = ' (' + jQuery('#currencyCode').val() + ')';

    jQuery('#invoiceSearchFilters').bind('submit', function (e) {
        jQuery("form#invoiceSearchFilters").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        if (!jQuery('form#invoiceSearchFilters').validationEngine("validate")) {
            return false;
        }
        enableQuickSearch = false;
        e.preventDefault();
        if (jQuery('#submitAndSaveFilter').is(':visible')) {
            gridAdvancedSearchLinkState = true;
        }
        jQuery('#invoiceGrid').data("kendoGrid").dataSource.page(1);
    });
    gridFiltersEvents('Invoice_Header', 'invoiceGrid', 'invoiceSearchFilters');
    gridInitialization();
    jQuery('#invoiceSearchLookUp').val('');
});
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['invoiceDateValue', 'invoiceDateEndValue', 'dueDateValue', 'dueDateEndValue', 'createdOnValue', 'createdOnEndValue', 'modifiedOnValue', 'modifiedOnEndValue']});
    caseLookup(jQuery('#caseIDValue'));
    clientLookup({"lookupField": jQuery("#clientNameValue")});
    clientAccountLookup(jQuery('#accountIDValue'));
    userLookup('assigneeValue');
    userLookup('createdByValue');
    userLookup('modifiedByValue');
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}

function exportInvoicesToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFiltersToInvoice();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL('money') + 'vouchers/invoice_export_to_excel').submit();
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
            jQuery('#accountID').val(ui.item.record.id);
        }
    });
}
function getPaidStatusTranslation(val) {
    return _lang.InvoicePaidStatus[val];
}
function relatedMattersLinks(caseCategories, caseIds) {
    if (caseCategories != null && caseIds !== null) {
        var caseIdsArray = caseIds.split(',');
        var caseCategoriesArray = caseCategories.split(',');
        var template = '';
        for (var i = 0; i < caseIdsArray.length; i++) {
            var controller = caseCategoriesArray[i] != "IP" ? 'cases' : 'intellectual_properties';
            template += '<a href="' + getBaseURL() + controller + '/edit/' + caseIdsArray[i] + '"  >M' + caseIdsArray[i] + '</a>';
            if (i < caseIdsArray.length - 1) {
                template += ',';
            }
        }
        return template;
    }
    return '';
}
function addBreakLine(str) {
    str = str.split(":/;").join(",<br />");
    return str;
}
function currencyRate(clientCurrency, rate) {
    countryCurrencyCode = jQuery('#currencyCode').val()
    if (rate < 1) return '1 ' + clientCurrency + ' = ' + rate + ' ' + countryCurrencyCode
    else {
    rate = parseFloat(rate).toFixed(5);
    return '1 ' + clientCurrency + ' = ' + rate + ' ' + countryCurrencyCode
    }
}

function gridInitialization() {
    currencyCode = ' (' + jQuery('#currencyCode').val() + ')';
    var tableColumns = [];
    tableColumns.push({
        field: 'id', title: ' ', filterable: false, sortable: false,
        template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                // '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/invoice_edit/#= id #">' + _lang.viewEdit + '</a>' +
                '<a class="dropdown-item #= (paidStatus =="cancelled"? "d-none" : "")#" href="javascript:;" onclick="invoiceExport(\'#= id #\', \'preview\')">' + _lang.preview + '</a>' +
                '<a class="dropdown-item #= (paidStatus =="cancelled"? "d-none" : "")#" href="javascript:;" onclick="invoiceExport(\'#= id #\', \'word\')">' + _lang.exportToWord + '</a>' +
                '<a class="dropdown-item #= (paidStatus =="cancelled"? "d-none" : "")#" href="javascript:;" onclick="invoiceExport(\'#= id #\', \'pdf\')">' + _lang.exportToPDF + '</a>' +
                '<a class="dropdown-item #= jQuery.inArray(paidStatus,["draft","cancelled"])!=-1 ? "d-none" : ""#" href="' + getBaseURL('money') + 'vouchers/invoice_payment_add/#= id #">' + _lang.recordPayment + '</a>' +
                '<a class="dropdown-item #= jQuery.inArray(paidStatus,["draft","cancelled"])!=-1 ? "d-none" : ""#" href="' + getBaseURL('money') + 'vouchers/invoice_payments_made/#= id #">' + _lang.paymentMade + '</a>' +
                '<a class="dropdown-item #= jQuery.inArray(paidStatus,["draft","cancelled"])==-1 ? "d-none" : ""#" href="javascript:;" onclick="apiChangeInvoiceStatus(\'#= invoice_header_id #\',\'open\')">' + _lang.convertToOpen + '</a>' +
                '<a class="dropdown-item #= e_invoicing=="active" || jQuery.inArray(paidStatus,["draft","partially paid","paid"])!=-1  || (jQuery.inArray(paidStatus,["overdue"])!=-1 && jQuery.inArray(invoiceStatus,["partially paid","paid"])!=-1) ? "d-none" : ""#" href="javascript:;" onclick="apiChangeInvoiceStatus(\'#= invoice_header_id #\',\'draft\')">' + _lang.setAsDraft + '</a>' +
                '<a class="dropdown-item #= (e_invoicing=="active" && paidStatus !="draft") || jQuery.inArray(paidStatus,["cancelled","partially paid","paid"])!=-1  || (jQuery.inArray(paidStatus,["overdue"])!=-1 && jQuery.inArray(invoiceStatus,["partially paid","paid"])!=-1) ? "d-none" : ""#" href="javascript:;" onclick="apiChangeInvoiceStatus(\'#= invoice_header_id #\',\'cancel\')">' + _lang.cancel + '</a>' +
                '<a href="javascript:;" class="dropdown-item #= (e_invoicing=="active" && paidStatus !="draft") || (jQuery.inArray(paidStatus,["partially paid","paid"])!=-1  || (jQuery.inArray(paidStatus,["overdue"])!=-1 && jQuery.inArray(invoiceStatus,["partially paid","paid"])!=-1)) ? "d-none" : ""#" onclick="confirmationDialog(\'confirm_delete_record\', {resultHandler: apiDeleteInvoiceRecord, parm:  \'#= invoice_header_id #\'} )">' + _lang.delete + '</a>' +
                '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/invoice_details/#= id #">' + _lang.invoiceDetails + '</a>' +
                '<a class="dropdown-item #= partnerShare == "yes" ? "" : "d-none" #" href="' + getBaseURL('money') + 'vouchers/invoice_details/#= id #/internal">' + _lang.invoiceDetailsInternal + '</a>' +
                // '<a class="dropdown-item #= jQuery.inArray(paidStatus,["draft","cancelled"])!=-1 ? "d-none" : ""#" href="' + getBaseURL('money') + 'vouchers/save_credit_note/#= id #">' + _lang.money.createCreditNote + '</a>' +
                // '<a class="dropdown-item #= jQuery.inArray(paidStatus,["draft","cancelled"])!=-1 ? "d-none" : ""#" href="' + getBaseURL('money') + 'vouchers/debit_note_add/#= id #">' + _lang.money.createDebitNote + '</a>' +
                '</div></div>', width: '70px'
    });
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'invoiceID') {
                array_push = {field: "invoiceID", template: '<a href="' + getBaseURL('money') + 'vouchers/invoice_edit/#= id #">#= prefix ##=refNum ##=suffix !=null?suffix:"" #</a>', title: _lang.invoiceNumber, width: '120px'};
            }
            else if (item === 'clientAccount') {
                array_push = {field: "clientAccount", template: '<a href="' + getBaseURL() + '#=model_type=="Company" ? "companies/tab_company/" : "contacts/edit/"##= member_id #">#= clientAccount #</a>', title: _lang.clientsAccount, width: '200px'};
            }
            else if (item === 'account_number') {
                array_push = {field: "account_number", template: '<a href="' + getBaseURL('money') + 'accounts/edit/#= accountID #">#= account_number #</a>', title: _lang.accountTypeAccountNumber.sprintf([_lang.client_Money]), width: '200px'};
            }
            else if (item === 'total') {
                array_push = {field: "total", template: "#= accounting.formatMoney(accounting.toFixed(total, 2), \"\") + ' ' + clientCurrency#", title: _lang.total, width: '160px'};            }
            else if (item === 'totalBaseCurrency') {
                array_push = {field: "totalBaseCurrency", template: "#= accounting.formatMoney(accounting.toFixed(accounting.toFixed(total, 2)*rate, 2), \"\")#", title:currencyCode ? _lang.total + ' ' + currencyCode: _lang.total, width: '160px'};
            }
            else if (item === 'payemntsMade') {
                array_push = {field: "payemntsMade", template: "<span class='#= payemntsMade && payemntsMade > 0 ? 'darkGreen' : '' #'>#= accounting.formatMoney(accounting.toFixed(payemntsMade ? payemntsMade : 0, 2), \"\") #</span>", title: _lang.paymentMade, width: '160px'};
            }
            else if (item === 'balanceDue') {
                array_push = {field: "balanceDue", template: "#= accounting.formatMoney(accounting.toFixed(balanceDue >= 0 ? balanceDue : total, 2), \"\") #", title: _lang.balanceDue, width: '160px'};
            }
            else if (item === 'clientCurrency') {
                array_push = {field: "clientCurrency", title: _lang.currency, width: '108px'};
            }
            else if (item === 'exchangeRate') {
                array_push = {field: "exchangeRate",  template: '#= currencyRate(clientCurrency, rate)#', title: _lang.exchangeRate, width: '150px'};
            }
            else if (item === 'paidStatus') {
                array_push = {field: "paidStatus", title: _lang.status, template: '<span class="#= paidStatus == "partially paid" ? "customBLue" : paidStatus == "open" ? "orange" : paidStatus == "paid" ? "darkGreen" : paidStatus == "overdue" ? "red" : paidStatus == "cancelled" ? "purple" : "" #">#= getPaidStatusTranslation(paidStatus) #</span>', width: '95px'};
            }
            else if (item === 'dated') {
                array_push = {field: "dated", format: "{0:yyyy-MM-dd}", title: _lang.invoiceDate, width: '165px'};
            }
            else if (item === 'dueOn') {
                array_push = {field: "dueOn", format: "{0:yyyy-MM-dd}", title: _lang.dueOn, width: '120px'};
            }
            else if (item === 'purchaseOrder') {
                array_push = {field: "purchaseOrder", title: _lang.purchaseOrder, width: '120px'};
            }
            else if (item === 'refNum') {
                array_push = {field: "refNum", template: '#= referenceNum !=null?referenceNum:"" #', title: _lang.invoiceRef, width: '200px'};
            }
            else if (item === 'case_id') {
                array_push = {field: "case_id", template: '#= relatedMattersLinks(caseCategory, case_id) #', title: _lang.caseId, width: '173px'};
            }
            else if (item === 'caseSubject') {
                array_push = {field: "caseSubject", template: '#= caseSubject!= null ? addBreakLine(caseSubject) : ""  #', title: _lang.caseSubject, width: '210px'};
            }
            else if (item === 'assignee') {
                array_push = {field: "assignee", template: '#= assignee!= null ? addBreakLine(assignee) : ""  #', title: _lang.assignee, width: '210px'};
            }
            else if (item === 'createdByName') {
                array_push = {field: "createdByName", title: _lang.createdBy, width: '145px', template: '#= (createdByName!=null && createdStatus=="Inactive")? createdByName+" ("+_lang.custom[createdStatus]+")":((createdByName!=null)?createdByName:"") #'};
            }
            else if (item === 'createdOn') {
                array_push = {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '125px'};
            }
            else if (item === 'modifiedByName') {
                array_push = {field: "modifiedByName", title: _lang.modifiedBy, width: '140px', template: '#= (modifiedByName!=null && modifiedStatus=="Inactive")? modifiedByName+" ("+_lang.custom[modifiedStatus]+")":((modifiedByName!=null)?modifiedByName:"") #'};
            }
            else if (item === 'modifiedOn') {
                array_push = {field: "modifiedOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '125px'};
            }
            else if (item === 'totalTax') {
                array_push = {field: "totalTax", template: "#= accounting.formatMoney(accounting.toFixed(totalTax, 2), \"\") + ' ' + clientCurrency #", title: _lang.total+' '+ _lang.tax, width: '160px'};
            }
            else if (item === 'totalTaxBaseCurrency') {
                array_push = {field: "totalTaxBaseCurrency", template: "#= accounting.formatMoney(accounting.toFixed(accounting.toFixed(totalTax, 2)*rate, 2), \"\")#", title: currencyCode ? _lang.total+' '+ _lang.tax + ' ' + currencyCode: _lang.total+' '+ _lang.tax, width: '160px'};
            }
            else if (item === 'subTotal') {
                array_push = {field: "subTotal", template: "#= accounting.formatMoney(accounting.toFixed(subTotal, 2), \"\") + ' ' + clientCurrency#", title: _lang.subTotal, width: '160px'};
            }
            else if (item === 'subTotalBaseCurrency') {
                array_push = {field: "subTotalBaseCurrency", template: "#= accounting.formatMoney(accounting.toFixed(accounting.toFixed(subTotal, 2)*rate, 2), \"\")#", title: currencyCode ? _lang.subTotal + ' ' + currencyCode: _lang.subTotal , width: '160px'};
            }
            else if (item === 'totalDiscount') {
                array_push = {field: "totalDiscount", template: "#= accounting.formatMoney(accounting.toFixed(totalDiscount, 2), \"\") + ' ' + clientCurrency #", title: _lang.totalDiscount, width: '160px'};
            }
            else if (item === 'totalDiscountBaseCurrency') {
                array_push = {field: "totalDiscountBaseCurrency", template: "#= accounting.formatMoney(accounting.toFixed(accounting.toFixed(totalDiscount, 2)*rate, 2), \"\")#", title: currencyCode ? _lang.totalDiscount + ' ' + currencyCode: _lang.totalDiscount, width: '160px'};
            }
            else if (item === 'sub_total_after_discount') {
                array_push = {field: "sub_total_after_discount", template: "#= accounting.formatMoney(accounting.toFixed(sub_total_after_discount, 2), \"\") + ' ' + clientCurrency #", title: _lang.subTotalAfterDiscount, width: '160px'};
            }
            else if (item === 'sub_total_after_discount_base_currency') {
                array_push = {field: "sub_total_after_discount_base_currency", template: "#= accounting.formatMoney(accounting.toFixed(accounting.toFixed(sub_total_after_discount, 2)*rate, 2), \"\")#", title: currencyCode ? _lang.subTotalAfterDiscount + ' ' + currencyCode : _lang.subTotalAfterDiscount, width: '160px'};
            }
            else if (item === 'taxable') {
                array_push = {field: "taxable", template: "#= accounting.formatMoney(accounting.toFixed(taxable, 2), \"\") #", title: _lang.taxable, width: '160px'};
            }
            else if (item === 'nonTaxable') {
                array_push = {field: "nonTaxable", template: "#= accounting.formatMoney(accounting.toFixed(nonTaxable, 2), \"\") #", title: _lang.nonTaxable, width: '160px'};
            }
            else if (item === 'practice_area') {
                array_push = {field: "practice_area", title: _lang.caseType, width: '210px'};
            }
            else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    try {
        var gridDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL('money') + "vouchers/invoices_list",
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
                                pinesMessage({ty: $response.feedbackMessage.ty, m: $response.feedbackMessage.m});
                            } else {
                                pinesMessage({ty: 'error', m: _lang.updatesFailed});
                            }
                        }
                        animateDropdownMenuInGrids('invoiceGrid');
                        if ($response.columns_html) {
                            jQuery('#column-picker-trigger-container').html($response.columns_html);
                            jQuery('*[data-callexport]').on('click', function () {
                                if(hasAccessToExport!=1){
                                    pinesMessage({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                                }else {
                                    if ($response.totalRows <= 10000) {
                                        if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                            exportInvoicesToExcel(true);
                                        } else {
                                            exportInvoicesToExcel();
                                        }
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
                            setGridFiltersData(gridFormData, 'invoiceGrid');
                            options.loadWithSavedFilters = 1;
                            options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                            gridSavedFiltersParams = '';
                        }
                        else {
                            options.filter = checkWhichTypeOfFilterIUseAndReturnFiltersToInvoice();
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
                        id: {editable: false, type: "number"},
                        invoiceID: {type: "number"},
                        accountID: {type: "number"},
                        voucherType: {type: "string"},
                        total: {type: "number"},
                        totalBaseCurrency: {type: "number"}, 
                        payemntsMade: {type: "number"},
                        dated: {type: "date"},
                        dueOn: {type: "date"},
                        paidStatus: {type: "string"},
                        description: {type: "string"},
                        clientAccount: {type: "string"},
                        clientCurrency: {type: "string"},
                        exchangeRate: {type: "string"},
                        balanceDue: {type: "number"},
                        purchaseOrder: {type: "string"},
                        refNum: {type: "string"},
                        caseID: {type: "string"},
                        case_id: {type: "string"},
                        caseSubject: {type: "string"},
                        assignee: {type: "string"},
                        member_id: {type: "number"},
                        model_type: {type: "string"},
                        createdByName: {type: "string"},
                        createdOn: {type: "date"},
                        modifiedByName: {type: "string"},
                        modifiedOn: {type: "date"},
                        displayDiscount: {type: "number"},
                        account_number: {type: "string"}
                    }
                },
                parse: function(response) {
                    var rows = [];
                    if(response.data){
                        var data = response.data;
                        rows = response;
                        rows.data = [];
                        for (var i = 0; i < data.length; i++) {
                            var row = data[i];
                            row['clientAccount'] = escapeHtml(row['clientAccount']);
                            row['account_number'] = escapeHtml(row['account_number']);
                            row['referenceNum'] = escapeHtml(row['referenceNum']);
                            row['caseSubject'] = escapeHtml(row['caseSubject']);
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
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: "single",
            sortable: {mode: "multiple"},
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
    gridTriggers({'gridContainer': jQuery('#invoiceGrid'), 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
}

function checkWhichTypeOfFilterIUseAndReturnFiltersToInvoice() {
    var filtersForm = jQuery('#invoiceSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('invoiceSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterInvoiceValue', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}

function apiMoneyBaseUrl() {
    return window.apiModuleGlobal.getApiBaseUrl("money");   //getBaseURL() + "api/v2/money"
}

function apiGetInitialHeaders(type, accept) {
    var header = window.apiModuleGlobal.getInitialHeaders(type, accept);
    return header[Object.keys(header)[0]];
}

function apiChangeInvoiceStatus(invoiceId, status) {
    var url = '';
    var msg101 = '';
    var msg102 = '';
    switch (status) {
        case 'open':
            url = apiMoneyBaseUrl() + '/invoices/' + invoiceId + '/status/open?organization_id=' + organizationIDGlobal;
            msg101 = _lang.invoiceHasBeenConvertedToOpen;
            msg202 = _lang.invalid_request;
            break;
        case 'draft':
            url = apiMoneyBaseUrl() + '/invoices/' + invoiceId + '/status/draft?organization_id=' + organizationIDGlobal;
            msg101 = _lang.invoiceHasBeenSetAsDraft;
            msg202 = _lang.you_can_not_set_paid_or_partially_paid_invoice_as_draft;
            break;
        case 'cancel':
            url = apiMoneyBaseUrl() + '/invoices/' + invoiceId + '/status/cancelled?organization_id=' + organizationIDGlobal;
            msg101 = _lang.invoiceHasBeenCancelled;
            msg202 = _lang.you_can_not_cancel_any_paid_or_partially_paid_invoice;
            break;
    }
    //
    initApiAccessToken(function () {
        jQuery.ajax({
            url: url,
            headers: apiGetInitialHeaders(),
            type: 'PATCH',
            dataType: 'JSON',
            data: {},
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                pinesMessageV2({ ty: 'success', m: /*data.message*/ msg101 });
                if (jQuery('#invoiceGrid').length > 0)
                    jQuery('#invoiceGrid').data("kendoGrid").dataSource.read();
            },
            complete: function (XHRObj) {
                jQuery('#loader-global').hide();
            },
            error: function (jqXHR, textStatus, errorThrown ) {
                pinesMessageV2({ ty: 'error', m: jqXHR.responseJSON?.message ? jqXHR.responseJSON.message : _lang.feedback_messages.error + ': ' + errorThrown });
                if (jqXHR.status == 401) 
                    localStorage.removeItem('api-access-token');
                setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoices_list/', 700);
            }
        });
    });
}

function apiDeleteInvoiceRecord(invoiceId) {
    initApiAccessToken(function () {
        jQuery.ajax({
            url: apiMoneyBaseUrl() + '/invoices/' + invoiceId + '?organization_id=' + organizationIDGlobal,
            headers: apiGetInitialHeaders(),
            type: 'DELETE',
            dataType: "json",
            data: {},
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (data) {
                pinesMessage({ ty: 'success', m: _lang.deleteRecordSuccessfull });
                if (jQuery('#invoiceGrid').length > 0)
                    jQuery('#invoiceGrid').data("kendoGrid").dataSource.read();
            },
            complete: function (XHRObj) {
                jQuery('#loader-global').hide();
            },
            error: function (jqXHR, textStatus, errorThrown ) {
                pinesMessageV2({ ty: 'error', m: jqXHR.responseJSON?.message ? jqXHR.responseJSON.message : _lang.feedback_messages.error + ': ' + errorThrown });
                if (jqXHR.status == 401) 
                    localStorage.removeItem('api-access-token');
                setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoices_list/', 700);
            }
        });
    });
}

function exportSubmit(id, container) {
    var template = jQuery('#export-template option:selected',container).val();

    format = jQuery('#export-type option:selected',container).val();
    if(format != 'preview' && format != 'pdf')
        format = '';    //for word
    
    var urlAction = getBaseURL('money') + 'vouchers/invoice_export_to_word/' + id + '/' + template + '/' + format;
    if(format == 'preview')
        openFileViewer(urlAction);
    else{
        jQuery("#export-options").attr('action', urlAction);
        jQuery("#export-options").submit();
    }
    jQuery(".modal", container).modal("hide");
}