var container;
var availableUsers, availableUserGroups, operators, fieldsDetails;
jQuery(document).ready(function () {
    container = jQuery('.process-container', '#approval-center-container');
    initializeSelectPermissions(container);
    if (jQuery('#approval-id', container).val() == ''){
        lookupApproversSignees(jQuery('#lookup-approvers1', container), 'approval[approvers][1][approvers][]', 'approval[approvers][1][approvers_types][]', '#selected-approvers1', 'approvers-container1', container, '', 'approval', true);
    }
    jQuery('.criteria-section .select-picker', container).selectpicker();
    loadCustomFieldsEvents('criteria-field-', container);
    jQuery('.tooltip-title', container).tooltipster();
});

function getFieldDetails(that, count) {
    var value = jQuery(that).val();
    var container = jQuery('.criteria-row-' + count, '#criteria-section');
    html = '<option></option>';
    if (value == '') {
        jQuery('select.operator', container).html(html).selectpicker('refresh');
        jQuery('.value-container', container).html('<div data-field="value" class="inline-error d-none"></div>');
    } else {
        html = '';
        var options = operators[fieldsDetails[value]['field_data_type']];
        if (options) {
            for (opt in options) {
                html += '<option value="' + opt + '">' + options[opt] + '</option>';
            }
        }
        jQuery('select.operator', container).html(html).selectpicker('refresh');
        jQuery('.value-container', container).html(fieldsDetails[value]['html']).append(' <div data-field="value" class="inline-error d-none"></div>');
        setTimeout(function () {
            var name = 'approval[criteria][' + count + '][value][]';
            jQuery('.criteria-field', container).attr('name', name);
            loadCustomFieldsEvents('criteria-field-', container);
        }, 100);

    }

}

function addCriteriaRow(container) {
    var rowCount = jQuery('tbody', container).attr('data-count-row');
    rowCount++;
    var clonedRow = jQuery('#default-criteria tr').clone();
    jQuery('select', clonedRow).selectpicker();
    clonedRow.attr('class', 'criteria-row-' + rowCount);
    jQuery('.remove-row', clonedRow).removeClass('d-none').attr('onclick', 'removeRow(this,  \'' + rowCount + '\', \'criteria\')');
    jQuery('.id', clonedRow).attr('name', 'approval[criteria][' + rowCount + '][id]').val('');
    jQuery('select.field', clonedRow).attr('name', 'approval[criteria][' + rowCount + '][field]');
    jQuery('select.field', clonedRow).attr('onChange', 'getFieldDetails(this, \'' + rowCount + '\')');
    jQuery('select.operator', clonedRow).html('<option></option>').selectpicker('refresh').attr('name', 'approval[criteria][' + rowCount + '][operator]');
    jQuery('.value-container', clonedRow).html('<div data-field="value" class="inline-error d-none"></div>');
    jQuery('.inline-error', clonedRow).addClass('d-none');
    jQuery('tbody', container).append(clonedRow);
    jQuery('tbody', container).attr('data-count-row', rowCount);
}

