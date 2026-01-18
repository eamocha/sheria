jQuery(document).ready(function () {
	jQuery('.multi-select', '#makerCheckerUserGroupsPermissionsReportFilters').chosen({no_results_text: _lang.no_results_matched,placeholder_text: _lang.select,width: "100%"}).change();
	searchMakerCheckerUserGroupsPermissionsReport();
	jQuery('#makerCheckerUserGroupsPermissionsReportFilters').bind('submit', function (e) {
		e.preventDefault();
		searchMakerCheckerUserGroupsPermissionsReport();
	});
});
function advancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		makeFieldsDatePicker({fields: ['modifiedOnValue', 'modifiedOnEndValue', 'authorizedOnValue', 'authorizedOnEndValue']});
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
function searchMakerCheckerUserGroupsPermissionsReport() {
        document.getElementsByName("page").value =1;
        document.getElementsByName("skip").value =0;
	if (undefined === jQuery('#makerCheckerUserGroupsPermissionsReportGrid').data('kendoGrid')) {
		jQuery('#makerCheckerUserGroupsPermissionsReportGrid').kendoGrid(makerCheckerUserGroupsPermissionsReportGridOptions);
		return false;
	}
	jQuery('#makerCheckerUserGroupsPermissionsReportGrid').data('kendoGrid').dataSource.page(1);
	return false;
}
function getMakerCheckerUserGroupsPermissionsReportFilters() {
	var filtersForm = jQuery('#makerCheckerUserGroupsPermissionsReportFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('makerCheckerUserGroupsPermissionsReportFilters', '.', true);
	var filters = '';
	filters = searchFilters.filter;
	enableAll(filtersForm);
	return filters;
}
var makerCheckerUsersReportDataSrc = new kendo.data.DataSource({
	transport: {
		read: {
			url: getBaseURL() + "user_groups/maker_checker_permissions_report",
			dataType: "JSON",
			type: "POST",
			complete: function () {
				if (jQuery('#filtersFormWrapper').is(':visible'))
					jQuery('#filtersFormWrapper').slideUp();
				if (_lang.languageSettings['langDirection'] === 'rtl')
					gridScrollRTL();
				jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
				animateDropdownMenuInGrids('makerCheckerUserGroupsPermissionsReportGrid');
			}
		},
		parameterMap: function (options, operation) {
			if ("read" === operation) {
				options.filter = getMakerCheckerUserGroupsPermissionsReportFilters();
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
                    module: {type: "string"},
                    columnStatus: {type: "string"},
                    createdOn: {type: "dateTime"},
                    authorizedOn: {type: "dateTime"},
                    affectedUserGroupName: {type: "string"},
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
var makerCheckerUserGroupsPermissionsReportGridOptions = {
	autobind: true,
	dataSource: makerCheckerUsersReportDataSrc,
	columns: [
		{field: "id", filterable: false, title: ' ', template: '<div class="dropdown">' + gridActionIconHTML +
					'<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
					'<li><a href="javascript:;" onclick="viewPermissions(\'#= id #\');">' + _lang.viewChanges + '</a></li>' +
					'</ul></div>', width: '70px'},
		{field: "affectedUserGroupName", title: _lang.affectedUserGroup, width: '150px'},
		{field: "module", title: _lang.module, width: '80px'},
		{field: "columnStatus", title: _lang.approvalStatus, width: '130px'},
		{field: "makerUserProfile", title: _lang.maker, width: '120px',template: '#= (makerUserProfile!=null && makerStatus=="Inactive")? makerUserProfile+" ("+_lang.custom[makerStatus]+")":((makerUserProfile!=null)?makerUserProfile:"") #'},
		{field: "checkerUserProfile", title: _lang.checker, width: '120px',template: '#= (checkerUserProfile!=null && checkerStatus=="Inactive")? checkerUserProfile+" ("+_lang.custom[checkerStatus]+")":((checkerUserProfile!=null)?checkerUserProfile:"") #'},
		{field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '115px'},
		{field: "authorizedOn", format: "{0:yyyy-MM-dd}", title: _lang.authorizedOn, width: '115px'}
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
					+ '<h4 class="pull-left col-md-8">' + _lang.makerCheckerUserGroupsPermissionsReport + '</h4>'
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
					+ '<a class="dropdown-item" onclick="exportMakerCheckerUsersReportToExcel()" title="' + _lang.exportToExcel + '" class="dropdown-item" href="javascript:;" >' + _lang.exportToExcel + '</a>'
					+ '</div>'
					+ '</div>'
					+ '</div>'
		}],
	columnMenu: {messages: _lang.kendo_grid_sortable_messages}
};
function getFieldName(fieldName) {
	return _lang.userFields[fieldName];
}
function exportMakerCheckerUsersReportToExcel() {
	var newFormFilter = jQuery('#exportResultsForm');
	var filters = getMakerCheckerUserGroupsPermissionsReportFilters();
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('makerCheckerUserGroupsPermissionsReportFilters')));

	jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
	newFormFilter.attr('action', getBaseURL() + 'export/maker_checker_user_groups_permissions_report').submit();
}
function viewPermissions(id) {
	jQuery.ajax({
		dataType: 'JSON',
		type: 'POST',
		url: getBaseURL() + 'user_groups/maker_checker_permissions_report',
		data: {id: id, action: 'viewPermissions'},
		beforeSend: function () {
		},
		success: function (response) {
			if (!jQuery('#viewChangesDialog').length) {
				jQuery('<div id="viewChangesDialog" class="d-none"></div>').appendTo('body');
			}
			var viewChangesDialog = jQuery('#viewChangesDialog');
			viewChangesDialog.html(response.html).dialog({
				autoOpen: true,
				buttons: [
					{
						text: _lang.cancel,
						'class': 'btn btn-link',
						click: function () {
							jQuery(this).dialog("close");
						}
					}
				],
				open: function () {
					jQuery(window).bind('resize', (function () {
						resizeNewDialogWindow(viewChangesDialog, '60%', '500');
					}));
					resizeNewDialogWindow(viewChangesDialog, '60%', '500');
				},
				draggable: true,
				modal: false,
				position: {my: 'center', at: 'center'},
				resizable: false,
				title: _lang.viewChanges
			}).removeClass('d-none');
		},
		error: defaultAjaxJSONErrorsHandler
	});
}
