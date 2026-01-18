jQuery(document).ready(function () {
    $tiersGrid = jQuery('#tiersGrid');
    try {
        var casesSearchDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + "reports/cases_by_tiers/1/" + case_type_id + "/" + range1 + "/" + range2,
                    dataType: "JSON",
                    type: "POST"
                },
                parameterMap: function (options, operation) {
                    if ("read" == operation) {
                        options.caseSubject = jQuery('#caseLookUp').val();
                        options.case_type_id = case_type_id;
                        options.range1 = range1;
                        options.range2 = range2;
                        options.returnData = 1;
                        jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    }
                    return options;
                }
            },
            schema: {
                type: "json",
                data: "data",
                total: "totalRows",
                model: {
                    fields: {
                        id: {type: "integer"},
                        internalReference: {type: "string"},
                        subject: {type: "string"},
                        providerGroup: {type: "string"},
                        assignee: {type: "string"},
                        type: {type: "string"},
                        priority: {type: "string"},
                        status: {type: "string"},
                        statusComments: {type: "string"},
                        caseStage: {type: "string"},
                        arrivalDate: {type: "date"},
                        dueDate: {type: "date"},
                        clientName: {type: "string"},
                        estimatedEffort: {type: "integer"},
                        effectiveEffort: {type: "integer"},
                        caseValue: {type: "number"},
                        judgmentValue: {type: "number"},
                        recoveredValue: {type: "number"},
                        archivedCases: {type: "string"}

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
                            row['statusComments'] = escapeHtml(row['statusComments']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            pageSize: 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        });
        var casesSearchGridOptions = {
            autobind: true,
            dataSource: casesSearchDataSrc,
            columns: [
                {field: "id", filterable: false, title: _lang.id, template: '<a href="' + getBaseURL() + 'cases/edit/#= id #">#= caseID #</a><i class="iconLegal iconPrivacy#=private#"></i>', width: '120px'},
                {field: "internalReference", title: _lang.internalReference, width: '220px'},
                {field: "subject", title: _lang.subject_Case, width: '220px'},
                {field: "providerGroup", title: _lang.providerGroup, width: '150px'},
                {field: "assignee", title: _lang.assignee, width: '192px'},
                {field: "type", title: _lang.type, width: '90px'},
                {field: "priority", title: _lang.priority, template: '#= getCustomTranslation(priority) #', width: '85px'},
                {field: "status", title: _lang.caseStatus, width: '163px'},
                {field: "statusComments", title: _lang.status_comments, template: '#= (statusComments!=null&&statusComments!="") ? statusComments.substring(0,40)+"..." : ""#', width: '320px'},
                {field: "caseStage", title: _lang.caseStage, width: '173px', template: "#= caseStage && stage_id ? '<a href=\"' + getBaseURL() + 'cases/events/' + id + '?stage=stage-' + stage_id + '-container' + '\">' + caseStage + '</a>' : '' #"},
                {field: "arrivalDate", format: "{0:yyyy-MM-dd}", title: _lang.filedOn, width: '120px', template: "#= (arrivalDate == null) ? '' : kendo.toString(arrivalDate, 'yyyy-MM-dd') #"},
                {field: "dueDate", format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '158px', template: "#= (dueDate == null) ? '' : kendo.toString(dueDate, 'yyyy-MM-dd') #"},
                {field: "clientName", title: _lang.clientName_Case, width: '150px'},
                {field: "caseClientPosition", title: _lang.clientPosition_Case, width: '140px'},
                {field: "opponentNames", title: _lang.opponent_Case, width: '140px'},
                {field: "estimatedEffort", title: _lang.estEffort, template: '#= jQuery.fn.timemask({time: estimatedEffort}) #', width: '151px'},
                {field: "effectiveEffort", title: _lang.efftEffort, template: '#= jQuery.fn.timemask({time: effectiveEffort}) #', width: '162px'},
                {field: "caseValue", title: _lang.caseValue, template: "#= (caseValue==null)?'':kendo.toString(caseValue, \"n2\") #", width: '182px'},
                {field: "judgmentValue", title: _lang.judgmentValue, template: "#= (judgmentValue==null)?'':kendo.toString(judgmentValue, \"n2\") #", width: '168px'},
                {field: "recoveredValue", title: _lang.recoveredValue, template: "#= (recoveredValue==null)?'':kendo.toString(recoveredValue, \"n2\") #", width: '154px'},
                {field: "archivedCases", title: _lang.archived, template: '#= getCustomTranslation(archivedCases) #', width: '100px'}
            ],
            editable: false,
            filterable: false,
            height: 500,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            sortable: {
                mode: "multiple"
            },
            selectable: "single",
            toolbar: [{
                    name: "quick-add-edit",
                    template: '<div class="col-md-5 no-padding">'
                            + '<h4 class="col-md-2">' + _lang.cases + '</h4>'
                            + ' <div class="input-group col-md-7">'
                            + ' <input type="text" class="form-control search" placeholder=" '
                            + _lang.search + '" name="caseLookUp" id="caseLookUp" onkeyup="caseQuickSearch(event.keyCode, this.value);" title="'
                            + _lang.searchCase + '" />'
                            + '</div>'
                            + '</div>'
                            + '<div class="col-md-1 pull-right">'
                            + '<div class="btn-group pull-right">'
                            + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                            + _lang.actions + ' <span class="caret"></span>'
                            + '<span class="sr-only">Toggle Dropdown</span>'
                            + '</button>'
                            + '<ul class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                            + '<li><a onclick="exportCasesToExcel()" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a></li>'
                            + '</ul>'
                            + '</div>'
                            + '</div>'



                }],
            columnMenu: {messages: _lang.kendo_grid_sortable_messages}
        };
    } catch (e) {
    }
    $tiersGrid.kendoGrid(casesSearchGridOptions);
});
function getCustomTranslation(val) {
    return _lang.custom[val];
}
function exportCasesToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var caseLookUp = '';
    caseLookUp = jQuery('#caseLookUp').val();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(caseLookUp));
    newFormFilter.attr('action', getBaseURL() + 'export/cases_by_tiers/' + case_type_id + '/' + range1 + '/' + range2).submit();
}
function caseQuickSearch(keyCode, term) {
    if (keyCode == 13) {
        jQuery('#tiersGrid').data("kendoGrid").dataSource.read();
    }
}
