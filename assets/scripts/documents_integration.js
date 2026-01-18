jQuery(document).ready(function () {
    // load provider grids
    for (i in loadedIntegrationProviders){
        if(loadedIntegrationProviders[i] != 'A4L'){
            loadIntegrationGrid(loadedIntegrationProviders[i]);
        }
    }
    integrationAttachmentForm = jQuery('#integrationAttachmentForm');
    customGridToolbarCSSButtons();

    jQuery('.integration-tab').one('click', function(){
        let integration = jQuery(this).data('integration-code');
        loadApp4Legal360DocsIframe(integration);
    });
});

function loadIntegrationGrid(provider) {
    try {
        var $integrationDocumentsGrid = jQuery('#integrationDocumentsGrid_' + provider);
        var integrationDocumentsForm = jQuery('#integrationDocumentsForm_' + provider);
        var integrationDocumentsDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + "integrations/load_documents/" + provider,
                    dataType: "JSON",
                    type: "POST",
                    complete: function (XHRObj) {
                        jQuery('#loader-global').hide();
                        if (XHRObj.responseText == 'access_denied') {
                            return false;
                        }
                        if (XHRObj.responseText == 'login_needed') {
                            return false;
                        }
                        $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        jQuery('#lineage', integrationDocumentsForm).val($response.lineage);
                        updateIntegrationCrumbLink($response.lineage_path, $response.provider, $response.default_folder_path);
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                        animateDropdownMenuInGrids('integrationDocumentsGrid_' + provider);
                    },
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    }
                },
                update: {
                    url: getBaseURL() + moduleController + "/integration_edit_documents",
                    dataType: "jsonp",
                    type: "POST",
                    complete: function (XHRObj) {
                        $integrationDocumentsGrid.data('kendoGrid').dataSource.read();
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
                        options.lineage = jQuery('#lineage', integrationDocumentsForm).val();
                        options.term = jQuery('#term', integrationDocumentsForm).val();
                    }
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {editable: false, type: "string"},
                        name: {editable: false, type: "string"},
                        type: {editable: false, type: "string"},
                        lineage: {editable: false, type: "string"}
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
                            row['id'] = escapeHtml(row['id']);
                            row['name'] = escapeHtml(row['name']);
                            row['type'] = escapeHtml(row['type']);
                            row['lineage'] = escapeHtml(row['lineage']);
                            row['providerName'] = escapeHtml(row['providerName']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            }, error: function (e) {
                defaultErrorHandler(e.xhr)
            },
            batch: true, pageSize: 20, serverPaging: false, serverFiltering: false, serverSorting: false
        });
        $integrationDocumentsGridOptions = {
            autobind: true,
            dataSource: integrationDocumentsDataSrc,
            columns: [
                {
                    field: "id",
                    template:
                            '<div class="dropdown">' + gridActionIconHTML +
                            '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                            '<a class="dropdown-item" href="##" class="#=(type===\'folder\') ? \'hide\' : \'\'#" onclick="integrationDownloadFile(\'#= name #\', \'#= lineage #\', \'#= providerName #\'); return false;">' + _lang.download + '</a>' +
                            '<a class="dropdown-item" href="##" onclick="integrationRenameDocument(\'#= addslashes(encodeURIComponent(name)) #\', \'#= name #\', \'#= lineage #\', \'#= providerName #\'); return false;">' + _lang.rename + '</a>' +
                            '<a class="dropdown-item" href="##" onclick="integrationPreDeleteDoc(\'#= lineage #\', \'#= providerName #\'); return false;">' + _lang.deleteRow + '</a>' +
                            '</div>' +
                            '</div>' +
                            '<i fileId="#= id #" class="pull-right fs-common-icon #= (type == \'folder\') ? \'selectable \' + getExtIcon(\'folder\') : getExtIcon(extension) #" onclick="toggleFileIconSelection(this, \'#= type #\')"></i>',
                    sortable: false, filterable: false, title: _lang.actions, width: '30px'
                },
                {
                    field: "name",
                    template: '<i class="iconLegal"></i><a href="javascript:;" # if (type == \'file\') { # onclick="integrationDownloadFile(\'#= name #\', \'#= lineage #\', \'#= providerName #\')" # } else { # onclick="integrationLoadDirectoryContent(\'#= addslashes(lineage) #\', \'#= providerName #\')" # } #>#= name #</a>',
                    title: _lang.name,
                    width: '500px'
                }
            ],
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
                            + '<input type="text" class="form-control search" placeholder="' + _lang.searchIn.sprintf(['App4Legal']) + '" id="integrationAttachmentLookUp" onkeyup="integrationAttachmentQuickSearch(\'' + provider + '\', event.keyCode, this.value, modelLineage);" title="' + _lang.search + '" />'
                            + '</div>'
                            + '</div>'
                            + '<div class="col-md-2 pull-right">'
                            + '<div class="btn-group pull-right">'
                            + '<button type="button" class="btn btn-info dropdown-toggle gridActionsButton" data-toggle="dropdown">' + _lang.actions
                            + ' <span class="caret"></span>'
                            + '<span class="sr-only">Toggle Dropdown</span>'
                            + '</button>'
                            + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                            + '<a class="dropdown-item" href="javascript:;" onclick="integrationUploadFile(\'' + provider + '\');">' + _lang.uploadFile + '</a>'
                            + '<a class="dropdown-item" href="javascript:;" onclick="integrationCreateFolder(\'' + provider + '\');">' + _lang.createFolder + '</a>'
                            + '</div>'
                            + '</div>'
                            + '</div>'
                }
            ],
            columnMenu: {messages: _lang.kendo_grid_sortable_messages}
        };
        if (undefined === $integrationDocumentsGrid.data('kendoGrid')) {
            $integrationDocumentsGrid.kendoGrid($integrationDocumentsGridOptions);
            return false;
        }
        resetPagination($integrationDocumentsGrid);
    } catch (e) {
    }
}
function integrationAttachmentQuickSearch(provider, keyCode, term, modelLineage) {
    if (keyCode === 13) {
        var integrationDocumentsForm = jQuery('#integrationDocumentsForm_' + provider);
        if (term === "") {
            jQuery('#lineage', integrationDocumentsForm).val(modelLineage);
        }
        jQuery('#term', integrationDocumentsForm).val(term);
        var $integrationDocumentsGrid = jQuery('#integrationDocumentsGrid_' + provider);
        resetPagination($integrationDocumentsGrid);
    }
}
function integrationLoadDirectoryContent(documentLineage, provider) {
    var $integrationDocumentsGrid = jQuery('#integrationDocumentsGrid_' + provider);
    var integrationDocumentsForm = jQuery('#integrationDocumentsForm_' + provider);
    document.location.hash = '';
    jQuery('#term', integrationDocumentsForm).val('');
    jQuery('#integrationAttachmentLookUp', $integrationDocumentsGrid).val('');
    jQuery('#lineage', integrationDocumentsForm).val(documentLineage);
    var directories = documentLineage.split('/');
    let searchInPlaceholder = [directories[directories.length - 1]];
    if(searchInPlaceholder == "default_application_folder" || searchInPlaceholder == "") searchInPlaceholder = ["App4legal"];
    jQuery('#integrationAttachmentLookUp').attr('placeholder', _lang.searchIn.sprintf(searchInPlaceholder));
    resetPagination($integrationDocumentsGrid);
}
function updateIntegrationCrumbLink(lineage, provider, defaultFolderPath){
    var BreadcrumbContainer = jQuery('#integrationBreadcrumbContainer_' + provider);
    jQuery('.not-fixed', BreadcrumbContainer).remove();
    // count nb of slashes in lineage: one slash => root directry otherwise it is a sub folder
    if((lineage.match(new RegExp(/\//ig, "g")) || []).length > 0){
        // build bread crumb when accessing sub folders
        var directories = lineage.split('/');
        for (i in directories) {
            if(i > 0){ // root directory is already exists 
                var path = "";
                for (j in directories){
                    if(j> 0 && j<=i){
                        path += "/" + directories[j];
                    }
                }
                nodeToAdd = '<li class="breadcrumb-item not-fixed">';
                if(i == 1 && modelName != 'doc'){
                    nodeToAdd += directories[i];
                }else{
                    if(defaultFolderPath){
                        nodeToAdd += '<a href="javascript: integrationLoadDirectoryContent(\'' + defaultFolderPath + addslashes(path) + '\', \'' + provider + '\');">' + directories[i] + '</a>';
                    } else{
                        nodeToAdd += '<a href="javascript: integrationLoadDirectoryContent(\'' + addslashes(path) + '\', \'' + provider + '\');">' + directories[i] + '</a>';
                    }
                }
                nodeToAdd += '</li>';
                BreadcrumbContainer.append(nodeToAdd);
            }
        }
        jQuery('li.active', BreadcrumbContainer).removeClass('active bold');
    }
}
function integrationRenameDocument(oldNameSafe, oldName, lineage, provider) {
    name = prompt(_lang.rename + ' "' + decodeURIComponent(oldNameSafe) + '"', decodeURIComponent(oldNameSafe));
    parentPath = lineage.slice(0, -(oldName.length));
    if ('null' != name && "" != name) {
        jQuery.ajax({
            url: getBaseURL() + "integrations/rename_document/",
            type: 'POST',
            dataType: 'JSON',
            data: {
                'provider': provider,
                'old_name': encodeURIComponent(lineage),
                'new_name': parentPath + encodeURIComponent(name)
            },
            success: function (response) {
                if (!response.result) {
                    pinesMessage({ty: 'error', m: response.message});
                } else{
                    pinesMessage({ty: 'success', m: response.message});
                }
                var $integrationDocumentsGrid = jQuery('#integrationDocumentsGrid_' + provider);
                if (undefined != $integrationDocumentsGrid.data('kendoGrid')) {
                    $integrationDocumentsGrid.data('kendoGrid').dataSource.read();
                }
            },
            error: defaultErrorHandler
        });
    }else if(name == ""){
        pinesMessage({ty: 'error', m: _lang.actionFailed});
    }
}
function defaultErrorHandler(e, n) {
    switch (e.responseText) {
        case"access_denied":
            ajaxAccessDenied();
            break;
        case"login_needed":
            ajaxLoginForm();
            break;
        default:
            pinesMessage({ty: "error", m: _lang.actionFailed});
    }
    if (jQuery("#loader-global").is(':visible'))
        jQuery("#loader-global").hide();
}
function integrationUploadFile(provider){
    var integrationDocumentsForm = jQuery('#integrationDocumentsForm_' + provider);
    var attachmentDialog = jQuery("#integrationAttachmentDialog");
    if (!attachmentDialog.is(':data(dialog)')) {
        attachmentDialog.dialog({
            autoOpen: true,
            buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info',
                    click: function () {
                        integrationAttachmentForm.validationEngine({
                            validationEventTrigger: "submit",
                            autoPositionUpdate: true,
                            promptPosition: 'bottomRight',
                            scroll: false
                        });
                        var dataIsValid = jQuery(integrationAttachmentForm, this).validationEngine('validate');
                        if (dataIsValid) {
                            showHideEmptyGridMessage("hide");
                            jQuery('#lineage', integrationAttachmentForm).val(jQuery('#lineage', integrationDocumentsForm).val());
                            jQuery('#provider', integrationAttachmentForm).val(provider);
                            integrationAttachmentForm.attr('action', integrationAttachmentForm.attr('action') + '/' + provider);
                            integrationAttachmentForm.submit();
                        }
                    }
                },
                {
                    text: _lang.cancel,
                    class: 'btn btn-link',
                    click: function () {
                        resetInterationAttachmentDocumentForm();
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
                resetInterationAttachmentDocumentForm();
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
function integrationAttachmentOnFormSubmit() {
    jQuery('#integrationAttachmentFormContainer', '#integrationAttachmentDialog').addClass('d-none');
    jQuery('#loading', '#integrationAttachmentDialog').removeClass('d-none');
    return true;
}
function resetInterationAttachmentDocumentForm() {
    var attachmentDialog = jQuery("#integrationAttachmentDialog");
    jQuery(integrationAttachmentForm, attachmentDialog).validationEngine('hide');
    jQuery(integrationAttachmentForm, attachmentDialog)[0].reset();
}
function uploadIntegrationDocumentDone(message, type, provider){
    if (message)
        pinesMessage({ty: type, m: message});
    jQuery("#integrationAttachmentDialog").dialog("close");
    resetPagination(jQuery('#integrationDocumentsGrid_' + provider));
    jQuery('#integrationAttachmentFormContainer', '#integrationAttachmentDialog').removeClass('d-none');
    jQuery('#loading', '#integrationAttachmentDialog').addClass('d-none');
}
function integrationCreateFolder(provider){
    var $integrationDocumentsGrid = jQuery('#integrationDocumentsGrid_' + provider);
    var integrationDocumentsForm = jQuery('#integrationDocumentsForm_' + provider);
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
                            jQuery('#lineage', documentFolderForm).val(jQuery('#lineage', integrationDocumentsForm).val());
                            jQuery('#provider', documentFolderForm).val(provider);
                            var that = this;
                            var formData = jQuery(documentFolderForm, this).serialize();
                            jQuery.ajax({
                                data: formData,
                                dataType: 'JSON',
                                type: 'POST',
                                url: getBaseURL() + 'integrations/create_folder',
                                success: function (response) {
                                    if (response.result) {
                                        if ($integrationDocumentsGrid.length) {
                                            showHideEmptyGridMessage("hide");
                                            resetPagination($integrationDocumentsGrid);
                                        }
                                        pinesMessage({ty: 'information', m: response.message});
                                    } else {
                                        jQuery("#output", that).html('&nbsp;');
                                        if (undefined === response.validationErrors) {
                                            if (!response.result) {
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
                                error: defaultErrorHandler
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
function integrationPreDeleteDoc(path, provider){
    confirmationDialog('confirm_delete_record', {resultHandler: integrationDeleteDocument, parm: {path: path, provider: provider}});
}
function integrationDeleteDocument(data){
    var $integrationDocumentsGrid = jQuery('#integrationDocumentsGrid_' + data['provider']);
    jQuery.ajax({
        url: getBaseURL() + 'integrations/delete_document',
        type: "POST",
        data: {provider: data['provider'], path: data['path']},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                if ($integrationDocumentsGrid.length) {
                    showHideEmptyGridMessage("hide");
                    resetPagination($integrationDocumentsGrid);
                }
                pinesMessage({ty: 'information', m: response.message});
            } else {
                pinesMessage({ty: 'error', m: response.message});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultErrorHandler
    });
}
function integrationDownloadFile(fileName, path, provider){
    jQuery('<form class="d-none" action="' + getBaseURL() + 'integrations/download_file' + '" method="post" name="integration-temp-form" id="integration-temp-form-id"><input type="hidden" name="provider" value="' + provider + '" /><textarea name="path">' + path + '</textarea><textarea name="file_name">' + fileName + '</textarea></form>').appendTo("body").submit().remove();
}

function loadApp4Legal360DocsIframe(integration) {
    let app4Legal360DocsUrl = `https://docs.app4legal.com/${integration}`;
    let lang                = document.documentElement.lang;
    let moduleName          = document.getElementById('module').value;
    let moduleRecordId      = document.getElementById('module-record-id').value;
    let iframeHeight        = jQuery('div.k-state-active').height();

    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + '/users/get_api_token_data',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                let jsonObj = {
                    "x-api-key": response.apiKey,
                    "api-base-url": response.apiBaseUrl,
                    "lang": lang,
                    "module": moduleName,
                    "module-record-id": moduleRecordId
                };
                let iframeEl = document.getElementById(`app4legal-${integration}-iframe`);
                if (iframeEl != null) {
                    iframeEl.addEventListener('load', function() {
                        console.log(`${integration} iframe is loaded, sending message...`);
                        iframeEl.contentWindow.postMessage({payload: jsonObj}, "*");
                        jQuery('#loader-global').hide();
                    });
                }
                iframeEl.src = app4Legal360DocsUrl;
                iframeEl.height = iframeHeight + 'px';
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
