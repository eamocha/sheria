var gridDataSrc, gridOptions, grid;

function contractGridEvents() {
    grid = jQuery('#contracts-grid');
    contractGridInitialization();
    searchRelatedContracts();
    lookUpContracts({
        'lookupField': jQuery('#lookup-contract', '#related-contract-form'),
        'hiddenId': jQuery('#lookup-contract-id', '#related-contract-form'),
        'errorDiv': 'contract_id'
    }, '#related-contract-form');
}


function searchRelatedContracts() {
    if (undefined == grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        var gridGrid = grid.data('kendoGrid');
        return false;
    }
    grid.data('kendoGrid').dataSource.read();
    return false;
}

function getContractFormFilters() {
    var filtersForm = jQuery('#contractSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('contractSearchFilters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);

    return filters;
}

function contractGridInitialization() {
    gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('contract') + "contracts/related_contracts",
                dataType: "JSON",
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
                    options.filter = getContractFormFilters();
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
                    comments: {type: "string"},
                    name: {editable: false, type: "string"},
                    type: {editable: false, type: "string"},
                    assignee: {editable: false, type: "string"},
                    contract_date: {editable: false, type: "date"},
                    start_date: {editable: false, type: "date"},
                    end_date: {editable: false, type: "date"},
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
                field: 'id',
                sortable: !sqlsrv2008,
                headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                filterable: false,
                title: _lang.id,
                template: '<a href="' + getBaseURL('contract') + 'contracts/view/#= contract_b_id #">#= contractID #</a>',
                width: '120px'
            },
            {field: "comments", title: _lang.comment, width: '100px'},
            {
                field: 'name',
                title: _lang.contractName,
                template: '<a href="' + getBaseURL('contract') + 'contracts/view/#= contract_b_id #"><bdi>#= addslashes(name).replace("\\\\\\\'", "\\\'") #</bdi></a>',
                width: '220px'
            },
            {field: 'type', title: _lang.contractType, width: '170px'},
            {
                field: 'assignee',
                sortable: !sqlsrv2008,
                headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                title: _lang.assignee,
                template: '#= (assignee!=null && assignee_status=="Inactive")? assignee+" ("+_lang.custom[assignee_status]+")":((assignee!=null)?assignee:"") #',
                width: '192px'
            },
            {
                field: 'contract_date',
                format: "{0:yyyy-MM-dd}",
                title: _lang.contractDate,
                width: '140px',
            },
            {
                field: 'start_date',
                format: "{0:yyyy-MM-dd}",
                title: _lang.startDate,
                width: '140px',
            },
            {
                field: 'end_date',
                format: "{0:yyyy-MM-dd}",
                title: _lang.endDate,
                width: '140px',
            },
            {
                field: "actions",
                template: '<a href="javascript:;" onclick="relateContractDelete(#= recordId #)" title="' + _lang.deleteRow + '"><i class="fa-solid fa-trash-can red"></i></a>',
                sortable: false,
                title: _lang.actions,
                width: '65px'
            }

        ],
        editable: true,
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
        toolbar: [{name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}]
    };
}

function onSelectLookupCallback() {
    jQuery('#relation-button', '#related-contract-form').removeAttr('disabled');
}

function relateContractAdd() {
    var contractId = jQuery('#contract-id-filter', '#contractSearchFilters').val();
    var newRelatedContract = jQuery('#lookup-contract-id', '#related-contract-form').val();
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_contract_add',
        dataType: "json",
        type: "POST",
        data: {contract_id: contractId, related_contract_id: newRelatedContract},
        success: function (response) {
            pinesMessage({ty: response.result ? 'success' : 'error', m: response.display_message});
            if (response.result) {
                jQuery(grid).data('kendoGrid').dataSource.read();
                jQuery('#lookup-contract-id', '#related-contract-form').val('');
                jQuery('#lookup-contract', '#related-contract-form').val('');
                jQuery('#relation-button', '#related-contract-form').attr('disabled', 'disabled');
            }

        }, error: defaultAjaxJSONErrorsHandler
    });
}

function relateContractDelete(caseId) {
    confirmationDialog('confim_delete_action', {resultHandler: _relateContractDelete, parm: caseId});
}

function _relateContractDelete(id) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_contract_delete',
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