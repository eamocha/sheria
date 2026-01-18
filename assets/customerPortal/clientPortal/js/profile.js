function signatureRemove(param) {
    jQuery.ajax({
        url: getBaseURL('customer-portal') + 'users/delete_signature/'+param.id,
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            if (response.result) {
                jQuery(param.elm, '#signature-div').remove();
                var rowCount = jQuery('div#signature-rows-container', '#signature-div').attr('data-count-row');
                jQuery('div#signature-rows-container', '#signature-div').attr('data-count-row', rowCount - 1);
                pinesMessage({ty: 'success', m: _lang.deleteRecordSuccessfull});
                if(rowCount==1){
                    jQuery("#signature-save-btn" ,'#signature-rows-container').hide();
                    jQuery("#no-signature-div" ,'#signature-rows-container').removeClass('d-none');                }

            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'error', m: response.display_message});
                }else{
                    pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
                }

            }
            jQuery('.modal', '.confirmation-dialog-container').modal('hide');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function addSignatureRow(container) {
    var rowCount = jQuery('div#signature-rows-container', container).attr('data-count-row');
    var clonedRow = jQuery('#default-signature-rows-container .section', container).clone();
    rowCount++;
    var newId = 'signature-container-' + rowCount;
    clonedRow.attr('id', newId);
    jQuery('input', clonedRow).val('');
    jQuery('.delete-signature', clonedRow).attr('onclick', 'removeRow("#' + newId + '", "#' + container.attr('id') + '")');
    jQuery('.inline-error', clonedRow).attr('id', 'upload-error-' + rowCount).addClass('d-none');
    jQuery('.label-signature', clonedRow).attr('id', 'signature-label-' + rowCount);
    jQuery('input', clonedRow).removeAttr('disabled');
    jQuery('div#signature-rows-container', container).append(clonedRow);
    jQuery('div#signature-rows-container', container).attr('data-count-row', rowCount);
}

function removeRow(elmContainer, mainContainer, id) {
    var container = jQuery(elmContainer, mainContainer);
    id = id || false;
    var count = jQuery('div#signature-rows-container', mainContainer).attr('data-count-row');
    if (id) {
        confirmationDialog('confirm_delete_record', {
            resultHandler: signatureRemove,
            parm: {id: id, elm: elmContainer}
        });
    } else {
        if (count > 1) {
            jQuery(container).remove();
            jQuery('div#signature-rows-container', mainContainer).attr('data-count-row', count - 1);
            rowsUpdateCount('signature',mainContainer);
        } else {
            pinesMessage({ty: 'error', m: _lang.feedback_messages.deleteRowFailed});

        }
    }

}

function signatureAdd() {
    var formData = new FormData(document.getElementById(jQuery("form", '.master-container').attr('id')));
    formData.append('action', 'userSignatureForm');
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('customer-portal') + 'users/signature',
        type: 'POST',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            } else {
                pinesMessage({ty: 'warning', m: _lang.feedback_messages.error});
            }
            jQuery('#loader-global').hide();
        }, complete: function () {
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function signatureAddForm() {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('customer-portal') + 'users/add_signature',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#signature-form-container').length <= 0) {
                    jQuery('<div id="signature-form-container"></div>').appendTo("body");
                    var signatureFormContainer = jQuery('#signature-form-container');
                    signatureFormContainer.html(response.html);
                    commonModalDialogEvents(signatureFormContainer, signatureAddFormSubmit);
                    signatureAddFormEvents(signatureFormContainer);
                }
            }
            jQuery('#loader-global').hide();
        }, complete: function () {
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function signatureAddFormEvents(container) {
    jQuery('#sign-nav-tabs li:first-child a', container).tab('show');
    jQuery('#sign-nav-tabs', container).on('shown.bs.tab', function (e) {
        activeTab = jQuery(e.target).attr("aria-controls") // activated tab
        jQuery('#active-tab', container).val(activeTab);
    });

    jQuery('#full-name', container).on('change', function () {
        jQuery('.sign-full-name', container).html(jQuery(this).val());
    });
    jQuery('#initials', container).on('change', function () {
        jQuery('.sign-initials', container).html(jQuery(this).val());
    });
    jQuery('input:radio[name="choose[font]"]', container).change(
        function () {
            if (this.checked) {
                jQuery('tr.row-selectable', container).removeClass('row-selected');
                jQuery(this).closest('tr.row-selectable').addClass('row-selected');
            }
        });
    signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)'
    });
    initialsPad = new SignaturePad(document.getElementById('initials-pad'), {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)'
    });
}

function signatureAddFormSubmit(container) {
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    var proceed = true;
    if (activeTab == 'choose') {
        if (!jQuery("input:radio[name='choose[font]']").is(':checked')) {
            jQuery("div", jQuery('#choose', container)).find("[data-field=signature]").removeClass('d-none').html("Please choose a signature.").addClass('validation-error');
            return false;
        }
    }
    if (activeTab == 'drawing') {
        jQuery('.inline-error', container).addClass('d-none');
        if (!signaturePad.isEmpty()) {
            var sigDraw = signaturePad.toDataURL('image/png');
            var sigDrawImg = sigDraw.replace(/^data:image\/(png|jpg);base64,/, "");
            formData.append('draw[signature]', sigDrawImg);
        } else {
            jQuery("div", jQuery('#drawing', container)).find("[data-field=signature]").removeClass('d-none').html("Please insert signature.").addClass('validation-error');
            proceed = false;
        }
        if (!initialsPad.isEmpty()) {
            var initialsDraw = initialsPad.toDataURL('image/png');
            var initialsDrawImg = initialsDraw.replace(/^data:image\/(png|jpg);base64,/, "");
            formData.append('draw[initials]', initialsDrawImg);
        } else {
            jQuery("div", jQuery('#drawing', container)).find("[data-field=initials]").removeClass('d-none').html("Please insert your initials.").addClass('validation-error');
            proceed = false;
        }
        if (!proceed) {
            return false;
        }
    }
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        type: 'POST',
        url: getBaseURL('customer-portal') + 'users/add_signature',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                window.location = window.location.href;
            } else {

                if (typeof response.validation_errors !== 'undefined' && response.validation_errors) {
                    displayValidationErrors(response.validation_errors, jQuery('#' + activeTab, container));
                }
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function setDefaultSignature(id) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('customer-portal') + 'users/set_default_signature',
        type: 'POST',
        data: {id: id},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery('.set-default-link', '#signature-rows-container').removeClass('d-none');
                jQuery('.set-default-link', '#signature-container-'+id).addClass('d-none');
                jQuery('.active-signature', '#signature-rows-container').addClass('d-none');
                jQuery('.active-signature', '#signature-container-'+id).removeClass('d-none');

            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
