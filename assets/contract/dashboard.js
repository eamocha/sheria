jQuery(document).ready(function () {
    jQuery('#main-container').attr('style', 'background-color: #F5F5F5 !important');
    jQuery('body').attr('style', 'background-color: #F5F5F5 !important');
    jQuery('.select-picker', '#contract-dashboard').selectpicker({ dropupAuto: false });
    loadDashboardData();
    contractsPerStatusPieCharts();
    contractsPerPartyPieCharts();
    contractsPerDepartmentPieCharts();
    barCharts();
});

function loadDashboardData() {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'dashboard/index',
        type: 'GET',
        beforeSend: function () {
            jQuery('#month-expired-contract-list li').remove();
            jQuery('#quarter-expired-contract-list li').remove();
            jQuery('#next-quarter-expired-contract-list li').remove();
            jQuery('#received-contract-list li').remove();
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.expiring_contracts_this_month.length > 0) {
                jQuery.each(response.expiring_contracts_this_month, function (key, value) {
                    if (key < 5) {
                        jQuery("#month-expired-contract-list").append("<li><span class='priority-" + value.priority + "'></span><span class='widget-item-description contracts-description'><a href='" + getBaseURL('contract') + "contracts/view/" + value.id + "'> " + value.contract_id + "</a>: " + value.name + "</span> <span title='" + value.end_date + "' class='float-right tooltip-title widget-date'>" + value.end_date + "</span><br><span class='widget-data widget-contract-type'>" + value.type + "</span></li>");
                    }
                });
            } else {
                jQuery("#month-expired-contract-list").append("<li><p class='margin-10'>" + _lang.contratDashboardNoRecords + "</p></li>");
            }
            if (response.expiring_contracts_this_quarter.length > 0) {
                jQuery.each(response.expiring_contracts_this_quarter, function (key, value) {
                    if (key < 5) {
                        jQuery("#quarter-expired-contract-list").append("<li><span class='priority-" + value.priority + "'></span><span class='widget-item-description contracts-description'><a href='" + getBaseURL('contract') + "contracts/view/" + value.id + "'> " + value.contract_id + "</a>: " + value.name + "</span> <span title='" + value.end_date + "' class='float-right tooltip-title widget-date'>" + value.end_date + "</span><br><span class='widget-data widget-contract-type'>" + value.type + "</span></li>");
                    }
                });
            } else {
                jQuery("#quarter-expired-contract-list").append("<li><p class='margin-10'>" +  _lang.contratDashboardNoRecords + "</p></li>");
            }
            if (response.expiring_contracts_next_quarter.length > 0) {
                jQuery.each(response.expiring_contracts_next_quarter, function (key, value) {
                    if (key < 5) {
                        jQuery("#next-quarter-expired-contract-list").append("<li><span class='priority-" + value.priority + "'></span><span class='widget-item-description contracts-description'><a href='" + getBaseURL('contract') + "contracts/view/" + value.id + "'> " + value.contract_id + "</a>: " + value.name + "</span> <span title='" + value.end_date + "' class='float-right tooltip-title widget-date'>" + value.end_date + "</span><br><span class='widget-data widget-contract-type'>" + value.type + "</span></li>");
                    }
                });
            } else {
                jQuery("#next-quarter-expired-contract-list").append("<li><p class='margin-10'>" +  _lang.contratDashboardNoRecords + "</p></li>");
            }
            if (response.received_contracts.length > 0) {
                jQuery.each(response.received_contracts, function (key, value) {
                    if (key < 5) {
                        jQuery("#received-contract-list").append("<li><span class='priority-" + value.priority + "'></span><span class='widget-item-description contracts-description'><a href='" + getBaseURL('contract') + "contracts/view/" + value.id + "'> " + value.contract_id + "</a>: " + value.name + "</span> <span title='" + value.status + "' class='float-right tooltip-title widget-status " + (value.status_category == 'in progress' ? 'in-progress' : value.status_category) + "'>" + (value.status.length > 18 ? value.status.substring(0, 15) + "..." : value.status) + "</span><br><span class='widget-data widget-contract-type'>" + value.type + "</span></li>");
                    }
                });
            } else {
                jQuery("#received-contract-list").append("<li><p class='margin-10'>" +  _lang.contratDashboardNoRecords + "</p></li>");
            }
            jQuery('#month-expired-contract-count').text(response.expiring_contracts_this_month.length);
            jQuery('#quarter-expired-contract-count').text(response.expiring_contracts_this_quarter.length);
            jQuery('#next-quarter-expired-contract-count').text(response.expiring_contracts_next_quarter.length);
            jQuery('#received-contract-count').text(response.received_contracts.length);
        },
        complete: function () {
            jQuery('#loader-global').hide();
            showToolTip();
            jQuery('.widget-tooltip').tooltipster({
                contentAsHTML: true,
                minWidth: 408,
            });
        },
        error: defaultAjaxJSONErrorsHandler
    });

}

