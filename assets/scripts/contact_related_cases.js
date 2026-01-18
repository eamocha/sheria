var enableQuickSearch = true;
jQuery(document).ready(function () {
    relateToCaseLookup();
    var contactRelatedCasesDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "contacts/related_cases/" + jQuery('#contactIdFilter').val(),
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" === operation) {
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
                    id: {editable: false, type: "number"},
                    contact_id: {type: "string"},
                    caseId: {type: "string"},
                    subject: {type: "string"},
                    caseStatus: {type: "string"},
                    caseType: {type: "string"},
                    priority: {type: "string"},
                    arrivalDate: {type: "date"},
                    dueDate: {type: "date"},
                    providerGroup: {type: "string"},
                    assignee: {type: "string"},
                    actions: {editable:false}
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
                        row['assignee'] = escapeHtml(row['assignee']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            if (e.xhr.responseText != 'True')
                defaultAjaxJSONErrorsHandler(e.xhr)
        },
        batch: true,
        pageSize: 10,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    var contactRelatedCasesGridOptions = {
        autobind: true,
        dataSource: contactRelatedCasesDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "caseId", title: _lang.caseId, template: "<a href=" + getBaseURL() + "#= category == 'IP' ? 'intellectual_properties' : 'cases' #/edit/#= case_id #>#= caseId #</a>", width: '120px'},
            {field: "subject", title: _lang.caseSubject, width: '220px'},
            {field: "caseStatus", title: _lang.workflow_status, width: '120px'},
            {field: "category", title: _lang.type, width: '130px', template: "#= category == 'Matter' ? '" + _lang.corporateMatter + "' : (category == 'Litigation' ? '" + _lang.litigation + "' : '" + _lang.IP + "') #"},
            {field: "providerGroup", title: _lang.providerGroup, width: '150px'},
            {field: "assignee", title: _lang.assignee, width: '170px', template: '#= (assignee!=null && assignToStatus=="Inactive")? assignee+" ("+_lang.custom[assignToStatus]+")":((assignee!=null)?assignee:"") #'},
            {field: "caseType", title: _lang.caseType, width: '174px'},
            {field: "case_container_id", title: _lang.caseContainerID, template: '#=  getContainersLinks((container_id != null ? container_id : "") , (case_container_id != null ? case_container_id : "")) #' , width: '160px'},
            {field: "priority", title: _lang.priority, width: '90px'},
            {field: "arrivalDate", format: "{0:yyyy-MM-dd}", title: _lang.filedOn, width: '120px', template: "#= (arrivalDate == null) ? '' : kendo.toString(arrivalDate, 'yyyy-MM-dd') #"},
            {field: "dueDate", format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '151px', template: "#= (dueDate == null) ? '' : kendo.toString(dueDate, 'yyyy-MM-dd') #"},
            {field: "actions", title: _lang.actions, width: '120px', template: '<div class="wraper-actions"><div class="list-of-actions"><a href="javascript:;" onclick="deleteSelectedRow(\'#= case_id #\')"><i class="fa fa-fw fa-trash"></i></a></div></div>'}
        ],
        toolbar: [{name: "quick-search", template: ' <div class="col-md-4 mb-10"><div class="input-group col-md-11"><input type="text" class="form-control search margin-top quick-search-filter" placeholder=" ' + _lang.searchCase + '" name="caseLookUp" id="caseLookUp" onkeyup="caseQuickSearch(event.keyCode, this.value);" title="' + _lang.searchCase + '" /></div></div>'
                        + '<ul class="operations quick-action pull-right">'
                        + '<li class="opts-li actions">'
                        + '<a href="javascript:;" class="gh-top-tools-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="spr spr-export"></i></a>'
                        + '<div class="dropdown-menu">'
                        + '<a class="dropdown-item" href="javascript:;" title="' + _lang.exportToExcel + '" onclick="exportContactRelatedCasesToExcel()">' + _lang.exportToExcel + '</a>'
                        + '</div>'
                        + '</li>'
                        + '</ul>'
                        + '</div>'
            }],
        editable: false,
        filterable: false,
         pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: true,buttonCount:5
        },
        reorderable: true,
        resizable: true,
        scrollable: true,
        height: '330',
        selectable: "single",
        sortable: {mode: "multiple"}
    };
    var relatedCasesGrid = jQuery('#relatedCasesGrid');
    if (undefined == relatedCasesGrid.data('kendoGrid')) {
        relatedCasesGrid.kendoGrid(contactRelatedCasesGridOptions);
        return false;
    }
    relatedCasesGrid.data('kendoGrid').dataSource.read();
    return false;
});
function deleteSelectedRow(caseId) {
    confirmationDialog('confim_delete_action', {resultHandler: _deleteSelectedRow, parm: caseId});
}

