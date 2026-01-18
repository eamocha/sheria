var gridId = 'awaitingApprovalsGrid';
var awaitingApprovalsGrid = null;
var enableQuickSearch = false, customFieldsNames;
authIdLoggedIn = '';
var awaitingApprovalsSearchDataSrc, contractsSearchGridOptions;
jQuery(document).ready(function () {
    awaitingApprovalsGrid = jQuery('#' + gridId);
    try {
        if (awaitingApprovalsGrid.length > 0) {
            gridFiltersEvents('awaiting_approvals', 'awaitingApprovalsGrid', 'awaiting-approvals-filters');
            if(!gridHasDefaultFilter){
                jQuery('#approver_usersValue', '#awaiting-approvals-filters').val(approverUser);
                jQuery('#approver_user_groupsValue', '#awaiting-approvals-filters').val(approverUserGroup);
                jQuery('#requester_managerValue', '#awaiting-approvals-filters').val(approverUser);
            }
            gridInitialization();
            jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
                awaitingApprovalsGrid.data('kendoGrid').dataSource.read();
            });
            jQuery('.multi-select', '#awaiting-approvals-filters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();

            jQuery('#awaiting-approvals-filters').bind('submit', function (e) {
                jQuery("form#awaiting-approvals-filters").validationEngine({
                    validationEventTrigger: "submit",
                    autoPositionUpdate: true,
                    promptPosition: 'bottomRight',
                    scroll: false
                });
                if (!jQuery('#awaiting-approvals-filters').validationEngine("validate")) {
                    return false;
                }
                e.preventDefault();
                jQuery('#contract-lookup').val('');
                if (jQuery('#submitAndSaveFilter').is(':visible')) {
                    gridAdvancedSearchLinkState = true;
                }
                enableQuickSearch = false;
                document.getElementsByName("page").value = 1;
                document.getElementsByName("skip").value = 0;
                awaitingApprovalsGrid.data('kendoGrid').dataSource.page(1);
            });
        }
    } catch (e) {
    }
});


function contractQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterValue', '#filtersFormWrapper').val(term);
        awaitingApprovalsGrid.data("kendoGrid").dataSource.page(1);
    }
}

function checkWhichTypeOfFilterIUseAndReturnFilters() {
    var filtersForm = jQuery('#awaiting-approvals-filters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('awaiting-approvals-filters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterValue', '#awaiting-approvals-filters').val()) {
        filters = searchFilters.quickSearch;
    }
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}

