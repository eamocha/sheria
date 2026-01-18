var enableQuickSearch = true;
jQuery(document).ready(function () {
    relateToCaseLookup();
    var companyRelatedCasesDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "companies/related_cases/" + jQuery('#companyIdFilter').val(),
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
                id: "id",
                fields: {
                    id: {editable: false, type: "number"},
                    company_id: {type: "string"},
                    caseId: {type: "string"},
                    subject: {type: "string"},
                    caseStatus: {type: "string"},
                    caseType: {type: "string"},
                    priority: {type: "string"},
                    arrivalDate: {type: "date"},
                    dueDate: {type: "date"},
                    providerGroup: {type: "string"},
                    assignee: {type: "string"},
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
    var companyRelatedCasesGridOptions = {
        autobind: true,
        dataSource: companyRelatedCasesDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "caseId", title: _lang.caseId, template: "<a href=" + getBaseURL() + "#= category == 'IP' ? 'intellectual_properties' : 'cases' #/edit/#= case_id #>#= caseId #</a>", width: '120px'},
            {field: "subject", title: _lang.caseSubject, width: '220px'},
            {field: "caseStatus", title: _lang.workflow_status, width: '120px'},
            {field: "category", title: _lang.type, width: '130px', template: "#= category == 'Matter' ? '" + _lang.corporateMatter + "' : (category == 'Litigation' ? '" + _lang.litigation + "' : '" + _lang.IP + "') #"},
            {field: "providerGroup", title: _lang.providerGroup, width: '150px'},
            {field: "assignee", title: _lang.assignee, width: '170px', template: '#= (assignee!=null && assignToStatus=="Inactive")? assignee+" ("+_lang.custom[assignToStatus]+")":((assignee!=null)?assignee:"") #'},
            {field: "caseType", title: _lang.caseType, width: '181px'},
            {field: "case_container_id", title: _lang.caseContainerID, template: '#=  getContainersLinks((container_id != null ? container_id : "") , (case_container_id != null ? case_container_id : "")) #' , width: '160px'},
            {field: "priority", title: _lang.priority, width: '90px'},
            {field: "arrivalDate", format: "{0:yyyy-MM-dd}", title: _lang.filedOn, width: '174px', template: "#= (arrivalDate == null) ? '' : kendo.toString(arrivalDate, 'yyyy-MM-dd') #"},
            {field: "dueDate", format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '163px', template: "#= (dueDate == null) ? '' : kendo.toString(dueDate, 'yyyy-MM-dd') #"},
            {field: "actions", template: '<div class="wraper-actions-non"><div class="list-of-actions"><a href="javascript:;" onclick="deleteSelectedRow(\'#= id #\')"><i class="fa fa-fw fa-trash"></i></a></div></div>', sortable: false, title: _lang.actions, width: '65px'}
        ],
        toolbar: [{name: "quick-search", template: ' <div class="col-md-4 no-padding"><div class="input-group col-md-8"><input type="text" class="form-control search quick-search-filter" placeholder=" ' + _lang.searchCase + '" name="caseLookUp" id="caseLookUp" onkeyup="caseQuickSearch(event.keyCode, this.value);" title="' + _lang.searchCase + '" /></div></div>'
            }],
        editable: false,
        filterable: false,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: false,buttonCount:5
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
        relatedCasesGrid.kendoGrid(companyRelatedCasesGridOptions);
        return false;
    }
    relatedCasesGrid.data('kendoGrid').dataSource.read();
    return false;

    var lastScrollLeft = 0;
    jQuery(".k-grid-content").scroll(function() {
        var documentScrollLeft = jQuery(".k-grid-content").scrollLeft();
        if (lastScrollLeft != documentScrollLeft) {
            lastScrollLeft = documentScrollLeft;
            jQuery(".wraper-actions").css("right",-lastScrollLeft)

            if(isLayoutRTL())
                jQuery(".wraper-actions").css("left",lastScrollLeft)

        }
    });
});
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
    var filtersForm = jQuery('#companyRelatedCasesSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('companyRelatedCasesSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterCaseSubjectValue', '#filtersFormWrapper').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function exportCompanyRelatedCasesToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('companyRelatedCasesSearchFilters')));
    newFormFilter.attr('action', getBaseURL() + 'export/company_related_cases/' + jQuery('#companyIdFilter').val()).submit();
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
    var companyId = jQuery('#companyIdFilter', '#companyRelatedCasesSearchFilters').val();
    jQuery.ajax({
        url: getBaseURL() + 'companies/related_case_add',
        dataType: "json",
        type: "POST",
        data: {companyId: companyId, relatedCaseId: newRelatedCase},
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.status) {
                case 202:	// saved successfuly
                    ty = 'success';
                    m = _lang.feedback_messages.addedCompanyNewRelatedCaseSuccessfully.sprintf([companyId, newRelatedCase]);
                    break;
                case 101:	// could not save form
                    m = _lang.feedback_messages.addedNewRelatedCaseFailed;
                    break;
                case 102:	// could not save case self
                    m = _lang.feedback_messages.addedNewRelatedCaseFailedCompanySelf.sprintf([companyId, newRelatedCase])
                    break;
                case 103:	// could not save case already linked
                    m = _lang.feedback_messages.addedCompanyNewRelatedCaseFailedCaseExist.sprintf([companyId, newRelatedCase])
                    break;
                default:
                    break;
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
function deleteSelectedRow(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'companies/related_case_delete',
            dataType: "json",
            type: "POST",
            data: {
                recordId: id
            },
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// remove successfuly
                        ty = 'success';
                        m = _lang.relationRemovedSuccessfully;
                        break;
                    case 101:	// could not delete record
                        m = _lang.deleteRecordFailed;
                        break;
                    default:
                        break;
                }
                pinesMessageV2({ty: ty, m: m});
                jQuery('#relatedCasesGrid').data('kendoGrid').dataSource.read();
            }
        });
    }
}

function getContainersLinks(container_id, case_container_id){
    var containerId = container_id.split(",");
    var caseContainerId = case_container_id.split(",");
    var list = [];
    caseContainerId.forEach((element, index)=>{
        list.push('<a href="' + getBaseURL() + 'case_containers/edit/'+ element.trim() +'">'+ (containerId[index] != null ? containerId[index]: "" ) +'</a>');
    });

    return list.toString();
}
