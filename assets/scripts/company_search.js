var gridId = 'companyGrid';
var enableQuickSearch = false;
var categoryViewSelected = true;
var companiesSearchGridOptions, companiesSearchDataSrc;
jQuery(document).ready(function () {
    loadEventsForFilters();
    gridFiltersEvents('Company', 'companyGrid', 'companySearchFilters');
    gridInitialization();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery('#companyGrid').data('kendoGrid').dataSource.read();
    });
    jQuery('#resetBtnHiddenV2', '#companySearchFilters').on('click', function () {
        jQuery(".form-group-filter").show();
        jQuery('#companyGrid').data('kendoGrid').dataSource.read();
    });
    jQuery('.multi-select', '#companySearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    try {
        jQuery('#companySearchFilters').bind('submit', function (e) {
            jQuery("form#companySearchFilters").validationEngine({
                validationEventTrigger: "submit",
                autoPositionUpdate: true,
                promptPosition: 'bottomRight',
                scroll: false
            });
            if (!jQuery('form#companySearchFilters').validationEngine("validate")) {
                return false;
            }
            e.preventDefault();
            jQuery('#companyLookUp').val('');
            enableQuickSearch = false;
            if (jQuery('#submitAndSaveFilter').is(':visible')) {
                gridAdvancedSearchLinkState = true;
            }
            jQuery('#companyGrid').data("kendoGrid").dataSource.page(1);
        });
    } catch (e) {
    }
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
function getCategoryTemplate(company_category_id, category){
    return "<span style='color: " + contactCompanyCategoryColors[company_category_id] + "'>" + _lang.contactCompanyCategories[category] + "</span>";
}
function companyQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterCompanyValue', '#filtersForm').val(term);
        jQuery('#quickSearchFilterCompanyValue2', '#filtersForm').val(term);
        jQuery('#quickSearchFilterCompanyValue3', '#filtersForm').val(term);
        jQuery('#' + gridId).data("kendoGrid").dataSource.page(1);
    }
}
function checkWhichTypeOfFilterIUseAndReturnFiltersToCompany() {
    var filtersForm = jQuery('#companySearchFilters');
    // disableEmpty(filtersForm);
    disableUnCheckedFilters();
    var searchFilters = form2js('companySearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
        filters.customFields = searchFilters.customFields;
    } else if (jQuery('#quickSearchFilterCompanyValue', filtersForm).val() || categoryViewSelected) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function exportCompaniesToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFiltersToCompany();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('companySearchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL() + 'export/companies').submit();
}
function loadEventsForFilters() {
    // jQuery('#gridFiltersList').selectpicker()
    // makeFieldsDatePicker({fields: ['registrationDateValue', 'endDateValue', 'crReleasedOnValue', 'crReleasedOnContainerEndDateValue', 'crExpiresOnContainerValue', 'crExpiresOnContainerEndDateValue', 'modifiedOnValue', 'modifiedOnEndValue', 'createdOnValue', 'createdOnEndValue']});
    makeFieldsHijriDatePicker({fields: ['registration-date-value-hijri','registration-date-end-value-hijri-filter','crReleasedOnContainerValue-hijri-filter','crReleasedOnContainerValue-end-date-value-hijri-filter']});
    userLookup('createdByNameValue');
    userLookup('modifiedByNameValue');
    companyGroupAutocompleteMultiOption(jQuery('#parentValue', '#companySearchFilters'));
    contactAutocompleteMultiOption('lawyerValue', '3', resultHandlerAfterCompanyLawyerAutocomplete);
    companyRegistrationAuthorityLookup();
}
function resultHandlerAfterParentCompanyAutocomplete() {
    return true;
}
function resultHandlerAfterCompanyLawyerAutocomplete() {
    return true;
}
function companyRegistrationAuthorityLookup() {
    jQuery('#registrationAuthorityNameValue').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.more_filters = {};
            request.more_filters.category = ['Internal'];
            request.more_filters.requestFlag = 'categoryFlagOnly';
            jQuery.ajax({
                url: getBaseURL() + 'companies/autocomplete',
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
                                value: item.name,
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 1,
        select: function (event, ui) {
        }
    });
}
function companyGroupAutocompleteMultiOption(jQueryField) {
    jQueryField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.more_filters = {};
            request.more_filters.category = ['Group'];
            request.more_filters.requestFlag = 'categoryFlagOnly';
            jQuery.ajax({
                url: getBaseURL() + 'companies/autocomplete',
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
                                label: (null == item.shortName) ? item.name : (item.name + ' ' + item.shortName),
                                value: item.name,
                                record: item
                            };
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 1,
        select: function (event, ui) {
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}
function validateNumbers(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
    if (!decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
}
function gridInitialization() {
    var tableColumns = [];
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'name') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.name, template: '<a href="' + getBaseURL() + 'companies/tab_company/#= id #"><bdi class="trim-width-95-per">#= name #</bdi></a><i class="iconLegal iconPrivacy#=private#"></i>', width: '220px'};
            } else if (item === 'legalType') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, template: "#= (legalType == null) ? '' : legalType #", title: _lang.companyLegalType, width: '182px'};
            } else if (item === 'nationality') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, template: "#= (nationality == null) ? '' : nationality #", title: _lang.nationality, width: '170px'};
            } else if (item === 'lawyer') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, template: "#= (lawyer == null) ? '' : lawyer #", title: _lang.lawyer, width: '170px'};
            } else if (item === 'capital') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, template: "#= kendo.toString(capital, \"n0\") #", title: _lang.capital, width: '120px'};
            } else if (item === 'majorParent') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, template: '<a href="' + getBaseURL() + 'companies/container_view/#= company_id #"><bdi>#= majorParent #</bdi></a> ', title: _lang.companyGroup, width: '216px'};
            }
            else if (item === 'company_category') {
                array_push = {field: item, title: _lang.category, width: '120px', template: "#= (category == null) ? '' : getCategoryTemplate(company_category_id, company_category_keyName) #"};
            }
            else if (item === 'shareParValue') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, template: "#= kendo.toString(shareParValue, \"n2\") #", title: _lang.shareParValue, width: '200px'};
            }
            else if (item === 'registrationDate') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, template: "#= (registrationDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(registrationDate) : registrationDate, 'yyyy-MM-dd'))#", title: _lang.registrationDate, width: '185px'};
            }
            else if (item === 'shortName') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.nickname, width: '182px'};
            }
            else if (item === 'id') {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, filterable: false, template: '<a href="' + getBaseURL() + 'companies/tab_company/#= id #">COM#= id #</a>', title: _lang.id, width: '161px'};
            } else if (item.startsWith('custom_field_')) { //check if the item is a custom field then get the title name from a defined array
                array_push = {field: item, title: customFieldsNames[item],width: '140px', template: '#= ' + item + '!==null ? (' + item + '.length>255 ? ' + item + '.substring(0, 255) + "..." :' + item + ' ):"" #'};
            }
            else {
                array_push = {field: item, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    try {
        companiesSearchDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + "companies/index",
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
                                pinesMessageV2({ty: $response.feedbackMessage.ty, m: $response.feedbackMessage.m});
                            } else {
                                pinesMessageV2({ty: 'error', m: _lang.updatesFailed});
                            }
                        }
                        if ($response.columns_html) {
                            jQuery('#column-picker-trigger-container').html($response.columns_html);
                            jQuery('*[data-callexport]').on('click', function () {
                                if(hasAccessToExport!=1){
                                    pinesMessageV2({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                                }else{
                                    if($response.totalRows <= 10000) {
                                        if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                            exportCompaniesToExcel(true);
                                        } else {
                                            exportCompaniesToExcel();
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
                        }
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
                            setGridFiltersData(gridFormData, 'companyGrid');
                            options.loadWithSavedFilters = 1;
                            options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                            gridSavedFiltersParams = '';
                        } else {
                            options.filter = checkWhichTypeOfFilterIUseAndReturnFiltersToCompany();
                        }
                        options.sortData = JSON.stringify(companiesSearchDataSrc.sort());
                        jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    }
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {type: "integer"},
                        name: {type: "string"},
                        shortName: {type: "string"},
                        legalType: {type: "integer"},
                        nationality: {type: "integer"},
                        lawyer: {type: "integer"},
                        capital: {type: "number"},
                        capitalCurrency: {type: "string"},
                        private: {type: "string"},
                        foreignName: {type: "string"},
                        majorParent: {type: "string"},
                        shareParValue: {type: "number"},
                        registrationNb: {type: "integer"},
                        registrationTaxNb: {type: "integer"},
                        registrationDate: {type: "date"}
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
                            row['name'] = escapeHtml(row['name']);
                            row['nationality'] = escapeHtml(row['nationality']);
                            row['lawyer'] = escapeHtml(row['lawyer']);
                            row['majorParent'] = escapeHtml(row['majorParent']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr);
            },
            page: 1,
            pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
            sort: jQuery.parseJSON(gridSavedColumnsSorting || "null"),
        });
        companiesSearchGridOptions = {
            autobind: true,
            dataSource: companiesSearchDataSrc,
            columns: tableColumns,
            editable: false,
            filterable: false,
            height: 480,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: true,buttonCount:5
        },
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: "single",
            sortable: sqlsrv2008 ? false : {mode: "multiple"},
            toolbar: [{
                    name: "toolbar-menu",
                    template: '<div></div>'

                }],
            columnResize: function (e) {
                fixFooterPosition();
            },
            columnReorder: function (e) {
                orderColumns(e);
            }
        };
    } catch (e) {
    }
    gridTriggers({'gridContainer': jQuery('#companyGrid'), 'gridOptions': companiesSearchGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    displayColHeaderPlaceholder();
}