function pieCharts(value, chartId) {

    var options = {
        chart: {
            height: 350,
            type: 'donut',
        },
        legend: {
            show: true,
            position: 'top',
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
                            label: _lang.contractDashboardTotal,
                            fontSize: '22px'
                        }
                    }
                },
            }
        },
        series: [],
        labels: value.index,
    };
    var chart = new ApexCharts(
        document.querySelector(chartId),
        options
    );
    chart.render();
    chart.updateSeries(value.values);
}

function contractsPerDepartmentPieCharts() {
    var contractsPerDepartment = {
        type: jQuery('#contract-type', '.object-per-department-widget').val(),
        year: jQuery('#contract-year', '.object-per-department-widget').val()
    };
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'dashboard/pie_charts_widgets',
        data: {filters: {contracts_per_department: contractsPerDepartment}},
        type: 'GET',
        beforeSend: function () {
            jQuery('.loader-submit', '.object-per-department-widget').addClass('loading');
        },
        success: function (response) {
            pieCharts(response.pie_charts, "#pie-chart3");
        },
        complete: function () {
            jQuery('.loader-submit', '.object-per-department-widget').removeClass('loading');
        },
    });
}

function contractsPerStatusPieCharts() {
    var contractsPerStatus = {
        type: jQuery('#contract-type', '.object-per-status-widget').val(),
        year: jQuery('#contract-year', '.object-per-status-widget').val()
    };
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'dashboard/pie_charts_widgets',
        data: {filters: {contracts_per_status: contractsPerStatus}},
        type: 'GET',
        beforeSend: function () {
            jQuery('.loader-submit', '.object-per-status-widget').addClass('loading');
        },
        success: function (response) {
            pieCharts(response.pie_charts, "#pie-chart1");
        },
        complete: function () {
            jQuery('.loader-submit', '.object-per-status-widget').removeClass('loading');
        },
    });

}

function contractsPerPartyPieCharts() {
    var contractsPerParty = {
        type: jQuery('#contract-type', '.object-per-party-widget').val(),
        year: jQuery('#contract-year', '.object-per-party-widget').val()
    };
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'dashboard/pie_charts_widgets',
        data: {filters: {contracts_per_party: contractsPerParty}},
        type: 'GET',
        beforeSend: function () {
            jQuery('.loader-submit', '.object-per-party-widget').addClass('loading');
        },
        success: function (response) {
            pieCharts(response.pie_charts, "#pie-chart2");
        },
        complete: function () {
            jQuery('.loader-submit', '.object-per-party-widget').removeClass('loading');
        },
    });

}

function barChartsperValue() {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'dashboard/bar_charts_widgets',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            var options = {
                chart: {
                    height: 454,
                    type: 'bar',
                },
                title: {
                    // text: 'Contracts per Value this Year',
                    align: "left",
                    style: {
                        fontSize: '18px',
                        fontWeight: 'normal',
                        color: '#333354'
                    },
                },
                series: [],
                colors: ['#008ffb'],
                xaxis: {
                    categories: response.bar_charts.contracts_per_value.x_axis,
                    labels: {
                        show: true,
                        style: {
                            color: 'blue',
                            fontSize: '12px'
                        },
                    }
                },
                yaxis: {
                    show: false,
                }
            }
            var chart2 = new ApexCharts(document.querySelector("#contracts-per-value"), options);
            chart2.render();
            chart2.updateSeries([{
                data: response.bar_charts.contracts_per_value.y_axis,
            }]);

        },
        complete: function () {
            jQuery('#loader-global').hide();
            // showToolTip();
            jQuery('.widget-tooltip').tooltipster({
                contentAsHTML: true,
                minWidth: 408,
            });
        },
        error: defaultAjaxJSONErrorsHandler
    });

}
/*per month*/
function barCharts() {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'dashboard/bar_charts_widgets',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            var options = {
                chart: {
                    height: 454,
                    type: 'bar',
                },
                title: {
                    align: "left",
                    style: {
                        fontSize: '18px',
                        fontWeight: 'normal',
                        color: '#333354'
                    },
                },
                series: [{
                    data: response.bar_charts.contracts_per_value.y_axis, // Pass the raw numerical data
                }],
                colors: ['#008ffb'],
                xaxis: {
                    categories: response.bar_charts.contracts_per_value.x_axis,
                    labels: {
                        show: true,
                        style: {
                            color: 'blue',
                            fontSize: '12px'
                        },
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return new Intl.NumberFormat(undefined, {
                                style: 'currency',
                                currency: 'KES', // Assuming default currency is KES, adjust if needed
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(value);
                        },
                    },
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return new Intl.NumberFormat(undefined, {
                                style: 'currency',
                                currency: 'KES', // Assuming default currency is KES, adjust if needed
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(value);
                        }
                    }
                }
            };

            var chart2 = new ApexCharts(document.querySelector("#contracts-per-value"), options);
            chart2.render();
        },
        complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.widget-tooltip').tooltipster({
                contentAsHTML: true,
                minWidth: 408,
            });
        },
        error: defaultAjaxJSONErrorsHandler
    });
}