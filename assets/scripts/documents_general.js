var inlineEditingToolCookie = 'app4legalInlineEditingToolInstalled';

/**
 *
 * @param {integer} id
 * @param {integer} moduleId
 * @param {string} lineage
 * @returns {void}
 */
function displayInlineEditToolInstallModal(id, module, moduleId, lineage) {
    id = id || null;
    moduleId = moduleId || null;
    lineage = lineage || null;

    var installModalId = "#document-editor-modal";
    var html = jQuery('#document-editor-modal-inner-container').clone();
    if (jQuery(installModalId).length <= 0) {
        jQuery('<div id="document-editor-modal"></div>').appendTo("body");

        var installModal = jQuery(installModalId);
        installModal.html(html);
        initializeModalSize(installModalId, 0.4, 0.35);

        jQuery(document).keyup(function (e) {
            if (e.keyCode == 27) {
                jQuery('.modal', installModalId).modal('hide');
            }
        });

        jQuery(installModalId).find('input').keypress(function (e) {
            // Enter pressed?
            if (e.which == 13) {
                downloadInlineEditingTool();
            }
        });

        jQuery("#install-inline-edit", installModalId).click(function () {
            downloadInlineEditingTool();
        });

        jQuery('#already-installed-inline-edit', installModalId).click(function () {
            updateInlineEditingToolCookie(true, id, module, moduleId, lineage);
        });

        jQuery('.modal', installModalId).on('hidden.bs.modal', function () {
            destroyModal(jQuery(installModal));

        });

        jQuery('.modal', installModalId).on('show.bs.modal', function () {
            jQuery('#cancel-me').click();
        });

        jQuery('.modal', installModal).modal({
            keyboard: false,
            backdrop: 'static',
            show: true
        });
    }
}
function downloadInlineEditingTool() {
    jQuery("#document-editor-modal").find('.modal').modal('hide');
    document.cookie = inlineEditingToolCookie + '=true; expires=Tue, 19 Jan 2038 03:14:07 UTC';
    window.location.href = documentEditorDownloadURL;
}
function toggleFileIconSelection(icon, rowType) {
    if ('file' === rowType) {
        jQuery(icon).toggleClass('is-selected');
        jQuery(icon.parentNode).toggleClass('is-selected').nextAll().toggleClass('is-selected');
    }
}
function toggleFieldsetGroup(icon, div) {
    if (div.is(':visible')) {
        div.slideUp();
        icon.removeClass('fa-solid fa-angle-down');
        icon.addClass('fa-solid fa-angle-right');
    } else {
        div.slideDown();
        icon.removeClass('fa-solid fa-angle-right');
        icon.addClass('fa-solid fa-angle-down');
    }
}
/**
 *
 * @param {boolean} alreadyInstalled
 * @param {integer} id
 * @param {integer} moduleId
 * @param {integer} lineage
 * @returns {void}
 */
function updateInlineEditingToolCookie(alreadyInstalled, id, module, moduleId, lineage) {
    alreadyInstalled = alreadyInstalled || false;
    id = id || null;
    moduleId = moduleId || null;
    lineage = lineage || null;

    if (getCookie(inlineEditingToolCookie) == null) {
        var hostname = window.location.hostname;
        hostname = hostname.substring(0, hostname.indexOf('/'));
        document.cookie = inlineEditingToolCookie + '=true; domain=' + hostname + '; expires=Tue, 19 Jan 2038 03:14:07 UTC';
        (moduleController == 'vouchers') ? $attachmentDocumentGrid.data('kendoGrid').dataSource.read() : $documentsGrid.data('kendoGrid').dataSource.read();
        jQuery("#document-editor-modal").find('.modal').modal('hide');

        if (alreadyInstalled) {
            openDocumentInEditMode(id, module, moduleId, lineage);
        } else {
            downloadInlineEditingTool();
        }
    }
}

function documentEditorInstallationPopup() {
    var installModalId = "#document-editor-installation-modal-container";
    var html = jQuery('#document-editor-installation-modal');

    if (jQuery(installModalId).length <= 0) {

        jQuery('<div id="document-editor-installation-modal-container"></div>').appendTo("body");

        var installModal = jQuery(installModalId);
        installModal.html(html);
        initializeModalSize(installModalId);

        jQuery(document).keyup(function (e) {
            if (e.keyCode == 27) {
                jQuery('.modal', installModalId).modal('hide');
            }
        });

        jQuery(installModalId).find('input').keypress(function (e) {
            // Enter pressed?
            if (e.which == 13) {
                downloadInlineEditingTool();
            }
        });

        jQuery("#install-inline-edit", installModalId).click(function () {
            downloadInlineEditingTool();
        });

        jQuery('#already-installed-inline-edit', installModalId).click(function () {
            updateInlineEditingToolCookie(true, id, module, moduleId, lineage);
        });

        jQuery('.modal', installModalId).on('hidden.bs.modal', function () {
            destroyModal(jQuery(installModal));

        });

        jQuery('.modal', installModalId).on('show.bs.modal', function () {
            jQuery('#cancel-me').click();
        });

        jQuery('.modal', installModal).modal({
            keyboard: false,
            backdrop: 'static',
            show: true
        });
    }
}

