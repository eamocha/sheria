var enableQuickSearch = false;
var gridOptions = {};
var currencyCode = '';
jQuery(document).ready(function () {
    jQuery('.multi-select', '#quoteSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    jQuery('#quoteSearchFilters').bind('submit', function (e) {
        jQuery("form#quoteSearchFilters").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        if (!jQuery('form#quoteSearchFilters').validationEngine("validate")) {
            return false;
        }
        enableQuickSearch = false;
        e.preventDefault();
        if (jQuery('#submitAndSaveFilter').is(':visible')) {
            gridAdvancedSearchLinkState = true;
        }
        jQuery('#quoteGrid').data("kendoGrid").dataSource.page(1);
    });
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery('#quoteGrid').data('kendoGrid').dataSource.read();
    });
    gridFiltersEvents('Quote_Header', 'quoteGrid', 'quoteSearchFilters');
    gridInitialization();
    currencyCode = ' (' + jQuery('#currencyCode').val() + ')';

    jQuery('#quoteSearchLookUp').val('');
});
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['quoteDateValue', 'quoteDateEndValue', 'dueDateValue', 'dueDateEndValue', 'createdOnValue', 'createdOnEndValue', 'modifiedOnValue', 'modifiedOnEndValue']});
    caseLookup(jQuery('#caseIDValue'));
    clientLookup({"lookupField": jQuery("#clientNameValue")});
    clientAccountLookup(jQuery('#accountIDValue'));
    userLookup('createdByValue');
    userLookup('modifiedByValue');
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}


