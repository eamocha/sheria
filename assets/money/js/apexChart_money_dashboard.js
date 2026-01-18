function barChart(data) {
    valuesType = ('values_type' in data ) ? data.values_type : false
    columnsNb = jQuery('#columns-nb-money-dashboard').val();
    barChartColumns = getBarColumns(data)
    height = changeWidgetContainerHeight(data.settings.orientation, barChartColumns.length)
    colorsArray = getColorsArray(valuesType, data.values)
    chartId = "#chart-" + data.settings.id;
    changeWidgetMargin()
    addScrollBar(barChartColumns.length, data.settings.id)
    var options = {
        series: [],
        legend: {
            show: false,
        },
        colors: valuesType ? colorsArray : ["#00e396", "#775dd0", "#ff4560", "#00d7fe", "#ff6929", "#0001ff", "#f7fe00", "#ed65ff", "#22e01a", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9", "#008ffb", "#00e396", "#ff9718", "#ff4560", "#775dd0", "#ff1411", "#00d7fe", "#22e01a", "#0001ff", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9", "#ff9718", "#ff1411", "#008ffb"],
        chart: {
            id: data.settings.id,
            type: 'bar',
            height: height,
            width: '98.5%',
            toolbar: {
                show: false
            },
            sparkline: {
                enabled: false
            },
            events: {
                dataPointMouseEnter: function(event) {
                  event.path[0].style.cursor = "pointer";
                }
            }
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
                horizontal: data.settings.orientation =='horizontal' ? true : false,
                barHeight: '100%',
                distributed: true,
                dataLabels: {
                    position: valuesType ? 'center' : 'middle'
                },
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: data.settings.orientation =='horizontal' ? ( _lang.languageSettings['langDirection'] === 'rtl' ? 'end' : 'start') : 'middle',
            style: {
                colors: ['#545454'],
                fontSize: data.settings.orientation =='horizontal' ? 12 : 12,
            },
            formatter: function (val, opt) {
                return (number_format(val, 2, '.', ',') + ' ' + data.currency)
            },
            offsetY: 0,
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        xaxis: {
            labels: {
                show: data.settings.orientation =='horizontal' ? false : true,
                rotate: _lang.languageSettings['langDirection'] === 'rtl' ? -340 : -20, 
                trim: true,
                style: {
                    fontSize: data.settings.orientation =='horizontal' ? '10px' : '12px',
                },
                offsetX: 0,
                offsetY: -5,
            },
            categories: barChartColumns,
            offsetY: 0
        },
         yaxis: {
            floating: false,
            axisTicks: {
                show: true
            },
            axisBorder: {
                show: true
            },
            labels: {
                show: true,
                align: _lang.languageSettings['langDirection'] === 'rtl' ? 'center' : 'right',
                style: {
                    cssClass: 'horizontal-bar-chart-labels',
                },
                formatter: function (val, opt) {
                    return val.length > 20 ? val.substr(0, 19) + '...' : val
                },
                offsetX: data.settings.orientation =='horizontal' ? 10 : 0,
            },
            reversed: false,
        },
        tooltip: {
            theme: 'dark',
            x: {
                show: true,
            },
            y: {
                formatter: function (val) {
                    return number_format(val, 2, '.', ',') + ' ' + data.currency;
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
    ApexCharts.exec(data.settings.id, 'updateSeries', [{
        data: data.values
        }], true);
    ApexCharts.exec(data.settings.id, 'updateOptions', {
        xaxis: {
            categories: barChartColumns.length > 0 ? barChartColumns : ['']
        },
        chart: {
            height: height,
        },
    }, false, true); 
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
            categories: barChartColumns,
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
    var pieChartValues = []
    var pieChartLabels = [];
    for (var value of data.values) {
        pieChartValues.push(parseFloat(value))   
    }
    for (var column of data.names) {
        if(column in data){
            if('lang' in data[column]){
                pieChartLabels.push(_lang[data[column]['lang']][column]) 
            }else{
                pieChartLabels.push(_lang[column])
            }
        } else {
            pieChartLabels = data.names 
            break;
        }
    }
    chartId = "#chart-" + data.settings.id;
    var options = {
        chart: {
            id: data.settings.id,
            height: 357,
            type: 'donut',
            events: {
                dataPointMouseEnter: function(event) {
                  event.path[0].style.cursor = "pointer";
                }
            }
        },
        legend: {
            show: true,
            position: 'bottom',
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return [number_format(opts.w.config.series[opts.seriesIndex], 2, '.', ',') , data.currency]
            },
            style: {
                colors: ['#545454'],
                fontSize: 14,
                fontWeight: 'normal',
            },
            dropShadow: {
                enabled: false,
            }
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
                            fontSize: '18px',
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: _lang.total,
                            fontSize: '18px',
                            formatter: function (w) {
                                var value =  w.globals.seriesTotals.reduce((a, b) => {
                                  return a + b
                                }, 0)
                                return number_format(value, 2, '.', ',') + ' ' + data.currency
                              }
                        }
                    }
                },
            }
        },
        tooltip: {
            y: {
                formatter: function(value, w) {
                    var total =  w.globals.seriesTotals.reduce((a, b) => {
                        return a + b
                      }, 0)
                    var percentage = value / total * 100 
                    return number_format(value, 2, '.', ',') + ' ' + data.currency + ' (' + percentage.toFixed(2) + ' %)'
                }
            }
        },
        series: [],
        colors: data.colors ? data.colors : ["#ff9718", "#ff1411", "#008ffb", "#00e396", "#C0C0C0", "#0001ff", "#775dd0", "#ff4560", "#00d7fe", "#22e01a", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9", "#008ffb", "#00e396", "#ff9718", "#ff4560", "#775dd0", "#ff1411", "#00d7fe", "#22e01a", "#0001ff", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9"],
        labels: pieChartLabels.length > 0 ? pieChartLabels : [''],
    };
    var chart = new ApexCharts(
        document.querySelector(chartId),
        options
    );
    chart.render();
    ApexCharts.exec(data.settings.id, 'updateSeries', pieChartValues.length > 0 ? pieChartValues : [0], true);
    ApexCharts.exec(data.settings.id, 'updateOptions', {
        labels: pieChartLabels.length > 0 ? pieChartLabels : ['']
    }, false, true);    
    return chart;
}
function table (data) {
    jQuery("#chart-" + data.settings.id).html('<table id="table-widget-'+data.settings.id+'" class="table stripe hover"></table>')
    jQuery("#chart-cont-"+data.settings.id).css("width", "99%");
    jQuery("#chart-cont-"+data.settings.id).addClass("mt-15");
    var datatable = jQuery("#table-widget-"+data.settings.id).DataTable( {
        data: data.values,
        columns: [
            { data: 'accountName' , title: _lang.accountName ,render: function(data, type) {
                if (type === 'display') {
                    return '<a style="cursor: pointer;" class="text-primary">' + data + '</a>';
                }
                return data;
            }}, 
            { data: 'currency', title: _lang.currency},
            { data: 'balance', title: _lang.balance , render: jQuery.fn.dataTable.render.number( ',', '.', 2 )},
            { data: 'localAmount', title: 'Local' },
        ],
        "columnDefs": [
            {
                "targets": [ 3 ],
                "visible": false,
                "searchable": false
            }
        ],
        "bPaginate": false,
        "order": [ 3, 'desc' ],
        "scrollY": "220px",
        "info": false,
        "scrollCollapse": true,
    } );
    jQuery("a", "#table-widget-"+data.settings.id).click( function () {
        var rowData = datatable.row( jQuery(this).parents('tr') ).data();
        var drilldown = jQuery('#chart-drilldown-'+data.settings.id)
        drilldown.addClass('active')
        if (drilldown.hasClass("active")) {
            drilldownRelatedToColumn(rowData.id, data, [], rowData.accountName);
        }
    });
    jQuery(".dataTables_scrollBody", "#chart-cont-"+data.settings.id).css("border-bottom", "1px solid #ddd");
    jQuery(".dataTables_scroll", "#chart-cont-"+data.settings.id).css({"border-left": "1px solid #E0E0E0", "border-right": "1px solid #E0E0E0","margin-right": "15px", "margin-left": "15px"});
    jQuery(".odd", "#chart-cont-"+data.settings.id).css("background-color","#F6FBFF");
    jQuery(".sorting", "#chart-cont-"+data.settings.id).css({"background-color":"#629fd3", "color": "#fff"});
    jQuery(".dataTables_filter", "#chart-cont-"+data.settings.id).css({"margin":"0px 15px 8px 0"});
    jQuery("#table-widget-" + data.settings.id + "_wrapper").removeClass("form-inline");
    jQuery(".dataTables_scrollHeadInner", "#chart-cont-"+data.settings.id).css({"width":"100%"});
    jQuery("table", "#chart-cont-" + data.settings.id).css({"width":"100%"});
    jQuery("input", "#table-widget-" + data.settings.id + "_filter").css({"width":"79%", "display":"inline"});
}
function getBarColumns(data) {
    var barChartColumns = [];
    for (var column of data.names) {
        if(column in data){
            barChartColumns.push(_lang[column])
        } else {
            barChartColumns = data.names 
            break;
        }
    }
    return barChartColumns
}
function changeWidgetMargin() {
    jQuery(".apexcharts-canvas",".money_dashboard_chart").css({"margin-left": "-10px"});
}
function addScrollBar(length, id){
    if (length > 10) {
        jQuery("#chart-cont-"+ id).css(
            {
                "min-height": "330px", 
                "max-height": "330px",
                "min-width": "98%",
                "max-width": "98%",
                "overflow": "auto",
                "scrollbar-width": "thin"
            } 
        );
        jQuery("#chart-cont-" + id).addClass('add-scrollbar');
    }
}
function changeWidgetContainerHeight(orientation, length){
    if (orientation =='horizontal' && length > 10){
        return length * 34
    } else if (orientation =='horizontal' && length < 7 && length > 1){
        return length * 50
    } else if (orientation =='horizontal' && length == 1){
        return '75'
    } else {
        return '340' 
    }
} 
function getColorsArray(valuesType, values){
    colorsArray = [];
    if (valuesType) values.forEach(value => Number(value) < 0 ? colorsArray.push("#ff1411") : colorsArray.push("#008ffb"))
    return colorsArray
}