function gridInitialization() {
    var tableColumns = [];
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({
            field: 'id', title: ' ', filterable: false, sortable: false, template:
                '<input type="checkbox" name="contract-ids" value="#= id #" title="' + _lang.archiveCheckboxTitle + '" onchange="checkUncheckCheckboxes(this,false)" />' +
                '<div class="dropdown more">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                '<a class="dropdown-item" href="' + getBaseURL('contract') + 'contracts/view/#= id #">' + _lang.viewEdit + '</a>' +
                '<a class="dropdown-item" href="javascript:;" id = "archive-unarchive-btn-#= id #" name ="#= archived #" onclick="toolsActionsContract(\'#= id #\',\'archive-unarchive-btn-#= id #\');">#= (archived == \'no\') ? \''+_lang.archive+'\' : \''+_lang.unarchive+'\'  # </a>' +
                '<a class="dropdown-item" href="javascript:;" onclick="confirmationDialog(\'confim_delete_action\', {resultHandler: contractDelete, parm: {\'id\': \'#= id #\'}});">' + _lang.delete + '</a>' +
                '</div></div>', width: '70px', attributes: {class: "flagged-gridcell"}
        });
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'id') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    filterable: false,
                    title: _lang.id,
                    template: '<a href="' + getBaseURL('contract') + 'contracts/view/#= id #">#= contract_id #</a>',
                    width: '120px'
                };
            } else if (item === 'name') {
                array_push = {
                    field: item,
                    title: _lang.contractName,
                    template: '<a href="' + getBaseURL('contract') + 'contracts/view/#= id #"><bdi>#= addslashes(name).replace("\\\\\\\'", "\\\'") #</bdi></a>',
                    width: '220px'
                };
            } else if (item === 'assignee') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    title: _lang.assignee,
                    template: '#= (assignee!=null && assignee_status=="Inactive")? assignee+" ("+_lang.custom[assignee_status]+")":((assignee!=null)?assignee:"") #',
                    width: '192px'
                };
            } else if (item === 'requester') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    title: _lang.requester,
                    template: '#= (requester!=null && requester_status=="Inactive")? requester+" ("+_lang.custom[requester_status]+")":((requester!=null)?requester:"") #',
                    width: '192px'
                };
            } else if (item === 'contract_date') {
                array_push = {
                    field: item,
                    format: "{0:yyyy-MM-dd}",
                    title: _lang.contractDate,
                    width: '140px',
                };
            } else if (item === 'start_date') {
                array_push = {
                    field: item,
                    format: "{0:yyyy-MM-dd}",
                    title: _lang.startDate,
                    width: '140px',
                };
            } else if (item === 'end_date') {
                array_push = {
                    field: item,
                    format: "{0:yyyy-MM-dd}",
                    title: _lang.endDate,
                    width: '140px',
                };
            } else if (item === 'parties') {
                array_push = {
                    encoded: false,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    field: item,
                    title: _lang.parties,
                    width: '192px',
                    template: "#= (parties == null) ? '' : parties #"
                };
            } else if (item === 'approvers') {
                array_push = {
                    encoded: false,
                    sortable: false,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    field: item,
                    title: _lang.approvers,
                    width: '192px',
                    template: "#= (approvers == null) ? '' : approvers #"
                };
            }else if (item === 'type') {
                array_push = {field: item, title: _lang.contractType, width: '170px'};
            } else if (item === 'status') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    title: _lang.contractStatus,
                    width: '182px'
                };
            } else if (item === 'assigned_team') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    title: _lang.providerGroup,
                    width: '182px'
                };
            } else if (item === 'reference_number') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    title: _lang.internalReferenceNb,
                    width: '182px'
                };
            } else if (item === 'value') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    title: _lang.contractValue,
                    width: '182px'
                };
            } else if (item === 'createdByName') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    title: _lang.createdBy,
                    width: '182px'
                };
            } else if (item === 'modifiedByName') {
                array_push = {
                    field: item,
                    sortable: !sqlsrv2008,
                    headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                    title: _lang.modifiedBy,
                    width: '182px'
                };
            }

            else if (item === 'archived') {
                array_push = {
                  field: item,
                  sortable: !sqlsrv2008,
                  headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                  title: _lang.archived,
                  width: '109px'
                };
            } else if (item === 'description') {
                array_push = {
                    field: item, 
                    title: _lang.description, 
                    template: '#= getContractDesciption(description)#', 
                    width: '320px'};
            }

             else if (item.startsWith('custom_field_')) { //check if the item is a custom field then get the title name from a defined array
                array_push = {
                    field: item,
                    title: customFieldsNames[item],
                    width: '140px',
                    template: '#= ' + item + '!==null ? (' + item + '.length>255 ? ' + item + '.substring(0, 255) + "..." :' + item + ' ):"" #'
                };

            } else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    awaitingApprovalsSearchDataSrc = new kendo.data.DataSource({
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
                    if ($response.columns_html) {
                        jQuery('#column-picker-trigger-container').html($response.columns_html);
                        jQuery('*[data-callexport]').on('click', function () {
                            if (hasAccessToExport != 1) {
                                pinesMessage({
                                    ty: 'warning',
                                    m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page
                                });
                            } else {
                                if ($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportAwaitingApprovalsToExcel(true);
                                    } else {
                                        exportAwaitingApprovalsToExcel();
                                    }
                                } else {
                                    applyExportingModuleMethod(this);
                                }
                            }
                        });
                        gridEvents();
                        loadExportModalRanges($response.totalRows);
                    }
                    if (gridAdvancedSearchLinkState) {
                        gridAdvancedSearchLinkState = false;
                    } else {
                        if (jQuery('#filtersFormWrapper').is(':visible')) {
                            jQuery('#filtersFormWrapper').slideUp();
                            scrollToId('#filtersFormWrapper');
                        }
                    }
                    jQuery('#select-all-checkboxes').attr('checked', false);
                    animateDropdownMenuInGrids('awaitingApprovalsGrid');
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
                        setGridFiltersData(gridFormData, 'awaitingApprovalsGrid');
                        options.loadWithSavedFilters = 1;
                        options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                        gridSavedFiltersParams = '';
                    } else {
                        options.filter = checkWhichTypeOfFilterIUseAndReturnFilters();
                    }
                    options.sortData = JSON.stringify(awaitingApprovalsSearchDataSrc.sort());
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
                    contract_id: {type: "string"},
                    name: {type: "string"},
                    status: {type: "string"},
                    type: {type: "string"},
                    assigned_team: {type: "string"},
                    assignee: {type: "string"},
                    description: {type: "string"},
                    date: {type: "date"},
                    start_date: {type: "date"},
                    end_date: {type: "date"},
                    reference_number: {type: "string"},
                    value: {type: "string"},
                    parties: {type: "string"},
                    createdByName: {type: "string"},
                    modifiedByName: {type: "string"},
                    archived: {type: "string"}
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
                        row['name'] = escapeHtml(row['name']);
                        row['parties'] = escapeHtml(row['parties']);
                        row['description'] = escapeHtml(row['description']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        sort: jQuery.parseJSON(gridSavedColumnsSorting || "null")
    });
    awaitingApprovalsSearchGridOptions = {
        autobind: true,
        dataSource: awaitingApprovalsSearchDataSrc,
        columns: tableColumns,
        editable: false,
        filterable: false,
        height: 500,
        pageable: {
            input: true,
            messages: _lang.kendo_grid_pageable_messages,
            numeric: false,
            pageSizes: [10, 20, 50, 100],
            refresh: true
        },
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
        columnResize: function () {
            fixFooterPosition();
            resizeHeaderAndFooter();
        },
        columnReorder: function (e) {
            orderColumns(e);
        }
    };
    if (awaitingApprovalsGrid.length > 0) {
        gridTriggers({
            'gridContainer': awaitingApprovalsGrid,
            'gridOptions': awaitingApprovalsSearchGridOptions,
            'gridColumnsLength': Object.keys(tableColumns).length
        });
        var grid = awaitingApprovalsGrid.data('kendoGrid');
        grid.thead.find("th:first").append(jQuery('<input id="select-all-checkboxes" class="selectAll" type="checkbox" title="' + _lang.selectAllRecords + '" onclick="checkUncheckCheckboxes(this,\'all\');" />'));
        grid.thead.find("[data-field=actionsCol]>.k-header-column-menu").remove();
    }
    displayColHeaderPlaceholder();
}

function contractDelete(params) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/delete/',
        data: Array.isArray(params)?{'id': params}:{'id': params.id},
        type: 'post',
        dataType: 'json',
        success: function (response) {
            ty = 'error';
            if (response.result) {
                ty = 'success';
                jQuery('#' + gridId).data("kendoGrid").dataSource.read();
            }
            pinesMessage({ty: ty, m: response.display_message});
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['dateValue', 'dateEndValue', 'start_dateValue', 'start_dateEndValue', 'end_dateValue', 'end_dateEndValue']});
    userLookup('assigneeValue');
    jQuery('#partyTypeOpertator', '#awaiting-approvals-filters').change(function () {
        jQuery('#partyNameValue', '#awaiting-approvals-filters').val('');
    });
    jQuery('#partyNameValue', '#awaiting-approvals-filters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#partyTypeOpertator', '#awaiting-approvals-filters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'companies' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                label: _lang.no_results_matched_add.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                        value: item.name,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
}

function exportAwaitingApprovalsToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('awaiting-approvals-filters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL('contract') + 'export/awaiting_approvals').submit();
}