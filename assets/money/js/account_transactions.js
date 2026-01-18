var gridDataSrc = new kendo.data.DataSource({
    transport: {
        read: {
            url: getBaseURL('money') + "accounts/show_transactions",
            dataType: "JSON",
            type: "POST",
            complete: function () {
                if (_lang.languageSettings['langDirection'] === 'rtl')
                    gridScrollRTL();
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
                dated: {type: "date"},
                voucherType: {type: "string"},
                description: {type: "string"},
                credit: {type: "string"},
                debit: {type: "string"}
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
                    row['description'] = escapeHtml(row['description']);
                    rows.data.push(row);
                }
            }
            return rows;
        }
    },
    pageSize: 20,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});
var gridOptions = {};
jQuery(document).ready(function () {
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "dated", format: "{0:yyyy-MM-dd}", title: _lang.date, width: '75px'},
            {field: 'description', title: _lang.description, width: '120px', template: '<a href="#=getURL(voucherType,description,id,billPaymentId,settlement_account_id,credit_note_headers_id)#">#= (description != null) ? (voucherType == "JV" ? "JV-"+id : description) :""#</a>'},
            {field: 'voucherType', title: _lang.type, width: '75px'},
            {field: 'debit', title: _lang.debit, width: '75px'},
            {field: 'credit', title: _lang.credit, width: '75px'}
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
                template:
                        '<h4 class="no-margin no-padding">' + _lang.transactionsFor.sprintf([jQuery('#account_name').val() + ' - ' + jQuery('#account_currency').val() + ' (' + jQuery('#account-number').val() + ')']) + '</h4>'
            }]
    };
    initAccountsGrid();
    jQuery('#accountsSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        initAccountsGrid();
    });
});
function accountsAdvancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        makeFieldsDatePicker({fields: ['fromDate', 'toDate']});
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function hideAdvancedSearch() {
    //jQuery('#filtersFormWrapper').slideUp();
}
function getFormFilters() {
    var filtersForm = jQuery('#accountsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('accountsSearchFilters', '.', true);
    var filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}
function initAccountsGrid() {
    makeFieldsDatePicker({fields: ['fromDate', 'toDate']});
    var grid = jQuery('#accountsGrid');
    if (undefined === grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        return false;
    }
    grid.data('kendoGrid').dataSource.read();
    return false;
}
function getURL(voucherType,description,voucher_header_id,billPaymentId, settlement_account_id, credit_note_headers_id){
	var URL='';
	if(voucherType=='EXP'){
		URL=getBaseURL('money') + 'vouchers/expense_edit/'+voucher_header_id;
	}else if(voucherType=='JV'){
		URL=getBaseURL('money') + 'vouchers/journal_edit/'+voucher_header_id;
	}else if(voucherType=='BI'){
		URL=getBaseURL('money') + 'vouchers/bill_edit/'+voucher_header_id;
	}else if(voucherType=='INV' || voucherType=='DBN'){
		URL=getBaseURL('money') + 'vouchers/invoice_edit/'+voucher_header_id;
	}else if(voucherType=='BI-PY'){
		URL=getBaseURL('money') + 'vouchers/bill_edit/'+billPaymentId;
	}else if(voucherType=='INV-PY' || voucherType=='DBN-PY'){
		URL=getBaseURL('money') + 'vouchers/invoices_list/0/'+description.slice(7);
	}else if(voucherType=='PY'){
        URL=getBaseURL('money') + 'accounts/show_transactions/'+settlement_account_id;
	}else if(voucherType=='DP'){
        URL=getBaseURL('money') + 'clients/deposits/';
	}else if(voucherType=='CRN'){
        URL=getBaseURL('money') + 'vouchers/edit_credit_note/'+credit_note_headers_id;
    }
	return URL;
}