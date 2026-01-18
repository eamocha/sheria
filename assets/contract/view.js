var isPrivate, availableUsers, forceDownloadDoc;

jQuery(document).ready(function () {
    showToolTip('.contract-container', '#watchers');
    showToolTip('.contract-container', '#contributors');
    showToolTip('.contract-container', '#collaborators');
    showToolTip('.contract-container', '#emails');
    showToolTip('.contract-container', '#assigned-teams');
    showToolTip('.contract-container', '#contract-watchers');
    initTiny('comment', '#add-comment');
    if(jQuery('#is-edit-mode', '#header-section').val() == 1){
        contractEditForm(jQuery('#contract-id', '#header-section').val(), event)
    }
});


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

function moveStatus(contractId, statusId, transitionId, e) {
    if ('undefined' !== typeof e && e) {
        e.preventDefault();
    }
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        data: {'contract_id': contractId, 'status_id': statusId, 'transition_id': transitionId ? transitionId : ''},
        url: getBaseURL('contract') + 'contracts/move_status',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && 'undefined' !== typeof response.html) {
                jQuery('#statuses-section', '.contract-container').html(response.html);
            }
            if('undefined' !== response.overall_status){
                jQuery('#approval-status-icon', '.side-menu').attr('src', "assets/images/contract/" + response.overall_status + ".svg");
                jQuery('#approval-status-icon', '.side-menu').tooltipster("destroy");
                jQuery('#approval-status-icon', '.side-menu').attr('title', _lang[response.overall_status]);
                jQuery('#approval-status-icon', '.side-menu').removeClass('hide');
            }
            if ('undefined' !== typeof response.status_name) {
                jQuery('.colored-status', '.contract-container').html(response.status_name);
            }
            if ('undefined' !== typeof response.status_color) {
                jQuery('.colored-status', '.contract-container').css('background-color', response.status_color);
            }
            if (response.result && 'undefined' !== typeof response.screen_html) {
                screenTransitionFormEvents(contractId, transitionId, response.screen_html, 'contracts', false, false, getBaseURL('contract') + 'contracts/save_transition_screen_fields/');
            }
            if ('undefined' !== typeof response.message) {
                pinesMessage({ty: response.result ? 'success' : 'error', m: response.message});
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractEditForm(contractId, e) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    contractId = contractId || false;
    if (!contractId) {
        return false;
    }
    if ('undefined' !== typeof e && e) {
        e.preventDefault();
    }
    jQuery.ajax({
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL('contract') + 'contracts/edit/' + contractId,
        beforeSend: function () {
            jQuery('#loader-global').show();
            //show loader on button 
        },
        success: function (response) {
            var container = jQuery('.contract-container');
            //hide the header
            jQuery('#header-section', container).addClass('d-none');
            if ('undefined' !== typeof response.right_section_html) {
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
                    minViewMode: 'days',
                    orientation: 'bottom'
                };
                jQuery('#right-section', container).html(response.right_section_html);
                setDatePicker('#contract-date', container, datePickerOptions);
                setDatePicker('#start-date', container, datePickerOptions);
                setDatePicker('#end-date', container, datePickerOptions);
                var watcherContainer = jQuery('#fields-to-modify',container);
                lookupDetails = {'lookupField': jQuery('#lookup-watchers', watcherContainer), 'lookupContainer': 'watcher-lookup-container', 'errorDiv': 'lookupWatchers', 'boxName': 'contract_watchers', 'boxId': '#selected-watchers', 'onSelect': 'showCustomerPortal.updateTicketWatchers'};
                showCustomerPortal.lookUpCustomerPortalUsers(lookupDetails, jQuery(watcherContainer), true, false);
            }
            if ('undefined' !== typeof response.details_section_html) {
                jQuery('#details-section .contract-details', container).html(response.details_section_html);
                jQuery('#type', container).change(function () {
                    contractTypeEvent(jQuery(this).val(), jQuery("#sub-type", container));
                });
            }
            //hide comments section
            jQuery('.hide-on-edit' , container).addClass('d-none')
            jQuery('.comments-add', container).addClass('d-none');
            jQuery('.comments-section', container).addClass('d-none');
            jQuery('.select-picker', container).selectpicker();
            objectInitialization('parties', container);
            lookupDetails = {
                'lookupField': jQuery('#requester-lookup', container),
                'hiddenId': '#requester-id',
                'errorDiv': 'requester_id',
                'resultHandler': setRequesterToContract
            };
            lookUpContacts(lookupDetails, jQuery('#right-section', container));
            lookUpContracts({
                'lookupField': jQuery('#lookup-contract', container),
                'hiddenId': jQuery('#lookup-contract-id', container),
                'errorDiv': 'amendment_of'
            }, container);
            lookUpUsers(jQuery('#authorized-signatory-lookup', container), jQuery('#authorized-signatory-id', container), 'authorized_signatory', jQuery('.authorized-signatory-container', container), container);
            jQuery('#assignee-id', container).change(function () {
                if (jQuery('#assignee-id', container).val() == 'quick_add') {
                    jQuery('#assignee-id', container).val('').selectpicker('refresh');
                    addUserToTheProviderGroup(jQuery('#assigned-team-id', container).val(), 'assignee-id', true);
                }
            });
            jQuery('#assigned-team-id', container).change(function () {
                reloadUsersListByProviderGroupSelected(jQuery('#assigned-team-id', container).val(), jQuery("#assignee-id", container), true);
            });
            loadCustomFieldsEvents('custom-field-', container);
            loadSelectizeOptions('contributors', container);

            initPrivateWatchers('watchers', jQuery('#contract-privacy-container'), availableUsers, isPrivate);
            jQuery("#form-submit", container).click(function () {
                contractEditFormSubmit(contractId, container);
            });
            jQuery(container).find('input').keypress(function (e) {
                // Enter pressed?
                if (e.which == 13) {
                    e.preventDefault();
                    contractEditFormSubmit(contractId, container);
                }
            });
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractEditFormSubmit(contractId, container) {
    if (!jQuery('form#contract-edit-form', container).length) {
        jQuery('#main-section', container).wrap('<form action="" class="form-horizontal" novalidate="" id="contract-edit-form" method="post" accept-charset="utf-8" autocomplete="off">');
    }
    var formData = jQuery("form#contract-edit-form", container).serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-submit').addClass('loading');
            jQuery('#form-submit', container).attr('disabled', 'disabled');
            //global loader
        
           
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('contract') + 'contracts/edit/' + contractId,
        //beforeSend: function load global loader
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('#loader-submit').addClass('loading');
            jQuery('#form-submit', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                if ('undefined' !== typeof response.right_section_html) {
                    jQuery('#right-section', container).html(response.right_section_html);
                    showToolTip('.contract-container', '#watchers');
                    showToolTip('.contract-container', '#contributors');
                    showToolTip('.contract-container', '#contract-watchers');
                }
                if ('undefined' !== typeof response.details_section_html) {
                    jQuery('#details-section .contract-details', container).html(response.details_section_html);
                }
                if ('undefined' !== typeof response.header_section_html) {
                    jQuery('#header-section', container).html(response.header_section_html).removeClass('d-none');
                }
                //unhide comments section
                jQuery('.hide-on-edit' , container).removeClass('d-none')
                jQuery('.comments-add', container).removeClass('d-none');
                jQuery('.comments-section', container).removeClass('d-none');
                initTiny('comment', '#add-comment', contractId);
            } else {
                displayValidationErrors(response.validationErrors, container);
            }
        }, complete: function () {
            jQuery('#form-submit').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');
            jQuery('#loader-global').hide();
         
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function cancelEditingForm(contractId) {
    var container = jQuery('.contract-container');
    jQuery.ajax({
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL('contract') + 'contracts/view/' + contractId,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            var container = jQuery('.contract-container');
            jQuery('#header-section', container).removeClass('d-none');
            if ('undefined' !== typeof response.right_section_html) {
                jQuery('#right-section', container).html(response.right_section_html);
            }
            if ('undefined' !== typeof response.details_section_html) {
                jQuery('#details-section .contract-details', container).html(response.details_section_html);
            }
            //unhide comments section
            jQuery('.comments-add', container).removeClass('d-none');
            jQuery('.comments-section', container).removeClass('d-none');
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


// clicking on set as private link
function setAsPrivate(container, itemObj, creator, loggedUser) {
    creator = creator || false;
    loggedUser = loggedUser || false;
    itemObj = itemObj || false;
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/set_privacy',
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                jQuery('#private-link', container).addClass('d-none');
                jQuery('#public-link', container).removeClass('d-none');
                jQuery('.shared-with-label', container).addClass('d-none');
                jQuery('#private', container).val(1);
                jQuery('#watchers', container).removeClass('d-none');
                jQuery('.selectize-control', container).removeClass('d-none');
                jQuery('.alert', container).removeClass('d-none');
                if (creator && loggedUser && (Number(creator) != Number(loggedUser))) { // add modified user to watchers list in edit mode in case the logged user is not the owner / creator - Number using to cast id with leading zero to number in mysql
                    var control = itemObj[0].selectize;
                    control.addItem(loggedUser, true);
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

// clicking on set as public link in case, contact or comapny
function setAsPublic(container) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/set_privacy',
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                var $select = jQuery('#watchers').selectize();
                var control = $select[0].selectize;
                control.clear();
                control.clearOptions();
                jQuery('#private', container).val('');
                jQuery('#private-link', container).removeClass('d-none');
                jQuery('#public-link', container).addClass('d-none');
                jQuery('.shared-with-label', container).removeClass('d-none');
                jQuery('#watchers', container).addClass('d-none');
                jQuery('.selectize-control', container).addClass('d-none');
                jQuery('.alert', container).addClass('d-none');

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function initTiny(id, containerTxt, moduleRecordId, height) {
    moduleRecordId = moduleRecordId || jQuery('#contract-id', containerTxt).val();
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
        toolbar: 'formatselect | bold italic underline | link | undo redo | image code ',
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
                e.pasteAsPlainText = true;
                jQuery('.mce-i-image').parent().on('click', function (e) {
                    setTimeout(function () {
                        jQuery('.mce-browsebutton input[type="file"]').attr('accept', '*');
                    }, 200);
                });
                styleInputsComment(containerTxt);
            });
        },
        /* without images_upload_url set, Upload tab won't show up*/
        images_upload_url: getBaseURL('contract') + 'contracts/' + 'upload_file',
        /* we override default upload handler to simulate successful upload*/
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', getBaseURL('contract') + 'contracts/upload_file');
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
                    tinymce.activeEditor.execCommand('mceInsertContent', false, '<img data-name="' + json.file.full_name + '" src="' + getBaseURL('contract') + 'contracts/return_doc_thumbnail/' + json.file.id + '" width="100" height="100"  />');
                } else {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, '<a href="' + getBaseURL('contract') + 'contracts/download_file/' + json.file.id + '" >' + json.file.full_name + '</a>');
                }
                tinymce.activeEditor.windowManager.close();
            };
            formData = new FormData();
            formData.append('uploadDoc', blobInfo.blob());
            formData.append('module_record_id', moduleRecordId);
            formData.append('module', 'contract');
            formData.append('dragAndDrop', true);
            formData.append('lineage', '');
            xhr.send(formData);
        },
        init_instance_callback: function (editor) {
            editor.on('KeyUp', function (e) {
                styleInputsComment(containerTxt);
            }),
                editor.on('SetContent', function (e) {
                    styleInputsComment(containerTxt);
                });
        }
    });
}

