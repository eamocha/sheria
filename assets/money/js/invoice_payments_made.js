var gridOptions = {};
var InvoiceVoucherID = '';
var gridDataSrc = function () {
    return new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('money') + "vouchers/invoice_payments_made/" + jQuery('#voucher_header_id').val(),
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids('invoicePaymentsGrid');
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
                    id: {editable: false, type: "integer"},
                    dated: {type: "date"},
                    paymentID: {type: "string"},
                    attachment_id: {type: "string"},
                    paymentRefNum: {type: "string"},
                    accountCurrency: {type: "string"},
                    paymentAmount: {type: "string"},
                    accountID: {type: "number"},
                    accountName: {type: "string"},
                    payment_number: {type: "string"}
                }
            },
            parse: function(response){
                var rows = [];
            if(response.data){
                var data = response.data;
                rows = response;
                rows.data = [];
                for (var i = 0; i < data.length; i++) {
                    var row = data[i];
                    row['payment_number'] = escapeHtml(row['payment_number']);
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
    })
};
function initInvoicesGrid() {
    var grid = jQuery('#invoicePaymentsGrid');
    if (undefined === grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        return false;
    }
    grid.data('kendoGrid').dataSource.read();
    return false;
}
jQuery(document).ready(function () {
    InvoiceVoucherID = jQuery('#voucher_header_id').val();
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc(),
        columnMenu: {
            messages: _lang.kendo_grid_sortable_messages
        },
        columns: [
            {
                field: 'id', title: ' ', filterable: false, sortable: false,
                template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/invoice_payment_print/#= id +\'/\'+ paymentID #/' + InvoiceVoucherID + '">' + _lang.print + '</a>' +
                        '<a class="dropdown-item" onclick="paymentExport(\'#= id #\',\'#= paymentID #\',\'' + InvoiceVoucherID + '\');" href="javascript:;">' + _lang.viewReceiptInWord + '</a>' +
                        '<a class="dropdown-item ' + (!is_settlements_per_invoice_enabled ? "hidden" : "") + '" onclick="adviceFeeNoteExport(\'#= paymentID #\',\'' + InvoiceVoucherID + '\');" href="javascript:;">' + _lang.exportPartnerCommission + '</a>' +
                        '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/invoice_payment_edit/' + InvoiceVoucherID + '/#= paymentID #">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" style="#= (attachment_id) ? \'\' : \'display: none;\'  #" href="' + getBaseURL('money') + 'vouchers/invoice_payment_download_file/#= attachment_id #/#= voucherType #">' + _lang.downloadAttachment + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="deleteInvoicePayment(\'' + InvoiceVoucherID + '\',\'#= paymentID #\',\'#= payment_number #\');">' + _lang.deleteRow + '</a>' +
                        '</div></div>', width: '70px'
            },
            {field: "dated", format: "{0:yyyy-MM-dd}", title: _lang.paidOn, template: '<a href="' + getBaseURL('money') + 'vouchers/invoice_payment_edit/' + InvoiceVoucherID + '/#= paymentID #">#=kendo.toString(dated, "yyyy-MM-dd" ) #</a>', width: '130px'},
            {field: "paymentMethod", title: _lang.paymentMethod, template: '#= getPaymentMethod(paymentMethod) #' , width: '120px'},
            {field: "paymentAmount", template: "#= number_format(paymentAmount, 2, '.',',')#", title: _lang.amount, width: '130px'},
            {field: "accountCurrency", title: _lang.currency, width: '85px'},
            {field: "paymentRefNum", title: _lang.internalReferenceNb, width: '100px'},
            {field: "accountName", title: _lang.depositTo, width: '200px'}
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
                name: "invoice-grid-toolbar",
                template: '<div class="col-md-12 no-padding">'
                        + '<h4 class="col-md-4">' + _lang.paymentMade + '</h4>'
                        + '<div class="col-md-1 pull-right">'
                        + '<div class="btn-group pull-right">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                        + _lang.actions + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/invoice_payment_add/' + InvoiceVoucherID + '" >' + _lang.recordPayment + ' </a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
            }]
    };
    initInvoicesGrid();
    jQuery('#invoiceSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        initInvoicesGrid();
    });
});
function invoicesAdvancedSearchFilters() {
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
    var filtersForm = jQuery('#invoiceSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('invoiceSearchFilters', '.', true);
    var filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}
function deleteInvoicePayment(InvoiceVoucherID, paymentID, paymentNb) {
    paymentNb = paymentNb || 1;
    if (confirm(paymentNb > 1 ? _lang.confirmationDeleteRelatedPaymentData : _lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/invoice_payment_delete',
            type: 'POST',
            dataType: 'JSON',
            data: {InvoiceVoucherID: InvoiceVoucherID, paymentID: paymentID},
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
                jQuery('#invoicePaymentsGrid').data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function paymentExport(id,paymentId,invoiceVoucherID) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/payment_export_options',
        type: "GET",
        data: {'return': 'html'},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var exportOptions = "#export-options-container";
                if (jQuery(exportOptions).length <= 0) {
                    jQuery("<div id='export-options-container'></div>").appendTo("body");
                    var exportOptionsContainer = jQuery(exportOptions);
                    exportOptionsContainer.html(response.html);
                    jQuery('.select-picker', exportOptionsContainer).selectpicker();
                    commonModalDialogEvents(exportOptionsContainer);
                    jQuery("#form-submit", exportOptionsContainer).click(function () {
                        exportSubmit(id, exportOptionsContainer,paymentId,invoiceVoucherID);
                    });
                    jQuery(exportOptionsContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            exportSubmit(id, exportOptionsContainer,paymentId,invoiceVoucherID);
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
function exportSubmit(id, container,paymentId,invoiceVoucherId) {
    var template = jQuery('#export-template option:selected',container).val();
    var type = 'invoice_payment_export_to_word';
    jQuery(".modal", container).modal("hide");
    window.location = getBaseURL('money') + 'vouchers/' + type + '/' + id + '/' + paymentId + '/' + invoiceVoucherId  + '/' + template ;
}

function getPaymentMethod(paymentMethod){
    return getTranslation(paymentMethod.replace(/ /g, "_"));
}

function adviceFeeNoteExport(paymentId, invoiceVoucherID) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/advice_fee_note_export_options/' + invoiceVoucherID,
        type: "GET",
        data: {'return': 'html'},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var exportOptions = "#export-options-container";
                if (jQuery(exportOptions).length <= 0) {
                    jQuery("<div id='export-options-container'></div>").appendTo("body");
                    var exportOptionsContainer = jQuery(exportOptions);
                    exportOptionsContainer.html(response.html);
                    jQuery('.select-picker', exportOptionsContainer).selectpicker();
                    commonModalDialogEvents(exportOptionsContainer);
                    jQuery("#form-submit", exportOptionsContainer).click(function () {
                        adviceFeeNoteExportSubmit(paymentId, exportOptionsContainer, invoiceVoucherID);
                    });
                    jQuery(exportOptionsContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            adviceFeeNoteExportSubmit(paymentId, exportOptionsContainer, invoiceVoucherID);
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

function adviceFeeNoteExportSubmit(paymentId, container, invoiceVoucherId) {
    var templateId = jQuery('#export-template option:selected', container).val();
    var partnerId = jQuery('#partner-list option:selected', container).val();
    jQuery(".modal", container).modal("hide");
    window.location = getBaseURL('money') + 'vouchers/advice_fee_note_export_to_word/' + partnerId + '/' + invoiceVoucherId + '/' + templateId + '/' + paymentId;;
}
