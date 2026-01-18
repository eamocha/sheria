var gridDataSrc, gridOptions, grid;
jQuery(document).ready(function () {
    companyGridEvents();
});

function companyGridEvents() {
    grid = jQuery('#company-group-grid');
    companiesGridInitialization();
    searchRelatedCompanies();
    
    var lookupDetails = {
        'lookupField': jQuery('#lookup-company', '#related-company-form'),
        'hiddenId': '#lookup-company-id',
        'isBoxContainer': false,
        'resultHandler': onSelectLookupCallback
    };
    lookUpCompanies(lookupDetails, jQuery('#related-company-form'));
}


function searchRelatedCompanies() {
    if (undefined == grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        var gridGrid = grid.data('kendoGrid');
        return false;
    }
    grid.data('kendoGrid').dataSource.read();
    return false;
}

function getCompanyFormFilters() {
    var filtersForm = jQuery('#companySearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('companySearchFilters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}

function companiesGridInitialization() {
    gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "companies/container_view/" + jQuery('#companyIdFilter', '#companySearchFilters').val(),
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    options.filter = getCompanyFormFilters();
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
                    id: {editable: false, type: "integer"},
                    name: {editable: false, type: "string"},
                    shortName: {editable: false, type: "string"},
                    companyCategory: {editable: false, type: "string"},
                    legalType: {editable: false, type: "integer"},
                    nationality: {editable: false, type: "integer"},
                    subCategory: {editable: false, type: "integer"},
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
                        row['name'] = escapeHtml(row['name']);
                        row['shortName'] = escapeHtml(row['shortName']);
                        row['nationality'] = escapeHtml(row['nationality']);
                        row['company_sub_category'] = escapeHtml(row['company_sub_category']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        batch: true,
        pageSize: 20,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columns: [
            {
                field: 'name', 
                headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, 
                title: _lang.name, 
                template: '<a href="' + getBaseURL() + 'companies/tab_company/#= id #"><bdi>#= name #</bdi></a><i class="iconLegal iconPrivacy#=private#"></i>', 
                width: '220px'
            },
            {
                field: 'shortName', 
                headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, 
                title: _lang.nickname,
                width: '220px'
            },
            {
                field: 'companyCategory', 
                title: _lang.category, 
                width: '120px', 
                template: "#= (category == null) ? '' : getCategoryTemplate(company_category_id, company_category) #",
            },
            {
                field: 'subCategory',
                headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''},
                template: "#= (company_sub_category == null) ? '' : company_sub_category #",
                title: _lang.subCategory,
                width: '170px'
            },
            {
                field: 'legalType', 
                headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, 
                template: "#= (legalType == null) ? '' : legalType #", 
                title: _lang.companyLegalType, 
                width: '182px'
            },
            {
                field: 'nationality', 
                headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, 
                template: "#= (nationality == null) ? '' : nationality #", 
                title: _lang.nationality, 
                width: '170px'
            },
            {
                field: "actions",
                template: '<div class="wraper-actions"><div class="list-of-actions"><a href="javascript:;" onclick="deleteSelectedRow(\'#= id #\')"><i class="fa fa-fw fa-trash"></i></a></div></div>',
                sortable: false,
                title: _lang.actions,
                width: '120px'
            },

        ],
        editable: false,
        filterable: false,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages, pageSizes: [10, 20, 50, 100], refresh: false, buttonCount: 5
        },
        reorderable: true,
        resizable: true,
        height: 330,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
    };
}

function getCategoryTemplate(company_category_id, category){
    return "<span style='color: " + contactCompanyCategoryColors[company_category_id] + "'>" + category + "</span>";
}

function onSelectLookupCallback() {
    jQuery('#relation-button', '#related-company-form').removeAttr('disabled');
}
function deleteSelectedRow(id){
    confirmationDialog('confirm_delete_record', {
        resultHandler: _deleteSelectedRow,
        parm: {id: id}
    });
}

function _deleteSelectedRow(id){
    jQuery.ajax({
        url: getBaseURL() + 'companies/delete_cg_related_company',
        dataType: "json",
        type: "POST",
        data: id,
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.result) {
                case true:	// remove successfuly
                    ty = 'success';
                    m = _lang.relationRemovedSuccessfully;
                    break;
                case false:	// could not delete record
                    m = _lang.deleteRecordFailed;
                    break;
                default:
                    break;
            }
            pinesMessage({ty: ty, m: m});
            jQuery('#company-group-grid').data('kendoGrid').dataSource.read();
        }
    });
}

function relateCompanyAdd() {
    var companyGroupId = jQuery('#companyIdFilter').val();
    var newRelatedCompany = jQuery('#lookup-company-id', '#related-company-form').val();
    jQuery.ajax({
        url: getBaseURL() + 'companies/related_company_add',
        dataType: "json",
        type: "POST",
        data: {company_group_id: companyGroupId, related_company_id: newRelatedCompany},
        success: function (response) {
            pinesMessage({ty: response.result ? 'success' : 'error', m: response.display_message});
            if (response.result) {
                jQuery('#company-group-grid').data('kendoGrid').dataSource.read();
                jQuery('#lookup-company-id', '#related-company-form').val('');
                jQuery('#lookup-company', '#related-company-form').val('');
            }
        }, complete: function () {
            jQuery('#relation-button', '#related-company-form').attr('disabled', 'disabled');
        }, error: defaultAjaxJSONErrorsHandler
    });
}