var enableQuickSearch;
function taskQuickSearch(keyCode, term){
	if(keyCode==13){
		if(term.length > 0){
			document.getElementsByName("page").value =1;
			document.getElementsByName("skip").value =0;
			enableQuickSearch = true;
			jQuery('#quickSearchFilterTitleValue', '#filtersFormWrapper').val(term);
		}else{
			enableQuickSearch = false;
		}
		jQuery('#grid').data("kendoGrid").dataSource.page(1);
	}
}
function searchRelatedTasks(){
	var grid = jQuery('#grid');
	if (undefined == grid.data('kendoGrid')){
		grid.kendoGrid(gridOptions);
		var gridGrid = grid.data('kendoGrid');
		gridGrid.thead.find('th:first').append('<input type="checkbox" id="selectAllCheckboxes" class="select-all-checkboxes" onchange="checkUncheckAllCheckboxes(this);" title="'+_lang.selectAllRecords+'" />');
		return false;
	}
	grid.data('kendoGrid').dataSource.read();
	return false;
}
function getFormFilters(){
	var filtersForm = jQuery('#searchFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('searchFilters', '.', true);
	var filters = '';
	if(!enableQuickSearch){
		filters = searchFilters.filter;
	}else if(jQuery('#quickSearchFilterTitleValue', '#filtersFormWrapper').val()){
		filters = searchFilters.quickSearch;
	}
	enableAll(filtersForm);

	return filters;
}
function enableDisableUnarchivedButton(statusChkBx){
	if(statusChkBx.checked){
		jQuery('#unarchivedButtonId').removeAttr('disabled');
	}else if(!statusChkBx.checked && (jQuery("tbody" + " INPUT[type='checkbox']:checked").length == 0)){
		jQuery('#unarchivedButtonId').attr('disabled', 'disabled');
	}
}
function unarchivedSelectedTasks(){
	if(confirm(_lang.confirmationUnarchiveTasks)){
		jQuery.ajax({
			url:getBaseURL()+'tasks/archive_unarchive_tasks',
			type:'POST',
			dataType:'JSON',
			data:{
				gridData: form2js('gridFormContent', '.', true)
			},
			success:function(response){
				var ty = 'error';
				var m = '';
				switch(response.status){
					case 202:	// saved successfuly
						ty = 'information';
						m = _lang.feedback_messages.updatesSavedSuccessfully;
						break;
					case 101:	// could not save records
						m = _lang.feedback_messages.updatesFailed;
						break;
					default:
						break;
				}
				pinesMessage({ty: ty, m: m});
				jQuery('#grid').data("kendoGrid").dataSource.read();
				jQuery('#unarchivedButtonId').attr('disabled', 'disabled');
			},
			error: defaultAjaxJSONErrorsHandler
		});
	}
}
function deleteSelectedRow(id){
	if(confirm(_lang.confirmationDeleteSelectedRecord)){
		jQuery.ajax({
			url:getBaseURL()+'tasks/delete',
			type:'POST',
			dataType:'JSON',
			data:{taskId: id},
			success:function(response){
				var ty = 'error';
				var m = '';
				switch(response.status){
					case 202:	// removed successfuly
						ty = 'information';
						m = _lang.selectedRecordDeleted;
						break;
					case 101:	// could not remove record
						m = _lang.recordNotDeleted;
						break;
					case 303:	// could not remove record, task related to many object & component
						m = _lang.feedback_messages.deleteTaskFailed;
						break;
					default:
						break;
				}
				pinesMessage({ty: ty, m: m});
				jQuery('#grid').data("kendoGrid").dataSource.read();
			},
			error: defaultAjaxJSONErrorsHandler
		});
	}
}
function checkUncheckAllCheckboxes(statusChkBx){
	if(statusChkBx.checked && jQuery("tbody" + " INPUT[type='checkbox']").length >= 1){
		jQuery('#unarchivedButtonId').removeAttr('disabled');
	}else{
		jQuery('#unarchivedButtonId').attr('disabled', 'disabled');
	}
	jQuery("tbody" + " INPUT[type='checkbox']").attr( 'checked' , statusChkBx.checked);
}
function addNewCaseTask(){
	var caseId = jQuery('#caseIdInPage', '#gridFormContent').val();
	taskAddForm(caseId);
}
var gridDataSrc = new kendo.data.DataSource({
	transport: {
		read: {
			url: getBaseURL() + "cases/tasks",
			dataType: "JSON",
			type: "POST",
			complete: function(){
				if (jQuery('#filtersFormWrapper').is(':visible')) jQuery('#filtersFormWrapper').slideUp();
				jQuery('#selectAllCheckboxes').attr( 'checked' , false);
				jQuery('#unarchivedButtonId').attr('disabled', 'disabled');
                                if (_lang.languageSettings['langDirection'] === 'rtl')
                                    gridScrollRTL();
			}
		},
		parameterMap: function(options, operation){
			if ("read" !== operation && options.models) {
				return {
					models: kendo.stringify(options.models)
				};
			} else {
				options.filter = getFormFilters();
				options.returnData = 1;
			}
			options.caseIdFilter = jQuery('#caseIdFilter', '#searchFilters').val();
			return options;
		}
	},
	schema: {
		type: "json",
		data: "data",
		total: "totalRows",
		model: {
                    id: "id",
                    fields: {
						id: {editable: false, type: "integer"},
						actions: {type: "string"},
						taskId: { type: "string" },
						title: {type: "string"},
                        caseId: {type: "string"},
                        legal_case_id: {type: "integer"},
                        taskType: {type: "string"},
                        private: {type: "string"},
                        location: {type: "string"},
                        priority: {type: "string"},
                        taskStatus: {type: "string"},
                        description: {type: "string"},
                        assigned_to: {type: "string"},
                        estimated_effort: {type: "string"},
                        due_date: {type: "date"},
                        createdBy: {type: "string"},
                        createdOn: {type: "date"},
                        archivedTasks: {type: "string"},
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
                            row['createdBy'] = escapeHtml(row['createdBy']);
                            row['assigned_to'] = escapeHtml(row['assigned_to']);
							row['description'] = escapeHtml(row['description'].replace(/(<([^>]+)>)/gi, ""));
							rows.data.push(row);
                        }
                    }
                    return rows;
                }
	},error: function(e) {defaultAjaxJSONErrorsHandler(e.xhr)},
	pageSize: 10,
	serverPaging: true,
	serverFiltering: true,
	serverSorting: true
});
var gridOptions = {
	autobind: true,
	dataSource: gridDataSrc,
	columnMenu: {messages: _lang.kendo_grid_sortable_messages},
	columns:[
		{field: 'id', title: ' ', template: '<input type="checkbox" name="taskIds[]" id="taskId_#= id #" value="#= id #" class="first-grid-checkbox" onchange="enableDisableUnarchivedButton(this);" />', sortable:false, width: '50px'},
		{field: "actions", template: '<div class="wraper-actions"><div class="list-of-actions">' +
				'<a href="javascript:;" class="" onclick="deleteSelectedRow(\'#= id #\')"><i class="fa fa-fw fa-trash light_red-color"></i>' +
				'</a>' +
				'</div>' +
				'</div>', sortable:false, title: _lang.action, width: '100px'},
		{ field: "taskId", template: '<a href="tasks/view/#=id#" rel="tooltip" title="' + _lang.edit_task + '">#= taskId #</a><i class="iconLegal iconPrivacy#=private#"></i>', title: _lang.taskId, width: '118px' },
		{field: "title", title: _lang.title, width: '180px'},
		{field: "taskType", title: _lang.task_type, width: '135px'},
		{field: "taskStatus", title: _lang.task_status, width: '180px'},
		{field: "description", title: _lang.description_Case, width: '140px'},
		{field: "priority", title: _lang.priority, width: '110px'},
		{field: "location", title: _lang.location, width: '130px'},
		{field: "assigned_to", title: _lang.assignedTo, width: '160px',template: '#= (assigned_to!=null)?assigned_to:"" #'},
		{field: "estimated_effort", title: _lang.effort, width: '100px'},
		{field: "due_date", format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '130px'},
		{field: "createdBy", title: _lang.createdBy, width: '140px',template: '#= (createdBy!=null)?createdBy:"" #'},
		{field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '142px'},
		{field: "archivedTasks", title: _lang.archived, width: '124px'},
	],
	editable: "",
	filterable: false,
	pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes:[5, 10, 20, 50, 100], refresh: true},
	reorderable: true,
	resizable: true,
        height: 330,
	scrollable: true,
	sortable:{ mode: "multiple"},
        selectable: "single",
	toolbar: [{
		name: "task-grid-toolbar",
		template:  '<div class="col-md-3 no-padding quick-search margin-15-23">'
						+ '<div class="input-group col-md-12">'
							+ '<input type="text" class="form-control search quick-search-filter" placeholder=" '
							+ _lang.searchTask + '" name="taskLookUp" id="taskLookUp" onkeyup="taskQuickSearch(event.keyCode, this.value);" title="'
							+ _lang.searchTask + '" />'
						+ '</div>'
					+ '</div>'
					+ '<div class="col-md-1 float-right margin-15-23">'
						+ '<div class="btn-group float-right">' +
			'				<div class="dropdown">'
								+ '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">'
									+ _lang.actions + ' <span class="caret"></span>'
									+ '<span class="sr-only">Toggle Dropdown</span>'
								+ '</button>'
								+ '<div class="dropdown-menu action-add-btn">'
									+'<a class="dropdown-item disable-anchor" onclick="addNewCaseTask()"  class="btn btn-link" >'
									+ _lang.addNewTask + '</a>'
									+'<a class="dropdown-item"><input id="unarchivedButtonId" class="dropdown-item btn" onclick="unarchivedSelectedTasks()" disabled value="'
									+ _lang.unarchive + '" /></a>'
								+ '</div>'
							+ '</div>'
						+ '</div>'
					+ '</div>'
	}]
};
jQuery(document).ready(function(){
	searchRelatedTasks();
	jQuery('#searchFilters').bind('submit', function(e){
		e.preventDefault();
		jQuery('#taskLookUp').val('');
		enableQuickSearch = false;
		jQuery('#grid').data('kendoGrid').dataSource.read();
	});
	actionAddBtn = jQuery('.action-add-btn');
        if('undefined' !== typeof(disableMatter) && disableMatter){
            disableFields(actionAddBtn);
			disableAnchors(actionAddBtn);
        }
});
function taskCallBack() {
    jQuery('#grid').data("kendoGrid").dataSource.read();
    return true;
}