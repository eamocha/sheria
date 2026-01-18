var gridOptions = {};
var BillVoucherID = '';
function initBillsGrid() {
    var g = jQuery('#billPaymentsGrid');
    if (undefined === g.data('kendoGrid')) {
        g.kendoGrid(gridOptions);
        return false;
    }
    g.data('kendoGrid').dataSource.read();
    return false;
}
var gridDataSrc = function () {
    return new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('money') + "vouchers/bill_payments_made/" + jQuery('#voucher_header_id').val(),
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                    animateDropdownMenuInGrids('billPaymentsGrid');
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
                    id: {
                        editable: false,
                        type: "number"
                    },
                    dated: {
                        type: "date"
                    },
                    paymentID: {
                        type: "string"
                    },
                    attachment_id: {
                        type: "string"
                    },
                    paymentRefNum: {
                        type: "string"
                    },
                    accountCurrency: {
                        type: "string"
                    },
                    paymentAmount: {
                        type: "string"
                    },
                    accountID: {
                        type: "number"
                    },
                    accountName: {
                        type: "string"
                    }
                }
            }
        },
        pageSize: 20,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    })
};
jQuery(document).ready(function () {
    BillVoucherID = jQuery('#voucher_header_id').val();
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc(),
        columnMenu: {
            messages: _lang.kendo_grid_sortable_messages
        },
        columns: [
            {
                field: 'actionsCol',
                title: ' ',
                filterable: false,
                sortable: false,
                template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/bill_payment_print/#= id +\'/\'+ paymentID #/' + BillVoucherID + '">' + _lang.print + '</a>' +
                        '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/bill_payment_edit/' + BillVoucherID + '/#= paymentID #">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" style="#= (attachment_id) ? \'\' : \'display: none;\'  #" href="' + getBaseURL('money') + 'vouchers/bill_payment_download_file/#= attachment_id #">' + _lang.downloadAttachment + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="deleteBillPayment(\'' + BillVoucherID + '\',\'#= paymentID #\');">' + _lang.deleteRow + '</a>' +
                        '</div></div>',
                width: '70px'
            },
            {
                field: "dated",
                format: "{0:yyyy-MM-dd}",
                title: _lang.paidOn,
                template: '<a href="' + getBaseURL('money') + 'vouchers/bill_payment_edit/' + BillVoucherID + '/#= paymentID #">#= kendo.toString(dated, "yyyy-MM-dd") #</a>',
                width: '130px'
            },
            {
                field: "paymentMethod",
                title: _lang.paymentMethod,
                template: '#= getPaymentMethod(paymentMethod) #' ,
                width: '120px'
            },
            {
                field: "paymentAmount",
                template: "#= number_format(paymentAmount, 2, '.',',')#",
                title: _lang.amount,
                width: '130px'
            },
            {
                field: "accountCurrency",
                title: _lang.currency,
                width: '85px'
            },
            {
                field: "paymentRefNum",
                title: _lang.internalReferenceNb,
                width: '100px'
            },
            {
                field: "accountName",
                title: _lang.paidThrough,
                width: '200px'
            },
        ],
        editable: "",
        filterable: false,
        height: 500,
        pageable: {
            input: true,
            messages: _lang.kendo_grid_pageable_messages,
            numeric: false,
            pageSizes: [5, 10, 20, 50, 100],
            refresh: true
        },
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {
            mode: "multiple"
        },
        toolbar: [{
                name: "bill-grid-toolbar",
                template: '<div class="col-md-4 no-padding">'
                        + '<h4 class="col-md-10">' + _lang.paymentMade + '</h4>'
                        + '</div>'
                        + '<div class="col-md-1 pull-right">'
                        + '<div class="btn-group pull-right">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                        + _lang.actions + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/bill_payment_add/' + BillVoucherID + '" >' + _lang.recordPayment + ' </a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
            }]
    };
    initBillsGrid();
    jQuery('#billSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        initBillsGrid();
    });
});
function billsAdvancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function getFormFilters() {
    var filtersForm = jQuery('#billSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('billSearchFilters', '.', true);
    var filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}
function deleteBillPayment(BillVoucherID, paymentID) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/bill_payment_delete',
            type: 'POST',
            dataType: 'JSON',
            data: {BillVoucherID: BillVoucherID, paymentID: paymentID},
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 101:	// removed successfuly
                        ty = 'success';
                        m = _lang.selectedPaymentDeleted;
                        break;
                    case 202:	// could not remove record
                        ty = 'warning';
                        m = _lang.recordNotDeleted;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
                jQuery('#billPaymentsGrid').data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function getPaymentMethod(paymentMethod){
    return getTranslation(paymentMethod.replace(/ /g, "_"));
}
