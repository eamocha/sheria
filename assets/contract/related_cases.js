var gridDataSrc, gridOptions, grid;

function caseGridEvents() {
    grid = jQuery('#related-cases-grid');
    caseGridInitialization();
    searchRelatedCases();
    relateCaseToContractLookup();
}


function searchRelatedCases() {
    if (undefined == grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        var gridGrid = grid.data('kendoGrid');
        return false;
    }
    grid.data('kendoGrid').dataSource.read();
    return false;
}

function getCaseFormFilters() {
    var filtersForm = jQuery('#caseSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('caseSearchFilters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);

    return filters;
}

function caseGridInitialization() {
    var contractId = jQuery('#contract-id-filter', '#caseSearchFilters').val();
    gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('contract') + "contracts/related_cases",
                dataType: "JSON",
                data: {contract_id: contractId},
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible')) jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids('contracts-grid');
                }
            },
            update: {
                url: getBaseURL('contract') + "contracts/related_contract_edit",
                dataType: "json",
                type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (response.result) {
                        pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    } else {
                        pinesMessage({ty: 'error', m: '<ul><li>' + response.validationErrors + '</li></ul>'});
                    }
                    jQuery(grid).data('kendoGrid').dataSource.read();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    options.filter = getCaseFormFilters();
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
                    caseId: {editable: false, type: "string"},
                    caseSubject: {editable: false, type: "string"},
                    category: {editable: false, type: "string"},
                    caseType: {editable: false, type: "string"},
                    casePriority: {editable: false, type: "string"},
                    requestedBy: {editable: false, type: "string"},
                    assignedTo: {editable: false, type: "string"},
                    caseStatus: {editable: false, type: "string"},
                    actions: {editable: false, type: "string"}
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
                        row['assignee'] = escapeHtml(row['assignee']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        batch: true, pageSize: 5, serverPaging: true, serverFiltering: true, serverSorting: true
    });
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {
                field: 'caseId',
                sortable: !sqlsrv2008,
                filterable: false,
                title: _lang.caseId,
                template: '<a href="' + getBaseURL() + 'cases/edit/#= id #">#= caseId #</a>',
                width: '120px'
            },

            {field: "subject", title: _lang.caseSubject, width: '220px'},
            {field: "category", title: _lang.category, width: '140px'},
            {field: "type", title: _lang.caseType, width: '170px'},
            {field: "casePriority", title: _lang.casePriority, width: '140px'},
            {field: "requested_by_name", title: _lang.requestedBy, width: '140px'},
            {
                field: "assignedTo",
                title: _lang.assignee,
                width: '192px',
                template: '#= (assignedTo!=null && assignedToStatus=="Inactive")? assignedTo+" ("+_lang.custom[assignedToStatus]+")":((assignedTo!=null)?assignedTo:"") #'
            },
            {field: "caseStatus", title: _lang.case_status, width: '140px'},
            {
                field: "actions",
                template: '<a href="javascript:;" onclick="relateCaseDelete(\'#= recordId #\')" title="' + _lang.deleteRow + '"><i class="fa-solid fa-trash-can red"></i></a>',
                sortable: false,
                title: _lang.actions,
                width: '65px'
            }
        ],
        editable: false,
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

    };
}


function relateCaseToContractLookup() {
    jQuery("#lookup-case", '#related-case-form').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.more_filters = {};
            request.more_filters.caseType = 'ExtractIP';
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
                jQuery('#lookup-case-id', '#related-case-form').val(ui.item.record.id);
                jQuery('#relation-button', '#related-case-form').removeAttr('disabled');
            }
        }
    });
}

function relateCaseAdd() {
    var contractId = jQuery('#contract-id-filter', '#caseSearchFilters').val();
    var newRelatedCase = jQuery('#lookup-case-id', '#related-case-form').val();
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_case_add',
        dataType: "json",
        type: "POST",
        data: {contract_id: contractId, related_case_id: newRelatedCase},
        success: function (response) {
            pinesMessage({ty: response.result ? 'success' : 'error', m: response.display_message});
            if (response.result) {
                jQuery(grid).data('kendoGrid').dataSource.read();
                jQuery('#lookup-case-id', '#related-case-form').val('');
                jQuery('#lookup-case', '#related-case-form').val('');
                jQuery('#relation-button', '#related-case-form').attr('disabled', 'disabled');
            }

        }, error: defaultAjaxJSONErrorsHandler
    });
}

function relateCaseDelete(caseId) {
    confirmationDialog('confim_delete_action', {resultHandler: _relateCaseDelete, parm: caseId});
}

function _relateCaseDelete(id) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_case_delete',
        dataType: "json",
        type: "POST",
        data: {
            recordId: id
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.relationRemovedSuccessfully});
                jQuery(grid).data('kendoGrid').dataSource.read();
            } else {
                pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
            }

        }, error: defaultAjaxJSONErrorsHandler
    });
}