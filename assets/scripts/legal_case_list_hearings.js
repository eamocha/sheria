var enableQuickSearch = false;
var savePageSize = true;
function gridInitialization() {
    var tableColumns = [];
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({
            field: "id",
            template:
                    '<input type="checkbox" class="hearing-bulk-update-checkbox #= nonVerifiedHearings == \'1\' ? \'\' : \'d-none\' #" name="hearingIds[]" id="hearingId_#= id #" value="#= id #" title="' + _lang.bulkHearingUpdateCheckboxTitle + '" />' +
                    '<div class="dropdown dropdown-action #= nonVerifiedHearings == \'1\' ? \'more\' : \'\' #">' + gridActionIconHTMLV2 + '<div class="dropdown-menu margin-minus-top" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item #= nonVerifiedHearings == \'1\' ? \'\' : \'d-none\' #" href="javascript:;"  onclick="verifyHearingWindow(\'#= id #\',\'#= legal_case_id #\')">' + _lang.verify + '</a>' +
                    '<a class="dropdown-item #= nonVerifiedHearings == \'1\' ? \'d-none\' : \'\' #"href="javascript:;"  onclick="legalCaseHearingForm(\'#= id #\',\'\',\'#= legal_case_id #\')">' + _lang.edit + '</a>' +
                    "#=(task_id != null && nonVerifiedHearings != 1) ? '<a class=\"dropdown-item\" target=\"_blank\" href=\"' + getBaseURL() + 'calendars/view/'+kendo.toString(startDate, \'yyyy-MM-dd\')+'\"   title=\"' + _lang.viewInCalendar + '\">' + _lang.viewInCalendar + '</a>':''#" +
                    '<a class="dropdown-item #= nonVerifiedHearings == \'1\' ? \'d-none\' : \'\' #"href="javascript:;" onclick="legalCaseHearingForm(\'#= id #\', \'clone\',\'#= legal_case_id #\')" title="' + _lang.clone + '">' + _lang.clone + '</a>' +
                    '<a class="dropdown-item #= (nonVerifiedHearings == \'1\' || (verificationProcessEnabled && verifiedSummary == \'0\') ? \'d-none\' : \'\') #"href="javascript:;" onclick="generateHearingReport(\'#= id #\')" title="' + _lang.generateHearingReport + '">' + _lang.generateHearingReport + '</a>' +
                    '<a class="dropdown-item #= nonVerifiedHearings == \'1\' ? \'d-none\' : \'\' #"href="cases/hearing_export_to_word/#= id #"  title="' + _lang.exportToWord + '">' + _lang.exportToWord + '</a>' +
                    '<a class="dropdown-item #= judged == \'yes\' || nonVerifiedHearings == \'1\' ? \'d-none\' : \'\' #"href="javascript:;" onClick="hearingSetJudgment(\'#= id #\',\'#= legal_case_id #\');"  title="' + _lang.setJudgment + '">' + _lang.setJudgment + '</a>' +
                    '<a class="dropdown-item #= nonVerifiedHearings == \'1\' ? \'d-none\' : \'\' #"href="javascript:;" onclick="recordRelatedExpense(\'#= legal_case_id #\',\'hearing\',\'#= id #\')" title="' + _lang.recordExpense + '">' + _lang.recordExpense + '</a>' +
                    '<a class="dropdown-item #= nonVerifiedHearings == \'1\' ? \'d-none\' : \'\' #"href="javascript:;" onclick="recordRelatedExpense(\'#= legal_case_id #\',\'hearing\',\'#= id #\', true)" title="' + _lang.recordBulkExpenses + '">' + _lang.recordBulkExpenses + '</a>' +
                    '<a class="dropdown-item #= nonVerifiedHearings == \'1\' ? \'d-none\' : \'\' #"href="javascript:;" onclick="deleteCaseHearing(\'#= id #\')" title="' + _lang.delete + '">' + _lang.delete + '</a>' +
                    '</div></div><img class="hearing-report-flag pull-right row-title" width="14" height="14" src="assets/images/icons/hearing-report-#= clientReportEmailSent > 0 ? \'\' : \'not-\' #sent.svg" title="#= displayReportFlagTitle(clientReportEmailSent) #">', title: ' ', filterable: false, sortable: false, width: '100px', attributes:{class: "flagged-gridcell"}});
        
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'startDate') {
                array_push = {field: "startDate", width: '194px', format: "{0:yyyy-MM-dd}", template: '<a href="javascript:;" onclick="legalCaseHearingForm(#= "\'" + id + "\'" #,\'\',#= "\'" + legal_case_id + "\'" #)">' + '#= (kendo.toString((hijriCalendarEnabled == 1 ? gregorianToHijri(startDate) : startDate), \'yyyy-MM-dd\') + ((startTime == null) ? \'\' : (\' \' + startTime.substring(0, 5)))) #' + '</a>', title: _lang.hearingDate};
            }
            else if (item === 'previousHearingDate') {
                array_push = {field: "previousHearingDate", width: '194px', format: "{0:yyyy-MM-dd}", template: "#= (previousHearingDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(previousHearingDate) : previousHearingDate, 'yyyy-MM-dd'))#", title: _lang.previousHearingDate};
            }else if (item === 'reference') {
                array_push = {field: "reference", width: '190px',  title: _lang.externalReferenceAndDate, template: '<span class="row-title" title="#= (reference!=null&&reference!="") ? displayCourtReferenceContent(reference, hijriCalendarEnabled == 1) : \'\' #">#= (reference!=null&&reference!="") ? displayCourtReferenceContent(reference, hijriCalendarEnabled == 1) : \'\' #</span>'};
            }else if (item === 'areaOfPractice') {
                array_push = {field: "areaOfPractice", width: '190px', title: _lang.caseType};
            } else if (item === 'judged') {
                array_push = {field: "judged", width: '190px', template: "#= getTranslation(judged) #", title: _lang.judgedQuestion};
            } else if (item === 'judgment_name') {
                array_push = {field: "judgment_name", sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, width: '190px', title: _lang.judgment};
            } else if (item === 'caseID') {
                array_push = {field: "caseID", width: '300px', template: '<a href="' + getBaseURL() + 'cases/edit/#= legal_case_id #">#= caseID #</a>', title: _lang.relatedLitigationCase};
            } else if (item === 'caseSubject') {
                array_push = {field: "caseSubject", width: '300px', template: '<a class="case-full-subject-title" href="' + getBaseURL() + 'cases/edit/#= legal_case_id #" title="#= displayContent(fullCaseSubject) #">#= caseSubject #</a>', title: _lang.litigationCaseSubject};
            } else if (item === 'clients') {
                array_push = {encoded: false, width: '192px', field: "clients", title: _lang.client, template: "#= (clients == null) ? '' : clients #"};
            } else if (item === 'clientPosition') {
                array_push = {field: "clientPosition", sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, width: '150px', title: _lang.clientPosition_Case};
            }
            else if (item === 'opponents') {
                array_push = {encoded: false, width: '192px', field: "opponents", title: _lang.opponents_Case, template: '<span class="row-title" title="#= (opponents!=null&&opponents!="") ? displayContent(opponents) : \'\' #">#= (opponents!=null&&opponents!="") ? opponents : \'\' #</span>'};
            }
            else if (item === 'lawyers') {
                array_push = {encoded: false, width: '192px', field: "lawyers", title: _lang.lawyers_for_hearing, template: '<span class="row-title" title="#= (lawyers!=null&&lawyers!="") ? displayContent(lawyers) : \'\' #">#= (lawyers!=null&&lawyers!="") ? lawyers : \'\' #</span>'};
            }
            else if (item === 'court') {
                array_push = {field: "court", width: '192px', title: _lang.court};
            }
            else if (item === 'courtDegree') {
                array_push = {field: "courtDegree", width: '192px', title: _lang.courtDegree};
            }
            else if (item === 'stage_name') {//renamed to court rank
                array_push = {field: "stage_name", width: '192px', title: _lang.courtRank, template: "#= stage_name && stage_id ? '<a href=\"' + getBaseURL() + 'cases/events/' + legal_case_id + '?stage=stage-' + stage_id + '-container'+ '\">' + stage_name + '</a>' : '' #"};
            }
            else if (item === 'type_name') {
                array_push = {field: "type_name", width: '192px', title: _lang.CourtActivityPurpose};
            }else if (item === 'client_foreign_name') {
                array_push = {field: item, title: _lang.clientForeignName, width: '192px'};
            }
            else if (item === 'opponent_foreign_name') {
                array_push = {field: item, title: _lang.opponentForeignName, width: '192px', template: '<span class="row-title" title="#= (opponent_foreign_name!=null&&opponent_foreign_name!="") ? displayContent(opponent_foreign_name) : \'\' #">#= (opponent_foreign_name!=null&&opponent_foreign_name!="") ? opponent_foreign_name : \'\' #</span>'};
            } else if(item === 'summary'){
                array_push = {field: item, title: _lang.summaryByLawyer, width: '300px', template: '<a class="row-title" href="javascript:;" onClick="fillHearingSummary(\'#= id #\');" title="#= _lang.fillHearingSummary #"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;<span class="row-title" title="#= (summary!=null&&summary!="") ? displayContent(summary) : \'\' #">#= (summary!=null&&summary!="") ? summary : \'\' #</span>'};
            }else if(item === 'summaryToClient'){
                array_push = {field: item, title: _lang.summaryToClient, width: '300px', template: '<span class="row-title" title="#= (summaryToClient!=null&&summaryToClient!="") ? displayContent(summaryToClient) : \'\' #">#= (summaryToClient!=null&&summaryToClient!="") ? summaryToClient : \'\' #</span>'};
            }
             else if(item === 'latest_development'){
                array_push = {field: item, title: _lang.latest_development, width: '300px', template: '<span class="row-title" title="#= (latest_development!=null&&latest_development!="") ? displayContent(latest_development) : \'\' #">#= (latest_development!=null&&latest_development!="") ? latest_development : \'\' #</span>'};
            }else if(item === 'comments'){
                array_push = {field: item, title: getTranslation(item), width: '182px', template: '<span class="row-title" title="#= (comments!=null&&comments!="") ? displayContent(comments) : \'\' #">#= (comments!=null&&comments!="") ? comments : \'\' #</span>'};
            }else if(item === 'reasons_of_postponement'){
                array_push = {field: item, title: getTranslation(item), width: '182px', template: '<span class="row-title" title="#= (reasons_of_postponement!=null&&reasons_of_postponement!="") ? displayContent(reasons_of_postponement) : \'\' #">#= (reasons_of_postponement!=null&&reasons_of_postponement!="") ? reasons_of_postponement : \'\' #</span>'};
            }else if(item === 'judgment'){
                array_push = {field: item, title: getTranslation(item), width: '182px', template: '<span class="row-title" title="#= (judgment!=null&&judgment!="") ? displayContent(judgment) : \'\' #">#= (judgment!=null&&judgment!="") ? judgment : \'\' #</span>'};
            }else if(item === 'createdOn'){
                array_push = {field: item, width: '194px', format: "{0:yyyy-MM-dd}", template: '#= (kendo.toString((hijriCalendarEnabled == 1 ? gregorianToHijri(createdOn) : createdOn), \'yyyy-MM-dd\')) #' + '</a>', title: _lang.createdOn};
            }else if(item === 'createdByName'){
                array_push = {field: "createdByName", title: _lang.createdBy, width: '182px'};
            }else if(item === 'caseReference'){ // internal ref nb of the case
                array_push = {field: item, title: _lang.internalReference, width: '182px'};
            }else if(item === 'day'){
                array_push = {field: item, template: "#= getTranslation(kendo.toString(startDate, 'dddd')) #", title: _lang.day, width: '120px', filterable: false, sortable: false, reorderable: true};
            }else if(item === 'verifiedSummary'){
                array_push = {field: item, template: "#= verifiedSummary > 0 ? _lang.yes : _lang.no #", title: _lang.verified, width: '120px', filterable: false, sortable: false, reorderable: true};
            }else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    var gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {dataType: "JSON", type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if ($response.result != undefined && !$response.result) {
                        if ($response.gridDetails != undefined) {
                            setGridDetails($response.gridDetails);
                        }
                        if ($response.feedbackMessage != undefined) {
                            pinesMessageV2({ty: $response.feedbackMessage.ty, m: $response.feedbackMessage.m});
                        } else {
                            pinesMessageV2({ty: 'error', m: _lang.updatesFailed});
                        }
                    }
                    if ($response.columns_html) {
                        jQuery('#column-picker-trigger-container').html($response.columns_html);
                        jQuery('*[data-callexport]').on('click', function () {
                            if (hasAccessToExport != 1) {
                                pinesMessageV2({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                            } else {
                                if ($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportHearingsToExcel(true);
                                    } else {
                                        exportHearingsToExcel();
                                    }
                                } else {
                                        applyExportingModuleMethod(this);
                                    }
                                }
                        });
                        gridEvents();
                        loadExportModalRanges($response.totalRows);
                        if (gridAdvancedSearchLinkState) {
                            gridAdvancedSearchLinkState = false;
                        }
                        animateDropdownMenuInGridsV2('hearingsGrid', 200);
                    }
                    if(jQuery('#gridFiltersList').val() !== 'non_verified_hearings'){ // hide bulk update for other filters
                        jQuery('#bulk-update-summary-container').addClass('d-none');
                        jQuery('.hearing-bulk-update-checkbox').addClass('d-none');
                        jQuery('#select-all-hearing-checkboxes').addClass('d-none');
                        jQuery('.dropdown', jQuery('.k-grid-content', '#hearingsGrid')).removeClass('more');
                    }else{
                        jQuery('#bulk-update-summary-container').removeClass('d-none');
                        jQuery('.hearing-bulk-update-checkbox').removeClass('d-none');
                        if ($response.totalRows != undefined && $response.totalRows > 0) {
                            jQuery('#select-all-hearing-checkboxes').removeClass('d-none').attr('checked', false);
                        }else{
                            jQuery('#select-all-hearing-checkboxes').addClass('d-none');
                        }
                        jQuery('.dropdown', jQuery('.k-grid-content', '#hearingsGrid')).addClass('more');
                    }
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    options.loadWithSavedFilters = 0;
                    options.action = 'readHearings';
                    if (gridSavedFiltersParams) {
                        options.filter = gridSavedFiltersParams;
                        var gridFormData = [];
                        gridFormData.formData = ["gridFilters"];
                        gridFormData.formData.gridFilters = gridSavedFiltersParams;
                        setGridFiltersData(gridFormData, 'hearingsGrid');
                        options.loadWithSavedFilters = 1;
                        options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                        gridSavedFiltersParams = '';
                    } else {
                        options.sortData = JSON.stringify(gridDataSrc.sort());
                        options.filter = checkWhichTypeOfFilterIUseAndReturnFilters();
                    }
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                }
                return options;
            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {id: "id", fields: {
                    id: {editable: false, type: "integer"},
                    legal_case_id: {editable: false, type: "integer"},
                    startDate: {type: "date"},
                    startTime: {type: "string"},
                    reference: {type: "string"},
                    caseSubject: {type: "string"},
                    subject: {type: "string"},
                    opponents: {type: "string"},
                    lawyers: {type: "string"},
                    court: {type: "string"},
                    clients: {type: "string"},
                    clientPosition: {type: "string"},
                    judgment_name: {type: "string"},
                    judged: {type: "string"},
                    createdOn: {type: "date"}
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
                        row['clients'] = escapeHtml(row['clients']);
                        row['caseSubject'] = escapeHtml(row['caseSubject']);
                        row['fullCaseSubject'] = escapeHtml(row['fullCaseSubject']);
                        row['opponents'] = escapeHtml(row['opponents']);
                        row['lawyers'] = escapeHtml(row['lawyers']);
                        row['clients'] = escapeHtml(row['clients']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        batch: true,
        page: 1, pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        sort: jQuery.parseJSON(gridSavedColumnsSorting || "null")
    });
    var gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnResize: function (e) {
            fixFooterPosition();
        },
        columnReorder: function (e) {
            orderColumns(e);
        },
        columns: tableColumns,
        editable: false,
        filterable: false,
        height: 500,
        pageable: {messages: _lang.kendo_grid_pageable_messages, pageSizes: [10, 20, 50, 100], refresh: true, buttonCount:5},
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
        dataBound: function () {
            jQuery('.case-full-subject-title').each(function (index, element) {
                jQuery(element).tooltip({
                    content: function () {
                        return this.getAttribute("title");
                    },
                    delay: {"show": 100, "hide": 100},
                    template: '<div class="tooltip" role="tooltip" style="z-index:6666"><div class="tooltip-arrow"></div><div class="tooltip-inner tooltip-cust"></div></div>',
                    container: 'body'
                });
            });
            jQuery('.row-title').each(function (index, element) {
                jQuery(element).tooltip({
                    content: function () {
                        return this.getAttribute("title");
                    },
                    delay: {"show": 100, "hide": 100},
                    template: '<div class="tooltip" role="tooltip" style="z-index:6666"><div class="tooltip-arrow"></div><div class="tooltip-inner tooltip-cust"></div></div>',
                    container: 'body'
                });
            });
            displayColHeaderPlaceholder();
        },
    };
    gridTriggers({'gridContainer': jQuery('#hearingsGrid'), 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    if(nonVerifiedHearings == 1){
        var grid = jQuery('#hearingsGrid').data('kendoGrid');
        grid.thead.find("th:first").append(jQuery('<input id="select-all-hearing-checkboxes" class="selectAll" type="checkbox" style="margin-left: 12px;" title="' + _lang.selectAllRecords + '" onclick="checkUncheckAllHearingCheckboxes(this);" />'));
        grid.thead.find("[data-field=actionsCol]>.k-header-column-menu").remove();
    }
}
function displayReportFlagTitle(clientReportEmailSent){
    return clientReportEmailSent > 0 ? _lang.hearingSentReportFlagTitle.sprintf([clientReportEmailSent]) : _lang.hearingNotSentReportFlagTitle;
}
function checkUncheckAllHearingCheckboxes(statusChkBx) {
    jQuery("tbody" + " INPUT[type='checkbox']").attr('checked', statusChkBx.checked);
}
function bulkUpdateSummaryToClient(){
    confirmationDialog('bulk_update_summary_to_client_confirmation', {
        resultHandler: function (){
            jQuery.ajax({
                url: getBaseURL() + 'cases/hearing_bulk_update_summary_to_client',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    gridData: form2js('gridFormContent', '.', true)
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                success: function (response) {
                    if (response.result) {
                        if(response.empty_summary == 0){
                            pinesMessageV2({ty: 'information', m: _lang.feedback_messages.updatesSavedSuccessfully});
                        }else{
                            pinesMessageV2({ty: 'information', m: response.summary_msg});
                        }
                    }else{
                        if(response.empty_rows){
                            pinesMessageV2({ty: 'warning', m: response.empty_rows});
                        }else{
                            pinesMessageV2({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                        }
                    }
                    jQuery('#hearingsGrid').data('kendoGrid').dataSource.read();
                },
                complete: function () {
                    jQuery('#loader-global').hide();
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    });
}
function hearingsQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchSummaryValue', '#hearingsSearchFilters').val(term);
        jQuery('#quickSearchReferenceValue', '#hearingsSearchFilters').val(term);
        jQuery('#quickSearchSubjectValue', '#hearingsSearchFilters').val(term);
        jQuery('#quickSearchMatterSubjectValue', '#hearingsSearchFilters').val(term);
        jQuery('#hearingsGrid').data("kendoGrid").dataSource.page(1);
    }
}
function checkWhichTypeOfFilterIUseAndReturnFilters() {
    // remove fixed assigee from quick search when the saved filter is not "my hearings"
    if(jQuery('#gridFiltersList').val() === 'my_hearings'){
        jQuery('input', '#quick-search-assignee-container').each(function (){
           jQuery(this).prop('disabled', false); 
        });
    }else{
        jQuery('input', '#quick-search-assignee-container').each(function (){
           jQuery(this).prop('disabled', true); 
        });
    }
    var filtersForm = jQuery('#hearingsSearchFilters');
    // disableEmpty(filtersForm);
    disableUnCheckedFilters();
    var searchFilters = form2js('hearingsSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchSummaryValue', '#hearingsSearchFilters').val() || jQuery('#quickSearchLawyersValue', '#hearingsSearchFilters').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function loadEventsForFilters() {
    caseLookup(jQuery('#legal_case_idValue'));
    contactAutocompleteMultiOption('judgesValue', '3', functionReturnTRUE);
    contactAutocompleteMultiOption('opponentLawyersValue', '3', functionReturnTRUE);
    userLookup('createdByNameValue');
    userLookup('modifiedByNameValue');
    jQuery('#opponents_enOpertator', '#hearingsSearchFilters').change(function () {
        jQuery('#opponents_enValue', '#hearingsSearchFilters').val('');
    });
    jQuery('#opponents_enValue', '#hearingsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#opponents_enOpertator', '#hearingsSearchFilters').val();
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
                                    label: _lang.no_results_matched,
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
    jQuery('#opponent_foreign_name_enValue', '#hearingsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#opponent_foreign_name_enOpertator', '#hearingsSearchFilters').val();
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
                                    label: _lang.no_results_matched,
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
    jQuery('#opponent_foreign_name_enOpertator', '#hearingsSearchFilters').change(function () {
        jQuery('#opponent_foreign_name_enValue', '#hearingsSearchFilters').val('');
    });
    jQuery('#clientsOpertator', '#hearingsSearchFilters').change(function () {
        jQuery('#clientsValue', '#hearingsSearchFilters').val('');
    });
    jQuery('#clientsValue', '#hearingsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#clientsOpertator', '#hearingsSearchFilters').val();
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
                                    label: _lang.no_results_matched,
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
    jQuery('#clientForeignNameOpertator', '#hearingsSearchFilters').change(function () {
        jQuery('#clientForeignNameValue', '#hearingsSearchFilters').val('');
    });
    jQuery('#clientForeignNameValue', '#hearingsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#clientForeignNameOpertator', '#hearingsSearchFilters').val();
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
}
function caseHearingLookup($lookupField, isHearingForm, $container) {
    $lookupField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.more_filters = {};
            request.more_filters.category = 'Litigation';
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
                                label: item.id + ': ' + item.subject,
                                value: item.id,
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
                if (isHearingForm) {
                    jQuery('#legal_case_id', $container).val(ui.item.record.id);
                    var caseSubjectSelected = ui.item.record.fullSubject;
                    var caseSubjectToDisplay = caseSubjectSelected;
                    if (caseSubjectSelected.length >= 55) {
                        caseSubjectToDisplay = caseSubjectToDisplay.substring(0, 55) + '...';
                    }
                    jQuery('#caseSubject', $container).text(caseSubjectToDisplay);
                    jQuery('#caseSubjectLinkId', $container).removeClass('d-none').attr('href', 'cases/edit/' + ui.item.record.id);
                    fetchCaseRelatedDataToHearingFrom(ui.item.record.id);
                }
            }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });

}
function opponentsLookUp(fieldObject, isFormField) {
    fieldObject = fieldObject || jQuery('#lookupHearingOpponents');
    isFormField = isFormField || false;
    fieldObject.autocomplete({
        autoFocus: false,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'opponents/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: isFormField ? _lang.no_results_matched_add.sprintf([request.term]) : _lang.no_results_matched.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.name,
                                value: isFormField ? '' : item.name,
                                record: item
                            };
                        }));
                    }
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            if (isFormField) {
                if (ui.item.record.id > 0) {
                    setNewCaseMultiOption(jQuery('#selected_hearing_opponents'), {id: ui.item.record.id, value: ui.item.record.name, name: 'Hearing_Opponents'});
                } else if (ui.item.record.id == -1) {
                    quickAddNewOpponent(ui.item.record.term);
                }
            }
        }
    });
}
function clientsLookUp(field, isFormField) {
    field = field || jQuery('#lookupHearingClients');
    isFormField = isFormField || false;
    field.autocomplete({
        autoFocus: false,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL('money') + 'clients/autocomplete',
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
                                label: item.name,
                                value: isFormField ? '' : item.name,
                                record: item
                            };
                        }));
                    }
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0 && isFormField) {
                setNewCaseMultiOption(jQuery('#selected_hearing_clients'), {id: ui.item.record.id, value: ui.item.record.name, name: 'Hearing_Clients'});
            }
        }
    });
}
function functionReturnTRUE() {
    return true;
}
function exportHearingsToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('hearingsSearchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL() + 'export/list_hearings').submit();
}
function searchHearings() {
    var hearingsGridId = jQuery('#hearingsGrid');
    if (undefined == hearingsGridId.data('kendoGrid')) {
        hearingsGridId.kendoGrid(hearingsGridOptions);
        //fixGridHeader();
        return false;
    }
    hearingsGridId.data('kendoGrid').dataSource.read();
    return false;
}
var hearingsGridOptions = {};
jQuery(document).ready(function () {
    var hearingsGridId = jQuery('#hearingsGrid');
    gridFiltersEvents('Legal_Case_Hearing', 'hearingsGrid', 'hearingsSearchFilters');
    if(typeof gridHasDefaultFilter !== 'undefined' && !gridHasDefaultFilter){
        jQuery('.grid-fixed-filter', '#hearingsSearchFilters').val(assignee_fixed_filter);
    }
    if(hearingsGridId.length){
        gridInitialization();
        jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
            hearingsGridId.data('kendoGrid').dataSource.read();
        });
    }
    jQuery('.multi-select', '#hearingsSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
//    makeFieldsDatePicker({fields: ['startDateValue', 'startDateEndValue', 'postponedDateValue', 'postponedDateEndValue','modifiedOnValue','modifiedOnEndValue','createdOnValue','createdOnEndValue']});
//    makeFieldsHijriDatePicker({fields: ['start-date-hijri-filter', 'start-date-end-hijri-filter', 'postponed-date-hijri-filter', 'postponed-date-end-hijri-filter']});
    jQuery('#hearingsSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        jQuery('#eventsLookUp').val('');
        if (jQuery('#submitAndSaveFilter').is(':visible')) {
            gridAdvancedSearchLinkState = true;
        }
        enableQuickSearch = false;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        if(hearingsGridId.length) hearingsGridId.data('kendoGrid').dataSource.page(1);
        //hideAdvancedSearch();
    });
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
    loadEventsForFilters();
});
function deleteCaseHearing(id) {
    if (confirm(_lang.confirmationOfDeletingHearing)) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/delete_case_hearing/' + id,
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                if (response.result) {
                    if (jQuery('#hearingsGrid').length) {
                        jQuery('#hearingsGrid').data("kendoGrid").dataSource.read();
                    }
                    loadUserLatestReminders('refresh');
                    pinesMessageV2({ty: 'success', m: _lang.feedback_messages.hearingDeletedSuccessfully});
                } else {
                    pinesMessageV2({ty: 'information', m: _lang.feedback_messages.deleteHearingFailed});
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });

    }
}
function generateHearingReport(id, callback){
    callback = callback || false;
    jQuery.ajax({
        url: getBaseURL() + 'cases/generate_hearing_summary_report/' + id,
        dataType: 'JSON',
        type: 'GET',
        data: {action: 'list'},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery(".document-generation-container").length <= 0) {
                    jQuery('<div class="d-none document-generation-container"></div>').appendTo("body");
                    var documentGenerationContainer = jQuery('.document-generation-container');
                    documentGenerationContainer.html(response.html).removeClass('d-none');
                    jQuery('.modal', documentGenerationContainer).modal({
                        keyboard: true,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery('#templates', documentGenerationContainer).selectpicker().change(function () {
                        if (this.value !== '') {
                            jQuery.ajax({
                                url: getBaseURL() + 'cases/generate_hearing_summary_report/' + id,
                                dataType: 'JSON',
                                type: 'GET',
                                data: {template_id: this.value, action: 'read'},
                                beforeSend: function () {
                                    jQuery('#loader-global').show();
                                },
                                success: function (response) {
                                    if (response.html) {
                                        jQuery('#template-fields-variables', documentGenerationContainer).html(response.html).removeClass('d-none');
                                        
                                    } else {
                                        jQuery('#template-fields-variables', documentGenerationContainer).html('').addClass('d-none');
                                    }
                                    jQuery('#generate', documentGenerationContainer).removeClass('d-none').unbind().click(function () {
                                        generateReportSubmit(documentGenerationContainer, id, callback);
                                    });
                                    jQuery('#send-report', documentGenerationContainer).removeClass('d-none').unbind().click(function () {
                                        generateReportSendToClient(documentGenerationContainer, id, callback);
                                    });
                                }, complete: function () {
                                    jQuery('#loader-global').hide();
                                },
                                error: defaultAjaxJSONErrorsHandler
                            });
                        } else {
                            jQuery('#template-fields-variables', documentGenerationContainer).html('').addClass('d-none');
                            jQuery('#generate', documentGenerationContainer).addClass('d-none');
                            jQuery('#send-report', documentGenerationContainer).addClass('d-none');
                        }
                    });
                    jQuery('.modal', documentGenerationContainer).on('hidden.bs.modal', function () {
                        destroyModal(documentGenerationContainer);
                    });
                }
            } else {
                pinesMessageV2({ty: 'error', m: response.error});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function generateReportSendToClient(container, id, callback) {
    callback = callback || false;
    jQuery('.loader-submit', container).addClass('loading');
    jQuery('#generate', container).attr('disabled', 'disabled');
    jQuery('#send-report', container).attr('disabled', 'disabled');
    var fileName = jQuery('#doc-name-preffix').val() + jQuery('#doc-name-suffix').text();
    jQuery('#doc-name').val(fileName);
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/hearing_send_report_to_client/' + id,
        success: function (response) {
            if (response.result) {
                // open popup to fill the email content before sending it to client
                jQuery.ajax({
                    dataType: 'JSON',
                    type: 'POST',
                    url: getBaseURL() + 'cases/hearing_prepare_report_to_client/' + id,
                    success: function (response) {
                        if (response.html) {
                            jQuery('.loader-submit', container).removeClass('loading');
                            jQuery('#generate', container).removeAttr('disabled');
                            jQuery('#send-report', container).removeAttr('disabled');
                            // open popup to fill the email content before sending it to client
                            if (jQuery(".send-email-container").length <= 0) {
                                jQuery('<div class="d-none send-email-container"></div>').appendTo("body");
                                var sendEmailContainer = jQuery('.send-email-container');
                                sendEmailContainer.html(response.html).removeClass('d-none');
                                jQuery('.modal', sendEmailContainer).modal({
                                    keyboard: true,
                                    show: true,
                                    backdrop: 'static'
                                });
                                jQuery('.modal', sendEmailContainer).on('hidden.bs.modal', function () {
                                    destroyModal(sendEmailContainer);
                                });
                                jQuery('#form-send-submit').click(function (){
                                    if(!jQuery('#email-to').val() || !jQuery('#email-subject').val() || !jQuery('#email-message').val()){
                                        pinesMessageV2({ty: 'warning', m: _lang.feedback_messages.requiredFormFields});
                                    }else{
                                        var emailData = jQuery("form", sendEmailContainer).serialize();
                                        jQuery.ajax({
                                            data: emailData,
                                            dataType: 'JSON',
                                            type: 'POST',
                                            url: getBaseURL() + 'cases/hearing_submit_report_to_client/' + id,
                                            beforeSend: function () {
                                                jQuery('.loader-submit', sendEmailContainer).addClass('loading');
                                                jQuery('#form-send-submit').attr('disabled', 'disabled');
                                            },
                                            success: function (response) {
                                                if (response.result) {
                                                    jQuery('.modal', sendEmailContainer).modal('hide');
                                                    jQuery('.modal', container).modal('hide');
                                                    pinesMessageV2({ty: 'success', m: response.msg});
                                                    if(jQuery("#hearingsGrid").length) jQuery('#hearingsGrid').data('kendoGrid').dataSource.read();
                                                    if(callback && isFunction(callback)) callback();
                                                } else {
                                                    pinesMessageV2({ty: 'error', m: response.msg});
                                                }
                                            }, complete: function () {
                                                jQuery('.loader-submit', sendEmailContainer).removeClass('loading');
                                                jQuery('#form-send-submit').removeAttr('disabled');
                                            },
                                            error: defaultAjaxJSONErrorsHandler
                                        });
                                    }
                                });
                            }
                        } else {
                            pinesMessageV2({ty: 'error', m: response.msg});
                        }
                    },
                    error: defaultAjaxJSONErrorsHandler
                });
            } else {
                pinesMessageV2({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function generateReportSubmit(container, id, callback) {
    callback = callback || false;
    var fileName = jQuery('#doc-name-preffix').val() + jQuery('#doc-name-suffix').text();
    jQuery('#doc-name').val(fileName);
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('#generate', container).attr('disabled', 'disabled');
            jQuery('#send-report', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/generate_hearing_summary_report/' + id,
        success: function (response) {
            if (response.result) {
                window.location = getBaseURL() + 'cases/download_hearing_report/' + fileName;
                pinesMessageV2({ty: 'success', m: response.msg});
                jQuery('.modal', container).modal('hide');
                if(callback && isFunction(callback)) callback();
            } else {
                pinesMessageV2({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            }
        }, complete: function () {
            jQuery('#generate', container).removeAttr('disabled');
            jQuery('#send-report', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function changeLookUp(value) {
    if (value == 'lookUp') {
        caseLookup(jQuery('#legal_case_idValue'));
        jQuery('#legal_case_idValue').addClass('lookup');
        jQuery("#legal_case_idValue").val('');
        jQuery('#legal_case_idValue').attr('title', _lang.startTyping);
        jQuery('#legal_case_idValue').attr('placeholder', _lang.startTyping);
        jQuery("#lookup_type").val('legal_case_id');
    } else {
        if (jQuery("#legal_case_idValue").hasClass('ui-autocomplete-input')) {
            jQuery("#legal_case_idValue").autocomplete("destroy");
            jQuery("#legal_case_idValue").val('');
            jQuery('#legal_case_idValue').removeClass('ui-autocomplete-input lookup');
            jQuery('#legal_case_idValue').removeAttr('title');
            jQuery('#legal_case_idValue').removeAttr('placeholder');
            jQuery("#lookup_type").val('caseSubject');
        }
    }
}