function _deleteSelectedRow(caseId) {
    var contactId = jQuery('#contactIdFilter', '#contactRelatedCasesSearchFilters').val();
    jQuery.ajax({
        url: getBaseURL() + 'contacts/related_case_delete',
        dataType: "json",
        type: "POST",
        data: {
            contactId: contactId,
            caseId: caseId
        },
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.result) {
                case true:  // remove successfuly
                    ty = 'success';
                    m = _lang.relationRemovedSuccessfully;
                    break;
                case false: // could not delete record
                    m = _lang.deleteRecordFailed;
                    break;
                default:
                    break;
            }
            pinesMessage({ty: ty, m: m});
            jQuery('#relatedCasesGrid').data('kendoGrid').dataSource.read();
        }
    });
}
function caseQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterCaseSubjectValue', '#filtersFormWrapper').val(term);
        var relatedCasesGrid = jQuery('#relatedCasesGrid');
        relatedCasesGrid.data("kendoGrid").dataSource.page(1);
    }
}
function getFormFilters() {
    var filtersForm = jQuery('#contactRelatedCasesSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('contactRelatedCasesSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterCaseSubjectValue', '#filtersFormWrapper').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function exportContactRelatedCasesToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    newFormFilter.attr('action', getBaseURL() + 'export/contact_related_cases/' + jQuery('#contactIdFilter').val()).submit();
}
function relateToCaseLookup() {
    jQuery("#relatedCaseLookUp", '#relatedCasesForm').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'cases/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.subject,
                                value: item.subject,
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#relatedCaseId', '#relatedCasesForm').val(ui.item.record.id);
                jQuery('#btnAdd', '#relatedCasesForm').removeAttr('disabled');
            }
        }
    });
}
function addRelatedCase() {
    var newRelatedCase = jQuery('#relatedCaseId', '#relatedCasesForm').val();
    var contactId = jQuery('#contactIdFilter', '#contactRelatedCasesSearchFilters').val();
    jQuery.ajax({
        url: getBaseURL() + 'contacts/related_case_add',
        dataType: "json",
        type: "POST",
        data: {contactId: contactId, relatedCaseId: newRelatedCase},
        success: function (response) {
            var ty = 'error';
            var m = '';
            if(response.status){
                ty = 'success';
                m = _lang.feedback_messages.addedContactNewRelatedCaseSuccessfully.sprintf([contactId, newRelatedCase]);
            }else{
                switch (response.message) {
                    case 'save_error':	// could not save form
                        m = _lang.feedback_messages.addedNewRelatedCaseFailed;
                        break;
                    case 'contact_exists':	// could not save case already linked
                        m = _lang.feedback_messages.addedContactNewRelatedCaseFailedCaseExist.sprintf([contactId, newRelatedCase])
                        break;
                    default:
                        break;
                }
            }  
            pinesMessageV2({ty: ty, m: m});
            jQuery('#relatedCasesGrid').data('kendoGrid').dataSource.read();
            resetRElatedCasesForm();
        }, error: defaultAjaxJSONErrorsHandler
    });
}
function resetRElatedCasesForm() {
    jQuery('#relatedCaseId', '#relatedCasesForm').val('');
    jQuery('#relatedCaseLookUp', '#relatedCasesForm').val('');
    jQuery('#btnAdd', '#relatedCasesForm').attr('disabled', 'disabled');
}

function getContainersLinks(container_id, case_container_id){
    var containerId = container_id.split(",");
    var caseContainerId = case_container_id.split(",");
    var list = [];
    caseContainerId.forEach((element, index)=>{
        list.push('<a href="' + getBaseURL() + 'case_containers/edit/'+ element.trim() +'">'+ containerId[index] +'</a>');
    });
    return list.toString();
}