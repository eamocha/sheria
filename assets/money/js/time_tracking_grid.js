var enableQuickSearch = false;
var gridOptions = {};
var splittedItems = [];
var $bulkTimeTrackingGrid = null;
jQuery(document).ready(function () {
    $moneyTimeTrackingGrid = jQuery('#timeTrackingGrid');
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery('#timeTrackingGrid').data('kendoGrid').dataSource.read();
    });
    jQuery('.multi-select', '#searchFilters').chosen({no_results_text: _lang.no_results_matched,placeholder_text: _lang.select,width: "100%"}).change();
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
        enableQuickSearch = false;
        e.preventDefault();
        if (jQuery('#submitAndSaveFilter').is(':visible')) {
            gridAdvancedSearchLinkState = true;
        }
        jQuery('#timeTrackingGrid').data("kendoGrid").dataSource.page(1);
    });
    gridFiltersEvents('User_Activity_Log_Money_Module', 'timeTrackingGrid', 'searchFilters');
    gridInitialization();

});

function advancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        loadEventsForFilters();
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function loadEventsForFilters() {
    caseLookup(jQuery('#caseValue'));
    taskLookup(jQuery('#taskValue'));
    userLookup('userValue');
    userLookup('createdByValue');
    makeFieldsDatePicker({fields: ['creationDate', 'creationDateEndValue', 'logDateValue', 'logDateEndValue']});
    clientLookup({"lookupField" : jQuery("#client-value")});
}
function exportActivityLogsToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFiltersToLog();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    newFormFilter.attr('action', getBaseURL('money') + 'time_tracking/export_to_excel').submit();
}
function deleteActivityDialog(e) {
    e = e || false;
    if (false != e && confirm(_lang.time_log_delete)) {
        jQuery.ajax({
            url: getBaseURL() + "time_tracking/delete/" + encodeURIComponent(e),
            type: "POST",
            dataType: "JSON",
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (e) {
                jQuery("#loader-global").hide();
                if (e.result ) {
                    pinesMessage({ty: "success", m: e.msg});
                    if (null != $timeTrackingGrid)
                        $timeTrackingGrid.data("kendoGrid").dataSource.read()
                    if (null != $bulkTimeTrackingGrid) {
                        try { $bulkTimeTrackingGrid.data("kendoGrid").dataSource.read(); } catch (e) {}
                    }
                } else
                    pinesMessage({ty: "warning", m:e.msg});
            },
            error: defaultAjaxJSONErrorsHandler
        })
    }
}

function checkUncheckCheckboxes(checkBox) {
    jQuery("tbody" + " INPUT[type='checkbox']").attr('checked', checkBox.checked);
}
function validateDecimals(field, rules, i, options) {
	var val = field.val();
	var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
	if (!decimalPattern.test(val)) {
		return _lang.decimalAllowed;
	}
}

