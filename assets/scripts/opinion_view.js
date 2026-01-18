var id, module, moduleRecordId, lineage, moduleController, attachmentsContainer, thisDropzone,
matterModule, matterModuleRecordId, matterLineage, matterModuleController, matterAttachmentsContainer, thisDropzoneMatter;

jQuery(document).ready(function () {
    attachmentsContainer = jQuery('#attachments-module', '#opinion-display-form');
    module = jQuery('#module', attachmentsContainer).val();
    moduleController = jQuery('#module-controller', attachmentsContainer).val();
    moduleRecordId = jQuery('#module-record-id', attachmentsContainer).val();
    lineage = jQuery('#lineage', attachmentsContainer).val();
    id = jQuery('#id', '#opinion-display-form').val();

    matterAttachmentsContainer = jQuery('#matter-attachments-module', '#opinion-display-form');
    matterModule = jQuery('#module', matterAttachmentsContainer).val();
    matterModuleController = jQuery('#module-controller', matterAttachmentsContainer).val();
    matterModuleRecordId = jQuery('#module-record-id', matterAttachmentsContainer).val();
    matterLineage = jQuery('#lineage', matterAttachmentsContainer).val();
    dragAndDrop();
    if (typeof matterModule !== 'undefined') {
        dragAndDropMatterAttachment();
    }
    opinionCommentsList(id);
    showToolTip('#opinion-display-form', '#watchers');
    showToolTip('#opinion-display-form', '#contributors');
 });
