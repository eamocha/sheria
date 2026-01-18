var container;
jQuery(document).ready(function () {
    container = jQuery('#sla-report');
    jQuery('.select-picker', container).selectpicker({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.select,
        width: '100%',
        dropupAuto: false
    });
});

function validateFilters() {
    if (jQuery('#workflow-dropdown').val() === '') {
        pinesMessage({ty: 'error', m: _lang.validation_field_required.sprintf([_lang.workflow])});
        return false;
    }
    submitFilters();
}

function submitFilters() {
    let filterData = jQuery('#report-filter', container).serializeArray();
    jQuery.ajax({
        url: getBaseURL() + 'reports/sla/',
        type: 'POST',
        dataType: 'JSON',
        data: filterData,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            let noResultContent = "<div class='filter-container-style margin-top-15 margin-bottom centered-text'><strong>" + _lang.no_results_matched + "</strong></div>";
            if (response.status) {
                jQuery('.report-body', container).html(response.html);
                jQuery('.report-actions', container).removeClass('d-none');
                collapseExpandRows(container);
            } else {
                jQuery('.report-actions', container).addClass('d-none');
                jQuery('.report-body', container).html(noResultContent);
                if (response.error !== 'undefined' && response.error) {
                    pinesMessage({ty: 'error', m: _lang.validation_field_required.sprintf([_lang.workflow])});
                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function exportToPDF() {
    let formId = jQuery('#report-filter', container);
    jQuery('#filtersInfoForExport1', formId).val(JSON.stringify(getExportInfoFilter('report-filter')));
    let oldFormAction = formId.attr('action');
    formId.attr('action', getBaseURL() + 'reports/sla_pdf').submit();
    formId.attr('action', oldFormAction);
}