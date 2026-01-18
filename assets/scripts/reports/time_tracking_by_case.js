jQuery(document).ready(function() {
	if (document.dataFetched > 0) {
		jQuery.jqplot.config.enablePlugins = true;
		var ticks = document.ticks;
		plot1 = jQuery.jqplot('case_bar_chart_per_user', [document.data], {
			stackSeries: true,
			animate: true,
			animateReplot: true,
			seriesDefaults: {
				renderer: jQuery.jqplot.BarRenderer,
				rendererOptions: {
					barMargin: 8,
					highlightMouseOver: true
				},
				pointLabels: {
					show: true
				}
			},
			axes: {
				xaxis: {
					renderer: jQuery.jqplot.CategoryAxisRenderer,
					ticks: ticks,
					label: _lang.nbOfHoursLoggedPerUser
				},
				yaxis: {
					rendererOptions: {
						forceTickAt0: true,
					},
					tickOptions: {
						fontSize: '11px',
						formatter: function(format, value) {
							return hoursToMinutesHours(value);
						}
					},
					showTicks: true,
					padMin: 0
				}
			},
			seriesColors: ["#FFA086"],
			highlighter: {
				show: true,
				showlabel: true
			}
		});
	}
});
jQuery(window).resize(function () {
plot1.replot({axes: {xaxis: {min: null, max: null}}});
});
function filterByDateChange(element) {
	window.location = getBaseURL() + 'reports/time_tracking_by_case/' + document.legalCaseId + '/' + jQuery(element).val();
}