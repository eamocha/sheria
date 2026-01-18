var enableQuickSearch = false;
var contactsSearchDataSrc, contractSearchGridOptions;
jQuery(document).ready(function () {
    loadEventsForFilters()
    gridFiltersEvents('Contact', 'searchResults', 'contactSearchFilters');
    gridInitialization();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery('#searchResults').data('kendoGrid').dataSource.read();
    });
    jQuery('#resetBtnHiddenV2', '#contactSearchFilters').on('click', function () {
        jQuery(".form-group-filter").show();
        jQuery('#searchResults').data('kendoGrid').dataSource.read();
    });
    jQuery('.multi-select', '#contactSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    jQuery('#contactSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        jQuery('#contactLookUp').val('');
        if (jQuery('#submitAndSaveFilter').is(':visible')) {
            gridAdvancedSearchLinkState = true;
        }
        enableQuickSearch = false;
        jQuery("#searchResults").data("kendoGrid").dataSource.page(1);
    });
    var lastScrollLeft = 0;
    // jQuery(".k-grid-content").scroll(function() {
    //     var documentScrollLeft = jQuery(".k-grid-content").scrollLeft();
    //     if (lastScrollLeft != documentScrollLeft) {
    //         lastScrollLeft = documentScrollLeft;
    //         jQuery(".wraper-actions").css("right",-lastScrollLeft)

    //         if(isLayoutRTL())
    //             jQuery(".wraper-actions").css("left",lastScrollLeft)

    //     }
    // });
});
function contactQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterContactValue', '#filtersForm').val(term);
        jQuery('#quickSearchFilterContactValue2', '#filtersForm').val(term);
        jQuery('#quickSearchFilterContactValue3', '#filtersForm').val(term);
        jQuery('#quickSearchFilterContactValue4', '#filtersForm').val(term);
        jQuery('#quickSearchFilterContactValue5', '#filtersForm').val(term);
        jQuery('#quickSearchFilterContactValue6', '#filtersForm').val(term);
        jQuery('#quick-search-filter-contact-value-8', '#filtersForm').val(term);
        jQuery("#searchResults").data("kendoGrid").dataSource.page(1);
    }
}
function loadEventsForFilters() {
    // jQuery('#gridFiltersList').selectpicker()
    // makeFieldsDatePicker({fields: ['createdOnDate', 'endDateValue','modifiedOnValue','modifiedOnEndValue','createdOnValue','createdOnEndValue']});
    userLookup('createdByNameValue');
    userLookup('modifiedByNameValue');
    companyAutocompleteMultiOption(jQuery('#companyValue', '#contactSearchFilters'), resultHandlerAfterCompanyAutocomplete);
    jQuery('#nationalityValue').autocomplete({autoFocus: false, delay: 600, source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({url: getBaseURL() + 'home/load_country_list', dataType: "json", data: request, error: defaultAjaxJSONErrorsHandler, success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched_for.sprintf([request.term]), value: '', record: {id: -1, term: request.term}}]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {label: item.countryName, value: item.countryName, record: item}
                        }));
                    }
                }});
        }, minLength: 2, select: function (event, ui) {
        }});
}
function getSearchResults(filtersForm, formData) {
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'contacts/index',
        data: formData,
        beforeSend: function () {
            jQuery('#submit, #reset', filtersForm).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('#submit, #reset', filtersForm).attr('disabled', 'disabled');
            jQuery('#submit, #reset', filtersForm).removeAttr('disabled');
            jQuery('#searchResults').html(response.html);
            scrollToId('#searchResults');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function exportContactsToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('contactSearchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
   newFormFilter.attr('action', getBaseURL() + 'export/contacts').submit();
}
function checkWhichTypeOfFilterIUseAndReturnFilters() {
    var filtersForm = jQuery('#contactSearchFilters');
    // disableEmpty(filtersForm);
    disableUnCheckedFilters();
    var searchFilters = form2js('contactSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterContactValue', '#contactSearchFilters').val() || jQuery('#quickSearchFilterContactValue7', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
function getCustomTranslation(fieldValue) {
    return _lang.custom[fieldValue];
}
function getCategoryTemplate(contact_category_id, category){
    return "<span style='color: " + contactCompanyCategoryColors[contact_category_id] + "'>" + _lang.contactCompanyCategories[category] + "</span>";
}
function deleteContact(id, step) {
    id = id || 0;
    step = step || "confirm_message";
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url:  getBaseURL() + 'contacts/delete/' + id,
        data: {'step': step},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                jQuery("#contact-delete-dialog").remove(); 
                if (jQuery('#contact-delete-dialog').length <= 0) {
                    jQuery('<div id="contact-delete-dialog"></div>').appendTo("body");
                    jQuery('#contact-delete-dialog').html(response.html);
                }
                jQuery('.modal', jQuery('#contact-delete-dialog')).modal({
                    keyboard: false,
                    show: true,
                    backdrop: 'static'
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function  gridInitialization() {
    var tableColumns = [];
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({field: 'id', title: ' ', filterable: false, sortable: false, template: ''+
                    '<div class="dropdown dropdown-action">' + gridActionIconHTMLV2 + '<div class="dropdown-menu margin-minus-top" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'contacts/edit/#= id #">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="deleteContact(\'#= id #\');">' + _lang.delete + '</a>' +
                    '</div></div>', width: '45px', attributes:{class: "flagged-gridcell"}
        });
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'id') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, filterable: false, title: _lang.contactID, template: '<a href="' + getBaseURL() + 'contacts/edit/#= id #">#= contactID #</a>', width: '122px'};
            } else if (item === 'fullName') {
                array_push = {field: item, title: _lang.name, template: '<a href="' + getBaseURL() + 'contacts/edit/#= id #"><bdi>#= fullName #</bdi></a><i class="iconLegal iconPrivacy#=private#"></i>', width: '180px'};
            }
            else if (item === 'isLawyer') {
                array_push = {field: item, template: "#= getCustomTranslation(isLawyer)#", title: _lang.is_lawyer, width: '95px'};
            } else if (item === 'company') {
                array_push = {field: item, title: _lang.company, template: "#= getCompany(company)   #", encoded: false, width: '120px'};
            } else if (item === 'country') {
                array_push = {field: item, title: _lang.country, width: '120px', template: "#= (country == null) ? '' : country #"};
            }
            else if (item === 'category') {
                array_push = {field: item, title: _lang.category, width: '120px', template: "#= (category == null) ? '' : getCategoryTemplate(contact_category_id, category_keyName) #"};
            }
            else if (item === 'email') {
                array_push = {field: item, title: _lang.email, template: '<a href="mailto:#= email #">#= email #</a>', width: '180px'};
            }
            else if (item === 'jobTitle') {
                array_push = {field: item, title: _lang.userFields.jobTitle, width: '150px'};
            }
            else if (item === 'title') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'additional_id_type') {
                array_push = {field: item, title: _lang.additionalIdType, width: '150px', template: "#= (additional_id_type == null) ? '' : additionalIdTypesArr[additional_id_type] #"};
            }
            else if (item === 'additional_id_value') {
                array_push = {field: item, title: _lang.additionalIdValue, width: '150px'};
            }
            else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    contactsSearchDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "contacts/index",
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
                            if($response.totalRows <= 10000) {
                                if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                    exportContactsToExcel(true);
                                } else {
                                    exportContactsToExcel();
                                }
                            }else {
                                applyExportingModuleMethod(this);
                            }
                        });
                        gridEvents();
                        loadExportModalRanges($response.totalRows);
                    }
                    if (gridAdvancedSearchLinkState) {
                        gridAdvancedSearchLinkState = false;
                    }
                    animateDropdownMenuInGridsV2('searchResults',50);
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
                        setGridFiltersData(gridFormData, 'searchResults');
                        options.loadWithSavedFilters = 1;
                        options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                        gridSavedFiltersParams = '';
                    } else {
                        options.filter = checkWhichTypeOfFilterIUseAndReturnFilters();
                    }
                    options.sortData = JSON.stringify(contactsSearchDataSrc.sort());
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
                    fullName: {type: "string"},
                    jobTitle: {type: "string"},
                    email: {type: "string"},
                    phone: {type: "string"},
                    mobile: {type: "string"},
                    company: {type: "integer"},
                    private: {type: "string"},
                    category: {type: "string"}
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
                        row['fullName'] = escapeHtml(row['fullName']);
                        row['company'] = escapeHtml(row['company']);
                        row['country'] = escapeHtml(row['country']);
                        row['category'] = escapeHtml(row['category']);
                        row['email'] = escapeHtml(row['email']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        page: 1, pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        sort: jQuery.parseJSON(gridSavedColumnsSorting || "null")
    });
    contractSearchGridOptions = {
        autobind: true,
        dataSource: contactsSearchDataSrc,
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
        sortable: {mode: "multiple"},
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
    gridTriggers({'gridContainer': jQuery('#searchResults'), 'gridOptions': contractSearchGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    displayColHeaderPlaceholder();
}
function getCompany(value)
{
    return ( value != null && value != "" ) ? ("<span class='tooltip-title rtl-fixing-keno-text' title='" + value + "'>" + helpers.truncate(value, 50, true,   _lang.languageSettings['langDirection'] === 'rtl') + "</span>" ) : "";
}
