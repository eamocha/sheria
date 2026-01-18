var $attachmentDocumentGrid = null, $attachmentDocumentForm = null, $attachmentDocumentSearchFilters = null, attachmentDocumentDataSrc = null, attachmentDocumentGridOptions = null, flagGetCrumbs = 0, flagReloadGrid = true, modelName, subModelName, modelId, docPath, $enableQuickSearch = false;
$dropZone = null;
var officeFileTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx'];
var allowedConverterFileTypes = ['pptx','ppt','docx','doc','wps','dotx','docm','dotm','dot','odt','xlsx','xls'];
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
                            attachmentId = jQuery($documentConfig.filterModuleAttachmentIdValue, '#' + $attachmentDocumentSearchFilters).val();
                            jQuery($documentConfig.moduleAttachmentId, $attachmentDocumentForm).val(attachmentId);
                            jQuery('#module', $attachmentDocumentForm).val(jQuery('#module', '#' + $documentConfig.attachmentDocumentSearchFilters).val());
                            $attachmentDocumentForm.submit();
                        }
                    }
                },
                {
                    text: _lang.cancel,
                    'class': 'btn btn-link',
                    click: function () {
                        resetAttachmentDocumentForm();
                        jQuery(this).dialog("close");
                    }
                }],
            close: function () {
                resetAttachmentDocumentForm();
                jQuery(window).unbind('resize');
            },
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(that, '60%', '300');
                }));
                resizeNewDialogWindow(that, '60%', '300');
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
function submitAttachmentDocumentForm() {
    $attachmentDocumentForm.submit();
}