function addApproverRow(container) {
    var rowCount = jQuery('tbody', container).attr('data-count-row');
    rowCount++;
    var clonedRow = jQuery('#default-approvers tr').clone();
    clonedRow.attr('class', 'approvers-row-' + rowCount);
    jQuery('.remove-row', clonedRow).removeClass('d-none').attr('onclick', 'removeRow(this,  \'' + rowCount + '\', \'approvers\')');
    jQuery('select.user-groups-selectized', clonedRow).attr('name', 'approval[approvers][' + rowCount + '][user_groups][]');
    jQuery('.approvers-label input', clonedRow).attr('name', 'approval[approvers][' + rowCount + '][label]');
    jQuery('.approvers-order input', clonedRow).attr('name', 'approval[approvers][' + rowCount + '][rank]');
    jQuery('input.board-member-checkbox', clonedRow).attr('id', 'bm-'+rowCount);
    jQuery('label.label-selected', jQuery('.approvers-board-member', clonedRow)).attr('for', 'bm-'+rowCount);
    jQuery('input.board-member-checkbox', clonedRow).attr('onclick', 'boardDirectorRolesForm(this, \'#is-board-member\', jQuery(".approvers-row-' + rowCount + '", "#approvers-section"), \'approval_center\');');
    jQuery('input#is-board-member', clonedRow).attr('name', 'approval[approvers][' + rowCount + '][is_board_member]');
    jQuery('input#board-member-role', clonedRow).attr('name', 'approval[approvers][' + rowCount + '][board_member_role]');
    jQuery('input.shareholder-checkbox', clonedRow).attr('id', 'sh-'+rowCount);
    jQuery('label.label-selected', jQuery('.approvers-shareholder', clonedRow)).attr('for', 'sh-'+rowCount);
    jQuery('input.shareholder-checkbox', clonedRow).attr('onclick', 'toggleCheckbox(this, \'#is-shareholder\', jQuery(".approvers-row-' + rowCount + '", "#approvers-section"));');
    jQuery('input#is-shareholder', clonedRow).attr('name', 'approval[approvers][' + rowCount + '][is_shareholder]');
    jQuery('input.requester-manager-checkbox', clonedRow).attr('id', 'rm-'+rowCount);
    jQuery('label.label-selected', jQuery('.approvers-requester-manager', clonedRow)).attr('for', 'rm-'+rowCount);
    jQuery('input.requester-manager-checkbox', clonedRow).attr('onclick', 'toggleCheckbox(this, \'#is-requester-manager\', jQuery(".approvers-row-' + rowCount + '", "#approvers-section"));');
    jQuery('input#is-requester-manager', clonedRow).attr('name', 'approval[approvers][' + rowCount + '][is_requester_manager]');
    initializeSelectPermissions(clonedRow);
    jQuery('.approvers-container', clonedRow).attr('id', 'approvers-container' + rowCount);
    jQuery('.selected-approvers', clonedRow).attr('id', 'selected-approvers' + rowCount);
    jQuery('.lookup-approvers', clonedRow).attr('id', 'lookup-approvers' + rowCount);
    lookupApproversSignees(jQuery('#lookup-approvers' + rowCount, clonedRow), 'approval[approvers][' + rowCount + '][approvers][]', 'approval[approvers][' + rowCount + '][approvers_types][]', '#selected-approvers' + rowCount, 'approvers-container' + rowCount, jQuery(container), '', 'approval', true);
    jQuery('.inline-error', clonedRow).addClass('d-none');
    jQuery('tbody', container).append(clonedRow);
    jQuery('tbody', container).attr('data-count-row', rowCount);
}

function deleteRecord(params) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'approval_center/delete_details',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': params.id,
            'object': params.object,
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'information', m: _lang.deleteRecordSuccessfull});
                ((params.element).parent().parent().parent()).remove();
                var count = jQuery('tbody', '#' + params.object + '-section').attr('data-count-row');
                jQuery('tbody', '#' + params.object + '-section').attr('data-count-row', count - 1);

                return true;
            }
            pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
            return false;
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function approvalCenterSubmit(id) {
    id = id || false;
    var formData = jQuery('#approval-center-form', '#approval-center-container').serializeArray();
    jQuery.ajax({
        url: getBaseURL('contract') + 'approval_center/' + (id ? 'edit' : 'add'),
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('#form-submit', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                setTimeout(function () {
                    window.location = getBaseURL('contract') + 'approval_center';
                }, 200);
            } else {
                if (typeof response.validation_errors !== 'undefined') {
                    jQuery.each(response.validation_errors, function (category, val) {
                        jQuery.each(val, function (rowId, errors) {
                            displayValidationErrors(errors, jQuery('.' + category + '-row-' + rowId, '#' + category + '-section'));
                        });
                    });
                }
            }
        }, complete: function () {
            jQuery('#form-submit', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
    });
}

