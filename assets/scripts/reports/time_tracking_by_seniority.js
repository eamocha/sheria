jQuery(document).ready(function () {
	if (jQuery.isEmptyObject(document.chart_data) == false && document.dataFetched > 0) {
		var plot1 = jQuery.jqplot('chart', [document.chart_data],
				{
					seriesDefaults: {
						renderer: jQuery.jqplot.PieRenderer,
						rendererOptions: {
							showDataLabels: true
						}
					},
					legend: {
						show: true,
						location: 'e'
					}
				}
		);
	} 
	jQuery(window).resize(function () {
		plot1.replot({axes: {xaxis: {min: null, max: null}}});
	});
});
function export_to_pdf(caseId) {
		window.location = getBaseURL() + 'reports/export_time_tracking_seniority_pdf/' + caseId ;	
}