function resetAttachmentDocumentForm() {
    var attachmentDialog = jQuery("#attachmentDialog");
    jQuery($attachmentDocumentForm, attachmentDialog).validationEngine('hide');
    jQuery($attachmentDocumentForm, attachmentDialog)[0].reset();
}
function searchAttachmentDocument() {
    if (undefined === $attachmentDocumentGrid.data('kendoGrid')) {
        $attachmentDocumentGrid.kendoGrid(attachmentDocumentGridOptions);
        return false;
    }
    resetPagination($attachmentDocumentGrid);
    return true;
}
function attachmentQuickSearch(keyCode, term) {
    if (keyCode == 13) {
        jQuery('#term').val(term);
        resetPagination($attachmentDocumentGrid);
    }
}
function uploadDocumentDone(message, type) {
    if (message)
        pinesMessage({ty: type, m: message});
    jQuery("#attachmentDialog").dialog("close");
    resetPagination($attachmentDocumentGrid);
    jQuery('#attachmentFormContainer', '#attachmentDialog').removeClass('d-none');
    jQuery('#loading', '#attachmentDialog').addClass('d-none');
}
function attachmentDocumentFormSubmitAndStartUpload() {
    jQuery('#attachmentFormContainer', '#attachmentDialog').addClass('d-none');
    jQuery('#loading', '#attachmentDialog').removeClass('d-none');
    return true;
}
function moneyDownloadFile(id) {
    var downloadUrl = getBaseURL('money') + $documentConfig.moduleController + '/' + $documentConfig.objName + '_download_file/' + id;
    if($documentConfig.objName == 'invoice')
        downloadUrl += '/' + $documentConfig.module;
    window.location = downloadUrl;
}
function moneyPreviewFile(id, module, moduleId, lineage, extension) {
    jQuery.ajax({
        url: getBaseURL('money')  + 'vouchers/preview_document' ,
        method: 'post',
        data: {id: id},
        dataType: 'json',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var viewDocDialogId = "#document-view-dialog";
                if (jQuery(viewDocDialogId).length <= 0) {
                    jQuery('<div id="document-view-dialog"></div>').appendTo("body");
                    var viewDocDialog = jQuery(viewDocDialogId);
                    viewDocDialog.html(response.html);
                    jQuery('.file-container', viewDocDialog).addClass('loading');
                    initializeModalSize(viewDocDialogId, 0.9, 0.7);
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery('.modal', viewDocDialogId).modal('hide');
                        }
                    });

                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(jQuery(viewDocDialog));
                    });

                    if(response.image_document){
                        var fileContainer = jQuery('.file-container', viewDocDialog);
                        fileContainer.html(response.image_document);
                    } else{
                        var fileContainer = jQuery('.file-container', viewDocDialog);
                        if (fileContainer.length > 0) {
                            if(!detectIE()){
                                fileContainer.html('<embed src="javascript:void(0);">');
                                fileContainer.find('embed').attr('src', response.document.url);
                            } else{
                                fileContainer.html('<iframe src="javascript:void(0);"></iframe>');
                                fileContainer.find('iframe').attr('src', response.document.url);
                            }
                        }
                    }
                    jQuery('#title', viewDocDialog).text(response.document.full_name);
                    jQuery('.modal', viewDocDialog).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
                }
            }
        }, complete: function () {
                jQuery('#loader-global').hide();
        },
    });
}
function inQuickSearch() {
    $searchField = jQuery('#quickSearchFilterFieldsValue', '#' + $attachmentDocumentSearchFilters).val();
    return $searchField != '';
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
function deleteDocument(id) {
    if (confirm(_lang.confirmationDeleteFile)) {
        jQuery.ajax({
            url: getBaseURL('money') + $documentConfig.moduleController + '/' + $documentConfig.objName + '_delete_document',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'document_id': id,
                'module': $documentConfig.module
            },
            success: function (response) {
                pinesMessage({ty: response.status ? 'information' : 'error', m: response.message});
                resetPagination($attachmentDocumentGrid);
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function getExtIcon(ext) {
    ext = ext.toLowerCase();
    $extensions = {'doc': 'fs-word-icon', 'docx': 'fs-word-icon', 'xls': 'fs-excel-icon', 'xlsx': 'fs-excel-icon', 'ppt': 'fs-powerpoint-icon', 'pps': 'fs-powerpoint-icon', 'pptx': 'fs-powerpoint-icon', 'pdf': 'fs-pdf-icon', 'tif': 'fs-image-icon', 'jpg': 'fs-image-icon', 'png': 'fs-image-icon', 'gif': 'fs-image-icon', 'jpeg': 'fs-image-icon', 'bmp': 'fs-image-icon', 'folder': 'fs-folder-icon', 'msg': 'fs-email-icon', 'eml': 'fs-email-icon', 'vcf': 'fs-email-icon', 'html': 'fs-email-icon', 'htm': 'fs-email-icon', 'txt': 'fs-text-icon', 'zip': 'fs-compress-icon', 'rar': 'fs-compress-icon', 'avi': 'fs-video-icon', 'mpg': 'fs-video-icon', 'mp4': 'fs-video-icon', 'mp3': 'fs-video-icon', 'flv': 'fs-video-icon', 'unknown': 'fs-unknown-icon', 'mov': 'fs-video-icon', 'wav': 'fs-video-icon', '3gp': 'fs-video-icon', 'avi': 'fs-video-icon', 'jfif': 'fs-image-icon'};
    return (undefined == $extensions[ext]) ? $extensions['unknown'] : $extensions[ext];
}

function renameDocument(id, name, fullName, type) {
    name = prompt(_lang.rename + ' "' + decodeURIComponent(fullName) + '"', decodeURIComponent(name));
    if (null !== name && "" !== name) {
        actionName = type == 'file' ? 'rename_file' : 'rename_folder';
        jQuery.ajax({
            url: getBaseURL('money') + $documentConfig.moduleController + '/' + $documentConfig.objName + '_' + actionName,
            type: 'POST',
            dataType: 'JSON',
            data: {
                'document_id': id,
                'new_name': name,
                'module': $documentConfig.module
            },
            success: function (response) {
                if (!response.status) {
                    pinesMessage({ty: 'error', m: response.message});
                }
                if (undefined != $attachmentDocumentGrid.data('kendoGrid')) {
                    $attachmentDocumentGrid.data('kendoGrid').dataSource.read();
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function drag_and_drop() {
    var dragging = 0;
    var dropbox = jQuery('#dropbox');
    $dropZone = new Dropzone('#dropbox', {
        url: getBaseURL('money') + $documentConfig.moduleController + '/' + $documentConfig.objName + '_upload_file',
        paramName: 'uploadDoc',
        addRemoveLinks: true,
        parallelUploads: 1,
        maxFilesize: $documentConfig.allowed_upload_size_megabite,
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
            dragging++;
            jQuery('#dragAndDrop').removeClass('d-none');
            return showHideEmptyGridMessage("show");
        },
        dragleave: function (e) {
            dragging--;
            if (dragging === 0) {
                jQuery('#dragAndDrop').addClass('d-none');
                return showHideEmptyGridMessage("noBorder");
            }
            return true;
        },
        drop: function (e) {
            dragging++;
            return true;
        },
        accept: function (file, done) {
            if (jQuery("#attachmentLookUp").val() != "")
                return false;
            if(csrfName.length > 0){
                this.options.params[csrfName] = csrfValue;
            }
            this.options.params['module'] = jQuery('#module', '#' + $documentConfig.attachmentDocumentSearchFilters).val();
            this.options.params['module_record_id'] = jQuery('#module-record-id', '#' + $documentConfig.attachmentDocumentSearchFilters).val();
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
            if (response.status) {
                pinesMessage({ty: 'information', m: response.message});
                searchAttachmentDocument();
                file.previewElement.classList.add("dz-success");
            } else {

                if (typeof response == "string" && response == "login_needed") {
                    defaultAjaxHTMLErrorsHandler(response);
                } else {
                    var ty = 'error', msg = response.message;
                    pinesMessage({ty: ty, m: msg});
                    file.previewElement.classList.add("dz-error");
                }
            }
            jQuery('.dz-remove', file.previewElement).remove();
            return true;
        },
        complete: function (file) {
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
function showMessage(msg) {
    pinesMessage({ty: 'error', m: msg});
}
function setGridFilters(searchTerm, folderId) {
    jQuery('#quickSearchFilterFieldsValue', '#' + $attachmentDocumentSearchFilters).val(searchTerm);
    jQuery('#quickSearchFilterFieldsValue2', '#' + $attachmentDocumentSearchFilters).val(searchTerm);
}
function gridDataBound(e) {
    var grid = e.sender;
    if (jQuery(".empty-grid-container").length == 0) {
        jQuery(e.sender.wrapper)
                .find('table.k-selectable')
                .before('<div class="empty-grid-container" id="empty-grid-container"><div class="empty-grid-content-container"><div class="empty-grid-content-image"><img src="assets/images/attachments-grid-empty.png"/></div><div class="empty-grid-content-text"><p class="empty-grid-content-first-text">' + _lang.dragAndDrop.emptyGridFirstMessage + '</p><p class="empty-grid-content-second-text">' + _lang.dragAndDrop.emptyGridSecondMessage + '</p></div></div></div>');
    }
    if (grid.dataSource.total() == 0 && jQuery("#attachmentLookUp").val() == "") {
        showHideEmptyGridMessage("showWithoutBorder");
    } else {
        showHideEmptyGridMessage("hide");
    }

    if (jQuery("#attachmentLookUp").val() != "") {
        jQuery(".gridActionsButton").addClass("disabled");
    } else {
        jQuery(".gridActionsButton").removeClass("disabled");

    }
    animateDropdownMenuInGrids($documentConfig.attachmentDocumentGrid.substring(1, ($documentConfig.attachmentDocumentGrid.length)));
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

jQuery(document).ready(function () {
    module = jQuery('#module', $attachmentDocumentSearchFilters).val();
    moduleRecordId = jQuery('#module-record-id', $attachmentDocumentSearchFilters).val();
    attachmentDocumentDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('money') + $documentConfig.moduleController + '/' + $documentConfig.objName + "_load_documents",
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids($documentConfig.attachmentDocumentGrid.substring(1, ($documentConfig.attachmentDocumentGrid.length)));
                }
            },
            update: {
                url: getBaseURL('money') + $documentConfig.moduleController + '/' + $documentConfig.objName + "_edit_documents",
                dataType: "jsonp",
                type: "POST",
                complete: function (XHRObj) {
                    $attachmentDocumentGrid.data('kendoGrid').dataSource.read();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    options.module = module;
                    options.module_record_id = moduleRecordId;
                    options.term = jQuery('#term').val();
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
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, batch: true, pageSize: 10, serverPaging: false, serverFiltering: false, serverSorting: false, error: function (e) {
            if (e.xhr.responseText == 'False')
                defaultAjaxJSONErrorsHandler(e.xhr)
        }
    });
    attachmentDocumentGridOptions = {
        autobind: true,
        dataSource: attachmentDocumentDataSrc,
        dataBound: gridDataBound,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {
                field: "id",
                template: '<div class="dropdown">' + gridActionIconHTML +
                        '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item #= (type == \'file\' && extension.toLowerCase() == \'pdf\')? \'\' : \'hide\' #" data-title="#= full_name #" target=\'_blank\' href="#= getBaseURL(\'money\') + $documentConfig.moduleController + \'/view_document/\' + id + \'/\' + encodeURIComponent(rawurlencode(full_name)) #">' + _lang.view + '</a>' + 
                        '<a class="dropdown-item #= type == \'folder\' ? \'hide\' : \'\' #" href="javascript:;" onclick="moneyDownloadFile(#= id #); return false;">' + _lang.download + '</a>' +
                        '<a class="dropdown-item #=(type===\'folder\' ||  jQuery.inArray(extension.toLowerCase(), allowedConverterFileTypes)=="-1" ) ? \'hide\' : \'\'#" href="##" onclick="moneyPreviewFile(#= id #, \'#= module #\', #= module_record_id #, \'#= addslashes(parent_lineage) #\', \'#= extension #\'); return false;">' + _lang.preview + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="renameDocument(#= id #, \'#= addslashes(encodeURIComponent(name)) #\', \'#= addslashes(encodeURIComponent(full_name)) #\', \'#= type #\'); return false;">' + _lang.rename + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="deleteDocument(#= id #); return false;">' + _lang.deleteRow + '</a>' +
                        '</div>' +
                        '</div>' +
                        '<i fileId="#= id #" class="pull-right fs-common-icon #= (type == \'folder\') ? \'selectable \' + getExtIcon(\'folder\') : getExtIcon(extension) #" onclick="toggleFileIconSelection(this, \'#= type #\');"></i>',
                sortable: false, filterable: false, title: _lang.actions, width: '81px'
            },
            {
                field: "full_name",
                template: '<a href="javascript:;" onclick="#= type == \'folder\' # ? loadDirectoryContent(#= id #) : moneyDownloadFile(#= id #);return false;" >#= full_name #</a>',
                // template: '<i class="iconLegal iconPrivacy#= parseInt(private) ? \'yes\' : \'no\' # "></i><a href="javascript:;" class="v-align-middle" # if (type == \'file\') { # title="#= _lang.totalOfVersions.sprintf([version]) #" onclick="downloadFile(#= id #, true); return false;" # } else { # onclick="loadDirectoryContent(#= id # ,\'#= addslashes(lineage) #\')" # } #>#= full_name #</a>&nbsp;#if(type == \'file\'){#<button class="btn btn-link no-outline#if(getCookie(inlineEditingToolCookie) == null){# btn-dimmed#}#" title="#= _lang.openDocumentInEditingTool#" onclick="editDocument(#= id #, \'#= module #\', #= module_record_id #, \'#= addslashes(parent_lineage) #\', \'#= extension #\')"><i class="fa-solid fa-up-right-from-square"/></button>#}#',
                title: _lang.name,
                width: '500px'
            },
            {field: "comment", title: _lang.keyWords, width: '144px'},
            {field: "size", template: "#= size > 0 ? (size < (1024 * 1024) ? kendo.toString(size / 1024, '0.00 (KB)') : kendo.toString(size / (1024 * 1024), '0.00 (MB)')) : '' # ", title: _lang.size, width: '80px'},
            {field: "display_created_on", title: _lang.addedOn, width: '136px'},
            {field: "modifier_full_name", title: _lang.modifiedBy, width: '140px'},
            {field: "modifiedOn", title: _lang.modifiedOn, width: '136px'},
            {field: "display_creator_full_name", title: _lang.addedBy, width: '140px'},
        ],
        editable: true,
        filterable: false,
        height: 375,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {mode: "multiple"},
        toolbar: [{
                name: "toolbar-menu",
                template: '<div class="col-md-3">'
                        + '<div class="input-group col-md-12">'
                        + '<input type="text" class="form-control search" placeholder="' + _lang.search + '" id="attachmentLookUp" onkeyup="attachmentQuickSearch(event.keyCode, this.value);" title="' + _lang.search + '" />'
                        + '</div>'
                        + '</div>'
                        + '<div class="col-md-2 pull-right">'
                        + '<div class="btn-group pull-right">'
                        + '<button type="button" class="btn btn-info dropdown-toggle gridActionsButton" data-toggle="dropdown">' + _lang.actions
                        + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" href="javascript:;" onclick="uploadFile();">' + _lang.uploadFile + '</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'

            }, {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}]
    };
    $attachmentDocumentGrid = jQuery($documentConfig.attachmentDocumentGrid);
    $attachmentDocumentForm = jQuery($documentConfig.attachmentDocumentForm);
    $attachmentDocumentSearchFilters = $documentConfig.attachmentDocumentSearchFilters;
    drag_and_drop();
    searchAttachmentDocument();
    customGridToolbarCSSButtons();
    jQuery('form#' + $attachmentDocumentSearchFilters).bind('submit', function (e) {
        e.preventDefault();
        //searchAttachmentDocument();
    });
});