function styleInputsComment(containerTxt) {
    if (containerTxt === '#add-comment') {
        if (tinymce.activeEditor.getContent().length > 0) {
            if (jQuery("#comment-id").val()) {
                jQuery("#contract-comment-form #comment").val(tinymce.activeEditor.getContent());
            }
            jQuery("#save-comment").removeAttr("disabled");
            jQuery("#save-comment").css({"background-color": "#3b7fc4", 'color': 'white'});
        } else {
            jQuery("#save-comment").attr("disabled", "disabled");
            jQuery("#save-comment").css({"background-color": "#f4f5f7", 'color': 'gray'});
        }
    }
}

function commentToggle(id, container) {
    var id = parseInt(id);
    var comment = jQuery('#comment-' + id, container);
    if (jQuery('.comment-body', comment).is(':visible')) {
        jQuery('.comment-body', comment).slideUp();
        jQuery('i', '#comment-' + id + ' a:first').removeClass('fa-solid fa-angles-down');
        jQuery('i', '#comment-' + id + ' a:first').addClass('fa-solid fa-angles-right');
    } else {
        jQuery('.comment-body', comment).slideDown();
        jQuery('i', '#comment-' + id + ' a:first').removeClass('fa-solid fa-angles-right');
        jQuery('i', '#comment-' + id + ' a:first').addClass('fa-solid fa-angles-down');
    }
}

