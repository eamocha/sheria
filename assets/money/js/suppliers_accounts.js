var enableQuickSearch = true;
var gridDataSrc = new kendo.data.DataSource({
    transport: {
        read: {
            url: getBaseURL('money') + "accounts/suppliers",
            dataType: "JSON",
            type: "POST",
            complete: function () {
                if (jQuery('#filtersFormWrapper').is(':visible'))
                    jQuery('#filtersFormWrapper').slideUp();
                if (_lang.languageSettings['langDirection'] === 'rtl')
                    gridScrollRTL();
        				jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
                animateDropdownMenuInGrids('accountsGrid');
            }
        },
        parameterMap: function (options, operation) {
            if ("read" !== operation && options.models) {
                return {
                    models: kendo.stringify(options.models)
                };
            } else {
                options.filter = getFormFilters();
                options.returnData = 1;
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
                currencyCode: {type: "string"},
                totalDebit: {type: "string"},
                totalCredit: {type: "string"},
                foreignAmount: {type: "string"},
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
                    row['account_number'] = escapeHtml(row['account_number']);
                    rows.data.push(row);
                }
            }
            return rows;
        }
    },
    pageSize: 50,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});
var gridOptions = {};
jQuery(document).ready(function () {
	jQuery('.multi-select', '#accountsSearchFilters').chosen({no_results_text: _lang.no_results_matched,placeholder_text: _lang.select,width: "100%"}).change();
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {
                field: 'id',
                title: ' ',
                filterable: false,
                sortable: false,
                template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="' + getBaseURL('money') + 'accounts/edit/#= id #">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" href="' + getBaseURL('money') + 'accounts/show_transactions/#= id #">' + _lang.zoomIn + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="deleteAccount(\'#= id #\')">' + _lang.deleteRow + '</a>' +
                        '</div></div>',
                width: '20px'
            },
            {field: 'name', template: '<a href="' + getBaseURL('money') + 'accounts/edit/#= id #">#= name #</a>', title: _lang.accountName, width: '75px'},
            {field: 'account_number', template: '<a href="' + getBaseURL('money') + 'accounts/edit/#= id #">#= account_number #</a>', title: _lang.accountTypeAccountNumber.sprintf([_lang.supplier]), width: '75px'},
            {field: 'currencyCode', title: _lang.currency, width: '75px'},
            {field: 'totalDebit', template: "#= money_format('%i',round(totalDebit,2)) #", title: _lang.debit, width: '75px'},
            {field: 'totalCredit', template: "#= money_format('%i',Math.abs(round(totalCredit,2))) #", title: _lang.credit, width: '75px'},
            {field: 'foreignAmount', template: "#= money_format('%i',round(foreignAmount,2)) #", title: _lang.balanceDue, width: '75px'}
        ],
        editable: "",
        filterable: false,
        height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {
            mode: "multiple"
        },
        toolbar: [{
                name: "account-grid-toolbar",
                template: '<div class="col-md-6 no-padding">'
                        + '<h4 class="col-md-5 no-padding-right">' + _lang.suppliersAccounts + '</h4>'
                        + '<div class="input-group col-md-6">'
                        + '<input type="text" class="form-control search" placeholder=" ' + _lang.search + '" name="companyLookUp" id="quickSearchLookUp" onkeyup="accountQuickSearch(event.keyCode, this.value);" title="' + _lang.searchCompany + '" />'
                        + '</div>'
                        + '</div>'
                        + '<div class="col-md-2 no-padding advanced-search">'
                        + '<a href="javascript:;" onclick="accountsAdvancedSearchFilters()">' + _lang.advancedSearch + '</a>'
                        + '</div>'
                        + '<div class="col-md-1 pull-right">'
                        + '<div class="btn-group pull-right">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">' + _lang.actions
                        + '  <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" href="' + getBaseURL('money') + 'accounts" >' + _lang.chartOfAccounts + ' </a>'
                        + '<a class="dropdown-item" href="' + getBaseURL('money') + 'accounts/clients" >' + _lang.listClientAccounts + ' </a>'
                        + '<a class="dropdown-item" href="' + getBaseURL('money') + 'accounts/partners" >' + _lang.listPartnerAccounts + ' </a>'
                        + '<a class="dropdown-item" onclick="exportAccountsToExcel()" title="' + _lang.exportToExcel + '" href="javascript:;" >' + _lang.exportToExcel + '</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
            }]
    };
    initAccountsGrid();
    jQuery('#accountsSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        jQuery('#quickSearchLookUp').val('');
        enableQuickSearch = false;
        initAccountsGrid();
    });
});
function exportAccountsToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    newFormFilter.attr('action', getBaseURL('money') + 'accounts/export_suppliers_to_excel').submit();
}
function accountsAdvancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function accountQuickSearch(keyCode, term) {
    if (keyCode == 13) {
        enableQuickSearch = true;
        document.getElementsByName("page").value =1;
                document.getElementsByName("skip").value =0;
        jQuery('#quickSearchFilter').val(term);
        jQuery('#quickSearchFilterAccountNumberValue').val(term);
        jQuery('#accountsGrid').data("kendoGrid").dataSource.page(1);
    }
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function getFormFilters() {
    var filtersForm = jQuery('#accountsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('accountsSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilter').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
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
                        m = _lang.unableToDeleteAnVendorAccount;
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
function initAccountsGrid() {
    var grid = jQuery('#accountsGrid');
    document.getElementsByName("page").value =1;
        document.getElementsByName("skip").value =0;
    if (undefined === grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        return false;
    }
    grid.data('kendoGrid').dataSource.page(1);
    return false;
}