function gridInitialization() {
    let tableColumns = [];
    $bulkTimeTrackingGrid = jQuery('#timeTrackingGrid');
    var hiddenUserRate = showUserRate ? false : true;
    jQuery('#edit-money-bulk-time-container').on('hidden.bs.modal', function () {
        splittedItems = [];
    });
    jQuery("#bulk-time-dialog-submit",jQuery("#bulk-time-dialog-submit-container")).on('click',function () {
        moneyBulkGridActions.submitSplittedTimeLogs();
    });
    jQuery('.tooltip-title', '#edit-money-bulk-time-container').tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true,
        multiple: true
    });
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({
            title: ' ',
            field: 'id',
            template: function (dataItem) {
                var rowNumber = ++record - 1;
                if(dataItem.timeStatus == "billable"){
                    if(dataItem.billingStatus != "reimbursed" && dataItem.billingStatus != "invoiced"){
                        return  helpers.getSettingGridTemplate([
                            ['onclick="logActivityDialog('+ dataItem.id +',' + undefined + ',\'hideCase\')"', _lang.viewEdit],
                            ['onclick="splitTimePopup(\'' + convertDecimalToTime(  { time: dataItem.effectiveEffort })  + '\', '+ rowNumber + ',\'' + dataItem.uid +'\')"', helpers.capitalizeFirstLetter(_lang.split)],
                            ['onclick="deleteActivityDialog('+ dataItem.id +')"', _lang.delete_log],
                        ], true);
                    } else {
                        return '';
                    }
                } else if(dataItem.timeStatus == "newRecord"){
                    var marginGear = _lang.languageSettings['langDirection'] === 'rtl' ? 'margin-0-2' : 'margin-left-8';
                    return '<span class="fa-solid fa-trash-can purple_color '+ marginGear + '" onclick="moneyBulkGridActions.deleteRowGrid(\''+ dataItem.uid + '\', \''+ dataItem.itemId + '\')" aria-hidden="true"></span>';
                } else if(dataItem.timeStatus == "internal"){
                    return  helpers.getSettingGridTemplate([
                        ['onclick="logActivityDialog('+ dataItem.id +',' + undefined + ',\'hideCase\')"', _lang.viewEdit],
                        ['onclick="deleteActivityDialog('+ dataItem.id +')"', _lang.delete_log],
                    ], true);
                } else {
                    return '';
                }
            },
            filterable: true,
            sortable: false,
            width: '70px'
        });
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'logDate') {
                array_push = {field: "logDate", format: "{0:yyyy-MM-dd}", template: "#= (logDate == null) ? '' : kendo.toString(logDate, 'yyyy-MM-dd') #", title: _lang.date, width: '140px'};
            }
            else if (item === 'worker') {
                array_push = {field: "worker", title: _lang.user, width: '120px',template: '#= (worker!=null ? worker : "") #'};
            }
            else if (item === 'legalCaseId') {
                array_push = {field: "legalCaseId", template: "<a href=" + getBaseURL() + "#= caseCategory == 'IP' ? 'intellectual_properties' : 'cases' #/edit/#= legal_case_id #>#= legalCaseId ? legalCaseId : '' #</a>", title: _lang.caseId, width: '95px'};
            }
            else if (item === 'legalCaseSummary') {
                array_push = {field: "legalCaseSummary", title: _lang.caseSubject, template:'<span> #= legalCaseSummary!=="" && legalCaseSummary !== null ? replaceHtmlCharacter(legalCaseSummary) : "" # </span>', width: '320px'};
            }
            else if (item === 'matterInternalReference') {
                array_push = {field: "matterInternalReference", title: _lang.internalReference, width: '125px'};
            }
            else if (item === 'taskId') {
                array_push = {field: "taskId", template: '<a href="tasks/view/#=taskId#" rel="tooltip" title="' + _lang.edit_task + '">#= taskId ? taskId : "" #</a>', title: _lang.taskId, width: '95px'};
            }
            else if (item === 'taskTitle') {
                array_push = {field: "task_title", title: _lang.taskTitle, width: '180px'}
            }
            else if (item === 'taskSummary') {
                array_push = {field: "taskSummary", template: '<span> #= taskSummary!=="" && taskSummary !== null ? replaceHtmlCharacter(taskSummary) : "" # </span>', title: _lang.task_description, width: '154px'};
            }
            else if (item === 'clientName') {
                array_push = {field: "allRecordsClientName", title: _lang.client_Money, width: '140px'};
            }
            else if (item === 'effectiveEffort') {
                array_push = {field: "effectiveEffort", title: _lang.efftEffort, template: '#= jQuery.fn.timemask({time: effectiveEffort}) #', width: '149px'};
            }
            else if (item === 'effectiveEffortHours') {
                array_push = {field: "effectiveEffortHours", title: _lang.efftEffortHours, template: '#= (effectiveEffort === null) ? "" : (effectiveEffort) #', width: '149px', sortable: false};
            }
            else if (item === 'billingStatus') {
                array_push = {field: "billingStatus", template: '<span class="#= billingStatus == "invoiced" ? "lightGreen" : billingStatus == "reimbursed" ? "darkGreen" : "red" #">#= timeStatus == "billable" ? (billingStatus == "invoiced" ? helpers.capitalizeFirstLetter(_lang.timeTrackingStatus.invoiced) : billingStatus == "reimbursed" ?helpers.capitalizeFirstLetter(_lang.timeTrackingStatus.reimbursed) : helpers.capitalizeFirstLetter(_lang.timeTrackingStatus.toInvoice)) : billingStatus #</span>', width: '95px', title: _lang.billingStatusTimeLogs};
            }
            else if (item === 'comments') {
                array_push = {field: "comments", title: _lang.description, template: '<a href="javascript:;" onclick="logActivityDialog(\'#= id #\')" rel="tooltip" title="">#= (comments!=null&&comments!="") ? replaceHtmlCharacter(comments).substring(0,40)+"..." : ""#</a>', width: '320px'};
            }
            else if (item === 'timeTypeName' || item === 'timeTypeId') {
                array_push = {field: "timeTypeId", title: _lang.timeType, template: "#= (timeTypeId == null) ? ' ' : helpers.getObjectFromArr(timeTypesFromView, 'text', 'value', timeTypeId) #",  width: '200px'};
            }
            else if (item === 'timeInternalStatusName') {
                array_push = {field: "timeInternalStatusName", title: _lang.timeInternalStatus, width: '120px'};
            }
            else if (item === 'timeStatus') {
                array_push = {field: "timeStatus", title: _lang.timeStatus, template: '#= (typeof getTimeLogsStatusTranslation(timeStatus) !== \'undefined\') ? helpers.capitalizeFirstLetter(getTimeLogsStatusTranslation(timeStatus)) : _lang.ExpenseStatus[\'non-billable\'] #', width: '152px'};
            }
            else if (item === 'inserter') {
                array_push = {field: "inserter", title: _lang.createdBy, width: '130px',template: '#= (inserter!=null ? inserter : "") #'};
            }
            else if (item === 'createdOn') {
                array_push = {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '110px'};
            }
            else if(item === 'ratePerHour'){
                array_push = {field: "ratePerHour", title: _lang.rate, template: "#= moneyBulkGridActions.getUserRatePerHourTemplate(ratePerHour, timeStatus, rate_system, entityRatePerHour) #", sortable: false, width: "105px", hidden: hiddenUserRate};
            }
            tableColumns.push(array_push);
        });
        tableColumns.push({field: "rate_system", hidden: true, title: ""});
        tableColumns.push({field: "entityRatePerHour", hidden: true, title: ""});
    }
    try {
        var gridDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL('money') + "time_tracking/index",
                    dataType: "JSON",
                    type: "POST",
                    complete: function (XHRObj) {
                        jQuery('#loader-global').hide();
                        if (XHRObj.responseText == 'access_denied') {
                            return false;
                        }
                        $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        if ($response.result != undefined && !$response.result) {
                            if ($response.gridDetails != undefined) {
                                setGridDetails($response.gridDetails);
                            }
                            if ($response.feedbackMessage != undefined) {
                                pinesMessage({ty: $response.feedbackMessage.ty, m: $response.feedbackMessage.m});
                            } else {
                                pinesMessage({ty: 'error', m: _lang.updatesFailed});
                            }
                        }
                        animateDropdownMenuInGrids('timeTrackingGrid');
                        if ($response.columns_html) {
                            jQuery('#column-picker-trigger-container').html($response.columns_html);
                            gridEvents();
                        }
                        if (gridAdvancedSearchLinkState) {
                            gridAdvancedSearchLinkState = false;
                        } else {
                            if (jQuery('#filtersFormWrapper').is(':visible')) {
                                jQuery('#filtersFormWrapper').slideUp();
                                scrollToId('#filtersFormWrapper');
                            }
                        }
                        jQuery('.default-rate-tooltip').tooltipster({
                            contentAsHTML: true,
                            timer: 22800,
                            animation: 'grow',
                            delay: 200,
                            theme: 'tooltipster-default',
                            touchDevices: false,
                            trigger: 'hover',
                            maxWidth: 350,
                            interactive: true,
                            multiple: true
                        });
                    },
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" == operation) {
                        options.loadWithSavedFilters = 0;
                        if (gridSavedFiltersParams) {
                            options.filter = gridSavedFiltersParams;
                            var gridFormData = [];
                            gridFormData.formData = ["gridFilters"];
                            gridFormData.formData.gridFilters = gridSavedFiltersParams;
                            setGridFiltersData(gridFormData, 'timeTrackingGrid');
                            options.loadWithSavedFilters = 1;
                            options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                            gridSavedFiltersParams = '';
                        }
                        else {
                            var container = jQuery("#edit-money-bulk-time-container", jQuery("#entityDropDown"));
                            options.filter = checkWhichTypeOfFilterIUseAndReturnFiltersToLog();
                            options.returnData = 1;
                            options.organization_id = jQuery('#organizations', container).val();
                            options.only_log_rate = 0
                        }
                        options.sortData = JSON.stringify(gridDataSrc.sort());
                        jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    } else{
                        for (i in options.models) {
                            if (parseInt(options.models[i]['timeTypeId']) < 1) {
                                options.models[i]['timeTypeId'] = null;
                            }
                            if (parseInt(options.models[i]['timeInternalStatusId']) < 1) {
                                options.models[i]['timeInternalStatusId'] = null;
                            }
                        }
                        return { models: kendo.stringify(options.models) };
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
                        id: {editable: false, type: "number"},
                        user_id: {editable: false, type: "number"},
                        billingStatus: {editable: false, type: "string"},
                        task_id: { editable: false, type: "number" },
                        legal_case_id: {editable: false, type: "number"},
                        comments: {type: "string"},
                        timeTypeId: {field: "timeTypeId"},
                        timeInternalStatusId: {field: "timeInternalStatusId", editable: false},
                        logDate: {type: "date"},
                        effectiveEffort: {editable: true, type: "string"},
                        effectiveEffortHours: {editable: false, type: "string"},
                        createdBy: {editable: false, type: "number"},
                        createdOn: {editable: false, type: "date"},
                        taskId: { editable: false, type: "string" },
                        taskTitle: {editable: false, type: "string"},
                        taskSummary: {editable: false, type: "string"},
                        legalCaseId: {editable: false, type: "string"},
                        legalCaseSummary: {editable: false, type: "string"},
                        worker: {editable: false, type: "string"},
                        inserter: {editable: false, type: "string"},
                        timeStatus: {editable: false, type: "string"},
                        allRecordsClientName: {editable: false, type: "string"},
                        matterInternalReference: {editable: false, type: "string"},
                        ratePerHour: {type: "number", validation: { min: 0 }},
                        timeInternalStatusName: {editable: false, type: "string"}
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
                            row['legalCaseId'] = escapeHtml(row['legalCaseId']);
                            row['comments'] = escapeHtml(row['comments']);
                            row['allRecordsClientName'] = escapeHtml(row['allRecordsClientName']);
                            row['inserter'] = escapeHtml(row['inserter']);
                            if(row['taskSummary'] !== null){
                                row['taskSummary'] = escapeHtml((row['taskSummary']).replace(/(<([^>]+)>)/gi, ""));
                            }
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            },
            error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            page: 1,
            pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
            sort: jQuery.parseJSON(gridSavedColumnsSorting || "null"),
            change: function (e) {
                var bulkGrid = jQuery('#timeTrackingGrid').data("kendoGrid");
                if(!e.action){
                    var items = this.data();
                    if(Array.isArray(splittedItems) || !splittedItems.length){
                        if(splittedItems.length != 0){
                            jQuery("#bulk-time-dialog-submit-container").removeClass("hide");
                        } else{
                            jQuery("#bulk-time-dialog-submit-container").addClass("d-none");
                        }
                        jQuery.each(items, function(index, value){
                            jQuery.each(splittedItems, function(rowIndex, row){
                                if(row.itemId === value.id){
                                    if(row.isNew){
                                        var newRowbulkGrid = {
                                            id: "",
                                            logDate: convert(value.logDate).date,
                                            worker: value.worker,
                                            effectiveEffort: splittedItems[rowIndex].effectiveEffort,
                                            ratePerHour: value.ratePerHour,
                                            entityRatePerHour: value.entityRatePerHour,
                                            timeStatus: 'newRecord',
                                            comments: value.comments,
                                            timeTypeId: value.timeTypeId && value.timeTypeId.value ? value.timeTypeId.value : value.timeTypeId,
                                            timeInternalStatusId: value.timeInternalStatusId && value.timeInternalStatusId.value ? value.timeInternalStatusId.value : value.timeInternalStatusId,
                                            rate_system: value.rate_system,
                                            createdOn: value.createdOn,
                                            inserter: value.inserter,
                                            billingStatus: value.billingStatus,
                                            effectiveEffortHours: value.effectiveEffortHours,
                                            allRecordsClientName: value.allRecordsClientName,
                                            taskSummary: value.taskSummary,
                                            taskId: value.taskId,
                                            taskTitle: value.taskTitle,
                                            matterInternalReference: value.matterInternalReference,
                                            legalCaseSummary: value.legalCaseSummary,
                                            legalCaseId: value.legalCaseId,
                                            caseCategory: value.caseCategory,
                                            timeTrackingGrid: value.timeTrackingGrid,
                                            legal_case_id: value.legal_case_id,
                                            timeInternalStatusName: value.timeInternalStatusName
                                        };
                                        var rowItem = bulkGrid.dataSource.get(row.itemId);
                                        var indexNewRow = bulkGrid.dataSource.indexOf(rowItem);
                                        var newItem = bulkGrid.dataSource.insert(indexNewRow + 1, newRowbulkGrid);
                                        splittedItems[rowIndex].id = newItem.uid;
                                    } else {
                                        splittedItems[rowIndex].id = value.uid;
                                        splittedItems[rowIndex].entityRatePerHour = value.entityRatePerHour;
                                        moneyBulkGridActions.setGridField(value.uid, "effectiveEffort", splittedItems[rowIndex].effectiveEffort, bulkGrid);
                                    }
                                }
                            });
                        });
                    }
                }
                if (e.action === "itemchange"){
                    var rowItem = e.items[0];
                    moneyBulkGridActions.updateItemRow(bulkGrid, rowItem);
                }
            }
        });
        gridOptions = {
            autobind: true,
            dataSource: gridDataSrc,
            columns: tableColumns,
            editable: true,
            filterable: false,
            height: 480,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: "single",
            sortable: {mode: "multiple"},
            dataBinding: function(e) {
                record = (this.dataSource.page() -1) * this.dataSource.pageSize();
            },
            dataBound: function (e) {
                moneyBulkGridActions.boundDataToGridStyle();
            },
            toolbar: [{
                    name: "toolbar-menu",
                    template: '<div></div>'

                }],
            columnResize: function (e) {
                fixFooterPosition();
                resizeHeaderAndFooter();
            },
            columnReorder: function (e) {
                orderColumns(e);
            },
            edit: function (e) {
                if(e.model.timeStatus == "internal" || e.model.timeStatus == "newRecord") {
                    var ratePerHourElement = e.container.find("input[name=ratePerHour]");
                    ratePerHourElement.val("");
                    ratePerHourElement.attr('disabled', 'disabled');
                }
                if(e.model.billingStatus == "reimbursed" || e.model.billingStatus == "invoiced"){
                    var invoicedRow = e.container.find("input");
                    invoicedRow.attr('disabled', 'disabled');
                }
                if(e.container.find("input[name=ratePerHour]") && e.container.find("input[name=ratePerHour]").length > 0){
                    var ratePerHourElement = e.container.find("input[name=ratePerHour]");
                    ratePerHourElement.blur(function(){
                        setTimeout(function () {
                            jQuery('.default-rate-tooltip').tooltipster({
                                contentAsHTML: true,
                                timer: 22800,
                                animation: 'grow',
                                delay: 200,
                                theme: 'tooltipster-default',
                                touchDevices: false,
                                trigger: 'hover',
                                maxWidth: 350,
                                interactive: true,
                                multiple: true
                            });
                        }, 800);
                    });
                }
                var effectiveEffortElement = e.container.find("input[name=effectiveEffort]");
                effectiveEffortElement.attr('disabled', 'disabled');
                var fieldName = gridOptions.columns[e.container.index()].field;
                if(fieldName == 'effectiveEffort'){
                    pinesMessageV2({ty: 'information', m: _lang.fieldCanBeEdit});
                }
            }
        };
    } catch (e) {
    }
    if (undefined == $bulkTimeTrackingGrid.data('kendoGrid')) {
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'timeTypeId') {
                if (timeTypesFromView !== ""){
                    gridOptions.columns[i + 1].values = timeTypesFromView;
                }
            }
        });
        $bulkTimeTrackingGrid.kendoGrid(gridOptions);
        fixGridHeader(true);

        return false;
    } else{
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'timeTypeId') {
                if (timeTypesFromView !== ""){
                    gridOptions.columns[i + 1].values = timeTypesFromView;
                }
            }
        });
        gridTriggers({'gridContainer': jQuery('#timeTrackingGrid'), 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
        return false;
    }
    gridTriggers({'gridContainer': jQuery('#timeTrackingGrid'), 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
}
function logQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterLogValue', '#filtersFormWrapper').val(term);
        jQuery('#timeTrackingGrid').data("kendoGrid").dataSource.page(1);
    }
}

