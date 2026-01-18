jQuery(document).ready(function () {
    jQuery('.tooltip-title', '#assignments-container').tooltipster();
    jQuery('.select-picker', '#assignments-container').selectpicker();
    jQuery('.record', '#assignment-container').each(function () {
        radioButtonsEvents(this);
    });
    if (jQuery('.record', '#assignment-container').hasClass('d-none')) {
        jQuery('input', jQuery('.record', '#assignment-container')).attr('disabled', 'disabled');
        jQuery('select', jQuery('.record', '#assignment-container')).attr('disabled', 'disabled');
    }
});
function removeRow(id, elm, container) {
    id = id || false;
    if (id) {
        confirmationDialog('confirm_delete_record', {resultHandler: deleteRecord, parm: {id: id, element: jQuery(elm, container)}});
    } else {
        (jQuery(elm, container).parent().parent()).remove();
        if (typeof checkRowCount === "function") {
            checkRowCount();
        }
    }

}
function deleteRecord(params) {
    jQuery.ajax({
        url: getBaseURL() + 'assignments/delete',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': params.id,
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'information', m: _lang.deleteRecordSuccessfull});
                ((params.element).parent().parent()).remove();
                if (typeof checkRowCount === "function") {
                    checkRowCount();
                }
                return true;
            }
            pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
            return false;
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function addRow(container) {
    var rowCount = jQuery('tbody', container).attr('data-count-row');
    rowCount++;
    jQuery('.select-picker', jQuery('.default-row', container)).selectpicker('destroy');
    var clonedRow = jQuery('.default-row', container).clone();
    jQuery('select', clonedRow).removeAttr('disabled');
    jQuery('.select-picker', jQuery('.default-row', container)).selectpicker();
    jQuery('.select-picker', clonedRow).selectpicker();
    clonedRow.attr('class', 'record row-' + rowCount);
    jQuery('.remove-row', clonedRow).removeClass('d-none');
    var category = jQuery(clonedRow).attr('data-category-name');
    jQuery('.radio-button', clonedRow).attr('name', category + '[assignment_rule][' + rowCount + ']');
    jQuery("input[type=radio]", clonedRow).removeAttr('checked');
    jQuery('.id', clonedRow).attr('name', category + '[id][' + rowCount + ']').val('');
    jQuery('.type', clonedRow).removeClass('d-none').attr('name', category + '[type][' + rowCount + ']');
    jQuery('.assigned-user', clonedRow).attr('name', category + '[user_id][' + rowCount + ']');
    jQuery('.assignee-visibility', clonedRow).attr('name', category + '[visible_assignee][' + rowCount + ']').val(1);
    jQuery("input[type=checkbox]", jQuery('.assignee-container', clonedRow)).attr('onclick', '');
    jQuery("input[type=checkbox]", jQuery('.assignee-container', clonedRow)).on('click', function () {
        updateHiddenFields(this, jQuery('.assignee-visibility', clonedRow), '#' + category + '-assignment-container')
    });
    jQuery("input[type=checkbox]", clonedRow).attr('checked', 'checked');
    jQuery('.inline-error', clonedRow).addClass('d-none');
    jQuery('tbody', container).append(clonedRow);
    radioButtonsEvents(jQuery('.row-' + rowCount, container));
    jQuery('tbody', container).attr('data-count-row', rowCount);
    updateFields(clonedRow, category, rowCount);
}
function assignmentsSubmit(object) {
    var container = jQuery('#assignments-container');
    var formData = jQuery("form#assignments-form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-save', container).attr('disabled', 'disabled');
            jQuery('.inline-error', container).addClass('d-none');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'assignments/save/' + object,
        success: function (response) {
            if (typeof response.id !== 'undefined') {
                jQuery.each(response.id, function (category, val) {
                    jQuery.each(val, function (index, id) {
                        var rowContainer = jQuery('.row-' + index, '#' + category + '-assignment-container');
                        jQuery('.id', rowContainer).val(id);

                    });
                });
            }
            if (typeof response.validation_errors !== 'undefined') {
                jQuery.each(response.validation_errors, function (category, val) {
                    jQuery.each(val, function (index, errors) {
                        displayValidationErrors(errors, jQuery('.row-' + index, '#' + category + '-assignment-container'));
                    });
                });
            } else {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            }
        }, complete: function () {
            jQuery('.btn-save', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function radioButtonsEvents(container) {
    jQuery('input[type=radio]', container).on('change', function () {
        if (this.value == 'specific_user') {
            jQuery('select', jQuery('.specific-user-lookup', container)).removeAttr('disabled');
            jQuery('.select-picker', jQuery('.specific-user-lookup', container)).selectpicker("refresh");
            jQuery('.specific-user-lookup', container).removeClass('d-none');
            jQuery('.assignee-visibility-checkbox', container).removeAttr('disabled').removeClass('disabled');
        } else {
            jQuery('select', jQuery('.specific-user-lookup', container)).attr('disabled', 'disabled');
            jQuery('.specific-user-lookup', container).addClass('d-none');
            if(this.value == 'rr_algorithm'){
                jQuery('.assignee-visibility', container).val(0);
                jQuery('.assignee-visibility-checkbox', container).prop("checked", false).attr('disabled', 'disabled');
            } else if(this.value == 'no'){
                jQuery('.assignee-visibility-checkbox', container).removeAttr('disabled').removeClass('disabled');
            }
        }
    });
}
function updateFields(clonedContainer) {
    jQuery("input[type=radio][value='specific_user']", clonedContainer).attr('checked', 'checked');
    jQuery('input', clonedContainer).removeAttr('disabled');
}
function hideAssignee(container) {
    jQuery('.assignee-visibility', container).val(0);
    jQuery('.assignee-visibility-checkbox', container).prop("checked", false).attr('disabled', 'disabled');
}
//check if there is no more row visible => then show the default row
function checkRowCount() {
    if (jQuery('tr.record', '#assignment-container').length === 1 && jQuery('tr.record', '#assignment-container').hasClass('d-none')) {
        jQuery('tr.record', '#assignment-container').removeClass('d-none');
        jQuery('input', jQuery('.default-row', '#assignment-container')).removeAttr('disabled');
        jQuery('select', jQuery('.default-row', '#assignment-container')).removeAttr('disabled');
    }
}