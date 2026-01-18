var container;
jQuery(document).ready(function () {
    showToolTip();
    jQuery("#workflow").selectpicker();
    jQuery('.select-picker', container).selectpicker();
});

function showLogs() {
    var checked = jQuery('#checkShowLogs').is(':checked');
    var workflow_id = jQuery('#workflow').val();
    var hideLogs;
    (!checked)? hideLogs = '/hideNoLogs': hideLogs = '',
    jQuery.ajax({
        url: getBaseURL('contract') + 'reports/sla_met_vs_breached_bar_chart/' + workflow_id + hideLogs,
        type: 'GET',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                jQuery('#bar_chart_report', container).html(response.html);
            } 
        },
        complete: function () {
            jQuery('#loader-global').hide();
        }
    });
}

function submitSlaForm() {
    let formData = jQuery('#sla-met-vs-breached', container).serializeArray();
    jQuery.ajax({
        url: getBaseURL('contract') + 'reports/sla_met_vs_breached_bar_chart/',
        type: 'POST',
        dataType: 'JSON',
        data: formData,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                jQuery('#bar_chart_report', container).html(response.html);
            } 
        },
        complete: function () {
            jQuery('#loader-global').hide();
        }
    });
}

function showBarChart(){
    var ticks = document.ticks;
    var plot1 = jQuery.jqplot('sla-met-vs-breached-bar-chart', document.data, {
        height: '500',
        seriesDefaults: {
            renderer: jQuery.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true},
            pointLabels: {
                show: true,
                formatString: '%s'
            }
        },
        legend: {
            show: true,
            placement: 'outsideGrid',
            labels: document.valuesNames,
            location: (jQuery(window).width() > 800) ? 'ne' : 'n'
        },
        axesDefaults: {
            tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer,
            tickOptions: {
                angle: -90,
                fontSize: '12pt'
            }
        },
        axes: {
            xaxis: {
                renderer: jQuery.jqplot.CategoryAxisRenderer,
                ticks: ticks
            },
            yaxis: {
                showTicks: false,
                padMin: 0,
                min: 0,
            }
        },
        seriesColors: ["#80E569", "#C7293D", "#5BD8F5", "#E2C163"]
    });
    jQuery('.jqplot-point-label').each(function () {
        if (jQuery(this).text() == '0') {
            jQuery(this).hide();
        }
    });
    jQuery(window).resize(function () {
        if (jQuery(window).width() > 200)
            plot1.replot({axes: {xaxis: {min: null, max: null}}});
    });
}

function validateFilters() {
    if (jQuery('#workflow-dropdown').val() == "null") {
        pinesMessage({ty: 'error', m: _lang.validation_field_required.sprintf([_lang.workflow])});
        return false;
    }
    submitFilters();
}

function submitFilters() {
    let filterData = jQuery('#report-filter', container).serializeArray();
    jQuery.ajax({
        url: getBaseURL('contract') + 'reports/sla/',
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
    formId.attr('action', getBaseURL('contract') + 'reports/sla_pdf').submit();
    formId.attr('action', oldFormAction);
}
