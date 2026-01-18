var enableQuickSearch = false;
var gridOptions = {};
var currencyCode = '';
jQuery(document).ready(function () {
    jQuery('.multi-select', '#billSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery('#billGrid').data('kendoGrid').dataSource.read();
    });
    jQuery('#billSearchFilters').bind('submit', function (e) {
        jQuery("form#billSearchFilters").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        if (!jQuery('form#billSearchFilters').validationEngine("validate")) {
            return false;
        }
        enableQuickSearch = false;
        e.preventDefault();
        if (jQuery('#submitAndSaveFilter').is(':visible')) {
            gridAdvancedSearchLinkState = true;
        }
        jQuery('#billGrid').data("kendoGrid").dataSource.page(1);
    });
    gridFiltersEvents('Bill_Header', 'billGrid', 'billSearchFilters');
    gridInitialization();
    jQuery('#billLookUp').val('');
});
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['billDateValue', 'billDateEndValue', 'dueDateValue', 'dueDateEndValue', 'createdOnValue', 'createdOnEndValue', 'modifiedOnValue', 'modifiedOnEndValue']});
    caseLookup(jQuery('#caseValue'));
    clientLookup({"lookupField": jQuery("#clientValue")});
    supplierAccountLookup(jQuery('#supplierAccountValue'));
    supplierLookup(jQuery('#billNameValue'));
    userLookup('createdByValue');
    userLookup('modifiedByValue');
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function exportBillsToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFiltersToBill();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    newFormFilter.attr('action', getBaseURL('money') + 'vouchers/bill_export_to_excel').submit();
}
function supplierAccountLookup(supplierAccountID) {
    supplierAccountID.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL('money') + 'accounts/lookup_supplier',
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
                                label: item.name + ' - ' + item.currencyCode + ' (' + item.account_number + ')',
                                value: item.name + ' - ' + item.currencyCode,
                                record: item
                            };
                        }));
                    }
                }
            });
        }, error: defaultAjaxJSONErrorsHandler,
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            jQuery('#accountID').val(ui.item.record.id);
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
                }, error: defaultAjaxJSONErrorsHandler
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
}
function getBillStatusTranslation(val) {
    return _lang.BillStatus[val];
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
function deleteBillRecord(billId) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/delete_bill/' + billId,
        dataType: "json",
        data: {},
        success: function (data) {
            if (data.status) {
                jQuery('#billGrid').data("kendoGrid").dataSource.read();
                pinesMessage({ty: 'success', m: _lang.deleteRecordSuccessfull});
            } else {
                pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
            }
        }
    });

}
function gridInitialization() {
    var tableColumns = [];
    tableColumns.push({
        field: 'id', title: ' ', filterable: false, sortable: false,
        template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/bill_edit/#= id #">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/bill_payment_add/#= id #">' + _lang.recordPayment + '</a>' +
                    '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/bill_payments_made/#= id #">' + _lang.paymentMade + '</a>' +
                    '<a class="dropdown-item #= jQuery.inArray(billStatus,["partially paid","paid"])!=-1 || (jQuery.inArray(billStatus,["overdue"])!=-1 && jQuery.inArray(status,["partially paid","paid"])!=-1) ? "hide" : ""#" onclick="confirmationDialog(\'confirm_delete_record\', {resultHandler: deleteBillRecord, parm:  \'#= id #\'} )" href="javascript:;">' + _lang.delete + '</a>' +
                    '</div></div>', width: '70px'
    });
    if (jQuery('#display-columns').val()) {
        currencyCode = ' (' + jQuery('#currencyCode').val() + ')';    
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'dated') {
                array_push = {field: "dated", template: '<a href="' + getBaseURL('money') + 'vouchers/bill_edit/#= id #">#=kendo.toString(dated, "yyyy-MM-dd" ) #</a>', format: "{0:yyyy-MM-dd}", title: _lang.billDate, width: '146px'};
            }
            else if (item === 'supplierAccount') {
                array_push = {field: "supplierAccount", title: _lang.supplierAccount, width: '200px'};
            }
            else if (item === 'account_number') {
                array_push = {field: "account_number", title: _lang.accountTypeAccountNumber.sprintf([_lang.supplier]), width: '200px'};
            }
            else if (item === 'total') {
                array_push = {field: "total", template: "#= accounting.formatMoney(accounting.toFixed(total, 2), \"\") #", title: _lang.total + currencyCode, width: '185px'};
            }
            else if (item === 'payemntsMade') {
                array_push = {field: "payemntsMade", template: "#= accounting.formatMoney(accounting.toFixed(payemntsMade ? payemntsMade : 0, 2), \"\") #", title: _lang.paymentMade + currencyCode, width: '200px'};
            }
            else if (item === 'balanceDue') {
                array_push = {field: "balanceDue", template: "#= accounting.formatMoney(accounting.toFixed(balanceDue ? balanceDue : total, 2), \"\") #", title: _lang.balanceDue + currencyCode, width: '172px'};
            }
            else if (item === 'dueDate') {
                array_push = {field: "dueDate", format: "{0:yyyy-MM-dd}", title: _lang.dueDate, width: '142px'};
            }
            else if (item === 'billStatus') {
                array_push = {field: "billStatus", title: _lang.status, template: '<span class="#= billStatus == "partially paid" ? "customBLue" : billStatus == "open" ? "orange" : billStatus == "paid" ? "darkGreen" : billStatus == "overdue" ? "red" : "" #">#= getBillStatusTranslation(billStatus) #</span>', width: '95px'};
            }
            else if (item === 'referenceNum') {
                array_push = {field: "referenceNum", title: _lang.supplierBillNum, width: '167px'};
            }
            else if (item === 'caseID') {
                array_push = {field: "caseID", template: '<a href="' + getBaseURL() + '#= (caseCategory!="IP") ? "cases/edit/"+case_id : "intellectual_properties/edit/"+case_id #">#= case_id ? caseID : "" #</a>', title: _lang.caseId, width: '118px'};
            }
            else if (item === 'caseSubject') {
                array_push = {field: "caseSubject", title: _lang.caseSubject, width: '200px'};
            }
            else if (item === 'clientName') {
                array_push = {field: "clientName", template: '<a href="' + getBaseURL('money') + 'clients/client_details/#= clientID #">#= clientName ? clientName : "" #</a>', title: _lang.clientName_Money, width: '200px'};
            }
            else if (item === 'description') {
                array_push = {field: "description", title: _lang.note, width: '220px'};
            }
            else if (item === 'createdByName') {
                array_push = {field: "createdByName", title: _lang.createdBy, width: '150px', template: '#= (createdByName!=null && createdStatus=="Inactive")? createdByName+" ("+_lang.custom[createdStatus]+")":((createdByName!=null)?createdByName:"") #'};
            }
            else if (item === 'createdOn') {
                array_push = {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '140px'};
            }
            else if (item === 'modifiedByName') {
                array_push = {field: "modifiedByName", title: _lang.modifiedBy, width: '140px', template: '#= (modifiedByName!=null && modifiedStatus=="Inactive")? modifiedByName+" ("+_lang.custom[modifiedStatus]+")":((modifiedByName!=null)?modifiedByName:"") #'};
            }
            else if (item === 'modifiedOn') {
                array_push = {field: "modifiedOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '129px'};
            }
            else if (item === 'totalTax') {
                array_push = {field: "totalTax", template: "#= accounting.formatMoney(accounting.toFixed(totalTax, 2), \"\") #", title: _lang.total+' '+ _lang.tax, width: '160px'};
            }
            else if (item === 'subTotal') {
                array_push = {field: "subTotal", template: "#= accounting.formatMoney(accounting.toFixed(subTotal, 2), \"\") #", title: _lang.subTotal, width: '160px'};
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
                    url: getBaseURL('money') + "vouchers/bills_list",
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
                                console.log($response);
                                setGridDetails($response.gridDetails);
                            }
                            if ($response.feedbackMessage != undefined) {
                                pinesMessage({ty: $response.feedbackMessage.ty, m: $response.feedbackMessage.m});
                            } else {
                                pinesMessage({ty: 'error', m: _lang.updatesFailed});
                            }
                        }
                        animateDropdownMenuInGrids('billGrid');
                        if ($response.columns_html) {
                            jQuery('#column-picker-trigger-container').html($response.columns_html);
                            gridEvents();
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
                            setGridFiltersData(gridFormData, 'billGrid');
                            options.loadWithSavedFilters = 1;
                            options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                            gridSavedFiltersParams = '';
                        }
                        else {
                            options.filter = checkWhichTypeOfFilterIUseAndReturnFiltersToBill();
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
                        organization_id: {type: "number"},
                        billID: {type: "number"},
                        accountID: {type: "number"},
                        voucherType: {type: "string"},
                        RefNum: {type: "string"},
                        total: {type: "number"},
                        paymentsMade: {type: "number"},
                        dated: {type: "date"},
                        dueDate: {type: "date"},
                        billStatus: {type: "string"},
                        description: {type: "string"},
                        supplierAccount: {type: "string"},
                        clientID: {type: "number"},
                        clientName: {type: "string"},
                        balanceDue: {type: "string"},
                        createdByName: {type: "string"},
                        createdOn: {type: "date"},
                        modifiedByName: {type: "string"},
                        modifiedOn: {type: "date"},
                        account_number: {type: "string"},
                        subTotal: {type: "number"},
                        totalTax: {type: "number"},
                        taxNumber: {type: "string"}
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
                            row['createdByName'] = escapeHtml(row['createdByName']);
                            row['modifiedByName'] = escapeHtml(row['modifiedByName']);
                            row['clientName'] = escapeHtml(row['clientName']);
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
    gridTriggers({'gridContainer': jQuery('#billGrid'), 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
}
function billQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterBillValue', '#filtersFormWrapper').val(term);
        jQuery('#billGrid').data("kendoGrid").dataSource.page(1);
    }
}

function checkWhichTypeOfFilterIUseAndReturnFiltersToBill() {
    var filtersForm = jQuery('#billSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('billSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterBillValue', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}