function changeQuoteStatus(voucherID, status) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/move_quote_status_to/' + status,
        type: "POST",
        data: {voucher_id: voucherID},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                jQuery('.widget-data', '#quote-status-container').tooltipster('hide');
                var quoteStatusId = "#quote-status-container";
                if (jQuery(quoteStatusId).length <= 0) {
                    jQuery("<div id='quote-status-container'></div>").appendTo("body");
                    var quoteStatusContainer = jQuery(quoteStatusId);
                    quoteStatusContainer.html(response.html);
                    commonModalDialogEvents(quoteStatusContainer);
                    jQuery("#form-submit", quoteStatusContainer).click(function () {
                        quoteStatusSubmit(quoteStatusContainer, status);
                    });
                    jQuery(quoteStatusContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            quoteStatusSubmit(quoteStatusContainer);
                        }
                    });
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.invalid_record});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function quoteStatusSubmit(container, status) {
    var formData = jQuery('form#quote-status-form', container).serialize();
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/move_quote_status_to/' + status,
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (!response.status) {
                displayValidationErrors(response.validationErrors, container);
                pinesMessage({ty: 'error', m: _lang.feedback_messages.quoteStatusNotUpdated});
            } else {
                pinesMessage({ty: 'success', m: _lang.quoteStatusChangedSuccessfully.sprintf([getTranslation(response.status)])});
                jQuery(".modal", container).modal("hide");
                jQuery('#quoteGrid').data("kendoGrid").dataSource.read();
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
// function convertQuoteToInvoice(voucherID) {
//     jQuery.ajax({
//         url: getBaseURL('money') + 'vouchers/convert_quote_to_invoice/',
//         type: "POST",
//         data: {voucher_id: voucherID},
//         dataType: "JSON",
//         beforeSend: function () {
//             jQuery("#loader-global").show();
//         },
//         success: function (response) {
//             if (!response.status) {
//                 pinesMessage({ty: 'error', m: _lang.feedback_messages.quoteStatusNotUpdated});
//             } else {
//                 pinesMessage({ty: 'success', m: _lang.quoteStatusChangedSuccessfully.sprintf([getTranslation('invoiced')]) + '<a href="'+getBaseURL('money') + 'vouchers/invoice_edit/'+response.voucher_id+'">'+response.related_invoice_id+'</a>'});
//                 jQuery('#quoteGrid').data("kendoGrid").dataSource.read();
//             }
//         }, complete: function () {
//             jQuery("#loader-global").hide();
//         },
//         error: defaultAjaxJSONErrorsHandler
//     });
// }
function exportQuotesToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFiltersToQuote();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    newFormFilter.attr('action', getBaseURL('money') + 'vouchers/quote_export_to_excel').submit();
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
    return _lang.QuotePaidStatus[val];
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
function deleteQuoteRecord(quoteId) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/delete_quote/' + quoteId,
        dataType: "json",
        data: {},
        success: function (data) {
            if (data.status) {
                jQuery('#quoteGrid').data("kendoGrid").dataSource.read();
                pinesMessage({ty: 'success', m: _lang.deleteRecordSuccessfull});
            } else {
                pinesMessage({ty: 'error', m: _lang.deleteRecordFailed + (data.failed_reason ? ' ('+data.failed_reason+')' : '')});
            }
        }
    });
}
function gridInitialization() {
    var tableColumns = [];
    tableColumns.push({
        field: 'id', title: ' ', filterable: false, sortable: false,
        template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/quote_edit/#= id #">' + _lang.viewEdit + '</a>' +
                '<a class="dropdown-item #= jQuery.inArray(paidStatus,["invoiced","open"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="changeQuoteStatus(\'#= id #\',\'open\')">' + _lang.convertToOpen + '</a>' +
                '<a class="dropdown-item #= jQuery.inArray(paidStatus,["open", "rejected", "cancelled","invoiced"])!=-1 ? "d-none" : ""#" href="' + getBaseURL('money') + 'vouchers/convert_quote_to_invoice/#= id #" >' + _lang.convertToInvoice + '</a>' +
                '<a class="dropdown-item #= jQuery.inArray(paidStatus,["invoiced","approved"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="changeQuoteStatus(\'#= id #\',\'approved\')">' + _lang.approve + '</a>' +
                '<a class="dropdown-item #= jQuery.inArray(paidStatus,["rejected"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="changeQuoteStatus(\'#= id #\',\'rejected\')">' + _lang.reject + '</a>' +
                '<a class="dropdown-item #= jQuery.inArray(paidStatus,["cancelled","rejected","approved"])!=-1 ? "d-none" : ""#" href="javascript:;" onclick="changeQuoteStatus(\'#= id #\',\'cancelled\')">' + _lang.cancel + '</a>' +
                '<a class="dropdown-item" href="javascript:;" onclick="confirmationDialog(\'confirm_delete_record\', {resultHandler: deleteQuoteRecord, parm:  \'#= id #\'} )">' + _lang.delete + '</a>' +
               '</div></div>', width: '70px'
    });
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'quoteID') {
                array_push = {field: "quoteID", template: '<a href="' + getBaseURL('money') + 'vouchers/quote_edit/#= id #">#=refNum ##=suffix !=null?suffix:"" #</a>', title:_lang.quoteNumber, width: '120px'};
            }
            else if (item === 'clientAccount') {
                array_push = {field: "clientAccount", template: '<a href="' + getBaseURL() + '#=model_type=="Company" ? "companies/tab_company/" : "contacts/edit/"##= member_id #">#= clientAccount #</a>', title: _lang.clientsAccount, width: '200px'};
            }
            else if (item === 'account_number') {
                array_push = {field: "account_number", template: '<a href="' + getBaseURL('money') + 'accounts/edit/#= accountID #">#= account_number #</a>', title: _lang.accountTypeAccountNumber.sprintf([_lang.client_Money]), width: '200px'};
            }
            else if (item === 'total') {
                array_push = {field: "total", template: "#= money_format('%i',round(total,2)) #", title: _lang.total, width: '160px'};
            }
            else if (item === 'payemntsMade') {
                array_push = {field: "payemntsMade", template: "#= payemntsMade ? money_format('%i',round(payemntsMade,2)) : money_format('%i',0) #", title: _lang.paymentMade, width: '160px'};
            }
            else if (item === 'balanceDue') {
                array_push = {field: "balanceDue", template: "#= balanceDue >= 0 ? money_format('%i',round(balanceDue,2)) : money_format('%i',round(total,2))#", title: _lang.balanceDue, width: '160px'};
            }
            else if (item === 'clientCurrency') {
                array_push = {field: "clientCurrency", title: _lang.currency, width: '108px'};
            }
            else if (item === 'paidStatus') {
                array_push = {field: "paidStatus", title: _lang.status, template: '<span class="#= paidStatus == "invoiced" ? "customBLue" : paidStatus == "open" ? "orange" : paidStatus == "approved" ? "darkGreen" : paidStatus == "rejected" ? "red" : paidStatus == "cancelled" ? "purple" : "" #">#= getPaidStatusTranslation(paidStatus) #</span>', width: '95px'};
            }
            else if (item === 'dated') {
                array_push = {field: "dated", format: "{0:yyyy-MM-dd}", title:_lang.quoteDate, width: '165px'};
            }
            else if (item === 'dueOn') {
                array_push = {field: "dueOn", format: "{0:yyyy-MM-dd}", title: _lang.dueOn, width: '120px'};
            }
            else if (item === 'purchaseOrder') {
                array_push = {field: "purchaseOrder", title: _lang.purchaseOrder, width: '120px'};
            }
            else if (item === 'refNum') {
                array_push = {field: "refNum", template: '#= referenceNum !=null?referenceNum:"" #', title: _lang.quoteRef, width: '200px'};
            }
            else if (item === 'case_id') {
                array_push = {field: "case_id", template: '#= relatedMattersLinks(caseCategory, case_id) #', title: _lang.caseId, width: '173px'};
            }
            else if (item === 'caseSubject') {
                array_push = {field: "caseSubject", template: '#= caseSubject!= null ? addBreakLine(caseSubject) : ""  #', title: _lang.caseSubject, width: '210px'};
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
                array_push = {field: "totalTax", template: "#= accounting.formatMoney(accounting.toFixed(totalTax, 2), \"\") #", title: _lang.total+' '+ _lang.tax, width: '160px'};
            }
            else if (item === 'subTotal') {
                array_push = {field: "subTotal", template: "#= accounting.formatMoney(accounting.toFixed(subTotal, 2), \"\") #", title: _lang.subTotal, width: '160px'};
            }
            else if (item === 'totalDiscount') {
                array_push = {field: "totalDiscount", template: "#= accounting.formatMoney(accounting.toFixed(totalDiscount, 2), \"\") #", title: _lang.totalDiscount, width: '160px'};
            }
            else if (item === 'sub_total_after_discount') {
                array_push = {field: "sub_total_after_discount", template: "#= accounting.formatMoney(accounting.toFixed(sub_total_after_discount, 2), \"\") #", title: _lang.subTotalAfterDiscount, width: '160px'};
            }
            else if (item === 'taxable') {
                array_push = {field: "taxable", template: "#= accounting.formatMoney(accounting.toFixed(taxable, 2), \"\") #", title: _lang.taxable, width: '160px'};
            }
            else if (item === 'nonTaxable') {
                array_push = {field: "nonTaxable", template: "#= accounting.formatMoney(accounting.toFixed(nonTaxable, 2), \"\") #", title: _lang.nonTaxable, width: '160px'};
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
                    url: getBaseURL('money') + "vouchers/quotes_list",
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
                        animateDropdownMenuInGrids('quoteGrid');
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
                            setGridFiltersData(gridFormData, 'quoteGrid');
                            options.loadWithSavedFilters = 1;
                            options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                            gridSavedFiltersParams = '';
                        }
                        else {
                            options.filter = checkWhichTypeOfFilterIUseAndReturnFiltersToQuote();
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
                        quoteID: {type: "number"},
                        accountID: {type: "number"},
                        voucherType: {type: "string"},
                        total: {type: "number"},
                        payemntsMade: {type: "number"},
                        dated: {type: "date"},
                        dueOn: {type: "date"},
                        paidStatus: {type: "string"},
                        description: {type: "string"},
                        clientAccount: {type: "string"},
                        clientCurrency: {type: "string"},
                        balanceDue: {type: "number"},
                        purchaseOrder: {type: "string"},
                        refNum: {type: "string"},
                        caseID: {type: "string"},
                        case_id: {type: "string"},
                        caseSubject: {type: "string"},
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
    gridTriggers({'gridContainer': jQuery('#quoteGrid'), 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
}

function checkWhichTypeOfFilterIUseAndReturnFiltersToQuote() {
    var filtersForm = jQuery('#quoteSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('quoteSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterQuoteValue', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}