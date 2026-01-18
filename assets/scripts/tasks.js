var taskStatusValues = ['open', 'In Progress', 'done'];
var enableQuickSearch = false;
var enableTaskStatusesQuickSearch = true;
var licenseFlag = false, customFieldsNames;
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['dueDateValue', 'createdOnValue', 'modifiedOnValue', 'dueDateEndValue', 'createdOnEndValue', 'modifiedOnEndValue']});
    caseLookup(jQuery('#caseIdValueLookUp'));
    userLookup('assignedToValue');
    userLookup('reporterValue');
    userLookup('createdByValue');
    userLookup('modifiedByValue');
    taskLocationLookup('locationValue');
    lookUpContracts({
        'lookupField': jQuery('#contractLookup', $searchFiltersForm),
        'hiddenId': jQuery('#contract-value', $searchFiltersForm),
    }, $searchFiltersForm);
}
function taskQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        revertAllFilters();
        enableQuickSearch = true;
        enableTaskStatusesQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterTitleValue', '#filtersFormWrapper').val(term);
        $tasksGrid.data("kendoGrid").dataSource.page(1);
    }
}
function getFormFilters() {
    var filtersForm = jQuery('#searchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('searchFilters', '.', true);
    var filters = '';
    var myTasks;
    try {
        myTasks = parseInt(jQuery('#userId').attr('auth'));
    } catch (e) {
        myTasks = 0;
    }
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else {
        filters = searchFilters.quickSearch;
    }
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
function enableDisableUnarchivedButton(statusChkBx) {
    if (statusChkBx.checked) {
        jQuery('#unarchivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip').attr('title', '');
    } else if (!statusChkBx.checked && (jQuery("tbody" + " INPUT[type='checkbox']:checked").length == 0)) {
        disabledUnArchiveBtn();
    }
}
function unarchivedSelectedTasks() {
    if (confirm(_lang.confirmationUnarchiveTasks)) {
        jQuery.ajax({
            url: getBaseURL() + 'tasks/archive_unarchive_tasks',
            type: 'POST',
            dataType: 'JSON',
            data: {
                gridData: form2js('gridFormContent', '.', true)
            },
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
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
                jQuery('#tasksGrid').data("kendoGrid").dataSource.read();
                disabledUnArchiveBtn();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function exportTasksToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    jQuery("#task-contributors").prop('disabled',false).trigger('chosen:updated'); // temporarily enable the task-contributors dropdown so the value can be sent
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('searchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    var related_tasks = (tasks) ? 'my_tasks' : (reportedByMe) ? 'reported_tasks' : 'all';
    newFormFilter.attr('action', getBaseURL() + 'export/tasks/' + related_tasks).submit();
    if(contributedByMe) {
        jQuery("#task-contributors").prop('disabled',true).trigger('chosen:updated'); // re-disable task-contributors after finishing the export
    }
}
function deleteSelectedRow(id) {
    jQuery('#pendingReminders').parent().popover('hide');
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
                        m = _lang.taskDeletedSuccessfully;
                        loadUserLatestReminders('refresh');
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
                jQuery('#tasksGrid').data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function checkUncheckAllCheckboxes(statusChkBx) {
    if (statusChkBx.checked && jQuery("tbody" + " INPUT[type='checkbox']").length >= 1) {
        jQuery('#unarchivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip').attr('title', '');
    } else {
        disabledUnArchiveBtn();
    }
    jQuery("tbody" + " INPUT[type='checkbox']").attr('checked', statusChkBx.checked);
}
function taskStatusFilterClick(anchor, all) {
    all = all || false;
    anchor = jQuery(anchor);
    checkbox = jQuery('input[type=checkbox]', anchor);
    icon = jQuery('i', anchor);
    //	jQuery(anchor.parent().parent().parent()).toggleClass('open');
    checkbox.attr('checked', !checkbox.is(':checked'));
    if (checkbox.is(':checked')) {
        icon.addClass('checkbox-yes-icon').removeClass('checkbox-no-icon');
    } else {
        icon.removeClass('checkbox-yes-icon').addClass('checkbox-no-icon');
    }
    if (all) {
        jQuery('input[type=checkbox]', anchor.parent().parent()).attr('checked', checkbox.is(':checked'));
        if (checkbox.is(':checked')) {
            jQuery('i', anchor.parent().parent()).addClass('checkbox-yes-icon').removeClass('checkbox-no-icon');
        } else {
            jQuery('i', anchor.parent().parent()).removeClass('checkbox-yes-icon').addClass('checkbox-no-icon');
        }
    } else if (
            jQuery('input[type=checkbox]:checked', anchor.parent().parent()).length == (jQuery('input[type=checkbox]', anchor.parent().parent()).length - 1)
            && jQuery('input[type=checkbox]:first', anchor.parent().parent()).is(':checked')
            ) {
        var allBox = jQuery('input[type=checkbox]:first', anchor.parent().parent()).attr('checked', false);
        jQuery('i', allBox.parent()).removeClass('checkbox-yes-icon').addClass('checkbox-no-icon');
    } else if (
            jQuery('input[type=checkbox]:checked', anchor.parent().parent()).length == (jQuery('input[type=checkbox]', anchor.parent().parent()).length - 1)
            && !jQuery('input[type=checkbox]:first', anchor.parent().parent()).is(':checked')
            ) {
        var allBox = jQuery('input[type=checkbox]:first', anchor.parent().parent()).attr('checked', true);
        jQuery('i', allBox.parent()).addClass('checkbox-yes-icon').removeClass('checkbox-no-icon');
    }
}
function taskStatusQuickSearch() {
    enableTaskStatusesQuickSearch = true;
    $tasksGrid.data("kendoGrid").dataSource.read();
}



var gridOptions = {};
var $searchFiltersForm = null;
jQuery(document).ready(function () {
    $tasksGrid = jQuery('#tasksGrid');
    gridInitialization();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery("#task-contributors").prop('disabled',false).trigger('chosen:updated'); // temporarily enable the task-contributors dropdown so the value can be sent
        $tasksGrid.data('kendoGrid').dataSource.read();
        if(contributedByMe) {
            jQuery("#task-contributors").prop('disabled',true).trigger('chosen:updated'); // re-disable task-contributors after finishing the export
        }
    });
    jQuery('.multi-select', '#searchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    jQuery('#searchFilters').bind('submit', function (e) {
        jQuery("form#searchFilters").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        if (!jQuery('form#searchFilters').validationEngine("validate")) {
            return false;
        }
        e.preventDefault();
        jQuery('#taskLookUp').val('');
        enableQuickSearch = false;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#tasksGrid').data('kendoGrid').dataSource.page(1);
    });

    jQuery('.checkbox', '#taskStatusesList').click(function (e) {
        e.stopPropagation();
    });
});
function getCustomTranslation(val) {
    return _lang.custom[val];
}
function validateNumbers(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
    if (!decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
}
function changeLookUp(value) {
    if (value == 'lookUp') {
        caseLookup(jQuery('#caseIdValue'));
        jQuery('#caseIdValue').addClass('lookup');
        jQuery("#caseIdValue").val('');
        jQuery('#caseIdValue').attr('title', _lang.startTyping);
        jQuery('#caseIdValue').attr('placeholder', _lang.startTyping);
        jQuery("#lookup_type").val('legal_case_id');
    } else {
        if (jQuery("#caseIdValue").hasClass('ui-autocomplete-input')) {
            jQuery("#caseIdValue").autocomplete("destroy");
            jQuery("#caseIdValue").val('');
            jQuery('#caseIdValue').removeClass('ui-autocomplete-input lookup');
            jQuery('#caseIdValue').removeAttr('title');
            jQuery('#caseIdValue').removeAttr('placeholder');
            jQuery("#lookup_type").val('caseFullSubject');
        }
    }
}
function gridInitialization() {
    var tableColumns = [];
    var savePageSize = false;
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({field: "id", title: ' ', filterable: false, sortable: false, template: '<input type="checkbox" name="taskIds[]" id="taskId_#= id #" title="' + _lang.archiveCheckboxTitle + '" value="#= id #" onchange="enableDisableUnarchivedButton(this);" />' +
                    '<div class="dropdown more">' + gridActionIconHTML + '<div class="dropdown-menu list-grid-action-demo ' + (isLayoutRTL() ? 'dropdown-menu-right" ' : '" ')  + 'role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="javascript:;" onclick="taskEditForm(\'#= id #\')">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="' +
                    'logActivityDialog(false, {legalCaseLookupId: \'#= null == legal_case_id ? \'\' : legal_case_id #\', legalCaseLookup: \'#= null == caseId ? \'\' : caseId #\', ' +
                    'legalCaseSubject: \'#= null == caseSubject ? \'\' : addslashes(encodeURIComponent(caseSubject)) #\', legalCaseCategory: \'#= caseCategory #\', task_id: \'#= taskId #\', taskSubject: \'#= title #\', ' +
                    'taskId: \'#= id #\', modifiedByName: \'#= modifiedBy #\', createdOn: \'#= convert(createdOn).date #\', createdByName: \'#= createdBy #\', modifiedOn: \'#= convert(modifiedOn).date #\', taskStatus: \'#= taskStatus #\'}, \'\', \'taskLog\');">' + _lang.log_time + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="TimerForm(null, \'\\add\',{legal_case_id: \'#= null==legal_case_id? \'\' : legal_case_id #\' , subject: \'#= null==legal_case_id? \'\' : caseId +\'\\: \'+ addslashes(encodeURIComponent(caseSubject)) #\'} ,{taskId: \'#= id #\' , taskSubject: \'#= title #\'});" >' + _lang.start_timer + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="recordRelatedExpense(\'#= null==legal_case_id? \'0\' : legal_case_id #\',\'task\',\'#= id #\')" title="' + _lang.recordExpense + '">' + _lang.recordExpense + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="recordRelatedExpense(\'#= null==legal_case_id? \'0\' : legal_case_id #\',\'task\',\'#= id #\', true)" title="' + _lang.recordBulkExpenses + '">' + _lang.recordBulkExpenses + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="deleteSelectedRow(\'#= id #\')">' + _lang.deleteRow + '</a>' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'tasks/export_task_to_word/#= id #">' + _lang.exportToWord + '</a>' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'tasks/export_task_to_word_for_clients/#= id #">' + _lang.exportToWordForClients + '</a>' +
                    '</div></div>', width: '70px'});
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'taskId') {
                array_push = {field: item, template: '<a href="tasks/view/#=id#" rel="tooltip" title="' + _lang.edit_task + '">#= taskId #</a><i class="iconLegal iconPrivacy#=private#"></i>', title: _lang.id, width: '124px'};
            } else if (item === 'title') {
                array_push = {field: item, title: _lang.columnTitle, template: '<a title="#= title #" href="tasks/view/#=id#"><bdi>#= title #</bdi></a>', width: '300px'};
            } else if (item === 'taskType') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.type, width: '137px'};
            } else if (item === 'taskStatus') {
                array_push = {field: item, title: _lang.workflow_status, width: '138px'};
            } 
            else if (item === 'contract_name') {
                array_push = {field: item, template: '<a href="modules/contract/contracts/view/#=contract_id#" rel="tooltip" title="' + _lang.relatedContract + '">#= contract_name ? contract_name : "" #</a><i class="iconLegal iconPrivacy#=private#"></i>', title: _lang.relatedContract, width: '124px'};
            }
            else if (item === 'description') {
                array_push = {field: item, title: _lang.description, template: '<span><bdi>#= replaceHtmlCharacter(description) #</bdi></span>', width: '300px'};
            } else if (item === 'caseId') {
                array_push = {field: item, template: '<a href="' + getBaseURL() + '#= (caseCategory!="IP") ? "cases/edit/"+legal_case_id : "intellectual_properties/edit/"+legal_case_id #">#= legal_case_id ? caseId : "" #</a>', title: _lang.caseId, width: '143px'};
            } else if (item === 'caseSubject') {
                array_push = {field: item, template: '<a href="' + getBaseURL() + '#= (caseCategory!="IP") ? "cases/edit/"+legal_case_id : "intellectual_properties/edit/"+legal_case_id #">#= caseSubject ? caseSubject : "" #</a>', title: _lang.caseSubject, width: '143px'};
            } else if (item === 'priority') {
                array_push = {field: item, title: _lang.priority, template: '#= getCustomTranslation(priority) #', width: '100px'};
            } else if (item === 'reporter') {
                array_push = {field: item, title: _lang.requestedBy, width: '140px', template: '#= (reporter!=null)?reporter:"" #'};
            } else if (item === 'assigned_to') {
                array_push = {field: item, title: _lang.assignedTo, width: '140px', template: '#= (assigned_to!=null)?assigned_to:"" #'};
            } else if (item === 'estimated_effort') {
                array_push = {field: item, title: _lang.estEffort, width: '154px', template: '#= jQuery.fn.timemask({time: estimated_effort}) #'};
            } else if (item === 'effectiveEffort') {
                array_push = {field: item, title: _lang.efftEffort, width: '168px', template: '#= jQuery.fn.timemask({time: effectiveEffort}) #'};
            } else if(item === 'effectiveEffortHours') {
                array_push = {field: 'effectiveEffortHours', title: _lang.efftEffortHours, template: '#= (effectiveEffort === null) ? "" : (effectiveEffort) #' , width: '120px', sortable: false};
            } else if (item === 'due_date') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '168px'};
            } else if (item === 'createdBy') {
                array_push = {field: item, title: _lang.createdBy, width: '155px', template: '#= (createdBy!=null)?createdBy:"" #'};
            } else if (item === 'createdOn') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '133px'};
            } else if (item === 'modifiedByName') {
                array_push = {field: item, title: _lang.modifiedBy, width: '155px', template: '#= (modifiedByName!=null)?modifiedByName:"" #'};
            } else if (item === 'modifiedOn') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '133px'};
            } else if (item === 'archivedTasks') {
                array_push = {field: item, title: _lang.archived, template: '#= getCustomTranslation(archivedTasks) #', width: '101px'};
            } else if (item.startsWith('custom_field_')) { //check if the item is a custom field then get the title name from a defined array
                array_push = {field: item, title: customFieldsNames[item],width: '140px', template: '#= ' + item + '!==null ? (' + item + '.length>255 ? ' + item + '.substring(0, 255) + "..." :' + item + ' ):"" #'};  
            } else if (item === 'contributors') {
                array_push = {field: item, title: _lang.contributors,width: '140px'};
            } else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    var gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (XHRObj.responseText == 'access_denied') {
                        return false;
                    }
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if ($response.columns_html) {
                        jQuery('#column-picker-trigger-container').html($response.columns_html);
                        jQuery('*[data-callexport]').on('click', function () {
                            if (hasAccessToExport != 1) {
                                pinesMessage({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                            } else {
                                if ($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportTasksToExcel(true);
                                    } else {
                                        exportTasksToExcel();
                                    }
                                } else {
                                    applyExportingModuleMethod(this);
                                }
                            }
                        });
                        gridEvents();
                        loadExportModalRanges($response.totalRows);
                    }
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    jQuery('#selectAllCheckboxes').attr('checked', false);
                    disabledUnArchiveBtn();
                    animateDropdownMenuInGrids('tasksGrid');
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    var userValue = jQuery('#quickSearchFilterAssignedToValue').val();
                    operation.url = userValue !== '' ? (getBaseURL() + 'tasks/my_tasks') : (getBaseURL() + 'tasks/all_tasks');
                    options.filter = getFormFilters();
                    options.sortData = JSON.stringify(gridDataSrc.sort());
                    if (savePageSize) {
                        options.savePageSize = true;
                        savePageSize = false;
                    }
                    if (enableTaskStatusesQuickSearch) {
                        var taskStatuses = jQuery("input.task-status-group:checked", '#taskStatusesList').map(function () {
                            return this.value;
                        }).get();
                        options.TaskStatusesFilter = taskStatuses;
                    }
                }
                jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
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
                    taskId: { type: "string" },
                    title: {type: "string"},
                    caseId: {type: "string"},
                    legal_case_id: {type: "integer"},
                    taskType: {type: "string"},
                    private: {type: "string"},
                    priority: {type: "string"},
                    taskStatus: {type: "string"},
                    description: {type: "string"},
                    assigned_to: {type: "string"},
                    due_date: {type: "date"},
                    location: {type: "string"},
                    caseSubject: {type: "string"},
                    reporter: {type: "string"},
                    estimated_effort: {type: "string"},
                    effectiveEffort: {type: "string"},
                    effectiveEffortHours: {type: "string"},
                    createdBy: {type: "string"},
                    createdOn: {type: "date"},
                    modifiedBy: {type: "string"},
                    modifiedByName: {type: "string"},
                    modifiedOn: {type: "date"},
                    archivedTasks: {type: "string"},
                    actions: {type: "string"}
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
                        if(row['taskFullDescription'] != null){
                            row['taskFullDescription'] = escapeHtml(row['taskFullDescription'].replace(/(<([^>]+)>)/gi, ""));
                        }else{
                            row['taskFullDescription'] = '';
                        }
                        row['taskDescription'] = escapeHtml(row['taskDescription']);
                        if(row['description'] != null){
                            row['description'] = escapeHtml(row['description'].replace(/(<([^>]+)>)/gi, ""));
                        }else{
                            row['description'] = '';
                        }
                        row['caseSubject'] = escapeHtml(row['caseSubject']);
                        row['caseFullSubject'] = escapeHtml(row['caseFullSubject']);
                        row['createdBy'] = escapeHtml(row['createdBy']);
                        row['modifiedBy'] = escapeHtml(row['modifiedBy']);
                        row['modifiedByName'] = escapeHtml(row['modifiedByName']);
                        row['reporter'] = escapeHtml(row['reporter']);
                        row['assigned_to'] = escapeHtml(row['assigned_to']);
                        row['contributors'] = escapeHtml(row['contributors']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        sort: jQuery.parseJSON(gridSavedColumnsSorting || "null")
    });
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnResize: function (e) {
            fixFooterPosition();
            resizeHeaderAndFooter();
        },
        columnReorder: function (e) {
            orderColumns(e);
        },
        columns: tableColumns,
        dataBound: function () {
            jQuery('.tasks-title-desc').each(function (index, element) {
                if(jQuery(element).hasClass("tooltipstered")){
                    jQuery(element).tooltipster("destroy");
                }
                jQuery(element).tooltipster({
                    contentAsHTML: true,
                    timer: 22800,
                    animation: 'grow',
                    delay: 200,
                    theme: 'tooltipster-default',
                    touchDevices: false,
                    trigger: 'hover',
                    maxWidth: 350,
                    interactive: true
                });
            });
        },
        editable: "",
        filterable: false,
        height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {
            mode: "multiple"
        },
        toolbar: [{
                name: "toolbar-menu",
                template: '<div></div>'

            }],
    };
    gridTriggers({'gridContainer': $tasksGrid, 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    var gridGrid = $tasksGrid.data('kendoGrid');
    gridGrid.thead.find('th:first').append('<input type="checkbox" id="selectAllCheckboxes" onchange="checkUncheckAllCheckboxes(this);" title="' + _lang.selectAllRecords + '" />');
    gridGrid.thead.find("[data-field=actionsCol]>.k-header-column-menu").remove();
    displayColHeaderPlaceholder();
    if(contributedByMe) {
        jQuery("#task-contributors").prop('disabled',true).trigger('chosen:updated');
    }
}
function taskCallBack() {
    jQuery('#tasksGrid').data("kendoGrid").dataSource.read();
    return true;
}