function checkWhichTypeOfFilterIUseAndReturnFiltersToLog() {
    var filtersForm = jQuery('#searchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('searchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterLogValue', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
var moneyBulkGridActions = (function() {

    function splitRow(bulkGrid, id, uid, convertedBillableTime, convertedNoneBillableTime){
        var itemRow = bulkGrid.dataSource.getByUid(uid);
        var existInSplitArray = "";
        jQuery.each(splittedItems, function(index, value){
            if(value.id === uid){
                existInSplitArray = index;
            }
        });
        setGridField(uid, "effectiveEffort", convertedBillableTime, bulkGrid);
        if(existInSplitArray !== "") splittedItems[existInSplitArray].effectiveEffort = convertedBillableTime;
        insertRowToGrid(bulkGrid, id, convertedNoneBillableTime, itemRow.id);
        if(splittedItems.length != 0){
            jQuery("#bulk-time-dialog-submit-container").removeClass("hide");
        } else{
            jQuery("#bulk-time-dialog-submit-container").addClass("d-none");
        }
    }

    function setFields(uid, itemRow, bulkGrid, rate_system) {
        var itemSchemaObject ={
            id: itemRow.id,
            logDate: itemRow.logDate,
            worker: itemRow.worker,
            effectiveEffort: itemRow.effectiveEffort,
            ratePerHour: itemRow.ratePerHour,
            entityRatePerHour: itemRow.entityRatePerHour,
            timeStatus: itemRow.timeStatus,
            comments: itemRow.comments,
            timeTypeId: itemRow.timeTypeId && itemRow.timeTypeId.value ? itemRow.timeTypeId.value : itemRow.timeTypeId,
            timeInternalStatusId: itemRow.timeInternalStatusId && itemRow.timeInternalStatusId.value ? itemRow.timeInternalStatusId.value : itemRow.timeInternalStatusId,
            rate_system: rate_system,
            createdOn: itemRow.createdOn,
            inserter: itemRow.inserter,
            billingStatus: itemRow.billingStatus,
            effectiveEffortHours: itemRow.effectiveEffortHours,
            allRecordsClientName: itemRow.allRecordsClientName,
            taskSummary: itemRow.taskSummary,
            taskId: itemRow.taskId,
            taskTitle: itemRow.taskTitle,
            matterInternalReference: itemRow.matterInternalReference,
            legalCaseSummary: itemRow.legalCaseSummary,
            legalCaseId: itemRow.legalCaseId,
            caseCategory: itemRow.caseCategory,
            timeTrackingGrid: itemRow.timeTrackingGrid,
            legal_case_id: itemRow.legal_case_id,
            timeInternalStatusName: itemRow.timeInternalStatusName
        };
        jQuery.each(itemSchemaObject,function(index, value){
            setGridField(uid, index, value, bulkGrid);
        });
    }

    function setGridField(uid, field, value, grid) {
        var rowItem = grid.dataSource.getByUid(uid);
        rowItem.set(field, value);
    }

    function insertRowToGrid(bulkGrid , id ,NoneBillableTime, itemRowId) {
        id = parseInt(id);
        let gridData = bulkGrid.dataSource.data();
        var newRowbulkGrid = {
            id: "",
            logDate: convert(gridData[id].logDate).date,
            worker: gridData[id].worker,
            effectiveEffort: NoneBillableTime,
            ratePerHour: gridData[id].ratePerHour,
            entityRatePerHour: gridData[id].entityRatePerHour,
            timeStatus: 'newRecord',
            comments: gridData[id].comments,
            timeTypeId: gridData[id].timeTypeId && gridData[id].timeTypeId.value ? gridData[id].timeTypeId.value : gridData[id].timeTypeId,
            timeInternalStatusId: gridData[id].timeInternalStatusId && gridData[id].timeInternalStatusId.value ? gridData[id].timeInternalStatusId.value : gridData[id].timeInternalStatusId,
            rate_system: gridData[id].rate_system,
            createdOn: gridData[id].createdOn,
            inserter: gridData[id].inserter,
            billingStatus: gridData[id].billingStatus,
            effectiveEffortHours: gridData[id].effectiveEffortHours,
            allRecordsClientName: gridData[id].allRecordsClientName,
            taskSummary: gridData[id].taskSummary,
            taskId: gridData[id].taskId,
            taskTitle: gridData[id].taskTitle,
            matterInternalReference: gridData[id].matterInternalReference,
            legalCaseSummary: gridData[id].legalCaseSummary,
            legalCaseId: gridData[id].legalCaseId,
            caseCategory: gridData[id].caseCategory,
            timeTrackingGrid: gridData[id].timeTrackingGrid,
            legal_case_id: gridData[id].legal_case_id,
            itemId: itemRowId,
            timeInternalStatusName: gridData[id].timeInternalStatusName
        };
        var newItem = bulkGrid.dataSource.insert(id + 1, newRowbulkGrid);
        jQuery(".k-grid-content").height("+=250");
        splittedItems.push({
            id: newItem.uid,
            isNew: true,
            itemId: itemRowId,
            effectiveEffort: NoneBillableTime,
            rowNumber: id,
            ratePerHour: gridData[id].ratePerHour,
            entityRatePerHour: gridData[id].entityRatePerHour,
            timeTypeId: gridData[id].timeTypeId && gridData[id].timeTypeId.value ? gridData[id].timeTypeId.value : gridData[id].timeTypeId,
            timeInternalStatusId: gridData[id].timeInternalStatusId && gridData[id].timeInternalStatusId.value ? gridData[id].timeInternalStatusId.value : gridData[id].timeInternalStatusId,
            comments: gridData[id].comments,
            logDate: convert(gridData[id].logDate).date,
            rate_system: gridData[id].rate_system,
            createdOn: gridData[id].createdOn,
            inserter: gridData[id].inserter,
            billingStatus: gridData[id].billingStatus,
            timeStatus: 'newRecord',
            effectiveEffortHours: gridData[id].effectiveEffortHours,
            allRecordsClientName: gridData[id].allRecordsClientName,
            taskSummary: gridData[id].taskSummary,
            taskId: gridData[id].taskId,
            taskTitle: gridData[id].taskTitle,
            matterInternalReference: gridData[id].matterInternalReference,
            legalCaseSummary: gridData[id].legalCaseSummary,
            legalCaseId: gridData[id].legalCaseId,
            caseCategory: gridData[id].caseCategory,
            timeTrackingGrid: gridData[id].timeTrackingGrid,
            legal_case_id: gridData[id].legal_case_id,
            timeInternalStatusName: gridData[id].timeInternalStatusName
        });
        fixHeight();
        boundDataToGridStyle();
    }

    function updateItemRow(bulkGrid, item){
        var itemRow = bulkGrid.dataSource.getByUid(item.uid);
        var existInSplitArray = "";
        jQuery.each(splittedItems, function(index, value){
            if(value.id === item.uid){
                existInSplitArray = index;
            }
        });
        if(existInSplitArray !== ""){
            splittedItems[existInSplitArray].effectiveEffort = itemRow.effectiveEffort;
            splittedItems[existInSplitArray].ratePerHour = itemRow.ratePerHour;
            splittedItems[existInSplitArray].entityRatePerHour = itemRow.entityRatePerHour;
            splittedItems[existInSplitArray].logDate = convert(itemRow.logDate).date;
            splittedItems[existInSplitArray].comments = itemRow.comments;
            splittedItems[existInSplitArray].timeTypeId = itemRow.timeTypeId && itemRow.timeTypeId.value ? itemRow.timeTypeId.value : itemRow.timeTypeId;
            splittedItems[existInSplitArray].timeInternalStatusId = itemRow.timeInternalStatusId && itemRow.timeInternalStatusId.value ? itemRow.timeInternalStatusId.value : itemRow.timeInternalStatusId;
            splittedItems[existInSplitArray].rate_system = (itemRow.ratePerHour) ? (itemRow.rate_system == 'system_rate' ? itemRow.rate_system : 'fixed_rate')  : null;
            splittedItems[existInSplitArray].createdOn= itemRow.createdOn;
            splittedItems[existInSplitArray].inserter = itemRow.inserter;
            splittedItems[existInSplitArray].billingStatus = itemRow.billingStatus;
            splittedItems[existInSplitArray].allRecordsClientName = itemRow.allRecordsClientName;
            splittedItems[existInSplitArray].taskId = itemRow.taskId;
            splittedItems[existInSplitArray].timeInternalStatusName = itemRow.timeInternalStatusName;
        } else {
            splittedItems.push(
                {
                    id: item.uid,
                    isNew: false,
                    itemId: itemRow.id,
                    effectiveEffort: itemRow.effectiveEffort,
                    rowNumber: itemRow.id,
                    ratePerHour: itemRow.ratePerHour,
                    entityRatePerHour: itemRow.entityRatePerHour,
                    timeTypeId: itemRow.timeTypeId && itemRow.timeTypeId.value ? itemRow.timeTypeId.value : itemRow.timeTypeId,
                    timeInternalStatusId: itemRow.timeInternalStatusId && itemRow.timeInternalStatusId.value ? itemRow.timeInternalStatusId.value : itemRow.timeInternalStatusId,
                    comments: itemRow.comments,
                    logDate: convert(itemRow.logDate).date,
                    rate_system: (itemRow.ratePerHour) ? (itemRow.rate_system === 'system_rate' ? itemRow.rate_system : 'fixed_rate')  : null,
                    createdOn: itemRow.createdOn,
                    inserter: itemRow.inserter,
                    billingStatus: itemRow.billingStatus,
                    timeStatus: 'newRecord',
                    effectiveEffortHours: itemRow.effectiveEffortHours,
                    allRecordsClientName: itemRow.allRecordsClientName,
                    taskSummary: itemRow.taskSummary,
                    taskId: itemRow.taskId,
                    taskTitle: itemRow.taskTitle,
                    matterInternalReference: itemRow.matterInternalReference,
                    legalCaseSummary: itemRow.legalCaseSummary,
                    legalCaseId: itemRow.legalCaseId,
                    caseCategory: itemRow.caseCategory,
                    timeTrackingGrid: itemRow.timeTrackingGrid,
                    legal_case_id: itemRow.legal_case_id,
                    timeInternalStatusName: itemRow.timeInternalStatusName
                }
            );
        }
        var itemRowSystem = (itemRow.ratePerHour) ? (itemRow.rate_system === 'system_rate' ? itemRow.rate_system : 'fixed_rate')  : null;
        setFields(item.uid, itemRow, bulkGrid, itemRowSystem);
        var bulkTable = jQuery("#timeTrackingGrid tbody");
        bulkTable.find("tr[data-uid=" + item.uid + "]").addClass("edit-kendo-row");
        if(splittedItems.length != 0){
            jQuery("#bulk-time-dialog-submit-container").removeClass("hide");
        } else{
            jQuery("#bulk-time-dialog-submit-container").addClass("d-none");
        }
    }

    function boundDataToGridStyle(){
        var bulkTable = jQuery("#timeTrackingGrid tbody");
        if(Array.isArray(splittedItems) && splittedItems.length){
            jQuery.each(splittedItems,function (index, value) {
                bulkTable.find("tr[data-uid=" + value.id + "]").addClass(value.isNew ? "new-kendo-row" : "edit-kendo-row");
            });
        }
    }

    function deleteRowGrid(uid){
        var bulkEditData = jQuery('#timeTrackingGrid').data("kendoGrid").dataSource;
        var dataRow = bulkEditData.getByUid(uid);
        var itemParentIndex = '';
        var isTheLatestChild = true;
        splittedItems = splittedItems.filter(function( item ) {
            if(item.id === uid){
                jQuery.each(splittedItems,function (parentIndex, parentItem) {
                    if(parentItem.itemId === item.itemId && !parentItem.isNew){
                        splittedItems[parentIndex].effectiveEffort = (parseFloat(parentItem.effectiveEffort) + parseFloat(dataRow.effectiveEffort)).toFixed(2);
                        itemParentIndex = parentIndex;
                        jQuery.each(splittedItems,function(childrenIndex, childrenValue){
                            if(childrenValue.isNew && parentItem.itemId === childrenValue.itemId && childrenValue.id !== uid){
                                isTheLatestChild = false;
                            }
                        });
                    }
                });
            } else {
                return true;
            }
        });
        if(isTheLatestChild) splittedItems.splice(itemParentIndex, 1);
        bulkEditData.remove(dataRow);
        bulkEditData.read();
        fixHeight();
    }

    function submitSplittedTimeLogs() {
        var container = jQuery("#edit-money-bulk-time-container");
        if(Array.isArray(splittedItems) && splittedItems.length){
            jQuery.ajax({
                dataType: 'JSON',
                url: getBaseURL() + 'cases/bulk_edit_time/',
                data: {"data": JSON.stringify(splittedItems)},
                type: 'POST',
                beforeSend: function () {
                    ajaxEvents.beforeActionEvents(container);
                },
                success: function (response) {
                    if(response.status){
                        if (null != $timeTrackingGrid) {
                            try { $timeTrackingGrid.data("kendoGrid").dataSource.read(); } catch (e) {}
                        }
                        splittedItems = [];
                        if (null != $bulkTimeTrackingGrid) {
                            try { $bulkTimeTrackingGrid.data("kendoGrid").dataSource.read(); } catch (e) {}
                        }
                        pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    }
                }, complete: function () {
                    ajaxEvents.completeEventsAction(container);
                },
                error: defaultAjaxJSONErrorsHandler
            });
        } else {
            pinesMessageV2({ty: 'information', m: _lang.noRecordsChanged});
            jQuery('.modal').modal('hide');
        }
    }

    function getUserRatePerHourTemplate(ratePerHour, timeStatus, rateSystem, entityRatePerHour){
        var NoRateTemplate = _lang.noRateFound + "<span class=\"margin-left-3 pull-right\" style=\"width:auto;margin-top:3px;margin-right:3px\"><span class=\"fas fa-question-circle tooltip-title billable-check-box-message\"></span></span>";
        if(timeStatus == "billable"){
            if(rateSystem != null){
                if((ratePerHour != null && ratePerHour > 0)){
                    return ratePerHour;
                } else{
                    return NoRateTemplate;
                }
            } else {
                if(entityRatePerHour != null && entityRatePerHour > 0){
                    return getDefaultRateTemplate(entityRatePerHour);
                } else{
                    return NoRateTemplate;
                }
            }
        } else{
            return "";
        }
    }

    function getDefaultRateTemplate(rate){
        return "<span title='" + rate + "' class='default-rate-tooltip'>" + _lang.defaultRate + "</span>"
    }

    return {
        splitRow: splitRow,
        setGridField: setGridField,
        setFields: setFields,
        deleteRowGrid: deleteRowGrid,
        updateItemRow: updateItemRow,
        insertRowToGrid: insertRowToGrid,
        boundDataToGridStyle: boundDataToGridStyle,
        submitSplittedTimeLogs: submitSplittedTimeLogs,
        getUserRatePerHourTemplate: getUserRatePerHourTemplate
    };
}());
function splitTimePopup(effortValue, id, uid) {
    uid = uid || false;
    var splitDialogUrl = getBaseURL().concat('time_tracking/split_time/').concat(effortValue);
    var data = {};
    jQuery.ajax({
        url: splitDialogUrl,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var splitDialogId = "split-time-container";
                jQuery('<div id="split-time-container"></div>').appendTo("body");
                var container = jQuery("#"+ splitDialogId);
                container.html(response.html);
                initializeModalSize(container, 0.35, 'auto');
                commonModalDialogEvents(container);
                var bulkGrid = jQuery('#timeTrackingGrid').data("kendoGrid");
                var rowItem = bulkGrid.dataSource.getByUid(uid);
                if(rowItem.logDate){
                    jQuery("#split-date-wrapper span",container).html(helpers.convertDate(rowItem.logDate).date);
                    jQuery("#split-date-wrapper", container).removeClass("hide");
                }
                var originalTimeDecimal = parseFloat(rowItem.effectiveEffort);
                jQuery("#effective-effort-hour-billable",container).keyup(function() {
                    if(jQuery(this).val()){
                        var convertedBillableTime = convertTimeToDecimal(jQuery(this).val());
                        if(convertedBillableTime){
                            jQuery("#effective-effort-hour-non-billable",container).val(convertDecimalToTime({time: parseFloat((originalTimeDecimal - convertedBillableTime).toFixed(2))}));
                        }
                    }
                });
                jQuery("#split-time-dialog-submit",container).click(function () {
                    var convertedBillableTime = convertTimeToDecimal(jQuery("#effective-effort-hour-billable",container).val());
                    var convertedNoneBillableTime = convertTimeToDecimal(jQuery("#effective-effort-hour-non-billable",container).val());
                    if(!jQuery("#effective-effort-hour-billable",container).val()){
                        ajaxEvents.displayValidationError('effective-effort-hour-billable', container,_lang.validation_field_required.sprintf([_lang.timeTrackingStatus.billable]));
                    } else if(!convertedBillableTime){
                        ajaxEvents.displayValidationError('effective-effort-hour-billable', container,_lang.timeValidateFormat.sprintf([_lang.timeTrackingStatus.billable]));
                    } else if(originalTimeDecimal <= convertedBillableTime){
                        ajaxEvents.displayValidationError('effective-effort-hour-billable', container,_lang.splitTimeMessage);
                    } else {
                        moneyBulkGridActions.splitRow(bulkGrid, id, uid, convertedBillableTime, convertedNoneBillableTime);
                        jQuery('.modal', "#split-time-container").modal('hide');
                    }
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function getTimeLogsStatusTranslation(val) {
    if (val !== '-')
        return _lang.timeTrackingStatus[val];
    else
        return '';
}
function getExpenseStatusTranslation(val) {
    return _lang.ExpenseStatus[val];
}
function fixHeight() {
    fixFooterPosition();
    resizeHeaderAndFooter();
}
jQuery(document).ready(function(){
    if (undefined == $bulkTimeTrackingGrid.data('kendoGrid')) {
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'timeTypeId') {
                if (timeTypesFromView !== ""){
                    gridOptions.columns[i + 1].values = timeTypesFromView;
                }
            }
        });
        $bulkTimeTrackingGrid.kendoGrid(gridOptions);
        return false;
    }
});