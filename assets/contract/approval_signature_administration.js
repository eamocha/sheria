function deleteApproval(params) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'approval_center/delete',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': params.id
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'information', m: _lang.deleteRecordSuccessfull});
                (jQuery('#item-'+params.id, '.administration-container')).remove();

            }else{
                pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
                return false;
            }

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteSignature(params) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'signature_center/delete',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': params.id
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'information', m: _lang.deleteRecordSuccessfull});
                (jQuery('#item-'+params.id, '.administration-container')).remove();

            }else{
                pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
                return false;
            }

        },
        error: defaultAjaxJSONErrorsHandler
    });
}


function removeRow(elm, rowCount, object, id) {
    var container = jQuery('.' + object + '-row-' + rowCount, '#' + object + '-section');
    id = id || false;
    var count = jQuery('tbody', '#' + object + '-section').attr('data-count-row');
    if (count > 1) {
        if (id) {
            confirmationDialog('confirm_delete_record', {
                resultHandler: deleteRecord,
                parm: {id: id, object: object, element: jQuery(elm, container)}
            });
        } else {
            (jQuery(elm, container).parent().parent().parent()).remove();
            jQuery('tbody', '#' + object + '-section').attr('data-count-row', count - 1);
        }
    } else {
        pinesMessage({ty: 'error', m: _lang.feedback_messages.deleteRowFailed});

    }
}
function boardDirectorRolesForm(that, checkBoxInput, container, controller, id){
    id = id || false;
    var isChecked = jQuery(that, container).is(':checked');
    jQuery(checkBoxInput, container).val(isChecked ? '1' : '0');
    if(isChecked){
        jQuery.ajax({
            url: getBaseURL('contract') + controller+'/board_members_roles_form',
            dataType: 'JSON',
            type: 'GET',
            data: id? {'assignee_id': id} : false,
            success: function (response) {
                if (response.result) {
                    if (response.html) {
                        if (jQuery('#bm-roles-container').length <= 0) {
                            jQuery('<div id="bm-roles-container" class="primary-style"></div>').appendTo("body");
                            var rolesContainer = jQuery('#bm-roles-container');
                            rolesContainer.html(response.html);
                            jQuery('.select-picker', rolesContainer).selectpicker();
                            initializeModalSize(rolesContainer);
                            jQuery(".modal", rolesContainer).modal({
                                keyboard: false,
                                backdrop: "static",
                                show: true
                            });
                            jQuery('.close-option',rolesContainer).on("click", function() {
                                jQuery(".modal", rolesContainer).modal("hide");
                                if(!id){
                                    jQuery(that, container).click();
                                }
                            });
                            jQuery('.modal-body',rolesContainer).on("scroll", function() {
                                jQuery('.bootstrap-select.open').removeClass('open');
                            });
                            jQuery(document).keyup(function (e) {
                                if (e.keyCode == 27) {
                                    jQuery(".modal", rolesContainer).modal("hide");
                                }
                            });
                            jQuery('.modal', rolesContainer).on('hidden.bs.modal', function () {
                                destroyModal(rolesContainer);
                            });
                            jQuery("#form-submit", rolesContainer).click(function () {
                                boardDirectorRolesSubmitForm(rolesContainer, container, controller, id);
                            });
                            jQuery(rolesContainer).find('input').keypress(function (e) {
                                // Enter pressed?
                                if (e.which == 13) {
                                    e.preventDefault();
                                    boardDirectorRolesSubmitForm(rolesContainer, container, controller, id);
                                }
                            });
                        }
                    }
                }
            }, error: defaultAjaxJSONErrorsHandler
        });
    }else{
        jQuery('.toggle-label span', container).html('');
        jQuery('.toggle-label img', container).remove();
    }

}

function boardDirectorRolesSubmitForm(dialogContainer, rowContainer, controller, id){
    var val = jQuery('#bm-role-list', dialogContainer).val();
    if(id){
        id = id || false;
        jQuery.ajax({
            url: getBaseURL('contract') + controller+'/save_board_member_role',
            dataType: 'JSON',
            type: 'POST',
            data: {'assignee_id': id, 'role_id': val},
            success: function (response) {
                if (response.result) {
                    boardDirectorRolesEvents(val, rowContainer, dialogContainer);
                }
            }, error: defaultAjaxJSONErrorsHandler
        });
    }else{
        boardDirectorRolesEvents(val, rowContainer, dialogContainer);
    }

}
function boardDirectorRolesEvents(val, rowContainer, dialogContainer){
    jQuery(".modal").modal("hide");
    jQuery('#board-member-role', rowContainer).val(val);
    jQuery('.toggle-label span', rowContainer).html( val ? jQuery('[data-id="bm-role-list"]').attr('title') : _lang.notSelected);
}
function toggleCheckbox(that, checkBoxInput, container) {
    jQuery(checkBoxInput, container).val(jQuery(that, container).is(':checked') ? '1' : '0');
}