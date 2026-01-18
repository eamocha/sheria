var id, module, moduleRecordId, lineage, moduleController, attachmentsContainer, thisDropzone;

jQuery(document).ready(function () {
    attachmentsContainer = jQuery('#attachments-module', '#client-display-attachments');
    module = jQuery('#module', attachmentsContainer).val();
    moduleController = jQuery('#module-controller', attachmentsContainer).val();
    moduleRecordId = jQuery('#module-record-id', attachmentsContainer).val();
    lineage = jQuery('#lineage', attachmentsContainer).val();
    id = jQuery('#id', '#client-display-attachments').val();
    dragAndDrop();
 });
function dragAndDrop() {
    var dragging = 0;
    $dropZone = new Dropzone('.attachments-drop-zone', {
        url: getBaseURL('money') + moduleController + '/upload_file',
        paramName: 'uploadDoc',
        addRemoveLinks: true,
        parallelUploads: 1,
        maxFilesize: allowedUploadSizeMegabite,
        thumbnailWidth: 200,
        thumbnailHeight: 90,
        createImageThumbnails: true,
        params: {
            dragAndDrop: true
        },
        clickable: '.zone-button',
        previewsContainer: '.zone-div',
        init: function () {
            thisDropzone = this;
            thisDropzone.on('addedfile', function (file) {
                var preview = document.getElementsByClassName('dz-preview');
                preview = preview[preview.length - 1];
                if (typeof file.id !== 'undefined') {
                    preview.title = _lang.docUploadedBy.sprintf([file.name, file.creator, file.createdOn]);
                    preview.id = 'preview-document-' + file.id;
                    var image = jQuery('img', preview);
                    var icon;
                    switch(file.extension.toLowerCase()){
                        case 'docx':
                        case 'doc':
                            icon = 'docx';
                            break;
                        case 'xlsx':
                        case 'xls':
                            icon = 'xlsx';
                            break;
                        case 'pptx':
                        case 'ppt':
                        case 'pps':
                            icon = 'pptx';
                            break;
                        case 'pdf':
                            icon = 'pdf';
                            break;
                        case 'txt':
                            icon = 'txt';
                            break;
                        default:
                            icon = 'file';
                    }
                    image.on('error', function () {
                        this.src = 'assets/images/' + icon + '.png';
                    });
                    file.previewElement.addEventListener("click", function () {
                        window.location.href = getBaseURL('money') + moduleController + '/download_file/' + file.id;
                    });
                    jQuery('.dz-remove', preview).remove();
                    file.deleteLink = Dropzone.createElement("<a class=\"dz-delete\" href=\"javascript:;\" onclick=\"confirmationDialog('confirm_delete_record', {resultHandler: documentDelete, parm: " + file.id + " });\">" + this.options.dictRemoveFile + "</a>");
                    file.deleteLink.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                    });
                    file.previewElement.appendChild(file.deleteLink);
                }
            });
            jQuery.get(getBaseURL('money') + moduleController + '/load_documents', {module: module, module_record_id: moduleRecordId, lineage: lineage}, function (response) {
                if (response.data == null) {
                    return;
                }
                jQuery.each(response.data, function (key, value) {
                    reloadAttachments(thisDropzone, value);
                });
            });
        },
        dragover: function (e) {
            this.element.classList.add("dz-drag-hover");
            return true;
        },
        dragenter: function (e) {
            dragging++;
        },
        dragleave: function (e) {
            this.element.classList.remove("dz-drag-hover");
            dragging--;
            return true;
        },
        drop: function (e) {
            this.element.classList.remove("dz-drag-hover");
            dragging++;
            return true;
        },
        accept: function (file, done) {
            if(csrfName.length > 0){
                this.options.params[csrfName] = csrfValue;
            }
            this.options.params['module'] = module;
            this.options.params['module_record_id'] = moduleRecordId;
            this.options.params['lineage'] = jQuery('#lineage', attachmentsContainer).val();
            return done();
        },
        error: function (file, message) {
            file.previewElement.classList.add("dz-error");
            file.previewElement.querySelector("[data-dz-errormessage]").textContent = message;
            jQuery('.dz-preview', '.zone-div').each(function () {
                jQuery(this).remove();
            });
            pinesMessage({ty: 'error', m: message});
            return true;
        },
        success: function (file, response) {
            if (response.status) {
                pinesMessage({ty: 'information', m: response.message});
                jQuery(file.previewElement).remove();
                var value = response.file;
                reloadAttachments(this, value);
                 jQuery('#modified-by-image').attr('src','users/get_profile_picture/' + response.file.modifiedBy + '/1');
                 jQuery('#modified-by').html(response.file.modifier_full_name);
                 jQuery('#modified-on').html(response.file.modifiedOn.substr(0, 10));
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
                    file.previewElement.querySelector("[data-dz-errormessage]").textContent = msg;
                }
            }
            return true;
        },
        complete: function (file) {
            queuedFiles = this.getQueuedFiles();
            uploadingFiles = this.getUploadingFiles();
            if ((queuedFiles.length > 0 || uploadingFiles.length > 0)) {
                return true;
            } else {
                dragging = 0;
                return true;
            }
        }
    });
}
function documentDelete(id) {
    jQuery.ajax({
        url: getBaseURL('money') + moduleController + '/delete_document',
        data: {document_id: id, module_record_id: moduleRecordId },
        type: 'POST',
        success: function (response) {
            pinesMessage({ty: response.status ? 'success' : 'error', m: response.message});
            jQuery('#document-item-' + id, jQuery('#client-display-attachments')).remove();
            jQuery('#preview-document-' + id, attachmentsContainer).remove();
            jQuery('#modified-by-image').attr('src','users/get_profile_picture/' + response.data.modifiedBy + '/1');
            jQuery('#modified-by').html(response.data.modifier_full_name);
            jQuery('#modified-on').html(response.data.modifiedOn.substr(0, 10));

        }, beforeSend: function () {
            jQuery('#loader-global').show();
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function reloadAttachments(that, value) {
    var mockFile = {name: value.full_name, size: value.size, id: value.id, createdOn: value.display_created_on, creator: value.display_creator_full_name, extension: value.extension};
    that.emit("addedfile", mockFile);
    that.options.thumbnail.call(this, mockFile, 'modules/money/clients/return_doc_thumbnail/' + value.id);
    // Make sure that there is no progress bar, etc...
    that.emit("complete", mockFile);
    that.files.push(mockFile);
}