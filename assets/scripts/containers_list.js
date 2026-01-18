var gridId = 'companyContainersGrid';
var enableQuickSearch = true;
var categoryViewSelected = true;
try {
    var companiesSearchDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "companies/containers_list",
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids('companyContainersGrid');
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    options.filter = checkWhichTypeOfFilterIUseAndReturnFiltersToCompany();
                    options.returnData = 1;
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                }
                return options;
            }
        },
        schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {editable: false, type: "integer"},
                    companyID: {editable: false, type: "string"},
                    name: {type: "string"},
                    foreignName: {type: "string"},
                    nationality: {type: "string"},
                    private: {type: "string"},
                    status: {type: "string"},
                    city: {type: "string"},
                    state: {type: "string"},
                    zip: {type: "string"},
                    website: {type: "string"},
                    phone: {type: "string"},
                    fax: {type: "string"},
                    mobile: {type: "string"}
                }
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        pageSize: 20, serverPaging: true, serverFiltering: true, serverSorting: true
    });
    var companiesSearchGridOptions = {
        autobind: true,
        dataSource: companiesSearchDataSrc,
        columns: [
            {field: "id", title: ' ', filterable: false, sortable: false,
                template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="javascript://COM#= id #" onclick="companyContainerForm(\'#= id #\');">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="deleteCompanyContainer(\'#= id #\');">' + _lang.deleteRow + '</a>' +
                        '</div></div>', width: '20px'
            },
            {field: "companyID", filterable: false, template: '<a href="' + getBaseURL() + 'companies/container_view/#= id #">#= companyID #</a><i class="iconLegal iconPrivacy#=private#"></i>', title: _lang.groupId, width: '90px'},
            {field: "name", title: _lang.groupName, width: '220px'}
        ],
        editable: false,
        filterable: false,
        height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [{
                name: "toolbar-menu",
                template: '<div class="col-md-6 no-padding">'
                        + '<h4 class="col-md-5 no-padding-right">' + _lang.companyGroups + '</h4>'
                        + '<div class="input-group col-md-4">'
                        + '<input type="text" class="form-control lookup search margin-top" placeholder=" ' + _lang.search + '" name="companyLookUp" id="companyLookUp" onkeyup="companyQuickSearch(event.keyCode, this.value);"/>'
                        + '</div>'
                        + '</div>'
                        + '<div class="col-md-1 pull-right">'
                        + '<div class="btn-group pull-right">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">' + _lang.actions
                        + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a id="addCompanyContainer" class="dropdown-item" href="javascript://Add Container;" onclick="companyContainerForm();">' + _lang.newGroup + '</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
            }],
        columnMenu: {messages: _lang.kendo_grid_sortable_messages}
    };
} catch (e) {
}
jQuery(document).ready(function () {
    try {
        searchCompanies();
        jQuery('#companySearchFilters').bind('submit', function (e) {
            e.preventDefault();
            jQuery('#companyLookUp').val('');
            enableQuickSearch = false;
            searchCompanies();
        });
    } catch (e) {
    }
});
function companyQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        enableQuickSearch = true;
        document.getElementsByName("page").value =1;
        document.getElementsByName("skip").value =0;
        jQuery('#quickSearchFilterCompanyValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilterCompanyValue2', '#filtersFormWrapper').val(term);
        jQuery('#' + gridId).data("kendoGrid").dataSource.page(1);
    }
}
function checkWhichTypeOfFilterIUseAndReturnFiltersToCompany() {
    var filtersForm = jQuery('#companySearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('companySearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterCompanyValue', filtersForm).val() || categoryViewSelected) {
        filters = searchFilters.quickSearch;
    }
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
function resultHandlerAfterParentCompanyAutocomplete() {
    return true;
}
function resultHandlerAfterCompanyLawyerAutocomplete() {
    return true;
}
function searchCompanies() {
    var companyContainersGridId = jQuery('#companyContainersGrid');
    document.getElementsByName("page").value =1;
    document.getElementsByName("skip").value =0;
    if (undefined == companyContainersGridId.data('kendoGrid')) {
        companyContainersGridId.kendoGrid(companiesSearchGridOptions);
        return false;
    }
    companyContainersGridId.data('kendoGrid').dataSource.page(1);
    return false;
}
function deleteCompanyContainer(containerId) {
    if (!containerId)
        return;
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        window.location = getBaseURL() + 'companies/container_delete/' + containerId;
    }
}
