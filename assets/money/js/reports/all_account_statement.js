let allAccountStatement = (function () {
    'use strict';
    let allAccountStatementReportContainer = jQuery('#all-account-statement-report-container');

    function loadSelectPickers() {
        jQuery('.select-picker', allAccountStatement.allAccountStatementReportContainer).selectpicker();
    }

    function loadDatePickers() {
        jQuery('#from-date, #to-date', allAccountStatement.allAccountStatementReportContainer).datepicker({dateFormat: 'yy-mm-dd', showButtonPanel: true, changeYear: true, changeMonth: true});
    }

    function exportAllAccountStatementToExcel() {
        let allAccountStatementForm = jQuery('#all-account-statement-form',allAccountStatement.allAccountStatementReportContainer);
        allAccountStatementForm.attr('action', getBaseURL('money') + 'reports/export_excel_all_account_statement').submit();
        allAccountStatementForm.attr('action', '');
    }

    function exportAllAccountStatementToWord() {
        let allAccountStatementForm = jQuery('#all-account-statement-form',allAccountStatement.allAccountStatementReportContainer);
        allAccountStatementForm.attr('action', getBaseURL('money') + 'reports/export_word_all_account_statement').submit();
        allAccountStatementForm.attr('action', '');
    }

    function getResult() {
        let allAccountStatementResult = jQuery('#all-account-statement-result',allAccountStatement.allAccountStatementReportContainer);
        let allAccountStatementForm = jQuery('#all-account-statement-form',allAccountStatement.allAccountStatementReportContainer);
        jQuery.ajax({
            url: getBaseURL('money') + 'reports/all_account_statement_result',
            type: 'POST',
            dataType: 'JSON',
            data: allAccountStatementForm.serializeArray(),
            beforeSend: function () {
                allAccountStatementResult.removeClass('d-none');
                allAccountStatementResult.html('<div class="col-md-12 no-margin loading">&nbsp;</div>');
            },
            success: function (response) {
                let noResultContent = "<div class='filter-container-style margin-top-15 margin-bottom centered-text'><strong>"+ _lang.no_results_matched +"</strong></div>";
                if(response.result){
                    allAccountStatementResult.html(response.html);
                    jQuery('#report-actions').removeClass('d-none');
                }else{
                    jQuery('#report-actions').addClass('d-none');
                    allAccountStatementResult.html(noResultContent);
                }

            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    return {
        exportAllAccountStatementToWord: exportAllAccountStatementToWord,
        exportAllAccountStatementToExcel: exportAllAccountStatementToExcel,
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
     * @var allAccountStatement.loadSelectPickers()
     * @var allAccountStatement.loadDatePickers()
     */
    allAccountStatement.loadSelectPickers();
    allAccountStatement.loadDatePickers();
});