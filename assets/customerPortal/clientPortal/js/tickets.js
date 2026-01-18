var $tasksGridOptions, $tasksGrid;
var $hearingsGridOptions, $hearingsGrid;
jQuery(document).ready(function () {
    jQuery('[data-toggle="tooltip"]').tooltip();
    jQuery('input[type="text"], textarea, select').addClass('form-control');
    jQuery('div[requiredField="yes"]').each(function () {
        var container = jQuery(this);
        jQuery('.cp-screen-field', container).attr('required', '');
    });
    jQuery('.cp-screen-date-field').each(function () {
        var options = jQuery.extend({changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", firstDay: 1});
        jQuery(this).datepicker(options);
    });
    setActiveTab('tickets');

    $tasksGrid = jQuery('#tasksGrid');
    $hearingsGrid = jQuery('#hearingsGrid');
    loadTicketTasksGrid();
    loadTicketHearingsGrid();
    customGridToolbarCSSButtons();
});

function customGridToolbarCSSButtons() {
    jQuery('.k-grid-save-changes', '.k-grid-toolbar').removeClass('k-button').addClass('btn btn-info margin-right').css('line-height', '1').find('span').attr('class', 'margin-right fa-solid fa-circle-check');
    jQuery('.k-grid-cancel-changes', '.k-grid-toolbar').removeClass('k-button').addClass('btn btn-info').css('line-height', '1').find('span').attr('class', 'margin-right fa-solid fa-trash-can red');
    jQuery('.k-grid-add', '.k-grid-toolbar').removeClass('k-button').addClass('btn btn-info margin-right').css('line-height', '1').find('span').attr('class', 'margin-right fa-solid fa-plus');
    jQuery('.margin-right.fa-solid').hide();
}

jQuery(document).on("click", "#tab-tasks-label", function() {
    $tasksGrid.data('kendoGrid').dataSource.read();
});
jQuery(document).on("click", "#tab-hearings-label", function() {
    $hearingsGrid.data('kendoGrid').dataSource.read();
});

function loadTicketTasksGrid() {
    try {
        var tasksDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + "modules/customer-portal/tickets/ticket_tasks/" + jQuery("#ticket-id").val(),
                    dataType: "JSON",
                    type: "POST",
                    complete: function (XHRObj) {
                        if (XHRObj.responseText == 'access_denied') {
                            return false;
                        }
                        $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        if (undefined !== $response.error) {
                            pinesMessage({ty: 'error', m: $response.error});
                            $tasksGrid.data('kendoGrid').dataSource.read();
                        }
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        return {
                            models: kendo.stringify(options.models)
                        };
                    }
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        taskId: {editable: false, type: "string"},
                        taskType: {editable: false, type: "string"},
                        taskStatus: {editable: false, type: "string"},
                        description: {editable: false, type: "string"},
                        priority: {editable: false, type: "string"},
                        assigned_to: {editable: false, type: "string"},
                        estimated_effort: {editable: false, type: "string"},
                        due_date: {editable: false, type: "string"},
                        archived: {editable: false, type: "string"}
                    }
                }
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            batch: true, pageSize: 20, serverPaging: false, serverFiltering: false, serverSorting: false
        });
        $tasksGridOptions = {
            autobind: true,
            dataSource: tasksDataSrc,
            columns: [
                {field: "taskType", title: _lang.task_type, width: '130px'},
                {field: "taskStatus", title: _lang.task_status, width: '130px'},
                {field: "description", title: _lang.description, width: '200px'},
                {field: "priority", title: _lang.priority, width: '100px'},
                {field: "assigned_to", title: _lang.assignee, width: '180px'},
                {field: "estimated_effort", title: _lang.effort, width: '80px'},
                {field: "due_date", title: _lang.dueDate, width: '100px'},
                {field: "archived", title: _lang.archived, width: '100px'}
            ],
            editable: true,
            filterable: false,
            height: 500,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: "single",
            sortable: {mode: "multiple"},
            toolbar: [],
            columnMenu: {messages: _lang.kendo_grid_sortable_messages}
        };
        $tasksGrid.kendoGrid($tasksGridOptions);
    } catch (e) {
    }
}
function loadTicketHearingsGrid() {
    try {
        var hearingsDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + "modules/customer-portal/tickets/ticket_hearings/" + jQuery("#ticket-id").val(),
                    dataType: "JSON",
                    type: "POST",
                    complete: function (XHRObj) {
                        if (XHRObj.responseText == 'access_denied') {
                            return false;
                        }
                        $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        if (undefined !== $response.error) {
                            pinesMessage({ty: 'error', m: $response.error});
                            $hearingsGrid.data('kendoGrid').dataSource.read();
                        }
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        return {
                            models: kendo.stringify(options.models)
                        };
                    }
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {editable: false, type: "string"},
                        startDate: {editable: false, type: "string"},
                        startTime: {editable: false, type: "string"},
                        type_name: {editable: false, type: "string"},
                        stage_name: {editable: false, type: "string"},
                        lawyers: {editable: false, type: "string"},
                        judged: {editable: false, type: "string"},
                        judgment_name: {editable: false, type: "string"},
                        clients: {editable: false, type: "string"},
                        clientPosition: {editable: false, type: "string"},
                        reference: {editable: false, type: "string"},
                        opponents: {editable: false, type: "string"},
                        court: {editable: false, type: "string"},
                        courtDegree: {editable: false, type: "string"},
                        courtRegion: {editable: false, type: "string"},
                        courtType: {editable: false, type: "string"},
                        summary: {editable: false, type: "string"},
                        previousHearingDate: {editable: false, type: "string"},
                        comments: {editable: false, type: "string"},
                        reasons_of_postponement: {editable: false, type: "string"},
                        containerID: {editable: false, type: "string"},
                        areaOfPractice: {editable: false, type: "string"}
                    }
                }
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            batch: true, pageSize: 20, serverPaging: false, serverFiltering: false, serverSorting: false
        });
        $hearingsGridOptions = {
            autobind: true,
            dataSource: hearingsDataSrc,
            columns: [
                {field: "startDate", width: '194px', format: "{0:yyyy-MM-dd}", template: '#= (kendo.toString((hijriCalendarEnabled == 1 ? gregorianToHijri(startDate) : startDate), \'yyyy-MM-dd\') + ((startTime == null) ? \'\' : (\' \' + startTime.substring(0, 5)))) #', title: _lang.date},
                {field: "type_name", width: '192px', title: _lang.type},
                {field: "stage_name", width: '192px', title: _lang.hearingStage},
                {encoded: false, width: '192px', field: "lawyers", title: _lang.lawyers_for_hearing, template: "#= (lawyers == null) ? '' : lawyers #"},
                {field: "judged", width: '190px', title: _lang.judgedQuestion},
                {field: "judgment_name", width: '190px', title: _lang.judgment},
                {encoded: false, width: '192px', field: "clients", title: _lang.client, template: "#= (clients == null) ? '' : clients #"},
                {field: "clientPosition", width: '150px', title: _lang.clientPosition_Case},
                {field: "reference", width: '190px', title: _lang.externalReference},
                {encoded: false, width: '192px', field: "opponents", title: _lang.opponents_Case, template: "#= (opponents == null) ? '' : opponents #"},
                {field: "court", width: '192px', title: _lang.court},
                {field: "courtDegree", width: '192px', title: _lang.courtDegree},
                {field: "courtRegion", width: '192px', title: _lang.courtRegion},
                {field: "courtType", width: '192px', title: _lang.courtType},
                {field: "summary", width: '192px', title: _lang.summary},
                {field: "previousHearingDate", width: '194px', format: "{0:yyyy-MM-dd}", template: "#= (previousHearingDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(previousHearingDate) : previousHearingDate, 'yyyy-MM-dd'))#", title: _lang.previousHearingDate},
                {field: "comments", width: '192px', title: _lang.comments},
                {field: "reasons_of_postponement", width: '192px', title: _lang.reasons_of_postponement},
                {field: "containerID", width: '192px', title: _lang.containerID},
                {field: "areaOfPractice", width: '190px', title: _lang.caseType}
            ],
            editable: true,
            filterable: false,
            height: 500,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: "single",
            sortable: {mode: "multiple"},
            toolbar: [],
            columnMenu: {messages: _lang.kendo_grid_sortable_messages}
        };
        $hearingsGrid.kendoGrid($hearingsGridOptions);
    } catch (e) {
    }
}
