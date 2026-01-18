
datePickerOptions = {
    weekStart: 1,
    todayHighlight: true,
    format: "yyyy-mm-dd",
    autoclose: true,
    showOnFocus: false,
    language: _lang.languageSettings['langName'],
    startDate: -Infinity,
    endDate: Infinity,
    viewMode: 'days',
    minViewMode: 'days'
};

jQuery(document).ready(function () {
    jQuery('[data-toggle="tooltip"]').tooltip();
    var container = jQuery('#modifiable-fields');
    var lookupDetails = {
        'lookupField': jQuery('#lookup-requested-by', container),
        'errorDiv': 'requestedBy',
        'hiddenId': '#requested-by',
        'onSelect': 'updateContractRequester'
    };
    lookUpCustomerPortalUsers(lookupDetails, container, false, 'contracts');
    lookupDetails = {
        'lookupField': jQuery('#lookup-watchers', container),
        'lookupContainer': 'watcher-lookup-contract',
        'errorDiv': 'lookupWatchers',
        'boxName': 'watchers',
        'boxId': '#selected-watchers',
        'onSelect': 'updateContractWatchers'
    };
    lookUpCustomerPortalUsers(lookupDetails, jQuery(container), true);
    setActiveTab('contracts');
});

/*
* clear requestedBy if the user removed did empty the field or typed a none valuid requester
*/
function clearRequestedBy() {
    if (jQuery('#requested-by').val() == '') {
        updateContractRequester();
    }
}

/*
* update the contract requester when user select data
*/
function updateContractRequester() {
    jQuery.ajax({
        url: 'modules/customer-portal/contracts/update_contract_requester',
        type: "post",
        data: {contract_id: jQuery("#contract-id").val(), requestedBy: jQuery('#requested-by').val()},
        dataType: "JSON",
        success: function (response) {
            if (response.status && response.user_contract_permission == "none") {
                window.location.href = getBaseURL('customer-portal') + 'contracts?ty=' + response.message.type + '&m=' + response.message.text;
            } else if (response.status && response.user_contract_permission == "read") {
                window.location.reload();
            } else if (response.status != null) {
                if (typeof response.modifiedOn !== undefined) {
                    jQuery('#last-update').html(response.modifiedOn);
                }
                pinesMessage({ty: response.message.type, m: response.message.text});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/*
* update contract watchers when user select data
*/
function updateContractWatchers() {
    var watchers_ids = [];
    jQuery("input[name*='watchers']", "#selected-watchers").each(function () {
        watchers_ids.push(jQuery(this).val());
    });
    jQuery.ajax({
        url: "modules/customer-portal/contracts/update_contract_watchers",
        type: "post",
        data: {contract_id: jQuery("#contract-id").val(), watchers: watchers_ids},
        dataType: "JSON",
        success: function (response) {
            if (response.status && response.user_contract_permission == "none") {
                window.location.href = getBaseURL('customer-portal') + 'contracts?ty=' + response.message.type + '&m=' + response.message.text;
            } else if (response.status != null) {
                pinesMessage({ty: response.message.type, m: response.message.text});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractAddComment(contractId) {
    jQuery.ajax({
        url: 'modules/customer-portal/contracts/add_comment/',
        type: 'GET',
        data: {
            contract_id: contractId,
        },
        dataType: 'JSON',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var commentContainerId = "#comment-form-container";
                if (jQuery(commentContainerId).length <= 0) {
                    jQuery("<div id='comment-form-container'></div>").appendTo("body");
                    var commentContainer = jQuery(commentContainerId);
                    commentContainer.html(response.html);
                    initTiny('comment', "#comment-form-container", contractId);
                    jQuery(".modal", commentContainer).modal({
                        keyboard: false,
                        backdrop: "static",
                        show: true
                    });
                    jQuery('.modal-body',commentContainer).on("scroll", function() {
                        jQuery('.bootstrap-select.open').removeClass('open');
                    });
                    jQuery('.modal', commentContainer).on('shown.bs.modal', function (e) { // IE9 not supported
                        if (jQuery(".first-input", commentContainer).val() == '') {
                            jQuery(".first-input", commentContainer).focus();
                        }
                    });
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery(".modal", commentContainer).modal("hide");
                        }
                    });
                    jQuery('.modal', commentContainer).on('hidden.bs.modal', function () {
                        commentContainer.remove();
                    });
                    jQuery("#form-submit", commentContainer).click(function () {
                        contractAddCommentSubmit(commentContainer);
                    });
                    jQuery(commentContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            contractAddCommentSubmit(commentContainer);
                        }
                    });
                    initializeModalSize(commentContainer);
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
    });
}

function contractAddCommentSubmit(container) {
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    formData.append('comment', tinymce.activeEditor.getContent());

    jQuery.ajax({
        url: getBaseURL('customer-portal') + 'contracts/add_comment',
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
                jQuery(".modal", container).modal("hide");
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                window.location = getBaseURL('customer-portal') + 'contracts/view/'+ (jQuery('#contract-id', container).val());
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

function initTiny(id, containerTxt, moduleRecordId, height) {
    height = height || 200;
    tinymce.remove(containerTxt + ' #' + id);
    tinymce.init({
        selector: containerTxt + ' #' + id,
        menubar: false,
        statusbar: false,
        branding: false,
        height: height,
        resize: false,
        relative_urls: false,
        remove_script_host: false,
        plugins: ['link', 'paste', 'image'],
        link_assume_external_targets: true,
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | link | undo redo | undo redo | image code ',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        formats: {
            underline: {inline: 'u', exact: true},
            alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'left'}},
            aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'center'}},
            alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'right'}},
            alignjustify: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: {align: 'justify'}
            },
            p: {block: 'p', styles: {'font-size': '10pt'}},
        }, paste_preprocess: function (plugin, args) {
            tinymce.activeEditor.windowManager.alert('Some of the pasted content will not stay with the same format');
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                jQuery('#' + id + '_ifr', containerTxt).contents().find('body').attr("dir", "auto");
                // jQuery('#comment_ifr').contents().find('body').focus();
                e.pasteAsPlainText = true;
                jQuery('.mce-i-image').parent().on('click', function (e) {
                    setTimeout(function () {
                        jQuery('.mce-browsebutton input[type="file"]').attr('accept', '*');
                    }, 200);
                });
            });
        },
        /* without images_upload_url set, Upload tab won't show up*/
        images_upload_url: getBaseURL('customer-portal') + 'contracts/' + 'upload_file',
        /* we override default upload handler to simulate successful upload*/
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', getBaseURL('customer-portal') + 'contracts/upload_file');
            xhr.onload = function () {
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
                var images = ['tif', 'jpg', 'png', 'gif', 'jpeg', 'bmp', 'jfif'];
                if (images.includes(json.file.extension)) {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, '<img data-name="' + json.file.full_name + '" src="' + getBaseURL('customer-portal') + 'contracts/return_doc_thumbnail/' + json.file.id + '" width="100" height="100"  />');
                } else {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, '<a href="' + getBaseURL('customer-portal') + 'contracts/download_file/' + json.file.id + '" >' + json.file.full_name + '</a>');
                }
                var ed = parent.tinymce.editors[0];
                ed.windowManager.windows[0].close();
            };
            formData = new FormData();
            formData.append('uploadDoc', blobInfo.blob());
            formData.append('module_record_id', moduleRecordId);
            formData.append('module', 'contract');
            formData.append('dragAndDrop', true);
            formData.append('lineage', '');
            xhr.send(formData);
        }
    });
}

