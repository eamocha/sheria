jQuery(document).ready(function() {
	searchUserAuditReport();
	jQuery('#userAuditReportFilters').bind('submit', function(e) {
		e.preventDefault();
		searchUserAuditReport();
	});
});
function advancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		makeFieldsDatePicker({fields: ['modifiedOnValue', 'modifiedOnEndValue']});
		userLookup('user_idValue');
		userLookup('modifiedByValue');
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
function searchUserAuditReport() {
    document.getElementsByName("page").value =1;
        document.getElementsByName("skip").value =0;
	if (undefined === jQuery('#userAuditReportGrid').data('kendoGrid')) {
		jQuery('#userAuditReportGrid').kendoGrid(userAuditReportGridOptions);
		return false;
	}
	jQuery('#userAuditReportGrid').data('kendoGrid').dataSource.page(1);
	return false;
}
function getUserAuditReportFilters() {
	var filtersForm = jQuery('#userAuditReportFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('userAuditReportFilters', '.', true);
	var filters = '';
	filters = searchFilters.filter;
	enableAll(filtersForm);
	return filters;
}
var userAuditReportDataSrc = new kendo.data.DataSource({
	transport: {
		read: {
			url: getBaseURL() + "users/audit_reports",
			dataType: "JSON",
			type: "POST",
			complete: function() {
				if (jQuery('#filtersFormWrapper').is(':visible'))
					jQuery('#filtersFormWrapper').slideUp();
                                if (_lang.languageSettings['langDirection'] === 'rtl')
                                        gridScrollRTL();
			}
		},
		parameterMap: function(options, operation) {
			if ("read" === operation) {
				options.filter = getUserAuditReportFilters();
				options.returnData = 1;
			}
			return options;
		}
	},
	schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                        id: {type: "integer"},
                        userFullName: {type: "integer"},
                        action: {type: "string"},
                        fieldName: {type: "string"},
                        beforeData: {type: "string"},
                        afterData: {type: "string"},
                        modifiedOn: {type: "dateTime"},
                        modifiedFullName: {type: "string"}
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
                        row['userFullName'] = escapeHtml(row['userFullName']);
                        row['fieldName'] = escapeHtml(row['fieldName']);
                        row['modifiedFullName'] = escapeHtml(row['modifiedFullName']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
	}, error: function(e) {
		defaultAjaxJSONErrorsHandler(e.xhr);
	},
	pageSize: 50, serverPaging: true, serverFiltering: true, serverSorting: true
});
var userAuditReportGridOptions = {
	autobind: true,
	dataSource: userAuditReportDataSrc,
	columns: [
		{field: "userFullName", title: _lang.user, width: '100px',template: '#= (userFullName!=null && userStatus=="Inactive")? userFullName+" ("+_lang.custom[userStatus]+")":((userFullName!=null)?userFullName:"") #'},
		{field: "action", title: _lang.action, template: "#= getFieldName(action)#", width: '96px'},
		{field: "fieldName", template: "#= getFieldName(fieldName)#", title: _lang.fieldName, width: '143px'},
		{field: "beforeData", template: "#= fieldName=='password'?'*':(fieldName=='banned'?(beforeData=='0'?_lang.no:(beforeData==null?'':_lang.yes)):(fieldName=='flagChangePassword'?(beforeData=='0'?_lang.no:(beforeData==null || beforeData==''?'':_lang.yes)):(fieldName=='isAd' ? ((beforeData == '1') ? _lang.activeDirectory : (beforeData == '0' ? _lang.localDirectory : '')) : (beforeData==null?'':beforeData))))#", title: _lang.oldValue, width: '150px'},
		{field: "afterData", template: "#= fieldName=='password'?'*':(fieldName=='banned'?(afterData=='0'?_lang.no:(afterData==null?'':_lang.yes)):(fieldName=='flagChangePassword'?(afterData=='0'?_lang.no:(afterData==null || afterData==''?'':_lang.yes)):(fieldName=='isAd' ? ((afterData == '1') ? _lang.activeDirectory : (afterData == '0' ? _lang.localDirectory : '')) : (afterData==null?'':afterData))))#", title: _lang.newValue, width: '150px'},
		{field: "modifiedFullName", title: _lang.modifiedBy, width: '120px',template: '#= (modifiedFullName!=null && modifiedStatus=="Inactive")? modifiedFullName+" ("+_lang.custom[modifiedStatus]+")":((modifiedFullName!=null)?modifiedFullName:"") #'},
		{field: "modifiedOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '115px'}
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
			template: '<div class="row w-100"><div class="col-md-6">'
					+ '<h4 class="pull-left col-md-6">' + _lang.usersAuditReport + '</h4>'
					+ '<div class="no-padding advanced-search pull-left">'
					+ '<a href="javascript:;" onclick="advancedSearchFilters()">' + _lang.advancedSearch + '</a>'
					+ '</div>'
					+ '</div>'
					+ '<div class="col-md-1 pull-right">'
					+ '<div class="btn-group pull-right">'
					+ '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
					+ _lang.actions + ' <span class="caret"></span>'
					+ '<span class="sr-only">Toggle Dropdown</span>'
					+ '</button>'
					+ '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
					+ '<a onclick="exportUserAuditReportToExcel()" title="' + _lang.exportToExcel + '" class="dropdown-item" href="javascript:;" >' + _lang.exportToExcel + '</a>'
					+ '</div>'
					+ '</div>'
					+ '</div></div>'
		}],
	columnMenu: {messages: _lang.kendo_grid_sortable_messages}
};
function exportUserAuditReportToExcel() {
	var newFormFilter = jQuery('#exportResultsForm');
	var filters = getUserAuditReportFilters();
	jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('userAuditReportFilters')));
	newFormFilter.attr('action', getBaseURL() + 'export/users_audit').submit();
}
function getFieldName(fieldName) {
	return _lang.userFields[fieldName];
}
