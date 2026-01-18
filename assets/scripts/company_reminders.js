var enableQuickSearch = true;
var remindersDataSrc = new kendo.data.DataSource({
	transport: {
		read: {
			url: getBaseURL() + "reminders/show_all_reminders",
			dataType: "JSON",
			type: "POST",
			complete: function () {
				if (jQuery('#filtersFormWrapper').is(':visible'))
					jQuery('#filtersFormWrapper').slideUp();
				if (_lang.languageSettings['langDirection'] === 'rtl')
					gridScrollRTL();
				jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
				animateDropdownMenuInGrids('reminderGrid');
			}
		},
		parameterMap: function (options, operation) {
			if ("read" == operation) {
				options.filter = getFormFilters();
				options.returnData = 1;
				jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
			}
			return options;
		}
	},
	schema: {type: "json", data: "data", total: "totalRows",
		model: {
                    id: "id",
                    fields: {
                        id: {type: "number"},
                        reminderID: {type: "number"},
                        summary: {type: "string"},
                        type: {type: "string"},
                        status: {type: "string"},
                        legal_case: {type: "string"},
                        contact: {type: "string"},
                        company: {type: "string"},
                        task: {type: "string"},
                        remindDate: {type: "date"},
                        remindTime: {type: "string"},
                        remindUser: {type: "string"},
                        createdOn: {type: "date"},
                        createdByName: {type: "string"}
                    }
		},
                parse: function(response) {
                    var rows = [];
                    if(response.data){
                        var data = response.data;
                        rows = response;
                        rows.data = [];
                        for (var i = 0; i < data.length; i++) {
                            var row = data[i];
                            row['summary'] = escapeHtml(row['summary']);
                            row['createdByName'] = escapeHtml(row['createdByName']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
	},error: function(e) {defaultAjaxJSONErrorsHandler(e.xhr)},
	pageSize: 20, serverPaging: true, serverFiltering: true, serverSorting: true
});
var reminderGridOptions = {
	autobind: true,
	dataSource: remindersDataSrc,
	columns:[
		{
		field:"id",
		filterable: false,
		template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
		'<a class="dropdown-item" href="javascript:;" onclick="reminderForm(#= id #);">' + _lang.viewEdit + '</a>' +
		'<a class="dropdown-item" href="javascript:;" onclick="dismissReminder(#= id #);">' + _lang.dismiss + '</a>' +
		'<a class="dropdown-item" href="javascript:;" onclick="reminderForm(#= id #,\'postpone\');">' + _lang.postpone + '</a>' +
		'<a class="dropdown-item" href="javascript:;" onclick="checkReminderRecurrence(#= id #);">' + _lang.deleteRow + '</a>' +
		'</div></div>',
		title: _lang.actions,
		width: '120px'
		},
		{field: "remindDate", format: "{0:yyyy-MM-dd}", template: "#= (remindDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(remindDate) : remindDate, 'yyyy-MM-dd'))#", title: _lang.remindOn, width: '140px'},
		{field: "remindTime", template: '#= remindTime.substring(0, 5) #', title: _lang.time, width: '104px'},
		{field: "summary", template: '<a href="javascript:;" onclick="reminderForm(#= id #);">#= summary #</a>', title: _lang.summary, width: '160px'},
		{field: "type", title: _lang.type, width: '100px'},
		{field: "status", title: _lang.status, width: '120px'},
		{field: "remindUser", title: _lang.remindUser,template: '#= (remindUser!=null && userStatus=="Inactive")? remindUser+" ("+_lang.custom[userStatus]+")":((remindUser!=null)?remindUser:"") #', width: '184px'},
		{field: "legal_case", title: _lang.relatedCase, width: '180px'},
		{field: "contact", title: _lang.relatedContact, width: '180px'},
		{field: "task", title: _lang.relatedTask, width: '180px'},
		{field: "createdByName", title: _lang.createdBy,template: '#= (createdByName!=null && createdByStatus=="Inactive")? createdByName+" ("+_lang.custom[createdByStatus]+")":((createdByName!=null)?createdByName:"") #', width: '140px'},
		{field: "createdOn", format: "{0:yyyy-MM-dd}", template: '#= (kendo.toString((hijriCalendarEnabled == 1 ? gregorianToHijri(createdOn) : createdOn), \'yyyy-MM-dd\')) #', title: _lang.createdOn, width: '140px'}

	],
	editable: false,
	filterable: false,
        resizable: true,
	height: 480,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: false,buttonCount:5
        },	reorderable: true,
	scrollable: true,
        selectable: "single",
	sortable:{mode: "multiple"},
	toolbar: [{
			name: "toolbar-menu",
			template: '<div class="col-md-4 no-padding">'
                                + '<div class="input-group col-md-7">'
                                + '<input type="text" class="form-control search quick-search-filter" placeholder=" '+ _lang.reminders + '" id="reminderLookUp" onkeyup="reminderQuickSearch(event.keyCode, this.value);" title="'+ _lang.searchReminder + '" />'
                                + '</div>'
                                + '</div>'
		}],
	columnMenu: {messages: _lang.kendo_grid_sortable_messages}
};
function advancedSearchFilters(){ 
	if(!jQuery('#filtersFormWrapper').is(':visible')){
		loadEventsForFilters();
		jQuery('#filtersFormWrapper').removeClass('d-none').slideDown();
	}else{
		scrollToId('#filtersFormWrapper');
	}
}
function loadEventsForFilters(){

    if (jQuery('.hijri-date-picker', '#remindDateContainer').length > 0) 
      makeFieldsHijriDatePicker({ fields: ['remindDateValue','remindDateEndValue','modifiedOnValue','modifiedOnEndValue','createdOnValue','createdOnEndValue'] });
    else
      makeFieldsDatePicker({fields: ['remindDateValue', 'remindDateEndValue','modifiedOnValue','modifiedOnEndValue','createdOnValue','createdOnEndValue']});

	userLookup('userValue');
	userLookup('createdByValue');
	caseLookup(jQuery('#caseValue', $searchFiltersForm));
	taskLookup(jQuery('#taskValue', $searchFiltersForm));
	contactAutocompleteMultiOption('contactValue', '3', advancedSearchLookupFieldsResultHandler);
	companyAutocompleteMultiOption(jQuery('#companyValue', '#filtersFormWrapper'),advancedSearchLookupFieldsResultHandler);
}
function exportRemindersToExcel() {
	var newFormFilter = jQuery('#exportResultsForm');
	var filters = getFormFilters();
	jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
	jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('company-reminders-search-filters')));

	newFormFilter.attr('action', getBaseURL() + 'export/company_reminders').submit();
}
function advancedSearchLookupFieldsResultHandler(){
	return true;
}
function hideAdvancedSearch(){
	jQuery('#filtersFormWrapper').slideUp();
}
function reminderQuickSearch(keyCode, term){
	if(keyCode==13 ){//&& term.length > 1
		enableQuickSearch = true;
                document.getElementsByName("page").value =1;
                document.getElementsByName("skip").value =0;
		jQuery('#quickSearchFilterValue', '#filtersFormWrapper').val(term);
		jQuery('#reminderGrid').data("kendoGrid").dataSource.page(1);
	}
}
function searchReminders(){
	var grid = jQuery('#reminderGrid');
         document.getElementsByName("page").value =1;
         document.getElementsByName("skip").value =0;
	if (undefined == grid.data('kendoGrid')){
		grid.kendoGrid(reminderGridOptions);
		var gridGrid = grid.data('kendoGrid');
		return false;
	}
        grid.data("kendoGrid").dataSource.page(1);
	return false;
}
function getFormFilters(){
	var filtersForm = jQuery('#company-reminders-search-filters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('company-reminders-search-filters', '.', true);
	var filters = '';
	var reminderStatusDefault = jQuery('#quickSearchFilterStatusValue', '#filtersFormWrapper').val().length > 0;
	var quickSearchFilterValue = jQuery('#quickSearchFilterValue', '#filtersFormWrapper').val().length > 0;
	if(!enableQuickSearch){
		filters = searchFilters.filter;
		if(hijriCalendarEnabled == 1)
        {
            const DateHijriFieldsName = ["reminders.remindDate","reminders.createdOn"];
            var FiltersRecords = filters.filters;

            FiltersRecords.forEach(function( filter ) {
                                
                filter['filters'].forEach( function( elm ) 
                {
                    if( jQuery.inArray( elm['field'], DateHijriFieldsName ) != -1 )
                        elm['value'] = hijriToGregorian(elm['value']);
                });

            });
        }
	} else if(quickSearchFilterValue || reminderStatusDefault){
		filters = searchFilters.quickSearch;
	}
	enableAll(filtersForm);
	return filters;
}
var $searchFiltersForm = null;
jQuery(document).ready(function(){
	jQuery('.multi-select', '#company-reminders-search-filters').chosen({no_results_text: _lang.no_results_matched,placeholder_text: _lang.select,width: "100%"}).change();
	$searchFiltersForm = jQuery('form#company-reminders-search-filters');
	$searchFiltersForm.bind('submit', function(e){
		e.preventDefault();
		jQuery('#reminderLookUp').val('');
		enableQuickSearch = false;
		searchReminders();
	});
	searchReminders();
});
function reminderCallBack(){
    jQuery('#reminderGrid').data("kendoGrid").dataSource.page(1);
    return true;
}