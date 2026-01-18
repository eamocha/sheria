var $documentsGrid, documentsForm, $documentsGridOptions, $attachmentDocumentForm, moduleDocumentPathTypeValues, $urlDocumentGridOptions, $urlDocumentGrid, urlsForm, $enableQuickSearch = false, $urlDocumentForm, allowedUploadSizeMegabite, directoryForm, $directoryDocumentForm;
var officeFileTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx', 'xlt', 'xltx', 'docm', 'xlsm', 'xltm', 'pptm', 'slk', 'sylk'];
var viewableFileTypes = ['doc', 'docx', 'xls', 'xlsx', 'xlt', 'xltx', 'ppt', 'pps', 'pptx', 'pdf', 'gif', 'jpg', 'jpeg', 'png', 'html', 'txt', 'htm', 'mpg', 'mp3', 'mp4', 'flv', 'mov', 'wav', '3gp', 'avi', 'jfif'];
var allowedConverterFileTypes = ['pptx','ppt','docx','doc','wps','dotx','docm','dotm','dot','odt','xlsx','xls'];
var selectedItems = [];
var newCreatedFolders = [];
var foldersHaveNewChildren = [];

function copyFolderPath(folderPath) {
    var pathForm = jQuery('#pathForm');
    $url = document.location.href;
    $modelUrl = $url.split("#")[0];
    if (undefined !== folderPath && folderPath !== '') {
        folderPath = $modelUrl + '#' + folderPath;
    } else {
        folderPath = $modelUrl + '#' + jQuery('#lineage', documentsForm).val();
    }
    var msie = window.navigator.userAgent.indexOf("MSIE ");
    if (msie > 0) {
        jQuery('#folderPath', pathForm).val(folderPath);
        setTimeout(function () {
            jQuery('#folderPath', pathForm).focus().select();
            jQuery('#label').removeClass('d-none');
        }, 200);
        pathForm.removeClass('d-none');
        jQuery('#openPathBtn', pathForm).addClass('d-none');
        jQuery('.modal', '#pathForm').modal({
            keyboard: false
        });
    } else {
        copyTextToClipboard(folderPath);
        pinesMessage({ty: 'success', m: _lang.pathIsCopiedToClipboard});
        return true;
    }
}
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
    document.location.hash = '';
    jQuery('#lineage', documentsForm).val(folderLineage);
    jQuery('#term', documentsForm).val('');
    jQuery('#attachmentLookUp', $documentsGrid).val('');
    resetPagination($documentsGrid);
    resetSelectedDocuments();
}
function documentsSearch() {
    if (undefined === $documentsGrid.data('kendoGrid')) {
        if (moduleDocumentTypeValues === "" || moduleDocumentStatusValues === "") {
            $documentsGridOptions.columns.splice(3, 2);
        } else {
            $documentsGridOptions.columns[3].values = moduleDocumentTypeValues;
            $documentsGridOptions.columns[4].values = moduleDocumentStatusValues;
        }
        $documentsGrid.kendoGrid($documentsGridOptions);
        return false;
    }
    resetPagination($documentsGrid);
    return false;
}
function getExtIcon(ext) {
    ext = ext.toLowerCase();
    $extensions = { 'doc': 'fs-word-icon', 'docx': 'fs-word-icon', 'docm': 'fs-word-icon', 'xls': 'fs-excel-icon', 'xlsx': 'fs-excel-icon', 'xlt': 'fs-excel-icon', 'xltx': 'fs-excel-icon', 'ppt': 'fs-powerpoint-icon', 'pps': 'fs-powerpoint-icon', 'pptx': 'fs-powerpoint-icon', 'pdf': 'fs-pdf-icon', 'tif': 'fs-image-icon', 'jpg': 'fs-image-icon', 'png': 'fs-image-icon', 'gif': 'fs-image-icon', 'jpeg': 'fs-image-icon', 'bmp': 'fs-image-icon', 'folder': 'fs-folder-icon', 'folder_empty':'fs-folder-empty-icon', 'msg': 'fs-email-icon', 'eml': 'fs-email-icon', 'vcf': 'fs-email-icon', 'html': 'fs-email-icon', 'htm': 'fs-email-icon', 'txt': 'fs-text-icon', 'zip': 'fs-compress-icon', 'rar': 'fs-compress-icon', 'avi': 'fs-video-icon', 'mpg': 'fs-video-icon', 'mp4': 'fs-video-icon', 'mp3': 'fs-video-icon', 'flv': 'fs-video-icon', 'unknown': 'fs-unknown-icon', 'mov': 'fs-video-icon', 'wav': 'fs-video-icon', '3gp': 'fs-video-icon', 'avi': 'fs-video-icon', 'xlsm': 'fs-excel-icon', 'xltm': 'fs-excel-icon', 'pptm': 'fs-powerpoint-icon', 'slk': 'fs-excel-icon', 'sylk': 'fs-excel-icon', 'jfif': 'fs-image-icon'};
    return (undefined === $extensions[ext]) ? $extensions['unknown'] : $extensions[ext];
}
function loadDirectoryContent(id, documentLineage) {
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {id: id, module: module},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + moduleController + "/check_folder_privacy",
        success: function (response) {
            if (!response.result) {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.notAccessibleFolder});
                return false;
            }
            jQuery('#term', documentsForm).val('');
            jQuery('#attachmentLookUp', $documentsGrid).val('');
            jQuery('#lineage', documentsForm).val(documentLineage);
            resetPagination($documentsGrid);
            resetSelectedDocuments();
            document.location.hash = '';
            return true;
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function createFolder() {
    var documentFolderDialog = jQuery("#documentFolderContainer");
    var documentFolderForm = jQuery("#documentFolderForm");
    if (!documentFolderDialog.is(':data(dialog)')) {
        jQuery('#name', documentFolderForm).keydown(function (e) {
            if (e.keyCode === 13) {
                jQuery('#createFolderSaveFormId').click();
            }
        });
        documentFolderForm.validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false,
            'custom_error_messages': {'#name': {'required': {'message': _lang.validation_field_required.sprintf([_lang.name])}}}
        });
        documentFolderDialog.dialog({
            autoOpen: true,
            buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info',
                    id: 'createFolderSaveFormId',
                    click: function () {
                        var dataIsValid = jQuery(documentFolderForm, this).validationEngine('validate');
                        if (dataIsValid) {
                            jQuery('#createFolderSaveFormId').attr('disabled', 'disabled');
                            jQuery('#lineage', documentFolderForm).val(jQuery('#lineage', documentsForm).val());
                            var that = this;
                            var formData = jQuery(documentFolderForm, this).serialize();
                            jQuery.ajax({
                                data: formData,
                                dataType: 'JSON',
                                type: 'POST',
                                url: getBaseURL() + moduleController + '/create_folder',
                                success: function (response) {
                                    if (response.status) {
                                        if ($documentsGrid.length) {
                                            showHideEmptyGridMessage("hide");
                                            resetPagination($documentsGrid);
                                        }
                                        pinesMessage({ty: 'information', m: response.message});
                                    } else {
                                        jQuery("#output", that).html('&nbsp;');
                                        if (undefined === response.validationErrors) {
                                            if (!response.status) {
                                                pinesMessage({ty: 'error', m: response.message});
                                            }
                                        } else {
                                            for (i in response.validationErrors) {
                                                jQuery('#' + i, that).addClass("invalid");
                                                pinesMessage({ty: 'error', m: response.validationErrors[i]});
                                            }
                                            if (response.validationErrors === "")
                                                pinesMessage({ty: 'error', m: _lang.errorCreatingDirectory});
                                        }
                                    }
                                    jQuery(that).dialog("close");
                                },
                                error: defaultAjaxJSONErrorsHandler
                            });
                        }
                    }
                },
                {
                    text: _lang.cancel,
                    'class': 'btn btn-link',
                    click: function () {
                        resetAttachmentDocumentFolder();
                        jQuery(this).dialog("close");
                    }
                }],
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(that, '50%', '220');
                }));
                resizeNewDialogWindow(that, '50%', '220');
                jQuery('#name', that).focus();
            },
            close: function () {
                resetAttachmentDocumentFolder();
                jQuery(window).unbind('resize');
            },
            draggable: true,
            modal: false,
            resizable: true,
            responsive: true,
            title: _lang.createFolder
        });
    } else {
        documentFolderDialog.dialog("open");
    }
}
function submitAttachmentDocumentForm() {
    $attachmentDocumentForm.submit();
}
function uploadFile() {
    var attachmentDialog = jQuery("#attachmentDialog");
    if (!attachmentDialog.is(':data(dialog)')) {
        ctrlS(submitAttachmentDocumentForm);
        attachmentDialog.dialog({
            autoOpen: true,
            buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info',
                    click: function () {
                        $attachmentDocumentForm.validationEngine({
                            validationEventTrigger: "submit",
                            autoPositionUpdate: true,
                            promptPosition: 'bottomRight',
                            scroll: false
                        });
                        var dataIsValid = jQuery($attachmentDocumentForm, this).validationEngine('validate');
                        if (dataIsValid) {
                            showHideEmptyGridMessage("hide");
                            jQuery('#lineage', $attachmentDocumentForm).val(jQuery('#lineage', documentsForm).val());
                            $attachmentDocumentForm.submit();
                        }
                    }
                },
                {
                    text: _lang.cancel,
                    class: 'btn btn-link',
                    click: function () {
                        resetAttachmentDocumentForm();
                        jQuery(this).dialog("close");
                    }
                }],
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                var that = jQuery(this);
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(that, '60%', '350');
                }));
                resizeNewDialogWindow(that, '60%', '350');
            },
            close: function () {
                resetAttachmentDocumentForm();
                jQuery(window).unbind('resize');
            },
            draggable: true,
            modal: false,
            title: _lang.uploadFile,
            resizable: true,
            responsive: true
        });
    } else {
        attachmentDialog.dialog("open");
    }
}
function submitDirectoryDocumentForm() {
    $directoryDocumentForm.submit();
}
function uploadDirectory() {
    var directoryDialog = jQuery("#directoryDialog");
    if (!directoryDialog.is(':data(dialog)')) {
        ctrlS(submitDirectoryDocumentForm);
        directoryDialog.dialog({
            autoOpen: true,
            buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info',
                    click: function () {
                        var folderext = document.getElementById('uploadDir').files;
                        var paths = "";
                        for (var i = 0, f; f = folderext[i]; ++i){
                            paths += folderext[i].webkitRelativePath+"###";
                        }
                        jQuery('#folderext', $directoryDocumentForm).val(paths);
                        $directoryDocumentForm.validationEngine({
                            validationEventTrigger: "submit",
                            autoPositionUpdate: true,
                            promptPosition: 'bottomRight',
                            scroll: false
                        });
                        var dataIsValid = jQuery($directoryDocumentForm, this).validationEngine('validate');
                        if (dataIsValid) {
                            showHideEmptyGridMessage("hide");
                            jQuery('#lineage', $directoryDocumentForm).val(jQuery('#lineage', directoryForm).val());
                            $directoryDocumentForm.submit();
                        }
                    }
                },
                {
                    text: _lang.cancel,
                    class: 'btn btn-link',
                    click: function () {
                        resetDirectoryDocumentForm();
                        jQuery(this).dialog("close");
                    }
                }],
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                var that = jQuery(this);
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(that, '60%', '350');
                }));
                resizeNewDialogWindow(that, '60%', '350');
            },
            close: function () {
                resetDirectoryDocumentForm();
                jQuery(window).unbind('resize');
            },
            draggable: true,
            modal: false,
            title: _lang.uploadDirectory,
            resizable: true,
            responsive: true
        });
    } else {
        directoryDialog.dialog("open");
    }
}
function directoryDocumentFormSubmitAndStartUpload() {
    jQuery('#directoryFormContainer', '#directoryDialog').addClass('d-none');
    jQuery('#loading', '#directoryDialog').removeClass('d-none');
    return true;
}
function uploadDirectoryDocumentDone(message, type) {
    if (message)
        pinesMessage({ty: type, m: message});
    jQuery("#directoryDialog").dialog("close");
    resetPagination($documentsGrid);
    jQuery('#directoryFormContainer', '#directoryDialog').removeClass('d-none');
    jQuery('#loading', '#directoryDialog').addClass('d-none');
}
function resetDirectoryDocumentForm() {
    var directoryDialog = jQuery("#directoryDialog");
    jQuery($directoryDocumentForm, directoryDialog).validationEngine('hide');
    jQuery($directoryDocumentForm, directoryDialog)[0].reset();
}
function attachmentDocumentFormSubmitAndStartUpload() {
    jQuery('#attachmentFormContainer', '#attachmentDialog').addClass('d-none');
    jQuery('#loading', '#attachmentDialog').removeClass('d-none');
    return true;
}
function uploadDocumentDone(message, type) {
    if (message)
        pinesMessage({ty: type, m: message});
    jQuery("#attachmentDialog").dialog("close");
    resetPagination($documentsGrid);
    jQuery('#attachmentFormContainer', '#attachmentDialog').removeClass('d-none');
    jQuery('#loading', '#attachmentDialog').addClass('d-none');
}
function resetAttachmentDocumentFolder() {
    var documentFolderDialog = jQuery("#documentFolderContainer");
    var documentFolderForm = jQuery("#documentFolderForm");
    jQuery(documentFolderForm, documentFolderDialog).validationEngine('hide');
    jQuery('#name', documentFolderDialog).removeClass('invalid');
    jQuery(documentFolderForm, documentFolderDialog)[0].reset();
}
function resetAttachmentDocumentForm() {
    var attachmentDialog = jQuery("#attachmentDialog");
    jQuery($attachmentDocumentForm, attachmentDialog).validationEngine('hide');
    jQuery($attachmentDocumentForm, attachmentDialog)[0].reset();
}
function deleteRow(id) {
    if (confirm(_lang.confirmationDeleteFile)) {
        jQuery.ajax({
            url: getBaseURL() + moduleController + '/document_delete',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'docId': id
            },
            success: function (response) {
                pinesMessage({ty: response.status ? 'information' : 'error', m: response.status ? _lang.deleteRecordSuccessfull : _lang.recordNotDeleted});
                $urlDocumentGrid.data('kendoGrid').dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function deleteDocument(id, versionDocument) {
    versionDocument = versionDocument || false;
    if (confirm(_lang.confirmationDeleteFile)) {
        jQuery.ajax({
            url: getBaseURL() + moduleController + '/delete_document',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'document_id': id,
                newest_version: !versionDocument
            },
            success: function (response) {
                pinesMessage({ty: response.status ? 'information' : 'error', m: response.message});
                if (response.status) {
                    if (versionDocument) {
                        jQuery('#version-item-' + id).remove();
                    } else {
                        resetPagination($documentsGrid);
                    }
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function renameDocument(id, name, fullName, type) {
    name = prompt(_lang.rename + ' "' + decodeURIComponent(fullName) + '"', decodeURIComponent(name));
    if (null !== name && "" !== name) {
        actionName = type == 'file' ? 'rename_file' : 'rename_folder';
        jQuery.ajax({
            url: getBaseURL() + moduleController + '/' + actionName,
            type: 'POST',
            dataType: 'JSON',
            data: {
                'document_id': id,
                'new_name': name
            },
            success: function (response) {
                if (!response.status) {
                    pinesMessage({ty: 'error', m: response.message});
                } else {
                    pinesMessage({ty: 'success', m: response.message});
                }
                if (undefined != $documentsGrid.data('kendoGrid')) {
                    $documentsGrid.data('kendoGrid').dataSource.read();
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function searchUrlDocument() {
    if (undefined === $urlDocumentGrid.data('kendoGrid')) {
        $urlDocumentGridOptions.columns[1].values = moduleDocumentPathTypeValues;
        $urlDocumentGridOptions.columns[5].values = moduleDocumentTypeValues;
        $urlDocumentGridOptions.columns[6].values = moduleDocumentStatusValues;
        $urlDocumentGrid.kendoGrid($urlDocumentGridOptions);
        customGridToolbarCSSButtons();
        return false;
    }
    $urlDocumentGrid.data('kendoGrid').dataSource.read();
    return false;
}
function loadAttachmentsGrid() {
    var recordIsVisible = jQuery('#visibleToCP').val();
    var recordIsVisibleinAP = jQuery('#visibleToAP').val();
    try {
        var documentsDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + moduleController + "/load_documents",
                    dataType: "JSON",
                    type: "POST",
                    async: false,
                    complete: function (XHRObj) {
                        jQuery('#loader-global').hide();
                        if (XHRObj.responseText == 'access_denied') {
                            return false;
                        }
                        if (XHRObj.responseText == 'login_needed') {
                            return false;
                        }
                        $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        if (undefined !== $response.error) {
                            pinesMessage({ty: 'error', m: $response.error});
                            jQuery('#lineage', documentsForm).val($response.initialModelPath);
                            $documentsGrid.data('kendoGrid').dataSource.read();
                        } else {
                            jQuery('#lineage', documentsForm).val($response.lineage);
                            updateCrumbLink($response.crumbLinkData);
                            if (typeof disableInactiveBreadCrumb === "function") {
                                disableInactiveBreadCrumb();
                            }
                        }
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                        animateDropdownMenuInGrids('documentsGrid');
                        if(!$response.allowDownloadFolder){
                            jQuery('.download-folder-trigger').addClass('d-none');
                            jQuery('.bulk-download-folder-trigger').addClass('d-none');
                        }
                    },
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    }
                },
                update: {
                    url: getBaseURL() + moduleController + "/edit_documents",
                    dataType: "jsonp",
                    type: "POST",
                    complete: function (XHRObj) {
                        $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        if (undefined !== $response.validation_errors && $response.validation_errors) {
                            for (i in $response.validation_errors) {
                                pinesMessage({ty: 'error', m: $response.validation_errors[i]});
                            }
                        }
                        $documentsGrid.data('kendoGrid').dataSource.read();

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
                    jQuery('#versions-list').addClass('d-none').html('');
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {editable: false, type: "string"},
                        full_name: {editable: false, type: "string"},
                        children_count: {editable: false, type: "string"},
                        document_type_id: {field: "document_type_id"},
                        document_status_id: {field: "document_status_id"},
                        comment: {type: "string"},
                        size: {editable: false, type: "string"},
                        display_creator_full_name: {editable: false, type: "string"},
                        display_created_on: {editable: false, type: "string"},
                        modifier_full_name: {editable: false, type: "string"},
                        modifiedOn: {editable: false, type: "string"}
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
                            row['full_name'] = escapeHtml(row['full_name']);
                            row['lineage'] = escapeHtml(row['lineage']);
                            row['parent_lineage'] = escapeHtml(row['parent_lineage']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
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
                    template: '# if(parseInt(is_accessible)){ # <input type="checkbox" class="row-checkbox hidden" title="' + _lang.select + '"/> # } #',
                    headerTemplate: '<input type="checkbox" class="row-checkbox-all" title="' + _lang.selectAll + '"/>',
                    width: '28px'
                },
                {
                    field: "id",
                    template:
                            (recordIsVisible == 1 ? '#= visible_in_cp == "1" ? (type == "folder" ? \'<span class="cp-flag folder-flag"></span>\' : \'<span class="cp-flag file-flag"></span>\') : \'\'#' : '') +
                            '<div class="dropdown #= !parseInt(is_accessible) ? \'hide\' : \'\' #" data-index="#= id #"  id="docs-actions-menu_#= id #">' + '<button id="dms-action-wheel" class="btn btn-default btn-xs"  data-toggle="dropdown"><i class="purple_color fa-solid fa-gear"></i> <span class="caret no-margin"></span></button>' +
                            '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel" id="docs-dropdown-menu_#= id #">' +
                            '<a class="dropdown-item #= (type == \'file\' && extension.toLowerCase() == \'pdf\')? \'\' : \'hide\' #" data-title="#= full_name #" target=\'_blank\' href="#= getBaseURL() + moduleController + \'/view_document/\' + id + \'/\' + encodeURIComponent(rawurlencode(full_name)) #">' + _lang.view + '</a>' +
                            '<a class="dropdown-item #=(type===\'folder\' ||  jQuery.inArray(extension.toLowerCase(), allowedConverterFileTypes)=="-1" ) ? \'hide\' : \'\'#" href="##" onclick="previewFile(#= id #, \'#= module #\', #= module_record_id #, \'#= addslashes(parent_lineage) #\', \'#= extension #\'); return false;">' + _lang.preview + '</a>' +
                            '<a  class="dropdown-item #=(type===\'folder\') ? \'hide\' : \'\'#" href="##" onclick="downloadFile(#= id #, true); return false;">' + _lang.download + '</a>' +
                            '<a  class="dropdown-item download-folder-trigger #=(type!==\'folder\') ? \'hide\' : \'\'#" href="##" onclick="downloadFolder(#= id #); return false;">' + _lang.downloadFolder + '</a>' +
                            '# if (system_document != "1") { # <a class="dropdown-item" href="##" onclick="renameDocument(#= id #, \'#= addslashes(encodeURIComponent(name)) #\', \'#= addslashes(encodeURIComponent(full_name)) #\', \'#= type #\'); return false;">' + _lang.rename + '</a>#} #' +
                            '# if (system_document != "1") { # <a class="dropdown-item" href="##" onclick="deleteDocument(#= id #); return false;">' + _lang.deleteRow + '</a></li> #} #' +
                            '<a class="dropdown-item" style="#= (type!==\'folder\' || (name == \'Matter Notes Attachments\' && display_created_by_channel === \'CP\')) ?\'display: none;\' : \'\'  #" href="##" onclick="shareFolder(#= id #, #= private #); return false;">' + _lang.shareWith + '</a>' +
                            '<a class="dropdown-item" style="#= (type===\'folder\') ? \'\' : \'display: none;\'  #" href="##" onclick="copyFolderPath(\'#= addslashes(lineage) #\'); return false;">' + _lang.copyFolderPath + '</a>' +
                            ( recordIsVisible == 1 ? '# if ((module == "case" || module == "caseContainer") && name != "Matter Notes Attachments") { # <a class="dropdown-item" href="##" onclick="showHideInCp(\'#= id #\', \'#= visible_in_cp #\', \'#= type #\'); return false;"> #= visible_in_cp == 1 ? _lang.hideInCustomerPortal : _lang.showInCustomerPortal #</a> #} #' : '') +
                            '<a class="dropdown-item" href="##" onclick="showHideInAp(\'#= id #\', \'#= visible_in_ap #\', \'#= type #\'); return false;"> #= visible_in_ap == 1 ? _lang.hideInAdvisorPortal : _lang.showInAdvisorPortal #</a>' +
                            '<a class="dropdown-item" href="##" onclick="moveDocument(#= id #, ' + (moduleRecordId !== '' ? moduleRecordId : "null") + ', false); return false;">' + _lang.move + '</a>' +
                            '<a class="dropdown-item #= type != \'file\' ? \'hide\' : \'\' #" href="javascript:;" onclick="listFileVersions(#= id #);">' + _lang.listVersions + '</a>' +
                            '# if (jQuery(\'\\\\#term\').val()) { # <a class="dropdown-item" href="javascript:;" onclick="openFolder(\'#= addslashes(parent_lineage) #\');">' + _lang.openLocation + '</a> # } #' +
                            '</div>' +
                            '</div>' +
                            '<i fileId="#= id #" class="pull-right fs-common-icon #= (type == \'folder\') ?  (children_count >0) ? \'selectable \' + getExtIcon(\'folder\') : \'selectable \' + getExtIcon(\'folder_empty\') : getExtIcon(extension) #" onclick="toggleFileIconSelection(this, \'#= type #\')"></i>',
                    sortable: false, filterable: false, title: _lang.dms_actions, width: '81px', attributes: {
                        class: "actions-cell flagged-gridcell"
                    }
                },
                {
                    field: "full_name",
                    template: '<i class="iconLegal iconPrivacy#= parseInt(private) ? \'yes\' : \'no\' # "></i><a href="javascript:;" class="v-align-middle" # if (type == \'file\') { # title="#= _lang.totalOfVersions.sprintf([version]) #" onclick="downloadFile(#= id #, true); return false;" # } else { # onclick="loadDirectoryContent(#= id # ,\'#= addslashes(lineage) #\')" # } #>#= full_name #</a>&nbsp;#if(type == \'file\'){#<button class="btn btn-link no-outline#if(getCookie(inlineEditingToolCookie) == null){# btn-dimmed#}#" title="#= _lang.openDocumentInEditingTool#" onclick="editDocument(#= id #, \'#= module #\', #= module_record_id #, \'#= addslashes(parent_lineage) #\', \'#= extension #\')"><i class="fa-solid fa-up-right-from-square"/></button>#}#',
                    title: _lang.name,
                    width: '500px'
                },
                {field: "document_type_id", title: _lang.type, width: '120px', template: "#= (document_type_id == null) ? ' ' : helpers.getObjectFromArr(moduleDocumentTypeValues, 'text', 'value', document_type_id) #"},
                {field: "document_status_id", title: _lang.status, width: '120px', template: "#= (document_status_id == null) ? ' ' : helpers.getObjectFromArr(moduleDocumentStatusValues, 'text', 'value', document_status_id) #"},
                {field: "comment", title: _lang.keyWords, width: '144px'},
                {field: "size", template: "#= size > 0 ? (size < (1024 * 1024) ? kendo.toString(size / 1024, '0.00 (KB)') : kendo.toString(size / (1024 * 1024), '0.00 (MB)')) : '' # ", title: _lang.size, width: '80px'},
                {field: "display_created_on", title: _lang.addedOn, width: '136px'},
                {field: "display_creator_full_name", title: _lang.addedBy, width: '140px'},
                {field: "modifier_full_name", title: _lang.modifiedBy, width: '140px'},
                {field: "modifiedOn", title: _lang.modifiedOn, width: '136px'},
            ],
            selectable: false,
            editable: true,
            filterable: false,
            height: 500,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: false,
                    sortable: {mode: "multiple"},
            toolbar: [{
                    name: "toolbar-menu",
                    template:
                                '<div class="col-md-3 p-0">'
                                    + '<div class="input-group col-md-12">'
                                        + '<input type="text" class="form-control search" placeholder="' + _lang.search + '" id="attachmentLookUp" onkeyup="attachmentQuickSearch(event.keyCode, this.value);" title="' + _lang.search + '" />'
                                    + '</div>'
                                + '</div>'
                                + '<div class="d-flex col-md-3 pull-right justify-content-between">'
                                    + '<a href="' + getBaseURL() + 'integrations' + '" class="' + (displayIntegrationButton ? '' : 'hide ') + 'btn btn-link">' + _lang.integrateWithDropbox + '</a>'
                                    + '<div class="btn-group pull-right" id="docs-actions-demo-open">'
                                        + '<div class="dropdown">'
                                            + '<button type="button" class="btn btn-info dropdown-toggle gridActionsButton" id="docs-dropdown-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' + _lang.actions
                                                + ' <span class="caret"></span>'
                                                + ' <span class="sr-only">Toggle Dropdown</span>'
                                            + '</button>'
                                            + '<div class="dropdown-menu dropdown-menu-right action-add-btn" aria-labelledby="docs-dropdown-menu">'
                                                + '<button class="dropdown-item btn-action" href="javascript:;" onclick="uploadFile();">' + _lang.uploadFile + '</button>'
                                                + '# if (installationType == "on-server") { # <button class="dropdown-item btn-action" href="javascript:;" onclick="uploadDirectory();">' + _lang.uploadDirectory + '</button> # } #'
                                                + '<button class="dropdown-item btn-action" href="javascript:;" onclick="createFolder();">' + _lang.createFolder + '</button>'
                                                + '<a class="dropdown-item" href="javascript:;" onclick="copyFolderPath();">' + _lang.copyFolderPath + '</a>'
                                                + '<button class="dropdown-item btn-action" href="javascript:;" onclick="generateDocument();">' + _lang.generateDocument + '</button>'
                                                + '<button class="dropdown-item btn-action" href="javascript:;" onclick="moveDocument(-1, ' + (moduleRecordId !== '' ? moduleRecordId : 'null') + ', true); return false;">' + _lang.moveAll + '</button>'
                                                + '<a class="dropdown-item" class"bulk-download-folder-trigger" href="javascript:;" onclick="downloadSelectedFilesAndFolder();">' + _lang.downloadSelectedFilesAndFolder + '</a>'
                                                + '<a class="dropdown-item" id="download-actions-demo-open" href="javascript:;" onclick="downloadInlineEditingTool();">' + _lang.downloadInlineEditingTool + '</a>'
                                            + '</div>'
                                        + '</div>'
                                    + '</div>'
                                + '</div>'
                }, {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}
            ],
            dataBound: gridDataBound,
            columnMenu: {messages: _lang.kendo_grid_sortable_messages}
        };
    } catch (e) {
    }

    // on checkbox clicked
    $documentsGrid.on('click', '.row-checkbox', function (e) {
        e.stopPropagation();

        toggleSelectItem(jQuery(this), jQuery(this).prop('checked'));
        uncheckCheckboxAll();
    });

    // on select-all checkbox clicked
    $documentsGrid.on('click', '.row-checkbox-all', function () {
        var checkbox = jQuery(this);
        var checked = checkbox.prop('checked');

        // select all items
        if (checked) {
            jQuery('.row-checkbox', $documentsGrid).each(function () {
                toggleSelectItem(jQuery(this), true);
            });
        } else { // deselect all items
            jQuery('.row-checkbox', $documentsGrid).each(function () {
                toggleSelectItem(jQuery(this), false);
            });
        }
    });

    // on hover over grid item
    $documentsGrid.on('mouseenter', 'tbody[role="rowgroup"] tr[role="row"]', function () {
        toggleShowCheckbox(jQuery(this).find('.row-checkbox'), true);
    }).on('mouseleave', 'tbody[role="rowgroup"] tr[role="row"]', function () {
        toggleShowCheckbox(jQuery(this).find('.row-checkbox'), false);
    }).on('mouseover', 'tbody[role="rowgroup"] tr[role="row"]', function () {
        jQuery(this).find('.row-checkbox').removeClass('hidden');
    });
    gridEvents();
}

/**
 * check all the checkboxes of the selected items
 * @returns void
 */
function checkSelectedItems() {
    var grid = $documentsGrid.data("kendoGrid");

    var selectedItems = getSelectedItems();

    for (var i = 0; i < selectedItems.length; i++) {
        var itemId = selectedItems[i];
        var item = jQuery('tbody[role="rowgroup"] tr[role="row"]:has(.actions-cell .dropdown[data-index="' + itemId + '"])', $documentsGrid);

        item.addClass("k-state-selected");
        var checkbox = jQuery('.row-checkbox', item);
        var dataItem = grid.dataItem(item);

        if (dataItem !== null) {
            toggleSelectItem(checkbox, true, dataItem);
        }
    }
}

/**
 * select/deselect the grid item
 * 
 * @param {Object} checkbox
 * @param {Boolean} checked
 * @param {Object} dataItem
 * @returns void
 */
function toggleSelectItem(checkbox, checked, dataItem) {
    var item = checkbox.parents('tr');
    var grid = $documentsGrid.data("kendoGrid");
    dataItem = dataItem || grid.dataItem(item);

    if (dataItem !== null) {
        if (checked) {
            item.addClass("k-state-selected");
            checkbox.prop('checked', true);
        } else {
            item.removeClass("k-state-selected");
            checkbox.prop('checked', false);
        }

        toggleShowCheckbox(checkbox, checked);

        var itemIndex = itemExists(dataItem.id);

        // if item doesn't exist in the items array, push it
        if (itemIndex < 0) {
            insertSelectedItems(selectedItems.length, dataItem.id, checked);
        } else {
            insertSelectedItems(itemIndex, dataItem.id, checked);
        }
    }
}

/**
 * 
 * @param {type} itemId
 * @returns {Number} item index
 */
function itemExists(itemId) {
    for (var i = 0; i < selectedItems.length; i++) {
        var item = selectedItems[i];

        if (item.id === itemId) {
            return i;
        }
    }

    return -1;
}

/**
 * 
 * @param {type} itemIndex
 * @param {type} itemId
 * @param {type} status
 * @returns void
 */
function insertSelectedItems(itemIndex, itemId, status) {
    selectedItems[itemIndex] = {id: itemId, checked: status};
}

/**
 * show/hide the grid item's checkbox
 * 
 * @param {Object} checkbox
 * @param {Boolean} checked
 * @returns void
 */
function toggleShowCheckbox(checkbox, checked) {
    if (checkbox.prop('checked') || (checked && !checkbox.prop('checked'))) {
        checkbox.removeClass('hidden');
    } else if (!checked && !checkbox.prop('checked')) {
        checkbox.addClass('hidden');
    }
}

/**
 * uncheck the header checkbox (Select All)
 * 
 * @returns void
 */
function uncheckCheckboxAll() {
    jQuery('.row-checkbox-all', $documentsGrid).prop('checked', false);
}

/**
 * get the IDs of selected items only
 * 
 * @returns {Array}
 */
function getSelectedItems() {
    var arr = [];

    for (var i = 0; i < selectedItems.length; i++) {
        var item = selectedItems[i];
        if (item.checked === true) {
            arr.push(item.id);
        }
    }

    return arr;
}

function gridDataBound(e) {
    if (jQuery("#attachmentLookUp").val() != "") {
        $documentsGrid.data('kendoGrid').showColumn('location');
    } else {
        $documentsGrid.data('kendoGrid').hideColumn('location');
    }
    var grid = e.sender;
    if (jQuery(".empty-grid-container").length == 0) {
        jQuery(e.sender.wrapper)
                .find('.k-grid-content table[role="grid"]')
                .before('<div class="empty-grid-container" id="empty-grid-container"><div class="empty-grid-content-container"><div class="empty-grid-content-image"><img src="assets/images/attachments-grid-empty.png"/></div><div class="empty-grid-content-text"><p class="empty-grid-content-first-text">' + _lang.dragAndDrop.emptyGridFirstMessage + '</p><p class="empty-grid-content-second-text">' + _lang.dragAndDrop.emptyGridSecondMessage + '</p></div></div></div>');
    }
    if (grid.dataSource.total() == 0 && jQuery("#attachmentLookUp").val() == "") {
        showHideEmptyGridMessage("showWithoutBorder");
    } else {
        showHideEmptyGridMessage("hide");
    }
    animateDropdownMenuInGrids('documentsGrid');
    checkSelectedItems();

    var rows = grid.tbody.find("[role='row']");

    rows.unbind("click");

    jQuery('tbody .row-checkbox', $documentsGrid).shiftcheckbox({
        onChange: function (checked) {
            toggleSelectItem(jQuery(this), checked);
        }
    });
}
function showHideEmptyGridMessage(flag) {
    if (flag == "show" && jQuery("#attachmentLookUp").val() == "") {
        jQuery('.empty-grid-container').addClass("empty-grid-container-on-hover");
    } else if (flag == "hide") {
        jQuery('.empty-grid-content-container').hide();
    } else if (flag == "noBorder") {
        jQuery('.empty-grid-container').removeClass("empty-grid-container-on-hover");
    } else if (flag == "showWithoutBorder") {
        jQuery('.empty-grid-content-container').show();
    }
}
function loadUrlGrid() {
    if (undefined !== $urlDocumentGrid) {
        var urlDocumentDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + moduleController + '/urls',
                    dataType: "JSON",
                    type: "POST",
                    complete: function (XHRObj) {
                        if (XHRObj.responseText == 'access_denied') {
                            ajaxAccessDenied();
                        }
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                        animateDropdownMenuInGrids('urlGrid');
                    }
                },
                update: {
                    url: getBaseURL() + moduleController + '/document_edit',
                    dataType: "jsonp",
                    type: "POST",
                    complete: function (XHRObj) {
                        if (XHRObj.responseText == 'access_denied') {
                            ajaxAccessDenied();
                            $urlDocumentGrid.data('kendoGrid').dataSource.read();
                        } else {
                            var response = jQuery.parseJSON(XHRObj.responseText || "null");
                            if (response.result) {
                                pinesMessage({ty: 'information', m: _lang.feedback_messages.updateDocumentsSuccessfully});
                                $urlDocumentGrid.data('kendoGrid').dataSource.read();
                            } else {
                                var errorMsg = '';
                                for (i in response.validationErrors) {
                                    errorMsg += '<li>' + i + ': ' + response.validationErrors[i] + '</li>';
                                }
                                if (errorMsg !== '') {
                                    pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                    $urlDocumentGrid.data('kendoGrid').dataSource.read();
                                }
                            }
                        }
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        return {
                            models: kendo.stringify(options.models)
                        };
                    } else {
                        options.filter = getFormFilters('documentSearchFilters');
                        options.returnData = 1;
                    }
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {editable: false, type: "integer"},
                        module_id: {type: "string"},
                        document_type_id: {field: "document_type_id", validation: {required: {message: _lang.validation_field_required.sprintf([_lang.type])}}},
                        document_status_id: {field: "document_status_id", validation: {required: {message: _lang.validation_field_required.sprintf([_lang.status])}}},
                        name: {type: "string"},
                        pathType: {type: "string"},
                        path: {type: "string", validation: {required: {message: _lang.validation_field_required.sprintf([_lang.url])}}},
                        comments: {type: "string"},
                        modifiedBy: {editable: false, type: "string"},
                        modifiedOn: {editable: false, type: "string"},
                        addedBy: {editable: false, type: "string"},
                        createdOn: {editable: false, type: "string"}
                    }
                }
            }, error: function (e) {
                if (e.xhr.responseText.validationErrors === '')
                    defaultAjaxJSONErrorsHandler(e.xhr);
            },
            batch: true, pageSize: 10, serverPaging: true, serverFiltering: true, serverSorting: true
        });
        $urlDocumentGridOptions = {
            autobind: true,
            dataSource: urlDocumentDataSrc,
            columnMenu: {messages: _lang.kendo_grid_sortable_messages},
            columns: [
                {field: "id", template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                            '<a class="dropdown-item" href="##" onclick="deleteRow(\'#= id #\'); return false;">' + _lang.deleteRow + '</a>' +
                            '<a class="dropdown-item" style=#= pathType === \'web\' ? "" : "display:none;" # class="dropdown-item" href="#= path #" target="_blanc">' + _lang.goTo + '</a>' +
                            '</div></div>',
                    sortable: false, filterable: false, title: _lang.actions, width: '70px'
                },
                {field: "pathType", title: _lang.urlType, values: [], width: '127px'},
                {field: "path", template: "#= pathType == 'web' ? validURL(path) : path #", title: _lang.url, width: '180px'},
                {field: "name", title: _lang.name, width: '120px'},
                {field: "comments", title: _lang.comments, width: '146px'},
                {field: "document_type_id", title: _lang.type, values: [], width: '120px'},
                {field: "document_status_id", title: _lang.status, values: [], width: '120px'},
                {field: "modifiedBy", title: _lang.modifiedBy, width: '140px'},
                {field: "modifiedOn", title: _lang.modifiedOn, width: '140px'},
                {field: "addedBy", title: _lang.addedBy, width: '140px'},
                {field: "createdOn", title: _lang.addedOn, width: '136px'}
            ],
            editable: true,
            filterable: false,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            height: 350,
            sortable: {mode: "multiple"},
            selectable: "single",
            toolbar: [{
                    name: "toolbar-menu",
                    template:
                            '<div class="col-md-3">'
                            + '<div class="input-group col-md-12">'
                            + '<input type="text" class="form-control search" placeholder="' + _lang.search + '" id="documentLookUp" onkeyup="documentQuickSearch(event.keyCode, this.value);" title="' + _lang.search + '" />'
                            + '</div>'
                            + '</div>'
                            + '<div class="col-md-2 pull-right">'
                            + '<div class="btn-group pull-right">'
                            + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">' + _lang.actions
                            + ' <span class="caret"></span>'
                            + '<span class="sr-only">Toggle Dropdown</span>'
                            + '</button>'
                            + '<div class="dropdown-menu action-add-btn uniquediv" aria-labelledby="dLabel" role="menu">'
                            + '<button class="dropdown-item btn-action" href="javascript:;" onclick="addUrlDocument()">' + _lang.addURL + '</button>'
                            + '</div>'
                            + '</div>'
                            + '</div>'
                }, {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}]
        };
    }
}
function getFormFilters(formId) {
    var filtersForm = jQuery('#' + formId);
    if (!$enableQuickSearch) {
        jQuery('#quickSearchFilterFieldsValue', filtersForm).val('');
        jQuery('#quickSearchFilterFieldsValue2', filtersForm).val('');
    }
    disableEmpty(filtersForm);
    var searchFilters = form2js(formId, '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}
function documentQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        jQuery('#documentQuickSearchFilterFieldsValue', '#documentSearchFilters').val(term);
        jQuery('#documentQuickSearchFilterFieldsValue2', '#documentSearchFilters').val(term);
        $urlDocumentGrid.data("kendoGrid").dataSource.read();
    }
}
function addUrlDocument() {
    var documentDialog = jQuery("#documentDialog");
    if (!documentDialog.is(':data(dialog)')) {
        //ctrlS(submitUrlDocumentForm);
        jQuery('#pathType').change(function () {
            if (this.value === 'web') {
                var pathFieldSelector = jQuery('#path');
                var pathFieldValue = pathFieldSelector.val();
                if (pathFieldValue === '' || (pathFieldValue.substring(0, 7) !== 'http://' && pathFieldValue.substring(0, 8) !== 'https://')) {
                    pathFieldSelector.val('http://' + pathFieldValue);
                }
            }
        });
        $urlDocumentForm.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false});
        documentDialog.dialog({
            autoOpen: true,
            buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info',
                    click: function () {
                        var dataIsValid = jQuery($urlDocumentForm, this).validationEngine('validate');
                        if (dataIsValid) {
                            var that = this;
                            var formData = jQuery($urlDocumentForm, this).serialize();
                            jQuery.ajax({
                                data: formData,
                                dataType: 'JSON',
                                type: 'POST',
                                url: getBaseURL() + moduleController + '/document_add',
                                success: function (response) {
                                    if (response.result) {
                                        resetUrlDocumentFormValues();
                                        jQuery(that).dialog("close");
                                        if ($urlDocumentGrid.length) {
                                            $urlDocumentGrid.data('kendoGrid').dataSource.read();
                                        }
                                    } else {
                                        jQuery("#output", that).html('&nbsp;');
                                        for (i in response.validationErrors) {
                                            jQuery('#' + i, that).addClass("invalid");
                                            pinesMessage({ty: 'error', m: response.validationErrors[i]});
                                        }
                                    }
                                },
                                error: defaultAjaxJSONErrorsHandler
                            });
                        }
                    }
                },
                {
                    text: _lang.cancel,
                    class: 'btn btn-link',
                    click: function () {
                        resetUrlDocumentFormValues();
                        jQuery(this).dialog("close");
                    }
                }],
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(documentDialog, '60%', '500');
                }));
                resizeNewDialogWindow(documentDialog, '60%', '500');
            },
            close: function () {
                resetUrlDocumentFormValues();
                jQuery(window).unbind('resize');
            },
            draggable: true, modal: false, title: _lang.addNewURL, resizable: true, responsive: true
        });
    } else {
        documentDialog.dialog("open");
    }
}
function submitUrlDocumentForm() {
    $urlDocumentForm.submit();
}
function resetUrlDocumentFormValues() {
    var documentDialog = jQuery("#documentDialog");
    jQuery($urlDocumentForm, documentDialog).validationEngine('hide');
    jQuery($urlDocumentForm, documentDialog)[0].reset();
}
function validURL(str) {
    if (new RegExp("([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?").test(str)) {
        return '<a href="' + str + '" target="_blank">' + str + '</a>';
    }
    return str;
}