// //common functions used to all bootstrap modal dialog
// function commonModalDialogEvents(container, submitFunction) {
//     submitFunction = submitFunction || false;
//     jQuery(".modal", container).modal({
//         keyboard: false,
//         backdrop: "static",
//         show: true
//     });
//     jQuery('#pendingReminders').parent().popover('hide');
//     jQuery('.modal', container).on('shown.bs.modal', function (e) { // IE9 not supported
//         if (jQuery(".first-input", container).val() == '') {
//             jQuery(".first-input", container).focus();
//         }
//     });
//     jQuery(document).keyup(function (e) {
//         if (e.keyCode == 27) {
//             jQuery(".modal", container).modal("hide");
//         }
//     });
//     jQuery('.modal', container).on('hidden.bs.modal', function () {
//         destroyModal(container);
//     });
//     if (submitFunction) {
//         jQuery("#form-submit", container).click(function () {
//             submitFunction(container);
//         });
//         jQuery(container).find('input').keypress(function (e) {
//             // Enter pressed?
//             if (e.which == 13) {
//                 e.preventDefault();
//                 submitFunction(container);
//             }
//         });
//     }
//
// }
/*
 *Destroy Modal
 *Destroy modal on close/cancel/save/escape of the modal
 *To avoid the problem when opening dialogs on the fly(small popups)in jquery dialog then openning modal dialog and the dialog on the fly
 *(the dialog on the fly will have the html before the modal dialog and thus the dialog on the fly will open first not over the main dialog)
 *@param string container( jQuery selector for modal container )
 */
function destroyModal(container) {
    container.remove();
}

//add extra options to the datepicker
function setDatePicker(idOrClass, container) {
    if (_lang.languageSettings['langName'] === 'arabic') {
        datePickerOptions['container'] = idOrClass;
    }
    jQuery(idOrClass, container).bootstrapDP(datePickerOptions);
}

/**
 * detect IE
 * returns version of IE or false, if browser is not Internet Explorer
 */
function detectIE() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        // IE 11 => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
        // Edge (IE 12+) => return version number
        return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    // other browser
    return false;
}
function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);

    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0)
            return null;
    } else {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);

        if (end == -1) {
            end = dc.length;
        }
    }
    return decodeURI(dc.substring(begin + prefix.length, end));
}

// function approveWithSignature(){
//     if(jQuery('#signature-rows-container', '#approval-form-container').length >0){
//         jQuery('#signature-rows-container', '#approval-form-container').removeClass('d-none');
//         jQuery('input[type=radio]', '#approval-form-container').removeAttr('disabled');
//         jQuery('.approve-with-signature-link', '#approval-form-container').addClass('d-none');
//     }else{
//         pinesMessage({ty: 'warning', m: _lang.feedback_messages.signatureNotAvailable});
//     }
// }
