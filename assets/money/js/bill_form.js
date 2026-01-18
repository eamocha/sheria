function hide_unhide_tax(voucherID, Tax) {
    switch (Tax) {
        case 'hide':
            url = getBaseURL('money') + 'vouchers/bill_edit_hide_tax';
            msg101 = _lang.taxHasBeenConvertedTohide;
            break;
        case 'unhide':
            url = getBaseURL('money') + 'vouchers/bill_edit_unhide_tax';
            msg101 = _lang.taxHasBeenConvertedToUnhide;
            break;
    }
    jQuery.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        data: {voucherID: voucherID},
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.status) {
                case 101:	// changed successfuly
                    ty = 'information';
                    m = msg101;
                    window.location = getBaseURL('money') + 'vouchers/bill_edit/' + voucherID;
                    break;
                case 102:
                    ty = 'warning';
                    break;
                default:
                    break;
            }
            pinesMessage({ty: ty, m: m});
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function billExport(billId, voucherId) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/bill_export_options/',
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
                        billExportSubmit(billId, voucherId, exportOptionsContainer);
                    });
                    jQuery(exportOptionsContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            billExportSubmit(billId, voucherId, exportOptionsContainer);
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

function billExportSubmit(billId, voucherId, container) {
    var templateId = jQuery('#export-template option:selected', container).val();
    jQuery(".modal", container).modal("hide");
    window.location = getBaseURL('money') + 'vouchers/bill_export_to_word/' + billId + '/' + voucherId + '/' + templateId;
}