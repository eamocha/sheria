var gridId = 'containerGrid';
var casesGrid = null;
var enableQuickSearch = false;
authIdLoggedIn = '';
var casesSearchDataSrc, casesSearchGridOptions;
function caseQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterValue', '#filtersFormWrapper').val(term);
        casesGrid.data("kendoGrid").dataSource.page(1);
    }
}
function exportCasesToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('containerFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL() + 'export/cases_containers').submit();
}
function checkWhichTypeOfFilterIUseAndReturnFilters() {
    var filtersForm = jQuery('#containerFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('containerFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterValue', '#containerFilters').val()) {
        filters = searchFilters.quickSearch;
    }
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
jQuery(document).ready(function () {
    casesGrid = jQuery('#' + gridId);
    jQuery('.multi-select', '#containerFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    $legalCaseGrid = jQuery('#containerGrid');
    newCaseFormDialog = jQuery('#' + newCaseFormDialogId);
    try {
        if (casesGrid.length > 0) {
            gridFiltersEvents('Matter_Container', 'containerGrid', 'containerFilters');
            gridInitialization();
            jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
                $legalCaseGrid.data('kendoGrid').dataSource.read();
            });
            jQuery('#containerFilters').bind('submit', function (e) {
                jQuery("form#containerFilters").validationEngine({
                    validationEventTrigger: "submit",
                    autoPositionUpdate: true,
                    promptPosition: 'bottomRight',
                    scroll: false
                });
                if (!jQuery('#containerFilters').validationEngine("validate")) {
                    return false;
                }
                e.preventDefault();
                jQuery('#caseLookUp').val('');
                if (jQuery('#submitAndSaveFilter').is(':visible')) {
                    gridAdvancedSearchLinkState = true;
                }
                enableQuickSearch = false;
                document.getElementsByName("page").value = 1;
                document.getElementsByName("skip").value = 0;
                casesGrid.data('kendoGrid').dataSource.page(1);
            });
        }
    } catch (e) {
    }
});
function gridInitialization() {
    var tableColumns = [];
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({field: 'id', title: ' ', filterable: false, sortable: false, template:
                    '#= visible_in_cp == "1" ? \'<span class="flag-green"></span>\' : \'\'#' +
                    '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'case_containers/edit/#= id #">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="deleteCaseContainer(\'#= id #\');">' + _lang.delete + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="showHideContainerInCp(\'#= id #\');">#= visible_in_cp == 1 ? _lang.hideInCustomerPortal : _lang.showInCustomerPortal #</a>' +
                    '</div></div>', width: '70px', attributes:{class: "flagged-gridcell"}
        });
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'id') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, filterable: false, title: _lang.id, template: '<a href="' + getBaseURL() + 'case_containers/edit/#= id #">#= containerId #</a>', width: '120px'};
            } else if (item === 'containerSubject') {
                array_push = {field: item, title: _lang.subject_Case, template: '<a href="' + getBaseURL() + 'case_containers/edit/#= id #"><bdi>#= addslashes(containerSubject).replace("\\\\\\\'", "\\\'") #</bdi></a>', width: '220px'};
            } else if (item === 'assignee') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.assigneeCaseMatter, template: '#= (assignee!=null && userStatus=="Inactive")? assignee+" ("+_lang.custom[userStatus]+")":((assignee!=null)?assignee:"") #', width: '192px'};
            } else if (item === 'caseArrivalDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.arrival_date, width: '140px', template: "#= (caseArrivalDate == null) ? '' : kendo.toString(caseArrivalDate, 'yyyy-MM-dd') #"};
            }else if (item === 'caseClientPosition') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.clientPosition_Case, width: '140px'};
            }else if (item === 'opponentNames') {
                array_push = {encoded: false, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, field: item, title: _lang.opponents, width: '192px', template: "#= (opponentNames == null) ? '' : opponentNames #"};
            }else if (item === 'type') {
                array_push = {field: item, title: _lang.caseType, width: '170px'};
            }
            else if (item === 'clientName') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.clientName_Case, width: '175px'};
            }
            else if (item === 'requested_by_name') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.requestedBy, width: '175px'};
            }
            else if (item === 'client_foreign_name') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.clientForeignName, width: '175px'};
            }
            else if (item === 'opponent_foreign_name') {
                array_push = {field: item, title: _lang.opponentForeignName, width: '170px'};
            }
            else if (item === 'status') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'providerGroup') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'internalReference') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            } else if (item === 'containerClosedOn') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.closedOn, width: '140px', template: "#= (containerClosedOn == null) ? '' : kendo.toString(containerClosedOn, 'yyyy-MM-dd') #"};
            } else if (item === 'containerComments') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.comments, template: '#= (containerComments!=null&&containerComments!="") ? ((containerComments.length>40)? containerComments.substring(0,40)+"..." : containerComments) : ""#', width: '320px'};
            } else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    casesSearchDataSrc = new kendo.data.DataSource({
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
                            if(hasAccessToExport!=1){
                                pinesMessage({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                            }else{
                                if($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportCasesToExcel(true);
                                    } else {
                                        exportCasesToExcel();
                                    }
                                }else {
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
                    animateDropdownMenuInGrids('containerGrid');
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
                        setGridFiltersData(gridFormData, 'containerGrid');
                        options.loadWithSavedFilters = 1;
                        options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                        gridSavedFiltersParams = '';
                    } else {
                        options.filter = checkWhichTypeOfFilterIUseAndReturnFilters();
                    }
                    options.sortData = JSON.stringify(casesSearchDataSrc.sort());
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
                    id: {type: "integer"},
                    status: {type: "string"},
                    type: {type: "string"},
                    providerGroup: {type: "string"},
                    assignee: {type: "string"},
                    subject: {type: "string"},
                    caseArrivalDate: {type: "date"},
                    clientName: {type: "string"},
                    internalReference: {type: "string"}
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
                        row['containerSubject'] = escapeHtml(row['containerSubject']);
                        row['opponentNames'] = escapeHtml(row['opponentNames']);
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
    casesSearchGridOptions = {
        autobind: true,
        dataSource: casesSearchDataSrc,
        columns: tableColumns,
        editable: false,
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
        columnResize: function () {
            fixFooterPosition();
            resizeHeaderAndFooter();
        },
        columnReorder: function (e) {
            orderColumns(e);
        }
    };
    gridTriggers({'gridContainer': casesGrid, 'gridOptions': casesSearchGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    displayColHeaderPlaceholder();
}
function deleteCaseContainer(containerId) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'case_containers/delete/',
            data: {'id': containerId},
            type: 'post',
            dataType: 'json',
            success: function (response) {
                // 101 => deleted successfully
                if (response.status == 102) {// delete failed
                    pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
                } else if (response.status == 103) { // not able to delete "must delete related cases"
                    pinesMessage({ty: 'error', m: _lang.deleteCaseContainerNotAllowed});
                }else{
                    pinesMessage({ty: 'information', m: _lang.deleteRecordSuccessfull});
                }
                jQuery('#' + gridId).data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['caseArrivalDateValue', 'caseArrivalDateEndValue', 'closedOnValue', 'closedOnEndValue']});
    userLookup('assigneeValue');
    jQuery('#clientTypeOpertator', '#containerFilters').change(function () {
        jQuery('#clientNameValue', '#containerFilters').val('');
    });
    jQuery('#foreignClientTypeOpertator', '#containerFilters').change(function () {
        jQuery('#clientForeignNameValue', '#containerFilters').val('');
    });
    contactAutocompleteMultiOption('requested-by-value', '3', resultHandlerAfterContactsLegalCaseContainersAutocomplete);
    jQuery('#clientNameValue', '#containerFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#clientTypeOpertator', '#containerFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
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

    jQuery('#clientForeignNameValue', '#containerFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#foreignClientTypeOpertator', '#containerFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.noMatchesFound,
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
                                        label: item.contactForeignName,
                                        value: item.contactForeignName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: item.foreignName,
                                        value: item.foreignName,
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

    jQuery('#opponentTypeOpertator', '#containerFilters').change(function () {
        jQuery('#opponentNameValue', '#containerFilters').val('');
    });
    jQuery('#foreignOpponentTypeOpertator', '#containerFilters').change(function () {
        jQuery('#opponentForeignNameValue', '#containerFilters').val('');
    });
    jQuery('#opponentNameValue', '#containerFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#opponentTypeOpertator', '#containerFilters').val();
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
    jQuery('#opponentForeignNameValue', '#containerFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#foreignOpponentTypeOpertator', '#containerFilters').val();
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
                                       label: item.contactForeignName,
                                       value: item.contactForeignName,
                                       record: item
                                   }
                               } else if (lookupType == 'companies') {
                                   return {
                                       label: item.foreignName,
                                       value: item.foreignName,
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