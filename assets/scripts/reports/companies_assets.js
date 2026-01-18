var companiesAssetsDataSrc, companiesAssetsGridOptions;
function gridInitialization() {
    var tableColumns = [];
    if (jQuery("#columns", '#report-search-filters').val()) {
        var columnsArray = (jQuery("#columns", '#report-search-filters').val()).split(',');
        jQuery.each(columnsArray, function (i, item) {
            if (item == 'company_name') {
                array_push = {field: item, title: _lang.company, template: '<a href="companies/asset_view/#= company_id #">#= company_name #</a>', width: '140px', sortable: true};
            } else if (item == 'company_id') {
                return true;
            }else if (item == 'description') {
                array_push = {field: item, title: getTranslation(item), width: '240px', template: '#= ' + item + '!==null ? (' + item + '.length>255 ?' + item + '.substring(0, 255) + "..." :' + item + ' ):"" #'};
            } 
            else
            if (!isNaN(parseInt(item * 1))) {
                array_push = {field: 'custom_' + item, title: customFieldsNames[item], template: '#= custom_' + item + '!==null ? (custom_' + item + '.length>255 ? custom_' + item + '.substring(0, 255) + "..." :custom_' + item + ' ):"" #', width: '140px', sortable: false};
            } else {
                array_push = {field: item, title: getTranslation(item), width: '140px', sortable: true};
            }
            tableColumns.push(array_push);
        });
    }
    companiesAssetsDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "companies/asset_view/",
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
                    animateDropdownMenuInGrids('reminderGrid');
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    options.filter = getFormFilters();
                    options.returnData = 1;
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                }
                return options;
            }

        },
        schema: {
            type: "json", data: "data", total: "totalRows",
            model: {
                fields: {
                    company_id: {type: "string"},
                    company_name: {type: "string"},
                    name: {type: "string"},
                    type: {type: "string"},
                    ref: {type: "string"},
                    description: {type: "string"},
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
                        row['company_name'] = escapeHtml(row['company_name']);
                        row['description'] = escapeHtml(row['description']);
                        for(var key in row){
                            if(key.includes('custom_')){
                                row[key] = escapeHtml(row[key]);
                            }
                        }
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            if (e.xhr.responseText != 'True')
                defaultAjaxJSONErrorsHandler(e.xhr)
        },
        pageSize: 10,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    companiesAssetsGridOptions = {
        autobind: true,
        dataSource: companiesAssetsDataSrc,
        columns: tableColumns,
        editable: false,
        filterable: false,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        height: 480,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [
            {name: "gridTitle",
                template: '<div class="col-md-4 p-0 d-md-flex">'
                        + '<h4 class="col-md-2">' + _lang.assets + '</h4>'
                        + '<div class="advanced-search pl-5">'
                        + '<a href="javascript:;" onclick="advancedSearchFilters(true)">' + _lang.advancedSearch + '</a>'
                        + '</div></div>'
                        + '<div class="col-md-2 float-right">'
                        + '<div class="btn-group float-right">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">' + _lang.actions
                        + '<span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" role="menu">'
                        + '<li><a  class="dropdown-item" onclick="companyAssetExportToExcel()" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a></li>'
                        + '</ul>'
                        + '</div>'
                        + '</div>'
            }
        ]
    };
}
function searchCompanyAssets() {
    var companiesAssetsGrid = jQuery('#companies-assets-grid', '#companies-assets-grid-form');
    if (undefined == companiesAssetsGrid.data('kendoGrid')) {
        companiesAssetsGrid.kendoGrid(companiesAssetsGridOptions);
        return false;
    }
    companiesAssetsGrid.data('kendoGrid').dataSource.read();
    return false;
}
function getFormFilters() {
    var filtersForm = jQuery('#report-search-filters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('report-search-filters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
jQuery(document).ready(function () {
    $searchFiltersForm = jQuery('form#report-search-filters');
    gridInitialization();
    $searchFiltersForm.bind('submit', function (e) {
        e.preventDefault();
        searchCompanyAssets();
    });
    searchCompanyAssets();
    isKendoInlineFormHasUnsavedData();
    customGridToolbarCSSButtons();
});
function loadEventsForFilters() {
    companyAutocompleteMultiOption(jQuery('#company-name', $searchFiltersForm), advancedSearchLookupFieldsResultHandler);
}
function advancedSearchLookupFieldsResultHandler(record){
    jQuery('#companyValue',$searchFiltersForm).val(record.id);
	return true;
}
function companyAssetExportToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('report-search-filters')));

    newFormFilter.attr('action', getBaseURL() + 'export/companies_assets_report').submit();
}
