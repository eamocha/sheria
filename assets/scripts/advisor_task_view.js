var id, module, moduleRecordId, lineage, moduleController, attachmentsContainer, thisDropzone;
var tinymceInstance = null;
jQuery(document).ready(function () {
    attachmentsContainer = jQuery('#attachments-module', '#task-display-form');
    module = jQuery('#module', attachmentsContainer).val();
    moduleController = jQuery('#module-controller', attachmentsContainer).val();
    moduleRecordId = jQuery('#module-record-id', attachmentsContainer).val();
    lineage = jQuery('#lineage', attachmentsContainer).val();
    id = jQuery('#id', '#task-display-form').val();
    dragAndDrop();
    taskCommentsList(id);
    showToolTip('#task-display-form', '#watchers');
    showToolTip('#task-display-form', '#contributors');
});

function scrollToComment() {
    if (jQuery("#comment-" + activeComment).length > 0) {
        jQuery('html, body').animate({
            scrollTop: jQuery("#comment-" + activeComment).offset().top
        }, "slow");
        jQuery("#comment-" + activeComment).css("border-left", "2px solid #205081");
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
function dragAndDrop() {
    var dragging = 0;
    $dropZone = new Dropzone('.attachments-drop-zone', {
        url: getBaseURL() + moduleController + '/upload_file',
        paramName: 'uploadDoc',
        parallelUploads: 1,
        addRemoveLinks: false,
        maxFilesize: allowedUploadSizeMegabite,
        thumbnailWidth: 200,
        thumbnailHeight: 90,
        createImageThumbnails: true,
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
                    switch (file.extension.toLowerCase()) {
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
                        window.location.href = getBaseURL() + moduleController + '/download_file/' + file.id + '/advisor_task';
                    });
                    jQuery('.dz-remove', preview).remove();
                }
            });
            jQuery.get(getBaseURL() + moduleController + '/load_documents', { module: module, module_record_id: moduleRecordId, lineage: lineage }, function (response) {
                if (response.data == null) {
                    return;
                }
                jQuery.each(response.data, function (key, value) {
                    reloadAttachments(thisDropzone, value);
                });
            });
        },
        accept: function (file, done) {
            return done();
        },
        error: function (file, message) {
            file.previewElement.classList.add("dz-error");
            file.previewElement.querySelector("[data-dz-errormessage]").textContent = message;
            jQuery('.dz-preview', '.zone-div').each(function () {
                jQuery(this).remove();
            });
            pinesMessage({ ty: 'error', m: message });
            return true;
        },
        success: function (file, response) {
            if (response.status) {
                pinesMessage({ ty: 'information', m: response.message });
                jQuery(file.previewElement).remove();
                var value = response.file;
                reloadAttachments(this, value);
                jQuery('#modified-by-image').attr('src', 'users/get_profile_picture/' + response.file.modifiedBy + '/1');
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
                    pinesMessage({ ty: ty, m: msg });
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
    $dropZone.disable();
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
    var mockFile = { name: value.full_name, size: value.size, id: value.id, createdOn: value.display_created_on, creator: value.display_creator_full_name, extension: value.extension };
    that.emit("addedfile", mockFile);
    that.options.thumbnail.call(this, mockFile, 'tasks/return_doc_thumbnail/' + value.id);
    // Make sure that there is no progress bar, etc...
    that.emit("complete", mockFile);
    that.files.push(mockFile);
}

function taskCallBack() {
    window.location = window.location.href;
}
function taskCommentForm(taskId, id) {
    taskId = taskId || false;
    id = id || false;
    if (!taskId) {
        pinesMessage({ ty: 'error', m: _lang.invalid_request });
        return false;
    }
    jQuery.ajax({
        url: getBaseURL() + 'cases/' + (!id ? 'add_advisor_task_comment' : 'edit_advisor_task_comment'),
        type: 'GET',
        data: {
            advisor_task_id: taskId,
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
                    initTinyTaskView("#" + tinymceId, taskId);
                    jQuery('#add-comment').addClass('hidden');
                    commonModalDialogEvents(commentContainer, taskCommentSubmit);
                    initializeModalSize(commentContainer);
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
    });
}
function taskCommentSubmit(container) {
    var id = jQuery('#comment-id', container).val();
    var taskId = jQuery("#task-id", container).val();
    var sendNotificationsEmail = jQuery("#send_notifications_email", container).val();
    var formData = new FormData();
    formData.append('comment', tinymceInstance.getContent());
    formData.append('id', id);
    formData.append('advisor_task_id', taskId);
    formData.append('send_notifications_email', sendNotificationsEmail);
    jQuery.ajax({
        url: getBaseURL() + 'cases/' + (!id ? 'add_advisor_task_comment' : 'edit_advisor_task_comment'),
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
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                jQuery('#modified-by-image').attr('src', 'users/get_profile_picture/' + response.data.modifiedBy + '/1');
                jQuery('#modified-by').html(response.data.modifier_full_name);
                jQuery('#modified-on').html(response.data.modifiedOn.substr(0, 10));
                if (!id) {
                    if (jQuery("#no-comments").length > 0) {
                        jQuery('#no-comments').remove();
                    }
                    jQuery('.comments-content').append(response.html);
                    taskCommentFormInline('', true);
                } else {
                    taskCommentsList(jQuery('#task-id', container).val());
                    if (undefined !== response.file) {
                        jQuery.each(response.file, function (key, value) {
                            reloadAttachments(thisDropzone, value);
                        });
                    }
                    if (undefined !== response.upload_result && !response.upload_result) {
                        pinesMessage({ ty: 'error', m: response.upload_msg });
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

function taskCommentFormInline(taskId, dismiss) {
    taskId = taskId || false;
    var container = jQuery('#add-comment');
    var containerTxt = '#add-comment';
    if (dismiss) {
        container.addClass('hidden');
        tinymce.remove(containerTxt + ' #comment');
        return;
    }
    jQuery('.inline-error', container).addClass('d-none');
    container.removeClass('hidden');
    initTinyTaskView(containerTxt, taskId);
    //tinymceInstance.setContent('');
    // jQuery('html, body').animate({
    //     scrollTop: jQuery(containerTxt).offset().top
    // }, "slow");
}
function initTinyTaskView(containerTxt, taskId) {
    tinymce.remove(containerTxt + ' #comment');
    tinymce.init({
        selector: containerTxt + ' #comment',
        menubar: false,
        statusbar: false,
        branding: false,
        height: 200,
        resize: false,
        relative_urls: false,
        remove_script_host: false,
        plugins: ['link', 'paste', 'image'],
        link_assume_external_targets: true,
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | link | undo redo | image code ',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        formats: {
            underline: { inline: 'u', exact: true }
        }, paste_preprocess: function (plugin, args) {
            tinymceInstance.windowManager.alert('Some of the pasted content will not stay with the same format');
        },
        setup: function (editor) {
            tinymceInstance = editor;
            editor.on('init', function (e) {
                jQuery('#comment_ifr').contents().find('body').attr("dir", "auto");
                jQuery('#comment_ifr').contents().find('body').focus();
                e.pasteAsPlainText = true;
                jQuery('.mce-i-image').parent().on('click', function (e) {
                    setTimeout(function () {
                        jQuery('.mce-browsebutton input[type="file"]').attr('accept', '*');
                    }, 200);
                });
                styleInputsComment();
            });
        },
        /* without images_upload_url set, Upload tab won't show up*/
        //images_upload_url: getBaseURL() + 'tasks/' + 'upload_file',
        /* we override default upload handler to simulate successful upload*/
        // images_upload_handler: function (blobInfo, success, failure) {
        //     var xhr, formData;
        //     xhr = new XMLHttpRequest();
        //     xhr.withCredentials = false;
        //     xhr.open('POST', getBaseURL() + 'tasks/upload_file');
        //     xhr.onload = function () {
        //         var json;
        //         json = JSON.parse(xhr.responseText);
        //         if (json.status == false) {
        //             failure(json.message);
        //             return;
        //         }
        //         if (!json) {
        //             failure('Invalid JSON: ' + xhr.responseText);
        //             return;
        //         }
        //         if (undefined !== json.file) {
        //             reloadAttachments(thisDropzone, json.file);
        //         }
        //         var images = ['tif', 'jpg', 'png', 'gif', 'jpeg', 'bmp', 'jfif'];
        //         if (images.includes(json.file.extension)) {
        //             tinymceInstance.execCommand('mceInsertContent', false, '<img data-name="' + json.file.full_name + '" src="' + getBaseURL() + 'tasks/return_doc_thumbnail/' + json.file.id + '" width="100" height="100"  />');
        //         } else {
        //             tinymceInstance.execCommand('mceInsertContent', false, '<a href="' + getBaseURL() + 'tasks/download_file/' + json.file.id + '" >' + json.file.full_name + '</a>');
        //         }
        //         var ed = parent.tinymce.editors[0];
        //         ed.windowManager.windows[0].close();
        //     };
        //     formData = new FormData();
        //     formData.append('uploadDoc', blobInfo.blob());
        //     formData.append('module_record_id', taskId);
        //     formData.append('module', 'task');
        //     formData.append('dragAndDrop', true);
        //     formData.append('lineage', '');
        //     xhr.send(formData);
        // },
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
function styleInputsComment() {
    if (tinymceInstance.getContent().length > 0) {
        if (jQuery("#comment-id").val()) {
            jQuery("#task-comment-form #comment").val(tinymceInstance.getContent());
        }
        jQuery("#save-comment").removeAttr("disabled");
        jQuery("#save-comment").css({ "background-color": "#3b7fc4", 'color': 'white' });
    } else {
        jQuery("#save-comment").attr("disabled", "disabled");
        jQuery("#save-comment").css({ "background-color": "#f4f5f7", 'color': 'gray' });
    }
}
function taskCommentsList(id, showAll) {
    showAll = showAll || false;
    jQuery.ajax({
        url: getBaseURL() + 'cases/advisor_task_comments',
        type: "GET",
        data: { id: id, showAll: showAll ? 1 : 0 },
        dataType: "JSON",
        contentType: false,
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                jQuery('#comments-container', '#task-display-form').html(response.html);
                scrollToComment();
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function commentDelete(params) {
    jQuery.ajax({
        url: getBaseURL() + 'cases/delete_advisor_task_comment',
        dataType: 'json',
        data: { id: params.id, module_record_id: moduleRecordId },
        type: 'POST',
        success: function (response) {
            if (response.result) {
                jQuery('#comment-' + parseInt(params.id), '#task-display-form').remove();
                jQuery('#modified-by-image').attr('src', 'users/get_profile_picture/' + response.data.modifiedBy + '/1');
                jQuery('#modified-by').html(response.data.modifier_full_name);
                jQuery('#modified-on').html(response.data.modifiedOn.substr(0, 10));
            } else {
                pinesMessage({ ty: 'error', m: _lang.deleteRecordFailed });
            }
            jQuery('.modal', '.confirmation-dialog-container').modal('hide');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}