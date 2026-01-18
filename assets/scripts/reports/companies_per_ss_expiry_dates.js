var gridId = 'companyGrid';
var enableQuickSearch = false;
var categoryViewSelected = true;
function advancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		companiesLoadEventsForFilters();
		jQuery('#filtersFormWrapper').slideDown();
	} else {
		scrollToId('#filtersFormWrapper');
	}
}
function hideAdvancedSearch() {
	jQuery('#filtersFormWrapper').slideUp();
}

function checkWhichTypeOfFilterIUseAndReturnFiltersToCompany() {
	var filtersForm = jQuery('#companySearchFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('companySearchFilters', '.', true);
	var filters = '';
	if (!enableQuickSearch) {
		filters = searchFilters.filter;
	} else if (jQuery('#quickSearchFilterCompanyValue', filtersForm).val() || categoryViewSelected) {
		filters = searchFilters.quickSearch;
	}
	filters.customFields = searchFilters.customFields;
	enableAll(filtersForm);
	return filters;
}
function exportToExcel() {
	var newFormFilter = jQuery('#exportResultsForm');
	var filters = checkWhichTypeOfFilterIUseAndReturnFiltersToCompany();
	jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('companySearchFilters')));
	newFormFilter.attr('action', getBaseURL() + 'export/companies_per_ss_expiry_dates').submit();
}
function companiesLoadEventsForFilters() {
	if (jQuery('.visualize-hijri-date', '#companySearchFilters').length > 0) {
		makeFieldsHijriDatePicker({fields: ['expiresOnValue', 'expiresOnDateEndValue']});
	} else {
		makeFieldsDatePicker({fields: ['expiresOnValue', 'expiresOnDateEndValue']});
	}
}
