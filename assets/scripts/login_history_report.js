jQuery(document).ready(function () {
	jQuery('.multi-select', '#loginHistoryReportFilters').chosen({no_results_text: _lang.no_results_matched,placeholder_text: _lang.select,width: "100%"}).change();
    searchLoginHistoryReport();
    jQuery('#loginHistoryReportFilters').bind('submit', function (e) {
        e.preventDefault();
        searchLoginHistoryReport();
    });
});
function advancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        makeFieldsDatePicker({fields:['logDateValue', 'logDateEndValue']});
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function functionReturnOnlyTrue() {
    return true;
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function searchLoginHistoryReport() {
    document.getElementsByName("page").value =1;
        document.getElementsByName("skip").value =0;
    if (undefined === jQuery('#loginHistoryReportGrid').data('kendoGrid')) {
        jQuery('#loginHistoryReportGrid').kendoGrid(loginHistoryReportGridOptions);
        return false;
    }
    jQuery('#loginHistoryReportGrid').data('kendoGrid').dataSource.page(1);
    return false;
}
function getLoginHistoryReportFilters() {
    var filtersForm = jQuery('#loginHistoryReportFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('loginHistoryReportFilters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}
var loginHistoryReportDataSrc = new kendo.data.DataSource({
    transport: {
        read: {
			url: getBaseURL() + "users/login_history_report",
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
                options.filter = getLoginHistoryReportFilters();
                options.returnData = 1;
            }
            return options;
        }
    },
    schema: {type: "json", data: "data", total: "totalRows",
        model: {
            id: "id",
            fields: {
                id: {type: "integer"},
                user_id: {type: "integer"},
                userLogin: {type: "string"},
                userGroupName: {type: "string"},
                action: {type: "string"},
                source_ip: {type: "string"},
                log_message: {type: "string"},
                log_message_status: {type: "string"},
                logDate: {type: "dateTime"},
                fullLogDate: {type: "dateTime"},
                user_agent: {type: "string"}
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
                    row['action'] = escapeHtml(row['action']);
                    row['log_message_status'] = escapeHtml(row['log_message_status']);
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
var loginHistoryReportGridOptions = {
    autobind: true,
    dataSource: loginHistoryReportDataSrc,
    columns: [
        {field: "user_id", title: _lang.user_id, template: "#= (user_id) ? user_id-1 : '' #", width: '115px'},
        {field: "userLogin", title: _lang.userLogin, width: '164px'},
        {field: "userGroupName", title: _lang.user_group, width: '164px'},
        {field: "action", title: _lang.log_action, template: "#= getActionTranslationName(action) #", width: '60px'},
        {field: "source_ip", title: _lang.source_ip, width: '80px'},
        {field: "log_message_status", title: _lang.status, template: "#= getlogMessageStatus(log_message_status) #", width: '84px'},
        {field: "fullLogDate", format: "{0:yyyy-MM-dd}", title: _lang.date, width: '80px'},
        {field: "log_message", title: _lang.action_details, template: "#= getlogMessage(log_message_status) #", width: '130px'},
        {field: "user_agent", title: _lang.environment, width: '128px'}
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
            template: '<div class="row w-100"><div class="col-md-4">'
                    + '<div class="row"><h4 class="col-6">' + _lang.loginHistoryReport + '</h4><div class="col-6 advanced-search">'
                    + '<a href="javascript:;" onclick="advancedSearchFilters()">' + _lang.advancedSearch + '</a></div>'
                    + '</div>'
                    + '</div>'
                    + '<div class="col-md-4 pull-right">'
                    + '<div class="btn-group pull-right">'
                    + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                    + _lang.actions + ' <span class="caret"></span>'
                    + '<span class="sr-only">Toggle Dropdown</span>'
                    + '</button>'
                    + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                    + '<a class="dropdown-item" href="users/clear_login_history_logs" class="">'+ _lang.housekeeping+ '</a>'
                    + '<a class="dropdown-item" onclick="exportLoginHistoryReportToExcel()" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a>'
                    + '</div>'
                    + '</div>'
                    + '</div></div>'
        }],
    columnMenu: {messages: _lang.kendo_grid_sortable_messages}
};
function exportLoginHistoryReportToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getLoginHistoryReportFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('loginHistoryReportFilters')));
    newFormFilter.attr('action', getBaseURL() + 'export/login_history_report').submit();
}
function getActionTranslationName(fieldName) {
    return (fieldName === 'login') ? _lang.sign_in : _lang.sign_out;
}
function getlogMessageStatus(fieldName) {
    return (fieldName === 'log_msg_status_2') ? _lang.failed : _lang.successful;
}
function getlogMessage(status) {
    return _lang.loginHistoryReportStatuses[status];
}
