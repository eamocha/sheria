jQuery(document).ready(function() {
	jQuery('.multi-select', '#expenseReportSearchFilters').chosen({no_results_text: _lang.no_results_matched,placeholder_text: _lang.select,width: "100%"}).change();
	makeFieldsDatePicker({fields: ['paidOnValue', 'paidOnEndValue']});
	caseLookup(jQuery('#caseValue', '#expenseReportSearchFilters'), false);
	jQuery("#clientValue").autocomplete({
		autoFocus: true,
		delay: 600,
		source: function(request, response) {
			request.term = request.term.trim();
			jQuery.ajax({
				url: getBaseURL('money') + 'clients/autocomplete',
				dataType: "json",
				data: request,
				success: function(data) {
					if (data.length < 1) {
						response([{
								label: _lang.no_results_matched.sprintf([request.term]),
								value: '',
								record: {
									id: -1,
									term: request.term
								}
							}]);
					} else {
						response(jQuery.map(data, function(item) {
							return {
								label: item.name,
								value: item.name,
								record: item
							};
						}));
					}
				},error: defaultAjaxJSONErrorsHandler
			});
		},
		response: function(event, ui) {
		},
		minLength: 3,
		select: function(event, ui) {
		}
	});
        jQuery('#expenseCategoryValue_chosen .chosen-choices li input').addClass('form-control');
});
function showReportResults() {
	var $form = jQuery('#expenseReportSearchFilters');
	var $viewPort = jQuery('#viewPort');
	var filters = $form.serialize();
	jQuery.ajax({
		url: getBaseURL('money') + 'reports/expenses',
		type: 'POST',
		dataType: 'JSON',
		data: filters,
		beforeSend: function() {
			$viewPort.html('<div class="col-md-12 no-margin loading">&nbsp;</div>');
		},
		success: function(response) {
			enableAll($form);
			if(response.status == 200){
				$viewPort.html(response.html);
				response.containData ? jQuery('#reportActions').removeClass('d-none') : jQuery('#reportActions').addClass('d-none')
			}else{
				$viewPort.html("");
			}
		},
		error: defaultAjaxJSONErrorsHandler
	});
}
function exportStatementOfExpensesToExcel() {
			var newFormFilter = jQuery('#expenseReportSearchFilters');
			newFormFilter.attr('action', getBaseURL('money') + 'reports/export_excel_statement_of_expenses').submit();
			newFormFilter.attr('action', '');
			enableAll(newFormFilter);
	}
function exportStatementOfExpensesToWord() {
			var newFormFilter = jQuery('#expenseReportSearchFilters');
			newFormFilter.attr('action', getBaseURL('money') + 'reports/export_word_statement_of_expenses').submit();
			newFormFilter.attr('action', '');
			enableAll(newFormFilter);
	}
