var charts = [];
jQuery(document).ready(function () {
    jQuery('#main-container').attr('style', 'background-color: #F5F5F5 !important');
    jQuery('body').attr('style', 'background-color: #F5F5F5 !important');
    loadDashboardData();
    var container = jQuery('#litigation-dashboard');
    jQuery(".dashboard-date-filter", container).bootstrapDP({
        weekStart: 1,
        todayHighlight: true,
        format: "yyyy-mm-dd",
        autoclose: true,
        showOnFocus: false,
        language: _lang.languageSettings['langName'],
        startDate: jQuery('#start-date-input').val(),
        defaultViewDate: jQuery('#start-date-input').val(),
        endDate: Infinity,
        viewMode: 'days',
        minViewMode: 'days'
    });
});
function loadDashboardData(widgets) {
    widgets = widgets || 'all';
    var container = jQuery('#litigation-dashboard');
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'dashboard/litigation_dashboard/' + jQuery('#dashboard-number', container).val(),
        data: { from_date: jQuery('#from-date-input', container).val(), to_date: jQuery('#to-date-input', container).val(), widgets: widgets},
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            jQuery.each(response, function (key, widget) {
                charts[widget.settings.chart_number] = window[widget.settings.type](widget);
            });
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function barChart(data) {
    chartId = "#" + data.settings.id;
    var options = {
        series: [],
        chart: {
            type: 'bar',
            height: data.names.length > 7 ? (data.names.length * 45) : '310',
            toolbar: {
                export: {
                    csv: {
                        filename: 'litigation_dashboard',
                        headerCategory: 'category',
                        headerValue: 'value',
                    },
                },
            },
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
                return val
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
                    cssClass: 'horizontal-bar-chart-labels',
                },
                formatter: function (val, opt) {
                    return val.length > 40 ? val.substring(0, 40) : val
                },
                maxWidth: 400,
            },
            reversed: _lang.languageSettings['langDirection'] === 'rtl'? true : false,
        },
        tooltip: {
            theme: 'dark',
            x: {
                show: true,
            },
            y: {
                formatter: function (val) {
                    return val.toFixed(0);
                },
                title: {
                    formatter: function () {
                        return ''
                    }
                }
            }
        }
    };
    var chart = new ApexCharts(document.querySelector(chartId), options);
    chart.render();
    chart.updateSeries([{
        data: data.values,
    }]);
    return chart;
}
function lineChart(data){
    chartId = "#" + data.settings.id;
    var options = {
        series: [],
        chart: {
            height: 310,
            type: 'line',
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 0.2
            },
            toolbar: {
                show: false
            },
            zoom:{
                enabled: false
            }
        },
        dataLabels: {
            enabled: true,
        },
        stroke: {
            curve: 'smooth'
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                opacity: 0.5
            },
        },
        tooltip: {
            x: {
                show: true
            },
            y: {
                title: {
                    formatter: function () {
                        return ''
                    }
                }
            }
        },
        xaxis: {
            categories: data.names,
        },
        yaxis: {
            labels: {
                show: true,
                align: _lang.languageSettings['langDirection'] === 'rtl' ? 'left' : 'right',
                style: {
                    cssClass: 'horizontal-bar-chart-labels',
                },
            }
        }
    };
    var chart = new ApexCharts(document.querySelector(chartId), options);
    chart.render();
    chart.updateSeries([{
        data: data.values,
    }]);
    return chart;
}
function pieChart(data){
    chartId = "#" + data.settings.id;
    var options = {
        chart: {
            height: 357,
            type: 'donut',
        },
        legend: {
            show: true,
            position: 'bottom',
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex]
            },
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        name: {
                            show: true,
                        },
                        value: {
                            show: true,
                            fontSize: '24px',
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: _lang.total,
                            fontSize: '22px'
                        }
                    }
                },
            }
        },
        series: [],
        colors: ["#008ffb", "#00e396", "#ff9718", "#ff4560", "#775dd0", "#ff1411", "#00d7fe", "#22e01a", "#0001ff", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9", "#008ffb", "#00e396", "#ff9718", "#ff4560", "#775dd0", "#ff1411", "#00d7fe", "#22e01a", "#0001ff", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9"],
        labels: data.names.length > 0 ? data.names : [''],
    };
    var chart = new ApexCharts(
        document.querySelector(chartId),
        options
    );
    chart.render();
    chart.updateSeries(data.values.length > 0 ? data.values : [0]);
    return chart;
}
function exportDashboardToPdf() {
    var dataURL = [];
    var container = jQuery('#litigation-dashboard');
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
            url: getBaseURL() + 'dashboard/export_litigation_dashboard_pdf',
            data: {
                from_date: jQuery('#from-date-input', container).val(),
                to_date: jQuery('#to-date-input', container).val(),
                dashboard_number: jQuery('#dashboard-number', container).val(),
                images: values
            },
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