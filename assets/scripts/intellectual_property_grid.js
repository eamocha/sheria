var gridId = 'intellectualPropertyGrid';
var intellectualPropertyGrid = null;
var enableQuickSearch = false;
notificationsNoteTemplate = '', authIdLoggedIn = '';
var intellectualPropertiesGridOptions, intellectualPropertiesDataSrc;
function intellectualPropertyQuickSearch(keyCode, term) {
    if (keyCode == 13) {
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterSubjectValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilterDescriptionValue', '#filtersFormWrapper').val(term);
        intellectualPropertyGrid.data("kendoGrid").dataSource.page(1);
    }
}
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['registrationDateValue', 'registrationDateEndValue', 'renewalExpiryDateValue', 'renewalExpiryDateEndValue', 'renewalDateValue', 'renewalDateEndValue','modifiedOnValue','modifiedOnEndValue','createdOnValue','createdOnEndValue']});
    userLookup('legalCaseAssigneeValue');
    userLookup('createdByValue');
    userLookup('modifiedByValue');
    jQuery('#clientTypeOpertator', '#intellectualPropertySearchFilters').change(function () {
        jQuery('#clientValue', '#intellectualPropertySearchFilters').val('');
    });
    jQuery('#clientValue', '#intellectualPropertySearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#clientTypeOpertator', '#intellectualPropertySearchFilters').val();
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
                                    label: _lang.no_results_matched_for.sprintf([request.term]),
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
    jQuery('#agentTypeOpertator', '#intellectualPropertySearchFilters').change(function () {
        jQuery('#agentValue', '#intellectualPropertySearchFilters').val('');
    });
    jQuery('#agentValue', '#intellectualPropertySearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#agentTypeOpertator', '#intellectualPropertySearchFilters').val();
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
                                    label: _lang.no_results_matched_for.sprintf([request.term]),
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
function exportIpToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('intellectualPropertySearchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL() + 'export/intellectual_property').submit();
}
function checkWhichTypeOfFilterIUseAndReturnFilters() {
    var filtersForm = jQuery('#intellectualPropertySearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('intellectualPropertySearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
        filters.customFields = searchFilters.customFields;
    } else if (jQuery('#quickSearchFilterSubjectValue', '#intellectualPropertySearchFilters').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
jQuery(document).ready(function () {
    $intellectualPropertyGrid = jQuery('#intellectualPropertyGrid');
    gridFiltersEvents('IP', 'intellectualPropertyGrid', 'intellectualPropertySearchFilters');
    gridInitialization();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        $intellectualPropertyGrid.data('kendoGrid').dataSource.read();
    });
    jQuery('.multi-select', '#intellectualPropertySearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    if (document.location.href.substr(document.location.href.length - 5, 5) == 'print') {
        window.open(jQuery('#printAnchor').attr('href'));
        document.location = document.location.href.substr(0, document.location.href.length - 6);
    }
    try {
        setTimeout(function () {
            if (licenseHasExpired) {
                jQuery(':submit').click(function () {
                    alertLicenseExpirationMsg();
                    return false;
                });
            }
        }, 200);
        intellectualPropertyGrid = jQuery('#' + gridId);
        if (intellectualPropertyGrid.length > 0) {
            jQuery('#intellectualPropertySearchFilters').bind('submit', function (e) {
                jQuery("form#intellectualPropertySearchFilters").validationEngine({
                    validationEventTrigger: "submit",
                    autoPositionUpdate: true,
                    promptPosition: 'bottomRight',
                    scroll: false
                });
                if (!jQuery('form#intellectualPropertySearchFilters').validationEngine("validate")) {
                    return false;
                }
                e.preventDefault();
                jQuery('#intellectualPropertyLookUp').val('');
                document.getElementsByName("page").value = 1;
                document.getElementsByName("skip").value = 0;
                enableQuickSearch = false;
                intellectualPropertyGrid.data('kendoGrid').dataSource.page(1);
            });
        }
    } catch (e) {
    }
});
function gridInitialization() {
    var tableColumns = [];
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({field: 'id', title: ' ', filterable: false, sortable: false, template: '<div class="dropdown">' + gridActionIconHTML + '<civ class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'intellectual_properties/edit/#= id #">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'intellectual_properties/export_to_word/#= id #">' + _lang.exportToWord + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="confirmationDialog(\'confirm_delete_record\', {resultHandler: deleteIPRecord, parm: \'#= id #\'});">' + _lang.delete + '</a>' +
                    '</div></div>', width: '70px'
        });
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'id') {
                array_push = {field: item, title: _lang.id, template: '<a href="' + getBaseURL() + 'intellectual_properties/edit/#= id #">#= id #</a>', width: '100px'};
            } else if (item === 'subject') {
                array_push = {field: item, title: _lang.subject, template: '<a href="' + getBaseURL() + 'intellectual_properties/edit/#= id #"><bdi>#= subject #</bdi></a>', width: '195px'};
            } else if (item === 'intellectualPropertyRight') {
                array_push = {field: item, title: _lang.intellectual_property_right, width: '195px'};
            } else if (item === 'ipClass') {
                array_push = {field: item, title: _lang.ip_class, width: '195px'};
            } else if (item === 'ipSubcategory') {
                array_push = {field: item, title: _lang.ip_subcategory, width: '195px'};
            } else if (item === 'ipName') {
                array_push = {field: item, title: _lang.ip_name, width: '175px'};
            }
            else if (item === 'ipStatus') {
                array_push = {field: item, title: _lang.ip_status, width: '195px'};
            }
            else if (item === 'statusComments') {
                array_push = {field: item, title: _lang.status_comments, width: '195px'};
            }
            else if (item === 'description') {
                array_push = {field: item, title: _lang.description, template: '#= description.length > 50 ? description.substring(0, 50) + "..." : description #', width: '300px'};
            }
            else if (item === 'legalCaseAssignee') {
                array_push = {field: item, title: _lang.assigneeCaseMatter, width: '192px', template: '#= (legalCaseAssignee!=null && legalCaseAssigneeStatus=="Inactive")? legalCaseAssignee+" ("+_lang.custom[legalCaseAssigneeStatus]+")":((legalCaseAssignee!=null)?legalCaseAssignee:"") #', width: '192px'};
            }
            else if (item === 'providerGroup') {
                array_push = {field: item, title: _lang.providerGroup, width: '150px'};
            }
            else if (item === 'countryName') {
                array_push = {field: item, title: _lang.country, width: '175px'};
            }
            else if (item === 'client') {
                array_push = {field: item, title: _lang.clientOwner, width: '175px'};
            }
            else if (item === 'renewalExpiryDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.renewalExpiryDate, width: '175px', template: "#= (renewalExpiryDate == null) ? '' : kendo.toString(renewalExpiryDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'renewalDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.renewalDate, width: '158px', template: "#= (renewalDate == null) ? '' : kendo.toString(renewalDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'renewalUsersToRemind') {
                array_push = {field: item, title: _lang.usersToRemind, width: '175px'};
            }
            else if (item === 'arrivalDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.filedOn, width: '158px', template: "#= (arrivalDate == null) ? '' : kendo.toString(arrivalDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'filingNumber') {
                array_push = {field: item, title: _lang.filingNb, width: '195px'};
            }
            else if (item === 'acceptanceRejection') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", format: "{0:yyyy-MM-dd}", title: _lang.acceptanceRejection, width: '195px', template: "#= (acceptanceRejection == null) ? '' : kendo.toString(acceptanceRejection, 'yyyy-MM-dd') #"};
            }
            else if (item === 'registrationDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.registrationDate, width: '158px', template: "#= (registrationDate == null) ? '' : kendo.toString(registrationDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'createdOn') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '158px', template: "#= (createdOn == null) ? '' : kendo.toString(createdOn, 'yyyy-MM-dd') #"};
            } 
            else if (item === 'createdByName') {
                array_push = {field: 'createdByName', title: _lang.createdBy, width: '175px'};
            }
            else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    try {
        intellectualPropertiesDataSrc = new kendo.data.DataSource({
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
                                }else {
                                    if ($response.totalRows <= 10000) {
                                        if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                            exportIpToExcel(true);
                                        } else {
                                            exportIpToExcel();
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
                        animateDropdownMenuInGrids('intellectualPropertyGrid');
                    }, beforeSend: function () {
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
                            setGridFiltersData(gridFormData, 'intellectualPropertyGrid');
                            options.loadWithSavedFilters = 1;
                            options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                            gridSavedFiltersParams = '';
                        } else {
                            options.filter = checkWhichTypeOfFilterIUseAndReturnFilters();
                        }
                        options.sortData = JSON.stringify(intellectualPropertiesDataSrc.sort());
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
                        id: {type: "string"},
                        subject: {type: "string"},
                        intellectualPropertyRight: {type: "string"},
                        ipClass: {type: "string"},
                        ipSubcategory: {type: "string"},
                        ipName: {type: "string"},
                        ipStatus: {type: "string"},
                        statusComments: {type: "string"},
                        legalCaseAssignee: {type: "string"},
                        providerGroup: {type: "string"},
                        countryName: {type: "string"},
                        client: {type: "string"},
                        renewalExpiryDate: {type: "date"},
                        renewalDate: {type: "date"},
                        renewalUsersToRemind: {type: "string"},
                        agent: {type: "string"},
                        registrationReference: {type: "string"},
                        arrivalDate: {type: "date"},
                        filingNumber: {type: "string"},
                        acceptanceRejection: {type: "date"},
                        registrationDate: {type: "date"},
                        certificationNumber: {type: "string"},
                        createdByName: {type: "string"},
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
                            row['subject'] = escapeHtml(row['subject']);
                            row['acceptanceRejection'] = escapeHtml(row['acceptanceRejection']);
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
        intellectualPropertiesGridOptions = {
            autobind: true,
            dataSource: intellectualPropertiesDataSrc,
            columns: tableColumns,
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
                    name: "toolbar-menu",
                    template: '<div></div>'
                }],
            columnResize: function (e) {
                fixFooterPosition();
                resizeHeaderAndFooter();
            },
            columnReorder: function (e) {
                orderColumns(e);
            }
        };
    } catch (e) {
    }
    gridTriggers({'gridContainer': $intellectualPropertyGrid, 'gridOptions': intellectualPropertiesGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
}
