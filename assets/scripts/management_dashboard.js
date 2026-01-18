jQuery(document).ready( function(){
    jQuery('.select-picker').selectpicker();
    jQuery('#main-container').attr('style', 'background-color: #F5F5F5 !important');
    jQuery('body').attr('style', 'background-color: #F5F5F5 !important');
    jQuery('.dashboard-table').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "searching": false,
        "pageLength": 5,
        "aaSorting": []
    });
    loadDashboardData();
  });
  
  function loadDashboardData(){
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'dashboard/management',
        data: {year: jQuery('#year').val()},
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            barChart(response.cases_by_filing, 'cases-by-filing', '#008ffb');
            barChart(response.cases_by_due_date, 'cases-by-due-date', '#00e396');
            var options = {
                chart: {
                    height: 320,
                    type: 'donut',
                },
                legend: {
                    show: true,
                    position: 'right',
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
                colors: ["#008ffb", "#00e396", "#ff9718", "#ff4560", "#775dd0", "#ff1411", "#00d7fe", "#22e01a", "#0001ff", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9"],
                labels: response.cases_per_assignee.names,
            };
            var chart = new ApexCharts(
              document.querySelector('#cases-by-assignee'),
              options
            );
            chart.render();
            chart.updateSeries(response.cases_per_assignee.case_count);
            //
            options.labels = response.cases_per_status.names;
            var chart = new ApexCharts(
                document.querySelector('#cases-by-status'),
                options
              );
              chart.render();
              chart.updateSeries(response.cases_per_status.values);
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
  function barChart(data, chartId, color){
    var options = {
        chart: {
            height: 309,
            type: 'bar',
        },
        series: [],
        colors: [color],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            labels: {
              show: true,
              style: {
                  color: 'blue',
                  fontSize: '12px'
              },
            }
        },
        yaxis:{
            show: false,
        }
    }
    var chart2 = new ApexCharts(document.querySelector("#" + chartId), options);
    chart2.render();
    chart2.updateSeries([{
      data: data,
      }]);
  }