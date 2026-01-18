var enableQuickSearch = false;
var attachmentCount, companyAssetsDataSrc, companyAssetsGridOptions;
function gridInitialization() {
    var tableColumns = [];
    if (jQuery("#columns", '#companyAssetsSearchFilters').val()) {
        var columnsArray = (jQuery("#columns", '#companyAssetsSearchFilters').val()).split(',');
        jQuery.each(columnsArray, function (i, item) {
            if (item == 'id') {
                array_push = {
                    field: "id", filterable: false,
                    template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                            '<a class="dropdown-item" href="companies/asset_edit/#= id #">' + _lang.viewEdit + '</a>' +
                            '<a class="dropdown-item" href="companies/asset_edit/#= id #/docs">' + _lang.attachments + '</a>' +
                            '<a class="dropdown-item" href="javascript:;" onclick="confirmationDialog(\'confirm_delete_record\',{resultHandler: deleteSelectedRow, parm: #= id #})" title="' + _lang.deleteRow + '"  style="#= (id >0) ? \'\' : \'display: none;\' #">' + _lang.deleteRow + '</a>' +
                            '</div></div>',
                    title: _lang.actions, width: '90px'
                };
            } else if (item == 'asset_id') {
                array_push = {field: item, title: _lang.id, width: '140px'};
            } else if (item == 'description') {
                array_push = {field: item, title: getTranslation(item), width: '240px', template: '#= ' + item + '!==null ? (' + item + '.length>255 ?' + item + '.substring(0, 255) + "..." :' + item + ' ):"" #'};
            } else
            if (!isNaN(parseInt(item * 1))) {
                array_push = {field: 'custom_' + item, title: customFieldsNames[item], template: '#= custom_' + item + '!==null ? (custom_' + item + '.length>255 ? custom_' + item + '.substring(0, 255) + "..." :custom_' + item + ' ):"" #', width: '140px', sortable: false};
            } else {
                array_push = {field: item, title: getTranslation(item), width: '140px', sortable: true};
            }
            tableColumns.push(array_push);
        });
    }
    companyAssetsDataSrc = new kendo.data.DataSource({
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
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                }
                return options;
            }
        },
        schema: {
            type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {editable: false, type: "number"},
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
    companyAssetsGridOptions = {
        autobind: true,
        dataSource: companyAssetsDataSrc,
        columns: tableColumns,
        editable: false,
        filterable: false,
 pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: false,buttonCount:5
        }, 
                reorderable: true,
        resizable: true,
        height: 480,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [
            {name: "gridTitle",
                template: '<div class="col-md-4 col-xs-6 no-padding">'
                        + '<div class="input-group col-md-7 col-xs-7 no-padding">'
                        + '<input type="text" class="form-control search quick-search-filter" placeholder="' + _lang.assets + '" name="assetsLookUp" id="assetsLookUp" onkeyup="assetsQuickSearch(event.keyCode, this.value);" title="' + _lang.searchAssets + '" />'
                        + '</div>'
                        + '</div>'
            }
        ]
    };
}
function searchCompanyAssets() {
    var assetsGrid = jQuery('#assetsGrid', '#assetsGridForm');
    if (undefined == assetsGrid.data('kendoGrid')) {
        assetsGrid.kendoGrid(companyAssetsGridOptions);
        return false;
    }
    assetsGrid.data('kendoGrid').dataSource.read();
    return false;
}
function getFormFilters() {
    var filtersForm = jQuery('#companyAssetsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('companyAssetsSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchCompanyAssetsValue', '#filtersFormWrapper').val().length > 0 || jQuery('#quickSearchCompanyAssetsCompanyIdValue', '#filtersFormWrapper').val().length > 0) {
        filters = searchFilters.quickSearch;
    }
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
function deleteSelectedRow(id) {
    jQuery.ajax({
        url: getBaseURL() + 'companies/asset_delete',
        type: 'POST',
        dataType: 'JSON',
        data: {
            assetId: id,
            assetCompany: jQuery('#companyId', '#assetsGridForm').val()
        },
        success: function (response) {
            if (response.status) {
                pinesMessageV2({ty: 'success', m: _lang.companyAssetDeletedSuccessfully});
                jQuery('#assetsGrid', '#assetsGridForm').data("kendoGrid").dataSource.read();
            } else {
                pinesMessageV2({ty: 'error', m: _lang.recordNotDeleted});
            }


        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function assetsQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchCompanyAssetsValue', '#companyAssetsSearchFilters').val(term);
        jQuery('#assetsGrid', '#assetsGridForm').data("kendoGrid").dataSource.page(1);
    }
}
function companyAssetForm() {
    jQuery.ajax({
        url: getBaseURL() + 'companies/asset_add',
        type: 'GET',
        dataType: "json",
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.html) {
                var companyAssetId = "#company-asset-form-div";
                if (jQuery(companyAssetId).length <= 0) {
                    jQuery("<div id='company-asset-form-div'></div>").appendTo("body");
                    var companyAssetContainer = jQuery(companyAssetId);
                    companyAssetContainer.html(response.html);
                    commonModalDialogEvents(companyAssetContainer, companyAssetFormSubmit);
                    initializeModalSize(companyAssetContainer);
                    jQuery('.select-picker', companyAssetContainer).selectpicker({dropupAuto: false});
                    jQuery('#company-id', companyAssetContainer).val(jQuery('#companyId', '#assetsGridForm').val());
                    fixDateTimeFieldDesign(companyAssetContainer);
                    loadCustomFieldsEvents('custom-field-',companyAssetContainer);
                }
            }
        }
    });
}
function companyAssetFormSubmit(container) {
    var formData = new FormData(document.getElementById('company-asset-form'));
    jQuery.ajax({
        url: getBaseURL() + 'companies/asset_add',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('#assetsGrid', '#assetsGridForm').data('kendoGrid').dataSource.read();
                jQuery(".modal", container).modal("hide");
                if (typeof response.validation_errors !== 'undefined' && typeof response.validation_errors['files'] !== 'undefined') {
                    pinesMessageV2({ty: 'warning', m: response.validation_errors['files']});
                }
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }
        , complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
jQuery(document).ready(function () {
    jQuery('.multi-select', '#companyAssetsSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    $searchFiltersForm = jQuery('form#companyAssetsSearchFilters');
    gridInitialization();
    $searchFiltersForm.bind('submit', function (e) {
        e.preventDefault();
        enableQuickSearch = false;
        searchCompanyAssets();
    });
    searchCompanyAssets();
    isKendoInlineFormHasUnsavedData();
    customGridToolbarCSSButtons();
});
function loadEventsForFilters() {
    return true;
}
function companyAssetExportToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('companyAssetsSearchFilters')));

    newFormFilter.attr('action', getBaseURL() + 'export/company_assets').submit();
}