function commentDelete(params) {
    jQuery.ajax({
        url: getBaseURL('contract') + params.url + params.comment_id,
        method: 'get',
        dataType: 'json',
        success: function (response) {
            var ty = 'error';
            var m = '';
            if (response.status) {
                ty = 'information';
                m = _lang.deleteRecordSuccessfull;
                jQuery('.comments-lists-container', '.contract-notes-container').each(function () {
                    jQuery(this).empty();
                });

                fetchContractCommentsTab(params.contract_id, jQuery('.contract-notes-container.active'), null, jQuery('.contract-notes-container.active .comments-lists-container').data('index'));
            } else {
                m = _lang.deleteRecordFailed;
            }
            jQuery('.modal', '.confirmation-dialog-container').modal('hide');

            pinesMessageV2({ty: ty, m: m});
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractAddCommentSubmit() {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var container = jQuery('#add-comment', '#details-section');
    var formData = new FormData(document.getElementById(jQuery("form#comment-form", '#details-section').attr('id')));
    formData.append('comment', tinymce.activeEditor.getContent());

    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/add_comment',
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
                jQuery('.comments-lists-container', '.contract-notes-container').each(function () {
                    jQuery(this).empty();
                });
                fetchContractCommentsTab(response.data.contract_id, jQuery('.contract-notes-container.active'), null, jQuery('.contract-notes-container.active .comments-lists-container').data('index'));
                tinymce.activeEditor.setContent('');
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
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

function contractCommentForm(contractId, id) {
    contractId = contractId || false;
    id = id || false;
    if (!contractId) {
        pinesMessage({ty: 'error', m: _lang.invalid_request});
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/edit_comment',
        type: 'GET',
        data: {
            contract_id: contractId,
            id: id
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
                    initTiny('edit-comment', "#comment-edit-container", contractId);
                    commonModalDialogEvents(commentContainer, contractEditCommentSubmit);
                    initializeModalSize(commentContainer);
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
    });
}

function contractEditCommentSubmit() {
    var container = jQuery('#comment-edit-container');
    var formData = new FormData(document.getElementById(jQuery("form#comment-edit-form", container).attr('id')));
    formData.append('comment', tinymce.activeEditor.getContent());
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/edit_comment',
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

                jQuery('.comments-lists-container', '.contract-notes-container').each(function () {
                    jQuery(this).empty();
                });

                fetchContractCommentsTab(response.data.contract_id, jQuery('.contract-notes-container.active'), null, jQuery('.contract-notes-container.active .comments-lists-container').data('index'));

                if (undefined !== response.upload_result && !response.upload_result) {
                    pinesMessage({ty: 'error', m: response.upload_msg});
                }
                jQuery(".modal", container).modal("hide");

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

function contractApproverForm(contractId, id) {
    id = id || false;
    if (id) {
        func = 'edit_approver_per_contract';
    } else {
        func = 'add_approver_per_contract';
    }
    contractMatrixRowForm(contractId, id, func);
}

function contractSigneeForm(contractId, id) {
    id = id || false;
    if (id) {
        func = 'edit_signature_signee_per_contract';
    } else {
        func = 'add_signature_signee_per_contract';
    }
    contractMatrixRowForm(contractId, id, func);
}

function contractMatrixRowForm(contractId, id, func) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    id = id || false;
    data = [{name: "contract_id", value: contractId}];
    if (id) {
        data.push({name: "id", value: id});
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'contracts/' + func,
        type: 'GET',
        data: data,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#related-matrix-row-container').length <= 0) {
                    jQuery('<div id="related-matrix-row-container"></div>').appendTo("body");
                    var relatedMatrixRow = jQuery('#related-matrix-row-container');
                    relatedMatrixRow.html(response.html);
                    jQuery('.select-picker', relatedMatrixRow).selectpicker();
                    jQuery('.tooltip-title', relatedMatrixRow).tooltipster();
                    commonModalDialogEvents(relatedMatrixRow);
                    initializeModalSize(relatedMatrixRow, 0.5, 0.6);
                    initializeSelectPermissions(relatedMatrixRow);
                    lookupApproversSignees(jQuery('#lookup-approvers-signees', relatedMatrixRow), 'Approvers_Signees[]', 'Approvers_Signees_types[]', '#selected-approvers', 'approvers-signees-container', relatedMatrixRow, '', response.type, false);
                    jQuery("#form-submit", relatedMatrixRow).click(function () {
                        contractMatrixRowFormSubmit(relatedMatrixRow, func);
                    });
                    jQuery(relatedMatrixRow).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            contractMatrixRowFormSubmit(relatedMatrixRow, func);
                        }
                    });
                }
            }
            jQuery('#loader-global').hide();
        }, complete: function () {
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractMatrixRowFormSubmit(container, func) {
    var formData = jQuery("form#matrix-users-row-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL('contract') + 'contracts/' + func,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.modal', container).modal('hide');
                if (func === 'add_approver_per_contract' || func === 'edit_approver_per_contract') {
                    approvalCenterTab(jQuery('#contract-id', container).val());
                    jQuery('#approval-status-icon', '.side-menu').attr('src', "assets/images/contract/" + response.overall_status + ".svg");
                    jQuery('#approval-status-icon', '.side-menu').tooltipster("destroy");
                    jQuery('#approval-status-icon', '.side-menu').attr('title', _lang[response.overall_status]);
                    jQuery('#approval-status-icon', '.side-menu').removeClass('d-none');
                } else {
                    signatureCenterTab(jQuery('#contract-id', container).val());
                    jQuery('#signature-status-icon', '.side-menu').attr('src', "assets/images/contract/" + response.overall_status + ".svg");
                    jQuery('#signature-status-icon', '.side-menu').tooltipster("destroy");
                    jQuery('#signature-status-icon', '.side-menu').attr('title', _lang[response.overall_status]);
                    jQuery('#signature-status-icon', '.side-menu').removeClass('d-none');
                }
                showToolTip('.side-menu', '.icon-tooltip');
            } else {
                displayValidationErrors(response.validation_errors, container);
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function loadBoardMembersPerParty(that) {
    if (jQuery(that).val() !== '') {
        jQuery.ajax({
            url: getBaseURL('contract') + 'contracts/load_board_members_per_party',
            type: 'GET',
            data: {'party_id': jQuery(that).val(), 'role_id': jQuery('#bm-role-id', '#approval-form-container').val()},
            dataType: "json",
            error: defaultAjaxJSONErrorsHandler,
            success: function (response) {
                if (response.result) {
                    var newOptions = '<option value="">' + _lang.none + '</option>';
                    if (typeof response.users != "undefined" && response.users != null) {
                        for (i in response.users) {
                            newOptions += '<option value="' + i + '">' + response.users[i] + '</option>';
                        }
                    }
                    jQuery("#bm-collaborators", '#approval-form-container').html(newOptions).selectpicker("refresh");
                }
            }
        });
    } else {
        jQuery("#bm-collaborators", '#approval-form-container').html('').selectpicker("refresh");
    }

}


function loadShareholdersPerParty(that) {
    if (jQuery(that).val() !== '') {
        jQuery.ajax({
            url: getBaseURL('contract') + 'contracts/load_shareholders_per_party',
            type: 'GET',
            data: {'party_id': jQuery(that).val()},
            dataType: "json",
            error: defaultAjaxJSONErrorsHandler,
            success: function (response) {
                if (response.result) {
                    var newOptions = '<option value="">' + _lang.none + '</option>';
                    if (typeof response.users != "undefined" && response.users != null) {
                        for (i in response.users) {
                            newOptions += '<option value="' + i + '">' + response.users[i] + '</option>';
                        }
                    }
                    jQuery("#sh-collaborators", '#approval-form-container').html(newOptions).selectpicker("refresh");
                }
            }
        });
    } else {
        jQuery("#sh-collaborators", '#approval-form-container').html('').selectpicker("refresh");
    }

}

function contractApproverDelete(params) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/delete_approver_per_contract',
        dataType: 'json',
        data: {id: params.id},
        type: 'POST',
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: response.display_message});
                approvalCenterTab(params.contract_id);

            } else {
                pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractSigneeDelete(params) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/delete_signee_per_contract',
        dataType: 'json',
        data: {id: params.id},
        type: 'POST',
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: response.display_message});
                signatureCenterTab(params.contract_id);

            } else {
                pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractApproveAll(params) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/approve_all_contract',
        dataType: 'json',
        data: {contract_id: params.contract_id},
        type: 'POST',
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                approvalCenterTab(params.contract_id);
                jQuery('#approval-status-icon', '.side-menu').attr('src', "assets/images/contract/approved.svg");
                jQuery('#signature-status-icon', '.side-menu').attr('src', "assets/images/contract/awaiting_signature.svg");
                jQuery('#approval-status-icon', '.side-menu').removeClass('d-none');
                jQuery('#approval-status-icon', '.side-menu').tooltipster("destroy");
                jQuery('#approval-status-icon', '.side-menu').attr('title', _lang.approved);
                jQuery('#signature-status-icon', '.side-menu').tooltipster("destroy");
                jQuery('#signature-status-icon', '.side-menu').attr('title', _lang.awaiting_signature);
                showToolTip('.side-menu', '.icon-tooltip');
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.actionFailed});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function submitSummary(container) {
    var formData = jQuery("form#summary-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL('contract') + 'contracts/load_summary',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.modal', container).modal('hide');
            } else {
                displayValidationErrors(response.validation_errors, container);
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function requestToSignWithDocuSign(contractId) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {'contract_id': contractId},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL('contract') + 'docusign_integration/request_to_sign/',
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".contract-docs-container").length <= 0) {
                    jQuery('<div class="d-none contract-docs-container"></div>').appendTo("body");
                    var contractDocsContainer = jQuery('.contract-docs-container');
                    contractDocsContainer.html(response.html).removeClass('d-none');
                    var ContactlookupDetails = {
                        'lookupField': jQuery('#lookup-contacts', '#request-signature-container'),
                        'lookupContainer': 'contact-lookup-container',
                        'errorDiv': 'lookupContacts',
                        'boxId': '#selected-signees',
                        'boxName': 'signeeContacts',
                        'resultHandler': setSelectedContactToSignee
                    };
                    lookUpContacts(ContactlookupDetails, jQuery('#request-signature-container'), true, false, true);
                    commonModalDialogEvents(contractDocsContainer, requestToSignWithDocuSignSubmit);
                    initializeModalSize(contractDocsContainer, 0.5, 0.6);
                    loadSelectizeContactOptions('contacts', contractDocsContainer, 'contacts/autocomplete', 'has_email');
                }
            } else {
                pinesMessage({ty: 'warning', m: response.display_message});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function requestToSignWithDocuSignSubmit(container) {
    var formData = jQuery("form#request-signature-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL('contract') + 'docusign_integration/request_to_sign',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.modal', container).modal('hide');
                signatureCenterTab(jQuery('#contract-id', container).val());
            } else {
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                }
                if ('undefined' !== typeof response.needs_authenticate && response.needs_authenticate) {
                    window.location.href = getBaseURL('contract') + 'docusign_integration/authenticate';
                    return;
                }
                if ('undefined' !== typeof response.display_message) {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

//show related tasks data
function relatedTasksTab(contractId) {
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#related-tasks-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_tasks/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, 'contract');
                taskGridEvents();

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


//show related legal Opinions data
function relatedLegalOpinionsTab(contractId) {
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#related-legal-opinions-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_legalOpinions/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, 'contract');
                legalOpinionGridEvents();

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function activateDeactivate(that, id) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }

    console.log(jQuery(that).find('option:selected').val());

    status = jQuery(that).find('option:selected').val()
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/activate_deactivate',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': id,
            'status': status,
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'information', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('#contract-status-label', '#header-section').html(response.status);
            } else {
                pinesMessage({ty: 'error', m: _lang.updatesFailed});
                return false;
            }

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractRenewForm(id) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/renew',
        type: 'GET',
        dataType: 'JSON',
        data: {
            'contract_id': id
        },
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery("#contract-renew-container").length <= 0) {
                    jQuery('<div id="contract-renew-container" class="primary-style"></div>').appendTo("body");
                    var contractRenewContainer = jQuery('#contract-renew-container');
                    contractRenewContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(contractRenewContainer, contractRenewFormSubmit);
                    jQuery(".modal", contractRenewContainer).modal('handleUpdate');
                    // initializeModalSize(contractRenewContainer, 0.6, 0.6);
                    renewalLookupsEvents(contractRenewContainer);
                    jQuery('.select-picker', contractRenewContainer).selectpicker({dropupAuto: false});
                    setDatePicker('#new-contract-date', contractRenewContainer);
                    setDatePicker('#new-start-date', contractRenewContainer);
                    setDatePicker('#new-end-date', contractRenewContainer);
                }
            } else {
                pinesMessage({ty: 'warning', m: response.display_message});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractRenewFormSubmit(container) {
    var formData = jQuery("form#renewal-form", container).serializeArray();
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/renew/',
        dataType: 'JSON',
        type: 'POST',
        data: formData,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                var msg = _lang.feedback_messages.addedNewContractSuccessfully.sprintf(['<a href="' + getBaseURL('contract') + 'contracts/view/' + response.new_contract_id + '">' + response.model_code + response.new_contract_id + '</a>']);
                pinesMessage({ty: 'success', m: msg});
                jQuery('.modal', container).modal('hide');
                if ('undefined' !== typeof response.renewal_history_section_html) {
                    jQuery('.renewal-history-section', '#contracts-details').html(response.renewal_history_section_html);
                }
                if (jQuery('#contracts-details').length && response.archived) {
                    //in single contract form
                    jQuery("#archive-unarchive-btn", ".contract-container").attr('name', response.archived == "yes" ? "yes" : "no");
                    jQuery('#archive-unarchive-btn', ".contract-container").text(response.archived == "yes" ? _lang.unarchive : _lang.archive);
                    jQuery("#archive-unarchive-btn", ".contract-container").attr('title', response.archived == "yes" ? _lang.unarchive : _lang.archive);
                    jQuery(".archived-flag", ".contract-container").html(response.archived == "yes" ? "(" + _lang.archived + ")" : '');
                }
                if (jQuery('#contracts-details').length && response.deactivated == 'yes') {
                    //in single contract form
                    jQuery('#contract-status', '#header-section').prop('checked', false);
                    jQuery('#contract-status-label', '#header-section').html(_lang.custom.Inactive);
                }
            } else {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


//show related reminders data
function relatedRemindersTab(contractId) {
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#related-reminders-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_reminders/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, 'contract');
                reminderGridEvents();

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function relatedCasesTab(contractId) {

    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#related-cases-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_cases/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, 'contract');
                caseGridEvents();

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function relatedContractsTab(contractId) {
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#related-contracts-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_contracts/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, 'contract');
                contractGridEvents();

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function contractAmendmentForm(id) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/amend',
        type: 'GET',
        dataType: 'JSON',
        data: {
            'contract_id': id
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery("#contract-amend-container").length <= 0) {
                    jQuery('<div id="contract-amend-container" class="primary-style"></div>').appendTo("body");
                    var contractAmendContainer = jQuery('#contract-amend-container');
                    contractAmendContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(contractAmendContainer, contractAmendmentFormSubmit);
                    contractFormEvents(contractAmendContainer);
                    checkBoxContainersValues({'contributors-container': jQuery('#selected-contributors', contractAmendContainer)}, contractAmendContainer);
                    initializeModalSize(contractAmendContainer);
                }
            } else {
                pinesMessage({ty: 'warning', m: response.display_message});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function contractAmendmentFormSubmit(container) {
    var formData = jQuery("form#contract-amend-form", container).serializeArray();
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/amend/',
        dataType: 'JSON',
        type: 'POST',
        data: formData,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                var msg = _lang.feedback_messages.addedNewContractSuccessfully.sprintf(['<a href="' + getBaseURL('contract') + 'contracts/view/' + response.new_contract_id + '">' + response.model_code + response.new_contract_id + '</a>']);
                pinesMessage({ty: 'success', m: msg});
                jQuery('.modal', container).modal('hide');
                if ('undefined' !== typeof response.amendment_history_section_html) {
                    jQuery('.amendment-history-section', '#contracts-details').html(response.amendment_history_section_html);
                }
                if (jQuery('#contracts-details').length) {
                    //in single contract form
                    jQuery("#archive-unarchive-btn", ".contract-container").attr('name', response.archived == "yes" ? "yes" : "no");
                    jQuery('#archive-unarchive-btn', ".contract-container").text(response.archived == "yes" ? _lang.unarchive : _lang.archive);
                    jQuery("#archive-unarchive-btn", ".contract-container").attr('title', response.archived == "yes" ? _lang.unarchive : _lang.archive);
                    jQuery(".archived-flag", ".contract-container").html(response.archived == "yes" ? "(" + _lang.archived + ")" : '');
                }
                if (jQuery('#contracts-details').length && response.deactivated == 'yes') {
                    //in single contract form
                    jQuery('#contract-status', '#header-section').prop('checked', false);
                    jQuery('#contract-status-label', '#header-section').html(_lang.custom.Inactive);
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


function deactivateContract(params) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/deactivate',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': params.id
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'information', m: _lang.updatesSavedSuccessfully});

            } else {
                pinesMessage({ty: 'error', m: _lang.updatesFailed});
                return false;
            }

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function fetchContractCommentsTab(contractId, tab, url, tabIndex) {
    var commentsContainer = tab.find('.comments-lists-container');

    if (url == null) {
        switch (tabIndex) {
            case 2:
                url = 'contracts/get_all_core_and_cp_comments';
                break;
            case 3:
                url = 'contracts/get_all_email_comments';
                break;
            default:
                url = 'contracts/get_all_comments';
        }
    }

    if (commentsContainer.html() == '') {
        jQuery.ajax({
            url: getBaseURL('contract') + url,
            type: 'post',
            data: {
                'id': contractId
            },
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    togglContractCommentsTab(tab, tabIndex);
                    commentsContainer.html(response.html).attr('data-index', tabIndex);
                    generate_pagination_links();
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        togglContractCommentsTab(tab, tabIndex);
    }
}

function togglContractCommentsTab(tab, index) {
    jQuery('.contract-notes-container').each(function () {
        jQuery(this).addClass('hidden').removeClass('active');
    });

    tab.removeClass('hidden').addClass('active');

    jQuery('#contract-notes-tabs > ul > li:not([data-index="' + index + '"])').each(function () {
        jQuery(this).removeClass('active');
    });

    jQuery('#contract-notes-tabs > ul > li[data-index="' + index + '"]').addClass('active');
}
function toggleHistoryTab(tab, index) {
    jQuery('.history-report-table').each(function () {
        jQuery(this).addClass('d-none').removeClass('active');
    });
    tab.removeClass('d-none').addClass('active');

    jQuery('#history-section-content > ul > li:not([data-index="' + index + '"])').each(function () {
        jQuery(this).removeClass('active');
    });

    jQuery('#history-section-content > ul > li[data-index="' + index + '"]').addClass('active');
}

function generate_pagination_links() {
    jQuery('a', '.contract-notes-container:not(.hidden) .comments-lists-container .contractCommentsPagination').each(function () {
        aSearchHref = jQuery(this).attr('href');
        jQuery(this).attr('onclick', "get_contract_comments('" + aSearchHref + "'," + "jQuery('.contract-notes-container:not(.hidden) .comments-lists-container .contractCommentsPagination').parent()" + ');');
        jQuery(this).attr('href', 'javascript:;');
    });
}

function toggle_comment(id) {
    contract_comment = jQuery('#contract-comment_' + id, '.contract-notes-container.active');
    if (jQuery('#commentText', contract_comment).is(':visible')) {
        jQuery('#commentText', contract_comment).slideUp();
        jQuery('#contract-comment_' + id + ' a:first').find('[data-fa-i2svg]').toggleClass('fa-angles-down').toggleClass('fa-angles-right');
    } else {
        jQuery('#commentText', contract_comment).slideDown();
        jQuery('#contract-comment_' + id + ' a:first').find('[data-fa-i2svg]').toggleClass('fa-angles-right').toggleClass('fa-angles-down');
    }
}

function expandAllNotes() {
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', jQuery('.contract-notes-container.active'))).slideDown();
    jQuery('a > i.fa-angles-right', jQuery('.contract-notes-container.active')).removeClass('fa-angles-right').addClass('fa-angles-down');
}

function collapseAllNotes() {
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', jQuery('.contract-notes-container.active'))).slideUp();
    jQuery('a > i.fa-angles-down', jQuery('.contract-notes-container.active')).removeClass('fa-angles-down').addClass('fa-angles-right');
}

function expandAllEmailNotes() {
    //hide up all notes
    jQuery('.contract-comments-emails-table tr.comment-container').each(function () {
        jQuery(this).find('td').slideDown(300).css({'display': 'table-cell'});
    });
    jQuery('i.fa-angles-right', jQuery('.contract-notes-container.active')).removeClass('fa-angles-right').addClass('fa-angles-down');
}

function collapseAllEmailNotes() {
    //hide up all notes
    jQuery('.contract-comments-emails-table tr.comment-container').each(function () {
        jQuery(this).find('td').slideUp(100);
    });
    jQuery('i.fa-angles-down', jQuery('.contract-notes-container.active')).removeClass('fa-angles-down').addClass('fa-angles-right');
}

function boardMemberEvent(hiddenInput, checkBoxInput) {
    isChecked = checkBoxInput.is(':checked');
    hiddenInput.val(isChecked ? '1' : '0');
    if (isChecked) {
        jQuery('#bm-details').removeClass('d-none');
    } else {
        jQuery('#bm-details').addClass('d-none');
        jQuery('.select-picker', '#bm-details').val('').selectpicker("refresh");
    }
}

function shareholderEvent(hiddenInput, checkBoxInput) {
    isChecked = checkBoxInput.is(':checked');
    hiddenInput.val(isChecked ? '1' : '0');
    if (isChecked) {
        jQuery('#sh-details').removeClass('d-none');
    } else {
        jQuery('#sh-details').addClass('d-none');
        jQuery('.select-picker', '#sh-details').val('').selectpicker("refresh");
    }
}
///milestones

function milestone(contractId) {
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#milestone-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/milestone/' + contractId,
        dataType: 'JSON',
        type: 'GET',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, 'contract');
                jQuery('.select-picker', '#milestones-events-details-container').selectpicker();
                jQuery('.progress-status-container', '#milestones-events-details-container').each(function () {
                    currentStatus = jQuery(this).attr("current-status");
                    containerId = jQuery(this).attr("id");
                    progressStatusSelect = jQuery('.dropdown-toggle', '#' + containerId).addClass(currentStatus + '-progress-status');
                });
                langFormat ={'en': 'en', 'ar': 'ar', 'fr': 'fr' , 'sp':'es', 'tu': 'tr', 'ru': 'ru' , 'de':'de'};
                var align = (response.data.rtl)? 'right': 'left';
                ej.base.registerLicense('Mgo+DSMBaFt/QHFqVVhkW1pFdEBBXHxAd1p/VWJYdVt5flBPcDwsT3RfQF9jT3xQdkxjXX5dcHJTRg==;Mgo+DSMBPh8sVXJ0S0d+XE9AcVRDX3xKf0x/TGpQb19xflBPallYVBYiSV9jS3xTcEdgWXZedHVWR2ldVw==;ORg4AjUWIQA/Gnt2VVhhQlFaclhJXGFWfVJpTGpQdk5xdV9DaVZUTWY/P1ZhSXxRdkNhWn9XcHBURWVZWEM=;NjYxMDI4QDMyMzAyZTMxMmUzMGNZOHdtMmRKZnBUTW5HcHFsalJWdGpzeFpVcW1WU2srRm0rSFYrQ0NNazg9;NjYxMDI5QDMyMzAyZTMxMmUzMEQwbi9QdWw5MUtqYWZoQ29neGtObmtaWHRIVE15djkxNzN2NFdzWWVFemM9;NRAiBiAaIQQuGjN/V0Z+XU9EaFtFVmJLYVB3WmpQdldgdVRMZVVbQX9PIiBoS35RdEVlW3xeeXRRR2RZVUF2;NjYxMDMxQDMyMzAyZTMxMmUzMFFkL0pERkpVdUZSOTVOb0dzQm5JbXNlclNoMFIyS1hhdFB2N0lLWGo3aXM9;NjYxMDMyQDMyMzAyZTMxMmUzMG1mcmVYQ0lESkRnSTZrblIxTGx6YzZoL3RDNDE1clBmV0V1RlFvTkd0VWs9;Mgo+DSMBMAY9C3t2VVhhQlFaclhJXGFWfVJpTGpQdk5xdV9DaVZUTWY/P1ZhSXxRdkNhWn9XcHBUQ2BVV0A=;NjYxMDM0QDMyMzAyZTMxMmUzMERvN2d3M1ViZGhjdUNJYkR3UTJzS2RmMS85cXMzNEdIL3BhczhySjRpYTg9;NjYxMDM1QDMyMzAyZTMxMmUzMGcvZk5ONEhNWnpKblBQZTloMWFTcmpRM3ZFWTBRMGU2YWVkdUlXR0orTk09');
                ej.base.enableRtl(response.data.rtl);
                ej.base.setCulture(langFormat[response.data.language]);
                ej.base.L10n.load({
                    'en': {
                        'gantt': {
                            "id": "ID",
                            "name": "Title",
                            "startDate": "Start Date",
                            "endDate": "Due Date",
                            "duration": "Duration",
                            "progress": "Progress",
                        }
                    },
                    'ar': {
                        'gantt': {
                            "id": "الرقم",
                            "name": "العنوان",
                            "startDate": "تاريخ البدء",
                            "endDate": "تاريخ الإستحقاق",
                            "duration": "المدة",
                            "progress": "التقدم",
                        }
                    },
                    'fr': {
                        'gantt': {
                            "id": "ID",
                            "name": "Titre",
                            "startDate": "Date de Début",
                            "endDate": "Date d'Echéance",
                            "duration": "Durée",
                            "progress": "Progrès",
                        }
                    },
                    'es': {
                        'gantt': {
                            "id": "ID",
                            "name": "Título",
                            "startDate": "Fecha de Inicio",
                            "endDate": "Fecha de Vencimiento",
                            "duration": "Duración",
                            "progress": "Progreso",
                        }
                    },
                    'tr': {
                        'gantt': {
                            "id": "KIMLIK",
                            "name": "Başlık",
                            "startDate": "Başlangıç Tarihi",
                            "endDate": "Son Tarih",
                            "duration": "Süre",
                            "progress": "İlerleme",
                        }
                    },
                    'ru': {
                        'gantt': {
                            "id": "Я БЫ",
                            "name": "Название",
                            "startDate": "Дата начала",
                            "endDate": "Дата выполнения",
                            "duration": "Продолжительность",
                            "progress": "Прогресс",
                        }
                    },
                    'de': {
                        'gantt': {
                            "id": "Ich würde",
                            "name": "Titel",
                            "startDate": "Anfangsdatum",
                            "endDate": "Geburtstermin",
                            "duration": "Dauer",
                            "progress": "Fortschritt",
                        }
                    },

                });
                var ganttChart = new ej.gantt.Gantt({
                    height: '100%',
                    rowHeight:25,
                    dateFormat :  'yyyy-MM-dd',
                    includeWeekend: true,
                    dayWorkingTime: [{ from: 0, to: 23 }],
                    enablePredecessorValidation: false,
                    locale: langFormat[response.data.language],
                    taskFields: {
                      id: 'id',
                      name: 'title',
                      startDate: 'StartDate',
                      endDate: 'EndDate',
                    },
                    columns: [
                        { field: 'title', headerText: _lang.columnTitle, textAlign: align},
                        { field: 'StartDate', headerText: _lang.startDate, allowSorting: true, textAlign: align},
                        { field: 'EndDate', headerText: _lang.dueDate,  allowSorting: true, textAlign:align}
                    ],
                    splitterSettings:{
                        position: "20%"
                      },
                  });
                ganttChart.dataSource = [];
                languageFormat ={'en': 'en-US', 'ar': 'ar-EG', 'fr': 'fr' , 'sp':'es', 'tu': 'tr', 'ru': 'ru' , 'de':'de'   };
                const options = {  year: 'numeric', month: 'long' };
                ganttChart.timelineSettings.topTier.format = 'MMM';
                ganttChart.timelineSettings.topTier.unit = 'Month';
                ganttChart.timelineSettings.topTier.count = 1;
                ganttChart.timelineSettings.topTier.formatter = (date) => {
                    return new Intl.DateTimeFormat(languageFormat[response.data.language], options).format(date);
                };
                const optionsDay = {  day: 'numeric'};
                ganttChart.timelineSettings.bottomTier.unit = 'Day';
                ganttChart.timelineSettings.bottomTier.count = 1;
                ganttChart.timelineSettings.bottomTier.formatter = (date) => {
                    return new Intl.DateTimeFormat(languageFormat[response.data.language], optionsDay).format(date);
                };
                response.data.milestones.forEach(function callback(milestone, index){
                    milestoneObject = {'id' : milestone.id, 'title' : milestone.title};
                    if (milestone.start_date != null ){
                        milestoneObject.StartDate = new Date(milestone.start_date);
                    }
                    if (milestone.due_date != null ){
                        milestoneObject.EndDate = new Date(milestone.due_date);
                    }
                    ganttChart.dataSource.push(milestoneObject);
                });
                  if  (response.data.milestones.length >0){
                    ganttChart.appendTo('#milestones-gantt-container');
                  }
            }
        }, complete: function (response) {
            if(response.responseJSON.data.rtl){
                jQuery(".e-gantt-tree-grid-pane", "#milestone-container").css("order", "2");
                jQuery(".e-gantt-chart-pane", "#milestone-container").css("order", "0");
            }
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

///
function contractMilestoneFormSubmit(container, func) {
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        contentType: false,
        cache: false,
        processData: false,
        url: getBaseURL('contract') + 'contracts/' + func,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.modal', container).modal('hide');
                milestone(formData.get('contract_id'));
            } else {
                displayValidationErrors(response.validationErrors, container);
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function documentsDialog(milestoneId) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/milestone_documents/' + milestoneId,
        dataType: 'JSON',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var modelContainer = "document-dialog-container";
                jQuery('<div id="document-dialog-container"></div>').appendTo("body");
                var container = jQuery("#" + modelContainer);
                container.html(response.html);
                initializeModalSize(container, 0.35, 'auto');
                commonModalDialogEvents(container);
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteMilestoneDocument(param) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/delete_document_milestone',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'document_id': param.documentId,
            'newest_version': true,
            'contract_id': param.contractId
        },
        success: function (response) {
            pinesMessage({
                ty: response.status ? 'information' : 'error',
                m: response ? _lang.deleteRecordSuccessfull : _lang.recordNotDeleted
            });
            if (response.status) {
                jQuery('#document-item-' + param.documentId, '#document-dialog-container').remove();
                if (param.milestoneId) updateMilestoneCount(param.milestoneId);
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function updateMilestoneCount(milestoneId) {
    var documentItemCount = jQuery('#document-item-count-' + milestoneId, '#milestone-container-' + milestoneId);
    if (documentItemCount) {
        var docCount = documentItemCount.data('count');
        if (docCount > 1) {
            documentItemCount.html('<a href="javascript:;">' + (docCount - 1) + " " + _lang.documents + '</a>');
            documentItemCount.data('count', docCount - 1);
        } else {
            documentItemCount.attr('onclick', '');
            documentItemCount.html(0 + " " + _lang.documents);
            jQuery('.modal', jQuery("#document-dialog-container")).modal('hide');
        }
    }
}

function deleteMilestone(parms) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/delete_milestone',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'milestone_id': parms.milestoneId,
            'contract_id': parms.contractId
        },
        success: function (response) {
            pinesMessage({
                ty: response.status ? 'information' : 'error',
                m: response ? _lang.deleteRecordSuccessfull : _lang.recordNotDeleted
            });
            if (response.status) {
                milestone(parms.contractId);
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function changeMilestoneStatus(status, milestoneId) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    status = status.value;
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/change_milestone_status',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'milestone_id': milestoneId, 'status': status
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            pinesMessage({
                ty: response ? 'information' : 'error',
                m: response ? _lang.feedback_messages.milestoneStatusUpdateSuccessfull : _lang.feedback_messages.milestoneStatusUpdateFail
            });
            if (response) {
                currentStatus = jQuery('#status-container-' + milestoneId).attr("current-status");
                jQuery('#card-border-' + milestoneId).removeClass(currentStatus + '-card-border');
                jQuery('#card-border-' + milestoneId).addClass(status + '-card-border');
                progressStatusSelect = jQuery('.dropdown-toggle', '#status-container-' + milestoneId);
                jQuery('#status-container-' + milestoneId).attr("current-status", status);
                progressStatusSelect.removeClass(currentStatus + '-progress-status');
                progressStatusSelect.addClass(status + '-progress-status');
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}

function changeFinancialStatus(selectChanged, milestoneId) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    financialStatus = selectChanged.value;
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/change_milestone_financial_status',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'milestone_id': milestoneId, 'financial_status': financialStatus
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            pinesMessage({
                ty: response ? 'information' : 'error',
                m: response ? _lang.feedback_messages.milestoneStatusUpdateSuccessfull : _lang.feedback_messages.milestoneStatusUpdateFail
            });
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function showHideMilestoneCp(id , flag){
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/show_hide_milestone_cp',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': id ,'flag': flag
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function loadCultureFiles(name) {
    var files = ['ca-gregorian.json', 'numbers.json', 'timeZoneNames.json'];
    if (name === 'ar') {
        files.push('numberingSystems.json');
    }
    if (name === 'hijri') {
        var files = ['ca-islamic.json', 'ca-islamic-civil.json', 'ca-islamic-rgsa.json', 'ca-islamic-tbla.json', 'ca-islamic-umalqura.json', 'numberingSystems.json', 'numbers.json', 'timeZoneNames.json'];
    }
    var loader = ej.base.loadCldr;
    var loadCulture = function (prop) {
        var val, ajax;
        ajax = new ej.base.Ajax('assets/syncfusion/ej2-locale/cldr-dates-full/main/' + name + '/' + files[prop], 'GET', false);
        ajax.onSuccess = function (value) {
            val = value;
        };
        ajax.send();
        loader(JSON.parse(val));
    };
    for (var prop = 0; prop < files.length; prop++) {
        loadCulture(prop);
    }
}



function contractMilestoneForm(contractId, milestoneId) {
    milestoneId = milestoneId || false;
    func = (milestoneId) ? 'edit_milestone' : 'add_milestone';
    milestoneForm(contractId, milestoneId, func);
}

function milestoneForm(contractId, milestoneId, func) {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    milestoneId = milestoneId || false;
    data = [{name: "contract_id", value: contractId}];
    if (milestoneId) {
        data.push({name: "milestone_id", value: milestoneId});
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'contracts/' + func,
        type: 'GET',
        data: data,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#milestone-form-main-container').length <= 0) {
                    jQuery('<div id="milestone-form-main-container"></div>').appendTo("body");
                    var form = jQuery('#milestone-form-main-container');
                    form.html(response.html);
                    jQuery('.select-picker', form).selectpicker();
                    setDatePicker('#start-date', form, datePickerOptions);
                    setDatePicker('#due-date', form, datePickerOptions);
                    commonModalDialogEvents(form);
                    initializeModalSize(form, 0.5, 0.6);
                    jQuery("#form-submit", form).click(function () {
                        contractMilestoneFormSubmit(form, func);
                    });
                    jQuery(form).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            contractMilestoneFormSubmit(form, func);
                        }
                    });
                    jQuery('#target', form).change(function () {
                        if (this.checked) {
                            jQuery(this).prop("checked");
                            jQuery('#amount_container', form).addClass("d-none");
                            jQuery('#percentage_container', form).removeClass("d-none");
                        } else {
                            jQuery(this).prop("unchecked");
                            jQuery('#amount_container', form).removeClass("d-none");
                            jQuery('#percentage_container', form).addClass("d-none");
                        }
                    });
                }
                if (response.notification_available) {
                    notifyMeBefore(jQuery('#milestone-form-container'));
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function relatedSuretiesTab(contractId) {
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#related-sureties-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/related_sureties/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, 'contract');
               // suretiesTabGridEvents();

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}