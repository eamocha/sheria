var $documentsGridOptions, $documentsGrid, documentsForm = false;
var officeFileTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx'];
jQuery(document).ready(function () {
    $documentsGrid = jQuery('#documents-grid');
    documentsForm = jQuery('#documents-form');
    moduleRecordId = jQuery('#module-record-id', documentsForm).val();
    moduleController = jQuery('#module-controller', documentsForm).val();
    module = jQuery('#module', documentsForm).val();
    loadCPAttachmentsGrid();
    documentsSearch();
});

function documentsSearch() {
    if (undefined === $documentsGrid.data('kendoGrid')) {
        if (moduleDocumentTypeValues === "" || moduleDocumentStatusValues === "") {
            $documentsGridOptions.columns.splice(2, 2);
        } else {
            $documentsGridOptions.columns[2].values = moduleDocumentTypeValues;
            $documentsGridOptions.columns[3].values = moduleDocumentStatusValues;
        }
        $documentsGrid.kendoGrid($documentsGridOptions);
        return false;
    }
    return false;
}

function getExtIcon(ext) {
    ext = ext.toLowerCase();
    $extensions = {'doc': 'fs-word-icon', 'docx': 'fs-word-icon', 'xls': 'fs-excel-icon', 'xlsx': 'fs-excel-icon', 'ppt': 'fs-powerpoint-icon', 'pps': 'fs-powerpoint-icon', 'pptx': 'fs-powerpoint-icon', 'pdf': 'fs-pdf-icon', 'tif': 'fs-image-icon', 'jpg': 'fs-image-icon', 'png': 'fs-image-icon', 'gif': 'fs-image-icon', 'jpeg': 'fs-image-icon', 'bmp': 'fs-image-icon', 'folder': 'fs-folder-icon', 'msg': 'fs-email-icon', 'eml': 'fs-email-icon', 'vcf': 'fs-email-icon', 'html': 'fs-email-icon', 'htm': 'fs-email-icon', 'txt': 'fs-text-icon', 'zip': 'fs-compress-icon', 'rar': 'fs-compress-icon', 'avi': 'fs-video-icon', 'mpg': 'fs-video-icon', 'mp4': 'fs-video-icon', 'mp3': 'fs-video-icon', 'flv': 'fs-video-icon', 'unknown': 'fs-unknown-icon', 'jfif': 'fs-image-icon'};
    return (undefined === $extensions[ext]) ? $extensions['unknown'] : $extensions[ext];
}

function loadDirectoryContent(docId, parentLineage) {
    parentLineage = parentLineage || "";
    document.location.hash = '';
    jQuery('#term', documentsForm).val('');
    jQuery('#attachmentLookUp', $documentsGrid).val('');
    var parentPath = ('' !== parentLineage) ? parentLineage : jQuery('#lineage', documentsForm).val();
    jQuery('#lineage', documentsForm).val(parentPath);
    $documentsGrid.data('kendoGrid').dataSource.read();
}

function attachmentQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        jQuery('#term', documentsForm).val(term);
        $documentsGrid.data('kendoGrid').dataSource.read();
    }
}

function downloadFiles(commentId) {
    var downloadUrl = getBaseURL() + 'modules/customer-portal/' + moduleController + '/comment_attachments_download/' + commentId;
    window.location = downloadUrl;
}
jQuery(document).on("click", "#tab-attachments-label", function() {
    $documentsGrid.data('kendoGrid').dataSource.read();
});