function drag_and_drop() {
    var dragging = 0;
    $dropZone = new Dropzone('#dropzone-container', {
        url: getBaseURL() + moduleController + '/upload_file',
        paramName: 'uploadDoc',
        addRemoveLinks: true,
        parallelUploads: 1,
        maxFilesize: allowedUploadSizeMegabite,
        params: {
            dragAndDrop: true
        },
        clickable: false,
        previewsContainer: '#dragAndDrop',
        dragover: function (e) {
            if (dragging !== 0) {
                jQuery('#dragAndDrop').removeClass('d-none');
                return showHideEmptyGridMessage("show");
            }
            return true;
        },
        dragenter: function (e) {
            if (jQuery("#attachmentLookUp").val() != "") {
                jQuery('.dz-preview', '#dragAndDrop').each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            dragging++;
            jQuery('#dragAndDrop').removeClass('d-none');
            return showHideEmptyGridMessage("show");
        },
        dragleave: function (e) {
            if (jQuery("#attachmentLookUp").val() != "") {
                jQuery('.dz-preview', '#dragAndDrop').each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            dragging--;
            if (dragging === 0) {
                jQuery('#dragAndDrop').addClass('d-none');
                return showHideEmptyGridMessage("noBorder");
            }
            return true;
        },
        drop: function (e) {
            if (jQuery("#attachmentLookUp").val() != "") {
                jQuery('.dz-preview', '#dragAndDrop').each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            dragging++;
            return true;
        },
        accept: function (file, done) {
            if (jQuery("#attachmentLookUp").val() != "") {
                jQuery('.dz-preview', '#dragAndDrop').each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            if(csrfName.length > 0){
                this.options.params[csrfName] = csrfValue;
            }
            this.options.params['module'] = module;
            this.options.params['module_record_id'] = moduleRecordId;
            this.options.params['lineage'] = jQuery('#lineage', documentsForm).val();
            showHideEmptyGridMessage("hide");
            return done();
        },
        error: function (file, message) {
            file.previewElement.classList.add("dz-error");
            jQuery('#dragAndDrop').addClass('d-none');
            showHideEmptyGridMessage("noBorder");
            file.previewElement.querySelector("[data-dz-errormessage]").textContent = message;
            jQuery('.dz-preview', '#dragAndDrop').each(function () {
                jQuery(this).remove();
            });
            pinesMessage({ty: 'error', m: message});
            return true;
        },
        success: function (file, response) {
            if (jQuery("#attachmentLookUp").val() != "") {
                jQuery('.dz-preview', '#dragAndDrop').each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            if (response.status) {
                pinesMessage({ty: 'information', m: response.message});
                documentsSearch();
                file.previewElement.classList.add("dz-success");
            } else {
                if (typeof response == "string" && response == "login_needed") {
                    defaultAjaxHTMLErrorsHandler(response);
                } else {
                    var ty = 'error', msg = response.message;
                    if (response.status === undefined) {
                        msg = _lang.your_session_timed_out_please_login;
                    }
                    pinesMessage({ty: ty, m: msg});
                    file.previewElement.classList.add("dz-error");
                }
            }
            jQuery('.dz-remove', file.previewElement).remove();
            return true;
        },
        complete: function (file) {
            if (jQuery("#attachmentLookUp").val() != "") {
                jQuery('.dz-preview', '#dragAndDrop').each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            queuedFiles = this.getQueuedFiles();
            uploadingFiles = this.getUploadingFiles();
            if ((queuedFiles.length > 0 || uploadingFiles.length > 0)) {
                return true;
            } else {
                dragging = 0;
                jQuery('#dragAndDrop').addClass('d-none');
                showHideEmptyGridMessage("noBorder");
                jQuery('.dz-preview', '#dragAndDrop').each(function () {
                    jQuery(this).remove();
                });
                return true;
            }
        }
    });
}
function shareFolder(id, docPrivate) {
    $dialogId = jQuery("#sharedWithDialog");
    $dialogId.html('<div align="center"><img height="18" src="assets/images/icons/16/loader-submit.gif" /></div>');
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/share_folder',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'modeType': 'getHtml',
            'folder_id': id,
            'private': docPrivate
        },
        beforeSend: function () {
        },
        success: function (response) {
            $dialogId.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        click: function () {
                            var that = this;
                            var formData = jQuery("#sharedWithForm", $dialogId).serialize();
                            jQuery.ajax({
                                data: formData,
                                dataType: 'JSON',
                                type: 'POST',
                                url: getBaseURL() + moduleController + '/share_folder',
                                success: function (response) {
                                    if (response.status) {
                                        jQuery(that).dialog("close");
                                        $documentsGrid.data('kendoGrid').dataSource.read();
                                        pinesMessage({ty: 'information', m: response.message});
                                    } else {
                                        pinesMessage({ty: 'error', m: response.message});
                                    }
                                },
                                error: defaultAjaxJSONErrorsHandler
                            });
                        }
                    },
                    {
                        text: _lang.cancel,
                        'class': 'btn btn-link',
                        click: function () {
                            if (jQuery("#sharedWithForm", $dialogId).length)
                                jQuery("#sharedWithForm", $dialogId)[0].reset();
                            jQuery(this).dialog("close");
                        }
                    }],
                open: function () {
                    var that = jQuery(this);
                    that.removeClass('d-none');
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '320');
                    }));
                    resizeNewDialogWindow(that, '50%', '320');
                },
                close: function () {
                    if (jQuery("#sharedWithForm", $dialogId).length)
                        jQuery("#sharedWithForm", $dialogId)[0].reset();
                    jQuery(window).unbind('resize');
                },
                draggable: true,
                modal: false,
                responsive: true,
                resizable: true,
                title: _lang.shareWith
            });
            if (!response) {
                jQuery($dialogId).dialog("close");
                pinesMessage({ty: 'error', m: _lang.access_denied});
            } else {
                $dialogId.html(response.html);
                jQuery('#module', $dialogId).val(module);
                jQuery('#folder-id', $dialogId).val(id);
                jQuery('#lookupUsers', '#sharedWithDialog').autocomplete({autoFocus: false, delay: 600, source: function (request, response) {
                        request.term = request.term.trim();
                        jQuery.ajax({url: getBaseURL() + 'users/autocomplete/active', dataType: "json", data: request, error: defaultAjaxJSONErrorsHandler, success: function (data) {
                                if (data.length < 1) {
                                    response([{label: _lang.no_results_matched_for.sprintf([request.term]), value: '', record: {user_id: -1, term: request.term}}]);
                                } else {
                                    response(jQuery.map(data, function (item) {
                                        return {label: item.firstName + ' ' + item.lastName, value: '', record: item}
                                    }));
                                }
                            }});
                    }, minLength: 2, select: function (event, ui) {
                        if (ui.item.record.id > 0) {
                            setNewCaseMultiOption(jQuery('#selected_users', '#sharedWithDialog'), {id: ui.item.record.id, value: ui.item.record.firstName + ' ' + ui.item.record.lastName, name: 'watchers_users'});
                        }
                    }});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function attachmentQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        if (term === "") {
            jQuery('#lineage', documentsForm).val('');
        }
        jQuery('#term', documentsForm).val(term);
        resetPagination($documentsGrid);
    }
}
function sharedWithRadioOnClick(private) {
    if (parseInt(private)) {
        jQuery('#sharedWithMultiUsers', '#sharedWithForm').removeClass('d-none');
    } else {
        jQuery('#sharedWithMultiUsers', '#sharedWithForm').addClass('d-none');
    }
}
function searchByUrl() {
    $hashURL = document.location.hash;
    $folderPath = $hashURL.substring(1);
    if ($folderPath !== '') {
        jQuery('#lineage', documentsForm).val($folderPath);
        jQuery.ajax({
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            data: {lineage: $folderPath, module: module},
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + moduleController + "/check_folder_privacy",
            success: function (response) {
                if (!response.result) {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.notAccessibleFolder});
                    window.location.href = window.location.href.substr(0, window.location.href.indexOf('#'));
                } else {
                    loadAttachmentsGrid();
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        loadAttachmentsGrid();
    }
}
function generateDocument() {
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/generate_document/' + moduleRecordId,
        dataType: 'JSON',
        type: 'GET',
        data: {action: 'list'},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery(".document-generation-container").length <= 0) {
                    jQuery('<div class="d-none document-generation-container"></div>').appendTo("body");
                    var documentGenerationContainer = jQuery('.document-generation-container');
                    documentGenerationContainer.html(response.html).removeClass('d-none');
                    jQuery('.modal', documentGenerationContainer).modal({
                        keyboard: true,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery('#templates', documentGenerationContainer).selectpicker().change(function () {
                        if (this.value !== '') {
                            jQuery.ajax({
                                url: getBaseURL() + moduleController + '/generate_document/' + moduleRecordId,
                                dataType: 'JSON',
                                type: 'GET',
                                data: {template_id: this.value, action: 'read'},
                                beforeSend: function () {
                                    jQuery('#loader-global').show();
                                },
                                success: function (response) {
                                    if(response.template_error){
                                        jQuery('#generate', documentGenerationContainer).addClass('d-none');
                                        jQuery('#template-fields-variables', documentGenerationContainer).html('').addClass('d-none');
                                        pinesMessage({ty: 'error', m: response.template_error});
                                    } else {
                                        if (response.html) {
                                            jQuery('#template-fields-variables', documentGenerationContainer).html(response.html).removeClass('d-none');
                                            
                                        } else {
                                            jQuery('#template-fields-variables', documentGenerationContainer).html('').addClass('d-none');
                                        }
                                        jQuery('#generate', documentGenerationContainer).removeClass('d-none').unbind().click(function () {
                                            generateDocumentSubmit(documentGenerationContainer);
                                        });
                                    }
                                }, complete: function () {
                                    jQuery('#loader-global').hide();
                                },
                                error: defaultAjaxJSONErrorsHandler
                            });
                        } else {
                            jQuery('#template-fields-variables', documentGenerationContainer).html('').addClass('d-none');
                            jQuery('#generate', documentGenerationContainer).addClass('d-none');
                        }
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(documentGenerationContainer);
                    });
                }
            } else {
                pinesMessage({ty: 'error', m: response.error});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function generateDocumentSubmit(container) {
    jQuery('#doc-path').val(jQuery('#lineage', documentsForm).val());
    jQuery('#doc-name').val(jQuery('#doc-name-preffix').val() + jQuery('#doc-name-suffix').text());
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('#generate', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + moduleController + '/generate_document/' + moduleRecordId,
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: response.msg});
                // reload grid
                $documentsGrid.data('kendoGrid').dataSource.read();
                jQuery('.modal', container).modal('hide');
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            }
        }, complete: function () {
            jQuery('#generate', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
jQuery(document).ready(function () {
    //Attachment Grid:
    $documentsGrid = jQuery('#documentsGrid');
    documentsForm = jQuery('#documentsForm');
    module = jQuery('#module', documentsForm).val();
    moduleRecordId = moduleRecordId === '' ? jQuery('#module-record-id', documentsForm).val() : moduleRecordId;
    searchByUrl();
    $attachmentDocumentForm = jQuery('#attachmentForm');
    $directoryDocumentForm = jQuery('#directoryForm');
    if(jQuery('#documentsTabs').length){
        jQuery(".k-state-active", "#documentsTabs").prop('disabled', true);
    }
    rightClickKendo("#documentsGrid", 'docs');
    disableActionButtons();
});

function listFileVersions(fileId) {
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/list_file_versions',
        type: 'POST',
        dataType: 'JSON',
        data: {
            "file_id": fileId
        },
        success: function (response) {
            if (response.status) {
                if (response.html !== undefined) {
                    jQuery('#versions-list-container').removeClass('d-none').html(response.html);
                    scrollToId(jQuery('#versions-list-container'));
                } else {
                    jQuery('#versions-list-container').addClass('d-none').html('');
                }
            } else {
                jQuery('#versions-list-container').addClass('d-none').html('');
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function openDocument(id, extension) {
    var openDocument = getBaseURL() + moduleController + '/open_document/' + id + '/' + extension;
    window.open(openDocument);
}
function showHideInCp(id, visible, type){
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/show_hide_document_in_cp',
        type: 'POST',
        dataType: 'JSON',
        data: {'id': id},
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if(response.result && visible == 0 && type == 'folder'){
                confirmationDialog('confirmation_message_to_show_children_documents', {resultHandler: showChildrenInCp, parm: {id: id, message: response.message, showChildren: 1}, onCloseHandler: showChildrenInCp, onCloseParm: {id:id, message: response.message, showChildren: 0}});
            }else{
                pinesMessage(response.result ? {ty: 'success', m: response.message} : {ty: response.info ? 'information' : 'error', m: response.error ? response.error : response.info});
                jQuery("#loader-global").hide();
                $documentsGrid.data('kendoGrid').dataSource.read();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function showChildrenInCp(data){
    if(data.showChildren){ 
        jQuery.ajax({
            url: getBaseURL() + moduleController + '/show_children_documents_in_cp',
            type: 'POST',
            dataType: 'JSON',
            data: {'id': data.id},
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
    $documentsGrid.data('kendoGrid').dataSource.read();
    pinesMessage({ty: 'success', m: data.message});
}

function showHideInAp(id, visible, type){
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/show_hide_document_in_ap',
        type: 'POST',
        dataType: 'JSON',
        data: {'id': id},
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if(response.result && visible == 0 && type == 'folder'){
                confirmationDialog('confirmation_message_to_show_ap_children_documents', {resultHandler: showChildrenInAp, parm: {id: id, message: response.message, showChildren: 1}, onCloseHandler: showChildrenInAp, onCloseParm: {id:id, message: response.message, showChildren: 0}});
            }else{
                pinesMessage(response.result ? {ty: 'success', m: response.message} : {ty: response.info ? 'information' : 'error', m: response.error ? response.error : response.info});
                jQuery("#loader-global").hide();
                $documentsGrid.data('kendoGrid').dataSource.read();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function showChildrenInAp(data){
    if(data.showChildren){ 
        jQuery.ajax({
            url: getBaseURL() + moduleController + '/show_children_documents_in_ap',
            type: 'POST',
            dataType: 'JSON',
            data: {'id': data.id},
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
    $documentsGrid.data('kendoGrid').dataSource.read();
    pinesMessage({ty: 'success', m: data.message});
}


function moveDocument(id, moduleRecordId, isBulk) {
    // if it's not a bulk move, all other rows should be discarded
    if (!isBulk) {
        var row = jQuery('.dropdown[data-index="' + id + '"]').closest('tr');
        var rowCheckbox = row.find('.row-checkbox');

        toggleSelectItem(rowCheckbox, true);
        selectedItems = [{id: id, checked: true}];

        jQuery('tr:not(:has(.dropdown[data-index="' + id + '"])) .row-checkbox', $documentsGrid).each(function () {
            var checkbox = jQuery(this);
            var item = checkbox.parents('tr');

            checkbox.prop('checked', false);
            item.removeClass("k-state-selected");
            uncheckCheckboxAll();
        });
    }

    if (getSelectedItems().length > 0) {
        jQuery.ajax({
            dataType: 'JSON',
            url: getBaseURL() + moduleController + '/move_document/' + moduleRecordId + (typeof moveAssets != 'undefined' ? '/true' : ''),
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.html) {
                    var moveDocDialogId = "#document-move-dialog";
                    if (jQuery(moveDocDialogId).length <= 0) {
                        jQuery('<div id="document-move-dialog"></div>').appendTo("body");
                        var movDocDialog = jQuery(moveDocDialogId);
                        movDocDialog.html(response.html);
                        jQuery("form", movDocDialog).attr('id', 'move-doc-form');
                        initializeModalSize(moveDocDialogId);
                        jQuery(document).keyup(function (e) {
                            if (e.keyCode == 27) {
                                jQuery('.modal', moveDocDialogId).modal('hide');
                            }
                        });
                        jQuery(moveDocDialogId).find('input').keypress(function (e) {
                            // Enter pressed?
                            if (e.which == 13) {
                                moveDocSubmit(moveDocDialogId);
                            }
                        });
                        jQuery("#move-doc-btn", moveDocDialogId).click(function () {
                            moveDocSubmit(moveDocDialogId);
                        });
                        jQuery('.modal').on('hidden.bs.modal', function () {
                            destroyModal(jQuery(moveDocDialogId));
                        });
                        jQuery('.modal', movDocDialog).modal({
                            keyboard: false,
                            backdrop: 'static',
                            show: true
                        });

                        var tree = JSON.parse(response.tree);

                        jQuery("#treeview").on('click', function () {
                            setTargetFolder();
                        }).on('loaded.jstree', function () {
                            setTargetFolder();

                        }).on('contextmenu', function () {
                            jQuery('.jstree-default-contextmenu').css({'z-index': 1050});
                        }).bind('create_node.jstree', function (e, data) {
                            jQuery('#treeview').jstree("deselect_all", true);
                            jQuery('#treeview').jstree("select_node", data.node);
                            jQuery('#treeview').jstree("edit", data.node);
                            var parent = getNodeParent(data.node);
                            var parentNode = jQuery('#treeview').jstree("get_node", parent);
                            foldersHaveNewChildren.push(parentNode);
                            newCreatedFolders.push(data.node);
                            setTargetFolder();
                        }).jstree({
                            "core": {
                                data: tree,
                                multiple: false, // disable select multi folders
                                check_callback: true, // so that create works
                                force_text: true,
                                'strings': {
                                    'New node': 'New Folder'
                                }
                            },
                            "plugins": ["contextmenu", "sort", "unique"],
                            "contextmenu": {
                                items: jstreeContextMenu
                            }
                        });
                    }
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        pinesMessage({ty: 'information', m: _lang.feedback_messages.moveDocumentSelectSome});
    }
}

function jstreeContextMenu(node) {
    var items = {};

    if (!containsObject(node, foldersHaveNewChildren)) {
        items.createItem = {
            label: _lang.create,
            action: function () {
                var parent = jQuery('#treeview').jstree('get_selected');
                var newNode = {state: "open", data: {lineage: '', parent_lineage: ''}};

                jQuery('#treeview').jstree("create_node", parent, newNode, 'last', editSelectedNode, true);
            }
        }
    }

    if (containsObject(node, newCreatedFolders)) {
        items.renameItem = {
            label: _lang.rename,
            action: function () {
                jQuery('#treeview').jstree("edit", node);
            }
        };
    }

    return items;
}

function containsObject(obj, list) {
    var i;
    for (i = 0; i < list.length; i++) {
        if (list[i] === obj) {
            return true;
        }
    }

    return false;
}
function appendObjTo(thatArray, newObj) {
    const frozenObj = Object.freeze(newObj);
    return Object.freeze(thatArray.concat(frozenObj));
}
function prepareNewNodesToPost() {
    var formData = [];

    for (var i = 0; i < newCreatedFolders.length; i++) {
        var node = newCreatedFolders[i];

        var nodeData = {
            fake_id: node.id,
            module: module,
            module_record_id: moduleRecordId,
            parent: node.parent,
            lineage: null,
            name: node.text
        };

        // node's parent
        var parentNode = getNodeParent(node);

        // node's parent's parent
        var parentNodeParent = getNodeParent(parentNode);

        // if the new folder is not added on the root
        if (parentNodeParent.id !== '#') {
            nodeData.lineage = parentNode.data.lineage;
        }

        formData.push(nodeData);
    }

    return formData;
}
function getNodeParent(node) {
    var parentNodeId = '#';

    if (typeof node.parents !== 'undefined' && node.parents.length > 0) {
        parentNodeId = node.parents[0];
    }

    return jQuery('#treeview').jstree("get_node", {id: parentNodeId});
}
function editSelectedNode() {
    jQuery('#treeview').jstree("edit", jQuery('#treeview').jstree('get_selected'));
}
function setTargetFolder() {
    // the id of the selected folder to move the item(s) to it.
    var targetFolderId = jQuery("#treeview").jstree("get_selected")[0];
    var targetFolder = jQuery("#treeview").closest('form').find('input[name="target_folder"]');

    if (targetFolder.length > 0) {
        targetFolder.val(targetFolderId);
    } else {
        jQuery("#treeview").closest('form').append('<input type="hidden" name="target_folder" value="' + targetFolderId + '"/>');
    }
}
function moveDocSubmit(container) {
    var targetFolder = jQuery('form input[name="target_folder"]', container).val();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: {
            target_folder: targetFolder,
            selected_items: getSelectedItems(),
            new_created_folders: prepareNewNodesToPost()
        },
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + moduleController + '/move_document',
        success: function (response) {
            if (response.result.status === true) {
                jQuery('.modal').modal('hide');
                // reload grid
                $documentsGrid.data('kendoGrid').dataSource.read();
                pinesMessage({ty: 'success', m: _lang.feedback_messages.moveDocumentSuccess});
                resetSelectedDocuments();
            } else if (response.result.status === false) {
                pinesMessage({ty: 'error', m: _lang.feedback_messages[response.result.error.message].sprintf([response.result.error.value])});
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.error});
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function resetSelectedDocuments() {
    selectedItems = [];
    newCreatedFolders = [];
    foldersHaveNewChildren = [];
    uncheckCheckboxAll();
}
function gridEvents() {
    documentsSearch();
    customGridToolbarCSSButtons();
    // URL Grid:
    $urlDocumentGrid = jQuery('#urlGrid');
    urlsForm = jQuery('#urlsForm');
    $urlDocumentForm = jQuery('#urlDocumentForm');
    loadUrlGrid();
    drag_and_drop();
    jQuery('#documentsTabs').kendoTabStrip({
        collapsible: true,
        activate: function (e) {
            var $contentTarget = jQuery(e.contentElement);
            if (document.getElementById($contentTarget.attr('id')).contains(document.getElementById("urlGrid"))) {
                searchUrlDocument();
                jQuery('#versions-list-container').addClass('d-none').html('');
            } else if (document.getElementById($contentTarget.attr('id')).contains(document.getElementById("documentsGrid"))) {
                documentsSearch();
            } else {
                // load integration grid
                for (i in loadedIntegrationProviders) {
                    if (loadedIntegrationProviders[i] != 'A4L') {
                        if (document.getElementById($contentTarget.attr('id')).contains(document.getElementById("integrationDocumentsGrid_" + loadedIntegrationProviders[i]))) {
                            loadIntegrationGrid(loadedIntegrationProviders[i]);
                        }
                    }
                }
            }
            disableActionButtons();
            jQuery(".k-item", "#documentsTabs").prop('disabled', false);
            jQuery(".k-state-active", "#documentsTabs").prop('disabled', true);
        },
        select: function (e) {
            document.location.hash = '';
        },
        animation: {close: {duration: 10, effects: "expand:vertical"}}
    });

    if (showDocumentEditorInstallationModal) {
        documentEditorInstallationPopup();
    }
}

function downloadSelectedFilesAndFolder(id, isBulk = true){
    id = id || 0;
    if(isBulk){
        if (getSelectedItems().length > 0) {
            var selected_items = getSelectedItems();
            window.location = getBaseURL() + moduleController + '/download_docs_zip_file?selected_items= ' + selected_items.join(",");
        } else {
            pinesMessage({ty: 'information', m: _lang.feedback_messages.downloadDocumentSelectSome});
        }
    } else {
        window.location = getBaseURL() + moduleController + '/download_docs_zip_file?selected_items= ' + id;
    }
}

function downloadFolder(id){
    downloadSelectedFilesAndFolder(id, false);
}

function disableActionButtons(){
    actionAddBtn = jQuery('.action-add-btn');
    if('undefined' !== typeof(disableMatter) && disableMatter){
      disableFields(actionAddBtn);
    }
}