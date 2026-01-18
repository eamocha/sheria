let clientStatement = (function () {
    'use strict';
    let clientStatementReportContainer = jQuery('#client-statement-report-container');

    function loadSelectPickers() {
        jQuery('.select-picker', clientStatement.clientStatementReportContainer).selectpicker();
    }

    function loadDatePickers() {
        jQuery('#from-date, #to-date', clientStatement.clientStatementReportContainer).datepicker({dateFormat: 'yy-mm-dd', showButtonPanel: true, changeYear: true, changeMonth: true});
    }

    function exportClientStatementToExcel() {
        let clientStatementForm = jQuery('#client-statement-form',clientStatement.clientStatementReportContainer);
        if(jQuery('#clients', '#client-statement-form').val() === ''){
            pinesMessage({ty: 'information', m: _lang.validation_field_required.sprintf([_lang['clientName']])});
            return false;
        }
        clientStatementForm.attr('action', getBaseURL('money') + 'reports/export_excel_client_statement').submit();
        clientStatementForm.attr('action', '');
    }

    function exportClientStatementToWord() {
        let clientStatementForm = jQuery('#client-statement-form',clientStatement.clientStatementReportContainer);
        if(jQuery('#clients', '#client-statement-form').val() === ''){
            pinesMessage({ty: 'information', m: _lang.validation_field_required.sprintf([_lang['clientName']])});
            return false;
        }
        clientStatementForm.attr('action', getBaseURL('money') + 'reports/export_word_client_statement').submit();
        clientStatementForm.attr('action', '');
    }

    function getResult() {
        let clientStatementResult = jQuery('#client-statement-result',clientStatement.clientStatementReportContainer);
        let clientStatementForm = jQuery('#client-statement-form',clientStatement.clientStatementReportContainer);
        jQuery.ajax({
            url: getBaseURL('money') + 'reports/client_statement_result',
            type: 'POST',
            dataType: 'JSON',
            data: clientStatementForm.serializeArray(),
            beforeSend: function () {
                clientStatementResult.removeClass('d-none');
                clientStatementResult.html('<div class="col-md-12 no-margin loading">&nbsp;</div>');
            },
            success: function (response) {
                let noResultContent = "<div class='filter-container-style margin-top-15 margin-bottom centered-text'><strong>"+ _lang.no_results_matched +"</strong></div>";
                if(response.result !== ''){
                    if (response.status === true) {
                        clientStatementResult.html(response.html);
                        jQuery('#report-actions').removeClass('d-none');
                    } else {
                        pinesMessage({ty: 'information', m: _lang.validation_field_required.sprintf([_lang['clientName']])});
                        clientStatementResult.html(noResultContent);
                    }
                }else{
                    jQuery('#report-actions').addClass('d-none');
                    clientStatementResult.html(noResultContent);
                }

            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    return {
        exportClientStatementToWord: exportClientStatementToWord,
        exportClientStatementToExcel: exportClientStatementToExcel,
        loadDatePickers: loadDatePickers,
        loadSelectPickers: loadSelectPickers,
        getResult: getResult
    };
}());

/**
 * On ready load select piker
 */
jQuery(document).ready(function () {
    /**
     * @var clientStatement.loadSelectPickers()
     * @var clientStatement.loadDatePickers()
     */
    clientStatement.loadSelectPickers();
    clientStatement.loadDatePickers();
});