/**
 *
 * @param {integer} id
 * @param module
 * @param {integer} moduleId
 * @param parentLineage
 * @returns {void}
 */
function openDocumentInEditMode(id, module, moduleId, parentLineage) {
    if (getCookie(inlineEditingToolCookie) !== null) {
        moduleId = moduleId || null;

        if (module === 'doc') {
            module = 'docs';
        }

        openLocation = 'appforlegal:' + encodeURIComponent('docId=' + id + '&docModuleType=' + module + '&moduleId=' + moduleId + '&lineage=' + parentLineage);
        window.location = openLocation;
        jQuery('#versions-list-container').addClass('d-none').html('');
    } else {
        displayInlineEditToolInstallModal(id, module, moduleId, parentLineage);
    }
}
/**
 *
 * @param {integer} id
 * @param {integer} moduleId
 * @param {string} lineage
 * @param {string} extension
 * @returns {void}
 */
function editDocument(id, module, moduleId, lineage, extension) {
    var url = (module == 'contract' ? getBaseURL(module) : getBaseURL()) + moduleController + '/view_document';
    if (moduleController == 'vouchers') {
        url = getBaseURL('money') + moduleController + '/view_document';
    }
    if(denyAccessToFeature('In-line-Word-Editor')){
        var planFeatureWarningMsgsObj = JSON.parse(planFeatureWarningMsgs);
        pinesMessage({ty: 'warning', m: planFeatureWarningMsgsObj['In-line-Word-Editor'] ? planFeatureWarningMsgsObj['In-line-Word-Editor'] : _lang.noAccessToPlanFeature});
        return;
    }

    if (extensionIsViewable(extension)) {
        jQuery.ajax({
            url: url ,
            method: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (response) {
                if (response.html) {
                    var viewDocDialogId = "#document-view-dialog";

                    if (jQuery(viewDocDialogId).length <= 0) {
                        jQuery('<div id="document-view-dialog"></div>').appendTo("body");

                        var viewDocDialog = jQuery(viewDocDialogId);
                        viewDocDialog.html(response.html);
                        initializeModalSize(viewDocDialogId, 0.9, 0.7);

                        jQuery(document).keyup(function (e) {
                            if (e.keyCode == 27) {
                                jQuery('.modal', viewDocDialogId).modal('hide');
                            }
                        });

                        jQuery(viewDocDialogId).find('input').keypress(function (e) {
                            // Enter pressed?
                            if (e.which == 13) {
                                jQuery('.modal', viewDocDialog).modal('hide');
                                openDocumentInEditMode(id, module, moduleId, lineage);
                            }
                        });

                        jQuery("#edit-doc-btn", viewDocDialogId).click(function () {
                            jQuery('.modal', viewDocDialog).modal('hide');
                            openDocumentInEditMode(id, module, moduleId, lineage);
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
            }
        });
    } else {
        openDocumentInEditMode(id, module, moduleId, lineage);
    }
}

function denyAccessToFeature(feature) {
    var planExcludedFeaturesArr = planExcludedFeatures.split(',');
    return planExcludedFeaturesArr.indexOf(feature) > -1;
}

/**
 *
 * @param {string} extension
 * @returns {Boolean}
 */
function extensionIsViewable(extension) {

    for (var i = 0; i < viewableExtensions.length; i++) {
        var viewableExt = viewableExtensions[i];

        if (viewableExt.extension === extension.toLowerCase()) {
            return true;
        }
    }

    return false;
}
/**
 *
 * @param {integer} id
 * @param {integer} moduleId
 * @param {string} lineage
 * @param {string} extension
 * @returns {void}
 */
function previewFile(id, module, moduleId, lineage, extension) {
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/preview_document' ,
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

function downloadFile(id, newest_version, module, controller) {
    module = module || false;
    controller = controller || moduleController;
    newest_version = newest_version || false;
    if(controller){
        var downloadUrl = (module && module !== 'core' ? getBaseURL(module) : getBaseURL()) + controller + '/download_file/' + id + '/' + newest_version;
        window.location = downloadUrl;
    }
}