var isPrivate, availableUsers, forceDownloadDoc;
jQuery(document).ready(function () {
    if (jQuery('.contract-container').length > 0) {
        showToolTip('.contract-container', '.icon-tooltip');
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
            if(typeof response.overall_status !== 'undefined'){
                jQuery('#approval-status-icon', '.side-menu').attr('src', "assets/images/contract/" + response.overall_status + ".svg");
                jQuery('#approval-status-icon', '.side-menu').tooltipster("destroy");
                jQuery('#approval-status-icon', '.side-menu').attr('title', _lang['awaiting_approval']);
                jQuery('#approval-status-icon', '.side-menu').removeClass('hide');
                jQuery('#approval-status-icon', '.side-menu').tooltipster();
                approvalCenterTab(contractId, 'contract');
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

function approvalForm(approved, contractId, contractApprovalStatusId, approvalCount, module) {
    module = module || 'contract';
    jQuery.ajax({
        dataType: 'JSON',
        data: {'contract_approval_status_id': contractApprovalStatusId, 'approved': approved},
        type: 'GET',
        url: getBaseURL(module) + 'contracts/submit_for_approval',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".approval-form-container").length <= 0) {
                    jQuery('<div class="d-none approval-form-container"></div>').appendTo("body");
                    var approvalFormContainer = jQuery('.approval-form-container');
                    approvalFormContainer.html(response.html).removeClass('d-none');
                    setDatePicker('#approval-date', approvalFormContainer);
                    commonModalDialogEvents(approvalFormContainer);
                    jQuery("#form-submit", approvalFormContainer).click(function () {
                        submitApproval(approvalFormContainer, module);
                    });
                    jQuery(approvalFormContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            submitApproval(approvalFormContainer, module);
                        }
                    });
                    initializeModalSize(approvalFormContainer);
                    jQuery('#contract-id', approvalFormContainer).val(contractId);
                    jQuery('#contract-approval-status-id', approvalFormContainer).val(contractApprovalStatusId);
                    jQuery('#status', approvalFormContainer).val(approved ? 'approved' : 'rejected');
                    jQuery('.select-picker', approvalFormContainer).selectpicker();
                    if (approvalCount == 1) {
                        jQuery('#enforce-previous-approvals-container', approvalFormContainer).addClass('d-none');
                    }
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

function submitApproval(container, module) {
    var formData = jQuery("form#approval-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/submit_for_approval',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.modal', container).modal('hide');
                if (module === 'customer-portal') {
                    window.location.reload();
                } else {
                    approvalCenterTab(jQuery('#contract-id', container).val(), module);
                    jQuery('#approval-status-icon', '.side-menu').attr('src', "assets/images/contract/" + response.overall_status + ".svg");
                    jQuery('#approval-status-icon', '.side-menu').tooltipster("destroy");
                    jQuery('#approval-status-icon', '.side-menu').attr('title', _lang[response.overall_status]);
                    if (response.overall_status == 'approved') {
                        jQuery('#signature-status-icon', '.side-menu').attr('src', "assets/images/contract/awaiting_signature.svg");
                        jQuery('#signature-status-icon', '.side-menu').tooltipster("destroy");
                        jQuery('#signature-status-icon', '.side-menu').attr('title', _lang.awaiting_signature);
                    }
                    showToolTip('.side-menu', '.icon-tooltip');
                }
            } else {
                displayValidationErrors(response.validation_errors, container);
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.btn-save', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractTabEvents(response, contractId, module){
    module = module || 'contract';
    if(module == 'contract'){
        jQuery('#main-section').html(response.html);
        jQuery('#edit-btn-link', '#header-section').prop("onclick", null).off("click");
        jQuery('#edit-btn-link', '#header-section').attr("href", getBaseURL(module) + 'contracts/view/' + contractId + "/1");
        jQuery('#header-section', '.contract-container').removeClass('d-none');
        window.history.replaceState({}, '', getBaseURL('contract/contracts/view/' + contractId));
    }else{
        jQuery('#contracts-details').html(response.html);
    }

}

//show approval center data
function approvalCenterTab(contractId, module) {
    module = module || 'contract';
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#approval-center-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL(module) + 'contracts/approval_center/' + contractId,
        dataType: 'JSON',
        async: false,
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    contractTabEvents(response, contractId, module);
                    jQuery('.new-comment-section', '#contracts-details').each(function (index, element) {
                        initTiny('comment', '#' + jQuery(this).attr('id'), contractId, 50);
                    });
                    jQuery('.tooltip-title', '#contracts-details').tooltipster({
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
            } else {
                pinesMessage({ty: 'information', m: response.display_message});
            }

        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

//show signature center data
function signatureCenterTab(contractId, module) {
    module = module || 'contract';
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#signature-center-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL(module) + 'contracts/signature_center/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    contractTabEvents(response, contractId, module);
                    jQuery('.tooltip-title', '#contracts-details').tooltipster({
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
            } else {
                pinesMessage({ty: 'information', m: response.display_message});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function toggleCheckbox(hiddenInput, checkBoxInput) {
    hiddenInput.val(checkBoxInput.is(':checked') ? '1' : '0');
}

//show documents data
function docsTab(contractId, module) {
    module = module || 'contract';
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#docs-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL(module) + 'contracts/documents/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, module);
                documentTabEvents(module);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractDocList(contractId, type, module) {
    module = module || 'contract';
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {type: type},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL(module) + 'contracts/list_contract_docs/' + contractId,
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".contract-docs-container").length <= 0) {
                    jQuery('<div class="d-none contract-docs-container"></div>').appendTo("body");
                    var contractDocsContainer = jQuery('.contract-docs-container');
                    contractDocsContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(contractDocsContainer);
                }
            } else {
                pinesMessage({ty: 'error', m: response.display_message});

            }
        }, complete: function () {
            jQuery('#loader-global').hide();


        },
        error: defaultAjaxJSONErrorsHandler
    });

}

function signForm(contractId, contractSignatureStatusId, module) {
    module = module || 'contract';
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {'contract_signature_status_id': contractSignatureStatusId, 'contract_id': contractId},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL(module) + 'contracts/sign_contract_doc/',
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".contract-docs-container").length <= 0) {
                    jQuery('<div class="d-none contract-docs-container"></div>').appendTo("body");
                    var contractDocsContainer = jQuery('.contract-docs-container');
                    contractDocsContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(contractDocsContainer);
                    jQuery("#form-submit", contractDocsContainer).click(function () {
                        signFormSubmit(contractDocsContainer, module);
                    });
                    jQuery(contractDocsContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            signFormSubmit(contractDocsContainer, module);
                        }
                    });
                    initializeModalSize(contractDocsContainer, 0.4, 0.4);


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

function signFormSubmit(container, module) {
    module = module || 'contract';
    var formData = jQuery("form#contract-signature-form", container).serializeArray();
    // var docId = jQuery(".contract-doc-checkbox:checked").val();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/sign_contract_doc',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.modal', container).modal('hide');
                signatureCenterTab(jQuery('#contract-id', container).val(), module);
                jQuery('#signature-status-icon', '.side-menu').attr('src', "assets/images/contract/" + response.overall_status + ".svg");
                jQuery('#signature-status-icon', '.side-menu').tooltipster("destroy");
                jQuery('#signature-status-icon', '.side-menu').attr('title', _lang[response.overall_status]);
                showToolTip('.side-menu', '.icon-tooltip');
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
            } else {
                pinesMessage({ty: 'warning', m: response.display_message});
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function summaryForm(module, approvalSignatureStatusId, type) {
    module = module || 'contract';
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {
            'approval_signature_status_id': approvalSignatureStatusId,
            'type': type,
        },
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL(module) + 'contracts/load_summary/',
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".contract-summary-container").length <= 0) {
                    jQuery('<div class="d-none contract-summary-container"></div>').appendTo("body");
                    var summaryContainer = jQuery('.contract-summary-container');
                    summaryContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(summaryContainer);
                    jQuery("#form-submit", summaryContainer).click(function () {
                        submitSummary(summaryContainer, module);
                    });
                    jQuery(summaryContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            submitSummary(summaryContainer, module);
                        }
                    });
                    initializeModalSize(summaryContainer, 0.5, 0.6);
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

function submitSummary(container, module) {
    var formData = jQuery("form#summary-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/load_summary',
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

function sendEmailToContact(contractId, module, approvalStatusId) {
    module = module || 'contract';
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {'contract_id': contractId, 'approval_status_id': approvalStatusId},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL(module) + 'contracts/send_email_to_contact_approver/',
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".request-contact-approval-container").length <= 0) {
                    jQuery('<div class="d-none request-contact-approval" id="request-contact-approval-container"></div>').appendTo("body");
                    var contactApprovalContainer = jQuery('.request-contact-approval');
                    contactApprovalContainer.html(response.html).removeClass('d-none');
                    var ContactlookupDetails = {
                        'lookupField': jQuery('#lookup-contacts', contactApprovalContainer),
                        'lookupContainer': 'contact-lookup-container',
                        'errorDiv': 'lookupContacts',
                        'boxName': 'approverContacts',
                        'boxId': '#selected-contacts',
                        'resultHandler': setSelectedContactToApprover
                    };
                    lookUpContacts(ContactlookupDetails, contactApprovalContainer, true, false, true);
                    lookupPrivateUsers(jQuery('#lookupCCUsers', contactApprovalContainer), 'cc_users', '#selected-users', 'cc-users-container', contactApprovalContainer);
                    jQuery('#approval-status-id', contactApprovalContainer).val(approvalStatusId);
                    commonModalDialogEvents(contactApprovalContainer, sendEmailToContactSubmit);
                    initializeModalSize(contactApprovalContainer, 0.6, 0.6);
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

function sendEmailToContactSubmit(container) {
    var formData = jQuery("form#request-approval-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL('contract') + 'contracts/send_email_to_contact_approver/',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.emailSentSuccessfully});
                jQuery('.modal', container).modal('hide');
                approvalCenterTab(jQuery('#contract-id', container).val());
            } else {
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
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
function resendApprovalEmail(contractId, module, approvalStatusId) {
    jQuery.ajax({
        dataType: 'JSON',
        data: {'contract_id': contractId, 'approval_status_id': approvalStatusId},
        type: 'POST',
        url: getBaseURL(module) + 'contracts/resend_approval_email/',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            pinesMessage({ty: response.result ? 'success' : 'warning', m: response.result ? _lang.feedback_messages.emailSentSuccessfully : _lang.feedback_messages.outgoingMailNotConfigured});

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function uploadContactApproval(contractId, approvalStatusId) {
    jQuery.ajax({
        dataType: 'JSON',
        data: {'contract_id': contractId, 'contract_approval_status_id': approvalStatusId},
        type: 'GET',
        url: getBaseURL('contract') + 'contracts/upload_approval_document',
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
                    commonModalDialogEvents(uploadFileContainer);
                    jQuery("#form-submit", uploadFileContainer).click(function () {
                        uploadContactApprovalSubmit(uploadFileContainer);
                    });
                    jQuery(uploadFileContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            uploadContactApprovalSubmit(uploadFileContainer);
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

function uploadContactApprovalSubmit(container) {
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL('contract') + 'contracts/upload_approval_document',
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

function uploadSignedDocumentForm(contractId, signatureStatusId, module) {
    module = module || 'contract';
    jQuery.ajax({
        dataType: 'JSON',
        data: {'contract_id': contractId, 'contract_signature_status_id': signatureStatusId},
        type: 'GET',
        url: getBaseURL(module) + 'contracts/upload_signed_document',
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
                    commonModalDialogEvents(uploadFileContainer);
                    jQuery("#form-submit", uploadFileContainer).click(function () {
                        uploadSignedDocumentFormSubmit(uploadFileContainer, module);
                    });
                    jQuery(uploadFileContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            uploadSignedDocumentFormSubmit(uploadFileContainer, module);
                        }
                    });
                }
            } else {
                pinesMessage({ty: 'warning', m: response.message});
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function uploadSignedDocumentFormSubmit(container, module) {
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/upload_signed_document',
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
                signatureCenterTab(response.module_record_id, module);
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
                jQuery('#signature-status-icon').attr('src', "assets/images/contract/" + response.overall_status + ".svg");
                jQuery('#signature-status-icon', '.side-menu').tooltipster("destroy");
                jQuery('#signature-status-icon', '.side-menu').attr('title', _lang[response.overall_status]);
                showToolTip('.side-menu', '.icon-tooltip');
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

function loadSignatureVariables(that, module) {
    module = module || 'contract';
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {'document_id': jQuery(that).val()},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL(module) + 'contracts/load_document_variables/',
        success: function (response) {
            if (response.result && response.html) {
                jQuery('#doc-signature-variables').html(response.html);
                jQuery('#signature-rows-container').removeClass('d-none');
            } else {
                jQuery('#doc-signature-variables').html('');
                jQuery('#signature-rows-container').addClass('d-none');
                pinesMessage({ty: 'warning', m: response.display_message});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function signWithDocuSign(contractId, contractSignatureStatusId, module) {
    module = module || 'contract';
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {'contract_signature_status_id': contractSignatureStatusId, 'contract_id': contractId},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL(module) + 'docusign_integration/sign_contract/',
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".contract-docs-container").length <= 0) {
                    jQuery('<div class="d-none contract-docs-container"></div>').appendTo("body");
                    var contractDocsContainer = jQuery('.contract-docs-container');
                    contractDocsContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(contractDocsContainer);
                    jQuery("#form-submit", contractDocsContainer).click(function () {
                        signWithDocuSignSubmit(contractDocsContainer, module);
                    });
                    jQuery(contractDocsContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            signWithDocuSignSubmit(contractDocsContainer, module);
                        }
                    });
                    initializeModalSize(contractDocsContainer, 0.4, 0.4);

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

function signWithDocuSignSubmit(container, module) {
    var id = jQuery("input[name=document_id]:checked", container).val();
    var signatureId = jQuery("#id", container).val();
    if (id && signatureId) {
        window.location = getBaseURL(module) + 'docusign_integration/integrate/' + id + '/' + signatureId;
        return;
    } else {
        pinesMessage({ty: 'error', m: _lang.validation_field_required.sprintf([_lang.file])});
        return false;
    }

}


function negotiationForm(contractId, contractApprovalStatusId, module) {
    module = module || 'contract';
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {'contract_id': contractId, 'contract_approval_status_id': contractApprovalStatusId},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL(module) + 'contracts/add_negotiation/',
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery("#contract-negotiation-container").length <= 0) {
                    jQuery('<div class="d-none" id="contract-negotiation-container"></div>').appendTo("body");
                    var negotiationContainer = jQuery('#contract-negotiation-container');
                    negotiationContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(negotiationContainer);
                    initTiny('comment', '#contract-negotiation-container', contractId);
                    jQuery("#form-submit", negotiationContainer).click(function () {
                        submitNegotiationForm(contractId, negotiationContainer, module);
                    });
                    jQuery(negotiationContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            submitNegotiationForm(contractId, negotiationContainer, module);
                        }
                    });
                    initializeModalSize(negotiationContainer, 0.5, 0.5);

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


function submitNegotiationForm(contractId, container, module) {
    var formData = new FormData(document.getElementById(jQuery("form#contract-negotiate-form", container).attr('id')));
    formData.append('comment', tinymce.activeEditor.getContent());
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/add_negotiation',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('#form-submit', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                approvalCenterTab(contractId, module);
                jQuery('.modal', container).modal('hide');
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
            } else {
                displayValidationErrors(response.validation_errors, container);
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('#form-submit', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function submitNegotiationComment(contractId, negotiationId, contractApprovalStatusId, container, module) {
    module = module || 'contract';
    var formData = new FormData();
    formData.append('negotiation_id', negotiationId);
    formData.append('comment', tinymce.activeEditor.getContent());
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/add_comment_negotiation',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.comment-button', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});

                approvalCenterTab(contractId, module);
                jQuery('#negotiation-section-' + contractApprovalStatusId).removeClass('d-none');
                jQuery('#related-documents-count', '.contract-container').text(response.related_documents_count);
            } else {
                displayValidationErrors(response.validation_errors, container);
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.comment-button', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function forwardNegotiationForm(contractId, negotiationId, module) {
    module = module || 'contract';
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {'contract_id': contractId, 'negotiation_id': negotiationId},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL(module) + 'contracts/forward_negotiation/',
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery("#contract-forward-negotiation-container").length <= 0) {
                    jQuery('<div class="d-none" id="contract-forward-negotiation-container"></div>').appendTo("body");
                    var forwardNegotiationContainer = jQuery('#contract-forward-negotiation-container');
                    forwardNegotiationContainer.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(forwardNegotiationContainer);
                    // jQuery('.select-picker', forwardNegotiationContainer).selectpicker();
                    // lookUpUsers(jQuery('#user-lookup', forwardNegotiationContainer), jQuery('#user-id', forwardNegotiationContainer), 'user_id', jQuery('#users-container', forwardNegotiationContainer), forwardNegotiationContainer);
                    if (module == 'customer-portal') {
                        lookUpCollaborators(jQuery('#user-lookup', forwardNegotiationContainer), jQuery('#user-id', forwardNegotiationContainer), 'user_id', jQuery('#users-container', forwardNegotiationContainer), forwardNegotiationContainer);

                    } else {
                        lookUpUsers(jQuery('#user-lookup', forwardNegotiationContainer), jQuery('#user-id', forwardNegotiationContainer), 'user_id', jQuery('.user-container', forwardNegotiationContainer), forwardNegotiationContainer);

                        jQuery('#user-type', forwardNegotiationContainer).selectpicker().change(function () {
                            jQuery("#user-lookup", forwardNegotiationContainer).val('').typeahead('destroy');//destroy the typehead when changing the type(company/contact) and re-initialize it.
                            jQuery("#user-id", forwardNegotiationContainer).val('');
                            jQuery(".inline-error", forwardNegotiationContainer).html('');
                            if (jQuery(this).val() == 'collaborator') {
                                lookUpCollaborators(jQuery('#user-lookup', forwardNegotiationContainer), jQuery('#user-id', forwardNegotiationContainer), 'user_id', jQuery('#users-container', forwardNegotiationContainer), forwardNegotiationContainer);

                            } else {
                                lookUpUsers(jQuery('#user-lookup', forwardNegotiationContainer), jQuery('#user-id', forwardNegotiationContainer), 'user_id', jQuery('.user-container', forwardNegotiationContainer), forwardNegotiationContainer);

                            }
                        });
                    }


                    jQuery("#form-submit", forwardNegotiationContainer).click(function () {
                        submitForwardNegotiationForm(forwardNegotiationContainer, module);
                    });
                    jQuery(forwardNegotiationContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            submitForwardNegotiationForm(forwardNegotiationContainer, module);
                        }
                    });
                    initializeModalSize(forwardNegotiationContainer, 0.5, 0.4);

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

function submitForwardNegotiationForm(container, module) {
    // var formData = new FormData(document.getElementById(jQuery("form#contract-forward-negotiate-form", container).attr('id')));
    var formData = jQuery("form#contract-forward-negotiate-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/forward_negotiation',
        // contentType: false, // required to be disabled
        // cache: false,
        // processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('#form-submit', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                approvalCenterTab(jQuery('#contract-id', container).val(), module);
                jQuery('.modal', container).modal('hide');
                showHideNegotiations(response.negotiation_id);
            } else {
                if (response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.actionFailed});

                }

            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('#form-submit', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function completeNegotiationForm(contractId, negotiationId, module) {
    confirmationDialog('complete_negotiation_confirmation', {
        resultHandler: function () {
            submitCompleteNegotiationForm(contractId, negotiationId, module);
        }
    });
    // module = module || 'contract';
    // jQuery.ajax({
    //     beforeSend: function () {
    //         jQuery('#loader-global').show();
    //     },
    //     data: {'contract_id': contractId, 'contract_approval_status_id': contractApprovalStatusId},
    //     dataType: 'JSON',
    //     type: 'GET',
    //     url: getBaseURL(module) + 'contracts/complete_negotiation/',
    //     success: function (response) {
    //         if (response.result) {
    //             // if ('undefined' !== typeof response.html && response.html) {
    //             //     if (jQuery("#negotiation-complete-container").length <= 0) {
    //             //         jQuery('<div class="d-none" id="negotiation-complete-container"></div>').appendTo("body");
    //             //         var container = jQuery('#negotiation-complete-container');
    //             //         container.html(response.html).removeClass('d-none');
    //             //         commonModalDialogEvents(container);
    //             //         initTiny('comment', '#negotiation-complete-container');
    //             //         jQuery("#form-submit", container).click(function () {
    //             //             submitCompleteNegotiationApprovalForm(contractId, contractApprovalStatusId, module, container);
    //             //         });
    //             //         jQuery(container).find('input').keypress(function (e) {
    //             //             // Enter pressed?
    //             //             if (e.which == 13) {
    //             //                 e.preventDefault();
    //             //                 confirmationDialog('confim_delete_action', {resultHandler: submitCompleteNegotiationForm, parm: contractId});
    //             //                 submitCompleteNegotiationApprovalForm(contractId, contractApprovalStatusId, module, container);
    //             //             }
    //             //         });
    //             //         initializeModalSize(container);
    //             //
    //             //     }
    //             // }else{//complete negotiation
    //            
    //             // }
    //
    //         } else {
    //             pinesMessage({ty: 'warning', m: response.display_message});
    //         }
    //     }, complete: function () {
    //         jQuery('#loader-global').hide();
    //     },
    //     error: defaultAjaxJSONErrorsHandler
    // });
}

function submitCompleteNegotiationApprovalForm(contractId, contractApprovalStatusId, module, container) {
    var formData = new FormData(document.getElementById(jQuery("form#complete-negotiate-form", container).attr('id')));

    formData.append('contract_approval_status_id', contractApprovalStatusId);
    formData.append('contract_id', contractId);
    formData.append('comment', tinymce.activeEditor.getContent());

    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'contracts/complete_negotiation',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.save-button', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            // jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                approvalCenterTab(contractId, module);
                // jQuery('.modal', container).modal('hide');
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.save-button', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function submitCompleteNegotiationForm(contractId, negotiationId, module) {
    // var formData = new FormData(document.getElementById(jQuery("form#contract-negotiate-form", container).attr('id')));
    // formData.append('comment', tinymce.activeEditor.getContent());
    jQuery.ajax({
        dataType: 'JSON',
        data: {'negotiation_id': negotiationId, 'action': 'complete'},
        type: 'POST',
        url: getBaseURL(module) + 'contracts/complete_negotiation',
        // contentType: false, // required to be disabled
        // cache: false,
        // processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            // jQuery('.btn-save', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            // jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                approvalCenterTab(contractId, module);
                showHideNegotiations(negotiationId);
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
            // jQuery('.btn-save', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function approveWithSignature(container) {
    if (jQuery('#signature-rows-container', container).length > 0) {
        jQuery('#signature-rows-container', container).removeClass('d-none');
        jQuery('input[type=radio]', container).removeAttr('disabled');
        jQuery('.approve-with-signature-link', container).addClass('d-none');
    } else {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.signatureNotAvailable});
    }
}

function toggleDetails(container, iconId) {
    var elementsToggleIcon = jQuery('#' + iconId, container);
    if (elementsToggleIcon.hasClass('fa-arrow-right')) {
        elementsToggleIcon.removeClass('fa-arrow-right');
        elementsToggleIcon.addClass('fa-arrow-down');
    } else {
        elementsToggleIcon.removeClass('fa-arrow-down');
        elementsToggleIcon.addClass('fa-arrow-right');
    }
}

function showHideNegotiations(id) {
    if (jQuery('#negotiation-section-' + id).hasClass('d-none')) {
        jQuery('#negotiation-section-' + id).removeClass('d-none');
        jQuery('body').append('<div class="modal-backdrop fade in"></div>');
    } else {
        jQuery('#negotiation-section-' + id).addClass('d-none');
        jQuery('.modal-backdrop').remove();
    }
}

function draftCollaborateTab(contractId, module) {
    module = module || 'contract';
    jQuery('.nav-link', '.contract-container').removeClass('active');
    jQuery('#collaborate-tab', '.contract-container').addClass('active');
    jQuery.ajax({
        url: getBaseURL(module) + 'contracts/draft_collaborate/' + contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                contractTabEvents(response, contractId, module);
                // documentTabEvents(module);

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function loadiFrame(id, contractId, ext, externalUser) {
    id = id || false;
    externalUser = externalUser || false;
    ext = ext || 'docx';
    var documentEditorUrl = "https://documenteditor.sheria360.com/app/start?module=contract";

    if (id) {
        documentEditorUrl += "&extension=" + ext + "&id=" + id;
    } else {
        documentEditorUrl += "&extension=" + ext + "&moduleRecordId=" + contractId;
    }
    if(externalUser){
        let jsonObj = {
            "x-api-token": jQuery('#external-user-token', '.external-actions-container').val(),
            "x-api-token-id": jQuery('#external-user-token-id', '.external-actions-container').val(),
        };
        loadiFrameAction(documentEditorUrl, jsonObj, getBaseURL()); 
    }else{
        jQuery.ajax({
            dataType: "JSON",
            url: getBaseURL() + "users/get_api_token_data",
            type: "GET",
            success: function (response) {
                if (response.result) {
                    let jsonObj = {
                        "x-api-key":  response.apiKey,
                    };
                   loadiFrameAction(documentEditorUrl, jsonObj, response.apiBaseUrl) 
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function loadiFrameAction(documentEditorUrl, jsonObj, baseUrl){
    let iframeEl = jQuery("#document-editor-iframe");

      // Check if the URL or JSON object is valid
    if (!documentEditorUrl || !jsonObj || Object.keys(jsonObj).length === 0) {
        console.error("Invalid content: Cannot load iframe due to missing or invalid data.");
        return;
    }

    if (iframeEl != null) {
        jQuery("#loader-global").remove();
        document.getElementById('document-editor-iframe').addEventListener('load', function () {
            document.getElementById('document-editor-iframe').contentWindow.postMessage({payload: jsonObj}, "*");
        });
    }
    documentEditorUrl += "&apiBaseUrl=" + btoa(baseUrl);
    iframeEl.attr("src", documentEditorUrl);
}

function updateDocsCount(contractId) {
    jQuery.ajax({
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        data: {contract_id: contractId},
        dataType: 'JSON',
        type: 'GET',
        url: getBaseURL('contract') + 'contracts/load_docs_count/',
        success: function (response) {
            if (response.count) {
                jQuery('#related-documents-count', '.contract-container').text(response.count);
            }
        }, complete: function () {
            jQuery('#loader-global').hide();


        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function shareDocForm(module, moduleId) {
    module = module || 'contract';
    jQuery.ajax({
        url: getBaseURL(module) + 'contracts/share_doc',
        data: {module_id: moduleId, doc_id: jQuery('#selected-doc-id', '#contracts-details').val()},
        dataType: 'JSON',
        type: 'GET',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".share-with-container").length <= 0) {
                    jQuery('<div class="d-none share-with-container primary-style"></div>').appendTo("body");
                    var container = jQuery('.share-with-container');
                    container.html(response.html).removeClass('d-none');
                    commonModalDialogEvents(container, shareDocFormSubmit);
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.somethingWrong});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function shareDocFormSubmit(container) {
    var formData = jQuery("form#share-form", container).serializeArray();
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL('contract') + 'contracts/share_doc/',
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.documentSharedSuccessfully});
                jQuery('.modal', container).modal('hide');
            } else {
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
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
function expandCollapseCustomFields(){
    container = jQuery('#contracts-details');
    if(jQuery("#expand-collapse-link",container).attr('title') === "Show More"){
        jQuery("#expand-collapse-icons",container).attr('src',"assets/images/contract/collapse.svg");
        jQuery("#expand-collapse-link",container).attr('title',_lang.showLess);
        jQuery("#collapsable-custom-fields",container).removeClass("d-none");
        jQuery("#expand-collapse-text",container).html(_lang.collapse);
    } else {
        jQuery("#expand-collapse-icons",container).attr('src',"assets/images/contract/expand.svg");
        jQuery("#expand-collapse-link",container).attr('title',_lang.showMore);
        jQuery("#collapsable-custom-fields",container).addClass("d-none");
        jQuery("#expand-collapse-text",container).html(_lang.expandAllFields);
    }
}