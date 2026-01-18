var charts = [];
var container = jQuery('#time-tracking-dashboard');
jQuery(document).ready(function () {
    jQuery('#main-container').attr('style', 'background-color: #F5F5F5 !important');
    jQuery('body').attr('style', 'background-color: #F5F5F5 !important');
    loadDashboardData();
    jQuery('.select-picker').selectpicker();
    jQuery('.multi-select').chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.all,
        width: "100%"
    }).change();
    jQuery('.dashboard-filter', '#time-tracking-dashboard').change(function () {
        if(this.id == 'assigned-team'){
            setUserOptions()
        }
        loadDashboardData();
    });
});
function setUserOptions()
{
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'dashboard/get_filter_users',
        data: { assigned_teams: jQuery('#assigned-team', '#time-tracking-dashboard').val()},
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            jQuery('#user-id', '#time-tracking-dashboard').empty();
            jQuery.each(response.users, function (key, value) {
                jQuery('#user-id', '#time-tracking-dashboard').append('<option value="' + key + '">' + value + '</option>');
            });
            jQuery('#user-id', '#time-tracking-dashboard').trigger("chosen:updated");
        },
        complete: function () {

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function loadDashboardData() {
    var container = jQuery('#time-tracking-dashboard');
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'dashboard/time_tracking_dashboard',
        data: { year_filter: jQuery('#year', container).val(), user_id: jQuery.isEmptyObject(jQuery('#user-id', container).val()) ?  "" : jQuery('#user-id', container).val(), assigned_team: jQuery.isEmptyObject(jQuery('#assigned-team', container).val()) ?  "" : jQuery('#assigned-team', container).val(), seniority_level: jQuery.isEmptyObject(jQuery('#seniority-level', container).val()) ?  "" : jQuery('#seniority-level', container).val(), organization: jQuery('#organizations', container).val()},
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            charts[7] = timeLogsPerMonth(response.time_logs_per_month);
            charts[8] = billableUtilizationRatePerUser(response.billable_utilization_rate_per_user);
            charts[9] = nonBillableUtilizationRatePerUser(response.non_billable_utilization_rate_per_user);
            var count = 0;
            jQuery.each(response.charts, function (key, widget) {
                charts[widget.settings.number] = window[widget.settings.type](widget);
                count++;
            });
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function barChart(data) {
    var chartId = "#" + data.settings.id;
    var options = {
        chart: {
            height: 302,
            type: 'bar'
        },
        series: [],
        colors: ["#008ffb", "#00e396", "#feb019"],
        xaxis: {
            categories: [_lang.total, _lang.timeTrackingStatus.billable, _lang.timeTrackingStatus.internal],
        },
        yaxis: {
            labels: {
                style: {
                    cssClass: 'vertical-bar-chart-labels',
                },
                maxWidth: 400,
            },
        },
        plotOptions: {
            bar: {
                distributed: true,
                horizontal: false,
                columnWidth: '80%',
                dataLabels: {
                    orientation: 'horizontal',
                    position: 'center'
                }
            }
        },
        dataLabels: {
            style: {
                colors: ['#000']
            },
            offsetY: 15,
            formatter: function (val) {
                return number_format(val, 2, '.', ',') + data.settings.unit
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return number_format(val, 2, '.', ',') + data.settings.unit
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector(chartId), options);
    chart.render();
    chart.updateSeries([{
        name: data.settings.name,
        data: [(data.billable + data.non_billable).toFixed(2), data.billable.toFixed(2), data.non_billable.toFixed(2)]
    }]);
    return chart;
}

function pieChart(data) {
    chartId = "#" + data.settings.id;
    var options = {
        chart: {
            height: 350,
            type: data.settings.id == 'percentage-chart' ? 'pie' : 'donut',
        },
        legend: {
            show: true,
            position: 'bottom',
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#000']
            },
            formatter: function (val, opts) {
                return number_format(opts.w.config.series[opts.seriesIndex], 2, '.', ',') + data.settings.unit;
            },
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: data.settings.id == 'percentage-chart' ? false : true,
                        name: {
                            show: true,
                        },
                        value: {
                            show: true,
                            fontSize: '24px',
                        },
                        total: {
                            show: data.settings.id == 'percentage-chart' ? false : true,
                            showAlways: true,
                            label: _lang.total,
                            fontSize: '22px'
                        }
                    }
                },
            }
        },
        tooltip: {
            x: {
                show: false,
            },
            y: {
                formatter: function (val, opts) {
                    return number_format(val, 2, '.', ',') + data.settings.unit;
                }
            }
        },
        series: [],
        colors: ["#008ffb", "#00e396", "#ff9718", "#ff4560", "#775dd0", "#ff1411", "#00d7fe", "#22e01a", "#0001ff", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9", "#008ffb", "#00e396", "#ff9718", "#ff4560", "#775dd0", "#ff1411", "#00d7fe", "#22e01a", "#0001ff", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9"],
        labels: [_lang.timeTrackingStatus.billable, _lang.timeTrackingStatus.internal]
    };
    var chart = new ApexCharts(
        document.querySelector(chartId),
        options
    );
    chart.render();
    chart.updateSeries([data.billable, data.non_billable]);
    return chart;
}

function timeLogsPerMonth(data){
    var options = {
        series: [],
        chart: {
            type: 'bar',
            height: 310
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '70%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: data.names,
        },
        yaxis: {
            labels: {
                style: {
                    cssClass: 'horizontal-bar-chart-labels',
                },
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + "h"
                }
            }
        }
    };
    var chart = new ApexCharts(document.querySelector('#time-logs-per-month'), options);
    chart.render();
    chart.updateSeries([{
        name: _lang.billableTarget,
        data: data.target
    }, {
        name: _lang.timeTrackingStatus.billable,
        data: data.billable
    }, {
        name: _lang.timeTrackingStatus.internal,
        data: data.internal
    }]);
    return chart;
}
function billableUtilizationRatePerUser(data) {
    var options = {
        series: [],
        colors: ["#008ffb", "#00e396", "#ff4560"],
        chart: {
            type: 'bar',
            height: data.names.length > 7 ? (data.names.length * 45) : '310',
        },
        noData: {
            text: _lang.noData,
            align: 'center',
            verticalAlign: 'middle',
            offsetX: 0,
            offsetY: 0,
            style: {
                color: undefined,
                fontSize: '14px',
                fontFamily: undefined
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '70%',
                dataLabels: {
                    position: 'bottom'
                },
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: _lang.languageSettings['langDirection'] === 'rtl' ? 'end' : 'start',
            style: {
                colors: ['#4d4f4e'],
                fontSize: 14,
            },
            formatter: function (val, opt) {
                return val + '%'
            },
            offsetX: 0,
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        xaxis: {
            labels: {
                show: false,
            },
            categories: data.names,
        },
        yaxis: {
            labels: {
                show: true,
                align: _lang.languageSettings['langDirection'] === 'rtl' ? 'left' : 'right',

                style: {
                    cssClass: 'vertical-bar-chart-labels',
                },
                formatter: function (val, opt) {
                    return val.length > 40 ? val.substring(0, 40) : val
                },
                maxWidth: 400,
            },
            reversed: _lang.languageSettings['langDirection'] === 'rtl' ? true : false,
        },
        tooltip: {
            theme: 'dark',
            x: {
                show: true,
            },
            y: {
                formatter: function (val) {
                    return val.toFixed(2) + '%';
                },
                title: {
                    formatter: function () {
                        return ''
                    }
                }
            }
        }
    };
    var chart = new ApexCharts(document.querySelector("#billable-utilization-rate-per-user"), options);
    chart.render();
    chart.updateSeries([{
        name: 'Billable Utilization Rate',
        data: data.values
    }]);
    return chart;
}

function nonBillableUtilizationRatePerUser(data) {
    var options = {
        series: [],
        colors: ["#008ffb", "#00e396", "#ff4560"],
        chart: {
            type: 'bar',
            height: data.names.length > 7 ? (data.names.length * 45) : '310',
            
        },
        noData: {
            text: _lang.noData,
            align: 'center',
            verticalAlign: 'middle',
            offsetX: 0,
            offsetY: 0,
            style: {
                color: undefined,
                fontSize: '14px',
                fontFamily: undefined
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '70%',
                dataLabels: {
                    position: 'bottom'
                },
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: _lang.languageSettings['langDirection'] === 'rtl' ? 'end' : 'start',
            style: {
                colors: ['#4d4f4e'],
                fontSize: 14,
            },
            formatter: function (val, opt) {
                return val + '%'
            },
            offsetX: 0,
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        xaxis: {
            labels: {
                show: false,
            },
            categories: data.names,
        },
        yaxis: {
            labels: {
                show: true,
                align: _lang.languageSettings['langDirection'] === 'rtl' ? 'left' : 'right',

                style: {
                    cssClass: 'vertical-bar-chart-labels',
                },
                formatter: function (val, opt) {
                    return val.length > 40 ? val.substring(0, 40) : val
                },
                maxWidth: 400,
            },
            reversed: _lang.languageSettings['langDirection'] === 'rtl' ? true : false,
        },
        tooltip: {
            theme: 'dark',
            x: {
                show: true,
            },
            y: {
                formatter: function (val) {
                    return val.toFixed(2) + '%';
                },
                title: {
                    formatter: function () {
                        return ''
                    }
                }
            }
        }
    };
    var chart = new ApexCharts(document.querySelector("#non-billable-utilization-rate-per-user"), options);
    chart.render();
    chart.updateSeries([{
        name: 'Non Billable Utilization Rate',
        data: data.values
    }]);
    return chart;
}
//function exportDashboardToPdf() {
//    var dataURL = [];
//    jQuery.each(charts, function (key, chart) {
//        if (chart != undefined) {
//            dataURL.push(chart.dataURI().then(function (img) {
//                return { number: key, data: img.imgURI != undefined ? img.imgURI : img.blob };
//            }));
//        }
//    });
//    Promise.all(dataURL).then(function (values) {
//        //jQuery.each(values, function (key, value) { 
//        //    if(value.data.blob != undefined){
//        //        var reader = new FileReader();
//        //        reader.readAsDataURL(value.data.blob);
//        //        reader.onloadend = function () {
//        //            values[key].data = reader.result;
//        //        }
//        //    }else{
//        //        values[key].data = value.data.imgURI;
//        //    }
//        //});
//        jQuery.ajax({
//            dataType: 'JSON',
//            url: getBaseURL() + 'dashboard/export_time_tracking_dashboard_pdf',
//            data: { images: values },
//            type: 'POST',
//            beforeSend: function () {
//                jQuery('#loader-global').show();
//            },
//            success: function (response) {
//                window.location = getBaseURL() + 'dashboard/download_dashboard_pdf/' + response.file_name;
//            },
//            complete: function () {
//                jQuery('#loader-global').hide();
//            },
//            error: defaultAjaxJSONErrorsHandler
//        });
//    });
//}
function exportDashboardToPdf() {
    var dataURL = [];
    jQuery.each(charts, function (key, chart) {
        if (chart != undefined) {
            dataURL.push(chart.dataURI().then(function (imgURI) {
                return { number: key, data: imgURI.imgURI };
            }));
        }
    });
    Promise.all(dataURL).then(function (values) {
        jQuery.ajax({
            dataType: 'JSON',
            url: getBaseURL() + 'dashboard/export_time_tracking_dashboard_pdf',
            data: { images: values },
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                window.location = getBaseURL() + 'dashboard/download_dashboard_pdf/' + response.file_name;
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    });
}