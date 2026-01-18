var $documentsGrid, documentsForm, $documentsGridOptions, moduleDocumentpath_typeValues, $urlDocumentGridOptions,
    $urlDocumentGrid, $enableQuickSearch = false, allowedUploadSizeMegabite, module;
var officeFileTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx'];
var viewableFileTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx', 'pdf', 'gif', 'jpg', 'jpeg', 'png', 'html', 'txt', 'htm', 'mpg', 'mp3', 'mp4', 'flv', 'mov', 'wav', '3gp', 'avi', 'jfif'];
var selectedItems = [];
var newCreatedFolders = [];
var foldersHaveNewChildren = [];
var moduleController = 'contracts';
var gridContainer, $dropZone;
// var module = 'contract';
function documentTabEvents(module) {
    module = module || 'contract';

    $documentsGrid = jQuery('#documentsGrid');
    documentsForm = jQuery('#documentsForm');
    moduleRecord = jQuery('#module-record', documentsForm).val();
    moduleRecordId = moduleRecordId === '' ? jQuery('#module-record-id', documentsForm).val() : moduleRecordId;
    searchByUrl(module);
}

function searchByUrl(module) {
    $hashURL = document.location.hash;
    $folderPath = $hashURL.substring(1);
    if ($folderPath !== '') {
        jQuery('#lineage', documentsForm).val($folderPath);
        jQuery.ajax({
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            data: {lineage: $folderPath, module: moduleRecord},
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL(module) + moduleController + "/check_folder_privacy",
            success: function (response) {
                if (!response.result) {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.notAccessibleFolder});
                    window.location.href = window.location.href.substr(0, window.location.href.indexOf('#'));
                } else {
                    loadAttachmentsGrid(module);
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        loadAttachmentsGrid(module);
    }
}

function loadAttachmentsGrid(module) {
    gridContainer = $documentsGrid;
    var visibleToCP = jQuery('#visible', '#contract-docs-container').val();
    try {
        var documentsDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL(module) + moduleController + "/load_documents",
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
                        }
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                        animateDropdownMenuInGrids('documentsGrid');
                    },
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    }
                },
                update: {
                    url: getBaseURL(module) + moduleController + "/edit_documents",
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
                            if (parseInt(options.models[i]['to_be_approved']) < 1) {
                                options.models[i]['to_be_approved'] = null;
                            }                            
                            if (parseInt(options.models[i]['to_be_signed']) < 1) {
                                options.models[i]['to_be_signed'] = null;
                            }
                        }
                        return {
                            models: kendo.stringify(options.models)
                        };
                    } else {
                        options.module = moduleRecord;
                        options.module_record_id = moduleRecordId;
                        options.lineage = jQuery('#lineage', documentsForm).val();
                        options.term = jQuery('#term', documentsForm).val();
                    }
                    jQuery('#versions-list').addClass('d-none').html('');
                    return options;
                }
            },
            schema: {
                type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {editable: false, type: "string"},
                        full_name: {editable: false, type: "string"},
                        approval: {field: "approval"},
                        signature: {field: "signature"},
                        document_type_id: {field: "document_type_id"},
                        document_status_id: {field: "document_status_id"},
                        to_be_approved: {field: "to_be_approved"},
                        to_be_signed: {field: "to_be_signed"},
                        comment: {type: "string"},
                        size: {editable: false, type: "string"},
                        display_creator_full_name: {editable: false, type: "string"},
                        display_created_on: {editable: false, type: "string"},
                        modifier_full_name: {editable: false, type: "string"},
                        modifiedOn: {editable: false, type: "string"}
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
                    field: "id",
                    template:
                        (visibleToCP == 1 ? '#= visible_in_cp == "1" ? (type == "folder" ? \'<span class="cp-flag folder-flag"></span>\' : \'<span class="cp-flag file-flag"></span>\') : \'\'#' : '') +

                        '<div class="dropdown #= !parseInt(is_accessible) ? \'d-none\' : \'\' #" data-index="#= id #">' + 
                        '<button id="dms-action-wheel" class="btn btn-default btn-sm"  data-toggle="dropdown"><i class="fa-solid fa-gear"></i> <span class="caret no-margin"></span></button>'+ 
                        '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item #= (type == \'file\' && extension.toLowerCase() == \'pdf\')? \'\' : \'d-none\' #" data-title="#= full_name #" target=\'_blank\' href="#= getBaseURL(\'contract\') + moduleController + \'/view_document/\' + id + \'/\' + encodeURIComponent(rawurlencode(full_name)) #">' + _lang.view + '</a>' + 
                        '<a class="dropdown-item #=(type===\'folder\') ? \'d-none\' : \'\'#" href="##" onclick="downloadFile(#= id #, true, \''+module+'\'); return false;">' + _lang.download + '</a>' +
                        '# if (type == "file" && (extension == "docx" || "doc")) { # <a class="dropdown-item" href="##"  onclick="saveAsPDF(#= id #); return false;">' + _lang.saveAsPDF + '</a> #} #' +
                        '# if (system_document != "1") { # <a class="dropdown-item" href="##"  onclick="renameDocument(#= id #, \'#= type #\'); return false;">' + _lang.rename + '</a> #} #' +
                        '# if (system_document != "1") { # <a class="dropdown-item" href="##" onclick="deleteDocument(#= id #); return false;">' + _lang.deleteRow + '</a> #} #' +
                        (module != 'customer-portal' ? '<a class="dropdown-item" style="#= (type!==\'folder\') ?\'display: none;\' : \'\'  #" href="##" onclick="shareFolder(#= id #, #= private #); return false;">' + _lang.shareWith + '</a>' +
                        (visibleToCP == 1 ? '# if (name != "Contract_Notes_Attachments") { # <a class="dropdown-item" href="##" onclick="showHideInCp(\'#= id #\', \'#= visible_in_cp #\', \'#= type #\'); return false;"> #= visible_in_cp == 1 ? _lang.hideInCustomerPortal : _lang.showInCustomerPortal #</a> #} #' : '') : '' )+
                        '<a class="dropdown-item #= type != \'file\' ? \'d-none\' : \'\' #" href="javascript:;" onclick="listFileVersions(#= id #);">' + _lang.listVersions + '</a>' +
                        '# if (jQuery(\'\\\\#term\').val()) { # <a class="dropdown-item" href="javascript:;" onclick="openFolder(\'#= addslashes(parent_lineage) #\');">' + _lang.openLocation + '</a> # } #' +
                        '</div>' +
                        '</div>' +
                        '<i fileId="#= id #" class="float-right fs-common-icon #= (type == \'folder\') ? \'selectable \' + getExtIcon(\'folder\') : getExtIcon(extension) #" onclick="toggleFileIconSelection(this, \'#= type #\')"></i>',
                    sortable: false, filterable: false, title: _lang.dms_actions, width: '81px', attributes: {
                        class: "actions-cell flagged-gridcell"
                    }
                },
                {
                    field: "full_name",
                    template: '<i class="iconLegal iconPrivacy#= parseInt(private) ? \'yes\' : \'no\' # "></i>' +
                        (module != 'customer-portal' ?'<a href="javascript:;" class="v-align-middle" # if (type == \'file\') { # title="#= _lang.totalOfVersions.sprintf([version]) #" onclick="downloadFile(#= id #, true, \''+module+'\'); return false;" # } else { # onclick="loadDirectoryContent(#= id # ,\'#= addslashes(lineage) #\', \''+module+'\')" # } #>#= full_name #</a>&nbsp;' 
                        : '<a href="javascript:;" class="v-align-middle" # if (type == \'file\') { # title="#= _lang.totalOfVersions.sprintf([version]) #" onclick="downloadFile(#= id #, true, \''+module+'\'); return false;" # } else { # onclick="loadDirectoryContentCP(#= id # ,\'#= addslashes(lineage) #\')" # } #>#= full_name #</a>&nbsp;') +
                        (module != 'customer-portal' ? '#if(type == \'file\'){#<button class="btn btn-link no-outline#if(getCookie(inlineEditingToolCookie) == null){# btn-dimmed#}#" title="#= _lang.openDocumentInEditingTool#" onclick="editDocument(#= id #, \'#= module #\', #= module_record_id #, \'#= addslashes(parent_lineage) #\', \'#= extension #\');">' +
                        '<i class="fa-solid fa-arrow-up-right-from-square"></i></button>#}#' : ''),
                    title: _lang.name,
                    width: '480px'
                },
                {
                    field: "to_be_approved",
                    title: _lang.toBeApproved,
                    width: '150px',
                    template: "#= (to_be_approved == null) ? ' ' : helpers.getObjectFromArr(toBeApprovedValues, 'text', 'value', to_be_approved) #"
                },
                {
                    field: "to_be_signed",
                    title: _lang.toBeSigned,
                    width: '140px',
                    template: "#= (to_be_signed == null) ? ' ' : helpers.getObjectFromArr(toBeSignedValues, 'text', 'value', to_be_signed) #"
                },
                {field: "comment", title: _lang.keyWords, width: '144px'},
                {
                    field: "document_type_id",
                    title: _lang.type,
                    width: '120px',
                    template: "#= (document_type_id == null) ? ' ' : helpers.getObjectFromArr(moduleDocumentTypeValues, 'text', 'value', document_type_id) #"
                },
                {
                    field: "document_status_id",
                    title: _lang.status,
                    width: '120px',
                    template: "#= (document_status_id == null) ? ' ' : helpers.getObjectFromArr(moduleDocumentStatusValues, 'text', 'value', document_status_id) #"
                },
                {
                    field: "size",
                    template: "#= size > 0 ? (size < (1024 * 1024) ? kendo.toString(size / 1024, '0.00 (KB)') : kendo.toString(size / (1024 * 1024), '0.00 (MB)')) : '' # ",
                    title: _lang.size,
                    width: '80px'
                },
                {field: "modifier_full_name", title: _lang.modifiedBy, width: '140px'},
                {field: "modifiedOn", title: _lang.modifiedOn, width: '136px'},
                {field: "display_creator_full_name", title: _lang.addedBy, width: '140px'},
                {field: "display_created_on", title: _lang.addedOn, width: '136px'}
            ],
            selectable: false,
            editable: true,
            filterable: false,
            height: 500,
            pageable: {
                input: true,
                messages: _lang.kendo_grid_pageable_messages,
                numeric: false,
                pageSizes: [10, 20, 50, 100],
                refresh: true
            },
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: false,
            sortable: {mode: "multiple"},
            toolbar: [{
                name: "toolbar-menu",
                template:
                    '<div class="col-md-3">'
                    + '<div class="input-group col-md-12">'
                    + '<input type="text" class="form-control search" placeholder="' + _lang.search + '" id="attachment-lookup" onkeyup="attachmentQuickSearch(event.keyCode, this.value);" title="' + _lang.search + '" />'
                    + '</div>'
                    + '</div>'
                    + '<div class="col-md-3 float-right">'
                    + '<div class="btn-group float-right">'
                    + '<button type="button" class="btn btn-info dropdown-toggle gridActionsButton" data-toggle="dropdown">' + _lang.actions
                    + ' <span class="caret"></span>'
                    + '<span class="sr-only">Toggle Dropdown</span>'
                    + '</button>'
                    + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                    + '<a class="dropdown-item" role="button" href="javascript:;" onclick="uploadFileForm();">' + _lang.uploadFile + '</a>'
                    + '# if (installationType == "on-server") { # <a class="dropdown-item" role="button" href="javascript:;" onclick="uploadDirectoryForm();">' + _lang.uploadDirectory + '</a> # } #'
                    + '<a class="dropdown-item" role="button" href="javascript:;" onclick="folderForm();">' + _lang.createFolder + '</a>'
                    + '<a class="dropdown-item" role="button" href="javascript:;" onclick="generateDocument();">' + _lang.generateDocument + '</a>'
                    + '<a class="dropdown-item" role="button" href="javascript:;" onclick="downloadInlineEditingTool();">' + _lang.downloadInlineEditingTool + '</a>'
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
    gridEvents(module);
}

function showHideInCp(id, visible, type) {
    jQuery.ajax({
        url: getBaseURL('contract') + moduleController + '/show_hide_document_in_cp',
        type: 'POST',
        dataType: 'JSON',
        data: {'id': id},
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result && visible == 0 && type == 'folder') {
                confirmationDialog('confirmation_message_to_show_children_documents', {
                    resultHandler: showChildrenInCp,
                    parm: {id: id, message: response.message, showChildren: 1},
                    onCloseHandler: showChildrenInCp,
                    onCloseParm: {id: id, message: response.message, showChildren: 0}
                });
            } else {
                pinesMessage(response.result ? {
                    ty: 'success',
                    m: response.message
                } : {ty: response.info ? 'information' : 'error', m: response.error ? response.error : response.info});
                jQuery("#loader-global").hide();
                $documentsGrid.data('kendoGrid').dataSource.read();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function showChildrenInCp(data) {
    if (data.showChildren) {
        jQuery.ajax({
            url: getBaseURL('contract') + moduleController + '/show_children_documents_in_cp',
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

function gridDataBound(e) {
    emptyContainer = "empty-grid-container";
    if (jQuery('#attachment-lookup').val() != "") {
        gridContainer.data('kendoGrid').showColumn('location');
    } else {
        gridContainer.data('kendoGrid').hideColumn('location');
    }
    var grid = e.sender;
    if (jQuery('.' + emptyContainer).length == 0) {
        jQuery(e.sender.wrapper)
            .find('.k-grid-content table[role="grid"]')
            .before('<div class="empty-grid-container ' + emptyContainer + '" id=' + emptyContainer + '><div class="empty-grid-content-container"><div class="empty-grid-content-image"><img src="assets/images/attachments-grid-empty.png"/></div><div class="empty-grid-content-text"><p class="empty-grid-content-first-text">' + _lang.dragAndDrop.emptyGridFirstMessage + '</p><p class="empty-grid-content-second-text">' + _lang.dragAndDrop.emptyGridSecondMessage + '</p></div></div></div>');
    }
    if (grid.dataSource.total() == 0 && jQuery("#attachment-lookup").val() == "") {
        showHideEmptyGridMessage("showWithoutBorder");
    } else {
        showHideEmptyGridMessage("hide");
    }

    if (jQuery("#attachment-lookup").val() != "") {
        jQuery(".gridActionsButton", gridContainer).addClass("disabled");
    } else {
        jQuery(".gridActionsButton", gridContainer).removeClass("disabled");

    }
    animateDropdownMenuInGrids(jQuery(gridContainer).attr('id'));
    checkSelectedItems();

    var rows = grid.tbody.find("[role='row']");

    rows.unbind("click");

    jQuery('tbody .row-checkbox', gridContainer).shiftcheckbox({
        onChange: function (checked) {
            toggleSelectItem(jQuery(this), checked);
        }
    });
}

function gridEvents(module) {
    documentsSearch();
    customGridToolbarCSSButtons();
    // URL Grid://hidden for now
    $urlDocumentGrid = jQuery('#urlGrid');
    // urlsForm = jQuery('#urlsForm');
    // loadUrlGrid();


    drag_and_drop();
    jQuery('#documentsTabs').kendoTabStrip({
        collapsible: true,
        activate: function (e) {
                drag_and_drop();
                documentsSearch();
                gridContainer = $documentsGrid;
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

function loadUrlGrid() {
    if (undefined !== $urlDocumentGrid) {
        var urlDocumentDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL(module) + moduleController + '/urls',
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
                    url: getBaseURL(module) + moduleController + '/edit_url',
                    dataType: "jsonp",
                    type: "POST",
                    complete: function (XHRObj) {
                        if (XHRObj.responseText == 'access_denied') {
                            ajaxAccessDenied();
                            $urlDocumentGrid.data('kendoGrid').dataSource.read();
                        } else {
                            var response = jQuery.parseJSON(XHRObj.responseText || "null");
                            if (response.result) {
                                pinesMessage({
                                    ty: 'information',
                                    m: _lang.feedback_messages.updateDocumentsSuccessfully
                                });
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
            schema: {
                type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {editable: false, type: "integer"},
                        module_id: {type: "string"},
                        to_be_approved: {
                            field: "to_be_approved",
                        },
                        to_be_signed: {
                            field: "to_be_signed",
                        },
                        document_type_id: {
                            field: "document_type_id",
                            validation: {required: {message: _lang.validation_field_required.sprintf([_lang.type])}}
                        },
                        document_status_id: {
                            field: "document_status_id",
                            validation: {required: {message: _lang.validation_field_required.sprintf([_lang.status])}}
                        },
                        name: {type: "string"},
                        path_type: {type: "string"},
                        path: {
                            type: "string",
                            validation: {required: {message: _lang.validation_field_required.sprintf([_lang.url])}}
                        },
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
                {
                    field: "id",
                    template: '<div class="dropdown">' + gridActionIconHTML + '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<li><a href="##" onclick="deleteUrl(\'#= id #\'); return false;">' + _lang.deleteRow + '</a></li>' +
                        '<li style=#= path_type === \'web\' ? "" : "display:none;" #><a href="#= path #" target="_blanc">' + _lang.goTo + '</a></li>' +
                        '</ul></div>',
                    sortable: false,
                    filterable: false,
                    title: _lang.actions,
                    width: '70px'
                },
                {field: "path_type", title: _lang.urlType, values: [], width: '127px'},
                {
                    field: "path",
                    template: "#= path_type == 'web' ? validURL(path) : path #",
                    title: _lang.url,
                    width: '180px'
                },
                {field: "name", title: _lang.name, width: '120px'},
                {field: "comments", title: _lang.comments, width: '146px'},
                {field: "to_be_approved", title: _lang.status, values: [], width: '120px'},
                {field: "to_be_signed", title: _lang.status, values: [], width: '120px'},
                {field: "document_type_id", title: _lang.type, values: [], width: '120px'},
                {field: "document_status_id", title: _lang.status, values: [], width: '120px'},
                {field: "modifiedBy", title: _lang.modifiedBy, width: '140px'},
                {field: "modifiedOn", title: _lang.modifiedOn, width: '130px'},
                {field: "addedBy", title: _lang.addedBy, width: '140px'},
                {field: "createdOn", title: _lang.addedOn, width: '136px'}
            ],
            editable: true,
            filterable: false,
            pageable: {
                input: true,
                messages: _lang.kendo_grid_pageable_messages,
                numeric: false,
                pageSizes: [5, 10, 20, 50, 100],
                refresh: true
            },
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
                    + '<input type="text" class="form-control search" placeholder="' + _lang.search + '" id="documentLookUp" onkeyup="urlQuickSearch(event.keyCode, this.value);" title="' + _lang.search + '" />'
                    + '</div>'
                    + '</div>'
                    + '<div class="col-md-2 float-right">'
                    + '<div class="btn-group float-right">'
                    + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">' + _lang.actions
                    + ' <span class="caret"></span>'
                    + '<span class="sr-only">Toggle Dropdown</span>'
                    + '</button>'
                    + '<ul class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                    + '<li><a href="javascript:;" onclick="urlForm()">' + _lang.addURL + '</a></li>'
                    + '</ul>'
                    + '</div>'
                    + '</div>'
            }, {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}]
        };
    }
}

function validURL(str) {
    if (new RegExp("([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?").test(str)) {
        return '<a href="' + str + '" target="_blank">' + str + '</a>';
    }
    return str;
}

function searchUrlDocument() {
    if (undefined === $urlDocumentGrid.data('kendoGrid')) {
        $urlDocumentGridOptions.columns[1].values = moduleDocumentpath_typeValues;
        $urlDocumentGridOptions.columns[5].values = toBeApprovedValues;
        $urlDocumentGridOptions.columns[6].values = toBeSignedValues;
        $urlDocumentGridOptions.columns[7].values = moduleDocumentTypeValues;
        $urlDocumentGridOptions.columns[8].values = moduleDocumentStatusValues;
        $urlDocumentGrid.kendoGrid($urlDocumentGridOptions);
        customGridToolbarCSSButtons();
        return false;
    }
    $urlDocumentGrid.data('kendoGrid').dataSource.read();
    return false;
}

function documentsSearch() {
    if (undefined === $documentsGrid.data('kendoGrid')) {
        if (moduleDocumentTypeValues === "" || moduleDocumentStatusValues === "") {
            $documentsGridOptions.columns.splice(3, 3);
        } else {
            $documentsGridOptions.columns[2].values = toBeApprovedValues;
            $documentsGridOptions.columns[3].values = toBeSignedValues;
            $documentsGridOptions.columns[5].values = moduleDocumentTypeValues;
            $documentsGridOptions.columns[6].values = moduleDocumentStatusValues;
        }
        $documentsGrid.kendoGrid($documentsGridOptions);
        return false;
    }
    resetPagination($documentsGrid);
    return false;
}

function showHideEmptyGridMessage(flag) {
    if (flag == "show" && jQuery('#attachment-lookup').val() == "") {
        jQuery('.empty-grid-container').addClass("empty-grid-container-on-hover");
    } else if (flag == "hide") {
        jQuery('.empty-grid-content-container').hide();
    } else if (flag == "noBorder") {
        jQuery('.empty-grid-container').removeClass("empty-grid-container-on-hover");
    } else if (flag == "showWithoutBorder") {
        jQuery('.empty-grid-content-container').show();
    }
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

function drag_and_drop() {
    lookupId = '#attachment-lookup';
    containerId = '#dragAndDrop';
    var dragging = 0;
    $dropZone = new Dropzone('#dropzone-container', {
        url: getBaseURL(module) + moduleController + '/upload_file',
        paramName: 'uploadDoc',
        addRemoveLinks: true,
        parallelUploads: 1,
        maxFilesize: allowedUploadSizeMegabite,
        params: {
            dragAndDrop: true
        },
        clickable: false,
        previewsContainer: containerId,
        dragover: function (e) {
            if (dragging !== 0) {
                jQuery(containerId).removeClass('d-none');
                return showHideEmptyGridMessage("show");
            }
            return true;
        },
        dragenter: function (e) {
            if (jQuery(lookupId).val() != "") {
                jQuery('.dz-preview', containerId).each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            dragging++;
            jQuery(containerId).removeClass('d-none');
            return showHideEmptyGridMessage("show");
        },
        dragleave: function (e) {
            if (jQuery(lookupId).val() != "") {
                jQuery('.dz-preview', containerId).each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            dragging--;
            if (dragging === 0) {
                jQuery(containerId).addClass('d-none');
                return showHideEmptyGridMessage("noBorder");
            }
            return true;
        },
        drop: function (e) {
            if (jQuery(lookupId).val() != "") {
                jQuery('.dz-preview', containerId).each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            dragging++;
            return true;
        },
        accept: function (file, done) {
            if (jQuery(lookupId).val() != "") {
                jQuery('.dz-preview', containerId).each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            if(typeof csrfName !== "undefined" && csrfName.length > 0){
                this.options.params[csrfName] = csrfValue;
            }
            this.options.params['module'] = moduleRecord;
            this.options.params['module_record_id'] = moduleRecordId;
            this.options.params['lineage'] = jQuery('#lineage', documentsForm).val();
            showHideEmptyGridMessage("hide");
            return done();
        },
        error: function (file, message) {
            file.previewElement.classList.add("dz-error");
            jQuery(containerId).addClass('d-none');
            showHideEmptyGridMessage("noBorder");
            file.previewElement.querySelector("[data-dz-errormessage]").textContent = message;
            jQuery('.dz-preview', containerId).each(function () {
                jQuery(this).remove();
            });
            pinesMessage({ty: 'error', m: message});
            return true;
        },
        success: function (file, response) {
            if (jQuery(lookupId).val() != "") {
                jQuery('.dz-preview', containerId).each(function () {
                    jQuery(this).remove();
                });
                return false;
            }
            if (response.status) {
                pinesMessage({ty: 'information', m: response.message});
                documentsSearch();
                file.previewElement.classList.add("dz-success");
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
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
            if (jQuery(lookupId).val() != "") {
                jQuery('.dz-preview', containerId).each(function () {
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
                jQuery(containerId).addClass('d-none');
                showHideEmptyGridMessage("noBorder");
                jQuery('.dz-preview', containerId).each(function () {
                    jQuery(this).remove();
                });
                return true;
            }
        }
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

function sharedWithRadioOnClick(private, container) {
    if (parseInt(private)) {
        jQuery('#sharedWithMultiUsers', container).removeClass('d-none');
    } else {
        jQuery('#sharedWithMultiUsers', container).addClass('d-none');
    }
}

function getExtIcon(ext) {
    ext = ext.toLowerCase();
    $extensions = {
        'doc': 'fs-word-icon',
        'docx': 'fs-word-icon',
        'xls': 'fs-excel-icon',
        'xlsx': 'fs-excel-icon',
        'ppt': 'fs-powerpoint-icon',
        'pps': 'fs-powerpoint-icon',
        'pptx': 'fs-powerpoint-icon',
        'pdf': 'fs-pdf-icon',
        'tif': 'fs-image-icon',
        'jpg': 'fs-image-icon',
        'png': 'fs-image-icon',
        'gif': 'fs-image-icon',
        'jpeg': 'fs-image-icon',
        'bmp': 'fs-image-icon',
        'folder': 'fs-folder-icon',
        'msg': 'fs-email-icon',
        'eml': 'fs-email-icon',
        'vcf': 'fs-email-icon',
        'html': 'fs-email-icon',
        'htm': 'fs-email-icon',
        'txt': 'fs-text-icon',
        'zip': 'fs-compress-icon',
        'rar': 'fs-compress-icon',
        'avi': 'fs-video-icon',
        'mpg': 'fs-video-icon',
        'mp4': 'fs-video-icon',
        'mp3': 'fs-video-icon',
        'flv': 'fs-video-icon',
        'unknown': 'fs-unknown-icon',
        'mov': 'fs-video-icon',
        'wav': 'fs-video-icon',
        '3gp': 'fs-video-icon',
        'avi': 'fs-video-icon',
        'jfif': 'fs-image-icon',
    };
    return (undefined === $extensions[ext]) ? $extensions['unknown'] : $extensions[ext];
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

function uploadDocumentDone(message, type) {
    if (message)
        pinesMessage({ty: type, m: message});
    jQuery("#attachmentDialog").dialog("close");
    resetPagination($documentsGrid);
    jQuery('#upload-form-container', '#attachmentDialog').removeClass('d-none');
    jQuery('#loading', '#attachmentDialog').addClass('d-none');
}

function deleteDocument(id, versionDocument) {
    versionDocument = versionDocument || false;
    if (confirm(_lang.confirmationDeleteFile)) {
        jQuery.ajax({
            url: getBaseURL(module) + moduleController + '/delete_document',
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
                        resetPagination(jQuery('#' + gridContainer.attr('id')));
                    }
                    jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}


function downloadFiles(files, newest_version) {//in a zip folder
    newest_version = newest_version || false;
    var downloadUrl = getBaseURL(module) + moduleController + '/download_files?files=' + files + '&newest_version=' + newest_version;
    window.location = downloadUrl;
}

function openFolder(folderLineage) {
    document.location.hash = '';
    jQuery('#lineage', documentsForm).val(folderLineage);
    jQuery('#term', documentsForm).val('');
    jQuery('#attachment-lookup', $documentsGrid).val('');
    resetPagination($documentsGrid);
    resetSelectedDocuments();
}

function uploadFileForm() {
    var contractId = jQuery('#id', '#contract-docs-container').val();
    
    jQuery.ajax({
        dataType: 'JSON',
        data: {'contract_id': contractId, 'lineage': jQuery('#lineage', documentsForm).val()},
        type: 'GET',
        url: getBaseURL(module) + 'contracts/upload_file',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".upload-file-container").length <= 0) {
                    jQuery('<div class="d-none upload-file-container"></div>').appendTo("body");
                    var uploadFileContainer = jQuery('.upload-file-container');
                    uploadFileContainer.html(response.html).removeClass('d-none');
                    jQuery('.select-picker', uploadFileContainer).selectpicker();
                    var lineage = jQuery('#lineage', documentsForm).val();
                    jQuery('#lineage', uploadFileContainer).val(lineage);
                    commonModalDialogEvents(uploadFileContainer);
                    jQuery("#form-submit", uploadFileContainer).click(function () {
                        uploadFileFormSubmit(uploadFileContainer);
                    });
                    jQuery(uploadFileContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            uploadFileFormSubmit(uploadFileContainer);
                        }
                    });
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function uploadFileFormSubmit(container) {
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/upload_file',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.status) {
                ty = 'success';
                jQuery('.modal', container).modal('hide');
                resetPagination($documentsGrid);
                showHideEmptyGridMessage("hide");
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
            } else {
                ty = 'error';
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                }

            }
            if ('undefined' !== typeof response.message) {
                pinesMessage({ty: ty, m: response.message});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function urlForm() {
    var contractId = jQuery('#id', '#contract-docs-container').val();
    jQuery.ajax({
        dataType: 'JSON',
        data: {'contract_id': contractId},
        type: 'GET',
        url: getBaseURL(module) + 'contracts/add_url',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".url-container").length <= 0) {
                    jQuery('<div class="d-none url-container"></div>').appendTo("body");
                    var urlFormContainer = jQuery('.url-container');
                    urlFormContainer.html(response.html).removeClass('d-none');
                    jQuery('.select-picker', urlFormContainer).selectpicker();
                    commonModalDialogEvents(urlFormContainer, urlFormSubmit);
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function urlFormSubmit(container) {
    var formData = jQuery("form#url-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/add_url',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery('.modal', container).modal('hide');
                if ($urlDocumentGrid.length) {
                    $urlDocumentGrid.data('kendoGrid').dataSource.read();
                }
            } else {
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                }
            }


        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function urlQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        jQuery('#url-quick-searchFilterFieldsValue', '#documentSearchFilters').val(term);
        jQuery('#url-quick-searchFilterFieldsValue2', '#documentSearchFilters').val(term);
        $urlDocumentGrid.data("kendoGrid").dataSource.read();
    }
}

function uploadDirectoryForm() {
    var contractId = jQuery('#id', '#contract-docs-container').val();
    jQuery.ajax({
        dataType: 'JSON',
        data: {'contract_id': contractId},
        type: 'GET',
        url: getBaseURL('contract') + 'contracts/upload_directory',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".document-directory-container").length <= 0) {
                    jQuery('<div class="d-none document-directory-container"></div>').appendTo("body");
                    var documentDirectoryContainer = jQuery('.document-directory-container');
                    documentDirectoryContainer.html(response.html).removeClass('d-none');
                    jQuery('#lineage', documentDirectoryContainer).val(jQuery('#lineage', documentsForm).val());
                    commonModalDialogEvents(documentDirectoryContainer, directoryFormSubmit);
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function directoryFormSubmit(container) {
    var folderext = document.getElementById('uploadDir').files;
    var paths = "";
    for (var i = 0, f; f = folderext[i]; ++i){
        paths += folderext[i].webkitRelativePath+"###";
    }
    jQuery('#folderext', 'form#document-form').val(paths);
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL('contract') + 'contracts/upload_directory',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');

        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            for(var i = 0; i < response.length; i++) {
                if (response[i]['status']) {
                    ty = 'success';
                    jQuery('.modal', container).modal('hide');
                    resetPagination($documentsGrid);
                    showHideEmptyGridMessage("hide");
                } else {
                    ty = 'error';
                    if ('undefined' !== typeof response[i]['validation_errors']) {
                        displayValidationErrors(response[i]['validation_errors'], container);
                    } else {
                        pinesMessage({ty: ty, m: response[i]['message']});
                    }
                }
                if ('undefined' !== typeof response[i]['message']) {
                    pinesMessage({ty: ty, m: response[i]['message']});
                }
            }
            
        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function folderForm() {
    var contractId = jQuery('#id', '#contract-docs-container').val();
    jQuery.ajax({
        dataType: 'JSON',
        data: {'contract_id': contractId},
        type: 'GET',
        url: getBaseURL(module) + 'contracts/create_folder',
        beforeSend: function () {
            jQuery('#loader-global').show();

        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".document-folder-container").length <= 0) {
                    jQuery('<div class="d-none document-folder-container"></div>').appendTo("body");
                    var documentFolderContainer = jQuery('.document-folder-container');
                    documentFolderContainer.html(response.html).removeClass('d-none');
                    jQuery('#lineage', documentFolderContainer).val(jQuery('#lineage', documentsForm).val());
                    commonModalDialogEvents(documentFolderContainer, folderFormSubmit);
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function folderFormSubmit(container) {
    var formData = jQuery("form#document-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/create_folder',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');

        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.status) {
                ty = 'success';
                jQuery('.modal', container).modal('hide');
                resetPagination($documentsGrid);
                showHideEmptyGridMessage("hide");
            } else {
                ty = 'error';
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                }
            }
            if ('undefined' !== typeof response.message) {
                pinesMessage({ty: ty, m: response.message});
            }


        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function renameDocument(id, type) {
    if (type === 'folder') {
        url = 'rename_folder';
    } else {
        url = 'rename_file';
    }
    jQuery.ajax({
        type: 'GET',
        dataType: 'JSON',
        data: {
            'document_id': id,
        },
        url: getBaseURL(module) + 'contracts/' + url,
        beforeSend: function () {
            jQuery('#loader-global').show();

        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".document-container").length <= 0) {
                    jQuery('<div class="d-none document-container"></div>').appendTo("body");
                    var documentRenameContainer = jQuery('.document-container');
                    documentRenameContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(documentRenameContainer);
                    jQuery("#form-submit", documentRenameContainer).click(function () {
                        renameDocumentSubmit(documentRenameContainer, url);
                    });
                    jQuery(documentRenameContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            renameDocumentSubmit(documentRenameContainer, url);
                        }
                    });
                }
            }


        }, complete: function () {
            jQuery('#loader-global').hide();

        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function renameDocumentSubmit(container, url) {
    var formData = jQuery("form#document-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/' + url,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');

        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.status) {
                ty = 'success';
                jQuery('.modal', container).modal('hide');
                resetPagination(jQuery('#' + gridContainer.attr('id')));
            } else {
                ty = 'error';
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                }


            }
            if ('undefined' !== typeof response.message) {
                pinesMessage({ty: ty, m: response.message});
            }


        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function loadDirectoryContent(id, documentLineage, module) {
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {id: id, module: moduleRecord},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL(module) + moduleController + "/check_folder_privacy",
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
function loadDirectoryContentCP(id, documentLineage) {
        jQuery('#term', documentsForm).val('');
        jQuery('#attachmentLookUp', $documentsGrid).val('');
        jQuery('#lineage', documentsForm).val(documentLineage);
        resetPagination($documentsGrid);
        resetSelectedDocuments();
        document.location.hash = '';
        return true;
}

function generateDocument() {
    jQuery.ajax({
        url: getBaseURL(module) + moduleController + '/generate_document/' + moduleRecordId,
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
                    jQuery('.modal', documentGenerationContainer).modal('handleUpdate');
                    jQuery('#templates', documentGenerationContainer).selectpicker().change(function () {
                        if (this.value !== '') {
                            jQuery.ajax({
                                url: getBaseURL(module) + moduleController + '/generate_document/' + moduleRecordId,
                                dataType: 'JSON',
                                type: 'GET',
                                data: {template_id: this.value, action: 'read'},
                                beforeSend: function () {
                                    jQuery('#loader-global').show();
                                },
                                success: function (response) {
                                    if (response.html) {
                                        jQuery('#template-fields-variables', documentGenerationContainer).html(response.html).removeClass('d-none');

                                    } else {
                                        jQuery('#template-fields-variables', documentGenerationContainer).html('').addClass('d-none');
                                    }
                                    jQuery('#generate', documentGenerationContainer).removeClass('d-none').unbind().click(function () {
                                        generateDocumentSubmit(documentGenerationContainer);
                                    });
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
        url: getBaseURL(module) + moduleController + '/generate_document/' + moduleRecordId,
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: response.msg});
                // reload grid
                $documentsGrid.data('kendoGrid').dataSource.read();
                jQuery('.modal', container).modal('hide');
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
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

function listFileVersions(fileId) {
    jQuery.ajax({
        url: getBaseURL(module) + moduleController + '/list_file_versions',
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

function resetSelectedDocuments() {
    selectedItems = [];
    newCreatedFolders = [];
    foldersHaveNewChildren = [];
}

function deleteUrl(id) {
    if (confirm(_lang.confirmationDeleteFile)) {
        jQuery.ajax({
            url: getBaseURL(module) + 'contracts/delete_url',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'docId': id
            },
            success: function (response) {
                if (response.result) {
                    $urlDocumentGrid.data('kendoGrid').dataSource.read();
                }
                pinesMessage({
                    ty: response.result ? 'information' : 'error',
                    m: response.result ? _lang.deleteRecordSuccessfull : _lang.recordNotDeleted
                });
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function saveAsPDF(id) {
    confirmationDialog('confirmation_message_to_convert_doc_to_pdf', {
        resultHandler: saveAsPDFSubmit,
        parm: {id: id}
    });
}

function saveAsPDFSubmit(data) {
    jQuery.ajax({
        url: getBaseURL(module) + 'contracts/save_as_pdf',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'document_id': data.id
        },
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                $documentsGrid.data('kendoGrid').dataSource.read();
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
            }
            pinesMessage({
                ty: response.result ? 'success' : 'error',
                m: response.result ? _lang.feedback_messages.updatesSavedSuccessfully : _lang.actionFailed
            });
        },
        complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function shareFolder(id, docPrivate) {
    jQuery.ajax({
        dataType: 'JSON',
        data: {
            'modeType': 'getHtml',
            'folder_id': id,
            'private': docPrivate
        },
        type: 'POST',
        url: getBaseURL(module) + 'contracts/share_folder',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status && response.html) {
                if (jQuery("#share-with-container").length <= 0) {
                    jQuery('<div id="share-with-container" class="primary-style"></div>').appendTo("body");
                    var sharedWithContainer = jQuery('#share-with-container');
                    sharedWithContainer.html(response.html).removeClass('d-none');
                    jQuery('#folder-id', sharedWithContainer).val(id);
                    lookupPrivateUsers(jQuery('#users-lookup', sharedWithContainer), 'watchers_users', '#selected-users', 'sharedWithMultiUsers', sharedWithContainer);
                    commonModalDialogEvents(sharedWithContainer, shareFolderSubmit);
                    initializeModalSize(sharedWithContainer, 0.3, 0.3);
                    checkBoxContainersValues({'sharedWithMultiUsers': jQuery('#selected-users', sharedWithContainer)}, sharedWithContainer);
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.access_denied});
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function shareFolderSubmit(container) {
    var formData = jQuery("form#share-with-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/share_folder',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');

        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.status) {
                ty = 'success';
                jQuery('.modal', container).modal('hide');
                resetPagination($documentsGrid);
            } else {
                ty = 'error';
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                }
            }
            if ('undefined' !== typeof response.message) {
                pinesMessage({ty: ty, m: response.message});
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            }


        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}