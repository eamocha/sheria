var timeTrackingGridDataSrc, timeTrackingGridOptions;
/* Functions */
function getFormFilters() {
    disableEmpty($timeTrackingGridFilters);
    var searchFilters = form2js('searchFilters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll($timeTrackingGridFilters);
    return filters;
}
function loadEventsForFilters() {
    caseLookup(jQuery('#caseValue', $timeTrackingGridFilters));
    taskLookup(jQuery('#taskValue', $timeTrackingGridFilters));
    userLookup('userValue');
    userLookup('createdByValue');
    userLookup('modifiedByValue');
    contactAutocompleteMultiOption('requestedByLookUp', 2, setContactToTimeForm);
    makeFieldsDatePicker({fields: ['creationDate', 'creationDateEndValue', 'logDateValue', 'logDateEndValue','modifiedOnValue','modifiedOnEndValue']});
    clientLookup({"lookupField": jQuery('#client-value')});
}
function setContactToTimeForm(record) {
    var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#requestedByLookUp').val(name);
}
function exportActivityLogsToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('searchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL() + 'export/activity_logs/' + time_logs).submit();
}
function deleteActivityDialog(e) {
    e = e || false;
    if (false != e && confirm(_lang.time_log_delete)) {
        jQuery.ajax({url: getBaseURL() + "time_tracking/delete/" + encodeURIComponent(e), type: "POST", dataType: "JSON", beforeSend: function () {
                jQuery("#loader-global").show();
            }, success: function (e) {
                jQuery("#loader-global").hide();
                if (e.result ) {
                    pinesMessage({ty: "success", m: e.msg});
                    if (null != $timeTrackingGrid)
                        $timeTrackingGrid.data("kendoGrid").dataSource.read()
                } else
                    pinesMessage({ty: "warning", m:e.msg});
            }, error: defaultAjaxJSONErrorsHandler})
    }
}
/* Vars  */
var $timeTrackingGridFilters = null;
jQuery(document).ready(function () {
    $timeTrackingGrid = jQuery('#timeTrackingGrid');
    gridInitialization();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        $timeTrackingGrid.data('kendoGrid').dataSource.read();
    });
    jQuery('.multi-select', '#searchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    $timeTrackingGridFilters = jQuery('#filtersFormWrapper');
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
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        $timeTrackingGrid.data("kendoGrid").dataSource.page(1);
    });
});
function getTimeLogsStatusTranslation(val) {
    return _lang.timeTrackingStatus[val];
}
function validateNumbers(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
    if (!decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
}
function gridInitialization() {
    var tableColumns = [];
    var savePageSize = false;
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({title: ' ', field: 'id',
            template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="javascript:;" onclick="logActivityDialog(\'#= id #\', {legalCaseCategory: \'#= caseCategory #\'})">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="deleteActivityDialog(\'#= id #\')">' + _lang.delete_log + '</a>' +
                    '<a class="dropdown-item" href="' + getBaseURL() + '/time_tracking/time_log_export_to_word/#= id #">' + _lang.exportToWord + '</a>' +
                    '</div></div>',
            sortable: false, width: '60px'});
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'logDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", template: "#= (logDate == null) ? '' : kendo.toString(logDate, 'yyyy-MM-dd') #", title: _lang.date, width: '140px'};
            } else if (item === 'worker') {
                array_push = {field: item, title: _lang.user, width: '120px', template: '#= (worker!=null ? worker : "") #'};
            }
            else if (item === 'seniorityLevel') {
                array_push = {field: item, title: _lang.SeniorityLevel, width: '143px'};
            }
            else if (item === 'legalCaseId') {
                array_push = {field: item, template: '<a href="' + getBaseURL() + '#= (caseCategory!="IP") ? "cases/edit/"+legal_case_id : "intellectual_properties/edit/"+legal_case_id #">#= legal_case_id ? legalCaseId : "" #</a>', title: _lang.caseId, width: '95px'};
            }
            else if (item === 'legalCaseSummary') {
                array_push = {field: item, template: '<a href="' + getBaseURL() + '#=(caseCategory!="IP") ? "cases/edit/"+legal_case_id : "intellectual_properties/edit/"+legal_case_id#"> #=(legalCaseSummary!=="" && legalCaseSummary!==null)? legalCaseSummary : "" #</a> ', title: _lang.caseSubject, width: '140px'};
            }
            else if (item === 'taskId') {
                array_push = {field: item, template: '<a href="tasks/view/#=task_id#" rel="tooltip" title="' + _lang.edit_task + '">#= taskId ? taskId : "" #</a>', title: _lang.taskId, width: '93px'};
            }
            else if (item === 'taskTitle') {
                array_push = {field: 'task_title', title: _lang.taskTitle, width: '200px'};
            }
            else if (item === 'taskSummary') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, template: '<span class="tasks-title-desc" title="#= task_full_summary #" > #= taskSummary!=="" && taskSummary !== null ? taskSummary : "" #</span>', title: _lang.task_description, width: '140px'};
            }
            else if (item === 'clientName') {
                array_push = {field: 'allRecordsClientName', title: _lang.client, width: '140px'};
            }
            else if (item === 'effectiveEffort') {
                array_push = {field: item, title: _lang.efftEffort, template: '#= jQuery.fn.timemask({time: effectiveEffort}) #', width: '120px'};
            }
            else if(item === 'effectiveEffortHours') {
                array_push = {field: "effectiveEffortHours", title: _lang.efftEffortHours, template: '#= (effectiveEffort === null) ? "" : (effectiveEffort) #', width: '120px', sortable: false};
            }
            else if (item === 'comments') {
                array_push = {field: item, title: _lang.description, template: '<a href="javascript:;" onclick="logActivityDialog(\'#= id #\', {legalCaseCategory: \'#= caseCategory #\'})" rel="tooltip" title="">#= (comments!=null&&comments!="") ? replaceHtmlCharacter(comments).substring(0,40)+"..." : ""#</a>', width: '320px'};
            }
            else if (item === 'timeTypeName') {
                array_push = {field: item, title: _lang.timeType, width: '150px'};
            }
            else if (item === 'timeInternalStatusName') {
                array_push = {field: item, title: _lang.timeInternalStatus, width: '150px'};
            }
            else if (item === 'timeStatus') {
                array_push = {field: item, title: _lang.timeStatus, template: '#= helpers.capitalizeFirstLetter(getTimeLogsStatusTranslation(timeStatus)) #', width: '132px'};
            }
            else if (item === 'inserter') {
                array_push = {field: item, title: _lang.createdBy, width: '130px', template: '#= (inserter!=null ? inserter : "") #'};
            }
            else if (item === 'createdOn') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '129px'};
            }
            else if (item === 'matterInternalReference') {
                array_push = {field: item, title: _lang.internalReference, width: '125px'};
            }
            else if (item === 'requestedBy') {
                array_push = {field: item, title: _lang.requestedBy, width: '100px'};
            }
            else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    timeTrackingGridDataSrc = new kendo.data.DataSource({
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
                            if(hasAccessToExport!=1){
                                pinesMessage({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                            }else {
                                if ($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportActivityLogsToExcel(true);
                                    } else {
                                        exportActivityLogsToExcel();
                                    }
                                } else {
                                    applyExportingModuleMethod(this);
                                }
                            }
                        });
                        gridEvents();
                        loadExportModalRanges($response.totalRows);
                    }
                    if ($timeTrackingGridFilters.is(':visible'))
                        $timeTrackingGridFilters.slideUp();
                    animateDropdownMenuInGrids('timeTrackingGrid');
                }, beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    operation.url = jQuery('#quickSearchFilterUserValue').val() !== '' ? (getBaseURL() + 'time_tracking/my_time_logs') : (getBaseURL() + 'time_tracking/all_time_logs');
                    options.filter = getFormFilters();
                    options.sortData = JSON.stringify(timeTrackingGridDataSrc.sort());
                    if (savePageSize) {
                        options.savePageSize = true;
                        savePageSize = false;
                    }
                }
                jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                options.caseIdFilter = jQuery('#caseIdFilter', '#searchFilters').val();
                return options;
            }
        },
        schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {editable: false, type: "number"},
                    user_id: {type: "number"},
                    task_id: {type: "number"},
                    legal_case_id: {type: "number"},
                    logDate: {type: "date"},
                    effectiveEffort: {type: "string"},
                    effectiveEffortHours: {type: "string"},
                    createdBy: {type: "number"},
                    createdOn: {type: "date"},
                    taskId: { type: "string" },
                    taskTitle: {type: "string"},
                    taskSummary: {type: "string"},
                    legalCaseId: {type: "string"},
                    legalCaseSummary: {type: "string"},
                    worker: {type: "string"},
                    seniorityLevel: {type: "string"},
                    inserter: {type: "string"},
                    requestedBy: {type: "string"},
                    comments: {type: "string"},
                    timeTypeName: {type: "string"},
                    timeInternalStatusName: {type: "string"},
                    timeStatus: {type: "string"},
                    allRecordsClientName: {type: "string"},
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
                        row['worker'] = escapeHtml(row['worker']);
                        row['legalCaseSummary'] = escapeHtml(row['legalCaseSummary']);
                        row['comments'] = escapeHtml(row['comments']);
                        row['inserter'] = escapeHtml(row['inserter']);
                        if(row['taskSummary'] !== null){
                            row['taskSummary'] = escapeHtml((row['taskSummary']).replace(/(<([^>]+)>)/gi, ""));
                        }
                        if(row['task_full_summary'] !== null){
                            row['task_full_summary'] = escapeHtml((row['task_full_summary']).replace(/(<([^>]+)>)/gi, ""));
                        }
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
    timeTrackingGridOptions = {autobind: true, dataSource: timeTrackingGridDataSrc,
        columns: tableColumns,
        dataBound: function () {
            jQuery('.tasks-title-desc').each(function (index, element) {
                jQuery(element).tooltip({
                    delay: {"show": 100, "hide": 100},
                    template: '<div class="tooltip" role="tooltip" style="z-index:6666"><div class="tooltip-arrow"></div><div class="tooltip-inner tooltip-cust"></div></div>',
                    container: 'body'
                });
            });
        },
        editable: "", filterable: false, height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true}, reorderable: true, resizable: true, scrollable: true, sortable: {mode: "multiple"}, selectable: "single",
        toolbar: [{
                name: "task-grid-toolbar",
                template: '<div></div>'
            }],
        columnResize: function (e) {
            fixFooterPosition();
            resizeHeaderAndFooter();
        },
        columnReorder: function (e) {
            orderColumns(e);
        }
    };
    gridTriggers({'gridContainer': $timeTrackingGrid, 'gridOptions': timeTrackingGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    displayColHeaderPlaceholder();
}
function taskCallBack() {
    jQuery('#timeTrackingGrid').data("kendoGrid").dataSource.read();
    return true;
}