function opinionCommentForm(opinionId, id) {
    opinionId = opinionId || false;
    id = id || false;
    if (!opinionId) {
        pinesMessage({ty: 'error', m: _lang.invalid_request});
        return false;
    }
    jQuery.ajax({
        url: getBaseURL() + 'legal_opinions/' + (!id ? 'add_comment' : 'edit_comment'),
        type: 'GET',
        data: {
            opinion_id: opinionId,
            id: id ? id : ''
        },
        dataType: "json",
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var commentContainerId = "#comment-edit-container";
                if (jQuery(commentContainerId).length <= 0) {
                    jQuery("<div id='comment-edit-container'></div>").appendTo("body");
                    var commentContainer = jQuery(commentContainerId);
                    commentContainer.html(response.html);
                    var tinymceId = jQuery(commentContainerId).attr("id");
                    initTinyOpinionView("#"+tinymceId,opinionId);
                    jQuery('#add-comment').addClass('hidden');
                    commonModalDialogEvents(commentContainer, opinionCommentSubmit);
                    initializeModalSize(commentContainer);
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
    });
}
function opinionCommentSubmit(container) {
    var id = jQuery('#comment-id', container).val();
    var opinionId = jQuery("#opinion-id", container).val();
    var sendNotificationsEmail = jQuery("#send_notifications_email", container).val();
    var formData = new FormData();
    formData.append('comment',tinymce.activeEditor.getContent());
    formData.append('id',id);
    formData.append('opinion_id',opinionId);
    formData.append('send_notifications_email',sendNotificationsEmail);
    jQuery.ajax({
        url: getBaseURL() + 'legal_opinions/' + (!id ? 'add_comment' : 'edit_comment'),
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        type: 'POST',
        dataType: "json",
        data: formData,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
            jQuery('.inline-error', container).addClass('d-none');
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('#modified-by-image').attr('src','users/get_profile_picture/' + response.data.modifiedBy + '/1');
                jQuery('#modified-by').html(response.data.modifier_full_name);
                jQuery('#modified-on').html(response.data.modifiedOn.substr(0, 10));
                if(!id){
                    if(jQuery("#no-comments").length > 0){
                        jQuery('#no-comments').remove();
                    }
                    jQuery('.comments-content').append(response.html);
                    opinionCommentFormInline('',true);
                }else{
                    opinionCommentsList(jQuery('#opinion-id', container).val());
                    if (undefined !== response.file) {
                        jQuery.each(response.file, function (key, value) {
                            reloadAttachments(thisDropzone, value);
                        });
                    }
                    if (undefined !== response.upload_result && !response.upload_result) {
                        pinesMessage({ty: 'error', m: response.upload_msg});
                    }
                    jQuery(".modal", container).modal("hide");
                }
                
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler,
    });
}
function opinionCommentFormInline(opinionId , dismiss) {
    opinionId = opinionId || false;
    var container = jQuery('#add-comment');
    var containerTxt = '#add-comment';
    if(dismiss){
        container.addClass('d-none');
        tinymce.remove(containerTxt +' #comment');
        return ;
    }
    jQuery('.inline-error', container).addClass('d-none');
    container.removeClass('d-none');
    initTinyOpinionView(containerTxt,opinionId);
    tinymce.activeEditor.setContent('');
    jQuery('html, body').animate({
        scrollTop: jQuery(containerTxt).offset().top
    }, "slow");
}
function initTinyOpinionView(containerTxt,opinionId){
    tinymce.remove(containerTxt+' #comment');
    tinymce.init({
        selector: containerTxt+' #comment',
        menubar: false,
        statusbar: false,
        branding: false,
        height: 200,
        resize: false,
        relative_urls:false,
        remove_script_host:false,
        plugins: ['link', 'lists','paste','image'],
        link_assume_external_targets:true,
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline numlist bullist | link | undo redo | image code ',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        formats: {
            underline: {inline: 'u', exact: true}
        }, paste_preprocess: function (plugin, args) {
            tinymce.activeEditor.windowManager.alert('Some of the pasted content will not stay with the same format');
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                jQuery('#comment_ifr').contents().find('body').attr("dir", "auto");
                jQuery('#comment_ifr').contents().find('body').focus();
                e.pasteAsPlainText = true;
                jQuery('.mce-i-image').parent().on('click' ,function (e) {
                    setTimeout(function(){ 
                        jQuery('.mce-browsebutton input[type="file"]').attr('accept','*');
                    }, 200);
                });
                styleInputsComment();
            });
        },
        /* without images_upload_url set, Upload tab won't show up*/
        images_upload_url: getBaseURL() + 'legal_opinions/' + 'upload_file',
        /* we override default upload handler to simulate successful upload*/
        images_upload_handler: function (blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', getBaseURL() + 'legal_opinions/upload_file');
                xhr.onload = function() {
                    var json;
                    json = JSON.parse(xhr.responseText);
                    if (json.status == false) {
                        failure(json.message);
                        return;
                    }
                    if (!json) {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    if (undefined !== json.file) {
                        reloadAttachments(thisDropzone, json.file);
                    }
                   var images = ['tif','jpg','png','gif','jpeg','bmp', 'jfif'];
                   if(images.includes(json.file.extension)){
                        tinymce.activeEditor.execCommand('mceInsertContent', false, '<img data-name="'+json.file.full_name+'" src="'+getBaseURL() + 'legal_opinions/return_doc_thumbnail/'+json.file.id+'" width="100" height="100"  />');
                   }else{
                        tinymce.activeEditor.execCommand('mceInsertContent', false, '<a href="'+getBaseURL() + 'legal_opinions/download_file/'+json.file.id+'" >'+json.file.full_name+'</a>');
                    }
                    var ed = parent.tinymce.editors[0];
                    ed.windowManager.windows[0].close();
                };
                formData = new FormData();
                formData.append('uploadDoc', blobInfo.blob());
                formData.append('module_record_id', opinionId);
                formData.append('module', 'task');//keep it as task because of the error with DMS.php
                formData.append('dragAndDrop', true);
                formData.append('lineage', '');
                xhr.send(formData);
        },
        init_instance_callback: function (editor) {
            editor.on('KeyUp', function (e) {
                styleInputsComment();
            }),
            editor.on('SetContent', function (e) {
                styleInputsComment();
            });
          }
    });
}
//add opinion file
function Attach_legal_opinion(opinionId,caseId, callback){

        callback = callback || false;
        callback ? addFileForm(opinionId, caseId, callback,false) : addFileForm(opinionId,caseId, false,false);

}
function addFileForm(id, caseId, callback,type){
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    id = id || false;
    caseId = caseId || 0;


    callback = callback || false;
    type = type || false;
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        //url: getBaseURL() + 'legal_opinions/' + (id ? 'edit/' + id : 'add/' + caseId + '/' + hearingId + '/' + (stageId ? '/' + stageId : '')),
        url: getBaseURL() + 'legal_opinions/add_legal_opinion_file/' + id,
        data: { action: 'return_html' },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#opinion-dialog').length <= 0) {
                    jQuery('<div id="opinion-dialog"></div>').appendTo("body");
                    var opinionDialog = jQuery('#opinion-dialog');
                    opinionDialog.html(response.html);

                    commonModalDialogEvents(opinionDialog);
                    var opinionId = jQuery("#id", opinionDialog).val();

                    jQuery("#form-opinion_file-submit", opinionDialog).click(function () {
                        opinionFileFormSubmit(opinionDialog, opinionId, callback);
                    });
                    jQuery(opinionDialog).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            opinionFileFormSubmit(opinionDialog, opinionId, callback);
                        }
                    });
                    if (caseId && !id) {
                        jQuery('#caseLookupId', opinionDialog).val(caseId);
                    }
                    opinionFormEvents(opinionDialog, response);

                }
            } else {
                pinesMessage({ ty: 'error', m: _lang.feedback_messages.privateOpinionMeetingMessage });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}

