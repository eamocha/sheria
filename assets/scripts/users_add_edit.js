var loadingImgHTML = '<div class="row" id="loadingGif" align="center"><img src="assets/images/icons/16/loader-submit.gif" width="24" height="24" /></div>';

function userFormValidationRules() {
    jQuery("#userDetailsForm").validationEngine({
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false,
        'custom_error_messages': {
            '#firstName': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.firstName])
                }
            },
            '#lastName': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.lastName])
                }
            },
            '.label-signature': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.label])
                }
            },
            '#confirmPassword': {
                'condRequired': {
                    'message': _lang.validation_field_required.sprintf([_lang.passwordConfirmation])
                },
                'equals': {
                    'message': _lang.validation_field_passwords_not_match
                }
            }
        }
    });
}

function userProviderGroupsAutocomplete() {
    jQuery("#lookupProviderGroups").autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.more_filters = {};
            request.more_filters.allUsers = '0';
            jQuery.ajax({
                url: getBaseURL() + 'provider_groups/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched_add.sprintf([request.term]),
                            value: '',
                            record: {
                                id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.name,
                                value: '',
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 1,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                setSelectedProviderGroup(ui.item.record);
            } else if (ui.item.record.id == -1) {
                quickAddProviderGroup(ui.item.record.term);
            }
        }
    });
}

function userUserAutocomplete() {
    jQuery("#lookupUser").autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'users/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched_for.sprintf([request.term]),
                            value: '',
                            record: {
                                id: -1
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.firstName + ' ' + item.lastName,
                                value: item.firstName + ' ' + item.lastName,
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 1,
        select: function (event, ui) {
            if (ui.item.record.id > 0 && ui.item.record.id != jQuery("#id", '#userDetailsForm').val())
                window.location = getBaseURL() + 'users/edit/' + ui.item.record.id;
        }
    });
}

function quickAddProviderGroup(quickName) {
    quickName = quickName || '';
    var dialogWin = jQuery("#quickAddProviderGroupDialog");
    dialogWin.dialog({
        autoOpen: true,
        buttons: [{
            text: _lang.save_and_select,
            click: function () {
                var formObj = jQuery("#providerGroupQuickAddForm");
                if (formObj.validationEngine('validate')) {
                    var newCompanyData =
                        jQuery.ajax({
                            url: getBaseURL() + 'provider_groups/quick_add',
                            type: 'post',
                            dataType: 'json',
                            data: formObj.serialize(),
                            beforeSend: function () {
                                jQuery("#loadingGif", formObj).show('fast');
                            },
                            success: function (response) {
                                jQuery("#loadingGif", formObj).hide('fast');
                                switch (response.status) {
                                    case 100:
                                    case 101:
                                        for (i in response.validationErrors) {
                                            jQuery('#' + i, '#providerGroupQuickAddForm').after('<span class="help-block error">' + String(response.validationErrors[i]) + '</span>');
                                        }
                                        break;
                                    case 500:
                                        setSelectedProviderGroup(response.record);
                                        pinesMessage({
                                            ty: 'success',
                                            m: _lang.feedback_messages.addedNewProviderGroupSuccessfully
                                        });
                                        dialogWin.dialog("close");
                                }
                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                }
            },
            'class': 'btn btn-sm btn-info'
        }],
        close: function () {
            dialogWin.dialog("destroy");
            dialogWin.addClass('d-none');
        },
        modal: false,
        open: function () {
            var that = jQuery(this);
            that.removeClass('d-none');
            jQuery(window).bind('resize', (function () {
                resizeNewDialogWindow(that, '40%', '300');
            }));
            resizeNewDialogWindow(that, '40%', '300');
            jQuery("#providerGroupQuickAddForm").validationEngine({
                validationEventTrigger: "submit",
                autoPositionUpdate: true,
                promptPosition: 'topLeft',
                scroll: false,
                'custom_error_messages': {
                    '#name': {
                        'required': {
                            'message': _lang.validation_field_required.sprintf([_lang.name])
                        }
                    }
                }
            });
            jQuery('#name', '#providerGroupQuickAddForm').val(quickName);
        },
        title: _lang.quickAddNewProviderGroup
    });
}

function setSelectedProviderGroup(providerGroup) {
    var theWrapper = jQuery('#selected_provider_groups', '#userDetailsForm');
    if (providerGroup.id && !jQuery('#user_provider_group' + providerGroup.id, theWrapper).length) {
        theWrapper.append(
            jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="user_provider_group' + providerGroup.id + '"><span id="' + providerGroup.id + '">' + providerGroup.name + '</span> </div>')
                .append(jQuery('<input type="hidden" value="' + providerGroup.id + '" name="provider_groups_users[]" />'))
                .append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button remove-button-event flex-end-item" tabindex="-1">')
                    .click(function (event) {
                        event.preventDefault();
                        jQuery(this.parentNode).remove();
                    })
                .append(jQuery('<i class="fa fa-remove"></i></a></div>'))
                )
        );
    }
}

function submitUsersForm() {
    jQuery('form#userDetailsForm').submit();
}

function userNiceDropDownLists() {
    jQuery("#nationality").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseNationality,
        width: '100%'
    });
    jQuery("#country").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseCountry,
        width: '100%',
        height: 100
    });
}

