var userManagementReportGridOptions;var userManagementReportDataSrc;
jQuery(document).ready(function () {
    userManagementReportDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "users/user_management_report",
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
                }
            },
            parameterMap: function (options, operation) {
                if ("read" === operation) {
                    options.filter = getFormFilters();
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
                    employeeId: {type: "string"},
                    firstName: {type: "string"},
                    lastName: {type: "string"},
                    ad_userCode: {type: "string"},
                    username: {type: "string"},
                    email: {type: "string"},
                    status: {type: "string"},
                    department: {type: "string"},
                    comments: {type: "string"},
                    userGroupName: {type: "string"},
                    userGroupDescription: {type: "string"},
                    last_login: {type: "date"},
                    created: {type: "date"},
                    modified: {type: "date"},
                    userModifiedName: {type: "string"},
                    authorized_by: {type: "string"}
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
                        row['username'] = escapeHtml(row['username']);
                        row['userGroupDescription'] = escapeHtml(row['userGroupDescription']);
                        row['userModifiedName'] = escapeHtml(row['userModifiedName']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        pageSize: 50, serverPaging: true, serverFiltering: true, serverSorting: true
    });
    userManagementReportGridOptions = {
        autobind: true,
        dataSource: userManagementReportDataSrc,
        columns: [
            {field: "employeeId", title: _lang.employeeId, width: '145px'},
            {field: "firstName", title: _lang.firstName, width: '120px'},
            {field: "lastName", title: _lang.lastName, width: '120px'},
            {field: "username", template: "#= username.split('@')[0] #", title: _lang.activeDirectoryId, width: '165px'},
            isCloudInstance ? '' : {field: "username", title: _lang.username, width: '192px'},
            {field: "email", title: _lang.email, width: '192px'},
            {field: "status", template: "#=  getTranslation(status)#", title: _lang.status, width: '99px'},
            {field: "department", title: _lang.department, width: '192px'},
            {field: "comments", title: _lang.comments, width: '192px'},
            {field: "userGroupName", title: _lang.user_group, width: '192px'},
            {field: "userGroupDescription", title: _lang.groupDescription, template: '<span rel="tooltip" title="#=userGroupDescription#"> #=(userGroupDescription!=null&&userGroupDescription!="") ? ((userGroupDescription.length>40)? userGroupDescription.substring(0,40)+"..." : userGroupDescription) : ""#</span>', width: '320px'},
            {field: "last_login", title: _lang.userFields.last_login, format: "{0:yyyy-MM-dd}", width: '158px'},
            {field: "created", title: _lang.createdOn, format: "{0:yyyy-MM-dd}", width: '158px'},
            {field: "userModifiedName", title: _lang.lastModifiedBy, width: '170px', template: '#= (userModifiedName!=null && userModifiedStatus=="Inactive")? userModifiedName+" ("+_lang.custom[userModifiedStatus]+")":((userModifiedName!=null)?userModifiedName:"") #'},
            {field: "modified", title: _lang.lastModifiedOn, format: "{0:yyyy-MM-dd}", width: '159px'},
            {field: "authorized_by", title: _lang.authorizedBy, width: '120px'}
        ],
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
                template: '<div class="col-md-3 no-padding">'
                        + '<h4 class="col-md-12">' + _lang.userManagementReport + '</h4>'
                        + '</div>'
                        + '<div class="col-md-2 no-padding advanced-search">'
                        + '<a href="javascript:;" onclick="advancedSearchFilters()">'
                        + _lang.advancedSearch + '</a>'
                        + '</div>'
                        + '<div class="col-md-1 pull-right">'
                        + '<div class="btn-group pull-right">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                        + _lang.actions + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" onclick="exportUserManagementReportToExcel()" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
            }],
        columnMenu: {messages: _lang.kendo_grid_sortable_messages}
    };
    jQuery('.multi-select', '#userSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    searchUserManagementReport();
});
function searchUserManagementReport() {
    document.getElementsByName("page").value = 1;
    document.getElementsByName("skip").value = 0;
    if (undefined == jQuery('#userManagementReportGrid').data('kendoGrid')) {
        var kGrid = jQuery('#userManagementReportGrid').kendoGrid(userManagementReportGridOptions);
        var kGridData = kGrid.data('kendoGrid');
        if (!makerCheckerFeatureStatus) {
            kGridData.hideColumn("authorized_by");
        }
        return false;
    }
    jQuery('#userManagementReportGrid').data('kendoGrid').dataSource.page(1);
    return false;
}
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['creationDateValue', 'creationDateEndValue', 'last_loginValue', 'last_loginEndValue', 'modifiedOnValue', 'modifiedOnEndValue']});
    userLookup('modifiedByValue');
    userLookup('authorized_byValue');
}
function advancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        loadEventsForFilters();
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function getTranslation(fieldValue) {
    return _lang.custom[fieldValue];
}
function getFormFilters() {
    var filters = '';
    var filtersForm = jQuery('#userSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('userSearchFilters', '.', true)
    filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}
function exportUserManagementReportToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('userSearchFilters')));

    newFormFilter.attr('action', getBaseURL() + 'export/users_management').submit();
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
