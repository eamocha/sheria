jQuery(document).ready(function () {
	jQuery("#userGroupsSearchFilters").submit();
});
function advancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		loadEventsForFilters();
		jQuery('#filtersFormWrapper').slideDown();
	} else {
		scrollToId('#filtersFormWrapper');
	}
}
function loadEventsForFilters() {
	makeFieldsDatePicker({fields: ['creationDateValue', 'creationDateEndValue', 'last_loginValue', 'last_loginEndValue', 'modifiedOnValue', 'modifiedOnEndValue']});
	userLookup('createdByValue');
	userLookup('modifiedByValue');
	userLookup('AuthorizedByFullNameValue');
}
function searchUserGroups() {
    document.getElementsByName("page").value =1;
        document.getElementsByName("skip").value =0;
	if (undefined == jQuery('#searchResults').data('kendoGrid')) {
		var kGrid = jQuery('#searchResults').kendoGrid(contractSearchGridOptions);
		var kGridData = kGrid.data('kendoGrid');
		if (!makerCheckerFeatureStatus) {
			kGridData.hideColumn("flagNeedApproval");
			kGridData.hideColumn("authorizedBy");
		}
		return false;
	}
	jQuery('#searchResults').data('kendoGrid').dataSource.page(1);
	return false;
}
function hideAdvancedSearch() {
	jQuery('#filtersFormWrapper').slideUp();
}
function getFormFilters() {
	var filters = '';
	var filtersForm = jQuery('#userGroupsSearchFilters');
	if(jQuery('#idValue').val() != ''){
		jQuery('#idValueHidden').val(parseInt(jQuery('#idValue').val())+1);
	}else{
		jQuery('#idValueHidden').val('');
	}
	disableEmpty(filtersForm);
	var searchFilters = form2js('userGroupsSearchFilters', '.', true);
	filters = searchFilters.filter;
	enableAll(filtersForm);
	return filters;
}
var userGroupsSearchDataSrc = new kendo.data.DataSource({
	transport: {
		read: {
			url: getBaseURL() + "user_groups/index",
			dataType: "JSON",
			type: "POST",
			complete: function () {
				if (jQuery('#filtersFormWrapper').is(':visible'))
					jQuery('#filtersFormWrapper').slideUp();
				if (_lang.languageSettings['langDirection'] === 'rtl')
					gridScrollRTL();
				animateDropdownMenuInGrids('searchResults');
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
                        id: {type: "integer"},
                        name: {type: "string"},
                        description: {type: "string"},
                        createdBy: {type: "string"},
                        modifiedBy: {type: "string"},
                        authorizedBy: {type: "string"},
                        createdOn: {type: "date"},
                        modifiedOn: {type: "date"}
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
                        row['description'] = escapeHtml(row['description']);
                        row['createdBy'] = escapeHtml(row['createdBy']);
                        row['modifiedBy'] = escapeHtml(row['modifiedBy']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
	}, error: function (e) {
		defaultAjaxJSONErrorsHandler(e.xhr);
	},
	pageSize: 20, serverPaging: true, serverFiltering: true, serverSorting: true
});
var contractSearchGridOptions = {
	autobind: true,
	dataSource: userGroupsSearchDataSrc,
	columns: [
		{field: "id", filterable: false, title: ' ', template: '<div class="dropdown">' + gridActionIconHTML +
					'<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
					'<li style="#= ((system_group == 1) || (flagNeedApproval == 1 && makerCheckerFeatureStatus) || (isReport)) ? \'display: none;\' : \'\'  #"><a href="' + getBaseURL() + 'user_groups/edit/#= id #">' + _lang.viewEdit + '</a></li>' +
                                        '<li style="#= ((flagNeedApproval == 1 && makerCheckerFeatureStatus) || (isReport)) ? \'display: none;\' : \'\'  #"><a href="' + getBaseURL() + 'user_groups/clone_group/#= id #">' + _lang.clone + '</a></li>' +
					'<li style="#= ((flagNeedApproval == 1 && makerCheckerFeatureStatus) || (isReport)) ? \'display: none;\' : \'\'  #"><a href="' + getBaseURL() + 'user_groups/list_users/#= id #">' + _lang.showUsers + '</a></li>' +
					'<li style="#= ((system_group == 1) || (flagNeedApproval == 1 && makerCheckerFeatureStatus) || (isReport)) ? \'display: none;\' : \'\'  #"><a href="javascript:;"  onclick="deleteUserGroup(\'#= id #\');">' + _lang.deleteRow + '</a></li>' +
					'<li style="#= (isReport) ? \'display: none;\' : (flagNeedApproval == 1 && isUserChecker) ? \'\' : \'display: none;\' #"><a href="javascript:;" onclick="checkerApproveChangesOnGroup(\'#= id #\');">' + _lang.approveChanges + '</a></li>' +
					'<li><a href="' + getBaseURL() + 'user_groups/permissions_list_by_group/#= id #">' + _lang.permissionsList + '</a></li>' +
					'</ul></div>', width: '70px'},
		{field: "id", filterable: false, title: _lang.groupId, template: '<a href="' + getBaseURL() + 'user_groups/edit/#= id #" title="' + _lang.viewEdit + '">#= id-1 #</a>', width: '185px'},
		{field: "name", title: _lang.user_group, width: '192px'},
		{field: "description", title: _lang.groupDescription, template: '#= (description!=null&&description!="") ? ((description.length>40)? description.substring(0,40)+"..." : description) : ""#', width: '320px'},
		{field: "flagNeedApproval", template: "#= (flagNeedApproval == '1') ? _lang.yes : _lang.no #", title: _lang.pendingApprovals, width: '175px'},
		{field: "createdBy", title: _lang.createdBy, width: '192px',template: '#= (createdBy!=null && createdByStatus=="Inactive")? createdBy+" ("+_lang.custom[createdByStatus]+")":((createdBy!=null)?createdBy:"") #'},
		{field: "createdOn", title: _lang.createdOn, format: "{0:yyyy-MM-dd}", width: '192px'},
		{field: "modifiedBy", title: _lang.lastModifiedBy, width: '192px',template: '#= (modifiedBy!=null && modifiedByStatus=="Inactive")? modifiedBy+" ("+_lang.custom[modifiedByStatus]+")":((modifiedBy!=null)?modifiedBy:"") #'},
		{field: "modifiedOn", title: _lang.lastModifiedOn, format: "{0:yyyy-MM-dd}", width: '192px'},
		{field: "authorizedBy", title: _lang.authorizedBy, width: '192px'}
	],
	editable: false,
	filterable: false,
	height: 480,
	pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, refresh: true, pageSizes: [10, 20, 50, 100]},
	reorderable: true,
	resizable: true,
	scrollable: true,
	sortable: {mode: "multiple"},
	selectable: "single",
	columnMenu: {
		messages: _lang.kendo_grid_sortable_messages
	},
	toolbar: [{
			name: "users-grid-toolbar",
			template: '<div class="row nav-full-width">'
			        + '<div class="col-md-6 no-padding form-group row">'
					+ '<h4 class="col-md-5">' + _lang.userGroups + '</h4>'
					+ ' <div class="input-group col-md-5">'
                                        +'<a href="javascript:;" onclick="advancedSearchFilters()" class="btn btn-default btn-link">'
					+ _lang.advancedSearch + '</a>&nbsp;&nbsp;&nbsp;&nbsp;'
                                        + '</div>'
					+ '</div>'
					+ '<div class="col-md-1 pull-right">'
					+ '<div class="btn-group pull-right">'
					+ '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
					+ _lang.actions + ' <span class="caret"></span>'
					+ '<span class="sr-only">Toggle Dropdown</span>'
					+ '</button>'
					+ '<ul class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
					+ '<li><a onclick="exportUserGroupsToExcel(#=isReport#)" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a></li>'
					+ '</ul>'
					+ '</div>'
					+ '</div>'
					+ '</div>'
		}]
};

function checkerApproveChangesOnGroup(userGroupId) {
	jQuery.ajax({
		dataType: 'JSON',
		type: 'POST',
		url: getBaseURL() + 'user_groups/checker_approve_changes',
		data: {modeType: 'getForm', id: userGroupId},
		beforeSend: function () {
		},
		success: function (response) {
			if (!jQuery('#approveChangesDialog').length) {
				jQuery('<div id="approveChangesDialog" class="d-none"></div>').appendTo('body');
			}
			var approveChangesDialog = jQuery('#approveChangesDialog');
			approveChangesDialog.html(response.html).dialog({
				autoOpen: true,
				buttons: [
					{
						'class': 'btn btn-info',
						click: function () {
							var sendApproveRequest = true;
							if (!jQuery('input[name="changeIds[]"]:checked', approveChangesDialog).length) {
								if (!confirm(_lang.confirmationDiscardChangesEditMode)) {
									sendApproveRequest = false;
								}
							}
							if (sendApproveRequest) {
								var formData = jQuery("form#approveChangesForm", approveChangesDialog).serialize();
								jQuery.ajax({
									url: getBaseURL() + 'user_groups/checker_approve_changes',
									type: 'POST',
									dataType: 'JSON',
									data: formData,
									beforeSend: function () {
									},
									success: function (response) {
										if (!response.result) {
											var errorMsg = '';
											for (i in response.validationErrors) {
												errorMsg += '<li>' + response.validationErrors[i] + '</li>';
											}
											if (errorMsg != '') {
												pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
											}
										} else {
											approveChangesDialog.dialog("close");
											pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
											jQuery('#searchResults').data("kendoGrid").dataSource.read();
										}
									},
									error: defaultAjaxJSONErrorsHandler
								});
							}
						},
						text: _lang.approve
					},
					{
						'class': 'btn btn-info ' + (response.changeType === 'edit' ? 'd-none' : ''),
						click: function () {
							if (confirm(_lang.confirmationDiscardChanges.sprintf([_lang.user_group]))) {
								jQuery.ajax({
									url: getBaseURL() + 'user_groups/checker_approve_changes',
									type: 'POST',
									dataType: 'JSON',
									data: {modeType: 'discardUser', id: userGroupId, changeType: response.changeType},
									beforeSend: function () {
									},
									success: function () {
										approveChangesDialog.dialog("close");
										jQuery('#searchResults').data("kendoGrid").dataSource.read();
									},
									error: defaultAjaxJSONErrorsHandler
								});
							}
						},
						text: _lang.discardUserGroup
					},
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
						resizeNewDialogWindow(approveChangesDialog, '60%', '500');
					}));
					resizeNewDialogWindow(approveChangesDialog, '60%', '500');
				},
				draggable: true,
				modal: false,
				position: {my: 'center', at: 'center'},
				resizable: false,
				title: _lang.approveChanges
			}).removeClass('d-none');

		},
		error: defaultAjaxJSONErrorsHandler
	});
}

function deleteUserGroup(id) {
	if (confirm(_lang.confirmationDeleteSelectedRecord)) {
		jQuery.ajax({
			url: getBaseURL() + 'user_groups/delete/' + id,
			type: 'POST',
			dataType: 'JSON',
			data: {},
			success: function (response) {
				var ty = 'error';
				var m = response.msg;
				switch (response.status) {
					case 202:
						ty = 'information';
						jQuery('#searchResults').data("kendoGrid").dataSource.read();
						break;
					case 104:
						ty = 'information';
						break;
					default:
						break;
				}
				pinesMessage({ty: ty, m: m});
			},
			error: defaultAjaxJSONErrorsHandler
		});
	}
}
function exportUserGroupsToExcel(isReport) {
	var newFormFilter = jQuery('#exportResultsForm');
	var filters = getFormFilters();
	jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
	jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('userGroupsSearchFilters')));

	newFormFilter.attr('action', getBaseURL() + 'export/user_groups/'+isReport).submit();
}