function cloneUserForm(id) {
    if (canAddUser) {
        window.location = getBaseURL() + 'users/edit/' + id + '/clone';
    } else {
        displaySubscriptionAdditionalUserForm();
    }
}

jQuery(document).ready(function () {
    userFormValidationRules();
    setDatePicker('#date-of-birth-date-container', jQuery('#add-edit-user-container'));
    userUserAutocomplete();
    userProviderGroupsAutocomplete();
    userNiceDropDownLists();
    setDatePicker('#dateOfBirth', jQuery('#personal_info_div'));

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
    var isIE9 = !!navigator.userAgent.match(/Trident\/5\./);
    if (msie > 0 || isIE9) {
        jQuery('#profilePicture').removeClass('d-none');
        jQuery('#edit').addClass('d-none');
    }
});

function signatureRemove(param) {
    jQuery.ajax({
        url: getBaseURL() + 'users/delete_signature/' + param.id,
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            if (response.result) {
                jQuery(param.elm, '#signature-div').remove();
                var rowCount = jQuery('div#signature-rows-container', '#signature-div').attr('data-count-row');
                jQuery('div#signature-rows-container', '#signature-div').attr('data-count-row', rowCount - 1);
                pinesMessage({ty: 'success', m: _lang.deleteRecordSuccessfull});

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
            rowsUpdateCount("signature",mainContainer);

        } else {
            pinesMessage({ty: 'error', m: _lang.feedback_messages.deleteRowFailed});

        }
    }

}

function signatureAddForm() {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'users/add_signature',
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
                    initializeModalSize(signatureFormContainer, 0.6);
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
            jQuery("div", jQuery('#choose', container)).find("[data-field=signature]").removeClass('d-none').html(_lang.feedback_messages.chooseSignature).addClass('validation-error');
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
            jQuery("div", jQuery('#drawing', container)).find("[data-field=signature]").removeClass('d-none').html(_lang.feedback_messages.insertSignature).addClass('validation-error');
            proceed = false;
        }
        if (!initialsPad.isEmpty()) {
            var initialsDraw = initialsPad.toDataURL('image/png');
            var initialsDrawImg = initialsDraw.replace(/^data:image\/(png|jpg);base64,/, "");
            formData.append('draw[initials]', initialsDrawImg);
        } else {
            jQuery("div", jQuery('#drawing', container)).find("[data-field=initials]").removeClass('d-none').html(_lang.feedback_messages.insertInitials).addClass('validation-error');
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
        url: getBaseURL() + 'users/add_signature',
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
        url: getBaseURL() + 'users/set_default_signature',
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