/**
 * submit the opinion file
 * @param container
 * @param id
 * @param callback
 */
function opinionFileFormSubmit(container, id, callback) {
    id = id || false;
    var formData = new FormData(document.getElementById(jQuery("form#attach-advisory-form", container).attr('id')));
    //formData.append('instructions', tinymce.activeEditor.getContent());
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: getBaseURL() + 'legal_opinions/add_legal_opinion_file/' + id,
        success: function (response) {
            if (response.totalNotifications >= 1) {
                jQuery('#pendingNotifications').css('display', 'inline-block').text(response.totalNotifications);
            } else {
                jQuery('#pendingNotifications').html('');
            }
            jQuery('.inline-error', '#opinion-dialog').addClass('d-none');
            if (response.result) {
                if (jQuery('#notify-me-before-container', container).is(':visible')) {
                    loadUserLatestReminders('refresh');
                }
                if (!response.cloned && typeof opinionCallBack === "function") {
                    opinionCallBack();
                }
                if (jQuery('#my-dashboard').length > 0) {
                    loadDashboardData('opinions');
                    pieCharts();
                }
                updateGetingStartedSteps('opinion');
                var msg = _lang.feedback_messages.addedNewOpinionSuccessfully.sprintf(['<a href="' + getBaseURL() + 'legal_opinions/view/' + response.id + '">' + response.opinion_code + '</a>']);
                pinesMessage({ ty: 'success', m: id ? _lang.feedback_messages.updatesSavedSuccessfully : msg });
                if (!response.cloned) {
                    jQuery('.modal', '#opinion-dialog').modal('hide');
                } else {
                    jQuery("#clone", container).val("no");
                    assignmentPerType(jQuery('#type', container).val(), 'opinion', container);
                }
                if (isFunction(callback)) {
                    callback(jQuery("#stage-id").val());
                }
            } else {
                displayValidationErrors(response.validationErrors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function styleInputsComment(){
    if(tinymce.activeEditor.getContent().length > 0){
        if(jQuery("#comment-id").val()){
            jQuery("#opinion-comment-form #comment").val(tinymce.activeEditor.getContent());
        }
        jQuery("#save-comment").removeAttr("disabled");
        jQuery("#save-comment").css({"background-color":"#3b7fc4",'color':'white'});
    }else{
        jQuery("#save-comment").attr("disabled","disabled");
        jQuery("#save-comment").css({"background-color":"#f4f5f7",'color':'gray'});
    }
}
function opinionCommentsList(id, showAll) {
    showAll = showAll || false;
    jQuery.ajax({
        url: getBaseURL() + 'legal_opinions/comments',
        type: "GET",
        data: {id: id, showAll: showAll ? 1 : 0},
        dataType: "JSON",
        contentType: false,
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                jQuery('#comments-container', '#opinion-display-form').html(response.html);
                scrollToComment();
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function scrollToComment(){
    if(jQuery("#comment-"+activeComment).length > 0){
        jQuery('html, body').animate({
            scrollTop: jQuery("#comment-"+activeComment).offset().top
        }, "slow");
        jQuery("#comment-"+activeComment).css("border-left","2px solid #205081");
    }
}
function commentToggle(id, container) {
    var id = parseInt(id);
    var comment = jQuery('#comment-' + id, container);
    if (jQuery('.comment-body', comment).is(':visible')) {
        jQuery('.comment-body', comment).slideUp();
        jQuery('i', '#comment-' + id + ' a:first').removeClass('fa-solid fa-angle-down');
        jQuery('i', '#comment-' + id + ' a:first').addClass('fa-solid fa-angle-right');
    } else {
        jQuery('.comment-body', comment).slideDown();
        jQuery('i', '#comment-' + id + ' a:first').removeClass('fa-solid fa-angle-right');
        jQuery('i', '#comment-' + id + ' a:first').addClass('fa-solid fa-angle-down');
    }
}
function commentDelete(params) {
    jQuery.ajax({
        url: getBaseURL() + 'legal_opinions/delete_comment',
        dataType: 'json',
        data: {id: params.id , module_record_id: moduleRecordId },
        type: 'POST',
        success: function (response) {
            if (response.result) {
                jQuery('#comment-' + parseInt(params.id), '#opinion-display-form').remove();
                jQuery('#modified-by-image').attr('src','users/get_profile_picture/' + response.data.modifiedBy + '/1');
                jQuery('#modified-by').html(response.data.modifier_full_name);
                jQuery('#modified-on').html(response.data.modifiedOn.substr(0, 10));
            } else {
                pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
            }
            jQuery('.modal', '.confirmation-dialog-container').modal('hide');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function dragAndDrop() {
    var dragging = 0;
    var previewedExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx'];
    var canBePreviewed = false;
    $dropZone = new Dropzone('#attachments-module-body', {
        url: getBaseURL() + moduleController + '/upload_file',
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
        clickable: '#attachments-form .zone-button',
        previewsContainer: '#attachments-form .zone-div',
        init: function () {
            thisDropzone = this;
            thisDropzone.on('addedfile', function (file) {
                var preview = jQuery('#attachments-form').find('.dz-preview');
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
                        for (i = 0; i < previewedExtensions.length; i++){
                            if (previewedExtensions[i] == file.extension.toLowerCase()) {
                                canBePreviewed = true;
                                break;
                            }
                            canBePreviewed = false;
                        }
                        if (canBePreviewed) {
                            previewFile(file.id, moduleController, moduleRecordId, lineage, file.extension);
                        }
                        else {
                            editDocument(file.id, moduleController, moduleRecordId, lineage, file.extension);
                        }    
                    });
                    jQuery('.dz-remove', preview).remove();
                    file.downloadLink = Dropzone.createElement("<a class=\"dz-download btn-info\" href=\"javascript:;\">" + this.options.dictDownloadFile + "</a>"); 
                    file.deleteLink = Dropzone.createElement("<a class=\"dz-delete\" href=\"javascript:;\" onclick=\"confirmationDialog('confirm_delete_record', {resultHandler: documentDelete, parm: " + file.id + " });\">" + this.options.dictRemoveFile + "</a>");
                    file.downloadLink.addEventListener("click", function(e){
                        downloadFile(file.id, true);
                        e.preventDefault();
                        e.stopPropagation();
                    });
                    file.deleteLink.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                    });
                    file.previewElement.appendChild(file.downloadLink);
                    file.previewElement.appendChild(file.deleteLink);
                }
            });
            jQuery.get(getBaseURL() + moduleController + '/load_documents', {module: module, module_record_id: moduleRecordId, lineage: lineage}, function (response) {
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
        url: getBaseURL() + moduleController + '/delete_document',
        data: {document_id: id, module_record_id: moduleRecordId },
        type: 'POST',
        success: function (response) {
            pinesMessage({ty: response.status ? 'success' : 'error', m: response.message});
            jQuery('#document-item-' + id, jQuery('#activity-module', '#opinion-display-form')).remove();
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
function dragAndDropMatterAttachment() {
    var dragging = 0;
    var previewedExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pps', 'pptx'];
    var canBePreviewed = false;
    $dropZoneMatter = new Dropzone('#matter-attachments-module-body', {
        url: getBaseURL() + matterModuleController + '/matter_upload_file',
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
        clickable: '#matter-attachments-form .zone-button',
        previewsContainer: '#matter-attachments-form .zone-div',
        init: function () {
            thisDropzoneMatter = this;
            thisDropzoneMatter.on('addedfile', function (file) {
                var preview = jQuery('#matter-attachments-form').find('.dz-preview');
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
                        for (i = 0; i < previewedExtensions.length; i++){
                            if (previewedExtensions[i] == file.extension.toLowerCase()) {
                                canBePreviewed = true;
                                break;
                            }
                            canBePreviewed = false;
                        }
                        if (canBePreviewed) {
                            previewFile(file.id, moduleController, moduleRecordId, lineage, file.extension);
                        }
                        else {
                            editDocument(file.id, moduleController, moduleRecordId, lineage, file.extension);
                        }
                    });
                    jQuery('.dz-remove', preview).remove();
                    file.downloadLink = Dropzone.createElement("<a class=\"dz-download btn-info\" href=\"javascript:;\">" + this.options.dictDownloadFile + "</a>");
                    file.deleteLink = Dropzone.createElement("<a class=\"dz-delete\" href=\"javascript:;\" onclick=\"confirmationDialog('confirm_delete_record', {resultHandler: matterDocumentDelete, parm: " + file.id + " });\">" + this.options.dictRemoveFile + "</a>");
                    file.downloadLink.addEventListener("click", function (e) {
                        downloadFile(file.id, true);
                        e.preventDefault();
                        e.stopPropagation();

                    });
                    file.deleteLink.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                    });
                    file.previewElement.appendChild(file.downloadLink);
                    file.previewElement.appendChild(file.deleteLink);
                }
            });
            jQuery.get(getBaseURL() + matterModuleController + '/matter_load_documents', {module: matterModule, module_record_id: matterModuleRecordId, opinion_record_id: moduleRecordId, lineage: matterLineage}, function (response) {
                for (var i = 0; i < response.length; i++) {
                    if (response[i]['data'] == null) {
                        return;
                    }
                    jQuery.each(response[i]['data'], function (key, value) {
                        reloadMatterAttachments(thisDropzoneMatter, value);
                    });
                }
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
            this.options.params['module'] = matterModule;
            this.options.params['module_record_id'] = matterModuleRecordId;
            this.options.params['opinion_record_id'] = moduleRecordId;
            this.options.params['lineage'] = jQuery('#lineage', matterAttachmentsContainer).val();
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
                reloadMatterAttachments(this, value);
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
function matterDocumentDelete(id) {
    jQuery.ajax({
        url: getBaseURL() + moduleController + '/matter_delete_document',
        data: {document_id: id, module_record_id: matterModuleRecordId },
        type: 'POST',
        success: function (response) {
            pinesMessage({ty: response.status ? 'success' : 'error', m: response.message});
            jQuery('#document-item-' + id, jQuery('#activity-module', '#opinion-display-form')).remove();
            jQuery('#preview-document-' + id, matterAttachmentsContainer).remove();
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
function showToolTip(container, popoverId) {
    jQuery(popoverId + '-link', container).tooltipster({
        content: jQuery('.popover-content', jQuery(popoverId, container)).html(),
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
}
function reloadAttachments(that, value) {
    var mockFile = {name: value.full_name, size: value.size, id: value.id, createdOn: value.display_created_on, creator: value.display_creator_full_name, extension: value.extension};
    that.emit("addedfile", mockFile);
    that.options.thumbnail.call(this, mockFile, 'legal_opinions/return_doc_thumbnail/' + value.id);
    // Make sure that there is no progress bar, etc...
    that.emit("complete", mockFile);
    that.files.push(mockFile);
}
function reloadMatterAttachments(that, value) {
    var mockFile = {name: value.full_name, size: value.size, id: value.id, createdOn: value.display_created_on, creator: value.display_creator_full_name, extension: value.extension};
    that.emit("addedfile", mockFile);
    that.options.thumbnail.call(this, mockFile, 'legal_opinions/return_matter_doc_thumbnail/' + value.id);
    that.emit("complete", mockFile);
    that.files.push(mockFile);
}
function opinionCallBack() {
    window.location = window.location.href;
}