function updateCrumbLink(crumbObject) {
    var BreadcrumbContainer = jQuery('#BreadcrumbContainer');
    jQuery('.not-fixed', BreadcrumbContainer).remove();
    if (undefined !== crumbObject || crumbObject !== '') {
        var nodeToAdd = '';
        for (i in crumbObject) {
            nodeToAdd = '<li class="breadcrumb-item not-fixed">';
            nodeToAdd += '<a href="javascript: openFolder(\'' + addslashes(crumbObject[i]['lineage']) + '\');">' + crumbObject[i]['name'] + '</a>';
            nodeToAdd += '</li>';
            BreadcrumbContainer.append(nodeToAdd);
        }
        jQuery('li.active', BreadcrumbContainer).removeClass('active bold');
    }
}
function openFolder(folderLineage) {
    jQuery('#lineage', documentsForm).val(folderLineage);
    $documentsGrid.data('kendoGrid').dataSource.read();
}
function loadCPAttachmentsGrid() {
    try {
        var documentsDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + "modules/customer-portal/" + moduleController + "/load_attachments",
                    dataType: "JSON",
                    type: "POST",
                    complete: function (XHRObj) {
                        if (XHRObj.responseText == 'access_denied') {
                            return false;
                        }
                        $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        if (undefined !== $response.error) {
                            pinesMessage({ty: 'error', m: $response.error});
                            jQuery('#lineage', documentsForm).val($response.initialModelPath);
                            $documentsGrid.data('kendoGrid').dataSource.read();
                        } else {
                            jQuery('#lineage', documentsForm).val($response.fetchedPath);
                            updateCrumbLink($response.crumbLinkData);
                        }
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        for (i in options.models) {
                            if (parseInt(options.models[i]['document_type_id']) < 1) {
                                options.models[i]['document_type_id'] = null;
                            }
                            if (parseInt(options.models[i]['document_status_id']) < 1) {
                                options.models[i]['document_status_id'] = null;
                            }
                        }
                        return {
                            models: kendo.stringify(options.models)
                        };
                    } else {
                        options.module = module;
                        options.module_record_id = moduleRecordId;
                        options.lineage = jQuery('#lineage', documentsForm).val();
                        options.term = jQuery('#term', documentsForm).val();
                    }
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {editable: false, type: "string"},
                        full_name: {editable: false, type: "string"},
                        size: {editable: false, type: "string"},
                        creator_full_name: {editable: false, type: "string"},
                        createdOn: {editable: false, type: "string"}
                    }
                }
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            batch: true, pageSize: 20, serverPaging: false, serverFiltering: false, serverSorting: false
        });
        $documentsGridOptions = {
            autobind: true,
            dataSource: documentsDataSrc,
            columns: [
                {
                    field: "full_name",
                    template: '<i fileId="#= id #" class="fs-common-icon #= (type == \'folder\') ? \'selectable \' + getExtIcon(\'folder\') : getExtIcon(extension) #"></i><i class="iconLegal iconPrivacy#= parseInt(private) ? \'yes\' : \'no\' #"></i><a href="javascript:void(0)" onclick="#= type == \'folder\'# ? loadDirectoryContent(\'#= id #\', \'#= addslashes(lineage) #\') : downloadFile(\'#= id #\', false, \'customer-portal\')" >#= full_name #</a>',
                    title: _lang.name
                },
                {field: "size", template: "#= size > 0 ? (size < (1024 * 1024) ? kendo.toString(size / 1024, '0.00 (KB)') : kendo.toString(size / (1024 * 1024), '0.00 (MB)')) : '' # ", title: _lang.size},
                {field: "creator_full_name", title: _lang.addedBy},
                {field: "createdOn", title: _lang.addedOn}
            ],
            editable: true,
            filterable: false,
            height: 500,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: "single",
            sortable: {mode: "multiple"},
            toolbar: [{
                    name: "toolbar-menu",
                    template:
                            '<div class="col-md-3">'
                            + '<div class="input-group col-md-12">'
                            + '<input type="text" class="form-control search" placeholder="' + _lang.search + '" id="attachmentLookUp" onkeyup="attachmentQuickSearch(event.keyCode, this.value);" title="' + _lang.search + '" />'
                            + '</div>'
                            + '</div>'
                }],
            columnMenu: {messages: _lang.kendo_grid_sortable_messages}
        };
        $documentsGrid.kendoGrid($documentsGridOptions);
    } catch (e) {
    }
}
