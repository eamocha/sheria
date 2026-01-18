var $documentsGrid, documentsForm, $documentsGridOptions, moduleDocumentpath_typeValues, $urlDocumentGridOptions,
    $urlDocumentGrid, $enableQuickSearch = false, allowedUploadSizeMegabite, module;
var officeFileTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx'];
var viewableFileTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx', 'pdf', 'gif', 'jpg', 'jpeg', 'png', 'html', 'txt', 'htm', 'mpg', 'mp3', 'mp4', 'flv', 'mov', 'wav', '3gp', 'avi', 'jfif'];
var selectedItems = [];
var newCreatedFolders = [];
var foldersHaveNewChildren = [];
var moduleController = 'conveyancing';
var module = 'customer-portal';
var gridContainer, $dropZone;

function documentTabEvents(module) {
    module = module || 'contract';

    $documentsGrid = jQuery('#documentsGrid');
    documentsForm = jQuery('#documentsForm');
    moduleRecord = jQuery('#module-record', documentsForm).val();
    moduleRecordId = moduleRecordId === '' ? jQuery('#module-record-id', documentsForm).val() : moduleRecordId;
    searchByUrl(module);
}
function validURL(str) {
    if (new RegExp("([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?").test(str)) {
        return '<a href="' + str + '" target="_blank">' + str + '</a>';
    }
    return str;
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
                    jQuery('#related-documents-count', '.conveyancing-container').text(response.related_documents_count);
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function uploadFileForm() {
    //var conveyancingId = jQuery('#id', '#conveyancing-docs-container').val()|0;//remove this line if you want to use the default value of conveyancingId
    
    jQuery.ajax({
        dataType: 'JSON',
        data: {'conveyancing_id': conveyancingId, 'lineage': jQuery('#lineage', documentsForm).val()|0},//remove this line if you want to use the default value of lineage
        type: 'GET',
        url: getBaseURL(module) + 'conveyancing/upload_file',
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
        url: getBaseURL(module) + 'conveyancing/upload_file',
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
               // resetPagination($documentsGrid);
                //showHideEmptyGridMessage("hide");
                jQuery('#related-documents-count', '.conveyancing-container').text(response.related_documents_count);
                //load docs. clear #conveyancing-docs container first.
                          
                loadDocuments();
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
function download_attachment(id, newest_version, module, controller) {
    module = module || false;
    controller = controller || moduleController;
    newest_version = newest_version || false;
    if(controller){
        var downloadUrl = (module && module !== 'core' ? getBaseURL(module) : getBaseURL()) + controller + '/download_file/' + id + '/' + newest_version;
        window.location = downloadUrl;
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
        url: getBaseURL(module) + 'conveyancing/save_as_pdf',
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
                jQuery('#related-documents-count', '.conveyancing-container').text(response.related_documents_count);
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

