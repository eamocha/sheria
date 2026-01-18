jQuery(document).ready(function () {
	jQuery('.multi-select', '#makerCheckerUsersReportFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
	searchMakerCheckerUsersReport();
	jQuery('#makerCheckerUsersReportFilters').bind('submit', function (e) {
		e.preventDefault();
		searchMakerCheckerUsersReport();
	});
});
function advancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		makeFieldsDatePicker({fields: ['modifiedOnValue', 'modifiedOnEndValue', 'authorizedOnValue', 'authorizedOnEndValue']});
		userLookup('user_idValue');
		userLookup('modifiedByValue');
		userLookup('makerUserProfileValue');
		userLookup('checkerUserProfileValue');
		jQuery('#filtersFormWrapper').slideDown();
	} else {
		scrollToId('#filtersFormWrapper');
	}
}
function functionReturnOnlyTrue() {
	return true;
}
function hideAdvancedSearch() {
	jQuery('#filtersFormWrapper').slideUp();
}
function searchMakerCheckerUsersReport() {
        document.getElementsByName("page").value =1;
        document.getElementsByName("skip").value =0;
	if (undefined === jQuery('#makerCheckerUsersReportGrid').data('kendoGrid')) {
		jQuery('#makerCheckerUsersReportGrid').kendoGrid(makerCheckerUsersReportGridOptions);
		return false;
	}
	jQuery('#makerCheckerUsersReportGrid').data('kendoGrid').dataSource.page(1);
	return false;
}
function getMakerCheckerUsersReportFilters() {
	var filtersForm = jQuery('#makerCheckerUsersReportFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('makerCheckerUsersReportFilters', '.', true);
	var filters = '';
	filters = searchFilters.filter;
	enableAll(filtersForm);
	return filters;
}
var makerCheckerUsersReportDataSrc = new kendo.data.DataSource({
	transport: {
		read: {
			url: getBaseURL() + "users/maker_checker_report",
			dataType: "JSON",
			type: "POST",
			complete: function () {
				if (jQuery('#filtersFormWrapper').is(':visible'))
					jQuery('#filtersFormWrapper').slideUp();
				if (_lang.languageSettings['langDirection'] === 'rtl')
					gridScrollRTL();
				jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
			}
		},
		parameterMap: function (options, operation) {
			if ("read" === operation) {
				options.filter = getMakerCheckerUsersReportFilters();
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
                    id: {type: "integer"},
                    changeType: {type: "string"},
                    columnName: {type: "string"},
                    columnValue: {type: "string"},
                    columnStatus: {type: "string"},
                    columnRequestedValue: {type: "string"},
                    createdOn: {type: "dateTime"},
                    authorizedOn: {type: "dateTime"},
                    affectedUserId: {type: "integer"},
                    affectedUserProfile: {type: "string"},
                    makerUserProfile: {type: "string"},
                    checkerUserProfile: {type: "string"}
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
                        row['columnName'] = escapeHtml(row['columnName']);
                        row['affectedUserProfile'] = escapeHtml(row['affectedUserProfile']);
                        row['makerUserProfile'] = escapeHtml(row['makerUserProfile']);
                        row['checkerUserProfile'] = escapeHtml(row['checkerUserProfile']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
	}, error: function (e) {
		defaultAjaxJSONErrorsHandler(e.xhr);
	},
	pageSize: 50, serverPaging: true, serverFiltering: true, serverSorting: true
});
var makerCheckerUsersReportGridOptions = {
	autobind: true,
	dataSource: makerCheckerUsersReportDataSrc,
	columns: [
		{field: "affectedUserProfile", title: _lang.affectedUser, width: '150px',template: '#= (affectedUserProfile!=null && affectedStatus=="Inactive")? affectedUserProfile+" ("+_lang.custom[affectedStatus]+")":((affectedUserProfile!=null)?affectedUserProfile:(affectedUserId==null?"<span class=redColor>"+_lang.deletedUser+"</span>":"")) #'},
		{field: "changeType", title: _lang.action, template: "#= changeType == 'add' ? getFieldName('insert') : getFieldName('update') #", width: '100px'},
		{field: "columnStatus", title: _lang.approvalStatus, width: '145px'},
		{field: "columnName", template: "#= getFieldName(columnName)#", title: _lang.fieldName, width: '143px'},
		{field: "columnValue", title: _lang.oldValue, width: '150px'},
		{field: "columnRequestedValue", title: _lang.requestedValue, width: '150px'},
		{field: "makerUserProfile", title: _lang.maker, width: '120px',template: '#= (makerUserProfile!=null && makerStatus=="Inactive")? makerUserProfile+" ("+_lang.custom[makerStatus]+")":((makerUserProfile!=null)?makerUserProfile:"") #'},
		{field: "checkerUserProfile", title: _lang.checker, width: '120px',template: '#= (checkerUserProfile!=null && checkerStatus=="Inactive")? checkerUserProfile+" ("+_lang.custom[checkerStatus]+")":((checkerUserProfile!=null)?checkerUserProfile:"") #'},
		{field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '125px'},
		{field: "authorizedOn", format: "{0:yyyy-MM-dd}", title: _lang.authorizedOn, width: '125px'}
	],
	editable: false,
	filterable: false,
	height: 480,
	pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
	reorderable: true,
	resizable: true,
	scrollable: true,
	sortable: {mode: "multiple"},
	selectable: "single",
	toolbar: [{
			name: "toolbar-menu",
			template: '<div class="col-md-6 no-padding">'
					+ '<div class="clearfix">'
					+ '<h4 class="pull-left col-md-6">' + _lang.makerCheckerUsersReport + '</h4>'
					+ '<div class="no-padding advanced-search pull-left">'
					+ '<a href="javascript:;" onclick="advancedSearchFilters()">' + _lang.advancedSearch + '</a>'
					+ '</div>'
					+ '</div>'
					+ '</div>'
					+ '<div class="col-md-1 pull-right">'
					+ '<div class="btn-group pull-right">'
					+ '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
					+ _lang.actions + ' <span class="caret"></span>'
					+ '<span class="sr-only">Toggle Dropdown</span>'
					+ '</button>'
					+ '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
					+ '<a class="dropdown-item" onclick="exportMakerCheckerUsersReportToExcel()" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a>'
					+ '</div>'
					+ '</div>'
					+ '</div>'
		}],
	columnMenu: {messages: _lang.kendo_grid_sortable_messages}
};
function exportMakerCheckerUsersReportToExcel() {
	var newFormFilter = jQuery('#exportResultsForm');
	var filters = getMakerCheckerUsersReportFilters();
	jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
	jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('makerCheckerUsersReportFilters')));

	newFormFilter.attr('action', getBaseURL() + 'export/maker_checker_users_report').submit();
}
function getFieldName(fieldName) {
	return _lang.userFields[fieldName];
}
