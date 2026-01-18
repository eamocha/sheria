jQuery(function () {
    jQuery(document).ready(function () {
        var form = jQuery('#time-logs-per-month');
        if (jQuery('#invoiceDateOpertator').val() != 'cast_between') {
            jQuery('#calendar-icon').addClass('d-none')
            disableEmpty(form);
        }
        submitForm(form);
        jQuery('#invoiceDateOpertator').change(function () {
            if (jQuery('#invoiceDateOpertator').val() == 'cast_between') {
                jQuery('#calendar-icon').removeClass('d-none');
                var e = disableEmpty(form);
                jQuery('div.form-group input.form-control:last-of-type, div.form-group select.form-control:last-of-type', e).each(function (e, t) {
                    if (String(t.value).trim() === "" && !jQuery(t).hasClass('hasDatepicker')) {
                        jQuery('input, select', jQuery(t).parent().parent()).removeAttr("disabled");
                    }
                });
                jQuery('div.form-group input.form-control.hasDatepicker:last-of-type', e).each(function (e, t) {
                    if (String(t.value).trim() === "") {
                        jQuery('input, select', jQuery(t).parent()).removeAttr("disabled");
                    }
                });
            } else {
                jQuery('#calendar-icon').addClass('d-none')
                disableEmpty(e);
            }
        });
        setDatePicker('#invoiceDateValueContainer',jQuery("#time-logs-date-container"));
        setDatePicker('#invoiceDateEndValueContainer',jQuery("#time-logs-date-container"));
        var minDate = jQuery("#invoiceDateEndValue", jQuery('#time-logs-date-container')).data('datepicker').getFormattedDate('yyyy-mm-dd');
        jQuery('#invoiceDateEndValue', jQuery('#time-logs-date-container')).bootstrapDP('setStartDate', minDate);
    });
});

function exportReportToExcel() {
    var form = jQuery('#time-logs-per-month');
    form.attr('action', getBaseURL('money') + 'reports/time_logs_per_month_export_to_excel/').submit();
}

function submitForm(form) {
    jQuery('input:checkbox').prop('checked', 'checked');
    jQuery.ajax({
        url: getBaseURL('money') + 'reports/time_logs_per_month/' + jQuery('#invoiceDateOpertator').val() + '/' + jQuery('#invoiceDateValue').val() + '/' + jQuery('#invoiceDateEndValue').val(),
        method: 'post',
        dataType: 'json',
        data: form.serialize(),
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.status) {
                if (response.total_rows > 0) {
                    jQuery('#report-table').html(response.html);
                    jQuery('#report-filters').removeClass('d-none');
                    jQuery('.no-data').addClass('d-none');
                    adjustTableWidth();
                } else {
                    jQuery('.no-data').removeClass('d-none');
                    jQuery('#report-table').html('');
                    adjustTableWidth();
                }
            } else {
                pinesMessage({ ty: 'warning', m: _lang.error_runing_report });
            }
        }
    });
}

function toggleDisplayTableColumns(element, value) {
    jQuery('.' + element).toggle("fast", "linear");
    adjustTableWidth();
}

function adjustTableWidth() {
    var tableWidth = jQuery("#time-logs-per-month-report-table").width();
    var windowWidth = jQuery(window).width();
    if (tableWidth > windowWidth) {
        jQuery("#time-logs-per-month-report-table").css("display", "block");
    } else {
        jQuery("#time-logs-per-month-report-table").css("display", "table");
    }
}
