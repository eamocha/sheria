var matterReportDataSrc, matterAttachGridOptions;
jQuery(document).ready(function () {
    gridInitialization();
});

function  gridInitialization() {
    tableColumns = [
        {field:"caseId", title: _lang.caseId, template:  '<a href="' + getBaseURL() + 'cases/edit/#= id #">#= caseId #</a>', width: '120px'},
        {field:"internalReference", title: _lang.internalReferenceNumber, width: '120px'},
        {field:"category", title: _lang.category, width: '120px'},
        {field:"subject", title: _lang.subject_Case, template:  '<a href="' + getBaseURL() + 'cases/edit/#= id #">#= subject #</a>', width: '200px'},
        {field:"client", title: _lang.client, width: '120px'},
        {field:"type", title: _lang.caseType, width: '120px'},
        {field:"assignee", title: _lang.assignee, width: '120px'},
        {field:"status", title: _lang.status, width: '120px'},
        {field:"foldersNames", title: _lang.folders, width: '120px'},
        {field:"filesDetails", title: _lang.numberOfFiles, template: "#= (filesDetails == null) ? '' : splitFilesDetails(filesDetails, 'number') #", width: '120px'},
        {field:"filesDetails", title: _lang.generalTotalSize, template: "#= (filesDetails == null) ? '' : splitFilesDetails(filesDetails, 'size') #",  width: '120px'}
    ];
    matterReportDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "reports/matters_attachments_report",
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (XHRObj.responseText == 'access_denied') {
                        return false;
                    }
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    jQuery('*[data-callexport]').on('click', function () {
                        if($response.totalRows <= 1000) {
                            exportCasesToExcel();
                        } else {
                            applyExportingModuleMethod(this);
                        }
                    });
                    loadExportModalRanges($response.totalRows, 1, 1000);
                    animateDropdownMenuInGridsV2('searchResults',50);
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
        },
        schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {type: "integer"},
                    caseId: {type: "string"},
                    internalReference: {type: "string"},
                    category: {type: "string"},
                    subject: {type: "string"},
                    type: {type: "string"},
                    client: {type: "string"},
                    assignee: {type: "string"},
                    status: {type: "string"},
                    foldersNames: {type: "string"},
                    filesDetails: {type: "string"},
                }
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        page: 1, pageSize: gridDefaultPageSize,
        serverPaging: true,
        serverSorting: true
    });
    matterAttachGridOptions = {
        autobind: true,
        dataSource: matterReportDataSrc,
        columns: tableColumns,
        editable: false,
        filterable: false,
        height: 480,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: true, buttonCount:5
        },
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {mode: "multiple"},
        toolbar: [{
            name: "toolbar-menu",
            template:
                '<div class="col-md-1 pull-right">'
                + '<div class="btn-group pull-right">'
                    + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'+ _lang.actions
                        + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                    + '</button>'
                    + '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" role="menu">'
                        + '<li><a class="dropdown-item" data-callexport="exportCasesToExcel();"   href="javascript:;" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a></li>'
                    + '</ul>'
                + '</div>'
                + '</div>'
            }],
        columnResize: function (e) {
            fixFooterPosition();
        },
        columnReorder: function (e) {
            orderColumns(e);
        }
    };
    gridTriggers({'gridContainer': jQuery('#matterAttachGrid'), 'gridOptions': matterAttachGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    displayColHeaderPlaceholder();
}

function exportCasesToExcel() {
    var attachmentsReport = jQuery('#attachments-report');
    attachmentsReport.attr('action', getBaseURL() + 'export/export_matters_attachments_report').submit();
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', attachmentsReport).val(pageNumber);
    enableAll(attachmentsReport);
}

function splitFilesDetails(files, field){
    var details = files.split('::');
    if (field == 'size')
        return convertBytes(details[1]);
    if (field == 'number')
        return details[0];
}

function convertBytes(bytes){
    const sizes = ["Bytes", "KB", "MB", "GB", "TB"]
    if (bytes == 0 || bytes == null) {
        return "0 Bytes"
    }
    const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)))
    if (i == 0) {
        return bytes + " " + sizes[i]
    }
    return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i]
}