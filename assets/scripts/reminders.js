var enableQuickSearch = true;
var remindersDataSrc, reminderGridOptions;

function loadEventsForFilters() {
    if (jQuery('.hijri-date-picker', '#remindDateContainer').length > 0) 
     makeFieldsHijriDatePicker({ fields: ['remindDateValue','remindDateEndValue','modifiedOnValue','modifiedOnEndValue','createdOnValue','createdOnEndValue'] });
    else
     makeFieldsDatePicker({fields: ['remindDateValue', 'remindDateEndValue','modifiedOnValue','modifiedOnEndValue','createdOnValue','createdOnEndValue']});
    userLookup('userValue');
    userLookup('createdByValue');
    userLookup('modifiedByValue');
    caseLookup(jQuery('#caseValue', $searchFiltersForm));
    lookUpContracts({
        'lookupField': jQuery('#contractLookup', $searchFiltersForm),
        'hiddenId': jQuery('#contract-value', $searchFiltersForm),
    }, $searchFiltersForm);
    taskLookup(jQuery('#taskValue', $searchFiltersForm));
    contactAutocompleteMultiOption('contactValue', '3', advancedSearchLookupFieldsResultHandler);
    companyAutocompleteMultiOption(jQuery('#companyValue', '#filtersFormWrapper'), advancedSearchLookupFieldsResultHandler);
}
function exportRemindersToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('searchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL() + 'export/reminders').submit();
}
function advancedSearchLookupFieldsResultHandler() {
    return true;
}
function reminderQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterValue', '#filtersFormWrapper').val(term);
        jQuery('#reminderGrid').data("kendoGrid").dataSource.page(1);
    }
}
function getFormFilters() {
    var filtersForm = jQuery('#searchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('searchFilters', '.', true);
    var filters = '';
    var reminderStatusDefault = jQuery('#quickSearchFilterStatusValue', '#filtersFormWrapper').val().length > 0;
    var quickSearchFilterValue = jQuery('#quickSearchFilterValue', '#filtersFormWrapper').val().length > 0;
    
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
        
        if(hijriCalendarEnabled == 1)
        {
            const DateHijriFieldsName = ["reminders.remindDate","reminders.createdOn","reminders.modifiedOn"];
            var FiltersRecords = filters.filters;

            FiltersRecords.forEach(function( filter ) {
                                
                filter['filters'].forEach( function( elm ) 
                {
                    if( jQuery.inArray( elm['field'], DateHijriFieldsName ) != -1 )
                        elm['value'] = hijriToGregorian(elm['value']);
                });

            });
        }
    } else if (quickSearchFilterValue || reminderStatusDefault) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
var $searchFiltersForm = null;
jQuery(document).ready(function () {
    gridInitialization();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery('#reminderGrid').data('kendoGrid').dataSource.read();
    });
    jQuery('.multi-select', '#searchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    $searchFiltersForm = jQuery('form#searchFilters');
    $searchFiltersForm.bind('submit', function (e) {
        e.preventDefault();
        jQuery('#reminderLookUp').val('');
        enableQuickSearch = false;
        jQuery('#reminderGrid').data("kendoGrid").dataSource.page(1);
    });
});
function gridInitialization() {
    var tableColumns = [];
    var savePageSize = false;
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({field: "id",
            filterable: false,
            template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="javascript:;" onclick="reminderForm(#= id #);">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="dismissReminder(#= id #);">' + _lang.dismiss + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="reminderForm(#= id #,\'postpone\');">' + _lang.postpone + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="checkReminderRecurrence(#= id #);">' + _lang.deleteRow + '</a>' +
                    '</div></div>',
            title: _lang.actions,
            width: '103px'});
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'remindDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", template: "#= (remindDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(remindDate) : remindDate, 'yyyy-MM-dd'))#", title: _lang.remindOn, width: '130px'};
            } else if (item === 'remindTime') {
                array_push = {field: item, template: '#= remindTime.substring(0, 5) #', title: _lang.time, width: '130px'};
            }
            else if (item === 'summary') {
                array_push = {field: item, template: '<a class="reminder-summary" title="#= summary #" href="javascript:;" onclick="reminderForm(#= id #);">#= (summary!=null&&summary!="") ? ((_lang.languageSettings[\'langDirection\'] === \'rtl\') ? "..." + summary.substring(0,40) : summary.substring(0,40) + "...") : "" #</a>', title: _lang.summary, width: '310px'};
            } else if (item === 'remindUser') {
                array_push = {field: item, title: _lang.remindUser, width: '180px', template: '#= (remindUser!=null && userStatus=="Inactive")? remindUser+" ("+_lang.custom[userStatus]+")":((remindUser!=null)?remindUser:"") #'};
            } else if (item === 'legal_case') {
                array_push = {field: item, title: _lang.relatedCase, width: '180px', template: '#= (legal_case!=null&&legal_case!="") ? (legal_case.length > 20 ? legal_case.substring(0,20)+"..." : legal_case) : "" #'};
            }
            else if (item === 'contract') {
                array_push = {field: item, title: _lang.relatedContract, width: '180px', template: '#= (contract!=null&&contract!="") ? (contract.length > 20 ? contract.substring(0,20)+"..." : contract) : "" #'};
            }
            else if (item === 'company') {
                array_push = {field: item, title: _lang.relatedCompany, width: '180px'};
            }
            else if (item === 'contact') {
                array_push = {field: item, title: _lang.relatedContact, width: '180px'};
            }
            else if (item === 'task') {
                array_push = {field: item, title: _lang.relatedTask, template: '#= (task!=null&&task!="") ? task.substring(0,50) : "" #', width: '200px'};
            }
            else if (item === 'createdByName') {
                array_push = {field: item, title: _lang.createdBy, width: '140px', template: '#= (createdByName!=null && createdByStatus=="Inactive")? createdByName+" ("+_lang.custom[createdByStatus]+")":((createdByName!=null)?createdByName:"") #'};
            }
            else if (item === 'createdOn') {
                array_push = {field: item, width: '194px', format: "{0:yyyy-MM-dd}", template: '#= (kendo.toString((hijriCalendarEnabled == 1 ? gregorianToHijri(createdOn) : createdOn), \'yyyy-MM-dd\')) #' + '</a>', title: _lang.createdOn};
            }
            else {
                array_push = {field: item, title: getTranslation(item), width: '180px'};
            }
            tableColumns.push(array_push);
        });
    }
    remindersDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "reminders/show_my_reminders",
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (XHRObj.responseText == 'access_denied') {
                        return false;
                    }
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if ($response.columns_html) {
                        jQuery('#column-picker-trigger-container').html($response.columns_html);
                        jQuery('*[data-callexport]').on('click', function () {
                            if(hasAccessToExport!=1){
                                pinesMessage({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                            }else {
                                if ($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportRemindersToExcel(true);
                                    } else {
                                        exportRemindersToExcel();
                                    }
                                } else {
                                    applyExportingModuleMethod(this);
                                }
                            }
                        });
                        gridEvents();
                        loadExportModalRanges($response.totalRows);
                    }
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    animateDropdownMenuInGrids('reminderGrid');
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    options.filter = getFormFilters();
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    options.sortData = JSON.stringify(remindersDataSrc.sort());
                    if (savePageSize) {
                        options.savePageSize = true;
                        savePageSize = false;
                    }
                }
                return options;
            }
        },
        schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {type: "number"},
                    reminderID: {type: "number"},
                    summary: {type: "string"},
                    type: {type: "string"},
                    status: {type: "string"},
                    legal_case: {type: "string"},
                    contact: {type: "string"},
                    company: {type: "string"},
                    task: {type: "string"},
                    remindDate: {type: "date"},
                    remindTime: {type: "string"},
                    remindUser: {type: "string"},
                    createdOn: {type: "date"},
                    createdByName: {type: "string"}
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
                        row['summary'] = escapeHtml(row['summary']);
                        row['legal_case'] = escapeHtml(row['legal_case']);
                        row['remindUser'] = escapeHtml(row['remindUser']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        sort: jQuery.parseJSON(gridSavedColumnsSorting || "null")
    });
    reminderGridOptions = {
        autobind: true,
        dataSource: remindersDataSrc,
        columns: tableColumns,
        dataBound: function () {
            jQuery('.reminder-summary').each(function (index, element) {
                jQuery(element).tooltip({
                    delay: {"show": 100, "hide": 100},
                    template: '<div class="tooltip" role="tooltip" style="z-index:6666"><div class="tooltip-arrow"></div><div class="tooltip-inner tooltip-cust"></div></div>',
                    container: 'body'
                });
            });
        },
        editable: false,
        filterable: false,
        height: 480,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
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
    gridTriggers({'gridContainer': jQuery('#reminderGrid'), 'gridOptions': reminderGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
}
function reminderCallBack(){
    jQuery('#reminderGrid').data("kendoGrid").dataSource.page(1);
    return true;
}
