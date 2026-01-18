var enableQuickSearch, gridDataSrc, gridOptions;

function taskGridEvents() {
    gridInitialization();
    searchRelatedTasks();
    jQuery('#searchFilters').bind('submit', function (e) {
        e.preventDefault();
        jQuery('#taskLookUp').val('');
        enableQuickSearch = false;
        jQuery('#grid').data('kendoGrid').dataSource.read();
    });
}

function taskQuickSearch(keyCode, term) {
    if (keyCode == 13) {
        if (term.length > 0) {
            document.getElementsByName("page").value = 1;
            document.getElementsByName("skip").value = 0;
            enableQuickSearch = true;
			jQuery('#quickSearchFilterTitleValue', '#filtersFormWrapper').val(term);
        } else {
            enableQuickSearch = false;
        }
        jQuery('#grid').data("kendoGrid").dataSource.page(1);
    }
}

function searchRelatedTasks() {
    var grid = jQuery('#grid');
    if (undefined == grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        var gridGrid = grid.data('kendoGrid');
        return false;
    }
    grid.data('kendoGrid').dataSource.read();
    return false;
}

function getFormFilters() {
    var filtersForm = jQuery('#searchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('searchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
	} else if (jQuery('#quickSearchFilterTitleValue', '#filtersFormWrapper').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);

    return filters;
}

function deleteTaskSelectedRow(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'tasks/delete',
            type: 'POST',
            dataType: 'JSON',
            data: {taskId: id},
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
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
function contractTaskAddForm() {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var contractId = jQuery('#contractIdInPage', '#gridFormContent').val();
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'tasks/add/0/0/0/' + contractId,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#task-dialog').length <= 0) {
                    jQuery('<div id="task-dialog"></div>').appendTo("body");
                    var taskDialog = jQuery('#task-dialog');
                    taskDialog.html(response.html);
                    initTinyTemp('description', "#task-dialog", "task");
                    jQuery('.modal', taskDialog).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
                    var taskId = jQuery("#id", taskDialog).val();
                    fixDateTimeFieldDesign(taskDialog);
                    loadCustomFieldsEvents('custom-field-', taskDialog);
                    jQuery("#save-task-btn", taskDialog).click(function () {
                        taskFormSubmit(taskDialog, taskId, false);
                    });
                    jQuery(taskDialog).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            taskFormSubmit(taskDialog, taskId, false);
                        }
                    });
                    jQuery('#lookup-contract-id', taskDialog).val(contractId);
                    taskFormEvents(taskDialog, response);
                    jQuery('#type', taskDialog).change(function () {
                        assignmentPerType(this.value, 'task', taskDialog);
                    });
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.privateTaskMeetingMessage});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function gridInitialization() {
    gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('contract') + "contracts/related_tasks",
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible')) jQuery('#filtersFormWrapper').slideUp();
                    jQuery('#unarchivedButtonId').attr('disabled', 'disabled');
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    options.filter = getFormFilters();
                    options.returnData = 1;
                }
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
                    actions: {type: "string"}
                }
            },
            parse: function (response) {
                var rows = [];
                if (response.data) {
                    var data = response.data;
                    rows = response;
                    rows.data = [];
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        row['createdBy'] = escapeHtml(row['createdBy']);
                        row['assigned_to'] = escapeHtml(row['assigned_to']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        pageSize: 10,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {
                field: "taskId",
                template: '<a href="tasks/view/#=id#" rel="tooltip" title="' + _lang.edit_task + '">#= taskId #</a><i class="iconLegal iconPrivacy#=private#"></i>',
                title: _lang.taskId,
                width: '100px'
            },
            {field: "title", title: _lang.title, width: '180px'},
            {field: "taskType", title: _lang.task_type, width: '129px'},
            {field: "taskStatus", title: _lang.task_status, width: '146px'},
            {field: 'description', title: _lang.description, template: '<a class="tasks-title-desc" title="#= displayContent(taskFullDescription) #" href="tasks/view/#=id#"><bdi>#= description #</bdi></a>', width: '300px'},
            {field: "priority", title: _lang.priority, width: '100px'},
            {field: "location", title: _lang.location, width: '100px'},
            {
                field: "assigned_to",
                title: _lang.assignedTo,
                width: '140px',
                template: '#= (assigned_to!=null)?assigned_to:"" #'
            },
            {field: "estimated_effort", title: _lang.effort, width: '73px'},
            {field: "due_date", format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '150px'},
            {
                field: "createdBy",
                title: _lang.createdBy,
                width: '140px',
                template: '#= (createdBy!=null)?createdBy:"" #'
            },
            {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '132px'},
            {field: "archivedTasks", title: _lang.archived, width: '104px'},
            {
                field: "actions",
                template: '<a href="javascript:;" onclick="deleteTaskSelectedRow(\'#= id #\')"  title="' + _lang.deleteRow + '"><i class="fa-solid fa-xmark"></i></a>',
                sortable: false,
                title: _lang.actions,
                width: '65px'
            },
        ],
        editable: "",
        filterable: false,
        pageable: {
            input: true,
            messages: _lang.kendo_grid_pageable_messages,
            numeric: false,
            pageSizes: [5, 10, 20, 50, 100],
            refresh: true
        },
        reorderable: true,
        resizable: true,
        height: 330,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [{
            name: "task-grid-toolbar",
            template: '<div class="col-md-3">'
                + '<div class="input-group col-md-12 margin-top">'
                + '<input type="text" class="form-control search" placeholder=" '
                + _lang.searchTask + '" name="taskLookUp" id="taskLookUp" onkeyup="taskQuickSearch(event.keyCode, this.value);" title="'
                + _lang.searchTask + '" />'
                + '</div>'
                + '</div>'
                + '<div class="col-md-1 float-right">'
                + '<div class="btn-group float-right">'
                + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                + _lang.actions + ' <span class="caret"></span>'
                + '<span class="sr-only">Toggle Dropdown</span>'
                + '</button>'
                + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                + '<a role="button" class="dropdown-item" onclick="contractTaskAddForm()"  >'
                + _lang.addNewTask + '</a>'
                + '</div>'
                + '</div>'
                + '</div>'
        }]
    };
}

function taskCallBack() {
    if (undefined == jQuery('#grid').data('kendoGrid')) {
        return true;
    }
    jQuery('#grid').data("kendoGrid").dataSource.read();
    return true;
}