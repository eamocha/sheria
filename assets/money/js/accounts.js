var enableQuickSearch = false;
var gridOptions = {};
jQuery(document).ready(function () {
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery('#accountsGrid').data('kendoGrid').dataSource.read();
    });
    jQuery('.multi-select', '#accountsSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    jQuery('#accountsSearchFilters').bind('submit', function (e) {
        jQuery("form#accountsSearchFilters").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        if (!jQuery('form#accountsSearchFilters').validationEngine("validate")) {
            return false;
        }
        enableQuickSearch = false;
        e.preventDefault();
        if (jQuery('#submitAndSaveFilter').is(':visible')) {
            gridAdvancedSearchLinkState = true;
        }
        jQuery('#accountsGrid').data("kendoGrid").dataSource.page(1);
    });
    gridFiltersEvents('Account', 'accountsGrid', 'accountsSearchFilters');
    gridInitialization();
    
});

function loadEventsForFilters() {
    accountsLookup();
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function exportAccountsToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFiltersToAccount();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    newFormFilter.attr('action', getBaseURL('money') + 'accounts/export_to_excel').submit();
}

function accountsLookup() {
    jQuery("#accountsNameValue", '#accountsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL('money') + 'accounts/autocomplete',
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
                                label: item.name,
                                value: item.name,
                                record: item
                            }
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

function filterByOrganization(OrgValue) {
    jQuery('#organization_id', '#accountsSearchFilters').val(OrgValue);
    jQuery('#accountsGrid').data("kendoGrid").dataSource.read();
}

function deleteAccount(accountID) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL('money') + 'accounts/delete_account',
            type: 'POST',
            dataType: 'JSON',
            data: {accountID: accountID},
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 101:	// removed successfuly
                        ty = 'information';
                        m = _lang.selectedAccountDeleted;
                        break;
                    case 202:	// could not remove record
                        ty = 'warning';
                        m = _lang.unableToDeleteAnAccount;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
                jQuery('#accountsGrid').data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function getAccountCategoryTranslation(accountCat) {
    var keys = Object.keys(_lang.accountCategoryTypes);
    var result = {};

    for (i = 0; i < keys.length; i++) {
      var key = keys[i];
      result[fixLangKey(key)] = _lang.accountCategoryTypes[key];
    }
    return result[accountCat];
}
function fixLangKey(key) { return key.replace(/_/g, ' '); }
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
                template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="' + getBaseURL('money') + 'accounts/edit/#= id #">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item #= systemAccount === "yes" ? "d-none" : "" #" href="javascript:;" onclick="deleteAccount(#= id #)">' + _lang.deleteRow + '</a>' +
                        '</div></div>',
                width: '60px'
            });
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'name') {
                array_push = {field: 'name', template: '<a href="' + getBaseURL('money') + 'accounts/show_transactions/#= id #">#= name #</a>', title: _lang.accountName, width: '135px'};
            }
            else if (item === 'account_number') {
                array_push = {field: 'account_number', template: '<a href="' + getBaseURL('money') + 'accounts/show_transactions/#= id #">#= account_number #</a>', title: _lang.accountNumber, width: '135px'};
            }
            else if (item === 'accountCategoryType') {
                array_push = {field: 'accountCategoryType', title: _lang.accountType, template: '#= getAccountCategoryTranslation(accountCategoryType) #', width: '128px'};
            }
            else if (item === 'currencyCode') {
                array_push = {field: 'currencyCode', title: _lang.currency, width: '108px'};
            }
            else if (item === 'foreignAmount') {
                array_push = {field: 'foreignAmount', template: "#= currency_id == " + organizationCurrencyID + " || systemAccount === 'yes' ? money_format('%i',round(localAmount,2)) : money_format('%i',round(foreignAmount,2)) #", title: _lang.balance, width: '108px'};
            }
            else if (item === 'systemAccount') {
                array_push = {field: 'systemAccount', template: "#= systemAccount === 'yes' ? '" + _lang.yes + "' : '" + _lang.no + "'#", title: _lang.systemAccount, width: '135px'};
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
                    url: getBaseURL('money') + "accounts/index",
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
                        animateDropdownMenuInGrids('accountsGrid');
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
                            setGridFiltersData(gridFormData, 'accountsGrid');
                            options.loadWithSavedFilters = 1;
                            options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                            gridSavedFiltersParams = '';
                        }
                        else {
                            options.filter = checkWhichTypeOfFilterIUseAndReturnFiltersToAccount();
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
                        name: {type: "string"},
                        systemAccount: {type: "string"},
                        accountType: {type: "string"},
                        accountCategory: {type: "string"},
                        accountCategoryType: {type: "string"},
                        currencyCode: {type: "string"},
                        model_type: {type: "string"},
                        fullName: {type: "string"},
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
                            row['name'] = escapeHtml(row['name']);
                            row['systemAccount'] = escapeHtml(row['systemAccount']);
                            row['accountCategoryType'] = escapeHtml(row['accountCategoryType']);
                            row['account_number'] = escapeHtml(row['account_number']);
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
    gridTriggers({'gridContainer': jQuery('#accountsGrid'), 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
}
function accountsQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterNameValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilteraccount_numberValue', '#filtersFormWrapper').val(term);
        jQuery('#accountsGrid').data("kendoGrid").dataSource.page(1);
    }
}

function checkWhichTypeOfFilterIUseAndReturnFiltersToAccount() {
    var filtersForm = jQuery('#accountsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('accountsSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterNameValue', filtersForm).val() || jQuery('#quickSearchFilteraccount_numberValue', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}