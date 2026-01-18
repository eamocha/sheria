function editShareMovement(shareMovementId, shareholdersGrid = false) {
    if(shareholdersGrid){
        jQuery.ajax({
            url: getBaseURL() + 'share_movements/check_transactions/' + shareMovementId,
            type: 'GET',
            dataType: 'JSON',
            beforeSend: function () {
            },
            success: function (response) {
                if(response.result){
                    _editShareMovement(shareMovementId);
                }
                else {
                    var msg = _lang.feedback_messages.shareholderMultipleTransactions.sprintf(['<a target="_blank" href="' + getBaseURL() + 'reports/shares_by_date/' + response.data.company_id + '/' + response.data.member_id + '">' + _lang.companyShare + '</a>']);
                    pinesMessage({ty: 'warning', m: msg});
                }
            }
        });
    } else {
        _editShareMovement(shareMovementId);
    }
}

function _editShareMovement(shareMovementId){
    var container = jQuery('#shareMovementDialog');
    container.prepend('<div class="loading" id="loadingEffect">&nbsp;</div>');
    jQuery.ajax({
        url: getBaseURL() + 'share_movements/purge_edit/' + shareMovementId,
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
        },
        success: function (response) {
            if (!container.is(':data(dialog)')) {
                container.dialog({
                    autoOpen: true,
                    buttons: [
                        {
                            'class': 'btn btn-info',
                            click: function () {
                                var shareMovementForm = jQuery("form#shareMovementForm", container);
                                var dataIsValid = shareMovementForm.validationEngine('validate');
                                var formData = shareMovementForm.serialize();
                                if (dataIsValid) {
                                    jQuery.ajax({
                                        url: getBaseURL() + 'share_movements/purge_edit',
                                        data: formData,
                                        type: 'POST',
                                        dataType: 'JSON',
                                        beforeSend: function () {
                                            jQuery('.error', container).each(function () {
                                                jQuery(this).removeClass('error');
                                            });
                                        },
                                        success: function (response) {
                                            if (response.result == true) {
                                                container.dialog("close");
                                                resetShareMovementFormValues();
                                                window.location.href = window.location.href;
                                            } else {
                                                var errorMsg = '';
                                                for (i in response.validationErrors) {
                                                    jQuery('#' + i, container).addClass('invalid').focus(function () {
                                                        jQuery(this).removeClass('invalid');
                                                    });
                                                    errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                                }
                                                if (errorMsg != '') {
                                                    pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                                }
                                            }
                                        },
                                        error: defaultAjaxJSONErrorsHandler
                                    });
                                }
                            },
                            text: _lang.save
                        },
                        {
                            'class': 'btn btn-link',
                            click: function () {
                                resetShareMovementFormValues();
                                jQuery(this).dialog("close");
                            },
                            text: _lang.cancel
                        }
                    ],
                    close: function () {
                        resetShareMovementFormValues();
                        jQuery(window).unbind('resize');
                    },
                    open: function () {
                        var that = jQuery(this);
                        that.removeClass('d-none');
                        resetShareMovementFormValues();
                        jQuery(window).bind('resize', (function () {
                            resizeNewDialogWindow(container, '90%', '550');
                        }));
                        resizeNewDialogWindow(container, '90%', '550');
                    },
                    draggable: true,
                    modal: false,
                    resizable: true,
                    title: _lang.editShareMovement
                });
            }
            jQuery('#loadingEffect', container).remove();
            switch (parseInt(response.status)) {
                case 200: // data received
                    container.html(response.html);
                    loadShareMovementFormEvents();
                    var shareMovementForm = jQuery("form#shareMovementForm", container);
                    jQuery('#numberOfSharesTo', shareMovementForm).focus();
                    break;
                case 404:
                    //Invalid Record ID
                    break;
                default:
                    break;
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function loadShareMovementFormEvents() {
    var container = jQuery('#shareMovementDialog');
    makeFieldsDatePicker({fields: ['initiatedOnTo', 'executedOnTo', 'initiatedOnFrom', 'executedOnFrom']});
    makeFieldsHijriDatePicker({fields: ['initiated-on-to-hijri','executed-on-hijri']});
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        jQuery('#initiated-on-to-hijri', '#initiated-on-container').val(gregorianToHijri(jQuery('#initiated-on-to-gregorian', '#initiated-on-container').val()));
        jQuery('#executed-on-hijri', '#executed-on-container').val(gregorianToHijri(jQuery('#executed-on-gregorian', '#executed-on-container').val()));
    }
    jQuery("#shareMovementForm").validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
    jQuery('select#memberTypeTo').change(function () {
        jQuery("#shareholder_contact_idTo", container).val('');
        jQuery("#shareholder_company_idTo", container).val('');
        jQuery('#memberNameTo').val('');
    });
    jQuery('select#memberTypeFrom').change(function () {
        jQuery("#shareholder_contact_idFrom").val('');
        jQuery("#shareholder_company_idFrom").val('');
        jQuery('#memberNameFrom').val('');
    });
    jQuery('#numberOfSharesTo').change(function () {
        jQuery("#numberOfSharesFrom").val(this.value);
    });
    jQuery('#numberOfSharesFrom').change(function () {
        jQuery("#numberOfSharesTo").val(this.value);
    });
    jQuery('#categoryTo').change(function () {
        jQuery("#categoryFrom").val(this.value);
    });
    jQuery('#categoryFrom').change(function () {
        jQuery("#categoryTo").val(this.value);
    });
    jQuery('#initiatedOnTo').change(function () {
        jQuery("#initiatedOnFrom").val(this.value);
    });
    jQuery('#initiatedOnFrom').change(function () {
        jQuery("#initiatedOnTo").val(this.value);
    });
    jQuery('#executedOnTo').change(function () {
        jQuery("#executedOnFrom").val(this.value);
    });
    jQuery('#executedOnFrom').change(function () {
        jQuery("#executedOnTo").val(this.value);
    });
    var lookupTypeTo = jQuery("select#memberTypeTo", container);
    var url = '';
    jQuery("#memberNameTo", container).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.more_filters = {};
            if (lookupTypeTo.val() == 'Company') {
                url = 'companies';
                request.more_filters.id = jQuery('#company_idTo', container).val();//to ignore the company of tab.
                request.more_filters.category = ['Internal', 'Group'];//tofilter category of companies
                request.more_filters.requestFlag = 'sharesTransfer';//to specify this page form
            } else {
                url = 'contacts';
                request.more_filters = {};
            }
            jQuery.ajax({
                url: getBaseURL() + url + '/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
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
                            if (lookupTypeTo.val() == 'Person') {
                                return {
                                    label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    record: item
                                }
                            } else if (lookupTypeTo.val() == 'Company') {
                                return {
                                    label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                    value: item.name,
                                    record: item
                                }
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
            var memberType = lookupTypeTo.val();
            if (ui.item.record.id > 0) {
                jQuery("#shareholder_contact_idTo", container).val('');
                jQuery("#shareholder_company_idTo", container).val('');
                jQuery((memberType == 'Person' ? "#shareholder_contact_idTo" : "#shareholder_company_idTo"), container).val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                if (memberType == 'Person') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": container,
                        "lookupResultHandler": setContactDataAfterAutocompleteToSharesTransferFormTo,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                    companyContactFormMatrix.companyDialog = {
                        "referalContainerId": container,
                        "lookupResultHandler": setCompanyDataAfterAutocompleteToSharesTransferFormTo,
                        "lookupValue": ui.item.record.term
                    };
                    companyAddForm();
                }
            }
        }
    });
    var lookupTypeFrom = jQuery("select#memberTypeFrom", container);
    var url = '';
    jQuery("#memberNameFrom", container).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.more_filters = {};
            if (lookupTypeFrom.val() == 'Company') {
                url = 'companies';
                request.more_filters.id = jQuery('#company_idFrom', container).val();//to ignore the company of tab.
                request.more_filters.category = ['Internal', 'Group'];//tofilter category of companies
                request.more_filters.requestFlag = 'sharesTransfer';//to specify this page form
            } else {
                url = 'contacts';
                request.more_filters = {};
            }
            jQuery.ajax({
                url: getBaseURL() + url + '/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
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
                            if (lookupTypeFrom.val() == 'Person') {
                                return {
                                    label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    record: item
                                }
                            } else if (lookupTypeFrom.val() == 'Company') {
                                return {
                                    label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                    value: item.name,
                                    record: item
                                }
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
            var memberType = lookupTypeFrom.val();
            if (ui.item.record.id > 0) {
                jQuery("#shareholder_contact_idFrom").val('');
                jQuery("#shareholder_company_idFrom").val('');
                jQuery(memberType == 'Person' ? "#shareholder_contact_idFrom" : "#shareholder_company_idFrom").val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                if (memberType == 'Person') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": container,
                        "lookupResultHandler": setContactDataAfterAutocompleteToSharesTransferFormFrom,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                    companyContactFormMatrix.companyDialog = {
                        "referalContainerId": container,
                        "lookupResultHandler": setCompanyDataAfterAutocompleteToSharesTransferFormFrom,
                        "lookupValue": ui.item.record.term
                    };
                    companyAddForm();
                }
            }
        }
    });
}
function setContactDataAfterAutocompleteToSharesTransferFormTo(record) {
    var name =(record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#shareholder_contact_idTo', '#shareMovementForm').val(record.id);
    jQuery('#memberNameTo', '#shareMovementForm').val(name);
}
function setCompanyDataAfterAutocompleteToSharesTransferFormTo(record, container) {
    var name = record.name;
    jQuery('#shareholder_company_idTo', container).val(record.id);
    jQuery('#memberNameTo', container).val(name);
}
function setContactDataAfterAutocompleteToSharesTransferFormFrom(record) {
    var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#shareholder_contact_idFrom', '#shareMovementForm').val(record.id);
    jQuery('#memberNameFrom', '#shareMovementForm').val(name);
}
function setCompanyDataAfterAutocompleteToSharesTransferFormFrom(record, container) {
    var name = record.name;
    jQuery('#shareholder_company_idFrom', container).val(record.id);
    jQuery('#memberNameFrom', container).val(name);
}
function resetShareMovementFormValues() {
    var shareMovementForm = jQuery("form#shareMovementForm", jQuery('#shareMovementDialog'));
    if (shareMovementForm.length > 0) {
        shareMovementForm.validationEngine('hide');
        shareMovementForm[0].reset();
    }
}
function deleteShareMovement(shareMovementId, isTrasnferMode, shareholderGrid = false) {
    if(shareholderGrid){
        jQuery.ajax({
            url: getBaseURL() + 'share_movements/check_transactions/' + shareMovementId,
            type: 'GET',
            dataType: 'JSON',
            beforeSend: function () {
            },
            success: function (response) {
                if(response.result){
                    _deleteShareMovement(shareMovementId, isTrasnferMode);
                }
                else {
                    var msg = _lang.feedback_messages.shareholderMultipleTransactions.sprintf(['<a target="_blank" href="' + getBaseURL() + 'reports/shares_by_date/' + response.data.company_id + '/' + response.data.member_id + '">' + _lang.companyShare + '</a>']);
                    pinesMessage({ty: 'warning', m: msg});
                }
            }
        });
    } else {
        _deleteShareMovement(shareMovementId, isTrasnferMode);
    }
}
function _deleteShareMovement(shareMovementId, isTrasnferMode){
    if (isTrasnferMode != '') {
        jQuery('#deleteTransferMessage').removeClass('d-none');
    } else {
        jQuery('#deleteTransferMessage').addClass('d-none');
    }
    var deleteContainer = jQuery('#shareMovementDeleteDialog');
    deleteContainer = jQuery('#shareMovementDeleteDialog');
    if (!deleteContainer.is(':data(dialog)')) {
        deleteContainer.dialog({
            autoOpen: true,
            buttons: [
                {
                    'class': 'btn btn-info',
                    click: function () {
                        // if (jQuery('#confirmActionDialog').is(':checked')) {
                        jQuery.ajax({
                            url: getBaseURL() + 'share_movements/purge_delete',
                            data: {id: shareMovementId},
                            type: 'POST',
                            dataType: 'JSON',
                            beforeSend: function () {
                            },
                            success: function (response) {
                                if (response.result == true) {
                                    window.location.href = window.location.href;
                                } else {
                                    pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
                                }
                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                        //}
                        jQuery(deleteContainer).dialog("close");
                    },
                    text: _lang.deleteRow
                },
                {
                    'class': 'btn btn-link',
                    click: function () {
                        resetShareMovementDeleteDialog();
                        jQuery(this).dialog("close");
                    },
                    text: _lang.cancel
                }
            ],
            close: function () {
                resetShareMovementDeleteDialog();
            },
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(that, '30%', '180');
                }));
                resizeNewDialogWindow(that, '30%', '180');
                resetShareMovementDeleteDialog();
            },
            resizable: true,
            draggable: true,
            modal: false,
            title: _lang.confirmationDelete
        });
    } else {
        deleteContainer.dialog("open");
        resetShareMovementDeleteDialog();
    }
} 
function resetShareMovementDeleteDialog() {
    jQuery("form#shareMovementDeleteDialog", jQuery('#shareMovementDeleteDialog'))[0].reset();
}
